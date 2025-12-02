<?php

class FileUploader
{
    private string $uploadDir;
    private array $allowedMimes;
    private int $maxFiles;
    private int $maxFileSizeMB;
    private int $maxTotalSizeMB;

    public function __construct(
        string $uploadDir,
        array $allowedMimes = ['image/jpeg', 'image/png'],
        int $maxFiles = 10,
        int $maxFileSizeMB = 5,
        int $maxTotalSizeMB = 50
    ) {
        $this->uploadDir = $uploadDir;
        $this->allowedMimes = $allowedMimes;
        $this->maxFiles = $maxFiles;
        $this->maxFileSizeMB = $maxFileSizeMB;
        $this->maxTotalSizeMB = $maxTotalSizeMB;

        if (!is_dir($uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory: " . $this->uploadDir);
            }
        }

        if (!is_writable($uploadDir)) {
            throw new Exception("Upload directory is not writable: {$this->uploadDir}");
        }
    }

    public function upload(array $files, string $prefix): array
    {
        $filenames = $files['name'] ?? [];
        if (!is_array($filenames) || empty(array_filter($filenames))) {
            return [];
        }

        $count = count($filenames);
        if ($count > $this->maxFiles) {
            throw new Exception("Too many files. Maximum {$this->maxFiles} photos allowed.");
        }

        $this->validate_files($files, $count);

        $savedFiles = [];
        try {
            for ($i = 0; $i < $count; $i++) {
                $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $savedFiles[] = $this->processFile($files, $i, $prefix);
            }

            return $savedFiles;
        } catch (Exception $error) {
            $this->cleanup($savedFiles);
            throw $error;
        }
    }

    private function processFile(array $files, int $index, string $prefix): array
    {
        $name = basename($files['name'][$index] ?? 'Unknown File');
        $temp = $files['tmp_name'][$index] ?? null;

        if (!is_uploaded_file($temp)) {
            throw new Exception("Security violation: {$name} is not a valid upload");
        }

        $mime = $this->detectMime($temp);
        $ext = $this->getExtension($mime, $name);
        $savedFileName = $this->generateFileName($name, $prefix, $ext);
        $destPath = $this->uploadDir . DIRECTORY_SEPARATOR . $savedFileName;

        $this->saveFile($temp, $destPath, $mime, $name);

        return [
            'name' => $name,
            'saved' => $savedFileName,
            'path' => $destPath,
            'mime' => $mime,
            'ext' => $ext,
        ];
    }

    private function saveFile(string $source, string $dest, string $mime, string $name): void
    {
        $size = filesize($source);

        // Files <= 5MB or no GD, just move
        if ($size <= 5 * 1024 * 1024 || !extension_loaded('gd')) {
            if (!move_uploaded_file($source, $dest)) {
                throw new Exception("Failed to save: {$name}");
            }
            return;
        }

        $img = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            default => null
        };

        if (!$img) {
            throw new Exception("Failed to create image: " . $name);
        }

        // Save in original format with compression
        // JPEG at 85% quality
        // PNG at level 6
        $res = match ($mime) {
            'image/jpeg' => imagejpeg($img, $dest, 85),
            'image/png'  => imagepng($img, $dest, 6), // compression level 0-9
            default      => false,
        };
        unset($img);

        if (!$res) {
            throw new Exception("Failed to save compressed image: " . $name);
        }
    }

    private function validate_files(array $files, int $count): void
    {
        $totalSize = 0;
        $maxFileSizeBytes = $this->maxFileSizeMB * 1024 * 1024;
        $maxTotalSizeBytes = $this->maxTotalSizeMB * 1024 * 1024;

        for ($i = 0; $i < $count; $i++) {
            $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;

            // Skip empty files
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $name = basename($files['name'][$i] ?? 'Unknown File');

            if ($error !== UPLOAD_ERR_OK) {
                $msgMap = [
                    UPLOAD_ERR_INI_SIZE   => 'exceeds php.ini upload_max_filesize',
                    UPLOAD_ERR_FORM_SIZE  => 'exceeds form MAX_FILE_SIZE',
                    UPLOAD_ERR_PARTIAL    => 'was only partially uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'missing temporary folder on server',
                    UPLOAD_ERR_CANT_WRITE => 'failed to write to disk',
                    UPLOAD_ERR_EXTENSION  => 'blocked by a PHP extension',
                ];
                $reason = $msgMap[$error] ?? 'unknown upload error';
                throw new Exception("Upload failed for {$name}: {$reason}");
            }

            $temp = $files['tmp_name'][$i] ?? null;
            if (!file_exists($temp)) {
                throw new Exception("Uploaded file missing: {$name}");
            }

            $size = filesize($temp);
            if ($size > $maxFileSizeBytes) {
                throw new Exception("File too large ({$this->maxFileSizeMB}MB max): {$name}");
            }

            $mime = $this->detectMime($temp);
            if (!in_array($mime, $this->allowedMimes, true)) {
                throw new Exception("Unsupported image type: " . $name);
            }

            $totalSize += $size;
            if ($totalSize > $maxTotalSizeBytes) {
                throw new Exception("Total upload size exceeds {$this->maxTotalSizeMB}MB limit. Please reduce file count or sizes.");
            }
        }
    }

    private function detectMime(string $path): string
    {
        $mime = strtolower(mime_content_type($path) ?: '');

        // Normalize MIME type aliases
        return match ($mime) {
            'image/jpg', 'image/pjpeg' => 'image/jpeg',
            'image/x-png' => 'image/png',
            default => $mime
        };
    }

    private function getExtension(string $mime, string $name): string
    {
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => null
        };

        if (!$ext) {
            throw new Exception("Unsupported file type: {$name}");
        }

        return $ext;
    }

    private function generateFileName(string $name, string $prefix, string $ext): string
    {
        $base = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($name, PATHINFO_FILENAME));
        $hash = substr(hash('sha256', $name . microtime(true) . random_bytes(8)), 0, 12);
        return "{$prefix}_" . date('Y-m-d') . "_{$base}_{$hash}.{$ext}";
    }

    private function cleanup(array $savedFiles): void
    {
        foreach ($savedFiles as $file) {
            if (isset($file['path']) && is_file($file['path'])) {
                @unlink($file['path']);
            }
        }
    }
}

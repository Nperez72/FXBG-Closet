<?php
session_cache_expire(30);
session_start();

$loggedin = false;
$accesslevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

// admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}
if (!$loggedIn) {
    header('Location: login.php');
    die();
}

$target_dir = 'uploads/';

// List PDF files in /uploads
function listPDFFiles($dir) {
    $pdfFiles = array();
    if (is_dir($dir)) {
        if ($open_dir = opendir($dir)) {
            while (($file = readdir($open_dir)) !== false) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
                    $pdfFiles[] = $file;
                }
            }
            closedir($open_dir);
        }
    }
    return $pdfFiles;
}

$pdfFiles = listPDFFiles($target_dir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Volunteer Documents</title>
  <link href="css/normal_tw.css" rel="stylesheet">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<style>
        .date-box {
            background: #274471;
            padding: 7px 30px;
            border-radius: 50px;
            box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset;
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }   
        .dropdown {
            padding-right: 50px;
        }   

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->
</head>
<body>

    <!-- Hero Section with Title -->
    <header class="hero-header">
        <div class="center-header">
            <h1>Manage Volunteer Documents</h1>
        </div>
    </header>

    <main>
        <div class="main-content-box w-[80%] overflow-hidden">

            <!-- Document Table -->
<table>
    <tbody>
        <?php if ($pdfFiles): ?>
            <?php foreach ($pdfFiles as $pdf): ?>
                <tr>
                    <td>
                        <a href="<?php echo $target_dir . $pdf; ?>" target="_blank" class="text-blue-700 hover:underline">
                            <?php echo htmlspecialchars($pdf); ?>
                        </a>
                    </td>
                    <td class="text-right">
                        <a href="deleteResources.php?file=<?php echo urlencode($pdf); ?>"
                           class="delete-button"
                           onclick="return confirm('Are you sure you want to delete <?php echo addslashes($pdf); ?>?')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td class="py-6 text-gray-500" colspan="2">No Documents Found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

        </div>

        <!-- Upload Form -->
        <form action="uploadResources.php" method="post" enctype="multipart/form-data" class="mt-10 w-[80%] bg-white p-6 rounded-xl border-2 border-gray-300 shadow-md">
            <label for="fileToUpload" class="font-medium text-center">Select PDF to Upload:</label>
            <input type="file" name="fileToUpload" id="fileToUpload" accept="application/pdf" class="block mx-auto mb-4 border border-gray-300 p-2 rounded-md w-full">

            <div class="flex justify-center space-x-4 mt-4">
                <input type="submit" value="Upload PDF" name="submit" class="blue-button">
            </div>
        </form>

        <!-- Return Button -->
        <div class="mt-6">
            <a href="index.php" class="return-button">Return to Dashboard</a>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="blue-div"></div>
            <p class="info-text">
                Welcome to the volunteer document hub. Upload, review, and manage volunteer documents from here.
            </p>
        </div>
    </main>
</body>
</html>


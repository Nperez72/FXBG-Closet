<?php

/**
*
*  FUNCTIONS USED FOR SENDING EMAILS
*  WILL NOT FUNCTION XAMPP ONLY ON SITEGROUND
*  getEmailByType() + getAllEmails()  sendEmails() are helper functions
*  but are left non-private to allow for using them if needed/being lazy
*  emailing should only be done through the email***() functions
*  sending emails is done through the php function mail()
*
**/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

/**
 * Fetch admin emails from dbaccounts (type >= 2).
 * dbaccounts: username, email (nullable for non-admin), password, type
 */
function getAdminAccountEmails(): array
{
    include_once('database/dbinfo.php');
    $conn = connect();
    $stmt = $conn->prepare("SELECT email FROM dbaccounts WHERE type >= 2 AND email IS NOT NULL AND email <> ''");
    $stmt->execute();
    $res = $stmt->get_result();
    $emails = [];
    while ($row = $res->fetch_assoc()) {
        $email = trim($row['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emails[] = $email;
        }
    }
    $stmt->close();
    $conn->close();
    return array_values(array_unique($emails));
}


/**
 *  EACH EMAIL TYPE IS A SEPARATE FUNCTION TO REQUIRE 1 LESS PARAM AND INCREASE READABILITY
 **/
function emailAdmins(string $fromUser, string $subject, string $body): array
{
    $list = getAdminAccountEmails();
    return sendEmails($list, $fromUser, $subject, $body);
}


/**
 * Send emails to each address in the supplied list.
 *
 * @param array  $emails   List of recipient email addresses.
 * @param string $fromUser Local-part for the From address.
 * @param string $subject  Email subject.
 * @param string $body     Email body.
 * @param array  $attachments Optional array of file paths to attach.
 * @return array Returns ['success' => bool, 'sent_count' => int, 'total_count' => int, 'error' => string|null]
 */
function sendEmails(array $emails, string $fromUser, string $subject, string $body, array $attachments = []): array
{
    $host = '127.0.0.1';
    $port = 1025;
    $fromEmail = 'localhost@example.com';

    // For Siteground later
    // $username = 'sitegroundemail@example.com';
    // $password = 'emailpassword';
    // $fromEmail = 'sitegroundemail@example.com';

    // Validate attachments
    if (!empty($attachments)) {
        foreach ($attachments as $filePath) {
            $fileName = basename($filePath);
            if (!file_exists($filePath)) {
                return [
                    'success' => false,
                    'sent_count' => 0,
                    'total_count' => count($emails),
                    'error' => "Attachment not found: {$fileName}",
                ];
            }
            if (!is_readable($filePath)) {
                return [
                    'success' => false,
                    'sent_count' => 0,
                    'total_count' => count($emails),
                    'error' => "Attachment not readable: {$fileName}",
                ];
            }
        }
    }

    $sentCount = 0;
    $failures = [];

    foreach ($emails as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $failures[] = "Invalid email: {$email}";
        }

        $mail = new PHPMailer(true);
        try {
            // Settings
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->SMTPAuth = false;

            // SiteGround later:
            // $mail->SMTPAuth = true;
            // $mail->Username = $username;
            // $mail->Password = $password;
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Recipients
            $mail->setFrom($fromEmail, $fromUser);
            $mail->addAddress($email);

            // Attachments
            foreach ($attachments as $filePath) {
                $mail->addAttachment($filePath);
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            $sentCount++;
        } catch (Exception $e) {
            $failures[] = "Failed to send to {$email}: {$mail->ErrorInfo}";
        }
    }

    return [
        'success' => $sentCount > 0,
        'sent_count' => $sentCount,
        'total_count' => count($emails),
        'error' => !empty($failures) ? implode('; ', $failures) : null,
    ];
}


/**
 * Render a PHP template file with variables and return as string.
 *
 * @param string $path Absolute path to template file
 * @param array  $vars Variables to extract for use inside template
 * @return string Rendered HTML
 */
function render_email_template(string $path, array $vars = []): string
{
    if (!file_exists($path)) {
        throw new InvalidArgumentException("Template file not found: $path");
    }

    extract($vars, EXTR_SKIP);

    ob_start();
    include $path;
    return ob_get_clean();
}

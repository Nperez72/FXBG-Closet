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
 *  ------->MAY NEED FIELDS CHANGED BEFORE REAL PRODUCTION <-----------
 *   -------> DOMAIN WILL ALMOST CERTIANLY CHANGE IN PRODUCTION <------------
 *
 * @param array  $emails   List of recipient email addresses.
 * @param string $fromUser Local-part for the From address.
 * @param string $subject  Email subject.
 * @param string $body     Email body.
 * @param array  $attachments Optional array of file paths to attach.
 * @return array           Returns an  array where keys are emails and values are boolean statuses.
 */
function sendEmails(array $emails, string $fromUser, string $subject, string $body, array $attachments = []): array
{
    $domain = 'localhost';
    $fromAddress = "{$fromUser}@{$domain}";

    // Validate attachments
    if (!empty($attachments)) {
        foreach ($attachments as $filePath) {
            $fileName = basename($filePath);
            if (!file_exists($filePath)) {
                return ['error' => "Attachment file does not exist: {$fileName}"];
            }
            if (!is_readable($filePath)) {
                return ['error' => "Attachment file is not readable: {$fileName}"];
            }
        }
    }

    $results = [];

    // Generate a unique boundary string
    $boundary = md5(time());

    $headers = "From: {$fromAddress}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";

    if (empty($attachments)) {
        // No attachments: use text/plain format
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message = $body;
    } else {
        // Email has attachments: use multipart/mixed format
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $body . "\r\n\r\n";

        foreach ($attachments as $filePath) {
            $fileName = basename($filePath);
            $fileContent = file_get_contents($filePath);
            $encodedContent = chunk_split(base64_encode($fileContent));
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

            $message .= "--{$boundary}\r\n"; // Start new part
            $message .= "Content-Type: {$mimeType}; name=\"{$fileName}\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n";
            $message .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n\r\n";
            $message .= $encodedContent . "\r\n";
        }
        $message .= "--{$boundary}--";
    }

    foreach ($emails as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $results[$email] = mail($email, $subject, $message, $headers);
        } else {
            $results[$email] = false;
        }
    }
    return $results;
}

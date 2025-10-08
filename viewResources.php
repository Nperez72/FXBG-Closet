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

    if (!$loggedIn) {
        header('Location: login.php');
        die();
    }

    $resource_dir = './uploads';

    // List PDF files in /uploads
    function listPDFFiles($dir) {
        $pdfFiles = array();
        if (is_dir($dir)) {

            if ($open_dir = opendir($dir)) { // open uploads directory

                while (($file = readdir($open_dir)) !== false) {

                    if (pathinfo($file, PATHINFO_EXTENSION) == 'pdf') { // if file is a PDF
                        $pdfFiles[] = $file;
                    }
                }
                closedir($open_dir);
            }
        }
    return $pdfFiles;
    }

$pdfFiles = listPDFFiles($resource_dir);
?>

<!DOCTYPE html>
    <html>
    <head>
        <?php require_once('universal.inc') ?>
        <link rel="stylesheet" href="css/messages.css"></link>
        <script src="js/messages.js"></script>
        <title>View PDF</title>
        <style>
             /* class="general" style="width: 80%; margin-left: auto; margin-right: auto; border-collapse: collapse" */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-left: auto;
                margin-right: auto;
                font-family: Arial, sans-serif;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f4f4f4;
                border-bottom: 1px solid #ddd;
            }
            tr:nth-child(even) {
                background-color: #ece8e8;
            }
        </style>
    </head>
    <body>
        <h1>List of PDF Files</h1>
        </main class="dashboard">
            <?php require_once('header.php') ?>
            <div class="table-wrapper">
            <table class="general" style="width: 80%; margin-left: auto; margin-right: auto; border-collapse: collapse">
                    <thead>
                        <tr>
                            <th>File</th>
                        </tr>
                    </thead>
                    <!-- display each pdf as a row in the table -->
                    <tbody class="standout">
                        <?php foreach ($pdfFiles as $pdf): ?>
                            <tr>
                                <td><a href="<?php echo $resource_dir . '/' . $pdf; ?>" target="_blank"><?php echo $pdf; ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <p><br></p>
            <div id="calendar-footer">
                <center>
                <a class="button cancel" href="index.php" style="width: 80%">Return to Dashboard</a>
                </center>
            </div>
        </main>
    </body>    
    </html>

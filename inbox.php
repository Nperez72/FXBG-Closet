<?php
session_cache_expire(30);
session_start();

$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <link rel="stylesheet" href="css/messages.css">
    <script>
        function toggleBulkActions() {
            const checkboxes = document.querySelectorAll('.messageCheckbox');
            const bulkBar = document.getElementById('bulk-actions');
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            bulkBar.style.display = anyChecked ? 'flex' : 'none';
        }

        function toggleSelectAll(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.messageCheckbox');
            checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
            toggleBulkActions();
        }

        function confirmAndSubmit(formId, msg) {
            if (confirm(msg)) {
                document.getElementById(formId).submit();
            }
        }
    </script>
    <style>
        #bulk-actions {
            display: none;
            justify-content: flex-end;
            align-items: center;

        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    <title>Inbox</title>
</head>
<body>
<?php require_once('header.php') ?>
<h1>Inbox</h1>
<main class="general">
    <?php
    require_once('database/dbMessages.php');
    require_once('database/dbPersons.php');
    require_once('include/output.php');

    $newMessages = get_user_unread_messages($userID);
    $oldMessages = get_user_read_messages($userID);
    $allMessages = array_merge($newMessages, $oldMessages);

    usort($allMessages, function($a, $b) {
        return strtotime(str_replace('-', ' ', $b['time'])) - strtotime(str_replace('-', ' ', $a['time']));
    });

    mark_all_as_read($userID);
    ?>
    <?php if (count($allMessages) > 0): ?>
        <form id="bulkDeleteForm" action="deleteNotification.php" method="POST">
            <div class="top-bar">
            <button type="submit" name="delete_all" class="button delete" style="width:10%; margin-bottom: 10px;" onclick="return confirm('Are you sure you want to delete ALL notifications?');">Delete All</button>
                <div id="bulk-actions" style="display:none;">
                    <span><strong>With Selected:</strong></span>
                    <button type="submit" name="bulk_delete" class="button delete" style="margin-bottom: 10px;" onclick="return confirm('Delete selected notifications?');">Delete</button>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="general">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>From</th>
                            <th>Title</th>
                            <th>Received</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody class="standout">
                        <?php 
                            $id_to_name_hash = [];
                            foreach ($allMessages as $message):
                                $sender = $id_to_name_hash[$message['senderID']] ?? get_name_from_id($message['senderID']);
                                $id_to_name_hash[$message['senderID']] = $sender;

                                $messageID = $message['id'];
                                $title = $message['title'];
                                $timePacked = $message['time'];
                                [$year, $month, $day, $clock] = explode('-', $timePacked);
                                $time = time24hto12h($clock);
                                $class = 'message';
                                if (!$message['wasRead']) $class .= ' unread';
                                if ($message['prioritylevel']) $class .= ' prio' . $message['prioritylevel'];
                        ?>
                        <tr class="<?= $class ?>" data-message-id="<?= $messageID ?>">
                            <td><input type="checkbox" class="rowCheckbox" name="selected_messages[]" value="<?= $messageID ?>"></td>
                            <td><?= $sender ?></td>
                            <td><?= $title ?></td>
                            <td><?= "$month/$day/$year $time" ?></td>
                            <td>
                                <a class="button delete" 
                                href="deleteNotification.php?id=<?= $messageID ?>" 
                                onclick="return confirm('Are you sure you want to delete this message?');">
                                Delete Notification
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
        <?php else: ?>
            <p class="no-messages standout">You currently have no notifications.</p>
        <?php endif; ?>

        <a class="button cancel" href="index.php">Return to Dashboard</a>

        <script>
            document.getElementById('selectAll').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.rowCheckbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkActions();
            });

            document.querySelectorAll('.rowCheckbox').forEach(cb => {
                cb.addEventListener('change', toggleBulkActions);
            });

            function toggleBulkActions() {
                const anyChecked = [...document.querySelectorAll('.rowCheckbox')].some(cb => cb.checked);
                document.getElementById('bulk-actions').style.display = anyChecked ? 'block' : 'none';
            }
        </script>
</main>
</body>
</html>

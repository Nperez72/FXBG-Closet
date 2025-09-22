<?php
session_start();
include 'database/dbShifts.php';

if (!isset($_SESSION['_id'])) {
    die("Error: User not logged in.");
}

$person_id = $_POST['user_id'];
$today = date("Y-m-d");
$currentTime = date("H:i:s");

// Function to display the success message with an animated checkmark
function showSuccessMessage($message, $redirectUrl = null, $shift_id=null) {
    $content = isset($shift_id) ? get_shift_hours($shift_id) : "✔";
    echo '<html><head><style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            font-family: Quicksand, sans-serif;
        }
        .success-container {
            text-align: center;
        }
        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: popIn 0.5s ease-in-out;
        }
        .checkmark:after {
	    content: "' . htmlspecialchars($content) . '";
            color: white;
            font-size: 40px;
            font-weight: bold;
        }
        .message {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
	    justify-content: center;
            opacity: 0;
            animation: fadeIn 0.8s forwards 0.5s;
        }
        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style></head><body>';

    echo '<div class="success-container">
            <div class="checkmark"></div>
            <div class="message">' . $message . '</div>
          </div>';

    if ($redirectUrl) {
        echo '<script>
            setTimeout(function() {
                window.location.href = "' . $redirectUrl . '";
            }, 3000); // 3 seconds
        </script>';
    }

    echo '</body></html>';
    exit;
}

// Check if the user has an active shift (checked in but not checked out)
$existingShift = get_open_shift($person_id, $today);

if ($existingShift) {
    // If no description is submitted, show the prompt
    if (!isset($_POST['desc'])) {
        ?>
        <html>
        <head>
            <style>
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: #f4f4f4;
                    font-family: Quicksand, sans-serif;
                }
                .checkout-container {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    width: 350px;
                }
                label {
                    font-weight: bold;
                    display: block;
                    margin-bottom: 10px;
                }
                textarea {
                    width: 100%;
                    height: 80px;
                    padding: 10px;
                    border: 2px solid #ccc;
                    border-radius: 5px;
                    font-size: 16px;
                    resize: none;
                    transition: border-color 0.3s ease-in-out;
                }
                textarea:focus {
                    border-color: #294877;
                    outline: none;
                }
                button {
                    background-color: #294877;
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    font-size: 16px;
                    cursor: pointer;
                    border-radius: 5px;
                    margin-top: 10px;
                    transition: background 0.3s;
                }
                button:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="checkout-container">
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $person_id; ?>">
                    <label for="desc">Enter a description before checking out:</label>
                    <textarea name="desc"></textarea>
                    <button type="submit">Check Out</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    // User submitted description → proceed to check out
    $desc = $_POST['desc'];

    if (update_shift_end_time($existingShift, $currentTime, $desc)) {
        showSuccessMessage("Hours Logged!","viewCheckInOut.php",$existingShift);
    } else {
        echo "Error: Could not check out.";
    }
} else {
    // User is not checked in → Proceed to check in
    //if (check_existing_shift($person_id, $today)) {
    //    die("You have already checked in and out today.");
    //}

    if (insert_shift($person_id, $today, $currentTime)) {
        showSuccessMessage("Checked in successfully!", "viewCheckInOut.php");
    } else {
        echo "Error: Could not check in.";
    }
}
?>


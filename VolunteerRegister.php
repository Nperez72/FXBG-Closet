<?php
    require_once('include/input-validation.php');
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('database/dbMessages.php'); ?>
    <title>Fredericksburg SPCA | Register</title>
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
<body class="relative">
<?php
    require_once('domain/Person.php');
    require_once('database/dbPersons.php');

    $showPopup = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ignoreList = array('password', 'password-reenter');
        $args = sanitize($_POST, $ignoreList);

        $required = array(
            'first_name', 'last_name', 'birthdate',
            'street_address', 'city', 'state', 'zip', 
            'email', 'phone', 'phone_type',
            'emergency_contact_first_name', 'emergency_contact_last_name',
            'emergency_contact_relation', 'emergency_contact_phone',
            'emergency_contact_phone_type',
            'username', 'password',
            'is_community_service_volunteer',
            'is_new_volunteer', 
            'total_hours_volunteered'
        );

        $errors = false;

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $errors = true;
        }

        $first_name = $args['first_name'];
        $last_name = $args['last_name'];
        $birthday = validateDate($args['birthdate']);
        if (!$birthday) {
            echo "<p>Invalid birthdate.</p>";
            $errors = true;
        }

        $street_address = $args['street_address'];
        $city = $args['city'];
        $state = $args['state'];
        if (!valueConstrainedTo($state, array(
            'AK','AL','AR','AZ','CA','CO','CT','DC','DE','FL','GA','HI','IA','ID','IL','IN','KS','KY','LA','MA','MD','ME',
            'MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY','OH','OK','OR','PA','RI','SC','SD','TN','TX',
            'UT','VA','VT','WA','WI','WV','WY'))) {
            echo "<p>Invalid state.</p>";
            $errors = true;
        }

        $zip_code = $args['zip'];
        if (!validateZipcode($zip_code)) {
            echo "<p>Invalid ZIP code.</p>";
            $errors = true;
        }

        $email = strtolower($args['email']);
        if (!validateEmail($email)) {
            echo "<p>Invalid email.</p>";
            $errors = true;
        }

        $phone1 = validateAndFilterPhoneNumber($args['phone']);
        if (!$phone1) {
            echo "<p>Invalid phone number.</p>";
            $errors = true;
        }

        $phone1type = $args['phone_type'];
        if (!valueConstrainedTo($phone1type, array('cellphone', 'home', 'work'))) {
            echo "<p>Invalid phone type.</p>";
            $errors = true;
        }

        $emergency_contact_first_name = $args['emergency_contact_first_name'];
        $emergency_contact_last_name = $args['emergency_contact_last_name'];
        $emergency_contact_relation = $args['emergency_contact_relation'];

        $emergency_contact_phone = validateAndFilterPhoneNumber($args['emergency_contact_phone']);
        if (!$emergency_contact_phone) {
            echo "<p>Invalid emergency contact phone.</p>";
            $errors = true;
        }

        $emergency_contact_phone_type = $args['emergency_contact_phone_type'];
        if (!valueConstrainedTo($emergency_contact_phone_type, array('cellphone', 'home', 'work'))) {
            echo "<p>Invalid emergency phone type.</p>";
            $errors = true;
        }

        $skills = isset($args['skills']) ? $args['skills'] : '';
        $interests = isset($args['interests']) ? $args['interests'] : '';

        $is_community_service_volunteer = $args['is_community_service_volunteer'] === 'yes' ? 1 : 0;
        $is_new_volunteer = isset($args['is_new_volunteer']) ? (int)$args['is_new_volunteer'] : 1;
        $total_hours_volunteered = isset($args['total_hours_volunteered']) ? (float)$args['total_hours_volunteered'] : 0.00;

        $type = ($is_community_service_volunteer === 1) ? 'volunteer' : 'participant';
        $archived = 0;
        $status = "Inactive";
        $training_level = "None";

        $id = $args['username'];

        $password = isSecurePassword($args['password']);
        if (!$password) {
            echo "<p>Password is not secure enough.</p>";
            $errors = true;
        } else {
            $password = password_hash($args['password'], PASSWORD_BCRYPT);
        }

        if ($errors) {
            echo '<p class="error">Your form submission contained unexpected or invalid input.</p>';
            die();
        }

        $newperson = new Person(
            $id, $password, date("Y-m-d"),
            $first_name, $last_name, $birthday,
            $street_address, $city, $state, $zip_code,
            $phone1, $phone1type, $email,
            $emergency_contact_first_name, $emergency_contact_last_name,
            $emergency_contact_phone, $emergency_contact_phone_type,
            $emergency_contact_relation, $type, $status, $archived, 
            $skills, $interests, $training_level,
            $is_community_service_volunteer, $is_new_volunteer,
            $total_hours_volunteered
        );

        $result = add_person($newperson);
        if (!$result) {
            $showPopup = true;
        } else {
            echo '<script>document.location = "login.php?registerSuccess";</script>';
            $title = $id . " has been added as a volunteer";
            $body = "New volunteer account has been created";
            system_message_all_admins($title, $body);
        }
    } else {
        require_once('registrationForm.php');
    }
?>

<?php if ($showPopup): ?>
<div id="popupMessage" class="absolute left-[40%] top-[20%] z-50 bg-red-800 p-4 text-white rounded-xl text-xl shadow-lg">
    That username is already taken.
</div>
<?php endif; ?>

<!-- Auto-hide popup -->
<script>
window.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('popupMessage');
    if (popup) {
        popup.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
            popup.style.opacity = '0';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 500);
        }, 4000);
    }
});
</script>

</body>
</html>

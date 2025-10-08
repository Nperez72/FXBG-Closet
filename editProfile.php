<?php
    // Author: Lauren Knight
    // Description: Profile edit page
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    if (!isset($_SESSION['_id'])) {
        header('Location: login.php');
        die();
    }

    require_once('include/input-validation.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modify_access"]) && isset($_POST["id"])) {
        $id = $_POST['id'];
        header("Location: /gwyneth/modifyUserRole.php?id=$id");
    } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["profile-edit-form"])) {
        require_once('domain/Person.php');
        require_once('database/dbPersons.php');
        // make every submitted field SQL-safe except for password
        $ignoreList = array('password');
        $args = sanitize($_POST, $ignoreList);

        $editingSelf = true;
        if ($_SESSION['access_level'] >= 2 && isset($_POST['id'])) {
            $id = $_POST['id'];
            $editingSelf = $id == $_SESSION['_id'];
            $id = $args['id'];
            // Check to see if user is a lower-level manager here
        } else {
            $id = $_SESSION['_id'];
        }

        // echo "<p>The form was submitted:</p>";
        // foreach ($args as $key => $value) {
        //     echo "<p>$key: $value</p>";
        // }

        $required = array(
            'first_name', 'last_name', 'birthday', 'street_address', 'city', 'state',
            'zip_code', 'email', 'phone1', 'phone1type', 'emergency_contact_first_name',
            'emergency_contact_last_name', 'emergency_contact_phone',
            'emergency_contact_phone_type', 'emergency_contact_relation'
        );
        $errors = false;
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $errors = true;
        }

        $first_name = $args['first_name'];
        
        $last_name = $args['last_name'];
        
        $birthday = validateDate($args['birthday']);
        if (!$birthday) {
            $errors = true;
            // echo 'bad dob';
        }
        
        $street_address = $args['street_address'];

        $city = $args['city'];

        $state = $args['state'];
        if (!valueConstrainedTo($state, array('AK', 'AL', 'AR', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA',
                'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME',
                'MI', 'MN', 'MO', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM',
                'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX',
                'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY'))) {
            $errors = true;
        }

        $zip_code = $args['zip_code'];
        if (!validateZipcode($zip_code)) {
            $errors = true;
            // echo 'bad zip';
        }

        $email = validateEmail($args['email']);
        if (!$email) {
            $errors = true;
            // echo 'bad email';
        }

        $phone1 = validateAndFilterPhoneNumber($args['phone1']);
        if (!$phone1) {
            $errors = true;
            // echo 'bad phone';
        }

        $phone1type = $args['phone1type'];
        if (!valueConstrainedTo($phone1type, array('cellphone', 'home', 'work'))) {
            $errors = true;
            // echo 'bad phone type';
        }
        
        /*@
        $contactWhen = $args['contact-when'];
        $contactMethod = $args['contact-method'];
        if (!valueConstrainedTo($contactMethod, array('phone', 'text', 'email'))) {
            $errors = true;
            // echo 'bad contact method';
        }
        @*/

        $emergency_contact_first_name = $args['emergency_contact_first_name'];
        
        $emergency_contact_last_name = $args['emergency_contact_last_name'];
        
        $emergency_contact_phone = validateAndFilterPhoneNumber($args['emergency_contact_phone']);
        if (!$emergency_contact_phone) {
            $errors = true;
            // echo 'bad e-contact phone';
        }

        $emergency_contact_phone_type = $args['emergency_contact_phone_type'];
        if (!valueConstrainedTo($emergency_contact_phone_type, array('cellphone', 'home', 'work'))) {
            $errors = true;
            // echo 'bad phone type';
        }

        $emergency_contact_relation = $args['emergency_contact_relation'];

        /*@
        $gender = $args['gender'];
        if (!valueConstrainedTo($gender, ['Male', 'Female', 'Other'])) {
            $errors = true;
            echo 'bad gender';
        }
        @*/

       $type = 'v';


       
        

        
        $skills = $args['skills'];
        $interests = $args['interests'];
        
       
        // For the new fields, default to 0 if not set
        
       
        if ($errors) {
            $updateSuccess = false;
        }
        
        $result = update_person_required(
            $id, $first_name, $last_name, $birthday, $street_address, $city, $state,
            $zip_code, $email, $phone1, $phone1type, $emergency_contact_first_name,
            $emergency_contact_last_name, $emergency_contact_phone,
            $emergency_contact_phone_type, $emergency_contact_relation, $type,
             $skills, $interests
        );
        if ($result) {
            if ($editingSelf) {
                header('Location: viewProfile.php?editSuccess');
            } else {
                header('Location: viewProfile.php?editSuccess&id='. $id);
            }
            die();
        }

    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Fredericksburg SPCA | Manage Profile</title>
</head>
<body>
    <?php
        require_once('header.php');
        $isAdmin = $_SESSION['access_level'] >= 2;
        require_once('profileEditForm.php');
    ?>
</body>
</html>

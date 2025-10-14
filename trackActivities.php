<!DOCTYPE html>
<html>
<head>
    <title>FXBG Pride | Track Activities</title>
    <link href="css/normal_tw.css" rel="stylesheet">
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
    #activity_description {
    width: 100%;
    }
</style>
</head>
<body class="relative">
<?php
    require_once('activityForm.php');
?>
</body>
</html>
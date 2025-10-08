<?php
session_start();
header('Content-Type: application/json');
include_once "database/dbShifts.php";

// Authorization check
if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 2) {
    echo json_encode(["success" => false, "error" => "Unauthorized access."]);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
    exit();
}

// Retrieve and validate POST data
$shiftIds = $_POST['shift_ids'] ?? [];
$description = $_POST['description'] ?? '';

if (!is_array($shiftIds) || empty($shiftIds)) {
    echo json_encode(["success" => false, "error" => "No shift IDs provided."]);
    exit();
}

$errors = [];

foreach ($shiftIds as $shiftId) {
    try {
        clockOutByShiftId($shiftId, $description);
    } catch (Exception $e) {
        $errors[] = "Failed to clock out shift ID $shiftId: " . $e->getMessage();
    }
}

if (!empty($errors)) {
    echo json_encode(["success" => false, "error" => implode("; ", $errors)]);
    exit();
}

echo json_encode(["success" => true]);
exit();

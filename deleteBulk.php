<?php
ob_start();
include_once "database/dbDiscussions.php";

// Detect if request is AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$response = ['success' => false];

if (isset($_POST['bulk_delete']) && isset($_POST['selected_discussions'])) {
    $selected = json_decode($_POST['selected_discussions'], true);
    if (is_array($selected) && count($selected) > 0) {
        deleteDiscussions($selected);
        $response['success'] = true;
        $response['redirect'] = 'viewDiscussions.php';
    } else {
        error_log('Selected discussions are invalid or empty: ' . var_export($selected, true));
    }
} 
else if (isset($_POST['delete_all'])) {
    deleteAllDiscussions();
    $response['success'] = true;
    $response['redirect'] = 'viewDiscussions.php';
    
    // If not AJAX, do a real redirect
    if (!$isAjax) {
        header('Location: viewDiscussions.php');
        exit;
    }
} 
else {
    error_log('Missing required data for bulk delete. POST data: ' . var_export($_POST, true));
}

// For AJAX requests, return JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

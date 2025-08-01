<?php
include 'db.php';

if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', 'uploads/');
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
if (!defined('ALLOWED_TYPES')) define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);
$phone = $conn->real_escape_string($_POST['phone']);

$errors = [];

// Validate name
if (empty($name)) {
    $errors[] = "Name field is required";
}

// Check if email exists
$check_email = $conn->query("SELECT id FROM userrs WHERE email = '$email'");
if ($check_email->num_rows > 0) {
    $errors[] = "Email '$email' is already registered";
}

// Check if phone exists
if (!empty($phone)) {
    $check_phone = $conn->query("SELECT id FROM userrs WHERE phone = '$phone'");
    if ($check_phone->num_rows > 0) {
        $errors[] = "Phone number '$phone' is already in use";
    }
}

// Handle image upload
$imageName = null;
if (!empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error: " . $file['error'];
    } elseif ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "File is too large. Maximum size is " . (MAX_FILE_SIZE / 1024 / 1024) . "MB";
    } elseif (!in_array($file['type'], ALLOWED_TYPES)) {
        $errors[] = "Invalid file type. Only JPG, PNG, and GIF are allowed";
    } else {
        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $ext;
        $destination = UPLOAD_DIR . $imageName;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $errors[] = "Failed to save uploaded file";
        }
    }
}

// If any errors, display them with preserved form values
if (!empty($errors)) {
    // Store form values in session
    session_start();
    $_SESSION['form_values'] = [
        'name' => htmlspecialchars($_POST['name']),
        'email' => htmlspecialchars($_POST['email']),
        'phone' => htmlspecialchars($_POST['phone'])
    ];
    
    header("Location: error-display.php?errors=" . urlencode(json_encode($errors)) . "&type=Validation Errors&redirect=create.php");
    exit();
}

// Proceed with insertion if no errors
$sql = "INSERT INTO userrs (name, email, phone, image) VALUES ('$name', '$email', '$phone', " . 
       ($imageName ? "'$imageName'" : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    // Clear any stored form values on success
    if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['form_values']);
    }
    header("Location: read.php");
    exit();
} else {
    $errors[] = "Database error: " . $conn->error;
    header("Location: error-display.php?errors=" . urlencode(json_encode($errors)) . "&type=Database Error&redirect=create.php");
    exit();
}
?>
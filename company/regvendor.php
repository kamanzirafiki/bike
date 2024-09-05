<?php
// Include the database connection file
include '../db_connection.php';

// Function to validate email format
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number
function is_valid_phone($phone) {
    // Allow digits only, and require exactly 10 digits
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Function to check if email is already registered
function is_email_registered($email, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vendors WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}

// Collect form data
$company_name = trim($_POST['company_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = trim($_POST['password']);
$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

// Validate form data
$errors = [];

if (empty($company_name)) {
    $errors[] = "Company Name is required.";
}

if (!is_valid_email($email)) {
    $errors[] = "Invalid email format.";
} elseif (is_email_registered($email, $pdo)) {
    $errors[] = "Email is already registered.";
}

if (!is_valid_phone($phone)) {
    $errors[] = "Phone number must be exactly 10 digits long.";
}

if (strlen($password) < 6) { // Example: Minimum password length
    $errors[] = "Password must be at least 6 characters long.";
}

if (!empty($errors)) {
    echo "Errors:<br>";
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $pdo->prepare("INSERT INTO vendors (company_name, email, phone, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([$company_name, $email, $phone, $password_hash, $created_at, $updated_at]);

// Execute and check success
if ($success) {
    echo "New vendor registered successfully";
} else {
    echo "Error: " . $stmt->errorInfo()[2];
}

// Close connection (not necessary with PDO, but good practice to unset)
unset($pdo);
?>

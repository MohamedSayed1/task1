<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['_token'])) {
        die("CSRF token validation failed.");
    }

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $image = $_FILES['user_image'];

    // Validate image
    if (!isImageValid($image)) {
        die("Invalid image file.");
    }

    // Save the image to a directory and get the image path
    $image_path = 'upload/' . uniqid() . '_' . $image['name'];
    move_uploaded_file($image['tmp_name'], $image_path);

    // Connect to the database and insert the user information
    $db = new mysqli('localhost', 'root', '', 'task1');
    $query = "INSERT INTO users (first_name, last_name, image_path) VALUES ('$first_name', '$last_name', '$image_path')";
    $db->query($query);

    echo 'User added successfully!';
}

function validateCSRFToken($token) {
    return $token == $_SESSION['_token'];
}

function isImageValid($image) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    return in_array($image['type'], $allowedTypes) && $image['size'] <= $maxSize;
}

function csrf_token()
{

    $token = bin2hex(random_bytes(32));
    $_SESSION['_token'] = $token;

    return $token;

}

require 'index.html';



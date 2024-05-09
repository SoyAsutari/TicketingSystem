<?php
session_start(); 

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password']; // No hashing here
    $email = $_POST['email'];
    $notel = $_POST['notel'];
    $status = $_POST['status'];
    
    $id = generateUniqueAccountId($conn);

    $sql = "INSERT INTO accounts (id, username, password, email, notel, status) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $id, $username, $password, $email, $notel, $status);
    
    if ($stmt->execute()) {
        header("Location: SA-Create-Account.php?success=true");
        exit;
    } else {
        header("Location: SA-Create-Account.php?success=false");
        exit;
    }
} else {
    header("Location: SA-Create-Account.php");
    exit;
}

function generateUniqueAccountId($conn) {
    $id = mt_rand(1000000000, 9999999999);
    
    $sql = "SELECT id FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Generate new id if id already createdS
        return generateUniqueAccountId($conn);
    } else {
        return $id;
    }
}

$conn->close();
?>

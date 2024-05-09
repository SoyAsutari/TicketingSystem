<?php
session_start();

include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $accountId = $_GET['id'];

    $deleteRepliesSql = "DELETE FROM replies WHERE ticket_id IN (SELECT id FROM tickets WHERE account_id = ?)";
    $deleteRepliesStmt = $conn->prepare($deleteRepliesSql);
    $deleteRepliesStmt->bind_param("i", $accountId);
    $deleteRepliesStmt->execute();

    $deleteTicketsSql = "DELETE FROM tickets WHERE account_id = ?";
    $deleteTicketsStmt = $conn->prepare($deleteTicketsSql);
    $deleteTicketsStmt->bind_param("i", $accountId);
    $deleteTicketsStmt->execute();
    
    $deleteAccountSql = "DELETE FROM accounts WHERE id = ?";
    $deleteAccountStmt = $conn->prepare($deleteAccountSql);
    $deleteAccountStmt->bind_param("i", $accountId);
    $deleteAccountStmt->execute();

    if ($deleteAccountStmt->affected_rows > 0) {
        header("Location: SA-Accounts-All.php");
        exit;
    } else {
        echo "Error: Unable to delete account.";
    }

    $deleteRepliesStmt->close();
    $deleteTicketsStmt->close();
    $deleteAccountStmt->close();
}

$conn->close();
?>

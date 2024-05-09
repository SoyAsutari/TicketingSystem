<?php
include 'config.php';

if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    $sql = "UPDATE tickets SET status = 'closed' WHERE id = '$ticket_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: SA-Ticket-Desc.php?id=$ticket_id");
        exit;
    } else {
        echo "Error updating ticket status: " . $conn->error;
    }
} else {
    header("Location: SA-Main.php");
    exit;
}

$conn->close();
?>

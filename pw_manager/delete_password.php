<?php
require 'db.php';
require 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
if ($stmt->execute([$id, $user_id])) {
    header("Location: dashboard.php?delete_success=1");
} else {
    echo "Failed to delete password.";
}
?>

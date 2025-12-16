<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM accounts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

header('Location: list_accounts.php');
exit();

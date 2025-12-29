<?php
require_once __DIR__ . '/../includes/session_check.php';

require_once __DIR__ . '/../config/db.php';

$id = 0;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; 
}

if ($id > 0) {

    $sql = "DELETE FROM accounts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header('Location: list_accounts.php');
exit();

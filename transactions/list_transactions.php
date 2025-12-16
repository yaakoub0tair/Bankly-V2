<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$account_id = isset($_GET['account_id']) ? (int) $_GET['account_id'] : 0;

if ($account_id > 0) {
    $sql = "SELECT a.account_number, c.full_name
            FROM accounts a
            JOIN clients c ON c.id = a.client_id
            WHERE a.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $account_id]);
    $account = $stmt->fetch();
} else {
    $account = null;
}

$sql = "SELECT t.*, a.account_number, u.username
        FROM transactions t
        JOIN accounts a ON a.id = t.account_id
        LEFT JOIN users u ON u.id = t.user_id";
$params = [];

if ($account_id > 0) {
    $sql .= " WHERE t.account_id = :account_id";
    $params[':account_id'] = $account_id;
}

$sql .= " ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transactions - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
<div class="topbar">
    <div class="left">
        <strong>Bankly V2</strong> – Historique des transactions
    </div>
    <div class="right">
        <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
        &nbsp;|&nbsp;
        <a href="../dashboard.php">Dashboard</a>
        &nbsp;|&nbsp;
        <a href="../logout.php">Se déconnecter</a>
    </div>
</div>

<main>
    <h1>Historique des transactions</h1>

    <?php if ($account): ?>
        <p>Compte : <?php echo htmlspecialchars($account['account_number']); ?>
           — Client : <?php echo htmlspecialchars($account['full_name']); ?></p>
    <?php endif; ?>

    <table class="table-basic">
        <thead>
        <tr>
            <th>ID</th>
            <th>Compte</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Avant</th>
            <th>Après</th>
            <th>Utilisateur</th>
            <th>Date</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?php echo htmlspecialchars($t['id']); ?></td>
                <td><?php echo htmlspecialchars($t['account_number']); ?></td>
                <td><?php echo htmlspecialchars($t['type']); ?></td>
                <td><?php echo htmlspecialchars($t['amount']); ?></td>
                <td><?php echo htmlspecialchars($t['balance_before']); ?></td>
                <td><?php echo htmlspecialchars($t['balance_after']); ?></td>
                <td><?php echo htmlspecialchars($t['username'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($t['created_at']); ?></td>
                <td><?php echo htmlspecialchars($t['description']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../dashboard.php">
            <i class="fas fa-arrow-left"></i> Bankly V2
        </a>
        <div class="d-flex align-items-center gap-2">
            <small class="text-white">
                <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)
            </small>
            <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>
            <i class="fas fa-receipt text-primary"></i>
            Historique des transactions
        </h1>
        <?php if ($account): ?>
            <span class="badge bg-primary fs-6">
                Compte : <?= htmlspecialchars($account['account_number']) ?>
                — <?= htmlspecialchars($account['full_name']) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
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
                <?php if (count($transactions) > 0): ?>
                    <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['id']) ?></td>
                            <td><code><?= htmlspecialchars($t['account_number']) ?></code></td>
                            <td>
                                <?php if ($t['type'] === 'deposit'): ?>
                                    <span class="badge bg-success">Dépôt</span>
                                <?php elseif ($t['type'] === 'withdraw'): ?>
                                    <span class="badge bg-danger">Retrait</span>
                                <?php elseif ($t['type'] === 'transfer'): ?>
                                    <span class="badge bg-info text-dark">Virement</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($t['type']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(number_format($t['amount'], 2, '.', ' ')) ?></td>
                            <td><?= htmlspecialchars(number_format($t['balance_before'], 2, '.', ' ')) ?></td>
                            <td><?= htmlspecialchars(number_format($t['balance_after'], 2, '.', ' ')) ?></td>
                            <td><?= htmlspecialchars($t['username'] ?? '') ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($t['created_at']) ?></small></td>
                            <td><?= htmlspecialchars($t['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                            Aucune transaction trouvée
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

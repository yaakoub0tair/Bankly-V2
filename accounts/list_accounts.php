<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$sql = "SELECT a.*, c.full_name 
        FROM accounts a
        JOIN clients c ON a.client_id = c.id";
$result   = $pdo->query($sql);
$accounts = $result->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comptes - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
<div class="topbar">
    <div class="left">
        <strong>Bankly V2</strong> – Comptes
    </div>
    <div class="right">
        <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
        | <a href="../dashboard.php">Dashboard</a>
        | <a href="../logout.php">Se déconnecter</a>
    </div>
</div>

<main>
    <div class="header-row">
        <h1>Liste des comptes (<?php echo count($accounts); ?>)</h1>
        <a href="add_account.php" class="link-card">+ Nouveau compte</a>
    </div>

    <table class="table-basic">
        <thead>
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Numéro</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Solde</th>
            <th>Créé le</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($accounts as $a): ?>
            <tr>
                <td><?php echo htmlspecialchars($a['id']); ?></td>
                <td><?php echo htmlspecialchars($a['full_name']); ?></td>
                <td><?php echo htmlspecialchars($a['account_number']); ?></td>
                <td><?php echo htmlspecialchars($a['type']); ?></td>
                <td><?php echo htmlspecialchars($a['status']); ?></td>
                <td><?php echo htmlspecialchars($a['balance']); ?></td>
                <td><?php echo htmlspecialchars($a['created_at']); ?></td>
                <td>
                    <a href="edit_account.php?id=<?php echo $a['id']; ?>">Modifier</a>
                    |
                    <a href="delete_account.php?id=<?php echo $a['id']; ?>"
                       onclick="return confirm('Supprimer ce compte ?');">
                        Supprimer
                    </a>
                    |
                    <a href="../transactions/make_transaction.php?account_id=<?php echo $a['id']; ?>">
                        Transaction
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>

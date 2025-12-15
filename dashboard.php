<?php
require_once __DIR__ . '/includes/session_check.php';
require_once __DIR__ . '/config/db.php'; 


$nb_clients      = $pdo->query("SELECT COUNT(*) AS c FROM clients")->fetch()['c'] ?? 0;
$nb_accounts     = $pdo->query("SELECT COUNT(*) AS c FROM accounts")->fetch()['c'] ?? 0;
$nb_transactions = $pdo->query("SELECT COUNT(*) AS c FROM transactions")->fetch()['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Bankly V2</title>
    <link rel="stylesheet" href="./public/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="left">
            <strong>Bankly V2</strong> – Dashboard
        </div>
        <div class="right">
            <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
            &nbsp;|&nbsp;
            <a href="logout.php">Se déconnecter</a>
        </div>
    </div>

    <main>
        <h1>Bienvenue <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Clients</h3>
                <p><?php echo $nb_clients; ?></p>
            </div>
            <div class="stat-card">
                <h3>Comptes</h3>
                <p><?php echo $nb_accounts; ?></p>
            </div>
            <div class="stat-card">
                <h3>Transactions</h3>
                <p><?php echo $nb_transactions; ?></p>
            </div>
        </div>

        <div class="links-grid">
            <a href="clients/list_clients.php" class="link-card">Gérer les clients</a>
            <a href="accounts/list_accounts.php" class="link-card">Gérer les comptes</a>
            <a href="transactions/make_transaction.php" class="link-card">Faire une transaction</a>
            <a href="transactions/list_transactions.php" class="link-card">Historique des transactions</a>
        </div>
    </main>
</body>
</html>

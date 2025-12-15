<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$sql = "SELECT * FROM clients";
$result = $pdo->query($sql);
$clients = $result->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clients - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="left">
            <strong>Bankly V2</strong> – Clients
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
        <div class="header-row">
            <h1>Liste des clients (<?php echo count($clients); ?>)</h1>
            <a href="add_client.php" class="link-card">+ Nouveau client</a>
        </div>

        <table class="table-basic">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>CIN</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Créé le</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['id']); ?></td>
                        <td><?php echo htmlspecialchars($c['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['email']); ?></td>
                        <td><?php echo htmlspecialchars($c['cin']); ?></td>
                        <td><?php echo htmlspecialchars($c['telephone']); ?></td>
                        <td><?php echo htmlspecialchars($c['adress']); ?></td>
                        <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>

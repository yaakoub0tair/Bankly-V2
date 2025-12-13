<?php
require_once __DIR__ . '/includes/session_check.php';
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
    </main>
</body>
</html>

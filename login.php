<?php
// login.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/config/db.php';

$error = '';
$username = '';


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Khassek t3mer jami3 les champs.';
    } else {
        
        $stmt = $pdo->prepare(
            'SELECT id, username, password, role 
             FROM users 
             WHERE username = ? AND password = ?'
        );
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();

        if ($user) {
     
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Username ou mot de passe ghalaṭ.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login - Bankly V2</title>
    <link rel="stylesheet" href="./public/css/login.css">
 
</head>
<body>
    <div class="card">
        <div class="title">Bankly V2</div>
        <div class="subtitle">Espace interne - Connexion agent</div>

        <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Nom d’utilisateur</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?php echo htmlspecialchars($username ?? ''); ?>"
                    placeholder="admin ou agent1"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Votre mot de passe"
                    required
                >
            </div>

            <button type="submit" class="btn-primary">Se connecter</button>
        </form>

        <p class="hint">
            Comptes de test :
            <span class="badge">admin / 12345678</span>
            <span class="badge">agent1 / 12345678</span>
        </p>
    </div>
</body>
</html>

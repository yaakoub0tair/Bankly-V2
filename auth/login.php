<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/db.php';

$error    = '';
$username = '';

if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Tous les champs sont requis.';
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

            header('Location: ../dashboard.php');
            exit();
        } else {
            $error = 'Username ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-1">
                        <i class="fas fa-university"></i> Bankly V2
                    </h2>
                    <small>Espace interne · Connexion</small>
                </div>

                <div class="card-body p-5">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" autocomplete="off">
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Username
                            </label>
                            <input 
                                type="text" 
                                name="username" 
                                class="form-control" 
                                value="<?= htmlspecialchars($username ?? '') ?>" 
                                placeholder="Entrez votre username" 
                                required 
                                autofocus
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Mot de passe
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                class="form-control" 
                                placeholder="Entrez votre mot de passe" 
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </button>

                    </form>

                    <hr class="my-4">

                    <p class="text-center text-muted small mb-0">
                        <i class="fas fa-shield-alt"></i> Connexion sécurisée
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

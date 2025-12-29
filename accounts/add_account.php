<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];


$clients = $pdo->query("
    SELECT id, full_name 
    FROM clients 
    ORDER BY full_name
")->fetchAll();


$client_id = 0;
$account_number = '';
$type = 'courant';
$status = 'actif';
$balance = '0.00';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    $client_id      = (int)($_POST['client_id'] ?? 0);
    $account_number = trim($_POST['account_number'] ?? '');
    $type           = $_POST['type'] ?? 'courant';
    $status         = $_POST['status'] ?? 'actif';
    $balance        = trim($_POST['balance'] ?? '0');

  
    if ($client_id <= 0) {
        $errors[] = "Client obligatoire.";
    }

    if ($account_number === '') {
        $errors[] = "Numéro de compte obligatoire.";
    }

    if (!is_numeric($balance)) {
        $errors[] = "Solde invalide.";
    }

   
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO accounts 
            (client_id, account_number, type, status, balance, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $client_id,
            $account_number,
            $type,
            $status,
            $balance
        ]);

        header('Location: list_accounts.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un compte - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="list_accounts.php">
            <i class="fas fa-arrow-left"></i> Comptes
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

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 m-0">
                    <i class="fas fa-credit-card text-primary"></i>
                    Ajouter un compte
                </h1>
                <a href="list_accounts.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list"></i> Liste des comptes
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-triangle-exclamation"></i> Erreurs :
                    </h6>
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="post" novalidate>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user text-primary"></i> Client *
                            </label>
                            <select name="client_id" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= ($client_id == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-hashtag text-primary"></i> Numéro de compte *
                            </label>
                            <input type="text"
                                   name="account_number"
                                   class="form-control"
                                   value="<?= htmlspecialchars($account_number) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-layer-group text-primary"></i> Type
                            </label>
                            <select name="type" class="form-select">
                                <option value="courant" <?= $type === 'courant' ? 'selected' : '' ?>>Courant</option>
                                <option value="epargne" <?= $type === 'epargne' ? 'selected' : '' ?>>Épargne</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-toggle-on text-primary"></i> Statut
                            </label>
                            <select name="status" class="form-select">
                                <option value="actif" <?= $status === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="suspendu" <?= $status === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                                <option value="ferme" <?= $status === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-coins text-primary"></i> Solde initial
                            </label>
                            <input type="number"
                                   step="0.01"
                                   name="balance"
                                   class="form-control"
                                   value="<?= htmlspecialchars($balance) ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                            <a href="list_accounts.php" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            <p class="text-muted small mt-3">
                Les champs marqués d'un * sont obligatoires.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- [web:23] -->
</body>
</html>

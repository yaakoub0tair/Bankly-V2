<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: list_accounts.php');
    exit();
}

$sql = "SELECT * FROM accounts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$account = $stmt->fetch();

if (!$account) {
    header('Location: list_accounts.php');
    exit();
}

$account_number = $account['account_number'];
$type           = $account['type'];
$status         = $account['status'];
$balance        = $account['balance'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_number = trim($_POST['account_number'] ?? '');
    $type           = trim($_POST['type'] ?? '');
    $status         = trim($_POST['status'] ?? '');
    $balance        = trim($_POST['balance'] ?? '');

    if ($account_number === '') $errors[] = "Le numéro de compte est obligatoire.";
    if ($type === '')           $errors[] = "Le type est obligatoire.";
    if ($status === '')         $errors[] = "Le statut est obligatoire.";
    if ($balance === '' || !is_numeric($balance)) {
        $errors[] = "Le solde doit être un nombre.";
    }

    if (empty($errors)) {
        $sql = "UPDATE accounts
                SET account_number = :account_number,
                    type           = :type,
                    status         = :status,
                    balance        = :balance
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':account_number' => $account_number,
            ':type'           => $type,
            ':status'         => $status,
            ':balance'        => $balance,
            ':id'             => $id,
        ]);

        header('Location: list_accounts.php?success=updated');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un compte - Bankly V2</title>
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
                    <i class="fas fa-pen text-warning"></i>
                    Modifier le compte #<?= htmlspecialchars($id) ?>
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
                                <i class="fas fa-layer-group text-primary"></i> Type *
                            </label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <option value="courant" <?= $type === 'courant' ? 'selected' : '' ?>>Courant</option>
                                <option value="epargne" <?= $type === 'epargne' ? 'selected' : '' ?>>Épargne</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-toggle-on text-primary"></i> Statut *
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <option value="actif" <?= $status === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="suspendu" <?= $status === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                                <option value="ferme" <?= $status === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-coins text-primary"></i> Solde *
                            </label>
                            <input type="number"
                                   step="0.01"
                                   name="balance"
                                   class="form-control"
                                   value="<?= htmlspecialchars($balance) ?>"
                                   required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Mettre à jour
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

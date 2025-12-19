<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: list_clients.php');
    exit();
}

$sql = "SELECT * FROM clients WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: list_clients.php');
    exit();
}

$fullname  = $client['full_name'];
$email     = $client['email'];
$cin       = $client['cin'];
$telephone = $client['telephone'];
$adresse   = $client['adress'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname  = trim($_POST['fullname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $cin       = trim($_POST['cin'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse   = trim($_POST['adresse'] ?? '');

    if ($fullname === '') $errors[] = "Le nom complet est obligatoire.";
    if ($email === '')    $errors[] = "L'email est obligatoire.";
    if ($cin === '')      $errors[] = "Le CIN est obligatoire.";

    if (empty($errors)) {
        $sql = "UPDATE clients
                SET full_name = :full_name,
                    email     = :email,
                    cin       = :cin,
                    telephone = :telephone,
                    adress    = :adress
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $fullname,
            ':email'     => $email,
            ':cin'       => $cin,
            ':telephone' => $telephone,
            ':adress'    => $adresse,
            ':id'        => $id,
        ]);

        header('Location: list_clients.php?success=updated');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un client - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- [web:23] -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- [web:26] -->
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="list_clients.php">
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

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 m-0">
                    <i class="fas fa-user-edit text-warning"></i>
                    Modifier le client #<?= htmlspecialchars($id) ?>
                </h1>
                <a href="list_clients.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list"></i> Liste des clients
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
                                <i class="fas fa-user text-primary"></i> Nom complet *
                            </label>
                            <input type="text"
                                   name="fullname"
                                   class="form-control"
                                   value="<?= htmlspecialchars($fullname) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope text-primary"></i> Email *
                            </label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($email) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-id-card text-primary"></i> CIN *
                            </label>
                            <input type="text"
                                   name="cin"
                                   class="form-control"
                                   value="<?= htmlspecialchars($cin) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone text-primary"></i> Téléphone
                            </label>
                            <input type="text"
                                   name="telephone"
                                   class="form-control"
                                   value="<?= htmlspecialchars($telephone) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt text-primary"></i> Adresse
                            </label>
                            <textarea name="adresse"
                                      class="form-control"
                                      rows="3"><?= htmlspecialchars($adresse) ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="list_clients.php" class="btn btn-outline-secondary">
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

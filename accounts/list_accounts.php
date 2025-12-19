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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptes - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../dashboard.php">
            <i class="fas fa-arrow-left"></i> Bankly V2
        </a>
        <div class="d-flex align-items-center gap-2">
            <small class="text-white"><?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</small>
            <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-credit-card text-primary"></i>
            Comptes (<?= count($accounts) ?>)
        </h1>
        <a href="add_account.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau compte
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
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
                <?php if (count($accounts) > 0): ?>
                    <?php foreach ($accounts as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['id']) ?></td>
                            <td><strong><?= htmlspecialchars($a['full_name']) ?></strong></td>
                            <td><?= htmlspecialchars($a['account_number']) ?></td>
                            <td><?= htmlspecialchars($a['type']) ?></td>
                            <td><?= htmlspecialchars($a['status']) ?></td>
                            <td><?= htmlspecialchars(number_format($a['balance'], 2, '.', ' ')) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($a['created_at']) ?></small></td>
                            <td class="d-flex gap-1 flex-wrap">
                                <a href="edit_account.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete_account.php?id=<?= $a['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Supprimer ce compte ?');"
                                   title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="../transactions/make_transaction.php?account_id=<?= $a['id'] ?>"
                                   class="btn btn-sm btn-info"
                                   title="Nouvelle transaction">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                            Aucun compte trouvé
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

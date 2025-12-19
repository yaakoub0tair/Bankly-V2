<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$clients = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - Bankly V2</title>
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
            <small class="text-white"><?= htmlspecialchars($username) ?></small>
            <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users text-primary"></i> Clients</h1>
        <a href="add_client.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Client <?= $_GET['success'] === 'added' ? 'ajouté' : ($_GET['success'] === 'updated' ? 'modifié' : 'supprimé') ?> avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>cin</th>
                        <th>telephone</th>
                        <th>adress</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                        
                      
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($clients) > 0): ?>
                        <?php foreach ($clients as $i => $client): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($client['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($client['email']) ?></td>
                                <td><?= htmlspecialchars($client['cin']) ?></td>
                                <td><?= htmlspecialchars($client['telephone']) ?></td>
                                <td><?= htmlspecialchars($client['adress']) ?></td>
                                <td><small class="text-muted"><?= date('d/m/Y', strtotime($client['created_at'])) ?></small></td>
                                <td>
                                    <a href="view_client.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                                    <a href="edit_client.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-warning" title="Modifier"><i class="fas fa-edit"></i></a>
                                    <a href="delete_client.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')" title="Supprimer"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                Aucun client trouvé
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

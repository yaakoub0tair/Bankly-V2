<?php
require_once __DIR__ . '/includes/session_check.php';
require_once __DIR__ . '/config/db.php';

$nb_clients = $pdo->query("SELECT COUNT(*) AS c FROM clients")->fetch()['c'] ?? 0;
$nb_accounts = $pdo->query("SELECT COUNT(*) AS c FROM accounts")->fetch()['c'] ?? 0;
$nb_transactions = $pdo->query("SELECT COUNT(*) AS c FROM transactions")->fetch()['c'] ?? 0;
$total_balance = $pdo->query("SELECT SUM(balance) AS total FROM accounts")->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hover-lift { transition: all 0.3s; }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-university text-primary"></i> Bankly V2
        </a>
        <div class="d-flex align-items-center gap-2">
            <small class="text-white"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></small>
            <a href="auth/logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <h1 class="mb-1"><i class="fas fa-wave-hand"></i> Bienvenue, <span class="text-primary"><?= htmlspecialchars($username) ?></span></h1>
    <p class="text-muted small mb-4"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y H:i') ?></p>

    <div class="row mb-4 g-3">
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-primary border-5 shadow-sm">
                <div class="card-body p-3">
                    <i class="fas fa-users text-primary fa-2x"></i>
                    <h6 class="text-muted text-uppercase fw-bold fs-6 mt-2 mb-1">Clients</h6>
                    <h3 class="fw-bold text-primary mb-1"><?= $nb_clients ?></h3>
                    <p class="text-success small mb-0"><i class="fas fa-arrow-up"></i> Actifs</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-info border-5 shadow-sm">
                <div class="card-body p-3">
                    <i class="fas fa-credit-card text-info fa-2x"></i>
                    <h6 class="text-muted text-uppercase fw-bold fs-6 mt-2 mb-1">Comptes</h6>
                    <h3 class="fw-bold text-info mb-1"><?= $nb_accounts ?></h3>
                    <p class="text-success small mb-0"><i class="fas fa-arrow-up"></i> Ouverts</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-warning border-5 shadow-sm">
                <div class="card-body p-3">
                    <i class="fas fa-exchange-alt text-warning fa-2x"></i>
                    <h6 class="text-muted text-uppercase fw-bold fs-6 mt-2 mb-1">Transactions</h6>
                    <h3 class="fw-bold text-warning mb-1"><?= $nb_transactions ?></h3>
                    <p class="text-success small mb-0"><i class="fas fa-arrow-up"></i> Enregistrées</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-success border-5 shadow-sm">
                <div class="card-body p-3">
                    <i class="fas fa-coins text-success fa-2x"></i>
                    <h6 class="text-muted text-uppercase fw-bold fs-6 mt-2 mb-1">Solde Total</h6>
                    <h3 class="fw-bold text-success mb-1"><?= number_format($total_balance, 0, ',', ' ') ?> MAD</h3>
                    <p class="text-success small mb-0"><i class="fas fa-arrow-up"></i> Total</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mb-3 fw-bold"><i class="fas fa-cogs"></i> Gestion</h2>

    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <a href="clients/list_clients.php" class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-3">
                        <i class="fas fa-users text-primary fa-3x mb-2"></i>
                        <h5 class="card-title fw-bold mb-2">Gérer Clients</h5>
                        <p class="card-text text-muted small">Créer, modifier les fiches clients.</p>
                    </div>
                    <div class="card-footer bg-light border-0 p-2">
                        <small class="text-primary fw-bold"><i class="fas fa-arrow-right"></i> Plus</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="accounts/list_accounts.php" class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-3">
                        <i class="fas fa-credit-card text-info fa-3x mb-2"></i>
                        <h5 class="card-title fw-bold mb-2">Gérer Comptes</h5>
                        <p class="card-text text-muted small">Ouvrir, modifier les comptes bancaires.</p>
                    </div>
                    <div class="card-footer bg-light border-0 p-2">
                        <small class="text-info fw-bold"><i class="fas fa-arrow-right"></i> Plus</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="transactions/list_transactions.php" class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-3">
                        <i class="fas fa-history text-warning fa-3x mb-2"></i>
                        <h5 class="card-title fw-bold mb-2">Transactions</h5>
                        <p class="card-text text-muted small">Visualiser dépôts et retraits.</p>
                    </div>
                    <div class="card-footer bg-light border-0 p-2">
                        <small class="text-warning fw-bold"><i class="fas fa-arrow-right"></i> Plus</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

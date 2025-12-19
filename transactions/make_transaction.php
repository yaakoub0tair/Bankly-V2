<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];
$account_id = isset($_GET['account_id']) ? (int) $_GET['account_id'] : 0;

if ($account_id <= 0) {
    header('Location: ../accounts/list_accounts.php');
    exit();
}

$sql = "SELECT a.*, c.full_name
        FROM accounts a
        JOIN clients c ON c.id = a.client_id
        WHERE a.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $account_id]);
$account = $stmt->fetch();

if (!$account) {
    header('Location: ../accounts/list_accounts.php');
    exit();
}

$type        = 'depot';
$amount      = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type        = $_POST['type'] ?? 'depot';
    $amount      = trim($_POST['amount'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!in_array($type, ['depot', 'retrait'], true)) {
        $errors[] = "Type de transaction invalide.";
    }

    if ($amount === '' || !is_numeric($amount) || $amount <= 0) {
        $errors[] = "Le montant doit être un nombre positif.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $sql = "SELECT balance FROM accounts WHERE id = :id FOR UPDATE";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $account_id]);
            $row = $stmt->fetch();

            if (!$row) {
                $pdo->rollBack();
                $errors[] = "Compte introuvable.";
            } else {
                $balance_before = (float) $row['balance'];
                $amount_val     = (float) $amount;

                if ($type === 'retrait' && $balance_before < $amount_val) {
                    $pdo->rollBack();
                    $errors[] = "Solde insuffisant pour ce retrait.";
                } else {
                    $balance_after = $type === 'depot'
                        ? $balance_before + $amount_val
                        : $balance_before - $amount_val;

                    $sql = "UPDATE accounts
                            SET balance = :balance_after
                            WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':balance_after' => $balance_after,
                        ':id'            => $account_id,
                    ]);

                    $sql = "INSERT INTO transactions
                            (account_id, user_id, type, amount,
                             balance_before, balance_after, description)
                            VALUES
                            (:account_id, :user_id, :type, :amount,
                             :balance_before, :balance_after, :description)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':account_id'     => $account_id,
                        ':user_id'        => (int) $user_id,
                        ':type'           => $type,
                        ':amount'         => $amount_val,
                        ':balance_before' => $balance_before,
                        ':balance_after'  => $balance_after,
                        ':description'    => $description,
                    ]);

                    $pdo->commit();
                    header('Location: list_transactions.php?account_id=' . $account_id . '&success=created');
                    exit();
                }
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = "Erreur lors de la transaction : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction - Bankly V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../accounts/list_accounts.php">
            <i class="fas fa-arrow-left"></i> Comptes
        </a>
        <div class="d-flex align-items-center gap-2">
            <small class="text-white">
                <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)
            </small>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">
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
                    <i class="fas fa-exchange-alt text-primary"></i>
                    Nouvelle transaction
                </h1>
                <a href="list_transactions.php?account_id=<?= $account_id ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-receipt"></i> Historique
                </a>
            </div>

            <!-- Account Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-credit-card"></i>
                        Compte : <?= htmlspecialchars($account['account_number']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Client</p>
                            <h6 class="mb-0"><?= htmlspecialchars($account['full_name']) ?></h6>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Solde actuel</p>
                            <h6 class="mb-0 text-success">
                                <strong><?= htmlspecialchars(number_format($account['balance'], 2, '.', ' ')) ?> DH</strong>
                            </h6>
                        </div>
                    </div>
                </div>
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
                                <i class="fas fa-layer-group text-primary"></i> Type de transaction *
                            </label>
                            <select name="type" class="form-select" required>
                                <option value="depot" <?= $type === 'depot' ? 'selected' : '' ?>>
                                    <i class="fas fa-arrow-down"></i> Dépôt
                                </option>
                                <option value="retrait" <?= $type === 'retrait' ? 'selected' : '' ?>>
                                    <i class="fas fa-arrow-up"></i> Retrait
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-coins text-primary"></i> Montant *
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       step="0.01"
                                       name="amount"
                                       class="form-control"
                                       value="<?= htmlspecialchars($amount) ?>"
                                       placeholder="0.00"
                                       required>
                                <span class="input-group-text">DH</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-note-sticky text-primary"></i> Description
                            </label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Notes supplémentaires..."><?= htmlspecialchars($description) ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Valider la transaction
                            </button>
                            <a href="list_transactions.php?account_id=<?= $account_id ?>" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h6><i class="fas fa-lightbulb"></i> Avant de valider</h6>
                <ul class="mb-0">
                    <li>Vérifiez que le montant est correct</li>
                    <li>Pour un retrait, assurez-vous que le solde est suffisant</li>
                    <li>Ajoutez une description pour garder une trace</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

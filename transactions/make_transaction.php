<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];
$account_id = isset($_GET['account_id']) ? (int) $_GET['account_id'] : 0;

if ($account_id <= 0) {
    header('Location: ../dashboard.php');
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
    header('Location: ../dashboard.php');
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
                    header('Location: list_transactions.php?account_id=' . $account_id);
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
    <title>Transaction - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
<div class="topbar">
    <div class="left">
        <strong>Bankly V2</strong> – Nouvelle transaction
    </div>
    <div class="right">
        <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
        &nbsp;|&nbsp;
        <a href="../dashboard.php">Dashboard</a>
        &nbsp;|&nbsp;
        <a href="../logout.php">Se déconnecter</a>
    </div>
</div>

<main>
    <h1>Transaction sur le compte <?php echo htmlspecialchars($account['account_number']); ?></h1>
    <p>Client : <?php echo htmlspecialchars($account['full_name']); ?></p>
    <p>Solde actuel : <?php echo htmlspecialchars($account['balance']); ?></p>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $e): ?>
                <p><?php echo htmlspecialchars($e); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-basic">
        <label>
            Type de transaction*
            <select name="type">
                <option value="depot"   <?php if ($type === 'depot')   echo 'selected'; ?>>Dépôt</option>
                <option value="retrait" <?php if ($type === 'retrait') echo 'selected'; ?>>Retrait</option>
            </select>
        </label>

        <label>
            Montant*
            <input type="text" name="amount"
                   value="<?php echo htmlspecialchars($amount); ?>">
        </label>

        <label>
            Description
            <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
        </label>

        <button type="submit" class="btn-primary">Valider la transaction</button>
    </form>
</main>
</body>
</html>

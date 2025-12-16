<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];

// Clients pour le select
$clients_stmt = $pdo->query("SELECT id, full_name FROM clients ORDER BY full_name");
$clients = $clients_stmt->fetchAll();

$client_id      = '';
$account_number = '';
$type           = 'courant';
$status         = 'actif';
$balance        = '0.00';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id      = (int)($_POST['client_id'] ?? 0);
    $account_number = trim($_POST['account_number'] ?? '');
    $type           = $_POST['type']   ?? 'courant';
    $status         = $_POST['status'] ?? 'actif';
    $balance        = trim($_POST['balance'] ?? '0');

    if ($client_id <= 0) $errors[] = "Client obligatoire.";
    if ($account_number === '') $errors[] = "Numéro de compte obligatoire.";
    if ($balance === '' || !is_numeric($balance)) $errors[] = "Solde initial invalide.";

    if (empty($errors)) {
        $sql = "INSERT INTO accounts (client_id, account_number, type, status, balance, created_at)
                VALUES (:client_id, :account_number, :type, :status, :balance, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':client_id'      => $client_id,
            ':account_number' => $account_number,
            ':type'           => $type,
            ':status'         => $status,
            ':balance'        => $balance,
        ]);
        header('Location: list_accounts.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un compte - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
<div class="topbar">
    <div class="left">
        <strong>Bankly V2</strong> – Ajouter un compte
    </div>
    <div class="right">
        <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
        | <a href="list_accounts.php">Retour</a>
        | <a href="../logout.php">Se déconnecter</a>
    </div>
</div>

<main>
    <h1>Ajouter un compte</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $e): ?><p><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-basic">
        <label>Client*
            <select name="client_id">
                <option value="">-- Choisir --</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?php echo $c['id']; ?>"
                        <?php if ($client_id == $c['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($c['full_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Numéro de compte*
            <input type="text" name="account_number"
                   value="<?php echo htmlspecialchars($account_number); ?>">
        </label>

        <label>Type
            <select name="type">
                <option value="courant" <?php if ($type === 'courant') echo 'selected'; ?>>Courant</option>
                <option value="epargne" <?php if ($type === 'epargne') echo 'selected'; ?>>Épargne</option>
            </select>
        </label>

        <label>Statut
            <select name="status">
                <option value="actif" <?php if ($status === 'actif') echo 'selected'; ?>>Actif</option>
                <option value="suspendu" <?php if ($status === 'suspendu') echo 'selected'; ?>>Suspendu</option>
                <option value="ferme" <?php if ($status === 'ferme') echo 'selected'; ?>>Fermé</option>
            </select>
        </label>

        <label>Solde initial
            <input type="number" step="0.01" name="balance"
                   value="<?php echo htmlspecialchars($balance); ?>">
        </label>

        <button type="submit" class="btn-primary">Enregistrer</button>
    </form>
</main>
</body>
</html>

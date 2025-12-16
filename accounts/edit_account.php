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

        header('Location: list_accounts.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un compte - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="left">
            <strong>Bankly V2</strong> – Modifier un compte
        </div>
        <div class="right">
            <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
            &nbsp;|&nbsp;
            <a href="list_accounts.php">Retour à la liste</a>
            &nbsp;|&nbsp;
            <a href="../logout.php">Se déconnecter</a>
        </div>
    </div>

    <main>
        <h1>Modifier le compte #<?php echo htmlspecialchars($id); ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-basic">
            <label>
                Numéro de compte*
                <input type="text" name="account_number"
                       value="<?php echo htmlspecialchars($account_number); ?>">
            </label>

            <label>
                Type*
                <input type="text" name="type"
                       value="<?php echo htmlspecialchars($type); ?>">
            </label>

            <label>
                Statut*
                <input type="text" name="status"
                       value="<?php echo htmlspecialchars($status); ?>">
            </label>

            <label>
                Solde*
                <input type="text" name="balance"
                       value="<?php echo htmlspecialchars($balance); ?>">
            </label>

            <button type="submit" class="btn-primary">Mettre à jour</button>
        </form>
    </main>
</body>
</html>

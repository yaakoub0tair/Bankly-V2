<?php
require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];

$fullname  = '';
$email     = '';
$cin       = '';
$telephone = '';
$adresse   = '';

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
        $sql = "INSERT INTO clients (full_name, email, cin, telephone, adress, created_at)
                VALUES (:full_name, :email, :cin, :telephone, :adress, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $fullname,
            ':email'     => $email,
            ':cin'       => $cin,
            ':telephone' => $telephone,
            ':adress'    => $adresse,
        ]);

        header('Location: list_clients.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un client - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="left">
            <strong>Bankly V2</strong> – Ajouter un client
        </div>
        <div class="right">
            <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)
            &nbsp;|&nbsp;
            <a href="list_clients.php">Retour à la liste</a>
            &nbsp;|&nbsp;
            <a href="../logout.php">Se déconnecter</a>
        </div>
    </div>

    <main>
        <h1>Ajouter un client</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-basic">
            <label>
                Nom complet*
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>">
            </label>

            <label>
                Email*
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            </label>

            <label>
                CIN*
                <input type="text" name="cin" value="<?php echo htmlspecialchars($cin); ?>">
            </label>

            <label>
                Téléphone
                <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">
            </label>

            <label>
                Adresse
                <textarea name="adresse"><?php echo htmlspecialchars($adresse); ?></textarea>
            </label>

            <button type="submit" class="btn-primary">Enregistrer</button>
        </form>
    </main>
</body>
</html>

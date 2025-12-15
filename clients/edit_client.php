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

        header('Location: list_clients.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un client - Bankly V2</title>
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="left">
            <strong>Bankly V2</strong> – Modifier un client
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
        <h1>Modifier le client #<?php echo htmlspecialchars($id); ?></h1>

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
                <input type="text" name="fullname"
                       value="<?php echo htmlspecialchars($fullname); ?>">
            </label>

            <label>
                Email*
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($email); ?>">
            </label>

            <label>
                CIN*
                <input type="text" name="cin"
                       value="<?php echo htmlspecialchars($cin); ?>">
            </label>

            <label>
                Téléphone
                <input type="text" name="telephone"
                       value="<?php echo htmlspecialchars($telephone); ?>">
            </label>

            <label>
                Adresse
                <textarea name="adresse"><?php echo htmlspecialchars($adresse); ?></textarea>
            </label>

            <button type="submit" class="btn-primary">Mettre à jour</button>
        </form>
    </main>
</body>
</html>

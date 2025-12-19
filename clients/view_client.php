<?php
require_once __DIR__.'/../includes/session_check.php';
require_once __DIR__.'/../config/db.php';

$id=isset($_GET['id'])?(int)$_GET['id']:0;
if($id<=0){header('Location: list_clients.php');exit();}

$stmt=$pdo->prepare("SELECT * FROM clients WHERE id=:id");
$stmt->execute([':id'=>$id]);
$c=$stmt->fetch();
if(!$c){header('Location: list_clients.php');exit();}

$stmtAcc=$pdo->prepare("SELECT * FROM accounts WHERE client_id=:id ORDER BY created_at DESC");
$stmtAcc->execute([':id'=>$id]);
$accs=$stmtAcc->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Client #<?=htmlspecialchars($id)?> - Bankly V2</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="bg-light">
<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="list_clients.php"><i class="fas fa-arrow-left"></i> Clients</a>
    <div class="d-flex align-items-center gap-2">
      <small class="text-white"><?=htmlspecialchars($username)?> (<?=htmlspecialchars($role)?>)</small>
      <a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i></a>
    </div>
  </div>
</nav>

<div class="container py-4">
<div class="row justify-content-center"><div class="col-lg-8">

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 m-0"><i class="fas fa-user text-primary"></i> Détails du client</h1>
  <div class="d-flex gap-2">
    <a href="edit_client.php?id=<?=$c['id']?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
    <a href="delete_client.php?id=<?=$c['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression de ce client ?');"><i class="fas fa-trash"></i></a>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white d-flex justify-content-between">
    <span><i class="fas fa-id-badge"></i> Client #<?=htmlspecialchars($c['id'])?></span>
    <small>Créé le : <?=htmlspecialchars(date('d/m/Y H:i',strtotime($c['created_at']??'now')))?></small>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <h5 class="card-title mb-1"><i class="fas fa-user-circle text-primary"></i> <?=htmlspecialchars($c['full_name'])?></h5>
        <p class="text-muted mb-0">CIN : <strong><?=htmlspecialchars($c['cin'])?></strong></p>
      </div>
      <div class="col-md-6 text-md-end">
        <?php if(!empty($c['telephone'])):?><p class="mb-1"><i class="fas fa-phone text-primary"></i> <?=htmlspecialchars($c['telephone'])?></p><?php endif;?>
        <?php if(!empty($c['email'])):?><p class="mb-0"><i class="fas fa-envelope text-primary"></i> <?=htmlspecialchars($c['email'])?></p><?php endif;?>
      </div>
    </div>
    <?php if(!empty($c['adress'])):?>
      <hr><p class="mb-0"><i class="fas fa-map-marker-alt text-primary"></i> <?=nl2br(htmlspecialchars($c['adress']))?></p>
    <?php endif;?>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="fas fa-credit-card text-primary"></i> Comptes du client</span>
    <a href="../accounts/add_account.php?client_id=<?=$c['id']?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Nouveau compte</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>#</th><th>Numéro</th><th>Type</th><th>Statut</th><th>Solde</th><th>Créé le</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if(count($accs)>0):foreach($accs as $i=>$a):?>
          <tr>
            <td><?=$i+1?></td>
            <td><code><?=htmlspecialchars($a['account_number'])?></code></td>
            <td><?=htmlspecialchars($a['type'])?></td>
            <td>
              <?php if($a['status']==='active'):?><span class="badge bg-success">Actif</span>
              <?php elseif($a['status']==='inactive'):?><span class="badge bg-secondary">Inactif</span>
              <?php else:?><span class="badge bg-danger">Bloqué</span><?php endif;?>
            </td>
            <td><?=htmlspecialchars(number_format($a['balance'],2,'.',' '))?> DH</td>
            <td><small class="text-muted"><?=htmlspecialchars($a['created_at'])?></small></td>
            <td class="d-flex flex-wrap gap-1">
              <a href="../accounts/edit_account.php?id=<?=$a['id']?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
              <a href="../accounts/delete_account.php?id=<?=$a['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce compte ?');"><i class="fas fa-trash"></i></a>
              <a href="../transactions/list_transactions.php?account_id=<?=$a['id']?>" class="btn btn-sm btn-info"><i class="fas fa-receipt"></i></a>
            </td>
          </tr>
        <?php endforeach;else:?>
          <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-inbox fa-3x mb-2 d-block"></i>Aucun compte trouvé</td></tr>
        <?php endif;?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>

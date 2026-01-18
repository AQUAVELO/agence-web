<?php
/**
 * Page de traitement de l'annulation de RDV
 */

require '_settings.php';

$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$rdv = isset($_GET['rdv']) ? htmlspecialchars($_GET['rdv']) : '';
$city = isset($_GET['city']) ? htmlspecialchars($_GET['city']) : '';

if ($email && $rdv) {
    // 1. Suppression de la réservation dans am_free
    // On cherche l'entrée qui contient l'email et le RDV spécifique dans le nom
    $search_rdv = "%" . $rdv . "%";
    $del = $database->prepare("DELETE FROM am_free WHERE email = ? AND name LIKE ?");
    $del->execute([$email, $search_rdv]);
    
    $success = true;
} else {
    $success = false;
}
?>

<section class="content-area bg1" style="padding: 100px 0;">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
      
      <?php if ($success) : ?>
        <div style="font-size: 4rem; color: #ff9800; margin-bottom: 20px;">
          <i class="fa fa-calendar-times-o"></i>
        </div>
        <h2 style="color: #333; margin-bottom: 20px;">Votre rendez-vous a été annulé</h2>
        <p style="font-size: 1.1rem; color: #666; margin-bottom: 30px;">
            Le créneau du <b><?= htmlspecialchars($rdv) ?></b> a bien été libéré dans notre planning.
        </p>
        <a href="index.php?p=page&city=<?= urlencode($city) ?>" class="btn btn-primary" style="padding: 15px 40px; border-radius: 50px; font-weight: bold; background: #00a8cc; border: none; color: white; text-decoration: none;">
          RETOUR AU CENTRE
        </a>
      <?php else : ?>
        <h2 style="color: #d9534f;">Erreur lors de l'annulation</h2>
        <p>Nous n'avons pas pu identifier votre rendez-vous. Merci de nous contacter par téléphone.</p>
        <a href="index.php" class="btn btn-default">Retour à l'accueil</a>
      <?php endif; ?>

    </div>
  </div>
</section>

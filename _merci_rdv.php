<?php
// On récupère les infos
$rdv_brut = isset($_GET['rdv']) ? htmlspecialchars($_GET['rdv']) : '';
$nom_rdv = isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : '';
$email_rdv = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$phone_rdv = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';
$city_rdv = isset($_GET['city']) ? htmlspecialchars($_GET['city']) : 'Cannes';

// URLs dynamiques
$url_annuler = "index.php?p=annulation&email=" . urlencode($email_rdv) . "&rdv=" . urlencode($rdv_brut) . "&city=" . urlencode($city_rdv);
$url_modifier = "index.php?p=calendrier_cannes&center=305&nom=" . urlencode($nom_rdv) . "&email=" . urlencode($email_rdv) . "&phone=" . urlencode($phone_rdv) . "&old_rdv=" . urlencode($rdv_brut);

// Formatage affichage
$rdv_display = strpos($rdv_brut, '(') !== false ? str_replace(['(', ')'], ['pour une séance ', ''], $rdv_brut) : $rdv_brut . " pour une séance AQUAVELO";
?>

<section class="content-area bg1" style="padding: 100px 0;">
  <div class="container">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 50px 40px; border-radius: 30px; box-shadow: 0 15px 50px rgba(0,0,0,0.15); text-align: center; border: 1px solid #eee;">
      
      <div style="font-size: 5rem; color: #4CAF50; margin-bottom: 20px;"><i class="fa fa-check-circle"></i></div>
      <h1 style="color: #333; margin-bottom: 30px; font-weight: 800; font-size: 2.2rem;">Votre séance est réservée !</h1>
      
      <div style="background: #f0faff; padding: 30px; border-radius: 20px; margin-bottom: 35px; border: 2px dashed #00a8cc;">
        <p style="font-size: 1.1rem; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Récapitulatif de votre rendez-vous :</p>
        <p style="font-size: 1.8rem; color: #00a8cc; font-weight: 800; line-height: 1.4; margin: 0;">le <?= $rdv_display ?></p>
      </div>

      <div style="background: #fff3cd; padding: 20px; border-radius: 15px; border-left: 8px solid #ffc107; margin-bottom: 35px; text-align: left;">
        <p style="margin: 0; color: #856404; font-size: 1.3rem; font-weight: bold;"><i class="fa fa-clock-o"></i> IMPORTANT : Arriver 15 minutes avant le cours.</p>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; margin-bottom: 40px;">
        <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; border-left: 5px solid #00a8cc;">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #333;"><i class="fa fa-map-marker"></i> Lieu</h3>
            <p style="margin-bottom: 5px;"><strong>Aquavelo Cannes</strong></p>
            <p style="font-size: 0.95rem; color: #666; margin: 0;">60 avenue du Docteur Raymond Picaud, Cannes</p>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; border-left: 5px solid #00a8cc;">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #333;"><i class="fa fa-phone"></i> Contact</h3>
            <p style="margin-bottom: 5px;"><strong>Besoin d'aide ?</strong></p>
            <p style="font-size: 1.2rem; color: #00a8cc; font-weight: bold; margin: 0;">04 93 93 05 65</p>
        </div>
      </div>

      <div style="background: #fdfdfd; padding: 25px; border-radius: 15px; text-align: left; margin-bottom: 40px; border: 1px solid #eee;">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: #333; margin-bottom: 15px;">🎒 N'oubliez pas de venir équipé(e) avec :</h3>
        <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
            <div style="font-size: 0.95rem; color: #555;">✅ Votre maillot de bain,</div>
            <div style="font-size: 0.95rem; color: #555;">✅ Une serviette,</div>
            <div style="font-size: 0.95rem; color: #555;">✅ Un gel douche,</div>
            <div style="font-size: 0.95rem; color: #555;">✅ Une bouteille d'eau,</div>
            <div style="font-size: 0.95rem; color: #555;">✅ Des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas)</div>
        </div>
      </div>

      <div style="margin-top: 30px; border-top: 2px solid #f5f5f5; padding-top: 30px;">
          <p style="font-weight: bold; color: #999; text-transform: uppercase; font-size: 0.8rem; margin-bottom: 15px;">Gestion de votre événement :</p>
          <div style="display: flex; gap: 15px; justify-content: center;">
              <a href="<?= $url_modifier ?>" class="btn btn-default" style="padding: 12px 30px; border: 2px solid #ddd; border-radius: 50px; color: #555; font-weight: bold;">Replanifier</a>
              <a href="<?= $url_annuler ?>" class="btn btn-default" style="padding: 12px 30px; border: 2px solid #ddd; border-radius: 50px; color: #555; font-weight: bold;">Annuler</a>
          </div>
      </div>

      <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
        <a href="index.php?p=page&city=<?= urlencode($city_rdv) ?>" class="btn btn-primary" style="padding: 15px 40px; border-radius: 50px; font-weight: bold; background: #00a8cc; border: none; font-size: 1.2rem; color: white; text-decoration: none;">RETOUR À L'ACCUEIL</a>
      </div>
    </div>
  </div>
</section>

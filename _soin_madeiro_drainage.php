<?php
/**
 * Réservation — Soin madérothérapie ou drainage lymphatique
 * Journée événement : lundi 4 mai 2026, créneaux horaires 9h–14h — 90 €
 */
require_once '_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

const SOIN_EVENT_ISO = '2026-05-04';
const SOIN_EVENT_LABEL_FR = 'lundi 4 mai 2026';
const SOIN_PRIX_EUROS = 90;
/** am_free.segment_id — hors planning séance d’essai / crons / synchro Google */
const SMD_AMFREE_SEGMENT = 'soin-madeiro-drainage';

$creneaux_horaires = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00'];

$smd_success = false;
$smd_error = '';

/** Notifications centre (même logique que cryo + contact site) */
$smd_admin_recipients = ['aqua.cannes@gmail.com', 'claude@alesiaminceur.com'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['smd_submit'])) {

    $prenom = htmlspecialchars(trim($_POST['smd_prenom'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nom = htmlspecialchars(trim($_POST['smd_nom'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['smd_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['smd_telephone'] ?? ''), ENT_QUOTES, 'UTF-8');
    $type_soin = htmlspecialchars(trim($_POST['smd_type_soin'] ?? ''), ENT_QUOTES, 'UTF-8');
    $creneau = trim($_POST['smd_creneau'] ?? '');
    $message = htmlspecialchars(trim($_POST['smd_message'] ?? ''), ENT_QUOTES, 'UTF-8');

    $types_valides = ['Madérothérapie', 'Drainage lymphatique'];
    if (!in_array($type_soin, $types_valides, true)) {
        $type_soin = '';
    }

    if (!in_array($creneau, $creneaux_horaires, true)) {
        $creneau = '';
    }

    if (empty($prenom) || empty($nom) || empty($email) || empty($telephone) || empty($type_soin) || empty($creneau)) {
        $smd_error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $smd_error = 'Veuillez entrer une adresse email valide.';
    } else {
        $hp = explode(':', $creneau);
        $creneau_label = (isset($hp[0], $hp[1]) ? ((int) $hp[0]) . 'h' . $hp[1] : $creneau);

        $smd_db_ok = false;
        try {
            $reference = 'SMD' . date('dmHis') . bin2hex(random_bytes(2));
            $name_db = $prenom . ' ' . $nom . ' — ' . $type_soin . ' — créneau ' . $creneau_label . ' — ' . SOIN_EVENT_LABEL_FR;
            $ins = $database->prepare(
                'INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $ins->execute([$reference, 305, 3, $name_db, $email, $telephone, SMD_AMFREE_SEGMENT]);
            $smd_db_ok = true;
        } catch (Throwable $e) {
            error_log('SMD insert am_free : ' . $e->getMessage());
            $smd_error = 'Enregistrement impossible pour le moment. Merci de réessayer ou de nous appeler au 04 93 93 05 65.';
        }

        if ($smd_db_ok) {
            try {
            $mailCentre = new PHPMailer(true);
            $mailCentre->isSMTP();
            $mailCentre->Host = $settings['mjhost'];
            $mailCentre->SMTPAuth = true;
            $mailCentre->Username = $settings['mjusername'];
            $mailCentre->Password = $settings['mjpassword'];
            $mailCentre->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailCentre->Port = 587;
            $mailCentre->CharSet = 'UTF-8';

            $mailCentre->setFrom('contact@aquavelo.com', 'Aquavelo Cannes — Soins');
            foreach ($smd_admin_recipients as $addr) {
                $mailCentre->addAddress($addr);
            }
            $mailCentre->addReplyTo($email, $prenom . ' ' . $nom);

            $mailCentre->isHTML(true);
            $mailCentre->Subject = '📅 Nouvelle réservation — ' . $type_soin . ' — ' . SOIN_EVENT_LABEL_FR . ' ' . $creneau_label;
            $mailCentre->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background-color: #e0f7fa; padding: 30px; border-radius: 15px 15px 0 0; text-align: center; border: 2px solid #00a8cc;'>
                        <h1 style='color: #00a8cc; margin: 0; font-size: 22px;'>Nouvelle réservation soin</h1>
                        <p style='color: #00a8cc; margin-top: 10px; font-weight: bold;'>" . htmlspecialchars($type_soin, ENT_QUOTES, 'UTF-8') . " — " . SOIN_PRIX_EUROS . " €</p>
                    </div>
                    <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 15px 15px;'>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Prénom</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$prenom}</td></tr>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Nom</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$nom}</td></tr>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Email</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'><a href='mailto:{$email}'>{$email}</a></td></tr>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Téléphone</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'><a href='tel:{$telephone}'>{$telephone}</a></td></tr>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Soin</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$type_soin}</td></tr>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Date</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>" . SOIN_EVENT_LABEL_FR . ' (' . SOIN_EVENT_ISO . ")</td></tr>
                            <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Créneau</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$creneau_label}</td></tr>
                            " . (!empty($message) ? "<tr><td style='padding: 10px;'><strong>Message</strong></td><td style='padding: 10px;'>{$message}</td></tr>" : '') . "
                        </table>
                        <p style='margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; color: #856404;'><strong>Action :</strong> confirmer le RDV et le paiement à " . SOIN_PRIX_EUROS . " €.</p>
                    </div>
                </div>";
            $mailCentre->AltBody = "Réservation {$type_soin}\n{$prenom} {$nom}\n{$email}\n{$telephone}\nDate : " . SOIN_EVENT_LABEL_FR . "\nHeure : {$creneau}\nMessage : {$message}\n";

            $mailCentre->send();
        } catch (Exception $e) {
            error_log('Erreur envoi email centre soin madéro/drainage : ' . $e->getMessage());
        }

        try {
            $mailProspect = new PHPMailer(true);
            $mailProspect->isSMTP();
            $mailProspect->Host = $settings['mjhost'];
            $mailProspect->SMTPAuth = true;
            $mailProspect->Username = $settings['mjusername'];
            $mailProspect->Password = $settings['mjpassword'];
            $mailProspect->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailProspect->Port = 587;
            $mailProspect->CharSet = 'UTF-8';

            $mailProspect->setFrom('noreply@aquavelo.com', 'Aquavelo Cannes');
            $mailProspect->addAddress($email, $prenom . ' ' . $nom);
            $mailProspect->addReplyTo('aqua.cannes@gmail.com', 'Aquavelo Cannes');

            $mailProspect->isHTML(true);
            $mailProspect->Subject = '✅ Votre demande de RDV — ' . $type_soin . ' — Aquavelo Cannes';
            $mailProspect->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background-color: #e0f7fa; padding: 15px; border-radius: 10px; text-align: center; border: 1px solid #00a8cc;'>
                        <p style='color: #00a8cc; margin: 0; font-size: 16px; font-weight: bold;'>Merci {$prenom}, nous avons bien reçu votre demande !</p>
                    </div>
                    <div style='padding: 24px 0;'>
                        <h2 style='color: #00a8cc;'>Récapitulatif</h2>
                        <div style='background: #f8f9fa; padding: 20px; border-radius: 10px;'>
                            <p><strong>Soin :</strong> {$type_soin}</p>
                            <p><strong>Date :</strong> " . SOIN_EVENT_LABEL_FR . "</p>
                            <p><strong>Heure choisie :</strong> {$creneau_label}</p>
                            <p><strong>Tarif annoncé :</strong> " . SOIN_PRIX_EUROS . " €</p>
                        </div>
                        <p style='color: #155724; margin-top: 20px;'>Notre équipe vous recontacte rapidement pour confirmer votre rendez-vous.</p>
                    </div>
                    <div style='text-align: center; border-top: 1px solid #eee; padding-top: 16px;'>
                        <a href='tel:0493930565' style='color: #00a8cc; font-weight: bold;'>04 93 93 05 65</a>
                        <p style='color: #999; font-size: 0.85rem;'>Aquavelo Cannes — 60 avenue du Docteur Picaud, 06150 Cannes</p>
                    </div>
                </div>";
            $mailProspect->AltBody = "Bonjour {$prenom},\n\nNous avons bien enregistré votre demande pour {$type_soin} le " . SOIN_EVENT_LABEL_FR . " à {$creneau_label} (" . SOIN_PRIX_EUROS . " €).\n\nL'équipe Aquavelo Cannes vous recontacte très vite.\n";

            $mailProspect->send();
        } catch (Exception $e) {
            error_log('Erreur envoi email prospect soin madéro/drainage : ' . $e->getMessage());
        }

            $smd_success = true;
        }
    }
}

$form_action = BASE_PATH . 'soin-madeiro-drainage#smd-booking';
?>
<style>
:root {
    --cryo-primary: #00d4ff;
    --cryo-primary-dark: #00a8cc;
    --cryo-secondary: #ff6b6b;
    --cryo-accent: #ffd93d;
    --cryo-dark: #1a1a2e;
    --cryo-gray: #666;
    --cryo-light: #f8f9fa;
}
.cryo-hero {
    min-height: 70vh;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.88) 0%, rgba(0, 168, 204, 0.92) 100%),
        url('<?= BASE_PATH ?>images/Cannes1.jpg') center/cover no-repeat;
    display: flex;
    align-items: center;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}
.cryo-hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
    position: relative;
    z-index: 1;
}
.cryo-hero-content { color: white; }
.cryo-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 20px;
}
.cryo-hero h1 {
    font-size: 2.4rem;
    font-weight: 800;
    margin-bottom: 16px;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    color: white;
}
.cryo-hero h1 span {
    display: block;
    font-size: 1.2rem;
    font-weight: 500;
    opacity: 0.95;
    margin-top: 10px;
    line-height: 1.5;
}
.cryo-hero-description { font-size: 1.05rem; margin-bottom: 24px; opacity: 0.95; line-height: 1.7; }
.cryo-price-box {
    background: white;
    border-radius: 20px;
    padding: 22px 28px;
    display: inline-block;
    margin-bottom: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.cryo-price-new { font-size: 3.2rem; font-weight: 800; color: var(--cryo-primary-dark); }
.cryo-price-new sup { font-size: 1.4rem; vertical-align: super; }
.cryo-price-label { display: block; font-size: 0.95rem; color: var(--cryo-gray); margin-top: 6px; }
.cryo-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: white;
    color: var(--cryo-primary-dark);
    padding: 16px 32px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    text-decoration: none;
}
.cryo-cta-btn:hover { transform: translateY(-2px); color: var(--cryo-primary-dark); text-decoration: none; }
.cryo-hero-image img {
    border-radius: 24px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
    width: 100%;
}
.cryo-section { padding: 70px 0; }
.cryo-section-container { max-width: 1100px; margin: 0 auto; padding: 0 20px; }
.cryo-section-header { text-align: center; margin-bottom: 40px; }
.cryo-section-subtitle {
    color: var(--cryo-primary-dark);
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 12px;
}
.cryo-section-title { font-size: 2.1rem; color: var(--cryo-dark); margin-bottom: 12px; }
.cryo-section-description { font-size: 1.05rem; color: var(--cryo-gray); max-width: 680px; margin: 0 auto; }
.cryo-benefits { background: white; }
.cryo-benefits-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
.cryo-benefit-card {
    background: var(--cryo-light);
    padding: 28px 20px;
    border-radius: 18px;
    text-align: center;
    border: 2px solid transparent;
}
.cryo-benefit-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.cryo-benefit-icon i { font-size: 1.6rem; color: white; }
.cryo-benefit-card h3 { font-size: 1.05rem; color: var(--cryo-dark); margin-bottom: 10px; }
.cryo-benefit-card p { color: var(--cryo-gray); font-size: 0.88rem; line-height: 1.55; margin: 0; }
.cryo-steps { background: var(--cryo-light); }
.cryo-steps-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
.cryo-step-card { text-align: center; }
.cryo-step-number {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem; font-weight: 700; color: white;
    margin: 0 auto 16px;
}
.cryo-step-card h3 { font-size: 1.15rem; color: var(--cryo-dark); margin-bottom: 10px; }
.cryo-step-card p { color: var(--cryo-gray); font-size: 0.95rem; margin: 0; }
.cryo-pricing {
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    padding: 70px 0;
}
.cryo-pricing-container { max-width: 720px; margin: 0 auto; padding: 0 20px; }
.cryo-pricing-card {
    background: white;
    border-radius: 28px;
    padding: 40px 36px;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.2);
    text-align: center;
}
.cryo-pricing-title { font-size: 1.75rem; color: var(--cryo-dark); margin-bottom: 8px; }
.cryo-pricing-subtitle { color: var(--cryo-gray); margin-bottom: 20px; }
.cryo-pricing-amount .new { font-size: 3.2rem; font-weight: 800; color: var(--cryo-primary-dark); }
.cryo-pricing-amount .new sup { font-size: 1.4rem; vertical-align: super; }
.cryo-pricing-features { list-style: none; margin: 0 0 24px 0; padding: 0; text-align: left; display: inline-block; }
.cryo-pricing-features li {
    padding: 8px 0;
    font-size: 0.98rem;
    color: var(--cryo-gray);
    display: flex;
    align-items: center;
    gap: 10px;
}
.cryo-pricing-features li i { color: var(--cryo-primary); }
.cryo-form-section { background: var(--cryo-light); padding: 70px 0; }
.cryo-form-container { max-width: 700px; margin: 0 auto; padding: 0 20px; }
.cryo-form-card {
    background: white;
    padding: 44px 36px;
    border-radius: 28px;
    box-shadow: 0 16px 50px rgba(0, 0, 0, 0.08);
}
.cryo-form-header { text-align: center; margin-bottom: 28px; }
.cryo-form-header h2 { font-size: 1.65rem; color: var(--cryo-dark); margin-bottom: 8px; }
.cryo-form-header p { color: var(--cryo-gray); margin: 0; }
.cryo-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.cryo-form-group { margin-bottom: 6px; }
.cryo-form-group.full-width { grid-column: span 2; }
.cryo-form-group label { display: block; font-weight: 600; color: var(--cryo-dark); margin-bottom: 6px; font-size: 0.92rem; }
.cryo-form-group label span { color: var(--cryo-secondary); }
.cryo-form-group input, .cryo-form-group select, .cryo-form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    background: var(--cryo-light);
}
.cryo-form-group input:focus, .cryo-form-group select:focus, .cryo-form-group textarea:focus {
    outline: none;
    border-color: var(--cryo-primary);
    background: white;
}
.cryo-form-group .error-msg { color: var(--cryo-secondary); font-size: 0.82rem; margin-top: 4px; display: none; }
.cryo-form-group.has-error input, .cryo-form-group.has-error select { border-color: var(--cryo-secondary); }
.cryo-form-group.has-error .error-msg { display: block; }
.cryo-form-submit {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.cryo-form-submit:disabled { opacity: 0.7; cursor: not-allowed; }
.cryo-form-note { text-align: center; margin-top: 16px; color: var(--cryo-gray); font-size: 0.88rem; }
.cryo-confirmation { display: none; text-align: center; padding: 32px 16px; }
.cryo-confirmation.show { display: block; }
.cryo-confirmation i.fa-check-circle { font-size: 4rem; color: var(--cryo-primary); margin-bottom: 20px; }
.cryo-confirmation h3 { font-size: 1.55rem; color: var(--cryo-dark); margin-bottom: 12px; }
.cryo-confirmation p { color: var(--cryo-gray); font-size: 1.02rem; }
@media (max-width: 1024px) {
    .cryo-hero-container { grid-template-columns: 1fr; text-align: center; }
    .cryo-hero-image { display: none; }
    .cryo-benefits-grid { grid-template-columns: repeat(2, 1fr); }
    .cryo-steps-container { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .cryo-hero { padding: 50px 0; min-height: auto; }
    .cryo-hero h1 { font-size: 1.75rem; }
    .cryo-benefits-grid { grid-template-columns: 1fr; }
    .cryo-form-grid { grid-template-columns: 1fr; }
    .cryo-form-group.full-width { grid-column: span 1; }
    .cryo-form-card { padding: 28px 20px; }
}
</style>

<section class="cryo-hero">
    <div class="cryo-hero-container">
        <div class="cryo-hero-content">
            <div class="cryo-badge">
                <i class="fa fa-calendar"></i> Cannes — <?= htmlspecialchars(SOIN_EVENT_LABEL_FR, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <h1>
                Madérothérapie &amp; drainage lymphatique
                <span>Soin au choix — journée spéciale <?= htmlspecialchars(SOIN_EVENT_LABEL_FR, ENT_QUOTES, 'UTF-8'); ?> — créneaux d’une heure de 9h à 14h</span>
            </h1>
            <p class="cryo-hero-description">
                Retrouvez confort, circulation et sensation de légèreté grâce à un protocole manuel encadré par nos praticiennes.
                Choisissez votre créneau horaire ci-dessous.
            </p>
            <div class="cryo-price-box">
                <span class="cryo-price-new"><?= SOIN_PRIX_EUROS; ?><sup>€</sup></span>
                <span class="cryo-price-label">la séance (tarif journée spéciale)</span>
            </div>
            <a href="#smd-booking" class="cryo-cta-btn">
                <i class="fa fa-calendar-check-o"></i>
                Choisir mon créneau
            </a>
        </div>
        <div class="cryo-hero-image">
            <img src="<?= BASE_PATH ?>images/Cannes1.jpg" alt="Centre Aquavelo Cannes">
        </div>
    </div>
</section>

<section class="cryo-section cryo-benefits">
    <div class="cryo-section-container">
        <div class="cryo-section-header">
            <span class="cryo-section-subtitle">Les bienfaits</span>
            <h2 class="cryo-section-title">Pourquoi ces soins ?</h2>
            <p class="cryo-section-description">Deux approches complémentaires pour soutenir la silhouette, la circulation et la récupération.</p>
        </div>
        <div class="cryo-benefits-grid">
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon"><i class="fa fa-leaf"></i></div>
                <h3>Madérothérapie</h3>
                <p>Technique manuelle issue du bois de madrier pour cibler la cellulite et relancer les tissus en douceur.</p>
            </div>
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon"><i class="fa fa-tint"></i></div>
                <h3>Drainage lymphatique</h3>
                <p>Mouvements lents et précis pour favoriser l’élimination des tensions et la sensation de jambes légères.</p>
            </div>
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon"><i class="fa fa-clock-o"></i></div>
                <h3>Créneaux clairs</h3>
                <p>Un rendez-vous par heure entre 9h et 14h le <?= htmlspecialchars(SOIN_EVENT_LABEL_FR, ENT_QUOTES, 'UTF-8'); ?>.</p>
            </div>
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon"><i class="fa fa-map-marker"></i></div>
                <h3>Aquavelo Cannes</h3>
                <p>60 avenue du Docteur Picaud, 06150 Cannes — accueil et suivi personnalisé.</p>
            </div>
        </div>
    </div>
</section>

<section class="cryo-section cryo-steps">
    <div class="cryo-section-container">
        <div class="cryo-section-header">
            <span class="cryo-section-subtitle">Déroulé</span>
            <h2 class="cryo-section-title">Comment ça se passe ?</h2>
        </div>
        <div class="cryo-steps-container">
            <div class="cryo-step-card">
                <div class="cryo-step-number">1</div>
                <h3>Réservation</h3>
                <p>Vous choisissez le type de soin et votre heure sur ce formulaire.</p>
            </div>
            <div class="cryo-step-card">
                <div class="cryo-step-number">2</div>
                <h3>Confirmation</h3>
                <p>Nous vous rappelons pour valider le RDV et le règlement à <?= SOIN_PRIX_EUROS; ?> €.</p>
            </div>
            <div class="cryo-step-card">
                <div class="cryo-step-number">3</div>
                <h3>Séance</h3>
                <p>Installez-vous confortablement : votre praticienne adapte la pression à votre ressenti.</p>
            </div>
        </div>
    </div>
</section>

<section class="cryo-pricing">
    <div class="cryo-pricing-container">
        <div class="cryo-pricing-card">
            <h2 class="cryo-pricing-title">Journée spéciale <?= htmlspecialchars(SOIN_EVENT_LABEL_FR, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="cryo-pricing-subtitle">Madérothérapie <strong>ou</strong> drainage lymphatique</p>
            <div class="cryo-pricing-amount">
                <span class="new"><?= SOIN_PRIX_EUROS; ?><sup>€</sup></span>
            </div>
            <ul class="cryo-pricing-features">
                <li><i class="fa fa-check-circle"></i> Soin au choix lors de la réservation</li>
                <li><i class="fa fa-check-circle"></i> Créneaux d’une heure de 9h à 14h</li>
                <li><i class="fa fa-check-circle"></i> Centre Aquavelo Cannes</li>
            </ul>
            <a href="#smd-booking" class="cryo-cta-btn" style="width: 100%; justify-content: center;">
                <i class="fa fa-calendar-check-o"></i>
                Réserver à <?= SOIN_PRIX_EUROS; ?> €
            </a>
        </div>
    </div>
</section>

<section class="cryo-form-section" id="smd-booking">
    <div class="cryo-form-container">
        <div class="cryo-form-card">
            <div class="cryo-form-header">
                <h2><i class="fa fa-calendar" style="color: var(--cryo-primary);"></i> Réservez votre créneau</h2>
                <p><?= htmlspecialchars(SOIN_EVENT_LABEL_FR, ENT_QUOTES, 'UTF-8'); ?> — <?= SOIN_PRIX_EUROS; ?> € la séance</p>
            </div>

            <?php if (!$smd_success): ?>
            <form id="smdBookingForm" method="post" action="<?= htmlspecialchars($form_action, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="smd_submit" value="1">

                <?php if ($smd_error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 14px; border-radius: 10px; margin-bottom: 18px; border: 1px solid #f5c6cb;">
                    <i class="fa fa-exclamation-triangle"></i> <?= $smd_error ?>
                </div>
                <?php endif; ?>

                <div class="cryo-form-grid">
                    <div class="cryo-form-group full-width">
                        <label for="smd_type_soin">Type de soin <span>*</span></label>
                        <select id="smd_type_soin" name="smd_type_soin" required>
                            <option value="">Choisissez votre soin</option>
                            <option value="Madérothérapie">Madérothérapie</option>
                            <option value="Drainage lymphatique">Drainage lymphatique</option>
                        </select>
                        <span class="error-msg">Veuillez sélectionner un soin</span>
                    </div>
                    <div class="cryo-form-group full-width">
                        <label for="smd_creneau">Heure du rendez-vous <span>*</span></label>
                        <select id="smd_creneau" name="smd_creneau" required>
                            <option value="">Choisissez une heure</option>
                            <?php foreach ($creneaux_horaires as $h): ?>
                            <?php
                                $hp = explode(':', $h);
                                $label = ((int) ($hp[0] ?? 0)) . 'h' . ($hp[1] ?? '00');
                            ?>
                            <option value="<?= htmlspecialchars($h, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-msg">Veuillez choisir un créneau</span>
                    </div>
                    <div class="cryo-form-group">
                        <label for="smd_prenom">Prénom <span>*</span></label>
                        <input type="text" id="smd_prenom" name="smd_prenom" placeholder="Votre prénom" required>
                        <span class="error-msg">Veuillez entrer votre prénom</span>
                    </div>
                    <div class="cryo-form-group">
                        <label for="smd_nom">Nom <span>*</span></label>
                        <input type="text" id="smd_nom" name="smd_nom" placeholder="Votre nom" required>
                        <span class="error-msg">Veuillez entrer votre nom</span>
                    </div>
                    <div class="cryo-form-group">
                        <label for="smd_email">Email <span>*</span></label>
                        <input type="email" id="smd_email" name="smd_email" placeholder="votre@email.com" required>
                        <span class="error-msg">Veuillez entrer un email valide</span>
                    </div>
                    <div class="cryo-form-group">
                        <label for="smd_telephone">Téléphone <span>*</span></label>
                        <input type="tel" id="smd_telephone" name="smd_telephone" placeholder="06 12 34 56 78" required>
                        <span class="error-msg">Veuillez entrer votre téléphone</span>
                    </div>
                    <div class="cryo-form-group full-width">
                        <label for="smd_message">Message (optionnel)</label>
                        <textarea id="smd_message" name="smd_message" rows="3" placeholder="Précisions ou questions…"></textarea>
                    </div>
                </div>

                <button type="submit" class="cryo-form-submit" id="smdSubmitBtn">
                    <i class="fa fa-calendar-check-o"></i>
                    Confirmer ma demande à <?= SOIN_PRIX_EUROS; ?> €
                </button>
                <p class="cryo-form-note">
                    <i class="fa fa-shield"></i>
                    Vous recevrez un email de confirmation ; notre équipe vous rappelle pour finaliser le RDV et le paiement.
                </p>
            </form>
            <?php endif; ?>

            <div class="cryo-confirmation <?= $smd_success ? 'show' : '' ?>" id="smdConfirmation">
                <i class="fa fa-check-circle"></i>
                <h3>Merci pour votre demande !</h3>
                <p>Nous avons bien enregistré votre créneau pour le <?= htmlspecialchars(SOIN_EVENT_LABEL_FR, ENT_QUOTES, 'UTF-8'); ?>.</p>
                <p style="margin-top: 12px; font-weight: 600; color: var(--cryo-primary-dark);">Un email de confirmation vous a été envoyé.</p>
                <div style="margin-top: 24px;">
                    <a href="<?= BASE_PATH ?>centres/Cannes" style="display: inline-block; background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-weight: 600;">
                        <i class="fa fa-home"></i> Retour au centre de Cannes
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('smdBookingForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        var ok = true;
        document.querySelectorAll('#smdBookingForm .cryo-form-group').forEach(function(g) { g.classList.remove('has-error'); });
        ['smd_prenom', 'smd_nom', 'smd_email', 'smd_telephone', 'smd_type_soin', 'smd_creneau'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && !String(el.value).trim()) {
                if (el.closest('.cryo-form-group')) el.closest('.cryo-form-group').classList.add('has-error');
                ok = false;
            }
        });
        var em = document.getElementById('smd_email');
        if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value.trim())) {
            if (em.closest('.cryo-form-group')) em.closest('.cryo-form-group').classList.add('has-error');
            ok = false;
        }
        var ph = document.getElementById('smd_telephone');
        if (ph && !/^[\d\s\-\+\(\)]{10,}$/.test(ph.value.trim())) {
            if (ph.closest('.cryo-form-group')) ph.closest('.cryo-form-group').classList.add('has-error');
            ok = false;
        }
        if (!ok) {
            e.preventDefault();
            return false;
        }
        var btn = document.getElementById('smdSubmitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi en cours…';
        }
    });
});
</script>

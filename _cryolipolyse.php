<?php
// ========== TRAITEMENT DU FORMULAIRE CRYOLIPOLYSE ==========
require_once '_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$cryo_success = false;
$cryo_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cryo_submit'])) {
    
    // Récupération des données
    $prenom = htmlspecialchars(trim($_POST['cryo_prenom'] ?? ''));
    $nom = htmlspecialchars(trim($_POST['cryo_nom'] ?? ''));
    $email = filter_var(trim($_POST['cryo_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['cryo_telephone'] ?? ''));
    $zone = htmlspecialchars(trim($_POST['cryo_zone'] ?? ''));
    $horaire = htmlspecialchars(trim($_POST['cryo_horaire'] ?? ''));
    $message = htmlspecialchars(trim($_POST['cryo_message'] ?? ''));
    
    // Validation
    if (empty($prenom) || empty($nom) || empty($email) || empty($telephone) || empty($zone) || empty($horaire)) {
        $cryo_error = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $cryo_error = "Veuillez entrer une adresse email valide.";
    } else {
        try {
            // ========== EMAIL 1 : Notification au centre ==========
            $mailCentre = new PHPMailer(true);
            $mailCentre->isSMTP();
            $mailCentre->Host = $settings['mjhost'];
            $mailCentre->SMTPAuth = true;
            $mailCentre->Username = $settings['mjusername'];
            $mailCentre->Password = $settings['mjpassword'];
            $mailCentre->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailCentre->Port = 587;
            $mailCentre->CharSet = 'UTF-8';
            
            $mailCentre->setFrom('contact@aquavelo.com', 'Aquavelo Cryolipolyse');
            $mailCentre->addAddress('aqua.cannes@gmail.com');
            $mailCentre->addReplyTo($email, $prenom . ' ' . $nom);
            
            $mailCentre->isHTML(true);
            $mailCentre->Subject = "🧊 Nouvelle réservation Cryolipolyse - {$prenom} {$nom}";
            $mailCentre->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background-color: #e0f7fa; padding: 30px; border-radius: 15px 15px 0 0; text-align: center; border: 2px solid #00a8cc;'>
                        <h1 style='color: #00a8cc; margin: 0; font-size: 24px;'>🧊 Nouvelle Réservation Cryolipolyse</h1>
                        <p style='color: #00a8cc; margin-top: 10px; font-weight: bold;'>Offre découverte à 99€</p>
                    </div>
                    <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 15px 15px;'>
                        <h2 style='color: #00a8cc; margin-top: 0;'>Informations du prospect</h2>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Prénom :</strong></td>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$prenom}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Nom :</strong></td>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$nom}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Email :</strong></td>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><a href='mailto:{$email}'>{$email}</a></td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Téléphone :</strong></td>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><a href='tel:{$telephone}'>{$telephone}</a></td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Zone à traiter :</strong></td>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$zone}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Horaire préféré :</strong></td>
                                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$horaire}</td>
                            </tr>
                            " . (!empty($message) ? "<tr>
                                <td style='padding: 10px;'><strong>Message :</strong></td>
                                <td style='padding: 10px;'>{$message}</td>
                            </tr>" : "") . "
                        </table>
                        <div style='margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 10px; border-left: 4px solid #ffc107;'>
                            <p style='margin: 0; color: #856404;'><strong>⚡ Action requise :</strong> Contactez ce prospect rapidement pour confirmer son rendez-vous de cryolipolyse.</p>
                        </div>
                    </div>
                </div>
            ";
            $mailCentre->AltBody = "Nouvelle réservation Cryolipolyse\n\nPrénom: {$prenom}\nNom: {$nom}\nEmail: {$email}\nTéléphone: {$telephone}\nZone: {$zone}\nHoraire: {$horaire}\nMessage: {$message}";
            
            $mailCentre->send();
            
        } catch (Exception $e) {
            error_log("Erreur envoi email Centre Cryolipolyse: " . $e->getMessage());
        }
        
        // ========== EMAIL 2 : Confirmation au prospect ==========
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
            $mailProspect->Subject = "✅ Votre demande de séance Cryolipolyse a bien été reçue !";
            $mailProspect->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <img src='https://aquavelo.com/images/content/logo.png' alt='Aquavelo' style='max-width: 150px;'>
                    </div>
                    <div style='background-color: #e0f7fa; padding: 15px; border-radius: 10px; text-align: center; border: 1px solid #00a8cc;'>
                        <p style='color: #00a8cc; margin: 0; font-size: 16px; font-weight: bold;'>🧊 Merci {$prenom}, demande reçue nous allons vous contacter !</p>
                    </div>
                    <div style='padding: 30px 0;'>
                        <h2 style='color: #00a8cc;'>Récapitulatif de votre demande</h2>
                        <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                            <p><strong>🎯 Zone à traiter :</strong> {$zone}</p>
                            <p><strong>🕐 Horaire préféré :</strong> {$horaire}</p>
                            <p><strong>💰 Offre :</strong> Séance découverte à <span style='color: #00a8cc; font-weight: bold;'>99€</span> au lieu de 149€</p>
                        </div>
                        <div style='background: #d4edda; padding: 20px; border-radius: 10px; border-left: 4px solid #28a745;'>
                            <h3 style='color: #155724; margin-top: 0;'>📞 Et maintenant ?</h3>
                            <p style='color: #155724; margin-bottom: 0;'>Notre équipe va vous contacter très rapidement pour confirmer votre rendez-vous et répondre à toutes vos questions.</p>
                        </div>
                    </div>
                    <div style='text-align: center; padding: 20px; border-top: 1px solid #eee;'>
                        <p style='color: #666; font-size: 0.9rem;'>Une question ? Contactez-nous :</p>
                        <p style='margin: 10px 0;'>
                            <a href='tel:0493930565' style='color: #00a8cc; text-decoration: none; font-weight: bold;'>📞 04 93 93 05 65</a>
                        </p>
                        <p style='color: #999; font-size: 0.85rem;'>
                            Aquavelo Cannes - 60 avenue du Docteur Picaud, 06150 Cannes
                        </p>
                    </div>
                </div>
            ";
            $mailProspect->AltBody = "Bonjour {$prenom},\n\nMerci pour votre demande de séance Cryolipolyse !\n\nRécapitulatif :\n- Zone à traiter : {$zone}\n- Horaire préféré : {$horaire}\n- Offre : 99€ au lieu de 149€\n\nNotre équipe va vous contacter très rapidement pour confirmer votre rendez-vous.\n\nÀ très bientôt !\nL'équipe Aquavelo Cannes";
            
            $mailProspect->send();
            
        } catch (Exception $e) {
            error_log("Erreur envoi email Prospect Cryolipolyse: " . $e->getMessage());
            // On ne bloque pas si l'email prospect échoue
        }
        
        // Succès global (au moins l'email centre a été envoyé)
        $cryo_success = true;
    }
}
?>
<!-- 
    Landing Page Cryolipolyse - Offre 99€ au lieu de 149€
    Intégrée au site Aquavelo
-->

<style>
/* ========== STYLES CRYOLIPOLYSE ========== */
:root {
    --cryo-primary: #00d4ff;
    --cryo-primary-dark: #00a8cc;
    --cryo-secondary: #ff6b6b;
    --cryo-accent: #ffd93d;
    --cryo-dark: #1a1a2e;
    --cryo-gray: #666;
    --cryo-light: #f8f9fa;
}

/* Hero Cryo */
.cryo-hero {
    min-height: 80vh;
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.85) 0%, rgba(0, 168, 204, 0.9) 100%),
                url('/images/cryo/cryolipolyse-hero.jpg') center/cover no-repeat;
    display: flex;
    align-items: center;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.cryo-hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 120px;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
    background-size: cover;
}

.cryo-hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 1;
}

.cryo-hero-content {
    color: white;
}

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

.cryo-badge i {
    color: var(--cryo-accent);
    margin-right: 8px;
}

.cryo-hero h1 {
    font-size: 3.2rem;
    font-weight: 800;
    margin-bottom: 20px;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    color: white;
}

.cryo-hero h1 span {
    display: block;
    font-size: 1.5rem;
    font-weight: 400;
    opacity: 0.95;
    margin-top: 10px;
}

.cryo-hero-description {
    font-size: 1.15rem;
    margin-bottom: 30px;
    opacity: 0.95;
    line-height: 1.8;
}

/* Prix promo */
.cryo-price-box {
    background: white;
    border-radius: 20px;
    padding: 25px 30px;
    display: inline-block;
    margin-bottom: 30px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.cryo-price-old {
    font-size: 1.8rem;
    color: #999;
    text-decoration: line-through;
    margin-right: 15px;
}

.cryo-price-new {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--cryo-primary-dark);
}

.cryo-price-new sup {
    font-size: 1.5rem;
    vertical-align: super;
}

.cryo-price-label {
    display: block;
    font-size: 0.95rem;
    color: var(--cryo-gray);
    margin-top: 5px;
}

.cryo-price-savings {
    background: var(--cryo-secondary);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
}

.cryo-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: white;
    color: var(--cryo-primary-dark);
    padding: 18px 40px;
    border-radius: 50px;
    font-size: 1.2rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    text-decoration: none;
}

.cryo-cta-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
    color: var(--cryo-primary-dark);
    text-decoration: none;
}

/* Hero Image */
.cryo-hero-image {
    position: relative;
}

.cryo-hero-image img {
    border-radius: 30px;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
    width: 100%;
}

.cryo-floating-badge {
    position: absolute;
    bottom: 30px;
    left: -20px;
    background: white;
    padding: 20px 25px;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 15px;
}

.cryo-floating-badge i {
    font-size: 2rem;
    color: var(--cryo-primary);
}

.cryo-floating-badge span {
    font-weight: 600;
    color: var(--cryo-dark);
    font-size: 0.95rem;
}

/* Sections communes */
.cryo-section {
    padding: 80px 0;
}

.cryo-section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.cryo-section-header {
    text-align: center;
    margin-bottom: 50px;
}

.cryo-section-subtitle {
    display: inline-block;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 15px;
}

.cryo-section-title {
    font-size: 2.5rem;
    color: var(--cryo-dark);
    margin-bottom: 20px;
}

.cryo-section-description {
    font-size: 1.1rem;
    color: var(--cryo-gray);
    max-width: 700px;
    margin: 0 auto;
}

/* Benefits */
.cryo-benefits {
    background: white;
}

.cryo-benefits-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
}

.cryo-benefit-card {
    background: var(--cryo-light);
    padding: 35px 25px;
    border-radius: 20px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.cryo-benefit-card:hover {
    transform: translateY(-8px);
    background: white;
    border-color: var(--cryo-primary);
    box-shadow: 0 20px 60px rgba(0, 212, 255, 0.15);
}

.cryo-benefit-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.cryo-benefit-icon i {
    font-size: 1.8rem;
    color: white;
}

.cryo-benefit-card h3 {
    font-size: 1.2rem;
    color: var(--cryo-dark);
    margin-bottom: 12px;
}

.cryo-benefit-card p {
    color: var(--cryo-gray);
    font-size: 0.9rem;
    line-height: 1.6;
}

/* How it works */
.cryo-steps {
    background: var(--cryo-light);
}

.cryo-steps-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
}

.cryo-step-card {
    text-align: center;
    position: relative;
}

.cryo-step-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0 auto 20px;
}

.cryo-step-card h3 {
    font-size: 1.3rem;
    color: var(--cryo-dark);
    margin-bottom: 12px;
}

.cryo-step-card p {
    color: var(--cryo-gray);
    font-size: 1rem;
}

/* Testimonials */
.cryo-testimonials {
    background: white;
}

.cryo-testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

.cryo-testimonial-card {
    background: var(--cryo-light);
    padding: 30px;
    border-radius: 20px;
    position: relative;
}

.cryo-testimonial-card::before {
    content: '"';
    font-size: 5rem;
    color: var(--cryo-primary);
    opacity: 0.2;
    position: absolute;
    top: 10px;
    left: 20px;
    font-family: Georgia, serif;
    line-height: 1;
}

.cryo-testimonial-stars {
    color: var(--cryo-accent);
    margin-bottom: 15px;
}

.cryo-testimonial-text {
    font-size: 1rem;
    color: var(--cryo-gray);
    margin-bottom: 20px;
    font-style: italic;
    position: relative;
    z-index: 1;
    line-height: 1.7;
}

.cryo-testimonial-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cryo-testimonial-author img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
}

.cryo-testimonial-author h5 {
    font-size: 0.95rem;
    color: var(--cryo-dark);
    margin: 0 0 3px 0;
}

.cryo-testimonial-author span {
    font-size: 0.85rem;
    color: var(--cryo-gray);
}

/* Pricing CTA */
.cryo-pricing {
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    padding: 80px 0;
    position: relative;
}

.cryo-pricing-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.cryo-pricing-card {
    background: white;
    border-radius: 30px;
    padding: 50px;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.cryo-pricing-badge {
    display: inline-block;
    background: var(--cryo-secondary);
    color: white;
    padding: 10px 25px;
    border-radius: 50px;
    font-weight: 600;
    margin-bottom: 25px;
    animation: cryo-pulse 2s infinite;
}

@keyframes cryo-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.cryo-pricing-title {
    font-size: 2rem;
    color: var(--cryo-dark);
    margin-bottom: 10px;
}

.cryo-pricing-subtitle {
    color: var(--cryo-gray);
    margin-bottom: 25px;
}

.cryo-pricing-amount {
    margin-bottom: 25px;
}

.cryo-pricing-amount .old {
    font-size: 2rem;
    color: #ccc;
    text-decoration: line-through;
    margin-right: 15px;
}

.cryo-pricing-amount .new {
    font-size: 4rem;
    font-weight: 800;
    color: var(--cryo-primary-dark);
}

.cryo-pricing-amount .new sup {
    font-size: 1.8rem;
    vertical-align: super;
}

.cryo-pricing-features {
    list-style: none;
    margin: 0 0 30px 0;
    padding: 0;
    text-align: left;
    display: inline-block;
}

.cryo-pricing-features li {
    padding: 10px 0;
    font-size: 1rem;
    color: var(--cryo-gray);
    display: flex;
    align-items: center;
    gap: 12px;
}

.cryo-pricing-features li i {
    color: var(--cryo-primary);
    font-size: 1.1rem;
}

/* Formulaire */
.cryo-form-section {
    background: var(--cryo-light);
    padding: 80px 0;
}

.cryo-form-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 0 20px;
}

.cryo-form-card {
    background: white;
    padding: 50px;
    border-radius: 30px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
}

.cryo-form-header {
    text-align: center;
    margin-bottom: 35px;
}

.cryo-form-header h2 {
    font-size: 1.8rem;
    color: var(--cryo-dark);
    margin-bottom: 10px;
}

.cryo-form-header p {
    color: var(--cryo-gray);
}

.cryo-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.cryo-form-group {
    margin-bottom: 15px;
}

.cryo-form-group.full-width {
    grid-column: span 2;
}

.cryo-form-group label {
    display: block;
    font-weight: 600;
    color: var(--cryo-dark);
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.cryo-form-group label span {
    color: var(--cryo-secondary);
}

.cryo-form-group input,
.cryo-form-group select,
.cryo-form-group textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.3s ease;
    background: var(--cryo-light);
}

.cryo-form-group input:focus,
.cryo-form-group select:focus,
.cryo-form-group textarea:focus {
    outline: none;
    border-color: var(--cryo-primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(0, 212, 255, 0.1);
}

.cryo-form-group .error-msg {
    color: var(--cryo-secondary);
    font-size: 0.85rem;
    margin-top: 5px;
    display: none;
}

.cryo-form-group.has-error input,
.cryo-form-group.has-error select {
    border-color: var(--cryo-secondary);
}

.cryo-form-group.has-error .error-msg {
    display: block;
}

.cryo-form-submit {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, var(--cryo-primary), var(--cryo-primary-dark));
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    box-shadow: 0 10px 40px rgba(0, 212, 255, 0.3);
}

.cryo-form-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 50px rgba(0, 212, 255, 0.4);
}

.cryo-form-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.cryo-form-note {
    text-align: center;
    margin-top: 20px;
    color: var(--cryo-gray);
    font-size: 0.9rem;
}

.cryo-form-note i {
    color: var(--cryo-primary);
    margin-right: 5px;
}

/* Confirmation */
.cryo-confirmation {
    display: none;
    text-align: center;
    padding: 40px;
}

.cryo-confirmation.show {
    display: block;
}

.cryo-confirmation i.fa-check-circle {
    font-size: 5rem;
    color: var(--cryo-primary);
    margin-bottom: 25px;
}

.cryo-confirmation h3 {
    font-size: 1.8rem;
    color: var(--cryo-dark);
    margin-bottom: 15px;
}

.cryo-confirmation p {
    color: var(--cryo-gray);
    font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .cryo-hero-container {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .cryo-hero h1 {
        font-size: 2.5rem;
    }
    
    .cryo-hero-image {
        display: none;
    }
    
    .cryo-benefits-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cryo-steps-container {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .cryo-testimonials-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .cryo-hero {
        padding: 60px 0;
        min-height: auto;
    }
    
    .cryo-hero h1 {
        font-size: 2rem;
    }
    
    .cryo-hero h1 span {
        font-size: 1.1rem;
    }
    
    .cryo-price-new {
        font-size: 2.8rem;
    }
    
    .cryo-section-title {
        font-size: 1.8rem;
    }
    
    .cryo-benefits-grid {
        grid-template-columns: 1fr;
    }
    
    .cryo-form-grid {
        grid-template-columns: 1fr;
    }
    
    .cryo-form-group.full-width {
        grid-column: span 1;
    }
    
    .cryo-pricing-card {
        padding: 30px 20px;
    }
    
    .cryo-pricing-amount .new {
        font-size: 3rem;
    }
    
    .cryo-form-card {
        padding: 30px 20px;
    }
    
    .cryo-section {
        padding: 60px 0;
    }
}
</style>

<!-- ========== HERO SECTION ========== -->
<section class="cryo-hero">
    <div class="cryo-hero-container">
        <div class="cryo-hero-content">
            <div class="cryo-badge">
                <i class="fa fa-fire"></i> Offre Limitée - Cannes
            </div>
            
            <h1>
                Cryolipolyse
                <span>Éliminez vos graisses par le froid</span>
            </h1>
            
            <p class="cryo-hero-description">
                Technique non invasive et indolore pour détruire définitivement les cellules graisseuses. 
                Résultats visibles dès la première séance, sans chirurgie ni éviction sociale.
            </p>
            
            <div class="cryo-price-box">
                <span class="cryo-price-old">149 €</span>
                <span class="cryo-price-new">99<sup>€</sup></span>
                <span class="cryo-price-label">La séance découverte</span>
                <span class="cryo-price-savings">
                    <i class="fa fa-tag"></i> Économisez 50 €
                </span>
            </div>
            
            <a href="#cryo-booking" class="cryo-cta-btn">
                <i class="fa fa-calendar-check-o"></i>
                Réserver ma séance à 99€
            </a>
        </div>
        
        <div class="cryo-hero-image">
            <img src="/images/cryo/cryolipolyse-hero.jpg" 
                 alt="Séance de Cryolipolyse - traitement minceur professionnel">
            
            <div class="cryo-floating-badge">
                <i class="fa fa-check-circle"></i>
                <span>Sans chirurgie<br>Sans douleur</span>
            </div>
        </div>
    </div>
</section>

<!-- ========== BENEFITS SECTION ========== -->
<section class="cryo-section cryo-benefits">
    <div class="cryo-section-container">
        <div class="cryo-section-header">
            <span class="cryo-section-subtitle">Pourquoi choisir la cryolipolyse</span>
            <h2 class="cryo-section-title">Les Avantages du Traitement</h2>
            <p class="cryo-section-description">
                Une technologie révolutionnaire qui élimine les graisses localisées de façon définitive et naturelle.
            </p>
        </div>
        
        <div class="cryo-benefits-grid">
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon">
                    <i class="fa fa-snowflake-o"></i>
                </div>
                <h3>100% Non Invasif</h3>
                <p>Aucune chirurgie, aucune aiguille. Le froid contrôlé agit naturellement sur les cellules graisseuses.</p>
            </div>
            
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon">
                    <i class="fa fa-heart"></i>
                </div>
                <h3>Indolore</h3>
                <p>Sensation de froid au début, puis plus rien. Détendez-vous pendant que la technologie agit.</p>
            </div>
            
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <h3>Sans Éviction Sociale</h3>
                <p>Reprenez vos activités immédiatement après la séance. Aucun temps de récupération.</p>
            </div>
            
            <div class="cryo-benefit-card">
                <div class="cryo-benefit-icon">
                    <i class="fa fa-refresh"></i>
                </div>
                <h3>Résultats Durables</h3>
                <p>Les cellules graisseuses détruites sont éliminées définitivement par votre organisme.</p>
            </div>
        </div>
    </div>
</section>

<!-- ========== HOW IT WORKS ========== -->
<section class="cryo-section cryo-steps">
    <div class="cryo-section-container">
        <div class="cryo-section-header">
            <span class="cryo-section-subtitle">Le processus</span>
            <h2 class="cryo-section-title">Comment ça Fonctionne ?</h2>
            <p class="cryo-section-description">
                Un protocole simple et efficace en 3 étapes pour des résultats visibles.
            </p>
        </div>
        
        <div class="cryo-steps-container">
            <div class="cryo-step-card">
                <div class="cryo-step-number">1</div>
                <h3>Consultation Gratuite</h3>
                <p>Analyse de vos zones à traiter et définition d'un programme personnalisé adapté à vos objectifs.</p>
            </div>
            
            <div class="cryo-step-card">
                <div class="cryo-step-number">2</div>
                <h3>Séance de Cryolipolyse</h3>
                <p>Application du froid contrôlé (-8°C) pendant 45 minutes. Les cellules graisseuses sont cristallisées.</p>
            </div>
            
            <div class="cryo-step-card">
                <div class="cryo-step-number">3</div>
                <h3>Élimination Naturelle</h3>
                <p>Votre corps élimine naturellement les cellules détruites en 6 à 12 semaines. Résultats progressifs.</p>
            </div>
        </div>
    </div>
</section>

<!-- ========== TESTIMONIALS ========== -->
<section class="cryo-section cryo-testimonials">
    <div class="cryo-section-container">
        <div class="cryo-section-header">
            <span class="cryo-section-subtitle">Témoignages</span>
            <h2 class="cryo-section-title">Ce que Disent Nos Clientes</h2>
        </div>
        
        <div class="cryo-testimonials-grid">
            <div class="cryo-testimonial-card">
                <div class="cryo-testimonial-stars">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </div>
                <p class="cryo-testimonial-text">
                    "J'avais tout essayé pour perdre mes poignées d'amour. La cryolipolyse a été la solution miracle ! Résultats visibles dès le premier mois."
                </p>
                <div class="cryo-testimonial-author">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Isabelle">
                    <div>
                        <h5>Isabelle M.</h5>
                        <span>Cannes</span>
                    </div>
                </div>
            </div>
            
            <div class="cryo-testimonial-card">
                <div class="cryo-testimonial-stars">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </div>
                <p class="cryo-testimonial-text">
                    "Séance très agréable, l'équipe est top et les résultats sont là ! J'ai perdu 3 cm de tour de ventre sans effort."
                </p>
                <div class="cryo-testimonial-author">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80" alt="Caroline">
                    <div>
                        <h5>Caroline D.</h5>
                        <span>Mandelieu</span>
                    </div>
                </div>
            </div>
            
            <div class="cryo-testimonial-card">
                <div class="cryo-testimonial-stars">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </div>
                <p class="cryo-testimonial-text">
                    "Après ma grossesse, j'avais du mal à retrouver ma silhouette. Grâce à la cryo, j'ai enfin retrouvé mon corps d'avant !"
                </p>
                <div class="cryo-testimonial-author">
                    <img src="https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=100&q=80" alt="Nathalie">
                    <div>
                        <h5>Nathalie P.</h5>
                        <span>Nice</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PRICING CTA ========== -->
<section class="cryo-pricing">
    <div class="cryo-pricing-container">
        <div class="cryo-pricing-card">
            <span class="cryo-pricing-badge">
                <i class="fa fa-clock-o"></i> Offre Limitée
            </span>
            
            <h2 class="cryo-pricing-title">Séance Découverte Cryolipolyse</h2>
            <p class="cryo-pricing-subtitle">Réservez maintenant et profitez de notre offre exclusive</p>
            
            <div class="cryo-pricing-amount">
                <span class="old">149€</span>
                <span class="new">99<sup>€</sup></span>
            </div>
            
            <ul class="cryo-pricing-features">
                <li><i class="fa fa-check-circle"></i> Consultation personnalisée incluse</li>
                <li><i class="fa fa-check-circle"></i> Séance de 45 minutes</li>
                <li><i class="fa fa-check-circle"></i> 1 zone au choix (ventre, cuisses, bras...)</li>
                <li><i class="fa fa-check-circle"></i> Suivi post-traitement</li>
                <li><i class="fa fa-check-circle"></i> Conseils nutrition offerts</li>
            </ul>
            
            <a href="#cryo-booking" class="cryo-cta-btn" style="width: 100%; justify-content: center;">
                <i class="fa fa-calendar-check-o"></i>
                Réserver ma séance à 99€
            </a>
        </div>
    </div>
</section>

<!-- ========== BOOKING FORM ========== -->
<section class="cryo-form-section" id="cryo-booking">
    <div class="cryo-form-container">
        <div class="cryo-form-card">
            <div class="cryo-form-header">
                <h2><i class="fa fa-calendar" style="color: var(--cryo-primary);"></i> Réservez Votre Séance</h2>
                <p>Remplissez le formulaire ci-dessous pour réserver votre séance découverte à 99€</p>
            </div>
            
            <?php if (!$cryo_success): ?>
            <form id="cryoBookingForm" method="post" action="/cryolipolyse#cryo-booking">
                <input type="hidden" name="cryo_submit" value="1">
                
                <?php if ($cryo_error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <i class="fa fa-exclamation-triangle"></i> <?= $cryo_error ?>
                </div>
                <?php endif; ?>
                
                <div class="cryo-form-grid">
                    <div class="cryo-form-group">
                        <label for="cryo_prenom">Prénom <span>*</span></label>
                        <input type="text" id="cryo_prenom" name="cryo_prenom" placeholder="Votre prénom" required>
                        <span class="error-msg">Veuillez entrer votre prénom</span>
                    </div>
                    
                    <div class="cryo-form-group">
                        <label for="cryo_nom">Nom <span>*</span></label>
                        <input type="text" id="cryo_nom" name="cryo_nom" placeholder="Votre nom" required>
                        <span class="error-msg">Veuillez entrer votre nom</span>
                    </div>
                    
                    <div class="cryo-form-group">
                        <label for="cryo_email">Email <span>*</span></label>
                        <input type="email" id="cryo_email" name="cryo_email" placeholder="votre@email.com" required>
                        <span class="error-msg">Veuillez entrer un email valide</span>
                    </div>
                    
                    <div class="cryo-form-group">
                        <label for="cryo_telephone">Téléphone <span>*</span></label>
                        <input type="tel" id="cryo_telephone" name="cryo_telephone" placeholder="06 12 34 56 78" required>
                        <span class="error-msg">Veuillez entrer votre téléphone</span>
                    </div>
                    
                    <div class="cryo-form-group">
                        <label for="cryo_zone">Zone à traiter <span>*</span></label>
                        <select id="cryo_zone" name="cryo_zone" required>
                            <option value="">Sélectionnez une zone</option>
                            <option value="Ventre">Ventre</option>
                            <option value="Poignées d'amour">Poignées d'amour</option>
                            <option value="Cuisses">Cuisses</option>
                            <option value="Bras">Bras</option>
                            <option value="Dos">Dos</option>
                            <option value="Double menton">Double menton</option>
                            <option value="Autre">Autre (préciser)</option>
                        </select>
                        <span class="error-msg">Veuillez sélectionner une zone</span>
                    </div>
                    
                    <div class="cryo-form-group">
                        <label for="cryo_horaire">Horaire préféré <span>*</span></label>
                        <select id="cryo_horaire" name="cryo_horaire" required>
                            <option value="">Sélectionnez un créneau</option>
                            <option value="Matin (9h - 12h)">Matin (9h - 12h)</option>
                            <option value="Midi (12h - 14h)">Midi (12h - 14h)</option>
                            <option value="Après-midi (14h - 18h)">Après-midi (14h - 18h)</option>
                            <option value="Soir (18h - 20h)">Soir (18h - 20h)</option>
                        </select>
                        <span class="error-msg">Veuillez sélectionner un horaire</span>
                    </div>
                    
                    <div class="cryo-form-group full-width">
                        <label for="cryo_message">Message (optionnel)</label>
                        <textarea id="cryo_message" name="cryo_message" rows="3" placeholder="Précisions ou questions..."></textarea>
                    </div>
                </div>
                
                <button type="submit" class="cryo-form-submit" id="cryoSubmitBtn">
                    <i class="fa fa-calendar-check-o"></i>
                    Réserver ma séance à 99€
                </button>
                
                <p class="cryo-form-note">
                    <i class="fa fa-shield"></i>
                    Nous vous contacterons rapidement pour confirmer votre RDV et procéder au paiement.
                </p>
            </form>
            <?php endif; ?>
            
            <!-- Message de confirmation -->
            <div class="cryo-confirmation <?= $cryo_success ? 'show' : '' ?>" id="cryoConfirmation">
                <i class="fa fa-check-circle"></i>
                <h3>Merci pour votre réservation !</h3>
                <p>Nous avons bien reçu votre demande. Notre équipe va vous recontacter rapidement pour confirmer votre rendez-vous de cryolipolyse.</p>
                <p style="margin-top: 15px; font-weight: 600; color: var(--cryo-primary-dark);">
                    Un email de confirmation vous a été envoyé.
                </p>
                <div style="margin-top: 30px;">
                    <a href="https://aquavelo.com" style="display: inline-block; background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; box-shadow: 0 5px 20px rgba(0,168,204,0.3); transition: all 0.3s ease;">
                        <i class="fa fa-home"></i> Retour au site Aquavelo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript pour le formulaire -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cryoBookingForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const submitBtn = document.getElementById('cryoSubmitBtn');
        
        // Reset des erreurs
        document.querySelectorAll('.cryo-form-group').forEach(group => {
            group.classList.remove('has-error');
        });
        
        // Validation des champs requis
        const requiredFields = ['cryo_prenom', 'cryo_nom', 'cryo_email', 'cryo_telephone', 'cryo_zone', 'cryo_horaire'];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !field.value.trim()) {
                field.parentElement.classList.add('has-error');
                isValid = false;
            }
        });
        
        // Validation email
        const emailField = document.getElementById('cryo_email');
        if (emailField) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value.trim())) {
                emailField.parentElement.classList.add('has-error');
                isValid = false;
            }
        }
        
        // Validation téléphone
        const phoneField = document.getElementById('cryo_telephone');
        if (phoneField) {
            const phoneRegex = /^[\d\s\-\+\(\)]{10,}$/;
            if (!phoneRegex.test(phoneField.value.trim())) {
                phoneField.parentElement.classList.add('has-error');
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Si valide, on soumet le formulaire (pas de preventDefault)
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi en cours...';
        
        // Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'cryo_booking_submit', {
                'event_category': 'conversion',
                'event_label': 'cryolipolyse_99'
            });
        }
        
        return true;
    });
    
    // Remove error on input
    document.querySelectorAll('.cryo-form-group input, .cryo-form-group select, .cryo-form-group textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.parentElement.classList.remove('has-error');
        });
    });
    
    // Si la confirmation est affichée, scroll vers elle
    const confirmation = document.getElementById('cryoConfirmation');
    if (confirmation && confirmation.classList.contains('show')) {
        setTimeout(function() {
            confirmation.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }
});
</script>


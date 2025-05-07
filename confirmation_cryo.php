<?php
// confirmation_cryo.php
// Callback Monetico (CGI-2). Vérifie le MAC, envoie les mails via PHPMailer et répond au serveur Monetico.

require 'vendor/autoload.php';
require 'settings.php';  // Doit définir MONETICO_KEY et $conn (PDO)

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ajoute le champ 'version' au calcul du MAC
function validateMAC(array $params, string $keyHex): bool {
    $recognizedKeys = [
        'TPE','contexte_commande','date','montant','reference','texte-libre',
        'code-retour','cvx','vld','brand','status3ds','numauto','originecb',
        'bincb','hpancb','ipclient','originetr','cbmasquee','modepaiement',
        'authentification','usage','typecompte','ecard',
        'version',     // <- ajouté
        'MAC'
    ];

    // Filtrage & encodage
    $macFields = [];
    foreach ($recognizedKeys as $k) {
        if (isset($params[$k])) {
            $macFields[$k] = mb_convert_encoding($params[$k], 'UTF-8', 'auto');
        }
    }
    ksort($macFields, SORT_STRING);

    // Construction de la chaîne signée
    $chaine = '';
    foreach ($macFields as $k => $v) {
        if ($k !== 'MAC') {
            $chaine .= "$k=$v*";
        }
    }
    $chaine = rtrim($chaine, '*');

    // Debug
    file_put_contents('confirmation_debug.txt',
        "[".date('c')."] Chaîne signée: $chaine\n" .
        "[".date('c')."] MAC reçu    : {$params['MAC']}\n", 
        FILE_APPEND
    );

    // Calcul HMAC-SHA1
    $macCalc = strtoupper(hash_hmac('sha1', $chaine, pack('H*', $keyHex)));
    file_put_contents('confirmation_debug.txt',
        "[".date('c')."] MAC calculé : $macCalc\n\n",
        FILE_APPEND
    );

    return isset($params['MAC']) && hash_equals($macCalc, strtoupper($params['MAC']));
}

// Envoi des mails de remerciement + administratif
function sendThankYouEmail($toEmail, $prenom, $nom, $telephone, $achat, $montant): string {
    $codeValidation = strtoupper(substr(md5(uniqid('', true)), 0, 8));
    $mail = new PHPMailer(true);
    try {
        // Configuration SMTP (Mailjet)
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'adf33e0c77039ed69396e3a8a07400cb';
        $mail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Mail client
        $mail->setFrom('jacquesverdier4@gmail.com','Aquavelo');
        $mail->addAddress($toEmail);
        $mail->addReplyTo('jacquesverdier4@gmail.com','Aquavelo');
        $mail->isHTML(true);
        $mail->Subject = 'Merci pour votre achat';
        $mail->Body    =
            "<p>Bonjour <strong>$prenom $nom</strong>,</p>"
          . "<p>Merci pour votre achat de <strong>$achat</strong> pour un montant de <strong>$montant</strong>.</p>"
          . "<p>Pour prendre rendez-vous, envoyez un WhatsApp à <strong>Loredana</strong> au <strong>07 55 00 73 87</strong>.</p>"
          . "<hr><div style='padding:20px;border:2px dashed #104e8b;background:#f4f8fb;'>"
          . "<h2 style='text-align:center;color:#104e8b;'>🎟️ Bon de réservation - Séance Cryo</h2>"
          . "<p><strong>Nom :</strong> $prenom $nom</p>"
          . "<p><strong>Téléphone :</strong> $telephone</p>"
          . "<p><strong>Email :</strong> $toEmail</p>"
          . "<p><strong>Offre :</strong> $achat</p>"
          . "<p><strong>Montant :</strong> $montant</p>"
          . "<p><strong>Centre :</strong> AQUAVELO – "
          . "<a href='https://maps.google.com/?q=60 avenue du Docteur Raymond Picaud, Cannes' target='_blank'>"
          . "60 av. Dr Raymond Picaud, Cannes</a></p>"
          . "<p><strong>Code de validation :</strong> "
          . "<span style='font-size:1.3em;color:#cc3366;'>$codeValidation</span></p>"
          . "<p style='text-align:center;margin-top:15px;'>"
          . "📍 Présentez ce bon lors de votre venue.</p>"
          . "</div><p>À bientôt,<br>Claude – Équipe AQUAVELO</p>";

        $mail->AltBody =
            "Bonjour $prenom $nom,\n\n"
          . "Merci pour votre achat de $achat pour $montant.\n\n"
          . "Contact : Loredana – 07 55 00 73 87\n\n"
          . "Centre : AQUAVELO, 60 av. Dr R. Picaud, Cannes\n"
          . "Code : $codeValidation\n\n"
          . "Présentez ce code imprimé lors de votre venue.\n\n"
          . "Claude – Équipe AQUAVELO";

        $mail->send();

        // Mail admin
        $admin = clone $mail;
        $admin->clearAddresses();
        $admin->addAddress('aqua.cannes@gmail.com');
        $admin->Subject = "Nouvel achat – $prenom $nom";
        $admin->Body =
            "<p>Un achat a été effectué :</p><ul>"
          . "<li>Nom : <strong>$nom $prenom</strong></li>"
          . "<li>Email : $toEmail</li>"
          . "<li>Téléphone : $telephone</li>"
          . "<li>Produit : <strong>$achat</strong></li>"
          . "<li>Montant : <strong>$montant</strong></li>"
          . "<li>Code : $codeValidation</li>"
          . "<li>Centre : <a href='https://maps.google.com/?q=60 avenue du Docteur Raymond Picaud, Cannes'>"
          . "60 av. Dr R. Picaud, Cannes</a></li>"
          . "</ul>";
        $admin->send();

        return $codeValidation;

    } catch (Exception $e) {
        file_put_contents('confirmation_debug.txt',
            "[".date('c')."] Erreur PHPMailer: {$mail->ErrorInfo}\n",
            FILE_APPEND
        );
        return '';
    }
}

// --- POINT D’ENTRÉE CGI 2 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log brut
    file_put_contents('confirmation_debug.txt',
        "[".date('c')."] POST reçu :\n".print_r($_POST, true)."\n",
        FILE_APPEND
    );

    // 1) Vérification MAC
    if (!isset($_POST['MAC']) || !validateMAC($_POST, MONETICO_KEY)) {
        file_put_contents('confirmation_debug.txt',
            "[".date('c')."] ❌ MAC invalide\n\n",
            FILE_APPEND
        );
        header('Content-Type: text/plain');
        echo "version=2\ncdr=1\n"; // NOK
        exit;
    }

    // 2) Extraction des infos client
    parse_str(str_replace(';','&', $_POST['texte-libre'] ?? ''), $infos);
    $email     = trim($infos['email']     ?? '');
    $prenom    = trim($infos['prenom']    ?? '');
    $nom       = trim($infos['nom']       ?? '');
    $telephone = trim($infos['telephone'] ?? '');
    $achat     = trim($infos['achat']     ?? 'Inconnu');
    $montant   = trim($infos['montant']   ?? ($_POST['montant'] ?? '0.00 EUR'));

    file_put_contents('confirmation_debug.txt',
        "[".date('c')."] Infos client :\n".print_r($infos, true)."\n",
        FILE_APPEND
    );

    // 3) Envoi mail et mise à jour DB
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = sendThankYouEmail($email, $prenom, $nom, $telephone, $achat, $montant);
        // Marquer la vente en DB
        $stmt = $conn->prepare("
            UPDATE formule
            SET vente = 1
            WHERE id = (
              SELECT id FROM (
                SELECT id FROM formule WHERE email = :email ORDER BY id DESC LIMIT 1
              ) AS tmp
            )
        ");
        $stmt->execute(['email' => $email]);
    } else {
        file_put_contents('confirmation_debug.txt',
            "[".date('c')."] ❌ Email invalide\n\n",
            FILE_APPEND
        );
    }

    // 4) Réponse CGI ou redirection
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        header('Content-Type: text/plain');
        echo "version=2\ncdr=0\n"; // OK
        exit;
    }
    header('Location: merci.php');
    exit;
}

// Toute autre méthode => on renvoie l’utilisateur
header('Location: https://www.aquavelo.com/centres/Cannes');
exit;


















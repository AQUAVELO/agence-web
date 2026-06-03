<?php
$privacy_updated = '31 mai 2026';
$privacy_email = 'claude@alesiaminceur.com';
$privacy_address = '60 avenue du Docteur Picaud, 06150 Cannes, France';
?>

<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Politique de confidentialité</h1>
    <ol class="breadcrumb pull-right">
      <li><a href="<?= BASE_PATH ?>">Accueil</a></li>
      <li class="active">Politique de confidentialité</li>
    </ol>
  </div>
</header>

<section class="content-area bg1" style="padding: 50px 0 70px;">
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <div class="alert" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none; padding: 25px 30px; border-radius: 12px; margin-bottom: 35px;">
          <p style="margin: 0; font-size: 1.05rem; line-height: 1.7;">
            <i class="fa fa-shield"></i>
            Le site <strong>www.aquavelo.com</strong> s’engage à protéger vos données personnelles conformément au
            Règlement général sur la protection des données (RGPD) et à la loi « Informatique et Libertés ».
          </p>
          <p style="margin: 12px 0 0 0; font-size: 0.95rem; opacity: 0.95;">
            Dernière mise à jour : <?= htmlspecialchars($privacy_updated, ENT_QUOTES, 'UTF-8'); ?>
          </p>
        </div>

        <article class="legal-content" style="font-size: 1.02rem; line-height: 1.75; color: #444;">

          <h2 style="color: #00a8cc; margin-top: 0;">1. Responsable du traitement</h2>
          <p>
            Le responsable du traitement des données personnelles collectées via le site
            <a href="https://www.aquavelo.com/">www.aquavelo.com</a> est :
          </p>
          <ul>
            <li><strong>Raison sociale :</strong> AQUA CANNES</li>
            <li><strong>Responsable de publication :</strong> AQUA CANNES</li>
            <li><strong>Adresse :</strong> <?= htmlspecialchars($privacy_address, ENT_QUOTES, 'UTF-8'); ?></li>
            <li><strong>E-mail :</strong> <a href="mailto:<?= htmlspecialchars($privacy_email, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($privacy_email, ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li><strong>Téléphone :</strong> <a href="tel:+33622647095">06 22 64 70 95</a></li>
          </ul>
          <p>
            Les centres du réseau Aquavelo (franchisés ou affiliés) peuvent traiter vos données en tant que
            responsables de traitement distincts pour la gestion de votre relation commerciale au sein de leur établissement.
          </p>

          <h2 style="color: #00a8cc;">2. Données personnelles collectées</h2>
          <p>Selon les services utilisés, nous pouvons collecter les catégories de données suivantes :</p>
          <ul>
            <li>Identité : nom, prénom</li>
            <li>Coordonnées : adresse e-mail, numéro de téléphone, ville</li>
            <li>Données liées à la réservation : créneau choisi, centre concerné, historique de séances d’essai</li>
            <li>Données de communication : contenu des messages envoyés via nos formulaires</li>
            <li>Données de navigation : adresse IP, type de navigateur, pages consultées, date et heure de connexion (via cookies, sous réserve de votre consentement)</li>
            <li>Données de paiement : traitées par nos prestataires de paiement sécurisés ; nous ne conservons pas vos coordonnées bancaires complètes</li>
          </ul>

          <h2 style="color: #00a8cc;">3. Finalités et bases légales</h2>
          <p>Vos données sont traitées pour les finalités suivantes :</p>
          <table class="table table-bordered" style="background: #fff; margin: 20px 0;">
            <thead>
              <tr style="background: #f5f9fc;">
                <th>Finalité</th>
                <th>Base légale</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Gestion des demandes de contact et de séance découverte</td>
                <td>Exécution de mesures précontractuelles / intérêt légitime</td>
              </tr>
              <tr>
                <td>Prise de rendez-vous et gestion du planning</td>
                <td>Exécution du contrat ou mesures précontractuelles</td>
              </tr>
              <tr>
                <td>Envoi d’e-mails de confirmation, rappels et suivi après séance</td>
                <td>Exécution du contrat / intérêt légitime</td>
              </tr>
              <tr>
                <td>Envoi d’offres commerciales et newsletters</td>
                <td>Votre consentement (désinscription possible à tout moment)</td>
              </tr>
              <tr>
                <td>Mesure d’audience et amélioration du site</td>
                <td>Votre consentement (cookies non essentiels)</td>
              </tr>
              <tr>
                <td>Respect des obligations légales et comptables</td>
                <td>Obligation légale</td>
              </tr>
            </tbody>
          </table>

          <h2 style="color: #00a8cc;">4. Destinataires des données</h2>
          <p>Vos données peuvent être communiquées, dans la stricte limite de leurs missions, à :</p>
          <ul>
            <li>Les équipes et dirigeants des centres Aquavelo concernés par votre demande</li>
            <li>Notre hébergeur : <strong>O2switch</strong> — Chemin des Pardiaux, 63000 Clermont-Ferrand, France</li>
            <li>Nos prestataires d’envoi d’e-mails (ex. Mailjet / SMTP professionnel)</li>
            <li>Nos prestataires de mesure d’audience (Google Analytics, Google Tag Manager), si vous avez accepté les cookies</li>
            <li>Google Calendar, pour la synchronisation de certains rendez-vous (centres équipés)</li>
            <li>Les autorités compétentes, sur demande légale</li>
          </ul>
          <p>Nous ne vendons pas vos données personnelles à des tiers.</p>

          <h2 style="color: #00a8cc;">5. Durée de conservation</h2>
          <ul>
            <li><strong>Prospects / séances découverte :</strong> jusqu’à 3 ans à compter du dernier contact</li>
            <li><strong>Clients et abonnés :</strong> durée de la relation contractuelle, puis archivage selon les délais légaux (comptabilité, litiges)</li>
            <li><strong>Cookies analytics :</strong> 13 mois maximum après dépôt, conformément aux recommandations de la CNIL</li>
            <li><strong>Logs techniques :</strong> durée limitée au strict nécessaire pour la sécurité du site</li>
          </ul>

          <h2 style="color: #00a8cc;">6. Cookies et traceurs</h2>
          <p>
            Lors de votre visite, des cookies peuvent être déposés sur votre terminal. Les cookies strictement
            nécessaires au fonctionnement du site peuvent être utilisés sans consentement.
          </p>
          <p>Les cookies de mesure d’audience et de statistiques ne sont activés qu’après votre accord via le bandeau cookies.</p>
          <p><strong>Cookies et traceurs susceptibles d’être utilisés :</strong></p>
          <ul>
            <li>Cookies de session et de préférences (fonctionnement du site)</li>
            <li>Google Analytics (mesure d’audience)</li>
            <li>Google Tag Manager (gestion des balises)</li>
            <li>Outils d’analyse complémentaires (ex. Segment), le cas échéant</li>
          </ul>
          <p>
            Vous pouvez à tout moment retirer votre consentement en supprimant les cookies via les paramètres de
            votre navigateur ou en refusant les cookies lors de votre prochaine visite.
            Pour en savoir plus : <a href="https://www.cnil.fr/fr/cookies-et-autres-traceurs" target="_blank" rel="noopener noreferrer">CNIL — Cookies et traceurs</a>.
          </p>

          <h2 style="color: #00a8cc;">7. Vos droits</h2>
          <p>Conformément au RGPD, vous disposez des droits suivants :</p>
          <ul>
            <li>Droit d’accès à vos données</li>
            <li>Droit de rectification des données inexactes</li>
            <li>Droit à l’effacement (« droit à l’oubli »), dans les limites prévues par la loi</li>
            <li>Droit à la limitation du traitement</li>
            <li>Droit d’opposition au traitement fondé sur l’intérêt légitime</li>
            <li>Droit à la portabilité des données que vous nous avez fournies</li>
            <li>Droit de retirer votre consentement à tout moment (sans affecter la licéité du traitement antérieur)</li>
            <li>Droit de définir des directives relatives au sort de vos données après votre décès</li>
          </ul>
          <p>
            Pour exercer vos droits, adressez votre demande (avec une copie d’une pièce d’identité si nécessaire) à :
            <a href="mailto:<?= htmlspecialchars($privacy_email, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($privacy_email, ENT_QUOTES, 'UTF-8'); ?></a>
            ou par courrier à <?= htmlspecialchars($privacy_address, ENT_QUOTES, 'UTF-8'); ?>.
          </p>
          <p>Nous nous engageons à vous répondre dans un délai d’un mois à compter de la réception de votre demande.</p>

          <h2 style="color: #00a8cc;">8. Réclamation auprès de la CNIL</h2>
          <p>
            Si vous estimez, après nous avoir contactés, que vos droits ne sont pas respectés, vous pouvez introduire
            une réclamation auprès de la Commission nationale de l’informatique et des libertés (CNIL) :
            <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer">www.cnil.fr</a> —
            3 Place de Fontenoy, TSA 80715, 75334 Paris Cedex 07.
          </p>

          <h2 style="color: #00a8cc;">9. Sécurité</h2>
          <p>
            Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données
            contre la destruction, la perte, l’altération, la divulgation ou l’accès non autorisé, notamment :
          </p>
          <ul>
            <li>Connexion sécurisée HTTPS (protocole SSL/TLS)</li>
            <li>Accès restreint aux données personnelles</li>
            <li>Sauvegardes et pare-feu côté hébergeur</li>
            <li>Mots de passe et accès administrateurs protégés</li>
          </ul>

          <h2 style="color: #00a8cc;">10. Transferts hors Union européenne</h2>
          <p>
            Certains prestataires (notamment Google) peuvent traiter des données en dehors de l’Union européenne.
            Dans ce cas, des garanties appropriées sont mises en place (clauses contractuelles types, décisions
            d’adéquation de la Commission européenne) conformément au RGPD.
          </p>

          <h2 style="color: #00a8cc;">11. Mineurs</h2>
          <p>
            Le site s’adresse principalement à un public majeur. Les mineurs doivent obtenir l’accord de leur
            représentant légal avant de transmettre des données personnelles via nos formulaires.
          </p>

          <h2 style="color: #00a8cc;">12. Modification de la politique</h2>
          <p>
            La présente politique peut être mise à jour pour refléter l’évolution de nos pratiques ou de la
            réglementation. La date de dernière mise à jour figure en haut de cette page.
          </p>

          <h2 style="color: #00a8cc;">13. Liens utiles</h2>
          <ul>
            <li><a href="<?= BASE_PATH ?>contact">Page contact</a></li>
            <li><a href="https://www.cnil.fr/fr/reglement-europeen-protection-donnees" target="_blank" rel="noopener noreferrer">Textes officiels RGPD (CNIL)</a></li>
          </ul>

        </article>

        <div class="text-center" style="margin-top: 40px;">
          <a href="<?= BASE_PATH ?>contact" class="btn btn-lg" style="background: #00a8cc; color: white; border: none; padding: 14px 35px; border-radius: 25px; margin-right: 10px;">
            <i class="fa fa-envelope"></i> Nous contacter
          </a>
          <a href="<?= BASE_PATH ?>" class="btn btn-lg btn-default" style="padding: 14px 35px; border-radius: 25px;">
            <i class="fa fa-home"></i> Retour à l'accueil
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

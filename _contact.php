<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
?>
<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Contactez-nous</h1>

    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Contact</li>
    </ol>
  </div>
</header>

<section class="content-area bg1">
  <div class="container">

    <header class="page-header text-center">
      <h1 class="page-title">Besoin d'aide </h1>
      <p class="larger">Vous avez une question ? Utilisez le formulaire ci-dessous ou envoyez-nous un e-mail à <a href="mailto:claude@alesiaminceur.com">claude@alesiaminceur.com</a> </p>
      <p class="larger">ou contactez nous au 06 22 64 70 95.</p>
    </header>

    <div class="row">
      <div class="col-md-8">
        <div class="contactForm">
          <div class="successMessage alert alert-success alert-dismissable" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Merci! Nous vous répondrons dès que possible.
          </div>
          <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Une erreur est survenue.
          </div>

          <div class="col-sm-6 col-sm-offset-3">
            <hr>

            <form action="#contact" method="post">
              <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo $_POST['email']; ?>" />
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Nom, prénom et ville concernée </label>
                <input type="text" class="form-control" placeholder="Nom, prénom et la ville concernée" name="nom" value="<?php echo $_POST['nom']; ?>" />
              </div>
              <div class="form-group">
                <label for="exampleInputFile">Message</label>
                <textarea class="form-control" placeholder="Message" name="message"><?php echo $_POST['message']; ?></textarea>
              </div>
              <input type="hidden" name="raison">

              <div class="checkbox">

              </div>
              <button type="submit" name="send" class="btn btn-default">Envoyer</button>
            </form>


            <?php
            if (isset($_POST['send']) && empty($_POST['raison'])) {




              $mail = new PHPMailer(true);
              $mail->IsSMTP();
              $mail->Host = "in-v3.mailjet.com";
              // $mail->Host = $settings['mjhost'];
              $mail->isHTML(true);                                  // Set email format to HTML

              $mail->SMTPAuth = true;
              $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
              $mail->Port = 587;
              //$mail->Username = $settings['mjusername'];
              //$mail->Password = $settings['mjpassword'];
              $mail->Username = getenv("MAILJET_USERNAME");
              $mail->Password = getenv("MAILJET_PASSWORD");
              //Create a new PHPMailer instance
              $nom = $_POST['nom'];
              $email = $_POST['email'];
              $message = $_POST['message'];
              $err = 0;

              if (empty($nom) or empty($email) or empty($message)) {
                $vide = '<br />Un des champs est vide.';
                $err++;
              }
              $errors = $vide;

              if ($err == 0) {

                $mail->setFrom('contact@aquavelo.com', 'Aquavelo');
                $mail->addReplyTo('contact@aquavelo.com', 'Aquavelo');

                $destinataire = "Aquavelo <claude@alesiaminceur.com>";
                $mail->addAddress('franchise@alesiaminceur.com', 'Claude Rodriguez');

                $object = "Contact sur le site Aquavelo";
                $mail->Subject = $object;

                $message = 'La personne ' . $nom . ' avec le mail ' . $email . ' vous a envoy&eacute; ce message :
		<br /><br />' . $message;

                $mail->msgHTML($message);

                //send the message, check for errors
                if (!$mail->send()) {
                  echo '<div class="error">Votre message n\'a pas &eacute;t&eacute; envoy&eacute;.</div>';
                } else {
                  echo '<div class="error">Votre message a bien &eacute;t&eacute; envoy&eacute;.</div>';
                }


                echo '<div class="error">Votre message a bien &eacute;t&eacute; envoy&eacute;.</div>';
              } else {
                echo '<div class="error"><span class="underline">Il y a ' . $err . ' erreur';
                if ($err > 1) {
                  echo 's';
                }
                echo ' dans le formulaire :</span>' . $errors . '</div>';
              }
            }



            ?>


          </div>
        </div>
      </div>
      <div class="col-md-4">

        <dl>
          <dt>Recrutement</dt>
          <dd>Nous recherchons des maîtres nageurs pour notre développement national en France et international sur l'Espagne et le Maroc, envoyer CV + photo à claude@alesiaminceur.com </dd>
          <dd></dd>
          <dd></dd>
          <dt>Gestionnaire du site internet</dt>
          <dd>Aqua Cannes</dd>
          <dd>60 avenue du Docteur Raymond Picaud</dd>
	  <dd>06150 Cannes</dd>
	  <dd>Capital 15000 €</dd>	
	  <dd>RCS 822 269 528</dd>
	  <dd>TVA FR44822269528</dd>
		

          <dt>Téléphone</dt>
          <dd>+33 (0)6 22 64 70 95</dd>

          <dt>E-mail</dt>
          <dd><a href="mailto:claude@alesiaminceur.com">claude@alesiaminceur.com</a></dd>
        </dl>
        <dl>
	<button type="button" data-toggle="modal" data-target="#infos" class="btn btn-primary">Informations</button>
<div class="modal" id="infos">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Plus d'informations</h4>
      </div>
      <div class="modal-body">
        <!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Document sans nom</title>
</head>

<body>
<div>
  <div></div>
</div>
<p><strong>Mentions Légales</strong> </p>
<p> </p>
<h3>1. Présentation du site.</h3>
<p>En vertu de l&rsquo;article 6 de la loi n° 2004-575 du 21 juin 2004 pour la confiance dans l&rsquo;économie numérique, il est précisé aux utilisateurs du site <a href="https://aquavelo.com/">https://aquavelo.com</a> l&rsquo;identité des différents intervenants dans le cadre de sa réalisation et de son suivi :</p>
<p><strong>Propriétaire</strong> : ALESIA MINCEUR</p>
<p><strong>Créateur</strong> : Claude RODRIGUEZ</p>
<p><strong>Responsable publication</strong> : ALESIA MINCEUR</p>
<p>Le responsable publication est une personne physique ou une personne morale.</p>
<p><strong>Webmaster</strong> : Claude RODRIGUEZ 171 allée des Mimosas</p>
<p><strong>Hébergeur</strong> : O2switch<br>
Chemin des Pardiaux, 63000 Clermont-Ferrand, France</p>
<h3>2. Conditions générales d&rsquo;utilisation du site et des services proposés.</h3>
<p>L&rsquo;utilisation du site <a href="https://aquavelo.com/">https://aquavelo.com</a> implique l&rsquo;acceptation pleine et entière des conditions générales d&rsquo;utilisation ci-après décrites. Ces conditions d&rsquo;utilisation sont susceptibles d&rsquo;être modifiées ou complétées à tout moment, les utilisateurs du site <a href="https://aquavelo.com/">https://aquavelo.com</a> sont donc invités à les consulter de manière régulière.</p>
<p>Ce site est normalement accessible à tout moment aux utilisateurs. Une interruption pour raison de maintenance technique peut être toutefois décidée par Julia, qui s&rsquo;efforcera alors de communiquer préalablement aux utilisateurs les dates et heures de l&rsquo;intervention.</p>
<p>Le site <a href="https://aquavelo.com/">https://aquavelo.com</a> est mis à jour régulièrement par Claude RODRIGUEZ. De la même façon, les mentions légales peuvent être modifiées à tout moment : elles s&rsquo;imposent néanmoins à l&rsquo;utilisateur qui est invité à s&rsquo;y référer le plus souvent possible afin d&rsquo;en prendre connaissance.</p>
<h3>3. Description des services fournis.</h3>
<p>Le site <a href="https://aquavelo.com/">https://aquavelo.com</a> a pour objet de fournir des informations concernant des services sur l'Aquavelo destiné aux particuliers.</p>
<p>Claude RODRIGUEZ s&rsquo;efforce de fournir sur le site <a href="https://aquavelo.com/">https://aquavelo.com</a> des informations aussi précises que possible. Toutefois, il ne pourrait être tenue responsable des omissions, des inexactitudes et des carences dans la mise à jour, qu&rsquo;elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.</p>
<p>Tous les informations indiquées sur le site <a href="https://aquavelo.com/">https://aquavelo.com</a> sont données à titre indicatif, et sont susceptibles d&rsquo;évoluer. Par ailleurs, les renseignements figurant sur le site <a href="https://aquavelo.com/">https://aquavelo.com</a> ne sont pas exhaustifs. Ils sont donnés sous réserve de modifications ayant été apportées depuis leur mise en ligne.</p>
<h3>4. Limitations contractuelles sur les données techniques.</h3>
<p>Le site utilise la technologie JavaScript.</p>
<p>Le site Internet ne pourra être tenu responsable de dommages matériels liés à l&rsquo;utilisation du site. De plus, l&rsquo;utilisateur du site s&rsquo;engage à accéder au site en utilisant un matériel récent, ne contenant pas de virus et avec un navigateur de dernière génération mis-à-jour</p>
<h3>5. Propriété intellectuelle et contrefaçons.</h3>
<p>AQUAVELO est propriétaire des droits de propriété intellectuelle ou détient les droits d&rsquo;usage sur tous les éléments accessibles sur le site, notamment les textes, images, graphismes, logo, icônes…</p>
<p>Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de Claude RODRIGUEZ.</p>
<p>Toute exploitation non autorisée du site ou de l&rsquo;un quelconque des éléments qu&rsquo;il contient sera considérée comme constitutive d&rsquo;une contrefaçon et poursuivie conformément aux dispositions des articles L.335-2 et suivants du Code de Propriété Intellectuelle.</p>
<h3>6. Limitations de responsabilité.</h3>
<p>ALESIA MINCEUR ne pourra être tenue responsable des dommages directs et indirects causés au matériel de l&rsquo;utilisateur, lors de l&rsquo;accès au site <a href="https://aquavelo.com/">https://aquavelo.com</a>, et résultant soit de l&rsquo;utilisation d&rsquo;un matériel ne répondant pas aux spécifications indiquées au point 4, soit de l&rsquo;apparition d&rsquo;un bug ou d&rsquo;une incompatibilité.</p>
<p>ALESIA MINCEUR ne pourra également être tenue responsable des dommages indirects (tels par exemple qu&rsquo;une perte de marché ou perte d&rsquo;une chance) consécutifs à l&rsquo;utilisation du site <a href="https://aquavelo.com/">https://aquavelo.com</a>.</p>
<p>Des espaces interactifs (possibilité de poser des questions dans l&rsquo;espace contact) sont à la disposition des utilisateurs. ALESIA MINCEUR se réserve le droit de supprimer, sans mise en demeure préalable, tout contenu déposé dans cet espace qui contreviendrait à la législation applicable en France, en particulier aux dispositions relatives à la protection des données. Le cas échéant, ALESIA MINCEUR se réserve également la possibilité de mettre en cause la responsabilité civile et/ou pénale de l&rsquo;utilisateur, notamment en cas de message à caractère raciste, injurieux, diffamant, ou pornographique, quel que soit le support utilisé (texte, photographie…).</p>
<h3>7. Gestion des données personnelles.</h3>
<p>En France, les données personnelles sont notamment protégées par la loi n° 78-87 du 6 janvier 1978, la loi n° 2004-801 du 6 août 2004, l&rsquo;article L. 226-13 du Code pénal et la Directive Européenne du 24 octobre 1995.</p>
<p>A l&rsquo;occasion de l&rsquo;utilisation du site <a href="https://aquavelo.com/">https://aquavelo.com</a>, peuvent êtres recueillies : l&rsquo;URL des liens par l&rsquo;intermédiaire desquels l&rsquo;utilisateur a accédé au site <a href="https://aquavelo.com/">https://aquavelo.com</a> le fournisseur d&rsquo;accès de l&rsquo;utilisateur, l&rsquo;adresse de protocole Internet (IP) de l&rsquo;utilisateur.</p>
<p>En tout état de cause ALESIA MINCEUR ne collecte des informations personnelles relatives à l&rsquo;utilisateur que pour le besoin de certains services proposés par le site <a href="https://aquavelo.com/">https://aquavelo.com</a>. L&rsquo;utilisateur fournit ces informations en toute connaissance de cause, notamment lorsqu&rsquo;il procède par lui-même à leur saisie. Il est alors précisé à l&rsquo;utilisateur du site <a href="https://aquavelo.com/">https://aquavelo.com</a> l&rsquo;obligation ou non de fournir ces informations.</p>
<p>Conformément aux dispositions des articles 38 et suivants de la loi 78-17 du 6 janvier 1978 relative à l&rsquo;informatique, aux fichiers et aux libertés, tout utilisateur dispose d&rsquo;un droit d&rsquo;accès, de rectification et d&rsquo;opposition aux données personnelles le concernant, en effectuant sa demande écrite et signée, accompagnée d&rsquo;une copie du titre d&rsquo;identité avec signature du titulaire de la pièce, en précisant l&rsquo;adresse à laquelle la réponse doit être envoyée.</p>
<p>Aucune information personnelle de l&rsquo;utilisateur du site <a href="https://aquavelo.com/">https://aquavelo.com</a> n&rsquo;est publiée à l&rsquo;insu de l&rsquo;utilisateur, échangée, transférée, cédée ou vendue sur un support quelconque à des tiers. Seule l&rsquo;hypothèse du rachat de ALESIA MINCEUR et de ses droits permettrait la transmission des dites informations à l&rsquo;éventuel acquéreur qui serait à son tour tenu de la même obligation de conservation et de modification des données vis à vis de l&rsquo;utilisateur du site <a href="https://aquavelo.com/">https://aquavelo.com</a>. </p>
<p>Les bases de données sont protégées par les dispositions de la loi du 1er juillet 1998 transposant la directive 96/9 du 11 mars 1996 relative à la protection juridique des bases de données.</p>
<h3>8. Liens hypertextes et cookies.</h3>
<p>Le site <a href="https://aquavelo.com/">https://aquavelo.com</a> contient un certain nombre de liens hypertextes vers d&rsquo;autres sites, mis en place avec l&rsquo;autorisation de ALESIA MINCEUR. Cependant, ALESIA MINCEUR n&rsquo;a pas la possibilité de vérifier le contenu des sites ainsi visités, et n&rsquo;assumera en conséquence aucune responsabilité de ce fait.</p>
<p>La navigation sur le site <a href="https://aquavelo.com/">https://aquavelo.com</a> est susceptible de provoquer l&rsquo;installation de cookie(s) sur l&rsquo;ordinateur de l&rsquo;utilisateur. Un cookie est un fichier de petite taille, qui ne permet pas l&rsquo;identification de l&rsquo;utilisateur, mais qui enregistre des informations relatives à la navigation d&rsquo;un ordinateur sur un site. Les données ainsi obtenues visent à faciliter la navigation ultérieure sur le site, et ont également vocation à permettre diverses mesures de fréquentation.</p>
<p>Le refus d&rsquo;installation d&rsquo;un cookie peut entraîner l&rsquo;impossibilité d&rsquo;accéder à certains services. L&rsquo;utilisateur peut toutefois configurer son ordinateur de la manière suivante, pour refuser l&rsquo;installation des cookies :</p>
<p>Sous Internet Explorer : onglet outil (pictogramme en forme de rouage en haut a droite) / options internet. Cliquez sur Confidentialité et choisissez Bloquer tous les cookies. Validez sur Ok.</p>
<p>Sous Firefox : en haut de la fenêtre du navigateur, cliquez sur le bouton Firefox, puis aller dans l&rsquo;onglet Options. Cliquer sur l&rsquo;onglet Vie privée.<br>
  Paramétrez les Règles de conservation sur : utiliser les paramètres personnalisés pour l&rsquo;historique. Enfin décochez-la pour désactiver les cookies.</p>
<p>Sous Safari : Cliquez en haut à droite du navigateur sur le pictogramme de menu (symbolisé par un rouage). Sélectionnez Paramètres. Cliquez sur Afficher les paramètres avancés. Dans la section « Confidentialité », cliquez sur Paramètres de contenu. Dans la section « Cookies », vous pouvez bloquer les cookies.</p>
<p>Sous Chrome : Cliquez en haut à droite du navigateur sur le pictogramme de menu (symbolisé par trois lignes horizontales). Sélectionnez Paramètres. Cliquez sur Afficher les paramètres avancés. Dans la section « Confidentialité », cliquez sur préférences. Dans l&rsquo;onglet « Confidentialité », vous pouvez bloquer les cookies.</p>
<h3>9. Droit applicable et attribution de juridiction.</h3>
<p>Tout litige en relation avec l&rsquo;utilisation du site <a href="https://aquavelo.com/">https://aquavelo.com </a>est soumis au droit français. Il est fait attribution exclusive de juridiction aux tribunaux compétents de Paris.</p>
<h3>10. Les principales lois concernées.</h3>
<p>Loi n° 78-87 du 6 janvier 1978, notamment modifiée par la loi n° 2004-801 du 6 août 2004 relative à l&rsquo;informatique, aux fichiers et aux libertés.</p>
<p>Loi n° 2004-575 du 21 juin 2004 pour la confiance dans l&rsquo;économie numérique.</p>
<h3>11. Lexique.</h3>
<p>Utilisateur : Internaute se connectant, utilisant le site susnommé.</p>
<p>Informations personnelles : « les informations qui permettent, sous quelque forme que ce soit, directement ou non, l&rsquo;identification des personnes physiques auxquelles elles s&rsquo;appliquent » (article 4 de la loi n° 78-17 du 6 janvier 1978).</p>
</body>
</html>
      </div>
      <div class="modal-footer">
        <em>Informations legales</em>
      </div>
    </div>
  </div>
</div>

        </dl>


      </div>

    </div>

  </div>
</section>



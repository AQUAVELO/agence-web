<?php
$title = "Cours de Natation avec Maître-Nageur | Apprentissage & Perfectionnement";
$meta_description = "Trouvez un maître-nageur diplômé pour vos cours de natation. Apprentissage pour enfants, perfectionnement adultes et natation sportive. Réservez votre séance avec Aquacoach.";
?>

<!-- JSON-LD spécifique Natation -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "serviceType": "Cours de Natation",
  "provider": {
    "@type": "LocalBusiness",
    "name": "Aquavelo",
    "image": "https://www.aquavelo.com/images/cours.jpeg",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "60 avenue du Docteur Picaud",
      "addressLocality": "Cannes",
      "postalCode": "06150",
      "addressCountry": "FR"
    }
  },
  "description": "Cours de natation individuels ou collectifs encadrés par des maîtres-nageurs diplômés. Apprentissage, perfectionnement et natation sportive pour tous niveaux.",
  "areaServed": "FR",
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Services de Natation",
    "itemListElement": [
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "Apprentissage de la natation pour enfants"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "Perfectionnement natation adultes"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "Coaching natation sportive"
        }
      }
    ]
  }
}
</script>

<section class="content-area">
    <div class="container" style="background-color: white; padding: 40px 20px;">
        <div class="row text-center" style="margin-bottom: 40px;">
            <div class="col-md-12">
                <h1 style="color: #00afdf; font-weight: 700; margin-bottom: 20px;">Cours de Natation & Coaching Aquatique</h1>
                <p class="lead" style="max-width: 800px; margin: 0 auto;">Découvrez nos programmes de natation adaptés à tous les niveaux. Que vous souhaitiez apprendre à nager, vaincre l'aquaphobie ou vous perfectionner, nos maîtres-nageurs certifiés vous accompagnent.</p>
            </div>
        </div>

        <style>
            .natation-card {
                background: #fff;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
                margin-bottom: 30px;
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            .natation-card:hover {
                transform: translateY(-10px);
            }
            .natation-card-img {
                height: 250px;
                background-size: cover;
                background-position: center;
            }
            .natation-card-body {
                padding: 30px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .natation-card h2 {
                font-size: 24px;
                color: #333;
                margin-top: 0;
                margin-bottom: 15px;
            }
            .natation-card p {
                color: #666;
                line-height: 1.6;
                margin-bottom: 25px;
            }
            .btn-natation {
                display: inline-block;
                padding: 12px 30px;
                background-color: #00afdf;
                color: white;
                border-radius: 50px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: all 0.3s;
                text-decoration: none !important;
            }
            .btn-natation:hover {
                background-color: #008eb6;
                color: white;
                box-shadow: 0 5px 15px rgba(0,175,223,0.4);
            }
            .btn-outline {
                background-color: transparent;
                border: 2px solid #00afdf;
                color: #00afdf;
            }
            .btn-outline:hover {
                background-color: #00afdf;
                color: white;
            }
        </style>

        <div class="row">
            <div class="col-md-6">
                <div class="natation-card">
                    <div class="natation-card-img" style="background-image: url('images/couple_cherchant.jpeg');"></div>
                    <div class="natation-card-body">
                        <div>
                            <h2>Trouver un Maître-Nageur</h2>
                            <p>Vous recherchez des cours de natation pour vous ou vos enfants ? Trouvez facilement un coach professionnel près de chez vous pour un apprentissage personnalisé et sécurisé.</p>
                        </div>
                        <a href="https://aquacoach.fr" target="_blank" class="btn-natation">Découvrir Aquacoach</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="natation-card">
                    <div class="natation-card-img" style="background-image: url('images/cours.jpeg');"></div>
                    <div class="natation-card-body">
                        <div>
                            <h2>Devenir Coach Partenaire</h2>
                            <p>Vous êtes maître-nageur diplômé (BEESAN, BPJEPS AAN) ? Rejoignez notre réseau, développez votre clientèle et gérez vos cours en toute liberté avec nos outils dédiés.</p>
                        </div>
                        <a href="<?= BASE_PATH ?>?p=inscription_nageur" class="btn-natation btn-outline">S'inscrire comme Coach</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row text-center" style="margin-top: 50px;">
            <div class="col-md-12">
                <h3 style="color: #333; margin-bottom: 30px;">Pourquoi choisir nos cours de natation ?</h3>
                <div class="row">
                    <div class="col-sm-4">
                        <i class="fa fa-shield fa-3x" style="color: #00afdf; margin-bottom: 15px;"></i>
                        <h4>Sécurité Maximale</h4>
                        <p>Tous nos intervenants sont diplômés d'État et formés aux premiers secours.</p>
                    </div>
                    <div class="col-sm-4">
                        <i class="fa fa-users fa-3x" style="color: #00afdf; margin-bottom: 15px;"></i>
                        <h4>Tous Niveaux</h4>
                        <p>De l'éveil aquatique dès 4 ans au perfectionnement technique pour adultes.</p>
                    </div>
                    <div class="col-sm-4">
                        <i class="fa fa-calendar fa-3x" style="color: #00afdf; margin-bottom: 15px;"></i>
                        <h4>Flexibilité</h4>
                        <p>Choisissez vos horaires et votre lieu d'entraînement selon vos disponibilités.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

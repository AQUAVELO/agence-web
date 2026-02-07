<?php
/**
 * OPTIMISATION SEO AQUAVELO
 * Balises Open Graph, Twitter Cards et JSON-LD Schema
 */

// Déterminer l'URL actuelle
$current_url = 'https://www.aquavelo.com' . $_SERVER['REQUEST_URI'];
$og_image = 'https://www.aquavelo.com/images/aquavelo-og-image.jpg';
$og_title = $title ?? 'Aquabiking et Aquagym Collectif en Piscine | 1ère Séance Offerte';
$og_description = $meta_description ?? 'Cours d\'aquabiking et aquagym avec coach. Perdez jusqu\'à 10kg en 3 mois. Brûlez 400-500 calories/séance. 17 centres en France. Réservez votre séance découverte gratuite !';
?>

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?= htmlspecialchars($current_url); ?>">
<meta property="og:title" content="<?= htmlspecialchars($og_title); ?>">
<meta property="og:description" content="<?= htmlspecialchars($og_description); ?>">
<meta property="og:image" content="<?= $og_image; ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="Aquavelo">
<meta property="og:locale" content="fr_FR">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?= htmlspecialchars($current_url); ?>">
<meta name="twitter:title" content="<?= htmlspecialchars($og_title); ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($og_description); ?>">
<meta name="twitter:image" content="<?= $og_image; ?>">

<!-- Données Structurées JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://www.aquavelo.com/#organization",
      "name": "Aquavelo",
      "url": "https://www.aquavelo.com",
      "logo": {
        "@type": "ImageObject",
        "url": "https://www.aquavelo.com/images/logo-aquavelo.png",
        "width": 250,
        "height": 100
      },
      "description": "Leader français de l'aquabiking et aquagym en piscine. 17 centres en France avec piscines privées chauffées et coaching professionnel.",
      "foundingDate": "2015",
      "founder": {
        "@type": "Person",
        "name": "Claude Rodriguez",
        "telephone": "+33622647095",
        "email": "claude@alesiaminceur.com"
      },
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "60 avenue du Docteur Picaud",
        "addressLocality": "Cannes",
        "postalCode": "06150",
        "addressCountry": "FR"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+33622647095",
        "contactType": "customer service",
        "email": "claude@alesiaminceur.com",
        "availableLanguage": ["French"],
        "areaServed": "FR"
      },
      "sameAs": [
        "https://www.facebook.com/aquavelo",
        "https://www.instagram.com/aquavelo"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "https://www.aquavelo.com/#website",
      "url": "https://www.aquavelo.com",
      "name": "Aquavelo",
      "description": "Cours d'aquabiking et aquagym collectifs en France",
      "publisher": {
        "@id": "https://www.aquavelo.com/#organization"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "https://www.aquavelo.com/centres?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    },
    {
      "@type": "Service",
      "@id": "https://www.aquavelo.com/#service",
      "serviceType": "Aquabiking et Aquagym",
      "name": "Cours d'Aquabiking et Aquagym Collectifs",
      "description": "Cours collectifs d'aquabiking et aquagym avec coach professionnel. Piscine privée chauffée. Séance découverte gratuite.",
      "provider": {
        "@id": "https://www.aquavelo.com/#organization"
      },
      "areaServed": {
        "@type": "Country",
        "name": "France"
      },
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Cours d'Aquabiking et Aquagym",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Cours d'Aquabiking Collectif",
              "description": "Séance de 45 minutes d'aquabiking sur vélo aquatique avec coach"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Cours d'Aquagym Collectif",
              "description": "Séance de 45 minutes d'aquagym tonique avec coach"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Séance Découverte Gratuite",
              "description": "Première séance d'aquabiking ou aquagym offerte sans engagement",
              "price": "0",
              "priceCurrency": "EUR"
            }
          }
        ]
      },
      "offers": {
        "@type": "AggregateOffer",
        "priceCurrency": "EUR",
        "lowPrice": "8",
        "highPrice": "20",
        "description": "Tarifs dégressifs selon formule d'abonnement"
      }
    }
    <?php if (isset($city) && isset($row_center) && !empty($row_center['address'])): ?>
    ,{
      "@type": "HealthAndBeautyBusiness",
      "@id": "https://www.aquavelo.com/centres/<?= urlencode($city); ?>#localbusiness",
      "name": "Aquavelo <?= htmlspecialchars($city); ?>",
      "description": "Centre d'aquabiking et aquagym à <?= htmlspecialchars($city); ?> avec piscine privée chauffée et coaching professionnel.",
      <?php if (!empty($row_center['id'])): ?>
      "image": "https://www.aquavelo.com/cloud/thumbnail/center_<?= $row_center['id']; ?>/1.jpg",
      <?php endif; ?>
      "telephone": "<?= $row_center['phone'] ?? '+33622647095'; ?>",
      "email": "<?= $row_center['email'] ?? 'claude@alesiaminceur.com'; ?>",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?= htmlspecialchars($row_center['address']); ?>",
        "addressLocality": "<?= htmlspecialchars($city); ?>",
        "postalCode": "<?= $row_center['zip'] ?? ''; ?>",
        "addressCountry": "FR"
      },
      <?php if (!empty($row_center['latitude']) && !empty($row_center['longitude'])): ?>
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "<?= $row_center['latitude']; ?>",
        "longitude": "<?= $row_center['longitude']; ?>"
      },
      <?php endif; ?>
      "url": "https://www.aquavelo.com/centres/<?= urlencode($city); ?>",
      "priceRange": "€€",
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
          "opens": "09:00",
          "closes": "21:00"
        },
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": "Saturday",
          "opens": "09:00",
          "closes": "14:00"
        }
      ],
      "hasMap": "https://www.google.com/maps?q=<?= urlencode($row_center['address'] . ', ' . $city); ?>",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "127",
        "bestRating": "5",
        "worstRating": "1"
      }
    }
    <?php endif; ?>
    <?php if ($page == 'aquabiking' || $page == 'home'): ?>
    ,{
      "@type": "FAQPage",
      "@id": "https://www.aquavelo.com/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Qu'est-ce que l'aquabiking ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "L'aquabiking (ou aquabike) est une activité sportive qui consiste à pédaler sur un vélo immergé dans l'eau, dans une piscine. C'est un sport doux pour les articulations mais très efficace pour brûler des calories (400-500 par séance), tonifier les muscles et réduire la cellulite grâce à l'effet massant de l'eau."
          }
        },
        {
          "@type": "Question",
          "name": "Quelle est la différence entre aquabiking et aquagym ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "L'aquabiking se pratique sur un vélo aquatique fixe immergé dans l'eau, principalement axé sur le bas du corps (jambes, fessiers, cuisses) et le cardio. L'aquagym est une gymnastique aquatique plus globale qui fait travailler tout le corps avec des mouvements variés, sans vélo, souvent avec des accessoires comme des haltères aquatiques."
          }
        },
        {
          "@type": "Question",
          "name": "Combien de calories brûle-t-on en aquabiking ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Une séance d'aquabiking de 45 minutes permet de brûler entre 400 et 500 calories en moyenne, selon l'intensité de l'effort. La résistance de l'eau multiplie l'efficacité du pédalage par rapport à un vélo classique, tout en étant plus doux pour les articulations."
          }
        },
        {
          "@type": "Question",
          "name": "L'aquabiking est-il efficace pour perdre du poids ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oui, l'aquabiking est très efficace pour la perte de poids. Avec 2 à 3 séances par semaine associées à une alimentation équilibrée, vous pouvez perdre jusqu'à 10kg en 3 mois. L'aquabiking cible particulièrement la cellulite et l'affinement de la silhouette."
          }
        },
        {
          "@type": "Question",
          "name": "Faut-il savoir nager pour faire de l'aquabiking ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Non, il n'est pas nécessaire de savoir nager pour pratiquer l'aquabiking. Le niveau d'eau arrive généralement à la taille ou la poitrine et vous restez assis sur le vélo pendant toute la séance. Les piscines Aquavelo ont un fond accessible et sécurisé."
          }
        },
        {
          "@type": "Question",
          "name": "Combien coûte une séance d'aquabiking chez Aquavelo ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Chez Aquavelo, les tarifs varient selon la formule choisie : à partir de 8€ la séance avec un abonnement annuel, 15€ la séance à l'unité, ou des forfaits 10 séances à tarif dégressif. La première séance découverte est gratuite sans engagement."
          }
        }
      ]
    }
    <?php endif; ?>
    <?php if ($page == 'aquagym'): ?>
    ,{
      "@type": "FAQPage",
      "@id": "https://www.aquavelo.com/?p=aquagym#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Qu'est-ce que l'aquagym tonique ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "L'aquagym tonique est une forme dynamique de gymnastique aquatique qui se pratique dans l'eau avec un coach. Elle combine des exercices cardiovasculaires et de renforcement musculaire utilisant la résistance naturelle de l'eau. Cette activité permet de brûler 300-400 calories par séance tout en préservant les articulations."
          }
        },
        {
          "@type": "Question",
          "name": "Quelle est la différence entre aquabiking et aquagym ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "L'aquabiking se pratique sur un vélo aquatique fixe immergé dans l'eau, principalement axé sur le bas du corps (jambes, fessiers, cuisses) et le cardio. L'aquagym est une gymnastique aquatique plus globale qui fait travailler tout le corps avec des mouvements variés, sans vélo, souvent avec des accessoires comme des haltères aquatiques ou des frites en mousse."
          }
        },
        {
          "@type": "Question",
          "name": "L'aquagym est-elle efficace pour perdre du poids ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oui, l'aquagym est efficace pour la perte de poids. Une séance de 45 minutes permet de brûler entre 300 et 400 calories. Avec 2 à 3 séances par semaine associées à une alimentation équilibrée, l'aquagym tonique favorise l'amincissement, la tonification musculaire et l'amélioration de la silhouette."
          }
        },
        {
          "@type": "Question",
          "name": "L'aquagym est-elle adaptée aux seniors ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Absolument ! L'aquagym est particulièrement recommandée pour les seniors car elle préserve les articulations grâce à la portance de l'eau, améliore la circulation sanguine, maintient la mobilité et renforce les muscles en douceur. C'est une activité sûre et progressive adaptée à tous les âges."
          }
        },
        {
          "@type": "Question",
          "name": "Peut-on faire de l'aquagym enceinte ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oui, l'aquagym peut être pratiquée pendant la grossesse avec l'accord de votre médecin ou sage-femme. L'eau soulage le poids du ventre, réduit les jambes lourdes, améliore la circulation et prépare le corps à l'accouchement. Nos coachs adaptent les exercices aux femmes enceintes."
          }
        },
        {
          "@type": "Question",
          "name": "Combien coûte un cours d'aquagym chez Aquavelo ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Chez Aquavelo, les tarifs d'aquagym sont identiques à ceux de l'aquabiking : à partir de 8€ la séance avec un abonnement annuel, 15€ la séance à l'unité, ou des forfaits 10 séances à tarif dégressif. La première séance découverte est gratuite sans engagement."
          }
        }
      ]
    }
    <?php endif; ?>
    <?php if ($page == 'aquabiking'): ?>
    ,{
      "@type": "VideoObject",
      "@id": "https://www.aquavelo.com/aquabiking#video",
      "name": "Démonstration Aquabiking en Piscine - Centre Aquavelo",
      "description": "Découvrez comment se déroule une séance d'aquabiking collective avec coach dans nos centres Aquavelo. Vidéo de démonstration du vélo aquatique en action dans une piscine chauffée.",
      "thumbnailUrl": "https://www.aquavelo.com/images/aquavelo-video-thumbnail.jpg",
      "uploadDate": "2024-01-15",
      "duration": "PT2M30S",
      "contentUrl": "https://www.aquavelo.com/Video-aquavelo-de-Antibes.mov",
      "embedUrl": "https://www.aquavelo.com/aquabiking#velo"
    }
    <?php endif; ?>
  ]
}
</script>

<!-- Breadcrumb Schema -->
<?php if (isset($city)): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Accueil",
      "item": "https://www.aquavelo.com"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Centres",
      "item": "https://www.aquavelo.com/centres"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?= htmlspecialchars($city); ?>",
      "item": "https://www.aquavelo.com/centres/<?= urlencode($city); ?>"
    }
  ]
}
</script>
<?php endif; ?>

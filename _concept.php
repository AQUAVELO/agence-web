<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept Aquavelo - Ouvrir un centre d'aquabiking</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.95), rgba(0, 168, 204, 0.95)), 
                        url('images/content/about-v2-title-bg.jpg') center/cover;
            color: white;
            padding: 120px 20px;
            text-align: center;
            position: relative;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto;
            font-weight: 300;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Section Title */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #00a8cc;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .section-title h3 {
            font-size: 1.5rem;
            color: #666;
            font-weight: 400;
        }

        /* Content Sections */
        .content-section {
            padding: 80px 0;
        }

        .bg-white {
            background: #fff;
        }

        .bg-light {
            background: #f8f9fa;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #e8f8fc 0%, #d4f1f9 100%);
        }

        /* Two Column Layout */
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }

        .two-columns p {
            margin-bottom: 20px;
            text-align: justify;
            line-height: 1.8;
        }

        /* Icon Boxes */
        .icon-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .icon-box {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 168, 204, 0.15);
            transition: all 0.3s ease;
            text-align: center;
        }

        .icon-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 168, 204, 0.25);
        }

        .icon-box i {
            font-size: 3rem;
            color: #00d4ff;
            margin-bottom: 20px;
        }

        .icon-box h4 {
            font-size: 1.3rem;
            color: #00a8cc;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .icon-box p {
            color: #666;
            line-height: 1.7;
            text-align: left;
        }

        /* Stats Counter */
        .stats-section {
            background: linear-gradient(135deg, #00d4ff 0%, #00a8cc 100%);
            color: white;
            padding: 60px 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .stat-item {
            padding: 20px;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            display: block;
            margin-bottom: 10px;
        }

        .stat-number sup {
            font-size: 1.5rem;
            margin-left: 5px;
        }

        .stat-desc {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        /* Investment Section */
        .investment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 50px;
        }

        .investment-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 168, 204, 0.15);
        }

        .investment-card h3 {
            color: #00a8cc;
            font-size: 1.5rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #00d4ff;
        }

        .investment-card ul {
            list-style: none;
            padding: 0;
        }

        .investment-card ul li {
            padding: 12px 0;
            padding-left: 30px;
            position: relative;
            line-height: 1.7;
        }

        .investment-card ul li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #00d4ff;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 168, 204, 0.3);
            margin: 10px 10px 10px 0;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 168, 204, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 168, 204, 0.1));
            padding: 60px 20px;
            text-align: center;
            margin-top: 60px;
        }

        .cta-section h3 {
            font-size: 2rem;
            color: #00a8cc;
            margin-bottom: 30px;
        }

        .contact-info {
            font-size: 1.2rem;
            margin: 20px 0;
        }

        .contact-info i {
            color: #00d4ff;
            margin-right: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .two-columns,
            .investment-grid {
                grid-template-columns: 1fr;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .btn {
                width: 100%;
                margin: 10px 0;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Quote Box */
        .quote-box {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin: 40px 0;
            font-size: 1.3rem;
            font-weight: 300;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 168, 204, 0.3);
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>💧 Ouvrir un Centre Aquavelo</h1>
            <p>Notre concept d'aquabiking collectif propose une solution alternative en plein cœur de votre ville</p>
        </div>
    </section>

    <!-- Introduction -->
    <section class="content-section bg-white">
        <div class="container">
            <div class="section-title">
                <h2>Aquavélo : Le Sport Minceur de Référence</h2>
                <h3>L'activité de fitness idéale pour perdre du poids, éliminer la cellulite et drainer les toxines</h3>
            </div>

            <div class="two-columns">
                <div>
                    <p><strong>À qui s'adresse l'aquavélo ?</strong> L'un des points forts de l'aquabiking, c'est qu'il se pratique quel que soit son niveau de forme. En effet, même les personnes avec des capacités de mouvements réduits (surpoids, âge, femmes enceintes, accidents articulaires ou opérations, problèmes de dos…) peuvent pratiquer sans danger avec un effet garanti.</p>

                    <p>D'une part, par rapport à l'exercice physique pratiqué sur terre, l'exercice de l'hydrobike évite les stress musculaires et diminue la production d'acide lactique. Vous pouvez oublier les si désagréables courbatures !</p>

                    <p>D'autre part, le milieu aquatique évite de brusquer les articulations tout en bénéficiant de la résistance de l'eau qui assure un massage et un drainage naturel. L'aquabiking permet aux personnes âgées, aux femmes enceintes, ou aux convalescents, de reprendre une activité sportive en douceur.</p>

                    <p>Contrairement à l'aquagym simple, l'aquabiking permet de tonifier et de remodeler rapidement sa silhouette. Dans l'eau, votre corps est plus léger… mais vos muscles travaillent pourtant bien plus pour repousser le poids de l'eau !</p>
                </div>

                <div>
                    <p>Ce sport développe également votre endurance et vos capacités cardio-vasculaires en douceur. La résistance de l'eau a pour conséquence un effort différent, un massage aux multiples vertus : le cœur bat 10% moins vite que dans l'air et les graisses sont brûlées en priorité.</p>

                    <p>L'avantage du travail musculaire effectué sous l'eau provoque un effet de massage continu très efficace pour drainer les jambes lourdes. C'est la raison pour laquelle la pratique du vélo aquatique est un ennemi redoutable de la peau d'orange.</p>

                    <p><strong>En terme d'effet</strong>, c'est comme si on faisait du vélo avec une séance de drainage lymphatique et du palper rouler. On cumule 3 effets sur le plan vasculaire !</p>

                    <p><strong>Combien brûle-t-on de calories ?</strong> Près de 400 à 500 pour une séance de 45 minutes, pour un sport sans traumatisme et qui est facile à pratiquer même si vous êtes en surpoids ou en convalescence !</p>

                    <p>L'effet anti-cellulite est vraiment visible rapidement avec une pratique de 2 séances par semaine. Imaginez les bénéfices au bout de 12 mois : des jambes et des fesses bien galbées, une sangle abdominale tonique en un minimum de temps.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Avantages du Réseau -->
    <section class="content-section bg-gradient">
        <div class="container">
            <div class="section-title">
                <h2>Le Groupe Alésia Minceur</h2>
                <h3>Les Avantages de Notre Réseau</h3>
            </div>

            <p style="text-align: center; max-width: 800px; margin: 0 auto 50px; font-size: 1.1rem;">
                Le réseau existe depuis 9 ans, certains dirigeants ont 2 centres ou ont acheté les murs.
            </p>

            <div class="icon-boxes">
                <div class="icon-box">
                    <i class="fas fa-chart-line"></i>
                    <h4>Communication Puissante</h4>
                    <p>Nous maîtrisons parfaitement la communication : marketing digital (réseaux sociaux, web), affichage traditionnel, publipostage, emailing, etc. Nous générons le trafic souhaité dans votre centre pour optimiser votre rentabilité. Nous avons nos propres mannequins pour les campagnes promotionnelles.</p>
                </div>

                <div class="icon-box">
                    <i class="fas fa-users"></i>
                    <h4>Fidélisation Client</h4>
                    <p>Les animations et ambiances de centre nous permettent d'avoir un taux de renouvellement de plus de 40% ! Une clientèle fidèle qui revient année après année.</p>
                </div>

                <div class="icon-box">
                    <i class="fas fa-handshake"></i>
                    <h4>Accompagnement Total</h4>
                    <p>L'emplacement est trouvé par Aquavelo dans une rue avec du passage (piéton et voiture) dans une ville d'au minimum 15 000 habitants. Possibilité d'ouvrir en zone commerciale ou en centre ville.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">98<sup>%</sup></span>
                    <span class="stat-desc">Taux de satisfaction client</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">90<sup>jours</sup></span>
                    <span class="stat-desc">Temps moyen d'ouverture</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">17</span>
                    <span class="stat-desc">Centres ouverts en France</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">25<sup>ans</sup></span>
                    <span class="stat-desc">Expérience dans l'amincissement</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Ouvrir un Centre -->
    <section class="content-section bg-white" id="ouvrir">
        <div class="container">
            <div class="section-title">
                <h2>Notre Force de Réseau</h2>
                <h3>Ouvrir un Centre AQUAVELO</h3>
            </div>

            <div style="text-align: center; margin-bottom: 40px;">
                <a href="https://www.dropbox.com/s/wow02pk0jncaolr/8p%20Aquavelo%20Piscine.pdf?dl=0" class="btn" target="_blank">
                    <i class="fas fa-download"></i> Télécharger la Brochure
                </a>
            </div>

            <div class="investment-grid">
                <div class="investment-card">
                    <h3><i class="fas fa-building"></i> Le Concept</h3>
                    <p>La communication AQUAVELO permet d'attirer la clientèle intéressée par la thalassothérapie, l'amincissement corporel, l'activité physique en douceur.</p>
                    <p><strong>Ciblage client :</strong> 15 à 85 ans, 90% de femmes</p>
                    <p><strong>Horaires :</strong> 7 jours sur 7 de 9h à 21h</p>
                    <p><strong>Encadrement :</strong> Cours réalisés par des maîtres nageurs</p>
                    <p><strong>Réservation :</strong> Les clientes réservent directement leurs cours sur notre application selon leur disponibilité</p>
                </div>

                <div class="investment-card">
                    <h3><i class="fas fa-handshake"></i> L'Accompagnement</h3>
                    <ul>
                        <li>Formation technique, commerciale et marketing opérationnel de 15 jours minimum</li>
                        <li>Marketing commercial intense (internet, fidélisation, prospectus, etc.)</li>
                        <li>Plateforme collaborative (docs, vidéos commerciales et techniques)</li>
                        <li>Réunions régulières</li>
                        <li>Un coach pour vous assister</li>
                        <li>Campagnes marketing intenses</li>
                        <li>Redevances fixes : 490 € par mois</li>
                    </ul>
                </div>
            </div>

            <div class="quote-box">
                <i class="fas fa-chart-bar" style="font-size: 2rem; margin-bottom: 20px;"></i>
                <p>Les centres AQUAVELO encaissent entre 300 000 € et 600 000 € de chiffre d'affaires annuel avec une excellente rentabilité pour un centre de 200 à 350 m²</p>
            </div>

            <div class="investment-grid" style="margin-top: 50px;">
                <div class="investment-card">
                    <h3><i class="fas fa-user-tie"></i> Profil Recherché</h3>
                    <p>Pour créer votre centre, peu importe que vous soyez un homme ou une femme, vous devez avoir un profil commercial et être motivé par le contact humain.</p>
                    <p><strong>Emplacement :</strong> Un emplacement "bis" (rue avec passage piéton et voiture) dans une ville d'au minimum 40 000 habitants.</p>
                    <p><strong>Taille :</strong> Vous pouvez ouvrir votre centre avec 6 à 20 vélos en fonction de l'apport que vous souhaitez investir.</p>
                </div>

                <div class="investment-card">
                    <h3><i class="fas fa-euro-sign"></i> Investissement</h3>
                    <ul>
                        <li>Droit d'entrée : à partir de 6 000 €</li>
                        <li>Travaux et aménagements : 1 000 à 1 500 € du m² (bassin, climatisation, déshumidificateur, vélos, vestiaires, carrelage, etc.)</li>
                        <li>Enseigne et Vitrophanie : 2 000 €</li>
                    </ul>
                    <p style="background: #e8f8fc; padding: 20px; border-radius: 10px; margin-top: 20px;">
                        <strong style="color: #00a8cc; font-size: 1.2rem;">Investissement total :</strong><br>
                        Inférieur à 200 000 € pour 200 m²<br>
                        <strong style="color: #00d4ff;">Apport requis : 50 000 €</strong>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="cta-section">
        <div class="container">
            <h3><i class="fas fa-phone-alt"></i> Contactez-Nous</h3>
            <div class="contact-info">
                <p><i class="fas fa-user"></i> <strong>Claude RODRIGUEZ</strong></p>
                <p><i class="fas fa-mobile-alt"></i> <a href="tel:0622647095" style="color: #00a8cc; text-decoration: none;">06 22 64 70 95</a></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:directionalesiaminceur@gmail.com" style="color: #00a8cc; text-decoration: none;">directionalesiaminceur@gmail.com</a></p>
            </div>
            <a href="mailto:directionalesiaminceur@gmail.com" class="btn">
                <i class="fas fa-paper-plane"></i> Demander des Informations
            </a>
        </div>
    </section>

    <script>
        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.icon-box, .investment-card').forEach(el => {
            observer.observe(el);
        });

        // Animation des compteurs (version corrigée)
        const animateCounter = (element, target, suffix = '') => {
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    element.innerHTML = Math.floor(current) + suffix;
                    requestAnimationFrame(updateCounter);
                } else {
                    element.innerHTML = target + suffix;
                }
            };

            updateCounter();
        };

        // Déclencher l'animation des compteurs au scroll
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statItem = entry.target;
                    const number = statItem.querySelector('.stat-number');
                    
                    // Extraire le nombre et le suffixe
                    const text = number.textContent.trim();
                    let value, suffix;
                    
                    if (text.includes('%')) {
                        value = 98;
                        suffix = '<sup>%</sup>';
                    } else if (text.includes('jours')) {
                        value = 90;
                        suffix = '<sup>jours</sup>';
                    } else if (text === '17') {
                        value = 17;
                        suffix = '';
                    } else if (text.includes('ans')) {
                        value = 25;
                        suffix = '<sup>ans</sup>';
                    }
                    
                    // Animer seulement une fois
                    if (!statItem.classList.contains('animated')) {
                        statItem.classList.add('animated');
                        number.textContent = '0';
                        animateCounter(number, value, suffix);
                    }
                    
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-item').forEach(stat => {
            statsObserver.observe(stat);
        });
    </script>
</body>
</html>

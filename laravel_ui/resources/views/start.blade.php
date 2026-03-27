<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Witrack - Start Page</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(26, 95, 95, 0.2), 0 0 20px rgba(26, 95, 95, 0.1);
            }
            50% {
                box-shadow: 0 4px 12px rgba(26, 95, 95, 0.3), 0 0 30px rgba(26, 95, 95, 0.2);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #0f3f3f 0%, #1a6f6f 50%, #2a5f5f 100%);
            min-height: 100vh;
            color: #333;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(26, 95, 95, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(42, 95, 95, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
            animation: fadeInUp 0.6s ease-out;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.2rem 2rem;
        }

        .logo {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.3s;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo i {
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            position: relative;
            transition: color 0.3s;
            font-size: 0.95rem;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #1a5f5f, #2a7f7f);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: #1a5f5f;
        }

        .login-btn {
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            color: white;
            padding: 0.7rem 1.8rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid transparent;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(26, 95, 95, 0.2);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 95, 95, 0.3);
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        .hero {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 4.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin: 4rem 0;
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: fadeInUp 0.8s ease-out 0.2s both;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(26, 95, 95, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            line-height: 1.15;
            margin-bottom: 1.2rem;
            color: #1a1a1a;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .hero-content .highlight {
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-content > p:first-of-type {
            font-size: 1.4rem;
            color: #2a5f5f;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .hero-content p {
            font-size: 1.05rem;
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }

        .tagline {
            display: inline-block;
            background: linear-gradient(135deg, rgba(26, 95, 95, 0.1), rgba(42, 95, 95, 0.08));
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #1a5f5f;
            margin: 1.5rem 0;
        }

        .tagline p {
            margin: 0.3rem 0;
        }

        .description {
            color: #777;
            font-size: 0.95rem;
            margin: 1.5rem 0;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            color: white;
            padding: 1.1rem 2.3rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(26, 95, 95, 0.3);
            letter-spacing: 0.3px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(26, 95, 95, 0.4);
        }

        .cta-button:active {
            transform: translateY(-1px);
        }

        .rocket-icon {
            font-size: 1.3rem;
            animation: float 3s ease-in-out infinite;
        }

        .graphic {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 350px;
        }

        .network-graph {
            position: relative;
            width: 280px;
            height: 280px;
            filter: drop-shadow(0 10px 30px rgba(26, 95, 95, 0.15));
        }

        .center-node {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1a9f9f 0%, #2a7f7f 100%);
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(26, 95, 95, 0.3);
            animation: glow 3s ease-in-out infinite;
            z-index: 10;
        }

        .center-node::before {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 95, 95, 0.2), transparent);
            animation: pulse 2s ease-in-out infinite;
        }

        .outer-node {
            position: absolute;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #a8d4d4 0%, #b8e4e4 100%);
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(26, 95, 95, 0.2);
            animation: float 4s ease-in-out infinite;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }

        .node-1 { top: 5px; left: 50%; transform: translateX(-50%); animation-delay: 0s; }
        .node-2 { top: 25%; right: 15px; animation-delay: 0.5s; }
        .node-3 { bottom: 25%; right: 12px; animation-delay: 1s; }
        .node-4 { bottom: 5px; left: 50%; transform: translateX(-50%); animation-delay: 1.5s; }
        .node-5 { bottom: 25%; left: 12px; animation-delay: 1s; }
        .node-6 { top: 25%; left: 15px; animation-delay: 0.5s; }

        svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .features-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 2.2rem;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out both;
        }

        .feature-card:nth-child(1) { animation-delay: 0.3s; }
        .feature-card:nth-child(2) { animation-delay: 0.4s; }
        .feature-card:nth-child(3) { animation-delay: 0.5s; }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(26, 95, 95, 0.15);
            border: 1px solid rgba(26, 95, 95, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-card h3 {
            color: #1a1a1a;
            margin-bottom: 0.8rem;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
            margin: 4rem 0;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 2.2rem;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            animation: fadeInUp 1.2s ease-out both;
        }

        .info-box:nth-child(1) { animation-delay: 0.5s; }
        .info-box:nth-child(2) { animation-delay: 0.6s; }
        .info-box:nth-child(3) { animation-delay: 0.7s; }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(26, 95, 95, 0.15);
        }

        .info-box h3 {
            color: #1a5f5f;
            margin-bottom: 1.2rem;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box ul {
            list-style: none;
        }

        .info-box li {
            padding: 0.7rem 0;
            color: #666;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .info-box li:hover {
            color: #1a5f5f;
            padding-left: 10px;
        }

        .info-box li:before {
            content: "→";
            color: #1a5f5f;
            margin-right: 0.7rem;
            font-weight: bold;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .info-box li:hover:before {
            opacity: 1;
        }

        .contact-info {
            margin-top: 1rem;
            color: #666;
            font-size: 0.95rem;
        }

        .contact-info p {
            margin: 0.8rem 0;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .contact-info a {
            color: #1a5f5f;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .contact-info a:hover {
            color: #0f3f3f;
            text-decoration: underline;
        }

        .social-links {
            display: flex;
            justify-content: flex-start;
            gap: 1rem;
            margin: 1.5rem 0;
            font-size: 1.4rem;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(26, 95, 95, 0.1), rgba(42, 95, 95, 0.08));
            color: #1a5f5f;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            border: 1px solid rgba(26, 95, 95, 0.1);
        }

        .social-links a:hover {
            background: linear-gradient(135deg, #1a5f5f 0%, #2a7f7f 100%);
            color: white;
            transform: translateY(-3px);
        }

        footer {
            background: linear-gradient(135deg, #0f3f3f 0%, #1a5f5f 100%);
            color: white;
            text-align: center;
            padding: 3.5rem 2rem;
            margin-top: 5rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-menu {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            margin: 1.5rem 0;
            flex-wrap: wrap;
        }

        .footer-menu a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            position: relative;
        }

        .footer-menu a::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 1px;
            background: white;
            transition: width 0.3s;
        }

        .footer-menu a:hover::after {
            width: 100%;
        }

        .footer-menu a:hover {
            color: white;
        }

        .footer-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 2.5rem;
                gap: 2.5rem;
                margin: 2rem 0;
            }

            .hero-content h1 {
                font-size: 2.2rem;
            }

            .nav-links {
                gap: 1rem;
                font-size: 0.85rem;
            }

            .info-section,
            .features-section {
                grid-template-columns: 1fr;
            }

            .graphic {
                height: 250px;
            }

            .network-graph {
                width: 200px;
                height: 200px;
            }

            nav {
                padding: 1rem 1.5rem;
            }

            main {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/" class="logo">
                <i class="fas fa-network-wired"></i> Witrack
            </a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#docs">Docs</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <a href="/login" class="login-btn">connexion</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="graphic">
                <div class="network-graph">
                    <svg viewBox="0 0 250 250" preserveAspectRatio="xMidYMid meet">
                        <!-- Lines connecting nodes with gradient -->
                        <defs>
                            <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#1a9f9f;stop-opacity:0.3" />
                                <stop offset="100%" style="stop-color:#2a7f7f;stop-opacity:0.1" />
                            </linearGradient>
                        </defs>
                        <line x1="125" y1="125" x2="125" y2="30" stroke="url(#lineGradient)" stroke-width="2.5"/>
                        <line x1="125" y1="125" x2="200" y2="85" stroke="url(#lineGradient)" stroke-width="2.5"/>
                        <line x1="125" y1="125" x2="210" y2="155" stroke="url(#lineGradient)" stroke-width="2.5"/>
                        <line x1="125" y1="125" x2="125" y2="220" stroke="url(#lineGradient)" stroke-width="2.5"/>
                        <line x1="125" y1="125" x2="50" y2="155" stroke="url(#lineGradient)" stroke-width="2.5"/>
                        <line x1="125" y1="125" x2="40" y2="85" stroke="url(#lineGradient)" stroke-width="2.5"/>
                    </svg>
                    <div class="center-node"></div>
                    <div class="outer-node node-1"></div>
                    <div class="outer-node node-2"></div>
                    <div class="outer-node node-3"></div>
                    <div class="outer-node node-4"></div>
                    <div class="outer-node node-5"></div>
                    <div class="outer-node node-6"></div>
                </div>
            </div>

            <div class="hero-content">
                <h1>AVEC <span class="highlight">Witrack</span></h1>
                <p>Posez vos questions, vos documents répondent</p>
                
                <div class="tagline">
                    <p style="font-weight: 600; color: #1a5f5f;">Rejoignez Une Nouvelle Façon D'exploiter Vos Données</p>
                    <p class="description">Analysez, Interrogez Et Comprenez Vos Documents Grâce À L'intelligence Artificielle.</p>
                </div>

                <a href="/start" class="cta-button">
                    <span class="rocket-icon">🚀</span>
                    Démarrer Votre Expérience
                </a>
            </div>
        </section>

        <section class="features-section">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Recherche Intelligente</h3>
                <p>Trouvez instantanément les informations dont vous avez besoin dans vos documents grâce à l'IA.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>Analyse Avancée</h3>
                <p>Obtenez des insights profonds et des analyses détaillées de votre contenu documentaire.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Sécurité Garantie</h3>
                <p>Vos données sont protégées avec les plus hauts standards de sécurité et de confidentialité.</p>
            </div>
        </section>

        <section class="info-section">
            <div class="info-box">
                <h3><i class="fas fa-bars" style="color: #1a5f5f;"></i> Navigation</h3>
                <ul>
                    <li><a href="#home" style="text-decoration: none; color: inherit;">Home</a></li>
                    <li><a href="#about" style="text-decoration: none; color: inherit;">About</a></li>
                    <li><a href="#docs" style="text-decoration: none; color: inherit;">Docs</a></li>
                    <li><a href="#contact" style="text-decoration: none; color: inherit;">Contact</a></li>
                </ul>
            </div>

            <div class="info-box">
                <h3><i class="fas fa-envelope" style="color: #1a5f5f;"></i> Contact</h3>
                <div class="contact-info">
                    <p><i class="fas fa-envelope"></i> <a href="mailto:contact@witrack.link">contact@witrack.link</a></p>
                    <p><i class="fas fa-phone"></i> +213 560 51 85 86</p>
                    <p><i class="fas fa-map-marker-alt"></i> Centre d'Affaire ANPT Cyber Parc, Sidi Abdellah, Rahmania, Alger</p>
                </div>
            </div>

            <div class="info-box">
                <h3><i class="fas fa-share-alt" style="color: #1a5f5f;"></i> Witrack</h3>
                <div class="social-links">
                    <a href="#tiktok" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#linkedin" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
                <p style="color: #999; font-size: 0.85rem; margin-top: 1rem;"><i class="fas fa-star"></i> Suivez-nous sur les réseaux sociaux</p>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-menu">
                <a href="#"><i class="fas fa-lock"></i> Privacy Policy</a>
                <a href="#"><i class="fas fa-file-contract"></i> Terms of Use</a>
                <a href="#"><i class="fas fa-redo"></i> Sales and Refunds</a>
                <a href="#"><i class="fas fa-gavel"></i> Legal</a>
                <a href="#"><i class="fas fa-sitemap"></i> Site Map</a>
            </div>
            <p class="footer-text">Copyright © 2026 Witrack. All rights reserved.</p>
            <p class="footer-text" style="margin-top: 0.5rem;">United States ✓</p>
        </div>
    </footer>
</body>
</html>

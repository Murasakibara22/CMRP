<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center; }
        .header img { max-width: 100px; margin-bottom: 20px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; }
        .content { padding: 40px 30px; }
        .welcome-box { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 30px; border-radius: 15px; text-align: center; margin-bottom: 30px; }
        .welcome-box h2 { color: #ffffff; margin: 0 0 10px 0; font-size: 24px; }
        .welcome-box p { color: #ffffff; margin: 0; font-size: 16px; opacity: 0.9; }
        .credentials-box { background: #f8f9fa; border-left: 4px solid #667eea; padding: 25px; border-radius: 10px; margin: 30px 0; }
        .credential-item { margin: 15px 0; }
        .credential-label { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 5px; }
        .credential-value { font-size: 18px; color: #2c3e50; font-weight: 600; background: #ffffff; padding: 12px 15px; border-radius: 8px; font-family: 'Courier New', monospace; }
        .info-card { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); padding: 20px; border-radius: 12px; margin: 20px 0; }
        .info-card h3 { color: #d63031; margin: 0 0 10px 0; font-size: 16px; display: flex; align-items: center; }
        .info-card h3::before { content: "⚠️"; margin-right: 10px; }
        .info-card ul { margin: 10px 0; padding-left: 20px; color: #2d3436; }
        .info-card ul li { margin: 8px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; padding: 16px 40px; text-decoration: none; border-radius: 50px; font-weight: 600; margin: 30px 0; text-align: center; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); transition: transform 0.3s; }
        .cta-button:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6); }
        .features { display: flex; gap: 15px; margin: 30px 0; flex-wrap: wrap; }
        .feature-item { flex: 1; min-width: 150px; background: #f8f9fa; padding: 20px; border-radius: 12px; text-align: center; }
        .feature-icon { font-size: 32px; margin-bottom: 10px; }
        .feature-text { font-size: 14px; color: #6c757d; }
        .footer { background: #2c3e50; color: #ecf0f1; padding: 30px; text-align: center; }
        .footer p { margin: 5px 0; font-size: 14px; }
        .social-links { margin: 20px 0; }
        .social-links a { display: inline-block; margin: 0 10px; color: #ecf0f1; text-decoration: none; font-size: 20px; }
        @media (max-width: 600px) {
            .container { margin: 20px 10px; }
            .features { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('logoStaff.png') }}" alt="Logo">
            <h1>✨ Bienvenue dans l'équipe !</h1>
        </div>

        <div class="content">
            <div class="welcome-box">
                <h2>Bonjour {{ $admin->nom }} 👋</h2>
                <p>Vous êtes maintenant administrateur de la plateforme</p>
            </div>

            <p style="color: #2c3e50; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
                Votre compte administrateur a été créé avec succès ! Vous avez désormais accès à toutes les fonctionnalités de gestion de la plateforme.
            </p>

            <div class="credentials-box">
                <h3 style="color: #667eea; margin: 0 0 20px 0; font-size: 18px;">🔐 Vos identifiants de connexion</h3>

                <div class="credential-item">
                    <div class="credential-label">Adresse Email</div>
                    <div class="credential-value">{{ $admin->email }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">Mot de passe</div>
                    <div class="credential-value" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        {{ $password }}
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h3>Recommandations de sécurité</h3>
                <ul>
                    <li>Changez votre mot de passe lors de votre première connexion</li>
                    <li>Ne partagez jamais vos identifiants avec quiconque</li>
                    <li>Utilisez un gestionnaire de mots de passe sécurisé</li>
                    <li>Déconnectez-vous après chaque session</li>
                </ul>
            </div>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-text">Tableau de bord complet</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👥</div>
                    <div class="feature-text">Gestion utilisateurs</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🏪</div>
                    <div class="feature-text">Validation fournisseurs</div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/auth/connexion') }}" class="cta-button">
                    🚀 Accéder à l'espace administrateur
                </a>
            </div>

            <p style="color: #6c757d; font-size: 14px; text-align: center; margin-top: 30px;">
                Vous rencontrez un problème ? <a href="mailto:support@example.com" style="color: #667eea;">Contactez le support</a>
            </p>
        </div>

        <div class="footer">
            <p style="font-weight: 600; font-size: 16px;">Plateforme de Gros</p>
            <div class="social-links">
                <a href="#">📘</a>
                <a href="#">🐦</a>
                <a href="#">📷</a>
                <a href="#">💼</a>
            </div>
            <p>© {{ date('Y') }} Tous droits réservés</p>
            <p style="font-size: 12px; opacity: 0.8;">Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>

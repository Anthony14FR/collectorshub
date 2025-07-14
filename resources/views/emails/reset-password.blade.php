<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe CollectorsHub</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('images/background_colors.png') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header-subtitle {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .greeting {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 20px;
        }
        
        .username {
            color: #3b82f6;
        }
        
        .message {
            font-size: 16px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            margin: 20px 0;
        }
        
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
        }
        
        .footer {
            background: #1e293b;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .footer-text {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .security-notice {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            color: #92400e;
        }
        
        .security-title {
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pokeball-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 50%, white 50%);
            border: 2px solid #374151;
            margin-right: 10px;
        }
        
        .app-url {
            color: #6b7280;
            font-size: 12px;
            margin-top: 20px;
            word-break: break-all;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 20px;
                border-radius: 10px;
            }
            
            .content {
                padding: 20px;
            }
            
            .reset-button {
                padding: 12px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="header-content">
                <div class="logo">🎮 CollectorsHub</div>
                <div class="header-subtitle">Récupération de votre compte</div>
            </div>
        </div>
        
        <div class="content">
            <div class="greeting">
                Salut <span class="username">{{ $user->username }}</span> ! 👋
            </div>
            
            <div class="message">
                Vous avez demandé une réinitialisation de votre mot de passe pour votre compte <strong>CollectorsHub</strong>.<br><br>
                Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe et retrouver l'accès à votre collection Pokémon !
            </div>
            
            <a href="{{ $actionUrl }}" class="reset-button">
                🔑 Réinitialiser mon mot de passe
            </a>
            
            <div class="security-notice">
                <div class="security-title">
                    <div class="pokeball-icon"></div>
                    Sécurité de votre compte
                </div>
                <div>
                    Ce lien de récupération expirera dans <strong>60 minutes</strong>.<br>
                    Si vous n'avez pas demandé cette récupération, ignorez cet email.<br>
                    Votre mot de passe actuel reste inchangé jusqu'à ce que vous en définissiez un nouveau.
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-text">
                Vous recevez cet email car une demande de récupération de mot de passe a été effectuée pour votre compte CollectorsHub.
            </div>
            <div class="footer-text">
                📧 Si vous avez des questions, contactez-nous à : 
                <a href="mailto:contact@collectorshub.fr" style="color: #60a5fa;">contact@collectorshub.fr</a>
            </div>
            <div class="app-url">
                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                {{ $actionUrl }}
            </div>
        </div>
    </div>
</body>
</html> 
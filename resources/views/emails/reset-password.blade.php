<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe Collector's Hub</title>
    <style>
        body { color: #1e293b; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 32px auto; border-radius: 16px; border: 1px solid #60a5fa; box-shadow: 0 8px 32px rgba(0,0,0,0.07); overflow: hidden; }
        .header { color: #fff; text-align: center; padding: 32px 16px; background: #3b82f6; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 4px; }
        .subtitle { font-size: 15px; opacity: 0.9; }
        .content { padding: 32px 24px; text-align: center; }
        .username { color: #2563eb; font-weight: bold; }
        .button { display: inline-block; background: #3b82f6; color: #fff; font-weight: bold; font-size: 16px; border-radius: 10px; padding: 12px 32px; text-decoration: none; margin: 24px 0; }
        .info { color: #334155; border-radius: 10px; padding: 16px; margin: 20px 0; font-size: 14px; border: 1px solid #e0e7ef; }
        .footer { color: #64748b; text-align: center; font-size: 12px; padding: 20px; border-top: 1px solid #e0e7ef; }
        .link { color: #2563eb; text-decoration: none; }
        .small { font-size: 10px; margin-top: 16px; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">🎮 Collector's Hub</div>
            <div class="subtitle">Récupération de votre compte</div>
        </div>
        <div class="content">
            <div style="font-size: 20px; font-weight: bold; margin-bottom: 16px;">
                Salut <span class="username">{{ $user->username }}</span>
            </div>
            <div style="font-size: 15px; color: #334155; line-height: 1.6; margin-bottom: 24px;">
                Vous avez demandé une réinitialisation de votre mot de passe pour votre compte <strong style="color: #1e293b;">Collector's Hub</strong>.<br><br>
                Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
            </div>
            <a href="{{ $actionUrl }}" class="button">Réinitialiser mon mot de passe</a>
            <div class="info">
                Ce lien expirera dans <strong style="color: #1e293b;">60 minutes</strong>.<br>
                Si vous n'avez pas demandé cette récupération, ignorez cet email.<br>
                Votre mot de passe actuel reste inchangé jusqu'à ce que vous en définissiez un nouveau.
            </div>
        </div>
        <div class="footer">
            Vous recevez cet email car une demande de récupération de mot de passe a été effectuée pour votre compte Collector's Hub.<br>
            📧 Contact : <a href="mailto:contact@collectorshub.fr" class="link">contact@collectorshub.fr</a>
            <div class="small">
                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                <span class="link">{{ $actionUrl }}</span>
            </div>
        </div>
    </div>
</body>
</html>
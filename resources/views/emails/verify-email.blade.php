<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre compte CollectorsHub</title>
</head>
<body style="margin: 0; padding: 0; background-color: #001e29; font-family: Arial, sans-serif; color: #e2e8f0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #001e29;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #001e29; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3); border: 1px solid #3b82f6;">
                    <tr>
                        <td style="background-color: #3b82f6; color: white; text-align: center; padding: 40px 30px;">
                            <div style="font-size: 28px; font-weight: bold; margin-bottom: 8px;">🎮 Collector's Hub</div>
                            <div style="font-size: 16px; opacity: 0.9;">Votre aventure vous attend</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #001e29; padding: 40px 30px; text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #e2e8f0; margin-bottom: 20px;">
                                Salut <span style="color: #3b82f6; font-weight: bold;">{{ $notifiable->username }}</span>
                            </div>
                            
                            <div style="font-size: 16px; color: #94a3b8; line-height: 1.6; margin-bottom: 30px;">
                                Bienvenue dans l'univers de <strong style="color: #e2e8f0;">Collector's Hub</strong><br>
                                Pour commencer votre collection et accéder à toutes les fonctionnalités, 
                                vous devez vérifier votre adresse email.
                            </div>
                            
                            <table cellpadding="0" cellspacing="0" style="margin: 20px auto;">
                                <tr>
                                    <td style="background-color: #3b82f6; border-radius: 16px; padding: 16px 40px;">
                                        <a href="{{ $actionUrl }}" style="color: white; text-decoration: none; font-weight: bold; font-size: 16px; display: block;">
                                             Vérifier mon compte
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <div style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid #3b82f6; border-radius: 16px; padding: 20px; margin: 20px 0; color: #94a3b8;">
                                <div>
                                    Ce lien de vérification expirera dans <strong style="color: #e2e8f0;">60 minutes</strong><br>
                                    Si vous n'avez pas créé de compte, ignorez cet email.
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #0f2937; color: #e2e8f0; padding: 30px; text-align: center; border-top: 1px solid rgba(59, 130, 246, 0.1);">
                            <div style="font-size: 12px; margin-bottom: 15px; color: #64748b;">
                                Vous recevez cet email car vous avez créé un compte sur Collector's Hub.
                            </div>
                            <div style="font-size: 12px; margin-bottom: 15px; color: #64748b;">
                                📧 Si vous avez des questions, contactez-nous à : 
                                <a href="mailto:contact@collectorshub.fr" style="color: #3b82f6; text-decoration: none;">contact@collectorshub.fr</a>
                            </div>
                            <div style="color: #64748b; font-size: 10px; margin-top: 20px; word-break: break-all; background-color: rgba(0, 0, 0, 0.2); padding: 8px; border-radius: 6px; border: 1px solid rgba(59, 130, 246, 0.05);">
                                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                                <span style="color: #3b82f6; font-size: 9px;">{{ $actionUrl }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifiez votre email - PokéArena</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #1a1a2e; color: #e5e7eb;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 1px rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); padding: 40px 20px; text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 10px;">🎮</div>
                            <h1 style="margin: 0; font-size: 32px; font-weight: bold; color: #ffffff; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                PokéArena
                            </h1>
                            <p style="margin: 10px 0 0; font-size: 16px; color: rgba(255, 255, 255, 0.9);">
                                L'aventure commence ici !
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Welcome Message -->
                            <div style="text-align: center; margin-bottom: 30px;">
                                <div style="font-size: 64px; margin-bottom: 20px;">✉️</div>
                                <h2 style="margin: 0 0 10px; font-size: 24px; font-weight: bold; color: #e5e7eb;">
                                    Bienvenue, {{ $user->username }} !
                                </h2>
                                <p style="margin: 0; font-size: 16px; color: #9ca3af; line-height: 1.6;">
                                    Vous êtes à un clic de rejoindre l'arène !
                                </p>
                            </div>

                            <!-- Info Box -->
                            <div style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                                <p style="margin: 0; font-size: 14px; color: #e5e7eb; line-height: 1.6; text-align: center;">
                                    Pour commencer votre aventure de dresseur Pokémon, vous devez d'abord vérifier votre adresse email. 
                                    Cliquez sur le bouton ci-dessous pour activer votre compte.
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="{{ $url }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 12px; box-shadow: 0 8px 16px rgba(139, 92, 246, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1); transition: all 0.3s ease;">
                                    🚀 Vérifier mon email
                                </a>
                            </div>

                            <!-- Alternative Link -->
                            <div style="background: rgba(255, 255, 255, 0.05); border-radius: 8px; padding: 20px; margin-top: 30px;">
                                <p style="margin: 0 0 10px; font-size: 12px; color: #9ca3af; text-align: center;">
                                    Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
                                </p>
                                <p style="margin: 0; font-size: 12px; word-break: break-all; color: #60a5fa; text-align: center;">
                                    <a href="{{ $url }}" style="color: #60a5fa; text-decoration: underline;">{{ $url }}</a>
                                </p>
                            </div>

                            <!-- Security Notice -->
                            <div style="margin-top: 30px; padding: 15px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px;">
                                <p style="margin: 0; font-size: 13px; color: #fbbf24; text-align: center;">
                                    <strong>⚡ Important :</strong> Ce lien expire dans 60 minutes pour votre sécurité.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Features Section -->
                    <tr>
                        <td style="padding: 0 30px 40px;">
                            <h3 style="margin: 0 0 20px; font-size: 18px; font-weight: bold; color: #e5e7eb; text-align: center;">
                                Ce qui vous attend dans PokéArena :
                            </h3>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding: 10px;">
                                        <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px; padding: 20px;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">🎁</div>
                                            <h4 style="margin: 0 0 5px; font-size: 14px; font-weight: bold; color: #e5e7eb;">
                                                Starter Pokémon
                                            </h4>
                                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                                Choisissez votre compagnon
                                            </p>
                                        </div>
                                    </td>
                                    <td style="text-align: center; padding: 10px;">
                                        <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 12px; padding: 20px;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">⚔️</div>
                                            <h4 style="margin: 0 0 5px; font-size: 14px; font-weight: bold; color: #e5e7eb;">
                                                Combats épiques
                                            </h4>
                                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                                Défiez d'autres dresseurs
                                            </p>
                                        </div>
                                    </td>
                                    <td style="text-align: center; padding: 10px;">
                                        <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 20px;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">🏪</div>
                                            <h4 style="margin: 0 0 5px; font-size: 14px; font-weight: bold; color: #e5e7eb;">
                                                Marketplace
                                            </h4>
                                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                                Échangez vos Pokémon
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.2); padding: 30px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <p style="margin: 0 0 10px; font-size: 12px; color: #9ca3af;">
                                Cet email a été envoyé à {{ $user->email }}
                            </p>
                            <p style="margin: 0 0 10px; font-size: 12px; color: #9ca3af;">
                                Si vous n'avez pas créé de compte, ignorez simplement cet email.
                            </p>
                            <div style="margin-top: 20px;">
                                <p style="margin: 0; font-size: 12px; color: #6b7280;">
                                    © 2024 PokéArena. Tous droits réservés.
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
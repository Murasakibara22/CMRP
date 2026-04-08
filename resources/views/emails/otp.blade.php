<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de Vérification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px 8px 0 0;">
                            <img src="{{ asset('logoStaff.png') }}" alt="Logo" width="80" style="display: block; margin: 0 auto;">
                            <h1 style="color: #ffffff; margin: 20px 0 0; font-size: 28px; font-weight: 600;">Code de Vérification</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #333333; font-size: 16px; line-height: 24px; margin: 0 0 20px;">
                                Bonjour <strong>{{ $userName }}</strong>,
                            </p>

                            <p style="color: #666666; font-size: 15px; line-height: 24px; margin: 0 0 30px;">
                                Vous avez demandé à vous connecter à votre compte administrateur. Utilisez le code de vérification ci-dessous pour continuer :
                            </p>

                            <!-- OTP Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center" style="background-color: #f8f9fa; border: 2px dashed #667eea; border-radius: 8px; padding: 30px;">
                                        <div style="font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #667eea; font-family: 'Courier New', monospace;">
                                            {{ $otp }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #666666; font-size: 14px; line-height: 22px; margin: 30px 0 20px;">
                                <strong style="color: #333333;">⏱️ Ce code expire dans 10 minutes.</strong>
                            </p>

                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                                <p style="color: #856404; font-size: 13px; line-height: 20px; margin: 0;">
                                    <strong>⚠️ Sécurité :</strong> Si vous n'avez pas demandé ce code, veuillez ignorer cet email et changer votre mot de passe immédiatement.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center;">
                            <p style="color: #999999; font-size: 12px; line-height: 18px; margin: 0 0 10px;">
                                Cet email a été envoyé automatiquement, veuillez ne pas y répondre.
                            </p>
                            <p style="color: #999999; font-size: 12px; line-height: 18px; margin: 0;">
                                © {{ date('Y') }} Plateforme Gros. Tous droits réservés.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

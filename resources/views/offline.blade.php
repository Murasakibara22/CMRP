<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#405189">
    <title>CMRP — Hors ligne</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Nunito', -apple-system, sans-serif;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            text-align: center;
            color: #212529;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 32px;
            max-width: 360px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
        }
        .icon {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(64,81,137,.1);
            color: #405189;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
        }
        h1 { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
        p  { font-size: 14px; color: #878a99; line-height: 1.6; margin-bottom: 28px; }
        button {
            background: linear-gradient(135deg, #2d3a63, #405189);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 13px 32px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            font-family: inherit;
            transition: opacity .2s;
        }
        button:active { opacity: .85; }
        .logo {
            width: 56px; height: 56px;
            border-radius: 14px;
            margin: 0 auto 24px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="/images/icons/android/android-launchericon-192-192.png"
             alt="CMRP" class="logo">
        <div class="icon">📡</div>
        <h1>Vous êtes hors ligne</h1>
        <p>
            Vérifiez votre connexion internet et réessayez.<br>
            Vos données seront synchronisées dès la reconnexion.
        </p>
        <button onclick="window.location.reload()">
            Réessayer
        </button>
    </div>
</body>
</html>

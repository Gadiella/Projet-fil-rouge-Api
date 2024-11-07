<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation de mot de passe</title>
</head>
<body>
    <p>Bonjour {{ $username }},</p>

    <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
    <p>Voici votre code OTP pour finaliser la procédure de réinitialisation :</p>

    <h2>{{ $otpCode }}</h2>

    <p>Le code est valide pendant 10 minutes. Si vous n'avez pas demandé à réinitialiser votre mot de passe, veuillez ignorer cet email.</p>

    <p>Merci,<br>Archiva Nexus</p>
</body>
</html>

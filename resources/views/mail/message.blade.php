<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue sur notre plateforme</title>
</head>
<body>
    <h1>Bienvenue, {{ $username }} !</h1>
    <p>Merci de vous connecter à notre plateforme.</p>
    <p>Voici vos informations :</p>
    <p><strong>Nom d'utilisateur :</strong> {{ $username }}</p>
    <p><strong>Mot de passe :</strong> {{ $password }}</p>
</body>
</html>

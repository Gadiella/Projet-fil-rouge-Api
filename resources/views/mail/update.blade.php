<p>Bonjour,</p>
<p>Votre compte a été modifié avec succès. Voici vos nouveaux identifiants :</p>

<p><strong>Nom d'utilisateur :</strong> {{ $username }}</p>

@if ($newPassword)
    <p><strong>Mot de passe :</strong> {{ $newPassword }}</p>
@else
    <p>Votre mot de passe n'a pas été modifié.</p>
@endif

<p>Merci,</p>
<p>L'équipe de support</p>

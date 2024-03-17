<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body dir="rtl">
    <p>Bonjour !</p>
    <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de votre mot de passe.</p>
    <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
    <p><a href="{{ env('API_FRONT') }}/password-change?token={{ $token }}">Réinitialiser le mot de passe</a></p>
    <p>Si vous n'avez pas demandé la réinitialisation de votre mot de passe, aucune action supplémentaire n'est nécessaire.</p>
    <p>Merci !</p>
</body>
</html>

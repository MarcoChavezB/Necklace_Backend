<!DOCTYPE html>
<html>
<head>
    <title>Activación de cuenta</title>
</head>
<body>
<h2>Bienvenido a Necklace!</h2>
<br/>
Tu correo electrónico registrado es {{$user['email']}} , Por favor haz clic en el enlace de abajo para verificar tu correo electrónico y activar tu cuenta
Este codigo tiene una expiracion de 5 dias
<br/>
<a href="{{$url}}">Verificar correo electrónico</a>
</body>
</html>

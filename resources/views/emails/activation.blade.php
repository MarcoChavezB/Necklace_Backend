<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Activación de Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        h2 {
            color: #446dff;
        }
        p {
            color: #333;
        }
        a {
            display: inline-block;
            color: #fff;
            background-color: #446dff;
            padding: 10px 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Bienvenido a Necklace!</h2>
    <p>Hola {{$user['nombre']}},</p>
    <p>Por favor haz clic en el enlace de abajo para verificar tu correo electrónico y activar tu cuenta.</p>
    <p>Este enlace tiene una expiración de 5 días, verifica antes de que expire.</p>
    <a href="{{$url}}">Verificar Correo Electrónico</a>
</div>
</body>
</html>

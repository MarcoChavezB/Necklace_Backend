<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4CAF50;
        }
        p {
            color: #333;
        }
        img {
            width: 200px; /* Aumenta este valor para hacer la imagen más grande */
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ asset('svg/logotipo.svg') }}" alt="Logotipo">
    <h1>¡Registro Exitoso!</h1>
    <p>Tu cuenta ha sido activada con éxito.</p>
</div>
</body>
</html>

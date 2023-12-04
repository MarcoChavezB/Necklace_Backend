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
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ asset('svg/logotipo.svg') }}" alt="Logotipo">
    <h1>¡Verificacion Exitosa!</h1>
    <p>Tu email ha sido verificado con éxito.</p>
    <br>
    <p>Gracias por utilizar nuestros servicios.</p>
    <p>Ya puedes logearte en la app</p>
</div>
</body>
</html>

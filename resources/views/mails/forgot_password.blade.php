<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <title>Document</title>
    <style>
        *{
            font-family: 'Poppins', sans-serif;

            padding: 0%;
            margin: 0%;
        }
        body {
            width: 600px;
            margin: auto;
        }
        h1{
            font-family: 'Poppins', sans-serif;

        }
        p{
            font-family: 'Poppins', sans-serif;

        }
        div#section__title {
            width: 100%;
            margin: auto;
            text-align: center;
            padding-top: 30px;
            padding-bottom: 30px;
        }
        div#section__banner {
            background-image: url(https://dashboard.xlearn.com.co/iconos-xln/bg.png);
            background-size: cover;
            padding: 55px;
        }
        div#section__banner h2 {
            color: #fff;
            text-align: center;
        }
        div#section__banner span {
            background: #31fb84;
            color: #0a2332;
        }
        div#section__banner p {
            text-align: center;
            color: #fff;
        }
        div#section__info {
            width: 100%;
    padding-top: 50px;
    padding-bottom: 50px;
    position: relative;
    height: 10vh;
        }
        .columnas_mail_icon {
            width: 33%;
            float: left;
        }
        .columnas_mail_icon img {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
        }
        .columnas_mail_icon p {
            text-align: center;
            padding: 20px;
        }
        .content__button__activate {
            width: 100%;
            margin: auto;
            text-align: center;
            padding-top: 41px;
        }
        .content__button__activate .xln__btn__email {
            background: #31fb84;
            margin: auto;
            text-align: center;
            padding-top: 10px;
            padding-left: 30px;
            padding-right: 30px;
            padding-bottom: 10px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 5px;
            position: relative;
            top: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
            text-decoration: none;
            color: #0a2332
        }
        .content__button__activate p {
            margin-top: 50px;
            font-size: 20px;
            padding-bottom: 100px;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
        }

        @media (max-width:600px) {

            body {
                width: 100%;
            }
            .columnas_mail_icon {
                width: 50%;
                float: none;
                padding: 0px;
                padding-top: 40px;
                padding-bottom: 40px;
                margin: auto;
            }
            div#section__banner {
                background-size: cover;
                padding: 30px;
                padding-top: 75px;
                padding-bottom: 75px;
            }
            div#section__info {
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div id="section__title">
        <h1>Bienvenido a <img src="https://dashboard.xlearn.com.co/iconos-xln/logo.png"/></h1>
        <p>Tu registro ha sido exitoso</p>
    </div>

    <div id="section__banner">
        <h2>¡Es el momento de crear, transformar y aprender con <span>Xlearn!</span></h2>
        <p>Lleva tu empresa a otro nivel a través del entrenamiento de tus equipos, genera valor y desarrolla tus proyectos de innovación</p>
    </div>

    <div id="section__info">
        <div class="columnas_mail_icon">
            <img src="https://dashboard.xlearn.com.co/iconos-xln/1.png"/>
            <p>Contenido exclusivo según las necesidades de tus proyectos</p>
        </div>
        <div class="columnas_mail_icon">
            <img src="https://dashboard.xlearn.com.co/iconos-xln/2.png"/>
            <p>Contenido exclusivo según las necesidades de tus proyectos</p>
        </div>
        <div class="columnas_mail_icon">
            <img src="https://dashboard.xlearn.com.co/iconos-xln/3.png"/>
            <p>Contenido exclusivo según las necesidades de tus proyectos</p>
        </div>
    </div>

    <div class="content__button__activate">
        <a class="xln__btn__email" href="{{ $urlRecover }}" target="_blank" data-saferedirecturl="{{$urlRecover}}">Recuperar contraseña</a>
        <p>Para continuar explorando Xlearn</p>
        <p> Si no funciona el boton, copie y pegue el siguiente enlace en un navegador: <br>{{ $urlRecover }}.</p>
    </div>
</body>
</html>
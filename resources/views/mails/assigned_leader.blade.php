<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet"/>
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
            padding-bottom: 100px;
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
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
            text-align: center;
            margin-bottom: 135px;
        }
        .columnas_mail_icon {
            width: 50%;
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
        .parrafo__span__xln {
            background: #31fb84;
            color: #0a2332;
            font-weight: bold;
        }

        .footer {
            padding: 10px 0;
        }
        .redes{
            width: 100%;
            text-align: center;
        }
        .footer a {
            color: #00FF84;
            font-size: 17px;
            background-color: #0a2332;
            border-radius: 100%;
            padding-top: 10px;
            padding-bottom: 10px;
            padding-left: 15px;
            padding-right: 15px;
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
            p {
                padding-left: 30px;
                padding-right: 20px;
            }
           b {
                padding-left: 30px;
                padding-right: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="section__title">
        <h1>Bienvenido a <img src="https://dashboard.xlearn.com.co/iconos-xln/logo.png"/></h1>
        <p>Tu asignación de cursos ha sido exitosa</p>
    </div>

    <div id="section__banner">
        <h2>¡Es el momento de crear, transformar y aprender con  <span>Xlearn!</span></h2>
    </div>

    <br/>
    <br/>
    <p>Ahora es momento de ingresar a la plataforma y crear tu ruta de aprendizaje, conoce como hacerlo en el siguiente video: <a href="https://youtu.be/CxGjAg9CR6E" target="_blank">https://youtu.be/CxGjAg9CR6E</a>    </p>
    <br/>
    <p><b>Usuario:</b> {{ $information['email'] }}</p>
    <p><b>Contraseña:</b> {{ $information['password'] }}</p>
    <br/>
    <br/>

    <div class="content__button__activate">
        <a class="xln__btn__email" href="{{ $information['urlConfimation'] }}" target="_blank" data-saferedirecturl="{{$information['urlConfimation']}}">¡Ingresa aquí!</a>
        <p>Para continuar explorando Xlearn</p>

    </div>


    <p style="text-align: center;"> Si no funciona el boton, copie y pegue el siguiente enlace en un navegador: <br>{{ $information['urlConfimation'] }}.</p>
    <br>
    <br>
    <footer class="footer">
        <div class="container redes text-center">
            <a href="https://www.facebook.com/XLearn10XThinking" target="_blank"><i class="fa fa-facebook"></i></a>
            <a href="https://twitter.com/XLearn2" target="_blank"><i class="fa fa-twitter"></i></a>
            <a href="https://www.linkedin.com/in/xlearn-platform-409991240/" target="_blank"><i class="fa fa-linkedin"></i></a>
            <a href="https://www.youtube.com/channel/UCyS1OiIhWpylctMcbSkYgFQ" target="_blank"><i class="fa fa-youtube"></i></a>
        </div>
    </footer>

</body>
</html>
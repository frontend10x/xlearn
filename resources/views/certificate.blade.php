<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
        <title>Document</title>
        <style>
            body{
                font-family: 'Poppins', sans-serif;
                background-image: url(https://dashboard.xlearn.com.co/certificado-xlearn.jpg);
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: 100%;
                margin: 0px;
                text-transform: uppercase;
            }
            @page {margin:0px;}
            .contenedor{
                text-align: center;
            }
            .course{
                font-size: 20px;
                letter-spacing: 3px;
                margin-top: 25%;
                margin-bottom: -1%;
                color: #497278;
                font-weight: 400
            }
            .name{
                font-size: 55px;
                margin-top: 10px;
                letter-spacing: 1px;
            }
            .date{
                margin-top: 16%;
                margin-left: -34%;
                letter-spacing: 1px;
                font-weight: 400;
                color: #497278;
            }
            
        </style>
    </head>
    <body>

        <div class="contenedor">
            <h3 class="course">{{ $course}}.</h3>
            <h1 class="name">{{$user}}</h1>
            <p class="date">{{$date}}</p> 
        </div>

    </body>
</html>
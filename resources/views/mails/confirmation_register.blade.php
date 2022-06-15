<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Confirmación de correo</title>
</head>
<body>
    <p>Hola! este es un correo de confirmación de pruebas.</p>
    <p>Para activar su usuario, por favor presione sobre el siguiente enlace</p>
    
    <td align="center" width="250" height="40" bgcolor="#5887f5" style="border-radius:5px;color:#ffffff;display:block">
        <a href="{{ $urlConfimation }}" style="color:#ffffff;font-size:16px;font-family:Helvetica,sans-serif;font-size:18px;text-decoration:none;line-height:40px;width:100%;display:inline block" target="_blank" data-saferedirecturl="{{$urlConfimation}}">
            Activar ahora            
        </a>
    </td>
    <br>
    Si no funciona, copie y pegue el siguiente enlace en un navegador: {{ $urlConfimation }}.
</body>
</html>
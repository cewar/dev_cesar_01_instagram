<?php
require_once(__DIR__ . '/app/instagram.php');

# SI VIENE EL CODIGO...
if (isset($_GET['code'])) {
    try {
        # TRATO DE OBTENER EL TOKEN CON EL CODIGO
        $access_token = @GetAccessToken($_GET['code']);
        # SI LO OBTENGO...
        if ($access_token) {
            # GUARDO EL TOKEN EN LA SESIÓN
            saveSessionAcessToken($access_token);
        } else {
            # SI NO, TRATO DE OBTENER EL TOKEN DE LA SESIÓN
            $access_token = getSessionAccessToken();
        }
        # TRATO DE OBTENER EL USUARIO
        $user_info = @GetUserProfileInfo($access_token);
        # SI NO LO OBTENGO, MUESTRO UN MENSAJE DE ERROR
        if ($user_info->code !== 200) {
            echo "Su sesión ha expirado, vuelve a la pantalla anterior para obtener tu nuevo codigo";
        # EN CASO CONTRARIO MUESTRO EL RESULTADO (SI ME SIGUE O NO)
        } else {
            echo responseMessage($user_info, $access_token);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }
    # SI NO VIENE EL CODIGO...
} else {
    # TRATO DE OBTENER EL TOKEN DE LA SESIÓN...
    $access_token = getSessionAccessToken();
    # TRATO DE OBTENER EL USUARIO
    $user_info = @GetUserProfileInfo($access_token);
    #LA MISMA EXPLICACIÓN DEL CODIGO ANTERIOR...
    if ($user_info->code !== 200) {
        echo "Su sesión ha expirado, vuelve a la pantalla anterior para obtener tu nuevo codigo";
    } else {
        echo responseMessage($user_info, $access_token);
    }
}

function responseMessage($user_info, $access_token)
{
    $heFollowsMe = checkMyFollowers($user_info, $access_token);
    if ($heFollowsMe) {
        return "Si, tú si me sigues :D";
    } else {
        return "No, tú aún no me sigues :(";
    }
}

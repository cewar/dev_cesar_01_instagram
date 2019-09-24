<?php

# CONFIGURACIÓN DE LA API PARA PODER HACER LOGIN
define('INSTAGRAM_CLIENT_ID', 'ae6c42018c7c44be92e16ac03518cbbc');
define('INSTAGRAM_CLIENT_SECRET', '248b7ab0032641feb04282f443aa9b4c');
define('INSTAGRAM_REDIRECT_URI', 'http://45.79.42.199/callback.php');

# CREDENCIALES DE LA CUENTA A LA QUE QUEREMOS OBTENER LOS SEGUIDORES
define('INSTAGRAM_MASTER_USERNAME', 'instamastergm');
define('INSTAGRAM_MASTER_PASSWORD', 'insta.master01');

require_once(__DIR__ . '/vendor/autoload.php');

# DEPENDENCIA PARA EJECUTAR EL SCRAP
use InstagramScraper\Instagram;

# DEPENDENCIA PARA EJECUTAR PETICIONES Y CONVERTIR DATOS
use Unirest\Request;
use Unirest\Request\Body;

# OBTENER TOKEN DE INSTAGRAM
function GetAccessToken($code)
{
    # https://www.instagram.com/developer/authentication/
    $url = 'https://api.instagram.com/oauth/access_token';

    $body = array(
        "client_id" => INSTAGRAM_CLIENT_ID,
        "redirect_uri" => INSTAGRAM_REDIRECT_URI,
        "client_secret" => INSTAGRAM_CLIENT_SECRET,
        "code" => $code,
        "grant_type" => "authorization_code"
    );

    $data = Request::post($url, [], $body);
    return $data->body->access_token;
}

# OBTENER INFORMACIÓN BASICA DEL USUARIO
function GetUserProfileInfo($access_token)
{
    # https://www.instagram.com/developer/endpoints/users/
    $url = "https://api.instagram.com/v1/users/self/?access_token=$access_token";
    $data = Request::get($url);
    return $data;
}

# OBTENER MIS SEGUIDORES
function getMyFollowers()
{
    try {
        //code...
        $instagram = Instagram::withCredentials(INSTAGRAM_MASTER_USERNAME, INSTAGRAM_MASTER_PASSWORD, __DIR__ . '/app/cache');
        $instagram->login();
        sleep(2);
        $followers_array = [];
        $account = $instagram->getAccount(INSTAGRAM_MASTER_USERNAME);
        sleep(1);
        $followers = $instagram->getFollowers($account->getId(), 9999, 999, true);
        foreach ($followers as $key => $follower) {
            $username = $follower["username"];
            $followers_array[] = $username;
        }
        return $followers_array;
    } catch (\Throwable $th) {
        var_dump($th->getMessage());
    }
}

# OBTENER LA URL DE AUTORIZACIÓN
# https://www.instagram.com/developer/authorization/
function getLoginUrl()
{
    return 'https://api.instagram.com/oauth/authorize/?client_id=' . INSTAGRAM_CLIENT_ID . '&redirect_uri=' . urlencode(INSTAGRAM_REDIRECT_URI) . '&response_type=code&scope=basic';
}

# GUARDA EL TOKEN EN UNA SESIÓN
function saveSessionAcessToken($access_token)
{
    session_start();
    $_SESSION["access_token"] = $access_token;
}

# OBTENER EL TOKEN DE LA SESIÓN
function getSessionAccessToken()
{
    session_start();
    return (isset($_SESSION["access_token"]) ? $_SESSION["access_token"] : '');
}

# BUSCAR EN MI LISTA DE SEGUIDORES EL USUARIO ACTUAL
function checkMyFollowers($user_info, $access_token)
{
    $user_username = $user_info->body->data->username;
    $followers = @getMyFollowers($access_token);
    if (heFollowsMe($user_username, $followers)) {
        return true;
    } else {
        return false;
    }
}

# VER SI SE ENCUENTRA EN MI LISTA DE SEGUIDORES
function heFollowsMe($username, $followers)
{
    return in_array($username, $followers);
}

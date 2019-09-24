<?php
require_once(__DIR__ . '/app/instagram.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <a href="<?=getLoginUrl(); ?>">
        Verifica si me sigues en Instagram
    </a>
</body>

</html>
<?php

session_start([
    'name' => "minha sessão",
    'cookie_lifetime' => 60 * 60 * 24,
    'cookie_path' => '/',
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

$_SESSION["usuario"] = "admin";

setcookie('cookie', "valor do cookie", time() + 60 * 60);

setcookie('teste', "testando", [
    'expires' => time() + 60 * 60,
    'path' => '/', 
    'secure' => true, 
    'httponly' => true,
    'samesite' => 'Strict'
]);

?>

<h1>Dashboard</h1>
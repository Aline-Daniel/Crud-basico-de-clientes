<?php
require_once __DIR__ . '/seguranca.php';
exigir_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Metodo nao permitido.');
}
verificar_csrf();
$_SESSION = [];
session_regenerate_id(true);
header('Location: login.php', true, 303);
exit;

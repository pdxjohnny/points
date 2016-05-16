<?php
// Requires
require_once('/var/www/lib/all.php');
header('Content-Type: application/json');

$args = array(
    'id'		=> FILTER_VALIDATE_INT,
    'username'	=> FILTER_VALIDATE_EMAIL,
    'password'	=> FILTER_SANITIZE_ENCODED,
);

$user = client_input($args);
var_dump($user);

$database = new Database;
$user = $database->login_user($user);
if ($user == false) {
    $err = new ErrorResponse;
    $err->code = 401;
    $err->message = "Invalid Login";
    $err->render();
    return;
}

echo json_encode_utf8($user);
?>

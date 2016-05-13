<?php
// Requires
require_once('lib/all.php');
header('Content-Type: application/json');

$args = array(
    'id'		=> FILTER_VALIDATE_INT,
    'username'	=> FILTER_SANITIZE_ENCODED,
    'password'	=> FILTER_SANITIZE_ENCODED,
);

$user = filter_input_array(INPUT_GET, $args);

function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}

$res = array(
    'user'  => $database->create_user($user)
);
echo json_encode(utf8ize($res));
?>

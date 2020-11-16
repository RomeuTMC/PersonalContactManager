<?php
header("Content-Type: application/json; charset=UTF-8");
define("LOCKED", "Acesso Proibido, ou bloqueado.");
require_once "_header.php";
if (file_exists($route[0].'.php')) {
    include_once $route[0].'.php';
} else {
    __out(array("erro"=>"Endpoint Inválido:".$route[0]), 400);
}
__out($_MSG, 200);
?>
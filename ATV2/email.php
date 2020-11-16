<?php
if (!defined("LOCKED")) die(LOCKED);
switch ($metodo){
case "POST":
    $_MSG['DATA']=email_novo($route[1]);
    break;
case "PATCH":
    $_MSG['DATA']=email_atualiza($route[1]);
    break;
case "DELETE":
    $_MSG['DATA']=email_apaga($route[1]);
    break;
default:
    __out(array("erro"=>"METODO INVÁLIDO - $metodo"), 405);
    break;
}

function email_novo($nId = 0) {
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $TipoEmail=filter_var($_POST['TipoEmail'], FILTER_SANITIZE_NUMBER_INT);
    $Email=filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);
    $stmt = $db->prepare(
        "INSERT INTO emails (nId, nIdContato, eTipo, uEmail)
        VALUES (NULL, '$nId', '".$TipoEmail."', '".$Email."');"
    );
    $stmt->execute();
    return $db->lastInsertId();
}

function email_atualiza($nId = 0) {
    parse_str(file_get_contents('php://input'), $_PATCH);
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $TipoEmail=filter_var($_PATCH['TipoEmail'], FILTER_SANITIZE_NUMBER_INT);
    $Email=filter_var($_PATCH['Email'], FILTER_SANITIZE_STRING);
    $stmt = $db->prepare("UPDATE emails SET eTipo='".$TipoEmail."', uEmail='".$Email."' WHERE nId=$nId;" );
    $stmt->execute();
    return $nId;
}

function email_apaga($nId = 0) {
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $stmt = $db->prepare("DELETE FROM emails WHERE nId=$nId");
    $stmt->execute();
    return $nId;
}
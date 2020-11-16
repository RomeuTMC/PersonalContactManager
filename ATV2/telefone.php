<?php
if(!defined("LOCKED")) {
    die(LOCKED);
}
switch ($metodo){
case "POST":
    $_MSG['DATA']=telefone_novo($route[1]);
    break;
case "PATCH":
    $_MSG['DATA']=telefone_atualiza($route[1]);
    break;
case "DELETE":
    $_MSG['DATA']=telefone_apaga($route[1]);
    break;
default:
    __out(array("erro"=>"METODO INVÁLIDO - $metodo"), 405);
    break;
}

function telefone_novo($nId = 0) {
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $TipoFone=filter_var($_POST['TipoFone'], FILTER_SANITIZE_NUMBER_INT);
    $Fone=filter_var($_POST['Fone'], FILTER_SANITIZE_STRING);
    if ($fones<>count($aFone)) {
        __out(array("erro"=>"Erro nos DADOS DE TELEFONES"), 400);
    }
    $stmt = $db->prepare(
        "INSERT INTO fones (nId, nIdContato, eTipo, sValor)
        VALUES (NULL, '$nId', '".$TipoFone."', '".$Fone."');"
    );
    $stmt->execute();
    return $db->lastInsertId();
}

function telefone_atualiza($nId = 0) {
    parse_str(file_get_contents('php://input'), $_PATCH);
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $TipoFone=filter_var($_PATCH['TipoFone'], FILTER_SANITIZE_NUMBER_INT);
    $Fone=filter_var($_PATCH['Fone'], FILTER_SANITIZE_STRING);
    $stmt = $db->prepare("UPDATE fones SET eTipo='".$TipoFone."', sValor='".$Fone."' WHERE nId=$nId;");
    $stmt->execute();
    return $nId;
}

function telefone_apaga($nId = 0) {
    global $db;
    if($nId==0){
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $stmt = $db->prepare("DELETE FROM fones WHERE nId=$nId");
    $stmt->execute();
    return $nId;
}

<?php
/**
 *  Arquivo com todos os métodos para a API do contato
 */
if (!defined("LOCKED")) {
    die(LOCKED);
}
switch ($metodo){
case "POST":
    $_MSG['DATA']=contato_novo();
    break;
case "GET":
    $_MSG['DATA']=contato_lista();
    break;
case "PATCH":
    $_MSG['DATA']=contato_atualiza($route[1]);
    break;
case "DELETE":
    $_MSG['DATA']=contato_apaga($route[1]);
    break;
case "VIEW":
    $_MSG['DATA']=contato_visualiza($route[1]);
    break;
default:
    __out(array("erro"=>"METODO INVÁLIDO - $metodo"),405);
    break;
}

function contato_novo() {
    global $db;
    $sNomeCompleto=filter_var($_POST['sNomeCompleto'], FILTER_SANITIZE_STRING);
    $tObservacao=filter_var($_POST['tObservacao'], FILTER_SANITIZE_STRING);
    $aTipoFone=filter_var_array($_POST['aTipoFone'], FILTER_SANITIZE_NUMBER_INT);
    $aFone=filter_var_array($_POST['aFone'], FILTER_SANITIZE_STRING);
    $aTipoEmail=filter_var_array($_POST['aTipoEmail'], FILTER_SANITIZE_NUMBER_INT);
    $aEmail=filter_var_array($_POST['aEmail'], FILTER_SANITIZE_EMAIL);
    $fones=count($aTipoFone);
    if ($fones<>count($aFone)) {
        __out(array("erro"=>"Erro nos DADOS DE TELEFONES"), 400);
    }
    $emails=count($aTipoEmail);
    if ($emails<>count($aEmail)) {
        __out(array("erro"=>"Erro nos DADOS DE EMAIL"), 400);
    }
    $stmt = $db->prepare(
        "INSERT INTO contatos (nId,sNomeCompleto,tObservacao)
        VALUES (NULL, '$sNomeCompleto', '$tObservacao');"
    );
    $stmt->execute();
    $nId=$db->lastInsertId();
    for ($i=0; $i<$fones; $i++) {
        $stmt = $db->prepare(
            "INSERT INTO fones (nId, nIdContato, eTipo, sValor)
            VALUES (NULL, '$nId', '".$aTipoFone[$i]."', '".$aFone[$i]."');"
        );
        $stmt->execute();
    }
    for ($i=0; $i<$emails; $i++) {
        $stmt = $db->prepare(
            "INSERT INTO emails (nId, nIdContato, uEmail, eTipo)
            VALUES (NULL, '$nId', '".$aEmail[$i]."', '".$aTipoEmail[$i]."');"
        );
        $stmt->execute();
    }
    return $nId;
}

function contato_lista() {
    global $_MSG;
    global $db;
    $results=SqlQuery("SELECT nId,sNomeCompleto from contatos");
    $_MSG['REGISTERS']=count($results);
    return $results;
}

function contato_atualiza($nId = 0){
    parse_str(file_get_contents('php://input'), $_PATCH);
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $sNomeCompleto=filter_var($_PATCH['sNomeCompleto'], FILTER_SANITIZE_STRING);
    $tObservacao=filter_var($_PATCH['tObservacao'], FILTER_SANITIZE_STRING);
    $stmt = $db->prepare(
        "UPDATE contatos SET sNomeCompleto='".$sNomeCompleto."', tObservacao='".$tObservacao."'
        WHERE nId=".$nId.";"
    );
    $stmt->execute();
    return $nId;
}

function contato_apaga($nId = 0) {
    global $db;
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    }
    $stmt = $db->prepare("DELETE FROM emails WHERE nIdContato=$nId");
    $stmt->execute();
    $stmt = $db->prepare("DELETE FROM fones WHERE nIdContato=$nId");
    $stmt->execute();
    $stmt = $db->prepare("DELETE FROM contatos WHERE nId=$nId");
    $stmt->execute();
    return $nId;
}

function contato_visualiza($nId = 0) {
    if ($nId==0) {
        __out(array("erro"=>"INVALID PROCEDURE - ID"), 406);
    } 
    global $db;
    $results=SqlQuery("SELECT nId,sNomeCompleto,tObservacao from contatos WHERE nId=$nId");
    $data['contato']=$results;
    $results=SqlQuery("SELECT nId,eTipo,sValor from fones WHERE nIdContato=$nId");
    $data['fones']=$results;
    $results=SqlQuery("SELECT nId,eTipo,uEmail from emails WHERE nIdContato=$nId");
    $data['emails']=$results;
    return $data;
}
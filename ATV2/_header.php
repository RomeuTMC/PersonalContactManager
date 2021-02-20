<?php
/** 
 * Funções gerais e startup do sistema 
 * PHP Version 7.4
 */

if (!defined("LOCKED")) {
    die(LOCKED);
}

date_default_timezone_set('America/Sao_Paulo'); //garante que a TIMEZONE esteja correta
error_reporting(E_ERROR);
ini_set("display_errors", true);

//Conecta ao Banco de Dados
$db=DBConect();

//Autenticação BASIC AUTH
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.0 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="Acesso Restrito'.
        '",qop="auth",nonce="'.uniqid().'",opaque="'.md5('Acesso Restrito').'"'
    );
    header('HTTP/1.0 401 Unauthorized');
    __out(array("erro"=>"Falha na Autenticação, usuário ou senha não fornecidos"),401);
} else {
    //busca usuário e senha na base de dados e responde
    $user=filter_var($_SERVER['PHP_AUTH_USER'], FILTER_SANITIZE_EMAIL);
    $pass=filter_var($_SERVER['PHP_AUTH_PW'], FILTER_SANITIZE_STRING);
    $results=SqlQuery("SELECT nId, sNomeCompleto, sEmail, sPassword FROM Users WHERE sEmail='$user' limit 1");
    if (count($results)<>1 ) {
        __out(array("erro"=>"Username Inválido"),401);
    }
    if (!password_verify($pass, $results[0]['sPassword'])) {
        __out(array("erro"=>"Usename e Senha Não Conferem"), 401);
    }
}

//Roteamento
if (isset($_GET['route'])) {
    $route = $_GET['route'];
    $route = explode('/', $route);
    $route = filter_var_array($route, FILTER_SANITIZE_STRING);
} else {
    __out(array("erro"=>"NO END POINT ESPECIFIED"), 400);
}

//Verbo HTTP do metodo
if (isset($_SERVER['REQUEST_METHOD'])) {
    $metodo=$_SERVER['REQUEST_METHOD'];
} else {
    __out(array("erro"=>"METHOD INVALIDO"), 405);
}

// FUNÇÕES DE USO DO SISTEMA
/** 
 * Conexão Banco de Dados
 * */
function DBConect(){
    try {
        $db1 = new PDO("sqlite:PersonalContactManager.db3");
        $db1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e) {
        echo 'Error: ';
        echo $e->getMessage();
        __out(array("erro"=>"Falha no BANCO DE DADOS"),409);
    }
    return $db1;
}

/**
 * Chamada, que já faz o tratamento de erro, e da a saida
 *  formatada em Array do SELECT, e permite uso de 
 * Statment Prepare
 * @return Array
 */
function SqlQuery($sql, array $pr = null){
    global $db;
    $r=$db->prepare("$sql");
    if (!$r->execute($pr)){
        $error= $r->errorInfo();
        $erro="SQL ERRO:".$error[0].'-'.$error[1].'-'.$error[2];
        __out(array("erro"=>"$erro"),409);
        return FALSE;
    };
    $r=$r->fetchAll(PDO::FETCH_ASSOC);
    return $r;
}

/**
 * Faz a saída da API com os cabeçalhos e respostas JSON
 */
function __out($_msg=array(), $excode=200) {
    http_response_code($excode);
    header('HTTP/1.0 $excode Error');
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    if ($excode=200) {
        $status=array("status"=>"$excode","erro"=>"Sucesso!");
    } else { 
        $status=array("status"=>"$excode");
    }
    $_msg=array_merge($status, $_msg);
    echo json_encode($_msg);
    exit($excode);
}
?>
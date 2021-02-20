<?php
function is_balanced(string $str){
    $pilha = array();
    $str = str_split($str);
    $len=count($str);
    if(($len%2)!=0){
        return FALSE;
    }
    $ct=0;
    $ignore=array();
    for($i=0; $i<$len; $i++){
        if($str[$i] == '{' || $str[$i] == '[' || $str[$i] == '('){
            array_push($pilha, $str[$i]);
            $ct++;
            continue;
        } else {
            if(count($pilha)==0){
                if($ct==0){
                    return FALSE;
                }
                continue;
            }
            $last=end($pilha);
            if(($str[$i]==")") && ($last!="(")){
                return FALSE;
            } else {
                array_pop($pilha);
            }
            if(($str[$i]=="]") && ($last!="[")){
                return FALSE;
            } else {
                array_pop($pilha);
            }
            if(($str[$i]=="}") && ($last!="{")){
                return FALSE;
            } else {
                array_pop($pilha);
            }
        }
    }
    if(empty($pilha)){
        return TRUE;
    } else {
        return FALSE;
    }
}

echo "<h1>Testes</h1>";
$teste=array(
    "()",
    "[]",
    "{}",
    "{[()]}",
    "(){}[]",
    "[{()}](){}",
    "[]{()",
    "[{)]",
    "([{}])",
    "((()))",
    "[{}]([]){}{{{}}}",
    "}{()",
    ")(",
    ")(()",
    "(b+a)",
    "())(",
    ".()",
    "().()",
    "( )"
);
$len=count($teste);
for($i=0; $i<$len; $i++){
    if(is_balanced($teste[$i])){
        echo "<font color='#0F0'>TRUE</font> - ".$teste[$i]."</br>";
    } else {
        echo "<font color='#F00'>FALSE</font> - ".$teste[$i]."</br>";
    }
}
?>
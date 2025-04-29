<?php
function validaInput($dados): string {
    $dados = trim($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
   // return htmlspecialchars(trim($dados));
}

function FunctionName()  {
    
}

?>
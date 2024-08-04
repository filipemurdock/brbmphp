<?php
//SAM - EDITE SOMENTE ABAIXO
    $servidor = "localhost";
    $usuario = "oswald";
    $senha = "Testes@009";
    $dbname = "oswald";
    
  
    $conn1 = mysqli_connect($servidor, $usuario, $senha, $dbname);
    
    if(!$conn1){
        die("Falha na conexao: " . mysqli_connect_error());
    } else {
        
    }
//SAM - NAO PRECISA COLOCAR SEU DOMINIO 
    define('PATH', realpath('.'));
    define('SUBFOLDER', false);
    define('URL', 'https://' . $_SERVER["SERVER_NAME"]); //SAM - NAO MEXER
    define('ADMIN_URL',  'admin'); //SAM - NÃO MEXER
    define('SCRIPTAPI', 'https://' . $_SERVER["SERVER_NAME"]); //SAM - NAO MEXER
    ini_set('display_errors', 0);
    date_default_timezone_set('America/Sao_Paulo');

    return [
        'db' => [
            'name'    =>  $dbname,
            'host'    =>  $servidor,
            'user'    =>  $usuario,
            'pass'    =>  $senha,
            'charset' =>  'utf8mb4' 
        ],
        'conn1' => $conn1
    ];
?>
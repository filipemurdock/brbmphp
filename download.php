<?php
error_log("Download script reached"); // Adicione esta linha para verificar se o script está sendo executado

// Checar se o usuário está logado
session_start();
if (!isset($_SESSION["neira_userlogin"]) || $_SESSION["neira_userlogin"] != 1) {
    header("Location: login.php");
    exit();
}

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['file'])) {
    die('No file specified.');
}

$file = urldecode($_GET['file']);
$filePath = __DIR__ . '/upload/uploads/' . $file; // Usando __DIR__ para garantir o caminho correto

if (file_exists($filePath)) {
    // Forçar o download do arquivo
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    flush(); // Limpar o buffer do sistema
    readfile($filePath);
    exit();
} else {
    echo 'Arquivo não encontrado.';
}
?>

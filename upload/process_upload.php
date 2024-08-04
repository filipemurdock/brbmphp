<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectar ao banco de dados
$dsn = 'mysql:host=localhost;dbname=oswald';
$username = 'oswald';
$password = 'Testes@009';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}

if (isset($_POST['order_id']) && isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $orderId = $_POST['order_id'];
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Verifica a extensão do arquivo
    $allowedExtensions = ['zip', 'rar'];
    if (in_array($fileExtension, $allowedExtensions)) {
        // Define o caminho para onde o arquivo será movido
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . $fileName;

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Atualiza o nome do arquivo na tabela orders (somente o nome do arquivo, sem o caminho)
            $query = "UPDATE orders SET files = :files WHERE order_id = :order_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':files', $fileName); // Armazena apenas o nome do arquivo
            $stmt->bindParam(':order_id', $orderId);
            
            if ($stmt->execute()) {
                echo "Arquivo carregado e atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o banco de dados.";
            }
        } else {
            echo "Erro ao mover o arquivo para o diretório de uploads.";
        }
    } else {
        echo "Extensão de arquivo não permitida.";
    }
} else {
    echo "Nenhum arquivo enviado ou erro no upload.";
}
?>

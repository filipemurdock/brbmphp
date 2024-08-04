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

// Consultar os pedidos onde a coluna 'files' é NULL
$query = 'SELECT order_id FROM orders WHERE files IS NULL';
$stmt = $pdo->prepare($query);

if (!$stmt->execute()) {
    die('Erro na execução da consulta: ' . implode(" ", $stmt->errorInfo()));
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar se a consulta retornou resultados
if (!$orders) {
    echo 'Nenhum pedido encontrado.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Upload de Arquivos</title>
</head>
<body>
    <h1>Upload de Arquivos</h1>
    <form action="process_upload.php" method="post" enctype="multipart/form-data">
        <label for="order_id">Selecione o Pedido:</label>
        <select name="order_id" id="order_id">
            <?php foreach ($orders as $order): ?>
                <option value="<?php echo htmlspecialchars($order['order_id']); ?>">
                    Pedido #<?php echo htmlspecialchars($order['order_id']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="file">Escolha o Arquivo:</label>
        <input type="file" name="file" id="file" required>
        <br>
        <button type="submit">Enviar Arquivo</button>
    </form>
</body>
</html>

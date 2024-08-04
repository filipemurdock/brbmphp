<?php
header('Content-Type: application/json');

// Conectar ao banco de dados
$host = 'localhost'; // Ajuste conforme necessário
$dbname = 'oswald'; // Ajuste conforme necessário
$username = 'oswald'; // Ajuste conforme necessário
$password = 'Testes@009'; // Ajuste conforme necessário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Definir o client_id (substitua com o valor apropriado conforme sua lógica de autenticação)
    session_start();
    if (!isset($_SESSION['client_id'])) {
        echo json_encode(['error' => 'Client ID não está definido na sessão.']);
        exit();
    }
    $client_id = $_SESSION['client_id'];

    // Preparar e executar a consulta para obter apenas os pedidos com category_id 57
    $stmt = $pdo->prepare("
        SELECT o.order_id AS id, o.order_create AS date, o.sms_number AS sms_number, o.order_url AS link, s.service_name AS service, o.order_status AS status, o.expiration_time
        FROM orders o
        INNER JOIN services s ON o.service_id = s.service_id
        WHERE o.client_id = :client_id AND s.category_id = 57
    ");
    $stmt->execute(['client_id' => $client_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($orders);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}



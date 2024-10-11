<?php
header('Content-Type: application/json');
session_start();

// Verifique se o neira_userid está na sessão e defina o client_id
if (isset($_SESSION['neira_userid'])) {
    $client_id = $_SESSION['neira_userid'];
} else {
    echo json_encode(['error' => 'Client ID não está definido na sessão.']);
    exit();
}

// Configurações do banco de dados
$host = 'localhost'; // Altere para o host do seu banco de dados
$db = 'oswald'; // Altere para o nome do seu banco de dados
$user = 'oswald'; // Altere para o nome de usuário do banco de dados
$pass = 'Testes@009'; // Altere para a senha do banco de dados
$charset = 'utf8mb4';

// Configuração do DSN para PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Cria a conexão com o banco de dados
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Falha na conexão: ' . $e->getMessage()]);
    exit();
}

// Busque os pedidos para o cliente logado e categoria 57
try {
    $query = "SELECT o.order_id AS id, o.order_create AS date, o.sms_number AS sms_number, o.order_url AS link, 
                     s.service_name AS service, o.order_status AS status, o.expiration_time
              FROM orders o
              INNER JOIN services s ON o.service_id = s.service_id
              WHERE o.client_id = :client_id AND s.category_id = 57";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(['client_id' => $client_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Falha ao buscar pedidos: ' . $e->getMessage()]);
    exit();
}

// Atualize a data de expiração para pedidos em progresso
foreach ($orders as &$order) {
    if (empty($order['expiration_time']) && $order['status'] === 'inprogress') {
        $expiration_time = date("Y-m-d H:i:s", strtotime($order['date'] . ' + 15 minutes'));
        try {
            $updateStmt = $conn->prepare("UPDATE orders SET expiration_time = :expiration_time WHERE order_id = :order_id");
            $updateStmt->execute(['expiration_time' => $expiration_time, 'order_id' => $order['id']]);
            $order['expiration_time'] = $expiration_time;
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Falha ao atualizar data de expiração: ' . $e->getMessage()]);
            exit();
        }
    }
}

// Retorne os dados em JSON
echo json_encode($orders);
?>

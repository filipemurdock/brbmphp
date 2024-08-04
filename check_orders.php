<?php
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
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Falha na conexão: ' . $e->getMessage()]);
    exit();
}

// Verifique se há pedidos com status "pending" ou "inprogress"
try {
    $query = "SELECT COUNT(*) AS count 
              FROM orders o
              INNER JOIN services s ON o.service_id = s.service_id
              WHERE s.category_id = 57 AND (o.order_status = 'pending' OR o.order_status = 'inprogress')";

    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo json_encode(['has_orders' => true]);
    } else {
        echo json_encode(['has_orders' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Falha ao verificar pedidos: ' . $e->getMessage()]);
    exit();
}
?>

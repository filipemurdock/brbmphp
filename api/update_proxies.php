<?php
session_start();

if (!isset($_SESSION["neira_userlogin"]) || $_SESSION["neira_userlogin"] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=oswald', 'oswald', 'Testes@009');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION['neira_userid'])) {
    $client_id = $_SESSION['neira_userid'];
} else {
    echo json_encode(['error' => 'client_id não definido']);
    exit;
}

$sql = "SELECT o.*, s.service_name, s.eligible_for_refill 
        FROM orders o 
        JOIN services s ON o.service_id = s.service_id
        JOIN categories c ON s.category_id = c.category_id
        WHERE o.client_id = :client_id 
          AND c.category_id = 52 
          AND s.service_name LIKE '%proxy%'";
$stmt = $pdo->prepare($sql);
$stmt->execute(['client_id' => $client_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as &$order) {
    $order_urls = explode("\n", trim($order['order_url']));
    $order['proxies'] = array_slice($order_urls, 0, $order['order_quantity']);
}

echo json_encode($orders);

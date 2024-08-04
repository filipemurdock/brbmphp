<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
file_put_contents('update_orders_debug.log', date('Y-m-d H:i:s') . " - update_orders.php foi chamado\n", FILE_APPEND);

// Conectar ao banco de dados
try {
    $conn = new PDO('mysql:host=localhost;dbname=oswald', 'oswald', 'Testes@009');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit();
}

// Defina variáveis de busca e paginação conforme necessário
$c_id = $user["client_id"]; // Certifique-se de definir esta variável corretamente
$search = ""; // Adapte seu critério de busca
$to = 25; // Número de itens por página

// Buscar e atualizar pedidos
$ordersQuery = $conn->prepare("SELECT o.*, s.service_name 
                               FROM orders o 
                               INNER JOIN services s ON o.service_id = s.service_id 
                               WHERE o.client_id = :c_id AND s.category_id = 57 $search 
                               ORDER BY o.order_id DESC 
                               LIMIT 0, $to");
$ordersQuery->execute(['c_id' => $c_id]);
$orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

// Atualiza a data de expiração para cada pedido, se ainda não estiver definida
foreach ($orders as &$order) {
    if (empty($order['expiration_time']) && $order['order_status'] === 'inprogress') {
        $expiration_time = date("Y-m-d H:i:s", strtotime($order['order_create'] . ' + 15 minutes'));
        $order_id = $order['order_id'];
        
        // Atualize a data de expiração no banco de dados
        $updateStmt = $conn->prepare("UPDATE orders SET expiration_time = :expiration_time WHERE order_id = :order_id");
        $updateStmt->execute(['expiration_time' => $expiration_time, 'order_id' => $order_id]);
        
        // Atualizar a ordem com a nova data de expiração
        $order['expiration_time'] = $expiration_time;
    }
}

// Gere o HTML atualizado para a tabela de pedidos
foreach ($orders as $order) {
    echo "<tr>";
    echo "<td>{$order['order_id']}</td>";
    echo "<td>{$order['order_url']}</td>";
    echo "<td>{$order['service_name']}</td>";
    echo "<td>{$order['expiration_time']}</td>";
    echo "</tr>";
}
?>

<?php
// Ativar exibição de erros para depuração
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
header('Content-Type: application/json');

$servername = "localhost";
$username = "oswald";
$password = "Testes@009";
$dbname = "oswald";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtendo dados do POST
    $data = json_decode(file_get_contents("php://input"));
    $order_id = $data->order_id;
    $proxy_url = $data->proxy_url; // Obtendo o proxy URL

    // Obter informações do pedido original
    $order_stmt = $conn->prepare("SELECT client_id, service_id FROM orders WHERE order_id = :order_id");
    $order_stmt->bindParam(':order_id', $order_id);
    $order_stmt->execute();
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $client_id = $order['client_id'];
        $service_id = $order['service_id'];

        // Consultar o service_name usando o service_id na tabela services
        $service_stmt = $conn->prepare("SELECT refill_type, refill_time, max_refills, service_name FROM services WHERE service_id = :service_id");
        $service_stmt->bindParam(':service_id', $service_id);
        $service_stmt->execute();
        $service = $service_stmt->fetch(PDO::FETCH_ASSOC);

        if ($service) {
            $service_name = $service['service_name'];
			$refill_type = $service["refill_type"];
            $refill_time = $service["refill_time"];
            $max_refills = $service["max_refills"];
			
            // Verificar o número de reabastecimentos
            $refill_count_stmt = $conn->prepare("SELECT COUNT(*) as refill_count FROM refill_status WHERE order_id = :order_id");
            $refill_count_stmt->execute(array("order_id" => $order_id));
            $refill_count = $refill_count_stmt->fetch(PDO::FETCH_ASSOC)["refill_count"];

            if ($refill_count >= $max_refills) {
                // Mostrar mensagem de erro quando o limite de reabastecimentos é atingido
                echo json_encode(['success' => false, 'message' => 'Você atingiu o limite de reabastecimentos para este pedido.']);
            } else {
                $creation_date = date('Y-m-d H:i:s');
                $ending_date = date('Y-m-d H:i:s', strtotime($creation_date . ' + ' . $refill_time . ' days')); // Exemplo de 30 dias após a criação

            // Preparar e executar a inserção na tabela refill_status
            $stmt = $conn->prepare("INSERT INTO refill_status (client_id, order_id, service_id, creation_date, ending_date, refill_status, service_name, order_url) VALUES (:client_id, :order_id, :service_id, :creation_date, :ending_date, 'Pending', :service_name, :order_url)");
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':service_id', $service_id);
            $stmt->bindParam(':creation_date', $creation_date);
            $stmt->bindParam(':ending_date', $ending_date);
            $stmt->bindParam(':service_name', $service_name);
            $stmt->bindParam(':order_url', $proxy_url); // Inserir o proxy URL

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode(['success' => false, 'message' => 'Database insert failed', 'error' => $errorInfo]);
				}
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Service not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

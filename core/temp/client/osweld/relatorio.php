
<?php

session_start(); // Inicia a sessão se ainda não estiver iniciada

// Verifica se o usuário está logado e se possui permissão
if (!isset($_SESSION["neira_userlogin"]) || $_SESSION["neira_userlogin"] != 1 || $user["client_type"] == 1) {
    header("Location: " . site_url('logout'));
    exit; // Encerra o script para evitar execução desnecessária
}

// Conexão com o banco de dados (substitua com suas configurações)
try {
    $conn = new PDO("mysql:host=localhost;dbname=oswald", "oswald", "Testes@009");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit; // Encerra o script em caso de erro de conexão
}

// Verificações adicionais, como verificação de SMS ou e-mail
if ($_SESSION["neira_userlogin"] == 1) {
    if ($settings["sms_verify"] == 2 && $user["sms_verify"] != 2) {
        header("Location: " . site_url('verify/sms'));
        exit;
    } elseif ($settings["mail_verify"] == 2 && $user["mail_verify"] != 2) {
        header("Location: " . site_url('verify/mail'));
        exit;
    }
}

// Processamento do pedido de refill ao clicar no botão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["refill_button"])) {
    // Captura dos dados necessários (substitua com os dados específicos do seu formulário ou botão)
    $order_id = $_POST["order_id"]; // Exemplo: $_POST["order_id"]
    $service_name = "Nome do serviço"; // Exemplo: $order["service_name"]
    $creation_date = date("Y-m-d H:i:s"); // Data atual
    $refill_status = "Pending"; // Status inicial do refill

    // Insere os dados na tabela refill_status
    $insert = $conn->prepare("INSERT INTO refill_status (client_id, order_id, service_name, creation_date, refill_status) 
                             VALUES (:client_id, :order_id, :service_name, :creation_date, :refill_status)");
    $insert->execute(array(
        "client_id" => $user["client_id"], // Substitua com a variável correta
        "order_id" => $order_id,
        "service_name" => $service_name,
        "creation_date" => $creation_date,
        "refill_status" => $refill_status
    ));

    // Verificação e feedback ao usuário
    if ($insert) {
        echo "Pedido de refill cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar pedido de refill.";
    }
}

?>
       
<?php

//Configurações gerais
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Verificação de login e condições adicionais
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["neira_userlogin"]) || $_SESSION["neira_userlogin"] != 1 || $user["client_type"] == 1) {
    header("Location:".site_url('logout'));
    exit;
}
  $title .= "Reposições | BMIlimitada";

if ($_SESSION["neira_userlogin"] == 1) {
    if ($settings["sms_verify"] == 2 && $user["sms_verify"] != 2) {
        header("Location:".site_url('verify/sms'));
        exit;
    } elseif ($settings["mail_verify"] == 2 && $user["mail_verify"] != 2) {
        header("Location:".site_url('verify/mail')); 
        exit;
    }
}

// Conexão ao banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=oswald', 'oswald', 'Testes@009');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Verifica se o client_id está definido na sessão
if (isset($_SESSION['neira_userid'])) {
    $client_id = $_SESSION['neira_userid'];
    $_SESSION['client_id'] = $client_id; // Armazena o client_id na sessão
} else {
    echo "Erro: client_id não definido na sessão.";
    exit;
}

// Consulta SQL para buscar pedidos da categoria 52 que contenham 'proxy' no nome do serviço
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

// Processamento dos proxies
foreach ($orders as &$order) {
    $order_urls = explode("\n", trim($order['order_url']));
    $order['proxies'] = array_slice($order_urls, 0, $order['order_quantity']);
}

// Passar a variável $orders para o template Twig
$twig = new \Twig\Environment($loader);
$template = $twig->load('proxys.twig');
echo $template->render(array('orders' => $orders));
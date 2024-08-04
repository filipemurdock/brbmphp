<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$title = "Gestão de SMS | BMIlimitada";

if ($_SESSION["neira_userlogin"] != 1 || $user["client_type"] == 1) {
    header("Location:" . site_url('logout'));
}

if ($_SESSION["neira_userlogin"] == 1) {
    if ($settings["sms_verify"] == 2 && $user["sms_verify"] != 2) {
        header("Location:" . site_url('verify/sms'));
    } elseif ($settings["mail_verify"] == 2 && $user["mail_verify"] != 2) {
        header("Location:" . site_url('verify/mail'));
    }
}


$status_list = ["all", "Pending", "Refilling", "Completed", "Rejected", "Error", "canceled"];
$search_statu = route(1);
if (!route(1)) {
    $route[1] = "all";
}

if (!in_array($search_statu, $status_list)) {
    $route[1] = "all";
}

if (route(2)) {
    $page = route(2);
} else {
    $page = 1;
}

$search = "";
if (route(1) != "all") {
    $search = "AND o.order_status='" . route(1) . "'";
}
if (!empty(urldecode($_GET["search"]))) {
    $search .= " AND (o.order_url LIKE '%" . urldecode($_GET["search"]) . "%' OR o.order_id LIKE '%" . urldecode($_GET["search"]) . "%')";
}
if (!empty($_GET["subscription"])) {
    $search .= " AND (o.subscriptions_id LIKE '%" . $_GET["subscription"] . "%')";
}
if (!empty($_GET["dripfeed"])) {
    $search .= " AND (o.dripfeed_id LIKE '%" . $_GET["dripfeed"] . "%')";
}

$c_id = $user["client_id"];
$to = 25;
$count = $conn->query("SELECT o.* 
                       FROM orders o 
                       INNER JOIN services s ON o.service_id = s.service_id 
                       WHERE o.client_id = '$c_id' AND s.category_id = 57 $search")
    ->rowCount();
$pageCount = ceil($count / $to);
if ($page > $pageCount) {
    $page = 1;
}
$where = ($page * $to) - $to;
$paginationArr = ["count" => $pageCount, "current" => $page, "next" => $page + 1, "previous" => $page - 1];

$orders = $conn->prepare("SELECT o.*, s.service_name 
                          FROM orders o 
                          INNER JOIN services s ON o.service_id = s.service_id 
                          WHERE o.client_id = :c_id AND s.category_id = 57 $search 
                          ORDER BY o.order_id DESC LIMIT $where, $to");
$orders->execute(array("c_id" => $user["client_id"]));
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);

// Atualiza a data de expiração para cada pedido, se ainda não estiver definida
foreach ($orders as &$order) {
    if (empty($order['expiration_time']) && $order['order_status'] === 'inprogress') {
        $expiration_time = date("Y-m-d H:i:s", strtotime($order['order_create'] . ' + 15 minutes'));
        $order_id = $order['order_id'];
        
        // Atualize a data de expiração no banco de dados
        $updateStmt = $conn->prepare("UPDATE orders SET expiration_time = :expiration_time WHERE order_id = :order_id");
        $updateStmt->execute(['expiration_time' => $expiration_time, 'order_id' => $order_id]);
        
        // Verificar se a atualização foi bem-sucedida
        if ($updateStmt->rowCount() > 0) {
            // Atualização bem-sucedida
        } else {
            // Falha na atualização
        }

        // Atualizar a ordem com a nova data de expiração
        $order['expiration_time'] = $expiration_time;
    }
}


$ordersList = [];

foreach ($orders as $order) {
    $o["id"] = $order["order_id"];
    $o["date"] = date("Y-m-d H:i:s", (strtotime($order["order_create"]) + $user["timezone"]));
    $o["link"] = $order["order_url"];
    $o["service"] = $order["service_name"];
    $o["status"] = $languageArray["orders.status." . $order["order_status"]];
    $o["refill_id"] = $order["order_id"];
    $o["refill_status"] = $order["order_status"];
    $o["show_refill"] = true;
    $o["expiration_time"] = $order["expiration_time"];
    $o["sms_number"] = ($order["order_status"] === "inprogress") ? $order["sms_number"] : '';
    
    if ($order["order_status"] == "completed" && substr($order["order_remains"], 0, 1) == "-") {
        $o["remains"] = "+" . substr($order["order_remains"], 1);
    } else {
        $o["remains"] = $order["order_remains"];
    }
    array_push($ordersList, $o);
}

// Inclua o Twig e passe as variáveis
include '/core/temp/client/oswald/sms-2.twig';

?>

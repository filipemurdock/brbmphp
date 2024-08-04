<?php
 //Ativar exibição de erros para depuração
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


$title .= $languageArray["orders.title"];

if( $_SESSION["neira_userlogin"] != 1  || $user["client_type"] == 1  ){
  header("Location:".site_url('logout'));
}

if($_SESSION["neira_userlogin"] == 1 ):
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }elseif($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail')); 
    }

endif;

$request = route(1);
$o_id = route(2);

$status_list  = ["all","Pending","Refilling","Completed","Rejected","Error","canceled"];
$search_statu = route(1); if( !route(1) ):  $route[1] = "all";  endif;

if( !in_array($search_statu,$status_list) ):
    $route[1] = "all";
endif;

$status_list  = ["all","Pending","Refilling","Completed","Rejected","Error","canceled"];
$search_statu = route(1); if( !route(1) ):  $route[1] = "all";  endif;

if ($request == 'refill' && $o_id) {
    $order = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
    $order->execute(array("id" => $o_id));
    $order = $order->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Adicionando a lógica para verificar o tipo de reabastecimento e o tempo de reabastecimento
        $service_id = $order["service_id"];
        $service_stmt = $conn->prepare("SELECT refill_type, refill_time FROM services WHERE service_id = :service_id");
        $service_stmt->execute(array("service_id" => $service_id));
        $service = $service_stmt->fetch(PDO::FETCH_ASSOC);

        if ($service) {
            $refill_type = $service["refill_type"];
            $refill_time = $service["refill_time"];

            // Verificações de tempo para decidir se o botão de reposição deve ser exibido
            $refill_hours = $refill_time * 24; // Convertendo dias em horas
            $order_create_time = strtotime($order["order_create"]);
            $current_time = time();
            $elapsed_time_hours = ($current_time - $order_create_time) / 3600;

            // Verifique se o pedido está completo, se o tempo de reposição passou e se o tipo de reposição é 2
            if ($order["order_status"] == "completed" && $elapsed_time_hours >= $refill_hours && $refill_type == "2") {
                // Insira o pedido na tabela de refill_status
                $refill_placed_time = date("Y-m-d H:i:s");
                $refill_end_time = date("Y-m-d H:i:s", strtotime($refill_placed_time . ' + 1 day'));

                $insert = $conn->prepare("INSERT INTO refill_status SET client_id=:client_id , order_id=:order_id , refill_apiid=:refill_apiid ,order_apiid=:order_apiid , refill_response=:refill_response , creation_date=:creation_date , ending_date=:ending_date ,  order_url=:order_url , service_name=:service_name ");
                $insert->execute(array(
                    "client_id" => $order["client_id"],
                    "order_id" => $order["order_id"],
                    "refill_apiid" => "0", // Defina como necessário para o seu sistema
                    "order_apiid" => $order["api_orderid"], // Supondo que você tenha esse campo no seu banco de dados
                    "refill_response" => "Success",
                    "creation_date" => $refill_placed_time,
                    "ending_date" => $refill_end_time,
                    "order_url" => $order["order_url"],
                    "service_name" => $order["service_name"]
                ));

                if ($insert) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully Placed',
                            showConfirmButton: true,
                            confirmButtonText: 'Okay'
                        }).then(function() {
                            window.location.href = 'orders/#solicitacaoconcluida';
                        });
                    </script>";
                } else {
                    die; // Trate os erros conforme necessário
                }
            } else {
                $rd = site_url("orders");
                echo "<script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Refill not allowed yet',
                        showConfirmButton: true,
                        confirmButtonText: 'Dismiss'
                    }).then(function() {
                        window.location.href = '$rd';
                    });
                </script>";
            }
        } else {
            die; // Trate os erros conforme necessário
        }
    } else {
        die; // Trate os erros conforme necessário
    }
}

// Aqui continua o seu código existente para exibir a lista de pedidos e seus status
$status_list = ["all", "pending", "inprogress", "completed", "partial", "processing", "canceled"];
$search_statu = route(1);
if (!route(1)) {
    $route[1] = "all";
}
if (!in_array($search_statu, $status_list)) {
    $route[1] = "all";
}
$page = route(2) ? route(2) : 1;
$search = route(1) != "all" ? "&& order_status='" . route(1) . "'" : "";
$search .= !empty($_GET["search"]) ? " && ( order_url LIKE '%" . $_GET["search"] . "%' || order_id LIKE '%" . $_GET["search"] . "%' ) " : "";
$search .= !empty($_GET["subscription"]) ? " && ( subscriptions_id LIKE '%" . $_GET["subscription"] . "%'  ) " : "";
$search .= !empty($_GET["dripfeed"]) ? " && ( dripfeed_id LIKE '%" . $_GET["dripfeed"] . "%'  ) " : "";

$c_id = $user["client_id"];
$to = 25;
$count = $conn->query("SELECT * FROM orders WHERE client_id='$c_id' && dripfeed='1' && subscriptions_type='1' $search ")->rowCount();
$pageCount = ceil($count / $to);
if ($page > $pageCount) {
    $page = 1;
}
$where = ($page * $to) - $to;
$paginationArr = ["count" => $pageCount, "current" => $page, "next" => $page + 1, "previous" => $page - 1];

$orders = $conn->prepare("SELECT * FROM orders INNER JOIN services ON services.service_id = orders.service_id WHERE orders.dripfeed=:dripfeed && orders.subscriptions_type=:subs && orders.client_id=:c_id $search ORDER BY orders.order_id DESC LIMIT $where,$to ");
$orders->execute(array("c_id" => $user["client_id"], "dripfeed" => 1, "subs" => 1));
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);
$ordersList = [];

// LOOP foreach Processando lista de pedidos
foreach ($orders as &$order) {
    if (isset($order['files']) && !empty($order['files'])) {
        // Use htmlspecialchars para evitar problemas com caracteres especiais
        $safeFile = htmlspecialchars($order['files'], ENT_QUOTES, 'UTF-8');
        $fileName = $safeFile; // Defina $fileName corretamente
        $order['downloadButton'] = '<a href="download.php?file=' . urlencode($fileName) . '" class="btndownload btn btn-primary" title="Download" aria-label="Download">
            <i class="fa fa-download"></i>
        </a>';
    } else {
        $order['downloadButton'] = ''; // Use uma string vazia em vez de null para evitar problemas
    }

    // Adicione isto para obter e passar fileName
    $order['fileName'] = $order['files'] ?? null; // Certifique-se de que 'files' está presente

	var_dump($order['downloadButton']); // Adicione isto para depuração


	
	
	// Continue com a sua lógica de construção do array $ordersList
    $o["refillButton"] = false;

    // Adicione a lógica para obter refill_type
    $service_id = $order["service_id"];
    $service_stmt = $conn->prepare("SELECT refill_type, refill_time FROM services WHERE service_id = :service_id");
    $service_stmt->execute(array("service_id" => $service_id));
    $service = $service_stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        $refill_type = $service["refill_type"];
        $refill_time = $service["refill_time"];

        // Verificações de tempo para decidir se o botão de reposição deve ser exibido
        $refill_hours = $refill_time * 24; // Convertendo dias em horas
        $order_create_time = strtotime($order["order_create"]);
        $current_time = time();
        $elapsed_time_hours = ($current_time - $order_create_time) / 3600;

        // Verifique se o pedido está completo e se o tempo de reposição passou
        if ($order["order_status"] == "completed" && $elapsed_time_hours >= $refill_hours && $refill_type == "2") {
            $o["refillButton"] = true;
        }
		
		
    }
	


	
	
//Adicione isto para depuração
//var_dump($order["order_status"]);
//var_dump($elapsed_time_hours);
//var_dump($refill_hours);
//var_dump($refill_type);

//error_log("Refill type: " . $refill_type); // Adicione esta linha para verificar o valor	
//error_log("Elapsed Time Hours: " . $elapsed_time_hours); // Adicione esta linha para verificar o valor
//error_log("Refill Hours: " . $refill_hours); // Adicione esta linha para verificar o valor
//error_log("Refill Button Flag: " . ($o["refillButton"] ? "true" : "false")); // Adicione esta linha para verificar o valor
//error_log("Refill Time: " . $refill_time); // Adicione esta linha para verificar o valor


	

    // Verifique se o pedido pode ser cancelado com base em critérios específicos (exemplo: cancel_type igual a 2)
    if ($order["cancel_type"] == 2 && ($order["order_status"] == 'pending' || $order["order_status"] == 'processing' || $order["order_status"] == 'inprogress')) {
        $o["cancelButton"] = true;
    } else {
        $o["cancelButton"] = false;
    }

    $o["id"] = htmlentities($order["order_id"]);
    $o["date"] = date("Y-m-d H:i:s", (strtotime($order["order_create"]) + $user["timezone"]));
    $o["link"] = htmlentities($order["order_url"]);
    $o["charge"] = htmlentities($order["order_charge"]);
    $o["start_count"] = htmlentities($order["order_start"]);
    $o["quantity"] = htmlentities($order["order_quantity"]);
    $o["service_id"] = htmlentities($order["service_id"]);
    $o["service"] = htmlentities($order["service_name"]);
	$o["downloadButton"] = ($order["downloadButton"]); // Adicione essa linha

    $o["status"] = $languageArray["orders.status." . $order["order_status"]];
	

    if ($order["order_status"] == "completed" && substr($order["order_remains"], 0, 1) == "-") {
        $o["remains"] = "+" . substr($order["order_remains"], 1);
    } else {
        $o["remains"] = htmlentities($order["order_remains"]);
    }

    array_push($ordersList, $o);
}
// Passar a lista de pedidos para o Twig

$twig->addGlobal('orders', $ordersList);

 $twig->render('orders.twig');
	var_dump($order['downloadButton']); // Adicione isto para depuração

?>

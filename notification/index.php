<?php
include("../int/config.php");
$sql = "SELECT method_extras FROM payment_methods WHERE id = 52";
$stmt = mysqli_query($conn1, $sql);
$row = mysqli_fetch_assoc($stmt);
$methodExtrasJSON = $row['method_extras'];
$methodExtras = json_decode($methodExtrasJSON, true);
$merchantWebsite = $methodExtras['merchant_website'];
//Para capturar o corpo JSON
$json_convertido = json_decode(file_get_contents('php://input'), true);
if (isset($json_convertido['token'])){
    $id_pedido = $json_convertido['invoice_id'];
    $token = $json_convertido['token'];

    
    $post = array(
    'merchant_key'=> $merchantWebsite,
    'token'=> $token
    );


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://expaybrasil.com/en/request/status' );
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    //var_dump(curl_exec($ch));
    $response = json_decode(curl_exec($ch),true);
    //print_r($response);

    curl_close($ch);
    $items = $response['transaction_request'];
    $status = $items['status'];
    print $status;
    
  

    
    if ($status == 'paid') {
        $invoice_id = $items['invoice_id'];
        $valor = $items['total'];
        $id_pedido = $invoice_id;
    
     
        $result_usuario = "UPDATE `payments` SET `payment_status` = 3 WHERE `payments`.`payment_extra` = '$id_pedido';";
        $resultado_usuario = mysqli_query($conn1, $result_usuario);
    
       
        $query = "SELECT * FROM payments WHERE `payments`.`payment_extra` = '$id_pedido'";
        $info_usuario = mysqli_query($conn1, $query);
        $id_usr = mysqli_fetch_assoc($info_usuario);
        $id_user = $id_usr['client_id'];
    
        
        $add_saldo = "UPDATE `clients` SET `balance` = `balance` + '$valor' WHERE `clients`.`client_id` = $id_user";
        $info_usuario = mysqli_query($conn1, $add_saldo);
    
     
        $update_delivery = "UPDATE `payments` SET `payment_delivery` = 2 WHERE `payments`.`payment_extra` = '$id_pedido'";
        $resultado_update_delivery = mysqli_query($conn1, $update_delivery);
    }
    
}
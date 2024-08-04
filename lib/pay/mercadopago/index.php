<?php
require '../../../vendor/mercadopago/autoload.php';

$URL_RETORNO = $_POST['CALLBACK_URL'];
$TOKEN_MP    = $_POST['TOKEN'];
$REFERENCIA  = $_POST['ORDER_ID'];
$VALOR       = $_POST['TXN_AMOUNT'];
$NOME        = $_POST['NOME'];
$BASE        = $_POST['BASE'];

$URL = $BASE."/addfunds";

$amount = floatval(str_replace(',','.',$VALOR));

MercadoPago\SDK::setAccessToken($TOKEN_MP);
$preference = new MercadoPago\Preference();
$item = new MercadoPago\Item();
$item->title = 'Recarga de saldo para '.$user['username'];
$item->quantity = 1;
$item->unit_price = $amount;

// Configuração para excluir métodos de pagamento
$payment_methods = [
    'excluded_payment_types' => [
        ['id' => 'credit_card'],
        ['id' => 'ticket'],
        ['id' => 'debit_card']
    ],
    'installments' => 12 // Você pode alterar o número de parcelas aqui
];

$preference->payment_methods = $payment_methods; // Adicione a configuração ao objeto preference

$preference->items = array($item);
$preference->external_reference = $REFERENCIA;
$preference->back_urls = array("success" => $URL, "failure" => $URL, "pending" => $URL);
$preference->notification_url   = $URL_RETORNO;
$preference->auto_return = "approved";
$preference->save();     
header("Location: ".$preference->init_point);
?>



<?php 


$symbol = $currency['symbol'];
$currency_value = $currency['value'];
$action = $_POST["action"];
$percent = $user["coustm_rate"]/100;
if ($action == "services_list"):
    $category = $_POST["category"];


    $services = $conn->prepare("SELECT * FROM services WHERE category_id=:c_id && service_type=:type ORDER BY service_line ");
    $services->execute(array('c_id' => $category, 'type' => 2));
    $services = $services->fetchAll(PDO::FETCH_ASSOC);
    if ($services):
        $serviceList = "";
    else:
        $serviceList = "<option value='0'>Serviço não encontrado nesta categoria</option>";
    endif;
    foreach ($services as $service) {
        $search = $conn->prepare("SELECT * FROM clients_service WHERE service_id=:service && client_id=:c_id ");
        $search->execute(array("service" => $service["service_id"], "c_id" => $user["client_id"]));
        
         if ($service["service_secret"] == 2 || $search->rowCount()):
            $multiName = json_decode($service["name_lang"], true);
            if ($multiName[$user["lang"]]):
                $name = $multiName[$user["lang"]];
            else:
                $name = $service["service_name"];
            endif;
            $serviceList .= "<option value='" . $service['service_id'] . "' ";
            if ($_SESSION["data"]["services"] == $service['service_id']):
                $serviceList .= "selected";
            endif;
            
            
             $roundedPrice = round(service_price($service["service_id"]) * $currency['value'], 3);
            $serviceList .= "<option>" . $service["service_id"] . " - " . $name . " - " . $currency['symbol'] ." ". priceFormat($roundedPrice) . "</option>";
            // engaging-violet  $serviceList .= "<option data-content=\"".htmlentities("<span class=\"badge badge-secondary style-text-primary badge-rounded\">".$service["service_id"] ."</span>"). " - " . $name . " - " . $currency['symbol'] ." ". priceFormat($roundedPrice) . "</option>";
        endif;
        
        
        
    }
    
    echo json_encode(['services' => $serviceList]);
    
elseif ($action == "service_detail"):
    $s_id = $_POST["service"];
    $service = $conn->prepare("SELECT * FROM services WHERE service_id=:s_id");
    $service->execute(array(
        's_id' => $s_id
    ));
    $service = $service->fetch(PDO::FETCH_ASSOC);
    $service["service_price"] = service_price($service["service_id"]);
    $serviceDetails = "";
   
     if($settings["avarage"] == 2):      
           
     $orders = $conn->prepare("SELECT order_create,last_check FROM orders  WHERE service_id='$row[service_id]' && order_status='completed' order by order_id DESC LIMIT 10");
                $orders->execute(array());
               
                if($orders->rowCount() < 9) {
                        $callback = $languageArray["monitor.error"];
                }
           
                foreach($orders as $order) {
                    $basla = strtotime($order["order_create"]);
                    $bitis = strtotime($order["last_check"]);
                    $bitissil = $bitis-900;
                    $ortalama= ($bitissil-$basla) ;
                    $orta = $ortalama/60;
                    $ortalama1 = round(abs($basla - $bitissil));
                   
                    $callback = $ortalama1.",";
                }
 
                $parcala = explode(",",$callback);
   
                $dizi = array($parcala["0"],$parcala["2"],$parcala["3"],$parcala["4"],$parcala["5"],$parcala["6"],$parcala["7"],$parcala["8"],$parcala["9"],$parcala["1"]);
                $ortalamamiz = explode(".",ortalama($dizi));
               
                if($ortalamamiz[0] == "NaN") {
                  $service_speed = $languageArray["monitor.error"];
                } else {
                  $service_speed = convertSecToStr($ortalamamiz[0]);
                }  
                
                endif;
   
    $multiDesc = json_decode($service["description_lang"], true);
   
    if ($multiDesc[$user["lang"]]):
        $desc = $multiDesc[$user["lang"]];
    else:
        $desc = $service["service_description"];
    endif;
    
 
    
    if ($desc):
        $description=str_replace("\n","<br />",$desc);
        $serviceDetails .= '<div class="form-group fields" id="description">
<label for="service_description" class="control-label">' . $languageArray["neworder.description"] . '</label>
<div class="panel-body border-solid border-rounded" id="service_description">
' . $description . '
</div>
</div>';
    endif;
        
          
                $avarageTime = true;
               
                $orders = $conn->prepare("SELECT * FROM orders  WHERE service_id='$s_id' && order_status='completed' order by order_id DESC LIMIT 10");
                $orders->execute(array());
                
                if($orders->rowCount() < 9) { 
                	$callback = 'Ainda não calculado';
                }
            
                foreach($orders as $order) {
                    $basla = strtotime($order["order_create"]);
                    $bitis = strtotime($order["last_check"]);
                    $bitissil = $bitis-900;
                    $ortalama= ($bitissil-$basla) ;
                    $orta = $ortalama/60;
                    $ortalama1 = round(abs($basla - $bitissil));
                    
                    $callback = $ortalama1.",";
                }

                $parcala = explode(",",$callback);
    
                $dizi = array($parcala["0"],$parcala["2"],$parcala["3"],$parcala["4"],$parcala["5"],$parcala["6"],$parcala["7"],$parcala["8"],$parcala["9"],$parcala["1"]);
                $ortalamamiz = explode(".",ortalama($dizi));
                
                if($ortalamamiz[0] == "NaN") {
                  $veri = 'Ainda não calculado';
                } else {
                  $veri = convertSecToStr($ortalamamiz[0]); 
                }  
                
				$s["service_speed"] = $veri;
    
    
       $description    = str_replace("\n","<br />",$service["service_description"]);
        $fsptime    = str_replace("\n","<br />",$s["service_speed"]);
        
                if($settings['site_theme'] == 'smmspot'){
                  $serviceDetails.= ' <div class="form-group fields" id="description">
              
			  <label class="control-label"  for="service_description" class="control-label"><span>Tempo Medio</span>
                                                    <span class="ml-1 mr-1 far fa-exclamation-circle" data-toggle="tooltip" data-placement="top" title="O tempo médio é baseado nos últimos 10 pedidos concluídos"></span>
                                               </label>

        <div class="fg"><div class="fg-icon"><i class="far fa-hourglass-half"></i></div>
			  <label class="fg-control"  for="service_description" class="fg-control"><span>'.$fsptime.' </span>
                                                    
                                               </label>
             
              </div>
</div>
<br>
            </div>
            <style>    .fa.fa-exclamation-circle {
        color: #ff5733;
        font-size: 18px;
        margin: 0 5px;
        cursor: pointer;
    }

    #custom-tooltip:after {
        content: "O tempo médio é baseado nos últimos 10 pedidos concluídos";
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 10px;
        position: fixed; 
        z-index: 1;
        top: 50%; /* Centraliza verticalmente */
        left: 50%; /* Centraliza horizontalmente */
        transform: translate(-50%, -50%); /* Centraliza tanto vertical quanto horizontalmente */
        opacity: 0;
        transition: opacity 0.3s;
        border: 2px solid #000;
    }

    #custom-tooltip:hover:after {
        visibility: visible;
        opacity: 1;
    }
</style>';

               
}else{
        $serviceDetails.= '<div class="form-group fields" id="description"><label class="control-label" for="service_description"><span>Tempo médio</span><span class="ml-1 mr-1 fa fa-exclamation-circle" id="custom-tooltip"></span></label><div class="panel-body border-solid border-rounded" id="service_description">'.$fsptime.'</div></div>
        <style>    .fa.fa-exclamation-circle {
        color: #ff5733;
        font-size: 18px;
        margin: 0 5px;
        cursor: pointer;
    }

    #custom-tooltip:after {
        content: "O tempo médio é baseado nos últimos 10 pedidos concluídos";
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 10px;
        position: fixed; 
        z-index: 1;
        top: 50%; /* Centraliza verticalmente */
        left: 50%; /* Centraliza horizontalmente */
        transform: translate(-50%, -50%); /* Centraliza tanto vertical quanto horizontalmente */
        opacity: 0;
        transition: opacity 0.3s;
        border: 2px solid #000;
    }

    #custom-tooltip:hover:after {
        visibility: visible;
        opacity: 1;
    }
</style>
';
 
}

      


    /* if ($service["service_package"] == 1 || $service["service_package"] == 2 || $service["service_package"] == 3 || $service["service_package"] == 4):
        if ($service["want_username"] == 2):
            $link_type = 'Username';
        else:
            $link_type = 'Link';
        endif;
        
                if($settings['site_theme'] == 'smmspot'){
                $serviceDetails .= '<div class="form-group fields" id="order_link"><div class="fga mb-4"><label class="fla" for="orderLink">'.$link_type.'</label><div class="fg"><div class="fg-icon"><i class="far fa-link"></i></div><input class="fg-control" name="link" value="" type="text" id="field-orderform-fields-link"></div></div></div>';
}else{
        $serviceDetails.= '<div class="form-group fields" id="order_link">
                <label class="control-label" for="field-orderform-fields-link">' . $link_type . '</label>
                <input class="form-control" name="link" value="' . $_SESSION["data"]["link"] . '" type="text" id="field-orderform-fields-link">
              </div>';
}
    endif; */
    
    if ($service["service_package"] == 1):
         if($settings['site_theme'] == 'smmspot'){
                     $serviceDetails .= '<div class="form-group fields" id="order_quantity"><div class="fga mb-4"><label class="fla" for="orderQuantitiy">' . $languageArray["neworder.quantity"] . '</label><div class="fg"><div class="fg-icon"><i class="far fa-sort-numeric-up-alt"></i></div><input class="fg-control" name="quantity" value="" type="text" id="neworder_quantity"></div></div></div><small class="help-block min-max">Min: ' . number_format($service["service_min"], 0, '.', '.') . ' - Max: ' . number_format($service["service_max"], 0, '.', '.') . '</small>';

               
}else{
        $serviceDetails.= '<div class="form-group fields" id="order_quantity">
                  <label class="control-label" for="field-orderform-fields-quantity">Quantidade</label>
                  <input class="form-control" name="quantity" value="' . $_SESSION["data"]["quantity"] . '" type="text" id="neworder_quantity">
              </div>
              <small class="help-block min-max">Min: ' . $service["service_min"] . ' - Max: ' . $service["service_max"] . '</small>
              ';
}
    endif;

   /*  if ($service["service_package"] == 11 || $service["service_package"] == 12 || $service["service_package"] == 13 || $service["service_package"] == 14 || $service["service_package"] == 15):
        $serviceDetails.= '<div class="form-group fields" id="order_link">
                <label class="control-label" for="field-orderform-fields-link">Nome de usuário</label>
                <input class="form-control" name="username" value="' . $_SESSION["data"]["username"] . '" type="text" id="field-orderform-fields-link">
              </div>';
    endif; */

    if ($service["service_package"] == 3):
        $serviceDetails.= '<div class="form-group fields" id="order_quantity">
              <label class="control-label" for="field-orderform-fields-quantity">Quantidade</label>
              <input class="form-control" name="quantity" value="" type="text" id="neworder_quantity" disabled="">
          </div>
          <small class="help-block min-max">Min: ' . $service["service_min"] . ' - Max: ' . $service["service_max"] . '</small>
          ';
    endif;
    
    if ($service["service_package"] == 11 || $service["service_package"] == 12 || $service["service_package"] == 13):
        $serviceDetails.= '<div class="form-group fields" id="order_link">
                <label class="control-label" for="field-orderform-fields-link">Qual o limite de postagens você gostaria?</label>
                <input class="form-control" name="posts" value="' . $_SESSION["data"]["posts"] . '" type="text" id="field-orderform-fields-link">
              </div>'; 
        $serviceDetails.= '<div class="form-group fields" id="order_min">
              <label class="control-label" for="order_count">Quantidade</label>
              <div class="row">
                  <div class="col-xs-6">
                      <input type="text" class="form-control" id="order_count" name="min" value="' . $_SESSION["data"]["min"] . '" placeholder="Min">
                  </div>
                  <div class="col-xs-6">
                      <input type="text" class="form-control" id="order_count" name="max" value="' . $_SESSION["data"]["max"] . '" placeholder="Max">
                  </div>
              </div>
              <small class="help-block min-max">Min: ' . $service["service_min"] . ' - Max: ' . $service["service_max"] . '</small>
          </div>
          <div class="form-group fields" id="order_delay">
              <div class="row">
                  <div class="col-xs-6">
                      <label class="control-label" for="field-orderform-fields-delay">Quanto tempo de atraso no pedido você deseja?</label>
                      <select class="form-control" name="delay" id="field-orderform-fields-delay">
                          <option value="0" ';
        if ($_SESSION["data"]["delay"] == 0):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>Sem atraso</option>
                          <option value="300" ';
        if ($_SESSION["data"]["delay"] == 300):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>5 minutos</option>
                          <option value="600" ';
        if ($_SESSION["data"]["delay"] == 600):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>10 minutos</option>
                          <option value="900" ';
        if ($_SESSION["data"]["delay"] == 900):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>15 minutos</option>
                          <option value="1800" ';
        if ($_SESSION["data"]["delay"] == 1800):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>30 minutos</option>
                          <option value="3600" ';
        if ($_SESSION["data"]["delay"] == 3600):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>60 minutos</option>
                          <option value="5400" ';
        if ($_SESSION["data"]["delay"] == 5400):
            $serviceDetails.= ' selected';
        endif;
        $serviceDetails.= '>90 minutos</option>
                      </select>
                  </div>
                  <div class="col-xs-6">
                      <label for="field-orderform-fields-expiry">Data final</label>
                      <div class="input-group" id="datetimepicker">
                          <input class="form-control datetime" name="expiry" id="expiryDate" value="' . $_SESSION["data"]["expiry"] . '" type="text" autocomplete="off">
                          <span class="input-group-btn">
                              <button class="btn btn-default clear-datetime" id="clearExpiry" type="button"> <span class="fa fa-trash-o"></span></button>
                          </span>
                      </div>
                  </div>
              </div>
          </div>';
    endif;
    if ($service["service_package"] == 3 || $service["service_package"] == 4):
        $serviceDetails.= '<div class="form-group fields" id="order_comment">
              <label class="control-label">Comentarios</label>
              <textarea class="form-control counter" name="comments" id="neworder_comment" cols="30" rows="10" data-related="quantity">' . $_SESSION["data"]["comments"] . '</textarea>
          </div>';
    endif;
    if ($service["service_dripfeed"] == 2):
        if ($_SESSION["data"]["check"]):
            $check = "checked";
        endif;
        $serviceDetails.= '<div id="dripfeed">
                <div class="form-group fields" id="order_check">
                    <label class="control-label has-depends " for="dripfeedcheckbox">
                        <input name="check" value="1" type="checkbox" ' . $check . ' id="dripfeedcheckbox">
                        Drip-feed 
                    </label>
                    <div class="hidden" id="dripfeed-options">
                        <div class="form-group">
                            <label class="control-label" for="dripfeed-runs">Quantas vezes o processo deve ser repetido?</label>
                            <input class="form-control" name="runs" value="' . $_SESSION["data"]["runs"] . '" type="text" id="dripfeed-runs">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="dripfeed-interval">Quantos minutos de intervalo? (60*24 = 1440 para envio diário)</label>
                            <input class="form-control" name="interval" value="' . $_SESSION["data"]["interval"] . '" type="text" id="dripfeed-interval">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="dripfeed-totalquantity">Quantidade total a ser enviada</label>
                            <input class="form-control" name="total_quantity" value="' . $_SESSION["data"]["total_quantity"] . '" type="text" id="dripfeed-totalquantity" readonly="">
                        </div>
                    </div>
                </div>
            </div>
            ';
    endif;
    $runs = $_POST["runs"];
    if (!$runs):
        $runs = 1;
    endif;
    $dripfeed = $_POST["dripfeed"];
    $quantity = $_POST["quantity"];
    if ($s_id != 0 && $dripfeed == "bos"):
        $price = $quantity * $service["service_price"];
         $data = ['details' => $serviceDetails, 'price' => $currency['symbol'] . ' ' . priceFormat($price * $currency_value)];
    elseif ($s_id != 0 && $dripfeed == "var"):
        $price = $runs * $quantity * $service["service_price"];
         $data = ['details' => $serviceDetails, 'price' => $currency['symbol'] . ' ' . priceFormat($price * $currency_value)];
    elseif ($s_id != 0 && !isset($dripfeed)):
        $price = $service["service_price"];
$price1 = $price*$percent;
$price = $price-$price1;
         $data = ['details' => $serviceDetails, 'price' => $currency['symbol'] . ' ' . priceFormat($price * $currency_value)];
        
        
    else:
        $data = ['empty' => 1];
    endif;
    if ($service["service_package"] == 11 || $service["service_package"] == 12 || $service["service_package"] == 13):
        $data["sub"] = 1;
    endif;
    echo json_encode($data);
    unset($_SESSION["data"]);
elseif ($action == "service_price"):
    $service = $_POST["service"];
    $quantity = $_POST["quantity"];
    $comments = $_POST["comments"];
    $dripfeed = $_POST["dripfeed"];
    $runs = $_POST["runs"];
    if (!$runs):
        $runs = 1;
    endif;
    $price = service_price($service);



    if ($comments):
        $quantity = count(explode("\n", $comments));
    endif;

    if ($quantity == 0) {
        $totalPrice = service_price($service)*0 . $symbol;

    } elseif ($dripfeed == "var") {
        $totalPri = priceFormat(($price * $quantity * $runs)*$currency_value);

$totalPric = $totalPri*$percent;
$totalPrice = $totalPri-$totalPric;
$totalPrice = $currency['symbol'] . ' ' . ($totalPri - $totalPric);
    } else {
        $totalPri = priceFormat(($price * $quantity)*$currency_value);

$totalPric = $totalPri*$percent;
$totalPrice = $totalPri-$totalPric;
$totalPrice = $currency["symbol"] . ' ' . ($totalPri - $totalPric);
    }
    echo json_encode(['price' => $totalPrice, 'commentsCount' => $quantity, 'totalQuantity' => $runs * $quantity]);
 
endif;
<?php

  if( $user["access"]["sam"] != 1  ):
    header("Location:".site_url("admin"));
    exit();
  endif;


if( route(2) && is_numeric(route(2)) ):
  $page = route(2);
else:
  $page = 1;
endif;

  if( route(2)  ==  "counter" ):
    $count          = $conn->prepare("SELECT * FROM orders WHERE dripfeed=:dripfeed && subscriptions_type=:sub $search_add ");
    $count        ->execute(array("dripfeed"=>1,"sub"=>1));
    $count          = $count->rowCount();
    $services = $conn->prepare("SELECT * FROM services");
    $services->execute(array());
    $services = $services->fetchAll(PDO::FETCH_ASSOC);
    $active   = $_POST["active"];
    echo '<li'; if( !$active ): echo ' class="active"'; endif; echo '>
            <a href="/admin/orders/all">All Orders ('.$count.')</a>
          </li>';
      foreach ($services as $service):
        echo '<li'; if( $service["service_id"] == $active ): echo ' class="active"'; endif; echo '>
                <a '; if( $service["service_type"] == 1 ): echo ' style="color: #c1c1c1;"'; endif; echo ' href="admin/orders/all?service_id='.$service["service_id"].'"><span class="label-id">'.$service["service_id"].'</span> '.$service["service_name"].' ('.countRow(["table"=>"orders","where"=>["service_id"=>$service["service_id"]]]).')</a>
              </li>';
      endforeach;
    exit();
  endif;

  if( $_SESSION["client"]["data"] ):
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value) {
      $$key = $value;
    }
    unset($_SESSION["client"]);
  endif;

    if( route(2) && is_numeric(route(2)) ):
      $page = route(2);
    else:
      $page = 1;
    endif;

    $statusList = ["all","pending","inprogress","completed","partial","canceled","processing","fail","cronpending"];
    if( route(3) && in_array(route(3),$statusList) ):
      $status   = route(3);
    elseif( !route(3) || !in_array(route(3),$statusList) ):
      $status   = "all";
    endif;

    if( $_GET["search_type"] == "username" && $_GET["search"] && countRow(["table"=>"clients","where"=>["username"=>$_GET["search"]]])):
      $search_where = $_GET["search_type"];
      $search_word  = urldecode($_GET["search"]);
      $clients      = $conn->prepare("SELECT client_id FROM clients WHERE username LIKE '%".$search_word."%' ");
      $clients     -> execute(array());
      $clients      = $clients->fetchAll(PDO::FETCH_ASSOC);
      $id=  "("; foreach ($clients as $client) { $id.=$client["client_id"].","; } if( substr($id,-1) == "," ):  $id = substr($id,0,-1); endif; $id.=")";
      $search       = " orders.client_id IN ".$id;
      $count        = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id = orders.client_id WHERE {$search} && orders.dripfeed='1' && orders.subscriptions_type='1' ");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE {$search} && orders.dripfeed='1' && orders.subscriptions_type='1' ";
      $search_link  = "?search=".$search_word."&search_type=".$search_where;
    elseif( $_GET["search_type"] == "order_id" && $_GET["search"] ):
      $search_where = $_GET["search_type"];
      $search_word  = urldecode($_GET["search"]);
      $count        = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id = orders.client_id WHERE orders.order_id LIKE '%".$search_word."%' && orders.dripfeed='1' && orders.subscriptions_type='1' ");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE orders.order_id LIKE '%".$search_word."%'  && orders.dripfeed='1' && orders.subscriptions_type='1' ";
      $search_link  = "?search=".$search_word."&search_type=".$search_where;
    elseif( $_GET["search_type"] == "order_url" && $_GET["search"] ):
      $search_where = $_GET["search_type"];
      $search_word  = urldecode($_GET["search"]);
      $count        = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id = orders.client_id WHERE orders.order_url LIKE '%".$search_word."%' && orders.dripfeed='1' && orders.subscriptions_type='1' ");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE orders.order_url LIKE '%".$search_word."%'  && orders.dripfeed='1' && orders.subscriptions_type='1' ";
      $search_link  = "?search=".$search_word."&search_type=".$search_where;
    elseif( $_GET["subscription"] ):
      $subs_id      = $_GET["subscription"];
      $count        = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id = orders.client_id WHERE orders.order_id LIKE '%".$search_word."%' && orders.dripfeed='1' && orders.subscriptions_type='1' && orders.subscriptions_id='$subs_id' ");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE orders.subscriptions_id='$subs_id'  && orders.dripfeed='1' && orders.subscriptions_type='1' ";
      $search_link  = "?subscription=".$_GET["subscription"];
    elseif( $_GET["dripfeed"] ):
      $drip_id      = $_GET["dripfeed"];
      $count        = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id = orders.client_id WHERE orders.order_id LIKE '%".$search_word."%' && orders.dripfeed='1' && orders.subscriptions_type='1' && orders.dripfeed_id='$drip_id' ");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE orders.dripfeed_id='$drip_id'  && orders.dripfeed='1' && orders.subscriptions_type='1' ";
      $search_link  = "?dripfeed=".$_GET["subscription"];
    elseif( $status != "all" ):
      if( $_GET["mode"] && $_GET["mode"] == "manuel" ):
        $search_add   = " && orders.order_api=0";
        $search_link  = "?mode=".$_GET["mode"];
      elseif( $_GET["mode"] && $_GET["mode"]== "auto" ):
        $search_add   = " && orders.order_api!=0";
        $search_link  = "?mode=".$_GET["mode"];
      elseif( $_GET["service_id"] ):
        $search_add   = " && orders.service_id=".$_GET["service_id"];
        $search_link  = "?service_id=".$_GET["service_id"];
      else:
        $search_add   = "";
      endif;
      if( $status == "fail" ):
        $search_add  .= ' && orders.order_error!="-" ';
        $count          = $conn->prepare("SELECT * FROM orders WHERE dripfeed=:dripfeed && subscriptions_type=:sub $search_add ");
        $count        ->execute(array("dripfeed"=>1,"sub"=>1));
        $count          = $count->rowCount();
        $search         = "WHERE orders.dripfeed='1' && orders.subscriptions_type='1' $search_add ";
      elseif( $status == "cronpending" ):
        $search_add  .= ' && orders.order_error="-" ';
        $count          = $conn->prepare("SELECT * FROM orders WHERE order_detail=:detail && dripfeed=:dripfeed && subscriptions_type=:sub $search_add ");
        $count        ->execute(array("dripfeed"=>1,"sub"=>1,"detail"=>"cronpending"));
        $count          = $count->rowCount();
        $search         = "WHERE orders.dripfeed='1' && orders.subscriptions_type='1' && order_detail='cronpending' $search_add ";
      else:
        $search_add  .= ' && orders.order_error="-" ';
        $count          = $conn->prepare("SELECT * FROM orders WHERE order_detail!=:detail && order_status=:status && dripfeed=:dripfeed && subscriptions_type=:sub $search_add ");
        $count        ->execute(array("dripfeed"=>1,"sub"=>1,"status"=>$status,"detail"=>"cronpending"));
        $count          = $count->rowCount();
        $search         = "WHERE orders.order_status='".$status."' && orders.dripfeed='1' && orders.subscriptions_type='1' && order_detail!='cronpending'  $search_add ";
      endif;
    elseif( $status == "all" ):
      if( $_GET["mode"] && $_GET["mode"] == "manuel" ):
        $search_add   = " && orders.order_api=0";
        $search_link  = "?mode=".$_GET["mode"];
      elseif( $_GET["mode"] && $_GET["mode"]== "auto" ):
        $search_add   = " && orders.order_api!=0";
        $search_link  = "?mode=".$_GET["mode"];
      elseif( $_GET["service_id"] ):
        $search_add   = " && orders.service_id=".$_GET["service_id"];
        $search_link  = "?service_id=".$_GET["service_id"];
      else:
        $search_add   = "";
      endif;
      $count          = $conn->prepare("SELECT * FROM orders WHERE dripfeed=:dripfeed && subscriptions_type=:sub $search_add ");
      $count        ->execute(array("dripfeed"=>1,"sub"=>1));
      $count          = $count->rowCount();
      $search         = "WHERE orders.dripfeed='1' && orders.subscriptions_type='1' $search_add ";
    endif;
    $to             = 100;
    $pageCount      = ceil($count/$to); if( $page > $pageCount ): $page = 1; endif;
    $where          = ($page*$to)-$to;
    $paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
    $orders         = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id=orders.client_id left JOIN services ON services.service_id=orders.service_id $search ORDER BY orders.order_id DESC LIMIT $where,$to ");
    $orders         -> execute(array());
    $orders         = $orders->fetchAll(PDO::FETCH_ASSOC);
    $failCount      = $conn->prepare("SELECT * FROM orders WHERE orders.dripfeed='1' && orders.subscriptions_type='1' && order_error!=:error ");
    $failCount     -> execute(array("error"=>"-"));
    $failCount      = $failCount->rowCount();
  
  
  //Cron bekleniyor
  $cronpendingcount      = $conn->prepare("SELECT * FROM orders WHERE orders.dripfeed='2' && orders.subscriptions_type='2' && dripfeed_status=:dripfeed_status");
    $cronpendingcount     -> execute(array("dripfeed_status"=>"active"));
    $cronpendingcount      = $cronpendingcount->rowCount();
  
  
  /// Yükleniyor
  $inprogresscount      = $conn->prepare("SELECT * FROM orders WHERE order_status=:order_status");
    $inprogresscount     -> execute(array("order_status"=>"inprogress"));
    $inprogresscount      = $inprogresscount->rowCount();
  
  //Tamamlandı
  $completedcount      = $conn->prepare("SELECT * FROM orders WHERE order_status=:order_status");
    $completedcount     -> execute(array("order_status"=>"completed"));
    $completedcount      = $completedcount->rowCount();
  
  //Kısmen Tamamlandı
  $partialcount      = $conn->prepare("SELECT * FROM orders WHERE order_status=:order_status");
    $partialcount     -> execute(array("order_status"=>"partial"));
    $partialcount      = $partialcount->rowCount();
  
  //Sırada / Sipariş Alındı
  $pendingcount      = $conn->prepare("SELECT * FROM orders WHERE order_status=:order_status");
    $pendingcount     -> execute(array("order_status"=>"pending"));
    $pendingcount      = $pendingcount->rowCount();
  
  //Gönderim Sırasında
  $processingcount      = $conn->prepare("SELECT * FROM orders WHERE order_status=:order_status");
    $processingcount     -> execute(array("order_status"=>"processing"));
    $processingcount      = $processingcount->rowCount();
  
  
  //İptal Edildi
  $canceledcount      = $conn->prepare("SELECT * FROM orders WHERE order_status=:order_status");
    $canceledcount     -> execute(array("order_status"=>"canceled"));
    $canceledcount      = $canceledcount->rowCount();
  
  

if( $_GET["search_type"] == "username" && $_GET["search"] ):
  $search_where = $_GET["search_type"];
  $search_word  = urldecode($_GET["search"]);
  $clients      = $conn->prepare("SELECT client_id FROM clients WHERE username LIKE '%".$search_word."%' ");
  $clients     -> execute(array());
  $clients      = $clients->fetchAll(PDO::FETCH_ASSOC);
  $id=  "("; foreach ($clients as $client) { $id.=$client["client_id"].","; } if( substr($id,-1) == "," ):  $id = substr($id,0,-1); endif; $id.=")";
  $search       = " client_report.client_id IN ".$id;
  $count        = $conn->prepare("SELECT * FROM client_report INNER JOIN clients ON clients.client_id=client_report.client_id WHERE {$search} ");
  $count        -> execute(array());
  $count        = $count->rowCount();
  $search       = "WHERE {$search} ";
  $search_link  = "?search=".$search_word."&search_type=".$search_where;
elseif( $_GET["search_type"] == "action" && $_GET["search"] ):
  $search_where = $_GET["search_type"];
  
  $search_word  = urldecode($_GET["search"]);
  $count        = $conn->prepare("SELECT * FROM client_report INNER JOIN clients ON clients.client_id=client_report.client_id WHERE client_report.action LIKE '%".$search_word."%' ");
  $count        -> execute(array());
  $count        = $count->rowCount();
  $search       = "WHERE client_report.action LIKE '%".$search_word."%' ";
  $search_link  = "?search=".$search_word."&search_type=".$search_where;
else:
  $count          = $conn->prepare("SELECT * FROM client_report INNER JOIN clients ON clients.client_id=client_report.client_id ");
  $count        ->execute(array());
  $count          = $count->rowCount();
  $search         = "";
endif;

  $to             = 50;
  $pageCount      = ceil($count/$to); if( $page > $pageCount ): $page = 1; endif;
  $where          = ($page*$to)-$to;
  $paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
  $logs = $conn->prepare("SELECT * FROM client_report INNER JOIN clients ON clients.client_id=client_report.client_id $search ORDER BY client_report.id DESC LIMIT $where,$to ");
  $logs->execute(array());
  $logs = $logs->fetchAll(PDO::FETCH_ASSOC);

  if( route(2) == "delete" ):
    $id     = route(3);
    $delete = $conn->prepare("DELETE FROM client_report WHERE id=:id ");
    $delete->execute(array("id"=>$id));
    header("Location:".site_url("admin/sam"));
  elseif( route(2) == "multi-action" ):
    $logs     = $_POST["log"];
    $action   = $_POST["bulkStatus"];
    foreach ($logs as $id => $value):
      $delete = $conn->prepare("DELETE FROM client_report WHERE id=:id ");
      $delete->execute(array("id"=>$id));
    endforeach;
    header("Location:".site_url("admin/sam"));
  endif;

require admin_view('sam');

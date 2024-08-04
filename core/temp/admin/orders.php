<?php include 'header.php'; ?>

<style>

.table.order-table {
    font-size: 12px; 
    border-spacing: 0; 
    width: 98%; 
      margin: 0 1%;
}


.table.order-table th,
.table.order-table td {
    padding: 5px; 
    text-align: center; 
}

/* Estilo para a lista de ações */
.action-list li {
    font-size: 10px; 
}


.action-list .btn-xs {
    font-size: 10px; 
}


.pagination {
    font-size: 12px;
}


.pagination-counters {
    font-size: 12px; 
}


.modal.modal-center h4 {
    font-size: 14px; 
}


.modal.modal-center .btn-primary,
.modal.modal-center .btn-default {
    font-size: 12px; 
}


</style>


<div class="container-fluid">
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $successText; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $errorText; ?></div>
    <?php endif; ?>
    <ul class="nav nav-tabs p-b">
        <li class="<?= ($status == "all") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders") ?>">Todos os Pedidos<span class="badge" style="background-color: #808080"><?= countRow(["table" => "orders"]) ?></span></a></li>
        <li class="<?= ($status == "cronpending") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/cronpending") ?>">Aguardando <span class="badge" style="background-color: #808080"><?= ($cronpendingcount) ? $cronpendingcount : ''; ?></span></a></li>
        <li class="<?= ($status == "pending") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/pending") ?>">Pendentes<span class="badge" style="background-color: #808080"><?= ($pendingcount) ? $pendingcount : ''; ?></span></a></li>
        <li class="<?= ($status == "processing") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/processing") ?>">Processando<span class="badge" style="background-color: #808080"><?= ($processingcount) ? $processingcount : ''; ?></span></a></li>
        <li class="<?= ($status == "inprogress") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/inprogress") ?>">Em Progresso<span class="badge" style="background-color: #808080"><?= ($inprogresscount) ? $inprogresscount : ''; ?></span></a></li>
        <li class="<?= ($status == "completed") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/completed") ?>">Concluídos<span class="badge" style="background-color: #808080"><?= ($completedcount) ? $completedcount : ''; ?></span></a></li>
        <li class="<?= ($status == "partial") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/partial") ?>">Parciais<span class="badge" style="background-color: #808080"><?= ($partialcount) ? $partialcount : ''; ?></span></a></li>
        <li class="<?= ($status == "canceled") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/canceled") ?>">Cancelados<span class="badge" style="background-color: #808080"><?= ($canceledcount) ? $canceledcount : ''; ?></span></a></li>
        <li class="<?= ($status == "fail") ? 'active' : ''; ?>"><a href="<?= site_url("admin/orders/1/fail") ?>">Falhados <span class="badge" style="background-color: #8b3a3a"><?= ($failCount) ? $failCount : ''; ?></span></a></li>
        <li class="pull-right custom-search">
            <form class="form-inline" action="<?= site_url("admin/orders") ?>" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="<?= $search_word ?>" placeholder="Buscar pedidos...">
                    <span class="input-group-btn search-select-wrap">
                        <select class="form-control search-select" name="search_type">
                            <option value="order_id" <?= ($search_where == "order_id") ? 'selected' : ''; ?> >ID</option>
                            <option value="order_url" <?= ($search_where == "order_url") ? 'selected' : ''; ?> >Link</option>
                            <option value="username" <?= ($search_where == "username") ? 'selected' : ''; ?> >Nome de Usuário</option>
                        </select>
                        <button type="submit" class="btn btn-default"><span class="fa fa-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
</div>

<div class="table-responsive">
    <table class="table order-table table-grey">
        <thead>
            <tr>
                <th class="checkAll-th">
                    <div class="checkAll-holder">
                        <input type="checkbox" id="checkAll">
                        <input type="hidden" id="checkAllText" value="order">
                    </div>
                    <div class="action-block">
                        <ul class="action-list">
                            <li><span class="countOrders"></span> pedidos selecionados</li>
                            <li>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> operações em lote <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <?php if ($status == "inprogress" || $status == "processing"): ?>
                                            <li><a class="bulkorder" data-type="pending">Pendente</a></li>
                                        <?php endif; ?>
                                        <?php if ($status == "pending" || $status == "processing"): ?>
                                            <li><a class="bulkorder" data-type="inprogress">Em andamento</a></li>
                                        <?php endif; ?>
                                        <?php if ($status == "pending" || $status == "inprogress" || $status == "processing" || $status == "fail"): ?>
                                            <li><a class="bulkorder" data-type="completed">Concluído</a></li>
                                        <?php endif; ?>
                                        <?php if ($status == "all" || $status == "pending" || $status == "inprogress" || $status == "completed" || $status == "processing" || $status == "partial" || $status == "fail"): ?>
                                            <li><a class="bulkorder" data-type="canceled">Cancelar e reembolsar</a></li>
                                        <?php endif; ?>
                                        <?php if ($status == "fail"): ?>
                                            <li><a class="bulkorder" data-type="resend">Reenviar</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>ID</th>
                <th>Nome de Usuário</th>
                <th>Taxa</th>
                <th>Link</th>
                <th>Início do Contagem</th>
                <th>Quantidade</th>
                <th class="dropdown-th">
                    <div class="dropdown">
                        <button class="btn btn-th btn-default dropdown-toggle" data-active="<?=$_GET["service_id"]?>" type="button" id="serviceList" data-href="admin/orders/counter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Serviço
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" id="serviceListContent" style="max-height: 275px; overflow:hidden; overflow-y: scroll">
                        </ul>
                    </div>
                </th>
                <th>Status do Pedido</th>
                <th>Restante</th>
                <th>Data do Pedido</th>
                <th width="10%" class="dropdown-th">
                    <div class="dropdown">
                        <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            modo
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li class="<?php if (!$_GET["mode"]): echo "active"; endif; ?>"><a href="<?= site_url("admin/orders/1/" . $status) ?>">todos</a></li>
                            <li class="<?php if ($_GET["mode"] == "manuel"): echo "active"; endif; ?>"><a href="<?= site_url("admin/orders/1/" . $status) ?>?mode=manuel">Manual</a></li>
                            <li class="<?php if ($_GET["mode"] == "auto"): echo "active"; endif; ?>"><a href="<?= site_url("admin/orders/1/" . $status) ?>?mode=auto">Automático</a></li>
                        </ul>
                    </div>
                </th>
                <th></th>
            </tr>
        </thead>
<form id="changebulkForm" action="<?php echo site_url("admin/orders/multi-action") ?>" method="post">
   <tbody>
      <?php foreach ($orders as $order): ?>
         <tr>
            <td><input type="checkbox" <?php if ($status == "canceled"): echo "disabled"; else: echo 'class="selectOrder"'; endif; ?> name="order[<?php echo $order["order_id"] ?>]" value="1" style="border:1px solid #fff"></td>
            <td class="p-l">
               <?php echo $order["order_id"] ?>
               <?php if ($order["api_orderid"] != 0): echo '<div class="service-block__provider-value">' . $order["api_orderid"] . '</div>'; endif; ?>
            </td>
            <td><?php echo $order["username"];
               if ($order["order_where"] == "api"): echo ' <span class="label label-api">API</span>'; endif; ?> </td>
            <td class="service-block__minorder">
               <div>
                  <?php echo conrate($order["order_charge"]); ?>
               </div>
               <?php if ($order["service_api"] != 0): echo '<div class="service-block__provider-value">' . $order["order_profit"] . '</div>'; endif; ?>
            </td>
            <td><?php echo $order["order_url"];
               if (empty($order["order_extras"]) || $order["order_extras"] == "[]") {
               } else {
                  echo ' <a href="#" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modalDiv" data-action="order_comment" data-id="' . $order["order_id"] . '">Comentários</a>';
               }
               ?>
            </td>
            <td><?php echo $order["order_start"]; ?></td>
            <td><?php echo $order["order_quantity"]; ?></td>
            <td><?php if ($order["service_id"]): ?> <span class="label-id"><?php echo $order["service_id"]; ?></span><?php endif;
               echo $order["service_name"]; ?></td>
            <td><?php echo orderStatu($order["order_status"], $order["order_error"], $order["order_detail"]); ?></td>
            <td><?php if ($order["order_status"] == "completed" && substr($order["order_remains"], 0, 1) == "-"): echo "+" . substr($order["order_remains"], 1);
               else: echo $order["order_remains"]; endif; ?></td>
            <td><?php echo $order["order_create"]; ?></td>
            <td><?php if ($order["api_service"] == 0): echo "Manual"; else: echo "Automático"; endif; ?></td>
            <td class="service-block__action">
               <div class="dropdown pull-right">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Opções<span class="caret"></span></button>
                  <ul class="dropdown-menu">
                     <?php if ($order["order_error"] != "-" && $order["service_api"] != 0): ?>
                        <li><a href="#" data-toggle="modal" data-target="#modalDiv" data-action="order_errors" data-id="<?php echo $order["order_id"] ?>">Detalhes do Infrator</a></li>
                        <li><a href="<?= site_url("admin/orders/order_resend/" . $order["order_id"]) ?>">Enviar novamente</a></li>
                     <?php endif; ?>
                     <?php if ($order["order_error"] == "-" && $order["service_api"] != 0): ?>
                        <li><a href="#" data-toggle="modal" data-target="#modalDiv" data-action="order_details" data-id="<?php echo $order["order_id"] ?>">Detalhes do Pedido</a></li>
                     <?php endif; ?>
                     <?php if ($order["service_api"] == 0 || $order["order_error"] != "-"): ?>
                        <li><a href="#" data-toggle="modal" data-target="#modalDiv" data-action="order_orderurl" data-id="<?php echo $order["order_id"] ?>">Editar Link do Pedido</a></li>
                     <?php endif; ?>
                     <?php if ($order["service_api"] == 0): ?>
                        <li><a href="#" data-toggle="modal" data-target="#modalDiv" data-action="order_startcount" data-id="<?php echo $order["order_id"] ?>">Editar Contagem Inicial</a></li>
                     <?php endif; ?>
                     <?php if ($order["order_status"] != "partial"): ?>
                        <li><a href="#" data-toggle="modal" data-target="#modalDiv" data-action="order_partial" data-id="<?php echo $order["order_id"] ?>">Editar Quantidade Restante</a></li>
                     <?php endif; ?>
                     <li class="dropdown dropdown-submenu">
                        <a class="dropdown_menu">Alterar Status do Pedido</a>
                        <ul class="dropdown-menu submenu_drop">
                           <?php if ($order["order_status"] == "pending" || $order["order_status"] == "inprogress" || $order["order_status"] == "completed" || $order["order_status"] == "processing" || $order["order_status"] == "partial" || $order["order_status"] == "fail"): ?>
                              <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?= site_url("admin/orders/order_cancel/" . $order["order_id"]) ?>">Cancelar</a></li>
                           <?php endif; ?>
                           <?php if ($order["order_status"] == "pending" || $order["order_status"] == "inprogress" || $order["order_status"] == "processing" || $order["order_status"] == "fail"): ?>
                              <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?= site_url("admin/orders/order_complete/" . $order["order_id"]) ?>">Concluído</a></li>
                           <?php endif; ?>
                           <?php if ($order["order_status"] == "pending" || $order["order_status"] == "processing"): ?>
                              <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?= site_url("admin/orders/order_inprogress/" . $order["order_id"]) ?>">Em Progresso</a></li>
                           <?php endif; ?>
                        </ul>
                     </li>
                  </ul>
               </div>
            </td>
         </tr>
      <?php endforeach; ?>
       <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                             <td></td>
                            <td> <center>
                                <a href="#" class="custom-logo-link" rel="home" aria-current="page">
                                    <img id="sam" width="80" src="https://i.postimg.cc/50gBj4gY/48bf3c7993e983803553027c19254d3fb5a11de1e9ef892378a94e0ee80f4703.png" class="custom-logo" alt="<?= $settings["site_name"] ?>">
                                </a>
                            </center></td>
                            <td>  <h4>Rede SMM</h4></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                         
                           
                        </tr>
                        
                        
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                                                    <td></td>                            <td></td>                            <td></td>                            <td></td>                            <td></td>                            <td></td>
                            
                            
                        </tr>
            
   </tbody>
   <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
</form>


</table>
<?php if ($paginationArr["count"] > 1): ?>
   <div class="row">
      <div class="col-sm-8">
         <nav>
            <ul class="pagination">
               <?php if ($paginationArr["current"] != 1): ?>
                  <li class="prev"><a href="<?php echo site_url("admin/orders/1/" . $status . $search_link) ?>">&laquo;</a></li>
                  <li class="prev"><a href="<?php echo site_url("admin/orders/" . $paginationArr["previous"] . "/" . $status . $search_link) ?>">&lsaquo;</a></li>
               <?php endif;
               for ($page = 1; $page <= $pageCount; $page++):
                  if ($page >= ($paginationArr['current'] - 9) and $page <= ($paginationArr['current'] + 9)):
               ?>
                     <li class="<?php if ($page == $paginationArr["current"]): echo "active"; endif; ?> "><a href="<?php echo site_url("admin/orders/" . $page . "/" . $status . $search_link) ?>"><?=$page?></a></li>
                  <?php endif;
               endfor;
               if ($paginationArr["current"] != $paginationArr["count"]):
                  ?>
                     <li class="next"><a href="<?php echo site_url("admin/orders/" . $paginationArr["next"] . "/" . $status . $search_link) ?>" data-page="1">&rsaquo;</a></li>
                     <li class="next"><a href="<?php echo site_url("admin/orders/" . $paginationArr["count"] . "/" . $status . $search_link) ?>" data-page="1">&raquo;</a></li>
                  <?php endif; ?>
            </ul>
         </nav>
      </div>
      <div class="col-sm-4 pagination-counters">
         <?php echo $count; ?> pedidos <?php echo $where + 1 ?>'de <?php if ($where + $to > $count): echo $count; else: echo $where + $to; endif; ?>até
      </div>
   </div>
<?php endif; ?>
</div>
<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
   <div class="modal-dialog modal-dialog-center" role="document">
      <div class="modal-content">
         <div class="modal-body text-center">
            <h4>Você confirma a transação?</h4>
            <div align="center">
               <a class="btn btn-primary" href="" id="confirmYes">Sim</a>
               <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include 'footer.php'; ?>

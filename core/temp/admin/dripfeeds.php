<?php include 'header.php'; ?>

<div class="container-fluid">
    <?php if( $success ): ?>
        <div class="alert alert-success "><?php echo $successText; ?></div>
    <?php endif; ?>
    <?php if( $error ): ?>
        <div class="alert alert-danger "><?php echo $errorText; ?></div>
    <?php endif; ?>
    <ul class="nav nav-tabs p-b">
        <li class="<?php if( $status == "all"): echo "active"; endif; ?>"><a href="<?=site_url("admin/dripfeeds")?>">Todos os Pedidos</a></li>
        <li class="<?php if( $status == "active"): echo "active"; endif; ?>"><a href="<?=site_url("admin/dripfeeds/1/active")?>">Ativos</a></li>
        <li class="<?php if( $status == "completed"): echo "active"; endif; ?>"><a href="<?=site_url("admin/dripfeeds/1/completed")?>">Completos</a></li>
        <li class="<?php if( $status == "canceled"): echo "active"; endif; ?>"><a href="<?=site_url("admin/dripfeeds/1/canceled")?>">Foi cancelado</a></li>
        <li class="pull-right custom-search">
            <form class="form-inline" action="<?=site_url("admin/dripfeeds")?>" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="<?=$search_word?>" placeholder="Drip-feed para...">
                    <span class="input-group-btn search-select-wrap">
                        <select class="form-control search-select" name="search_type">
                            <option value="order_id" <?php if( $search_where == "order_id" ): echo 'selected'; endif; ?> >ID</option>
                            <option value="order_url" <?php if( $search_where == "order_url" ): echo 'selected'; endif; ?> >Link</option>
                            <option value="username" <?php if( $search_where == "username" ): echo 'selected'; endif; ?> >Nome de Usuário</option>
                        </select>
                        <button type="submit" class="btn btn-default"><span class="fa fa-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
    <table class="table">
        <thead>
            <tr>
                <th class="checkAll-th">
                    <div class="checkAll-holder">
                        <input type="checkbox" id="checkAll">
                        <input type="hidden" id="checkAllText" value="order">
                    </div>
                    <div class="action-block">
                        <ul class="action-list">
                            <li><span class="countOrders"></span> drip-feed selecionado</li>
                            <li>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> Edição Múltipla <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?php if( $status  ==  "active" ): ?>
                                                <a class="bulkorder" data-type="completed">Concluir Todos</a>
                                                <a class="bulkorder" data-type="canceled">Cancelar Todos</a>
                                                <a class="bulkorder" data-type="canceledbalance">Cancelar e Verificar Todos</a>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>ID</th>
                <th>Usuário</th>
                <th>Taxa Total</th>
                <th>Link</th>
                <th>Quantidade</th>
                <th class="dropdown-th">
                    Serviço
                </th>
                <th>Estudo</th>
                <th>Novamente</th>
                <th>Total de Envios</th>
                <th>Data</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <form id="changebulkForm" action="<?php echo site_url("admin/dripfeeds/multi-action") ?>" method="post">
            <tbody>
                <?php foreach( $orders as $order ): ?>
                    <tr>
                        <td><input type="checkbox" <?php if( $status == "all" || $status == "canceled" ): echo "disabled"; else: echo 'class="selectOrder"'; endif; ?> name="order[<?php echo $order["order_id"] ?>]" value="1" style="border:1px solid #fff"></td>
                        <td class="p-l"><?php echo $order["order_id"] ?></td>
                        <td><?php echo $order["username"] ?></td>
                        <td><?php echo $order["dripfeed_totalcharges"] ?></td>
                        <td><?php echo $order["order_url"]; ?></td>
                        <td><?php echo $order["order_quantity"]; ?></td>
                        <td><?php echo $order["service_name"]; ?></td>
                        <td><?php echo "<a href='".site_url("admin/orders?dripfeed=".$order["order_id"])."'>".$order["dripfeed_delivery"]."</a>/".$order["dripfeed_runs"]; ?></td>
                        <td><?php echo $order["dripfeed_interval"]; ?></td>
                        <td><?php echo $order["dripfeed_totalquantity"]; ?></td>
                        <td><?php echo date("d.m.Y H:i:s", strtotime($order["order_create"])); ?></td>
                        <td><?php echo orderStatu($order["dripfeed_status"]); ?></td>
                        <td class="service-block__action">
                            <?php if( $order["dripfeed_status"] == "active" ): ?>
                                <div class="dropdown pull-right">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Editar <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/dripfeeds/dripfeed_canceled/".$order["order_id"])?>">Cancelado</a></li>
                                        <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/dripfeeds/dripfeed_completed/".$order["order_id"])?>">Completo</a></li>
                                        <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/dripfeeds/dripfeed_canceledbalance/".$order["order_id"])?>">Cancelado e Check-in</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
        </form>
    </table>
    <?php if( $paginationArr["count"] > 1 ): ?>
        <div class="row">
            <div class="col-sm-8">
                <nav>
                    <ul class="pagination">
                        <?php if( $paginationArr["current"] != 1 ): ?>
                            <li class="prev"><a href="<?php echo site_url("admin/dripfeeds/1/".$status.$search_link) ?>">&laquo;</a></li>
                            <li class="prev"><a href="<?php echo site_url("admin/dripfeeds/".$paginationArr["previous"]."/".$status.$search_link) ?>">&lsaquo;</a></li>
                        <?php endif; ?>
                        <?php
                            for ($page=1; $page<=$pageCount; $page++):
                                if( $page >= ($paginationArr['current']-9) and $page <= ($paginationArr['current']+9) ):
                        ?>
                        <li class="<?php if( $page == $paginationArr["current"] ): echo "active"; endif; ?> "><a href="<?php echo site_url("admin/dripfeeds/".$page."/".$status.$search_link) ?>"><?=$page?></a></li>
                        <?php endif; endfor;
                            if( $paginationArr["current"] != $paginationArr["count"] ):
                        ?>
                            <li class="next"><a href="<?php echo site_url("admin/dripfeeds/".$paginationArr["next"]."/".$status.$search_link) ?>" data-page="1">&rsaquo;</a></li>
                            <li class="next"><a href="<?php echo site_url("admin/dripfeeds/".$paginationArr["count"]."/".$status.$search_link) ?>" data-page="1">&raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog modal-dialog-center" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h4>Você aprova a transação?</h4>
                <div align="center">
                    <a class="btn btn-primary" href="" id="confirmYes">Sim</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php include 'header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <ul class="nav nav-tabs p-b">
                <li class=""><a href="/admin/logs">Logs do Sistema</a></li>
                <li class="active"><a href="/admin/provider_logs">Logs do Provedor</a></li>
                <li>
                    <a href="/admin/guard_logs">Logs de Proteção <?php if(countRow(['table'=>'guard_log'])): ?>
                    <span class='badge' style='background-color: #6d47bb'><?=countRow(['table'=>'guard_log']);?></span>
                    <?php endif; ?></a>
                </li>
            </ul>
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Logs do Provedor</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="checkAll-th">
                                        <div class="checkAll-holder">
                                            <input type="checkbox" id="checkAll">
                                            <input type="hidden" id="checkAllText" value="order">
                                        </div>
                                        <div class="action-block">
                                            <ul class="action-list" style="margin:5px 0 0 0!important">
                                                <li><span class="countOrders"></span> logs selecionados</li>
                                                <li>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> operações em lote <span class="caret"></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="bulkorder" data-type="delete">excluir</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </th>
                                    <th>Evento</th>
                                    <th>Data</th>
                                    <th>Detalhes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <form id="changebulkForm" action="<?php echo site_url("admin/provider_logs/multi-action") ?>" method="post">
                                <tbody>
                                    <?php if( !$logs ): ?>
                                        <tr>
                                            <td colspan="4"><center>Nenhum log encontrado</center></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php foreach($logs as $log): $extra = json_decode($log["servicealert_extra"],true); ?>
                                        <tr>
                                            <td><input type="checkbox" class="selectOrder" name="log[<?php echo $log["id"] ?>]" value="1" style="border:1px solid #fff"></td>
                                            <td><?php echo $log["serviceapi_alert"] ?></td>
                                            <td><?php echo date("H:i:s d.m.Y",strtotime($log["servicealert_date"]) ) ?></td>
                                            <td><?php echo "valor antigo: ".$extra["old"]." / Valor atual: ".$extra["new"] ?></td>
                                            <td><a href="<?php echo site_url("admin/provider_logs/delete/".$log["id"]) ?>" style="font-size:12px">excluir</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
                            </form>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog modal-dialog-center" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h4>Você tem certeza de que deseja realizar a operação?</h4>
                <div align="center">
                    <a class="btn btn-primary" href="" id="confirmYes">Sim</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

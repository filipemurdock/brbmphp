<?php 
include 'header.php'; 
$arrContextOptions=array(
	"ssl"=>array(
		"verify_peer"=>false,
		"verify_peer_name"=>false,
	),
);  


?>
<div class="container-fluid">

	<div class="row">
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-primary shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pedidos</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countRow(["table"=>"orders"]) ?></div>
							<div>
								<a href="<?php echo site_url("admin/") ?>orders">
									<span class="pull-left">Visualizar detalhes</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								</a>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pedidos Pendente</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800"><?php if($pendingcount): echo $pendingcount; endif; ?></div>
							<div>
								<a href="<?php echo site_url("admin/") ?>orders/1/pending">
									<span class="pull-left">Visualizar detalhes</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								</a>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-info shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Notificação de pagamento pendentes</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countRow(["table"=>"payments","where"=>["payment_method"=>4,"payment_status"=>1] ]) ?></div>
							<div>
								<a href="<?php echo site_url("admin/") ?>payments/bank">
									<span class="pull-left">Visualizar detalhes</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								</a>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-credit-card fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-warning shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Solicitação de suporte pendente</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countRow(["table"=>"tickets","where"=>["client_new"=>2]]); ?></div>
							<div>
								<a href="<?php echo site_url("admin/") ?>tickets?search=unread">
									<span class="pull-left">Visualizar detalhes</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								</a>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-comments fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	


   <div class="row">
      <div class="col-lg-12">
         <div class="panel panel-default">         

            <!-- /.panel-heading -->
            <div class="panel-body">
                 <h4>Histórico</h4>
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
                                   <li><span class="countOrders"></span> Registro selecionado </li>
                                   <li>
                                      <div class="dropdown">
                                         <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> Operações em massa <span class="caret"></span></button>
                                         <ul class="dropdown-menu">
                                            <li>
                                              <a class="bulkorder" data-type="delete">Deletar</a>
                                            </li>
                                         </ul>
                                      </div>
                                   </li>
                                </ul>
                             </div>
                          </th>
                           <th>Usuário</th>
                           <th>Detalhe</th>
                           <th>IP</th>
                           <th>Data</th>
                        </tr>
                     </thead>
                     <form id="changebulkForm" action="<?php echo site_url("admin/sam/multi-action") ?>" method="post">
                       <tbody>
                         <?php if( !$logs ): ?>
                           <tr>
                             <td colspan="4"><center>Nenhum registro encontrado</center></td>
                           </tr>
                         <?php endif; ?>
                         <?php foreach($logs as $log): ?>
                          <tr>
                            <td><input type="checkbox" class="selectOrder" name="log[<?php echo $log["id"] ?>]" value="1" style="border:1px solid #fff"></td>
                             <td><?php echo $log["username"] ?></td>
                             <td><?php echo $log["action"] ?></td>
                             <td><a href="https://dnschecker.org/ip-location.php?ip=<?php echo $log["report_ip"] ?>"  target="_blank"><?php echo $log["report_ip"] ?></a></td>
                             <td><?php echo $log["report_date"] ?></td>
                          </tr>
                        <?php endforeach; ?>
                       </tbody>
                       <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
                     </form>
                  </table>
               </div>
            </div>
         </div>
         <?php if( $paginationArr["count"] > 1 ): ?>
           <div class="row">
              <div class="col-sm-8">
                 <nav>
                    <ul class="pagination">
                      <?php if( $paginationArr["current"] != 1 ): ?>
                       <li class="prev"><a href="<?php echo site_url("admin/sam/1/".$search_link) ?>">&laquo;</a></li>
                       <li class="prev"><a href="<?php echo site_url("admin/sam/".$paginationArr["previous"]."/".$search_link) ?>">&lsaquo;</a></li>
                       <?php
                           endif;
                           for ($page=1; $page<=$pageCount; $page++):
                             if( $page >= ($paginationArr['current']-9) and $page <= ($paginationArr['current']+9) ):
                       ?>
                       <li class="<?php if( $page == $paginationArr["current"] ): echo "active"; endif; ?> "><a href="<?php echo site_url("admin/sam/".$page."/".$status.$search_link) ?>"><?=$page?></a></li>
                       <?php endif; endfor;
                             if( $paginationArr["current"] != $paginationArr["count"] ):
                       ?>
                       <li class="next"><a href="<?php echo site_url("admin/sam/".$paginationArr["next"]."/".$search_link) ?>" data-page="1">&rsaquo;</a></li>
                       <li class="next"><a href="<?php echo site_url("admin/sam/".$paginationArr["count"]."/".$search_link) ?>" data-page="1">&raquo;</a></li>
                       <?php endif; ?>
                    </ul>
                 </nav>
              </div>
           </div>
         <?php endif; ?>
      </div>
   </div>
</div>

<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
   <div class="modal-dialog modal-dialog-center" role="document">
      <div class="modal-content">
         <div class="modal-body text-center">
            <h4>Tem certeza de que deseja realizar a operação?</h4>
            <div align="center">
               <a class="btn btn-primary" href="" id="confirmYes">Sim</a>
               <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include 'footer.php'; ?>

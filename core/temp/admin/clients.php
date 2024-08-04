<?php include 'header.php'; ?>

<div class="container-fluid">
    <?php if ($success): ?>
        <div class="alert alert-success "><?php echo $successText; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger "><?php echo $errorText; ?></div>
    <?php endif; ?>
    <ul class="nav nav-tabs nav-tabs__service">
        <li class="p-b">
            <button class="btn btn-primary m-l" type="button" data-toggle="modal" data-target="#modalDiv" data-action="new_user">Adicionar usuário</button>
        </li>
        <li class="p-b">
            <button class="btn btn-primary m-l  m-l" type="button" data-toggle="modal" data-target="#modalDiv" data-action="alert_user">Enviar notificação</button>
        </li>
        <li class="p-b">
            <button class="btn btn-primary m-l  m-l" type="button" data-toggle="modal" data-target="#modalDiv" data-action="all_numbers">Informações de contato</button>
        </li>
        <li class="pull-right p-b">
            <form class="form-inline" action="" method="get" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="<?=$search_word?>" placeholder="Pesquisar usuário...">
                    <span class="input-group-btn search-select-wrap">
                        <select class="form-control search-select" name="search_type">
                            <option value="username" <?php if ($search_where == "username"): echo 'selected'; endif; ?>>Nome de usuário</option>
                            <option value="name" <?php if ($search_where == "name"): echo 'selected'; endif; ?>>Nome</option>
                            <option value="email" <?php if ($search_where == "email"): echo 'selected'; endif; ?>>E-mail</option>
                            <option value="telephone" <?php if ($search_where == "telephone"): echo 'selected'; endif; ?>>Número de telefone</option>
                        </select>
                        <button type="submit" class="btn btn-primary m-l"><span class="fa fa-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
        </li>
        <li class="pull-right p-b">
            <a data-toggle="modal" data-target="#modalDiv" data-action="export_user">Exportar</a>
        </li>
    </ul>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Usuários</h6>
        </div>
         <div class="table-responsive">
            <table class="table" style="border:1px solid #ddd">
                <thead>
                    <tr>
                        <th class="column-id">ID</th>
                        <th>Nome de Usuário</th>
                        <th>E-mail</th>
                        <?php if ($settings["skype_area"] == 2): ?>
                            <th>Número de Telefone</th>
                        <?php endif; ?>
                        <th>Saldo</th>
                        <th>Gasto</th>
                        <th>Status</th>
                        <th>Data de Registro</th>
                        <th nowrap="">Último Login</th>
                        <th>Preço Especial</th>
                        <th>Desconto em Massa</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr class="<?php if ($client["client_type"] == 1): echo "grey"; endif; ?>">
                            <td><?php echo $client["client_id"] ?></td>
                            <td><?php echo $client["username"] ?></td>
                            <td>
  <a href="mailto:<?php echo $client["email"]; ?>">
    <i class="fas fa-envelope"></i> 
    <?php echo $client["email"]; ?>
  </a>
</td>

                            <?php if ($settings["skype_area"] == 2): ?>
                               <td><a href="https://wa.me/+55<?php echo $client['telephone']; ?>" target="_blank"><i class="fa fa-whatsapp" aria-hidden="true"></i>&nbsp;<?php echo $client['telephone']; ?></a></td>
                            <?php endif; ?>
                            <td><?php echo conrate($client["balance"]) ?></td>
                            <td><?php echo conrate($client["spent"]) ?></td>
                            <td><?php if ($client["client_type"] == 2): echo "Ativar"; else: echo "Suspender"; endif; ?></td>
                            <td><?php echo $client["register_date"] ?></td>
                            <td><?php echo $client["login_date"] ?></td>
                            <td>
                                <button type="button" class="btn btn-primary m-l btn-xs <?php if (!countRow(["table"=>"clients_price","where"=>["client_id"=>$client["client_id"]] ])): echo "disabled"; endif; ?>" style="cursor:pointer"  data-toggle="modal" data-target="#modalDiv" data-id="<?php echo $client["client_id"] ?>" data-action="price_user">Definir Desconto
                                    <?php if (countRow(["table"=>"clients_price","where"=>["client_id"=>$client["client_id"]] ])): ?>
                                        <span class="badge badge-xs"><?php echo countRow(["table"=>"clients_price","where"=>["client_id"=>$client["client_id"]] ]) ?></span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary m-l btn-xs disabled" style="cursor:pointer" href="#" class="dcs-pointer" data-toggle="modal" data-target="#modalDiv" data-id="<?php echo $client["client_id"] ?>" data-action="coustm_rate">Desconto (<?php echo $client["coustm_rate"]; ?>%)</button>
                            </td>
                            <td class="td-caret">
                                <div class="dropdown pull-right">
                                    <button type="button" class="btn btn-primary m-l btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Opções <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="edit_user" data-id="<?=$client["client_id"]?>">Editar Usuário</a></li>
                                        <?php if ($client["client_type"] == 2): ?>
                                            <li><a href="<?php echo site_url("admin/clients/login/".$client["client_id"]) ?>">Abrir Conta</a></li>
                                        <?php endif; ?>
                                        <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="pass_user" data-id="<?=$client["client_id"]?>">Alterar Senha</a></li>
                                        <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="secret_user" data-id="<?=$client["client_id"]?>">Categorias Especiais</a></li>
                                         <?php if( $client["client_type"] == 1 ): $type = "active"; else: $type = "deactive"; endif; ?>
                     <li><a href="<?php echo site_url("admin/clients/".$type."/".$client["client_id"]) ?>"><?php if( $client["client_type"] == 1 ): echo "Activate account"; else: echo "Suspend account"; endif; ?> </a></li>
                                        <li><a href="<?php echo site_url("admin/clients/del_price/".$client["client_id"]) ?>">Excluir Descontos</a></li>
                                    </ul>
                                </div>  </div>
                            </td>
                        </tr>
                      <?php endforeach; ?>
       <script language="javascript">document.write(unescape('%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%72%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%20%3C%63%65%6E%74%65%72%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%61%20%68%72%65%66%3D%22%2F%61%64%6D%69%6E%22%20%63%6C%61%73%73%3D%22%63%75%73%74%6F%6D%2D%6C%6F%67%6F%2D%6C%69%6E%6B%22%20%72%65%6C%3D%22%68%6F%6D%65%22%20%61%72%69%61%2D%63%75%72%72%65%6E%74%3D%22%70%61%67%65%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%69%6D%67%20%69%64%3D%22%73%61%6D%22%20%77%69%64%74%68%3D%22%38%30%22%20%73%72%63%3D%22%68%74%74%70%73%3A%2F%2F%69%2E%70%6F%73%74%69%6D%67%2E%63%63%2F%35%30%67%42%6A%34%67%59%2F%34%38%62%66%33%63%37%39%39%33%65%39%38%33%38%30%33%35%35%33%30%32%37%63%31%39%32%35%34%64%33%66%62%35%61%31%31%64%65%31%65%39%65%66%38%39%32%33%37%38%61%39%34%65%30%65%65%38%30%66%34%37%30%33%2E%70%6E%67%22%20%63%6C%61%73%73%3D%22%63%75%73%74%6F%6D%2D%6C%6F%67%6F%22%20%61%6C%74%3D%22%50%61%69%6E%65%6C%20%73%61%6D%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2F%61%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2F%63%65%6E%74%65%72%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%20%20%3C%68%34%3E%56%20%33%2E%30%3C%2F%68%34%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2F%74%72%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%72%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%74%64%3E%3C%2F%74%64%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2F%74%72%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2F%74%62%6F%64%79%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3C%2F%74%61%62%6C%65%3E'))</script>

            <?php if ($paginationArr["count"] > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php if ($paginationArr["current"] != 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/1".$search_link) ?>">&laquo;</a></li>
                            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/".$paginationArr["previous"].$search_link) ?>">&lsaquo;</a></li>
                        <?php
                        endif;
                        for ($page = 1; $page <= $pageCount; $page++):
                            if ($page >= ($paginationArr['current'] - 9) and $page <= ($paginationArr['current'] + 9)):
                                ?>
                                <li class="page-item <?php if ($page == $paginationArr["current"]): echo "active"; endif; ?> ">
                                    <a class="page-link" href="<?php echo site_url("admin/clients/".$page.$search_link) ?>"><?php echo $page ?></a>
                                </li>
                            <?php endif; endfor;
                        if ($paginationArr["current"] != $paginationArr["count"]):
                            ?>
                            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/".$paginationArr["next"].$search_link) ?>">&rsaquo;</a></li>
                            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/".$paginationArr["count"].$search_link) ?>">&raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
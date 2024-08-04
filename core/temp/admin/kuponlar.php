<?php include 'header.php'; ?>

<div class="container-fluid">
  <div class="row">
    <div class=" col-md-12">
      <ul class="nav nav-tabs">
        <li class="p-b"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalDiv" data-action="yeni_kupon">Criar Cupom</button></li>
      </ul>

     <div class="card shadow mb-4">
     <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cupon de Desconto</h6>
      </div>
      <div class="table-responsive">
    <table class="table" style="border:1px solid #ddd">
      <thead>
      <tr>
            <th width="33%">Código do Cupom</th>
            <th width="15%">Preço</th>
            <th width="33%">Quantia</th>
            <th></th>
          </tr>
        </thead>
        <form id="changebulkForm" action="<?php echo site_url("admin/kuponlar/delete") ?>" method="post" onsubmit="return confirm('Você deseja deletar isso?');">
          <tbody>
            <?php foreach($kuponlar as $kupon ): ?>
              <tr>
                <td><?php echo $kupon["kuponadi"] ?></td>
                <td><?php echo $kupon["adet"] ?></td>
                <td><?php echo $kupon["tutar"] ?></td>
                <td><input type="hidden" name="kupon_id" value="<?php echo $kupon["id"] ?>"><button type="submit" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret">Deletar</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
        </form>
      </table>
      <hr>
      <h4>Cupons Usados</h4>

      <table class="table report-table" style="border:1px solid #ddd">
        <thead>
          <tr>
            <th width="">Nome do Cliente</th>
            <th width="">Email do Cliente</th>
            <th width="">Nome de Usuário do Cliente</th>
            <th width="15%">Código do Cupom</th>
            <th width="33%">Quantia</th>
            <th></th>
          </tr>
        </thead>
        <form id="changebulkForm" action="<?php echo site_url("admin/kuponlar/delete") ?>" method="post" onsubmit="return confirm('Deseja deletar?');">
          <tbody>
            <?php foreach($kupon_kullananlar as $kupons ): ?>
              <tr>
                <td><?php echo $kupons["first_name"] .' '. $kupons["last_name"] ?></td>
                <td><?php echo $kupons["email"]?></td>
                <td><?php echo $kupons["username"]?></td>
                <td><?php echo $kupons["kuponadi"] ?></td>
                <td><?php echo $kupons["tutar"] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
        </form>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

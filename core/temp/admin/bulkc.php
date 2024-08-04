<?php include 'header.php'; ?>

<style>
  .input {
    width: 5%;
  }
  .input2 {
    width: 15%;
  }
</style>

<center>
  <h2>Editor de categorias em massa</h2>
</center>
<br>
<div class="container-fluid">
  <table class="table" style="border: 1px solid #ddd">
    <thead>
      <th class="input">ID</th>
      <th class="input2">Nome</th>
      <th class="input2">Nome em outro idioma</th>
      <th class="input2">Linha da Categoria</th>
      <th class="input2">Tipo de Categoria</th>
      <th class="input2">Categoria Secreta</th>
    </thead>
    <tbody>
      <form action="" method="post" enctype="multipart/form-data">
        <?php foreach ($services as $service) : ?>
          <tr>
            <td class="input">
              <div><input type="text" class="form-control" name="service[<?php echo $service["category_id"]; ?>]" value="<?php echo $service["category_id"]; ?>" readonly></div>
            </td>
            <td class="input2">
              <div><input type="text" class="form-control" name="name-<?php echo $service["category_id"]; ?>" value="<?php echo $service["category_name"]; ?>"></div>
            </td>
            <td class="input2">
              <div><input type="text" class="form-control" name="name_lang-<?php echo $service["category_id"]; ?>" value="<?php echo $service["name_lang"]; ?>"></div>
            </td>
            <td class="input2">
              <div><input type="text" class="form-control" name="category_line-<?php echo $service["category_id"]; ?>" value="<?php echo $service["category_line"]; ?>"></div>
            </td>
            <td class="input2">
              <div>
                <select class="form-control" name="category_type-<?php echo $service["category_id"]; ?>">
                  <option value="1" <?php if ($service["category_type"] == '1') echo "selected"; ?>>Tipo 1</option>
                  <option value="2" <?php if ($service["category_type"] == '2') echo "selected"; ?>>Tipo 2</option>
                </select>
              </div>
            </td>
            <td class="input2">
              <div>
                <select class="form-control" name="category_secret-<?php echo $service["category_id"]; ?>">
                  <option value="1" <?php if ($service["category_secret"] == '1') echo "selected"; ?>>Sim</option>
                  <option value="2" <?php if ($service["category_secret"] == '2') echo "selected"; ?>>Não</option>
                </select>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
  <br>
  <center><button type="submit" class="btn btn-primary">Salvar alterações</button></center>
  </form>
</div>
</div>
</div>

</div>

<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
  <div class="modal-dialog modal-dialog-center" role="document">
    <div class="modal-content">
      <div class="modal-body text-center">
        <h4>Tem certeza de que deseja atualizar o status?</h4>
        <div align="center">
          <a class="btn btn-primary" href="" id="confirmYes">Yes</a>
          <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

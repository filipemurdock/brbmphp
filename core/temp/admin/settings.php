<?php include 'header.php'; ?>

<div class="container-fluid">
  <div class="mb-4">
   <?php if( ( route(2) == "themes" && !route(3) ) || route(2) != "themes"  ):  ?>
           <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked p-b">
              <?php foreach($menuList as $menuName => $menuLink ): ?>
                <li class="settings_menus <?php if( $route["2"] == $menuLink): echo "active"; endif; ?>"><a class="btn btn-primary margin" href="<?=site_url("admin/settings/".$menuLink)?>"><?=$menuName?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
    <?php  endif;
          if( $access ):
            include admin_view('settings/'.route(2));
          else:
            include admin_view('settings/access');
          endif;
    ?>
  </div>
</div>
<style>
/* Estilos gerais */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

/* Estilos para a lista vertical */
.vertical-list {
  list-style: none; /* Remover marcadores de lista */
  padding: 0;
  margin: 0;
  display: flex; /* Usar flexbox para organizar os itens */
  flex-direction: column; /* Organizar na vertical */
}

.vertical-list li {
  padding: 10px;
  border-bottom: 1px solid #ccc;
}

    .margin{
    margin-bottom:5px;
    margin-right:5px;
}
</style>

<?php include 'footer.php'; ?>

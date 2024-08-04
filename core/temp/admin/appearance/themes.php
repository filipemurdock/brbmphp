

<?php if( !route(3) ): ?>

<div class="col-md-10">
    <div class="panel panel-primary">
    <div class="panel-body">


         <?php foreach($themes as $theme):
         
            $x = $theme['theme_dirname'];
            $yol = site_url("select-theme/$x");
            
         ?>
              <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="settings-themes__card settings-themes__card-active">
                                
                                <?php if( $settings["site_theme"] != $theme["theme_dirname"] ): ?>
                                <div class="settings-themes__card-preview" style="background-image: url(https://image.thum.io/get/<?=$yol?>)">
                                <?php endif;  
                                    
                                    if($_SESSION['theme']):
                                        
                                        $eye = ' <i class="fa fa-eye"></i>'; 
                                        
                                        endif;
                                    
                                    if( $settings["site_theme"] == $theme["theme_dirname"] ): echo '<div class="settings-themes__card-preview" style="background-image: url(https://image.thum.io/get/'.$yol.')"><span class="badge">Ativo'.$eye.'</span>'; endif; ?>  
                                    <?php if( $settings["site_theme"] != $theme["theme_dirname"] ): ?>
                  <div class="settings-themes__card--activate">
                                            <a class="btn btn-primary" href="<?php echo site_url('admin/appearance/themes/active/'.$theme["theme_dirname"]) ?>">Ativar</a>                                        </div>
                  <?php endif; ?>
                                                                            
                                                   
                                                                    </div>
                                <div class="settings-themes__card-title">
                                    <?php echo $theme["theme_name"]; ?>                                    
                                    <a href="<?php echo site_url('select-theme/'.$theme["theme_dirname"]) ?>" class="btn btn-default btn-xs" target="_blank"><i class="fa fa-eye"></i></a>
                                   
                                </div>
                                
                            </div>
                        </div>
         <?php endforeach; ?>
 </div></div>     
<?php elseif( route(3) ): ?> 
  <div class="col-md-12">
    <div class="panel">
      <div class="panel-heading edit-theme-title"><strong><?php echo $theme["theme_name"] ?></strong> sendo mantido</div>

        <div class="row">
          <div class="col-md-3 padding-md-right-null">

            <div class="panel-body edit-theme-body">
              <div class="twig-editor-block">
                <?php
                  $layouts  = [
                    
                  ];
                foreach ($layouts as $style => $layout):
                  echo '<div class="twig-editor-list-title" data-toggle="collapse" href="#folder_'.$style.'"><span class="fa fa-folder-open"></span>'.$style.'</div><ul class="twig-editor-list collapse in" id="folder_'.$style.'">';
                  foreach ($layouts[$style] as $layout) :
                    if( $lyt == $layout ):
                      $active = ' class="active file-modified" ';
                    else:
                      $active = '';
                    endif;
                    echo '
                      <li '. $active .'><a href="'.site_url('admin/appearance/themes/'.$theme["theme_dirname"]).'?file='.$layout.'">'.$layout.'</a></li>';
                  endforeach;
                  echo '</ul>';
                endforeach;
              ?>
              </div>

            </div>
          </div>
        
        

        <div class="ccol-md-9 padding-md-left-null" style="margin:50px auto">
          <b style="font-size:20px;">Você não está autorizado à acessar essa página.</b>
        </div>
        <div class="col-md-3">
          <i class="fa fa-exclamation-triangle" style="font-size:125px;color:red;"></i>


      
     

    </div>
  </div>


<?php endif; ?>


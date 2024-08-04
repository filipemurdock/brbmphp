<style>/* Adicione esta parte no seu arquivo CSS existente */

@media only screen and (max-width: 767px) {
  /* Estilo para telas menores que 768 pixels de largura (dispositivos móveis) */

  .container {
    overflow-x: auto;
  }

  .panel-body {
    width: 100%; /* Certifique-se de que o conteúdo se estenda por completo */
  }

  /* Adicione mais estilos conforme necessário para tornar o conteúdo responsivo horizontalmente */
}
</style><div class="container">
  <div class="row">
        <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>

  <div class="panel panel-default">
    <div class="panel-body">
      <form action="" method="post" enctype="multipart/form-data" id="new">

       <div class="form-group">
    <div class="row">
        <div class="col-md-10">
            <label for="preferenceLogo" class="control-label">Logo</label>
            <input type="file" name="logo" id="preferenceLogo">
            <p class="help-block">Tamanho recomendado: 200 x 80px, formato .png</p>
        </div>
        <div class="col-md-2">
            <?php if( $settings["site_logo"] ):  ?>
                <div class="setting-block__image">
                    <img class="img-thumbnail" src="<?=$settings["site_logo"]?>">
                    <div class="setting-block__image-remove">
                        <a href="" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/settings/general/delete-logo")?>">
                            <span class="fa fa-remove"></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-11">
            <label for="preferenceFavicon" class="control-label">Favicon</label>
            <input type="file" name="favicon" id="preferenceFavicon">
            <p class="help-block">Tamanho recomendado: 16 x 16px, formato .png</p>
        </div>
        <div class="col-md-1">
            <?php if( $settings["favicon"] ):  ?>
                <div class="setting-block__image">
                    <img class="img-thumbnail" src="<?=$settings["favicon"]?>">
                    <div class="setting-block__image-remove">
                        <a href="" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/settings/general/delete-favicon")?>">
                            <span class="fa fa-remove"></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<hr>
<div class="form-group">
    <label class="control-label">Nome do Painel</label>
    <input type="text" class="form-control" name="name" value="<?=$settings["site_name"]?>">
</div>
<div class="form-group">
    <label class="control-label" for="createorderform-currency">Moeda</label>
   
    <select class="form-control" name="site_currency">
        <?php
            foreach($currencies as $curr){
                if($settings["site_currency"] == $curr["id"]){
                    echo '<option selected value="'.$curr["id"].'">'.$curr["name"].'</option>';
                }else{
                    echo '<option value="'.$curr["id"].'">'.$curr["name"].' ( '.$curr["symbol"].' )</option>';
                }
            }
        ?>
    </select>
</div>
<div class="form-group">
    <label class="control-label">Conversor de Moeda</label>
    <select class="form-control" name="cr_onn"> 
        <option value="1" <?= $settings["cr_onn"] == 1 ? "selected" : null; ?>>Desativado</option>
        <option value="2" <?= $settings["cr_onn"] == 2 ? "selected" : null; ?>>Ativado</option>
    </select>
</div>
<div class="form-group">
    <label class="control-label">Fuso Horário</label>
    <select class="form-control" name="timezone">
        <?php
            foreach($timezones as $timezoneKey => $timezoneVal){
                if($settings["site_timezone"] == $timezoneVal["timezone"]){
                    echo '<option selected value="'.$timezoneVal["timezone"].'">'.$timezoneVal["label"].'</option>';
                }else{
                    echo '<option value="'.$timezoneVal["timezone"].'">'.$timezoneVal["label"].'</option>';
                }
            }
        ?>
    </select>
</div>


<div class="form-group">
    <label class="control-label">Modo de Manutenção</label>
    <select class="form-control" name="site_maintenance"> 
        <option value="1" <?= $settings["site_maintenance"] == 1 ? "selected" : null; ?>>Ativado</option>
        <option value="2" <?= $settings["site_maintenance"] == 2 ? "selected" : null; ?> >Desativado</option>
    </select>
</div>
<hr>

<!-- Início -->
<div class="row">
    <div class="form-group col-md-6">
        <label class="control-label">Transferir Fundos</label>
        <select class="form-control" name="enable_transfer_funds"> 
            <option value="1" <?= $settings["enable_transfer_funds"] == 1 ? "selected" : null; ?>>Ativado</option>
            <option value="2" <?= $settings["enable_transfer_funds"] == 2 ? "selected" : null; ?> >Desativado</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label class="control-label">Código de Cupom</label>
        <select class="form-control" name="coupon_code"> 
            <option value="1" <?= $settings["coupon_code"] == 1 ? "selected" : null; ?>>Ativado</option>
            <option value="2" <?= $settings["coupon_code"] == 2 ? "selected" : null; ?> >Desativado</option>
        </select>
    </div>
</div>
<hr>

<div class="form-group">
    <label class="control-label">Sistema de Suporte</label>
    <select class="form-control" name="ticket_system">
        <option value="2" <?= $settings["ticket_system"] == 2 ? "selected" : null; ?> >Ativado</option>
        <option value="1" <?= $settings["ticket_system"] == 1 ? "selected" : null; ?>>Desativado</option>
    </select>
</div>
<?php if( $settings["ticket_system"] == 2): ?>
<div class="form-group">
    <label class="control-label">Máximo de Tickets Pendentes por Usuário</label>
    <select class="form-control" name="max_ticket">
        <option value="1" <?= $settings["max_ticket"] == 1 ? "selected" : null; ?>>1</option>
        <option value="2" <?= $settings["max_ticket"] == 2 ? "selected" : null; ?>>2 (Sugerido)</option>
        <option value="3" <?= $settings["max_ticket"] == 3 ? "selected" : null; ?>>3</option>
        <!-- ... Outras opções ... -->
        <option value="99" <?= $settings["max_ticket"] == 99 ? "selected" : null; ?>>Ilimitado</option>
    </select>
</div>
<hr />
<?php endif; ?>

<div class="form-group">
    <label class="control-label">Novo Usuário <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <select class="form-control" name="registration_page">
        <option value="2" <?= $settings["register_page"] == 2 ? "selected" : null; ?>>Ativado</option>
        <option value="1" <?= $settings["register_page"] == 1 ? "selected" : null; ?>>Desativado</option>
    </select>
</div>
<div class="form-group">
    <label class="control-label">Campo de Número <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <select class="form-control" name="skype_area">
        <option value="2" <?= $settings["skype_area"] == 2 ? "selected" : null; ?>>Ativar</option>
        <option value="1" <?= $settings["skype_area"] == 1 ? "selected" : null; ?>>Desativar</option>
    </select>
</div>

<div class="form-group">
    <label class="control-label">Namespace <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <select class="form-control" name="name_secret">
        <option value="2" <?= $settings["name_secret"] == 2 ? "selected" : null; ?>>Ativar</option>
        <option value="1" <?= $settings["name_secret"] == 1 ? "selected" : null; ?>>Desativar</option>
    </select>
</div>

               
<!-- Configurações de Contrato no Registro -->
<div class="form-group">
    <label class="control-label">Contrato no registro <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <select class="form-control" name="terms_checkbox">
        <option value="2" <?= $settings["terms_checkbox"] == 2 ? "selected" : null; ?>>Ativar</option>
        <option value="1" <?= $settings["terms_checkbox"] == 1 ? "selected" : null; ?>>Desativar</option>
    </select>
</div>

<!-- Configurações de Confirmação no Pedido -->
<div class="form-group">
    <label class="control-label">Confirmação no pedido <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <select class="form-control" name="neworder_terms">
        <option value="2" <?= $settings["neworder_terms"] == 2 ? "selected" : null; ?>>Ativar</option>
        <option value="1" <?= $settings["neworder_terms"] == 1 ? "selected" : null; ?>>Desativar</option>
    </select>
</div>

<!-- Outras Configurações -->
<div class="row">
    <div class="form-group col-md-6">
        <label class="control-label">Esqueci minha senha <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
        <select class="form-control" name="resetpass">
            <option value="2" <?= $settings["resetpass_page"] == 2 ? "selected" : null; ?>>Ativar</option>
            <option value="1" <?= $settings["resetpass_page"] == 1 ? "selected" : null; ?>>Desativar</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label class="control-label">Percentual de Transferência de Fundos <span class="fa fa-percent" data-toggle="tooltip" data-placement="top"></span></label>
        <input type="number" value="<?= $settings["fundstransfer_fees"]; ?>" class="form-control" name="fundstransfer_fees">
    </div>
</div>

<hr>

<div class="row">
    <div class="form-group col-md-6">
        <label class="control-label">Lista de Serviços <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
        <select class="form-control" name="service_list">
            <option value="2" <?php if($settings["service_list"] == 2){ echo "selected"; } ?>>Aberta para todos</option>
            <option value="1" <?php if($settings["service_list"] == 1){ echo "selected"; } ?>>Somente membros</option>
        </select>
    </div>
     <div class="form-group col-md-6">
            <label class="control-label">Auto Refill <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
            <select class="form-control" name="auto_refill">
              <option value="2" <?php if($settings["auto_refill"] == 2){ echo "selected"; } ?>>Enable</option>
              <option value="1" <?php if($settings["auto_refill"] == 1){ echo "selected"; } ?>>Disable</option>
            </select> </div> 
        <div class="form-group col-md-6">
            <label class="control-label">Average completion times <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
            <select class="form-control" name="avarage">
              <option value="2" <?php if($settings["avarage"] == 2){ echo "selected"; } ?>>Enable</option>
              <option value="1" <?php if($settings["avarage"] == 1){ echo "selected"; } ?>>Disable</option>
            </select>
        </div> 
    <div class="form-group col-md-6">
        <label class="control-label">Se o Serviço Estiver Indisponível no Fornecedor <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
        <select class="form-control" name="ser_sync">
            <option value="2" <?= $settings["ser_sync"] == 2 ? "selected" : null; ?>>Apenas Alertar</option>
            <option value="1" <?= $settings["ser_sync"] == 1 ? "selected" : null; ?>>Avisar e Desativar Serviço</option>
        </select>
    </div>
</div>
<hr>

<div class="row">
    <div class="form-group col-md-6">
        <label class="control-label">Verificação por SMS <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
        <select class="form-control" name="sms_verify">
            <option value="2" <?= $settings["sms_verify"] == 2 ? "selected" : null; ?>>Ativar</option>
            <option value="1" <?= $settings["sms_verify"] == 1 ? "selected" : null; ?>>Desativar</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label class="control-label">Verificação por E-mail <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
        <select class="form-control" name="mail_verify">
            <option value="2" <?php if($settings["mail_verify"] == 2){ echo "selected"; } ?>>Ativar</option>
            <option value="1" <?php if($settings["mail_verify"] == 1){ echo "selected"; } ?>>Desativar</option>
        </select>
    </div>
</div>
<!--<hr>-->

<!-- Admin 2fa -->
<!--<div class="alert" style="background-color:rgba(252, 98, 56);">-->
<!--    <div class="form-group">-->
<!--        <label class="control-label">Admin 2FA <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>-->
<!--        <select class="form-control" name="otp">-->
<!--            <option value="2" <?= $settings["otp"] == 2 ? "selected" : null; ?>>Ativar</option>-->
<!--            <option value="1" <?= $settings["otp"] == 1 ? "selected" : null; ?>>Desativar</option>-->
<!--        </select>-->
<!--    </div>-->
<!--</div>-->
<hr>
 
        <!-- start -->
       <!-- Configurações de Login com o Google -->
<div class="alert">
    <div class="row">
        <div class="form-group col-md-3">
            <label class="control-label">Login com o Google <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
            <select class="form-control" name="google">
                <option value="2" <?php if($settings["google"] == 2){ echo "selected"; } ?>>Ativar</option>
                <option value="1" <?php if($settings["google"] == 1){ echo "selected"; } ?>>Desativar</option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label class="control-label">RedirectUri</label>
            <input type="text" class="form-control" value="<?php echo site_url("google")?>" disabled>
        </div>
        <div class="form-group col-md-3">
            <label class="control-label">Chave secreta do clientee</label>
            <input type="text" class="form-control" name="gkey" value="<?=$settings["gkey"]?>">
        </div>
        <div class="form-group col-md-3">
            <label class="control-label">ID do cliente</label>
            <input type="text" class="form-control" name="gsecret" value="<?=$settings["gsecret"]?>" >
        </div>
    </div>
</div>
<hr>

<!-- Configurações de Música, Código do Cabeçalho, Código do Rodapé e Banner -->
<div class="form-group">
    <label class="control-label">URL da Música <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <textarea class="form-control" rows="2" name="music_url"><?=$settings["music_url"]?></textarea>
</div>

<div class="form-group">
    <label class="control-label">Campo de Código do Cabeçalho (Visível em Todas as Páginas) <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <textarea class="form-control" rows="7" name="custom_header" placeholder='<style type="text/css">...</style>'><?=$settings["custom_header"]?></textarea>
</div>

<div class="form-group">
    <label>Campo de Código do Rodapé (Visível em Todas as Páginas) <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"></span></label>
    <textarea class="form-control" rows="7" name="custom_footer" placeholder='<script>...</script>'><?=$settings["custom_footer"]?></textarea>
</div>
<hr>

<!-- Configurações de Confirmação do Banner e Texto do Banner -->
<div class="form-group field-editgeneralform-skype_field required">
    <label class="control-label" for="editgeneralform-skype_field">Confirmação do Banner <div class="tooltip5">  </div> </label>
    <select class="form-control" name="panner_confirmation">
        <option value="1" <?= $settings["panner_confirmation"] == 1 ? "selected" : null; ?> >Ativado</option>
        <option value="2" <?= $settings["panner_confirmation"] == 2 ? "selected" : null; ?>>Desativado</option>
    </select>
</div>

<div class="form-group">
    <label class="control-label">Texto do Banner (Ar)</label>
    <input type="text" class="form-control" dir="rtl" name="banner_text_ar" value="<?=$settings["banner_text_ar"]?>">
</div>

<div class="form-group">
    <label class="control-label">Texto do Banner (En)</label>
    <input type="text" class="form-control" name="banner_text_en" value="<?=$settings["banner_text_en"]?>">
</div>

<div class="form-group">
    <label class="control-label">URL do Banner</label>
    <input type="text" class="form-control" name="banner_url" value="<?=$settings["banner_url"]?>">
</div>
<hr>

<!-- Botão de Atualização e Modal de Confirmação -->
<button type="submit" class="btn btn-primary">Atualizar</button>
</form>
</div>
</div>
</div>

<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
<div class="modal-dialog modal-dialog-center" role="document">
<div class="modal-content">
<div class="modal-body text-center">
<h4>Você confirma as atualizações?</h4>
<div align="center">
<a class="btn btn-primary" href="" id="confirmYes">Sim</a>
<button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
</div>
</div>
</div>
</div>
</div>

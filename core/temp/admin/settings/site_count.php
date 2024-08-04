<div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-body">
            <h1>Pedidos Falsos</h1>

            <div class="form-group">
                <center><div class="alert alert-info">Pular ID do Pedido [A cada 5 minutos]</div></center>
            </div>
            <form method="post" action="/admin/settings/site_counts">
                <div class="form-group">
                    <label class="control-label">Modo LIGAR/DESLIGAR</label>
                    <select class="form-control" name="fake_order_service_enabled"> 
                        <option value="1" <?= $settings["fake_order_service_enabled"] == 1 ? "selected" : null; ?>>Desligar</option>
                        <option value="2" <?= $settings["fake_order_service_enabled"] == 2 ? "selected" : null; ?> >Ligar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Mínimo</label>
                    <input class="form-control" type="number" name="min" value="<?php if(is_numeric($settings["min"])){ echo $settings["min"]; } ?>">
                </div>

                <div class="form-group">
                    <label class="control-label">Máximo</label>
                    <input class="form-control" type="number" name="fake_order_max" value="<?php if(is_numeric($settings["fake_order_max"])){ echo $settings["fake_order_max"]; } ?>">
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

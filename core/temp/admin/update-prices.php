<?php include 'header.php'; ?>

<?php if( $success ): ?>
    <div class="alert alert-success "><?php echo $successText; ?></div>
<?php endif; ?>
<?php if( $error ): ?>
    <div class="alert alert-danger "><?php echo $errorText; ?></div>
<?php endif; ?>

<div class="container container-md">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <h3><center>Aumentar Lucro</center></h3>
                        <div class="form-group">
                            <label for="" class="control-label">Para Serviços</label>
                            <select class="form-control" name="type">
                                <option value="1" >Todos os Serviços</option>
                                <option value="2" >Serviços Manuais</option>
                                <option value="3" >Serviços de API</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Lucro (%)</label>
                            <input type="text" class="form-control" name="new_profit" >
                        </div>
                        <center><button type="submit" class="btn btn-primary">Aumentar Lucro</button></center>
                    </form>
                    <hr>
                    <form action="admin/update-prices/decrease" method="post" enctype="multipart/form-data">
                        <h3><center>Diminuir Lucro</center></h3>
                        <div class="form-group">
                            <label for="" class="control-label">Para Serviços</label>
                            <select class="form-control" name="type">
                                <option value="1" >Todos os Serviços</option>
                                <option value="2" >Serviços Manuais</option>
                                <option value="3" >Serviços de API</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Lucro (%)</label>
                            <input type="text" class="form-control" name="new_profit" >
                        </div>
                        <hr>
                        <center><button type="submit" class="btn btn-primary">Diminuir Lucro</button></center>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

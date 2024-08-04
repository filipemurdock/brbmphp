<div class="col-md-8">
    <?php if($success): ?>
        <div class="alert alert-success"><?=$successText;?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?=$errorText;?></div>
    <?php endif; ?>

    <div class="settings-header__table">
        
        <button id="rateUpdateBtn" type="button" class="btn btn-default m-b">Botão de Sincronização</button>
    </div>
    
    <script>
        document.write('<center id="loading"><img src="/img/ajax-loader-2.gif"></center>');
        window.onload = function() {
            document.getElementById("loading").style.display = "none";
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rateUpdateBtn').click(function() {
                Swal.fire({
                    title: 'Aguarde (Aproximadamente 1 minuto)',
                    text: 'Carregando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    onBeforeOpen: function() {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '/admin/cron-sync',
                    type: 'POST',
                    success: function(response) {
                        if (response == 'Sincronizado com Sucesso.') {
                            Swal.fire({
                                title: 'Sucesso',
                                text: 'Sincronização realizada com sucesso.',
                                icon: 'success'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000); // ajuste o atraso conforme necessário
                        } else {
                            Swal.fire({
                                title: 'Falha',
                                text: 'Falha ao sincronizar.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Falha',
                            text: 'Falha ao sincronizar.',
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        setTimeout(function() {
                            Swal.close();
                        }, 3000);
                    }
                });
            });
        });
    </script>

    <div class="providers"></div>
</div>

<?php include 'header.php'; ?>

<style>
    .input {
        width: 5%;
    }

    .input2 {
        width: 20%;
    }

    .input3,
    .input4,
    .input5,
    .input6 {
        width: 15%;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 10px;
        margin: 0 5px;
        text-decoration: none;
        color: #000;
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .pagination a.active {
        background-color: #007bff;
        color: #fff;
    }
</style>

<center><h2> Editor de serviço em massa </h2></center>
<br>

<div class="container-fluid">
    <div class="table-responsive">
        <table class="table" id="dt">
            <thead>
                <th class="input">ID</th>
                <th class="input2">Nome</th>
                <th class="input3">Min</th>
                <th class="input4">Max</th>
                <th class="input5">Preço</th>
                <th class="input6">Descrição</th>
            </thead>
            <tbody>
                <form action="" method="post" enctype="multipart/form-data">
                    <?php
                    $itemsPerPage = 50; // Display 100 items per page
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $itemsPerPage;

                    $servicesToShow = array_slice($services, $offset, $itemsPerPage);

                    foreach ($servicesToShow as $service) :
                    ?>
                        <tr>
                          <td class="input">
											<input type="text" class="form-control" name="service[<?php echo $service["service_id"]; ?>]"  value="<?php echo $service["service_id"]; ?>" readonly>
			  </td>
			 
                                            <td class="input2">
<input type="text" class="form-control" name="name_and_lang-<?= $service['service_id'] ?>" value="<?= htmlspecialchars($service['service_name']) ?>" />
</td>
                                            

                                             
                                            <td class="input3">
                                               <div><input type="text" class="form-control" name="min-<?php echo $service["service_id"]; ?>" value="<?php echo $service["service_min"]; ?>"></div>
                                              
                                            </td>
                                            <td class="input4 ">
                                               <div><input type="text" class="form-control" name="max-<?php echo $service["service_id"]; ?>" value="<?php echo $service["service_max"]; ?>"></div>
                                            </td>
   <td class="input5">
                                               <div><input type="text" class="form-control" name="price-<?php echo $service["service_id"]; ?>" value="<?php echo $service["service_price"]; ?>"></div>
                                            </td>
   <td class="input6">
                                               <div>
												  <textarea class="form-control" name="desc-<?php echo $service["service_id"]; ?>" rows="4" cols="50" ><?php echo $service["service_description"]; ?></textarea> 
												   <td><center><button type="submit" class="btn btn-primary" >Salvar alterações </button></center></td>
                                </div>
                            
                        </tr>
                    <?php endforeach; ?>
                </form>
            </tbody>
        </table>

        <div class="pagination">
            <?php
            $totalPages = ceil(count($services) / $itemsPerPage);

            // Show 5 page numbers at a time
            $startPage = max(1, $currentPage - 2);
            $endPage = min($startPage + 4, $totalPages);

            for ($i = $startPage; $i <= $endPage; $i++) {
                echo "<a href='admin/bulk/?page=$i'" . ($i == $currentPage ? " class='active'" : "") . ">$i</a>";
            }
            ?>
        </div>
    </div>
</div>

<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog modal-dialog-center" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h4>Tem certeza de que deseja atualizar o status? </h4>
                <div align="center">
                    <a class="btn btn-primary" href="" id="confirmYes">Sim</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>
<?php include 'footer.php'; ?>

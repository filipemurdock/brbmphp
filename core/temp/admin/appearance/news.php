<div class="col-md-8">
    
<ul class="nav nav-tabs">
    <li class="p-b">
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalDiv" data-action="new_news">Adicionar novo anúncio</button>        
 </li>
  </ul>
  
    <div class="card shadow mb-4">
     <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Anucios do site</h6>
      </div>
      <div class="table-responsive">
    <table class="table" style="border:1px solid #ddd">
      <thead>
      <tr>
            <th><div style="float:left;">Ícone de anúncio</div></th>
            <th>Título do anúncio</th>
            <th>Data do anúncio</th>
            <th></th>
         </tr>
      </thead>
      <tbody>
         <?php foreach($newsList as $new): ?>
         <tr>
<td><div style="float:left;"><img src='img/icons/<?=$new["news_icon"]?>.png' widht="32" height="32"></div></td>
     <td><?=$new["news_title"]?></td>
  
  <td><?=$new["news_date"]?></td>
  
            <td class="text-right col-md-1">
              <div class="dropdown pull-right">
             
<button type="button" class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#modalDiv" data-action="edit_news" data-id="<?=$new['id']?>">Editar</button>         

</div>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</div>
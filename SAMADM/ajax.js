$(document).ready(function(){

  /* Katogeriye ait servisleri çek */
  category_detail();
  $("#neworder_category").change(function(){
    category_detail();
  });
  /* Katogeriye ait servisleri çek */
  /* Servise ait verileri çek */
  $("#neworder_services").change(function(){
    service_detail();
  });
  /* Servise ait verileri çek */
  /* Sipariş miktarı değişince fiyat hesapla */
  $(document).on('keyup', '#order_quantity', function() {
    var service   = $("#neworder_services").val();
    var quantity  = $("#neworder_quantity").val();
    var runs      = $("#dripfeed-runs").val();
      if( $("#dripfeedcheckbox").prop('checked') ){
        var dripfeed  = "var";
      }else{
        var dripfeed  = "bos";
      }
    $.post('ajax_data',{action:'service_price',service:service,quantity:quantity,dripfeed:dripfeed,runs:runs}, function(data){
        $("#charge").val(data.price);
        $("#dripfeed-totalquantity").val(data.totalQuantity);
    }, 'json');
  });
  $(document).on('keyup', '#dripfeed-runs', function() {
    var service   = $("#neworder_services").val();
    var quantity  = $("#neworder_quantity").val();
    var runs      = $("#dripfeed-runs").val();
      if( $("#dripfeedcheckbox").prop('checked') ){
        var dripfeed  = "var";
      }else{
        var dripfeed  = "bos";
      }
    $.post('ajax_data',{action:'service_price',service:service,quantity:quantity,dripfeed:dripfeed,runs:runs}, function(data){
        $("#charge").val(data.price);
        $("#dripfeed-totalquantity").val(data.totalQuantity);
    }, 'json');
  });
  $(document).on('keyup', '#neworder_comment', function() {
    comment_charge();
  });
  /* Sipariş miktarı değişince fiyat hesapla */
  /* Dripfeed değiştir */
  $(document).on('change', '#dripfeedcheckbox', function() {
    var dripfeed = $(this).prop('checked');
    if( dripfeed ){
      $("#dripfeed-options").removeClass();
      dripfeed_charge();
    }else{
      $("#dripfeed-options").addClass('hidden');
      dripfeed_charge();
    }
  });
  /* Dripfeed değiştir */
});


 

jQuery(document).on("click", '[role="spin_ajax"]', function() {
  var e = window.location.origin;
  id   = $(this).attr('role-id');
  $('#returndiv').html('<div class="alert alert-info">Çark Çevriliyor Lütfen Bekleyin..</div>');
 


  $.ajax({
    type: "POST",
    url:  "ajax_data",
    dataType: "json",
    data:"command=spin_ajax&id="+id,
    success: function(nuvem) { 
      if (nuvem.status=='success') {

        $('#returndiv').html('<div class="alert alert-success">'+nuvem.message+'</div>');
      }else if (nuvem.status=='error') {
        $('#inner-wheel').attr('style','transform: rotate(-27deg);');
        $('#returndiv').html('<div class="alert alert-danger">'+nuvem.message+'</div>');
      } 
    }
  })
});

function category_detail(){
  var category_now = $("#neworder_category").val();
  $.post('ajax_data',{action:'services_list',category:category_now}, function(data){
      $("#neworder_services").html(data.services);
      service_detail();
  }, 'json');
}

function service_detail(){
  var service_now = $("#neworder_services").val();
  $.post('ajax_data',{action:'service_detail',service:service_now}, function(data){
      if( data.empty == 1 ){
        $("#charge_div").hide();
      }else{
        $("#charge_div").show();
        $("#neworder_fields").html(data.details);
        $("#charge").val(data.price);
      }
      $(".datetime").datepicker({
         format: "dd/mm/yyyy",
         language: "tr",
         startDate: new Date(),
       }).on('change', function(ev){
         $(".datetime").datepicker('hide');
       });
      $("#clearExpiry").click(function(){
           $("#expiryDate").val('');
       });
       var dripfeed = $("#dripfeedcheckbox").prop('checked');
       if( dripfeed ){
         $("#dripfeed-options").removeClass();
       }
       comment_charge();
        if( $("#dripfeedcheckbox").prop('checked') ){
          dripfeed_charge();
        }
          if( data.sub ){
            $("#charge_div").hide();
          }else{
            $("#charge_div").show();
          }
  }, 'json');
}

function comment_charge(){
  var service   = $("#neworder_services").val();
  var comments  = $("#neworder_comment").val();
    if( comments ){
      $.post('ajax_data',{action:'service_price',service:service,comments:comments}, function(data){
          $("#neworder_quantity").val(data.commentsCount);
          $("#charge").val(data.price);
      }, 'json');
    }
}

function dripfeed_charge(){
  var service     = $("#neworder_services").val();
  var quantity    = $("#neworder_quantity").val();
  var runs        = $("#dripfeed-runs").val();
    if( $("#dripfeedcheckbox").prop('checked') ){
      var dripfeed  = "var";
    }else{
      var dripfeed  = "bos";
    }
  $.post('ajax_data',{action:'service_detail',service:service,quantity:quantity,dripfeed:dripfeed,runs:runs}, function(data){
      $("#charge").val(data.price);
  }, 'json');
}

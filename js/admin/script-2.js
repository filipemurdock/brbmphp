$(document).ready(function(){
    

  var site_url  = $('head base').attr('href');
  
  
    $("#minPriceCheckbox").click(function(){
        alert("danger");
    });

  $(document).on('submit', 'form[data-xhr]', function(event){
      event.preventDefault();
      var action    = $(this).attr('action');
      var method    = $(this).attr('method');
      var formData  = new FormData($(this)[0]);
        $.ajax({
          url:  action,
          type: method,
          dataType: 'json',
          data: formData,
          cache: false,
          contentType: false,
          processData: false
        })
        .done(function(result){
              /* İşlem başarılı, dönen sonucu ekrana bastır */
                if( result.s == "error" ){
                  var heading = "Malsucedido";
                }else{
                  var heading = "Bem-sucedido";
                }
          $.toast({
              heading: heading,
              text: result.m,
              icon: result.s,
              loader: true,
              loaderBg: "#0a007b9b"
          });
          if (result.r!=null) {
            if( result.time ==null ){ result.time = 3; }
               /* Yönlendirilecek adres boş değil ise yönlendir */
            setTimeout(function(){
              window.location.href  = result.r;
            },result.time*1000);
          }

        })
        .fail(function(){
             /* Ajax işlemi başarısız, hata bas */
             $.toast({
                 heading: 'Error!',
                 text: 'Falha na solicitação',
                 icon: 'error',
                 loader: true,
                 loaderBg: "#0a007b9b"
             });
        })
  });


    
  $("#delete-row").click(function(){
    var action = $(this).attr("data-url");
    swal({
      title: "Tem certeza de que deseja excluí-lo?",
      text: "Se você confirmar que este conteúdo será excluído, talvez não seja possível recuperá-lo.",
      icon: "warning",
      buttons: true,
      dangerMode: true,
      buttons: ["Close", "Yes, I am sure!"],
    })
    .then((willDelete) => {
      if (willDelete) {
        $.ajax({
          url:  action,
          type: "GET",
          dataType: "json",
          cache: false,
          contentType: false,
          processData: false
        })
        .done(function(result){
          if( result.s == "error" ){
            var heading = "Malsucedido";
          }else{
            var heading = "Bem-sucedido";
          }
            $.toast({
                heading: heading,
                text: result.m,
                icon: result.s,
                loader: true,
                loaderBg: "#0a007b9b"
            });
            if (result.r!=null) {
              if( result.time ==null ){ result.time = 3; }
              setTimeout(function(){
                window.location.href  = result.r;
              },result.time*1000);
            }
        })
        .fail(function(){
          $.toast({
              heading: "Malsucedido",
              text: "Falha na solicitação",
              icon: "error",
              loader: true,
              loaderBg: "#0a007b9b"
          });
        });
        /* İçerik silinmesi onaylandı */
      } else {
        $.toast({
            heading: "Malsucedido",
            text: "Solicitação de exclusão negada",
            icon: "error",
            loader: true,
            loaderBg: "#0a007b9b"
        });
      }
    });
  });

});
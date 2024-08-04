$(document).ready(function () {
      $("#search").keyup(function () {
        var searchTerm = $("#search").val();
        var listItem = $('.results tbody').children('tr');
        var searchSplit = searchTerm.replace(/ /g, "'):containsi('")
        $.extend($.expr[':'], { 'containsi': function (elem, i, match, array) {
                return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
            }
        });

        $("tbody tr").not(":containsi('" + searchSplit + "')").each(function (e) {
            $(this).attr('visible', 'false');
        });

        $("tbody tr:containsi('" + searchSplit + "')").each(function (e) {
            $(this).attr('visible', 'true');
        });



    });

  
    $('[data-toggle="minimize"]').click(function(){
      $("body").toggleClass("sidebar-icon-only");
    });
   
    $('[data-toggle="offcanvas"]').click(function(){
        $(".sidebar").toggleClass("active");
    });
  
  
    $('.banner-slider').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        dots:true,
        rtl:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    })
    
    $('.counter').counterUp({
         time: 1300
     });
  
});


$('#panel-description').empty();
$.ajax({
    url: '#https://jsonstorage.net/api/items/fbd72b3d-82a8-4474-963e-840120e3b998',
    contentType: "application/json; charset=utf-8",
    //data: JSON.stringify(product),
    dataType: 'json',
    success: function (result) {
	data = eval(result);
	data = data.d;
	var InnerHtml = '';
        for (i = 0; i < data.length; i++) {
            InnerHtml += "<div class=\"site-description\">";
			InnerHtml += "<h1>" + data[i].Title + "</h1>";
			InnerHtml += "<p>" + data[i].ContentData + "</p>";
			InnerHtml += "</div>";
          }
            $('#panel-description').append(InnerHtml);

    }
});
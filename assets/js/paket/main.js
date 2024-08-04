if(document.getElementById('paket_kategori')){
    $('#paket_kategori').change(function(){
        $.ajax({
            url: window.base_url+"paket_ajax",
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: ['search_paket':$(this).val()],
            type: 'POST',
            success: function (data) {
                if (data['status'] == 'failed') {
                    swal(data['message'], data['sub_message'], 'error');
                } else if (data['status'] == 'Complete') {
                    var field = data['field'];
                    var i = 0;
                    var select = document.getElementById('paket_kategori_sec');
                    select.removeChild();
                    for (let i = 0; i < data['field_count']; i++) {
                        var option = document.createElement('option');
                        option.value = data['field'][i]['id'];
                        option.innerText = data['field'][i]['name'];
                        select.appendChild(option);
                    }
                }
            }
        });


    });
}
if(document.getElementById('search_kategori')){
    $('#search_kategori ').change(function(){
        $.ajax({
            url: window.base_url+"paket_ajax",
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: ['search':$(this).val()],
            type: 'POST',
            success: function (data) {
                if (data['status'] == 'failed') {
                    swal(data['message'], data['sub_message'], 'error');
                } else if (data['status'] == 'Complete') {
                    var field = data['field'];
                    var i = 0;
                    var select = document.getElementById('paket_servis_sec');
                    select.removeChild();
                    for (let i = 0; i < data['field_count']; i++) {
                        var option = document.createElement('option');
                        option.value = data['field'][i]['id'];
                        option.innerText = data['field'][i]['name'];
                        select.appendChild(option);
                    }
                }
            }
        });


    });
}
if(document.getElementById('favori')){
    function favori(thiss){
        $.ajax({
            url: window.base_url+"paket_ajax",
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: ['favori':$(thiss).data('id')],
            type: 'POST',
            success: function (data) {
                if (data['status'] == 'failed') {
                    swal(data['message'], data['sub_message'], 'error');
                } else if (data['status'] == 'Complete') {
                    swal(data['message'], data['sub_message'], 'success');
                    }
                }
            }
        });


    };
}

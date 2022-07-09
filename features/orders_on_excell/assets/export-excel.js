
jQuery(function ($){


    console.log(za_export_excel_object)
    // export as excell orders
    $(document).on("click", ".export-orders-as-excell", function (e) {
        $.ajax({
            url: za_export_excel_object.ajax_url,
            method: 'POST',
            type:'json' ,
            data: {
                'action' : 're_get_orders_as_excel' ,
                'nonce'  : za_export_excel_object.nonce
            },
            success:function (e){
                console.log( e );
                $.ajax({
                    url: e.file_name,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (data) {
                        var a = document.createElement('a');
                        var url = window.URL.createObjectURL(data);
                        a.href = url;
                        a.download = 'myfile.pdf';
                        document.body.append(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                    }
                });
            }
        });
    });
    // export as excell orders





})



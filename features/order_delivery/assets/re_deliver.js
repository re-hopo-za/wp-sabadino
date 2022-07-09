




jQuery(function ($) {

    $(document).on('click','.change_p_status_to_packing' , function () {
       let p_id = $(this).data('product');
       $('.loader').show();
        $.ajax({
            url: re_deliver_object.admin_url ,
            type: "post",
            data: {
                'action' : 're_change_status_to_packing' ,
                'p_id'   : p_id
            } ,
            success:function (data) {
                $('.order_item_con').html(data);
                $('.loader').hide();
            }
        });
    });


    $(document).on('click','.change_p_status_to_processing' , function () {
        let p_id = $(this).data('product');
        $('.loader').show();
        $.ajax({
            url: re_deliver_object.admin_url ,
            type: "post",
            data: {
                'action' : 're_change_p_status_to_processing' ,
                'p_id'   : p_id
            } ,
            success:function (data) {
                $('.order_item_con').html(data);
                $('.loader').hide();

            }
        });
    });


    $(document).on('click','.change_p_status_to_delivering' , function () {
        let p_id = $(this).data('product');
        $('.loader').show();
        $.ajax({
            url: re_deliver_object.admin_url ,
            type: "post",
            data: {
                'action' : 're_change_p_status_to_delivering',
                'p_id'   : p_id
            } ,
            success:function (data) {
                $('.order_item_con').html(data);
                $('.loader').hide();
            }
        });
    });



    //tabs
    $(document).on('click','.tabs button' , function () {

        let whichTab = $(this).data('tabs');

        $('.loader').show();

        $(".tabs>button").removeClass('active');
        $(this).addClass('active');

        let id = parseInt( $(this).attr('id') );

        if (id == 1){

            $.ajax({
                url: re_deliver_object.admin_url ,
                type: "post",
                data: {
                    'action' : 're_get_products_status'
                } ,
                success:function (data) {
                    $('.tabs-con>div').hide();
                    $('.order_item_con').html(data);
                    $(whichTab).show();
                    $('.loader').hide();
                }
            });
        }else {

            $('.tabs-con>div').hide();
            $(whichTab).show();
            $('.loader').hide();
        }

    });









//// stock

        $(document).on('click' ,'.btn-update', function () {
            let p_id = $(this).data('pid');
            let count = $(".p-stock-"+p_id).val();
            if ( $.isNumeric(count)   ){
                $('.loader').show();
                $('.p-count').val('a');
                $.ajax({
                    url: re_deliver_object.admin_url  ,
                    type: "post",
                    data: {
                        'action'  : 're_update_product_inventory' ,
                        'p_id'    : p_id ,
                        'count'   : count
                    } ,
                    success:function (data) {
                        $('.main-stock-con table').html(data);
                        $('.loader').hide();
                    }
                });
            }
        });





//////delivery action
    $(document).on('click' ,'.change-status-delivery', function () {
        let p_id   = $(this).children('a').data('product');
        let status = $(this).prev().find('select').val();

        if ( parseInt(status) > 0 && parseInt(status) !== 2  ){
            if (confirm('مطمئین هستید میخواهید تغییر وضعیت دهید')  ){

                $('.loader').show();
                $.ajax({
                    url: re_deliver_object.admin_url  ,
                    type: "post",
                    data: {
                        'action'   : 're_delivery_status' ,
                        'p_id'     : p_id ,
                        'status'   : status
                    } ,
                    success:function (data) {
                        $('.delivery_list_con').text('').html(data);
                        $('.loader').hide();
                    }
                });

            }
        }





        if ( parseInt(status) === 2 ){
                $(this).siblings('.deliver_time').show();
        }

    });

    $(document).on('click' ,'.save_time_deliver_again', function () {
        let p_id = $(this).data('product');
        $(this).parent().hide();
        $('.loader').show();

        let time = $(this).siblings('section').find('input[name="date_input"]:checked').val() ;
        let date = $(this).siblings('section').find('input[name="date_input"]:checked').data('date') ;
        let deliver_again =  time+'|'+date;

        $.ajax({
            url: re_deliver_object.admin_url  ,
            type: "post",
            data: {
                'action'   : 're_delivery_again' ,
                'p_id'     : p_id ,
                'date'     : deliver_again
            } ,
            success:function (data) {
                $('.delivery_list_con').text('').html(data);
                $('.loader').hide();
            }
        });
    });


$(document).on('click' ,'.close' , function () {
    $(this).parent().hide();
})

})
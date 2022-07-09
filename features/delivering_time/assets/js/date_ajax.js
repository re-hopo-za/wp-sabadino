




    jQuery(document).ready(function($) {



        $(document).on('click' , '.date-list .order-item' ,
            function () {


            $('html, body').animate({ scrollTop: 1200 }, 1000);

            let isWholeDays   = parseInt($(this).data('wholedays'));
            let just_delivery = $('input[name=just_delivery]:checked', '#status').val();


            let date = $(this).data('date');
                console.log(date);
            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: { 'action' : "re_list" , 'date':date  ,'isWholeDays':isWholeDays ,'just_delivery':just_delivery} ,
                success: function(data) {
                    $('#orders-list * ').remove();
                    if (data==0){
                        $('#orders-list').append('<p class="nothing"> سفارشی موجود نیست'+'</p>');
                    }else{
                        $('#orders-list').append(data);
                    }

                },
                error: function(MLHttpRequest, textStatus, errorThrown){

                }
            });


        });





        $('input[type=range]').on('input', function () {
            $(this).trigger('change');
            $('#datePrint').html( $(this).val() );
        });




        $(document).on('change' , '.re_days_status' , function () {
             if(parseInt( $(this).val() ) === 0){
                $(this).parent().parent().addClass('deactivate-hours');
                let deactivate_time = $(this).attr('id');
                deactivate_time = '.'+deactivate_time;
                $(deactivate_time).addClass('deactivate-hours');
            }else {
                 $(this).parent().parent().removeClass('deactivate-hours');
                 let deactivate_time = $(this).attr('id');
                 deactivate_time = '.'+deactivate_time;
                 $(deactivate_time).removeClass('deactivate-hours');
            }
        })



    });
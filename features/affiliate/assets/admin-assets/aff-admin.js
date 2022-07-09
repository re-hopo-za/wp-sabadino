




jQuery( function ($) {



    let loader_aff =  '<svg class="loader_aff" xmlns="http://www.w3.org/2000/svg"\n' +
        '     style="margin: auto;   display: block; shape-rendering: auto;"\n' +
        '     width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
        '    <path d="M10 50A40 40 0 0 0 90 50A40 42 0 0 1 10 50" fill="#ff3700" stroke="none">\n' +
        '        <animateTransform attributeName="transform" type="rotate" dur="0.14836795252225518s" repeatCount="indefinite"\n' +
        '                          keyTimes="0;1" values="0 50 51;360 50 51"></animateTransform>\n' +
        '    </path>\n' +
        '</svg>';



    $('.aff-menu-items>div:first-of-type').addClass('aff-menu-active');
    $('.aff-menu-con').append(loader_aff);




    setTimeout(function () {
        $('.loader_aff').remove();

        let window_item_s = $(".re-input-con input[type='radio']:checked").val();
        window_item_s = '.re-options-con-'+window_item_s;
        $(document).find('.re-aff-op-con>div').removeClass('re-window-con-show');
        $(document).find(window_item_s).addClass('re-window-con-show');
    } , 2000);




    $(document).on('click' ,'.aff-menu-items>div', function () {
        let item = $(this).attr('class');
        $('.aff-menu-items>div').removeClass('aff-menu-active');
        $(this).addClass('aff-menu-active');

        item = '#'+item;
        if ( $(item).is(":hidden")  ){
            $('.aff-menu-con>div').hide();
            $(item).show();
        }

    });

    $(document).on('click' , '.re-payment-type input' , function () {
        let payment_type = parseInt($(this).val());
        if (payment_type === 1){
            $(document).find('.re-terms-amount .re-amount').text('$ تومان');
        }else{
            $(document).find('.re-terms-amount .re-amount').text('% درصدی');
        }
    });


    $(document).on('click' , '.re-input-con input' , function () {
        let window_item = parseInt($(this).val());
        window_item = '.re-options-con-'+window_item;

        $(document).find('.re-aff-op-con>div').removeClass('re-window-con-show');
        $(document).find(window_item).addClass('re-window-con-show');
    });


    $(document).on('click','.re-options-con-2 .add' , function () {
         let term_element = $('.re-set-new-term');
        if( term_element.is(":hidden")  ){
            term_element.show();
        }
    })

    $(document).on('click' , '.aff-close' , function () {
        $(this).parent().hide();
    })


    $(document).on('click' , '.re-term-con-variable li>span' , function () {
        if (confirm('مطمئینی؟؟؟' ))
            $(this).parent().remove();
    })





    $(".re-set-new-term input").on("click",function () {
        $(document).find(".re-set-new-term input").val('');
    });

    $(".re-set-new-term input  ").on("keypress keyup blur",function (event) {

        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        let holder_import = $('.re-set-new-term');
        holder_import.data('setnewterms' , $(this).val() );
        holder_import.data('whitchinput' , $(this).attr('id') );
    });


    $(".re_calculate_income_pro input  , .re-terms-count-con input, .re-terms-amount input  ").on("keypress keyup blur",function (event) {

        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });




    $(document).on('click' , '#re-save-new-term' , function () {
        let holder_extract = $('.re-set-new-term');
        let holder_data   = holder_extract.data('setnewterms');
        let holder_which  = holder_extract.data('whitchinput');
        let option = {"type" : null , "count":0 , "amount":0 }


        let term_list = $(document).find('.re-term-con-variable ul li:last-of-type');
        let count = parseInt(term_list.data('termnumber') );
            if ($('.re-term-con-variable ul li:last-of-type').length <= 0){
                count = 0;
            }

        if (holder_which === "re-set-input-1"){
            option.type   = "درصد";
            option.dtype  = "percent";
            option.amount = parseInt( holder_data);
            option.count  =   count+1
        }else {
            option.type   = "تومان";
            option.dtype  = "fixed";
            option.amount = parseInt(  holder_data );
            option.count  = count+1
        }

        let term_item =
            '<li data-termnumber="'+option.count+'" data-termtype="'+option.dtype+'"  data-termvalue="'+option.amount+'">\n' +
            '    <span>*</span>\n' +
            '    <p>بار<span>'+option.count+'</span> </p>\n' +
            '    <p>'+option.amount+'<span>'+option.type+'</span> </p>\n' +
            '</li>';


        if (option.amount > 0 ){
            $(document).find('.re-term-con-variable ul ').append(term_item);
        }

        $(this).parent().parent().hide();
    });






    $(document).on( 'click','#re_save_aff_setting' , function (e) {
        e.preventDefault();
        let window_item_s = parseInt($(".re-input-con input[type='radio']:checked").val() );



        let   setting ;
            if (window_item_s === 0 ){
                    setting = {  status : 'o'} ;
            }else if (window_item_s === 1 ){
                let term_type_be = parseInt($(".re-payment-type input[type='radio']:checked").val())  === 0 ?'p' : 'f';

                    setting = {
                    status     : 'a' ,
                    term_count :  $('#re-terms-count').val() ,
                    term_type  :  term_type_be ,
                    term_value :  $('#re-terms-percent').val()   ,
                } ;


            }else {
                let terms_fixed =  {} ;

                $(".re-term-con-variable ul li").each(function(i) {
                    terms_fixed['item' + i] = {
                        termnumber : $(this).data('termnumber')  ,
                        termtype   : $(this).data('termtype')  ,
                        termvalue  : $(this).data('termvalue')
                    };
                });

                    setting = {
                    status  : 'f',
                    data    : terms_fixed
                }

            }


        $.ajax({
            url: re_aff_data.aff_admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_aff_set_calculate' ,
                'setting' : setting
            } ,
            success: function (data) {

            }
        });


    });



    ////// Save Settings

    $(document).on( 'click','.re_save_settings' , function (e) {
        e.preventDefault();



        let re_exclude_coupon          =  $(".re_calculate_income_item #re_exclude_coupon").is(":checked");
        let re_cal_income_save_order   =  parseInt($(".re_calculate_income_item_last input[type='radio']:checked").val()) ;
        let re_minimal_cart            =  $(".re_calculate_income_pro #re_minimal_cart").val();
        let re_maximal_cart            =  $(".re_calculate_income_pro #re_maximal_cart").val();
        let re_exclude_user_change_aff =  $(".re_exclude_user_change_aff #re_exclude_user_change_aff").is(":checked");

        let aff_setting = {
            re_exclude_coupon          : re_exclude_coupon ,
            re_cal_income_save_order   : re_cal_income_save_order,
            re_minimal_cart            : re_minimal_cart  ,
            re_maximal_cart            : re_maximal_cart  ,
            re_exclude_user_change_aff : re_exclude_user_change_aff
        };


        $.ajax({
            url: re_aff_data.aff_admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_aff_set_setting' ,
                'setting' : aff_setting
            } ,
            success: function (data) {
            }
        });


    });

    $(document).on( 'click','.re_save_visual_settings' , function (e) {
        e.preventDefault();



        let re_visual_users_count         =  $(".re_visual_users_count input").is(":checked");
        let re_visual_purchase_count      =  $(".re_visual_purchase_count input").is(":checked");
        let re_visual_chart               =  $(".re_visual_chart input").is(":checked");
        let re_visual_users_profile       =  $(".re_visual_users_profile input").is(":checked");

        let visual_aff_setting = {
            re_visual_users_count             : re_visual_users_count ,
            re_visual_purchase_count          : re_visual_purchase_count,
            re_visual_chart                   : re_visual_chart  ,
            re_visual_users_profile           : re_visual_users_profile
        };

        $.ajax({
            url: re_aff_data.aff_admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_visual_aff_set_setting' ,
                'setting' : visual_aff_setting
            } ,
            success: function (data) {
            }
        });


    });




})
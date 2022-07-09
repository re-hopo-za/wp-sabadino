


jQuery(function ($){

    $(document).on('click' , '.re_sidebar_con li' , function (){
        $('.re_sidebar_con li').removeClass('re_mobile_menus_active');
        $(this).addClass('re_mobile_menus_active');

        let mobile_item = parseInt( $(this).attr('id') );
        mobile_item = mobile_item * 70 ;  
        $(document).find('.re_body>div>div>').attr('style', 'transform: translateX( '+mobile_item+'vw)!important;transition: transform 0.5s ease-in-out!important;');

    });



    $(document).on('click' , '.re_add_slider' , function (){
        if ($('.re_inputs_slider_con').is(":hidden")  ){
            $('.re_inputs_slider_con').show();
        }
    })
    $(document).on('click' , '.close-section' , function (){
        $(this).parent().remove();
    })



    $(document).on('click' , '.re_slider_remove_con button' , function (){
        if (confirm('&#1581;&#1584;&#1601; &#1588;&#1608;&#1583; &#1567;&#1567;&#1567;'))
            $(this).parent().parent().remove();
        let sliders ={};
        $(".re_items_0 ul li").each(function(i) {
            sliders[i] = {
                slider: $(this).data('href')
            };
        });

        $.ajax({
            url: re_back_app_objects.admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_and_app_slider' ,
                'sliders' :  sliders
            } ,
            success: function (data) {
                console.log(data)
            }
        });

    })


    $(document).on('click' , '.re_banner_remove_con button' , function (){
        if (confirm('&#1581;&#1584;&#1601; &#1588;&#1608;&#1583; &#1567;&#1567;&#1567;'))
            $(this).parent().parent().remove();
            let banners = {};
        $(".re_items_1 ul li").each(function(i) {
            banners[i] = {
                banner: $(this).data('href')
            };
        });

        $.ajax({
            url: re_back_app_objects.admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_and_app_banner' ,
                'banners' :  banners
            } ,
            success: function (data) {
            }
        });

    })



    $(document).on('click' , '.add_slider_con button' , function (){
        let sliders ={};
        let img_url = $('.re_inputs_slider_con input').val();
        if ( img_url.length ){
            if (confirm('&#1575;&#1590;&#1575;&#1601;&#1607; &#1588;&#1608;&#1583; &#1567;&#1567;')) {

                let img_element = '<li data-href="'+img_url+'">\n' +
                    '<div class="re_slider_img_con">\n' +
                    '<img src="'+img_url+'" alt="">\n' +
                    '</div>\n' +
                    '<div class="re_slider_remove_con">\n' +
                    '<button> &#1581;&#1584;&#1601; </button>\n' +
                    '</div>\n' +
                    '</li>' ;

                $('.re_items_0 ul').append(img_element);
                $('.re_inputs_slider_con input').val('');

                $(".re_items_0 ul li").each(function(i) {
                    sliders[i] = {
                        slider : $(this).data('href')
                    };
                });
                $.ajax({
                    url: re_back_app_objects.admin_url,
                    dataType: "json",
                    method: 'POST',
                    data: {
                        'action'  : 're_and_app_slider' ,
                        'sliders' :  sliders
                    } ,
                    success: function (data) {
                    }
                });


            }
        }
    })



    // on upload button click
    $('body').on( 'click', '.misha-upl', function(e){

        e.preventDefault();

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                    type : 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false
            }).on('select', function() { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(".re_inputs_slider_con input").val(attachment.url).next().val(attachment.id).next().show();
            }).open();
    });



    // on upload button click Banner
    $('body').on( 'click', '.misha-upl-banner', function(e){

        e.preventDefault();

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                    type : 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false
            }).on('select', function() { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(".re_inputs_banner_con input").val(attachment.url).next().val(attachment.id).next().show();
            }).open();
    });



    $(document).on('click' , '.add_banner_con button' , function (){

        let img_url = $('.re_inputs_banner_con input').val();
        let banners = {};
        if ( img_url.length ){
            if (confirm('مطمئنی')) {
                let img_element = '<li data-href="'+img_url+'">\n' +
                    '<div class="re_banner_img_con">\n' +
                    '<img src="'+img_url+'" alt="">\n' +
                    '</div>\n' +
                    '<div class="re_banner_remove_con">\n' +
                    '<button> &#1581;&#1584;&#1601; </button>\n' +
                    '</div>\n' +
                    '</li>' ;


                $('.re_items_1 ul').append(img_element);
                $('.re_inputs_banner_con input').val('');

                $(".re_items_1 ul li").each(function(i) {
                    banners[i] = {
                        banner: $(this).data('href')
                    };
                });

                $.ajax({
                    url: re_back_app_objects.admin_url,
                    dataType: "json",
                    method: 'POST',
                    data: {
                        'action'  : 're_and_app_banner' ,
                        'banners' :  banners
                    } ,
                    success: function (data) {
                    }
                });

            }
        }
    })





    $(document).on('click' , '.app_discount_section button' , function (e){
        e.preventDefault();
        let d_date = $('.app_discount_section #discount_timer_input_date').val();
        let d_time = $('.app_discount_section #discount_timer_time').val();

        if ( d_date.length &&  d_time.length ){
            if (confirm('مطمئنی')) {

                $.ajax({
                    url: re_back_app_objects.admin_url,
                    dataType: "json",
                    method: 'POST',
                    data: {
                        'action'        : 're_set_discount_time' ,
                        'discount_date' :  d_date ,
                        'discount_time' :  d_time
                    } ,
                    success: function (data) {
                    }
                });

            }
        }
    })




    $(document).on('click' , '.minimum_purchase_amount button' , function (e){
        e.preventDefault();
        let d_amount = $('.minimum_purchase_amount #minimum_purchase_amount').val();
 
            if (confirm('مطمئنی')) {

                $.ajax({
                    url: re_back_app_objects.admin_url,
                    dataType: "json",
                    method: 'POST',
                    data: {
                        'action'        : 're_minimum_purchase_amount' ,
                        'purchase_min'  :  d_amount , 
                    } ,
                    success: function (data) {
                    }
                }); 
            }
    })

    $(document).on('click' , '.display_product_count button' , function (e){
        e.preventDefault();
        let p_count = $('.display_product_count #display_product_count').val();

        if (confirm('مطمئنی')) {

            $.ajax({
                url: re_back_app_objects.admin_url,
                dataType: "json",
                method: 'POST',
                data: {
                    'action'         : 're_display_product_count' ,
                    'product_count'  :  p_count ,
                } ,
                success: function (data) {
                }
            });
        }
    })




})
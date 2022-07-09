
(function($) {
    downloadTimer = null ;
    let phone = '';
    let section = 1;
    let btnButton = '';
    let btnStatus = false;
    let btnConfirmStatus = false;
    let timeLeft = 120;
    let loader = '<div class="loader-con">' +
        '<div class="sk-chase">' +
        '<div class="sk-chase-dot"></div>' +
        '<div class="sk-chase-dot"></div>' +
        '<div class="sk-chase-dot"></div>' +
        '<div class="sk-chase-dot"></div>' +
        '<div class="sk-chase-dot"></div>' +
        '<div class="sk-chase-dot"></div>' +
        '</div></div>';





    // $('.re_profile').find('a')
    //     .html(re_registration.button_name)
    //     .attr('href','javascript:void(0)');





    $(document).on('keyup' , '#re_phone' , function(e){

        let inputVal =  $(this).val();

        if (  inputVal.substring(0,1) === '0'){
            if (inputVal.length === 11){
                if ( !isNaN(inputVal) ) {
                    btnStatus  = true;
                    $(document).find('#re_send_phone').removeClass('btnSendColorFalse').addClass('btnSendColorTrue');
                }else {
                    btnStatus = false;
                    $(document).find('#re_send_phone').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
                }
            }else{
                $(document).find('#re_send_phone').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
                btnStatus = false;
            }
        }else{
            $(document).find('#re_send_phone').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
            btnStatus = false;
        }


    });


    $(document).on('keyup' , '#re_code' , function(e){

        let inputVal =  $(this).val();
        if (inputVal.length === 4){
            if ( !isNaN(inputVal) ) {
                btnConfirmStatus  = true;
                $(document).find('#re_confirm_code').removeClass('btnSendColorFalse').addClass('btnSendColorTrue');
            }else {
                btnConfirmStatus = false;
                $(document).find('#re_confirm_code').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
            }
        }else{
            $(document).find('#re_confirm_code').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
            btnConfirmStatus = false;
        }
    });



    $(document).on('click' , '.re_resend_code' , function () {
        section  = 1;
        clearInterval(downloadTimer);
        timeLeft = 120;
        $('#re_registration_con').remove();
        $('<div/>', {
            id: 're_registration_con',
            "class": 're_registration_con',
        }).appendTo(document.body);
        $('#re_registration_con').append(re_registration.form);
        $(document).find('#re_send_phone').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
        $('#re_phone').focus();

    });



    $(document).on('click' , '#re_profile_con' , function () {
        loadForm();
    });

    function  loadForm() {
        if (parseInt(re_registration.register_status) === 1){
            window.location.assign(re_registration.profileWOO);
        }else {

            $('<div/>', {
                id: 're_registration_con',
                "class": 're_registration_con',
            }).appendTo(document.body);

            if (section===1){
                $('#re_registration_con').append(re_registration.form);
                $(document).find('#re_send_phone').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');
                $('#re_phone').focus();
            }else{
                $('#re_registration_con').append(re_registration.confirmForm);
                $(document).find('#re_confirm_code').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');

                $('#re_confirm_code').text(btnButton);
                $('#re_code').focus();
                $('#phone_number').text(phone);
                timeLeft = 120;
                timer();
            }
        }
    }




    $(document).on('click' , '#cancel_register' , function () {
        $('#re_registration_con').remove();
    });

    $(document).on("click", '#re_registration_con', function(e) {
        if (  e.target.id  ==='re_registration_con') {
            $('#re_registration_con').remove();
        }
    });





    $(document).on('click' , '#re_send_phone' , function () {
        if (btnStatus === true) {
            sendCode();
        }
    });

    $(document).on("keypress" , function(e){

        if (e.which == 13) {
            if ($('.registerCode').length) {
                if (btnStatus === true) {
                    sendCode();
                }
            }
        }
    });


    $send_status = true;
    function sendCode(){
        if ( $send_status ){
            $send_status = false;
            phone = $('#re_phone').val();
            $('#re_registration_first').append(loader);
            $.ajax({
                url: re_registration.admin_url,
                dataType: "json",
                method: 'POST',
                data: {
                    'action': 're_send_phone',
                    'ajax_nonce' : re_registration.ajax_nonce ,
                    'phone': phone,
                },
                success: function (data) {
                    console.log(data)
                    $send_status = true;
                    if ( 200 === data.result.status ){
                        $('.loader-con').hide('slow' , function () {

                            $('#re_registration_con').html(re_registration.confirmForm);
                            if ( data.result.old_code ){
                                $('.re_timer_con').remove();
                                $('.re_register_notice_con').html('<p class="re_register_notice" >زمان ارسال مجدد کد برای شما فرا نرسیده، لطفا آخرین کد دریافتی را وارد نمایید.</p><p id="code_error_msg">  </p>');
                            }

                            $(document).find('#re_confirm_code').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');

                            $('#phone_number').text(phone);
                            $('#re_code').focus();
                            section = 2;
                            btnButton =data.result.btnText ;
                            $('#re_confirm_code').text(btnButton);
                            timer();

                        }).remove();
                    }else {
                        $('.loader-con').remove();
                        $('.registerCode label').text(data.result.text ).css('font-size', '10px !important');
                    }
                }
            });
        }

    }




    function timer( ){
        clearInterval(downloadTimer);
        $('#re_timer').text(timeLeft);

        downloadTimer = setInterval(function(){
            timeLeft--;
            let time = $('#re_timer');
            $(time).text( parseInt(time.text()) - 1 );
            if(timeLeft <= 1) {
                $('#re_registration_first .confirmCode:last-of-type').html('<a href="javascript:void(0)" class="re_resend_code">ارسال مجدد کد</a>');
            }

            if(timeLeft <= 0){
                clearInterval(downloadTimer);
            }
        },1000);
    }



    $(document).on('click' , '#re_confirm_code' , function () {
        if (btnConfirmStatus == true ) {
            confirmCode();
        }
    });


    $(document).on("keypress" , function(e){
        if (btnConfirmStatus == true ) {
            if (e.which == 13) {
                if ($('.confirmCode').length) {
                    confirmCode();
                }
            }
        }
    });

      function confirmCode(){
        $('#re_registration_first').append(loader);
        let confirmCode = $('#re_code').val();

        $.ajax({
            url : re_registration.admin_url ,
            dataType: 'json' ,
            method : 'POST' ,
            data:{
                'action'     : 're_confirm_code' ,
                'interCode'  : confirmCode ,
                'ajax_nonce' : re_registration.ajax_nonce ,
                'phone'      : phone ,
            } ,
            success:function (e) {


                if ( 200 == e.result.status ){

                    if ( $('#re_profile_con' ).hasClass('go-cart') === true ){
                        window.location.assign( e.result.url+'/cart/' );
                    }else {
                        window.location.assign( e.result.url );
                    }
                }else {
                    btnConfirmStatus = false;
                    section = 1;
                    $('.loader-con , .resend-code-con').remove();
                    $('.re_register_notice_con').html( e.result.text );
                    if ( 403 === e.result.status ){
                        $( "#re_code , #re_confirm_code" ).prop( "disabled", true );
                        $(document).find('#re_confirm_code').removeClass('btnSendColorTrue').addClass('btnSendColorFalse');

                    }

                }

            },
            error:function (x , a  , t ) {
                $('.re_register_notice_con').html('خطا هنگام ارسال');
            }
        });
    }





    if ( re_registration.reg <= 0 ){
        $(document).on('click' , '.wc-forward'  , function () {
            $('#re_profile_con').addClass('go-cart');
            $('.wc-forward').removeAttr("href");

            $('.cart-widget-side').removeClass('woodmart-cart-opened');
            $('.woodmart-close-side').removeClass('woodmart-close-side-opened');

            loadForm();

            return false;
        });
    }




})( jQuery );

jQuery(function ($) {



    let loader_referral = '<div class="referral_loader"><div class="loadingio-spinner-ellipsis-r88594tqbi8"><div class="ldio-458f7fx757p"><div></div><div></div><div></div><div></div><div></div></div></div></div>';

    $('#datatable').DataTable();




    $(".tabs-list li a").click(function(e){
        e.preventDefault();
    });

    $(".tabs-list li").click(function(){
        var tabid = $(this).find("a").attr("href");
        $(".tabs-list li,.tabs div.tab").removeClass("active");   // removing active class from tab

        $(".tab").hide();   // hiding open tab
        $(tabid).show();    // show tab
        $(this).addClass("active"); //  adding active class to clicked tab

    });



    $(document).on('click','.slide-ref' , function (e) {


        let id = $(this).attr("id");
        let length = '.'+id ;
        let con    = '.con'+id ;
        let $this = $(this);
        if ( !$( length ).length ) {
            let row_container = '<tr class="row_container ' + id + '">    <td colspan="7" class="collapseDivClose">'+loader_referral+'</td></tr> ';
            let user_id = $(this).data('userid');
            $(document).find(con).after(row_container);

            $.ajax({
                url: re_referral_object.admin_url,
                method: 'POST',
                data: {
                    'action': 're_referral_action',
                    'user_id': user_id
                },
                success: function (data) {
                    id = "." + id + ' td';
                    $this.find(">div").css({"justifyContent":"left","paddingRight":"0px","paddingLeft":"3px"});
                    $this.find(">div div").css({"backgroundColor":"#05F824"});
                    $(document).find(id).html(data).addClass('collapseDivOpen').removeClass('collapseDivClose');
                }
            });
        }else {

            $this.find(">div").css({"justifyContent":"right","paddingLeft":"0px","paddingRight":"3px"});
            $this.find(">div div").css({"backgroundColor":"#F8054F"});
            id = "." + id + ' td';
            $(document).find(id).removeClass('collapseDivOpen').addClass('collapseDivClose');
            $(length).remove();
        }
    });



    $(document).on('click' , '.re-menu-items p' , function () {
        let clicked_item = $(this).data('item_number');
        $('.re-menu-items p').removeClass('re-ref-active');
        $(this).addClass('re-ref-active');

        $('.re-menu-pages>div').removeClass('re-ref-page-active');
        $( clicked_item ).addClass('re-ref-page-active');

        $('.re-ref-save p').data('re_ref_settings' , clicked_item );

    });



    $(document).on('click','.re-term-con-variable>div>div.add' , function () {
        let term_element = $('.re-ref-set-new-term');
        if( term_element.is(":hidden")  ){
            term_element.show();
        }
    })


    $(document).on('click' , '.re-ref-close' , function () {
        $(this).parent().hide();
    })

    $(".re-ref-set-new-term input").on("click",function () {
        $(document).find(".re-ref-set-new-term input").val('');
    });


    $(document).on('click' , '.re-term-con-variable li>span' , function () {
        if (confirm('مطمئینی؟؟؟' ))
            $(this).parent().remove();
    })


    $(".re-ref-set-new-term input ").on("keypress keyup blur" , function (event) {

        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        let holder_import = $('.re-ref-set-new-term');
        holder_import.data('setnewterms' , $(this).val() );
        holder_import.data('whitchinput' , $(this).attr('id') );
    });



    $(document).on('click' , '#re-ref-save-new-term' , function () {
        let holder_extract = $('.re-ref-set-new-term');
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

 
    $(document).on('click' , '.re-ref-save p' , function () {
        let re_ref_settings = $(this).data('re_ref_settings');
        let settings ;
        if (re_ref_settings === '#re3'){

            let terms_fixed =  {} ;
            $(".re-ref-dynamic ul li").each( function(i) {
                terms_fixed[ $(this).data('termnumber') ] = {
                    termnumber : $(this).data('termnumber')  ,
                    termtype   : $(this).data('termtype')    ,
                    termvalue  : $(this).data('termvalue')
                };
            });
            console.log(terms_fixed)
            settings = {
                status  : '3',
                data    : terms_fixed
            }

        }else if(re_ref_settings === '#re2'){

            let ref_term_type    =  $(".re-ref-cal-type input[type='radio']:checked").val() ;
            let ref_term_count   =  $("#re-ref-cal-count-input").val() ;
            let ref_term_amount  =  $("#re-ref-cal-count-input-amount").val() ;
            settings = {
                status             : '2' ,
                ref_term_count     : ref_term_count ,
                ref_term_type      : ref_term_type ,
                ref_term_amount    : ref_term_amount ,
            }


        }else {
            settings = {status : '1'}
        }
        $.ajax({
            url: re_referral_object.admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_ref_set_calculate' ,
                'settings' : settings
            } ,
            success: function (data) {

            }
        });

    });

    $("#re-ref-cal-count-input , #re-ref-cal-count-input-amount ,.re_calculate_income_pro input  , .re-terms-count-con input, .re-terms-amount input").on("keypress keyup blur",function (event) {

        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });


    $(document).on('click' , '.re-how-cal-score-list-bth p' , function () {
        $('.re-how-cal-score-list-bth p').removeClass('ref-cal-score-item');
        $(this).addClass('ref-cal-score-item');
        let which_section = '.'+ $(this).attr('id');

        // $('.re-how-cal-score-list-con').data('which_cal' , which_section );

        $('.re-how-cal-score-list-con>div').removeClass('ref-cal-score-con');
        $(which_section).addClass('ref-cal-score-con');
    });


    $(document).on('click' , '.re-how-cal-score-save p' , function () {
        let Which_cal = $(this).data('whichnput');
        let Roof      = $('#re-how-cal-get-gift').val();
        let Which_cal_input = $(this).parent().parent().find('input').val();

        let Cal_Gifts = {
            status : Which_cal ,
            roof   : Roof ,
            amount : Which_cal_input
        }

        $.ajax({
            url: re_referral_object.admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_ref_cal_gifts' ,
                'settings' : Cal_Gifts
            } ,
            success: function (data) {

            }
        });

    })


    $(document).on('click' ,'.re_referral_setting button' , function (e){
        e.preventDefault();
        let ref_settins_status  =  $(".re_referral_status input[type='radio']:checked").val() ;

        $.ajax({
            url: re_referral_object.admin_url,
            dataType: "json",
            method: 'POST',
            data: {
                'action'  : 're_change_referral_status' ,
                'status' : ref_settins_status
            } ,
            success: function (data) {

            }
        });
    });

})
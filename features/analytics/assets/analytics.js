
jQuery(function ($) {



    jQuery(document).find('.start_time').persianDatepicker({
        initialValue: false,
        autoClose: true,
        timePicker: {
            enabled: true,
            second:{
                enabled: false
            },
            meridian:true
        },
        format: 'YYYY/MM/DD HH:MM',
        initialValueType: 'gregorian',
        altField: '[name="start_time"]',
        nextButtonText :"544654"

    });


//end date picker
    jQuery(document).find('.end_time').persianDatepicker({
        initialValue: false,
        autoClose: true,
        timePicker: {
            enabled: true,
            second:{
                enabled: false
            },
            meridian:true
        },
        format: 'YYYY/MM/DD HH:MM',
        initialValueType: 'gregorian',
        altField: '[name="end_time"]',
        nextButtonText :"544654"
    });

    jQuery(document).find('.buy_first_time').persianDatepicker({
        initialValue: false,
        autoClose: true,
        timePicker: {
            enabled: true,
            second:{
                enabled: false
            },
            meridian:true
        },
        format: 'YYYY/MM/DD HH:MM',
        initialValueType: 'gregorian',
        altField: '[name="buy_first_time"]',
        nextButtonText :"544654"

    });



    jQuery(document).find('.buy_last_time').persianDatepicker({
        initialValue: false,
        autoClose: true,
        timePicker: {
            enabled: true,
            second:{
                enabled: false
            },
            meridian:true
        },
        format: 'YYYY/MM/DD HH:MM',
        initialValueType: 'gregorian',
        altField: '[name="buy_last_time"]',
        nextButtonText :"544654"
    });


    jQuery(document).find('.send_time').persianDatepicker({
        initialValue: false,
        autoClose: true,
        timePicker: {
            enabled: true,
            second:{
                enabled: false
            },
            meridian:true
        },
        format: 'YYYY/MM/DD HH:MM',
        initialValueType: 'gregorian',
        altField: '[name="send_time_input"]',
        nextButtonText :"544654"
    });



    const  object              = sabadino_analytics_object;

    const  new_users_daily     = object.new_users_daily;
    const  sales_last_month    = object.sales_last_month;
    const  total_sales_pie     = object.total_sales_pie;
    const  five_products       = object.five_products;
    const  five_products_keys  = object.five_products_keys;
    const  specific_product    = object.specific_product;
    const  courses_keys        = object.courses_keys;
    const  daily_sales         = object.dailySales;
    const  daily_user_register = object.daily_user_register;
    const  ajax_url            = object.ajax_url;
    const  sabadino_nonce      = object.sabadino_nonce;





    moment.locale('fa');
    Highcharts.dateFormats = {
        'a': function(ts) {
            return moment(ts).format('dddd')
        },
        'A': function(ts) {
            return moment(ts).format('dddd')
        },
        'd': function(ts) {
            return moment(ts).format('DD')
        },
        'e': function(ts) {
            return moment(ts).format('D')
        },
        'b': function(ts) {
            return moment(ts).format('MMMM')
        },
        'B': function(ts) {
            return moment(ts).format('MMMM')
        },
        'm': function(ts) {
            return moment(ts).format('MM')
        },
        'y': function(ts) {
            return moment(ts).format('YY')
        },
        'Y': function(ts) {
            return moment(ts).format('YYYY')
        },
        'W': function(ts) {
            return moment(ts).format('ww')
        }
    };





    let permission_loader = '<svg xmlns="http://www.w3.org/2000/svg" id="permission-loader" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
        '  <path d="M10 50A40 40 0 0 0 90 50A40 43.1 0 0 1 10 50" fill="#3e3d3d" stroke="none">\n' +
        '    <animateTransform attributeName="transform" type="rotate" dur="0.1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 51.55;360 50 51.55"></animateTransform>\n' +
        '  </path>\n' +
        ' </svg>';

    const export_loader ='<svg xmlns="http://www.w3.org/2000/svg" style="margin: auto;  display: block; shape-rendering: auto;" width="44px" height="25px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
        '<circle cx="84" cy="50" r="10" fill="#3e6d8d">\n' +
        '    <animate attributeName="r" repeatCount="indefinite" dur="0.43859649122807015s" calcMode="spline" keyTimes="0;1" values="14;0" keySplines="0 0.5 0.5 1" begin="0s"></animate>\n' +
        '    <animate attributeName="fill" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="discrete" keyTimes="0;0.25;0.5;0.75;1" values="#3e6d8d;#f9aa47;#ffd400;#4b9bbe;#3e6d8d" begin="0s"></animate>\n' +
        '</circle><circle cx="16" cy="50" r="30" fill="#3e6d8d">\n' +
        '  <animate attributeName="r" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;14;14;14" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="0s"></animate>\n' +
        '  <animate attributeName="cx" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="0s"></animate>\n' +
        '</circle><circle cx="50" cy="50" r="10" fill="#4b9bbe">\n' +
        '  <animate attributeName="r" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;14;14;14" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.43859649122807015s"></animate>\n' +
        '  <animate attributeName="cx" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.43859649122807015s"></animate>\n' +
        '</circle><circle cx="84" cy="50" r="10" fill="#ffd400">\n' +
        '  <animate attributeName="r" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;14;14;14" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.8771929824561403s"></animate>\n' +
        '  <animate attributeName="cx" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.8771929824561403s"></animate>\n' +
        '</circle><circle cx="16" cy="50" r="10" fill="#f9aa47">\n' +
        '  <animate attributeName="r" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;14;14;14" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-1.3157894736842104s"></animate>\n' +
        '  <animate attributeName="cx" repeatCount="indefinite" dur="1.7543859649122806s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-1.3157894736842104s"></animate>\n' +
        '</circle>\n' +
        ' </svg>';


    const sale_plus ='<svg version="1.1" fill="#00CE17" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"\n' +
        '     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">\n' +
        '    <path d="M55.5,379c-2.7,0-5.5-1-7.6-3.1c-4.2-4.2-4.2-10.9,0-15.1l106.7-106.7c2.6-2.6,6.5-3.8,10.1-2.8l164.8,41.2l123.7-123.7\n' +
        '        c4.2-4.2,10.9-4.2,15.1,0s4.2,10.9,0,15.1l-128,128c-2.6,2.6-6.5,3.7-10.1,2.8l-164.8-41.2L63,375.9C60.9,377.9,58.2,379,55.5,379z"/>\n' +
        '      <path d="M460.8,272.3c-5.9,0-10.7-4.8-10.7-10.7V187h-74.7c-5.9,0-10.7-4.8-10.7-10.7s4.8-10.7,10.7-10.7h85.3\n' +
        '        c5.9,0,10.7,4.8,10.7,10.7v85.3C471.5,267.5,466.7,272.3,460.8,272.3z"/>\n' +
        '</svg>';

    const sale_mins ='<svg version="1.1"  fill="#CE0027" xmlns="http://www.w3.org/2000/svg"  x="0px" y="0px"\n' +
        '     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">\n' +
        '    <path d="M51.6,132c-2.7,0-5.5,1.2-7.6,3.6c-4.2,4.7-4.2,12.5,0,17.2l106.7,121.7c2.6,3,6.5,4.3,10.1,3.2l164.8-47l123.7,141.2\n' +
        '        c4.2,4.7,10.9,4.7,15.1,0s4.2-12.5,0-17.2l-128-146.1c-2.6-3-6.5-4.2-10.1-3.2l-164.8,47L59.1,135.6C57,133.2,54.3,132,51.6,132z"/>\n' +
        '    <path d="M456.9,253.7c-5.9,0-10.7,5.5-10.7,12.2v85.2h-74.7c-5.9,0-10.7,5.5-10.7,12.2s4.8,12.2,10.7,12.2h85.3\n' +
        '        c5.9,0,10.7-5.5,10.7-12.2v-97.4C467.6,259.2,462.8,253.7,456.9,253.7z"/>\n' +
        '</svg>';

    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });





    $(document).find('#active-page-name').text('آنالیز');
    $(document).on('click' , '.menu-items-con li' , function (e){
        e.preventDefault();
        $(document).find('#active-page-name').text($(this).find('a').text( ));
        let whiche_item = '.'+$(this).attr('id');
        $(document).find('.menu-items-con li').removeClass('active');
        $(this).addClass('active');
        $(document).find('main>div').removeClass('active');
        $(document).find( whiche_item ).addClass('active');
    });



    $(document).on('click' , '.remove--item' , function (){
        $(this).parent().remove();
    });

    // add new course to list
    $(document).on('click' , '.course-add' , function (){
        $(document).find('.courses-list section').hide();
        $(this).parent().siblings('section').show();
    });

    $(document).on('click' , '.dynamic-courses-list button.close' , function (){
        $(this).parent().parent().parent().hide();
    });


    $(document).on('click' , '.add-course-form button' , function (e){
        e.preventDefault();
        let $this      = $(this);
        let courses_id = [];
        let element = $this.data('course');
        let keyword = $this.siblings('input').val();
        if (keyword.length >= 3 ){
            element = '.courses-list .'+element;
            $( $(element).html() ).each( function( ) {
                courses_id.push( $(this).attr('id') )
            });

            $this.html( permission_loader );

            $.ajax({
                url: ajax_url ,
                dataType: "json" ,
                method  : 'POST' ,
                data: {
                    'action'   : 'za_search_products' ,
                    'exclude'  : courses_id           ,
                    'keyword'  : keyword
                } ,
                success: function ( data, textStatus, xhr ) {
                    if ( xhr.status === 200 ){
                        let course_final ='';
                        $this.text( 'جستجو' );

                        for (const [key, value] of Object.entries(data)) {
                            course_final += '<li id="'+ value.id +'"> '+ value.title +' </li>';
                        }
                        $this.parent().parent().siblings('.courses-fetched-list').children('ul').html(course_final);
                    }

                    if ( xhr.status === 404  ){
                        $this.text( 'دوره یافت نشد' );
                    }
                }
            });
        }
    });


    // add new course to list
    $(document).on('dblclick' , '.courses-fetched-list li' , function (){
        let new_course = '<p id="'+ $(this).attr('id') +'"> '+  $(this).text()  +' <span class="remove--item dashicons dashicons-dismiss"></span></p>';
        let old_course = $(this).parent().parent().parent().siblings('.items-list').html();
        old_course = old_course + new_course;
        $(this).parent().parent().parent().siblings('.items-list').html(old_course);
        $(this).parent().parent().parent().hide();
        $(this).parent().html('');
    });


    $(document).click(function(e) {
        let course_element = $('.courses-list section');
        if( course_element.is(':visible') ){
            if( !$(e.target).is('.course-add') && !$(e.target).is('.courses-list section *')){
                e.preventDefault();
                course_element.hide();
            }
        }
    });



    $(document).on('dblclick' , '#user-export' , function (e){
        e.preventDefault();
    });

    $(document).find('#start_time').blur(function(){
        if ( $(this).val() == '' ){
            $(document).find('#start_time_input').val('');
        }
    });
    $(document).find('#end_time').blur(function(){
        if ( $(this).val() == '' ){
            $(document).find('#end_time_input').val('');
        }
    });

    $(document).on('click' ,'.user-export', function (){
        let form    = $(document).find('#form-container');
        let $this   = $(this);
        $this.removeClass('user-export');
        $this.html(export_loader);
        $(document).find('.status-viewer').text('در حال پردازش');
        $(document).find('.status-count').html( export_loader );
            let purchase_total_from = $(form).find( '#total-from' ).val();
            let purchase_total_to   = $(form).find( '#total-to' ).val();
            let order_count_min     = $(form).find( '#order-count-min' ).val();
            let order_count_max     = $(form).find( '#order-count-max' ).val();
            let product_include     = $(form).find( '#product-include' );
            let product_exclude     = $(form).find( '#product-exclude' );
            let from_register_time  = $(form).find('[name ="start_time"]').val();
            let until_register_time = $(form).find('[name ="end_time"]').val();
            let buy_first_time      = $(form).find('[name ="buy_first_time"]').val();
            let buy_last_time       = $(form).find('[name ="buy_last_time"]').val();
            let without_purchase    = $(form).find('[name ="without-any-purchase"]' ).is(':checked');

            let include_products    = [];
            let exclude_products    = [];

            from_register_time      = from_register_time.slice( 0, -3 );
            until_register_time     = until_register_time.slice( 0, -3 );
            buy_first_time          = buy_first_time.slice( 0, -3 );
            buy_last_time           = buy_last_time.slice( 0, -3 );
            $( $(product_include).html() ).each( function( ) {
                include_products.push( $(this).attr('id') )
            });

            $( $(product_exclude).html() ).each( function( ) {
                exclude_products.push( $(this).attr('id') )
            });

        $.ajax({
            url: ajax_url ,
            dataType: "json" ,
            method  : 'POST' ,
            data: {
                'action'              : 'za_analytics_user_export'    ,
                'nonce'               : sabadino_nonce                ,
                'purchase_total_from' : purchase_total_from           ,
                'purchase_total_to'   : purchase_total_to             ,
                'order_count_min'     : order_count_min               ,
                'order_count_max'     : order_count_max               ,
                'from_register'       : from_register_time            ,
                'until_register'      : until_register_time           ,
                'buy_first_time'      : buy_first_time                ,
                'buy_last_time'       : buy_last_time                 ,
                'include_products'    : include_products              ,
                'exclude_products'    : exclude_products              ,
                'without_purchase'    : without_purchase
            } ,
            success: function ( data, textStatus, xhr ) {

                if ( xhr.status === 200 ){
                    $this.html('دریافت مجدد');
                    $this.addClass('user-export');
                    $(document).find('.status-viewer').text('اطلاعات دریافت شد');
                    $(document).find('.status-count').html( data.count );
                    let option = '<option value="'+ data.code  +'"  data-desciption="" data-count="'+ data.count +'"   data-message="" data-name="" selected  > '+ data.code +' </option>'
                    $(document).find('#user_list_select').append( option );

                    $.ajax({
                        url: ajax_url ,
                        dataType: "json" ,
                        method  : 'POST' ,
                        data: {
                            'action'      : 'za_get_sms_list' ,
                            'nonce'       : sabadino_nonce
                        } ,
                        success: function ( data, textStatus, xhr ) {
                            if ( xhr.status === 200 ){
                                $(document).find('.sms-list-con tbody ').html( data.result );
                            }
                        }
                    });
                }
                if ( xhr.status === 404  ){
                    $this.html('دریافت مجدد');
                    $this.addClass('user-export');
                    $(document).find('.status-viewer').text('اطلاعات دریافت شد');
                    $(document).find('.status-count').html( 0 );
                }
            }
        });
    });

    let options = '';
    for (const [key, value] of Object.entries( object.final_users_list )) {
        options += '<option value="'+ key +'"  data-desciption="'+ value.description +'" data-date="'+ value.date +'" data-message="'+ value.message +'" data-status="'+ value.status +'"  data-count="'+ value.count +'" > '+ key +' </option>';
    }

    $(document).find('#user_list_select option').after( options );
    $(document).find('.sms-list-con tbody ').html( object.final_sms_list );


    $(document).find('#user_list_select').on('change', function() {
        let description = $(this).find(':selected').data('desciption');
        let status      = $(this).find(':selected').data('status');
        let message     = $(this).find(':selected').data('message');
        let count       = $(this).find(':selected').data('count');
        let date        = $(this).find(':selected').data('date');

        if( status){
            $(document).find('#sms-send-status').prop('checked', true);
        }else{
            $(document).find('#sms-send-status').prop('checked' , false );
        }

        $(document).find('[name ="send_time_input"]').val( date );
        $(document).find('#description').val( description );
        $(document).find('#sms-message').val( message );
        $(document).find('.status-count').text( count );
    });

    $(document).on('click' ,'#sms-save', function ( e ){
        e.preventDefault();
        let $this = $(this);

        let form = $(document).find('#send-sms-con');
        let user_list_select = $(form).find( '#user_list_select' ).val();
        let status_count     = $(form).find( '.status-count' ).text();
        let sms_message      = $(form).find( '#sms-message' ).val();
        let send_time_input  = $(form).find('[name ="send_time_input"]').val();
        let sms_list_status  = $(form).find( '#sms-send-status' ).is(":checked");
        let description      = $(form).find( '#description' ).val();

        send_time_input = send_time_input.slice( 0, -3 );

        let select      = $(document).find('#user_list_select')
        select.find(':selected').data('desciption' , description );
        select.find(':selected').data('status'  , sms_list_status );
        select.find(':selected').data('message' , sms_message );
        select.find(':selected').data('count'   , status_count );
        select.find(':selected').data('date' ,send_time_input);


        if(  user_list_select.length < 4 ){
            alert('هیچ لیستی انتخاب نشده');
            return;
        }
        $this.html(export_loader);
        $.ajax({
            url: ajax_url ,
            dataType: "json" ,
            method  : 'POST' ,
            data: {
                'action'      : 'za_send_sms'     ,
                'nonce'       : sabadino_nonce    ,
                'code'        : user_list_select  ,
                'count'       : status_count      ,
                'message'     : sms_message       ,
                'date'        : send_time_input   ,
                'status'      : sms_list_status   ,
                'description' : description
            } ,
            success: function ( data, textStatus, xhr ) {
                if ( xhr.status === 200 ){
                    $this.html('ذخیره تغییرات');
                    $.ajax({
                        url: ajax_url ,
                        dataType: "json" ,
                        method  : 'POST' ,
                        data: {
                            'action'      : 'za_get_sms_list' ,
                            'nonce'       : sabadino_nonce
                        } ,
                        success: function ( data, textStatus, xhr ) {
                            if ( xhr.status === 200 ){
                                $(document).find('.sms-list-con tbody ').html( data.result );

                            }
                            if ( xhr.status === 404  ){

                            }
                        }
                    });
                }
                if ( xhr.status === 404  ){

                }
            }
        });


    });


    $(document).find('#export-all-exclude-user').change(function (){
        if ($(this).is(':checked')){
            $(document).find('.user-export-form-container .first-control-con').css('visibility','hidden');
        }else{
            $(document).find('.user-export-form-container .first-control-con').css('visibility','visible');
        }
    });

    $(document).on('click','.form-group svg' , function (){
        $(this).siblings('input').val('');
    });


    $(document).on('click','.remove-sms-list svg' , function (){
        if ( confirm( 'حذف شود؟؟ ') ){

            let $this    = $(this).parent();
            let old_svg  = $this.html();
            let sms_code = $this.data('list-name');
            $this.html( export_loader );
            $.ajax({
                url: ajax_url ,
                dataType: "json" ,
                method  : 'POST' ,
                data: {
                    'action'  : 'za_remove_sms_list' ,
                    'nonce'   : sabadino_nonce ,
                    'code'    : sms_code
                } ,
                success: function ( data, textStatus, xhr ) {
                    if ( xhr.status === 200 ){
                        $this.parent().remove();
                    }
                    if ( xhr.status === 500  ){
                        $this.html( old_svg );
                    }
                }
            });


        }

    });




    // number Seperator
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }


    ///Analytics
    color_5_chart_0 = '#AAF856';
    color_5_chart_1 = '#76C51D';
    color_5_chart_2 = '#419300';
    color_5_chart_3 = '#006500';
    color_5_chart_4 = '#003800';

    $(document).find('#yesterday-sales-shower').html( numberWithCommas( daily_sales.yesterday ));
    $(document).find('#b-yesterday-sales-shower').html( numberWithCommas( daily_sales.b_yesterday));
    $(document).find('#today-sales-shower').html( numberWithCommas( daily_sales.today));
    $(document).find('#this-week-sales-shower').html( numberWithCommas( daily_sales.week));

    $(document).find('#yesterday-user-shower').html( numberWithCommas( daily_user_register.yesterday ));
    $(document).find('#b-yesterday-user-shower').html( numberWithCommas( daily_user_register.b_yesterday));
    $(document).find('#today-user-shower').html( numberWithCommas( daily_user_register.today));
    $(document).find('#week-user-shower').html( numberWithCommas( daily_user_register.week));

    if ( parseInt( daily_sales.yesterday)  > parseInt( daily_sales.b_yesterday) ){
        $(document).find('#icon-yesterday-sale-con').html( sale_plus );
    }else {
        $(document).find('#icon-yesterday-sale-con').html( sale_mins );
    }

    if ( parseInt( daily_user_register.yesterday)  > parseInt( daily_user_register.b_yesterday) ){
        $(document).find('#icon-yesterday-user-con').html( sale_plus );
    }else {
        $(document).find('#icon-yesterday-user-con').html( sale_mins );
    }

    if (document.getElementById("new-user-report") && new_users_daily !== null ) {
        const chart = Highcharts.stockChart('new-user-report', {

            chart: {
                alignTicks: false,
                height: 600
            },
            title: {
                text: 'کاربران ثبت نامی'
            },
            credits: {
                enabled: false
            },
            rangeSelector: {
                selected: 1
            },
            series: [{
                type: 'column',
                name: 'کاربر جدید',
                data: new_users_daily,
                color: color_5_chart_2

            }]

        });
    }

    if (document.getElementById("five-product-sales") && five_products_keys !== null ) {
        arr_0    = [];
        arr_1    = [];
        arr_2    = [];
        arr_3    = [];
        arr_4    = [];
        tot_0    = 0;
        tot_1    = 1;
        tot_2    = 2;
        tot_3    = 3;
        tot_4    = 4;
        key_cat  = [];
        $index   = 0;

        let keys    = Object.keys( five_products_keys );
        let values  = Object.values( five_products_keys );

        for (const [key, values] of Object.entries( five_products ) ) {
            key_cat.push( key );
            arr_0.push( values[keys[0]]);
            arr_1.push( values[keys[1]]);
            arr_2.push( values[keys[2]]);
            arr_3.push( values[keys[3]]);
            arr_4.push( values[keys[4]]);

            tot_0 = tot_0 + values[keys[0]];
            tot_1 = tot_1 + values[keys[1]];
            tot_2 = tot_2 + values[keys[2]];
            tot_3 = tot_3 + values[keys[3]];
            tot_4 = tot_4 + values[keys[4]];

        }
        key_cat.sort();

        average = [] ;
        let index = 0;
        for (const [key, values] of Object.entries( five_products  ) ) {
            for (const [ke, value] of Object.entries( values  ) ) {
                average[index]= Object.values( values );
            }
            index++;
        }
        let avg_0 = average[0].reduce(function(a, b){return a+b;}) / 5;
        let avg_1 = average[1].reduce(function(a, b){return a+b;}) / 5;
        let avg_2 = average[2].reduce(function(a, b){return a+b;}) / 5;
        let avg_3 = average[3].reduce(function(a, b){return a+b;}) / 5;
        let avg_4 = average[4].reduce(function(a, b){return a+b;}) / 5;
        let avg_5 = average[5].reduce(function(a, b){return a+b;}) / 5;
        let avg_6 = average[6].reduce(function(a, b){return a+b;}) / 5;


        Highcharts.chart('five-product-sales', {
            chart: {
                height: 600
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'پنج محصول اصلی'
            },
            xAxis: {
                categories: [ key_cat[0] ,key_cat[1] ,key_cat[2] ,key_cat[3],key_cat[4] ,key_cat[5] ,key_cat[6] ],
                title: {
                    text: null
                }
            },
            labels: {
                items: [{
                    html: 'میزان کل فروش ',
                    style: {
                        left: '50px',
                        top: '18px',
                        color: (
                            Highcharts.defaultOptions.title.style &&
                            Highcharts.defaultOptions.title.style.color
                        ) || 'black'
                    }
                }]
            },

            tooltip: {
                // pointFormat: '<div style="display: flex;justify-content: space-between" ></div> <p>{series.name}:</p> <p> <b class="seperate-numbers">{point.y:.1f}</b> </p></div>'
            },
            series: [{
                type: 'column',
                name: values[0],
                data: arr_0 ,
                color: color_5_chart_0
            }, {
                type: 'column',
                name: values[1],
                data: arr_1,
                color: color_5_chart_1
            }, {
                type: 'column',
                name: values[2],
                data: arr_2 ,
                color: color_5_chart_2
            }, {
                type: 'column',
                name: values[3],
                data: arr_3 ,
                color: color_5_chart_3
            }, {
                type: 'column',
                name: values[4],
                data: arr_4,
                color: color_5_chart_4
            }, {
                type: 'spline',
                name: 'میانگین فروش ',
                color : '#F3475A',
                data: [ avg_0 ,avg_1 ,avg_2 ,avg_3 ,avg_4 ,avg_5, avg_6],
                marker: {
                    lineWidth: 2,
                    lineColor: Highcharts.getOptions().colors[3],
                    fillColor: 'white'
                }
            }, {
                type: 'pie',
                name: 'فروش کل ',
                data: [{
                    name: values[0],
                    y: tot_0,
                    color: color_5_chart_0
                }, {
                    name: values[1],
                    y: tot_1,
                    color: color_5_chart_1
                }, {
                    name: values[2],
                    y: tot_2,
                    color: color_5_chart_2
                },{
                    name: values[3],
                    y: tot_3,
                    color: color_5_chart_3
                }, {
                    name: values[4],
                    y: tot_4,
                    color: color_5_chart_4
                }  ],
                center: [160, 10 ],
                size: 100,
                showInLegend: false,
                dataLabels: {
                    enabled: false
                }
            }]
        });

    }

    let chart_options = '';
    for (const [key, values] of Object.entries( courses_keys ) ) {
        chart_options += '<option value="'+key+'">'+values+'</option>';
    }
    $(document).find('#chart-form-container select').html(chart_options);

    if (document.getElementById("specific-product-sales") && specific_product !== null ){
        Highcharts.chart('specific-product-sales', {
            credits: {
                enabled: false
            },
            chart: {
                type: 'line'
            },
            title: {
                text: 'فروش ماهیانه'
            },
            subtitle: {
                text: ''
            },
            yAxis: {
                title: {
                    text: ''
                } ,
                crosshair: true
            },
            xAxis: {
                categories: Object.keys(specific_product[Object.keys(specific_product)[0]]).reverse()  ,
                accessibility: {
                    rangeDescription: ''
                },
                crosshair: true
            },
            legend: {
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                }
            },
            series: [{
                name: ' ',
                data:   Object.values(specific_product[Object.keys(specific_product)[0]]).reverse(),
                color: '#006500'
            } ],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'right'
                        }
                    }
                }]
            }

        });

        $(document).on('click' ,'#add-chart-specific' ,function (){

            let all  = $( "#all-courses").is(':checked');
            let chart  = $('#specific-product-sales').highcharts(),
                newData = chart.series[0].yData.reverse();


            if ( all ){
                $( $(document).find('#chart-form-container select').html( ) ).each( function( ) {
                    chart.addSeries({
                        name: $(this).text(),
                        data:Object.values(specific_product[$(this).val()]).reverse()
                    });
                });
            }else {
                let id   = $( "#add-chart-select option:selected" ).val();
                let text = $( "#add-chart-select option:selected" ).text();


                chart.addSeries({
                    name: text,
                    data: Object.values(specific_product[id]).reverse()
                });
            }



        });
    }

    if ( sales_last_month ){
        Highcharts.chart('all-sales-in-month', {
            credits: {
                enabled: false
            },
            chart: {
                type: 'line'
            },
            title: {
                text: 'فروش ماهیانه'
            },
            subtitle: {
                text: ''
            },
            yAxis: {
                title: {
                    text: ''
                } ,
                crosshair: true
            },
            xAxis: {
                categories:  sales_last_month.keys ,
                accessibility: {
                    rangeDescription: ''
                },
                crosshair: true
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                enabled: false
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                }
            },
            series: [{
                name: '',
                data:  sales_last_month.values,
                color: '#006500'
            } ],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

        });
    }


    pie_colors = ['#192814' ,'#003800' ,'#006500','#419300' ,'#76C51D','#AAF856' ]
    if (document.getElementById("all-sales-pie") && total_sales_pie !== null) {

        let salesmonth_pie = [];
        let index = 0;
        for ( const [key, values] of Object.entries(total_sales_pie )) {
            for ( const [ke, value] of Object.entries( values )  ) {
                salesmonth_pie.push({name: ke, y: value ,color:pie_colors[index]});
                index++;
            }
        }

        Highcharts.chart('all-sales-pie', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'مجموع فروش هر دوره در 30 روز گذشته'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        borderRadius: 5,
                        color:'#000000',
                        backgroundColor: 'rgba(252, 255, 255, 1)',
                        borderWidth: 1,
                        borderColor: '#ddd',
                        y: -6,
                        enabled: true,
                        format: '<b> {point.percentage:.1f} % </b>',
                        distance: -50,
                        filter: {
                            property: 'percentage',
                            operator: '>',
                            value: 4
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: '',
                colorByPoint: true,
                data: salesmonth_pie
            }]
        });
    }



    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('_');
    }



    $('#order-count-min').on("change mousemove", function() {
        $(document).find('#order-count-min-viewer').html( numberWithCommas($(this).val()) );
    });
    $('#order-count-max').on("change mousemove", function() {
        $(document).find('#order-count-max-viewer').html( numberWithCommas($(this).val()) );
    });
    $('#total-to').on("change mousemove", function() {
        $(document).find('#total-to-viewer').html( numberWithCommas($(this).val()));
    });
    $('#total-from').on("change mousemove", function() {
        $(document).find('#total-from-viewer').html( numberWithCommas($(this).val()));
    });














})







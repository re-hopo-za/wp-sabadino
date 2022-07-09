




    jQuery(document).ready(function($) {

        $( "input.re_input:not(:disabled):first" ).prop( "checked", true );



        let dates = $( "input.re_input:not(:disabled):first" );
        let date = dates.val();
        let time = dates.data('date');
        $('#daypart').attr('value', date+'|'+time);



        $(document).on('click' , '.re_input' , function () {
            let dates = $('input[name="date_input"]:checked');
            let date  = dates.val();
            let micro = dates.data('micro-time');

            let time = dates.data('date');
            $('#daypart').attr('value', date+'|'+time);
            $('#re_micro_time').attr('value', micro );
        });


});
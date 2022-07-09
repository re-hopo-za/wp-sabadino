
jQuery(function ($){





    $("#re_ref_input").on("keypress keyup blur",function (event) {

        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });



 $(document).on('submit' ,'.re_ref_code_btn' , function (e){
     e.preventDefault();
      let re_ref_input  = $('#re_ref_input').val();
     if (re_ref_input != ''){
         $.ajax({
             dataType:'json' ,
             url : re_referral_dashboard_object.admin_url ,
             method : 'POST' ,
             data:{
                 'action'          : 're_set_referral_code' ,
                 'referral_code'   :  re_ref_input
             } ,
             success:function (e) {
                 console.log(e)
             },
             error:function (x , a  , t ) {
             }
         });
     }


 });



})
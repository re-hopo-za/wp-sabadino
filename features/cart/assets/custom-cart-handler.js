(function ($) {

    let adding_to_cart_process = 0;
    let minus_icon  = sabadino_object.home_url+'/wp-content/plugins/sabadino/features/cart/assets/minus.png';
    let trash_icon  = sabadino_object.home_url+'/wp-content/plugins/sabadino/features/cart/assets/trash.png';
    let plus        = sabadino_object.home_url+'/wp-content/plugins/sabadino/features/cart/assets/plus.png';
    let plus_white  = sabadino_object.home_url+'/wp-content/plugins/sabadino/features/cart/assets/plus-white.png';
    var variables   = sabadino_object.variables_list


    $(document).on('click' , '.cart-con .normal-cart', function (e) {
        e.preventDefault();
        let $this  = $(this);
        let parent = $this.parent();

        let product_type = parent.data('product-type');
        let product_id   = parseInt( parent.data('product-id') );
        let whole_parent = $(document).find('.p-id-'+ product_id );
        let whole_this = $(document).find('.p-id-'+ product_id +'>div' );
        let factor = parseInt( parent.data('factor' ) );
        let stock = parseInt( parent.data('stock') );
        let whole_plus_img = whole_parent.find('.cart-plus-con img');
        let whole_mines_img = whole_parent.find('.cart-mines-con img');
        let whole_count_label = $(document).find('.p-id-'+ product_id +' .cart-count-con span' );
        let whole_plus_btn = $(document).find('.p-id-'+ product_id ).find('.cart-plus-con');

        if( product_type === 'simple-product' ){
            if( parent.hasClass('variable-handler') ){
                let parent_id  = parent.data('parent-id');
                let whole_parent = $(document).find('.p-id-'+ parent_id )
                whole_parent.find('.cart-plus-con img').attr( 'src' ,plus );
                whole_parent.find('>div').removeClass('normal-cart').addClass('added-cart');
                let calculated = factor + variable_calculate_quantity( parent_id );
                variable_update_parent_quantity( parent_id ,calculated );
                variable_update_quantity( parent_id ,product_id ,factor );
                whole_parent.find('.cart-count-con span').text( calculated );
                if ( stock === factor ){
                    whole_parent.find('.cart-plus-con').addClass('limit-stock');
                    whole_parent.attr( "title" , "موجودی این محصول "+ stock +" می باشد" );
                }
                if ( calculated === factor ){
                    whole_parent.find('.cart-mines-con img').attr( 'src' ,trash_icon );
                }else {
                    whole_parent.find('.cart-mines-con img').attr( 'src' ,minus_icon );
                }
            }
            if ( stock === 1 ){
                whole_plus_btn.addClass('limit-stock');
                whole_plus_btn.attr( "title" , "موجودی این محصول "+ stock +" می باشد" );
            }
            whole_parent.data('count' ,factor );
            whole_count_label.text( factor);
            whole_this.removeClass('normal-cart').addClass('added-cart');
            whole_mines_img.attr( 'src' ,trash_icon );
            whole_plus_img.attr( 'src' ,plus );


            start_adding_to_cart_animate();
            adding_to_cart_process++;
            $.ajax({
                url: sabadino_object.ajax_url,
                method: 'GET',
                dataType: 'json',
                cache:false ,
                data: {
                    'action'     : 're_cart_add',
                    'product_id' : product_id,
                    'quantity'   : factor
                },
                success:function (e) {
                    adding_amount_to_holder( e['count'] ,e['total']);
                    $.post(
                        sabadino_object.ajax_url,
                        {'action': 'mode_theme_update_mini_cart'},
                        function(response) {
                            $('.widget_shopping_cart_content').html( response );
                        }
                    );
                    adding_to_cart_process--;
                    if (adding_to_cart_process === 0 ){
                        end_adding_to_cart_animate();
                    }
                }
            });

        }else {
            variable_handler( product_id );
        }
    });


    $(document).on('click' , '.cart-con .cart-plus-con' , function (e) {
        e.preventDefault();
        let $this  = $(this);
        let parent = $this.parent().parent();
        let product_id   = parseInt( parent.data('product-id') );
        let whole_parent = $(document).find('.p-id-'+ product_id );
        let whole_btn_con = whole_parent.find('>div');
        let product_type = parent.data('product-type');
        let whole_this = $(document).find('.p-id-'+ product_id +' .cart-plus-con' );
        let factor = parseInt( parent.data('factor' ) );
        let stock  = parseInt( parent.data('stock') );
        let whole_plus_img = whole_parent.find('.cart-plus-con img');
        let whole_mines_img = whole_parent.find('.cart-mines-con img');
        let whole_count_label = $(document).find('.p-id-'+ product_id +' .cart-count-con span' );
        let count       = parseInt( parent.data('count') );
        if( product_type === 'simple-product' ){
            let calculate_stock =  stock - count;
            if ( calculate_stock >= count + factor || calculate_stock >= factor ){
                if ( calculate_stock >= count + factor ){
                    whole_plus_img.find('img').attr('src' ,plus );
                }else if ( calculate_stock >= factor ){
                    whole_this.addClass('limit-stock');
                    whole_this.attr( "title" , "موجودی این محصول "+ stock +" می باشد" );
                }
                count = count + factor;
                whole_btn_con.removeClass('normal-cart').addClass('added-cart');
                whole_parent.data('count'  , count );
                whole_count_label.text(count);
                adding_to_cart_process++;
                if ( count > factor ){
                    whole_mines_img.attr('src' ,minus_icon );
                }

                if( parent.hasClass('variable-handler') ){
                    let parent_id  = parent.data('parent-id');
                    let whole_parent = $(document).find('.p-id-'+ parent_id )
                    whole_parent.find('.cart-plus-con img').attr( 'src' ,plus );
                    if ( count > factor ){
                        whole_parent.find('.cart-mines-con img').attr( 'src' ,minus_icon );
                    }

                    let calculated = factor + variable_calculate_quantity( parent_id );
                    variable_update_parent_quantity( parent_id ,calculated );
                    variable_update_quantity( parent_id ,product_id ,count );
                    whole_parent.find('.cart-count-con span').text( calculated );

                    if ( stock === factor ){
                        whole_parent.find('.cart-plus-con').addClass('limit-stock');
                        whole_parent.attr( "title" , "موجودی این محصول "+ stock +" می باشد" );
                    }
                }


                start_adding_to_cart_animate();

                $.ajaxQueue({
                    url: sabadino_object.ajax_url,
                    method: 'GET',
                    dataType: 'json',
                    cache:false ,
                    data: {
                        'action': 're_cart_add',
                        'product_id' : product_id,
                        'quantity'   : count
                    }
                }).done(function( e ) {
                    adding_amount_to_holder( e['count'] ,e['total']);
                    $.post(
                        sabadino_object.ajax_url,
                        {'action': 'mode_theme_update_mini_cart'},
                        function( response ) {
                            $('.widget_shopping_cart_content').html( response );
                        }
                    );
                    adding_to_cart_process--;
                    if ( adding_to_cart_process === 0 ){
                        end_adding_to_cart_animate();
                    }
                });
            }
        }else{
            variable_handler( product_id );
        }
    });


    $(document).on('click' , '.cart-con .cart-mines-con' , function (e) {
        e.preventDefault();
        let $this  = $(this);
        let parent = $this.parent().parent();
        let product_id   = parseInt( parent.data('product-id') );
        let whole_parent = $(document).find('.p-id-'+ product_id );
        let whole_btn_con = whole_parent.find('>div');
        let product_type = parent.data('product-type');
        let whole_plus_con = $(document).find('.p-id-'+ product_id +' .cart-plus-con' );
        let factor = parseInt( parent.data('factor' ) );
        let whole_plus_img = whole_parent.find('.cart-plus-con img');
        let whole_mines_img = whole_parent.find('.cart-mines-con img');
        let whole_count_label = $(document).find('.p-id-'+ product_id +' .cart-count-con span' );
        let count = parseInt( parent.data('count') );

        if( product_type === 'simple-product' ){
            count = count - factor;

            whole_parent.data('count' , count );
            whole_count_label.text( count );
            whole_plus_con.removeClass('limit-stock');

            adding_to_cart_process++;
            start_adding_to_cart_animate();

            if ( count === 0 ){
                whole_btn_con.removeClass('added-cart').addClass('normal-cart');
                whole_count_label.text('اضافه کردن به سبد');
                whole_plus_img.attr('src' ,plus_white );
            }
            if ( count === factor ){
                whole_mines_img.attr('src' ,trash_icon );
            }

            if( parent.hasClass('variable-handler') ){
                let parent_id  = parent.data('parent-id');
                let whole_parent = $(document).find('.p-id-'+ parent_id )
                let calculated   = variable_calculate_quantity( parent_id ) - factor;
                variable_update_parent_quantity( parent_id ,calculated );
                variable_update_quantity( parent_id ,product_id ,count );
                if ( calculated === factor ){
                    whole_parent.find('.cart-mines-con img').attr('src' ,trash_icon );
                }
                whole_parent.find('.cart-count-con span').text( calculated );
                if ( calculated === 0 ){
                    whole_parent.find('>div').removeClass('added-cart').addClass('normal-cart');
                    whole_parent.find('.cart-count-con span').text('اضافه کردن به سبد');
                    whole_parent.find('.cart-plus-con img').attr('src' ,plus_white );
                }

            }


            $.ajaxQueue({
                url: sabadino_object.ajax_url,
                method: 'GET',
                dataType: 'json',
                cache:false ,
                data: {
                    'action': 're_cart_add',
                    'product_id' : product_id,
                    'quantity'   : count
                }
            }).done(function( e ) {
                adding_amount_to_holder( e['count'] ,e['total']);
                $.post(
                    sabadino_object.ajax_url,
                    {'action': 'mode_theme_update_mini_cart'},
                    function(response) {
                        $('.widget_shopping_cart_content').html( response );
                    }
                );
                adding_to_cart_process--;
                if (adding_to_cart_process === 0 ){
                    end_adding_to_cart_animate();
                }
            });
        }else{
            variable_handler( product_id );
        }

    });

    $(document).on('click' , '.remove_from_cart_button' , function () {
        let product_id = $(this).data('product_id');
        let whole_app = $(document).find('.p-id-'+ product_id );
        let product_type = whole_app.data('product-type');
        if ( product_type === 'simple-product' ){
            whole_app.data('count' ,0 );
            whole_app.find('>div').removeClass('added-cart').addClass('normal-cart');
            whole_app.find('.cart-plus-con img').attr('src' ,plus_white );
            whole_app.find('span').text('اضافه کردن به سبد');
        }else{
            let child_id = $(this).data('variable-id');
            console.log(child_id)
            let calculated = variable_calculate_quantity( product_id ) - variable_get_quantity( product_id ,child_id );
            console.log(calculated)
            if ( calculated === 0 ){
                whole_app.data('count' ,0 );
                whole_app.find('>div').removeClass('added-cart').addClass('normal-cart');
                whole_app.find('.cart-plus-con img').attr('src' ,plus_white );
                whole_app.find('span').text('اضافه کردن به سبد');
            }else {
                whole_app.find('span').text( calculated );
            }
            variable_update_parent_quantity( product_id ,calculated );
            variable_update_quantity( product_id ,child_id ,0 );
        }
    });



    ////// variable


    function variable_calculate_quantity( parent_id ){
        let calculated = 0;
        for ( const [ key, value] of Object.entries( variables[parent_id].list ) ) {
            calculated += parseInt( value.quantity_cart );
        }
        return calculated;
    }

    function variable_update_parent_quantity( parent_id ,quantity ){
        variables[parent_id].quantity = quantity;
    }
    function variable_update_quantity( parent_id ,child_id ,quantity ){
        variables[parent_id].list[child_id].quantity_cart = quantity;
    }
    function variable_get_quantity( parent_id ,child_id  ){
        return variables[parent_id].list[child_id].quantity_cart;
    }

    function variable_handler( parent_id  ){
        let first_child_index;
        try {
            first_child_index = Object.keys(variables[parent_id].list)[0];
        }catch ( error ){
            first_child_index = false;
        }
        if ( first_child_index ){
            let first_child = variables[parent_id].list[first_child_index];
            if ( Object.keys( first_child ).length ){

                let variation_side = $(document).find('.za-variation-sidebar');
                variation_side.addClass('open');
                $('.woodmart-close-side').addClass('woodmart-close-side-opened');
                variation_side.find('.za-variation-select-box').data('parent-id' ,parent_id );
                variation_side.find('.za-variation-cart-btn').data( 'variation-id' ,first_child.variation_id );
                variation_side.find('.za-variation-cart-btn').data( 'quantity-cart' ,first_child.quantity_cart );
                variation_side.find('.za-variation-image img').attr('src' ,variables[parent_id].image );
                variation_side.find('.za-variation-title h5').text( variables[parent_id].name );
                variation_side.find('.za-variation-other-amount del').text( '-' );
                variation_side.find('.za-variation-our-amount b').text( '-' );

                setTimeout(function (){
                    for ( const [ key, value] of Object.entries( first_child[Object.keys(first_child)[0]]) ) {
                        let f_key = key.replace('attribute_' , '' );
                        $(document).find('.za-variation-select-box select#'+f_key).val(value);
                    }
                } , 50 )

                generate_add_to_cart_btn( parent_id ,variables[parent_id] )
                $(document).find('.za-variation-select-box').html( generate_select_element( variables[parent_id].translate ) );
            }
        }
    }


    function generate_select_element( translate ){
        let select = '';
        for ( const [ key, value] of Object.entries( translate ) ) {
            select +=
                '<div>' +
                '<label for="'+key+'">'+value.name+'</label>\n' +
                '<select name="'+key+'" id="'+key+'">\n';
            select += '<option value=""> انتخاب کنید </option>';
            for ( const [ k, v ] of Object.entries( value.items ) ) {
                select += '<option value="'+k+'">'+v+'</option>';
            }
            select += '</select> </div>';
        }
        return select;
    }


    function generate_add_to_cart_btn( parent_id ,variable ,empty = false ){
        console.log(variable)
        let btn;
        if ( variable.is_in_stock && variable.is_purchasable && !empty ){
            let stock  = variables[parent_id].stock;
            let factor = variables[parent_id].factor ? variables[parent_id].factor : 1;
            if ( stock >= factor ){
                if ( variable.quantity_cart > 0 ){
                    let minus_selector = variable.quantity_cart === 1 ? trash_icon : minus_icon;
                    btn =
                        `<div class="variable-handler cart-con p-id-${variable.variation_id}" data-product-type="simple-product" data-parent-id="${parent_id}"
                              data-product-id="${variable.variation_id}" data-factor="${factor}" data-stock="${stock}" data-count="${variable.quantity_cart}">
                             <div class="added-cart" >
                                <div class="cart-plus-con" >
                                    <img src="${plus}" alt="plus icon">
                                </div>
                                <div class="cart-count-con" >
                                    <span class="cart-count"> ${variable.quantity_cart}</span>
                                </div>
                                <div class="cart-mines-con" >
                                    <img src="${minus_selector}" alt="minus icon">
                                </div>
                            </div>
                        </div>`;
                }else{
                    btn =
                        `<div class="variable-handler cart-con p-id-${variable.variation_id}" data-product-type="simple-product" data-parent-id="${parent_id}"
                              data-product-id="${variable.variation_id}" data-factor="${factor}" data-stock="${stock}" data-count="${variable.quantity_cart}">
                             <div class="normal-cart" >
                                 <div class="cart-plus-con" >
                                     <img src="${plus_white}" alt="plus white icon">
                                 </div>
                                 <div class="cart-count-con" >
                                     <span class="cart-count">اضافه کردن به سبد</span>
                                 </div>
                                 <div class="cart-mines-con" >
                                     <img src="${minus_icon}" alt="trash icon">
                                 </div>
                             </div>
                        </div>`;
                }
            }

        }else {
            btn =
                '<div class="re-outOfStock">' +
                '    <div>' +
                '        <p>محصولی انتخاب نشده</p>' +
                '    </div>' +
                '</div>';
        }
        $(document).find('.za-variation-cart-btn').html( btn );
    }




    $(document).on('change' , '.za-variation-select-box select' , function () {
        let $this     = $(this);
        let parent_id =  $this.parent().parent().data('parent-id');
        let variable  = variables[parent_id];
        let selects   = [];
        $(document).find('.za-variation-select-box select').each( function( index , node ) {
            if ( $(node).val() ){
                selects.push( $(node).val() )
            }
        });
        let child_product = false;
        let sale_price , reg_price , variation_id , quantity_cart , is_in_stock , is_purchasable = '';
        for ( const [ p_id ,values] of Object.entries( variable.list ) ) {

            const attribute_checker = values['attributes'].filter( value => selects.includes( value ) );
            if ( attribute_checker.length === values['attributes'].length  ){
                child_product  = values;
                sale_price     = values.sale_price;
                reg_price      = values.regular_price;
                variation_id   = p_id;
                quantity_cart  = values.quantity_cart;
                is_in_stock    = values.is_in_stock;
                is_purchasable = values.is_purchasable;
            }
        }

        if ( child_product ){
            $(document).find('.za-variation-other-amount del').text( reg_price );
            $(document).find('.za-variation-our-amount b').text( sale_price);
            $(document).find('.za-variation-cart-btn').data( 'variation-id' ,'' ).addClass('disable');
            $(document).find('.za-variation-cart-btn').data( 'variation-id' ,variation_id ).removeClass('disable');
            generate_add_to_cart_btn( parent_id ,child_product );
        }else{
            $(document).find('.za-variation-other-amount del').text( '' );
            $(document).find('.za-variation-our-amount b').text( '');
            $(document).find('.za-variation-cart-btn').data( 'variation-id' ,'' ).addClass('disable');
            generate_add_to_cart_btn( parent_id ,child_product ,true );
        }
    });





    function start_adding_to_cart_animate(){
        $(document).find('.sabadino-cart-icon svg').addClass('loading-icon-rotate');
    }
    function end_adding_to_cart_animate(){
        $(document).find('.sabadino-cart-icon svg').removeClass('loading-icon-rotate');
    }

    function adding_amount_to_holder( count ,total ){
        $(document).find('span.sabadino-cart-number').text( count );
        $(document).find('.sabadino-cart-subtotal').html(
            '<span class="woocommerce-Price-amount amount"><bdi>'+total+'<span class="woocommerce-Price-currencySymbol">تومان</span></bdi></span>');

    }





    $(document).on('click' , '#za-variation-closer' , function (e) {
        $(this).parent().parent().removeClass('open');
        $('.woodmart-close-side').removeClass('woodmart-close-side-opened');
    });


    $(document).on('click' , '.cart-widget-opener' , function () {
        $(document).find('.cart-widget-side').addClass('widget-cart-opened');
        $('.woodmart-close-side').addClass('woodmart-close-side-opened');
    });

    $(document).on('click' , '.close-side-widget' , function () {
        $(document).find('.cart-widget-side').removeClass('widget-cart-opened');
    });
    $(document).on('click' , '.woodmart-close-side' , function () {
        if ($(document).find('.cart-widget-side').hasClass('widget-cart-opened') ){
            $(document).find('.cart-widget-side').removeClass('widget-cart-opened')
        }
        if ($(document).find('.za-variation-sidebar').hasClass('open') ){
            $(document).find('.za-variation-sidebar').removeClass('open')
        }

    });




    $(document).on('click', '.plus, .minus', function () {
        // Get values
        var $qty = $(this).closest('.quantity').find('.qty'),
            currentVal = parseFloat($qty.val()),
            max = parseFloat($qty.attr('max')),
            min = parseFloat($qty.attr('min')),
            step = $qty.attr('step');

        // Format values
        if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
        if (max === '' || max === 'NaN') max = '';
        if (min === '' || min === 'NaN') min = 0;
        if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = '1';

        // Change the value
        if ($(this).is('.plus')) {
            if (max && (currentVal >= max)) {
                $qty.val(max);
            } else {
                $qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
            }
        } else {
            if (min && (currentVal <= min)) {
                $qty.val(min);
            } else if (currentVal > 0) {
                $qty.val((currentVal - parseFloat(step)).toFixed(step.getDecimals()));
            }
        }

        // Trigger change event
        $qty.trigger('change');
    });


    if (!String.prototype.getDecimals) {
        String.prototype.getDecimals = function () {
            var num = this,
                match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            if (!match) {
                return 0;
            }
            return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
        }
    }


    $(document).on('click', '.widget_shopping_cart .remove_from_cart_button', function (e) {
        e.preventDefault();
        let $this = $(this);
        $this.parent().addClass('removing-process');
        setTimeout(function (){
            $.ajax({
                url: re_cart_ob.admin_url,
                method: 'GET',
                dataType: 'json',
                cache:false ,
                data: {
                    'action': 're_refresh_cart_icon'
                },
                success:function (e) {

                    adding_amount_to_holder( e.count ,e.total );

                }
            });
        } , 2000 )

    });





})(jQuery);

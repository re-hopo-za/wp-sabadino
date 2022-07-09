
jQuery(function ($){

    const ajax_url = sabadino_main_object.ajax_url;
    const search_loader = '<svg width="35" height="35" style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
                                '<path d="M10 50A40 40 0 0 0 90 50A40 44.9 0 0 1 10 50" fill="#fb3030" stroke="none">\n' +
                                     '  <animateTransform attributeName="transform" type="rotate" dur="0.12033694344163658s" repeatCount="indefinite" keyTimes="0;1" values="0 50 52.45;360 50 52.45"></animateTransform>\n' +
                                '</path>\n' +
                            ' </svg>';
    const search_icon ='<svg  xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="25" height="25"\n' +
        '                         viewBox="0 0 512 512"  xml:space="preserve">\n' +
        '                        <g>\n' +
        '                            <g>\n' +
        '                                <path d="M225.474,0C101.151,0,0,101.151,0,225.474c0,124.33,101.151,225.474,225.474,225.474\n' +
        '                                    c124.33,0,225.474-101.144,225.474-225.474C450.948,101.151,349.804,0,225.474,0z M225.474,409.323\n' +
        '                                    c-101.373,0-183.848-82.475-183.848-183.848S124.101,41.626,225.474,41.626s183.848,82.475,183.848,183.848\n' +
        '                                    S326.847,409.323,225.474,409.323z"/>\n' +
        '                            </g>\n' +
        '                        </g>\n' +
        '                            <g>\n' +
        '                                <g>\n' +
        '                                    <path d="M505.902,476.472L386.574,357.144c-8.131-8.131-21.299-8.131-29.43,0c-8.131,8.124-8.131,21.306,0,29.43l119.328,119.328\n' +
        '                                c4.065,4.065,9.387,6.098,14.715,6.098c5.321,0,10.649-2.033,14.715-6.098C514.033,497.778,514.033,484.596,505.902,476.472z"/>\n' +
        '                                </g>\n' +
        '                            </g>\n' +
        '                     </svg>';

    function navChild() {

        var body = $("body"),
            mobileNav = $(".mobile-nav"),
            dropDownCat = $(".mobile-nav .site-mobile-menu .menu-item-has-children"),
            elementIcon = '<span class="icon-sub-menu"></span>';


        var closeSide = $(document).find('.woodmart-close-side');

        dropDownCat.append(elementIcon);

        mobileNav.on("click", ".icon-sub-menu", function (e) {
            e.preventDefault();

            if ($(this).parent().hasClass("opener-page")) {
                $(this).parent().removeClass("opener-page").find("> ul").slideUp(200);
                $(this).parent().removeClass("opener-page").find(".sub-menu-dropdown .container > ul, .sub-menu-dropdown > ul").slideUp(200);
                $(this).parent().find('> .icon-sub-menu').removeClass("up-icon");
            } else {
                $(this).parent().addClass("opener-page").find("> ul").slideDown(200);
                $(this).parent().addClass("opener-page").find(".sub-menu-dropdown .container > ul, .sub-menu-dropdown > ul").slideDown(200);
                $(this).parent().find('> .icon-sub-menu').addClass("up-icon");
            }
        });

        mobileNav.on('click', '.mobile-nav-tabs li', function () {
            if ($(this).hasClass('active')) return;
            var menuName = $(this).data('menu');
            $(this).parent().find('.active').removeClass('active');
            $(this).addClass('active');
            $('.mobile-menu-tab').removeClass('active');
            $('.mobile-' + menuName + '-menu').addClass('active');
        });

        body.on("click", ".mobile-nav-icon > a", function (e) {
            e.preventDefault();

            if (mobileNav.hasClass("act-mobile-menu")) {
                closeMenu();
            } else {
                openMenu();
            }

        });

        body.on("click touchstart", ".woodmart-close-side", function () {
            closeMenu();
        });

        body.on('click', '.mobile-nav .login-side-opener', function () {
            closeMenu();
        });

        function openMenu() {
            mobileNav.addClass("act-mobile-menu");
            closeSide.addClass('woodmart-close-side-opened');
        }

        function closeMenu() {
            mobileNav.removeClass("act-mobile-menu");
            closeSide.removeClass('woodmart-close-side-opened');
        }
    }
     navChild();

    let toggle_menu = $(document).find('.main-category-in-top .elementor-menu-toggle');
    toggle_menu.removeClass('elementor-menu-toggle');
    toggle_menu.addClass('elementor-menu-toggle-sab');

    $(document).on('click' , '.elementor-menu-toggle-sab' ,function ( ){
        $(document).find('.slide-from-left').addClass('act-mobile-menu');
        $('.woodmart-close-side').addClass('woodmart-close-side-opened');
    });





    //live search
    let search_element = $(document).find('.za-live-search');
    search_element.keypress(function(e) {
        updateSearchResult( $(this) );
    });
    search_element.keydown( function(e) {
        if (e.keyCode === 8 || e.keyCode === 46) {
            updateSearchResult( $(this) );
        }
    });
    search_element.bind('paste', function(e) {
        updateSearchResult( $(this) );
    });

    function updateSearchResult( $this ){
        if ( $this.val().length > 1 ){
            $(document).find('.za-live-search-main div').html( search_loader );
            $.ajax({
                url: ajax_url,
                method: 'POST',
                data: {
                    'action'  : 'sabadino_search_live',
                    'keyword' : $this.val()
                },
                success:function (e){
                    $(document).find('.close-search-con').show();
                    $(document).find('.za-live-search-main div').html( search_icon );
                    $(document).find('.za-live-search-result').show().children('ul').html( e );
                }
            });
        }
    }
    $(document).on("click", ".close-search-con", function (e) {
        $(this).hide();
        $(document).find('.za-live-search-result').hide();
    });
    //live search end





})



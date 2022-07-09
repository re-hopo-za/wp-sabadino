<?php


namespace Sabadino\features\woocommerce;

use Walker_Nav_Menu;



class SABADINO_Mega_Menu_Walker extends Walker_Nav_Menu {

    private $color_scheme = 'light';
    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    public function __construct() {

        register_sidebar(
            [
                'name'          => esc_html__( 'Area after the mobile menu', 'woocommerce' ),
                'id'            => 'mobile-menu-widgets',
                'description'   => esc_html__( 'Place your widgets that will be displayed after the mobile menu links', 'woocommerce' ),
                'class'         => '',
                'before_widget' => '<div id="%1$s" class="woodmart-widget widget mobile-menu-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h5 class="widget-title">',
                'after_title'   => '</h5>'
            ]
        );
    }

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);

        if( $depth == 0) {
            $output .= "\n$indent<div class=\"sub-menu-dropdown color-scheme-" . $this->color_scheme . "\">\n";
            $output .= "\n$indent<div class=\"container\">\n";

        }
        if( $depth < 1 ) {
            $sub_menu_class = "sub-menu";
        } else {
            $sub_menu_class = "sub-sub-menu";
        }

        $output .= "\n$indent<ul class=\"$sub_menu_class color-scheme-" . $this->color_scheme . "\">\n";

        $this->color_scheme = 'light' ;
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
        if( $depth == 0) {
            $output .= "$indent</div>\n";
            $output .= "$indent</div>\n";
        }
    }

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $classes[] = 'item-level-' . $depth;

        $design = $width = $height = $icon = $label = $label_out = '';
        $design  = get_post_meta( $item->ID, '_menu_item_design',  true );
        $width   = get_post_meta( $item->ID, '_menu_item_width',   true );
        $height  = get_post_meta( $item->ID, '_menu_item_height',  true );
        $icon    = get_post_meta( $item->ID, '_menu_item_icon',    true );
        $event   = get_post_meta( $item->ID, '_menu_item_event',   true );
        $label   = get_post_meta( $item->ID, '_menu_item_label',   true );
        $label_text = get_post_meta( $item->ID, '_menu_item_label-text',   true );
        $block   = get_post_meta( $item->ID, '_menu_item_block',   true );
        $dropdown_ajax = get_post_meta( $item->ID, '_menu_item_dropdown-ajax',  true );
        $opanchor = get_post_meta( $item->ID, '_menu_item_opanchor', true );
        $callbtn  = get_post_meta( $item->ID, '_menu_item_callbtn', true );
        $color_scheme = get_post_meta( $item->ID, '_menu_item_colorscheme', true );

        if ( $color_scheme == 'light' ) {
            $this->color_scheme = 'light';
        }elseif( $color_scheme == 'dark' ){
            $this->color_scheme = 'dark';
        }

        if( empty($design) ) $design = 'default';

        if ( ! is_object( $args ) ) return;

        if( $depth == 0 && $args->menu_class != 'site-mobile-menu' ) {
            $classes[] = 'menu-item-design-' . $design;
            $classes[] = 'menu-' . ( (  in_array( $design, array( 'sized', 'full-width' ) ) ) ? 'mega-dropdown' : 'simple-dropdown' );
            $event = (empty($event)) ? 'hover' : $event;
            $classes[] = 'item-event-' . $event;
        }

        if ( $block && $args->menu_class == 'site-mobile-menu' ) {
            $classes[] = 'menu-item-has-block';
        }

        if( $opanchor == 'enable' ) {
            $classes[] = 'onepage-link';
            if(($key = array_search('current-menu-item', $classes)) !== false) {
                unset($classes[$key]);
            }
        }

        if( $callbtn == 'enable' ) {
            $classes[] = 'callto-btn';
        }

        if( !empty( $label ) ) {
            $classes[] = 'item-with-label';
            $classes[] = 'item-label-' . $label;
            $label_out = '<span class="menu-label menu-label-' . $label . '">' . esc_attr( $label_text ) . '</span>';
        }

        if( ! empty( $block ) && $design != 'default' ) {
            $classes[] = 'menu-item-has-children';
        }

        if( $dropdown_ajax == 'yes') {
            $classes[] = 'dropdown-load-ajax';
        }

        if ( $height ) {
            $classes[] = 'dropdown-with-height';
        }

        /**
         * Filter the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filter the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        /**
         * Filter the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param object $item  The current menu item.
         * @param array  $args  An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
        $atts['class'] = 'woodmart-nav-link';

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $icon_url = '';

        if( $item->object == 'product_cat' ) {
            $icon_url = get_term_meta( $item->object_id, 'category_icon_alt', true );
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        if($icon != '') {
            $item_output .= '<i class="fa fa-' . $icon . '"></i>';
        }

        $icon_attrs = apply_filters( 'woodmart_megamenu_icon_attrs', false );

        if( ! empty( $icon_url ) ) {
            $item_output .= '<img src="'  . esc_url( $icon_url ) . '" alt="' . esc_attr( $item->title ) . '" ' . $icon_attrs . ' class="category-icon" />';
        }
        /** This filter is documented in wp-includes/post-template.php */
        $item_output .= '<span class="nav-link-text">' . $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after . '</span>';
        $item_output .= $label_out;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $styles = '';

        if( $depth == 0 && $args->menu_class != 'site-mobile-menu' ) {
            /**
             * Add background image to dropdown
             **/


            if( has_post_thumbnail( $item->ID ) ) {
                $post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'full' );

                //ar($post_thumbnail);

                $styles .= '.menu-item-' . $item->ID . ' > .sub-menu-dropdown {';
                $styles .= 'background-image: url(' . $post_thumbnail[0] .'); ';
                $styles .= '}';
            }

            if( ! empty( $block ) && !in_array("menu-item-has-children", $item->classes) && $design != 'default' ) {
                $item_output .= "\n$indent<div class=\"sub-menu-dropdown color-scheme-" . $this->color_scheme . "\">\n";
                $item_output .= "\n$indent<div class=\"container\">\n";
                if( $dropdown_ajax == 'yes') {
                    $item_output .= '<div class="dropdown-html-placeholder" data-id="' . $block . '"></div>';
                } else {
                    $item_output .= woodmart_html_block_shortcode( array( 'id' => $block ) );
                }
                $item_output .= "\n$indent</div>\n";
                $item_output .= "\n$indent</div>\n";

                if( $this->color_scheme == 'light' || $this->color_scheme == 'dark' ) $this->color_scheme = whb_get_dropdowns_color() ;
            }
        }

        if($design == 'sized' && !empty($height) && !empty($width) && $args->menu_class != 'site-mobile-menu' ) {
            $styles .= '.menu-item-' . $item->ID . '.menu-item-design-sized > .sub-menu-dropdown {';
            $styles .= 'min-height: ' . $height .'px; ';
            $styles .= 'width: ' . $width .'px; ';
            $styles .= '}';
        }

        if( $styles != '' && $args->menu_class != 'site-mobile-menu' ) {
            $item_output .= '<style>';
            $item_output .= $styles;
            $item_output .= '</style>';
        }

        /**
         * Filter a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param object $item        Menu item data object.
         * @param int    $depth       Depth of menu item. Used for padding.
         * @param array  $args        An array of {@see wp_nav_menu()} arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

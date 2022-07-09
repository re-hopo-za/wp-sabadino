<?php


namespace Sabadino\features\product_slider\widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use Sabadino\features\product_slider\Products;
use Sabadino\features\product_slider\templates\Templates;



defined( 'ABSPATH' ) || die();

/**
 * Product Slider widget class.
 *
 * @since 1.0.0
 */
class ProductSliderWidget extends Widget_Base {


    /*
     * Class constructor.
     *
     * @param array $data Widget data.
     * @param array $args Widget arguments.
     */
    public function __construct( $data = array(), $args = null ) {
        parent::__construct( $data, $args );
//        wp_register_style( 'awesomesauce', plugins_url( '/assets/css/awesomesauce.css', ELEMENTOR_AWESOMESAUCE ), array(), '1.0.0' );

    }


    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var ProductSliderWidget The single instance of the class.
     */

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'products-slider';
    }


    /**
     * Retrieve the widget category.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_categories() {
        return ['za-product-slider'];
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Products Slider', 'elementor-sabadino' );
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'fa fa-sliders';
    }


    /**
     * Enqueue styles.
     */
    public function get_style_depends() {
        return [];
//        return array( 'awesomesauce' );
    }

    /**
     * Retrieve the widget category.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    protected function _register_controls() {


        $this->start_controls_section(
            'query_configuration',
            [
                'label' => esc_html__( 'Query Builder', 'sabadino-textdomain' )  ,
                'tab' => Controls_Manager::TAB_CONTENT
            ]
        );

        $this->add_control(
            'product_kinds',
            [
                'label' => esc_html__( 'Product Kinds', 'sabadino-textdomain' ),
                'placeholder' => esc_html__( 'Choose Products Kinds', 'sabadino-textdomain' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => false,
                'default' => '',
                'options' => Products::getProductsKind()
            ]
        );


        $this->add_control(
            'product_cats',
            [
                'label' => esc_html__( 'Categories', 'sabadino-textdomain' ),
                'placeholder' => esc_html__( 'Choose Categories to Include', 'sabadino-textdomain' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => '',
                'options' => Products::get_instance()::getProductsCats()
            ]
        );


        $this->add_control(
            'product_tags',
            [
                'label' => esc_html__( 'Tags', 'sabadino-textdomain' ),
                'placeholder' => esc_html__( 'Choose Tags to Include', 'sabadino-textdomain' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => '',
                'options' => Products::get_instance()::getProductsTags()
            ]
        );


        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__( 'Limit', 'sabadino-textdomain' ),
                'placeholder' => esc_html__( 'Default is 10', 'sabadino-textdomain' ),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'default' => 10,
            ]
        );



        $this->end_controls_section();

        $this->start_controls_section(
            'item_configuration',
            [
                'label' => esc_html( 'Item Configurtion' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'display_image',
            [
                'label' => esc_html__( 'Show Image', 'sabadino-textdomain' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'sabadino-textdomain' ),
                'label_off' => esc_html__( 'No', 'sabadino-textdomain' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail_size',
                'default' => 'medium_large',
                'condition' => [
                    'display_image'	=>	'yes',
                ]
            ]
        );
        $this->end_controls_section();

    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function render() {


        $settings   = $this->get_settings_for_display();

        $element_id = 'wb_wpce_'.$this->get_id();
        $products   = Products::products( $settings );
        ?>
 
        <div class="za-carousel za-products-wraper "
             id="<?php echo $element_id ?>" <?php echo $this->getElementAttributes($settings) ?>
        >
        <?php echo Templates::templateOne( $products , $settings ); ?>
        </div>
        <?php

    }


    public function getElementAttributes($settings)
    {

        $slide_to_show    = isset($settings['slide_to_show']) && $settings['slide_to_show'] ? $settings['slide_to_show'] : 3;
        $slides_to_scroll = isset($settings['slides_to_scroll']) && $settings['slides_to_scroll'] ? $settings['slides_to_scroll'] : 3;
        $display_rating   = isset($settings['display_rating']) && $settings['display_rating'] ? $settings['display_rating'] : 'no';
        $display_price    = isset($settings['display_price']) && $settings['display_price'] ? $settings['display_price'] : 'no';

        return '
        data-slide-to-show="'.$slide_to_show.'"
        data-slides-to-scroll="'.$slides_to_scroll.'"
        data-display-rating="'.$display_rating.'"
        data-display-price="'.$display_price.'"
        ';
    }





}

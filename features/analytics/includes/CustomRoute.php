<?php


namespace Sabadino\features\analytics\includes;

class CustomRoute
{
    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {
        add_filter('theme_page_templates', array($this, 'add_template'), 10, 4);
        add_filter('template_include', array($this, 'load_template'));
        add_action('init', array($this, 'add_rewrite'), 9);
    }


    public function add_template( $post_templates, $wp_theme, $post, $post_type )
    {
        $post_templates['sabadino-reporter.php'] = 'Sabadino Reporter';
        return $post_templates;
    }


    public function load_template($template)
    {
        if (get_page_template_slug() === 'sabadino-reporter.php') {
            if ($theme_file = locate_template( array('sabadino-reporter.php') ) ) {
                $template = $theme_file;
            } else {
                $template = ZA_ANALYTICS_ROOT . 'pages/analytics-template.php';
            }

        }
        return $template;
    }


    public function add_rewrite()
    {
        add_rewrite_rule('sabadino-reporter/([0-9]*)', 'index.php/sabadino-reporter?sabadino-reporter=$1');
    }


}



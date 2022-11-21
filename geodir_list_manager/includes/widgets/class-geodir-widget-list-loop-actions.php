<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * GeoDirectory Search widget.
 *
 * @since 1.0.0
 */
class GeoDir_Widget_List_Loop_Actions extends WP_Super_Duper {

    /**
     * Register the advanced search widget with WordPress.
     *
     */
    public function __construct() {


        $options = array(
            'textdomain'    => GEODIRECTORY_TEXTDOMAIN,
            'block-icon'    => 'admin-site',
            'block-category'=> 'widgets',
            'block-keywords'=> "['list loop','lists','geodir']",
            'class_name'    => __CLASS__,
            'base_id'       => 'gd_list_loop_actions', // this us used as the widget id and the shortcode id.
            'name'          => __('GD > List Loop Actions','gd-lists'), // the name of the widget.
            'widget_ops'    => array(
                'classname'   => 'geodir-list-loop-actions-container', // widget class
                'description' => esc_html__('Shows the actions available to the user on a list page, like the author actions like edit and delete list.','gd-lists'), // widget description
                'geodirectory' => true,
            ),
//            'arguments'     => array(
//                'layout'  => array(
//                    'title' => __('Layout:','gd-lists'),
//                    'desc' => __('How the listings should laid out by default.','gd-lists'),
//                    'type' => 'select',
//                    'options'   =>  array(
//                        "2"        =>  __('Grid View (Two Columns)','gd-lists'),
//                        "3"        =>  __('Grid View (Three Columns)','gd-lists'),
//                        "4"        =>  __('Grid View (Four Columns)','gd-lists'),
//                        "5"        =>  __('Grid View (Five Columns)','gd-lists'),
//                        "0"        =>  __('List view','gd-lists'),
//                    ),
//                    'default'  => 'h3',
//                    'desc_tip' => true,
//                    'advanced' => true
//                )
//            )
        );


        parent::__construct( $options );
    }

    /**
     * The Super block output function.
     *
     * @param array $args
     * @param array $widget_args
     * @param string $content
     *
     * @return mixed|string|void
     */
    public function output($args = array(), $widget_args = array(),$content = ''){
        global $post;

        ob_start();
        
        if(is_single() && isset($post->post_type) && $post->post_type=='gd_list'){

            do_action('geodir_lists_before_loop_actions');
            do_action('geodir_lists_loop_actions');
            do_action('geodir_lists_after_loop_actions');

        }

        return ob_get_clean();
    }

}
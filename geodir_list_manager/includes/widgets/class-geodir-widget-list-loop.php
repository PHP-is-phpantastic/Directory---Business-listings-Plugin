<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * GeoDirectory Search widget.
 *
 * @since 1.0.0
 */
class GeoDir_Widget_List_Loop extends WP_Super_Duper {

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
            'base_id'       => 'gd_list_loop', // this us used as the widget id and the shortcode id.
            'name'          => __('GD > List Loop','gd-lists'), // the name of the widget.
            'widget_ops'    => array(
                'classname'   => 'geodir-list-loop-container', // widget class
                'description' => esc_html__('Shows the current posts saved to a list.','gd-lists'), // widget description
                'geodirectory' => true,
            ),
            'arguments'     => array(
                'layout'  => array(
                    'title' => __('Layout:','gd-lists'),
                    'desc' => __('How the listings should laid out by default.','gd-lists'),
                    'type' => 'select',
                    'options'   =>  array(
                        "2"        =>  __('Grid View (Two Columns)','gd-lists'),
                        "3"        =>  __('Grid View (Three Columns)','gd-lists'),
                        "4"        =>  __('Grid View (Four Columns)','gd-lists'),
                        "5"        =>  __('Grid View (Five Columns)','gd-lists'),
                        "0"        =>  __('List view','gd-lists'),
                    ),
                    'default'  => 'h3',
                    'desc_tip' => true,
                    'advanced' => true
                )
            )
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
        global $wp_query, $gd_layout_class,$post;

        ob_start();
        if(is_single() && isset($post->post_type) && $post->post_type=='gd_list'){
            
            // check if we have listings
            $data = new GeoDir_Lists_Data();
            $posts = $data->get_posts();
            
            
            
            $widget_args = wp_parse_args( $args, array(
                'layout' => ''
            ) );

			$gd_layout_class = geodir_convert_listing_view_class( $widget_args['layout'] );

            // check if we have listings or if we are faking it
            if($wp_query->post_count == 1 && empty($wp_query->posts)){
                geodir_no_listings_found();
            }elseif(geodir_is_page('search') && !isset($_REQUEST['geodir_search'])){
                geodir_no_listings_found();
            }else{

                // check we are not inside a template builder container
                if(isset($wp_query->posts[0]) && $wp_query->posts[0]->post_type=='page'){
                    // reset the query count so the correct number of listings are output.
                    rewind_posts();
                    // reset the proper loop content
                    global $wp_query,$gd_temp_wp_query;
                    $wp_query->posts = $gd_temp_wp_query;
                }

                global $geodir_is_widget_listing;
                if ( isset( $post ) ) {
                    $reset_post = $post;
                }
                if ( isset( $gd_post ) ) {
                    $reset_gd_post = $gd_post;
                }
                $geodir_is_widget_listing = true;

                geodir_get_template( 'content-widget-listing.php', array( 'widget_listings' => $posts ) );

                $geodir_is_widget_listing = false;

                if ( isset( $reset_post ) ) {
                    if ( ! empty( $reset_post ) ) {
                        setup_postdata( $reset_post );
                    }
                    $post = $reset_post;
                }
                if ( isset( $reset_gd_post ) ) {
                    $gd_post = $reset_gd_post;
                }
            }
        }else{
            _e("This list is empty at the moment, check back later.", "gd-lists");
        }

        // add filter to make main page comments closed after the GD loop
        //add_filter( 'comments_open', array(__CLASS__,'comments_open'),10,2);

        return ob_get_clean();
    }

    /**
     * Filter to close the comments for archive pages after the GD loop.
     * 
     * @param $open
     * @param $post_id
     *
     * @return bool
     */
    public static function comments_open($open, $post_id){

        global $post;
        if(isset($post->ID) && $post->ID==$post_id){
            $open = false;
        }

        return $open;
    }

}
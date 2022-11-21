<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since 2.0.0
 *
 * @package    GeoDir_Lists
 * @subpackage GeoDir_Lists/admin
 *
 * Class GeoDir_Lists_Admin
 */
class GeoDir_Lists_Admin {

    /**
     * Constructor.
     *
     * @since 2.0.0
     *
     * GeoDir_Lists_Admin constructor.
     */
    public function __construct() {

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );
        add_action( 'p2p_init', array( $this,'list_p2p_connection' ) );
        add_action( 'widgets_init', array( $this,'register_list_widgets') );
//        add_filter( 'geodir_page_options', array( $this,'add_page_option')); @todo change this to the list view template

        add_filter( 'geodirectory_screen_ids', array( $this,'screen_ids'));
        add_action( 'geodir_clear_version_numbers' ,array( $this, 'clear_version_number'));

        
    }

    /**
     * Deletes the version number from the DB so install functions will run again.
     */
    public function clear_version_number(){
        delete_option( 'geodir_lists_version' );
    }
    
    /**
     * Set the GD list pages as a geodirectory page so the correct files are loaded.
     *
     * @param $screen_ids
     *
     * @return array
     */
    public function screen_ids($screen_ids){

        $post_type = 'gd_list';

        $screen_ids[] = $post_type . '_page_'.$post_type.'-settings'; // CPT settings page

        return $screen_ids;
    }


    

    /**
     * Register and enqueue list manager styles and scripts.
     *
     * @since 2.0.0
     */
    public function enqueue_styles_and_scripts(){

        wp_register_script( 'list-manager-admin-script', GD_LISTS_PLUGIN_URL . 'assets/js/geodir_list_manager_admin.js', array( 'jquery' ), '2.0.0', true );
        wp_enqueue_script( 'list-manager-admin-script' );

        wp_register_style('list-manager-admin-style', GD_LISTS_PLUGIN_URL . 'assets/css/geodir_list_manager_admin.css', array(), '2.0.0');
        wp_enqueue_style('list-manager-admin-style' );

    }

    

    /**
     * Connection to gd_list to custom gd post types.
     *
     * @since 2.0.0
     */
    public function list_p2p_connection() {

        $all_postypes = geodir_get_posttypes();

        if (!$all_postypes) {
            $all_postypes = array('gd_place');
        }
        foreach ($all_postypes as $pt) {
            p2p_register_connection_type(
                array(
                    'name'  => $pt.'_to_gd_list',
                    'from'  => $pt,
                    'to'    => 'gd_list',
                    'sortable' => 'to',
                    'admin_box' => array(
                        'show' => 'to',
                        'context' => 'side'
                    )
                )
            );
        }
    }

    /**
     * Register gd list manager widgets.
     *
     * @since 2.0.0
     */
    public function register_list_widgets() {

        register_widget( 'GeoDir_Widget_List_Save' );
        register_widget( 'GeoDir_Widget_List_Loop' );
        register_widget( 'GeoDir_Widget_List_Loop_Actions' );

//        new GeoDir_Widget_List_Add();
//        new GeoDir_Widget_List_Save();
//        new GeoDir_Widget_Add_List();
//        new GeoDir_Widget_List_Loop();

    }

    public function add_page_option( $pages ) {

        $pages[] = array(
            'title' => __( 'Lists Page Settings', 'gd-lists' ),
            'type'  => 'title',
            'desc'  => __('List page settings for set add list page.','gd-lists'),
            'id'    => 'page_lists_options',
            'desc_tip' => true,
        );

        $pages[] = array(
            'name'     => __( 'Add List Page', 'gd-lists' ),
            'desc'     => __( 'Select the page to use for add list', 'gd-lists' ),
            'id'       => 'geodir_add_list_page',
            'type'     => 'single_select_page',
            'class'      => 'geodir-select',
            'desc_tip' => true,
        );

        $pages[] = array( 'type' => 'sectionend', 'id' => 'page_options' );

        return $pages;

    }

}
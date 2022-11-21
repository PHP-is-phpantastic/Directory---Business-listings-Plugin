<?php

// Check GD_List_Userswp class exists or not.
if( ! class_exists( 'GD_List_Userswp' ) ) {

    /**
     * GD_List_Userswp Class for Userswp actions.
     *
     * @since 2.0.0
     *
     * Class GD_List_Userwp
     */
    class GD_List_Userswp{

        /**
         * Constructor.
         *
         * @since 2.0.0
         *
         * GD_List_Userswp constructor.
         */
        public function __construct() {

            add_filter( 'uwp_available_tab_items', array($this,'add_list_tab_items'), 10, 1);
            add_filter( 'uwp_profile_tabs', array( $this, 'profile_list_tabs' ), 10, 3 );
            add_action( 'uwp_profile_lists_tab_content', array( $this, 'list_tab_content' ), 10, 1 );

        }

        /**
         * Add new list tab items on UsersWp.
         *
         * @since 2.0.0
         *
         * @param array  $tabs Get users tabs items.
         *
         * @return array $tabs.
         */
        public function add_list_tab_items( $tabs ){

            $tabs['lists'] = geodir_lists_name_plural();

            return $tabs;
        }

        /**
         * Add list tab on user profile page.
         *
         * @since 2.0.0
         *
         * @param array $tabs Tab lists.
         * @param object $user Get user object.
         * @param array $allowed_tabs Get Allowed tabs.
         *
         * @return mixed
         */
        public function profile_list_tabs( $tabs, $user, $allowed_tabs ) {

            $count = $this->user_lists_count($user->ID);
            if ( in_array( 'lists', $allowed_tabs ) && $count > 0 ) {
                $tabs['lists'] = array(
                    'title' => geodir_lists_name_plural(),
                    'count' => $count
                );
            }

            return $tabs;

        }

        /**
         * Get list tab content.
         *
         * @since 2.0.0
         *
         * @param object $user Get user object.
         */
        public function list_tab_content( $user ) {

            if ( empty( $user->ID ) ) {
                return;
            }

            $this->users_lists( $user->ID );

        }

        /**
         * Get user lists count.
         *
         * @since 2.0.0
         *
         * @param int $user_id Get user id.
         *
         * @return string
         */
        public function user_lists_count( $user_id ) {
            return count_user_posts( $user_id , 'gd_list' );
        }

        /**
         * Get User lists.
         *
         * @since 2.0.0
         *
         * @param int $user_id Get user id.
         */
        public function users_lists( $user_id ) {

            if ( get_current_user_id() == $user_id ) {
                $title = sprintf( __( "My %s","gd-lists" ), geodir_lists_name_plural() );
            } else {
                $title = wp_sprintf( __( "%s's %s", 'gd-lists' ), get_the_author_meta( 'display_name', $user_id ),geodir_lists_name_plural()  );
            }
            ?>
            <div class="users-gd-lists">
                <div class="users-gd-list-header"><h3><?php echo $title; ?></h3></div>
                <ul class="uwp-profile-item-ul">
                    <?php

                    $get_list_results = GeoDir_Lists_Data::get_user_lists($user_id);

                    if( !empty( $get_list_results ) && $get_list_results !='' ) {

                        foreach ( $get_list_results as $list_key => $list_value ) {
                            $maybe_private = '';
                            if($list_value->post_status=='private'){
                                $maybe_private = '<i class="fas fa-user-secret" title="'.sprintf( __( "Non-public %s (you can still share a direct link with your friends)", 'gd-lists' ), geodir_lists_name_singular() ).'"></i> ';
                            }

                            $list_content = ! empty( $list_value->post_content ) ? wp_trim_excerpt($list_value->post_content) : '';

                            ?>
                            <li class="uwp-profile-item-li uwp-profile-item-clearfix gd-list-item-wrap">
                                <h3><?php echo $maybe_private;?><a href="<?php echo get_permalink( $list_value->ID ); ?>"><?php echo __($list_value->post_title,'gd-lists'); ?></a></h3>
                                <div class="list_description"><?php echo $list_content; ?></div>
                                <div class="uwp-item-actions"><?php do_action( 'geodir_single_list_actions', $list_value->ID, $user_id ); ?></div>
                            </li>
                            <?php
                        }

                    } else{ ?>
                        <li class="no-users-lists"><?php _e('No list(s) founds','gd-lists'); ?></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }

    }

    new GD_List_Userswp();
}
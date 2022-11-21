<?php

// Check GeoDir_Lists_Forms class exists or not.
if ( ! class_exists( 'GeoDir_Lists_Forms' ) ) {

	/**
	 * GeoDir_Lists_Formss Class for the CPT actions.
	 *
	 * @since 2.0.0
	 *
	 * Class GeoDir_Lists_Forms
	 */
	class GeoDir_Lists_Forms {

		/**
		 * Constructor.
		 *
		 * @since 2.0.0
		 *
		 * GeoDir_Lists_Forms constructor.
		 */
		public function __construct() {

			// add_filter( 'geodir_loop_active', array( $this, 'maybe_loop' ) );

		}

		public static function edit_dialog( $post_id = '' ) {
			$user_id = get_current_user_id();
			$post = get_post($post_id);
			$content = '';
			if ( $user_id && isset($post->post_author) && $user_id==$post->post_author ) {

				$content .= "<h2>" . sprintf( __( "Edit %s",'gd-lists' ), geodir_lists_name_singular() ) . "</h2>";
				$content .= "<form>";

				// list name
				$content .= "<label for='gd-list-name'>" . __( "Name",'gd-lists' ) . "</label>";
				$content .= '<input id="gd-list-name" name="list_name" type="text" value="'.esc_attr($post->post_title).'">';

				// list description
				$content .= "<label for='gd-list-description'>" . __( "Description",'gd-lists' ) . "</label>";
				$content .= '<textarea id="gd-list-description" name="list_description" class="gd-ios-scrollbars">'.esc_textarea($post->post_content).'</textarea>';

				// public or non-public
				$content .= '<label><input type="radio" name="is_public" value="1" '.checked('publish',$post->post_status,false).'><span>'. __( "Public",'gd-lists' ) .'</span> </label>';
				$content .= '<label><input type="radio" name="is_public" value="0" '.checked('private',$post->post_status,false).'><span>'. __( "Non-Public",'gd-lists' ) .'</span> </label>';

				// description
				$content .= '<p>'. sprintf( __( "A public %s is displayed on the site and can be viewed by anyone. A non-public %s can still be visible to others if you share a link to it.", 'gd-lists' ), geodir_lists_name_singular(), geodir_lists_name_singular() ) .'</p>';

				$content .= "<p>" . self::save_list_button( 0,$post_id ) . "</p>"; // live inside form so we can use JS: this

				//$content .= print_r($user_lists, true);
				$content .= "</form>";


			}

			return $content;
		}

		public static function save_dialog( $post_id = '' ) {
			$user_id = get_current_user_id();
			$content = '';
			if ( $user_id ) {

				$user_lists = GeoDir_Lists_Data::get_user_lists( $user_id );

				$content .= "<h2>" . sprintf( __( "Save to %s", 'gd-lists' ), geodir_lists_name_singular() ) . "</h2>";
				$content .= "<form>";

				if ( empty( $user_lists ) ) {
					$content .= "<p>" . sprintf( __( "You don't have any %s yet.","gd-lists" ), geodir_lists_name_plural() ) . "</p>";
				} else {
					$content .= "<div class='gd-list-select-container'>";
					$content .= "<ul class='gd-list-select-container-list gd-ios-scrollbars'>";

					foreach ( $user_lists as $list ) {
						$image = GeoDir_Lists_Data::get_list_image( $list->ID, 'thumbnail' );
						$post_title = wp_strip_all_tags($list->post_title);
						$maybe_private = '';
						// maybe private? <i class="fas fa-user-secret"></i>
						if($list->post_status=='private'){
							$maybe_private = '<i class="fas fa-user-secret" title="'.sprintf( __( "Non-public %s (you can still share a direct link with your friends)", 'gd-lists' ), geodir_lists_name_singular() ).'"></i> ';
						}
						$post_link = get_permalink($list->ID);

						$action_link = self::get_list_save_button($list->ID,$post_id);

						$content .= "<li>";
						$content .= "<div class='gd-list-wrap'>";
						$content .= <<<HTML
	<span class='gd-list-image'>
		$image
  	</span> 
  	<span class='gd-list-info'>
		$maybe_private<a href="$post_link">$post_title</a>
  	</span> 
  	<span class='gd-list-actions'>
		$action_link
  	</span> 
HTML;
						$content .= "</div>";
						$content .= "</li>";
					}

					$content .= "</ul>";
					$content .= "</div>";


				}

				//$content .= print_r($user_lists, true);
				$content .= "</form>";

				$content .= "<p>" . self::create_new_list_button( $post_id ) . "</p>";

			} else {
				$content .= self::login_options( $post_id );
			}

			return $content;
		}



		public static function new_dialog( $post_id = '' ) {
			$user_id = get_current_user_id();
			$content = '';
			if ( $user_id ) {

				$content .= "<h2>" . sprintf( __( "New %s", 'gd-lists' ), geodir_lists_name_singular() ) . "</h2>";
				$content .= "<form>";

				// list name
				$content .= "<label for='gd-list-name'>" . __( "Name",'gd-lists' ) . "</label>";
				$content .= '<input id="gd-list-name" name="list_name" type="text">';

				// list description
//				$content .= "<label for='gd-list-description'>" . __( "Description",'gd-lists' ) . "</label>";
//				$content .= '<textarea id="gd-list-description" name="list_description"></textarea>';

				// public or non-public
				$content .= '<label><input type="radio" name="is_public" value="1" checked><span>'. __( "Public",'gd-lists' ) .'</span> </label>';
				$content .= '<label><input type="radio" name="is_public" value="0"><span>'. __( "Non-Public",'gd-lists' ) .'</span> </label>';

				// description
				$content .= '<p>'. sprintf( __( "A public %s is displayed on the site and can be viewed by anyone. A non-public %s can still be visible to others if you share a link to it.", 'gd-lists' ), geodir_lists_name_singular(), geodir_lists_name_singular() ) .'</p>';

				$content .= "<p>" . self::save_list_button( $post_id ) . "</p>"; // live inside form so we can use JS: this

				//$content .= print_r($user_lists, true);
				$content .= "</form>";


			} else {
				$content .= self::login_options( $post_id );
			}

			return $content;
		}

		public static function save_list_button( $post_id = '',$list_id = '') {

			$args    = array(
				'badge'   =>  __( "Save", 'gd-lists' ),
				'onclick' => "gd_list_save_list($post_id,this,$list_id)",
				'size' => 'medium',
				'css_class' => 'gd-pointer',
				'alignment' => 'right'

			);
			$content = geodir_get_post_badge( '', $args );


			return $content;
		}

		public static function create_new_list_button( $post_id ) {

			$args    = array(
				'badge'   => '<i class="fas fa-bookmark"></i> ' . sprintf( __( "Save to a New %s", 'gd-lists' ), geodir_lists_name_singular() ),
				'onclick' => "gd_list_create_new_list_dialog($post_id)",
				'css_class' => 'gd-pointer'
			);
			$content = geodir_get_post_badge( $post_id, $args );


			return $content;
		}

		public function new_list_dialog() {
			$content = '';


			return $content;
		}

		public static function login_options( $post_id = '' ) {
			$redirect = '';
			if ( $post_id ) {
				$redirect = get_permalink( $post_id );
			}
			$notifications['login_msg'] = array(
				'type' => 'info',
				'note' => __( 'You must be logged in to use this feature', 'gd-lists' ),
			);
			$content                    = geodir_notification( $notifications );
			$content .= "<br />";
			$content .= GeoDir_User::login_link( $redirect );

			return $content;
		}

		public static function get_list_save_button($list_id,$post_id){
			
			$in_list = GeoDir_Lists_Data::has_post($list_id,$post_id);

			$remove_text = __("Remove","gd-lists");
			$save_text = __("Save","gd-lists");
			$current_text = $in_list ? $remove_text : $save_text;
			$current_class = $in_list ? 'gd-list-action-remove' : 'gd-list-action-add';

			$content = '<a href="javascript:void(0);" onclick="gd_list_save_to_list('.absint($list_id).', '.absint($post_id).',this)" class="gd-list-save-action-link '.$current_class.'" data-text-save="'.$save_text.'" data-text-remove="'.$remove_text.'">'.$current_text.'</a>';

			return $content;
		}

	}

}
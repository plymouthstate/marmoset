<?php

class Marmoset_Theme {
	public static function init() {
		if( !is_admin() ) {
			wp_enqueue_script( 'marmoset-js', get_bloginfo('template_directory') . '/marmoset.js', array('jquery-ui-183'), 3, true );
			wp_enqueue_style( 'marmoset-960', get_bloginfo('template_directory') . '/960.css' );
			wp_enqueue_style( 'marmoset-style', get_bloginfo('template_directory') . '/style.css', 'marmoset-960', 1 );

			wp_enqueue_script( 'jquery-ui-183', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.js', array('jquery'), '1.8.3', true );

			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js', false, '1.4.4', true);
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'jquery-blockui', 'https://www.plymouth.edu/js/jquery-plugins/jquery.blockui.js', false, '2', true);
			wp_enqueue_script( 'jquery-blockui' );

			add_filter( 'post_class', array( 'Marmoset_Theme', 'post_class'), 10, 3 );
		}
	}

	public static function post_class( $classes, $class, $ID ) {
		$taxonomies = array(
			'marm_members',
			'marm_queue',
			'marm_stakeholders',
			'marm_status',
			'marm_project'
		);

		foreach( $taxonomies as $taxonomy ) {
			$class_prepend = substr( $taxonomy, 5 );
			$terms = get_the_terms( (int) $ID, $taxonomy );
			if( !empty( $terms ) ) {
					foreach( (array) $terms as $order => $term ) {
							$class = $class_prepend.'_'.$term->slug;

							if( !in_array( $class, $classes ) ) {
									$classes[] = $class;
							}
					}
			}
		}//end foreach

		return $classes;
	}//end post_class

	public static function project_select() {
		$echo = false;

		$get_meta = array();

		if( $_GET['member'] ) { $_GET['members'] = $_GET['member']; unset( $_GET['member'] ); }
		if( $_GET['members'] ) { $get_meta[] = 'members'; }
		if( $_GET['status'] ) { $get_meta[] = 'status'; }
		if( $_GET['stakeholders'] ) { $_GET['stakehold'] = $_GET['stakeholders']; unset( $_GET['stakeholders'] ); }
		if( $_GET['stakehold'] ) { $get_meta[] = 'stakehold'; }

		if( $get_meta ) {
			echo '<script>';
			foreach( $get_meta as $meta_key ) {
				$members = explode( ',', $_GET[ $meta_key ] );
				foreach( (array) $members as $member ) {
					$member = trim( $member );
				?>
					marm.toggle_meta_filter('<?php echo $meta_key; ?>', '<?php echo $member; ?>', false, false); 
				<?php
				}//end foreach
			}//end foreach
			?> marm.count_projects(); <?php
			if( $_GET['unfocused'] == 'false' ) {
			?> marm.hide_unfocused(); <?php
			}
			echo '</script>';
		}//end if
	}//end project_select
}

add_action( 'init', 'Marmoset_Theme::init' );

<?php
/*
Plugin Name: Marmoset
Plugin URI: http://
Description: 
Version: 1.0
Author: Adam Backstrom
Author URI: http://www.plymouth.edu/
License: GPL2
*/

class Marmoset {
	public static function init() {
		$args = array(
			'labels' => array(
				'name' => 'Projects',
				'singular_name' => 'Project',
				'add_new' => 'Add New Project',
			),
			'public' => true,
			'hierarchical' => true,
			'supports' => array('title', 'editor', 'comments', 'revisions', /*/), 'custom-fields', /*/),
			'menu_position' => 4,
			'taxonomies' => array( 'marm_status', 'marm_stakehold' ),
			'register_meta_box_cb' => 'Marmoset::project_meta_box_cb',
			'rewrite' => array('slug' => 'project'),
		);
		register_post_type( 'marm_project', $args );

		$args = array(
			'label' => 'Project Status',
			'show_in_nav_menus' => true,
			'show_ui' => false,
			'public' => true,
			'publicly_queryable' => true,
		);
		register_taxonomy( 'marm_status', 'marm_project', $args );

		$args = array(
			'label' => 'Stakeholders',
			'show_in_nav_menus' => true,
			'public' => true,
			'show_ui' => true,
			'hierarchical' => true,
		);
		register_taxonomy( 'marm_stakehold', 'marm_project', $args );

		$args = array(
			'label' => 'Project Queue',
			'show_in_nav_menus' => true,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'hierarchical' => false,
			'rewrite' => array('slug' => 'queue'),
		);
		register_taxonomy( 'marm_queue', 'marm_project', $args );

		$args = array(
			'label' => 'Members',
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'public' => true,
			'publicly_queryable' => true,
			'hierarchical' => true, // needed for default checkbox ui in dashboard
		);
		register_taxonomy( 'marm_members', 'marm_project', $args );

		if( is_admin() ) {
			add_action( 'wp_ajax_project_order', __CLASS__ . '::project_order' );
		}

		add_action( 'wp_ajax_submit_project', __CLASS__ . '::submit_project' );
	}//end init

	public static function project_meta_box_cb() {
		add_meta_box('marm-project-props', 'Project Properties', 'Marmoset::project_properties', 'marm_project', 'side', 'high' );
	}//end project_meta_box_cb

	public static function project_taxonomies( $post_id ) {
		if( $parent_id = wp_is_post_revision( $post_id ) ) {
			$post_id = $parent_id;
		}

		$terms = wp_get_object_terms( $post_id, array('marm_queue', 'marm_status') );

		$return = array();
		foreach( $terms as $term ) {
			$return[$term->taxonomy] = $term->term_id;
		}

		return $return;
	}

	public static function project_properties() {
		global $post;

		$project_progress = (int)get_post_meta( $post->ID, 'project_progress', true );
		$project_complexity = (int)get_post_meta( $post->ID, 'project_complexity', true );
		$due_date = get_post_meta( $post->ID, 'due_date', true );
		$project_proposed = get_post_meta( $post->ID, 'project_proposed', true );

		if( $due_date != '' ) {
			$due_date = strftime('%F', $due_date);
		}

		if( $project_proposed != '' ) {
			$project_proposed = strftime('%F', $project_proposed);
		}

		$tax = self::project_taxonomies( $post->ID );

		// set defaults if they weren't already set
		if( !isset($tax['marm_queue']) ) {
			/// TODO: hookable
			$marm_queue = get_term_by('slug', 'small', 'marm_queue');
			$tax['marm_queue'] = $marm_queue->term_id;
		}

		if( !isset($tax['marm_status']) ) {
			/// TODO: hookable
			$marm_status = get_term_by('slug', 'current', 'marm_status');
			$tax['marm_status'] = $marm_status->term_id;
		}

		self::wp_nonce_field();

		echo '<label for="marm-progress">Progress:</label> ';
		echo '<input name="marm-progress" style="text-align: right;" type="text" size="4" maxlength="3" value="' . $project_progress . '"> %<br/>';

		echo '<label for="marm-proposed">Proposed:</label> ';
		echo '<input name="marm-proposed" type="text" size="15" value="' . $project_proposed . '"><br/>';

		echo '<label for="marm-duedate">Due Date:</label> ';
		echo '<input name="marm-duedate" type="text" size="15" value="' . $due_date . '"><br/>';

		echo '<label for="marm-status">Status:</label> ';
		wp_dropdown_categories("hide_empty=0&taxonomy=marm_status&orderby=name&name=marm-status&selected={$tax['marm_status']}");
		echo '<br/>';

		echo '<label for="marm-queue">Queue:</label> ';
		wp_dropdown_categories("hide_empty=0&taxonomy=marm_queue&orderby=name&name=marm-queue&selected={$tax['marm_queue']}");
		echo '<br/>';

		echo '<label for="marm-complexity">Complexity (1-5):</label> ';
		echo '<input name="marm-complexity" type="range" size="5" min="1" max="5" value="' . $project_complexity . '"><br/>';
	}

	public static function project_properties_save( $post_id, $force = false ) {
		global $wpdb;

		if( ! $force && ! current_user_can( 'edit_posts' ) ) {
			return $post_id;
		}

		if( !wp_verify_nonce( $_POST['marm-nonce'], plugin_basename(__FILE__) ) ) {
			return $post_id;
		}

		$post = get_post( $post_id );
		$tax = self::project_taxonomies( $post_id );

		$progress = abs((int)$_POST['marm-progress']);
		$complexity = abs((int)$_POST['marm-complexity']);

		if( $progress > 0 ) {
			// increments of 5
			$progress = round($progress / 5) * 5;
		} elseif( $progress > 100 ) {
			$progress = 100;
		}

		if( $project_complexity < 1 ) {
			$project_complexity = 1;
		} elseif( $project_complexity > 5 ) {
			$project_complexity = 5;
		}

		$due_date = $_POST['marm-duedate'];
		if( empty($due_date) ) {
			delete_post_meta( $post_id, 'due_date' );
		} else {
			$due_date = strtotime($due_date);
			update_post_meta( $post_id, 'due_date', $due_date );
		}

		$project_proposed = $_POST['marm-proposed'];
		if( empty($project_proposed) ) {
			delete_post_meta( $post_id, 'project_proposed' );
		} else {
			$project_proposed = strtotime($project_proposed);
			update_post_meta( $post_id, 'project_proposed', $project_proposed );
		}

		update_post_meta( $post_id, 'project_progress', $progress );
		update_post_meta( $post_id, 'project_complexity', $complexity );

		// don't add revisions to a taxonomy, add the original post
		$post_parent = wp_is_post_revision( $post_id );
		$post_tax_id = $post_parent ? $post_parent : $post_id;

		$marm_status = (int)$_POST['marm-status'];
		wp_set_object_terms( $post_tax_id, $marm_status, 'marm_status' );

		$marm_queue = (int)$_POST['marm-queue'];
		wp_set_object_terms( $post_tax_id, $marm_queue, 'marm_queue' );

		// if status or queue has changed, update the post order
		if( $marm_status != $tax['marm_status'] || $marm_queue != $tax['marm_queue'])  {
			$order_value = 0;

			self::get_projects( array( 'posts_per_page' => 1, 'echo' => false ) );

			if( have_posts() && $post = the_post() ) {
				$order_value = get_post_meta( $post->ID, 'project_order', true );
			}

			update_post_meta( $post_id, 'project_order', $order_value + 1 );
		}
	}//end project_properties_save

	public static function remove_meta_boxes() {
		remove_meta_box('tagsdiv-marm_queue', 'marm_project', 'side');
		remove_meta_box('commentstatusdiv', 'marm_project', 'normal');
		remove_meta_box('revisionsdiv', 'marm_project', 'normal');
	}

	/**
	 * Project list to fetch has an implicit queue. Fetch projects with a given status.
	 */
	public static function get_projects( $args ) {
		$defaults = array(
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'post_type' => 'marm_project',
			'meta_key' => 'project_order',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'echo' => true,
		);

		$args = wp_parse_args( $args, $defaults );

		$status = $args['status'];

		if( isset($args['queue']) ) {
			$queue = $args['queue'];
		} else {
			global $query_string;

			parse_str($query_string, $qv);
			$queue = $qv['marm_queue'];
		}

		$tax_query = array(
			array(
				'taxonomy' => 'marm_status',
				'terms' => $status,
				'field' => 'slug',
			),
			array(
				'taxonomy' => 'marm_queue',
				'terms' => $queue,
				'field' => 'slug',
			),
		);

		$args['tax_query'] = $tax_query;

		query_posts( $args );

		if( $args['echo'] ) {
			include TEMPLATEPATH . '/projects-compact.php';
		}
	}//end get_projects

	public static function the_due_date() {
		global $post;

		$due_date = get_post_meta( $post->ID, 'due_date', true );
		if( $due_date !== '' ) {
			echo strftime('%F', $due_date);
		}
	}
	
	/**
	 * Called via admin-ajax.php.
	 */
	public static function project_order() {
		global $wpdb;

		$target_id = (int)$_GET['target_id'];
		$other_id = (int)$_GET['other_id'];
		$placement = $_GET['placement'];
		$proj_queue = $_GET['proj_queue'];
		$proj_status = $_GET['proj_status'];

		wp_set_object_terms( $target_id, $proj_status, 'marm_status' );

		$new_sort_order = null;

		if( $placement == 'single' ) {
			$new_sort_order = 0;
		} elseif( $placement == 'after' ) {
			// only happens when this element is being placed at the end of the list
			$other_sort_order = get_post_meta( $other_id, 'project_order', true );
			$new_sort_order = $other_sort_order + 1;
		} else {
			// going before something else; bump up all other items
			$other_sort_order = get_post_meta( $other_id, 'project_order', true );

			$wpdb->query( $wpdb->prepare("
				UPDATE $wpdb->postmeta
				SET meta_value = meta_value + 1
				WHERE meta_key = 'project_order' AND meta_value >= %d
			", $other_sort_order) );

			$new_sort_order = $other_sort_order;
		}

		update_post_meta( $target_id, 'project_order', $new_sort_order );
	}
	/**
	 * Submit a project via ajax.
	 */
	public static function submit_project() {
		header('Content-type: application/json');

		$post_type = 'marm_project';
		$post_status = 'publish';
		$post_title = $_POST['marm-title'];
		$post_content = $_POST['marm-content'];

		$post = compact( 'post_type', 'post_status', 'post_title', 'post_content' );
		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) ) {
			/// TODO: better
			die( $post_id );
		}

		$marm_stakehold = $_POST['tax_input']['marm_stakehold'];
		$marm_stakehold = array_map( create_function( '$a', 'return (int)$a;' ), $marm_stakehold );

		wp_set_object_terms( $post_id, 'Proposed', 'marm_status' );
		wp_set_object_terms( $post_id, 'Proposed', 'marm_queue' );
		wp_set_object_terms( $post_id, $marm_stakehold, 'marm_stakehold' );

		update_post_meta( $post_id, 'project_proposed', strftime('%F') );
		update_post_meta( $post_id, 'due_date', $_POST['marm-duedate'] );

		echo json_encode( array( 'post_id' => $post_id ) );
		die();
	}//end submit_project

	public static function wp_nonce_field() {
		wp_nonce_field( plugin_basename(__FILE__), 'marm-nonce' );
	}
}

add_action( 'init', 'Marmoset::init' );
register_activation_hook( __FILE__, 'Marmoset::activate' );
add_action('save_post', 'Marmoset::project_properties_save');

add_action( 'add_meta_boxes_marm_project', 'Marmoset::remove_meta_boxes' );

if( !function_exists( 'the_project_complexity' ) ) {
	$marm_project_complexity = array();

	function the_project_complexity() {
		global $post;

		if( !isset( $marm_project_complexity[ $post->ID ] ) ) {
			$marm_project_complexity[ $post->ID ] = (int) get_post_meta( get_the_ID(), 'project_complexity', true);
		}//end if

		echo $marm_project_complexity[ $post->ID ];
		
	}//end the_project_complexity
}//end if

if( !function_exists( 'the_project_queue' ) ) {
	$marm_project_queue = array();

	function get_project_queue() {
		global $post;

		$queue = wp_get_object_terms( get_the_ID(), array('marm_queue') );

		return $queue[0];
	}//end the_project_queue
}//end if

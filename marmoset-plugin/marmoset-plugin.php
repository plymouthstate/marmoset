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
	/**
	 * returns the project's members
	 */
	public static function get_formatted_members() {
		global $post;

		if( ! $post->taxonomies ) {
			$post->taxonomies = get_the_taxonomies();
		}//end if

		return $post->taxonomies['marm_members'];
	}//end get_formatted_queue

	/**
	 * returns the project's queue
	 */
	public static function get_formatted_queue() {
		global $post;

		if( ! $post->taxonomies ) {
			$post->taxonomies = get_the_taxonomies();
		}//end if

		return $post->taxonomies['marm_queue'];
	}//end get_formatted_queue

	/**
	 * returns the project's stakeholders
	 */
	public static function get_formatted_stakeholders() {
		global $post;

		if( ! $post->taxonomies ) {
			$post->taxonomies = get_the_taxonomies();
		}//end if

		return $post->taxonomies['marm_stakeholders'];
	}//end get_formatted_stakeholders

	/**
	 * returns the project's status
	 */
	public static function get_formatted_status() {
		global $post;

		if( ! $post->taxonomies ) {
			$post->taxonomies = get_the_taxonomies();
		}//end if

		return $post->taxonomies['marm_status'];
	}//end get_formatted_status

	/**
	 * return the complete date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function get_the_complete_date( $format = null, $gmt_offset = null ) {
		global $post;

		$date = get_post_meta( $post->ID, 'complete_date', true );
		if( $date !== '' ) {
			return self::format_date( $date, $format, $gmt_offset );
		}//end if
	}//end get_the_complete_date

	/**
	 * returns the project complexity
	 */
	public static function get_the_complexity() {
		global $post;
		static $project_complexity;

		if( !isset( $project_complexity[ $post->ID ] ) ) {
			$project_complexity[ $post->ID ] = (int) get_post_meta( get_the_ID(), 'project_complexity', true);

			if( !$project_complexity[ $post->ID ] ) {
				$project_complexity[ $post->ID ] = 1;
			}//end if
		}//end if

		return $project_complexity[ $post->ID ];
	}//end get_the_complexity

	/**
	 * return the due date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function get_the_due_date( $format = null, $gmt_offset = null ) {
		global $post;

		$date = get_post_meta( $post->ID, 'due_date', true );
		if( $date !== '' ) {
			return self::format_date( $date, $format, $gmt_offset );
		}//end if
	}//end get_formatted_due_date

	/**
	 * returns the project progress
	 */
	public static function get_the_progress() {
		global $post;

		if( !isset( $post->progress ) ) {
			$post->progress = get_post_meta( $post->ID, 'project_progress', true );
		}//end if

		return $post->progress;
	}//end get_the_progress

	/**
	 * return the estimated_start date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function get_the_estimated_start_date( $format = null, $gmt_offset = null ) {
		global $post;

		$date = get_post_meta( $post->ID, 'estimated_start_date', true );
		if( $date !== '' ) {
			return self::format_date( $date, $format, $gmt_offset );
		}//end if
	}//end get_the_estimated_start_date

	/**
	 * returns the project members
	 */
	public static function get_the_members() {
		global $post;

		if( !isset( $post->members ) ) {
			$post->members = wp_get_object_terms( get_the_ID(), array('marm_members') );
		}//end if

		return $post->members;
	}//end get_the_members

	/**
	 * returns the project queue
	 */
	public static function get_the_queue() {
		global $post;

		if( !isset( $post->queue ) ) {
			$queue = wp_get_object_terms( get_the_ID(), array('marm_queue') );
			$post->queue = $queue[0];
		}//end if

		return $post->queue;
	}//end get_the_queue

	/**
	 * returns the project stakeholders
	 */
	public static function get_the_stakeholders() {
		global $post;

		if( !isset( $post->stakeholders ) ) {
			$post->stakeholders = wp_get_object_terms( get_the_ID(), array('marm_stakeholders') );
		}//end if

		return $post->stakeholders;
	}//end get_the_stakeholders

	/**
	 * Return post members in The Loop, caching in $post.
	 */
	public static function get_the_members() {
		global $post;

		if( !isset( $post->members ) ) {
			$post->members = wp_get_object_terms( get_the_ID(), array('marm_members') );
		}//end if

		return $post->members;
	}//end get_the_members

	/**
	 * Return post members in The Loop, caching in $post.
	 */
	public static function get_the_status() {
		global $post;

		if( !isset( $post->marm_status ) ) {
			$post->marm_status = array_pop( wp_get_object_terms( get_the_ID(), array('marm_status') ) );
		}//end if

		return $post->marm_status;
	}//end get_the_status

	/**
	 * return the start date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function get_the_start_date( $format = null, $gmt_offset = null ) {
		global $post;

		$date = get_post_meta( $post->ID, 'start_date', true );
		if( $date !== '' ) {
			return self::format_date( $date, $format, $gmt_offset );
		}//end if
	}//end get_formatted_start_date


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
			'taxonomies' => array( 'marm_status', 'marm_stakeholders' ),
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
		register_taxonomy( 'marm_stakeholders', 'marm_project', $args );

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

	public static function project_properties() {
		global $post;

		$project_progress = (int)get_post_meta( $post->ID, 'project_progress', true );
		$project_complexity = (int)get_post_meta( $post->ID, 'project_complexity', true );
		$due_date = get_post_meta( $post->ID, 'due_date', true );
		$start_date = get_post_meta( $post->ID, 'start_date', true );
		$complete_date = get_post_meta( $post->ID, 'complete_date', true );
		$estimated_start_date = get_post_meta( $post->ID, 'estimated_start_date', true );

		if( $due_date != '' ) {
			$due_date = strftime('%F', $due_date);
		}

		if( $start_date != '' ) {
			$start_date = strftime('%F', $start_date);
		}

		if( $complete_date != '' ) {
			$complete_date = strftime('%F', $complete_date);
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

		echo '<label for="marm-estimatedstartdate">Est. Start Date:</label> ';
		echo '<input name="marm-estimatedstartdate" type="text" size="15" value="' . $estimated_start_date . '"><br/>';

		echo '<label for="marm-startdate">Actual Start Date:</label> ';
		echo '<input name="marm-startdate" type="text" size="15" value="' . $start_date . '"><br/>';

		echo '<label for="marm-duedate">Due Date:</label> ';
		echo '<input name="marm-duedate" type="text" size="15" value="' . $due_date . '"><br/>';

		echo '<label for="marm-completedate">Completion Date:</label> ';
		echo '<input name="marm-completedate" type="text" size="15" value="' . $complete_date . '"><br/>';

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

		$complete_date = $_POST['marm-completedate'];
		if( empty($complete_date) ) {
			delete_post_meta( $post_id, 'complete_date' );
		} else {
			$complete_date = strtotime($complete_date);
			update_post_meta( $post_id, 'complete_date', $complete_date );
		}

		$due_date = $_POST['marm-duedate'];
		if( empty($due_date) ) {
			delete_post_meta( $post_id, 'due_date' );
		} else {
			$due_date = strtotime($due_date);
			update_post_meta( $post_id, 'due_date', $due_date );
		}

		$start_date = $_POST['marm-startdate'];
		if( empty($start_date) ) {
			delete_post_meta( $post_id, 'start_date' );
		} else {
			$start_date = strtotime($start_date);
			update_post_meta( $post_id, 'start_date', $start_date );
		}

		$estimated_start_date = $_POST['marm-estimatedstartdate'];
		if( empty($estimated_start_date) ) {
			delete_post_meta( $post_id, 'estimated_start_date' );
		} else {
			$estimated_start_date = strtotime($estimated_start_date);
			update_post_meta( $post_id, 'estimated_start_date', $estimated_start_date );
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

	/**
	 * formats a date in the given format and gmt offset.  If no format or gmt offset
	 * is provided, grab the format from the options table.
	 *
	 * @param $date timestamp
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function format_date( $date, $format = null, $gmt_offset = null ) {
		if( $format === null ) {
			$format = get_option('date_format');
		}//end if

		if( $gmt_offset === null ) {
			$gmt_offset = get_option('gmt_offset');
		}//end if

		return date_i18n( $format, $date, $gmt_offset );
	}//end format_date

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

	public static function remove_meta_boxes() {
		remove_meta_box('tagsdiv-marm_queue', 'marm_project', 'side');
		remove_meta_box('commentstatusdiv', 'marm_project', 'normal');
		remove_meta_box('revisionsdiv', 'marm_project', 'normal');
	}

	/**
	 * output the start date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function the_start_date( $format = null, $gmt_offset = null ) {
		echo self::get_the_start_date( $format, $gmt_offset );
	}//end the_start_date

	/**
	 * output the completion date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function the_complete_date( $format = null, $gmt_offset = null ) {
		echo self::get_the_complete_date( $format, $gmt_offset );
	}//end the_complete_date

	/**
	 * output the project's complexity
	 */
	public static function the_complexity() {
		echo self::get_the_complexity();
	}//end the_complexity

	/**
	 * output the due date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function the_due_date( $format = null, $gmt_offset = null ) {
		echo self::get_the_due_date( $format, $gmt_offset );
	}//end the_due_date

	/**
	 * output the project's members
	 */
	public static function the_members() {
		echo self::get_formatted_members();
	}//end the_members

	/**
	 * output the project's progress
	 */
	public static function the_progress() {
		echo self::get_the_progress();
	}//end the_progress

	/**
	 * output the estimated_start date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function the_estimated_start_date( $format = null, $gmt_offset = null ) {
		echo self::get_the_estimated_start_date( $format, $gmt_offset );
	}//end the_estimated_start_date

	/**
	 * output the project's queue
	 */
	public static function the_queue() {
		echo self::get_formatted_queue();
	}//end the_queue

	/**
	 * output the project's stakeholders
	 */
	public static function the_stakeholders() {
		echo self::get_formatted_stakeholders();
	}//end the_stakeholders

	/**
	 * output the project's status
	 */
	public static function the_status() {
		echo self::get_formatted_status();
	}//end the_queue
	
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

		$marm_stakeholders = $_POST['tax_input']['marm_stakeholders'];

		// will be null if there are no terms in the stakeholder taxonomy
		if( is_array( $marm_stakeholders ) ) {
			$marm_stakeholders = array_map( create_function( '$a', 'return (int)$a;' ), $marm_stakeholders );
		} else {
			$marm_stakeholders = array();
		}

		// find new stakeholders
		foreach ( $_POST['marm-stakeholder'] as $stakeholder ) {
			$stakeholder = trim($stakeholder);

			if( $stakeholder ) {
				$marm_stakeholders[] = $stakeholder;
			}
		}

		wp_set_object_terms( $post_id, 'Proposed', 'marm_status' );
		wp_set_object_terms( $post_id, 'Proposed', 'marm_queue' );
		wp_set_object_terms( $post_id, $marm_stakeholders, 'marm_stakeholders' );

		update_post_meta( $post_id, 'project_proposed', strftime('%F') );
		update_post_meta( $post_id, 'due_date', $_POST['marm-duedate'] );

		echo json_encode( array( 'post_id' => $post_id ) );
		die();
	}//end submit_project

	public static function wp_nonce_field() {
		wp_nonce_field( plugin_basename(__FILE__), 'marm-nonce' );
	}
	public static function save_complexity(){

		$project_complexity = $_POST['marm-complexity'];			

		if( $project_complexity < 1 ) {
			$project_complexity = 1;
		} elseif( $project_complexity > 5 ) {
			$project_complexity = 5;
		}

		update_post_meta( $_POST['project-id'], 'project_complexity', $project_complexity );

	}
}

add_action( 'init', 'Marmoset::init' );
register_activation_hook( __FILE__, 'Marmoset::activate' );
add_action('save_post', 'Marmoset::project_properties_save');

add_action( 'wp_ajax_save_complexity', 'Marmoset::save_complexity' );

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

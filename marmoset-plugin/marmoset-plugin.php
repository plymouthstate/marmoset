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
	public static $project_classes = array();

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

		$tax = self::project_taxonomies( $post->ID );

		return $tax['marm_complexity'];
	}//end get_the_complexity

	public static function get_the_complexity_int() {
		global $post;

		$slug = self::get_the_complexity_slug();
		$integer = substr( $slug, -1 );

		return $integer;
	}//end get_the_complexity_slug

	public static function get_the_complexity_name() {
		global $post;

		$term_id = self::get_the_complexity();
		$term = get_term( $term_id, 'marm_complexity' );

		return $term->name;
	}//end get_the_complexity_name

	public static function get_the_complexity_slug() {
		global $post;

		$term_id = self::get_the_complexity();
		$term = get_term( $term_id, 'marm_complexity' );

		return $term->slug;
	}//end get_the_complexity_slug

	public static function is_overdue( $args )	{
		global $post;

		$date = get_post_meta( $post->ID, $args[ 'date_display' ], true );
		if( $date && $args[ 'display_overdue' ] ) {				
			if( $date - time() < 0 )	{
				return true;
			}
		}

	}
	public static function date_diff( $time1, $time2, $format = '%1$s years, %2$s months'){
		// If not numeric then convert texts to unix timestamps
		if (!is_int($time1)) {
			$time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
			$time2 = strtotime($time2);
		}

		// If time1 is bigger than time2
		// Then swap time1 and time2
		if ($time1 > $time2) {
			$ttime = $time1;
			$time1 = $time2;
			$time2 = $ttime;
		}

		// Set up intervals and diffs arrays
		$intervals = array('year','month','day','hour','minute','second');
		$diffs = array();

		// Loop thru all intervals
		foreach ($intervals as $interval) {
			// Set default diff to 0
			$diffs[$interval] = 0;
			// Create temp time from time1 and interval
			$ttime = strtotime("+1 " . $interval, $time1);
			// Loop until temp time is smaller than time2
			while ($time2 >= $ttime) {
				$time1 = $ttime;
				$diffs[$interval]++;
				// Create new temp time from time1 and interval
				$ttime = strtotime("+1 " . $interval, $time1);
			}
		}
		if( $format == 'array'){
			return $diffs;
		} else {
			$text = sprintf($format, $diffs['year'], $diffs['month'], $diffs['day'], $diffs['hour'], $diffs['minute'], $diffs['second']);

			foreach( $intervals as $interval){
				if( $diffs[$interval] == 1 ){
					$search[] = $interval.'s';
					$replace[] = $interval;
				}//end if
			}//end foreach

			return str_replace($search, $replace, $text);
		}//end else
	}//end date_diff
	public static function get_the_overdue_date()	{
		global $post;

		$date = get_post_meta( $post->ID, 'due_date', true );
		$months = self::date_diff((int)$date, strtotime( 'now' ), '%2$s');
		$days = self::date_diff((int)$date, strtotime( 'now' ), '%3$s');

		if( $months == 1 ) {
			$month_str.= $months.' month';
		}
		else {
			$month_str.= $months.' months';
		}

		if( $days == 1 ) {
			$day_str = $days.' day';
		}
		else {
			$day_str = $days.' days';
		}
		if( $months == 0 ) {
			return $day_str. ' overdue';
		}
		elseif( $days == 0 ) {
			return $month_str. ' overdue'; 
		}
		else {
			return $month_str. ' ' .$day_str. ' overdue';
		}
		
	}
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
			$progress = get_post_meta( $post->ID, 'project_progress', true );
			$post->progress = ($progress) ? $progress : '0';
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

	public static function get_the_date_entered( $format = null, $gmt_offset = null ) {
		global $post;
			
		$date = strtotime( $post->post_date );
		return self::format_date( $date, $format, $gmt_offset );
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
			'supports' => array('title', 'editor', 'comments', 'revisions' ),
			'menu_position' => 4,
			'taxonomies' => array( 'marm_status', 'marm_stakeholders', 'marm_complexity' ),
			'register_meta_box_cb' => 'Marmoset::project_meta_box_cb',
			'rewrite' => array('slug' => 'project'),
		);
		//$args['supports'][] = 'custom-fields';
		register_post_type( 'marm_project', $args );

		$args = array(
			'label' => 'Project Status',
			'show_in_nav_menus' => true,
			'show_ui' => true,
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
			'rewrite' => array('slug' => 'stakeholder'),
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
			'rewrite' => array('slug' => 'member'),
		);
		register_taxonomy( 'marm_members', 'marm_project', $args );

		$args = array(
			'label' => 'Complexity',
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'public' => true,
			'publicly_queryable' => true,
		);
		register_taxonomy( 'marm_complexity', 'marm_project', $args );

		if( wp_count_terms( 'marm_complexity' ) == 0 ) {

			$default_complexity = array();
			$default_complexity[] = array(
				'name' => 'Value Size',
				'desc' => 'a few developer hours ( < 12 hours )',
			);
			$default_complexity[] = array(
				'name' => 'Medium',
				'desc' => 'a few developer days ( < 10 days )',
			);
			$default_complexity[] = array(
				'name' => 'Large',
				'desc' => 'a few developer days ( < 6 weeks )',
			);
			$default_complexity[] = array(
				'name' => 'Very Large',
				'desc' => 'a few developer months ( < 3 months )',
			);
			$default_complexity[] = array(
				'name' => 'Super Size',
				'desc' => 'more than a few developer months ( > 3 months )',
			);

			for( $i = 1; $i <= 5; $i++ ) {
				wp_insert_term( $default_complexity[$i][$name], 'marm_complexity', array(
					'description' => $default_complexity[$i][$dsec],
					'slug' => "complexity-$i",
				));
			}
		}

		if( is_admin() ) {
			add_action( 'wp_ajax_project_order', __CLASS__ . '::project_order' );
			add_action( 'admin_menu', __CLASS__ . '::remove_meta_boxes' );
		}

		add_action( 'wp_ajax_submit_project', __CLASS__ . '::submit_project' );
	}//end init

	/**
	 * Plugin activation hook.
	 */
	public static function activate() {
	}//end activate

	public static function project_meta_box_cb() {
		add_meta_box('marm-project-props', 'Project Properties', 'Marmoset::project_properties', 'marm_project', 'side', 'high' );
	}//end project_meta_box_cb

	public static function project_properties() {
		global $post;

		$project_progress = (int)get_post_meta( $post->ID, 'project_progress', true );
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

		if( $estimated_start_date != '' ) {
			$estimated_start_date = strftime('%F', $estimated_start_date);
		}

		$tax = self::project_taxonomies( $post->ID );

		// set defaults if they weren't already set
		if( !isset($tax['marm_queue']) ) {
			/// TODO: hookable
			$marm_queue = get_term_by( 'slug', 'small', 'marm_queue' );
			$tax['marm_queue'] = $marm_queue->term_id;
		}

		if( !isset($tax['marm_status']) ) {
			/// TODO: hookable
			$marm_status = get_term_by( 'slug', 'current', 'marm_status' );
			$tax['marm_status'] = $marm_status->term_id;
		}

		if( !isset($tax['marm_complexity']) ) {
			$marm_complexity = get_term_by( 'slug', 'complexity-0', 'marm_complexity' );
			$tax['marm_complexity'] = $marm_complexity->term_id;
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
		wp_dropdown_categories("hide_empty=0&taxonomy=marm_complexity&orderby=slug&name=marm-complexity&selected={$tax['marm_complexity']}");
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

		if( $progress > 0 ) {
			// increments of 5
			$progress = round($progress / 5) * 5;
		} elseif( $progress > 100 ) {
			$progress = 100;
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

		// don't add revisions to a taxonomy, add the original post
		$post_parent = wp_is_post_revision( $post_id );
		$post_tax_id = $post_parent ? $post_parent : $post_id;

		$marm_status = (int)$_POST['marm-status'];
		wp_set_object_terms( $post_tax_id, $marm_status, 'marm_status' );

		$marm_queue = (int)$_POST['marm-queue'];
		wp_set_object_terms( $post_tax_id, $marm_queue, 'marm_queue' );

		$marm_complexity = (int)$_POST['marm-complexity'];
		wp_set_object_terms( $post_tax_id, $marm_complexity, 'marm_complexity' );

		// if status or queue has changed, update the post order
		if( $marm_status != $tax['marm_status'] || $marm_queue != $tax['marm_queue'])  {
			$order_value = self::max_project_order( $marm_queue, $marm_status );
			update_post_meta( $post_id, 'project_order', $order_value + 1 );
		}
	}//end project_properties_save

	public static function project_taxonomies( $post_id ) {
		if( $parent_id = wp_is_post_revision( $post_id ) ) {
			$post_id = $parent_id;
		}

		$terms = wp_get_object_terms( $post_id, array('marm_queue', 'marm_status', 'marm_complexity') );

		$return = array();

		foreach( $terms as $term ) {
			$return[$term->taxonomy] = $term->term_id;
		}

		if( ! isset($terms['marm_complexity']) ) {
			$terms['marm_complexity'] = get_term_by( 'slug', 'complexity-0', 'marm_complexity' );
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

		$status_field = 'slug';
		$queue_field = 'slug';

		if( is_int($status) || is_numeric($status) ) {
			$status = (int)$status;
			$status_field = 'term_id';
		}

		if( is_int($queue) || is_numeric($queue) ) {
			$queue = (int)$queue;
			$queue_field = 'term_id';
		}

		$tax_query = array(
			array(
				'taxonomy' => 'marm_status',
				'terms' => $status,
				'field' => $status_field,
			),
			array(
				'taxonomy' => 'marm_queue',
				'terms' => $queue,
				'field' => $queue_field,
			),
		);

		$args['tax_query'] = $tax_query;
		query_posts( $args );

		if( $args['meta_key'] != 'project_order' ) {
			Marmoset::$project_classes[] = 'non-default-orderby orderby-'.$args['meta_key'];
		}//end if

		if( $args['echo'] ) {
			include TEMPLATEPATH . '/projects-compact.php';
		}
	}//end get_projects

	public static function remove_meta_boxes() {
		remove_meta_box('tagsdiv-marm_queue', 'marm_project', 'side');
		remove_meta_box('tagsdiv-marm_complexity', 'marm_project', 'side');
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

	public static function max_project_order( $queue, $status ) {
		Marmoset::get_projects( "posts_per_page=1&order=DESC&echo=0&status=$status&queue=$queue" );
		the_post();
		$latest_post_id = get_post_meta( get_the_ID(), 'project_order', true );
		wp_reset_query();

		if( ! $latest_post_id ) {
			$latest_post_id = 1;
		}

		return $latest_post_id;
	}//end max_project_order

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

	public static function the_complexity_slug() {
		echo self::get_the_complexity_slug();
	}//end get_the_complexity_slug

	public static function the_complexity_name() {
		echo self::get_the_complexity_name();
	}//end get_the_complexity_name

	public static function the_complexity_int() {
		echo self::get_the_complexity_int();
	}//end get_the_complexity_name

	/**
	 * output the due date for the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function the_due_date( $format = null, $gmt_offset = null ) {
		echo self::get_the_due_date( $format, $gmt_offset );
	}//end the_due_date

	/**
	 * output the date the user entered the project
	 * @param $format string date format
	 * @param $gmt_offset string GMT offset
	 */
	public static function the_date_entered( $format = null, $gmt_offset = null ) {
		echo self::get_the_date_entered( $format, $gmt_offset );
	}//end the_due_date

	public static function the_date( $function, $format = null, $gmt_offset = null ) {
		call_user_func( __CLASS__.'::the_'.$function, $format, $gmt_offset );
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
	 * output the project classes
	 */
	public static function the_project_classes() {
		echo implode(' ', self::$project_classes );
	}//end the_project_classes

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

	public static function format_date_title( $title ) {
		$title = str_replace( '_', ' ', $title );
		echo mb_convert_case( $title, MB_CASE_TITLE, "UTF-8" );
	}

	/**
	 * output all queues
	 */
	public static function the_queues( $class = 'queues', $term_class = '', $span = false, $include_completed = true) {
		$terms = get_terms('marm_queue');
		$text = '<ul class="'.$class.'">'."\n";
		foreach( $terms as $term) {
			if( $span ) {
				$term->name = '<span>'.$term->name.'</span>';
			}//end if
			$text .= '<li><a href="'.get_bloginfo('url').'/queue/'.$term->slug.'/" class="'.$term_class.' '.$term->slug.'">'.$term->name.'</a></li>'."\n";
		}//end foreach
		$text .= '<li><a href="'.get_bloginfo('url').'/complete/" class="'.$term_class.' completed">'.($span ? '<span>Completed</span>' : 'Completed').'</a></li>'."\n";
		$text .= '</ul>';

		echo $text;
	}//end the_queues

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
	
	public static function get_the_complexity_description()
	{
		$complexity = wp_get_object_terms( get_the_ID(), 'marm_complexity' );
		return ($complexity[0]->description ? $complexity[0]->description : $complexity[0]->name);
	}
	public static function the_complexity_description()
	{
		echo self::get_the_complexity_description();
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
		$post_title = $_REQUEST['marm-title'];
		$post_content = $_REQUEST['marm-content'];

		$latest_post_id = self::max_project_order( 'proposed', 'proposed' );

		$marm_stakeholders = $_REQUEST['tax_input']['marm_stakeholders'];
		$marm_complexity = get_term( (int) $_REQUEST['marm-complexity'], 'marm_complexity' );

		// will be null if there are no terms in the stakeholder taxonomy
		if( is_array( $marm_stakeholders ) ) {
			$marm_stakeholders = array_map( create_function( '$a', 'return (int)$a;' ), $marm_stakeholders );
		} else {
			$marm_stakeholders = array();
		}

		foreach ( (array)$_REQUEST['marm-stakeholder'] as $stakeholder ) {
			$stakeholder = trim($stakeholder);

			if( $stakeholder ) {
				$marm_stakeholders[] = $stakeholder;
			}
		}

		$post = compact( 'post_type', 'post_status', 'post_title', 'post_content' );
		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) ) {
			/// TODO: better
			die( $post_id );
		}

		wp_set_object_terms( $post_id, 'new', 'marm_status' );
		wp_set_object_terms( $post_id, 'projects', 'marm_queue' );
		wp_set_object_terms( $post_id, $marm_stakeholders, 'marm_stakeholders' );
		wp_set_object_terms( $post_id, $marm_complexity->slug, 'marm_complexity' );

		update_post_meta( $post_id, 'due_date', strtotime( $_REQUEST['marm-duedate'] ) );
		update_post_meta( $post_id, 'project_order', $latest_post_id + 1 );

		$url = get_permalink( $post_id );
		echo json_encode( array( 'url' => $url ) );
		die();
	}//end submit_project

	public static function wp_nonce_field() {
		wp_nonce_field( plugin_basename(__FILE__), 'marm-nonce' );
	}

	public static function save_complexity(){
		$project_complexity = $_REQUEST['marm-complexity'];			

		if( $project_complexity < 0 ) {
			$project_complexity = 0;
		} elseif( $project_complexity > 5 ) {
			$project_complexity = 5;
		}

		$post_id = $_REQUEST['project-id'];
		$post_parent = wp_is_post_revision( $post_id );
		$post_tax_id = $post_parent ? $post_parent : $post_id;

		$term = get_term_by( 'slug', 'complexity-' . $project_complexity, 'marm_complexity' );

		wp_set_object_terms( $post_tax_id, $term->slug, 'marm_complexity' );
	}

	public static function display_complexity() {
		header("Content-type: json");
		$tax = get_term_by( 'slug', 'complexity-' . $_REQUEST['marm-complexity'], 'marm_complexity' );
		$complexity_info = array(
			'description' => htmlspecialchars_decode( $tax->description ),
			'name' => $tax->name,
		);
		die( json_encode( $complexity_info ) );
	}
}

add_action( 'init', 'Marmoset::init' );
add_action('save_post', 'Marmoset::project_properties_save');

add_action( 'wp_ajax_change_complexity', 'Marmoset::save_complexity', 1 );
add_action( 'wp_ajax_change_complexity', 'Marmoset::display_complexity', 20 );

add_action( 'add_meta_boxes_marm_project', 'Marmoset::remove_meta_boxes' );

add_action( 'wp_ajax_project_submit', 'Marmoset::submit_project' );

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

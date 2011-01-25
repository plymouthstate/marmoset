<?php

class Marmoset_Theme {
	public $found_members = array();
	public $found_stakeholders = array();
	public $found_statuses = array();

	public function init() {
		if( !is_admin() ) {
			wp_enqueue_script( 'marmoset-js', get_bloginfo('template_directory') . '/marmoset.js', array('jquery-ui-183', 'jquery-hotkeys'), 4, true );
			wp_enqueue_style( 'marmoset-960', get_bloginfo('template_directory') . '/960.css' );
			wp_enqueue_style( 'colorbox-theme6', get_bloginfo('template_directory') . '/js/colorbox/theme6/colorbox.css' );
			wp_enqueue_style( 'marmoset-style', get_bloginfo('template_directory') . '/style.css', 'marmoset-960', 3 );

			wp_enqueue_script( 'jquery-ui-183', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.js', array('jquery'), '1.8.3', true );

			wp_deregister_script( 'jquery-hotkeys' );
			wp_enqueue_script( 'jquery-hotkeys', get_bloginfo('template_directory') . '/js/jquery.hotkeys.js', array('jquery'), '0.8', true );

			wp_enqueue_script( 'jquery-history', get_bloginfo('template_directory') . '/js/jquery.history.js', array('jquery'), '1', true );

			wp_deregister_script( 'jquery-colorbox' );
			wp_enqueue_script( 'jquery-colorbox', get_bloginfo('template_directory') . '/js/colorbox/jquery.colorbox-min.js', array('jquery'), '1.3.15', true );

			wp_deregister_script( 'jquery-pubsub' );
			wp_enqueue_script( 'jquery-pubsub', get_bloginfo('template_directory') . '/js/jq.pubsub.js', array('jquery'), '1', true );

			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js', false, '1.4.4', true);
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'jquery-blockui', 'https://www.plymouth.edu/js/jquery-plugins/jquery.blockui.js', false, '2', true);
			wp_enqueue_script( 'jquery-blockui' );

			add_filter( 'post_class', array( $this, 'post_class'), 10, 3 );
		}

		if( is_taxonomy('marm_queue') ) {
			add_action( 'wp_footer', array( $this, 'project_filter' ) );
		}

		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}//end init

	public function body_class( $classes ) {
		if( current_user_can( 'edit_posts' ) ) {
			$classes[] = 'user-cap-edit_posts';
		}

		return $classes;
	}//end body_class

	public function body_class_submit( $classes ) {
		$classes[] = 'marm-submit';
		return $classes;
	}

	public function query_vars( $query_vars ) {
		$query_vars[] = 'marm_submit';
		return $query_vars;
	}//end query_vars

	public function rewrite_rules_array( $rules ) {
		$new = array();
		$new['submit/?$'] = 'index.php?marm_submit=1';
		return $new + $rules;
	}//end query_vars

	public function template_redirect() {
		global $wp_query;

		if( $wp_query->query_vars['marm_submit'] ) {
			add_filter( 'body_class', array( $this, 'body_class_submit' ) );
			include TEMPLATEPATH . '/submit.php';
			die();
		} else {
			add_action( 'wp_footer', array( $this, 'include_submit_form' ) );
		}
	}//end template_redirect

	public function include_submit_form() {
		include TEMPLATEPATH . '/includes/submit-form.php';
	}

	public function project_filter() {
		include TEMPLATEPATH . '/includes/project-filter.php';
	}//end project_filter

	public function post_class( $classes, $class, $ID ) {
		$taxonomies = array(
			'marm_members',
			'marm_queue',
			'marm_stakeholders',
			'marm_status',
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

	/**
	 * Run from footer.php to apply filtering settings from the query string.
	 */
	public function project_select() {
		$echo = false;

		$get_meta = array();

		if( $_GET['member'] ) { $_GET['members'] = $_GET['member']; unset( $_GET['member'] ); }
		if( $_GET['members'] ) { $get_meta[] = 'members'; }
		if( $_GET['status'] ) { $get_meta[] = 'status'; }
		if( $_GET['stakeholder'] ) { $_GET['stakeholders'] = $_GET['stakeholder']; unset( $_GET['stakeholder'] ); }
		if( $_GET['stakeholders'] ) { $get_meta[] = 'stakeholders'; }

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

	/**
	 * Track the terms found on this page.
	 */
	public function update_found_terms() {
		$stakeholders = Marmoset::get_the_stakeholders();
		foreach( $stakeholders as $stakeholder ) {
			$this->found_stakeholders[ $stakeholder->slug ] += 1;
		}

		$members = Marmoset::get_the_members();
		foreach( $members as $member ) {
			$this->found_members[ $member->slug ] += 1;
		}

		$status = Marmoset::get_the_status();
		$this->found_statuses[ $status->slug ] += 1;
	}//end update_found_terms

	public function output_found_terms() {
		echo '<ul>';
		$this->output_found_terms_for( 'marm_stakeholders', $this->found_stakeholders );
		$this->output_found_terms_for( 'marm_members', $this->found_members );
		$this->output_found_terms_for( 'marm_status', $this->found_statuses );
		echo '</ul>';
	}

	public function output_found_terms_for( $taxonomy_slug, $terms ) {
		$taxonomy = get_taxonomy( $taxonomy_slug );
		$terms_full = array();

		// trim marm_ off the front (hacky, needs a fix)
		echo '<li class="' . substr( $taxonomy->name, 5 ) . '"><span>' . $taxonomy->labels->name . ':</span>';

		echo '<ul>';
		foreach( $terms as $term_slug => $found_count ) {
			$term = get_term_by( 'slug', $term_slug, $taxonomy->name );
			$term->found_count = $found_count;
			$terms_full[] = $term;
		}

		usort( $terms_full, create_function( '$a, $b', 'return strnatcasecmp($a->name, $b->name);' ) );

		foreach( $terms_full as $term ) {
			echo '<li class="' . $term->slug . '"><a href="#">' . $term->name . ' (' . $term->found_count . ')</a></li>';
		}

		echo '</ul><br class="clear"></li>';
	}//end output_found_terms_for

	public function widgets_init() {
		register_sidebar(array(
			'name' => 'Project List',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		)); 

		register_widget( 'Marmoset_Widget_Projects' );
	}//end widgets_init
}//end Marmoset_Theme

class Marmoset_Widget_Projects extends WP_Widget {
	public function __construct() {
		parent::__construct(false, 'Marmoset: Projects');
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$term = get_term_by( 'slug', $instance['term_slug'], 'marm_status' );
		$title = $term->name;

		?>

		<div class="grid_16 project-status" data-status="<?php echo $term->slug; ?>">
		<h2><?php echo $title; ?></h2>
			<div>
				<?php Marmoset::get_projects( array( 'marm_status' => $term->slug) ); ?>
			</div>
		</div>
		<div class="clear"></div>

		<?php
	}

	public function form( $instance ) {
		$taxonomy = get_taxonomy( 'marm_status' );
		$terms = get_terms( $taxonomy->name );

		$selected_term = $instance['term_slug'];

		echo '<select id="' . $this->get_field_id('term_slug') .
			'" name="' . $this->get_field_name('term_slug') . '">';

		foreach( $terms as $term ) {
			echo '<option value="' . $term->slug . '"';
			if( $term->slug == $selected_term ) {
				echo ' selected="selected"';
			}
			echo '>';
			echo esc_html( $term->name );
			echo '</option>';;
		}

		echo '</select>';
	}//end form

	public function update( $new, $old ) {
		$instance = $old;
		$instance['term_slug'] = $new['term_slug'];
		return $instance;
	}//end update
}//end Marmoset_Widget_Projects

global $marmoset_theme;
$marmoset_theme = new Marmoset_Theme;

add_action( 'init', array( $marmoset_theme, 'init' ) );
add_action( 'widgets_init', array( $marmoset_theme, 'widgets_init' ) );
add_action( 'body_class', array( $marmoset_theme, 'body_class' ) );
add_filter( 'query_vars', array( $marmoset_theme, 'query_vars' ) );
add_action( 'template_redirect', array( $marmoset_theme, 'template_redirect' ) );
add_action( 'rewrite_rules_array', array( $marmoset_theme, 'rewrite_rules_array' ) );

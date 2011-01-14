<?php
require_once 'PSU.php';
class Marmoset_Theme {
	public $found_members = array();
	public $found_stakeholders = array();
	public $found_statuses = array();

	public function init() {
		if( !is_admin() ) {
			wp_enqueue_script( 'marmoset-js', get_bloginfo('template_directory') . '/marmoset.js', array('jquery-ui-183', 'jquery-shortkeys'), 3, true );
			wp_enqueue_style( 'marmoset-960', get_bloginfo('template_directory') . '/960.css' );
			wp_enqueue_style( 'marmoset-style', get_bloginfo('template_directory') . '/style.css', 'marmoset-960', 1 );

			wp_enqueue_script( 'jquery-ui-183', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.js', array('jquery'), '1.8.3', true );
			wp_enqueue_script( 'jquery-shortkeys', get_bloginfo('template_directory') . '/js/jquery.shortkeys.js', array('jquery'), 1, true );

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
			$this->found_stakeholders[ $stakeholder->slug ] = 1;
		}

		$members = Marmoset::get_the_members();
		foreach( $members as $member ) {
			$this->found_members[ $member->slug ] = 1;
		}

		$status = Marmoset::get_the_status();
		$this->found_statuses[ $status->slug ] = 1;
	}//end update_found_terms

	public function output_found_terms() {
		echo '<ul>';
		$this->output_found_terms_for( 'marm_stakeholders', array_keys($this->found_stakeholders) );
		$this->output_found_terms_for( 'marm_members', array_keys($this->found_members) );
		$this->output_found_terms_for( 'marm_status', array_keys($this->found_statuses) );
		echo '</ul>';
	}

	public function output_found_terms_for( $taxonomy_slug, $terms ) {
		$taxonomy = get_taxonomy( $taxonomy_slug );

		// trim marm_ off the front (hacky, needs a fix)
		echo '<li class="' . substr( $taxonomy->name, 5 ) . '"><span>' . $taxonomy->labels->name . ':</span>';

		echo '<ul>';
		foreach( $terms as $term_slug ) {
			$term = get_term_by( 'slug', $term_slug, $taxonomy->name );
			echo '<li class="' . $term->slug . '"><a href="#">' . $term->name . '</a></li>';
		}
		echo '</ul><br class="clear"></li>';
	}//end output_found_terms_for
}//end Marmoset_Theme

global $marmoset_theme;
$marmoset_theme = new Marmoset_Theme;

add_action( 'init', array( $marmoset_theme, 'init' ) );

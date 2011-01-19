<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
<script type="text/javascript">
var admin_ajax = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
</head>
<body <?php body_class(); ?>>
<div class="container_16 outer">
	<header class="grid_16">
		<h1><span><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></span></h1></a>
	</header>
	<nav class="grid_16">
		<?php Marmoset::the_queues('grid_12 alpha queues', 'button', true); ?>
		<ul class="grid_4 omega options">
			<?php if( is_user_logged_in() && $page = get_page_by_path('submit') ) : ?>
				<?php if( is_single() && 'marm_project' == get_post_type() ): ?>
				<li>
				<a href="<?php echo get_edit_post_link(); ?>" class="button save"><span>Edit Project</span></a>
				</li>
				<?php endif; ?>
			<li>
				<a href="<?php echo home_url().'/submit/'; ?>" class="button add submit-proposal"><span>Add Project</span></a>
			</li>
			<?php elseif( !is_user_logged_in() ): ?>
			<li>
				<a href="<?php echo home_url().'/wp-admin/'; ?>" class="button login"><span>Sign In</span></a>
			</li>
			<?php endif; ?>
		</ul>
	</nav>
	<div class="grid_16" class="body">
	<?php include 'submit.php'; ?>

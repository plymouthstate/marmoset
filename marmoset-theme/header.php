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
<div class="container_16" id="outer">
	<div class="grid_16" id="header">
		<h1><span><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></span></h1></a>
		<a href="#" class="button add">
			<span>Submit Project</span>
		</a>
		<a href="#" class="button">
			<span>Sign In</span>
		</a>
		<a href="#" class="button save">
			<span>Save</span>
		</a>
		<a href="#" class="button delete">
			<span>Delete</span>
		</a>
	</div>
	<div class="clear"></div>

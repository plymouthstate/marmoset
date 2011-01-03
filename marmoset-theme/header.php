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
<div id="project-filter">
	Focusing on projects with:
	<ul></ul>

	<div class="options">
		<div><span id="project-filter-total">0</span> Projects Total</div>
		<a href="" id="toggle-unfocused"><span class="hide">Hide</span><span class="show">Show</span> Unfocused</a>
	</div>
</div>

<div class="container_16" id="outer">
	<div class="grid_16" id="header">
		<h1><span><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></span></h1></a>
	</div>
	<div class="clear"></div>

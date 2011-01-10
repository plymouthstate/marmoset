<?php get_header(); ?>

<div class="project-queue" data-queue="<?php echo get_query_var('term'); ?>">

<div class="grid_16 project-status" data-status="current">
	<h2>Current</h2>
	<div>
		<?php Marmoset::get_projects( array( 'marm_status' => 'current') ); ?>
	</div>
</div>
<div class="clear"></div>

<div class="grid_16 project-status" data-status="starting-soon">
	<h2>Starting Soon</h2>
	<div>
		<?php Marmoset::get_projects( array( 'marm_status' => 'starting-soon' ) ); ?>
	</div>
</div>
<div class="clear"></div>

<div class="grid_16 project-status" data-status="deferred">
	<h2>Deferred</h2>
	<div>
		<?php Marmoset::get_projects( array( 'marm_status' => 'deferred' ) ); ?>
	</div>
</div>
<div class="clear"></div>

<div class="grid_16 project-status" data-status="proposed">
	<h2>Proposed</h2>
	<div>
		<?php Marmoset::get_projects( array( 'marm_status' => 'proposed' ) ); ?>
	</div>
</div>
<div class="clear"></div>

<div class="grid_16">
	<?php include 'submit.php'; ?>
</div>
<div class="clear"></div>

</div>

<?php get_footer(); ?>

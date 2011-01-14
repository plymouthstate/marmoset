<?php global $marmoset_theme; ?>

<div id="project-filter" style="display:none;">
	<h2>Filters</h2>
	<?php $marmoset_theme->output_found_terms(); ?>

	<div class="options">
		<div><span id="project-filter-total">0</span> Projects Total</div>
		<a href="" id="toggle-unfocused"><span class="hide">Hide</span><span class="show">Show</span> Unfocused</a>
	</div>
</div>

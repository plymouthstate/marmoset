<?php global $marmoset_theme; ?>

<div id="project-filter" style="display:none;">
	<h2>Filters</h2>
	<ul class="shortcuts">
		<li><span>c</span> = clear</li>
		<li><span>h</span> = hide/show unfocused</li>
		<li><span>m</span> = my projects</li>
	</ul>
	<?php $marmoset_theme->output_found_terms(); ?>

	<div class="options">
		<div><span id="project-filter-total">0</span> Projects Total</div>
		<a href="" id="toggle-unfocused"><span class="hide">Hide</span><span class="show">Show</span> Unfocused</a>
	</div>
</div>

<?php require_once ABSPATH . '/wp-admin/includes/template.php'; ?>

<h2>Submit Project</h2>
<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" class="submit-project" method="post">
	<input type="hidden" name="action" value="submit_project">
	<?php Marmoset::wp_nonce_field(); ?>
	<ul>
		<li>
			<label>Name:</label>
			<input name="marm-title" type="text" size="30">
		</li>
		<li>
			<label>Description:</label>
			<textarea name="marm-content"></textarea>
		</li>
		<li>
			<label>Due date:</label>
			<input name="marm-duedate" type="text" size="30">
		</li>
		<li class="stakeholders">
			<label for="stakeholders">Stakeholders:</label>
			<?php if ( wp_count_terms('marm_stakeholders') > 0 ): ?>
				<div class="input-stakeholders">
					<ul>
						<?php wp_terms_checklist( 0, 'taxonomy=marm_stakeholders' ); ?>
					</ul>
				</div>
			<?php else: ?>
				The list of stakeholders is currently empty.
			<?php endif; ?>
			<br>
			<div class="hidden">
				<input type="text" name="marm-stakeholder[]" size="20" placeholder="New stakeholder"><!-- No space, doesn't work with .siblings() anyway. --><a href="#">Delete</a><br>
				<a href="#">Add another&hellip;</a>
			</div>
			<div>Don't see your group? <a href="#">Create new stakeholders</a>.</div>
		</li>
		<li>
			<label for="">&nbsp;</label>
			<input type="submit" value="Submit Project">
		</li>
	</ul>
</form>

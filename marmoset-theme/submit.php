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
		<li>
			<label for="stakeholders">Stakeholders</label>
			<div class="input-stakeholders">
				<ul>
					<?php wp_terms_checklist( 0, 'taxonomy=marm_stakehold' ); ?>
				</ul>
			</div>
		</li>
		<li>
			<label for="">&nbsp;</label>
			<input type="submit" value="Submit Project">
		</li>
	</ul>
</form>

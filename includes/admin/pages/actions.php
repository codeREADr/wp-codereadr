<?php
$responses = CodeReadr\Actions_Model::get_actions();
?>
<div class="codereadr-admin-page codereadr-admin-responses-page">
	<div class="codereadr-admin-page__header">
		<h3><?php _e( 'CodeREADr Actions ', 'codereadr' ); ?></h3>
	</div>
	<div class="codereadr-admin-page__content">
		<div class="codereadr-admin-page__header">
			<h4><?php _e( 'Registered Actions', 'codereadr' ); ?></h4>
			<a class="codereadr-button-primary"> <?php _e( 'Add a new action', 'codereadr' ); ?></a>
		</div>
		<?php if ( empty( $responses ) ) : ?>
			<div class="codereadr-not-found-message"> <?php _e( 'No actions were found!', 'codereadr' ); ?></div>
		<?php endif; ?>
	</div>
</div>

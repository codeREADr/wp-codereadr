<?php
$is_api_key_valid = null;

if ( ! empty( $_POST['api-key'] ) ) {
	$sanitized_api_key = sanitize_text_field( trim( $_POST['api-key'] ) );
	$request           = wp_remote_post(
		'https://api.codereadr.com/api/',
		array(
			'body' => array(
				'api_key' => $sanitized_api_key,
				'section' => 'users',
				'action'  => 'retrieve',
			),
		)
	);
	$is_api_key_valid  = 'connection-problem';
	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
	} else {
		$response         = wp_remote_retrieve_body( $request );
		$is_api_key_valid = true;
		if ( strpos( $response, 'API key is missing' ) ) {
			$is_api_key_valid = false;
		} else {
			update_option( 'codereadr-api-key', $sanitized_api_key );
		}
	}
}

if ( false === $is_api_key_valid ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Invalid API key.', 'codereadr' ); ?></p>
	<?php
} elseif ( 'connection-problem' === $is_api_key_valid ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Connection problem! Please check your connection.', 'codereadr' ); ?></p>
	<?php
} elseif ( true === $is_api_key_valid ) {
	?>
		<p class ="codereadr-success-message"> 
		<?php
		_e( 'Api key saved successfully.', 'codereadr' );
		?>
		</p>
	<?php
}
$api_key = esc_attr( get_option( 'codereadr-api-key' ) );
?>
<div class="codereadr-admin-page codereadr-admin-settings-page">
	<div class="codereadr-admin-page__header">
		<img class="codereadr-icon-image" src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/codereadr-icon.png'; ?>" />
		<h3><?php _e( 'CodeREADr Settings ', 'codereadr' ); ?></h3>
	</div>
	<div class="codereadr-admin-page__content codereadr-admin-settings-page__content">
	<div class="codereadr-admin-page__content_heading">
		<h4><?php _e( 'API KEY ', 'codereadr' ); ?></h4>
	</div>
	<form action="#" method="post">
		<div class="codereadr-admin-settings-page__api-key-label"><?php _e( 'Enter your API Key here', 'codereadr' ); ?></div>
		<div style="flex: 1">
			<input <?php echo $api_key ? 'disabled' : ''; ?> type="text" name="api-key" class="codereadr-admin-settings-page__api-key-input codereadr-text-input" value="<?php echo $api_key ? esc_attr( str_repeat( '*', strlen( $api_key ) - 4 ) . substr( $api_key, -4 ) ) : ''; ?>" />
			<div class="codereadr-admin-settings-page__buttons">
				<a class="codereadr-button-secondary">Reset settings</a>
				<input type="submit" class="codereadr-button-primary" value="Save changes" />
			</div>
		</div>
	</form> 
	</div>
	<div class="codereadr-cards">
		<div class="codereadr-card">
			<h4 class="codereadr-card__heading"> About CodeREADr </h4>	
			<div class="codereadr-card__content"> Learn more about CodeREADr and how it works, unique features and industry solutions by visitng website. </div>	
			<a class="codereadr-button-secondary" href="https://www.codereadr.com/">Visit Website</a>
		</div>
		<div class="codereadr-card">
			<h4 class="codereadr-card__heading"> Support </h4>	
			<div class="codereadr-card__content"> Feeling stuck? Contact us via live chat or by filling out our support form.</div>	
			<a class="codereadr-button-secondary" href=" https://www.codereadr.com/support-request">Contact Support</a>
		</div>
		<div class="codereadr-card">
			<h4 class="codereadr-card__heading"> Knowledge Base </h4>	
			<div class="codereadr-card__content"> Learn more about how to use CodeREADr to its full potential by reading through or knowledge base.</div>	
			<a class="codereadr-button-secondary" href="https://www.codereadr.com/library/learn/integrations">Visit Knowledge Base</a>
		</div>
		<div class="codereadr-card">
			<h4 class="codereadr-card__heading"> Need an Account?</h4>	
			<div class="codereadr-card__content"> Get started today by signing up for a free account. No credit card is required. No long term commitments. Upgrade and downgrade monthly.</div>	
			<a class="codereadr-button-secondary" href="https://secure.codereadr.com/registration">Register</a>
		</div>
	</div>
</div>

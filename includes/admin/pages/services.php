<?php
$api_key = get_option( 'codereadr-api-key' );
if ( ! $api_key ) { ?>
	<p class="codereadr-error-message"><?php _e( 'You should insert your api key first to retrieve the services list.', 'codereadr' ); ?> <a href="<?php echo admin_url( 'admin.php?page=codereadr-settings' ); ?>"><?php _e( 'Insert it from here', 'codereadr' ); ?></a></p>
	<?php
	exit;
}
$request = wp_remote_get(
	'https://api.codereadr.com/api/',
	array(
		'body' => array(
			'api_key' => $api_key,
			'section' => 'services',
			'action'  => 'retrieve',
		),
	)
);
if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Error while retrieving list.', 'codereadr' ); ?> </p>
	<?php
	exit;
} else {
	$response         = wp_remote_retrieve_body( $request );
	$is_api_key_valid = true;
	if ( strpos( $response, 'API key is missing' ) ) {
		?>
		<p class="codereadr-error-message"><?php _e( 'Your API key is invalid!', 'codereadr' ); ?>  <a href="<?php echo admin_url( 'admin.php?page=codereadr-settings' ); ?>"><?php _e( 'Please re-insert it from here', 'codereadr' ); ?></a> </p>
		<?php
		exit;
	} else {
		$response = simplexml_load_string( $response );
		if ( ! $response ) {
			echo "Failed loading XML\n";
			foreach ( libxml_get_errors() as $error ) {
				echo "\t", $error->message;
			}
			exit;
		}
	}
}
?>

<div class='codereadr-admin-services-page'>
	<h3> 
	<?php
	_e( 'CodeREADr Services ', 'codereadr' );
	?>
	</h3>
	<div class="codereadr-admin-services-page__content">
		<div class="codereadr-admin-services-page__header">
			<h4><?php _e( 'Installed services ', 'codereadr' ); ?></h4> 
			<a class="codereadr-button-primary"> <?php _e( 'Add a new service', 'codereadr' ); ?></a>
		</div>
		<table> 
			<thead> 
				<tr> <th> Id </th> <th> Service name</th> <th>Validation Method</th> </tr>
			</thead>
			<tbody> 

			<?php foreach ( $response->service as $service ) { ?>
				<tr>
					<td> <?php echo $service->attributes()->id; ?> </td>
					<td> <?php echo $service->name; ?> </td>
					<td> <?php echo $service->validationmethod; ?> </td>
				</tr>
				<?php
			}
			?>
	</div>
</div>

<?php
use CodeReadr\Managers\Actions_Manager;
use CodeReadr\Services_Model;
use CodeReadr\Integrations;
$api_key                  = get_option( 'codereadr-api-key' );
$in_database_services     = Services_Model::get_services();
$in_database_services_ids = wp_list_pluck( $in_database_services, 'codereadr_service_id' );
$integrations             = Integrations::get_all_integrations();
$registered_actions       = Actions_Manager::instance()->get_all_registered();
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
if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Error while retrieving list.', 'codereadr' ); ?> </p>
	<?php
	exit;
} else {
	$service = wp_remote_retrieve_body( $request );
	if ( strpos( $service, 'API key is missing' ) ) {
		?>
		<p class="codereadr-error-message"><?php _e( 'Your API key is invalid!', 'codereadr' ); ?>  <a href="<?php echo admin_url( 'admin.php?page=codereadr-settings' ); ?>"><?php _e( 'Please re-insert it from here', 'codereadr' ); ?></a> </p>
		<?php
		exit;
	} else {
		$services = simplexml_load_string( $service );
		if ( ! $service ) {
			echo "Failed loading XML\n";
			foreach ( libxml_get_errors() as $error ) {
				echo "\t", $error->message;
			}
			exit;
		}
	}

	$services = codereadr_xml2array( $services ) ['service'];

	$services = array_filter(
		$services,
		function ( $service ) use ( $in_database_services_ids ) {

			$service_id = (int) codereadr_xml2array( $service->attributes()->id )[0];
			return in_array( $service_id, $in_database_services_ids, true );
		}
	);

}
?>
<script>
var codereadrInDBservices  = <?php echo wp_json_encode( $in_database_services, JSON_UNESCAPED_SLASHES ); ?>;
var codereadrInDBservicesIds  = <?php echo wp_json_encode( $in_database_services_ids ); ?>;
var registeredActions = <?php echo wp_json_encode( $registered_actions ); ?>;

</script>
<div class='codereadr-admin-page codereadr-admin-services-page'>
	<div class='codereadr-admin-page__header'>
		<img class="codereadr-icon-image" src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/codereadr-icon.png'; ?>" />
		<h3> 
		<?php
		_e( 'CodeREADr Services ', 'codereadr' );

		?>
		</h3>
		<p style="margin-left: 40px" class="codereadr-info-message"><strong>Note:</strong> Services listed here are the services meant to integrate with WordPress</p>
	</div>	
	<div class="codereadr-admin-page__content codereadr-admin-services-page__content">
			<div class="codereadr-admin-page__content_heading" style="display: flex;justify-content: space-between;">
				<h4><?php _e( 'Configured Services ', 'codereadr' ); ?></h4> 
				<a class="codereadr-button-primary codereadr-add-new-service"> <?php _e( 'Add a new service', 'codereadr' ); ?></a>
			</div>
			<table> 
				<thead> 
					<tr> <th> Service Id </th> <th> Service name</th> <th>Method</th><th>Integration</th> <th> Action </th><th style="text-align: right">Actions</th></tr>
				</thead>
				<tbody> 

				<?php
				if ( empty( $integrations ) ) {
					?>
					<p class="codereadr-error-message"><?php _e( 'No integrations installed!', 'codereadr' ); ?> <a href="<?php echo admin_url( 'admin.php?page=codereadr-integrations' ); ?>"><?php _e( 'Check our available integrations here', 'codereadr' ); ?></a></p>
					<?php
					exit;
				}
				if ( ! empty( $services ) ) {
					foreach ( $services as $service ) {
						$in_database_service = current(
							array_filter(
								$in_database_services,
								function( $database_service ) use ( $service ) {
									return $database_service['codereadr_service_id'] == (int) $service->attributes()->id;
								}
							)
						);
						$integration_slug    = esc_attr( $in_database_service['integration_slug'] );
						$integration_title   = esc_attr( $integration_slug );
						if ( $integrations[ $integration_slug ] ) {
							$integration_title = $integrations[ $integration_slug ]['title'];
						}

						$action_name = esc_attr( $in_database_service['action_name'] );
						$action_type = Actions_Manager::instance()->get_registered( $action_name );
						if ( $action_type ) {
							$action_name = $action_type->title;
						}
						?>
					<tr>
						<td> <?php echo (int) $service->attributes()->id; ?> </td>
						<td> <?php echo esc_attr( $service->name ); ?> </td>
						<td> Postback </td>
						<td> <?php echo $integration_title; ?> </td>
						<td> <?php echo $action_name; ?> </td>
						<td> 
							<div class="codereadr-admin-services-action__buttons">
								<a class="codereadr-admin-services-action__edit" data-service-id='<?php echo (int) $service->attributes()->id; ?>'><img src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/ic_edit.svg'; ?>" /> Edit Service</a>
								<a class="codereadr-admin-services-users__manage" data-service-id='<?php echo (int) $service->attributes()->id; ?>'><img src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/ic_users.svg'; ?>" /> Manage users</a>
								<a class="codereadr-delete-service" data-service-id='<?php echo (int) $service->attributes()->id; ?>'><img src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/ic_delete.svg'; ?>" /> Delete</a>
							</div>
						</td>
					</tr>
							<?php
					}
				}
				?>
			</table>
		</div>
		<div class="codereadr-modal codereadr-service-modal">
			<div class="codereadr-modal__content">
				<div class="codereadr-modal__header">
					<h3> <?php _e( 'Add a new service', 'codereadr' ); ?> </h3>
					<div class="codereadr-modal__close">
						<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
							<path fill="none" stroke="#000" stroke-width="2" d="M3,3 L21,21 M3,21 L21,3"></path>
						</svg>
					</div>
				</div>
				<div class="codereadr-modal__body">
					<form class="codereadr-service-form">
						
							<div class="codereadr-flex codereadr-flex-row" style="padding: 0 0 20px; border-bottom: 1px solid #eee">
								<label style="margin-bottom: 10px;display: block; font-weight:bold;width: 170px"> Service Name: </label>
								<input style="flex: 1 1" type="text" class="codereadr-service-name codereadr-text-input" name="codereadr-service-name" />
							</div>
							<div class="codereadr-flex codereadr-flex-row" style="flex-wrap: nowrap; padding: 20px 0; border-bottom: 1px solid #eee; gap: 20px">
								<div class="codereadr-flex codereadr-flex-row" style="flex: 1 1 50%;">
									<label style="width: 170px;display: block; font-weight: bold;"> Choose your integration: </label>	
									<select  class="codereadr-service-integration-select" name="codereadr-service-integration-slug">
										<?php foreach ( $integrations as $integration_slug => $integration ) { ?>
											<option value="<?php echo $integration_slug; ?>"><?php echo $integration['title']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="codereadr-flex codereadr-flex-row" style="flex: 1 1 50%;">
									<label style="margin-right: 12px;display: block;font-weight: bold;"> Choose your action: </label>
									<select class="codereadr-service-action-select" name="codereadr-service-action-select">
										<?php foreach ( Actions_Manager::instance()->get_all_registered() as $action_name => $action ) { ?>
												<option data-action-name="<?php echo $action_name; ?>" value="<?php echo $action_name; ?>"><?php echo $action->title; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="codereadr-flex codereadr-flex-column" style="padding: 20px 0; border-bottom: 1px solid #eee; align-items: baseline;">
								<div class="codereadr-service-action-description"></div>
								<div class="codereadr-service-action-hint" style="margin-top: 8px;"></div>
							</div>
							<h3 style="font-size: 16px; margin-bottom: 0"> Valid Response </h3>
							<div class="codereadr-service-validations">

								<div class="codereadr-service-validations__left-side">
									<div class="codereadr-flex codereadr-flex-column" style="padding: 20px 0;">  
										<div class="codereadr-success-response-area">
											<h3 style="margin-bottom: 10px; color: #fff; font-size: 16px"> Success Response Text: </h3>
											<textarea name="codereadr-service-response-text" class="codereadr-service-response-text" style="min-height: 160px; min-height: 160px;border: 1px solid #e3e3e3;" ></textarea>
										</div>
									</div>
							
									<div class="codereadr-service-action__default-invalid-conditions"></div>
									<div class="codereadr-service-action__optional-invalid-conditions"></div>
								</div>
								<div class="codereadr-service-validations__right-side">
									<div style="color: #7e7c7c;background: #e3e3e3;;padding: 24px;border-radius: 5px; border: 1px solid #c4c4c4;"> <p style="font-size: 16px;margin-top: 0;">You can use the following tags: </p> 
										<p>	
											<strong> Codereadr merge tags: </strong> <br />
												{tid}	The scanned barcode's value. <br>
												{sid}	The numeric ID of the service the scan was made under.<br>
										</p>
										<div class="codereadr-service-action__custom-merge-tags"></div>
									</div>
								</div>
							</div>
							<!-- In WordPress database service id. !-->
							<input type="hidden"  name="service-database-unique-id" class="codereadr-service-database-unique-id" />
							<!-- Codereader remote saved service id at Codereadr server. !-->
							<input type="hidden" name="codereadr-service-remote-id" class="codereadr__codereadr-service-id" />
							<div class="codereadr-admin-services-page__buttons">
								<a class="codereadr-button-secondary">Cancel</a>
								<a class="codereadr-button-primary">Save</a>
							</div>

					</form>
				</div>
			</div>
		</div>
		<div class="codereadr-modal codereadr-manage-users-modal">

			<div class="codereadr-manage-users-modal__content codereadr-modal__content">
				<div class="codereadr-users-listing-screen show">
					<div class="codereadr-modal-header">
						<div style="display:flex; justify-content: space-between; align-items: center;">
							<div class="codereadr-modal__headings">
								<div class="codereadr-modal-heading">
									<h3> Manage '<span></span>' Users </h3>
									<p style="color: #acacac">By selecting these users, you are granting them permissions for this service.</p>
								</div>
								
							</div>
							<a class="codereadr-button-primary codereadr-button-add-new-user">Add a new user</a>
						</div>
					</div>
					<div class="codereadr-modal__close codereadr-modal__close-icon">
						<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
							<path fill="none" stroke="#000" stroke-width="2" d="M3,3 L21,21 M3,21 L21,3"></path>
						</svg>
					</div>
					<div class="codereadr-listing-screen__loader show">
						<div class="dl">
							<div class="dl__container">
								<div class="dl__corner--top"></div>
								<div class="dl__corner--bottom"></div>
							</div>
							<div class="dl__square"></div>
						</div>
					</div>
					<form class="codereadr-users-listing-screen__users">
						
						<div style="display: flex; gap: 40px;">
							<h4> Users List </h4>

							<div class="codereadr-users-selection">
								<span class="codereadr-users-selection__all">Select All</span>
								<span class="codereadr-users-selection__separator">|</span>
								<span class="codereadr-users-selection__none">Deselect All</span>
							</div>
						</div>

						<div class="codereadr-users-listing-screen__users-list"> </div>
						<a class="codereadr-button-secondary codereadr-modal__close" style="margin-right: 10px;width: 160px;text-align: center;">Cancel</a>
						<a class="codereadr-button-primary codereadr-users-list-save-button">Save changes</a>

					</form> 
				</div>
				<div class="codereadr-users-form-screen">
					<div class="codereadr-modal-heading">
						<h3> Add a new user </h3>
						<p style="color: #acacac">Create a new user log in.</p>
						<div class="codereadr-modal__close codereadr-modal__close-icon">
							<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
								<path fill="none" stroke="#000" stroke-width="2" d="M3,3 L21,21 M3,21 L21,3"></path>
							</svg>
						</div>
					</div>
					<form class="codereadr-users-form-screen__add-user" style="margin-top: 20px;">
						<div class="codereadr-users-form-screen__user-name" style="margin-bottom: 15px;">
							<div style="width: 200px"> Email Address </div>
							<input type="email" class="codereadr-users-form-screen__user-name-input codereadr-text-input" />
						</div>
						<div class="codereadr-users-form-screen__user-password" style="margin-bottom: 55px;">
							<div style="width: 200px"> Password </div>
							<input type="password" class="codereadr-users-form-screen__user-password-input codereadr-text-input" />
						</div>
						<a class="codereadr-button-secondary codereadr-back-to-users-listing-screen" style="margin-right: 10px;width: 160px; width: 150px;display: inline-flex;justify-content: center;">Back</a>
						<a class="codereadr-button-primary codereadr-create-new-user-button">Save Changes</a>
					</form>

				</div>
			</div>
		</div>
	<div class="codereadr-floating-error-message hidden"></div>
	<div class="codereadr-floating-success-message hidden"></div>

</div>

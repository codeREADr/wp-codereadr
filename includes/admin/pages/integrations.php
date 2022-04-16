<?php
use CodeReadr\Integrations;

wp_enqueue_script( 'codereadr-highlight-js' );
wp_enqueue_style( 'codereadr-highlight-css' );
// base dir of plugins (with trailing slash) instead of WP_PLUGIN_DIR.
$plugins_dir  = trailingslashit( dirname( CODEREADR_PLUGIN_DIR ) );
$integrations = array(
	'event-tickets' => array(
		'title'       => 'Event Tickets Plus',
		'icon_url'    => CODEREADR_PLUGIN_URL . '/includes/admin/assets/images/event-tickets-icon.png',
		'category'    => 'ticketing',
		'description' => 'Integrate Codereadr with Event Tickets Plus plugin.',
		'plugin_link' => 'https://theeventscalendar.com/products/wordpress-event-tickets',
		'plugin_file' => 'event-tickets-plus/event-tickets-plus.php',
	),
	'wc-box-office' => array(
		'title'       => 'Woocommerce Box Office',
		'icon_url'    => CODEREADR_PLUGIN_URL . '/includes/admin/assets/images/woocommerce-box-office-icon.png',
		'category'    => 'ticketing',
		'description' => 'Integrate Codereadr with Woocommerce Box Office.',
		'plugin_link' => 'https://woocommerce.com/products/woocommerce-box-office/',
		'plugin_file' => 'woocommerce-box-office/woocommerce-box-office.php',
	),
);

foreach ( $integrations as $slug => $addon ) {
	$full_plugin_file = $plugins_dir . $addon['plugin_file'];
	$plugin_exists    = file_exists( $full_plugin_file );
	$plugin_data      = $plugin_exists ? get_plugin_data( $full_plugin_file ) : array();

	$integrations[ $slug ]['full_plugin_file'] = $full_plugin_file;
	$integrations[ $slug ]['is_installed']     = $plugin_exists;
	$integrations[ $slug ]['is_active']        = is_plugin_active( $addon['plugin_file'] );
	$integrations[ $slug ]['version']          = $plugin_data['Version'] ?? null;
}

?>
<div class="codereadr-admin-page codereadr-admin-integrations-page">
	<div class='codereadr-admin-page__header'>
		<img class="codereadr-icon-image" src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/codereadr-icon.png'; ?>" />
		<h3> 
		<?php
		_e( 'CodeREADr Integrations ', 'codereadr' );

		?>
		</h3>
	</div>	
	<div class="codereadr-admin-integrations-page__content">
		<div class="codereadr-integration codereadr-event-custom-integration">
			<h4> Custom Integrations </h4>
			<div class="codereadr-integration__card">
				<div class="codereadr-integration__heading">
					<svg focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="SettingsIcon" tabindex="-1" title="Settings">
						<path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"></path>
					</svg>
					<h4> Build Your Custom Integration </h4>
				</div>
				<div class="codereadr-integration__description">
					Copy the following code and insert it in your child theme.
				</div>
				<div class="codereadr-integration__footer">
					<img style="width: 30px;" src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/codereadr-icon.png'; ?>" />
					<A class="codereadr-default-button codreadr-custom-integration__view">View</a>
				</div>
			</div>
		</div>
		<div class="codereadr-integration codereadr-event-category">
			<h4> Ticketing </h4>
			<div class="codereadr-integration__cards">
				<?php foreach ( $integrations as $integration ) { ?>
					
					<div class="codereadr-integration__card">
						<div class="codereadr-integration__heading">
							<img src="<?php echo $integration['icon_url']; ?>" />
							<h4> <?php echo $integration['title']; ?> </h4>
						</div>
						<div class="codereadr-integration__description">
							<?php echo $integration['description']; ?> <a href="<?php echo $integration['plugin_link']; ?>" target="_blank">More Info</a>
						</div>
						<div class="codereadr-integration__footer">
							<img style="width: 30px;" src="<?php echo CODEREADR_PLUGIN_URL . 'includes/admin/assets/images/codereadr-icon.png'; ?>" />
							<?php
							if ( $integration['is_installed'] && $integration['is_active'] ) {
								?>
								<div class="codereadr-integration__installed"  style="color: #a9abad; font-style: italic;">Installed</div>
						<?php } elseif ( $integration['is_installed'] ) { ?>
							<a class="codereadr-default-button" target="_blank" href="<?php echo admin_url( 'plugins.php' ); ?>">Activate</a>
								<?php
						} else {

							?>
							<a class="codereadr-default-button" href="<?php echo $integration['plugin_link']; ?>">Install</a>

								<?php
						}
						?>
							<a class="codereadr-default-button" href="<?php echo $integration['plugin_link']; ?>">How it works</a>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		
	</div>

	<div class="codereadr-modal">
		<div class="codereadr-modal__content" style="max-width: 900px">
			<div class="codereadr-modal__header">
				<h3> <?php _e( 'Custom Integration', 'codereadr' ); ?> </h3>
				<div class="codereadr-modal__close">
					<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
						<path fill="none" stroke="#000" stroke-width="2" d="M3,3 L21,21 M3,21 L21,3"></path>
					</svg>
				</div>
			</div>
	
			<div class="codereadr-modal__body">
			<h3> Copy the following code and insert it in your child theme </h3>
			<pre style="background: #272821; padding: 30px;">   
				<code class="language-php">
/**
 * Building custom integration for Codereadr
 */

use CodeReadr\Abstracts\Action;
use CodeReadr\Managers\Actions_Manager;


/**
* Let Codereadr know about my integration
* You should replace 'my-custom-integration' with your unique integration slug.
* And you should replace "My Custom Integration" with your integration title.
*/
add_filter(
  'codereadr_integrations',
  function( $integrations ) {
	$integrations['my-custom-integration'] = array(
	  'title' => 'My Custom Integration',
	);

	return $integrations;
  }
);


/**
* Adding an action for this integration via the following action class
*/
if ( ! class_exists( 'Codereadr_My_Custom_Action' ) ) {
  class Codereadr_My_Custom_Action extends Action {

	/**
	 * Action name.
	 * It must be a unique name.
	 *
	 * @var string
	*/
	public $name = 'my-custom-action';


	/**
	 * Integration slug
	 * This should be the same slug for your integraion unique slug.
	 *
	 * @var string
	*/
	public $integration_slug = 'my-custom-integration';

	/**
	 * Action title.
	 * The action title that will appear on admin dashboard.
	 *
	 * @var string
	*/
	public $title = 'Redeam Ticket';

	/**
	 * Action description.
	 * This is the action description that will appear on admin dashboard.
	 *
	 * @var string
	 */
	public $description = 'This is my custom action description';

	/**
	 * Default invalid conditions.
	 *
	 * @var array
	*/
	public $default_invalid_conditions = array(
	  'ticket_not_found' => array(
		'title'                 => 'If ticket is not found',
		'default'               => true,
		'default_response_text' => 'Ticket Not found',
	  ),
	);

	/**
	  * Allowable merge tags.
	  * The custom merge tags that are related to your action.
	  * The allowable merge tags for this action
	  *
	  * @var array
	*/
	public $allowable_merge_tags = array(
	  'full_name'  => array(
		'tag'         => '{full_name}',
		'description' => 'The user full name',
	  ),
	  'user_email' => array(
		'tag'         => '{user_email}',
		'description' => 'The user email',
	  ),
	);
	/**
	 * Process action.
	 * After processing any action we should set action data with a new value
	 * to be able to access it via handle_response method.
	*/
	public function process_action( $scan_data, $meta ) {
	  $ticket_id = $scan_data['tid'];

	  // Remove the hash from the following line and insert your query.
	  // $is_ticket_found = Do  Your Query Here!
	  if ( ! $is_ticket_found ) {
		$default_invalid_conditions = $meta['default_invalid_conditions'];
		$response_text = $default_invalid_conditions['ticket_not_found']['response_text'];
		return array(
		  'status' => 0,
		  'text'   => $response_text,
		);
	  }

	  $success_reponse_text = $meta['success_response_txt'];
	  return array(
		'status' => 1,
		'text'   => $success_reponse_text,
	  );

	  // At all cases you must return an array with
	  // "status" key with value of 0 in case on invalid response  and 1 for valid response.
	  // And "text" for the response text.
	}
  }
  Actions_Manager::instance()->register( new Codereadr_My_Custom_Action() );
}

					</code>
				</pre>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		hljs.highlightAll();
	})
</script>

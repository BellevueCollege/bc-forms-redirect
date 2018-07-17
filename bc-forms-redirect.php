<?php
/*
Plugin Name: Gravity Forms Redirect to Forms Server
Plugin URI: https://github.com/BellevueCollege/bc-forms-redirect
Description: Direct users to the forms server to edit or manage their forms
Author: Bellevue College Integration Team
Version: 1 #{versionStamp}#
Author URI: http://www.bellevuecollege.edu
GitHub Plugin URI: BellevueCollege/bc-forms-redirect
*/
defined( 'ABSPATH' ) || exit;


/**
 * Add Page to Menu
 */
add_action( 'admin_menu', 'bc_forms_redirect_menu' );

function bc_forms_redirect_menu() {
	add_menu_page( 
		'External Forms Server', 
		'Forms Server', 
		'read', 
		'bc-forms-redirect', 
		'bc_forms_redirect_menu_cb', 
		'dashicons-migrate', 
		10 
	); 
}

/**
 * Output page content
 */
function bc_forms_redirect_menu_cb() {
	if ( ! current_user_can( 'read' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} ?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p>Forms on this site have been moved to separate WordPress site dedicated to forms and forms administration. This allows Bellevue College to better secure potentially sensitive student information.</p>
		<?php if ( get_option('bc_forms_redirect_url') ) { ?>
			<p><a href="<?php echo get_option('bc_forms_redirect_url'); ?>" class="button button-primary">Access Forms Website</a></p>
		<?php } ?>
		<?php if ( is_super_admin() ) : ?>
			<hr>
			<form action="options.php" method="post" style="border: 2px solid pink; border-radius: 6px; background: #fff; padding: 1em;">
				<?php

				if ( isset( $_GET['settings-updated'] ) ) {
					// add settings saved message with the class of "updated"
					add_settings_error( 'bc_forms_redirect_message', 'bc_forms_redirect_message','Settings Saved', 'updated' );
				}
	
				// show error/update messages
				settings_errors( 'bc_forms_redirect_message' );

				// output security fields for the registered setting "wporg"
				settings_fields( 'bc_forms_redirect' );
				// output setting sections and their fields
				// (sections are registered for "wporg", each field is registered to a specific section)
				do_settings_sections( 'bc-forms-redirect' );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		
		<?php endif; ?>
	</div>
	
<?php }

function bc_forms_redirect_settings_init() {
	// register a new setting for "Forms Redirect" page
	register_setting( 
		'bc_forms_redirect',
		'bc_forms_redirect_url',
		'esc_url'
	);

	// register a new section in the "wporg" page
	add_settings_section(
		'bc_forms_redirect_settings',
		'Forms Redirect Settings',
		'',
		'bc-forms-redirect'
	);
 
	// register a new field in the  Forms Redirect Settings section
	add_settings_field(
		'bc_forms_redirect_url', // as of WP 4.6 this value is used only internally
		// use $args' label_for to populate the id inside the callback
		'URL of site on Forms server',
		'bc_forms_redirect_field_url_cb',
		'bc-forms-redirect',
		'bc_forms_redirect_settings',
		array( 
			'label_for' => 'bc_forms_redirect_url',
			'sanitize_callback' => 'esc_url'
		)
 );
}
 
/**
 * register our wporg_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'bc_forms_redirect_settings_init' );
 
/**
 * custom option and settings:
 * callback functions
 */
 
 
function bc_forms_redirect_field_url_cb( $args ) {
	
	?>
	<input type="text" name="bc_forms_redirect_url" id="bc_forms_redirect_url" value="<?php echo get_option('bc_forms_redirect_url'); ?>" />
	<?php
}


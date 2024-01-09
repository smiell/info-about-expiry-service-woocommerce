<?php

/**
 * Class ZhnGrupa_Expired_Service_Options
 *
 * Configure the plugin settings page.
 */
class ZhnGrupa_Expired_Service_Options {

	/**
	 * Capability required by the user to access the My Plugin menu entry.
	 *
	 * @var string $capability
	 */
	private $capability = 'manage_options';

	/**
	 * Array of fields that should be displayed in the settings page.
	 *
	 * @var array $fields
	 */
	private $fields = [
		[
			'id' => 'messageTitle',
			'label' => 'Email message title',
			'description' => 'Set message title wchih be sent',
			'type' => 'text',
		],
        [
			'id' => 'messageContent',
			'label' => 'Message content (HTML)',
			'description' => 'Message content to sent',
			'type' => 'wysiwyg',
		],
        [
			'id' => 'enableCuponGeneration',
			'label' => 'Enable discount code',
			'description' => 'When checked, discount codes will be genearted and you can use these in messages',
			'type' => 'checkbox',
		],
        [
			'id' => 'discountCodeAmount',
			'label' => 'Amount of discount code',
			'description' => 'for example.: 15% (DO NOT PUT % character here), only amount',
			'type' => 'text',
		],
	];

	/**
	 * The Plugin Settings constructor.
	 */
	function __construct() {
		add_action( 'admin_init', [$this, 'settings_init'] );
		add_action( 'admin_menu', [$this, 'options_page'] );
	}

	/**
	 * Register the settings and all fields.
	 */
	function settings_init() : void {

		// Register a new setting this page.
		register_setting( 'zhngrupa-expired-service', 'zhngrupa_expired_service' );


		// Register a new section.
		add_settings_section(
			'zhngrupa-expired-service-section',
			__( '', 'zhngrupa-expired-service' ),
			[$this, 'render_section'],
			'zhngrupa-expired-service'
		);


		/* Register All The Fields. */
		foreach( $this->fields as $field ) {
			// Register a new field in the main section.
			add_settings_field(
				$field['id'], /* ID for the field. Only used internally. To set the HTML ID attribute, use $args['label_for']. */
				__( $field['label'], 'zhngrupa-expired-service' ), /* Label for the field. */
				[$this, 'render_field'], /* The name of the callback function. */
				'zhngrupa-expired-service', /* The menu page on which to display this field. */
				'zhngrupa-expired-service-section', /* The section of the settings page in which to show the box. */
				[
					'label_for' => $field['id'], /* The ID of the field. */
					'class' => 'wporg_row', /* The class of the field. */
					'field' => $field, /* Custom data for the field. */
				]
			);
		}
	}

	/**
	 * Add a subpage to the WordPress Settings menu.
	 */
	function options_page() : void {
		add_submenu_page(
			'tools.php', /* Parent Menu Slug */
			'Expired Service Notifer', /* Page Title */
			'Expired service notifer', /* Menu Title */
			$this->capability, /* Capability */
			'zhngrupa-expired-service', /* Menu Slug */
			[$this, 'render_options_page'], /* Callback */
		);
	}

	/**
	 * Render the settings page.
	 */
	function render_options_page() : void {

		// check user capabilities
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'zhngrupa-expired-service' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'wporg_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="description">Set this options to get quick way to sending e-mails messages to customer about expired service that was bought in order</h2>
			<form action="options.php" method="post">
				<?php
				/* output security fields for the registered setting "wporg" */
				settings_fields( 'zhngrupa-expired-service' );
				/* output setting sections and their fields */
				/* (sections are registered for "wporg", each field is registered to a specific section) */
				do_settings_sections( 'zhngrupa-expired-service' );
				/* output save settings button */
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Args to configure the field.
	 */
	function render_field( array $args ) : void {

		$field = $args['field'];

		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'zhngrupa_expired_service' );

		switch ( $field['type'] ) {

			case "text": {
				?>
				<input
					type="text"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "checkbox": {
				?>
				<input
					type="checkbox"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="1"
					<?php echo isset( $options[ $field['id'] ] ) ? ( checked( $options[ $field['id'] ], 1, false ) ) : ( '' ); ?>
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "textarea": {
				?>
				<textarea
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
				><?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?></textarea>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "select": {
				?>
				<select
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
				>
					<?php foreach( $field['options'] as $key => $option ) { ?>
						<option value="<?php echo $key; ?>" 
							<?php echo isset( $options[ $field['id'] ] ) ? ( selected( $options[ $field['id'] ], $key, false ) ) : ( '' ); ?>
						>
							<?php echo $option; ?>
						</option>
					<?php } ?>
				</select>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "password": {
				?>
				<input
					type="password"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "wysiwyg": {
				wp_editor(
					isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : '',
					$field['id'],
					array(
						'textarea_name' => 'zhngrupa_expired_service[' . $field['id'] . ']',
						'textarea_rows' => 5,
					)
				);
                echo 'You can use these parameters in message Content:
                <li><strong>%customer_name%</strong> - which be replaced with customer first name</li>
                <li><strong>%date%</strong> - which be replaced by actuall date DD-MM-YY</li>
                <li><strong>%coupon%</strong> - which be replaced by generated discount code when enabled</li>
                <li><strong>%coupon_amount%</strong> - which be replaced by generated discount code amount when enabled</li>
                ';
				break;
			}

			case "email": {
				?>
				<input
					type="email"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "url": {
				?>
				<input
					type="url"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "color": {
				?>
				<input
					type="color"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

			case "date": {
				?>
				<input
					type="date"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="zhngrupa_expired_service[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], 'zhngrupa-expired-service' ); ?>
				</p>
				<?php
				break;
			}

		}
	}


	/**
	 * Render a section on a page, with an ID and a text label.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     An array of parameters for the section.
	 *
	 *     @type string $id The ID of the section.
	 * }
	 */
	function render_section( array $args ) : void {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( '', 'zhngrupa-expired-service' ); ?></p>
		<?php
	}

}



<?php
/**
 * Plugin Name:       Kalyvox AI Receptionist – Call Widget
 * Plugin URI:        https://kalyvox.ai/en/24-7-answering-service
 * Description:       Add a configurable click-to-call widget for your Kalyvox AI receptionist, plus a reusable shortcode for calls-to-action.
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            Kalyvox
 * Author URI:        https://kalyvox.ai/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kalyvox-ai-receptionist
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

final class Kalyvox_AI_Receptionist {
	const VERSION = '1.0.0';
	const OPTION_NAME = 'kalyvox_ai_receptionist_options';
	const PAGE_SLUG = 'kalyvox-ai-receptionist';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_assets' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render_floating_widget' ) );
		add_shortcode( 'kalyvox_call_button', array( __CLASS__, 'render_shortcode' ) );
	}

	public static function defaults() {
		return array( 'enabled' => 1, 'phone' => '', 'label' => '', 'position' => 'right', 'display' => 'all', 'color' => '#0f766e' );
	}

	public static function get_options() {
		$options = get_option( self::OPTION_NAME, array() );
		return wp_parse_args( is_array( $options ) ? $options : array(), self::defaults() );
	}

	public static function add_settings_page() {
		add_options_page( __( 'Kalyvox AI Receptionist', 'kalyvox-ai-receptionist' ), __( 'Kalyvox', 'kalyvox-ai-receptionist' ), 'manage_options', self::PAGE_SLUG, array( __CLASS__, 'render_settings_page' ) );
	}

	public static function register_settings() {
		register_setting( 'kalyvox_ai_receptionist', self::OPTION_NAME, array( 'type' => 'array', 'sanitize_callback' => array( __CLASS__, 'sanitize_options' ), 'default' => self::defaults() ) );
	}

	public static function sanitize_options( $input ) {
		$defaults = self::defaults();
		$input = is_array( $input ) ? $input : array();
		$phone = isset( $input['phone'] ) ? sanitize_text_field( wp_unslash( $input['phone'] ) ) : '';
		$phone = preg_replace( '/[^0-9+().\-\s]/', '', $phone );
		$label = isset( $input['label'] ) ? sanitize_text_field( wp_unslash( $input['label'] ) ) : '';
		$position = isset( $input['position'] ) ? sanitize_key( $input['position'] ) : $defaults['position'];
		$display = isset( $input['display'] ) ? sanitize_key( $input['display'] ) : $defaults['display'];
		$color = isset( $input['color'] ) ? sanitize_hex_color( $input['color'] ) : $defaults['color'];
		return array(
			'enabled' => empty( $input['enabled'] ) ? 0 : 1,
			'phone' => is_string( $phone ) ? trim( $phone ) : '',
			'label' => $label,
			'position' => in_array( $position, array( 'left', 'right' ), true ) ? $position : $defaults['position'],
			'display' => in_array( $display, array( 'all', 'mobile' ), true ) ? $display : $defaults['display'],
			'color' => $color ? $color : $defaults['color'],
		);
	}

	public static function enqueue_admin_assets( $hook_suffix ) {
		if ( 'settings_page_' . self::PAGE_SLUG === $hook_suffix ) {
			wp_enqueue_style( 'kalyvox-ai-receptionist-admin', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), self::VERSION );
		}
	}

	public static function enqueue_frontend_assets() {
		wp_enqueue_style( 'kalyvox-ai-receptionist', plugins_url( 'assets/css/widget.css', __FILE__ ), array(), self::VERSION );
	}

	public static function render_floating_widget() {
		$options = self::get_options();
		if ( ! empty( $options['enabled'] ) && ! empty( $options['phone'] ) ) {
			echo self::get_button_markup( $options ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public static function render_shortcode( $atts ) {
		$options = self::get_options();
		$atts = shortcode_atts( array( 'label' => '', 'phone' => '' ), $atts, 'kalyvox_call_button' );
		if ( '' !== $atts['phone'] ) { $options['phone'] = sanitize_text_field( $atts['phone'] ); }
		if ( '' !== $atts['label'] ) { $options['label'] = sanitize_text_field( $atts['label'] ); }
		if ( empty( $options['phone'] ) ) { return ''; }
		$options['position'] = 'inline';
		$options['display'] = 'all';
		return self::get_button_markup( $options );
	}

	private static function get_button_markup( $options ) {
		$phone = preg_replace( '/[^0-9+]/', '', (string) $options['phone'] );
		if ( ! $phone ) { return ''; }
		$label = ! empty( $options['label'] ) ? $options['label'] : __( 'Call us', 'kalyvox-ai-receptionist' );
		$color = sanitize_hex_color( $options['color'] );
		$classes = array( 'kalyvox-call-widget' );
		if ( 'inline' === $options['position'] ) {
			$classes[] = 'kalyvox-call-widget--inline';
		} else {
			$classes[] = 'kalyvox-call-widget--floating';
			$classes[] = 'left' === $options['position'] ? 'kalyvox-call-widget--left' : 'kalyvox-call-widget--right';
		}
		if ( 'mobile' === $options['display'] ) { $classes[] = 'kalyvox-call-widget--mobile-only'; }
		$svg = '<svg aria-hidden="true" viewBox="0 0 24 24" focusable="false"><path d="M6.62 10.79a15.46 15.46 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24 11.36 11.36 0 0 0 3.57.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.57 1 1 0 0 1-.25 1.02l-2.2 2.2Z"/></svg>';
		return sprintf( '<a class="%1$s" href="tel:%2$s" style="--kalyvox-widget-color:%3$s" aria-label="%4$s">%5$s<span>%6$s</span></a>', esc_attr( implode( ' ', $classes ) ), esc_attr( $phone ), esc_attr( $color ? $color : '#0f766e' ), esc_attr( $label ), $svg, esc_html( $label ) );
	}

	private static function get_kalyvox_url( $destination = 'home' ) {
		$is_french = 0 === strpos( determine_locale(), 'fr_' );
		$base = $is_french ? 'https://kalyvox.ai/' : 'https://kalyvox.ai/en/';
		if ( 'help' === $destination ) { $base = $is_french ? 'https://kalyvox.ai/aide' : 'https://kalyvox.ai/en/help'; }
		return $base;
	}

	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		$options = self::get_options();
		$default_label = __( 'Call us', 'kalyvox-ai-receptionist' );
		$preview_label = ! empty( $options['label'] ) ? $options['label'] : $default_label;
		?>
		<div class="wrap kalyvox-admin">
			<div class="kalyvox-admin__hero">
				<div>
					<div class="kalyvox-admin__brand">Kalyvox</div>
					<h1><?php esc_html_e( 'Turn website visitors into phone conversations.', 'kalyvox-ai-receptionist' ); ?></h1>
					<p><?php esc_html_e( 'Add a clear call button to your WordPress site and send callers to your Kalyvox AI receptionist. Kalyvox can answer 24/7, qualify requests, handle common questions, transfer calls, book appointments and send your team structured summaries and alerts.', 'kalyvox-ai-receptionist' ); ?></p>
					<div class="kalyvox-admin__actions"><a class="button button-primary" href="<?php echo esc_url( self::get_kalyvox_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Set up my AI receptionist', 'kalyvox-ai-receptionist' ); ?></a><a class="button" href="<?php echo esc_url( self::get_kalyvox_url( 'help' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Kalyvox help center', 'kalyvox-ai-receptionist' ); ?></a></div>
				</div>
				<div class="kalyvox-admin__feature-card" aria-hidden="true"><div class="kalyvox-admin__feature-row"><span>24/7</span><?php esc_html_e( 'Answers every call', 'kalyvox-ai-receptionist' ); ?></div><div class="kalyvox-admin__feature-row"><span>AI</span><?php esc_html_e( 'Qualifies the request', 'kalyvox-ai-receptionist' ); ?></div><div class="kalyvox-admin__feature-row"><span>→</span><?php esc_html_e( 'Routes or books when needed', 'kalyvox-ai-receptionist' ); ?></div><div class="kalyvox-admin__feature-row"><span>✓</span><?php esc_html_e( 'Sends a structured summary', 'kalyvox-ai-receptionist' ); ?></div></div>
			</div>
			<div class="kalyvox-admin__steps"><div><strong>1</strong><span><?php esc_html_e( 'Get your Kalyvox phone number.', 'kalyvox-ai-receptionist' ); ?></span></div><div><strong>2</strong><span><?php esc_html_e( 'Paste it below and customize the button.', 'kalyvox-ai-receptionist' ); ?></span></div><div><strong>3</strong><span><?php esc_html_e( 'Save, visit your site and place a test call.', 'kalyvox-ai-receptionist' ); ?></span></div></div>
			<div class="kalyvox-admin__grid">
				<div class="kalyvox-admin__panel"><h2><?php esc_html_e( 'Call widget settings', 'kalyvox-ai-receptionist' ); ?></h2><p class="description"><?php esc_html_e( 'No Kalyvox data is loaded on your website. The plugin only creates a standard tel: link to the number you configure.', 'kalyvox-ai-receptionist' ); ?></p>
				<form method="post" action="options.php"><?php settings_fields( 'kalyvox_ai_receptionist' ); ?>
				<div class="kalyvox-field kalyvox-field--toggle"><label><input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enabled]" value="1" <?php checked( 1, $options['enabled'] ); ?>><span><strong><?php esc_html_e( 'Enable floating call widget', 'kalyvox-ai-receptionist' ); ?></strong><small><?php esc_html_e( 'You can still use the shortcode even when the floating widget is disabled.', 'kalyvox-ai-receptionist' ); ?></small></span></label></div>
				<div class="kalyvox-field"><label for="kalyvox-phone"><?php esc_html_e( 'Phone number', 'kalyvox-ai-receptionist' ); ?></label><input id="kalyvox-phone" class="regular-text" type="tel" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[phone]" value="<?php echo esc_attr( $options['phone'] ); ?>" placeholder="+33 1 23 45 67 89"><p class="description"><?php esc_html_e( 'Use the Kalyvox number that answers your inbound calls. International format is recommended.', 'kalyvox-ai-receptionist' ); ?></p></div>
				<div class="kalyvox-field"><label for="kalyvox-label"><?php esc_html_e( 'Button label', 'kalyvox-ai-receptionist' ); ?></label><input id="kalyvox-label" class="regular-text" type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[label]" value="<?php echo esc_attr( $options['label'] ); ?>" placeholder="<?php echo esc_attr( $default_label ); ?>" maxlength="80"><p class="description"><?php esc_html_e( 'Leave blank to use the translated default label.', 'kalyvox-ai-receptionist' ); ?></p></div>
				<div class="kalyvox-admin__two-cols"><div class="kalyvox-field"><label><?php esc_html_e( 'Position', 'kalyvox-ai-receptionist' ); ?></label><select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[position]"><option value="right" <?php selected( 'right', $options['position'] ); ?>><?php esc_html_e( 'Bottom right', 'kalyvox-ai-receptionist' ); ?></option><option value="left" <?php selected( 'left', $options['position'] ); ?>><?php esc_html_e( 'Bottom left', 'kalyvox-ai-receptionist' ); ?></option></select></div><div class="kalyvox-field"><label><?php esc_html_e( 'Visibility', 'kalyvox-ai-receptionist' ); ?></label><select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[display]"><option value="all" <?php selected( 'all', $options['display'] ); ?>><?php esc_html_e( 'Desktop and mobile', 'kalyvox-ai-receptionist' ); ?></option><option value="mobile" <?php selected( 'mobile', $options['display'] ); ?>><?php esc_html_e( 'Mobile only', 'kalyvox-ai-receptionist' ); ?></option></select></div></div>
				<div class="kalyvox-field"><label><?php esc_html_e( 'Button color', 'kalyvox-ai-receptionist' ); ?></label><div class="kalyvox-color-field"><input type="color" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[color]" value="<?php echo esc_attr( $options['color'] ); ?>"><code><?php echo esc_html( $options['color'] ); ?></code></div></div>
				<?php submit_button( __( 'Save widget settings', 'kalyvox-ai-receptionist' ) ); ?></form></div>
				<div><div class="kalyvox-admin__panel kalyvox-admin__preview"><h2><?php esc_html_e( 'Preview', 'kalyvox-ai-receptionist' ); ?></h2><p><?php esc_html_e( 'The public widget uses your theme fonts and stays above page content.', 'kalyvox-ai-receptionist' ); ?></p><div class="kalyvox-admin__preview-stage"><div class="kalyvox-admin__preview-button" style="--kalyvox-preview-color: <?php echo esc_attr( $options['color'] ); ?>"><span aria-hidden="true">☎</span><?php echo esc_html( $preview_label ); ?></div></div></div>
				<div class="kalyvox-admin__panel"><h2><?php esc_html_e( 'Use it inside a page', 'kalyvox-ai-receptionist' ); ?></h2><p><?php esc_html_e( 'Add the configured call button anywhere WordPress accepts shortcodes:', 'kalyvox-ai-receptionist' ); ?></p><code>[kalyvox_call_button]</code><p><?php esc_html_e( 'Optional overrides:', 'kalyvox-ai-receptionist' ); ?></p><code>[kalyvox_call_button label=&quot;Call sales&quot; phone=&quot;+33123456789&quot;]</code></div>
				<div class="kalyvox-admin__panel kalyvox-admin__note"><h2><?php esc_html_e( 'Not a Kalyvox customer yet?', 'kalyvox-ai-receptionist' ); ?></h2><p><?php esc_html_e( 'The widget works with any callable phone number. With Kalyvox behind that number, callers can be answered and qualified automatically even when your team is unavailable.', 'kalyvox-ai-receptionist' ); ?></p><a href="<?php echo esc_url( self::get_kalyvox_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Discover Kalyvox', 'kalyvox-ai-receptionist' ); ?> →</a></div></div>
			</div>
		</div><?php
	}
}

Kalyvox_AI_Receptionist::init();

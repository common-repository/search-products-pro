<?php
/*
Plugin Name: Search Products PRO
Plugin URI: https://search-products.com
Description: WooCommerce products search PRO plugin, the best and the fastest AJAX search bar for WooCommerce.
Author: SearchProducts
License: GPLv2 or later
Version: 1.0.0
Requires at least: 4.5
Tested up to: 5.9.9
WC requires at least: 3.0.0
WC tested up to: 4.9.9
Author URI: https://search-products.com
Text Domain: search-products
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'SearchProductsPRO' ) ) :

	final class SearchProductsPRO {

		public static $version = '1.0.0';

		protected static $_instance = null;

		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			$this->hooks();
			$this->includes();
		}

		public function activate() {
			if ( !class_exists( 'WooCommerce' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );

				wp_die( esc_html__( 'This plugin requires WooCommerce. Download it from WooCommerce official website', 'search-products' ) . ' &rarr; https://search-products.com' );
				exit;
			}
		}

		public function load_srchpro() {
			if ( $this->is_request( 'admin' ) ) {
				include_once ( 'includes/settings/settings.php' );
			}
		}

		private function is_request( $type ) {
			switch ( $type ) {

				case 'admin':
					return is_admin();

				case 'ajax':
					return defined( 'DOING_AJAX' );

				case 'cron':
					return defined( 'DOING_CRON' );

				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );

			}
		}

		public function hooks() {
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			add_action( 'plugins_loaded', array( $this, 'load_elementor' ) );

			add_action( 'init', array( $this, 'textdomain' ), 0 );

			add_action( 'init', array( $this, 'load_srchpro' ), 100 );
		}

		public function includes() {
			include_once( 'includes/settings/get.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/class-settings.php' );
			}
			
			include_once( 'includes/class-front.php' );

			$this->load_wpbakery();
		}

		public function load_wpbakery() {
			include_once( 'includes/integrations/wpbakery.php' );
		}

		public function load_elementor() {
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'load_elementor_widget' ) );
		}
		
		public function load_elementor_widget() {
			require_once( 'includes/integrations/elementor.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_Search_Products_PRO() );
		}

		public function textdomain() {
			$this->load_plugin_textdomain();
		}

		public function load_plugin_textdomain() {
			$domain = 'search-products';
			$dir    = untrailingslashit( WP_LANG_DIR );

			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			$loaded = load_textdomain( $domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo' );

			if ( empty( $loaded ) ) {
				load_plugin_textdomain( $domain, false, plugin_dir_path( __FILE__ ) . '/lang' );
			}

			return $loaded;
		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function plugin_basename() {
			return untrailingslashit( plugin_basename( __FILE__ ) );
		}

		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		public function version() {
			return self::$version;
		}

	}

	function SearchProductsPRO() {
		return SearchProductsPRO::instance();
	}

	SearchProductsPRO::instance();

endif;

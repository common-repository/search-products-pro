<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'SearchProductsPRO_Get' ) ) {

	class SearchProductsPRO_Get {

		public static $version = '1.5.0';

		protected static $_instance = null;

		public static $settings = array();

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function get_option_autoload( $option, $default = '' ) {
			if ( isset( self::$settings['autoload'] ) ) {
				if ( isset( self::$settings['autoload'][$option] ) ) {
					return self::$settings['autoload'][$option];
				}
				
				if ( $default ) {
					return $default;
				}

				return false;
			}

			$options = get_option( 'srchpro_autoload', false );

			if ( false !== $options ) {
				self::$settings['autoload'] = $options;

				if ( isset( $options[$option] ) ) {
					return $options[$option];					
				}
			}

			if ( $default ) {
				return $default;
			}

			return false;
		}

		public function get_option( $option, $plugin, $default = '' ) {
			if ( isset( self::$settings[$plugin] ) ) {
				if ( isset( self::$settings[$plugin][$option] ) ) {
					return self::$settings[$plugin][$option];
				}
				
				if ( $default ) {
					return $default;
				}

				return false;
			}
			
			$options = get_option( 'srchpro_settings_' . $plugin, false );

			if ( false !== $options ) {
				self::$settings[$plugin] = $options;

				if ( isset( $options[$option] ) ) {
					return $options[$option];					
				}
			}

			if ( $default ) {
				return $default;
			}

			return false;
		}


	}

	function SearchProductsPRO_Get() {
		return SearchProductsPRO_Get::instance();
	}

	SearchProductsPRO_Get::instance();

}

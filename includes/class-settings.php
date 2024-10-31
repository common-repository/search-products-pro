<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchProductsPRO_Settings {

	public static $plugin;

	public static function init() {

		self::$plugin = array(
			'name' => 'Search Products PRO',
			'slug' => 'search-products-pro',
			'label' => 'search_products_pro',
			'image' => '',
			'path' => 'search-products-pro/search-products-pro',
			'version' => SearchProductsPRO::$version,
		);

		$page = isset( $_REQUEST['page'] ) && 'search_products_pro' == $_REQUEST['page'] ? true : false;

		if ( $page ) {
			add_filter( 'srchpro_plugins_settings', array( 'SearchProductsPRO_Settings', 'get_settings' ), 50 );
		}

		add_filter( 'srchpro_plugins', array( 'SearchProductsPRO_Settings', 'add_plugin' ), 0 );

	}

	public static function add_plugin( $plugins ) {
		$plugins[self::$plugin['label']] = array(
			'slug' => self::$plugin['label'],
			'name' => self::$plugin['name']
		);

		return $plugins;
	}

	public static function get_settings( $plugins ) {

		$plugins[self::$plugin['label']] = array(
			'slug' => self::$plugin['label'],
			'name' => esc_html( self::$plugin['name'] ),
			'desc' => esc_html( 'Settings page', 'search-products' ),
			'link' => esc_url( 'https://search-products.com' ),
			'ref' => array(
				'name' => esc_html__( 'Visit Search-Products.com', 'search-products' ),
				'url' => 'https://search-products.com'
			),
			'doc' => array(
				'name' => esc_html__( 'Get help', 'search-products' ),
				'url' => 'https://search-products.com'
			),
			'sections' => array(
				'dashboard' => array(
					'name' => esc_html__( 'How to use?', 'search-products' ),
					'desc' => esc_html__( 'How to use this plugin?', 'search-products' ),
				),
				'terms' => array(
					'name' => esc_html__( 'Product Terms', 'search-products' ),
					'desc' => esc_html__( 'Configure product terms search options', 'search-products' ),
				),
				'sku' => array(
					'name' => esc_html__( 'Product SKU', 'search-products' ),
					'desc' => esc_html__( 'Configure product sku search options', 'search-products' ),
				),
				'meta' => array(
					'name' => esc_html__( 'Product Meta', 'search-products' ),
					'desc' => esc_html__( 'Configure product meta search options', 'search-products' ),
				),
				'customize' => array(
					'name' => esc_html__( 'Customize Plugin', 'search-products' ),
					'desc' => esc_html__( 'Customize the look of the plugin', 'search-products' ),
				),
				'general' => array(
					'name' => esc_html__( 'Plugin Options', 'search-products' ),
					'desc' => esc_html__( 'Set plugin options', 'search-products' ),
				),
			),
			'settings' => array(

				'_dashboard' => array(
					'type' => 'html',
					'id' => '_dashboard',
					'desc' => '	
							<h3>' . esc_html__( 'Hi!', 'search-products' ) . '</h3>
							<p>' . esc_html__( 'Visit Search Products PRO website, demos and knowledge base.', 'search-products' ) . '</p>
							<p><a href="https://search-products.com" class="srchpro-button-primary x-color" target="_blank">' . esc_html__( 'Visit Documentation Pages', 'search-products' ) . '</a></p>
						',
					'section' => 'dashboard',
				),

				'_utility' => array(
					'name' => esc_html__( 'Plugin Options', 'search-products' ),
					'type' => 'utility',
					'id' => '_utility',
					'desc' => esc_html__( 'Quick export/import, backup and restore, or just reset your optons here', 'search-products' ),
					'section' => 'dashboard',
				),

				'characters' => array(
					'name' => esc_html__( 'Characters to Search', 'search-products' ),
					'type' => 'number',
					'desc' => esc_html__( 'Trigger search when number of characters is reached', 'search-products' ),
					'id'   => 'characters',
					'autoload' => false,
					'default' => '',
					'section' => 'general'
				),

				'products' => array(
					'name' => esc_html__( 'Products to Display', 'search-products' ),
					'type' => 'number',
					'desc' => esc_html__( 'Enter how many product to display after search', 'search-products' ),
					'id'   => 'products',
					'autoload' => false,
					'default' => '',
					'section' => 'general'
				),

				'separator' => array(
					'name' => esc_html__(  'Category Separator', 'search-products' ),
					'type' => 'text',
					'desc' => esc_html__( 'Enter category separator', 'search-products' ),
					'id'   => 'separator',
					'autoload' => false,
					'translate' => true,
					'default' => '',
					'section' => 'general'
				),

				'placeholder' => array(
					'name' => esc_html__(  'Placeholder', 'search-products' ),
					'type' => 'text',
					'desc' => esc_html__( 'Enter placeholder text', 'search-products' ),
					'id'   => 'placeholder',
					'autoload' => false,
					'translate' => true,
					'default' => '',
					'section' => 'customize'
				),

				'notfound' => array(
					'name' => esc_html__(  'No Products Found', 'search-products' ),
					'type' => 'text',
					'desc' => esc_html__( 'Enter no products found message', 'search-products' ),
					'id'   => 'notfound',
					'autoload' => false,
					'translate' => true,
					'default' => '',
					'section' => 'customize'
				),

				'_icon_callout' => array(
					'name' => esc_html__(  'Call Out Icon', 'search-products' ),
					'type' => 'file',
					'desc' => esc_html__( 'Replace element call out icon', 'search-products' ),
					'id'   => '_icon_callout',
					'autoload' => false,
					'default' => '',
					'section' => 'customize'
				),

				'_icon_search' => array(
					'name' => esc_html__(  'Search Icon', 'search-products' ),
					'type' => 'file',
					'desc' => esc_html__( 'Replace element search icon', 'search-products' ),
					'id'   => '_icon_search',
					'autoload' => false,
					'default' => '',
					'section' => 'customize'
				),

				'_icon_dismiss' => array(
					'name' => esc_html__(  'Dismiss Icon', 'search-products' ),
					'type' => 'file',
					'desc' => esc_html__( 'Replace element dismiss icon', 'search-products' ),
					'id'   => '_icon_dismiss',
					'autoload' => false,
					'default' => '',
					'section' => 'customize'
				),

				'taxonomies' => array(
					'name' => esc_html__( 'Product terms', 'search-products' ),
					'type' => 'multiselect',
					'desc' => esc_html__( 'Select product terms to search in. Select product categories, tags, attributes and more to include in product terms search. USE CMD+CLICK TO SELECT MULTIPLE!', 'search-products' ),
					'section' => 'terms',
					'id'   => 'taxonomies',
					'options' => 'ajax:product_taxonomies',
					'default' => '',
					'autoload' => false,
				),

				'interval' => array(
					'name' => esc_html__( 'Product Terms Cache', 'search-products' ),
					'type' => 'select',
					'desc' => esc_html__( 'Set product terms cache interval.', 'search-products' ),
					'section' => 'terms',
					'id'   => 'interval',
					'default' => 'saved',
					'options' => array(
						'saved' => esc_html__( 'When plugin options are saved', 'search-products' ),
						'hourly' => esc_html__( 'Once hourly', 'search-products' ),
						'twicedaily' => esc_html__( 'Twice daily', 'search-products' ),
						'daily' => esc_html__( 'Once daily', 'search-products' ),
					),
					'autoload' => false,
				),

				'_sku' => array(
					'name' => esc_html__( 'Product SKU', 'search-products' ),
					'type' => 'checkbox',
					'desc' => esc_html__( 'Enable product SKU search.', 'search-products' ),
					'section' => 'sku',
					'id'   => '_sku',
					'default' => 'no',
					'autoload' => false,
				),

				'_sku_compare' => array(
					'name' => esc_html__( 'Product SKU Compare', 'search-products' ),
					'type' => 'select',
					'desc' => esc_html__( 'Select product SKU compare method', 'search-products' ),
					'section' => 'sku',
					'id'   => '_sku_compare',
					'options' => array(
						'and' => esc_html__( 'Exact', 'search-products' ),
						'like' => esc_html__( 'Like', 'search-products' ),
					),
					'default' => '',
					'autoload' => false,
				),

				'meta_keys' => array(
					'name' => esc_html__( 'Product Meta', 'search-products' ),
					'type' => 'list',
					'desc' => esc_html__( 'Add product meta keys to perform search in.', 'search-products' ),
					'section' => 'meta',
					'id'   => 'meta_keys',
					'options' => 'list',
					'settings' => array(
						'name' => array(
							'name' => esc_html__(  'Title', 'search-products' ),
							'type' => 'text',
							'desc' => esc_html__( 'Enter title', 'search-products' ),
							'id'   => 'name',
							'default' => '',
						),
						'_key' => array(
							'name' => esc_html__(  'Meta Key', 'search-products' ),
							'type' => 'text',
							'desc' => esc_html__( 'Enter product meta key to perform search in', 'search-products' ),
							'id'   => '_key',
							'default' => '',
						),
						'_compare' => array(
							'name' => esc_html__( 'Meta Compare', 'search-products' ),
							'type' => 'select',
							'desc' => esc_html__( 'Select product meta compare method', 'search-products' ),
							'id'   => '_compare',
							'options' => array(
								'and' => esc_html__( 'Exact', 'search-products' ),
								'like' => esc_html__( 'Like', 'search-products' ),
							),
							'default' => '',
						),
					),
					'default' => '',
					'autoload' => false,
						
				),

			),
		);

		return SearchProducts()->_do_options( $plugins, self::$plugin['label'] );
	}

}

	SearchProductsPRO_Settings::init();

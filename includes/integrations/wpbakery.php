<?php

if ( function_exists( '_spp__integration_wpbakery' ) ) {
	return false;
}

add_action( 'vc_before_init', '_spp__integration_wpbakery' );

function _spp__integration_wpbakery() {

	vc_map( array(
		'name' => esc_html__( 'Search Products PRO', 'search-products' ),
		'base' => 'search_products_pro',
		'class' => '',
		'category' => esc_html__( 'Content', 'search-products'),

		'params' => array(
			array(
				'type' => 'textfield',
				'holder' => 'div',
				'class' => '',
				'heading' => __( 'ID', 'search-products' ),
				'param_name' => 'id',
				'value' => '',
				'description' => esc_html__( 'Customize shortcode element ID', 'search-products' )
			),
			array(
				'type' => 'textfield',
				'holder' => 'div',
				'class' => '',
				'heading' => __( 'Class', 'search-products' ),
				'param_name' => 'class',
				'value' => '',
				'description' => esc_html__( 'Customize shortcode element class', 'search-products' )
			),
			array(
				'type' => 'textfield',
				'holder' => 'div',
				'class' => '',
				'heading' => __( 'Search in category', 'search-products' ),
				'param_name' => 'category',
				'value' => '',
				'description' => esc_html__( 'Enter category slug to search in. Default is search in all categories', 'search-products' )
			),
			array(
				'type' => 'dropdown',
				'holder' => 'div',
				'class' => '',
				'heading' => __( 'Element method', 'search-products' ),
				'param_name' => 'callout',
				'options' => array(
					'yes', 'no'
				),
				'value' => '',
				'description' => esc_html__( 'Use default or call out element', 'search-products' )
			),
		),
	) );

}

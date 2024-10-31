<?php
/**
 * Elementor Search Products PRO widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */

use Elementor\Widget_Base;

class Elementor_Search_Products_PRO extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Search Products PRO widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'search-products-pro';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Search Products PRO widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Search Products PRO', 'search-products' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Search Products PRO widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-search';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Search Products PRO widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Register Search Products PRO widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Element controls', 'search-products' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'id',
			[
				'label' => esc_html__( 'ID', 'search-products' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'type' => 'text',
				'description' => esc_html__( 'Customize shortcode element ID', 'search-products' ),
			]
		);

		$this->add_control(
			'class',
			[
				'label' => esc_html__( 'Class', 'search-products' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'type' => 'text',
				'description' => esc_html__( 'Customize shortcode element class', 'search-products' ),
			]
		);

		$this->add_control(
			'category',
			[
				'label' => esc_html__( 'Search in category', 'search-products' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'type' => 'text',
				'description' => esc_html__( 'Enter category slug to search in. Default is search in all categories', 'search-products' ),
			]
		);

		$this->add_control(
			'callout',
			[
				'label' => esc_html__( 'Element method', 'search-products' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'type' => 'select',
				'options' => array(
					'no' => esc_html__( 'Default', 'search-prodcuts' ),
					'yes' => esc_html__( 'Call out', 'search-prodcuts' ),
				),
				'description' => esc_html__( 'Use default or call out element', 'search-products' ),
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render Search Products PRO widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		echo do_shortcode( sprintf( '[search_products_pro id="%s" class="%s" category="%s" callout="%s"]', $settings['id'], $settings['class'], $settings['category'], $settings['callout'] ) );

	}

}

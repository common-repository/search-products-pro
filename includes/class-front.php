<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class SearchProductsPRO_Frontend {

	protected static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
			
		return self::$_instance;
			
	}

	private function __construct() {
		$this->init_front();
	}

	private function init_front() {
		add_shortcode( 'search_products_pro', array( $this, 'shortcode' ) );

		add_action( 'wp_ajax_nopriv_search_products_pro', array( $this, 'respond' ) );
		add_action( 'wp_ajax_search_products_pro', array( $this, 'respond' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_footer', array( $this, 'check_scripts' ) );

		add_filter( 'posts_search', array( $this, '_extended_search' ), 99999, 2 );

		add_action( 'search_products_pro_taxonomies_cache', array( $this, '_taxonomies_cache' ) );
		add_action( 'srchpro_ajax_saved_settings_search_products_pro', array( $this, '_taxonomies_cache' ) );
	}

	public function scripts() {
		wp_register_script( 'search-products-pro-js', SearchProductsPRO()->plugin_url() . '/assets/js/scripts.js', array( 'jquery' ), SearchProductsPRO()->version(), true );
		wp_enqueue_script( 'search-products-pro-js' );
			
		wp_register_style ( 'search-products-pro-css', SearchProductsPRO()->plugin_url() . '/assets/css/styles' . ( is_rtl() ? '-rtl' : '' ) . '.css', false, SearchProductsPRO()->version() );
		wp_enqueue_style( 'search-products-pro-css' );
	}

	public function check_scripts() {
		if ( wp_script_is( 'search-products-pro-js', 'enqueued' ) ) {
			$args = array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'characters' => absint( SearchProductsPRO_Get()->get_option( 'characters', 'search_products_pro', 2 ) ),
				'localize' => array(
					'notfound' => esc_html( $this->_get_notfound() ),
				),
				'es' => get_option( '_srchpro_settings_search_products_pro_cache', array() ),
			);
	
			wp_localize_script( 'search-products-pro-js', 'sp', $args );
		}
	
	}

	public function shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'id'       => '',
			'class'    => '',
			'category' => '',
			'callout'  => 'no',
		), $atts );
			
		return $this->_get_live_search_element( $atts );
	}

	public function _get_notfound() {
		$notfound = SearchProductsPRO_Get()->get_option( 'notfound', 'search_products_pro', '' );

		if ( empty( $notfound ) ) {
			return esc_html__( 'No products found', 'search-products-pro' );
		}

		return $notfound;
	}

	public function _get_placeholder() {
		$placeholder = SearchProductsPRO_Get()->get_option( 'placeholder', 'search_products_pro', '' );

		if ( empty( $placeholder ) ) {
			return esc_html__( 'Search products', 'search-products-pro' );
		}
			
		return $placeholder;
	}

	public function _get_products() {
		$num = absint( SearchProductsPRO_Get()->get_option( 'products', 'search_products_pro', 10 ) );
		  
		return $num > 0 ? $num : 10;
	}

	public function _get_separator() {
		$separator = SearchProductsPRO_Get()->get_option( 'separator', 'search_products_pro', '' );

		if ( '' == $separator ) {
			$separator = ( is_rtl() ? '<' : '>' );
		}

		return '<span class="spp--separator">' . esc_html( $separator ) . '</span>';
	}

	public function _build_query() {
		check_ajax_referer( 'search-products-pro-nounce', 'nonce' );

		$string = '';
		$category = '';

		if ( isset( $_POST['settings'] ) ) {
			$string = sanitize_text_field( isset( $_POST['settings'][0] ) ? (string) $_POST['settings'][0] : '' );
			$category = sanitize_title( isset( $_POST['settings'][1] ) ? (string) $_POST['settings'][1] : '' );
		}

		$query = array(
			'spp__active'      => true,
			'post_status'  => 'publish',
			's'            => $string,
			'orderby'      => 'relevance',
			'limit'        => $this->_get_products(),
		);

		if ( !empty( $category ) ) {
			$query['category'] = array( $category );
		}

		return apply_filters( 'search_products_pro_query', $query );
	}

	public function respond() {
		$products = array();

		$query = wc_get_products( $this->_build_query() );

		if ( $query ) {
			foreach ( $query as $product ) {
				$products[] = array(
					'id' => absint( $product->get_id() ),
					'path' => wp_kses_post( $this->_get_trail( $product->get_category_ids() ) ),
					'title' => wp_kses_post( $this->_get_separator() . '<a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $product->get_title() ) . '</a>' ),
					'image' => wp_kses_post( '<a href="' . esc_url( $product->get_permalink() ) . '">' . $product->get_image() . '</a>' ),
					'price' => strip_tags( $product->get_price_html(), '<del>' ),
				);
			}
		}

		wp_send_json( $products );
		exit; 
	}

	public function _get_trail( $ids ) {
		if ( $ids[0] ) {
			$term_id = $ids[0];
		}

		while ( $term_id ) {
			$term = get_term( $term_id, 'product_cat' );

			$parents[] = sprintf( '<a href="%s">%s</a>', esc_url( get_term_link( $term, 'product_cat' ) ), $term->name );

			$term_id = $term->parent;
		}

		array_reverse( $parents );

		return implode( $this->_get_separator(), $parents );
	}
		
	public function _get_customized_icon( $icon ) {
		$replace = SearchProductsPRO_Get()->get_option( '_icon_' . $icon, 'search_products_pro', '' );

		if ( ! empty( $replace ) ) {
			?><img class="<?php echo esc_attr( 'spp--' . $icon ); ?>" width="48" height="48" src="<?php echo esc_url( $replace ); ?>" alt="<?php esc_attr_e( 'Search bar icon', 'search-products' ); ?>" />
				<?php
		}
	}

	public function _get_live_search_element( $atts ) {
		$id = $this->_get_element_id( $atts['id'] );
			
		ob_start();
			
		if ( 'yes' == $atts['callout'] ) {
			?>
				<div class="spp--callout" data-callout="<?php esc_attr_e( $id ); ?>"><?php $this->_get_customized_icon( 'callout' ); ?></div>
<?php
		}
		?>
			<div id="<?php esc_attr_e( $id ); ?>" class="<?php esc_attr_e( $this->_get_element_class( $atts['class'] ) ); ?>" data-category="<?php esc_attr_e( $this->_get_element_category( $atts['category'] ) ); ?>" data-nonce="<?php esc_attr_e( wp_create_nonce( 'search-products-pro-nounce' ) ); ?>">
				<input class="spp--input" type="text" placeholder="<?php esc_attr_e( $this->_get_placeholder() ); ?>"/>

				<button class="spp--button"><?php $this->_get_customized_icon( 'search' ); ?><?php $this->_get_customized_icon( 'dismiss' ); ?></button>
			</div>
<?php
		return ob_get_clean();
	}

	public function _get_element_id( $id ) {
		if ( !empty(  $id ) ) {
		   return  $id;
		}

		return uniqid( 'spp--' );
	}

	public function _get_element_class( $class ) {
		if ( !empty( $class ) ) {
			return 'spp--element ' . $class;
		}

		return 'spp--element';
	}

	public function _get_element_category( $category ) {
		$category = sanitize_title( $category );

		if ( term_exists( $category, 'product_cat' ) ) {
			return $category;
		}

		return '';
	}

	public function _cache_start( $options ) {
		$job = wp_next_scheduled( 'search_products_pro_taxonomies_cache' );

		if ( ! empty( SearchProductsPRO_Get()->get_option( 'taxonomies', 'search_products_pro', array() ) ) ) {
			if ( $job ) {
				wp_unschedule_event( $job, 'search_products_pro_taxonomies_cache' );
			}

			wp_schedule_event( time(), SearchProductsPRO_Get()->get_option( 'interval', 'search_products_pro', 'saved' ), 'search_products_pro_taxonomies_cache' );
		} else {
			if ( $job ) {
				wp_unschedule_event( $job, 'search_products_pro_taxonomies_cache' );
			}
		}
	}

	public function _taxonomies_cache() {
		$cache = array();
		$taxonomies = SearchProductsPRO_Get()->get_option( 'taxonomies', 'search_products_pro', array() );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms( $taxonomy, array(
				'hide_empty' => false,
				'fields' => 'id=>name',
			) );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $id => $name ) {
					$cache[] = array(
						$id,
						$name,
						$taxonomy,
					);
				}
			}
		}

		if ( ! empty( $cache ) ) {
			update_option( '_srchpro_settings_search_products_pro_cache', $cache, false );
		}
	}

	public function _get_meta_keys_query_product_ids( $query ) {

		$meta_query = array();

		$sku  = SearchProductsPRO_Get()->get_option( '_sku', 'search_products_pro', 'no' );

		if ( 'yes' == $sku ) {
			$meta_query[] = array(
				'key' => '_sku',
				'value' => $query->query_vars['search_terms'],
				'compare' => SearchProductsPRO_Get()->get_option( '_sku', 'search_products_pro', 'and' ) == 'like' ? 'LIKE' : 'IN',
			);
		}

		$meta_keys  = SearchProductsPRO_Get()->get_option( 'meta_keys', 'search_products_pro', array() );

		if ( ! empty( $meta_keys ) && is_array( $meta_keys ) ) {
			foreach ( $meta_keys as $meta_key ) {
				if ( ! isset( $meta_key['_key'] ) ) {
					continue;
				}

				$meta_query[] = array(
					'key'     => sanitize_title( $meta_key['_key'] ),
					'value'   => $query->query_vars['search_terms'],
					'compare' => isset( $meta_key['_compare'] ) && 'like' == $meta_key['_compare'] ? 'LIKE' : 'IN',
				);
			}

		}

		if ( ! empty( $meta_query ) ) {
			if (count($meta_query)>1) {
				$meta_query['relation'] = 'OR';
			}

			$meta_query_ids = (array) get_posts( array(
				'posts_per_page'  => $this->_get_products(),
				'post_type'       => 'product',
				'post_status'     => 'publish',
				'fields'          => 'ids',
				'meta_query'      => $meta_query,
			) );

			if ( ! empty( $meta_query_ids ) ) {
				return $meta_query_ids;
			}
		}

		return array();
	}

	public function _get_taxonomy_query_product_ids() {
		check_ajax_referer( 'search-products-pro-nounce', 'nonce' );

		$data = json_decode( stripslashes( isset( $_POST['settings'][2] ) ? (string) $_POST['settings'][2] : '' ), true );

		if ( is_array( $data ) ) {
			foreach ( $data as $item ) {
				if ( isset( $item['taxonomy'] ) && isset( $item['id'] ) && $item['id'] > 0 ) {
					$tax_query[] = array(
						'taxonomy' => sanitize_title( $item['taxonomy'] ),
						'field'    => 'id',
						'terms'    => absint( $item['id'] ),
					);
				}
			}
	
			if ( ! empty( $tax_query ) ) {
				if (count($data)>1) {
					$tax_query['relation'] = 'OR';
				}
	
				$tax_query_ids = (array) get_posts( array(
					'posts_per_page'  => $this->_get_products(),
					'post_type'       => 'product',
					'post_status'     => 'publish',
					'fields'          => 'ids',
					'tax_query'       => $tax_query,
				) );
	
				if ( ! empty( $tax_query_ids ) ) {
					return $tax_query_ids;
				}
			}
		}

		return array();
	}

	public function _extended_search( $search, $query ) {
		if ( empty( $search ) ) {
			return $search;
		}

		if ( !isset( $query->query_vars['spp__active'] ) ) {
			return $search;
		}

		$tax_product_ids = $this->_get_taxonomy_query_product_ids();
		$meta_product_ids = $this->_get_meta_keys_query_product_ids( $query );

		$product_ids = array_unique( array_merge( $tax_product_ids, $meta_product_ids ) );

		if ( count( $product_ids ) > 0 ) {
			global $wpdb;
			return str_replace( 'AND (((', "AND ((({$wpdb->posts}.ID IN (" . implode( ',', $product_ids ) . ')) OR (', $search );
		}

		return $search;
	}

}

	add_action( 'init', array( 'SearchProductsPRO_Frontend', 'instance' ) );

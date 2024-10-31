<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'SearchProductsPRO_Admin' ) ) {

	class SearchProductsPRO_Admin {

		public static $version = '1.0.0';

		protected static $_instance = null;

		public static $plugin = null;

		public static $slug = null;

		public static $lang;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			$this->init_hooks();
		}

		private function init_hooks() {
			$plugins = apply_filters( 'srchpro_plugins', array() );

			add_action( 'admin_menu', array( $this, 'load_settings_page' ), 9999999998 );

			$page = isset( $_REQUEST['page'] ) && 'search_products_pro' == $_REQUEST['page']  ? true : false;

			if ( $page ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ), 10 );
				add_action( 'admin_footer', array( $this, 'add_vars' ) );
				add_filter( 'srchpro_settings_templates', array( $this, 'default_templates') );
			}

			add_action( 'wp_ajax_srchpro_ajax_factory', array( $this, 'ajax_factory' ) );
		}

		public function load_settings_page() {
			$page = esc_html__( 'Search Products PRO', 'search-products' );

			add_submenu_page( 'woocommerce', $page, $page, 'manage_woocommerce', 'search_products_pro', array( $this, 'display_page' ) );
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

		public function admin_js() {

			$plugins = apply_filters( 'srchpro_plugins', array() );

			$page = isset( $_REQUEST['page'] ) && 'search_products_pro' == $_REQUEST['page'] ? true: false;

			if ( $page ) {
				wp_enqueue_style( 'srchpro-style', $this->plugin_url() . '/css/srchpro' . ( is_rtl() ? '-rtl' : '' ) . '.css', false, self::$version );

				wp_register_script( 'srchpro-settings', $this->plugin_url() . '/js/srchpro-core.js', array( 'jquery', 'wp-util', 'jquery-ui-core', 'jquery-ui-sortable' ), self::$version, true );
				wp_enqueue_script( 'srchpro-settings' );

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );

				if ( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}
			}

		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function display_page() {

			?>
			<div id="srchpro-settings" class="<?php echo esc_attr( 'srchpro-' . current_filter() ); ?>" data-nonce="<?php esc_attr_e( wp_create_nonce( 'srchpro-nounce' ) ); ?>"></div>
		<?php

		}

		public function add_templates() {

			?>

			<script type="text/template" id="tmpl-srchpro-main-wrapper">
				<div id="srchpro-main-wrapper" data-slug="{{ data.slug }}"<?php self::get_language(); ?>>
					<span id="icon"></span>
					<div id="srchpro-main-header">
						<h2 class="srchpro-plugin">
							{{ data.name }}
						</h2>
						<p class="srchpro-desc">
							{{ data.desc }}
						</p>
						<p class="srchpro-main-buttons">
							<a href="{{ data.doc.url }}" class="srchpro-button-primary" target="_blank">
								{{ data.doc.name }}
							</a>
							<a href="{{ data.ref.url }}" class="srchpro-button" target="_blank">
								{{ data.ref.name }}
							</a>
						</p>
					</div>
					<div id="srchpro-main">
						<ul id="srchpro-settings-menu"></ul>
						<div id="srchpro-settings-main"></div>
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-button">
				<a href="{{ data.url }}" class="srchpro-button{{ data.class }}">
					{{ data.name }}
				</a>
			</script>

			<script type="text/template" id="tmpl-srchpro-li-menu">
				<li data-id="{{ data.id }}">
					{{ data.name }}
				</li>
			</script>

			<script type="text/template" id="tmpl-srchpro-settings">
				<div id="srchpro-settings-main-{{ data.id }}" data-id="{{ data.id }}">
					<div id="srchpro-settings-header">
						<p class="srchpro-desc">
							{{{ data.desc }}}
							<span id="save" class="srchpro-button-primary"><?php esc_html_e( 'Save', 'search-products' ); ?></span>
						</p>
					</div>
					<div id="srchpro-settings-wrapper">
						{{{ data.settings }}}
					</div>
					<div id="srchpro-settings-footer">
						<p class="srchpro-desc">
							{{{ data.desc }}}
							<span id="save-alt" class="srchpro-button-primary"><?php esc_html_e( 'Save', 'search-products' ); ?></span>
						</p>
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-option">
				<div id="{{ data.id }}-option" class="{{ data.class }}<# if ( data.column ) { #>{{ 'srchpro-column srchpro-column-'+data.column }}<# } #>">
					<div class="srchpro-option-header">
						<h3>
							{{ data.name }}
						</h3>
					</div>
					<div class="srchpro-option-wrapper">
						{{{ data.option }}}
						<p class="srchpro-desc">
							{{{ data.desc }}}
						</p>
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-html">
				<div id="{{ data.id }}-option" class="{{ data.class }}<# if ( data.column ) { #>{{ 'srchpro-column srchpro-column-'+data.column }}<# } #>">
					{{{ data.desc }}}
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-utility">
				<span id="srchpro-export" class="srchpro-button"><?php esc_html_e( 'Export', 'search-products' ); ?></span><span id="srchpro-import" class="srchpro-button"><?php esc_html_e( 'Import', 'search-products' ); ?></span><span id="srchpro-backup" class="srchpro-button"><?php esc_html_e( 'Backup', 'search-products' ); ?></span><span id="srchpro-restore" class="srchpro-button"><?php esc_html_e( 'Restore', 'search-products' ); ?></span><span id="srchpro-reset" class="srchpro-button"><?php esc_html_e( 'Reset', 'search-products' ); ?></span>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-text">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="text"<# if ( data.val ) { #> value="{{ data.val }}"<# } else { #><# } #> />
			</script>

			<script type="text/template" id="tmpl-srchpro-option-file">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="text"<# if ( data.val ) { #> value="{{ data.val }}"<# } else { #><# } #> /> <span class="srchpro-button srchpro-file-add"><?php esc_html_e( 'Add +', 'search-products' ); ?></span>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-textarea">
				<textarea id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}"><# if ( data.val ) { #>{{ data.val }}<# } else { #><# } #></textarea>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-multiselect">
				<select id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" class="srchpro-multiple" multiple>
					{{{ data.options }}}
				</select>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-values-multiselect">
				<option value="{{ data.val }}"<# if ( data.sel ) { #> selected="selected"<# } else { #><# } #>>{{ data.name }}</option>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-select">
				<select id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}">
					{{{ data.options }}}
				</select>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-values-select">
				<option value="{{ data.val }}"<# if ( data.sel ) { #> selected="selected"<# } else { #><# } #>>{{ data.name }}</option>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-checkbox">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="checkbox" <# if ( data.val == "yes" ) { #> checked="checked"<# } else { #><# } #>/> <label for="{{ data.eid }}"></label>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-number">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="number"<# if ( data.val ) { #> value="{{ data.val }}"<# } else { #><# } #> />
			</script>

			<script type="text/template" id="tmpl-srchpro-option-hidden">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="hidden"<# if ( data.val ) { #> value="{{ data.val }}"<# } else { #><# } #> />
			</script>

			<script type="text/template" id="tmpl-srchpro-option-include">
				<span class="srchpro-button srchpro-include"><?php esc_html_e( 'Configure', 'search-products' ); ?></span>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-list">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="hidden"<# if ( data.val ) { #> value="{{ data.val }}"<# } else { #><# } #> />
				<div id="{{ data.id }}-list" class="srchpro-option-list">
					{{{ data.options }}}
				</div>
				<span class="srchpro-button-primary srchpro-option-list-add" data-id="{{ data.id }}"><?php esc_html_e( 'Add Item +', 'search-products' ); ?></span>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-list-item">
				<div class="srchpro-option-list-item">
					<span class="srchpro-option-list-item-icon srchpro-list-expand-button" data-type="{{ data.type }}"></span>
					<span class="srchpro-option-list-item-title">{{ data.title }}</span>
					<span class="srchpro-option-list-item-icon srchpro-list-remove-button" data-id="{{ data.id }}"></span>
					<span class="srchpro-option-list-item-icon srchpro-list-move-button"></span>
					<# if ( data.customizer ) { #><span class="srchpro-option-list-item-icon srchpro-list-customizer-button"></span><# } #>
					<div class="srchpro-option-list-item-container">
						{{{ data.options }}}
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-option-list-select">
				<input id="{{ data.eid }}" data-option="{{ data.id }}" class="srchpro-change{{ data.class }}" name="{{ data.name }}" type="hidden"<# if ( data.val ) { #> value="{{ data.val }}"<# } #> />
				<div id="{{ data.id }}-list" class="srchpro-option-list">
					{{{ data.options }}}
				</div>
				{{{ data.selects }}} <span class="srchpro-button-primary srchpro-option-list-select-add" data-id="{{ data.id }}"><?php esc_html_e( 'Add Item +', 'search-products' ); ?></span>
			</script>

			<script type="text/template" id="tmpl-srchpro-include-customizer">
				<div id="srchpro-include-customizer" data-id="{{ data.id }}">
					<div class="srchpro-include-customizer-wrapper">
						<div class="srchpro-include-customizer-header">
							<span id="srchpro-include-customizer-exit"></span>
							<h2><?php esc_html_e( 'Include/Exclude Manager', 'search-products' ); ?></h2>
							<span id="srchpro-exclude-toggle" class="<# if ( data.selected == 'OUT' ) { #>srchpro-button-primary<# } else { #>srchpro-button<# } #>"><?php esc_html_e( 'Exclude', 'search-products' ); ?></span>
							<span id="srchpro-include-toggle" class="<# if ( data.selected == 'IN' ) { #>srchpro-button-primary<# } else { #>srchpro-button<# } #>"><?php esc_html_e( 'Include', 'search-products' ); ?></span>
						</div>
						<div id="srchpro-include-customizer-terms" data-taxonomy="{{ data.taxonomy }}"></div>
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-customizer">
				<div id="srchpro-customizer" data-id="{{ data.id }}">
					<div class="srchpro-customizer-wrapper">
						<div class="srchpro-customizer-header">
							<span id="srchpro-customizer-exit"></span>
							<h2><?php esc_html_e( 'Terms Manager', 'search-products' ); ?></h2>
							<# if ( data.type !== 'orderby' && data.type !== 'vendor' && data.type !== 'instock' && data.type !== 'range' ) { #>

								<# if ( data.type == 'meta' || data.type == 'meta_range' || data.type == 'ivpa_custom' || data.type == 'price' || data.type == 'per_page' ) { #>
									<span id="srchpro-customizer-add" class="srchpro-button-primary">Add Term +</span>
								<# }
								else { #>
									<span id="srchpro-customizer-custom-order" class="<# if ( data.order == 'true' ) { #>srchpro-button-primary<# } else { #>srchpro-button<# } #>"><?php esc_html_e( 'Custom Order', 'search-products' ); ?></span>
								<# } #>

							<# } #>
						</div>
						<# if ( data.type !== 'range' && data.type !== 'meta_range' ) { #>
							<div class="srchpro-customizer-style">
								<div id="srchpro-special-options">
									<span class="srchpro-special-option">
										<label><?php esc_html_e( 'Type', 'search-products' ); ?></label>
										<select class="srchpro-terms-style-change" data-option="type">
											<option value=""<# if ( data.style == '' ) { #> selected="selected"<# } #>><?php esc_html_e( 'None', 'search-products' ); ?></option>
											<option value="text"<# if ( data.style == 'text' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Plain Text', 'search-products' ); ?></option>
											<option value="color"<# if ( data.style == 'color' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Color', 'search-products' ); ?></option>
											<option value="image"<# if ( data.style == 'image' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Thumbnail', 'search-products' ); ?></option>
											<option value="selectbox"<# if ( data.style == 'selectbox' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Select Box', 'search-products' ); ?></option>
											<# if ( data.type.substr(0,5) !== 'ivpa_' ) { #>
												<option value="system"<# if ( data.style == 'system' ) { #> selected="selected"<# } #>><?php esc_html_e( 'System Select', 'search-products' ); ?></option>
												<option value="selectize"<# if ( data.style == 'selectize' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Live Select', 'search-products' ); ?></option>
											<# } #>	
											<option value="html"<# if ( data.style == 'html' ) { #> selected="selected"<# } #>>HTML</option>
											<# if ( data.type == 'ivpa_custom' ) { #>
												<option value="input"<# if ( data.style == 'input' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Input Field', 'search-products' ); ?></option>
												<option value="checkbox"<# if ( data.style == 'checkbox' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Checkbox', 'search-products' ); ?></option>
												<option value="textarea"<# if ( data.style == 'textarea' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Textarea', 'search-products' ); ?></option>
												<option value="system"<# if ( data.style == 'system' ) { #> selected="selected"<# } #>><?php esc_html_e( 'System Select', 'search-products' ); ?></option>
											<# } #>
										</select>
									</span>
									{{{ data.controls }}}
								</div>
							</div>
						<# } #>
						<div id="srchpro-customizer-terms" class="srchpro-terms-list" data-taxonomy="{{ data.taxonomy }}">
							{{{ data.terms }}}
						</div>
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-customizer-style-text">
				<span class="srchpro-special-option">
					<label><?php esc_html_e( 'Style', 'search-products' ); ?></label>
					<select class="srchpro-terms-style-change" data-option="style">
						<option value="border"<# if ( data.border == 'round' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Border', 'search-products' ); ?></option>
						<option value="background"<# if ( data.style == 'background' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Background', 'search-products' ); ?></option>
						<option value="round"<# if ( data.style == 'round' ) { #> selected="selected"<# } #>><?php esc_html_e( 'Round', 'search-products' ); ?></option>
					</select>
				</span>
				<span class="srchpro-special-option">
					<label><?php esc_html_e( 'Normal', 'search-products' ); ?></label>
					<input type="text" class="srchpro-terms-color srchpro-terms-style-change" data-option="normal"<# if ( data.normal ) { #> value="{{ data.normal }}"<# } #> />
				</span>
				<span class="srchpro-special-option">
					<label><?php esc_html_e( 'Active', 'search-products' ); ?></label>
					<input type="text" class="srchpro-terms-color srchpro-terms-style-change" data-option="active"<# if ( data.active ) { #> value="{{ data.active }}"<# } #> />
				</span>
				<span class="srchpro-special-option">
					<label><?php esc_html_e( 'Disabled', 'search-products' ); ?></label>
					<input type="text" class="srchpro-terms-color srchpro-terms-style-change" data-option="disabled"<# if ( data.disabled ) { #> value="{{ data.disabled }}"<# } #> />
				</span>
				<span class="srchpro-special-option">
					<label><?php esc_html_e( 'Out of stock', 'search-products' ); ?></label>
					<input type="text" class="srchpro-terms-color srchpro-terms-style-change" data-option="outofstock"<# if ( data.outofstock ) { #> value="{{ data.outofstock }}"<# } #> />
				</span>
			</script>

			<script type="text/template" id="tmpl-srchpro-customizer-style-swatch">
				<span class="srchpro-special-option">
					<label><?php esc_html_e( 'Show labels', 'search-products' ); ?></label>
					<select class="srchpro-terms-style-change" data-option="label">
						<option value="no"<# if ( data.label == 'no' ) { #> selected="selected"<# } #>>No</option>
						<option value="side"<# if ( data.label == 'side' ) { #> selected="selected"<# } #>>Aside</option>
					</select>
				</span>
				<span class="srchpro-special-option srchpro-special-option-sm">
					<label><?php esc_html_e( 'Swatch size', 'search-products' ); ?></label>
					<input type="text" class="srchpro-terms-style-change" data-option="size"<# if ( data.size ) { #> value="{{ data.size }}"<# } #> />
				</span>
			</script>

			<script type="text/template" id="tmpl-srchpro-customizer-term">
				<div class="srchpro-terms-list-item" data-id="{{ data.id }}" data-slug="{{ data.slug }}">
					<div class="srchpro-term-badge">
						<span class="srchpro-term-item-title">{{ data.title }}</span>
						<# if ( data.type == 'meta' || data.type == 'meta_range' || data.type == 'price' || data.type == 'per_page' ) { #>
							<span class="srchpro-term-item-icon srchpro-term-remove-button" data-id="{{ data.id }}"></span>
						<# } #>
						<# if ( data.type == 'meta' || data.type == 'meta_range' || data.type == 'price' || data.type == 'per_page' || data.type =='orderby' || data.type =='vendor' || data.type =='instock' || data.order == 'true' ) { #>
							<span class="srchpro-term-item-icon srchpro-term-move-button"></span>
						<# } #>
					</div>
					<div class="srchpro-term-options-holder">
						<div class="srchpro-term-option">
							<label><?php esc_html_e( 'Name', 'search-products' ); ?></label>
							<input type="text" class="srchpro-terms-change" name="name" />
						</div>

						<# if ( data.type == 'meta' || data.type == 'meta_range' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Meta value', 'search-products' ); ?></label>
								<input type="text" class="srchpro-terms-change" name="data" />
							</div>
						<# } #>

						<# if ( data.style !== 'text' && data.style !== 'selectbox' && data.style !== 'system' && data.style !== 'selectize' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Value', 'search-products' ); ?></label>
								<# if ( data.style !== 'html' ) { #>
									<input type="text" class="srchpro-terms-change <# if ( data.style ) { #>srchpro-terms-{{ data.style }}<# } #>" name="value" />
								<# }
								else { #>
									<textarea class="srchpro-terms-change <# if ( data.style ) { #>srchpro-terms-{{ data.style }}<# } #>" name="value"></textarea>
								<# } #>
								<# if ( data.style == 'image' ) { #>
									<span class="srchpro-button srchpro-terms-image-add">Add Image +</span>
								<# } #>
							</div>
						<# } #>

						 <# if ( data.type == 'price' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Min', 'search-products' ); ?></label>
								<input type="number" class="srchpro-terms-change" name="min" />
								<label><?php esc_html_e( 'Max', 'search-products' ); ?></label>
								<input type="number" class="srchpro-terms-change" name="max" />
							</div>
						<# } #>

						<# if ( data.type == 'per_page' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Count', 'search-products' ); ?></label>
								<input type="number" class="srchpro-terms-change" name="count" />
							</div>
						<# } #>

						<# if ( data.type !== 'range' && data.type !== 'meta_range' && data.style !== 'system' && data.style !== 'selectize' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Tooltip', 'search-products' ); ?></label>
								<textarea class="srchpro-terms-change" name="tooltip"></textarea>
							</div>
						<# } #>
					</div>
				</div>
			</script>

			<script type="text/template" id="tmpl-srchpro-customizer-term-ivpa">
				<div class="srchpro-terms-list-item" data-id="{{ data.id }}" data-slug="{{ data.slug }}">
					<div class="srchpro-term-badge">
						<span class="srchpro-term-item-title">{{ data.title }}</span>
						<# if ( data.type == 'ivpa_custom' ) { #>
							<span class="srchpro-term-item-icon srchpro-term-remove-button" data-id="{{ data.id }}"></span>
						<# } #>
						<# if ( data.type == 'ivpa_custom' || data.order == 'true' ) { #>
							<span class="srchpro-term-item-icon srchpro-term-move-button"></span>
						<# } #>
					</div>
					<div class="srchpro-term-options-holder">
						<div class="srchpro-term-option">
							<label><?php esc_html_e( 'Name', 'search-products' ); ?></label>
							<input type="text" class="srchpro-terms-change" name="name" />
						</div>

						<# if ( data.style !== 'text' && data.style !== 'selectbox' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Value', 'search-products' ); ?></label>
								<# if ( data.style !== 'html' ) { #>
									<input type="text" class="srchpro-terms-change <# if ( data.style ) { #>srchpro-terms-{{ data.style }}<# } #>" name="value" />
								<# }
								else { #>
									<textarea class="srchpro-terms-change <# if ( data.style ) { #>srchpro-terms-{{ data.style }}<# } #>" name="value"></textarea>
								<# } #>
								<# if ( data.style == 'image' ) { #>
									<span class="srchpro-button srchpro-terms-image-add"><?php esc_html_e( 'Add Image +', 'search-products' ); ?></span>
								<# } #>
							</div>
						<# } #>

						<# if ( data.type == 'ivpa_custom' ) { #>
							<div class="srchpro-term-option">
								<label><?php esc_html_e( 'Price', 'search-products' ); ?></label>
								<input type="text" class="srchpro-terms-change" name="price" />
							</div>
						<# } #>

						<div class="srchpro-term-option">
							<label><?php esc_html_e( 'Tooltip', 'search-products' ); ?></label>
							<textarea class="srchpro-terms-change" name="tooltip"></textarea>
						</div>

					</div>
				</div>
			</script>
		<?php

		}

		public function add_vars() {

			if ( wp_script_is( 'srchpro-settings', 'enqueued' ) ) {
				$this->add_templates();

				$vars = apply_filters( 'srchpro_plugins_settings', array() );

				$slug = sanitize_title( isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '' );

				if ( isset( $vars[$slug] ) ) {
					$vars[$slug]['ajax'] = esc_url( admin_url( 'admin-ajax.php' ) );
					wp_localize_script( 'srchpro-settings', 'srchpro', $vars[$slug] );
				}

			}

		}

		public function ajax_die( $opt) {
			$opt['success'] = false;
			wp_send_json( $opt );
			exit;
		}


		public function _terms_get_options( $terms, &$ready, &$level, $mode ) {
			foreach ( $terms as $term ) {
				if ( 'select' == $mode ) {
					$ready[$term->term_id] = ( $level > 0 ? str_repeat( '-', $level ) . ' ' : '' ) . $term->name;
				} else {
					$ready[] = array(
						'id' => $term->term_id,
						'name' => ( $level > 0 ? str_repeat( '-', $level ) . ' ' : '' ) . $term->name,
						'slug' => $term->slug,
					);
				}
				if ( !empty( $term->children ) ) {
					$level++;
					SearchProducts()->_terms_get_options( $term->children, $ready, $level, $mode );
					$level--;
				}
			}
		}

		public function _terms_sort_hierarchicaly( array &$cats, array &$into, $parentId = 0 ) {
			foreach ( $cats as $i => $cat ) {
				if ( $cat->parent == $parentId ) {
					$into[$cat->term_id] = $cat;
					unset($cats[$i]);
				}
			}
			foreach ( $into as $topCat ) {
				$topCat->children = array();
				SearchProducts()->_terms_sort_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
			}
		}

		public function __remove_languages() {
			if ( class_exists( 'SitePress') ) {
				do_action( 'wpml_switch_language', apply_filters( 'wpml_default_language', null ) );
			}
		}

		public function _terms_get( $set, $mode ) {
			$taxonomy = $set[2];
			$ready = array();

			if ( taxonomy_exists( $taxonomy ) ) {

				if ( isset( $set[4] ) && 'no_lang' == $set[4] && $this->language() ) {
					$this->__remove_languages();
				}

				$args = array(
					'hide_empty' => 0,
					'hierarchical' => ( is_taxonomy_hierarchical( $taxonomy ) ? 1 : 0 )
				);

				$terms = get_terms( $taxonomy, $args );

				if ( is_taxonomy_hierarchical( $taxonomy ) ) {
					$terms_sorted = array();
					SearchProducts()->_terms_sort_hierarchicaly( $terms, $terms_sorted );
					$terms = $terms_sorted;
				}

				if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
					$var =0;
					SearchProducts()->_terms_get_options( $terms, $ready, $var, $mode );
				}

			}

			return $ready;
		}

		public function _terms_decode( $str ) {
			$str = preg_replace( '/%u([0-9a-f]{3,4})/i', "&#x\\1;", urldecode( $str ) );
			return html_entity_decode( $str, null, 'UTF-8' );
		}

		public function _types_get() {
			$types = wc_get_product_types();
			$ready = array();
			if ( !empty( $types ) ) {
				foreach ( $types as $k => $v ) {
					$ready[$k] = $v;
				}
			}
			return $ready;
		}

		public function _taxonomies_get() {
			$taxonomies = get_object_taxonomies( 'product' );
			$ready = array();
			if ( !empty( $taxonomies ) ) {
				foreach ( $taxonomies as $k ) {
					if ( substr($k, 0, 3) == 'pa_' ) {
						$ready[$k] =  wc_attribute_label( $k );
					} else if ( taxonomy_exists( $k ) ) {
						$taxonomy = get_taxonomy( $k );
						if ( $taxonomy->public ) {
							$ready[$k] = $taxonomy->label;
						}
					}
				}
			}
			return $ready;
		}

		public function _attributes_get_alt() {
			$attributes = get_object_taxonomies( 'product' );
			$ready = array();
			if ( !empty( $attributes ) ) {
				foreach ( $attributes as $k ) {
					if ( substr($k, 0, 3) == 'pa_' ) {
						$ready[$k] =  wc_attribute_label( $k );
					}
				}
			}
			return $ready;
		}

		public function _attributes_get() {
			$attributes = wc_get_attribute_taxonomies();
			$ready = array();

			if ( !empty( $attributes ) ) {
				foreach ( $attributes as $attribute ) {
					$ready['pa_' . $attribute->attribute_name] = $attribute->attribute_label;
				}
			}

			return $ready;
		}

		public function array_overlay( $a, $b ) {
			foreach ( $b as $k => $v ) {
				$a[$k] = $v;
			}
			return $a;
		}

		public function ajax_factory() {
			check_ajax_referer( 'srchpro-nounce', 'nonce' );

			$ajaxData = json_decode( stripslashes( sanitize_textarea_field( isset( $_POST['srchpro'] ) ? (string) $_POST['srchpro'] : '' ) ), true );

			$opt = array(
				'success' => true
			);

			if ( !isset( $ajaxData['type'] ) ) {
				SearchProducts()->ajax_die($opt);
			}

			if ( !isset( $ajaxData['settings'] ) ) {
				SearchProducts()->ajax_die($opt);
			}

			if ( apply_filters( 'srchpro_can_you_save', false ) ) {
				$this->ajax_die($opt);
			}

			$plugin = sanitize_title( isset( $ajaxData['plugin'] ) ? (string) $ajaxData['plugin']: '' );

			switch ( $ajaxData['type'] ) {

				case 'get_control_options':
					$set = explode( ':', sanitize_text_field( $ajaxData['settings'] ) );

					switch ( $set[1] ) {

						case 'image_sizes':
							$image_array = array();
							$image_sizes = get_intermediate_image_sizes();
							foreach ( $image_sizes as $image_size ) {
								$image_array[$image_size] = $image_size;
							}
							wp_send_json( $image_array );
							exit;
						break;
						case 'wp_options':
							wp_send_json( get_option( substr( sanitize_title( $ajaxData['settings'] ), 16 ) ) );
							exit;
						break;
						case 'users':
							$return = array();
							$users = get_users( array( 'fields' => array( 'id', 'display_name' ) ) );

							foreach ( $users as $user ) {
								$return[$user->id] = $user->display_name;
							}

							wp_send_json( $return );
							exit;
						break;
						case 'product_attributes':
							wp_send_json( SearchProducts()->_attributes_get_alt() );
							exit;
						break;
						case 'product_taxonomies':
							wp_send_json( SearchProducts()->_taxonomies_get() );
							exit;
						break;
						case 'product_types':
							wp_send_json( SearchProducts()->_types_get() );
							exit;
						break;
						case 'taxonomy':
							wp_send_json( SearchProducts()->_terms_get( $set, 'select' ) );
							exit;
						break;
						case 'terms':
							wp_send_json( SearchProducts()->_terms_get( $set, 'terms' ) );
							exit;
						break;
						default:
							SearchProducts()->ajax_die($opt);
							break;

					}

					break;

				case 'save':
					$slc = isset( $ajaxData['delete'] ) ? (array) $ajaxData['delete'] : array();

					if ( !empty( $slc ) && is_array( $slc ) ) {
						foreach ( $slc as $k => $v ) {
							delete_option( $v );
						}
					}

					$sld = isset( $ajaxData['solids'] ) ? (array) $ajaxData['solids'] : array();

					if ( !empty( $sld ) ) {
						foreach ( $sld as $k => $v ) {
							$val = isset( $v['val'] ) && !empty( $v['val'] ) ? $v['val'] : false;
							if ( !is_array( $val ) ) {
								$val = array();
							}
							$std = get_option( $k, array() );
							if ( !is_array( $std ) ) {
								$std = array();
							}

							if ( empty( $val ) ) {
								update_option( $k, '', false );
							} else {
								
								update_option( $k, $val, false );
							}
						}
					}

					$stg = isset( $ajaxData['settings'] ) ? (array) $ajaxData['settings'] : array();

					foreach ( $stg as $k => $v ) {
						if ( isset( $v['autoload'] ) ) {
							if ( 'true' == $v['autoload'] ) {
								$opt['auto'][$k] = $v['val'];
							} else if ( '' == $v['autoload'] ) {
								$opt['std'][$k] = isset( $v['val'] ) ? $v['val'] : false;
							}
						}
					}

					$opt = apply_filters( 'srchpro_ajax_save_settings', $opt );

					if ( isset( $opt['std'] ) && !empty( $opt['std'] ) && is_array( $opt['std'] ) && isset(  $plugin ) ) {
						update_option( 'srchpro_settings_' . $plugin, array_merge( get_option( 'srchpro_settings_' . $plugin, array() ), $opt['std'] ), false );
					}

					if ( isset( $opt['auto'] ) && !empty( $opt['auto'] ) && is_array( $opt['auto'] ) ) {
						$opt['auto'] = array_merge( get_option( 'srchpro_autoload', array() ), $opt['auto'] );
					}

					$opt = apply_filters( 'srchpro_ajax_save_settings_auto', $opt );

					if ( !empty( $opt['auto'] ) ) {
						update_option( 'srchpro_autoload', $opt['auto'], true );
					}

					do_action( 'srchpro_ajax_saved_settings_' . $plugin, $opt );

					wp_send_json( array( 'success' => true ) );
					exit;

				break;

				case 'export':
					$stg = isset( $ajaxData['settings'] ) ? (array) $ajaxData['settings'] : array();

					if ( isset( $stg['auto'] ) && !empty( $stg['auto'] ) && is_array( $stg['auto'] ) ) {
						$backup_auto = get_option( 'srchpro_autoload', array() );

						foreach ( $stg['auto'] as $k ) {
							if ( isset( $backup_auto[$k] ) ) {
								$exp['auto'][$k] = $backup_auto[$k];
							}
						}
					}

					if ( isset( $stg['std'] ) && !empty( $stg['std'] ) && is_array( $stg['std'] ) ) {
						$exp['std'] = get_option( 'srchpro_settings_' . $plugin, array() );
					}

					if ( isset( $stg['solids'] ) && !empty( $stg['solids'] ) && is_array( $stg['solids'] ) ) {
						foreach ( $stg['solids'] as $k ) {
							$exp['solids'][$k] = get_option( $k );
						}
					}

					wp_send_json( $this->get_for_options( $exp ) );
					exit;
				break;

				case 'import':
					$stg = sanitize_textarea_field( isset( $ajaxData['settings'] ) ? (string) $ajaxData['settings'] : '' );

					if ( '' !== $stg ) {

						$opt = $this->get_for_options( json_decode( stripslashes( $stg ), true ) );

						$opt = apply_filters( 'srchpro_ajax_save_settings', $opt );

						if ( isset( $opt['auto'] ) && !empty( $opt['auto'] ) && is_array( $opt['auto'] ) ) {
							$opt['auto'] = array_merge( get_option( 'srchpro_autoload', array() ), $opt['auto'] );
							update_option( 'srchpro_autoload', $opt['auto'], true );
						}

						if ( isset( $opt['std'] ) && !empty( $opt['std'] ) && is_array( $opt['std'] ) ) {
							update_option( 'srchpro_settings_' . $plugin, $opt['std'], false );
						}

						if ( isset( $opt['solids'] ) && !empty( $opt['solids'] ) && is_array( $opt['solids'] ) ) {
							foreach ( $opt['solids'] as $key => $solid ) {
								update_option( $key, $solid, false );
							}
						}

						wp_send_json( array( 'success' => true ) );
						exit;
					}
					wp_send_json( array( 'success' => false ) );
					exit;
				break;

				case 'backup':
					$bkp = array();
					$stg = isset( $ajaxData['settings'] ) ? (array) $ajaxData['settings'] : array();

					if ( isset( $stg['auto'] ) && !empty( $stg['auto'] ) && is_array( $stg['auto'] ) ) {
						$backup_auto = get_option( 'srchpro_autoload', array() );
						foreach ( $stg['auto'] as $k ) {
							if ( isset( $backup_auto[$k] ) ) {
								$bkp['auto'][$k] = $backup_auto[$k];
							}
						}
					}

					if ( isset( $stg['std'] ) && !empty( $stg['std'] ) && is_array( $stg['std'] ) ) {
						$bkp['std'] = get_option( 'srchpro_settings_' . $plugin, array() );
					}

					if ( isset( $stg['solids'] ) && !empty( $stg['solids'] ) && is_array( $stg['solids'] ) ) {
						foreach ( $stg['solids'] as $k ) {
							$bkp['solids'][$k] = get_option( $k );
						}
					}

					$bkp['time'] = time();

					update_option( '_srchpro_settings_backup_' . $plugin, $bkp );

					wp_send_json( array( 'success' => true ) );
					exit;
				break;

				case 'restore':
					$bkp = get_option( '_srchpro_settings_backup_' . $plugin );

					if ( isset( $bkp['auto'] ) && !empty( $bkp['auto'] ) && is_array( $bkp['auto'] ) ) {
						$bkp['auto'] = array_merge( get_option( 'srchpro_autoload', array() ), $bkp['auto'] );
						update_option( 'srchpro_autoload', $bkp['auto'], true );
					}

					if ( isset( $bkp['std'] ) && !empty( $bkp['std'] ) && is_array( $bkp['std'] ) ) {
						update_option( 'srchpro_settings_' . $plugin, $bkp['std'], false );
					}

					if ( isset( $bkp['solids'] ) && !empty( $bkp['solids'] ) && is_array( $bkp['solids'] ) ) {
						foreach ( $bkp['solids'] as $key => $solid ) {
							update_option( $key, $solid, false );
						}
					}

					wp_send_json( array( 'success' => true ) );
					exit;
				break;

				case 'reset':
					$stg = isset( $ajaxData['settings'] ) ? (array) $ajaxData['settings'] : array();

					if ( isset( $stg['auto'] ) && !empty( $stg['auto'] ) && is_array( $stg['auto'] ) ) {
						$opt = get_option( 'srchpro_autoload', array() );

						foreach ( $opt as $k => $v ) {
							if ( in_array( $k, $stg['auto'] ) ) {
								unset( $opt[$k] );
							}
						}

						update_option( 'srchpro_autoload', $opt, true );
					}

					delete_option( 'srchpro_settings_' . $plugin );

					if ( !empty( $stg['solids'] ) && is_array( $stg['solids'] ) ) {
						foreach ( $stg['solids'] as $key ) {
							delete_option( $key );
						}
					}

					wp_send_json( array( 'success' => true ) );
					exit;
				break;

				case 'filter':
					SearchProducts()->_filter_get();
					break;

				default:
					SearchProducts()->ajax_die($opt);
					break;

			}

		}

		public function _filter_get() {
			check_ajax_referer( 'srchpro-nounce', 'nonce' );

			$ajaxData = json_decode( stripslashes( sanitize_textarea_field( isset( $_POST['srchpro'] ) ? (string) $_POST['srchpro'] : '' ) ), true );

			$key = isset( $ajaxData['settings'] ) ? (string) $ajaxData['settings'] : '';

			if ( '' === $key ) {
				SearchProducts()->ajax_die( array() );
			}

			wp_send_json( Prdctfltr()->___get_preset( $key ) );
			exit;

		}

		public function get_for_options( $stg ) {
			$opt = array(
				'auto' => array(),
				'std' => array(),
			);

			if ( isset( $stg['auto'] ) && is_array( $stg['auto'] ) ) {
				$opt['auto'] = $stg['auto'];
			}

			if ( isset( $stg['std'] ) && is_array( $stg['std'] ) ) {
				$opt['std'] = $stg['std'];
			}

			if ( isset( $stg['solids'] ) && is_array( $stg['solids'] ) ) {
				$opt['solids'] = $stg['solids'];
			}

			return $opt;
		}

		public static function get_language() {
			if ( !empty( self::language() ) ) {
				?>
				 data-language="<?php esc_attr_e( self::language() ); ?>"
				<?php
			}
			return false;
		}

		public static function language() {
			if ( self::$lang ) {
				return self::$lang;
			}

			self::$lang = '';

			if ( class_exists( 'SitePress' ) ) {
				$default = apply_filters( 'wpml_default_language', null );
				$language = apply_filters( 'wpml_current_language', null );
				if ( $default !== $language ) {
					$doit = $language;
				}
			}

			if ( function_exists( 'qtranxf_getLanguageDefault' ) ) {
				$default = qtranxf_getLanguageDefault();
				$language = qtranxf_getLanguage();
				if ( $default !== $language ) {
					$doit = $language;
				}
			}

			if ( function_exists( 'pll_default_language' ) ) {
				$default = pll_default_language();
				$language = pll_current_language();
				if ( $default !== $language ) {
					$doit = $language;
				}
			}

			if ( isset( $doit ) ) {
				self::$lang = $doit;
			}

			return self::$lang;
		}

		public static function stripslashes_deep( $value ) {
			return is_array( $value ) ? array_map( 'stripslashes_deep', $value ) : stripslashes( $value );
		}

		public static function sanitize_color( $color ) {
			if ( empty( $color ) || is_array( $color ) ) {
				return 'rgba(0,0,0,0)';
			}

			if ( false === strpos( $color, 'rgba' ) ) {
				return sanitize_hex_color( $color );
			}

			$color = str_replace( ' ', '', $color );
			sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
			return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
		}

		public function _do_options( $plugins, $slug ) {

			$backup = get_option( '_srchpro_settings_backup_' . $slug, '' );

			if ( '' !== $backup && isset( $backup['time'] ) ) {
				$plugins[$slug]['backup'] = gmdate( get_option( 'time_format', '' ) . ', ' . get_option( 'date_format', 'd/m/Y' ), $backup['time'] );
			}

			foreach ( $plugins[$slug]['settings'] as $k => $v ) {
				if ( empty( $v ) || isset( $v['section'] ) && 'dashboard' == $v['section']  ) {
					continue;
				}

				$get = isset( $v['translate'] ) && !empty( $this->language() ) ? $v['id'] . '_' . $this->language() : $v['id'];

				$set = isset( $v['default'] ) ?  $v['default'] : '';

				if ( isset( $v['autoload'] ) && true === $v['autoload'] ) {
					$set = SearchProductsPRO_Get()->get_option_autoload( $get, $set );
				} else {
					$set = SearchProductsPRO_Get()->get_option( $get, $slug, $set );
				}

				if ( false === $set ) {
					$set = isset( $v['default'] ) ? $v['default'] : '';
				}

				$plugins[$slug]['settings'][$k]['val'] = $this->stripslashes_deep( $set );
			}

			return apply_filters( $slug . '_settings', $plugins );
		}

	}

	function SearchProducts() {
		return SearchProductsPRO_Admin::instance();
	}

	SearchProductsPRO_Admin::instance();

}

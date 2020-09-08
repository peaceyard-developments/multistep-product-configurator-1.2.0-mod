<?php

if( !class_exists('MSPC_Admin_Product') ) {

	class MSPC_Admin_Product {

		public function __construct() {

			add_filter( 'product_type_options', array( &$this, 'add_product_type_option' ) );
			add_filter( 'woocommerce_product_data_tabs', array( &$this, 'add_product_data_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( &$this, 'add_product_data_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( &$this, 'save_custom_fields' ), 10, 2 );

		}

		//add checkbox to enable mspc for a product
		public function add_product_type_option( $types ) {

			$types['mspc'] = array(
				'id' => '_mspc',
				'wrapper_class' => 'show_if_mspc show_if_variable',
				'label' => __( 'Multistep Product Configurator', 'radykal' ),
				'description' => __( 'Enable the multistep product configurator form.', 'radykal' )
			);

			return $types;

		}

		//the tab in the data panel
		public function add_product_data_tab( $tabs ) {

			$tabs['mspc'] = array(
				'label'  => __( 'MSPC', 'radykal' ),
				'target' => 'mspc_data',
				'class'  => array( 'hide_if_mspc' ),
			);

			return $tabs;

		}

		//custom panel in the product post to set some options
		public function add_product_data_panel() {

			global $wpdb, $post;

			$options = MSPC_Admin_Settings::get_options();
			$custom_fields = get_post_custom($post->ID);
			$stored_options = array();

			foreach( MSPC_Admin_Settings::get_options() as $key => $value) {

				$option_key = 'mspc_'.$key;
				if( isset($custom_fields[$option_key]) ) {
					$stored_options[$key] = $custom_fields[$option_key][0];
				}
				else {
					$stored_options[$key] = '';
				}

			}

			require_once(MSPC_PLUGIN_ADMIN_DIR.'/views/html-admin-meta-box.php');

		}

		//be sure to save the checkbox value (product post)
		public function save_custom_fields( $post_id, $post ) {

			update_post_meta( $post_id, '_mspc', isset( $_POST['_mspc'] ) ? 'yes' : 'no' );

			if( isset($_POST['mspc_module']) ) {

				foreach( MSPC_Admin_Settings::get_options() as $key => $value) {

					$option_key = 'mspc_'.$key;
					if( isset($_POST[$option_key]) ) {

						update_post_meta( $post_id, $option_key, $_POST[$option_key] );

					}
					else {

						update_post_meta( $post_id, $option_key, '' );

					}

				}

			}

		}

	}
}

new MSPC_Admin_Product();

?>
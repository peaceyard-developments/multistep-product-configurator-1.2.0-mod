<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('MSPC_Admin_Scripts_Styles') ) {

	class MSPC_Admin_Scripts_Styles {

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles_scripts' ), 50 );

		}

		public function enqueue_styles_scripts( $hook ) {

			global $post;

			$wc_settings_page = 'wc-settings';

			//woocommerce settings
			if( $hook == 'woocommerce_page_'.$wc_settings_page.'' ) {

				wp_enqueue_style( 'mspc-admin', plugins_url('/css/admin.css', __FILE__) );
				wp_enqueue_script( 'mspc-admin', plugins_url('/js/admin.js', __FILE__), false, Multistep_Product_Configurator::VERSION );

			}

			//woocommerce post types
		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		    	//product
		        if ( 'product' === $post->post_type ) {

		        	wp_enqueue_style( 'mspc-admin', plugins_url('/css/admin.css', __FILE__) );
		        	wp_enqueue_script( 'mspc-meta-box', plugins_url('/js/meta-box.js', __FILE__), false, Multistep_Product_Configurator::VERSION );

		        }
		    }

			//edit attribute form
		    if( $hook == 'edit-tags.php' || $hook == 'term.php' ) {

				$attributes = wc_get_attribute_taxonomy_names();
				if( !empty($attributes) && in_array($_GET['taxonomy'], $attributes) ) {

					wp_enqueue_media();

					wp_enqueue_style( 'mspc-admin', plugins_url('/css/admin.css', __FILE__) );
					wp_enqueue_script( 'mspc-admin', plugins_url('/js/admin.js', __FILE__), false, Multistep_Product_Configurator::VERSION );

					if( Multistep_Product_Configurator::add_fpd_code() ) {

/*
						wp_enqueue_style( 'radykal-admin' );
				    	wp_enqueue_style( 'fpd-admin' );
				    	wp_enqueue_script( 'radykal-admin' );
						wp_enqueue_script( 'fpd-admin' );
*/

						require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/designs.php' );

						wp_enqueue_style( 'fpd-design-options', plugins_url('/admin/react-app/css/design-options.css', FPD_PLUGIN_ROOT_PHP), array(
							'fpd-semantic-ui',
						), Fancy_Product_Designer::VERSION );

						wp_enqueue_script( 'fpd-design-options', plugins_url('/admin/react-app/js/design-options.js', FPD_PLUGIN_ROOT_PHP), array(
							'fpd-semantic-ui',
							'fpd-admin'
						), Fancy_Product_Designer::VERSION );

						wp_add_inline_script( 'fpd-design-options', FPD_Admin_Scripts_Styles::REACT_NO_CONFLICT_JS, 'after' );

						wp_localize_script( 'fpd-design-options', 'fpd_design_opts', array(
							'labels' => FPD_Labels_Designs::get_labels(),
						) );

					}

				}

		    }
		}
	}
}

new MSPC_Admin_Scripts_Styles();

?>
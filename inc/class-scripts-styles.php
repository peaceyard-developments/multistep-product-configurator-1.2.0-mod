<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('MSPC_Scripts_Styles') ) {

	class MSPC_Scripts_Styles {

		public static $add_script = false;

		public function __construct() {

			add_action( 'wp_enqueue_scripts',array( &$this,'enqueue_styles' ) );
			add_action( 'wp_footer', array(&$this, 'footer_handler') );

		}

		//includes scripts and styles in the frontend
		public function enqueue_styles() {

			global $post;

			//only enqueue css and js files when necessary
			if( isset($post->ID) && mspc_enabled($post->ID) ) {

				wp_enqueue_style( 'semantic-ui', plugins_url('/semantic/css/semantic.min.css', MSPC_PLUGIN_ROOT_PHP), false, '0.19.0' );
				wp_enqueue_style( 'mspc', plugins_url('/css/multistep-product-configurator.css', MSPC_PLUGIN_ROOT_PHP), false, Multistep_Product_Configurator::VERSION );

			}

		}

		public function footer_handler() {

			if( self::$add_script ) {

				wp_enqueue_script( 'mspc', plugins_url('/js/multistep-product-configurator.min.js', MSPC_PLUGIN_ROOT_PHP), array('jquery'), Multistep_Product_Configurator::VERSION );

			}

		}

	}

}

new MSPC_Scripts_Styles();
?>
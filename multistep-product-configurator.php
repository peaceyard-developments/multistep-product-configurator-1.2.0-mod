<?php
/*
Plugin Name: Multistep Product Configurator for WooCommerce 1.2.0 - Mod
Plugin URI: http://codecanyon.net/item/multistep-product-configurator-for-woocommerce-/8749384
Description: Create a Multistep Product Configurator with the attributes and variations of your products.
Version: 1.2.0
Author: radykal.me
Modified by: Manoj R. Randeni
Author URI: https://radykal.me
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



if (!defined('MSPC_PLUGIN_DIR'))
    define( 'MSPC_PLUGIN_DIR', dirname(__FILE__) );

if (!defined('MSPC_PLUGIN_ROOT_PHP'))
    define( 'MSPC_PLUGIN_ROOT_PHP', dirname(__FILE__).'/'.basename(__FILE__)  );

if (!defined('MSPC_PLUGIN_ADMIN_DIR'))
    define( 'MSPC_PLUGIN_ADMIN_DIR', dirname(__FILE__) . '/admin' );


if( !class_exists('Multistep_Product_Configurator') ) {

	class Multistep_Product_Configurator {

		const VERSION = '1.2.0';
		const MIN_FPD_VERSION = '4.2.0';
		const CAPABILITY = "edit_mspc";
		const DEMO = false;

		public function __construct() {

			require_once(MSPC_PLUGIN_DIR.'/inc/mspc-functions.php');
			require_once(MSPC_PLUGIN_ADMIN_DIR.'/class-admin.php');
			require_once(MSPC_PLUGIN_DIR.'/inc/class-scripts-styles.php');

			add_action( 'plugins_loaded', array( &$this,'plugins_loaded' ) );
			add_action( 'init', array( &$this, 'init') );

		}

		public function plugins_loaded() {

			load_plugin_textdomain( 'radykal', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

		}

		public function init() {

			require_once(MSPC_PLUGIN_DIR.'/inc/class-frontend-product.php');

		}

		public static function add_fpd_code() {

			return class_exists('Fancy_Product_Designer') && version_compare(Fancy_Product_Designer::VERSION, Multistep_Product_Configurator::MIN_FPD_VERSION, '>=');

		}

	}
}

new Multistep_Product_Configurator();

<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('MSPC_Admin_Settings') ) {

	class MSPC_Admin_Settings {

		public function __construct() {

		}

		/**
		 * Get default options
		 *
		 */
		public static function get_options() {

			$options = array(

				'module' => self::get_module_options(),
				'grid_item_layout' => array(
					'vertical'	 => __( 'Vertical', 'radykal' ),
					'horizontal' => __( 'Horizontal', 'radykal' )
				),
				'columns' => array(
					'1'	 => __( 'One', 'radykal' ),
					'2' => __( 'Two', 'radykal' ),
					'3'	 => __( 'Three', 'radykal' ),
					'4'	 => __( 'Four', 'radykal' ),
					'5'	 => __( 'Five', 'radykal' ),
					'6'	 => __( 'Six', 'radykal' ),
					'7'	 => __( 'Seven', 'radykal' ),
					'8'	 => __( 'Eight', 'radykal' ),
					'9'	 => __( 'Nine', 'radykal' ),
					'6'	 => __( 'Ten', 'radykal' ),
				),
				'template_layout' => array(
					'none' => __( 'None', 'radykal' ),
					'mspc-fullwidth-summary' => __( 'Fullwidth Summary', 'radykal' )
				),
				'product_image' => array(
					'default' => __( 'Default', 'radykal' ),
					'hidden' => __( 'Hidden', 'radykal' ),
					'under_title' => __( 'Under Title', 'radykal' ),
					'under_mspc' => __( 'Under MSPC', 'radykal' ),
				),
				'module_position' => array(
					'after_title' => __( 'After Title', 'radykal' ),
					'after_short_desc' => __( 'After Short Description', 'radykal' ),
					'after_product_image' => __( 'After Product Image', 'radykal' ),
					'before_product_con' => __( 'Before Product Container', 'radykal' ),
				),
				'debug_mode' => 'no',
				'auto_next' => 'no',
				'step_by_step' => 'no',
				'tooltip_title' => 'no'

			);

			if( Multistep_Product_Configurator::add_fpd_code() ) {

				$options['module_position']['before_fancy_product_designer'] = __( 'Before Fancy Product Designer', 'radykal' );
				$options['module_position']['after_fancy_product_designer'] = __( 'After Fancy Product Designer', 'radykal' );

				$options['fpd_modules'] = '';
			}

			return $options;

		}

		/**
		 * Get the module options
		 *
		 */
		public static function get_module_options() {

			return array(
				'tabs'	 => __( 'Tabs', 'radykal' ),
				'accordion' => __( 'Accordion', 'radykal' ),
				'steps'	 => __( 'Steps', 'radykal' ),
				'steps-vertical'	 => __( 'Steps Vertical', 'radykal' )
			);

		}


		/**
		 * Get an option value. If no value is found in database, return default value
		 *
		 */
		public static function get_option( $key ) {

			if( get_option($key) === false ) {

				return self::get_default_option($key);

			}
			else {

				//check if option is type of number and has an empty string as value
				if( self::get_option_type($key) == 'number' && trim(get_option($key)) == '') {
					return self::get_default_option($key);
				}
				else {
					return mspc_convert_string_value_to_int(get_option($key));
				}

			}

		}

		/**
		 * Get the default value of an option
		 *
		 */
		public static function get_default_option( $key ) {

			foreach( self::get_settings() as $section_option ) {
				if(isset($section_option['id']) && $section_option['id'] == $key) {
					return mspc_convert_string_value_to_int($section_option['default']);
					break;
				}
			}

			return false;

		}

		/**
		 * Get the type of an option
		 *
		 */
		public static function get_option_type( $key ) {


			foreach( self::get_settings() as $section_option ) {
				if(isset($section_option['id']) && $section_option['id'] == $key) {
					return $section_option['type'];
					break;
				}
			}

			return false;

		}

	}

}

new MSPC_Admin_Settings();

?>
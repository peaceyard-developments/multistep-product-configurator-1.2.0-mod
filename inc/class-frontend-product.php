<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!class_exists('MSPC_Frontend_Product')) {

	class MSPC_Frontend_Product
	{

		public function __construct()
		{

			add_filter('body_class', array(&$this, 'add_class'));
			add_action('wp_head', array(&$this, 'head_handler'));
		}

		//add fancy-product class in body
		public function add_class($classes)
		{

			global $post;
			if (isset($post->ID) && mspc_enabled($post->ID)) {

				$classes[] = 'mspc-product';

				$template_layout = get_post_meta($post->ID, 'mspc_template_layout', true);
				if ($template_layout && $template_layout != 'none') {
					$classes[] = $template_layout;
				}
			}

			return $classes;
		}

		//used to reposition the product image if requested
		public function head_handler()
		{

			global $post;

			if (isset($post->ID) && mspc_enabled($post->ID)) {

				$product_image = get_post_meta($post->ID, 'mspc_product_image', true);

				//hide product image
				if ($product_image == 'hidden') {
					remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
				}
				//position under product title
				else if ($product_image == 'under_title') {
					remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
					add_action('woocommerce_single_product_summary', 'woocommerce_show_product_images', 5);
				}
				//position under mspc
				else if ($product_image == 'under_mspc') {
					remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
					add_action('woocommerce_single_product_summary', 'woocommerce_show_product_images', 10);
				}

				$module_pos = get_post_meta($post->ID, 'mspc_module_position', true);

				//hide product image
				if ($module_pos == 'after_short_desc') {
					add_action('woocommerce_single_product_summary', array(&$this, 'add_mspc_form'), 25);
				}
				//under product image
				else if ($module_pos == 'after_product_image') {
					add_action('woocommerce_before_single_product_summary', array(&$this, 'add_mspc_form'), 25);
				}
				//before product container
				else if ($module_pos == 'before_product_con') {
					add_action('woocommerce_before_single_product', array(&$this, 'add_mspc_form'), 20);
				}
				//before fancy product designer
				else if ($module_pos == 'before_fancy_product_designer') {
					// MRR - Add MSPC even with raq
					if (!class_exists('YITH_YWRAQ_Order_Request')) {
						add_action('fpd_before_product_designer', array(&$this, 'add_mspc_form'), 20);
					} else {
						add_action('fpd_before_product_designer_raq', array(&$this, 'add_mspc_form'), 20);
					}
					// MRR -END
				}
				//after fancy product designer
				else if ($module_pos == 'after_fancy_product_designer') {
					add_action('fpd_after_product_designer', array(&$this, 'add_mspc_form'), 20);
				}
				//default: under title
				else {
					add_action('woocommerce_single_product_summary', array(&$this, 'add_mspc_form'), 6);
				}
			}
		}

		//the actual product designer will be added
		public function add_mspc_form()
		{

			global $product;

			if (mspc_enabled($product->get_id()) && $product->has_attributes()) {

				MSPC_Scripts_Styles::$add_script = true;
				$fpd_enabled = class_exists('Fancy_Product_Designer');

				$module = get_post_meta($product->get_id(), 'mspc_module', true); //tabs, steps, accordion
				$columns = intval(get_post_meta($product->get_id(), 'mspc_columns', true)); //1-6
				$grid_item_layout = get_post_meta($product->get_id(), 'mspc_grid_item_layout', true); //horizontal, vertical
				$auto_next = get_post_meta($product->get_id(), 'mspc_auto_next', true); //auto-next
				$auto_next_class = $auto_next == 'yes' ? ' mspc-auto-next' : '';
				$step_by_step = get_post_meta($product->get_id(), 'mspc_step_by_step', true); //auto-next
				$step_by_step_class = $step_by_step == 'yes' ? ' mspc-step-by-step' : '';
				$tooltip_title = get_post_meta($product->get_id(), 'mspc_tooltip_title', true);

				$attributes = $product->get_variation_attributes();
				$attribute_count = 0;
				$attribute_keys = array_keys($attributes);
				$loop_length = sizeof($attributes);

				//FPD
				$fpd_modules = get_post_meta($product->get_id(), 'mspc_fpd_modules', true);
				$fpd_modules = json_decode($fpd_modules, true);
				$fpd_modules = $fpd_modules ? $fpd_modules : array();

				$minus_count = 0;
				$loop_length += sizeof($fpd_modules);

?>
				<div class="mspc-wrapper mspc-clearfix mspc-module-<?php echo $module; ?><?php echo $auto_next_class;
																							echo $step_by_step_class; ?>">

					<?php if ($module == 'accordion') : //accordion 
					?>

						<div class="mspc-accordion">
							<?php while ($attribute_count < $loop_length) : ?>

								<?php
								$key = array_search(strval($attribute_count + 1), array_column($fpd_modules, 'position'));
								if ($key !== false) :
									$fpd_module = $fpd_modules[$key];
									$target = 'mspc-fpd-module--' . $fpd_module['module'];
								?>
									<a href="#" class="mspc-menu-item" data-target=".<?php echo $target; ?>">
										<i class="icon add"></i><span><?php echo esc_html($fpd_module['label']); ?></span>
									</a>
									<div class="mspc-content mspc-fpd-module <?php echo $target; ?>">
										<div class="mspc-tab-content ui column">
											<?php echo do_shortcode('[fpd_module type="' . $fpd_module['module'] . '"]'); ?>
										</div>
									</div>
								<?php
									$minus_count++;
									$attribute_count++;
									continue;
								endif;

								if (!isset($attribute_keys[($attribute_count - $minus_count)])) {
									$attribute_count++;
									continue;
								}

								$attr_key = $attribute_keys[($attribute_count - $minus_count)];
								$options = $attributes[$attr_key];
								$attribute_count++;
								?>
								<a href="#" class="mspc-menu-item" data-target=".mspc-<?php echo $attr_key; ?>">
									<i class="icon add"></i><span><?php echo wc_attribute_label($attr_key); ?></span>
								</a>
								<div class="mspc-content">
									<div class="mspc-tab-content mspc-variations mspc-clearfix ui column grid doubling mspc-<?php echo $attr_key . ' ' . $this->get_column_class($columns); ?>">
										<?php
										echo $this->get_variation_items(
											$attr_key,
											$options,
											$grid_item_layout,
											$columns,
											$tooltip_title
										);
										?>
									</div>
								</div>

							<?php endwhile; ?>
						</div>

					<?php else : //steps, tabs, vertical steps 
					?>

						<div class="mspc-menu ui <?php echo $this->get_menu_class($module, $loop_length);  ?>">
							<?php while ($attribute_count < $loop_length) :

								$key = array_search(strval($attribute_count + 1), array_column($fpd_modules, 'position'));
								if ($key !== false) :
									$fpd_module = $fpd_modules[$key];
									$target = 'mspc-fpd-module--' . $fpd_module['module'];
							?>
									<a href="#" class="mspc-menu-item ui <?php echo $this->get_menu_item_class($module); ?>" data-target=".<?php echo $target; ?>">
										<?php echo esc_html($fpd_module['label']); ?>
									</a>
								<?php
									$minus_count++;
									$attribute_count++;
									continue;
								endif;

								if (!isset($attribute_keys[($attribute_count - $minus_count)])) {
									$attribute_count++;
									continue;
								}

								$attr_key = $attribute_keys[($attribute_count - $minus_count)];
								$attribute_count++;
								if (is_null($attr_key))
									continue;
								?>
								<a class="mspc-menu-item ui <?php echo $this->get_menu_item_class($module); ?>" data-target=".mspc-<?php echo $attr_key; ?>">
									<?php echo wc_attribute_label($attr_key); ?>
								</a>
							<?php endwhile; ?>
						</div><!-- Menu -->

						<div class="mspc-content ui <?php echo $this->get_content_class($module); ?>">

							<?php
							$attribute_count = $minus_count = 0;
							while ($attribute_count < $loop_length) :

								$key = array_search(strval($attribute_count + 1), array_column($fpd_modules, 'position'));
								if ($key !== false) :
									$fpd_module = $fpd_modules[$key];
									$target = 'mspc-fpd-module--' . $fpd_module['module'];
							?>
									<div class="mspc-tab-content mspc-fpd-module <?php echo $target; ?>">
										<div class="ui column">
											<?php echo do_shortcode('[fpd_module type="' . $fpd_module['module'] . '"]'); ?>
										</div>
									</div>
								<?php
									$minus_count++;
									$attribute_count++;
									continue;
								endif;

								if (!isset($attribute_keys[($attribute_count - $minus_count)])) {
									$attribute_count++;
									continue;
								}

								$attr_key = $attribute_keys[($attribute_count - $minus_count)];
								$options = $attributes[$attr_key];
								$attribute_count++;

								?>
								<div class="mspc-tab-content mspc-variations mspc-clearfix ui column grid doubling mspc-<?php echo $attr_key . ' ' . $this->get_column_class($columns); ?>">
									<?php
									echo $this->get_variation_items(
										$attr_key,
										$options,
										$grid_item_layout,
										$columns,
										$tooltip_title
									);
									?>
								</div>
							<?php endwhile; ?>

						</div><!-- Content -->

					<?php endif; ?>
					<a href="#" class="mspc-clear-selection"><?php _e('Clear selection', 'radykal'); ?></a>

				</div><!-- Wrapper --->

			<?php
			}
		}

		private function get_variation_items($attribute_name, $options, $grid_item_layout = 'vertical', $columns = 3, $tooltip_title = '')
		{

			$orderby = wc_attribute_orderby($attribute_name);

			$args = array();
			switch ($orderby) {
				case 'name':
					$args = array('orderby' => 'name', 'hide_empty' => false, 'menu_order' => false);
					break;
				case 'id':
					$args = array('orderby' => 'id', 'order' => 'ASC', 'menu_order' => false, 'hide_empty' => false);
					break;
				case 'menu_order':
					$args = array('menu_order' => 'ASC', 'hide_empty' => false);
					break;
			}

			$terms = get_terms($attribute_name, $args);

			ob_start();

			if (isset($terms->errors)) {
			?>
				<div class="column"><?php printf(__('No attributes found for this taxonomy. Be sure that they are visible on the product page and you created them via the <a href="%s" target="_blank">Attributes admin page</a>.'), admin_url() . 'edit.php?post_type=product&page=product_attributes'); ?></div>
				<?php
				return false;
			}

			foreach ($terms as $term) :

				if (!in_array($term->slug, $options)) {
					continue;
				}

				$fpd_params = '';
				$fpd_thumbnail = '';
				if (class_exists('FPD_Parameters')) {

					$fpd_params = stripslashes(get_option('mspc_variation_fpd_params_' . $term->term_id, ''));

					if (!empty($fpd_params)) {

						if (fpd_is_json($fpd_params)) //+1.2.0
							$fpd_params = json_decode($fpd_params, true);
						else {
							parse_str($fpd_params, $fpd_params);
						}
						if (isset($fpd_params['enabled']) && fpd_not_empty($fpd_params['enabled'])) {
							//convert string to array
							$fpd_params = FPD_Parameters::convert_parameters_to_string($fpd_params);
						} else
							$fpd_params = '';
					}

					$fpd_thumbnail = get_option('mspc_variation_fpd_thumbnail_' . $term->term_id, '');
				}

				$stage_image = $image_html = '';

				$image_url = get_option('mspc_variation_image_' . $term->term_id);
				if ($image_url !== false && !empty($image_url)) {

					$image_id = $this->get_image_id($image_url);
					if (!is_null($image_id)) {
						$stage_image = wp_get_attachment_image_src($image_id, empty($fpd_params) ? 'shop_single' : 'full');
						$stage_image = $stage_image[0];
						$image_thumb = empty($fpd_thumbnail) ? $stage_image : $fpd_thumbnail;
					} else {
						$stage_image = $image_url;
					}

					$image_thumb = empty($fpd_thumbnail) ? $stage_image : $fpd_thumbnail;
					$image_html = '<img src="' . $image_thumb . '" alt="' . $term->name . '" class="mspc-attribute-image rounded ui image" />';
				}

				$description_html = '';
				if (!empty($term->description)) {
					$description_html = '<p>' . $term->description . '</p>';
				}

				if ($grid_item_layout == 'vertical') :
				?>

					<div class="mspc-variation mspc-vertical column" data-parameters='<?php echo $fpd_params; ?>' data-image='<?php echo $stage_image; ?>' data-title="<?php echo $term->name; ?>">
						<div class="mspc-clearfix">
							<div class="mspc-radio ui radio checkbox">
								<input type="radio" name="<?php echo $attribute_name; ?>" value="<?php echo esc_attr($term->slug); ?>">
								<label></label>
							</div>
							<?php echo $image_html; ?>
							<div class="mspc-text-wrapper">
								<?php if ($tooltip_title !== 'yes') : ?>
									<strong class="mspc-attribute-title"><?php echo $term->name; ?></strong>
								<?php endif; ?>
								<?php echo $description_html; ?>
							</div>
						</div>
						<?php if ($tooltip_title == 'yes') : ?>
							<div class="mspc-tooltip"><?php echo $term->name; ?></div>
						<?php endif; ?>
					</div>

				<?php else : ?>

					<div class="mspc-variation mspc-horizontal column" data-parameters='<?php echo $fpd_params; ?>' data-image='<?php echo $stage_image; ?>' data-title="<?php echo $term->name; ?>">
						<div class="mspc-clearfix">
							<?php echo $image_html; ?>
							<div class="mspc-text-wrapper">
								<?php if ($tooltip_title !== 'yes') : ?>
									<strong class="mspc-attribute-title"><?php echo $term->name; ?></strong>
								<?php endif; ?>
								<?php echo $description_html; ?>
								<div class="mspc-radio ui radio checkbox">
									<input type="radio" name="<?php echo $attribute_name; ?>" value="<?php echo esc_attr($term->slug); ?>">
									<label></label>
								</div>
							</div>
						</div>
						<?php if ($tooltip_title == 'yes') : ?>
							<div class="mspc-tooltip"><?php echo $term->name; ?></div>
						<?php endif; ?>
					</div>

<?php endif;

			endforeach;

			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

		private function get_menu_class($type, $columns)
		{

			switch ($type) {
				case 'steps':
					return 'steps ' . $this->get_column_class($columns);
				case 'steps-vertical':
					return 'steps vertical ';
				case 'accordion':
					return 'fluid accordion';
				default:
					return 'top attached tabular menu';
			}
		}

		private function get_menu_item_class($type)
		{

			switch ($type) {
				case 'steps':
					return 'step item';
				case 'steps-vertical':
					return 'step item';
				case 'accordion':
					return 'fluid accordion';
				default:
					return 'item';
			}
		}

		private function get_content_class($type)
		{

			switch ($type) {
				case 'steps':
					return 'segment';
				case 'steps-vertical':
					return 'segment';
				case 'accordion':
					return 'fluid accordion';
				default:
					return 'bottom attached segment';
			}
		}

		private function get_column_class($columns)
		{

			switch ($columns) {
				case 2:
					return 'two';
				case 3:
					return 'three';
				case 4:
					return 'four';
				case 5:
					return 'five';
				case 6:
					return 'six';
				case 7:
					return 'seven';
				case 8:
					return 'eight';
				case 9:
					return 'nine';
				case 10:
					return 'ten';
				default:
					return 'one';
			}
		}

		private function get_image_id($url)
		{

			//MRR-Get the domain
			if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $url, $regs)) {
				$domain = $regs['domain'];
			} else {
				$domain = "www";
			}
			// Split the $url into two parts with the wp-content directory as the separator
			$parsed_url  = explode(parse_url(WP_CONTENT_URL, PHP_URL_PATH), $url);
			// Get the host of the current site and the host of the $url, ignoring www
			$this_host = str_ireplace($domain, '', parse_url(home_url(), PHP_URL_HOST));
			$file_host = str_ireplace($domain, '', parse_url($url, PHP_URL_HOST));

			// Return nothing if there aren't any $url parts or if the current host and $url host do not match
			if (!isset($parsed_url[1]) || empty($parsed_url[1]) || ($this_host != $file_host)) {
				return;
			}
			//MRR-END

			//MRR - Remove slow query of transparent.png
			if ($parsed_url[1] === '/uploads/2020/01/transparent.png') {
				$attachment = '/uploads/2020/01/transparent.png';
			} else {
				global $wpdb;
				$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1]));
			}

			return $attachment[0];
			//MRR-END

			/* // Split the $url into two parts with the wp-content directory as the separator
			$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
			// Get the host of the current site and the host of the $url, ignoring www
			$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
			$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

			// Return nothing if there aren't any $url parts or if the current host and $url host do not match
			if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
				return;
			}

			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
			// Returns null if no attachment is found
			return $attachment[0]; */
		}
	}
}

new MSPC_Frontend_Product();

?>
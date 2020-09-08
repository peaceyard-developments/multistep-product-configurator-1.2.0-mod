jQuery(document).ready(function() {

	var $wrapper = jQuery('.mspc-wrapper'),
		$menuItems = $wrapper.find('.mspc-menu-item'),
		$content = $wrapper.find('.mspc-content'),
		$variationItems = $content.find('.mspc-variation'),
		$variationForm = jQuery('.variations_form'),
		$selectVariations = $variationForm.find('.variations select'),
		initialVariations = [],
		noInitialScroll = false;

	jQuery('.mspc-clear-selection').click(function(evt) {

		$variationItems.removeClass('active').show().find('input[type="radio"]').prop('checked', false);
		$variationForm.find('.reset_variations').click();
		$menuItems.removeClass('active').first().removeClass('disabled').click().addClass('active');

		_menuItemsState();

		evt.preventDefault();

	});

	//variation gets selected
	$variationItems.click(function() {

		var $this = jQuery(this);

		$this.parents('.mspc-variations').find('.mspc-variation').removeClass('active');
		$this.addClass('active').find('input[type="radio"]').prop('checked', true).change();

		if(initialVariations === 'fpd-ready') {
			_addElementToFpd($this);
		}

	});

	//set and get default variations
	$selectVariations.each(function(i, item) {

		var initialVariation = jQuery('input[name="'+this.id+'"]')
		.filter('[value="'+jQuery(this).val()+'"]')
		.parents('.mspc-variation');

		//store initial variation
		if(initialVariation.size()) {
			initialVariations.push(initialVariation);
		}

		//loop trough all set default variations and click each
		if(i == $selectVariations.size()-1) {

			for(var j=0; j < initialVariations.length; j++) {
				initialVariations[j].click();
			}
		}

		$wrapper.trigger('mspc_defaults_set');

	});


	//radio changed, update select boxes
	$content.find('.mspc-variation input[type="radio"]').change(function() {

		var selectIndex = jQuery('.mspc-variations').index(jQuery(this).parents('.mspc-variations'));
			$selectBox = $selectVariations.eq(selectIndex);

		//set select value
		$selectBox.focusin().val(this.value).change();

		_menuItemsState();

		//go to next tab if wished
		if($wrapper.hasClass('mspc-auto-next')) {
			jQuery('.mspc-menu-item.active').nextAll('.mspc-menu-item:first').click();
		}

		$wrapper.trigger('mspc_variation_change', [selectIndex, this.value]);

	});

	$menuItems.click(function(evt) {

		var $this = jQuery(this),
			selectId = $this.data('target').replace('.mspc-', ''),
			$select = jQuery('select#'+selectId+'').focusin();

		if($this.hasClass('disabled')) {
			return false;
		}

		//hide all variation items
		if($select.children('option.active,option.enabled').size() > 0)  {
			$variationItems.find('input[type="radio"]')
			.filter('[name="'+selectId+'"]')
			.parents('.mspc-variation').hide();
		}

		//loop through all active option and show corresponding variation item
		$select.children('option.active,option.enabled').each(function(i, option) {

			var $option = jQuery(option),
				selectId = $option.parent('select').attr('id');

			$variationItems.find('input[type="radio"]') //all radio buttons in variations
			.filter('[name="'+selectId+'"]') //filter by name
			.filter('[value="'+option.value+'"]') //filter by value
			.parents('.mspc-variation:first').show() //show variation


		});

		if($wrapper.find('.mspc-accordion').size() > 0) {

			//accordion
			if( !$this.hasClass('active') ) {

				$menuItems.children('.icon').removeClass('minus').addClass('add');
				$this.children('.icon').removeClass('add').addClass('minus');

				var time = 300;
				$content.slideUp(time);
				$this.next('.mspc-content:first').delay(time).slideDown(time, function() {

					//scroll to selected tab
					if(noInitialScroll) {
						jQuery([document.documentElement, document.body]).animate({
					        scrollTop: $this.offset().top
					    }, 0);
					}

				    noInitialScroll = true;

				});

			}

		}
		else {

			//steps, tabs
			$content.find('.mspc-tab-content').hide();
			jQuery($this.data('target')).show();

		}

		$menuItems.removeClass('active');
		$this.addClass('active');


		evt.preventDefault();

	});

	//delay to update select boxes
	setTimeout(function() {
		$menuItems.first().click();
	}, 10);

	function _menuItemsState() {

		if($wrapper.hasClass('mspc-step-by-step')) {

			$menuItems.filter(':not(.active,:first)').addClass('disabled');

			$selectVariations.each(function(i, item) {

				if(this.value && this.value != '') {

					$menuItems.filter('[data-target=".mspc-'+this.id+'"]')
					.nextAll('.mspc-menu-item:first').removeClass('disabled');

				}

			});

		}

	}


	//fancy product designer

	jQuery('.fpd-container').on('productCreate', function() {

		if(document.URL.search('cart_item_key') == -1) {
			for(var i=0; i < initialVariations.length; ++i) {
				_addElementToFpd(initialVariations[i]);
			}
			jQuery('.fpd-container').trigger('mspc_init_variations_set');
		}
		initialVariations = 'fpd-ready';

	});

	function _addElementToFpd(variation) {

		$variation = jQuery(variation);
		if(fancyProductDesigner) {
			if($variation.data('parameters') && typeof $variation.data('parameters') === 'object' && $variation.data('image')) {

				fancyProductDesigner.addElement(
					'image',
					$variation.data('image'), //image source
					$variation.data('title'), //title
					$variation.data('parameters') //parameters
				);
			}

		}

	}

	$menuItems.first().click().addClass('active');

	_menuItemsState();

});
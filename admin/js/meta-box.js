jQuery(document).ready(function($) {

	$('#_mspc').change(function() {
		if($(this).is(':checked')) {
			$('.hide_if_mspc').show();
		}
		else {
			$('.hide_if_mspc').hide();
		}
	}).change();


	//FPD
	var $fpdModulesList = $('#mspc-fpd-modules-list'),
		$fpdModulesInput = $('[name="mspc_fpd_modules"]');

	$fpdModulesList
	.on('change', 'input', fpdModulesUpdated)
	.on('click', 'h3 > a', function(evt) {

		evt.preventDefault();

		$(this).parents('.wc-metabox:first').remove();
		fpdModulesUpdated();

	})

	$('#mspc-fpd-add-module').click(function(evt) {

		evt.preventDefault();

		var module = $('#mspc-fpd-module').val();

		createFPDModuleBox(module);


	});

	function createFPDModuleBox(module, position, label) {

		position = position === undefined ? 1 : position;
		label = label === undefined ? 'Tab Label' : label;

		var moduleName = module.replace('-', ' ');

		$fpdModulesList.append('<div class="wc-metabox" data-module="'+module+'"><h3><strong>'+moduleName+'</strong><input type="number" step="1" min="1" value="'+position+'" class="mspc-fpd-module-pos woocommerce-help-tip" data-tip="Tab Position: 1 for first, 2 for second position and so on." /><input type="text" class="mspc-fpd-module-label woocommerce-help-tip" value="'+label+'"  data-tip="Tab Label" /><a href="#" class="delete">Remove</a></h3></div>');

		fpdModulesUpdated();

		$( document.body ).trigger( 'init_tooltips' );

	}

	function fpdModulesUpdated() {

		var modulesArr = [];

		$fpdModulesList.children('.wc-metabox').each(function(i, item) {

			var $item = $(item);

			modulesArr.push({
				module: $item.data('module'),
				position: $item.find('[type="number"]').val(),
				label: $item.find('[type="text"]').val()
			})

		});

		$fpdModulesInput.val(JSON.stringify(modulesArr));

	}

	try {

		var currentModules = JSON.parse($fpdModulesInput.val());

		if($.isArray(currentModules)) {

			currentModules.forEach(function(item) {
				createFPDModuleBox(item.module, item.position, item.label);
			});

		}

	}
	catch(evt) {}

});
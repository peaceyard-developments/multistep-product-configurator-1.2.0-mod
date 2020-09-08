jQuery(document).ready(function($) {

	var mediaUploader = null;

	$('#mspc-add-image').click(function(evt) {

		mediaUploader = wp.media({
            multiple: false
        });

		mediaUploader.on('select', function() {

			$('#mspc-image-url').val(mediaUploader.state().get('selection').toJSON()[0].url);

			mediaUploader = null;
        });

        mediaUploader.open();

		evt.preventDefault();

	});

/*
	var $modalWrapper = $('#fpd-modal-edit-options'),
		$paramsInput = $('#mspc-fpd-params'),
		$thumbnailInput = $('#mspc-fpd-thumbnail');

	$('#mspc-set-fpd-params').click(function(evt) {

		$modalWrapper.parent().css('display', 'block');
		fpdSetDesignFormFields($paramsInput, $thumbnailInput);

		evt.preventDefault();

	});

	//save and close modal
	$modalWrapper.on('click', '.fpd-save-admin-modal', function(evt) {

		$thumbnailInput.val($('#fpd-set-design-thumbnail').data('thumbnail'));
		$paramsInput.val($modalWrapper.find('form').serialize().replace(/[^&]+=&/g, '').replace(/&[^&]+=$/g, ''));

		closeModal($modalWrapper);

		evt.preventDefault();

	})
	.on('click', '.fpd-close-modal', function(evt) {

		closeModal($modalWrapper);

		evt.preventDefault();

	});
*/

});

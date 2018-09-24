(function($) {
	$(document).ready(function() {
		$('body #wpcfur-email-template').trumbowyg({
			btns: [
				['viewHTML'],
				['undo', 'redo'],
				['formatting'],
				['strong', 'em', 'del'],
				['superscript', 'subscript'],
				['link'],
				['insertImage'],
				['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
				['unorderedList', 'orderedList'],
				['horizontalRule'],
				['removeformat']
			],
			resetCss: true,
			removeformatPasted: true,
			autogrow: true,
			autogrowOnEnter: true,
			urlProtocol: true
		});
	});
})(jQuery);

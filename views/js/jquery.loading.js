/**
 * Loading dialog plugin for jQuery
 *
 * Useful when making Ajax calls. It shows a loading div over the specified element.
 * If the element specified doesn't have an ID, an ID is generated for it and
 * assigned to it.
 *
 * Usage:
 * =================
 * $('#someDiv').loading('show'); // shows the loading div
 * $('#someDiv').loading('hide'); // hides the loading div
 */

(function ($) {
	$.fn.loading = function (val) {

		var isLoading = (document.getElementById(this[0].id + '-loading-wrapper') !== null),

			methods = {
				show: function () {
					var i,
						str,
						offset = this.offset(),
						height;

					if (!this[0].getAttribute('id')) {
						for (i = 0; document.getElementById('autogen-loading-' + i); i++) {
						}
						this[0].setAttribute('id', 'autogen-loading-' + i);
					}

					str = $('#loading-div-tmpl')
						.val()
						.replace(/#\{id\}/, this[0].getAttribute('id'));

					$(this).parent().append(str);

					offset.top -= 5;
					height = this.height() + 10;
					$('#' + this[0].id + "-loading-wrapper")
						.offset(offset)
						.css('height', height)
						.css('width', this.width())
						.removeClass('hidden');
				},
				hide: function () {
					$('#' +  this[0].id + '-loading-wrapper').remove();
				}
			};

		if (val === 'show' && !isLoading) {
			methods.show.apply(this);

		} else if (val === 'hide' && isLoading) {
			methods.hide.apply(this);
		}
		return this;
	};
}(jQuery));
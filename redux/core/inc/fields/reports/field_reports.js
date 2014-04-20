/* global redux_change */
(function($){
	"use strict";

	$.redux = $.redux || {};

	$(document).ready(function () {
		//multi text functionality
		$.redux.reports();
	});

	$.redux.reports = function(){
		$('.report-remove').live('click', function() {
			var confirm_delete = confirm("Are you sure you want to delete this report?");
			if (confirm_delete) {
				$(this).parents('.form-report').remove();;
			}
			return false;
		});

		$('.report-add').click(function(){
			var parent = $(this).parents("table");
			var clone = parent.find(".form-report-hidden").clone(true).removeClass("form-report-hidden");
			clone.find("input").removeAttr("disabled");
			$(this).before(clone);
			return false;
		});
	};
})(jQuery);
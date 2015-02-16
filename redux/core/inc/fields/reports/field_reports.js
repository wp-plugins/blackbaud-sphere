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
			clone.find("input").removeAttr("disabled");			clone.find("select").removeAttr("disabled");
			$(this).before(clone);
			return false;
		});
		
		$('.empty-supporters').click(function(){
			var data = {
				'action': 'empty_supporters',
				'empty': 1
			};
			$('#empty-supporters').text("Emptying...");
			$.post(ajaxurl, data, function(response) {
				$('#empty-supporters').text(response);
			});
			return false;
		});
		
		$('.sync-manually').click(function(){
			var data = {
				'action': 'sync_manually',
				'sync': 1
			};
			$('#sync-manually').text("Syncing...");
			$.post(ajaxurl, data, function(response) {
				$('#sync-manually').text(response);
			});
			return false;
		});
	};
})(jQuery);
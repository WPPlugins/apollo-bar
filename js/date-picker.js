jQuery(document).ready(function($) {
	if($('#apb_start_date').length) {
		$(function() {
			var pickerOpts = {
				dateFormat: 'yy-mm-dd'
			};
			$('#apb_start_date').datepicker(pickerOpts);
			$('#apb_end_date').datepicker(pickerOpts);
		});
	}
});
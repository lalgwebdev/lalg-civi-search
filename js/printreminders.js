// Simplify the PDF printing page for Reminder Letters
CRM.$(function($) {
	jQuery(document).ready(function(){
// console.log('JS Ready');	 	

		function checkBold (rep) {
//			console.log('Entered checkBold()');
//			console.log('Bold: ' + $('#cke_24').is(':visible') );
			// Waits for the Bold button in the CKEditor to appear
			if ($('#cke_24').is(':visible') ) {
				setTimeout(function(){
					$('.crm-contact-task-pdf-form-block select#template').val('86').change();
					$('#_qf_LalgPrintReminders_upload-bottom').text('Download and clear flags');
					$('#_qf_LalgPrintReminders_submit_preview-bottom').text('Download');
				}, 500);				
				return;
			}
			if (rep > 0) {setTimeout(() => checkBold(rep-1), 100);}
		}
		
		checkBold(100);					// Limit of 10 seconds
  	  
	});
});
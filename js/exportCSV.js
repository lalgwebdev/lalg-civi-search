// Simplify the CSV Export page for Labels

CRM.$(function($) {
//	console.log("Running JS")
	$('input#CIVICRM_QFID_2_exportOption').click();
	$('input#CIVICRM_QFID_2_exportOption').click(showMappingOption());
	$('input#postal_mailing_export_postal_mailing_export').prop('checked', true);

});

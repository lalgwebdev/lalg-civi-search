jQuery(document).ready(function(){

// Remove all the Actions on the Search Reminders page, except LALG Create Reminder Letters 
	//console.log('Clearing Actions');
	jQuery('select#task.crm-action-menu option[value!="searchactiondesigner_1"]').not(':first-child').remove();
	
});
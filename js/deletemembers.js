jQuery(document).ready(function(){

// Remove all the Actions on the Search Delete Members page, except LALG Delete Members 
	//console.log('Clearing Actions');
	jQuery('select#task.crm-action-menu option[value!="113"]').not(':first-child').remove();
	
});
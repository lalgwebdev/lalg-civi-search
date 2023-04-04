window.onload = function() {

// Remove all the Actions on the Search Reminders page, except LALG Print Reminders 

	jQuery('select#task.crm-action-menu option[value != 115]').not(':first-child').remove();
};
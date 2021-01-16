jQuery(document).ready(function(){

// Remove all the Actions on the Search Labels page, except LALG Print Labels 

	//console.log('Clearing Actions');
	jQuery('select#task option[value!="112"]').not(':first-child').remove();
	
});
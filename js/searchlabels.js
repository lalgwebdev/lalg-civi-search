jQuery(document).ready(function(){

// Remove all the Actions on the Search Labels page, except LALG Print Labels 

	//console.log('Clearing Actions');
//	jQuery('select#task option[value!="112"]').not(':first-child').remove();
	jQuery('select#task option').not(':first-child').each(function(){
      val = jQuery(this).attr("value");
	  if (val != 113 && val != 112) {
		  jQuery(this).remove();
	  }
    });
	
});
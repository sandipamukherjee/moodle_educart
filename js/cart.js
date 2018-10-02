// A $( document ).ready() block.
jQuery( document ).ready(function($) {
    $(".add_to_cart").click(function() {
    	var courseid = $(this).attr("cart-courseid");
	    $.ajax({url: $(this).attr("post-url"), 
	    	data : { cart_courseid : courseid, cart_price : $(this).attr("cart-price"), 'userid' : $(this).attr("userid")},
	    	type: 'POST',
	    	success: function(result){
	    		$("#add_to_cart_"+courseid).css('display', 'none');
	    		$("#view_cart_"+courseid).addClass('view_cart_display');
	    	}
		});
    });

    $(".product_remove").click(function() {
    	remove_cart_courseid = $(this).attr("course-id");
    	remove_cart_userid = $(this).attr("user-id");
    	$.ajax({url: $(this).attr("post-url"), 
	    	data : { remove_cart_courseid : remove_cart_courseid, 'userid' : remove_cart_userid},
	    	type: 'POST',
	    	success: function(result){
	    		location.reload();
	    		//console.log('ok');
	    	}
		});
    });
});
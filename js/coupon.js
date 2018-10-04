jQuery( document ).ready(function($) {
	
	var coupon = url = "";
	$('#coupon_field').blur(function(){
    	url = $('#coupon_valid_url').val();
    	coupon = $(this).val();
    	if (coupon != "") { 
    		$.ajax({url: url, 
		    	data : { coupon : coupon },
		    	type: 'post',
		    	success: function(result){
		    		if(result == 1) {
		    			$('.coupon_info_success').css('display', 'none');
		    			$('.coupon_info').css('display', 'none');
		    			$("#apply_coupon").removeClass('apply_coupon').css('display', 'inline-block');
		    		} else if (result == 0) {
		    			$('.coupon_info_success').css('display', 'none');
		    			$('.coupon_info').css('display', 'block');
		    			$("#apply_coupon").css('display', 'none');
		    		}
		    	}
			});
    	}
	});
	$('#coupon_field').focusin(function() {
		
	});
    $('#apply_coupon').click(function(){
    	var user = $(this).attr('userid');
    	$.ajax({url: url, 
	    	data : { couponapplied : coupon, user : user },
	    	type: 'post',
	    	success: function(result){
	    		console.log(result);
	    		var response = result.split('-')[0];

	    		var message = result.split('-')[1];
	    		if (response == 0) {
	    			$('.coupon_info_success').css('display', 'none');
	    			$('.coupon_info').html(message).addClass('alert-warning').css('display', 'block');
	    		} else if(response == 1) {
	    			$('.coupon_info').css('display', 'none');
	    			$('.coupon_info_success').css('display', 'block');
	    			$(".cart_total tr:nth-child(2)").css('display', 'table-row');
	    			$(".cart_total tr:nth-child(2)").find('.lastcol').html(message);
	    			var now_price = parseFloat($(".cart_total tr.lastrow td.lastcol").text());
	    			var updatedprice = now_price - message;
	    			$(".cart_total tr.lastrow td.lastcol").html(parseFloat(updatedprice).toFixed(2));
	    			$("#hidden_coupon_applied").val(message);
	    		} else if(response == 2) {
	    			$('.coupon_info').css('display', 'none');
	    			$('.coupon_info_success').css('display', 'block');
	    			$(".cart_total tr:nth-child(2)").css('display', 'table-row');
	    			var now_price = parseFloat($(".cart_total tr.lastrow td.lastcol").text());
	    			var percentagediscount = (( now_price * message)/100).toFixed(2);
	    			$(".cart_total tr:nth-child(2)").find('.lastcol').html(percentagediscount);
	    			var updatedprice = $(".cart_total tr.lastrow td.lastcol").text() - percentagediscount;
	    			$(".cart_total tr.lastrow td.lastcol").html(parseFloat(updatedprice).toFixed(2));
	    			$("#hidden_coupon_applied").val(percentagediscount);
	    		}
	    	}
		});
    });
});

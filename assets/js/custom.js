$(document).ready(function(){
	$(".edit-profile-btn").click(function(){
		$(".wrapper").addClass("edit-profile-popup-open");
	})
	
	$(".edit-profile-popup-overlay, .close-edit-popup").click(function(){
		$(".wrapper").removeClass("edit-profile-popup-open");
		$(".wrapper").removeClass("edit-profile-popup-open");
	})
	
	//Tab Menu
	$('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	})

	$(".header-profile").click(function(){
		$(".header-profile ul").slideToggle("300");
	})

	$(".post-follow-back-arrow").click(function(){
		$(this).parent().parent().parent(".post-profile-block").addClass("show-comment-box").siblings().removeClass("show-comment-box");
	});
	
	$( "#datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:'d MM, yy',
		maxDate: '0',
		yearRange: "-100:+0", // last hundred years
	});
	$('#datepicker').datepicker('setDate', new Date());

});

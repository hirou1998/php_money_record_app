$(window).on('load', function(){
	setTimeout(function(){
		$("#wrapper").removeClass("wrapper_none");
		$("nav").removeClass("nav_none");
		$("#loading").css("display", 'none');
	}, 1000)
});
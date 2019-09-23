$(window).on('load', function(){
	setTimeout(function(){
		$("#wrapper").removeClass("wrapper_none");
		$("nav").removeClass("nav_none");
		$("#loading").css("display", 'none');
	}, 1000)
});

Vue.component('api-loading', {
	template: '<div class="api_loading"><span class="loader"></span></div>'
});
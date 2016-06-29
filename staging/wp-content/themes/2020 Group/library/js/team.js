(function($)	{
	
	$(document).ready(function() {
		
		var hash = window.location.hash;

		//console.log(hash);

		if(hash != '') {
			//console.log('hash exists');
			$('#teamTabContent .content').removeClass('active');
			$('#teamTabs dd').removeClass('active');
			livecontent = '#teamTabContent ' + hash;
			livetab = '#teamTabs a[href="' + hash + '"]';
			$(livecontent).addClass('active');
			$(livetab).parent().addClass('active');

		}

		
	});

})(jQuery);
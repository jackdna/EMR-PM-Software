(function($){
	
	/*Extend CustomScrollBar to update scrollable html content*/
	$.mCustomScrollbar.methods.updateContent = function( htmlData, expandHeight ){
		
		var d = $(this).data('mCS');
		
		var dataContainer = this;
		
		if( d !== undefined)
			dataContainer = $("#mCSB_"+d.idx+"_container");
		
		$(dataContainer).html( htmlData );
		
		if( expandHeight !== undefined )
			$(dataContainer).css('height', '100%');
		else
			$(dataContainer).css('height', 'auto');
	};
})(jQuery);
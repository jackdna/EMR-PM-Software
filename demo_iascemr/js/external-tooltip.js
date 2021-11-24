
$(document).ready(function(e) {

	
		(function(){


				var settings = {
						trigger:'click',
						title:'WebUI Popover ',
						content:'<div class="jumbotron"> This is a sample text for typesetting and print industry. </div>',
						width:320,						
						multi:true,						
						closeable:false,
						style:'',
						delay:300,
						padding:true
				};



				// $('a[data-toggle="tab"]').on('click',function(e){
				// 	e.preventDefault();
				// 	var $this = $(this);
				// 	$this.parent().addClass('active').siblings().removeClass('active');
				// 	var $content = $this.closest('.nav-tabs').next().find($this.attr('href'));
				// 	$content.addClass('active').siblings().removeClass('active');
				// });

			



				
				




				// $('.btn-settings').on('click',function(e){
				// 		e.preventDefault();
				// 		$(this).addClass('active').siblings().removeClass('active');
				// 		var option = $(this).data('option');
				// 		settings[option]= $(this).data(option);					
				// 		initPopover();
				// });	

				// $('.btn-reset').on('click',function(e){
				// 	e.preventDefault();
				// 	location.reload();
				// });	

				function updateSettings(){
					settings.style=$('.option-style.active').data('option');
					settings.trigger=$('.option-trigger.active').data('option');
					settings.closeable=$('.option-closeable').hasClass('active');
					settings.multi = $('.option-mulit').hasClass('active');
					settings.arrow = $('.option-arrow').hasClass('active');
					initPopover();
				}		


				function initPopover(){		
					//	
					var listContentpostop = $('#listContent_postop').html(),
						listSettingspostop = {content:listContentpostop,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_postop').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingspostop));
				
			//
			
					var listContentpr = $('#listContent_pr').html(),
						listSettingspr = {content:listContentpr,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_pr').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingspr));
				
				//
				
					var listContentsd = $('#listContentsd').html(),
						listSettingssd = {content:listContentsd,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_sd').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingssd));
					
					
					var listContentss = $('#listContentss').html(),
						listSettingsss = {content:listContentss,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_ss').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingsss));
					//Procedure Notes
					
					var listContentdoo = $('#listContentdoo').html(),
						listSettingsdoo = {content:listContentsd,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_doo').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingsdoo));
					
					
					
					//Procedure Notes
					
					var listContentexposure = $('#listContentexposure').html(),
						listSettingsexposure = {content:listContentexposure,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_exposure').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingsexposure));
					//Procedure Notes
					
					
					//Procedure Notes
					
					var listContentpower = $('#listContentpower').html(),
						listSettingspower = {content:listContentpower,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					$('.show-pop-list_power').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingspower));
					//Procedure Notes
					
					//Procedure Notes
					var listContentcount = $('#listContentcount').html(),
						listSettingscount = {content:listContentcount,
											title:'Please Select',
											padding:false,
											closeable:true
					};
					$('.show-pop-list_count').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingscount));
					//Procedure Notes
					//Procedure Notes
					var listContentshot = $('#listContentshot').html(),
						listSettingsshot = {content:listContentshot,
											title:'Please Select',
											padding:false,
											closeable:true
					};
					$('.show-pop-list_shot').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettingsshot));
					//Procedure Notes
					
					
					$('a.show-pop').webuiPopover('destroy').webuiPopover(settings);				
					
					var tableContent = $('#tableContent').html(),
						tableSettings = {content:tableContent,
											width:500
										};
					$('a.show-pop-table').webuiPopover('destroy').webuiPopover($.extend({},settings,tableSettings));

					var listContent = $('#listContent').html(),
						listSettings = {content:listContent,
											title:'Please Select',
											padding:false,
											closeable:true
										};
										
										
										// Gaurav  
					$('.show-pop-list').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings));
					
					
					// Laser- COntinue 
					
					
					var listContent_l = $('#listContent_l').html(),
						listSettings_l = {content:listContent_l,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					
					$('.show-pop-list_l').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_l));
					// Gaurav  
					
					// LAser COntinue
					
					
					// Laser- COntinue 
					
					
					var listContent_cc = $('#listContent_cc').html(),
						listSettings_cc = {content:listContent_cc,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					
					$('.show-pop-list_cc').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_cc));
					// Gaurav  
					
					// LAser COntinue
					
					// Laser- COntinue 
					
					
					var listContent_hx = $('#listContent_hx').html(),
						listSettings_hx = {content:listContent_hx,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					
					$('.show-pop-list_hx').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_hx));
					// Gaurav  
					
					// LAser COntinue
					
					// Laser- COntinue 
					
					
					var listContent_phx = $('#listContent_phx').html(),
						listSettings_phx = {content:listContent_phx,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					
					$('.show-pop-list_phx').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_phx));
					// Gaurav  
					// LAser COntinue
				
					// Laser- COntinue 
					
					
					var listContent_oc = $('#listContent_oc').html(),
						listSettings_oc = {content:listContent_oc,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					
					$('.show-pop-list_oc').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_oc));
					// Gaurav  
					// LAser COntinue
					
					var listContent_sle = $('#listContent_sle').html(),
						listSettings_sle = {content:listContent_sle,
											title:'Please Select',
											padding:false,
											closeable:true
										};
					
					$('.show-pop-list_sle').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_sle));
					// Gaurav  
					// LAser COntinue
					
					var listContent_fundus = $('#listContent_fundus').html(),
						listSettings_fundus = {content:listContent_fundus,
											title:'Please Select',
											padding:false,
											closeable:true
										};
			
					$('.show-pop-list_fundus').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_fundus));
					// Gaurav  
					
					// LAser COntinue
					
					var listContent_me = $('#listContent_me').html(),
						listSettings_me = {content:listContent_me,
											title:'Please Select',
											padding:false,
											closeable:true
										};
			
					$('.show-pop-list_me').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_me));
					// Gaurav  
    				// LAser COntinue
					
					var listContent_preop = $('#listContent_preop').html(),
						listSettings_preop = {content:listContent_preop,
											title:'Please Select',
											padding:false,
											closeable:true
										};
			
					$('.show-pop-list_preop').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings_preop));
					// Gaurav  
					
					
					var listContent1 = $('#listContent1').html(),
						listSettings1 = {content:listContent1,
											title:'Please Select',
											padding:false,
											closeable:true
										};
				//Gaurav
										
								
					$('.show-pop-trigger2').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings1));
					
					
					var listContent2 = $('#listContent3').html(),
						listSettings2 = {content:listContent2,
											title:'Please Select',
											padding:false,
											closeable:true
										};
										//Gaurav
										
								
					$('.show-pop-list3').webuiPopover('destroy').webuiPopover($.extend({},settings,listSettings2));
					

					var largeContent = $('#largeContent').html(),
						largeSettings = {content:largeContent,
											title:'Evaluation',
											width:400,
											height:350,
											delay:{show:300,hide:1000},
											closeable:true
										};
										
						//				
					var popLarge = $('.show-pop-large').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));


					$('a.show-pop-delay').webuiPopover('destroy').webuiPopover({trigger:'hover',width:300});
					$('a.show-pop-code-delay').webuiPopover('destroy').webuiPopover({
																						trigger:'hover',
																						width:300,
																						delay:{
																							show:0,
																							hide:1000
																						}
																					});
					 var
					 	iframeSettings = {	width:500,
					 						height:350,
					 						closeable:true,
					 						padding:false,
					 						type:'iframe',
					 						url:'http://getbootstrap.com'};					
					$('a.show-pop-iframe').webuiPopover('destroy').webuiPopover($.extend({},settings,iframeSettings));


					var
					 	asyncSettings = {	width:'auto',
					 						height:'200',
					 						closeable:true,
					 						padding:false,
					 						cache:false,
					 						url:'https://api.github.com/',
					 						type:'async',
					 						content:function(data){
					 							var html = '<ul class="list-group">';
					 							for(var key in data){
					 								html+='<li class="list-group-item"><a href="'+ data[key] +'" target="_blank">'
					 								+ '<i class="glyphicon glyphicon-leaf"></i> '+ key+'</a>'+'</li>';	
					 							}
												html+='</ul>';
												return html;
					 						}};
					$('a.show-pop-async').webuiPopover('destroy').webuiPopover($.extend({},settings,asyncSettings));

					$('a.show-pop-backdrop').webuiPopover('destroy').webuiPopover({content:'popover with backdrop!', backdrop:true});


					// var nocacheSettings = {
					// 		cache:true,
					// 		content:function(){
					// 			return $('#aa').text();
					// 		}
					// }

					
					// $('a.show-pop-uncache').webuiPopover('destroy').webuiPopover($.extend({},settings,nocacheSettings));

					$('#change').on('click',function(e){
						e.preventDefault();
						$('#aa').text('changed text');
					})


					$('a.show-pop-event').each(function(i,item){
							var ename = $(item).text()+'.webui.popover';
							$(item).webuiPopover('destroy').webuiPopover(settings)
							.on(ename,function(e){
								var log =  ename+' is trigged!';
								if (console){
									console.log(log);
								}
								$('#eventLogs').text($('#eventLogs').text()+'\n'+log);
							});
					});



					
					$('body').on('click','.pop-click',function(e){
						e.preventDefault();
						if (console){
							console.log('clicked');
						}
					});


					var inputSettints = {
						placement:'right',
						title:'',
						multi:false,
						width:220
					};
					

					$('.form-input').webuiPopover('destroy').webuiPopover($.extend({},settings,inputSettints));

					$('.form-input').on('focus',function(){
						$(this).webuiPopover('show');
					});

				}

				$('#resetLogs').on('click',function(e){
					e.preventDefault();
					$('#eventLogs').text('');
				});


				initPopover();

				
				
					
			})();
	 (function(){
				
				if($(window).width() < 767){
					
					   $('body .webui-popover.top').css({
						   	'left' : '10px'
						   
						   
						   });
					
					}
					
			 })
	
			
});
	
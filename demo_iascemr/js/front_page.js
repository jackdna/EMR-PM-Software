				$(document).on('click', '.panel-heading.haed_p_clickable', function(e){
					var $this = $(this);
					if(!$this.hasClass('panel-collapsed')) {
						$this.parents('.panel').find('.panel-body').slideUp();
						$this.addClass('panel-collapsed');
						$this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
					} else {
						$this.parents('.panel').find('.panel-body').slideDown();
						$this.removeClass('panel-collapsed');
						$this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
					}
				})	
				$(document).ready(function () {
					
				/*	$('.sign_link').click(function(){
						
						var Innner =	$(this).siblings('.sign_inner');
						if(Inner.hasClass('in')){
								$(this).hide();
							}
						else{
							$(Inner).click(funciton(){
								$('.sign_link').show();
							});
						}
					});	
					*/
					$('.inner_safety_wrap');
					$('.inner_safety_wrap a.li_check').click(function(){
						var assign_span =	$(this).children('span');
						var checked_class = "fa-check-square-o";
						var unchecked_class = "fa-square-o"	;
						var th_name = $(this).attr('data-name');
						if($(assign_span).hasClass(checked_class) ){
							$(assign_span).addClass(unchecked_class);
							$(assign_span).removeClass(checked_class);
							
							alert($(th_name));
							
							
						}
					 	
						else {
							$(assign_span).removeClass(unchecked_class);
							$(assign_span).addClass(checked_class);
						}
					});
					
						
						
						
				});
				
			
		  		$(document).ready(function() {
					   $(document).on('click','#toggle_btn1',function(){	
								$('.toggled_1').toggleClass('toggle_AGAIN').first().stop().delay('slow');
						});
					   $(document).on('click','#toggle_btn3',function(){	
							$(this).hide('fast');
							$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
						});	
						$(document).on('click','#toggle_btn2',function(){	
							$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
							$('#toggle_btn3').show('fast');
						});	
				
				/*	
			    */
					$('#patient_form_m').click(function(){
								$('#patient_form').modal({show:true,backdrop:true});
								$('#pt_ocular_sx_hx').modal('hide');		
					});
					$('#pt_ocular_sx_hx_m').click(function(){
								$('#pt_ocular_sx_hx').modal({show:true,backdrop:true});	
								$('#patient_form').modal('hide');
									
					});
					if( typeof tooltip == 'function')
						$('[data-toggle="tooltip"]').tooltip()
					$('.search_button').click(function(){
							$('.search_wrap').collapse('toggle');
							$('.date_wrap').collapse('toggle');
						});
				    $(".dropdown").click(
						function() {
						
					    });
						
						$(".dropdown-menu #a_allactivecancelled").click(function(){
							$("#allactivecancelled").hide();	
						});
						
						$(".dropdown-menu #a_allactivecancelled").click(function(){
							$("#allactivecancelled").hide();	
						});
						
					//	$(".dropdown-menu .radioFilter").prop('checked','true',function(){
//							$("#allactivecancelled").hide();	
//						});						
						
					  $('.clickable').click(function(){
						  $(this).closest('tr').next('tr').toggle('fade');
					   }); 
					  if( typeof selectpicker == 'function') 
					  	$(".selectpicker").selectpicker();	
				  });
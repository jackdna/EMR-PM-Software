<script>
			
			function do_sess_alive(){//to keep session alive till next cycle.
				clearInterval(top.log_sec_timer);
				top.sessionAlive();
				top.auto_sess_timer = setTimeout(function(){
					clearTimeout(top.auto_sess_timer);
					clearTimeout(top.ajax_sess_timer)
					top.sessionCheck(top.auto_sess_timeout,top.auto_sess_timeout_warning_time,'callBack','auto_sess_timeout_span');
				},top.auto_sess_timeout_warning_time);
				top.closeDialog();
			}
			
			
			function fun_sec_timer(){
				var sec_start = parseInt(top.document.getElementById("logout_seconds").innerHTML);
				var sec_time = sec_start - 1;
				top.document.getElementById("logout_seconds").innerHTML = sec_time;
				if(sec_time<1)
				{
						clearInterval(top.log_sec_timer); 
						top.goLogout();
				}
			}
			
			/*--AUTO SESSION TIMEOUT SCRIPT END--*/
			
			var sessionCheck = function(timeOutIn,recallIn,return_func,showin) {
				
				top.$.xhrPool.abortAll = function() {
						$(this).each(function(i, jqXHR) {   //  cycle through list of recorded connection
							//alert(i + ': ' + jqXHR);
							jqXHR.abort();  //  aborts connection
							top.$.xhrPool.splice(i, 1); //  removes from list by index
						});
						
				};
				/*$.each(top.$.xhrPool, function(jqXHR) { 
							jqXHR.abort(); 
				});*/
				
				var xhr = $.ajax({
									url : './common/sessionTimeout.php',
									type:'POST',
									data : {'do':1, 'timeout_in':timeOutIn, 'recall_in':recallIn},
									beforeSend: function(jqXHR) {
										top.$.xhrPool.push(jqXHR);
									},
									complete: function(jqXHR)
									{
										//jqXHR.abort();
									},
									success:function(data)
									{
										eval(return_func+'(data,showin)');
									}
				});
				
				
			}
			
			
			var sessionAlive= function() {
				
				var xhr = $.ajax({
								url : './common/sessionTimeout.php',
								type:'POST',
								data : {'do':2},
								beforeSend: function(jqXHR) {
										top.$.xhrPool.push(jqXHR);
									},
								complete: function(jqXHR)
									{
										//jqXHR.abort();
									},	
								success:function(data)
								{ // Do Nothing
								}
						});
			
			}
			
			
			function callBack(content,showin)
			{
				if(content)
				{ 
					if(content.indexOf("{js}")!=-1)
					{
						var responseARR=content.split("{js}") ;
						content=responseARR[0] ;
						eval(responseARR[1]) ;
					} 
					if(typeof(showin)!="undefined")
						top.document.getElementById(showin).innerHTML=content;
				}
			
			}
			
						
			function showDialog(HTitle,BodyText,btnYesTitle, btnYesHref,btnNoTitle, btnNoHref,BoxID)
			{
				var html	=	'';
				
				html	+=	'<div class="panel panel-default bg_panel_sum dialogBox" >';
				
				html	+=	'<div class=" haed_p_clickable dialogHead " >';
				html	+=	'<h3 class="panel-title rob" >' + HTitle + '</h3>';
				html	+=	'</div>'; // panel-bod
				
				html	+=	'<div class=" text-center dialogBody"  >';
				html	+=	BodyText;
				html	+=	'<a class="btn btn-info"   href="' + btnYesHref + '">' + btnYesTitle + ' </a>&nbsp;';
				html	+=	'<a class="btn btn-danger" href="' + btnNoHref + '">' + btnNoTitle + ' </a>';
				html	+=	'</div>';
				
				html	+=	'</div>';
				
				top.document.getElementById(BoxID).innerHTML = html;	
				top.document.getElementById(BoxID).style.display = 'block';
				
				//window.document.focus();
				top.window.focus();
				
			}
			
			function closeDialog()
			{
				var BoxID	=	'dialogBoxScreen';
				top.document.getElementById(BoxID).innerHTML = '';
				top.document.getElementById(BoxID).style.display = 'none';
			}
			
			function goLogout()
			{
					$.each(top.windowObject, function(index,obj) { obj.close(); });
					window.location = "logout.php";
			}
			
			
</script>
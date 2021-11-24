<script>
			/*top.$.xhrPool.abortAll = function() {
						$.each(this, function(jqXHR) { 
							jqXHR.abort(); 
						});
			};*/
			top.$.xhrPool.abortAll = function() {
						$(this).each(function(i, jqXHR) {   //  cycle through list of recorded connection
							jqXHR.abort();  //  aborts connection
							top.$.xhrPool.splice(i, 1); //  removes from list by index
						});
						
				};
			
			clearTimeout(top.auto_sess_timer);
			clearInterval(top.log_sec_timer);	
			clearTimeout(top.ajax_sess_timer);
			
			top.auto_sess_timeout 	=	<?=$_SESSION['loginUserSessionTimeout']?>;
				
			if(top.auto_sess_timeout)
			{
				top.auto_sess_timeout_warning_time = top.auto_sess_timeout > 60 ? top.auto_sess_timeout - 60 : top.auto_sess_timeout - 30;
				top.auto_sess_timeout_warning_time = top.auto_sess_timeout_warning_time*1000; 	//in mili-seconds for setInterval
				top.auto_sess_timer = setTimeout(function(){
					clearTimeout(top.auto_sess_timer);
					top.sessionCheck(top.auto_sess_timeout,top.auto_sess_timeout_warning_time,'callBack','auto_sess_timeout_span');
				}, top.auto_sess_timeout_warning_time);
				
			}//end of if(sess_timeout)
			
			
</script>
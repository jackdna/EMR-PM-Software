<?php
require_once('../../admin_header.php');
require_once('../../../../library/classes/admin/scheduler_admin_func.php');  
?> 
		<script type="text/javascript">
			function keyCatcher() 
			{
				var e = event.srcElement.tagName;
				//var f= event.srcElement.tagType;
				//alert(f);
				//
				if (event.keyCode == 8 && e != "INPUT" && e != "TEXTAREA") 
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
			}
			document.onkeydown = keyCatcher;
					
			$(document).ready(function()
			{
				//jQuery('#loading_img').css({'display':'block'});
				jQuery.ajax({
					url:'load.php',
					type:'POST',
					complete:function(respData)
					{
						resultData=respData.responseText;						
						$('#divLoadAvailable').html(resultData);	
						//jQuery('#loading_img').css({'display':'none'});
						var ar = [["save_labels_status","Save Selected Labels","top.fmain.save_labels();"]];
						top.btn_show("ADMN",ar);						
					}
				});
			});		
			
			function save_labels()
			{
				jQuery('#loading_img').css({'display':'block'});
				label_str='';
				$('input[type="checkbox"]').each(function(index)
				{
					if($(this).is(':checked')){
						es_label_val = encodeURIComponent($(this).val());
						label_str+='-*-'+es_label_val;
					}	
				});

				$.ajax({
					url:'create_labels_xml.php',
					type:'POST',
					data:'labels='+label_str,
					success:function(respData){
						jQuery('#loading_img').css({'display':'none'});						
						top.alert_notification_show("Labels for available have been saved successfully");
					}
				});
			}
			set_header_title('Available');
		</script>
		<div class="whtbox">
		<table class="table table-striped table-bordered table-condensed adminnw">
			<thead>
				<tr>           
					<th class="col-sm-4">Label</th>
					<th class="col-sm-1">Label Type</th>
					<th class="col-sm-1">Enable Label</th>
					<th class="col-sm-4">Label</th>
					<th class="col-sm-1">Label Type</th>
					<th class="col-sm-1">Enable Label</th>
				</tr>
			</thead>
			<tbody id="divLoadAvailable"></tbody>
		</table>
		</div>
<?php
	include('../../admin_footer.php');	
?>
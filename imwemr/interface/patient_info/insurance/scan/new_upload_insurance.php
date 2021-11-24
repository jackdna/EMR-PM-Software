<script>	
$(function(){

	$("#file_icon").on('change',function(){
		var values = $(this)[0].files.length;
		var maxFiles = uplLimit;
		var uplStr = (maxFiles>1)?"files":"file";
		var msg = '';
		var color = '';
		if(values>maxFiles) {
			msg = "You can upload maximum "+maxFiles+" "+uplStr;
			$(this)[0].value=""; color='red';
		}
		else
		{
			msg = values+ ' file(s) selected';
			color = 'green'
		}
		$("#file_label").html(msg).css({'color':color});;
	});
	
	$("#browse_btn").on('click',function(){ $("#file_icon").trigger('click');  });
});
</script>

<div class="clearfix"></div>
<div class="row">
	<div class="col-xs-1"></div>
  <div class="col-xs-10">
  	<div class="clearfix">&nbsp;</div>
  	<form name="frm_upload" action="<?php echo $GLOBALS['php_server']."/interface/patient_info/insurance/scan/upload_scan_insurance.php?imwemr=".session_id()."&method=upload&type=".$type."&call_from=".$_REQUEST['call_from']."&isRecordExists=".$isRecordExists;?>" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="actType" value="upload" />
      <input type="hidden" name="frm_new_upload" id="frm_new_upload" value="1" >
     	 		
          <div class="row <?php echo ($uplLimit == '0' ? 'hidden' : '');?>" id="up_control">
          	<div class="input-group">
            	<input type="file" name="file[]" multiple id="file_icon" style="visibility:hidden; position:absolute;">
            	<label id="file_label" class="form-control" disabled></label>
              <span class="input-group-addon btn btn-primary btn-lg" id="browse_btn">
            		<i class="glyphicon glyphicon-folder-open"></i>&nbsp;Browse... 
             	</span>
         		</div>	   
         	</div>
          
          <div class="row <?php echo ($uplLimit == '0' ? '' : 'hidden');?>" id="up_warning">
          	<div class="alert alert-danger">Please <a onClick="javascript:show_prev_tab();">delete</a> one document, to upload a new one</div>
         	</div>   
				
				      
      <div class="clearfix">&nbsp;</div>	
     	<div class="row"> 
      	<label>Comment:&nbsp;</label><br>
        <textarea name="comments" class="form-control" rows="2"><?php echo $rowQry['cardscan_comments'];?></textarea>
        <div class="clearfix"></div>
        <?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00') && ($rowQry['crtDate'] != '')){?>
        	<span class="text-center"><h5>Last Scan Date Time -:&nbsp;<?php echo $rowQry['crtDate']; ?></h5></span>
       	<?php }?>
      </div>			
 		</form>	     
 	</div>
  <div class="col-xs-1"></div>
  
</div>
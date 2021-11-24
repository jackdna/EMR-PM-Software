<?php	

if($_POST['frm_new_scan'] == 1 ){
	$comment12 = imw_real_escape_string($_POST['comments']);
	if($isRecordExists)
	{
		$qry = "update insurance_data set cardscan_operator = '".$userauthorized."', cardscan_comments = '".$comment12."' where pid = '".$pid."' and type = '".$type."' and ins_caseid ='".$ins_caseid."'";
		$res = imw_query($qry);
		if($res){
			$imageName = "";
			$scan_card_new_name1 = "scan_card_new_".$_SESSION["scan_card_type"];
			$imageName = $_SESSION[$scan_card_new_name1];						
			$_SESSION[$scan_card_new_name1]=NULL;
			$_SESSION[$scan_card_new_name1]="";												
			unset($_SESSION[$scan_card_new_name1]);
	
			$_SESSION["scan_card_type"]=NULL;
			$_SESSION["scan_card_type"]="";												
			unset($_SESSION["scan_card_type"]);
			echo "<script>close_window('".$imageName."');</script>";	
		}
	}
	else
	{
		$qry = "update insurance_scan_documents set cardscan_operator='".$userauthorized."', cardscan_comments = '".$comment12."' where patient_id = '".$pid."' and type = '".$type."' and ins_caseid = '".$ins_caseid."'";
		$res = imw_query($qry);
		if($res){
			$imageName = "";
			$scan_card_new_name1 = "scan_card_new_".$_SESSION["scan_card_type"];
			$imageName = $_SESSION[$scan_card_new_name1];						
			$_SESSION[$scan_card_new_name1]=NULL;
			$_SESSION[$scan_card_new_name1]="";												
			unset($_SESSION[$scan_card_new_name1]);
			$_SESSION["scan_card_type"]=NULL;
			$_SESSION["scan_card_type"]="";												
			unset($_SESSION["scan_card_type"]);
			echo "<script>close_window('".$imageName."');</script>";	
		}
	}
	sleep(1);die();
}

?>

<div class="clearfix"></div>

<div class="row">
	<div class="col-xs-1"></div>
  <div class="col-xs-10">
  	<div class="clearfix">&nbsp;</div>
    <div class="row">
			
      <div class="col-xs-12 <?php echo ($uplLimit == '0' ? 'hidden' : '');?>" id="scan_control">
      	<?php
				
					if($browser['name'] == 'msie' || $browser['name'] != "chrome" )
					{ 
						if($uplLimit=='0') { echo '<script>var autoScan="no";</script>'; }
						include_once $GLOBALS['fileroot']. "/library/scan/scan_control.php";
					}
					else {
						if($uplLimit=='0') { echo '<script>var autoScan="no";</script>'; }
						include_once $GLOBALS['fileroot']. "/library/scanc/scan_control.php";
					}
				?>
     	</div>   	
			
      <div class="col-xs-12 <?php echo ($uplLimit == '0' ? '' : 'hidden');?>" id="scan_warning">
      	<div class="alert alert-danger">Please <a style="cursor:pointer;" onClick="javascript:show_prev_tab();">delete</a> one document, to upload a new one</div>
      </div>
      
		</div>
		
    <div class="clearfix"></div>
    
  	<div class="row">
    	<form name="frm_scn" action="" method="post">
      	<input type="hidden" name="frm_new_scan" id="frm_new_scan" value="1" >
      	<label>Comment:&nbsp;</label><br>
        <textarea name="comments" class="form-control" rows="2"><?php echo $rowQry['cardscan_comments'];?></textarea>
        <div class="clearfix"></div>
        <?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00') && ($rowQry['crtDate'] != '')){?>
        	<span class="text-center"><h5>Last Scan Date Time -:&nbsp;<?php echo $rowQry['crtDate']; ?></h5></span>
       	<?php }?>
     	</form>
   	</div>    
 	</div>
  <div class="col-xs-1"></div>
  
</div>
<?php	
	
	//Check IP
	if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
	{
		$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
	}			
	
	if( $_POST['frm_new_scan'] == 1 )
	{
		$comment = addslashes($_POST['comments']);
		$qry = "Select ".$idFld." from ".$tblName." Where ".$idFld."='$pid'";
		$sql = imw_query($qry);
		$cnt = imw_num_rows($sql);

		$qry = "update ".$tblName." set licenseOperator ='".$userauthorized."',licenseComments = '".$comment."' where ".$idFld."='$pid'";
		$res = imw_query($qry);
		
		$imageName = "";
		$imageName = $_SESSION[$type.'scan_image_new'];
		$_SESSION[$type.'scan_image_new']=NULL;
		$_SESSION[$type.'scan_image_new']="";
		unset($_SESSION[$type.'scan_image_new']);
		
		if( !$cnt ) {
			$_SESSION[$type.'scan_license_comment'] = $_POST['comments'];
			$_SESSION[$type.'scan_license_opr'] = $userauthorized;
		}

		echo "<script>close_window('".$imageName."','".$type."');</script>";	
		sleep(1);die();
	}
	
	$selQry = "select DATE_FORMAT(licenseDate,'%m-%d-%Y %h:%i:%s') AS crtDate1,licenseComments from ".$tblName." where ".$idFld." = '".$pid."'";
	$resQry = imw_query($selQry);
	$rowQry = imw_fetch_array($resQry);
?>
<div class="clearfix"></div>

<div class="row">
	<div class="col-xs-1"></div>
  <div class="col-xs-10">
  	
    <div class="row">
	<div class="col-xs-12">
  	<?php
			
			if($browser['name'] == 'msie' || $browser['name'] != "chrome" )
			{ 
				include_once $GLOBALS['fileroot']. "/library/scan/scan_control.php";
			}
			else {
				include_once $GLOBALS['fileroot']. "/library/scanc/scan_control.php";
			}
			?>
    
  </div>
</div>
		
    <div class="clearfix"></div>
    
  	<div class="row">
    	<form name="frm_new_scan_licence" action="" method="post">
      	<input type="hidden" name="frm_new_scan" id="frm_new_scan" value="1" >
		  <input type="hidden" name="type" id="type" value="<?php echo $type; ?>" >  
      	<label>Comment:&nbsp;</label><br>
        <textarea name="comments" class="form-control" rows="2"><?php echo stripslashes($rowQry['licenseComments']);?></textarea>
        <div class="clearfix"></div>
        <?php if(($rowQry['crtDate1'] != '00-00-0000 12:00:00') && ($rowQry['crtDate1'] != '')){?>
        	<span class="text-center"><h4>Last Scan Date Time-:&nbsp;<?php echo $rowQry['crtDate1']; ?></h4></span>
       	<?php }?>
     	</form>
   	</div>    
 	</div>
  <div class="col-xs-1"></div>
  
</div>
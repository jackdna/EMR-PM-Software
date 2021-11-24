<?php

$web_path = $GLOBALS['webroot'].'/data/'.constant('PRACTICE_PATH');

if($_POST["frm_del_prev_license"] == 1)
{			
	$imgpathr_f = $_POST["patientLicImagePath"];
	$real_path = substr(data_path(),0,-1).$imgpathr_f;
	if(trim($imgpathr_f)!="") {
		if(file_exists($real_path)) {
			$vquery2 = "update ".$tblName." set ".$licImgFld." ='' where ".$idFld." = ".$pid;															
			$vsql2 = imw_query($vquery2);
			if($vsql2){ echo $msg = "Licence Scan Deleted Sucessfully!"; unlink($real_path);}
			else echo "License file removed Error: while updating records. Please try again!!!";
		}
	}
}

$vquery = "select ".$licImgFld." as licence_photo, ".$idFld." as pid from ".$tblName." where ".$idFld." = ".$pid;
$vsql = imw_query($vquery);
if(imw_num_rows($vsql)){
	$rt = imw_fetch_assoc($vsql);
}
?>
<script type="text/javascript">
	function delImg(id,patientLicImagePath,type)
	{
		if( typeof type === 'undefined') type = '';
		var cMsg = 'Sure ! You Want To Delete '+(type ? 'Resp. Party' : 'Patient')+' License Image ?';
		if(confirm(cMsg)){
			document.getElementById("delId").value = id;
			document.getElementById("patientLicImagePath").value = patientLicImagePath;
			document.delFrmPatientLicImage.submit();
		}
	}
	
</script>
<form name="delFrmPatientLicImage" action="" method="post">
	<input type="hidden" name="frm_del_prev_license" id="frm_del_prev_license"  value="1">
	<input type="hidden" name="type" id="type"  value="<?php echo $type; ?>">
	<input type="hidden" name="delId" id="delId"  value="">
	<input type="hidden" name="patientLicImagePath" id="patientLicImagePath" value="">
	
	<div class="row mt20">
		<div class="col-xs-3">
			<?php
					
					$file_not_exists = true;
					if ($rt["licence_photo"] <> '' )
					{
						$imgpath1 .= substr(data_path(1),0,-1).$rt["licence_photo"];
						$real_path = substr(data_path(),0,-1).$rt["licence_photo"];
						$arrPatientLicImage = explode('/',$rt["licence_photo"]);
						$arrPatientImageLicName = explode('-',$arrPatientLicImage[2]);
						$patientImageLicName = $arrPatientImageLicName[0];
						if(file_exists($real_path))
						{
							$file_not_exists = false;
			?>
      			<div class="image-grid">
					<span><img src="<?php echo $imgpath1;?>" /></span>
					<a class="delete" title="Delete" href="javascript:delImg('<?php echo $rt["pid"]; ?>','<?php echo $rt["licence_photo"];?>','<?php echo $type;?>');" ><i class="glyphicon glyphicon-remove-circle"></i></a>
					<a class="layer" data-toggle="modal" data-target="#imageLicense"></a>
				</div>
            <?php
						}
					}
				
					if($file_not_exists)
					{
						echo 'No scaned documents !';	
					}
          	?>
		</div>
  	</div>
</form>
<div id="imageLicense" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Prev Scan License Image</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<?php
        	if($imgpath1)
					{ 
						echo '<img src="'.$imgpath1.'" />';
					}
				?>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
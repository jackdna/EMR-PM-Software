<?php
$web_path = $GLOBALS['php_server'].'/data/'.constant('PRACTICE_PATH');
$fileroot_path = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH');

if($_POST["frm_del_prev_insurance"] == 1 )
{
	if($isRecordExists)
		$qry = "select ".$scan_card_val." from insurance_data where id = '".$delId."' ";
	else
		$qry = "select $scan_card_val from insurance_scan_documents where scan_documents_id = ".$delId;		
	
	$sql = imw_query($qry);
	$row = imw_fetch_assoc($sql);
	
	$scan_label = ($scan_card_val == 'scan_card') ? 'scan_label': 'scan_label2';
	
	if($isRecordExists)
		$qry = "update insurance_data set $scan_card_val = '', $scan_label = '' where id = ".$delId;
	else
		$qry = "update insurance_scan_documents set $scan_card_val = '', $scan_label = '' where scan_documents_id = ".$delId;
		
	imw_query($qry);		
	
	chmod(data_path().$row[$scan_card_val],0777);
	@unlink(data_path().$row[$scan_card_val]);
	
	echo '<script>refresh_limits(1);</script>';	

}

$ins_caseid = $cur_case_id ? $cur_case_id : $_SESSION['currentCaseid'];


if($isRecordExists){
	$data_query = "select * from insurance_data where id = '".$isRecordExists."' ";
}
else{
	$data_query = "select *, scan_documents_id as id from insurance_scan_documents where type = '$type' And ins_caseid = '".$ins_caseid."' And patient_id = '".$pid."' And document_status = '0'";
}

$data_sql = imw_query($data_query);
$data_row = imw_fetch_assoc($data_sql);

?>
<script>
	function delImg(id,scan_card){
		if(confirm('Sure ! You Want To Delete Scan Document')){
			document.getElementById("delId").value = id;
			document.getElementById("scan_card_val").value = scan_card;
			document.delFrm.submit();
		}
	}
</script>
<form name="delFrm" action="" method="post">
	<input type="hidden" name="frm_del_prev_insurance" id="frm_del_prev_insurance" value="1">
	<input type="hidden" name="delId" id="delId" value="">
	<input type="hidden" name="scan_card_val" id="scan_card_val" value="">
</form>

<div class="row mt20">
  	<div class="col-xs-1"></div>
    
    <div class="col-xs-3">
    	<div class="thumbnail text-center pd0">
      	<span class="col-xs-12 bg bg-info pd5">Scan Card 1</span>
     		<span class="clearfix"></span>
        <div class="image-grid">
					<?php
            
            $cardscan_date = $data_row['created_date'] ? get_date_format($data_row['created_date']) : '';
            $cardscan_label = $data_row['scan_label'] ? $data_row['scan_label'] : '';
            $file_not_exists = true;
            
            if($data_row["scan_card"] <> '' )
            {
			$filepathImgSrc1 = $fileroot_path.$data_row["scan_card"];
              $scan_card_img = $web_path.$data_row["scan_card"];
							$real_path = data_path().$data_row["scan_card"];
              $scan_card_db_img = $scan_card_img;
              if(file_exists($real_path))
              {
                $scan_card_img_size = getimagesize($scan_card_img);
                $is_pdf_file = (strpos($scan_card_img, ".pdf") <>  false) ? true : false;			
                $file_not_exists = false;
                
                $scan_card_img = ($is_pdf_file) ? $GLOBALS['webroot'].'/library/images/pdfimg.png' : $scan_card_img;
                $onclick = ($is_pdf_file) ? 'onClick="window.open(\''.$scan_card_db_img.'\',\'\',\'width=800, height=700\')"' : 'data-toggle="modal" data-target="#scan_card1"';
                
          ?>				
							
                <span><img class="img-thumbnail" src="<?php echo $scan_card_img;?>" /></span>
                <a class="delete" title="Delete" href="javascript:delImg('<?php echo $data_row["id"]; ?>','scan_card');" >
                  <i class="glyphicon glyphicon-remove-circle"></i>
                </a>
                <a class="layer" <?php echo $onclick;?>></a>
							
				
					<?php
              }
            }
						
						if($file_not_exists)
						{
							echo '<span>No scanned documents !</span>';	
						}
									
					?>
  			</div>
        <?php if($cardscan_date) { ?> <span><label><b>Created date:</b><?php echo $cardscan_date;?></label></span><br><?php } ?>
        <?php if($cardscan_label) { ?> <span><label><?php echo $cardscan_label;?></label></span> <?php } ?>
        
      </div>
      
    </div>
    
    <div class="col-xs-3">
    	<div class="thumbnail text-center pd0">
      	<span class="col-xs-12 bg bg-info pd5">Scan Card 2</span>
     		<span class="clearfix"></span>
        <div class="image-grid">
					<?php
            
            $cardscan_date = $data_row['created_date'] ? get_date_format($data_row['created_date']) : '';
            $cardscan_label = $data_row['scan_label2'] ? $data_row['scan_label2'] : '';
            $file_not_exists = true;
           
            if($data_row["scan_card2"] <> '' )
            {
				$filepathImgSrc2 = $fileroot_path.$data_row["scan_card2"];
							$scan_card_img2 = $web_path.$data_row["scan_card2"];
              $real_path = data_path().$data_row["scan_card2"];
              $scan_card_db_img2 = $scan_card_img2;
							
              if(file_exists($real_path))
              {
                $scan_card_img_size = getimagesize($scan_card_img2);
                $is_pdf_file2 = (strpos($scan_card_img2, ".pdf") <>  false) ? true : false;			
                $file_not_exists = false;
                
                $scan_card_img2 = ($is_pdf_file2) ? $GLOBALS['webroot'].'/library/images/pdfimg.png' : $scan_card_img2;
                $onclick = ($is_pdf_file2) ? 'onClick="window.open(\''.$scan_card_db_img2.'\',\'\',\'width=800, height=700\')"' : 'data-toggle="modal" data-target="#scan_card2"';
                
          ?>				
								
                <span><img class="img-thumbnail" src="<?php echo $scan_card_img2;?>" /></span>
                <a class="delete" title="Delete" href="javascript:delImg('<?php echo $data_row["id"]; ?>','scan_card2');" >
                  <i class="glyphicon glyphicon-remove-circle"></i>
                </a>
                <a class="layer" <?php echo $onclick;?>></a>
							
				
					<?php
              }
            }
						
						if($file_not_exists)
						{
							echo '<span>No scanned documents !</span>';	
						}
									
					?>
  			</div>
        <?php if($cardscan_date) { ?> <span><label><bl>Created date:</b><?php echo $cardscan_date;?></label></span><br><?php } ?>
        <?php if($cardscan_label) { ?> <span><label><?php echo $cardscan_label;?></label></span> <?php } ?>
        
      </div>
      
    </div>
</div>
<div id="scan_card1" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Scan Card 1</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<?php
			$printButton = '';
        	if( $scan_card_img && !$is_pdf_file)
			{ 
				echo '<img src="'.$scan_card_img.'" />';
				$file_location1 = write_html('<table align="center" style="width:700px;text-align:left;"><tr><td style="width:700px;text-align:center"><img src="'.$filepathImgSrc1.'" style="width:100%;"/></td></tr></table>','insCard1.html');
				$printButton = '<button type="button" class="btn btn-success" data-location="'.$file_location1.'" onClick="printCard(this);">Print</button>';
			}
		?>
      </div>
      
      <div class="modal-footer">
		<?php echo $printButton;unset($printButton); ?>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<div id="scan_card2" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Scan Card 2</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<?php
			$printButton = '';
        	if( $scan_card_img2 && !$is_pdf_file2 )
			{ 
				echo '<img src="'.$scan_card_img2.'" />';
				$file_location2 = write_html('<table align="center" style="width:700px;text-align:left;"><tr><td style="width:700px;text-align:center;"><img src="'.$filepathImgSrc2.'" style="width:100%;"/></td></tr></table>','insCard2.html');
				$printButton = '<button type="button" class="btn btn-success" data-location="'.$file_location2.'" onClick="printCard(this);">Print</button>';
			}
		?>
      </div>
      
      <div class="modal-footer">
		<?php echo $printButton;unset($printButton); ?>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>    
    
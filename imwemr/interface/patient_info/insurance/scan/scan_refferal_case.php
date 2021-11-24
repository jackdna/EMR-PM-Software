<?php 
	include '../../../../config/globals.php';
	$browser = browser();
	//Check IP
	if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
	{
		$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
	}
	
	$userauthorized = $_SESSION['authId'];
	$pid = $_SESSION['patient'];
	$pid = (int) $pid;
	
	if(empty($del_id) === false)
	{
		$query = "update upload_lab_rad_data set upload_status = '1' where upload_lab_rad_data_id in (".$del_id.")";
		imw_query($query);
	}

	//--- GET ALL USERS NAME ---
	$userNameArr = array();
	$query = "select id,substring(lname,1,1) as lname, substring(fname,1,1) as fname from users";
	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	if($cnt > 0 )
	{
		$row = imw_fetch_assoc($sql);
		
		$name  = ucfirst($row['fname']);
		$name .= ucfirst($row['lname']);
		$id = $$row['id'];
		$userNameArr[$id] = $name;
	}
	
	//--- GET ALL IMAGES WITH DETAILS ----
	if($scan_id != '' && $scan_id != '0')
	{	
		$query = "select upload_lab_rad_data_id ,upload_file_name ,upload_by, 
									date_format(upload_date,'".get_sql_date_format()."') as upload_date from upload_lab_rad_data
									where uplaod_primary_id = '".$scan_id."' and scan_from = '".$scan_for."' and upload_status = '0'";
	}
	else
	{
		$query = "select upload_lab_rad_data_id ,upload_file_name ,upload_by,
									date_format(upload_date,'%m-%d-%Y') as upload_date from upload_lab_rad_data
									where uplaod_primary_id = '0' 
											AND ins_type = '".$ins_type."'
											AND ins_data_id = '".$_REQUEST['ins_data_id']."'
											AND patient_id = '".$_SESSION['patient']."' 
											AND upload_by = '".$_SESSION['authId']."'
											AND scan_from = '".$scan_for."' and upload_status = '0'";
	}
	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	$image_data = '';
	$i = 0; 
	while( $row = imw_fetch_object($sql) )
	{
		$upload_file_name = $row->upload_file_name;
		$upload_lab_rad_data_id = $row->upload_lab_rad_data_id;
		$upload_date = $row->upload_date;
		$upload_file_name = $row->upload_file_name;
		$upload_by = $row->upload_by;
		$user_name = $userNameArr[$upload_by];
		
		$arrPath = pathinfo($upload_file_name);
		$extension = $arrPath['extension'];
		
		$is_pdf_file = ($extension == "pdf") ? true : false;
		
		$pdf_image_path = '/library/images/pdfimg.png';
		
		$thumb_image_name = str_replace('upload_patient_scan','upload_patient_scan/thumbnail',$upload_file_name);
		$thumb_image_path = ($is_pdf_file) ? $GLOBALS['webroot'].$pdf_image_path : data_path(1).substr($thumb_image_name,1);
		$thumb_real_path  = ($is_pdf_file) ? $GLOBALS['fileroot'].$pdf_image_path : $GLOBALS['fileroot'].$thumb_image_path;
		
		$image_name = $upload_file_name;
		$image_path = data_path(1).substr($image_name,1);
		$real_path = data_path().substr($image_name,1);
		
		if( file_exists($real_path) )
		{
			$i++;
			$onclick = ($is_pdf_file) ? 'onClick="window.open(\''.$image_path.'\',\'\',\'width=800, height=700\')"' : 'onClick="set_image(\''.$image_path.'\')"';
			$image_data .= '<div class="col-xs-2">';
			$image_data .= '<div class="thumbnail text-center pd0">';
			$image_data .= '<div class="image-grid">';
			
			$thumb_image_path = file_exists($thumb_real_path) ? $thumb_image_path : $image_path;
			$image_data .= '<span><img class="img-thumbnail" src="'.$thumb_image_path.'" style="min-height:100px; max-height: 100px;" /></span>';
			
			$image_data .= '<div class="delete" title="Select to delete">';
			$image_data .= '<div class="checkbox checkbox-default">';
			$image_data .= '<input type="checkbox" id="delete_'.$upload_lab_rad_data_id.'" name="delete_scan_doc[]" value="'.$upload_lab_rad_data_id.'" />';
			$image_data .= '<label for="delete_'.$upload_lab_rad_data_id.'" title="Select to delete"></label>';
			$image_data .= '</div>';
			$image_data .= '</div>';
			
			$image_data .= '<a class="layer" '.$onclick.'></a>';
			
			$image_data .= '</div>';
			if( $user_name ) 
				$image_data .= '<span><label><b>By : </b>'.$user_name.'</label></span>';
			$image_data .= '<span><label><b>Date : </b>'.$upload_date.'</label></span>';
			
			$image_data .= '</div>';
			$image_data .= '</div>';
		}
		
		if( $i == 6 ) {	$image_data .= '<div class="clearfix mt5"></div>'; $i = 0; }
		
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Scan Document :: imwemr ::';?></title>
   	
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript">
			window.focus();
			
			var web_root = '<?php echo $GLOBALS['php_server'];?>';
			var browser_name = '<?php echo $browser['name'];?>';
			
			function scan_document(scan_or_upload)
			{
				var scan_id = '<?php echo $scan_id; ?>';
				var scan_for = '<?php echo $scan_for; ?>';
				var ins_data_id = '<?php echo $_REQUEST['ins_data_id']; ?>';
				var url = 'scan_and_upload_data.php?scanOrUpload='+scan_or_upload;
				url += '&scan_id='+scan_id+'&scan_for='+scan_for+'&ins_data_id='+ins_data_id;
				window.location.href = url;
			}
			
			function delete_doc(){
				var delete_scan_doc_op = document.getElementsByName("delete_scan_doc[]");
				var del_scan_doc = false;
				var del_id_arr = new Array();
				var j = 0;
				for(i=0;i<delete_scan_doc_op.length;i++){
					if(delete_scan_doc_op[i].checked == true){
						del_scan_doc = true;
						del_id_arr[j] = delete_scan_doc_op[i].value;
						j++;
					}
				}
				var del_id = del_id_arr.join(',');
				if(del_scan_doc == false){
					alert("Please select a document to delete.");
				}
				else{
					var con = confirm('Sure you want to delete document?');
					if(con == true){
						dgi("del_id").value = del_id;
						document.view_frm.submit();
					}
				}
			}
				
			function resize_window() 
			{ 
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				var parHeight = 670;//(browser_name == 'msie') ? 640 : 670;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
			}
			
			function set_image($_image)
			{
				if($_image)
				{
					var img = '<img src="'+$_image+'" />';
					$("#scan_data .modal-body").html(img);
					$("#scan_data").modal('show');
				}
			}
			
			$(function(){
				resize_window();
				
				$('body').on('mouseleave','.image-grid',function(){
					if( $(this).find(':checkbox').is(':checked') )
						$(this).find('.layer, .delete').show(10);
					else
						$(this).find('.layer, .delete').hide(10);
						
				}).on('mouseenter mouseover','.image-grid',function(){
					$(this).find('.layer, .delete').show(10);
				});
				
			});
			
		</script>
    <style type="text/css">
			.image-grid { height:auto;}
		</style>
		
</head>
<body>
		
    <div class="panel panel-primary">
      <div class="panel-heading">View Images</div>
      <div class="panel-body popup-panel-body">
      	
        <div class="clearfix">&nbsp;</div>
      	<form name="view_frm" action="" method="post">
  				<input type="hidden" name="scan_id" value="<?php echo $scan_id; ?>" >
          <input type="hidden" name="scan_for" value="<?php echo $scan_for; ?>" >
          <input type="hidden" name="ins_data_id" value="<?php echo $_REQUEST['ins_data_id']; ?>" >
    			<input type="hidden" name="del_id" id="del_id" value="">
          
          <div class="row">
          	<div class="col-xs-12">
          		<?php 
								if( $image_data )  echo $image_data;
								else
								{
									echo '<div class="alert alert-info">No Record Exists.</div>';
								}
							?>
           	</div>
          </div>
          
       	</form>	 
        
         	
     	</div>
      
  		<footer class="panel-footer">
      	<button type="button" name="scan_doc" class="btn btn-primary" id="scan_doc" onClick="scan_document('scan');">Scan</button>
        <button type="button" name="upload_doc" class="btn btn-success" id="upload_doc" onClick="scan_document('upload');">Upload</button>
        <button type="button" name="upload_doc" class="btn btn-warning" id="delete_btn" onClick="delete_doc();">Delete</button>
        <button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
      </footer>
      
    </div>
    
    <div id="scan_data" class="modal" role="dialog" >
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h4 class="modal-title" id="modal_title">Scan Data</h4>
          </div>
          
          <div class="modal-body" style="min-height:200px; max-height:450px; overflow:hidden; overflow-y:auto;">
            <div class="loader"></div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
          
        </div>
   	</div>
    
</div>


</body>

</html>

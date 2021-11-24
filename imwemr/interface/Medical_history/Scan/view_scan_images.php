<?php 
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

include_once('../../../config/globals.php');
require_once($GLOBALS['srcdir']."/classes/common_function.php");
extract($_REQUEST);

$browser = browser();
$mode = $_REQUEST['mode'];
$multi_pt_id = $_REQUEST['multi_pt_id'];

if (isset($_POST['ajax_req']) && $_POST['ajax_req'] == "ajax_scan_id") {
	$scan_id = $_POST['scan_id'];
	$batch_img_chk=0;
	$sel_img=imw_query("select upload_lab_rad_data_id from  upload_lab_rad_data where uplaod_primary_id='".$scan_id."' and upload_status='0'");
	$batch_img_chk=imw_num_rows($sel_img);
		if(imw_num_rows($sel_img)>0){
			$batch_img_chk=1;	
		}
		echo $batch_img_chk.'|~~|'.$scan_id;
	exit;	
}

if(!isset($upload_from) || empty($upload_from) === true){ $upload_from = $_REQUEST['upload_from']; }
//---- CHANGE SCAN DOCUMENT STATUS AS DELETED ---
if(empty($del_id) === false){
	$del_qry = imw_query("update upload_lab_rad_data set upload_status = '1' where upload_lab_rad_data_id in ($del_id)");
}

$upload_from = xss_rem($upload_from, 1);
$lab_id = xss_rem($lab_id, 1);

//--- GET IMAGES -----
if($upload_from != '' and $lab_id != ''){
	$lab_id=preg_replace('/[^0-9]+/','',$lab_id);
	//--- GET ALL USERS NAME ---
	$get_username_qry = imw_query("select id,substring(lname,1,1) as lname,substring(fname,1,1) as fname from users");
	if(imw_num_rows($get_username_qry) > 0){
		$username_row = imw_fetch_assoc($get_username_qry);
		$name  = ucfirst($username_row['fname']);
		$name .= ucfirst($username_row['lname']);
		$id = $username_row['id'];
		$userNameArr[$id] = $name;
	}
	
	//--- GET ALL IMAGES WITH DETAILS ----
	if($mode=='view' && $multi_pt_id!='' && $upload_from=='admin_documents') {
		$qry_strng = "select upload_lab_rad_data_id ,upload_file_name ,upload_by,
					date_format(upload_date,'".get_sql_date_format()."') as upload_date,upload_file_type from upload_lab_rad_data
					where uplaod_primary_id = '$lab_id' and scan_from = '$upload_from' AND givenToEduMultiPtId LIKE \"%\'".$multi_pt_id."\'%\"";
	}else {
		$qry_strng = "select upload_lab_rad_data_id ,upload_file_name ,upload_by,
					date_format(upload_date,'".get_sql_date_format()."') as upload_date,upload_file_type from upload_lab_rad_data
					where uplaod_primary_id = '$lab_id' and scan_from = '$upload_from' and upload_status = '0'";
	}
	
	$imgQryRes = imw_query($qry_strng);
	if(imw_num_rows($imgQryRes) > 0){
		while($row = imw_fetch_object($imgQryRes)){
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
			
			$thumb_image_name = str_replace('upload_radiology','upload_radiology/thumbnail',$upload_file_name);
			$thumb_image_path = ($is_pdf_file) ? $GLOBALS['webroot'].$pdf_image_path : data_path(1).substr($thumb_image_name,1);
			$thumb_real_path  = ($is_pdf_file) ? $GLOBALS['fileroot'].$pdf_image_path : $GLOBALS['fileroot'].$thumb_image_path;
			
			$image_name = $upload_file_name;
			$image_path = data_path(1).substr($image_name,1);
			$real_path = data_path().substr($image_name,1);
			if( file_exists($real_path) ){
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
	}
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
		<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css" rel="stylesheet" />
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
    <script type="text/javascript">
			window.focus();
			
			var web_root = '<?php echo $GLOBALS['php_server'];?>';
			var browser_name = '<?php echo $browser['name'];?>';
			
			function scan_document(upload){
				window.location.href='scan_and_upload_data.php?scanOrUpload='+upload+'&lab_id=<?php print $lab_id; ?>&upload_from=<?php print $upload_from; ?>';
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
					fAlert("Please select a document to delete.");
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
				
				
				<input type="hidden" name="scan_id" id="scan_id" value="<?php print $scan_id; ?>" >
				<input type="hidden" name="scan_for" id="scan_for" value="<?php print $scan_for; ?>" >
				<input type="hidden" name="mode" id="mode" value="<?php print $mode; ?>" >
				<input type="hidden" name="row_id" id="row_id" value="<?php echo $_REQUEST["lab_id"]; ?>" >
				<input type="hidden" name="multi_pt_id" id="multi_pt_id" value="<?php print $multi_pt_id; ?>" >
				<input type="hidden" name="del_id" id="del_id" value="">

				<div class="row">
					<div class="col-xs-12">
					<?php 
					if( $image_data )  echo $image_data;
					else{
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

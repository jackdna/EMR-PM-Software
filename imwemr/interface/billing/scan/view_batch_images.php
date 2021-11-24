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
Purpose: Shows scans and uploads
Access Type: Include 
*/
include '../../../config/globals.php';
$browser = browser();
//Check IP
if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
{
	$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
}

$mode =$_REQUEST['mode'];
$multi_pt_id = $_REQUEST['multi_pt_id'];

if (isset($_POST['ajax_req']) && $_POST['ajax_req'] == "ajax_scan_id") 
{
	$scan_id = $_POST['scan_id'];
	$batch_img_chk=0;
	$sel_img = imw_query("select upload_lab_rad_data_id from  upload_lab_rad_data where uplaod_primary_id='".$scan_id."' and upload_status='0'");
	$batch_img_chk = imw_num_rows($sel_img);
	if(imw_num_rows($sel_img)>0){
		$batch_img_chk=1;	
	}
	echo $batch_img_chk.'|~~|'.$scan_id;
	exit;	
}

if(!$upload_from) { $upload_from = $_REQUEST['upload_from']; }

//---- CHANGE SCAN DOCUMENT STATUS AS DELETED ---
if(empty($del_id) === false){
	$query= "update upload_lab_rad_data set upload_status = '1'
						where upload_lab_rad_data_id in (".$del_id.")";
	$sql = imw_query($query);
}

$upload_from = xss_rem($upload_from, 1);
$lab_id = xss_rem($lab_id, 1);

//--- GET IMAGES ------
if($upload_from != '' and $lab_id != '')
{
	$lab_id=preg_replace('/[^0-9]+/','',$lab_id);
	
	//--- GET ALL USERS NAME ---
	$query = "select id,substring(lname,1,1) as lname, substring(fname,1,1) as fname from users";
	$userQryRes = get_array_records_query($query);
	$userNameArr = array();
	for($i=0;$i<count($userQryRes);$i++)
	{		
		$name = ucfirst($userQryRes[$i]['fname']);
		$name .= ucfirst($userQryRes[$i]['lname']);
		$id = $userQryRes[$i]['id'];
		$userNameArr[$id] = $name;
	}
	
	//--- GET ALL IMAGES WITH DETAILS ----
	if($mode=='view' && $multi_pt_id!='' && $upload_from=='admin_documents')
	{
		$query = "select upload_lab_rad_data_id ,upload_file_name ,upload_by,
										 date_format(upload_date,'".get_sql_date_format()."') as upload_date,
										 upload_file_type from upload_lab_rad_data
										 where uplaod_primary_id = '".$lab_id."' and scan_from = '".$upload_from."' 
										 AND givenToEduMultiPtId LIKE \"%\'".$multi_pt_id."\'%\"";
	}
	else
	{
		$query = "select upload_lab_rad_data_id ,upload_file_name ,upload_by,
										 date_format(upload_date,'".get_sql_date_format()."') as upload_date,
										 upload_file_type from upload_lab_rad_data
										 where uplaod_primary_id = '".$lab_id."' and scan_from = '".$upload_from."' and upload_status = '0'";
	}
	
	$imgQryRes = get_array_records_query($query);
	$image_data = '';
	$image_counter = 0;
	for($i=0,$j=1;$i<count($imgQryRes);$i++,$j++)
	{
		$upload_file_name = $imgQryRes[$i]['upload_file_name'];
		$upload_lab_rad_data_id = $imgQryRes[$i]['upload_lab_rad_data_id'];
		$upload_date = $imgQryRes[$i]['upload_date'];
		$upload_file_name = $imgQryRes[$i]['upload_file_name'];
		$upload_by = $imgQryRes[$i]['upload_by'];
		$upload_file_type = $imgQryRes[$i]['upload_file_type'];
		$user_name = $userNameArr[$upload_by];
		
		$upload_file_name = str_replace('../main/uploaddir','',$upload_file_name);
		$upload_file_name = (substr_count($upload_file_name,'#')>0) ? str_replace('#','%23',$upload_file_name) : $upload_file_name ;
		$chkBxHidden = ($mode=='view') ? $chkBxHidden='hidden' : 'checkbox';
		
		$arrPath = pathinfo($upload_file_name);
		$extension = $arrPath['extension'];
		
		$is_pdf_file = ($extension == "pdf") ? true : false;
		
		$pdf_image_path = '/library/images/pdfimg.png';
		
		$thumb_image_name = str_replace('upload_'.$upload_from,'upload_'.$upload_from.'/thumbnail',$upload_file_name);
		$thumb_image_path = ($is_pdf_file) ? $GLOBALS['webroot'].$pdf_image_path : data_path(1).substr($thumb_image_name,1);
		$thumb_real_path  = ($is_pdf_file) ? $GLOBALS['fileroot'].$pdf_image_path : data_path().substr($thumb_image_name,1);
		
		$image_name = $upload_file_name;
		$image_path = data_path(1).substr($image_name,1);
		$real_path = data_path().substr($image_name,1);
		
		if( file_exists($real_path) )
		{
			$new_width  = 720;
            $new_height = 1000;
            list($width, $height) = getimagesize($real_path);
            // pre($width.'-'.$height);

            if($width > 720 || $height > 1000) {
                if ($width > $height) {
                  $image_height = floor(($height/$width)*$new_width);
                  $image_width  = $new_width;
                } else {
                  $image_width  = floor(($width/$height)*$new_height);
                  $image_height = $new_height;
                }

                $width = $image_width;
                $height = $image_height;
            }

			$image_counter++;
			$onclick = ($is_pdf_file) ? 'onClick="window.open(\''.$image_path.'\',\'\',\'width=800, height=700\');"' : 'onClick="set_image(\''.$image_path.'\','.$image_counter.');"';
			$image_data .= '<div class="col-xs-2">';
			$image_data .= '<div class="thumbnail text-center pd0">';
			$image_data .= '<div class="image-grid">';
			
			$thumb_image_path = file_exists($thumb_real_path) ? $thumb_image_path : $image_path;
			$image_data .= '<span><img class="img-thumbnail" src="'.$thumb_image_path.'" style="min-height:100px; max-height: 100px;" /></span>';
			
			$image_data .= '<div class="delete '.$chkBxHidden.'" title="Select to delete">';
			$image_data .= '<div class="checkbox checkbox-default">';
			$image_data .= '<input type="'.$chkBxHidden.'" id="delete_'.$upload_lab_rad_data_id.'" name="delete_scan_doc[]" value="'.$upload_lab_rad_data_id.'" data-fullimg="'.$image_path.'" data-fullimgWidth="'.$width.'" data-fullimgHeight="'.$height.'" onchange="checkSelectAll()"/>';
			$image_data .= '<label for="delete_'.$upload_lab_rad_data_id.'" title="Select to delete"></label>';
			$image_data .= '</div>';
			$image_data .= '</div>';
			
			$image_data .= '<a class="layer" id="link_'.$image_counter.'" '.$onclick.'></a>';
			
			$image_data .= '</div>';
			if( $user_name ) 
				$image_data .= '<span><label><b>By : </b>'.$user_name.'</label></span>';
			$image_data .= '<span><label><b>Date : </b>'.$upload_date.'</label></span>';
			
			$image_data .= '</div>';
			$image_data .= '</div>';
		}
		
		if( $j == 6 ) {	$image_data .= '<div class="clearfix mt5"></div>'; $j = 0; }
		
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
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript">
			window.focus();
			var web_root = '<?php echo $GLOBALS['php_server'];?>';
			var browser_name = '<?php echo $browser['name'];?>';
			var total_images = '<?php echo $image_counter;?>';
			total_images = parseInt(total_images);
			var mdl_max_height = 650;
			function scan_document(scan_or_upload)
			{
				var lab_id = '<?php echo $lab_id; ?>';
				var upload_from = '<?php echo $upload_from; ?>';
				var ins_data_id = '<?php echo $_REQUEST['ins_data_id']; ?>';
				var url = 'scan_and_upload_data.php?scanOrUpload='+scan_or_upload;
				url += '&lab_id='+lab_id+'&upload_from='+upload_from;
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
			
			function resize_layout() {
				var fh = $(".panel-footer").outerHeight(true);
				var hh = $(".panel-heading").outerHeight(true);
				var h = window.outerHeight - (fh+hh+75);
				$(".panel-body").css({'max-height':h+'px','height':h+'px'});
				mdl_max_height = h*0.85;
				$(".modal-body").css({'max-height':mdl_max_height+'px','min-height':mdl_max_height+'px'});
			}
			
			function resize_window() 
			{ 
				var parWidth = screen.availWidth * 0.9; //(screen.availWidth > 1200) ? 1200 : screen.availWidth ;
				var parHeight = screen.availHeight * 0.9; //(browser_name == 'msie') ? 810 : 800;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
				resize_layout();	
			}
			
			function set_image($_image,iCounter)
			{
				if($_image){
					var img = '<img src="'+$_image+'" />';
					$("#batch_images .modal-body").html(img);
					$("#batch_images").modal('show');
					
					var nextActionBtnDisable = false;
					var prevActionBtnDisable = false;
					if( iCounter >= total_images ) nextActionBtnDisable = true;
					if( iCounter <= 1) prevActionBtnDisable = true;
						
					$("#nextButton").prop('disabled',nextActionBtnDisable).data('counter',iCounter+1);
					$("#prevButton").prop('disabled',prevActionBtnDisable).data('counter',iCounter-1);
					
				}
			}
			
			function prevNext(_this){
				var c = $(_this).data('counter');
				$("a#link_"+c).trigger('click');
			}
			
			function close_window(){
			var scan_id = document.getElementById('row_id').value;
				if(scan_id!=''){
					jQuery.ajax({
						url: '<?php echo basename($_SERVER['PHP_SELF']); ?>',
						dataType: 'html',
						type: 'POST',
						data: {ajax_req:'ajax_scan_id', scan_id:scan_id}, 
						beforeSend: function() {},
						success: function(data) {
							var resp = data.split('|~~|');
							var imgCheck = resp[0];
							var row_id = resp[1];
							if(top.frames[0]) {
								if(top.frames[0].frames[0]){
									if(top.frames[0].frames[0].frames[0]){
										if(top.frames[0].frames[0].frames[0].document.getElementById("scn_id_"+row_id)) {
											if(imgCheck == 1){
												top.frames[0].frames[0].frames[0].document.getElementById("scn_id_"+row_id).innerHTML = '<img src="../chart_notes/images/scanDcs_active.png" alt="Scan" border="0">';
											} else {
												top.frames[0].frames[0].frames[0].document.getElementById("scn_id_"+row_id).innerHTML = '<img src="../chart_notes/images/scanDcs_deactive.png" alt="Scan" border="0">';
											}		
										}
									}
								}
							}
							top.removeMessi();
						}
					});
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
			$(window).resize(function(){resize_layout();});
		
            function PrintDiv() {
                var divToPrint = document.getElementById('divToPrint');
                var img = $('#divToPrint img').attr('src');
                
                var $new_width  = 720;
                var $new_height = 1000;
                
                //var newImage = new Image();
                //newImage.src = img;
                var width = $('#divToPrint img').width();
                var height = $('#divToPrint img').height();

                if(width > 720 || height > 1000) {
                    if (width > height) {
                      var image_height = Math.floor((height/width)*$new_width);
                      var image_width  = $new_width;
                    } else {
                      var image_width  = Math.floor((width/height)*$new_height);
                      var image_height = $new_height;
                    }

                    width = image_width;
                    height = image_height;
                }

                var image = '<img src="'+img+'" width="'+width+'" height="'+height+'" />';
                var w = screen.availWidth*0.8;
                var h = screen.availHeight*0.8;
                var popupWin = window.open('', '_blank', 'width='+w+',height='+h);
                popupWin.document.write('<html><body>' + image + '</html>');
                popupWin.document.close();
                popupWin.print();
                popupWin.close();
            }

			function selectAll() {
				var delete_scan_doc_op = document.getElementsByName("delete_scan_doc[]");
				var j = 1;
				var index = 0;
				var no_checked = 0;
				while (index < delete_scan_doc_op.length) {
		        	if(delete_scan_doc_op[index].checked == true){
						no_checked++;
					}
		            index++;
		        }
		        
		        if(delete_scan_doc_op.length != no_checked) {
		        	for(i=0;i<delete_scan_doc_op.length;i++){
						delete_scan_doc_op[i].checked = true;
						$('#link_'+j).css('display','block');
						$('#link_'+j).parent('.image-grid').find('div.delete').css('display','block');
						$('#selectAll').text('Unselect All');
						j++;
					}
		        } else {
		        	for(i=0;i<delete_scan_doc_op.length;i++){
						delete_scan_doc_op[i].checked = false;
						$('#link_'+j).css('display','none');
						$('#link_'+j).parent('.image-grid').find('div.delete').css('display','none');
						$('#selectAll').text('Select All');
						j++;
					}
		        }
			}
			function checkSelectAll() {
				var cBox = document.getElementsByName("delete_scan_doc[]");
				var index = 0;
				var no_checked = 0;
				while (index < cBox.length) {
		        	if(cBox[index].checked == true){
						no_checked++;
					}
		            index++;
		        }
		        if(cBox.length != no_checked) {
		        	$('#selectAll').text('Select All');
		        } else {
		        	$('#selectAll').text('Unselect All');
		        }
			}
	        function printAll() {
	        	document.getElementById('loader').style.display = 'block';
				var cBox = document.getElementsByName("delete_scan_doc[]");

				var frame1 = document.createElement('iframe');
				frame1.name = "frame1";
				frame1.style.position = "absolute";
				frame1.style.top = "-1000000px";
				document.body.appendChild(frame1);
				var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
				frameDoc.document.open();

				var htmlContent = '<!DOCTYPE html><html><body>';
				var index = 0;
                var no_checked = 0;
                						
                while (index < cBox.length) {
		        	if(cBox[index].checked == true){
						var img_w = document.getElementsByName("delete_scan_doc[]")[index].getAttribute('data-fullimgWidth');
		        		var img_h = document.getElementsByName("delete_scan_doc[]")[index].getAttribute('data-fullimgHeight');
						var path = document.getElementsByName("delete_scan_doc[]")[index].getAttribute('data-fullimg');
						htmlContent += '<div class="contentWrapper" style="page-break-after: always;"><img src="'+path+'" width="'+img_w+'" height="'+img_h+'" /></div>';

						var n = 0;
						var imag = $('.contentWrapper > img') ;
						imag = new Image();
						imag.onload = function() { 
							n++;
							// console.log($(this).attr('src'));
							if(n == no_checked) {
								// console.log('done '+n+' -- '+no_checked); 
								// console.log(htmlContent);
								callPrint(htmlContent, frameDoc, frame1)
							}
						}
						imag.src=path;
						imag.width=img_w;
						imag.height=img_h;

						no_checked++;
					}
		            index++;
		        }
		        htmlContent += "</body></html>";

		        if(no_checked == 0) {
		        	alert('Please select any image.');
		        	document.getElementById('loader').style.display = 'none';
		        }
				return false;
	        }

	        function callPrint(content,frameDoc,frame1) {
				frameDoc.document.write(content);
				frameDoc.document.close();
				setTimeout(function () {
					window.frames["frame1"].focus();
					window.frames["frame1"].print();
					document.body.removeChild(frame1);
				}, 500);
				document.getElementById('loader').style.display = 'none';
	        }
 </script>
    <style type="text/css">
		.image-grid { height:auto;}

		#loader {display: none; position: absolute; top: 0;left: 0;right: 0;margin: 0 auto; width: 100%; height: 100%; 
			background: rgba(0,0,0,0.5); padding-top: 60px;
		    z-index: 999;
		    text-align: center;
		    font-size: 60px;
		    color: #fff;
		}
	</style>
		
</head>
<body class="body_c">
	<div id="loader">Loading ...</div>
  
  	<div class="panel panel-primary">
      <div class="panel-heading clearfix">
      	<span class="pull-left">View Image(s) </span>
      	<button type="button" class="btn btn-success btn-sm pull-right" id="selectAll" onclick="selectAll()">Select All</button>
      </div>
      <div class="panel-body popup-panel-body" style="height:640px;max-height:640px;">
      	
        <div class="clearfix">&nbsp;</div>
      	<form name="view_frm" action="" method="post">
  				<input type="hidden" name="scan_id" id="scan_id" value="<?php print $scan_id; ?>" >
          <input type="hidden" name="scan_for" id="scan_for" value="<?php print $scan_for; ?>" >
          <input type="hidden" name="mode" id="mode" value="<?php print $mode; ?>" >
          <input type="hidden" name="row_id" id="row_id" value="<?php echo $_REQUEST["lab_id"]; ?>" >
          <input type="hidden" name="multi_pt_id" id="multi_pt_id" value="<?php print $multi_pt_id; ?>" >
          <input type="hidden" name="del_id" id="del_id" value="">
          
          <div class="row" id="rowData">
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
        <button type="button" class="btn btn-success pull-left" onclick="printAll();">Print All</button>
      
      	<button type="button" name="scan_doc" class="btn btn-primary" id="scan_doc" onClick="scan_document('scan');">Scan</button>
        <button type="button" name="upload_doc" class="btn btn-success" id="upload_doc" onClick="scan_document('upload');">Upload</button>
        <button type="button" name="delete_doc" class="btn btn-warning" id="delete_btn" onClick="delete_doc();">Delete</button>
        <button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
      </footer>
      
    </div>
    
    <div id="batch_images" class="modal" role="dialog" >
      <div class="modal-dialog modal-lg" style="width:90%;">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">x</button>
            <h4 class="modal-title" id="modal_title">View Image(s)</h4>
          </div>
          
          <div class="modal-body" id="divToPrint" style="min-height:200px; max-height:650px; overflow:hidden; overflow-y:auto;">
            <div class="loader"></div>
          </div>
          
          <div class="modal-footer">
           	<button type="button" id="prevButton" onClick="prevNext(this);" class="btn btn-success pull-left" data-counter="0">Prev</button>
           	<button type="button" id="nextButton" onClick="prevNext(this);" class="btn btn-success pull-left" data-counter="0">Next</button>
            <button type="button" name="print_doc" class="btn btn-success" id="print_doc" onClick="PrintDiv();">Print</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
          
        </div>
   		</div>
   	</div>

	</body>
</html>
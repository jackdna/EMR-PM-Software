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
require_once("../../../config/globals.php");
require_once("../../../library/patient_must_loaded.php");
include_once($GLOBALS['srcdir']."/classes/eligibility.class.php");
$patient_id = $_SESSION['patient'];
$OBJCommonFunction = new CLSCommonFunction;
$pg_title = 'Eligibility';
$eligibility = new Eligibility($patient_id);
$library_path = $GLOBALS['webroot'].'/library';
$eligibility->save($_REQUEST);
$eligibility->eligibility_data = $eligibility->load_eligibity_data();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Eligibility :: imwemr ::';?></title>
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Application Common CSS -->
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <!-- jQuery's Date Time Picker -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
    <!-- Bootstrap -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
    <!-- Application Common JS -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
    <style>
		.grythead{ font-size:14px !important; white-space:nowrap; vertical-align:top; }
		.realtime_icon{ background-image:url(<?php echo $library_path; ?>/images/insuriconflt.png); background-repeat:no-repeat; background-position:0 0; display:inline-block; height:41px; padding:0px 21px; width:41px; margin-bottom:-18px;  }
.realtime_icon:hover{ background-image:url(<?php echo $library_path; ?>/images/insuriconflt.png); background-repeat:no-repeat; background-position:0 -41px;}
		</style>
    <!-- Patient Info JS -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/patient_info.js"></script>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  	<div class="container-fluid" id="body_div">
    	<form name="frmEligibility" id="frmEligibility" action="">
      	
        <!-- Hidden Fields Section Start -->
        <input type="hidden" name="save" id="save" value=""/>
        <!-- Hidden Fields Section End -->
      	
        <table class="table table-bordered table-hover table-striped scroll release-table">
          <thead class="header">
              <tr class="grythead">
                <td width="9%" >Status</td>
                <td width="6%" >Date</td>
                <td width="5%" >Time </td>
                <td width="7%" >DOS</td>
                <td width="7%" >DEC</td>
                <td width="10%">Name</td>
                <td width="7%" >DOB</td>
                <td width="6%" >Address</td>
                <td width="7%" >Ins. Carrier </td>
                <td width="6%" >Policy#</td>
                <td width="11%">Details</td>
                <td width="10%">Comments</td>
                <td width="5%" >S/U/P</td>
                <td width="4%" >OP</td>
              </tr>
          </thead>
          
          <tbody>
          	<?php
							echo implode('',$eligibility->eligibility_data);
						?>	
          </tbody>
       	</table>
          
      	    
   		</form>
  	</div>
    <script>
			var records = '<?php echo $eligibility->eligibility_count; ?>';
			records = parseInt(records);
			function saveEligibility(){
				document.getElementById("save").value = "save";
				document.frmEligibility.submit();
			}
			function get271Report(id){top.popup_win('eligibility_report.php?id='+id);}
			function doScan(id, processFor){top.popup_win('scan_upload_eligibility_doc.php?id='+id+'&scanOrUpload='+processFor);}
			function getPreview(id){
				$.ajax({
					url:'doc_preview.php',
					type:'post',
					dataType:'json',
					data:{action:'list',id:id},
					beforeSend:function(){
						top.show_loading_image('show',150);
					},
					complete:function(){
						top.show_loading_image('hide');
					},
					success:function(r){
						var title = 'Eligibility Scan Upload Preview ' + ((typeof r.p_name !== 'undefined') ? 'For '+r.p_name : '');
						var content = previewHTML(r.images);
						top.show_modal('eligibility_list_modal',title,content,'','550','modal_95');
					}
				});
			}
			
			function previewHTML(d){
				var html = '';
				
				//html += '<div class="col-xs-12">';
				html += '<div class="row">';
				
				// Left Side
				html += '<div class="col-xs-2" style="overflow:hidden; overflow-y:auto; max-height:520px; min-height:500px;">';
				var $file_tag = '<div class="alert alert-info text-center"><b>No Preview Available</b></div>';
				for(var i in d) {
					
					var w = d[i].width; var h = d[i].height; var t = d[i].docType;
					
					var $file_dir = top.JS_WEB_ROOT_PATH + '/data/'+ top.practice_dir;
					var $file_path = $file_dir + d[i].docPath;
					
					// Thumb File path
					var $file_thumb = $file_path.replace('real_time_scan_upload/','real_time_scan_upload/thumb/');
					if( t == 'pdf')	$file_thumb = top.JS_WEB_ROOT_PATH + '/library/images/pdfimg.png';
					
					if( i == 0 ){
						var $file_tag = '<div style="width:'+w+'px; height:'+h+'px;"><img src="'+$file_path+'" width="'+w+'" height="'+h+'" /></div>';
						if( t == 'pdf')
							$file_tag = '<object width="100%" height="520" data="'+$file_path+'"></object>';		
						
					}
					
					var $file_thumb_tag = '<img src="'+$file_thumb+'" />';
					
					html += '<div class="col-xs-12 pointer text-center" id="eligBox_'+d[i].scanId+'" data-id="'+d[i].scanId+'">';
					html += '<div class="row image-grid" style="height:auto;">';
					html += '<span>'+$file_thumb_tag+'</span>';
					if( d[i].docDate != '00-00-0000')
						html += '<span class="clearfix "><b>'+d[i].docDate+'</b></span>';
					
					html += '<span class="layer" onClick="top.fmain.setPath(this);" data-path="'+$file_path+'" data-width="'+w+'" data-height="'+h+'" data-type="'+t+'"></span>'
					html += '<a class="delete" title="Delete" href="javascript:top.fmain.delImage(\''+d[i].scanId+'\');"><i class="glyphicon glyphicon-remove-circle"></i></a>';
					html += '</div>';
					html += '</div>';
					
				}
				
				html += '</div>';
				
				// Right Side
				html += '<div class="col-xs-10" style="overflow:auto; max-height:520px; min-height:500px;" id="previewContentBox">';
				html += $file_tag;
				html += '</div>';
				
				html += '</div>';
				//html += '</div>';
				
				return html;
			}
			
			function setPath(_this){
				var t = $(_this,top.document).data('type');
				var p = $(_this,top.document).data('path');
				var w = $(_this,top.document).data('width');
				var h = $(_this,top.document).data('height');
				
				var $file_tag = '<div style="width:'+w+'px; height:'+h+'px;"><img src="'+p+'" width="'+w+'" height="'+h+'" /></div>';	
				if( t == 'pdf') {
					$file_tag = '<object width="100%" height="500" data="'+p+'"></object>';		
				}
				
				if( $("#eligibility_list_modal #previewContentBox",top.document).length){
					$("#eligibility_list_modal #previewContentBox",top.document).html($file_tag);
				}
				
			}
			
			function delImage(id){
				$.ajax({
					url:'doc_preview.php',
					type:'post',
					dataType:'json',
					data:{action:'del',id:id},
					beforeSend:function(){
						top.show_loading_image('show',150);
					},
					complete:function(){
						top.show_loading_image('hide');
					},
					success:function(r){
						if( r.success ){
							if( $('#eligBox_'+id,top.document).next().find('.layer').length > 0 ) {
								$('#eligBox_'+id,top.document).next().find('.layer').trigger('click');
							}
							else if( $('#eligBox_'+id,top.document).prev().find('.layer').length > 0 ) {
								$('#eligBox_'+id,top.document).prev().find('.layer').trigger('click');
							}
							else if($("#eligibility_list_modal #previewContentBox",top.document).length > 0 )
								$("#eligibility_list_modal #previewContentBox",top.document).html('<div class="alert alert-info text-center"><b>No Preview Available</b></div>');
							
							$('#eligBox_'+id,top.document).remove();
							top.fAlert('Image deleted successfully');
						}
						else
							top.fAlert('!!!...Error while deleting. Please try again.');
					}
				});
				
			}
			
			function setRTEAmount(elId){top.popup_win('eligibility_report.php?set_rte_amt=yes&id='+elId);}
			
			if(records > 0)
			{
				var arr = [ ["btSaveEl","Save","top.fmain.saveEligibility();"],
										["add_appt","Add Appt","top.change_main_Selection(top.document.getElementById('sch_icon_li'));"]
									];
			}
			else
			{
				var arr = [["add_appt","Add Appt","top.change_main_Selection(top.document.getElementById('sch_icon_li'));"]];
			}
			top.btn_show("ELIGIBILITY",arr);
			top.show_loading_image("hide");
			
			function show_details(_this)
			{
				var obj = $(_this);
				
				var title = obj.data('title');
				var id = obj.data('id');
				var req = obj.data('request');
				
				$.ajax({
					url:'eligibility_parser.php',
					type:'post',
					data:{id:id,t:req},
					beforeSend:function(){
						top.show_loading_image('show',150);
					},
					complete:function(){
						top.show_loading_image('hide');
					},
					success:function(r) {
						var content = r;
						top.show_modal('eligibility_modal',title,content,'','550','modal_95')
					}
					
				})
				
			}
			top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
		</script>
	</body>  
</html>
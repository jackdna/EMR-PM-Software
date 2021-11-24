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
 
 Purpose: Manage Patient's consent Forms.
 Access Type: Indirect Access.
 
*/

include_once("../../../config/globals.php");
require_once("../../../library/patient_must_loaded.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION['patient'];
$pg_title = 'Consent Forms';

$blClientBrowserIpad = false;
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$blClientBrowserIpad = true;
}

$consentScroll=' scrolling="no" ';
if($blClientBrowserIpad == true){
	$consentScroll = ' scrolling="yes" ';	
}
$_GET['from'] = xss_rem($_GET['from']);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Consent Form :: imwemr ::';?></title>
		
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
    <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>
  	<script type="text/javascript">
		function sendSavedFax(){
			var getFaxNo=$('#send_fax_number').val();
			var getFaxRecipientName=$('#send_fax_name').val();
			var getFaxName=$('#sendSaveFaxName').val();
			var db_form_created_date=$('#db_form_created_date').val();
			var package_category_id=$('#package_category_id').val();
			var form_information_id=$('#form_information_id').val();
			//var pat_template_id=$("#pat_temp_id").val()
			//var ref_phy_id=$("#hiddselectReferringPhy").val();
			
			var errMsg = '';
			var focus = '';
			if($('#send_fax_name').val()===''){
				errMsg  += "&#8226; Name\n";
				focus = '#send_fax_name';
			}
			if($('#send_fax_number').val()===''){
				errMsg  += "&#8226; Fax Number\n";
				if(focus==='')
					focus = '#send_fax_number';
			}
			
			if(errMsg!==''){
				errMsg = "Please enter following fields(s):\n"+errMsg;
				
				/*Decode Html entities*/
				var div = document.createElement('div');
				div.innerHTML = errMsg;
				errMsg = div.firstChild.nodeValue;
				
				alert(errMsg);
				$(focus).focus();
				return false;
			}else{
				window.top.show_loading_image("show", "150", "");
				fanNoTemp = getFaxNo.replace(/[^0-9+]/g,"");
				url_hold_sig = "consent_send_fax.php?send_fax=yes&txtConsentFaxNo="+fanNoTemp+"&txtConsentFaxName="+getFaxRecipientName+"&txtConsentFaxPdfName="+getFaxName+"&db_form_created_date="+db_form_created_date+"&package_category_id="+package_category_id+"&form_information_id="+form_information_id;
				$.ajax({
					type: "POST",
					url: url_hold_sig,
					success: function(r){
						$('#div_load_image').hide();
						//document.write(r);
						var msg=r.split(".");
						window.top.show_loading_image("hide", "150", "");
						alert(r);
						if(msg[0]=="Transaction_No" && getFaxNo){
							//$("#send_fax_div").hide();
							$(".btn-danger").click();
							
							
							$("#consent_tree_id").attr("src","tree4consent_form.php");
						}
					}
				});
			}
		}
		function show_consent_fax_div() {
			var form_information_id = $('#form_information_id').val();
			if(document.getElementById("send_fax_number")) {
				document.getElementById("send_fax_number").value = '';	
			}
			if(document.getElementById("send_fax_name")) {
				document.getElementById("send_fax_name").value = '';
			}
			$('#modal_title').html("Send Fax - Consent Form");
			top.fmain.document.getElementById("consent_data_id").src = "consent_send_fax.php?show_fax_popup=yes&form_information_id="+form_information_id;
			
			/*
			if(document.getElementById("send_fax_div")) {
				document.getElementById("send_fax_div").style.display = 'inline-block';
				document.getElementById("send_fax_div").style.left = '3px';
				//document.getElementById("send_fax_div").style.width = '300px';
			}*/
		}	
		
		function setFrameHeightConsentData() {
			var frame_id		=	parent.$('#consent_data');
			var main_height		= 	$(window).height();
			var header_height	=	parent.parent.$('#mainWhtboxHeader').outerHeight(true);
			var footer_height	=	parent.parent.$('#mainWhtboxFooter').outerHeight(true);
			var height_custom 	=	main_height - (header_height+footer_height)+20;
			frame_id.css({ 'min-height' : height_custom , 'max-height': height_custom });
			top.$('#div_loading_image').hide();
		}
		setFrameHeightConsentData();
		</script>  

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
 	<body onUnload="top.btn_show('');">
  	<?php
  		if(isset($_GET['from']) && !empty($_GET['from']) && $_GET['from'] == 'checkin') {
				$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 470)) ;
  		} else {
				$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 290)) ;
  		}
		//$col_height = $col_height-22;
	?>
  	<div class="col-xs-12 bg-white">
    	<div class="row">
            <div class=" col-xs-2 " style="height:<?php echo $col_height;?>px; max-height:100%; overflow:scroll">
            	<?php include_once 'tree4consent_form.php'; ?>
            </div>
            <div class="col-xs-10 ">
                <div class="row">
                    <div class="well pd0 margin_0 nowrap" style="vertical-align:text-top;">
                    <iframe name="consent_data" id="consent_data_id" <?php echo $consentScroll;?>  style="width:100%; height:<?php echo $col_height;?>px;" src="treeDetails.php" frameborder="0"></iframe>
                    </div>   
                </div>
            </div>
          </div>
    </div>
        
    <div id="send_fax_div" class="modal" role="dialog" >
        <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h4 class="modal-title" id="modal_title">Send Fax - Consent Package</h4>
            </div>
          
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <label>Name</label>
                        <br>
						<input type="text" name="selectReferringPhy" id="selectReferringPhy" class="form-control" 
							onkeyup="top.loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number','','','send_fax_number','','',top.fmain);"
							autocomplete="off">
						<input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label>Fax No.</label>
                        <br>
                        <input type="text" name="send_fax_number" id="send_fax_number" class="form-control" autocomplete="off">
                    </div>
                </div>                                     
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info"  id="send_close_btn" onclick="return sendSavedFax();" >Send Fax</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="$('#div_load_image').hide();top.$('#div_loading_image').hide();" >Close</button>

			<input type="hidden" name="sendSaveFaxName" id="sendSaveFaxName" value="<?php echo $setNameFaxPDF;?>">
			<input type="hidden" name="db_form_created_date" id="db_form_created_date" value="">
			<input type="hidden" name="package_category_id" id="package_category_id" value="">
			<input type="hidden" name="form_information_id" id="form_information_id" value="">
          </div>
          
        </div>
      </div>
    </div>
    <form name="consent_main_frm" action="index.php" method="get">
    	<input type="hidden" name="consent_form_id" id="consent_form_id">
        <input type="hidden" name="doc_name" id="doc_name" value="<?php echo $_REQUEST["doc_name"];?>">
    </form>
	<script>
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
    $(".popbox").each(function(i, obj) {
        $(this).popover({
          html: true,
          content: function() {
            var id = $(this).attr('id')
            return $('#popover-content-' + id).html();
          }
        });
    });			
    
    $('#consent_data_id').load(function(){
    var src = $(this)[0].contentWindow.location.href;
    src = src.split('.pdf?');
    if(typeof(src[1])!=='undefined' && src[1]==='hidebtn'){
        top.btn_show('');
    }
    });
	
	
	$(document).ready(function() {
		var consent_form_id = "<?php echo $_REQUEST["consent_form_id"];?>";
		if(consent_form_id) {
			top.fmain.consent_data.location = "consentFormDetails.php?consent_form_id="+consent_form_id;	
		}
	});
    
    </script>
	</body>
</html>
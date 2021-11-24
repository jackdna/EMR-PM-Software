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
?>
<?php 
/*	
File: check_in_consent_index.php
Purpose: Consent forms in checkin screen
Access Type: Include 
*/

include_once('../../config/globals.php');
//include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once("../../library/patient_must_loaded.php");
//require_once("common/consent_functions.php");
$patient_id = $_SESSION['patient'];

//--- GET PATIENT INFORMATION -----
$patientQuery = "select *, date_format(DOB,'".get_sql_date_format()."') as patient_dob from patient_data where id = '$patient_id'";
$patientQryRes = get_array_records_query($patientQuery);

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
    <!--<link href="css_accounting.php" type="text/css" rel="stylesheet">-->
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/accounting.css" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">

    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-ui.min.1.11.2.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/ci_function.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/Driving_License_Scanning.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>

	<script type="text/javascript">
		function change_tab_color(id,clr){
			$('#Consent_Forms').removeClass('tab_white');
			$('#Consent_Forms').removeClass('tab_green');
			$('#Consent_Forms').removeClass('tab_orange');
			$('#Consent_Forms').addClass(clr);
		}
		function setFrameHeight() {
			var frame_id		=	$('#all_data');
			var body_class		=	$('.body_c');
			var div_loading_id	=	$('#div_loading_image');
			var main_width		= 	$(window).width();
			var main_height		= 	$(window).height();
			var header_height	=	$('#mainWhtboxHeader',top.document).outerHeight(true);
			var footer_height	=	$('#mainWhtboxFooter',top.document).outerHeight(true);
			var height_custom 	=	main_height - (header_height+footer_height+20);
			var width_custom 	=	main_width - 30;
			frame_id.css({ 'min-height' : height_custom , 'max-height': height_custom });
			body_class.css({ 'min-width' : width_custom  });
			if(document.getElementById("div_loading_image")) {
				document.getElementById("div_loading_image").style.left = parseInt(main_width/2)+'px';
				$("#div_loading_image").hide();
			}
		}
    </script>
    
    <style>
			.adminbox{ padding:10px;  margin-bottom:10px;border:none!important}
			.adminbox .headinghd{ border-bottom:2px solid #ff6b6b; padding:0px; margin:0px 0px 10px 0px; display:inline-block; float:left; width:100%  }
			.adminbox .headinghd h4{ text-transform:uppercase; font-size:16px; font-family: 'robotobold'; }
			.adminbox h3{ font-size:14px; color:#6c6c6c; margin:0px 0px 10px 0px; padding:0px; text-transform:uppercase; font-weight:bold }
			.adminbox .input-group-addon {padding: 0px 7px !important;}
			.adminbox label{overflow:inherit}
			.adminbox .tblBg { clear:both }
			.input-group-btn select {
				border-color: #ccc;
				margin-top: 0px;
				margin-bottom: 0px;
				padding-top: 7px;
				padding-bottom: 7px;
			}
			.thumbnail{padding:0px; margin-bottom: 0px!important}
			.extension_box label::before{display:none}
			footer::before{
				content: "";
				position: fixed;
				left: 0;
				width: 100%;
				height: 100%;
				-webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				-moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				box-shadow: 0px 0px 6px rgba(0,0,0,.8);
				z-index: 100;
			}
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.thumbnail .caption {
				position: absolute;
				top: 5px;
				width: 80%;
			}
			.xdsoft_monthselect .xdsoft_option{
				text-align: left !important;
			}
		</style>
</head>
<body class="body_c">
<div id="div_loading_image" class="text-center " style="position:absolute;">
	<div class="loading_container">
		<div class="process_loader"></div>
    	<div id="div_loading_text" class="text-info text-center">Please wait, while system is getting ready for you...</div>
	</div>
</div>	
<?php
	$temp_check_in_qsting_exp=explode('&',urldecode($_SESSION['temp_check_in_qsting']));
?>
	<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $defaultTab;?>">
    <div class="mainwhtbox " id="mainWhtboxHeader">
        <div class="row">
            <!-- Tabs -->
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-5">
                        <ul class="nav nav-tabs" style="border-bottom:none">
                            <li class="lead" onClick="window.location.href = '../scheduler/common/new_patient_info_popup_new.php?<?php echo urldecode($_SESSION['temp_check_in_qsting']); ?>'"><a href="javascript:void[0]"><?php if($temp_check_in_qsting_exp[1]=="sch_id="){echo "New Patient";}else{echo "Check In";} ?></a></li>
                            <li class="active lead" ><a href="#">Consent Forms</a></li>
                        </ul>
                    </div>	
                    <div class="col-sm-7">
                        <?php
                            $edit_patient_id = $patientQryRes[0]['pid'];
                            if($edit_patient_id != ''){
                                $last_name = $patientQryRes[0]['lname'];
                                $first_name = $patientQryRes[0]['fname'];
                                echo '<p class="lead">'.$last_name.', '.$first_name.' - '.$edit_patient_id.'</p>';
                            }
                        ?>
                    </div>
                </div>	
            </div>	
        </div>
    </div>
    <div class="mainwhtbox">            
			<iframe name="all_data" id="all_data" width="100%" frameborder="0" scrolling="no" src="consent_forms/index.php?doc_name=signed_consent&from=checkin"></iframe>
		</div>		
   
    <div class="mainWhtbox" id="mainWhtboxFooter">
    	<div class="row">
            <div id="module_buttons" class="footer ad_modal_footer">
                <div class="col-sm-12 text-center">
                    <span id="module_buttons_new">
                        <span>
                            <input type="button" id="consent_save" name="consent_save" value="Save" class="btn btn-success" onClick="top.all_data.consent_data.save_form('save_form');loading();">
                            <input type="submit" id="btn_submit" name="btn_submit" value="Save" class="hide">	
                        </span>
                        <span>
                            <input type="button" id="consent_save_print" name="consent_save_print" value="Save and Print" class="btn btn-success" onClick="top.all_data.consent_data.save_form('print_form');">
                        </span>
                        <span>
                            <!--<input type="button" id="consent_print" name="consent_print" value="Print" class="btn btn-success" onClick="top.all_data.consent_data.print_form();">-->
                        </span>
                        <?php if(constant('DEFAULT_PRODUCT')=='imwemr'){?>
                                <span>
                                    <input type="button" id="consent_hold" name="consent_hold" value="On Hold for:" class="btn btn-success" onClick="top.all_data.consent_data.hold_dr_sig()">
                                </span>
                        <?php } ?>
                    </span>
                    <span>
                        <input type="button" id="but_closeTemp" name="but_closeTemp" value="Close" class="btn btn-danger" onClick="javaScript: window.close();">
                    </span>
                </div>
            </div>
      	</div>
    </div> 
<script type="text/javascript">
function loading(){
	$('.loading').show();
}

$(document).ready(function(){
	$('div.loading').hide();
	//frmeHeight = ($(window).height() - 70)+'px';
	//$('#all_data').height(frmeHeight);
    if(typeof(str)!='undefined'){
        $(".page_bottom_bar").html('<div class="nowrap alignCenter" style="overflow:hidden">'+str+'</div>');
    }
});
setFrameHeight();
</script>
</body>
</html>
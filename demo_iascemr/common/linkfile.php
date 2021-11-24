<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
if(trim($spec_new)) {
?>
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">		
<?php	
}
?>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<!--<link rel="stylesheet" href="css/form.css" type="text/css" />
<link rel="stylesheet" href="css/theme.css" type="text/css" />
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
-->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-select.css" />
    <link rel="stylesheet" href="css/jquery.webui-popover.css" />
    <link rel="stylesheet" href="css/datepicker.css" />
   
    <script type="text/javascript" src="js/jquery-1.11.3.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/bootstrap-select.js"></script>

	<script type="text/javascript" src="js/wufoo.js"></script>
    <script type="text/javascript" src="js/jsFunction.js"></script>
	<script type="text/javascript" src="js/cur_timedate.js"></script>
	<script type="text/javascript" src="js/simpletreemenu.js"></script>
    <script type="text/javascript" src="js/moocheck.js"></script>
	<script src="js/epost.js"></script>
	<script type="text/javascript" src="js/disableKeyBackspace.js"></script>
	<script src="js/dragresize.js"></script>
    
    


    
<?php
echo $spec_new;

include_once("common/functions.php");
include_once("common/includeFiles.php");

$title1_color="#FFFFFF"; //white 
$title2_color="#000000"; //black
$commonbg_color="#BCD2B0"; //Green
$bottom_common_bg_color="#ECF1EA";
// Done BY Mamta 
	//Pre_op_physician_order (2 Forms)
$bgdark_orange_physician="#C06E2D";
$bgmid_orange_physician="#DEA068";
$border_color_physician="#BB5E00";
$bglight_orange_physician="#FFE6CC";
$row1color_physician="#FFF2E6";
	//Local/gen_anes_record(4 forms)
//$border_blue_local_anes="#080E4C";
$border_blue_local_anes="#323CC0";
$bgdark_blue_local_anes="#3232F0";
$bgmid_blue_local_anes="#80AFEF";
$bglight_blue_local_anes="#EAF4FD";
$tablebg_local_anes="#C5D8FD";
$white="#FFFFFF";

//End edit by mamta


// Done BY Munisha
           //Post_op_nursing_order
$title_post_op_nursing_order="#C0AA1E";
$bgcolor_post_op_nursing_order="#F5EEBD";
$border_post_op_nursing_order="#C0AA1E";
$heading_post_op_nursing_order="#EFE492";
$rowcolor_post_op_nursing_order="#FDFAEB";
          //pre_op_nursing_order
$title_pre_op_nursing_order="#C0AA1E";
$bgcolor_pre_op_nursing_order="#F5EEBD";
$border_pre_op_nursing_order="#C0AA1E";
$heading_pre_op_nursing_order="#EFE492";
//$rowcolor_pre_op_nursing_order="#FAF6DC";
$rowcolor_pre_op_nursing_order="#FDFAEB";
         
		 //op_room_record
$title_op_room_record="#004587";
$bgcolor_op_room_record="#CFE1F7";
$border_op_room_record="#004587";
$heading_op_room_record="#80A7D6";
$rowcolor_op_room_record="#E2EDFB";

 		//laser procedure
$bgcolor_laser_procedure="#F5EEBD";

      //discharge summary sheet
$title_discharge_summary_sheet="#FF950E";
$bgcolor_discharge_summary_sheet="#FBE8D2";
$border_discharge_summary_sheet="#FF950E";
$heading_discharge_summary_sheet="#FCBE6F";
$rowcolor_discharge_summary_sheet="#FBF5EE";
  //Amendments_notes
$title_Amendments_notes="#A0A0C8";
$bgcolor_Amendments_notes="#EEEEFA";
$border_Amendments_notes="#A0A0C8";
$heading_Amendments_notes="#D0D0ED";
$rowcolor_Amendments_notes="#F0F0FA";
//End edit by Munisha

//SET BACKGROUND COLOR OF MANDATORY FIELDS FOR ALL CHART NOTES
$whiteBckGroundColor='background-color:#FFFFFF;';
$chngBckGroundColor='background-color:#F6C67A';
//END SET BACKGROUND COLOR OF MANDATORY FIELDS FOR ALL CHART NOTES
?>

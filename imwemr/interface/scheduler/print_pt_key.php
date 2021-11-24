<?php
require_once(dirname(__FILE__).'/../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once($GLOBALS['fileroot'].'/library/classes/print_pt_key.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location

//require_once(dirname(__FILE__)."/../main/Functions.php");
$qry_template="SELECT pt_docs_template_content FROM pt_docs_template WHERE pt_docs_template_enable_facesheet='temp_key' LIMIT 0,1";
$res_template=imw_query($qry_template);
$print_pt_key = new print_pt_key;
$patientId = $_REQUEST['patient_id'];
if(imw_num_rows($res_template)>0){
	echo $loading='<div style="font-family:verdana; font-size:16px;position:abosulte; margin:20% 30%;">Please wait. Printing is in process..<br><img src="'.$GLOBALS['webroot'].'/library/images/ajax-loader.gif" style="margin-left:30px;"></div>';
	imw_query("UPDATE patient_data SET temp_key_chk_datetime='".date('Y-m-d H:i:s')."', temp_key_chk_val = '1', temp_key_chk_opr_id = '".$_SESSION['authId']."' WHERE id = '".$patientId."'");
	$patQry = "select patient_data.*, pos_facilityies_tbl.facilityPracCode,heard_about_us.heard_options , 
				employer_data.name emp_name, employer_data.street as emp_street, 
				employer_data.street2 as emp_street2, employer_data.state as emp_state,
				employer_data.postal_code as emp_postal_code, employer_data.city as emp_city,
				users.lname as users_lname, users.fname as users_fname, users.mname as users_mname, users.pro_suffix as users_suffix,
				date_format(patient_data.date, '".get_sql_date_format()."') as reg_date,
				date_format(patient_data.DOB, '".get_sql_date_format()."') as patient_dob
				
				from patient_data left join pos_facilityies_tbl 
				on pos_facilityies_tbl.pos_facility_id = default_facility
				left join heard_about_us on patient_data.heard_abt_us = heard_about_us.heard_id
				left join employer_data on employer_data.pid = patient_data.id
				left join users on users.id = patient_data.providerID
				where patient_data.id = '$patientId'";
	$tempData=imw_query($patQry);
	$patQryRes = imw_fetch_assoc($tempData);
	
	$templateData=$template_data="";

	$row_template=imw_fetch_assoc($res_template);
	$template_data=html_entity_decode($row_template['pt_docs_template_content']);
	if($template_data){
	$templateData = $print_pt_key->loadTemplateData($template_data,$patQryRes);
		
	}
}else{
	echo '<div style="font-family:verdana; border:2px solid #CCC;border-radius:10px; font-size:16px; padding:15px; width:730px;position:abosulte; margin:20% 7%;">No Template Selected For PT Key. <br>Please add template in Admin->Documents->Pt Docs Tab for (Template Type) PT Key.</div>';
}

//========IMAGES PATH REPLACEMENT AS PER R8 SERVERS========
$templateData = str_ireplace($webroot.'/data/'.PRACTICE_PATH.'/gn_images/','../../data/'.PRACTICE_PATH.'/gn_images/',$templateData);
//=========================THE END=========================

$templateData = str_ireplace($webroot.'/interface/common/new_html2pdf/','../../reports/new_html2pdf/',$templateData);
$templateData = str_ireplace($webroot.'/interface/reports/new_html2pdf/','',$templateData);
$templateData = str_ireplace($web_root.'/interface/main/uploaddir/document_logos/','../../main/uploaddir/document_logos/',$templateData);

if($templateData){
	$file_location = write_html($templateData);
}

?>
<html>
	<head>
    	<title>PT KEY PRINT</title>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
        <?php if($file_location){?>
		<script type="text/javascript">
            top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
            top.html_to_pdf('<?php echo $file_location; ?>','p','',true);
        </script>
        <?php }?>
    </head>
    <body class="body_c bg2">
    	<form name="frm_print_pt_key" >
        </form>
    </body>
</html>
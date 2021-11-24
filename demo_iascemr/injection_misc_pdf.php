<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
//print '<pre>';
$root=($_SERVER['DOCUMENT_ROOT']);
include("common_functions.php");
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
// allergies_status_reviewed(table field t be added)
include("new_header_print.php");
include_once("injection_misc_pdf_content.php");
$table_printh=$table_main;
//die($table_main);

//START GET IDOC APPOINTMENT ID
$sc_emr_iasc_appt_id = "";
$stQry = "Select appt_id from stub_tbl where patient_confirmation_id='".$_REQUEST["pConfId"]."' ORDER BY stub_id DESC LIMIT 0,1 ";
$stRes = imw_query($stQry);
if(imw_num_rows($stRes)>0) {
	$stRow = imw_fetch_array($stRes);
	$sc_emr_iasc_appt_id = $stRow["appt_id"];
}
//END GET IDOC APPOINTMENT ID


?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<?php 
if($injectionMiscFormStatus =='completed' || $injectionMiscFormStatus =='not completed') {
	if($iDocOpNoteSave == "yes") { //ADD CONTENT IN IDOC OPERATIVE NOTE
		imw_close($link); //CLOSE SURGERYCENTER CONNECTION
		include_once('connect_imwemr.php'); // imwemr connection
		$chkInjectionOpnoteQry="SELECT pn_rep_id FROM pn_reports WHERE patient_id = '".$imwPatientIdInjection."' AND sc_emr_injection_report_id = '".$injectionprocedureRecordpostedID."' AND sc_emr_injection_report_id != '' ";
		$chkInjectionOpnoteRes=imw_query($chkInjectionOpnoteQry) or die($chkInjectionOpnoteQry.imw_error());
		$insUpdtInjectionOpQry = " INSERT INTO ";
		$insUpdtInjectionOpWhereQry =  " ";
		if(imw_num_rows($chkInjectionOpnoteRes)>0){
			$insUpdtInjectionOpQry = " UPDATE ";
			$insUpdtInjectionOpWhereQry =  " WHERE patient_id = '".$imwPatientIdInjection."' AND sc_emr_injection_report_id = '".$injectionprocedureRecordpostedID."' AND sc_emr_injection_report_id != '' ";	
		}
		$setInjectionOpnoteQry = $insUpdtInjectionOpQry." pn_reports SET  
							patient_id 					= '".$imwPatientIdInjection."',
							txt_data 					= '".addslashes($table_printh)."',
							pn_rep_date 				= '".date("Y-m-d H:i:s")."',
							sc_emr_template_name 		= 'ASC Injection Procedure',
							sc_emr_injection_report_id 	= '".$injectionprocedureRecordpostedID."',
							sc_emr_iasc_appt_id			= '".$sc_emr_iasc_appt_id."',
							status 						= '0'
							".$insUpdtInjectionOpWhereQry;
		$setInjectionOpnoteRes=imw_query($setInjectionOpnoteQry) or die($setInjectionOpnoteQry.imw_error());
		imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
		include("common/conDb.php");  //SURGERYCENTER CONNECTION
	}else {

		$fileOpen    = fopen('new_html2pdf/pdffile.html','w+');
		$filePut     = fputs($fileOpen,$table_printh);
		fclose($fileOpen);
?>
        <table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
            <tr>
                <td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
            </tr>
        </table>
        <form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">
        </form> 
        <script type="text/javascript">
            submitfn();
        </script>
<?php
	}
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	

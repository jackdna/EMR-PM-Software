<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include_once("common/conDb.php");
?>
<html>
	<head>
		<title>Surgerycenter EMR</title>
        
	</head>
	<?php
		include_once("common/commonFunctions.php");
		include_once("admin/classObjectFunction.php");
		$objManageData 					= new manageData;
		$thisId 						= $_REQUEST['thisId'];
		$innerKey 						= $_REQUEST['innerKey'];
		$preColor 						= $_REQUEST['preColor'];
		$uid							= $_SESSION['loginUserId'];
		$patient_id 					= $_REQUEST['patient_id'];
		$pConfId 						= $_REQUEST['pConfId'];
		if(!$pConfId) {$pConfId 		= $_SESSION['pConfId'];  }
		if(!$patient_id) {$patient_id 	= $_SESSION['patient_id'];  }
		$ascId 							= $_SESSION['ascId'];
		$check_list_id 					= $_REQUEST['check_list_id'];
		$usertype			= $_SESSION['loginUserType'];

	
		if($check_list_id){
			$getCheckListDetail = $objManageData->getExtractRecord('surgical_check_list', 'check_list_id', $check_list_id);
		}else if($pConfId){
			$getCheckListDetail = $objManageData->getExtractRecord('surgical_check_list', 'confirmation_id', $pConfId);	
		}
		if(is_array($getCheckListDetail)){
			extract($getCheckListDetail);
		}
		$procedure_check_in_dbNurseName="________";
		
		include("new_header_print.php");
		
		if($checklist_old_new == 'old')
		{
			include 'check_list_printpop_previous.php';	
		}
		else
		{
			include 'check_list_printpop_new.php';	
		}
		
		$table_print=$head_table.$table;
		$fp = fopen('new_html2pdf/pdffile.html','w+');
		$intBytes = fputs($fp,$table_print);
		fclose($fp);
		if($form_status=='completed' || $form_status=='not completed') {
	?>
	<script language="javascript">
		function submitfn(){
			document.printFrm.submit();
		}
	</script>
	<table  bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
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
	}else {
		echo "<center>Please verify/save this form before print</center>";
	}
	?>	
	
</html>

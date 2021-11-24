<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("conDb.php");


if($_REQUEST['field']) {
	
	 if($_REQUEST['field']==1)
	 {
		 
			$procCatQry	=	"Select PC.isMisc,isInj From procedures P JOIN procedurescategory PC On P.catId = PC.proceduresCategoryId Where P.procedureId = '".$_REQUEST['id']."' ";
		 	$procCatSql	=	imw_query($procCatQry) or die('Error Found: '.imw_error());
		 	$procCatRes	=	imw_fetch_assoc($procCatSql);
			$isMiscProc	=	'';
			if($$procCatRes['isInj'])			$isMiscProc	=	'injection';
			elseif($procCatRes['isMisc'])	$isMiscProc	=	'misc';
		 	
			$query1 = "update stub_tbl set patient_primary_procedure = '$_REQUEST[text]' where stub_id = '$_REQUEST[stub_id]'";
		 	$query2 = "update patientconfirmation set patient_primary_procedure = '$_REQUEST[text]',
				 patient_primary_procedure_id= '$_REQUEST[id]' where patientConfirmationId = '$_REQUEST[pConfId]'"; 
			$query3 = "update patientconfirmation set prim_proc_is_misc = '".$isMiscProc."' where patientConfirmationId = '$_REQUEST[pConfId]' And prim_proc_is_misc <> '' "; 	 
			
	}
	else
	{
		$query1 = "update stub_tbl set patient_secondary_procedure = '$_REQUEST[text]' where stub_id = '$_REQUEST[stub_id]'";
		$query2 = "update patientconfirmation set patient_secondary_procedure = '$_REQUEST[text]',
				 patient_secondary_procedure_id= '$_REQUEST[id]' where patientConfirmationId = '$_REQUEST[pConfId]'"; 
			
	}
	if($query1)imw_query($query1);
	if($query2)imw_query($query2);
	if($query3)imw_query($query3);
}
?>
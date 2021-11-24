<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$sxProcQry = "SELECT pdt.imwPatientId,pc.dos,pc.site,pr.name as proc_name, opr.manufacture, opr.lensBrand, opr.model, opr.Diopter 
				FROM patientconfirmation pc
				LEFT JOIN patient_data_tbl pdt ON (pdt.patient_id = pc.patientId)
				LEFT JOIN procedures pr ON (pr.procedureId = pc.patient_primary_procedure_id)
				LEFT JOIN operatingroomrecords opr ON (opr.confirmation_id = pc.patientConfirmationId)
				WHERE pc.patient_primary_procedure_id !='0' AND pc.patientConfirmationId = '".$_REQUEST["pConfId"]."'";
$sxProcRes = imw_query($sxProcQry) or die($sxProcQry.imw_error());
if(@imw_num_rows($sxProcRes)>0){
	$sxProcRow 			= imw_fetch_assoc($sxProcRes);
	$sxImwPatientId	= $sxProcRow["imwPatientId"];
	$sxDOS				= $sxProcRow["dos"];
	$sxSite				= $sxProcRow["site"];
	$sxMan 				= $sxProcRow["manufacture"];
	$sxLensBrand 		= $sxProcRow["lensBrand"];
	$sxModel 			= $sxProcRow["model"];
	$sxDiopter 			= $sxProcRow["Diopter"];
	$sxComments 		= "";
	if($sxMan) 			{$sxComments.="Manufacturer:".addslashes($sxMan).'\n';	}
	if($sxLensBrand) 	{$sxComments.="LensBrand:".addslashes($sxLensBrand).'\n';	}
	if($sxModel) 		{$sxComments.="Model:".addslashes($sxModel).'\n';			}
	if($sxDiopter) 		{$sxComments.="Diopter:".addslashes($sxDiopter).'\n';		}
	imw_close($link); //CLOSE SURGERYCENTER CONNECTION
	include('connect_imwemr.php'); // imwemr connection
	$chkListQry 		= "SELECT id FROM lists WHERE type = '6' AND allergy_status	= 'Active' AND begdate = '".$sxDOS."' AND scemr_confirmation_id = '".$_REQUEST["pConfId"]."' AND pid = '".$sxImwPatientId."' AND pid !='0' ";
	$chkListRes 		= imw_query($chkListQry) or die($chkListQry.imw_error());
	$saveListQry 		= " INSERT INTO ";
	$saveListWhrQry 	= "";
	if(@imw_num_rows($chkListRes)>0){
		$saveListQry 	= " UPDATE ";
		$saveListWhrQry = " WHERE scemr_confirmation_id = '".$_REQUEST["pConfId"]."' ";
	}
	$sxSiteQry = "";
	if($sxSite <=3) {
		$sxSiteQry = " sites = '".$sxSite."', ";	
	}
	$saveListQry 		.= " lists SET 
								title 					= '".addslashes($sxProcRow["proc_name"])."',
								date					= '".date("Y-m-d H:i:s")."',
								type 					= '6',
								begdate					= '".$sxDOS."',
								pid 					= '".$sxImwPatientId."',
								allergy_status			= 'Active',
								comments				= '".$sxComments."',
								$sxSiteQry
								scemr_confirmation_id 	= '".$_REQUEST["pConfId"]."'
								".$saveListWhrQry;
	$saveListRes 		= imw_query($saveListQry) or die($saveListQry.imw_error());
	imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
	include("common/conDb.php");  //SURGERYCENTER CONNECTION
}
?>
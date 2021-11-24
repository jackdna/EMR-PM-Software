<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");

$objManageData = new manageData;

$indexStart	=	isset($_REQUEST["c"])	?	$_REQUEST["c"]	:	0	;
$totalCount =	isset($_REQUEST['cn'])	?	$_REQUEST['cn']	:	''	;	


if(empty($totalCount))
{
	$qryCnt = "SELECT count( PC.patientConfirmationId ) AS totalConfIds FROM `patientconfirmation` PC 
						Join `operatingroomrecords`  ORR 
						On PC.patientConfirmationId = ORR.confirmation_id 
						WHERE (ORR.form_status = 'not completed' || ORR.form_status = 'completed')
						ORDER BY PC.patientConfirmationId ASC";
	
	$resCnt 	= imw_query($qryCnt) or die(imw_error().$qryCnt);
	$res		= imw_fetch_object($resCnt);
	$totalCount = $res->totalConfIds;
	
	//echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?cn=".$totalCount."');\">Click Here for Confirmation to run this update</a>";
	//exit();
	
}

if($totalCount > 0 )
{
	$fetchRecords	=	500;
	$array	=	array(
					'AmviscPlus'=>'Amvisc Plus',
					'Discovisc'=>'Duovisc',
					'Healon'=>'Healon',
					'Healon5'=>'Healon5',
					'HealonGV'=>'HealonGV',
					'Miochol'=>'Miochol',
					'Miostat'=>'Miostat',
					'Occucoat'=>'Occucoat',
					'Provisc'=>'Provisc',
					'TrypanBlue'=>'Trypan Blue',
					'Viscoat'=>'Viscoat',
					'XylocaineMPF'=>'Xylocaine MPF 1%');
					
	$fields	=	'ORR.Healon, ORR.Occucoat, ORR.Provisc, ORR.Miostat, ORR.HealonGV, ORR.Discovisc, ORR.AmviscPlus, ORR.TrypanBlue, ORR.Healon5, ORR.Viscoat, ORR.Miochol, ORR.XylocaineMPF, ORR.HealonList, ORR.OccucoatList, ORR.ProviscList, ORR.MiostatList, ORR.HealonGVList, ORR.DiscoviscList, ORR.AmviscPlusList, ORR.Healon5List, ORR.ViscoatList, ORR.MiocholList ';
	
	$qry = "SELECT PC.patientConfirmationId AS PatientConfirmationId, ".$fields." FROM `patientconfirmation` PC 
						Join `operatingroomrecords`  ORR 
						On PC.patientConfirmationId = ORR.confirmation_id 
						WHERE (ORR.form_status = 'not completed' || ORR.form_status = 'completed')
						ORDER BY PC.patientConfirmationId ASC
						Limit ".$indexStart.", ".$fetchRecords." "; 
	
	$tblIns	=	'operatingroomrecords_supplies';
	
	$res = imw_query($qry) or die(imw_error().$qry);
	
	if(imw_num_rows($res)>0)
	{
		
		while($row	=	imw_fetch_object($res))
		{
			$Pci	=	$row->PatientConfirmationId;
			
			foreach($array as $Key=>$Supply)
			{
				$Chk	=	($row->$Key == 'Yes') ? 1 : 0 ;	
				$List	=	$Key.'List';
				$List	=	($row->$List) ? $row->$List : '';
				$Qty	=	($Key <> 'TrypanBlue' && $Key <> 'XylocaineMPF') ? 1 : 0;
				
				
				$chkQry	=	"Select suppRecordId  From ".$tblIns." Where suppName = '".$Supply."' And confirmation_id = ".$Pci." ";
				$chkSql	=	imw_query($chkQry) or die(imw_error());
				$chkNum	=	imw_num_rows($chkSql);
				if($chkNum > 0) {
					
					$data	=	array(
								'suppChkStatus'=>$Chk,
								'suppList'=>$List,
								'displayStatus'=>1
								);
								
					$chkRes		=	imw_fetch_object($chkSql);
					$recordId	=	$chkRes->suppRecordId ;
					
					$objManageData->UpdateRecord($data,$tblIns,'suppRecordId',$recordId);
				}
				else
				{
					
					$data	=	array(
								'suppName'=>$Supply,
								'suppQtyDisplay'=>$Qty,
								'suppChkStatus'=>$Chk,
								'suppList'=>$List,
								'templateId'=>0,
								'confirmation_id'=>$Pci,
								'displayStatus'=>1
								);
					$objManageData->addRecords($data,$tblIns);				
				}
				
			}
			
			$indexStart++;
		
			echo "<br>Process Done ".$indexStart." of ".$totalCount.'<br>';
			echo '<br><br>';	
		}
		
		echo "<script>window.location.replace('?c=".$indexStart."&cn=".$totalCount."');</script >";
		exit;
		
	}
	
	else
	{
		echo "<br>Process Completed with ".$indexStart." updated record(s)";	
	}
	
	
	
}
else {
	echo "<br>Process Completed with ".$indexStart." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){ $color = 'red'; }
$msg_info[] = "Update Implementing template base supplies for previous record - run OK";

?>

<html>
<head>
<title>Update - Implementing Supplies for Previous Records</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>
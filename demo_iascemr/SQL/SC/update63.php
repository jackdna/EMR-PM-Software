<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include('../../connect_imwemr.php'); // imwemr connection
if(!$_GET['check'])
{
	if(imw_num_rows(imw_query("SHOW TABLES LIKE 'procedureinfo_BAK'"))>=1){die("Update already ran before");}else{$_GET['check']='done';}
	//create a copy of a table procedureinfo
	imw_query("CREATE TABLE procedureinfo_BAK AS (SELECT * FROM procedureinfo)");
	//create a copy of a table superbill
	imw_query("CREATE TABLE superbill_BAK AS (SELECT * FROM superbill)");
}

include("../../common/conDb.php");
$icd10_code='';
$qryCnt = "select DS.icd10_code, PC.dos, PC.ascId, PC.nextGenPersonId from dischargesummarysheet as DS join patientconfirmation as PC ON DS.confirmation_id=PC.patientConfirmationId 
			where PC.dos>='2015-10-01' and DS.icd10_code<>'' AND PC.ascId<>0 and DS.form_status='completed'";

$tb=$_GET["st"];

if(empty($tb)){
	$tb=$_GET["st"]=0;
}
		
$tCnt = $_GET['tCnt'];
if(empty($tCnt)){
	$resCnt = imw_query($qryCnt) or die(imw_error().$qryCnt);
	$tCnt = imw_num_rows($resCnt);
}

if($tb<=$tCnt)
{
	//get record for dos less than equal to 1 st oct = discharge smmary= patient confirmation
	$query1=$qryCnt."  LIMIT $tb,1";
	$data1=imw_query($query1)or die(imw_error().' '.$query1);
	$data1Rs=imw_fetch_object($data1);
	$icd10_code=$data1Rs->icd10_code;
	if($data1Rs->ascId)
	{
		//connect to idoc and check aganist asc_id in superbill psted 0 
		include('../../connect_imwemr.php'); // imwemr connection
		$query2="select idSuperBill from superbill where ascId= ".$data1Rs->ascId." and patientId= ".$data1Rs->nextGenPersonId." and postedStatus=0";
		$data2=imw_query($query2)or die(imw_error().' '.$query2);
		$data2Rs=imw_fetch_object($data2);
		if($data2Rs->idSuperBill)
		{
			//check in superbill procedure aganisht bill id and dx =''
			$query3="select id from procedureinfo where idSuperBill=$data2Rs->idSuperBill and (dx1<>'' || dx2<>'' || dx3<>'' || dx4<>'' || dx5<>'' || dx6<>'' || dx7<>'' || dx8<>'' || dx9<>'' || dx10<>'' || dx11<>'' || dx12<>'')";
			$data3=imw_query($query3)or die(imw_error().' '.$query3);
			if(imw_num_rows($data3)==0)
			{
				$diag_code_arr=explode(',',$icd10_code);
				$dx1=$diag_code_arr[0];
				$dx2=$diag_code_arr[1];
				$dx3=$diag_code_arr[2];
				$dx4=$diag_code_arr[3];
				$dx5=$diag_code_arr[4];
				$dx6=$diag_code_arr[5];
				$dx7=$diag_code_arr[6];
				$dx8=$diag_code_arr[7];
				$dx9=$diag_code_arr[8];
				$dx10=$diag_code_arr[9];
				$dx11=$diag_code_arr[10];
				$dx12=$diag_code_arr[11];

				$query4="select id from procedureinfo where idSuperBill=$data2Rs->idSuperBill order by porder ASC limit 0,1";
				$data4=imw_query($query4)or die(imw_error().' '.$query4);
				$data4Rs=imw_fetch_object($data4);
				if($data4Rs->id)
				{
					//update dx codes
					$updateQ1="update procedureinfo set dx1='".$dx1."',dx2='".$dx2."',dx3='".$dx3."',dx4='".$dx4."',dx5='".$dx5."',
							dx6='".$dx6."',dx7='".$dx7."',dx8='".$dx8."',dx9='".$dx9."',dx10='".$dx10."',
							dx11='".$dx11."',dx12='".$dx12."' where id=$data4Rs->id";
					imw_query($updateQ1)or die(imw_error().' '.$updateQ1);
					//update dx code type 
					$arrDxCodes=array();
					$str_dx_codes="";
					for($i=1;$i<=12;$i++){
						$ic=$i-1;
						$arrDxCodes[$i]=$diag_code_arr[$ic];
					}
					$str_dx_codes=serialize($arrDxCodes);
					$updateQ2="update superbill set sup_icd10=1, arr_dx_codes='".imw_real_escape_string($str_dx_codes)."' where idSuperBill=$data2Rs->idSuperBill";
					imw_query($updateQ2)or die(imw_error().' '.$updateQ2);
				}
			}
		}
	}
	
	echo "<br>Process Done ".$_GET["st"]." of ".$tCnt;
	
	$_GET["st"]=$_GET["st"]+1;	
	
	echo "<script>window.location.replace('?st=".$_GET["st"]."&tCnt=".$tCnt."&check=".$_GET['check']."');</script>";
	exit();
	
}else {
	$_GET["st"]=$_GET["st"]-1;	
	echo "<br>Process Completed with ".$_GET["st"]." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update DX Code IN iDoc run OK";

?>

<html>
<head>
<title>Update DX Code IN iDoc</title>
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
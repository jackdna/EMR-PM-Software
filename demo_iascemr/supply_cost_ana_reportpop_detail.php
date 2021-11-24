<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
if($_REQUEST['action']!='csv') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}

set_time_limit(500);
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

// query part to exclude surgeons in vcna reports
$usersExclude =	'';
$surgeonExclude = '';
if( constant('VCNA_SURGEON_EXCLUDE') )
{
	$usersExclude = 'And usersId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').') ';	
	$surgeonExclude = 'And pc.surgeonId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').')';	
}

//get detail for logged in facility
$queryFac	=	imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac	=	imw_fetch_object($queryFac);
$name		=	stripslashes($dataFac->fac_name);
$address	=	stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

//create array for procedure acro
$proc_q=imw_query("select procedureAlias, procedureId, name from procedures");
while($proc_d=imw_fetch_object($proc_q))
{
	$proc_alias[$proc_d->procedureId]=($proc_d->procedureAlias)?$proc_d->procedureAlias:$proc_d->name;
}
	
function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 
	if(file_exists($fileName))
	{ 
		$img_size=getimagesize('new_html2pdf/white.jpg');
		 $width=$img_size[0];
		 $height=$img_size[1];
		 $filename;
		do
		{
			if($width > $targetWidth)
			{
				 $width=$targetWidth;
				 $percent=$img_size[0]/$width;
				 $height=$img_size[1]/$percent; 
			}
			if($height > $targetHeight)
			{
				$height=$targetHeight;
				$percent=$img_size[1]/$height;
				$width=$img_size[0]/$percent; 
			}
	
		}while($width > $targetWidth || $height > $targetHeight);
	
		$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
		$returnArr[] = $width;
		$returnArr[] = $height;
		return $returnArr; 
	} 
	return "";
}	
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry) or die($SurgeryQry.imw_error());
while($SurgeryRecord=imw_fetch_array($SurgeryRes))
{
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
}
$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
imagejpeg($bakImgResource,'new_html2pdf/white.jpg');
$size=getimagesize('new_html2pdf/white.jpg');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='new_html2pdf/white.jpg';
			
// end set surgerycenter detail  
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;

$superBillProcIdArr = $objManageData->superBillProcIdArrFun();	
if($_REQUEST['proc_save']=='yes') {
	$date1 = trim($_REQUEST["date1"]);
	$date2 = trim($_REQUEST["date2"]);
	
	$from_date 	= $objManageData->changeDateYMD($date1);
	$to_date 	= $objManageData->changeDateYMD($date2);
	
	$tmpReqProcArr 	= array_values(array_unique(array_filter(explode(",",$_REQUEST["procedure"]))));
	$procedure_id = implode(",",$tmpReqProcArr);
	
	$physicianImp = $_REQUEST["physician"];
	//$physicianImp = implode(",",$physicianArr);
	
	if(!$procedure_id) {  $procedure_id = '0';}
	if(!$physicianImp) {  $physicianImp = '0';}
	else
	{
		if($physicianImp!='all'){
		$physicianQry=" AND pc.surgeonId IN ($physicianImp)";}
		
		if($physicianImp =='all'){
			$physicianQry = $surgeonExclude;
		}
	}
	
	if(constant('STRING_SEARCH')=='YES')
	{	$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
	}else{
		$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
	}
	
	$procIdQry = "";
	if($procedure_id!='all') {
		if(constant('STRING_SEARCH')=='YES')
		{
			$procedure_tbl=imw_query("select procedureId, name from procedures where procedureId IN(".$procedure_id.") ");
			$procedure_name = array();
			while($proc=imw_fetch_array($procedure_tbl)){
				array_push($procedure_name,"'".$proc['name']."'");
			}
			$procNameImplode = implode(",",$procedure_name);
			$procIdQry = " AND pc.cost_procedure_name IN(".$procNameImplode.") ";	
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
		}else{
			$procIdQry = " AND pc.cost_procedure_id IN(".$procedure_id.") ";	 
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
		}
			
	}
	
	$table='';
	$qry = "SELECT pc.patientConfirmationId,pc.dos, pc.cost_procedure_name as proc_name,
			CONCAT(pdt.patient_lname,', ',pdt.patient_fname,' ',pdt.patient_mname) AS patient_name,
			sc.item_type, sc.item_name, sc.item_cost, sc.item_qty, sc.item_total_cost,pc.supply_cost,
			CONCAT(users.lname,', ',users.fname,' ',users.mname) AS user_name,
			pc.cost_procedure_id as proc_id,users.usersId
			FROM patientconfirmation pc
			INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
			INNER JOIN dischargesummarysheet ds ON(ds.confirmation_id=pc.patientConfirmationId AND (ds.form_status='completed' OR ds.form_status='not completed'))
			INNER JOIN surgery_cost sc on (sc.confirmation_id=pc.patientConfirmationId AND sc.item_type !='Labor')
			LEFT JOIN patient_data_tbl pdt ON(pc.patientId = pdt.patient_id)
			LEFT JOIN users ON(users.usersId = pc.surgeonId)
			$proc_JOIN
			WHERE (pc.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
			AND pc.finalize_status='true'
			AND pc.supply_cost>0 ".$physicianQry."
			ORDER BY users.usersId ASC,pc.dos ASC, pc.surgery_time ASC ";		
	$res = imw_query($qry)or die(imw_error().' ----- ');
	$recExist=false;
	$numRow = imw_num_rows($res);
	if($numRow>0) {
	$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
	
$pgHeaderFooter = '
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		
		<page_header>
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0" >
				<tr >
					<td  class="text_16b color_white" width="700" style="background-color:#cd532f; padding-left:5px; "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
					 </td>
					<td style="background-color:#cd532f; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				 </tr>
		
				<tr height="22" bgcolor="#F1F4F0">
					<td align="right" colspan="2" class="text_16b">Surgery Center Supply Cost Report Detail</td>
					
				</tr>	
				<tr >
					<td colspan="2">&nbsp;</td>
				</tr>				
		</table>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" id="headerTable">		
			<tr  valign="top">
				<td align="left"   class="text_b BdrAll" style="width:30px;">S.No</td>
				<td align="left"   class="text_b BdrTBR " style="width:200px;">Patient Name</td>
				<td align="left"   class="text_b BdrTBR " style="width:150px;">Procedure Name</td>
				<td align="left"   class="text_b BdrTBR " style="width:100px;">Item Type</td>
				<td align="left"   class="text_b BdrTBR " style="width:100px;">Item Name</td>
				<td align="left"   class="text_b BdrTBR " style="width:80px;">Item Cost</td>
				<td align="left"   class="text_b BdrTBR " style="width:80px;">Item Qty</td>
				<td align="left"   class="text_b BdrTBR " style="width:80px;">Item Total</td>
				<td align="left"   class="text_b BdrTBR " style="width:100px;">Supply Cost</td>
			</tr>
		</table>
		
		</page_header>
		';	
	
	$table.='
			<style>
				.BdrAll { 
					border:solid 1px #999; 
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;	
				}
				.BdrTBR { 
					border:solid 1px #999; border-left: solid 0px #fff;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrLBR { 
					border:solid 1px #999; border-top: solid 0px #fff;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrBR {
					border-bottom: solid 1px #999;
					border-right: solid 1px #999;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrR {
					border-right: solid 1px #999;
					padding-top:2px; padding-bottom:2px;  padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrB {
					border-bottom: solid 1px #999;
					padding-top:2px; padding-bottom:2px;  padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				
				.tb_heading{ 
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:16px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
				}
				.text_16b{
					font-size:16px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
				}
				.color_white{
					color:#FFFFFF;
				}
				.text{
					font-size:14px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
				.orangeFace{
					color:#FE8944;
				}
				.text_15 {
					font-size:15px;
					font-family:Arial, Helvetica, sans-serif;
				}
				.text_18 {
					font-size:18px;
					font-family:Arial, Helvetica, sans-serif;
					
				}
				
			
			</style>
			<page backtop="38mm" backbottom="15mm">'.$pgHeaderFooter.'
				<table width="100%" border="0" cellpadding="0" cellspacing="0" id="bodyTable">			
				';		
		//get total cost of procedure for each surgeon
		$dosArr = $dosSrgnArr = $dosSrgnPtArr = array();
		$tmpUsrIdArr = array();
		$pConfIdArr = array();
		$k=0;$b=0;
		while($row = imw_fetch_assoc($res)){
			
			$recExist=false;
			$pConfId = $row["patientConfirmationId"];
			if(count(array_intersect($superBillProcIdArr[$pConfId], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfId]) == count($tmpReqProcArr)){
				$recExist = true;
				$pConfIdArr[] = $pConfId;
			}
			
			if( !$recExist ) continue;
			
			$tmpPatientProcNames = '';
			foreach( $superBillProcIdArr[$pConfId] as $tmpProc)
			{
				$tmpPatientProcNames .= $proc_alias[$tmpProc].'<br>';
			}
				
			$patientConfirmationId 	= $row["patientConfirmationId"];
			$dos 					= $row["dos"];
			$sur_name 				= trim(stripslashes($row["user_name"]));
			$patient_name 		= trim(stripslashes($row["patient_name"]));
			$proc_name 				= trim($tmpPatientProcNames);
			$item_type 				= $row["item_type"];
			$item_name 				= $row["item_name"];
			$item_cost 				= $row["item_cost"];
			$item_qty 				= $row["item_qty"];
			$item_total_cost 		= $row["item_total_cost"];
			$supply_cost 			= $row["supply_cost"];
			if(!in_array($dos,$dosArr[$dos])) {
				if($b > 0) {
				$table.='	
				</table></page><page backtop="38mm" backbottom="15mm">'.$pgHeaderFooter.'
				<table width="100%" border="0" cellpadding="0" cellspacing="0" id="bodyTable">			
					';
				}
				$dosArr[$dos][] = $dos;
				$dosShow = date("m-d-Y",strtotime($dos));
				$table.='	
				<tr  valign="top">
					<td align="left" colspan="9"   class="text BdrAll" >DOS '.$dosShow.'</td>
				</tr>
					';
				$b++;	
			}
			if(!in_array($sur_name,$dosSrgnArr[$dos][$sur_name])) {
				$k=0;
				$dosSrgnArr[$dos][$sur_name][] = $sur_name;
				$table.='	
				<tr  valign="top">
					<td align="left" colspan="9"   class="text_b BdrAll" >Surgeon Dr. '.$sur_name.'</td>
				</tr>
					';
			}
			$a = $k;
			if(!in_array($patient_name,$dosSrgnPtArr[$dos][$sur_name][$patient_name])) {
					$dosSrgnPtArr[$dos][$sur_name][$patient_name][] = $patient_name;
					$k++;
					$a = $k;
			}else {
				$a = $patient_name = $proc_name = $supply_cost = '';	
			}
			
			$table.='	
			<tr  valign="top">
				<td align="left"   class="text BdrAll " style="width:30px;">'.$a.'</td>
				<td align="left"   class="text BdrTBR " style="width:200px;">'.$patient_name.'</td>
				<td align="left"   class="text BdrTBR " style="width:150px;">'.$proc_name.'</td>
				<td align="left"   class="text BdrTBR " style="width:100px;">'.$item_type.'</td>
				<td align="left"   class="text BdrTBR " style="width:100px;">'.$item_name.'</td>
				<td align="left"   class="text BdrTBR " style="width:80px;">'.$item_cost.'</td>
				<td align="left"   class="text BdrTBR " style="width:80px;">'.$item_qty.'</td>
				<td align="left"   class="text BdrTBR " style="width:80px;">'.$item_total_cost.'</td>
				<td align="left"   class="text BdrTBR " style="width:100px;">'.$supply_cost.'</td>
			</tr>';	
		}
		$table .= '</table></page>';		
	}
}
if($_REQUEST['action']=='csv' && $numRow>0) {
	/*
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/supply_cost_ana_reportpop_detail.csv';
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	fwrite($fpH1, $csv_content1."\n");
	fwrite($fpH1, $csv_content2."\n\r");
	fwrite($fpH1, $csv_content."\n\r");
	$objManageData->download_file($file_name);
	fclose($fpH1);
	exit;
	*/
} else {
	$htmlFileName = 'pdffile'.$_SESSION['loginUserId'];
	$fileOpen = fopen('new_html2pdf/'.$htmlFileName.'.html','w+');
	$intBytes = fputs($fileOpen,$table);
	//echo $table;die;
	fclose($fileOpen);
}
	

?>

<html>
<head>
<meta charset="utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<title>Supplies Cost Analysis Report</title>    
<script language="javascript">
	function submitfn(){
		document.printFrm.submit();
	}
</script>
</head>
<body >
    <form name="printFrm" action="new_html2pdf/createPdf.php?op=l" method="post">
    	<input type="hidden" name="htmlFileName" value="<?php echo $htmlFileName;?>">
    </form>
    <?php 
    if($numRow > 0){?>		
        <script type="text/javascript">
            window.focus();
            submitfn();
        </script>
    <?php 
    }else {?>
        <script>
            if(document.getElementById("loader_tbl")) {
                document.getElementById("loader_tbl").style.display = "none";	
            }
        </script>	
        <!--<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
            <tr>
                <td style="width:100%; vertical-align:text-top; text-align:center"><b>No Record Found</b></td> 
            </tr>
        </table>-->
    <?php		
    }?>
</body>
</html>

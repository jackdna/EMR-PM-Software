<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php"); 
$facQry = "";
if(trim($_SESSION['iolink_iasc_facility_id'])) {
	$facQry = " AND piwt.iasc_facility_id IN (".$_SESSION['iolink_iasc_facility_id'].") ";	
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<script type="text/javascript">	
	window.focus();
	function selPdfpid(pid,title,iterfname,itermname,iterlname,itersuffix,status,dob_format,sex,street,zip,city,state,ss,phone_home,phone_biz,phone_cell,dos_format,patient_in_waiting_id,objId) 
	{			
		if(opener.closed)
		{
			alert('The destination form was closed, action can not be performed on your selection.');
		}else if(!dos_format) {
			alert('Please make future appointment to select this patient');
			return
		}
		else
		{		
			var hidd_objId = 'hidd_'+objId;
			var ptNme = iterlname+', '+iterfname+' - '+dos_format;
			var ptId_wtId = pid+'-'+patient_in_waiting_id;
			if(opener.document.getElementById(objId)) {
				opener.document.getElementById(objId).value = ptNme;
			}
			if(opener.document.getElementById(hidd_objId)) {
				opener.document.getElementById(hidd_objId).value = ptId_wtId;
			}
			window.close();
			opener.window.focus();
		}
	}
</script>

<?php
$spec= "
</head>
<body>";
include("common/link_new_file.php"); 
include_once("common/functions.php");
include_once("admin/classObjectFunction.php");
include("common/iOLinkCommonFunction.php");

$objId 		= $_REQUEST['objId'];
$txtSearchTemp 	= trim($_REQUEST["txtSearch"]);
$txtSearchSplit = explode('-',$txtSearchTemp);

$txtSearch 	= trim($txtSearchSplit[0]);
if($txtSearch <>"") {
	$andPdQry = "pdt.patient_id!='0'";
	if(!is_numeric($txtSearch)){
		if(strpos($txtSearch, ',')){
			$searchKeywordArr = explode(",", $txtSearch);
			$patientLastName = trim($searchKeywordArr[0]);
			$patientFirstName = trim($searchKeywordArr[1]);
			$andPdQry .= " AND pdt.patient_fname LIKE '".$patientFirstName."%' AND pdt.patient_lname LIKE '".$patientLastName."%'";
		}else{
			$andPdQry .= " AND (pdt.patient_fname LIKE '".$txtSearch."%' OR pdt.patient_lname LIKE '".$txtSearch."%')";
		}
	}else if(is_numeric($txtSearch)) {
			$andPdQry .= " AND pdt.patient_id = '".$txtSearch."%'";
	}
	/*
	$pdQry = "SELECT pdt.* , DATE_FORMAT(date_of_birth,'%m-%d-%Y') AS dobShow,DATE_FORMAT(piwt.dos,'%m-%d-%Y') AS dosShow,piwt.patient_in_waiting_id FROM patient_data_tbl pdt, patient_in_waiting_tbl piwt 
							WHERE pdt.patient_id 				=  piwt.patient_id
							".$andPdQry."
							AND piwt.dos 						>=  DATE_FORMAT(now(),'%Y-%m-%d')
							AND piwt.patient_status 			!=  'Canceled'
							ORDER BY pdt.patient_lname , pdt.patient_fname"; 
	*/
	$pdQry = "SELECT pdt.* , DATE_FORMAT(date_of_birth,'%m-%d-%Y') AS dobShow,DATE_FORMAT(piwt.dos,'%m-%d-%Y') AS dosShow,piwt.patient_in_waiting_id FROM patient_data_tbl pdt 
			LEFT JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id			=	pdt.patient_id 
			AND piwt.dos 				>=  DATE_FORMAT(now(),'%Y-%m-%d')
			AND piwt.patient_status	!=  'Canceled'
			$facQry )
			WHERE ".$andPdQry."
			ORDER BY pdt.patient_lname , pdt.patient_fname"; 
	
	$pdRes = imw_query($pdQry) or die(imw_error());
	$pat_data = '';
	if(imw_num_rows($pdRes)>0) {
		while($pdRow = imw_fetch_array($pdRes)) {
			$patient_in_waiting_id 	= $pdRow['patient_in_waiting_id'];
			$pat_id 				= $pdRow['patient_id'];
			$title 					= $pdRow['title'];
			$iterfname 				= $pdRow['patient_fname'];
			$itermname 				= $pdRow['patient_mname'];
			$iterlname 				= $pdRow['patient_lname'];
			$itersuffix 			= $pdRow['patient_suffix'];
			$status 				= '';
			$dob_format 			= $pdRow['dobShow'];
			$dos_format 			= $pdRow['dosShow'];
			$gender_info 			= $pdRow['sex'];
			
			$genderShow 			= '';
			if($gender_info=='m') 		{ $genderShow='Male'; 
			}else if($gender_info=='f') { $genderShow='Female'; 
			}

			$street 				= $pdRow['street1'];
			$zip 					= $pdRow['zip'];
			$city 					= $pdRow['city'];
			$state 					= $pdRow['state'];
			$ssn 					= '';
			$phone_home 			= core_phone_format($pdRow['homePhone']);
			$phone_biz				= core_phone_format($pdRow['workPhone']);
			$phone_cell 			= '';
			$patientname 			= $iterlname.', '.$iterfname;
			$anchor 				= "<a onClick=\"selPdfpid('$pat_id','$title','".addslashes($iterfname)."','".addslashes($itermname)."','".addslashes($iterlname)."','".addslashes($itersuffix)."','$status','$dob_format','$gender_info','".addslashes($street)."','$zip','".addslashes($city)."','$state','','$phone_home','$phone_biz','$phone_cell','$dos_format','$patient_in_waiting_id','$objId');\" href =\"javascript:void(0)\" class=\"link_home\" >";
			
			$pat_data .= <<<DATA
				<tr class="text1 alignLeft valignMiddle" style="cursor:pointer; background-color:#FFFFFF;"  >
					<td class="nowrap" style="border:1px solid; border-color:#CCCCCC;">$anchor $patientname </a></td>
					<td class="nowrap" style="border:1px solid; border-color:#CCCCCC;">$anchor $dob_format </a></td>
					<td style="border:1px solid; border-color:#CCCCCC;">$anchor $street </a></td>
					<td style="border:1px solid; border-color:#CCCCCC;">$anchor $genderShow </a></td>
					<td class="nowrap" style="border:1px solid; border-color:#CCCCCC;">$anchor $dos_format </a></td>
					<td class="nowrap" style="border:1px solid; border-color:#CCCCCC;">$anchor $pat_id </a></td>
				</tr>
				
DATA;
		
		}
	}
}
?>
<table class="table_collapse">
    <tr style="height:20px;" >    
        <td  class="text_10b"  style="padding-left:5px; padding-top:2px;background-image:url(<?php echo $bgHeadingImage;?>);">
            Select Patient
        </td>
    </tr>
</table>
<table class="table_collapse alignCenter" style="border:none;">
   
    <tr>
        <td class="valignTop" style="height:315px;">
            <?php
            if(imw_num_rows($pdRes)>0) {
			?>
			<table class="alignCenter" style="width:100%; padding:2px; ">
                <tr style="cursor:pointer; font-size:12px; background-color:#F8F9F7;border:1px solid; border-color:#CCCCCC;" class="text_homeb alignLeft valignMiddle">
					<td class="nowrap" style="width:20%; height:20px;border:1px solid; border-color:#CCCCCC; padding-left:3px;">Patient Name</td>
					<td class="nowrap" style="width:10%; height:20px;border:1px solid; border-color:#CCCCCC; padding-left:3px;">DOB</td>
					<td style="width:30%; height:20px;border:1px solid; border-color:#CCCCCC; padding-left:3px;" >Address</td>
                    <td style="width:10%; height:20px;border:1px solid; border-color:#CCCCCC; padding-left:3px;" >Gender</td>
					<td class="nowrap" style="width:10%; height:20px;border:1px solid; border-color:#CCCCCC; padding-left:3px;">DOS</td>
					<td class="nowrap" style="width:10%; height:20px;border:1px solid; border-color:#CCCCCC; padding-left:3px;">ID</td>
				</tr>
                <?php
				print $pat_data;
				?>
            </table>
            <?php
			}
			else{
			?>
            	<script type="text/javascript">
					window.close();
				</script>
            <?php
			}
			?>
        </td>
    </tr>
</table>
</body>
</html>
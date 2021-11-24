<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php"); 
include_once("common/functions.php");
include_once("admin/classObjectFunction.php");
include("common/iOLinkCommonFunction.php");
include("common/link_new_file.php"); 

$lastName = trim($_REQUEST["lastName"]);
$firstName = trim($_REQUEST["firstName"]);

if($lastName != '' || $firstName != ''){
	$pdQry = "select pdt.* , DATE_FORMAT(date_of_birth,'%m-%d-%Y') AS dobShow from patient_data_tbl pdt where";
	if($lastName != ''){
		$pdQry .= " pdt.patient_lname like '$lastName%'";
	}
	if($lastName != '' and $firstName != ''){
		$pdQry .= " and ";
	}
	if($firstName != ''){
		$pdQry .= " pdt.patient_fname like '$firstName%'";
	}
	$pdQry .= " order by pdt.patient_lname, pdt.patient_fname";
	
	$pdRes = imw_query($pdQry) or die(imw_error());
	$pat_data = '';
	if(imw_num_rows($pdRes)>0) {
		while($pdRow = imw_fetch_array($pdRes)) {
			$pat_id = $pdRow['patient_id'];
			$title = $pdRow['title'];
			$iterfname = $pdRow['patient_fname'];
			$itermname = $pdRow['patient_mname'];
			$iterlname = $pdRow['patient_lname'];
			$itersuffix = $pdRow['patient_suffix'];
			$status = '';
			$dob_format = $pdRow['dobShow'];
			$gender_info = $pdRow['sex'];
			
			$genderShow = '';
			if($gender_info=='m') 		{ $genderShow='Male'; 
			}else if($gender_info=='f') { $genderShow='Female'; 
			}

			$street = $pdRow['street1'];
			$zip = $pdRow['zip'];
			$city = $pdRow['city'];
			$state = $pdRow['state'];
			$ssn = '';
			$phone_home = core_phone_format($pdRow['homePhone']);
			$phone_biz = core_phone_format($pdRow['workPhone']);
			$phone_cell = '';
			$patientname = $iterlname.', '.$iterfname;
			$anchor = "<a onClick=\"selpid('$pid','$title','".addslashes($iterfname)."','".addslashes($itermname)."','".addslashes($iterlname)."','".addslashes($itersuffix)."','$status','$dob_format','$gender_info','".addslashes($street)."','$zip','".addslashes($city)."','$state','','$phone_home','$phone_biz','$phone_cell');\" href =\"\" class=\"text1 link_home\" >";
			
			$pat_data .= <<<DATA
				<tr style="cursor:pointer; font-size:11px;" class="text1"  align="left" valign="middle"  bgcolor="#FFFFFF">
					<td  nowrap>$anchor $patientname </a></td>
					<td  nowrap>$anchor $dob_format </a></td>
					<td>$anchor $street </a></td>
					<td  nowrap>$anchor $genderShow </a></td>
					<td  nowrap>$anchor $pat_id </a></td>
				</tr>
				
DATA;
		
		}
	}
}
?>
<html>
<head>
<title>iMedic: Select Patient</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<script type="text/javascript">	
	
	function selpid(pid,title,iterfname,itermname,iterlname,itersuffix,status,dob_format,sex,street,zip,city,state,ss,phone_home,phone_biz,phone_cell) 
	{			
		if(opener.closed)
		{
			alert('The destination form was closed, action can not be performed on your selection.');
		}
		else
		{		
			opener.popUpRelationValue(pid,title,iterfname,itermname,iterlname,itersuffix,status,dob_format,sex,street,zip,city,state,ss,phone_home,phone_biz,phone_cell);
			window.close();
		}
	}
</script>
</head>
<body >	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr height="20">    
        <td  background="<?php echo $bgHeadingImage;?>"  class="text_10b"  style="padding-left:5px; padding-top:2px;">
            Select Patient
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
   
    <tr>
        <td height="315" valign="top">
            <?php
            if(imw_num_rows($pdRes)>0) {
			?>
			<table align="center" width="100%" cellpadding="2" cellspacing="0" border="1" bordercolor="#CCCCCC">
                <tr style="cursor:pointer; font-size:12px;" class="text1b" align="left" valign="middle"  bgcolor="#F8F9F7">
					<td width="20%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">Patient Name</td>
					<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">DOB</td>
					<td width="40%" height="20" ><img src="images/tpixel.gif" width="3" height="1">Address</td>
					<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">Gender</a></td>
					<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">ID</a></td>
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
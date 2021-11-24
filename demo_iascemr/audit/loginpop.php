<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../globalsSurgeryCenter.php");
include_once("../admin/classObjectFunction.php");
$objManageData = new manageData;

$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

$get_http_path=$_REQUEST['get_http_path'];
//set surgerycenter detail
			
			$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
			$SurgeryRes= imw_query($SurgeryQry);
			while($SurgeryRecord=imw_fetch_array($SurgeryRes))
			{
		    $nameSur= stripslashes($SurgeryRecord['name']);
			//$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
			$img = $SurgeryRecord['logoName'];
			$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
			}
        //$file=@fopen('../new_html2pdf/white.jpg','w+');
		//@fputs($file,$surgeryCenterLogo);
		$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
		imagejpeg($bakImgResource,'../new_html2pdf/white.jpg');
		
		$size=getimagesize('../new_html2pdf/white.jpg');
	    $hig=$size[1];
	    $wid=$size[0];
$filename='../new_html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 

if(file_exists($fileName))
{ 
$img_size=getimagesize('../new_html2pdf/white.jpg');
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
return $returnArr; } 
return "";
}				
// end set surgerycenter detail 
$provider_type =$_REQUEST['provider_type'];
$provider_type_req = "";
$providerIdArr = array();
$userIdImplode = '0';
$provider_data_arr=explode(",",$_REQUEST['provider']);
if(count($provider_data_arr)>0){
	foreach($provider_data_arr as $prov_ids){
		list($provider_id,$provider_type_req)=explode("@@",$prov_ids);
		$providerIdArr[]=$provider_id;
	}
	$userIdImplode = implode(",",$providerIdArr);
	if($userIdImplode =='all') { $userIdImplode = '0';  }
}
 
$flag='false';
 $date= date("m/d/Y");
 //$userid=$_REQUEST['user'];
 $start_datetxt=$_REQUEST['login'];
 $end_datetxt=$_REQUEST['logout'];
 $andUsrQry = "";
 if($provider_type) {
	$andUsrQry = " AND u.user_type = '".$provider_type."' ";	 
 }
	$query=imw_query("select u.* from users u where u.usersId IN(".$userIdImplode.")".$andUsrQry);
	$username = "";
	$usernameArr = array();
	$numRow = imw_num_rows($query);
	while(@$nameUsr=imw_fetch_array($query))
	{
	 if($numRow ==1) {
	 	$username=stripslashes($nameUsr['lname'].", ".$nameUsr['fname']);
	 }
	 $usernameArr[$nameUsr['usersId']] = stripslashes($nameUsr['lname'].", ".$nameUsr['fname']);
	}
$table='';
$table.='
	<style>
		.tb_heading{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#FE8944;
		}
		.text_b{
			font-size:14px;
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
		.text_14b{
			font-size:14px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
		}
		.text{
			font-size:12px;
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
		.bottomBorder {
			border-bottom-style:solid; border-bottom:2px;padding-top:4px;padding-bottom:4px;
			font-family:Arial, Helvetica, sans-serif;
		}
		.lightBlue {
			border-bottom-style:solid; border-bottom:2px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#EAF4FD;
		}
		.midBlue {
			font-family:Arial, Helvetica, sans-serif;
			background-color:#80AFEF;
		}
		.text_orangeb{
			font-weight:bold;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FFFFFF;
			color:#CB6B43;
		}
		.lightGreen {
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#ECF1EA;
		}
		.lightorange {
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#CB6B43;
		}
		.midorange {
			font-size:18px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FE8944;
		}
		
	</style>
	<page backtop="30mm" backbottom="15mm">			
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>';	
?>
<html>
<head>
	</head>
<?php 	
		if(($userIdImplode) && ($start_datetxt!='') && ($end_datetxt!=''))
		{
		
			$dat1=explode("-",$start_datetxt);
			$dat1[0];
			$dat1[1];
			$dat1[2];
			
			$dat2=explode("-",$end_datetxt);
			$dat2[0];
			$dat2[1];
			$dat2[2];	
			$start_date=$dat1[2].'-'.$dat1[0].'-'.$dat1[1];
			//$end_date=$dat2[2].'-'.$dat2[0].'-'.($dat2[1]+1);
		
			$tomorrow  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
			$end_date =date("Y-m-d",$tomorrow);
			$quTempQry = "SELECT l.*,u.fname,u.lname, u.user_type FROM login_logout_audit_tbl l  
							INNER JOIN users u ON (u.usersId = l.user_id ".$andUsrQry.")
							WHERE l.user_id IN(".$userIdImplode.") 
							AND  ((l.login_date_time between '$start_date' and  '$end_date')
									OR (l.logout_date_time between '$start_date' and '$end_date')
								 )
							ORDER BY l.login_date_time, l.logout_date_time ";
			$qu=imw_query($quTempQry) or die($quTempQry.imw_error());
	}
	
	elseif(($start_datetxt!='') && ($end_datetxt!=''))
		{
		
			$dat1=explode("-",$start_datetxt);
			$dat1[0];
			$dat1[1];
			$dat1[2];
			$dat2=explode("-",$end_datetxt);
			$dat2[0];
			$dat2[1];
			$dat2[2];	
			$start_date=$dat1[2].'-'.$dat1[0].'-'.$dat1[1];
			$end_date=$dat2[2].'-'.$dat2[0].'-'.($dat2[1]+1);
			$tomorrowTemp  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
			$end_date =date("Y:m:d",$tomorrowTemp);
			$quTempQry = "SELECT l.*,u.fname,u.lname,u.user_type FROM login_logout_audit_tbl l
							INNER JOIN users u ON (u.usersId = l.user_id ".$andUsrQry.")
							WHERE ((l.login_date_time between '$start_date' and  '$end_date') 
									OR (l.logout_date_time between '$start_date' and '$end_date')
								  )
							ORDER BY l.login_date_time, l.logout_date_time ";
			$qu=imw_query($quTempQry) or die($quTempQry.imw_error());
	  
	}
	elseif($userIdImplode)
	{
		$quTempQry = "SELECT l.*, u.user_type FROM login_logout_audit_tbl l 
						INNER JOIN users u ON (u.usersId = l.user_id ".$andUsrQry.")
						WHERE l.user_id IN(".$userIdImplode.") 
						ORDER BY l.login_date_time, l.logout_date_time ";
		$qu=imw_query($quTempQry) or die($quTempQry.imw_error());
	}
	if(@imw_num_rows($qu)>0)
	{
 	$i=0;
	while($result=imw_fetch_array($qu))
	{
		foreach($result as $key=>$value)
		{
			$resu[$i][$key]=$value;
		}
		$i++;
	}
	$num=imw_num_rows($qu);
	$rowbreak=32;
	  $numb=ceil($num/$rowbreak);
	$rowstart=0;
	$rowend = 32;
for($rows=0;$rows<$numb;$rows++)
	{
	$tableRec = '';   
	for($j=$rowstart;$j<$rowend;$j++){
	 
	//print'<pre>';print_r($resu);
	$userList=$resu[$j]['lname'].', '.$resu[$j]['fname'];
	$userType=$resu[$j]['user_type'];
	$record2 = $objManageData->getFullDtTmFormat($resu[$j]['login_date_time']);
	$record3='N/A';
	if($resu[$j]['logout_date_time']!="0000-00-00 00:00:00") {
		$record3 = $objManageData->getFullDtTmFormat($resu[$j]['logout_date_time']);
	}

	 if($j<$num && $username=='')
	 {
	 
	$tableRec.='<tr height="22">
	    <td style="width:25%;" align="Left" >'.$userList.'</td>
		<td style="width:20%;" align="Left"  height="28"  >'.$userType.'</td>
		<td style="width:25%;" align="Left" height="28"  >'. $record2 .'</td>
		<td style="width:25%;" align="Left" height="28"  >'. $record3 .'</td>
	</tr>
	';
	}
	elseif($j<$num)
	{
	
	  $tableRec.='<tr height="22">
		<td style="width:25%;" align="Left" height="28"  >'. $record2 .'</td>
		<td style="width:25%;" align="Left" height="28"  >'. $record3 .'</td>
	</tr>
	';
	}
	}
	$rowstart = $rowend;
	$rowend=$rowend + 32;
	$img_logo = showThumbImages('../new_html2pdf/white.jpg',170,50);
 $imgheight= $img_logo[2]+8;
 $imgwidth= $img_logo[1]+8;
	$table.='<page_header>
		<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" >
			<tr height="'.$higinc.'" >
				<td class="text_14b" style="width:290px;background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
				 </td>
				<td class="text_14b" style="width:290px;background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" >Login/Logout Audit Report
				 </td> 
				<td style="background-color:#CD523F;width:120px; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
			</tr>
			<tr height="22"  bgcolor="#F1F4F0">
				<td colspan="3" align="right" valign="top"><b>Date:'.$date.'</b></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</table>	
	
	<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" >';
	
	if($username!='')
	{  
	$table.='<tr height="22"  bgcolor="#F1F4F0">
				<td colspan="2" style="width:25%;"  valign="top" align="left"><b>User Name:</b>'.$username.'</td>
			</tr>
			<tr height="22">
				<td  style="width:25%;" align="Left">Logged-In Date/Time </td>
				<td  style="width:25%;" align="Left">Logged-Out Date/Time </td>
			</tr>';
	}
	else
	{
	$img_logo = showThumbImages('../new_html2pdf/white.jpg',170,50);
 $imgheight= $img_logo[2]+8;
 $imgwidth= $img_logo[1]+8;
	  $table.='
		<tr height="22">
			<td style="width:25%;" align="Left">User Name</td>
			<td  style="width:20%;" align="Left">User Type </td>
			<td  style="width:25%;" align="Left">Logged-In Date/Time </td>
			<td  style="width:25%;" align="Left">Logged-Out Date/Time </td>
		</tr>';
	}
	$table.='</table></page_header>
			<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				'.$tableRec.'
			</table>
		
	';
	if($num>$rowend)
	{
	 //$table.='<newpage>';
	}
	}
	}
	else
	{
	$img_logo = showThumbImages('../new_html2pdf/white.jpg',170,50);
 $imgheight= $img_logo[2]+8;
 $imgwidth= $img_logo[1]+8;
			$flag='true';
			$disp='block';
		  $msg=  "No record found";
	
	$table.='<page_header>
		<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" >
			<tr height="'.$higinc.'" >
				<td class="text_14b" style="width:290px;background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
				 </td>
				<td class="text_14b" style="width:290px;background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" >Login/Logout Audit Report
				 </td> 
				<td style="background-color:#CD523F;width:120px; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
			</tr>
			<tr height="22"  bgcolor="#F1F4F0">
				<td colspan="3" align="right" valign="top"><b>Date:'.$date.'</b></td>
			</tr>
			<tr><td colspan="3" height="100" valign="middle" nowrap="nowrap"  align="center">'.$msg.'</td></tr>
		</table></page_header>	
	';
	}
$table.='</page>';	
$htmlFileName = 'testPdf.html';
$flPth = '../new_html2pdf/'.$htmlFileName;
if(file_exists($flPth)) {
	unlink($flPth);
}
$fileOpen = fopen($flPth,'w+');
$filePut = fputs(fopen($flPth,'w+'),$table);
fclose($fileOpen);
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="../images/ajax-loader.gif"></td> 
	</tr>
</table>
<body onLoad="submitfn();">
<form name="printFrm" action="../new_html2pdf/createPdf.php?op=p&htmlFileName=<?php echo $htmlFileName;?>" method="post">
	</form> 

<!--<form name="printFrm"  action="../html2pdf/index.php?AddPage=P" method="post">
</form>		-->
<script type="text/javascript">
	submitfn();
</script>
</body>
	
	
<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
    <tr height="20" bgcolor="#EAF0F7" valign="top">
      <td align="center">Please wait while data is retrieving from the server.</td>
    </tr>
    <tr height="20" bgcolor="#EAF0F7" valign="top">
      <td align="center"><img src="../images/pdf_load_img.gif"></td> 
    </tr>
</table>
<?php
set_time_limit(900);
session_start();
include("../common/conDb.php"); 
include_once("../admin/classObjectFunction.php");
$objManageData = new manageData;
$get_http_path=$_REQUEST['get_http_path'];

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
		return $returnArr; 
	} 
	return;
}	

//get detail for logged in facility
$queryFac	=	imw_query("select * from facility_tbl where fac_id='".$_SESSION['facility']."'")or die(imw_error());
$dataFac	=	imw_fetch_object($queryFac);
$name		=	stripslashes($dataFac->fac_name);
$address	=	stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

//set surgerycenter detail
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry) or die($SurgeryQry.imw_error());
while($SurgeryRecord=imw_fetch_array($SurgeryRes))
{
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
}

$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
imagejpeg($bakImgResource,'../new_html2pdf/white.jpg');
$size=getimagesize('../new_html2pdf/white.jpg');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='../new_html2pdf/white.jpg';

$img_logo = showThumbImages('../new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;
// end set surgerycenter detail  

$userid=$_REQUEST['user'];
$start_datetxt=$_REQUEST['login'];
$end_datetxt=$_REQUEST['logout'];
$type=$_REQUEST['atype'];
$date=date("m:d:Y");

$users = array();
$query=imw_query("select * from users");
while( $row = imw_fetch_assoc($query))
{
	$users[$row['usersId']] = $row;
}
?>
<html>
<head></head>
<?php

if(($start_datetxt!='') && ($end_datetxt!=''))
{
	$start_datetxt=$_REQUEST['login'];
	$userid=$_REQUEST['user'];
	$type=$_REQUEST['atype'];
	$end_datetxt=$_REQUEST['logout'];
	
	$dat1=explode("-",$start_datetxt);
	$dat2=explode("-",$end_datetxt);
	
	$start_date=$dat1[2].'-'.$dat1[0].'-'.$dat1[1];
	$tomorrow  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
	$end_date =date("Y-m-d",$tomorrow);
	
	if(($userid!='') && ($start_datetxt!='') && ($end_datetxt!='')&& ($type=='all'))	
	{ 
		$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid 
															and password_status_date  between '$start_date' and  '$end_date'");
	}
	elseif(($userid!='') && ($start_datetxt!='') && ($end_datetxt!='')&& ($type!=''))
	{
		$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid
															and password_status_date  >= '$start_date' 
															and password_status_date  <= '$end_date' and status='$type'");
	}
	elseif(($userid!='') && ($start_datetxt!='') && ($end_datetxt!=''))
	{
		$qu=imw_query("select * from password_change_reset_audit_tbl where user_id=$userid
															and password_status_date  >= '$start_date' 
															and action_date_time  <= '$end_date'");
	}
	elseif(($start_datetxt!='') && ($end_datetxt!='') &&($type=='all'))
	{
		$qu=imw_query("select b.*,a.fname,a.lname from password_change_reset_audit_tbl b, users a 
																where password_status_date >= '$start_date' 
																and password_status_date  <= '$end_date'
																and a.usersId=b.user_id");
	}
	elseif(($start_datetxt!='') && ($end_datetxt!='') &&($type!=''))
	{
		$qu=imw_query("select b.*,a.fname,a.lname from password_change_reset_audit_tbl b, users a 
																where password_status_date >= '$start_date' 
																and password_status_date  <= '$end_date'
																and status='$type' and a.usersId=b.user_id");
	}
	//end of datefield check block
}
elseif($userid!='' && ($type!='')&&($type!='all') )
{
	$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid and status='$type'");
}
elseif($userid!='' &&($type=='all'))
{
	$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid");
}


$records = imw_num_rows($qu);
$report_data = array();
while($row=imw_fetch_assoc($qu))
{
	$report_data[] = $row;
}
	$pg_footer= '<page_footer>
								<table style="width: 100%;">
									<tr><td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td></tr>
								</table>
							</page_footer>';
	$pg_header = '
		<page_header>
			<table width="700" border="0" cellpadding="0" cellspacing="0" >
				<tr >
					<td width="'.((int) (700 - $imgwidth)).'" style="background-color:#cd532f; padding-left:5px; color:white;" align="left" valign="middle"><b>'.$name.'<br>'.$address.'</b></td>
					<td style="background-color:#cd532f;" align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				</tr>
				
				<tr height="22">
					<th align="right" colspan="2">Password Change/Reset/Lockouts Report</th>
				</tr>	
				<tr ><td colspan="2">&nbsp;</td></tr>				
			</table>
		</page_header>';
	
	$table.='
			<style>
				.BdrLBR { 
					border:solid 1px #999; border-top: solid 0px #fff;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrBR {
					border-bottom: solid 1px #999;
					border-right: solid 1px #999;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
								
				.tb_heading{ 
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#ccc;
				}
				
				
			</style>
			<page backtop="23mm" backbottom="15mm">'.$pg_footer . $pg_header.'
			
				<table width="700" border="0" cellpadding="4" cellspacing="0" id="bodyTable" style="border:solid 1px #999">
					<thead>
						<tr>
							<th width="140" class="BdrLBR tb_heading">User Name</th>
							<th width="170" class="BdrBR tb_heading">Action Date/Time</th>
							<th width="80" class="BdrBR tb_heading">Status</th>
							<th width="130" class="BdrBR tb_heading">Operator</th>
							<th width="170" class="BdrBR tb_heading">Comments</th>
						</tr>
					</thead>
					<tbody>';
					if( $records > 0 )
					{ 
					foreach($report_data as $data)
					{
						$u_data = $users[$data['user_id']];
						$user_name =  $u_data['lname'].", ".$u_data['fname'];
						$o_data = $users[$data['operator_id']];
						$opr_name =  $o_data['lname'].", ".$o_data['fname'];
						$table .= '<tr>';
						$table .= '<td class="BdrLBR">'.$user_name.'</td>';
						$table .= '<td class="BdrBR">'.$objManageData->getFullDtTmFormat($data['password_status_date']).'</td>';
						$table .= '<td class="BdrBR">'.ucwords($data['status']).'</td>';
						$table .= '<td class="BdrBR">'.$opr_name.'</td>';
						$table .= '<td class="BdrBR">'.$data['comments'].'</td>';
						$table .= '</tr>';
					}
					}
					else
					{
						$table .= '<tr><th colspan="5">no record found</th></tr>';	
					}
			$table .= '			
					</tbody>
				</table>
			</page>';			
					
$htmlFileName = 'pdffile'.$_SESSION['loginUserId'];
$fileOpen = fopen('../new_html2pdf/'.$htmlFileName.'.html','w+');
$intBytes = fputs($fileOpen,$table);
fclose($fileOpen);	
//die($table);
?>
<html>
<head>
<meta charset="utf-8">
<title>Password Change/Reset/Lockouts Report</title>    
<script language="javascript">
	function submitfn(){
		document.printFrm.submit();
	}
</script>
</head>
<body >
    <form name="printFrm" action="../new_html2pdf/createPdf.php" method="post">
    	<input type="hidden" name="htmlFileName" value="<?php echo $htmlFileName;?>">
    </form>
    <script type="text/javascript">
			window.focus();
			submitfn();
		</script>
</body>
</html>
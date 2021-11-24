<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../globalsSurgeryCenter.php");
include_once("../admin/classObjectFunction.php");
$objManageData = new manageData;
include("common/auditLinkfile.php");
$get_http_path=$_REQUEST['get_http_path'];
 
$fac_con="";
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" And ST.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

//set surgerycenter detail
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry);
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
		return $returnArr; 
	} 
	return "";
}		
		
// end set surgerycenter detail  
$date= date("m/d/Y");
$userid=$_REQUEST['user'];
$type=$_REQUEST['atype'];
$start_datetxt=$_REQUEST['login'];
$end_datetxt=$_REQUEST['logout'];
$query=imw_query("select * from users where usersId=$userid");
while(@$nameUsr=imw_fetch_array($query))
{
 $username=stripslashes($nameUsr['lname'].", ".$nameUsr['fname']);
}
if(!trim($username)) { $username = "All"; }
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
    if(($start_datetxt!='') && ($end_datetxt!=''))
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
        $start_date_range = $dat1[0].'-'.$dat1[1].'-'.$dat1[2];
        $end_date_range = $dat2[0].'-'.$dat2[1].'-'.$dat2[2];
        $end_date=$dat2[2].'-'.$dat2[0].'-'.$dat2[1];
        $tomorrow  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
        $end_date =date("Y-m-d",$tomorrow);
        
        if(($userid!='') && ($start_datetxt!='') && ($end_datetxt!='')&& ($type=='all'))
        {
        
            $qry = "select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b, users a, stub_tbl ST  
                            Where 
                            b.confirmation_id = ST.patient_confirmation_id 
                            And a.usersId=$userid 
                            And b.user_id=$userid 
                            And a.usersId=b.user_id
                            And action_date_time  between '$start_date' and  '$end_date'
                            ".$fac_con."
                            ";
        }
        elseif(($userid!='') && ($start_datetxt!='') && ($end_datetxt!=='')&& ($type!=''))
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
            $tomorrow  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
            $end_date =date("Y-m-d",$tomorrow);
            $qry = "select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b, users a, stub_tbl ST
                                where 
                                b.confirmation_id = ST.patient_confirmation_id 
                                And a.usersId=$userid 
                                And b.user_id=$userid 
                                And a.usersId=b.user_id
                                And action_date_time  >= '$start_date' 
                                And action_date_time  <= '$end_date' and status='$type'
                                ".$fac_con."
                            ";
        }
        elseif(($userid!='') && ($start_datetxt!='') && ($end_datetxt!=''))
        {
            $qry = "select * from chartnotes_change_audit_tbl b, stub_tbl ST
                                where user_id=$userid
                                And b.confirmation_id = ST.patient_confirmation_id 
                                And action_date_time  >= '$start_date' 
                                And action_date_time  <= '$end_date'
                                ".$fac_con."
                            ";
        }
        elseif(($start_datetxt!='') && ($end_datetxt!='') &&($type=='all'))
        {
    
            $qry = "select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b, users a, stub_tbl ST
                                 where
                                 b.confirmation_id = ST.patient_confirmation_id 
                                 And action_date_time >= '$start_date' 
                                 And action_date_time  <= '$end_date'
                                 And a.usersId=b.user_id
                                 ".$fac_con."
                            ";
        }
        elseif(($start_datetxt!='') && ($end_datetxt!='') &&($type!=''))
        {
            $qry = "select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b, users a, stub_tbl ST
                                     where 
                                     b.confirmation_id = ST.patient_confirmation_id
                                     And action_date_time >= '$start_date' 
                                     And action_date_time  <= '$end_date' 
                                     And status='$type' 
                                     And a.usersId=b.user_id
                                     ".$fac_con."
                            ";
        }
        //end of date field's if block	
    }
    
    elseif($userid!='' &&($type=='all'))
    { 
        $qry="select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b, users a, stub_tbl ST
                             where 
                             b.confirmation_id = ST.patient_confirmation_id
                             And a.usersId=$userid 
                             And b.user_id=$userid 
                             And a.usersId=b.user_id
                             ".$fac_con."
                        ";
    }
    elseif($userid!='' && ($type!='') )
    {
    
        $qry="select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b, users a, stub_tbl ST
                             where 
                             b.confirmation_id = ST.patient_confirmation_id
                             And a.usersId=$userid 
                             And a.usersId=b.user_id 
                             And b.status='$type'
                             ".$fac_con."
                        ";
    }
    elseif($type=='all')
    {
        $qry="select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b,users a, stub_tbl ST 
            where 
            b.confirmation_id = ST.patient_confirmation_id
            And a.usersId=b.user_id
            ".$fac_con."
            ";
    }
    elseif($type!='')
    {
        $qry="select b.*,a.fname,a.lname from chartnotes_change_audit_tbl b,users a, stub_tbl ST
                             where 
                             b.confirmation_id = ST.patient_confirmation_id
                             And status='$type' 
                             And a.usersId=b.user_id
                            ".$fac_con."
                    ";
    }
    
    $qu	=	imw_query($qry);
    if(@imw_num_rows($qu)>0)
    {
        $l=0;
        while(@$resu=imw_fetch_array($qu))
        {
            foreach($resu as $key1 => $val){
                $ChartDetails[$l][$key1] = $val;
            }	
            $l++;
        }
        $num=imw_num_rows($qu);
        $rowbreak=20;
         $numb=ceil($num/$rowbreak);
        $rowstart=0;
        $rowend = 20;
        $chart_note=$resu[4];
        $audit_type=$resu[5];
        $img_logo = showThumbImages('../new_html2pdf/white.jpg',170,50);
        $imgheight= $img_logo[2]+8;
        $imgwidth= $img_logo[1]+8;
    
        $table.='<page_header>
            <table style="width:700px;" border="0" cellpadding="0" cellspacing="0" >
                <tr height="'.$higinc.'" >
                    <td class="text_14b" style="width:290px;background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
                     </td>
                    <td class="text_14b" style="width:290px;background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" >Patient Chart Note Audit Report
                     </td> 
                    <td style="background-color:#CD523F;width:120px; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
                </tr>		
                <tr height="22" bgcolor="#F1F4F0">
                    <td align="left" colspan="3" class="">
                        User Name&nbsp;&nbsp;<b>'.$username.'</b>&nbsp;&nbsp;&nbsp;&nbsp;From&nbsp;'.$start_date_range.'&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;'.$end_date_range.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Report Date&nbsp;&nbsp;'.$date.'
                    </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </table>	
            <table style="width:700px;" border="0" cellpadding="0" cellspacing="0" >					
                <tr>
                    <td style="width:23%;" align="Left" class="text_printb">Action Date/Time </td>
                    <td style="width:18%;" align="Left" class="text_printb">Patient Name </td>
                    <td style="width:30%;" align="Left" class="text_printb">Chart Note </td>
                    <td style="width:18%;" align="Left" class="text_printb">Audit Type </td>
                </tr>
            </table>
        </page_header>';
        
        for($rows=0;$rows<$numb;$rows++){
            $tableRec = '';   
            for($j=$rowstart;$j<$rowend;$j++){
                $j.'-';
                $record3 = $objManageData->getFullDtTmFormat($ChartDetails[$j]['action_date_time']);
                $pid=$ChartDetails[$j]['patient_id'];
                $patientIdQry=imw_query("select patient_lname,patient_fname from patient_data_tbl where patient_id=$pid");
                $patientRes= @imw_fetch_array($patientIdQry);
                $patientname = $patientRes['patient_lname'].', '.$patientRes['patient_fname'];
                if($j<$num){
                    $tableRec.='
                     <tr>
                        <td height="40" style="width:23%;"   align="left" valign="middle" class="text_print"> '. $record3.' </td>
                        <td height="40" style="width:18%;"  align="left" >'.$patientname.'</td>
                        <td height="40" style="width:30%;"  align="left" >'.$ChartDetails[$j]['form_name'].'</td>
                        <td height="40" style="width:18%;"  align="left">'.$ChartDetails[$j]['status'].'</td>
                    </tr>';
                }
            }
            $rowstart = $rowend;
            $rowend=$rowend + 20;
            
            $table.='
                    <table style="width:700px;"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
                        '.$tableRec.'
                    </table>';
        }
    } 
    $table.='</page>';
	//echo $table;
    $flPth = '../new_html2pdf/testPdf.html';
    if(file_exists($flPth)) {
        unlink($flPth);
    }
    $fileOpen = fopen($flPth,'w+');
    $filePut = fputs(fopen($flPth,'w+'),$table);
    fclose($fileOpen);
    ?>
    <table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
        <tr>
            <td width="100%" align="center" valign="middle"><img src="../images/ajax-loader.gif"></td> 
        </tr>
    </table>
    <body onLoad="submitfn();">
        <form name="printFrm" action="../new_html2pdf/createPdf.php?op=p&htmlFileName=<?php echo $flPth;?>" method="post">
        </form> 
        <script type="text/javascript">
            function submitfn()
            {
                document.printFrm.submit();
            }
            submitfn();
        </script>
    </body>
</html>
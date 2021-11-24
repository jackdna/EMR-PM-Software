<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../globalsSurgeryCenter.php");
include("common/auditLinkfile.php");
 $userid=$_REQUEST['user'];
 $start_datetxt=$_REQUEST['login'];
 $end_datetxt=$_REQUEST['logout'];
 $type=$_REQUEST['atype'];
 $date=date("m:d:Y");
 $userid=$_REQUEST['user'];
 $start_datetxt=$_REQUEST['login'];
 $end_datetxt=$_REQUEST['logout'];
	$query=imw_query("select * from users where usersId=$userid");
	while(@$name=imw_fetch_array($query))
	{
	 $username=$name[1]." ".$name[3];
	}
?>
	<html>
<head>
	</head>
	<?php
		if(($userid!='') && ($start_datetxt!='') && ($end_datetxt!=='')&& ($type=='all'))
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
		  //$end_date=$dat2[2].'-'.$dat2[0].'-'.$dat2[1];
		 $tomorrow  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
		 $end_date =date("Y:m:d",$tomorrow);
		$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid
	and action_date_time  between '$start_date' and  '$end_date'");
		}
		elseif(($userid!='') && ($start_datetxt!='') && ($end_datetxt!='')&& ($type!=''))
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
		  //$end_date=$dat2[2].'-'.$dat2[0].'-'.$dat2[1];
		 $tomorrow  =date(mktime(0, 0, 0, $dat2[0]  , $dat2[1]+1, $dat2[2]));
		 $end_date =date("Y:m:d",$tomorrow);
		$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid
	and action_date_time  >= '$start_date' and action_date_time  <= '$end_date' and status='$type'");
	}
	elseif(($userid!='') && ($start_datetxt!='') && ($end_datetxt!==''))
	{
	$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid
	and action_date_time  >= '$start_date' and action_date_time  <= '$end_date'");
	}
	elseif($userid!='' && ($type!='')&&($type!='all') )
	{
	$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid and status='$type'");
	}
	elseif($userid!='' &&($type=='all'))
	{
	$qu=imw_query("select * from password_change_reset_audit_tbl  where user_id=$userid");
	}
	if(@imw_num_rows($qu)>0)
	{
	 $i=0;
	while(@$resu=imw_fetch_array($qu))
	{
		foreach($resu as $key=>$val)
		{
			$userpass[$i][$key]=$val;
		}
		$i++;
	}
	//echo "<p>";
	//print_r($userpass);
	echo $num=imw_num_rows($qu);
	$rowbreak=25;
	 $numb=ceil($num/$rowbreak);
	$rowstart=0;
	$rowend = 25;
	for($rows=0;$rows<$numb;$rows++)
	{
	$tableRec = '';   
	for($j=$rowstart;$j<$rowend;$j++){
	 
	$res=explode("-",$userpass[$j]['password_status_date']);	
	$time=explode(" ",$userpass[$j]['password_status_date']);	
	 $res[1];
	$day=explode(" ",$res[2]);
	$res[0];
	$record2=$res[1]."-".$day[0]."-".$res[0]." ".$time[1];
	$tableRec.='<tr height="22" bgcolor="#F1F4F0">
		<td width="50%" align="center" class="text_print">'. $record2.' </td>
		<td width="50%" align="center" class="text_print">'. $userpass[$j][3].'</td>
	</tr>
	<tr height="8"><td colspan="2"></td></tr>';
	}
	$rowstart = $rowend;
	$rowend=$rowend + 25;
	$table.='<table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
	 
	<tr height="35" bgcolor="#BCD2B0">
		<td  height="35" align="left" width="45%"  valign="top" ><div class="text_printb" >Imedic Surgery Center</div>
				 22 plainfield ave Lavallette, NJ 08735 </td>
	    <td  align="right"><img src="../images/logo1.gif"></td>
	</tr>
	<tr height="22"  bgcolor="#F1F4F0">
		<td  valign="top" align="left"><span class="text_printb"><img src="../images/tpixel.gif" width="14">User Name: </span><span class="red_txt">'.$username.'</span></td>
		<td  align="right" valign="top"><span class="text_printb">Date: </span><span class="red_txt">'.$date.'</span><img src="../images/tpixel.gif" width="14"></td>
	</tr>
	<tr height="22">
		<td width="50%" align="center" class="text_printb">Action Date/Time </td>
		<td width="50%" align="center" class="text_printb">Status </td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
				'.$tableRec.'
			</table>
		</td>
	</tr>
	<tr height="200"><td colspan="4"></td></tr>
	';
	
	
	//echo $table;
	
	}
	 }
	else
	{
			$flag='true';
			$disp='block';
		    $msg=  "No Record Exist For This User";
	}	
	
		$table.='<tr style="display:'.$disp.'"><td colspan="2" style="color:#990000; font-size:12px" align="center">'; if($flag) $table.= $msg; $table.='</td></tr>

	 <tr height="222"><td colspan="2"></td></tr>
	</table>';
	echo $table;
	$fileOpen = fopen('testPdf.html','w+');
$filePut = fputs(fopen('testPdf.html','w+'),$table);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/auditing/testPdf.html';
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table style="font:vetrdana; font-size:14px;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="../images/ajax-loader.gif"></td> 
	</tr>
</table>

<body>
 <form name="printFrm" action="../html2pdf/public_html/demo/html2ps.php"   method="post">
<input type="hidden" name="process_mode" value="single">
<input type="hidden" name="url" value="testPdf.html">
<input type="hidden" name="URL" value="<?php echo $URL; ?>">   
<input type="hidden" name="proxy" value="&&">
<input type="hidden" name="pixels" value="800">
<input type="hidden" name="scalepoints" value="1"> 
<input type="hidden" name="renderimages" value="1"> 
<input type="hidden" name="renderlinks" value="1"> 
<input type="hidden" name="renderfields" value="1"> 
<input type="hidden" name="media" value="Letter">
<input type="hidden" name="cssmedia" value="Handheld">
<input type="hidden" name="leftmargin" value="2">
<input type="hidden" name="rightmargin" value="2">
<input type="hidden" name="topmargin" value="2">
<input type="hidden" name="bottommargin" value="0">
<input type="hidden" name="toc-location" value="before">
<input type="hidden" name="smartpagebreak" value="0">
<input type="hidden" name="pslevel" value="1">
<input type="hidden" name="method" value="fpdf">
<input type="hidden" name="pdfversion" value="1.3">
<input type="hidden" name="output" value="0">
<table width="100%">
<?php //echo  $table; ?>
</table>
</form>		
<script type="text/javascript">
	submitfn();
</script>
</body>
	
	
</html>
	
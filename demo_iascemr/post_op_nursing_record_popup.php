<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/linkfile.php");


   
	/*$hidd_vitalSignBp=$_REQUEST['vitalSignBP_main'];
	$hidd_vitalSignP=$_REQUEST['vitalSignP_main'];
	$hidd_vitalSignR=$_REQUEST['vitalSignR_main'];
	$hidd_vitalSignTime=$_REQUEST['vitalSignTime_main'];*/
	 $postOpSite = $_REQUEST["postOPSite"];
	 $postOpSiteTime = $_REQUEST["postOpSiteTime"];
	 $nourishKind = $_REQUEST["nourishKind"];
	 $heparinLockOutTime = $_REQUEST["heparinLockOutTime"];
	 $patient_aox3 = $_REQUEST["patient_aox3"];
	 $recoveryComments = $_REQUEST["recoveryComments"];
	//$relivedNurseId = $_REQUEST["relivedNurseIdList"];
	$patientReleased2Adult = $_REQUEST["patientReleased2Adult"];
	 $patientsRelation = $_REQUEST["patientsRelation"];
	 $patientsRelationOther = $_REQUEST["patientsRelationOther"];
	//$nurseInitials = $_REQUEST["nurseInitials"];
	
?>




<html>

<body>
<?php
$table='<table cellpadding="0" cellspacing="0" border="1" bgcolor='.$bgcolor_post_op_nursing_order.' width="670">
	   <tr>
					<td align="right" colspan=3><img src="images/leftyellow_post_op_nurse_order.gif" width="3" height="24"></td>
					<td align="center" colspan=3 bgcolor='.$title_post_op_nursing_order.'><b>Post-Op Nursing Record</b></td>
				  	<td align="left" valign="top" colspan=3><img src="images/rightyellow_post_op_nurse_order.gif" width="3" height="24"></td>
				</tr>
		 <tr>
	  <td colspan=9><img src="images/tpixel.gif" width="4" height="1"></td>
	</tr>
	<tr bgcolor='.$heading_post_op_nursing_order.'>
			<td height="25"  colspan="10"><img src="images/tpixel.gif" width="15" height="1" />Recovery Vital Signs</td>
        </tr>
		
				<tr bgcolor='.$bg_color_post_op_nurse.'>
					<td width=40><img src="images/tpixel.gif" width="15" height="1" /><img src="images/vsign.gif" alt="Vital signs"/><img src="images/tpixel.gif" width="10" height="1" /></td>
					<td width=40 nowrap="nowrap" style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;"><b>BP</b></td>
					<td width=40 nowrap="nowrap" style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;">'.$hidd_vitalSignBp.'<img src="images/tpixel.gif" width="1" height="1" /></td>
					<td width=20 style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;"><b>P</b><img src="images/tpixel.gif" width="10" height="1" /></td>
					<td width=40 class="text_10"><img src="images/tpixel.gif" width="10">'.$hidd_vitalSignP.'</td>
					<td width=20 style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;"><b>R</b><img src="images/tpixel.gif" width="10" height="1" /></td>
					<td width=40 style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;"><img src="images/tpixel.gif" width="10">'.$hidd_vitalSignR.'<img src="images/tpixel.gif" width="15" height="1" /></td>
					<td width=40 align="right" style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;"><b>Time</b></span><img src="images/tpixel.gif" width="5" height="1" /></td>
					<td width=60 style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;"><img src="images/tpixel.gif" width="5" height="1" />'.$hidd_vitalSignTime.'</td>
					
					
				</tr>
				  <tr bgcolor='.$rowcolor_post_op_nursing_order.' height="20">
		            <td colspan="2" align="center"  nowrap="nowrap" style="color:#800080; cursor:hand; font-family:verdana; font-size:14px; font-weight:bold;" width="180"><img src="images/tpixel.gif" width="12" height="1"/>Post-Operative Site<img src="images/tpixel.gif" width="5" height="1"/> </td>
				    <td colspan="3" align="left" style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;" width="330"><img src="images/tpixel.gif" width="16" height="1">'.$postOpSite.'</td>
				    <td colspan="1" style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;" align="right"><b>Time</b><img src="images/tpixel.gif" width="5" height="1"></td>
				    <td colspan="3" align="center" style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;" id="sTime" width="78">'.$postOpSiteTime.'</td>
				</tr>
				<tr bgcolor="#FFFFFF" height="20">
			       <td colspan=2 style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;" align="center" style="color:#800080; cursor:hand;" ><img src="images/tpixel.gif" width="15" height="1" />Nourishment Kind<img src="images/tpixel.gif" width="19" height="1"/><img src="images/tpixel.gif" width="10" height="1" /></td>
                   <td colspan=2 align="center" style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal; width:324px"><img src="images/tpixel.gif" width="17" height="1"/>'.$nourishKind.'</td>
			       <td colspan=5 style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;" align="left">&nbsp;</td>
				</tr>			
				 <tr bgcolor='.$heading_post_op_nursing_order.' height="25">
		            <td colspan=2 width="150" nowrap="nowrap" style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;"><img src="images/tpixel.gif" width="15" height="1" />Heparin Lock Out</td>
			        <td colspan=3 width="11" nowrap="nowrap" style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;" style="padding-right:10px;">&nbsp;</td>
	                <td colspan=1 width="420" nowrap="nowrap" style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold;" align="right"><img src="images/tpixel.gif" width="5" height="1" />Time<img src="images/tpixel.gif" width="5" height="1" /></td>
		            <td colspan=2 align="center" style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;" width="82">'.$heparinLockOutTime.'</td>
			       <td width="37" nowrap="nowrap" class="text_10b" style="padding-right:10px;"><img src="images/tpixel.gif" width="1" height="1"/><img id="dt_tm" src="images/clock.gif" alt="clock" border="0"/ onclick="return show1Time();"></td>
		         </tr>
				 <tr bgcolor="#FFFFFF" height="25">
		           <td style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;"  nowrap="nowrap"><img src="images/tpixel.gif" width="15" height="1" />Patient</td>
			       <td style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold; padding-right:10px;" nowrap="nowrap"><img src="images/tpixel.gif" width="10" height="1" /></td>
			       <td style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal; padding-right:5px;" nowrap="nowrap" ><img src="images/tpixel.gif" width="20" height="1" /><span class="text_10" style="padding-right:5px;">AOx3</span></td>
		           <td style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal; padding-right:10px;" nowrap="nowrap" width="">'.$patient_aox3.'</td>';
				    if($patientReleased2Adult=="Yes") { $relationDisplay="block";}else { $relationDisplay="none"; $patientsRelationOtherDisplay = "none"; }
			     $table.='<td colspan=2 style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal;" nowrap="nowrap">Patient Released to Responsible Adult</td>
			       <td align="center" width=""><img src="images/tpixel.gif" width="5" height="1" />'.$patientReleased2Adult.'</td>
			       <td style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal; display:'.$relationDisplay.'; " id="relation_heading_id"><img src="images/tpixel.gif" width="10" height="1" />Relationship</td>
			       <td align="center" style="display:'.$relationDisplay.'; " id="relation_id"><img src="images/tpixel.gif" width="15" height="1" />'. $patientsRelation .'</td>';
			        if($patientsRelation=="other") { $patientsRelationOtherDisplay = "block";} else { $patientsRelationOtherDisplay = "none";}
			       $table.=' <td colspan=2 id="txt_otherRelation_id" align="center" style="display:$patientsRelationOtherDisplay;">'.$patientsRelationOther.'</td>
                </tr>
				<tr bgcolor='.$rowcolor_post_op_nursing_order.' height="20">
		          <td colspan=3 style="font-family:verdana; font-size:14px; color:#000000; font-weight:bold; align="center" style="color:#800080; cursor:hand;"><img src="images/tpixel.gif" width="15" height="1" />Comments <img src="images/tpixel.gif" width="9" height="1"/></td>
	              <td colspan=7 align="center" style="font-family:verdana; font-size:14px; color:#000000; font-weight:normal; width:220px; ">'.$recoveryComments.'</td>
                  
			   </tr>
			   	<tr>
					<td colspan=3 nowrap class="text_10"><img src="images/tpixel.gif" width="15" height="1" /> <b>Nurse :</b>'.$NurseName.'</td>
					<td colspan=3 nowrap class="text_10b"><img src="images/tpixel.gif" width="100" height="1" />Relived Nurse<img src="images/tpixel.gif" width="10" height="1" /></td>
					<td  nowrap class="text_10">';
									
								$relivedNurseQry = "select * from users where usersId='$relivedNurseId'";
								$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
								$relivedNurseRow=imw_fetch_array($relivedNurseRes);
								$relivedSelectNurseID = $relivedNurseRow["usersId"];
								$relivedNurseName = $relivedNurseRow["fname"]." ".$relivedNurseRow["mname"]." ".$relivedNurseRow["lname"];
												
												$table.=' '.$relivedNurseName.'
					</td>
				</tr>
				<tr>
				 <td nowrap class="text_10" colspan=3><img src="images/tpixel.gif" width="15" height="1" /> <b>Electronically Signed :</b>'; if($signatureOfNurse)  $sign='Yes'; else $sign= 'No';
				 $table.= ' '.$sign.'</td>
			   	 <td colspan="7" nowrap class="text_10">&nbsp;</td>
							</tr>
</table>';

echo $table;

//Generate PDF
$fileOpen = fopen('Pdf.html','w+');
$filePut = fputs(fopen('Pdf.html','w+'),$table);
fclose($fileOpen);
$URL='http://'.$_SERVER['HTTP_HOST'].'/surgerycenter/Pdf.html';
if(file_exists($URL))print $URL;
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<form name="printFrm" action="" method="post">
<input type="hidden" name="process_mode" value="single">
<input type="hidden" name="url" value="Pdf.html">
<input type="hidden" name="URL" value="<?php echo $URL; ?>">   
<input type="hidden" name="proxy" value="&&">
<input type="hidden" name="pixels" value="800">
<input type="hidden" name="scalepoints" value="1"> 
<input type="hidden" name="renderimages" value="1"> 
<input type="hidden" name="renderlinks" value="1"> 
<input type="hidden" name="renderfields" value="1"> 
<input type="hidden" name="media" value="Letter">
<input type="hidden" name="cssmedia" value="Handheld">
<input type="hidden" name="leftmargin" value="0">
<input type="hidden" name="rightmargin" value="0">
<input type="hidden" name="topmargin" value="0">
<input type="hidden" name="bottommargin" value="0">
<input type="hidden" name="toc-location" value="before">
<input type="hidden" name="smartpagebreak" value="0">
<input type="hidden" name="pslevel" value="1">
<input type="hidden" name="method" value="fpdf">
<input type="hidden" name="pdfversion" value="1.3">
<input type="hidden" name="output" value="0">
</form>		
<script type="text/javascript">
	//submitfn();
</script>

</body>
</html>

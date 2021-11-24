<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
ob_start();
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once("common/schedule_functions.php");
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
//getting patient details
$pat_id=$_GET['pat_id'];
$strQry = "SELECT fname, lname FROM patient_data WHERE id = '".$pat_id."'";
$rsData = imw_query($strQry);
$arrData = imw_fetch_assoc($rsData);
$strPatientName = $arrData['fname']." ".$arrData['lname'];
?>
<style>
.text_b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	background-color:#FFFFFF;
}
.text_10b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	background-color:#FFFFFF;
}
.tb_heading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684AB;
}
.text{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.text_9{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#ECE9D8;
}
.text_b_w{
	font-size:10px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#ffffff;
	background-color:#4684ab;
}
.text_e_b_w{
	font-size:16px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#ffffff;
	background-color:#4684ab;
}
.text_cancel{
	style=color:red;
	background-color:#CCCCCC;
	font-weight:bold;
}
.text_noshow{
	style=color:orange;
	background-color:#f3f3f3;
	font-weight:bold;
}
.text_detail{
	background-color:#A2C7DA;
}
</style>
<page> 
<table width="100%" border="0">	
	<?php			
	if($pat_id<>""){			
		?>		
	<tr height="20">
		<td width="740" colspan="8" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_e_b_w\""; } ?> align="center">&nbsp;Appointment Hx report for <?php echo $strPatientName;?> - <?php echo $pat_id;?>.&nbsp;</td>
	</tr>
	
		<?php
		$arr_site = array("" => "", "bilateral" => "(OU)", "left" => "(OS)", "right" => "(OD)");
		$vquery_c = "	SELECT 
							schedule_appointments.sa_patient_id, schedule_appointments.sa_patient_app_status_id, 
							schedule_appointments.id,ps.oldMadeBy as sa_madeby,schedule_appointments.sa_doctor_id,
							schedule_appointments.sa_comments, schedule_appointments.procedureid, schedule_appointments.procedure_site, 
							date_format( schedule_appointments.sa_app_time, '%m-%d-%y' ) AS sa_app_time, 
							time_format( sa_app_starttime, '%h:%i %p' ) AS sa_app_starttime, 
							time_format( sa_app_endtime, '%h:%i %p' ) AS sa_app_endtime,
							date_format( sa_app_start_date, '%m/%d/%y' ) AS sa_app_start_date, slot_procedures.proc, 
							slot_procedures.acronym, schedule_appointments.sa_facility_id  
						FROM 
							schedule_appointments
							LEFT JOIN previous_status ps ON (ps.sch_id = schedule_appointments.id AND ps.status=0)
							LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
							WHERE schedule_appointments.sa_patient_id ='".$pat_id."' 
							GROUP BY schedule_appointments.id ORDER BY schedule_appointments.sa_app_start_date DESC 
						";								 
		$vsql_c = imw_query($vquery_c);
		while($vrs=imw_fetch_array($vsql_c)){		
			$proc_site = strtolower($vrs['procedure_site']);
			$proc_site = $arr_site[$proc_site];
			$id=$vrs["id"];
			$procedureid=$vrs["procedureid"];
			$doctor_id=$vrs["sa_doctor_id"];
			$facility_id=$vrs["sa_facility_id"];
			$sa_patient_id=$vrs["sa_patient_id"];
			$prc_id=$procedureid;
			$strOpMadeByName = getOperatorInitialByUsername($vrs['sa_madeby']);
			
			//tr style
			$sty = "";
			$onclick = "";
//			if($vrs['sa_patient_app_status_id'] == '18'){
				$sty="class='text_cancel'";
//			}elseif($vrs['sa_patient_app_status_id'] == '3'){
//				$sty="class='text_noshow'";
//			}else{
//				$sty="";
//			}
			?>
	<tr class='text_b_w' height="20">
		<td width="125" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center"  nowrap><?php if($_REQUEST['mode'] == "tiny"){ echo "Date &amp; Time"; }else{ echo "Appt. Date &amp; Time"; } ?></td>						
		<td width="75" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo "Chk. In"; }else{ echo "Check In"; } ?></td>
		<td width="75" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo "Chk. Out"; }else{ echo "Check Out"; } ?></td>
		<td width="85" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo "Loc."; }else{ echo "Location"; } ?></td>
		<td width="125" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo "Phy."; }else{ echo "Provider"; } ?></td>
		<td width="85" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?>  align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo "Pro."; }else{ echo "Procedure"; } ?></td>
		<td width="150" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo "Notes"; }else{ echo "Notes"; } ?></td>	
		<td width="20" <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_b_s\""; }else{ echo "class=\"text_b_w\""; } ?> align="center">Op.</td>						
	</tr>
	<tr class="text_9" style="cursor:hand;">
		<td width="125" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center" nowrap>&nbsp;<?php echo($vrs["sa_app_start_date"]." ".$vrs["sa_app_starttime"]);?></td>						
		<td width="75" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center" nowrap>
			<?php echo getCheckin_time($vrs["id"]);?>	
		</td>
		<td width="75" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center" nowrap>
			<?php echo(getCheckout_time($vrs["id"]));?>
		</td>
		<td width="85" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo getFacility_name($facility_id, "tiny"); }else{ echo getFacility_name($facility_id); } ?></td>
		<td width="125" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center"><?php if($_REQUEST['mode'] == "tiny"){ echo getProvider_name($doctor_id, "tiny"); }else{ echo getProvider_name($doctor_id); } ?></td>
		<td width="85" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center" nowrap><?php if($_REQUEST['mode'] == "tiny"){ echo $vrs['acronym']; }else{ echo $vrs['proc']; } ?> <?php echo $proc_site;?></td>
		<td width="150" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="left"><?php echo($vrs['sa_comments']);?></td>	
		<td width="10" <?php echo $sty;?> <?php if($_REQUEST['mode'] == "tiny"){ echo $onclick;} ?> <?php if($_REQUEST['mode'] == "tiny"){ echo "class=\"text_9\""; } ?> align="center"><?php echo($strOpMadeByName);?></td>						
	</tr>
	<tr width='100%'>
		<td width="740" colspan="8" align="center">
			<?php 
			GetOneSchdeuleDetails($pat_id, $vrs["id"], "", "print",$CommonAppStatusArr);
			?>
		</td>
	</tr>
			<?php 
		}
	}else{
		?>					
	<tr width='100%' height="20" bgcolor="#4684ab">
		<td colspan="8" width="740" class="text_b_w" align="center">&nbsp;</td>
	</tr>
   		<?php
	}
	?>
	
</table>
</page>
<?php
$headDataALL = ob_get_contents();

if(trim($headDataALL) != ""){
	$file_location = write_html($headDataALL);
	
	if($file_location){
	?>
	<html>
		<body>
        	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
			<script type="text/javascript">
                top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
                top.html_to_pdf('<?php echo $file_location; ?>','l','','','false');
				window.self.close();
            </script>

			
		</body>
	</html>
	<?php
	}
}else{
	?>
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
			<td align="center">No Result.</td>
		</tr>
	</table>
	<?php
}
?>

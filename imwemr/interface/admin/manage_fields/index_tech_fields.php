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

require_once("../admin_header.php");
require_once($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

//default selected 
$elem_visit = "CEE";
$scriptMsg = '';
// Get Selected
if(isset($_GET["elem_visit"]) && !empty( $_GET["elem_visit"] )){
	$elem_visit = $_GET["elem_visit"];
}

if($_REQUEST['save_data']){
	$ocualr = $_REQUEST['ocualr'];
	$general_health = $_REQUEST['general_health'];
	$medication = $_REQUEST['medication'];
	$surgeries = $_REQUEST['surgeries'];
	$allergies = $_REQUEST['allergies'];
	$immunizations = $_REQUEST['immunizations'];
	$social = $_REQUEST['social'];
	$cvf = $_REQUEST['cvf'];
	$visit = $_REQUEST['visit'];
	$cc_history = $_REQUEST['cc_history'];
	$vision = $_REQUEST['vision'];
	$distance = $_REQUEST['distance'];
	$near = $_REQUEST['near'];
	$ar = $_REQUEST['ar'];
	$pc = $_REQUEST['pc'];
	$mr = $_REQUEST['mr'];
	$cvf_c = $_REQUEST['cvf_c'];
	$amsler_grid = $_REQUEST['amsler_grid'];
	$icp_color_plates = $_REQUEST['icp_color_plates'];
	$steroopsis = $_REQUEST['steroopsis'];
	$diplopia = $_REQUEST['diplopia'];
	$retinoscopy = $_REQUEST['retinoscopy'];
	$exophthalmometer = $_REQUEST['exophthalmometer'];
	$pupil = $_REQUEST['pupil'];
	$eom = $_REQUEST['eom'];
	$external = $_REQUEST['external'];
	$iop = $_REQUEST['iop'];
	$tech_id = $_REQUEST['tech_id'];
	$selectVisit = addslashes(trim($_REQUEST["elem_selectVisit"]));
	$selectVisitOther = addslashes(trim($_REQUEST["elem_selectVisitOther"]));
	if( ($selectVisit == "Other") && (!empty($selectVisitOther)) && ( $selectVisitOther != "Other" ) ){
		$ptVisit = $selectVisitOther;
		$sql = "SELECT tech_id FROM tech_tbl WHERE LCASE(ptVisit) = '".strtolower($ptVisit)."' ";
		$row = imw_query($sql);
		if($row == false){
			$sql = "INSERT INTO tech_tbl(tech_id, ocualr, general_health, medication, surgeries, allergies, immunizations, social, cvf, visit, cc_history, vision, distance,
					near, ar, pc , mr, cvf_c, amsler_grid, icp_color_plates, steroopsis, diplopia, retinoscopy, exophthalmometer, 
					pupil, eom, external, iop, ptVisit) 
					VALUES(null, '$ocular', '$general_health','$medication','$surgeries', '$allergies','immunizations','$social','$cvf','$visit','$cc_history', '$vision','$distance',
					'$near','$ar','$pc','$mr','$cvf_c','$amsler_grid','$icp_color_plates','$steroopsis','$diplopia','$retinoscopy','$exophthalmometer',
					'$pupil','$eom','$external','$iop','$ptVisit')";
			$res = imw_query($sql);			
			$scriptMsg .= "<script>top.alert_notification_show('Record Saved Successfully');</script>";
		}else{
			$scriptMsg .= "<script>fAlert('Visit name \'$ptVisit\' already exists.');</script>";
		}		
		$elem_visit = $ptVisit;		
		
	}else{	
		$tech_update = imw_query("update tech_tbl set ocualr='$ocualr',general_health='$general_health',medication='$medication',
					surgeries='$surgeries',allergies='$allergies',immunizations='$immunizations',social='$social',
					visit='$visit',cc_history='$cc_history',vision='$vision',distance='$distance',near='$near',ar='$ar',
					pc='$pc',mr='$mr',cvf_c='$cvf_c',amsler_grid='$amsler_grid',icp_color_plates='$icp_color_plates',
					steroopsis='$steroopsis',diplopia='$diplopia',retinoscopy='$retinoscopy',exophthalmometer='$exophthalmometer',
					pupil='$pupil',eom='$eom',external='$external',iop='$iop' where tech_id='$tech_id'");
		if($tech_update){
			$elem_visit = $selectVisit ;
			$scriptMsg .= "<script>top.alert_notification_show('Record Saved Successfully.');</script>";
		}
	}
}

//GET NEW VALUES
$oAdmn = new Admn();
$arrPtVisit=$oAdmn->wv_getPtVisit();

$sql_tech = imw_query("select * from tech_tbl where ptVisit='$elem_visit'");	
if(imw_num_rows($sql_tech) > 0){
	$row_fet = imw_fetch_array($sql_tech);
	$ocualr = $row_fet['ocualr'];
	$general_health = $row_fet['general_health'];
	$medication = $row_fet['medication'];
	$surgeries = $row_fet['surgeries'];
	$allergies = $row_fet['allergies'];
	$immunizations = $row_fet['immunizations'];
	$social = $row_fet['social'];
	$cvf = $row_fet['cvf'];
	$visit = $row_fet['visit'];
	$cc_history = $row_fet['cc_history'];
	$vision = $row_fet['vision'];
	$distance = $row_fet['distance'];
	$near = $row_fet['near'];
	$ar = $row_fet['ar'];
	$pc = $row_fet['pc'];
	$mr = $row_fet['mr'];
	$cvf_c = $row_fet['cvf_c'];
	$amsler_grid = $row_fet['amsler_grid'];
	$icp_color_plates = $row_fet['icp_color_plates'];
	$steroopsis = $row_fet['steroopsis'];
	$diplopia = $row_fet['diplopia'];
	$retinoscopy = $row_fet['retinoscopy'];
	$exophthalmometer = $row_fet['exophthalmometer'];
	$pupil = $row_fet['pupil'];
	$eom = $row_fet['eom'];
	$external = $row_fet['external'];
	$iop = $row_fet['iop'];
	$tech_id = $row_fet['tech_id'];
}
?>	
<?php echo $scriptMsg;?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_tech_fields.js"></script>
<body>

<div class="whtbox pdnon" id="pracfields" >
	<input type="hidden" name="preObjBack" value="">
		<form action="" name="tech" method="post">
		<input type="hidden" name="tech_id" value="<?php echo $tech_id; ?>">
		<input type="hidden" name="save_data" value="">
		
			<div class="headinghd pd10">
			<div class="row">
				<div class="col-sm-6">
					<h2 style="margin-top:5px;">Medical History</h2>
				</div>
				<div class="col-sm-3">
					<input type="text" name="elem_selectVisitOther"  id="elem_selectVisitOther" value="" tabindex="2" style="visibility:hidden;">
				</div>
				<div class="col-sm-3 pull-right">
					<label for="elem_selectVisit">Select Visit:</label>
					<select name="elem_selectVisit" id="elem_selectVisit" class="selectpicker" onChange="chk4Other('elem_selectVisit')">
						<?php
							if( count($arrPtVisit) > 0 ){
								foreach($arrPtVisit as $key => $val ){
									$sel = ($val == $elem_visit) ? "selected" : "";
									echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
								}
							}
						?>
					</select>
				</div>
				
			</div>
		</div>
		<div class="row pd10">
			<div class="col-md-6">
				<div class="table-responsive respotable adminnw" id="meducalHistoryTable1">
					<table class="table table-bordered table-hover">
						<tr class="lhtgrayhead">
							<th>Medical History</th>
							<th style="width:30%" colspan="2" class="text-center">Advisory</th>
						</tr>
						<tr class="lhtgrayhead">
							<th></th>
							<th class="text-center">Yes</th>
							<th class="text-center">No</th>
						</tr>
						<tr>
							<td>Ocular</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="ocualr" id="ocualr_y"  onClick="setCheckAction('ocualr_y','ocualr_n');" value="yes" <?php if($ocualr=='yes')echo 'checked';?>><label for="ocualr_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="ocualr" id="ocualr_n"  onClick="setCheckAction('ocualr_n','ocualr_y');" value="no" <?php if($ocualr=='no')echo 'checked';?>><label for="ocualr_n"></label></div></td>
						</tr>
						<tr>
							<td>General Health</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="general_health" id="general_health_y" onClick="setCheckAction('general_health_y','general_health_n');" value="yes" <?php if($general_health=='yes')echo 'checked';?>><label for="general_health_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="general_health" id="general_health_n" onClick="setCheckAction('general_health_n','general_health_y');" value="no" <?php if($general_health=='no')echo 'checked';?>><label for="general_health_n"></label></div></td>
						</tr>
						<tr>
							<td>Medication</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="medication" id="medication_y" onClick="setCheckAction('medication_y','medication_n');" value="yes" <?php if($medication=='yes')echo 'checked';?>><label for="medication_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="medication" id="medication_n" onClick="setCheckAction('medication_n','medication_y');" value="no" <?php if($medication=='no')echo 'checked';?>><label for="medication_n"></label></div></td>
						</tr>
						<tr>
							<td>Surgeries</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="surgeries" id="surgeries_y" onClick="setCheckAction('surgeries_y','surgeries_n');"  value="yes" <?php if($surgeries=='yes')echo 'checked';?>><label for="surgeries_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="surgeries" id="surgeries_n" onClick="setCheckAction('surgeries_n','surgeries_y');"  value="no" <?php if($surgeries=='no')echo 'checked';?>><label for="surgeries_n"></label></div></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<div class="table-responsive respotable adminnw" id="meducalHistoryTable2">
					<table class="table table-bordered table-hover">
						<tr class="lhtgrayhead">
							<th>Medical History</th>
							<th style="width:30%" colspan="2" class="text-center">Advisory</th>
						</tr>   
						<tr class="lhtgrayhead">
							<th></th>
							<th class="text-center">Yes</th>
							<th class="text-center">No</th>
						</tr>
						<tr>
							<td>Allergies</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="allergies" id="allergies_y" onClick="setCheckAction('allergies_y','allergies_n');" value="yes" <?php if($allergies=='yes')echo 'checked';?>><label for="allergies_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="allergies" id="allergies_n" onClick="setCheckAction('allergies_n','allergies_y');" value="no" <?php if($allergies=='no')echo 'checked';?>><label for="allergies_n"></label></div></td>
						</tr>
						<tr>
							<td>Immunizations</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="immunizations" id="immunizations_y" onClick="setCheckAction('immunizations_y','immunizations_n');" value="yes" <?php if($immunizations=='yes')echo 'checked';?>><label for="immunizations_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="immunizations"  id="immunizations_n" onClick="setCheckAction('immunizations_n','immunizations_y');" value="no" <?php if($immunizations=='no')echo 'checked';?>><label for="immunizations_n"></label></div></td>
						</tr>
						<tr>
							<td>Social</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="social" id="social_y" onClick="setCheckAction('social_y','social_n');"  value="yes" <?php if($social=='yes')echo 'checked';?>><label for="social_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="social" id="social_n" onClick="setCheckAction('social_n','social_y');" value="no" <?php if($social=='no')echo 'checked';?>><label for="social_n"></label></div></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class=" pd10 headinghd">
			<h2>Chart Notes</h2>
		</div>
		<div class="row pd10">
			<div class="col-md-6">
				<div class="table-responsive respotable adminnw" id="chartNotesTable1">
					<table class="table table-bordered table-hover">
						<tr class="lhtgrayhead">
							<th>Chart Notes</th>
							<th style="width:30%" colspan="2" class="text-center">Advisory</th>
						</tr>
						<tr class="lhtgrayhead">
							<th></th>
							<th class="text-center">Yes</th>
							<th class="text-center">No</th>
						</tr>
						<tr>
							<td>Visit</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="visit" id="visit_y" onClick="setCheckAction('visit_y','visit_n');" value="yes" <?php if($visit=='yes')echo 'checked';?>><label for="visit_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="visit" id="visit_n" onClick="setCheckAction('visit_n','visit_y');" value="no" <?php if($visit=='no')echo 'checked';?>><label for="visit_n"></label></div></td>
						</tr>
						<tr>
							<td>CC & History</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="cc_history" id="cc_history_y" onClick="setCheckAction('cc_history_y','cc_history_n');" value="yes" <?php if($cc_history=='yes')echo 'checked';?>><label for="cc_history_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="cc_history" id="cc_history_n" onClick="setCheckAction('cc_history_n','cc_history_y');" value="no" <?php if($cc_history=='no')echo 'checked';?>><label for="cc_history_n"></label></div></td>
						</tr>
						<tr>
							<td>Vision</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="vision" id="vision_y" onClick="setCheckAction('vision_y','vision_n'); sel_check('yes');" value="yes" <?php if($vision=='yes')echo 'checked';?>><label for="vision_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="vision" id="vision_n" onClick="setCheckAction('vision_n','vision_y'); sel_check('no');" value="no" <?php if($vision=='no')echo 'checked';?>><label for="vision_n"></label></div></td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;Distance</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="distanceyes" name="distance" onClick="setCheckAction('distanceyes','distanceno');"  value="yes" <?php if($distance=='yes')echo 'checked';?>><label for="distanceyes"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="distanceno" name="distance"  onClick="setCheckAction('distanceno','distanceyes');" value="no" <?php if($distance=='no')echo 'checked';?>><label for="distanceno"></label></div></td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;Near</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="nearyes" name="near" onClick="setCheckAction('nearyes','nearno');" value="yes" <?php if($near=='yes')echo 'checked';?>><label for="nearyes"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="nearno" name="near"  onClick="setCheckAction('nearno','nearyes');" value="no" <?php if($near=='no')echo 'checked';?>><label for="nearno"></label></div></td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;AR</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="aryes" name="ar" onClick="setCheckAction('aryes','arno');" value="yes" <?php if($ar=='yes')echo 'checked';?>><label for="aryes"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="arno" name="ar" onClick="setCheckAction('arno','aryes');" value="no" <?php if($ar=='no')echo 'checked';?>><label for="arno"></label></div></td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;PC</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="pcyes" name="pc" onClick="setCheckAction('pcyes','pcno');" value="yes" <?php if($pc=='yes')echo 'checked';?>><label for="pcyes"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="pcno" name="pc" onClick="setCheckAction('pcno','pcyes');" value="no" <?php if($pc=='no')echo 'checked';?>><label for="pcno"></label></div></td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;MR</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="mryes" name="mr" onClick="setCheckAction('mryes','mrno');" value="yes" <?php if($mr=='yes')echo 'checked';?>><label for="mryes"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" id="mrno" name="mr" onClick="setCheckAction('mrno','mryes');" value="no" <?php if($mr=='no')echo 'checked';?>><label for="mrno"></label></div></td>
						</tr>
						<tr>
							<td>CVF</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="cvf_c" id="cvf_y" onClick="setCheckAction('cvf_y','cvf_n');"  value="yes" <?php if($cvf_c=='yes')echo 'checked';?>><label for="cvf_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="cvf_c" id="cvf_n" onClick="setCheckAction('cvf_n','cvf_y');"  value="no" <?php if($cvf_c=='no')echo 'checked';?>><label for="cvf_n"></label></div></td>
						</tr>
						<tr>
							<td>Amsler Grid</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="amsler_grid" id="amsler_grid_y" onClick="setCheckAction('amsler_grid_y','amsler_grid_n');" value="yes" <?php if($amsler_grid=='yes')echo 'checked';?>><label for="amsler_grid_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="amsler_grid" id="amsler_grid_n" onClick="setCheckAction('amsler_grid_n','amsler_grid_y');" value="no" <?php if($amsler_grid=='no')echo 'checked';?>><label for="amsler_grid_n"></label></div></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<div class="table-responsive respotable adminnw" id="chartNotesTable2">
					<table class="table table-bordered table-hover">
						<tr class="lhtgrayhead">
							<th>Chart Notes</th>
							<th style="width:30%" colspan="2" class="text-center">Advisory</th>
						</tr>
						<tr class="lhtgrayhead">
							<th></th>
							<th class="text-center">Yes</th>
							<th class="text-center">No</th>
						</tr>
						<tr>  
  							<td>ICP Color Plates</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="icp_color_plates" id="icp_color_plates_y" onClick="setCheckAction('icp_color_plates_y','icp_color_plates_n');" value="yes" <?php if($icp_color_plates=='yes')echo 'checked';?>><label for="icp_color_plates_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="icp_color_plates" id="icp_color_plates_n" onClick="setCheckAction('icp_color_plates_n','icp_color_plates_y');" value="no" <?php if($icp_color_plates=='no')echo 'checked';?>><label for="icp_color_plates_n"></label></div></td>
						</tr>
						<tr>
							<td>Steropsis</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="steroopsis" id="steroopsis_y"  onClick="setCheckAction('steroopsis_y','steroopsis_n');" value="yes" <?php if($steroopsis=='yes')echo 'checked';?>><label for="steroopsis_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="steroopsis" id="steroopsis_n"  onClick="setCheckAction('steroopsis_n','steroopsis_y');" value="no" <?php if($steroopsis=='no')echo 'checked';?>><label for="steroopsis_n"></label></div></td>
						</tr>
						<tr>
							<td>Diplopia</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="diplopia"  id="diplopia_y" onClick="setCheckAction('diplopia_y','diplopia_n');"  value="yes" <?php if($diplopia=='yes')echo 'checked';?>><label for="diplopia_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="diplopia" id="diplopia_n" onClick="setCheckAction('diplopia_n','diplopia_y');" value="no" <?php if($diplopia=='no')echo 'checked';?>><label for="diplopia_n"></label></div></td>
						</tr>
						<tr>
							<td>Retinoscopy</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="retinoscopy" id="retinoscopy_y" onClick="setCheckAction('retinoscopy_y','retinoscopy_n');" value="yes" <?php if($retinoscopy=='yes')echo 'checked';?>><label for="retinoscopy_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="retinoscopy" id="retinoscopy_n" onClick="setCheckAction('retinoscopy_n','retinoscopy_y');" value="no" <?php if($retinoscopy=='no')echo 'checked';?>><label for="retinoscopy_n"></label></div></td>
						</tr>
						<tr>
							<td>Exophthalmometer</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="exophthalmometer" id="exophthalmometer_y" onClick="setCheckAction('exophthalmometer_y','exophthalmometer_n');" value="yes" <?php if($exophthalmometer=='yes')echo 'checked';?>><label for="exophthalmometer_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="exophthalmometer" id="exophthalmometer_n" onClick="setCheckAction('exophthalmometer_n','exophthalmometer_y');"  value="no" <?php if($exophthalmometer=='no')echo 'checked';?>><label for="exophthalmometer_n"></label></div></td>
						</tr>
						<tr>
							<td>Pupil</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="pupil" id="pupil_y" onClick="setCheckAction('pupil_y','pupil_n');"  value="yes" <?php if($pupil=='yes')echo 'checked';?>><label for="pupil_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="pupil" id="pupil_n" onClick="setCheckAction('pupil_n','pupil_y');"  value="no" <?php if($pupil=='no')echo 'checked';?>><label for="pupil_n"></label></div></td>
						</tr>
						<tr>
							<td>EOM</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="eom" id="eom_y" onClick="setCheckAction('eom_y','eom_n');" value="yes" <?php if($eom=='yes')echo 'checked';?>><label for="eom_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="eom" id="eom_n" onClick="setCheckAction('eom_n','eom_y');"  value="no" <?php if($eom=='no')echo 'checked';?>><label for="eom_n"></label></div></td>
						</tr>
						<tr>
							<td>External</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="external" id="external_y"  onClick="setCheckAction('external_y','external_n');"  value="yes" <?php if($external=='yes')echo 'checked';?>><label for="external_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="external" id="external_n" onClick="setCheckAction('external_n','external_y');"  value="no" <?php if($external=='no')echo 'checked';?>><label for="external_n"></label></div></td>
						</tr>
						<tr>
							<td>IOP</td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="iop" id="iop_y" onClick="setCheckAction('iop_y','iop_n');"  value="yes" <?php if($iop=='yes')echo 'checked';?>><label for="iop_y"></label></div></td>
							<td class="text-center"><div class="checkbox"><input type="checkbox" name="iop" id="iop_n" onClick="setCheckAction('iop_n','iop_y');"  value="no" <?php if($iop=='no')echo 'checked';?>><label for="iop_n"></label></div></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		
	</form>
	<form name="frmVisit" action="" method="get">
		<input type="hidden" name="elem_visit" value="<?php echo $elem_visit;?>">	
	</form>
</div>
<?php
	require_once("../admin_footer.php");
?>

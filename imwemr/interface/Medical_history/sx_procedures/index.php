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

include_once($GLOBALS['srcdir'] . "/classes/medical_hx/sx_procedure.class.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$sx = new SxProcedure($medical->current_tab);
$sx->del_sx_procedure($_REQUEST['mode'], $_REQUEST['del_id']);
$sx->data = $sx->load_sx_procedure($_REQUEST);
extract($sx->data);
//pre($sx->vocabulary);
//--- SET SX / PROCEDURE NOT EXISTS -----
//$objManageData->Smarty->assign('checkSurgeries',$checkSurgeries);
?>
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/js/sx_datepicker/sx_proc_datepicker.css"/>
<div class="col-xs-12">

	<form name="sx_procedures_form" id="sx_procedures_form" action="<?php echo $folder; ?>/save.php" method="post">
		<input type="hidden" name="info_alert" id="info_alert" value="<?php echo urlencode(serialize($sx->vocabulary)); ?>">
		<input type="hidden" name="preObjBack" id="preObjBack" value=""/>
		<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $sx->current_tab; ?>">
		<input type="hidden" name="next_tab" id="next_tab" value="">
		<input type="hidden" name="next_dir" id="next_dir" value="">
		<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
		<input type="hidden" name="change_value" id="change_value" value="">
		<input type="hidden" name="hidSXProIdVizChange" id="hidSXProIdVizChange" value="">
		<input type="hidden" name="callFrom" id="callFrom" value="<?php echo $_REQUEST["callFrom"]; ?>" />
		<input type="hidden" name="flgSxIco" id="flgSxIco" value="<?php echo $_REQUEST["flgSxIco"]; ?>" />
		<input type="hidden" name="divH" id="divH" value="<?php echo $_REQUEST["divH"]; ?>" />

		<div <?php echo ($_REQUEST['callFrom'] == 'WV' ? 'style=" min-height:' . ($_REQUEST["divH"]) . 'px; max-height:' . ($_REQUEST["divH"]) . 'px;overflow:hidden; overflow-y:auto;"' : ''); ?> >

			<div class="oculartop">
				<div class="row">

					<div class="col-sm-12">
						<div class="eyetst text-left" >
							<div class="checkbox checkbox-default">
								<?php
								$chk_change = ($_REQUEST["callFrom"] <> 'WV') ? 'chk_change(\'' . ($sx_exists == 'checked' ? $sx_exists : '') . '\',this,event);' : '';
								?>
								<input id="no_sur_chk" type="checkbox" name="commonNoSurgeries" value="NoSurgeries" <?php echo $sx_exists; ?> onChange="<?php echo $chk_change; ?>  changeSxVal(); statusOfAllInputs();" >
								<label for="no_sur_chk">No&nbsp;Surgeries</label>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="clearfix"></div>

			<div class="table-responsive">

				<table class="table table-striped table-bordered" id="surgery_ocu_table">
					<thead>
						<tr class="grythead">
							<td  align="center" width="300">
								
								Ocular Sx/Procedures
								
							</td>
							<td colspan="3" align="center" width="100">Site</td>
							<td rowspan="2" align="center" width="200">Date of Procedure</td>
							<td rowspan="2" align="center" width="150">Physician</td>
							<td rowspan="2" align="center" width="150">Comments</td>
							<td rowspan="2" align="center" width="90">Type</td>
							<td rowspan="2" align="center" width="90">Status</td>
							<td rowspan="2" align="center" width="110">SNOMED CT</td>
							<td rowspan="2" align="center" width="50">Refusal</td>
							<td rowspan="2" align="center" width="50">Del</td>
						</tr>
						<tr class="grythead">
							<td id="td_def_vw">
								<div class="radio radio-inline"><input type="radio" name="el_df_vw" id="el_df_vw_All" value="All" <?php echo $el_df_vw=="All" ? "CHECKED" : ""; ?> ><label for="el_df_vw_All">All</label></div>
								<div class="radio radio-inline"><input type="radio" name="el_df_vw" id="el_df_vw_Ret" value="Ret" <?php echo $el_df_vw=="Ret" ? "CHECKED" : ""; ?> ><label for="el_df_vw_Ret">Ret</label></div>
								<div class="radio radio-inline"><input type="radio" name="el_df_vw" id="el_df_vw_GL" value="GL" <?php echo $el_df_vw=="GL" ? "CHECKED" : ""; ?> ><label for="el_df_vw_GL">GL</label></div>
								<div class="radio radio-inline"><input type="radio" name="el_df_vw" id="el_df_vw_Other" value="Other" <?php echo $el_df_vw=="Other" ? "CHECKED" : ""; ?> ><label for="el_df_vw_Other">Other</label></div>
							</td>
							<td width="20" align="center">OU</td>
							<td width="20" align="center">OD</td>
							<td width="20" align="center">OS</td>
						</tr>
					</thead>

					<tbody>
						<?php
						$i = 0;
						$j = 0;
						foreach ($finalResArr['OCU'] as $DATA_KEY => $DATA_VAL1) {
							$i++;
							$j++;
							?>

							<tr id="sx_tr<?php echo $i; ?>">

								<!--  Ocular Sx/Procedures -->
								<td>
									<input type="hidden" name="sg_id<?php echo $i; ?>" id="sg_id<?php echo $i; ?>" value="<?php echo $DATA_VAL1['SX_ID']; ?>" >
									<input type="hidden" name="sg_occular<?php echo $i; ?>" id="sg_occular<?php echo $i; ?>" <?PHP echo $DATA_VAL1['SX_OCCULAR']; ?> value="6" />
									<input data-toggle="tooltip" data-placement="top" <?php echo ($DATA_VAL1['SX_TITLE'])?'data-original-title="'.$DATA_VAL1['SX_TITLE'].'"':''; ?> type="text" id="sx_title_text<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="sx_title_text<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_TITLE']); ?>', this, event);<?php } ?>  insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_TITLE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['SX_TITLE']; ?>" class="form-control" onChange="changeSxVal(this);" />
								</td>

								<!-- SITE OU -->
								<td class="text-center">
									<div class="radio">
										<input type="radio" name="sx_site<?php echo $i; ?>" id="md_ou<?php echo $i; ?>" value="3" tabindex="<?php echo $i; ?>" <?php if ($DATA_VAL1['MED_SITE'] == '3') echo 'checked'; ?> onClick="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['MED_SITE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['MED_SITE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); chkBoxSetting('md_od<?php echo $i; ?>', 'md_os<?php echo $i; ?>', 'md_po<?php echo $i; ?>')" >
										<label for="md_ou<?php echo $i; ?>">&nbsp;</label>
									</div>
								</td>

								<!-- SITE OD -->
								<td class="text-center">
									<div class="radio">
										<input type="radio" name="sx_site<?php echo $i; ?>" id="md_od<?php echo $i; ?>" value="2" tabindex="<?php echo $i; ?>" <?php if ($DATA_VAL1['MED_SITE'] == '2') echo 'checked'; ?> onClick="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['MED_SITE']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['MED_SITE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); chkBoxSetting('md_os<?php echo $i; ?>', 'md_ou<?php echo $i; ?>', 'md_po<?php echo $i; ?>');" >
										<label for="md_od<?php echo $i; ?>">&nbsp;</label>
									</div>
								</td>

								<!--  SITE OS -->
								<td class="text-center">
									<div class="radio">
										<input type="radio" name="sx_site<?php echo $i; ?>" id="md_os<?php echo $i; ?>" value="1" tabindex="<?php echo $i; ?>" <?php if ($DATA_VAL1['MED_SITE'] == '1') echo 'checked'; ?> onClick="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['MED_SITE']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['MED_SITE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); chkBoxSetting('md_od<?php echo $i; ?>', 'md_ou<?php echo $i; ?>', 'md_po<?php echo $i; ?>');" >
										<label for="md_os<?php echo $i; ?>">&nbsp;</label>
									</div>
								</td>

								<!-- Date Of Procedure -->
								<td>
									<div class="col-sm-8">
										<div class="input-group">
											<input type="text" tabindex="<?php echo $i; ?>" name="sg_begindate<?php echo $i; ?>" id="sg_begindate<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); changeSxVal();" onChange="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); changeSxVal();" value="<?php echo $DATA_VAL1['SX_BEG_DATE']; ?>" maxlength="10" class="date-pick1  dt_surgery form-control" title="<?php echo inter_date_format(); ?>" onBlur="insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); check_sx_beg_date(this);">
											<label for="sg_begindate<?php echo $i; ?>" class="input-group-addon btn">
												<i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<input type="text" tabindex="<?php echo $i; ?>" name="sg_begtime<?php echo $i; ?>" id="sg_begtime<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_TIME']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_TIME']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['SX_BEG_TIME']; ?>" class="form-control" onChange="changeSxVal();">
									</div>
								</td>

								<!-- Physician -->
								<td>
									<input type="hidden" tabindex="<?php echo $i; ?>" name="referredby_id<?php echo $i; ?>" id="referredby_id<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['referredby_id']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['referredby_id']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['referredby_id']; ?>" onChange="changeSxVal();">
									<input type="text" tabindex="<?php echo $i; ?>" name="sg_referredby<?php echo $i; ?>" id="sg_referredby<?php echo $i; ?>" onKeyUp="top.loadPhysicians(this, 'referredby_id<?php echo $i; ?>');<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_REFFERED_BY']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_REFFERED_BY']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['SX_REFFERED_BY']; ?>" class="form-control" onChange="changeSxVal();" onFocus="top.loadPhysicians(this, 'referredby_id<?php echo $i; ?>');">
								</td>

								<!-- Comments -->
								<td>
									<textarea class="form-control" tabindex="<?php echo $i; ?>" rows="1" id="sg_comments<?php echo $i; ?>" name="sg_comments<?php echo $i; ?>" onChange="changeSxVal();" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_COMMENTS']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_COMMENTS']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));"><?php echo $DATA_VAL1['SX_COMMENTS']; ?></textarea>
								</td>

								<!-- TYPE -->
								<td>
									<select class="minimal form-control" name="surgery_type<?php echo $i; ?>" id="surgery_type<?php echo $i; ?>" data-width="100%"  onChange="changeSxVal();<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['proc_type']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['proc_type']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));">
										<option value="surgery" <?php echo ('surgery' == $DATA_VAL1['proc_type'] ? 'selected' : ''); ?> >Surgery</option>
										<option value="procedure" <?php echo ('procedure' == $DATA_VAL1['proc_type'] ? 'selected' : ''); ?>>Procedure</option>
										<option value="intervention" <?php echo ('intervention' == $DATA_VAL1['proc_type'] ? 'selected' : ''); ?>>Intervention</option>
									</select>
								</td>
								
								<td>
									<select class="minimal form-control" name="procedure_status<?php echo $i; ?>" id="procedure_status<?php echo $i; ?>" data-width="100%"  onChange="changeSxVal();<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['procedure_status']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['procedure_status']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));">
										<option vlaue="">Select</option>
										<option value="pending" <?php echo ('pending' == $DATA_VAL1['procedure_status'] ? 'selected' : ''); ?>>Pending</option>
										<option value="completed" <?php echo ('completed' == $DATA_VAL1['procedure_status'] ? 'selected' : ''); ?>>Completed</option>
									</select>
								</td>
								<!-- SNOMED CT -->
								<td>
									<input type="text" tabindex="<?php echo $i; ?>" name="ccda_code<?php echo $i; ?>" id="ccda_code<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['ccda_code']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['ccda_code']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['ccda_code']; ?>" class="form-control" onChange="changeSxVal();">
								</td>
                                <!-- Refusal CT -->
								<td class="text-center">
									<div class="checkbox">
										<input type="checkbox" class="checkbox" name="refusal<?php echo $i; ?>" id="refusal<?php echo $i; ?>" tabindex="<?php echo $i; ?>" <?php if ($DATA_VAL1['REFUSAL'] == '1') echo 'checked'; ?> onChange="check_refusal(<?php echo $i; ?>); insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['REFUSAL']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['REFUSAL']; ?>">
										<label for="refusal<?php echo $i; ?>">&nbsp;</label>
									</div>
								</td>
								<input type="hidden" name="refusal_reason<?php echo $i; ?>" id="refusal_reason<?php echo $i; ?>" value="<?php echo $DATA_VAL1['REFUSAL_REASON']; ?>">
								<input type="hidden" name="refusal_snomed<?php echo $i; ?>" id="refusal_snomed<?php echo $i; ?>" value="<?php echo $DATA_VAL1['REFUSAL_SNOMED']; ?>">
								
								<td align="center">
	<?php
	if ($j == count($finalResArr['OCU'])) {
        $month=$DATA_VAL1['SX_MONTH'];
        $date=$DATA_VAL1['SX_DATE'];
        $year=$DATA_VAL1['SX_YEAR'];
        
		echo '<span id="add_row_' . $i . '" class="glyphicon glyphicon-plus pointer" alt="Add More" onClick="addNewRow(' . $i . ',\'OCU\');"></span>';
	} else {
		echo '<span class="pointer glyphicon glyphicon-remove" alt="Delete" onClick="removeTableRow(\'' . $DATA_VAL1['SX_ID'] . '\', ' . $i . ');" ></span>';
	}
	?>
								</td>
							</tr>

									<?php
								}
								?>
					</tbody>
				</table>

				<table class="table table-striped table-bordered" id="surgery_sys_table">
					<thead>
						<tr class="grythead">
							<td align="center" width="350">Other Sx/Procedures</td>
							<td align="center" width="200">Date of Procedure</td>
							<td align="center" width="150">Physician</td>
							<td align="center" width="200">Comments</td>
							<td align="center" width="100">Type</td>
							<td align="center" width="100">Status</td>
							<td align="center" width="150">SNOMED CT</td>
							<td align="center" width="50">Refusal</td>
							<td align="center" width="60">Del</td>
						</tr>
					</thead>

					<tbody>
<?php
$i = $i;
$j = 0;

foreach ($finalResArr['SYS'] as $DATA_KEY => $DATA_VAL1) {
	$i++;
	$j++;
	?>


							<tr id="sx_tr<?php echo $i; ?>">

								<!--  Ocular Sx/Procedures -->
								<td>
									<input type="hidden" name="sg_id<?php echo $i; ?>" id="sg_id<?php echo $i; ?>" value="<?php echo $DATA_VAL1['SX_ID']; ?>" >
									<input type="hidden" name="sg_occular<?php echo $i; ?>" id="sg_occular<?php echo $i; ?>" <?PHP echo $DATA_VAL1['SX_OCCULAR']; ?> value="5" />
									<input data-toggle="tooltip" data-placement="top" <?php echo ($DATA_VAL1['SX_TITLE'])?'data-original-title="'.$DATA_VAL1['SX_TITLE'].'"':''; ?> type="text" id="sx_title_text<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="sx_title_text<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_TITLE']); ?>', this, event);<?php } ?>  insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_TITLE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['SX_TITLE']; ?>" class="form-control" onChange="changeSxVal(this);" />
								</td>

								<!-- Date Of Procedure -->
								<td>
									<div class="col-sm-8">
										<div class="input-group">
											<input type="text" tabindex="<?php echo $i; ?>" name="sg_begindate<?php echo $i; ?>" id="sg_begindate<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); changeSxVal();" onChange="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); changeSxVal();" value="<?php echo $DATA_VAL1['SX_BEG_DATE']; ?>" maxlength="10" class="date-pick1  dt_surgery form-control" title="<?php echo inter_date_format(); ?>" onBlur="insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); check_sx_beg_date(this);">
											<label for="sg_begindate<?php echo $i; ?>" class="input-group-addon btn">
												<i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<input type="text" id="sg_begtime<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="sg_begtime<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $DATA_VAL1['SX_BEG_TIME']; ?>',this,event);" value="<?php echo $DATA_VAL1['SX_BEG_TIME']; ?>" class="form-control">
									</div>
								</td>

								<!-- Physician -->
								<td>
									<input type="hidden" tabindex="<?php echo $i; ?>" name="referredby_id<?php echo $i; ?>" id="referredby_id<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['referredby_id']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['referredby_id']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['referredby_id']; ?>" class="input_text_10" onChange="changeSxVal();">
									<input type="text" tabindex="<?php echo $i; ?>" name="sg_referredby<?php echo $i; ?>" id="sg_referredby<?php echo $i; ?>" onKeyUp="top.loadPhysicians(this, 'referredby_id<?php echo $i; ?>');<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_REFFERED_BY']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_REFFERED_BY']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['SX_REFFERED_BY']; ?>" class="form-control" onChange="changeSxVal();" onFocus="top.loadPhysicians(this, 'referredby_id<?php echo $i; ?>');">
								</td>

								<!-- Comments -->
								<td>
									<textarea class="form-control" tabindex="<?php echo $i; ?>" rows="1" id="sg_comments<?php echo $i; ?>" name="sg_comments<?php echo $i; ?>" onChange="changeSxVal();" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_COMMENTS']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_COMMENTS']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));"><?php echo $DATA_VAL1['SX_COMMENTS']; ?></textarea>
								</td>

								<!-- TYPE -->
								<td>
									<select class="minimal form-control" name="surgery_type<?php echo $i; ?>" id="surgery_type<?php echo $i; ?>" data-width="100%" onChange="changeSxVal();<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['proc_type']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['proc_type']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));">
										<option value="surgery" <?php echo ('surgery' == $DATA_VAL1['proc_type'] ? 'selected' : ''); ?> >Surgery</option>
										<option value="procedure" <?php echo ('procedure' == $DATA_VAL1['proc_type'] ? 'selected' : ''); ?>>Procedure</option>
										<option value="intervention" <?php echo ('intervention' == $DATA_VAL1['proc_type'] ? 'selected' : ''); ?>>Intervention</option>
									</select>
								</td>

								<td>
									<select class="minimal form-control" name="procedure_status<?php echo $i; ?>" id="procedure_status<?php echo $i; ?>" data-width="100%" onChange="changeSxVal();<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['procedure_status']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['procedure_status']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));">
										<option vlaue="">Select</option>
										<option value="pending" <?php echo ('pending' == $DATA_VAL1['procedure_status'] ? 'selected' : ''); ?>>Pending</option>
										<option value="completed" <?php echo ('completed' == $DATA_VAL1['procedure_status'] ? 'selected' : ''); ?>>Completed</option>
									</select>
								</td>
								<!-- SNOMED CT -->
								<td>
									<input type="text" tabindex="<?php echo $i; ?>" name="ccda_code<?php echo $i; ?>" id="ccda_code<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['ccda_code']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['ccda_code']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['ccda_code']; ?>" class="form-control" onChange="changeSxVal();">
								</td>
								<!-- Refusal CT -->
								<td class="text-center">
									<div class="checkbox">
										<input type="checkbox" class="checkbox" name="refusal<?php echo $i; ?>" id="refusal<?php echo $i; ?>" tabindex="<?php echo $i; ?>" <?php if ($DATA_VAL1['REFUSAL'] == '1') echo 'checked'; ?> onChange="check_refusal(<?php echo $i; ?>); insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['REFUSAL']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['REFUSAL']; ?>">
										<label for="refusal<?php echo $i; ?>">&nbsp;</label>
									</div>
								</td>
								<input type="hidden" name="refusal_reason<?php echo $i; ?>" id="refusal_reason<?php echo $i; ?>" value="<?php echo $DATA_VAL1['REFUSAL_REASON']; ?>">
								<input type="hidden" name="refusal_snomed<?php echo $i; ?>" id="refusal_snomed<?php echo $i; ?>" value="<?php echo $DATA_VAL1['REFUSAL_SNOMED']; ?>">
								
								<td align="center">
	<?php
	if ($j == count($finalResArr['SYS'])) {
        $month=$DATA_VAL1['SX_MONTH'];
        $date=$DATA_VAL1['SX_DATE'];
        $year=$DATA_VAL1['SX_YEAR'];
        
		echo '<span id="add_row_' . $i . '" class="glyphicon glyphicon-plus pointer" alt="Add More" onClick="addNewRow(' . $i . ',\'SYS\');"></span>';
	} else {
		echo '<span class="glyphicon glyphicon-remove pointer" alt="Delete" onClick="removeTableRow(\'' . $DATA_VAL1['SX_ID'] . '\', ' . $i . ');"></span>';
	}
	?>
								</td>
							</tr>

									<?php
								}
								?>	

					</tbody>
				</table>
				<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog">
					<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title" id="modal_title">Refusal Reason</h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="refusal_row" id="refusal_row" value="" >
								<input type="hidden" name="rowID" id="rowID" value="" >
								<div class="form-group">
									<label for="usrname">Refusal Reason</label>
									<textarea type="text" class="form-control" id="refusal_reason" name="refusal_reason"></textarea>
								</div>
								<div class="form-group">
									<label for="psw">Refusal Snomed</label>
									<input type="text" class="form-control" id="refusal_snomed" name="refusal_snomed">
								</div>
							</div>
							<div id="module_buttons" class="ad_modal_footer modal-footer">
								<button type="button" class="btn btn-success" onclick="check_refusal_values();">Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div> 
	
				<!-- Implantable devices List -->
				
			<div class="oculartop" id="surgery_implant_filter">
				<div class="text-left" style="font-size: 16px;color: rgb(255, 255, 255);background-color: rgb(103, 55, 130);vertical-align: middle;padding: 10px;">
					<div class="row">
						<div class="col-sm-6">
							Implantable Device
						</div>
						<div class="col-sm-6 text-right">
							<div class="form-inline"><label>&nbsp;</label>
								<select class="minimal form-control" name="filter_type" id="filter_type" onchange="filter_devices(this);">
									<option value="all">All</option>
									<option value="active" <?php echo ((isset($_REQUEST['filter']) && $_REQUEST['filter'] != '' && 'active' == $_REQUEST['filter']) ? 'selected' : ''); ?>>Active</option>
									<option value="inactive" <?php echo ((isset($_REQUEST['filter']) && $_REQUEST['filter'] != '' && 'inactive' == $_REQUEST['filter']) ? 'selected' : ''); ?>>Inactive</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
				<table class="table table-striped table-bordered" id="surgery_implant_table">
					<thead>
						<tr class="grythead">
							<td align="center" width="300">UDI Number</td>
							<td align="center" width="200">Date of Implant</td>
							<td align="center" width="150">Physician</td>
							<td align="center" width="150">Assigning Authority</td>
							<td align="center" width="200">Device Details</td>
							<td align="center" width="100">Status</td>
							<td align="center" width="150">SNOMED CT</td>
							<td align="center" width="60">Del</td>
						</tr>
					</thead>
					
					<tbody>
					<?php
					$i = $i;
					$j = 0;

					foreach ($finalResArr['IMPLANT'] as $DATA_KEY => $DATA_VAL1) {
						//pre($DATA_VAL1);
						$i++;
						$j++;
						
						//if((isset($_REQUEST['filter']) && $_REQUEST['filter'] != '' && $_REQUEST['filter'] != 'all' && $_REQUEST['filter'] != $DATA_VAL1['implant_status']) && $DATA_VAL1['implant_status'] != '') {
						//	continue;
						//}
                        
                        $implant_onclick=' implantable_devices(this, '.$i.'); ';
                        if(isset($DATA_VAL1['implant_status']) && $DATA_VAL1['implant_status']=='order' || $DATA_VAL1['implant_status']=='applied' ){
                            //$implant_onclick=' ';
                        }
                        $comment_detail_click = ' onclick="comment_detail(this, '.$DATA_VAL1['SX_ID'].');" ';
                        $implant_tooltip=' data-original-title="Click Here to view details." ';
                        if(empty($DATA_VAL1['SX_ID']) || (isset($DATA_VAL1['implant_status']) && $DATA_VAL1['implant_status']=='order' || $DATA_VAL1['implant_status']=='applied') ){
                            $comment_detail_click=' ';
                            $implant_tooltip=' ';
                        }
						?>

							<tr class="implnt_tr<?php echo $i; ?>" id="sx_tr<?php echo $i; ?>">

								<!--  Ocular Sx/Procedures -->
								<td>
									<input type="hidden" name="sg_id<?php echo $i; ?>" id="sg_id<?php echo $i; ?>" value="<?php echo $DATA_VAL1['SX_ID']; ?>" >
									<input type="hidden" name="sg_occular<?php echo $i; ?>" id="sg_occular<?php echo $i; ?>" <?PHP echo $DATA_VAL1['SX_OCCULAR']; ?> value="9" />
<!--									<input type="text" id="sx_title_text<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="sx_title_text<?php echo $i; ?>" value="<?php echo $DATA_VAL1['SX_TITLE']; ?>" class="form-control" onChange="changeSxVal(); onclick="implantable_devices(this, <?php echo $i; ?>);" />-->
									<input type="text" id="sx_title_text<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="sx_title_text<?php echo $i; ?>" onclick="<?php echo $implant_onclick; ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_TITLE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_TITLE']); ?>', this, event);<?php } ?>" value="<?php echo $DATA_VAL1['SX_TITLE']; ?>" class="form-control" onChange="changeSxVal(this);" />
								</td>

								<!-- Date Of Procedure -->
								<td>
									<div class="col-sm-8">
										<div class="input-group">
											<input type="text" tabindex="<?php echo $i; ?>" name="sg_begindate<?php echo $i; ?>" id="sg_begindate<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); changeSxVal();" onChange="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); changeSxVal();" value="<?php echo $DATA_VAL1['SX_BEG_DATE']; ?>" maxlength="10" class="date-pick1  dt_surgery form-control" title="<?php echo inter_date_format(); ?>" onBlur="insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_BEG_DATE']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>')); check_sx_beg_date(this);">
											<label for="sg_begindate<?php echo $i; ?>" class="input-group-addon btn">
												<i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<input type="text" id="sg_begtime<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="sg_begtime<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $DATA_VAL1['SX_BEG_TIME']; ?>',this,event);" value="<?php echo $DATA_VAL1['SX_BEG_TIME']; ?>" class="form-control">
									</div>
								</td>

								<!-- Physician -->
								<td>
									<input type="hidden" tabindex="<?php echo $i; ?>" name="referredby_id<?php echo $i; ?>" id="referredby_id<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['referredby_id']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['referredby_id']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['referredby_id']; ?>" class="input_text_10" onChange="changeSxVal();">
									<input type="text" tabindex="<?php echo $i; ?>" name="sg_referredby<?php echo $i; ?>" id="sg_referredby<?php echo $i; ?>" onKeyUp="top.loadPhysicians(this, 'referredby_id<?php echo $i; ?>');<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_REFFERED_BY']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_REFFERED_BY']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['SX_REFFERED_BY']; ?>" class="form-control" onChange="changeSxVal();" onFocus="top.loadPhysicians(this, 'referredby_id<?php echo $i; ?>');">
								</td>
								<td>
									<input type="text" tabindex="<?php echo $i; ?>" name="assign_auth<?php echo $i; ?>" id="assign_auth<?php echo $i; ?>" value="<?php echo $DATA_VAL1['assigning_authority_UDI']; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['assigning_authority_UDI']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['assigning_authority_UDI']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" class="form-control" onChange="changeSxVal();">
								</td>

								<!-- Comments -->
								<td>
                                    <textarea data-toggle="tooltip" data-placement="top" <?php echo $implant_tooltip; ?> class="form-control" tabindex="<?php echo $i; ?>" rows="1" id="sg_comments<?php echo $i; ?>" name="sg_comments<?php echo $i; ?>" <?php echo $comment_detail_click;?> onChange="changeSxVal();" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['SX_COMMENTS']); ?>', this, event);<?php } ?>insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['SX_COMMENTS']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));"><?php echo $DATA_VAL1['SX_COMMENTS']; ?></textarea>
								</td>

								<!-- TYPE -->
								<td>
									<select class="minimal form-control" name="surgery_type<?php echo $i; ?>" id="surgery_type<?php echo $i; ?>" data-width="100%" onChange="changeSxVal();<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['proc_type']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['proc_type']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));">
										<option vlaue="">Select</option>
										<option value="active" <?php echo ('active' == $DATA_VAL1['implant_status'] ? 'selected' : ''); ?> >Active</option>
										<option value="inactive" <?php echo ('inactive' == $DATA_VAL1['implant_status'] ? 'selected' : ''); ?>>Inactive</option>
                                        <option value="order" <?php echo ('order' == $DATA_VAL1['implant_status'] ? 'selected' : ''); ?>>Order</option>
										<option value="applied" <?php echo ('applied' == $DATA_VAL1['implant_status'] ? 'selected' : ''); ?>>Applied</option>
									</select>
								</td>

								<!-- SNOMED CT -->
								<td>
									<input type="text" tabindex="<?php echo $i; ?>" name="ccda_code<?php echo $i; ?>" id="ccda_code<?php echo $i; ?>" onKeyUp="<?php if ($_REQUEST['callFrom'] <> 'WV') { ?>chk_change('<?php echo addslashes($DATA_VAL1['ccda_code']); ?>', this, event);<?php } ?> insertSxProIdVizChange('<?php echo addslashes($DATA_VAL1['ccda_code']); ?>', this, event, document.getElementById('sg_id<?php echo $i; ?>'));" value="<?php echo $DATA_VAL1['ccda_code']; ?>" class="form-control" onChange="changeSxVal();">
								</td>
								<td align="center">
	<?php
	if ($j == count($finalResArr['IMPLANT'])) {
        $month=$DATA_VAL1['SX_MONTH'];
        $date=$DATA_VAL1['SX_DATE'];
        $year=$DATA_VAL1['SX_YEAR'];
        
		echo '<span id="add_row_' . $i . '" class="glyphicon glyphicon-plus pointer" alt="Add More" onClick="addNewRow(' . $i . ',\'IMPLANT\');"></span>';
	} else {
		echo '<span class="glyphicon glyphicon-remove pointer" alt="Delete" onClick="removeTableRow(\'' . $DATA_VAL1['SX_ID'] . '\', ' . $i . ');"></span>';
	}
	?>
								</td>
							</tr>

									<?php
								}
								?>	

					</tbody>
				</table>
				
			</div>  

			<div class="clearfix"></div>

		</div>
<?php
if ($_REQUEST["callFrom"] == 'WV') {
	?>
			<div class="col-xs-12 panel-footer ad_modal_footer" id="module_buttons">
				<div class="row text-center">
					<input type="submit" id="btSaveSxPro" name="btSaveSxPro" class="btn btn-success" value="Done" />
					<input type="button" id="btClose" name="btClose" class="btn btn-danger" value="Cancel" onClick="window.close();" />
				</div>
	       	</div>
	        <div class="clearfix"></div>
			<?php
		}
		?>
		<input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $i; ?>">

	</form>

</div>


<?php include_once'implantable_devices.php'; ?>
<?php
$pkIdAuditTrail = join('-',$pkIdAuditTrailArr);

//--- GET POLICY STATUS FOR AUDIT TRIAL ----
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
$patient_id = $_SESSION["patient"];

if($policyStatus == 1 and $pkIdAuditTrailID != '' and isset($_SESSION['Patient_Viewed']) === true){
	
	$opreaterId = $_SESSION["authId"];												 
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_SxProcedures = array();
	$arrAuditTrailView_SxProcedures[0]['Pk_Id'] = $pkIdAuditTrailID;
	$arrAuditTrailView_SxProcedures[0]['Table_Name'] = 'lists';
	$arrAuditTrailView_SxProcedures[0]['Action'] = 'view';
	$arrAuditTrailView_SxProcedures[0]['Operater_Id'] = $_SESSION['authId'];
	$arrAuditTrailView_SxProcedures[0]['Operater_Type'] = getOperaterType($_SESSION['authId']);
	$arrAuditTrailView_SxProcedures[0]['IP'] = $ip;
	$arrAuditTrailView_SxProcedures[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_SxProcedures[0]['URL'] = $URL;
	$arrAuditTrailView_SxProcedures[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_SxProcedures[0]['OS'] = $os;
	$arrAuditTrailView_SxProcedures[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_SxProcedures[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_SxProcedures[0]['Filed_Label'] = 'Surgeries Data';
	$arrAuditTrailView_SxProcedures[0]['Category_Desc'] = 'surgeries';
	$arrAuditTrailView_SxProcedures[0]['Old_Value'] = $pkIdAuditTrail;
	$arrAuditTrailView_SxProcedures[0]['pid'] = $patient_id;

	$patientViewed = $_SESSION['Patient_Viewed'];
	if(is_array($patientViewed) && $patientViewed["Medical History"]["Sx_Procedures"] == 0){
		if($policyStatus == 1){
			auditTrail($arrAuditTrailView_SxProcedures,$mergedArray,0,0,0);
			$patientViewed["Medical History"]["Sx_Procedures"] = 1;
			$_SESSION['Patient_Viewed'] = $patientViewed;
		}
	}
}

// *** CLS Alerts Calls ***
if (trim($_SESSION['alertShowForThisSession']) != "Cancel") {
	require_once($GLOBALS['srcdir'] . "/classes/CLSAlerts.php");
	$OBJPatSpecificAlert = new CLSAlerts();
	$alertToDisplayAt = "admin_specific_chart_note_med_hx";
	echo $getAdminAlert = $OBJPatSpecificAlert->getAdminAlert($_SESSION['patient'], $alertToDisplayAt, $form_id, "350px");
	$alertToDisplayAt = "patient_specific_chart_note_med_hx";
	echo $getPatSpecificAlert = $OBJPatSpecificAlert->getPatSpecificAlert($_SESSION['patient'], $alertToDisplayAt, "350px");
	echo $autoSetDivLeftMargin = $OBJPatSpecificAlert->autoSetDivLeftMargin("140", "265");
	echo $autoSetDivTopMargin = $OBJPatSpecificAlert->autoSetDivTopMargin("250", "30");
	echo $writeJS = $OBJPatSpecificAlert->writeJS();
    $curDate = date(phpDateFormat());
}
?>
<script>
    var date_global_format = '<?php echo json_encode($GLOBALS["date_format"]); ?>'.replace(/\"/g, "");
    var curDate = '<?php echo $curDate;?>';

    var dateOptions = "<?php echo $date;?>";
    var monthOptions = "<?php echo $month;?>";
    var yearOptions = "<?php echo $year;?>";

	function implantable_devices(obj, rowid) {
		$('#device_dtl').hide();
		$('#show_detail').show('fast');
		$('#udi_num').val(obj.value);		
		$('#rowid').val(obj.id+'::'+rowid);	
		var strValue = $('#hidSXProIdVizChange').val();
		var intMedId = ($('#sg_id'+rowid).val()) ? $('#sg_id'+rowid).val() : "";
		if(strValue.search(intMedId) < 0){
			strValue = strValue + intMedId + ",";
		}		
		$('#hidSXProIdVizChange').val(strValue);
				
		$('#implantable_device').modal({show: 'true'});		
	}
	
	function filter_devices(elem) {
		var option = elem.value;
		document.location.href = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/index.php?showpage=sx_procedures&filter="+option;
	}
	
</script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/sx_datepicker/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/sx_datepicker/sx_proc_calendar_layout.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/sx_procedure.js"></script>
<script>
	self.focus();
	var callFrom = document.getElementById('callFrom').value;
	var sx_typeahead = <?php echo json_encode($sx->sx_typehead()); ?>;
	var phrases_typeahead = <?php echo json_encode($sx->phrases); ?>;
	var vocabulary_sx = <?php echo json_encode($sx->vocabulary); ?>;
	bind_sx_typeahead();
	if (callFrom !== 'WV')  top.btn_show("SX");
	if( callFrom == 'WV') { // to close surgeries popup
		if( $("#surgeryDiv",window.opener.top.fmain.document).length > 0 ) {
			$("#surgeryDiv",window.opener.top.fmain.document).hide();
		}
	}
	$(function(){
	$("#no_sur_chk").triggerHandler('change');
	});
</script>


<script>
jQuery.curCSS = function(element, prop, val) {
    return jQuery(element).css(prop, val);
};

function date_picker1(){
		var now = new Date();	

		$('.date-pick1').DatePicker({
				format:top.jquery_date_format,
				date: $(this.id).val(),
				current: $(this.id).val(),
				starts: 1,
				position: 'right',
				view: 'years',
				eleID: '',
				onBeforeShow: function(){
					eleID = this.id;
					//options.eleID = this.id;
					//$('#inputDate').DatePickerSetDate($('#inputDate').val(), true);
				},
				onChange: function(formated, dates){
					//var el = $(ev.target);
					var options = $(this).data('datepicker');
                    
                    console.log(options);
					//alert(options.currentMonth+"-"+options.currentDate+"-"+options.currentYear)
					var arrDate = new Array();
					if(options.currentYear != ''){
						arrDate[2] = (options.currentYear<10)?'0'+options.currentYear:options.currentYear;
						if(options.currentMonth != ''){
							//options.currentMonth = parseInt(options.currentMonth)+1;
							arrDate[0] = (options.currentMonth<10)?'0'+options.currentMonth:options.currentMonth;
							if(options.currentDate != ''){
								arrDate[1] = (options.currentDate<10)?'0'+options.currentDate:options.currentDate;
							}
						}
						arrDate = arrDate.filter(function(e){return e});
						$("#"+eleID).val(arrDate.join('-'));
					}else
						$("#"+eleID).val(formated);

				},
				onRender: function(date) {
					return {
					disabled: (date.valueOf() > now.valueOf())
					}
				}
		});
        
}$(document).ready(function(e) {
    $(".dt_surgery").focus(function(){
		if($.trim($(this).val())==""){
			$(this).val(curDate);
		}
	});
	
	$("#td_def_vw input[type=radio]").on("click", function(){
		var s = $("#td_def_vw :checked").val();
		window.location.replace("?showpage=sx_procedures&defVw="+s);
	});    
});

    
</script>
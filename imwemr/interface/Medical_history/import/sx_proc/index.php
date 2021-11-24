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

$pid = $import_obj->patient_id;

if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'save_sx_proc'){
	$save_status = $import_obj->save_sx_proc_data($_REQUEST);
	if($save_status != '' && $save_status > 0){
		if(empty($direct) == false){
			header("Location:import_ccda.php?direct_save=yes&showpage=".$_REQUEST['showpage']."&pt_id=".$_REQUEST['pt_id']."&xml_id=".$_REQUEST['xml_id']);
		}else{
			header("Location:index.php?showpage=".$_REQUEST['showpage']."");
		}
	}
}

$height = $_SESSION['wn_height'] - 300;
$count = 0;
$sx_sys_count = 0;
//Getting all medications data from DB
$type = '5,6';
$sx_full_data = $import_obj->get_all_sx_proc_data($type,$_REQUEST,$direct);
$sx_ocu_data_arr = $sx_sys_data_arr = array();

if(count($sx_full_data['finalResArr']['OCU']) > 0){
	foreach($sx_full_data['finalResArr']['OCU'] as $obj){
		$sx_ocu_data_arr[] = $obj;
	}
}

if(count($sx_full_data['finalResArr']['SYS']) > 0){
	foreach($sx_full_data['finalResArr']['SYS'] as $obj){
		$sx_sys_data_arr[] = $obj;
	}
}

//Getting all allergies data based on XML ids
$medication_xml_data = $import_obj->get_allergies_data_xml($_REQUEST);
extract($medication_xml_data);

$complete_file_path = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').'/xml/refphy/';
?>
<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<form action="<?php echo $sx_full_data['form_save_action']; ?>" method="post" name="frm" id="frm" onsubmit="return check_form()">
            	<input type="hidden" name="pt_id" id="pt_id" value="<?php echo $pid;?>" />
                <input type="hidden" name="xml_id" id="xml_id" value="<?php echo $_REQUEST['xml_id'];?>" />
				<table class="table table-bordered table-striped table-condensed">
					<?php 
						$sx_ocu_header = '
							<tr class="grythead">
								<th rowspan="2">
									<div class="checkbox">
										<input type="checkbox" name="xml_chk" id="xml_chk" onclick="select_xml_chk(&quot;chk_xml_mdhx&quot;,this);" autocomplete="off">
										<label for="xml_chk"></label>	
									</div>
								</th>
								<th rowspan="2">Ocular Sx/Procedures</th>
								<th colspan="3">Site</th>
								<th rowspan="2">Date of Procedure</th>
								<th rowspan="2">Physician</th>
								<th rowspan="2">Comments</th>
								<th rowspan="2">Type</th>
								<th rowspan="2">SNOMED CT</th>
							</tr>
							<tr class="grythead">
								<th>OD</th>
								<th>OS</th>
								<th>OU</th>
							</tr>
							';
							
						$sx_sys_header = '
							<tr class="grythead">
								<th></th>
								<th colspan="4">Other Sx/Procedures</th>
								<th>Date of Procedure</th>
								<th>Physician</th>
								<th>Comments</th>
								<th>Type</th>
								<th>SNOMED CT</th>
							</tr>
							';		
						echo $sx_ocu_header;	
						if(count($sx_ocu_data_arr) > 0){
							foreach($sx_ocu_data_arr as $row){
								?>
								<tr>
									<td>
										<div class="checkbox">
											<input type="checkbox" name="chk_box[]" id="checkbox_<?php echo $count; ?>" value="<?php echo $count;?>"  class="chk_xml_mdhx" />
											<label for="checkbox_<?php echo $count; ?>"></label>	
										</div>
										<input type="hidden" name="hid_list_id[]" id="checkbox" value="<?php echo $row['SX_ID'];?>"> 
										<input type="hidden" name="hid_type_id[]" id="checkbox" value="6"> 
										<input type="hidden" name="ccda_code_system[]" value="<?php echo $row['ccda_code_system']; ?>"> 
										<input type="hidden" name="ccda_code_system_name[]" value="<?php echo $row['ccda_code_system_name']; ?>"> 
								   </td>
									<td>
										<input  type="text" class="form-control" name="sx_title[]"  value="<?php echo $row['SX_TITLE'];?>">
									</td>
									<td id="tdSite<?php echo $count;?>">
										<div class="radio radio-inline">
											<input type="radio" name="sx_occular_<?php echo $count; ?>" id="md_ou<?php echo $count;?>" value="3" <?php echo ($row['MED_SITE'] == "3")?"checked":"";?> >
											<label for="md_ou<?php echo $count;?>"></label>
										</div>
									</td>
									<td>	
										<div class="radio radio-inline">
											<input type="radio" name="sx_occular_<?php echo $count; ?>" id="md_od<?php echo $count;?>" value="2"  <?php echo ($row['MED_SITE'] == "2")?"checked":"";?>>
											<label for="md_od<?php echo $count;?>"></label>
										</div>
									</td>
									<td>		
										<div class="radio radio-inline">
											<input type="radio" name="sx_occular_<?php echo $count; ?>" id="md_os<?php echo $count;?>" value="1"  <?php echo ($row['MED_SITE'] == "1")?"checked":"";?> >
											<label for="md_os<?php echo $count;?>"></label>
										</div>
									</td>
									<td>
										<div class="input-group">
											<input type="text" id="sx_beg_date_<?php echo $count; ?>" class="form-control datepicker" name="sx_beg_date[]" value="<?php echo $row['SX_BEG_DATE'];  ?>">
											<label for="sx_beg_date_<?php echo $count; ?>" class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>
										</div>
									</td>
									<td>
										<input type="text" name="sx_reff_name[]" value="<?php echo $row['SX_REFFERED_BY']; ?>" class="form-control" onkeyup="loadPhysicians(this,'referredby_id_<?php echo $count;?>','<?php echo $complete_file_path; ?>');">
										<input type="hidden" id="referredby_id_<?php echo $count; ?>" name="sx_reff_id[]" value="<?php echo $row['referredby_id']; ?>">
									</td>
									<td>  	
										<textarea class="form-control" name="sx_comments[]" rows="1"><?php echo $row['SX_COMMENTS']; ?></textarea>
									</td>
									<td>
										<select class="selectpicker" name="sx_surgery_type[]" id="surgery_type<?php echo $count;?>" data-width="100%">
											<option value="surgery" <?php echo ('surgery' == $row['proc_type'] ? 'selected' : '');?> >Surgery</option>
											<option value="procedure" <?php echo ('procedure' == $row['proc_type'] ? 'selected' : '');?>>Procedure</option>
											<option value="intervention" <?php echo ('intervention' == $row['proc_type'] ? 'selected' : '');?>>Intervention</option>
										</select>
									</td>
									<td>
										<input type="text" name="sx_ccda_code[]" value="<?php echo $row['ccda_code']; ?>" class="form-control">       
									</td>
								</tr>	
								<?php
								$count++;
							}
						}else{
							echo '<tr><td colspan="10" class="text-center">No record</td></tr>';
						}
						
						
						//SYS Procedures
						echo $sx_sys_header;
						if(count($sx_sys_data_arr) > 0){
							foreach($sx_sys_data_arr as $row){
								?>
								<tr>
									<td>
										<div class="checkbox">
											<input type="checkbox" name="chk_box[]" id="checkbox_<?php echo $count;?>" value="<?php echo $count;?>"  class="chk_xml_mdhx" />
											<label for="checkbox_<?php echo $count;?>"></label>	
										</div>
										<input type="hidden" name="hid_list_id[]" id="checkbox" value="<?php echo $row['SX_ID'];?>"> 
										<input type="hidden" name="hid_type_id[]" id="checkbox" value="5"> 
										<input type="hidden" name="ccda_code_system[]" value="<?php echo $row['ccda_code_system']; ?>"> 
										<input type="hidden" name="ccda_code_system_name[]" value="<?php echo $row['ccda_code_system_name']; ?>"> 
								   </td>
									<td colspan="4">
										<input  type="text" class="form-control" name="sx_title[]"  value="<?php echo $row['SX_TITLE'];?>">
									</td>
									<td>
										<div class="input-group">
											<input type="text" id="sx_beg_date[]" class="form-control datepicker" name="sx_beg_date[]" value="<?php echo $row['SX_BEG_DATE'];  ?>">
											<label for="sx_beg_date[]" class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>
										</div>
									</td>
									<td>
										<input type="text" name="sx_reff_name[]" value="<?php echo $row['SX_REFFERED_BY']; ?>" class="form-control" onkeyup="loadPhysicians(this,'referredby_id_<?php echo $count;?>','<?php echo $complete_file_path; ?>');">
										<input type="hidden" id="referredby_id_<?php echo $count; ?>" name="sx_reff_id[]" value="<?php echo $row['referredby_id']; ?>">
									</td>
									<td>  	
										<textarea class="form-control" name="sx_comments[]" rows="1"><?php echo $row['SX_COMMENTS']; ?></textarea>
									</td>
									<td>
										<select class="selectpicker" name="sx_surgery_type[]" id="surgery_type<?php echo $sx_sys_count;?>" data-width="100%">
											<option value="surgery" <?php echo ('surgery' == $row['proc_type'] ? 'selected' : '');?> >Surgery</option>
											<option value="procedure" <?php echo ('procedure' == $row['proc_type'] ? 'selected' : '');?>>Procedure</option>
											<option value="intervention" <?php echo ('intervention' == $row['proc_type'] ? 'selected' : '');?>>Intervention</option>
										</select>
									</td>
									<td>
										<input type="text" name="sx_ccda_code[]" value="<?php echo $row['ccda_code']; ?>" class="form-control">       
									</td>
								</tr>	
								<?php
								$count++;
							}
						}else{
							echo '<tr><td colspan="10" class="text-center">No record</td></tr>';
						}
					?>		
				</table>
				<?php if(count($return_med_arr) > 0 && !isset($_REQUEST['page_request'])){
						$j = 0;
						foreach($return_med_arr as $key => $val){
							$file_details = explode('~~~',$key);
							?>
							<table class="table table-bordered table-striped table-condensed">
								<tr class="purple_bar">
									<th colspan="14">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="xml_chk" id="xml_chk_<?php echo $key; ?>" onclick='select_xml_chk("chk_xml_<?php echo $file_details[1]?>",this);'/>
											<label for="xml_chk_<?php echo $key; ?>">Source of Sx/Procedures: &nbsp;<?php echo $file_details[0];?></label>	
										</div>	
									</th>
								</tr>
								<tr class="grythead">
									<th rowspan="2"></th>
									<th rowspan="2">Ocular Sx/Procedures</th>
									<th colspan="3">Site</th>
									<th rowspan="2">Date of Procedure</th>
									<th rowspan="2">Physician</th>
									<th rowspan="2">Comments</th>
									<th rowspan="2">Type</th>
									<th rowspan="2">SNOMED CT</th>
								</tr>
								<tr class="grythead">
									<th>OD</th>
									<th>OS</th>
									<th>OU</th>
								</tr>
								
							<?php 
							if(count($val) == 0){
								echo '<tr><td colspan="10" class="text-center">No record found</td></tr>';
							}else{	
								foreach($val as $sx_list){ 	
								?>
									<tr>
										<td>
											<div class="checkbox">
												<input type="checkbox" name="chk_box[]" class="chk_xml_<?php echo $file_details[1]; ?>" id="checkbox_<?php echo $count; ?>" value="<?php echo $count;?>"/>
												<label for="checkbox_<?php echo $count; ?>"></label>	
											</div>
											<input type="hidden" name="hid_list_id[]" value=""> 
											<input type="hidden" name="hid_type_id[]" value="0"> 
											<input type="hidden" name="ccda_code_system[]" value="<?php echo $sx_list['ccda_code_system']; ?>"> 
											<input type="hidden" name="ccda_code_system_name[]" value="<?php echo $sx_list['ccda_code_system_name']; ?>"> 
									   </td>
										<td>
											<input  type="text" class="form-control" name="sx_title[]"  value="<?php echo $sx_list['name'];?>">
										</td>
										<td id="tdSite<?php echo $count;?>">
											<div class="radio radio-inline">
												<input type="radio" name="sx_occular_<?php echo $count; ?>" id="md_ou<?php echo $count;?>" value="3">
												<label for="md_ou<?php echo $count;?>"></label>
											</div>
										</td>
										<td>	
											<div class="radio radio-inline">
												<input type="radio" name="sx_occular_<?php echo $count; ?>" id="md_od<?php echo $count;?>" value="2">
												<label for="md_od<?php echo $count;?>"></label>
											</div>
										</td>
										<td>		
											<div class="radio radio-inline">
												<input type="radio" name="sx_occular_<?php echo $count; ?>" id="md_os<?php echo $count;?>" value="1">
												<label for="md_os<?php echo $count;?>"></label>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="text" id="sx_beg_date_<?php echo $count; ?>" class="form-control datepicker" name="sx_beg_date[]" value="<?php echo get_date_format($sx_list['date']);  ?>">
												<label for="sx_beg_date_<?php echo $count; ?>" class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" name="sx_reff_name[]" value="<?php echo $sx_list['provider']; ?>" class="form-control" onkeyup="loadPhysicians(this,'referredby_id_<?php echo $count;?>','<?php echo $complete_file_path; ?>');">
											<input type="hidden" id="referredby_id_<?php echo $count;?>" name="sx_reff_id[]" value="">
											
										</td>
										<td>  	
											<textarea class="form-control" name="sx_comments[]" rows="1"></textarea>
										</td>
										<td>
											<select class="selectpicker" name="sx_surgery_type[]" id="surgery_type<?php echo $count;?>" data-width="100%">
												<option value="surgery" >Surgery</option>
												<option value="procedure" >Procedure</option>
												<option value="intervention" >Intervention</option>
											</select>
										</td>
										<td>
											<input type="text" name="sx_ccda_code[]" value="<?php echo $sx_list['ccda_code']; ?>" class="form-control">       
										</td>
									</tr>		
									<?php
									$j++;$count++;
								}
							}
					?>
						</table>
					<?php }
					} ?>	
			</form>	
		</div>
	</div>
</div>
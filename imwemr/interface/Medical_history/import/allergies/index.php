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
$height = $_SESSION['wn_height'] - 300;
$count = 0;
//pre($_POST,1);
//Getting all allergies data from DB and also retrives the Merge data based on request
	// if first time page is loaded => It will bring data from DB
	// if consolidate request is made => It will bring data based on checked values	
$type = '3,7';
$allergy_full_data = $import_obj->get_all_allergies_data($type,$_REQUEST,$direct);
extract($allergy_full_data);

//Getting all allergies data based on XML ids and consolidate request is not made
$allergies_xml_data = $import_obj->get_allergies_data_xml($_REQUEST);
extract($allergies_xml_data);

//Setting CLS Alerts
echo $cls_alerts; 

if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'save_allergies'){
	$save_status = $import_obj->save_allergies_data($_REQUEST);
	if($save_status != '' && $save_status > 0){
		log_ccd_incorporaton(trim(str_replace(',','',$_REQUEST['xml_id'])),$pid,$_REQUEST['showpage']);
		if(empty($direct) == false){
			header("Location:import_ccda.php?direct_save=yes&showpage=".$_REQUEST['showpage']."&pt_id=".$_REQUEST['pt_id']."&xml_id=".$_REQUEST['xml_id']);
		}else{
			header("Location:index.php?showpage=".$_REQUEST['showpage']."");
		}
	}
}
?>
<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<form action="<?php echo $form_save_action; ?>" method="post" name="frm" id="frm" onsubmit="return check_form()">
				<input type="hidden" name="pt_id" id="pt_id" value="<?php echo $pid;?>" />
                <input type="hidden" name="xml_id" id="xml_id" value="<?php echo $_REQUEST['xml_id'];?>" />
                <table class="table table-striped table-bordered table-condensed">
					<tr class="grythead">
						<th>
							<div class="checkbox">
								<input type="checkbox" name="xml_chk" id="xml_chk" onclick='select_xml_chk("chk_xml_mdhx",this);'/>
								<label for="xml_chk"></label>	
							</div>
						</th>
						<th>Drug</th>
						<th>Name</th>
						<th>Begin Date</th>
						<th>Reactions / Comments</th>
                        <th>Severity</th>
						<th>RxNorm Code</th>
						<?php
							//if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'merge'){
								?>
								<th>Status</th>
								<?php
						//	}
						
						?>	
						<th><?php if($_REQUEST['page_request']!='merge'){ echo 'Last Modified';}else {echo 'Review on';}?></th>		
					</tr>
					<!-- DB Records -->
					<?php
						if(count($allergy_data) > 0){
							foreach($allergy_data as $obj){
								$row_count_val = $count;
								if($_REQUEST['page_request'] == 'merge'){
									$row_count_val = $count;
								}	
								?>
								<tr >
									<td>
										<div class="checkbox">
											<input type="checkbox" name="chk_allergies[]" id="checkbox_<?php echo $count;?>" value="<?php echo $row_count_val;?>" class="chk_xml_mdhx" <?php if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'merge'){echo 'checked';}?>>	
											<label for="checkbox_<?php echo $count;?>"></label>
										</div>
										<input type="hidden" name="hid_list_id[<?php echo $row_count_val; ?>]" value="<?php echo $obj['id'];?>">
									</td>
									<td>
										<select name="ag_occular_drug[]" class="selectpicker" data-width="100%">
											<option value="fdbATDrugName" <?php echo ($obj['ag_occular_drug'] == "fdbATDrugName")?"selected":"";?>>Drug</option>
											<option value="fdbATIngredient" <?php echo ($obj['ag_occular_drug'] == "fdbATIngredient")?"selected":"";?>>Ingredient</option>
											<option value="fdbATAllergenGroup" <?php echo ($obj['ag_occular_drug'] == "fdbATAllergenGroup")?"selected":"";?>>Allergen</option>
										</select>
									</td>

									<td>
										<input type="text" class="form-control" name="title[]"  value="<?php echo $obj['title'];?>">
									</td>
									<td>
										<div class="input-group">
											<input type="text" id="beg_date_<?php echo $counter; ?>" name="begdate[]" value="<?php echo $obj['begdate'];?>" class="datepicker form-control allergy_bg_date">
											<label for="beg_date_<?php echo $counter; ?>" class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>
										</div>
										
									</td>
									<td>
										<textarea class="form-control body_c" rows="1" name="comments[]"><?php echo $obj['comments'];?></textarea>
                                        <input type="hidden" name="reaction_code[]" value="<?php echo $obj['reaction_code'];?>" />
									</td>
                                    <td>
										<input type="text"  name="severity[]" value="<?php echo ucwords(strtolower($obj['severity']));?>" class="allergy_bg_date form-control"  maxlength="25">
									</td>

									<td>
										<input type="text" class="form-control"  name="ccda_code[]" value="<?php echo $obj['ccda_code'];?>" class="allergy_bg_date">
									</td>
									<?php
										//if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'merge'){
											?>
											<td>
												 <input type="text"  name="status[<?php echo $count;?>]" value="<?php echo $obj['status'];?>" class="allergy_bg_date form-control">
											</td>
											<?php
										//}
									?>	
									<td nowrap="nowrap">
										<?php
											$date=date_create($obj['timestamp']);
											echo date_format($date,"m-d-Y h:i A");
											//echo $import_obj->get_last_modified($obj['id'],'Allergies',$_REQUEST);
										?>
									</td>
								</tr>
								<?php
								$count++;	
							}
						}else{
							if(!isset($_REQUEST['page_request'])){
								echo "<tr><td colspan='9' class='text-center'> No records found</td></tr>";
							}else{
								echo "<tr><td colspan='9' class='text-center'> No records found</td></tr>";
							}
							
						}
					?>	
				</table>
				
				<!-- XML Records -->
				<?php if(count($return_med_arr) > 0){ 
						$j = 0;
						foreach($return_med_arr as $key => $val){
							$file_details = explode('~~~',$key);
							?>
							<table class="table table-bordered table-striped ">
								<tr class="purple_bar">
									<th colspan="8">
										<div class="checkbox">
											<input type="checkbox" name="xml_chk" id="xml_chk_<?php echo $key; ?>" onclick='select_xml_chk("chk_xml_<?php echo $file_details[1]?>",this);'/>
											<label for="xml_chk_<?php echo $key; ?>">Source of Allergy List: &nbsp;<?php echo $file_details[0];?></label>	
										</div>	
									</th>
								</tr>
								<tr class="grythead">	
									<th></th>
									<th>Drug</th>
									<th>Name</th>
									<th>Begin Date</th>
									<th>Reactions / Comments</th>
                                    <th>Severity</th>
									<th>RxNorm Code</th>
									<th>Status</th>
								</tr>
							<?php 
							if(count($val) == 0){
								echo '<tr><td colspan="7" class="text-center">No record found</td></tr>';
							}else{	
								foreach($val as $obj){ 	?>
									<tr id="tblag_<?php print $j; ?>">
										<td>
											<div class="checkbox">
												<input type="checkbox" name="chk_allergies[]" id="checkbox_<?php echo $count; ?>" value="<?php echo $count;?>" class="chk_xml_<?php echo $file_details[1]?>">
												<label for="checkbox_<?php echo $count; ?>"></label>	
											</div>
											<input type="hidden" name="ccda_code_system[<?php echo $count;?>]"  value="<?php echo $obj['ccda_code_system'];?>">
											<input type="hidden" name="ccda_code_system_name[<?php echo $count;?>]" value="<?php echo $obj['ccda_code_system_name'];?>">
										</td>
										<td>
											<select name="ag_occular_drug[]" class="selectpicker" data-width="100%">
												<option value="fdbATDrugName" <?php echo ($obj['ag_occular_drug'] == "fdbATDrugName")?"selected":"";?>>Drug</option>
												<option value="fdbATIngredient" <?php echo ($obj['ag_occular_drug'] == "fdbATIngredient")?"selected":"";?>>Ingredient</option>
												<option value="fdbATAllergenGroup" <?php echo ($obj['ag_occular_drug'] == "fdbATAllergenGroup")?"selected":"";?>>Allergen</option>
											</select>
										</td>
										<td>
											<input type="text" class="form-control" name="title[]"  value="<?php echo $obj['title'];?>">
										</td>
										<td>
											<input type="text"  name="begdate[]" value="<?php echo get_date_format($obj['begdate'],'yyyy-mm-dd');?>"  class="datepicker allergy_bg_date form-control"  maxlength="10">                                    
										</td>
										<td>
											<textarea class="form-control" rows="1" name="comments[]"><?php echo $obj['comments'];?></textarea>
                                            <input type="hidden" name="reaction_code[]" value="<?php echo $obj['reaction_code'];?>" />
										</td>
										<td>
											<input type="text"  name="severity[]" value="<?php echo ucwords(strtolower($obj['severity']));?>" class="allergy_bg_date form-control"  maxlength="25">
										</td>
										<td>
											<input type="text"  name="ccda_code[]" value="<?php echo $obj['ccda_code'];?>" class="allergy_bg_date form-control"  maxlength="10">
										</td>
										<td>
											<input type="text"  name="status[<?php echo $count;?>]" value="<?php echo $obj['status'];?>" class="allergy_bg_date form-control"  maxlength="10">                                    
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

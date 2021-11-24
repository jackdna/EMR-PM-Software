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
/*medication Import main controller file*/
$pid = $import_obj->patient_id;
$height = $_SESSION['wn_height'] - 300;
$count = 0;
//Getting all medications data from DB
$type = '1,4';
$medications_full_data = $import_obj->get_all_medications_data($type,$_REQUEST,$direct);
extract($medications_full_data);

//Getting all allergies data based on XML ids
$medication_xml_data = $import_obj->get_allergies_data_xml($_REQUEST);
extract($medication_xml_data);


if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'save_medications'){
	$save_status = $import_obj->save_medications_data($_REQUEST);
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
            
				<table class="table table-bordered table-striped table-condensed">
					<tr class="grythead">
						<th rowspan="2">
							<div class="checkbox">
								<input type="checkbox" name="xml_chk" id="xml_chk" onclick='select_xml_chk("chk_xml_mdhx",this);'/>
								<label for="xml_chk"></label>	
							</div>
						</th>
						<th rowspan="2" nowrap="nowrap">Ocular Name</th>
						<th rowspan="2">Dosage</th>
						<th nowrap colspan="4">Site
						</th>
						<th rowspan="2">Sig</th>
                        <th rowspan="2">Route</th>
						<th colspan="2">Compliant</th>
						<th rowspan="2">Begin Date </th>
						<th rowspan="2">End Date </th>
						<th rowspan="2">Comments </th>
						<th rowspan="2">RxNorm Code </th>
						<th rowspan="2"><?php if($_REQUEST['page_request']!='merge'){ echo 'Last Modified';}else {echo 'Review on';}?></th>
					</tr>
					<tr class="grythead">
						<th>OU&nbsp;</th>
						<th>OD&nbsp;</th>
						<th>OS&nbsp;</th>
						<th>PO</th>
						<th>Yes</th>
						<th>No</th>
					</tr>
					<?php 
						if(count($medications_full_data) > 0){
							foreach($medications_full_data as $row){
								?>
								<tr>
									<td>
										<div class="checkbox">
											<input type="checkbox" name="chk_box[]" id="checkbox_<?php echo $count; ?>" value="<?php echo $count;?>"  class="chk_xml_mdhx" <?php if(count($return_med_arr) == 0 && isset($_REQUEST['page_request'])){echo 'checked';} ?> />
											<label for="checkbox_<?php echo $count; ?>"></label>	
										</div>
										<input type="hidden" name="hid_list_id[]" id="checkbox" value="<?php echo $row['id'];?>"> 
								   </td>	
									<td nowrap="nowrap" class="form-inline text-left">
										<div class="checkbox" style="padding:5px!important">
											<input id="type_checkbox_<?php echo $count;?>" type="checkbox" name="chk_ocular[<?php echo $count;?>]" <?php echo ($row['type']==4)?'checked="checked"':"";?> value="4">
											<label for="type_checkbox_<?php echo $count;?>"></label>	
										</div>
										<input type="text" name="title[]" class="form-control" value="<?php echo $row['title'];?>">
									</td>
							  
									<td>
										<input  type="text" class="form-control" name="dosage[]"  value="<?php echo $row['destination'];?>">
									</td>
									<td id="tdSite<?php echo $count;?>">
										<div class="radio radio-inline">
											<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_ou<?php echo $count;?>" value="3" <?php echo ($row['sites'] == "3")?"checked":"";?> >
											<label for="md_ou<?php echo $count;?>"></label>
										</div>
									</td>
									<td>	
										<div class="radio radio-inline">
											<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_od<?php echo $count;?>" value="2"  <?php echo ($row['sites'] == "2")?"checked":"";?>>
											<label for="md_od<?php echo $count;?>"></label>
										</div>
									</td>
									<td>		
										<div class="radio radio-inline">
											<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_os<?php echo $count;?>" value="1"  <?php echo ($row['sites'] == "1")?"checked":"";?> >
											<label for="md_os<?php echo $count;?>"></label>
										</div>
									</td>
									<td>
										<div class="radio radio-inline">
											<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_po<?php echo $count;?>" value="4"  <?php echo ($row['sites'] == "4")?"checked":"";?> >
											<label for="md_po<?php echo $count;?>"></label>
										</div>
									</td>
									<td>
										<textarea class="form-control" rows="1"  name="sig[]"><?php echo $row['sig'];?></textarea>
									</td>
                                    <td>
	                                    <input type="text" id="med_route<?php echo $count; ?>"  name="med_route[]" value="<?php echo $row['med_route'];?>" class="form-control allergy_bg_date">
                                    </td>
									<td>
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="compliant[<?php echo $count;?>]" id="cmp_yes<?php echo $count;?>" value="1"  <?php echo ($row['compliant'] == "1")?"checked":"";?> onClick="chkBoxSetting('cmp_no<?php echo $count;?>');">
											<label for="cmp_yes<?php echo $count;?>"></label>	
										</div>
									</td>
									<td>  	
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="compliant[<?php echo $count;?>]" id="cmp_no<?php echo $count;?>" value="0"  <?php echo ($row['compliant'] == "0")?"checked":"";?> onClick="chkBoxSetting('cmp_yes<?php echo $count;?>');">
											<label for="cmp_no<?php echo $count;?>"></label>	
										</div>	
									</td>
									<td>
										<div class="input-group">
											<input type="text" id="begdate<?php echo $count; ?>"  name="begdate[]" value="<?php echo ($row['begdate']=="00-00-0000")?"":$row['begdate'];?>" class="datepicker form-control allergy_bg_date">
											<label for="begdate<?php echo $count; ?>" class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>	
										</div>
									</td>
									<td>
										<div class="input-group">
											<input type="text" id="enddate<?php echo $count; ?>"  name="enddate[]" value="<?php echo($row['enddate']=="00-00-0000")?"":$row['enddate'];?>" size="12" class="datepicker form-control allergy_bg_date">
											<label for="enddate<?php echo $count; ?>" class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>	
										</div>                               
									</td>
									<td>
										<textarea class="form-control" rows="1" name="comments[]"><?php echo $row['med_comments'];?></textarea>
									</td>
									<td>
										<input type="text"  name="ccda_code[]" value="<?php echo $row['ccda_code'];?>" size="6" class=" allergy_bg_date form-control">                                    
									</td>
									<td nowrap="nowrap">
										<?php
											$date=date_create($row['timestamp']);
											echo date_format($date,"m-d-Y h:i A");
											//echo $import_obj->get_last_modified($row['id'],'Medications');
										?>
									</td>
								</tr>	
								<?php
								$count++;
							}
						}else{
							echo '<tr><td colspan="16" class="text-center">No record found</td></tr>';
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
									<th colspan="15">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="xml_chk" id="xml_chk_<?php echo $key; ?>" onclick='select_xml_chk("chk_xml_<?php echo $file_details[1]?>",this);'/>
											<label for="xml_chk_<?php echo $key; ?>">Source of Medication List: &nbsp;<?php echo $file_details[0];?></label>	
										</div>	
									</th>
								</tr>
								<tr class="grythead">
									<th rowspan="2">
									</th>
									<th rowspan="2" nowrap="nowrap">Ocular Name</th>
									<th rowspan="2">Dosage</th>
									<th nowrap colspan="4">Site
									</th>
									<th rowspan="2">Sig</th>
                                    <th rowspan="2">Route</th>
									<th colspan="2">Compliant</th>
									<th rowspan="2">Begin Date </th>
									<th rowspan="2">End Date </th>
									<th rowspan="2">Comments </th>
									<th rowspan="2">RxNorm Code </th>
								</tr>
								<tr class="grythead">
									<th>OU&nbsp;</th>
									<th>OD&nbsp;</th>
									<th>OS&nbsp;</th>
									<th>PO</th>
									<th>Yes</th>
									<th>No</th>
								</tr>	
								
							<?php 
							if(count($val) == 0){
								echo '<tr><td colspan="14" class="text-center">No record found</td></tr>';
							}else{	
								foreach($val as $medication){ 	?>
									<tr>
										<td>
											<div class="checkbox">
												<input type="checkbox" name="chk_box[]" id="checkbox_<?php echo $count;?>" value="<?php echo $count;?>"  class="chk_xml_<?php echo $file_details[1]?>">
												<label for="checkbox_<?php echo $count;?>"></label>	
											</div>
											<input type="hidden" name="ccda_code_system[<?php echo $count;?>]"  value="<?php echo $medication['ccda_code_system'];?>">
											<input type="hidden" name="ccda_code_system_name[<?php echo $count;?>]" value="<?php echo $medication['ccda_code_system_name'];?>"> 
										</td>
										<td nowrap="nowrap" class="form-inline text-left">
											<div class="checkbox" style="padding-left:5px!important;">
												<input type="checkbox" id="chk_ocular[<?php echo $count;?>]" name="chk_ocular[<?php echo $count;?>]" <?php echo ($medication['type']==4)?'checked="checked"':"";?> value="4">
												<label for="chk_ocular[<?php echo $count;?>]"></label>	
											</div>
											<input type="text" name="title[]" class="form-control" value="<?php echo $medication['title'];?>">
										</td>
								  
										<td>
											<input  type="text" class="form-control" name="dosage[]"  value="<?php echo $medication['destination'];?>">
										</td>
										<td id="tdSite<?php echo $count;?>">
											<div class="radio radio-inline">
												<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_ou<?php echo $count;?>" value="3" <?php echo ($medication['sites'] == "3")?"checked":"";?> >
												<label for="md_ou<?php echo $count;?>"></label>	
											</div>
										</td>
										<td>	
											<div class="radio radio-inline">
												<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_od<?php echo $count;?>" value="2"  <?php echo ($medication['sites'] == "2")?"checked":"";?> >
												<label for="md_od<?php echo $count;?>"></label>	
											</div>	
										</td>
										<td>	
											<div class="radio radio-inline">
												<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_os<?php echo $count;?>" value="1"  <?php echo ($medication['sites'] == "1")?"checked":"";?>>
												<label for="md_os<?php echo $count;?>"></label>	
											</div>
										</td>
										<td>		
											<div class="radio radio-inline">
												<input type="radio" name="md_occular[<?php echo $count;?>]" id="md_po<?php echo $count;?>" value="4"  <?php echo ($medication['sites'] == "4")?"checked":"";?>>
												<label for="md_po<?php echo $count;?>"></label>	
											</div>
										</td>
										<td>
											<textarea class="form-control" rows="1" name="sig[]"><?php echo $medication['sig'];?></textarea>
										</td>
                                        <td>
		                                    <input type="text" id="med_route<?php echo $count; ?>"  name="med_route[]" value="<?php echo $medication['route'];?>" class="form-control allergy_bg_date">
	                                    </td>
										<td>
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="compliant[<?php echo $count;?>]" id="cmp_yes<?php echo $count;?>" value="1"  <?php echo "checked";?> onClick="chkBoxSetting('comp_no<?php echo $count;?>');">
												<label for="cmp_yes<?php echo $count;?>"></label>	
											</div>
										</td>
										<td>	
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="compliant[<?php echo $count;?>]" id="cmp_no<?php echo $count;?>" value="0"  onClick="chkBoxSetting('cmp_yes<?php echo $count;?>');">
												<label for="cmp_no<?php echo $count;?>"></label>	
											</div>
										</td>	
										<td >
											<div class="input-group">
												<input type="text" id="begdate_<?php echo $count; ?>"  name="begdate[]" value="<?php echo get_date_format($medication['begdate']);?>" class="datepicker form-control allergy_bg_date">
												<label for="begdate_<?php echo $count; ?>" class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="text" id="end_date_<?php echo $count; ?>" name="enddate[]" value="<?php echo get_date_format($medication['enddate']);?>" class="datepicker form-control allergy_bg_date">
												<label for="end_date_<?php echo $count; ?>" class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>                                    
										</td>
										<td>
											<textarea class="form-control" rows="1" name="comments[]"><?php echo $medication['med_comments'];?></textarea>
										</td>
										<td>
											<input type="text"  name="ccda_code[]" value="<?php echo $medication['ccda_code'];?>" class="form-control allergy_bg_date">                                    
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
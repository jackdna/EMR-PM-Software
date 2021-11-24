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

//Getting all problem list data from DB and also retrives the Merge data based on request
	// if first time page is loaded => It will bring data from DB
	// if consolidate request is made => It will bring data based on checked values
$prob_list_dt = $import_obj->get_all_prob_list_data($_REQUEST,$direct);
extract($prob_list_dt);

//Getting all problem list data based on XML ids
$prob_list_xml_data = $import_obj->get_allergies_data_xml($_REQUEST);
extract($prob_list_xml_data);

if(isset($_REQUEST['page_request']) && $_REQUEST['page_request'] == 'save_problem_list'){
	$save_status = $import_obj->save_problem_list_data($_REQUEST);
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
	<form action="<?php echo $form_save_action; ?>" method="post" name="frm" id="frm" onsubmit="return check_form()">
    	<input type="hidden" name="pt_id" id="pt_id" value="<?php echo $pid;?>" />
        <input type="hidden" name="xml_id" id="xml_id" value="<?php echo $_REQUEST['xml_id'];?>" />
		<table class="table table-bordered table-striped table-condensed">
			<tr class="grythead">
				<th>
					<div class="checkbox">
						<input type="checkbox" name="xml_chk" id="xml_chk" onclick='select_xml_chk("chk_xml_medhx",this);'/>
						<label for="xml_chk"></label>	
					</div>
				</th>
				<th>Onset Date</th>
				<th>Time</th>
				<th>Problem</th>
				<th>Problem Type</th>
				<th>Snomed Code</th>
				<th><?php if($_REQUEST['page_request']!='merge'){ echo 'Last Modified';}else {echo 'Review on';}?></th>	
			</tr>	
			<?php 
				if(count($prob_list_data) >0){
					foreach($prob_list_data as $row){
					?>
						<tr>
							<td>
								<div class="checkbox">
									<input type="checkbox" name="chk_import[]" id="chk_import_<?php echo $count; ?>" value="<?php echo $count;?>" class="chk_xml_medhx" <?php if(count($return_med_arr) == 0 && isset($_REQUEST['page_request'])){echo 'checked';}?>>
									<label for="chk_import_<?php echo $count; ?>"></label>
								</div>
								<input type="hidden" name="hid_id[]" id="checkbox_<?php echo $count; ?>" value="<?php echo $row['id'];?>">	
							</td>	
							<td>
								<div class="input-group">
									<input type="text" id="onset_date_<?php echo $count; ?>" name="onset_date[]" class="form-control datepicker" value="<?php echo $row['onset_date'];?>">
									<label for="onset_date_<?php echo $count; ?>" class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</label>	
								</div>
								
							</td>
							<td>
								<input type="text" name="OnsetTime[]" class="form-control" value="<?php echo ($row['OnsetTime']!="00:00:00")?$row['OnsetTime']:"";?>">
							</td>	
							<td>
								<textarea name="problem_name[]" id="problem_name" rows="1" class="form-control"><?php echo($row["problem_name"]);?></textarea>
							</td>
							<td>
								<select name="prob_type[]" class="selectpicker" data-width="100%" data-title="Select">
									<?php echo $import_obj->get_prob_list_type_arr('dropdown',$row['prob_type']); ?>	
								</select>
							</td>
							<td>
								<input type="text" name="ccda_code[]" class="form-control" value="<?php echo $row['ccda_code'];?>">
							</td>
							<td>
							<?php 
								$date=date_create($row['timestamp']);
								echo date_format($date,"m-d-Y h:i A");
								//echo $import_obj->get_last_modified_pbl($row['id'],$_REQUEST); 
							?>
							</td> 	
						</tr>
					<?php
						$count++;
					}
				}else{
					echo '<tr><td colspan="7" class="text-center">No record found</td></tr>';
				}
			?>			
		</table>
		<?php if(count($return_med_arr) > 0 && !isset($_REQUEST['page_request'])){ 
			$j = 0;
			foreach($return_med_arr as $key => $val){
				$file_details = explode('~~~',$key);
				?>
				<table class="table table-bordered table-striped ">
					<tr class="purple_bar">
						<th colspan="7">
							<div class="checkbox">
								<input type="checkbox" name="xml_chk" id="xml_chk_<?php echo $key; ?>" onclick='select_xml_chk("chk_xml_<?php echo $file_details[1]?>",this);'/>
								<label for="xml_chk_<?php echo $key; ?>">Source of Problem List: &nbsp;<?php echo $file_details[0];?></label>	
							</div>	
						</th>
					</tr>
					<tr class="grythead">
						<th></th>
						<th>Onset Date</th>
						<th>Time</th>
						<th>Problem</th>
						<th>Problem Type</th>
						<th>Snomed Code</th>
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
									<input type="checkbox" name="chk_import[]" id="chk_import<?php echo $count; ?>" value="<?php echo $count;?>" class="chk_xml_<?php echo $file_details[1]?>">
									<label for="chk_import<?php echo $count; ?>"></label>	
								</div>
								<input type="hidden" name="ccda_code_system[<?php echo $count;?>]"  value="<?php echo $obj['ccda_code_system'];?>">
								<input type="hidden" name="ccda_code_system_name[<?php echo $count;?>]" value="<?php echo $obj['ccda_code_system_name'];?>">
							</td>
							<td>
								<div class="input-group">
									<input type="text" id="onset_date_<?php echo $count; ?>" name="onset_date[]" class="form-control datepicker" value="<?php echo get_date_format($obj['onset_date'],'yyyy-mm-dd');?>">
									<label for="onset_date_<?php echo $count; ?>" class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>	
									</label>	
								</div>
								
							</td>
							<td>
								 <input type="text" name="OnsetTime[]" class="form-control" value="<?php echo ($obj['OnsetTime']!="00:00:00")?$obj['OnsetTime']:"";?>">
							</td>
							<td>
								<textarea name="problem_name[]" id="problem_name" rows="1" class="form-control" ><?php echo($obj["problem_name"]);?></textarea>                          
							</td>
							<td>
								<select name="prob_type[]" class="selectpicker" data-width="100%" data-title="Select">   
								<?php echo $import_obj->get_prob_list_type_arr('dropdown',$obj['prob_type']); ?>                                       
								</select>
							</td>
							<td>
								<input type="text" name="ccda_code[]" class="form-control" value="<?php echo $obj['ccda_code'];?>">
							</td>
							<td>
								<input type="text" name="status[]" class="form-control" value="<?php echo $obj['status'];?>">
							</td>	
						</tr>
						<?php
						$j++;$count++;
					}
				}	?>
				</table>
	<?php 	}
		} ?>
	</form>
</div>

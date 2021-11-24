<div class="col-sm-10">
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label>Template Name</label>
				<input class="form-control" data-preview-template="<?php echo $name; ?>" value="<?php echo $name; ?>" name="name" id="templateName" type="text">	
			</div>	
		</div>
		
		<div class="col-sm-3">
			<div class="form-group">
				<label>SNOMED CT</label>
				 <input id="ccda_code" name="ccda_code" type="text" class="form-control" value="<?php echo $ccda_code; ?>">
			</div>	
		</div>
		
		<div class="col-sm-3">
			<div class="form-group">
				<label>&nbsp;</label>
				<select name="doc_from" id="doc_from" class="selectpicker" data-width="100%" onChange="education_show_elem(this);">
					<option value="writeDoc"  <?php if($doc_from == 'writeDoc' || !$doc_from) { echo 'selected'; }?>>Write Document</option>
					<option value="scanDoc"   <?php if($doc_from == 'scanDoc') 	 { echo 'selected'; }?>>Scan Document</option>
					<option value="uploadDoc" <?php if($doc_from == 'uploadDoc') { echo 'selected'; }?>>Upload Document</option>
				</select>
			</div>	
		</div>
		
		<div class="col-sm-3">
			<div class="form-group">
				<label>&nbsp;</label>
				<select name="andOrCondition" id="andOrCondition" class="selectpicker" data-width="100%">
					<option value="A" <?php if($andOrCondition=='A' || !$andOrCondition) { echo 'selected'; }?>>AND ed Condition</option>
					<option value="O" <?php if($andOrCondition=='O') { echo 'selected'; }?>>OR ed Condition</option>
				</select>
			</div>	
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						<label>Cpt Code</label>
						<select name="cpt[]" id="cpt" class="selectpicker" multiple="multiple" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Select">
							<?php 
								$cpt_sel_arr = '';
								if($cpt){
									$cpt_sel_arr = explode(',',$cpt);
								}
								$cpt_opt_str = '';
								foreach($cpt_arr as $key => $val){
									$sel = '';
									if(in_array($val['cpt_code'],$cpt_sel_arr)){
										$sel = 'selected';
									}
									$cpt_opt_str .= '<option value="'.$val['cpt_code'].'" '.$sel.'>'.$val['cpt_desc'].'-'.$val['cpt_code'].'</option>';
								}
								echo $cpt_opt_str;
							?>
						</select>	
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Type of visit</label>
						<select name="visit[]" id="visit" class="selectpicker" multiple="multiple" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Select">
							<?php 
								$type_sel_arr = '';
								if($visit){
									$type_sel_arr = explode(',',$visit);
								}
								$type_opt_str = '';
								foreach($type_visit_arr as $key => $val){
									$sel = '';
									if(in_array($key,$type_sel_arr)){
										$sel = 'selected';
									}
									$type_opt_str .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
								}
								echo $type_opt_str;
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Medications</label>
						<select name="medications[]" id="medications" class="selectpicker" multiple="multiple" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Select">
							<?php
								$med_sel_arr = '';
								if($medications){
									$med_sel_arr = explode(',',$medications);
								}
								$med_sel_arr = explode(',',$medications);
								$medication_opt_str = '';
								foreach($medication_arr as $key => $val){
									$sel = '';
									if(in_array($key,$med_sel_arr)){
										$sel = 'selected';
									}
									$medication_opt_str .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
								}
								echo $medication_opt_str;
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Tests/Exam</label>
						<select name="tests[]"  class="selectpicker" id="tests" multiple="multiple" data-width="100%" data-actions-box="true" data-live-search="true" data-title="Select">
							<?php
								$test_sel_arr = '';
								if($tests){
									$test_sel_arr = explode(',',$tests);
								}
								$tests_opt_str = '';
								foreach($test_exam_arr as $key => $val){
									$sel = '';
									if(in_array($key,$test_sel_arr)){
										$sel = 'selected';
									}
									$tests_opt_str .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
								}
								echo $tests_opt_str;
							?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label>Lab</label>
						<select name="txt_lab_name" class="selectpicker" id="txt_lab_name" data-width="100%">
							<?php 
								$lab_opt_str ='<option value="">None</option>';
								foreach($lab_arr as $key => $val){
									$sel = '';
									if($txt_lab_name == $val){
										$sel = 'selected';
									}
									$lab_opt_str .= '<option value="'.$val.'" '.$sel.'>'.$val.'</option>';
								}
								echo $lab_opt_str;
							?>
						</select>	
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label>Lab Result</label>
						<div class="row">
							<div class="col-sm-5">
								 <select id="lab_criteria" name="lab_criteria" class="selectpicker" data-width="100%">
									<?php 
										$lab_criteria_str = '';
										foreach($lab_criteria_arr as $key => $val){
											$sel = '';
											if($lab_criteria == $key){
												$sel = 'selected';
											}
											$lab_criteria_str .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
										}
										echo $lab_criteria_str;
									?>
								 </select>
							</div>	
							<div class="col-sm-7">
								<input type="text"  id="lab_result" name="lab_result" class="form-control" value="<?php echo $lab_result; ?>">
							</div>	
						</div>	
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>	
<div class="col-sm-2">
	<div class="form-group">
		<label onClick="top.fmain.getDxValues(this);" data-element="#dx" data-modal="#dxModal" class="text_purple pointer"><strong>Dx Code</strong></label>
		<?php
			if(trim($dx)){
				$arr_dx_code = explode(",",$dx);
				$dx_code_str = array();
				foreach($arr_dx_code as $key => $val){
					if(trim($val)){
						$dx_code_str[] = $val;
					}
				}
				$dx_code_str = implode("\n",$dx_code_str);
			}
		?>	
		<textarea rows="4" name="dx" id="dx" class="form-control"><?php echo $dx_code_str; ?></textarea>		
	</div>
</div>
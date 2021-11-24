
<div class="row">
	<!-- Heading -->
	<div class="col-sm-12 purple_bar">
		<label>Import Medications :: CSV (comma separated) Import Only</label>	
	</div>
	
	<!-- Header Btns -->
	<div class="col-sm-12 pt10">
		<form name="frm_import_medication" id="medications_form" action="index.php" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="col-sm-4">
					<div class="row">
						<div class="col-sm-8">
							<input type="file" name="u_img" id="u_img" class="btn btn-primary" autocomplete="off">	
						</div>	
						<div class="col-sm-3">
							<button  class="btn btn-success" id="butId1" type="button" name="btnImportCsv" onClick="import_fun();">Import</button>	
						</div>	
					</div>
				</div>
				
				<div class="col-sm-4 form-inline">
					<div class="row">
						<div class="col-sm-7 text-right nowrap">
							<label>Scripts: </label>
							<select name="ccd_xml[]" id="ccd_xml"  multiple="multiple" class="selectpicker" data-title="Select" data-actions-box="true" data-width="80%" data-size="5">
								<?php echo $opt_values = $import_obj->get_document_type_arr('dropdown',$_REQUEST); ?>
							</select>	
						</div>
						<div class="col-sm-5">
							<button  class="btn btn-success" id="load" type="button" name="btnImportCsv" onClick="reload_frame();">Load</button>		
						</div>			
					</div>
				</div>
				
				<div class="col-sm-4 text-center">
					<button class="btn btn-success" type="button" id="show_csv_sample">View Sample CSV Format</button>	
				</div>	
			</div>
			<div class="row pt10">
				<?php if($import_obj->import_error != ''){ ?>
					<div id="upload_err" class="col-sm-4">
						<?php echo $import_obj->import_error; ?>
					</div>
				<?php } ?>
			</div>	
		</form>		
	</div>	
	
	<!-- Heading Row -->
	<div id="content_div_head" class="col-sm-12 pt10 purple_bar">
		<label>Medical History</label>	
	</div>	
	
	<!-- Tabs -->
	<div class="col-sm-12 pt10">
		<div class="row">
			<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $import_obj->current_tab; ?>">
			<input type="hidden" name="preObjBack" id="preObjBack" value="a">
			<input type="hidden" name="patYearsOrlder" id="patYearsOrlder" value="<?php echo $patYearsOrlder; ?>"/>
			<input type="hidden" name="medical_tab_change" id="medical_tab_change" value=""/>
			<input type="hidden" name="hid_chk_change_data_main" id="hid_chk_change_data_main" value="no">	
			<ul class="nav nav-tabs medical_import">
			  <li id="medications" onClick="change_tab(this);"><a href="javascript:void[0]">Medications</a></li>
			  <li id="allergies" onClick="change_tab(this);"><a href="javascript:void[0]">Allergies</a></li>
			  <li id="problem_list" onClick="change_tab(this);"><a href="javascript:void[0]">Problem List</a></li>
			  <li id="sx_proc" onClick="change_tab(this);"><a href="javascript:void[0]">Sx Procedures</a></li>
			</ul>	
		</div>
	</div>
</div>
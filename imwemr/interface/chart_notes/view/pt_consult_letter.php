<!-- Modal -->
<div id="ptConsultLettersModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Patient Consult Letters - <small><?php echo $patName; ?></small></h4>
			</div>
			<div class="modal-body">
				<div  class="row">
					<div id="list_div_pcl" class="col-sm-4" data-fax-btn="<?php echo $fax_btn_show; ?>" data-fax-log-btn="<?php echo $fax_log_btn_show; ?>"  data-email-btn="<?php echo $email_btn_show; ?>">
						<!-- List -->
						<?php echo $html_left_pane; ?>
					</div>
					<div class="col-sm-8">
						<!-- pdf -->
						<iframe name="consent_data"  id="consult_data_id" width="100%" height="100%" frameborder="1" ></iframe>
					</div>
				</div>
			</div>
			<div class="modal-footer">				
				<button type="button" class="btn btn-primary hidden" id="sendEmailBtn" onclick="show_cl_popup('E');" >Send Email</button>
				<button type="button" class="btn btn-primary hidden" id="sendFaxBtn" onclick="show_cl_popup('F');" >Send Fax</button>
				<button type="button" class="btn btn-primary hidden" id="send_fax_Log" onclick="show_cl_popup('L');" >Fax Log</button>				
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>

<!-- Send Fax -->
<!-- Modal -->
<div  class="modal fade" role="dialog" id="send_fax_div">
	<div class="modal-dialog">

	<!-- Modal content-->
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4 class="modal-title">Send Fax</h4>
	</div>
	<div class="modal-body">	
	<!-- content -->
	<div   id="faxDiv"  >		
		<div class="row">
			<div class="form-group" >
				<label class="control-label col-sm-2" for="selectReferringPhy">Ref Phy:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy" value="">
				<input type="text" name="selectReferringPhy"  id="selectReferringPhy" onKeyUp="loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number');" onFocus="loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number');" value="" class="form-control">
				</div>
			</div>
		
			<div class="form-group" >
				<label class="control-label col-sm-2" for="send_fax_number">Fax&nbsp;Number:</label>
				<div class="col-sm-4">			
				<input type="text"  name="send_fax_number" id="send_fax_number" class="form-control" value="" onchange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="selectReferringPhyCc1">Cc1:</label>
				<div class="col-sm-4">			
				<input type="hidden" name="hiddselectReferringPhyCc1" id="hiddselectReferringPhyCc1" value="">
				<input type="text" name="selectReferringPhyCc1"  id="selectReferringPhyCc1"  onKeyUp="loadPhysicians(this,'hiddselectReferringPhyCc1','','send_fax_numberCc1');" value="" onFocus="loadPhysicians(this,'hiddselectReferringPhyCc1','','send_fax_numberCc1');" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="send_fax_numberCc1">Fax&nbsp;Number:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_fax_numberCc1" id="send_fax_numberCc1"  class="form-control" value="" onchange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="selectReferringPhyCc2">Cc2:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyCc2" id="hiddselectReferringPhyCc2" value="">
				<input type="text" name="selectReferringPhyCc2"  id="selectReferringPhyCc2"  onKeyUp="loadPhysicians(this,'hiddselectReferringPhyCc2','','send_fax_numberCc2');" value="" onFocus="loadPhysicians(this,'hiddselectReferringPhyCc2','','send_fax_numberCc2');" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="send_fax_numberCc2">Fax&nbsp;Number:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_fax_numberCc2" id="send_fax_numberCc2" class="form-control" value="" onChange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="selectReferringPhyCc3">Cc3:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyCc3" id="hiddselectReferringPhyCc3" value="">
				<input type="text" name="selectReferringPhyCc3"  id="selectReferringPhyCc3" onKeyUp="loadPhysicians(this,'hiddselectReferringPhyCc3','','send_fax_numberCc3');" value="" onFocus="loadPhysicians(this,'hiddselectReferringPhyCc3','','send_fax_numberCc3');" class="form-control">
				</div>
			</div>	
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="send_fax_numberCc3">Fax&nbsp;Number:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_fax_numberCc3" id="send_fax_numberCc3"  class="form-control" value="" onChange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
	</div>
	<!-- content -->	
	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-default" id="send_close_btn" onclick="return sendSavedFax();" >Send Fax</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
	</div>

	</div>
</div>

<!-- Send Email -->
<!-- Modal -->
<div  class="modal fade" role="dialog" id="send_email_div">
	<div class="modal-dialog">

	<!-- Modal content-->
	<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4 class="modal-title">Send Email</h4>
	</div>
	<div class="modal-body">	
	<!-- content -->
	<div   id="faxDiv"  >		
		<div class="row">
			<div class="form-group" >
				<label class="control-label col-sm-2" for="selectReferringPhy">Ref Phy:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyEmail" id="hiddselectReferringPhyEmail" value="">
				<input type="text" name="selectReferringPhyEmail"  id="selectReferringPhyEmail" onKeyUp="loadPhysicians(this,'hiddselectReferringPhyEmail','','send_email_id');" onFocus="loadPhysicians(this,'hiddselectReferringPhyEmail','','send_email_id');" value="" class="form-control">
				</div>
			</div>
		
			<div class="form-group" >
				<label class="control-label col-sm-2" for="send_fax_number">Email&nbsp;ID:</label>
				<div class="col-sm-4">			
				<input type="text"  name="send_email_id" id="send_email_id" class="form-control" value=""  autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="selectReferringPhyCc1">Cc1:</label>
				<div class="col-sm-4">			
				<input type="hidden" name="hiddselectReferringPhyEmailCc1" id="hiddselectReferringPhyEmailCc1" value="">
				<input type="text" name="selectReferringPhyEmailCc1"  id="selectReferringPhyEmailCc1"  onKeyUp="loadPhysicians(this,'hiddselectReferringPhyEmailCc1','','send_email_idCc1');" value="" onFocus="loadPhysicians(this,'hiddselectReferringPhyEmailCc1','','send_email_idCc1');" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="send_email_idCc1">Email&nbsp;ID:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_email_idCc1" id="send_email_idCc1"  class="form-control" value=""  autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="selectReferringPhyCc2">Cc2:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyEmailCc2" id="hiddselectReferringPhyEmailCc2" value="">
				<input type="text" name="selectReferringPhyEmailCc2"  id="selectReferringPhyEmailCc2"  onKeyUp="loadPhysicians(this,'hiddselectReferringPhyEmailCc2','','send_email_idCc2');" value="" onFocus="loadPhysicians(this,'hiddselectReferringPhyEmailCc2','','send_email_idCc2');" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="send_fax_numberCc2">Email&nbsp;ID:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_email_idCc2" id="send_email_idCc2" class="form-control" value=""  autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="selectReferringPhyCc3">Cc3:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyEmailCc3" id="hiddselectReferringPhyEmailCc3" value="">
				<input type="text" name="selectReferringPhyEmailCc3"  id="selectReferringPhyEmailCc3" onKeyUp="loadPhysicians(this,'hiddselectReferringPhyEmailCc3','','send_email_idCc3');" value="" onFocus="loadPhysicians(this,'hiddselectReferringPhyEmailCc3','','send_email_idCc3');" class="form-control">
				</div>
			</div>	
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="send_fax_numberCc3">Email&nbsp;ID:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_email_idCc3" id="send_email_idCc3"  class="form-control" value=""  autocomplete="off">
				</div>
			</div>
		</div>
		
	</div>
	<!-- content -->	
	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-default" id="send_close_btn" onclick="return sendSavedEmail();" >Send Email</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
	</div>

	</div>
</div>

<!-- hidden -->
<input type="hidden" name="sendSaveFaxName" id="sendSaveFaxName" value="<?php echo $setNameFaxPDF;?>">	
<input type="hidden" name="sendSaveEmailName" id="sendSaveEmailName" value="<?php echo $setNameFaxPDF;?>">		
<input type="hidden" id="pat_temp_id" name="pat_temp_id">
<!-- hidden -->

<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/consult_letters.js"></script>
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
?>
        <script type="text/javascript">
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			var formObjects		   = new Array('phrase_id','phrase');
			function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Loading iPortal Survey...');
				
				if(typeof(s)!='string' || s==''){s = 'Active';}
				s_url = "&s="+s;
				
				if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
				if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f} ;
				
				oso		= $('#ord_by_field').val(); //old_so
				soAD	= $('#ord_by_ascdesc').val();
				if(typeof(so)=='undefined' || so==''){
					so 		= $('#ord_by_field').val();
				}else{
					$('#ord_by_field').val(so);
					if(oso==so){
						if(soAD=='ASC') soAD = 'DESC';
						else  soAD = 'ASC';
					}else{
						soAD = 'ASC';
					}
					$('#ord_by_ascdesc').val(soAD);
				};
				$('.link_cursor span').html('');
				if(soAD=='ASC')	$(currLink).find('span').html(' <img src="../../../library/images/arr_up.gif">');
				else $(currLink).find('span').html(' <img src="../../../library/images/arr_dn.gif">');
				
				var so_url='&so='+so+'&soAD='+soAD;
				
				ajaxURL = "ajax_survey_conf.php?task=show_list"+s_url+p_url+f_url+so_url;
				$.ajax({
				  url: ajaxURL,
				  success: function(r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
					showRecords(r);
				  }
				});
			}
			function showRecords(r){
			
				r = jQuery.parseJSON(r);
				result = r.records; //$('#result_set').html(result+'<hr>end of Response');exit;
				arr_survey=r.array_survey;
				arr_porc=r.get_all_proc;
				arr_problem_list=r.get_problem_list;
				var height_select="280px";
				var survey_option="<option></option>";
				$.each(arr_survey,function(index,value){
					survey_option+="<option value='"+index+"-||-"+value+"'>"+value+"</option>";
				});
				$("#survey_id_set").html(survey_option);
				
				var problem_option="";
				$.each(arr_problem_list,function(index,value){
					$.each(value,function(index,value){
						problem_option+="<option value='"+index+"-||-"+value+"'>"+value+"</option>";
					});
				});
				$("#problem_list").html(problem_option);
				$("#problem_list").css({"height":height_select});
				
				
				var proc_option="";
				$.each(arr_porc,function(index,value){
					$.each(value,function(index,value){
						proc_option+="<option value='"+index+"-||-"+value+"'>"+value+"</option>";
					});
				});
				$("#appointment_proc").html(proc_option);
				$("#appointment_proc").css({"height":height_select});
				
				h='';var no_record='yes';
				if(r != null){
					row = '';
					row_class = '';
					for(x in result){no_record='no';
						s = result[x];
						rowData = new Array();
						row += '<tr class="link_cursor'+row_class+'">';
						for(y in s){
							tdVal = s[y];
							rowData[y] = tdVal;
							if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
							if(y=='survey_name'){
								row	+= '<td data-label="Survey Name"  onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
							if(y=='survey_active_date_from'){
								row	+= '<td data-label="Survey Active to Expire date"  onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
							if(y=='age_group'){
								row	+= '<td data-label="Age Group"  onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
						}
						if(row_class==''){row_class=' alt';}else{row_class='';}
						totalRecords++;
						row += '</tr>';
						arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
					}
					h = row;
				}
				if(no_record=='yes'){h+="<tr><td colspan='4' style='text-align:center;'>No Record Found</td></tr>";}
				$('#result_set').html(h);		
				$('.selectpicker').selectpicker('refresh');
				top.show_loading_image('hide');
			}
			
			function addNew(ed,pkId){
				var modal_title = '';
				if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
				else {
					modal_title = 'Add New Record';
					$('#adm_epostId').val('');
					document.add_edit_frm.reset();
				}
				$('#myModal .modal-header .modal-title').text(modal_title);
				$('#myModal').modal('show');
				$('.selectpicker').selectpicker('refresh');
			//	$( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true});
				if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
				$('.selectpicker').selectpicker('refresh');
			}
			function saveFormData(){
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving data...');
				frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
				var msg='';
				if($.trim($('#survey_id_set').val())==""){
					msg+="&bull;&nbsp;Please select the survey<br>";
				}
				
				if($.trim($('#survey_active_date').val())==""){
					msg+="&bull;&nbsp;Please select the Survey Active date<br>";
				}
				if($.trim($('#survey_expire_date').val())==""){
					msg+="&bull;&nbsp;Please select the Survey Expire date<br>";
				}
				if($.trim($('#appt_start_date').val())==""){
					msg+="&bull;&nbsp;Please select the appointment Start date<br>";
				}
				if($.trim($('#appt_end_date').val())==""){
					msg+="&bull;&nbsp;Please select the appointment End date<br>";
				}
				if($.trim($('#survey_message').val())==""){
					msg+="&bull;&nbsp;Please enter the survey message<br>";
				}
				
				if(msg!=''){
					top.fAlert(msg);
					top.show_loading_image('hide');
					return false;	
				}
				$.ajax({
					type: "POST",
					url: "ajax_survey_conf.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='enter_unique'){
							top.fAlert('Record already exist.');		
							return false;
						}
						if(d.toLowerCase().indexOf('success') > 0){
							top.alert_notification_show(d);
						}else{
							top.fAlert(d);
						}
						$('#myModal').modal('hide');
						LoadResultSet();
					}
				});
			}
			function deleteSelectet(){
				pos_id = '';
				$('.chk_sel').each(function(){
					if($(this).is(':checked')){
						pos_id += $(this).val()+', ';
					}
				});
				if(pos_id!=''){
					top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+pos_id+"')");
				}else{
					top.fAlert('No Record Selected.');
				}
			}
			function deleteModifiers(pos_id) {
				pos_id = pos_id.substr(0,pos_id.length-2);
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Deleting Record(s)...');
				frm_data = 'pkId='+pos_id+'&task=delete';
				$.ajax({
					type: "POST",
					url: "ajax_survey_conf.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
						else{top.fAlert(d+'Record delete failed. Please try again.');}
					}
				});
			}
			
			function setStatus(rowid,value)
			{
				var dataString = 'task=change_status&rid='+rowid+'&value='+value;
				$.ajax({
					type: "POST",
					url: "ajax_survey_conf.php",
					data: dataString,
					cache: false,
					success: function(response)
					{
						if(response=="true")
						{
							if(value==1)
							{
								$("#status_"+rowid).html('<span style="color:red;"> Deleted </span>');
								$("#status_"+rowid).attr('onClick','setStatus("'+rowid+'","0")');
							}
							else if(value==0)
							{
								$("#status_"+rowid).html('<span style="color:green;"> Active </span>');
								$("#status_"+rowid).attr('onClick','setStatus("'+rowid+'","1")');
							}
							top.alert_notification_show("Record Updated Successfully");
						}
					}
				});
			}
			
			function fillEditData(pkId){
				f = document.add_edit_frm;
				e = f.elements;
				add_edit_frm.reset();
				$('#id').val(pkId);
				for(i=0;i<e.length;i++){
					o = e[i];
					if($.inArray(o.phrase,formObjects)){
						on	= o.id;
						
						v	= arrAllShownRecords[pkId][on];
						if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
							if (o.type == "checkbox" || o.type == "radio"){
								oid = on;
								if(v==1)
								{
									$("#"+oid).prop('checked',true);
								}
							} else if(o.type!='submit' && o.type!='button'){
								o.value = v;
							}
						}
						if(on=="gender" || on=="problem_list" || on=="appointment_proc"){
							var fill="";
							if(v.indexOf("|~~|")>0){
								fill=v.split("|~~|");
								$("#"+on).val(fill);	
							}else{
								$("#"+on).val(v);
							}
						}
					}
				}
			}
	</script>
	<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="survey_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
		<div class="whtbox">
			<div class="table-responsive respotable">
				<table class="table table-bordered adminnw">
					<thead>
						<tr>
							<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
							<th onClick="LoadResultSet('','','','survey_name',this);">Survey Name<span></span></th>
							<th onClick="LoadResultSet('','','','survey_expire_date',this);">Survey Active to Expire Date<span></span></th>
							<th onClick="LoadResultSet('','','','age_group_start',this);">Age Group<span></span></th>
						</tr>
					</thead>
					<tbody id="result_set"></tbody>
				</table>
			</div>
		</div>
		<div class="common_modal_wrapper"> 
		<!-- Modal -->
			<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg"> 
				<!-- Modal content-->
					<div class="modal-content">
					<form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="saveFormData();return false;">
						<input type="hidden" name="id" id="id" >
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title" id="modal_title">Modal Header</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<div class="batcfrm">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label>Survey</label>
												<select class="selectpicker" data-width="100%" data-size="5" data-title="Select" id="survey_id_set" name="survey_id_set"></select>
											</div>	
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label>Gender</label>
												<select name="gender[]" id="gender" class="selectpicker" data-width="100%" data-title="Select" multiple="multiple">
													<option value="male">Male</option>
													<option value="female">Female</option>
													<option value="unknown">Unknown</option>
												</select>
											</div>	
										</div>
											
										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label>Date Range</label>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="sr-only" for="survey_active_date"></label>
																	<div class="input-group">
																		<div class="input-group">
																			<div class="input-group-addon labbg">From</div>
																			<input type="text" class="form-control datepicker" name="survey_active_date" id="survey_active_date" />
																			<label class="input-group-addon pointer" for="survey_active_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
																			</label>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																<label class="sr-only" for="survey_expire_date"></label>
																	<div class="input-group">
																		<div class="input-group-addon labbg">To</div>
																		<input type="text" class="form-control datepicker"  name="survey_expire_date" id="survey_expire_date">
																		<label class="input-group-addon pointer" for="survey_expire_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
																	</div>
																</div>
															</div>
														</div>	
													</div>
												</div>	

												<div class="col-sm-6">
													<div class="form-group">
														<label>Appointment Date</label>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="sr-only" for="appt_start_date"></label>
																	<div class="input-group">
																		<div class="input-group-addon labbg">From</div>
																		<input type="text" class="form-control datepicker" name="appt_start_date" id="appt_start_date" />
																		<label class="input-group-addon pointer" for="appt_start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
																	</div>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																<label class="sr-only" for="appt_end_date"></label>
																	<div class="input-group">
																		<div class="input-group-addon labbg">To</div>
																		<input type="text" class="form-control datepicker" name="appt_end_date" id="appt_end_date" />
																		<label class="input-group-addon pointer" for="appt_end_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>	
										</div>

										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label>Age Group</label>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="sr-only" for="age_group_start"></label>
																	<div class="input-group">
																		<div class="input-group-addon labbg">From</div>
																		<input type="text" class="form-control" name="age_group_start" id="age_group_start" />
																	</div>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																<label class="sr-only" for="age_group_end"></label>
																	<div class="input-group">
																		<div class="input-group-addon labbg">To</div>
																		<input type="text" class="form-control" name="age_group_end" id="age_group_end" />
																	</div>
																</div>
															</div>
														</div>
													</div>	
												</div>

												<div class="col-sm-6">
													<div class="form-group">
														<label>&nbsp;</label>
														<div class="checkbox">
															<input type="checkbox" value="1" id="enable_comment" name="enable_comment" />
															<label for="enable_comment">Show Comment box</label>
														</div>	
													</div>	
												</div>	
											</div>
										</div>

										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label>Survey Message</label>	
														<textarea id="survey_message" name="survey_message" class="form-control"> </textarea>	
													</div>	
												</div>	
												<div class="col-sm-6">
													<div class="form-group">
														<label>Survey Thanks Message</label>	
														<textarea id="survey_thanks_message" name="survey_thanks_message" class="form-control"></textarea>	
													</div>	
												</div>		
											</div>
										</div>

										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label>Problem List</label>	
														<select name="problem_list[]" id="problem_list" class="selectpicker" data-width="100%" data-size="6" data-actions-box="true" data-title="Select" data-live-search="true" multiple="multiple"> </select>	
													</div>
												</div>	
												<div class="col-sm-6">
													<div class="form-group">
														<label>Appointment Procedures</label>	
														<select class="selectpicker" name="appointment_proc[]" id="appointment_proc" data-live-search="true" data-actions-box="true" data-width="100%" data-size="6" data-title="Select" multiple="multiple">
														</select>
													</div>
												</div>	
											</div>	
										</div>	
									</div>
								</div>	
							</div>	
						</div>
						<div id="module_buttons" class="modal-footer ad_modal_footer">
							<button type="submit" class="btn btn-success">Save</button>
							<button type="button" class="btn btn-danger" value="Cancel" data-dismiss="modal">Close</button>
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>
<script type="text/javascript">
	LoadResultSet();
	var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
	top.btn_show("ADMN",ar);
	$(document).ready(function(){
		check_checkboxes();
		set_header_title('Set Survey');
	});
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	show_loading_image('none');
</script>
<?php 
	require_once('../admin_footer.php');
?>
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
require_once("../../admin_header.php");
$phy_id_cn=$GLOBALS['arrValidCNPhy'];
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/ci_function.js"></script>
<style type="text/css">
	.num_cnt{ margin-left:3px;border:1px solid #CCC; font-size:12px; font-weight:bold; cursor:pointer; color:#666; background:#F9F8F6; font-family:Verdana, Geneva, sans-serif; padding:2px 5px 2px 5px;}
	.num_cnt.selected{ font-size:14px; color:#FFF !important; cursor:text;  background:#5c2a79 !important;}
	.grpbox { height: 345px; }
	
</style>
<body>
	<textarea id="hidd_reason_text" style="display:none;"></textarea>
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<input type="hidden" name="pg_aplhabet" id="pg_aplhabet" value="A">
	<input type="hidden" name="page" id="page" value="1">
	<input type="hidden" name="status" id="status" value="">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','name',this);">Company Name<span></span></th>
						<th onClick="LoadResultSet('','in_house_code',this);">Practice Code<span></span></th>
						<th onClick="LoadResultSet('','contact_address',this);">Contact Address<span></span></th>
						<th onClick="LoadResultSet('','phone',this);">Phone<span></span></th>
						<th>Pri / Sec Payments<span></span></th>
						<th>Claim Type<span></span></th>
						<th>HX<span></span></th>
						<th>Referral Required<span></span></th>
						<th>Status<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="row ">
			<div class="col-sm-9 pagingcs text-center">
				<ul class="pagination">
					<li id="div_pages"></li>
				</ul>
			</div>
			<div class="col-sm-3 form-inline recodpag" >Records per page 
				<select class="form-control minimal" name="record_limit" id="record_limit" onChange="LoadResultSet()">
					<option value="19">19</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="200">200</option>
				</select>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-9 text-center">
				<ul class="pagination" id="pagenation_alpha_order"></ul>
			</div>
			<div class="col-sm-3 form-inline activuser">
				<div class="input-group">
					<input type="text" class="form-control"  name="search" id="search" >
					<div class="input-group-addon pointer" onClick="srh_records();"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
				</div>
				<select name="srchStatus" id="srchStatus"  class="form-control minimal" onChange="javascript:LoadResultSet(this.value);" >
					<option value="0">Active</option>
					<option value="1">Inactive</option>
					<option value="all">All</option>
				</select>
			</div>
		</div>
	</div>
<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
	<input type="hidden" name="id" id="id">	    
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:98%;"> 
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-3 col-md-6 col-sm-6">
							<div class="grpbox">
								<div class="head"><span>Company</span></div>
								<div class="tblBg">
								<div class="clearfix"></div>
									<div class="form-group">
										<label for="name" >Company Name</label>
										<input class="form-control" onBlur="changeClass(this);" onChange="get_ins_data(this.value)" type="text" name="name" id="name" value="">
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6 form-group">
												<label for="in_house_code" >Practice Code</label>
												<input onBlur="changeClass(this);" name="in_house_code" id="in_house_code" value="" class="form-control">
											</div>
											<div class="col-sm-6" id="div_ins_group_dd">
												<label for="groupedIn" >Insurance Group</label>
												<select class="form-control minimal" name="groupedIn" id="groupedIn">
													<option>--Select Group--</option>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-4">
												<label for="Insurance_payment">Primary</label>
												<select  class="form-control minimal" name="Insurance_payment" id="Insurance_payment"></select>
											</div>
											<div class="col-sm-4">
												<label for="secondary_payment_method">Secondary</label>
												<select id="secondary_payment_method" name="secondary_payment_method" class="form-control minimal"></select>
                               				</div>
											<div class="col-sm-4">
												<label for="claim_type" >Claim type</label>
												<select id="claim_type" name="claim_type" class="form-control minimal" onChange="ToggleMSP();"></select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-8">
												<label for="ins_accept_assignment">Accept/No Accept Assignment</label>
												<select id="ins_accept_assignment" name="ins_accept_assignment"  class="form-control minimal">
													<option value="0">Accept Assignment</option>
													<option value="1">NAA - Courtesy Billing</option>
													<option value="2">NAA - No Courtesy Billing</option>
												</select>
											</div>
											<div class="col-sm-4">
												<label for="institutional_type">Institutional Type</label>
												<select id="institutional_type" name="institutional_type" class="form-control minimal"></select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label for="claim_filing_days" >Claim Filing Days (CFD)</label>
													<input class="form-control"  type="text" name="claim_filing_days" id="claim_filing_days" value=""  onKeyUp="validate_num(this)" ondrop="return false;" onpaste="return false;">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label for="payment_due_days" >Payment Due Days (PD)</label>
													<input class="form-control"  type="text" name="payment_due_days" id="payment_due_days" value="" onKeyUp="validate_num(this)" ondrop="return false;" onpaste="return false;">
												</div>
											</div>	
											<div class="col-sm-8">
											<div class="row">
											<div class="col-sm-5">
												<div class="checkbox">
													<input style="cursor:pointer" type="checkbox" name="BatchFile" id="BatchFile" value="1" >
													<label for="BatchFile">Direct Billing</label>
												</div>
											</div>
											<div class="col-sm-7">
												<div class="checkbox">
													<input style="cursor:pointer" type="checkbox" name="collect_copay" id="collect_copay" value="1" >
													<label for="collect_copay">Collect tests Copay</label>
												</div>
											</div>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="checkbox">
													<input style="cursor:pointer" type="checkbox" name="ref_management" id="ref_management" value="1" >
													<label for="ref_management">Referral&nbsp;Required</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6">
								<div class="grpbox">
								<div class="head"><span>Contact</span></div>
								<div class="tblBg">
								<div class="clearfix"></div>
									<div class="form-group">
										<label for="name" >Contact Name</label>
										<input class="form-control" type="text" name="contact_name" id="contact_name" value="">
									</div>
									<div class="form-group">
										<label for="contact_address" >Street</label>
										<textarea class="form-control" type="text" name="contact_address" id="contact_address" rows="5"></textarea>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label for="Zip"><?php getZipPostalLabel(); ?></label>
													<div class="row">
														<div class="col-sm-6">
															<input type="text" class="form-control"  name="Zip" id="Zip" onChange="zip_vs_state_R6(this,dgi('City'),dgi('State'));" value="" />
														</div>
														<div class="col-sm-6">
															<?php if(inter_zip_ext()){?>
															<input type="text" class="form-control" name="zip_ext" id="zip_ext" value="" />
															<?php }?>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="City">City</label>
													<input class="form-control"  name="City" id="City" value="" />
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="State">State</label>
													<input type="text" class="form-control" name="State" id="State" value="<?php print $policyDetail["State"];?>" />
												</div>
											</div>
										</div>
									</div>
								</div>
								</div>
							</div>
							<div class="col-lg-2 col-md-6 col-sm-6">
							<div class="grpbox">
								<div class="head"><span>Mailing</span></div>
								<div class="tblBg">
								<div class="clearfix"></div>
									<div class="form-group">
										<label for="phone" >Phone</label>
										<input class="form-control" onBlur="changeClass(this);" type="text" name="phone" id="phone" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
									</div>
									<div class="form-group">
										<label for="fax" >Fax</label>
										<input class="form-control" onBlur="changeClass(this);" type="text" name="fax" id="fax" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-4 form-group">
											<label for="email" >Email</label>
											<input name="email" id="email" type="text" class="form-control" value="" onKeyUp="javascript:search_email(event,this,'div_email_section')" >
												<div id="div_email_section" class="input-group" style="position:absolute">
													<span class="input-group-btn">
														<select class="btn hide"  size="5" style="width:100%">
															<option selected value="@aol.com">aol.com</option>
															<option value="@msn.com">msn.com</option>
															<option value="@gmail.com">gmail.com</option>
															<option value="@yahoo.com">yahoo.com</option>
															<option value="@hotmail.com">hotmail.com</option>
														</select>
													</span>
												</div>
											</div>
											<div class="col-sm-4">
												<label for="co_ins" >Co-Ins</label>
												<input type="text" name="co_ins" id="co_ins" class="form-control" value="">
												<input type="hidden" name="pre_co_ins" id="pre_co_ins" value="">
											</div>
											<div class="col-sm-4">
												<label for="collect_sec_ins" <?php echo show_tooltip('Collect Sec. Ins.')?>>Collect..</label><br />
												<div class="checkbox"><input type="checkbox" name="collect_sec_ins" id="collect_sec_ins" value="1"><label for="collect_sec_ins" ></label></div>
												<input type="hidden" name="pre_collect_sec" id="pre_collect_sec" value="">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-4">
												<label for="Payer_id">Payer ID <br> (Inst.)</label>
												<input  type="text" class="form-control" name="Payer_id" id="Payer_id" value="" onBlur="changeClass(this);" />
											</div>
											<div class="col-sm-4">
												<label for="Payer_id_pro">Payer ID <br> (Pro.)</label>
												<input type="text" name="Payer_id_pro" id="Payer_id_pro" onBlur="changeClass(this);" class="form-control" />
                               				</div>
											<div class="col-sm-4">
												<label for="emdeon_payer_eligibility" <?php echo show_tooltip('Payer ID<br> (Real Time Eligibility)')?> >Payer ID<br> 
												(RTE)</label>
												<input type="text" id="emdeon_payer_eligibility" name="emdeon_payer_eligibility" class="form-control" onBlur="changeClass(this);" />
											</div>
										</div>
									</div>
								</div>
								</div>
							</div>
							<div class="col-lg-2 col-md-6 col-sm-6">
								<div class="grpbox">
									<div class="head"><span>IDS</span></div>
									<div class="tblBg">
										<div class="clearfix"></div>
										<div class="form-group">
											<label for="insurance_Practice_Code_id">Practice Group ID</label>
											<input class="form-control" type="text" name="insurance_Practice_Code_id" id="insurance_Practice_Code_id" value="" />
										</div>
										<div class="form-group">
											<label for="institutional_Code_id" >Institutional Group ID</label>
											<input class="form-control" type="text" name="institutional_Code_id" id="institutional_Code_id" value="" />
										</div>
										<div class="form-group">
											<label for="Reciever_id">Receiver ID</label>
											<input class="form-control" type="text" name="Reciever_id" id="Reciever_id" value="" />
										</div>
										<div class="form-group">
											<label for="payer_type">Submitter ID</label>
											<input class="form-control" type="text" name="payer_type" id="payer_type" value="" />
										</div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-7">
                                                    <label for="capitation_pol"><strong>Capitation Policies</strong></label><br />
                                                    <div class="checkbox form-inline">
                                                        <input type="checkbox" name="capitation" id="capitation" value="1">
                                                        <label for="capitation" ></label> 
                                                        <button type="button" class="btn btn-success" id="capitation_btn" disabled onClick="capitation_fun();">Capitation</button>
                                                    </div>                                                
                                            	</div>
                                               <div class="col-sm-5">
                                                    <label for="cpt_alert_pol"><strong>CPT Alert</strong></label>
                                                    <button type="button" class="btn btn-success" id="cpt_alert_btn" onClick="display_cpt_alert_div();">CPT Alert</button>                                                    
                                            	</div>
                                            </div>
                                        	
										</div>
									</div>
                                 </div>
							</div>
							<div class="col-lg-2 col-md-6 col-sm-6">
								<div class="grpbox">
									<div class="head"><span>More Info</span></div>
									<div class="tblBg">
									<div class="clearfix"></div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="FeeTable">Fee Table</label>
												<select name="FeeTable" id="FeeTable" class="form-control minimal"></select>
											</div>
											<div class="col-sm-6">
												<label for="ins_del_status">Status</label>
												<select name="ins_del_status" id="ins_del_status" class="form-control minimal">
													<option value="0">Active</option>
													<option value="1">Inactive</option>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="ins_state_payer_code" <?php echo show_tooltip('State Payer Code')?>>State Payer</label>
												<select name="ins_state_payer_code" id="ins_state_payer_code" class="form-control minimal"></select>
											</div>
											<div class="col-sm-6">
												<label for="ins_type">Ins.&nbsp;Type</label>
												 <select  name="ins_type" id="ins_type" onChange="ToggleMSP();" class="form-control minimal"></select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="attn">Description</label>
										<textarea class="form-control" name="attn" id="attn" rows="1" ></textarea>
									</div>
									<div class="form-group" style="padding-left:10px;">
										<div class="row">
											<div class="col-sm-3">
												<div class="checkbox">	
													<input style="cursor:pointer" type="checkbox" name="frontdesk_desc" id="frontdesk_desc" value="1"><label for="frontdesk_desc">FD</label>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="checkbox">	
													<input style="cursor:pointer" type="checkbox" name="billing_desc" id="billing_desc" value="1"><label for="billing_desc">Billing</label>
												</div>
											</div>
											<div class="col-sm-5">
											<div class="checkbox">	
													<input style="cursor:pointer" type="checkbox" name="rte_chk" id="rte_chk" value="1"><label for="rte_chk">RTE</label>
												</div>
												
											</div>
										</div>
									</div>
									<div class="form-group" style="padding-left:10px;">
										<div class="row">
											<div class="col-sm-6">
												<div class="checkbox">	
													<input style="cursor:pointer" type="checkbox" name="transmit_ndc" id="transmit_ndc" value="1" <?php echo show_tooltip('Allow to send NDC information in claim data')?>>
													<label for="transmit_ndc" <?php echo show_tooltip('Allow to send NDC information in claim data')?>>Send NDC</label>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="checkbox">
													<input style="cursor:pointer" type="checkbox" name="pre_atuh_chk" id="pre_atuh_chk" value="1"><label for="pre_atuh_chk">Pre-Auth</label>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label for="FeeTable">ICD Code</label>
												<select  name="icd_code" id="icd_code" class="form-control minimal">
													<option value=""  ></option>
													<option value="ICD-9"  >ICD-9</option>
													<option value="ICD-10" >ICD-10</option>
												</select>
											</div>
											<div class="col-sm-6">
												<label for="msp_type">MSP Type</label>
												<select  name="msp_type" id="msp_type" class="form-control minimal"></select>
											</div>
										</div>
									</div>
									</div>
									</div>
								</div>
							</div>
						</div>
                        <div id="module_buttons" class="ad_modal_footer modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
			</div>
		</div>
	</div>
</form> 
<?php
	$arr_cpt_code=$arr_cpt_alert_code=array();
	$cpt_qry = "select cpt_prac_code,cpt_fee_id,cpt_desc from cpt_fee_tbl where status='active' and delete_status = '0' order by cpt_prac_code asc";
	$cpt_run = imw_query($cpt_qry);
	while($cpt_fet=imw_fetch_array($cpt_run)) {
		$arr_cpt_code[$cpt_fet['cpt_fee_id']]=str_replace(array('"',"\n\r", "\n", "\r"),'',trim($cpt_fet['cpt_desc'])).' - '.trim($cpt_fet['cpt_prac_code']);
		$arr_cpt_alert_code[$cpt_fet['cpt_fee_id']]=trim($cpt_fet['cpt_prac_code']);
	}
?>   
<form name="add_edit_frm_cap" id="add_edit_frm_cap" onSubmit="saveFormData();return false;">
    <div id="capitation_div" class="modal fade" role="dialog">
        <div class="boxheadertop">
                <div id="InsModal" class="modal" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <label></label>
                            <h4 class="modal-title" id="modal_title">Please Add/Remove CPT(s) using Arrow Buttons.</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <!-- CPT Codes Modal Box Starts -->
                                    <div class="text-left" id="pop_up_cpt" style="display:none">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class=" formlabel">
                                                    <div class="headinghd">
                                                        <h4>List of All CPT List</h4>
                                                    </div>
            
                                                    <div>
                                                         <select  class="form-control"  id="cpt1" name="cpt1[]"  size="35" multiple="multiple" style="height:250px!important;overflow-y:scroll">	<?php foreach($arr_cpt_code as $cptkey=> $cpt_names) { ?>
                                                                <option value="<?php echo $cptkey; ?>"><?php echo $cpt_names; ?></option>
                                                        	<?php } ?>
                                                        </select>
                                                    </div>
                                                </div>	
                                            </div>
                                                
                                            <div class="col-lg-2 text-center">
                                                <input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_cpt','cpt1','selected_cpt','all');"><br>
                                                <input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_cpt','cpt1','selected_cpt','single');"><br>
                                                <input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_cpt','selected_cpt','cpt1','single_remove');"><br>
                                                <input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_cpt','selected_cpt','cpt1','all_remove');">	
                                            </div>
                                            <div class="col-lg-5">
                                                <div class=" formlabel">
                                                    <div class="headinghd">
                                                        <h4>List of Selected CPT List</h4>
                                                    </div>
                                                    <div>
                                                        <select  class="form-control"  id="selected_cpt" name=""  size="35" multiple="multiple" style="overflow-y:scroll;height:250px!important"></select>
                                                    </div>
                                                </div>
                                            </div>	
                                        </div>
                                    </div>
                                <!--  CPT Codes Modal Box Ends  -->
                                
                                <!-- Users Modal Box Starts -->
                                    <?php
                                        $arr_user=array();
                                        $qry = "select id,fname,lname,mname,sx_physician,user_type,Enable_Scheduler from users WHERE delete_status='0' order by lname ASC";
                                        $run = imw_query($qry);
                                        while($fet=imw_fetch_array($run)) {
											$fname=$fet["fname"];
											$lname=$fet["lname"];
											$mname="";
											if($fet["mname"]!=""){
												$mname=" ".trim($fet["mname"]).'.';
											}
											$name=$lname.", ".$fname.$mname;
											if($fet["Enable_Scheduler"]=='1' || in_array($fet["user_type"],$phy_id_cn)){
                                            	$arr_user[$fet['id']]=$name;
											}
                                        }
                                    ?>
                                    <div class="text-left" id="pop_up_user" style="display:none">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class=" formlabel">
                                                    <div class="headinghd">
                                                        <h4>List of All User List</h4>
                                                    </div>
            
                                                    <div>
                                                         <select  class="form-control"  id="user1" name="user1[]"  size="35" multiple="multiple" style="height:250px!important;overflow-y:scroll">
														 	<?php foreach($arr_user as $key=> $names) { ?>
                                                                <option value="<?php echo $key; ?>"><?php echo $names; ?></option>
                                                        	<?php } ?>
                                                        </select>
                                                    </div>
                                                </div>	
                                            </div>
                                                
                                            <div class="col-lg-2 text-center">
                                                <input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_user','user1','selected_user','all');"><br>
                                                <input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_user','user1','selected_user','single');"><br>
                                                <input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_user','selected_user','user1','single_remove');"><br>
                                                <input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_user','selected_user','user1','all_remove');">	
                                            </div>
                                            <div class="col-lg-5">
                                                <div class=" formlabel">
                                                    <div class="headinghd">
                                                        <h4>List of Selected User List</h4>
                                                    </div>
                                                    <div>
                                                        <select  class="form-control"  id="selected_user" name=""  size="35" multiple="multiple" style="overflow-y:scroll;height:250px!important"></select>
                                                    </div>
                                                </div>
                                            </div>	
                                        </div>
                                    </div>
                                <!--  CPT Codes Modal Box Ends  -->
                            </div>	
                        </div>
                        <div id="module_buttons" class="modal-footer ad_modal_footer">
                            <!-- CPT Codes Footer -->
                            <div class="pop_up_cpt" style="display:none">
                                <button type="button" class="btn btn-success" onClick="selected_ele_close('InsModal','selected_cpt','cap_cpt_code','scp_div_cpt','done')">Done</button>
                                <button type="button" class="btn btn-danger" value="Close" onClick="selected_ele_close('InsModal','selected_cpt','cap_cpt_code','scp_div_cpt','close')">Close</button>
                            </div>
                            <!-- User List Footer -->
                            <div class="pop_up_user" style="display:none">
                                <button type="button" class="btn btn-success" onClick="selected_ele_close('InsModal','selected_user','cap_user','scp_div_disable','done')">Done</button>
                                
                                <button type="button" class="btn btn-danger" value="Close" onClick="selected_ele_close('InsModal','selected_user','cap_user','scp_div_disable','close')">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-dialog" style="width:98%;"> 
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal_title">Modal Header</h4>
                </div>
                    <div class="modal-body">
                        <div class="row">
                        	<div class="col-sm-5">
                                <div class="form-group" onClick="return popup_dbl('pop_up_cpt','cpt1','selected_cpt','','cap_cpt_code')">
                                    <label class="text_purple pointer">CPT Code (Exclusions)</label>	
                                    <select  class="form-control"  style="cursor:pointer" id="cap_cpt_code" name="cap_cpt_code[]" multiple="multiple" size="15"></select>		
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group" onClick="return popup_dbl('pop_up_user','user1','selected_user','','cap_user')">
                                    <label class="text_purple pointer">Provider (Exclusions)</label>	
                                    <select  class="form-control"  style="cursor:pointer" id="cap_user" name="cap_user[]" multiple="multiple" size="15"></select>		
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="cap_wrt_code">Adjustment Code</label>
                                <select name="cap_wrt_code" id="cap_wrt_code" class="selectpicker" data-width="100%"></select>	
                            </div>
                        </div>
                        <div class="clearfix pt10"></div>
                        <div id="module_buttons" class="ad_modal_footer modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                     </div>   
              </div>
          </div>
      </div>
</form>
<form method="post" name="add_edit_frm_cpt_alert" id="add_edit_frm_cpt_alert" onSubmit="saveFormData();return false;">
<input type="hidden" name="cpt_ins_id" id="cpt_ins_id" value="">
<input type="hidden" name="cpt_row_count" id="cpt_row_count" value="0">
 <div id="cpt_alert_div" class="modal" role="dialog">
     <div class="modal-dialog" style="width:75%;"> 
        <div class="modal-content">
        		<div id="selectpickerUI" style="position:absolute;"></div>
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal_title">CPT Code Alert</h4>
                </div>
                <div class="modal-body" style="max-height:300px; overflow:scroll;">
                    <div class="row">
                        <table class="table table-bordered table-hover adminnw">
                            <thead>
                                <tr>
                                    <th>CPT Code</th>
                                    <th>Comment</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="table_cpt_alert"></tbody>
                        </table>
                    </div>
                </div>
                <div id="module_buttons" class="ad_modal_footer modal-footer">
                    <button type="button" class="btn btn-success save_cpt_alert" onClick="save_cpt_alert();">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
          </div>
      </div>
  </div>    
</form>      
<script type="text/javascript">
    var arr_cpt_code = <?php echo json_encode($arr_cpt_code); ?>;
	var arr_cpt_alert_code = <?php echo json_encode($arr_cpt_alert_code); ?>;
	var arrAllShownRecords = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('id');

	function ToggleMSP(){
		fo = $('#ins_type');  fo2 = $('#claim_type');
		to = $('#msp_type');
		if(fo.val()=='MA' || fo.val()=='MB' || fo2.val().toLowerCase()=='medicare' || fo2.val()=='1') to.parent('td').css({'visibility':'visible'});
		else to.parent('td').css({'visibility':'hidden'});
	}

	function LoadResultSet(s,so,currLink,alpha,page,record_limit,searchStr,cont_num){//p=practice code, f=fac code, s=status, so=sort by;
		var cont_num = cont_num || 1;
		parent.parent.show_loading_image('block','300', 'Loading Referring physicians...');
		if(typeof(s)!='string' || s==''){s = $('#status').val()}
		s_url = "&s="+s;
		$("#status").val(s);
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
	if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
	else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
	so_url='&so='+so+'&soAD='+soAD;
	if(typeof(alpha)=='undefined' || alpha==''){
		alpha = $('#pg_aplhabet').val();
	}else{
		$('#pg_aplhabet').val(alpha);
	}
	$('a').parent('li').removeClass('pointer active');
	$('#'+alpha).addClass('activealpha');
	$('#'+alpha).parent('li').addClass('pointer active')
	
	if(typeof(page)=='undefined' || page==''){
		page = $('#page').val();
	}
	if(typeof(record_limit)=='undefined' || record_limit==''){
		record_limit = $('#record_limit').val();
	}
	else{
		$('#record_limit').val(record_limit);
	}
	search_Url = "";
	if(typeof(searchStr)!='undefined' && searchStr!=''){
		search_Url = "&searchStr="+searchStr;
	}
	pg_url = '&alpha='+alpha+'&page='+page+'&record_limit='+record_limit;		
	ajaxURL = "ajax.php?ajax_task=show_list"+s_url+so_url+pg_url+search_Url;
	//a=window.open(); a.document.write(ajaxURL);
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
		showRecords(r,cont_num);
	  }
	});
}
function showRecords(r,cont_num){
	r = JSON.parse(r);
	result = r.records;
	var total_pages = r.total_pages;
	var primary_payment_type = r.primary_payment_type;
	var secondary_payment_type = r.secondary_payment_type;
	var claim_type = r.claim_type;
	var institutional_type = r.institutional_type;
	var ins_state_payer_code = r.ins_state_payer_code;
	var ins_type = r.ins_type;
	var fee_table = r.fee_table;
	var ins_grp = r.ins_grp;
	//var prov_list = r.prov_list;
	//var cpt_code = r.cpt_code;
	var wrt_code = r.wrt_code;
	var MSP_types = r.MSP_types;
	$("#msp_type").html(MSP_types);
	h='';var no_record='yes';
	if(r != null){
		row = '';
		
		for(x in result){no_record='no';
			s = result[x];
			rowData = {};
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				rowData[y] = tdVal;
			}
			pkId = s.id;
			s.ref_management = parseInt(s.ref_management);
			row += '<td style="width:20px; padding-left:13px;"><div class="checkbox"><input type="checkbox" name="id" id="chk_'+s.id+'" class="chk_sel" value="'+s.id+'"><label for="chk_'+s.id+'"></label></div></td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');"><div style="overflow:hidden">'+s.name+'</div></td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');"><div style="overflow:hidden">'+s.in_house_code+'</div></td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');"><div style="overflow:hidden">'+s.address+'</div></td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');"><div style="overflow:hidden">'+s.phone+'</div></td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.pri_sec_pay+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.td_claim_type+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');" title="'+s.hx_operator_name+'\n'+s.hx_date_time+'">'+s.hx_operator+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');" class="text-center">'+(s.ref_management?'Yes':'No')+'</td>';
			row += '<td class="text-center">'+s.status+'</td>';
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='10' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);
	num_paging(total_pages,cont_num);
	fill_primary_payment_type(primary_payment_type);
	fill_secondary_payment_type(secondary_payment_type);
	fill_claim_type(claim_type);
	fill_institutional_type(institutional_type);
	fill_ins_state_payer_code(ins_state_payer_code);
	fill_ins_type(ins_type);
	fill_fee_table(fee_table);
	fill_ins_grp(ins_grp);
	//fill_cpt_code(cpt_code);
	//fill_prov_list(prov_list);
	fill_wrt_code(wrt_code);
	parent.parent.show_loading_image('none');
}

function addNew(ed,pkId){
	var modal_title = '';
    $('#cpt_row_count').val('');
    $('#table_cpt_alert').html('');
	$('#cpt_alert_btn').prop('disabled', false);
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#id').val('');
		$('#capitation_btn').prop('disabled', true);
		$('#cpt_alert_btn').prop('disabled', true);
		document.add_edit_frm.reset();
		document.add_edit_frm_cap.reset();
		var nz=0;
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId,document.add_edit_frm);}
}

function fillEditData(pkId,frmName){
	f = frmName;
	e = f.elements;
	frmName.reset();
	$('#id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			if(typeof v  === 'undefined') continue;
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if(o.type == "select-multiple"){
				  if(v != "undefined" && v != null){
					  var elem = $("#"+o.id);
					  if(on=="cap_cpt_code[]" || on=="cap_user[]"){
						  var optVal = '';
						  $.each(v, function(id, name){
							  optVal+="<option value='"+name.id+"' selected>"+name.value+"</option>";
						  });
						  $("select[name^="+on.replace('[]','')+"]").html(optVal);
						  $(".selectpicker").selectpicker('refresh');
					  }else{
						  elem.find('option').each(function(id, elem){
							var id = $(elem).attr('value');
								if($.inArray( id, v ) !== -1){
									$(elem).prop("selected",true);
								}else{
									$(elem).remove();
								}
							});
					  }
					  
				  }
				}
				else if(o.type == "select-one"){
					//$(o).val(v);
					o = "#"+o.id+" option[value='"+v+"']";
					//alert(o)
					$(o).prop("selected",true);
				}
				else if (o.type == "checkbox" || o.type == "radio"){
					if(v!="" && v!=0) {
                        document.getElementById(on).checked = true;
                    } else {
                        document.getElementById(on).checked = false;
                    }
				} else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}
			}
		//}
	}
	check_capt();		
	$(".selectpicker").selectpicker('refresh');	
	ToggleMSP();
}
function saveFormData(){
	if(!validateForm(document.add_edit_frm))return false;
	top.show_loading_image('hide');
	var epe=$("#emdeon_payer_eligibility").val();
	var chk_cap_pop="";
	if($("#pre_atuh_chk").is(":checked") || $("#rte_chk").is(":checked")){
		if($.trim(epe)==""){
			top.fAlert('Please enter Payer ID (RTE)');
			changeClass(document.getElementById('emdeon_payer_eligibility'));
			return false;
		}
	}
    var page = $('#page').val();
	top.show_loading_image('show','300', 'Saving data...');
	if($('#capitation_div').is(':visible')){
		chk_cap_pop="yes";
		var id=$('#add_edit_frm #id').val();
		var name=$('#add_edit_frm #name').val();
		var frm_data = $('#add_edit_frm_cap').serialize()+'&ajax_task=save_update&chk_cap_pop='+chk_cap_pop+'&id='+id+'&name='+name;
	}else{
		var frm_data = $('#add_edit_frm').serialize()+'&ajax_task=save_update';
	}
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {//a = window.open();a.document.write(d)
			//alert(d)
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert('Record already exist.');		
				return false;
			}
			if(d > 0){
				if(chk_cap_pop=="yes"){
					top.alert_notification_show('Capitation Saved Successfully.');
				}else{
					top.alert_notification_show('Record Saved Successfully.');
                    
                    changeCptSelectOptions();
				}
			}else{
				top.fAlert(d);
			}
			if($('#capitation_div').is(':visible')){
				$('#capitation_div').modal('hide');
				$('#add_edit_frm #id').val(d);
				if(arrAllShownRecords[d]){
                    arrAllShownRecords[d]['cap_cpt_code'] = '';
                    if($('#add_edit_frm_cap [name="cap_cpt_code[]"]').val()) {
                        arrAllShownRecords[d]['cap_cpt_code'] = $('#add_edit_frm_cap [name="cap_cpt_code[]"]').val().join(',');
                    }
					arrAllShownRecords[d]['cap_cpt_code[]'] = [];
					$('#add_edit_frm_cap [name="cap_cpt_code[]"]').find('option').each(function(){
						var tmp = {};
						tmp['id'] = $(this).val(); tmp['value'] = $(this).text();
						arrAllShownRecords[d]['cap_cpt_code[]'].push(tmp);
						
					});
				
                    arrAllShownRecords[d]['cap_user'] = '';
                    if($('#add_edit_frm_cap [name="cap_user[]"]').val()) {
                        arrAllShownRecords[d]['cap_user'] = $('#add_edit_frm_cap [name="cap_user[]"]').val().join(',');
                    }
					arrAllShownRecords[d]['cap_user[]'] = [];
					$('#add_edit_frm_cap [name="cap_user[]"]').find('option').each(function(){
						var tmp = {};
						tmp['id'] = $(this).val(); tmp['value'] = $(this).text();
						arrAllShownRecords[d]['cap_user[]'].push(tmp);
					});
				
					arrAllShownRecords[d]['cap_wrt_code'] = $('#add_edit_frm_cap [name="cap_wrt_code"]').val();
				}
			}else{
				$('#myModal').modal('hide');
				LoadResultSet("","","","","","","",""+page+"");
			}
			
		}
	});
}function onBlur_reason(Reasonval){$("#hidd_reason_text").val(Reasonval);}
function deleteSelectet(){
	ids = '';
	$('.chk_sel').each(function(){
		if($(this).is(':checked')){
			ids += $(this).val()+', ';
		}
	})
	var reason_field="<br /><br />Reason: <textarea style='vertical-align:text-top;width:250px; overflow:auto;' onblur='window.top.fmain.onBlur_reason(this.value);'></textarea>";
	if(ids!=''){
		top.fancyConfirm("Are you sure you want to delete?"+reason_field,"","window.top.fmain.deleteInsurance('"+ids+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}
function deleteInsurance(ids) {
	var delReason;
	if($("#hidd_reason_text")){delReason=$("#hidd_reason_text").val();}
	if($.trim(delReason)==""){top.fAlert('Please enter reason for deletion.');return false;	}
	ids = ids.substr(0,ids.length-2);
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Deleting Record(s)...');
	frm_data = 'pkId='+ids+'&delReason='+delReason+'&ajax_task=delete';//alert(frm_data)
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
			else{top.fAlert(d+'Record delete failed. Please try again.');}
		}
	});
}
function num_paging(total_pages, cont_num){
    $('#page').val(cont_num);
	var cnt_start = 1;
	var cnt_end = total_pages;
	if(total_pages>25){
		cnt_end=25;
		if(cont_num>10){cnt_start=parseInt(cont_num)-10;cnt_end=parseInt(cont_num)+10;}
	}
	var alpha = $('#pg_aplhabet').val();
	var d_t=s_class=num_span="";
	for(var i=cnt_start;i<=cnt_end;i++){
		alpha = $("#pg_aplhabet").val();
		record_limit = $("#pg_aplhabet").val();
		s_class='';d_t=i;
		if(i==cont_num){d_t=""+i+"";s_class='selected';}
		num_span +=" <span class='num_cnt "+s_class+"' id=\"conr_"+i+"\" onclick='LoadResultSet(\"\",\"\",\"\",\""+alpha+"\","+i+",\"\",\"\",\""+i+"\")'>"+d_t+"</span> ";
		if(total_pages<=i){
			break;
		}
	}
	$("#div_pages").html(num_span);
}
function fill_primary_payment_type(primary_payment_type){
	options_val = '';
	for(index in primary_payment_type){
		arr = primary_payment_type[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.type+"</option>";
	}
	$("#Insurance_payment").html(options_val);
}
function fill_secondary_payment_type(secondary_payment_type){
	options_val = '';
	for(index in secondary_payment_type){
		arr = secondary_payment_type[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.type+"</option>";
	}
	$("#secondary_payment_method").html(options_val);
}
function fill_claim_type(claim_type){
	options_val = '';
	for(index in claim_type){
		arr = claim_type[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.type+"</option>";
	}
	$("#claim_type").html(options_val);
}
function fill_institutional_type(institutional_type){
	options_val = '';
	for(index in institutional_type){
		arr = institutional_type[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.type+"</option>";
	}
	$("#institutional_type").html(options_val);
}
function fill_ins_state_payer_code(ins_state_payer_code){
	options_val = '';
	for(index in ins_state_payer_code){
		arr = ins_state_payer_code[index];
		options_val+="<option value='"+arr.id+"' title='"+arr.title+"'>"+arr.type+"</option>";
	}
	$("#ins_state_payer_code").html(options_val);
}
function fill_ins_type(ins_type){
	options_val = '';
	for(index in ins_type){
		arr = ins_type[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.type+"</option>";
	}
	$("#ins_type").html(options_val);
}

function fill_fee_table(fee_table){
	options_val = '';
	for(index in fee_table){
		arr = fee_table[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.value+"</option>";
	}
	$("#FeeTable").html(options_val);
}
function fill_ins_grp(ins_grp){
	options_val = '<option value="0">-Select-</option>';
	for(index in ins_grp){
		arr = ins_grp[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.value+"</option>";
	}
	$("#groupedIn").html(options_val);
}
function fill_cpt_code(cpt_code){
	options_val = '';
	for(index in cpt_code){
		arr = cpt_code[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.value+"</option>";
	}
	$("#cap_cpt_code").html(options_val);
	$(".selectpicker").selectpicker('refresh');
}
function fill_prov_list(prov_list){
	options_val = '';
	for(index in prov_list){
		arr = prov_list[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.value+"</option>";
	}
	$("#cap_user").html(options_val);
	$(".selectpicker").selectpicker('refresh');
}
function fill_wrt_code(wrt_code){
	options_val = '<option value="">Select</option>';
	for(index in wrt_code){
		arr = wrt_code[index];	
		options_val+="<option value='"+arr.id+"'>"+arr.value+"</option>";
	}
	$("#cap_wrt_code").html(options_val);
	$(".selectpicker").selectpicker('refresh');
}
function validate(field, index){
	validate_name = field.name;
	validate_type = field.type;
	validate_value = field.value;
	validate_minLength = (field.minLength!="undefined")?field.minLength:"";
	obj = document.getElementById(validate_name);
	switch(validate_type){
		case "char":
			patt = /[a-zA-Z]/;
			return (validate_value == "" || !(patt.test(validate_value))) ? false:true;
		break;
		case "number":
			patt = /[0-9]/;
			return (validate_value == "" || !(patt.test(validate_value)) || (validate_minLength!="" && validate_value.length < validate_minLength))?false:true;
		break;
		case "alphanum":
			patt = /[0-9a-zA-Z\-]/;
			if(validate_value == "" || !(patt.test(validate_value))){
				return false;
			}
			else if(validate_minLength!="" && validate_value.length < validate_minLength){
				fields[index]['msg'] = "Please Enter NPI#  as exactly  10 characters.";
				return false;
			}
		return true;
		case "password":
			objUN = document.getElementById(field.username);
			obConfirm = document.getElementById(field.confirm_password);
			objHidPass = document.getElementById('hid_password');
			var userFname = document.getElementById('FirstName').value;
			var userLname = document.getElementById('LastName').value;
			if(objUN.value != "" && validate_value == "" && objHidPass.value == ""){
				return false;
			}else if(validate_value != "" && objUN.value == ""){
				fields[index]['msg'] = "Please Enter Username.";
				return false;
			}else if(validate_value!="" && objUN.value!=""){
				if(validate_value.length < validate_minLength){
					fields[index]['msg'] = "Must be at least 8 characters long.";
					return false;
				}
				if(!validate_value.match(/[0-9]/g) || !validate_value.match(/[a-zA-Z]/g)){
					fields[index]['msg'] = "Must contain alphanumeric characters";
					return false;
				}
				if( validate_value == objUN.value || validate_value == userFname || validate_value == userLname){
					fields[index]['msg'] = "Password can not have user First Name or Last Name or user login id.";
					return false;
				}
				if(validate_value !=""  && validate_value != obConfirm.value){
					fields[index]['msg'] = "Confirm password should match password.";
					return false;
				}
			} 
			return true;
		break;
	}
}
fields = {};
fields[0] = {name:"name",type:'char',displayName:"Company Name"};
fields[1] = {name:"in_house_code",type:'alphanum',displayName:"Practice Code"};
function validateForm(f){
	fldArr = {};
	validFlag = true;
	for(i in fields){
		fld_name = fields[i]['name'];
		obj = document.getElementById(fld_name);
		fields[i]['value'] = obj.value;
		if(!validate(fields[i],i)){
			changeClass(obj);
			fldArr[i] = fields[i];//
		}
	}
	msg = "Enter following fields correctly:- <br>";
	for(i in fldArr){
		validFlag = false;
		msg += " &bull; "+fldArr[i]['displayName']+" ";
		if(typeof(fldArr[i]['msg']) != "undefined"){
			msg += " ; "+fldArr[i]['msg'];
		}
		msg +="<br>";
	}
	
	if($("#Insurance_payment option:selected").val() == "Electronics" || $("#secondary_payment_method option:selected").val() == "Electronics"){
		if($('#Payer_id_pro').val() == ""){
			changeClass(document.getElementById('Payer_id_pro'));
			validFlag = false;
			msg +=" &bull; Payer ID (Professional)<br>";
		}
	}
	if(!validFlag){
		fAlert(msg);
		return false;
	}
	return true;
}
function changeClass(obj){
	if(obj.value == "")
	obj.className = 'mandatory form-control';
	else
	obj.className = 'form-control';
}
function getFocusObj(obj){
		var objId = obj.id;
		if(document.getElementById(objId)){
			var str = document.getElementById(objId).value;
			setCaretPosition(objId, str.length);
		}
}
function setCaretPosition(elemId, caretPos) {
	var elem = document.getElementById(elemId);
	if(elem != null) {
		if(elem.createTextRange) {
			var range = elem.createTextRange();
			range.move('character', caretPos);
			range.select();
		}
		else {
			if(elem.selectionStart) {
				elem.focus();
				elem.setSelectionRange(0, caretPos);
			}
			else
				elem.focus();
		}
	}
}
function stateChanges(){
	if (xmlHttp.readyState == 4){
		result=xmlHttp.responseText
		if(document.getElementById("TaxId")){
			document.getElementById("TaxId").value = result;
		}
	}
}	
function export_csv(){
	window.location="../admin/billing/add_insurance/export2.php";
}
function set_status(status,id){
	top.show_loading_image('show');
	$.ajax({
		type: "POST",
		url: "ajax.php?ajax_task=set_status&id="+id+"&status="+status,
		success: function(d) {
			LoadResultSet();
			top.show_loading_image('hide');
		}
	});
}
/*function popup_dbl(divid,sourceid,destinationid,act,odiv){
	if(act=="single" || act=="all"){
		if(act=='single')	{
			$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
		}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
	}else if(act=="single_remove" || act=="all_remove"){
		if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
		if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
		$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
			var at = $(a).text(), bt = $(b).text();
			return (at > bt)?1:((at < bt)?-1:0);
		}));
		$("#"+destinationid).val('');
	}else{
		$("#"+destinationid+" option").remove();
		$("#"+odiv+" option").clone().appendTo("#"+destinationid);
		$("#"+divid).show("clip");
	}
}
function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
		if(action=="done"){
			var sel_cnt=$("#"+sourceid+" option").length;
			$("#"+divid).hide("clip");
			$("#"+destinationid+" option").each(function(){$(this).remove();})
			$("#"+sourceid+" option").appendTo("#"+destinationid);
			$("#"+destinationid+" option").attr({"selected":"selected"});
			$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
			if(sel_cnt>8){
				$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
			}
		}else if(action=="close"){
			$("#"+divid).hide("clip");
		}
}*/
function srh_records(){
	searchStr = $("#search").val();
	LoadResultSet('','','','','','',searchStr)
}
function get_ins_data(name){
	top.show_loading_image('show');
	$.ajax({
		type: "POST",
		url: "ajax.php?ajax_task=get_ins_data&name="+name,
		complete:function(r){top.show_loading_image('hide');},
		success: function(id) {
			if(id != ""){
				fillEditData(id,document.add_edit_frm);
			}
		}
	});
}
function capitation_fun(){
	var modal_title = 'Capitation';
	var pkId = $('#id').val();
	if(pkId>0){
		fillEditData(pkId,document.add_edit_frm_cap);
	}
	if(pkId<=0){
		$('#cap_cpt_code').empty();
		$('#cap_user').empty();
		$('#cap_wrt_code').val('');
	}
	$('#capitation_div .modal-header .modal-title').text(modal_title);
	$('#capitation_div').modal('show');
}
function popup_dbl(divid,sourceid,destinationid,act,odiv){
	var modal_title = '';
	
	if(divid == 'pop_up_problem_list'){
		modal_title = 'Please Add/Remove Problems(s) using Arrow Buttons.';
	}else if(divid == 'pop_up_cpt'){
		modal_title = 'Please Add/Remove CPT(s) using Arrow Buttons.';
	}
	
	if(act=="single" || act=="all"){
		if(act=='single')	{
			$("#"+sourceid+" option:Selected").appendTo("#"+destinationid);
		}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
	}else if(act=="single_remove" || act=="all_remove"){
		if(act=="single_remove"){$("#"+sourceid+"  option:Selected").appendTo("#"+destinationid);}
		if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
		$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
			var at = $(a).text(), bt = $(b).text();
			return (at > bt)?1:((at < bt)?-1:0);
		}));
		$("#"+destinationid).val('');
	}else{
		$("#"+destinationid+" option").remove();
		$("#"+odiv+" option").clone().appendTo("#"+destinationid);
		//$("#"+divid).show("clip");
		$('#InsModal .modal-content .modal-header .modal-title').text(modal_title);
		$('#'+divid+'').css('display','block');
		$('.'+divid+'').css('display','block');
		$('#InsModal').modal('show');
	}	
	$('#InsModal').on('hidden.bs.modal', function (e) {
		$('#'+divid+'').css('display','none');
		$('.'+divid+'').css('display','none');

        changeCptSelectOptions();
	});
}

function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
	if(action=="done"){
		var sel_cnt=$("#"+sourceid+" option").length;
		$("#"+destinationid+" option").each(function(){$(this).remove();})
		$("#"+sourceid+" option").appendTo("#"+destinationid);
		$("#"+destinationid+" option").prop("selected",true);
		$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
		if(sel_cnt>8){
			$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
		}
		$("#"+divid).modal('hide');
	}else if(action=="close"){
		$("#"+divid).modal('hide');
	}
}

function display_cpt_alert_div(){
	parent.parent.show_loading_image('show');
	$('#table_cpt_alert').html('');
	$('#cpt_row_count').val('');
	var saved_cpt_alert=0;
	var pkId = $('#id').val();
	var post_data = "ajax_task=get_cpt_alert&ins_id="+pkId;
	$.ajax({ 
		type: "POST",
		data:post_data,
		url: top.JS_WEB_ROOT_PATH+"/interface/admin/billing/add_insurance/ajax.php",
		success: function(data){
			if (data.indexOf('~') >= 0){
				var arrData = data.split('~');
				if(arrData[1]>0){
					$('#table_cpt_alert').html(arrData[0]);
                    $('#cpt_row_count').val(arrData[1]);
					$('.selectpicker').selectpicker('refresh');						
				}
			}	
		},
        complete: function (data) {
            saved_cpt_alert=$('#cpt_row_count').val();
			if(saved_cpt_alert<=0){saved_cpt_alert=0;}
            addcptAlertRows(saved_cpt_alert,'');
        }
	});	

	$('#cpt_alert_div').modal('show');
	$('.selectpicker').selectpicker('refresh');
	parent.parent.show_loading_image('hide');
}

function addcptAlertRows(rowNo,change_row){
	var pkId = $('#id').val();
	$("#cpt_ins_id").val(pkId);
	var rowData='';
	i=parseInt(rowNo)+1;
	if(change_row!=""){
		var imgObj = document.getElementById("add_cpt_alert_row"+change_row);
		imgObj.title = 'Delete Row';
		imgObj.src = top.JS_WEB_ROOT_PATH+'/library/images/closerd.png';
		imgObj.onclick=function(){ 
			$("#cptAlertRow"+change_row).remove(); 
		}
	 	imgObj.id="add_cpt_alert_row"+change_row;
	}
	var cpt_code_options = '';
	$.each(arr_cpt_alert_code,function(id,val){
		cpt_code_options += '<option value="'+id+'">'+val+'</option>';
	});
	$('#cpt_row_count').val(i);
	rowData+='<tr id="cptAlertRow'+i+'">';
	rowData+='<td style="vertical-align: top!important; max-width:220px;"><select name="cpt_code_id'+i+'[]" id="cpt_code_id'+i+'" class="selectpicker" data-container="#selectpickerUI" multiple="multiple" data-width="100%" data-size="10" data-actions-box="true" data-title="Please select" data-live-search="true">';
	rowData+=cpt_code_options;
	rowData+='</select></td><td><textarea class="form-control" name="cpt_comment'+i+'" id="cpt_comment'+i+'" rows="3"></textarea></td>';
	rowData+='<td class="pt10 text-center pointer" style="vertical-align:middle;"><img id="add_cpt_alert_row'+i+'" src="'+top.JS_WEB_ROOT_PATH+'/library/images/add_icon.png" alt="Add More" onClick="addcptAlertRows('+i+','+i+');" ></td>';
	rowData+='</tr>';
	if(i=='1'){
		$('#table_cpt_alert').html(rowData);
	}else{
		$("#cptAlertRow"+rowNo).after(rowData);
	}
	
	$('.selectpicker').selectpicker('refresh');
}


function save_cpt_alert(){
    $('.save_cpt_alert').prop('disabled', true);
	parent.parent.show_loading_image('show');
    var cpt_row_count=$('#cpt_row_count').val();
	var frm_data = $("#add_edit_frm_cpt_alert").serialize()+"&cpt_row_count="+cpt_row_count+"&ajax_task=save_cpt_alert";
	$.ajax({ 
		type: "POST",
		url: top.JS_WEB_ROOT_PATH+"/interface/admin/billing/add_insurance/ajax.php",
		data:frm_data,
		success: function(data){
            $('.save_cpt_alert').prop('disabled', false);
			if(data!=""){
				top.fAlert(data);
			}
			parent.parent.show_loading_image('hide');
			$('#cpt_alert_div').modal('hide');
		}
	});	
}	


function remove_cpt_alert(rowNo){
	parent.parent.show_loading_image('show');
	var id_val = $('#cpt_ins_edit_id'+rowNo).val();
	if(id_val>0){
		var post_data = "ajax_task=del_cpt_alert&cpt_ins_del_id="+id_val;
		$.ajax({ 
			type: "POST",
			url: top.JS_WEB_ROOT_PATH+"/interface/admin/billing/add_insurance/ajax.php",
			data:post_data,
			success: function(data){
				$("#cptAlertRow"+rowNo).remove();
			}
		});
	}
	parent.parent.show_loading_image('hide');
}
</script>

<script type="text/javascript">
	LoadResultSet();
	
	var fmb = 'top.fmain';
	var ar = [["add_new","Add New",fmb+".addNew();"],["dx_cat_del","Delete",fmb+".deleteSelectet();"],["csv_insurance","Export CSV",fmb+".export_csv();"]];
	top.btn_show("ADMN",ar);
	$(document).ready(function(){
		check_checkboxes();
		$( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true});
		$("#search").keypress(function (evt){
			if(evt.keyCode==13){	
				srh_records();
			}
		});
        
        $('#InsModal').on('shown.bs.modal', function(){
           $('#selected_cpt option:selected').each(function(id, elem){
                var value = $(elem).val();
                //$('#cpt1 option[value="'+value+'"]').addClass('hide');
                $('#cpt1 option[value="'+value+'"]').remove();
            });
        });

	});
	set_header_title('Insurance');	
	var first="A",last="Z";alphabet= '';
	var ch='';
	var alphaNum="";
	alphabet+="<li class=\"num\"><a id=\"0-9\" onClick='LoadResultSet(\"\",\"\",\"\",\"0-9\")' style='cursor:pointer;'>0-9</a></li>";
	for(var i = first.charCodeAt(0); i <= last.charCodeAt(0); i++){
		ch=eval("String.fromCharCode("+i+")");
		cl='';
		if(ch=='A'){cl='pointer active';}
		status = $("#status").val();
		s = $("#ord_by_field").val();
		so = $("#ord_by_ascdesc").val();
		alphabet+="<li class=\""+cl+"\"><a id=\""+ch+"\" onClick='LoadResultSet(\"\",\"\",\"\",\""+ch+"\")' style='cursor:pointer'>"+ch+"</a></li>";
	}
	$("#pagenation_alpha_order").html(alphabet);

	var customSpeciality = new Array('Anesthesia','Cardiology','Cardiovascular surgery','Clinical laboratory sciences','Clinical Neurophysiology','Dermatology','Emergency medicine','Endocrinology','Family Medicine','Gastroenterology','General surgery','Geriatrics','Hematology','Hepatology','Infectious disease','Intensive care medicine','Maxillofacial surgery','Nephrology','Neurology','Neurosurgery','Obstetrics and gynecology','Oncology','Ophthalmology','Orthopedic surgery','Otolaryngology','Palliative care','Pathology','Pediatrics','Pediatric surgery','Physical medicine and rehabilitation','ENT','Plastic surgery','Proctology','Psychiatry','Pulmonology','Radiology','Rheumatology','Surgical oncology','Thoracic surgery','Transplant surgery','Trauma surgery','Urology','Vascular surgery');
	if(customSpeciality){
		//var objSpeciality = new actb(document.getElementById('specialty'),customSpeciality);
	}
	$('[data-toggle="tooltip"]').tooltip();
    
    function check_capt() {
        if($('#capitation').is(":checked") == true) {
            $('#capitation_btn').removeAttr('disabled');
            $('#capitation').val(1);
        } else {
            $('#capitation_btn').attr('disabled', true);
            $('#capitation').val(0);
        }
    }
    
    $('#capitation').click(function() {
        check_capt();
    });
    
    function changeCptSelectOptions() {
        var optStr = '';
        <?php foreach($arr_cpt_code as $cptkey=> $cpt_names) { ?>
            optStr+='<option value="<?php echo $cptkey; ?>"><?php echo addslashes($cpt_names); ?></option>';
        <?php } ?>
        $('#cpt1').empty().html(optStr);
    }
</script>
<?php require_once("../../admin_footer.php"); ?>

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
$without_pat="yes"; 
require_once("../accounting/acc_header.php");
$title = 'Paper';
//------------------------ Insurance Company Detail------------------------//
$qry = "select * from insurance_companies order by in_house_code ASC";
$res = imw_query($qry);
while($row = imw_fetch_array($res)){
	$ins_comp_data[$row['id']]=$row;
	
	$ins_name = $row['in_house_code'];
	if($ins_name == ''){
		$ins_name = $row['name'];
		if(strlen($ins_name) > 20){
			$ins_name = substr($ins_name,0,20).'....';
		}
	}
	$ins_name_arr[$row['id']]=$ins_name;
}
//------------------------ Insurance Company Detail------------------------//

//------------------------ Insurance Company Groups Detail ------------------------//
$fet_ins_groups=imw_query("select id,title from ins_comp_groups where delete_status='0' order by title");
while($row_ins_groups=imw_fetch_array($fet_ins_groups)){
	$ins_gro_data[]=$row_ins_groups;
}
//------------------------ Insurance Company Groups Detail ------------------------//
 
//------------------------ Groups Detail ------------------------//
$fet_groups=imw_query("select * from groups_new where del_status='0' order by name");
while($row_groups=imw_fetch_array($fet_groups)){
	$gro_data[]=$row_groups;
}
//------------------------ Groups Detail ------------------------//

//------------------------ Users Detail ------------------------//
$phy_id_cn=$GLOBALS['arrValidCNPhy'];
$sql = imw_query("select * from users WHERE delete_status='0' order by lname ASC");
while($row=imw_fetch_array($sql)){			
	$mname="";
	if($row["mname"]!=""){
		$mname=" ".trim($row["mname"]).'.';
	}
	$name=$row["lname"].", ".$row["fname"].$mname;
	if($row["Enable_Scheduler"]=='1' || in_array($row["user_type"],$phy_id_cn)){
		$prov_name_arr[$row["id"]]=$name;
	}
}	
//------------------------ Users Detail ------------------------//
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/billing_electronic.js"></script>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet">
<form name="frm_billing" action="" method="post">
<div class="createbtch">
	<div class="row">
		<div class="col-sm-5">
			<?php
				if($_REQUEST['WithoutPrintub']!=""){
					$print_cms_sel['WithoutPrintub']="checked";
				}else if($_REQUEST['Printub']!=""){
					$print_cms_sel['Printub']="checked";
				}else if($_REQUEST['PrintCms_white']!=""){
					$print_cms_sel['PrintCms_white']="checked";
				}else{
					$print_cms_sel['PrintCms']="checked";
				}
			?>
			<div class="row">
				<div class="col-sm-3">
					<div class="checkbox">
						<input type="checkbox" id="PrintCms" name="PrintCms" value="PrintCms" onClick="selectChkBox(this);" <?php echo $print_cms_sel['PrintCms']; ?>>
						<label for="PrintCms"><strong>CMS 1500</strong></label>
					</div>
				</div>	
				<div class="col-sm-4">
					<div class="checkbox">
						<input type="checkbox" id="PrintCms_white" name="PrintCms_white" value="PrintCms_white" onClick="selectChkBox(this);" <?php echo $print_cms_sel['PrintCms_white']; ?>>
						<label for="PrintCms_white"><strong>CMS 1500 - Red Form</strong></label>
					</div>
				</div>	
				<div class="col-sm-2">
					<div class="checkbox">
						<input type="checkbox" id="Printub" name="Printub" value="Printub" onClick="selectChkBox(this);" <?php echo $print_cms_sel['Printub']; ?>>
						<label for="Printub"><strong>UB-04</strong></label>
					</div>
				</div>	
				<div class="col-sm-3">
					<div class="checkbox">
						<input type="checkbox" id="WithoutPrintub" name="WithoutPrintub" value="WithoutPrintub" onClick="selectChkBox(this);" <?php echo $print_cms_sel['WithoutPrintub']; ?>>
						<label for="WithoutPrintub"><strong>UB-04 - Red Form</strong></label>
					</div>
				</div>
			</div>		
		</div>	
		<div class="col-sm-4">
			<?php
				if($_REQUEST['InsComp']=="3"){
					$InsComp_sel[3]="checked";
				}else if($_REQUEST['InsComp']=="2"){
					$InsComp_sel[2]="checked";
				}else{
					$InsComp_sel[1]="checked";
				}
			?>
			<div class="row">
				<div class="col-sm-4">
					<div class="radio radio-inline">
						<input type="radio" id="InsComp_pri" name="InsComp" value="1" onClick="show_check_box();" <?php echo $InsComp_sel[1]; ?>/>
						<label for="InsComp_pri"><strong>Primary Ins.</strong></label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="radio radio-inline">	
						<input type="radio" id="InsComp_sec" name="InsComp" value="2" onClick="show_check_box('true');" <?php echo $InsComp_sel[2]; ?>/>
						<label for="InsComp_sec"><strong>Secondary Ins.</strong></label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="radio radio-inline">	
						<input type="radio" id="InsComp_tri" name="InsComp" value="3" onClick="show_check_box();" <?php echo $InsComp_sel[3]; ?>/>
						<label for="InsComp_tri"><strong>Tertiary Ins.</strong></label>
					</div>
				</div>
			</div>	
		</div>
		<!--<div class="col-sm-2">
			<div class="radio radio-inline">
				<input type="radio" id="Print_process_sum" name="Print_process" value="summary" checked="checked"/>
				<label for="Print_process_sum"><strong>Summary</strong></label>
			</div>
			<div class="radio radio-inline">	
				<input type="radio" id="Print_process_det" name="Print_process" value="details"/>
				<label for="Print_process_det"><strong>Details</strong></label>
			</div>
		</div>-->
		<div class="col-sm-2">
			<div id="elec_claims_inc_id" class="checkbox">
				<input type="checkbox" name="inc_elec_claims" id="inc_elec_claims" value="1" <?php if($_REQUEST['inc_elec_claims']==1){echo "checked";} ?> />
				<label for="inc_elec_claims"><strong>Include electronic payors</strong></label>
			</div>	
		</div>
		<!--<div class="col-sm-2">
			<div class="checkbox">
				<input type="checkbox" id="SaveFile" name="SaveFile" value="SaveFile" <?php if($_REQUEST['SaveFile']=='SaveFile'){echo "checked";} ?>>
				<label for="SaveFile"><strong>Save To File</strong></label>
			</div>
		</div>-->
	</div>
	<div class="row pt10">
		<div class="col-sm-3">
			<?php
			if($_REQUEST['Posted_End_date']==""){
				$_REQUEST['Posted_End_date']=date(phpDateFormat());
			}
			if($_REQUEST['DOS_End_date']==""){
				$_REQUEST['DOS_End_date']=date(phpDateFormat());
			}
			?>
			<label>Posted Charges</label>
			<div class="row">
				<div class="col-sm-6">
					<div class="input-group">
						<div class="input-group-addon labbg">From</div>
						<input type="text" name="Posted_Start_date" id="Posted_Start_date" value="<?php echo $_REQUEST['Posted_Start_date']; ?>" class="form-control date-pick">
						<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
					</div>
				</div>	
				<div class="col-sm-6">	
					<div class="input-group">
						<div class="input-group-addon labbg">To</div>
						<input type="text" name="Posted_End_date" id="Posted_End_date" value="<?php echo $_REQUEST['Posted_End_date'];?>" class="form-control date-pick">
						<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
					</div>
				</div>
			</div>		
		</div>
		<div class="col-sm-3">
			<label>DOS</label>
			<div class="row">
				<div class="col-sm-6">
					<div class="input-group">
						<div class="input-group-addon labbg">From</div>
						<input type="text" name="DOS_Start_date" id="DOS_Start_date" value="<?php echo $_REQUEST['DOS_Start_date']; ?>" class="form-control date-pick">
						<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
					</div>
				</div>	
				<div class="col-sm-6">	
					<div class="input-group">
						<div class="input-group-addon labbg">To</div>
						<input type="text" name="DOS_End_date" id="DOS_End_date" value="<?php echo $_REQUEST['DOS_End_date']; ?>" class="form-control date-pick">
						<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
					</div>
				</div>
			</div>		
		</div>
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-3">
					<label for="insurance_id">Insurance</label>
					<select name="Insurance[]" id="insurance_id" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
						<?php
						foreach($ins_comp_data as $key=>$val){
							if($ins_comp_data[$key]['Insurance_payment']=='HCFA1500' || $ins_comp_data[$key]['secondary_payment_method']=='HCFA1500'){ 	
							$sel="";
							if(in_array($ins_comp_data[$key]['id'],$_REQUEST['Insurance'])){
								$sel="selected";
							}
						?>
								<option value="<?php echo $ins_comp_data[$key]['id']; ?>" <?php echo $sel; ?>><?php echo $ins_name_arr[$key]; ?></option>
						<?php }	} ?>
					</select>
				</div>
                <div class="col-sm-3">
					<label for="insurance_gro_id">Insurance Group</label>
					<select name="insurance_gro[]" id="insurance_gro_id" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
						<?php
						foreach($ins_gro_data as $key=>$val){
							$sel="";
							if(in_array($ins_gro_data[$key]['id'],$_REQUEST['insurance_gro'])){
								$sel="selected";
							}
						?>
								<option value="<?php echo $ins_gro_data[$key]['id']; ?>" <?php echo $sel; ?>><?php echo $ins_gro_data[$key]['title']; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-3">
					<label for="groups">Practice Group</label>
					<select id="groups" name="groups"  class="selectpicker" data-width="100%">
						<option value="">Select All</option>
						<?php
						foreach($gro_data as $key=>$val){
							$sel="";
							if($_REQUEST['InsComp']>0){
								if($_REQUEST['groups']==$gro_data[$key]['gro_id']){
									$sel="selected";
								}
							}else{
								if($gro_data[$key]['group_institution']=='1'){
									$sel="selected";
								}
							}
						?>
							<option value="<?php echo $gro_data[$key]['gro_id']; ?>" <?php echo $sel; ?>><?php echo $gro_data[$key]['name']; ?></option>
						<?php } ?>
					</select>
				</div>	
				<div class="col-sm-3">
					<label for="physicians">Physician</label>
					<select id="physicians" name="physicians" class="selectpicker" data-width="100%">
						<option value="">Select All</option>
						<?php
						foreach($prov_name_arr as $key=>$val){
							$sel="";
							if($_REQUEST['InsComp']>0){
								if($_REQUEST['physicians']==$key){
									$sel="selected";
								}
							}else{
								if($_SESSION['authId']==$key && !in_array(strtolower($billing_global_server_name), array('farbowitz'))){
									$sel="selected";
								}
							}
							
						?>
							<option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $val; ?></option>
						<?php } ?>
					</select>
				</div>		
			</div>		
		</div>
	</div>	
	<div class="pt10"></div>
</div>
</form>
<?php if($_REQUEST['InsComp']>0){?>
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="validclaim">
				<div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/valid_claim.png" alt=""/></figure><h2 id="span_validclaims"><span>Valid Claims</span>0</h2></div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="validamt">
				<div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/valid_amount.png" alt=""/></figure><h2 id="span_validamount"><span>Valid Amount</span>$0.00</h2></div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="invaildclaim">
				<div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/invail_claim.png" alt=""/></figure><h2 id="span_invalidclaims"><span>Invalid Claims</span>0</h2></div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6">
			<div class="invaildamnt">
				<div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/invaild_amount.png" alt=""/></figure><h2 id="span_invalidamount"><span>Invalid Amount</span>$0.00</h2></div>
			</div>
		</div>
	</div>
	<div class="table-responsive" style="margin:0px; height:<?php echo $_SESSION['wn_height']-490;?>px; overflow-x:auto; width:100%;" >
		<?php
			include_once("paper_billing_result.php");
		?>
	</div>
<?php } ?>	
</div>
</body>
</html>
<script type="text/javascript">
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("re_print","Re-Print","top.fmain.re_print();");
	mainBtnArr[1] = new Array("start_process","View Claims","top.fmain.printProcess();");
	mainBtnArr[2] = new Array("print_process","Print Claims","top.fmain.check_data();");
	top.btn_show("PPR",mainBtnArr);
	top.$('#acc_page_name').html('<?php echo $title; ?>');
</script>
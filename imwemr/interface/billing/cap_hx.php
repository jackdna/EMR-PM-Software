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
$title = "Capitation Hx";
require_once("../accounting/acc_header.php");
require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
?>
<script type="text/javascript">
function cap_hx_detail(cap_date,cap_opt,cap_main_id){
	window.location.href="cap_hx.php?cap_date="+cap_date+"&cap_opt="+cap_opt+"&cap_main_id="+cap_main_id;
}
function view_all_batch(){
	window.location.href="cap_hx.php";
}
function print_batch(){
	var cap_date='<?php echo $_REQUEST['cap_date']; ?>';
	var cap_opt='<?php echo $_REQUEST['cap_opt']; ?>';
	var cap_main_id='<?php echo $_REQUEST['cap_main_id']; ?>';
	window.open('cap_hx_print.php?cap_date='+cap_date+'&cap_opt='+cap_opt+"&cap_main_id="+cap_main_id,'','scrollbars=yes,resizable=1');
}
</script>
<?php
	$phy_id_arr=array();
	$sel_prov=imw_query("select id,lname,fname from users order by lname,fname asc");
	while($fet_prov=imw_fetch_array($sel_prov)){
		$phy_id_arr[]=$fet_prov['id'];
		$phy_id_name[$fet_prov['id']]=$fet_prov['lname'].', '.$fet_prov['fname'];
	}
?>
<div class="purple_bar">
	<div class="row">
		<div class="col-sm-4">
			<label>Capitation Hx</label>	
		</div>
		 <?php if($_REQUEST['cap_date']!=""){?>
			<div class="col-sm-4">
				<label>Batch Date : <?php echo get_date_format($_REQUEST['cap_date'],"mm-dd-yyyy"); ?></label>	
			</div>
			<div class="col-sm-4 text-right">
				<label>Batch Created By : <?php echo $phy_id_name[$_REQUEST['cap_opt']]; ?></label>	
			</div>
		<?php }?>			
	</div>	
</div>
<div class="table-responsive" style="width:100%;height:<?php print $_SESSION['wn_height']-350;?>px;overflow:auto;">
	<?php if($_REQUEST['cap_date']==""){ ?>
		<table class="table table-bordered table-hover table-striped">
			<tr class="grythead">
				<th>Batch Date</th>
				<th>Total Patient</th>
				<th>Total Writeoff</th>
				<th>Batch Created By</th>
			</tr>
			<?php
				$getSqlDateForamt = get_sql_date_format();
				$row=imw_query("select DATE_FORMAT(entered_date,'$getSqlDateForamt') as batch_name,entered_by,DATE_FORMAT(entered_date,'$getSqlDateForamt') as created_date,
								sum(write_off_amount) as tot_wrt, count(patient_id) as total_pat,entered_by,cap_main_id
								from cap_batch group by cap_main_id order by DATE_FORMAT(entered_date,'%Y-%m-%d') desc");
				while($rec=imw_fetch_array($row)){
				?>
				<tr>
					<td class="text-center"><a href="javascript:cap_hx_detail('<?php echo $rec['created_date']; ?>','<?php echo $rec['entered_by']; ?>','<?php echo $rec['cap_main_id']; ?>');"  class="text_10"><?php echo $rec['created_date']; ?></a></td>
					<td class="text-center"><a href="javascript:cap_hx_detail('<?php echo $rec['created_date']; ?>','<?php echo $rec['entered_by']; ?>','<?php echo $rec['cap_main_id']; ?>');"  class="text_10"><?php echo $rec['total_pat']; ?></a></td>
					<td class="text-right"><a href="javascript:cap_hx_detail('<?php echo $rec['created_date']; ?>','<?php echo $rec['entered_by']; ?>','<?php echo $rec['cap_main_id']; ?>');"  class="text_10"><?php echo numberFormat($rec['tot_wrt'],2); ?></a></td>
					<td class="text-left"><a href="javascript:cap_hx_detail('<?php echo $rec['created_date']; ?>','<?php echo $rec['entered_by']; ?>','<?php echo $rec['cap_main_id']; ?>');"  class="text_10"><?php echo $phy_id_name[$rec['entered_by']]; ?></a></td>
				</tr> 
			<?php } ?>
		</table>
	<?php }else{?>
		<table class="table table-bordered table-hover table-striped">
			<tr class="grythead">
				<th>Patient Name - ID</th>
				<th>Encounter Id</th>
				<th>Ins. Plan Name</th>
				<th>DOS</th>
				<th>DOC</th>
				<th>Charges</th>
				<th>Balance</th>
				<th>Copay</th>
				<th>Pending Copay</th>
			</tr>
			<?php
			$cap_opt=$_REQUEST['cap_opt'];
			$cap_date=$_REQUEST['cap_date'];
			$cap_main_id=$_REQUEST['cap_main_id'];
			$strQry = "select cap_batch.*,patient_data.fname,patient_data.mname,patient_data.lname from cap_batch join patient_data on patient_data.id=cap_batch.patient_id where cap_main_id='$cap_main_id' group by cap_batch.id order by patient_data.lname asc,patient_data.fname asc";
			$den_qry=imw_query($strQry);
			$tot_sum=0;
			if(imw_num_rows($den_qry)>0){
				while($den_fet=imw_fetch_array($den_qry)){
				?>						
				<tr>
					<td>
						<?php
						 $patientName = ucwords(trim($den_fet['lname'].", ".$den_fet['fname']." ".$den_fet['mname']));
						 echo $patientName .' - '.$den_fet['patient_id']; 
						 $tot_pat_arr[$den_fet['patient_id']]=$patientName;
						?>
					</td>
					<td>
						<?php
							echo $den_fet['encounter_id'];
						?>
					</td>
					<td>
						<?php
							echo $den_fet['ins_plan_name'];
						?>
					</td>
					<td class="text-center">
						<?php
							$dat_exp_show_dos = explode("-", $den_fet['dos']);
							$shw_dat_dos = date('m-d-Y',mktime(0,0,0,$dat_exp_show_dos[1],$dat_exp_show_dos[2],$dat_exp_show_dos[0]));
							echo $shw_dat_dos;
						?>
					</td>
					<td class="text-center">
						<?php
							if($den_fet['doc']!="0000-00-00"){
								$dat_exp_show_doc = explode("-", $den_fet['doc']);
								$shw_dat_doc = date('m-d-Y',mktime(0,0,0,$dat_exp_show_doc[1],$dat_exp_show_doc[2],$dat_exp_show_doc[0]));
								echo $shw_dat_doc;
							}
						?>
					</td>
					<td class="text-right">
						<?php echo numberFormat($den_fet['charges'],2,'yes'); $tot_amt_arr[]=$den_fet['charges']; ?>
					</td>
					<td class="text-right">
						<?php echo numberFormat($den_fet['balance'],2,'yes'); $tot_bal_arr[]=$den_fet['balance']; ?>
					</td>
					<td class="text-right">
						<?php echo numberFormat($den_fet['copay'],2,'yes'); $tot_copay_arr[]=$den_fet['copay']; ?>
					</td> 
					<td class="text-right">
						<?php echo numberFormat($den_fet['pending_copay'],2,'yes'); $tot_pend_copay_arr[]=$den_fet['pending_copay']; ?>
					</td> 
				</tr>
				<?php
				}
			}
			if(imw_num_rows($den_qry)==0){?>
				<tr><td colspan="9" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td></tr>
			<?php }else{
			?>
				<tr class="purple_bar" style="font-weight:bold;">
					<td class="text-right">
						Total Patient : <?php echo count($tot_pat_arr); ?>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="text-right">
						<?php echo numberFormat(array_sum($tot_amt_arr),2,'yes'); ?>
					</td>
					<td class="text-right">
						<?php echo numberFormat(array_sum($tot_bal_arr),2,'yes'); ?>
					</td>
					<td class="text-right">
						<?php echo numberFormat(array_sum($tot_copay_arr),2,'yes'); ?>
					</td>
					<td class="text-right">
						<?php echo numberFormat(array_sum($tot_pend_copay_arr),2,'yes'); ?>
					</td> 
				</tr>
			 <?php
			}
			?> 
		
	</table>
	<?php } ?>
</div>
<div class="row pt10">	
	<div class="col-sm-12 text-center">
		<?php if($_REQUEST['cap_date']!=""){?>
			<input type="button" name="view_all_batch" id="view_all_batch" class="btn btn-primary" value="View All Batch" onClick="view_all_batch();">
			<input type="button" name="print_batch" id="print_batch" class="btn btn-primary" value="Print Batch" onClick="print_batch();">
		<?php }?>
			<input type="button" name="CancelBtn" id="CancelBtn" class="btn btn-danger" value="Close" onClick="window.close();">
	</div>
</div>
</div>
</body>
</html>
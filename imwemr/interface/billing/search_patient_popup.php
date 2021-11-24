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
require_once(dirname(__FILE__).'/../../config/globals.php'); 
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once($GLOBALS['fileroot'].'/library/classes/class.app_base.php'); 
$app_base= new app_base();
if($_REQUEST['elem_status']!=""){
	$elem_status=$_REQUEST['elem_status'];
}else{
	$elem_status="Active";
}

if($_REQUEST["btn_sub"] == "Search"){   
	$txt_for =addslashes(trim($_REQUEST["txt_for"]));
	$sel_by = $_REQUEST["sel_by"];	
	if(empty($txt_for)){
	  $sel_by = "Nothing";      
	}else{
		if($sel_by != "Resp.LN" && $sel_by != "Ins.Policy" && $sel_by != "Address" && $sel_by != "External MRN") {
			$elem_status=$sel_by;
			$sel_by=$app_base->getFindBy($txt_for);
		}
	}     
	if($txt_for<>""){			
		list ($result1s,$nurow,$prevDataPtIdArr,$Total_Records) = $app_base->core_search($sel_by,$elem_status,$txt_for,$previousSearch = false);
	}
	$elem_status = $sel_by;
}
$str_patient="";
$arr_pt=array();
if(count($result1s) > 0){
	/*for($k = 0; $k < count($result1s); $k++){
		$arr_pt[] = $result1s[$k]["id"];
	}*/
	foreach($result1s as $key=>$subArray)
	{
		$arr_pt[] = $subArray["id"];
	}
	
	$total_ids=count($arr_pt);
	$str_patient = implode(",", $arr_pt);
	
	$where=$join="";
	if($total_ids>10)
	{
		$tmp_table="PatientIdList_".time().'_'.$_SESSION["authId"];
		//add temporary table here
		imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
		imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (id INT)");
		$insertValues=str_replace(',','),(',$str_all_pt);
		
		imw_query("INSERT INTO $tmp_table (id) VALUES($insertValues)");
		$join=" INNER JOIN ".$tmp_table." pl ON pcl.patient_id  = pl.id ";
	}
	else{
		//add IN clause in query
		$where="pcl.patient_id IN (".$str_all_pt.") AND ";
	}
	//$sql = "select sum(totalBalance) as totalBalance, patient_id from patient_charge_list where del_status='0' and patient_id IN (".$str_patient.") GROUP BY patient_id";
	$sql = "select sum(pcl.totalBalance) as totalBalance, pcl.patient_id from patient_charge_list pcl $join where $where pcl.del_status='0' GROUP BY pcl.patient_id";
	$res = imw_query($sql);
	if(imw_num_rows($res) > 0){
		while($arr=imw_fetch_assoc($res))
		{
			$arr_balance[$arr["patient_id"]] = $arr["totalBalance"];
		}
	}
	
	$where=$join="";
	if($total_ids>10)
	{
		$join=" INNER JOIN ".$tmp_table." pl ON sa.sa_patient_id  = pl.id ";
	}
	else{
		//add IN clause in query
		$where="sa.sa_patient_id IN (".$str_all_pt.") AND ";
	}
	/*$sql_appt = "SELECT sa.sa_patient_id, date_format(sa.sa_app_start_date,'%m-%d-%Y') as ap_start_date, sa.sa_app_starttime, sa.procedureid, sp.proc, sp.acronym FROM schedule_appointments sa
			JOIN slot_procedures sp ON (sp.id=sa.procedureid) 
			WHERE sa.sa_patient_id IN (".$str_patient.") AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
			AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > NOW()";*/
	$sql_appt = "SELECT sa.sa_patient_id, date_format(sa.sa_app_start_date,'%m-%d-%Y') as ap_start_date, sa.sa_app_starttime, sa.procedureid, sp.proc, sp.acronym FROM schedule_appointments sa
			INNER JOIN slot_procedures sp ON (sp.id=sa.procedureid) 
			$join
			WHERE $where sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
			AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > NOW()";
	$res_appt = imw_query($sql_appt);
	if(imw_num_rows($res_appt) > 0){
		while($arr_appt=imw_fetch_assoc($res_appt))
		{
			$str_apt_details = "";
			$str_apt_details .= $arr_appt['ap_start_date'].' '.core_time_format($arr_appt['sa_app_starttime']).' ';
			$str_apt_details .= $arr_appt['acronym']=='' ? $arr_appt['proc'] : $arr_appt['acronym'];
			$arr_pt_appt[$arr_appt["sa_patient_id"]] = $str_apt_details;
		}
	}
	//remove temporary table
	if($tmp_table){
	imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);}
}

?>
<?php $srh_row="col-sm-12";if($show_extra_div!=""){ $srh_row="col-sm-6";}?>     
<form name="frm_sel" id="frm_sel" method="post">
<input type="hidden" name="pat_srh_id" id="pat_srh_id" value="<?php echo $_REQUEST['pat_srh_id']; ?>">
    <div class="row" id="homeDropDownSCH">
        <div class="<?php echo $srh_row; ?>" id="search_patient">
            <div class="row">
            	<?php if($show_extra_div==""){echo '<div class="col-sm-3"></div>';}?>
                <div class="col-sm-3">
                    <input type="text" class="form-control" name="txt_for" id="txt_for" placeholder="Search patient..." value="<?php echo stripslashes($_REQUEST["txt_for"])?>" onkeypress="{if (event.keyCode==13)return chk('pat_srh');}">
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input type="text" id="sel_by" name="sel_by" value="<?php echo $elem_status; ?>" readonly class="form-control">
                        <div style="white-space:nowrap">
                            <div class="dropdown">
                                <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary" data-target="#"><span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu" id="main_search_dd"></ul>
                            </div>
                            <input type='hidden' id="btn_sub" name="btn_sub" value='Search'>
                            <input type="hidden" name="from" value="<?php echo ($fax)?$_REQUEST['from']:''; ?>">
                            <input type="hidden" name="fieldKey" value="<?php echo $faxfieldKey; ?>">
                            <button id="save_butt" type="button" class="btn tsearch" onClick="chk('pat_srh');"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
        <?php if($show_extra_div!=""){ echo $show_extra_div;}?>       
    </div>
</form>     
<?php 
	if($_REQUEST['pat_srh_id']=="" && $_REQUEST['txt_for']!=""){
	if($div_res_hg==""){
		$div_res_hg="430";		
	}
?>
	<div class="row pt10">
        <?php if($show_srh_title!=""){ echo $show_srh_title;}?>    
		<div class="col-sm-12">
			<div class="row" style="height:<?php echo $div_res_hg; ?>px; overflow:auto">
				<table class="table table-striped table-bordered table-hover">
					<?php if(count($result1s)>0){?>
						<tr class="grythead">
							<th>Patient Name - Id</th>
							<th>Address</th>
							<th>Phone</th>
							<th>Balance</th>
							<th>Future Appt.</th>
						</tr>
					<?php
						foreach ($result1s as $patientData){
							
							$patientname=$patient_srh_name=$patientData['lname'];
							if($patientData['fname']!="")	{
								$patientname.=', '.$patientData['fname'];
								$patient_srh_name.=', '.$patientData['fname'];
							}
							if($patientData['mname']!="")	{
								$patientname.=' '.$patientData['mname'];
							}
							
							//--- GET PATIENT ADDRESS ----
							$street = trim($patientData['street']);
							if(empty($patientData['street']) == false and empty($patientData['city']) == false){
								$street .= "<br>";
							}
							$street .= $patientData['city'].', ';
							$street .= $patientData['state'].' ';
							$street .= trim($patientData['postal_code']);
							$street = trim(ucfirst($street));
							if($street[0] == ','){
							$street = substr($street,1);
							}
							//--- CHANGE PHONE FORMAT -------
							$phone_home = core_phone_format(trim($patientData['phone_home']));
							
							$balance = numberFormat($arr_balance[$patientData["id"]], 2);
							$apptDetails 	= $arr_pt_appt[$patientData["id"]];
							$call_final_fun="";
							if($call_fun!=""){
								$call_final_fun=$call_fun."(".$patientData['id'].")";
							}
							?>
							<tr onClick="chk(<?php echo $patientData['id']; ?>)" style="cursor:pointer;">
							<td>
								<input type="hidden" name="pat_name_<?php echo $patientData['id']; ?>" id="pat_name_<?php echo $patientData['id']; ?>" value="<?php echo stripslashes($patient_srh_name); ?>">
								<?php echo stripslashes($patientname).' - '.$patientData['id']; ?>
                            </td>
							<td><?php echo $street; ?></td>
							<td><?php echo $phone_home; ?></td>
							<td class="text-right"><?php echo $balance; ?></td>
							<td><?php echo $apptDetails; ?></td>
							</tr>
						<?php	
						}
					}else{
					?>
						<tr>
							<td colspan="7" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
						</tr>
					<?php
					}
					?>
				</table>
			</div>
            <div class="col-sm-12 text-center" id="module_buttons">
                <input type="button" class="btn btn-danger" align="bottom" name="close" id="close" onclick="close_fun()" value="Close">	
            </div>
		</div>
	</div>
<?php } ?> 

<script type="text/javascript">
var show_extra_div='<?php $show_extra_div;?>';
function chk(srh_val)
{
	$('#pat_srh_id').val('');
	if(srh_val=="pat_srh"){
		if($('#sel_by').val() == '')
		{
			alert("Please select 'Select By'. ");
			return false;
		}
		
		if($('#txt_for').val() == '')
		{
			alert("Please Fill in 'For'. ");
			return false;
		}
	}else{
		$('#pat_srh_id').val(srh_val);
		$('#txt_for').val($('#pat_name_'+srh_val).val());
	}
	document.frm_sel.submit();
}
function get_dropdown(icon_name){
	$.ajax({
		url:'<?php echo $GLOBALS['webroot']; ?>/interface/core/ajax_handler.php?task='+icon_name+'',
		success:function(response){
			var result = JSON.parse(response);
			$('#main_search_dd').html(result.recent_search);
		}
	});
}
if(show_extra_div==""){
	function close_fun(){
		window.close();
	}
}
$(document).ready(function(){
	get_dropdown('get_icon_bar_status');
	$('body').on('click','#main_search_dd li a:lt(11)',function(){
		var fv = $(this).text();
		if(typeof(fv)!='undefined' && fv!='Advance') 
		{
			$('#sel_by').val(fv);
			$('#findByShow').val(fv);
			if($(this).hasClass('noclose') === false){
				$('ul#main_search_dd').trigger('click');
			}
		}
	});
	
	$('body').on('click','#main_search_dd li a:gt(11)',function(){
		$('#pat_srh_id').val('');
		var fv = $(this).text();
		var pt_id = $(this).attr('pt_id');
		if(typeof(pt_id)=='undefined'){
			$('#sel_by').val(fv).attr('title',fv);
		}
		else{
			var pt_name = fv.split('-');
				$("#txt_for").val(pt_name[0]);
				$('#sel_by').val('Active');
				document.frm_sel.submit();	
		}
		$('.dropdown-submenu > .dropdown-menu').css('display','none');
	});
	
});
</script>
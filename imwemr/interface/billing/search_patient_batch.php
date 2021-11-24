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
function show_enc_gro_color($g_color){
	$show_style="";
	if($g_color!=""){
		$show_style="style='border-left:10px solid $g_color;'";
	}
	return $show_style;
}
if($_REQUEST['batch_pat_srh']!=""){
	$txt_for =addslashes(trim($_REQUEST["batch_pat_srh"]));
	$b_id=$_REQUEST["b_id"];
	$sel_by = $_REQUEST["sel_by"];
	if(empty($txt_for)){
	  $sel_by = "Nothing";      
	}else{
		if($sel_by != "Resp.LN" && $sel_by != "Ins.Policy" && $sel_by != "Address") {
			$elem_status=$sel_by;
			$sel_by=$app_base->getFindBy($txt_for);
		}
	}  
	if($txt_for<>""){			
		list ($results,$nurow,$prevDataPtIdArr,$Total_Records) = $app_base->core_search($sel_by,$elem_status,$txt_for,$previousSearch = false);
	}	
	
	$str_patient="";
	$arr_pt=array();
	if(count($results) > 0){
 		foreach($results as $res_key => $res_val){
			$pat_arr[] = $results[$res_key]["id"];
			$pat_name_arr[$results[$res_key]["id"]]=$results[$res_key]["lname"].', '.$results[$res_key]["fname"];
		}
		$str_patient = implode(",", $pat_arr);
		
		$bal_whr="";
		if($_REQUEST['sel_all_enc']==""){
			$bal_whr=" and patient_charge_list_details.newBalance>0";
		}
		$grp_id_whr="";
		if($_REQUEST['sel_grp_ids']!=""){
			$grp_id_whr=" and patient_charge_list.gro_id in(".$_REQUEST['sel_grp_ids'].")";
		}
		
		$sql = imw_query("select patient_charge_list.patient_id,patient_charge_list.charge_list_id,patient_charge_list.encounter_id,patient_charge_list.date_of_service,
						patient_charge_list.totalBalance,patient_charge_list_details.newBalance,patient_charge_list.gro_id
						from patient_charge_list join patient_charge_list_details on patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id 
						where patient_charge_list_details.del_status='0' and patient_charge_list_details.patient_id IN (".$str_patient.") $bal_whr $grp_id_whr
						order by patient_charge_list.date_of_service desc");
		while($row=imw_fetch_assoc($sql))
		{
			$chl_data_arr[$row['patient_id']][$row['charge_list_id']]=$row;
			$proc_balance_arr[$row['patient_id']][$row['charge_list_id']][]=$row['newBalance'];
		}
		
		$grp_qry=imw_query("select group_color,gro_id,name from groups_new");
		while($grp_row=imw_fetch_array($grp_qry)){
			$group_name_arr[$grp_row['gro_id']]=ucfirst($grp_row['name']);
			$group_color_arr[$grp_row['gro_id']]=$grp_row['group_color'];
		}
		
		$pat_data="";
		foreach($pat_name_arr as $pat_id => $pat_val){
			if(count($chl_data_arr[$pat_id])>0){
				$pat_data .="<tr class='outbox outboxbrd'><td colspan='3'>".$pat_val.' - '.$pat_id."</td></tr>";
				$pat_data .="<tr class='outtr'><td>DOS</td><td>E.Id</td><td>Amount</td></tr>";
				foreach($chl_data_arr[$pat_id] as $chl_key => $chl_val){
					$dos=$chl_data_arr[$pat_id][$chl_key]['date_of_service'];
					$enc_id=$chl_data_arr[$pat_id][$chl_key]['encounter_id'];
					$enc_bal=$chl_data_arr[$pat_id][$chl_key]['totalBalance'];
					$enc_proc_bal=array_sum($proc_balance_arr[$pat_id][$chl_key]);
					$gro_id=$chl_data_arr[$pat_id][$chl_key]['gro_id'];
					$group_color=$group_color_arr[$gro_id];
					if($_REQUEST['deb_patient_id']>0){
						$pat_data .="<tr class='outbox pointer' onclick='load_batch_file(\"makePayment\",\"encounter_id=$enc_id&crd_patient_id=$pat_id&deb_patient_id=".$_REQUEST['deb_patient_id']."&deb_chld_id=".$_REQUEST['deb_chld_id']."&deb_amt=".$_REQUEST['deb_amt']."&deb_ins_type=".$_REQUEST['deb_ins_type']."\")'><td ".show_enc_gro_color($group_color).">".get_date_format($dos)."</td><td>".$enc_id."</td><td>".numberFormat($enc_proc_bal,2,'yes')."</td></tr>";
					}else{
						$pat_data .="<tr class='outbox pointer' onclick='load_batch_file(\"makePayment\",\"b_id=$b_id&encounter_id=$enc_id&batch_pat_id=$pat_id\")'><td ".show_enc_gro_color($group_color).">".get_date_format($dos)."</td><td>".$enc_id."</td><td>".numberFormat($enc_proc_bal,2,'yes')."</td></tr>";
					}
				}
			}
		}
	}
	if($pat_data=="")
	{
		echo "<div class='alert alert-info'>No Outstanding Amount</div>";
	}else{
		echo "<table class='table table-bordered'>".$pat_data."</table>";
	}
	
}else{
	if($_REQUEST['elem_status']!=""){
		$elem_status=$_REQUEST['elem_status'];
	}else{
		$elem_status="Active";
	}
?>
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/css/billinginfo.css">
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
    <div class="row" id="search_patient" style="margin-top:1px;">
        <div class="col-sm-6">
            <input type="text" class="form-control" name="txt_for" id="txt_for" placeholder="Search patient..." value="<?php echo stripslashes($_REQUEST["txt_for"])?>" onkeypress="{if (event.keyCode==13)return chk('pat_srh');}">
        </div>
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" id="sel_by" name="sel_by" value="<?php echo $elem_status; ?>" onkeypress="{if (event.keyCode==13)return chk('pat_srh')}" readonly class="form-control">
                <div style="white-space:nowrap">
                    <div class="dropdown">
                        <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary" data-target="#">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu" id="main_search_dd"></ul>
                    </div>
                    <input type="hidden" name="pat_srh_id" id="pat_srh_id" value="">
                    <input type='hidden' id="btn_sub" name="btn_sub" value='Search'>
                    <input type="hidden" name="from" value="<?php echo ($fax)?$_REQUEST['from']:''; ?>">
                    <input type="hidden" name="fieldKey" value="<?php echo $faxfieldKey; ?>">
                    <button id="save_butt" type="button" class="btn tsearch" onClick="chk('pat_srh');">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    </button>
                </div>	
            </div>
        </div>
    </div>
	<script type="text/javascript">
        function chk(srh_val)
        {
			var no_enter_pay_msg = '<div class="alert alert-info mt10">Please select an encounter.</div>';
            $('#pat_srh_id').val('');
			var sel_by= $('#sel_by').val();
			var txt_for= $('#txt_for').val();
			var sel_all_enc = '';
			var sel_grp_ids = '';
			var sel_grp_ids_arr = [];
			if($('#all_enc')){
				if($('#all_enc').is(':checked')==true){
					sel_all_enc= 1;
				}
			}
			if($('#batch_grp_srh')){
				if($('#batch_grp_srh option:selected').length){
					$('#batch_grp_srh option:selected').each(function(){ 
					   sel_grp_ids_arr.push($(this).val());
					});
					if(sel_grp_ids_arr.length) sel_grp_ids = sel_grp_ids_arr.join(',');
				}
			}
			var b_id = '<?php echo $_REQUEST['b_id']; ?>';
			var deb_patient_id = '<?php echo $_REQUEST['deb_patient_id']; ?>';
			var deb_chld_id = '<?php echo $_REQUEST['deb_chld_id']; ?>';
			var deb_amt = '<?php echo $_REQUEST['deb_amt']; ?>';
			var deb_ins_type = '<?php echo $_REQUEST['deb_ins_type']; ?>';
            if(srh_val=="pat_srh"){
				var msg = "";
				if(sel_by == ""){
					msg = "Please select 'Select By'.\n";
				}
				if(txt_for == ""){
					msg += "Please select a patient or enter value to search.";	
				}
				if(msg!=""){
					fAlert(msg);
					return false;
				}

				$.ajax({
						url:'<?php echo $GLOBALS['webroot']; ?>/interface/billing/search_patient_batch.php?deb_patient_id='+deb_patient_id+'&deb_chld_id='+deb_chld_id+'&deb_amt='+deb_amt+'&deb_ins_type='+deb_ins_type+'&b_id='+b_id+'&batch_pat_srh='+txt_for+'&sel_by='+sel_by+'&sel_all_enc='+sel_all_enc+'&sel_grp_ids='+sel_grp_ids+'',
						beforeSend: function(){
							$('#outstanding_amount').html(landingSectionLoader);
							$('#batch_transactions_list').html(no_enter_pay_msg);
							$('#outstanding_amount_main').removeClass('hide');
							$('#batch_transactions_list').removeClass('col-sm-12').addClass('col-sm-10');
						},
						success:function(response){
						$('#outstanding_amount').html(response);
					}
				});
            }else{
                $('#pat_srh_id').val(srh_val);
				$.ajax({
					url:'<?php echo $GLOBALS['webroot']; ?>/interface/billing/search_patient_batch.php?deb_patient_id='+deb_patient_id+'&deb_chld_id='+deb_chld_id+'&deb_amt='+deb_amt+'&deb_ins_type='+deb_ins_type+'&b_id='+b_id+'&batch_pat_srh='+srh_val+'&sel_by='+sel_by+'&sel_all_enc='+sel_all_enc+'&sel_grp_ids='+sel_grp_ids+'',
					beforeSend: function(){
						$('#outstanding_amount').html(landingSectionLoader);
						$('#batch_transactions_list').html(no_enter_pay_msg);
						$('#outstanding_amount_main').removeClass('hide');
						$('#batch_transactions_list').removeClass('col-sm-12').addClass('col-sm-10');
					},
					success:function(response){
						$('#outstanding_amount').html(response);
					}
				});
            }
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
                }
                $('.dropdown-submenu > .dropdown-menu').css('display','none');
            });
            
        });
    </script>
<?php } ?>    
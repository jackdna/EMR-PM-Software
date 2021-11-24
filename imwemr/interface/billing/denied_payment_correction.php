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
$pg_title = 'ERA Claim Rejections';
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
if(inter_date_format() == 'mm-dd-yyyy'){
	$global_date_format = 'm-d-Y';
}
$operator_id = $_SESSION['authId'];
if($_REQUEST['denied_resp']>0){
	foreach($_REQUEST['chkbx'] as $d_key=>$d_val){
		$sel_qry=imw_query("select encounter_id from deniedpayment where deniedId='$d_val'");
		$row_qry=imw_fetch_array($sel_qry);
		$encounter_id=$row_qry['encounter_id'];
		
		imw_query("update deniedpayment set next_responsible_by='$operator_id' where deniedId='$d_val'");
		
		set_payment_trans($encounter_id);
	}
}
$sel_resp=imw_query("select denial_resp_all from denial_resp");
$row_resp=imw_fetch_array($sel_resp);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:top.global_date_format, //'m-d-Y'
		});	
		$('.selectpicker').selectpicker();
	});
	function submit_frm(id){
		$('#denied_resp').val(id);
		top.show_loading_image("show","150");
		document.frm.submit();
	}
</script>
<div class="row">
	<form name="frm" action="denied_payment_correction.php" method="post">
    <input type="hidden" name="denied_resp" id="denied_resp" value="">
        <div class="purple_bar form-inline col-sm-12">
        	<div class="col-sm-2">
                <div class="checkbox">
                    <input type="checkbox" id="posted_enc" name="posted_enc" value="1" <?php if($_REQUEST['posted_enc']=="1"){echo "checked";} ?>>
                    <label for="posted_enc">Posted Encounter</label>
                </div>
            </div>
            <div class="col-sm-2">
                <label>Date From : </label>
                <div class="input-group">
                    <input id="date1" type="text" name="date_frm" value="<?php echo $_REQUEST['date_frm']; ?>" maxlength="10" class="date-pick form-control input-sm">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                </div>
            </div>
            <div class="col-sm-2">
                <label>To : </label>
                <div class="input-group">
                    <input id="date2" type="text" name="date_to" value="<?php echo $_REQUEST['date_to']; ?>" maxlength="10" class="date-pick form-control input-sm">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                </div>
            </div>
            <div class="col-sm-2 text-left">
                <select name="srh_by" class="selectpicker" data-width="100%">
                    <option value="dos" <?php if($_REQUEST['srh_by']=="dos"){echo "selected";} ?>>DOS</option>
                    <option value="denied" <?php if($_REQUEST['srh_by']=="denied"){echo "selected";} ?>>Denied</option>
                </select>
            </div>
            <div class="col-sm-4 text-right">
            </div>	
        </div>
        <div class="table-responsive" style="width:100%;height:<?php print $_SESSION['wn_height']-410;?>px;overflow:auto;">
        <?php
            if($_REQUEST['date_frm']<>"" && $_REQUEST['date_to']<>""){
            $dat_chk_frm=$_REQUEST['date_frm'];
            $dat_frm_final = getDateFormatDB($dat_chk_frm);
            $dat_chk_to=$_REQUEST['date_to'];
            $dat_to_final = getDateFormatDB($dat_chk_to);
                if($_REQUEST['srh_by']=='dos'){
                    $dat_whr="and (pcl.date_of_service  between '$dat_frm_final' and  '$dat_to_final')";
                }else if($_REQUEST['srh_by']=='denied'){
                    $dat_whr="and (dp.deniedDate  between '$dat_frm_final' and  '$dat_to_final')";
                }	
            }
            
            //find PQRI and G code id
            $chk_cpt_cat_id = "";
            $cpt_ary = "select cft.cpt_fee_id from cpt_category_tbl cct,cpt_fee_tbl cft	where cct.cpt_cat_id = cft.cpt_cat_id and 
                     ((cct.cpt_category like 'PQRI%' or cct.cpt_category like 'G%') or (cft.cpt4_code like 'PQRI%' or cft.cpt4_code like 'G%'))";
                        
            $sel_pqri=imw_query($cpt_ary);
            $chk_cpt_cat_id = array();
            while($fet_pqr_gcode=imw_fetch_array($sel_pqri)){
                $chk_cpt_cat_id[]=$fet_pqr_gcode['cpt_fee_id'];
            }
            $cpt_imp=implode(',',$chk_cpt_cat_id);
			
			$whr_next_resp="";
			if($row_resp['denial_resp_all']==0){
				$whr_next_resp=" and dp.next_responsible_by='0'";
			}
			$whr_posted_enc="dp.deniedDate>pcl.postedDate and  ";
			if($_REQUEST['posted_enc']=="1"){
				$whr_posted_enc="";
			}
			
            if($cpt_imp){
                $strQry = "select dp.deniedId,dp.charge_list_detail_id,dp.deniedAmount,dp.patient_id,dp.encounter_id,dp.deniedDate,pcl.date_of_service,pd.fname,pd.mname,pd.lname
                        from patient_charge_list_details pcld, deniedpayment dp,patient_charge_list pcl,patient_data pd 
                        WHERE $whr_posted_enc pcld.del_status='0' and pcld.charge_list_detail_id = dp.charge_list_detail_id and pcld.charge_list_id =  pcl.charge_list_id 
                        and pcld.patient_id  =  pd.id and pcld.newBalance  > 0 and dp.denialDelStatus=0 and dp.status=0 and pcld.procCode not in($cpt_imp) $dat_whr
                        $whr_next_resp order by pcl.date_of_service desc";
                    $den_qry=imw_query($strQry);
                }
            
            $sno=0;
        ?>
            <table class="table table-bordered table-hover table-striped">
                <tr class='grythead'>
                    <th>
                        <div class="checkbox">
                            <input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();">
                            <label for="chkbx_all"></label>
                        </div>
                    </th>
                    <th>Patient Name</th>
                    <th>DOS</th>
                    <th>Encounter Id</th>
                    <th>Rejection Date</th>
                    <th>Rejection Amount</th>
                </tr>
                <?php
                $tot_sum=0;
                if(count($den_qry)>0){
                    while($den_fet=imw_fetch_array($den_qry)){
                    $sno++;
                    ?>						
                    <tr class="text-center">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" name="chkbx[]" id="chkbx_<?php echo $sno; ?>" value="<?php echo $den_fet['deniedId']; ?>" class="chk_box_css">	
                                <label for="chkbx_<?php echo $sno; ?>"></label>
                            </div>
                        </td>
                        <td class="text-left">
                            <a href="javascript:window.top.LoadAccountingView('<?php echo $den_fet['patient_id']; ?>', '<?php echo $den_fet['encounter_id']; ?>', 'enter_payment');" class="text_purple">
                            <?php
                            $patientName = ucwords(trim($den_fet['lname'].", ".$den_fet['fname']." ".$den_fet['mname']));
                             echo $patientName .' - '.$den_fet['patient_id']; 
                            ?>
                            </a>	
                        </td>
                        <td>
                            <?php
                            $dat_exp_show_dos = explode("-", $den_fet['date_of_service']);
                            $shw_dat_dos = date(''.$global_date_format.'',mktime(0,0,0,$dat_exp_show_dos[1],$dat_exp_show_dos[2],$dat_exp_show_dos[0]));
                            echo $shw_dat_dos;
                            ?>
                        </td>
                        <td>
                            <?php echo $den_fet['encounter_id']; ?>
                        </td>
                        <td>
                            <?php
                            $dat_exp_show = explode("-", $den_fet['deniedDate']);
                            $shw_dat=date(''.$global_date_format.'',mktime(0,0,0,$dat_exp_show[1],$dat_exp_show[2],$dat_exp_show[0]));
                            echo $shw_dat;
                            ?>
                        </td>
                        <td class="text-right">
                            <?php echo numberformat($den_fet['deniedAmount'],2); ?>
                        </td> 
                    </tr>
                    <?php
                    $tot_sum=$tot_sum+$den_fet['deniedAmount'];
                }if(imw_num_rows($den_qry)==0){
                ?>
                    <tr><td colspan="6" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td></tr>
            <?php }
                    
                }
                ?>
            </table>
        </div>	
        <?php
            if($den_qry){
                if(imw_num_rows($den_qry)>0){ ?>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 text-right purple_bar">
                            <strong style="margin-right:10px;"><?php echo numberformat($tot_sum,2); ?></strong>
                        </div>	
                    </div>
                </div>	
        <?php 
                } 
            }
        ?>			
    </div>
</form>    
<script type="text/javascript">
	var ar = [["done_data","Search","top.fmain.submit_frm('');"]
	<?php if($row_resp['denial_resp_all']==0){?>
	,["next_responsible","Next Responsible","top.fmain.submit_frm('1');"]
	<?php } ?>
	];
	top.btn_show("CLRJ",ar);
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');	
</script>
</div>
</body>
</html>
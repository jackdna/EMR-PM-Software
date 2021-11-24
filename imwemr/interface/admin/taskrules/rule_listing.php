<?php
include_once("../admin_header.php");
$library_path = $GLOBALS['webroot'] . '/library';
extract($_REQUEST);

require_once('../../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;

/* Get Task Manager Categories and rules */
$categories = array();
$sql = 'SELECT * FROM tm_rule_category ';
$resp = imw_query($sql);
$category_option="";
if ($resp && imw_num_rows($resp) > 0) {
    while ($row = imw_fetch_assoc($resp)) {
        $categories[$row['id']] = $row['tm_rule_category'];
        $sel=($_REQUEST['id']==$row['id'])?'selected':'';
        $category_option.= "<option ".$sel." value='".$row['id']."'>".$row['tm_rule_category']."</option>";
    }
}

$rules = array();
$rule_name = array();
$rule_sql = "SELECT * FROM tm_rules ";
$rule_rs = imw_query($rule_sql);
if ($rule_rs && imw_num_rows($rule_rs) > 0) {
    while ($rules_row = imw_fetch_assoc($rule_rs)) {
        $rules[] = $rules_row;
        $rule_name[$rules_row['id']] = $rules_row['tm_rule_name'];
    }
}


$pt_status=array();
$pt_sql = "SELECT * FROM patient_status_tbl ";
$pt_rs = imw_query($pt_sql);
if ($pt_rs && imw_num_rows($pt_rs) > 0) {
    while ($row = imw_fetch_assoc($pt_rs)) {
        $pt_status[$row['pt_status_id']] = $row['pt_status_name'];
    }
}

$pt_ac_status=array();
$pt_ac_sql = "Select id, status_name FROM account_status WHERE del_status='0' ORDER BY status_name ";
$pt_ac_rs = imw_query($pt_ac_sql);
if ($pt_ac_rs && imw_num_rows($pt_ac_rs) > 0) {
    while ($row = imw_fetch_assoc($pt_ac_rs)) {
        $pt_ac_status[$row['id']] = $row['status_name'];
    }
}

$rule_list = array();
$rule_sql = "SELECT * FROM tm_rules_list where rule_status=0 order by addedon desc";

require_once($GLOBALS['fileroot'].'/library/classes/paging.inc.php');
$page = !isset($_REQUEST['page'])?1:$_REQUEST['page'];

$objPaging = new Paging(25,$page);
$objPaging->sort_by = '';
$objPaging->sort_order = '';
$objPaging->query = $rule_sql;
$objPaging->func_name = "show_rule_list";
$rq_obj = $objPaging->fetchLimitedRecords();
foreach($rq_obj as $rules_row) {
    $rule_list[] = $rules_row;
}

/* Get User Groups */
$user_groups = array();
$sql = "select * from `user_groups` where `status` = '1' order by `name`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        $user_groups[$row['id']] = $row['name'];
    }
}

/* Get Users */
$users_arr = array();
$sql = "select id,fname,mname,lname from `users` where `delete_status` = '0' order by `lname`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
        $users_arr[$row['id']] = $prov_name;
    }
}

/* Get Facilities */
$facility_arr = array();
$qry = "select id, name from facility order by name";	
$sql_rs = imw_query($qry);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        $facility_arr[$row['id']] = $row['name'];
    }
}

/* Get slot_procedures array */
$arr_proc_names=array();
$qry = "SELECT id, proc, acronym, user_group, labels, ref_management FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 AND active_status = 'yes' ORDER BY proc";
$res = imw_query($qry);
if(imw_num_rows($res) > 0){
    while($arr_proc = imw_fetch_assoc($res)) {
        $arr_proc_names[$arr_proc["id"]]=$arr_proc["proc"];
    }
}

/* Get schedule_status array */
$ss_rule_name=array();
$ssrule_sql = "SELECT id,status_name FROM schedule_status where status=1 order by status_name asc";
$sssql_rs = imw_query($ssrule_sql);
if ($sssql_rs && imw_num_rows($sssql_rs) > 0) {
    while ($ssrow = imw_fetch_assoc($sssql_rs)) {
        if(trim($ssrow['status_name'])!=''){
            $ss_rule_name[$ssrow['id']] = $ssrow['status_name'];
        }
    }
}

/* Get Ref Physicians */
$ref_phy_arr = array();
$qry = "select physician_Reffer_id, TRIM(CONCAT(IF(Title!='',CONCAT(Title,' '),''),LastName,', ', FirstName ,' ',MiddleName)) AS name from refferphysician
        WHERE delete_status=0
        AND LastName!=''
        AND FirstName!=''
        AND primary_id = 0
        order by TRIM(LastName) asc
        ";
$sql_rs = imw_query($qry);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        if(trim($row['name'])!=''){
            $ref_phy_arr[$row['physician_Reffer_id']] = $row['name'];
        }
    }
}


//Ar aging Group, facility , ins group, ins comp
//--- GET Groups SELECT BOX ----
$group_id_arr = array();
$group_query = "Select gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
while ($group_res = imw_fetch_array($group_query_res)) {
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
}

//SET INSURANCE COMPANY DROP DOWN
$ins_comp_arr = array();
$insQryRes = insurance_provider_xml_extract();
for ($i = 0; $i < count($insQryRes); $i++) {
    if(!$insQryRes[$i]['attributes']) continue;
    if($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
        $ins_comp_arr[$ins_id] = $ins_name;
    }
}


//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
$ins_group_arr = array();
$org_group_arr = array();
$ins_group_options = '';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

    $org_group_arr[$ins_grp_id]=$ins_grp_name;
    $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "' ORDER BY id";
    $res = imw_query($qry);
    $tmp_grp_ins_arr = array();
    if (imw_num_rows($res) > 0) {
        while ($det_row = imw_fetch_array($res)) {
            $tmp_grp_ins_arr[] = $det_row['id'];
        }
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        $ins_group_arr[$grp_ins_ids] = $ins_grp_name;
    }
}

//Get POS facilities
$pos_facilities_arr = array();
$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl
				left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
				order by pos_facilityies_tbl.headquarter desc,
				pos_facilityies_tbl.facilityPracCode";
$res = imw_query($qry);
while($row=imw_fetch_assoc($res)){
    $id = $row['id'];
    $name = $row['name'];
    $pos_prac_code = $row['pos_prac_code'];
    $pos_facilities_arr[$id] = $name.' - '.$pos_prac_code;
}


$cpt_fee_arr = array();
$cpt_qry = imw_query("select cpt_fee_id,cpt_prac_code from cpt_fee_tbl");
while ($cpt_row = imw_fetch_array($cpt_qry)) {
    $cpt_fee_arr[$cpt_row['cpt_fee_id']] = $cpt_row['cpt_prac_code'];
}

?>

<body>
    <style>.tooltip-inner{min-width: 100px;max-width: 800px;}</style>
    <div class="container-fluid">
        <div class="">
            <textarea id="hidd_reason_text" class="hide"></textarea>
            <input type="hidden" name="ord_by_field" id="ord_by_field" value="name">
            <input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
            <div class="row" style="height:<?php echo ($_SESSION['wn_height'] - 340); ?>px; overflow-x:hidden; overflow:auto;">
                <div class="col-sm-12">
                    <table class="table table-bordered table-hover adminnw" id="table_color">
                        <thead>
                            <tr>
                                <th width="3%" class="text-center" style="padding-left:14px;">
                                    <div class="checkbox checkbox-inline">
                                        <input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
                                        <label for="chk_sel_all"></label>	
                                    </div>
                                </th>
                                <th width="9%" class="pointer link_cursor">Rule Category<span></span></th>				
                                <th width="6%" class="pointer link_cursor">Rule Name<span></span></th>
                                <th width="8%" class="pointer link_cursor">Reason Codes<span></span></th>
                                <th width="8%" class="pointer link_cursor">Patient Status<span></span></th>
                                <th width="10%" class="pointer link_cursor">Pt Account Status<span></span></th>
                                <th width="8%" class="pointer link_cursor">Pt Appt. Status<span></span></th>
                                <th width="9%" class="pointer link_cursor">Appt Procedure<span></span></th>
                                <th width="6%" class="pointer link_cursor">A/R Aging<span></span></th>
                                <th width="6%" class="pointer link_cursor">Transaction<span></span></th>
                                <th width="6%" class="pointer link_cursor">User Group<span></span></th>
                                <th width="6%" class="pointer link_cursor">User Name<span></span></th>
                                <th width="16%" class="pointer link_cursor">Comments<span></span></th>
                            </tr>
                        </thead>
                        <tbody id="result_set">
                            <?php if(!empty($rule_list)) {
                                foreach($rule_list as $list) {
                                    $rule_name_str=$rule_name[$list['rule_id']];
                                    $user_group='';
                                    if(isset($list['user_group']) && $list['user_group'] != '') {
                                        $uglist = explode(', ', $list['user_group']);
                                        foreach($uglist as $ugrow) {
                                            $user_group.= $user_groups[$ugrow]."<br />";
                                        }
                                        $user_group = substr($user_group,0,-2);
                                    }

                                    $user_name='';
                                    if(isset($list['user_name']) && $list['user_name'] != '') {
                                        $unlist = explode(', ', $list['user_name']);
                                        foreach($unlist as $unrow) {
                                            $user_name.= $users_arr[$unrow]."<br />";
                                        }
                                        $user_name = substr($user_name,0,-2);
                                    }

                                    $ptsstr='';
                                    if(isset($list['pt_status']) && $list['pt_status'] != '') {
                                        $ptstatus = explode(', ', $list['pt_status']);
                                        foreach($ptstatus as $ptsrow) {
                                            $ptsstr.= $pt_status[$ptsrow]."<br />";
                                        }
                                        $ptsstr = substr($ptsstr,0,-2);
                                    }

                                    $ptacsstr='';
                                    if(isset($list['pt_account_status']) && $list['pt_account_status'] != '') {
                                        $ptaccstatus = explode(', ', $list['pt_account_status']);
                                        foreach($ptaccstatus as $ptacsrow) {
                                            $ptacsstr.= $pt_ac_status[$ptacsrow]."<br />";
                                        }
                                        $ptacsstr = substr($ptacsstr,0,-2);
                                    }

                                    $ptapptsstr='';
                                    //$appt_arr = array('location'=>'Location', 'physician'=>'Physician');
                                    if(isset($list['pt_appt_status']) && $list['pt_appt_status'] != '') {
                                        $ptapptstatus = explode('||', trim($list['pt_appt_status']));

                                        $providers=(!empty($ptapptstatus[0]))?trim($ptapptstatus[0]):'';
                                        if($providers!='') {
                                            $providers_arr = explode(',', $providers);
                                            $ptapptsstr.='<b>Providers</b> => ';
                                            foreach($providers_arr as $provider) {
                                                $provider=trim($provider);
                                                $ptapptsstr.= $users_arr[$provider].";";
                                            }
                                        }
                                        //$facilities=$ptapptstatus[1];
                                        $facilities=(!empty($ptapptstatus[1]))?trim($ptapptstatus[1]):'';
                                        if($facilities) {
                                            $facilities_arr = explode(',', $facilities);
                                            $ptapptsstr.='<br /><b>Facilities</b> => ';
                                            foreach($facilities_arr as $facility) {
                                                $facility=trim($facility);
                                                $ptapptsstr.= $facility_arr[$facility].";";
                                            }
                                        }
                                    }

                                    $apptProcRpstr='';
                                    //appt_procedure 	appt_ref_phy
                                    if(isset($list['ss_id']) && $list['ss_id'] != 0 && isset($list['ss_type']) && trim($list['ss_type'])=='schedule_status') {
                                        $rule_name_str=$ss_rule_name[$list['ss_id']];
                                    }
                                    if(isset($list['appt_procedure']) && $list['appt_procedure'] != '') {
                                        $procedure_arr = explode(',', $list['appt_procedure']);
                                        $apptProcRpstr.='<b>Procedures</b> => ';
                                        foreach($procedure_arr as $procedure) {
                                            $procedure=trim($procedure);
                                            $apptProcRpstr.= $arr_proc_names[$procedure]."<br />";
                                        }
                                    }
                                    if(isset($list['appt_ref_phy']) && trim($list['appt_ref_phy'])!='') {
                                        $appt_ref_phy_arr = explode(',', $list['appt_ref_phy']);
                                        $apptProcRpstr.='<br /><b>Ref. Physician</b> => ';
                                        foreach($appt_ref_phy_arr as $ref_phy) {
                                            $ref_phy=trim($ref_phy);
                                            $apptProcRpstr.= $ref_phy_arr[$ref_phy]."<br />";
                                        }
                                    }

                                    $ptar_agingstr='';
                                    $ar_aging_arr = array('30-60'=>'30-60', '61-90'=>'61-90', '91-120'=>'91-120', '121-180'=>'121-180', '181'=>'180+');
                                    if(isset($list['ar_aging']) && $list['ar_aging'] != '') {
                                        $ptar_aging = explode('#', $list['ar_aging']);
                                        //foreach($ptar_aging as $ptar_aging_row) {
                                        $price_str='';
                                        if(isset($ptar_aging[1]) && $ptar_aging[1]!='')$price_str="<br /><b>Amount</b> => ".(($ptar_aging[1]=='any')?'':'$').$ptar_aging[1];
                                        $ptar_agingstr.= "<b>Days</b> => ".$ar_aging_arr[$ptar_aging[0]].$price_str."<br />";
                                        //}
                                        $ptar_agingstr = substr($ptar_agingstr,0,-2);
                                    }

                                    $trans_arr=array("discount"=>"Discount","denial"=>"Denied","deductible"=>"Deductible","write_off"=>"Write Off");
                                    $transaction_str='';
                                    if(isset($list['transaction']) && $list['transaction'] != '') {
                                        $transaction_arr = explode(', ', $list['transaction']);
                                        foreach($transaction_arr as $trs_row) {
                                            $transaction_str.= $trans_arr[$trs_row]."<br />";
                                        }
                                        $transaction_str = substr($transaction_str,0,-2);
                                    }

                                    $obj_tooltiptext='';
                                    if(strlen($list['reason_code']) > 25) {
                                        $obj_tooltiptext.=' <b>Reason Codes</b> => ';
                                        $obj_tooltiptext.= $list['reason_code'];
                                        $list['reason_code'] = substr($list['reason_code'], 0, 25).'...';
                                    }
                                    if(isset($list['tm_cpt_code']) && $list['tm_cpt_code'] != '') {
                                        $tm_cpt_code_arr = explode(',', $list['tm_cpt_code']);
                                        $obj_tooltiptext.=' <br /><b>CPT Code</b> => ';
                                        foreach($tm_cpt_code_arr as $tm_cpt_code) {
                                            $tm_cpt_code=trim($tm_cpt_code);
                                            $obj_tooltiptext.= $cpt_fee_arr[$tm_cpt_code]."; ";
                                        }
                                    }

                                    $temp_grp_str='';
                                    if(isset($list['tm_group']) && $list['tm_group'] != '') {
                                        $tm_group_arr = explode(',', $list['tm_group']);
                                        $temp_grp_str.='<br /><b>Group</b> => ';
                                        foreach($tm_group_arr as $tm_group) {
                                            $tm_group=trim($tm_group);
                                            $temp_grp_str.= $group_id_arr[$tm_group]."; ";
                                        }
                                    }
                                    if(isset($list['ar_facility']) && $list['ar_facility'] != '') {
                                        $ar_facility_arr = explode(',', $list['ar_facility']);
                                        $temp_grp_str.='<br /><b>Facility</b> => ';
                                        foreach($ar_facility_arr as $ar_facility) {
                                            $ar_facility=trim($ar_facility);
                                            $temp_grp_str.= ($pos_facilities_arr[$ar_facility])?$pos_facilities_arr[$ar_facility]."; ":'';
                                        }
                                    }
                                    if(isset($list['tm_ins_group']) && $list['tm_ins_group'] != '') {
                                        $tm_ins_group_arr = explode(',', $list['tm_ins_group']);
                                        $temp_grp_str.='<br /><b>Insurance Group</b> => ';
                                        foreach($tm_ins_group_arr as $tm_ins_group) {
                                            $tm_ins_group=trim($tm_ins_group);
                                            $temp_grp_str.= ($org_group_arr[$tm_ins_group])?$org_group_arr[$tm_ins_group]."; ":'';
                                        }
                                    }
                                    if(isset($list['tm_ins_comp']) && $list['tm_ins_comp'] != '') {
                                        $tm_ins_comp_arr = explode(',', $list['tm_ins_comp']);
                                        $temp_grp_str.='<br /><b>Insurance Comp.</b> => ';
                                        foreach($tm_ins_comp_arr as $tm_ins_comp) {
                                            $tm_ins_comp=trim($tm_ins_comp);
                                            $temp_grp_str.= ($ins_comp_arr[$tm_ins_comp])?$ins_comp_arr[$tm_ins_comp]."; ":'';
                                        }
                                    }

                                    if(isset($list['tm_cpt_code']) && $list['tm_cpt_code'] != '') {
                                        $obj_tooltiptext.=$temp_grp_str;
                                    } else if(isset($list['ar_aging']) && $list['ar_aging'] != '') {
                                        $ptar_agingstr.=$temp_grp_str;
                                    }

                                    ?>
                                    <tr>
                                        <td width="20px" class="text-center" style="padding-left:14px;"><div class="checkbox checkbox-inline"><input type="checkbox" name="id" id="<?php echo $list['id'];?>" class="chk_sel" value="<?php echo $list['id'];?>"><label for="<?php echo $list['id'];?>"></label></div></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $categories[$list['cat_id']];?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $rule_name_str;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');" data-placement="right" data-html="true" data-toggle="tooltip" data-container="body" data-original-title="<?php echo $obj_tooltiptext;?>"><?php echo $list['reason_code'];?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $ptsstr;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $ptacsstr;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $ptapptsstr;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $apptProcRpstr;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $ptar_agingstr;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $transaction_str;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $user_group;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $user_name;?></td>
                                        <td onclick="load_rule_edit('<?php echo $list['id'];?>');"><?php echo $list['comment'];?></td>
                                    </tr>
                            <?php } ?>
                            <?php } else { ?>
                                <tr class="text-center"><td colspan="13">No record found.</td></tr>
                            <?php } ?>
                        </tbody>	
                    </table>

                </div>
                
            </div>


            <div class="clearfix"></div>
            <?php if(!empty($rule_list)) { ?>
            <div class="row">
                <div class="text-center col-sm-6 pt10">
                    <div class="clearfix"></div>
                    <?php echo $objPaging->buildComponentR8($page); ?>
                </div>
            </div>
            <?php } ?>
        </div>
        
       
    </div>

    

    <script>
        var page_view='rule_list';
        
        $(document).ready(function () {
            set_header_title('Rules Listing');
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(window).load(function () {
            var ar_btn = ["create_rule", "Rule Manager", "top.fmain.create_rule();"];
            var ar = [ar_btn,["delete_rule", "Delete", "top.fmain.deleteRule();"]];

            top.btn_show("ADMN", ar);
            
            parent.show_loading_image('none');
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_task_rules.js"></script>
    
    <?php include_once('../admin_footer.php'); ?>
<?php

include_once('../admin_header.php');

$list_id='';
$rulerow=array();
if(isset($_REQUEST['list_id']) && $_REQUEST['list_id']!='') {
    $list_id=$_REQUEST['list_id'];
    
    $rule_sql = "SELECT * FROM tm_rules_list where rule_status=0 and id=$list_id";
    $rule_rs = imw_query($rule_sql);
    if ($rule_rs && imw_num_rows($rule_rs) == 1) {
        $rulerow = imw_fetch_assoc($rule_rs);
        $rulerow['type']='';
        if($rulerow['ss_id']>0 && $rulerow['cat_id']==2) {
            $rulerow['type']='schedule_status';
            $rulerow['tm_rcat_id']=$rulerow['cat_id'];
            $rulerow['ss_id']=$rulerow['ss_id'];
            $rulerow['rule_id']='ss'.$rulerow['ss_id'];
        }
    }
}
//pre($rulerow);

/* Get Task Manager Categories */
$categories = array();
$sql = 'SELECT * FROM tm_rule_category ';
$resp = imw_query($sql);
$category_option = "";
if ($resp && imw_num_rows($resp) > 0) {
    while ($row = imw_fetch_assoc($resp)) {
        $categories[$row['id']] = $row['tm_rule_category'];
        $sel = ($_REQUEST['id'] == $row['id']) ? 'selected' : '';
        $category_option .= "<option " . $sel . " value='" . $row['id'] . "'>" . $row['tm_rule_category'] . "</option>";
    }
}

/* Get Task Manager rules */
$rules = array();
$rule_sql = "SELECT * FROM tm_rules ";
$rule_rs = imw_query($rule_sql);
if ($rule_rs && imw_num_rows($rule_rs) > 0) {
    while ($rules_row = imw_fetch_assoc($rule_rs)) {
        $rules[] = $rules_row;
    }
}

/* Get Task Manager rules List */
$rule_list = array();
$rule_list_btn = false;
$rule_sql = "SELECT * FROM tm_rules_list where rule_status=0";
$rule_rs = imw_query($rule_sql);
if ($rule_rs && imw_num_rows($rule_rs) > 0) {
    $rule_list_btn = true;
    while ($rules_row = imw_fetch_assoc($rule_rs)) {
        $rule_list[] = $rules_row;
    }
}

/* Get Task Manager Categories and rules */
$tm_categories = array();
$categories = array();
$sql = 'SELECT * FROM `tm_rule_category` ';
$resp = imw_query($sql);
if ($resp && imw_num_rows($resp) > 0) {
    while ($row = imw_fetch_assoc($resp)) {
        $categories[$row['id']] = $row['tm_rule_category'];
        if ($row['id']) {
            $sql = "SELECT * FROM tm_rules where tm_rcat_id='" . $row['id'] . "' ";
            $tm_rules_rs = imw_query($sql);
            if ($tm_rules_rs && imw_num_rows($tm_rules_rs) > 0) {
                while ($tm_rules_row = imw_fetch_assoc($tm_rules_rs)) {
                    $row['rules'][] = $tm_rules_row;
                }
            }
            if($row['id']==2) {
                $ss_sql = "SELECT id,status_name as tm_rule_name FROM schedule_status WHERE id NOT IN('18','202','3','203') and status=1  order by status_name asc";
                //$ss_sql = "SELECT id,status_name as tm_rule_name FROM schedule_status order by status_name asc";
                $ss_rs = imw_query($ss_sql);
                if ($ss_rs && imw_num_rows($ss_rs) > 0) {
                    while ($ss_row = imw_fetch_assoc($ss_rs)) {
                        $ss_row['type']='schedule_status';
                        $ss_row['ss_id']=$ss_row['id'];
                        $ss_row['id']='ss'.$ss_row['id'];
                        $ss_row['tm_rcat_id']=$row['id'];
                        $row['rules'][] = $ss_row;
                    }
                }
                
            }
            $tm_categories[] = $row;
        }
    }
}
//pre($tm_categories);
/* Get Patient Status */
$pt_status_options = array();
$pt_sql = "SELECT * FROM patient_status_tbl ";
$pt_rs = imw_query($pt_sql);
if ($pt_rs && imw_num_rows($pt_rs) > 0) {
    while ($row = imw_fetch_assoc($pt_rs)) {
        $pt_status_options[$row['pt_status_id']] = $row['pt_status_name'];
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


/* Get User Groups */
$user_groups_str = '';
$sql = "select * from `user_groups` where `status` = '1' order by `name`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    $user_groups_str = '<select class="selectpicker" multiple name="user_group[]" id="user_group" data-width="100%" data-size="10" data-title="Select Group" data-actions-box="true" data-live-search="true">';
    while ($row = imw_fetch_assoc($sql_rs)) {
        $sel=(in_array($row['id'],explode(',',$rulerow['user_group'])))?' selected ':'';
        $user_groups_str .= '<option value="' . $row['id'] . '" "'.$sel.'">' . $row['name'] . '</option>';
    }
    $user_groups_str .= '</select>';
}

/* Get Users */
$users_str = '';
$prov_opt_str = '';
$sql = "select id,fname,mname,lname from `users` where `delete_status` = '0' order by `lname`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    $users_str = '<select class="selectpicker" multiple name="user_name[]" id="user_name" data-width="100%" data-size="10" data-title="Select User" data-actions-box="true" data-live-search="true">';
    while ($row = imw_fetch_assoc($sql_rs)) {
        $sel=(in_array($row['id'],explode(',',$rulerow['user_name'])))?' selected ':'';
        $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
        $users_str .= '<option value="' . $row['id'] . '"  "'.$sel.'">' . $prov_name . '</opiton>';
    }
    $users_str .= '</select>';
}



$ar_aging_arr = array('30-60'=>'30-60', '61-90'=>'61-90', '91-120'=>'91-120', '121-180'=>'121-180', '181'=>'180+');


$ptapptstatus = explode('||', $rulerow['pt_appt_status']);

/* Get Users */
$users_arr = array();
$sql = "select id,fname,mname,lname from `users` where `delete_status` = '0' order by `lname`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        $sel=(in_array($row['id'],explode(',', trim($ptapptstatus[0]))))?' selected ':'';
        $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
        $users_arr[$row['id']] = $prov_name;
    }
}

/* Get Users */
$facility_arr = array();
$qry = "select id, name from facility order by name";	
$sql_rs = imw_query($qry);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        $facility_arr[$row['id']] = $row['name'];
    }
}

/*
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
$ins_group_options = '';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

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
*/



?>

<body>
    <style>
        .rule_box .rule_li{padding:3px 5px;}
        .rule_box .rule_li.active{border:1px solid #d3d3d3;background-color: #f2eded;}
        .rule_box .rule_li.active .rule_options{line-height: 15px;}
    </style>
    <div class="container-fluid">
        <div class="task_rules" style="margin-top:2px;">
            <form name="assign_rule_frm" id="assign_rule_frm" onSubmit="assign_to();return false;">
                <input type="hidden" id="cat_id" name="cat_id" value=""/>
                <input type="hidden" id="list_id" name="list_id" value="<?php echo $list_id;?>"/>
                <div class="row">
                    <div class="col-sm-2 ">
                        <div class="rulesleft">
                            <ul class="nav nav-tabs" role="tablist">
                                <?php
                                $counter = 0;
                                foreach ($tm_categories as $cat_val) {
                                    $counter++;

                                    $class='';
                                    if($rulerow) {
                                        if($rulerow['cat_id'] == $cat_val['id']) {
                                            $class=' active ';
                                        }
                                    } else {
                                        if($counter == 1) {
                                            $class=' active ';
                                        }
                                    }
                                    ?>
                                    <li role="presentation" class="<?php echo $class; ?>">
                                        <a href="#cat_tab<?php echo $cat_val['id']; ?>" aria-controls="cat_tab<?php echo $cat_val['id']; ?>" role="tab" data-toggle="tab">
                                            <?php
                                            $icon_class = " glyphicon-list-alt ";
                                            switch(trim($cat_val['tm_rule_cat_alias'])) {
                                                case 'accounting':
                                                    $icon_class = " glyphicon-list-alt ";
                                                    break;
                                                case 'appointment':
                                                    $icon_class = " glyphicon-time ";
                                                    break;
                                                case 'ar_aging':
                                                    $icon_class = " glyphicon glyphicon-duplicate ";
                                                    break;
                                            }
                                            ?>
                                            <span class="glyphicon <?php echo $icon_class; ?>" aria-hidden="true"></span>
                                            <?php echo $cat_val['tm_rule_category']; ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>

                    </div>
                    <div class="col-sm-6 ">

                        <div class="tab-content" style="min-height:300px;">
                            <?php foreach ($tm_categories as $cat_val) { ?>
                                <div role="tabpanel" class="tab-pane rule_box" id="cat_tab<?php echo $cat_val['id']; ?>">
                                    <ul>
                                        <?php foreach ($cat_val['rules'] as $rule) {
                                            $checked='';
                                            if($rulerow['rule_id'] == $rule['id']) {
                                                $checked='checked';
                                            }
                                            
                                            $col1=' col-sm-8 ';
                                            $col2=' col-sm-4 ';
                                            $col3=' ';
                                            if(($rule['id'] == '2' && $rule['tm_rcat_id']=='1') || $rule['tm_rcat_id']=='2' || $rule['tm_rcat_id']=='3') {
                                                $col1=' col-sm-4 ';
                                                $col2=' col-sm-8 ';
                                                $col3=' ';
                                            }
//                                            if($rule['tm_rcat_id']=='3') {
//                                                $col1=' col-sm-5 ';
//                                                $col2=' col-sm-4 ';
//                                                $col3=' col-sm-3 ';
//                                            }
                                            
                                            ?>
                                            <li class="rule_li">
                                                <div class="row">
                                                    <div class="<?php echo $col1;?>">
                                                        <div class="checkbox">
                                                            <?php /*if(isset($rule['ss_id']) && $rule['ss_id']>0 && isset($rule['type']) && $rule['type']!='') {?>
                                                                <input type="hidden" name="ss_type_<?php echo $rule['id']; ?>" id="ss_type" value="<?php echo $rule['type']; ?>" />
                                                                <input type="hidden" name="ss_id_<?php echo $rule['id']; ?>" id="ss_id" value="<?php echo $rule['ss_id']; ?>" />
                                                            <?php }*/ ?>
                                                            <input type="checkbox" value="1" <?php echo $checked; ?> name="rulename_<?php echo $rule['id']; ?>[]" id="rule_<?php echo $rule['id']; ?>" onclick="showOptionsforRule(this, '<?php echo $cat_val['id']; ?>', '<?php echo $rule['id']; ?>', '<?php echo $rule['type']; ?>');" />
                                                            <label for="rule_<?php echo $rule['id']; ?>">
                                                                <?php echo trim($rule['tm_rule_name']); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="<?php echo $col2;?>">
                                                        <div class="clearfix"></div>
                                                        <div class="ruleoption_div_<?php echo $rule['id']; ?> rule_options"></div>
                                                    </div>
                                                    <?php /* if($rule['tm_rcat_id']=='3') {
                                                        
                                                        $ptar_aging = explode('#', $rulerow['ar_aging']);
                                                        $ptar_aging_value='';
                                                        if($rulerow['rule_id'] == $rule['id']) {
                                                            $ptar_aging_value = (isset($ptar_aging[1]) && $ptar_aging[1]!='')?$ptar_aging[1]:' any ';
                                                        }
                                                        ?>
                                                        <div class="<?php echo $col3;?>">
                                                            <div class="clearfix"></div>
                                                            <div class="form-inline ar_aging_val_div ar_aging_div_<?php echo $rule['id']; ?>" style="display:none;">
                                                                <label for="ar_aging_value">$</label>
                                                                <input type="text" name="ar_aging_value<?php echo $rule['id']; ?>" id="ar_aging_value<?php echo $rule['id']; ?>" value="<?php echo $ptar_aging_value;?>" class="form-control"/>
                                                            </div>
                                                        </div>
                                                    <?php } */ ?>
                                                    </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="clearfix"></div>
                        <div class="">
                            <div id="edit_rule_btn_div" >
                                <input type="hidden" value="" id="rule_id" name="rule_id" />
<!--                                <button type="button" class="btn btn-success" id="edit_rule_btn" onclick="editRuleCheck();" >Edit</button>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 ">
                        <div class="applyrules">
                            <h2>Apply This rule after the message arrives</h2>
                            <!--                        <div class="pd15">From people or <a href="">public group</a></div>-->
                            <div class="pd15 form-inline">
                                <div class="clearfix"></div>
                                <div class="col-sm-3">
                                    Assigned To
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $user_groups_str; ?>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo $users_str; ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="pd15">
                                <label>Comment</label>
                                <textarea class="form-control" name="comment" id="comment" rows="5"><?php echo ((isset($rulerow['comment']) && $rulerow['comment']!='') ?$rulerow['comment']:'');?></textarea>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="clearfix"></div>
            </form>
        </div>




        <!-- Rule Modal Box -->
        <div class="common_modal_wrapper">
            <div id="addNew_div" class="modal">
                <div id="selectContainer" style="position: absolute;"></div>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h4 class="modal-title" id="modal_title">Edit Record</h4>
                        </div>
                        <div class="modal-body" >
                            <div class="form-group">
                                <form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
                                    <input type="hidden" name="id" id="id" value="">
                                    <div class="row">
                                        <!-- Facility Col. -->
                                        <div class="col-sm-12" id="fac_div">
                                            <div class="adminbox">
                                                <div class="head">
                                                    <span>Rules</span>
                                                </div>
                                                <div class="tblBg" id="refer_div">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="tm_rcat_id">Rule Category</label>
                                                                <select name="tm_rcat_id" id="tm_rcat_id"  class="selectpicker" data-width="100%" data-title="Select">
                                                                    <option value="">Select</option>
                                                                    <?php echo $category_option; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="tm_rule_name">Rule</label>
                                                                <input type="text" name="tm_rule_name" id="tm_rule_name" value="" class="form-control">
                                                            </div>	
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        var page_view='rule_manager';
        
        <?php //if($rule_list_btn) { ?>
            var ar_btn = ["rule_list", "Rules Listing", "top.fmain.show_rule_list();"];
        <?php //} ?>
    </script>
    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_task_rules.js"></script>
    
    <script>
        <?php if((isset($rulerow['pt_status']) && $rulerow['pt_status'] != '') ||
                (isset($rulerow['reason_code']) && $rulerow['reason_code'] != '') || 
                (isset($rulerow['pt_account_status']) && $rulerow['pt_account_status'] != '') ||
                (isset($rulerow['pt_appt_status']) && $rulerow['pt_appt_status'] != '') ||
                (isset($rulerow['appt_procedure']) && $rulerow['appt_procedure'] != '') ||
                (isset($rulerow['ar_aging']) && $rulerow['ar_aging'] != '') ||
                (isset($rulerow['ss_id']) && $rulerow['ss_id']>0) ||
                (isset($rulerow['transaction']) && $rulerow['transaction'] != '')
                ) { ?>
            showOptionsforRule('#rule_<?php echo $rulerow['rule_id'];?>', '<?php echo $rulerow['cat_id'];?>', '<?php echo $rulerow['rule_id'];?>', '<?php echo $rulerow['type']; ?>','<?php echo $list_id;?>');
        <?php } ?>
    </script>

    <?php include_once('../admin_footer.php'); ?>
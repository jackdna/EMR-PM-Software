<?php
include_once('../../../../config/globals.php');
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/lab.class.php");

/** 
 * Parameters Sanitization to prevent arbitrary values - Security Fixes
 **/
$_REQUEST['lab_test_id'] = (int)xss_rem($_REQUEST['lab_test_id'], 3);


$medical = new MedicalHistory($_REQUEST['showpage']);
$lab_obj = new Lab($medical->current_tab);

$collection_types = array();
$default_urgency = array();
$schedules = array();
$samples = array();
$specimens = array();
if(isDssEnable()) {
	try {
		include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
		$obj = new Dss_medical_hx();
		$results = $obj->LabGetAllSamples();

		$arr = array();
		foreach ($results as $key => $n) {
		    if($n['field'] == 'CollSamp' || $n['field'] == 'Specimens'){
		      $arr[$n['field']] = $n['values'];  
		    } 
		}

		foreach ($arr as $keyParent => $x) {
		    $options = explode(':', $x);
		    foreach ($options as $key => $opt) {
		        $opts = explode(';', $opt);
		        if($keyParent == 'CollSamp') {
		            $samples[] = array('id'=>$opts[0],'name'=>$opts[1]);
		        } elseif ($keyParent == 'Specimens') {
		            $specimens[] = array('id'=>$opts[0],'name'=>$opts[1]);
		        }
		    }
		}
        
        $dss_location='';
        if(isset($_SESSION['dss_location']) && $_SESSION['dss_location']!='')
            $dss_location=$_SESSION['dss_location'];
        
        $params=array();
        $params['location']=$dss_location;
        $results = $obj->LabGetDialogDefaults($params);
        
        $dialogArr = array();
		foreach ($results as $key => $n) {
		    if($n['field'] == 'Collection Types' || $n['field'] == 'Default Urgency' || $n['field'] == 'Schedules'){
		      $dialogArr[$n['field']] = $n['values'];  
		    } 
		}
        
        
		foreach ($dialogArr as $keyParent => $x) {
		    $options = explode(':', $x);
		    foreach ($options as $key => $opt) {
		        $opts = explode(';', $opt);
		        if($keyParent == 'Collection Types') {
		            $collection_types[] = array('id'=>$opts[0],'name'=>$opts[1]);
		        } elseif ($keyParent == 'Default Urgency') {
		            $default_urgency[] = array('id'=>$opts[0],'name'=>$opts[1]);
		        } elseif ($keyParent == 'Schedules') {
		            $schedules[] = array('id'=>$opts[0],'name'=>$opts[1]);
		        }
		    }
		}
        
        
	} catch(Exception $e) {
		echo $e->getMessage();
	}
}

// --- Get Typeahead array
if(isset($_REQUEST['get_typeahead_arr']) && trim($_REQUEST['get_typeahead_arr']) == 'yes'){
	$return_array = array();
	$provider_arr = $lab_obj->get_providers_typeahead_arr();
	$loinc_arr = $lab_obj->get_loinc_arr();
	$abnormal_flag_arr = $lab_obj->get_abnormal_flag();
	
	$replace_str_arr = array('\'','"');
	$blank = array('','');
	//$provider_arr = explode('~~',str_replace($replace_str_arr,$blank,$provider_arr));
	$str_loinc_arr = explode(',',str_replace($replace_str_arr,$blank,$loinc_arr['string_arr']));
	
	$return_array['provider_arr'] = $provider_arr;
	$return_array['loinc_arr'] = $str_loinc_arr;
	$return_array['loinc_val_arr'] = $loinc_arr['val_arr'];
	$return_array['get_abnormal_flag'] = $abnormal_flag_arr;
	
	if(isDssEnable()) {
		$return_array['samples'] = $samples;
		$return_array['specimens'] = $specimens;
	}

	echo json_encode($return_array);
	exit();
}


// --- Delete lab record
if(isset($_REQUEST['del_lab_id']) && $_REQUEST['del_lab_id'] > 0){
	$del_status = $lab_obj->del_lab_record($_REQUEST['del_lab_id'], $_REQUEST['labOrder']);
	echo $del_status;
	exit();
}

// --- Delete test records
if(isset($_REQUEST['lab_test_id']) && $_REQUEST['lab_test_id'] > 0 && isset($_REQUEST['action'])){
	//Obser Request
	if(isset($_REQUEST['del_request_id']) && $_REQUEST['del_request_id'] > 0 && $_REQUEST['action'] == 'request'){
		$del_request_rec = $lab_obj->del_obser_rec($_REQUEST['del_request_id'],$_REQUEST['action']);
	}
	
	//Obser Specimen
	if(isset($_REQUEST['del_specimen_id']) && $_REQUEST['del_specimen_id'] > 0 && $_REQUEST['action'] == 'specimen'){
		$del_request_rec = $lab_obj->del_obser_rec($_REQUEST['del_specimen_id'],$_REQUEST['action']);
	}
	
	//Obser Sample
	if(isDssEnable() && isset($_REQUEST['del_sample_id']) && $_REQUEST['del_sample_id'] > 0 && $_REQUEST['action'] == 'sample'){
		$del_request_rec = $lab_obj->del_obser_rec($_REQUEST['del_sample_id'],$_REQUEST['action']);
	}
	
	//Obser Result
	if(isset($_REQUEST['del_result_id']) && $_REQUEST['del_result_id'] > 0 && $_REQUEST['action'] == 'result'){
		$del_request_rec = $lab_obj->del_obser_rec($_REQUEST['del_result_id'],$_REQUEST['action']);
	}
	
	echo $del_request_rec;
	exit();
}



// --- Get modal box data 
if(isset($_REQUEST['lab_test_id']) && $_REQUEST['get_modal'] == 'yes'){
	$lab_test_id = $_REQUEST['lab_test_id'];
	$modal_data= $lab_obj->get_modal_box_data($lab_test_id);
	?>
<style>
	.purple_bar{padding:3px;}
	.table{margin-bottom:0px}
	.modal-body{padding-bottom:0px}
	.row_add_btn{max-width: 30px;padding: 3px;}
</style>	
	<!-- New Lab Order Modal Box -->
		<div class="commom_wrapper">
			<div id="myModal" class="modal fade in" role="dialog">
				<div class="modal-dialog modal-lg">
					<!-- Modal content-->
					<div class="modal-content">
						<div id="selectpicker_cont" style="position:absolute;bottom:0px;width:100%"></div>
						<form name="frm_sub" action="index.php?showpage=lab&form_action=save_new_order" method="post" autocomplete="off">
						<input type="hidden" name="today_date" id="today_date" value="<?php echo get_date_format(date('Y-m-d')); ?>">
						<input type="hidden" name="lab_test_id" id="lab_test_id" value="<?php echo $lab_test_id; ?>">
							<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Lab Order</h4>
							</div>
							<div class="modal-body">
								<div class="row" style="height:<?php echo $_SESSION['wn_height']-450;?>px;overflow-y:auto;overflow-x:hidden;">
									<!-- Header Row -->
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label>Order# : <?php echo $modal_data['order_date_time']['order_no']; ?></label>	
                                            </div>

                                            <div class="col-sm-3 form-inline">
                                                <label style="width:auto">Order By:&nbsp;</label>
												<input type="text" name="order_by" id="order_by" value="<?php echo $modal_data['order_date_time']['provider1_name']; ?>" class="form-control" style="width:70%">	
												<input type="hidden" name="order_by_prov_id" value="<?php echo $modal_data['order_date_time']['order_provider_id'] ?>">
                                            </div>

                                            <div class="col-sm-7">
                                                <div class="row form-inline">

                                                    <label>Order Date/Time:&nbsp;</label>	
                                                    <div class="input-group">
                                                        <input type="text" name="order_date" id="order_date" class="datepicker form-control" onBlur="top.fmain.checkdate(this)" onClick="getDate_and_setToField('order_date', 'order_time')" value="<?php
                                                        if ($modal_data['order_date_time']['lab_order_date'] != "0000-00-00" && $modal_data['order_date_time']['lab_order_date'] != "") {
                                                            echo $modal_data['order_date_time']['lab_order_date'];
                                                        }
                                                        ?>"/>
                                                        <label for="order_date" class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </label>	
                                                    </div>

                                                    <div class="input-group mlr10">
                                                        <input type="text" name="order_time" id="order_time" class="form-control" value="<?php echo $modal_data['order_date_time']['lab_order_time']; ?>"/>
                                                        <label for="order_time" class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </label>	
                                                    </div>

                                                    <div class="input-group">
                                                        <input type="text" name="order_time_zone" id="order_time_zone" class="form-control" value="<?php echo $modal_data['order_date_time']['order_time_zone']; ?>"/>
                                                        <label for="order_time_zone" class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </label>	
                                                    </div>	

                                                </div>	
                                            </div>	
                                        </div>	
                                    </div>
									
                                    <?php if(isDssEnable()){ ?>
                                        <div class="col-sm-12 pt10">
                                            <div class="row">
                                                <div class="col-sm-4 form-inline">
                                                    <label>Collection Types </label>
                                                    <select name="dss_collection_type" id="dss_collection_type" class="form-control minimal" style="width:70%">
<!--                                                        <option value="">Select</option>-->
                                                        <?php foreach($collection_types as$ctkey=>$type) {
                                                            $selected='';
                                                            if($type['id']==$modal_data['order_date_time']['dss_collection_type'])
                                                            $selected=' selected="selected" ';
                                                            ?>
                                                            <option value="<?php echo $type['id'];?>" <?php echo $selected;?> ><?php echo $type['name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4 form-inline">
                                                    <label>Urgency </label>
                                                    <select name="dss_urgency" id="dss_urgency" class="form-control minimal" style="width:70%">
<!--                                                        <option value="">Select</option>-->
                                                        <?php foreach($default_urgency as$ukey=>$urgency) {
                                                            $selected='';
                                                            if($urgency['id']==$modal_data['order_date_time']['dss_urgency'])
                                                            $selected=' selected="selected" ';
                                                            ?>
                                                            <option value="<?php echo $urgency['id'];?>" <?php echo $selected;?> ><?php echo $urgency['name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4 form-inline">
                                                    <label>Schedules </label>
                                                    <select name="dss_schedules" id="dss_schedules" class="form-control minimal" style="width:70%">
<!--                                                        <option value="">Select</option>-->
                                                        <?php foreach($schedules as$skey=>$schedule) {
                                                            $selected='';
                                                            if($schedule['id']==$modal_data['order_date_time']['dss_schedules'])
                                                            $selected=' selected="selected" ';
                                                            ?>
                                                            <option value="<?php echo $schedule['id'];?>" <?php echo $selected;?> ><?php echo $schedule['name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    
									<!-- Lab Observation Requested Block -->
									<div class="col-sm-12 pt10">
										<div class="row">
											
											<div class="purple_bar">
												<div class="row">
													<div class="col-sm-10">
														<label>Observation Requested</label>
													</div>	
													<div class="col-sm-2 text-right">
                                                        <?php if(!isDssEnable()){ ?>
														<span class="glyphicon glyphicon-plus row_add_btn pointer" onClick="top.fmain.ob_req_addrow();"></span>	
                                                        <?php } ?>
													</div>	
												</div>
											</div>
											<div class="col-sm-12">
												<div id="obser_req" class="row" style="height:90px;overflow-x:auto;">
													<table class="table table-condensed table-bordered table-striped ob_req_countrow">
														<tr class="grythead" id="tr_c_0">
															<th>Service</th>
															<th>LOINC</th>
															<th class="text-nowrap">Observation Date/Time (Start)</th>
															<th class="text-nowrap">Observation Date/Time (End)</th>
															<th>Clinical Info</th>
															<th style="width:4%" ></th>
														</tr> 	
														<?php
														$pro_cont=1;
														foreach($modal_data['lab_obser_req'] as $obj){	?>
															<tr id="tr_c_<?php echo $pro_cont; ?>">
																<td>
																	<input 	type="hidden" name="requested_id_<?php echo $pro_cont; ?>" value="<?php echo $obj['id']; ?>">
																	<input 	type="text" name="service_<?php echo $pro_cont; ?>" id="service_<?php echo $pro_cont; ?>" value="<?php echo $obj['service']; ?>" onChange="fet_loinc_code('request','<?php echo $pro_cont; ?>');" class="form-control">
																</td>
																<td>
																	<input 	type="text" name="loinc_<?php echo $pro_cont; ?>" id="loinc_<?php echo $pro_cont; ?>" value="<?php echo $obj['loinc']; ?>" class="form-control">
																</td>
																<td>
																	<div class="row">
																		<div class="col-sm-7">
																			<div class="input-group">
																				<input type="text" name="start_date_<?php echo $pro_cont; ?>" id="start_date_<?php echo $pro_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('start_date_<?php echo $pro_cont; ?>', 'start_time_<?php echo $pro_cont; ?>')" value="<?php echo get_date_format($obj['start_date']); ?>"/>
																				<label for="start_date_<?php echo $pro_cont; ?>" class="input-group-addon">
																					<span class="glyphicon glyphicon-calendar"></span>	
																				</label>	
																			</div>	
																		</div>
																			
																		<div class="col-sm-5">
																			<div class="input-group">
																				<input type="text" name="start_time_<?php echo $pro_cont; ?>" id="start_time_<?php echo $pro_cont; ?>" class="form-control" onClick="getDate_and_setToField('start_date_<?php echo $pro_cont; ?>', 'start_time_<?php echo $pro_cont; ?>')" value="<?php echo $obj['start_time']; ?>"/>
																				<label for="start_time_<?php echo $pro_cont; ?>" class="input-group-addon">
																					<span class="glyphicon glyphicon-time"></span>	
																				</label>	
																			</div>	
																		</div>	
																	</div>
																</td>	
																<td>
																	<div class="row">
																		<div class="col-sm-7">
																			<div class="input-group">
																				<input type="text" name="end_date_<?php echo $pro_cont; ?>" id="end_date_<?php echo $pro_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('end_date_<?php echo $pro_cont; ?>', 'end_time_<?php echo $pro_cont; ?>')" value="<?php echo get_date_format($obj['end_date']); ?>"/>
																				<label for="end_date_<?php echo $pro_cont; ?>" class="input-group-addon">
																					<span class="glyphicon glyphicon-calendar"></span>	
																				</label>	
																			</div>	
																		</div>
																		<div class="col-sm-5">
																			<div class="input-group">
																				<input type="text" name="end_time_<?php echo $pro_cont; ?>" id="end_time_<?php echo $pro_cont; ?>" class="form-control" onClick="getDate_and_setToField('end_date_<?php echo $pro_cont; ?>', 'end_time_<?php echo $pro_cont; ?>')" value="<?php echo $obj['end_time']; ?>"/>
																				<label for="end_time_<?php echo $pro_cont; ?>" class="input-group-addon">
																					<span class="glyphicon glyphicon-time"></span>	
																				</label>	
																			</div>	
																		</div>	
																	</div>
																</td>            
																<td>
																	<input type="text" class="form-control" name="clinical_info_<?php echo $pro_cont; ?>" value="<?php echo $obj['clinical_info']; ?>">
																</td>
																<td class="text-center">
                                                                    <?php if(!isDssEnable()){ ?>
																	<span class="glyphicon glyphicon-remove pointer" id="removebtn" onClick="javascript:top.fancyConfirm('Are you sure you want to delete this record?','Delete Record','top.fmain.del_test(\'<?php echo $obj['id']; ?>\',\'request\',\'<?php echo $lab_test_id; ?>\')');"></span>
                                                                    <?php } ?>
																</td>
															</tr>
														<?php $pro_cont++;
														} ?>
                                                        <?php if(!isDssEnable() || count($modal_data['lab_obser_req'])==0){ ?>
														<script>ob_req_addrow();</script>
                                                        <?php } ?>
													</table>
												</div>	
											</div>		
										</div>
										<input type="hidden" name="request_cont" id="request_cont" value="<?php echo $pro_cont; ?>">
									</div>	
									
                                    <?php if(isDssEnable()){ ?>
                                    <!-- Lab Sample Block -->
									<div class="col-sm-12 pt10">
										<div class="row">
											<div class="purple_bar">
												<div class="row">
													<div class="col-sm-10">
														<label>Sample</label>		
													</div>	
													<div class="col-sm-2 text-right">
                                                        <?php if(!isDssEnable()){ ?>
														<span class="glyphicon glyphicon-plus row_add_btn pointer" onClick="top.fmain.smp_addrow();"></span>
                                                        <?php } ?>
													</div>	
												</div>
											</div>
											
											<div class="col-sm-12">
												<div id="obser_sample_bl" class="row"  style="height:90px;overflow-x:auto;">
													<input type="hidden" name="sample_cont" id="sample_cont" value="1">
													<table class="table table-condensed table-striped table-bordered smp_countrow">
														<tr class="grythead" id="tr_s_0">
															<th>Type</th>
															<th class="text-nowrap">Collection Date/Time (Start)</th>
															<th class="text-nowrap">Collection Date/Time (End)</th>
															<th>Condition</th>
															<th>Rejection(Y/N)</th>
															<th>Comments</th>
															<th style="width:4%" class="text-center"></th>
														</tr>
														<?php
															$smp_cont=1;
															foreach($modal_data['lab_sample'] as $obj){	?>
																<tr id="tr_s_<?php echo $smp_cont; ?>">
																	<td>
																		<input 	type="hidden" name="sample_id_<?php echo $smp_cont; ?>" value="<?php echo $obj['id']; ?>">
																		<input 	type="text" class="form-control" name="smp_collection_type_<?php echo $smp_cont; ?>" id="smp_collection_type_<?php echo $smp_cont; ?>" value="<?php echo $obj['smp_collection_type']; ?>">

																		<input 	type="hidden" class="form-control" name="hidden_smp_collection_type_<?php echo $smp_cont; ?>" id="hidden_smp_collection_type_<?php echo $smp_cont; ?>" value="">
																	</td>
																	<td>
																		<div class="row">
																			<div class="col-sm-7">
																				<div class="input-group">
																					<input type="text" name="sample_start_date_<?php echo $smp_cont; ?>" id="sample_start_date_<?php echo $smp_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('sample_start_date_<?php echo $smp_cont; ?>', 'sample_start_date_<?php echo $smp_cont; ?>')" value="<?php echo get_date_format($obj['sample_start_date']); ?>"/>
																					<label for="sample_start_date_<?php echo $smp_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>
																					</label>
																				</div>		
																			</div>
																			<div class="col-sm-5">
																				<div class="input-group">
																					<input type="text" name="sample_start_time_<?php echo $smp_cont; ?>" id="sample_start_time_<?php echo $smp_cont; ?>" class="form-control" onClick="getDate_and_setToField('sample_start_date_<?php echo $smp_cont; ?>', 'sample_start_time_<?php echo $smp_cont; ?>')" value="<?php echo $obj['sample_start_time']; ?>"/>
																					<label for="sample_start_time_<?php echo $smp_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-time"></span>
																					</label>
																				</div>
																			</div>
																		</div>
																	</td>	
																	<td>
																		<div class="row">
																			<div class="col-sm-7">
																				<div class="input-group">
																					<input type="text" name="sample_end_date_<?php echo $smp_cont; ?>" id="sample_end_date_<?php echo $smp_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('sample_end_date_<?php echo $smp_cont; ?>', 'sample_end_date_<?php echo $smp_cont; ?>')" value="<?php echo get_date_format($obj['sample_end_date']); ?>"/>
																					<label for="sample_end_date_<?php echo $smp_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>
																					</label>
																				</div>		
																			</div>
																			<div class="col-sm-5">
																				<div class="input-group">
																					<input type="text" name="sample_end_time_<?php echo $smp_cont; ?>" id="sample_end_time_<?php echo $smp_cont; ?>" class="form-control" onClick="getDate_and_setToField('sample_end_date_<?php echo $smp_cont; ?>', 'sample_end_time_<?php echo $smp_cont; ?>')" value="<?php echo $obj['sample_end_time']; ?>"/>
																					<label for="sample_end_time_<?php echo $smp_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-time"></span>
																					</label>
																				</div>
																			</div>	
																		</div>
																	</td>            
																	<td>
																		<input type="text" class="form-control" name="sample_condition_<?php echo $smp_cont; ?>" value="<?php echo $obj['sample_condition']; ?>">
																	</td>
																	<td>
																		<input type="text" class="form-control" name="sample_rejection_<?php echo $smp_cont; ?>" value="<?php echo $obj['sample_rejection']; ?>">
																	</td>
																	<td>
																		<input type="text" class="form-control" name="sample_comments_<?php echo $smp_cont; ?>" value="<?php echo $obj['sample_comments']; ?>">
																	</td>
																	<td class="text-center">
                                                                        <?php if(!isDssEnable()){ ?>
																		<span class="pointer glyphicon glyphicon-remove" id="smp_removebtn" onClick="javascript:top.fancyConfirm('Are you sure you want to delete this record?','Delete Record','top.fmain.del_test(\'<?php echo $obj['id']; ?>\',\'sample\',\'<?php echo $lab_test_id; ?>\')');"></span>
                                                                        <?php } ?>
																	</td>
																</tr>
														<?php $smp_cont++; } ?>
                                                                <?php if(!isDssEnable() || count($modal_data['lab_sample'])==0){ ?>
														<script>smp_addrow();</script>
                                                        <?php } ?>
													</table>	
												</div>	
											</div>
										</div>	
									</div>
                                    <?php } ?>
                                    
                                    
									<!-- Lab specimen Block -->
									<div class="col-sm-12 pt10">
										<div class="row">
											<div class="purple_bar">
												<div class="row">
													<div class="col-sm-10">
														<label>Specimen</label>		
													</div>	
													<div class="col-sm-2 text-right">
                                                        <?php if(!isDssEnable()){ ?>
														<span class="glyphicon glyphicon-plus row_add_btn pointer" onClick="top.fmain.spm_addrow();"></span>
                                                        <?php } ?>
													</div>	
												</div>
											</div>
											
											<div class="col-sm-12">
												<div id="obser_specimen_bl" class="row"  style="height:90px;overflow-x:auto;">
													<input type="hidden" name="specimen_cont" id="specimen_cont" value="1">
													<table class="table table-condensed table-striped table-bordered spm_countrow">
														<tr class="grythead" id="tr_b_0">
															<th>Type</th>
															<th class="text-nowrap">Collection Date/Time (Start)</th>
															<th class="text-nowrap">Collection Date/Time (End)</th>
															<th>Condition</th>
															<th>Rejection(Y/N)</th>
															<th>Comments</th>
															<th style="width:4%" class="text-center"></th>
														</tr>
														<?php
															$pro_cont=1;
															foreach($modal_data['lab_specimen'] as $obj){	?>
																<tr id="tr_b_<?php echo $pro_cont; ?>">
																	<td>
																		<input 	type="hidden" name="specimen_id_<?php echo $pro_cont; ?>" value="<?php echo $obj['id']; ?>">
																		<input 	type="text" class="form-control" name="collection_type_<?php echo $pro_cont; ?>" id="collection_type_<?php echo $pro_cont; ?>" value="<?php echo $obj['collection_type']; ?>">
																		<input 	type="hidden" class="form-control" name="hidden_collection_type_<?php echo $pro_cont; ?>" id="hidden_collection_type_<?php echo $pro_cont; ?>" value="">
																	</td>
																	<td>
																		<div class="row">
																			<div class="col-sm-7">
																				<div class="input-group">
																					<input type="text" name="collection_start_date_<?php echo $pro_cont; ?>" id="collection_start_date_<?php echo $pro_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('collection_start_date_<?php echo $pro_cont; ?>', 'collection_start_time_<?php echo $pro_cont; ?>')" value="<?php echo get_date_format($obj['collection_start_date']); ?>"/>
																					<label for="collection_start_date_<?php echo $pro_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>
																					</label>
																				</div>		
																			</div>
																			<div class="col-sm-5">
																				<div class="input-group">
																					<input type="text" name="collection_start_time_<?php echo $pro_cont; ?>" id="collection_start_time_<?php echo $pro_cont; ?>" class="form-control" onClick="getDate_and_setToField('collection_start_date_<?php echo $pro_cont; ?>', 'collection_start_time_<?php echo $pro_cont; ?>')" value="<?php echo $obj['collection_start_time']; ?>"/>
																					<label for="collection_start_time_<?php echo $pro_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-time"></span>
																					</label>
																				</div>
																			</div>
																		</div>
																	</td>	
																	<td>
																		<div class="row">
																			<div class="col-sm-7">
																				<div class="input-group">
																					<input type="text" name="collection_end_date_<?php echo $pro_cont; ?>" id="collection_end_date_<?php echo $pro_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('collection_end_date_<?php echo $pro_cont; ?>', 'collection_end_time_<?php echo $pro_cont; ?>')" value="<?php echo get_date_format($obj['collection_end_date']); ?>"/>
																					<label for="collection_end_date_<?php echo $pro_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-calendar"></span>
																					</label>
																				</div>		
																			</div>
																			<div class="col-sm-5">
																				<div class="input-group">
																					<input type="text" name="collection_end_time_<?php echo $pro_cont; ?>" id="collection_end_time_<?php echo $pro_cont; ?>" class="form-control" onClick="getDate_and_setToField('collection_end_date_<?php echo $pro_cont; ?>', 'collection_end_time_<?php echo $pro_cont; ?>')" value="<?php echo $obj['collection_end_time']; ?>"/>
																					<label for="collection_end_time_<?php echo $pro_cont; ?>" class="input-group-addon">
																						<span class="glyphicon glyphicon-time"></span>
																					</label>
																				</div>
																			</div>	
																		</div>
																	</td>            
																	<td>
																		<input type="text" class="form-control" name="collection_condition_<?php echo $pro_cont; ?>" value="<?php echo $obj['collection_condition']; ?>">
																	</td>
																	<td>
																		<input type="text" class="form-control" name="collection_rejection_<?php echo $pro_cont; ?>" value="<?php echo $obj['collection_rejection']; ?>">
																	</td>
																	<td>
																		<input type="text" class="form-control" name="collection_comments_<?php echo $pro_cont; ?>" value="<?php echo $obj['collection_comments']; ?>">
																	</td>
																	<td class="text-center">
                                                                        <?php if(!isDssEnable()){ ?>
																		<span class="pointer glyphicon glyphicon-remove" id="spm_removebtn" onClick="javascript:top.fancyConfirm('Are you sure you want to delete this record?','Delete Record','top.fmain.del_test(\'<?php echo $obj['id']; ?>\',\'specimen\',\'<?php echo $lab_test_id; ?>\')');"></span>
                                                                        <?php } ?>
																	</td>
																</tr>
														<?php $pro_cont++; } ?>
                                                                <?php if(!isDssEnable() || count($modal_data['lab_specimen'])==0){ ?>
														<script>spm_addrow();</script>
                                                        <?php } ?>
													</table>	
												</div>	
											</div>
										</div>	
									</div>	
									
									<!-- Lab observation result block -->	
									<div class="col-sm-12 pt10">
										<div class="row">
											<div class="purple_bar">
												<div class="row">
													<div class="col-sm-10">
														<label>Observation Result</label>		
													</div>	
													<div class="col-sm-2 text-right ">
                                                        <?php if(!isDssEnable()){ ?>
														<span class="row_add_btn glyphicon glyphicon-plus pointer" onClick="top.fmain.ob_res_addrow();"></span>	
                                                        <?php } ?>
													</div>	
												</div>
											</div>
											<div class="col-sm-12">
												<div id="observ_result_bl" class="row"  style="height:90px;overflow-x:auto;">
													<table class="table table-condensed table-bordered table-striped ob_res_countrow">
														<tr class="grythead text-center" id="tr_d_0">
															<th>Observation</th>
															<th>LOINC</th>
															<th>Result</th>
															<th>UOM</th>
															<th>Range</th>
															<th class="text-nowrap">Abnormal Flag</th>
															<th>Status</th>
															<th class="text-nowrap">Result Date/Time</th>
															<th>Comments</th>
															<th style="width:4%" class="pointer text-center"></th>
														</tr>
														<?php
															$pro_cont=1;
															foreach($modal_data['lab_obser_result'] as $obj){	?>
														<tr id="tr_d_<?php echo $pro_cont; ?>">
															<td>
																<input 	type="hidden"  name="result_id_<?php echo $pro_cont; ?>" value="<?php echo $obj['id']; ?>">
																<input 	type="text" class="form-control" name="observation_<?php echo $pro_cont; ?>" id="observation_<?php echo $pro_cont; ?>" value="<?php echo $obj['observation']; ?>" onChange="fet_loinc_code('result','<?php echo $pro_cont; ?>');">
															</td>
															<td>
																<input 	type="text" class="form-control" name="result_loinc_<?php echo $pro_cont; ?>" id="result_loinc_<?php echo $pro_cont; ?>" value="<?php echo $obj['result_loinc']; ?>">
															</td>
															<td>
																<input 	type="text" class="form-control" name="result_<?php echo $pro_cont; ?>" value="<?php echo $obj['result']; ?>">
															</td>
															<td>
																<input 	type="text" class="form-control" name="uom_<?php echo $pro_cont; ?>" value="<?php echo $obj['uom']; ?>">
															</td>
															<td>
																<input 	type="text" class="form-control" name="result_range_<?php echo $pro_cont; ?>" value="<?php echo $obj['result_range']; ?>">
																</td>
															<td style="width:100px">
																<select name="abnormal_flag_<?php echo $pro_cont; ?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select" data-container="#selectpicker_cont">
																<?php 
																	foreach($modal_data['abnormal_arr'] as $key => $val){
																		$sel="";
																		if($key==$obj['abnormal_flag']){
																			$sel="selected";
																		}
																		echo "<option value='$key' $sel>$key - $val</option>";
																	}
																 ?>
																</select>
															</td>
															<td>
																<input 	type="text" class="form-control" name="status_<?php echo $pro_cont; ?>" value="<?php echo $obj['status']; ?>">
															</td>
															<td style="width:400px">
																<div class="row">
																	<div class="col-sm-7">
																		<div class="input-group">
																			<input type="text" name="result_date_<?php echo $pro_cont; ?>" id="result_date_<?php echo $pro_cont; ?>" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField('result_date_<?php echo $pro_cont; ?>', 'result_time_<?php echo $pro_cont; ?>')" value="<?php echo get_date_format($obj['result_date']); ?>"/>
																			<label for="result_date_<?php echo $pro_cont; ?>" class="input-group-addon">
																				<span class="glyphicon glyphicon-calendar"></span>
																			</label>	
																		</div>
																	</div>
																	<div class="col-sm-5">
																		<div class="input-group">
																			<input type="text" name="result_time_<?php echo $pro_cont; ?>" id="result_time_<?php echo $pro_cont; ?>" class="form-control" onClick="getDate_and_setToField('result_date_<?php echo $pro_cont; ?>', 'result_time_<?php echo $pro_cont; ?>')" value="<?php echo $obj['result_time']; ?>"/>
																			<label for="result_time_<?php echo $pro_cont; ?>" class="input-group-addon">
																				<span class="glyphicon glyphicon-time"></span>
																			</label>	
																		</div>	
																	</div>	
																</div>
															</td>	
															<td>
																<input type="text" class="form-control" name="result_comments_<?php echo $pro_cont; ?>" value="<?php echo $obj['result_comments']; ?>">
															</td>
															<td class="text-center">
                                                                <?php if(!isDssEnable()){ ?>
																<span class="pointer glyphicon glyphicon-remove" id="removebtn" onClick="javascript:top.fancyConfirm('Are you sure you want to delete this record?','Delete Record','top.fmain.del_test(\'<?php echo $obj['id']; ?>\',\'result\',\'<?php echo $lab_test_id; ?>\')');" ></span>
                                                                <?php } ?>
															</td>
														</tr>
														<?php $pro_cont++;}?>
                                                        <?php if(!isDssEnable() || count($modal_data['lab_obser_result'])==0){ ?>
                                                            <script>ob_res_addrow();</script>
                                                        <?php } ?>
														<input type="hidden" name="result_cont" id="result_cont" value="<?php echo $pro_cont; ?>">
													</table>	
												</div>	
											</div>	
										</div>	
									</div>
								</div>
							</div>
							<div id="module_buttons" class="modal-footer ad_modal_footer">
								<?php if(isDssEnable()){ ?>
									<input type="hidden" name="save" value="Save">
									<input type="button" name="save" value="Save" id="save_btn" class="btn btn-success" onclick="validateLabOrder();">&nbsp;
								<?php } else { ?>
                                    <input type="submit" name="save" value="Save" id="save_btn" class="btn btn-success">&nbsp;
								<?php } ?>
								<input type="button" name="close" value="Close" id="close_btn" class="btn btn-danger" data-dismiss="modal">
							</div>
						</form>	
					</div>
				</div>
			</div>
		</div>
	<?php
	exit();
}
?>
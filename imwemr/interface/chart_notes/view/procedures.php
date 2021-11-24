<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->

<!-- Bootstrap -->

<!--
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" rel="stylesheet">
-->
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvproccss" rel="stylesheet">
<!--<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" rel="stylesheet" type="text/css">-->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/redactor/redactor.css" rel="stylesheet" type="text/css" />

<!--
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/wv_landing.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/drawing.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/superbill.css" rel="stylesheet" type="text/css">
-->
<?php echo $css_files; ?>


<!--<link href="../../library/css/font-awesome.css" rel="stylesheet" type="text/css">-->
<!--<link href="../../library/css/style.css" rel="stylesheet" type="text/css">-->
<style>
	.proc_alg .head{ padding:3px; text-align:center ;border:none; text-decoration:none; padding-right:10px; padding-left:10px;}
	.proc_alg .red_color{ background-color:red!important; cursor: pointer;}
	#divAllergy{ min-height:90px;overflow:auto; overflow-x:hidden;display:none;position:absolute;background-color:white; border:1px solid black;padding:5px; z-index:2; }
	#div_smart_tags_options{ top:200px;left:400px; width:300px; z-index:999; }
	#div_cnvs_bottox{border:1px solid black;background:url(<?php echo $elem_cnvs_bottox_drw_img;?>) no-repeat;background-color:white;display:inline-block;height:<?php echo $elem_cnvs_bottox_drw_img_dim_h;?>px;}
	#hold_to_phy_div{left:400px; width:400px; }
	.site_pad {text-align:center;}
	#cked_hg { height:500px;}

	#elem_pnData+#cke_elem_pnData .cke_contents
	{height: <?php echo $_SESSION['wn_height']-532; ?>px !important;}
	#pre_op_lasik .form-group>label{  width:50%; }
	.pt_info{ padding:1px;}
	.drops .form-group>label{ width:40%; }
	#procdos {
		display: inline-block;
	    font-weight: bold;
	    font-size: 14px !important;
	    width: 150px;
	    height: 20px !important;
	    background-color: transparent;
	    color: white !important;
	    border-color: transparent;
	    padding: 1px !important;
	    line-height: 8px;
	}
	#amendment .bg-grey-1{background-color:#ececec}

</style>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
       <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv/3.7.3/html5shiv.min.js"></script>
       <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
 <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsproc"></script>
<!-- Reactor JS SOURCE -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/redactor.js?<?php echo filemtime('../../library/redactor/redactor.js');?>"></script>
<!-- Plug Ins -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/fontcolor.js"></script>
<!-- <script src="<?php echo $GLOBALS['webroot']; ?>/redactor/plugins/fontfamily.js"></script> -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/fontsize.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/imagemanager.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/table.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/fullscreen.js"></script>
<!-- jQuery's Date Time Picker -->
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
<!--<script>
	//$(document).ready(function (){  if($("#elem_form_id").val()==""||$("#elem_form_id").val()=="0"){ alert("Please open patient chart note first."); window.close();  }	});
</script>-->
<script>
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var elem_per_vo = "<?php echo $elem_per_vo;?>";
var sess_pt = "<?php echo $patient_id; ?>";
var rootdir = "<?php echo $rootdir;?>";
var finalize_flag = "<?php echo $finalize_flag;?>";
var isReviewable = "<?php echo $isReviewable;?>";
var logged_user_type = "<?php echo $logged_user_type;?>";
var examName = "Procedures";
var z_cnvs_bottox_drw_coords= '<?php echo $elem_cnvs_bottox_drw_coords; ?>';
var arr_item_no_qty=<?php echo $js_arr_item_qty; ?>;
var arr_thrash=<?php echo $js_arr_thrash; ?>;
var arr_lot_no=<?php echo $js_arr_lot_no;?>;
var arr_dx_code =<?php echo $js_arr_dx;?>;
var arr_mod_code =<?php echo $js_arr_mod;?>;
var arr_cpt_code=<?php echo $js_arr_cpt;?>;
var arr_med_name=<?php echo $js_arr_med; ?>;
var consent_arr =<?php echo $arr_consent;?>;
var opnotes_arr =<?php echo $arr_opnotes;?>;
var _dtFormat = '<?php echo phpDateFormat(); ?>';
var _dTime = '<?php echo date('H:i').':00'; ?>';
var jQueryIntDateFormat = "<?php echo jQueryIntDateFormat(); ?>";
var operator = "<?php echo $operator;?>";

$(document).ready(function($) {
	window.top.$( "#procdos" ) //.val($("#elem_chart_DOS").val())
		.datetimepicker({
			format: _dtFormat+' H:i:s',
			step: 10,
			defaultTime: _dTime,
			todayButton: true,
			autoclose: true,
			scrollInput: false,
			maxDate: '0',
			timepicker: true,
		})
		.bind("change",function(){
			$("#elem_chart_DOS").val(this.value).trigger("change");
		});
});
</script>
</head>
<body>
<form name="frmProcedure"  method="post" >
<input type="hidden" name="elem_saveForm" value="procedures_save">
<input type="hidden" name="elem_chart_procedures_id" id="elem_chart_procedures_id" value="<?php echo $elem_chart_procedures_id; ?>">
<input type="hidden" name="elem_patient_id" value="<?php echo $patient_id; ?>">
<input type="hidden" name="elem_form_id" id="elem_form_id" value="<?php echo $formId; ?>">
<input type="hidden" name="elem_finalized_status" id="elem_finalized_status" value="<?php echo $elem_finalized_status; ?>">

<input type="hidden" name="elem_finalize_flag" value="<?php echo $finalize_flag; ?>">
<input type="hidden" name="elem_isReviewable" value="<?php echo $isReviewable ? 1 : 0 ; ?>">

<input type="hidden" id="curOpId" name="curOpId" value="<?php echo $elem_OpTempId; ?>">
<input type="hidden" id="curConfrmId" name="curConfrmId" value="<?php echo $elem_consentForm; ?>">
<input type="hidden" id="proc_con_frm_id" name="proc_con_frm_id" value="<?php echo $proc_con_frm_id; ?>">
<input type="hidden" id="proc_pn_rep_Id" name="proc_pn_rep_Id" value="<?php echo $proc_pn_rep_Id; ?>">

<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">

<input type="hidden" name="elem_chart_DOS" id="elem_chart_DOS" value="<?php echo $DOS.' '.date('H:i:s'); ?>">
<input type="hidden" name="hidd_cpt_grid_id" id="hidd_cpt_grid_id" value="">
<input type="hidden" name="hid_str_cpt_mod" id="hid_str_cpt_mod" value="">
<input type="hidden" name="cntCptGrid" id="cntCptGrid" value="<?php echo $cntCptGrid_t;?>">
<input type="hidden" name="hid_icd10" id="hid_icd10" value="1">
<?php  // $_SESSION['wn_height']; ?>
<?php  $value= $_SESSION['wn_height']-225 ."px"; ?>
<div id="dvprocedure_note" class=" container-fluid procedure_note" style="height:<?php echo $value; ?> ; overflow-y:auto;">
<div id="dvprocedure_note_sec" class="whtbox procedures <?php echo (!empty($elem_finalized_status)) ? "final_proc" : ""; ?> ">
	<!--<figure><img src="<?php echo $GLOBALS['webroot'];?>/library/images/close1.png" alt="" onClick="cancel_exam()" /></figure>-->
	<div class="asshead purple_bar">
		<div class="row">
			<div class="col-sm-3"><h2>Procedures</h2></div>
			<div class="col-sm-6 mt5"><?php echo $ptName_Id;?></div>
			<div class="col-sm-3 mt5" style="text-align: center;"><?php if(!empty($DOS)){ echo "DOS - <input id='procdos' name='elem_chart_DOS_fn' value='".$DOS.' '.$elem_exam_time_cur."' class='form-control' readonly='' autocomplete='off'>"; }?></div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="proc_alg">

		<div class="row" >
			<div class="col-sm-2 mt10" >
				<span class="head <?php echo $cssAllergy; ?>" <?php echo $jsAllergy; ?> >Allergies</span>
				<div id="divAllergy">
				<?php echo $strAllergies; ?>
				</div>
			</div>
			<div class="col-sm-3 mt10" >
				<div class="input-group">
				<div class="input-group-addon">BP</div>
				<input type="text" name="elem_BP" value="<?php echo $elem_BP; ?>" size="6" class="form-control <?php echo $ar_proc_note_getEditCss["elem_BP"]; ?>"  placeholder="">
				</div>
			</div>
			<div class="col-sm-2 mt10">
				<div class="checkbox checkbox-inline">
					<input type="checkbox" id="elem_heartattack" name="elem_heartattack" value="1"  <?php if($elem_heartattack==1) echo "checked"; ?>>
				<label for="elem_heartattack">Heart Attack and Stroke</label>
				</div>

			</div>
			<div class="col-sm-5 othfld mt10">
				<textarea name="elem_otherProcNote" rows="1"  class="form-control <?php echo $ar_proc_note_getEditCss["elem_otherProcNote"]; ?>" placeholder="Other" ><?php echo !empty($elem_otherProcNote) ? $elem_otherProcNote : ""; ?></textarea>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div><?php echo $strTabsDosProc;?></div>
	<div class="clearfix"></div>

	<div class="proctab">
		<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#procnote" aria-controls="procnote" role="tab" data-toggle="tab" onClick="proc_showTabs('procnote')">Proc Note</a></li>
		<li role="presentation"><a href="#consent_form" aria-controls="consent_form" role="tab" data-toggle="tab" onClick="proc_showTabs('consent_form')">Consent Form</a></li>
		<li role="presentation"><a href="#op_report" aria-controls="op_report" role="tab" data-toggle="tab" onClick="proc_showTabs('op_report')">Op Report</a></li>
		<li role="presentation" class="<?php echo (empty($elem_finalized_status)) ? "hidden" : "" ; ?>"><a href="#amendment" aria-controls="amendment" role="tab" data-toggle="tab" onClick="proc_showTabs('amendment')">Amendment</a></li>
		</ul>

		<div class="tab-content" >
			<div role="tabpanel" class="tab-pane active" id="procnote"><!-- proc_note -->

				<div class="row">
					<div class="col-sm-3">
						<div>
						<label for="elem_procedure">Procedures</label>
						<div class="form-group">
						<select name="elem_procedure" id="elem_procedure" class="form-control minimal <?php echo $ar_proc_note_getEditCss["elem_procedure"]; ?>">
							<option value=""></option>
							<?php
								echo $strProcOptions;
							?>
						</select>
						</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="proc_alg proc_sect">
						<label class="head">Site</label>
						<div class="sitemt proc_alg site_pad">

							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_site" id="elem_site_ou" value="OU" onClick="checksignle(this)" <?php if($elem_site=="OU") echo "checked"; ?>>
							<label for="elem_site_ou"><span class="oucol">OU</span></label>
							</div>
							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_site" id="elem_site_od" value="OD" onClick="checksignle(this)" <?php if($elem_site=="OD") echo "checked"; ?>>
							<label for="elem_site_od"><span class="odcol">OD</span></label>
							</div>
							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_site" id="elem_site_os" value="OS" onClick="checksignle(this)" <?php if($elem_site=="OS") echo "checked"; ?>>
							<label for="elem_site_os"><span class="odcol">OS</span></label>
							</div>
						</div>
					</div>
					</div>
					<div class="col-sm-3">
						<div id="dvlids" class="proc_alg proc_sect">
						<label class="head">Lids</label>
						<div class="sitemt proc_alg site_pad">

							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_lidsopt_rul" id="elem_lidsopt_rul" value="RUL" onClick="checksignle(this)" <?php if($elem_lidsopt_rul=="RUL") echo "checked"; ?>>
							<label for="elem_lidsopt_rul">RUL </label>
							</div>
							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_lidsopt_rll" id="elem_lidsopt_rll" value="RLL" onClick="checksignle(this)" <?php if($elem_lidsopt_rll=="RLL") echo "checked"; ?>>
							<label for="elem_lidsopt_rll">RLL </label>
							</div>
							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_lidsopt_lul" id="elem_lidsopt_lul" value="LUL" onClick="checksignle(this)" <?php if($elem_lidsopt_lul=="LUL") echo "checked"; ?>>
							<label for="elem_lidsopt_lul">LUL </label>
							</div>
							<div class="checkbox checkbox-inline">
							<input type="checkbox" name="elem_lidsopt_lll" id="elem_lidsopt_lll" value="LLL" onClick="checksignle(this)" <?php if($elem_lidsopt_lll=="LLL") echo "checked"; ?>>
							<label for="elem_lidsopt_lll">LLL </label>
							</div>
						</div>
						</div>

						<div id="dvPtOccu" class="hidden lasik_elms proc_alg">
							<label class="head">Occupation</label>
							<div class="sitemt proc_alg site_pad">
								<input type="text" id="elem_pt_occu" name="elem_pt_occu" value="<?php echo $elem_pt_occu; ?>"  class="form-control"  >
							</div>
						</div>


					</div>
					<div class="col-sm-4">

						<div id="dvTypeBtx" class="hidden botox_elms proc_alg">
							<label class="head">Botox type</label>

							<div class="  sitemt">
								<div class=" radio radio-inline">
								<input type="radio" name="elem_typeBtx" value="Medical" id="elem_typeBtx_med"  <?php if($elem_typeBtx=="Medical"||empty($elem_typeBtx)){echo "checked";} ?> >
								<label for="elem_typeBtx_med">Medical</label>
								</div>
								<div class="radio radio-inline">
								<input type="radio" name="elem_typeBtx" id="elem_typeBtx_cos" value="Cosmetic"  <?php if($elem_typeBtx=="Cosmetic"){echo "checked";} ?> >
								<label for="elem_typeBtx_cos">Cosmetic</label>
								</div>
							</div>
						</div>

						<div id="dvPtHobby" class="hidden lasik_elms proc_alg">
							<label class="head">Hobbies</label>
							<div class="sitemt proc_alg site_pad">
								<input type="text" id="elem_pt_hobby" name="elem_pt_hobby" value="<?php echo $elem_pt_hobby; ?>"  class="form-control"  >
							</div>
						</div>



					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
						<label for="exampleInputEmail1">CPT Code</label>
						<select id="cpt_multi_select" name="cpt_multi_select[]" multiple='multiple' size="1" class="form-control minimal selectpicker" data-actions-box="true">
						<?php echo $cpt_multi_select_options; ?>
						</select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="elem_dxCode">Dx Code</label>
							<select size="1" multiple id="elem_dxCode" name="elem_dxCode[]"  onchange="checksignle(this);" class="form-control minimal selectpicker" data-actions-box="true">
							<?php echo $dx_options; ?>
							</select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="elem_startTime">Start Time</label>
									<input type="text" id="elem_startTime" name="elem_startTime" value="<?php echo $elem_startTime; ?>" size="6" onClick="insertTime(this);" class="<?php echo $ar_proc_note_getEditCss["elem_startTime"]; ?> form-control" placeholder="">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
								<label for="elem_endTime">End Time</label>
								<input type="text" id="elem_endTime" name="elem_endTime" value="<?php echo $elem_endTime; ?>" size="6" onClick="insertTime(this);" class="<?php echo $ar_proc_note_getEditCss["elem_endTime"]; ?> form-control" placeholder="">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
								<label for="elem_iopType">Post Op IOP</label>
								<!--
								<select name="elem_iopType" class="form-control minimal <?php echo $ar_proc_note_getEditCss["elem_iopType"]; ?>">
									<option value=""  ></option>
									<option value="TA" <?php if($elem_iopType=="TA") echo "selected"; ?> >T<sub>A</sub></option>
									<option value="TP" <?php if($elem_iopType=="TP") echo "selected"; ?> >T<sub>P</sub></option>
									<option value="TX" <?php if($elem_iopType=="TX") echo "selected"; ?> >T<sub>X</sub></option>
									<option value="TT" <?php if($elem_iopType=="TT") echo "selected"; ?> >T<sub>T</sub></option>

								</select>
								-->
								<input type="text" id="elem_iopType" name="elem_iopType" value="<?php echo $elem_iopType; ?>" class="<?php echo $ar_proc_note_getEditCss["elem_iopType"]; ?> form-control iop_method"  placeholder="Method">

								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
								<label class="odcol" for="elem_iopOd">OD</label>
								<input type="text" id="elem_iopOd" name="elem_iopOd" value="<?php echo $elem_iopOd; ?>" size="4" onClick="insertTimeIOP();" class="<?php echo $ar_proc_note_getEditCss["elem_iopOd"]; ?> form-control"  placeholder="">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
								<label class="oscol" for="elem_iopOs">OS</label>
								<input type="text" id="elem_iopOs" name="elem_iopOs" value="<?php echo $elem_iopOs; ?>" size="4"  onClick="insertTimeIOP();" class="<?php echo $ar_proc_note_getEditCss["elem_iopOs"]; ?> form-control"  placeholder="">
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="row">
							<div class="form-group col-sm-6">
							<label for="">Time</label>
							<input type="text" id="elem_iopTime"  name="elem_iopTime" value="<?php echo $elem_iopTime; ?>" size="6" onClick="insertTime(this);" class="<?php echo $ar_proc_note_getEditCss["elem_iopTime"]; ?> form-control"  placeholder="">
							</div>
							<div class="col-sm-6">

								 <label>Complication</label>
								<div class="sitemt ">

									<div class=" radio radio-inline">

									<input type="radio" name="elem_complication" id="elem_complication_yes" value="1"   <?php if($elem_complication=="1"){echo "checked";} ?> >
									<label for="elem_complication_yes">Yes</label>
									</div>
									<div class=" radio radio-inline">
									<input type="radio" name="elem_complication" id="elem_complication_no" value="0"  <?php if(empty($elem_complication)){echo "checked";} ?> >
									<label for="elem_complication_no">No</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="row">
							<div class="col-sm-6">
								<div id="dvcmt" class="form-group">
									<label for="">CMT</label>
									<input type="text" id="elem_cmt" name="elem_cmt" value="<?php echo $elem_cmt; ?>"  class=" <?php echo $ar_proc_note_getEditCss["elem_cmt"]; ?> form-control" placeholder="">
								</div>
							</div>
						</div>
					</div>



						<div class="col-sm-12">
						<div class="form-group">
						<label for="">Comments</label>
						<textarea name="elem_comments" rows="2" class="form-control <?php echo $ar_proc_note_getEditCss["elem_comments"]; ?>" ><?php echo $elem_comments; ?></textarea>
						</div>

					</div>
				</div>

				<div class="clearfix"></div>
				<div id="tbl_pronote_med" class="premed">
					<div class="row">
						<div class="col-sm-7">
							<h3>Pre-OP Meds</h3>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Pre-OP Meds</label>
									<?php echo $html_PreOp["Med"]; ?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Lot#</label>
									<?php echo $html_PreOp["Lot"]; ?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Qty in hand</label>
									<?php echo $html_PreOp["Qty"]; ?>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
                            <div id="div_intravitreal_meds" class="row " <?php echo ($div_intravitreal_meds ? 'style="display:none;"' : ''); ?>>
                                <h3>Intravitreal Meds</h3>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Intravitreal Meds</label>
									<?php echo $html_IntVit["Med"]; ?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Lot#</label>
									<?php echo $html_IntVit["Lot"]; ?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Qty in hand</label>
									<?php echo $html_IntVit["Qty"]; ?>
									</div>
								</div>
							</div>
							<div id="div_laser_procedure_notes" class="row " <?php echo ($div_laser_procedure_notes ? 'style="display:none;"' : ''); ?>>
								<h3>Laser Procedure Notes</h3>
								<input type="hidden" id="laser_procedure_note" name="laser_procedure_note" value="<?php echo $laser_procedure_note; ?>">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Spot Duration</label>
											<textarea class="form-control" rows="1" name="spot_duration" id="spot_duration"><?php echo trim($spot_duration); ?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Spot Size</label>
											<textarea class="form-control" rows="1" name="spot_size" id="spot_size"><?php echo trim($spot_size); ?></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Power</label>
											<textarea class="form-control" rows="1" name="power" id="power"><?php echo trim($power); ?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment"># of Shots</label>
											<textarea class="form-control" rows="1" name="shots" id="shots"><?php echo trim($shots); ?></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Total Energy</label>
											<textarea class="form-control" rows="1" name="total_energy" id="total_energy"><?php echo trim($total_energy); ?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Degree of opening</label>
											<textarea class="form-control" rows="1" name="degree_of_opening" id="degree_of_opening"><?php echo trim($degree_of_opening); ?></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Exposure</label>
											<textarea class="form-control" rows="1" name="exposure" id="exposure"><?php echo trim($exposure); ?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="comment">Count</label>
											<textarea class="form-control" rows="1" name="count" id="count"><?php echo trim($count); ?></textarea>
										</div>
									</div>
								</div>
							</div>

							<div class="clearfix"></div>
							<h3>Post - OP Meds</h3>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Post - OP Meds</label>
									<?php echo $html_PostOp["Med"]; ?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Lot#</label>
									<?php echo $html_PostOp["Lot"]; ?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
									<label for="">Qty in hand</label>
									<?php echo $html_PostOp["Qty"]; ?>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-5">

							<div class="checkbox">
								<input type="checkbox" name="elem_timeout" id="elem_timeout" value="1" onClick="checkDisTimeout(this)" <?php if($elem_timeout=="1"){echo "checked";} ?> >
								<label for="elem_timeout"><strong>Time Out</strong></label>
							</div>
							<div class=" clearfix"></div>
							<div  id="tbl_Timeout" class="<?php if($elem_timeout=="1"){echo "";}else{ echo "hidden"; } ?>">
								<div class="ptnamid ">
									<ul>
									<li ><span class="head">Pt Name - ID</span></li>
									<li><?php echo $ptName_Id_2; ?></li>
									</ul>
								</div>
								<div class="clearfix"></div>
								<div class="form-group row corctproc">
									<label for="" class="col-sm-4 control-label">Correct Procedure</label>
									<div class="col-sm-6">
										<select name="elem_corrctprocedure" class="form-control minimal <?php echo $ar_proc_note_getEditCss["elem_corrctprocedure"]; ?>" >
											<option value=""></option>
											<?php
												//echo $strProcOptions;
												echo $strCorProcOptions;
											?>
										</select>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="row">
									<div class="col-sm-5">
										<div class="corsit">
											<h3>Correct Site</h3>

											<div class="checkbox checkbox-inline">
											<input type="checkbox" name="elem_corrctsite" id="elem_corrctsite_ou" value="OU" onClick="checksignle(this)" <?php if($elem_corrctsite=="OU") echo "checked"; ?>>
											<label for="elem_corrctsite_ou"> <span class="oucol"><strong>OU</strong></span></label>
											</div>

											<div class="checkbox checkbox-inline">
											<input type="checkbox" name="elem_corrctsite" id="elem_corrctsite_od" value="OD" onClick="checksignle(this)" <?php if($elem_corrctsite=="OD") echo "checked"; ?>>
											<label for="elem_corrctsite_od"><span class="odcol"><strong>OD</strong></span></label>
											</div>

											<div class="checkbox checkbox-inline">

											<input type="checkbox" name="elem_corrctsite" id="elem_corrctsite_os" value="OS" onClick="checksignle(this)" <?php if($elem_corrctsite=="OS") echo "checked"; ?>>
											<label for="elem_corrctsite_os"> <span class="oscol"> <strong>OS</strong></span></label>
											</div>
										</div>
									</div>


									<div class="col-sm-7 ">
										<div class="corsit  purple_bar">
											<h3 style="color:#fff;">Correct Lids</h3>
											<div class=" checkbox checkbox-inline">
											<input type="checkbox" name="elem_cor_lidsopt_rul" id="elem_cor_lidsopt_rul" value="RUL" onClick="checksignle(this)" <?php if($elem_cor_lidsopt_rul=="RUL") echo "checked"; ?> >
											<label for="elem_cor_lidsopt_rul"> RUL</label>
											</div>
											<div class=" checkbox checkbox-inline">
											<input type="checkbox" name="elem_cor_lidsopt_rll" id="elem_cor_lidsopt_rll" value="RLL" onClick="checksignle(this)" <?php if($elem_cor_lidsopt_rll=="RLL") echo "checked"; ?>>
											<label for="elem_cor_lidsopt_rll">RLL</label>
											</div>
											<div class=" checkbox checkbox-inline">
											<input type="checkbox" name="elem_cor_lidsopt_lul" id="elem_cor_lidsopt_lul" value="LUL" onClick="checksignle(this)" <?php if($elem_cor_lidsopt_lul=="LUL") echo "checked"; ?>>
											<label for="elem_cor_lidsopt_lul">LUL</label>
											</div>
											<div class=" checkbox checkbox-inline">

											<input type="checkbox" name="elem_cor_lidsopt_lll" id="elem_cor_lidsopt_lll" value="LLL" onClick="checksignle(this)" <?php if($elem_cor_lidsopt_lll=="LLL") echo "checked"; ?>>
											<label for="elem_cor_lidsopt_lll">LLL</label>
											</div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="prochint">
									<div class="checkbox checkbox-inline">

										<input type="checkbox" name="elem_siteMarked" id="elem_siteMarked" value="1" <?php if($elem_siteMarked=="1") echo "checked"; ?> >

										<label for="elem_siteMarked">Site marked (visible after prep & draper)</label>
									</div>
									<div class="clearfix"></div>
									<div class="checkbox checkbox-inline">

										<input type="checkbox" name="elem_positionProstheses" id="elem_positionProstheses" value="1"  <?php if($elem_positionProstheses=="1") echo "checked"; ?> >

										<label for="elem_positionProstheses">Position, prostheses, implants verified and equipment available if required</label>
									</div>
									<div class="clearfix"></div>
									<div class="checkbox checkbox-inline">

										<input type="checkbox" name="elem_consentCompletedSigned" id="elem_consentCompletedSigned" value="1" <?php if($elem_consentCompletedSigned=="1") echo "checked"; ?> >

										<label for="elem_consentCompletedSigned">Consent completed and signed </label>
									</div>
								</div>
								<div class="clearfix"></div>
								<h3>Providers</h3>
								<div class=" clearfix"></div>
								<textarea rows="3" name="elem_providers"  class="form-control <?php echo $ar_proc_note_getEditCss["elem_providers"]; ?>" ><?php echo $elem_providers; ?></textarea>
							</div>
						</div>
					</div>
				</div>
				<div id="divAddFields" class="premed hidden">
					<div class="row">
					<div class="col-sm-5">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
								<label for="elem_botox_total">Total</label>
								<input type="text" id="elem_botox_total" name="elem_botox_total" value="<?php echo $elem_botox_total;?>" size="5" class="form-control" >
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
								<label for="elem_botox_used">Used</label>
								<input type="text" id="elem_botox_used" name="elem_botox_used" value="<?php echo $elem_botox_used;?>" size="5" class="form-control" >
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
								<label for="elem_botox_wasted">Wasted</label>
								<input type="text" id="elem_botox_wasted" name="elem_botox_wasted" value="<?php echo $elem_botox_wasted;?>" readonly size="5" class="form-control">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-8">
								<div class="form-group">
								<label for="elem_botox_lot">Lot#</label>
								<input type="text" id="elem_botox_lot" name="elem_botox_lot" value="<?php echo $elem_botox_lot;?>" class="form-control" >
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
								<label for="elem_lot_expr_dt">EXPIRATION DATE</label>
								<input type="text" id="elem_lot_expr_dt" name="elem_lot_expr_dt" value="<?php echo $elem_lot_expr_dt;?>" class="form-control" >
								</div>
							</div>
						</div>

						<div class="row text-uppercase">
							<h3>Visual</h3>
						</div>

						<div class="row">
							<div class="col-sm-3 odcol">
								<label>OD</label>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<label for="elem_botox_sc_od">SC</label>
								<input type="text" id="elem_botox_sc_od" name="elem_botox_sc_od" value="<?php echo $elem_botox_sc_od;?>" size="5" class="form-control" >
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<label for="elem_botox_cc_od">CC</label>
								<input type="text" id="elem_botox_cc_od" name="elem_botox_cc_od" value="<?php echo $elem_botox_cc_od;?>" size="5" class="form-control" >
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<label for="elem_botox_other_od">Other</label>
								<input type="text" id="elem_botox_other_od" name="elem_botox_other_od" value="<?php echo $elem_botox_other_od;?>" size="5" class="form-control" >
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3 oscol">
								<label>OS</label>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<label for="elem_botox_sc_os">SC</label>
								<input type="text" id="elem_botox_sc_os" name="elem_botox_sc_os" value="<?php echo $elem_botox_sc_os;?>" size="5" class="form-control" >
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<label for="elem_botox_cc_os">CC</label>
								<input type="text" id="elem_botox_cc_os" name="elem_botox_cc_os" value="<?php echo $elem_botox_cc_os;?>" size="5" class="form-control" >
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<label for="elem_botox_other_os">Other</label>
								<input type="text" id="elem_botox_other_os" name="elem_botox_other_os" value="<?php echo $elem_botox_other_os;?>" size="5" class="form-control" >
								</div>
							</div>
						</div>
						<div class="row">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="elem_botox_rbdcs" id="elem_botox_rbdcs" value="R&B Discussed, Consent signed" <?php echo ($elem_botox_rbdcs=="R&B Discussed, Consent signed") ? "CHECKED" : "";?> > <label for="elem_botox_rbdcs">R&amp;B Discussed, Consent signed</label>
							</div>
						</div>
						<div class="row">
							<div class="radio radio-inline">
								<input type="radio" name="elem_botox_inject_radio" id="elem_botox_inject_radio_1" value="First Injection" <?php echo ($elem_botox_inject_radio=="First Injection") ? "CHECKED" : "";?> > <label for="elem_botox_inject_radio_1">First Injection</label>
							</div>
						</div>
						<div class="row">
							<div class="radio radio-inline">
								<input type="radio" name="elem_botox_inject_radio" id="elem_botox_inject_radio_2" value="No change in pattern" <?php echo ($elem_botox_inject_radio=="No change in pattern") ? "CHECKED" : "";?> > <label for="elem_botox_inject_radio_2">No change in pattern</label>
							</div>
						</div>
						<div class="row">
							<div class="radio radio-inline">
								<input type="radio" name="elem_botox_inject_radio" id="elem_botox_inject_radio_3" value="Injection pattern altered" <?php echo ($elem_botox_inject_radio=="Injection pattern altered") ? "CHECKED" : "";?> > <label for="elem_botox_inject_radio_3">Injection pattern altered</label>
							</div>
						</div>
					</div>
					<div class="col-sm-5" >
						<div id="divBottox">
							<div id="div_cnvs_bottox">
								<canvas id="cnvs_bottox" height="<?php echo $elem_cnvs_bottox_drw_img_dim_h;?>" width="<?php echo $elem_cnvs_bottox_drw_img_dim_w;?>" ></canvas>
							</div>
							<input type="hidden" id="elem_cnvs_bottox_drw" name="elem_cnvs_bottox_drw" value="<?php echo $elem_cnvs_bottox_drw;?>">
							<input type="hidden" id="elem_cnvs_bottox_drw_coords" name="elem_cnvs_bottox_drw_coords" value="">
							<input type="hidden" id="elem_bottox_open_flg" name="elem_bottox_open_flg" value="<?php echo $elem_bottox_open_flg;?>">
							<input type="hidden" id="elem_cnvs_bottox_drw_img" name="elem_cnvs_bottox_drw_img" value="<?php echo $elem_cnvs_bottox_drw_img; ?>">
							<input type="hidden" id="elem_cnvs_bottox_drw_img_dim" name="elem_cnvs_bottox_drw_img_dim" value="<?php echo $elem_cnvs_bottox_drw_img_dim; ?>">
						</div>

					</div>
					<div class="col-sm-2">
						<div id="divBtxUnits">
						<?php echo $botoxDosages; ?>
						</div>
						<div id="divOldBottox" class="pull-left"  >
							<?php echo $old_pt_bottox;?>
						</div>
					</div>
					</div>
				</div>
				<div id="div_lasik_proc" class="premed hidden lasik_con">
					<input type="hidden" id="elem_lasik_open_flg" name="elem_lasik_open_flg" value="<?php echo $elem_lasik_open_flg;?>">
					<div id="pre_op_lasik">
					<div class="row">
						<div class="col-sm-1">Pre-op tech</div>
						<div class="col-sm-7">
							<select id="el_pre_op_tech" name="el_pre_op_tech" class="form-control minimal">
							<?php echo $ddPreOPTech; ?>
							</select>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Insert punctal plugs</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_ins_punc_plg" id="el_ins_punc_plg_Y" value="Insert punctal plugs-Yes"  <?php if(strpos($pre_op_checks,"Insert punctal plugs-Yes")!==false) echo "checked"; ?> >
									<label for="el_ins_punc_plg_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_ins_punc_plg" id="el_ins_punc_plg_N" value="Insert punctal plugs-No"  <?php if(strpos($pre_op_checks,"Insert punctal plugs-No")!==false) echo "checked"; ?> >
									<label for="el_ins_punc_plg_N">No</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-1">Allergies</div>
						<div class="col-sm-7">
							<textarea id="el_lasik_allergies" name="el_lasik_allergies" class="form-control" rows="1"><?php echo $el_lasik_allergies; ?></textarea>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Monovision</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_monovis" id="el_monovis_Y" value="Monovision-Yes"  <?php if(strpos($pre_op_checks,"Monovision-Yes")!==false) echo "checked"; ?> >
									<label for="el_monovis_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_monovis" id="el_monovis_N" value="Monovision-No"  <?php if(strpos($pre_op_checks,"Monovision-No")!==false) echo "checked"; ?> >
									<label for="el_monovis_N">No</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Consent Reviewed</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_consent_review" id="el_consent_review_Y" value="Consent Reviewed-Yes"  <?php if(strpos($pre_op_checks,"Consent Reviewed-Yes")!==false) echo "checked"; ?> >
									<label for="el_consent_review_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_consent_review" id="el_consent_review_N" value="Consent Reviewed-No"  <?php if(strpos($pre_op_checks,"Consent Reviewed-No")!==false) echo "checked"; ?> >
									<label for="el_consent_review_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Bedatine</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_bedatine" id="el_bedatine_Y" value="Bedatine-Yes"  <?php if(strpos($pre_op_checks,"Bedatine-Yes")!==false) echo "checked"; ?> >
									<label for="el_bedatine_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_bedatine" id="el_bedatine_N" value="Bedatine-No"  <?php if(strpos($pre_op_checks,"Bedatine-No")!==false) echo "checked"; ?> >
									<label for="el_bedatine_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Near eye</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_near_eye" id="el_near_eye_od" value="OD"  <?php if($el_near_eye=="OD") echo "checked"; ?> >
									<label for="el_near_eye_od" class="odcol">OD</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_near_eye" id="el_near_eye_os" value="OS"  <?php if($el_near_eye=="OS") echo "checked"; ?> >
									<label for="el_near_eye_os" class="oscol">OS</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Pachymetry</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_pachy" id="el_lasik_pachy_Y" value="Pachymetry-Yes"  <?php if(strpos($pre_op_checks,"Pachymetry-Yes")!==false) echo "checked"; ?> >
									<label for="el_lasik_pachy_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_pachy" id="el_lasik_pachy_N" value="Pachymetry-No"  <?php if(strpos($pre_op_checks,"Pachymetry-No")!==false) echo "checked"; ?> >
									<label for="el_lasik_pachy_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Repeat ORBS</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_repeat_orbs" id="el_repeat_orbs_Y" value="Repeat ORBS-Yes"  <?php if(strpos($pre_op_checks,"Repeat ORBS-Yes")!==false) echo "checked"; ?> >
									<label for="el_repeat_orbs_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_repeat_orbs" id="el_repeat_orbs_N" value="Repeat ORBS-No"  <?php if(strpos($pre_op_checks,"Repeat ORBS-No")!==false) echo "checked"; ?> >
									<label for="el_repeat_orbs_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Acular</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_acular" id="el_lasik_acular_Y" value="Acular-Yes"  <?php if(strpos($pre_op_checks,"Acular-Yes")!==false) echo "checked"; ?> >
									<label for="el_lasik_acular_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_acular" id="el_lasik_acular_N" value="Acular-No"  <?php if(strpos($pre_op_checks,"Acular-No")!==false) echo "checked"; ?> >
									<label for="el_lasik_acular_N">No</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Preparatory Antibiotic</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_pre_antibiotic" id="el_pre_antibiotic_Y" value="Preparatory Antibiotic-Yes"  <?php if(strpos($pre_op_checks,"Preparatory Antibiotic-Yes")!==false) echo "checked"; ?> >
									<label for="el_pre_antibiotic_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_pre_antibiotic" id="el_pre_antibiotic_N" value="Preparatory Antibiotic-No"  <?php if(strpos($pre_op_checks,"Preparatory Antibiotic-No")!==false) echo "checked"; ?> >
									<label for="el_pre_antibiotic_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Alcaine</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_alcaine" id="el_lasik_alcaine_Y" value="Alcaine-Yes"  <?php if(strpos($pre_op_checks,"Alcaine-Yes")!==false) echo "checked"; ?> >
									<label for="el_lasik_alcaine_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_alcaine" id="el_lasik_alcaine_N" value="Alcaine-No"  <?php if(strpos($pre_op_checks,"Alcaine-No")!==false) echo "checked"; ?> >
									<label for="el_lasik_alcaine_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label for="el_lasik_xanax" >Xanax/Valium 5 mg @</label>
								<input type="text" name="el_lasik_xanax" id="el_lasik_xanax" value="<?php echo $el_lasik_xanax; ?>" class="form-control" >
							</div>
						</div>
					</div>
					<hr/>
					</div>

					<div class="row ">
						<div class="col-sm-6 ">
						<div class=" checkbox checkbox-inline">
							<input type="checkbox" name="el_lasik_modifier_lasik" id="el_lasik_modifier_lasik" value="Lasik"  <?php if(strpos($lasik_modifier,"Lasik")!==false) echo "checked"; ?> >
							<label for="el_lasik_modifier_lasik">Lasik</label>
						</div>
						<div class=" checkbox checkbox-inline">
							<input type="checkbox" name="el_lasik_modifier_prk" id="el_lasik_modifier_prk" value="PRK"  <?php if(strpos($lasik_modifier,"PRK")!==false) echo "checked"; ?> >
							<label for="el_lasik_modifier_prk">PRK</label>
						</div>
						<div class=" checkbox checkbox-inline">
							<input type="checkbox" name="el_lasik_modifier_opti" id="el_lasik_modifier_opti" value="Optimized"  <?php if(strpos($lasik_modifier,"Optimized")!==false) echo "checked"; ?> >
							<label for="el_lasik_modifier_opti">Optimized</label>
						</div>
						<div class=" checkbox checkbox-inline">
							<input type="checkbox" name="el_lasik_modifier_topo_guided" id="el_lasik_modifier_topo_guided" value="Topo-Guided"  <?php if(strpos($lasik_modifier,"Topo-Guided")!==false) echo "checked"; ?> >
							<label for="el_lasik_modifier_topo_guided">Topo-Guided</label>
						</div>
						<div class=" checkbox checkbox-inline">
							<input type="checkbox" name="el_lasik_modifier_enh" id="el_lasik_modifier_enh" value="Enh"  <?php if(strpos($lasik_modifier,"Enh")!==false) echo "checked"; ?> >
							<label for="el_lasik_modifier_enh">Enh</label>
						</div>
						<div class=" checkbox checkbox-inline">
							<input type="checkbox" name="el_lasik_modifier_flap_lift" id="el_lasik_modifier_flap_lift" value="Flap Lift"  <?php if(strpos($lasik_modifier,"Flap Lift")!==false) echo "checked"; ?> >
							<label for="el_lasik_modifier_flap_lift">Flap Lift</label>
						</div>
						</div>
						<div class="col-sm-3">
						<div class="form-group">
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_lasik_modifier_eye" id="el_lasik_modifier_eye_od" value="OD"  <?php if($el_lasik_modifier_eye=="OD") echo "checked"; ?> >
								<label for="el_lasik_modifier_eye_od" class="odcol">OD</label>
							</div>
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_lasik_modifier_eye" id="el_lasik_modifier_eye_os" value="OS"  <?php if($el_lasik_modifier_eye=="OS") echo "checked"; ?> >
								<label for="el_lasik_modifier_eye_os" class="oscol">OS</label>
							</div>
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_lasik_modifier_eye" id="el_lasik_modifier_eye_ou" value="OU"  <?php if($el_lasik_modifier_eye=="OU") echo "checked"; ?> >
								<label for="el_lasik_modifier_eye_ou" class="oucol">OU</label>
							</div>
						</div>
						</div>
					</div>

					<div class="row ">
						<div class="col-sm-2">Verbal Patient Verification</div>
						<div class="col-sm-10">
							<div class="row">
								<div class="col-sm-3">
									<div class=" checkbox checkbox-inline">
										<input type="checkbox" name="el_vpv_name" id="el_vpv_name" value="Name"  <?php if($el_vpv_name=="Name") echo "checked"; ?> >
										<label for="el_vpv_name">Name</label>
									</div>
									<label class="pt_info bg-info"><?php echo $pt_name_lasik; ?></label>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_name_srg" >Surgeon</label>
										<select id="el_vpv_name_srg" name="el_vpv_name_srg" class="form-control minimal">
										<?php echo $ddNameSurg; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_name_tech" >Tech</label>
										<select id="el_vpv_name_tech" name="el_vpv_name_tech" class="form-control minimal">
										<?php echo $ddNameTech; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_name_time" >Time</label>
										<input type="text" name="el_vpv_name_time" id="el_vpv_name_time" value="<?php echo $el_vpv_name_time; ?>" class="form-control" onclick="insertTime(this);" >
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class=" checkbox checkbox-inline">
										<input type="checkbox" name="el_vpv_dob" id="el_vpv_dob" value="DOB"  <?php if($el_vpv_dob=="DOB") echo "checked"; ?> >
										<label for="el_vpv_dob">D.O.B.</label>
									</div>
									<label class="pt_info bg-info"><?php echo $pt_dob_lasik; ?></label>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_dob_srg" >Surgeon</label>
										<select id="el_vpv_dob_srg" name="el_vpv_dob_srg" class="form-control minimal">
										<?php echo $ddDOBSurg; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_dob_tech" >Tech</label>
										<select id="el_vpv_dob_tech" name="el_vpv_dob_tech" class="form-control minimal">
										<?php echo $ddDOBTech; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_dob_time" >Time</label>
										<input type="text" name="el_vpv_dob_time" id="el_vpv_dob_time" value="<?php echo $el_vpv_dob_time; ?>" class="form-control" onclick="insertTime(this);" >
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<div class=" checkbox checkbox-inline">
										<input type="checkbox" name="el_vpv_proc_site" id="el_vpv_proc_site" value="Procedure Site"  <?php if($el_vpv_proc_site=="Procedure Site") echo "checked"; ?> >
										<label for="el_vpv_proc_site">Procedure Site</label>
									</div>
									<label id="pt_site_lasik" class="pt_info bg-info"><?php echo $pt_site_lasik; ?></label>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_proc_site_srg" >Surgeon</label>
										<select id="el_vpv_proc_site_srg" name="el_vpv_proc_site_srg" class="form-control minimal">
										<?php echo $ddProcSiteSurg; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_proc_site_tech" >Tech</label>
										<select id="el_vpv_proc_site_tech" name="el_vpv_proc_site_tech" class="form-control minimal">
										<?php echo $ddProcSiteTech; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_proc_site_time" >Time</label>
										<input type="text" name="el_vpv_proc_site_time" id="el_vpv_proc_site_time" value="<?php echo $el_vpv_proc_site_time; ?>" class="form-control" onclick="insertTime(this);" >
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3 ">
									<label for="el_vpv_proc_type_mono">Procedure Type</label>
									<div class="form-group">

										<div class=" checkbox checkbox-inline">
											<input type="checkbox" name="el_vpv_proc_type" id="el_vpv_proc_type_mono" value="Mono"  <?php if($el_vpv_proc_type=="Mono") echo "checked"; ?> >
											<label for="el_vpv_proc_type_mono">Mono</label>
										</div>
										<div class=" checkbox checkbox-inline">
											<input type="checkbox" name="el_vpv_proc_type" id="el_vpv_proc_type_dvo" value="DVO"  <?php if($el_vpv_proc_type=="DVO") echo "checked"; ?> >
											<label for="el_vpv_proc_type_dvo">DVO</label>
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_proc_type_srg" >Surgeon</label>
										<select id="el_vpv_proc_type_srg" name="el_vpv_proc_type_srg" class="form-control minimal">
										<?php echo $ddProcTypeSurg; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_proc_type_tech" >Tech</label>
										<select id="el_vpv_proc_type_tech" name="el_vpv_proc_type_tech" class="form-control minimal">
										<?php echo $ddProcTypeTech; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group form-inline">
										<label for="el_vpv_proc_type_time" >Time</label>
										<input type="text" name="el_vpv_proc_type_time" id="el_vpv_proc_type_time" value="<?php echo $el_vpv_proc_type_time; ?>" class="form-control" onclick="insertTime(this);" >
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr/>

					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-5 text-center"><b>Right eye</b></div>
						<div class="col-sm-5 text-center"><b>Left eye</b></div>
					</div>

					<div class="row">
						<div class="col-sm-2">D.O.S MR</div>
						<div class="col-sm-5"><input type="text" name="el_dos_mr_od" id="el_dos_mr_od" value="<?php echo $el_dos_mr_od; ?>" class="form-control" ></div>
						<div class="col-sm-5"><input type="text" name="el_dos_mr_os" id="el_dos_mr_os" value="<?php echo $el_dos_mr_os; ?>" class="form-control" ></div>
					</div>

					<div class="row">
						<div class="col-sm-2">Post-op target</div>
						<div class="col-sm-1">
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_post_op_target_od_plano" id="el_post_op_target_od_plano" value="Plano"  <?php if($el_post_op_target_od_plano=="Plano") echo "checked"; ?> >
								<label for="el_post_op_target_od_plano">Plano</label>
							</div>
						</div>
						<div class="col-sm-4">
							<input type="text" name="el_post_op_target_od" id="el_post_op_target_od" value="<?php echo $el_post_op_target_od; ?>" class="form-control" >

						</div>
						<div class="col-sm-1">
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_post_op_target_os_plano" id="el_post_op_target_os_plano" value="Plano"  <?php if($el_post_op_target_os_plano=="Plano") echo "checked"; ?> >
								<label for="el_post_op_target_os_plano">Plano</label>
							</div>
						</div>
						<div class="col-sm-4">
							<input type="text" name="el_post_op_target_os" id="el_post_op_target_os" value="<?php echo $el_post_op_target_os; ?>" class="form-control" >

						</div>
					</div>


					<div class="row">
						<div class="col-sm-2">Avg K or Axis</div>
						<div class="col-sm-5"><input type="text" name="el_avg_k_axis_od" id="el_avg_k_axis_od" value="<?php echo $el_avg_k_axis_od; ?>" class="form-control" ></div>
						<div class="col-sm-5"><input type="text" name="el_avg_k_axis_os" id="el_avg_k_axis_os" value="<?php echo $el_avg_k_axis_os; ?>" class="form-control" ></div>
					</div>

					<div class="row">
						<div class="col-sm-2">Treatment 1</div>
						<div class="col-sm-5"><input type="text" name="el_treatment1_od" id="el_treatment1_od" value="<?php echo $el_treatment1_od; ?>" class="form-control" ></div>
						<div class="col-sm-5"><input type="text" name="el_treatment1_os" id="el_treatment1_os" value="<?php echo $el_treatment1_os; ?>" class="form-control" ></div>
					</div>

					<div class="row">
						<div class="col-sm-2">Pachy</div>
						<div class="col-sm-5"><input type="text" name="el_lasik_pachy_od" id="el_lasik_pachy_od" value="<?php echo $el_lasik_pachy_od; ?>" class="form-control" ></div>
						<div class="col-sm-5"><input type="text" name="el_lasik_pachy_os" id="el_lasik_pachy_os" value="<?php echo $el_lasik_pachy_os; ?>" class="form-control" ></div>
					</div>

					<div class="row">
						<div class="col-sm-2">Flap Thickness</div>
						<div class="col-sm-5"><input type="text" name="el_flap_thick_od" id="el_flap_thick_od" value="<?php echo $el_flap_thick_od; ?>" class="form-control" ></div>
						<div class="col-sm-5"><input type="text" name="el_flap_thick_os" id="el_flap_thick_os" value="<?php echo $el_flap_thick_os; ?>" class="form-control" ></div>
					</div>

					<div class="row">
						<div class="col-sm-2">Stromal Bed</div>
						<div class="col-sm-5"><input type="text" name="el_stromal_bed_od" id="el_stromal_bed_od" value="<?php echo $el_stromal_bed_od; ?>" class="form-control" ></div>
						<div class="col-sm-5"><input type="text" name="el_stromal_bed_os" id="el_stromal_bed_os" value="<?php echo $el_stromal_bed_os; ?>" class="form-control" ></div>
					</div>

					<div class="row">
						<div class="col-sm-2">Keratome</div>
						<div class="col-sm-2 "><label for="el_hansatome_od" >Hansatome #</label></div>
						<div class="col-sm-3">
							<input type="text" name="el_hansatome_od" id="el_hansatome_od" value="<?php echo $el_hansatome_od; ?>" class="form-control" >
						</div>
						<div class="col-sm-2"><label for="el_hansatome_od" >Hansatome #</label></div>
						<div class="col-sm-3">
							<input type="text" name="el_hansatome_os" id="el_hansatome_os" value="<?php echo $el_hansatome_os; ?>" class="form-control" >
						</div>
					</div>

					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-5">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group form-inline">
										<label for="el_keratome_ring_od" >Ring</label>
										<input type="text" name="el_keratome_ring_od" id="el_keratome_ring_od" value="<?php echo $el_keratome_ring_od; ?>" class="form-control" >
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group form-inline">
										<label for="el_keratome_plate_od" >Plate</label>
										<input type="text" name="el_keratome_plate_od" id="el_keratome_plate_od" value="<?php echo $el_keratome_plate_od; ?>" class="form-control" >
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-5">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group form-inline">
										<label for="el_keratome_ring_os" >Ring</label>
										<input type="text" name="el_keratome_ring_os" id="el_keratome_ring_os" value="<?php echo $el_keratome_ring_os; ?>" class="form-control" >
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group form-inline">
										<label for="el_keratome_plate_os" >Plate</label>
										<input type="text" name="el_keratome_plate_os" id="el_keratome_plate_os" value="<?php echo $el_keratome_plate_os; ?>" class="form-control" >
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_risks_benefits" id="el_risks_benefits" value="1"  <?php if($el_risks_benefits=="1") echo "checked"; ?> >
								<label for="el_risks_benefits"><small>The risks, benefits and alternatives regarding LASIK/PRK/enhancement/flap lift were discussed with the patient which includes but is not limited to infection,
							bleeding, over/under correction, astigmatism, cosmetic deformity, glare, halos, starburst, diplopia, glasses, bifocals, monovision, co-management, AK, RK, PRK,
							INTACS, IOL's, FDA etc. Patient states they understand and states they desire to proceed. </small></label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-2">
							<div class="form-group form-inline">
								<label for="el_surgeon_sign_id" <?php if(!empty($click_get_sign)){ echo " onclick=\"get_surgeon_sign();\" class=\"clickable\" "; } ?> >Surgeon Sign.</label>

								<input type="hidden" name="el_surgeon_sign" id="el_surgeon_sign" value="<?php echo $el_surgeon_sign; ?>" class="form-control" >
								<input type="hidden" name="el_surgeon_sign_path" id="el_surgeon_sign_path" value="<?php echo $el_surgeon_sign_path; ?>" class="form-control" >
							</div>
						</div>
						<div class="col-sm-4" id="div_sgn_sign">
							<?php if(!empty($el_surgeon_sign) && !empty($el_surgeon_sign_path)){  echo "<img src=\"".$el_surgeon_sign_path."\" alt=\"sign\" height=\"30\" >"; } ?>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label for="el_surgeon_sign_dos" >Date</label>
								<input type="text" name="el_surgeon_sign_dos" id="el_surgeon_sign_dos" value="<?php echo $el_surgeon_sign_dos; ?>" class="form-control" data-dtcr="<?php echo $el_surgeon_sign_dos_todo; ?>" readonly >
							</div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-sm-3" >
							<div class="form-group form-inline">
								<label  >Abrasion</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_abrasion" id="el_abrasion_od" value="OD"  <?php if($el_abrasion=="OD") echo "checked"; ?> >
									<label for="el_abrasion_od" class="odcol">OD</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_abrasion" id="el_abrasion_os" value="OS"  <?php if($el_abrasion=="OS") echo "checked"; ?> >
									<label for="el_abrasion_os" class="oscol">OS</label>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >BCL</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_bcl" id="el_bcl_od" value="OD"  <?php if($el_bcl=="OD") echo "checked"; ?> >
									<label for="el_bcl_od" class="odcol">OD</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_bcl" id="el_bcl_os" value="OS"  <?php if($el_bcl=="OS") echo "checked"; ?> >
									<label for="el_bcl_os" class="oscol">OS</label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group form-inline">
								<label for="el_post_op_type" >Type</label>
								<input type="text" name="el_post_op_type" id="el_post_op_type" value="<?php echo $el_post_op_type; ?>" class="form-control" >
							</div>
						</div>

					</div>

					<div class="row drops">
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Zymaxid</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_zymaxid" id="el_zymaxid_Y" value="Zymaxid-Yes"  <?php if(strpos($drops,"Zymaxid-Yes")!==false) echo "checked"; ?> >
									<label for="el_zymaxid_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_zymaxid" id="el_zymaxid_N" value="Zymaxid-No"  <?php if(strpos($drops,"Zymaxid-No")!==false) echo "checked"; ?> >
									<label for="el_zymaxid_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Pred Forte</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_pred_forte" id="el_pred_forte_Y" value="Pred Forte-Yes"  <?php if(strpos($drops,"Pred Forte-Yes")!==false) echo "checked"; ?> >
									<label for="el_pred_forte_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_pred_forte" id="el_pred_forte_N" value="Pred Forte-No"  <?php if(strpos($drops,"Pred Forte-No")!==false) echo "checked"; ?> >
									<label for="el_pred_forte_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Acular</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_postop_lasik_acular" id="el_postop_lasik_acular_Y" value="Acular-Yes"  <?php if(strpos($drops,"Acular-Yes")!==false) echo "checked"; ?> >
									<label for="el_postop_lasik_acular_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_postop_lasik_acular" id="el_postop_lasik_acular_N" value="Acular-No"  <?php if(strpos($drops,"Acular-No")!==false) echo "checked"; ?> >
									<label for="el_postop_lasik_acular_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Omnipred</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_omnipred" id="el_lasik_omnipred_Y" value="Omnipred-Yes"  <?php if(strpos($drops,"Omnipred-Yes")!==false) echo "checked"; ?> >
									<label for="el_lasik_omnipred_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lasik_omnipred" id="el_lasik_omnipred_N" value="Omnipred-No"  <?php if(strpos($drops,"Omnipred-No")!==false) echo "checked"; ?> >
									<label for="el_lasik_omnipred_N">No</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row drops">
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Polytrim</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_polytrim" id="el_polytrim_Y" value="Polytrim-Yes"  <?php if(strpos($drops,"Polytrim-Yes")!==false) echo "checked"; ?> >
									<label for="el_polytrim_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_polytrim" id="el_polytrim_N" value="Polytrim-No"  <?php if(strpos($drops,"Polytrim-No")!==false) echo "checked"; ?> >
									<label for="el_polytrim_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Lotemax</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lotemax" id="el_lotemax_Y" value="Lotemax-Yes"  <?php if(strpos($drops,"Lotemax-Yes")!==false) echo "checked"; ?> >
									<label for="el_lotemax_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_lotemax" id="el_lotemax_N" value="Lotemax-No"  <?php if(strpos($drops,"Lotemax-No")!==false) echo "checked"; ?> >
									<label for="el_lotemax_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label  >Prolensa</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_prolensa" id="el_prolensa_Y" value="Prolensa-Yes"  <?php if(strpos($drops,"Prolensa-Yes")!==false) echo "checked"; ?> >
									<label for="el_prolensa_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_prolensa" id="el_prolensa_N" value="Prolensa-No"  <?php if(strpos($drops,"Prolensa-No")!==false) echo "checked"; ?> >
									<label for="el_prolensa_N">No</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label for="el_temperature" >Temp</label>
								<input type="text" name="el_temperature" id="el_temperature" value="<?php echo $el_temperature; ?>" class="form-control" > F
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label for="el_humidity" >Hum</label>
								<input type="text" name="el_humidity" id="el_humidity" value="<?php echo $el_humidity; ?>" class="form-control" > %
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label for="el_post_op_surgeon" >Surgeon</label>
								<select id="el_post_op_surgeon" name="el_post_op_surgeon" class="form-control minimal">
								<?php echo $ddPostOpSurg; ?>
								</select>
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label for="el_laser_oprtr" >Laser Operator</label>
								<select id="el_laser_oprtr" name="el_laser_oprtr" class="form-control minimal">
								<?php echo $ddPostOpLaserOprator; ?>
								</select>
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group form-inline">
								<label for="el_keratome_tech" >Keratome Tech</label>
								<select id="el_keratome_tech" name="el_keratome_tech" class="form-control minimal">
								<?php echo $ddPostOpKeraTech; ?>
								</select>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-sm-1">Cornea Check</div>
						<div class="col-sm-2">
						<div class="form-group form-inline">
							<label class="odcol">OD</label>
							<div class=" checkbox checkbox-inline">
								<input type="checkbox" name="el_cornea_check_od_Ex" id="el_cornea_check_od_Ex" value="Excellent"  <?php if($el_cornea_check_od_Ex=="Excellent") echo "checked"; ?> >
								<label for="el_cornea_check_od_Ex">Excellent</label>
							</div>
						</div>
						</div>
						<div class="col-sm-2">
						<div class="form-group form-inline">
							<label class="oscol">OS</label>
							<div class=" checkbox checkbox-inline">

								<input type="checkbox" name="el_cornea_check_os_Ex" id="el_cornea_check_os_Ex" value="Excellent"  <?php if($el_cornea_check_os_Ex=="Excellent") echo "checked"; ?> >
								<label for="el_cornea_check_os_Ex">Excellent</label>
							</div>
						</div>
						</div>
					</div>


					<div class="row">
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label  >Plugs Inserted</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_plugs_inserted" id="el_plugs_inserted_collagen" value="Collagen"  <?php if($el_plugs_inserted=="Collagen") echo "checked"; ?> >
									<label for="el_plugs_inserted_collagen">Collagen</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_plugs_inserted" id="el_plugs_inserted_sillicone" value="Sillicone"  <?php if($el_plugs_inserted=="Sillicone") echo "checked"; ?> >
									<label for="el_plugs_inserted_sillicone">Sillicone</label>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_plugs_inserted_eye" id="el_plugs_inserted_eye_od" value="OD"  <?php if($el_plugs_inserted_eye=="OD") echo "checked"; ?> >
									<label for="el_plugs_inserted_eye_od" class="odcol">OD</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_plugs_inserted_eye" id="el_plugs_inserted_eye_os" value="OS"  <?php if($el_plugs_inserted_eye=="OS") echo "checked"; ?> >
									<label for="el_plugs_inserted_eye_os" class="oscol">OS</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_plugs_inserted_eye" id="el_plugs_inserted_eye_ou" value="OU"  <?php if($el_plugs_inserted_eye=="OU") echo "checked"; ?> >
									<label for="el_plugs_inserted_eye_ou" class="oucol">OU</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group form-inline">
								<label for="el_plugs_inserted_size" >Size</label>
								<input type="text" name="el_plugs_inserted_size" id="el_plugs_inserted_size" value="<?php echo $el_plugs_inserted_size; ?>" class="form-control" >
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group form-inline">
								<label  >Post op instructions/kit discussed, demonstrated and given?</label>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_post_op_kit_given" id="el_post_op_kit_given_Y" value="1"  <?php if($el_post_op_kit_given=="1") echo "checked"; ?> >
									<label for="el_post_op_kit_given_Y">Yes</label>
								</div>
								<div class=" checkbox checkbox-inline">
									<input type="checkbox" name="el_post_op_kit_given" id="el_post_op_kit_given_N" value="2"  <?php if($el_post_op_kit_given=="2") echo "checked"; ?> >
									<label for="el_post_op_kit_given_N">No</label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group form-inline">
								<label for="el_post_op_tech" >Post op tech</label>
								<select id="el_post_op_tech" name="el_post_op_tech" class="form-control minimal">
								<?php echo $ddPostOpTech; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-1">Comments</div>
						<div class="col-sm-11">
							<textarea name="el_lasik_comments" id="el_lasik_comments" class="form-control" placeholder="Comments" ><?php echo $el_lasik_comments; ?></textarea>
						</div>
					</div>
				</div>
                <div class="clearfix"></div>
                <div id="superbill" class="whitebox">
                    <?php
                    $oload = new SuperBillLoader($this->pid,$this->fid);
		    $oload->set_encntr_id($elem_encounter_id);
                    echo $oload->getWorkViewSuperbill();
                    ?>
                </div>
			</div>
			<div role="tabpanel" class="tab-pane" id="consent_form">
				<div class="row">
					<div class="col-sm-7">
						<div class="form-group form-inline">
							<label for="elem_consentForm" class="head" style="border:none;">Select Consent form:</label>
							<select id="elem_consentForm" name="elem_consentForm" onChange="getConsentForm(1)" class="form-control minimal">
							<?php echo $strConsentForms; ?>
							</select>
						</div>
					</div>
					<div class="col-sm-5">
						<span id="consent_select" class="glyphicon"> <?php echo $consent_signed_img;?></span>
					</div>
				</div>
				<div class="row">
					<div class="embed-responsive embed-responsive-16by9">
					<iframe id="iframeConsentForm" name="iframeConsentForm" class="embed-responsive-item"></iframe>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="op_report">
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group form-inline">
							<label for="elem_consentForm" class="head" style="border:none;">Select Op Note:</label>
							<?php echo $strPnTemplate ;?>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- Data -->
					<textarea name="elem_pnData" id="elem_pnData" class="form-control" ><?php $elem_pnData; ?></textarea>
                    <script type="text/javascript">
					$(function()
					{
						$('#elem_pnData').redactor({
							buttonSource: true,
							imageUpload: '<?php echo $GLOBALS['webroot']; ?>/library/redactor/upload.php',
							plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
							minHeight: <?php echo $_SESSION['wn_height'] - 530?>,
							maxHeight: <?php echo $_SESSION['wn_height'] - 530?>,
						});
					});
					</script>
					<!-- Data -->
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="amendment">

				<div class="row" >
					<div class="col-sm-5" >
						<div class="form-group">
							<label class="control-label" >
							Procedure Performed
							</label>
							<div class="form-control bg-grey-1"  >
							<?php echo $el_proc_perfmd_amnd; ?>
							</div>
						</div>
					</div>
					<div class="col-sm-3" >
						<div class="form-group">
							<label class="control-label" >
							Site
							</label>
							<div class="form-control bg-grey-1"  >
							<?php echo $pt_site_lasik; ?>
							</div>
						</div>
					</div>
					<div class="col-sm-4" >
						<div class="form-group">
							<label class="control-label" >
							Date Procedure Performed
							</label>
							<div class="form-control bg-grey-1" >
							<?php echo $DOS; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="row" >
					<div class="col-sm-12" >
						Create your Amendment below :
					</div>
				</div>

				<div class="row" >
					<div class="col-sm-12" >
						<textarea name="elem_amndmnt" id="elem_amndmnt" class="form-control" rows="10" <?php echo $elem_amndmnt_readonly; ?> ><?php echo $elem_amndmnt; ?></textarea>
					</div>
				</div>
				<div class="clearfix"></div>
				<br/>
				<div class="row" >
					<div class="col-sm-2 clickable" onclick="proc_get_phy_sign()" >Physician Signature</div>
					<div id="signatures" class="col-sm-8 " >
						<span id="td_signature_applet1" class="appletCon" onclick="getAssessmentSign(1)"><?php echo $hid_pa_sign_img; ?></span>
						<input type="hidden" id="hid_pa_sign" name="hid_pa_sign" value="<?php echo $hid_pa_sign; ?>" >
						<input type="hidden" id="hid_pa_sign_by" name="hid_pa_sign_by" value="<?php echo $el_sign_by; ?>">
						<input type="hidden" id="hid_pa_final_by" name="hid_pa_final_by" value="<?php echo $el_final_by; ?>">
						<?php echo $str_sign_on_by;?>
					</div>
				</div>

				<div class="row" >
					<div class="col-sm-2"  ></div>
					<div class="col-sm-8 " >
						<span class="amnd_fin_usr"><?php echo $finalizedbyuser; ?></span>
					</div>
				</div>

			</div>
		</div>
		<div class="clearfix"></div>

	</div>
	<div class="clearfix"></div>

</div>
</div>
<footer id="module_buttons"  class="footer text-center" style="padding: 0px;background: #fff; paddding-bottom:10px;" >
    <?php if(!empty($elem_auto_final)){ ?><small><strong class="text-primary pull-left lead">Auto-Finalized</label></strong></small><?php } ?>
     <?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable) || 1==1)){?>
    <button type="button" id="elem_btnSave" name="elem_btnSave" class="btn btn-success navbar-btn <?php echo $css_btn_hidden; ?>" onClick="saveProcedure()">Done</button>
    <button type="button" name="hold_btn" id="hold_btn"  data-toggle="modal" data-target="#hpModal" class="btn btn-success navbar-btn <?php echo $css_btn_hidden; ?>" >On Hold for:</button>

    <?php if($cur_usr_type_cn==1 && !empty($elem_chart_procedures_id) && $btn_finalize!="Un-Finalize"){ ?>
    <button type="button" id="elem_btnFinalize" name="elem_btnFinalize" class="btn btn-success navbar-btn" onClick="finalizeProcedure()"><?php echo $btn_finalize;?></button>
    <?php }} ?>

    <?php if(!empty($elem_chart_procedures_id) || ($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable) || 1==1) ){ ?>
    <?php if(empty($el_final_by) && $elem_per_vo != "1" && !empty($priv_proc_amend)){ ?>
    <button type="button" id="elem_btnDoneAmdmnt" name="elem_btnDoneAmdmnt" class="btn btn-success navbar-btn hidden" onClick="saveProcedure_amnd()">Done</button>
    <?php if($elem_per_no_final != "1"){ ?>
    <button type="button" id="elem_btnFinAmdmnt" name="elem_btnFinAmdmnt" class="btn btn-success navbar-btn hidden" onClick="finalizeProcedure_amnd()">Finalize</button>
     <?php }} ?>
    <input type="hidden" id="elem_hidPrint" name="elem_hidPrint" value="0" >
    <button type="button" id="elem_btnPrint" name="elem_btnPrint" class="btn btn-success navbar-btn" onClick="print_procedure();" >Print</button>
    <?php } ?>
    <?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable) || 1==1) && !empty($elem_chart_procedures_id)){ ?>

    <button type="button" name="del_btn" id="del_btn" onClick="delProcedure('<?php echo $elem_chart_procedures_id ; ?>');" class="btn btn-danger navbar-btn <?php echo $css_btn_hidden; ?>" >Delete</button>

    <?php } ?>
    <button type="button" class="btn btn-danger navbar-btn" id="elem_btnClose" onClick="cancel_exam()">Cancel</button>
</footer>

<!-- Hold -->
<div id="hpModal" class="modal fade" role="dialog">
<div class="modal-dialog">

<div class="div_popup bg1 border modal-content" id="hold_to_phy_div" >
	<div class="page_block_heading_patch pt4 pl5 modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Select Physician for Hold</h4>
	</div>
	<div class="m10 alignCenter modal-body">
		<p>
		<?php echo $ouser->getUsersDropDown('hold_to_physician');?>
		<input type="hidden" name="hidd_hold_to_physician" id="hidd_hold_to_physician" value="<?php echo $hidd_hold_to_physician;?>">
		</p>
	</div>
	<div class="m10 alignCenter modal-footer">
		<input type="button" class="btn btn-success hold" value="Done" onClick="hold_dr_sig()">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</div>

</div>
</div>
<!-- Hold -->
</form>

<!-- Smart tags --*>
<div class="div_popup white border" id="div_smart_tags_options">
	<div class="section_header"><span class="closeBtn" onClick="$('#div_smart_tags_options').hide();"></span>Smart Tag Options</div>
	<img src="../../images/ajax-loader.gif">
</div>
<div id="hold_temp_smarttag_data" class="hide"></div>
<!-- Smart tags -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
</body>
</html>

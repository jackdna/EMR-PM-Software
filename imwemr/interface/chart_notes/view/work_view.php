<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>

<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvcss" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
    <![endif]-->

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsmain"></script>
<?php
//inline js or css
echo $str_head_data;
?>

</head>
<body>
<div id="dvloading">Loading! Please wait..</div>
<?php
	//Early Messages--
	if(!empty($dt_old_activ_cn)){
	?>
		<div id="divAlertOldChart_Outer" class="div_model_outer">
		<div id="divAlertOldChart" class="div_msg_inner" >
			<span class="glyphicon glyphicon-warning-sign"></span>&nbsp;&nbsp;You are accessing an Active Chart Note of DOS <strong class="text-danger"><?php echo $dt_old_activ_cn;?></strong><br/><br/><br/>
			<input name="elem_buttonOldChart" type="button"  class="btn btn-success"
					id="elem_buttonOldChart" onClick="close_modal_js(this)"
					value="OK"/>
		</div>
		</div>
	<?php
	}
?>


<!-- Main Form -->
<form id="frmMain" name="frmMain" action="saveCharts.php" method="post" onsubmit="return false;">

<!-- 1 row : icons -->
<div class="mainnav">
<div class=" navbar navbar-default navbar-fixed-top">
<div class="row">
	<div class="col-sm-2 form-inline" >
        <div class="input-group usrvst" id="el_visit_ig" >
			<input type="text" id="el_visit" value="<?php echo $el_visit; ?>" class="form-control" aria-label="Visit - Type" placeholder="Visit - Type" data-toggle="tooltip" data-placement="bottom" title="<?php echo $el_visit; ?>" >
			<div class="input-group-btn">
				<button type="button" class="btn btn-default " onclick="vis_add_menu(this, 'menu_visit_type', 'el_visit')"> <span class="caret"></span></button>
				<!-- data -->
				<?php //echo $data_visit_testing;?>
				<!-- data -->
			</div><!-- /btn-group -->
		</div><!-- /input-group -->
	</div>
  <div class="col-sm-1 form-inline" >
    <h4><span class="label label-info"><?php echo ($flgPtEst) ? "EST." : "New"; ?></span></h4>
  </div>

	<div class="col-sm-9">
		<div id='pin_unpin_div'  style='display:none; position:absolute;color:white;cursor:pointer; padding:0px 10px; z-index:99999;'><img src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" ></div>
		<nav role="navigation" id="nav-main" class="okayNav">
			<!-- data -->
			<ul>
            <li id="pt_at_glance"><a href="#" title="Patient at a glance" <?php if(defined('PAG_HOVER_STOP') && constant('PAG_HOVER_STOP')=="1"){ ?> onMouseOver="top.tb_popup(this,'1'); " onMouseOut="top.tb_popup(this,'0'); " <?php }?> onClick="top.icon_popups('pt_at_glance');" ><img id="img_pag" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" style="pointer-events:none;" alt="" title="Patient at a glance"  /></a>  </li>
			<li id="test_manager" class="<?php echo $ar_icon_st["test_manager"];?>"><a href="#" onClick="top.icon_popups('test_manager');"><img id="img_tmngr" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Test Manager"/></a> </li>
			<?php if(!empty($GLOBALS['show_symphony_wv'])){?>
			<li id="ims_symph"><a href="#" onClick="top.popup_win('http://192.168.100.220/symphony/emr.aspx?MRN=<?php echo $patient_id;?>');"><img id="img_ims_symph" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Symphony"/></a> </li>
			<?php }?>
			<li id="Erx"><a href="#" onClick="top.popup_win('../chart_notes/erx_patient_selection.php?loadmodule=prescription');"><img id="img_erx" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Erx"/></a> </li>
			<li id="eRx_prescription"><a href="#" onClick="top.download_erx_data();"><img id="img_erxp" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="eRx Prescription"/></a> </li>
			<li id="general_health" class="<?php echo $ar_icon_st["general_health"];?>" onMouseOver="top.showMedReview('1');" onMouseOut="top.showMedReview('0');" ><a href="#" title="Patient General Health"  onClick="top.tb_popup(this);"><img id="img_gnhlth" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="General Health"  /></a> </li>
			<li id="smart_charting"><a href="#" onClick="top.tb_popup(this);" title="Smart Charting"><img id="img_smrtc" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Smart Charting"/></a> </li>
			<li id="patient_communication" class="<?php echo $ar_icon_st["patient_communication"];?>"><a href="#" onClick="top.tb_popup(this);" title="Patient Communication"><img id="img_pcom" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Pt. Communication"/></a> </li>
			<li id="patient_instruction_documents" class="<?php echo $ar_icon_st["patient_instruction_documents"];?>"><a href="#" onClick="top.tb_popup(this);" title="Patient Instruction Documents"><img id="img_ptidoc" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Patient Instruction Documents"/></a> </li>
			<li id="allergies"  class="<?php echo $ar_icon_st["allergies"];?>"><a href="#" title="Allergies" ondblclick="top.tb_popup(this);" onclick="top.tb_popup(this,1);"><img id="img_alrg" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Allergies"/></a> </li>
			<li id="Surgeries" class="<?php echo $ar_icon_st["Surgeries"];?>"><a href="#" title="Surgeries" ondblclick="top.tb_popup(this);" onclick="top.tb_popup(this,1);"><img id="img_srgr" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Surgeries"/></a> </li>
			<li id="glaucoma_flow_sheet" class="<?php echo $ar_icon_st["glaucoma_flow_sheet"];?>"><a href="#" onclick="top.tb_popup(this);" title="Glaucoma Flow sheet"><img id="img_gfs" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Glaucoma Flow sheet"/></a> </li>
			<li id="patient_refractive_sheet" class="<?php echo $ar_icon_st["patient_refractive_sheet"];?>"><a href="#" onclick="top.tb_popup(this);" title="Patient Refractive Sheet"><img id="img_prs" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Patient Refractive Sheet"/></a> </li>
			<li id="procedure_flow_sheet" class="<?php echo $ar_icon_st["procedure_flow_sheet"];?>"><a href="#" onclick="top.tb_popup(this);" title="Procedure Flow Sheet"><img id="img_pfs" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" title="Procedure Flow Sheet"/></a> </li>
			<li id="retinal_flow_sheet" class="<?php echo $ar_icon_st["retinal_flow_sheet"];?>"><a href="#" onclick="top.tb_popup(this);" title="Retinal Flow Sheet"><img id="img_rfs" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" title="Retinal Flow Sheet"/></a> </li>
			<li id="consult_letter" class="<?php echo $ar_icon_st["consult_letter"];?>"><a href="javascript:top.core_set_pt_session(top.fmain,'<?php echo $_SESSION['patient']; ?>','../chart_notes/consult_letter_page.php?doc_name=view_consult');"  title="Consult Letters"><img id="img_cnstltr" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Consult Letters"/></a></li>
			<li id="print"><a href="#" onClick="top.icon_popups('print_pt_summary');"><img id="img_prnt" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Print"/></a> </li>
			<!--<li><a href="#" onClick="top.tb_popup(this);" title="Patient Alerts"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/patient_alerts.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Pt. Alerts"/></a></li>-->
			<!--<li><a href="#"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/micon2.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Patient Medical History " /> </a></li>-->
			<!--<li><a href="#"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/micon6.png" alt="" data-toggle="tooltip" data-placement="bottom" title=" Flow sheets "/></a></li>-->
			<li id="MUR_checklist"><a href="#" onclick="top.tb_popup(this,1);" title="MUR Checklist"><img id="img_murc" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="MUR Checklist"/></a> </li>
			<!-- <li id="toric_calculator"><a href="#" onclick="top.tb_popup(this,0);" title="Toric Calculator"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/Toric-Calculatoricon.png" alt="" title="Toric Calculator"/></a> </li> -->
            <?php if($_SESSION['sess_privileges']['priv_acchx']=="1"){ ?>
                <li id="History_of_CPT_Services"><a href="#" onclick="top.tb_popup(this);" title="History of CPT Services"><img id="img_hocs" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="History of CPT Services"/></a> </li>
            <?php } ?>
			<li id="patient_chart_search"><a href="#" onclick="top.tb_popup(this);" title="Patient Chart Search"><img id="img_pcs" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Patient Chart Search"/></a> </li>
			<li id="patient_providers"><a href="#" onclick="top.tb_popup(this);" title="Patient Providers"><img id="img_ppro" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Patient Providers"/></a> </li>

			<li id="confidential_text"><a href="#" title="Confidential Text" onclick="top.tb_popup(this);"><img id="img_contxt" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Confidential Text"/></a> </li>
			<li id="primary_referrals"><a href="#" title="Primary Referrals" onclick="top.tb_popup(this);"><img id="img_prmrf" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Primary Referrals"/></a></li>

			<?php
				if(constant('DEFAULT_PRODUCT') == 'imwemr'){
					$isMpay = verify_payment_method("MPAY");
					if($isMpay){
			?>
      		<li id="mapy"><a href="#" onclick="top.tb_popup(this);" title="Mpay"><img id="img_mpy" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Mpay"/></a></li>
			<?php	}
				} ?>

			<li id="btn_in_pt_data" onClick="show_inpatient_data();"><a href="javascript:void(0);" id="btnInpatient" >In Patient Data</a></li>
			<li id="btn_pt_payer" onClick="show_pt_payer();"><a href="javascript:void(0);" id="btnPatientPayer" onClick="top.fmain.ptPayerModal('get');">Patient Payer</a></li>

			</ul>
			<!-- data -->
		</nav>
		<span id="btn_close_pt_wv" class="glyphicon glyphicon-remove pull-right" title="Close Patient" onclick="top.clean_patient_session();" ></span>
	</div>
</div>


</div>
</div>
<div class="clearfix"></div>

<?php if(empty($isMemo)){ ?>
<!-- 2 row : CCX -->
<div class="container-fluid">

	<input type="hidden" id="phth_pros" name="elem_phth_pros" value="<?php echo $elem_phth_pros; ?>" />
	<input type="hidden" id="elem_curset_phth_pros" name="elem_curset_phth_pros" value="<?php echo $elem_curset_phth_pros; ?>" />
	<input type="hidden" id="is_od_os" name="elem_eyePrPh" value="<?php echo $elem_eyePrPh; ?>" />


	<div id="infoInsData" class="hidden">
		<!-- data -->
		<label id="vis_ins" ><?php echo $elem_insuranceCaseName_id_alt; ?></label>
		<!-- data -->
	</div>

	<!--
	<div id="ad_dir">
	</div>
	-->

	<div class="clearfix"></div>
	<div class="historytable" style="margin-top:10px;">
		<div class="row ">
			<div class="col-lg-2 col-lg-2-lf col-md-2 col-sm-2  ">
				<div class="lftpannel">
					<h2 id="rvsBtn">HPI</h2>
					<ul class="nav nav-tabs" > <!--role="tablist"-->
						<!-- <li role="presentation" class="active"><a href="#Vision" onclick="top.icon_popups('contact_lens_worksheet');" style="cursor:pointer;" aria-controls="Vision" role="tab" data-toggle="tab">Vision</a></li> -->
					    <li role="presentation" ><a href="javascript:void(0);" onclick="loadRVS('','tabVision_Problem')"  class="hvr-sweep-to-right">Vision Problem </a></li><!--aria-controls="vision" role="tab" data-toggle="tab"-->
					    <li role="presentation"><a href="javascript:void(0);" onclick="loadRVS('','tabIrritation')" class="hvr-sweep-to-right"> Irritation</a></li><!--aria-controls="irritation" role="tab" data-toggle="tab"-->
					    <li role="presentation"><a href="javascript:void(0);" onclick="loadRVS('','tabPost_Segment')" class="hvr-sweep-to-right">Post Segment </a></li><!--aria-controls="postsegment" role="tab" data-toggle="tab"-->
					    <li role="presentation"><a href="javascript:void(0);" onclick="loadRVS('','tabNeuro')" class="hvr-sweep-to-right">Neuro</a></li><!--aria-controls="neuro" role="tab" data-toggle="tab"-->
					    <li role="presentation"><a href="javascript:void(0);" onclick="loadRVS('','tabrvs_FollowUp')"  class="hvr-sweep-to-right">Follow-Up</a></li><!--aria-controls="followup" role="tab" data-toggle="tab"-->
					  </ul>
				</div>
			</div>
			<div class="col-lg-10 col-lg-10-rt col-md-10 col-sm-10">
			<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="vision">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-6">
								<div class="whitebox">
									<div class="histryheader">
										<div class="row">
										<div class="col-lg-1 col-sm-1" data-toggle="tooltip" title="Chief Complaint" ><h2>CC</h2></div>
										<div class="col-lg-4 col-sm-4">
											<!--<input type="hidden" id="elem_neuroPsych" name="elem_neuroPsych" value="<?php //echo $elem_neuroPsych; ?>"  >-->
											<div class="input-group" id="el_chartneuro_ig" >
												<input type="text" id="elem_neuroPsych" name="elem_neuroPsych" class="form-control" aria-label="Neuro/Psych" data-toggle="tooltip" title="Neuro/Psych" placeholder="Neuro/Psych" readonly value="<?php echo $elem_neuroPsych; ?>">
												<div class="input-group-btn menu">
													<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span></button>
													<ul class="dropdown-menu dropdown-menu-right">
														<li class="dropdown-header">NEURO/PSYCH</li>
														<li role="separator" class="divider"></li>
														<?php echo $data_neuro_htm;?>
													</ul>
												</div>
											</div>

										</div>
										<div class="col-lg-6 col-sm-6 ccform form-inline">

										<div class="form-group">
											<input type="hidden" id="elem_pro_id" name="elem_pro_id" value="<?php echo ($elem_pro_id);?>">
											<input type="text" class="form-control" id="elem_pro_name" name="elem_pro_name" value="<?php echo $elem_pro_name; ?>" readonly>
										</div>
										<div class="form-group">
										<!-- Co signer -->
											<input type="hidden" id="elem_cosigner_id" name="elem_cosigner_id" value="<?php echo ($elem_cosigner_id);?>">
											<input type="text" class="form-control" id="elem_cosigner_name" name="elem_cosigner_name" value="<?php echo $elem_cosigner_name; ?>" readonly>
										<!-- Co signer -->
										</div>
										</div>
										<div class="col-lg-1 col-sm-1">
											<strong>
											<a href="javascript:void(0);" id="cchx_link" data-toggle="popover" data-trigger="focus"  class="clickable" onclick="showcCChxpop()">
											<h2 class="clickable">HX</h2>
											</a>
											</strong>
										</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="scroll-content tablcont">
										<div class="cheifcomp" >
										<textarea id="elem_ccompliant" name="elem_ccompliant"
												class=" scrollstopper"
												onChange="setUserName('elem_ccHx')" onKeyPress="return mandatory_ovr('elem_ccompliant');"
												 ><?php echo $elem_ccompliant;?></textarea>

										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-6">
							<div class="whitebox patienthist">
								<div class="histryheader form-inline" >
									<div class="row">
										<div class="col-sm-5"><h2>Patient History</h2></div>
										<div class="col-sm-7 ">
											<table class="table borderless" id="tbl_domi" >
											<tr><td>
											<div class="form-group">
											<label for="elem_dominantCc" >Dominant</label>
											<select id="elem_dominantCc" name="elem_dominantCc" class="form-control minimal">
												<option value=""></option>
												<option value="OU" <?php if($elem_dominantCc=="OU")echo "selected";?>>OU</option>
												<option value="OD" <?php if($elem_dominantCc=="OD")echo "selected";?>>OD</option>
												<option value="OS" <?php if($elem_dominantCc=="OS")echo "selected";?>>OS</option>
											</select>
											</div>
											</td><td>
											<div class="form-group">
											<label for="elem_eyeColorCc">Color</label>
											<input type="text" id="elem_eyeColorCc" name="elem_eyeColorCc" value="<?php echo $elem_eyeColorCc;?>"  class="form-control"  >
											</div>
											</td></tr>
											</table>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="scroll-content tablcont">
									<div class="cheifcomp">
									<textarea id="elem_chk" name="elem_ccHx"
										class=" scrollstopper <?php echo $elem_ccHx_css;?>"  <?php echo $elem_ccHx_js;?>
										onChange="setUserName('elem_ccHx')" onKeyPress="return mandatory_ovr('elem_ccHx');"
										 ><?php echo $elem_ccHx;?></textarea>
										<?php echo $elem_ccHx_htm;?>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							</div>
							<div class="col-lg-4 col-md-12 col-sm-12">
							<div class="whitebox">
							<div class="histryheader">
								<div class="row">
									<div class="col-sm-5" onclick="openMedHX('medication_grid','800');" ><h2 class="clickable">OCULAR MEDICATION</h2></div>
									<div class="col-sm-3 " id="ad_dir" ><?php echo $strPtAdvDir;?></div>
									<div class="col-sm-4 ">
										<div id="dd_pain" data-toggle="tooltip" title="Pain Level" >
											<!-- data -->
											<select name="elem_painCc" id="elem_painCc" data-toggle="tooltip" title="Pain Level" class="form-control minimal">
												<option value="">-Pain Level-</option>
												<option value="Mild" <?php if($elem_painCc=="Mild") echo "selected" ; ?>>Pain - Mild</option>
												<option value="Moderate" <?php if($elem_painCc=="Moderate") echo "selected" ; ?>>Pain - Moderate</option>
												<option value="Severe" <?php if($elem_painCc=="Severe") echo "selected" ; ?>>Pain - Severe</option>
												<?php
												for($i=0;$i<=10;$i++){
													$tmp = "Scale ".$i;
													$sel = ($elem_painCc==$tmp) ? "selected" : "";
													echo "<option value=\"".$tmp."\" ".$sel.">Pain - ".$tmp."</option>";
												}
												?>
											</select>
											<!-- data -->
										</div>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="scroll-content mCustomScrollbar tablcont">
							<div class="table-responsive respotable ocu_med_grid" id="divOcMeds">
								<!--data-->
								<?php echo $datahtm_ocu_meds;  ?>
								<!--data-->
							</div>
							</div>
							<div class="clearfix"></div>
						</div>
						</div>
						</div>
					</div>
				</div>
		<!-- Tab panes -->
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php } // memo ?>

<?php
if(!empty($flg_temp_vision)){
?>
<!-- 3 row : vision -->
<div class="workvision">
	<div class="row">
		 <div id="equalheight">
		 <div class="col-lg-2 col-lg-2-lf col-md-2 col-sm-2  examleftpan">
			<ul class="nav" > <!-- class="nav nav-tabs" role="tablist" -->
				<li role="presentation" >Vision<button type="button" class="btn btn-primary btn-xs pull-right " id="btn_rst_vis" onclick="setResetValues('vis');">Reset</button></li>
				<li role="presentation" class="active"><a href="#Distance" aria-controls="Distance" data-toggle="collapse" aria-expanded="false"  >Vis. Acuities<span class="glyphicon pull-right"></span></a></li>
				<li role="presentation"><a href="#PC" aria-controls="PC" data-toggle="collapse" aria-expanded="false"  >PC<span class="glyphicon pull-right"></span></a></li>
				<li role="presentation"><a href="#MR" aria-controls="MR" data-toggle="collapse" aria-expanded="false"  >MR<span class="glyphicon pull-right"></span></a></li>
				<?php if(empty($ctmpLasik)){?>
				<li role="presentation"><a href="#LASIK" aria-controls="LASIK" data-toggle="collapse" aria-expanded="false"  >LASIK<span class="glyphicon pull-right"></span></a></li>
				<?php } ?>



			    <!--<li role="presentation"><a href="#CVF" aria-controls="CVF" >CVF</a></li>
			    <li role="presentation"><a href="#AmslerGrid" aria-controls="AmslerGrid" >Amsler Grid</a></li>-->
			    <li role="presentation"><a href="#OtherVisionExams" aria-controls="OtherVisionExams" data-toggle="collapse" aria-expanded="false"  >Other<span class="glyphicon pull-right"></span></a></li>
			    <?php if($show_contactLens){ ?>
			    <li role="presentation"><a href="#ContactLens" aria-controls="ContactLens" data-toggle="collapse" aria-expanded="false" >Contact Lens<span class="glyphicon pull-right"></span></a></li>
			    <?php } ?>
			<!--
			    <li role="presentation"><a href="#Cp" aria-controls="Cp" role="tab" data-toggle="tab">Color Plates</a></li>
			    <li role="presentation"><a href="#Stereopsis" aria-controls="Stereopsis" role="tab" data-toggle="tab">Stereopsis</a></li>
			    <li role="presentation"><a href="#Retinoscopy" aria-controls="Retinoscopy" role="tab" data-toggle="tab">Retinoscopy</a></li>
			    <li role="presentation"><a href="#Cycloplegic" aria-controls="Cycloplegic" role="tab" data-toggle="tab">Cycloplegic Retinoscopy</a></li>
			    <li role="presentation"><a href="#Exophthalmometer" aria-controls="Exophthalmometer" role="tab" data-toggle="tab">Exophthalmometer</a></li>
			-->
			  </ul>

			  <!-- CVF -->
			  <div id="cvf" class="<?php echo $ctmpCvf;?>" ><?php echo $data_cvf_section;?></div>
			  <!-- CVF -->

			 <!-- Amsler grid -->
			 <div id="amsgrid" class="<?php echo $ctmpAg;?>" ><?php echo $data_amsler_section;?></div>
			 <!-- Amsler grid -->

		 </div>
		<div class="col-lg-10 col-lg-10-rt col-md-10 col-sm-10 " >
			<!--<div class="reloadpos hidden"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/reload.png" alt=""/></div>-->
			<div class="clearfix"></div>
			<div > <!-- class="tab-content" -->
			   <?php   echo $data_vision_section;	/*include(dirname(__FILE__).'/vision.php');*/ ?>
			    <!--<div role="tabpanel" class="tab-pane" id="CVF"><?php   //echo $data_cvf_section; ?></div>
			    <div role="tabpanel" class="tab-pane" id="AmslerGrid"><?php   //echo $data_amsler_section; ?></div>-->
			<?php if($show_contactLens){ ?>
				<div id="Vision">
                    <!--<div >-->
                        <?php include(dirname(__FILE__).'/../cl_block.php'); ?>
                    <!--</div>-->
				</div>
			<?php } ?>
                 <!-- ro1le="tabpanel" clas1s="tab-pane" -->
			    <!--
			    <div role="tabpanel" class="tab-pane" id="RoutineExam">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Medical">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Abbasi">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Arshia">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Cp">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Stereopsis">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Retinoscopy">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Cycloplegic">rtewrwerer</div>
			    <div role="tabpanel" class="tab-pane" id="Exophthalmometer">rtewrwerer</div>
			    -->
			</div>
		</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php } // vision ?>

<?php
if(!empty($isMemo)){
?>

<!-- Memo -->
<div id="idMemo" class="whitebox table-responsive worksheet">
<section id="sec_memo">
<input type="hidden" id="memo" name="memo" value="<?php echo $isMemo; ?>">
<?php echo $datamemo;?>
</section>
</div>
<!-- Memo -->

<?php
//Objective Note
}else if(!empty($flg_obj_note)){

?>
<div class="panel panel-success pnl_obj_note">
  <div class="panel-heading"><h2>Objective Note</h2></div>
  <div class="panel-body"><textarea name="elem_objNotes" class="form-control" rows="10"><?php echo $elem_objNotes; ?></textarea></div>
</div>
<div class="clearfix"></div>
<?php

//end Objective Note
?>

<?php
}else{//exm summary
?>

<!-- 4 row : Exam summary -->
<div id="idChartSumry" class="whitebox table-responsive worksheet">
<section id="summarysheet">
<!--header -->
<div id="sec_wv_sum">
	<table class="table table-bordered table-hover table-striped">
	<tr>
	<td class="leftpanel edone tallft" >
	<!--<span class="reloadicon cur_hnd"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/refreshicon.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Reset" onClick="setResetValues('All');" /></span>-->
	<button type="button" class="btn btn-primary" id="btn_reset_chart" onClick="setResetValues('All');" onmouseover="showWnlOpts(1,3,'')" onmouseout="showWnlOpts(0,3)">Reset</button>
	<input class="btn wnl" type="button" value="WNL" id="elem_btnWnlMas" onClick="setWnlValues_all();" onmouseover="showWnlOpts(1,1,'Mas')" onmouseout="showWnlOpts(0,1)" >
	<input class="btn nc" type="button" value="NC" id="elem_btnNoChangeMas" onClick="autoSaveNoChange_all();" onmouseover="showWnlOpts(1,2,'Mas')" onmouseout="showWnlOpts(0,2)" >
	</td>
	<td class="odbar ">OD</td>
	<td class="osbar ">OS</td>
	<td class="drawing edesc"><a onclick="openPW('drawingpane')" data-toggle="tooltip" title="Drawings">Drawings</a></td>
	</tr>
	</table>
</div>
<!--header -->

<!--PUPIL -->
<div id="pupil" >
</div>
<!--PUPIL -->

<!--EOM -->
<div id="eom" >
</div>
<!--EOM -->

<!--EXTERNAL -->
<div id="external" >
</div>
<!--EXTERNAL -->

<!--LA -->
<div id="la" >
</div>
<!--LA -->

<!--IOP/GON -->
<div id="iop_gon" >
</div>
<!--IOP/GON -->

<!--SLE -->
<div id="sle" >
</div>
<!--SLE -->

<!--FUNDUS -->
<div id="fundus_exam" >
</div>
<!--FUNDUS -->

<!--Refractive Surgery -->
<div id="ref_surg" >
</div>
<!--Refractive Surgery -->

<!--VF/OCT - GL -->
<div id="vf_oct_gl" >
</div>
<!--VF/OCT - GL -->



</section>

</div>
<div class="clearfix"></div>

<?php
} //exm summary
?>

<!-- 5 row Assess plans-->
<div id="assessplan" class="assismentplan">
	<div class="assismenthad ">
		<div class="row">
			<div class="col-lg-2 col-md-3 col-sm-12 "><h2>Tests History </h2></div>
			<div id="imp_ex_done" class="col-lg-10 col-md-9 col-sm-12 text-center"></div>
			<!--<div class="col-lg-2 col-md-3 col-sm-4  text-right"></div>-->
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="hxbar">
		<ul>
		<li><h2 >Assessment</h2><small class="clickable" onclick="chkSelection('1', this);">All NE</small></li>
		<li class="assesment"><input type="hidden" id="hid_icd10" name="hid_icd10" value="1"/>
			<!--<input class="btn versbut" type="button" value="ICD-10">-->
			<input type="hidden" id="elem_rxhandwritten" name="elem_rxhandwritten" value="<?php echo $elem_rxhandwritten; ?>">
			<input type="hidden" id="elem_labhandwritten" name="elem_labhandwritten" value="<?php echo $elem_labhandwritten; ?>">
			<input type="hidden" id="elem_radhandwritten" name="elem_radhandwritten" value="<?php echo $elem_radhandwritten; ?>">
			<input id="lblAssessHx" class="btn hx <?php echo $cls_hx_button; ?>" type="button" value="Hx" onclick="showAsHx(1)">
			<span id="spnRxHxRvd" data-toggle="tooltip" title="<?php echo $titleRxHxRvd; ?>" class="glyphicon glyphicon-flag pull-right <?php echo ($elem_resiHxReviewd==1) ? " active " : "" ;?>"></span>
		</li>
		<li class="form-inline visfunct">
			<div class="form-group">
				<label for="func_status"><strong>Visual Function - 14</strong></label>
				<select class="form-control " name="func_status" id="func_status">
				<?php
					foreach($arr_vis_func as $key_vis_func=>$val_vis_func){
						$tmp = ($func_status == $val_vis_func) ? "selected" : "";
						echo "<option value=\"".$val_vis_func."\" ".$tmp." >".$val_vis_func."</option>";
					}
				?>
				</select>
			</div>
		</li>
		<li class="dxseg"><h2>Dx</h2></li>
		<li class="planseg"><h2>Plan</h2></li>
		<li class="prevplan"><a href="javascript:void(0);" id="linkPrevPlan" onMouseOver="showPrevPlan(1)" onMouseOut="showPrevPlan(2)" onClick="restorePlan_all()" class="clickable">Prev. Plans</a></li>

		<li class="ordrsetbtn text-right"><input class="btn btn-primary orderbut ap_sub_title " type="button" value="Orders / Order Sets" onclick="top.fmain.funApPlan();" ></li>
		<?php
			//The below 2 variables is extracted from ChartAP::getFormInfo() in the ../workview.php
		?>
		<li ><input class="btn btn-primary orderbut ap_sub_title" type="button" value="Goals & HC" onclick="top.fmain.pt_g_hc(this);" ></li>
		<li ><input class="btn btn-primary orderbut ap_sub_title" type="button" value="PT Health" onclick="top.fmain.pt_health(this);" ></li>
		</ul>
	</div>
	<div class="clearfix"></div>


	<?php
	for($i=0,$j=1;$j<=$lenAssess;$i++,$j++){

		//Assessment --
		$zNE = "no_change_".$j;
		$tNE = $$zNE;

		$zRes = "elem_resolve".$j;
		$tRes = $$zRes;

		$vCls ="";
		if(!empty($tNE)){ $vCls = "apClr";}
		else if(!empty($tRes)){ $vCls = "apClrRes";}

		$zAssess = "elem_assessment".$j;
		$zDxcode = "elem_assessment_dxcode".$j;
		$zAssess_typeAhead = "elem_assessment_typeAhead".$j;
		$vAssess = $$zAssess;
		$vDxcode = $$zDxcode;
		$dx_class = "";
		if(!empty($vDxcode) && strpos($vDxcode,"-")!==false){
			$dx_class = 'mandatory';
		}

		//Ou
		$zOu = "elem_apOu".$j;
		$tOu = $$zOu;

		//Od
		$zOd = "elem_apOd".$j;
		$tOd = $$zOd;

		//Os
		$zOs = "elem_apOs".$j;
		$tOs = $$zOs;

		//Continue Meds
		$zConMeds = "elem_apConMeds".$j;
		$vConMeds = $$zConMeds;

		//Problist id
		$zProbId = "elem_problist_id_assess".$j;
		$vProbId = $$zProbId;

		//Plan --
		$zPlan = "elem_plan".$j;
		$vPlan = $$zPlan;

		//CFPlan
		$elem_CFPlan = "elem_CFPlan".$j;

		//pt_ap_id
		$zpt_ap_id = "el_pt_ap_id".$j;
		$vpt_ap_id = $$zpt_ap_id;


		//Archive Assessment
		if(!empty($arTmpRecArc["div"]["as"][$j])){
			$elem_assessment_htm = $arTmpRecArc["div"]["as"][$j];
			$elem_assessment_js = $arTmpRecArc["js"]["as"][$j];
			$elem_assessment_css = $arTmpRecArc["css"]["as"][$j];
			if(!empty($arTmpRecArc["curText"]["as"][$j])){$vAssess = $arTmpRecArc["curText"]["as"][$j];}
		}else{
			$elem_assessment_htm=$elem_assessment_js=$elem_assessment_css="";
		}
		//Archive Assessment
		//Archive Plan --
		if(!empty($arTmpRecArc["div"]["pl"][$j])){
			$elem_plan_htm = $arTmpRecArc["div"]["pl"][$j];
			$elem_plan_js = $arTmpRecArc["js"]["pl"][$j];
			$elem_plan_css = $arTmpRecArc["css"]["pl"][$j];
			if(!empty($arTmpRecArc["curText"]["pl"][$j])){	$vPlan = $arTmpRecArc["curText"]["pl"][$j]; }
		}else{
			$elem_plan_htm = $elem_plan_js = $elem_plan_css = "";
		}
		//Archive Plan --

		//show site drop down
		$zelem_incomplete_dxcode = "elem_incomplete_dxcode".$j;
		$show_amst_site=$$zelem_incomplete_dxcode;

		//show dx drop down
		$zelem_asmt_dxid = "elem_asmt_dxid".$j;
		$elem_asmt_dxid=$$zelem_asmt_dxid;


	?>
	<!-- Plan -->
	<!--<div class="planalt">-->
	<div class="planbox">
		<?php // This section is displayed none and it is not in use. this is kept here for js function. ?>
		<div class="row planchoose ap_eye_removed" >
			<div class="col-lg-3 col-md-3 col-sm-5 ">
				<ul>
				<li class="ouc"><input name="elem_apOu[]" id="elem_apOu<?php echo $j;?>" type="checkbox" value="<?php echo $j;?>" onclick="setApEye(this,0)"  onfocus="setApEye(this,1)" <?php echo $tOu;?> /><label for="elem_apOu<?php echo $j;?>"></label></li>
				<li class="odc"><input name="elem_apOd[]" id="elem_apOd<?php echo $j;?>" type="checkbox" value="<?php echo $j;?>" onclick="setApEye(this,0)"  onfocus="setApEye(this,1)" <?php echo $tOd;?> /><label for="elem_apOd<?php echo $j;?>"></label></li>
				<li class="osc"><input name="elem_apOs[]" id="elem_apOs<?php echo $j;?>" type="checkbox" value="<?php echo $j;?>" onclick="setApEye(this,0)"  onfocus="setApEye(this,1)"  <?php echo $tOs;?> /> <label for="elem_apOs<?php echo $j;?>"></label></li>
				</ul>
			</div>
		</div>
		<div class="clearfix ap_eye_removed"></div>
		<?php // End ?>

		<div class="row planform" >
			<div class="col-lg-1 col-md-1 col-sm-1 col-lg-1-sn col-md-1-sn col-sm-1-sn " > <!--  col-sm-1 plansn -->
				<div class="planchoose">
				<div class="counter ap_num" onClick="ap_adjt(<?php echo $j;?>, this);" ><?php echo $j;?></div>
				</div>
			</div>
			<div class="col-lg-1 col-md-1 col-sm-1 col-lg-1-opt col-md-1-opt col-sm-1-opt " >	<!--  col-sm-1 planoptin -->
				<div class="planchoose">
				<ul class="ul_ne">
				<li class="ne">
					<div class="checkboxO">
					<input type="checkbox" name="elem_apnc[]" id="no_change_<?php echo $j;?>" value="<?php echo $j;?>" onClick="chkSelection('<?php echo $j;?>', this);" <?php echo $tNE; ?> />
					<label for="no_change_<?php echo $j;?>">NE</label>
					</div>
				</li>
				<li class="res">
					<div class="checkboxO">
					<input name="elem_apres[]" id="elem_resolve<?php echo $j;?>" type="checkbox" value="<?php echo $j;?>" onClick="chkSelection('<?php echo $j;?>', this);" <?php echo $tRes; ?> />
					<label for="elem_resolve<?php echo $j;?>">RES</label>
					</div>
				</li>
				</ul>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-7 col-lg-4-as col-md-4-as col-sm-7-as  "> <!--  col-sm-7 -->
				<div class="row">
					<div class="<?php echo !empty($show_amst_site) ? "col-sm-11" : "col-sm-12" ; ?>">
			<textarea name="elem_assessment[]" rows="3" id="elem_assessment<?php echo $j;?>" onkeyup="setTaPlanHgt(<?php echo $j;?>);"  onchange="checkConsolePlan(this,'<?php echo $j;?>');getForthAssess();" class="form-control  <?php echo $vCls." ".$elem_assessment_css; ?>" onclick="showICD10CodeModifier(this)" tabindex="1" <?php echo $elem_assessment_js; ?>   ><?php echo $vAssess; ?></textarea>
			<?php echo $elem_assessment_htm; ?>
					</div>
					<div class="col-sm-1 <?php echo !empty($show_amst_site) ? "" : "hidden" ; ?> ">
			<select name="el_amst_site[]" id="el_amst_site<?php echo $j;?>" class="form-control " onchange="setApEye(this,2)"><option></option>
				<option value="OU" <?php if(!empty($tOu)){ echo "selected"; }?> >OU</option>
				<option value="OD" <?php if(!empty($tOd)){ echo "selected"; }?> >OD</option>
				<option value="OS" <?php if(!empty($tOs)){ echo "selected"; }?> >OS</option>
			</select>
					</div>
				</div>
			</div>
			<div class="col-lg-1 col-md-1 col-sm-2 col-lg-1-dx col-md-1-dx col-sm-2-dx "><!--  col-sm-2 -->
			<textarea  class=" form-control dx <?php echo $vCls." ".$dx_class;?>" rows="3"  onkeyup="setTaPlanHgt(<?php echo $j;?>);" name="elem_assessment_dxcode[]" id="elem_assessment_dxcode<?php echo $j;?>" tabindex="5" onblur="checkDXCodesChart(this);setTaPlanHgt(<?php echo $j;?>);getForthAssess();" onfocus="setCursorAtEnd(this);" data-dxid="<?php echo $elem_asmt_dxid; ?>"   ><?php echo $vDxcode;?></textarea>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 col-lg-4-pl col-md-4-pl col-sm-12-pl "><!--  col-sm-12 -->
			<textarea  name="elem_plan[]" rows="3" id="elem_plan<?php echo $j;?>" onkeyup="setTaPlanHgt(<?php echo $j;?>);" onchange="setTaPlanHgt(<?php echo $j;?>);"  data-elem_CFPlan="<?php echo $$elem_CFPlan; ?>" class="form-control plantext <?php echo $vCls." ".$elem_plan_css; ?>" tabindex="5" <?php echo $elem_plan_js; ?>  ><?php echo $vPlan; ?></textarea>
			<?php echo $elem_plan_htm; ?>
			</div>
			<div class="col-lg-1 col-md-1 col-sm-1 col-lg-1-x col-md-1-x col-sm-1-x "><!--  col-sm-1 -->
				<!--<div class="planchoose"><div class="closebtn">-->
				<!--<img src="<?php echo $GLOBALS['webroot'];?>/library/images/closerd.png" alt="Delete" onclick="reset_assessment('ap_elem_assessment<?php echo $j;?>');" />-->
				<span class="glyphicon glyphicon-remove" alt="Delete" onclick="reset_assessment('ap_elem_assessment<?php echo $j;?>');"></span>
				<!--</div></div>-->
			</div>
		</div>
	<input name="elem_apConMeds[]" id="elem_apConMeds<?php echo $j;?>" type="hidden" value="<?php echo $vConMeds;?>" />
	<input name="elem_problist_id_assess[]" id="elem_problist_id_assess<?php echo $j;?>" type="hidden" value="<?php echo $vProbId;?>" />
	<input name="elem_assessment_typeAhead<?php echo $j;?>" id="elem_assessment_typeAhead<?php echo $j;?>"  type="hidden" value="<?php echo $vConMeds;?>" />
	<input name="el_pt_ap_id[]" id="el_pt_ap_id<?php echo $j;?>" type="hidden" value="<?php echo $vpt_ap_id;?>" />
	</div>
	<div class="clearfix"></div>
	<!-- Plan -->
	<!--</div>-->
	<?php
		}
	?>

	<div class="addbut hidden"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="add ap box" onclick="getForthAssess(1);"/></div>

</div>
<div class="clearfix"></div>

<!-- 6 row : lower sections -->
<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-12">

	<?php if($str_dss){ ?>
		<div class="whitebox folwbox" id="dssBox">
			<div class="row">
				<div class="col-lg-6 col-md-12 col-sm-12">
					<h2>Search DSS TIU Title</h2>
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12">
					<select name="dssTiuTitle" id="dssTiuTitle" class="form-control minimal"></select>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="whitebox folwbox">
		<div class="followup">
			<h2 class="clickable" onclick="op_fu_sec()">Follow Up</h2>
			<ul>
			<li><a href="javascript:void(0);" id="btnCnslt"
				onClick="top.fmain.showOtherForms('<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/onload_wv.php?elem_action=Consult_letters','Template','1400','750');"
				class="btn btn-primary" >Consult</a></li>
			<!--<li><a href="javascript:void(0);" id="btnOpNt"
				onClick="top.fmain.showOtherForms('<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/onload_wv.php?elem_action=Procedures','Procedures','1275','750','0');"  >Procedures</a></li>-->
			<li><a href="javascript:void(0);" id="btnPtIns"
				onclick="top.tb_popup(this);" title="Patient Instruction Documents"
				class="btn btn-primary" >Pt Instructions</a></li>
			<li><a href="javascript:void(0);" id="btnEpst"
				onClick="top.fmain.epostpopTest()" class="btn btn-primary" >ePost It</a></li>
			<?php if($user_type_cn==1 || $_SESSION["logged_user_type"]==6){ ?>
			<li><a href="javascript:void(0);" id="btnPrgs"
				onClick="top.fmain.cn_progess_notes()" class="btn btn-primary" >Pr. Note</a></li>
			<?php } ?>
			<li><a href="javascript:void(0);" id="btntodo"
				onclick="top.popup_win('<?php echo $GLOBALS['webroot'] ?>/interface/scheduler/physician_scheduler/patient_notes.php?cur_patid=<?php echo $patient_id;?>','PatientNotesWindow','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=700,height=400,left=10,top=0');"
				class="btn btn-primary" >To Do</a></li>
			</ul>
			<div class="clearfix"></div>
		</div>
		<!--
		<div class="clearfix"></div>
		<div class="linkmor"><label id="ptIns" title="<?php echo ($elem_insuranceCaseName_id);?>"><?php echo (strlen($elem_insuranceCaseName_id)>30) ? substr($elem_insuranceCaseName_id,0,30)."..." : $elem_insuranceCaseName_id ;?></label></div>

		<div class="clearfix"></div>-->
		<!-- F/u -->
		<div id="dv_fu_sec" >
		<div class="panel-heading hdr" >Choose Follow Up <span class="glyphicon glyphicon-remove pull-right" onclick="op_fu_sec(3)"></span></div>
		<div class="fu_con">
			<div id="listFU" class="fubox" data-cntrfu="<?php echo $lenFu; ?>" data-fuembedin="work_view">
				<!--<h2>F/U</h2>-->
				<div class="clearfix"></div>
				<?php
				for($j=0,$i=0;$i<$lenFu;$i++){
					$j = $i+1;

					//Arc --
					//number
					if(!empty($arTmpRecArc["div"]["number"][$j])){
						$elem_followUpNumber_htm= $arTmpRecArc["div"]["number"][$j];
						$elem_followUpNumber_js = $arTmpRecArc["js"]["number"][$j];
						$elem_followUpNumber_css = $arTmpRecArc["css"]["number"][$j];
						if(!empty($arTmpRecArc["curText"]["number"][$j])){	$elem_followUpNumber = $arTmpRecArc["curText"]["number"][$j]; }
					}else{
						$elem_followUpNumber_htm = $elem_followUpNumber_js = $elem_followUpNumber_css ="";
					}
					//time
					if(!empty($arTmpRecArc["div"]["time"][$j])){
						$elem_followUpTime_htm = $arTmpRecArc["div"]["time"][$j];
						$elem_followUpTime_js = $arTmpRecArc["js"]["time"][$j];
						$elem_followUpTime_css = $arTmpRecArc["css"]["time"][$j];
						if(!empty($arTmpRecArc["curText"]["time"][$j])){	$elem_followUp = $arTmpRecArc["curText"]["time"][$j];	}
					}else{
						$elem_followUpTime_htm = $elem_followUpTime_js = $elem_followUpTime_css ="";
					}
					//visit_type
					if(!empty($arTmpRecArc["div"]["visit_type"][$j])){
						$elem_followUpVistType_htm = $arTmpRecArc["div"]["visit_type"][$j];
						$elem_followUpVistType_js = $arTmpRecArc["js"]["visit_type"][$j];
						$elem_followUpVistType_css = $arTmpRecArc["css"]["visit_type"][$j];
						if(!empty($arTmpRecArc["curText"]["visit_type"][$j])){ $elem_followUpVistType = $arTmpRecArc["curText"]["visit_type"][$j]; }
					}else{
						$elem_followUpVistType_htm = $elem_followUpVistType_js = $elem_followUpVistType_css ="";
					}
					//Arc --

					//Menu--
					$tmp_menu_followUpNumber = wv_get_simple_menu($arrFuNum_menu,"menu_followUpNumber","elem_followUpNumber_".$j);
					$tmp_menu_followUpVistType = wv_get_simple_menu($arrFuVist_menu,"menu_followUpVistType","elem_followUpVistType_".$j);

				?>
				<div class="row fu" >
					<div class="col-sm-1" >
						<div class="checkbox"><input type="checkbox" id="el_fu_choose_<?php echo $j; ?>" value="<?php echo $j; ?>" title="click to add" data-toggle="tooltip" tabindex="<?php echo $j."0"; ?>" ><label for="el_fu_choose_<?php echo $j; ?>" ></label></div>
					</div>
					<div class="col-sm-2" >
						<div class="input-group plain" >
							<input type="text" name="elem_followUpNumber[]" id="elem_followUpNumber_<?php echo $j; ?>" value="<?php echo $arrFuVals[$i]["number"]; ?>"
								class="form-control <?php echo $elem_followUpNumber_css; ?>" onchange="fu_refineNum(this)" <?php echo $elem_followUpNumber_js; ?> tabindex="<?php echo $j."1"; ?>" >
							<?php echo $tmp_menu_followUpNumber; ?>
						</div>
					</div>
					<div class="col-sm-2" >
						<select name="elem_followUp[]" id="elem_followUp_<?php echo $j; ?>" class="form-control <?php echo $elem_followUpTime_css; ?> "
									onchange="fu_move(this)" <?php echo $elem_followUpTime_js; ?>  tabindex="<?php echo $j."2"; ?>" >
									<option value=""></option>
									<?php echo $arrFuVals[$i]["time_options"]; ?>
						</select>
					</div>
					<div class="col-sm-3" >
						<div class="input-group plain" >
							<input type="text" name="elem_followUpVistType[]" id="elem_followUpVistType_<?php echo $j; ?>" value="<?php echo $arrFuVals[$i]["visit_type"]; ?>"
								 class="form-control <?php echo $elem_followUpVistType_css;?>" onchange="changeOther(this)"  title="<?php echo $arrFuVals[$i]["visit_type"]; ?>" data-toggle="tooltip"  <?php echo $elem_followUpVistType_js;?> tabindex="<?php echo $j."3"; ?>">
							<input type="hidden" name="elem_followUpVistTypeOther[]" id="elem_followUpVistTypeOther_<?php echo $j; ?>" value="">
							<?php echo $tmp_menu_followUpVistType; ?>
						</div>
					</div>
					<div class="col-sm-3" >
						<select name="elem_fuProName[]" id="elem_fuProName_<?php echo $j; ?>" class="form-control "
									 onmouseover="this.title=this.options[this.selectedIndex].text+'-'+this.value;" data-toggle="tooltip" onchange="fu_pro_change(this)"  tabindex="<?php echo $j."4"; ?>">
									 <option value=""></option>
									 <?php echo $arrFuVals[$i]["provider_options"]; ?>
						</select>
					</div>
					<div class="col-sm-1">
						<?php if($j==1){ ?>
						<span class="glyphicon glyphicon-plus" data-toggle="tooltip" title="Add F/U" onclick="fu_add()"></span>
						<?php } ?>
						<span class="glyphicon glyphicon-remove" data-toggle="tooltip" title="Remove F/U" id=\"fu_del<?php echo $j;?>\" onclick="fu_del('<?php echo $j;?>')"></span>

					</div>
					<?php echo $elem_followUpNumber_htm;
						 echo $elem_followUpTime_htm;
						 echo $elem_followUpVistType_htm;
					?>
				</div>
				<!--<div class="clearfix"></div>-->
				<?php
				}
				?>
				<input type="hidden" id="elem_fuCntr" name="elem_fuCntr" value="<?php echo $j;?>">
			</div>
		</div>
		<div class="panel-footer"><center><input type="button" id="btn_fu_save" name="btn_fu_save" value="Done" class="btn btn-success" onclick="op_fu_sec(1)"></center></div>
		</div>
		<!-- F/u -->
		<div class="clearfix"></div>
		<!-- SOC -->
		<div id="dv_strd_of_cr">
			<div class="pull-left" ><h2>STANDARDS OF CARE</h2></div>
			<div class="clearfix"></div>
			<div class="fubox" id="ft_strd_of_cr">
				<div class="form-group">
				<?php
					if(count($ar_soc) > 0){
					foreach($ar_soc as $ar_soc_k => $soc){
						if(!empty($soc["soc"])){
							$tmp = ($el_soc == $soc["id"]) ? "checked" : "";
							echo "<div class=\"col-xs-2 checkbox checkbox-inline\"><input type=\"checkbox\" name=\"el_soc\"
								id=\"el_soc".$ar_soc_k."\" value=\"".$soc["id"]."\" ".$tmp." ><label for=\"el_soc".$ar_soc_k."\" title=\"".$soc["descp"]."\" data-toggle=\"tooltip\" >".$soc["soc"]."</label></div> ";
						}
					}
					}
				?>
				</div>
				<div class="form-group">
				  <textarea class="form-control" rows="2" id="el_soc_commnts" name="el_soc_commnts" placeholder="SOC Comments"><?php echo $el_soc_commnts; ?></textarea>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<!-- SOC -->

		<div id="dv_fut_app">
			<div class="pull-left" ><h2>Future Appointments (Internal)</h2></div>
			<div class=" pull-right clickable" onclick="add_future_sch_tests_appoints()" >
      	<label class="pointer">Future Appointments (External)&nbsp;<span class="badge"><?php echo $futureSchTesExist; ?></span></label>
     	</div>
			<div class="clearfix"></div>
			<div class="fubox" id="ft_appnt"><?php echo $data_future_appointment; ?></div>
		</div>
		<div class="clearfix"></div>
		<div id="dv_recal">
			<div class="pull-left"><h2>Recalls</h2></div>
			<div class="pull-right clickable">	<input type="checkbox" id="elem_sur_ocu_hx" name="elem_sur_ocu_hx" value="1"  class="frcb" <?php echo $elem_sur_ocu_hx_checked;?> onclick="save_sur_ocu(this)" ><label for="elem_sur_ocu_hx" class="frcb"  >ASC - Surgical Ocular Hx <?php echo $elem_sur_ocu_hx_sent4dos;?></label></div>
			<div class="clearfix"></div>
			<div class="fubox" id="ft_recalls"><?php echo $data_sched_recalls; ?></div>
		</div>
		<div class="clearfix"></div>
		<div id="dv_transCare" class="row">
			<div class="col-sm-6">
				<h2>Transition of care</h2>
				<div>
					<input type="text" name="elem_doctorName" value="<?php echo $elem_doctorName;?>" id="elem_doctorName_refphy1" class="form-control <?php echo $elem_doctorName_css;?>"  onmouseover="get_reff_address_v2(this)" autocomplete="off" <?php echo $elem_doctorName_js;?> >
					<input type="hidden" name="elem_doctorName_id" id="hid_doctorName_refphy1" value="<?php echo $elem_doctorName_id; ?>" class="form-control" >
				</div>
				<?php echo $elem_doctorName_htm;?>
			</div>
			<div class="col-sm-6">
				<h2>Scribed by</h2>
				<div ><?php echo $sel_scribeby;?></div>
			</div>
		</div>

		<div class="clearfix"></div>
		<div id="dv_reasonfortransition" class="row">
			<div class="col-sm-6">
			<h2>Reason for transition of care</h2>
			<div class="clearfix"></div>
			<div ><textarea rows="1" class="form-control <?php echo $elem_transition_reason_css;?>" id="elem_transition_reason" name="elem_transition_reason" onkeyup="setTaPlanHgt(this.id);" <?php echo $elem_transition_reason_js;?> ><?php echo $elem_transition_reason; ?></textarea><?php echo $elem_transition_reason_htm;?></div>
			</div>
			<div class="col-sm-6">
			<h2 >Comments</h2>
			<div class="clearfix"></div>
			<div ><textarea rows="1" class="form-control <?php echo $elem_transition_notes_css;?>" id="elem_transition_notes" name="elem_transition_notes" onkeyup="setTaPlanHgt(this.id);" <?php echo $elem_transition_notes_js;?> ><?php echo $elem_transition_notes; ?></textarea><?php echo $elem_transition_notes_htm;?></div>
			<label class="pull-left"><?php echo $elem_transition_notes_Dt; ?></label>
			<label class="pull-right"><?php echo $elem_transition_notes_nm; ?></label>
			</div>
		</div>

		<div class="clearfix"></div>
		<div id="dv_ref2" class="row">
			<div class="col-sm-6">
			<h2>Refer to</h2>
			<div class="clearfix"></div>
			<div >
				<input type="text" name="elem_refer_to" id="elem_doctorName_refphy2" value="<?php echo $elem_refer_to; ?>" class="form-control <?php echo $elem_refer_to_css;?>" <?php echo $elem_refer_to_js;?> onmouseover="get_reff_address_v2(this)" ><?php echo $elem_refer_to_htm;?>
				<input type="hidden" name="elem_refer_to_id" id="hid_doctorName_refphy2" value="<?php echo $elem_refer_to_id; ?>" class="form-control" >
			</div>
			</div>
			<div class="col-sm-6">
			<h2>Refer to code</h2>
			<div class="clearfix"></div>
			<div >
				<input type="text" name="elem_refer_code" id="elem_refer_code" value="<?php echo $refer_code; ?>" class="form-control" onchange="referral_code_typeahead()">
                <input type="hidden" name="elem_refer_code_id" id="hid_refer_code" value="<?php echo $refer_code; ?>" class="form-control" >
			</div>
			</div>
		</div>

		<div class="clearfix"></div>
		<div id="dv_reasonforref" class="row">
			<div class="col-sm-6">
			<h2>Reason for Referrals</h2>
			<div class="clearfix"></div>
			<div ><textarea rows="1" class="form-control <?php echo $elem_consult_reason_css;?>" id="elem_consult_reason" name="elem_consult_reason" onkeyup="setTaPlanHgt(this.id);" <?php echo $elem_consult_reason_js;?> ><?php echo $elem_consult_reason; ?></textarea><?php echo $elem_consult_reason_htm;?></div>
			</div>
			<div class="col-sm-6">
			<h2 >Comments</h2>
			<div class="clearfix"></div>
			<div ><textarea rows="1" class="form-control <?php echo $elem_notes_css;?>" id="elem_notes" name="elem_notes" onkeyup="setTaPlanHgt(this.id);" <?php echo $elem_notes_js;?> ><?php echo $elem_notes; ?></textarea><?php echo $elem_notes_htm;?></div>
			<label class="pull-left"><?php echo $elem_notes_Dt; ?></label>
			<label class="pull-right"><?php echo $elem_notes_nm; ?></label>
			</div>
		</div>


		<div class="clearfix"></div>
        <?php
            $clomn1="";
            $clomn2=" col-lg-7 col-sm-6 ";
            $clomn3=" col-lg-5 col-sm-6 ";
            if($str_dss){
                $clomn1=" col-lg-3 col-sm-3 ";
                $clomn2=" col-lg-5 col-sm-5 ";
                $clomn3=" col-lg-4 col-sm-4 ";
            }
        ?>
		<!-- Pt dis comm + cgc -->
		<div id="dv_pt_discm_cgc" >
		<div class="row grow2g">
            <!-- Saving for DSS if visit is service connected eligibility -->
            <?php if($str_dss){ ?>
                <div class="<?php echo $clomn1; ?> service_eligibility">
                    <h2 data-toggle="tooltip" data-placement="top" title="Is this visit is service connected eligibility?" data-container="body">Service Eligibility</h2>

					<?php
						$formId = '';
						$style = '';
						$scval = '';
						if(isset($_SESSION['form_id']) && $_SESSION['form_id'] != '') {
							$formId = $_SESSION['form_id'];
						}elseif (isset($_SESSION['finalize_id']) && $_SESSION['finalize_id'] != '') {
							$formId = $_SESSION['finalize_id'];
						}

						$sql = imw_query("SELECT `service_eligibility` FROM `chart_master_table` WHERE `id` = '".$formId."'");
						if(imw_num_rows($sql) > 0) {
							$result = imw_fetch_assoc($sql);
							if(!empty($result['service_eligibility']) && ($result['service_eligibility'] != '' || $result['service_eligibility'] != 0)) {
								$style = 'style="color: orange;"';

								$sc = unserialize($result['service_eligibility']);
								$sval = '';
								foreach ($sc as $key => $val) {
									if($val == 1) {
										$sval .= $key.'=Yes, ';
									} else {
										$sval .= $key.'=No, ';
									}
								}
								$scval = rtrim($sval,', ');
							} else {
								$style = '';
							}
						}
					?>

					<span class="glyphicon glyphicon-question-sign" onclick="top.dssLoadServiceConnectedOpt('work_view','<?php echo $formId; ?>')" <?php echo $style; ?> data-toggle="tooltip" data-placement="top" title="" data-container="body" data-original-title="<?php echo $scval; ?>"></span>
					<input type="hidden" name="service_eligibility" id="service_eligibility" value="">

					<?php /* ?>
                    <div class="checkbox" style="margin:0px;">
                        <input type="checkbox" name="service_eligibility" id="service_eligibility" <?php if($service_eligibility==1){echo ' checked ';}?> value="<?php echo $service_eligibility;?>" autocomplete="off" onclick="service_eligibility_check(this);">
                        <label for="service_eligibility">&nbsp;</label>
<!--                        <input type="hidden" name="elem_time_of_service" id="elem_time_of_service" value="<?php //echo $time_of_service?>" />-->
                    </div>
                    <?php */ ?>
                </div>
            <?php } ?>

			<div class="<?php echo $clomn2; ?> ptdiscussion">
				<h2>Pt Discussion / Comments</h2>
				<div class="clearfix"></div>
				<div ><textarea class="form-control <?php echo $commentsForPatient_css;?> " id="commentsForPatient" name="commentsForPatient"
						onfocus="setExamDescChange(this,1);" rows="1" onkeyup="setTaPlanHgt(this.id);" <?php echo $commentsForPatient_js;?>
						><?php echo $commentsForPatient;?></textarea>
						<?php echo $commentsForPatient_htm;?>
				</div>
				<label class="pull-left"><?php echo $commentsForPatient_Dt; ?></label>
				<label class="pull-right"><?php echo $commentsForPatient_nm; ?></label>
			</div>
			<div class="<?php echo $clomn3; ?>">
				<div id="legendDiv" class="caregiver">
				<h2>Care Giver Colors:</h2>

				<?php
					if(count($ar_care_giver_colors)>0){
					foreach($ar_care_giver_colors as $key_cgc => $ar_cgc){
				?>
				<!--<div class="clearfix"></div>-->
				<div class="row">
				<div class="col-sm-3"><div class="dvcgc" style="background-color:<?php echo $ar_cgc["color1"];?>;"></div><div class="dvcgc" style="background-color:<?php echo $ar_cgc["color2"];?>;"></div></div><!--<img src="img/colorbar.png" alt=""/>-->
				<div class="col-sm-7 <?php echo $ar_cgc["clickable"];?>"><strong><?php echo $ar_cgc["name"];?></strong></div>
				<div class="col-sm-2"><?php echo $ar_cgc["type"];?></div>
				</div>
				<?php }}//end if ?>


				</div>
			</div>
		</div>
		</div>
		<div class="clearfix"></div>
		<!-- Pt dis comm + cgc -->

		<!-- Scribe Attestation -->
		<div id="div_attestation" class="row scribe_attes">
			<?php echo $htm_scribe_attes; ?>
		</div>
		<div class="clearfix"></div>
		<!-- Scribe Attestation -->

		<div class="todobx"  >
			<!--
			<h2 >To Do</h2>
			<div class="clearfix"></div>
			-->
			<div id="signatures" >

				<?php
				if(count($arrSigns) > 0){
				foreach($arrSigns as $k_sign => $arrSign){
				$key_sign = $k_sign + 1;
				?>
				<!-- Signature -->
				<div id="sign_phy<?php echo $key_sign;?>" class="row divsign" >
					<div class="col-md-2 col-sm-2 col-sm-2-sign" >
						<label id="lbl_phy_sig<?php echo $key_sign;?>" <?php echo (!empty($arrSign["strClickDbSig"])) ? "class=\"clickable\" onclick=\"getPhySign_db(".$key_sign.");\" " : "";?>   >Signature</label>
					</div>
					<div class="col-md-4 col-sm-4 col-sm-4-sign" >
						<span id="td_signature_applet<?php echo $key_sign;?>" class="appletCon" <?php echo (!empty($arrSign["strClickSigPopUp"])) ? "onclick=\"getAssessmentSign(".$key_sign.")\"" : ""; ?> ><?php echo !empty($arrSign["img_sign_path"]) ? "<img src=\"".$arrSign["img_sign_path"]."\" alt=\"sign\" width=\"225\" height=\"45\">" : "" ;?></span>
						<?php echo $arrSign["strFDate"]; ?>
					</div>
					<div class="col-md-2 col-sm-2 clickable">
						<label onclick="click2MoveSign(<?php echo $key_sign;?>)" class="signernm"> Sign. Name</label>
					</div>
					<div class="col-md-3 col-sm-3">
						<input type="text" id="elem_physicianIdName<?php echo $key_sign;?>" name="elem_physicianIdName<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_physicianIdName"];?>" readonly class="form-control" >
					</div>
					<div class="col-md-1 col-sm-1" >
						<!--
						<img src="<?php echo $GLOBALS['webroot']; ?>/library/images/closerd.png" alt="Delete" onclick="getAssessmentSign(<?php echo $key_sign; ?>,'3')" style="display:<?php echo $arrSign["del_btn"]; ?>" >
						<?php if(!empty($arrSign["add_btn"])){?><img src="<?php echo $GLOBALS['webroot'];?>/library/images/add_icon.png" alt="Add" onclick="addMoreSigns(<?php echo $arrSign["add_btn"]; ?>)" /><?php } ?>
						-->
						<span class="glyphicon glyphicon-remove" data-toggle="tooltip" title="Delete" onclick="getAssessmentSign(<?php echo $key_sign;?>,'3')" style="display:<?php echo $arrSign["del_btn"]; ?>" ></span>
						<?php if(!empty($arrSign["add_btn"])){?><span class="glyphicon glyphicon-plus" data-toggle="tooltip" title="Add" onclick="addMoreSigns(<?php echo $arrSign["add_btn"]; ?>)" ></span><?php } ?>

						<input type="hidden" name="elem_physicianId<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_physicianId"];?>" onChange="setPhysician(this)">
						<input type="hidden" name="elem_signCoords<?php echo $key_sign;?>" id="elem_signCoords<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_signCoords"];?>" onChange="setThisChangeStatus(this)">
						<input type="hidden" name="hdSignCoordsOriginal<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_signCoords"];?>">
						<input type="hidden" name="elem_is_user_sign<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_is_user_sign"];?>">
						<input type="hidden" name="elem_is_phy_sign<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_is_phy_sign"];?>">
						<input type="hidden" name="elem_sign_path<?php echo $key_sign;?>" value="<?php echo $arrSign["elem_sign_path"];?>">
					</div>
				</div>
				<!-- Signature -->
				<?php
				} //for sign ends
				}// if sign
				?>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	</div>
	<div class="col-lg-6 col-md-12 col-sm-12">
		<!-- Super bill -->
		<div id="superbill" class="whitebox"></div>
		<!-- Super bill -->
	</div>


</div>
<div class="clearfix"></div>
<!-- Patient At Glance -->
<div id="pgd_showpop" class="modal" role="dialog" style="margin:50px auto 0;width:90%;height:550px;"></div>

<!-- 7 row -->

<?php
//Patient chart Purged X sign --------
//14-08-2014 : C4S: Discussion/Issues 8/13/14: Big Red X is not appearing for the charts that are purged.
//*
if(!empty($elem_masterpurge_status)){
?>
<div id="dvPrgdSgn" data-toggle="tooltip" title="Purged chart note">X</div>
<?php
}
//*/
//Patient chart Purged X sign --------
?>

<!-- hidden -->
<?php echo $str_hidden_vals;?>
<!-- hidden -->

</form>
<?php //--  END MAIN FORM -- ?>

<!--Gen Health-->
<div id="genHealthDiv_wv" class="modal " role="dialog" ><?php echo $htm_gen_health;?></div>

<?php
//Chart Lock function
echo $htm_pt_chart_lock;
?>

<!-- hidden html -->
<?php
echo $chrttemp_HtmlHidden;
//$authUserID=$_SESSION["authUserID"];
?>
<!-- hidden html -->

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjs"></script>

</body>
</html>

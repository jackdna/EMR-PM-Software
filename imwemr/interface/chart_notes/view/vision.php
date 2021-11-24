<?php
//Chart Vision

?>

<!-- Vision Hidden -->
<input type="hidden" id="elem_visId" name="elem_visId" value="<?php echo $elem_visId;?>">
<input type="hidden" id="elem_formId" name="elem_formId" value="<?php echo $form_id;?>" >
<input type="hidden" id="elem_patientId" name="elem_patientId" value="<?php echo $patient_id;?>" >
<input type="hidden" id="elem_examDate" name="elem_examDate" value="<?php echo $elem_examDate;?>" >
<input type="hidden" id="elem_editModeVis" name="elem_editModeVis" value="<?php echo $elem_editModeVis;?>">
<input type="hidden" id="elem_statusElements" name="elem_statusElements" value="<?php echo $elem_statusElements;?>">
<input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
<input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">
<input type="hidden" id="elem_vis_section_touched" name="elem_vis_section_touched" value="">
<input type="hidden" id="ctmpMr3" name="ctmpMr3" value="<?php echo $ctmpMr3;?>">
<!-- Vision Hidden -->


<div  id="Vision" > <!-- role="tabpanel" class="tab-pane active" -->
	
	<?php
	if(empty($ctmpDisWS)){ //Distance	
	?>
	<!-- Distance Tab -->
			<div class="collapse in" id="Distance"> <!-- role="tabpanel" class="tab-pane active"  -->
				<div class="row">
					<!-- Distance -->
					<div class="col-lg-3 col-lg-3-hd col-md-5 col-sm-5  <?php echo $ctmpDis;?>" > <!--  -->
						<div class="examsectbox">
							<div class="header">
								<div class="distancetab head-icons" >
									<ul>
										<!--
										<li class="form-inline">
											<div class="kvalue img_acuity text-center" onClick="callPopup('popup_mr.php','popDistance','1250','760');" style="vertical-align:bottom;">
												<img src="<?php echo $GLOBALS['webroot'];?>/library/images/ar_icon.png" alt="Acuities" />												
											</div>
										</li>
										-->
										<li>
											<h2 class="clickable" onClick="callPopup('','popDistance','1250','760');">Distance</h2>
										</li>
										<li>
											<div class="input-group">
											<input type="text" name="elem_visSnellan" value="<?php if($elem_visSnellan) echo $elem_visSnellan; else echo ''; ?>" data-toggle="tooltip" title="<?php if($elem_visSnellan) echo $elem_visSnellan; else echo ''; ?>" class="form-control <?php echo $this->vis_getStatus("elem_visSnellan");?>" onclick="chkOther(this);" id="elem_visSnellan" />
											<?php												
												echo $menu_visSnellan ; /*wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visSnellan");*/
											?>
											</div>
										</li>
										<li>
											<select name="elem_addvisDisOdSel" id="elem_addvisDisOdSel" class="form-control <?php echo $this->vis_getStatus("elem_visDisOdSel4");?>" onchange="add_more_actuity()" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="PH" <?php echo ($elem_visDisOdSel4 == "PH" ) ? "selected" : "" ;?> >PH</option>
												<option value="GL" <?php echo ($elem_visDisOdSel4 == "GL" ) ? "selected" : "" ;?> >GL</option>
												<option value="SC" <?php echo ($elem_visDisOdSel4 == "SC" ) ? "selected" : "" ;?> >SC</option>
												<option value="CC" <?php echo ($elem_visDisOdSel4 == "CC" ) ? "selected" : "" ;?> >CC</option>    
											</select>
											
										</li>
										<li><span id="flg_addvisDisOdSel" class="glyphicon glyphicon-flag clickable <?php echo (empty($elem_visDisOdSel4)) ? "hidden" : "" ;?> " onclick="add_more_actuity()"></span></li>
									</ul>
									<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="exampd default">
								<table class="table borderless">
									<tr>
										<td class="odcol">OD</td>
										<td class="">
											<select id="elem_visDisOdSel1" name="elem_visDisOdSel1" onBlur="setActuity(this);setOs(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOdSel1");?>" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="SC" <?php echo ($elem_visDisOdSel1 == "SC") ? "selected" : ""; ?>>SC</option>
												<option value="CC" <?php echo ($elem_visDisOdSel1 == "CC") ? "selected" : ""; ?>>CC</option>
												<option value="CL-S" <?php echo ($elem_visDisOdSel1 == "CL-S") ? "selected" : ""; ?>>CL-S</option>
												<option value="GPCL" <?php echo ($elem_visDisOdSel1 == "GPCL") ? "selected" : ""; ?>>GPCL</option>
											</select>
										</td>
										<td class="">
											<div class="input-group">
												<input type="text" name="elem_visDisOdTxt1" id="elem_visDisOdTxt1" value="<?php if($elem_visDisOdTxt1) echo $elem_visDisOdTxt1; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOdTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
												<?php echo $menu_visDisOdTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt1",255,0,array("pdiv"=>"elem_visDisOdTxt1"));*/ ?>
												
											</div>
										</td>
										<td class="">
											<select id="elem_visDisOdSel2" name="elem_visDisOdSel2" onBlur="setActuity(this);setOs(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOdSel2");?>" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="CC" <?php echo ($elem_visDisOdSel2 == "CC" ) ? "selected" : "" ;?>>CC</option>
												<option value="SC" <?php echo ($elem_visDisOdSel2 == "SC") ? "selected" : ""; ?>>SC</option>
												<option value="PH" <?php echo ($elem_visDisOdSel2 == "PH" ) ? "selected" : "" ;?>>PH</option>
												<option value="GL" <?php echo ($elem_visDisOdSel2 == "GL" ) ? "selected" : "" ;?>>GL</option>
												<option value="CL-S" <?php echo ($elem_visDisOdSel2 == "CL-S") ? "selected" : ""; ?>>CL-S</option>
												<option value="GPCL" <?php echo ($elem_visDisOdSel2 == "GPCL") ? "selected" : ""; ?>>GPCL</option>
											</select>
										</td>
										<td class="">
											<div class="input-group">
												<input type="text" name="elem_visDisOdTxt2" value="<?php if($elem_visDisOdTxt2) echo $elem_visDisOdTxt2; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOdTxt2");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> id="elem_visDisOdTxt2" />											
												<?php echo $menu_visDisOdTxt2 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt2",200,500,array("pdiv"=>"elem_visDisOdTxt2"));*/ ?>											
											</div>
										</td>
									</tr>
									<tr>
										<td class=" oscol">OS</td>
										<td class="">
											<select id="elem_visDisOsSel1" name="elem_visDisOsSel1" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOsSel1");?>" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="SC" <?php echo ($elem_visDisOsSel1 == "SC") ? "selected" : ""; ?>>SC</option>
												<option value="CC" <?php echo ($elem_visDisOsSel1 == "CC" ) ? "selected" : "" ;?>>CC</option>
												<option value="CL-S" <?php echo ($elem_visDisOsSel1 == "CL-S") ? "selected" : ""; ?>>CL-S</option>
												<option value="GPCL" <?php echo ($elem_visDisOsSel1 == "GPCL") ? "selected" : ""; ?>>GPCL</option>
											</select>
										</td>
										<td class="">
											<div class="input-group">
												<input type="text" name="elem_visDisOsTxt1" id="elem_visDisOsTxt1" value="<?php if($elem_visDisOsTxt1) echo $elem_visDisOsTxt1; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOsTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
												<?php echo $menu_visDisOsTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt1",255,0,array("pdiv"=>"elem_visDisOsTxt1"));*/?>											
											</div>
										</td>
										<td class="">
											<select id="elem_visDisOsSel2" name="elem_visDisOsSel2" onBlur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOsSel2");?>" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="CC" <?php echo ($elem_visDisOsSel2 == "CC" ) ? "selected" : "" ;?>>CC</option>
												<option value="SC" <?php echo ($elem_visDisOsSel2 == "SC") ? "selected" : ""; ?>>SC</option>
												<option value="PH" <?php echo ($elem_visDisOsSel2 == "PH" ) ? "selected" : "" ;?>>PH</option>
												<option value="GL" <?php echo ($elem_visDisOsSel2 == "GL" ) ? "selected" : "" ;?>>GL</option>        
												<option value="CL-S" <?php echo ($elem_visDisOsSel2 == "CL-S") ? "selected" : ""; ?>>CL-S</option>
												<option value="GPCL" <?php echo ($elem_visDisOsSel2 == "GPCL") ? "selected" : ""; ?>>GPCL</option> 
											</select>
										</td>
										<td class="">
											<div class="input-group">
												<input type="text" name="elem_visDisOsTxt2" id="elem_visDisOsTxt2" value="<?php if($elem_visDisOsTxt2) echo $elem_visDisOsTxt2; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOsTxt2");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
												<?php echo $menu_visDisOsTxt2 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt2",200,500,array("pdiv"=>"elem_visDisOsTxt2"));*/ ?>											
											</div>
										</td>
									</tr>
									
									<tr>
										<td class=" oucol">OU</td>
										<td class="">
											<select id="elem_visDisOuSel1" name="elem_visDisOuSel1" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOuSel1");?>" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="SC" <?php echo ($elem_visDisOuSel1 == "SC") ? "selected" : ""; ?>>SC</option>	
												<option value="CC" <?php echo ($elem_visDisOuSel1 == "CC" ) ? "selected" : "" ;?>>CC</option>
												<option value="CL-S" <?php echo ($elem_visDisOuSel1 == "CL-S") ? "selected" : ""; ?>>CL-S</option>
												<option value="GPCL" <?php echo ($elem_visDisOuSel1 == "GPCL") ? "selected" : ""; ?>>GPCL</option>    
											</select>
										</td>
										<td class="">
											<div class="input-group">
												<input type="text" name="elem_visDisOuTxt1" id="elem_visDisOuTxt1" value="<?php if($elem_visDisOuTxt1) echo $elem_visDisOuTxt1; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOuTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
												<?php echo $menu_visDisOuTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt1",255,0,array("pdiv"=>"elem_visDisOuTxt1"));*/ ?>
											</div>
										</td>
										<td class="">
											<select id="elem_visDisOuSel2" name="elem_visDisOuSel2" onBlur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOuSel2");?>" <?php echo $distancePopupCall;?>>
												<option value=""></option>
												<option value="CC" <?php echo ($elem_visDisOuSel2 == "CC" ) ? "selected" : "" ;?>>CC</option>
												<option value="SC" <?php echo ($elem_visDisOuSel2 == "SC") ? "selected" : ""; ?>>SC</option>
												<option value="PH" <?php echo ($elem_visDisOuSel2 == "PH" ) ? "selected" : "" ;?>>PH</option>
												<option value="GL" <?php echo ($elem_visDisOuSel2 == "GL" ) ? "selected" : "" ;?>>GL</option>        
												<option value="CL-S" <?php echo ($elem_visDisOuSel2 == "CL-S") ? "selected" : ""; ?>>CL-S</option>
												<option value="GPCL" <?php echo ($elem_visDisOuSel2 == "GPCL") ? "selected" : ""; ?>>GPCL</option>
											</select>
										</td>
										<td class="">
											<div class="input-group">
												<input type="text" name="elem_visDisOuTxt2" id="elem_visDisOuTxt2" value="<?php if($elem_visDisOuTxt2) echo $elem_visDisOuTxt2; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOuTxt2");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
												<?php echo $menu_visDisOuTxt2 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt2",200,500,array("pdiv"=>"elem_visDisOuTxt2"));*/ ?>											
											</div>
										</td>
									</tr>
									<tr>										
										<td class="" colspan="6">
											<textarea id="elem_disDesc" name="elem_disDesc" class="form-control <?php echo $this->vis_getStatus("elem_disDesc");?>" rows="2"><?php echo $elem_disDesc; ?></textarea>
											<input type="hidden" name="elem_disDescLF" value="<?php echo $elem_disDescLF;?>">
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<!-- END Distance -->
					
					<!-- NEAR -->
					<div class="col-lg-3 col-lg-3-hd col-md-5 col-sm-5 <?php echo $ctmpNear;?>">
					<div class="examsectbox">
						<div class="header">
							<div class="neartab">
								<ul>
									<li>
										<h2 class="clickable" onClick="callPopup('','popNear','1250','760');">Near</h2>
									</li>
									<li>
										<div class="input-group">
										<input type="text" name="elem_visSnellan_near" value="<?php if($elem_visSnellan_near) echo $elem_visSnellan_near; else echo ''; ?>" data-toggle="tooltip" title="<?php if($elem_visSnellan_near) echo $elem_visSnellan_near; else echo ''; ?>" class="form-control <?php echo $this->vis_getStatus("elem_visSnellan_near");?>" onclick="chkOther(this);" id="elem_visSnellan_near" />											
										<?php echo $menu_visSnellan_near ; /*wv_get_simple_menu($arrSnellan,"menu_snellan_near","elem_visSnellan_near",253,0,array("pdiv"=>"elem_visSnellan_near"));*/ ?>
										</div>
									</li>
									<li><img id="img_flowsheet" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" onclick="show_iop_graphs(0,'D')" width="26" height="25" alt="img_flowsheet" class="clickable"></li>	
								</ul>
								<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol">OD</td>
								<td class="">
									<select name="elem_visNearOdSel1" id="elem_visNearOdSel1" onBlur="setActuity(this);setOs(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visNearOdSel1");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="SC" <?php echo ($elem_visNearOdSel1 == "SC") ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visNearOdSel1 == "CC") ? "selected" : "" ;?>>CC</option>
										<option value="CL-S" <?php echo ($elem_visNearOdSel1 == "CL-S") ? "selected" : "" ;?>>CL-S</option>
										<option value="GPCL" <?php echo ($elem_visNearOdSel1 == "GPCL") ? "selected" : "" ;?>>GPCL</option>
										<option value="MV" <?php echo ($elem_visNearOdSel1 == "MV") ? "selected" : "" ;?>>MV</option>
									</select>
								</td>
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visNearOdTxt1" id="elem_visNearOdTxt1" value="<?php if($elem_visNearOdTxt1) echo $elem_visNearOdTxt1; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visNearOdTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
										
										<?php echo $menu_visNearOdTxt1 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOdTxt1",255,0,array("pdiv"=>"elem_visNearOdTxt1"));*/ ?>
										
									</div>
								</td>
								<td class="">
									<select name="elem_visNearOdSel2" id="elem_visNearOdSel2" onBlur="setActuity(this);setOs(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visNearOdSel2");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="CC" <?php echo ($elem_visNearOdSel2 == "CC") ? "selected" : "" ;?>>CC</option>
										<option value="SC" <?php echo ($elem_visNearOdSel2 == "SC") ? "selected" : "" ;?>>SC</option>
										<option value="GL" <?php echo ($elem_visNearOdSel2 == "GL") ? "selected" : "" ;?>>GL</option>
										<option value="CL-S" <?php echo ($elem_visNearOdSel2 == "CL-S") ? "selected" : "" ;?>>CL-S</option>
										<option value="GPCL" <?php echo ($elem_visNearOdSel2 == "GPCL") ? "selected" : "" ;?>>GPCL</option>
										<option value="MV" <?php echo ($elem_visNearOdSel2 == "MV") ? "selected" : "" ;?>>MV</option>
									</select>
								</td>
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visNearOdTxt2" id="elem_visNearOdTxt2" value="<?php if($elem_visNearOdTxt2) echo $elem_visNearOdTxt2; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visNearOdTxt2");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
										
										<?php echo $menu_visNearOdTxt2 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOdTxt2",255,0,array("pdiv"=>"elem_visNearOdTxt2"));*/ ?>
										
									</div>
								</td>
							</tr>
							<tr>
								<td class=" oscol">OS</td>
								<td class="">
									<select name="elem_visNearOsSel1" id="elem_visNearOsSel1" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visNearOsSel1");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="SC" <?php echo ($elem_visNearOsSel1 == "SC") ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visNearOsSel1 == "CC") ? "selected" : "" ;?>>CC</option>
										<option value="CL-S" <?php echo ($elem_visNearOsSel1 == "CL-S") ? "selected" : "" ;?>>CL-S</option>
										<option value="GPCL" <?php echo ($elem_visNearOsSel1 == "GPCL") ? "selected" : "" ;?>>GPCL</option>
										<option value="MV" <?php echo ($elem_visNearOsSel1 == "MV") ? "selected" : "" ;?>>MV</option>
									</select>
								</td>
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visNearOsTxt1" id="elem_visNearOsTxt1" value="<?php if($elem_visNearOsTxt1) echo $elem_visNearOsTxt1; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visNearOsTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
										
										<?php echo $menu_visNearOsTxt1 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOsTxt1",255,0,array("pdiv"=>"elem_visNearOsTxt1"));*/ ?>
										
									</div>
								</td>
								<td class="">
									<select name="elem_visNearOsSel2" id="elem_visNearOsSel2" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visNearOsSel2");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="CC" <?php echo ($elem_visNearOsSel2 == "CC") ? "selected" : "" ;?>>CC</option>
										<option value="SC" <?php echo ($elem_visNearOsSel2 == "SC") ? "selected" : "" ;?>>SC</option>
										<option value="GL" <?php echo ($elem_visNearOsSel2 == "GL") ? "selected" : "" ;?>>GL</option>
										<option value="CL-S" <?php echo ($elem_visNearOsSel2 == "CL-S") ? "selected" : "" ;?>>CL-S</option>
										<option value="GPCL" <?php echo ($elem_visNearOsSel2 == "GPCL") ? "selected" : "" ;?>>GPCL</option>
										<option value="MV" <?php echo ($elem_visNearOsSel2 == "MV") ? "selected" : "" ;?>>MV</option>
									</select>
								</td>
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visNearOsTxt2" id="elem_visNearOsTxt2" value="<?php if($elem_visNearOsTxt2) echo $elem_visNearOsTxt2; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visNearOsTxt2");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
										
										<?php echo $menu_visNearOsTxt2 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOsTxt2",255,0,array("pdiv"=>"elem_visNearOsTxt2"));*/ ?>
										
									</div>
								</td>
							</tr>
							<tr>
								<td class=" oucol">OU</td>
								<td class="">
									<select name="elem_visNearOuSel1" id="elem_visNearOuSel1" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visNearOuSel1");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="SC" <?php echo ($elem_visNearOuSel1 == "SC") ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visNearOuSel1 == "CC") ? "selected" : "" ;?>>CC</option>
										<option value="CL-S" <?php echo ($elem_visNearOuSel1 == "CL-S") ? "selected" : "" ;?>>CL-S</option>
										<option value="GPCL" <?php echo ($elem_visNearOuSel1 == "GPCL") ? "selected" : "" ;?>>GPCL</option>
										<option value="MV" <?php echo ($elem_visNearOuSel1 == "MV") ? "selected" : "" ;?>>MV</option>
									</select>
								</td>
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visNearOuTxt1" id="elem_visNearOuTxt1" value="<?php if($elem_visNearOuTxt1) echo $elem_visNearOuTxt1; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visNearOuTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
										
										<?php echo $menu_visNearOuTxt1 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOuTxt1",255,0,array("pdiv"=>"elem_visNearOuTxt1"));*/ ?>
										
									</div>
								</td>
								<td class="">
									<select name="elem_visNearOuSel2" id="elem_visNearOuSel2" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visNearOuSel2");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="CC" <?php echo ($elem_visNearOuSel2 == "CC") ? "selected" : "" ;?>>CC</option>
										<option value="SC" <?php echo ($elem_visNearOuSel2 == "SC") ? "selected" : "" ;?>>SC</option>
										<option value="GL" <?php echo ($elem_visNearOuSel2 == "GL") ? "selected" : "" ;?>>GL</option>
										<option value="CL-S" <?php echo ($elem_visNearOuSel2 == "CL-S") ? "selected" : "" ;?>>CL-S</option>
										<option value="GPCL" <?php echo ($elem_visNearOuSel2 == "GPCL") ? "selected" : "" ;?>>GPCL</option>
										<option value="MV" <?php echo ($elem_visNearOuSel2 == "MV") ? "selected" : "" ;?>>MV</option>
									</select>
								</td>
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visNearOuTxt2" id="elem_visNearOuTxt2" value="<?php if($elem_visNearOuTxt2) echo $elem_visNearOuTxt2; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visNearOuTxt2");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />
										
											<?php echo $menu_visNearOuTxt2 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOuTxt2",255,0,array("pdiv"=>"elem_visNearOuTxt2"));*/ ?>
										
									</div>
								</td>
							</tr>
							<tr>								
								<td class="" colspan="6">
									<textarea id="elem_visNearDesc" name="elem_visNearDesc" class="form-control <?php echo $this->vis_getStatus("elem_visNearDesc");?>" rows="2"><?php echo $elem_visNearDesc; ?></textarea>
								</td>
							</tr>
							</table>
						</div>
					</div>
					</div>
					<!-- End NEAR -->					
					
					<!-- ADD ACUITY -->
					<div class="col-lg-2 col-lg-2-adac col-md-2 col-sm-2 <?php echo $ctmpDis;?>">
					<div class="examsectbox">
						<div class="header">
							<div class="distancetab head-icons" >
							<ul>
								<li><h2 class="clickable" onClick="callPopup('','popAdAcuity','1250','760');">Ad. Acuity</h2></li>
								<li>
									<select name="elem_visDisOuSel3" id="elem_visDisOuSel3" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOuSel3");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="PH" <?php echo ($elem_visDisOuSel3 == "PH" ) ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php echo ($elem_visDisOuSel3 == "GL" ) ? "selected" : "" ;?>>GL</option>
										<option value="SC" <?php echo ($elem_visDisOuSel3 == "SC" ) ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visDisOuSel3 == "CC" ) ? "selected" : "" ;?>>CC</option>    
									</select>
								</li>
							</ul>
							<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol">OD</td>
								<!--
								<td class="">
									<select name="elem_visDisOdSel3" id="elem_visDisOdSel3" onBlur="setActuity(this);setOs(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOdSel3");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="PH" <?php echo ($elem_visDisOdSel3 == "PH" ) ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php echo ($elem_visDisOdSel3 == "GL" ) ? "selected" : "" ;?>>GL</option>
										<option value="SC" <?php echo ($elem_visDisOdSel3 == "SC" ) ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visDisOdSel3 == "CC" ) ? "selected" : "" ;?>>CC</option>
									</select>
								</td>
								-->
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visDisOdTxt3" id="elem_visDisOdTxt3" value="<?php if($elem_visDisOdTxt3) echo $elem_visDisOdTxt3; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOdTxt3");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
										<?php echo $menu_visDisOdTxt3 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt1",255,0,array("pdiv"=>"elem_visDisOdTxt1"));*/ ?>
										
									</div>
								</td>								
							</tr>
							<tr>
								<td class=" oscol">OS</td>
								<!--
								<td class="">
									<select name="elem_visDisOsSel3" id="elem_visDisOsSel3" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOsSel3");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="PH" <?php echo ($elem_visDisOsSel3 == "PH" ) ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php echo ($elem_visDisOsSel3 == "GL" ) ? "selected" : "" ;?>>GL</option>
										<option value="SC" <?php echo ($elem_visDisOsSel3 == "SC" ) ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visDisOsSel3 == "CC" ) ? "selected" : "" ;?>>CC</option>
									</select>
								</td>
								-->
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visDisOsTxt3" id="elem_visDisOsTxt3" value="<?php if($elem_visDisOsTxt3) echo $elem_visDisOsTxt3; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOsTxt3");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
										<?php echo $menu_visDisOsTxt3 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt1",255,0,array("pdiv"=>"elem_visDisOsTxt1"));*/?>											
									</div>
								</td>
								
							</tr>
							<tr>
								<td class=" oucol">OU</td>
								<!--
								<td class="">
									<select name="elem_visDisOuSel3" id="elem_visDisOuSel3" onblur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visDisOuSel3");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="PH" <?php echo ($elem_visDisOuSel3 == "PH" ) ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php echo ($elem_visDisOuSel3 == "GL" ) ? "selected" : "" ;?>>GL</option>
										<option value="SC" <?php echo ($elem_visDisOuSel3 == "SC" ) ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visDisOuSel3 == "CC" ) ? "selected" : "" ;?>>CC</option>    
									</select>
								</td>
								-->
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visDisOuTxt3" id="elem_visDisOuTxt3" value="<?php if($elem_visDisOuTxt3) echo $elem_visDisOuTxt3; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOuTxt3");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
										<?php echo $menu_visDisOuTxt3 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt1",255,0,array("pdiv"=>"elem_visDisOuTxt1"));*/ ?>
									</div>
								</td>
								
							</tr>
							<tr>								
								<td class="" colspan="2">												
									<textarea name="elem_visDisAct3" class="form-control <?php echo $this->vis_getStatus("elem_visDisAct3");?>" rows="2"><?php echo $elem_visDisAct3; ?></textarea>									
									
								</td>
							</tr>
							</table>
						</div>
					</div>
					</div>
					<!-- ADD ACUITY -->
					
					
					<!-- ADD ACUITY Forth -->
					<div id="div_pop_acuity4" class=" hidden col-lg-2 col-lg-2-adac col-md-2 col-sm-2 <?php echo $ctmpDis;?>" style=" border:1px solid black; position:fixed; background-color:white; z-index:20; ">
					<div class="examsectbox">
						<div class="header">
							<div class="distancetab head-icons" >
							<ul>
								<li><h2 >Acuity</h2></li>
								<li>
									<select name="elem_visDisOdSel4" id="elem_visDisOdSel4" onblur="setActuity(this);" class="form-control   <?php echo $this->vis_getStatus("elem_visDisOdSel4");?>" <?php echo $distancePopupCall;?>>
										<option value=""></option>
										<option value="PH" <?php echo ($elem_visDisOdSel4 == "PH" ) ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php echo ($elem_visDisOdSel4 == "GL" ) ? "selected" : "" ;?>>GL</option>
										<option value="SC" <?php echo ($elem_visDisOdSel4 == "SC" ) ? "selected" : "" ;?>>SC</option>
										<option value="CC" <?php echo ($elem_visDisOdSel4 == "CC" ) ? "selected" : "" ;?>>CC</option>    
									</select>
								</li>
								<li>
									
								</li>
							</ul>
							
							<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol">OD</td>
								
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visDisOdTxt4" id="elem_visDisOdTxt4" value="<?php if($elem_visDisOdTxt4) echo $elem_visDisOdTxt4; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOdTxt4");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
										<?php echo $menu_visDisOdTxt4 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt1",255,0,array("pdiv"=>"elem_visDisOdTxt1"));*/ ?>
										
									</div>
								</td>								
							</tr>
							<tr>
								<td class=" oscol">OS</td>
								
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visDisOsTxt4" id="elem_visDisOsTxt4" value="<?php if($elem_visDisOsTxt4) echo $elem_visDisOsTxt4; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOsTxt4");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
										<?php echo $menu_visDisOsTxt4 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt1",255,0,array("pdiv"=>"elem_visDisOsTxt1"));*/?>											
									</div>
								</td>
								
							</tr>
							<tr>
								<td class=" oucol">OU</td>
								
								<td class="">
									<div class="input-group">
										<input type="text" name="elem_visDisOuTxt4" id="elem_visDisOuTxt4" value="<?php if($elem_visDisOuTxt4) echo $elem_visDisOuTxt4; else echo '20/'; ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visDisOuTxt4");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $distancePopupCall;?> />											
										<?php echo $menu_visDisOuTxt4 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt1",255,0,array("pdiv"=>"elem_visDisOuTxt1"));*/ ?>
									</div>
								</td>
								
							</tr>
							<tr>								
								<td class="" colspan="2">												
									<textarea name="elem_visDisAct4" class="form-control <?php echo $this->vis_getStatus("elem_visDisAct4");?>" rows="2"><?php echo $elem_visDisAct4; ?></textarea>									
									
								</td>
							</tr>
							</table>
						</div>
						<div class="clearfix"></div>
						<div class="footer text-center" >
							<button name="btn_act_done" class="btn btn-success" onclick="add_more_actuity('1')">Done</button>
							<button name="btn_act_reset" class="btn btn-danger" onclick="add_more_actuity('2')">Reset</button>
						</div>
					</div>
					</div>
					<!-- ADD ACUITY Forth -->

					


					
					<!-- K -->
					<div class="col-lg-2 col-lg-1-hd col-md-4 col-sm-4 <?php echo $ctmpAk;?>"><!-- col-md-6 col-sm-12 -->
						<div class="examsectbox">
							<div class="header">
								<div class="automan head-icons">
									<ul>
										<!--<li class="form-inline">
											<!--<div class="kvalue img_acuity" class="clickable" onClick="callPopup('','popAdAcuity','1250','760');">K</div>--*>											
										</li>-->
										<li>
											<h2 class="clickable" onClick="callPopup('','popK','1250','760');" >K</h2>
										</li>
										<li>
											<input type="checkbox" name="elem_kType" id="elem_krefractAuto" value="Auto" <?php echo (strpos($elem_kType,"Auto")!==false) ? "checked" : "" ;?> class="clickableLable <?php echo $this->vis_getStatus("elem_kType");?>" onClick="ak_sel(this);">
											<label for="elem_krefractAuto">Auto</label>
										</li>
										<li>
											<input type="checkbox" name="elem_kType" id="elem_krefractManual" value="Manual" <?php echo (strpos($elem_kType,"Manual")!==false) ? "checked" : "" ;?> class="clickableLable <?php echo $this->vis_getStatus("elem_kType");?>" onClick="ak_sel(this);">
											<label for="elem_krefractManual">Manual</label>
										</li>
									</ul>
									<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="exampd default">
								<table class="table borderless">
								<tr>
									<td class=" odcol">OD</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visAkOdK" >K</label></div>										
										<input type="text" id="elem_visAkOdK" name="elem_visAkOdK" value="<?php echo $elem_visAkOdK;?>"  onKeyUp="check2Blur(this,'K','elem_visAkOdSlash')" class="form-control <?php echo $this->vis_getStatus("elem_visAkOdK");?>" />
										</div>
									</td>
									<td class=" poscenter"><span>/</span></td>
									<td class="">
										<input type="text" name="elem_visAkOdSlash" id="elem_visAkOdSlash" value="<?php echo $elem_visAkOdSlash;?>"  onKeyUp="check2Blur(this,'slash','elem_visAkOdX')" class="form-control <?php echo $this->vis_getStatus("elem_visAkOdSlash");?>" />
									</td>
									<td class=" poscenter">
										<span>X</span>
									</td>
									<td class="">
										<input type="text" name="elem_visAkOdX" id="elem_visAkOdX" value="<?php echo $elem_visAkOdX;?>"  onKeyUp="check2Blur(this,'X','elem_visAkOsK')" class="form-control <?php echo $this->vis_getStatus("elem_visAkOdX");?>" />
									</td>
								</tr>
								<tr>
									<td class=" oscol">OS</td>
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visAkOsK" >K</label></div>
										<input type="text" id="elem_visAkOsK" name="elem_visAkOsK" value="<?php echo $elem_visAkOsK;?>"  onKeyUp="check2Blur(this,'K','elem_visAkOsSlash')" class="form-control <?php echo $this->vis_getStatus("elem_visAkOsK");?>" />
										</div>
									</td>
									<td class=" poscenter"><span>/</span></td>
									<td class="">
										<input type="text" name="elem_visAkOsSlash" id="elem_visAkOsSlash" value="<?php echo $elem_visAkOsSlash;?>"  onKeyUp="check2Blur(this,'slash','elem_visAkOsX')" class="form-control <?php echo $this->vis_getStatus("elem_visAkOsSlash");?>" />
									</td>
									<td class=" poscenter"><span>X</span></td>
									<td class="">
										<input type="text" name="elem_visAkOsX" id="elem_visAkOsX" value="<?php echo $elem_visAkOsX;?>"  onKeyUp="check2Blur(this,'X','elem_visAkDesc')" class="form-control <?php echo $this->vis_getStatus("elem_visAkOsX");?>" />
									</td>
								</tr>
								<tr>
									<td class="" colspan="6">
										<textarea name="elem_visAkDesc" class="form-control <?php echo $this->vis_getStatus("elem_visAkDesc");?>" rows="3"><?php echo $elem_visAkDesc; ?></textarea>
									</td>
								</tr>
								</table>
							</div>
						</div>
					</div>
					<!-- K -->	
					
					<!-- AR -->
					<div class="col-lg-3 col-lg-3-ar col-md-4 col-sm-4 " id="ar" >
						<div class="examsectbox">
							<div class="header">
								<div class="ar head-icons">
									<ul>
										<!--
										<li class="form-inline">
											<div class="kvalue img_acuity" onClick="callPopup('popup_ar_bat.php','popArBat','1085','650')">
											<img src="<?php echo $GLOBALS['webroot'];?>/library/images/ar_icon.png" alt="Acuities" />
											<!--<span class="glyphicon glyphicon-info-sign"></span>--*>
											</div>
										</li>
										-->
										
										<li>											
											<!--<h2 class="clickable" onClick="callPopup('','popAR','1250','760');" >AR</h2>-->										
											<ul class="nav nav-pills">
												<li class="<?php echo $tmp_dis_ar; ?>"><a  href="#pill_ar"><h2 class="clickable">AR</h2></a></li>
												<li class="<?php echo $tmp_dis_arc; ?>"><a  href="#pill_cyc_ar"><h2 class="clickable" data-toggle="tooltip" title="Cycloplegic AR">ARC</h2></a></li>												
											</ul>											
										</li>
										<li>	
											<a class="greenbtn_ar hvr-rectangle-out" onClick="opArUp();"><span class="glyphicon glyphicon-upload"></span></a>
											<input name="btnArPrvw" type="hidden" class="dff_button_sm" id="btnArPrvw" value="Image" />	
												
										</li>
										<li>
											<select name="elem_visArRefPlace" id="elem_visArRefPlace" data-toggle="tooltip" title="Place Of Refraction" class="form-control  <?php echo $this->vis_getStatus("elem_visArRefPlace");?>">
												<option value=""></option>
													<option value="Marco" <?php if($elem_visArRefPlace=="Marco")echo"selected";?>>Marco</option>
													<option value="Huvitz" <?php if($elem_visArRefPlace=="Huvitz")echo"selected";?>>Huvitz</option>
													<option value="Done by K's" <?php if($elem_visArRefPlace=="Done by K's")echo"selected";?>>Done by K's</option>
													<option value="Exam Lane" <?php if($elem_visArRefPlace=="Exam Lane")echo"selected";?>>Exam Lane</option>
											</select>
										</li>
										<li>
											<select name="elem_visArOdSel1" id="elem_visArOdSel1" class="form-control <?php if(empty($tmp_dis_ar)){ echo " hidden "; } ?> <?php echo $this->vis_getStatus("elem_visArOdSel1");?>" <?php echo $arPopupCall;?>>
												<option value=""></option>
												<option value="High" <?php echo ($elem_visArOdSel1 == "High") ? "selected" : "" ;?>>High</option>
												<option value="Med" <?php echo ($elem_visArOdSel1 == "Med") ? "selected" : "" ;?>>Med</option>
												<option value="Low" <?php echo ($elem_visArOdSel1 == "Low") ? "selected" : "" ;?>>Low</option>
											</select>
											<select name="elem_visCycArOdSel1" id="elem_visCycArOdSel1" class="form-control <?php if(!empty($tmp_dis_ar)){ echo " hidden "; } ?> <?php echo $this->vis_getStatus("elem_visCycArOdSel1");?>" <?php echo $arPopupCall;?>>
												<option value=""></option>
												<option value="High" <?php echo ($elem_visCycArOdSel1 == "High") ? "selected" : "" ;?>>High</option>
												<option value="Med" <?php echo ($elem_visCycArOdSel1 == "Med") ? "selected" : "" ;?>>Med</option>
												<option value="Low" <?php echo ($elem_visCycArOdSel1 == "Low") ? "selected" : "" ;?>>Low</option>
											</select>
										</li>
									</ul>
									<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
								</div>
							</div>
							<div class="clearfix"></div>
							
							<div class="exampd default tab-content" >
								<!-- pill AR -->
								<table class="table borderless tab-pane fade <?php echo $tmp_dis_ar." ".$ctmpAr;?>" id="pill_ar">
								<tr>
									<td class=" odcol">OD</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visArOdS">S</label></div>
											<input type="text" name="elem_visArOdS" id="elem_visArOdS" value="<?php echo $elem_visArOdS;?>" onBlur="justify2Decimal(this)"  onKeyUp="check2Blur(this,'S','elem_visArOdC')" class="form-control <?php echo $this->vis_getStatus("elem_visArOdS");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visArOdC">C</label></div>
											<input type="text" name="elem_visArOdC" id="elem_visArOdC" value="<?php echo $elem_visArOdC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visArOdA')" class="form-control <?php echo $this->vis_getStatus("elem_visArOdC");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visArOdA">A</label></div>
											<input type="text" name="elem_visArOdA" id="elem_visArOdA" value="<?php echo $elem_visArOdA;?>" onKeyUp="check2Blur(this,'A','elem_visArOsS')" class="form-control <?php echo $this->vis_getStatus("elem_visArOdA");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<!--
									<td class="">
										<select name="elem_visArOdSel1" id="elem_visArOdSel1" class="form-control <?php echo $this->vis_getStatus("elem_visArOdSel1");?>" <?php echo $arPopupCall;?>>
											<option value=""></option>
											<option value="High" <?php echo ($elem_visArOdSel1 == "High") ? "selected" : "" ;?>>High</option>
											<option value="Med" <?php echo ($elem_visArOdSel1 == "Med") ? "selected" : "" ;?>>Med</option>
											<option value="Low" <?php echo ($elem_visArOdSel1 == "Low") ? "selected" : "" ;?>>Low</option>
										</select>
									</td>
									-->
								</tr>
								<tr>
									<td class=" oscol">OS</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visArOsS">S</label></div>
											<input type="text" name="elem_visArOsS" id="elem_visArOsS" value="<?php echo $elem_visArOsS;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'S','elem_visArOsC')" class="form-control <?php echo $this->vis_getStatus("elem_visArOsS");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visArOsC">C</label></div>
											<input type="text" name="elem_visArOsC" id="elem_visArOsC" value="<?php echo $elem_visArOsC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visArOsA')" class="form-control <?php echo $this->vis_getStatus("elem_visArOsC");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visArOsA">A</label></div>
											<input type="text" name="elem_visArOsA" id="elem_visArOsA" value="<?php echo $elem_visArOsA;?>" onKeyUp="check2Blur(this,'A','elem_visArOsSel1')" class="form-control <?php echo $this->vis_getStatus("elem_visArOsA");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<!--
									<td class="">
										<select name="elem_visArOsSel1" id="elem_visArOsSel1" class="form-control  <?php echo $this->vis_getStatus("elem_visArOsA");?>" <?php echo $arPopupCall;?>>
											<option value=""></option>
											<option value="High" <?php echo ($elem_visArOsSel1 == "High") ? "selected" : "" ;?>>High</option>
											<option value="Med" <?php echo ($elem_visArOsSel1 == "Med") ? "selected" : "" ;?>>Med</option>
											<option value="Low" <?php echo ($elem_visArOsSel1 == "Low") ? "selected" : "" ;?>>Low</option>
										</select>
									</td>
									-->
								</tr>
								<tr>
									<td class="" colspan="4">
										<textarea name="elem_visArDesc" id="elem_visArDesc" class="form-control <?php echo $this->vis_getStatus("elem_visArDesc");?>" rows="3"><?php echo $elem_visArDesc;?></textarea>
									</td>
								</tr>
								</table>
								<!-- pill AR -->
								<!-- pill Cyc AR -->
								<table class="table borderless tab-pane fade <?php echo $tmp_dis_arc; ?>" id="pill_cyc_ar">
								<tr>
									<td class=" odcol">OD</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visCycArOdS">S</label></div>
											<input type="text" name="elem_visCycArOdS" id="elem_visCycArOdS" value="<?php echo $elem_visCycArOdS;?>" onBlur="justify2Decimal(this)"  onKeyUp="check2Blur(this,'S','elem_visCycArOdC')" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOdS");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visCycArOdC">C</label></div>
											<input type="text" name="elem_visCycArOdC" id="elem_visCycArOdC" value="<?php echo $elem_visCycArOdC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visCycArOdA')" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOdC");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visCycArOdA">A</label></div>
											<input type="text" name="elem_visCycArOdA" id="elem_visCycArOdA" value="<?php echo $elem_visCycArOdA;?>" onKeyUp="check2Blur(this,'A','elem_visCycArOsS')" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOdA");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<!--
									<td class="">
										<select name="elem_visCycArOdSel1" id="elem_visCycArOdSel1" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOdSel1");?>" <?php echo $arPopupCall;?>>
											<option value=""></option>
											<option value="High" <?php echo ($elem_visCycArOdSel1 == "High") ? "selected" : "" ;?>>High</option>
											<option value="Med" <?php echo ($elem_visCycArOdSel1 == "Med") ? "selected" : "" ;?>>Med</option>
											<option value="Low" <?php echo ($elem_visCycArOdSel1 == "Low") ? "selected" : "" ;?>>Low</option>
										</select>
									</td>
									-->
								</tr>
								<tr>
									<td class=" oscol">OS</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visCycArOsS">S</label></div>
											<input type="text" name="elem_visCycArOsS" id="elem_visCycArOsS" value="<?php echo $elem_visCycArOsS;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'S','elem_visCycArOsC')" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOsS");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visCycArOsC">C</label></div>
											<input type="text" name="elem_visCycArOsC" id="elem_visCycArOsC" value="<?php echo $elem_visCycArOsC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visCycArOsA')" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOsC");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<td class="">
										<div class="input-group plain">
											<div class="input-group-addon"><label for="elem_visCycArOsA">A</label></div>
											<input type="text" name="elem_visCycArOsA" id="elem_visCycArOsA" value="<?php echo $elem_visCycArOsA;?>" onKeyUp="check2Blur(this,'A','elem_visCycArOsSel1')" class="form-control <?php echo $this->vis_getStatus("elem_visCycArOsA");?>" <?php echo $arPopupCall;?>>
										</div>
									</td>
									<!--
									<td class="">
										<select name="elem_visCycArOsSel1" id="elem_visCycArOsSel1" class="form-control  <?php echo $this->vis_getStatus("elem_visCycArOsA");?>" <?php echo $arPopupCall;?>>
											<option value=""></option>
											<option value="High" <?php echo ($elem_visCycArOsSel1 == "High") ? "selected" : "" ;?>>High</option>
											<option value="Med" <?php echo ($elem_visCycArOsSel1 == "Med") ? "selected" : "" ;?>>Med</option>
											<option value="Low" <?php echo ($elem_visCycArOsSel1 == "Low") ? "selected" : "" ;?>>Low</option>
										</select>
									</td>
									-->
								</tr>
								<tr>
									<td class="" colspan="4">
										<textarea name="elem_visCycArDesc" id="elem_visCycArDesc" class="form-control <?php echo $this->vis_getStatus("elem_visCycArDesc");?>" rows="3"><?php echo $elem_visCycArDesc;?></textarea>
									</td>
								</tr>
								</table>
								<!-- pill Cyc AR -->
							</div>
						</div>
					</div>
					<!-- END AR -->
					<?php if(empty($ctmpBat) || empty($ctmpPam)){ ?>
					<!-- Bat/Pam -->
					<div class=" col-lg-3 col-lg-3-hd col-md-4 col-sm-4 " id="bat" >
						<div class="examsectbox">
							<div class="header">
								<div class="hdr form-inline">
									<ul>
										<li>
											<!--<h2 class="clickable" onClick="callPopup('','popPAM','1250','760');">PAM</h2>	
											<h2 class="clickable" onClick="callPopup('','popBAT','1250','760');">BAT</h2>-->	
											<ul class="nav nav-pills">
												<li class="<?php echo $tmp_dis_bat." ".$ctmpBat; ?>"><a  href="#pill_bat"><h2 class="clickable">BAT</h2></a></li>
												<li class="<?php echo $tmp_dis_pam." ".$ctmpPam; ?>"><a  href="#pill_pam"><h2 class="clickable">PAM</h2></a></li>												
											</ul>		
										</li>
										<li>
											<div class="input-group <?php if(!empty($tmp_dis_bat)){ echo " hidden "; } echo $ctmpPam; ?> ">
											<input type="text" name="elem_visPam" id="elem_visPam" value="<?php  echo $elem_visPam; ?>" class="form-control <?php echo $this->vis_getStatus("elem_visPam");?>" onclick="chkOther(this);" />											
											<?php echo $menu_visPam ; /*wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visPam",255,0,array("pdiv"=>"elem_visDisOdTxt1"));*/ ?>
											</div>
										</li>	
										<li>
											<div class="form-group <?php if(!empty($tmp_dis_bat)){ echo " hidden "; } echo $ctmpPam; ?> ">
												<label for="elem_visPamOdSel2">P<sub>SC</sub></label>
												<select name="elem_visPamOdSel2" id="elem_visPamOdSel2" onBlur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus("elem_visPamOdSel2");?>" <?php echo $distancePopupCall;?> >
												    <option value=""></option>
												    <option value="CC" <?php echo ($elem_visPamOdSel2 == "CC" || $elem_visPamOdSel2 == "") ? "selected" : "" ;?>>CC</option>
												    <option value="CL-S" <?php echo ($elem_visPamOdSel2 == "CL-S" ) ? "selected" : "" ;?>>CL-S</option>
												    <option value="GPCL" <?php echo ($elem_visPamOdSel2 == "GPCL" ) ? "selected" : "" ;?>>GPCL</option>
												</select> 
											</div>
										</li>	
									</ul>
									<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
								</div>	
							</div>						
							<div class="clearfix"></div>
							<div class="exampd default tab-content">
								<!-- Bat -->
								<table class="table borderless tab-pane fade  <?php echo $tmp_dis_bat." ".$ctmpBat; ?>" id="pill_bat">
								<tr>
									<td class=" odcol">OD</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatNlOd" >NL</label></div>										
										<input type="text" id="elem_visBatNlOd" name="elem_visBatNlOd" value="<?php echo $elem_visBatNlOd;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatNlOd");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>									
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatLowOd" >L</label></div>										
										<input type="text" id="elem_visBatLowOd" name="elem_visBatLowOd" value="<?php echo $elem_visBatLowOd;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatLowOd");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatMedOd" >M</label></div>										
										<input type="text" id="elem_visBatMedOd" name="elem_visBatMedOd" value="<?php echo $elem_visBatMedOd;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatMedOd");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>									
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatHighOd" >H</label></div>										
										<input type="text" id="elem_visBatHighOd" name="elem_visBatHighOd" value="<?php echo $elem_visBatHighOd;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatHighOd");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>	
									
								</tr>
								<tr>
									<td class=" oscol">OS</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatNlOs" >NL</label></div>										
										<input type="text" id="elem_visBatNlOs" name="elem_visBatNlOs" value="<?php echo $elem_visBatNlOs;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatNlOs");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>									
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatLowOs" >L</label></div>										
										<input type="text" id="elem_visBatLowOs" name="elem_visBatLowOs" value="<?php echo $elem_visBatLowOs;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatLowOs");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatMedOs" >M</label></div>										
										<input type="text" id="elem_visBatMedOs" name="elem_visBatMedOs" value="<?php echo $elem_visBatMedOs;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatMedOs");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>									
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatHighOs" >H</label></div>										
										<input type="text" id="elem_visBatHighOs" name="elem_visBatHighOs" value="<?php echo $elem_visBatHighOs;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatHighOs");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>
								</tr>
								<tr>
									<td class=" oucol">OU</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatNlOu" >NL</label></div>										
										<input type="text" id="elem_visBatNlOu" name="elem_visBatNlOu" value="<?php echo $elem_visBatNlOu;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatNlOu");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>									
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatLowOu" >L</label></div>										
										<input type="text" id="elem_visBatLowOu" name="elem_visBatLowOu" value="<?php echo $elem_visBatLowOu;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatLowOu");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>
									<td class="  ">	
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatMedOu" >M</label></div>										
										<input type="text" id="elem_visBatMedOu" name="elem_visBatMedOu" value="<?php echo $elem_visBatMedOu;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatMedOu");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>									
									<td class="">
										<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visBatHighOu" >H</label></div>										
										<input type="text" id="elem_visBatHighOu" name="elem_visBatHighOu" value="<?php echo $elem_visBatHighOu;?>"  class="form-control <?php echo $this->vis_getStatus("elem_visBatHighOu");?>" <?php echo $arPopupCall;?> />
										</div>
									</td>
								</tr>
								<tr>
									<td class="" colspan="5">
										<textarea name="elem_visBatDesc" class="form-control <?php echo $this->vis_getStatus("elem_visBatDesc");?>" rows="3"><?php echo $elem_visBatDesc; ?></textarea>
									</td>
								</tr>
								</table>
								<!-- Bat -->
								<!-- Pam -->
								<table class="table borderless tab-pane fade <?php echo $tmp_dis_pam." ".$ctmpPam; ?>" id="pill_pam">
								
								<tr>
									<td class=" od poscenter">OD</td>								
									<td class="">
										<div class="input-group">
											<input type="text" name="elem_visPamOdTxt1" id="elem_visPamOdTxt1" value="<?php  if($elem_visPamOdTxt1) echo $elem_visPamOdTxt1; else echo '20/';  ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visPamOdTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pamPopupCall;?>  />											
											<?php echo $menu_visPamOdTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOdTxt1",255,0,array("pdiv"=>"elem_visDisOsTxt1"));*/?>											
										</div>
									</td>
									<td class="">
										<div class="input-group">
											<input type="text" name="elem_visPamOdTxt2" id="elem_visPamOdTxt2" 
												value="<?php if($elem_visPamOdTxt2) echo $elem_visPamOdTxt2; else echo '20/'; ?>"
												class="form-control acuity <?php echo $this->vis_getStatus("elem_visPamOdTxt2");?>" 
												onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pamPopupCall;?> >
											<?php echo $menu_visPamOdTxt2 ; /* wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOdTxt2",0,0,array("pdiv"=>"divWorkView")); */ ?>
										</div>
									</td>									
								</tr>
								<tr>
									<td class=" os poscenter">OS</td>									
									<td class="">
										<div class="input-group">
											<input type="text" name="elem_visPamOsTxt1" id="elem_visPamOsTxt1" value="<?php  if($elem_visPamOsTxt1) echo $elem_visPamOsTxt1; else echo '20/';  ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visPamOsTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pamPopupCall;?>  />											
											<?php echo $menu_visPamOsTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOsTxt1",255,0,array("pdiv"=>"elem_visDisOsTxt1"));*/ ?>											
										</div>
									</td>
									<td class="">
										<div class="input-group">
											<input type="text" name="elem_visPamOsTxt2" id="elem_visPamOsTxt2" 
												value="<?php if($elem_visPamOsTxt2) echo $elem_visPamOsTxt2; else echo '20/'; ?>"
												class="form-control acuity <?php echo $this->vis_getStatus("elem_visPamOsTxt2");?>" 
												onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pamPopupCall;?> >
											<?php echo $menu_visPamOsTxt2 ; /* wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOsTxt2",0,0,array("pdiv"=>"divWorkView")); */ ?>
										</div>
									</td>									
								</tr>
								<tr>
									<td class=" ou poscenter">OU</td>									
									<td class="">
										<div class="input-group">
											<input type="text" name="elem_visPamOuTxt1" id="elem_visPamOuTxt1" value="<?php  if($elem_visPamOuTxt1) echo $elem_visPamOuTxt1; else echo '20/';  ?>" class="form-control acuity <?php echo $this->vis_getStatus("elem_visPamOuTxt1");?>" onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pamPopupCall;?>  />											
											<?php echo $menu_visPamOuTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOuTxt1",255,0,array("pdiv"=>"elem_visDisOuTxt1"));*/ ?>											
										</div>
									</td>
									<td class="">
										<div class="input-group">
											<input type="text" name="elem_visPamOuTxt2" id="elem_visPamOuTxt2" 
												value="<?php if($elem_visPamOuTxt2) echo $elem_visPamOuTxt2; else echo '20/'; ?>"
												class="form-control acuity <?php echo $this->vis_getStatus("elem_visPamOuTxt2");?>" 
												onblur="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pamPopupCall;?> >
											<?php echo $menu_visPamOuTxt ; /* wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOuTxt2",0,0,array("pdiv"=>"divWorkView")); */ ?>
										</div>
									</td>									
								</tr>	
								<tr>
									<td class="" colspan="3">										
										<textarea name="elem_pamDesc" class="form-control <?php echo $this->vis_getStatus("elem_pamDesc");?>"><?php echo $elem_pamDesc;?></textarea>
										<input type="hidden" name="elem_pamDescLF" value="<?php echo $elem_pamDescLF;?>">
									</td>
								</tr>
								</table>
								<!-- Pam -->
							</div>
						</div>
					</div>
					<!-- Bat/Pam -->
					<?php }//end if ?>
				</div>
			</div>
	<!-- END Distance Tab -->
	<?php 
		}//end distance 
		if(empty($ctmpPcWS)){ //PC
	?>
	
	<!-- PC Section -->
	<div class="collapse <?php echo $inclss_pc ; ?>"  id="PC"> <!-- role="tabpanel" class="tab-pane" -->
		<div class="row">
		<?php
		
		for($i=1;$i<=$len_pc;$i++){
		
			//---
			$indx=$i ;
			include($GLOBALS['incdir']."/chart_notes/view/vis_pc_inc.php");
		
		}//loop pc
		
		?>
		</div>
		<!--<span class="glyphicon glyphicon-plus btn_add_pc_mr " onclick="add_more_vision('pc')" data-toggle="tooltip" title="Add PC" ></span>-->
	</div>
	<!-- PC Section -->
	<?php
		} //End PC
		if(empty($ctmpMrWS)){ //MR	
	?>
	
	<!-- MR Section -->
		<div class="collapse <?php echo $inclss_mr ; ?>" id="MR"> <!-- role="tabpanel" class="tab-pane" -->
			<div class="row">
			<?php
			for($i=1; $i<=$len_mr; $i++){
			
				$indx = $i;
				include($GLOBALS['incdir']."/chart_notes/view/vis_mr_inc.php");				
			
			} //End MR
			?>	
			</div>
			<!--<span class="glyphicon glyphicon-plus btn_add_pc_mr " onclick="add_more_vision('mr')" data-toggle="tooltip" title="Add MR"></span>-->
		</div>
	<!-- End MR Section -->
	<?php
		} //End MR
		
		if(empty($ctmpLasik)){ //Lasik
	?>
	<!-- Lasik Section -->
		<div class="collapse <?php echo $inclss_lasik ; ?>" id="LASIK"> <!-- role="tabpanel" class="tab-pane" -->
			<div class="row">
			<?php
				//for($i=1; $i<=2; $i++){
				
					//$indx = $i;
					include($GLOBALS['incdir']."/chart_notes/view/vis_lasik_inc.php");
					
				//}	
			?>
			</div>			
		</div>
	<!-- End Lasik Section -->
	<?php
		} //End Lasik		
	?>
	
	<!-- OtherVisionExams Section -->
		<div class="collapse <?php echo $inclss_other_vis_exm ; ?>" id="OtherVisionExams">	<!-- role="tabpanel" class="tab-pane" -->
			<div class="row">
				
				<!-- CP Control -->
				<div class="col-lg-2 col-md-4 col-sm-6 <?php echo $ctmpIcpClr;?>" id="dv_cpctrl" ><?php echo $htm_cp_control; ?></div>
				<!-- CP Control -->
				<!-- Stereopsis -->
				<div class=" col-lg-1 col-md-4 col-sm-6 <?php echo $ctmpStereo;?>" id="dv_stereopsis" ><?php echo $htm_stereopsis;?></div>
				<!-- Stereopsis -->			
				
				<!-- Worth for Dot -->
				<div class="col-lg-2 col-md-4 col-sm-6 <?php echo $ctmpW4Dot;?>" id="dv_w4dot" ><?php echo $htm_worth_for_dot; ?>
					
				</div>
				<!-- Worth for Dot -->
				<!-- Retinoscopy -->
				<div class="col-lg-3 col-lg-3-ret col-md-4 col-sm-6 <?php echo $ctmpRetino;?>" >
					<div class="examsectbox">
						<div class="header">
							<div class="ar head-icons">
								<ul>
									<li>
										<h2>Retinoscopy</h2>							
									</li>
								</ul>
								<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol">OD</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visExoOdS">S</label></div>
										<input type="text" name="elem_visExoOdS" id="elem_visExoOdS" value="<?php echo $elem_visExoOdS;?>" onBlur="justify2Decimal(this)"  onKeyUp="check2Blur(this,'S','elem_visExoOdC')" class="form-control <?php echo $this->vis_getStatus("elem_visExoOdS");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visExoOdC">C</label></div>
										<input type="text" name="elem_visExoOdC" id="elem_visExoOdC" value="<?php echo $elem_visExoOdC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visExoOdA')" class="form-control <?php echo $this->vis_getStatus("elem_visExoOdC");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visExoOdA">A</label></div>
										<input type="text" name="elem_visExoOdA" id="elem_visExoOdA" value="<?php echo $elem_visExoOdA;?>" onKeyUp="check2Blur(this,'A','elem_visExoOsS')" class="form-control <?php echo $this->vis_getStatus("elem_visExoOdA");?>" >
									</div>
								</td>									
							</tr>
							<tr>
								<td class=" oscol">OS</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visExoOsS">S</label></div>
										<input type="text" name="elem_visExoOsS" id="elem_visExoOsS" value="<?php echo $elem_visExoOsS;?>" onBlur="justify2Decimal(this)"  onKeyUp="check2Blur(this,'S','elem_visExoOsC')" class="form-control <?php echo $this->vis_getStatus("elem_visExoOsS");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visExoOsC">C</label></div>
										<input type="text" name="elem_visExoOsC" id="elem_visExoOsC" value="<?php echo $elem_visExoOsC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visExoOsA')" class="form-control <?php echo $this->vis_getStatus("elem_visExoOsC");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visExoOsA">A</label></div>
										<input type="text" name="elem_visExoOsA" id="elem_visExoOsA" value="<?php echo $elem_visExoOsA;?>" onKeyUp="check2Blur(this,'A','elem_visCycloOdS')" class="form-control <?php echo $this->vis_getStatus("elem_visExoOsA");?>" >
									</div>
								</td>									
							</tr>
							</table>								
						</div>							
					</div>
				</div>
				<!-- Retinoscopy -->
				<!-- Cycloplegic Retino -->
				<div class="col-lg-3 col-lg-3-ret col-md-4 col-sm-6 <?php echo $ctmpCycRetino;?>" >
					<div class="examsectbox">
						<div class="header">
							<div class="ar head-icons">
								<ul>
									<li>
										<h2>Cycloplegic Retino</h2>							
									</li>
								</ul>
								<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol">OD</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visCycloOdS">S</label></div>
										<input type="text" name="elem_visCycloOdS" id="elem_visCycloOdS" value="<?php echo $elem_visCycloOdS;?>" onBlur="justify2Decimal(this)"  onKeyUp="check2Blur(this,'S','elem_visCycloOdC')" class="form-control <?php echo $this->vis_getStatus("elem_visCycloOdS");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visCycloOdC">C</label></div>
										<input type="text" name="elem_visCycloOdC" id="elem_visCycloOdC" value="<?php echo $elem_visCycloOdC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visCycloOdA')" class="form-control <?php echo $this->vis_getStatus("elem_visCycloOdC");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visCycloOdA">A</label></div>
										<input type="text" name="elem_visCycloOdA" id="elem_visCycloOdA" value="<?php echo $elem_visCycloOdA;?>" onKeyUp="check2Blur(this,'A','elem_visCycloOsS')" class="form-control <?php echo $this->vis_getStatus("elem_visCycloOdA");?>" >
									</div>
								</td>									
							</tr>
							<tr>
								<td class=" oscol">OS</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visCycloOsS">S</label></div>
										<input type="text" name="elem_visCycloOsS" id="elem_visCycloOsS" value="<?php echo $elem_visCycloOsS;?>" onBlur="justify2Decimal(this)"  onKeyUp="check2Blur(this,'S','elem_visCycloOsC')" class="form-control <?php echo $this->vis_getStatus("elem_visCycloOsS");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visCycloOsC">C</label></div>
										<input type="text" name="elem_visCycloOsC" id="elem_visCycloOsC" value="<?php echo $elem_visCycloOsC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','elem_visCycloOsA')" class="form-control <?php echo $this->vis_getStatus("elem_visCycloOsC");?>" >
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="elem_visCycloOsA">A</label></div>
										<input type="text" name="elem_visCycloOsA" id="elem_visCycloOsA" value="<?php echo $elem_visCycloOsA;?>" onKeyUp="check2Blur(this,'A','elem_visRetPD')" class="form-control <?php echo $this->vis_getStatus("elem_visCycloOsA");?>" >
									</div>
								</td>
							</tr>
							</table>	
						</div>
					</div>
				</div>
				<!-- Cycloplegic Retino -->
				<!-- Exophthalmometer -->
				<div class="col-lg-2 col-md-4 col-sm-6 <?php echo $ctmpExophth;?>" >
					<div class="examsectbox">
						<div class="header">
							<div class="ar head-icons">
								<ul>
									<li>
										<h2>Exophthalmometer</h2>							
									</li>
								</ul>
								<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">	
							<table class="table borderless">
							<tr>									
								<td></td>
								<td class="text-right">	
									<label for="dis_pd">PD</label>
								</td>
								<td>	
									<input type="text" name="elem_visRetPD" id="dis_pd" value="<?php echo $elem_visRetPD;?>" class="form-control <?php echo $this->vis_getStatus("elem_visRetPD");?>" >										
								</td>
								<td>										
								</td>
							</tr>
							<tr>
								<td>	
									<label class="od" for="elem_visRetOd">OD</label>
								</td>
								<td>	
									<input type="text" name="elem_visRetOd" id="elem_visRetOd" value="<?php echo $elem_visRetOd;?>" class="form-control <?php echo $this->vis_getStatus("elem_visRetOd");?>" >
								</td>
								<td class="text-right">
									<label class="os" for="elem_visRetOs">OS</label>
								</td>
								<td>	
									<input type="text" name="elem_visRetOs" id="elem_visRetOs" value="<?php echo $elem_visRetOs;?>" class="form-control <?php echo $this->vis_getStatus("elem_visRetOs");?>" >
								</td>
							</tr>
							</table>	
						</div>
					</div>
				</div>
				<!-- Exophthalmometer -->
				
			</div>				
		</div>
	<!-- End OtherVisionExams Section -->
	<!--	
		</div>
	</div>
	-->
	<div class="clearfix"></div>
</div>
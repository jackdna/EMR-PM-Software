<?php

$indx2 = ($indx>2) ? "_".$indx : "" ;
$sfx = ($indx>=2) ? "Other" : "" ;			

$tmp_row_mr_1="row_mr_".$indx;
$tmp_providerName="elem_providerName".$sfx.$indx2;
$tmp_providerId="elem_providerId".$sfx.$indx2;

$tmp_mrList1="elem_mrList".$indx;

$tmp_mrNoneGiven1="elem_mrNoneGiven".$indx;
$tmp_mr_pres_dt_1="elem_mr_pres_dt_".$indx;

$tmp_mr_type1="elem_mr_type".$indx;
$tmp_mrCyclopegic1="elem_mrCyclopegic".$indx;

$tmp_visMrOdS="elem_visMr".$sfx."OdS".$indx2;
$tmp_visMrOdC="elem_visMr".$sfx."OdC".$indx2;
$tmp_visMrOdA="elem_visMr".$sfx."OdA".$indx2;
$tmp_visMrOdTxt1="elem_visMr".$sfx."OdTxt1".$indx2;
$tmp_visMrOdAdd="elem_visMr".$sfx."OdAdd".$indx2;
$tmp_visMrOdTxt2="elem_visMr".$sfx."OdTxt2".$indx2;

$tmp_visMrOsS="elem_visMr".$sfx."OsS".$indx2;
$tmp_visMrOsC="elem_visMr".$sfx."OsC".$indx2;
$tmp_visMrOsA="elem_visMr".$sfx."OsA".$indx2;
$tmp_visMrOsTxt1="elem_visMr".$sfx."OsTxt1".$indx2;
$tmp_visMrOuTxt1="elem_visMr".$sfx."OuTxt1".$indx2;
$tmp_visMrOsAdd="elem_visMr".$sfx."OsAdd".$indx2;
$tmp_visMrOsTxt2="elem_visMr".$sfx."OsTxt2".$indx2;

$tmp_visMrDesc="elem_visMrDesc".$sfx.$indx2;
$tmp_visMrDescLF="elem_visMrDesc".$sfx."LF".$indx2;


$tmp_visMrOdSel2="elem_visMr".$sfx."OdSel2".$indx2;
$tmp_visMrOdSel2Vision="elem_visMr".$sfx."OdSel2Vision".$indx2;
$tmp_visMrOsSel2="elem_visMr".$sfx."OsSel2".$indx2;
$tmp_visMrOsSel2Vision="elem_visMr".$sfx."OsSel2Vision".$indx2;

$tmp_mrPrism1="elem_mrPrism".$indx;
$tmp_visMrOdP="elem_visMr".$sfx."OdP".$indx2;
$tmp_visMrOdSel1="elem_visMr".$sfx."OdSel1".$indx2;
$tmp_visMrOdSlash="elem_visMr".$sfx."OdSlash".$indx2;
$tmp_visMrOdPrism="elem_visMr".$sfx."OdPrism".$indx2;

$tmp_visMrOsP="elem_visMr".$sfx."OsP".$indx2;
$tmp_visMrOsSel1="elem_visMr".$sfx."OsSel1".$indx2;
$tmp_visMrOsSlash="elem_visMr".$sfx."OsSlash".$indx2;
$tmp_visMrOsPrism="elem_visMr".$sfx."OsPrism".$indx2;

$tmp_visMrPrismDesc_1="elem_visMrPrismDesc_".$indx;

//--
$tmp_menu_visMrOdTxt1="menu_visMr".$sfx."OdTxt1".$indx2;
$tmp_menu_visMrOdTxt2="menu_visMr".$sfx."OdTxt2".$indx2;
$tmp_menu_visMrOsTxt1="menu_visMr".$sfx."OsTxt1".$indx2;
$tmp_menu_visMrOuTxt1="menu_visMr".$sfx."OuTxt1".$indx2;
$tmp_menu_visMrOsTxt2="menu_visMr".$sfx."OsTxt2".$indx2;
$tmp_menu_visMrOdSel2Vision="menu_visMr".$sfx."OdSel2Vision".$indx2;
$tmp_menu_visMrOsSel2Vision="menu_visMr".$sfx."OsSel2Vision".$indx2;
//--

//display glph + prism
$tmp_glph_dis = trim($$tmp_visMrOdP.$$tmp_visMrOdSel1.$$tmp_visMrOdSlash.$$tmp_visMrOdPrism.
		$$tmp_visMrOsP.$$tmp_visMrOsSel1.$$tmp_visMrOsSlash.$$tmp_visMrOsPrism.$$tmp_visMrPrismDesc_1.
		$$tmp_visMrOdSel2.$$tmp_visMrOsSel2.$$tmp_visMrOdSel2Vision.$$tmp_visMrOsSel2Vision);
$tmp_glph_dis = str_replace("20/","",$tmp_glph_dis);
if(!empty($tmp_glph_dis) && $tmp_glph_dis!="20/"){$tmp_glph_dis=" in ";}else{$tmp_glph_dis="";}

//---

$t_ctmpMr = "ctmpMr".$indx; //template

//Label MR --
$lbl_mr_sec = "MR ".$indx;
if($$tmp_mr_type1 == "cycloplegic"||$$tmp_mrCyclopegic1 == "1"){
	$lbl_mr_sec = "CR ".$indx;
}else if($$tmp_mr_type1 == "Over Refraction"){
	$lbl_mr_sec = "OR ".$indx;
}else if($$tmp_mr_type1 == "trial frame"){
	$lbl_mr_sec = "TF ".$indx;
}else if($$tmp_mr_type1 == "Final"){
	$lbl_mr_sec = "FR ".$indx;
}else if($$tmp_mr_type1 == "Outside Rx"){
	$lbl_mr_sec = "ORx ".$indx;
}
//--

?>
<!-- MR 1 -->
<div id="<?php echo $tmp_row_mr_1 ; ?>" class="<?php echo $$t_ctmpMr; ?>">

	<!-- MR Block -->
	<div class="col-lg-6 col-md-12">
		<div class="row">
			<div class="col-lg-12 col-lg-mr-main-hd">
			<!-- main -->
		<div class="examsectbox">
			<div class="header">
				<div class="mrtab head-icons form-inline">
					<ul>
						<!--
						<li class="form-inline">
							<div class="kvalue img_acuity"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/ar_icon.png" alt="" onClick="callPopup('popup_mr.php','popMR','1345','685');" /></div>
						</li>
						-->						
						<li>
							<h2 id="h2_mr_id<?php echo $indx; ?>" class="clickable" onClick="callPopup('','popMR<?php echo $indx; ?>','1345','685');"><?php echo $lbl_mr_sec; ?></h2>
						</li>
						<li class="mr-physician">
							<input type="text" name="<?php echo $tmp_providerName ; ?>" value="<?php echo $$tmp_providerName; ?>" data-toggle="tooltip" title="<?php echo $$tmp_providerName; ?>" class="form-control proname <?php echo $this->vis_getStatus($tmp_providerName);?>" readonly  />
							<input type="hidden" name="<?php echo $tmp_providerId ; ?>" value="<?php echo $$tmp_providerId;?>">
						</li>
						<!--
						<li class="mr-list">
							<select name="<?php echo $tmp_mrList1 ; ?>" class="form-control" onChange="copy(this.value)" title="Copy From">
								<option value="">copy</option>
								<option value="Pc">PC 1</option>
								<option value="PC 2">PC 2</option>
								<option value="PC 3">PC 3</option>
								<option value="Ar">AR</option>
								<option value="MR 2">MR 2</option>
								<option value="MR 3">MR 3</option>
							</select>
						</li>
						-->
						<li class="given">
							<?php
							//echo "<br/>".$tmp_mrNoneGiven1." == MR ".$indx;
							$elem_mrNoneGiven_tmp = (!empty($elem_mrNoneGiven)&&strpos($elem_mrNoneGiven,"MR ".$indx)!==false) || ($$tmp_mrNoneGiven1 == "MR ".$indx) ? "checked=\"checked\"" : "";
							?>
							<input type="checkbox" id="<?php echo $tmp_mrNoneGiven1 ; ?>" name="<?php echo $tmp_mrNoneGiven1 ; ?>" value="MR <?php echo $indx; ?>" <?php echo $elem_mrNoneGiven_tmp; ?> class="clickableLable <?php echo $this->vis_getStatus($tmp_mrNoneGiven1);?>">
							<label for="<?php echo $tmp_mrNoneGiven1 ; ?>">Given</label>
						</li>
						<li class="date-field">							
								<input type="text" id="<?php echo $tmp_mr_pres_dt_1 ; ?>" name="<?php echo $tmp_mr_pres_dt_1 ; ?>" class="form-control datepicker" value="<?php echo $$tmp_mr_pres_dt_1; ?>" readonly onchange="chk_mr_given(this)" >
						</li>
						<li class="mr-type">
							<select name="<?php echo $tmp_mr_type1 ; ?>" id="<?php echo $tmp_mr_type1 ; ?>" onChange="checkValue(<?php echo $indx; ?>, this.value)" class="form-control" >
								<option value="">None</option>
								<!--<option value="ar" <?php echo ($$tmp_mr_type1 == "ar") ? "selected" : "" ;?>>A/R</option>-->
								<option value="cycloplegic" <?php echo ($$tmp_mr_type1 == "cycloplegic"||$$tmp_mrCyclopegic1 == "1") ? "selected" : "" ;?>>Cycloplegic</option>
								<!--<option value="cycloplegic ar" <?php echo ($$tmp_mr_type1 == "cycloplegic ar") ? "selected" : "" ;?>>Cycloplegic A/R</option>-->
								<option value="Over Refraction" <?php echo ($$tmp_mr_type1 == "Over Refraction") ? "selected" : "" ;?>>Over Refraction</option>								
								<!--<option value="mr 1" <?php echo ($$tmp_mr_type1 == "mr 1") ? "selected" : "" ;?>>MR 1</option>
								<option value="mr 2" <?php echo ($$tmp_mr_type1 == "mr 2") ? "selected" : "" ;?>>MR 2</option>
								<option value="given mr" <?php echo ($$tmp_mr_type1 == "given mr") ? "selected" : "" ;?>>Given MR</option>-->
								<option value="trial frame" <?php echo ($$tmp_mr_type1 == "trial frame") ? "selected" : "" ;?>>Trial Frame</option>
								<option value="Final" <?php echo ($$tmp_mr_type1 == "Final") ? "selected" : "" ;?>>Final</option>
								<option value="Outside Rx" <?php echo ($$tmp_mr_type1 == "Outside Rx") ? "selected" : "" ;?>>Outside Rx</option>
							</select>
						</li>
						<!--
						<li class="cycloplegic">
							<input type="checkbox" name="<?php echo $tmp_mrCyclopegic1 ; ?>" value="1" id="<?php echo $tmp_mrCyclopegic1 ; ?>" <?php echo ($$tmp_mrCyclopegic1 == "1") ? "checked=\"checked\"" : "" ;?> class="clickableLable <?php echo $this->vis_getStatus($tmp_mrCyclopegic1);?>">
							<label for="<?php echo $tmp_mrCyclopegic1 ; ?>" data-toggle="tooltip" title="Cycloplegic" ><span class="hdn-hd">Cycloplegic</span><span class="swn-hd">CP</span></label>
							
						</li>
						-->
						<li>
							<!-- MR List -->
							<select name="elem_mrList1" onChange="copyPcMr(this)" onclick="stopClickBubble();" data-toggle="tooltip" title="Copy From" data-mr="<?php echo $indx; ?>" class="form-control" >
								<!--<option value="">copy</option>								
								<option value="AR">AR</option>
								<option value="ARC">ARC</option>-->
								<?php
								echo str_replace("<option value=\"MR ".$indx."\">MR".$indx."</option>","", $str_opts_copy);
								?>								
							</select>
							<!-- MR List -->
						</li>
						<li >
						
							<button class="btn btn-primary btn-sm dis-mr-btn-glprism" type="button" data-toggle="collapse" data-target="#mr_gl_ph<?php echo $indx;?>, #mr_prism<?php echo $indx;?>" >GL/Prism</button>
							<button class="btn btn-primary btn-sm dis-mr-btn-cyl" type="button" id="lbl_showTransps<?php echo $indx; ?>" onClick="showTranspose('<?php echo $indx; ?>')" >+/-Cyl</button>
							<button class="btn btn-default btn-sm dis-mr-btn-print" type="button" onclick="printMr('', <?php echo $indx; ?>);" data-toggle="tooltip" title="Print MR <?php echo $indx; ?>" ><span class="glyphicon glyphicon-print"></span></button>
							<button class="btn btn-default btn-sm dis-mr-btn-clr" type="button" onClick="clearMr('<?php echo $indx; ?>')" data-toggle="tooltip" title="Clear MR <?php echo $indx; ?>" ><span class="glyphicon glyphicon-refresh"></span></button>
							
							<div class="dropdown dis-mr-cog ">
								<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span></button>
								<ul class="dropdown-menu dropdown-menu-right" data-mr="<?php echo $indx; ?>">
									<li class="hdn-hd"><a href="javascript:void(0);" data-toggle="collapse" data-target="#mr_gl_ph<?php echo $indx;?>, #mr_prism<?php echo $indx;?>">GL/PH/Prism</a></li>
									<li><a href="javascript:void(0);" id="lbl_showTransps<?php echo $indx; ?>" onClick="showTranspose('<?php echo $indx; ?>')">+/-Cyl</a></li>								
									<li><a href="javascript:void(0);" onclick="printMr('', <?php echo $indx; ?>);" data-toggle="tooltip" title="Print MR <?php echo $indx; ?>"><span class="glyphicon glyphicon-print"></span></a></li>
									<li><a href="javascript:void(0);" onClick="clearMr('<?php echo $indx; ?>')" data-toggle="tooltip" title="Clear MR <?php echo $indx; ?>"><span class="glyphicon glyphicon-refresh"></span></a></li>
									
								</ul>
							</div>
						</li>
					</ul>
					
					
					<!-- Add btns -->
					<?php  if($indx>2 && $indx % 2==0){ ?>
					<span class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 hdn-hd" onclick="add_more_vision('mr')" data-toggle="tooltip" title="Add MR"></span>
					<span class="glyphicon glyphicon-remove btn_add_pc_mr hdn-hd" onclick="clearMr('<?php echo $indx; ?>',1)" data-toggle="tooltip" title="Remove MR"></span>
					<?php }else if($indx % 2==0){ ?>
					<span class="glyphicon glyphicon-plus btn_add_pc_mr hdn-hd" onclick="add_more_vision('mr')" data-toggle="tooltip" title="Add MR"></span>	
					<?php } ?>
					<!-- NC -->
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
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOdS ; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visMrOdS ; ?>" id="<?php echo $tmp_visMrOdS ; ?>" value="<?php echo $$tmp_visMrOdS;?>" onBlur="justify2Decimal(this);setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visMrOdC ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOdS);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOdC ; ?>">C</label><strong></strong></div>
								<input type="text" name="<?php echo $tmp_visMrOdC ; ?>" id="<?php echo $tmp_visMrOdC ; ?>" value="<?php echo $$tmp_visMrOdC;?>" onBlur="justify2Decimal(this);setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visMrOdA ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOdC);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOdA ; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visMrOdA ; ?>" id="<?php echo $tmp_visMrOdA ; ?>" value="<?php echo $$tmp_visMrOdA;?>" onBlur="setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visMrOdTxt1 ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOdA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<input type="text" name="<?php echo $tmp_visMrOdTxt1 ; ?>" id="<?php echo $tmp_visMrOdTxt1 ; ?>" value="<?php echo $$tmp_visMrOdTxt1; ?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOdTxt1);?>" onBlur="changeMr(this);" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $mrPopupCall;?>>
								
								<?php echo $$tmp_menu_visMrOdTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis7","elem_visMrOdTxt1",164,0,array("pdiv"=>"elem_visMrOdTxt1"));*/ ?>
								
							</div>
						</td>
						<td class=" text-center">
							<div class="inexalab oucol">OU</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOdAdd ; ?>">Add</label></div>
								<input type="text" name="<?php echo $tmp_visMrOdAdd ; ?>" id="<?php echo $tmp_visMrOdAdd ; ?>" value="<?php echo $$tmp_visMrOdAdd;?>" onblur="addArthSign(this);changeMr(this); setOs(this);" onKeyUp="check2Blur(this,'Add','<?php echo $tmp_visMrOdTxt2 ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOdAdd);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<input type="text" name="<?php echo $tmp_visMrOdTxt2 ; ?>" id="<?php echo $tmp_visMrOdTxt2 ; ?>" value="<?php echo $$tmp_visMrOdTxt2; ?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOdTxt2);?>" onBlur="changeMr(this);" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $mrPopupCall;?>>
								
								<?php echo $$tmp_menu_visMrOdTxt2 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesMrNear7","elem_visMrOdTxt2",160,0,array("pdiv"=>"elem_visMrOdTxt2"));*/ ?>
								
							</div>
						</td>
					</tr>
					<tr>
						<td class=" oscol">OS</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOsS ; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visMrOsS ; ?>" id="<?php echo $tmp_visMrOsS ; ?>" value="<?php echo $$tmp_visMrOsS;?>" onblur="justify2Decimal(this);setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onkeyup="check2Blur(this,'S','<?php echo $tmp_visMrOsC ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOsS);?>" <?php echo $mrPopupCall;?> />
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOsC ; ?>">C</label></div>
								<input type="text" name="<?php echo $tmp_visMrOsC ; ?>" id="<?php echo $tmp_visMrOsC ; ?>" value="<?php echo $$tmp_visMrOsC;?>" onBlur="justify2Decimal(this);setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visMrOsA ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOsC);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOsA ; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visMrOsA ; ?>" id="<?php echo $tmp_visMrOsA ; ?>" value="<?php echo $$tmp_visMrOsA;?>" onBlur="setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visMrOsTxt1 ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOsA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group">
								<input type="text" name="<?php echo $tmp_visMrOsTxt1 ; ?>" id="<?php echo $tmp_visMrOsTxt1 ; ?>" value="<?php echo $$tmp_visMrOsTxt1; ?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOsTxt1);?>" onBlur="changeMr(this);" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $mrPopupCall;?>>
								
								<?php echo $$tmp_menu_visMrOsTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis8","elem_visMrOsTxt1",164,0,array("pdiv"=>"elem_visMrOsTxt1"));*/ ?>
								
							</div>
						</td>
						<td class="">
							<div class="input-group">
								<input type="text" name="<?php echo $tmp_visMrOuTxt1 ; ?>" id="<?php echo $tmp_visMrOuTxt1 ; ?>" value="<?php echo $$tmp_visMrOuTxt1; ?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOuTxt1);?>" onBlur="changeMr(this);" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $mrPopupCall;?>>
								
								<?php echo $$tmp_menu_visMrOuTxt1 ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis8","elem_visMrOuTxt1",284,0,array("pdiv"=>"elem_visMrOuTxt1"));*/ ?>
								
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visMrOsAdd ; ?>">Add</label></div>
								<input type="text" name="<?php echo $tmp_visMrOsAdd ; ?>" id="<?php echo $tmp_visMrOsAdd ; ?>" value="<?php echo $$tmp_visMrOsAdd;?>" onBlur="addArthSign(this);changeMr(this);" onKeyUp="check2Blur(this,'Add','<?php echo $tmp_visMrOsTxt2 ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visMrOsAdd);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group">
								<input type="text" name="<?php echo $tmp_visMrOsTxt2 ; ?>" id="<?php echo $tmp_visMrOsTxt2 ; ?>" value="<?php echo $$tmp_visMrOsTxt2; ?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOsTxt2);?>" onBlur="changeMr(this);" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $mrPopupCall;?>>
								
								<?php echo $$tmp_menu_visMrOsTxt2 ; /*wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesMrNear8","elem_visMrOsTxt2",160,0,array("pdiv"=>"elem_visMrOsTxt2"));*/ ?>
								
							</div>
						</td>
					</tr>
					<tr>						
						<td class="" colspan="8">
							<textarea id="<?php echo $tmp_visMrDesc ; ?>" name="<?php echo $tmp_visMrDesc ; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visMrDesc);?>" onkeyup="setTaPlanHgt(this.id);"><?php echo $$tmp_visMrDesc;?></textarea>
							<input type="hidden" name="<?php echo $tmp_visMrDescLF ; ?>" value="<?php echo $$tmp_visMrDescLF;?>">
						</td>
					</tr>	
				</table>								
			</div>
		</div>
			<!-- main -->
			</div>
			<!--
			<div id="mr_more<?php echo $indx;?>" class="collapse">
				<div class="row">
				</div>
			</div>
			-->
			<!-- MR 1 -GL/PH -->
				<div id="mr_gl_ph<?php echo $indx;?>" class="col-lg-4 col-lg-glph-hd col-md-4 col-sm-4 collapse <?php echo $tmp_glph_dis; ?>">
					<div class="examsectbox dv_glph_hdr">
						<div class="header">
							<div>
								<ul>
									<li>
										<input type="checkbox" id="<?php echo $tmp_visMrOdSel2."_GL" ; ?>" name="<?php echo $tmp_visMrOdSel2 ; ?>" value="GL" <?php echo ($$tmp_visMrOdSel2 == "GL") ? "checked" : "" ; ?> class="clickableLable <?php echo $this->vis_getStatus($tmp_visMrOdSel2);?>">
										<label for="<?php echo $tmp_visMrOdSel2."_GL" ; ?>">GL</label>
									</li>
									<li>
										<input type="checkbox" id="<?php echo $tmp_visMrOdSel2."_PH" ; ?>" name="<?php echo $tmp_visMrOdSel2 ; ?>" value="PH" <?php echo ($$tmp_visMrOdSel2 == "PH") ? "checked" : "" ; ?> class="clickableLable <?php echo $this->vis_getStatus($tmp_visMrOdSel2);?>">
										<label for="<?php echo $tmp_visMrOdSel2."_PH" ; ?>">PH</label>
									</li>
									
								</ul>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol visible-sm-block">OD</td>
								<!--
								<td class="">
									<select name="<?php //echo $tmp_visMrOdSel2 ; ?>" id="<?php //echo $tmp_visMrOdSel2 ; ?>" data-toggle="tooltip" title="GL/PH" onBlur="setActuity(this);setOs(this)" class="form-control  <?php echo $this->vis_getStatus($tmp_visMrOdSel2);?>">
										<option value=""></option>
										<option value="PH" <?php //echo ($$tmp_visMrOdSel2 == "PH") ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php //echo ($$tmp_visMrOdSel2 == "GL") ? "selected" : "" ;?>>GL</option>
									</select>
								</td>
								-->
								<td class="">
									<div class="input-group">
										<input type="text" name="<?php echo $tmp_visMrOdSel2Vision ; ?>" id="<?php echo $tmp_visMrOdSel2Vision ; ?>" value="<?php echo $$tmp_visMrOdSel2Vision;?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOdSel2Vision);?>" onBlur="setActuity(this);" onfocus="setCursorAtEnd(this);">
										
										<?php echo $$tmp_menu_visMrOdSel2Vision ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis9","elem_visMrOdSel2Vision",164,0,array("pdiv"=>"elem_visMrOdSel2Vision"));*/ ?>
										
									</div>
								</td>
							</tr>
							<tr>
								<td class=" oscol visible-sm-block">OS</td>
								<!--
								<td class="">
									<select name="<?php //echo $tmp_visMrOsSel2 ; ?>" id="<?php //echo $tmp_visMrOsSel2 ; ?>" data-toggle="tooltip" title="GL/PH" onBlur="setActuity(this);" class="form-control  <?php echo $this->vis_getStatus($tmp_visMrOsSel2);?>">
										<option value=""></option>
										<option value="PH" <?php //echo ($$tmp_visMrOsSel2 == "PH") ? "selected" : "" ;?>>PH</option>
										<option value="GL" <?php //echo ($$tmp_visMrOsSel2 == "GL") ? "selected" : "" ;?>>GL</option>
									</select>
								</td>
								-->
								<td class="">
									<div class="input-group">
										<input type="text" name="<?php echo $tmp_visMrOsSel2Vision ; ?>" id="<?php echo $tmp_visMrOsSel2Vision ; ?>" value="<?php echo $$tmp_visMrOsSel2Vision;?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visMrOsSel2Vision);?>" onBlur="setActuity(this);" onfocus="setCursorAtEnd(this);">
										
										<?php echo $$tmp_menu_visMrOsSel2Vision ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis9","elem_visMrOsSel2Vision",164,0,array("pdiv"=>"elem_visMrOsSel2Vision"));*/ ?>
										
									</div>
								</td>
							</tr>
							</table>
						</div>
					</div>
				</div>
			<!-- END MR 1 -GL/PH -->
<!-- MR 1- Prism -->
				<div id="mr_prism<?php echo $indx;?>" class="col-lg-8 col-lg-mr-prsm-hd col-md-8 col-sm-8 collapse <?php echo $tmp_glph_dis; ?>">
					<div class="examsectbox">
						<div class="header">
							<div>
								<ul >
									<li>
										<h2>Prism</h2>
										<input type="hidden" id="<?php echo $tmp_mrPrism1 ; ?>" name="<?php echo $tmp_mrPrism1 ; ?>" value="<?php echo ($$tmp_mrPrism1);?>" />
									</li>									
								</ul>
								
								<!-- Add btns -->
								<?php  if($indx>2 && $indx % 2==0){ ?>
								<span class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 swn-hd" onclick="add_more_vision('mr')" data-toggle="tooltip" title="Add MR"></span>
								<span class="glyphicon glyphicon-remove btn_add_pc_mr swn-hd" onclick="clearMr('<?php echo $indx; ?>',1)" data-toggle="tooltip" title="Remove MR"></span>
								<?php }else if($indx % 2==0){ ?>
								<span class="glyphicon glyphicon-plus btn_add_pc_mr swn-hd" onclick="add_more_vision('mr')" data-toggle="tooltip" title="Add MR"></span>	
								<?php } ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="exampd default">
							<table class="table borderless">
							<tr>
								<td class=" odcol hdn-hd">OD</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label for="<?php echo $tmp_visMrOdP ; ?>">P</label></div>
										<?php 
										$tmp = " name=\"".$tmp_visMrOdP."\" id=\"".$tmp_visMrOdP."\" onChange=\"setOs(this)\" class=\"form-control  ".$this->vis_getStatus($tmp_visMrOdP)."\" ";
										echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visMrOdP);
										?>
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label>&#9650;</label></div>
										<select name="<?php echo $tmp_visMrOdSel1 ; ?>" id="<?php echo $tmp_visMrOdSel1 ; ?>" onchange="setOs(this);" class="form-control  <?php echo $this->vis_getStatus($tmp_visMrOdSel1);?>" >
											<option value=""></option>
											<option value="BI" <?php echo ($$tmp_visMrOdSel1 == "BI" ) ? "selected" : "" ;?>>BI</option>
											<option value="BO" <?php echo ($$tmp_visMrOdSel1 == "BO" ) ? "selected" : "" ;?>>BO</option>
										</select>
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label>/</label></div>
										<?php 
										$tmp = " name=\"".$tmp_visMrOdSlash."\" id=\"".$tmp_visMrOdSlash."\" onChange=\"setOs(this)\" class=\"form-control  ".$this->vis_getStatus($tmp_visMrOdSlash)."\" ";
										echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visMrOdSlash);
										?>
									</div>
								</td>
								<td class="">
									<select id="<?php echo $tmp_visMrOdPrism ; ?>" name="<?php echo $tmp_visMrOdPrism ; ?>" onchange="setOs(this);" class="form-control  <?php echo $this->vis_getStatus($tmp_visMrOdPrism);?>">
										<option value=""></option>
										<option value="BD" <?php echo ($$tmp_visMrOdPrism == "BD" ) ? "selected" : "" ;?>>BD</option>
										<option value="BU" <?php echo ($$tmp_visMrOdPrism == "BU" ) ? "selected" : "" ;?>>BU</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class=" oscol hdn-hd">OS</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label>P</label></div>
										<?php 
										$tmp = " name=\"".$tmp_visMrOsP."\" id=\"".$tmp_visMrOsP."\" class=\"form-control  ".$this->vis_getStatus($tmp_visMrOsP)."\" ";
										echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visMrOsP);
										?>
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label>&#9650;</label></div>
										<select id="<?php echo $tmp_visMrOsSel1 ; ?>" name="<?php echo $tmp_visMrOsSel1 ; ?>" class="form-control  <?php echo $this->vis_getStatus($tmp_visMrOsSel1);?>">
											<option value=""></option>
											<option value="BI" <?php echo ($$tmp_visMrOsSel1 == "BI" ) ? "selected" : "" ;?>>BI</option>
											<option value="BO" <?php echo ($$tmp_visMrOsSel1 == "BO" ) ? "selected" : "" ;?>>BO</option>
										</select>
									</div>
								</td>
								<td class="">
									<div class="input-group plain">
										<div class="input-group-addon"><label>/</label></div>
										<?php 
										$tmp = " name=\"".$tmp_visMrOsSlash."\" id=\"".$tmp_visMrOsSlash."\" class=\"form-control  ".$this->vis_getStatus($tmp_visMrOsSlash)."\" ";
										echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visMrOsSlash);
										?>
									</div>
								</td>
								<td class="">
									<select name="<?php echo $tmp_visMrOsPrism ; ?>" id="<?php echo $tmp_visMrOsPrism ; ?>" class="form-control  <?php echo $this->vis_getStatus($tmp_visMrOsPrism);?>">
										<option value=""></option>
										<option value="BD" <?php echo ($$tmp_visMrOsPrism == "BD" ) ? "selected" : "" ;?>>BD</option>
										<option value="BU" <?php echo ($$tmp_visMrOsPrism == "BU" ) ? "selected" : "" ;?>>BU</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="" colspan="5">
									<textarea id="<?php echo $tmp_visMrPrismDesc_1 ; ?>" name="<?php echo $tmp_visMrPrismDesc_1 ; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visMrPrismDesc_1);?>"><?php echo $$tmp_visMrPrismDesc_1;?></textarea>
								</td>
							</tr>
							</table>
						</div>
					</div>
				</div>
			<!-- End MR 1- Prism -->			
		</div>	
	</div>
	<!-- END MR Block -->
	
</div>
<!-- END MR 1 -->
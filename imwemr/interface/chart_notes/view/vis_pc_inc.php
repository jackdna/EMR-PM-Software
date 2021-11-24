<?php

	$indx2= ($indx>1) ? $indx : "" ;		
	
	$tmp_pcDis1="elem_pcDis".$indx2;
	$tmp_pcNear1="elem_pcNear".$indx2;
	$tmp_visPcOdS="elem_visPcOdS".$indx2;
	$tmp_visPcOdC="elem_visPcOdC".$indx2;
	$tmp_visPcOdA="elem_visPcOdA".$indx2;
	$tmp_visPcOdAdd="elem_visPcOdAdd".$indx2;
	$tmp_visPcOdSel1="elem_visPcOdSel1".$indx2;

	$tmp_visPcOsS="elem_visPcOsS".$indx2;
	$tmp_visPcOsC="elem_visPcOsC".$indx2;
	$tmp_visPcOsA="elem_visPcOsA".$indx2;
	$tmp_visPcOsAdd="elem_visPcOsAdd".$indx2;
	$tmp_visPcOsSel1="elem_visPcOsSel1".$indx2;

	$tmp_visPcDesc="elem_visPcDesc".$indx2;
	$tmp_visPcDescLF="elem_visPcDesc".$indx2."LF";

	//--

	$tmp_visPcOdOverrefS="elem_visPcOdOverrefS".$indx2;
	$tmp_visPcOdOverrefC="elem_visPcOdOverrefC".$indx2;
	$tmp_visPcOdOverrefA="elem_visPcOdOverrefA".$indx2;
	$tmp_visPcOdOverrefV="elem_visPcOdOverrefV".$indx2;

	$tmp_visPcOsOverrefS="elem_visPcOsOverrefS".$indx2;
	$tmp_visPcOsOverrefC="elem_visPcOsOverrefC".$indx2;
	$tmp_visPcOsOverrefA="elem_visPcOsOverrefA".$indx2;
	$tmp_visPcOsOverrefV="elem_visPcOsOverrefV".$indx2;

	//-----

	$tmp_pcPrism1="elem_pcPrism".$indx2;
	$tmp_visPcOdP="elem_visPcOdP".$indx2;
	$tmp_visPcOdSel2="elem_visPcOdSel2".$indx2;
	$tmp_visPcOdSlash="elem_visPcOdSlash".$indx2;
	$tmp_visPcOdPrism="elem_visPcOdPrism".$indx2;

	$tmp_visPcOsP="elem_visPcOsP".$indx2;
	$tmp_visPcOsSel2="elem_visPcOsSel2".$indx2;
	$tmp_visPcOsSlash="elem_visPcOsSlash".$indx2;
	$tmp_visPcOsPrism="elem_visPcOsPrism".$indx2;

	$tmp_visPcPrismDesc_1="elem_visPcPrismDesc_".$indx;
	
	$tmp_menu_visPcOdOverrefV = "menu_visPcOdOverrefV".$indx2;
	$tmp_menu_visPcOsOverrefV = "menu_visPcOsOverrefV".$indx2;

	
	//display OR/Prism
	$tmp_ovr_dis = trim($$tmp_visPcOdOverrefS.$$tmp_visPcOdOverrefC.$$tmp_visPcOdOverrefA.$$tmp_visPcOdOverrefV.
				$$tmp_visPcOsOverrefS.$$tmp_visPcOsOverrefC.$$tmp_visPcOsOverrefA.$$tmp_visPcOsOverrefV.
				$$tmp_visPcOdP.$$tmp_visPcOdSel2.$$tmp_visPcOdSlash.$$tmp_visPcOdPrism.
				$$tmp_visPcOsP.$$tmp_visPcOsSel2.$$tmp_visPcOsSlash.$$tmp_visPcOsPrism);
	$tmp_ovr_dis = str_replace("20/","",$tmp_ovr_dis);
	if(!empty($tmp_ovr_dis) && $tmp_ovr_dis!="20/"){$tmp_ovr_dis=" in ";}else{$tmp_ovr_dis="";}
	
	$t_ctmpPc = "ctmpPc".$indx;//template

?>

<!-- PC -->

<div id="pc<?php echo $indx;?>" class="<?php echo $$t_ctmpPc;?>" >	

	<!-- PC1 Block -->
	<div class="col-lg-4 col-md-6 col-sm-6">
		<div class="examsectbox">
			<div class="header">
				<div class="pctab head-icons form-inline">
					<ul>
						<!--
						<li class="form-inline">
							<div class="kvalue img_acuity" onClick="callPopup('popup_pc.php','popPC','1065','650');">
								<img src="<?php echo $GLOBALS['webroot'];?>/library/images/ar_icon.png" alt=""/>
							</div>
						</li>
						-->
						<li>
							<h2 class="clickable" onClick="callPopup('','popPC<?php echo $indx; ?>','1065','650');">PC <?php echo $indx; ?></h2>
						</li>
						<li class="given">
							<input type="checkbox" id="<?php echo $tmp_pcDis1; ?>" name="<?php echo $tmp_pcDis1; ?>" value="Distance" onclick="vis_show_sec('pc<?php echo $indx; ?>_ds');" class="clickableLable <?php echo $this->vis_getStatus($tmp_pcDis1);?>" <?php echo ($$tmp_pcDis1 == "Distance") ? "checked=\"checked\"":""; ?>>
							<label for="<?php echo $tmp_pcDis1; ?>">Distance</label>
						</li>
						<li class="given">
							<input type="checkbox" id="<?php echo $tmp_pcNear1;?>" name="<?php echo $tmp_pcNear1;?>" value="Near" onclick="vis_show_sec('pc<?php echo $indx; ?>_nr');" class="clickableLable <?php echo $this->vis_getStatus($tmp_pcNear1);?>" <?php echo ($$tmp_pcNear1 == "Near") ? "checked=\"checked\"":""; ?>>
							<label for="<?php echo $tmp_pcNear1;?>">Near</label>
						</li>
						<li>
							<select name="<?php echo $tmp_pcList1; ?>" onChange="copyPcMr(this)" onclick="stopClickBubble();" class="form-control " data-toggle="tooltip" title="Copy From" data-pc="<?php echo $indx; ?>">
								<?php 
								echo str_replace("<option value=\"PC ".$indx."\">PC".$indx."</option>","", $str_opts_copy);
								?>	
							</select>
						</li>
						
						<li>
							<button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#pc_ovr_ref_prism<?php echo $indx;?>" >OR/Prism</button>							
							<!--<button type="button" class="btn btn-default " onclick="printPC()" data-toggle="tooltip" title="Print PC"><span class="glyphicon glyphicon-print"></span></button>
							<button type="button" class="btn btn-default " onclick="printPC()" data-toggle="tooltip" title="+/-Cyl">+/-Cyl</button>-->
						</li>
						<li>
							<div class="dropdown ">
								<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span></button>
								<ul class="dropdown-menu dropdown-menu-right" data-pc="<?php echo $indx; ?>">									
									<li><a href="javascript:void(0);" id="lbl_pc_showTransps<?php echo $indx; ?>" onClick="showTranspose('<?php echo $indx; ?>','PC')">+/-Cyl</a></li>								
									<li><a href="javascript:void(0);" onclick="printPC()" data-toggle="tooltip" title="Print PC"><span class="glyphicon glyphicon-print"></span></a></li>									
								</ul>
							</div>
						</li>
						<?php if($indx=="1"){ ?>
						<li>
							<a class="greenbtn_ar hvr-rectangle-out" onClick="opArUp('mp');"><span class="glyphicon glyphicon-upload"></span></a>
						</li>
						<?php } ?>
					</ul>
					
					
					<!-- Add btns -->
					<?php  if($indx>3 && $indx % 3==0){ ?>
					<span class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2" onclick="add_more_vision('pc')" data-toggle="tooltip" title="Add PC"></span>
					<span class="glyphicon glyphicon-remove btn_add_pc_mr " onclick="clearPc('<?php echo $indx; ?>',1)" data-toggle="tooltip" title="Remove PC"></span>
					<?php }else if($indx % 3==0){ ?>
					<span class="glyphicon glyphicon-plus btn_add_pc_mr " onclick="add_more_vision('pc')" data-toggle="tooltip" title="Add PC"></span>	
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
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdS;?>">S</label></div>
							<input type="text" name="<?php echo $tmp_visPcOdS;?>" id="<?php echo $tmp_visPcOdS;?>" value="<?php echo $$tmp_visPcOdS;?>" onBlur="justify2Decimal(this); changePc(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visPcOdC; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdS);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdC; ?>">C</label></div>
							<input type="text" name="<?php echo $tmp_visPcOdC; ?>" id="<?php echo $tmp_visPcOdC; ?>" value="<?php echo $$tmp_visPcOdC;?>" onBlur="justify2Decimal(this)" onkeyup="check2Blur(this,'C','<?php echo $tmp_visPcOdA; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdC);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdA; ?>">A</label></div>
							<input type="text" name="<?php echo $tmp_visPcOdA; ?>" id="<?php echo $tmp_visPcOdA; ?>" value="<?php echo $$tmp_visPcOdA;?>" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visPcOdAdd; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdA);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdAdd; ?>">Add</label></div>
							<input type="text" name="<?php echo $tmp_visPcOdAdd; ?>" id="<?php echo $tmp_visPcOdAdd; ?>" value="<?php echo $$tmp_visPcOdAdd;?>" onblur="setOs(this)" onKeyUp="check2Blur(this,'Add','<?php echo $tmp_visPcOdSel1 ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdAdd);?>">
						</div>
					</td>
					<td class="">
						<select name="<?php echo $tmp_visPcOdSel1; ?>" id="<?php echo $tmp_visPcOdSel1; ?>" onBlur="setOs(this)" class="form-control  <?php echo $this->vis_getStatus($tmp_visPcOdSel1);?>" <?php echo $pcPopupCall;?>>
							<option value=""></option>
							<option value="SV" <?php echo ($$tmp_visPcOdSel1 == "SV") ? "selected" : "" ; ?>>SV</option>
							<option value="BF" <?php echo ($$tmp_visPcOdSel1 == "BF") ? "selected" : "" ;?>>BF</option>
							<option value="Progs" <?php echo ($$tmp_visPcOdSel1 == "Progs") ? "selected" : "" ;?>>Progs</option>
							<option value="TRF" <?php echo ($$tmp_visPcOdSel1 == "TRF") ? "selected" : "" ;?>>TRF</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class=" oscol">OS</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsS; ?>">S</label></div>
							<input type="text" name="<?php echo $tmp_visPcOsS; ?>" id="<?php echo $tmp_visPcOsS; ?>" value="<?php echo $$tmp_visPcOsS;?>" onBlur="justify2Decimal(this); changePc(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visPcOsC; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsS);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsC; ?>">C</label></div>
							<input type="text" name="<?php echo $tmp_visPcOsC; ?>" id="<?php echo $tmp_visPcOsC; ?>" value="<?php echo $$tmp_visPcOsC;?>" onBlur="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visPcOsA; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsC);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsA; ?>">A</label></div>
							<input type="text" name="<?php echo $tmp_visPcOsA; ?>" id="<?php echo $tmp_visPcOsA; ?>" value="<?php echo $$tmp_visPcOsA;?>" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visPcOsAdd; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsA);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<div class="input-group plain">
							<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsAdd; ?>">Add</label></div>
							<input type="text" name="<?php echo $tmp_visPcOsAdd; ?>" id="<?php echo $tmp_visPcOsAdd; ?>" value="<?php echo $$tmp_visPcOsAdd;?>" onBlur="addArthSign(this)" onKeyUp="check2Blur(this,'Add','<?php echo $tmp_visPcOsSel1 ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsAdd);?>" <?php echo $pcPopupCall;?>>
						</div>
					</td>
					<td class="">
						<select name="<?php echo $tmp_visPcOsSel1; ?>" id="<?php echo $tmp_visPcOsSel1; ?>" class="form-control  <?php echo $this->vis_getStatus($tmp_visPcOsSel1);?>" <?php echo $pcPopupCall;?>>
							<option value=""></option>
							<option value="SV" <?php echo ($$tmp_visPcOsSel1 == "SV") ? "selected" : "" ;?>>SV</option>
							<option value="BF" <?php echo ($$tmp_visPcOsSel1 == "BF") ? "selected" : "" ;?>>BF</option>
							<option value="Progs" <?php echo ($$tmp_visPcOsSel1 == "Progs") ? "selected" : "" ;?>>Progs</option>
							<option value="TRF" <?php echo ($$tmp_visPcOsSel1 == "TRF") ? "selected" : "" ;?>>TRF</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="" colspan="6">
						<textarea id="<?php echo $tmp_visPcDesc; ?>" name="<?php echo $tmp_visPcDesc; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visPcDesc);?>" rows="2" onkeyup="setTaPlanHgt(this.id);"><?php echo $$tmp_visPcDesc; ?></textarea>
						<input type="hidden" name="<?php echo $tmp_visPcDescLF; ?>" value="<?php echo $$tmp_visPcDescLF;?>">
					</td>
				</tr>
				</table>
			</div>
		</div>
		
		<!-- PC1 Over Ref + Prism -->
		<div id="pc_ovr_ref_prism<?php echo $indx;?>" class="collapse dv_ovr_prsm <?php echo $tmp_ovr_dis; ?>">
			<div class="row">
				<div class="col-lg-12 col-lg-ovr-ref-hd">
		<!-- PC1 Over Ref -->
		<div id="pc_ovr_ref<?php echo $indx;?>" >
			<div class="examsectbox" >
				<div class="header clean">
					<div>
						<ul>
							<li><h2>Over Refraction</h2></li>
						</ul>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="exampd default">
					<table class="table borderless">
					<tr>
						<td class=" odcol">OD</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdOverrefS; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visPcOdOverrefS; ?>" id="<?php echo $tmp_visPcOdOverrefS; ?>" value="<?php echo $$tmp_visPcOdOverrefS;?>" onChange="justify2Decimal(this);" onBlur="changePc(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visPcOdOverrefC; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdOverrefS);?>" <?php echo $pcPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdOverrefC; ?>">C</label></div>
								<input type="text" name="<?php echo $tmp_visPcOdOverrefC; ?>" id="<?php echo $tmp_visPcOdOverrefC; ?>" value="<?php echo $$tmp_visPcOdOverrefC;?>" onChange="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visPcOdOverrefA; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdOverrefC);?>" <?php echo $pcPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdOverrefA; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visPcOdOverrefA; ?>" id="<?php echo $tmp_visPcOdOverrefA; ?>" value="<?php echo $$tmp_visPcOdOverrefA;?>" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visPcOdOverrefV; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOdOverrefA);?>" <?php echo $pcPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdOverrefV; ?>">V</label></div>
								<input type="text" name="<?php echo $tmp_visPcOdOverrefV; ?>" id="<?php echo $tmp_visPcOdOverrefV; ?>" value="<?php echo $$tmp_visPcOdOverrefV;?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visPcOdOverrefV);?>" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pcPopupCall;?>>
								<?php echo $$tmp_menu_visPcOdOverrefV ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis1","elem_visPcOdOverrefV",442,0,array("pdiv"=>"elem_visPcOdOverrefV"));*/ ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class=" oscol">OS</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsOverrefS; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visPcOsOverrefS; ?>" id="<?php echo $tmp_visPcOsOverrefS; ?>" value="<?php echo $$tmp_visPcOsOverrefS;?>" onChange="justify2Decimal(this);" onBlur="changePc(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visPcOsOverrefC; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsOverrefS);?>" <?php echo $pcPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsOverrefC; ?>">C</label></div>
								<input type="text" name="<?php echo $tmp_visPcOsOverrefC; ?>" id="<?php echo $tmp_visPcOsOverrefC; ?>" value="<?php echo $$tmp_visPcOsOverrefC;?>" onChange="justify2Decimal(this)" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visPcOsOverrefA; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsOverrefC);?>" <?php echo $pcPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsOverrefA; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visPcOsOverrefA; ?>" id="<?php echo $tmp_visPcOsOverrefA; ?>" value="<?php echo $$tmp_visPcOsOverrefA;?>" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visPcOsOverrefV; ?>');" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsOverrefA);?>" <?php echo $pcPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsOverrefV; ?>">V</label></div>
								<input type="text" name="<?php echo $tmp_visPcOsOverrefV; ?>" id="<?php echo $tmp_visPcOsOverrefV; ?>" value="<?php echo $$tmp_visPcOsOverrefV;?>" class="form-control acuity <?php echo $this->vis_getStatus($tmp_visPcOsOverrefV);?>" onchange="setActuity(this);" onfocus="setCursorAtEnd(this);" <?php echo $pcPopupCall;?>>
								<?php echo $$tmp_menu_visPcOsOverrefV ; /*wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis2","elem_visPcOsOverrefV",442,0,array("pdiv"=>"elem_visPcOsOverrefV"));*/ ?>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>
		</div>
		<!-- PC1 Over Ref -->	
				</div>
				<div class="col-lg-12 col-lg-prism-hd">
		<!-- PC1 Prism -->
		<div id="pc_prism<?php echo $indx;?>" >
			<div class="examsectbox" >
				<div class="header clean">
					<div>
						<ul>
							<li>
								<h2>Prism</h2>
								<input type="hidden" id="<?php echo $tmp_pcPrism1; ?>" name="<?php echo $tmp_pcPrism1; ?>" value="<?php echo ($$tmp_pcPrism1);?>">
							</li>
							<li class="pull-right">
								
							</li>
						</ul>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="exampd default">
					<table class="table borderless">
					<tr>
						<td class=" odcol hdn-hd">OD</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOdP; ?>">P</label></div>
								<?php
								$tmp = " name=\"".$tmp_visPcOdP."\" id=\"".$tmp_visPcOdP."\" onChange=\"setOs(this)\" class=\"form-control  ".$this->vis_getStatus($tmp_visPcOdP)."\" ";
								echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visPcOdP);
								?>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label>&#9650;</label></div>
								<select name="<?php echo $tmp_visPcOdSel2; ?>" id="<?php echo $tmp_visPcOdSel2; ?>" onChange="setOs(this)" class="form-control  <?php echo $this->vis_getStatus($tmp_visPcOdSel2);?>">
									<option value=""></option>
									<option value="BI" <?php echo ($$tmp_visPcOdSel2 == "BI" ) ? "selected" : "" ;?>>BI</option>
									<option value="BO" <?php echo ($$tmp_visPcOdSel2 == "BO" ) ? "selected" : "" ;?>>BO</option>
								</select>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label>/</label></div>
								<?php 
								$tmp = " name=\"".$tmp_visPcOdSlash."\" id=\"".$tmp_visPcOdSlash."\" onChange=\"setOs(this)\" class=\"form-control ".$this->vis_getStatus($tmp_visPcOdSlash)."\" ";
								echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visPcOdSlash);
								?>
							</div>
						</td>
						<td class="">
							<select name="<?php echo $tmp_visPcOdPrism; ?>" id="<?php echo $tmp_visPcOdPrism; ?>" onChange="setOs(this)" class="form-control  <?php echo $this->vis_getStatus($tmp_visPcOdPrism);?>" >
								<option value=""></option>
								<option value="BD" <?php echo ($$tmp_visPcOdPrism == "BD" ) ? "selected" : "" ;?>>BD</option>
								<option value="BU" <?php echo ($$tmp_visPcOdPrism == "BU" ) ? "selected" : "" ;?>>BU</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class=" oscol hdn-hd">OS</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visPcOsP; ?>">P</label></div>
								<?php
								$tmp = " name=\"".$tmp_visPcOsP."\" id=\"".$tmp_visPcOsP."\" class=\"form-control ".$this->vis_getStatus($tmp_visPcOsP)."\" ";
								echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visPcOsP);
								?>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label>&#9650;</label></div>
								<select  name="<?php echo $tmp_visPcOsSel2; ?>" id="<?php echo $tmp_visPcOsSel2; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsSel2);?>" >
									<option value=""></option>
									<option value="BI" <?php echo ($$tmp_visPcOsSel2 == "BI" ) ? "selected" : "" ;?>>BI</option>
									<option value="BO" <?php echo ($$tmp_visPcOsSel2 == "BO" ) ? "selected" : "" ;?>>BO</option>
								</select>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label>/</label></div>
								<?php
								$tmp = " name=\"".$tmp_visPcOsSlash."\" id=\"".$tmp_visPcOsSlash."\" class=\"form-control ".$this->vis_getStatus($tmp_visPcOsSlash)."\" ";
								echo $this->getVisDropDown("PrismQtr",$tmp,$$tmp_visPcOsSlash);
								?>
							</div>
						</td>
						<td class="">
							<select name="<?php echo $tmp_visPcOsPrism; ?>" id="<?php echo $tmp_visPcOsPrism; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visPcOsPrism);?>">
								<option value=""></option>
								<option value="BD" <?php echo ($$tmp_visPcOsPrism == "BD" ) ? "selected" : "" ;?>>BD</option>
								<option value="BU" <?php echo ($$tmp_visPcOsPrism == "BU" ) ? "selected" : "" ;?>>BU</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="" colspan="5">
							<textarea id="<?php echo $tmp_visPcPrismDesc_1; ?>" name="<?php echo $tmp_visPcPrismDesc_1; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visPcPrismDesc_1);?>"><?php echo $$tmp_visPcPrismDesc_1;?></textarea>
						</td>
					</tr>
					</table>
				</div>
			</div>
		</div>					
		<!-- PC1 Prism -->
				</div>
			</div>
		</div>
		<!-- PC1 Over Ref + Prism -->
		
	</div>	
	<!-- PC1 Block -->	
</div>
<!-- PC1 -->

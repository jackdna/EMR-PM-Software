<?php

$tmp_lasik_trgt_method = "el_lasik_trgt_method";
$tmp_visLasikTrgtDate = "el_visLasikTrgtDate";
$tmp_visLasikTrgtTime = "el_visLasikTrgtTime";
$tmp_lasik_trgt_intervention = "el_lasik_trgt_intervention";
$tmp_visLasikTrgtMicKera = "el_visLasikTrgtMicKera";
$tmp_lasik_trgt_Excimer = "el_lasik_trgt_Excimer";
$tmp_lasik_trgt_mode = "el_lasik_trgt_mode";
$tmp_lasik_trgt_opti_zone = "el_lasik_trgt_opti_zone";

$tmp_visLasikTrgtOdS = "el_visLasikTrgtOdS";
$tmp_visLasikTrgtOdC = "el_visLasikTrgtOdC";
$tmp_visLasikTrgtOdA = "el_visLasikTrgtOdA";
$tmp_visLasikTrgtOsS = "el_visLasikTrgtOsS";
$tmp_visLasikTrgtOsC = "el_visLasikTrgtOsC";
$tmp_visLasikTrgtOsA = "el_visLasikTrgtOsA";
$tmp_visLasikTrgtDesc = "el_visLasikTrgtDesc";

$tmp_visLasikLsrOdS = "el_visLasikLsrOdS";
$tmp_visLasikLsrOdC = "el_visLasikLsrOdC";
$tmp_visLasikLsrOdA = "el_visLasikLsrOdA";
$tmp_visLasikLsrOsS = "el_visLasikLsrOsS";
$tmp_visLasikLsrOsC = "el_visLasikLsrOsC";
$tmp_visLasikLsrOsA = "el_visLasikLsrOsA";
$tmp_visLasikLsrDesc = "el_visLasikLsrDesc";
$tmp_lasik_userid = "el_lasik_userid";



?>
<!-- COMMON LASIK -->
<div id="div_lasik_common" >
	
	<!-- Lasik Block -->
	<div class="col-lg-6 col-md-12">
		<div class="row">
			<div class="col-lg-12 col-lg-mr-main-hd">
			<!-- main -->
		<div class="examsectbox">
			<div class="header">
				<div class="mrtab head-icons form-inline">
					<ul>
						<li>
							<h2 class="clickable" >LASIK</h2>
						</li>
						<li style="width:10%;"></li>
						<li >
						<?php if(!empty($GLOBALS['IBRA'])){ ?><span class="glyphicon glyphicon-export clickable" data-toggle="tooltip" title="Send to IBRA" onclick="cnct_ibra()"></span><?php } ?>
						</li>
						<input type="hidden" id="<?php echo $tmp_lasik_userid ; ?>" name="<?php echo $tmp_lasik_userid ; ?>" value="">
					</ul>
					
					<span class="glyphicon glyphicon-ok-circle clickable" data-toggle="tooltip" title="No Change"></span>
					
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="exampd default">
				<table class="table borderless">
					<tr>
						<td>Method</td>
						<td>
							<select name="<?php echo $tmp_lasik_trgt_method ; ?>" id="<?php echo $tmp_lasik_trgt_method ; ?>"  class="form-control  <?php echo $this->vis_getStatus($tmp_lasik_trgt_method);?>" >
								<option value=""></option>
								<option value="LASIK" <?php echo ($$tmp_lasik_trgt_method == "LASIK" ) ? "selected" : "" ;?>>LASIK</option>
								<option value="LASEK" <?php echo ($$tmp_lasik_trgt_method == "LASEK" ) ? "selected" : "" ;?>>LASEK</option>
								<option value="PRK" <?php echo ($$tmp_lasik_trgt_method == "PRK" ) ? "selected" : "" ;?>>PRK</option>
								<option value="Phaco Cat" <?php echo ($$tmp_lasik_trgt_method == "Phaco Cat" ) ? "selected" : "" ;?>>Phaco Cat</option>
								<option value="Phakic IOL" <?php echo ($$tmp_lasik_trgt_method == "Phakic IOL" ) ? "selected" : "" ;?>>Phakic IOL</option>
							</select>
						</td>
						<td>Date</td>
						<td>
							<input type="text" name="<?php echo $tmp_visLasikTrgtDate ; ?>" id="<?php echo $tmp_visLasikTrgtDate ; ?>" value="<?php echo $$tmp_visLasikTrgtDate;?>" onBlur="setGivenMr(this,'Target');changeMr(this);"  class="form-control date-pick  <?php echo $this->vis_getStatus($tmp_visLasikTrgtDate);?>" >
						</td>
						<td>Time</td>
						<td>
							<input type="text" name="<?php echo $tmp_visLasikTrgtTime ; ?>" id="<?php echo $tmp_visLasikTrgtTime ; ?>" value="<?php echo $$tmp_visLasikTrgtTime;?>" onBlur="setGivenMr(this,'Target');changeMr(this);"  class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtTime);?>" >
						</td>
					</tr>
					<tr>
						<td>Intervention</td>
						<td>
							<select name="<?php echo $tmp_lasik_trgt_intervention ; ?>" id="<?php echo $tmp_lasik_trgt_intervention ; ?>"  class="form-control  <?php echo $this->vis_getStatus($tmp_lasik_trgt_intervention);?>" >
								<option value=""></option>
								<option value="First" <?php echo ($$tmp_lasik_trgt_intervention == "First" ) ? "selected" : "" ;?>>First</option>
								<option value="Second" <?php echo ($$tmp_lasik_trgt_intervention == "Second" ) ? "selected" : "" ;?>>Second</option>
								<option value="Third" <?php echo ($$tmp_lasik_trgt_intervention == "Third" ) ? "selected" : "" ;?>>Third</option>
								<option value="Fourth" <?php echo ($$tmp_lasik_trgt_intervention == "Fourth" || $$tmp_lasik_trgt_intervention == "Forth" ) ? "selected" : "" ;?>>Fourth</option>
							</select>
						</td>
						<td>Microkeratome</td>
						<td colspan="3">
							<input type="text" name="<?php echo $tmp_visLasikTrgtMicKera ; ?>" id="<?php echo $tmp_visLasikTrgtMicKera ; ?>" value="<?php echo $$tmp_visLasikTrgtMicKera;?>" onBlur="setGivenMr(this,'Target');changeMr(this);"  class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtMicKera);?>" >
						</td>									
					</tr>
					<tr>
						<td>Laser Excimer</td>
						<td>
							<div class="input-group">
								<input type="text" name="<?php echo $tmp_lasik_trgt_Excimer ; ?>" id="<?php echo $tmp_lasik_trgt_Excimer ; ?>" value="<?php echo ($$tmp_lasik_trgt_Excimer); ?>" class="form-control <?php echo $this->vis_getStatus($tmp_lasik_trgt_Excimer);?>"  />											
								<?php echo $menu_lasik_trgt_Excimer ; ?>											
							</div>
							
						</td>
						<td>Laser Mode</td>
						<td>
							<div class="input-group">
								<input type="text" name="<?php echo $tmp_lasik_trgt_mode ; ?>" id="<?php echo $tmp_lasik_trgt_mode ; ?>" value="<?php echo ($$tmp_lasik_trgt_mode); ?>" class="form-control <?php echo $this->vis_getStatus($tmp_lasik_trgt_mode);?>"  />											
								<?php echo $menu_lasik_trgt_mode ; ?>											
							</div>
							
						</td>
						<td>Laser Optical Zone</td>
						<td>
							<input type="text" name="<?php echo $tmp_lasik_trgt_opti_zone ; ?>" id="<?php echo $tmp_lasik_trgt_opti_zone ; ?>" value="<?php echo $$tmp_lasik_trgt_opti_zone;?>" onBlur="setGivenMr(this,'Target');changeMr(this);"  class="form-control <?php echo $this->vis_getStatus($tmp_lasik_trgt_opti_zone);?>" >
						</td>
					</tr>
					
				</table>
			</div>
		</div>
			<!-- main -->
			</div>			
		</div>	
	</div>
	<!-- END Lasik Block -->
	
</div>
<!-- COMMON LASIK -->

<!-- TARGET -->
<div id="div_lasik_target" >
	
	<!-- Lasik Block -->
	<div class="col-lg-3 col-md-12">
		<div class="row">
			<div class="col-lg-12 col-lg-mr-main-hd">
			<!-- main -->
		<div class="examsectbox">
			<div class="header">
				<div class="mrtab head-icons form-inline">
					<ul>
						<li>
							<h2 class="clickable" >TARGET</h2>
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
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikTrgtOdS ; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visLasikTrgtOdS ; ?>" id="<?php echo $tmp_visLasikTrgtOdS ; ?>" value="<?php echo $$tmp_visLasikTrgtOdS;?>" onBlur="justify2Decimal(this);setGivenMr(this,'Target');changeMr(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visLasikTrgtOdC ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtOdS);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikTrgtOdC ; ?>">C</label><strong></strong></div>
								<input type="text" name="<?php echo $tmp_visLasikTrgtOdC ; ?>" id="<?php echo $tmp_visLasikTrgtOdC ; ?>" value="<?php echo $$tmp_visLasikTrgtOdC;?>" onBlur="justify2Decimal(this);setGivenMr(this,'Target');changeMr(this);" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visLasikTrgtOdA ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtOdA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikTrgtOdA ; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visLasikTrgtOdA ; ?>" id="<?php echo $tmp_visLasikTrgtOdA ; ?>" value="<?php echo $$tmp_visLasikTrgtOdA;?>" onBlur="setGivenMr(this,'Target');changeMr(this);" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visLasikTrgtOsS ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtOdA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						
					</tr>
					<tr>
						<td class=" oscol">OS</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikTrgtOsS ; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visLasikTrgtOsS ; ?>" id="<?php echo $tmp_visLasikTrgtOsS ; ?>" value="<?php echo $$tmp_visLasikTrgtOsS;?>" onblur="justify2Decimal(this);setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onkeyup="check2Blur(this,'S','<?php echo $tmp_visLasikTrgtOsC ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtOsS);?>" <?php echo $mrPopupCall;?> />
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikTrgtOsC ; ?>">C</label></div>
								<input type="text" name="<?php echo $tmp_visLasikTrgtOsC ; ?>" id="<?php echo $tmp_visLasikTrgtOsC ; ?>" value="<?php echo $$tmp_visLasikTrgtOsC;?>" onBlur="justify2Decimal(this);setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visLasikTrgtOsA ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtOsC);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikTrgtOsA ; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visLasikTrgtOsA ; ?>" id="<?php echo $tmp_visLasikTrgtOsA ; ?>" value="<?php echo $$tmp_visLasikTrgtOsA;?>" onBlur="setGivenMr(this,'MR <?php echo $indx; ?>');changeMr(this);" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visLasikTrgtDesc ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtOsA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>						
					</tr>
					<tr>						
						<td class="" colspan="8">
							<textarea id="<?php echo $tmp_visLasikTrgtDesc ; ?>" name="<?php echo $tmp_visLasikTrgtDesc ; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikTrgtDesc);?>" onkeyup="setTaPlanHgt(this.id);"><?php echo $$tmp_visLasikTrgtDesc;?></textarea>
							<input type="hidden" name="<?php echo $tmp_visLasikTrgtDescLF ; ?>" value="<?php echo $$tmp_visLasikTrgtDescLF;?>">
						</td>
					</tr>	
				</table>								
			</div>
		</div>
			<!-- main -->
			</div>			
		</div>	
	</div>
	<!-- END Lasik Block -->
	
</div>
<!-- END TARGET -->

<!-- LASER -->
<div id="div_lasik_laser" >

	<!-- Lasik Block -->
	<div class="col-lg-3 col-md-12">
		<div class="row">
			<div class="col-lg-12 col-lg-mr-main-hd">
			<!-- main -->
		<div class="examsectbox">
			<div class="header">
				<div class="mrtab head-icons form-inline">
					<ul>
						<li>
							<h2 class="clickable" >LASER</h2>
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
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikLsrOdS ; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visLasikLsrOdS ; ?>" id="<?php echo $tmp_visLasikLsrOdS ; ?>" value="<?php echo $$tmp_visLasikLsrOdS;?>" onBlur="justify2Decimal(this);setGivenMr(this,'Laser');changeMr(this);" onKeyUp="check2Blur(this,'S','<?php echo $tmp_visLasikLsrOdC ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrOdS);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikLsrOdC ; ?>">C</label><strong></strong></div>
								<input type="text" name="<?php echo $tmp_visLasikLsrOdC ; ?>" id="<?php echo $tmp_visLasikLsrOdC ; ?>" value="<?php echo $$tmp_visLasikLsrOdC;?>" onBlur="justify2Decimal(this);setGivenMr(this,'Laser');changeMr(this);" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visLasikLsrOdA ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrOdA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikLsrOdA ; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visLasikLsrOdA ; ?>" id="<?php echo $tmp_visLasikLsrOdA ; ?>" value="<?php echo $$tmp_visLasikLsrOdA;?>" onBlur="setGivenMr(this,'Laser');changeMr(this);" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visLasikLsrOsS ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrOdA);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						
					</tr>
					<tr>
						<td class=" oscol">OS</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikLsrOsS ; ?>">S</label></div>
								<input type="text" name="<?php echo $tmp_visLasikLsrOsS ; ?>" id="<?php echo $tmp_visLasikLsrOsS ; ?>" value="<?php echo $$tmp_visLasikLsrOsS;?>" onblur="justify2Decimal(this);setGivenMr(this,'Laser');changeMr(this);" onkeyup="check2Blur(this,'S','<?php echo $tmp_visLasikLsrOsC ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrOsS);?>" <?php echo $mrPopupCall;?> />
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikLsrOsC ; ?>">C</label></div>
								<input type="text" name="<?php echo $tmp_visLasikLsrOsC ; ?>" id="<?php echo $tmp_visLasikLsrOsC ; ?>" value="<?php echo $$tmp_visLasikLsrOsC;?>" onBlur="justify2Decimal(this);setGivenMr(this,'Laser');changeMr(this);" onKeyUp="check2Blur(this,'C','<?php echo $tmp_visLasikLsrOsA ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrOsC);?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>
						<td class="">
							<div class="input-group plain">
								<div class="input-group-addon"><label for="<?php echo $tmp_visLasikLsrOsA ; ?>">A</label></div>
								<input type="text" name="<?php echo $tmp_visLasikLsrOsA ; ?>" id="<?php echo $tmp_visLasikLsrOsA ; ?>" value="<?php echo $$tmp_visLasikLsrOsA;?>" onBlur="setGivenMr(this,'Laser');changeMr(this);" onKeyUp="check2Blur(this,'A','<?php echo $tmp_visLasikLsrDesc ; ?>')" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrOsA); ?>" <?php echo $mrPopupCall;?>>
							</div>
						</td>						
					</tr>
					<tr>						
						<td class="" colspan="8">
							<textarea id="<?php echo $tmp_visLasikLsrDesc ; ?>" name="<?php echo $tmp_visLasikLsrDesc ; ?>" class="form-control <?php echo $this->vis_getStatus($tmp_visLasikLsrDesc);?>" onkeyup="setTaPlanHgt(this.id);"><?php echo $$tmp_visLasikLsrDesc;?></textarea>
							<input type="hidden" name="<?php echo $tmp_visLasikLsrDescLF ; ?>" value="<?php echo $$tmp_visLasikLsrDescLF;?>">
						</td>
					</tr>	
				</table>								
			</div>
		</div>
			<!-- main -->
			</div>			
		</div>	
	</div>
	<!-- END Lasik Block -->
	
</div>
<!-- END LASER -->
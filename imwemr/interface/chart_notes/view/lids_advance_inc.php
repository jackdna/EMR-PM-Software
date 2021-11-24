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
?>
<?php
/*
File: lids_advance_inc.php
Purpose: This file provides Lids Plastic section in LA exam.
Access Type : Include file
*/
?>
<div class="advanchead" onclick="placs_showAdvance(this)"><strong>Advance</strong></div>
	<div class="clearfix"> </div>	
	
	<!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#div6" aria-controls="div6" role="tab" data-toggle="tab" id="tab6" > Upper Lid</a></li>
		<li role="presentation" class=""><a href="#div7" aria-controls="div7" role="tab" data-toggle="tab" id="tab7" > Lower Lid</a></li>
	  </ul>
	<!-- Nav tabs -->
	<!-- Tab panes -->
	<div class="tab-content">
	<div role="tabpanel" class="tab-pane la-advance active" id="div6">
		<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="9" align="center"><!--<button class="upperlid" type="submit">Upper Lid</button> <button class="lowerlid" type="submit">Lower Lid</button>--></td>
		<td width="61" align="center" class="bilat bilat_all" onClick="check_bilateral('lids_advc')"><strong>Bilateral</strong></td>
		<td colspan="9" align="center">&nbsp;</td>
		</tr>
		<tr class="exmhlgcol grp_handle grp_brwpto <?php echo $cls_brwpto; ?>" id="d_brwpto">
		<td  align="left" class="grpbtn" onclick="openSubGrp('brwpto')">
			<label >Brow Ptosis <span class="glyphicon <?php echo $arow_brwpto; ?>"></span></label> 
		</td>
		<td colspan="8" align="left"><textarea rows="3" class="form-control" onblur="checkwnls();checkSymClr(this,'brwpto');"  name="elem_brwptoOd_text" id="elem_brwptoOd_text"><?php echo $elem_brwptoOd_text; ?></textarea></td>
		<td align="center" class="bilat" onclick="check_bl('brwpto')">BL</td>
		<td  align="left" class="grpbtn" onclick="openSubGrp('brwpto')">
			<label >Brow Ptosis <span class="glyphicon <?php echo $arow_brwpto; ?>"></span></label>
		</td>
		<td colspan="8" align="left"><textarea rows="3" class="form-control" onblur="checkwnls();checkSymClr(this,'brwpto');"  name="elem_brwptoOs_text" id="elem_brwptoOs_text"><?php echo $elem_brwptoOs_text; ?></textarea></td>
		</tr>
		
		
		<tr class="exmhlgcol grp_brwpto <?php echo $cls_brwpto; ?>">
		<td align="left">Medial</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOd_Absent" name="elem_medialOd_Absent" value="Absent" <?php echo ($elem_medialOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_medialOd_Absent" >Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOd_1" name="elem_medialOd_1" value="1+" <?php echo ($elem_medialOd_1 == "+1" || $elem_medialOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_medialOd_1">1+</label>
		</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOd_2" name="elem_medialOd_2" value="2+" <?php echo ($elem_medialOd_2 == "+2" || $elem_medialOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_medialOd_2">2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOd_3" name="elem_medialOd_3" value="3+" <?php echo ($elem_medialOd_3 == "+3" || $elem_medialOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_medialOd_3">3+</label>
		</td>
		<td colspan="4">
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOd_4" name="elem_medialOd_4" value="4+" <?php echo ($elem_medialOd_4 == "+4" || $elem_medialOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_medialOd_4">4+</label>
		</td>
		
		<td align="center" class="bilat" onclick="check_bl('brwpto')">BL</td>
		<td align="left">Medial</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOs_Absent" name="elem_medialOs_Absent" value="Absent" <?php echo ($elem_medialOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_medialOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOs_1" name="elem_medialOs_1" value="1+" <?php echo ($elem_medialOs_1 == "+1" || $elem_medialOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_medialOs_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOs_2" name="elem_medialOs_2" value="2+" <?php echo ($elem_medialOs_2 == "+2" || $elem_medialOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_medialOs_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOs_3" name="elem_medialOs_3" value="3+" <?php echo ($elem_medialOs_3 == "+3" || $elem_medialOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_medialOs_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_medialOs_4" name="elem_medialOs_4" value="4+" <?php echo ($elem_medialOs_4 == "+4" || $elem_medialOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_medialOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Brow Ptosis/Medial"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Brow Ptosis/Medial"]; }  ?>
		
		<tr class="exmhlgcol grp_brwpto <?php echo $cls_brwpto; ?>">
		<td align="left">Lateral</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOd_Absent" name="elem_lateralOd_Absent" value="Absent" <?php echo ($elem_lateralOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_lateralOd_Absent" >Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOd_1" name="elem_lateralOd_1" value="1+" <?php echo ($elem_lateralOd_1 == "+1" || $elem_lateralOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_lateralOd_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOd_2" name="elem_lateralOd_2" value="2+" <?php echo ($elem_lateralOd_2 == "+2" || $elem_lateralOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_lateralOd_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOd_3" name="elem_lateralOd_3" value="3+" <?php echo ($elem_lateralOd_3 == "+3" || $elem_lateralOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_lateralOd_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOd_4" name="elem_lateralOd_4" value="4+" <?php echo ($elem_lateralOd_4 == "+4" || $elem_lateralOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_lateralOd_4">4+</label>
		</td>
		<td align="center" class="bilat" onclick="check_bl('brwpto')">BL</td>
		<td align="left">Lateral</td>	
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOs_Absent" name="elem_lateralOs_Absent" value="Absent" <?php echo ($elem_lateralOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_lateralOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOs_1" name="elem_lateralOs_1" value="1+" <?php echo ($elem_lateralOs_1 == "+1" || $elem_lateralOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_lateralOs_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOs_2" name="elem_lateralOs_2" value="2+" <?php echo ($elem_lateralOs_2 == "+2" || $elem_lateralOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_lateralOs_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOs_3" name="elem_lateralOs_3" value="3+" <?php echo ($elem_lateralOs_3 == "+3" || $elem_lateralOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_lateralOs_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'brwpto');" id="elem_lateralOs_4" name="elem_lateralOs_4" value="4+" <?php echo ($elem_lateralOs_4 == "+4" || $elem_lateralOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_lateralOs_4">4+</label>
		</td>
		</tr>	
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Brow Ptosis/Lateral"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Brow Ptosis/Lateral"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Brow Ptosis"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Brow Ptosis"]; }  ?>
		
		<tr id="d_FroUse">
		<td align="left">Frontalis Use</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOd_Absent" name="elem_frouseOd_Absent" value="Absent" <?php echo ($elem_frouseOd_Absent == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOd_Absent">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOd_1" name="elem_frouseOd_1" value="1+" <?php echo ($elem_frouseOd_1 == "+1" || $elem_frouseOd_1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOd_2" name="elem_frouseOd_2" value="2+" <?php echo ($elem_frouseOd_2 == "+2" || $elem_frouseOd_2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOd_3" name="elem_frouseOd_3" value="3+" <?php echo ($elem_frouseOd_3 == "+3" || $elem_frouseOd_3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOd_4" name="elem_frouseOd_4" value="4+" <?php echo ($elem_frouseOd_4 == "+4" || $elem_frouseOd_4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOd_4">4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('FroUse')">BL</td>
		<td align="left">Frontalis Use</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOs_Absent" name="elem_frouseOs_Absent" value="Absent" <?php echo ($elem_frouseOs_Absent == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOs_Absent">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOs_1" name="elem_frouseOs_1" value="1+" <?php echo ($elem_frouseOs_1 == "+1" || $elem_frouseOs_1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOs_2" name="elem_frouseOs_2" value="2+" <?php echo ($elem_frouseOs_2 == "+2" || $elem_frouseOs_2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOs_3" name="elem_frouseOs_3" value="3+" <?php echo ($elem_frouseOs_3 == "+3" || $elem_frouseOs_3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_frouseOs_4" name="elem_frouseOs_4" value="4+" <?php echo ($elem_frouseOs_4 == "+4" || $elem_frouseOs_4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_frouseOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Frontalis Use"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Frontalis Use"]; }  ?>
		
		<tr id="d_LidsDerma">
		<td align="left">Dermatochalasis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOd_Absent" name="elem_dermaOd_Absent" value="Absent" <?php echo ($elem_dermaOd_Absent == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOd_Absent">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOd_1" name="elem_dermaOd_1" value="1+" <?php echo ($elem_dermaOd_1 == "+1" || $elem_dermaOd_1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOd_2" name="elem_dermaOd_2" value="2+" <?php echo ($elem_dermaOd_2 == "+2" || $elem_dermaOd_2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOd_3" name="elem_dermaOd_3" value="3+" <?php echo ($elem_dermaOd_3 == "+3" || $elem_dermaOd_3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOd_4" name="elem_dermaOd_4" value="4+" <?php echo ($elem_dermaOd_4 == "+4" || $elem_dermaOd_4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOd_4">4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('LidsDerma')">BL</td>
		<td align="left">Dermatochalasis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOs_Absent" name="elem_dermaOs_Absent" value="Absent" <?php echo ($elem_dermaOs_Absent == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOs_Absent">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOs_1" name="elem_dermaOs_1" value="1+" <?php echo ($elem_dermaOs_1 == "+1" || $elem_dermaOs_1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOs_2" name="elem_dermaOs_2" value="2+" <?php echo ($elem_dermaOs_2 == "+2" || $elem_dermaOs_2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOs_3" name="elem_dermaOs_3" value="3+" <?php echo ($elem_dermaOs_3 == "+3" || $elem_dermaOs_3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_dermaOs_4" name="elem_dermaOs_4" value="4+" <?php echo ($elem_dermaOs_4 == "+4" || $elem_dermaOs_4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_dermaOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Dermatochalasis"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Dermatochalasis"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_OrbFat <?php echo $cls_OrbFat; ?>" id="d_OrbFat">
		<td align="left" class="grpbtn" onclick="openSubGrp('OrbFat')">
			<label >Orbital Fat
			<span class="glyphicon <?php echo $arow_OrbFat; ?>"></span></label>			
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'OrbFat');"  name="elem_orbFatOd_text" class="form-control" ><?php echo ($elem_orbFatOd_text);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('OrbFat')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('OrbFat')">
			<label >Orbital Fat
			<span class="glyphicon <?php echo $arow_OrbFat; ?>"></span></label>	
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'OrbFat');"  name="elem_orbFatOs_text" class="form-control" ><?php echo ($elem_orbFatOs_text);?></textarea></td>		
		</tr>
		
		
		<tr class="exmhlgcol grp_OrbFat <?php echo $cls_OrbFat; ?>" >
		<td align="left">Medial Fat Prolapse</td>
		<td>				
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOd_Absent" name="elem_medialFatPrOd_Absent" value="Absent" <?php echo ($elem_medialOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOd_1" name="elem_medialFatPrOd_1" value="1+" <?php echo ($elem_medialFatPrOd_1 == "+1" || $elem_medialFatPrOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOd_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOd_2" name="elem_medialFatPrOd_2" value="2+" <?php echo ($elem_medialFatPrOd_2 == "+2" || $elem_medialFatPrOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOd_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOd_3" name="elem_medialFatPrOd_3" value="3+" <?php echo ($elem_medialFatPrOd_3 == "+3" || $elem_medialFatPrOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOd_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOd_4" name="elem_medialFatPrOd_4" value="4+" <?php echo ($elem_medialFatPrOd_4 == "+4" || $elem_medialFatPrOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOd_4">4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('OrbFat')" >BL</td>
		<td align="left">Medial Fat Prolapse</td>
		<td>				
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOs_Absent" name="elem_medialFatPrOs_Absent" value="Absent" <?php echo ($elem_medialFatPrOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOs_1" name="elem_medialFatPrOs_1" value="1+" <?php echo ($elem_medialFatPrOs_1 == "+1" || $elem_medialFatPrOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOs_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOs_2" name="elem_medialFatPrOs_2" value="2+" <?php echo ($elem_medialFatPrOs_2 == "+2" || $elem_medialFatPrOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOs_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOs_3" name="elem_medialFatPrOs_3" value="3+" <?php echo ($elem_medialFatPrOs_3 == "+3" || $elem_medialFatPrOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOs_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_medialFatPrOs_4" name="elem_medialFatPrOs_4" value="4+" <?php echo ($elem_medialFatPrOs_4 == "+4" || $elem_medialFatPrOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_medialFatPrOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat/Medial Fat Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat/Medial Fat Prolapse"]; }  ?>
		
		<tr class="exmhlgcol grp_OrbFat <?php echo $cls_OrbFat; ?>" >
		<td align="left">Central Fat Prolapse</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOd_Absent" name="elem_centralFatPrOd_Absent" value="Absent" <?php echo ($elem_centralFatPrOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOd_1" name="elem_centralFatPrOd_1" value="1+" <?php echo ($elem_centralFatPrOd_1 == "+1" || $elem_centralFatPrOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOd_1">1+</label>
		</td>
		<td>			

		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOd_2" name="elem_centralFatPrOd_2" value="2+" <?php echo ($elem_centralFatPrOd_2 == "+2" || $elem_centralFatPrOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOd_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOd_3" name="elem_centralFatPrOd_3" value="3+" <?php echo ($elem_centralFatPrOd_3 == "+3" || $elem_centralFatPrOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOd_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOd_4" name="elem_centralFatPrOd_4" value="4+" <?php echo ($elem_centralFatPrOd_4 == "+4" || $elem_centralFatPrOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOd_4">4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('OrbFat')" >BL</td>
		<td align="left">Central Fat Prolapse</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOs_Absent" name="elem_centralFatPrOs_Absent" value="Absent" <?php echo ($elem_centralFatPrOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOs_1" name="elem_centralFatPrOs_1" value="1+" <?php echo ($elem_centralFatPrOs_1 == "+1" || $elem_centralFatPrOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOs_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOs_2" name="elem_centralFatPrOs_2" value="2+" <?php echo ($elem_centralFatPrOs_2 == "+2" || $elem_centralFatPrOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOs_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOs_3" name="elem_centralFatPrOs_3" value="3+" <?php echo ($elem_centralFatPrOs_3 == "+3" || $elem_centralFatPrOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOs_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_centralFatPrOs_4" name="elem_centralFatPrOs_4" value="4+" <?php echo ($elem_centralFatPrOs_4 == "+4" || $elem_centralFatPrOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_centralFatPrOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat/Central Fat Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat/Central Fat Prolapse"]; }  ?>
		
		<tr class="exmhlgcol grp_OrbFat <?php echo $cls_OrbFat; ?>" >
		<td align="left">Sub Brow Fat Prolapse</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOd_Absent" name="elem_subBrFatPrOd_Absent" value="Absent" <?php echo ($elem_subBrFatPrOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOd_1" name="elem_subBrFatPrOd_1" value="1+" <?php echo ($elem_subBrFatPrOd_1 == "+1" || $elem_subBrFatPrOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOd_1">1+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOd_2" name="elem_subBrFatPrOd_2" value="2+" <?php echo ($elem_subBrFatPrOd_2 == "+2" || $elem_subBrFatPrOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOd_2">2+</label>
		</td>
		<td>			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOd_3" name="elem_subBrFatPrOd_3" value="3+" <?php echo ($elem_subBrFatPrOd_3 == "+3" || $elem_subBrFatPrOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOd_3">3+</label>
		</td>
		<td colspan="4">			
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOd_4" name="elem_subBrFatPrOd_4" value="4+" <?php echo ($elem_subBrFatPrOd_4 == "+4" || $elem_subBrFatPrOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOd_4">4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('OrbFat')" >BL</td>
		<td align="left">Sub Brow Fat Prolapse</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOs_Absent" name="elem_subBrFatPrOs_Absent" value="Absent" <?php echo ($elem_subBrFatPrOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOs_1" name="elem_subBrFatPrOs_1" value="1+" <?php echo ($elem_subBrFatPrOs_1 == "+1" || $elem_subBrFatPrOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOs_1">1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOs_2" name="elem_subBrFatPrOs_2" value="2+" <?php echo ($elem_subBrFatPrOs_2 == "+2" || $elem_subBrFatPrOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOs_2">2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOs_3" name="elem_subBrFatPrOs_3" value="3+" <?php echo ($elem_subBrFatPrOs_3 == "+3" || $elem_subBrFatPrOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOs_3">3+</label>
		</td>
		<td colspan="4">
		<input type="checkbox"  onclick="checkAbsent(this,'OrbFat');" id="elem_subBrFatPrOs_4" name="elem_subBrFatPrOs_4" value="4+" <?php echo ($elem_subBrFatPrOs_4 == "+4" || $elem_subBrFatPrOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_subBrFatPrOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat/Sub Brow Fat Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat/Sub Brow Fat Prolapse"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Orbital Fat"]; }  ?>
		
		<tr id="d_LidCrs">
		<td align="left">Lid Crease</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_lidCrsOd_Low" name="elem_lidCrsOd_Low" value="Low" <?php echo ($elem_lidCrsOd_Low == "Low") ? "checked=\"checked\"" : "";?>><label for="elem_lidCrsOd_Low">Low</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_lidCrsOd_Med" name="elem_lidCrsOd_Med" value="Medium" <?php echo ($elem_lidCrsOd_Med == "Medium") ? "checked=\"checked\"" : "";?>><label for="elem_lidCrsOd_Med">Medium</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkwnls()" id="elem_lidCrsOd_High" name="elem_lidCrsOd_High" value="High" <?php echo ($elem_lidCrsOd_High == "High") ? "checked=\"checked\"" : "";?>><label for="elem_lidCrsOd_High">High</label>
		</td>
		<td colspan="5">					
		<label>Measurements</label><input type="text"  onchange="checkwnls()" name="elem_lidCrsOd_Measure" class="form-control" value="<?php echo ($elem_lidCrsOd_Measure);?>" >
		</td>		
		<td align="center" class="bilat" onClick="check_bl('LidCrs')">BL</td>
		<td align="left">Lid Crease</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_lidCrsOs_Low" name="elem_lidCrsOs_Low" value="Low" <?php echo ($elem_lidCrsOs_Low == "Low") ? "checked=\"checked\"" : "";?>><label for="elem_lidCrsOs_Low">Low</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_lidCrsOs_Med" name="elem_lidCrsOs_Med" value="Medium" <?php echo ($elem_lidCrsOs_Med == "Medium") ? "checked=\"checked\"" : "";?>><label for="elem_lidCrsOs_Med">Medium</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkwnls()" id="elem_lidCrsOs_High" name="elem_lidCrsOs_High" value="High" <?php echo ($elem_lidCrsOs_High == "High") ? "checked=\"checked\"" : "";?>><label for="elem_lidCrsOs_High">High</label>
		</td>
		<td colspan="5">					
		<label>Measurements</label> <input type="text"  onchange="checkwnls()" name="elem_lidCrsOs_Measure" class="form-control" value="<?php echo ($elem_lidCrsOs_Measure);?>" >
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Lid Crease"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Lid Crease"]; }  ?>
		
		<tr id="d_LidsEntro">
		<td align="left">Entropion</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOd_Absent" name="elem_entroOd_Absent" value="Absent" <?php echo ($elem_entroOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_entroOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOd_1" name="elem_entroOd_1" value="1+" <?php echo ($elem_entroOd_1 == "+1" || $elem_entroOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_entroOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOd_2" name="elem_entroOd_2" value="2+" <?php echo ($elem_entroOd_2 == "+2" || $elem_entroOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_entroOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOd_3" name="elem_entroOd_3" value="3+" <?php echo ($elem_entroOd_3 == "+3" || $elem_entroOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_entroOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOd_4" name="elem_entroOd_4" value="4+" <?php echo ($elem_entroOd_4 == "+4" || $elem_entroOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_entroOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('LidsEntro')">BL</td>
		<td align="left">Entropion</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOs_Absent" name="elem_entroOs_Absent" value="Absent" <?php echo ($elem_entroOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_entroOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOs_1" name="elem_entroOs_1" value="1+" <?php echo ($elem_entroOs_1 == "+1" || $elem_entroOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_entroOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOs_2" name="elem_entroOs_2" value="2+" <?php echo ($elem_entroOs_2 == "+2" || $elem_entroOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_entroOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOs_3" name="elem_entroOs_3" value="3+" <?php echo ($elem_entroOs_3 == "+3" || $elem_entroOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_entroOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_entroOs_4" name="elem_entroOs_4" value="4+" <?php echo ($elem_entroOs_4 == "+4" || $elem_entroOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_entroOs_4">4+</label>				
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Entropion"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Entropion"]; }  ?>	
		
		<tr id="d_LidsEctro">
		<td align="left">Ectropion</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOd_Absent" name="elem_ectroOd_Absent" value="Absent" <?php echo ($elem_ectroOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_ectroOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOd_1" name="elem_ectroOd_1" value="1+" <?php echo ($elem_ectroOd_1 == "+1" || $elem_ectroOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_ectroOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOd_2" name="elem_ectroOd_2" value="2+" <?php echo ($elem_ectroOd_2 == "+2" || $elem_ectroOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_ectroOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOd_3" name="elem_ectroOd_3" value="3+" <?php echo ($elem_ectroOd_3 == "+3" || $elem_ectroOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_ectroOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOd_4" name="elem_ectroOd_4" value="4+" <?php echo ($elem_ectroOd_4 == "+4" || $elem_ectroOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_ectroOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('LidsEctro')">BL</td>
		<td align="left">Ectropion</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOs_Absent" name="elem_ectroOs_Absent" value="Absent" <?php echo ($elem_ectroOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_ectroOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOs_1" name="elem_ectroOs_1" value="1+" <?php echo ($elem_ectroOs_1 == "+1" || $elem_ectroOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_ectroOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOs_2" name="elem_ectroOs_2" value="2+" <?php echo ($elem_ectroOs_2 == "+2" || $elem_ectroOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_ectroOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOs_3" name="elem_ectroOs_3" value="3+" <?php echo ($elem_ectroOs_3 == "+3" || $elem_ectroOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_ectroOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_ectroOs_4" name="elem_ectroOs_4" value="4+" <?php echo ($elem_ectroOs_4 == "+4" || $elem_ectroOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_ectroOs_4">4+</label>				
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Ectropion"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Ectropion"]; }  ?>
		
		<tr id="d_LidLax">
		<td align="left">Lid Laxity</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOd_Absent" name="elem_lidLaxOd_Absent" value="Absent" <?php echo ($elem_lidLaxOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOd_1" name="elem_lidLaxOd_1" value="1+" <?php echo ($elem_lidLaxOd_1 == "+1" || $elem_lidLaxOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOd_2" name="elem_lidLaxOd_2" value="2+" <?php echo ($elem_lidLaxOd_2 == "+2" || $elem_lidLaxOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOd_3" name="elem_lidLaxOd_3" value="3+" <?php echo ($elem_lidLaxOd_3 == "+3" || $elem_lidLaxOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOd_4" name="elem_lidLaxOd_4" value="4+" <?php echo ($elem_lidLaxOd_4 == "+4" || $elem_lidLaxOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('LidLax')">BL</td>
		<td align="left">Lid Laxity</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOs_Absent" name="elem_lidLaxOs_Absent" value="Absent" <?php echo ($elem_lidLaxOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOs_1" name="elem_lidLaxOs_1" value="1+" <?php echo ($elem_lidLaxOs_1 == "+1" || $elem_lidLaxOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOs_2" name="elem_lidLaxOs_2" value="2+" <?php echo ($elem_lidLaxOs_2 == "+2" || $elem_lidLaxOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOs_3" name="elem_lidLaxOs_3" value="3+" <?php echo ($elem_lidLaxOs_3 == "+3" || $elem_lidLaxOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLaxOs_4" name="elem_lidLaxOs_4" value="4+" <?php echo ($elem_lidLaxOs_4 == "+4" || $elem_lidLaxOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_lidLaxOs_4">4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Lid Laxity"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Lid Laxity"]; }  ?>	
		
		<tr id="d_LidCont">
		<td align="left">Lid Contour</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOd_Good" name="elem_lidContOd_Good" value="Good" <?php echo ($elem_lidContOd_Good == "Good") ? "checked=checked" : "" ;?>><label for="elem_lidContOd_Good">Good</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOd_Peaked" name="elem_lidContOd_Peaked" value="Peaked" <?php echo ($elem_lidContOd_Peaked == "Peaked") ? "checked=checked" : "" ;?>><label for="elem_lidContOd_Peaked">Peaked</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOd_NasalPtosis" name="elem_lidContOd_NasalPtosis" value="Nasal Ptosis" <?php echo ($elem_lidContOd_NasalPtosis == "Nasal Ptosis") ? "checked=checked" : "" ;?>><label for="elem_lidContOd_NasalPtosis">Nasal Ptosis</label>
		</td>
		<td colspan="5">					
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOd_LateralPtosis" name="elem_lidContOd_LateralPtosis" value="Lateral Ptosis" <?php echo ($elem_lidContOd_LateralPtosis == "Lateral Ptosis") ? "checked=checked" : "" ;?>><label for="elem_lidContOd_LateralPtosis">Lateral Ptosis</label>						
		</td>
		<td align="center" class="bilat" onClick="check_bl('LidCont')">BL</td>
		<td align="left">Lid Contour</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOs_Good" name="elem_lidContOs_Good" value="Good" <?php echo ($elem_lidContOs_Good == "Good") ? "checked=checked" : "" ;?>><label for="elem_lidContOs_Good">Good</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOs_Peaked" name="elem_lidContOs_Peaked" value="Peaked" <?php echo ($elem_lidContOs_Peaked == "Peaked") ? "checked=checked" : "" ;?>><label for="elem_lidContOs_Peaked">Peaked</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOs_NasalPtosis" name="elem_lidContOs_NasalPtosis" value="Nasal Ptosis" <?php echo ($elem_lidContOs_NasalPtosis == "Nasal Ptosis") ? "checked=checked" : "" ;?>><label for="elem_lidContOs_NasalPtosis">Nasal Ptosis</label>
		</td>
		<td colspan="5">					
		<input type="checkbox"  onclick="checkwnls();" id="elem_lidContOs_LateralPtosis" name="elem_lidContOs_LateralPtosis" value="Lateral Ptosis" <?php echo ($elem_lidContOs_LateralPtosis == "Lateral Ptosis") ? "checked=checked" : "" ;?>><label for="elem_lidContOs_LateralPtosis">Lateral Ptosis</label>						
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Lid Contour"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Lid Contour"]; }  ?>
		
		<tr id="d_MRD1">
		<td align="left">MRD 1</td>
		<td colspan="2">
			<select name="elem_mrd1Od_sel"  class="form-control">
				<option value=""  ></option>
				<option value="-2 mm" <?php echo ($elem_mrd1Od_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
				<option value="-1 mm" <?php echo ($elem_mrd1Od_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
				<option value="0 mm" <?php echo ($elem_mrd1Od_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
				<option value="0.5 mm" <?php echo ($elem_mrd1Od_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
				<option value="1 mm" <?php echo ($elem_mrd1Od_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
				<option value="1.5 mm" <?php echo ($elem_mrd1Od_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
				<option value="2 mm" <?php echo ($elem_mrd1Od_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
				<option value="2.5 mm" <?php echo ($elem_mrd1Od_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
				<option value="3 mm" <?php echo ($elem_mrd1Od_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
				<option value="3.5 mm" <?php echo ($elem_mrd1Od_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
				<option value="4 mm" <?php echo ($elem_mrd1Od_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
				<option value="5 mm" <?php echo ($elem_mrd1Od_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
				<option value="6 mm" <?php echo ($elem_mrd1Od_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
				<option value="7 mm" <?php echo ($elem_mrd1Od_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
				<option value="8 mm" <?php echo ($elem_mrd1Od_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
			</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd1Od_txt" value="<?php echo ($elem_mrd1Od_txt) ;?>" class="form-control">						
		</td>			
		<td align="center" class="bilat" onClick="check_bl('MRD1')">BL</td>
		<td align="left">MRD 1</td>
		<td colspan="2">
		<select name="elem_mrd1Os_sel" class="form-control" >
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd1Os_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd1Os_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd1Os_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd1Os_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd1Os_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd1Os_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd1Os_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd1Os_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd1Os_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd1Os_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd1Os_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd1Os_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd1Os_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd1Os_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd1Os_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd1Os_txt" value="<?php echo ($elem_mrd1Os_txt) ;?>" class="form-control" >
		</td>			
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/MRD 1"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/MRD 1"]; }  ?>
		
		<tr id="d_MRD1cNS">
		<td align="left">MRD 1 c NS</td>
		<td colspan="2">
		<select name="elem_mrd1cnsOd_sel" class="form-control">
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd1cnsOd_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd1cnsOd_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd1cnsOd_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd1cnsOd_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd1cnsOd_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd1cnsOd_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd1cnsOd_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd1cnsOd_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd1cnsOd_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd1cnsOd_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd1cnsOd_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd1cnsOd_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd1cnsOd_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd1cnsOd_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd1cnsOd_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd1cnsOd_txt" value="<?php echo ($elem_mrd1cnsOd_txt) ;?>" class="form-control">						
		</td>		
		<td align="center" class="bilat" onClick="check_bl('MRD1cNS')">BL</td>
		<td align="left">MRD 1 c NS</td>
		<td colspan="2">
		<select name="elem_mrd1cnsOs_sel" class="form-control">
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd1cnsOs_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd1cnsOs_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd1cnsOs_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd1cnsOs_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd1cnsOs_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd1cnsOs_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd1cnsOs_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd1cnsOs_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd1cnsOs_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd1cnsOs_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd1cnsOs_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd1cnsOs_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd1cnsOs_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd1cnsOs_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd1cnsOs_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd1cnsOs_txt" value="<?php echo ($elem_mrd1cnsOs_txt) ;?>" class="form-control">
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/MRD 1 c NS"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/MRD 1 c NS"]; }  ?>	
		
		<tr id="d_MRD1cBH">
		<td align="left">MRD 1 c brows held up</td>
		<td colspan="2">
		<select name="elem_mrd1cbhOd_sel" class="form-control">
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd1cbhOd_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd1cbhOd_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd1cbhOd_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd1cbhOd_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd1cbhOd_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd1cbhOd_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd1cbhOd_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd1cbhOd_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd1cbhOd_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd1cbhOd_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd1cbhOd_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd1cbhOd_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd1cbhOd_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd1cbhOd_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd1cbhOd_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd1cbhOd_txt" value="<?php echo ($elem_mrd1cbhOd_txt) ;?>" class="form-control">						
		</td>
		
		<td align="center" class="bilat" onClick="check_bl('MRD1cBH')">BL</td>
		<td align="left">MRD 1 c brows held up</td>
		<td colspan="2">
		<select name="elem_mrd1cbhOs_sel" class="form-control">
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd1cbhOs_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd1cbhOs_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd1cbhOs_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd1cbhOs_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd1cbhOs_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd1cbhOs_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd1cbhOs_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd1cbhOs_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd1cbhOs_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd1cbhOs_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd1cbhOs_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd1cbhOs_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd1cbhOs_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd1cbhOs_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd1cbhOs_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd1cbhOs_txt" value="<?php echo ($elem_mrd1cbhOs_txt) ;?>" class="form-control">
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/MRD 1 c brows held up"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/MRD 1 c brows held up"]; }  ?>
		
		<tr id="d_MRD2">
		<td align="left">MRD 2</td>
		<td colspan="2">
		<select name="elem_mrd2Od_sel" class="form-control">
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd2Od_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd2Od_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd2Od_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd2Od_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd2Od_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd2Od_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd2Od_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd2Od_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd2Od_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd2Od_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd2Od_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd2Od_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd2Od_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd2Od_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd2Od_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd2Od_txt" value="<?php echo ($elem_mrd2Od_txt) ;?>" class="form-control">						
		</td>
		
		<td align="center" class="bilat" onClick="check_bl('MRD2')">BL</td>
		<td align="left">MRD 2</td>
		<td colspan="2">
		<select name="elem_mrd2Os_sel" class="form-control">
			<option value=""  ></option>
			<option value="-2 mm" <?php echo ($elem_mrd2Os_sel=="-2 mm") ? "selected" : "";  ?>>-2 mm</option>
			<option value="-1 mm" <?php echo ($elem_mrd2Os_sel=="-1 mm") ? "selected" : "";  ?>>-1 mm</option>
			<option value="0 mm" <?php echo ($elem_mrd2Os_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="0.5 mm" <?php echo ($elem_mrd2Os_sel=="0.5 mm") ? "selected" : "";  ?> >0.5 mm</option>
			<option value="1 mm" <?php echo ($elem_mrd2Os_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="1.5 mm" <?php echo ($elem_mrd2Os_sel=="1.5 mm") ? "selected" : "";  ?>>1.5 mm</option>
			<option value="2 mm" <?php echo ($elem_mrd2Os_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="2.5 mm" <?php echo ($elem_mrd2Os_sel=="2.5 mm") ? "selected" : "";  ?>>2.5 mm</option>
			<option value="3 mm" <?php echo ($elem_mrd2Os_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="3.5 mm" <?php echo ($elem_mrd2Os_sel=="3.5 mm") ? "selected" : "";  ?>>3.5 mm</option>
			<option value="4 mm" <?php echo ($elem_mrd2Os_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_mrd2Os_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_mrd2Os_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_mrd2Os_sel=="7 mm") ? "selected" : "";  ?>>7 mm</option>
			<option value="8 mm" <?php echo ($elem_mrd2Os_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_mrd2Os_txt" value="<?php echo ($elem_mrd2Os_txt) ;?>" class="form-control">
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/MRD 2"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/MRD 2"]; }  ?>
		
		<tr id="d_SupSS">
		<td align="left">Sup Scleral Show</td>
		<td colspan="2">
		<select name="elem_supSSOd_sel" class="form-control">
			<option value=""  ></option>
			<option value="0 mm" <?php echo ($elem_supSSOd_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="1 mm" <?php echo ($elem_supSSOd_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="2 mm" <?php echo ($elem_supSSOd_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="3 mm" <?php echo ($elem_supSSOd_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="4 mm" <?php echo ($elem_supSSOd_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_supSSOd_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_supSSOd_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>							
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_supSSOd_txt" value="<?php echo ($elem_supSSOd_txt) ;?>" class="form-control">						
		</td>
		
		<td align="center" class="bilat" onClick="check_bl('SupSS')">BL</td>
		<td align="left">Sup Scleral Show</td>
		<td colspan="2">
		<select name="elem_supSSOs_sel" class="form-control">
			<option value=""  ></option>
			<option value="0 mm" <?php echo ($elem_supSSOs_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="1 mm" <?php echo ($elem_supSSOs_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="2 mm" <?php echo ($elem_supSSOs_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="3 mm" <?php echo ($elem_supSSOs_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="4 mm" <?php echo ($elem_supSSOs_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_supSSOs_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_supSSOs_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>							
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_supSSOs_txt" value="<?php echo ($elem_supSSOs_txt) ;?>" class="form-control">
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Sup Scleral Show"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Sup Scleral Show"]; }  ?>
		
		<tr id="d_VFH">
		<td align="left">VFH</td>
		<td colspan="2">
		<select name="elem_VFHOd_sel" class="form-control">
			<option value=""  ></option>
			<option value="0 mm" <?php echo ($elem_VFHOd_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="1 mm" <?php echo ($elem_VFHOd_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="2 mm" <?php echo ($elem_VFHOd_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="3 mm" <?php echo ($elem_VFHOd_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="4 mm" <?php echo ($elem_VFHOd_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_VFHOd_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_VFHOd_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>			
			<option value="7 mm" <?php echo ($elem_VFHOd_sel=="7 mm") ? "selected" : "";  ?> >7 mm</option>
			<option value="8 mm" <?php echo ($elem_VFHOd_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
			<option value="9 mm" <?php echo ($elem_VFHOd_sel=="9 mm") ? "selected" : "";  ?>>9 mm</option>
			<option value="10 mm" <?php echo ($elem_VFHOd_sel=="10 mm") ? "selected" : "";  ?>>10 mm</option>
			<option value="11 mm" <?php echo ($elem_VFHOd_sel=="11 mm") ? "selected" : "";  ?>>11 mm</option>
			<option value="12 mm" <?php echo ($elem_VFHOd_sel=="12 mm") ? "selected" : "";  ?>>12 mm</option>
			<option value="13 mm" <?php echo ($elem_VFHOd_sel=="13 mm") ? "selected" : "";  ?>>13 mm</option>			
			<option value="14 mm" <?php echo ($elem_VFHOd_sel=="14 mm") ? "selected" : "";  ?> >14 mm</option>
			<option value="15 mm" <?php echo ($elem_VFHOd_sel=="15 mm") ? "selected" : "";  ?>>15 mm</option>
			<option value="16 mm" <?php echo ($elem_VFHOd_sel=="16 mm") ? "selected" : "";  ?>>16 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_VFHOd_txt" value="<?php echo ($elem_VFHOd_txt) ;?>" class="form-control">						
		</td>		
		<td align="center" class="bilat" onClick="check_bl('VFH')">BL</td>
		<td align="left">VFH</td>
		<td colspan="2">
		<select name="elem_VFHOs_sel" class="form-control">
			<option value=""  ></option>
			<option value="0 mm" <?php echo ($elem_VFHOs_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="1 mm" <?php echo ($elem_VFHOs_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="2 mm" <?php echo ($elem_VFHOs_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="3 mm" <?php echo ($elem_VFHOs_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="4 mm" <?php echo ($elem_VFHOs_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_VFHOs_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_VFHOs_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>			
			<option value="7 mm" <?php echo ($elem_VFHOs_sel=="7 mm") ? "selected" : "";  ?> >7 mm</option>
			<option value="8 mm" <?php echo ($elem_VFHOs_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
			<option value="9 mm" <?php echo ($elem_VFHOs_sel=="9 mm") ? "selected" : "";  ?>>9 mm</option>
			<option value="10 mm" <?php echo ($elem_VFHOs_sel=="10 mm") ? "selected" : "";  ?>>10 mm</option>
			<option value="11 mm" <?php echo ($elem_VFHOs_sel=="11 mm") ? "selected" : "";  ?>>11 mm</option>
			<option value="12 mm" <?php echo ($elem_VFHOs_sel=="12 mm") ? "selected" : "";  ?>>12 mm</option>
			<option value="13 mm" <?php echo ($elem_VFHOs_sel=="13 mm") ? "selected" : "";  ?>>13 mm</option>			
			<option value="14 mm" <?php echo ($elem_VFHOs_sel=="14 mm") ? "selected" : "";  ?> >14 mm</option>
			<option value="15 mm" <?php echo ($elem_VFHOs_sel=="15 mm") ? "selected" : "";  ?>>15 mm</option>
			<option value="16 mm" <?php echo ($elem_VFHOs_sel=="16 mm") ? "selected" : "";  ?>>16 mm</option>
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_VFHOs_txt" value="<?php echo ($elem_VFHOs_txt) ;?>" class="form-control">
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/VFH"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/VFH"]; }  ?>
		
		<tr id="d_LevFun">
		<td align="left">Levator Function</td>
		<td colspan="2">
		<select name="elem_LevFunOd_sel" class="form-control">
			<option value=""  ></option>
			<option value="0 mm" <?php echo ($elem_LevFunOd_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="1 mm" <?php echo ($elem_LevFunOd_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="2 mm" <?php echo ($elem_LevFunOd_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="3 mm" <?php echo ($elem_LevFunOd_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="4 mm" <?php echo ($elem_LevFunOd_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_LevFunOd_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_LevFunOd_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_LevFunOd_sel=="7 mm") ? "selected" : "";  ?> >7 mm</option>
			<option value="8 mm" <?php echo ($elem_LevFunOd_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
			<option value="9 mm" <?php echo ($elem_LevFunOd_sel=="9 mm") ? "selected" : "";  ?>>9 mm</option>
			<option value="10 mm" <?php echo ($elem_LevFunOd_sel=="10 mm") ? "selected" : "";  ?>>10 mm</option>
			<option value="11 mm" <?php echo ($elem_LevFunOd_sel=="11 mm") ? "selected" : "";  ?>>11 mm</option>
			<option value="12 mm" <?php echo ($elem_LevFunOd_sel=="12 mm") ? "selected" : "";  ?>>12 mm</option>
			<option value="13 mm" <?php echo ($elem_LevFunOd_sel=="13 mm") ? "selected" : "";  ?>>13 mm</option>
			<option value="14 mm" <?php echo ($elem_LevFunOd_sel=="14 mm") ? "selected" : "";  ?> >14 mm</option>
			<option value="15 mm" <?php echo ($elem_LevFunOd_sel=="15 mm") ? "selected" : "";  ?>>15 mm</option>
			<option value="16 mm" <?php echo ($elem_LevFunOd_sel=="16 mm") ? "selected" : "";  ?>>16 mm</option>
			<option value="17 mm" <?php echo ($elem_LevFunOd_sel=="17 mm") ? "selected" : "";  ?>>17 mm</option>
			<option value="18 mm" <?php echo ($elem_LevFunOd_sel=="18 mm") ? "selected" : "";  ?>>18 mm</option>
			<option value="19 mm" <?php echo ($elem_LevFunOd_sel=="19 mm") ? "selected" : "";  ?>>19 mm</option>
			<option value="20 mm" <?php echo ($elem_LevFunOd_sel=="20 mm") ? "selected" : "";  ?>>20 mm</option>	
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_LevFunOd_txt" value="<?php echo ($elem_LevFunOd_txt) ;?>" class="form-control">						
		</td>		
		<td align="center" class="bilat" onClick="check_bl('LevFun')">BL</td>
		<td align="left">Levator Function</td>
		<td colspan="2">
		<select name="elem_LevFunOs_sel" class="form-control" >
			<option value=""  ></option>
			<option value="0 mm" <?php echo ($elem_LevFunOs_sel=="0 mm") ? "selected" : "";  ?> >0 mm</option>
			<option value="1 mm" <?php echo ($elem_LevFunOs_sel=="1 mm") ? "selected" : "";  ?>>1 mm</option>
			<option value="2 mm" <?php echo ($elem_LevFunOs_sel=="2 mm") ? "selected" : "";  ?>>2 mm</option>
			<option value="3 mm" <?php echo ($elem_LevFunOs_sel=="3 mm") ? "selected" : "";  ?>>3 mm</option>
			<option value="4 mm" <?php echo ($elem_LevFunOs_sel=="4 mm") ? "selected" : "";  ?>>4 mm</option>
			<option value="5 mm" <?php echo ($elem_LevFunOs_sel=="5 mm") ? "selected" : "";  ?>>5 mm</option>
			<option value="6 mm" <?php echo ($elem_LevFunOs_sel=="6 mm") ? "selected" : "";  ?>>6 mm</option>
			<option value="7 mm" <?php echo ($elem_LevFunOd_sel=="7 mm") ? "selected" : "";  ?> >7 mm</option>
			<option value="8 mm" <?php echo ($elem_LevFunOd_sel=="8 mm") ? "selected" : "";  ?>>8 mm</option>
			<option value="9 mm" <?php echo ($elem_LevFunOd_sel=="9 mm") ? "selected" : "";  ?>>9 mm</option>
			<option value="10 mm" <?php echo ($elem_LevFunOd_sel=="10 mm") ? "selected" : "";  ?>>10 mm</option>
			<option value="11 mm" <?php echo ($elem_LevFunOd_sel=="11 mm") ? "selected" : "";  ?>>11 mm</option>
			<option value="12 mm" <?php echo ($elem_LevFunOd_sel=="12 mm") ? "selected" : "";  ?>>12 mm</option>
			<option value="13 mm" <?php echo ($elem_LevFunOd_sel=="13 mm") ? "selected" : "";  ?>>13 mm</option>	
			<option value="14 mm" <?php echo ($elem_LevFunOd_sel=="14 mm") ? "selected" : "";  ?> >14 mm</option>
			<option value="15 mm" <?php echo ($elem_LevFunOd_sel=="15 mm") ? "selected" : "";  ?>>15 mm</option>
			<option value="16 mm" <?php echo ($elem_LevFunOd_sel=="16 mm") ? "selected" : "";  ?>>16 mm</option>
			<option value="17 mm" <?php echo ($elem_LevFunOd_sel=="17 mm") ? "selected" : "";  ?>>17 mm</option>
			<option value="18 mm" <?php echo ($elem_LevFunOd_sel=="18 mm") ? "selected" : "";  ?>>18 mm</option>
			<option value="19 mm" <?php echo ($elem_LevFunOd_sel=="19 mm") ? "selected" : "";  ?>>19 mm</option>
			<option value="20 mm" <?php echo ($elem_LevFunOd_sel=="20 mm") ? "selected" : "";  ?>>20 mm</option>	
		</select>
		</td>
		<td colspan="6">
		<input type="text"  onchange="checkwnls();" name="elem_LevFunOs_txt" value="<?php echo ($elem_LevFunOs_txt) ;?>" class="form-control">
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Levator Function"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Levator Function"]; }  ?>
		
		<tr id="d_Lago">
		<td align="left">Lagophthalmos</td>
		<td colspan="8"><input type="text"  onclick="checkwnls();" name="elem_LagoOd_txt" value="<?php echo ($elem_LagoOd_txt) ;?>" class="form-control"></td>		
		<td align="center" class="bilat" onClick="check_bl('Lago')">BL</td>
		<td align="left">Lagophthalmos</td>
		<td colspan="8"><input type="text"  onclick="checkwnls();" name="elem_LagoOs_txt" value="<?php echo ($elem_LagoOs_txt) ;?>" class="form-control"></td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Lagophthalmos"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Lagophthalmos"]; }  ?>
		
		<tr id="d_LidLag">
		<td align="left">Lid Lag</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOd_Absent" name="elem_lidLagOd_Absent" value="Absent" <?php echo ($elem_lidLagOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_lidLagOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOd_1" name="elem_lidLagOd_1" value="1+" <?php echo ($elem_lidLagOd_1 == "+1" || $elem_lidLagOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOd_1">1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOd_2" name="elem_lidLagOd_2" value="2+" <?php echo ($elem_lidLagOd_2 == "+2" || $elem_lidLagOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOd_2">2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOd_3" name="elem_lidLagOd_3" value="3+" <?php echo ($elem_lidLagOd_3 == "+3" || $elem_lidLagOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOd_3">3+</label>
		</td>
		<td colspan="4">
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOd_4" name="elem_lidLagOd_4" value="4+" <?php echo ($elem_lidLaxOd_4 == "+4" || $elem_lidLaxOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('LidLag')">BL</td>
		<td align="left">Lid Lag</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOs_Absent" name="elem_lidLagOs_Absent" value="Absent" <?php echo ($elem_lidLagOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_lidLagOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOs_1" name="elem_lidLagOs_1" value="1+" <?php echo ($elem_lidLagOs_1 == "+1" || $elem_lidLagOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOs_1">1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOs_2" name="elem_lidLagOs_2" value="2+" <?php echo ($elem_lidLagOs_2 == "+2" || $elem_lidLagOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOs_2">2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOs_3" name="elem_lidLagOs_3" value="3+" <?php echo ($elem_lidLagOs_3 == "+3" || $elem_lidLagOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOs_3">3+</label>
		</td>
		<td colspan="4">
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_lidLagOs_4" name="elem_lidLagOs_4" value="4+" <?php echo ($elem_lidLaxOs_4 == "+4" || $elem_lidLaxOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_lidLagOs_4">4+</label>				
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Lid Lag"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Lid Lag"]; }  ?>
		
		<tr id="d_LacGlPr">
		<td align="left">Lacrimal Gland Prolapse</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOd_Absent" name="elem_LacGlPrOd_Absent" value="Absent" <?php echo ($elem_LacGlPrOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOd_1" name="elem_LacGlPrOd_1" value="1+" <?php echo ($elem_LacGlPrOd_1 == "+1" || $elem_LacGlPrOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOd_2" name="elem_LacGlPrOd_2" value="2+" <?php echo ($elem_LacGlPrOd_2 == "+2" || $elem_LacGlPrOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOd_3" name="elem_LacGlPrOd_3" value="3+" <?php echo ($elem_LacGlPrOd_3 == "+3" || $elem_LacGlPrOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOd_4" name="elem_LacGlPrOd_4" value="4+" <?php echo ($elem_LacGlPrOd_4 == "+4" || $elem_LacGlPrOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('LacGlPr')">BL</td>
		<td align="left">Lacrimal Gland Prolapse</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOs_Absent" name="elem_LacGlPrOs_Absent" value="Absent" <?php echo ($elem_LacGlPrOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOs_1" name="elem_LacGlPrOs_1" value="1+" <?php echo ($elem_LacGlPrOs_1 == "+1" || $elem_LacGlPrOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOs_2" name="elem_LacGlPrOs_2" value="2+" <?php echo ($elem_LacGlPrOs_2 == "+2" || $elem_LacGlPrOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOs_3" name="elem_LacGlPrOs_3" value="3+" <?php echo ($elem_LacGlPrOs_3 == "+3" || $elem_LacGlPrOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LacGlPrOs_4" name="elem_LacGlPrOs_4" value="4+" <?php echo ($elem_LacGlPrOs_4 == "+4" || $elem_LacGlPrOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LacGlPrOs_4">4+</label>				
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Lacrimal Gland Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Lacrimal Gland Prolapse"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_PtoVF <?php echo $cls_PtoVF; ?>" id="d_PtoVF">
		<td align="left" class="grpbtn" onclick="openSubGrp('PtoVF')">
			<label >Ptosis VF
			<span class="glyphicon <?php echo $arow_PtoVF; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'PtoVF');"  name="elem_PtoVFOd_text" class="form-control" ><?php echo ($elem_PtoVFOd_text);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('PtoVF')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('PtoVF')">
			<label >Ptosis VF 
			<span class="glyphicon <?php echo $arow_PtoVF; ?>"></span></label>
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'PtoVF');"  name="elem_PtoVFOs_text" class="form-control" ><?php echo ($elem_PtoVFOs_text);?></textarea></td>		
		</tr>
		
		<tr class="exmhlgcol grp_PtoVF <?php echo $cls_PtoVF; ?>" >
		<td align="left">Degree loss without taping</td>
		<td colspan="8" class="form-inline">
			<div class="form-group">
			<select name="elem_DegLossOd_degree" id="elem_DegLossOd_degree" class="form-control">
				<option value=""  ></option>
				<option value="0" <?php echo ($elem_DegLossOd_degree=="0") ? "selected" : "" ; ?>>0</option>
				<option value="5" <?php echo ($elem_DegLossOd_degree=="5") ? "selected" : "" ; ?> >5</option>
				<option value="10" <?php echo ($elem_DegLossOd_degree=="10") ? "selected" : "" ; ?>>10</option>
				<option value="15" <?php echo ($elem_DegLossOd_degree=="15") ? "selected" : "" ; ?>>15</option>
				<option value="20" <?php echo ($elem_DegLossOd_degree=="20") ? "selected" : "" ; ?>>20</option>
				<option value="25" <?php echo ($elem_DegLossOd_degree=="25") ? "selected" : "" ; ?>>25</option>
				<option value="30" <?php echo ($elem_DegLossOd_degree=="30") ? "selected" : "" ; ?>>30</option>						
				<option value="35" <?php echo ($elem_DegLossOd_degree=="35") ? "selected" : "" ; ?>>35</option>
			</select>
			<label for="elem_DegLossOd_degree">degrees</label>
			</div>
		</td>		
		<td align="center" class="bilat"  onClick="check_bl('PtoVF')">BL</td>
		<td align="left">Degree loss without taping</td>
		<td colspan="8" class="form-inline">
			<div class="form-group">
			<select name="elem_DegLossOs_degree" id="elem_DegLossOs_degree" class="form-control">
				<option value=""  ></option>
				<option value="0" <?php echo ($elem_DegLossOs_degree=="0") ? "selected" : "" ; ?>>0</option>
				<option value="5" <?php echo ($elem_DegLossOs_degree=="5") ? "selected" : "" ; ?> >5</option>
				<option value="10" <?php echo ($elem_DegLossOs_degree=="10") ? "selected" : "" ; ?>>10</option>
				<option value="15" <?php echo ($elem_DegLossOs_degree=="15") ? "selected" : "" ; ?>>15</option>
				<option value="20" <?php echo ($elem_DegLossOs_degree=="20") ? "selected" : "" ; ?>>20</option>
				<option value="25" <?php echo ($elem_DegLossOs_degree=="25") ? "selected" : "" ; ?>>25</option>
				<option value="30" <?php echo ($elem_DegLossOs_degree=="30") ? "selected" : "" ; ?>>30</option>						
				<option value="35" <?php echo ($elem_DegLossOs_degree=="35") ? "selected" : "" ; ?>>35</option>
			</select>
			<label for="elem_DegLossOs_degree">degrees</label>
			</div>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF/Degree loss without taping"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF/Degree loss without taping"]; }  ?>
		
		<tr class="exmhlgcol grp_PtoVF <?php echo $cls_PtoVF; ?>" >
		<td align="left">Degree improvement with lid taped</td>
		<td colspan="8" class="form-inline">
			<div class="form-group">
			<select id="elem_DegLossOd_degreeImp" name="elem_DegLossOd_degreeImp" class="form-control">
				<option value=""  ></option>
				<option value="0" <?php echo ($elem_DegLossOd_degreeImp=="0") ? "selected" : "" ; ?>>0</option>
				<option value="5" <?php echo ($elem_DegLossOd_degreeImp=="5") ? "selected" : "" ; ?> >5</option>
				<option value="10" <?php echo ($elem_DegLossOd_degreeImp=="10") ? "selected" : "" ; ?>>10</option>
				<option value="15" <?php echo ($elem_DegLossOd_degreeImp=="15") ? "selected" : "" ; ?>>15</option>
				<option value="20" <?php echo ($elem_DegLossOd_degreeImp=="20") ? "selected" : "" ; ?>>20</option>
				<option value="25" <?php echo ($elem_DegLossOd_degreeImp=="25") ? "selected" : "" ; ?>>25</option>
				<option value="30" <?php echo ($elem_DegLossOd_degreeImp=="30") ? "selected" : "" ; ?>>30</option>						
				<option value="35" <?php echo ($elem_DegLossOd_degreeImp=="35") ? "selected" : "" ; ?>>35</option>
			</select>
			<label for="elem_DegLossOd_degreeImp">degrees</label>
			</div>
		</td>
		<td align="center" class="bilat"  onClick="check_bl('PtoVF')">BL</td>	
		<td align="left">Degree improvement with lid taped</td>
		<td colspan="8" class="form-inline">
			<div class="form-group">
			<select name="elem_DegLossOs_degreeImp" id="elem_DegLossOs_degreeImp" class="form-control">
				<option value=""  ></option>
				<option value="0" <?php echo ($elem_DegLossOs_degreeImp=="0") ? "selected" : "" ; ?>>0</option>
				<option value="5" <?php echo ($elem_DegLossOs_degreeImp=="5") ? "selected" : "" ; ?> >5</option>
				<option value="10" <?php echo ($elem_DegLossOs_degreeImp=="10") ? "selected" : "" ; ?>>10</option>
				<option value="15" <?php echo ($elem_DegLossOs_degreeImp=="15") ? "selected" : "" ; ?>>15</option>
				<option value="20" <?php echo ($elem_DegLossOs_degreeImp=="20") ? "selected" : "" ; ?>>20</option>
				<option value="25" <?php echo ($elem_DegLossOs_degreeImp=="25") ? "selected" : "" ; ?>>25</option>
				<option value="30" <?php echo ($elem_DegLossOs_degreeImp=="30") ? "selected" : "" ; ?>>30</option>						
				<option value="35" <?php echo ($elem_DegLossOs_degreeImp=="35") ? "selected" : "" ; ?>>35</option>
			</select>
			<label for="elem_DegLossOs_degreeImp">degrees</label>
			</div>
		</td>		
		</tr>	
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF/Degree improvement with lid taped"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF/Degree improvement with lid taped"]; }  ?>

		<tr class="exmhlgcol grp_PtoVF <?php echo $cls_PtoVF; ?>" >
		<td align="left">% improvement with lids taped</td>
		<td colspan="8" class="form-inline">
			<div class="form-group">
			<select name="elem_PercLossOd_degreeImp" id="elem_PercLossOd_degreeImp" class="form-control">
				<option value=""  ></option>
				<option value="0" <?php echo ($elem_PercLossOd_degreeImp=="0") ? "selected" : "" ; ?>>0</option>
				<option value="5" <?php echo ($elem_PercLossOd_degreeImp=="5") ? "selected" : "" ; ?> >5</option>
				<option value="10" <?php echo ($elem_PercLossOd_degreeImp=="10") ? "selected" : "" ; ?>>10</option>
				<option value="15" <?php echo ($elem_PercLossOd_degreeImp=="15") ? "selected" : "" ; ?>>15</option>
				<option value="20" <?php echo ($elem_PercLossOd_degreeImp=="20") ? "selected" : "" ; ?>>20</option>
				<option value="25" <?php echo ($elem_PercLossOd_degreeImp=="25") ? "selected" : "" ; ?>>25</option>
				<option value="30" <?php echo ($elem_PercLossOd_degreeImp=="30") ? "selected" : "" ; ?>>30</option>						
				<option value="40" <?php echo ($elem_PercLossOd_degreeImp=="40") ? "selected" : "" ; ?>>40</option>
				<option value="50" <?php echo ($elem_PercLossOd_degreeImp=="50") ? "selected" : "" ; ?>>50</option>
				<option value="60" <?php echo ($elem_PercLossOd_degreeImp=="60") ? "selected" : "" ; ?>>60</option>
				<option value="70" <?php echo ($elem_PercLossOd_degreeImp=="70") ? "selected" : "" ; ?>>70</option>
				<option value="80" <?php echo ($elem_PercLossOd_degreeImp=="80") ? "selected" : "" ; ?>>80</option>
				<option value="90" <?php echo ($elem_PercLossOd_degreeImp=="90") ? "selected" : "" ; ?>>90</option>
				<option value="100" <?php echo ($elem_PercLossOd_degreeImp=="100") ? "selected" : "" ; ?>>100</option>
			</select>
			<label for="elem_PercLossOd_degreeImp">degrees</label>
			</div>
		</td>		
		<td align="center" class="bilat"  onClick="check_bl('PtoVF')">BL</td>
		<td align="left">% improvement with lids taped</td>
		<td colspan="8" class="form-inline">
			<div class="form-group">
			<select id="elem_PercLossOs_degreeImp" name="elem_PercLossOs_degreeImp" class="form-control">
				<option value=""  ></option>
				<option value="0" <?php echo ($elem_PercLossOs_degreeImp=="0") ? "selected" : "" ; ?>>0</option>
				<option value="5" <?php echo ($elem_PercLossOs_degreeImp=="5") ? "selected" : "" ; ?> >5</option>
				<option value="10" <?php echo ($elem_PercLossOs_degreeImp=="10") ? "selected" : "" ; ?>>10</option>
				<option value="15" <?php echo ($elem_PercLossOs_degreeImp=="15") ? "selected" : "" ; ?>>15</option>
				<option value="20" <?php echo ($elem_PercLossOs_degreeImp=="20") ? "selected" : "" ; ?>>20</option>
				<option value="25" <?php echo ($elem_PercLossOs_degreeImp=="25") ? "selected" : "" ; ?>>25</option>
				<option value="30" <?php echo ($elem_PercLossOs_degreeImp=="30") ? "selected" : "" ; ?>>30</option>						
				<option value="40" <?php echo ($elem_PercLossOs_degreeImp=="40") ? "selected" : "" ; ?>>40</option>
				<option value="50" <?php echo ($elem_PercLossOs_degreeImp=="50") ? "selected" : "" ; ?>>50</option>
				<option value="60" <?php echo ($elem_PercLossOs_degreeImp=="60") ? "selected" : "" ; ?>>60</option>
				<option value="70" <?php echo ($elem_PercLossOs_degreeImp=="70") ? "selected" : "" ; ?>>70</option>
				<option value="80" <?php echo ($elem_PercLossOs_degreeImp=="80") ? "selected" : "" ; ?>>80</option>
				<option value="90" <?php echo ($elem_PercLossOs_degreeImp=="90") ? "selected" : "" ; ?>>90</option>
				<option value="100" <?php echo ($elem_PercLossOs_degreeImp=="100") ? "selected" : "" ; ?>>100</option>
			</select> 
			<label for="elem_PercLossOs_degreeImp">degrees</label>
			</div>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF/% improvement with lids taped"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF/% improvement with lids taped"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Ptosis VF"]; }  ?>
		
		<tr id="d_PunEctro">
		<td align="left">Punctal Ectropion</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOd_Absent" name="elem_PunEctroOd_Absent" value="Absent" <?php echo ($elem_PunEctroOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOd_1" name="elem_PunEctroOd_1" value="1+" <?php echo ($elem_PunEctroOd_1 == "+1" || $elem_PunEctroOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOd_2" name="elem_PunEctroOd_2" value="2+" <?php echo ($elem_PunEctroOd_2 == "+2" || $elem_PunEctroOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOd_3" name="elem_PunEctroOd_3" value="3+" <?php echo ($elem_PunEctroOd_3 == "+3" || $elem_PunEctroOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOd_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOd_4" name="elem_PunEctroOd_4" value="4+" <?php echo ($elem_PunEctroOd_4 == "+4" || $elem_PunEctroOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('PunEctro')">BL</td>
		<td align="left">Punctal Ectropion</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOs_Absent" name="elem_PunEctroOs_Absent" value="Absent" <?php echo ($elem_PunEctroOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOs_1" name="elem_PunEctroOs_1" value="1+" <?php echo ($elem_PunEctroOs_1 == "+1" || $elem_PunEctroOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOs_2" name="elem_PunEctroOs_2" value="2+" <?php echo ($elem_PunEctroOs_2 == "+2" || $elem_PunEctroOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOs_3" name="elem_PunEctroOs_3" value="3+" <?php echo ($elem_PunEctroOs_3 == "+3" || $elem_PunEctroOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunEctroOs_4" name="elem_PunEctroOs_4" value="4+" <?php echo ($elem_PunEctroOs_4 == "+4" || $elem_PunEctroOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_PunEctroOs_4">4+</label>				
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Punctal Ectropion"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Punctal Ectropion"]; }  ?>
		
		<tr id="d_PunSten">
		<td align="left">Punctal Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOd_Absent" name="elem_PunStenOd_Absent" value="Absent" <?php echo ($elem_PunStenOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_PunStenOd_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOd_1" name="elem_PunStenOd_1" value="1+" <?php echo ($elem_PunStenOd_1 == "+1" || $elem_PunStenOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOd_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOd_2" name="elem_PunStenOd_2" value="2+" <?php echo ($elem_PunStenOd_2 == "+2" || $elem_PunStenOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOd_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOd_3" name="elem_PunStenOd_3" value="3+" <?php echo ($elem_PunStenOd_3 == "+3" || $elem_PunStenOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOd_3">3+</label>
		</td>
		<td colspan="4"> 					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOd_4" name="elem_PunStenOd_4" value="4+" <?php echo ($elem_PunStenOd_4 == "+4" || $elem_PunStenOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOd_4">4+</label>				
		</td>
		<td align="center" class="bilat" onClick="check_bl('PunSten')">BL</td>
		<td align="left">Punctal Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOs_Absent" name="elem_PunStenOs_Absent" value="Absent" <?php echo ($elem_PunStenOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_PunStenOs_Absent">Absent</label>        
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOs_1" name="elem_PunStenOs_1" value="1+" <?php echo ($elem_PunStenOs_1 == "+1" || $elem_PunStenOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOs_1">1+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOs_2" name="elem_PunStenOs_2" value="2+" <?php echo ($elem_PunStenOs_2 == "+2" || $elem_PunStenOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOs_2">2+</label>
		</td>
		<td>					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOs_3" name="elem_PunStenOs_3" value="3+" <?php echo ($elem_PunStenOs_3 == "+3" || $elem_PunStenOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOs_3">3+</label>
		</td>
		<td colspan="4">					
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_PunStenOs_4" name="elem_PunStenOs_4" value="4+" <?php echo ($elem_PunStenOs_4 == "+4" || $elem_PunStenOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_PunStenOs_4">4+</label>				
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Upper Lids/Punctal Stenosis"])){ echo $arr_exm_ext_htm["Lids"]["Upper Lids/Punctal Stenosis"]; }  ?>
		
		<tr id="d_adcmnt_lids_upr">
		<td align="left"><strong>Comments</strong></td>
		<td colspan="8" align="left" id="d_adcmnt_lids_upr_od"><textarea onBlur="checkwnls();" id="lidsAdCmntUprOd" name="lidsAdCmntUprOd"  rows="3" class="form-control"><?php echo ($lidsAdCmntUprOd);?></textarea></td>
		<td align="center" class="bilat" onClick="check_bl('adcmnt_lids_upr')">BL</td>
		<td align="left"><strong>Comments</strong></td>
		<td colspan="8" align="left" id="d_adcmnt_lids_upr_os"><textarea onBlur="checkwnls();" id="lidsAdCmntUprOs" name="lidsAdCmntUprOs" rows="3" class="form-control"><?php echo ($lidsAdCmntUprOs);?></textarea>   </td>
		</tr>
		
		</table>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane la-advance" id="div7">
		
		<div class="table-responsive">
		<table class="table table-bordered table-striped" >
			<tr>
			<td colspan="9" align="center"><!--<button class="upperlid" type="submit">Upper Lid</button> <button class="lowerlid" type="submit">Lower Lid</button>--></td>
			<td width="61" align="center" class="bilat bilat_all" onClick="check_bilateral('lids_advc')"><strong>Bilateral</strong></td>
			<td colspan="9" align="center">&nbsp;</td>
			</tr>
			
			<tr id="d_LLP">
			<td align="left">Lower Lid Position</td>
			<td>
			<input type="checkbox"  onclick="checkwnls();" id="elem_LLPOd_Normal" name="elem_LLPOd_Normal" value="Normal" <?php echo ($elem_LLPOd_Normal == "Normal") ? "checked=checked" : "" ;?>><label for="elem_LLPOd_Normal">Normal</label>        
			</td>
			<td colspan="2">
			<input type="checkbox"  onclick="checkwnls();" id="elem_LLPOd_MedRet" name="elem_LLPOd_MedRet" value="Medial Retraction" <?php echo ($elem_LLPOd_MedRet == "Medial Retraction") ? "checked=checked" : "" ;?>><label for="elem_LLPOd_MedRet">Medial Retraction</label>
			</td>
			<td colspan="5">					
			<input type="checkbox"  onclick="checkwnls();" id="elem_LLPOd_LateRet" name="elem_LLPOd_LateRet" value="Lateral Retraction" <?php echo ($elem_LLPOd_LateRet == "Lateral Retraction") ? "checked=checked" : "" ;?>><label for="elem_LLPOd_LateRet">Lateral Retraction</label>						
			</td>			
			<td align="center" class="bilat" onClick="check_bl('LLP')">BL</td>
			<td align="left">Lower Lid Position</td>
			<td>
			<input type="checkbox"  onclick="checkwnls();" id="elem_LLPOs_Normal" name="elem_LLPOs_Normal" value="Normal" <?php echo ($elem_LLPOs_Normal == "Normal") ? "checked=checked" : "" ;?>><label for="elem_LLPOs_Normal">Normal</label>        
			</td>
			<td colspan="2">
			<input type="checkbox"  onclick="checkwnls();" id="elem_LLPOs_MedRet" name="elem_LLPOs_MedRet" value="Medial Retraction" <?php echo ($elem_LLPOs_MedRet == "Medial Retraction") ? "checked=checked" : "" ;?>><label for="elem_LLPOs_MedRet">Medial Retraction</label>
			</td>
			<td colspan="5">					
			<input type="checkbox"  onclick="checkwnls();" id="elem_LLPOs_LateRet" name="elem_LLPOs_LateRet" value="Lateral Retraction" <?php echo ($elem_LLPOs_LateRet == "Lateral Retraction") ? "checked=checked" : "" ;?>><label for="elem_LLPOs_LateRet">Lateral Retraction</label>
			</td>			
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Lower Lid Position"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Lower Lid Position"]; }  ?>
			
			<tr id="d_Lax">
			<td align="left">Laxity</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOd_Absent" name="elem_LaxOd_Absent" value="Absent" <?php echo ($elem_LaxOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LaxOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOd_1" name="elem_LaxOd_1" value="1+" <?php echo ($elem_LaxOd_1 == "+1" || $elem_LaxOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LaxOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOd_2" name="elem_LaxOd_2" value="2+" <?php echo ($elem_LaxOd_2 == "+2" || $elem_LaxOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LaxOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOd_3" name="elem_LaxOd_3" value="3+" <?php echo ($elem_LaxOd_3 == "+3" || $elem_LaxOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LaxOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOd_4" name="elem_LaxOd_4" value="4+" <?php echo ($elem_LaxOd_4 == "+4" || $elem_LaxOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LaxOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('Lax')">BL</td>
			<td align="left">Laxity</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOs_Absent" name="elem_LaxOs_Absent" value="Absent" <?php echo ($elem_LaxOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LaxOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOs_1" name="elem_LaxOs_1" value="1+" <?php echo ($elem_LaxOs_1 == "+1" || $elem_LaxOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LaxOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOs_2" name="elem_LaxOs_2" value="2+" <?php echo ($elem_LaxOs_2 == "+2" || $elem_LaxOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LaxOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOs_3" name="elem_LaxOs_3" value="3+" <?php echo ($elem_LaxOs_3 == "+3" || $elem_LaxOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LaxOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LaxOs_4" name="elem_LaxOs_4" value="4+" <?php echo ($elem_LaxOs_4 == "+4" || $elem_LaxOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LaxOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Laxity"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Laxity"]; }  ?>
			
			<tr id="d_LCTLax">
			<td align="left">LCT Laxity</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOd_Absent" name="elem_LCTLaxOd_Absent" value="Absent" <?php echo ($elem_LCTLaxOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOd_1" name="elem_LCTLaxOd_1" value="1+" <?php echo ($elem_LCTLaxOd_1 == "+1" || $elem_LCTLaxOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOd_2" name="elem_LCTLaxOd_2" value="2+" <?php echo ($elem_LCTLaxOd_2 == "+2" || $elem_LCTLaxOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOd_3" name="elem_LCTLaxOd_3" value="3+" <?php echo ($elem_LCTLaxOd_3 == "+3" || $elem_LCTLaxOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOd_4" name="elem_LCTLaxOd_4" value="4+" <?php echo ($elem_LCTLaxOd_4 == "+4" || $elem_LCTLaxOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LCTLax')">BL</td>
			<td align="left">LCT Laxity</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOs_Absent" name="elem_LCTLaxOs_Absent" value="Absent" <?php echo ($elem_LCTLaxOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOs_1" name="elem_LCTLaxOs_1" value="1+" <?php echo ($elem_LCTLaxOs_1 == "+1" || $elem_LCTLaxOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOs_2" name="elem_LCTLaxOs_2" value="2+" <?php echo ($elem_LCTLaxOs_2 == "+2" || $elem_LCTLaxOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOs_3" name="elem_LCTLaxOs_3" value="3+" <?php echo ($elem_LCTLaxOs_3 == "+3" || $elem_LCTLaxOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LCTLaxOs_4" name="elem_LCTLaxOs_4" value="4+" <?php echo ($elem_LCTLaxOs_4 == "+4" || $elem_LCTLaxOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LCTLaxOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/LCT Laxity"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/LCT Laxity"]; }  ?>
			
			<tr id="d_MCTLax">
			<td align="left">MCT Laxity</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOd_Absent" name="elem_MCTLaxOd_Absent" value="Absent" <?php echo ($elem_MCTLaxOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOd_1" name="elem_MCTLaxOd_1" value="1+" <?php echo ($elem_MCTLaxOd_1 == "+1" || $elem_MCTLaxOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOd_2" name="elem_MCTLaxOd_2" value="2+" <?php echo ($elem_MCTLaxOd_2 == "+2" || $elem_MCTLaxOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOd_3" name="elem_MCTLaxOd_3" value="3+" <?php echo ($elem_MCTLaxOd_3 == "+3" || $elem_MCTLaxOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOd_4" name="elem_MCTLaxOd_4" value="4+" <?php echo ($elem_MCTLaxOd_4 == "+4" || $elem_MCTLaxOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('MCTLax')">BL</td>
			<td align="left">MCT Laxity</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOs_Absent" name="elem_MCTLaxOs_Absent" value="Absent" <?php echo ($elem_MCTLaxOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOs_1" name="elem_MCTLaxOs_1" value="1+" <?php echo ($elem_MCTLaxOs_1 == "+1" || $elem_MCTLaxOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOs_2" name="elem_MCTLaxOs_2" value="2+" <?php echo ($elem_MCTLaxOs_2 == "+2" || $elem_MCTLaxOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOs_3" name="elem_MCTLaxOs_3" value="3+" <?php echo ($elem_MCTLaxOs_3 == "+3" || $elem_MCTLaxOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_MCTLaxOs_4" name="elem_MCTLaxOs_4" value="4+" <?php echo ($elem_MCTLaxOs_4 == "+4" || $elem_MCTLaxOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_MCTLaxOs_4">4+</label>
			</td>
			</tr>	
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/MCT Laxity"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/MCT Laxity"]; }  ?>
			
			<tr id="d_LLEctro">
			<td align="left">Ectropion</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOd_Absent" name="elem_LLEctroOd_Absent" value="Absent" <?php echo ($elem_LLEctroOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOd_1" name="elem_LLEctroOd_1" value="1+" <?php echo ($elem_LLEctroOd_1 == "+1" || $elem_LLEctroOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOd_2" name="elem_LLEctroOd_2" value="2+" <?php echo ($elem_LLEctroOd_2 == "+2" || $elem_LLEctroOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOd_3" name="elem_LLEctroOd_3" value="3+" <?php echo ($elem_LLEctroOd_3 == "+3" || $elem_LLEctroOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOd_4" name="elem_LLEctroOd_4" value="4+" <?php echo ($elem_LLEctroOd_4 == "+4" || $elem_LLEctroOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLEctro')">BL</td>
			<td align="left">Ectropion</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOs_Absent" name="elem_LLEctroOs_Absent" value="Absent" <?php echo ($elem_LLEctroOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOs_1" name="elem_LLEctroOs_1" value="1+" <?php echo ($elem_LLEctroOs_1 == "+1" || $elem_LLEctroOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOs_2" name="elem_LLEctroOs_2" value="2+" <?php echo ($elem_LLEctroOs_2 == "+2" || $elem_LLEctroOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOs_3" name="elem_LLEctroOs_3" value="3+" <?php echo ($elem_LLEctroOs_3 == "+3" || $elem_LLEctroOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEctroOs_4" name="elem_LLEctroOs_4" value="4+" <?php echo ($elem_LLEctroOs_4 == "+4" || $elem_LLEctroOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLEctroOs_4">4+</label>
			</td>
			</tr>	
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Ectropion"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Ectropion"]; }  ?>

			<tr id="d_LLEntro">
			<td align="left">Entropion</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOd_Absent" name="elem_LLEntroOd_Absent" value="Absent" <?php echo ($elem_LLEntroOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOd_1" name="elem_LLEntroOd_1" value="1+" <?php echo ($elem_LLEntroOd_1 == "+1" || $elem_LLEntroOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOd_2" name="elem_LLEntroOd_2" value="2+" <?php echo ($elem_LLEntroOd_2 == "+2" || $elem_LLEntroOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOd_3" name="elem_LLEntroOd_3" value="3+" <?php echo ($elem_LLEntroOd_3 == "+3" || $elem_LLEntroOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOd_4" name="elem_LLEntroOd_4" value="4+" <?php echo ($elem_LLEntroOd_4 == "+4" || $elem_LLEntroOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLEntro')">BL</td>
			<td align="left">Entropion</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOs_Absent" name="elem_LLEntroOs_Absent" value="Absent" <?php echo ($elem_LLEntroOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOs_1" name="elem_LLEntroOs_1" value="1+" <?php echo ($elem_LLEntroOs_1 == "+1" || $elem_LLEntroOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOs_2" name="elem_LLEntroOs_2" value="2+" <?php echo ($elem_LLEntroOs_2 == "+2" || $elem_LLEntroOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOs_3" name="elem_LLEntroOs_3" value="3+" <?php echo ($elem_LLEntroOs_3 == "+3" || $elem_LLEntroOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLEntroOs_4" name="elem_LLEntroOs_4" value="4+" <?php echo ($elem_LLEntroOs_4 == "+4" || $elem_LLEntroOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLEntroOs_4">4+</label>
			</td>
			</tr>	
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Entropion"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Entropion"]; }  ?>
			
			<tr id="d_CicaSC">
			<td align="left">Cicatricial Skin Changes</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOd_Absent" name="elem_CicaSCOd_Absent" value="Absent" <?php echo ($elem_CicaSCOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOd_1" name="elem_CicaSCOd_1" value="1+" <?php echo ($elem_CicaSCOd_1 == "+1" || $elem_CicaSCOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOd_2" name="elem_CicaSCOd_2" value="2+" <?php echo ($elem_CicaSCOd_2 == "+2" || $elem_CicaSCOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOd_3" name="elem_CicaSCOd_3" value="3+" <?php echo ($elem_CicaSCOd_3 == "+3" || $elem_CicaSCOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOd_4" name="elem_CicaSCOd_4" value="4+" <?php echo ($elem_CicaSCOd_4 == "+4" || $elem_CicaSCOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('CicaSC')">BL</td>
			<td align="left">Cicatricial Skin Changes</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOs_Absent" name="elem_CicaSCOs_Absent" value="Absent" <?php echo ($elem_CicaSCOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOs_1" name="elem_CicaSCOs_1" value="1+" <?php echo ($elem_CicaSCOs_1 == "+1" || $elem_CicaSCOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOs_2" name="elem_CicaSCOs_2" value="2+" <?php echo ($elem_CicaSCOs_2 == "+2" || $elem_CicaSCOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOs_3" name="elem_CicaSCOs_3" value="3+" <?php echo ($elem_CicaSCOs_3 == "+3" || $elem_CicaSCOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_CicaSCOs_4" name="elem_CicaSCOs_4" value="4+" <?php echo ($elem_CicaSCOs_4 == "+4" || $elem_CicaSCOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_CicaSCOs_4">4+</label>
			</td>
			</tr>	
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Cicatricial Skin Changes"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Cicatricial Skin Changes"]; }  ?>
			
			<tr id="d_ISS">
			<td align="left">Inf Scleral Show</td>
			<td>
			<select name="elem_ISSOd_sel" onchange="checkwnls()" class="form-control">
				<option value=""  ></option>
				<option value="-3" <?php echo ($elem_ISSOd_sel=="-3") ? "selected" : ""; ?> >-3</option>
				<option value="-2" <?php echo ($elem_ISSOd_sel=="-2") ? "selected" : ""; ?> >-2</option>
				<option value="-1" <?php echo ($elem_ISSOd_sel=="-3") ? "selected" : ""; ?> >-1</option>
				<option value="0"  <?php echo ($elem_ISSOd_sel=="0") ? "selected" : ""; ?> >0</option>	
				<option value="+1"  <?php echo ($elem_ISSOd_sel=="+1" || $elem_ISSOd_sel=="1+") ? "selected" : ""; ?> >+1</option>	
				<option value="+2"  <?php echo ($elem_ISSOd_sel=="+2" || $elem_ISSOd_sel=="2+") ? "selected" : ""; ?> >+2</option>	
				<option value="+3"  <?php echo ($elem_ISSOd_sel=="+3" || $elem_ISSOd_sel=="3+") ? "selected" : ""; ?> >+3</option>	
				<option value="+4"  <?php echo ($elem_ISSOd_sel=="+4" || $elem_ISSOd_sel=="4+") ? "selected" : ""; ?> >+4</option>	
				<option value="+5"  <?php echo ($elem_ISSOd_sel=="+5" || $elem_ISSOd_sel=="5+") ? "selected" : ""; ?> >+5</option>		
			</select>
			</td>
			<td>
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOd_1" name="elem_ISSOd_1" value="1+" <?php echo ($elem_ISSOd_1 == "+1" || $elem_ISSOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_ISSOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOd_2" name="elem_ISSOd_2" value="2+" <?php echo ($elem_ISSOd_2 == "+2" || $elem_ISSOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_ISSOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOd_3" name="elem_ISSOd_3" value="3+" <?php echo ($elem_ISSOd_3 == "+3" || $elem_ISSOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_ISSOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOd_4" name="elem_ISSOd_4" value="4+" <?php echo ($elem_ISSOd_4 == "+4" || $elem_ISSOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_ISSOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('ISS')">BL</td>
			<td align="left">Inf Scleral Show</td>
			<td>
			<select name="elem_ISSOs_sel" class="form-control" onchange="checkwnls()">
				<option value=""  ></option>
				<option value="-3" <?php echo ($elem_ISSOs_sel=="-3") ? "selected" : ""; ?> >-3</option>
				<option value="-2" <?php echo ($elem_ISSOs_sel=="-2") ? "selected" : ""; ?> >-2</option>
				<option value="-1" <?php echo ($elem_ISSOs_sel=="-3") ? "selected" : ""; ?> >-1</option>
				<option value="0"  <?php echo ($elem_ISSOs_sel=="0") ? "selected" : ""; ?> >0</option>	
				<option value="+1"  <?php echo ($elem_ISSOs_sel=="+1" || $elem_ISSOs_sel=="1+") ? "selected" : ""; ?> >+1</option>	
				<option value="+2"  <?php echo ($elem_ISSOs_sel=="+2" || $elem_ISSOs_sel=="2+") ? "selected" : ""; ?> >+2</option>	
				<option value="+3"  <?php echo ($elem_ISSOs_sel=="+3" || $elem_ISSOs_sel=="3+") ? "selected" : ""; ?> >+3</option>	
				<option value="+4"  <?php echo ($elem_ISSOs_sel=="+4" || $elem_ISSOs_sel=="4+") ? "selected" : ""; ?> >+4</option>	
				<option value="+5"  <?php echo ($elem_ISSOs_sel=="+5" || $elem_ISSOs_sel=="5+") ? "selected" : ""; ?> >+5</option>			
			</select>
			</td>
			<td>
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOs_1" name="elem_ISSOs_1" value="1+" <?php echo ($elem_ISSOs_1 == "+1" || $elem_ISSOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_ISSOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOs_2" name="elem_ISSOs_2" value="2+" <?php echo ($elem_ISSOs_2 == "+2" || $elem_ISSOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_ISSOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOs_3" name="elem_ISSOs_3" value="3+" <?php echo ($elem_ISSOs_3 == "+3" || $elem_ISSOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_ISSOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkwnls()" id="elem_ISSOs_4" name="elem_ISSOs_4" value="4+" <?php echo ($elem_ISSOs_4 == "+4" || $elem_ISSOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_ISSOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Inf Scleral Show"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Inf Scleral Show"]; }  ?>

			<tr id="d_LLLago">
			<td align="left">Lagophthalmos</td>
			<td colspan="8" class="form-inline">
				<div class="form-group">
				<input type="text"  onchange="checkwnls();" id="elem_LLLagoOd_txt" name="elem_LLLagoOd_txt" value="<?php echo ($elem_LLLagoOd_txt) ;?>" class="form-control">
				<label id="elem_LLLagoOd_txt">mm</label>
				</div>
			</td>	
			<td align="center" class="bilat" onClick="check_bl('LLLago')">BL</td>
			<td align="left">Lagophthalmos</td>
			<td colspan="8" class="form-inline">
				<div class="form-group">
				<input type="text"  onchange="checkwnls();" name="elem_LLLagoOs_txt" id="elem_LLLagoOs_txt" value="<?php echo ($elem_LLLagoOs_txt) ;?>" class="form-control">
				<label id="elem_LLLagoOs_txt">mm</label>
				</div>
			</td>						
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Lagophthalmos"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Lagophthalmos"]; }  ?>
			
			<tr id="d_LLPunEctro">
			<td align="left">Punctal Ectropion</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOd_Absent" name="elem_LLPunEctroOd_Absent" value="Absent" <?php echo ($elem_LLPunEctroOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOd_1" name="elem_LLPunEctroOd_1" value="1+" <?php echo ($elem_LLPunEctroOd_1 == "+1" || $elem_LLPunEctroOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOd_2" name="elem_LLPunEctroOd_2" value="2+" <?php echo ($elem_LLPunEctroOd_2 == "+2" || $elem_LLPunEctroOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOd_3" name="elem_LLPunEctroOd_3" value="3+" <?php echo ($elem_LLPunEctroOd_3 == "+3" || $elem_LLPunEctroOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOd_4" name="elem_LLPunEctroOd_4" value="4+" <?php echo ($elem_LLPunEctroOd_4 == "+4" || $elem_LLPunEctroOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLPunEctro')">BL</td>
			<td align="left">Punctal Ectropion</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOs_Absent" name="elem_LLPunEctroOs_Absent" value="Absent" <?php echo ($elem_LLPunEctroOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOs_1" name="elem_LLPunEctroOs_1" value="1+" <?php echo ($elem_LLPunEctroOs_1 == "+1" || $elem_LLPunEctroOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOs_2" name="elem_LLPunEctroOs_2" value="2+" <?php echo ($elem_LLPunEctroOs_2 == "+2" || $elem_LLPunEctroOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOs_3" name="elem_LLPunEctroOs_3" value="3+" <?php echo ($elem_LLPunEctroOs_3 == "+3" || $elem_LLPunEctroOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunEctroOs_4" name="elem_LLPunEctroOs_4" value="4+" <?php echo ($elem_LLPunEctroOs_4 == "+4" || $elem_LLPunEctroOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLPunEctroOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Punctal Ectropion"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Punctal Ectropion"]; }  ?>
			
			<tr id="d_LLPunSten">
			<td align="left">Punctal Stenosis</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOd_Absent" name="elem_LLPunStenOd_Absent" value="Absent" <?php echo ($elem_LLPunStenOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOd_1" name="elem_LLPunStenOd_1" value="1+" <?php echo ($elem_LLPunStenOd_1 == "+1" || $elem_LLPunStenOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOd_2" name="elem_LLPunStenOd_2" value="2+" <?php echo ($elem_LLPunStenOd_2 == "+2" || $elem_LLPunStenOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOd_3" name="elem_LLPunStenOd_3" value="3+" <?php echo ($elem_LLPunStenOd_3 == "+3" || $elem_LLPunStenOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOd_4" name="elem_LLPunStenOd_4" value="4+" <?php echo ($elem_LLPunStenOd_4 == "+4" || $elem_LLPunStenOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLPunSten')">BL</td>
			<td align="left">Punctal Stenosis</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOs_Absent" name="elem_LLPunStenOs_Absent" value="Absent" <?php echo ($elem_LLPunStenOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOs_1" name="elem_LLPunStenOs_1" value="1+" <?php echo ($elem_LLPunStenOs_1 == "+1" || $elem_LLPunStenOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOs_2" name="elem_LLPunStenOs_2" value="2+" <?php echo ($elem_LLPunStenOs_2 == "+2" || $elem_LLPunStenOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOs_3" name="elem_LLPunStenOs_3" value="3+" <?php echo ($elem_LLPunStenOs_3 == "+3" || $elem_LLPunStenOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLPunStenOs_4" name="elem_LLPunStenOs_4" value="4+" <?php echo ($elem_LLPunStenOs_4 == "+4" || $elem_LLPunStenOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLPunStenOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Punctal Stenosis"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Punctal Stenosis"]; }  ?>
			
			<tr id="d_LLMFP">
			<td align="left">Medial Fat Prolapse</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOd_Absent" name="elem_LLMFPOd_Absent" value="Absent" <?php echo ($elem_LLMFPOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOd_1" name="elem_LLMFPOd_1" value="1+" <?php echo ($elem_LLMFPOd_1 == "+1" || $elem_LLMFPOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOd_2" name="elem_LLMFPOd_2" value="2+" <?php echo ($elem_LLMFPOd_2 == "+2" || $elem_LLMFPOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOd_3" name="elem_LLMFPOd_3" value="3+" <?php echo ($elem_LLMFPOd_3 == "+3" || $elem_LLMFPOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOd_4" name="elem_LLMFPOd_4" value="4+" <?php echo ($elem_LLMFPOd_4 == "+4" || $elem_LLMFPOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLMFP')">BL</td>
			<td align="left">Medial Fat Prolapse</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOs_Absent" name="elem_LLMFPOs_Absent" value="Absent" <?php echo ($elem_LLMFPOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOs_1" name="elem_LLMFPOs_1" value="1+" <?php echo ($elem_LLMFPOs_1 == "+1" || $elem_LLMFPOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOs_2" name="elem_LLMFPOs_2" value="2+" <?php echo ($elem_LLMFPOs_2 == "+2" || $elem_LLMFPOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOs_3" name="elem_LLMFPOs_3" value="3+" <?php echo ($elem_LLMFPOs_3 == "+3" || $elem_LLMFPOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLMFPOs_4" name="elem_LLMFPOs_4" value="4+" <?php echo ($elem_LLMFPOs_4 == "+4" || $elem_LLMFPOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLMFPOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Medial Fat Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Medial Fat Prolapse"]; }  ?>
			
			<tr id="d_LLCFP">
			<td align="left">Central Fat Prolapse</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOd_Absent" name="elem_LLCFPOd_Absent" value="Absent" <?php echo ($elem_LLCFPOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOd_1" name="elem_LLCFPOd_1" value="1+" <?php echo ($elem_LLCFPOd_1 == "+1" || $elem_LLCFPOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOd_2" name="elem_LLCFPOd_2" value="2+" <?php echo ($elem_LLCFPOd_2 == "+2" || $elem_LLCFPOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOd_3" name="elem_LLCFPOd_3" value="3+" <?php echo ($elem_LLCFPOd_3 == "+3" || $elem_LLCFPOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOd_4" name="elem_LLCFPOd_4" value="4+" <?php echo ($elem_LLCFPOd_4 == "+4" || $elem_LLCFPOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLCFP')">BL</td>
			<td align="left">Central Fat Prolapse</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOs_Absent" name="elem_LLCFPOs_Absent" value="Absent" <?php echo ($elem_LLCFPOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOs_1" name="elem_LLCFPOs_1" value="1+" <?php echo ($elem_LLCFPOs_1 == "+1" || $elem_LLCFPOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOs_2" name="elem_LLCFPOs_2" value="2+" <?php echo ($elem_LLCFPOs_2 == "+2" || $elem_LLCFPOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOs_3" name="elem_LLCFPOs_3" value="3+" <?php echo ($elem_LLCFPOs_3 == "+3" || $elem_LLCFPOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLCFPOs_4" name="elem_LLCFPOs_4" value="4+" <?php echo ($elem_LLCFPOs_4 == "+4" || $elem_LLCFPOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLCFPOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Central Fat Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Central Fat Prolapse"]; }  ?>
			
			<tr id="d_LLLFP">
			<td align="left">Lateral Fat Prolapse</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOd_Absent" name="elem_LLLFPOd_Absent" value="Absent" <?php echo ($elem_LLLFPOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOd_1" name="elem_LLLFPOd_1" value="1+" <?php echo ($elem_LLLFPOd_1 == "+1" || $elem_LLLFPOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOd_2" name="elem_LLLFPOd_2" value="2+" <?php echo ($elem_LLLFPOd_2 == "+2" || $elem_LLLFPOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOd_3" name="elem_LLLFPOd_3" value="3+" <?php echo ($elem_LLLFPOd_3 == "+3" || $elem_LLLFPOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOd_4" name="elem_LLLFPOd_4" value="4+" <?php echo ($elem_LLLFPOd_4 == "+4" || $elem_LLLFPOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('LLLFP')">BL</td>
			<td align="left">Lateral Fat Prolapse</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOs_Absent" name="elem_LLLFPOs_Absent" value="Absent" <?php echo ($elem_LLLFPOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOs_1" name="elem_LLLFPOs_1" value="1+" <?php echo ($elem_LLLFPOs_1 == "+1" || $elem_LLLFPOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOs_2" name="elem_LLLFPOs_2" value="2+" <?php echo ($elem_LLLFPOs_2 == "+2" || $elem_LLLFPOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOs_3" name="elem_LLLFPOs_3" value="3+" <?php echo ($elem_LLLFPOs_3 == "+3" || $elem_LLLFPOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_LLLFPOs_4" name="elem_LLLFPOs_4" value="4+" <?php echo ($elem_LLLFPOs_4 == "+4" || $elem_LLLFPOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LLLFPOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Lateral Fat Prolapse"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Lateral Fat Prolapse"]; }  ?>
			
			<tr id="d_TearTr">
			<td align="left">Tear Trough</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOd_Absent" name="elem_TearTrOd_Absent" value="Absent" <?php echo ($elem_TearTrOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_TearTrOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOd_1" name="elem_TearTrOd_1" value="1+" <?php echo ($elem_TearTrOd_1 == "+1" || $elem_TearTrOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOd_2" name="elem_TearTrOd_2" value="2+" <?php echo ($elem_TearTrOd_2 == "+2" || $elem_TearTrOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOd_3" name="elem_TearTrOd_3" value="3+" <?php echo ($elem_TearTrOd_3 == "+3" || $elem_TearTrOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOd_4" name="elem_TearTrOd_4" value="4+" <?php echo ($elem_TearTrOd_4 == "+4" || $elem_TearTrOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('TearTr')">BL</td>
			<td align="left">Tear Trough</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOs_Absent" name="elem_TearTrOs_Absent" value="Absent" <?php echo ($elem_TearTrOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_TearTrOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOs_1" name="elem_TearTrOs_1" value="1+" <?php echo ($elem_TearTrOs_1 == "+1" || $elem_TearTrOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOs_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOs_2" name="elem_TearTrOs_2" value="2+" <?php echo ($elem_TearTrOs_2 == "+2" || $elem_TearTrOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOs_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOs_3" name="elem_TearTrOs_3" value="3+" <?php echo ($elem_TearTrOs_3 == "+3" || $elem_TearTrOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_TearTrOs_4" name="elem_TearTrOs_4" value="4+" <?php echo ($elem_TearTrOs_4 == "+4" || $elem_TearTrOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_TearTrOs_4">4+</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Tear Trough"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Tear Trough"]; }  ?>
			
			<tr id="d_Naso">
			<td align="left">Nasojugal Fold</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOd_Absent" name="elem_NasoOd_Absent" value="Absent" <?php echo ($elem_NasoOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_NasoOd_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOd_1" name="elem_NasoOd_1" value="1+" <?php echo ($elem_NasoOd_1 == "+1" || $elem_NasoOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_NasoOd_1">1+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOd_2" name="elem_NasoOd_2" value="2+" <?php echo ($elem_NasoOd_2 == "+2" || $elem_NasoOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_NasoOd_2">2+</label>
			</td>
			<td>					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOd_3" name="elem_NasoOd_3" value="3+" <?php echo ($elem_NasoOd_3 == "+3" || $elem_NasoOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_NasoOd_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOd_4" name="elem_NasoOd_4" value="4+" <?php echo ($elem_NasoOd_4 == "+4" || $elem_NasoOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_NasoOd_4">4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('Naso')">BL</td>
			<td align="left">Nasojugal Fold</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOs_Absent" name="elem_NasoOs_Absent" value="Absent" <?php echo ($elem_NasoOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_NasoOs_Absent">Absent</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOs_1" name="elem_NasoOs_1" value="1+" <?php echo ($elem_NasoOs_1 == "+1" || $elem_NasoOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_NasoOs_1">1+</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOs_2" name="elem_NasoOs_2" value="2+" <?php echo ($elem_NasoOs_2 == "+2" || $elem_NasoOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_NasoOs_2">2+</label>
			</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOs_3" name="elem_NasoOs_3" value="3+" <?php echo ($elem_NasoOs_3 == "+3" || $elem_NasoOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_NasoOs_3">3+</label>
			</td>
			<td colspan="4">					
			<input type="checkbox"  onclick="checkAbsent(this);" id="elem_NasoOs_4" name="elem_NasoOs_4" value="4+" <?php echo ($elem_NasoOs_4 == "+4" || $elem_NasoOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_NasoOs_4">4+</label>
			</td>
			</tr>	
			<?php if(isset($arr_exm_ext_htm["Lids"]["Lower Lids/Nasojugal Fold"])){ echo $arr_exm_ext_htm["Lids"]["Lower Lids/Nasojugal Fold"]; }  ?>	

			<tr id="d_adcmnt_lids_lwr">
			<td align="left"><strong>Comments</strong></td>
			<td colspan="8" align="left" id="d_adcmnt_lids_lwr_od"><textarea onBlur="checkwnls();" id="lidsAdCmntLwrOd" name="lidsAdCmntLwrOd"  rows="3" class="form-control"><?php echo ($lidsAdCmntLwrOd);?></textarea></td>
			<td align="center" class="bilat" onClick="check_bl('adcmnt_lids_lwr')">BL</td>
			<td align="left"><strong>Comments</strong></td>
			<td colspan="8" align="left" id="d_adcmnt_lids_lwr_os"><textarea onBlur="checkwnls();" id="lidsAdCmntLwrOs" name="lidsAdCmntLwrOs" rows="3" class="form-control"><?php echo ($lidsAdCmntLwrOs);?></textarea>   </td>
			</tr>

		</table>
		</div>
		
	</div>
	</div>
	
	
	<div class="clearfix"> </div>
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
File: lacrimal_advance_inc.php
Purpose: This provides Plastic function in LA->lacrimal
Access Type : Include file
*/
?>
<?php if($flg_showPlastic=="1"){ //Check Template ?>
<!-- Lacrimal System -Advance -->
<div class="advanchead" onclick="placs_showAdvance(this)"><strong>Advance</strong></div>
<div class="clearfix"> </div>	
<div id="div8" class="table-responsive">
	<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="9" align="center"><!--<button class="upperlid" type="submit">Upper Lid</button> <button class="lowerlid" type="submit">Lower Lid</button>--></td>
		<td align="center" class="bilat bilat_all" onClick="check_bilateral('lacri_advc')"><strong>Bilateral</strong></td>
		<td colspan="9" align="center">&nbsp;</td>
		</tr>
		
		<tr id="d_TearMen">
		<td align="left">Tear Meniscus</td>
		<td colspan="2">		
		<input  type="checkbox"  onclick="checkwnls()" id="elem_TearMenOd_Normal" name="elem_TearMenOd_Normal" value="Normal" <?php echo ($elem_TearMenOd_Normal == "Normal") ? "checked=\"checked\"" : "";?>><label for="elem_TearMenOd_Normal" >Normal</label>
		</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkwnls()" id="elem_TearMenOd_Decreased" name="elem_TearMenOd_Decreased" value="Decreased" <?php echo ($elem_TearMenOd_Decreased == "Decreased") ? "checked=\"checked\"" : "";?>><label for="elem_TearMenOd_Decreased" >Decreased</label>
		</td>
		<td colspan="4">
		<input  type="checkbox"  onclick="checkwnls()" id="elem_TearMenOd_Increased" name="elem_TearMenOd_Increased" value="Increased" <?php echo ($elem_TearMenOd_Increased == "Increased") ? "checked=\"checked\"" : "";?>><label for="elem_TearMenOd_Increased" >Increased</label>
		</td>			
		<td align="center" class="bilat" onClick="check_bl('TearMen')">BL</td>
		<td align="left">Tear Meniscus</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkwnls()" id="elem_TearMenOs_Normal" name="elem_TearMenOs_Normal" value="Normal" <?php echo ($elem_TearMenOs_Normal == "Normal") ? "checked=\"checked\"" : "";?>><label for="elem_TearMenOs_Normal" >Normal</label>
		</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkwnls()" id="elem_TearMenOs_Decreased" name="elem_TearMenOs_Decreased" value="Decreased" <?php echo ($elem_TearMenOs_Decreased == "Decreased") ? "checked=\"checked\"" : "";?>><label for="elem_TearMenOs_Decreased" >Decreased</label>
		</td>
		<td colspan="4">
		<input  type="checkbox"  onclick="checkwnls()" id="elem_TearMenOs_Increased" name="elem_TearMenOs_Increased" value="Increased" <?php echo ($elem_TearMenOs_Increased == "Increased") ? "checked=\"checked\"" : "";?>><label for="elem_TearMenOs_Increased" >Increased</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Tear Meniscus"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Tear Meniscus"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_LacUpper <?php echo $cls_LacUpper; ?>" id="d_LacUpper">
		<td align="left" class="grpbtn" onclick="openSubGrp('LacUpper')">			
			<label >Upper Puncta
			<span class="glyphicon <?php echo $arow_LacUpper; ?>"></span></label> 
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpperOd_Absent" name="elem_LacUpperOd_Absent" value="Absent" <?php echo ($elem_LacUpperOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacUpperOd_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpperOd_Present" name="elem_LacUpperOd_Present" value="Present" <?php echo ($elem_LacUpperOd_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacUpperOd_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>	
		<td></td>		
		<td align="center" class="bilat" onClick="check_bl('LacUpper')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('LacUpper')">
			<label >Upper Puncta
			<span class="glyphicon <?php echo $arow_LacUpper; ?>"></span></label> 
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpperOs_Absent" name="elem_LacUpperOs_Absent" value="Absent" <?php echo ($elem_LacUpperOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacUpperOs_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpperOs_Present" name="elem_LacUpperOs_Present" value="Present" <?php echo ($elem_LacUpperOs_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacUpperOs_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>	
		</tr>
		
		
		<tr class="exmhlgcol grp_LacUpper <?php echo $cls_LacUpper; ?>" >
		<td align="left">Size</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_SizeOd_Small" name="elem_LacUpper_SizeOd_Small" value="Small" <?php echo ($elem_LacUpper_SizeOd_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_SizeOd_Small" >Small</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_SizeOd_Med" name="elem_LacUpper_SizeOd_Med" value="Medium" <?php echo ($elem_LacUpper_SizeOd_Med == "Medium") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_SizeOd_Med" >Medium</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_SizeOd_Large" name="elem_LacUpper_SizeOd_Large" value="Large" <?php echo ($elem_LacUpper_SizeOd_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_SizeOd_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>	
		<td align="center" class="bilat" onClick="check_bl('LacUpper')" >BL</td>
		<td align="left">Size</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_SizeOs_Small" name="elem_LacUpper_SizeOs_Small" value="Small" <?php echo ($elem_LacUpper_SizeOs_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_SizeOs_Small" >Small</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_SizeOs_Med" name="elem_LacUpper_SizeOs_Med" value="Medium" <?php echo ($elem_LacUpper_SizeOs_Med == "Medium") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_SizeOs_Med" >Medium</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_SizeOs_Large" name="elem_LacUpper_SizeOs_Large" value="Large" <?php echo ($elem_LacUpper_SizeOs_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_SizeOs_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>	
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta/Size"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta/Size"]; }  ?>
		
		<tr class="exmhlgcol grp_LacUpper <?php echo $cls_LacUpper; ?>" >
		<td align="left">Stenosis</td>
		<td>		
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOd_Absent" name="elem_LacUpper_StenOd_Absent" value="Absent" <?php echo ($elem_LacUpper_StenOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOd_Absent" >Absent</label>
		</td><td>		
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOd_1" name="elem_LacUpper_StenOd_1" value="1+" <?php echo ($elem_LacUpper_StenOd_1 == "+1" || $elem_LacUpper_StenOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOd_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOd_2" name="elem_LacUpper_StenOd_2" value="2+" <?php echo ($elem_LacUpper_StenOd_2 == "+2" || $elem_LacUpper_StenOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOd_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOd_3" name="elem_LacUpper_StenOd_3" value="3+" <?php echo ($elem_LacUpper_StenOd_3 == "+3" || $elem_LacUpper_StenOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOd_3" >3+</label>
		</td><td colspan="4">
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOd_4" name="elem_LacUpper_StenOd_4" value="4+" <?php echo ($elem_LacUpper_StenOd_4 == "+4" || $elem_LacUpper_StenOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOd_4" >4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('LacUpper')" >BL</td>
		<td align="left">Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOs_Absent" name="elem_LacUpper_StenOs_Absent" value="Absent" <?php echo ($elem_LacUpper_StenOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOs_1" name="elem_LacUpper_StenOs_1" value="1+" <?php echo ($elem_LacUpper_StenOs_1 == "+1" || $elem_LacUpper_StenOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOs_2" name="elem_LacUpper_StenOs_2" value="2+" <?php echo ($elem_LacUpper_StenOs_2 == "+2" || $elem_LacUpper_StenOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOs_3" name="elem_LacUpper_StenOs_3" value="3+" <?php echo ($elem_LacUpper_StenOs_3 == "+3" || $elem_LacUpper_StenOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOs_3" >3+</label>
		</td><td colspan="4">
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_StenOs_4" name="elem_LacUpper_StenOs_4" value="4+" <?php echo ($elem_LacUpper_StenOs_4 == "+4" || $elem_LacUpper_StenOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_StenOs_4" >4+</label>
		</td>
		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta/Stenosis"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta/Stenosis"]; }  ?>
		
		<tr class="exmhlgcol grp_LacUpper <?php echo $cls_LacUpper; ?>" >
		<td align="left">Obstruction</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_ObstOd_Absent" name="elem_LacUpper_ObstOd_Absent" value="Absent" <?php echo ($elem_LacUpper_ObstOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_ObstOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_ObstOd_Present" name="elem_LacUpper_ObstOd_Present" value="Present" <?php echo ($elem_LacUpper_ObstOd_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_ObstOd_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacUpper')" >BL</td>
		<td align="left">Obstruction</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_ObstOs_Absent" name="elem_LacUpper_ObstOs_Absent" value="Absent" <?php echo ($elem_LacUpper_ObstOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_ObstOs_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacUpper');" id="elem_LacUpper_ObstOs_Present" name="elem_LacUpper_ObstOs_Present" value="Present" <?php echo ($elem_LacUpper_ObstOs_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacUpper_ObstOs_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta/Obstruction"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta/Obstruction"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Upper Puncta"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_LacLower <?php echo $cls_LacLower; ?>" id="d_LacLower">
		<td align="left" class="grpbtn" onclick="openSubGrp('LacLower')">			
			<label >Lower Puncta
			<span class="glyphicon <?php echo $arow_LacLower; ?>"></span></label> 
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLowerOd_Absent" name="elem_LacLowerOd_Absent" value="Absent" <?php echo ($elem_LacLowerOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacLowerOd_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLowerOd_Present" name="elem_LacLowerOd_Present" value="Present" <?php echo ($elem_LacLowerOd_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacLowerOd_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacLower')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('LacLower')">
			<label >Lower Puncta
			<span class="glyphicon <?php echo $arow_LacLower; ?>"></span></label> 
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLowerOs_Absent" name="elem_LacLowerOs_Absent" value="Absent" <?php echo ($elem_LacLowerOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacLowerOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLowerOs_Present" name="elem_LacLowerOs_Present" value="Present" <?php echo ($elem_LacLowerOs_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacLowerOs_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		
		
		<tr class="exmhlgcol grp_LacLower <?php echo $cls_LacLower; ?>" >
		<td align="left">Size</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_SizeOd_Small" name="elem_LacLower_SizeOd_Small" value="Small" <?php echo ($elem_LacLower_SizeOd_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_LacLower_SizeOd_Small" >Small</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_SizeOd_Med" name="elem_LacLower_SizeOd_Med" value="Medium" <?php echo ($elem_LacLower_SizeOd_Med == "Medium") ? "checked=checked" : "" ;?>><label for="elem_LacLower_SizeOd_Med" >Medium</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_SizeOd_Large" name="elem_LacLower_SizeOd_Large" value="Large" <?php echo ($elem_LacLower_SizeOd_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_LacLower_SizeOd_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacLower')" >BL</td>
		<td align="left">Size</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_SizeOs_Small" name="elem_LacLower_SizeOs_Small" value="Small" <?php echo ($elem_LacLower_SizeOs_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_LacLower_SizeOs_Small" >Small</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_SizeOs_Med" name="elem_LacLower_SizeOs_Med" value="Medium" <?php echo ($elem_LacLower_SizeOs_Med == "Medium") ? "checked=checked" : "" ;?>><label for="elem_LacLower_SizeOs_Med" >Medium</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_SizeOs_Large" name="elem_LacLower_SizeOs_Large" value="Large" <?php echo ($elem_LacLower_SizeOs_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_LacLower_SizeOs_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta/Size"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta/Size"]; }  ?>
		
		
		<tr class="exmhlgcol grp_LacLower <?php echo $cls_LacLower; ?>" >
		<td align="left">Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOd_Absent" name="elem_LacLower_StenOd_Absent" value="Absent" <?php echo ($elem_LacLower_StenOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOd_1" name="elem_LacLower_StenOd_1" value="1+" <?php echo ($elem_LacLower_StenOd_1 == "+1" || $elem_LacLower_StenOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOd_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOd_2" name="elem_LacLower_StenOd_2" value="2+" <?php echo ($elem_LacLower_StenOd_2 == "+2" || $elem_LacLower_StenOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOd_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOd_3" name="elem_LacLower_StenOd_3" value="3+" <?php echo ($elem_LacLower_StenOd_3 == "+3" || $elem_LacLower_StenOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOd_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOd_4" name="elem_LacLower_StenOd_4" value="4+" <?php echo ($elem_LacLower_StenOd_4 == "+4" || $elem_LacLower_StenOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOd_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacLower')" >BL</td>
		<td align="left">Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOs_Absent" name="elem_LacLower_StenOs_Absent" value="Absent" <?php echo ($elem_LacLower_StenOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOs_1" name="elem_LacLower_StenOs_1" value="1+" <?php echo ($elem_LacLower_StenOs_1 == "+1" || $elem_LacLower_StenOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOs_2" name="elem_LacLower_StenOs_2" value="2+" <?php echo ($elem_LacLower_StenOs_2 == "+2" || $elem_LacLower_StenOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOs_3" name="elem_LacLower_StenOs_3" value="3+" <?php echo ($elem_LacLower_StenOs_3 == "+3" || $elem_LacLower_StenOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOs_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_StenOs_4" name="elem_LacLower_StenOs_4" value="4+" <?php echo ($elem_LacLower_StenOs_4 == "+4" || $elem_LacLower_StenOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LacLower_StenOs_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta/Stenosis"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta/Stenosis"]; }  ?>
		
		<tr class="exmhlgcol grp_LacLower <?php echo $cls_LacLower; ?>" >
		<td align="left">Obstruction</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_ObstOd_Absent" name="elem_LacLower_ObstOd_Absent" value="Absent" <?php echo ($elem_LacLower_ObstOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacLower_ObstOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_ObstOd_Present" name="elem_LacLower_ObstOd_Present" value="Present" <?php echo ($elem_LacLower_ObstOd_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacLower_ObstOd_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacLower')" >BL</td>
		<td align="left">Obstruction</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_ObstOs_Absent" name="elem_LacLower_ObstOs_Absent" value="Absent" <?php echo ($elem_LacLower_ObstOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LacLower_ObstOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacLower');" id="elem_LacLower_ObstOs_Present" name="elem_LacLower_ObstOs_Present" value="Present" <?php echo ($elem_LacLower_ObstOs_Present == "Present") ? "checked=checked" : "" ;?>><label for="elem_LacLower_ObstOs_Present" >Present</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta/Obstruction"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta/Obstruction"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lower Puncta"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_LacProb <?php echo $cls_LacProb; ?>" id="d_LacProb">
		<td align="left" class="grpbtn" onclick="openSubGrp('LacProb')">			
			<label >Lacrimal Probing
			<span class="glyphicon <?php echo $arow_LacProb; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'LacProb');"  name="elem_LacProbOd_text" class="form-control" ><?php echo ($elem_LacProbOd_text);?></textarea></td>
				
		<td align="center" class="bilat" onClick="check_bl('LacProb')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('LacProb')">
			<label >Lacrimal Probing
			<span class="glyphicon <?php echo $arow_LacProb; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'LacProb');"  name="elem_LacProbOs_text" class="form-control" ><?php echo ($elem_LacProbOs_text);?></textarea></td>		
		</tr>
		
		
		<tr class="exmhlgcol grp_LacProb <?php echo $cls_LacProb; ?>" >
		<td align="left">Canalicular Stenosis</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacProb')" >BL</td>
		<td align="left">Canalicular Stenosis</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis"]; }  ?>
		
		<tr class="exmhlgcol grp_LacProb <?php echo $cls_LacProb; ?>" >
		<td align="left">Upper</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOd_Absent" name="elem_UpCanStOd_Absent" value="Absent" <?php echo ($elem_UpCanStOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOd_1" name="elem_UpCanStOd_1" value="1+" <?php echo ($elem_UpCanStOd_1 == "+1" || $elem_UpCanStOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOd_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOd_2" name="elem_UpCanStOd_2" value="2+" <?php echo ($elem_UpCanStOd_2 == "+2" || $elem_UpCanStOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOd_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOd_3" name="elem_UpCanStOd_3" value="3+" <?php echo ($elem_UpCanStOd_3 == "+3" || $elem_UpCanStOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOd_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOd_4" name="elem_UpCanStOd_4" value="4+" <?php echo ($elem_UpCanStOd_4 == "+4" || $elem_UpCanStOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOd_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacProb')" >BL</td>
		<td align="left">Upper</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOs_Absent" name="elem_UpCanStOs_Absent" value="Absent" <?php echo ($elem_UpCanStOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOs_1" name="elem_UpCanStOs_1" value="1+" <?php echo ($elem_UpCanStOs_1 == "+1" || $elem_UpCanStOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOs_2" name="elem_UpCanStOs_2" value="2+" <?php echo ($elem_UpCanStOs_2 == "+2" || $elem_UpCanStOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOs_3" name="elem_UpCanStOs_3" value="3+" <?php echo ($elem_UpCanStOs_3 == "+3" || $elem_UpCanStOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOs_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_UpCanStOs_4" name="elem_UpCanStOs_4" value="4+" <?php echo ($elem_UpCanStOs_4 == "+4" || $elem_UpCanStOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_UpCanStOs_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Upper"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Upper"]; }?>
		
		<tr class="exmhlgcol grp_LacProb <?php echo $cls_LacProb; ?>" >
		<td align="left">Lower</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOd_Absent" name="elem_LwrCanStOd_Absent" value="Absent" <?php echo ($elem_LwrCanStOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOd_1" name="elem_LwrCanStOd_1" value="1+" <?php echo ($elem_LwrCanStOd_1 == "+1" || $elem_LwrCanStOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOd_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOd_2" name="elem_LwrCanStOd_2" value="2+" <?php echo ($elem_LwrCanStOd_2 == "+2" || $elem_LwrCanStOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOd_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOd_3" name="elem_LwrCanStOd_3" value="3+" <?php echo ($elem_LwrCanStOd_3 == "+3" || $elem_LwrCanStOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOd_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOd_4" name="elem_LwrCanStOd_4" value="4+" <?php echo ($elem_LwrCanStOd_4 == "+4" || $elem_LwrCanStOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOd_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacProb')" >BL</td>
		<td align="left">Lower</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOs_Absent" name="elem_LwrCanStOs_Absent" value="Absent" <?php echo ($elem_LwrCanStOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOs_1" name="elem_LwrCanStOs_1" value="1+" <?php echo ($elem_LwrCanStOs_1 == "+1" || $elem_LwrCanStOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOs_2" name="elem_LwrCanStOs_2" value="2+" <?php echo ($elem_LwrCanStOs_2 == "+2" || $elem_LwrCanStOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOs_3" name="elem_LwrCanStOs_3" value="3+" <?php echo ($elem_LwrCanStOs_3 == "+3" || $elem_LwrCanStOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOs_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_LwrCanStOs_4" name="elem_LwrCanStOs_4" value="4+" <?php echo ($elem_LwrCanStOs_4 == "+4" || $elem_LwrCanStOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_LwrCanStOs_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Lower"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Lower"]; }?>
		
		<tr class="exmhlgcol grp_LacProb <?php echo $cls_LacProb; ?>" >
		<td align="left">Common</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOd_Absent" name="elem_CmnCanStOd_Absent" value="Absent" <?php echo ($elem_CmnCanStOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOd_1" name="elem_CmnCanStOd_1" value="1+" <?php echo ($elem_CmnCanStOd_1 == "+1" || $elem_CmnCanStOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOd_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOd_2" name="elem_CmnCanStOd_2" value="2+" <?php echo ($elem_CmnCanStOd_2 == "+2" || $elem_CmnCanStOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOd_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOd_3" name="elem_CmnCanStOd_3" value="3+" <?php echo ($elem_CmnCanStOd_3 == "+3" || $elem_CmnCanStOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOd_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOd_4" name="elem_CmnCanStOd_4" value="4+" <?php echo ($elem_CmnCanStOd_4 == "+4" || $elem_CmnCanStOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOd_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('LacProb')" >BL</td>
		<td align="left">Common</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOs_Absent" name="elem_CmnCanStOs_Absent" value="Absent" <?php echo ($elem_CmnCanStOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOs_1" name="elem_CmnCanStOs_1" value="1+" <?php echo ($elem_CmnCanStOs_1 == "+1" || $elem_CmnCanStOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOs_2" name="elem_CmnCanStOs_2" value="2+" <?php echo ($elem_CmnCanStOs_2 == "+2" || $elem_CmnCanStOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOs_3" name="elem_CmnCanStOs_3" value="3+" <?php echo ($elem_CmnCanStOs_3 == "+3" || $elem_CmnCanStOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOs_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_CmnCanStOs_4" name="elem_CmnCanStOs_4" value="4+" <?php echo ($elem_CmnCanStOs_4 == "+4" || $elem_CmnCanStOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_CmnCanStOs_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Common"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Common"]; }?>
		
		<tr class="exmhlgcol grp_LacProb <?php echo $cls_LacProb; ?>" id="LacProb">
		<td align="left">Duct Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOd_Absent" name="elem_DuctStOd_Absent" value="Absent" <?php echo ($elem_DuctStOd_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_DuctStOd_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOd_1" name="elem_DuctStOd_1" value="1+" <?php echo ($elem_DuctStOd_1 == "+1" || $elem_DuctStOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOd_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOd_2" name="elem_DuctStOd_2" value="2+" <?php echo ($elem_DuctStOd_2 == "+2" || $elem_DuctStOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOd_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOd_3" name="elem_DuctStOd_3" value="3+" <?php echo ($elem_DuctStOd_3 == "+3" || $elem_DuctStOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOd_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOd_4" name="elem_DuctStOd_4" value="4+" <?php echo ($elem_DuctStOd_4 == "+4" || $elem_DuctStOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOd_4" >4+</label>
		</td><td colspan="3">
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOd_Obstructed" name="elem_DuctStOd_Obstructed" value="Obstructed" <?php echo ($elem_DuctStOd_Obstructed == "Obstructed") ? "checked=checked" : "" ;?>><label for="elem_DuctStOd_Obstructed" >Obstructed</label>
		</td>			
		<td align="center" class="bilat" onClick="check_bl('LacProb')" >BL</td>
		<td align="left">Duct Stenosis</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOs_Absent" name="elem_DuctStOs_Absent" value="Absent" <?php echo ($elem_DuctStOs_absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_DuctStOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOs_1" name="elem_DuctStOs_1" value="1+" <?php echo ($elem_DuctStOs_1 == "+1" || $elem_DuctStOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOs_2" name="elem_DuctStOs_2" value="2+" <?php echo ($elem_DuctStOs_2 == "+2" || $elem_DuctStOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOs_3" name="elem_DuctStOs_3" value="3+" <?php echo ($elem_DuctStOs_3 == "+3" || $elem_DuctStOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOs_3" >3+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOs_4" name="elem_DuctStOs_4" value="4+" <?php echo ($elem_DuctStOs_4 == "+4" || $elem_DuctStOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_DuctStOs_4" >4+</label>
		</td><td colspan="3">
		<input type="checkbox"  onclick="checkAbsent(this,'LacProb');" id="elem_DuctStOs_Obstructed" name="elem_DuctStOs_Obstructed" value="Obstructed" <?php echo ($elem_DuctStOs_Obstructed == "Obstructed") ? "checked=checked" : "" ;?>><label for="elem_DuctStOs_Obstructed" >Obstructed</label>
		</td>
		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Duct Stenosis"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing/Canalicular Stenosis/Duct Stenosis"]; }?>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Lacrimal Probing"]; }?>
		
		<tr class="exmhlgcol grp_handle grp_NslExm <?php echo $cls_NslExm; ?>" id="d_NslExm">
		<td align="left" class="grpbtn" onclick="openSubGrp('NslExm')">		
		<label >Nasal Exam 
		<span class="glyphicon <?php echo $arow_NslExm; ?>"></span></label>
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'NslExm');"  name="elem_NslExmOd_text" class="form-control" ><?php echo ($elem_NslExmOd_text);?></textarea></td>			
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('NslExm')">
		<label >Nasal Exam
		<span class="glyphicon <?php echo $arow_NslExm; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'NslExm');"  name="elem_NslExmOs_text" class="form-control" ><?php echo ($elem_NslExmOs_text);?></textarea></td>		
		</tr>
		
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Nasal Endoscopy</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_NslEndDnOd_Y" name="elem_NslEndDnOd_Y" value="Nasal Endoscopy" <?php echo ($elem_NslEndDnOd_Y == "Nasal Endoscopy") ? "checked=checked" : "" ;?>><label for="elem_NslEndDnOd_Y" >Yes</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_NslEndDnOd_N" name="elem_NslEndDnOd_N" value="No Nasal Endoscopy" <?php echo ($elem_NslEndDnOd_N == "No Nasal Endoscopy") ? "checked=checked" : "" ;?>><label for="elem_NslEndDnOd_N" >No</label>				
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Nasal Endoscopy</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_NslEndDnOs_Y" name="elem_NslEndDnOs_Y" value="Nasal Endoscopy" <?php echo ($elem_NslEndDnOs_Y == "Nasal Endoscopy") ? "checked=checked" : "" ;?>><label for="elem_NslEndDnOs_Y" >Yes</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_NslEndDnOs_N" name="elem_NslEndDnOs_N" value="No Nasal Endoscopy" <?php echo ($elem_NslEndDnOs_N == "No Nasal Endoscopy") ? "checked=checked" : "" ;?>><label for="elem_NslEndDnOs_N" >No</label>				
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Nasal Endoscopy"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Nasal Endoscopy"]; }?>
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Septum</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_SeptumOd_Normal" name="elem_SeptumOd_Normal" value="Normal" <?php echo ($elem_SeptumOd_Normal == "Normal") ? "checked=checked" : "" ;?>><label for="elem_SeptumOd_Normal" >Normal</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_SeptumOd_Thickened" name="elem_SeptumOd_Thickened" value="Thickened" <?php echo ($elem_SeptumOd_Thickened == "Thickened") ? "checked=checked" : "" ;?>><label for="elem_SeptumOd_Thickened" >Thickened</label>				
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Septum</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_SeptumOs_Normal" name="elem_SeptumOs_Normal" value="Normal" <?php echo ($elem_SeptumOs_Normal == "Normal") ? "checked=checked" : "" ;?>><label for="elem_SeptumOs_Normal" >Normal</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_SeptumOs_Thickened" name="elem_SeptumOs_Thickened" value="Thickened" <?php echo ($elem_SeptumOs_Thickened == "Thickened") ? "checked=checked" : "" ;?>><label for="elem_SeptumOs_Thickened" >Thickened</label>				
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Septum"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Septum"]; }?>
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Deviated Septum</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_Absent" name="elem_DevSeptumOd_Absent" value="Absent" <?php echo ($elem_DevSeptumOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_Right" name="elem_DevSeptumOd_Right" value="Right" <?php echo ($elem_DevSeptumOd_Right == "Right") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_Right" >Right</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_Left" name="elem_DevSeptumOd_Left" value="Left" <?php echo ($elem_DevSeptumOd_Left == "Left") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_Left" >Left</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_1" name="elem_DevSeptumOd_1" value="1+" <?php echo ($elem_DevSeptumOd_1 == "+1" || $elem_DevSeptumOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_1" >1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_2" name="elem_DevSeptumOd_2" value="2+" <?php echo ($elem_DevSeptumOd_2 == "+2" || $elem_DevSeptumOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_2" >2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_3" name="elem_DevSeptumOd_3" value="3+" <?php echo ($elem_DevSeptumOd_3 == "+3" || $elem_DevSeptumOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_3" >3+</label>
		</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOd_4" name="elem_DevSeptumOd_4" value="4+" <?php echo ($elem_DevSeptumOd_4 == "+4" || $elem_DevSeptumOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOd_4" >4+</label>
		</td>	
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Deviated Septum</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_Absent" name="elem_DevSeptumOs_Absent" value="Absent" <?php echo ($elem_DevSeptumOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_Absent" >Absent</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_Right" name="elem_DevSeptumOs_Right" value="Right" <?php echo ($elem_DevSeptumOs_Right == "Right") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_Right" >Right</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_Left" name="elem_DevSeptumOs_Left" value="Left" <?php echo ($elem_DevSeptumOs_Left == "Left") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_Left" >Left</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_1" name="elem_DevSeptumOs_1" value="1+" <?php echo ($elem_DevSeptumOs_1 == "+1" || $elem_DevSeptumOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_1" >1+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_2" name="elem_DevSeptumOs_2" value="2+" <?php echo ($elem_DevSeptumOs_2 == "+2" || $elem_DevSeptumOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_2" >2+</label>
		</td><td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_3" name="elem_DevSeptumOs_3" value="3+" <?php echo ($elem_DevSeptumOs_3 == "+3" || $elem_DevSeptumOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_3" >3+</label>
		</td><td colspan="2">
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_DevSeptumOs_4" name="elem_DevSeptumOs_4" value="4+" <?php echo ($elem_DevSeptumOs_4 == "+4" || $elem_DevSeptumOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_DevSeptumOs_4" >4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Deviated Septum"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Deviated Septum"]; }?>
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Middle Turbinate</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_MidTurSizeOd_Small" name="elem_MidTurSizeOd_Small" value="Small" <?php echo ($elem_MidTurSizeOd_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_MidTurSizeOd_Small" >Small</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_MidTurSizeOd_Medium" name="elem_MidTurSizeOd_Medium" value="Medium" <?php echo ($elem_MidTurSizeOd_Medium == "Medium") ? "checked=checked" : "" ;?>><label for="elem_MidTurSizeOd_Medium" >Medium</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_MidTurSizeOd_Large" name="elem_MidTurSizeOd_Large" value="Large" <?php echo ($elem_MidTurSizeOd_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_MidTurSizeOd_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Middle Turbinate</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_MidTurSizeOs_Small" name="elem_MidTurSizeOs_Small" value="Small" <?php echo ($elem_MidTurSizeOs_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_MidTurSizeOs_Small" >Small</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_MidTurSizeOs_Medium" name="elem_MidTurSizeOs_Medium" value="Medium" <?php echo ($elem_MidTurSizeOs_Medium == "Medium") ? "checked=checked" : "" ;?>><label for="elem_MidTurSizeOs_Medium" >Medium</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_MidTurSizeOs_Large" name="elem_MidTurSizeOs_Large" value="Large" <?php echo ($elem_MidTurSizeOs_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_MidTurSizeOs_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Middle Turbinate"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Middle Turbinate"]; }?>
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Inf. Turbinate</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_InfTurSizeOd_Small" name="elem_InfTurSizeOd_Small" value="Small" <?php echo ($elem_InfTurSizeOd_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_InfTurSizeOd_Small" >Small</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_InfTurSizeOd_Medium" name="elem_InfTurSizeOd_Medium" value="Medium" <?php echo ($elem_InfTurSizeOd_Medium == "Medium") ? "checked=checked" : "" ;?>><label for="elem_InfTurSizeOd_Medium" >Medium</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_InfTurSizeOd_Large" name="elem_InfTurSizeOd_Large" value="Large" <?php echo ($elem_InfTurSizeOd_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_InfTurSizeOd_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Inf. Turbinate</td>
		<td>	
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_InfTurSizeOs_Small" name="elem_InfTurSizeOs_Small" value="Small" <?php echo ($elem_InfTurSizeOs_Small == "Small") ? "checked=checked" : "" ;?>><label for="elem_InfTurSizeOs_Small" >Small</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_InfTurSizeOs_Medium" name="elem_InfTurSizeOs_Medium" value="Medium" <?php echo ($elem_InfTurSizeOs_Medium == "Medium") ? "checked=checked" : "" ;?>><label for="elem_InfTurSizeOs_Medium" >Medium</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'NslExm');" id="elem_InfTurSizeOs_Large" name="elem_InfTurSizeOs_Large" value="Large" <?php echo ($elem_InfTurSizeOs_Large == "Large") ? "checked=checked" : "" ;?>><label for="elem_InfTurSizeOs_Large" >Large</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Inf. Turbinate"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Inf. Turbinate"]; }?>
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Polyps</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOd_Absent" name="elem_PolypsOd_Absent" value="Absent" <?php echo ($elem_PolypsOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_PolypsOd_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOd_1" name="elem_PolypsOd_1" value="1+" <?php echo ($elem_PolypsOd_1 == "+1" || $elem_PolypsOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOd_1" >1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOd_2" name="elem_PolypsOd_2" value="2+" <?php echo ($elem_PolypsOd_2 == "+2" || $elem_PolypsOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOd_2" >2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOd_3" name="elem_PolypsOd_3" value="3+" <?php echo ($elem_PolypsOd_3 == "+3" || $elem_PolypsOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOd_3" >3+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOd_4" name="elem_PolypsOd_4" value="4+" <?php echo ($elem_PolypsOd_4 == "+4" || $elem_PolypsOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOd_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Polyps</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOs_Absent" name="elem_PolypsOs_Absent" value="Absent" <?php echo ($elem_PolypsOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_PolypsOs_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOs_1" name="elem_PolypsOs_1" value="1+" <?php echo ($elem_PolypsOs_1 == "+1" || $elem_PolypsOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOs_1" >1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOs_2" name="elem_PolypsOs_2" value="2+" <?php echo ($elem_PolypsOs_2 == "+2" || $elem_PolypsOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOs_2" >2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOs_3" name="elem_PolypsOs_3" value="3+" <?php echo ($elem_PolypsOs_3 == "+3" || $elem_PolypsOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOs_3" >3+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_PolypsOs_4" name="elem_PolypsOs_4" value="4+" <?php echo ($elem_PolypsOs_4 == "+4" || $elem_PolypsOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_PolypsOs_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Polyps"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Polyps"]; }?>
		
		<tr class="exmhlgcol grp_NslExm <?php echo $cls_NslExm; ?>" >
		<td align="left">Inflammation</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOd_Absent" name="elem_InflamOd_Absent" value="Absent" <?php echo ($elem_InflamOd_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_InflamOd_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOd_1" name="elem_InflamOd_1" value="1+" <?php echo ($elem_InflamOd_1 == "+1" || $elem_InflamOd_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_InflamOd_1" >1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOd_2" name="elem_InflamOd_2" value="2+" <?php echo ($elem_InflamOd_2 == "+2" || $elem_InflamOd_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_InflamOd_2" >2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOd_3" name="elem_InflamOd_3" value="3+" <?php echo ($elem_InflamOd_3 == "+3" || $elem_InflamOd_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_InflamOd_3" >3+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOd_4" name="elem_InflamOd_4" value="4+" <?php echo ($elem_InflamOd_4 == "+4" || $elem_InflamOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_InflamOd_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>		
		<td align="center" class="bilat" onClick="check_bl('NslExm')">BL</td>
		<td align="left">Inflammation</td>
		<td>	
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOs_Absent" name="elem_InflamOs_Absent" value="Absent" <?php echo ($elem_InflamOs_Absent == "Absent") ? "checked=checked" : "" ;?>><label for="elem_InflamOs_Absent" >Absent</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOs_1" name="elem_InflamOs_1" value="1+" <?php echo ($elem_InflamOs_1 == "+1" || $elem_InflamOs_1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_InflamOs_1" >1+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOs_2" name="elem_InflamOs_2" value="2+" <?php echo ($elem_InflamOs_2 == "+2" || $elem_InflamOs_2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_InflamOs_2" >2+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOs_3" name="elem_InflamOs_3" value="3+" <?php echo ($elem_InflamOs_3 == "+3" || $elem_InflamOs_3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_InflamOs_3" >3+</label>
		</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this,'NslExm');" id="elem_InflamOs_4" name="elem_InflamOs_4" value="4+" <?php echo ($elem_InflamOs_4 == "+4" || $elem_InflamOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_InflamOs_4" >4+</label>
		</td>
		<td></td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Inflammation"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam/Inflammation"]; }?>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Nasal Exam"]; }?>
		
		<tr class="exmhlgcol grp_handle grp_SplTest <?php echo $cls_SplTest; ?>" id="d_SplTest">
		<td align="left" class="grpbtn" onclick="openSubGrp('SplTest')">			
			<label >Special Tests
			<span class="glyphicon <?php echo $arow_SplTest; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'SplTest');"  name="elem_SplTestOd_text" class="form-control" ><?php echo ($elem_SplTestOd_text);?></textarea></td>				
		<td align="center" class="bilat" onClick="check_bl('SplTest')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('SplTest')">
			<label >Special Tests 
			<span class="glyphicon <?php echo $arow_SplTest; ?>"></span></label>
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'SplTest');"  name="elem_SplTestOs_text" class="form-control" ><?php echo ($elem_SplTestOs_text);?></textarea></td>		
		</tr>
		
		
		<tr class="exmhlgcol grp_SplTest dacry <?php echo $cls_SplTest; ?>" >
		<td align="left">Dacryocystography</td>
		<td colspan="6">	
		<textarea name="elem_DacryoOd_Txt" onBlur="insertDate(this);checkwnls();checkSymClr(this,'SplTest');" class="form-control" ><?php echo $elem_DacryoOd_Txt;?></textarea>
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_DacryoOd_Dt">Date</label>
		<input type="text" name="elem_DacryoOd_Dt" id="elem_DacryoOd_Dt" onBlur="checkwnls();checkSymClr(this,'SplTest');"    size="10"   class="form-control" placeholder="Date"  value="<?php echo $elem_DacryoOd_Dt;?>">
		</div>
		</td>	
		<td align="center" class="bilat" onClick="check_bl('SplTest')" >BL</td>
		<td align="left">Dacryocystography</td>
		<td colspan="6">	
		<textarea name="elem_DacryoOs_Txt" onBlur="insertDate(this);checkwnls();checkSymClr(this,'SplTest');" class="form-control" ><?php echo $elem_DacryoOs_Txt;?></textarea>
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_DacryoOs_Dt">Date</label>
		<input type="text" id="elem_DacryoOs_Dt" name="elem_DacryoOs_Dt" onBlur="checkwnls();checkSymClr(this,'SplTest');"    size="10" class="form-control" placeholder="Date"     value="<?php echo $elem_DacryoOs_Dt;?>">
		</div>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests/Dacryocystography"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests/Dacryocystography"]; }?>
		
		<tr class="exmhlgcol grp_SplTest lacsci <?php echo $cls_SplTest; ?>" >
		<td align="left">Lacrimal Scintogram</td>
		<td colspan="6">
		<textarea name="elem_LacScintOd_Txt" onBlur="insertDate(this);checkwnls();checkSymClr(this,'SplTest');" class="form-control" ><?php echo $elem_LacScintOd_Txt;?></textarea>
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_LacScintOd_Dt">Date</label>	
		<input type="text" id="elem_LacScintOd_Dt" name="elem_LacScintOd_Dt" onBlur="checkwnls();checkSymClr(this,'SplTest');"     size="10" class="form-control"  placeholder="Date"   value="<?php echo $elem_LacScintOd_Dt;?>">
		</div>
		</td>
		<td align="center" class="bilat" onClick="check_bl('SplTest')" >BL</td>
		<td align="left">Lacrimal Scintogram</td>
		<td colspan="6">
		<textarea name="elem_LacScintOs_Txt" onBlur="insertDate(this);checkwnls();checkSymClr(this,'SplTest');" class="form-control" ><?php echo $elem_LacScintOs_Txt;?></textarea>
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_LacScintOs_Dt">Date</label>	
		<input type="text" id="elem_LacScintOs_Dt" name="elem_LacScintOs_Dt" onBlur="checkwnls();checkSymClr(this,'SplTest');"    size="10" class="form-control"  placeholder="Date"  value="<?php echo $elem_LacScintOs_Dt;?>">
		</div>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests/Lacrimal Scintogram"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests/Lacrimal Scintogram"]; }?>
		
		<tr class="exmhlgcol grp_SplTest ctmri <?php echo $cls_SplTest; ?>" >
		<td align="left">CT/MRI</td>
		<td colspan="6">
		<textarea name="elem_CTMRIOd_Txt" onBlur="insertDate(this);checkwnls();checkSymClr(this,'SplTest');" class="form-control" ><?php echo $elem_CTMRIOd_Txt;?></textarea>
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_CTMRIOd_Dt">Date</label>			
		<input type="text" id="elem_CTMRIOd_Dt" name="elem_CTMRIOd_Dt" onBlur="checkwnls();checkSymClr(this,'SplTest');"    size="10" class="form-control" placeholder="Date"   value="<?php echo $elem_CTMRIOd_Dt;?>">
		</div>
		</td>
		<td align="center" class="bilat" onClick="check_bl('SplTest')" >BL</td>
		<td align="left">CT/MRI</td>
		<td colspan="6">
		<textarea id="elem_CTMRIOs_Txt" name="elem_CTMRIOs_Txt" onBlur="insertDate(this);checkwnls();checkSymClr(this,'SplTest');" class="form-control" ><?php echo $elem_CTMRIOs_Txt;?></textarea>
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_CTMRIOs_Dt">Date</label>		
		<input type="text" id="elem_CTMRIOs_Dt" name="elem_CTMRIOs_Dt" onBlur="checkwnls();checkSymClr(this,'SplTest');"     size="10" class="form-control" placeholder="Date"    value="<?php echo $elem_CTMRIOs_Dt;?>">
		</div>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests/CT/MRI"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests/CT/MRI"]; }?>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Advanced Plastics/Special Tests"]; }?>
		
		
	</table>
</div>
	
	
<!-- Lacrimal System -Advance -->
<?php }//End Template check  ?>
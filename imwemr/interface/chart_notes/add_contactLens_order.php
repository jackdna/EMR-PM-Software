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
include_once('../../config/globals.php');

// SET LENSE CODE FUNCTION
$lensListQry= "SELECT * FROM contactlensecode order by lense_code";
$lensListRes = imw_query($lensListQry) or die(imw_error());				
$lensListNumRow = imw_num_rows($lensListRes);
if($lensListNumRow>0) {
	$i=0;
	while($lensListRow=imw_fetch_array($lensListRes)) {
		$arrLensCode[$i]['CODE_ID'] = $lensListRow['code_id'];
		$arrLensCode[$i]['LENSE_CODE'] = $lensListRow['lense_code'];
	$i++;
	}
}

function lenseCodes($arrLensCode,$selVal){
	$options ='';
	foreach($arrLensCode as $value)
	{ 
		$selected ='';
		if($value['CODE_ID']==$selVal) { $selected ='selected'; }
		$options.='<option value="'.$value['CODE_ID'].'" '.$selected.'>'.$value['LENSE_CODE'].'</option>';
	}
	return $options;
}
// ------------------------	
// SET LENSE COLOR FUNCTION
$colorListQry= "SELECT * FROM contactlensecolor order by color_name";
$colorListRes = imw_query($colorListQry) or die(imw_error());				
$colorListNumRow = imw_num_rows($colorListRes);
if($colorListNumRow>0) {
	$i=0;
	while($colorListRow=imw_fetch_array($colorListRes)) {
		$arrLensColor[$i]['COLOR_ID'] = $colorListRow['color_id'];
		$arrLensColor[$i]['COLOR_NAME'] = $colorListRow['color_name'];
	$i++;
	}
}

function lenseColors($arrLensColor, $selVal){
	$options ='';
	foreach($arrLensColor as $value)
	{
		$selected ='';
		if($value['COLOR_ID']==$selVal) { $selected ='selected'; }
		$options.='<option value="'.$value['COLOR_ID'].'" '.$selected.' >'.$value['COLOR_NAME'].'</option>';
	}
	return $options;
}
// -------------------------


// GET VALUES FOR SIMPLE MENU TYPE
$sqlManufact = "Select distinct(manufacturer) from contactlensemake order by 
		`contactlensemake`.`manufacturer` ASC";
$resManuf = imw_query($sqlManufact);
$arrSubOptions = array();	$strLen =0;	$DDMenuWidth = 120;
if(imw_num_rows($resManuf) > 0){
	while($rowManuf = imw_fetch_array($resManuf)){
		$manufacturer = $rowManuf['manufacturer'];
		$sqlStyle = "select distinct(style),type from contactlensemake where manufacturer = '$manufacturer' 
					order by `contactlensemake`.`style` ASC";
		$resStyle = imw_query($sqlStyle);
		$manLen = strlen($manufacturer);
		while($rowStyle = imw_fetch_array($resStyle)){
			$arrSubOptions[] = array($rowStyle["style"]."-".$rowStyle["type"],$xyz,$rowStyle["style"]."-".$rowStyle["type"]);
			$arrSubOptions1 = $rowStyle["style"];
			$stringAllManufact.="'".str_replace("'","",$arrSubOptions1)."',";
			$strL= strlen($rowStyle["style"]."-".$rowStyle["type"]);
			if($manLen > $strL) { $strL = $manLen; }
			if($strL > $strLen) { $strLen = $strL; }
		}
		$arrMainValues[] = array($manufacturer,$arrSubOptions);
		$manufacturer = '';unset($arrSubOptions);
	}
}	
if($strLen > 60) { $simpleMenuWidth = 350; }

$stringAllManufact = substr($stringAllManufact,0,-1);

$i = $_REQUEST['rowNum'] + 1;
$odos = $_REQUEST['odos'];

if($odos =='od') {                                        
?>
	<tr id="typeTrOD<?php echo $i;?>">
		<td class="od">
		   <!--<img id="imgOD<?php echo $i;?>" class="link_cursor" src="../../library/images/addinput.png" alt="Add More" title="Add More" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '<?php echo $i;?>');">-->
		   <span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 link_cursor" title="Add More" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '<?php echo $i;?>');"></span>
		</td>
		<td class="od">
			<label>&nbsp;OD</label>
		</td> 	
		<td class="text-left">
			<input type="text" name="LensBoxOD<?php echo $i;?>" id="LensBoxOD<?php echo $i;?>" class="typeAhead form-control" value="" onFocus="set_typeahead(this,'init_typeahead')"/>
			<input type="hidden" name="LensBoxOD<?php echo $i;?>ID" id="LensBoxOD<?php echo $i;?>ID" value="" funVars="printOrder~od~<?php echo $i;?>" >
		</td>
		<td>
			<select name="lensNameIdList<?php echo $i;?>" id="lensNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
				<?php echo lenseCodes($arrLensCode, ''); ?>
			</select>
		</td>	
		<td>
			<select name="colorNameIdList<?php echo $i;?>" id="colorNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
				<?php echo lenseColors($arrLensColor, '');?>
			</select>
		</td>
		<td>
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="PriceOD<?php echo $i;?>" type="text" name="PriceOD<?php echo $i;?>" value="" class="form-control" >
		</td> 	
		<td>
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);" type="text" name="QtyOD<?php echo $i;?>" value=""  class="form-control"  id="QtyOD<?php echo $i;?>" />
		</td>
		<td>
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOD<?php echo $i;?>" id="SubTotalOD<?php echo $i;?>" value=""  >
		</td> 	
		<td class="text-left">
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="DiscountOD<?php echo $i;?>" type="text" class="form-control " name="DiscountOD<?php echo $i;?>" value="" >
		</td> 	
		<td>
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  type="text" class="form-control " name="TotalOD<?php echo $i;?>" id="TotalOD<?php echo $i;?>" value="" >
		</td>	
		<td>
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="InsOD<?php echo $i;?>" id="InsOD<?php echo $i;?>" value=""  >
		</td>	
		<td>
			<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="BalanceOD<?php echo $i;?>" id="BalanceOD<?php echo $i;?>" value=""  />
		</td>	
	</tr>
<?php } ?>

<?php
if($odos =='os') {                                        
?>	    
	<tr id="typeTrOS<?php echo $i;?>">
		<td class="green_color form-controlb">
		   <!--<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOS<?php echo $i;?>" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '<?php echo $i;?>');">-->
		   <span class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 link_cursor" id="imgOS<?php echo $i;?>" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '<?php echo $i;?>');"></span>
		</td>	
		<td class="os">
			<label>&nbsp;OS</label>
		</td> 
		<td>
			<input type="text" name="LensBoxOS<?php echo $i;?>" id="LensBoxOS<?php echo $i;?>" class="typeAhead form-control" value="" onFocus="set_typeahead(this,'init_typeahead')"/>
			<input type="hidden" name="LensBoxOS<?php echo $i;?>ID" id="LensBoxOS<?php echo $i;?>ID" value="" funVars="printOrder~os~<?php echo $i;?>" >
		</td>
		<td>
			<select name="lensNameIdListOS<?php echo $i;?>" id="lensNameIdListOS<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
				<?php echo lenseCodes($arrLensCode, ''); ?>
			</select>
		</td>
		<td>
			<select name="colorNameIdListOS<?php echo $i;?>" id="colorNameIdListOS<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
			  <?php echo lenseColors($arrLensColor, '');?>                                      
			</select>
		</td>	
		<td>
			<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="PriceOS<?php echo $i;?>" type="text" name="PriceOS<?php echo $i;?>" value=""  class="form-control" >
		</td> 
		<td>
			<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);" type="text" name="QtyOS<?php echo $i;?>" value=""  class="form-control"  id="QtyOS<?php echo $i;?>" />
		</td>
		<td>
			<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOS<?php echo $i;?>" id="SubTotalOS<?php echo $i;?>" value=""  >
		</td>	
		<td class="text-left">
			<input onChange="javascript:calcTotalBalOSFn();justify2Decimal(this);" id="DiscountOS<?php echo $i;?>" type="text" class="form-control " name="DiscountOS<?php echo $i;?>" value="" >
		</td>
		<td>
			<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="TotalOS<?php echo $i;?>" type="text" class="form-control " name="TotalOS<?php echo $i;?>" value="" >
		</td>	
		<td>
			<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="InsOS<?php echo $i;?>" id="InsOS<?php echo $i;?>" value=""  >
		</td>	
		<td>
			<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="BalanceOS<?php echo $i;?>" id="BalanceOS<?php echo $i;?>" value=""  />
		</td>	
	</tr>
<?php } ?>
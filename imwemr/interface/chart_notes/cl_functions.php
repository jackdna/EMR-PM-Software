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

$saveTypeList = array('Evaluation','Fit','Refit','CL Check','Update Trial','Take Home CL','Current CL','Current Trial','Final');
$oldSheet = 0;
// Set lens code function
function getLensCodeArr($options = true){
	$arrLensCode= array();
	$lensListQry= "SELECT * FROM contactlensecode order by lense_code";
	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);
	if($lensListNumRow>0) {
		$i=0;
		while($lensListRow=imw_fetch_array($lensListRes)) {
			if($options){
				$arrLensCode[$i]['CODE_ID'] = $lensListRow['code_id'];
				$arrLensCode[$i]['LENSE_CODE'] = $lensListRow['lense_code'];
			}else{
				$id = $lensListRow['code_id'];
				$val = $lensListRow['lense_code'];
				$arrLensCode[$id] = $val;
			}
		$i++;
		}
	}
	return $arrLensCode;
}

function lenseCodes($arrLensCode,$selVal){		// Before call this function need to Call above fun.
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
function getLensColorArr($options = true){
	$arrLensColor = array();
	$colorListQry= "SELECT * FROM contactlensecolor order by color_name";
	$colorListRes = imw_query($colorListQry) or die(imw_error());				
	$colorListNumRow = imw_num_rows($colorListRes);
	if($colorListNumRow>0) {
		$i=0;
		while($colorListRow=imw_fetch_array($colorListRes)) {
			if($options){
				$arrLensColor[$i]['COLOR_ID'] = $colorListRow['color_id'];
				$arrLensColor[$i]['COLOR_NAME'] = $colorListRow['color_name'];
			}else{
				$id = $colorListRow['color_id'];
				$val = $colorListRow['color_name'];
				$arrLensColor[$id] = $val;
			}
		$i++;
		}
	}
	return $arrLensColor;
}
function lenseColors($arrLensColor, $selVal){	// Before call this function need to Call above fun.
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

//----GET CONTACT LENS MAKE DETAIL
// SET LENSE CODE FUNCTION
function getLensManufacturer(){
	$arrLensManuf= array();
	$lensListQry= "SELECT clmk.*, cpttbl.cpt_fee_id, cpttbl.cpt4_code, cpttbl.cpt_prac_code, cpttbl.cpt_prac_code FROM contactlensemake clmk 
LEFT JOIN cpt_fee_tbl cpttbl ON cpttbl.cpt_fee_id = clmk.cpt_fee_id order by clmk.make_id";
	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);      // Get row count in result set
	if($lensListNumRow>0) {        // If result set is not empty
		$i=0;
		while($lensListRow=imw_fetch_array($lensListRes)){       // Iterate result set
				$lensManuf='';
				// Save lens make id and details in array in following format:
				// Array ([2] => Array ([cpt_fee_id] => 518 [cpt4Code] => V2500 [cpt_prac_code] => CL1))
				$arrLensManuf[$lensListRow['make_id']]['cpt_fee_id'] = $lensListRow['cpt_fee_id'];      // Save lens make id and its corresponding cpt fee id in array
				$arrLensManuf[$lensListRow['make_id']]['cpt4Code'] = $lensListRow['cpt4_code'];         // Save lens make id and its corresponding cpt4code in same array
				$arrLensManuf[$lensListRow['make_id']]['cpt_prac_code'] = $lensListRow['cpt_prac_code'];// Save lens make id and its corresponding cpt prac code in same array
				
				
				if($lensListRow['manufacturer']!=''){        // If lens type not specified
					$lensManuf .= $lensListRow['manufacturer']."-";    // Save only lens style
				}				
				if($lensListRow['type']==''){        // If lens type not specified
					$lensManuf .= $lensListRow['style'];    // Save only lens style
				}else{      // If lens type is specified
					$lensManuf .= $lensListRow['style'].'-'.$lensListRow['type'];           // Concatenate lens style and type, and save
				}
				
				if($lensManuf[strlen($lensManuf)-1] == "-"){
                    $lensManuf = substr($lensManuf, 0, (strlen($lensManuf) - 1));
                }
				
				$arrLensManuf[$lensListRow['make_id']]['det'] = $lensManuf;                         // Save lens make id and corresponding lens style and type information in array
				$arrLensManuf[$lensListRow['make_id']]['make_only'] = $lensListRow['style'];        // Save lens make id and corresponding lens style and type information in array
                $arrLensManuf[$lensListRow['make_id']]['type_only'] = $lensListRow['type'];         // Save lens make id and corresponding lens style and type information in array
                $arrLensManuf[$lensListRow['make_id']]['manufac'] = $lensListRow['manufacturer'];   // Save lens make id and corresponding manufacturer name in array
				

				if($lensListRow['price']<=0 && !empty($lensListRow['cpt_fee_id'])){       // If price is empty or 0
				    // Get cpt fee from cpt_fee_table
					$cptQry = "SELECT cpt_fee FROM cpt_fee_table WHERE cpt_fee_id ='".$lensListRow['cpt_fee_id']."' AND fee_table_column_id=1";
					$cptRes = imw_query($cptQry) or die(imw_error());				
					$cptNumRow = imw_num_rows($cptRes);
					if($cptNumRow>0){
						$cptRow	=imw_fetch_array($cptRes);
						$arrLensManuf[$lensListRow['make_id']]['price'] = $cptRow['cpt_fee'];     // Save make id and corresponding cpt fee in array
					}
				}else{      // If price is greater than 0
					$arrLensManuf[$lensListRow['make_id']]['price'] = $lensListRow['price'];       // Save make id and corresponding price in array
				}
		}
	}
	
	return $arrLensManuf;      // Return array
}
//--------------------------------

// CHECK IF CPT CODE EXITS BY SENDING CPTFEEID
function checkCPTCodeExists($cptId){
	if($cptId>0){
		$qry="Select * FROM cpt_fee_tbl WHERE cpt_fee_id = '".$cptId."'";
		$rs = imw_query($qry);
		if(imw_num_rows($rs) > 0){
			return '1';
		}else{
			return '0';
		}
	}else{
		return '0';
	}
}

// GET ID OF PROCEDURE
function getCPT_Prac_Fee_Id($cptPracCode){
	$CPT_FEE_ID ='0';
	$lensListQry= "SELECT cpt_fee_id FROM cpt_fee_tbl WHERE cpt_prac_code ='".$cptPracCode."'";
	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);
	if($lensListNumRow>0) {
		$lensListResult = imw_fetch_array($lensListRes);
		$CPT_FEE_ID = $lensListResult['cpt_fee_id'];
	}
	return $CPT_FEE_ID;
}


// -----------------------
function colorMenu($drawingDiv)
{
$colorMenu ='<div style="width:100px;">
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 4px 0px;" onClick="changeColor(0,0,255,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#0000FF; width:8px; height:10px;"></div>
		<div class="fl txt_10 black_color">Flat</div>
	</div>
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 4px 0px" onClick="changeColor(255,255,0,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#FFFF00; width:8px; height:10px;"></div>
		<div class="fl txt_10 black_color">Alignment</div>
	</div>                                
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 4px 0px" onClick="changeColor(255,0,0,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#FF0000; width:8px; height:10px;"></div>
		<div class="txt_10 black_color fl">Steep</div>
	</div> 
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 3px 0px" onClick="changeColor(0,0,0,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#000000; width:8px; height:10px;"></div>
		<div class="txt_10 black_color fl">Draw</div>
	</div>
	<div style="height:11px; clear:both" class="alignLeft"><img src="../../images/eraser.gif"  onClick="getclear(\''.$drawingDiv.'\');" style="cursor:hand;" alt="Eraser"></div>                                                                
 </div>';
 return $colorMenu;
}

function makeSCLRow($odos, $i, $rowName, $txtTot,$topPad, $strAcuitiesMrDisString,$strAcuitiesNearString,$arrMainValues,$isHTML5OK=0)
{	
		switch($odos){
			case 'od':
				$DDName = 'clTypeOD';
				break;
			case 'os':
				$DDName = 'clTypeOS';
				break;
		}		
	
		$rowData='<div id="'.$rowName.$i.'" style="display:none; clear:both; height:48px" class="scl_bg">

				<div class="fl" style="width:55px; '.$topPad.'">
                  <select name="'.$DDName.$i.'" id="'.$DDName.$i.'" onChange="changeRow(this.value, \''.$odos.'\', \''.$rowName.'\', \''.$txtTot.'\', \''.$i.'\');" style="width:50px;" >
                    <option value="scl" selected="selected">SCL</option>
                    <option value="rgp" >RGP</option>
                    <option value="cust_rgp">Custom RGP</option>
                  </select>
               </div>
               <div class="fl" style="width:95%;">
				<table class="table_collapse" id="sclTable">
                  	<script type="text/javascript">document.write(setTitleRows(\'scl\'));</script>';
					
			if($odos =='od') { 	
				  $rowData.='<tr>
    			    <td style="width:18px;" class="blue_color txt_10b"><div class="text13" style="margin-right:2px">OD</div></td>
                    <td style="width:70px;"  >
                      <input class="txt_10" type="text" id="SclBcurveOD'.$i.'" name="SclBcurveOD'.$i.'" value="" size="6" onblur="justify2DecimalCL(this); copyValuesODToOS(\'SclBcurveOD'.$i.'\',\'SclBcurveOS'.$i.'\');">
					</td>
                    <td style="width:70px;" class="txt_11b alignLeft">
					  <input  id="SclDiameterOD'.$i.'" type="text" class="txt_10 " name="SclDiameterOD'.$i.'" value="" size="7" onblur="justify2DecimalCL(this); copyValuesODToOS(\'SclDiameterOD'.$i.'\',\'SclDiameterOS'.$i.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td>
                    <td style="width:70px;" class="txt_11b alignLeft" >
				  	<input type="text" name="SclsphereOD'.$i.'" value="" size="6" class="txt_10"  id="SclsphereOD'.$i.'" onblur="justify2DecimalCL(this);" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td>  
                    <td style="width:72px;" class="txt_11b nowrap alignLeft" >
                      <input  id="SclCylinderOD'.$i.'" type="text" name="SclCylinderOD'.$i.'" value="" size="6" class="txt_10" onblur="justify2DecimalCL(this);" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td> 
                    <td style="width:80px;" class="txt_11b alignLeft" >
                    <input type="text" name="SclaxisOD'.$i.'" value="" size="6" class="txt_10"  id="SclaxisOD'.$i.'" onblur="justify2DecimalCL(this,\'noDecimal\');" onKeyUp="check2BlurCL(this,\'A\',\'\',\''.$rowName.$i.'\');"/>
                    </td> 
                    <td style="width:70px;" class="txt_11b alignLeft" >
                    <input  id="SclAddOD'.$i.'" type="text" class="txt_10" name="SclAddOD'.$i.'" value=""   size="5" onblur="justify2DecimalCL(this); copyValuesODToOS(\'SclAddOD'.$i.'\',\'SclAddOS'.$i.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td>
                    <td style="width:110px;" class="txt_11b alignLeft nowrap" >
                    <input id="SclDvaOD'.$i.'" type="text" name="SclDvaOD'.$i.'" value="20/" size="8" class="txt_10">
					<script>
                        $(\'#SclDvaOD\'+'.$i.').combobox({
                            data: ['.$strAcuitiesMrDisString.'],
                            autoShow: false,
                            listHTML: function(val, index) {
                                return $.ui.combobox.defaults.listHTML(
                                      val, index);
                            }
                        });
						$(\'#SclDvaOD\'+'.$i.').focus(function() {
						  setCursorAtEnd(this);
						});							
                    </script>
                    </td> 
                    <td style="width:130px;" class="txt_11b alignLeft nowrap" >
					<input type="text" name="SclNvaOD'.$i.'"  id="SclNvaOD'.$i.'" value="20/" size="12" class="txt_10" >
					<script>
                        $(\'#SclNvaOD\'+'.$i.').combobox({
                            data: ['.$strAcuitiesNearString.'],
                            autoShow: false,
                            listHTML: function(val, index) {
                                return $.ui.combobox.defaults.listHTML(
                                      val, index);
                            }
                        });
						$(\'#SclNvaOD\'+'.$i.').focus(function() {
						  setCursorAtEnd(this);
						});							
                    </script>   
                    </td> 
                    <td style="width:410px;" class="nowrap" >
                    <div class="fl alignLeft" style="width:335px;" >
                        <input type="text" name="SclTypeOD'.$i.'" id="SclTypeOD'.$i.'" class="fg-button ui-widget ui-corner-all lensboxod_menu" style="width:330px;" value=""/ >
						<input type="hidden" name="SclTypeOD'.$i.'ID" id="SclTypeOD'.$i.'ID" value="" funVars="worksheet~SclTypeOD'.$i.'~SclTypeOS'.$i.'~SclBcurveOD'.$i.'~SclDiameterOD'.$i.'">
                    </div>';
                    $rowData.='<div class="fl" style="width:27px;">&nbsp;<img src="images/odDrawing.png" id="scl_odDrawingPngImg'.$i.'" onClick="showAppletsDiv(\'drawingSCLODId'.$i.'\');"  style="cursor:hand;" title="Show/Hide OD Drawing"></div>
					<div class="fl" style="width:20px;">&nbsp;&nbsp;';
                    if($i < $txtTot) {  
					$rowData.=	'<img id="imgOD'.$i.'" class="link_cursor" src="../../images/cancelled.gif" alt="Delete Row" onClick="removeTableRow(\''.$rowName.$i.'\');">';
                     }else { 
                    $rowData.=	'<img id="imgOD'.$i.'" class="link_cursor" src="../../images/add_medical_history.gif" alt="Add More" onClick="addNewRow(dgi(\''.$DDName.$i.'\').value, \'od\', \''.$rowName.'\', \'imgOD\', \''.$i.'\',\'10\');">';
                     }
                    $rowData.='</div>';

                    $rowData.='<div id="drawingSCLODId'.$i.'" style="display:none; z-index:1001; position:absolute; top:30px; left:500px; background-color:#73b7dc;">
                     <table class="table_collapse_autoW">
                        <tr class="la_bg_brown white_color txt_11b" style="cursor:move;">
                            <td colspan="3">OD Drawing</td>
                            <td class="alignRight" style="cursor:hand;"><img src="../../images/close_chart.gif" onClick="javascript:hideAppletsDiv(\'drawingSCLODId'.$i.'\');"></td>
                        </tr>
                        <tr>
                        <td class="valignTop">'.
                            colorMenu('app_scl_od_drawing'.$i).'
                        </td>';
		
		if( $isHTML5OK=="1" ) { 
		
			$rowData.='<td><div class="sigdrw div_drw_od" >
				<canvas id="app_scl_od_drawing'.$i.'" width="224" height="80" data-left-pos="500" data-top-pos="30"></canvas>
				<input type="hidden" name="sig_dataapp_scl_od_drawing'.$i.'"  id="sig_dataapp_scl_od_drawing'.$i.'" />
				<input type="hidden" name="sig_imgapp_scl_od_drawing'.$i.'"  id="sig_imgapp_scl_od_drawing'.$i.'" value="" />
			</div></td>';
			
		} //COMPATIBILITY
		
                    $rowData.='<td class="alignRight" colspan="2">&nbsp; 
                            <textarea  onblur="checkwnls()"  name="corneaSCL_od_desc'.$i.'" id="corneaSCL_od_desc'.$i.'"  cols="12" rows="5"></textarea>
                        </td>
                        </tr>
                        </table>
                        <input type="hidden" name="elem_SCLOdDrawing'.$i.'" id="elem_SCLOdDrawing'.$i.'" value="" onChange="checkwnls();">
			<input type="hidden" name="elem_SCLOsDrawingPath'.$i.'" id="elem_SCLOsDrawingPath'.$i.'" value="" >
                        <input type="hidden" name="hdSCLOdDrawingOriginal'.$i.'" id="hdSCLOdDrawingOriginal'.$i.'" value="">
                    </div>';
                    $rowData.='
					</td>
                    </tr>';
			}
			if($odos =='os'){
				$rowData.='
					<tr>
					  <td style="width:18px;" class="green_color txt_10b"><div class="text13" style="margin-right:2px">OS</div></td>
                        <td style="width:70px;"><input  type="text" name="SclBcurveOS'.$i.'" id="SclBcurveOS'.$i.'" value="" size="6" class="txt_10" onblur="justify2DecimalCL(this);"></td> 
                        <td style="width:70px;"><input  id="SclDiameterOS'.$i.'" type="text" class="txt_10 " name="SclDiameterOS'.$i.'" value="" size="7" onblur="justify2DecimalCL(this);" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td style="width:70px;"><input type="text" name="SclsphereOS'.$i.'" value="" size="6" class="txt_10" id="SclsphereOS'.$i.'" onBlur="justify2DecimalCL(this)" onblur="justify2DecimalCL(this);" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td style="width:72px;"><input class="txt_10" id="SclCylinderOS'.$i.'" type="text" name="SclCylinderOS'.$i.'" value="" size="6"  onBlur="justify2DecimalCL(this)" onblur="justify2DecimalCL(this);" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"> </td> 
                        <td style="width:80px;"  class="alignLeft" ><input type="text" name="SclaxisOS'.$i.'" value="" size="6" class="txt_10" id="SclaxisOS'.$i.'" onblur="justify2DecimalCL(this,\'noDecimal\');" onKeyUp="check2BlurCL(this,\'A\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td style="width:70px;"><input  id="SclAddOS'.$i.'" type="text" class="txt_10 " name="SclAddOS'.$i.'"  value="" size="5" onblur="justify2DecimalCL(this);" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td style="width:110px;" class="valignTop nowrap" >
                          <input id="SclDvaOS'.$i.'" type="text" name="SclDvaOS'.$i.'" value="20/" size="8" class="txt_10" >
                          <script>
                                $(\'#SclDvaOS\'+'.$i.').combobox({
                                    data: ['.$strAcuitiesMrDisString.'],
                                    autoShow: false,
                                    listHTML: function(val, index) {
                                        return $.ui.combobox.defaults.listHTML(
                                              val, index);
                                    }
                                });
								$(\'#SclDvaOS\'+'.$i.').focus(function() {
								  setCursorAtEnd(this);
								});									
                            </script>		
                        </td> 
                        <td style="width:130px;" class="nowrap">
                        <input type="text" name="SclNvaOS'.$i.'"  id="SclNvaOS'.$i.'" value="20/" size="12" class="txt_10" > 
                        <script>
                                $(\'#SclNvaOS'.$i.'\').combobox({
                                    data: ['.$strAcuitiesNearString.'],
                                    autoShow: false,
                                    listHTML: function(val, index) {
                                        return $.ui.combobox.defaults.listHTML(
                                              val, index);
                                    }
                                });
								$(\'#SclNvaOS\'+'.$i.').focus(function() {
								  setCursorAtEnd(this);
								});									
                            </script>	
                        </td> 
                        <td class="nowrap" style="width:410px;" >
						<div class="fl alignLeft" style="width:335px;" >
							<input type="text" name="SclTypeOS'.$i.'" id="SclTypeOS'.$i.'" class="fg-button ui-widget ui-corner-all lensboxod_menu" style="width:330px;" value=""/>
							<input type="hidden" name="SclTypeOS'.$i.'ID" id="SclTypeOS'.$i.'ID" value="" funVars="halfFun~garbage1~garbage2~SclBcurveOS'.$i.'~SclDiameterOS'.$i.'">
						</div>';
						$rowData.='<div class="fl" style="width:27px;">&nbsp;<img id="scl_oSDrawingPngImg'.$i.'" src="images/odDrawing.png"  onClick="showAppletsDiv(\'drawingSCLOSId'.$i.'\');" class="green_color txt_10b" style="cursor:hand;" title="Show/Hide OS Drawing"></div>
                        <div class="fl" style="width:20px;">&nbsp;&nbsp;';
						if($i < $txtTot) {  
                        	$rowData.='<img id="imgOS'.$i.'" class="link_cursor" src="../../images/cancelled.gif" alt="Delete Row" onClick="removeTableRow(\''.$rowName.$i.'\');">';
                        }else { 
                        	$rowData.='<img id="imgOS'.$i.'" class="link_cursor" src="../../images/add_medical_history.gif" alt="Add More" onClick="addNewRow(dgi(\''.$DDName.$i.'\').value, \'os\', \''.$rowName.'\', \'imgOS\', \''.$i.'\',\'10\');">';
						} 	
						$rowData.='</div>';


						$rowData.='<div id="drawingSCLOSId'.$i.'" style="display:none;  z-index:1001; position: absolute; top:30px; left:5px; background-color:#73b7dc;">
                        <table class="table_collapse_autoW">
                            <tr class="la_bg_brown white_color txt_11b" style="cursor:move">
                                <td colspan="3">OS Drawing</td>
                                <td class="alignRight" style="cursor:hand;"><img src="../../images/close_chart.gif" onClick="javascript:hideAppletsDiv(\'drawingSCLOSId'.$i.'\');"></td>
                            </tr>
                            <tr>
                            <td class="valignTop">'.
                                colorMenu('app_scl_os_drawing'.$i).'     
                            </td>';
			    
			if( $isHTML5OK=="1" ) { 
		
				$rowData.='<td><div class="sigdrw div_drw_os">
					<canvas id="app_scl_os_drawing'.$i.'" width="224" height="80" data-left-pos="5" data-top-pos="30"></canvas>
					<input type="hidden" name="sig_dataapp_scl_os_drawing'.$i.'"  id="sig_dataapp_scl_os_drawing'.$i.'" />
					<input type="hidden" name="sig_imgapp_scl_os_drawing'.$i.'"  id="sig_imgapp_scl_os_drawing'.$i.'" value="" />
				</div></td>';
			
			} //COMPATIBILITY
			    
                        $rowData.='<td class="alignRight" colspan="2">&nbsp;
                                 <textarea  onblur="checkwnls()"  name="corneaSCL_os_desc'.$i.'" id="corneaSCL_os_desc'.$i.'"  cols="12" rows="5"></textarea>
                            </td>
                            </tr>
                            </table>
                            <input type="hidden" name="elem_SCLOsDrawing'.$i.'" id="elem_SCLOsDrawing'.$i.'" value="" onChange="checkwnls();">
			    <input type="hidden" name="elem_SCLOsDrawingPath'.$i.'" id="elem_SCLOsDrawingPath'.$i.'" value="" >
                            <input type="hidden" name="hdSCLOsDrawingOriginal'.$i.'" id="hdSCLOsDrawingOriginal'.$i.'" value="">
                        </div>';
						$rowData.='									
                                    </td>
                                </tr>';
			}

                   $rowData.='</table>
               </div>
             </div>';  
	return $rowData;
}

function getManufID($styleType){
	$styleType = trim($styleType);
	$makeId = 0;
	$qry= $qryWhere= $insQry='';
	$qry="Select make_id from contactlensemake WHERE 1=1";
	
	$manufacName = "";
	$style="";
	$type = "";
	$parts = explode("-", $styleType);
	if(empty($parts[0]) === false){
		$manufacName = trim($parts[0]);
	}
	if(empty($parts[1]) === false){
		$style = trim($parts[1]);
	}
	if(empty($parts[2]) === false){
		$type = trim($parts[2]);
	}
	
	$qryWhere .= " AND manufacturer='".$manufacName."' AND style='".$style."' AND type='".$type."'";
	$insQry .= " manufacturer='".addslashes(trim($manufacName))."', style='".addslashes(trim($style))."', type='".addslashes(trim($type))."'";
	
	/* $hyphenPos = stripos($styleType, "-");
	if($hyphenPos !== false){				// If string has hyphen
		$parts = explode("-", $styleType);
		if(empty($parts[0]) === false){
			$qryWhere .= " AND style='".$parts[0]."'";
			$insQry .= " style='".addslashes(trim($parts[0]))."'";
		}
		if(empty($parts[1]) === false){
			$qryWhere.= " AND type='".$parts[1]."'";
			$insQry.= ", type='".addslashes(trim($parts[1]))."'";
		}
	}else{
		$qryWhere .= " AND style='".$styleType."'";
		$insQry .= " style='".addslashes(trim($styleType))."'";
	} */
	
	$qry.=$qryWhere;
	
	$rs=imw_query($qry) or die(imw_error() . "-" .$qry);
	if(imw_num_rows($rs)>0){
		$res=imw_fetch_row($rs);
		$makeId = $res[0];
	}else{
		// GET CPT ID FOR DETAULT CL CPT CODE
		$cpt_fee_id=0;
		$cpt_fee_id = getCPT_Prac_Fee_Id('92310');
		if($cpt_fee_id<0){ $cpt_fee_id=0; } 
		
		// Insert New Data
		$insertQry = "Insert into contactlensemake SET manufacturer_id=0, source=1, cpt_fee_id='".$cpt_fee_id."', ".$insQry;
		$insRs = imw_query($insertQry) or die(imw_error() . "-" .$insRs);
		$makeId = imw_insert_id();
	}
	return $makeId;
	
	/* $i=1;
	if(sizeof($parts)>2){
		$i=1;
	}
	
	if($parts[$i]){
		$qryWhere= " AND style='".$parts[$i]."'";
		$insQry= " style='".addslashes(trim($parts[$i]))."'";
	}
	$i=$i+1;
	if($parts[$i]){
		$qryWhere.= " AND type='".$parts[$i]."'";
		$insQry.= ", type='".addslashes(trim($parts[$i]))."'";
	} */
}

/**
 * Gets lens's make id, curve and diameter
 * 
 */
function getLensManufVals(){
	$arrLensManufValues= array();
	$lensListQry= "SELECT clmk.make_id,clmk.base_curve, clmk.diameter FROM contactlensemake clmk 
LEFT JOIN cpt_fee_tbl cpttbl ON cpttbl.cpt_fee_id = clmk.cpt_fee_id WHERE clmk.base_curve<>'' OR clmk.diameter<>'' order by clmk.make_id";
	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);
	if($lensListNumRow>0){
		while($lensListRow=imw_fetch_array($lensListRes)) {
			$arrLensManufValues[$lensListRow['make_id']] = $lensListRow['make_id'].'$'.$lensListRow['base_curve'].'$'.$lensListRow['diameter'];
		}
	}
	return $arrLensManufValues;
}

function getCLCPTPracticeCode(){
	$arrCPTIds=array();
/*	$rs=imw_query("Select cpt_cat_id FROM cpt_category_tbl");
	while($res=imw_fetch_array($rs)){
		$arrCats[$res['cpt_cat_id']]=$res['cpt_cat_id'];
	}unset($rs);*/
	
	//$cl_cat_id= implode(',', $arrCats);
	$qryCPT = imw_query("Select cpt_fee_id, cpt4_code, cpt_prac_code from cpt_fee_tbl ORDER BY cpt_prac_code");
	while($qryCPTRes = imw_fetch_array($qryCPT)){
		//$arrCPTCodes[$qryCPTRes['cpt_fee_id']] = $qryCPTRes['cpt_prac_code'];
		$arrCPTIds[$qryCPTRes['cpt_fee_id']] = $qryCPTRes['cpt_fee_id'];
	}
	return $arrCPTIds;
}

function getCPTDefaultCharges($cptFeeId=''){
	
	$arrCPTIds = getCLCPTPracticeCode();
	$strCPTIds = implode(',', $arrCPTIds);
	
	//GET ID OF DEFAULT FEE COLUMN
	$rs=imw_query("Select fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)='default'");
	$res=imw_fetch_array($rs);
	$defaultFeeId = $res['fee_table_column_id'];
	
	//GET DEFAULT CPT CHARGES	
	if(sizeof($arrCPTIds)>0){
		$cptFeeTableQry = "Select cpt_fee_id,cpt_fee FROM cpt_fee_table WHERE fee_table_column_id='".$defaultFeeId."' AND cpt_fee_id IN(".$strCPTIds.")";
		$cptFeeTableRes = imw_query($cptFeeTableQry);
		while($cptFeeTableRow = imw_fetch_array($cptFeeTableRes)){
			$arrDefaultCPTFee[$cptFeeTableRow["cpt_fee_id"]] = $cptFeeTableRow["cpt_fee"];
		}
	}
	return $arrDefaultCPTFee;
}

function clearPrescription($clEye='',$clType=''){
	$detQryPart='';
	if($clType=='scl'){
		if($clEye=='od'){
			$detQryPart="
			SclsphereOD='',
			SclCylinderOD='',
			SclaxisOD='',
			SclBcurveOD='',
			SclDiameterOD='',
			SclAddOD='',
			SclDvaOD='',
			SclNvaOD='',
			SclTypeOD='',
			SclTypeOD_ID='',
			corneaSCL_od_desc='',
			elem_SCLOdDrawing='',
			hdSCLOdDrawingOriginal=''";
		}elseif($clEye=='os'){
			$detQryPart="		
			SclsphereOS='',
			SclCylinderOS='',
			SclaxisOS='',
			SclBcurveOS='',
			SclDiameterOS='',
			SclAddOS='',
			SclDvaOS='',
			SclNvaOS='',
			SclTypeOS='',
			SclTypeOS_ID='',
			corneaSCL_os_desc='',
			elem_SCLOsDrawing='',
			hdSCLOsDrawingOriginal=''";
		}elseif($clEye=='ou'){
			$detQryPart="		
			SclsphereOU='',
			SclCylinderOU='',
			SclaxisOU='',
			SclBcurveOU='',
			SclDiameterOU='',
			SclAddOU='',
			SclDvaOU='',
			SclNvaOU='',
			SclTypeOU='',
			SclTypeOU_ID='',
			corneaSCL_ou_desc='',
			elem_SCLOuDrawing='',
			hdSCLOuDrawingOriginal=''";
		}
	}elseif($clType=='rgp'){
		if($clEye=='od'){
			$detQryPart="
			RgpPowerOD='',
			RgpBCOD='',
			RgpDiameterOD='',
			RgpOZOD='',
			RgpColorOD='',
			RgpAddOD='',
			RgpDvaOD='',
			RgpNvaOD='',
			RgpTypeOD='',
			RgpTypeOD_ID=''";
		}elseif($clEye=='os'){
			$detQryPart="
			RgpPowerOS='',
			RgpBCOS='',
			RgpDiameterOS='',
			RgpOZOS='',
			RgpColorOS='',
			RgpAddOS='',
			RgpDvaOS='',
			RgpNvaOS='',
			RgpTypeOS='',
			RgpTypeOS_ID=''";
		}elseif($clEye=='ou'){
			$detQryPart="
			RgpPowerOU='',
			RgpBCOU='',
			RgpDiameterOU='',
			RgpOZOU='',
			RgpColorOU='',
			RgpAddOU='',
			RgpDvaOU='',
			RgpNvaOU='',
			RgpTypeOU='',
			RgpTypeOU_ID=''";
		}
	}elseif($clType=='cust_rgp'){
		if($clEye=='od'){
			$detQryPart="
			RgpCustomPowerOD='',
			RgpCustomBCOD='',
			RgpCustom2degreeOD='',
			RgpCustom3degreeOD='',
			RgpCustomPCWOD='',
			RgpCustomDiameterOD='',
			RgpCustomOZOD='',
			RgpCustomColorOD='',
			RgpCustomBlendOD='',
			RgpCustomEdgeOD='',
			RgpCustomAddOD='',
			RgpCustomDvaOD='',
			RgpCustomNvaOD='',
			RgpCustomTypeOD='',
			RgpCustomTypeOD_ID=''";
		}elseif($clEye=='os'){
			$detQryPart="
			RgpCustomPowerOS='',
			RgpCustomBCOS='',
			RgpCustom2degreeOS='',
			RgpCustom3degreeOS='',
			RgpCustomPCWOS='',
			RgpCustomDiameterOS='',
			RgpCustomOZOS='',
			RgpCustomColorOS='',
			RgpCustomBlendOS='',
			RgpCustomEdgeOS='',
			RgpCustomAddOS='',
			RgpCustomDvaOS='',
			RgpCustomNvaOS='',
			RgpCustomTypeOS='',
			RgpCustomTypeOS_ID=''";
		}elseif($clEye=='ou'){
			$detQryPart="
			RgpCustomPowerOU='',
			RgpCustomBCOU='',
			RgpCustom2degreeOU='',
			RgpCustom3degreeOU='',
			RgpCustomPCWOU='',
			RgpCustomDiameterOU='',
			RgpCustomOZOU='',
			RgpCustomColorOU='',
			RgpCustomBlendOU='',
			RgpCustomEdgeOU='',
			RgpCustomAddOU='',
			RgpCustomDvaOU='',
			RgpCustomNvaOU='',
			RgpCustomTypeOU='',
			RgpCustomTypeOU_ID=''";
		}
	}
	return $detQryPart;
}

function saveCLDrwing($sData, $sImg,$addTxt=""){
	include_once(dirname(__FILE__)."/SaveFile.php");

	$strpixls = $sData;
	$sImg = $sImg;
	
	if(!empty($strpixls)&& strpos($strpixls,"data:image/png;base64,") !== false){			
	
		$tmp_sign_path1 = "";
		$oSaveFile = new SaveFile($_SESSION["patient"]); 		
		
		$tmpDirPth_up = dirname(__FILE__)."/../../main/uploaddir";
		$tmpDirPth_sign = $oSaveFile->ptDir("ContactLens");
		$tmpDirPth_pt = "/PatientId_".$_SESSION["patient"];
		$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
		$tmp_sign_path=realpath($tmpDirPth_up.$form_sign_path);
		if(!empty($addTxt)){ $addTxt="_".$addTxt; }
		//Make Image 			
		$img_nm = "/conlen"."_".time()."_".$_SESSION["authId"].$addTxt.".jpg";
		$tmp_sign_path1=$tmp_sign_path.$img_nm;
		
	
		$strpixls = str_replace("data:image/png;base64,","",$strpixls);
		$r = file_put_contents($tmp_sign_path1, base64_decode($strpixls));
		
		return $form_sign_path.$img_nm;
		
	}
	
	return "";
}

function getHqFacility()
{
	$sql = "SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
	$row = sqlQuery($sql);
	if($row != false)
	{
		return $row["id"];
	}
	else
	{
		// Fix if No Hq. is selected
		$sql = "SELECT id FROM facility LIMIT 0,1 ";
		$row = sqlQuery($sql);
		if($row != false)
		{
			return $row["id"];
		}
	}
}

function getEncounterId()
{
	$facilityId = getHqFacility();
	$sql = "SELECT encounterId FROM facility WHERE id='".$facilityId."' ";
	$row = sqlQuery($sql);

	if($row != false)
	{
		$encounterId = $row["encounterId"];
	}
	
	//get from policies
	$sql = "select Encounter_ID from copay_policies WHERE policies_id = '1' ";
	$row = sqlQuery($sql);
	if($row != false){
		$encounterId_2 = $row["Encounter_ID"];		
	}
	//bigg
	if($encounterId<$encounterId_2){
		$encounterId = $encounterId_2;
	}
	
	//--
	$counter=0; //check only 100 times
	do{
	
	$flgbreak=1;
	//check in superbill
	if($flgbreak==1){
		$sql = "select count(*) as num FROM superbill WHERE encounterId='".$encounterId."' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			$flgbreak=0;
		}	
	}
	
	//check in chart_master_table--
	if($flgbreak==1){
		$sql = "select count(*) as num FROM chart_master_table WHERE encounterId='".$encounterId."' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			$flgbreak=0;
		}
	}
	
	//check in Accounting
	if($flgbreak==1){
		$sql = "select count(*) as num FROM patient_charge_list WHERE encounter_id='".$encounterId."'";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			$flgbreak=0;
		}	
	}
	
	if($flgbreak==0) {$encounterId=$encounterId+1;}
	$counter++;
	}while($flgbreak==0 && $counter<100);
	if($counter>=100){ exit("Error: encounter Id counter needs to reset."); }
	//--	
	
	$sql = "UPDATE copay_policies SET Encounter_ID = '".($encounterId+1)."' WHERE policies_id='1' ";
	$row = sqlQuery($sql);
	

	$sql = "UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facilityId."' ";
	$row = sqlQuery($sql);
	return $encounterId;
}

/**
 * Gets lens details
 * 
 */
function getLensDetails(){
    $manufacturerNameArray = array();
    $manufacturerIdArray = array();
    $manufacturerInfoArray = array();
    $query = "select make_id, manufacturer, style, type, base_curve, diameter FROM contactlensemake WHERE del_status=0 and (source = 0 || source = '') ORDER BY style, type ASC";
    $result = imw_query($query);            // Execute query
    if(imw_num_rows($result) > 0){        // If result set is not empty
        while($res = imw_fetch_array($result)){     // Iterate result set
            $styleType = '';
            $separator = '';
            if($res['manufacturer'] != ''){     // if manufacturer is specified
                $styleType = $res['manufacturer'];      // Set manufacturer in style type
                $separator = '-';
            }
            if($res['style'] != ''){
                $styleType .= $separator . $res['style'];
                $separator = '-';
            }
            if($res['type'] != ''){
                $styleType .= $separator . $res['type'];
            }
            $manufacturerNameArray[] = $styleType;
            $manufacturerIdArray[$styleType] = $res['make_id'];
            $manufacturerInfoArray[$res['make_id']] = $res['base_curve'].'~'.$res['diameter'];
        }
    }
    unset($result);
    return array($manufacturerNameArray, $manufacturerIdArray, $manufacturerInfoArray);
}

function getLensCharges($defaultCLChargesFeeArray){
    $clChargesAdminArray = array();
    $clChargesArray = array();
    $query = "select cl_charge_id, name, cpt_fee_id from cl_charges WHERE del_status=0 ORDER BY cl_charge_id";
    $result = imw_query($query);        // Execute query
    while($chargeRES = imw_fetch_array($result)){
        $cptPrice = $defaultCLChargesFeeArray[$chargeRES['cpt_fee_id']];
        $cptPrice = ($cptPrice <= 0) ? '0' : $cptPrice;
        $clChargesArray[$chargeRES['name']] = $cptPrice.'~'.$chargeRES['cl_charge_id'];
        $clChargesAdminArray[$chargeRES['cl_charge_id']] = $chargeRES['name'].'~'.$cptPrice;
    }
    $arrCLChargesJS = $clChargesArray;
    $arrCLChargesJS['Take Home CL']='Take Home CL';
    $arrCLChargesJS['Current CL']='Current CL';
    $arrCLChargesJS['Final']='Final';
    $arrCLChargesJS['Current Trial']='Current Trial';
    $arrCLChargesJS['Update Trial']='Update Trial';
    return array($clChargesArray, $arrCLChargesJS);
}

/**
 * Function to get top row when popup is opened for first time.
 * 
 * @param $patientId    Patient for which worksheets to be retreived
 */
function getTopRow($patientId){
    $worksheetListQuery = "SELECT currentWorksheetid, clws_trial_number, clws_id, DATE_FORMAT(dos,'%d-%m-%Y') as PreviousDOS, DATE_FORMAT(clws_savedatetime,'%d-%m-%Y') as savedDate, clws_type, form_id, del_status FROM contactlensmaster where patient_id='".$patientId."' ORDER BY form_id DESC, clws_id DESC";
    $result = imw_query($worksheetListQuery);        // Execute query
    $worksheetArray = array();
    $oldSheet = 0;
    while($res = imw_fetch_array($result)){
        $currentWorksheetId = $res['currentWorksheetid'];
        $clwsTrialNumber = $res['clws_trial_number'];
        $clwsId = $res['clws_id'];
        $previousDos = $res['PreviousDOS'];
        $savedDate = $res['savedDate'];
        $clwsType = $res['clws_type'];
        $formId = $res['form_id'];
        $delStatus = $res['del_status'];
        
        if($oldSheet <= 0){
            $oldSheet = $clwsId;
        }
        
        $worksheetArray["$clwsId"]['wsid'] = $wsId;
        $worksheetArray["$clwsId"]['worksheet_count'] = $currentWorksheetId;
        $worksheetArray["$clwsId"]['clws_trial_number'] = $clwsTrialNumber;
        $worksheetArray["$clwsId"]['previous_dos'] = $previousDos;
        $worksheetArray["$clwsId"]['saved_date'] = $savedDate;
        $worksheetArray["$clwsId"]['clws_type'] = $clwsType;
        $worksheetArray["$clwsId"]['form_id'] = $formId;
        $worksheetArray["$clwsId"]['del_status'] = $delStatus;
    }
    return array($worksheetArray, array("old-worksheet" => $oldSheet));
}

/* function getExistingWorksheetColumn($status, $wsId){
    $qry="Select cl.*, DATE_FORMAT(dos, '".getSqlDateFormat()."') as 'dos', DATE_FORMAT(cl.clws_savedatetime , '".getSqlDateFormat()."') AS worksheetdate, cl_det.* FROM contactlensmaster cl LEFT JOIN contactlensworksheet_det cl_det ON cl_det.clws_id = cl.clws_id WHERE cl.patient_id='".$_SESSION['patient']."'";
} */

function getFormAndCSWS($patientId){
    $formCSArray = array();
    $result = imw_query("select form_id, clws_id from contactlensmaster where patient_id='".$patientId."'");        // Execute query
    while($res = imw_fetch_array($result)){
        $form_id = $res['form_id'];
        $csWorksheetId = $res['clws_id'];
        $formCSArray["$form_id"][] = $csWorksheetId;
    }
    return $formCSArray;
}

function getODs($result, $wsId, $evalResult)
{
    $odArray = array();
    mysqli_data_seek($result, 0);
    while($res = imw_fetch_array($result))
    {
        $clWSId = $res['clws_id'];          // Get workseet id
        if($wsId == $clWSId)
        {
            $clEye = $res['clEye'];              // Whether column is 'OD' or 'OS'
            if($clEye == 'OD')              // If column is OD
            {
                $tempODArray = array();
                
                $tempODArray['od_id'] = $res['id'];
                $tempODArray['cl_type'] = $res['clType'];
                
                // For SCL
                $tempODArray['scl_b_curve_od'] = $res['SclBcurveOD'];
                $tempODArray['scl_diameter_od'] = $res['SclDiameterOD'];
                $tempODArray['scl_sphere_od'] = $res['SclsphereOD'];
                $tempODArray['scl_cylinder_od'] = $res['SclCylinderOD'];
                $tempODArray['scl_add_od'] = $res['SclAddOD'];
                $tempODArray['scl_axis_od'] = $res['SclaxisOD'];
                $tempODArray['scl_dva_od'] = $res['SclDvaOD'];
                $tempODArray['scl_nva_od'] = $res['SclNvaOD'];
                $tempODArray['scl_dva_ou'] = $res['SclDvaOU'];
                $tempODArray['scl_nva_ou'] = $res['SclNvaOU'];
                $tempODArray['scl_type_od'] = $res['SclTypeOD'];
                $tempODArray['scl_type_od_id'] = $res['SclTypeOD_ID'];
                $tempODArray['elem_scl_od_drawing'] = $res['elem_SCLOdDrawing'];
                $tempODArray['hd_scl_od_drawing_original'] = $res['hdSCLOdDrawingOriginal'];
                $tempODArray['elem_scl_od_drawing_path'] = $res['elem_SCLOdDrawingPath'];
                $tempODArray['idoc_drawing_id_od'] = $res['idoc_drawing_id_od'];
                $tempODArray['cornea_scl_od_desc'] = $res['corneaSCL_od_desc'];
                //$worksheetArray[$worksheetId][] =  $tempODArray;
                
                $tempODArray['has_drawing_od'] = "";
                
                // For RGP
                $tempODArray['rgp_power_od'] = $res['RgpPowerOD'];
                $tempODArray['rgp_bc_od'] = $res['RgpBCOD'];
                $tempODArray['rgp_diameter_od'] = $res['RgpDiameterOD'];
                $tempODArray['rgp_oz_od'] = $res['RgpOZOD'];
                $tempODArray['rgp_ct_od'] = $res['RgpCTOD'];
                $tempODArray['rgp_color_od'] = $res['RgpColorOD'];
                $tempODArray['rgp_latitude_od'] = $res['RgpLatitudeOD'];
                $tempODArray['rgp_add_od'] = $res['RgpAddOD'];
                $tempODArray['rgp_dva_od'] = $res['RgpDvaOD'];
                $tempODArray['rgp_nva_od'] = $res['RgpNvaOD'];
                $tempODArray['rgp_type_od'] = $res['RgpTypeOD'];
                $tempODArray['rgp_type_od_id'] = $res['RgpTypeOD_ID'];
                $tempODArray['rgp_warranty_od'] = $res['RgpWarrantyOD'];
            
                // For custom RGP
                $tempODArray['rgp_custom_power_od'] = $res['RgpCustomPowerOD'];
                $tempODArray['rgp_custom_bc_od'] = $res['RgpCustomBCOD'];
                $tempODArray['rgp_custom_2_degree_od'] = $res['RgpCustom2degreeOD'];
                $tempODArray['rgp_custom_3_degree_od'] = $res['RgpCustom3degreeOD'];
                $tempODArray['rgp_custom_pcw_od'] = $res['RgpCustomPCWOD'];
                $tempODArray['rgp_custom_diameter_od'] = $res['RgpCustomDiameterOD'];
                $tempODArray['rgp_custom_oz_od'] = $res['RgpCustomOZOD'];
                $tempODArray['rgp_custom_ct_od'] = $res['RgpCustomCTOD'];
                $tempODArray['rgp_custom_color_od'] = $res['RgpCustomColorOD'];
                $tempODArray['rgp_custom_blend_od'] = $res['RgpCustomBlendOD'];
                $tempODArray['rgp_custom_edge_od'] = $res['RgpCustomEdgeOD'];
                $tempODArray['rgp_custom_latitude_od'] = $res[' RgpCustomLatitudeOD'];
                $tempODArray['rgp_custom_add_od'] = $res['RgpCustomAddOD'];
                $tempODArray['rgp_custom_dva_od'] = $res['RgpCustomDvaOD'];
                $tempODArray['rgp_custom_nva_od'] = $res['RgpCustomNvaOD'];
                $tempODArray['rgp_custom_type_od'] = $res['RgpCustomTypeOD'];
                $tempODArray['rgp_custom_type_od_id'] = $res['RgpCustomTypeOD_ID'];
                $tempODArray['rgp_custom_warranty_od'] = $res['RgpCustomWarrantyOD'];
                
                while($evalRes = imw_fetch_array($evalResult)){
                    $evalWSId = $evalRes['clws_id'];
                    if($wsId == $evalWSId){
                        $tempODArray['CL_SLC_evaluation_sphere_OD'] = $evalRes['CLSLCEvaluationSphereOD'];
                        $tempODArray['CL_SLC_evaluation_cylinder_OD'] = $evalRes['CLSLCEvaluationCylinderOD'];
                        $tempODArray['CL_SLC_evaluation_position_OD'] = $evalRes['CLSLCEvaluationPositionOD'];
                        $tempODArray['CL_SLC_evaluation_position_other_OD'] = $evalRes['CLSLCEvaluationPositionOtherOD'];
                        $tempODArray['CL_SLC_evaluation_axis_OD'] = $evalRes['CLSLCEvaluationAxisOD'];
                        $tempODArray['CL_SLC_evaluation_DVA_OD'] = $evalRes['CLSLCEvaluationDVAOD'];
                        $tempODArray['CL_SLC_evaluation_sphere_NVA_OD'] = $evalRes['CLSLCEvaluationSphereNVAOD'];
                        $tempODArray['CL_SLC_evaluation_cylinder_NVA_OD'] = $evalRes['CLSLCEvaluationCylinderNVAOD'];
                        $tempODArray['CL_SLC_evaluation_axis_NVA_OD'] = $evalRes['CLSLCEvaluationAxisNVAOD'];
                        $tempODArray['CL_SLC_evaluation_NVA_OD'] = $evalRes['CLSLCEvaluationNVAOD'];
                        $tempODArray['CL_SLC_evaluation_comfort_OD'] = $evalRes['CLSLCEvaluationComfortOD'];
                        $tempODArray['CL_SLC_evaluation_movement_OD'] = $evalRes['CLSLCEvaluationMovementOD'];
                        $tempODArray['CL_SLC_evaluation_condtion_OD'] = $evalRes['CLSLCEvaluationCondtionOD'];
                        $tempODArray['CL_SLC_evaluation_DVA_OU'] = $evalRes['CLSLCEvaluationDVAOU'];
                        $tempODArray['CL_SLC_evaluation_NVA_OU'] = $evalRes['CLSLCEvaluationNVAOU'];
                        
                        $tempODArray['CL_RGP_evaluation_sphere_OD'] = $evalRes['CLRGPEvaluationSphereOD'];
                        $tempODArray['CL_RGP_evaluation_cylinder_OD'] = $evalRes['CLRGPEvaluationCylinderOD'];
                        $tempODArray['CL_RGP_evaluation_axis_OD'] = $evalRes['CLRGPEvaluationAxisOD'];
                        $tempODArray['CL_RGP_evaluation_DVA_OD'] = $evalRes['CLRGPEvaluationDVAOD'];
                        $tempODArray['CL_RGP_evaluation_sphere_NVA_OD'] = $evalRes['CLRGPEvaluationSphereNVAOD'];
                        $tempODArray['CL_RGP_evaluation_cylinder_NVA_OD'] = $evalRes['CLRGPEvaluationCylinderNVAOD'];
                        $tempODArray['CL_RGP_evaluation_axis_NVA_OD'] = $evalRes['CLRGPEvaluationAxisNVAOD'];
                        $tempODArray['CL_RGP_evaluation_NVA_OD'] = $evalRes['CLRGPEvaluationNVAOD'];
                        $tempODArray['CL_RGP_evaluation_comfort_OD'] = $evalRes['CLRGPEvaluationComfortOD'];
                        $tempODArray['CL_RGP_evaluation_movement_OD'] = $evalRes['CLRGPEvaluationMovementOD'];
                        $tempODArray['CL_RGP_evaluation_pos_before_OD'] = $evalRes['CLRGPEvaluationPosBeforeOD'];
                        $tempODArray['CL_RGP_evaluation_pos_before_other_OD'] = $evalRes['CLRGPEvaluationPosBeforeOtherOD'];
                        $tempODArray['CL_RGP_evaluation_pos_after_OD'] = $evalRes['CLRGPEvaluationPosAfterOD'];
                        $tempODArray['CL_RGP_evaluation_pos_after_other_OD'] = $evalRes['CLRGPEvaluationPosAfterOtherOD'];
                        $tempODArray['CL_RGP_evaluation_fluorescein_pattern_OD'] = $evalRes['CLRGPEvaluationFluoresceinPatternOD'];
                        $tempODArray['CL_RGP_evaluation_inverted_OD'] = $evalRes['CLRGPEvaluationInvertedOD'];
                        $tempODArray['evaluation_rotation_OD'] = $evalRes['EvaluationRotationOD'];
                    }
                }
                $odArray[] = $tempODArray;
            }
        }
    }

    //$worksheetArray[$worksheetId]['OD'] = $odArray;
    return $odArray;
}

function getOSs($result, $wsId, $evalResult){
    $osArray = array();
    mysqli_data_seek($result, 0);
    mysqli_data_seek($evalResult, 0);
    while($res = imw_fetch_array($result))
    {
        $clWSId = $res['clws_id'];          // Get workseet id
        if($wsId == $clWSId)
        {
            $clEye = $res['clEye'];              // Whether column is 'OD' or 'OS'
            if($clEye == 'OS')              // If column is OS
            {
                $tempOSArray = array();
                $tempOSArray['os_id'] = $res['id'];
                $tempOSArray['cl_type'] = $res['clType'];
                // For SCL
                $tempOSArray['scl_b_curve_os'] = $res['SclBcurveOS'];
                $tempOSArray['scl_diameter_os'] = $res['SclDiameterOS'];
                $tempOSArray['scl_sphere_os'] = $res['SclsphereOS'];
                $tempOSArray['scl_cylinder_os'] = $res['SclCylinderOS'];
                $tempOSArray['scl_add_os'] = $res['SclAddOS'];
                $tempOSArray['scl_axis_os'] = $res['SclaxisOS'];
                $tempOSArray['scl_dva_os'] = $res['SclDvaOS'];
                $tempOSArray['scl_nva_os'] = $res['SclNvaOS'];
                $tempOSArray['scl_dvaou_os'] = $res['SclDvaOU'];
                $tempOSArray['scl_type_os'] = $res['SclTypeOS'];
                $tempOSArray['scl_type_os_id'] = $res['SclTypeOS_ID'];
                $tempOSArray['elem_scl_os_drawing'] = $res['elem_SCLOsDrawing'];
                $tempOSArray['hd_scl_os_drawing_original'] = $res['hdSCLOsDrawingOriginal'];
                $tempOSArray['elem_scl_os_drawing_path'] = $res['elem_SCLOsDrawingPath'];
                $tempOSArray['idoc_drawing_id_os'] = "";
                $tempOSArray['cornea_scl_os_desc'] = $res['corneaSCL_os_desc'];
                $tempOSArray['has_drawing_os'] = "";
                //$worksheetArray[$worksheetId][] =  $tempODArray;
                
                // For RGP
                $tempOSArray['rgp_power_os'] = $res['RgpPowerOS'];
                $tempOSArray['rgp_bc_os'] = $res['RgpBCOS'];
                $tempOSArray['rgp_diameter_os'] = $res['RgpDiameterOS'];
                $tempOSArray['rgp_oz_os'] = $res['RgpOZOS'];
                $tempOSArray['rgp_ct_os'] = $res['RgpCTOS'];
                $tempOSArray['rgp_color_os'] = $res['RgpColorOS'];
                $tempOSArray['rgp_latitude_os'] = $res['RgpLatitudeOS'];
                $tempOSArray['rgp_add_os'] = $res['RgpAddOS'];
                $tempOSArray['rgp_dva_os'] = $res['RgpDvaOS'];
                $tempOSArray['rgp_nva_os'] = $res['RgpNvaOS'];
                $tempOSArray['rgp_type_os'] = $res['RgpTypeOS'];
                $tempOSArray['rgp_type_os_id'] = $res['RgpTypeOS_ID'];
                $tempOSArray['rgp_warranty_os'] = $res['RgpWarrantyOS'];
                
                // For custom RGP
                $tempOSArray['rgp_custom_power_os'] = $res['RgpCustomPowerOS'];
                $tempOSArray['rgp_custom_bc_os'] = $res['RgpCustomBCOS'];
                $tempOSArray['rgp_custom_2_degree_os'] = $res['RgpCustom2degreeOS'];
                $tempOSArray['rgp_custom_3_degree_os'] = $res['RgpCustom3degreeOS'];
                $tempOSArray['rgp_custom_pcw_os'] = $res['RgpCustomPCWOS'];
                $tempOSArray['rgp_custom_diameter_os'] = $res['RgpCustomDiameterOS'];
                $tempOSArray['rgp_custom_oz_os'] = $res['RgpCustomOZOS'];
                $tempOSArray['rgp_custom_ct_os'] = $res['RgpCustomCTOS'];
                $tempOSArray['rgp_custom_color_os'] = $res['RgpCustomColorOS'];
                $tempOSArray['rgp_custom_blend_os'] = $res['RgpCustomBlendOS'];
                $tempOSArray['rgp_custom_edge_os'] = $res['RgpCustomEdgeOS'];
                $tempOSArray['rgp_custom_latitude_os'] = $res['RgpCustomLatitudeOS'];
                $tempOSArray['rgp_custom_add_os'] = $res['RgpCustomAddOS'];
                $tempOSArray['rgp_custom_dva_os'] = $res['RgpCustomDvaOS'];
                $tempOSArray['rgp_custom_nva_os'] = $res['RgpCustomNvaOS'];
                $tempOSArray['rgp_custom_type_os'] = $res['RgpCustomTypeOS'];
                $tempOSArray['rgp_custom_type_os_id'] = $res['RgpCustomTypeOS_ID'];
                $tempOSArray['rgp_custom_warranty_os'] = $res['RgpCustomWarrantyOS'];
                while($evalRes = imw_fetch_array($evalResult)){
                    $evalWSId = $evalRes['clws_id'];
                    if($wsId == $evalWSId){
                        $tempOSArray['CL_SLC_evaluation_sphere_OS'] = $evalRes['CLSLCEvaluationSphereOS'];
                        $tempOSArray['CL_SLC_evaluation_cylinder_OS'] = $evalRes['CLSLCEvaluationCylinderOS'];
                        $tempOSArray['CL_SLC_evaluation_position_OS'] = $evalRes['CLSLCEvaluationPositionOS'];
                        $tempOSArray['CL_SLC_evaluation_position_other_OS'] = $evalRes['CLSLCEvaluationPositionOtherOS'];
                        $tempOSArray['CL_SLC_evaluation_axis_OS'] = $evalRes['CLSLCEvaluationAxisOS'];
                        $tempOSArray['CL_SLC_evaluation_DVA_OS'] = $evalRes['CLSLCEvaluationDVAOS'];
                        $tempOSArray['CL_SLC_evaluation_sphere_NVA_OS'] = $evalRes['CLSLCEvaluationSphereNVAOS'];
                        $tempOSArray['CL_SLC_evaluation_cylinder_NVA_OS'] = $evalRes['CLSLCEvaluationCylinderNVAOS'];
                        $tempOSArray['CL_SLC_evaluation_axis_NVA_OS'] = $evalRes['CLSLCEvaluationAxisNVAOS'];
                        $tempOSArray['CL_SLC_evaluation_NVA_OS'] = $evalRes['CLSLCEvaluationNVAOS'];
                        $tempOSArray['CL_SLC_evaluation_comfort_OS'] = $evalRes['CLSLCEvaluationComfortOS'];
                        $tempOSArray['CL_SLC_evaluation_movement_OS'] = $evalRes['CLSLCEvaluationMovementOS'];
                        $tempOSArray['CL_SLC_evaluation_condtion_OS'] = $evalRes['CLSLCEvaluationCondtionOS'];
                
                        $tempOSArray['CL_RGP_evaluation_sphere_OD'] = $evalRes['CLRGPEvaluationSphereOD'];
                        $tempOSArray['CL_RGP_evaluation_cylinder_OD'] = $evalRes['CLRGPEvaluationCylinderOD'];
                        $tempOSArray['CL_RGP_evaluation_axis_OD'] = $evalRes['CLRGPEvaluationAxisOD'];
                        $tempOSArray['CL_RGP_evaluation_DVA_OD'] = $evalRes['CLRGPEvaluationDVAOD'];
                        $tempOSArray['CL_RGP_evaluation_sphere_NVA_OD'] = $evalRes['CLRGPEvaluationSphereNVAOD'];
                        $tempOSArray['CL_RGP_evaluation_cylinder_NVA_OD'] = $evalRes['CLRGPEvaluationCylinderNVAOD'];
                        $tempOSArray['CL_RGP_evaluation_axis_NVA_OD'] = $evalRes['CLRGPEvaluationAxisNVAOD'];
                        $tempOSArray['CL_RGP_evaluation_NVA_OD'] = $evalRes['CLRGPEvaluationNVAOD'];
                        $tempOSArray['CL_RGP_evaluation_comfort_OD'] = $evalRes['CLRGPEvaluationComfortOD'];
                        $tempOSArray['CL_RGP_evaluation_movement_OD'] = $evalRes['CLRGPEvaluationMovementOD'];
                        $tempOSArray['CL_RGP_evaluation_pos_before_OD'] = $evalRes['CLRGPEvaluationPosBeforeOD'];
                        $tempOSArray['CL_RGP_evaluation_pos_before_other_OD'] = $evalRes['CLRGPEvaluationPosBeforeOtherOD'];
                        $tempOSArray['CL_RGP_evaluation_pos_after_OD'] = $evalRes['CLRGPEvaluationPosAfterOD'];
                        $tempOSArray['CL_RGP_evaluation_pos_after_other_OD'] = $evalRes['CLRGPEvaluationPosAfterOtherOD'];
                        $tempOSArray['CL_RGP_evaluation_fluorescein_pattern_OD'] = $evalRes['CLRGPEvaluationFluoresceinPatternOD'];
                        $tempOSArray['CL_RGP_evaluation_inverted_OD'] = $evalRes['CLRGPEvaluationInvertedOD'];
                
                        $contactLensEvaluationArray[$clWSId]['evaluation_rotation_OD'] = $evalRes['EvaluationRotationOD'];
                    }
                }
                $osArray[] = $tempOSArray;
            }
        }
    }
    return $osArray;
}

function getWorksheetArray($result){
    $finalArray = array();
    while($res = imw_fetch_array($result)){
        $worksheetArray = array();
        $worksheetId = $res['clws_id'];              // Get worksheet id from result set
        $worksheetArray['worksheet_id'] = $worksheetId;
        $worksheetArray['provider_id'] = $res['provider_id'];
        $worksheetArray['cl_type'] = $res['clType'];         // Contact lens type
        $worksheetArray['cl_ws_id'] =
        $worksheetArray['dos'] = $res['dos'];
        $worksheetArray['cl_ws_saved_time'] = $res['clws_savedatetime'];     // Contact lens worksheet saved time
        $worksheetArray['cl_ws_type'] = $res['clws_type'];                  // Contact lens worksheet type
        $worksheetArray['cl_ws_trial_number'] = $res['clws_trial_number'];       // Contact lens worksheet trial number
        $worksheetArray['current_ws_id'] = $res['currentWorksheetid'];       // Current worksheet id
        $worksheetArray['average_wear_time'] = $res['AverageWearTime'];         // Lens average wear time
        $worksheetArray['solutions'] = $res['Solutions'];         // Solutions
        $worksheetArray['age'] = $res['Age'];         // Age
        $worksheetArray['disposable_schedule'] = $res['DisposableSchedule'];
        $worksheetArray['form_id'] = $res['form_id'];
        $worksheetArray['del_status'] = $res['del_status'];
        $worksheetArray['charges_id'] = $res['charges_id'];
        $worksheetArray['worksheet_date'] = $res['worksheetdate'];
        $worksheetArray['cl_comment'] = $res['cl_comment'];
        $worksheetArray['cpt_evaluation_fit_refit'] = $res['cpt_evaluation_fit_refit'];
        $worksheetArray['usage_val'] = $res['usage_val'];
        $worksheetArray['all_around'] = $res['allaround'];;
        $worksheetArray['wear_scheduler'] = $res['wear_scheduler'];
        $worksheetArray['replenishment'] = $res['replenishment'];
        $worksheetArray['disinfecting'] = $res['disinfecting'];
        $worksheetArray['prosthesis'] = "";
        $worksheetArray['prosthesis_val'] = "";
        $worksheetArray['no_cl'] = "";
        $worksheetArray['no_cl_val'] = "";
        $finalArray["$worksheetId"] = $worksheetArray;
    }
    return $finalArray;
}

function getExistingWorksheet($status, $wsId, $firstTime){
    $query = "";
    if($firstTime == true){        // If popup is opened for first time
        $query = "select cl.*, DATE_FORMAT(dos, '%d-%m-%Y') as 'dos', DATE_FORMAT(cl.clws_savedatetime, '%d-%m-%Y') AS worksheetdate, cl_det.* FROM contactlensmaster cl " .
                "LEFT JOIN contactlensworksheet_det cl_det ON cl_det.clws_id = cl.clws_id WHERE cl.patient_id='".$_SESSION['patient']."' AND cl.clws_id='".$wsId."'";
    } else{             // If request is for getting worksheets by status
        $query = "Select cl.*, DATE_FORMAT(dos, '%d-%m-%Y') as 'dos', DATE_FORMAT(cl.clws_savedatetime , '%d-%m-%Y')".
                " AS worksheetdate, cl_det.* FROM contactlensmaster cl LEFT JOIN contactlensworksheet_det cl_det ON cl_det.clws_id = cl.clws_id WHERE".
                " cl.patient_id='".$_SESSION['patient']."'";
        if($status == 'deleted'){                   // If request is for deleted worksheets
            $query .= " and cl.del_status='1'";     // Set 'del_status' to 1
        }  else if($status == 'undeleted'){          // If request is for undeleted worksheets
            $query .= " and cl.del_status='0'";     // Set 'del_status' to 0
        } else if($status != 'all'){
            $query .= " and cl.clws_id IN(".$wsId.")";
        }
    }
    $query .= " ORDER BY cl.clws_id ASC, cl_det.clEye, cl_det.id ASC";
    //return $query;
    $rowCount = imw_num_rows($query);
    $result = imw_query($query);
    $worksheetArray = getWorksheetArray($result);
    //return $worksheetArray;
 
    $evalQuery = "select * from contactlens_evaluations cl_eval where clws_id IN(".implode(array_keys($worksheetArray), ",").")";
    $evalResult = imw_query($evalQuery);
    
    $worksheetIdKeysArray = array_keys($worksheetArray);
    for($i = 0;$i < sizeof($worksheetIdKeysArray);$i++)
    {
        $tmpWorksheetId = $worksheetIdKeysArray[$i];
        $worksheetArray["$tmpWorksheetId"]['OD'] = getODs($result, $tmpWorksheetId, $evalResult);
        $worksheetArray["$tmpWorksheetId"]['OS'] = getOSs($result, $tmpWorksheetId, $evalResult);
    }
    return json_encode($worksheetArray);
}

function deleteLensBlock($lensBlockId, $mode){
    if($mode == "delete"){
        imw_query("Update contactlensmaster SET del_status='1' WHERE clws_id IN(".$lensBlockId.")");
        return "Update contactlensmaster SET del_status='1' WHERE clws_id IN(".$lensBlockId.")";
    }else if($mode == "undelete"){
        imw_query("Update contactlensmaster SET del_status='0' WHERE clws_id IN(".$lensBlockId.")");
    }
}

function saveContactLensSheet($POST){
    /* $existingArray = json_decode($POST['existing']);
    //return $existingArray;
    foreach($existingArray as $key){
        $formId = $key->form_id;
        $blockId = $key->block_id;
        $clReq = $key->clreq;
        $dos = $key->dos;
        $averageWearTime = $key->average_wear_time;
        $solutions = $key->solutions;
        $age = $key->age;
        $disposableSchedule = $key->disposable_schedule;
        $allAround = $key->all_around;
        $usageVal = $key->usage_val;
        $providerId = $key->provider_id;
        $cptEvaluationFitRefit = $key->cpt_evaluation_fit_refit;
        $comments = $key->comments;
        $replenishment = $key->replenishment;
        $disinfecting = $key->disinfecting;
        $wearScheduler = $key->wear_scheduler;
        $clwsTrial = $key->clws_trial;
        $clwsType = $key->clws_type;
        //imw_query("UPDATE chart_master_table SET cl_order = '".$clReq."' WHERE id = '".$formId."'");        // Update cl_order in chart_master_table
        $query = "update contactlensmaster set provider_id='".$providerId."', dos='".$dos."', clws_savedatetime='".date('Y-m-d H:i:s')."', clGrp='OU', clws_type='".$clwsType."', clws_trial_number='".$clwsTrial."', cpt_evaluation_fit_refit='".$cptEvaluationFitRefit."', cl_comment='".$comments."', charges_id='', AverageWearTime='".$averageWearTime."', Solutions='".$solutions."', Age='".$age."', DisposableSchedule='".$disposableSchedule."', usage_val='".$usageVal."', allaround='".$allAround."', wear_scheduler='".$wearScheduler."', replenishment='".$replenishment."', disinfecting='".$disinfecting."', CLHXDOS='".$dos."' where clws_id='".$blockId."'";
        // Execute query
        
        // Extract OD
        foreach($key->OD as $odKey){
            $odId = explode("_", $odKey->od_id)[1];
            $make = $odKey->make;
            $bc = $odKey->bc;
            $diameter = $odKey->diameter;
            $opticalZone = $odKey->optical_zone;
            $centerThickness = $odKey->center_thickness;
            $spherePower = $odKey->sphere_power;
            $cylinder = $odKey->cylinder;
            $axis = $odKey->axis;
            $_2w = $odKey->_2w;
            $_3w = $odKey->_3w;
            $pcw = $odKey->pcw;
            $color = $odKey->color;
            $blend = $odKey->blend;
            $edge = $odKey->edge;
            $add = $odKey->add;
            $dva = $odKey->dva;
            $dvaOU = $odKey->dvaou;
            $nva = $odKey->nva;
            $nvaOU = $odKey->nvaou;
            $clType = $odKey->cl_type;
            if($clType == "scl"){
                $query = "update contactlensworksheet_det set 
                        SclBcurveOD='".$bc."',
                        SclDiameterOD='".$diameter."',
                        SclsphereOD='".$spherePower."',
                        SclCylinderOD='".$cylinder."',
                        SclaxisOD='".$axis."',
                        SclAddOD='".$add."',
                        SclDvaOD='".$dva."',
                        SclNvaOD='".$nva."',
                        SclTypeOD='',
                        idoc_drawing_id='',
                        corneaSCL_od_desc='',
                        corneaSCL_os_desc='',
                        SclNvaOU='".$nvaOU."',
                        SclDvaOU='".$dvaOU."',
                        clws_id='".$blockId."',
                        clEye='OD',
                        clType='scl' where id='".$odId."'";
            }else if($clType == "rgp"){
                $query = "update contactlensworksheet_det set
                        RgpBCOD='".$bc."',
                        RgpDiameterOD='".$diameter."',
                        RgpPowerOD='".$spherePower."',
                        RgpCylinderOD='".$cylinder."',
                        RgpAxisOD='".$axis."',
                        RgpAddOD='".$add."',
                        RgpDvaOD='".$dva."',
                        RgpNvaOD='".$nva."',
                        idoc_drawing_id='',
                        corneaSCL_od_desc='',
                        corneaSCL_os_desc='',
                        SclNvaOU='',
                        SclDvaOU='',
                        clws_id='".$blockId."',
                        clEye='OD',
                        clType='rgp' where id='".$odId."'";
            }else if($clType == "cust_rgp"){
                $query = "Update contactlensworksheet_det SET
                RgpCustomBCOD='".$bc."',
                RgpCustomDiameterOD='".$diameter."',
                RgpCustomCylinderOD='".$cylinder."',
                RgpCustomAxisOD='".$axis."',
                RgpCustomOZOD='AE RE',
                RgpCustomCTOD='',
                RgpCustomPowerOD='15',
                RgpCustom2degreeOD='15',
                RgpCustom3degreeOD='51',
                RgpCustomPCWOD='43',
                RgpCustomColorOD='Green',
                RgpCustomBlendOD='Medium',
                RgpCustomEdgeOD='41',
                RgpCustomAddOD='23',
                RgpCustomDvaOD='20/4',
                RgpCustomNvaOD='20/2',
                RgpCustomTypeOD='Cooper Biofinity Monthly-6 Pack',
                RgpCustomTypeOD_ID='601',
                idoc_drawing_id='',
                corneaSCL_od_desc='',
                corneaSCL_os_desc='',
                SclNvaOU='',
                SclDvaOU='', clws_id='".$blockId."', clEye='OD', clType='cust_rgp'  WHERE id='".$odId."'";
            }
        }
        return $query;
    }
    $newArray = json_decode($POST['existing']);*/
}
?>
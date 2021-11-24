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
//print_r($_REQUEST);
include_once("../../config/globals.php");
if(isset($_REQUEST['referer']) && $_REQUEST['referer']='wv'){
	$pagereferer = 'wv';
	//include_once("common/simpleMenu_2.php");
}else{
	$pagereferer = '';
	//include_once("common/simpleMenu.php");	
}
//include_once("common/functions.php");
//include_once("../main/main_functions.php");
//include_once("../main/chartNotesPrinting.php");
//include_once("common/menu_data.php");
include_once("common/cl_functions.php");
//include_once(dirname(__FILE__)."/common/session_chart_view_access.php");

$cylSign=$GLOBALS["def_cylinder_sign_cl"];
//Function --

function getSimpleMenu(){ }

function cl_getMenu($nm,$t){

	global $arrAcuitiesMrDis,$strAcuitiesMrDisString,
			$arrAcuitiesNear,$strAcuitiesNearString,
			$ColorOptionsArray, $ColorOptionsString,
			$BlendOptionsArray, $BlendOptionsString,
			$pagereferer;

	if($t=="Dis"){ //Distance		
		$arrM = $arrAcuitiesMrDis;
		$varM = "menu_acuitiesMrDis";
		$strM = $strAcuitiesMrDisString;
	
	}else if($t=="Nr"){ //Near
		$arrM = $arrAcuitiesNear;
		$varM = "menu_acuitiesNear";
		$strM = $strAcuitiesNearString;
	
	}else if($t=="Clr"){ //Color
		$arrM = $ColorOptionsArray;
		$varM = "menu_clcolorOpts";
		$strM = $ColorOptionsString;
	
	}else if($t=="Bd"){ //BlendOptions
		$arrM = $BlendOptionsArray;
		$varM = "menu_clblendOpts";
		$strM = $BlendOptionsString;
	}

 
	echo getSimpleMenu($arrM,$varM,$nm,0,0,array("pdiv"=>"divWorkView"));	
}

//Function --

function getColorsFromDB(){
	$clColorResult = imw_query("select color_name from contactlensecolor");
	$clColorArray = array();
	while($clColorRow = imw_fetch_assoc($clColorResult)){
		$clColorArray[] = $clColorRow['color_name'];
	}
	return $clColorArray;
}

function getCLMenuArray($menu){
	$clColorArray = array();
    if($menu == 'dva'){
        return array('20/15', '20/20', '20/25', '20/30', '20/40', '20/50', '20/60', '20/70', '20/80', '20/100', '20/150', '20/200', '20/300', '20/400', '20/600', '20/800', 'CF', 'CF 1ft', 'CF 2ft', 'CF 3ft', 'CF 4ft', 'CF 5ft', 'CF 6ft', 'HM', 'LP', 'LP c p', 'LP s p', 'NLP', 'F&F', 'F/(F)', '2/200', 'CSM', 'Enucleation', 'Prosthetic', 'Pt Uncoopera', 'Unable', '5/200');
    }else if($menu == 'nva'){
        return array('20/20(J1+)', '20/25(J1)', '20/30(J2)', '20/40(J3)', '20/32(J4)', '20/50(J5)', '20/60(J6)', '20/70(J7)', '20/63(J8)', '20/80', '20/100(J10)', '20/200(J16)', '20/400', '20/800', 'APC 20/30', 'APC 20/40', 'APC 20/60', 'APC 20/80', 'APC 20/100', 'APC 20/160', 'APC 20/200', 'CSM', '(C)SM', 'C(S)M', 'CS(M)', 'C(S)(M)', '(C)(S)M', '(C)S(M)', '(C)(S)(M)', 'F&F', 'Unable');
    }else if($menu == 'color'){
        return getColorsFromDB();
    }else if($menu == 'blend'){
        return array('Light', 'Medium', 'Heavy');
    }
    return array('');
}

function getMenus($menu, $element)
{
    $menuString = "";
    $menuString .= '<div class="input-group-btn menu menu_acuitiesMrDis">';
    $menuString .= '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-trgt-id="'.$element.'" tabindex="-1"><span class="caret"></span></button>';
    $menuString .= '<ul class="dropdown-menu  dropdown-menu-right">';
    $menuArray = getCLMenuArray($menu);
    for($i = 0; $i < sizeof($menuArray);$i++){
        $menuString .= "<li><a href=\"javascript:void(0);\" onclick=\"selectItemFromMenu('".$menuArray[$i]."', '".$element."');\" data-val='".$menuArray[$i]."'>".$menuArray[$i]."</a></li>";
    }
    $menuString .= '</ul>';
    $menuString .= '</div>';
    return $menuString;
}

$rowCols = $_REQUEST['rowCols'];
$oldNum = $_REQUEST['rowNum'];
$rowName = $_REQUEST['rowName'];
$totRows = $_REQUEST['totRows'];   // In case of Change Row
$showTitle = $_REQUEST['showTitle'];

//---------SET SIMPLE MENU DROP DOWN VALUES-------------
$sqlManufact = "Select distinct(manufacturer) from contactlensemake order by 
		`contactlensemake`.`manufacturer` ASC";
$resManuf = imw_query($sqlManufact);
$arrSubOptions = array();	$strLen =0;	$DDMenuWidth = 120;
if(imw_num_rows($resManuf) > 0){
	while($rowManuf = imw_fetch_array($resManuf)){
		$manufacturer = $rowManuf['manufacturer'];
		$sqlStyle = "select distinct(style), make_id,type, del_status, source, base_curve, diameter, manufacturer from contactlensemake where manufacturer = '$manufacturer' 
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
			
			//ARRAY FOR TYPEAHEAD
			if($rowStyle['del_status']=='0' && ($rowStyle['source']=='0' || $rowStyle['source']=='')){
				$styleType = ''; $sep='';
				if($rowStyle['manufacturer']!=''){ $styleType = $rowStyle['manufacturer']; $sep='-';}
				if($rowStyle['style']!=''){ $styleType.=$sep.$rowStyle['style']; $sep='-';}
				if($rowStyle['type']!=''){ $styleType.=$sep.$rowStyle['type']; }

				$arrManufac[]=$styleType;
				$arrManufacId[]=$rowStyle['make_id'];
				$arrManufacInfo[$rowStyle['make_id']]=$rowStyle['base_curve'].'~'.$rowStyle['diameter'];
			}
		}
		$arrMainValues[] = array($manufacturer,$arrSubOptions);
		$manufacturer = '';unset($arrSubOptions);
	}
}	

json_encode($arrManufac);
json_encode($arrManufacId);
json_encode($arrManufacInfo);

if($strLen > 60) { $simpleMenuWidth = 350; }
$stringAllManufact = substr($stringAllManufact,0,-1);
//------------------------------------------

$divHeight = "45px"; $rgpHeight = "48px";

$topPad = "padding-top:22px";

if($_REQUEST['mode']=='change')
{
	$i = $oldNum;
	
/*	if($showTitle ==1)
	{ $divHeight = "45px"; $rgpHeight = "48px";	}
	else { $divHeight = "30px"; $rgpHeight = "30px"; }
*/	
	$divHeight = "45px"; $rgpHeight = "48px";
	
}else{
	$i = $oldNum +1;
}

if($_REQUEST['odos']=='od'){
	$txtBox = 'txtTotOD';
	$DDName = 'clTypeOD';
}else if($_REQUEST['odos']=='os'){
	$txtBox = 'txtTotOS';
	$DDName = 'clTypeOS';
}else if($_REQUEST['odos']=='ou'){
	$txtBox = 'txtTotOU';
	$DDName = 'clTypeOU';
}

//
$isHTML5OK = isHtml5OK();

?>
<script type="text/javascript">
	var arrManufac=[];
	var arrManufacId=[];
	var arrManufacInfo=[];
	
	arrManufac = <?php echo json_encode($arrManufac); ?>;
	arrManufacId = <?php echo json_encode($arrManufacId); ?>;
	arrManufacInfo = <?php echo json_encode($arrManufacInfo); ?>;
</script>

<?php

//if($_REQUEST['mode'] == '') {		// IF NOT SCL or RGP Selected just new Row Adding
if($_REQUEST['addType']=='scl' || $_REQUEST['addType']=='prosthesis' || $_REQUEST['addType']=='no-cl'){
?>

<div id="<?php echo $rowName.$i;?>" style="display:block; clear:both; height:<?php echo $divHeight;?>;" class="scl_bg">
				<div style="float:left;width:7%; <?php echo $topPad;?>;">
                  <select name="<?php echo $DDName.$i;?>" id="<?php echo $DDName.$i;?>" class="form-control minimal" onChange="changeCLType(this);changeRow(this.value, '<?php echo $_REQUEST['odos'];?>', '<?php echo $rowName;?>', document.getElementById('<?php echo $txtBox;?>').value, '<?php echo $i;?>',arrManufac, arrManufacId, arrManufacInfo);" style="width:100%;">
                    <option value="scl" <?php if($_REQUEST['addType']=='scl')echo 'selected';?> >SCL</option>
					<option value="rgp_soft">RGP Soft</option>
					<option value="rgp_hard">RGP Hard</option>
                    <option value="cust_rgp">Custom RGP</option>
                    <option value="prosthesis" <?php if($_REQUEST['addType']=='prosthesis')echo 'selected';?>>Prosthesis</option>
                    <option value="no-cl" <?php if($_REQUEST['addType']=='no-cl')echo 'selected';?>>No-CL</option>
                  </select>
               </div>
               <div style="width:91%;float:left;">
				<table class="table borderless" id="sclTable" style="width:99%;">
           	    	<tr>
           	      	  <td class="txt_10b valignBottom" style="width:3%;"></td>
    				  <td style="width:9%;">B. Curve</td>
    				  <td class="txt_11b alignLeft" style="width:8%;">Diameter</td>
    				  <td class="txt_11b alignLeft" style="width:8%;">Sphere</td>
    				  <td class="txt_11b nowrap alignLeft" style="width:8%;">Cylinder</td>
    				  <td class="txt_11b alignLeft" style="width:8%;">Axis</td>
    				  <td class="txt_11b alignLeft" style="width:8%;">Color</td>
    				  <td class="txt_11b alignLeft" style="width:8%;">ADD</td>
    				  <td class="txt_11b alignLeft nowrap" style="width:8%;">DVA</td>
    				  <td class="txt_11b alignLeft nowrap" style="width:8%;">NVA</td>
    				  <td class="txt_11b alignLeft nowrap" style="width:22%;">Type</td>
    				  <td class="txt_11b alignLeft nowrap" style="width:2%;"></td>
					</tr>
                 <?php 
				 if($_REQUEST['odos']=='od'){ ?>
				 <tr class="alignLeft">
    			    <td class="odcol txt_10b">OD</td>
                    <td>
    	              <input class="form-control" type="text" id="SclBcurveOD<?php echo $i;?>" name="SclBcurveOD<?php echo $i;?>" value="<?php if($SclBcurveOD<>""){echo $SclBcurveOD;}?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('SclBcurveOD<?php echo $i;?>','SclBcurveOS<?php echo $i;?>');">
    	            </td> 
                    <td class="txt_11b alignLeft">
                      <input  id="SclDiameterOD<?php echo $i;?>" type="text" class="form-control" name="SclDiameterOD<?php echo $i;?>" value="<?php echo $SclDiameterOD;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('SclDiameterOD<?php echo $i;?>','SclDiameterOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">
                    </td>
                    <td class="txt_11b alignLeft">
					<input type="text" name="SclsphereOD<?php echo $i;?>" value="<?php if($SclsphereOD!=""){echo $SclsphereOD;}?>" class="form-control" style="width:100%;" id="SclsphereOD<?php echo $i;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"/>
                    </td>  
                    <td class="txt_11b nowrap alignLeft">
                      <input  id="SclCylinderOD<?php echo $i;?>" type="text" name="SclCylinderOD<?php echo $i;?>" value="<?php if($SclCylinderOD!="" ){echo $SclCylinderOD;}?>" class="form-control"  style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">                    
                    </td> 
                    <td class="txt_11b alignLeft">
					<input type="text" name="SclaxisOD<?php echo $i;?>" value="<?php if($SclaxisOD!=""){echo $SclaxisOD;}?>" class="form-control" style="width:100%;" id="SclaxisOD<?php echo $i;?>"  style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>', 'noDecimal');" onKeyUp="check2BlurCL(this,'A','','<?php echo $rowName.$i;?>');">                    
                    </td>
                    <td class="txt_11b alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="SclColorOD<?php echo $i;?>"  id="SclColorOD<?php echo $i;?>" value="<?php if($SclColorOD) echo $SclColorOD; ?>" class="form-control" onblur="this.click();" >
						<?php // cl_getMenu("RgpColorOD".$i,"Clr"); ?>
						<?php echo getMenus("color", "SclColorOD".$i); ?>
					</div>                    
                    </td>
                    <td class="txt_11b alignLeft" >
                    <input  id="SclAddOD<?php echo $i;?>" type="text" class="form-control" name="SclAddOD<?php echo $i;?>" value="<?php echo $SclAddOD;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('SclAddOD<?php echo $i;?>','SclAddOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>' );">
                    </td>
                    <td class="txt_11b alignLeft nowrap">
                        <div class="input-group" style="width:100%;">
                        	<input id="SclDvaOD<?php echo $i;?>" type="text" name="SclDvaOD<?php echo $i;?>" value="<?php if($SclDvaOD) echo $SclDvaOD; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); this.click();" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">
    						<?php // cl_getMenu("SclDvaOD".$i,"Dis"); ?>
    						<?php echo getMenus("dva", "SclDvaOD".$i); ?>
    		    		</div>
                    </td> 
                    <td class="txt_11b alignLeft nowrap">
                    	<div class="input-group" style="width:100%;">
    						<input type="text" name="SclNvaOD<?php echo $i;?>" id="SclNvaOD<?php echo $i;?>" value="<?php if($SclNvaOD) echo $SclNvaOD; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
    						<?php // cl_getMenu("SclNvaOD".$i,"Nr"); ?>
    						<?php echo getMenus("nva", "SclNvaOD".$i); ?>
						</div>
                    </td> 
                    <td class="txt_11b alignLeft nowrap">
                        <div class="alignLeft" style="width:100%;float:left;">
                            <input type="text" name="SclTypeOD<?php echo $i;?>" id="SclTypeOD<?php echo $i;?>" class="typeAhead form-control" style="width:100%; background-image:none;" value="" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"/>
    						<input type="hidden" name="SclTypeOD<?php echo $i;?>ID" id="SclTypeOD<?php echo $i;?>ID" value="" funVars="worksheet~SclTypeOD<?php echo $i;?>~SclTypeOS<?php echo $i;?>~SclBcurveOD<?php echo $i;?>~SclDiameterOD<?php echo $i;?>">
                        </div>
                    </td>
                    <td class="alignLeft nowrap">
                    <?php
                        /*echo "i: " . $i;
                        //echo "addType: ".$_REQUEST['addType']."  ".$i."   ".$totRows."";
                        if($i > 1){         // If new row added
                            // Show add icon
                    ?>
                            <figure>
                            	<span id='imgOD<?php echo $i; ?>' class='glyphicon glyphicon-plus' onClick="addNewRow(dgi('<?php echo $rowName.$i; ?>').value, 'od', '<?php echo $rowName;?>', 'imgOD', '<?php echo $i; ?>', '10','', arrManufac, arrManufacId, arrManufacInfo);" data-toggle='tooltip' title='Add row' data-original-title='Add row'></span>
                            </figure>
                            <script type="text/javascript">
                            	$("#imgOD" + <?php echo ($i - 1); ?>).removeClass('glyphicon-plus');
                            	$("#imgOD" + <?php echo ($i - 1); ?>).addClass('glyphicon-remove');
                            	$("#imgOD" + <?php echo ($i - 1); ?>).attr('data-original-title', 'Delete');
                            	$("#imgOD" + <?php echo ($i - 1); ?>).attr('onclick','removeTableRow("<?php echo $rowName.($i - 1); ?>")');
                            </script>
					<?php
                        }*/
                    ?>
                    <?php if(($_REQUEST['mode'] =='change' && $i==1 && $totRows >1) || ($_REQUEST['mode'] =='change' && $i!=$totRows)) {  ?>
					<figure><span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('<?php echo $rowName.$i;?>');" data-toggle="tooltip" title="Delete Row" data-original-title="Delete Row"></span></figure>
                    <?php }else { ?>
					<figure><span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('<?php echo $DDName.$i;?>').value, 'od', '<?php echo $rowName;?>', 'imgOD', '<?php echo $i;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-toggle="tooltip" title="Add More" data-original-title="Add More"></span></figure>                     
                    <?php } ?>
                    </td>
                    </tr>

					<?php }
					if($_REQUEST['odos']=='os'){ ?>
					<tr class="alignLeft">
					  	<td class="oscol txt_10b">OS</td>
                        <td>
                        	<input  type="text" name="SclBcurveOS<?php echo $i;?>" id="SclBcurveOS<?php echo $i;?>" value="<?php if($SclBcurveOS<>""){echo $SclBcurveOS;}?>" class="form-control" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');">
                        </td> 
                        <td>
                        	<input  id="SclDiameterOS<?php echo $i;?>" type="text" class="form-control" name="SclDiameterOS<?php echo $i;?>" value="<?php echo $SclDiameterOS;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">
                        </td> 
                        <td>
                        	<input type="text" name="SclsphereOS<?php echo $i;?>" value="<?php if($SclsphereOS!=""){echo $SclsphereOS;}?>" class="form-control " id="SclsphereOS<?php echo $i;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">
                        </td> 
                        <td>
                        	<input class="form-control " id="SclCylinderOS<?php echo $i;?>" type="text" name="SclCylinderOS<?php echo $i;?>" value="<?php if($SclCylinderOS!=""){echo $SclCylinderOS;}?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">
                        </td> 
                        <td><input type="text" name="SclaxisOS<?php echo $i;?>" value="<?php if($SclaxisOS!=""){echo $SclaxisOS;}?>" class="form-control" style="width:100%;" id="SclaxisOS<?php echo $i;?>"  style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>', 'noDecimal');" onKeyUp="check2BlurCL(this,'A','','<?php echo $rowName.$i;?>');"></td>
                        <td class="txt_11b alignLeft">
        					<div class="input-group" style="width:100%;">
        						<input type="text" name="SclColorOS<?php echo $i;?>"  id="SclColorOS<?php echo $i;?>" value="<?php if(SclColorOS) echo $SclColorOS; ?>" class="form-control" onblur="this.click();">
        						<?php // cl_getMenu("RgpColorOD".$i,"Clr"); ?>
        						<?php echo getMenus("color", "SclColorOS".$i); ?>
        					</div>
                        </td>
                        <td>
                        	<input id="SclAddOS<?php echo $i;?>" type="text" class="form-control" name="SclAddOS<?php echo $i;?>"  value="<?php echo $SclAddOS;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');">
                        </td> 
                        <td class="valignTop nowrap">
                            <div class="input-group" style="width:100%;">
                              <input id="SclDvaOS<?php echo $i;?>" type="text" name="SclDvaOS<?php echo $i;?>" value="<?php if($SclDvaOS) echo $SclDvaOS; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
    							<?php //cl_getMenu("SclDvaOS".$i,"Dis"); ?>
    							<?php echo getMenus("dva", "SclDvaOS".$i); ?>
    						</div>
                        </td> 
                        <td class="nowrap">
                        	<div class="input-group" style="width:100%;">
                            	<input type="text" name="SclNvaOS<?php echo $i;?>"  id="SclNvaOS<?php echo $i;?>" value="<?php if($SclNvaOS) echo $SclNvaOS; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();"> 
    							<?php //cl_getMenu("SclNvaOS".$i,"Nr"); ?>
    							<?php echo getMenus("nva", "SclNvaOS".$i); ?>
							</div>
                        </td> 
                        <td class="nowrap">
                            <div class="alignLeft" style="width:100%;float:left">
                                <input type="text" name="SclTypeOS<?php echo $i;?>" id="SclTypeOS<?php echo $i;?>" class="typeAhead form-control" style="width:100%; background-image:none;" value="" />
                                <input type="hidden" name="SclTypeOS<?php echo $i;?>ID" id="SclTypeOS<?php echo $i;?>ID" value="" funVars="halfFun~garbage1~garbage2~SclBcurveOS<?php echo $i;?>~SclDiameterOS<?php echo $i;?>">
                            </div>
                        </td>
                        <td class="alignLeft nowrap">
						<?php if(($_REQUEST['mode'] =='change' && $i==1 && $totRows >1) || ($_REQUEST['mode'] =='change' && $i!=$totRows)) {  ?>
                        <figure><span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('<?php echo $rowName.$i;?>');" data-toggle="tooltip" title="Delete Row" data-original-title="Delete Row"></span></figure>  
						<?php }else { ?>
						<figure><span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('<?php echo $DDName.$i;?>').value, 'os', '<?php echo $rowName;?>', 'imgOS', '<?php echo $i;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-toggle="tooltip" title="Add More" data-original-title="Add More"></span></figure>
						<?php } ?>
                                  </td>
                                </tr>
						<?php 
						} ?>
                   </table>
               </div>
             </div>   
<?php
}else if($_REQUEST['addType'] == 'rgp' || $_REQUEST['addType'] == 'rgp_soft' || $_REQUEST['addType'] == 'rgp_hard') {
?>
<div id="<?php echo $rowName.$i;?>" class="rgp_bg" style="display:block; clear:both; height:<?php echo $rgpHeight;?>">
				<div style="float:left;width:7%; <?php echo $topPad;?>;">
                  <select name="<?php echo $DDName.$i;?>" id="<?php echo $DDName.$i;?>" class="form-control minimal" onChange="changeCLType(this);changeRow(this.value, '<?php echo $_REQUEST['odos'];?>', '<?php echo $rowName;?>', document.getElementById('<?php echo $txtBox;?>').value, '<?php echo $i;?>',arrManufac, arrManufacId, arrManufacInfo);" style="width:100%;">
                    <option value="scl">SCL</option>
					<?php
						if($_REQUEST['addType'] == 'rgp'){
							echo "<option value='rgp' selected='selected'>RGP</option>";
						}
					?>
				
					<option value="rgp_soft"
					<?php
						if($_REQUEST['addType'] == 'rgp_soft'){
							echo "selected=\"selected\"";
						}
					?>
					>RGP Soft</option>
					<option value="rgp_hard"
					<?php
						if($_REQUEST['addType'] == 'rgp_hard'){
							echo "selected=\"selected\"";
						}
					?>
					>RGP Hard</option>
                    <option value="cust_rgp">Custom RGP</option>
                    <option value="prosthesis">Prosthesis</option>
                    <option value="no-cl">No-CL</option>
                  </select>
               </div>
               <div style="float:left;width:91%;">
				<table class="table borderless" id="rgp_table1" style="width:99%;">
				<tr>
				    <td style="width:3%;"></td>
					<td class="txt_11b nowrap" style="width:7%;">BC</td>
					<td class="txt_11b" style="width:7%;">Diameter</td> 												
					<td class="txt_11b" style="width:7%;">Power</td>
                    <td class="txt_11b" style="width:7%;">Cylinder</td>
                    <td class="txt_11b" style="width:7%;">Axis</td>
					<td class="txt_11b" style="width:7%;">OZ</td>
                    <td class="txt_11b" style="width:7%;">CT</td> 
					<td class="txt_11b" style="width:7%;">Color</td> 
					<td class="txt_11b" style="width:7%;">Add</td> 
					<td class="txt_11b" style="width:7%;">DVA</td> 
					<td class="txt_11b" style="width:7%;">NVA</td> 
					<td class="txt_11b" style="width:17%;">Type</td> 
					<td class="txt_11b" style="width:2%;"></td> 
				</tr>
                <?php 
				if($_REQUEST['odos']=='od') { ?>
				<tr class="alignLeft">
				  <td class="odcol txt_10b">OD</td>
					<td><input class="form-control " type="text" name="RgpBCOD<?php echo $i;?>" id="RgpBCOD<?php echo $i;?>" value="<?php if($RgpBCOD!="" ){echo $RgpBCOD;}?>" style="width:100%" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpBCOD<?php echo $i;?>','RgpBCOS<?php echo $i;?>');"></td>
					<td><input type="text" name="RgpDiameterOD<?php echo $i;?>" id="RgpDiameterOD<?php echo $i;?>" value="<?php if($RgpDiameterOD!="" ){echo $RgpDiameterOD;}?>" class="form-control " style="width:100%" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpDiameterOD<?php echo $i;?>','RgpDiameterOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td>
					<td><input class="form-control "  type="text" name="RgpPowerOD<?php echo $i;?>" id="RgpPowerOD<?php echo $i;?>" value="<?php if($RgpPowerOD!="" ){echo $RgpPowerOD;}?>" style="width:100%" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');" ></td> 
                    <td><input class="form-control "  type="text" name="RgpCylinderOD<?php echo $i;?>" id="RgpCylinderOD<?php echo $i;?>" value="<?php if($RgpCylinderOD!="" ){echo $RgpCylinderOD;}?>" size="5" style="width:100%" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');" ></td> 
                    <td><input class="form-control "  type="text" name="RgpAxisOD<?php echo $i;?>" id="RgpAxisOD<?php echo $i;?>" value="<?php if($RgpAxisOD!="" ){echo $RgpAxisOD;}?>" style="width:100%" onKeyUp="check2BlurCL(this,'A','','<?php echo $rowName.$i;?>');"></td> 
					<td><input type="text" name="RgpOZOD<?php echo $i;?>" id="RgpOZOD<?php echo $i;?>" value="<?php if($RgpOZOD!="" ){echo $RgpOZOD;}?>" class="form-control " style="width:100%"></td>
                    <td><input type="text" name="RgpCTOD<?php echo $i;?>" id="RgpCTOD<?php echo $i;?>" value="<?php if($RgpCTOD!="" ){echo $RgpCTOD;}?>" class="form-control " style="width:100%"></td>
                    <td class="alignLeft nowrap">
                        <div class="input-group" style="width:100%;">
    						<input type="text" name="RgpColorOD<?php echo $i;?>"  id="RgpColorOD<?php echo $i;?>" value="<?php if($RgpColorOD) echo $RgpColorOD; ?>" class="form-control" onblur="this.click();">
    						<?php // cl_getMenu("RgpColorOD".$i,"Clr"); ?>
    						<?php echo getMenus("color", "RgpColorOD".$i); ?>
    					</div>
				    <!-- Acuities -->
					</td> 
					<td><input id="RgpAddOD<?php echo $i;?>" type="text" class="form-control  " name="RgpAddOD<?php echo $i;?>" value="<?php echo $RgpAddOD;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpAddOD<?php echo $i;?>','RgpAddOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
					<td class="nowrap">
						<div class="input-group" style="width:100%;">
							<input id="RgpDvaOD<?php echo $i;?>" type="text" name="RgpDvaOD<?php echo $i;?>" value="<?php if($RgpDvaOD) echo $RgpDvaOD; else echo '20/'; ?>" style="width:100%;z-index:0;" class="form-control" onblur="this.click();">
							<?php // cl_getMenu("RgpDvaOD".$i,"Dis"); ?>
							<?php echo getMenus("dva", "RgpDvaOD".$i); ?>
						</div>
					<!-- Acuities-->
					</td>
                    <td class="nowrap"> 
                    	<div class="input-group" style="width:100%;">
							<input type="text" name="RgpNvaOD<?php echo $i;?>"  id="RgpNvaOD<?php echo $i;?>" value="<?php if($RgpNvaOD) echo $RgpNvaOD; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
					  		<?php // cl_getMenu("RgpNvaOD".$i,"Nr"); ?>
					  		<?php echo getMenus("nva", "RgpNvaOD".$i); ?>
						</div>
				    <!-- Acuities -->
					</td>
					<td>
                        <div class=" alignLeft" style="width:100%;float:left" >
                            <input type="text" name="RgpTypeOD<?php echo $i;?>" id="RgpTypeOD<?php echo $i;?>" class="typeAhead form-control" value="" style="width:100%; background-image:none;" />
    						<input type="hidden" name="RgpTypeOD<?php echo $i;?>ID" id="RgpTypeOD<?php echo $i;?>ID" value="" funVars="worksheet~RgpTypeOD<?php echo $i;?>~RgpTypeOS<?php echo $i;?>~RgpBCOD<?php echo $i;?>~RgpDiameterOD<?php echo $i;?>">
                        </div>
					</td> 
						
					<td class="nowrap"> 
    					<?php if(($_REQUEST['mode'] =='change' && $i==1 && $totRows >1) || ($_REQUEST['mode'] =='change' && $i!=$totRows)) {  ?>
                        <figure><span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('<?php echo $rowName.$i;?>');" data-toggle="tooltip" title="Delete Row" data-original-title="Delete Row"></span></figure>  
                        <?php }else { ?>
                        <figure><span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('<?php echo $DDName.$i;?>').value, 'od', '<?php echo $rowName;?>', 'imgOD', '<?php echo $i;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-toggle="tooltip" title="Add More" data-original-title="Add More"></span></figure>	
                        <?php } ?>
					</td> 
				</tr>
				<?php }
				if($_REQUEST['odos']=='os') { ?>
				<tr class="alignLeft">
				  <td class="oscol txt_10b">OS</td>
					<td><input class="form-control" type="text" name="RgpBCOS<?php echo $i;?>" id="RgpBCOS<?php echo $i;?>" value="<?php if($RgpBCOS!="" ){echo $RgpBCOS;}?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td>
					<td><input type="text" name="RgpDiameterOS<?php echo $i;?>" id="RgpDiameterOS<?php echo $i;?>" value="<?php if($RgpDiameterOS!="" ){echo $RgpDiameterOS;}?>"  class="form-control" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td>
					<td><input class="form-control" type="text" name="RgpPowerOS<?php echo $i;?>" id="RgpPowerOS<?php echo $i;?>" value="<?php if($RgpPowerOS!="" ){echo $RgpPowerOS;}?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td>
                    <td><input class="form-control" type="text" name="RgpCylinderOS<?php echo $i;?>" id="RgpCylinderOS<?php echo $i;?>" value="<?php if($RgpCylinderOS!="" ){echo $RgpCylinderOS;}?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td>
                    <td><input class="form-control" type="text" name="RgpAxisOS<?php echo $i;?>" id="RgpAxisOS<?php echo $i;?>" value="<?php if($RgpAxisOS!="" ){echo $RgpAxisOS;}?>" style="width:100%;" onKeyUp="check2BlurCL(this,'A','','<?php echo $rowName.$i;?>');"></td>
					<td><input type="text" name="RgpOZOS<?php echo $i;?>" id="RgpOZOS<?php echo $i;?>" value="<?php if($RgpOZOS!="" ){echo $RgpOZOS;}?>" class="form-control" style="width:100%;"></td>
                    <td><input type="text" name="RgpCTOS<?php echo $i;?>" id="RgpCTOS<?php echo $i;?>" value="<?php if($RgpCTOS!="" ){echo $RgpCTOS;}?>" class="form-control" style="width:100%;"></td> 
					<td class="alignLeft nowrap">
						<div class="input-group" style="width:100%;">
							<input type="text" name="RgpColorOS<?php echo $i;?>"  id="RgpColorOS<?php echo $i;?>" value="<?php if($RgpColorOS) echo $RgpColorOS; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
							<?php // cl_getMenu("RgpColorOS".$i,"Clr"); ?>
							<?php echo getMenus("color", "RgpColorOS".$i); ?>
						</div>
				        <!-- Acuities -->
					</td>
					<td><input  id="RgpAddOS<?php echo $i;?>" type="text" class="form-control" name="RgpAddOS<?php echo $i;?>" value="<?php echo $RgpAddOS;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
					<td class="nowrap">
						<div class="input-group" style="width:100%;">
							<input type="text" name="RgpDvaOS<?php echo $i;?>"  id="RgpDvaOS<?php echo $i;?>" value="<?php if($RgpDvaOS) echo $RgpDvaOS; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
							<?php // cl_getMenu("RgpDvaOS".$i,"Dis"); ?>
							<?php echo getMenus("dva", "RgpDvaOS".$i); ?>
						</div>
					<!-- Acuities -->
					</td> 
					<td class="nowrap">
						<div class="input-group" style="width:100%;">
							<input type="text" name="RgpNvaOS<?php echo $i;?>" id="RgpNvaOS<?php echo $i;?>" value="<?php if($RgpNvaOS) echo $RgpNvaOS; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
							<?php // cl_getMenu("RgpNvaOS".$i,"Nr"); ?>
							<?php echo getMenus("nva", "RgpNvaOS".$i); ?>
						</div>
				    <!-- Acuities -->
					</td> 
					<td class="nowrap">
                    <div class=" alignLeft" style="width:100%;float:left;">
                        <input type="text" name="RgpTypeOS<?php echo $i;?>" id="RgpTypeOS<?php echo $i;?>" class="typeAhead form-control" value="" style="width:100%; background-image:none;" />
						<input type="hidden" name="RgpTypeOS<?php echo $i;?>ID" id="RgpTypeOS<?php echo $i;?>ID" value="" funVars="halfFun~garbage1~garbage2~RgpBCOS<?php echo $i;?>~RgpDiameterOS<?php echo $i;?>">
                    </div>
					</td>
					
					<td class="nowrap">
					<?php if(($_REQUEST['mode'] =='change' && $i==1 && $totRows >1) || ($_REQUEST['mode'] =='change' && $i!=$totRows)) {  ?>
                    <figure><span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('<?php echo $rowName.$i;?>');" title="Delete Row"></span></figure>  
                    <?php }else { ?>
                    <figure><span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('<?php echo $DDName.$i;?>').value, 'os', '<?php echo $rowName;?>', 'imgOS', '<?php echo $i;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" title="Add More"></span></figure>
                    <?php } ?>
					</td> 
					
				</tr>
				<?php }
}else if($_REQUEST['addType'] == 'cust_rgp') {
?>
<div id="<?php echo $rowName.$i;?>" class="custRgp_bg" style="display:block; clear:both; height:<?php echo $rgpHeight;?>" >
    <div style="float:left;width:7%; <?php echo $topPad;?>;">
      <select name="<?php echo $DDName.$i;?>" id="<?php echo $DDName.$i;?>" class="form-control minimal" onChange="changeCLType(this);changeRow(this.value, '<?php echo $_REQUEST['odos'];?>', '<?php echo $rowName;?>', document.getElementById('<?php echo $txtBox;?>').value, '<?php echo $i;?>',arrManufac, arrManufacId, arrManufacInfo);" style="width:100%;">
        <option value="scl">SCL</option>
		<option value="rgp_soft">RGP Soft</option>
		<option value="rgp_hard">RGP Hard</option>
        <option value="cust_rgp" selected="selected">Custom RGP</option>
        <option value="prosthesis">Prosthesis</option>
        <option value="no-cl">No-CL</option>
      </select>
   </div>
   <div style="float:left;width:91%;">
	<table class="table borderless custRgp_bg" id="rgp_custom_table1" style="width:99%;">
			<tr class="txt_11b">
				<td style="width:3%;"></td>
				<td style="width:5%;" class="txt_11b nowrap">BC</td>									
				<td style="width:5%;" class="txt_11b">Diameter</td> 
				<td style="width:5%;" class="txt_11b">Power</td>
                <td style="width:5%;" class="txt_11b">Cylinder</td>
                <td style="width:5%;" class="txt_11b">Axis</td>
				<td style="width:5%;" class="txt_11b">2&#176;/W</td> 
				<td style="width:5%;" class="txt_11b">3&#176;/W</td> 
				<td style="width:5%;" class="txt_11b">PC/W</td> 
				<td style="width:5%;" class="txt_11b">OZ</td>
                <td style="width:5%;" class="txt_11b">CT</td> 
				<td style="width:5%;" class="txt_11b">Color</td> 
				<td style="width:5%;" class="txt_11b">Blend</td> 
				<td style="width:5%;" class="txt_11b">Edge</td> 
				<td style="width:5%;" class="txt_11b">Add</td> 
				<td style="width:5%;" class="txt_11b">DVA</td> 
				<td style="width:5%;" class="txt_11b">NVA</td> 
				<td style="width:14%;" class="txt_11b">Type</td> 
				<td style="width:2%;" class="txt_11b"></td> 
			</tr>
      <?php 
	  if($_REQUEST['odos']=='od') { ?>
			<tr class="alignLeft">
				<td class="odcol txt_10b">OD</td>
				<td><input class="form-control"  type="text" name="RgpCustomBCOD<?php echo $i;?>" id="RgpCustomBCOD<?php echo $i;?>" value="<?php echo($RgpCustomBCOD);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomBCOD<?php echo $i;?>','RgpCustomBCOS<?php echo $i;?>');"></td>												
				<td><input  id="RgpCustomDiameterOD<?php echo $i;?>" type="text" class="form-control" name="RgpCustomDiameterOD<?php echo $i;?>" value="<?php echo $RgpCustomDiameterOD;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomDiameterOD<?php echo $i;?>','RgpCustomDiameterOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
				<td><input class="form-control"  type="text" name="RgpCustomPowerOD<?php echo $i;?>" id="RgpCustomPowerOD<?php echo $i;?>" value="<?php echo($RgpCustomPowerOD);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
                <td><input class="form-control"  type="text" name="RgpCustomCylinderOD<?php echo $i;?>" id="RgpCustomCylinderOD<?php echo $i;?>" value="<?php echo($RgpCustomCylinderOD);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
                <td><input class="form-control"  type="text" name="RgpCustomAxisOD<?php echo $i;?>" id="RgpCustomAxisOD<?php echo $i;?>" value="<?php echo($RgpCustomAxisOD);?>" style="width:100%;" onKeyUp="check2BlurCL(this,'A','','<?php echo $rowName.$i;?>');"></td> 
				<td><input  type="text" name="RgpCustom2degreeOD<?php echo $i;?>" id="RgpCustom2degreeOD<?php echo $i;?>" value="<?php echo($RgpCustom2degreeOD);?>" style="width:100%;" class="form-control " onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td> 
				<td><input class="form-control"  type="text" name="RgpCustom3degreeOD<?php echo $i;?>" id="RgpCustom3degreeOD<?php echo $i;?>" value="<?php echo($RgpCustom3degreeOD);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td>	
				<td><input class="form-control"  type="text" name="RgpCustomPCWOD<?php echo $i;?>" id="RgpCustomPCWOD<?php echo $i;?>" value="<?php echo($RgpCustomPCWOD);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td>	
				<td><input  id="RgpCustomOZOD<?php echo $i;?>" type="text" class="form-control" name="RgpCustomOZOD<?php echo $i;?>" value="<?php echo $RgpCustomOZOD;?>" style="width:100%;"></td> 
                <td><input  id="RgpCustomCTOD<?php echo $i;?>" type="text" class="form-control" name="RgpCustomCTOD<?php echo $i;?>" value="<?php echo $RgpCustomCTOD;?>" style="width:100%;"></td> 
				<td class="nowrap"> <!-- Acuities -->
					<div class="input-group" style="width:100%;">
						<input type="text" name="RgpCustomColorOD<?php echo $i;?>"  id="RgpCustomColorOD<?php echo $i;?>" value="<?php if($RgpCustomColorOD) echo $RgpCustomColorOD; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomColorOD".$i,"Clr"); ?>
						<?php echo getMenus("color", "RgpCustomColorOD".$i); ?>
					</div>
				</td> 
				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input type="text" name="RgpCustomBlendOD<?php echo $i;?>"  id="RgpCustomBlendOD<?php echo $i;?>" value="<?php if($ColorOptionsArray) echo $RgpCustomBlendOD; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomBlendOD".$i,"Bd"); ?>
						<?php echo getMenus("blend", "RgpCustomBlendOD".$i); ?>
					</div>
				</td> 
				<td><input type="text" name="RgpCustomEdgeOD<?php echo $i;?>" id="RgpCustomEdgeOD<?php echo $i;?>" value="<?php echo($RgpCustomEdgeOD);?>" class="form-control" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomEdgeOD<?php echo $i;?>','RgpCustomEdgeOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
				<td><input id="RgpCustomAddOD<?php echo $i;?>" type="text" class="form-control" name="RgpCustomAddOD<?php echo $i;?>" value="<?php echo $RgpCustomAddOD;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomAddOD<?php echo $i;?>','RgpCustomAddOS<?php echo $i;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 

				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input id="RgpCustomDvaOD<?php echo $i;?>" type="text" name="RgpCustomDvaOD<?php echo $i;?>" value="<?php if($RgpCustomDvaOD) echo $RgpCustomDvaOD; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomDvaOD".$i,"Dis"); ?>
						<?php echo getMenus("dva", "RgpCustomDvaOD".$i); ?>
					</div>
				</td> 
				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input id="RgpCustomNvaOD<?php echo $i;?>" type="text" name="RgpCustomNvaOD<?php echo $i;?>" value="<?php if($RgpCustomNvaOD) echo $RgpCustomNvaOD; else echo '20/'; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomNvaOD".$i,"Nr"); ?>
						<?php echo getMenus("nva", "RgpCustomNvaOD".$i); ?>
					</div>
				</td> 
				<td class="nowrap">
                <div class="fl alignLeft" style="width:100%;">
                    <input type="text" name="RgpCustomTypeOD<?php echo $i;?>" id="RgpCustomTypeOD<?php echo $i;?>" class="typeAhead form-control" value="<?php echo $CLResData[$i]['RgpCustomTypeOD'];?>" style="width:100%; background-image:none;" />
                    <input type="hidden" name="RgpCustomTypeOD<?php echo $i;?>ID" id="RgpCustomTypeOD<?php echo $i;?>ID" value="<?php echo $CLResData[$i]['RgpCustomTypeOD_ID'];?>" funVars="worksheet~RgpCustomTypeOD<?php echo $i;?>~RgpCustomTypeOS<?php echo $i;?>~RgpCustomBCOD<?php echo $i;?>~RgpCustomDiameterOD<?php echo $i;?>">

                </div>
                </td> 
				<td class="nowrap"> 
					<?php if(($_REQUEST['mode'] =='change' && $i==1 && $totRows >1) || ($_REQUEST['mode'] =='change' && $i!=$totRows)) {  ?>
                    <figure><span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('<?php echo $rowName.$i;?>');" data-toggle="tooltip" title="Delete Row" data-original-title="Delete Row"></span></figure>
                    <?php }else { ?>
                    <figure><span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('<?php echo $DDName.$i;?>').value, 'od', '<?php echo $rowName;?>', 'imgOD', '<?php echo $i;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-toggle="tooltip" title="Add More" data-original-title="Add More"></span></figure>	
                    <?php } ?>
				</td> 
			</tr>
			<?php }
			if($_REQUEST['odos']=='os') { ?> 	            
			<tr class="alignLeft">
				<td class="oscol txt_10b">OS</td>  
				<td><input class="form-control" type="text" name="RgpCustomBCOS<?php echo $i;?>" id="RgpCustomBCOS<?php echo $i;?>" value="<?php echo($RgpCustomBCOS);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"  /></td>												
				<td><input id="RgpCustomDiameterOS<?php echo $i;?>" type="text" class="form-control  " name="RgpCustomDiameterOS<?php echo $i;?>" value="<?php echo $RgpCustomDiameterOS;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
				<td><input class="form-control" type="text" name="RgpCustomPowerOS<?php echo $i;?>" id="RgpCustomPowerOS<?php echo $i;?>" value="<?php echo($RgpCustomPowerOS);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
                <td><input class="form-control" type="text" name="RgpCustomCylinderOS<?php echo $i;?>" id="RgpCustomCylinderOS<?php echo $i;?>" value="<?php echo($RgpCustomCylinderOS);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
                <td><input class="form-control" type="text" name="RgpCustomAxisOS<?php echo $i;?>" id="RgpCustomAxisOS<?php echo $i;?>" value="<?php echo($RgpCustomAxisOS);?>" style="width:100%;" onKeyUp="check2BlurCL(this,'A','','<?php echo $rowName.$i;?>');"></td> 
				<td><input type="text" name="RgpCustom2degreeOS<?php echo $i;?>" id="RgpCustom2degreeOS<?php echo $i;?>"  value="<?php echo($RgpCustom2degreeOS);?>" style="width:100%;" class="form-control " onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td> 
				<td><input class="form-control" type="text" name="RgpCustom3degreeOS<?php echo $i;?>" id="RgpCustom3degreeOS<?php echo $i;?>"  value="<?php echo($RgpCustom3degreeOS);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td>	
				<td><input class="form-control" type="text" name="RgpCustomPCWOS<?php echo $i;?>" id="RgpCustomPCWOS<?php echo $i;?>" value="<?php echo($RgpCustomPCWOS);?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td>	
				<td><input id="RgpCustomOZOS<?php echo $i;?>" type="text" class="form-control  " name="RgpCustomOZOS<?php echo $i;?>" value="<?php echo $RgpCustomOZOS;?>" style="width:100%;"></td> 
                <td><input id="RgpCustomCTOS<?php echo $i;?>" type="text" class="form-control  " name="RgpCustomCTOS<?php echo $i;?>" value="<?php echo $RgpCustomCTOS;?>" style="width:100%;"></td> 
				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input type="text" name="RgpCustomColorOS<?php echo $i;?>" id="RgpCustomColorOS<?php echo $i;?>" value="<?php if($RgpCustomColorOS) echo $RgpCustomColorOS; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomColorOS".$i,"Clr"); ?>
						<?php echo getMenus("color", "RgpCustomColorOS".$i); ?>
					</div>
				</td> 
				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input type="text" name="RgpCustomBlendOS<?php echo $i;?>" id="RgpCustomBlendOS<?php echo $i;?>" value="<?php if($RgpCustomBlendOS) echo $RgpCustomBlendOS; ?>" class="form-control" style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomBlendOS".$i,"Bd"); ?>
						<?php echo getMenus("blend", "RgpCustomBlendOS".$i); ?>
					</div>
				</td> 
				<td><input type="text" name="RgpCustomEdgeOS<?php echo $i;?>" id="RgpCustomEdgeOS<?php echo $i;?>" value="<?php echo($RgpCustomEdgeOS);?>" class="form-control " style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
				<td><input  id="RgpCustomAddOS<?php echo $i;?>" type="text" class="form-control  " name="RgpCustomAddOS<?php echo $i;?>" value="<?php echo $RgpCustomAddOS;?>" style="width:100%;" onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','<?php echo $rowName.$i;?>');"></td> 
				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input id="RgpCustomDvaOS<?php echo $i;?>" type="text" name="RgpCustomDvaOS<?php echo $i;?>" value="<?php if($RgpCustomDvaOS) echo $RgpCustomDvaOS; else echo '20/'; ?>" class="form-control " style="width:100%;z-index:0;" onblur="this.click();">
						<?php // cl_getMenu("RgpCustomDvaOS".$i,"Dis"); ?>
						<?php echo getMenus("dva", "RgpCustomDvaOS".$i); ?>
				    </div>
				</td> 
				<td class="nowrap">
					<div class="input-group" style="width:100%;">
						<input id="RgpCustomNvaOS<?php echo $i;?>" type="text" name="RgpCustomNvaOS<?php echo $i;?>" value="<?php if($RgpCustomNvaOS) echo $RgpCustomNvaOS; else echo '20/'; ?>" class="form-control" onblur="this.click();">
						<?php cl_getMenu("RgpCustomNvaOS".$i,"Nr"); ?>
						<?php echo getMenus("nva", "RgpCustomNvaOS".$i); ?>
					</div>
				</td> 
				<td class="nowrap">
                <div class=" alignLeft" style="width:100%;float:left" >
                    <input type="text" name="RgpCustomTypeOS<?php echo $i;?>" id="RgpCustomTypeOS<?php echo $i;?>" class="typeAhead form-control" value="<?php echo $CLResData[$i]['RgpCustomTypeOS'];?>" style="width:100%; background-image:none;" />
                    <input type="hidden" name="RgpCustomTypeOS<?php echo $i;?>ID" id="RgpCustomTypeOS<?php echo $i;?>ID" value="<?php echo $CLResData[$i]['RgpCustomTypeOS_ID'];?>" funVars="halfFun~garbage1~garbage2~RgpCustomBCOS<?php echo $i;?>~RgpCustomDiameterOS<?php echo $i;?>" >
                </div>
				</td>
				<td> 
					<?php if(($_REQUEST['mode'] =='change' && $i==1 && $totRows >1) || ($_REQUEST['mode'] =='change' && $i!=$totRows)) {  ?>
                    <figure><span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('<?php echo $rowName.$i;?>');" data-toggle="tooltip" title="Delete Row" data-original-title="Delete Row"></span></figure>  
                    <?php }else { ?>
                    <figure><span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('<?php echo $DDName.$i;?>').value, 'os', '<?php echo $rowName;?>', 'imgOS', '<?php echo $i;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-toggle="tooltip" title="Add More" data-original-title="Add More"></span></figure>
                    <?php } ?>
				</td>				
			</tr>
			<?php 
			}
		 ?>			
	  </table>
	</div>
</div>
<?php
}
?>  

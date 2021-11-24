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
include_once('../../config/globals.php');
if(!isset($cl_flgSmDiv)){
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php'); 
}
include_once("menu_data.php");

$defCylSign=$GLOBALS["def_cylinder_sign_cl"];
if(empty($defCylSign)==true){ $defCylSign='+';}

$defCylRefSign=($GLOBALS["def_cylinder_sign_cl_ref"])? $GLOBALS["def_cylinder_sign_cl_ref"]: $defCylSign;
if(empty($defCylRefSign)==true){ $defCylRefSign='+';}

$manufacCount=0;
$sqlStyle = "select DISTINCT(manufacturer) from contactlensemake WHERE del_status=0 and (source = 0 || source = '') ORDER BY manufacturer ASC";
$resManuf = imw_query($sqlStyle);

if(imw_num_rows($resManuf) > 0){
	$simpleMenu='';	$practiceMake=0;
	if(!isset($cl_flgSmDiv)){
		$simpleMenu.= '<div id="simpleMenuDiv" style="height:180px; display:none">';
	}
	$simpleMenu.= '<ul id="menu_manuf">';
	while($RSManuf= imw_fetch_array($resManuf)){
		
		if($RSManuf['manufacturer']=='' && $practiceMake=1){
			$simpleMenu.= '<li><a href="javascript:void(0);">Practice</a>';
			$practiceMake=1;
		}else{
			$simpleMenu.= '<li><a href="javascript:void(0);">'.$RSManuf['manufacturer'].'</a>';
		}
		$manufacCount++;
		
		$qry="Select make_id, manufacturer, style, type from contactlensemake WHERE del_status=0 and (source = 0 || source = '') AND manufacturer='".$RSManuf['manufacturer']."' ORDER BY style ASC";
		$rs=imw_query($qry) or die(imw_error());
		if(imw_num_rows($rs)>0){
			{
				$simpleMenu.= '<ul>';
				while($res=imw_fetch_array($rs)){
					$styleType = '';
					/*if($res['manufacturer']!=''){ $styleType = $res['manufacturer']; }
					if($res['style']!=''){ $styleType .= $res['style']; }
					if($res['style']!='' && $res['type']!=''){ $styleType.='-'.$res['type']; }
					if($res['style']=='' && $res['type']!=''){ $styleType.=$res['type']; }*/
					
					$styleType = ''; $sep='';
					if($res['manufacturer']!=''){ $styleType = $res['manufacturer']; $sep='-';}
					if($res['style']!=''){ $styleType.=$sep.$res['style']; $sep='-';}
					if($res['type']!=''){ $styleType.=$sep.$res['type']; }
					
					
					$simpleMenu.= '<li><a href="javascript:void(0);" name="'.$res['make_id'].'" >'.$styleType.'</a>';
				}
				$simpleMenu.= '</ul>';
			}
		}
		$simpleMenu.= '</li>';
	}
	
	//BELOW PREVENTS TO SHRINK THE DIV IF CONTENT <8
	if($manufacCount<8){
		$tempLi=10-$manufacCount;
		for($i=0;$i<$tempLi; $i++){
			$simpleMenu.= '<li><a href="javascript:void(0);">&nbsp;</a></li>';
		}
	}
	
	$simpleMenu.= '</ul>';
	if(!isset($cl_flgSmDiv)){
	$simpleMenu.= '</div>';
	}
}
echo $simpleMenu;

//BC VALUES
$sclBcValues.= '
			<ul id="sclBcVals" class="dropdown-menu">
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.3</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.4</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.5</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.6</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.7</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.8</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">9.0</a></li>
			</ul>
			';

//BC VALUES
$rgpBcValues.= '
			<ul id="rgpBcVals" class="dropdown-menu">
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">8.0</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">9.0</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">10.0</a></li>
			</ul>
			';


//DIAMETER VALUES
$sclDiameterValues.= '
			<ul id="sclDiameterVals" class="dropdown-menu">
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">13.6</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">13.8</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14.0</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14.1</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14.2</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14.3</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14.4</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14.5</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">15.0</a></li>
			</ul>
			';			


//DIAMETER VALUES
$rgpDiameterValues.= '
			<ul id="rgpDiameterVals" class="dropdown-menu">
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">12</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">13</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">14</a></li>
				<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">15</a></li>
			</ul>
			';			

		
//SPHERE VALUES
$sphereValues = '';
$sphereValues .= '<ul id="sphereVals" class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
//$sphereValues .= '<li><a href="javascript:void(0);">0</a></li>';
$start = -20;
$end = 20;
if(isset($GLOBALS['CL_POWER_RANGE'])){
    $start = $GLOBALS['CL_POWER_RANGE']['min'];
    $end = $GLOBALS['CL_POWER_RANGE']['max'];
}
for($i = $start; $i <= $end; $i= $i + 0.25){
	$sign = "";
	$val = "";
	if($i > 0){
        $sign = '+';
        $val = $sign.number_format($i, 2);
	}else if($i < 0){
	    $val = $sign.number_format($i, 2);
	}
	else if($i == 0){ 
	    $sign = ' ';
	    $val = $sign." ".$i;
	}
	$tabIndex = ($start - $i);
	$sphereValues.='<li tabindex="'.$tabIndex.'" id="li'.trim($val).'"><a href="javascript:void(0);">'.$val.'</a></li>';
	//$sphereValues.='<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">'.$val.'</a></li>';
}
$sphereValues.='</ul>';

//CYINDER
if($defCylSign=='+'){
	$cylinderStart = 0;
	$cylinderEnd = 16;
}else{
	$cylinderStart = -16;
	$cylinderEnd = 0;
}
$cylinderJump = 0.25;
$cylinderValues = '';
if(isset($GLOBALS['CL_CYLINDER_RANGE']))
{
    if(isset($GLOBALS['CL_CYLINDER_RANGE']) && isset($GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_jump'])){
        $cylinderJump = $GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_jump'];
    }
    $cylinderValues = '<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
    if(isset($GLOBALS['CL_CYLINDER_RANGE']) && $GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_zero_pos'] == "TOP"){
        //$cylinderValues .= '<li id="cyl_0"><a href="javascript:void(0);">0</a></li>';
    }
    if(isset($GLOBALS['CL_CYLINDER_RANGE']) && $GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_val_order'] == "ASC"){
		$cylinderStart = $GLOBALS['CL_CYLINDER_RANGE']['max'];
        $cylinderEnd = $GLOBALS['CL_CYLINDER_RANGE']['min'];
        //$cylinderJump = (0 - $cylinderJump);
    }else{ 
		$cylinderEnd = $GLOBALS['CL_CYLINDER_RANGE']['min'];
		$cylinderStart = $GLOBALS['CL_CYLINDER_RANGE']['max'];
    }


	if($cylinderStart > $cylinderEnd){
		for($i = $cylinderEnd; $i <= $cylinderStart; $i = $i + $cylinderJump){
			$sign = "";
			$val = "";
			if($i > 0){
				$sign = '+';
				$val = $sign.number_format($i, 2);
			}else if($i < 0){
				$val = $sign.number_format($i, 2);
			}
			//if($i != 0.25 && $i != -0.25){
				//$cylinderValues.='<li id="cyl_'.$i.'"><a href="javascript:void(0);">'.$val.'</a></li>';
			//}
			if($i == 0){
				$cylinderValues.='<li id="cyl_0"><a href="javascript:void(0);">0</a></li>';
			}else{
				$cylinderValues.='<li id="cyl_'.$i.'"><a href="javascript:void(0);">'.$val.'</a></li>';
			}
		}
	}else if($cylinderStart < $cylinderEnd){
		for($i = $cylinderStart; $i <= $cylinderEnd; $i = $i + $cylinderJump){
			$sign = "";
			$val = "";
			if($i > 0){
				$sign = '+';
				$val = $sign.number_format($i, 2);
			}else if($i < 0){
				$val = $sign.number_format($i, 2);
			}
			
			//if($i != 0.25 && $i != -0.25){
				if($i=='0'){
					$cylinderValues.='<li id="cyl_0"><a href="javascript:void(0);">0</a></li>';
				}else{
					$cylinderValues.='<li id="cyl_'.$i.'"><a href="javascript:void(0);">'.$val.'</a></li>';
				}
			//}
			//if($i == -0.75){
				//$cylinderValues.='<li id="cyl_0"><a href="javascript:void(0);">0</a></li>';
			//}
		}
	}
	
    $cylinderValues.='</ul>';
}
else
{
    $cylinderValues = '<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
    for($i = $cylinderStart; $i <= $cylinderEnd; $i= $i + $cylinderJump){
        $sign = "";
        $val = "";
        if($i > 0){
            $sign = '+';
            $val = $sign.number_format($i, 2);
        }else if($i < 0){
            $val = $sign.number_format($i, 2);
        }
        else if($i == 0){
            $sign = ' ';
            $val = $sign." ".$i;
        }
        $cylinderValues.='<li id="cyl_'.$i.'"><a href="javascript:void(0);">'.$val.'</a></li>';
    }
    $cylinderValues.='</ul>';
}

//CYINDER - OVER-REFRACTION
if($defCylSign=='+'){
	$cylinderStart = 0;
	$cylinderEnd = 16;
}else{
	$cylinderStart = -16;
	$cylinderEnd = 0;
}
$cylinderJump = 0.25;
$cylinderRefValues = '';
if(isset($GLOBALS['CL_CYLINDER_RANGE']))
{
	if(isset($GLOBALS['CL_CYLINDER_RANGE']) && isset($GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_jump'])){
		$cylinderJump = $GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_jump'];
	}
	$cylinderRefValues = '<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
	if(isset($GLOBALS['CL_CYLINDER_RANGE']) && $GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_zero_pos'] == "TOP"){
		$cylinderRefValues .= '<li id="cyl_0"><a href="javascript:void(0);">0</a></li>';
	}
	if(isset($GLOBALS['CL_CYLINDER_RANGE']) && $GLOBALS['CL_CYLINDER_RANGE']['cl_cylinder_val_order'] == "ASC"){
		$cylinderStart = $GLOBALS['CL_CYLINDER_RANGE']['max'];
		$cylinderEnd = $GLOBALS['CL_CYLINDER_RANGE']['min'];
		$cylinderJump = (0 - $cylinderJump);
	}else{ 
		$cylinderStart = $GLOBALS['CL_CYLINDER_RANGE']['min'];
		$cylinderEnd = $GLOBALS['CL_CYLINDER_RANGE']['max'];
	}

	for($i = $cylinderStart; $i <= $cylinderEnd; $i = $i + $cylinderJump){
		$val = number_format($i, 2);
		$cylinderRefValues.='<li id="cyl_'.$i.'"><a href="javascript:void(0);">'.$val.'</a></li>';
	}
    $cylinderRefValues.='</ul>';
}
else
{
/*	$cylinderRefValues='
	<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">
	<li><a href="javascript:void(0);">0</a></li>';
	for($i=0.25; $i<=16; $i=$i+0.25){
		$sign= $val='';
		$val= $sign.number_format($i,2);
		$cylinderRefValues.='<li><a href="javascript:void(0);">'.$defCylRefSign.$val.'</a></li>';
	}
	$cylinderRefValues.='</ul>';*/

   $cylinderRefValues = '<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
    for($i = $cylinderStart; $i <= $cylinderEnd; $i= $i + $cylinderJump){
        $sign = "";
        $val = "";
        if($i > 0){
            $sign = '+';
            $val = $sign.number_format($i, 2);
        }else if($i < 0){
            $val = $sign.number_format($i, 2);
        }
        else if($i == 0){
            $sign = ' ';
            $val = $sign." ".$i;
        }
        $cylinderRefValues.='<li id="cyl_'.$i.'"><a href="javascript:void(0);">'.$val.'</a></li>';
    }
    $cylinderRefValues.='</ul>';	
}

//AXIS
$axisValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;"> ';
for($i=000; $i<=180; $i=$i+5){
	$len=strlen($i);
	if($len==1)$i='00'.$i;
	if($len==2)$i='0'.$i;
	$axisValues.='<li><a href="javascript:void(0);">'.$i.'</a></li>';
}
$axisValues.='</ul>';


//ADD
$addValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
for($i=0.50; $i<=5; $i=$i+0.25){
	$sign=$val='';
	if($i>0){ $sign='+';}
	$val= $sign.number_format($i,2);
	$addValues.='<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">'.$val.'</a></li>';
}
$addValues.='</ul>';

//POWER
$powerValues = '';
$powerValues .= '<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
//$powerValues .= '<li><a href="javascript:void(0);">0</a></li>';
$start = -20;
$end = 20;
if(isset($GLOBALS['CL_POWER_RANGE'])){
    $start = $GLOBALS['CL_POWER_RANGE']['min'];
    $end = $GLOBALS['CL_POWER_RANGE']['max'];
}
for($i = $start; $i <= $end; $i= $i + 0.25){
    $sign = "";
    $val = "";
    if($i > 0){
        $sign = '+';
        $val = $sign.number_format($i, 2);
    }else if($i < 0){
        $val = $sign.number_format($i, 2);
    }else if($i == 0){
        $sign = ' ';
        $val = $sign." ".$i;
    }
    $tabIndex = ($start - $i);
    $powerValues.='<li tabindex="'.$tabIndex.'" id="li'.trim($val).'"><a href="javascript:void(0);">'.$val.'</a></li>';
    //$sphereValues.='<li><a href="javascript:void(0);" onMouseOut="copyValuesODToOS(this,\'\', \'notMake\')">'.$val.'</a></li>';
}
$powerValues.='</ul>';

//DVA
$arrDVA=explode(',', $strAcuitiesMrDisString);
$dvaValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrDVA as $val){
	$val=str_replace('"', '', $val);
	$dvaValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$dvaValues.='</ul>';


//NVA
$arrNVA=explode(',', $strAcuitiesNearString);
$nvaValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrNVA as $val){
	$val=str_replace("'", '', $val);
	$nvaValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$nvaValues.='</ul>';


//COLOR
$colorValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrColor as $val){
	$val=str_replace('"', '', $val);
	$colorValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$colorValues.='</ul>';


//BLEND
$arrBlend=explode(',', $BlendOptionsString);
$blendValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrBlend as $val){
	$val=str_replace('"', '', $val);
	$blendValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$blendValues.='</ul>';


//COMFORT
$arrComfort=explode(',', $ComfortOptionsString);
$comfortValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrComfort as $val){
	$val=str_replace('"', '', $val);
	$comfort[$val]=$val;
	$comfortValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$comfortValues.='</ul>';


//MOVEMENT
$arrMovement=explode(',', $MovementOptionsString);
$movementValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrMovement as $val){
	$val=str_replace('"', '', $val);
	$movement[$val]=$val;
	$movementValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$movementValues.='</ul>';


//POSITION
$arrPosition=explode(',', $PositionOptionsString);
$positionValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrPosition as $val){
	$val=str_replace('"', '', $val);
	$position[$val]=$val;
	$positionValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$positionValues.='</ul>';


//CONDITION
$arrCondition=explode(',', $ConditionOptionsString);
$conditionValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrCondition as $val){
	$val=str_replace('"', '', $val);
	$condition[$val]=$val;
	$conditionValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$conditionValues.='</ul>';



//Fluorescein Pattern
$arrFLP=explode(',', $FluoresceinPatternOptionsString);
$flpValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrFLP as $val){
	$val=str_replace('"', '', $val);
	$flpValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$flpValues.='</ul>';


//Inverted Lids
$arrIL=explode(',', $InvertedLidsOptionsString);
$ilValues='<ul class="dropdown-menu" style="max-height:300px;overflow-y:scroll;">';
foreach($arrIL as $val){
	$val=str_replace('"', '', $val);
	$ilValues.='<li><a href="javascript:void(0);">'.trim($val).'</a></li>';
}
$ilValues.='</ul>';

?>


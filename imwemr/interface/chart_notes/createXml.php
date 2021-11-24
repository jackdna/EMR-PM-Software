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
File: createXml.php
Purpose: This file provides functions to create xml files for saving exam data.
Access Type : Include file
*/
?>
<?php
function showArrVals($arr)
{
	if(is_array($arr))
	{
		foreach($arr as $var => $val)
		{
			echo $var .":::". $val ."<BR>";
			if(is_array($val))
			{
				showArrVals($val);
			}
		}
	}	
}

function getXmlMenuArray($strMenuXml)
{	
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parse_into_struct($parser, $strMenuXml, $arrDirtyMenuVals, $index);
	xml_parser_free($parser);
	return $arrDirtyMenuVals;
}

function getMenuInputValueArray($arrDirtyMenuVals)
{	
	//echo "hello world<br>";
	// Create Menu and Input And Values
	$arrMenusInputsAndVals = array();	
	if(count($arrDirtyMenuVals) > 0){
		foreach($arrDirtyMenuVals as $var => $val)
		{
			if($val["type"] == "complete"){
				
				echo $var." <br> ";
				print_r($val);
				echo "<br><br>";
			}			
		}	
	}
}

function getMenuXmlStringFromFile($menuFilePath)
{
	$menuFile = $menuFilePath;	
	//Reading File
	//$fileContents = file_get_contents($menuFile);
	$fileContents = file($menuFile);
	
	$strMenuXml = "";
	foreach($fileContents as $var => $val)
	{
		$strMenuXml .= trim($val);
		
	}
	
	return $strMenuXml;
}
function getAttributeString($arr)
{
	$strAtt = "";
	foreach($arr as $var => $val)
	{
		$thisVar = strtolower($var);
		$thisVar = ($thisVar == "issingleselect") ? "isSingleSelect" : $thisVar;		
		$strAtt .= $thisVar."=\"".$val."\" ";
	}
	return $strAtt;
}

function xmlRefineValue($str){
	return str_replace(array("'","\"",">","<","&"),array("&#39;","&quot;","&gt;","&lt;","&amp;"), $str);
}

function processTag($arr,$menuName,$arrMenusInputsAndVals)
{	
	$tag = strtolower($arr["tag"]);
	$type = $arr["type"];
	$level = $arr["level"];
	$attributes = $arr["attributes"];	
	$retStr = "";
	$elemName = $attributes["elem_name"];
	if(!empty($elemName)){
		$value = xmlRefineValue($_POST[$elemName]);
	}	
	
	if($type == "open")
	{
		$strAtt = (is_array($attributes)) ? getAttributeString($attributes) : "";		
		$retStr .= "<$tag $strAtt>";			
	}
	else if($type == "close")
	{
		$retStr .= "</$tag>";		
	}
	else if($type == "complete")
	{
		if(is_array($attributes))
		{
			unset($attributes["DATA"]);
			$strAtt = (is_array($attributes)) ? getAttributeString($attributes) : "";
		}
		$retStr .= "<$tag $data $strAtt>$value</$tag>";		
	}		
	return $retStr;
}

function getMenuXmlStringRecreated($menuName,$arrDirtyMenuVals,$arrMenusInputsAndVals)
{	
	$newXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>"; 
	//$menuLevel = $menuName."-s1";
	$menuLevel = $menuName;
	foreach($arrDirtyMenuVals as $var => $val)
	{		
		$newXml .= processTag($val,$menuName,$arrMenusInputsAndVals);		
	}	
	return $newXml;
}

function getXmlValuesExtracted($arrDirtyMenuVals)
{
	$tmp1 = array();
	$retV=false;
	if(count($arrDirtyMenuVals) > 0){
		foreach($arrDirtyMenuVals as $key => $val){
			$tmp = $val["attributes"]["elem_name"];
			if(isset($tmp) && !empty($tmp)){
				global $$tmp;
				$$tmp = $val["value"];
			}
		}
		$retV=true;
	}
	return $retV;	
}

function newXmlString($menuName,$strMenu,$menuFilePath){	
	$strMenuXml = getMenuXmlStringFromFile($menuFilePath);
	$arrDirtyMenuVals = getXmlMenuArray($strMenuXml);
	return getMenuXmlStringRecreated($menuName,$arrDirtyMenuVals,$arrMenusInputsAndVals);
}

/*test
$menuName = "external";
$menuFilePath = "xml/external_od.xml";
$newXml = newXmlString($menuName,$strMenu,$menuFilePath);
//echo "<plaintext>";
//echo $newXml;
//exit;
//echo "<br>";
$arrDirtyMenuVals = getXmlMenuArray($newXml);
//print_r($arrDirtyMenuVals);
$retV = getXmlValuesExtracted($arrDirtyMenuVals);
//echo ("test:<br> ".$elem_periOd_hemoHage_sup);
//*/
function getXmlValuesExtracted_pr($arrDirtyMenuVals)
{
	//$tmp1 = array();
	$retV=false;
	if(count($arrDirtyMenuVals) > 0){
		foreach($arrDirtyMenuVals as $key => $val){
			$tmp = $val["attributes"]["elem_name"];
			if(isset($tmp) && !empty($tmp)){
				global $$tmp;
				$$tmp = $val["value"];
				$v[]=$$tmp;
			}
		}
		$retV=true;
	}
	if($retV){
		return $v;
	}
	//return $retV;	
}

?>
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
File: signApplet.php
Coded in PHP7
Purpose: This file provide Sign functionality in work view.
Access Type : Direct
*/
?>
<?php
//signApplet.php
require_once(dirname(__FILE__).'/../../config/globals.php');
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

//$strpixls = $_GET["strpixls"];
//$fid = $_GET["fid"];
$signType = $_GET["signType"];

$final_flg=$_GET["final_flg"];
$sec=$_GET["sec"];

//$signed=$_GET["signed"];

$w = 225*2;
$h =  45*2;
$isHTML5OK = 1; //isHtml5OK();
?>
<html>
<head>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<style>
span{float:left;}

.sigdrw{border:1px solid orange;display:inline-block;}
input[type=button]{width:70px;margin:2px;padding:0px;}

</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/work_view.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/simple_drawing.js"></script>

<script>
var signType=<?php echo $signType;?>;
var isHTML5OK="<?php echo $isHTML5OK;?>";
var sec="<?php echo $sec;?>";
function getSign(){
	if(isHTML5OK=="1"){
		
		return;	
	}
	/*
	coords = getCoords("sign");
	var objElemSignCoords = gebi("elem_signCoords");
	objElemSignCoords.value = coords;
	*/	
}

function transferSign(){
	if(isHTML5OK=="1"){
		var objData = gebi("sig_datasign");
		var objImg = gebi("sig_imgsign");
		
		if(objData==""){
			alert("signature can not be empty");
			return;
		}
		
		
		if(sec=="proc"){
			top.getAssessmentSign(signType,1,"",objData.value,objImg.value);
		}
		else{	
			top.fmain.getAssessmentSign(signType,1,"",objData.value,objImg.value);
		}
		return;
	}
	
	var objElemSignCoords = gebi("elem_signCoords");
	coords = refineCoords(objElemSignCoords.value);
	
	<?php //if($final_flg==1){?>
	/*
	if(coords=="" || coords=="0-0-0:;"){
		alert("signature can not be empty");
		return;
	}
	*/
	<?php //} ?>	
	
	//top.fmain.getAssessmentSign(signType,1,coords);	
}
function closeSign(){	
	if(sec=="proc"){
	top.getAssessmentSign(signType,2,"");
	}else{
	top.fmain.getAssessmentSign(signType,2,"");
	}	
}


//--

$(document).ready(function () {

if(isHTML5OK=="1"){
	$("canvas").each(function(){  oSimpDrw[this.id]=new SimpleDrawing(this.id); oSimpDrw[this.id].init(); });
}

});

//--



</script>
</head>
<body topmargin="0" leftmargin="0">
<?php
if($isHTML5OK){

$sig_path_od="";

?>
<span>
<div class="sigdrw">
	<canvas id="sign" width="<?php echo $w;?>" height="<?php echo $h;?>" ></canvas>
	<input type="hidden" name="sig_datasign"  id="sig_datasign" />
	<input type="hidden" name="sig_imgsign"  id="sig_imgsign" value="<?php echo $sig_path_od;?>" />
</div>
</span>
<?php } //COMPATIBILITY

?>

<span>
	<input name="btnDone" type="button"  class="dff_button_sm btn btn-success" id="btnDone" 
	onClick="transferSign();" value="Done" /><br/>
	
	<input name="btnDel" type="button"  class="dff_button_sm  btn btn-danger" id="btnDel" 
	onClick="getClear('sign');"
	value="Delete" /><br/>
	
	<input name="btnClose" type="button"  class="dff_button_sm btn btn-danger" id="btnClose" 
	onClick="closeSign();" value="Close" />	
</span>
</body>
</html>
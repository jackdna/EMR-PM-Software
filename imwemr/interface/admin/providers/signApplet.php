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
	Purpose: Create signature of provider
	Access Type: Direct
*/

require_once("../../../config/globals.php");
//require_once("../../chart_notes/common/functions.php");

if($_POST["elem_formAction"] == "GetSign")
{
	require_once("../../../library/classes/SaveFile.php");
	$strpixls=$_POST["strpixls"];
	$form_id=$_POST["fid"];
	$signType =  $_POST["signType"];
	$patient_id=$_SESSION["patient"];
	$save = $_POST["final_flg"];
	$proId = $_POST["proId"];
	$sData = $_POST["sData"];
	$sImg=$_POST["sImg"];

	if(empty($strpixls) && !empty($sData)){ $strpixls = $sData; }
	
	$oSaveFile = new SaveFile($proId,1); //User	
	$signSavePath = $oSaveFile->createSignImages($strpixls,0,0);
	if(!empty($signSavePath))
	{
		if(!empty($proId)){
			$sql="UPDATE users SET sign_path ='".imw_real_escape_string($signSavePath)."' WHERE id='".$proId."' ";
			sqlQuery($sql);
		}
		echo $signSavePath;
	}else{				
		echo "0";
	}
	exit();
}

$signType = $_GET["signType"];
$final_flg=$_GET["final_flg"];

$w = 400;
$h = 125;
$isHTML5OK = isHtml5OK();
?>
<html>
<head>
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">

<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/simple_drawing.js" type="text/javascript" ></script>
<script>
var signType=<?php echo $signType;?>;
var isHTML5OK="<?php echo $isHTML5OK;?>";
function getSign(){
	if(isHTML5OK=="1"){
		
		return;	
	}
	coords = getCoords("sign");
	var objElemSignCoords = document.getElementById("elem_signCoords");
	objElemSignCoords.value = coords; 	
}

function transferSign(){
	if(isHTML5OK=="1"){
		var objData = document.getElementById("sig_datasign");
		var objImg = document.getElementById("sig_imgsign");
		
		if(objData.value==""){
			alert("signature can not be empty");
			return;
		}
		
		top.fmain.getAssessmentSign(signType,1,"",objData.value,objImg.value);
		return;
	}
	
	var objElemSignCoords = document.getElementById("elem_signCoords");
	coords = refineCoords(objElemSignCoords.value);
	
	<?php //if($final_flg==1){?>
	if(coords=="" || coords=="0-0-0:;"){
		alert("signature can not be empty");
		return;
	}
	<?php //} ?>	
	
	top.fmain.getAssessmentSign(signType,1,coords);	
}
function closeSign(){	
	top.fmain.getAssessmentSign(signType,2,"");	
}

function getClear(ap)
{	
	if(document.applets[ap]){	
	document.applets[ap].clearIt();	
	if(typeof document.applets[ap].onmouseout == "function"){
		document.applets[ap].onmouseout();
	}else{
		var objP = document.applets[ap].parentNode;
		if( objP && (typeof objP.onmouseout == "function") ){
			objP.onmouseout();
		}
	}
	}else if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){
		
		if(oSimpDrw && oSimpDrw[ap]){oSimpDrw[ap].clearCanvas();	}		
	}
}

$(document).ready(function () {
	if(isHTML5OK=="1"){
		$("canvas").each(function(){  oSimpDrw[this.id]=new SimpleDrawing(this.id); oSimpDrw[this.id].init(); });
	}
});
</script>
</head>
<body >
	<div style="float:left; background-color:#FFF; display:block;">
		<?php
      if($isHTML5OK){
        $sig_path_od="";
    ?>
        <div class="col-xs-10">
        	<div class="row">
          	<div class="sigdrw">
            	<canvas id="sign" width="<?php echo $w;?>" height="<?php echo $h;?>" ></canvas>
            	<input type="hidden" name="sig_datasign"  id="sig_datasign" />
            	<input type="hidden" name="sig_imgsign"  id="sig_imgsign" value="<?php echo $sig_path_od;?>" />
          	</div>
         	</div>   
        </div>
    <?php 
      } //COMPATIBILITY
	  ?>
    
      <span class="col-xs-2 text-right mt5">
        <input name="btnDone" type="button"  class="btn btn-success" id="btnDone" onClick="transferSign();" value="Done" />
        <span class="clearfix"></span>
        <input name="btnDel" type="button"  class="btn btn-primary mt10" id="btnDel" onClick="getClear('sign');" value="Delete" />
        <span class="clearfix"></span>
        <input name="btnClose" type="button"  class="btn btn-danger mt10" id="btnClose" onClick="closeSign();" value="Close" />
      </span>
	</div>  
</body>
</html>
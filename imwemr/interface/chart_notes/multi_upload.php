<?php
/*
	/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
	
	File: tree4pt_docs.php
	Purpose: Show tree for consent form
	Access Type: Include 
*/
require_once("../../config/globals.php");
require_once("../../library/classes/dhtmlgoodies_tree.class.php");
require_once("../../library/classes/Mobile_Detect.php");
$library_path = $GLOBALS['webroot'].'/library';

// Exclude tablets.
$detect = new Mobile_Detect;
$this_device = "frontend";
if( $detect->isMobile() && !$detect->isTablet() ){
	$this_device = "mobile";
}
$sessOptId = $_SESSION['authId'];
$tree = new dhtmlgoodies_tree();
$_SESSION['authId'];
$patient_id = $_SESSION['patient'];
$dataPath = substr(data_path(), 0, -1);


if(!$p) { $p=1;}
include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
$p++;
if($_REQUEST["doc_name"]=="multi_upload") {		
	//$tree->addToArray($p,"Multi Upload",0,"");
	$tree->addToArray($p,"Multi Upload",0,"javascript:top.show_loading_image('show','-60','Please Wait');top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/multi_upload.php?doc_name=multi_upload');","",$initMultiUploadClass);
	$p++;
}
include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
$p++;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
        <link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
        <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
        <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>
	</head>
    <body onUnload="top.btn_show('');">
		<?php 
        $col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 310)) ; ?>
        <div class="col-xs-12 bg-white">
            <div class="row">
                <div id="multiupload_tree_div" class=" col-xs-2 " style=" max-height:100%; overflow:scroll">
					<?php 
                    $tree->writeCSS();
                    $tree->writeJavascript();
                    $tree->drawTree();
                    ?>
                </div>
                <div class="col-xs-10 ">
                    <div class="row">
                        <div class="well pd0 margin_0 nowrap " style="vertical-align:text-top;">
                        	<iframe name="ifrm_FolderContent" id="ifrm_FolderContent" <?php echo $consentScroll;?>  style="width:100%; " src="pdf_split.php" frameborder="0"></iframe>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
		<script>//alert($( document ).height()+'@@'+<?php echo $col_height;?>);
			top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
			top.btn_show("MULTI_UPLOAD");
			var dh = top.$( document ).height()-10;
			document.getElementById("ifrm_FolderContent").style.height=dh+'px';
			document.getElementById("multiupload_tree_div").style.height=dh+'px';
        </script>	
    </body>
</html>
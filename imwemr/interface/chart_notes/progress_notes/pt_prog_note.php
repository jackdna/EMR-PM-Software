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
File: pt_prog_note.php
Purpose: This file provide listing of all Operative Notes on title bar of work view.
Access Type : Direct
*/

require_once("../../../config/globals.php");
require_once("../../../library/classes/dhtmlgoodies_tree.class.php");
require_once("../../../library/classes/work_view/OperativeNote.php");
require_once("../../../library/classes/work_view/PnTemplate.php");
require_once("../../../library/classes/work_view/PnReports.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION["patient"];
$pg_title = 'Operative Notes';

$oPnData= new OperativeNote;
$oPnTmp = new PnTemplate;
$oPnRep = new PnReports;
$tree 	= new dhtmlgoodies_tree();
//-----  Get data from remote server -------------------

//$zRemotePageName = "progress_notes/pt_prog_note";
//require(dirname(__FILE__)."/../get_chart_from_remote_server.inc.php");

$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}

//START CODE TO MOVE OPERATIVE NOTE TO TRASH
if(!empty($_REQUEST["elem_delId"]) && $_REQUEST["elem_formAction"]=="dlRprt"){
	$delId = $_REQUEST["elem_delId"];
	$err = $oPnRep->deleteRecord($delId);
}
//END CODE TO MOVE OPERATIVE NOTE TO TRASH

//START CODE TO RESTORE OPERATIVE NOTE FROM TRASH
if(!empty($_REQUEST["elem_delId"]) && $_REQUEST["elem_formAction"]=="ActivateReport"){
	$restoreId = $_REQUEST["elem_delId"];
	$err = $oPnRep->activateRecord($restoreId);
}
//END CODE TO RESTORE OPERATIVE NOTE FROM TRASH

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> Operative Note</title>
        <link href="<?php echo $library_path;?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path;?>/css/common.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path;?>/css/document.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>
        
    	<script>
			window.focus();
		</script>
	</head>
    <body>
		<?php
        $col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 290)) ;
        ?>
        <div class="col-xs-12 bg-white">
        	<div class="row">
        		<div class=" col-xs-2 " style="height:<?php echo $col_height;?>px; max-height:100%; overflow:scroll">
                	<?php
					list($arrTemp,$arrTrash) = $oPnRep->getPtReports($patient_id);
					$showInfo = "";
					include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
					if(!$p) { $p=1;}
					if($_REQUEST["doc_name"]=="view_operative_note") {
						$p++;
						$tree->addToArray($p,"Operative Notes",0,"","",$initOpNotesClass);
						$mediaOPnote_arr = $oPnData->getThisPatientOPnoteMedia();
						$a=$p;
						foreach($arrTemp as $key => $val){
							$tDate = $key;
							$p++;
							$tree->addToArray($p,$tDate,$a,"","","icon-folder-filled");
							$b=$p;
							if(count($val) > 0) {
								foreach($val as $key2 => $val2){
									$p++;
									$pnId 			= $val2[0];
									$tId 			= $val2[1];
									$tNameScEMR 	= trim($val2[4]);
									$tName 			= $oPnTmp->getTempName($tId);
									if(!trim($tName)) { $tName = trim($tNameScEMR); }
									$showInfo		= "yes";
									$opNoteDateTime = $val2[3];
									$operatorName 	= stripslashes($val2[6]);
									$tree->addToArray($p,$tName,$b,"load_file.php?elem_pnRepId=$pnId&media_id=$media_id","ifrm_FolderContent","pdf-icon","remove-icon",$GLOBALS['webroot']."/interface/chart_notes/progress_notes/pt_prog_note.php?elem_delId=".$pnId."&elem_formAction=dlRprt&doc_name=".$_REQUEST['doc_name'],"","Move To Trash","","","",$showInfo,$opNoteDateTime,$operatorName,"","","",true,"","","",true);	
								}
							}
						}	
						
						foreach($arrTrash as $key => $val){
							$tDate = $key;
							$p++;
							$tree->addToArray($p,"Trash",$a);
							$b=$p;
							$p++;
							$tree->addToArray($p,$tDate,$b);
							$c=$p;
							if(count($val) > 0) {
								foreach($val as $key2 => $val2){
									$p++;
									$pnId 			= $val2[0];
									$tId 			= $val2[1];
									$tNameScEMR 	= trim($val2[4]);
									$tName 			= $oPnTmp->getTempName($tId);
									if(!trim($tName)) { $tName = trim($tNameScEMR); }
									
									$showInfo		= "yes";
									$opNoteDateTime	= $val2[3];
									$operatorName 	= stripslashes($val2[6]);
									$tree->addToArray($p,$tName,$c,"load_file.php?elem_pnRepId=$pnId&media_id=$media_id","ifrm_FolderContent","pdf-icon","restore-icon",$GLOBALS['webroot']."/interface/chart_notes/progress_notes/pt_prog_note.php?elem_delId=".$pnId."&elem_formAction=ActivateReport&doc_name=".$_REQUEST['doc_name'],"","Restore","","","",$showInfo,$opNoteDateTime,$operatorName,"");	
								}
							}
						}
						
					}
					include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
					$p++;
					$tree->writeCSS();
					$tree->writeJavascript();
					$tree->drawTree();				
                	?>
                </div>
                
                 <div class="col-xs-10 ">
                    <div class="row">
                        <div class="well pd0 margin_0 nowrap" style="vertical-align:text-top;">
                        <iframe name="ifrm_FolderContent" id="ifrm_FolderContent" <?php echo $consentScroll;?>  style="width:100%; height:<?php echo $col_height;?>px;" src="treeOpNoteDetails.php" frameborder="0"></iframe>
                        </div>   
                    </div>
                </div>
            </div>
        </div>        
        <form name="delFrm" action="pn_save.php" method="get">
            <!--
            <input type="hidden" name="elem_formAction" value="dlRprt">
            <input type="hidden" name="elem_delId" value="">
            -->
        </form>
				<script>
					$(function(){
						$('[data-toggle="tooltip"]').tooltip({container:'body'});
					});	
	        top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
      	</script>	
    </body>
</html>


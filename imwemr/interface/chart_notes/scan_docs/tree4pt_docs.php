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
require_once("../../../config/globals.php");
require_once("../../../library/classes/dhtmlgoodies_tree.class.php");
require_once("../../../library/classes/Mobile_Detect.php");
$library_path = $GLOBALS['webroot'].'/library';

// Exclude tablets.
$detect = new Mobile_Detect;
$this_device = "frontend";
if( $detect->isMobile() && !$detect->isTablet() ){
	$this_device = "mobile";
}
$sessOptId = $_SESSION['authId'];
$tree = new dhtmlgoodies_tree();
//$tree2 = new dhtmlgoodies_tree();
$_SESSION['authId'];
$patient_id = $_SESSION['patient'];
$dataPath = substr(data_path(), 0, -1);

//---- Get Patient Consent Forms Signed Date(s)-------
?>
<!--<div class="row">
	<div class=" col-xs-12 " style="height:<?php //echo ($col_height/2);?>px; max-height:100%; overflow:scroll; overflow-x:scroll">
-->
		<?php
if(!$p) { $p=1;}
include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
$p++;
if($_REQUEST["doc_name"]=="view_ccda") {		
	$tree->addToArray($p,"CCDA",0,"","",$initCcdaClass);
	$a=$p;
	$showInfo = "";
	foreach($documents as $key_element => $document) {
		$a++;
		$documentName 	= $document["name"];
		$documentPath 	= $document["path"];
		$documentPathNew= str_ireplace("../main/uploaddir","",$documentPath);
		$documentPathNew= "../../../data/".constant('PRACTICE_PATH').$documentPathNew;
		
		$showInfo		= "yes";
		$ptDocsDateTime	= $document['pt_docs_date_time'];
		$operatorName	= stripslashes($document['operator_name']);
		$tree->addToArray($a,$documentName,$p,"load_ccda.php?file_path=".$documentPathNew,"ifrm_FolderContent","pdf-icon","remove-icon","javascript:delCcda('$key_element')","","Delete","","","",$showInfo,$ptDocsDateTime,$operatorName,"","","",true,"","","",true);
	}
	$p=$a;
}
$z = $p;
if($_REQUEST["doc_name"]=="view_pt_docs") {		
	$tree->addToArray($z,"Pt. Docs",0,"","",$initPtDocClass);
	$p++;
	$tree->addToArray($p,"Saved Docs",$z,"","",$initPtDocScanClass);
	$a=$p;
	foreach($savedTemplateArr as $key_element => $cat_arr) {
		$a++;
		$tree->addToArray($a,$key_element,$p,"","","icon-folder-filled");
		$b=$a;
		foreach($cat_arr as $keyElements => $date_arr) {
			$b++;
			$tree->addToArray($b,$keyElements,$a,"","","icon-folder-filled");
			$c = $b;
			foreach($date_arr as $date_arr_key => $date_arr_val) {
				$c++;
				$pt_docs_patient_templates_id 	= $date_arr_val["pt_docs_patient_templates_id"];
				$pt_doc_primary_template_id		= $date_arr_val["pt_doc_primary_template_id"];
				$pt_docs_template_name 			= $date_arr_val["pt_docs_template_name"];
				$showInfo						= "yes";
				$ptDocsDateTime					= $date_arr_val['pt_docs_date_time'];
				$operatorName					= stripslashes($date_arr_val['operator_name']);
				
				$tree->addToArray($c,$pt_docs_template_name,$b,"load_pt_docs.php?temp_id=".$pt_docs_patient_templates_id."&mode=print","ifrm_FolderContent","pdf-icon","remove-icon","javascript:delPtDocs('$pt_doc_primary_template_id','$pt_docs_patient_templates_id')","","Delete","","","",$showInfo,$ptDocsDateTime,$operatorName,"","","",true,"","","",true);
			}
			$b=$c;
		}
		$a=$b;
	}
	$p=$a;
	
	$p++;
	$tree->addToArray($p,"Collection Letters",$z,"","",$initPtDocCollectionClass);
	$a=$p;
	foreach($savedCollectionArr as $folder_name=>$subArr){
		$a++;
		$tree->addToArray($a,$folder_name,$p,"","","icon-folder-filled");
		$b=$a;
		foreach($subArr as $keyElements => $date_arr) {
			$b++;
			$tree->addToArray($b,$keyElements,$a,"","","icon-folder-filled");
			$c=$b;
			foreach($date_arr as $date_arr_key => $date_arr_val) {
				$c++;
				$collection_id		= $date_arr_val["id"];
				$collection_name	= $date_arr_val["collection_name"];
				$showInfo			= "yes";
				$ptDocsDateTime		= $date_arr_val['pt_docs_date_time'];
				$operatorName		= stripslashes($date_arr_val['operator_name']);

				$tree->addToArray($c,$collection_name,$b,"load_pt_docs.php?temp_id=".$collection_id."&mode=print&type=collection","ifrm_FolderContent","pdf-icon","remove-icon","javascript:delPtDocs('collection','$collection_id')","","Delete","","","",$showInfo,$ptDocsDateTime,$operatorName,"","","",true,"","","",true);	
			}
			$b=$c;
		}
		$a=$b;
	}
	$p=$a;
	$p++;
	$tree->addToArray($p,"Patient Orders",$z,"","",$initPtDocPtOrderClass);
	$a=$p;
	foreach($orderTreeData as $order_date => $order_data_arr) {
		$a++;
		$tree->addToArray($a,$order_date,$p,"","","icon-folder-filled");
		$b=$a;
		foreach($order_data_arr as $keyCnt => $ordersDataArr) {
			$b++;
			$cnt						= $keyCnt+1;
			$print_orders_data_id		= $ordersDataArr["print_orders_data_id"];
			$order_name					= 'Order '.$cnt;
			$showInfo					= "yes";
			$ptDocsDateTime				= $ordersDataArr['pt_docs_date_time'];
			$operatorName				= stripslashes($ordersDataArr['operator_name']);
			
			$tree->addToArray($b,$order_name,$a,"load_pt_orders.php?print_orders_data_id=".$print_orders_data_id."&mode=print","ifrm_FolderContent","pdf-icon","remove-icon","javascript:delDocs('$print_orders_data_id')","","Delete","","","",$showInfo,$ptDocsDateTime,$operatorName,"","","",true,"","","",true);	
		}
		$a=$b;
	}
	$p=$a;
	$p++;
	$tree->addToArray($p,"Insurance Cards",$z,"","",$initPtDocInsClass);
	$a=$p;
	if(is_array($insCardData) && count($insCardData) > 0 ) {
		foreach($insCardData as $insCase => $insType )
		{
			$a++;
			$tree->addToArray($a,$insCase,$p,"","","icon-folder-filled");
			if(is_array($insType) && count($insType) > 0 ) 
			{
				$b=$a;
				foreach($insType as $ins_type => $docArr ) 
				{
					$b++;
					$tree->addToArray($b,$ins_type,$a,"","","icon-folder-filled");
					if( is_array($docArr) && count($docArr) > 0 ) 
					{
						$c=$b;
						foreach($docArr as $doc )
						{
							$c++;
							$docName = $doc['name'];
							$docPath = $doc['path'];
							$fileExt = pathinfo($doc['path'], PATHINFO_EXTENSION);
							$fileExt = strtolower($fileExt);
							$fileExt = $fileExt == 'jpeg' ? 'jpg' : $fileExt;
							$icon = in_array($fileExt,array('pdf','jpg','png','gif')) ? $fileExt."-icon" : "pdf-icon";

							$url = "load_pt_docs.php?pth=".base64_encode($docPath)."&mode=ins";
							if( $fileExt == 'pdf' ) {
								$url = $docPath;
							}
							$tree->addToArray($c,$docName,$b,$url,"ifrm_FolderContent",$icon,"","","","","","","","No","","","","","",true);
						}
						$b=$c;
					}
				}
				$a=$b;
			}
		}
	}
	// get data here 
	$p=$a;
	
	
	//TEST ==
	$p++;
	$tree->addToArray($p,"Interpretations",$z,"","",$initPtDocIntrClass);
	$a=$p;
	if(is_array($interpretations) && count($interpretations) > 0 ) {
		foreach($interpretations as $interkey => $interval )
		{
			$a++;
			$url = "load_pt_docs.php?pth=".base64_encode($interval["id"])."&mode=intrprttns&pid=".base64_encode($interval["pid"])."&fid=".base64_encode($interval["fid"])."&exam_name=".base64_encode($interval["exam_name"]);
			$tree->addToArray($a,$interkey,$p,$url,"ifrm_FolderContent",$icon,"","","","","","","","No","","","","","",true);
		}
	}
	// get data here 
	$p=$a;
	//TEST ==
	

}
/*		
$p++;
$tree->addToArray($p,"Outgoing Fax",$z,"");
$a=$p;
foreach($faxSent as $fax_date => $faxes) {
	$a++;
	$tree->addToArray($a,$fax_date,$p,"");
	$b=$a;
	foreach($faxes as $keyCnt => $fax) {
		$b++;
		$fax_files			= $fax["files"];
		$fax_link			= $fax["link"];
		$confirm_img		= $fax["success"];
		$showInfo			= "yes";
		$ptDocsDateTime		= $fax['pt_docs_date_time'];
		$operatorName		= stripslashes($ordersDataArr['operator_name']);
		$fax_number			= $fax['fax_number'];
		$tree->addToArray($b,$fax_files,$a,$fax_link,"ifrm_FolderContent","pdf-icon",$confirm_img,"javascript:void(0)","","","","","",$showInfo,$ptDocsDateTime,$operatorName,$fax_number);	
	}
	$a=$b;
}
$p=$a;

$p++;
$tree->addToArray($p,"Incoming Fax",$z,"");
$a=$p;
foreach($faxDocs as $fax_date => $faxes) {
	$a++;
	$tree->addToArray($a,$fax_date,$p,"");
	$b=$a;
	foreach($faxes as $keyCnt => $fax) {
		$b++;
		$fax_id				= $fax["id"];
		$fax_files			= $fax["files"];
		$fax_link			= $fax["link"];
		$showInfo			= "yes";
		$ptDocsDateTime		= $fax['pt_docs_date_time'];
		$faxNumber			= $fax['from'];
		
		$operatorName		= "";
		
		$tree->addToArray($b,$fax_files,$a,$fax_link,"ifrm_FolderContent","pdf-icon","restore-icon","javascript:restoreFax('$fax_id')","","Move to Pending","","","",$showInfo,$ptDocsDateTime,"",$faxNumber);
	}
	$a=$b;
}
$p=$a;
*/
        ?>
<!--    </div>    
</div> 
<div class="row">
	<div class=" col-xs-12 " style="height:<?php //echo ($col_height/2);?>px; max-height:100%; overflow:scroll">
-->    	<?php
if($_REQUEST["doc_name"]=="pt_docs_template") {				
	$p++;
	if(!$subTemplateCnt) {
		$subTemplateCnt = $p;
	}	
	$p++;
	$tree->addToArray($p,"Pt. Docs Templates",$subTemplateCnt,"","","icon-folder","","","","","","","","","","","","","active");
	$a=$p;
	foreach($unsavedTemplateArr as $category_name => $cat_arr) {
		$a++;
		$tree->addToArray($a,$category_name,$p,"");
		$b=$a;
		foreach($cat_arr as $keyCat => $catVal) {
			$b++;
			$ptDocsTemplateId	= $cat_arr[$keyCat]["pt_docs_template_id"];
			$ptDocsTemplateName	= $cat_arr[$keyCat]["pt_docs_template_name"];
			$tree->addToArray($b,$ptDocsTemplateName,$a,"load_pt_docs.php?temp_id=".$ptDocsTemplateId."&mode=load","ifrm_FolderContent","glyphicon-open-file");	
		}
		$a=$b;
	}
	$p=$a;
}
include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
$p++;

$tree->writeCSS();
$tree->writeJavascript();
$tree->drawTree();
		?>
<!--    </div>    
</div>    
-->
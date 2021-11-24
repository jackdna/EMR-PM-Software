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
File: logoImg.php
Purpose: This file provide Images to display as well as Flex function.
Access Type : Direct
*/
?>
<?php
ob_start();
require_once(dirname(__FILE__)."/../../config/globals.php");
$updir=substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);

//-----  Get data from remote server -------------------

//$zRemotePageName = "logoImg";
//require(dirname(__FILE__)."/get_chart_from_remote_server.inc.php");

//-----  Get data from remote server -------------------

include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/classes/common_function.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/wv_functions.php");

$pId = $_SESSION["patient"];
if(empty($pId)){
	exit("Please select patient.");
}
$strHtml = "";
//Obj Save File --
$oSaveFile = new SaveFile($pId);

$showFor = $_REQUEST['from'];
$scan_id = (int)$_REQUEST['scan_id'];
$prScan_id = (int)$_REQUEST['prScan_id'];
$headery = $_REQUEST["headery"];

/*Check Templatebased tests*/
$res_chkTemplate = imw_query("SELECT image_form,test_id FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id = '$scan_id'");
$rs_chkTemplate = imw_fetch_assoc($res_chkTemplate);
$where_query_part = "";
if($rs_chkTemplate['image_form']=='TemplateTests'){
	$where_query_part = " AND c2.test_id='".$rs_chkTemplate['test_id']."'";
}

$getimage_form = $_REQUEST["image_form"];
if(!$getimage_form) {
	$getimage_form = $rs_chkTemplate['image_form'];	
}
if($getimage_form=="ptInfoAdvancedDirective" || $getimage_form=="ptInfoMedHxGeneralHealth") {
	$where_query_part = " AND c1.image_form = '".$getimage_form."' AND c1.patient_id = '".$_SESSION['patient']."' ";	
}

//START CODE TO GET LOG OF SCAN
if($scan_id!='0') {
	$insrtLog 	= providerViewLogFun($scan_id,$_SESSION['authId'],$_SESSION['patient'],'tests');
}
//END CODE TO GET LOG OF SCAN

if($showFor == 'scanImage' && $getimage_form!="ptInfoAdvancedDirective" && $getimage_form!="ptInfoMedHxGeneralHealth"){ //Pdfs
	$getImageQry = imw_query("SELECT image_name,file_path FROM ".constant("IMEDIC_SCAN_DB").".scans 
								WHERE scan_id = '$scan_id'");
	$getImageRow = imw_fetch_assoc($getImageQry);
	//Header To File Location --
	if(!empty($getImageRow["file_path"])){
		$chkPth = urldecode($updir.$getImageRow["file_path"]);
		$pth ="";
		if(file_exists($chkPth)){
			$pth = $srcDir.$getImageRow["file_path"];
		}
		else{
			$arrFilePath = explode("/",$getImageRow["file_path"]);
			$fileNameNew = urlencode($getImageRow["image_name"]);
			$totIndices = count($arrFilePath);
			$arrFilePath[$totIndices-1] = $fileNameNew;
			$newFilePath = implode("/",$arrFilePath);
			$chkPth = urldecode($updir.$newFilePath);
			if(file_exists($chkPth)){
				$pth = $srcDir.$newFilePath;
			}			
		}
		if(!empty($pth)){			
			header("Location: ".checkUrl4Remote($pth));
		}else{
			exit("<br/>File not exists.");
		}
	}
	//Header To File Location --		
	exit();	
}
else if($showFor == "Flex" || $getimage_form == "ptInfoAdvancedDirective" || $getimage_form == "ptInfoMedHxGeneralHealth"){ //images
	$sPth = "../../flx_zoomr/Index/bin-debug/";		
	$sDir = "images/";
	$tDir = "images/thumbs/";	
	$arr = $arrT = $arraDocUploadDate = array();
	$flgImg = 1;
	$selectedImageTumb = $selectedImageMain = "";
	$sql = "SELECT DATE_FORMAT(c1.doc_upload_date, '".get_sql_date_format()."') docUploadDate,c2.* FROM ".constant("IMEDIC_SCAN_DB").".scans c1, ".constant("IMEDIC_SCAN_DB").".scans c2 
			WHERE c1.scan_id in('$scan_id','$prScan_id') 
			AND ((c1.form_id != 0 AND c1.form_id = c2.form_id) OR (c1.test_id != 0 AND c1.test_id = c2.test_id) )
			AND c1.image_form = c2.image_form ".$where_query_part." 
			ORDER BY scan_id DESC
			";		
	if($getimage_form == "ptInfoAdvancedDirective" || $getimage_form == "ptInfoMedHxGeneralHealth") {
		$sql = "SELECT c1.*, DATE_FORMAT(c1.created_date, '".get_sql_date_format()."') docUploadDate FROM ".constant("IMEDIC_SCAN_DB").".scans c1 WHERE 1=1 ".$where_query_part;
	}
	$rez = imw_query($sql);		
	for($i=0; $i < imw_num_rows($rez); $i++){

		//
		set_time_limit(10);	
						
		$row=imw_fetch_array($rez);
		if(!empty($row["file_path"])){			
			$pth = $srcDir.$row["file_path"];
			$pth = checkUrl4Remote($pth);
			//Create Thumbs
			$pth_fs = $updir.$row["file_path"];
			$file_info = pathinfo($pth_fs);
			$file_info_short = pathinfo($row["file_path"]);
			if(file_exists($pth_fs) == true){
				if(strtolower($file_info["extension"]) != "pdf" && $row["file_type"] != "application/pdf"){								
					$arraDocUploadDate[] = $row["docUploadDate"];
					//$pthThmb = $oSaveFile->createThumbsFromIMagick($pth_fs,"","500","200");
					$pthThmbTmp = $file_info_short["dirname"]."/thumbnail/".$file_info["basename"];
					if(file_exists($updir.$pthThmbTmp)) {
						$pthThmb = $pthThmbTmp;
					}else {
						$pthThmb = $oSaveFile->createThumbs($pth_fs,"","280","150");
					}
					$tempImgWH = "";				
					if(is_array($pthThmb) == true){						
						$selectedImageTumb = $pth;
						$arr[] = $pth; // Full Image					
						$tempImgWH = "width:".$pthThmb["imgWidth"]."px; height:".$pthThmb["imgHeight"]."px;";
						$arrT[] = array("image" => $pth, "size" => $tempImgWH);
					}
					else{			
						$arr[] = $pth; // Full Image
						//$arrT[] = $GLOBALS['rootdir']."/main/uploaddir".$pthThmb; // Thumbs
						$imagePath = $srcDir.$pthThmb; //Thumbs
						$imagePath = checkUrl4Remote($imagePath);
						$arrT[] = array("image" => $imagePath, "size" => "");
					}
					if((trim($row["scan_id"]) == trim($scan_id)) && (empty($selectedImage) == true) && (empty($selectedImageMain) == true)){
						$selectedImageMain = $pth;
						$selectedImageTumb = checkUrl4Remote($GLOBALS['rootdir']."/main/uploaddir".$pthThmb);
					}
					$flgImg = 1;
				}
			}
		}				
	}
	if(!$selectedImageMain) {
		$selectedImageMain =$arr[0]; 	
	}
	//print_r($arraDocUploadDate);		
	if(($flgImg == 1) && (count($arr) > 0)){				
			
		//Show images in HTML ONLY 
		$strHtml .= "<html>";
			$strHtml .= "<head>".
						"<title>images</title>".
						"<link type=\"text/css\"  href=\"".$GLOBALS['webroot']."/library/css/style.css\" rel=\"stylesheet\">".
						"<link type=\"text/css\"  href=\"".$GLOBALS['webroot']."/library/css/common.css\" rel=\"stylesheet\">".
						"<link type=\"text/css\"  href=\"".$GLOBALS['webroot']."/library/css/bootstrap.css\" rel=\"stylesheet\">".
						"<style type=\"text/css\">
							#img.source-image {
								width: 100%;
								position: absolute;
								top: 0;
								left: 0;
							}
							.imgTumbFixDimention img{width:210px;height:190px;}
						</style>".
						 "<script>
						 function showBigImage(pth,obj){								
							var temp = parseInt(document.getElementById(\"div1\").style.width)-20;
							document.getElementById(\"imgBig\").style.width=temp;	
							document.getElementById(\"imgBig\").src = \"\"+pth;						
							var divs = document.getElementsByTagName('div');							
							for (var i = 0; i < divs.length; i++){
								var temp = divs[i].id;								
								if(temp.search(\"divInner\")==0){								
									document.getElementById(temp).style.border=\"0px\";	
								}								
							}								
							document.getElementById(obj).style.border=\"2px solid\";
						 }
						 function setWidthDiv(){							 	
							document.getElementById(\"mainDiv\").style.width=document.body.clientWidth;		
							var clientWidthHalf = parseInt(document.body.clientWidth)/2;
							clientWidthHalf = parseInt(clientWidthHalf)/2;							
							var div1Width = parseInt(document.body.clientWidth) - parseInt(clientWidthHalf);
							
							document.getElementById(\"div1\").style.width=div1Width+80;
							var imgBigWidth = parseInt(document.getElementById(\"div1\").style.width)-20;
							document.getElementById(\"imgBig\").style.width=imgBigWidth;
							document.getElementById(\"div2\").style.width=clientWidthHalf-100;													
							document.getElementById(\"div1\").style.display=\"block\";
							//alert(document.getElementById(\"div2\").style.width);
						 }
						 </script>".
						 "</head>";
			$strHtml .= "<body topmargin=\"0\" leftmargin=\"0\" class=\"scrol_Vblue_color\" onLoad=\"setWidthDiv();\">";				
			$len = count($arrT);
			if($len > 0){			
				//Show Div for Big image 
				$strHtml .= "<div class=\"panel panel-primary\" id=\"mainDiv\" style=\"overflow:hidden; width:100%; height:93%\">";
				$strHtml .= "<div id=\"div1\" style=\"overflow:auto; display:none; float:left; width:85%; height:100%;\">".
								"<img id=\"imgBig\" src=\"".$selectedImageMain."\" alt=\"Image here\" />".
							 "</div>";
					 
				//Show Div for image thumbs
				$strHtml .= "<div id=\"div2\" style=\"overflow:auto;  float:right; width:15%; height:100%; vertical-align:top; \">";						
					for($i=0;$i<$len;$i++){
						$id = "";
						$id = "divInner".$i;
						$border = "";
						if($selectedImageTumb == $arrT[$i]){
							$border = "border: 2px solid;";
						}
						$strHtml .= "<div id=".$id." class=\"imgTumbFixDimention-notuse\" style=\"text-align:center; ".$border."\">
										<img  src=\"".$arrT[$i]['image']."\" alt=\"Image here\" style=\"cursor:hand; text-align:center; ".$arrT[$i]['size']."\" onclick=\"showBigImage('".$arr[$i]."','".$id."')\" />
									</div>
									";
						if($arraDocUploadDate[$i]!="00/00/0000"){			
							$strHtml .= "<div id=dateDiv".$id." class=\"txt_11\" style=\"text-align:center;  background-color:#f3f3f3;\">
											".$arraDocUploadDate[$i]."
										</div>
										";
						}			
					}
				$strHtml .= "</div>
						</div>
						<footer id=\"module_buttons\" class=\"panel-footer\">
							<input type=\"button\"  class=\"btn btn-danger\" id=\"Close\" value=\"Close\" onClick=\"window.close();\">
						</footer>";
			}
			//else{
				//$strHtml .= "<div style=\"height:auto%;width:auto; overflow : auto;\" id=\"imageContent\"><img id=\"imgBig\" src=\"".$arr[0]."\" alt=\"Image here\" /></div>";					
			//}
			$strHtml .= "</body>";
		$strHtml .= "</html>";
		//Show images 
	}
	//if images, display fx
	echo ("".$strHtml."");
}
?>
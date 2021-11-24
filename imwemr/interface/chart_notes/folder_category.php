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
File: folder_category.php
Purpose: This file provides all folders created for a patient and default ones.
Access Type : Direct
*/
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/patient_must_loaded.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");

$library_path = $GLOBALS['webroot'].'/library';
$redirectPth = $GLOBALS['rootdir']."/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs";
$pid = $_SESSION['patient'];
if(!empty($pid)){
	$oSaveFile = new SaveFile($pid);
}
$rootDir = substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);
$GLOBALDATEFORMAT = $GLOBALS["date_format"];
$cat_id=$_REQUEST['cat_id'];
$img1="<img src=\"".$library_path."/images/close_fold_new.png\" border=\"0\">";
$img2="<img src=\"".$library_path."/images/close_fold_new.png\" border=\"0\">";
if($_REQUEST['cat_id']<>"")	{
	$cat_id=$_REQUEST['cat_id'];
	$disp="none";
}else	{
	$cat_id=$_REQUEST['folder_id'];
	$disp="block";
}
if($cat_id!=0)	{
	$qry = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE parent_id=$cat_id AND folder_status ='active'and (patient_id='$pid' || patient_id=0) and parent_id=$cat_id AND folder_status ='active' order by folder_categories_id";
}else 	{
	$qry = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE parent_id=0 AND folder_status ='active'and (patient_id='$pid' || patient_id=0) and parent_id=0 AND folder_status ='active' order by folder_categories_id";
}
$dia_res=imw_query($qry);
$dia_num=imw_num_rows($dia_res);
if($dia_num<=0)	{
	$norec="No Record Found";
}
	$query = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE parent_id=0 AND folder_status ='active' and patient_id='$pid' || patient_id=0 and parent_id=0 AND folder_status ='active' order by folder_categories_id";

	$resultSet = imw_query($query) or die(mysql_error()); 

	

	if(imw_num_rows($resultSet))

	{

		$mainArr = array();

		$tempArr = array();

		while($row = imw_fetch_assoc($resultSet))

		{

			$level = 0;

			$categoryID = $row['folder_categories_id'];

			$categoryName = $row['folder_name'];

			$parentID = $row['parent_id'];
			
			$mainArr[$categoryID] = '&gt;&gt;'.$categoryName;

			$tempArr = getChild($categoryID, $level,$pid);
			
			$mainArr = mergeArr($mainArr,$tempArr);

		}

		$catArr = $mainArr;

	}
$col=4;

//START CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY
$ctId = scnfoldrCatIdFunNew(constant("IMEDIC_SCAN_DB"),'Medication');
$ChkMedDocExistsNumRow='';
if($ctId) {
	$ChkMedDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$pid,$ctId); //FUNCTION FROM common/scan_function.php	
}
$ChkAnyDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$pid,''); //FUNCTION FROM common/scan_function.php
//$scnImgSrcActive = $GLOBALS['webroot'].'/interface/chart_notes/images/scanDcs_active.png';
//$scnImgSrcDeActive = $GLOBALS['webroot'].'/interface/chart_notes/images/scanDcs_deactive.png';
$scnImgSrcActive = '';
$scnImgSrcDeActive = '';

if($ChkAnyDocExistsNumRow>0) { $scnImgSrcActive = scnDocReadChkFun($pid,'scan',$_SESSION['authId']); }


if(isset($_GET['scanId']) && $_GET['scanId'] != '') {
    $scanId = $_GET['scanId'];

    //set_text_scan($scanText, $value);	
    function set_text_scan($scan_id, $val) {
        $fieldName = 'doc_title';
        $chkQry = "SELECT scan_doc_id FROM " . constant("IMEDIC_SCAN_DB") . ".scan_doc_tbl where scan_doc_id = '" . $scan_id . "' AND doc_title = '' AND pdf_url != ''";
        $chkRes = imw_query($chkQry);
        if (imw_num_rows($chkRes) > 0) {
            $fieldName = 'pdf_url';
        }
        $sql = "UPDATE " . constant("IMEDIC_SCAN_DB") . ".scan_doc_tbl set " . $fieldName . " = '$val' where scan_doc_id = '$scan_id' ";
        $res = imw_query($sql);

        echo $val;
    }

    if ($value <> "") {
        echo set_text_scan($scanId, $value);
    }

    die();
}
?>

<script language="javascript">
	var scnImgSrc=scnImgMedSrc=scan_img_val_med='';
	var anyDocExistsNumRow	='<?php echo $ChkAnyDocExistsNumRow;?>';
	if(anyDocExistsNumRow>0) {
		scnImgSrc 	= '<?php echo $scnImgSrcActive;?>';
	}else {
		scnImgSrc 	= '<?php echo $scnImgSrcDeActive;?>';
	}//alert(scnImgSrc);
	scan_img_val 	= '<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
	
	//START CODE TO SET SCAN-ICON IN GENERAL HEALTH MEDICATION(IF MEDICATION EXISTS)
	var medDocExistsNumRow	= '<?php echo $ChkMedDocExistsNumRow;?>';
	if(medDocExistsNumRow>0) {
		scnImgMedSrc		= '<?php echo $scnImgSrcActive;?>';
		scan_img_val_med	= '<img src="'+scnImgMedSrc+'"  style="cursor:pointer;" vspace="0" border="0" align="middle" title="Scan Docs" onClick="window.open(\'scan_docs/index.php?med_type=Medication\',\'scanDocs\',\'resizable=yes,scrollbars=1,location=yes,status=yes, width=1000 height=700\');" >';
	}
	//END CODE TO SET SCAN-ICON IN GENERAL HEALTH MEDICATION(IF MEDICATION EXISTS)
</script>
<?php
//END CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY
?>
<html>
<head>
	<title>Scan Docs</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
	<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
    
	<style>
        a {
            font-weight:bold;
            color:#673782;
            font-family:"robotolight";
            font-size:14px;
            text-decoration:none;	
        }
        a:focus {
            text-decoration:none;
            color:#673782;	
        }
        a:hover {
            text-decoration:none;
            color:#673782;	
        }
    </style>
<script>
var oPP = new Array();

top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';

function GetXmlHttpObject()
{ 
		var objXMLHttp=null
		if (window.XMLHttpRequest)
		{
		objXMLHttp=new XMLHttpRequest()
		}
		else if (window.ActiveXObject)
		{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
		}
		return objXMLHttp
}
function scan_doc(id)	{
	if(!id) {
		id = '<?php echo $_REQUEST["cat_id"];?>';	
	}
	if(!id) {
		id = '<?php echo $_REQUEST["folder_id"];?>';	
	}
	if(typeof(top.showHideScanUploadBt)!="undefined"){top.showHideScanUploadBt("hide");}
	if(id!=0)	{
		top.show_loading_image("show",""," Loading...");
		top.fmain.ifrm_FolderContent.location.href=top.JS_WEB_ROOT_PATH+'/interface/chart_notes/scan_documents.php?folder_id='+id+'&sb=no';
		return true;
	}else	{
		top.fAlert("Please click on folder");
		return false;
	}
}
function upload_image(id)	{
	if(!id) {
		id = '<?php echo $_REQUEST["cat_id"];?>';	
	}
	if(!id) {
		id = '<?php echo $_REQUEST["folder_id"];?>';	
	}
	if(typeof(top.showHideScanUploadBt)!="undefined"){top.showHideScanUploadBt("hide");}
	if(id!=0)	{		
		top.show_loading_image("show",""," Loading...");
		top.fmain.ifrm_FolderContent.location.href=top.JS_WEB_ROOT_PATH+'/interface/chart_notes/common/upload_multi_docs.php?folder_id='+id+'&sb=no'; //upload_documents.php
		return true;
	}else	{
		top.fAlert("Please click on folder");
		return false;
	}
}

function date_add(valdate,id,type,file_name,cnfrm){
	
	GLOBALDATEFORMAT = "<?php echo $GLOBALS["date_format"]; ?>";
	
	if(GLOBALDATEFORMAT = "dd-mm-yyyy" && GLOBALDATEFORMAT != "")
	{
		date_global_format = "dd-mm-yyyy";
	}
	else
	{
		date_global_format = "mm-dd-yyyy";
	}
	
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	  {
	  	top.fAlert ("Browser does not support HTTP Request")
	 	 return
	  } 
	 	if(type=="update" && valdate=="")	{
				top.fAlert("Please enter date in  "+ date_global_format +" format");
		}else if(id!="" && valdate!="")	{
					var url="folder_scan_date.php"
					url=url+"?cdate="+valdate+"&id="+id+"&type="+type
					xmlHttp.onreadystatechange=status_fun
					xmlHttp.open("GET",url,true)
					xmlHttp.send(null);
		}
		if(type=="del") {
			if (typeof(cnfrm)=='undefined') {
				top.fancyConfirm('Do you want to delete selected?','',"top.fmain.ifrm_FolderContent.date_add('"+valdate+"','"+id+"','"+type+"','"+file_name+"',true)");
				return;
			}else{
				top.show_loading_image("show",""," Loading...");
				var url="folder_scan_date.php"
				url=url+"?cdate="+valdate+"&id="+id+"&type="+type+"&file_name="+file_name
				xmlHttp.onreadystatechange=status_fun
				xmlHttp.open("GET",url,true)
				xmlHttp.send(null);
			}

		}
		
		
}
function remove(folder_name,folder_id,cnfrm)	{	
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	  {
	  	top.fAlert ("Browser does not support HTTP Request")
	 	 return
	  } 
	  if(folder_name!="" && folder_id!="")	{
		
		if (typeof(cnfrm)=='undefined') {
				top.fancyConfirm('Do you want to remove this folder ? This will also delete all the files contains in this folder','',"top.fmain.ifrm_FolderContent.remove('"+folder_name+"','"+folder_id+"',true)");
				return;
		}else{
			var url="folder_scan_date.php"
			url=url+"?folder_id="+folder_id+"&type=remove"
			xmlHttp.onreadystatechange=status_fun2
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null);
		}
		
	  }
}
function status_fun2()	{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
	 		var changecolor='#F6C67A';
			result=xmlHttp.responseText;
			top.fAlert("Folder has been deleted with all files");
			var cat_id = '<?php echo $_REQUEST["cat_id"];?>';	
			var redirectPth = '<?php echo $redirectPth;?>';	
			if(!cat_id) {
				cat_id = '<?php echo $_REQUEST["folder_id"];?>';	
			}
			top.frames['fmain'].location.href = redirectPth+"&cat_id="+cat_id;
	}
}
function move(folder_id,type,file_id)	{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	  {
	  	alert ("Browser does not support HTTP Request")
	 	 return
	  } 
	if(type=="move")	{
		if(folder_id=="")	{
			top.fAlert("Please choose the folder to move the file");
		}else	{
			var url="folder_scan_date.php"
			url=url+"?folder_id="+folder_id+"&file_id="+file_id+"&type="+type
			xmlHttp.onreadystatechange=status_fun
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null);
		}
		
	}	
}
function status_fun()	{
	var cat_id = '<?php echo $_REQUEST["cat_id"];?>';	
	var redirectPth = '<?php echo $redirectPth;?>';	
	if(!cat_id) {
		cat_id = '<?php echo $_REQUEST["folder_id"];?>';	
	}
	
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
	 		var changecolor='#F6C67A';
			result=xmlHttp.responseText;
			if(result=="update")	{
				top.fAlert("Record has been updated");
			}else if(result=="del")	{
				top.fAlert("Record has been deleted");
				if(cat_id && redirectPth && top.frames['fmain']) {
					top.frames['fmain'].location.href = redirectPth+"&cat_id="+cat_id;
				}else {
					window.location.reload();
				}
			}else if(result=="move")	{
				top.fAlert("File has been moved");
				if(cat_id && redirectPth && top.frames['fmain']) {
					top.frames['fmain'].location.href = redirectPth+"&cat_id="+cat_id;
				}else {
					window.location.reload();
				}
			}
	}
}
function y2k(number)
{
	return (number < 1000)? number+1900 : number;
}

var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());

function padout(number)
{
	return (number < 10) ? '0' + number : number;
}

function Text_scan(id)
{
	document.getElementById('txt_'+id).style.display = 'block';
	document.getElementById('doc_id'+id).style.display = 'none';
	document.getElementById('txt_'+id).value = document.getElementById('doc_id'+id).innerHTML;
}	

function large(id,logo,Ext)	{
	var n = "scanpop_"+id+"_"+Ext;
	if(!oPP[n] || !(oPP[n].open) || (oPP[n].closed == true)){	
		oPP[n] = window.open('show_image.php?id='+id+'&ext='+Ext,''+n,'width=450,height=550,resizable=yes,scrollbars=yes');
		oPP[n].focus();
	}else{
		oPP[n].focus();
	}
}

function open_pdf(file_name,fileid){
	file_name = encodeURIComponent(file_name);
	var n = "scanpop_"+file_name;
	if(!oPP[n] || !(oPP[n].open) || (oPP[n].closed == true)){		
		var id = '<?php echo $pid; ?>';
		oPP[n] = window.open('show_image.php?pid='+id+'&file_name='+file_name+'&id='+fileid,''+n,'width=450,height=550,resizable=yes,scrollbars=yes');
		oPP[n].focus();
	}else{
		oPP[n].focus();
	}
}

/*
Following function is used in Scan Docs.
*/
function refresh_left_navi(){
	if(typeof(parent.refrashNavi) == "function"){
		parent.refrashNavi();
	}
}

function setPopRefArr(){	
	if(typeof top.oPP2 != "undefined"){
		oPP = top.oPP2;		
	}else if(typeof top.opener.oPP != "undefined"){
		oPP = top.opener.oPP;		
	}else{
		oPP = new Array();
	}	
}
/* -- */
//
function hideUnRdDocInner(id) {
	var obj  = document.getElementById('spnUnreadDocId'+id);
	var objNavi = top.document.getElementById('spnUnreadDocNaviId'+id);
	if(obj) {
		obj.innerHTML='';	
	}
	if(objNavi) {
		objNavi.innerHTML='';	
	}
}

</script>
</head>
<body>
<!---->	
<div class="col-xs-12 bg-white" >
    <div class="row" >
        <div class=" col-xs-12 " ></div>
    	<div class="col-xs-12 ">&nbsp;</div>
        <div class="col-xs-12 "><?php  echo folder_breadCrumb($cat_id,'');?></div>
        <div class="col-xs-12 ">&nbsp;</div>
        <?php
		if($dia_num>0){
		?>
        <div class="col-xs-12 "><b>Documents Folder</b></div>
        <div class="col-xs-12 ">&nbsp;</div>
        <div class="col-xs-12 ">
			<?php
            $c=1;
            while($row=imw_fetch_array($dia_res)){
                $folder_name = $row['folder_name'];
                $patient_id = $row['patient_id'];
                if($patient_id==$pid){
                    $img = $img2;
                }else{
                    $img = $img1;
                }
			?>
	            <div class="col-xs-3 ">
                	<div class="col-xs-12 ">
                    	<a href="folder_category.php?cat_id=<?php echo $row['folder_categories_id'];?>" class=""><?php echo $img;?><br>&nbsp;&nbsp;<?php echo $folder_name;?></a>
            		</div>
                    <?php
					if($patient_id==$pid){
					?>
                    <div class="col-xs-12 ">
                    	<a href="#" onClick="remove('<?php echo $folder_name;?>','<?php echo $row['folder_categories_id'];?>');" title="Delete"><img src="<?php echo $library_path.'/images/delete_icon.png';?>" border="0"></a>
                    </div>
                    <?php
					}
					?>
                    <div class="col-xs-12 "></div>
                    <div class="col-xs-12 "></div>
                </div>
			<?php	
			}
            ?>
        </div>
        <?php
		}
		?>
	</div>
</div>

<div class="col-xs-12 bg-white" >
	<?php 
    
	if($cat_id!=0){
		$qry11="Select *, DATE_FORMAT(task_review_date,'".get_sql_date_format('','Y','-')." %h:%i %p') AS task_review_date_new from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where folder_categories_id=$cat_id and patient_id='$pid' order by (upload_docs_date + upload_date) DESC, scan_doc_id DESC"; //and pdf_url <> '' ";						
		$res11=imw_query($qry11);
		if(imw_num_rows($res11)>0){?>
    		<div class="col-xs-12 bg-white" ><b>Patient Scan Documents</b></div>
			<?php
            $c1=1;
            $scanUploadDate='';
            $oprtInitial='';
            $oprtComments='';
            while($row11=imw_fetch_array($res11)){
                $doc_type = $row11['doc_type'];
                //$imag_name="folder_thumbnail.php?id=$row11[0]";
                $file_path = $row11['file_path'];
                //START CODE TO GET DATE AND OPERATOR INITIAL OF SCANED/UPLOADED DOCUMENT
                $doc_upload_type 		= $row11['doc_upload_type'];
                
                $task_review_date_new	= $row11['task_review_date_new'];
                $task_review_date_show	= "";
                if(substr($task_review_date_new,0,10)!=0 && preg_replace('/[^0-9]/','',substr($task_review_date_new,0,10))!="00000000") {
                    $task_review_date_show = $task_review_date_new;
                }
                if($doc_upload_type=='scan') {
                    $scanUploadDate 	= get_date_format(date('Y-m-d',strtotime($row11['upload_date'])))." ".date('h:i A',strtotime($row11['upload_date']));
                    $oprtInitial 		= operatorIntialFun($row11['scandoc_operator_id']);
                    $oprtComments 		= stripslashes($row11['scandoc_comment']);
                }else if($doc_upload_type=='upload') {
                    $scanUploadDate 	= get_date_format(date('Y-m-d',strtotime($row11['upload_docs_date'])))." ".date('h:i A',strtotime($row11['upload_docs_date']));
                    $oprtInitial 		= operatorIntialFun($row11['upload_operator_id']);
                    $oprtComments 		= stripslashes($row11['upload_comment']);
                }
                
                //END CODE TO GET DATE AND OPERATOR INITIAL OF SCANED/UPLOADED DOCUMENT
                $scnDocUnReadImage = scnDocUnReadImageFun($pid,'scan',$_SESSION['authId'],$row11['scan_doc_id']);
			?>
                <div class="col-xs-3 bg-white" >
                    <div class="col-xs-12 ">
						<?php $oprIntPadd='10px';
                        $tempImgWH = "";
                        if(!empty($row11["doc_title"])){
    
                            if(($row11["doc_type"])=='rtf'){ 
                                echo "<a href='".$web_root."/library/RTF2HTML/index.php?file_root=pt_doc_root&to_format=html&file_src=".urlencode($row11["file_path"])."' ><img src='../../library/images/icon_rtf_doc.png'></a></a>";
                            }
                            else if(($row11["doc_type"])=='doc'){
                                echo "<a href='".$srcDir.$row11["file_path"]."'><img src='../../library/images/icon_doc.png'></a>";
                            }
                            else if(($row11["doc_type"])=='html'){ 
                                echo "<a href='".$srcDir.$row11["file_path"]."' ><img src='../../library/images/icon_html_doc.png'></a>";
                            }
                            else if(($row11["doc_type"])=='tif'){
                                $thmb_path = $oSaveFile->createThumbs($rootDir.$file_path,"",110,100);
                                if(is_array($thmb_path) == true){
                                    $tempImgWH = "style=\"width:".$thmb_path["imgWidth"]."px; height:".$thmb_path["imgHeight"]."px;\"";
                                    if($GLOBALS['gl_browser_name']!='ie'){
                                        $thmb_path = substr($oSaveFile->getFilePath($file_path, "w"),0,-4).'_thumb.jpg';
                                    }else{
                                        $thmb_path = $oSaveFile->getFilePath($file_path, "w");
                                    }
                                }
                                else{
                                    $thmb_path = $srcDir.$thmb_path;
                                }
                                if($thmb_path['imgWidth']=="" || $thmb_path['imgHeight']){
                                    $tempImgWH = "style=\"width:100px; height:100px;border:1px solid #CCC;padding:5px;\"";
                                }
                                ?>
                                <a href="javascript:large('<?php echo $row11['0'];?>','logo<?php echo $c1;?>','<?php print $doc_type; ?>');">
                                <img title="click here to view large" name="logo<?php echo $c1;?>"  src="<?php echo $thmb_path;?>" <?php echo $tempImgWH;?> border="0" onClick="javascript:parent.hideUnRdDoc('<?php echo $row11['scan_doc_id'];?>');"></a>
                            <?php $oprIntPadd='5px';
                                }else{	
                                $thmb_path = $oSaveFile->createThumbs($rootDir.$file_path,"",200,150);
                                if(is_array($thmb_path) == true){
                                    $tempImgWH = "style=\"width:".$thmb_path["imgWidth"]."px; height:".$thmb_path["imgHeight"]."px;\"";
                                    $thmb_path = $oSaveFile->getFilePath($file_path, "w");
                                }
                                else{
                                    $thmb_path = $srcDir.$thmb_path;
                                }
                                if($doc_type == "pdf"){
                                    if(constant("STOP_CONVERT_COMMAND")=="YES") {
                                        $thmb_path = $library_path."/images/icon-pdf.png";
                                    }else {
                                        $thmb_path_file = $rootDir.$file_path;
                                        $thmb_path="common/pdf_thumb.php?pdf=".$thmb_path_file."&size=150";
                                    }
                                }?>
                                <a href="javascript:large('<?php echo $row11['0'];?>','logo<?php echo $c1;?>','<?php print $doc_type; ?>');">
                                <img title="click here to view large" name="logo<?php echo $c1;?>"  src="<?php echo $thmb_path;?>" <?php echo $tempImgWH;?> style="border:1px solid #CCC;" onClick="javascript:parent.hideUnRdDoc('<?php echo $row11['scan_doc_id'];?>');"></a>
                            <?php $oprIntPadd='5px';
                                }
                        } else if(!strpos($row11['pdf_url'],'.jpg') && 
                                            $row11['pdf_url'] != '' &&  $doc_type != "jpg") { 
                            if(constant("STOP_CONVERT_COMMAND")=="YES") {
                                $thmb_path = $library_path."/images/icon-pdf.png";
                            }else {
                                $thmb_path_file = $rootDir.$file_path;
                                $thmb_path="common/pdf_thumb.php?pdf=".$thmb_path_file."&size=150";
                            }
                        ?>													
                            <a href="javascript:open_pdf('<?php echo $row11['pdf_url']?>','<?php echo $row11['0']; ?>');"><img title="click here to view large" name="logo<?php echo $c1;?>"   src="<?php echo $thmb_path;?>" style="border:1px solid #CCC;" onClick="javascript:parent.hideUnRdDoc('<?php echo $row11['scan_doc_id'];?>');"></a><br><span class="text_10b" id="doc_id<?php echo $row11[0]; ?>" onClick="Text_scan('<?php echo $row11[0]; ?>');"><?php echo trim($row11['pdf_url']); ?> </span>
                        <?php 
                        } ?>
                    
                    </div>
                    <div class="col-xs-12 ">
                    	<span id="doc_id<?php echo $row11[0]; ?>" onClick="Text_scan('<?php echo $row11[0]; ?>');"><?php echo ucfirst(trim($row11[3]));?></span><input id="txt_<?php echo $row11[0]; ?>" type="text" value="" style="display:none;" onBlur="setText_scan('<?php echo $row11[0]; ?>');">
                    </div>
                    <div class="col-xs-12 ">
						<?php echo $oprtInitial.' - '.$scanUploadDate;?>
                        <span id="spnUnreadDocId<?php echo $row11['scan_doc_id'];?>"><?php echo $scnDocUnReadImage;?></span>
                    </div>
					<div class="col-xs-12 ">
                    	<?php 
							if($task_review_date_show) {
								echo "Reviewed on ".$task_review_date_show;
							}
						?>
                    </div>                    
                    <div class="col-xs-12 ">
                    	<?php echo $oprtComments;?>
                    </div>
                    <form name="chartform<?php echo $c1;?>" action="folder_category.php" method="get">
                    	<div class="col-xs-12 checkbox">
                        	<input type="hidden" name="cat_id" id="cat_id" value="<?php echo $_REQUEST['cat_id'];?>">Chart Note<input id="chart<?php echo $c1;?>" type="checkbox" <?php if($row11['chart_note_date']!="0000-00-00")	{ echo "checked"; $disp21="block";}else	{ $disp21="none";}?> onClick="if(this.checked){document.getElementById('disp_date2<?php echo $c1;?>').style.display='block';} else{	document.getElementById('disp_date2<?php echo $c1;?>').style.display='none';}" name="chart<?php echo $c1;?>"><label for="chart<?php echo $c1;?>" style="margin-left:25px;"></label>
                        </div>
                    	<div class="col-xs-12 " id="disp_date2<?php echo $c1;?>" style="display:<?php echo $disp21;?>;">
                            <div class="col-xs-5 " >
                                <span class="input-group" >
                                <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
                                <input  class="form-control datepicker" type="text" size="15" name="chartdate<?php echo $c1;?>" id="chartdate<?php echo $c1;?>" value="<?php if($row11['chart_note_date']!="0000-00-00")	
                                {
                                    if($GLOBALDATEFORMAT == "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
                                    {
                                        $dat=explode("-",$row11['chart_note_date']);	echo  $dat[2]."-".$dat[1]."-".$dat[0];
                                    }
                                    else
                                    {
                                        $dat=explode("-",$row11['chart_note_date']);	echo  $dat[1]."-".$dat[2]."-".$dat[0];
                                    }
                                }?>" >
                                
                            <label class="input-group-addon btn" for="chartdate<?php echo $c1;?>"><span class="glyphicon glyphicon-calendar"></span></label>
                            </span>
                            </div>
                             
                            <div class="col-xs-7"><input type="button" value="Save" class="btn btn-success" onClick="date_add(document.chartform<?php echo $c1;?>.chartdate<?php echo $c1;?>.value,'<?php echo $row11['scan_doc_id'];?>','update');"></div>
                        </div>
                        <div class="col-xs-12 " style="margin-top:2px;">
                            <input type="button" value="Edit" onClick="location.href='scan_documents.php?folder_id=<?php echo $cat_id;?>&editid=<?php echo $row11[0];?>';" class="btn btn-success">
                            <input type="button" value="Del" class="btn btn-danger" onClick="date_add(document.getElementById('chartdate<?php echo $c1;?>').value,'<?php echo $row11['scan_doc_id'];?>','del','<?php echo $row11['pdf_url']; ?>');">
                            <input type="button" value="Move" class="btn btn-success" onClick="move(document.getElementById('folder<?php echo $c1;?>').value,'move','<?php echo $row11[0];?>');">
                            <br>
                            <select name="parent_cat" class="selectpicker" id="folder<?php echo $c1;?>" style="width:150px; font-weight:bold;">
                                <option value="">Choose folder</option>
                                <?php
                                foreach($catArr as $key => $val){
                                    if($key == $parent_cat1)
                                        echo "<option value=$key selected><b>$val</b></option>";
                                    else
                                        echo "<option value=$key><b>$val</b></option>";
                                }
                                ?>
                            </select> 
                        </div>
                        <div class="col-xs-12 ">&nbsp;</div>
                    </form>
                </div>
            <?php
				if(($c1%4)==0) {
				?>
                	<div class="clearfix"></div>
                <?php		
				}
				$c1++;
			}
		}
	}
	?>
	<div class="col-xs-12 ">&nbsp;</div>
    <div class="col-xs-12 ">&nbsp;</div>
</div>
<script>
function restart<?php echo $c1;?>(){
	var GLOBALDATEFORMAT = "<?php echo $GLOBALDATEFORMAT; ?>";
	if(GLOBALDATEFORMAT = "dd-mm-yyyy" && GLOBALDATEFORMAT != "")
	{
		document.chartform<?php echo $c1;?>.chartdate<?php echo $c1;?>.value=''+ padout(day) + '-'  +padout(month - 0 + 1)  + '-' +  year ;
	}
	else
	{
		document.chartform<?php echo $c1;?>.chartdate<?php echo $c1;?>.value=''+  padout(month - 0 + 1) + '-'  + padout(day)  + '-' +  year
	}
   mywindow.close();
}
function newWindow(q){
	mywindow=open('../admin/manage_folder/mycal_folder.php?md='+q,'rajan','width=200,height=250,top=200,left=300');
	mywindow.location.href = '../admin/manage_folder/mycal_folder.php?md='+q;
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function manageBT(){
	if(top.document.getElementById("btSaveAsPDF")){
		top.document.getElementById("btSaveAsPDF").style.display = "none";
	}
	if(top.document.getElementById("btSaveAsJPG")){
		top.document.getElementById("btSaveAsJPG").style.display = "none";
	}
	var cat_id = '<?php echo $cat_id;?>';
	if(cat_id!=0) {
		if(top.document.getElementById("scnDocmntBtn")){
			top.document.getElementById("scnDocmntBtn").style.display = "inline-block";
		}
		if(top.document.getElementById("upldDocmntBtn")){
			top.document.getElementById("upldDocmntBtn").style.display = "inline-block";
		}
		if(top.document.getElementById("btAddNew")){
			top.document.getElementById("btAddNew").style.display = "inline-block";
		}
	}
}
function add_new_folder_fun() {
	var cat_id = '<?php echo $cat_id;?>';
	if(top.frames['fmain']) {
		if(top.frames['fmain'].ifrm_FolderContent) {
			top.show_loading_image("show",""," Loading...");
			top.frames['fmain'].ifrm_FolderContent.location.href = top.JS_WEB_ROOT_PATH+'/interface/chart_notes/add_new_folder.php?folder_id='+cat_id;	
		}
	}
}

manageBT();

function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function setText_scan(id){
    var xmlHttp1=GetXmlHttpObject()
        if (xmlHttp1==null){
            alert ("Browser does not support HTTP Request");
            return;
        }
        var val = document.getElementById('txt_'+id).value;

        var urltext="folder_category.php";
        urltext = urltext+"?scanId="+id+"&value="+encodeURIComponent(val);

        xmlHttp1.onreadystatechange=function(){
            if (xmlHttp1.readyState==4) {

                var trim_str = trim(xmlHttp1.responseText);

                document.getElementById('doc_id'+id).innerHTML = trim_str;
                document.getElementById('txt_'+id).value  = trim_str;
                document.getElementById('txt_'+id).style.display = 'none';
                document.getElementById('doc_id'+id).style.display = 'block';
                //top.iFrameDocuments.refrashNavi();
            }
        }
        xmlHttp1.open("GET",urltext,true);
        xmlHttp1.send(null);
}
    
$(document).ready(function(e) {
	top.show_loading_image("hide");
	if(typeof(top.jquery_date_format)!="undefined"){ var dft=top.jquery_date_format; }	
	else{ var dft='m-d-Y'; }
	$("input.datepicker").datetimepicker({timepicker:false,format:dft,autoclose: true, scrollInput:false});
	$("select.selectpicker").selectpicker();
});
//setPopRefArr();
</script>
</body>
</html>

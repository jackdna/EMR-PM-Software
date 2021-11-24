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
File: scan_documents.php
Purpose: This file provides scan functions in Upload Image in work view.
Access Type : Direct
*/
?>
<?php
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/patient_must_loaded.php");
include_once($GLOBALS['fileroot']."/library/classes/cls_common_function.php");
$library_path = $GLOBALS['webroot'].'/library';	
$srcDir = substr(data_path(1), 0, -1);
$pid = $_SESSION['patient'];
$OBJCommonFunction = new CLSCommonFunction;	
$editid=$_REQUEST['editid'];
$folder_id=(int)$_REQUEST['folder_id'];
$scanTypeFolder = $_REQUEST['scanTypeFolder'];
$userauthorized = $_SESSION['authId'];
$show = $_REQUEST['show'];
$rType = $_REQUEST['t'];
$rAction = $_REQUEST['a'];
$rValid =($rType == 'sch' && $rAction == 'iqs' ) ? true : false;
if( !$folder_id && !$rValid ) {
    die('Invalid access!!!');
}

if( $rValid ) {
    // Get list of folders 
    include_once  $GLOBALS['fileroot'].'/library/classes/folder_function.php';
    $arrFolder	= array();
    $arrAlertPhysician = array();
    $arrTaskPhysician = array();
    $arrLastCreated = array();
    $arrScanComment = array();
    $selQry = "select DATE_FORMAT(upload_date,'".get_sql_date_format()." %h:%i %p') AS crtDate,scandoc_comment,task_physician_id,folder_categories_id from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where patient_id = '$pid' && doc_upload_type='scan' order by `upload_date` asc ";
    $resQry = imw_query($selQry);
    
    if(imw_num_rows($resQry)){
        while($rowQry = imw_fetch_array($resQry)) {
            $arrTaskPhysician[$rowQry['folder_categories_id']] = $rowQry['task_physician_id'];
            $arrLastCreated[$rowQry['folder_categories_id']] = $rowQry['crtDate'];
            $arrScanComment[$rowQry['folder_categories_id']] = $rowQry['scandoc_comment'];
        }
    }
    //pre($arrTaskPhysician);pre($arrLastCreated);
    $qry = "SELECT folder_categories_id,folder_name,alertPhysician FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE parent_id=0 AND patient_id='0' AND folder_status='active' order by folder_name";
    $dia_res = imw_query($qry);
    $dia_num = imw_num_rows($dia_res);
    if($dia_num>0){
        $mainArr = array();
        $tempArr = array();
        while($rowFolder = imw_fetch_assoc($dia_res))
        {
            $level = 0;
            $categoryID = $rowFolder['folder_categories_id'];
            $categoryName = $rowFolder['folder_name'];
            $parentID = $rowFolder['parent_id'];
            $mainArr[$categoryID] = '&gt;&gt;'.$categoryName;
            if( $rowFolder['alertPhysician'] ) 
                $arrAlertPhysician[$categoryID] = $categoryID;
            $tempArr = getChild1($categoryID, $level,$pid);
            if(is_array($tempArr) && count(($tempArr)) > 0  )
                $mainArr = mergeArr($mainArr,$tempArr);
        }
        $catArr = $mainArr;
    }
    
    $folderOptions = '';
    if($dia_num>0){
        $counter = -1;
        foreach($catArr as $key => $val){
            $counter++;
            if( !$folder_id && $counter == 0 ) {
                $folder_id = $key;
            }
            $folderOptions .= '<option value="'.$key.'" '.($key == $folder_id?'selected':'').' data-alert="'.($arrAlertPhysician[$key]?'1':'0').'"><b>'.$val.'</b></option>';
        }
    }
}
$flPth = $GLOBALS['rootdir']."/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs&cat_id=".$folder_id;

	//port
if($_SERVER["SERVER_PORT"] == 80){
	$phpHTTPProtocol="http://";
}
	
	if($phpServerIP!=$_SERVER['HTTP_HOST'])	{
		$phpServerIP=$_SERVER['HTTP_HOST'];
		$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
    }

   //GET FOLDER DETAIL
   $alertPhysicianNew = "";
   if($folder_id) {
	   $folderQry = "SELECT alertPhysician FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_categories_id = '".$folder_id."'";
	   $folderRes = imw_query($folderQry) or die(imw_error());
	   if(imw_num_rows($folderRes)>0) {
			$folderRow = imw_fetch_array($folderRes);   
			$alertPhysicianNew = $folderRow["alertPhysician"];
	   }
   }
   //GET FOLDER DETAIL
   //START GET PRIMARY PHYISICIAN-ID
   if($pid) {
	   $priPhyIdQry = "SELECT providerId FROM patient_data WHERE pid = '".$pid."'";
	   $priPhyIdRes = imw_query($priPhyIdQry) or die(imw_error());
	   if(imw_num_rows($priPhyIdRes)>0) {
			$priPhyIdRow = imw_fetch_array($priPhyIdRes);   
			$priPhyId = $priPhyIdRow["providerId"];
	   }
   }
	if(!trim($priPhyId)) { //IF PRIMARY PHYISICIAN-ID NOT EXISTS THEN GET THIS ID FROM SCHEDULE APPOINTMENT
		$priPhyIdQryNew =  "select sa_doctor_id from schedule_appointments
				where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
				and sa_patient_id = '".$pid."' and sa_app_start_date <= now()
				order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1"; 
		$priPhyIdResNew = imw_query($priPhyIdQryNew) or die(imw_error());
		if(imw_num_rows($priPhyIdResNew)>0) {
			$priPhyIdRowNew = imw_fetch_array($priPhyIdResNew);   
			$priPhyId = $priPhyIdRowNew["sa_doctor_id"];
		}
	}
   //END GET PRIMARY PHYISICIAN-ID

$selQry = "select DATE_FORMAT(upload_date,'".get_sql_date_format()." %h:%i %p') AS crtDate,scandoc_comment,pdf_url,task_physician_id from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where patient_id = '$pid' && folder_categories_id='".$folder_id."' && doc_upload_type='scan' order by `upload_date` desc limit 0,1";
$resQry = imw_query($selQry);
$rowQry = imw_fetch_array($resQry);
$pdf = $rowQry['pdf_url'];

$task_physician_id=$scandoc_comment="";
if($editid) {
	$taskPhyExistQry = "select task_physician_id, scandoc_comment from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scan_doc_id = '".$editid."'";	
	$taskPhyExistRes = imw_query($taskPhyExistQry) or die(imw_error());
	if(imw_num_rows($taskPhyExistRes)>0) {
		$taskPhyExistRow 	= imw_fetch_array($taskPhyExistRes);   
		$task_physician_id 	= $taskPhyExistRow["task_physician_id"];
		$scandoc_comment	= $taskPhyExistRow["scandoc_comment"];
	}
}else {
	$scandoc_comment 		= $rowQry['scandoc_comment'];
	$task_physician_id 		= $priPhyId;
}

//START CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY
$ctId = scnfoldrCatIdFunNew(constant("IMEDIC_SCAN_DB"),'Medication');
$ChkMedDocExistsNumRow='';
if($ctId) {
	$ChkMedDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$pid,$ctId); //FUNCTION FROM common/scan_function.php	
}


$ChkAnyDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$pid); //FUNCTION FROM common/scan_function.php
$scnImgSrcActive = $GLOBALS['webroot'].'/library/images/scanDcs_active.png';
$scnImgSrcDeActive = $GLOBALS['webroot'].'/library/images/scanDcs_deactive.png';
if($ChkAnyDocExistsNumRow>0) { $scnImgSrcActive = scnDocReadChkFun($pid,'scan',$_SESSION['authId']); }
?>

<?php
//END CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY
?>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/core.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
    <?php if( $rValid ) { ?>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>     
    <?php } ?>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
	<script language="javascript">
        var anyDocExistsNumRow='<?php echo $ChkAnyDocExistsNumRow;?>';
        var scnImgSrc;
        if(top.opener) {
            if(top.opener.document.getElementById('14_ioc')) {
                if(anyDocExistsNumRow>0) {
                    scnImgSrc = '<?php echo $scnImgSrcActive;?>';
                }else {
                    scnImgSrc = '<?php echo $scnImgSrcDeActive;?>';
                }
                top.opener.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
            }
            //START CODE TO SET SCAN-ICON IN GENERAL HEALTH MEDICATION(IF MEDICATION EXISTS)
            var scnImgMedSrc=scan_img_val_med='';
            var medDocExistsNumRow	= '<?php echo $ChkMedDocExistsNumRow;?>';
            if(medDocExistsNumRow>0) {
                scnImgMedSrc		= '<?php echo $scnImgSrcActive;?>';
                scan_img_val_med	= '<img src="'+scnImgMedSrc+'"  style="cursor:pointer;" vspace="0" border="0" align="middle" title="Scan Docs" onClick="window.open(\'scan_docs/index.php?med_type=Medication\',\'scanDocs\',\'resizable=yes,scrollbars=1,location=yes,status=yes, width=1000 height=700\');" >';
            }
            if(top.opener.document.getElementById('14_ioc_med')) {//OCULAR MEDICATION IN GENERAL HEALTH DIV
                top.opener.document.getElementById('14_ioc_med').innerHTML=scan_img_val_med;
            }
            //END CODE TO SET SCAN-ICON IN GENERAL HEALTH MEDICATION(IF MEDICATION EXISTS)
                
        }
    </script>	
	<script>
	  function enlargeImage(sId){		
	      //document.getElementById('sId').value = sId;
	      //document.form1.submit();
		  }
	 function setPdf(url,com,tsk_phy_id,tld,edit_id){
		top.show_loading_image("show",""," Loading...");
		browser = get_browser();
		tld = tld || 'P'; 
        if( !edit_id ) edit_id = 0;
        //var s = "setPdf_part('"+url+"','"+com+"','"+tsk_phy_id+"','"+tld+"')";
		//if(browser == "ie")
		$r = upload();
        if( $r != 'No image found' || edit_id)
		    setTimeout(function(){setPdf_part(url,com,tsk_phy_id,tld)},"1000");
        else {
            top.show_loading_image("hide");
            top.fAlert('Please scan atleast single document.');
        }
	}
	function setPdf_part(url,com,tsk_phy_id,tld){
		window.location.href = url+'&comments='+com+'&task_physician_id='+tsk_phy_id+'&tld='+tld;
	}
	function form_submit(){
			document.frm1.submit();
		}
	function call_search(){
			document.getElementById('show').value='search';
			form_submit();
	}
	function showpdfScnDocs( id,pdf ){	
			if( (typeof id != "undefined") && (id != "") ){
			var url = "../chart_notes/show_image.php?id="+id+"&ext="+pdf;
			window.open(url,"","width=300,height=200,resizable=1,scrollbars=1");
			}
	}
	function showpdfScnDocs1(){	
			var url = "../main/demoApplet/uploaddir/PatientId_<?php echo $pid ?>/uploaddir/<?php echo $pdf?>.pdf";
			window.open(url,"","width=300,height=200,resizable=1,scrollbars=1");
	}
	function showBT(){
		if(top.document.getElementById("btSaveAsPDF")){
			top.document.getElementById("btSaveAsPDF").style.display = "inline-block";
		}
		if(top.document.getElementById("btSaveAsJPG")){
			top.document.getElementById("btSaveAsJPG").style.display = "inline-block";
		}
		if(top.document.getElementById("btBackFolderCat")){
				top.document.getElementById("btBackFolderCat").style.display = "inline-block";
			}
		if(top.document.getElementById("scnDocmntBtn")){
			top.document.getElementById("scnDocmntBtn").style.display = "none";
		}
		if(top.document.getElementById("upldDocmntBtn")){
			top.document.getElementById("upldDocmntBtn").style.display = "none";
		}
		if(top.document.getElementById("btAddNew")){
			top.document.getElementById("btAddNew").style.display = "none";
		}
	}
	function save_pdf_jpg(op){
		if(op == "pdf"){
			document.getElementById("refreshLeftNav").value = "yes";
			setPdf('<?php echo $GLOBALS['php_server']."/library/demoApplet/pdfDemo.php?folder_id=$folder_id&edit_id=$editid"; ?>',document.getElementById('comments').value,document.getElementById('task_physician_id').value,document.getElementById('pageTLD').value,'<?php echo $editid;?>');
		}
		else if(op == "jpg"){
			document.getElementById("refreshLeftNav").value = "yes";
			setPdf('<?php echo $GLOBALS['php_server']."/library/demoApplet/jpgDemo.php?folder_id=$folder_id&edit_id=$editid"; ?>',document.getElementById('comments').value,document.getElementById('task_physician_id').value,document.getElementById('pageTLD').value,'<?php echo $editid;?>');
		}
	}
	function reload_page(){
		if(top.frames['fmain']) {
			top.show_loading_image("show",""," Loading...");
			top.frames['fmain'].location.href = '<?php echo $flPth; ?>';
		}
	
	}
	function go_back_folder_cat(){
		reload_page();
    }
    <?php if( $rValid ) { ?>

        function compare(a, b) {
            // Use toUpperCase() to ignore character casing
            const valA = a.val.toUpperCase();
            const valB = b.val.toUpperCase();

            let status = 0;
            if (valA > valB) {
                status = 1;
            } else if (valA < valB) {
                status = -1;
            }
            return status;
        }

        var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
        var arrTaskPhysician = <?php echo json_encode($arrTaskPhysician);?>;
        var arrLastCreated = <?php echo json_encode($arrLastCreated);?>;
        var arrScanComment = <?php echo json_encode($arrScanComment);?>;
        var dropDownProv = <?php echo json_encode($OBJCommonFunction->dropDown_providers('','','1','array')); ?>;
        var tmpArr = [];
        $.each(dropDownProv, function(i,v){
            tmpArr.push({id:i,val:v});
        });
        tmpArr = tmpArr.sort(compare);
        dropDownProv = tmpArr;

        function save_docs(op) {

            if( op != 'pdf' && op!= 'jpg') return false;

            var v = $("[name=folder_id]").val();
            var loc = '<?php echo $GLOBALS['php_server'];?>'+ "/library/demoApplet/";
            var url = loc+"pdfDemo.php?qs=1&folder_id="+v;
            if(op == "jpg"){
                url = loc+"jpgDemo.php?qs=1&folder_id="+v;
            }
            setPdf(url,document.getElementById('comments').value,document.getElementById('task_physician_id').value,document.getElementById('pageTLD').value);
        }
        function setFolderChange(_this){

            var obj = $(_this);
            var fid = obj.val();
            $("[name=folder_id]").val(fid);
            var ap = parseInt($("#sel_folder option:selected").data('alert'));

            if( ap ) {
                var selectHtml = '';
                selectHtml += '<select class="minimal" name="task_physician_id" id="task_physician_id" >';
                selectHtml += '<option value="">Select</option>';
                $.each(dropDownProv,function(i,v){
                    selectHtml += '<option value="'+v.id+'" '+(arrTaskPhysician[fid]==v.id?'selected':'')+'>'+v.val+'</option>';
                });
                selectHtml += '</select>';                    
                $("#task_phy_div").html(selectHtml);    
                $("#task_physician_label").css('display','inline-block');
            }
            else {
                var selectHtml = '<input type="hidden" name="task_physician_id" id="task_physician_id">';
                $("#task_phy_div").html(selectHtml);
                $("#task_physician_label").css('display','none');
            }

            var commentVal = '';
            if( arrScanComment[fid] ) commentVal = arrScanComment[fid];
            $("#comments").val(commentVal);

            var lastCreatedStr = '';
            if( arrLastCreated[fid] ) {
                lastCreatedStr = '<b>Last Scan Date Time-:</b>&nbsp;'+arrLastCreated[fid]
            }
            $("#last_created_date_div").html(lastCreatedStr);
            
        }
    <?php } ?>    

</script>
</head>

<body>
<?php 
    if( $rValid ) {
        require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
        $app_base = new app_base();
        $patientInfoArr = $app_base->show_patient_info();
        $patient_name = core_get_patient_name($app_base->session['patient']);
        $patientInfo = $patient_name[4]."&nbsp;&nbsp;<small>(DOB - ".date(phpDateFormat(), strtotime($patientInfoArr['DOB'])).", Age ".show_age($patientInfoArr['DOB']).")</small>";

        echo '
        <div id="div_alert_notifications"><span class="notification_span"></span></div>
        <div id="div_loading_image" class="text-center">
            <div class="loading_container">
                <div class="process_loader"></div>
                <div id="div_loading_text" class="text-info">Loading...</div>
            </div>
        </div>

        <div class="purple_bar">
            <div class="row">
                <div class="col-sm-2">
                    <h4 class="acc_page_name" id="acc_page_name">Scan Docs</h4>
                </div>
                <div class="col-sm-6 mt5">'.$patientInfo.'</div>
                <div class="col-sm-4 mt5">
                    <select name="sel_folder" id="sel_folder" onChange="setFolderChange(this);" class="form-control minimal" >'.$folderOptions.'</select>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>';
        
    }
if($show != 'search'){	
	$browser = browser();
	if(strtolower($browser['name']) == "msie" || strtolower($browser['name'])!="chrome"){
		echo "<script>multiScan='yes';no_of_scans=100;duplexScan='yes';pageType='sdoc';scan_doc_upload_btn='yes'; ".($rValid?"quickScan='yes';autoScan='no';":'')." upload_scan_url = '".$GLOBALS['php_server']."/library/demoApplet/fileupload.php?imwemr=".session_id()."&method=upload&folder_id=".$folder_id."&editid=".$editid."&pid=".$_SESSION["patient"]."';</script>";
		include_once($GLOBALS['fileroot']."/library/scanc/scan_control.php");
	}
	else {
		echo "<script>multiScan='yes';no_of_scans=100;duplexScan='yes';pageType='sdoc';scan_doc_upload_btn='yes'; ".($rValid?"quickScan='yes';autoScan='no';":'')." upload_scan_url = '".$GLOBALS['php_server']."/library/demoApplet/fileupload.php?imwemr=".session_id()."&method=upload&folder_id=".$folder_id."&editid=".$editid."&pid=".$_SESSION["patient"]."';</script>";
		include_once($GLOBALS['fileroot']."/library/scanc/scan_control.php");
	}
}?>

<form name="frm1" id="frm1" action="scan_documents.php" method="post">
    <input type="hidden" name="show" value="<?php echo $show; ?>">
    <input type="hidden" name="folder_id" value="<?php echo isset($_REQUEST['folder_id'])?$_REQUEST['folder_id']:$folder_id; ?>">
    <input type="hidden" name="prevType" value="<?php echo $_REQUEST['prevType']; ?>">
    <input type="hidden" name="refreshLeftNav" id="refreshLeftNav" value="<?php echo $_REQUEST['refreshLeftNav']; ?>">
    <input type="hidden" name="pageTLD" id="pageTLD" value="<?=($_REQUEST['pageTLD'] ? $_REQUEST['pageTLD'] : 'P')?>">
    <input type="hidden" name="sId" value="">

    <div id="divImages" class="col-xs-12 bg-white" >
        <?php 
        if($show != 'search'){?>
            <div class="row" >
                <div class=" col-xs-12 " >&nbsp;</div>
                <div class=" col-xs-5 col-lg-4 " >
                    <?php if($alertPhysicianNew=='1') { $txtAreaWidth='350px';?>		
                            <label style="size:30; position:relative; " id="task_physician_label"><b>Physician&nbsp;Alert:</b></label>
                            <span id="task_phy_div">
                                <select class="minimal" name="task_physician_id" id="task_physician_id" data-size='5' data-dropup-auto ="false" >
                                    <option value="">Nothing selected</option>
                                    <?php
                                    echo $OBJCommonFunction->dropDown_providers($task_physician_id,'','1');
                                    ?>
                                </select>
                            </span>
                    <?php }else {$txtAreaWidth='550px';?>
                        <label style="size:30; position:relative; display:none;" id="task_physician_label"><b>Physician&nbsp;Alert:</b></label>
                        <span id="task_phy_div"><input type="hidden" name="task_physician_id" id="task_physician_id"></span>
                    <?php }?>
                </div>
                <div class=" col-xs-7 col-lg-8 " >
                    <?php $txtAreaWidth = $rValid ? '350px' : $txtAreaWidth; ?>
                    <label style="size:30; position:relative; top:-18px;"><b>Comment:&nbsp;</b></label>
                    <textarea name="comments" class="body_c" id="comments" style="width:<?php echo $txtAreaWidth;?>; height:40px;"><?php echo $scandoc_comment;?></textarea>
                </div>

                <div class=" col-xs-12" id="last_created_date_div">
                <?php
                    if(($rowQry['crtDate'] != '00-00-0000 12:00 AM') &&($rowQry['crtDate'] !='')){?>
                        <b>Last Scan Date Time-:</b>&nbsp;<?php echo $rowQry['crtDate'];?>
                <?php } ?>
                </div>
                <div class="clearfix"></div>
                
            </div>
    <?php 
        }else{
            $search = $_POST['search'];
            $selQry = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scandoc_comment LIKE '%$search%'";
            $res = imw_query($selQry);
            $rowQry = imw_fetch_array($res);
            $search12= $rowQry['scandoc_comment'];	
            //echo '<table width="100%">';
            //echo '<tr valign="middle">';
            ?>
            <div class="row" >
                <?php
                if($search12 != ''){
                    $getImagesToShowStr = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
                    where patient_id = '$pid' 
                    AND scandoc_comment = '$search12'
                    ORDER BY scan_doc_id";
                }
                if($_REQUEST['CompareBtn'] == "Compare"){
                        $imageArr = $_REQUEST['imageArr'];
                        if(count($imageArr)>0){
                            foreach($imageArr as $imagesCompareId){
                                if($imagesId){
                                    $imagesId = $imagesId.', '.$imagesCompareId;
                                }else{
                                    $imagesId = $imagesCompareId;
                                }
                            }
                        }
                        $getImagesToShowStr = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl
                        WHERE scan_doc_id in ($imagesId)";
                }
                if($_REQUEST['sId']){
                       $sId = $_REQUEST['sId'];
                       $getImagesToShowStr = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
                       WHERE scan_doc_id in ($sId)";
                }
                    //echo $getImagesToShowStr;
                $getImagesToShowQry = imw_query($getImagesToShowStr);
                if(imw_num_rows($getImagesToShowQry)>0){				
                    while($getImagesToShowRows = imw_fetch_assoc($getImagesToShowQry)){
                        $scan_id = $getImagesToShowRows['scan_doc_id'];
                        $image_name = $getImagesToShowRows['doc_title'];					
                        $imageScanedArr[]  = $scan_id;
                        $doc_title = $getImagesToShowRows['doc_title'];
                        $pdf_url = $getImagesToShowRows['pdf_url']; 					
                        $fileType = $getImagesToShowRows['doc_type'];					
                        $file_path= $getImagesToShowRows['file_path'];
                        if(!empty($file_path)){
                            $file_path = $srcDir.$file_path;
                            //$file_path = $GLOBALS['rootdir']."/main/uploaddir".$file_path;
                        }
                        
                        if($count>=4){
                           // echo '</tr><tr>';
                            $count = 0;
                        }
                        $count++;
                        ?>
                        <div class=" col-xs-3 " >
                            <div class=" col-xs-12 " >
                                <?php 
                                if( !empty($file_path) && ($fileType == "" || $fileType == "pdf")){ ?>
                                    <img style="cursor:hand; " src="<?php echo $library_path; ?>/images/pdficon.png" alt="pdf file" onClick="showpdfScnDocs('<?php echo $getImagesToShowRows['scan_doc_id'];?>','pdf')">
                                <?php 							
                                }else{?>									
                                    <img style="cursor:hand; " border="0"  onClick="showpdfScnDocs('<?php echo $getImagesToShowRows['scan_doc_id']; ?>','')" id="imgThumbNail<?php echo $getImagesToShowRows['scan_doc_id']; ?>" src="<?php echo $file_path; ?>" height="70" width="120">
                                <?php 
                                }?>
                            </div> 
                            <div class=" col-xs-12 " >
                                <?php echo $search;?>
                            </div>                   
                        </div>
                    <?php
                    }
                }else {
                ?>	
                    <div class=" col-xs-12 " >
                        <b>No Image Found.</b>
                    </div>    
                <?php
                }
                ?>
            </div>
            <?php
        }
        if($show=='search'){
        ?>
            <div class=" col-xs-12 " >&nbsp;</div>
            <div class=" col-xs-12 " >
                <input type="button" style="width:100px;" id="back" onClick="document.getElementById('show').value='';document.frm1.submit();" class="dff_button" name="backBtn" onMouseOver="button_over('back')" onMouseOut="button_over('back','')" value="Back">    
            </div>
        <?php
        }
        ?>    
    </div>
    <?php
        if( $rValid ){
    ?>	
            <footer style="position:fixed; bottom:0; width:100%; ">
                <div class="row">
                    <div class="col-xs-12 text-center" id="page_buttons"  >
                        <input type="button" class="btn btn-success" align="bottom" name="btSaveAsPDF" id="btSaveAsPDF" onclick="top.save_docs('pdf')" value="Save as PDF" style="display: inline-block;">
                        <input type="button" class="btn btn-success" align="bottom" name="btSaveAsJPG" id="btSaveAsJPG" onclick="top.save_docs('jpg')" value="Save as JPG" style="display: inline-block;">
                    </div>
                </div>
            </footer>
    <?php
        }
    ?>
</form>
<script>
	top.show_loading_image("hide");
	showBT();
	$(document).ready(function(e) {
		$("select.selectpicker").selectpicker();
    });
    <?php if( $folder_id > 0 && isset($_REQUEST['al']) && $_REQUEST['al'] == 'sh' ) { ?>
        top.alert_notification_show('Scan documents saved successfully.');
        if ( window.history.replaceState ) {
            var u = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/scan_documents.php?t=sch&a=iqs&sb=no&folder_id="+'<?php echo $folder_id;?>';
            window.history.replaceState(null, null, u);
        }
    <?php } ?>	
</script>
</body>
</html>
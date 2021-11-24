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
include_once(dirname(__FILE__)."/../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/patient_must_loaded.php");
require_once("../../../library/classes/dhtmlgoodies_tree.class.php");
$library_path = $GLOBALS['webroot'].'/library';
$pg_title = 'Scan Docs';
$tree = new dhtmlgoodies_tree();
if(!$p) { $p=1;}
//include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
$p++;
//$tree->addToArray($p,"Scan Docs",0,"");
$a=$p;
$p++;
//Functions 

//End Functions
$med_type = $_REQUEST['med_type']; //TO OPEN FOLDER DIRECTLY
//START CODE TO ADD MED-HX FOLDER IF NOT EXIST
//$categoryFolderArr = array('Allergies','Immunization','Medication','Sx/Procedures','Vital Sign');
if(constant("DISABLE_DEFAULT_FOLDER")!="1" || !constant("DISABLE_DEFAULT_FOLDER")){
	$categoryFolderArr = array('Medication');
	foreach($categoryFolderArr as $categoryFolderNme) {
		$sqlQry = "SELECT folder_categories_id FROM ".constant("IMEDIC_SCAN_DB").".folder_categories
				   WHERE folder_name='".addslashes($categoryFolderNme)."' AND parent_id='0' AND patient_id='0'
				   ORDER BY folder_categories_id";
		$sqlRes = imw_query($sqlQry) or die(imw_error());
		if(imw_num_rows($sqlRes)>0) {
			$sqlRow 			= imw_fetch_array($sqlRes);
			if($categoryFolderNme=='Medication') { $medInsrtId = $sqlRow['folder_categories_id']; }
		}else{
			$insfoldrQry 		= "INSERT INTO ".constant("IMEDIC_SCAN_DB").".folder_categories SET
									folder_name 	= '".addslashes($categoryFolderNme)."', parent_id='0',
									folder_status	= 'active', 
									patient_id='0', 
									created_by = '".$_SESSION['authId']."', 
									date_created = '".date('Y-m-d H:i:s')."', 
									modified_section = 'CNscanDocs'";
			$insfoldrRes 		= imw_query($insfoldrQry) or die(imw_error());
			if($categoryFolderNme=='Medication') { $medInsrtId = imw_insert_id(); }
		}
	}
}
//END CODE TO ADD MED-HX FOLDER IF NOT EXIST

//Navi Data
$patient_id = $_SESSION["patient"];
$divNavi = getScanTree_sd($patient_id);
$rightfrmSrc='';
if(strtolower($med_type)=='medication') {
	$rightfrmSrc="../folder_category.php?cat_id=".$medInsrtId;
	
}else if($_REQUEST['cat_id']) {
	$rightfrmSrc="../folder_category.php?cat_id=".$_REQUEST['cat_id'];
}
$copayFld = get_copay_field('collapse_docs_default');
$collapseDocs = (int)$copayFld['collapse_docs_default'];
?>
<html>
<head>
	<title>Scan Docs</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/simpletree.css" rel="stylesheet">
	<script type="text/javascript">
		var ddtreepath = "<?php echo $library_path."/";?>";	
		window.focus();
    </script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
    <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/simpletreemenu.js"></script>
    
	<style>
		
		/* Navi */
		div#divNavi{ width:100%; height:100%;overflow:auto; top:0px; left:1px; }
		a.naviFolderName{  font-weight:bold;color:#673782;font-family: 'robotolight';font-size: 14px;text-decoration:none; }
		.treeview li{padding-left:0px;}
		.naviFileName{
			font-size: 14px!important; font-family: 'robotolight'; font-weight: normal!important
			
		}
		.bullsize{ font-size:13px;padding-right:5px; }
		
		.submenu > a.navifoldername{padding-left:35px;}
		
		
		/*Content*/
		
	</style>
	<script type="text/javascript">
		var of=null;
		var oDivNavi = null;
		//var oPP2 = window.opener.oPP;
		
		function showHideScanUploadBt(op, page){
			page = page || "";
			if(op == "show"){
				if(page == upload){
					top.document.getElementById("btSaveComment").style.display = "inline-block";
					top.document.getElementById("btBackFolderCat").style.display = "inline-block";
				}
				else if(page == scan){
					top.document.getElementById("btSaveAsPDF").style.display = "inline-block";
					top.document.getElementById("btSaveAsJPG").style.display = "inline-block";
				}
			}
			else if(op == "hide"){		
				top.document.getElementById("btSaveComment").style.display = "none";
				top.document.getElementById("btBackFolderCat").style.display = "none";
			
				top.document.getElementById("btSaveAsPDF").style.display = "none";
				top.document.getElementById("btSaveAsJPG").style.display = "none";
				
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
		}
		
		//Show Folder
		function showFolder(id){
			top.show_loading_image("show",""," Loading...");
			showHideScanUploadBt("hide");
			if(of && (typeof id != "undefined")){				
				of.src="../folder_category.php?cat_id="+id;
			}
		}
		
		//Show File
		function showFile(id, ext){
			showHideScanUploadBt("hide");	
			if(of && typeof id != "undefined"){				
				of.src = '../show_image.php?id='+id+'&ext='+ext+"&noZoom=1" ;
			}
		}
		
		//Show File PDF
		function showFile_pdf(file_name,pid){
			showHideScanUploadBt("hide");
			if(of && (typeof file_name != "undefined") && (typeof pid != "undefined")){
				var u = '../../main/demoApplet/uploaddir/PatientId_'+pid+'/uploaddir/'+file_name+'.pdf';
				of.src = u;
			}
		}
		function showFile_pdf_new(file_name,pid,id){
			file_name = encodeURIComponent(file_name);
			showHideScanUploadBt("hide");
			if(of && (typeof file_name != "undefined") && (typeof pid != "undefined")){
				var u = '../show_image.php?pid='+pid+'&file_name='+file_name+'&id='+id ;
				of.src = u;
			}
		}
		function refrashNavi(){
			showHideScanUploadBt("hide");
			//Checks
			//Define Var -----
			var url = "../common/requestHandler.php";
			params = "elem_formAction=scanDocsTree";
			
			//-------------------------------------------
			//Get xmlHttp Object
			xmlHttp = getXmlHttpObject();
			if(xmlHttp == null)
			{
				window.status = "This browser does not support ajax."
				return;
			}
			xmlHttp.open("POST",url,true);
			//For Post Method 
			xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlHttp.setRequestHeader("Content-length",params.length);
			xmlHttp.setRequestHeader("connection","close");
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4)
				{
					if (xmlHttp.status == 200 || xmlHttp.status == 304) {
					//------  processing after connection   ----------
						var str = xmlHttp.responseText;
						if((typeof str != "undefined") && oDivNavi){
							oDivNavi.innerHTML=""+str;
							// set menu tree
							ddtreemenu.createTree('treemenuScan', true);	
						}
					
					//------  processing done --------------------------
					}else if(xmlHttp.status == 12030){
						refrashNavi(); //resnding request
					}
				}	
			};
			xmlHttp.send(params);
		top.opener.top.update_toolbar_icon('scanDocs');//updating toolbar icon
		}
		
		//close
		function window_close(){
			showHideScanUploadBt("hide");
			//window.close();
			try{
				//Scan POP
				for(var z in oPP2){
					if((top.oPP2[z]) && (top.oPP2[z].closed == false)){												
						top.oPP2[z].close();
					}	
				}
			}catch(ignored){} 
		}
		function hideUnRdDoc(id) {//----START FUNCTION
			
			var objNavi = document.getElementById('spnUnreadDocNaviId'+id);
			
			if(objNavi) {//-----START IF
				objNavi.innerHTML = '';	
				alert_parent_folder(objNavi);
				check_scan_document_exists();
				
				
		 }//------END IF
	}//--------END FUNCTION
	function alert_parent_folder(objNavi){
		$(objNavi).parents('li').each(function(index, elementLi) {
					var clearAlert = 1;
					liID = this.id;
					liReg = /li/;
					if(liReg.test(liID)){
						$(this).find('span').each(function(index, element) {
                            	spanID = element.id
								spanReg = /spnUnreadDocNaviId/
								//if(spanReg.test(spanID))
								//alert(spanID + "::"+spanReg.test(spanID)+"::"+element.innerHTML)
								if(spanReg.test(spanID) && element.innerHTML != ''){
									clearAlert = 0;return (false);
								}
                        });
							if(clearAlert){
								var obj = '';
								$(this).find('span').each(function(index, element) {
                                    spanID = element.id
									spanReg = /unReadCatId/
									//alert(spanReg.test(spanID))
									if(spanReg.test(spanID)){
										obj  = element;
										return (false);
									}
                                });
								if(typeof(obj) != "undefined")
								obj.innerHTML='';
							}
								
					}
		});
    }
    
    function check_scan_document_exists() {
        var i = $('li[grp=scanLi]').find('img[title="Unread Document"]').length;
        if(i==0){
            var docElem = $(".document_exists", window.top.opener.document);
            if(docElem.hasClass('doc_exists')){ docElem.removeClass('doc_exists'); }
        }
    }

	</script>
   <style>
   		ul{
			padding:5px 10px;
			width:250px;
		}
   </style> 
</head>
<body>
<?php 
$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 310)) ;
?>
  	<div class="col-xs-12 bg-white" >
    	<div class="row" >
        <div id="divNavi1"  class=" col-xs-2 " style=" max-height:100%; overflow:scroll">
			<?php echo $divNavi;?>  
        </div>
        <div class="col-xs-10 ">
            <div class="row">
                <div class="well pd0 margin_0 nowrap" style="vertical-align:text-top;">
					<iframe name="ifrm_FolderContent" id="ifrm_FolderContent" frameborder="0" width="100%" height="" src="<?php echo $rightfrmSrc;?>"></iframe>
                </div>   
            </div>
        </div>
      </div>
    </div>


<script>
	var collapseDocs = <?php print $collapseDocs;?>;
	top.btn_show("SCAN_DOCS");
	showHideScanUploadBt("hide");
	// set obj iframe
	of = document.getElementById("ifrm_FolderContent");
	//Div Navi
	oDivNavi = document.getElementById("divNavi");
	
	// set menu tree
	ddtreemenu.createTree('treemenuScan', true);	
	//ddtreemenu.expandSubTree('li','1');
	//ddtreemenu.rememberstate('li','1');
	ddtreemenu.flatten('li','expand',collapseDocs?false:true);
	//set height, width of menu tree
	$('#divNavi1').height($(window).height()-10);
	$('#ifrm_FolderContent').height($(window).height()-10);
	//alert(top.onloadfilterScanDocs);
	//var mw = parseInt(w+40);
	//top.onloadfilterScanDocs();	
</script>
<?php 
//include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
$p++;

//$tree->writeCSS();
//$tree->writeJavascript();
//$tree->drawTree();

?>
<script>
top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
</script>	
</body>
</html>
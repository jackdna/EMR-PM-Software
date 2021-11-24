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
require_once(dirname(__FILE__)."/../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
$patientId = $_SESSION['patient'];

//========GET CONSULT LETTER DATA BASED ON PROVIDED ID===========
//========HTML & PDF CREATION====================================
if(isset($_GET['get_html']) && $_GET['get_html'] == 'yes'){
	
	$consultPDFData = "";
	$consultId = $_POST['id'];
	
	//=======PATIENT CONSULT DATA GET ON BASED OF CONSULT ID=====
	$consultData = imw_query("SELECT templateData, top_margin, left_margin FROM `patient_consult_letter_tbl` WHERE patient_consult_id='".$consultId."'");
	$consultDataRes = imw_fetch_assoc($consultData);
	
	//===========CONSULT TEMPLATE DATA==============
	$consultPDFData	= $consultDataRes['templateData'];
	$topMargin		= $consultDataRes['top_margin'];
	$leftMargin		= $consultDataRes['left_margin'];
	
	//==========PDF MARGIN ADJUSTING WORK===========
	if(($topMargin==0 || $topMargin=="") && (strstr($consultPDFData,"<page_header>"))){$topMargin=5;}
	$consultPDFData ='<page backtop="'.$topMargin.'" backleft="'.$leftMargin.'" backbottom="15">'.$consultPDFData.'</page>';
	//==========IMAGES PATH REPLACEMENTS============
	$consultPDFData = str_ireplace($web_root."/interface/common/new_html2pdf/","",$consultPDFData);
	$consultPDFData = str_ireplace($web_root."/interface/reports/new_html2pdf/","",$consultPDFData);
	$consultPDFData = str_ireplace($web_root."/interface/main/uploaddir/document_logos/","../../main/uploaddir/document_logos/",$consultPDFData);
	$consultPDFData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$consultPDFData);
	
	//==========HTML FILE WRITE & PDF CREATION=======
	file_put_contents('../common/new_html2pdf/Faxlog.html',$consultPDFData);
	echo $GLOBALS['webroot']."/interface/common/new_html2pdf/createPdf.php?op=P&htmlFileName=Faxlog";
	exit;
}
//=======================ENDS HERE=================================

//========SEND FAX LOG DISPLAY IN POP UP WORKS STARTS HERE=========
if(trim($patientId) && (!empty($patientId))){
 
  //=======FETCH PATIENT DATA FOR DISPLAYING PATIENT===============
  $patientData = imw_query("SELECT fname, mname, lname FROM `patient_data` WHERE id='".$patientId."'");
  $patientDataRes = imw_fetch_assoc($patientData);
  $patientfname = trim($patientDataRes['fname']);
  $patientmname = trim($patientDataRes['mname']);
  $patientlname = trim($patientDataRes['lname']);
 
  if((!empty($patientfname)) && (!empty($patientmname)) && (!empty($patientlname))){
  	$patientName =  $patientDataRes['lname'].',&nbsp;'.$patientfname.'&nbsp;'.$patientmname.'&nbsp;-&nbsp;'.$patientId;
  }else{
  	$patientName =  $patientDataRes['lname'].',&nbsp;'.$patientfname.'&nbsp;-&nbsp;'.$patientId;
  }

  $consentTemplateIdArr = array();
  $ptConsentQry = "SELECT form_information_id, consent_form_id FROM patient_consent_form_information WHERE patient_id='".$patientId."'";  
  $ptConsentRes = imw_query($ptConsentQry);
  if(imw_num_rows($ptConsentRes)>0) {
	  while($ptConsentRow = imw_fetch_assoc($ptConsentRes)) {
	  	$form_information_id = $ptConsentRow["form_information_id"];
		$consent_form_id = $ptConsentRow["consent_form_id"];
		$consentTemplateIdArr[$form_information_id] = $consent_form_id;
	  }
  }

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Send Fax Log</title>
        <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
        <!-- Bootstrap Selctpicker CSS -->
        <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
        <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
        <script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/core_main.js" type="text/javascript" ></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript" ></script>
		<style>
            table tr{cursor:pointer}
        </style>
		<script type="text/javascript">
        window.focus();
        //======SEND CONSULT ID & POST DATA THROUGH AJAX============
        function get_pdf(file_name){
            file_name = $.trim(file_name);
            var wo=top.JS_WEB_ROOT_PATH;
            var basePath = wo+'<?php echo "/data/".constant('PRACTICE_PATH')."/PatientId_".$patientId."/fax_log"; ?>';
            if(file_name!==''){
                window.open(basePath+'/'+file_name,'Fax_Log_Pdfs', "width=700,height=500,top=150,left=150,scrollbars=yes");
            }
        }
        </script>
	</head>
	<!--==========SEND FAX LOG POP UP HTML DATA WORKS==========-->
    <body>
        <div class="container-fluid ">
            <div class="mainwhtbox pd10 mt10">
                <div class="row">    	    
                <!-- Access History sec. -->	
                    <div class="col-sm-12">
                        <div class="row">
            
                        <!-- Heading -->
                            <div class="col-sm-12 purple_bar">
                                <label>Send Fax Log</label>
                                <label style="margin-left:30%;"><?php echo $patientName; ?></label>
                            </div>  
                            <div class="col-sm-12">  
                                <div class="row" style="height:<?php echo ($_SESSION['wn_height']-330);?>px; overflow-x:hidden; overflow:auto;">
                                    <table class="table table-striped table-bordered " >
                                        <thead>
                                            <tr class="grythead">
                                                 <th class="pointer link_cursor" >Date</th>
                                                 <th class="pointer link_cursor" >Fax To</th>
                                                 <th class="pointer link_cursor" >Section</th>
                                                 <th class="pointer link_cursor" >Template Name</th>
                                                 <th class="pointer link_cursor" >Transaction Id</th>
                                                 <th class="pointer link_cursor" >Fax Status</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                        <?php
                                          //=========DISPLAY THE FAX LOG ON BASED OF UPDOX ID==========
                                          $faxLogQry = imw_query("SELECT patient_consult_id, template_id, template_name, cur_date_time, section_name, updox_id, updox_status, fax_type, `file_name`,section_pk_id FROM `send_fax_log_tbl` WHERE patient_id='".$patientId."' ORDER BY id DESC");
                                            
                                            if($faxLogQry && imw_num_rows($faxLogQry)>0){
                                               
                                               while($faxLogRes = imw_fetch_assoc($faxLogQry)){
                                                 $template_name = trim($faxLogRes['template_name']);
                                                 $cur_date_time = trim($faxLogRes['cur_date_time']);
                                                 $section_name =  trim($faxLogRes['section_name']);
                                                 $updox_id =  trim($faxLogRes['updox_id']);
                                                 $updox_status =  trim($faxLogRes['updox_status']);
                                                 $patientConsultId = trim($faxLogRes['patient_consult_id']);
                                                 $file_name = trim($faxLogRes['file_name']);
                                                 $section_pk_id = trim($faxLogRes['section_pk_id']);
												 $fax_type = trim($faxLogRes['fax_type']);
                                                 if($fax_type=="Primary"){
                                                      $fax_type = "Referring Physician";
                                                  }
												  $updir=substr(data_path(), 0, -1);
												  $faxPdfClick = "get_pdf('".$file_name."');";
												  if(trim($file_name) && !file_exists($updir."/PatientId_".$patientId."/fax_log/".$file_name)) {
													  if(trim($patientConsultId)) {
														  $faxPdfClick = "opener.top.popup_win('templatepri.php?tempId=".$patientConsultId."&media_id=');";
													  }else if(trim(strtolower($section_name))=='consent_form') {
														  $consent_template_id = $consentTemplateIdArr[$section_pk_id];
														  $faxPdfClick = "opener.top.popup_win('../patient_info/consent_forms/print_consent_form.php?consent_form_id=".$consent_template_id."&consent=yes&form_information_id=".$section_pk_id."');";
													  }
												  }
                                        ?>	
                                                            <tr onClick="<?php echo $faxPdfClick; ?>">
                                                                <td style="border-top:1px solid #FCFCFC;border-left:1px solid #FCFCFC;"  class="txt_11"><?php echo date('m-d-y'.' h:i',strtotime($cur_date_time)); ?></td>
                                                                <td style="border-top:1px solid #FCFCFC;border-left:1px solid #FCFCFC;"  class="txt_11"><?php echo $fax_type;  ?></td>
                                                                <td style="border-top:1px solid #FCFCFC;border-left:1px solid #FCFCFC;"  class="txt_11"><?php echo ucfirst($section_name); ?></td>
                                                                <td style="border-top:1px solid #FCFCFC;border-left:1px solid #FCFCFC;"  class="txt_11"><?php echo ucfirst($template_name); ?></td>
                                                                <td style="border-top:1px solid #FCFCFC;border-left:1px solid #FCFCFC;"  class="txt_11"><?php echo $updox_id; ?></td>
                                                                <td style="border-top:1px solid #FCFCFC;border-left:1px solid #FCFCFC;" class="txt_11"><?php echo ucfirst($updox_status); ?></td>
                                                            </tr>
                                        <?php	  
                                               }
                                            }else{
                                        ?>
                                                            <tr>
                                                                <td colspan="6" style="width:100%;text-align:center;"  class="txt_11">No Record Exists</td>
                                                            </tr>		
                                        <?php } ?>                                
                                    	</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>    
</html>
<?php 
} 
?>

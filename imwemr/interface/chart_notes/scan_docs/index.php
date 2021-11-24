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
$library_path = $GLOBALS['webroot'].'/library';
$pg_title = 'Pt. Docs';


//--- SET DEFAULT TAB ----
$defaultTab = 'Scan_Docs';
$arrTabs = array();
$arrTabs[] = 'Scan Docs';
$arrTabs[] = 'Pt Docs';

$getTabs = 'Scan_Docs';
//--- SET IFRAME HEIGHT ----
$wn_height = ($_SESSION['wn_height'] - 200);
$main_height = $_SESSION['main_height'];
//--- SET CSS FILES PATH -----
$css_header = $css_header;
$css_patient = $css_patient;

//--- GET PATIENT NAME FORMAT ------
$patient_id = $_SESSION["patient"];
$qry = "select lname, fname, mname from patient_data where id = '$patient_id'";
$patientQryRes = get_array_records_query($qry);
$patient_name_arr = array();
$patient_name_arr['LAST_NAME'] = $patientQryRes[0]['lname'];
$patient_name_arr['FIRST_NAME'] = $patientQryRes[0]['fname'];
$patient_name_arr['MIDDLE_NAME'] = $patientQryRes[0]['mname'];
$patientName = changeNameFormat($patient_name_arr);
$patientName .= ' - '.$patient_id;
$folder_id = $_REQUEST['folder_id'];
?>
<!DOCTYPE>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10">
	<script type="text/javascript">
        var wn_height = '<?php echo $main_height;?>';	
        window.moveTo(0,0);
       // window.resizeTo(1024, wn_height);
        window.focus();
    </script>
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
    <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/js_scanDocs.js"></script>
</head>
<body >
<div align="center" id="loading_img" width="100%" style="display:none; top:350px; left:500px; z-index:1000; position:absolute;">
	<img src="../../../images/loading_image.gif">
</div>
<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $defaultTab;?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">         
    <tr>
    	<td valign="middle" nowrap="nowrap" width="19%">
        	<?php echo $getTabs;?>
        </td>
    	<td style="text-align:center;"><div style="color:#000; font-size:14px; font-weight:bold;"><?php echo $patientName;?></div></td>
    </tr>
	<tr>
		<td colspan="2" style="vertical-align:top;">						
			<iframe name="iFrameDocuments" id="iFrameDocuments" width="100%" height="<?php echo $wn_height;?>" frameborder="0" scrolling="no" src="scan_docs.php?cat_id=<?php echo $folder_id;?>"></iframe>
		</td>
	</tr>
    <tr>
        <td colspan="10" bgcolor="#93b9dc" width="100%" align="center">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr id="footersid" height="10">
                    <td width="13%" align="left">
                        <img src="../../themes/default/images/bottom_bar_logo.gif">
                    </td>
                    <td width="61%" align="center" nowrap="nowrap">
                        <input type="button" value=" Save " class="dff_button" name="submit_btn" id="submit_btn" style="display:none;" onClick="saveTemplateData();">
                        
                        <input type="button" class="dff_button" id="btSaveComment" name="btSaveComment" value="Save Comment" style="display:none;" onClick="save_comments();">
                        <input type="button" class="dff_button" id="btBackFolderCat" name="btBackFolderCat" value="Go back to folder categories" style="display:none;" onClick="go_back_folder_cat();">
                        
                        <input type="button" class="dff_button" id="btSaveAsPDF" name="btSaveAsPDF" style="display:none;" value="Save as PDF" onClick="save_pdf_jpg('pdf');" >
                        <input type="button" class="dff_button" id="btSaveAsJPG" name="btSaveAsJPG" style="display:none;" value="Save as JPG" onClick="save_pdf_jpg('jpg');" >
                        
            			<input name="button" type="button" class="dff_button" id="btn_close" onClick="window.close();" value="Close"/>
                    </td>
                    <td width="26%" align="right" class="text_10"><!--<b>{$smarty.now|date_format:"%A, %B %e, %Y"}<span id="dt_tm"></span></b>--></td>
                </tr>                                  
            </table> 
        </td>
    </tr>
</table>
</body>
<script language="javascript">
	function show2(){
		if (!document.all&&!document.getElementById)
		return
			thelement=document.getElementById? document.getElementById("dt_tm"): document.all.dt_tm
		var Digital=new Date()
		var hours=Digital.getHours()
		var minutes=Digital.getMinutes()
		var seconds=Digital.getSeconds()
		var dn="PM"
		if (hours<12)
			dn="AM"
		if (hours>12)
			hours=hours-12
		if (hours==0)
			hours=12
		if (minutes<=9)
			minutes="0"+minutes
		if (seconds<=9)
			seconds="0"+seconds
		var ctime=hours+":"+minutes+":"+seconds+" "+dn
		thelement.innerHTML="<b style='font-size:10;color:#4A67A2; font-family:Verdana'>"+ctime+"</b>"
		setTimeout("show2()",1000)
	}
	show2();
</script>
</html>
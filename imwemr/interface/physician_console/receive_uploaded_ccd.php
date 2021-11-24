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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
//require_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');

$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');

$msgConsoleObj = new msgConsole();
//$oSaveFile = new SaveFile();

$curr_user_directory = '/users/UserId_'.$_SESSION['authId'].'/mails';

if(!is_dir($dir_path.$curr_user_directory) || !file_exists($dir_path.$curr_user_directory)){
	$a= mkdir($dir_path.$curr_user_directory,0755,true);
}
$time = date('mdyHis');
$file_name = $_FILES['ccdFile']['name'];


// -------UPLOAD XML FILE -------
$validXMLUploaded = false; $blIsAESDecryptperformed = false;
if(trim($_FILES['ccdFile']['type'])=='text/xml') $validXMLUploaded = true;

if($validXMLUploaded){
	/****CHECK IF UPLOADED FILE IS ENCRYTED*******/
	if($_POST['cbkEncrip'] && $_POST['cbkEncrip']=='on'){
		$key = 	trim($_POST['txtENCKey']);
		include(dirname(__FILE__)."/../../library/classes/AES.class.php");
		if($key != ""){
			$objAES = new AES($key);
			// -------UPLOAD ENCRYOTED FILE -------
			$encryp_file_name = preg_replace('/.xml/','',$file_name)."_encryp_".$time.'.xml';
			//$encryp_file_name = preg_replace('/.xml/',"",$encryp_file_name).".".get_file_extension($file_name);
			
			$complete_file_path_encryp = $dir_path.$curr_user_directory.'/'.$encryp_file_name;
			$file_pointer = copy($_FILES['ccdFile']['tmp_name'],$complete_file_path_encryp);
			//-------------------------------------
			$data = file_get_contents($complete_file_path_encryp);
			$xml = $objAES->decrypt($data);
			$blIsAESDecryptperformed = true;
			
			$file_name_db = preg_replace('/.xml/','',$file_name)."_".$time.'.xml';
			//$file_name_db = preg_replace('/.xml/',"",$file_name_db).".".get_file_extension($file_name);
			$complete_file_path = $dir_path.$curr_user_directory.'/'.$file_name_db;
			file_put_contents($complete_file_path,$xml);
		//	$file_pointer = "/PatientId_".$patientId."/CCD/".$file_name_db."";
		}
		
	}else{
		$file_name_db = preg_replace('/.xml/','',$file_name)."_".$time.'.xml';
		$complete_file_path = $dir_path.$curr_user_directory.'/'.$file_name_db;
		$file_pointer = copy($_FILES['ccdFile']['tmp_name'],$complete_file_path);
	}
	
	//---LINE BELOW TO CHECK IF XML IS ENCRYPTED OR PLAIN--
	$blXMLCheck = simplexml_load_string(file_get_contents($complete_file_path));
	
	$ccd_data_content = utf8_encode(utf8_decode(trim(file_get_contents($complete_file_path))));
	$xml_db_chk = $msgConsoleObj->check_patient_details($ccd_data_content,$complete_file_path);
	
	
	//pre($xml_db_chk,1);
	$patient_suggestions = $msgConsoleObj->get_patient_suggestions($xml_db_chk);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/html5shiv.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/respond.min.js"></script>
    <![endif]-->
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script> 
        <!-- Include all compiled plugins (below), or include individual files as needed --> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/console.js?<?php echo filemtime('../../library/js/console.js');?>"></script>

</head>
<body>
<?php if($validXMLUploaded){?>
    <table class="table">
        <tr>
            <td style="width:25%">Read Status: </td>
            <td style="width:75%"><?php 
				if($blXMLCheck === FALSE && ($_POST['cbkEncrip']=='on' && $blIsAESDecryptperformed)){echo 'Decryption failed. Check if correct <b>KEY</b> provided. ';}
				if($blXMLCheck === FALSE) {echo 'CCD is not in proper format. ';}
				if($blXMLCheck){echo 'Success. ';}
				if($blXMLCheck && $blIsAESDecryptperformed){echo 'Decryption done. ';}
			?></td>
        </tr>
        <?php if($blXMLCheck){?>
        <tr>
            <td valign="top">C-CDA Patient Details: </td>
            <td>
                <div style="width:300px; border:2px dashed #ddd; margin:20px; float:right; padding:10px;">
                    <a href="javascript:;" class="a_clr1" onClick="launch_view_uploaded_ccda();">View uploaded C-CDA document.</a><br><br>
                    <input type="button" value="Perform Reconciliation" title="Patient not selected" class="btn btn-success" name="btn_proceed" id="btn_proceed" disabled onClick="do_pt_doc_then_reconcile($('#patientId').val(),$('#xml_file_path').val());">
                </div>
            <?php foreach($xml_db_chk as $h=>$v){
                    if($h=='fname') $h = 'First Name';
                    else if($h=='lname') $h = 'Last Name';
                    else if($h=='mname') $h = 'Middle Name';
                    else if($h=='dob') {$h = 'DOB'; $v = get_date_format($v);}
                    echo '<b>'.ucfirst($h).'</b>: '.$v.'<br>';
                
                }
            ?></td>
        </tr>
        <tr><td colspan="2" class="bg-success">
            <div class="row">
                <div class="col-sm-3 lftpanel">
                    <b>Search Patients</b>
                </div>
                <div class="col-sm-6 form-group lftpanel">
                                
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="hidden" id="pt_sel_id" name="patient_id" value="">
                                <input type="text" id="txt_patient_name" name="txt_patient_name" onkeypress="{if (event.keyCode==13){return searchPatientManually(this);}}" value="" class="form-control" placeholder="Search Patient....">
                                <label class="input-group-addon" onclick="chk_patient($('#txt_patient_name'));searchPatientManually($('#txt_patient_name'));"><span class="glyphicon glyphicon-search"></span></label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatientManually(this)}" class="form-control minimal">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Deceased">Deceased</option>
                                    <option value="Resp.LN">Resp.LN</option>
                                    <option value="Ins.Policy">Ins.Policy</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    
                </div>
                <div class="col-sm-3"><span class="text_purple pointer pull-right" onclick="top.create_patient_ccda('','<?php echo urlencode($curr_user_directory.'/'.$file_name_db);?>','');">Create Patient</span></div>
            </div>
        </td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><b>Auto Matched Patients</b></td></tr>
        <tr>
            <td colspan="2"><?php if(count($patient_suggestions['pt_details'])>0){?>
                    <div class="row">
                    <?php 
                    $show_upto = count($patient_suggestions['pt_details']);
                    if($show_upto>4) $show_upto=4;
                    for($i=0; $i < $show_upto; $i++){
                        $this_rs_pt = $patient_suggestions['pt_details'][$i];
                        ?>
                        <div class="col-sm-3">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <div class="radio radio-inline"><input type="radio" id="suggested_pt_<?php echo $i;?>" name="suggested_xml_pt" value="<?php echo $this_rs_pt['id'];?>" onclick="physician_console2('<?php echo $this_rs_pt['id'];?>','<?php echo $this_rs_pt['lname'].', '.$this_rs_pt['fname'];?>');"><label for="suggested_pt_<?php echo $i;?>"><?php echo $patient_suggestions['common_name'].' - '.$this_rs_pt['id'];?></label></div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <p><strong>Sex</strong> : <?php echo $this_rs_pt['sex'];?></p>
                                        <p><strong>DOB</strong> : <?php echo get_date_format($this_rs_pt['DOB']);?></p>
                                        <p><strong>Zip</strong> : <?php echo $this_rs_pt['postal_code'];?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    </div>
                    <?php
                }else{
                    echo '<div class="alert alert-warning">No results for automatch patients. </div>';
                    ?>
                               
                    
                    <?php
                }
            ?>
            </td>
        </tr>
        <?php }?>
    </table>
    <input type="hidden" name="xml_file_path" id="xml_file_path" value="<?php echo $curr_user_directory.'/'.$file_name_db;?>">
    <input type="hidden" name="patientId" id="patientId" value="">	
<?php }else{?>
	<div class="alert alert-danger"><b>Please upload a valid XML C-CDA document.</b></div>
<?php }?>
</body>
</html>
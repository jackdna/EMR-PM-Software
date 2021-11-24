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
require_once("../../admin_header.php");
require_once($GLOBALS['srcdir']."/classes/admin/documents/document.class.php");
$doc_obj = new Documents("Prescriptions","Contact Lens");
$createdBy = $_SESSION['authId'];
$msg = "";
if($save){	
	$today = date('Y-m-d g:i:s A');
	$prescriptionType = $_REQUEST['prescriptionType'];
	$prescriptionTypeArr = split('-',$prescriptionType);
	$prescriptionType = $prescriptionTypeArr[0];
	$prescriptionName = $prescriptionTypeArr[1];
	
	if($_REQUEST['prescriptionEditId']){
		$intUpdatePrescriptionId = $_REQUEST['prescriptionEditId'];
		$prescriptionTypeArr = split('-',$intUpdatePrescriptionId);		
		$prescriptionType = $prescriptionTypeArr[0];
		$prescriptionName = $prescriptionTypeArr[1];		
		$queryupdate = "update prescription_template set ";
		$queryWhere = "where prescription_template_type = $prescriptionType";
	}
	else{
		$queryInsert = "insert into prescription_template set ";
	}
	$saveQuery = "prescription_template_name = '".addslashes(trim($prescriptionName))."',
				  prescription_template_content = '".addslashes(trim($_REQUEST['content']))."',
				  prescription_template_type = '".$prescriptionType."',
				  prescription_template_created_date_time = '".$today."',
				  prescription_template_created_by = ".$createdBy.",
				  printOption = '".$_REQUEST['printoption']."'			  
				  ";	
	if($_REQUEST['prescriptionEditId']){
		$queryInsertUpdate = $queryupdate.$saveQuery.$queryWhere;
	}	
	else{
		$queryInsertUpdate = $queryInsert.$saveQuery;
	}		  	
	$rsInsert = imw_query($queryInsertUpdate);
	if($rsInsert){
		$msg = "Prescription Template Successfully Saved";

//MITCHELL QUINN CHANGES	6.5	
		$prescriptionTempData = stripslashes(trim($_REQUEST['content']));
//MITCHELL QUINN CHANGES 6.5		
		//$_REQUEST['prescriptionEditId'] = $_REQUEST['prescriptionType'];
		if($_REQUEST['prescriptionType']){
			$prescriptionEditId = $_REQUEST['prescriptionType'];
			$prescriptionTempType = $_REQUEST['prescriptionType'];	
			$printOption = $_REQUEST['printoption'];
		}
		elseif($_REQUEST['prescriptionEditId']){
			$prescriptionEditId = $_REQUEST['prescriptionEditId'];
			$prescriptionTempType = $_REQUEST['prescriptionEditId'];
			$printOption = $_REQUEST['printoption'];
		}
	}
	else{
		$isErr=1;
		$msg = "Prescription Template Not Saved Please Try Again";
	}
			  	
}
elseif($_REQUEST['prescriptionEditId']){
	$intSerchPrescriptionId = $_REQUEST['prescriptionEditId'];
	$prescriptionTypeArr = split('-',$intSerchPrescriptionId);
	$prescriptionType = $prescriptionTypeArr[0];
	$prescriptionName = $prescriptionTypeArr[1];

	$querySelectPerticuler = "select prescription_template_type as prescriptionTempType,prescription_template_name as prescriptionTempName,prescription_template_content as prescriptionTempData,delete_status as prescriptionTempDeleteStatus,printOption from prescription_template where prescription_template_type = $prescriptionType";
	$rsSelectPerticuler = imw_query($querySelectPerticuler) or die($querySelectPerticuler.imw_error());
	@extract(imw_fetch_array($rsSelectPerticuler));
	
}
elseif($_REQUEST['prescriptionDeleteId']){
	$intDeletePrescriptionId = $_REQUEST['prescriptionDeleteId'];
	$queryDeletePerticuler = "delete from prescription_template where id = $intDeletePrescriptionId";
	$rsDeletePerticuler = imw_query($queryDeletePerticuler) or die($queryDeletePerticuler.imw_error());
	if($rsDeletePerticuler){
		$msg ="Prescription Template Successfully Deleted";
	}
}
$querySelect = "select id,prescription_template_name,prescription_template_content,delete_status,printOption from prescription_template";
$rsSelect = imw_query($querySelect) or die($querySelect.imw_error());

//START CODE FOR VOCABULARY
$allVocabCntctLns 		= array();
$allVocabCntctLns 		= $doc_obj->get_variable_tags("prescription_contact_lens");
//$ptInfoCntctLnsArr 		= $allVocabCntctLns[0]; 
$presCntctLnsArr 		= $allVocabCntctLns[7];
//END CODE FOR VOCABULARY			  	

//START CODE FOR VOCABULARY
$allVocabGlasses 		= array();
$allVocabGlasses 		= $doc_obj->get_variable_tags("prescription_glasses");
$ptInfoGlassesArr 		= $allVocabGlasses[0];
$presGlassesArr 		= $allVocabGlasses[8];
//END CODE FOR VOCABULARY			  	

//START CODE FOR VOCABULARY
$allVocabMedRx 			= array();
$allVocabMedRx 			= $doc_obj->get_variable_tags("prescription_medical_rx");
//$ptInfoMedRxArr 		= $allVocabMedRx[0];
$presMedRxArr 			= $allVocabMedRx[9];

//END CODE FOR VOCABULARY			  	
$allVocab 		= array();
$allVocab 		= $doc_obj->get_variable_tags("consent_form");
$ptInfoArr 		= $allVocab[0];
$cnsntFrmArr 	= $allVocab[1];
$logo_vacab = array();
$logo_url_arr = array();
$logo_vocab_res = imw_query("SELECT var_name,img_url FROM document_logos WHERE delete_status=0");
if($logo_vocab_res){
	while($logo_vocab_rs= imw_fetch_assoc($logo_vocab_res)){
		$logo_vacab['{'.$logo_vocab_rs['var_name'].'}'] = str_replace('_',' ',$logo_vocab_rs['var_name']).' : ';
		$logo_url_arr[$logo_vocab_rs['var_name']] = $logo_vocab_rs['img_url'];
	}
}

$variable_data = $allVocabCntctLns;
?>
<script type="text/javascript">
	<?php if($msg!=''){ 
		if($isErr=='1'){?>
			top.fAlert('<?php echo $msg;?>');			
		<?php }else{?>
			top.alert_notification_show('<?php echo $msg;?>');
	<?php }
	} ?>

	function submit_template_frm(){
		var msg = '';	
		var _bgColor	=	'#F6C67A';
		top.show_loading_image('show');
		if(document.getElementById("prescriptionType").value==""){
			msg = '&bull; Prescription Template Name is required.<br>';
			//prescription.prescriptionType.className="selectpicker form-control mandatory";
			$("#prescriptionType").addClass("selectpicker form-control mandatory");
			$('.selectpicker').selectpicker('refresh');
			document.getElementById("prescriptionType").style.backgroundColor = _bgColor;
			document.getElementById("prescriptionType").focus();
		}
	   document.getElementById('save').value='1';		
		if(msg){
			fAlert(msg);
			top.show_loading_image('hide');
			return false;
		}
		document.prescription.submit();
	}

	function getTemplate(obj){
		top.show_loading_image('show');
		var id = obj.value;
		if(id){		
			top.show_loading_image('hide');
			document.getElementById("prescriptionEditId").value = id;
			document.prescription.submit();
		}
		top.show_loading_image('hide');
		
	}
	
	function deleteTemplate(id,msg){
		if(typeof(msg)!='boolean'){msg = true;}
		if(msg){
			top.fancyConfirm("Sure! you want to delete prescription template?","", "window.top.fmain.all_data.iFrameConsole.deleteTemplate('"+id+"',false)");
		}else{
			document.prescription.content.value = '';
			document.getElementById("prescriptionEditId").value = '';
			document.getElementById("prescriptionDeleteId").value = id;
			document.prescription.submit();
		}
	}
	
	function resetForm(){
		document.prescription.content.value = '';
		document.getElementById("prescriptionEditId").value = '';
		document.getElementById("prescriptionDeleteId").value = '';
		location.href=location.href;
	}
	
</script>
        <script type="text/javascript">
		var logo_url_arr = jQuery.parseJSON('<?php echo json_encode($logo_url_arr);?>');

		function RedactorOnChangeEvenAction(){
			  h = $('#content').redactor('code.get'); 
			  for(x in logo_url_arr){
				  f = new RegExp('{'+x+'}', "i");
				  r = '<img src="'+logo_url_arr[x]+'">';
				  if(h.search(f)>=0){
					  h = h.replace(f, r);
					  $('#content').redactor('code.set',h);
				  }
			  }
			  
		  }
		
        $(function()
        {
            $('#content').redactor({
                
                buttonSource: true,
                imageUpload: '<?php echo $GLOBALS['webroot']; ?>/redactor/upload.php',
                plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
                minHeight: <?php echo $_SESSION['wn_height'] - 560?>, 
                maxHeight: <?php echo $_SESSION['wn_height'] - 560?>,
				buttonsHide: ['deleted', 'formatting', 'link', 'file']

            });
        $('#div_variables div span.text12b').click(function(){
		$('#div_variables').find('div.listing').slideUp();
		if($(this).parent().find('div.listing').css('display')!='block')$(this).parent().find('div.listing').slideDown();
		
	});
	
	$('.listing li span').mouseover(function (){
		var text = $(this).text();
		var $this = $(this);
		var $input = $('<input type=text readonly>');
		$input.prop('value', text);
		$input.appendTo($this.parent());
		$input.select();
		$this.hide();
		$input.focusout(function(){
			$this.show();
			$input.remove();
		});
	});
	/*
	XY = $('#savedTemplate').offset();
		
		});*/
		function showSlider(t){
			sliderX = XY.left;
			sliderY = XY.top;
			if($('#div_variables').css('display')!='none'){
				$('#div_variables').fadeOut('fast');
			}else{  
			$('#div_variables').css({left:sliderX,top:sliderY,width:$('#savedTemplate').width(),height:$('#savedTemplate').height()},'slow');
			$('#div_variables').fadeIn('fast');
			}
		}
</script>
<body >
<div class="whtbox documents_sec"> 
	<form name="prescription" id="prescription" method="post">    
        <input type="hidden" name="prescriptionEditId" id="prescriptionEditId" value="<?php if($prescriptionTempType==""){}else{echo $prescriptionEditId;} ?>" />
        <input type="hidden" name="prescriptionDeleteId" id="prescriptionDeleteId" value=""/>
        <input type="hidden" name="prescriptionDeleteStatus" id="prescriptionDeleteStatus" value=""/>
        <input type="hidden" name="save" id="save" value=""/>
        <div class="tblBg">
            <div class="row pt10">
                <div class="col-sm-3 lft_pnl">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <ul class="nav nav-tabs auto_resp_dp">
                                    <li class="pointer btn btn-success head active" href="#variable_data" data-toggle="tab"><span >Variables</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="tab-content">
                                <div class=" " id="variable_data">
                                    <div class="row">
                                        <div class="panel-group selection_box" id="auto_resp_acc">
                                            <div class="panel">
                                            <?php 
                                                $var_str = '';
                                                foreach($variable_data as $key => $val){
                                                    $toggle_names = $doc_obj->get_toggle_nm($key);
                                                    $variable_names = '<ul class="variable_list list-group">';
                                                    
                                                    //Variable names
                                                    foreach($val as $var_key => $var_val){
                                                        $variable_names .= '<li title="'.trim(str_replace(':','',$var_key)).'" class="list-group-item"><span>'.$var_key.'</span></li>';
                                                    }
                                                    $variable_names .= '</ul>';
                                                    
                                                    //Variable section
                                                    $var_str .= '
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_acc" href="#'.$toggle_names.'_div">
                                                          <h4 class="panel-title">
                                                            <span class="glyphicon glyphicon-menu-right"></span>
                                                            <span>
                                                                '.$key.' Variables
                                                            </span>
                                                          </h4>
                                                        </div>
                                                    </div>
                                                    <div id="'.$toggle_names.'_div" class="panel-collapse collapse">
                                                        '.$variable_names.'
                                                    </div>';
                                                }
                                                echo $var_str;
                                            ?>
                                            </div>	
                                        </div>	
                                    </div>
                                </div>
                            </div>	
                        </div>
                    </div>
                </div>
                <div class="col-sm-9 rght_pnl">
                    <div class="adminbox">
                        <div class="head">
                            <span>Prescription Template</span>	
                            <span class="pull-right"><?php echo $template_preview; ?></span>
                        </div>
                        <div class="tblBg">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Prescription Template Name</label>
                                                <select class="selectpicker form-control " data-width="100%" id="prescriptionType" name="prescriptionType" data-title="Select" data-size="6" onChange="getTemplate(this);">
                                                                <option value="">--Select--</option>
                                                                <option value="2-Contact Lens" <?php if($prescriptionTempType == 2){ echo 'selected';} elseif($prescriptionType == 2){ echo 'selected';} ?>>Contact Lens (SCL)</option>
                                                                <option value="4-Contact Lens" <?php if($prescriptionTempType == 4){ echo 'selected';} elseif($prescriptionType == 4){ echo 'selected';} ?>>Contact Lens (RGP)</option>                                                        
                                                                <option value="1-Glasses" <?php if($prescriptionTempType == 1){ echo 'selected';} elseif($prescriptionType == 1){ echo 'selected';}?>>Glasses</option>					
                                                                <option value="3-Medical Rx" <?php if($prescriptionTempType == 3){ echo 'selected';} elseif($prescriptionType == 3){ echo 'selected';} ?>>Medical Rx</option>													
                                                            </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-sm-4 content_box" style="margin-top:6px;">Print Option<br>
                                            <div class="checkbox checkbox-inline">
                                                <input type="checkbox" name="printoption" id="landscape"  onClick="setCheckSingleAction('landscape','portrait');" value="0" <?php if($printOption == 0){echo 'checked';} ?>>
                                                <label for="landscape">Landscape</label>	
                                            </div>
                                            <div class="checkbox checkbox-inline">
                                                <input type="checkbox" name="printoption" id="portrait"  onClick="setCheckSingleAction('portrait','landscape');" value="1" <?php if($printOption == 1){echo 'checked';} ?>>
                                                <label for="portrait">Portrait</label>	
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 pt10">
                                    <textarea id="content" name="content" class="ckeditor_textarea"><?php echo stripslashes($prescriptionTempData); ?></textarea>	
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
$js_php_arr = array("header_title"=>"Prescription Template","cur_tab"=>"prescriptions");
$js_php_arr = json_encode($js_php_arr);
$js_vars =  '
<script>
	var js_php_arr = '.$js_php_arr.';
</script>';
echo $js_vars;

?>
<script src="<?php echo $library_path ?>/js/admin/admin_documents.js"></script>
<?php
	require_once("../../admin_footer.php");
?> 

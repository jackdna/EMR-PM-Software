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

?><?php
/*
File: electronic_billing.php
Purpose: Electronic Billing Main interface.
Access Type: Direct Access (in frame) 
*/

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
$objEBilling = new ElectronicBilling();
$pg_title = 'Electronic';

//--- GET INSURANCE COMPANIES LIST -----
$PayerClearingHouseWise = $objEBilling->getPayersClearingHouseWise();

//--GETTING GROUP DETIALS----
$arr_groups = $objEBilling->get_groups_detail();

//GETTING AND SETTING INSURANCE GROUPS---
$OPT_InsGroupNames = ''; $all_ins_fromGrps = '';
$q_insGrp = "SELECT GROUP_CONCAT(ic.id) AS ins_ids, icg.title FROM ins_comp_groups icg 
			 JOIN insurance_companies ic ON(ic.groupedIn = icg.id) WHERE icg.delete_status=0 
			 GROUP BY icg.title ORDER BY icg.title";
$res_insGrp = imw_query($q_insGrp);
$all_ins_fromGrps = '';
if($res_insGrp && imw_num_rows($res_insGrp)>0){
	while($rs_insGrp = imw_fetch_assoc($res_insGrp)){
		$all_ins_fromGrps .= $rs_insGrp['ins_ids'].',';
		$OPT_InsGroupNames .= '<option value="'.$rs_insGrp['ins_ids'].'">'.$rs_insGrp['title'].'</option>';
	}
	$all_ins_fromGrps = substr($all_ins_fromGrps,0,-1);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Electronic Billing</title>
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" rel="stylesheet">
<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
<?php } ?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/html5shiv.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
</script>
</head>
<body>
<div class="container-fluid mtb10">
    <div class="whitebox">
        <div class="row billingara">
            <div class="col-lg-3 col-md-3 col-sm-4">
                <div class="filterbatchfile top_box_1">
                    <div class="header"><h2>
                    						<div class="pull-right checkbox"><input id="chk_show_archive" type="checkbox" value="1" onClick="LoadBatchList(2,this);">
                                            		<label for="chk_show_archive"> Show Archive</label></div>
                    						<figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/filtericon.png" alt=""/></figure>Filter Batch Files
                                        </h2></div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="">Search By Insurance</label>
                                <select class="form-control minimal" name="show_batch_files_by" id="show_batch_files_by" onChange="LoadBatchList(3,this)">
                                  	<option value="">Insurance All</option>
									<?php foreach($PayerClearingHouseWise as $CLhouse=>$Payers){?>
                                    <option value="<?php echo $CLhouse;?>"><?php echo $CLhouse;?></option>                        
                                    <?php }?>
                                </select>
							</div>
                        </div>
                        <div class="col-lg-6 col-md-12 srchpetient form-inline">
                            <div class="form-group">
                                <label for="">Search By Patient</label>
                                <input type="text" class="form-control"  name="show_batch_byPt" id="show_batch_byPt" onKeyPress="{if (event.keyCode==13) LoadBatchList(4,this);}" placeholder="Patient ID..."> <button class="searchbut" type="submit" onClick="LoadBatchList(4,document.getElementById('show_batch_byPt'))"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </div>
                        </div>
                    </div>
                	<div class="clearfix"></div>
                </div>
            	<div class="clearfix"></div>
                <div class="bltabdat" id="left_col">
                    <div class="table-responsive respotable" id="div_batch_list">
                        <table class="table table-bordered table-striped table-hover batchlist">
                        <thead>
                            <tr>
                                <th valign="top" class="batchlist_col1"><div class="checkbox"><input type="checkbox" name="chk_sel_all_batches" id="chk_sel_all_batches" value="1" onClick="selAll(this)"/><label for="chk_sel_all_batches"></label></div></th>
                                <th class="batchlist_col2"><strong>Batch Name</strong><small>(Interchange#)</small></th>
                                <th class="batchlist_col3"><strong>Recv.Date</strong></th>
                                <th class="batchlist_col4"><strong>&nbsp;</strong></th>
                            </tr>
                            <tr id="infomsg" class="hide"><td colspan="6" class="text-center bg-success"></td></tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        </table>
                    </div>
                	<div class="clearfix"></div>
                	<div class="text-center butmb">
                    	<button class="btn btn-primary" type="button" onClick="do_with_file('Re-Generate');">Re- Generate</button>
                        <button class="btn btn-info" id="btn_archive" type="button" onClick="do_with_file('Archive');">Archive</button>
                        <button class="btn btn-info" id="btn_unarchive" type="button" onClick="do_with_file('Un-Archive');">Un-Archive</button>
                        <button class="btn btn-danger" type="button" onClick="do_with_file('Delete');">Delete</button>
                    </div>
                </div>
            	<div class="clearfix"></div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-8">
            <form name="frm" method="GET">
                <div class="createbtch top_box_1">
                    <div class="bthead">
                        <div class="row">
                            <div class="col-lg-4 col-md-12 col-sm-12"><h2> <figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/batchicon.png" alt=""/></figure>
                              Create Batch</h2></div>
                              <div class="col-lg-4 col-md-6 col-sm-12 bttype ">
                                  <label class="radio-inline"><strong>Claim Type</strong></label>
                           	  	  <input type="radio"  name="claim_type" id="claim_type_pri" value="Primary" class="css-checkbox" checked="checked" />
                                  <label for="claim_type_pri" class="css-label radGroup2">Primary</label>
                            	  <input type="radio"  name="claim_type" id="claim_type_sec" value="Secondary" class="css-checkbox"/>
                                  <label for="claim_type_sec" class="css-label radGroup2">Secondary</label>
                              </div>
                              <div class="col-lg-4 col-md-6 col-sm-12 bttype ">
                                  <label class="radio-inline"><strong>Batch Mode</strong></label>
                            	  <input type="radio" name="batch_type" id="batch_type_prod" value="P" class="css-checkbox" checked="checked" />
                                  <label for="batch_type_prod" class="css-label radGroup2">Production</label>
                            	  <input type="radio" name="batch_type" id="batch_type_test" value="T" class="css-checkbox"/>
                                  <label for="batch_type_test" class="css-label radGroup2">Test</label>
                              </div>
                        </div>
                    </div>
                	<div class="clearfix"></div>
                    <div class="batcfrm">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                            	<h3>Posted Date</h3>
                                <div class="row">
                                <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="sr-only" for="exampleInputAmount">DOS (from)</label>
                                    <div class="input-group">
                                      <div class="input-group-addon labbg">From</div>
                                      <input type="text" name="pos_from" id="pos_from" class="form-control" placeholder="<?php echo strtoupper($GLOBALS['date_format']); ?>">
                                      <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true" onClick="$('#pos_from').click();"></span></div></label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-6"><div class="form-group">
                                    <label class="sr-only" for="exampleInputAmount">DOS (to)</label>
                                    <div class="input-group">
                                      <div class="input-group-addon labbg">To</div>
                                      <input type="text" class="form-control" name="pos_upto" id="pos_upto" placeholder="<?php echo strtoupper($GLOBALS['date_format']); ?>" value="<?php echo date(phpDateFormat());?>">
                                      <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                    </div>
                                  </div></div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <h3>Date of Service</h3>
                                <div class="row">
                                <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="sr-only" for="exampleInputAmount">DOS (from)</label>
                                    <div class="input-group">
                                      <div class="input-group-addon labbg">From</div>
                                      <input type="text" class="form-control" name="dos_from" id="dos_from" placeholder="<?php echo strtoupper($GLOBALS['date_format']); ?>">
                                      <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-6"><div class="form-group">
                                    <label class="sr-only" for="exampleInputAmount">DOS (to)</label>
                                    <div class="input-group">
                                      <div class="input-group-addon labbg">To</div>
                                      <input type="text" class="form-control" name="dos_upto" id="dos_upto" placeholder="<?php echo strtoupper($GLOBALS['date_format']); ?>" value="<?php echo date(phpDateFormat());?>">
                                      <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                    </div>
                                  </div></div>
                                </div>
                            </div>
                        </div>
                    	<div class="clearfix"></div>
                        <div class="instype">
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-6 ">
                                	<h3>Practice Group</h3>
                                	<select class="selectpicker" data-width="100%" name="group_select" id="group_select">
                                      <?php foreach($arr_groups as $val){?>
                                    	<option value="<?php echo $val['gro_id'];?>"><?php echo $val['name'];?></option>
                                      <?php }?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                <h3>Payer</h3>
                                	<select class="selectpicker" data-width="100%" name="claim_ins_type" id="claim_ins_type" onChange="toggleInsGrpFrm();">
                                      <?php foreach($PayerClearingHouseWise as $CLhouse=>$Payers){?>
                                        <option value="<?php echo $CLhouse;?>"><?php echo $CLhouse;?></option>                        
                                      <?php }?>
                                	</select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                	<h3>Payer Group</h3>
                                	<select class="selectpicker" data-live-search="false" data-width="100%" data-actions-box="false" multiple name="Ins_groups" id="Ins_groups">
                                      <?php echo $OPT_InsGroupNames;?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                	<h3>Payer Filter</h3>
                                	<input type="hidden" name="AllInsFromGroup" value="<?php echo $all_ins_fromGrps;?>" />
                                    <select class="selectpicker" data-width="100%" name="opt_insComps" id="opt_insComps">
                                      <option value="none" selected>None</option>
                                      <option value="selected">Selected</option>
                                      <option value="all">All</option>
                                      <option value="exclude">Exclude</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                	<div class="clearfix"></div>
                </div>
           </form>
            	<div class="clearfix"></div>
                <div class="row" id="right_banners">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="validclaim">
                            <div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/valid_claim.png" alt=""/></figure><h2 id="span_validclaims"><span>Valid Claims</span>0</h2></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="validamt">
                        	<div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/valid_amount.png" alt=""/></figure><h2 id="span_validamount"><span>Valid Amount</span>$0.00</h2></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="invaildclaim">
                            <div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/invail_claim.png" alt=""/></figure><h2 id="span_invalidclaims"><span>Invalid Claims</span>0</h2></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="invaildamnt">
                            <div class="claimara"><figure><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/invaild_amount.png" alt=""/></figure><h2 id="span_invalidamount"><span>Invalid Amount</span>$0.00</h2></div>
                        </div>
                    </div>
                </div>
            	<div class="clearfix"></div>
                <div class="table-responsive btchtabl" id="right_col">
                	<div id="result_area"></div>
                	<div id="result_area_2" class="hide"></div>
                </div>
            <div class="clearfix"></div>
            
            </div>
        
        </div>
    </div>
</div>      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery_1.7.1.js" type="text/javascript"></script>  
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.printElement.min.js"></script>
    <script type="text/javascript">var jq171 = jQuery.noConflict(true);</script>    
    
    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>

    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
    <script>
		(function($){
			$(window).load(function(){
				
				$("#content-1").mCustomScrollbar({
					theme:"minimal"
				});
				
			});
		})(jQuery);
	</script>
    
    <script>
    $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
    
    </script>   

    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.okayNav.min.js"></script>
    <script type="text/javascript">
        var navigation = $('#nav-main').okayNav();
    </script>
<script type="text/javascript">
$('document').ready(function(e) {
	toggleInsGrpFrm();
	init_display_ele_billing();
    LoadBatchList(0);
	var date_global_format = window.top.jquery_date_format;
	//var currentDate_temp = new Date();
	//currentDate = currentDate_temp.getMonth()+'-'+currentDate_temp.getDate()+'-'+currentDate_temp.getYear();
  $("#pos_from, #pos_upto, #dos_from, #dos_upto").datetimepicker({timepicker:false,format:top.global_date_format});

});
function init_display_ele_billing(){//left_col; right_col; right_banners;
	wh = parseInt($(window).innerHeight());
	//setTimeout(function(){match_height('top_box_1');},500);
	l_colH = parseInt(wh) - (parseInt($('.top_box_1').css('height'))+140);
	r_colH = parseInt(wh) - (parseInt($('.top_box_1').css('height'))+parseInt($('#right_banners').height()));
	$('#div_batch_list').height((l_colH+20)+'px');
	$('#right_col').css('height',(r_colH-110)+'px');
	$('#right_banners').css('visibility','hidden');
}
function OpenRequested(req){
	switch(req){
		case 'upload997':
			top.popup_win('upload_file.php','fileWindow','menubar=0,scrollbars=1,resizable=yes');
			break;
		case 'getReports':
			pphh = '<?php echo $_SESSION["wn_height"];?>';
			top.popup_win("get_batch_file_report.php","report_window","resizable=0,scrollbars=no,status=1,height="+pphh+",width=1280,top=10,left=50");
			break;	
	}
}

function LoadBatchList(s,a){
	top.show_loading_image('hide');
	top.show_loading_image('show','200', 'Loading Batch Files...');
	if(s=='4'){
		s = 0;
		patval = a.value;
		url_part = "&bypatient="+patval;
		$('#show_batch_files_by').val('');
	}else if(s=='3'){
		s = 0;
		inscomps = a.value;
		url_part = "&insCompIDs="+inscomps;
		$('#show_batch_byPt').val('');
	}else{
		if(s=='0'){$('#chk_show_archive').prop('checked',false);$('#btn_unarchive').hide();	$('#btn_archive').show();}
		if(typeof(a)!='undefined'){
			if(a.checked){
				s=2;
				$('#btn_archive').hide();
				$('#btn_unarchive').show();
			}else{
				s=0;
				$('#btn_unarchive').hide();
				$('#btn_archive').show();
			}
		}
		url_part = "";
	}
	ajaxURL = "electronic_billing_ajax.php?eb_task=batch_list&filestatus="+s+url_part;
	//a=window.open(); a.document.write(ajaxURL);
	$.ajax({
	  url: ajaxURL,
	  dataType: "json",
	  success: function(r){//a=window.open(); a.document.write(r);
		showBatchList(r);
	  }
	});
}

function do_with_file(task){
	var fileId = ''; var ArrfileId = new Array(); i = 0;
	$('.chk_batch_file').each(function(){
		if($(this).prop('checked')){
			if(fileId==''){
				fileId += $(this).val();
			}
			else{
				fileId += ','+$(this).val();
			}
			
			ArrfileId[i] = $(this).val();
			i++;
		}
	});

	if(task=='Re-Generate' && (isNaN(fileId) || fileId=='')){
		top.fAlert('Please select one (1) Batch file to '+task+'.'); return;
	}else if(fileId==''){
		if(task=='Archive'){
			LoadBatchList(2);
			return;
		}
		top.fAlert('Please select Batch file(s) to '+task+'.'); return;
	}
	switch(task){
		case 'Archive':
		case 'Delete':
			top.fancyConfirm('Are you sure? Selected file(s) will be '+task+'d.','', "window.top.fmain.file_actions('"+fileId+"','"+task+"')");
			break;
		case 'Un-Archive':
			file_actions(fileId,task);
			break;
		case 'Re-Generate':
			file_actions(fileId,task);
			break;
	}
}

function file_actions(fileIDs,task){
	top.show_loading_image('hide');
	ArrfileId = fileIDs.split(',');
	if(task!='Re-Generate'){
		for(x in ArrfileId){
			$('#batch_tr_'+ArrfileId[x]).hide();
		}
	}
	ajaxURL = "electronic_billing_ajax.php?eb_task="+task+"&fileIDs="+fileIDs;
	if(task=='Re-Generate'){
		d		= document.frm;
		bT0		= d.batch_type[0];
		bT1		= d.batch_type[1];
		bType	= bT0.checked==true ? bT0.value : (bT1.checked==true ? bT1.value : 'P');
		ajaxURL += "&bType="+bType;
		top.show_loading_image('show','200', 'Regenerating Batch File...');
	}//else{}
	//a=window.open();a.document.write(ajaxURL);exit;
	$.ajax({
	  url: ajaxURL,
	  dataType: "json",
	  success: function(r) {//a=window.open();a.document.write(r);
		top.show_loading_image('hide');
		if(task!='Re-Generate'){
			if(r.indexOf('Error')>0){
				for(y in ArrfileId){
					$('#batch_tr_'+ArrfileId[y]).show();
				}
				showInfoWarning(r,false);
			}else{
				for(y in ArrfileId){
					$('#batch_tr_'+ArrfileId[y]).remove();
				}
				showInfoWarning(r);
			}
		}else{
			if(r != null){
				if(typeof(r)=='object'){
					if(typeof(r.error)!='undefined'){
						if(r.error.indexOf('Re-Generate Successfully')>0){
							top.fAlert(r.error,'',"window.top.fmain.viewFile('"+fileIDs+"')");
						}else if(r.error.indexOf('Encounter Does not Exists')>0){
							top.fAlert(r.error,'',"window.top.fmain.viewFile('"+fileIDs+"','"+r.error+"','"+r.invalidCHLids+"')");
						}else{
							top.fAlert('Invalid Claims','',"window.top.fmain.viewFile('"+fileIDs+"','"+r.error+"','"+r.invalidCHLids+"')");
						}
						LoadBatchList(0);
						return;
					}					
				}
			}
		}
	  }
	});
}

function showBatchList(r){
	o = $('#div_batch_list table tbody');
	h = '';
	o.html(h);
	for(x in r){
		s = r[x];
		fileId		= s.Batch_file_submitte_id;
		fileName 	= s.file_name.replace('.8375010','');
		claimPriSec	= s.ins_comp;
		if(claimPriSec=='primary')
			claimPriSec	= 'Primary Claims Batch';
		else if(claimPriSec=='secondary')
			claimPriSec	= 'Secondary Claims Batch';
		tsuc 		= s.Transaction_set_unique_control;
		statuss 		= s.status;
		clHouse		= s.clearing_house;
		fFormat		= s.file_format;
		ICnum		= s.Interchange_control;
		fGrpName	= s.file_group_name;
		fRptDate	= (s.file_upload_date!=null)?s.file_upload_date:'';
		img_path 	= 'publish_x.png';
		status1 	= "";
		if(statuss == 1){
			img_path 	= 'confirm3.png';
			status1 	= "Submitted";
		}else if(statuss ==2){
			img_path 	= 'confirm2.png';
			status1 	= "Submitted (Errors)";
		}
		if(fFormat=='5010'){
			tempGrpName = '';
			clHouse		= (clHouse=='visionshare')?'M_':(clHouse=='emdeon')?'E_':'';
			fileName=clHouse+fileName;
			if(fGrpName!=null){
				if(fGrpName.split(/\s+/).length==1){
					fileName=fGrpName.substring(0,3)+'_'+fileName;
				}else{
					ArrGrpName = fGrpName.split(/\s+/);
					for(i=0;i<ArrGrpName.length;i++){
						tempGrpName += ArrGrpName[i].substring(0,1);
					}
					if(tempGrpName.length>3){tempGrpName = tempGrpName.substring(0,3);}
				}
			}
			fileName=(tempGrpName!='')?tempGrpName.toUpperCase()+'_'+fileName:fileName;
		}
		if(fileName.length>26){fileName = fileName.substring(0,25)+'..';}
		newICnum = paddZero(ICnum,4);
		fileName = fileName+'('+newICnum+')';
		h += '<tr id="batch_tr_'+fileId+'" title="'+claimPriSec+'" class="link_cursor">';
		h += '<td data-label="" class="checkopt"><div class="checkbox"><input type="checkbox" class="chk_batch_file" name="chk_batch_file[]" id="chk_batch_file'+fileId+'" value="'+fileId+'"/><label for="chk_batch_file'+fileId+'"></label></div></td>';
		h += '<td data-label="File Name: " class="fname">'+fileName+'</td>';
		h += '<td data-label="Receive Date: " class="fname">'+fRptDate+'</td>';
		h += '<td><img src="../../library/images/'+img_path+'" title="'+status1+'" border="0"></td>';
		h += '</tr>';
	}
	if(h==''){h='<tr><td colspan="4" class="alignCenter warning text12b">No Record Found.</td></tr>'}
	o.append(h);//a=window.open();a.document.write(h);
	o.find('tr td.fname').click(function(){x=$(this).parent('tr').attr('id');y=x.split('_');viewFile(y[2]);});
	top.show_loading_image('hide');
}

var prev_selected_row = false; var prev_selected_row_clr = false;
function viewFile(fileId,errMsg,errId){
	if(prev_selected_row != false){$(prev_selected_row).removeClass('hover'); $(prev_selected_row).addClass('bg3');}
	if($('#batch_tr_'+fileId).hasClass('bg5')){prev_selected_row_clr='bg5';}
	$('#batch_tr_'+fileId).removeClass('bg5').removeClass('bg3');
	$('#batch_tr_'+fileId).addClass('hover');
	prev_selected_row = $('#batch_tr_'+fileId);
	var url = top.JS_WEB_ROOT_PATH+'/interface/billing/fileProcessView.php?fileId='+fileId+'&errId='+errId+'&errMsg='+escape(errMsg);
	top.popup_win(url);		
}

function showInfoWarning(HTMLmsg,autoHide){
	if(typeof(autoHide)!='boolean'){autoHide=true;}
	o=$('#div_batch_list table thead tr#infomsg');
	o.find('td').html(HTMLmsg).parent('tr').fadeIn('fast');
	if(autoHide){
		t=setTimeout(function(){o.fadeOut('fast');if($('#div_batch_list table tbody').html()==''){document.getElementById('chk_show_archive').click();}},1000);
	}else{
		o.find('td').html(HTMLmsg+'<div class="padd5"><input type="button" value="&nbsp;OK&nbsp;" class="dff_button_sm" onclick="$(\'#div_batch_list table thead tr#infomsg\').fadeOut();">');
	}
}

function paddZero(str,rL){
	sL = str.length; dL = rL-sL;
	if(dL>0){
		for(i=0;i<dL;i++){str = '0'+str;} 
	}
	return str;
}

function toggleInsGrpFrm(){
	i = $('#claim_ins_type').get(0).selectedIndex;
	if(i==0){$('#tbl_ins_group_form').show();}
	else{$('#tbl_ins_group_form').hide();}
}

function setCookie(c_name,value,exdays){
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : ";path=/; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}


//By Karan -- Setting the date to default format before sending it via ajax
function setDateFormatGloBAL(val)
{
	var get_date_val = val.split("-");
	var set_date_global_date_format_array = $.makeArray( get_date_val );
	var DateObj = {};
	
	
	var date_global_format = window.top.jquery_date_format;
	if(date_global_format == "dd-mm-yy")
	{
		var return_date_format = set_date_global_date_format_array[1]+"-"+set_date_global_date_format_array[0]+"-"+set_date_global_date_format_array[2];
	}	
	else
	{
		var return_date_format = val;
	}		
	
	return return_date_format;
		
}


function ProcessClaims(m){
	setCookie('ClaimsToBatch','',1);
	top.show_loading_image('hide');
	top.show_loading_image('show','200', 'Collecting Posted Encounters...');
	d = document.frm;
	
	pf			= setDateFormatGloBAL(d.pos_from.value);
	pu			= setDateFormatGloBAL(d.pos_upto.value);
	df			= setDateFormatGloBAL(d.dos_from.value);
	du			= setDateFormatGloBAL(d.dos_upto.value);
	
	instype		= d.claim_ins_type.value;
	grp			= d.group_select.value;
	ct0			= d.claim_type[0];
	ct1			= d.claim_type[1];
	ct			= ct0.checked==true ? ct0.value : (ct1.checked==true ? ct1.value : 'Primary');
	<?php if($all_ins_fromGrps != ''){?>
	allIns		= d.AllInsFromGroup.value;
	oInsComps	= d.opt_insComps.value;
	insGrp		= $('#Ins_groups').val();
	<?php }else{?>
	allIns		= '';
	oInsComps	= '';
	insGrp		= '';	
	<?php }?>
	bT0			= d.batch_type[0];
	bT1			= d.batch_type[1];
	bType		= bT0.checked==true ? bT0.value : (bT1.checked==true ? bT1.value : 'P');
	claims_to_process='';
	if(m=='1'){
		//chk_batch_claims//chk_claimsMA18
		var calim_in_batch_limit = 300;
		var claim_count = 0;
		$('.chk_batch_claims, .chk_claimsMA18').each(function(){
			c = $(this).prop('checked');
			v = $(this).val();
			if(c){// && claim_count <= calim_in_batch_limit
				ClaimTypeDD 	= $('.dd_'+v);
				ClmControlNum 	= $('.txt_claim_num_'+v);
				ClaimTypeDDVal 	= ClaimTypeDD.val()
				ClmControlNumVal= ClmControlNum.val();
				v = v+':'+ClaimTypeDDVal+':'+ClmControlNumVal;
				claims_to_process += claims_to_process=='' ? v : ', '+v;
				claim_count++;
				
			}
		});
		if(claims_to_process==''){top.show_loading_image('hide');top.fAlert('No claim selected.'); return;}
		else{
			setCookie('ClaimsToBatch',claims_to_process,1);
		//	insGrp = '';
		}
	}
	/*postVals 	= {eb_task:"ProcessClaims",actionType:m, pf:pf, pu:pu, instype:instype, grp:grp, ct:ct, df:df, du:du, allIns:allIns, oInsComps:oInsComps, insGrp:insGrp, bType:bType, ProcessClaims:claims_to_process};*/
	postVals 	= "eb_task=ProcessClaims&actionType="+m+"&pf="+pf+"&pu="+pu+"&instype="+instype+"&grp="+grp+"&ct="+ct+"&df="+df+"&du="+du+"&allIns="+allIns+"&ProcessClaims="+claims_to_process+"&oInsComps="+oInsComps+"&bType="+bType+"&insGrp="+insGrp;
	//a=window.open(); a.document.write("electronic_billing_ajax.php?"+postVals);
	$.ajax({
		type: "POST",
		data: postVals,
		url:"electronic_billing_ajax.php", 
		success:function(r){
		  	top.show_loading_image('hide');
			if(r.length==0){
				$('#result_area').html('No record found.');
				$('#right_banners').css('visibility','hidden');
				return;
			}
		  	//$('#result_area').html(r+'<hr>end of Response');
		 	r = jQuery.parseJSON(r);
			if(r != null){
				if(m=='1'){
					if(typeof(r)=='object'){
							if(typeof(r.status)=='string' && r.status=='success'){
								if(typeof(r.batchid)!='undefined'){
									LoadBatchList(0);

									batchFilesArr = r.batchid.split(',');
									for(x in batchFilesArr){
										viewFile(batchFilesArr[x]);
									}
									$('#result_area').html('');
								}
							}							
					}
					return;
				}
				h='';
				LastRowPhy = LastRowPhyMA18 = ''; MA18 = nonMA18 = ErroredRows = ''; switchRow=false;
				h += '<div class="res_area">';
				h += '	<table class="table table-bordered table-hover table-striped" id="tbl_eligible_encounters">';
				h += '	<thead class="header">';
				h += '	 	<tr class="grythead">';
				h += '		  <th><div class="checkbox"><input type="checkbox" name="chk_batch_claims_selAll" id="chk_batch_claims_selAll" class="chk_batch_claims_selAll" value="" onClick="selAll(this)"/><label for="chk_batch_claims_selAll"></label></div></th>';
				h += '		  <th>Format</th>';
				h += '		  <th>Patient Name </th>';
				h += '		  <th>EID</th>';
				h += '		  <th>DOS </th>';
				h += '		  <th>Insurance </th>';
				h += '		  <th>CPT </th>';
				h += '		  <th style="width:100px !important;">DX Codes</th>';
				h += '		  <th>Units </th>';
				h += '		  <th>Charges </th>';
				h += '		  <th>Modifiers</th>';
				h += '		  <th>Claim Control#</th>';
				h += '	 	</tr>';
				h += '	</thead>';			
				AmtValid 	= 0;
				AmtInvalid 	= 0; 
				TotalValid 	= 0;
				TotalInvalid = 0; 
				cur_row_claim_id = ''; ICD9_Html = ICD10_Html = '';
				for(x in r){
					s = r[x];//$('#result_area').append(x);
					row = '<tr>';
					ErrorHtml = ErrorClass = functionstr = ''; rowCharges = 0; switchRow=false;
					for(y in s){
					//	$('#result_area').append(s[y]+' :: ');
						tdVal = s[y];
						if(y=='icd9_10' && tdVal=='0' && ICD9_Html=='' && s['EncError']==''){
							ICD9_Html = 'ICD-9 Claims';
							row += '<td colspan="12" class="tdhdbar">'+ICD9_Html+'</td></tr><tr>';
						}
						if(y=='icd9_10' && tdVal=='1' && ICD10_Html=='' && s['EncError']==''){
							ICD9_Html = '';
							ICD10_Html = 'ICD-10 Claims';
							row += '<td colspan="12" class="tdhdbar">'+ICD10_Html+'</td></tr><tr>';
						}

						if(ct!='Primary' && y=='MA18'){switchRow=true;}
						if(y=='Phy' && ((!switchRow && LastRowPhy!=tdVal) || (switchRow && LastRowPhyMA18!=tdVal))){
							row += '<td colspan="12" class="physbar">PHYSICIAN: '+tdVal+'</td></tr>';
							if(switchRow){LastRowPhyMA18 = tdVal;}else{LastRowPhy = tdVal;}
						}
						if(y=='EncError' && tdVal!=''){
							tdVal = '&bull; '+tdVal;
							ErrorHtml = '</tr><tr><td colspan="12" class="infobar">'+tdVal+'</td></tr><tr>';
							ErrorClass = ' class="text-warning"';
						}
						if(y=='CLid'){
							if(switchRow){chkName = 'chk_claimsMA18';}else{chkName = 'chk_batch_claims';}
							if(ErrorHtml!=''){chk_disabled=' disabled';}else{chk_disabled='';}
							row += '	<td class="text-center"><div class="checkbox"><input type="checkbox" name="'+chkName+'" id="'+chkName+tdVal+'" class="'+chkName+'" value="'+tdVal+'"'+chk_disabled+'><label for="'+chkName+tdVal+'"></label></div></td>';
							cur_row_claim_id = tdVal;
						}else if(y=='PtFunc'){
							functionstr = tdVal;
						}else if(y=='ClmFormat'){
							ClmFormatClass = '';
							if(tdVal=='838' && y=='ClmFormat') ClmFormatClass = ' class="bg-warning" Title="Voided Claim"';
							htmlClmFormat = '<td'+ClmFormatClass+'><select name="dd_clm_format[]" class="dd_'+cur_row_claim_id+'">';
							htmlClmFormat += '<option value="'+tdVal.substr(2,1)+'">'+tdVal+'</option>';
							if(tdVal=='831'){tdVal='837';}else{tdVal='831';}
							htmlClmFormat += '<option value="'+tdVal.substr(2,1)+'">'+tdVal+'</option>';
							htmlClmFormat += '</select></td>';
							row += htmlClmFormat;
						}else if(y=='PtName'){
							//if(pat_icn!=''){tdVal += '<br>Claim Control#: '+pat_icn;}
							row += '<td'+ErrorClass+' onclick=\''+functionstr+'\' class="link_cursor" title="Click to view encounter">'+tdVal+'</td>';
						}else if(y=='Eid' || y=='DOS' || y=='InsName'){
							row += '<td'+ErrorClass+'>'+tdVal+'</td>';
						}
						if(y=='ChargesDetails' && typeof(tdVal)=='object'){//CPT DETAILS FOUND.
							for(z in tdVal){
								tdVal2 = tdVal[z];
								if(z=='charges'){
									rowCharges = tdVal2;
									rowCharges = rowCharges.replace(",","");
								}
								row += '<td style="white-space:inherit !important;">'+tdVal2+'</td>';
							}
						}
						if(y=='claimNum'){
							if(tdVal==false){tdVal=' ';}
							row += '<td'+ErrorClass+'><input class="txt_claim_num_'+cur_row_claim_id+'" name="txt_claim_num[]" type="text" size="11" value="'+$.trim(tdVal)+'"'+chk_disabled+' onchange="change_clm_type_dd(\''+cur_row_claim_id+'\',this);" /></td>';							
						}
					}
					row += ErrorHtml;
					row += '</tr>';
					if(ErrorHtml != ''){
						ErroredRows += row;
						AmtInvalid = parseFloat(AmtInvalid)+parseFloat(rowCharges);
						TotalInvalid++;
						//AmtValid = AmtInvalid = 0.00; TotalValid = TotalInvalid = 0;
					}else if(typeof(switchRow)=='boolean'){
						if(switchRow){MA18 += row; switchRow=false;}else{nonMA18 += row;}
						AmtValid = parseFloat(AmtValid)+parseFloat(rowCharges);
						TotalValid++;
					}
					//$('#result_area').append('<hr>');
				}
				AmtValid = AmtValid.toFixed(2);
				AmtInvalid = AmtInvalid.toFixed(2);
				if(ErroredRows != ''){ErroredRows = '<tr><td colspan="12" style="height:"25px; border:none;">&nbsp;</td></tr><tr><td colspan="12" class="text-center text-warning">ENCOUNTERS WITH ERRORS</td></tr>'+ErroredRows;}
				if(MA18 != ''){MA18 = '<tr><td colspan="12" style="height:"25px; border:none;">&nbsp;</td></tr><tr><td style="width:20px;"><input type="checkbox" name="chk_claimsMA18_selAll" class="chk_claimsMA18_selAll" value="" onClick="selAll(this)"></td><td colspan="12" class="text-center">INSURANCE CROSS OVER FROM PRIMARY PAYER</td></tr>'+MA18;}
				h += nonMA18+MA18+ErroredRows+'</tbody></table></div>';
				//h += '<div style="margin-top:25px; text-align:center"><input type="button" value=" &nbsp; Print &nbsp; " class="dff_button" onclick="printResult();"></div>';
				$('#result_area').html(h);//a=window.open(); a.document.write(h);
				$('#span_validclaims').html('<span>VALID CLAIMS</span>'+TotalValid);
				$('#span_validamount').html('<span>VALID AMOUNT</span>$'+AmtValid);
				$('#span_invalidclaims').html('<span>INVALID CLAIMS</span>'+TotalInvalid);
				$('#span_invalidamount').html('<span>INVALID AMOUNT</span>$'+AmtInvalid);
				$('#right_banners').css('visibility','visible');
				//$('div.res_area').height($('#result_area').height()-19);
				//$('div.res_area').css({width:'880px','overflow':'scroll'});
				//$('#tbl_eligible_encounters').addClass('cellBorder3');
				//$('#tbl_status_header').addClass('table_collapse cellBorder3');
				$('#result_area #tbl_eligible_encounters tr.error_encounter').addClass('text-warning');
				top.$('#create_process').attr('disabled',false);
				top.$('#print_result').show();
				document.getElementById('chk_batch_claims_selAll').click();
			}else{
				$('#result_area').html('<div class="alignCenter warning text12b">No Record Found.</div>');
			}
         // var content = $( data ).find( '#content' );
          //$( "#result" ).empty().append( content );
      }
	});
}

function change_clm_type_dd(id,obj){
	dd_obj 	= $('.dd_'+id);
	v 		= $.trim(obj.value);
	if(v==''){
		dd_obj.val('1');
	}else{
	//	dd_obj.val('7');	
	}
}

function printResult(){
	h = $('#right_banners').html();
	h += $('.res_area').html();
	$('#result_area_2').html(h);
	jq171('#result_area_2').printElement();
}

function selAll(o){
	switch(o.name){
		case 'chk_sel_all_batches':
			$('.chk_batch_file').each(function(){$(this).prop('checked',o.checked);});
			break;	
		case 'chk_batch_claims_selAll':
			$('.chk_batch_claims').each(function(){if(!$(this).prop('disabled')){$(this).prop('checked',o.checked);}});
			break;
		case 'chk_claimsMA18_selAll':
			$('.chk_claimsMA18').each(function(){if(!$(this).prop('disabled')){$(this).prop('checked',o.checked);}});
			break;	
	}
}

function showE(ptid,eid){
    //To check restrict access of patient before load
    $.when(top.check_for_break_glass_restriction(ptid)).done(function(response){
        top.removeMessi();
        if(response.rp_alert=='y') {
            var patId=response.patId;
            var bgPriv=response.bgPriv;
            var rp_alert=response.rp_alert;
            top.core_restricted_prov_alert(patId, bgPriv, '','',eid);
        }else{
            url = '<?php echo $GLOBALS['rootdir'];?>/billing/set_session.php?patient='+ptid+'&eid='+eid;
            sc_wd=(screen.availWidth-20);
            sc_hg=(screen.availHeight-50);
            top.popup_win(url,"left=0,top=0,resizeable=1,scrollbars=1,menubar=0,toolbar=0,status=0,width="+sc_wd+",height="+sc_hg);
        }
    });
}
function match_height(cls_name){
	if(typeof(cls_name)=='undefined') cls_name = 'match_height';
	max_h = 0;
	$('.'+cls_name).each(function(index, element) {
        this_h = $(this).height();
		if(this_h > max_h) max_h = this_h;
    });
	$('.'+cls_name).css('height',max_h);
}

/*--btn Name--btn Value-- arg for on click Function*/
var mainBtnArr = new Array();
mainBtnArr[0] = new Array("start_process","View Claims","top.fmain.ProcessClaims(0);");
mainBtnArr[1] = new Array("create_process","Create Batch","top.fmain.ProcessClaims(1);");
mainBtnArr[2] = new Array("print_result","Print Claims","top.fmain.printResult();");
top.btn_show("PPR",mainBtnArr);
top.$('#create_process').attr('disabled',true);
top.$('#print_result').hide();
	
top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
</script>
</body>
</html>
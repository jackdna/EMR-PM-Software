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
File: get_batch_file_reports.php
Purpose: To get reports form clearing house
Access Type: Direct Access
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
$objEBilling = new ElectronicBilling();

$cert_data_arr = $objEBilling->getVScertNgroups();
$vs_module = true;
$perPageShow = 10;

if($cert_data_arr==false || count($cert_data_arr)==0){ //No ability cert available.
	$vs_module = false;	
	$perPageShow = 20;
}

$groups_details = $objEBilling->get_groups_detail();
$group_filter_options = '<option value="">-ALL-</option>';
foreach($groups_details as $grp_id=>$grp_rs){
	$group_filter_options .= '<option value="'.$grp_id.'">'.$grp_rs['name'].'</option>';
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Electronic Billing Reports</title>
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jPaginationstyle.css" rel="stylesheet">
<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
<?php } ?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/html5shiv.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/respond.min.js"></script>
<![endif]-->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.paginate.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<script type="text/javascript">
var default_resotred_layout = default_page_to_view = default_page_to_view_vs = 1;
var num_of_rows = <?php echo $perPageShow;?>;
var emd_selected_group = '';
var vis_selected_group = '';
$(document).ready(function(e) {
	if(js_GET('emd_page')!=false && typeof(js_GET('emd_page'))!='undefined'){default_page_to_view=js_GET('emd_page');}
	if(js_GET('page')!=false && typeof(js_GET('page'))!='undefined'){default_page_to_view_vs=js_GET('page');}
    setTimeout(function(){init_disp_billing_rpt();},200);
});

function init_disp_billing_rpt(obj_id){
	obj_id = typeof(obj_id)=='string' ? '' : obj_id;
	if(obj_id != ''){
		$('#div_emd_reports, #div_vis_reports, #div_vis_btns').show();
		$('.reports_result, #pageNsEmd, #pageNsVis').html('');
	}
	h 		= pageHeight();
	pbh		= $('.bilnghead').height()*2;
	sbh		= $('.tableHeader').height()*2;
	h		= parseInt(h-pbh-sbh);
	<?php if($vs_module){?>h1		= parseInt((h/2)-36);<?php }else{?>h1		= h;<?php }?>
	$('#div_emd_reports').height(h1);
	$('#div_emd_reports .reports_result').height(h1-30);
	<?php if($vs_module){?>$('#div_vis_reports').height(h1-50);$('#div_vis_reports .reports_result').height(h1-30);<?php }?>
	num_of_rows = parseInt(h1/34);
	<?php if($vs_module){?>num_of_rows--;<?php }?>
	loadResult(default_page_to_view);
}

function maximize_me(obj_id){
	if(default_resotred_layout==0){
		default_resotred_layout = 1;
		init_disp_billing_rpt(obj_id);
		return;	
	}else{
		default_resotred_layout = 0;
	}
	h 		= pageHeight();
	pbh		= $('.page_block_heading_patch').height()*2;
	sbh		= $('.subheading').height()*2;
	h		= parseInt(h-pbh-sbh-50);
	num_of_rows = parseInt(h/23);
	if(obj_id=='div_emd_reports'){
		$('#div_emd_reports').height(h);
		$('#div_emd_reports .reports_result').height(h-30);
		$('#div_emd_reports').show();
		$('#div_vis_reports, #div_vis_btns').hide();
		loadResult(default_page_to_view,'',true);
	}else if(obj_id=='div_vis_reports'){
		$('#div_vis_reports').height(h);
		$('#div_vis_reports .reports_result').height(h-30);
		$('#div_emd_reports').hide();
		$('#div_vis_reports, #div_vis_btns').show();
		loadResultVS(default_page_to_view_vs,'',true);
	}
}

function onReady(pagesToShow,selected_page){
	$("#pageNsEmd").html('');
	$("#pageNsEmd").paginate({
		count 					: pagesToShow,
		start 					: selected_page,
		display    				: 10,
		border					: true,
		border_color			: '#fff',
		text_color  			: '#fff',
		background_color    	: '#444',	
		border_hover_color		: '#ccc',
		text_hover_color  		: '#000',
		background_hover_color	: '#fff', 
		images					: true,
		mouse					: 'press',
		onChange     			: function (page){loadResult(page);}
	});
}
<?php if($vs_module){?>
function onReadyVS(pagesToShow,selected_page){
	$("#pageNsVis").html('');
	$("#pageNsVis").paginate({
		count 					: pagesToShow,
		start 					: selected_page,
		display     			: 10,
		border					: true,
		border_color			: '#fff',
		text_color  			: '#fff',
		background_color    	: '#444',	
		border_hover_color		: '#ccc',
		text_hover_color  		: '#000',
		background_hover_color	: '#fff', 
		images					: true,
		mouse					: 'press',
		onChange     			: function (page){loadResultVS(page);}
	});
}

function loadResultVS(page,grpObj,resetNums){
	if(typeof(resetNums)=='undefined') resetNums=false;
	if(typeof(page)=='undefined' || page==''){page = default_page_to_view_vs;}else{default_page_to_view_vs = page;}
	if(typeof((grpObj))!='undefined' && grpObj!=''){
		if(grpObj.id=='VisFilterGrp'){vis_selected_group = grpObj.value;page=default_page_to_view_vs = 1;}
	}
	con = $('#div_vis_reports .reports_result');
	con.prepend('<img src="../../library/images/doing.gif" class="m20" style="position:absolute;">');
	var uurl = "electronic_billing_ajax.php?eb_task=getEBReportsVS&upto="+num_of_rows+"&st="+page+"&VisGroupSelVal="+vis_selected_group;
	$.ajax({
		type: "POST",
	 	dataType: "json",
		url: uurl,
		success: function(r1){
			con.html('');
			window.location.hash = '?emd_page='+default_page_to_view+'&page='+default_page_to_view_vs;
			showReportsVS(r1,page,resetNums);
		}
	});
}

function showReportsVS(r1,selectedPage,resetNums){
	GroupsRS		= r1.GroupData;
	VisReportsData	= r1.VisReports;
	VisReports		= VisReportsData.result;
	if($('#pageNsVis').html()=='' || resetNums){
		pagesToShow		= VisReportsData.pagesToShow;
		onReadyVS(pagesToShow,selectedPage);
	}	
	o = $('#div_vis_reports .reports_result');
	o.html('');
	if(VisReports==false || VisReports=='false'){
		o.html('<div class="alignCenter warning text12b">No Record Found.</div>');
	}else{
		h = '<table class="table table-bordered table-striped table-hover">';
		for(x in VisReports){
			s = VisReports[x];
			report_id			= s.vs_report_id;
			batchXML			= s.batchXML;
			report_recieve_date	= s.receiveTimeDate;
			group_name			= s.group_name;if(group_name.length>25) group_name = group_name.substring(0,25)+'...';
			VSFileName 			= s.VSFileName;
			VSFileURI			= s.VSFileURI;
			imedicProcessStatus	= s.imedicProcessStatus;
			ProcessStatus997	= s.ProcessStatus997;
			VSCertUsed			= s.VSCertUsed;			
			operator_nm			= s.operator_nm;
			db997ProcessStatus	= s.db997ProcessStatus;
			
			anchor_to_file		= '<a class="a_clr1 link_cursor"  onClick="get_uri_data(\''+VSFileURI+'\',\''+VSFileName+'\', \''+report_id+'\', \'\', \''+imedicProcessStatus+'\', \''+VSCertUsed+'\')">'+VSFileName+'</a>';
			financial_enquiry	= '';
			if (db997ProcessStatus == 0)
				financial_enquiry	= '<a class="a_clr1 link_cursor"  onClick="get_uri_data(\''+VSFileURI+'\',\''+VSFileName+'\', \''+report_id+'\', \'\', \''+imedicProcessStatus+'\', \''+VSCertUsed+'\')">Not Processed</a>';
			else if(db997ProcessStatus == 1)
				financial_enquiry	= '<a class="a_clr1 link_cursor"  onClick="get_uri_data(\''+VSFileURI+'\',\''+VSFileName+'\', \''+report_id+'\', \'\', \''+imedicProcessStatus+'\', \''+VSCertUsed+'\')">Processed</a>';
			else if(db997ProcessStatus == 2)
				financial_enquiry	= '<a class="a_clr1 link_cursor"  onClick="get_uri_data(\''+VSFileURI+'\',\''+VSFileName+'\', \''+report_id+'\', \'error\', \''+imedicProcessStatus+'\', \''+VSCertUsed+'\')">Error</a>';
			else if(db997ProcessStatus == "277CA")
				financial_enquiry	= '<a class="a_clr1 link_cursor"  onClick="get_uri_data(\''+VSFileURI+'\',\''+VSFileName+'\', \''+report_id+'\', \'277CA\', \''+imedicProcessStatus+'\', \''+VSCertUsed+'\')">Claim Fin. Inq.</a>';

			/*
			group_nm			= s.group_name; if(group_nm.length>30) group_nm = group_nm.substring(0,30)+'...';
			anchor_to_file		= s.anchor_to_file;
			financial_enquiry	= s.financial_enquiry;
			wsMessageType		= s.wsMessageType;*/
			
			/****SETTING READ ICON AND TITLE*****/
			img_path 	= 'file.png';
			status1 	= "Unread";
			if(imedicProcessStatus == 1){
				img_path 	= 'confirm3.png';
				status1 	= "Read";
			}
			/****SETTING MAIN RESULTSET (EMDEON)***/
			h += '<tr id="report_tr_'+report_id+'">';
			h += '<td class="col_chk"><input type="checkbox" class="chk_report_file2" name="chk_batch_file2[]" value="'+report_id+'"></td>';
			h += '<td class="col_fname">&nbsp;'+anchor_to_file+'</td>';
			h += '<td class="col_recvdt">&nbsp;'+report_recieve_date+'</td>';
			h += '<td class="col_read"><img src="../../images/'+img_path+'" title="'+status1+'" border="0"></td>';
			h += '<td class="col_dwnby">&nbsp;'+operator_nm+'</td>';
			h += '<td class="col_grpnm">&nbsp;<span class="span_col_grpnm">'+group_name+'</span></td>';
			h += '<td class="col_finenq">'+financial_enquiry+'</td>';
			h += '</tr>';
		}
		if(h==''){h='<tr><td colspan="4" class="alignCenter warning text12b">No Record Found.</td></tr>'}
		else{$('#btn_vis_del').show();}
		o.html(h);
		/******SETTING GROUP DROPDOWN VALUE (BOTH)*****/
		if($('#VisFilterGrp').html()==''){
			grp_options = ''; grp_ids = new Array();
			for(g in GroupsRS){
				grp_rs 		= GroupsRS[g];
				grp_ids[g]	= grp_rs.grp_id;
				grp_options+= '<option value="'+grp_rs.grp_id+'">'+grp_rs.grp_name+'</option>';
			}
			grp_ids_all = grp_ids.join(',');
			grp_options = '<option value="'+grp_ids_all+'">--All--</option>'+grp_options;
			$('#VisFilterGrp').html(grp_options);
		}
	}
}

function get_uri_data(uri, fileName, recId, op, viewedStatus, certUse){
	recId = recId || false;
	op = op || '';
	var w = document.body.clientWidth;
	certUse = certUse || 'VS_PASS_WORD';
	var h = pageHeight()-120;
	if(fileName.substr(0,4)=='999.' || fileName.substr(0,3)=='997.'){
		w	=	1000;
		h	=	600;
	}
	var selectedVsDropD = document.getElementById("VisFilterGrp").value;
	var url = 'vision_share_view_uri.php?vs_uri='+escape(uri)+'&vs_file_name='+escape(fileName)+'&vs_rec_id='+recId+'&op='+op+'&viewedStatus='+viewedStatus+'&certUse='+certUse+'&selectedVsDropD='+selectedVsDropD;
	window.open(url,'view_vision_share_uri','menubar=0,scrollbars=1,height='+h+',resizable=no,status=1,width='+w+',left=10,top=10');			
}
<?php }?>
function loadResult(page,grpObj,resetNums){
	if(typeof(resetNums)=='undefined') resetNums=false;
	if(typeof(page)=='undefined' || page==''){page = default_page_to_view;}else{default_page_to_view = page;}
	if(typeof((grpObj))!='undefined' && grpObj!=''){
		if(grpObj.id=='EmdFilterGrp'){emd_selected_group = grpObj.value;page=default_page_to_view = 1;}
	}
	con = $('#div_emd_reports .reports_result');
	con.prepend('<img src="../../library/images/doing.gif" class="m20" style="position:absolute;">');
	//a=window.open();a.document.write("electronic_billing_ajax.php?eb_task=getEBReports&upto="+num_of_rows+"&st="+page+"&EmdGroupSelVal="+emd_selected_group);
	$.ajax({
		type: "POST",
	 	dataType: "json",
		url: "electronic_billing_ajax.php?eb_task=getEBReports&upto="+num_of_rows+"&st="+page+"&EmdGroupSelVal="+emd_selected_group,
		success: function(r){
			con.html('');
			window.location.hash = '?emd_page='+default_page_to_view+'&page='+default_page_to_view_vs;
			showReports(r,page,resetNums);
			if($('#div_vis_reports .reports_result').html()==''){
				loadResultVS(default_page_to_view_vs);
			}
		}
	});
}

function showReports(r,selectedPage,resetNums){
	//GroupsRS		= r.GroupData;
	EmdReportsData	= r.EmdReports;
	EmdReports		= EmdReportsData.result;
	if($('#pageNsEmd').html()=='' || resetNums){
		pagesToShow		= EmdReportsData.pagesToShow;
		onReady(pagesToShow,selectedPage);
	}
	o = $('#div_emd_reports .reports_result');
	o.html('');
	if(EmdReports==false || EmdReports=='false'){
		o.html('<div class="alignCenter warning text12b">No Record Found.</div>');
	}else{
		h = '<table class="table table-bordered table-hover">'
		for(x in EmdReports){
			s = EmdReports[x];
			report_id			= s.emdeon_report_id;
			report_status		= s.report_status;
			report_recieve_date	= s.report_recieve_date;
			read_status 		= s.read_status;
			operator_nm			= s.operator_nm;
			group_nm			= s.group_name; if(group_nm.length>25) group_nm = group_nm.substring(0,25)+'...';
			anchor_to_file		= s.anchor_to_file;
			financial_enquiry	= s.financial_enquiry;
			wsMessageType		= s.wsMessageType;
			
			/*****SETTING READ ICON AND TITLE******/
			img_path 	= 'glyphicon-folder-close';
			status1 	= "Unread";
			if(read_status == 1){
				img_path 	= 'glyphicon-folder-open';
				status1 	= "Read";
			}
			
			/*****SETTING MAIN RESULTSET (EMDEON)****/
			h += '<tr id="report_tr_'+report_id+'">';
			h += '<td class="col1 text-center"><div class="checkbox"><input type="checkbox" class="chk_report_file1" name="chk_batch_file1[]" id="chk'+report_id+'" value="'+report_id+'"><label for="chk'+report_id+'"></label></div></td>';
			h += '<td data-label="Report Name: " class="col2">&nbsp;'+anchor_to_file+'</td>';
			h += '<td data-label="Receive Date: " class="col3 text-center">&nbsp;'+report_recieve_date+'</td>';
			h += '<td data-label="Read Status: " class="col4 text-center"><span class="glyphicon '+img_path+'" title="'+status1+'"></span></td>';
			h += '<td data-label="Downloaded By: " class="col5">&nbsp;'+operator_nm+'</td>';
			h += '<td data-label="Group Name: " class="col6">&nbsp;<span class="span_col_grpnm">'+group_nm+'</span></td>';
			h += '<td data-label="Batch No.: " class="col7">'+financial_enquiry+'</td>';
			h += '</tr>';
		}
		if(h==''){h='<tr><td colspan="4" class="alignCenter warning text12b">No Record Found.</td></tr>'}
		else{$('#btn_emd_del').show();}
		o.html(h);
		
		/******SETTING GROUP DROPDOWN VALUE (BOTH)*****/
		/*if($('#EmdFilterGrp').html()==''){
			grp_options = ''; grp_ids = new Array();
			for(g in GroupsRS){
				grp_rs 		= GroupsRS[g];
				grp_ids[g]	= grp_rs.grp_id;
				grp_options+= '<option value="'+grp_rs.grp_id+'">'+grp_rs.grp_name+'</option>';
			}
			grp_ids_all = grp_ids.join(',');
			grp_options = '<option value="'+grp_ids_all+'">--All--</option>'+grp_options;
			$('#EmdFilterGrp, #VisFilterGrp').html(grp_options);
		}*/
	}
}

function open_file(url){
	window.open(url,'','menubar=0,width=1280,scrollbars=0,status=1,resizable=yes,height=<?php echo $_SESSION['wn_height'];?>');
}

function viewHumanReadable(rptId,RptType){
	if(typeof(RptType)=='undefined'){RptType='comm';}
	url = 'electronic_billing_decode.php?eb_task=EDI2Human&report_id='+rptId+'&report_type='+RptType;
	window.open(url,'viewHumanReadable','width=1000,height=600,menubar=0,scrollbars=1,resizable=yes,status=1');
}

function js_GET(key){
	var querystring = window.location.hash;
	q = querystring.substring(2);
	arr_q_vars = q.split("&");
	for (i=0;i<arr_q_vars.length;i++){
		key_name = arr_q_vars[i].split("=");
		if (key_name[0] == key){
			return key_name[1];
		}
	}
}

function sel_all(o){
	id = o.id;
	if(id=='emd_sel_all'){
		$('.chk_report_file1').each(function(){$(this).prop('checked',o.checked);});
	}else if(id=='vis_sel_all'){
		$('.chk_report_file2').each(function(){$(this).prop('checked',o.checked);});
	}
}

function delRecords(s){
	var fileId = ''; var ArrfileId = new Array(); i = 0;
	if(s=='emd') chk = '.chk_report_file1';
	else if(s=='vis') chk = '.chk_report_file2';
	
	$(chk).each(function(){
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
	if(fileId==''){
		top.fAlert('Please select reports to delete.');
		return;
	}
	top.fancyConfirm('Are you sure? Selected file(s) will be deleted.','',"top.file_actions('"+fileId+"','"+s+"')");
}

function file_actions(fileIDs,task){
	ArrfileId = fileIDs.split(',');
	task1 = task+'_reports_del';
	ajaxURL = "electronic_billing_ajax.php?eb_task="+task1+"&fileIDs="+fileIDs;
//	a=window.open();a.document.write(ajaxURL);exit;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {//a=window.open();a.document.write(r);
		if(r=='success'){
			if(task=='emd') loadResult();
			else if(task=='vis') loadResultVS();
		}		
	  }
	});
}

function get_reports(cl,o){
	$(o).prop('disabled','disabled');
	$('#'+cl+'_rpt_process').show();
	task = cl+'_reports_get';
	ajaxURL = "electronic_billing_ajax.php?eb_task="+task;//	a=window.open();a.document.write(ajaxURL);exit;					
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {//a=window.open();a.document.write(r);
		if(r!=''){top.fAlert(r);}
		$('#'+cl+'_rpt_process').hide();
		$(o).prop('disabled',false);			
	  	if(cl=='emd') loadResult();
	  	else if(cl=='vis') loadResultVS();
	  }
	});
}
</script>
</head>
<body>
<div class="billreport">
    <div class="bilnghead">
        <div class="row">
        	<div class="col-sm-8">
            	<h2>
            		EMDEON Electronic Billing Reports 
                    <span id="emd_rpt_process" class="hide"> <img src="../../library/images/doing.gif"> Connecting to clearing house...please wait</span>
                </h2>
            </div>
        	<div class="col-sm-2 reptrht">
            	<button class="btn btn-success" data-toggle="modal" data-target="#div_get_conf"><span class="glyphicon glyphicon-upload " aria-hidden="true"></span> Upload Reports</button>
        	</div>
            <div class="col-sm-2 reptrht">
            	<button class="btn btn-success" onClick="get_reports('emd',this);"><span class="glyphicon glyphicon-save " aria-hidden="true"></span> Download</button>
        	</div>
        </div>
    </div>
	<div class="clearfix"></div>
    <div class="respotable reporttabl tableHeader">
        <table class="table table-bordered table-hover">
        <thead>
            <tr class="grythead">
                <th class="col1"><div class="checkbox"><input type="checkbox" name="emd_sel_all" id="emd_sel_all" value="1" onClick="sel_all(this);"/><label for="emd_sel_all"></label></div></th>
                <th class="col2">Report Name</th>
                <th class="col3">Receive Date</th>
                <th class="col4">Read</th>
                <th class="col5">Downloaded By</th>
                <th class="col6"><select class="selectpicker show-menu-arrow" data-width="100%" data-actions-box="false" id="EmdFilterGrp" onChange="loadResult('',this)">
                    <?php echo $group_filter_options;?>
                </select></th>
                <th class="col7">&nbsp;</th>
            </tr>
        </thead>
        </table>
    </div>
    <div id="div_emd_reports">
        <div class="reports_result table-responsive respotable reporttabl"></div>
        <div id="pageNsEmd"></div>
    </div>
</div>


<?php if($vs_module){?>
<div class="billreport">
    <div class="bilnghead">
        <div class="row">
        	<div class="col-sm-8">
            	<h2>
            		VISION SHARE Electronic Billing Reports 
                    <span id="vis_rpt_process" class="hide"> <img src="../../library/images/doing.gif"> Connecting to clearing house...please wait</span>
                </h2>
            </div>
        	<div class="col-sm-4 reptrht">
            	<button class="btn btn-success" onClick="get_reports('vis',this);"><span class="glyphicon glyphicon-save " aria-hidden="true"></span> Download</button>
        	</div>
        </div>
    </div>
	<div class="clearfix"></div>
    <div class="table-responsive respotable reporttabl tableHeader">
        <table class="table table-bordered table-hover">
        <thead>
            <tr class="grythead">
                <th class="col1"><div class="checkbox"><input type="checkbox" name="vis_sel_all" id="vis_sel_all" value="1" onClick="sel_all(this);"/><label for="vis_sel_all"></label></div></th>
                <th class="col2">Report Name</th>
                <th class="col3">Receive Date</th>
                <th class="col4">Read</th>
                <th class="col5">Downloaded By</th>
                <th class="col6"><select class="selectpicker show-menu-arrow" data-width="100%" data-actions-box="false" onChange="loadResultVS('',this)">
                     <?php echo $group_filter_options;?>
                </select></th>
                <th class="col7">&nbsp;</th>
            </tr>
        </thead>
        </table>
    </div>
    <div id="div_vis_reports">
        <div class="reports_result table-responsive respotable reporttabl"></div>
        <div id="pageNsVis"></div>
    </div>
</div>



<?php }?>


<div class="clearfix"></div>
<div class="text-center">
   	<input type="button" id="btn_emd_del" class="btn btn-warning" value="Delete Reports" onClick="delRecords('emd');">&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" class="btn btn-info" value="Close" onClick="window.close();">
</div>
<div class="clearfix"></div>

<!-- Modal (UPLOAD REPORT CONFIRMATION)-->
<div id="div_get_conf" class="modal fade" role="dialog" data-keyboard="false">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Confirmation Report (997/999)</h4>
      </div>
      <div class="modal-body" id="get_conf_body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="text text-warning" id="divErrorText"></div>
                    <form method="post" enctype="multipart/form-data" autocomplete="off" action="upload_file.php" name="get_conf_form" id="get_conf_form">
                        <div class="form-group">
                            <label for="email">Select File</label>
                            <input type="file" name="uploadFile" id="uploadFile" class="form-control" />
                        </div>
                        <div class="form-group text-center mt20">
                            <input type="submit" class="btn btn-success" id="saveUploadFile" name="saveUploadFile" value="Upload" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
if(typeof(window.opener.top.innerDim)=='function'){
	var innerDim = window.opener.top.innerDim();
	if(innerDim['w'] > 1980) innerDim['w'] = 1850; else innerDim['w'] = innerDim['w']-50;
	if(innerDim['h'] > 1024) innerDim['h'] = 950; else innerDim['h'] = innerDim['h']-20;
	window.resizeTo(innerDim['w'],innerDim['h']);
	//init_disp_billing_rpt();
	/*
	brows	= get_browser();
	if(brows!='ie') innerDim['h'] = innerDim['h']-35;
	var result_div_height = innerDim['h']-210;
	$('.resultset_div').height(result_div_height+'px');
	*/
}
</script>
</body>
</html>
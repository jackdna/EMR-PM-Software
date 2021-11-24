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

File: glucomaLog.php
Purpose: This file provide Glucoma Log Section in Glucoma Flow Sheet.
Access Type : Direct
*/
	require_once('../../config/globals.php');
	extract($_REQUEST);
	//Check patient session and closing popup if no patient in session
	$window_popup_mode = true;
	require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

	$library_path = $GLOBALS['webroot'].'/library';
	include_once $GLOBALS['srcdir']."/classes/common_function.php";
	include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
	include_once $GLOBALS['srcdir']."/classes/SaveFile.php";
	
	
	$pid = $_SESSION['patient'];
	$auth_id = $_SESSION['authId'];
	
	$glaucoma_obj = New ChartGlucoma($pid);
	
	
	/// function 
	function getGlucomaGraph($series, $seriesName, $axisName, $graphTitle, $imgId, $seriesColor){		
		echo "<img id=\"".$imgId."\"". 
				"src=\"getGlucomaGraph.php?series=".base64_encode(serialize($series)).
				"&seriesName=".base64_encode(serialize($seriesName)).
				"&axisName=".base64_encode(serialize($axisName)).
				"&graphTitle=".$graphTitle.
				"&seriesColor=".base64_encode(serialize($seriesColor)).
				"\" alt=\"image\" style=\"display:none;top:0px;\">";
		
	}
	
	function getMenuValue($str){
		global $glaucoma_obj;	
        $retStr = "";
		$arrCheck = array("Normal","Border Line", "PS", "Increase Abnormal", "Decrease Abnormal", "No Change Abnormal", "Abnormal", "Stable");
		if(!empty($str)){
            foreach($arrCheck as $key => $val){			           
                if(strpos($str,$val) !== false){
				    $retStr = $val;
				    break;
			    }
		    }
        }
		return ($retStr == "Empty") ? "" : $glaucoma_obj->refineMenuValue($retStr);
	}	

	///
		
	function getGlaucomaDeactivated($patient_id){
		$sql = "SELECT *
				FROM glucoma_main 
				WHERE patientId ='".$patient_id."'
				AND	activate = '0'
				";
		return sqlStatement($sql);		
	}
	
	
	function removeUnwantedSigns($str){
		return preg_replace("/(^\,|\,$)/","",$str);	
	}
	
	// Default Value
	$elem_activate = "-1";	
	// elemActivate
	if(isset($_POST["elem_activate"]) && !empty($_POST["elem_activate"])){
		$elem_activate = $_POST["elem_activate"];   
	}
	else if(isset($_GET["mode"]) && !empty($_GET["mode"])){
		$elem_activate = $_GET["mode"];   
	}
  
    //if Past Glucoma Record exists    
    $sql = "SELECT *
            FROM glucoma_main 
            WHERE patientId = '".$glaucoma_obj->pid."' 
            AND activate = '1' ";    
    $row = sqlQuery($sql);
    if($row != false)
    {        
      $elem_glucomaId = $row["glucomaId"];
      $elem_activate = $row["activate"];  
    } 
	
?>
<!DOCTYPE html>
<html>
<head>
<title>Log</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	div#conAddNewTable{ display:none;}	
	form { margin:0px;}
    .frmDiv { display: none;}
    div#divGonio{width:46px; overflow:hidden;}
    
    .purged td,.purged textarea,.purged td a{text-decoration: line-through;color:red;}
    #ox_lbl_view{position:absolute;background-color:#ffffff;margin-top:60px; border:1px solid #333;}
	
	.td_30w{
		min-width:30px;
		min-height:30px;
	}
  
  table.t_p_cl, .t_p_cl tbody
  {
	padding:0px !important;
	margin:0px !important;  	
  }
  table.t_p_cl td
  {
	  padding:3px !important;
	  margin:0px !important;
	  font-size:10px;
  }
  
  .t_p_cl, .t_p_cl td
  {
	border:1px solid #999;
	border-collapse:collapse;  
  }
  
	.table{margin-bottom:0px!important;}
	.table [class^="hide_col_"] {
		max-width:300px!important;
		min-width:200px!important;
	}
	
</style>
<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Refractive Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/gfs.css"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/amcharts.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/serial.js" type="text/javascript"></script>


<!-- Simple Menu -->
<script>
    function editLog(id){
      var objFrmAddNew = top.frmAddNew;      
      var idDiv = "conDiv_".concat(id);
      var objDiv = document.getElementById(idDiv);
      var objBtnDisplay = top.document.getElementById("btnAddNew");      
      top.displayAddNewForm(objBtnDisplay,true);
      if(objDiv != null)
      {
        var objDivElems = objDiv.getElementsByTagName("INPUT");
        var len = objDivElems.length;
        var name=value="";
        for(var i=0;i<len;i++)
        {            
            name=objDivElems[i].name;
            value=objDivElems[i].value;
            name=name.substring(0,name.lastIndexOf("_"));
            if(name.indexOf("elem_medicationRecommend") != -1)
            {
                name = name.replace(/Recommend/g,"");    
            }                                        
            //alert(name+"\nhello\n "+objFrmAddNew.elements[name]);
            var objAddElem = objFrmAddNew.elements[name];
            switch(objAddElem.type)
            {
               case "text":
               case "select-one":
               case "textarea":
               case "hidden":
                objAddElem.value = value;
                if(typeof objAddElem.onchange == "function")
                {
                  objAddElem.onchange();
                }
               break;
               case "checkbox":
                if(value == "1")
                {
                  objAddElem.checked = true;      
                }    
               break;
            }
        }       
      }             
    }

	function hide_col_function(val){
		var display_stat=$('.hide_col_'+val).css('display');
		if(display_stat=='none'){
			$('#elem_show_data_'+val).val(0);
			$('.hide_col_'+val).css({'display':'table-cell'});
			$('#arrow_dn_'+val).css({'display':'none'});
			$('#arrow_up_'+val).css({'display':'inline'});
			$('#arrow_dn_'+val).removeClass('box_wrapped_full');
			show_up_cols_auto_click();			
		}else{
			$('#elem_show_data_'+val).val(1);
			$('.hide_col_'+val).css({'display':'none'});
			$('#arrow_dn_'+val).css({'display':'inline'});
			$('#arrow_dn_'+val).addClass('box_wrapped_full')
			$('#arrow_up_'+val).css({'display':'none'});
		}		
	}
	
	function hide_up_col(i)
	{		
		i = parseInt(i);
		var j = 0;
		while(i >= 0)
		{				
			if($('#elem_show_data_'+i).val() == 1)
			{
				i--;				
				continue;	
			}
								
			$(".hide_col_"+i).each(function()
			{
				if($(this).hasClass('commentsNotInc'))
				{
					$(this).css({'display':'table-cell'});
				}
				else
				{
					if($(this).hasClass('nHide'))
					{}
					else
					{
						$(this).css({'display':'none'});	
					}					
				}
			});
			
			j = i;
			i--;			
		}
		parent_hide_cols(0);
		$("#bl_coll_uncollapse").css({"display":"inline"});
		set_height_col();
		
		if( top.resizeIframe)
			top.resizeIframe('iframe[name="iframeLog"]');
	}
	
	function parent_hide_cols(mode)
	{
		if(mode == 0)
		{
			$('.leftColsHide').css({'display':'none'});	
			if(document.getElementById('add_row_'+1)){
				if(document.getElementById('add_row_'+1).style.display=='block'){
					top.add_new_col(document.btnAddNew,'1');	
				}
			}
		}
		else if(mode == 1)
		{
			$('.leftColsHide').css({'display':'table-cell'});										
		}
	}
	
	function show_up_cols_auto_click()
	{
		$("#show_up_cols").trigger('click');			
	}
	
	function hide_up_cols_auto_click()
	{
		$("#hide_up_cols").trigger('click');
	}
	
	function show_up_col(i)
	{		
		i = parseInt(i);
		var j = 0;
		while(i >= 0)
		{
			if($('#elem_show_data_'+i).val() == 1)
			{
				i--;				
				continue;	
			}
			$(".hide_col_"+i).each(function()
			{
				if($(this).hasClass('nHide'))
				{}
				else
				{
					$(this).css({'display':'table-cell'});
				}								
			});
			j = i;			
			i--;					
		}
		
		parent_hide_cols(1);
		$("#bl_coll_uncollapse").css({"display":"none"});
			set_height_col();
		
		if( top.resizeIframe)
			top.resizeIframe('iframe[name="iframeLog"]');	
	}	
	
	function getToolTip(id){
		if(id>=0){
			$('#surg_show_div').html($('#surg_get_div_'+id).html());			
			$('#surg_show_modal').css({"display":"block", "max-height": $(window).height() - 20, "overflow-y":"auto"});			
		}else{
			$('#surg_show_modal').css({"display":"none"});			
		}
		
	}
			
	function highlight_fun(id){
		if(document.getElementById('elem_highlight_data_'+id).value==1){
			document.getElementById('elem_highlight_data_'+id).value=0;
			$('.hide_col_'+id).removeClass('bg3');
		}else{
			document.getElementById('elem_highlight_data_'+id).value=1;
			$('.hide_col_'+id).addClass('bg3');
		}
	}
	
	function set_height_col(){
		$('.headcol').each(function(id,elem){
			var parent_height = $(elem).parent().height();
			$(elem).height(parent_height);
		});
	}
	
	$(document).ready(function(){
		hide_up_cols_auto_click();
		set_height_col();
	});
	
</script>
</head>
<style>
.headcol{
	position: absolute;
	width: 10em;
	left: 0;
	top: auto;
	border-top-width: 1px;
	/*only relevant for first row*/
	margin-top: -1px;
}

.fixed_table_col{
  width: auto;
  overflow-x: scroll;
  margin-left: 10em;
  overflow-y: visible;
  padding: 0;
}

</style>
<body style="background-color:#FFFFFF;overflow:scroll;" class="scrol_Vblue_color" o1nclick="top.clickHandler();">
<div id="surg_show_modal" class="modal fade in" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button class="close" type="button" onClick="$('#surg_show_modal').hide();">&times;</button>
				<h4 class="header-title">Surgery Details</h4>	
			</div>
			<div id="surg_show_div" class="modal-body">
				
			</div>	
			<div class="modal-footer ad_modal_footer" id="module_buttons">
				<button class="btn btn-danger" type="button" onClick="$('#surg_show_modal').hide();">Close</button>
			</div>
		</div>	
	</div>	
</div>
<div class="fixed_table_col">
<table style="width:100%;border-collapse:collapse;" border="0">
<tr>
	<td bgcolor="#FFFFFF">
<!-- Log Past Reading -->	
<?php if($GLOBALS['gl_browser_name']=='ipad'){ ?>
<div style="height:<?php echo $_SESSION['wn_height']-470; ?>px;overflow:scroll;position:relative;width:1385px;">
<?php } ?>
<form name="frmEditLog">
<table class="table table-striped ">
<tr class="valign-top" >
<?php
	function make_military_time($time)
	{
		return date('Hi', strtotime($time));
	}
	if($elem_activate != "-1")
	{
        $arrValOdCd=array();
	$arrValOsCd=array();
	$arrDtCd=array();
	
	$arrValOdTa=array();
	$arrValOsTa=array();
	$arrDtTa=array();
	
	//$arrValOdTp=array();
	//$arrValOsTp=array();
	//$arrDtTp=array();
	
	$arrValOdTx=array();
	$arrValOsTx=array();
	$arrDtTx=array();
	
	$arrValOdCdAll=array();
	$arrValOsCdAll=array();
	$arrValOdTaAll=array();
	$arrValOsTaAll=array();
	$arrValOdTpAll=array();
	$arrValOsTpAll=array();
	$arrValOdTxAll=array();
	$arrValOsTxAll=array();
	$arrDtAll=array();	
	
		$surg_qry=imw_query("select title,begdate,sites from lists where pid='".$glaucoma_obj->pid."' and type='6'");
		while($surg_row=imw_fetch_array($surg_qry)){
			$surg_arr[]=$surg_row;
		}
	
        //Get Glucoma Reading
	    $rez = $glaucoma_obj->getGlaucomaPastReadings($glaucoma_obj->pid);
		$rec_num=imw_num_rows($rez);
	    for($i=0;$row=sqlFetchArray($rez);$i++)
	    {	
			
		$strOcuMed = $row["ocular_med"];
		$logId=$row["id"];
		$logDate=get_date_format($row["dateReading"],'mm-dd-yyyy');
		$logTime=$row["timeReading"];
		$logTaOd=$row["taOd"];
		$logTaOs=$row["taOs"];
		$logTpOd=$row["tpOd"];
		$logTpOs=$row["tpOs"];
		$logTxOd=$row["txOd"];
		$logTxOs=$row["txOs"];
		$logTtOd=$row["ttOd"];
		$logTtOs=$row["ttOs"];
		$log_ta_time=$row["ta_time"];
		$log_tp_time=$row["tp_time"];
		$log_tx_time=$row["tx_time"];
		$log_tt_time=$row["tt_time"];
		
		$logVfOdOrg=$row["vfOdSummary"];		    
		$logVfOd = getMenuValue($logVfOdOrg);
		$logVfOsOrg=$row["vfOsSummary"];
		$logVfOs = getMenuValue($logVfOsOrg);
		$logNfaOdOrg=$row["nfaOdSummary"];
		$logNfaOd = getMenuValue($logNfaOdOrg);
		$logNfaOsOrg=$row["nfaOsSummary"];
		$logNfaOs = getMenuValue($logNfaOsOrg);
		
		$va_od_left_val = '';
		$va_os_left_val = '';
		$log_va_od_summary='';
		$log_va_os_summary='';
		$va_od_summary_val_arr = array();
		$va_os_summary_val_arr = array();
		$iop_od_summary_val_arr=array();
		$iop_os_summary_val_arr=array();
		if($row["va_od_summary"] != "")
		{
			$va_od_summary = explode('/',$row["va_od_summary"]);	
			$va_od_left_val = $va_od_summary[0];
			if(trim($va_od_summary[1]) != "")
			{
				$va_od_summary_val_arr[] = $va_od_summary[1];				
			}
			$log_va_od_summary=$row["va_od_summary"];
		}
		if($row["va_od_summary_2"] != "" && $log_va_od_summary=="")
		{
			$va_od_summary = explode('/',$row["va_od_summary_2"]);	
			$va_od_left_val = $va_od_summary[0];
			if(trim($va_od_summary[1]) != "")
			{
				$va_od_summary_val_arr[] = $va_od_summary[1];				
			}
			$log_va_od_summary=$row["va_od_summary_2"];
		}
		if($row["va_od_summary_3"] != "")
		{
			$va_od_summary = explode('/',$row["va_od_summary_3"]);	
			if(trim($va_od_summary[1]) != "")
			{
				//$va_od_summary_val_arr[] = $va_od_summary[1];				
			}
		}
		
		if($row["va_os_summary"] != "")
		{
			$va_os_summary = explode('/',$row["va_os_summary"]);	
			$va_os_left_val = $va_os_summary[0];
			if(trim($va_os_summary[1]) != "")
			{
				$va_os_summary_val_arr[] = $va_os_summary[1];				
			}
			$log_va_os_summary=$row["va_os_summary"];

		}
		
		if($row["va_os_summary_2"] != "" && $log_va_os_summary=="")
		{
			$va_os_summary = explode('/',$row["va_os_summary_2"]);
			$va_os_left_val = $va_os_summary[0];	
			if(trim($va_os_summary[1]) != "")
			{
				$va_os_summary_val_arr[] = $va_os_summary[1];				
			}
			$log_va_os_summary=$row["va_os_summary_2"];
		}
		
		if($row["va_os_summary_3"] != "")
		{
			$va_os_summary = explode('/',$row["va_os_summary_3"]);	
			if(trim($va_os_summary[1]) != "")
			{
				//$va_os_summary_val_arr[] = $va_os_summary[1];				
			}
		}				
		
   	$logGonioOdOrg=$row["gonioOdSummary"];
		$logGonioOd=(!empty($row["gonioOdSummary"])  && ($row["gonioOdSummary"] != "Not Done") && ($row["gonioOdSummary"] != "Empty")) ? "Done" : "" ; $logGonioOsOrg=$row["gonioOsSummary"];
		$logGonioOs=(!empty($row["gonioOsSummary"])  && ($row["gonioOdSummary"] != "Not Done") && ($row["gonioOsSummary"] != "Empty")) ?  "Done" : "" ; 
		
		$gonio_od_summary=$row["gonio_od_summary"];
		$gonio_os_summary=$row["gonio_os_summary"];

		$sle_od_summary=$row["sle_od_summary"];
		$sle_os_summary=$row["sle_os_summary"];

		$fundus_od_cd_ratio=$row["fundus_od_cd_ratio"];
		$fundus_os_cd_ratio=$row["fundus_os_cd_ratio"];
		
		$fundus_od_summary=$row["fundus_od_summary"];
		$fundus_os_summary=$row["fundus_os_summary"];
		
		$assessment = $glaucoma_obj->replace_spl_chr($row["assessment"]);
		$plan = $glaucoma_obj->replace_spl_chr($row["plan"]);
		$glucoma_med=$row["glucoma_med"];
		$glucoma_med_allergies=$row["glucoma_med_allergies"];

		
		$logPachyOdReads=$row["pachyOdReads"];
		$logPachyOdAvg=$row["pachyOdAvg"];
		$logPachyOdCorr=$row["pachyOdCorr"];
            
		$logPachyOd = (!empty($row["pachyOdReads"])) ? $row["pachyOdReads"]."," : "";
		$logPachyOd .= (!empty($row["pachyOdAvg"])) ? $row["pachyOdAvg"]."," : "";
		$logPachyOd .= (!empty($row["pachyOdCorr"])) ? $row["pachyOdCorr"] : "";								
        	
		$logPachyOsReads=$row["pachyOsReads"];
		$logPachyOsAvg=$row["pachyOsAvg"];
		$logPachyOsCorr=$row["pachyOsCorr"];	    
		    
		$logPachyOs = (!empty($row["pachyOsReads"])) ? $row["pachyOsReads"]."," : "";
		$logPachyOs .= (!empty($row["pachyOsAvg"])) ? $row["pachyOsAvg"]."," : "";
		$logPachyOs .= (!empty($row["pachyOsCorr"])) ? $row["pachyOsCorr"] : ""; 		
		    
		$logDiscOdOrg = $row["diskPhotoOd"];
		$logDiscOsOrg = $row["diskPhotoOs"];
		$logDiscOd = ($row["diskPhotoOd"] == "Done") ? "Done" : "";
		$logDiscOs = ($row["diskPhotoOs"] == "Done") ? "Done" : "";
		
		$logCdOd="";
		$logCdOs="";
		if(!empty($row["cdOd"]) || !empty($row["cdOs"])){
			$logCdOd = $row["cdOd"];
			$logCdOs = $row["cdOs"];
		}else	if(!empty($fundus_od_cd_ratio) || !empty($fundus_os_cd_ratio)){
			$logCdOd = $fundus_od_cd_ratio;
			$logCdOs = $fundus_os_cd_ratio;
		} 
		
		$logTC = $row["treatmentChange"];
		$logMedication = removeUnwantedSigns($row["medication"]);
		$show_data = $row["show_data"];
		$highlight_data = $row["highlight_data"];
		$logCee = $row["cee"];
		if(empty($logDate)){
			continue;
		}
		
		//Purged : Red Line add
		$strPurgCls="";
		$purge_status = $row["purge_status"]; //purge_status
		if($purge_status=="1"){ $strPurgCls = " class=\"purged\" "; }
		
		//Graph Data
		$tmpDtAll="";
		$tmpOdCd = "";
		$tmpOsCd = "";
		$tmpOdTa = "";
		$tmpOsTa = "";
		$tmpOdTp = "";
		$tmpOsTp = "";
		$tmpOdTx = "";
		$tmpOsTx = "";
		$tmpOdTt = "";
		$tmpOsTt = "";
		
		if( (!empty($logCdOd) && is_numeric($logCdOd) ) || (!empty($logCdOs) && is_numeric($logCdOs)) ){ //Cd
			$arrDtCd[] = $logDate;
			$tmpOdCd=(!empty($logCdOd) ) ? $logCdOd : "";
			$tmpOsCd=(!empty($logCdOs) ) ? $logCdOs : ""; 
			
			$arrValOdCd[]=$tmpOdCd;
			$arrValOsCd[]=$tmpOsCd;			
			$tmpDtAll=$logDate;
		}
		
		if( (!empty($logTaOd) && is_numeric($logTaOd) ) || (!empty($logTaOs) && is_numeric($logTaOs) ) ){ // Ta
			$arrDtTa[] = $logDate;
			$tmpOdTa = (!empty($logTaOd)) ? $logTaOd : "";
			$tmpOsTa = (!empty($logTaOs)) ? $logTaOs : "";
			
			$arrValOdTa[]=$tmpOdTa;
			$arrValOsTa[]=$tmpOsTa;
			
			$tmpDtAll=$logDate;
			
		}
		
		if( (!empty($logTpOd) && is_numeric($logTpOd) ) || (!empty($logTpOs) && is_numeric($logTpOs) ) ){
			$arrDtTp[] = $logDate;
			$tmpOdTp = !empty($logTpOd) ? $logTpOd : "" ;
			$tmpOsTp = !empty($logTpOs) ? $logTpOs : "" ;
			
			$arrValOdTp[]=$tmpOdTp;
			$arrValOsTp[]=$tmpOsTp;
			
			$tmpDtAll=$logDate;
		}
		
		if( (!empty($logTxOd) && is_numeric($logTxOd) ) || (!empty($logTxOs) && is_numeric($logTxOs) ) ){
			$arrDtTx[] = $logDate;
			$tmpOdTx = !empty($logTxOd) ? $logTxOd : "" ;
			$tmpOsTx = !empty($logTxOs) ? $logTxOs : "" ;
			
			$arrValOdTx[]=$tmpOdTx;
			$arrValOsTx[]=$tmpOsTx;
			
			$tmpDtAll=$logDate;
		}
		
		if( (!empty($logTtOd) && is_numeric($logTtOd) ) || (!empty($logTtOs) && is_numeric($logTtOs) ) ){
			$arrDtTt[] = $logDate;
			$tmpOdTt = !empty($logTtOd) ? $logTtOd : "" ;
			$tmpOsTt = !empty($logTtOs) ? $logTtOs : "" ;
			
			$arrValOdTt[]=$tmpOdTt;
			$arrValOsTt[]=$tmpOsTt;
			
			$tmpDtAll=$logDate;
		}
		
		if(!empty($tmpDtAll)){
			
			$arrValOdCdAll[]=!empty($tmpOdCd) ? $tmpOdCd : "" ;
			$arrValOsCdAll[]=!empty($tmpOsCd) ? $tmpOsCd : "" ;
			
			$arrValOdTaAll[]=!empty($tmpOdTa) ? $tmpOdTa : "" ;
			$arrValOsTaAll[]=!empty($tmpOsTa) ? $tmpOsTa : "" ;
			
			$arrValOdTpAll[]=!empty($tmpOdTp) ? $tmpOdTp : "" ;
			$arrValOsTpAll[]=!empty($tmpOsTp) ? $tmpOsTp : "" ;
			
			$arrValOdTxAll[]=!empty($tmpOdTx) ? $tmpOdTx : "" ;
			$arrValOsTxAll[]=!empty($tmpOsTx) ? $tmpOsTx : "" ;
			
			$arrValOdTtAll[]=!empty($tmpOdTt) ? $tmpOdTt : "" ;
			$arrValOsTtAll[]=!empty($tmpOsTt) ? $tmpOsTt : "" ;
			
			$arrDtAll[]=$tmpDtAll;
		}
		$test_data_arr=array();
		$test_qry=imw_query("select test_type from glaucoma_past_test where glaucoma_past_id='$logId'");
		while($test_row=imw_fetch_array($test_qry)){
			$test_data_arr[]=$test_row['test_type'];
		}
		
		$test_data_exp=strtoupper(implode(", ",$test_data_arr));
		$gonio_od_summary_data="";
		$gonio_os_summary_data="";
		$sle_od_summary_data="";
		$sle_os_summary_data="";
		$fundus_od_summary_data="";
		$fundus_os_summary_data="";
		$fundus_od_cd_ratio_data="";
		$fundus_os_cd_ratio_data="";
		$log_va_od_summary_data="";
		$log_va_os_summary_data="";
		$log_va_od_summary_col="";
		$log_va_os_summary_col="";
		$log_va_od_summary_col="";
		$log_va_os_summary_col="";
		$log_iop_od_summary_data="";
		$log_iop_os_summary_data="";
		$fundus_od_cd_ratio_line="";
		if($gonio_od_summary!=""){
			$gonio_od_summary_data="<div class='col-sm-12'><font color='blue'>OD: </font>".$gonio_od_summary."</div>";
		}
		if($gonio_os_summary!=""){
			$gonio_os_summary_data="<div class='col-sm-12'><font color='green'>OS: </font>".$gonio_os_summary."</div>";
		}
		if($sle_od_summary!=""){
			$sle_od_summary_data="<div class='col-sm-12'><font color='blue'>OD: </font>".$sle_od_summary."</div>";
		}
		if($sle_os_summary!=""){
			$sle_os_summary_data="<div class='col-sm-12'><font color='Green'>OS: </font>".$sle_os_summary."</div>";
		}
		
		if($fundus_od_cd_ratio!=""){
			$fundus_od_cd_ratio_data="<div class='col-sm-12'><font color='blue'>OD </font><font color='#000000'>CD : </font>".str_replace('C:D','',$fundus_od_cd_ratio)."</div>";
		}
		if($fundus_os_cd_ratio!=""){
			$fundus_os_cd_ratio_data="<div class='col-sm-12'><font color='Green'>OS </font><font color='#000000'>CD : </font>".str_replace('C:D','',$fundus_os_cd_ratio)."</div>";
		}
		if(($fundus_od_cd_ratio!="" || $fundus_os_cd_ratio!="") && ($fundus_od_summary!="" || $fundus_os_summary!="")){
			$fundus_od_cd_ratio_line="<div style='border-bottom:1px solid #CCE6FF; width:100%; padding-top:2px;'></div>";
		}
		if($fundus_od_summary!=""){
			$fundus_od_summary_data="<div class='col-sm-12'><font color='blue'>OD: </font>".$fundus_od_summary."</div>";
		}
		if($fundus_os_summary!=""){
			$fundus_os_summary_data="<div class='col-sm-12'><font color='Green'>OS: </font>".$fundus_os_summary."</div>";
		}
		
		if($log_va_od_summary!=""){
			$log_va_od_summary_col= " <font color='blue'>OD: </font>".$log_va_od_summary;
		}
		$log_va_od_summary_data="<div class='col-sm-12'>".$log_va_od_summary_col."&nbsp;</div>";
		if($log_va_os_summary!=""){
			$log_va_os_summary_col= " <font color='Green'>OS: </font>".$log_va_os_summary;
		}
		$log_va_os_summary_data="<div class='col-sm-12'>".$log_va_os_summary_col."&nbsp;</div>";
		
		//if($logTaOd>0 && $logTaOs>0){
		if($logTaOd>0 || $logTaOs>0){
			$pressure_od=$logTaOd;
			$pressure_os=$logTaOs;
			$iop_od_summary_val_arr['ta']=$logTaOd;
			$iop_os_summary_val_arr['ta']=$logTaOs;
		}else if($logTxOd>0 || $logTxOs>0){ //if($logTxOd>0 && $logTxOs>0){
			$pressure_od=$logTxOd;
			$pressure_os=$logTxOs;
		}else{
			$pressure_od=$logTpOd;
			$pressure_os=$logTpOs;
		}
		//if($logTxOd>0 && $logTxOs>0){
		if($logTxOd>0 || $logTxOs>0){
			$iop_od_summary_val_arr['tx']=$logTxOd;
			$iop_os_summary_val_arr['tx']=$logTxOs;
		}
		//if($logTpOd>0 && $logTpOs>0){
		if($logTpOd>0 || $logTpOs>0){
			$iop_od_summary_val_arr['tp']=$logTpOd;
			$iop_os_summary_val_arr['tp']=$logTpOs;
		}
		if($logTtOd>0 || $logTtOs>0){
			$iop_od_summary_val_arr['tt']=$logTtOd;
			$iop_os_summary_val_arr['tt']=$logTtOs;
		}
		
		
		arsort($iop_od_summary_val_arr);
		arsort($iop_os_summary_val_arr);						
		$log_iop_od_summary=implode(',',$iop_od_summary_val_arr);
		$log_iop_os_summary=implode(',',$iop_os_summary_val_arr);	
		
		if($log_iop_od_summary!="" || $log_iop_os_summary!=""){
			$log_iop_od_summary_col= "<table class='t_p_cl table table-bordered'>
										<tbody>
										<tr>
											<td></td>
											<td>T<sub>A</sub></td>
											<td>T<sub>X</sub></td>
											<td>T<sub>P</sub></td>
											<td>T<sub>T</sub></td>
										</tr>
										<tr>
											<td><font color='blue'>OD</font></td>
											<td>".$iop_od_summary_val_arr['ta']."</td>
											<td>".$iop_od_summary_val_arr['tx']."</td>
											<td>".$iop_od_summary_val_arr['tp']."</td>
											<td>".$iop_od_summary_val_arr['tt']."</td>
										</tr>
										<tr>
											<td><font color='Green'>OS</font></td>
											<td>".$iop_os_summary_val_arr['ta']."</td>
											<td>".$iop_os_summary_val_arr['tx']."</td>
											<td>".$iop_os_summary_val_arr['tp']."</td>
											<td>".$iop_os_summary_val_arr['tt']."</td>
										</tr>
										</tbody>																				
									</table>";
		}else{
			$log_iop_od_summary_col = '';
		}
		$log_iop_od_summary_data="<div class='col-sm-12'>".$log_iop_od_summary_col."&nbsp;</div>";
		
		$log_time_mil_arr=array();
		$log_iop_time_data="";
		if($log_ta_time!=""){
			if(strpos($log_ta_time,'AM')>0){
				$log_ta_time_mil=str_replace(':','',str_replace('AM','',$log_ta_time));
			}else{
				$log_ta_time=str_replace('PM',' PM',$log_ta_time);
				$log_ta_time_mil = make_military_time($log_ta_time);
			}
			//$log_time_mil_arr[]=$log_ta_time_mil;
			$log_time_mil_arr[]=$log_ta_time;
		}
		if($log_tp_time!=""){
			if(strpos($log_tp_time,'AM')>0){
				$log_tp_time_mil=str_replace(':','',str_replace('AM','',$log_tp_time));
			}else{
				$log_tp_time=str_replace('PM',' PM',$log_tp_time);
				$log_tp_time_mil = make_military_time($log_tp_time);
			}
			//$log_time_mil_arr[]=$log_tp_time_mil;
			$log_time_mil_arr[]=$log_tp_time;
		}
		if($log_tx_time!=""){
			if(strpos($log_tx_time,'AM')>0){
				$log_tx_time_mil=str_replace(':','',str_replace('AM','',$log_tx_time));
			}else{
				$log_tx_time=str_replace('PM',' PM',$log_tx_time);
				$log_tx_time_mil = make_military_time($log_tx_time);
			}
			//$log_time_mil_arr[]=$log_tx_time_mil;
			$log_time_mil_arr[]=$log_tx_time;
		}
		if($log_tt_time!=""){
			if(strpos($log_tt_time,'AM')>0){
				$log_tt_time_mil=str_replace(':','',str_replace('AM','',$log_tt_time));
			}else{
				$log_tt_time=str_replace('PM',' PM',$log_tt_time);
				$log_tt_time_mil = make_military_time($log_tt_time);
			}
			//$log_time_mil_arr[]=$log_tx_time_mil;
			$log_time_mil_arr[]=$log_tt_time;
		}
		sort($log_time_mil_arr);
		$log_iop_time_data="<div class='col-sm-12'>".str_replace('PM',' PM',str_replace('AM',' AM',$log_time_mil_arr[0]))."&nbsp;</div>";
		
		$cssStyle="";
		$dateReading_exp=explode('-',$row["dateReading"]);
		$dateReading_ymd=$dateReading_exp[2].'-'.$dateReading_exp[0].'-'.$dateReading_exp[1];
		if(strtotime($dateReading_ymd) == strtotime(date('Y-m-d'))){
			$cssStyle = "color:#390;";
		}
		$row_date[$i][]=$logDate;
		$row_show[$i]=$show_data;
		$highlight_css="";
		if($highlight_data>0){
			$highlight_css=" bg3";
		}
		$row_highlight[$i]=$highlight_css;
		
		
		$row_data[$i][]='
		<td class="hide_col_'.$i.' text_10 nHide'.$highlight_css.'"  onDblClick="highlight_fun('.$i.');">
			<div id="logDateId'.$i.'" class="row date_fixed_header">
				<div class="col-sm-8">
					<span>'.$logDate.'</span>
				</div>
				<div class="col-sm-4">
					<span onClick="hide_col_function('.$i.')" class="pointer">
						<img src="'.$GLOBALS["webroot"].'/library/images/rhtar1.png" align="right" id="arrow_up_'.$i.'">
					</span>
				</div>
			</div>
		</td>';
		
		$row_data[$i][]='
		<td class="hide_col_'.$i.' '.$highlight_css.' nHide" onDblClick="highlight_fun('.$i.');">
			<div id="visualAcuityId'.$i.'" class="row">
				'.$log_va_od_summary_data.'
				'.$log_va_os_summary_data.'
				'.$log_iop_time_data.'
				'.$log_iop_od_summary_data.'
				'.$log_iop_os_summary_data.'
			</div>
		</td>';
						
		$row_data[$i][]='<td class="hide_col_'.$i.' '.$highlight_css.' nHide" onDblClick="highlight_fun('.$i.');">
             <div class="row">
                 <div class="col-sm-12">'.$strOcuMed.'&nbsp;</div>
			</div>   
            </td>';				
		
		$row_data[$i][]='<td class="hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">
				<div class="row">
					<br>
					<img src="graph_image_creator.php?od='.$pressure_od.'&os='.$pressure_os.'" style="height:100%"/>
				</div>
		</td>';
		
		$row_data[$i][]='<td class="text_10 hide_col_'.$i.' '.$highlight_css.'" style="'.$cssStyle.'" onDblClick="highlight_fun('.$i.');">'.$glucoma_med.'</td>';
		
		$row_data[$i][]='<td class="text_10 hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">'.$test_data_exp.'</td>';												
		$row_data[$i][]='<td class="text_10 hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">'.$assessment.'</td>';
		
		$row_data[$i][]='<td class="text_10 hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">'.$plan.'</td>';
		
		$row_data[$i][]='
		<td class="hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">
			<div class="row">
				'.$gonio_od_summary_data.'
				'.$gonio_os_summary_data.'
			</div>
		</td>';
		
		$row_data[$i][]='
		<td class="hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">
			<div class="row">
				'.$fundus_od_cd_ratio_data.''.$fundus_os_cd_ratio_data.''.$fundus_od_cd_ratio_line.''.$fundus_od_summary_data.' '.$fundus_os_summary_data.'
			</div>
		</td>';				
		
		$row_data[$i][]='
		<td class="text_10 commentsNotInc hide_col_'.$i.' '.$highlight_css.'" onDblClick="highlight_fun('.$i.');">
			<textarea id="comments_log_id_'.$i.'" rows="3" name="elem_medication_'.$logId.'" class="form-control">'.$logMedication.'</textarea>
			<input type="text" name="elem_show_data_'.$logId.'" class="form-control" id="elem_show_data_'.$i.'" value="'.$show_data.'" style="display:none;" />
			<input type="text" name="elem_highlight_data_'.$logId.'" id="elem_highlight_data_'.$i.'" value="'.$highlight_data.'" class="form-control" style="display:none;" />
		</td>';
	?>
	<?php
	    //Set Form values
         echo "<div id=\"conDiv_".$logId."\" class=\"frmDiv\">";
         $arrElems = array('elem_id'=>$logId,'elem_date'=>$logDate,'elem_cdOd'=>$logCdOd,'elem_cdOs'=>$logCdOs,
                          'elem_taOd'=>$logTaOd,'elem_taOs'=>$logTaOs,'elem_vfOdSummary'=>$logVfOdOrg,
                          'elem_vfOsSummary'=>$logVfOsOrg,'elem_gonioOd'=>$logGonioOd,'elem_gonioOs'=>$logGonioOs,
                          'elem_time'=>$logTime,'elem_tc'=>$logTC,'elem_medicationRecommend'=>$logMedication,'elem_show_data'=>$show_data,
                          'elem_cee'=>$logCee,'elem_tpOd'=>$logTpOd,'elem_tpOs'=>$logTpOs,'elem_txOd'=>$logTxOd,'elem_txOs'=>$logTxOs,'elem_ttOd'=>$logTtOd,'elem_ttOs'=>$logTtOs,
                          'elem_nfaOdSummary'=>$logNfaOdOrg,'elem_nfaOsSummary'=>$logNfaOsOrg,'elem_diskFundusOd'=>$logDiscOdOrg,
                          'elem_diskFundusOs'=>$logDiscOsOrg);
         foreach($arrElems as $key => $val){
             $nameTmp = $key."_".$logId;   
             echo "<input type=\"hidden\" name=\"".$nameTmp."\" value=\"".$val."\">";
         }                 
         echo "</div>";       
        }
		$field_data[0][]='
		<td class=" loglft headcol"  id="date_iop_col">
			<div class="date_fixed_header_lbl date_fixed_header_lbl2 row">
				<div class="col-sm-6">
					<span>Date :</span>
				</div>
				<div class="col-sm-6">
					<span id="icoGrph_all" class="grphGFS pointer" onClick="top.IOP_showGraphsAm();"><img src="'.$GLOBALS['webroot'].'/library/images/flow_sheet.png"></span>
				</div>
			</div>
		</td>';
		$field_data[0][]='
		<td class=" text-nowrap loglft headcol">
			<div class="date_fixed_header_lbl2">Visual Acuity : </div>
		</td>';
		
		$field_data[0][]='
		<td class=" loglft headcol">
			<div class="date_fixed_header_lbl2">Ocular Meds : </div>
		</td>';
		
		$field_data[0][]='
		<td class="leftColsHide loglft headcol">
			<div class="date_fixed_header_lbl2 row">
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							IOP :
						</div>
						<div class="col-sm-6">
							<a id="hide_up_cols" onclick="hide_up_col('.$i.');" class="pointer pull-left"><span class="glyphicon glyphicon-triangle-top"></span></a> <a class="pointer" id="show_up_cols" onclick="show_up_col('.$i.');set_height_col();"><span class="glyphicon glyphicon-triangle-bottom"></span></a>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 text-right">
							<img src="../../library/images/graph_number.png">
						</div>	
					</div>	
				</div>
			</div>
		</td>';
		
		$field_data[0][]='<td class=" leftColsHide loglft headcol">
			<div class="date_fixed_header_lbl2">Eye Meds : </div>
		</td>';
		
		$field_data[0][]='
		<td class=" leftColsHide loglft headcol">
			<div class="date_fixed_header_lbl2">
				Test : 
			</div>
		</td>';
		
		$field_data[0][]='
		<td class=" leftColsHide loglft headcol">
			<span style="visibility:hidden;">Assessment : </span>
			<div class="date_fixed_header_lbl2">Assessment : </div>
		</td>';
		
		$field_data[0][]='<td class=" leftColsHide loglft headcol"><div  class="date_fixed_header_lbl2">Plan : </div></td>';
		$field_data[0][]='<td class=" leftColsHide loglft headcol">
		<div class="date_fixed_header_lbl2">Gonio : </div></td>';
		$field_data[0][]='<td class=" leftColsHide loglft headcol">
		<div >Disc : </div></td>';
		$field_data[0][]='<td class=" loglft headcol">
			<div class="row text-nowrap">
				<div class="col-sm-12">
					Comment :
				</div>
				<div id="bl_coll_uncollapse" class="col-sm-12 text-right" style="display:none;">
					<a style="cursor:pointer;" onclick="hide_up_col('.$i.');"><span class="glyphicon glyphicon-triangle-top"></span></a> <a style="cursor:pointer;" id="show_up_cols" onclick="show_up_col('.$i.');"><span class="glyphicon glyphicon-triangle-bottom"></span></a>	
				</div>	
			</div>
		</td>';
	
		$add_row_data[0][]='<td id="add_row_0" style="display:none;"><input class="form-control" type="text" name="elem_date" id="elem_date" value="" onBlur="checkdate(this)"></td>';
		$add_row_data[0][]='<td id="add_row_1" style="vertical-align:top; display:none;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr><td class="text_10"><font color="blue">OD : </font></td>
										<td><input size="15" class="form-control" type="text" name="elem_va_od_summary" id="elem_va_od_summary"  value=""></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td class="text_10"><font color="green">OS : </font></td>
										<td><input size="15" class="form-control" type="text" name="elem_va_os_summary" id="elem_va_os_summary"  value=""></td>
									</tr>
								</table>
							</td>';
		
		$add_row_data[0][]='<td id="add_row_4" style="display:none;"><input class="form-control" type="text" name="elem_ocular_med" id="elem_ocular_med"  value=""></td>';					
		
		$add_row_data[0][]='<td id="add_row_2" style="vertical-align:top; display:none;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr><td></td><td class="text_10">TA</td><td class="text_10">TP</td><td class="text_10">TX</td></tr>
									<tr><td class="text_10"><font color="blue">OD : </font></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_ta_od_summary" id="elem_ta_od_summary"  value=""></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_tp_od_summary" id="elem_tp_od_summary"  value=""></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_tx_od_summary" id="elem_tx_od_summary"  value=""></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_tt_od_summary" id="elem_tt_od_summary"  value=""></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td class="text_10"><font color="Green">OS : </font></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_ta_os_summary" id="elem_ta_os_summary"  value=""></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_tp_os_summary" id="elem_tp_os_summary"  value=""></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_tx_os_summary" id="elem_tx_os_summary"  value=""></td>
										<td><input size="2" class="input_text_10" type="text" name="elem_tt_os_summary" id="elem_tt_os_summary"  value=""></td>
									</tr>
								</table>
							</td>';
		
		$add_row_data[0][]='<td id="add_row_3" style="display:none;"><input class="form-control" type="text" name="elem_glucoma_med" id="elem_glucoma_med"  value=""></td>';					
		$add_row_data[0][]='<td id="add_row_8" style="display:none;" ><input class="form-control" type="text" name="elem_test_data" id="elem_test_data"  value=""></td>';												
		
		$add_row_data[0][]='<td id="add_row_9" style="display:none;" ><input class="form-control" type="text" name="elem_assessment" id="elem_assessment"  value=""></td>';
		$add_row_data[0][]='<td id="add_row_10" style="display:none;" ><input class="form-control" type="text" name="elem_plan" id="elem_plan"  value=""></td>';
		$add_row_data[0][]='<td id="add_row_5" style="vertical-align:top; display:none;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr><td class="text_10"><font color="blue">OD : </font></td>
										<td><input size="15" class="input_text_10" type="text" name="elem_gonio_od_summary" id="elem_gonio_od_summary"  value=""></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td class="text_10"><font color="green">OS : </font></td>
										<td><input size="15" class="input_text_10" type="text" name="elem_gonio_os_summary" id="elem_gonio_os_summary"  value=""></td>
									</tr>
								</table>
							</td>';
		$add_row_data[0][]='<td id="add_row_7" style="vertical-align:top; display:none;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr><td class="text_10"  style="white-space:nowrap;"><font color="blue">OD </font><font color="#000000">CD : </font></td>
										<td><input size="13" class="input_text_10" type="text" name="elem_fundus_od_cd_ratio" id="elem_fundus_od_cd_ratio"  value=""></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td class="text_10"  style="white-space:nowrap;"><font color="green">OS </font><font color="#000000">CD : </font></td>
										<td><input size="13" class="input_text_10" type="text" name="elem_fundus_os_cd_ratio" id="elem_fundus_os_cd_ratio"  value=""></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td class="text_10"><font color="blue">OD : </font></td>
										<td><input size="13" class="input_text_10" type="text" name="elem_fundus_od_summary" id="elem_fundus_od_summary"  value=""></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td class="text_10"><font color="green">OS : </font></td>
										<td><input size="13" class="input_text_10" type="text" name="elem_fundus_os_summary" id="elem_fundus_os_summary"  value=""></td>
									</tr>
								</table>
							</td>';					
		$add_row_data[0][]='<td id="add_row_11" style="display:none;" ><textarea class="form-control" cols="21" rows="3" name="elem_medication" id="elem_medication"></textarea></td>';
?>
<td valign="top" style="border-top-width:0px;" >
     <table class="table table-striped table-bordered">
	 <?php	
		if($rec_num>0){	
			for($f=0;$f<11;$f++){
				echo "<tr>";
				echo $field_data[0][$f];
				echo $add_row_data[0][$f];
				$skip_sur_data=array();
				for($k=0;$k<$rec_num;$k++){
					echo $row_data[$k][$f];
					
					$sur_data="";
					$sur_div="<div style='display:none;' id='surg_get_div_".$k."'><table class='table table-bordered table-striped'><tr class='grythead'><td class='text_10b' width='250px' nowrap>Sx Name</td><td class='text_10b'>Site</td><td class='text_10b' nowrap>Date</td></tr>";
					if($f==0){
						echo '<td class="td_30w text_10 parent_hide_col'.$row_highlight[$k].'" rowspan="11"><div class="row"><span onClick="hide_col_function('.$k.')">
						<img src="../../library/images/rhtarrow.png" style="border:none; display:none;" class="pointer" id="arrow_dn_'.$k.'"  class="date_fixed_header_open"></span></div></td>';
						$chart_date_arr=explode('-',$row_date[$k][$f]);
						$chart_date_exp=$chart_date_arr[2].'-'.$chart_date_arr[0].'-'.$chart_date_arr[1];
						$chart_date_arr2=explode('-',$row_date[$k+1][$f]);
						$chart_date_exp2=$chart_date_arr2[2].'-'.$chart_date_arr2[0].'-'.$chart_date_arr2[1];
						for($j=0;$j<count($surg_arr);$j++){
							if($surg_arr[$j]['begdate']!="0000-00-00" && !in_array($j,$skip_sur_data)){
								if($surg_arr[$j]['begdate']>$chart_date_exp2 && $surg_arr[$j]['begdate']<=$chart_date_exp){
									$begdate_arr=explode('-',$surg_arr[$j]['begdate']);
									$begdate_exp=$begdate_arr[1].'-'.$begdate_arr[2].'-'.substr($begdate_arr[0],2);
									$begdate_exp = get_date_format($surg_arr[$j]['begdate'],'','',2);
									if($surg_arr[$j]['sites']==1){
										//OS
										$surg_site="OS";
									}
									if($surg_arr[$j]['sites']==2){
										//OD
										$surg_site="OD";
									}
									if($surg_arr[$j]['sites']==3){
										//OU
										$surg_site="OU";
									}
									if($surg_arr[$j]['sites']==4){
										//PO
										$surg_site="PO";
									}
									$sur_data.="<tr><td class='text_10'>".$surg_arr[$j]['title']."</td><td class='text_10'>".$surg_site."</td><td class='text_10' nowrap>".$begdate_exp."</td></tr>";
									$skip_sur_data[]=$j;
								}
							}
							
						}
						if($sur_data!=""){
							$final_sur_div="";
							$final_sur_div=$sur_div.$sur_data."</table></div>";
							
							echo '<td class="td_30w text_10" style="cursor:pointer;vertical-align:top; padding:0px; background:url(../../library/images/surgery_img.png) repeat-y;" rowspan="11" onmouseover="getToolTip('.$k.');">&nbsp;&nbsp;&nbsp;&nbsp;'.$final_sur_div.'</td>';
						}
					}
				} 
				echo "</tr>";
			}	
		}else{
			for($f=0;$f<11;$f++){
				echo "<tr bgcolor='#ffffff'>";
				echo $field_data[0][$f];
				echo $add_row_data[0][$f];
				echo "</tr>";
			}	
		}
		for($t=0;$t<$rec_num;$t++){
			if($row_show[$t]>0){
				echo "<script type='text/javascript'>hide_col_function(".$t.")</script>";
			}
		}
	?>
     </table>
 </td> 
	
<?php		
	}
$totRows = $i-1;
 
    ?>
</tr></table>
</form>
<?php if($GLOBALS['gl_browser_name']=='ipad'){ ?>
</div>
<?php } ?>
<!-- Log Past Reading -->
	</td>
</tr>
<tr>
	<td>
<!-- Set Activate -->
<form name="frmActivate" action="" method="post">
   <input type="hidden" name="elem_activate" value="<?php echo $elem_activate;?>">
</form>
<!-- Set Activate -->
<!-- Graphs -->
<?php
$flagAll = 0;
if( count($arrDtCd) > 1 ){
	$arrUni1 = array_unique(array_merge($arrValOdCd,$arrValOsCd));	
	if(count($arrUni1)<= 2){
		if((count($arrUni1) == 1)){
			$arrValOdCd[] = "0";
			$arrValOsCd[] = "0";
			$arrDtCd[] = date("m-d-Y");			
		}else if((($arrUni1[0] == "") || ($arrUni1[1] == ""))){
			$arrValOdCd = $glaucoma_obj->arr_replace("","0",$arrValOdCd);
			$arrValOsCd = $glaucoma_obj->arr_replace("","0",$arrValOsCd);
		}
	}
	
	$series = array(array_reverse($arrValOdCd),array_reverse($arrValOsCd),array_reverse($arrDtCd));	
	$seriesName = array("OD","OS");
	
	$axisName = array("Date", "CD");
	$graphTitle = "CD Values";
	$imgId="imgGrph_cd";
	$seriesColor = array(array("0","0","255"), array("35","142","35"));
	echo getGlucomaGraph($series, $seriesName, $axisName, $graphTitle,$imgId,$seriesColor);
	echo "<script>top.document.getElementById('icoGrph_cd').style.display='block';</script>";
	$flagAll += 1;
}

if( count($arrDtTa) > 1 ){
	$arrUni1 = array_unique(array_merge($arrValOdTa,$arrValOsTa));
	if(count($arrUni1)<= 2){
		if((count($arrUni1) == 1)){
			$arrValOdTa[] = "0";
			$arrValOsTa[] = "0";
			$arrDtTa[] = date('m-d-Y');
			
		}else if((($arrUni1[0] == "") || ($arrUni1[1] == ""))){
			$arrValOdTa = $glaucoma_obj->arr_replace("","0",$arrValOdTa);
			$arrValOsTa = $glaucoma_obj->arr_replace("","0",$arrValOsTa);
		}
	}	
	
	$series = array(array_reverse($arrValOdTa),array_reverse($arrValOsTa),array_reverse($arrDtTa));
	$seriesName = array("OD","OS");
	
	$axisName = array("Date", "TA");
	$graphTitle = "TA Values";
	$imgId="imgGrph_ta";
	$seriesColor = array(array("0","0","255"), array("35","142","35"));
	echo getGlucomaGraph($series, $seriesName, $axisName, $graphTitle,$imgId,$seriesColor);	
	echo "<script>top.document.getElementById('icoGrph_ta').style.display='block';</script>";
	$flagAll += 1;
}

if( count($arrDtTx) > 1 ){
	
	$arrUni1 = array_unique(array_merge($arrValOdTx,$arrValOsTx));
	if(count($arrUni1)<= 2){
		if((count($arrUni1) == 1)){
			$arrValOdTx[] = "0";
			$arrValOsTx[] = "0";
			$arrDtTx[] = date('m-d-Y');
			
		}else if((($arrUni1[0] == "") || ($arrUni1[1] == ""))){
			$arrValOdTx = $glaucoma_obj->arr_replace("","0",$arrValOdTx);
			$arrValOsTx = $glaucoma_obj->arr_replace("","0",$arrValOsTx);
		}
	}
	
	$series = array(array_reverse($arrValOdTx),array_reverse($arrValOsTx),array_reverse($arrDtTx));
	$seriesName = array("OD","OS");
	
	$axisName = array("Date", "TX");
	$graphTitle = "TX Values";
	$imgId="imgGrph_tx";
	$seriesColor = array(array("0","0","255"), array("35","142","35"));
	echo getGlucomaGraph($series, $seriesName, $axisName, $graphTitle,$imgId,$seriesColor);
	echo "<script>top.document.getElementById('icoGrph_tx').style.display='block';</script>";
	$flagAll += 1;
}

if( $flagAll >= 1 ){
	$arr_valOd_cd=array_reverse($arrValOdCdAll);
	$arr_valOs_cd=array_reverse($arrValOsCdAll);
	
	$arr_valOd_ta=array_reverse($arrValOdTaAll);
	$arr_valOs_ta=array_reverse($arrValOsTaAll);
	
	$arr_valOd_tp=array_reverse($arrValOdTpAll);
	$arr_valOs_tp=array_reverse($arrValOsTpAll);	
	
	$arr_valOd_tx=array_reverse($arrValOdTxAll);
	$arr_valOs_tx=array_reverse($arrValOsTxAll);	
	
	$arr_valOd_tt=array_reverse($arrValOdTtAll);
	$arr_valOs_tt=array_reverse($arrValOsTtAll);	
	
	$arr_dtAll = array_reverse($arrDtAll);
	
	//TEST
	$arr_valOd_cd_t = array_unique($arr_valOd_cd);
	if((count($arr_valOd_cd_t) == 1) && empty($arr_valOd_cd_t[0])){
		$arr_valOd_cd = array();
	}
	$arr_valOs_cd_t = array_unique($arr_valOs_cd);
	if((count($arr_valOs_cd_t) == 1) && empty($arr_valOs_cd_t[0])){
		$arr_valOd_cs = array();
	}
	$arr_valOd_ta_t = array_unique($arr_valOd_ta);
	if((count($arr_valOd_ta_t) == 1) && empty($arr_valOd_ta_t[0])){
		$arr_valOd_ta = array();
	}
	$arr_valOs_ta_t = array_unique($arr_valOs_ta);
	if((count($arr_valOs_ta_t) == 1) && empty($arr_valOs_ta_t[0])){
		$arr_valOs_ta = array();
	}
	
	$arr_valOd_tp_t = array_unique($arr_valOd_tp);
	if((count($arr_valOd_tp_t) == 1) && empty($arr_valOd_tp_t[0])){
		$arr_valOd_tp = array();
	}
	$arr_valOs_tp_t = array_unique($arr_valOs_tp);
	if((count($arr_valOs_tp_t) == 1) && empty($arr_valOs_tp_t[0])){
		$arr_valOs_tp = array();
	}
	
	
	$arr_valOd_tx_t = array_unique($arr_valOd_tx);
	if((count($arr_valOd_tx_t) == 1) && empty($arr_valOd_tx_t[0])){
		$arr_valOd_tx = array();
	}
	$arr_valOs_tx_t = array_unique($arr_valOs_tx);
	if((count($arr_valOs_tx_t) == 1) && empty($arr_valOs_tx_t[0])){
		$arr_valOs_tx = array();
	}
	
	$arr_valOd_tt_t = array_unique($arr_valOd_tt);
	if((count($arr_valOd_tt_t) == 1) && empty($arr_valOd_tt_t[0])){
		$arr_valOd_tt = array();
	}
	$arr_valOs_tt_t = array_unique($arr_valOs_tt);
	if((count($arr_valOs_tt_t) == 1) && empty($arr_valOs_tt_t[0])){
		$arr_valOs_tt = array();
	}
	
	//TEST
	
	$arrUni1 = array_unique(array_merge($arr_valOd_ta,$arr_valOs_ta,$arr_valOd_tp,$arr_valOs_tp,
				$arr_valOd_tx,$arr_valOd_tx,$arr_valOd_tt,$arr_valOd_tt));
	if(count($arrUni1)<= 2){
		if((count($arrUni1) == 1)){
			$arr_valOd_cd[] = "0";
			$arr_valOs_cd[] = "0";
			$arr_valOd_ta[] = "0";
			$arr_valOs_ta[] = "0";
			$arr_valOd_tp[] = "0";
			$arr_valOs_tp[] = "0";
			$arr_valOd_tx[] = "0";
			$arr_valOs_tx[] = "0";
			$arr_valOd_tt[] = "0";
			$arr_valOs_tt[] = "0";
			$arr_dtAll[] = date('m-d-Y');
		
		}else if((($arrUni1[0] == "") || ($arrUni1[1] == ""))){
			
			$arr_valOd_cd= $glaucoma_obj->arr_replace("","0",$arr_valOd_cd);
			$arr_valOs_cd= $glaucoma_obj->arr_replace("","0",$arr_valOs_cd);
			$arr_valOd_ta= $glaucoma_obj->arr_replace("","0",$arr_valOd_ta);
			$arr_valOs_ta= $glaucoma_obj->arr_replace("","0",$arr_valOs_ta);
			$arr_valOd_tp= $glaucoma_obj->arr_replace("","0",$arr_valOd_tp);
			$arr_valOs_tp= $glaucoma_obj->arr_replace("","0",$arr_valOs_tp);
			$arr_valOd_tx= $glaucoma_obj->arr_replace("","0",$arr_valOd_tx);
			$arr_valOs_tx= $glaucoma_obj->arr_replace("","0",$arr_valOs_tx);
			$arr_valOd_tt= $glaucoma_obj->arr_replace("","0",$arr_valOd_tt);
			$arr_valOs_tt= $glaucoma_obj->arr_replace("","0",$arr_valOs_tt);
		}
	}
	
	$nm_taOd= "TA OD";
	$nm_taOs= "TA OS";
	$nm_tpOd= "TP OD";
	$nm_tpOs= "TP OS";
	$nm_txOd= "TX OD";
	$nm_txOs= "TX OS";
	$nm_txOd= "TT OD";
	$nm_txOs= "TT OS";
	
	$seriesAll = array($arr_valOd_ta,$arr_valOs_ta,$arr_valOd_tp,$arr_valOs_tp,
				$arr_valOd_tx,$arr_valOs_tx, $arr_valOd_tt,$arr_valOs_tt,
				$arr_dtAll);
	//$nm_cdOd, $nm_cdOs,			
	$seriesName = array($nm_taOd, $nm_taOs,$nm_tpOd,$nm_tpOs,
					$nm_txOd, $nm_txOs, $nm_ttOd, $nm_ttOs);
					
	$axisName = array("Date", "CD & IOP");
	$graphTitle = "CD and IOP Values";
	$imgId="imgGrph_all";
	
	echo "<script>document.getElementById('icoGrph_all').style.display='block';</script>";
	
	//Hidden		
	$elem_grph_cdod = (count($arr_valOd_cd) > 0 ) ? base64_encode(serialize($arr_valOd_cd)) : "";
	$elem_grph_cdos = (count($arr_valOs_cd) > 0 ) ? base64_encode(serialize($arr_valOs_cd)) : "";
	$elem_grph_taod = (count($arr_valOd_ta) > 0 ) ? base64_encode(serialize($arr_valOd_ta)) : "";
	$elem_grph_taos = (count($arr_valOs_ta) > 0 ) ? base64_encode(serialize($arr_valOs_ta)) : "";
	$elem_grph_tpod = (count($arr_valOd_tp) > 0 ) ? base64_encode(serialize($arr_valOd_tp)) : "";
	$elem_grph_tpos = (count($arr_valOs_tp) > 0 ) ? base64_encode(serialize($arr_valOs_tp)) : "";
	$elem_grph_txod = (count($arr_valOd_tx) > 0 ) ? base64_encode(serialize($arr_valOd_tx)) : "";
	$elem_grph_txos = (count($arr_valOs_tx) > 0 ) ? base64_encode(serialize($arr_valOs_tx)) : "";
	$elem_grph_ttod = (count($arr_valOd_tt) > 0 ) ? base64_encode(serialize($arr_valOd_tt)) : "";
	$elem_grph_ttos = (count($arr_valOs_tt) > 0 ) ? base64_encode(serialize($arr_valOs_tt)) : "";
	
	echo "\n<input type=\"hidden\" name=\"elem_grph_cdod\" id=\"elem_grph_cdod\" value=\"".$elem_grph_cdod."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_cdos\" id=\"elem_grph_cdos\" value=\"".$elem_grph_cdos."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_taod\" id=\"elem_grph_taod\" value=\"".$elem_grph_taod."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_taos\" id=\"elem_grph_taos\" value=\"".$elem_grph_taos."\">";
	echo "\n<input type=\"hidden\" name=\"elem_grph_txod\" id=\"elem_grph_txod\"  value=\"".$elem_grph_txod."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_txos\" id=\"elem_grph_txos\" value=\"".$elem_grph_txos."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_ttod\" id=\"elem_grph_ttod\"  value=\"".$elem_grph_ttod."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_ttos\" id=\"elem_grph_ttos\" value=\"".$elem_grph_ttos."\">";	
	echo "\n<input type=\"hidden\" name=\"elem_grph_dtAll\" id=\"elem_grph_dtAll\" value=\"".base64_encode(serialize($arr_dtAll))."\">";
}else{
}
function line_chart($graph_name,$graph_data){
	$key_i=0;$kk=0;
	foreach($graph_data[6] as $key=>$val){
		if($graph_data[0][$key]>0 || $graph_data[1][$key]>0 || $graph_data[2][$key]>0 || $graph_data[3][$key]>0 || $graph_data[4][$key]>0 || $graph_data[5][$key]>0){
			$line_payment_tot_arr[$key]["category"]=$val;
		}else{
			unset($graph_data[0][$key]);
			unset($graph_data[1][$key]);
			unset($graph_data[2][$key]);
			unset($graph_data[3][$key]);
			unset($graph_data[4][$key]);
			unset($graph_data[5][$key]);
		}
	}

	foreach($graph_data as $key=>$val){
		if($key!=6){	
			$key_i++;
			$title="";
			$title=$graph_name[$key];
			$line_pay_graph_var_arr[]=array("alphaField"=> "C",
				"balloonText"=> "[[title]] of [[category]]: [[value]]",
				"bullet"=> "round",
				"bulletField"=> "C",
				"bulletSizeField"=> "C",
				"closeField"=> "C",
				"colorField"=> "C",
				"customBulletField"=> "C",
				"dashLengthField"=> "C",
				"descriptionField"=> "C",
				"errorField"=> "C",
				"fillColorsField"=> "C",
				"gapField"=> "C",
				"highField"=> "C",
				"id"=> "AmGraph-$key_i",
				"labelColorField"=> "C",
				"lineColorField"=> "C",
				"lowField"=> "C",
				"openField"=> "C",
				"patternField"=> "C",
				"title"=> $title,
				"valueField"=> "column-$key_i",
				"xField"=> "C",
				"yField"=> "C");
			
			foreach($graph_data[$key] as $key2=>$val2){	
				$line_payment_tot_arr[$key2]["column-".$key_i]=$graph_data[$key][$key2];
				$kk++;
			}
		}
	}
	$return_arr['line_payment_tot_detail']=$line_payment_tot_arr;
	$return_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr;
	return $return_arr;
}
?>
<!-- Graphs -->

	</td>
</tr>
</table>
</div>	
<script type="text/javascript">
customarrayTitle = '<?php echo $_SESSION['strTypeHead'];?>';
customarrayTitle = customarrayTitle.split(',');
$('[id^=elem_medication]').each(function(id,elem){
	$(elem).typeahead({source:customarrayTitle});
});
</script>
</body>
</html>

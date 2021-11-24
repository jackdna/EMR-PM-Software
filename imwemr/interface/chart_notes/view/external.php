<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvexmcss" rel="stylesheet">

<style>
	
</style>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
<script>

var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var elem_per_vo = "<?php echo $elem_per_vo;?>";
var sess_pt = "<?php echo $patient_id; ?>";
var rootdir = "<?php echo $rootdir;?>";
var finalize_flag = "<?php echo $finalize_flag;?>";
var isReviewable = "<?php echo $isReviewable;?>";
var logged_user_type = "<?php echo $logged_user_type;?>";
var examName = "External";
var arrSubExams = new Array("Ee","Draw");
var ProClr=<?php echo $ProClr;?>;
var drawCntlNum=<?php echo $drawCntlNum; ?>;
var blEnableHTMLDrawing="<?php echo $blEnableHTMLDrawing;?>";
var def_pg='<?php echo $_GET["pg"];?>';

</script>

</head>
<body class="exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->
<form name="frmExternal" id="frmExternal" action="saveCharts.php" method="post" onSubmit="freezeElemAll('0')" enctype="multipart/form-data" class="frcb">
<input type="hidden" name="elem_saveForm" value="External Exam">
<input type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
<input type="hidden" name="elem_eeId" value="<?php echo $elem_eeId;?>">
<input type="hidden" name="elem_eeId_LF" value="<?php echo $elem_eeId_LF;?>">
<input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
<input type="hidden" name="elem_patientId" value="<?php echo $elem_patientId;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
<input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
<!--<input type="hidden" name="elem_notApplicable" value="<?php echo $elem_notApplicable;?>">--><!-- Not Required -->
<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">

<input type="hidden" name="elem_wnlEeOd" value="<?php echo $elem_wnlEeOd;?>">
<input type="hidden" name="elem_wnlEeOs" value="<?php echo $elem_wnlEeOs;?>">
<input type="hidden" name="elem_descExternal" value="<?php echo $elem_descExternal;?>">

<input type="hidden" name="elem_wnlEe" value="<?php echo $elem_wnlEe;?>">
<input type="hidden" name="elem_wnlDraw" value="<?php echo $elem_wnlDraw;?>">
<input type="hidden" name="elem_posEe" value="<?php echo $elem_posEe;?>">
<input type="hidden" name="elem_posDraw" value="<?php echo $elem_posDraw;?>">
<input type="hidden" name="elem_ncEe" value="<?php echo $elem_ncEe;?>">
<input type="hidden" name="elem_ncDraw" value="<?php echo $elem_ncDraw;?>">
<input type="hidden" name="elem_wnlDrawOd" value="<?php echo $elem_wnlDrawOd;?>">
<input type="hidden" name="elem_wnlDrawOs" value="<?php echo $elem_wnlDrawOs;?>">

<input type="hidden" name="elem_examined_no_change" value="<?php echo $elem_noChange;?>">
<input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
<input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">

<!-- newET_changeIndctr -->
<input type="hidden" name="elem_chng_divCon_Od" id="elem_chng_divCon_Od" value="<?php echo $elem_chng_divCon_Od;?>">
<input type="hidden" name="elem_chng_divCon_Os" id="elem_chng_divCon_Os" value="<?php echo $elem_chng_divCon_Os;?>">
<input type="hidden" name="elem_chng_divDraw_Od" id="elem_chng_divDraw_Od" value="<?php echo $elem_chng_divDraw_Od;?>">
<input type="hidden" name="elem_chng_divDraw_Os" id="elem_chng_divDraw_Os" value="<?php echo $elem_chng_divDraw_Os;?>">
<!-- newET_changeIndctr -->

<input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
<!--<input type="hidden" name="hidExternalDrawingId" id="hidExternalDrawingId" value="<?php //echo $dbIdocDrawingId;?>">-->
<input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">

<div class=" container-fluid">
<div class="whtbox exammain ">

<div class="clearfix"></div>
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">    
	<?php	
	foreach($arrTabs as $key => $val){
	$tmp2=$key;
	if($tmp2=="Con"){	$tmp2="Ee";	}
	$tmp = ($key == "Con") ? "active" : "";
	?>
	<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab"  id="tab<?php echo $key;?>" onclick="changeTab('<?php echo $key;?>')" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="divCon">
	<div class="examhd">
		<?php if($finalize_flag == 1){?>
		<label class="chart_status label label-danger pull-left">Finalized</label>
		<?php }?>
		
		<span id="examFlag" class="glyphicon flagWnl "></span>
		
		<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>
		
		<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();" 
			<?php echo ($elem_ncEe == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
		<label class="lbl_nochange frcb" for="elem_noChange">NO Change</label>

		<?php /*if (constant('AV_MODULE')=='YES'){?>
		<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
		<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
		<?php }*/?>		
	</div>    
	<div class="clearfix"> </div>    
	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="6" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
			
		</td>
		<td width="100" align="center" class="bilat bilat_all" onclick="check_bilateral()"><strong>Bilateral</strong></td>
		<td colspan="6" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>   
		</tr>
		
		<tr id="d_preaur">
		<td align="left">Pre-auricular </td>
		<td colspan="2">
		<input type="checkbox" onClick="checkAbsent(this);" id="od_1"  name="elem_preAuriOd_lymphNode" value="Lymph Node" <?php echo ($elem_preAuriOd_lymphNode == "Lymph Node") ? "checked=\"checked\"" : "" ;?>><label for="od_1" class="lym_node">Lymph Node</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_2" name="elem_preAuriOd_neg" value="Absent" <?php echo ($elem_preAuriOd_neg == "-ve" || $elem_preAuriOd_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_2" class="pre_neg">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_3" name="elem_preAuriOd_pos" value="Present" <?php echo ($elem_preAuriOd_pos == "+ve" || $elem_preAuriOd_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_3">Present</label>
		</td>
		<td align="center">&nbsp;</td>
		<td align="center" class="bilat" onclick="check_bl('preaur')">BL</td>
		<td align="left">Pre-auricular </td>
		<td colspan="2">
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_1" name="elem_preAuriOs_lymphNode" value="Lymph Node" <?php echo ($elem_preAuriOs_lymphNode == "Lymph Node") ? "checked=\"checked\"" : "" ;?>><label for="os_1" class="lym_node">Lymph Node</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_2" name="elem_preAuriOs_neg" value="Absent" <?php echo ($elem_preAuriOs_neg == "-ve"||$elem_preAuriOs_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_2" class="pre_neg">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_3" name="elem_preAuriOs_pos" value="Present" <?php echo ($elem_preAuriOs_pos == "+ve" || $elem_preAuriOs_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_3">Present</label>
		</td>
		<td align="center">&nbsp;</td>
		</tr>
		
		<tr id="d_Dacry">
		<td >Dacryocystitis</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_5"  name="elem_dacryOd_neg" value="Absent" <?php echo ($elem_dacryOd_neg == "-ve" || $elem_dacryOd_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_5">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_6"  name="elem_dacryOd_pos" value="Present" <?php echo ($elem_dacryOd_pos == "+ve" || $elem_dacryOd_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_6">Present</label>
		</td>
		<td >&nbsp;</td>
		<td colspan="2" >&nbsp;</td>
		<td align="center" class="bilat" onclick="check_bl('Dacry')">BL</td>
		<td >Dacryocystitis</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_5"  name="elem_dacryOs_neg" value="Absent" <?php echo ($elem_dacryOs_neg == "-ve" || $elem_dacryOs_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_5">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_6" name="elem_dacryOs_pos" value="Present" <?php echo ($elem_dacryOs_pos == "+ve" || $elem_dacryOs_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_6">Present</label>
		</td>
		<td >&nbsp;</td>
		<td colspan="2" >&nbsp;</td>
		</tr>		
		
		<tr id="d_HDerma">
		<td >HZV Dermatitis </td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_7"  name="elem_hzvDerOd_v1" value="V1" <?php echo ($elem_hzvDerOd_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="od_7">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_8" name="elem_hzvDerOd_v2" value="V2" <?php echo ($elem_hzvDerOd_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="od_8">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_9" name="elem_hzvDerOd_v3" value="V3" <?php echo ($elem_hzvDerOd_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="od_9">V3</label>
		</td>
		<td colspan="2" >
		<input type="text" onBlur="checkwnls();" id="od_10" name="elem_hzvDerOd_text" value="<?php echo ($elem_hzvDerOd_text);?>" class="form-control" placeholder="Text input">
		</td>
		<td align="center" class="bilat" onclick="check_bl('HDerma')">BL</td>
		<td >HZV Dermatitis </td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_7" name="elem_hzvDerOs_v1" value="V1" <?php echo ($elem_hzvDerOs_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="os_7">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_8"  name="elem_hzvDerOs_v2" value="V2" <?php echo ($elem_hzvDerOs_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="os_8">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_9"  name="elem_hzvDerOs_v3" value="V3" <?php echo ($elem_hzvDerOs_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="os_9">V3</label>
		</td>
		<td colspan="2" >
		<input type="text" id="os_10" onBlur="checkwnls();"  name="elem_hzvDerOs_text" value="<?php echo ($elem_hzvDerOs_text);?>" class="form-control" placeholder="Text input" >		
		</td>
		</tr>
		
		
		<tr id="d_Prop">
		<td >Proptosis</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="elem_proptosisOd_neg" name="elem_proptosisOd_neg" value="Absent" <?php echo ($elem_proptosisOd_neg == "-ve" || $elem_proptosisOd_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_proptosisOd_neg">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_11"  name="elem_proptosisOd_mild" value="Mild" <?php echo ($elem_proptosisOd_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="od_11">Mild</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_12" name="elem_proptosisOd_moderate" value="Moderate" <?php echo ($elem_proptosisOd_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="od_12">Moderate</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="od_13" name="elem_proptosisOd_severe" value="Severe" <?php echo ($elem_proptosisOd_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="od_13">Severe</label>
		</td>
		<td class="form-inline">
		<input type="text" onBlur="checkAbsent(this);" id="od_14" name="elem_proptosisOd_text" value="<?php echo ($elem_proptosisOd_text);?>" class="form-control" placeholder="Text input" >
		</td>		
		<td align="center" class="bilat" onclick="check_bl('Prop')">BL</td>
		<td >Proptosis</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="elem_proptosisOs_neg" name="elem_proptosisOs_neg" value="Absent" <?php echo ($elem_proptosisOs_neg == "-ve" || $elem_proptosisOs_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_proptosisOs_neg">Absent</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_11" name="elem_proptosisOs_mild" value="Mild" <?php echo ($elem_proptosisOs_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="os_11">Mild</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_12" name="elem_proptosisOs_moderate" value="Moderate" <?php echo ($elem_proptosisOs_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="os_12">Moderate</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this);" id="os_13" name="elem_proptosisOs_severe" value="Severe" <?php echo ($elem_proptosisOs_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="os_13">Severe</label>
		</td>
		<td>
		<input type="text" onBlur="checkAbsent(this);" id="os_14" name="elem_proptosisOs_text" value="<?php echo ($elem_proptosisOs_text);?>" class="form-control" placeholder="Text input" >
		</td>		
		</tr>
		
		<tr id="d_Ecch">
		<td >Ecchymosis</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_15" name="elem_ecchymosisOd_rul" value="RUL" <?php echo ($elem_ecchymosisOd_rul == "RUL") ? "checked=\"checked\"" : "" ;?>><label for="od_15">RUL</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_16" name="elem_ecchymosisOd_rll" value="RLL" <?php echo ($elem_ecchymosisOd_rll == "RLL") ? "checked=\"checked\"" : "" ;?>><label for="od_16">RLL</label>
		</td>
		<td >&nbsp;</td>
		<td colspan="2" >&nbsp;</td>
		<td align="center" class="bilat" onclick="check_bl('Ecch')">BL</td>
		<td >Ecchymosis</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_15" name="elem_ecchymosisOs_lul" value="LUL" <?php echo ($elem_ecchymosisOs_lul == "LUL") ? "checked=\"checked\"" : "" ;?>><label for="os_15">LUL</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_16" name="elem_ecchymosisOs_lll" value="LLL" <?php echo ($elem_ecchymosisOs_lll == "LLL") ? "checked=\"checked\"" : "" ;?>><label for="os_16">LLL</label>
		</td>
		<td >&nbsp;</td>
		<td colspan="2" >&nbsp;</td>
		</tr>		
		
		<tr id="d_Edema">
		<td >Edema</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_17" name="elem_edemaOd_rul" value="RUL" <?php echo ($elem_edemaOd_rul == "RUL") ? "checked=\"checked\"" : "" ;?>><label for="od_17">RUL</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_18" name="elem_edemaOd_rll" value="RLL" <?php echo ($elem_edemaOd_rll == "RLL") ? "checked=\"checked\"" : "" ;?>><label for="od_18">RLL</label>
		</td>
		<td >&nbsp;</td>
		<td colspan="2" >&nbsp;</td>
		<td align="center" class="bilat" onclick="check_bl('Edema')">BL</td>
		<td >Edema</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_17" name="elem_edemaOs_lul" value="LUL" <?php echo ($elem_edemaOs_lul == "LUL") ? "checked=\"checked\"" : "" ;?>><label for="os_17">LUL</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_18" name="elem_edemaOs_lll" value="LLL" <?php echo ($elem_edemaOs_lll == "LLL") ? "checked=\"checked\"" : "" ;?>><label for="os_18">LLL</label>
		</td>
		<td >&nbsp;</td>
		<td colspan="2" >&nbsp;</td>
		</tr>
		
		
		<tr id="d_Roseacea" class="grp_Roseacea">
		<td >Rosacea</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_mild" name="elem_roseaceaOd_mild" value="Mild" <?php echo ($elem_roseaceaOd_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="od_mild">Mild</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_moderate" name="elem_roseaceaOd_mod" value="Moderate" <?php echo ($elem_roseaceaOd_mod == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="od_moderate">Moderate</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_severe" name="elem_roseaceaOd_severe" value="Severe" <?php echo ($elem_roseaceaOd_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="od_severe">Severe</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_rul" name="elem_roseaceaOd_rul" value="RUL" <?php echo ($elem_roseaceaOd_rul == "RUL") ? "checked=\"checked\"" : "" ;?>><label for="od_rul">RUL</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="od_rll" name="elem_roseaceaOd_rll" value="RLL" <?php echo ($elem_roseaceaOd_rll == "RLL") ? "checked=\"checked\"" : "" ;?>><label for="od_rll" class="aato">RLL</label>
		</td>
		<td align="center" class="bilat" onclick="check_bl('Roseacea')" rowspan="2" >BL</td>
		<td >Rosacea</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_mild" name="elem_roseaceaOs_mild" value="Mild" <?php echo ($elem_roseaceaOs_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="os_mild">Mild</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_moderate" name="elem_roseaceaOs_mod" value="Moderate" <?php echo ($elem_roseaceaOs_mod == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="os_moderate">Moderate</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_severe" name="elem_roseaceaOs_severe" value="Severe" <?php echo ($elem_roseaceaOs_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="os_severe">Severe</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_rul" name="elem_roseaceaOs_lul" value="LUL" <?php echo ($elem_roseaceaOs_rul == "RUL" || $elem_roseaceaOs_lul == "LUL") ? "checked=\"checked\"" : "" ;?>><label for="os_rul">LUL</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();" id="os_rll" name="elem_roseaceaOs_lll" value="LLL" <?php echo ($elem_roseaceaOs_rll == "RLL" || $elem_roseaceaOs_lll == "LLL") ? "checked=\"checked\"" : "" ;?>><label for="os_rll" class="aato">LLL</label>
		</td>
		</tr>
		
		<tr id="d_Roseacea" class="grp_Roseacea">
		<td >&nbsp;</td>
		<td colspan="5" >
			<input type="text"  onClick="checkwnls();" id="od_txt" name="elem_roseaceaOd_txt" value="<?php echo ($elem_roseaceaOd_txt);?>" class="form-control" placeholder="Text input">			
		</td>
		
		<td >&nbsp;</td>
		<td colspan="5" >
			<input type="text"  onblur="checkwnls();" id="os_txt" name="elem_roseaceaOs_txt" value="<?php echo ($elem_roseaceaOs_txt);?>" class="form-control" placeholder="Text input">			
		</td>
		</tr>
		
		<tr class="exmhlgcol grp_handle grp_5thNerve <?php echo $cls_5thNerve; ?>" id="d_5thNerve" >
		<td class="grpbtn" onclick="openSubGrp('5thNerve')" ><label >5th Nerve <span class="glyphicon <?php echo $arow_5thNerve; ?>"></span></label> </td>
		<td colspan="5" >&nbsp;</td>
		<td align="center" class="bilat"></td>
		<td class="grpbtn" onclick="openSubGrp('5thNerve')"><label >5th Nerve <span class="glyphicon <?php echo $arow_5thNerve; ?>"></span></label> </td>
		<td colspan="5" >&nbsp;</td>
		</tr>
		
		<tr class="exmhlgcol grp_5thNerve <?php echo $cls_5thNerve; ?>">
		<td >Pin</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_pin_decreased" name="elem_5thNerve_pinOd_decreased" value="Decreased" <?php echo ($elem_5thNerve_pinOd_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="od_pin_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_pin_v1" name="elem_5thNerve_pinOd_v1" value="V1" <?php echo ($elem_5thNerve_pinOd_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="od_pin_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_pin_v2" name="elem_5thNerve_pinOd_v2" value="V2" <?php echo ($elem_5thNerve_pinOd_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="od_pin_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_pin_v3" name="elem_5thNerve_pinOd_v3" value="V3" <?php echo ($elem_5thNerve_pinOd_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="od_pin_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="od_pin_txt" name="elem_5thNerve_pinOd_txt" value="<?php echo ($elem_5thNerve_pinOd_txt);?>" class="form-control" placeholder="Text input">
		</td>
		<td rowspan="4" align="center" class="bilat" onclick="check_bl('5thNerve')">BL</td>	
		<td >Pin</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_pin_decreased" name="elem_5thNerve_pinOs_decreased" value="Decreased" <?php echo ($elem_5thNerve_pinOs_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="os_pin_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_pin_v1" name="elem_5thNerve_pinOs_v1" value="V1" <?php echo ($elem_5thNerve_pinOs_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="os_pin_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_pin_v2" name="elem_5thNerve_pinOs_v2" value="V2" <?php echo ($elem_5thNerve_pinOs_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="os_pin_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_pin_v3" name="elem_5thNerve_pinOs_v3" value="V3" <?php echo ($elem_5thNerve_pinOs_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="os_pin_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="os_pin_txt" name="elem_5thNerve_pinOs_txt" value="<?php echo ($elem_5thNerve_pinOs_txt);?>" class="form-control" placeholder="Text input">
		</td>
		</tr>
		
		
		<tr class="exmhlgcol grp_5thNerve <?php echo $cls_5thNerve; ?>">
		<td >Touch</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_touch_decreased" name="elem_5thNerve_touchOd_decreased" value="Decreased" <?php echo ($elem_5thNerve_touchOd_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="od_touch_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_touch_v1" name="elem_5thNerve_touchOd_v1" value="V1" <?php echo ($elem_5thNerve_touchOd_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="od_touch_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_touch_v2" name="elem_5thNerve_touchOd_v2" value="V2" <?php echo ($elem_5thNerve_touchOd_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="od_touch_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_touch_v3" name="elem_5thNerve_touchOd_v3" value="V3" <?php echo ($elem_5thNerve_touchOd_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="od_touch_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="od_touch_txt" name="elem_5thNerve_touchOd_txt" value="<?php echo ($elem_5thNerve_touchOd_txt);?>" class="form-control" placeholder="Text input">
		</td>
		
		<td >Touch</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_touch_decreased" name="elem_5thNerve_touchOs_decreased" value="Decreased" <?php echo ($elem_5thNerve_touchOs_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="os_touch_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_touch_v1" name="elem_5thNerve_touchOs_v1" value="V1" <?php echo ($elem_5thNerve_touchOs_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="os_touch_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_touch_v2" name="elem_5thNerve_touchOs_v2" value="V2" <?php echo ($elem_5thNerve_touchOs_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="os_touch_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_touch_v3" name="elem_5thNerve_touchOs_v3" value="V3" <?php echo ($elem_5thNerve_touchOs_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="os_touch_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="os_touch_txt" name="elem_5thNerve_touchOs_txt" value="<?php echo ($elem_5thNerve_touchOs_txt);?>" class="form-control" placeholder="Text input">
		</td>
		</tr>		
		
		<tr class="exmhlgcol grp_5thNerve <?php echo $cls_5thNerve; ?>">
		<td >Cornea Sensation</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_corsen_decreased" name="elem_5thNerve_corsenOd_decreased" value="Decreased" <?php echo ($elem_5thNerve_corsenOd_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="od_corsen_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_corsen_v1" name="elem_5thNerve_corsenOd_v1" value="V1" <?php echo ($elem_5thNerve_corsenOd_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="od_corsen_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_corsen_v2" name="elem_5thNerve_corsenOd_v2" value="V2" <?php echo ($elem_5thNerve_corsenOd_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="od_corsen_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_corsen_v3" name="elem_5thNerve_corsenOd_v3" value="V3" <?php echo ($elem_5thNerve_corsenOd_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="od_corsen_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="od_corsen_txt" name="elem_5thNerve_corsenOd_txt" value="<?php echo ($elem_5thNerve_corsenOd_txt);?>" class="form-control" placeholder="Text input">
		</td>	
		
		<td >Cornea Sensation</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_corsen_decreased" name="elem_5thNerve_corsenOs_decreased" value="Decreased" <?php echo ($elem_5thNerve_corsenOs_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="os_corsen_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_corsen_v1" name="elem_5thNerve_corsenOs_v1" value="V1" <?php echo ($elem_5thNerve_corsenOs_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="os_corsen_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_corsen_v2" name="elem_5thNerve_corsenOs_v2" value="V2" <?php echo ($elem_5thNerve_corsenOs_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="os_corsen_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_corsen_v3" name="elem_5thNerve_corsenOs_v3" value="V3" <?php echo ($elem_5thNerve_corsenOs_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="os_corsen_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="os_corsen_txt" name="elem_5thNerve_corsenOs_txt" value="<?php echo ($elem_5thNerve_corsenOs_txt);?>" class="form-control" placeholder="Text input">
		</td>
		</tr>
		
		
		<tr class="exmhlgcol grp_5thNerve <?php echo $cls_5thNerve; ?>">
		<td >Masseter</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_masseter_decreased" name="elem_5thNerve_masseterOd_decreased" value="Decreased" <?php echo ($elem_5thNerve_masseterOd_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="od_masseter_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_masseter_v1" name="elem_5thNerve_masseterOd_v1" value="V1" <?php echo ($elem_5thNerve_masseterOd_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="od_masseter_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_masseter_v2" name="elem_5thNerve_masseterOd_v2" value="V2" <?php echo ($elem_5thNerve_masseterOd_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="od_masseter_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="od_masseter_v3" name="elem_5thNerve_masseterOd_v3" value="V3" <?php echo ($elem_5thNerve_masseterOd_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="od_masseter_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="od_masseter_txt" name="elem_5thNerve_masseterOd_txt" value="<?php echo ($elem_5thNerve_masseterOd_txt);?>" class="form-control" placeholder="Text input">
		</td>
		
		<td >Masseter</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_masseter_decreased" name="elem_5thNerve_masseterOs_decreased" value="Decreased" <?php echo ($elem_5thNerve_masseterOs_decreased == "Decreased") ? "checked=\"checked\"" : "" ;?>><label for="os_masseter_decreased">Decreased</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_masseter_v1" name="elem_5thNerve_masseterOs_v1" value="V1" <?php echo ($elem_5thNerve_masseterOs_v1 == "V1") ? "checked=\"checked\"" : "" ;?>><label for="os_masseter_v1">V1</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_masseter_v2" name="elem_5thNerve_masseterOs_v2" value="V2" <?php echo ($elem_5thNerve_masseterOs_v2 == "V2") ? "checked=\"checked\"" : "" ;?>><label for="os_masseter_v2">V2</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'5thNerve');" id="os_masseter_v3" name="elem_5thNerve_masseterOs_v3" value="V3" <?php echo ($elem_5thNerve_masseterOs_v3 == "V3") ? "checked=\"checked\"" : "" ;?>><label for="os_masseter_v3" class="aato">V3</label>
		</td>
		<td>
		<input type="text"  onblur="checkwnls();checkSymClr(this,'5thNerve');" id="os_masseter_txt" name="elem_5thNerve_masseterOs_txt" value="<?php echo ($elem_5thNerve_masseterOs_txt);?>" class="form-control" placeholder="Text input">
		</td>
		</tr>		
		
		<tr class="exmhlgcol grp_handle grp_7thNerveParesis <?php echo $cls_7thNerveParesis; ?>" id="d_7thNerveParesis">
		<td colspan="2" class="grpbtn" onclick="openSubGrp('7thNerveParesis')"><label >7th Nerve Paresis <span class="glyphicon <?php echo $arow_7thNerveParesis; ?>"></span></label> </td>
		<td colspan="4" >&nbsp;</td>
		<td align="center" class="bilat"></td>
		<td colspan="2" class="grpbtn" onclick="openSubGrp('7thNerveParesis')"><label >7th Nerve Paresis <span class="glyphicon <?php echo $arow_7thNerveParesis; ?>"></span></label> </td>
		<td colspan="4" >&nbsp;</td>
		</tr>
		
		<tr class="exmhlgcol grp_7thNerveParesis <?php echo $cls_7thNerveParesis; ?>">
		<td >Cranial Nerve <br/>Abnormalities</td>
		<td colspan="2">
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="od_cna_decupr" name="elem_7thNerveParesisOd_cna_decUpr" value="Decreased Upper" <?php echo ($elem_7thNerveParesisOd_cna_decUpr == "Decreased Upper") ? "checked=\"checked\"" : "" ;?>><label for="od_cna_decupr" class="aato">Decreased Upper</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="od_cna_lower" name="elem_7thNerveParesisOd_cna_lower" value="Lower" <?php echo ($elem_7thNerveParesisOd_cna_lower == "Lower") ? "checked=\"checked\"" : "" ;?>><label for="od_cna_lower">Lower</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="od_cna_synkinesis" name="elem_7thNerveParesisOd_cna_synkinesis" value="Synkinesis" <?php echo ($elem_7thNerveParesisOd_cna_synkinesis == "Synkinesis") ? "checked=\"checked\"" : "" ;?>><label for="od_cna_synkinesis">Synkinesis</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="od_cna_spasm" name="elem_7thNerveParesisOd_cna_spasm" value="Spasm" <?php echo ($elem_7thNerveParesisOd_cna_spasm == "Spasm") ? "checked=\"checked\"" : "" ;?>><label for="od_cna_spasm">Spasm</label>
		</td>
		<td align="center" class="bilat" onclick="check_bl('7thNerveParesis')">BL</td>	
		<td >Cranial Nerve <br/>Abnormalities</td>
		<td colspan="2">
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="os_cna_decupr" name="elem_7thNerveParesisOs_cna_decUpr" value="Decreased Upper" <?php echo ($elem_7thNerveParesisOs_cna_decUpr == "Decreased Upper") ? "checked=\"checked\"" : "" ;?>><label for="os_cna_decupr" class="aato">Decreased Upper</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="os_cna_lower" name="elem_7thNerveParesisOs_cna_lower" value="Lower" <?php echo ($elem_7thNerveParesisOs_cna_lower == "Lower") ? "checked=\"checked\"" : "" ;?>><label for="os_cna_lower">Lower</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="os_cna_synkinesis" name="elem_7thNerveParesisOs_cna_synkinesis" value="Synkinesis" <?php echo ($elem_7thNerveParesisOs_cna_synkinesis == "Synkinesis") ? "checked=\"checked\"" : "" ;?>><label for="os_cna_synkinesis">Synkinesis</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'7thNerveParesis');" id="os_cna_spasm" name="elem_7thNerveParesisOs_cna_spasm" value="Spasm" <?php echo ($elem_7thNerveParesisOs_cna_spasm == "Spasm") ? "checked=\"checked\"" : "" ;?>><label for="os_cna_spasm">Spasm</label>
		</td>		
		</tr>		
		
		<tr class="exmhlgcol grp_handle grp_Trauma <?php echo $cls_Trauma; ?>" id="d_Trauma">
		<td class="grpbtn" onclick="openSubGrp('Trauma')"><label >Trauma <span class="glyphicon <?php echo $arow_Trauma; ?>"></span></label> </td>
		<td colspan="5" ><textarea onClick="checkwnls" onBlur="checkwnls();checkSymClr(this,'Trauma');" id="od_19" name="elem_traumaOd_text" class="form-control"><?php echo ($elem_traumaOd_text); ?></textarea></td>
		<td align="center" class="bilat" onclick="check_bl('Trauma')">BL</td>
		<td class="grpbtn" onclick="openSubGrp('Trauma')"><label >Trauma <span class="glyphicon <?php echo $arow_Trauma; ?>"></span></label> </td>
		<td colspan="5" ><textarea onClick="checkwnls" onBlur="checkwnls();checkSymClr(this,'Trauma');" id="os_19" name="elem_traumaOs_text" class="form-control"><?php echo ($elem_traumaOs_text); ?></textarea></td>
		</tr>	
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Tenderness</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" id="od_20" name="elem_traumaOd_tender_mild" value="Mild" <?php echo ($elem_traumaOd_tender_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="od_20">Mild</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" id="od_21" name="elem_traumaOd_tender_moderate" value="Moderate" <?php echo ($elem_traumaOd_tender_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="od_21">Moderate</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" id="od_22" name="elem_traumaOd_tender_severe" value="Severe" <?php echo ($elem_traumaOd_tender_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="od_22">Severe</label>
		</td>
		<td ></td>
		<td ></td>
		<td align="center" rowspan="11" class="bilat" onclick="check_bl('Trauma')">BL</td>
		<td >Tenderness</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" id="os_20" name="elem_traumaOs_tender_mild" value="Mild" <?php echo ($elem_traumaOs_tender_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="os_20">Mild</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" id="os_21" name="elem_traumaOs_tender_moderate" value="Moderate" <?php echo ($elem_traumaOs_tender_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="os_21">Moderate</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" id="os_22" name="elem_traumaOs_tender_severe" value="Severe" <?php echo ($elem_traumaOs_tender_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="os_22">Severe</label>
		</td>
		<td ></td>
		<td ></td>
		</tr>
		
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Palpable Fracture</td>
		<td>
		<input id="od_23" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_palpable_neg" value="Absent" <?php echo ($elem_traumaOd_palpable_neg == "-ve" || $elem_traumaOd_palpable_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_23">Absent</label>
		</td>
		<td>
		<input id="od_24" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_palpable_pos" value="Present" <?php echo ($elem_traumaOd_palpable_pos == "+ve" || $elem_traumaOd_palpable_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_24">Present</label>
		</td>
		<td>
		<input id="od_25" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_palpable_sup" value="Sup" <?php echo ($elem_traumaOd_palpable_sup == "Sup") ? "checked=\"checked\"" : "" ;?>><label for="od_25">Sup</label>
		</td>
		<td>
		<input id="od_26" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_palpable_inf" value="Inf" <?php echo ($elem_traumaOd_palpable_inf == "Inf") ? "checked=\"checked\"" : "" ;?>><label for="od_26">Inf</label>
		</td>
		<td>
		<input id="od_27" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_palpable_tmp" value="Tmp" <?php echo ($elem_traumaOd_palpable_tmp == "Tmp") ? "checked=\"checked\"" : "" ;?>><label for="od_27" class="aato">Tmp</label>
		</td>
		
		<td >Palpable Fracture</td>
		<td>
		<input id="os_23" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_palpable_neg" value="Absent" <?php echo ($elem_traumaOs_palpable_neg == "-ve" || $elem_traumaOs_palpable_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_23">Absent</label>
		</td>
		<td>
		<input id="os_24" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_palpable_pos" value="Present" <?php echo ($elem_traumaOs_palpable_pos == "+ve" ||  $elem_traumaOs_palpable_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_24">Present</label>
		</td>
		<td>
		<input id="os_25" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_palpable_sup" value="Sup" <?php echo ($elem_traumaOs_palpable_sup == "Sup") ? "checked=\"checked\"" : "" ;?>><label for="os_25">Sup</label>
		</td>
		<td>
		<input id="os_26" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_palpable_inf" value="Inf" <?php echo ($elem_traumaOs_palpable_inf == "Inf") ? "checked=\"checked\"" : "" ;?>><label for="os_26">Inf</label>
		</td>
		<td>
		<input id="os_27" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_palpable_tmp" value="Tmp" <?php echo ($elem_traumaOs_palpable_tmp == "Tmp") ? "checked=\"checked\"" : "" ;?>><label for="os_27" class="aato">Tmp</label>
		</td>
		</tr>
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Crepitus</td>
		<td>
		<input id="od_28" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_crepitus_neg" value="Absent" <?php echo ($elem_traumaOd_crepitus_neg == "-ve" || $elem_traumaOd_crepitus_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_28">Absent</label>
		</td>
		<td>
		<input id="od_29" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_crepitus_pos" value="Present" <?php echo ($elem_traumaOd_crepitus_pos == "+ve" || $elem_traumaOd_crepitus_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_29">Present</label>
		</td>
		<td>
		<input id="od_30" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_crepitus_sp" value="Sup" <?php echo ($elem_traumaOd_crepitus_sp == "Sup") ? "checked=\"checked\"" : "" ;?>><label for="od_30">Sup</label>
		</td>
		<td>
		<input id="od_31" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_crepitus_if" value="Inf" <?php echo ($elem_traumaOd_crepitus_if == "Inf") ? "checked=\"checked\"" : "" ;?>><label for="od_31">Inf</label>
		</td>
		<td ></td>
		
		<td >Crepitus</td>
		<td>
		<input id="os_28" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_crepitus_neg" value="Absent" <?php echo ($elem_traumaOs_crepitus_neg == "-ve" || $elem_traumaOs_crepitus_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_28">Absent</label>
		</td>
		<td>
		<input id="os_29" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_crepitus_pos" value="Present" <?php echo ($elem_traumaOs_crepitus_pos == "+ve"  || $elem_traumaOs_crepitus_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_29">Present</label>
		</td>
		<td>
		<input id="os_30" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_crepitus_sp" value="Sup" <?php echo ($elem_traumaOs_crepitus_sp == "Sup") ? "checked=\"checked\"" : "" ;?>><label for="os_30">Sup</label>
		</td>
		<td>
		<input id="os_31" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_crepitus_if" value="Inf" <?php echo ($elem_traumaOs_crepitus_if == "Inf") ? "checked=\"checked\"" : "" ;?>><label for="os_31">Inf</label>
		</td>
		<td ></td>
		</tr>
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Exophthalmos</td>
		<td>
		<input id="od_32" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_exoph_neg" value="Absent" <?php echo ($elem_traumaOd_exoph_neg == "-ve" || $elem_traumaOd_exoph_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_32">Absent</label>
		</td>
		<td>
		<input id="od_33" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_exoph_pos" value="Present" <?php echo ($elem_traumaOd_exoph_pos == "+ve" || $elem_traumaOd_exoph_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_33">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		
		<td >Exophthalmos</td>
		<td>
		<input id="os_32" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_exoph_neg" value="Absent" <?php echo ($elem_traumaOs_exoph_neg == "-ve" || $elem_traumaOs_exoph_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_32">Absent</label>
		</td>
		<td>
		<input id="os_33" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_exoph_pos" value="Present" <?php echo ($elem_traumaOs_exoph_pos == "+ve" || $elem_traumaOs_exoph_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_33">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		</tr>
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Enophthalmos</td>
		<td>
		<input id="od_34" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_enoph_neg" value="Absent" <?php echo ($elem_traumaOd_enoph_neg == "-ve" || $elem_traumaOd_enoph_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_34">Absent</label>
		</td>
		<td>
		<input id="od_35" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_enoph_pos" value="Present" <?php echo ($elem_traumaOd_enoph_pos == "+ve" || $elem_traumaOd_enoph_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_35">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		
		<td >Enophthalmos</td>
		<td>
		<input id="os_34" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_enoph_neg" value="Absent" <?php echo ($elem_traumaOs_enoph_neg == "-ve" || $elem_traumaOs_enoph_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_34">Absent</label>
		</td>
		<td>
		<input id="os_35" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_enoph_pos" value="Present" <?php echo ($elem_traumaOs_enoph_pos == "+ve" || $elem_traumaOs_enoph_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_35">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		</tr>
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Paresthesia/<br> Anesthesia</td>
		<td>
		<input id="od_36" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_pares_neg" value="Absent" <?php echo ($elem_traumaOd_pares_neg == "-ve" || $elem_traumaOd_pares_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_36">Absent</label>
		</td>
		<td>
		<input id="od_37" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_pares_pos" value="Present" <?php echo ($elem_traumaOd_pares_pos == "+ve" || $elem_traumaOd_pares_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_37">Present</label>
		</td>
		<td colspan="3">
		<input id="od_38" type="text" onBlur="checkAbsent(this,'Trauma');" name="elem_traumaOd_pares_text" value="<?php echo ($elem_traumaOd_pares_text);?>"  class="form-control" placeholder="Text input" >
		</td>		
		
		<td >Paresthesia/<br> Anesthesia</td>
		<td>
		<input id="os_36" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_pares_neg" value="Absent" <?php echo ($elem_traumaOs_pares_neg == "-ve" || $elem_traumaOs_pares_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_36">Absent</label>
		</td>
		<td>
		<input id="os_37" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_pares_pos" value="Present" <?php echo ($elem_traumaOs_pares_pos == "+ve" || $elem_traumaOs_pares_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_37">Present</label>
		</td>
		<td colspan="3">
		<input id="os_38" onBlur="checkAbsent(this,'Trauma');" type="text" name="elem_traumaOs_pares_text" value="<?php echo ($elem_traumaOs_pares_text);?>" class="form-control" placeholder="Text input" >
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>" id="tr_trauma_edema1">
		<td >Edema</td>
		<td>
		<input id="od_39" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_edema_neg" value="Absent" <?php echo ($elem_traumaOd_edema_neg == "-ve" || $elem_traumaOd_edema_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_39">Absent</label>
		</td>
		<td>
		<input id="od_40" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_edema_pos" value="Present" <?php echo ($elem_traumaOd_edema_pos == "+ve" || $elem_traumaOd_edema_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_40">Present</label>
		</td>
		<td>
		<input id="od_41" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_edema_mild" value="Mild" <?php echo ($elem_traumaOd_edema_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="od_41">Mild</label>
		</td>
		<td>
		<input id="od_42" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_edema_moderate" value="Moderate" <?php echo ($elem_traumaOd_edema_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="od_42">Moderate</label>
		</td>
		<td ></td>
		
		<td >Edema</td>
		<td>
		<input id="os_39" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_edema_neg" value="Absent" <?php echo ($elem_traumaOs_edema_neg == "-ve" || $elem_traumaOs_edema_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_39">Absent</label>
		</td>
		<td>
		<input id="os_40" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_edema_pos" value="Present" <?php echo ($elem_traumaOs_edema_pos == "+ve" || $elem_traumaOs_edema_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_40">Present</label>
		</td>
		<td>
		<input id="os_41" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_edema_mild" value="Mild" <?php echo ($elem_traumaOs_edema_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="os_41">Mild</label>
		</td>
		<td>
		<input id="os_42" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_edema_moderate" value="Moderate" <?php echo ($elem_traumaOs_edema_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="os_42">Moderate</label>
		</td>
		<td ></td>
		</tr>
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>" id="tr_trauma_edema2">
		<td ></td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="od_43" name="elem_traumaOd_edema_severe" value="Severe" <?php echo ($elem_traumaOd_edema_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="od_43">Severe</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="od_44" name="elem_traumaOd_edema_sup" value="Sup" <?php echo ($elem_traumaOd_edema_sup == "Sup") ? "checked=\"checked\"" : "" ;?>><label for="od_44">Sup</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="od_45"  name="elem_traumaOd_edema_inf" value="Inf" <?php echo ($elem_traumaOd_edema_inf == "Inf") ? "checked=\"checked\"" : "" ;?>><label for="od_45">Inf</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="od_46" name="elem_traumaOd_edema_tmp" value="Tmp" <?php echo ($elem_traumaOd_edema_tmp == "Tmp") ? "checked=\"checked\"" : "" ;?>><label for="od_46" class="aato">Tmp</label>
		</td>
		<td ></td>
		
		<td ></td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="os_43" name="elem_traumaOs_edema_severe" value="Severe" <?php echo ($elem_traumaOs_edema_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="os_43">Severe</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="os_44" name="elem_traumaOs_edema_sup" value="Sup" <?php echo ($elem_traumaOs_edema_sup == "Sup") ? "checked=\"checked\"" : "" ;?>><label for="os_44">Sup</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="os_45" name="elem_traumaOs_edema_inf" value="Inf" <?php echo ($elem_traumaOs_edema_inf == "Inf") ? "checked=\"checked\"" : "" ;?>><label for="os_45">Inf</label>
		</td>
		<td>
		<input type="checkbox"  onClick="checkAbsent(this,'Trauma');" id="os_46" name="elem_traumaOs_edema_tmp" value="Tmp" <?php echo ($elem_traumaOs_edema_tmp == "Tmp") ? "checked=\"checked\"" : "" ;?>><label for="os_46" class="aato">Tmp</label>
		</td>
		<td ></td>
		</tr>	
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Laceration</td>
		<td>
		<input id="od_47" type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" name="elem_traumaOd_laceration_super" value="Superficial" <?php echo ($elem_traumaOd_laceration_super == "Superficial") ? "checked=\"checked\"" : "" ;?>><label for="od_47">Superficial</label>
		</td>
		<td>
		<input id="od_48" type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" name="elem_traumaOd_laceration_deep" value="Deep" <?php echo ($elem_traumaOd_laceration_deep == "Deep") ? "checked=\"checked\"" : "" ;?>><label for="od_48">Deep</label>
		</td>
		<td colspan="3">
		<input id="od_49" type="text" onBlur="checkwnls();checkSymClr(this,'Trauma');" name="elem_traumaOd_laceration_text" value="<?php echo ($elem_traumaOd_laceration_text);?>" class="form-control" placeholder="Text input" >
		</td>		
		
		<td >Laceration</td>
		<td>
		<input id="os_47" type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" name="elem_traumaOs_laceration_super" value="Superficial" <?php echo ($elem_traumaOs_laceration_super == "Superficial") ? "checked=\"checked\"" : "" ;?>><label for="os_47">Superficial</label>
		</td>
		<td>
		<input id="os_48" type="checkbox"  onClick="checkwnls();checkSymClr(this,'Trauma');" name="elem_traumaOs_laceration_deep" value="Deep" <?php echo ($elem_traumaOs_laceration_deep == "Deep") ? "checked=\"checked\"" : "" ;?>><label for="os_48">Deep</label>
		</td>
		<td colspan="3">
		<input id="os_49" type="text" onBlur="checkwnls();checkSymClr(this,'Trauma');" name="elem_traumaOs_laceration_text" value="<?php echo ($elem_traumaOs_laceration_text);?>" class="form-control" placeholder="Text input" >
		</td>
		</tr>		
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Dermal Abrasion</td>
		<td>
		<input id="od_50" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_dermal_neg" value="Absent" <?php echo ($elem_traumaOd_dermal_neg == "-ve" || $elem_traumaOd_dermal_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_50">Absent</label>
		</td>
		<td>
		<input id="od_51" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_dermal_pos" value="Present" <?php echo ($elem_traumaOd_dermal_pos == "+ve" || $elem_traumaOd_dermal_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_51">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		
		<td >Dermal Abrasion</td>
		<td>
		<input id="os_50" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_dermal_neg" value="Absent" <?php echo ($elem_traumaOs_dermal_neg == "-ve" || $elem_traumaOs_dermal_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_50">Absent</label>
		</td>
		<td>
		<input id="os_51" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_dermal_pos" value="Present" <?php echo ($elem_traumaOs_dermal_pos == "+ve" || $elem_traumaOs_dermal_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_51">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		</tr>		
		
		<tr class="exmhlgcol grp_Trauma <?php echo $cls_Trauma; ?>">
		<td >Eschar</td>
		<td>
		<input id="od_52" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_eschar_neg" value="Absent" <?php echo ($elem_traumaOd_eschar_neg == "-ve" || $elem_traumaOd_eschar_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_52">Absent</label>
		</td>
		<td>
		<input id="od_53" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOd_eschar_pos" value="Present" <?php echo ($elem_traumaOd_eschar_pos == "+ve" || $elem_traumaOd_eschar_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_53">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		
		<td >Eschar</td>
		<td>
		<input id="os_52" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_eschar_neg" value="Absent" <?php echo ($elem_traumaOs_eschar_neg == "-ve" || $elem_traumaOs_eschar_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_52">Absent</label>
		</td>
		<td>
		<input id="os_53" type="checkbox"  onClick="checkAbsent(this,'Trauma');" name="elem_traumaOs_eschar_pos" value="Present" <?php echo ($elem_traumaOs_eschar_pos == "+ve" || $elem_traumaOs_eschar_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_53">Present</label>
		</td>
		<td ></td>
		<td ></td>
		<td ></td>
		</tr>
		
		
		<tr id="d_adOpt_ee">
		<td colspan="6" align="left"><textarea onBlur="checkwnls()"  id="od_54" name="elem_externalAdOptionsOd"  class="form-control" rows="3" placeholder="Comments"><?php echo ($elem_externalAdOptionsOd);?></textarea></td>
		<td align="center" class="bilat" onclick="check_bl('adOpt_ee')">BL</td>
		<td colspan="6" align="left"><textarea  onBlur="checkwnls()"  id="os_54" name="elem_externalAdOptionsOs"  class="form-control" rows="3" placeholder="Comments"><?php echo ($elem_externalAdOptionsOs);?></textarea></td>
		</tr>		
		
		</table>
	   </div>
	</div>
	<div role="tabpanel" class="tab-pane" id="divDraw">
		<!--Drawing -->
		<div class="subExam <?php //echo $cssDivDraw;?>">
			<div class="examhd">
				<?php if($finalize_flag == 1){?>
				<label class="chart_status label label-danger pull-left">Finalized</label>
				<?php }?>
				
				<span id="examFlag" class="glyphicon flagWnl "></span>
				
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>
				
				<input type="checkbox" id="elem_noChangeDraw"  name="elem_noChangeDraw" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncDraw == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChangeDraw">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/?>
			</div>
			<div class="clearfix"> </div>
			<div class="row">
				<div class="col-sm-2">
					<textarea onBlur="checkwnls()" name="elem_eeDesc_od" id="elem_eeDesc_od" class="form-control drw_text_box"  ><?php echo $elem_eeDesc_od;?></textarea>
				</div>
				<div class="col-sm-8 div_idoc_drw">
					<?php
					if($blEnableHTMLDrawing == false){ //Applet
					?>
					<?php
					}elseif($blEnableHTMLDrawing == true){ //html5						
					?>
						<input type="hidden" id="hidDrawingLoadAJAX" name="hidDrawingLoadAJAX" value="0"/>
						<input type="hidden" id="idoc_intDrawingExamId" name="idoc_intDrawingExamId" value="<?php echo $intDrawingExamId; ?>"/>
						<input type="hidden" id="idoc_intDrawingFormId" name="idoc_intDrawingFormId" value="<?php echo $intDrawingFormId; ?>"/>
						<input type="hidden" id="idoc_strScanUploadfor" name="idoc_strScanUploadfor" value="<?php echo $strScanUploadfor; ?>"/> 
						<?php
						    //$intDrawingExamId = $intDrawingFormId = 0;
						    //$strScanUploadfor = "";
						    //$intDrawingFormId = $form_id;
						    //$intDrawingExamId = $elem_eeId;
						    //$strScanUploadfor = "EXTERNAL_DSU";
						    //require("iDoc-Drawing/drawing.php");
						for($intTempDrawCount = 0; $intTempDrawCount < $drawCntlNum; $intTempDrawCount++){
							$dbdrawID = 0;
							$dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $imgDB = "";
							
							$dbdrawID = (int)$arrDrwaingData[0][$intTempDrawCount];
							if($dbdrawID > 0){
								$dbTollImage = $arrDrwaingData[1][$intTempDrawCount];
								$dbPatTestName = $arrDrwaingData[2][$intTempDrawCount];
								$dbPatTestId = $arrDrwaingData[3][$intTempDrawCount];
								$dbTestImg = $arrDrwaingData[4][$intTempDrawCount];
								$imgDB = $arrDrwaingData[5][$intTempDrawCount];
							}
							if(empty($dbTollImage) == true){
								$dbTollImage = "imgPicConCanvas";
							}
							
							$stDisp = "hidden";
							if($intTempDrawCount == 0){	$stDisp = "";		}
						?>
							<div id="divDrawing<?php echo $intTempDrawCount; ?>" class="<?php echo $stDisp; ?>">                            
							<input type="hidden" id="hidImageCss<?php echo $intTempDrawCount; ?>" name="hidImageCss<?php echo $intTempDrawCount; ?>" value="<?php echo $dbTollImage; ?>"/>
							<input type="hidden" id="hidRedPixel<?php echo $intTempDrawCount; ?>" name="hidRedPixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbRedPixel; ?>"/>
							<input type="hidden" id="hidGreenPixel<?php echo $intTempDrawCount; ?>" name="hidGreenPixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbGreenPixel; ?>"/>
							<input type="hidden" id="hidBluePixel<?php echo $intTempDrawCount; ?>" name="hidBluePixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbBluePixel; ?>"/>
							<input type="hidden" id="hidAlphaPixel<?php echo $intTempDrawCount; ?>" name="hidAlphaPixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbAlphaPixel; ?>"/>
							<input type="hidden" id="hidDrawingTestName<?php echo $intTempDrawCount; ?>" name="hidDrawingTestName<?php echo $intTempDrawCount; ?>" value="<?php echo $dbPatTestName; ?>"/>
							<input type="hidden" id="hidDrawingTestId<?php echo $intTempDrawCount; ?>" name="hidDrawingTestId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbPatTestId; ?>"/>
							<input type="hidden" id="hidDrawingTestImageP<?php echo $intTempDrawCount; ?>" name="hidDrawingTestImageP<?php echo $intTempDrawCount; ?>" value="<?php echo $dbTestImg; ?>"/>
							<input type="hidden" id="hidCanvasImgData<?php echo $intTempDrawCount; ?>" name="hidCanvasImgData<?php echo $intTempDrawCount; ?>" />
							<input type="hidden" id="hidImgDataFileName<?php echo $intTempDrawCount; ?>" name="hidImgDataFileName<?php echo $intTempDrawCount; ?>" value="<?php //echo $canvasDataFileNameDB; ?>" />
							<input type="hidden" id="hidDrawingChangeYesNo<?php echo $intTempDrawCount; ?>" name="hidDrawingChangeYesNo<?php echo $intTempDrawCount; ?>" />
							<input type="hidden" id="hidImagesData<?php echo $intTempDrawCount; ?>" name="hidImagesData<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbCanvasImageDataPoint; ?>" />
							<input type="hidden" id="hidOldAppletImaData<?php echo $intTempDrawCount; ?>" name="hidOldAppletImaData<?php echo $intTempDrawCount; ?>" value="<?php echo $fileNameTempOldData; ?>" />
							<input type="hidden" name="hidExternalDrawingId<?php echo $intTempDrawCount; ?>" id="hidExternalDrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
							<input type="hidden" name="hidDone<?php echo $intTempDrawCount; ?>" id="hidDone<?php echo $intTempDrawCount; ?>" >							
							<input type="hidden" id="hidDrwDataJson<?php echo $intTempDrawCount; ?>" name="hidDrwDataJson<?php echo $intTempDrawCount; ?>" >							
							<?php
								if($dbdrawID > 0){ echo "<input type=\"hidden\" name=\"hidLoad".$dbdrawID."\" id=\"hidLoad".$dbdrawID."\" >"; }
								include(dirname(__FILE__)."/drawing_new.php");
							?>
							</div>						
						<?php
						}
					}
					?>
				</div>
				<div class="col-sm-2">
					<textarea onBlur="checkwnls()" name="elem_eeDesc_os" id="elem_eeDesc_os" class="form-control drw_text_box"  ><?php echo $elem_eeDesc_os;?></textarea>
				</div>
			</div>
		</div>
		<!--Drawing -->
	</div>
	</div>

</div><!-- Tab Content -->
<div class="clearfix"></div>

</div>
</div>

<nav class="navbar navbar-inverse navbar-fixed-bottom btnbar">
	<div class="container center-block text-center">
		
		<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?> 
		<button type="button" class="btn btn-success navbar-btn" onclick="save_exam()">Done</button>
		<?php }?>
		
		<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?>
		<button type="button" class="btn btn-success navbar-btn" onclick="reset_exam()">Reset</button>
		<button type="button" class="btn btn-success navbar-btn" onclick="previous_exam()">Previous</button>
		<?php if(!empty($elem_editMode)){?>
		<button type="button" class="btn btn-success navbar-btn" onclick="purg_exam()">Purge</button>
		<?php }?>
		<?php }?>	
		<button type="button" class="btn btn-danger navbar-btn pull-right" onclick="cancel_exam()">Cancel</button>
		
	</div>
</nav>
</form>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>
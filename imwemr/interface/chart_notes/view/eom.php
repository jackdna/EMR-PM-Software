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
	#Grids .grid table td{border-right:4px solid black;border-bottom:4px solid black; width:33%; text-align:center; }
	#Grids .grid table tr > td.lasttd{border-right:0px;}
	#Grids .grid table tr.lasttr > td {border-bottom:0px;}
	#Grids .grid table tr td input[type=text]{width:59px;}	
	.doc_eom .menu .btn{padding-top:3px;padding-bottom:3px; }
	#divw4dot .exambox, #divcolorvis .exambox, #divAnoHead .exambox, #divNystag .exambox, #divGenComm .exambox{ min-height:100px; }
	.exammain .form-control{ margin-bottom: 0px!important;  }
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
var examName = "EOM";
var arrSubExams = new Array("Eom","Eom3");
var ProClr=<?php echo $ProClr;?>;
var drawCntlNum=<?php echo $drawCntlNum; ?>;
var blEnableHTMLDrawing="<?php echo $blEnableHTMLDrawing;?>";
var def_pg='<?php echo $_GET["pg"];?>';

</script>

    
</head>
<body class="doc_eom exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->
<form name="frmEom" id="frmEom" action="saveCharts.php" method="post" enctype="multipart/form-data" onSubmit="setNC2(this,1);" class="frcb">

<input type="hidden" name="elem_saveForm" value="EOM">
<input type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
<input type="hidden" name="elem_eomId" value="<?php echo $eom_id;?>">
<input type="hidden" name="elem_eomId_LF" value="<?php echo $elem_eomId_LF;?>">
<input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $elem_formId;?>">
<input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $elem_patientId;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
<input type="hidden" id="elem_wnl" name="elem_wnl" value="<?php echo $wnl;?>">
<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">

<input type="hidden" name="elem_isPositive" id="elem_isPositive" value="<?php echo $isPositive;?>">
<input type="hidden" name="elem_descEom" value="<?php echo $elem_descEom;?>">

<input type="hidden" name="elem_wnl2" value="<?php echo $wnl_2;?>">
<input type="hidden" name="elem_isPositive2" value="<?php echo $isPositive_2;?>">
<input type="hidden" name="elem_noChange2" value="<?php echo $examined_no_change2;?>">
<input type="hidden" name="elem_wnl3" value="<?php echo $wnl_3;?>">
<input type="hidden" name="elem_isPositive3" value="<?php echo $isPositive_3;?>">
<input type="hidden" name="elem_noChange3" value="<?php echo $examined_no_change3;?>">

<?php //Hidden fields ?>
<input type="hidden" name="elem_wnlEom" id="elem_wnlEom" value="<?php echo $wnl;?>">
<input type="hidden" name="elem_posEom" value="<?php echo $isPositive;?>">
<input type="hidden" name="elem_ncEom" value="<?php echo $examined_no_change;?>">
<input type="hidden" name="elem_wnlEom3" value="<?php echo $wnl_3;?>">
<input type="hidden" name="elem_posEom3" value="<?php echo $isPositive_3;?>">
<input type="hidden" name="elem_ncEom3" value="<?php echo $examined_no_change3;?>">
<input type="hidden" name="elem_examined_no_change" value="<?php echo $examined_no_change;?>">
<?php //Hidden fields ; ?>

<input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
<!--<input type="hidden" name="hidEOMDrawingId" id="hidEOMDrawingId" value="<?php //echo $dbIdocDrawingId;?>">-->
<input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">

<input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
<input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">

<!-- newET_changeIndctr -->
<?php echo $elem_changeInd; ?>
<!-- newET_changeIndctr -->

<div class=" container-fluid">
<div class="whtbox exammain ">

<div class="clearfix"></div>
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
	<?php	
	foreach($arrTabs as $key => $val){
	$tmp2=$key;
	$tmp = ($key == "Eom") ? "active" : "";
	?>
	<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab" onclick="changeTab('<?php echo $key;?>')" id="tab<?php echo $key;?>" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active <?php echo ($elem_editMode == 0 || $elem_chng_divEom==0) ? "bggrey" : "";?>" id="divEom">
	<div class="examhd">
	
	<?php if($finalize_flag == 1){?>
	<label class="chart_status label label-danger pull-left">Finalized</label>
	<?php }?>
	
	<span id="examFlag" class="glyphicon flagWnl "></span>
	<!--<img src="<?php //echo $GLOBALS['webroot'];?>/library/images/flag_yellow.png" alt=""/>--> 
	
	<button class="wnl_btn" type="button" onClick="setwnl();">WNL</button>
	
	<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();" 
			<?php echo ($examined_no_change == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
	<label class="lbl_nochange frcb" for="elem_noChange">NO Change</label>			
	
	<?php /*if (constant('AV_MODULE')=='YES'){?>
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
	<?php }*/?>
	
	</div>
	
	<div class="clearfix"> </div>
	<div class="table-responsive">
	<table class="table table-bordered table-striped">
	<tr>
	<td width="15%"><strong>NPC</strong></td>
	<td width="5%"><input type="checkbox" id="elem_npcWnlAbn_wnl" name="elem_npcWnlAbn" value="WNL" 
	<?php if($npc_wnl_abn=="WNL") echo 'checked';?> onClick="checkYFlagP(this);npcCheckCb(this);">
	<label for="elem_npcWnlAbn_wnl">WNL</label>
	</td>
	<td colspan="2">
	<input type="checkbox" id="elem_npcWnlAbn_Abn" name="elem_npcWnlAbn" value="Abn"  
	<?php if($npc_wnl_abn=="Abn") echo 'checked';?>  onClick="checkYFlagP(this);npcCheckCb(this);">
	<label for="elem_npcWnlAbn_Abn">ABN</label>
	</td>
	<td width="26%" colspan="4" class="form-inline"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this)" name="elem_npcCm" value="<?php echo $npc_cm ;?>"> cm</td>
	<td colspan="3">
	<input type="checkbox" id="elem_npcWnlAbn_na" name="elem_npcWnlAbn" value="N/A"  
	<?php if($npc_wnl_abn=="N/A") echo 'checked';?> onClick="checkYFlagP(this);npcCheckCb(this);">
	<label for="elem_npcWnlAbn_na">N/A</label>
	</td>
	<td width="9%">Comments</td>
	<td width="23%"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this);" id="ortho_desc"  name="ortho_desc" value="<?php echo $ortho_desc;?>"></td>


	</tr>
	<tr>
	<td><strong>NPA</strong></td>
	<td><input type="checkbox" id="elem_npaWnlAbn_WNL" name="elem_npaWnlAbn" value="WNL" 
	<?php if($elem_npaWnlAbn=="WNL") echo 'checked';?> onClick="checkYFlagP(this);npcCheckCb(this);">
	<label for="elem_npaWnlAbn_WNL">WNL</label>
	</td>
	<td colspan="2"><input type="checkbox" id="elem_npaWnlAbn_Abn" name="elem_npaWnlAbn" value="Abn" 
	<?php if($elem_npaWnlAbn=="Abn") echo 'checked';?>  class="text" onClick="checkYFlagP(this);npcCheckCb(this);">
	<label for="elem_npaWnlAbn_Abn">ABN</label>
	</td>
	<td colspan="4" class="form-inline"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this)" name="elem_npaCm" value="<?php echo $elem_npaCm ;?>"> cm</td>
	<td colspan="3">
	<input type="checkbox" id="elem_npaWnlAbn_na" name="elem_npaWnlAbn" value="N/A" 
	<?php if($elem_npaWnlAbn=="N/A") echo 'checked';?> onClick="checkYFlagP(this);npcCheckCb(this);">
	  <label for="elem_npaWnlAbn_na">N/A</label>
	</td>
	<td>Comments</td>
	<td><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this);"  name="npa_desc" value="<?php echo $npa_desc;?>" id="npa_desc"></td>


	</tr>
	<tr>
	<td><strong>EOM</strong></td>
	<td>
	<input type="checkbox" id="elem_eomFull"  name="elem_eomFull" value="1" 
	<?php if(($eom_full==1)){ echo 'checked';}?> onClick="checkYFlagP(this);" >
	<label for="elem_eomFull">Full</label>
	</td>
	<td colspan="6"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this);" name="full_desc" id="full_desc" value="<?php echo $full_desc;?>" ></td>
	<td colspan="3">
	<input type="checkbox" id="elem_eomOrtho" name="elem_eomOrtho" value="1" <?php //elementDefaultValue="1" ?> <?php if(($eom_ortho==1)){ echo 'checked';}?> 
	onClick="checkYFlagP(this);" >
	<label for="elem_eomOrtho">Ortho</label>
	</td>
	<td colspan="2"></td>
	</tr>
	<tr>
	<td><strong>Abn (Abnormal)</strong></td>
	<td>
	<input type="checkbox" id="rd_eom_abn_right" <?php if($eom_abn_right_left_alter=='Right'){ echo 'checked';}?> 
	name="elem_eomAbnRightLeftAlter_check" value="Right" onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_abn_right">Right</label>
	</td>
	<td width="4%">
	<input type="checkbox" id="rd_eom_abn_left" name="elem_eomAbnRightLeftAlter_check2" <?php if($eom_abn_right_left_alter=='Left'){ echo 'checked';}?> 
	value="Left" onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_abn_left">Left</label>
	</td>
	<td width="7%">
	<input type="checkbox" id="rd_eom_abn_alter" name="elem_eomAbnRightLeftAlter_check3" value="Alternate"  
	<?php if($eom_abn_right_left_alter=='Alternate'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_abn_alter">Alternate</label>
	</td>
	<td colspan="4">&nbsp;</td>
	<td width="4%">
	<input type="checkbox" id= "rd_eom_abn_near" name="elem_eomAbnNearFarBoth_check" value="Near" 
	<?php if($eom_abn_near_far_both=='Near'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_abn_near">Near</label>
	</td>
	<td width="3%">
	<input type="checkbox" id= "rd_eom_abn_far" name="elem_eomAbnNearFarBoth_check2" value="Far" 
	<?php if($eom_abn_near_far_both=='Far'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_abn_far">Far</label>
	</td>
	<td width="4%">
	<input type="checkbox" id= "rd_eom_abn_both" name="elem_eomAbnNearFarBoth_check3" value="Both"  
	<?php if($eom_abn_near_far_both=='Both'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_abn_both">Both</label>
	</td>
	<td colspan="2"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this);" name="elem_eomAbnDesc" value="<?php echo $eom_abn_desc ;?>"></td>
	</tr>
	<tr>
	<td><strong>Horizontal</strong></td>
	<td>
	<input type="checkbox" id= "rd_eom_hori_eso" name="elem_eomHoriEsoExo_check2" value="ESO" 
	<?php if($eom_hori_eso_exo=='ESO'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_hori_eso">ESO</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_hori_exo"  name="elem_eomHoriEsoExo_check" value="EXO" 
	<?php if($eom_hori_eso_exo=='EXO'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_hori_exo">EXO</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_hori_trophia" name="elem_eomHoriTrophiaPhoria_check2" value="Tropia"  
	<?php if($eom_hori_trophia_phoria=='Tropia'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);" >
	<label for="rd_eom_hori_trophia">Tropia</label>
	</td>
	<td colspan="4">
	<input type="checkbox" id= "rd_eom_hori_phoria" name="elem_eomHoriTrophiaPhoria_check" value="Phoria" 
	<?php if($eom_hori_trophia_phoria=='Phoria'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_hori_phoria">Phoria</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_hori_near" name="elem_eomHoriNearFarBoth_check3" value="Near" 
	<?php if($eom_hori_near_far_both=='Near'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_hori_near">Near</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_hori_far" name="elem_eomHoriNearFarBoth_check2" value="Far" 
	<?php if($eom_hori_near_far_both=='Far'){ echo 'checked';}?>  onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_hori_far">Far</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_hori_both" name="elem_eomHoriNearFarBoth_check" value="Both" 
	<?php if($eom_hori_near_far_both=='Both'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_hori_both">Both</label>
	</td>
	<td colspan="2"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this);" name="elem_eomHoriDesc" value="<?php echo $eom_hori_desc;?>"></td>
	</tr>
	<tr>
	<td><strong>Vertical </strong></td>
	<td>
	<input type="checkbox" id= "rd_eom_verti_hyper" name="elem_eomVertiHyperHypo_check" value="Hyper" 
	<?php if($eom_verti_hyper_hypo=='Hyper'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_hyper">Hyper</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_verti_hypo" name="elem_eomVertiHyperHypo_check2" value="Hypo" 
	<?php if($eom_verti_hyper_hypo=='Hypo'){ echo 'checked';}?>  onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_hypo">Hypo</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_verti_trophia" name="elem_eomVertiTrophiaPhoria_check" value="Tropia" 
	<?php if($eom_verti_trophia_phoria =='Tropia'){ echo 'checked';}?>  onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_trophia">Tropia</label>
	</td>
	<td colspan="4">
	<input type="checkbox" id= "rd_eom_verti_phoria" name="elem_eomVertiTrophiaPhoria_check2" value="Phoria" 
	<?php if($eom_verti_trophia_phoria =='Phoria'){ echo 'checked';}?>  onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_phoria">Phoria</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_verti_near" name="elem_eomVertiNearFarBoth_check" value="Near" 
	<?php if($eom_verti_near_far_both=='Near'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_near">Near</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_verti_far" name="elem_eomVertiNearFarBoth_check2" value="Far" 
	<?php if($eom_verti_near_far_both=='Far'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_far">Far</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_verti_both" name="elem_eomVertiNearFarBoth_check3" value="Both" 
	<?php if($eom_verti_near_far_both  =='Both'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_verti_both">Both</label>
	</td>
	<td colspan="2"><input type="text" class="form-control" placeholder="Text input" onBlur="checkYFlagP(this);" name="elem_eomVertiDesc" value="<?php echo $eom_verti_desc ;?>"></td>
	</tr>
	<tr class="grp_av_patterns">
	<td><strong>AV Patterns</strong></td>
	<td>
	<input type="checkbox" id= "rd_eom_avp_aeso" name="elem_eomAvpAesoAexo_check" value="A ESO" 
	<?php if($elem_eomAvpAesoAexo  =='A ESO'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_avp_aeso">A ESO</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_avp_aexo" name="elem_eomAvpAesoAexo_check2" value="A EXO" 
	<?php if($elem_eomAvpAesoAexo  =='A EXO'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_avp_aexo">A EXO</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_avp_veso" name="elem_eomAvpAesoAexo_check3" value="V ESO" 
	<?php if($elem_eomAvpAesoAexo  =='V ESO'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_avp_veso">V ESO</label>
	</td>
	<td>
	<input type="checkbox" id= "rd_eom_avp_vexo" name="elem_eomAvpAesoAexo_check4" value="V EXO" 
	<?php if($elem_eomAvpAesoAexo  =='V EXO'){ echo 'checked';}?> onClick="checkYFlagP(this);checkFunction(this);">
	<label for="rd_eom_avp_vexo">V EXO</label>
	</td>
	<td colspan="3" align="center">&nbsp;</td>
	<td colspan="5" class="form-inline">&nbsp;</td>
	</tr>
	<tr class="grp_av_patterns">
	<td><strong></strong></td>
	<td class="text-nowrap">
	<input type="checkbox" id= "elem_eomControl_poorcontrol" name="elem_eomControl[]" value="Poor Control" 
	<?php if(strpos($elem_eomControl,'Poor Control')!==false){ echo 'checked';}?> onClick="checkFunction(this);checkYFlagP(this);">
	<label for="elem_eomControl_poorcontrol">Poor Control</label>	
	</td>
	<td class="text-nowrap">
	<input type="checkbox" id= "elem_eomControl_faircontrol" name="elem_eomControl[]" value="Fair Control" 
	<?php if(strpos($elem_eomControl,'Fair Control')!==false){ echo 'checked';}?> onClick="checkFunction(this);checkYFlagP(this);">
	<label for="elem_eomControl_faircontrol">Fair Control</label>
	</td>
	<td class="text-nowrap">
	<input type="checkbox" id= "elem_eomControl_goodcontrol" name="elem_eomControl[]" value="Good Control" 
	<?php if(strpos($elem_eomControl,'Good Control')!==false){ echo 'checked';}?> onClick="checkFunction(this);checkYFlagP(this);">
	<label for="elem_eomControl_goodcontrol">Good Control</label>
	</td>
	<td class="text-nowrap">
	<input type="checkbox" id= "elem_eomControl_excelcontrol"  name="elem_eomControl[]" value="Excellent Control" 
	<?php if(strpos($elem_eomControl,'Excellent Control')!==false){ echo 'checked';}?> onClick="checkFunction(this);checkYFlagP(this);">
	<label for="elem_eomControl_excelcontrol">Excellent Control</label>
	</td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	<td colspan="5" ></td>
	</tr>
	<tr class="grp_ran_st">
	<td><strong>Randot Stereo Test</strong></td>
	<td><input type="checkbox" id="elem_ranSt_nofly"  name="elem_ranSt[]" value="No Fly" <?php if(strpos($elem_ranSt,"No Fly")!==false) echo "checked" ;?> onClick="checkFunction(this);checkYFlagP(this);"><label for="elem_ranSt_nofly">No Fly</label> </td>
	<td>
	<input type="checkbox" id="elem_ranSt_fly" name="elem_ranSt[]" value="Fly" <?php if(strpos($elem_ranSt,"No Fly")===false && strpos($elem_ranSt,"Fly")!==false) echo "checked" ;?> onClick="checkFunction(this);checkYFlagP(this);"><label for="elem_ranSt_fly">Fly</label> 
	</td>
	<td>
	<input type="checkbox" id="elem_ranSt_nobutterfly"  name="elem_ranSt[]" value="No Butterfly" <?php if(strpos($elem_ranSt,"No Butterfly")!==false) echo "checked" ;?> onClick="checkFunction(this);checkYFlagP(this);"><label for="elem_ranSt_nobutterfly">No Butterfly</label>
	</td>
	<td>
	<input type="checkbox" id="elem_ranSt_butterfly" name="elem_ranSt[]" value="Butterfly" <?php if(strpos($elem_ranSt,"No Butterfly")===false && strpos($elem_ranSt,"Butterfly")!==false) echo "checked" ;?> onClick="checkFunction(this);checkYFlagP(this);"><label for="elem_ranSt_butterfly">Butterfly</label>
	</td>
	<td align="center">
	<input type="checkbox" id="elem_ranSt_a" name="elem_ranSt[]" value="A" <?php if(strpos($elem_ranSt,"A")!==false) echo "checked" ;?> onClick="checkYFlagP(this);"><label for="elem_ranSt_a">A</label>
	</td>
	<td align="center">
	<input type="checkbox" id="elem_ranSt_b" name="elem_ranSt[]" value="B" <?php if(strpos($elem_ranSt,"B,")!==false || strpos($elem_ranSt,", B")!==false || $elem_ranSt=="B") echo "checked" ;?> onClick="checkYFlagP(this);"><label for="elem_ranSt_b">B</label>
	</td>
	<td align="center">
	<input type="checkbox" id="elem_ranSt_c" name="elem_ranSt[]" value="C" <?php if(strpos($elem_ranSt,"C")!==false) echo "checked" ;?> onClick="checkYFlagP(this);"><label for="elem_ranSt_c">C</label>
	</td>
	<td colspan="5" class="form-inline">
	<input type="text" name="elem_ranSt_Dots9" value="<?php echo $elem_ranSt_Dots9;?>" size="3" onblur="checkYFlagP(this);"> <label>Dots/9</label>        
	<input type="text" name="elem_stereo_SecondsArc" value="<?php echo $elem_stereo_SecondsArc;?>" size="3" onblur="checkYFlagP(this);"> <label>Seconds of arc</label>
	</td>
	</tr>    
	</table>    
	</div>
	<div class="clearfix"></div>
	
	 <!-- Grids  -->
	<div id="Grids">	
	<?php echo $data_grid;?>
	<input type="hidden" name="elem_counterGrid" value="<?php echo $counterGrid;?>">
	</div>	
	<!-- Grids  -->
	
	<div class="row">
<!-- Color Vision Test  -->
	<div id="divcolorvis" class="col-sm-6">
	<div class="exambox" >
	<div class="head">
	  <h2>Color Vision Test</h2>
	</div>
	<div class="clearfix"></div>
	<div class="pd10">
	<div class="row form-inline">
		<label>Control</label>
		<select name="elem_color_sign_od" onblur="checkYFlagP(this);" class="form-control">
		<option value=""></option>
		<option value="+" <?php if ($elem_color_sign_od=="+"){ echo "selected";} ?> >+</option>
		<option value="-" <?php if ($elem_color_sign_od=="-") {echo "selected";} ?> >-</option>
		</select>
		<label class="od">OD</label>
		<input type="text" name="elem_color_od_1" value="<?php echo $elem_color_od_1; ?>" onblur="checkYFlagP(this);" class="form-control" > /
		<input type="text" name="elem_color_od_2" value="<?php echo $elem_color_od_2; ?>" onblur="checkYFlagP(this);" class="form-control" >
	</div>
	<div class="row form-inline">
		<label>Control</label>
		<select name="elem_color_sign_os" onblur="checkYFlagP(this);" class="form-control">
		<option value=""></option>
		<option value="+" <?php if ($elem_color_sign_os=="+") {echo "selected";} ?> >+</option>
		<option value="-" <?php if ($elem_color_sign_os=="-") {echo "selected";} ?> >-</option>
		</select>
		<label class="os">OS</label>
		<input type="text" name="elem_color_os_1" value="<?php echo $elem_color_os_1; ?>" onblur="checkYFlagP(this);" class="form-control" > /
		<input type="text" name="elem_color_os_2" value="<?php echo $elem_color_os_2; ?>" onblur="checkYFlagP(this);" class="form-control" >
	</div>
	<div class="row form-inline">
		<label>Control</label>
		<select name="elem_color_sign_ou" onblur="checkYFlagP(this);" class="form-control">
		<option value=""></option>
		<option value="+" <?php if ($elem_color_sign_ou=="+") {echo "selected";} ?> >+</option>
		<option value="-" <?php if ($elem_color_sign_ou=="-") {echo "selected";} ?> >-</option>
		</select>
		<label class="ou">OU</label>
		<input type="text" name="elem_color_ou_1" value="<?php echo $elem_color_ou_1; ?>" onblur="checkYFlagP(this);" class="form-control" > /
		<input type="text" name="elem_color_ou_2" value="<?php echo $elem_color_ou_2; ?>" onblur="checkYFlagP(this);" class="form-control" >
	</div>
	<div class="examcomt"><textarea name="elem_comm_colorVis" onblur="checkYFlagP(this);" class="form-control" ><?php echo $elem_comm_colorVis; ?></textarea></div>
	</div>
	</div>
	</div>
<!-- Color Vision Test -->
<!-- w4Dot -->
	<div id="divw4dot" class="col-sm-6" >
	<div class="exambox" >
	<div class="head">
	  <h2>Worth 4 Dot Test</h2>
	</div>
	<div class="clearfix"></div>
	<div class="pd10">
	<div class="row form-inline">
	<label>Distance</label>
	<div class="input-group">
	<input type="text" name="elem_w4dot_distance" id="elem_w4dot_distance" value="<?php echo $elem_w4dot_distance; ?>" onblur="checkYFlagP(this);" class="form-control">
	<?php echo $menu_w4dot_distance; ?>
	</div>

	<label>Near</label>
	<div class="input-group">
	<input type="text" name="elem_w4dot_near" id="elem_w4dot_near" value="<?php echo $elem_w4dot_near;?>" onblur="checkYFlagP(this);" class="form-control">
	<?php echo $menu_w4dot_near; ?>
	</div>
	</div>
	<div class="examcomt"><textarea name="elem_comm_w4Dot" onblur="checkYFlagP(this);" class="form-control"><?php echo $elem_comm_w4Dot;?></textarea></div>
	</div>
	</div>
	</div>
<!--  w4Dot -->
	</div>
	
	<!--  Ductions -->
	<div id="divDuction" class="row" >
	<?php echo $data_duction; ?>
	</div>
	<!--  Ductions -->
	
	<!--  Anomalous Head Position No -->
	<div class="row">
		<div id="divAnoHead" class="col-sm-6" >
			<div class="exambox" >
				<div class="head">
					<div class="row">
					<div class="col-sm-5">
					<h2>Anomalous Head Position</h2>
					</div>
					<div class="col-sm-7">
						<input type="checkbox" id="elem_ahp_no" name="elem_ahp_no" value="1" <?php if ($elem_ahp_no==1) {echo "checked";} ?> onclick="checkYFlagP(this);">  
						<label for="elem_ahp_no">No</label>
					</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="pd10">
					<div class="row"><textarea onBlur="checkYFlagP(this);" name="elem_comments_AnoHead" class="form-control" ><?php echo $elem_comments_AnoHead;?></textarea></div>
				</div>
			</div>
		</div>
		<!-- Start NystagmusTable -->
		<div id="divNystag" class="col-sm-6" >
			<div class="exambox" >
				<div class="head">
					<div class="row">
					<div class="col-sm-3">
					<h2>Nystagmus</h2>
					</div>
					<div class="col-sm-8">
						<input type="checkbox" id="elem_nysta_no" name="elem_nysta_no" value="1" <?php if ($elem_nysta_no==1) {echo "checked";} ?> onclick="checkYFlagP(this);">  
						<label for="elem_nysta_no">No</label>
					</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="pd10">
					<div class="row"><textarea onBlur="checkYFlagP(this);" name="elem_comments_Nystag" class="form-control"  ><?php echo $elem_comments_Nystag;?></textarea></div>
				</div>
			</div>
		</div>		
	</div>
	<!--  General Comments --> 
	<div class="row">
		<div id="divGenComm" class="col-sm-12" >
			<div class="exambox" >
				<div class="head">
					<h2>General Comments</h2>
				</div>
				<div class="clearfix"></div>
				<div class="pd10">
					<div class="row"><textarea onBlur="checkYFlagP(this);" name="elem_comments_gen" class="form-control" ><?php echo $elem_comments_gen;?></textarea></div>
				</div>
			</div>
		</div>
	</div> 	
    </div>
    <div role="tabpanel" class="tab-pane" id="divEom3">
    
    
	<!-- Start Draw Table -->
	<div class="subExam <?php echo ($elem_editMode==0 || $elem_chng_divEom3==0) ? "greyAll" : "";?>">
		<div class="examhd">
	
			<?php if($finalize_flag == 1){?>
			<label class="chart_status label label-danger pull-left">Finalized</label>
			<?php }?>
			
			<span id="examFlag" class="glyphicon flagWnl "></span>
			<!--<img src="<?php //echo $GLOBALS['webroot'];?>/library/images/flag_yellow.png" alt=""/>--> 
			
			<button class="wnl_btn" type="button" onClick="setwnl();">WNL</button>
			
			<input type="checkbox" id="elem_noChange_draw"  name="elem_noChange_draw" value="1" onClick="setNC2();" 
					<?php echo ($examined_no_change3 == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
			<label class="lbl_nochange frcb" for="elem_noChange_draw">NO Change</label>			
			
			<?php /*if (constant('AV_MODULE')=='YES'){?>
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
			<?php }*/?>
		
		</div>
		<div class="clearfix"> </div>
		<div class="row">
			<div class="col-sm-2">
				<textarea onBlur="checkYFlagP(this)" name="el_eom_od" class="form-control drw_text_box"  ><?php echo $desc_draw_od;?></textarea>
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
					//Multi Drawing Start
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
					if(empty($dbTollImage) == true){	$dbTollImage = "imgEOMCanvas";	}							
					$stDisp = "hidden";
					if($intTempDrawCount == 0){  $stDisp = ""; }
					?>
						 <div id="divDrawing<?php echo $intTempDrawCount; ?>" class="row <?php echo $stDisp; ?>" >                            
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
							<input type="hidden" name="hidEOMDrawingId<?php echo $intTempDrawCount; ?>" id="hidEOMDrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
							<input type="hidden" name="hidDone<?php echo $intTempDrawCount; ?>" id="hidDone<?php echo $intTempDrawCount; ?>" >
							<input type="hidden" id="hidDrwDataJson<?php echo $intTempDrawCount; ?>" name="hidDrwDataJson<?php echo $intTempDrawCount; ?>" >
							<?php
								if($dbdrawID > 0){ echo "<input type=\"hidden\" name=\"hidLoad".$dbdrawID."\" id=\"hidLoad".$dbdrawID."\" >"; }								
								include(dirname(__FILE__)."/drawing_new.php");
							?>
						</div>	

					<?php
					}
					?>
				<?php
				}
				?>				
			</div>
			<div class="col-sm-2">
				<textarea onBlur="checkYFlagP(this)" name="el_eom_os" class="form-control drw_text_box" ><?php echo $desc_draw_os;?></textarea> 
			</div>
		</div>
	</div>
	<!-- Start Draw Table -->
    
    </div>
  </div>

</div>



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

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>
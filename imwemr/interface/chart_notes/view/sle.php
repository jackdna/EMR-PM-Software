<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->
	
<!-- Bootstrap -->
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvexmcss" rel="stylesheet">
<style>
.mfcss{display:none;}
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
	var examName = "SLE";
	var arrSubExams = new Array("Conj","Corn","Ant","Iris","Lens","Draw");	
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
<form name="frmTest" id="frmTest" action="saveCharts.php" method="post"  onSubmit="freezeElemAll('0');" enctype="multipart/form-data" class="frcb">
<input type="hidden" name="elem_saveForm" value="sletable1">
<input type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
<input type="hidden" name="elem_sleId" value="<?php echo $elem_sleId;?>">
<input type="hidden" name="elem_sleId_LF" value="<?php echo $elem_sleId_LF;?>">
<input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
<input type="hidden" name="elem_patientId" value="<?php echo $elem_patientId;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
<input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="patient_not_driving" value="<?php echo $patient_not_driving;?>">
<input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">	
<input type="hidden" name="elem_wnlConj" value="<?php echo $elem_wnlConj;?>">
<input type="hidden" name="elem_wnlCorn" value="<?php echo $elem_wnlCorn;?>">
<input type="hidden" name="elem_wnlAnt" value="<?php echo $elem_wnlAnt;?>">
<input type="hidden" name="elem_wnlIris" value="<?php echo $elem_wnlIris;?>">
<input type="hidden" name="elem_wnlLens" value="<?php echo $elem_wnlLens;?>">
<input type="hidden" name="elem_wnlDraw" value="<?php echo $elem_wnlDraw;?>">
<input type="hidden" name="elem_posConj" value="<?php echo $elem_posConj;?>">
<input type="hidden" name="elem_posCorn" value="<?php echo $elem_posCorn;?>">
<input type="hidden" name="elem_posAnt" value="<?php echo $elem_posAnt;?>">
<input type="hidden" name="elem_posIris" value="<?php echo $elem_posIris;?>">
<input type="hidden" name="elem_posLens" value="<?php echo $elem_posLens;?>">
<input type="hidden" name="elem_posDraw" value="<?php echo $elem_posDraw;?>">
<input type="hidden" name="elem_ncConj" value="<?php echo $elem_ncConj;?>">
<input type="hidden" name="elem_ncCorn" value="<?php echo $elem_ncCorn;?>">
<input type="hidden" name="elem_ncAnt" value="<?php echo $elem_ncAnt;?>">
<input type="hidden" name="elem_ncIris" value="<?php echo $elem_ncIris;?>">
<input type="hidden" name="elem_ncLens" value="<?php echo $elem_ncLens;?>">
<input type="hidden" name="elem_ncDraw" value="<?php echo $elem_ncDraw;?>">
<input type="hidden" name="elem_examined_no_change" value="<?php echo $elem_noChange;?>">
<input type="hidden" name="elem_wnlConjOd" value="<?php echo $elem_wnlConjOd;?>">
<input type="hidden" name="elem_wnlConjOs" value="<?php echo $elem_wnlConjOs;?>">
<input type="hidden" name="elem_wnlCornOd" value="<?php echo $elem_wnlCornOd;?>">
<input type="hidden" name="elem_wnlCornOs" value="<?php echo $elem_wnlCornOs;?>">
<input type="hidden" name="elem_wnlAntOd" value="<?php echo $elem_wnlAntOd;?>">
<input type="hidden" name="elem_wnlAntOs" value="<?php echo $elem_wnlAntOs;?>">
<input type="hidden" name="elem_wnlIrisOd" value="<?php echo $elem_wnlIrisOd;?>">
<input type="hidden" name="elem_wnlIrisOs" value="<?php echo $elem_wnlIrisOs;?>">
<input type="hidden" name="elem_wnlLensOd" value="<?php echo $elem_wnlLensOd;?>">
<input type="hidden" name="elem_wnlLensOs" value="<?php echo $elem_wnlLensOs;?>">
<input type="hidden" name="elem_wnlDrawOd" value="<?php echo $elem_wnlDrawOd;?>">
<input type="hidden" name="elem_wnlDrawOs" value="<?php echo $elem_wnlDrawOs;?>">
<input type="hidden" name="elem_descSle" value="<?php echo $elem_descSle;?>">
<input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
<!--<input type="hidden" name="hidSLEDrawingId" id="hidSLEDrawingId" value="<?php //echo $dbIdocDrawingId;?>">-->
<input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">

<input type="hidden" id="elem_utElemsConj" name="elem_utElemsConj" value="<?php echo $elem_utElemsConj;?>">
<input type="hidden" id="elem_utElemsConj_cur" name="elem_utElemsConj_cur" value="<?php echo $elem_utElemsConj_cur;?>">
<input type="hidden" id="elem_utElemsCorn" name="elem_utElemsCorn" value="<?php echo $elem_utElemsCorn;?>">
<input type="hidden" id="elem_utElemsCorn_cur" name="elem_utElemsCorn_cur" value="<?php echo $elem_utElemsCorn_cur;?>">
<input type="hidden" id="elem_utElemsAnt" name="elem_utElemsAnt" value="<?php echo $elem_utElemsAnt;?>">
<input type="hidden" id="elem_utElemsAnt_cur" name="elem_utElemsAnt_cur" value="<?php echo $elem_utElemsAnt_cur;?>">
<input type="hidden" id="elem_utElemsIris" name="elem_utElemsIris" value="<?php echo $elem_utElemsIris;?>">
<input type="hidden" id="elem_utElemsIris_cur" name="elem_utElemsIris_cur" value="<?php echo $elem_utElemsIris_cur;?>">
<input type="hidden" id="elem_utElemsLens" name="elem_utElemsLens" value="<?php echo $elem_utElemsLens;?>">
<input type="hidden" id="elem_utElemsLens_cur" name="elem_utElemsLens_cur" value="<?php echo $elem_utElemsLens_cur;?>">
<input type="hidden" id="elem_utElemsDraw" name="elem_utElemsDraw" value="<?php echo $elem_utElemsDraw;?>">
<input type="hidden" id="elem_utElemsDraw_cur" name="elem_utElemsDraw_cur" value="<?php echo $elem_utElemsDraw_cur;?>">

<input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
<input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">	
<!-- newET_changeIndctr -->
<?php
    echo $elem_changeInd;
?>
<input type="hidden" id="el_strPtrnGray" name="el_strPtrnGray" value="<?php echo $strPtrnGray;?>">
<!-- newET_changeIndctr -->

<div class=" container-fluid">
<div class="whtbox exammain ">

<div class="clearfix"></div>
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
	<?php	
	foreach($arrTabs as $key => $val){
	    if($key == "1"){
                $tmp2="Conj";
            }else if($key == "2"){
                $tmp2="Corn";
            }else if($key == "3"){
                $tmp2="Ant";
            }else if($key == "6"){
                $tmp2="Draw";
            }else{
                $tmp2=$val;
            }
	   
	    //CheckTemplate setting --
	    
	    $chkTemp=$val;
	    if($chkTemp=="Iris"){ $chkTemp="Iris & Pupil"; 
	    }else if($chkTemp=="Drawing"){ $chkTemp="DrawSLE"; }
	    
	    if(isset($arrTempProc) && !in_array("All",$arrTempProc) && !in_array($chkTemp,$arrTempProc)){
		continue;
	    }
	    
	    //CheckTemplate setting --	
	    
		$tmp = ($key == $defTabKey) ? "active" : "";
	?>
	<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab" onclick="changeTab('<?php echo $key;?>')" id="tab<?php echo $key;?>" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

<!-- tbar -->
<div class="row exm_title_bar">
	<div class="col-sm-1">
		<?php if($finalize_flag == 1){?>
		<label class="chart_status label label-danger pull-left">Finalized</label>
		<?php }?>	
	</div>
	<div class="col-sm-2 pharmo mt5">
	<!-- Pen Light -->
	<input type="checkbox" id="elem_penLight" name="elem_penLight" value="1" <?php echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
	<label id="lblPenLight" for="elem_penLight"><strong>Pen Light</strong></label>
	<!-- Pen Light -->
	</div>
</div>
<!-- tbar -->

  <!-- Tab panes -->
  <div class="tab-content">	
	<div role="tabpanel" class="tab-pane <?php echo (1 == $defTabKey) ? "active" : "" ?>" id="div1">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1">
				
			</div>
			<div class="col-sm-2 pharmo mt5">
				<!-- Pen Light --*>					
				<input type="checkbox" id="elem_penLightConj" name="elem_penLightConj" value="1" <?php //echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
				<label id="lblPenLight" for="elem_penLightConj"><strong>Pen Light</strong></label>				
				<!-- Pen Light -->
			</div>    
			<div class="col-sm-9"> 
				<span id="examFlag" class="glyphicon flagWnl "></span>
		
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncConj == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChange">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
			</div>    
		</div>
	</div>    
	<div class="clearfix"> </div>

	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="9" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		</td>
		<td width="100" align="center" class="bilat bilat_all" onClick="check_bilateral()" ><strong>Bilateral</strong></td>
		<td colspan="9" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>
		
		<tr id="d_ConjChal">
		<td align="left">Conjunctival Chalasis</td>
		<td>
		<input id="od_281" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_neg" value="Absent" <?php echo ($elem_ConjnctivalOd_neg=="-ve" || $elem_ConjnctivalOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_281"  >Absent</label>
		</td><td><input id="od_282" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_Pos" value="Present" <?php echo ($elem_ConjnctivalOd_Pos=="+ve" || $elem_ConjnctivalOd_Pos=="Present") ? "checked" : ""; ?>><label for="od_282"  >Present</label>
		</td><td><input id="od_283" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_T" value="T" <?php echo ($elem_ConjnctivalOd_T=="T") ? "checked" : ""; ?>><label for="od_283"  >T</label>
		</td><td><input id="od_284" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_pos1" value="1+" <?php echo ($elem_ConjnctivalOd_pos1=="+1" || $elem_ConjnctivalOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_284"  >1+</label>
		</td><td><input id="od_285" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_pos2" value="2+" <?php echo ($elem_ConjnctivalOd_pos2=="+2" || $elem_ConjnctivalOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_285"  >2+</label>
		</td><td><input id="od_286" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_pos3" value="3+" <?php echo ($elem_ConjnctivalOd_pos3=="+3" || $elem_ConjnctivalOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_286"  >3+</label>
		</td><td><input id="od_287" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOd_pos4" value="4+" <?php echo ($elem_ConjnctivalOd_pos4=="+4" || $elem_ConjnctivalOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_287"  >4+</label>
		</td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('ConjChal')">BL</td>
		<td align="left">Conjunctival Chalasis</td>
		<td>
		<input id="os_281" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_neg" value="Absent" <?php echo ($elem_ConjnctivalOs_neg=="-ve" || $elem_ConjnctivalOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_281"  >Absent</label>
		</td><td><input id="os_282" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_Pos" value="Present" <?php echo ($elem_ConjnctivalOs_Pos=="+ve" || $elem_ConjnctivalOs_Pos=="Present") ? "checked" : ""; ?>><label for="os_282"  >Present</label>
		</td><td><input id="os_283" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_T" value="T" <?php echo ($elem_ConjnctivalOs_T=="T") ? "checked" : ""; ?>><label for="os_283"  >T</label>
		</td><td><input id="os_284" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_pos1" value="1+" <?php echo ($elem_ConjnctivalOs_pos1=="+1" || $elem_ConjnctivalOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_284"  >1+</label>
		</td><td><input id="os_285" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_pos2" value="2+" <?php echo ($elem_ConjnctivalOs_pos2=="+2" || $elem_ConjnctivalOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_285"  >2+</label>
		</td><td><input id="os_286" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_pos3" value="3+" <?php echo ($elem_ConjnctivalOs_pos3=="+3" || $elem_ConjnctivalOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_286"  >3+</label>
		</td><td><input id="os_287" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctivalOs_pos4" value="4+" <?php echo ($elem_ConjnctivalOs_pos4=="+4" || $elem_ConjnctivalOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_287"  >4+</label>
		</td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Conjunctival Chalasis"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Conjunctival Chalasis"]; }  ?>
		
		<tr id="d_ConjSPK">
		<td align="left">Conjunctiva SPK</td>
		<td>
		<input id="od_288" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_neg" value="Absent" <?php echo ($elem_ConjnctSpkOd_neg=="-ve" || $elem_ConjnctSpkOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_288"  >Absent</label>
		</td><td><input id="od_289" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_Pos" value="Present" <?php echo ($elem_ConjnctSpkOd_Pos=="+ve" || $elem_ConjnctSpkOd_Pos=="Present") ? "checked" : ""; ?>><label for="od_289"  >Present</label>
		</td><td><input id="od_290" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_T" value="T" <?php echo ($elem_ConjnctSpkOd_T=="T") ? "checked" : ""; ?>><label for="od_290"  >T</label>
		</td><td><input id="od_291" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_pos1" value="1+" <?php echo ($elem_ConjnctSpkOd_pos1=="+1" || $elem_ConjnctSpkOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_291"  >1+</label>
		</td><td><input id="od_292" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_pos2" value="2+" <?php echo ($elem_ConjnctSpkOd_pos2=="+2" || $elem_ConjnctSpkOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_292"  >2+</label>
		</td><td><input id="od_293" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_pos3" value="3+" <?php echo ($elem_ConjnctSpkOd_pos3=="+3" || $elem_ConjnctSpkOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_293"  >3+</label>
		</td><td><input id="od_294" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOd_pos4" value="4+" <?php echo ($elem_ConjnctSpkOd_pos4=="+4" || $elem_ConjnctSpkOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_294"  >4+</label>
		</td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('ConjSPK')">BL</td>
		<td align="left">Conjunctiva SPK</td>
		<td>
		<input id="os_288" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_neg" value="Absent" <?php echo ($elem_ConjnctSpkOs_neg=="-ve" || $elem_ConjnctSpkOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_288"  >Absent</label>
		</td><td><input id="os_289" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_Pos" value="Present" <?php echo ($elem_ConjnctSpkOs_Pos=="+ve" || $elem_ConjnctSpkOs_Pos=="Present") ? "checked" : ""; ?>><label for="os_289"  >Present</label>
		</td><td><input id="os_290" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_T" value="T" <?php echo ($elem_ConjnctSpkOs_T=="T") ? "checked" : ""; ?>><label for="os_290"  >T</label>
		</td><td><input id="os_291" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_pos1" value="1+" <?php echo ($elem_ConjnctSpkOs_pos1=="+1" || $elem_ConjnctSpkOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_291"  >1+</label>
		</td><td><input id="os_292" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_pos2" value="2+" <?php echo ($elem_ConjnctSpkOs_pos2=="+2" || $elem_ConjnctSpkOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_292"  >2+</label>
		</td><td><input id="os_293" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_pos3" value="3+" <?php echo ($elem_ConjnctSpkOs_pos3=="+3" || $elem_ConjnctSpkOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_293"  >3+</label>
		</td><td><input id="os_294" type="checkbox"  onclick="checkAbsent(this)" name="elem_ConjnctSpkOs_pos4" value="4+" <?php echo ($elem_ConjnctSpkOs_pos4=="+4" || $elem_ConjnctSpkOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_294"  >4+</label>
		</td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Conjunctiva SPK"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Conjunctiva SPK"]; }  ?>
		
		<tr id="d_ConjNev">
		<td align="left">Nevus</td>
		<td>
		<input id="od_295" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOd_neg" value="Absent" <?php echo ($elem_NevusOd_neg=="-ve" || $elem_NevusOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_295"  >Absent</label>
		</td><td><input id="od_296" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOd_Pos" value="Present" <?php echo ($elem_NevusOd_Pos=="+ve" || $elem_NevusOd_Pos=="Present") ? "checked" : ""; ?>><label for="od_296"  >Present</label>
		</td><td><input id="od_297" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOd_Inferior" value="Inferior" <?php echo ($elem_NevusOd_Inferior=="Inferior") ? "checked" : ""; ?>><label for="od_297"   >Inferior</label>
		</td><td><input id="od_298" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOd_Superior" value="Superior" <?php echo ($elem_NevusOd_Superior=="Superior") ? "checked" : ""; ?>><label for="od_298"  >Superior</label>
		</td><td colspan="2"><input id="od_299" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOd_Temporal" value="Temporal" <?php echo ($elem_NevusOd_Temporal=="Temporal") ? "checked" : ""; ?>><label for="od_299"   >Temporal</label>
		</td><td><input id="od_300" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOd_Nasal" value="Nasal" <?php echo ($elem_NevusOd_Nasal=="Nasal") ? "checked" : ""; ?>><label for="od_300" >Nasal</label>
		</td>		
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('ConjNev')">BL</td>
		<td align="left">Nevus</td>
		<td>
		<input id="os_295" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOs_neg" value="Absent" <?php echo ($elem_NevusOs_neg=="-ve" || $elem_NevusOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_295"  >Absent</label>
		</td><td><input id="os_296" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOs_Pos" value="Present" <?php echo ($elem_NevusOs_Pos=="+ve" || $elem_NevusOs_Pos=="Present") ? "checked" : ""; ?>><label for="os_296"  >Present</label>
		</td><td><input id="os_297" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOs_Inferior" value="Inferior" <?php echo ($elem_NevusOs_Inferior=="Inferior") ? "checked" : ""; ?>><label for="os_297"   >Inferior</label>
		</td><td><input id="os_298" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOs_Superior" value="Superior" <?php echo ($elem_NevusOs_Superior=="Superior") ? "checked" : ""; ?>><label for="os_298"  >Superior</label>
		</td><td colspan="2"><input id="os_299" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOs_Temporal" value="Temporal" <?php echo ($elem_NevusOs_Temporal=="Temporal") ? "checked" : ""; ?>><label for="os_299"  >Temporal</label>
		</td><td><input id="os_300" type="checkbox"  onclick="checkAbsent(this)" name="elem_NevusOs_Nasal" value="Nasal" <?php echo ($elem_NevusOs_Nasal=="Nasal") ? "checked" : ""; ?>><label for="os_300"   >Nasal</label>
		</td>		
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Nevus"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Nevus"]; }  ?>
		
		<tr id="d_Injection" class="grp_Injection">
		<td align="left">Injection</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="od_1" name="elem_injectionOd_neg" value="Absent" <?php echo ($elem_injectionOd_neg=="-ve" || $elem_injectionOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_1"  >Absent</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_2" name="elem_injectionOd_T" value="T" <?php echo ($elem_injectionOd_T=="T") ? "checked" : ""; ?>><label for="od_2"  >T</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_3" name="elem_injectionOd_pos1" value="1+" <?php echo ($elem_injectionOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_3"  >1+</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_4" name="elem_injectionOd_pos2" value="2+" <?php echo ($elem_injectionOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_4"  >2+</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_5" name="elem_injectionOd_pos3" value="3+" <?php echo ($elem_injectionOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_5"  >3+</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_6" name="elem_injectionOd_pos4" value="4+" <?php echo ($elem_injectionOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_6"  >4+</label>
		</td>
		<td align="Left">&nbsp;</td>
		<td></td>
		<td rowspan="3" align="center" class="bilat" onClick="check_bl('Injection')">BL</td>
		<td  align="left">Injection</td>
		<td>
		<input id="os_1" type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_neg" value="Absent" <?php echo ($elem_injectionOs_neg=="-ve" || $elem_injectionOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_1"  >Absent</label>
		</td><td><input id="os_2"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_T" value="T" <?php echo ($elem_injectionOs_T=="T") ? "checked" : ""; ?>><label for="os_2"  >T</label>
		</td><td><input id="os_3"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_pos1" value="1+" <?php echo ($elem_injectionOs_pos1=="+1" || $elem_injectionOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_3"  >1+</label>
		</td><td><input id="os_4"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_pos2" value="2+" <?php echo ($elem_injectionOs_pos2=="+2" || $elem_injectionOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_4"  >2+</label>
		</td><td><input id="os_5"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_pos3" value="3+" <?php echo ($elem_injectionOs_pos3=="+3" || $elem_injectionOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_5"  >3+</label>
		</td><td><input id="os_6"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_pos4" value="4+" <?php echo ($elem_injectionOs_pos4=="+4" || $elem_injectionOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_6"  >4+</label>
		</td>
		<td align="Left">&nbsp;</td>
		<td></td>
		</tr>
		
		<tr id="d_Injection1" class="grp_Injection">
		<td align="left"></td>
		<td><input type="checkbox"  onclick="checkAbsent(this)" id="od_7" name="elem_injectionOd_nasal" value="Nasal" <?php echo ($elem_injectionOd_nasal=="Nasal") ? "checked" : ""; ?>><label for="od_7"  >Nas</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_8" name="elem_injectionOd_temporal" value="Temporal" <?php echo ($elem_injectionOd_temporal=="Temporal") ? "checked" : ""; ?>><label for="od_8"  >Temp</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_10" name="elem_injectionOd_superior" value="Superior" <?php echo ($elem_injectionOd_superior=="Superior") ? "checked" : ""; ?>><label for="od_10"  >Sup</label> 
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="od_11" name="elem_injectionOd_inferior" value="Inferior" <?php echo ($elem_injectionOd_inferior=="Inferior") ? "checked" : ""; ?>><label for="od_11"  >Inf</label>
		</td>
		<td align="Left">&nbsp;</td>
		<td align="Left">&nbsp;</td>
		<td align="Left">&nbsp;</td>
		<td></td>
		<td align="left"></td>
		<td><input id="os_7"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_nasal" value="Nasal" <?php echo ($elem_injectionOs_nasal=="Nasal") ? "checked" : ""; ?>><label for="os_7"  >Nas</label>
		</td><td><input id="os_8"  type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_temporal" value="Temporal" <?php echo ($elem_injectionOs_temporal=="Temporal") ? "checked" : ""; ?>><label for="os_8"  >Temp</label>
		</td><td><input id="os_10" type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_superior" value="Superior" <?php echo ($elem_injectionOs_superior=="Superior") ? "checked" : ""; ?>><label for="os_10"  >Sup</label> 
		</td><td><input id="os_11" type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_inferior" value="Inferior" <?php echo ($elem_injectionOs_inferior=="Inferior") ? "checked" : ""; ?>><label for="os_11"  >Inf</label> 
		</td>
		<td align="Left">&nbsp;</td>
		<td align="Left">&nbsp;</td>
		<td align="Left">&nbsp;</td>
		<td></td>
		</tr>
		
		<tr id="d_Injection2" class="grp_Injection">
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)" id="od_9" name="elem_injectionOd_diffuse" value="Diffuse" <?php echo ($elem_injectionOd_diffuse=="Diffuse") ? "checked" : ""; ?>><label for="od_9"   >Diffuse</label>											
		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)" id="od_12" name="elem_injectionOd_limbal" value="Limbal" <?php echo ($elem_injectionOd_limbal=="Limbal") ? "checked" : ""; ?>><label for="od_12"   >Limbal</label> 
		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)" id="od_13" name="elem_injectionOd_intrapalpebral" value="Interpalpebral" <?php echo (($elem_injectionOd_intrapalpebral=="Intrapalpebral") || ($elem_injectionOd_intrapalpebral=="Interpalpebral")) ? "checked" : ""; ?>><label for="od_13"   class="aato">Interpalpebral</label>
		</td>		
		
		<td align="left">&nbsp;</td>
		<td align="left"></td>
		<td colspan="2"><input id="os_9"  type="checkbox" onClick="checkAbsent(this)" name="elem_injectionOs_diffuse" value="Diffuse" <?php echo ($elem_injectionOs_diffuse=="Diffuse") ? "checked" : ""; ?>><label for="os_9"   >Diffuse</label>
		</td><td colspan="2"><input id="os_12" type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_limbal" value="Limbal" <?php echo ($elem_injectionOs_limbal=="Limbal") ? "checked" : ""; ?>><label for="os_12"   >Limbal</label> 
		</td><td colspan="3"><input id="os_13" type="checkbox"  onclick="checkAbsent(this)" name="elem_injectionOs_intrapalpebral" value="Interpalpebral" <?php echo (($elem_injectionOs_intrapalpebral=="Intrapalpebral") || ($elem_injectionOs_intrapalpebral=="Interpalpebral")) ? "checked" : ""; ?>><label for="os_13"   class="aato">Interpalpebral</label>
		</td>
		
		<td align="Left">&nbsp;</td>		
		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Injection"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Injection"]; }  ?>
		
		<tr id="d_mDis">
		<td align="left">Mucus Discharge</td>
		<td>
		<input id="od_14" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOd_neg" value="Absent" <?php echo ($elem_mucusDischargeOd_neg=="-ve" || $elem_mucusDischargeOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_14"  >Absent</label>
		</td><td><input id="od_15" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOd_T" value="T" <?php echo ($elem_mucusDischargeOd_T=="T") ? "checked" : ""; ?>><label for="od_15"  >T</label>
		</td><td><input id="od_16" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOd_pos1" value="1+" <?php echo ($elem_mucusDischargeOd_pos1=="+1" || $elem_mucusDischargeOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_16"  >1+</label>
		</td><td><input id="od_17" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOd_pos2" value="2+" <?php echo ($elem_mucusDischargeOd_pos2=="+2" || $elem_mucusDischargeOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_17"  >2+</label>
		</td><td><input id="od_18" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOd_pos3" value="3+" <?php echo ($elem_mucusDischargeOd_pos3=="+3" || $elem_mucusDischargeOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_18"  >3+</label>
		</td><td><input id="od_19" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOd_pos4" value="4+" <?php echo ($elem_mucusDischargeOd_pos4=="+4" || $elem_mucusDischargeOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_19"  >4+</label>
		</td>
		<td align="Left">&nbsp;</td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('mDis')">BL</td>
		<td align="left">Mucus Discharge</td>
		<td>
		<input id="os_14" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOs_neg" value="Absent" <?php echo ($elem_mucusDischargeOs_neg=="-ve" || $elem_mucusDischargeOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_14"  >Absent</label>
		</td><td><input id="os_15" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOs_T" value="T" <?php echo ($elem_mucusDischargeOs_T=="T") ? "checked" : ""; ?>><label for="os_15"  >T</label>
		</td><td><input id="os_16" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOs_pos1" value="1+" <?php echo ($elem_mucusDischargeOs_pos1=="+1" || $elem_mucusDischargeOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_16"  >1+</label>
		</td><td><input id="os_17" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOs_pos2" value="2+" <?php echo ($elem_mucusDischargeOs_pos2=="+2" || $elem_mucusDischargeOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_17"  >2+</label>
		</td><td><input id="os_18" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOs_pos3" value="3+" <?php echo ($elem_mucusDischargeOs_pos3=="+3" || $elem_mucusDischargeOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_18"  >3+</label>
		</td><td><input id="os_19" type="checkbox"  onclick="checkAbsent(this)" name="elem_mucusDischargeOs_pos4" value="4+" <?php echo ($elem_mucusDischargeOs_pos4=="+4" || $elem_mucusDischargeOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_19"  >4+</label>
		</td>
		<td align="Left">&nbsp;</td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Mucus Discharge"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Mucus Discharge"]; }  ?>
		
		<tr id="d_Follic">
		<td align="left">Follicles</td>
		<td>
		<input id="od_20" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_neg" value="Absent" <?php echo ($elem_folliclesOd_neg=="-ve" || $elem_folliclesOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_20"  >Absent</label>
		</td><td><input id="od_21" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_T" value="T" <?php echo ($elem_folliclesOd_T=="T") ? "checked" : ""; ?>><label for="od_21"  >T</label>
		</td><td><input id="od_22" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_pos1" value="1+" <?php echo ($elem_folliclesOd_pos1=="+1" || $elem_folliclesOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_22"  >1+</label>
		</td><td><input id="od_23" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_pos2" value="2+" <?php echo ($elem_folliclesOd_pos2=="+2" || $elem_folliclesOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_23"  >2+</label>
		</td><td><input id="od_24" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_pos3" value="3+" <?php echo ($elem_folliclesOd_pos3=="+3" || $elem_folliclesOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_24"  >3+</label>
		</td><td><input id="od_25" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_pos4" value="4+" <?php echo ($elem_folliclesOd_pos4=="+4" || $elem_folliclesOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_25"  >4+</label>
		</td><td><input id="od_26" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_rul" value="RUL" <?php echo ($elem_folliclesOd_rul=="RUL") ? "checked" : ""; ?>><label for="od_26"  >RUL</label>
		</td><td><input id="od_27" type="checkbox"  onclick="checkAbsent(this)" name="elem_folliclesOd_rll" value="RLL" <?php echo ($elem_folliclesOd_rll=="RLL") ? "checked" : ""; ?>><label for="od_27"  >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Follic')">BL</td>
		<td align="left">Follicles</td>
		<td>
		<input id="os_20" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_neg" value="Absent" <?php echo ($elem_folliclesOs_neg=="-ve" || $elem_folliclesOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_20"  >Absent</label>
		</td><td><input id="os_21" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_T" value="T" <?php echo ($elem_folliclesOs_T=="T") ? "checked" : ""; ?>><label for="os_21"  >T</label>
		</td><td><input id="os_22" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_pos1" value="1+" <?php echo ($elem_folliclesOs_pos1=="+1" || $elem_folliclesOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_22"  >1+</label>
		</td><td><input id="os_23" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_pos2" value="2+" <?php echo ($elem_folliclesOs_pos2=="+2" || $elem_folliclesOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_23"  >2+</label>
		</td><td><input id="os_24" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_pos3" value="3+" <?php echo ($elem_folliclesOs_pos3=="+3" || $elem_folliclesOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_24"  >3+</label>
		</td><td><input id="os_25" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_pos4" value="4+" <?php echo ($elem_folliclesOs_pos4=="+4" || $elem_folliclesOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_25"  >4+</label>
		</td><td><input id="os_26" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_rul" value="LUL" <?php echo ($elem_folliclesOs_rul=="LUL") ? "checked" : ""; ?>><label for="os_26"  >LUL</label>
		</td><td><input id="os_27" type="checkbox" onClick="checkAbsent(this)" name="elem_folliclesOs_rll" value="LLL" <?php echo ($elem_folliclesOs_rll=="LLL") ? "checked" : ""; ?>><label for="os_27"  >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Follicles"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Follicles"]; }  ?>
		
		<tr id="d_Papillae">
		<td align="left">Papillae</td>
		<td>
		<input id="od_28" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_neg" value="Absent" <?php echo ($elem_papillaeOd_neg=="-ve" || $elem_papillaeOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_28"  >Absent</label>
		</td><td><input id="od_29" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_T" value="T" <?php echo ($elem_papillaeOd_T=="T") ? "checked" : ""; ?>><label for="od_29"  >T</label>
		</td><td><input id="od_30" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_pos1" value="1+" <?php echo ($elem_papillaeOd_pos1=="+1" || $elem_papillaeOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_30"  >1+</label>
		</td><td><input id="od_31" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_pos2" value="2+" <?php echo ($elem_papillaeOd_pos2=="+2" || $elem_papillaeOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_31"  >2+</label>
		</td><td><input id="od_32" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_pos3" value="3+" <?php echo ($elem_papillaeOd_pos3=="+3" || $elem_papillaeOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_32"  >3+</label>
		</td><td><input id="od_33" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_pos4" value="4+" <?php echo ($elem_papillaeOd_pos4=="+4" || $elem_papillaeOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_33"  >4+</label>
		</td><td><input id="od_34" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_rul" value="RUL" <?php echo ($elem_papillaeOd_rul=="RUL") ? "checked" : ""; ?>><label for="od_34"  >RUL</label>
		</td><td><input id="od_35" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOd_rll" value="RLL" <?php echo ($elem_papillaeOd_rll=="RLL") ? "checked" : ""; ?>><label for="od_35"  >RLL</label> 
		</td>
		<td align="center" class="bilat" onClick="check_bl('Papillae')">BL</td>
		<td align="left">Papillae</td>
		<td>
		<input id="os_28" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_neg" value="Absent" <?php echo ($elem_papillaeOs_neg=="-ve" || $elem_papillaeOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_28"  >Absent</label>
		</td><td><input id="os_29" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_T" value="T" <?php echo ($elem_papillaeOs_T=="T") ? "checked" : ""; ?>><label for="os_29"  >T</label>
		</td><td><input id="os_30" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_pos1" value="1+" <?php echo ($elem_papillaeOs_pos1=="+1" || $elem_papillaeOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_30"  >1+</label>
		</td><td><input id="os_31" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_pos2" value="2+" <?php echo ($elem_papillaeOs_pos2=="+2" || $elem_papillaeOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_31"  >2+</label>
		</td><td><input id="os_32" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_pos3" value="3+" <?php echo ($elem_papillaeOs_pos3=="+3" || $elem_papillaeOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_32"  >3+</label>
		</td><td><input id="os_33" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_pos4" value="4+" <?php echo ($elem_papillaeOs_pos4=="+4" || $elem_papillaeOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_33"  >4+</label>
		</td><td><input id="os_34" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_rul" value="LUL" <?php echo ($elem_papillaeOs_rul=="LUL") ? "checked" : ""; ?>><label for="os_34"  >LUL</label>
		</td><td><input id="os_35" type="checkbox" onClick="checkAbsent(this)" name="elem_papillaeOs_rll" value="LLL" <?php echo ($elem_papillaeOs_rll=="LLL") ? "checked" : ""; ?>><label for="os_35"  >LLL</label> 
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Papillae"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Papillae"]; }  ?>
		
		<tr id="d_SHmg" class="grp_SHmg">
		<td align="left">Subconj Hmg</td>
		<td>
		<input id="od_36" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_neg" value="Absent" <?php echo ($elem_subconjHmgOd_neg=="-ve" || $elem_subconjHmgOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_36"  >Absent</label>
		</td><td><input id="od_37" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_T" value="T" <?php echo ($elem_subconjHmgOd_T=="T") ? "checked" : ""; ?>><label for="od_37"  >T</label>
		</td><td><input id="od_38" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_pos1" value="1+" <?php echo ($elem_subconjHmgOd_pos1=="+1" || $elem_subconjHmgOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_38"  >1+</label>
		</td><td><input id="od_39" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_pos2" value="2+" <?php echo ($elem_subconjHmgOd_pos2=="+2" || $elem_subconjHmgOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_39"  >2+</label>
		</td><td><input id="od_40" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_pos3" value="3+" <?php echo ($elem_subconjHmgOd_pos3=="+3" || $elem_subconjHmgOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_40"  >3+</label>
		</td><td><input id="od_41" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_pos4" value="4+" <?php echo ($elem_subconjHmgOd_pos4=="+4" || $elem_subconjHmgOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_41"  >4+</label>

		</td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" rowspan="2" onClick="check_bl('SHmg')">BL</td>
		<td align="left">Subconj Hmg</td>
		<td>
		<input id="os_36" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_neg" value="Absent" <?php echo ($elem_subconjHmgOs_neg=="-ve" || $elem_subconjHmgOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_36"  >Absent</label>
		</td><td><input id="os_37" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_T" value="T" <?php echo ($elem_subconjHmgOs_T=="T") ? "checked" : ""; ?>><label for="os_37"  >T</label>
		</td><td><input id="os_38" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_pos1" value="1+" <?php echo ($elem_subconjHmgOs_pos1=="+1" || $elem_subconjHmgOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_38"  >1+</label>
		</td><td><input id="os_39" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_pos2" value="2+" <?php echo ($elem_subconjHmgOs_pos2=="+2" || $elem_subconjHmgOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_39"  >2+</label>
		</td><td><input id="os_40" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_pos3" value="3+" <?php echo ($elem_subconjHmgOs_pos3=="+3" || $elem_subconjHmgOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_40"  >3+</label>
		</td><td><input id="os_41" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_pos4" value="4+" <?php echo ($elem_subconjHmgOs_pos4=="+4" || $elem_subconjHmgOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_41"   >4+</label>

		</td>
		<td></td>
		<td></td>
		</tr>
		
		
		<tr id="d_SHmg1" class="grp_SHmg">
		<td align="left"></td>
		<td><input id="od_42" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_nasal" value="Nasal" <?php echo ($elem_subconjHmgOd_nasal=="Nasal") ? "checked" : ""; ?>><label for="od_42"  >Nasal</label>
		</td><td><input id="od_43" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_temporal" value="Temporal" <?php echo ($elem_subconjHmgOd_temporal=="Temporal") ? "checked" : ""; ?>><label for="od_43"   >Temporal</label>
		</td><td><input id="od_sh_inf" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOd_inferior" value="Inferior" <?php echo ($elem_subconjHmgOd_inferior=="Inferior") ? "checked" : ""; ?>><label for="od_sh_inf"   >Inferior</label>
		</td><td><input id="od_45" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOd_superior" value="Superior" <?php echo ($elem_subconjHmgOd_superior=="Superior") ? "checked" : ""; ?>><label for="od_45"   >Superior</label>
		</td><td colspan="2"><input id="od_44" type="checkbox" onClick="checkAbsent(this)" name="elem_subconjHmgOd_diffuse" value="Diffuse" <?php echo ($elem_subconjHmgOd_diffuse=="Diffuse") ? "checked" : ""; ?>><label for="od_44"   >Diffuse</label>
		</td>		
		<td></td>
		<td></td>		
		<td align="left"></td>
		<td><input id="os_42" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_nasal" value="Nasal" <?php echo ($elem_subconjHmgOs_nasal=="Nasal") ? "checked" : ""; ?>><label for="os_42"  >Nasal</label>
		</td><td><input id="os_43" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_temporal" value="Temporal" <?php echo ($elem_subconjHmgOs_temporal=="Temporal") ? "checked" : ""; ?>><label for="os_43"   >Temporal</label>                            
		</td><td><input id="os_sh_inf" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_inferior" value="Inferior" <?php echo ($elem_subconjHmgOs_inferior=="Inferior") ? "checked" : ""; ?>><label for="os_sh_inf"   >Inferior</label>  
		</td><td><input id="os_45" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_superior" value="Superior" <?php echo ($elem_subconjHmgOs_superior=="Superior") ? "checked" : ""; ?>><label for="os_45"   >Superior</label>
		</td><td colspan="2"><input id="os_44" type="checkbox"  onclick="checkAbsent(this)" name="elem_subconjHmgOs_diffuse" value="Diffuse" <?php echo ($elem_subconjHmgOs_diffuse=="Diffuse") ? "checked" : ""; ?>><label for="os_44"   >Diffuse</label>
		</td>		
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Subconj Hmg"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Subconj Hmg"]; }  ?>
		
		<tr id="d_Ping">
		<td align="left">Pinguecula</td>
		<td>
		<input id="od_46" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_neg" value="Absent" <?php echo ($elem_pingueculaOd_neg=="-ve"  || $elem_pingueculaOd_neg=="Absent") ? "checked" : ""; ?>><label for="od_46"  >Absent</label>
		</td><td><input id="od_47" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_T" value="T" <?php echo ($elem_pingueculaOd_T=="T") ? "checked" : ""; ?>><label for="od_47"  >T</label>
		</td><td><input id="od_48" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_pos1" value="1+" <?php echo ($elem_pingueculaOd_pos1=="+1" || $elem_pingueculaOd_pos1=="1+") ? "checked" : ""; ?>><label for="od_48"  >1+</label>
		</td><td><input id="od_49" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_pos2" value="2+" <?php echo ($elem_pingueculaOd_pos2=="+2" || $elem_pingueculaOd_pos2=="2+") ? "checked" : ""; ?>><label for="od_49"  >2+</label>
		</td><td><input id="od_50" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_pos3" value="3+" <?php echo ($elem_pingueculaOd_pos3=="+3" || $elem_pingueculaOd_pos3=="3+") ? "checked" : ""; ?>><label for="od_50"  >3+</label>
		</td><td><input id="od_51" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_pos4" value="4+" <?php echo ($elem_pingueculaOd_pos4=="+4" || $elem_pingueculaOd_pos4=="4+") ? "checked" : ""; ?>><label for="od_51"  >4+</label>
		</td><td><input id="od_52" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_nasal" value="Nasal" <?php echo ($elem_pingueculaOd_nasal=="Nasal") ? "checked" : ""; ?>><label for="od_52"  >Nasal</label>
		</td><td><input id="od_53" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOd_temporal" value="Temporal" <?php echo ($elem_pingueculaOd_temporal=="Temporal") ? "checked" : ""; ?>><label for="od_53"  >Temporal</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Ping')">BL</td>
		<td align="left">Pinguecula</td>
		<td>
		<input id="os_46" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_neg" value="Absent" <?php echo ($elem_pingueculaOs_neg=="-ve" || $elem_pingueculaOs_neg=="Absent") ? "checked" : ""; ?>><label for="os_46"  >Absent</label>
		</td><td><input id="os_47" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_T" value="T" <?php echo ($elem_pingueculaOs_T=="T") ? "checked" : ""; ?>><label for="os_47"  >T</label>
		</td><td><input id="os_48" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_pos1" value="1+" <?php echo ($elem_pingueculaOs_pos1=="+1" || $elem_pingueculaOs_pos1=="1+") ? "checked" : ""; ?>><label for="os_48"  >1+</label>
		</td><td><input id="os_49" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_pos2" value="2+" <?php echo ($elem_pingueculaOs_pos2=="+2" || $elem_pingueculaOs_pos2=="2+") ? "checked" : ""; ?>><label for="os_49"  >2+</label>
		</td><td><input id="os_50" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_pos3" value="3+" <?php echo ($elem_pingueculaOs_pos3=="+3" || $elem_pingueculaOs_pos3=="3+") ? "checked" : ""; ?>><label for="os_50"  >3+</label>
		</td><td><input id="os_51" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_pos4" value="4+" <?php echo ($elem_pingueculaOs_pos4=="+4" || $elem_pingueculaOs_pos4=="4+") ? "checked" : ""; ?>><label for="os_51"  >4+</label>
		</td><td><input id="os_52" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_nasal" value="Nasal" <?php echo ($elem_pingueculaOs_nasal=="Nasal") ? "checked" : ""; ?>><label for="os_52"  >Nasal</label>
		</td><td><input id="os_53" type="checkbox"  onclick="checkAbsent(this)" name="elem_pingueculaOs_temporal" value="Temporal" <?php echo ($elem_pingueculaOs_temporal=="Temporal") ? "checked" : ""; ?>><label for="os_53"  >Temporal</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Pinguecula"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Pinguecula"]; }  ?>
		
		<tr id="d_fornBody" class="grp_fornBody">
		<td align="left">Foreign Body</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_neg" name="elem_conj_foreignOd_neg" value="Absent" 
		<?php echo ($elem_conj_foreignOd_neg == "-ve" || $elem_conj_foreignOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_neg"  >Absent</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_pos" name="elem_conj_foreignOd_pos" value="Present" 
		<?php echo ($elem_conj_foreignOd_pos == "+ve" || $elem_conj_foreignOd_pos == "Present") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_pos"  >Present</label>
		</td><td colspan="6"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_Metallic" name="elem_conj_foreignOd_Metallic" value="Metallic w/rust ring" 
		<?php echo ($elem_conj_foreignOd_Metallic == "Metallic w/rust run" || $elem_conj_foreignOd_Metallic == "Metallic w/rust ring") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_Metallic"   class="aato">Metallic w/rust ring</label>

		</td>		
		<td align="center" class="bilat" rowspan="3" onClick="check_bl('fornBody')">BL</td>
		<td align="left">Foreign Body</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_neg" name="elem_conj_foreignOs_neg" value="Absent" 
		<?php echo ($elem_conj_foreignOs_neg == "-ve" || $elem_conj_foreignOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_neg"  >Absent</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_pos" name="elem_conj_foreignOs_pos" value="Present" 
		<?php echo ($elem_conj_foreignOs_pos == "+ve" || $elem_conj_foreignOs_pos == "Present") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_pos"  >Present</label>
		</td><td colspan="6"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_Metallic" name="elem_conj_foreignOs_Metallic" value="Metallic w/rust ring" 
		<?php echo ($elem_conj_foreignOs_Metallic == "Metallic w/rust run" || $elem_conj_foreignOs_Metallic == "Metallic w/rust ring") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_Metallic"   class="aato">Metallic w/rust ring</label>

		</td>		
		</tr>
		
		
		<tr id="d_fornBody1" class="grp_fornBody">
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_NonMetallic" name="elem_conj_foreignOd_NonMetallic" value="Non Metallic" 
		<?php echo ($elem_conj_foreignOd_NonMetallic == "Non Metallic") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_NonMetallic"   class="non_meta">Non Metallic</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_Suture" name="elem_conj_foreignOd_Suture" value="Suture" 
		<?php echo ($elem_conj_foreignOd_Suture == "Suture") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_Suture"   class="aato">Suture</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_Old" name="elem_conj_foreignOd_Old" value="Old w/o staining" 
		<?php echo ($elem_conj_foreignOd_Old == "Old w/o staining") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_Old"   class="aato">Old w/o staining</label>
		</td>		
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_NonMetallic" name="elem_conj_foreignOs_NonMetallic" value="Non Metallic" 
		<?php echo ($elem_conj_foreignOs_NonMetallic == "Non Metallic") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_NonMetallic"   class="non_meta">Non Metallic</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_Suture" name="elem_conj_foreignOs_Suture" value="Suture" 
		<?php echo ($elem_conj_foreignOs_Suture == "Suture") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_Suture"   class="aato">Suture</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_Old" name="elem_conj_foreignOs_Old" value="Old w/o staining" 
		<?php echo ($elem_conj_foreignOs_Old == "Old w/o staining") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_Old"   class="aato">Old w/o staining</label>
		</td>		
		</tr>
		
		<tr id="d_fornBody2" class="grp_fornBody">
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_Penetrating" name="elem_conj_foreignOd_Penetrating" value="Penetrating" 
		<?php echo ($elem_conj_foreignOd_Penetrating == "Penetrating") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_Penetrating"   class="non_meta">Penetrating</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOd_NonPenetrating" name="elem_conj_foreignOd_NonPenetrating" value="Non-Penetrating" 
		<?php echo ($elem_conj_foreignOd_NonPenetrating == "Non-Penetrating") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOd_NonPenetrating"   class="aato">Non-Penetrating</label>
		</td>
		<td></td>
		<td></td>
		<td></td>		
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_Penetrating" name="elem_conj_foreignOs_Penetrating" value="Penetrating" 
		<?php echo ($elem_conj_foreignOs_Penetrating == "Penetrating") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_Penetrating"   class="non_meta">Penetrating</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);" id="elem_conj_foreignOs_NonPenetrating" name="elem_conj_foreignOs_NonPenetrating" value="Non-Penetrating" 
		<?php echo ($elem_conj_foreignOs_NonPenetrating == "Non-Penetrating") ? "checked=checked" : "" ;?>><label for="elem_conj_foreignOs_NonPenetrating"   class="aato">Non-Penetrating</label>
		</td>		
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Foreign Body"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Foreign Body"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_ConjBleb <?php echo $cls_ConjBleb; ?>" id="d_ConjBleb">
		<td  align="left" class="grpbtn" onclick="openSubGrp('ConjBleb')">			
			<label >Bleb
			<span class="glyphicon <?php echo $arow_ConjBleb; ?>"></span></label> 
		</td>
		<td >
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Quiet" name="elem_conj_blebOd_Quiet" value="Quiet" <?php echo ($elem_conj_blebOd_Quiet == "Quiet") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Quiet"   >Quiet</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Diffuse" name="elem_conj_blebOd_Diffuse" value="Diffuse" <?php echo ($elem_conj_blebOd_Diffuse == "Diffuse") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Diffuse"   >Diffuse</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Localized" name="elem_conj_blebOd_Localized" value="Localized" <?php echo ($elem_conj_blebOd_Localized == "Localized") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Localized"   >Localized</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Cystic" name="elem_conj_blebOd_Cystic" value="Cystic" <?php echo ($elem_conj_blebOd_Cystic == "Cystic") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Cystic"   >Cystic</label>		
		</td>		
		<td align="center" class="bilat"  onClick="check_bl('ConjBleb')">BL</td>
		<td  align="left" class="grpbtn" onclick="openSubGrp('ConjBleb')">
			<label >Bleb 
			<span class="glyphicon <?php echo $arow_ConjBleb; ?>"></span></label>
		</td>
		<td >
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Quiet" name="elem_conj_blebOs_Quiet" value="Quiet" <?php echo ($elem_conj_blebOs_Quiet == "Quiet") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Quiet"   >Quiet</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Diffuse" name="elem_conj_blebOs_Diffuse" value="Diffuse" <?php echo ($elem_conj_blebOs_Diffuse == "Diffuse") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Diffuse"   >Diffuse</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Localized" name="elem_conj_blebOs_Localized" value="Localized" <?php echo ($elem_conj_blebOs_Localized == "Localized") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Localized"   >Localized</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Cystic" name="elem_conj_blebOs_Cystic" value="Cystic" <?php echo ($elem_conj_blebOs_Cystic == "Cystic") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Cystic"   >Cystic</label>		
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_ConjBleb <?php echo $cls_ConjBleb; ?>">
		<td></td>	
		<td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Thin" name="elem_conj_blebOd_Thin" value="Thin" <?php echo ($elem_conj_blebOd_Thin == "Thin") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Thin"   >Thin</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Thick" name="elem_conj_blebOd_Thick" value="Thick" <?php echo ($elem_conj_blebOd_Thick == "Thick") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Thick"   >Thick</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Avascular" name="elem_conj_blebOd_Avascular" value="Avascular" <?php echo ($elem_conj_blebOd_Avascular == "Avascular") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Avascular"   >Avascular</label>		
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_Fibrotic" name="elem_conj_blebOd_Fibrotic" value="Fibrotic" <?php echo ($elem_conj_blebOd_Fibrotic == "Fibrotic") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_Fibrotic"   >Fibrotic</label>		
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOd_RingOfSteel" name="elem_conj_blebOd_RingOfSteel" value="Ring of Steel" <?php echo ($elem_conj_blebOd_RingOfSteel == "Ring of Steel") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOd_RingOfSteel"   class="aato">Ring of Steel</label>
		</td>
		<td align="center" class="bilat"  onClick="check_bl('ConjBleb')">BL</td>	
		<td></td>
		<td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Thin" name="elem_conj_blebOs_Thin" value="Thin" <?php echo ($elem_conj_blebOs_Thin == "Thin") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Thin"   >Thin</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Thick" name="elem_conj_blebOs_Thick" value="Thick" <?php echo ($elem_conj_blebOs_Thick == "Thick") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Thick"   >Thick</label>		
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Avascular" name="elem_conj_blebOs_Avascular" value="Avascular" <?php echo ($elem_conj_blebOs_Avascular == "Avascular") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Avascular"   >Avascular</label>		
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_Fibrotic" name="elem_conj_blebOs_Fibrotic" value="Fibrotic" <?php echo ($elem_conj_blebOs_Fibrotic == "Fibrotic") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_Fibrotic"   >Fibrotic</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_blebOs_RingOfSteel" name="elem_conj_blebOs_RingOfSteel" value="Ring of Steel" <?php echo ($elem_conj_blebOs_RingOfSteel == "Ring of Steel") ? "checked=checked" : "" ;?>><label for="elem_conj_blebOs_RingOfSteel"   class="aato">Ring of Steel</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_ConjBleb <?php echo $cls_ConjBleb; ?>">
		<td align="left">Vascularity</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_vascuOd_Mild" name="elem_conj_vascuOd_Mild" value="Mild" <?php echo ($elem_conj_vascuOd_Mild == "Mild") ? "checked=checked" : "" ;?>><label for="elem_conj_vascuOd_Mild"  >Mild</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_vascuOd_Moderate" name="elem_conj_vascuOd_Moderate" value="Moderate" <?php echo ($elem_conj_vascuOd_Moderate == "Moderate") ? "checked=checked" : "" ;?>><label for="elem_conj_vascuOd_Moderate"   >Moderate</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_vascuOd_Severe" name="elem_conj_vascuOd_Severe" value="Severe" <?php echo ($elem_conj_vascuOd_Severe == "Severe") ? "checked=checked" : "" ;?>><label for="elem_conj_vascuOd_Severe"   >Severe</label>
		</td>		
		<td></td>
		<td></td>
		<td></td>	
		<td align="center" class="bilat"  onClick="check_bl('ConjBleb')">BL</td>	
		<td align="left">Vascularity</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_vascuOs_Mild" name="elem_conj_vascuOs_Mild" value="Mild" <?php echo ($elem_conj_vascuOs_Mild == "Mild") ? "checked=checked" : "" ;?>><label for="elem_conj_vascuOs_Mild"  >Mild</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_vascuOs_Moderate" name="elem_conj_vascuOs_Moderate" value="Moderate" <?php echo ($elem_conj_vascuOs_Moderate == "Moderate") ? "checked=checked" : "" ;?>><label for="elem_conj_vascuOs_Moderate"   >Moderate</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_vascuOs_Severe" name="elem_conj_vascuOs_Severe" value="Severe" <?php echo ($elem_conj_vascuOs_Severe == "Severe") ? "checked=checked" : "" ;?>><label for="elem_conj_vascuOs_Severe"   >Severe</label>
		</td>		
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Bleb/Vascularity"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Bleb/Vascularity"]; }  ?>
		
		<tr class="exmhlgcol grp_ConjBleb <?php echo $cls_ConjBleb; ?>">
		<td align="left">Elevation</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOd_Low" name="elem_conj_elevOd_Low" value="Low" <?php echo ($elem_conj_elevOd_Low == "Low") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOd_Low"  >Low</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOd_pos1" name="elem_conj_elevOd_pos1" value="1+" <?php echo ($elem_conj_elevOd_pos1 == "+1" || $elem_conj_elevOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOd_pos1"  >1+</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOd_pos2" name="elem_conj_elevOd_pos2" value="2+" <?php echo ($elem_conj_elevOd_pos2 == "+2" || $elem_conj_elevOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOd_pos2"  >2+</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOd_pos3" name="elem_conj_elevOd_pos3" value="3+" <?php echo ($elem_conj_elevOd_pos3 == "+3" || $elem_conj_elevOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOd_pos3"  >3+</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>		
		<td align="center" class="bilat"  onClick="check_bl('ConjBleb')">BL</td>
		<td align="left">Elevation</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOs_Low" name="elem_conj_elevOs_Low" value="Low" <?php echo ($elem_conj_elevOs_Low == "Low") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOs_Low"  >Low</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOs_pos1" name="elem_conj_elevOs_pos1" value="1+" <?php echo ($elem_conj_elevOs_pos1 == "+1" || $elem_conj_elevOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOs_pos1"  >1+</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOs_pos2" name="elem_conj_elevOs_pos2" value="2+" <?php echo ($elem_conj_elevOs_pos2 == "+2" || $elem_conj_elevOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOs_pos2"  >2+</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_elevOs_pos3" name="elem_conj_elevOs_pos3" value="3+" <?php echo ($elem_conj_elevOs_pos3 == "+3" || $elem_conj_elevOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_conj_elevOs_pos3"  >3+</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Bleb/Elevation"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Bleb/Elevation"]; }  ?>
		
		<tr class="exmhlgcol grp_ConjBleb <?php echo $cls_ConjBleb; ?>">
		<td align="left">Extends for Clock Hrs.</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOd_1" name="elem_conj_exClkHrsOd_1" value="1" <?php echo ($elem_conj_exClkHrsOd_1 == "1" || $elem_conj_exClkHrsOd_1 == "1") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOd_1"  >1</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOd_2" name="elem_conj_exClkHrsOd_2" value="2" <?php echo ($elem_conj_exClkHrsOd_2 == "2" || $elem_conj_exClkHrsOd_2 == "2") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOd_2"  >2</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOd_3" name="elem_conj_exClkHrsOd_3" value="3" <?php echo ($elem_conj_exClkHrsOd_3 == "3" || $elem_conj_exClkHrsOd_3 == "3") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOd_3"  >3</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOd_4" name="elem_conj_exClkHrsOd_4" value="4+" <?php echo ($elem_conj_exClkHrsOd_4 == "4+" || $elem_conj_exClkHrsOd_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOd_4"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>	
		<td align="center" class="bilat"  onClick="check_bl('ConjBleb')">BL</td>	
		<td align="left">Extends for Clock Hrs.</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOs_1" name="elem_conj_exClkHrsOs_1" value="1" <?php echo ($elem_conj_exClkHrsOs_1 == "1" || $elem_conj_exClkHrsOs_1 == "1") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOs_1"  >1</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOs_2" name="elem_conj_exClkHrsOs_2" value="2" <?php echo ($elem_conj_exClkHrsOs_2 == "2" || $elem_conj_exClkHrsOs_2 == "2") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOs_2"  >2</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOs_3" name="elem_conj_exClkHrsOs_3" value="3" <?php echo ($elem_conj_exClkHrsOs_3 == "3" || $elem_conj_exClkHrsOs_3 == "3") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOs_3"  >3</label>
		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_exClkHrsOs_4" name="elem_conj_exClkHrsOs_4" value="4+" <?php echo ($elem_conj_exClkHrsOs_4 == "4+" || $elem_conj_exClkHrsOs_4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_conj_exClkHrsOs_4"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Bleb/Extends for Clock Hrs."])){ echo $arr_exm_ext_htm["Conjunctiva"]["Bleb/Extends for Clock Hrs."]; }  ?>
		
		<tr class="exmhlgcol grp_ConjBleb <?php echo $cls_ConjBleb; ?>">
		<td align="left">Seidel Test</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_seidelTOd_Leak" name="elem_conj_seidelTOd_Leak" value="Leak" <?php echo ($elem_conj_seidelTOd_Leak == "Leak") ? "checked=checked" : "" ;?>><label for="elem_conj_seidelTOd_Leak"   >Leak</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_seidelTOd_NoLeak" name="elem_conj_seidelTOd_NoLeak" value="No Leak" <?php echo ($elem_conj_seidelTOd_NoLeak == "No Leak") ? "checked=checked" : "" ;?>><label for="elem_conj_seidelTOd_NoLeak"   >No Leak</label>		
		</td>		
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>	
		<td align="center" class="bilat"  onClick="check_bl('ConjBleb')">BL</td>	
		<td align="left">Seidel Test</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_seidelTOs_Leak" name="elem_conj_seidelTOs_Leak" value="Leak" <?php echo ($elem_conj_seidelTOs_Leak == "Leak") ? "checked=checked" : "" ;?>><label for="elem_conj_seidelTOs_Leak"   >Leak</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'ConjBleb');" id="elem_conj_seidelTOs_NoLeak" name="elem_conj_seidelTOs_NoLeak" value="No Leak" <?php echo ($elem_conj_seidelTOs_NoLeak == "No Leak") ? "checked=checked" : "" ;?>><label for="elem_conj_seidelTOs_NoLeak"   >No Leak</label>		
		</td>		
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Bleb/Seidel Test"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Bleb/Seidel Test"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Bleb"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Bleb"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Conjunctiva"]["Main"])){ echo $arr_exm_ext_htm["Conjunctiva"]["Main"]; }  ?>
		
		<tr id="d_adOpt_Conj">
		<td align="left">Comments</td>
		<td colspan="8"><textarea  onblur="checkwnls()" id="od_54" name="elem_conjunctivaAdvanceOptionOd" class="form-control"><?php echo ($elem_conjunctivaAdvanceOptionOd); ?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('adOpt_Conj')">BL</td>
		<td align="left">Comments</td>
		<td colspan="8"><textarea  onblur="checkwnls()" id="os_54" name="elem_conjunctivaAdvanceOptionOs" class="form-control"><?php echo ($elem_conjunctivaAdvanceOptionOs); ?></textarea></td>		
		</tr>
		
		</table>
	</div>
	<div class="clearfix"> </div>
	</div>

	<div role="tabpanel" class="tab-pane <?php echo (2 == $defTabKey) ? "active" : "" ?>" id="div2">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1">
				
			</div>
			<div class="col-sm-2 pharmo mt5">			
				<!-- Pen Light --*>					
				<input type="checkbox" id="elem_penLightCorn" name="elem_penLightCorn" value="1" <?php //echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
				<label id="lblPenLight" for="elem_penLightCorn"><strong>Pen Light</strong></label>				
				<!-- Pen Light -->
			</div>    
			<div class="col-sm-9"> 
				<span id="examFlag" class="glyphicon flagWnl "></span>
		
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChangeCorn"  name="elem_noChangeCorn" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncCorn == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChangeCorn">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
			</div>    
		</div>
	</div>    
	<div class="clearfix"> </div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="9" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		</td>
		<td width="100" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
		<td colspan="9" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>
		
		
		<tr id="d_CorDia">
		<td align="left">Cornea Diameter</td>
		<td colspan="8"><textarea  onblur="checkwnls();" id="elem_corDiaOd" name="elem_corDiaOd" class="form-control" ><?php echo $elem_corDiaOd;?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('CorDia')">BL</td>
		<td align="left">Cornea Diameter</td>
		<td colspan="8"><textarea  onblur="checkwnls();" id="elem_corDiaOs" name="elem_corDiaOs" class="form-control" ><?php echo $elem_corDiaOs;?></textarea></td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Cornea Diameter"])){ echo $arr_exm_ext_htm["Cornea"]["Cornea Diameter"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_CorDE <?php echo $cls_CorDE; ?>" id="d_CorDE">
		<td align="left" class="grpbtn" onclick="openSubGrp('CorDE')">			
			<label >Dry Eyes
			<span class="glyphicon <?php echo $arow_CorDE; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'CorDE');"  id="od_55" name="elem_dryEyesOd" class="form-control" ><?php echo $elem_dryEyesOd;?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('CorDE')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('CorDE')">
			<label >Dry Eyes
			<span class="glyphicon <?php echo $arow_CorDE; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'CorDE');"  id="os_55" name="elem_dryEyesOs" class="form-control" ><?php echo $elem_dryEyesOs;?></textarea></td>		
		</tr>		
		
		<tr class="exmhlgcol grp_CorDE <?php echo $cls_CorDE; ?>">
		<td align="left">Dec TBUT</td>
		<td>
		<input type="text" onChange="checkAbsent(this,'CorDE');" name="elem_incTbitOd_text" value="<?php echo $elem_incTbitOd_text; ?>" class="form-control">
		</td><td><input id="od_56" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOd_neg" value="Absent" <?php echo ($elem_incTbitOd_neg == "-ve" || $elem_incTbitOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_56"  >Absent</label>
		</td><td><input id="od_57" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOd_T" value="T" <?php echo ($elem_incTbitOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_57"  >T</label>
		</td><td><input id="od_58" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOd_pos1" value="1+" <?php echo ($elem_incTbitOd_pos1 == "+1" || $elem_incTbitOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_58"  >1+</label>
		</td><td><input id="od_59" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOd_pos2" value="2+" <?php echo ($elem_incTbitOd_pos2 == "+2" || $elem_incTbitOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_59"  >2+</label>
		</td><td><input id="od_60" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOd_pos3" value="3+" <?php echo ($elem_incTbitOd_pos3 == "+3" || $elem_incTbitOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_60"  >3+</label>
		</td><td colspan="2"><input id="od_61" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOd_pos4" value="4+" <?php echo ($elem_incTbitOd_pos4 == "+4" || $elem_incTbitOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_61"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CorDE')" >BL</td>
		<td align="left">Dec TBUT</td>
		<td>
		<input type="text" onChange="checkAbsent(this,'CorDE');" name="elem_incTbitOs_text" value="<?php echo $elem_incTbitOs_text; ?>" class="form-control">
		</td><td><input id="os_56" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOs_neg" value="Absent" <?php echo ($elem_incTbitOs_neg == "-ve" || $elem_incTbitOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_56"  >Absent</label>
		</td><td><input id="os_57" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOs_T" value="T" <?php echo ($elem_incTbitOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_57"  >T</label>
		</td><td><input id="os_58" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOs_pos1" value="1+" <?php echo ($elem_incTbitOs_pos1 == "+1" || $elem_incTbitOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_58"  >1+</label>
		</td><td><input id="os_59" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOs_pos2" value="2+" <?php echo ($elem_incTbitOs_pos2 == "+2" || $elem_incTbitOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_59"  >2+</label>
		</td><td><input id="os_60" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOs_pos3" value="3+" <?php echo ($elem_incTbitOs_pos3 == "+3" || $elem_incTbitOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_60"  >3+</label>
		</td><td colspan="2"><input id="os_61" type="checkbox" onClick="checkAbsent(this,'CorDE');" name="elem_incTbitOs_pos4" value="4+" <?php echo ($elem_incTbitOs_pos4 == "+4" || $elem_incTbitOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_61"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dry Eyes/Dec TBUT"])){ echo $arr_exm_ext_htm["Cornea"]["Dry Eyes/Dec TBUT"]; }  ?>
		
		<tr class="exmhlgcol grp_CorDE <?php echo $cls_CorDE; ?>">
		<td align="left">Dec. Tear Lake</td>
		<td>
                                <input id="od_62" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOd_neg" value="Absent" <?php echo ($elem_decTearLakeOd_neg == "-ve" || $elem_decTearLakeOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_62"  >Absent</label>
                                </td><td><input id="od_63" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOd_T" value="T" <?php echo ($elem_decTearLakeOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_63"  >T</label>
                                </td><td><input id="od_64" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOd_pos1" value="1+" <?php echo ($elem_decTearLakeOd_pos1 == "+1" || $elem_decTearLakeOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_64"  >1+</label>
                                </td><td><input  id="od_65" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOd_pos2" value="2+" <?php echo ($elem_decTearLakeOd_pos2 == "+2" || $elem_decTearLakeOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_65"  >2+</label>
                                </td><td><input id="od_66" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOd_pos3" value="3+" <?php echo ($elem_decTearLakeOd_pos3 == "+3" || $elem_decTearLakeOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_66"  >3+</label>
                                </td><td><input id="od_67" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOd_pos4" value="4+" <?php echo ($elem_decTearLakeOd_pos4 == "+4" || $elem_decTearLakeOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_67"  >4+</label>
                            </td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('CorDE')" >BL</td>
		<td align="left">Dec. Tear Lake</td>
		<td>
		<input id="os_62" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOs_neg" value="Absent" <?php echo ($elem_decTearLakeOs_neg == "-ve" || $elem_decTearLakeOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_62"  >Absent</label>
		</td><td><input id="os_63" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOs_T" value="T" <?php echo ($elem_decTearLakeOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_63"  >T</label>
		</td><td><input id="os_64" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOs_pos1" value="1+" <?php echo ($elem_decTearLakeOs_pos1 == "+1" || $elem_decTearLakeOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_64"  >1+</label>
		</td><td><input id="os_65" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOs_pos2" value="2+" <?php echo ($elem_decTearLakeOs_pos2 == "+2" || $elem_decTearLakeOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_65"  >2+</label>
		</td><td><input id="os_66" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOs_pos3" value="3+" <?php echo ($elem_decTearLakeOs_pos3 == "+3" || $elem_decTearLakeOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_66"  >3+</label>
		</td><td><input id="os_67" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_decTearLakeOs_pos4" value="4+" <?php echo ($elem_decTearLakeOs_pos4 == "+4" || $elem_decTearLakeOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_67"  >4+</label>
		</td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dry Eyes/Dec. Tear Lake"])){ echo $arr_exm_ext_htm["Cornea"]["Dry Eyes/Dec. Tear Lake"]; }  ?>
		
		<tr class="exmhlgcol grp_CorDE <?php echo $cls_CorDE; ?>">
		<td align="left">SPK</td>
		<td>
		<input id="od_68" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOd_neg" value="Absent" <?php echo ($elem_spkOd_neg == "-ve" || $elem_spkOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_68"  >Absent</label>
		</td><td><input id="od_69" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOd_T" value="T" <?php echo ($elem_spkOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_69"  >T</label>
		</td><td><input id="od_70" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOd_pos1" value="1+" <?php echo ($elem_spkOd_pos1 == "+1" || $elem_spkOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_70"  >1+</label>
		</td><td><input id="od_71"  type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOd_pos2" value="2+" <?php echo ($elem_spkOd_pos2 == "+2" || $elem_spkOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_71"  >2+</label>
		</td><td><input id="od_72" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOd_pos3" value="3+" <?php echo ($elem_spkOd_pos3 == "+3" || $elem_spkOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_72"  >3+</label>
		</td><td><input id="od_73" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOd_pos4" value="4+" <?php echo ($elem_spkOd_pos4 == "+4" || $elem_spkOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_73"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('CorDE')" >BL</td>
		<td align="left">SPK</td>
		<td>
		<input id="os_68" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOs_neg" value="Absent" <?php echo ($elem_spkOs_neg == "-ve" || $elem_spkOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_68"  >Absent</label>
		</td><td><input id="os_69" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOs_T" value="T" <?php echo ($elem_spkOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_69"  >T</label>
		</td><td><input  id="os_70" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOs_pos1" value="1+" <?php echo ($elem_spkOs_pos1 == "+1" || $elem_spkOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_70"  >1+</label>
		</td><td><input id="os_71" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOs_pos2" value="2+" <?php echo ($elem_spkOs_pos2 == "+2" || $elem_spkOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_71"  >2+</label>
		</td><td><input id="os_72" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOs_pos3" value="3+" <?php echo ($elem_spkOs_pos3 == "+3" || $elem_spkOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_72"  >3+</label>
		</td><td><input id="os_73" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_spkOs_pos4" value="4+" <?php echo ($elem_spkOs_pos4 == "+4" || $elem_spkOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_73"  >4+</label>
		</td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dry Eyes/SPK"])){ echo $arr_exm_ext_htm["Cornea"]["Dry Eyes/SPK"]; }  ?>
		
		<tr class="exmhlgcol grp_CorDE <?php echo $cls_CorDE; ?>">
		<td align="left">Inc. Tear Lake</td>
		<td>
		<input id="od_74" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOd_neg" value="Absent" <?php echo ($elem_incTearLakeOd_neg == "-ve" || $elem_incTearLakeOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_74"  >Absent</label>
		</td><td><input id="od_75" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOd_T" value="T" <?php echo ($elem_incTearLakeOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_75"  >T</label>
		</td><td><input id="od_76" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOd_pos1" value="1+" <?php echo ($elem_incTearLakeOd_pos1 == "+1" || $elem_incTearLakeOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_76"  >1+</label>
		</td><td><input id="od_77" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOd_pos2" value="2+" <?php echo ($elem_incTearLakeOd_pos2 == "+2" || $elem_incTearLakeOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_77"  >2+</label>
		</td><td><input id="od_78" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOd_pos3" value="3+" <?php echo ($elem_incTearLakeOd_pos3 == "+3" || $elem_incTearLakeOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_78"  >3+</label>
		</td><td><input id="od_79" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOd_pos4" value="4+" <?php echo ($elem_incTearLakeOd_pos4 == "+4" || $elem_incTearLakeOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_79"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('CorDE')" >BL</td>
		<td align="left">Inc. Tear Lake</td>
		<td>
		<input id="os_74" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOs_neg" value="Absent" <?php echo ($elem_incTearLakeOs_neg == "-ve" || $elem_incTearLakeOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_74"  >Absent</label>
		</td><td><input id="os_75" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOs_T" value="T" <?php echo ($elem_incTearLakeOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_75"  >T</label>
		</td><td><input id="os_76" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOs_pos1" value="1+" <?php echo ($elem_incTearLakeOs_pos1 == "+1" || $elem_incTearLakeOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_76"  >1+</label>
		</td><td><input id="os_77" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOs_pos2" value="2+" <?php echo ($elem_incTearLakeOs_pos2 == "+2" || $elem_incTearLakeOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_77"  >2+</label>
		</td><td><input id="os_78" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOs_pos3" value="3+" <?php echo ($elem_incTearLakeOs_pos3 == "+3" || $elem_incTearLakeOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_78"  >3+</label>
		</td><td><input id="os_79" type="checkbox"  onclick="checkAbsent(this,'CorDE');" name="elem_incTearLakeOs_pos4" value="4+" <?php echo ($elem_incTearLakeOs_pos4 == "+4" || $elem_incTearLakeOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_79"  >4+</label>
		</td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dry Eyes/Inc. Tear Lake"])){ echo $arr_exm_ext_htm["Cornea"]["Dry Eyes/Inc. Tear Lake"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dry Eyes"])){ echo $arr_exm_ext_htm["Cornea"]["Dry Eyes"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_Dyst <?php echo $cls_Dyst; ?>" id="d_Dyst">
		<td align="left" class="grpbtn" onclick="openSubGrp('Dyst')">			
			<label >Dystrophy
			<span class="glyphicon <?php echo $arow_Dyst; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'Dyst');"  id="od_80" name="elem_dystrophyOd" class="form-control"><?php echo ($elem_dystrophyOd);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('Dyst')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('Dyst')">
			<label >Dystrophy
			<span class="glyphicon <?php echo $arow_Dyst; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'Dyst');"  id="os_80" name="elem_dystrophyOs" class="form-control" ><?php echo ($elem_dystrophyOs);?></textarea></td>		
		</tr>
		
		
		<tr class="exmhlgcol grp_Dyst <?php echo $cls_Dyst; ?>">
		<td align="left">Anterior (ABMD/MDF)</td>
		<td>
		<input id="od_81" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOd_neg" value="Absent" <?php echo ($elem_abmdMdfOd_neg == "-ve" || $elem_abmdMdfOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_81"  >Absent</label>
		</td><td><input id="od_82" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOd_T" value="T" <?php echo ($elem_abmdMdfOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_82"  >T</label>
		</td><td><input id="od_83" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOd_pos1" value="1+" <?php echo ($elem_abmdMdfOd_pos1 == "+1" || $elem_abmdMdfOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_83"  >1+</label>
		</td><td><input id="od_84" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOd_pos2" value="2+" <?php echo ($elem_abmdMdfOd_pos2 == "+2" || $elem_abmdMdfOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_84"  >2+</label>
		</td><td><input id="od_85" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOd_pos3" value="3+" <?php echo ($elem_abmdMdfOd_pos3 == "+3" || $elem_abmdMdfOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_85"  >3+</label>
		</td><td><input id="od_86" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOd_pos4" value="4+" <?php echo ($elem_abmdMdfOd_pos4 == "+4" || $elem_abmdMdfOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_86"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('Dyst')" >BL</td>
		<td align="left">Anterior (ABMD/MDF)</td>
		<td>
		<input id="os_81" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOs_neg" value="Absent" <?php echo ($elem_abmdMdfOs_neg == "-ve" || $elem_abmdMdfOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_81"  >Absent</label>
		</td><td><input id="os_82" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOs_T" value="T" <?php echo ($elem_abmdMdfOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_82"  >T</label>
		</td><td><input id="os_83" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOs_pos1" value="1+" <?php echo ($elem_abmdMdfOs_pos1 == "+1" || $elem_abmdMdfOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_83"  >1+</label>
		</td><td><input id="os_84" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOs_pos2" value="2+" <?php echo ($elem_abmdMdfOs_pos2 == "+2" || $elem_abmdMdfOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_84"  >2+</label>
		</td><td><input id="os_85" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOs_pos3" value="3+" <?php echo ($elem_abmdMdfOs_pos3 == "+3" || $elem_abmdMdfOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_85"  >3+</label>
		</td><td><input id="os_86" type="checkbox"  onclick="checkAbsent(this,'Dyst');" name="elem_abmdMdfOs_pos4" value="4+" <?php echo ($elem_abmdMdfOs_pos4 == "+4" || $elem_abmdMdfOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_86"  >4+</label>
		</td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dystrophy/Anterior (ABMD/MDF)"])){ echo $arr_exm_ext_htm["Cornea"]["Dystrophy/Anterior (ABMD/MDF)"]; }  ?>
		
		<tr class="exmhlgcol grp_Dyst <?php echo $cls_Dyst; ?>">
		<td align="left">Stromal</td>
		<td>
		<input id="od_301" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_neg" value="Absent" <?php echo ($elem_StromalOd_neg == "-ve" || $elem_StromalOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_301"  >Absent</label>
		</td><td><input id="od_302" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_pos" value="Present" <?php echo ($elem_StromalOd_pos == "+ve" || $elem_StromalOd_pos == "Present") ? "checked=checked" : "" ;?>><label for="od_302"  >Present</label>
		</td><td><input id="od_303" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_T" value="T" <?php echo ($elem_StromalOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_303"  >T</label>
		</td><td><input id="od_304" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_pos1" value="1+" <?php echo ($elem_StromalOd_pos1 == "+1" || $elem_StromalOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_304"  >1+</label>
		</td><td><input id="od_305" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_pos2" value="2+" <?php echo ($elem_StromalOd_pos2 == "+2" || $elem_StromalOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_305"  >2+</label>
		</td><td><input id="od_306" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_pos3" value="3+" <?php echo ($elem_StromalOd_pos3 == "+3" || $elem_StromalOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_306"  >3+</label>
		</td><td colspan="2"><input id="od_307" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOd_pos4" value="4+" <?php echo ($elem_StromalOd_pos4 == "+4" || $elem_StromalOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_307"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Dyst')" >BL</td>
		<td align="left">Stromal</td>
		<td>
		<input id="os_301" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_neg" value="Absent" <?php echo ($elem_StromalOs_neg == "-ve" || $elem_StromalOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_301"  >Absent</label>
		</td><td><input id="os_302" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_pos" value="Present" <?php echo ($elem_StromalOs_pos == "+ve" || $elem_StromalOs_pos == "Present") ? "checked=checked" : "" ;?>><label for="os_302"  >Present</label>
		</td><td><input id="os_303" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_T" value="T" <?php echo ($elem_StromalOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_303"  >T</label>
		</td><td><input id="os_304" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_pos1" value="1+" <?php echo ($elem_StromalOs_pos1 == "+1" || $elem_StromalOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_304"  >1+</label>
		</td><td><input id="os_305" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_pos2" value="2+" <?php echo ($elem_StromalOs_pos2 == "+2" || $elem_StromalOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_305"  >2+</label>
		</td><td><input id="os_306" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_pos3" value="3+" <?php echo ($elem_StromalOs_pos3 == "+3" || $elem_StromalOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_306"  >3+</label>
		</td><td colspan="2"><input id="os_307" type="checkbox" onClick="checkAbsent(this,'Dyst');" name="elem_StromalOs_pos4" value="4+" <?php echo ($elem_StromalOs_pos4 == "+4" || $elem_StromalOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_307"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dystrophy/Stromal"])){ echo $arr_exm_ext_htm["Cornea"]["Dystrophy/Stromal"]; }  ?>
		
		<tr class="exmhlgcol grp_Dyst <?php echo $cls_Dyst; ?>" id="d_Posterior">
		<td align="left">Posterior</td>
		<td>
		<input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_neg" name="elem_posteriorOd_neg" value="Absent" <?php echo ($elem_posteriorOd_neg == "-ve" || $elem_posteriorOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_neg"  >Absent</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_Guttata" name="elem_posteriorOd_Guttata" value="Guttata" <?php echo ($elem_posteriorOd_Guttata == "Guttata") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_Guttata"   class="pst_gut">Guttata</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_Fuchs" name="elem_posteriorOd_Fuchs" value="Fuchs" <?php echo ($elem_posteriorOd_Fuchs == "Fuchs") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_Fuchs"   >Fuchs</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_T" name="elem_posteriorOd_T" value="T" <?php echo ($elem_posteriorOd_T == "T") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_T"  >T</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('Dyst')" rowspan="3" >BL</td>
		<td align="left">Posterior</td>
		<td>
		<input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_neg" name="elem_posteriorOs_neg" value="Absent" <?php echo ($elem_posteriorOs_neg == "-ve" || $elem_posteriorOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_neg"  >Absent</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_Guttata" name="elem_posteriorOs_Guttata" value="Guttata" <?php echo ($elem_posteriorOs_Guttata == "Guttata") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_Guttata"   class="pst_gut">Guttata</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_Fuchs" name="elem_posteriorOs_Fuchs" value="Fuchs" <?php echo ($elem_posteriorOs_Fuchs == "Fuchs") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_Fuchs"   >Fuchs</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_T" name="elem_posteriorOs_T" value="T" <?php echo ($elem_posteriorOs_T == "T") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_T"  >T</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		
		<tr class="exmhlgcol grp_Dyst <?php echo $cls_Dyst; ?>" id="d_Posterior1">
		<td align="left"></td>
		<td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_pos1" name="elem_posteriorOd_pos1" value="1+" <?php echo ($elem_posteriorOd_pos1 == "+1" || $elem_posteriorOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_pos1"  >1+</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_pos2" name="elem_posteriorOd_pos2" value="2+" <?php echo ($elem_posteriorOd_pos2 == "+2" || $elem_posteriorOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_pos2"  >2+</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_pos3" name="elem_posteriorOd_pos3" value="3+" <?php echo ($elem_posteriorOd_pos3 == "+3" || $elem_posteriorOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_pos3"  >3+</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_pos4" name="elem_posteriorOd_pos4" value="4+" <?php echo ($elem_posteriorOd_pos4 == "+4" || $elem_posteriorOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_pos4"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		
		<td align="left"></td>
		<td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_pos1" name="elem_posteriorOs_pos1" value="1+" <?php echo ($elem_posteriorOs_pos1 == "+1" || $elem_posteriorOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_pos1"  >1+</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_pos2" name="elem_posteriorOs_pos2" value="2+" <?php echo ($elem_posteriorOs_pos2 == "+2" || $elem_posteriorOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_pos2"  >2+</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_pos3" name="elem_posteriorOs_pos3" value="3+" <?php echo ($elem_posteriorOs_pos3 == "+3" || $elem_posteriorOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_pos3"  >3+</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_pos4" name="elem_posteriorOs_pos4" value="4+" <?php echo ($elem_posteriorOs_pos4 == "+4" || $elem_posteriorOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_pos4"  >4+</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		
		<tr class="exmhlgcol grp_Dyst <?php echo $cls_Dyst; ?>" id="d_Posterior2">
		<td align="left"></td>
		<td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_PPD" name="elem_posteriorOd_PPD" value="PPD" <?php echo ($elem_posteriorOd_PPD == "PPD") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_PPD"  >PPD</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_Mild" name="elem_posteriorOd_Mild" value="Mild" <?php echo ($elem_posteriorOd_Mild == "Mild") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_Mild"  >Mild</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_Md" name="elem_posteriorOd_Md" value="Mod" <?php echo ($elem_posteriorOd_Md == "Mod") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_Md"  >Mod</label>
		</td><td colspan="5"><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOd_Severe" name="elem_posteriorOd_Severe" value="Severe" <?php echo ($elem_posteriorOd_Severe == "Severe") ? "checked=checked" : "" ;?>><label for="elem_posteriorOd_Severe"  >Severe</label>
		</td>		
		
		<td align="left"></td>
		<td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_PPD" name="elem_posteriorOs_PPD" value="PPD" <?php echo ($elem_posteriorOs_PPD == "PPD") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_PPD"  >PPD</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_Mild" name="elem_posteriorOs_Mild" value="Mild" <?php echo ($elem_posteriorOs_Mild == "Mild") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_Mild"  >Mild</label>
		</td><td><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_Md" name="elem_posteriorOs_Md" value="Mod" <?php echo ($elem_posteriorOs_Md == "Mod") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_Md"  >Mod</label>
		</td><td colspan="5"><input type="checkbox" onClick="checkAbsent(this,'Dyst');" id="elem_posteriorOs_Severe" name="elem_posteriorOs_Severe" value="Severe" <?php echo ($elem_posteriorOs_Severe == "Severe") ? "checked=checked" : "" ;?>><label for="elem_posteriorOs_Severe"  >Severe</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dystrophy/Posterior"])){ echo $arr_exm_ext_htm["Cornea"]["Dystrophy/Posterior"]; }  ?>
		
		<tr class="exmhlgcol grp_Dyst <?php echo $cls_Dyst; ?>">
		<td align="left">Band Keratopathy</td>
		<td>
		<input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOd_Nasal" name="elem_bandKeraOd_Nasal" value="Nasal" <?php echo ($elem_bandKeraOd_Nasal == "Nasal") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOd_Nasal"  >Nasal</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOd_Temp" name="elem_bandKeraOd_Temp" value="Temporal" <?php echo ($elem_bandKeraOd_Temp == "Temporal") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOd_Temp"   class="band_temp">Temporal</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOd_pos1" name="elem_bandKeraOd_pos1" value="1+" <?php echo ($elem_bandKeraOd_pos1 == "+1" || $elem_bandKeraOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOd_pos1"  >1+</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOd_pos2" name="elem_bandKeraOd_pos2" value="2+" <?php echo ($elem_bandKeraOd_pos2 == "+2" || $elem_bandKeraOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOd_pos2"  >2+</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOd_pos3" name="elem_bandKeraOd_pos3" value="3+" <?php echo ($elem_bandKeraOd_pos3 == "+3" || $elem_bandKeraOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOd_pos3"   >3+</label>
		</td><td colspan="3"><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOd_Visax" name="elem_bandKeraOd_Visax" value="Visual axis" <?php echo ($elem_bandKeraOd_Visax == "Visual axis") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOd_Visax"   class="aato" >Visual axis</label>
		 </td>		
		<td align="center" class="bilat" onClick="check_bl('Dyst')" >BL</td>
		<td align="left">Band Keratopathy</td>
		<td>
		<input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOs_Nasal" name="elem_bandKeraOs_Nasal" value="Nasal" <?php echo ($elem_bandKeraOs_Nasal == "Nasal") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOs_Nasal"  >Nasal</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOs_Temp" name="elem_bandKeraOs_Temp" value="Temporal" <?php echo ($elem_bandKeraOs_Temp == "Temporal") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOs_Temp"   class="band_temp">Temporal</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOs_pos1" name="elem_bandKeraOs_pos1" value="1+" <?php echo ($elem_bandKeraOs_pos1 == "+1" || $elem_bandKeraOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOs_pos1"  >1+</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOs_pos2" name="elem_bandKeraOs_pos2" value="2+" <?php echo ($elem_bandKeraOs_pos2 == "+2" || $elem_bandKeraOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOs_pos2"  >2+</label>
		</td><td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOs_pos3" name="elem_bandKeraOs_pos3" value="3+" <?php echo ($elem_bandKeraOs_pos3 == "+3" || $elem_bandKeraOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOs_pos3"   >3+</label>
		</td><td colspan="3"><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'Dyst');" id="elem_bandKeraOs_Visax" name="elem_bandKeraOs_Visax" value="Visual axis" <?php echo ($elem_bandKeraOs_Visax == "Visual axis") ? "checked=checked" : "" ;?>><label for="elem_bandKeraOs_Visax"   class="aato" >Visual axis</label>
		 </td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dystrophy/Band Keratopathy"])){ echo $arr_exm_ext_htm["Cornea"]["Dystrophy/Band Keratopathy"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Dystrophy"])){ echo $arr_exm_ext_htm["Cornea"]["Dystrophy"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_CornTruma <?php echo $cls_CornTruma; ?>" id="d_CornTruma">
		<td align="left" class="grpbtn" onclick="openSubGrp('CornTruma')">			
			<label >Trauma
			<span class="glyphicon <?php echo $arow_CornTruma; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea onBlur="checkwnls();checkSymClr(this,'CornTruma');"  id="od_99" name="elem_TraumaOd_text" class="form-control" ><?php echo $elem_TraumaOd_text; ?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('CornTruma')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('CornTruma')">
			<label >Trauma
			<span class="glyphicon <?php echo $arow_CornTruma; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'CornTruma');"  id="os_99" name="elem_TraumaOs_text" class="form-control" ><?php echo $elem_TraumaOs_text; ?></textarea></td>		
		</tr>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>">
		<td align="left">Abrasion</td>
		<td>
		<input id="od_100" type="checkbox"  onclick="checkAbsent(this,'CornTruma');" name="elem_abrasionOd_neg" value="Absent" <?php echo ($elem_abrasionOd_neg == "-ve" || $elem_abrasionOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_100"  >Absent</label> 
		</td><td><input id="od_101" type="checkbox"  onclick="checkAbsent(this,'CornTruma');" name="elem_abrasionOd_pos" value="Present" <?php echo ($elem_abrasionOd_pos == "+ve" || $elem_abrasionOd_pos == "Present") ? "checked=checked" : "" ;?>><label for="od_101"  >Present</label>
		</td><td colspan="6"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_abrasionOd_negInfiltrate" name="elem_abrasionOd_negInfiltrate" value="-ve Infiltrate" <?php echo ($elem_abrasionOd_negInfiltrate == "-ve Infiltrate") ? "checked=checked" : "" ;?>><label for="elem_abrasionOd_negInfiltrate"  >-ve Infiltrate</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornTruma')" >BL</td>
		<td align="left">Abrasion</td>
		<td>
		<input id="os_100" type="checkbox"  onclick="checkAbsent(this,'CornTruma');" name="elem_abrasionOs_neg" value="Absent" <?php echo ($elem_abrasionOs_neg == "-ve" || $elem_abrasionOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_100"  >Absent</label> 
		</td><td><input id="os_101" type="checkbox"  onclick="checkAbsent(this,'CornTruma');" name="elem_abrasionOs_pos" value="Present" <?php echo ($elem_abrasionOs_pos == "+ve" || $elem_abrasionOs_pos == "Present") ? "checked=checked" : "" ;?>><label for="os_101"  >Present</label> 
		</td><td colspan="6"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_abrasionOs_negInfiltrate" name="elem_abrasionOs_negInfiltrate" value="-ve Infiltrate" <?php echo ($elem_abrasionOs_negInfiltrate == "-ve Infiltrate") ? "checked=checked" : "" ;?>><label for="elem_abrasionOs_negInfiltrate"  >-ve Infiltrate</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Trauma/Abrasion"])){ echo $arr_exm_ext_htm["Cornea"]["Trauma/Abrasion"]; }  ?>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>">
		<td align="left">Irregular Epithelium</td>
		<td colspan="3">
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_irregularEpitheliumOd" name="elem_irregularEpitheliumOd" value="Irregular Epithelium" <?php echo ($elem_irregularEpitheliumOd == "Irregular Epithelium") ? "checked=checked" : "" ;?>><label for="elem_irregularEpitheliumOd"   class="tr_irrEp">Irregular Epithelium</label>
		</td><td colspan="5"><input id="od_102" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" name="elem_irr_pseudo_DendriteOd" value="Pseudo Dendrite" <?php echo ($elem_irr_pseudo_DendriteOd == "Pseudo Dendrite") ? "checked=checked" : "" ;?>><label for="od_102"   class="aato">Pseudo Dendrite</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornTruma')" >BL</td>
		<td align="left">Irregular Epithelium</td>
		<td colspan="3">
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_irregularEpitheliumOs" name="elem_irregularEpitheliumOs" value="Irregular Epithelium" <?php echo ($elem_irregularEpitheliumOs == "Irregular Epithelium") ? "checked=checked" : "" ;?>><label for="elem_irregularEpitheliumOs"   class="tr_irrEp">Irregular Epithelium</label>
		</td><td colspan="5"><input id="os_102" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" name="elem_irr_pseudo_DendriteOs" value="Pseudo Dendrite" <?php echo ($elem_irr_pseudo_DendriteOs == "Pseudo Dendrite") ? "checked=checked" : "" ;?>><label for="os_102"   class="aato">Pseudo Dendrite</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Trauma/Irregular Epithelium"])){ echo $arr_exm_ext_htm["Cornea"]["Trauma/Irregular Epithelium"]; }  ?>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>" id="d_Corn_Foreign_Body">
		<td align="left">Foreign Body</td>
		<td colspan="1">
		<input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_neg" name="elem_foreignOd_neg" value="Absent" <?php echo ($elem_foreignOd_neg == "-ve" || $elem_foreignOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_neg"  >Absent</label>
		</td><td colspan="1"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_pos" name="elem_foreignOd_pos" value="Present" <?php echo ($elem_foreignOd_pos == "+ve" || $elem_foreignOd_pos == "Present") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_pos"  >Present</label>
		</td><td colspan="6"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_Metallic" name="elem_foreignOd_Metallic" value="Metallic w/rust ring" <?php echo ($elem_foreignOd_Metallic == "Metallic w/rust run" || $elem_foreignOd_Metallic == "Metallic w/rust ring") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_Metallic"   class="aato">Metallic w/rust ring</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornTruma')" rowspan="3" >BL</td>
		<td align="left">Foreign Body</td>
		<td colspan="1">
		<input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_neg" name="elem_foreignOs_neg" value="Absent" <?php echo ($elem_foreignOs_neg == "-ve" || $elem_foreignOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_neg"  >Absent</label>
		</td><td colspan="1"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_pos" name="elem_foreignOs_pos" value="Present" <?php echo ($elem_foreignOs_pos == "+ve" || $elem_foreignOs_pos == "Present") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_pos"  >Present</label>
		</td><td colspan="6"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_Metallic" name="elem_foreignOs_Metallic" value="Metallic w/rust ring" <?php echo ($elem_foreignOs_Metallic == "Metallic w/rust run" || $elem_foreignOs_Metallic == "Metallic w/rust ring") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_Metallic"   class="aato">Metallic w/rust ring</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>" id="d_Corn_Foreign_Body1">
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_NonMetallic" name="elem_foreignOd_NonMetallic" value="Non Metallic" <?php echo ($elem_foreignOd_NonMetallic == "Non Metallic") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_NonMetallic"  >Non Metallic</label>
		</td><td colspan="1"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_Suture" name="elem_foreignOd_Suture" value="Suture" <?php echo ($elem_foreignOd_Suture == "Suture") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_Suture"  >Suture</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_Old" name="elem_foreignOd_Old" value="Old w/o staining" <?php echo ($elem_foreignOd_Old == "Old w/o staining") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_Old"   class="aato">Old w/o staining</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_NonMetallic" name="elem_foreignOs_NonMetallic" value="Non Metallic" <?php echo ($elem_foreignOs_NonMetallic == "Non Metallic") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_NonMetallic"  >Non Metallic</label>
		</td><td colspan="1"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_Suture" name="elem_foreignOs_Suture" value="Suture" <?php echo ($elem_foreignOs_Suture == "Suture") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_Suture"  >Suture</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_Old" name="elem_foreignOs_Old" value="Old w/o staining" <?php echo ($elem_foreignOs_Old == "Old w/o staining") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_Old"   class="aato">Old w/o staining</label>
		</td>
		</tr>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>" id="d_Corn_Foreign_Body2">
		<td align="left"></td>
		<td><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_Penetrating" name="elem_foreignOd_Penetrating" value="Penetrating" <?php echo ($elem_foreignOd_Penetrating == "Penetrating") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_Penetrating"  >Penetrating</label>
		</td><td colspan="7"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOd_NonPenetrating" name="elem_foreignOd_NonPenetrating" value="Non-Penetrating" <?php echo ($elem_foreignOd_NonPenetrating == "Non-Penetrating") ? "checked=checked" : "" ;?>><label for="elem_foreignOd_NonPenetrating"   class="aato">Non-Penetrating</label>
		</td>		
		
		<td align="left"></td>
		<td><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_Penetrating" name="elem_foreignOs_Penetrating" value="Penetrating" <?php echo ($elem_foreignOs_Penetrating == "Penetrating") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_Penetrating"  >Penetrating</label>
		</td><td colspan="7"><input type="checkbox"  onclick="checkAbsent(this,'CornTruma');" id="elem_foreignOs_NonPenetrating" name="elem_foreignOs_NonPenetrating" value="Non-Penetrating" <?php echo ($elem_foreignOs_NonPenetrating == "Non-Penetrating") ? "checked=checked" : "" ;?>><label for="elem_foreignOs_NonPenetrating"   class="aato">Non-Penetrating</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Trauma/Foreign Body"])){ echo $arr_exm_ext_htm["Cornea"]["Trauma/Foreign Body"]; }  ?>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>">
		<td align="left">Part. Thickness Laceration</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_partLacerationOd_Sup" name="elem_partLacerationOd_Sup" value="Superficial" <?php echo ($elem_partLacerationOd_Sup == "Superficial") ? "checked=checked" : "" ;?>><label for="elem_partLacerationOd_Sup"  >Superficial</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_partLacerationOd_Deep" name="elem_partLacerationOd_Deep" value="Deep" <?php echo ($elem_partLacerationOd_Deep == "Deep") ? "checked=checked" : "" ;?>><label for="elem_partLacerationOd_Deep"  >Deep</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_partLacerationOd_NegSiedel" name="elem_partLacerationOd_NegSiedel" value="-ve Siedel" <?php echo ($elem_partLacerationOd_NegSiedel == "-ve Siedel") ? "checked=checked" : "" ;?>><label for="elem_partLacerationOd_NegSiedel"  >-ve Siedel</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornTruma')" >BL</td>
		<td align="left">Part. Thickness Laceration</td>
		<td>
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_partLacerationOs_Sup" name="elem_partLacerationOs_Sup" value="Superficial" <?php echo ($elem_partLacerationOs_Sup == "Superficial") ? "checked=checked" : "" ;?>><label for="elem_partLacerationOs_Sup"  >Superficial</label>
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_partLacerationOs_Deep" name="elem_partLacerationOs_Deep" value="Deep" <?php echo ($elem_partLacerationOs_Deep == "Deep") ? "checked=checked" : "" ;?>><label for="elem_partLacerationOs_Deep"  >Deep</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_partLacerationOs_NegSiedel" name="elem_partLacerationOs_NegSiedel" value="-ve Siedel" <?php echo ($elem_partLacerationOs_NegSiedel == "-ve Siedel") ? "checked=checked" : "" ;?>><label for="elem_partLacerationOs_NegSiedel"  >-ve Siedel</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Trauma/Part. Thickness Laceration"])){ echo $arr_exm_ext_htm["Cornea"]["Trauma/Part. Thickness Laceration"]; }  ?>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>">
		<td align="left">Full Thickness Laceration</td>
		<td colspan="3">
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOd_WProlapsed" name="elem_fulltLacerationOd_WProlapsed" value="w/ uveal prolapsed" <?php echo ($elem_fulltLacerationOd_WProlapsed == "w/ uveal prolapsed") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOd_WProlapsed"   class="tr_wuvel">w/ uveal prolapsed</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOd_WOProlapsed" name="elem_fulltLacerationOd_WOProlapsed" value="w/o uveal prolapsed" <?php echo ($elem_fulltLacerationOd_WOProlapsed == "w/o uveal prolapsed") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOd_WOProlapsed"   class="aato">w/o uveal prolapsed</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornTruma')" rowspan="2" >BL</td>
		<td align="left">Full Thickness Laceration</td>
		<td colspan="3">
		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOs_WProlapsed" name="elem_fulltLacerationOs_WProlapsed" value="w/ uveal prolapsed" <?php echo ($elem_fulltLacerationOs_WProlapsed == "w/ uveal prolapsed") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOs_WProlapsed"   class="tr_wuvel">w/ uveal prolapsed</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOs_WOProlapsed" name="elem_fulltLacerationOs_WOProlapsed" value="w/o uveal prolapsed" <?php echo ($elem_fulltLacerationOs_WOProlapsed == "w/o uveal prolapsed") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOs_WOProlapsed"   class="aato">w/o uveal prolapsed</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_CornTruma <?php echo $cls_CornTruma; ?>">
		<td align="left"></td>
		<td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOd_PosSiedel" name="elem_fulltLacerationOd_PosSiedel" value="+ve Siedel" <?php echo ($elem_fulltLacerationOd_PosSiedel == "+ve Siedel") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOd_PosSiedel"  >+ve Siedel</label>	
		</td><td colspan="5"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOd_NegSiedel" name="elem_fulltLacerationOd_NegSiedel" value="-ve Siedel" <?php echo ($elem_fulltLacerationOd_NegSiedel == "-ve Siedel") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOd_NegSiedel"  >-ve Siedel</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOs_PosSiedel" name="elem_fulltLacerationOs_PosSiedel" value="+ve Siedel" <?php echo ($elem_fulltLacerationOs_PosSiedel == "+ve Siedel") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOs_PosSiedel"  >+ve Siedel</label>
		</td><td colspan="5"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornTruma');" id="elem_fulltLacerationOs_NegSiedel" name="elem_fulltLacerationOs_NegSiedel" value="-ve Siedel" <?php echo ($elem_fulltLacerationOs_NegSiedel == "-ve Siedel") ? "checked=checked" : "" ;?>><label for="elem_fulltLacerationOs_NegSiedel"  >-ve Siedel</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Trauma/Full Thickness Laceration"])){ echo $arr_exm_ext_htm["Cornea"]["Trauma/Full Thickness Laceration"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Trauma"])){ echo $arr_exm_ext_htm["Cornea"]["Trauma"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_Infect <?php echo $cls_Infect; ?>" id="d_Infect">
		<td align="left" class="grpbtn" colspan="2" onclick="openSubGrp('Infect')">			
			<label >Infection/Inflammation
			<span class="glyphicon <?php echo $arow_Infect; ?>"></span></label> 
		</td>
		<td colspan="7"></td>		
		<td align="center" class="bilat" onClick="check_bl('Infect')">BL</td>
		<td align="left" class="grpbtn" colspan="2" onclick="openSubGrp('Infect')">
			<label >Infection/Inflammation
			<span class="glyphicon <?php echo $arow_Infect; ?>"></span></label> 
		</td>
		<td colspan="7"></td>
		</tr>		
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left">Ulcer</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'Infect');"  id="od_113" name="elem_ulcerOd" class="form-control" ><?php echo ($elem_ulcerOd);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('Infect')" rowspan="6">BL</td>
		<td align="left">Ulcer</td>
		<td colspan="8"><textarea  onblur="checkwnls();checkSymClr(this,'Infect');"  id="os_113" name="elem_ulcerOs" class="form-control"><?php echo ($elem_ulcerOs);?></textarea></td>		
		</tr>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left"></td>
		<td colspan="2"><input id="od_114" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_infectiousOd" value="Infectious" <?php echo ($elem_infectiousOd == "Infectious") ? "checked=checked" : "" ;?>><label for="od_114"  >Infectious</label>
		</td><td ><input id="od_115" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_infiltrateOd" value="Infiltrate" <?php echo ($elem_infiltrateOd == "Infiltrate") ? "checked=checked" : "" ;?>><label for="od_115"  >Infiltrate</label>
		</td><td><input id="od_116" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_hazeOd" value="Haze" <?php echo ($elem_hazeOd == "Haze") ? "checked=checked" : "" ;?>><label for="od_116"  >Haze</label>
		</td><td colspan="4"><input id="od_117" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_neoroticOd" value="Neurotrophic" <?php echo ($elem_neoroticOd == "Neurotrophic") ? "checked=checked" : "" ;?>><label for="od_117"  >Neurotrophic</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="2"><input id="os_114" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_infectiousOs" value="Infectious" <?php echo ($elem_infectiousOs == "Infectious") ? "checked=checked" : "" ;?>><label for="os_114"  >Infectious</label>
		</td><td><input id="os_115" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_infiltrateOs" value="Infiltrate" <?php echo ($elem_infiltrateOs == "Infiltrate") ? "checked=checked" : "" ;?>><label for="os_115"  >Infiltrate</label>
		</td><td ><input id="os_116" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_hazeOs" value="Haze" <?php echo ($elem_hazeOs == "Haze") ? "checked=checked" : "" ;?>><label for="os_116"  >Haze</label>											
		</td><td colspan="4"><input id="os_117" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_neoroticOs" value="Neurotrophic" <?php echo ($elem_neoroticOs == "Neurotrophic") ? "checked=checked" : "" ;?>><label for="os_117"  >Neurotrophic</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left"></td>
		<td ><input id="od_118" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_kpsOd" value="KP&#39;s" <?php echo ($elem_kpsOd == "KP&#39;s" || $elem_kpsOd == "KP's") ? "checked=checked" : "" ;?>><label for="od_118"  >KP&#39;s</label>
		</td ><td colspan="7"><input id="od_119" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_endothelialOd" value="Endothelial Plaque" <?php echo ($elem_endothelialOd == "Endothelial Plaque") ? "checked=checked" : "" ;?>><label for="od_119"   class="aato">Endothelial Plaque</label>
		</td>		
		
		<td align="left"></td>
		<td ><input id="os_118" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_kpsOs" value="KP&#39;s" <?php echo ($elem_kpsOs == "KP&#39;s" || $elem_kpsOs == "KP's") ? "checked=checked" : "" ;?>><label for="os_118"  >KP&#39;s</label>
		</td ><td colspan="7"><input id="os_119" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_endothelialOs" value="Endothelial Plaque" <?php echo ($elem_endothelialOs == "Endothelial Plaque") ? "checked=checked" : "" ;?>><label for="os_119"   class="aato">Endothelial Plaque</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left"></td>
		<td ><input id="od_121" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_sterileOd" value="Sterile" <?php echo ($elem_sterileOd == "Sterile") ? "checked=checked" : "" ;?>><label for="od_121"  >Sterile</label> 
		</td><td colspan="7"><input id="od_122" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_peripheralHypersensitivityUlcerOd" value="Peripheral Hypersensitivity Ulcer" <?php echo ($elem_peripheralHypersensitivityUlcerOd == "Peripheral Hypersensitivity Ulcer") ? "checked=checked" : "" ;?>><label for="od_122"   class="aato">Peripheral Hypersensitivity Ulcer</label>
		</td>		
		
		<td align="left"></td>
		<td><input id="os_121" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_sterileOs" value="Sterile" <?php echo ($elem_sterileOs == "Sterile") ? "checked=checked" : "" ;?>><label for="os_121"  >Sterile </label>
		</td><td colspan="7"><input id="os_122" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_peripheralHypersensitivityUlcerOs" value="Peripheral Hypersensitivity Ulcer" <?php echo ($elem_peripheralHypersensitivityUlcerOs == "Peripheral Hypersensitivity Ulcer") ? "checked=checked" : "" ;?>><label for="os_122"   class="aato">Peripheral Hypersensitivity Ulcer</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left"></td>
		<td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_Location" name="elem_ulcerOd_Location" value="Location" <?php echo ($elem_ulcerOd_Location == "Location") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_Location"  >Location</label> 
		</td><td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_Peripheral" name="elem_ulcerOd_Peripheral" value="Peripheral" <?php echo ($elem_ulcerOd_Peripheral == "Peripheral") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_Peripheral"  >Peripheral</label> 
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_MidPeripheral" name="elem_ulcerOd_MidPeripheral" value="Mid Peripheral" <?php echo ($elem_ulcerOd_MidPeripheral == "Mid Peripheral") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_MidPeripheral"  >Mid Peripheral</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_Central" name="elem_ulcerOd_Central" value="Central" <?php echo ($elem_ulcerOd_Central == "Central") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_Central"  >Central</label> 
		</td>		
		
		<td align="left"></td>
		<td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_Location" name="elem_ulcerOs_Location" value="Location" <?php echo ($elem_ulcerOs_Location == "Location") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_Location"  >Location</label> 
		</td><td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_Peripheral" name="elem_ulcerOs_Peripheral" value="Peripheral" <?php echo ($elem_ulcerOs_Peripheral == "Peripheral") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_Peripheral"  >Peripheral</label> 
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_MidPeripheral" name="elem_ulcerOs_MidPeripheral" value="Mid Peripheral" <?php echo ($elem_ulcerOs_MidPeripheral == "Mid Peripheral") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_MidPeripheral"  >Mid Peripheral</label> 
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_Central" name="elem_ulcerOs_Central" value="Central" <?php echo ($elem_ulcerOs_Central == "Central") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_Central"  >Central</label> 
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left"></td>
		<td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_Progression" name="elem_ulcerOd_Progression" value="Progression" <?php echo ($elem_ulcerOd_Progression == "Progression") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_Progression"  >Progression</label> 
		</td><td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_Improving" name="elem_ulcerOd_Improving" value="Improving" <?php echo ($elem_ulcerOd_Improving == "Improving") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_Improving"  >Improving</label> 
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_Worsening" name="elem_ulcerOd_Worsening" value="Worsening" <?php echo ($elem_ulcerOd_Worsening == "Worsening") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_Worsening"  >Worsening</label> 
		</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOd_NoChange" name="elem_ulcerOd_NoChange" value="No Change" <?php echo ($elem_ulcerOd_NoChange == "No Change") ? "checked=checked" : "" ;?>><label for="elem_ulcerOd_NoChange"  >No Change</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_Progression" name="elem_ulcerOs_Progression" value="Progression" <?php echo ($elem_ulcerOs_Progression == "Progression") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_Progression"  >Progression</label> 
		</td><td colspan="1"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_Improving" name="elem_ulcerOs_Improving" value="Improving" <?php echo ($elem_ulcerOs_Improving == "Improving") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_Improving"  >Improving</label> 
		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_Worsening" name="elem_ulcerOs_Worsening" value="Worsening" <?php echo ($elem_ulcerOs_Worsening == "Worsening") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_Worsening"  >Worsening</label> 
		</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_ulcerOs_NoChange" name="elem_ulcerOs_NoChange" value="No Change" <?php echo ($elem_ulcerOs_NoChange == "No Change") ? "checked=checked" : "" ;?>><label for="elem_ulcerOs_NoChange"  >No Change</label> 
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Infection/Inflammation/Ulcer"])){ echo $arr_exm_ext_htm["Cornea"]["Infection/Inflammation/Ulcer"]; }  ?>
		
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left">Stromal Abscess</td>
		<td colspan="8"><input id="elem_stromalAbscessOd_neg" name="elem_stromalAbscessOd_neg" type="checkbox"  onclick="checkAbsent(this,'Infect');" value="Absent" <?php echo ($elem_stromalAbscessOd_neg == "-ve" || $elem_stromalAbscessOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_stromalAbscessOd_neg"  >Absent</label></td>
		<td align="center" class="bilat" onClick="check_bl('Infect')" >BL</td>
		<td align="left">Stromal Abscess</td>
		<td colspan="8"><input id="elem_stromalAbscessOs_neg" name="elem_stromalAbscessOs_neg" type="checkbox"  onclick="checkAbsent(this,'Infect');" value="Absent" <?php echo ($elem_stromalAbscessOs_neg == "-ve" || $elem_stromalAbscessOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="elem_stromalAbscessOs_neg"  >Absent</label></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Infection/Inflammation/Stromal Abscess"])){ echo $arr_exm_ext_htm["Cornea"]["Infection/Inflammation/Stromal Abscess"]; }  ?>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left">HSK</td>
		<td colspan="2">
		<input id="od_124" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_hskDendriteOd" value="Dendrite" <?php echo ($elem_hskDendriteOd == "Dendrite") ? "checked=checked" : "" ;?>><label for="od_124"   class="inf_hsk">Dendrite</label>
		</td><td colspan="3"><input id="od_125" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_hskGeoUlcerOd" value="Geographic Ulcer" <?php echo ($elem_hskGeoUlcerOd == "Geographic Ulcer") ? "checked=checked" : "" ;?>><label for="od_125"   class="inf_hsk">Geographic Ulcer</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_hskOd_Scar" name="elem_hskOd_Scar" value="Scar" <?php echo ($elem_hskOd_Scar == "Scar") ? "checked=checked" : "" ;?>><label for="elem_hskOd_Scar"  >Scar</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Infect')" >BL</td>
		<td align="left">HSK</td>
		<td colspan="2">
		<input id="os_124" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_hskDendriteOs" value="Dendrite" <?php echo ($elem_hskDendriteOs == "Dendrite") ? "checked=checked" : "" ;?>><label for="os_124"   class="inf_hsk">Dendrite</label>
		</td><td colspan="3"><input id="os_125" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" name="elem_hskGeoUlcerOs" value="Geographic Ulcer" <?php echo ($elem_hskGeoUlcerOs == "Geographic Ulcer") ? "checked=checked" : "" ;?>><label for="os_125"   class="inf_hsk">Geographic Ulcer</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_hskOs_Scar" name="elem_hskOs_Scar" value="Scar" <?php echo ($elem_hskOs_Scar == "Scar") ? "checked=checked" : "" ;?>><label for="elem_hskOs_Scar"  >Scar</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Infection/Inflammation/HSK"])){ echo $arr_exm_ext_htm["Cornea"]["Infection/Inflammation/HSK"]; }  ?>
		
		<tr class="exmhlgcol grp_Infect <?php echo $cls_Infect; ?>">
		<td align="left">HZK</td>
		<td colspan="2">
		<input id="od_126" name="elem_hzkPseudo_DendriteOd" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" value="Pseudo Dendrite" <?php echo ($elem_hzkPseudo_DendriteOd == "Pseudo Dendrite") ? "checked=checked" : "" ;?>><label for="od_126"   class="inf_hsk">Pseudo Dendrite</label>
		</td><td colspan="3"><input id="od_127" name="elem_hzkGeoUlcerOd" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" value="Geographic Ulcer" <?php echo ($elem_hzkGeoUlcerOd == "Geographic Ulcer") ? "checked=checked" : "" ;?>><label for="od_127"   class="inf_hsk">Geographic Ulcer</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_hzkOd_Scar" name="elem_hzkOd_Scar" value="Scar" <?php echo ($elem_hzkOd_Scar == "Scar") ? "checked=checked" : "" ;?>><label for="elem_hzkOd_Scar"  >Scar</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Infect')" >BL</td>
		<td align="left">HZK</td>
		<td colspan="2">
		<input id="os_126" name="elem_hzkPseudo_DendriteOs" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" value="Pseudo Dendrite" <?php echo ($elem_hzkPseudo_DendriteOs == "Pseudo Dendrite") ? "checked=checked" : "" ;?> ><label for="os_126"   class="inf_hsk">Pseudo Dendrite</label>
		</td><td colspan="3"><input id="os_127" name="elem_hzkGeoUlcerOs" type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" value="Geographic Ulcer" <?php echo ($elem_hzkGeoUlcerOs == "Geographic Ulcer") ? "checked=checked" : "" ;?> ><label for="os_127"   class="inf_hsk">Geographic Ulcer</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'Infect');" id="elem_hzkOs_Scar" name="elem_hzkOs_Scar" value="Scar" <?php echo ($elem_hzkOs_Scar == "Scar") ? "checked=checked" : "" ;?>><label for="elem_hzkOs_Scar"  >Scar</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Infection/Inflammation/HZK"])){ echo $arr_exm_ext_htm["Cornea"]["Infection/Inflammation/HZK"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Infection/Inflammation"])){ echo $arr_exm_ext_htm["Cornea"]["Infection/Inflammation"]; }  ?>
		
		<tr id="d_Ptery" class="grp_Ptery" >
		<td align="left">Pterygium</td>
		<td>
		<input id="od_128" type="checkbox"  onclick="checkwnls()" name="elem_pterygium1mmOd" value="1mm" <?php echo ($elem_pterygium1mmOd == "1mm") ? "checked=checked" : "" ;?> ><label for="od_128"  >1mm</label>
		</td><td><input id="od_129" type="checkbox"  onclick="checkwnls()" name="elem_pterygium2mmOd" value="2mm" <?php echo ($elem_pterygium2mmOd == "2mm") ? "checked=checked" : "" ;?> ><label for="od_129"  >2mm</label>
		</td><td><input id="od_130" type="checkbox"  onclick="checkwnls()" name="elem_pterygium3mmOd" value="3mm" <?php echo ($elem_pterygium3mmOd == "3mm") ? "checked=checked" : "" ;?> ><label for="od_130"  >3mm</label>
		</td><td colspan="2"><input id="od_131" type="checkbox"  onclick="checkwnls()" name="elem_pterygium4mmOd" value="4mm" <?php echo ($elem_pterygium4mmOd == "4mm") ? "checked=checked" : "" ;?> ><label for="od_131"  >4mm</label>
		</td><td colspan="2"><input id="od_132" type="checkbox"  onclick="checkwnls()" name="elem_pterygium5mmOd" value="5mm" <?php echo ($elem_pterygium5mmOd == "5mm") ? "checked=checked" : "" ;?> ><label for="od_132"  >5mm</label>
		</td>		
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('Ptery')" rowspan="2">BL</td>
		<td align="left">Pterygium</td>
		<td>
		<input id="os_128" type="checkbox"  onclick="checkwnls()" name="elem_pterygium1mmOs" value="1mm" <?php echo ($elem_pterygium1mmOs == "1mm") ? "checked=checked" : "" ;?> ><label for="os_128"  >1mm</label>
		</td><td><input id="os_129" type="checkbox"  onclick="checkwnls()" name="elem_pterygium2mmOs" value="2mm" <?php echo ($elem_pterygium2mmOs == "2mm") ? "checked=checked" : "" ;?> ><label for="os_129"  >2mm</label>
		</td><td><input id="os_130" type="checkbox"  onclick="checkwnls()" name="elem_pterygium3mmOs" value="3mm" <?php echo ($elem_pterygium3mmOs == "3mm") ? "checked=checked" : "" ;?> ><label for="os_130"  >3mm</label>
		</td><td colspan="2"><input id="os_131" type="checkbox"  onclick="checkwnls()" name="elem_pterygium4mmOs" value="4mm" <?php echo ($elem_pterygium4mmOs == "4mm") ? "checked=checked" : "" ;?> ><label for="os_131"  >4mm</label>
		</td><td colspan="2"><input id="os_132" type="checkbox"  onclick="checkwnls()" name="elem_pterygium5mmOs" value="5mm" <?php echo ($elem_pterygium5mmOs == "5mm") ? "checked=checked" : "" ;?> ><label for="os_132"  >5mm</label>
		</td>		
		<td></td>
		</tr>
		
		<tr class="grp_Ptery">
		<td align="left"></td>
		<td><input id="od_133" type="checkbox"  onclick="checkwnls()" name="elem_pterygiumNasalOd" value="Nasal" <?php echo ($elem_pterygiumNasalOd == "Nasal") ? "checked=checked" : "" ;?> ><label for="od_133"  >Nasal</label> 
		</td><td colspan="2"><input id="od_134" type="checkbox"  onclick="checkwnls()" name="elem_pterygiumTemporalOd" value="Temporal" <?php echo ($elem_pterygiumTemporalOd == "Temporal") ? "checked=checked" : "" ;?>><label for="od_134"   class="ptry_temp">Temporal</label>
		</td><td colspan="5"><input id="elem_pterygiumEncPupOd" type="checkbox"  onclick="checkwnls()" id="elem_pterygiumEncPupOd" name="elem_pterygiumEncPupOd" value="Encroaching Pupil" <?php echo ($elem_pterygiumEncPupOd == "Encroaching Pupil") ? "checked=checked" : "" ;?>><label for="elem_pterygiumEncPupOd"   class="aato">Encroaching Pupil</label>
		</td>		
		
		<td align="left"></td>
		<td><input id="os_133" type="checkbox"  onclick="checkwnls()" name="elem_pterygiumNasalOs" value="Nasal" <?php echo ($elem_pterygiumNasalOs == "Nasal") ? "checked=checked" : "" ;?> ><label for="os_133"  >Nasal</label> 
		</td><td colspan="2"><input id="os_134" type="checkbox"  onclick="checkwnls()" name="elem_pterygiumTemporalOs" value="Temporal" <?php echo ($elem_pterygiumTemporalOs == "Temporal") ? "checked=checked" : "" ;?>><label for="os_134"   class="ptry_temp">Temporal</label>
		</td><td colspan="5"><input id="elem_pterygiumEncPupOs" type="checkbox"  onclick="checkwnls()" id="elem_pterygiumEncPupOs" name="elem_pterygiumEncPupOs" value="Encroaching Pupil" <?php echo ($elem_pterygiumEncPupOs == "Encroaching Pupil") ? "checked=checked" : "" ;?>><label for="elem_pterygiumEncPupOs"   class="aato">Encroaching Pupil</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Pterygium"])){ echo $arr_exm_ext_htm["Cornea"]["Pterygium"]; }  ?>
		
		<tr class="exmhlgcol grp_handle grp_CornEdma <?php echo $cls_CornEdma; ?>" id="d_CornEdma">
		<td align="left" class="grpbtn" onclick="openSubGrp('CornEdma')">			
			<label >Edema 
			<span class="glyphicon <?php echo $arow_CornEdma; ?>"></span></label>
		</td>
		<td>
		<input id="od_135" type="text" onBlur="checkAbsent(this,'CornEdma');" class="text_box50 txt_10 form-control" name="elem_edemaOd_text" value="<?php echo ($elem_edemaOd_text);?>">
		</td><td><input id="od_136" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOd_neg" value="Absent" <?php echo ($elem_edemaOd_neg == "-ve" || $elem_edemaOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_136"  >Absent</label>
		</td><td><input id="od_137" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOd_T" value="T" <?php echo ($elem_edemaOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_137"  >T</label>
		</td><td><input id="od_138" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOd_pos1" value="1+" <?php echo ($elem_edemaOd_pos1 == "+1" || $elem_edemaOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_138"  >1+</label>
		</td><td><input id="od_139" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOd_pos2" value="2+" <?php echo ($elem_edemaOd_pos2 == "+2" || $elem_edemaOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_139"  >2+</label>
		</td><td><input id="od_140" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOd_pos3" value="3+" <?php echo ($elem_edemaOd_pos3 == "+3" || $elem_edemaOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_140"  >3+</label>
		</td><td colspan="2"><input id="od_141" type="checkbox" onClick="checkAbsent(this,'CornEdma');" name="elem_edemaOd_pos4" value="4+" <?php echo ($elem_edemaOd_pos4 == "+4" || $elem_edemaOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_141"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornEdma')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('CornEdma')">
			<label >Edema 
			<span class="glyphicon <?php echo $arow_CornEdma; ?>"></span></label>
		</td>
		<td>
		<input id="os_135" onBlur="checkAbsent(this,'CornEdma');" type="text" class="text_box50 txt_10 form-control" name="elem_edemaOs_text" value="<?php echo ($elem_edemaOs_text);?>">
		</td><td><input id="os_136" type="checkbox"   onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOs_neg" value="Absent" <?php echo ($elem_edemaOs_neg == "-ve" || $elem_edemaOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_136"  >Absent</label>
		</td><td><input id="os_137" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOs_T" value="T" <?php echo ($elem_edemaOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_137"  >T</label>
		</td><td><input id="os_138" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOs_pos1" value="1+" <?php echo ($elem_edemaOs_pos1 == "+1" || $elem_edemaOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_138"  >1+</label>
		</td><td><input id="os_139" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOs_pos2" value="2+" <?php echo ($elem_edemaOs_pos2 == "+2" || $elem_edemaOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_139"  >2+</label>
		</td><td><input id="os_140" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOs_pos3" value="3+" <?php echo ($elem_edemaOs_pos3 == "+3" || $elem_edemaOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_140"  >3+</label>
		</td><td colspan="2"><input id="os_141" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaOs_pos4" value="4+" <?php echo ($elem_edemaOs_pos4 == "+4" || $elem_edemaOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_141"  >4+</label>
		</td>		
		</tr>		
		
		<tr class="exmhlgcol grp_CornEdma <?php echo $cls_CornEdma; ?>">
		<td align="left">Epithelial (MCE)</td>
		<td>
		<input id="od_142" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOd_neg" value="Absent" <?php echo ($elem_epithelialOd_neg == "-ve" || $elem_epithelialOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_142"  >Absent</label>
		</td><td><input id="od_143" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOd_T" value="T" <?php echo ($elem_epithelialOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_143"  >T</label>
		</td><td><input id="od_144" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOd_pos1" value="1+" <?php echo ($elem_epithelialOd_pos1 == "+1" || $elem_epithelialOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_144"  >1+</label>
		</td><td><input id="od_145" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOd_pos2" value="2+" <?php echo ($elem_epithelialOd_pos2 == "+2" || $elem_epithelialOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_145"  >2+</label>
		</td><td colspan="2"><input id="od_146" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOd_pos3" value="3+" <?php echo ($elem_epithelialOd_pos3 == "+3" || $elem_epithelialOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_146"  >3+</label>
		</td><td colspan="2"><input id="od_147" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOd_pos4" value="4+" <?php echo ($elem_epithelialOd_pos4 == "+4" || $elem_epithelialOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_147"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornEdma')" >BL</td>
		<td align="left">Epithelial (MCE)</td>
		<td>
		<input id="os_142" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOs_neg" value="Absent" <?php echo ($elem_epithelialOs_neg == "-ve" || $elem_epithelialOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_142"  >Absent</label>
		</td><td><input id="os_143" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOs_T" value="T" <?php echo ($elem_epithelialOs_T == "T" || $elem_epithelialOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_143"  >T</label>
		</td><td><input id="os_144" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOs_pos1" value="1+" <?php echo ($elem_epithelialOs_pos1 == "+1" || $elem_epithelialOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_144"  >1+</label>
		</td><td><input id="os_145" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOs_pos2" value="2+" <?php echo ($elem_epithelialOs_pos2 == "+2" || $elem_epithelialOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_145"  >2+</label>
		</td><td colspan="2"><input id="os_146" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOs_pos3" value="3+" <?php echo ($elem_epithelialOs_pos3 == "+3" || $elem_epithelialOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_146"  >3+</label>
		</td><td colspan="2"><input id="os_147" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_epithelialOs_pos4" value="4+" <?php echo ($elem_epithelialOs_pos4 == "+4" || $elem_epithelialOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_147"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Edema/Epithelial (MCE)"])){ echo $arr_exm_ext_htm["Cornea"]["Edema/Epithelial (MCE)"]; }  ?>
		
		<tr class="exmhlgcol grp_CornEdma <?php echo $cls_CornEdma; ?>">
		<td align="left">Stromal</td>
		<td>        
		<input id="od_148" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOd_neg" value="Absent" <?php echo ($elem_edemaStromalOd_neg == "-ve" || $elem_edemaStromalOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_148"  >Absent</label>
		</td><td><input id="od_149" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOd_T" value="T" <?php echo ($elem_edemaStromalOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_149"  >T</label>
		</td><td><input id="od_150" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOd_pos1" value="1+" <?php echo ($elem_edemaStromalOd_pos1 == "+1" || $elem_edemaStromalOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_150"  >1+</label>
		</td><td><input id="od_151" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOd_pos2" value="2+" <?php echo ($elem_edemaStromalOd_pos2 == "+2" || $elem_edemaStromalOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_151"  >2+</label>
		</td><td colspan="2"><input id="od_152" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOd_pos3" value="3+" <?php echo ($elem_edemaStromalOd_pos3 == "+3" || $elem_edemaStromalOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_152"  >3+</label>
		</td><td colspan="2"><input id="od_153" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOd_pos4" value="4+" <?php echo ($elem_edemaStromalOd_pos4 == "+4" || $elem_edemaStromalOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_153"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornEdma')" >BL</td>
		<td align="left">Stromal</td>
		<td>
		<input id="os_148" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOs_neg" value="Absent" <?php echo ($elem_edemaStromalOs_neg == "-ve" || $elem_edemaStromalOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_148"  >Absent</label>
		</td><td><input id="os_149" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOs_T" value="T" <?php echo ($elem_edemaStromalOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_149"  >T</label>
		</td><td><input id="os_150" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOs_pos1" value="1+" <?php echo ($elem_edemaStromalOs_pos1 == "+1" || $elem_edemaStromalOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_150"  >1+</label>
		</td><td><input id="os_151" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOs_pos2" value="2+" <?php echo ($elem_edemaStromalOs_pos2 == "+2" || $elem_edemaStromalOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_151"  >2+</label>
		</td><td colspan="2"><input id="os_152" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOs_pos3" value="3+" <?php echo ($elem_edemaStromalOs_pos3 == "+3" || $elem_edemaStromalOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_152"  >3+</label>
		</td><td colspan="2"><input id="os_153" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_edemaStromalOs_pos4" value="4+" <?php echo ($elem_edemaStromalOs_pos4 == "+4" || $elem_edemaStromalOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_153"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Edema/Stromal"])){ echo $arr_exm_ext_htm["Cornea"]["Edema/Stromal"]; }  ?>
		
		
		<tr class="exmhlgcol grp_CornEdma <?php echo $cls_CornEdma; ?>">
		<td align="left">Folds/Striae</td>
		<td>        
		<input id="od_154" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOd_neg" value="Absent" <?php echo ($elem_foldsStriaOd_neg == "-ve" || $elem_foldsStriaOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_154"  >Absent</label>
		</td><td><input  id="od_155" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOd_T" value="T" <?php echo ($elem_foldsStriaOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_155"  >T</label>
		</td><td><input  id="od_156" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOd_pos1" value="1+" <?php echo ($elem_foldsStriaOd_pos1 == "+1" || $elem_foldsStriaOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_156"  >1+</label>
		</td><td><input  id="od_157" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOd_pos2" value="2+" <?php echo ($elem_foldsStriaOd_pos2 == "+2" || $elem_foldsStriaOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_157"  >2+</label>
		</td><td colspan="2"><input  id="od_158" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOd_pos3" value="3+" <?php echo ($elem_foldsStriaOd_pos3 == "+3" || $elem_foldsStriaOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_158"  >3+</label>
		</td><td colspan="2"><input  id="od_159" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOd_pos4" value="4+" <?php echo ($elem_foldsStriaOd_pos4 == "+4" || $elem_foldsStriaOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_159"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornEdma')" >BL</td>
		<td align="left">Folds/Striae</td>
		<td>
		<input  id="os_154" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOs_neg" value="Absent" <?php echo ($elem_foldsStriaOs_neg == "-ve" || $elem_foldsStriaOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_154"  >Absent</label>
		</td><td><input id="os_155" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOs_T" value="T" <?php echo ($elem_foldsStriaOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_155"  >T</label>
		</td><td><input id="os_156" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOs_pos1" value="1+" <?php echo ($elem_foldsStriaOs_pos1 == "+1" || $elem_foldsStriaOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_156"  >1+</label>
		</td><td><input id="os_157" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOs_pos2" value="2+" <?php echo ($elem_foldsStriaOs_pos2 == "+2" || $elem_foldsStriaOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_157"  >2+</label>
		</td><td colspan="2"><input id="os_158" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOs_pos3" value="3+" <?php echo ($elem_foldsStriaOs_pos3 == "+3" || $elem_foldsStriaOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_158"  >3+</label>
		</td><td colspan="2"><input id="os_159" type="checkbox"  onclick="checkAbsent(this,'CornEdma');" name="elem_foldsStriaOs_pos4" value="4+" <?php echo ($elem_foldsStriaOs_pos4 == "+4" || $elem_foldsStriaOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_159"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Edema/Folds/Striae"])){ echo $arr_exm_ext_htm["Cornea"]["Edema/Folds/Striae"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Edema"])){ echo $arr_exm_ext_htm["Cornea"]["Edema"]; }  ?>
		
		
		<tr class="exmhlgcol grp_handle grp_PigDepo <?php echo $cls_PigDepo; ?>" id="d_PigDepo" >
		<td align="left" class="grpbtn" onclick="openSubGrp('PigDepo')">			
			<label >Pigmentary deposits
			<span class="glyphicon <?php echo $arow_PigDepo; ?>"></span></label> 
		</td>
		<td>
		<input id="od_160" onBlur="checkAbsent(this,'PigDepo');" type="text" class="text_box50 txt_10 form-control" name="elem_pigmentaryDepositsOd_text" value="<?php echo ($elem_pigmentaryDepositsOd_text);?>">
		</td><td><input id="od_161" type="checkbox"  onclick="checkAbsent(this,'PigDepo');"  name="elem_pigmentaryDepositsOd_neg" value="Absent" <?php echo ($elem_pigmentaryDepositsOd_neg == "-ve" || $elem_pigmentaryDepositsOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_161"  >Absent</label>
		</td><td><input id="od_162" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOd_T" value="T" <?php echo ($elem_pigmentaryDepositsOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_162"  >T</label>
		</td><td><input id="od_163" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOd_pos1" value="1+" <?php echo ($elem_pigmentaryDepositsOd_pos1 == "+1" || $elem_pigmentaryDepositsOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_163"  >1+</label>
		</td><td><input id="od_164" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOd_pos2" value="2+" <?php echo ($elem_pigmentaryDepositsOd_pos2 == "+2" || $elem_pigmentaryDepositsOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_164"  >2+</label>
		</td><td><input id="od_165" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOd_pos3" value="3+" <?php echo ($elem_pigmentaryDepositsOd_pos3 == "+3" || $elem_pigmentaryDepositsOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_165"  >3+</label>
		</td><td colspan="2"><input id="od_166" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOd_pos4" value="4+" <?php echo ($elem_pigmentaryDepositsOd_pos4 == "+4" || $elem_pigmentaryDepositsOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_166"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('PigDepo')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('PigDepo')">
			<label >Pigmentary deposits
			<span class="glyphicon <?php echo $arow_PigDepo; ?>"></span></label> 
		</td>
		<td>
		<input id="os_160" onBlur="checkAbsent(this,'PigDepo');" type="text" class="text_box50 txt_10 form-control" name="elem_pigmentaryDepositsOs_text" value="<?php echo ($elem_pigmentaryDepositsOs_text);?>" >
		</td><td><input id="os_161" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOs_neg" value="Absent" <?php echo ($elem_pigmentaryDepositsOs_neg == "-ve" || $elem_pigmentaryDepositsOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_161"  >Absent</label>
		</td><td><input id="os_162" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOs_T" value="T" <?php echo ($elem_pigmentaryDepositsOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_162"  >T</label>
		</td><td><input id="os_163" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOs_pos1" value="1+" <?php echo ($elem_pigmentaryDepositsOs_pos1 == "+1" || $elem_pigmentaryDepositsOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_163"  >1+</label>
		</td><td><input id="os_164" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOs_pos2" value="2+" <?php echo ($elem_pigmentaryDepositsOs_pos2 == "+2" || $elem_pigmentaryDepositsOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_164"  >2+</label>
		</td><td><input id="os_165" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOs_pos3" value="3+" <?php echo ($elem_pigmentaryDepositsOs_pos3 == "+3" || $elem_pigmentaryDepositsOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_165"  >3+</label>
		</td><td colspan="2"><input id="os_166" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_pigmentaryDepositsOs_pos4" value="4+" <?php echo ($elem_pigmentaryDepositsOs_pos4 == "+4" || $elem_pigmentaryDepositsOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_166"  >4+</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_PigDepo <?php echo $cls_PigDepo; ?>">
		<td align="left">Vortex</td>
		<td>
		<input id="od_167" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOd_neg" value="Absent" <?php echo ($elem_vortexOd_neg == "-ve" || $elem_vortexOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_167"  >Absent</label>
		</td><td><input id="od_168" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOd_T" value="T" <?php echo ($elem_vortexOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_168"  >T</label>
		</td><td><input id="od_169" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOd_pos1" value="1+" <?php echo ($elem_vortexOd_pos1 == "+1" || $elem_vortexOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_169"  >1+</label>
		</td><td><input id="od_170" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOd_pos2" value="2+" <?php echo ($elem_vortexOd_pos2 == "+2" || $elem_vortexOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_170"  >2+</label>
		</td><td colspan="2"><input id="od_171" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOd_pos3" value="3+" <?php echo ($elem_vortexOd_pos3 == "+3" || $elem_vortexOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_171"  >3+</label>
		</td><td colspan="2"><input id="od_172" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOd_pos4" value="4+" <?php echo ($elem_vortexOd_pos4 == "+4" || $elem_vortexOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_172"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('PigDepo')" rowspan="2">BL</td>
		<td align="left">Vortex</td>
		<td>
		<input id="os_167" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOs_neg" value="Absent" <?php echo ($elem_vortexOs_neg == "-ve" || $elem_vortexOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_167"  >Absent</label>
		</td><td><input id="os_168" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOs_T" value="T" <?php echo ($elem_vortexOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_168"  >T</label>
		</td><td><input id="os_169" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOs_pos1" value="1+" <?php echo ($elem_vortexOs_pos1 == "+1" || $elem_vortexOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_169"  >1+</label>
		</td><td><input id="os_170" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOs_pos2" value="2+" <?php echo ($elem_vortexOs_pos2 == "+2" || $elem_vortexOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_170"  >2+</label>
		</td><td colspan="2"><input id="os_171" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOs_pos3" value="3+" <?php echo ($elem_vortexOs_pos3 == "+3" || $elem_vortexOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_171"  >3+</label>
		</td><td colspan="2"><input id="os_172" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_vortexOs_pos4" value="4+" <?php echo ($elem_vortexOs_pos4 == "+4" || $elem_vortexOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_172"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Pigmentary deposits/Vortex"])){ echo $arr_exm_ext_htm["Cornea"]["Pigmentary deposits/Vortex"]; }  ?>
		
		<tr class="exmhlgcol grp_PigDepo <?php echo $cls_PigDepo; ?>">
		<td align="left">K-Spindle</td>
		<td>
		<input id="od_173" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOd_neg" value="Absent" <?php echo ($elem_kSpindleOd_neg == "-ve" || $elem_kSpindleOd_neg == "Absent") ? "checked=checked" : "" ;?>><label for="od_173"  >Absent</label>
		</td><td><input id="od_174" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOd_T" value="T" <?php echo ($elem_kSpindleOd_T == "T") ? "checked=checked" : "" ;?>><label for="od_174"  >T</label>
		</td><td><input id="od_175" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOd_pos1" value="1+" <?php echo ($elem_kSpindleOd_pos1 == "+1" || $elem_kSpindleOd_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="od_175"  >1+</label>
		</td><td><input id="od_176" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOd_pos2" value="2+" <?php echo ($elem_kSpindleOd_pos2 == "+2" || $elem_kSpindleOd_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="od_176"  >2+</label>
		</td><td colspan="2"><input id="od_177" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOd_pos3" value="3+" <?php echo ($elem_kSpindleOd_pos3 == "+3" || $elem_kSpindleOd_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="od_177"  >3+</label>
		</td><td colspan="2"><input id="od_178" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOd_pos4" value="4+" <?php echo ($elem_kSpindleOd_pos4 == "+4" || $elem_kSpindleOd_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="od_178"  >4+</label>
		</td>		
		
		<td align="left">K-Spindle</td>
		<td>
		<input id="os_173" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOs_neg" value="Absent" <?php echo ($elem_kSpindleOs_neg == "-ve" || $elem_kSpindleOs_neg == "Absent") ? "checked=checked" : "" ;?>><label for="os_173"  >Absent</label>
		</td><td><input id="os_174" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOs_T" value="T" <?php echo ($elem_kSpindleOs_T == "T") ? "checked=checked" : "" ;?>><label for="os_174"  >T</label>
		</td><td><input id="os_175" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOs_pos1" value="1+" <?php echo ($elem_kSpindleOs_pos1 == "+1" || $elem_kSpindleOs_pos1 == "1+") ? "checked=checked" : "" ;?>><label for="os_175"  >1+</label>
		</td><td><input id="os_176" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOs_pos2" value="2+" <?php echo ($elem_kSpindleOs_pos2 == "+2" || $elem_kSpindleOs_pos2 == "2+") ? "checked=checked" : "" ;?>><label for="os_176"  >2+</label>
		</td><td colspan="2"><input id="os_177" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOs_pos3" value="3+" <?php echo ($elem_kSpindleOs_pos3 == "+3" || $elem_kSpindleOs_pos3 == "3+") ? "checked=checked" : "" ;?>><label for="os_177"  >3+</label>
		</td><td colspan="2"><input id="os_178" type="checkbox"  onclick="checkAbsent(this,'PigDepo');" name="elem_kSpindleOs_pos4" value="4+" <?php echo ($elem_kSpindleOs_pos4 == "+4" || $elem_kSpindleOs_pos4 == "4+") ? "checked=checked" : "" ;?>><label for="os_178"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Pigmentary deposits/K-Spindle"])){ echo $arr_exm_ext_htm["Cornea"]["Pigmentary deposits/K-Spindle"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Pigmentary deposits"])){ echo $arr_exm_ext_htm["Cornea"]["Pigmentary deposits"]; }  ?>
		
		<tr id="d_FilaK">
		<td align="left">Filamentary Keratitis</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_filaKOd_mild" name="elem_filaKOd_mild" value="Mild" 
			<?php echo ($elem_filaKOd_mild == "Mild") ? "checked=checked" : "" ;?>><label for="elem_filaKOd_mild"  >Mild</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls()" id="elem_filaKOd_mod" name="elem_filaKOd_mod" value="Moderate" 
			<?php echo ($elem_filaKOd_mod == "Moderate") ? "checked=checked" : "" ;?>><label for="elem_filaKOd_mod"   class="filak_mod">Moderate</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkwnls()" id="elem_filaKOd_severe" name="elem_filaKOd_severe" value="Severe" 
			<?php echo ($elem_filaKOd_severe == "Severe") ? "checked=checked" : "" ;?>><label for="elem_filaKOd_severe"  >Severe</label>        
		</td>		
		<td align="center" class="bilat" onClick="check_bl('FilaK')" >BL</td>
		<td align="left">Filamentary Keratitis</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_filaKOs_mild" name="elem_filaKOs_mild" value="Mild" 
			<?php echo ($elem_filaKOs_mild == "Mild") ? "checked=checked" : "" ;?>><label for="elem_filaKOs_mild"  >Mild</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls()" id="elem_filaKOs_mod" name="elem_filaKOs_mod" value="Moderate" 
			<?php echo ($elem_filaKOs_mod == "Moderate") ? "checked=checked" : "" ;?>><label for="elem_filaKOs_mod"   class="filak_mod">Moderate</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkwnls()" id="elem_filaKOs_severe" name="elem_filaKOs_severe" value="Severe" 
			<?php echo ($elem_filaKOs_severe == "Severe") ? "checked=checked" : "" ;?>><label for="elem_filaKOs_severe"  >Severe</label>
		</td>		
		</tr>	
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Filamentary Keratitis"])){ echo $arr_exm_ext_htm["Cornea"]["Filamentary Keratitis"]; }  ?>		
		
		<tr id="d_ConLens">
		<td align="left">Contact Lens</td>
		<td>
		<input id="od_179" type="checkbox"  onclick="checkwnls()" name="elem_sclOd" value="SCL" <?php echo ($elem_sclOd == "SCL") ? "checked=checked" : "" ;?>><label for="od_179"  >SCL</label>
		</td><td><input id="od_180" type="checkbox"  onclick="checkwnls()" name="elem_msclOd" value="B-SCL" <?php echo ($elem_msclOd == "B-SCL") ? "checked=checked" : "" ;?>><label for="od_180"   class="filak_mod">B-SCL</label>
		</td><td><input id="od_181" type="checkbox"  onclick="checkwnls()" name="elem_gsclOd" value="GPCL" <?php echo ($elem_gsclOd == "GPCL") ? "checked=checked" : "" ;?>><label for="od_181"  >GPCL</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('ConLens')" >BL</td>
		<td align="left">Contact Lens</td>
		<td>
		<input id="os_179" type="checkbox"  onclick="checkwnls()" name="elem_sclOs" value="SCL" <?php echo ($elem_sclOs == "SCL") ? "checked=checked" : "" ;?>><label for="os_179"  >SCL</label>
		</td><td><input id="os_180" type="checkbox"  onclick="checkwnls()" name="elem_msclOs" value="B-SCL" <?php echo ($elem_msclOs == "B-SCL") ? "checked=checked" : "" ;?>><label for="os_180"   class="filak_mod">B-SCL</label>
		</td><td><input id="os_181" type="checkbox"  onclick="checkwnls()" name="elem_gsclOs" value="GPCL" <?php echo ($elem_gsclOs == "GPCL") ? "checked=checked" : "" ;?>><label for="os_181"  >GPCL</label>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Contact Lens"])){ echo $arr_exm_ext_htm["Cornea"]["Contact Lens"]; }  ?>
			
		<tr class="exmhlgcol grp_handle grp_CornSurgry <?php echo $cls_CornSurgry; ?>" id="d_CornSurgry" >
		<td align="left" class="grpbtn" onclick="openSubGrp('CornSurgry')">			
			<label >Surgery
			<span class="glyphicon <?php echo $arow_CornSurgry; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea  onblur="checkwnls(); checkSymClr(this,'CornSurgry');"  id="od_182" name="elem_surgeryOd" class="form-control"><?php echo ($elem_surgeryOd);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('CornSurgry')">BL</td>
		<td align="left" class="grpbtn" onclick="openSubGrp('CornSurgry')">
			<label >Surgery
			<span class="glyphicon <?php echo $arow_CornSurgry; ?>"></span></label> 
		</td>
		<td colspan="8"><textarea onBlur="checkwnls();checkSymClr(this,'CornSurgry');"  id="os_182" name="elem_surgeryOs" class="form-control"><?php echo ($elem_surgeryOs);?></textarea></td>		
		</tr>
		
		<tr class="exmhlgcol grp_CornSurgry <?php echo $cls_CornSurgry; ?>">
		<td align="left"></td>
		<td>
		<input type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" id="elem_pkOd" name="elem_pkOd" value="PK" <?php echo ($elem_pkOd == "PK") ? "checked=checked" : "" ;?>><label for="elem_pkOd"  >PK</label>
		</td><td colspan="3"><input id="od_183" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_epithelialDefectOd" value="Epithelial Defect" <?php echo ($elem_epithelialDefectOd == "Epithelial Defect") ? "checked=checked" : "" ;?>><label for="od_183" >Epithelial Defect</label>
		</td>
		<td colspan="4"><input id="elem_corn_surg_woundsOd" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_corn_surg_woundsOd" value="Wounds OK" <?php echo ($elem_corn_surg_woundsOd == "Wounds OK") ? "checked=checked" : "" ;?>><label for="elem_corn_surg_woundsOd"   class="aato">Wounds OK</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('CornSurgry')" rowspan="6">BL</td>
		<td align="left"></td>
		<td>
		<input type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" id="elem_pkOs" name="elem_pkOs" value="PK" <?php echo ($elem_pkOs == "PK") ? "checked=checked" : "" ;?>><label for="elem_pkOs"  >PK</label>
		</td><td colspan="3"><input id="os_183" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_epithelialDefectOs" value="Epithelial Defect" <?php echo ($elem_epithelialDefectOs == "Epithelial Defect") ? "checked=checked" : "" ;?>><label for="os_183" >Epithelial Defect</label>
		</td>
		<td colspan="4"><input id="elem_corn_surg_woundsOs" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_corn_surg_woundsOs" value="Wounds OK" <?php echo ($elem_corn_surg_woundsOs == "Wounds OK") ? "checked=checked" : "" ;?>><label for="elem_corn_surg_woundsOs"   class="aato">Wounds OK</label>
		</td>		
		</tr>
		
		<tr class="exmhlgcol grp_CornSurgry <?php echo $cls_CornSurgry; ?>">
		<td align="left"></td>
		<td><input id="od_191" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_PRKOd" value="PRK" <?php echo ($elem_PRKOd == "PRK") ? "checked=checked" : "" ;?>><label for="od_191"  >PRK</label>
		</td><td colspan="3"><input id="od_184" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_scarHazeOd" value="Scar/Haze" <?php echo ($elem_scarHazeOd == "Scar/Haze") ? "checked=checked" : "" ;?>><label for="od_184"  >Scar/Haze</label> 
		</td><td colspan="4"><input id="elem_corn_surg_nostriaeOd" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_corn_surg_nostriaeOd" value="No striae" <?php echo ($elem_corn_surg_nostriaeOd == "No striae") ? "checked=checked" : "" ;?>><label for="elem_corn_surg_nostriaeOd"   class="aato">No striae</label></td>
		
		<td align="left"></td>
		<td><input id="os_191" type="checkbox"  onclick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_PRKOs" value="PRK" <?php echo ($elem_PRKOs == "PRK") ? "checked=checked" : "" ;?>><label for="os_191"  >PRK</label>
		</td><td colspan="3"><input id="os_184" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_scarHazeOs" value="Scar/Haze" <?php echo ($elem_scarHazeOs == "Scar/Haze") ? "checked=checked" : "" ;?>><label for="os_184"  >Scar/Haze</label>
		</td><td colspan="4"><input id="elem_corn_surg_nostriaeOs" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_corn_surg_nostriaeOs" value="No striae" <?php echo ($elem_corn_surg_nostriaeOs == "No striae") ? "checked=checked" : "" ;?>><label for="elem_corn_surg_nostriaeOs"   class="aato">No striae</label></td>
		</tr>
		
		<tr class="exmhlgcol grp_CornSurgry <?php echo $cls_CornSurgry; ?>">
		<td align="left"></td>
		<td><input id="od_190" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_lasikOd" value="LASIK" <?php echo ($elem_lasikOd == "Lasik" || $elem_lasikOd == "LASIK") ? "checked=checked" : "" ;?>><label for="od_190"  >LASIK</label>
		</td><td colspan="3"><input id="od_185" type="checkbox"  onclick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_vascularizationOd" value="Vascularization" <?php echo ($elem_vascularizationOd == "Vascularization") ? "checked=checked" : "" ;?>><label for="od_185"  class="aato">Vascularization</label>
		</td><td colspan="4"><input id="elem_corn_surg_interclearOd" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_corn_surg_interclearOd" value="Interface clear" <?php echo ($elem_corn_surg_interclearOd == "Interface clear") ? "checked=checked" : "" ;?>><label for="elem_corn_surg_interclearOd"   class="aato">Interface clear</label></td>
		
		<td align="left"></td>
		<td><input id="os_190" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_lasikOs" value="LASIK" <?php echo ($elem_lasikOs == "Lasik"||$elem_lasikOs == "LASIK") ? "checked=checked" : "" ;?>><label for="os_190"  >LASIK</label>
		</td><td colspan="3"><input id="os_185" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_vascularizationOs" value="Vascularization" <?php echo ($elem_vascularizationOs == "Vascularization") ? "checked=checked" : "" ;?>><label for="os_185"  class="aato">Vascularization</label>
		</td><td colspan="4"><input id="elem_corn_surg_interclearOs" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_corn_surg_interclearOs" value="Interface clear" <?php echo ($elem_corn_surg_interclearOs == "Interface clear") ? "checked=checked" : "" ;?>><label for="elem_corn_surg_interclearOs"   class="aato">Interface clear</label></td>
		</tr>
		
		<tr class="exmhlgcol grp_CornSurgry <?php echo $cls_CornSurgry; ?>">
		<td align="left"></td>
		<td><input id="od_308" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_RKOd" value="RK" <?php echo ($elem_RKOd == "RK") ? "checked=checked" : "" ;?>><label for="od_308"  >RK</label>
		</td><td colspan="7"><input id="od_186" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_edemaOd" value="Edema" <?php echo ($elem_edemaOd == "Edema") ? "checked=checked" : "" ;?>><label for="od_186"  >Edema</label>
		</td>
		
		<td align="left"></td>
		<td><input id="os_308" type="checkbox"  onclick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_RKOs" value="RK" <?php echo ($elem_RKOs == "RK") ? "checked=checked" : "" ;?>><label for="os_308"  >RK</label>
		</td><td colspan="7"><input id="os_186" type="checkbox"  onclick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_edemaOs" value="Edema" <?php echo ($elem_edemaOs == "Edema") ? "checked=checked" : "" ;?>><label for="os_186"  >Edema</label>
		</td>
		</tr>
		
		<tr class="exmhlgcol grp_CornSurgry <?php echo $cls_CornSurgry; ?>">
		<td align="left"></td>
		<td><input id="od_309" type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_AKOd" value="AK" <?php echo ($elem_AKOd == "AK") ? "checked=checked" : "" ;?>><label for="od_309"  >AK</label>
		</td><td colspan="7"><input id="od_187" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_suturesOd" value="Sutures" <?php echo ($elem_suturesOd == "Sutures") ? "checked=checked" : "" ;?>><label for="od_187"  >Sutures</label>
		</td>
		
		<td align="left"></td>
		<td><input id="os_309" type="checkbox"  onclick="checkwnls(); checkSymClr(this,'CornSurgry');" name="elem_AKOs" value="AK" <?php echo ($elem_AKOs == "AK") ? "checked=checked" : "" ;?>><label for="os_309"  >AK</label>
		</td><td colspan="7"><input id="os_187" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');" name="elem_suturesOs" value="Sutures" <?php echo ($elem_suturesOs == "Sutures") ? "checked=checked" : "" ;?>><label for="os_187"  >Sutures</label>
		</td>
		</tr>
		
		<tr class="exmhlgcol grp_CornSurgry <?php echo $cls_CornSurgry; ?>">
		<td align="left"></td>
		<td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" id="elem_LRIOd" name="elem_LRIOd" value="LRI" <?php echo ($elem_LRIOd == "LRI") ? "checked=checked" : "" ;?>><label for="elem_LRIOd"  >LRI</label>
		</td><td colspan="7">
		<input id="od_188" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');"
		    name="elem_flapSecureOd" value="Flap Secure" 
			<?php echo ($elem_flapSecureOd == "Flap Secure") ? "checked=checked" : "" ;?>	><label for="od_188"  >Flap Secure</label>
		</td>
		
		<td align="left"></td>
		<td><input type="checkbox" onClick="checkwnls(); checkSymClr(this,'CornSurgry');" id="elem_LRIOs" name="elem_LRIOs" value="LRI" <?php echo ($elem_LRIOs == "LRI") ? "checked=checked" : "" ;?>><label for="elem_LRIOs"  >LRI</label>
		</td><td colspan="7"><input id="os_188" type="checkbox"  onclick="checkwnls();checkSymClr(this,'CornSurgry');"
                name="elem_flapSecureOs" value="Flap Secure" 
		<?php echo ($elem_flapSecureOs == "Flap Secure") ? "checked=checked" : "" ;?>><label for="os_188"  >Flap Secure</label>        
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Surgery"])){ echo $arr_exm_ext_htm["Cornea"]["Surgery"]; }  ?>
		
		<tr id="d_ScarCorn" class="grp_ScarCorn">
		<td align="left">Scar</td>
		<td>
		<input id="od_311" type="checkbox" value="Stromal" name="elem_StromalOd" onClick="checkwnls();" <?php if($elem_StromalOd == 'Stromal') echo 'CHECKED'; ?>><label for="od_311"  >Stromal</label>
		</td><td colspan="2"><input id="od_312" type="checkbox" value="Anterior" name="elem_AnteriorOd" onClick="checkwnls();" <?php if($elem_AnteriorOd == 'Anterior') echo 'CHECKED'; ?>><label for="od_312"  >Anterior</label>
		</td><td colspan="2"><input id="od_313" type="checkbox" value="Mid" name="elem_MidOd" onClick="checkwnls();" <?php if($elem_MidOd == 'Mid') echo 'CHECKED'; ?>><label for="od_313"  >Mid</label>
		</td><td colspan="3"><input id="od_314" type="checkbox" value="Posterior" name="elem_PosteriorOd" onClick="checkwnls();" <?php if($elem_PosteriorOd == 'Posterior') echo 'CHECKED'; ?>><label for="od_314"  >Posterior</label>
		</td>				 
		<td align="center" class="bilat" onClick="check_bl('ScarCorn')" rowspan="3">BL</td>
		<td align="left">Scar</td>
		<td>
		<input id="os_311" type="checkbox" value="Stromal" name="elem_StromalOs" onClick="checkwnls();" <?php if($elem_StromalOs == 'Stromal') echo 'CHECKED'; ?>><label for="os_311"  >Stromal</label>
		</td><td><input id="os_312" type="checkbox" value="Anterior" name="elem_AnteriorOs" onClick="checkwnls();" <?php if($elem_AnteriorOs== 'Anterior') echo 'CHECKED'; ?>><label for="os_312"  >Anterior</label>
		</td><td colspan="2"><input id="os_313" type="checkbox" value="Mid" name="elem_MidOs" onClick="checkwnls();" <?php if($elem_MidOs == 'Mid') echo 'CHECKED'; ?>><label for="os_313"  >Mid</label>
		</td><td colspan="4"><input id="os_314" type="checkbox" value="Posterior" name="elem_PosteriorOs" onClick="checkwnls();" <?php if($elem_PosteriorOs == 'Posterior') echo 'CHECKED'; ?>><label for="os_314"  >Posterior</label>
		</td>				
		</tr>
		
		<tr class="grp_ScarCorn">
		<td align="left"></td>
		<td colspan="8"><input id="od_315" type="checkbox" value="Endothelial" name="elem_scarEndothelialOd" onClick="checkwnls();" <?php if($elem_scarEndothelialOd == 'Endothelial') echo 'CHECKED'; ?>><label for="od_315"  >Endothelial</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="8"><input id="os_315" type="checkbox" value="Endothelial" name="elem_scarEndothelialOs" onClick="checkwnls();" <?php if($elem_scarEndothelialOs == 'Endothelial') echo 'CHECKED'; ?>><label for="os_315"  >Endothelial</label>
		</td>		
		</tr>
		
		<tr class="grp_ScarCorn">
		<td align="left"></td>
		<td colspan="2"><input id="od_316" type="checkbox" value="Central" name="elem_CentralOd" onClick="checkwnls();" <?php if($elem_CentralOd == 'Central') echo 'CHECKED'; ?>><label for="od_316"  >Central</label>
		</td><td colspan="3"><input id="od_317" type="checkbox" value="Mid Peripheral" name="elem_MidPeripheralOd" onClick="checkwnls();" <?php if($elem_MidPeripheralOd == 'Mid Peripheral') echo 'CHECKED'; ?>><label for="od_317"  >Mid&nbsp;Peripheral</label>
		</td><td colspan="3"><input id="od_318" type="checkbox" value="Peripheral" name="elem_PeripheralOd" onClick="checkwnls();" <?php if($elem_PeripheralOd == 'Peripheral') echo 'CHECKED'; ?>><label for="od_318"  >Peripheral</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="2"><input id="os_316" type="checkbox" value="Central" name="elem_CentralOs" onClick="checkwnls();" <?php if($elem_CentralOs == 'Central') echo 'CHECKED'; ?>><label for="os_316"  >Central</label>
		</td><td colspan="3"><input id="os_317" type="checkbox" value="Mid Peripheral" name="elem_MidPeripheralOs" onClick="checkwnls();" <?php if($elem_MidPeripheralOs == 'Mid Peripheral') echo 'CHECKED'; ?>><label for="os_317"  >Mid&nbsp;Peripheral</label>
		</td><td colspan="3"><input id="os_318" type="checkbox" value="Peripheral" name="elem_PeripheralOs" onClick="checkwnls();" <?php if($elem_PeripheralOs == 'Peripheral') echo 'CHECKED'; ?>><label for="os_318"  >Peripheral</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Scar"])){ echo $arr_exm_ext_htm["Cornea"]["Scar"]; }  ?>
		
		<tr id="d_Vasc" class="grp_Vasc">
		<td align="left">Vascularization</td>
		<td colspan="8">
		<input type="checkbox" id="elem_vascOd_SubEpithelial" name="elem_vascOd_SubEpithelial" onClick="checkwnls();" <?php if($elem_vascOd_SubEpithelial == 'Sub-epithelial') echo 'CHECKED'; ?> value="Sub-epithelial"><label for="elem_vascOd_SubEpithelial"  >Sub-epithelial</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Vasc')" rowspan="5">BL</td>
		<td align="left">Vascularization</td>
		<td colspan="8">
		<input type="checkbox" id="elem_vascOs_SubEpithelial" name="elem_vascOs_SubEpithelial" onClick="checkwnls();" <?php if($elem_vascOs_SubEpithelial == 'Sub-epithelial') echo 'CHECKED'; ?> value="Sub-epithelial"><label for="elem_vascOs_SubEpithelial"  >Sub-epithelial</label>
		</td>		
		</tr>
		
		<tr class="grp_Vasc">
		<td align="left"></td>
		<td colspan="2"><input type="checkbox" id="elem_vascOd_Stromal" name="elem_vascOd_Stromal" onClick="checkwnls();" <?php if($elem_vascOd_Stromal== 'Stromal') echo 'CHECKED'; ?> value="Stromal"><label for="elem_vascOd_Stromal"  >Stromal</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOd_Superficial" name="elem_vascOd_Superficial" onClick="checkwnls();" <?php if($elem_vascOd_Superficial == 'Superficial') echo 'CHECKED'; ?> value="Superficial"><label for="elem_vascOd_Superficial"  >Superficial</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOd_Deep" name="elem_vascOd_Deep" onClick="checkwnls();" <?php if($elem_vascOd_Deep == 'Deep') echo 'CHECKED'; ?> value="Deep"><label for="elem_vascOd_Deep"  >Deep</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="2"><input type="checkbox" id="elem_vascOs_Stromal" name="elem_vascOs_Stromal" onClick="checkwnls();" <?php if($elem_vascOs_Stromal== 'Stromal') echo 'CHECKED'; ?> value="Stromal"><label for="elem_vascOs_Stromal" >Stromal</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOs_Superficial" name="elem_vascOs_Superficial" onClick="checkwnls();" <?php if($elem_vascOs_Superficial == 'Superficial') echo 'CHECKED'; ?> value="Superficial"><label for="elem_vascOs_Superficial" >Superficial</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOs_Deep" name="elem_vascOs_Deep" onClick="checkwnls();" <?php if($elem_vascOs_Deep == 'Deep') echo 'CHECKED'; ?> value="Deep"><label for="elem_vascOs_Deep" >Deep</label>
		</td>		
		</tr>
		
		<tr class="grp_Vasc">
		<td align="left"></td>
		<td colspan="2"><input type="checkbox" onClick="checkwnls();" <?php if($elem_vascOd_Endothelial == 'Endothelial') echo 'CHECKED'; ?> id="elem_vascOd_Endothelial" name="elem_vascOd_Endothelial" value="Endothelial"><label for="elem_vascOd_Endothelial"  >Endothelial</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOd_Peripheral" name="elem_vascOd_Peripheral" onClick="checkwnls();" <?php if($elem_vascOd_Peripheral == 'Peripheral') echo 'CHECKED'; ?> value="Peripheral"><label for="elem_vascOd_Peripheral"  >Peripheral</label>
		</td><td colspan="3"><input type="checkbox" onClick="checkwnls();" <?php if($elem_vascOd_Central == 'Central') echo 'CHECKED'; ?> id="elem_vascOd_Central" name="elem_vascOd_Central" value="Central"><label for="elem_vascOd_Central"  >Central</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="2"><input type="checkbox" onClick="checkwnls();" <?php if($elem_vascOs_Endothelial == 'Endothelial') echo 'CHECKED'; ?> id="elem_vascOs_Endothelial" name="elem_vascOs_Endothelial" value="Endothelial"><label for="elem_vascOs_Endothelial" >Endothelial</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOs_Peripheral" name="elem_vascOs_Peripheral" onClick="checkwnls();" <?php if($elem_vascOs_Peripheral == 'Peripheral') echo 'CHECKED'; ?> value="Peripheral"><label for="elem_vascOs_Peripheral" >Peripheral</label>
		</td><td colspan="3"><input type="checkbox" onClick="checkwnls();" <?php if($elem_vascOs_Central == 'Central') echo 'CHECKED'; ?> id="elem_vascOs_Central" name="elem_vascOs_Central" value="Central"><label for="elem_vascOs_Central" >Central</label>
		</td>		
		</tr>
		
		<tr class="grp_Vasc">
		<td align="left"></td>
		<td colspan="3"><input  type="checkbox" onClick="checkwnls();" <?php if($elem_vascOd_Pannus == 'Pannus') echo 'CHECKED'; ?> id="elem_vascOd_Pannus" name="elem_vascOd_Pannus" value="Pannus"><label for="elem_vascOd_Pannus"  >Pannus</label>
		</td><td colspan="5"><input type="checkbox" id="elem_vascOd_GhostBV" name="elem_vascOd_GhostBV" onClick="checkwnls();" <?php if($elem_vascOd_GhostBV == 'Ghost BV') echo 'CHECKED'; ?> value="Ghost BV"><label for="elem_vascOd_GhostBV"  >Ghost BV</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="3"><input  type="checkbox" onClick="checkwnls();" <?php if($elem_vascOs_Pannus == 'Pannus') echo 'CHECKED'; ?> id="elem_vascOs_Pannus" name="elem_vascOs_Pannus" value="Pannus"><label for="elem_vascOs_Pannus" >Pannus</label>
		</td><td colspan="5"><input type="checkbox" id="elem_vascOs_GhostBV" name="elem_vascOs_GhostBV" onClick="checkwnls();" <?php if($elem_vascOs_GhostBV == 'Ghost BV') echo 'CHECKED'; ?> value="Ghost BV"><label for="elem_vascOs_GhostBV" >Ghost BV</label>
		</td>		
		</tr>
		
		<tr class="grp_Vasc">
		<td align="left"></td>
		<td colspan="1"><input  type="checkbox" onClick="checkwnls();" <?php if($elem_vascOd_Superior == 'Superior') echo 'CHECKED'; ?> id="elem_vascOd_Superior" name="elem_vascOd_Superior" value="Superior"><label for="elem_vascOd_Superior"  >Superior</label>
		</td><td colspan="2"><input type="checkbox" id="elem_vascOd_Inferior" name="elem_vascOd_Inferior" onClick="checkwnls();" <?php if($elem_vascOd_Inferior == 'Inferior') echo 'CHECKED'; ?> value="Inferior"><label for="elem_vascOd_Inferior"  >Inferior</label>
		</td><td colspan="2"><input type="checkbox" id="elem_vascOd_Nasal" name="elem_vascOd_Nasal" onClick="checkwnls();" <?php if($elem_vascOd_Nasal == 'Nasal') echo 'CHECKED'; ?> value="Nasal"><label for="elem_vascOd_Nasal"  >Nasal</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOd_Temporal" name="elem_vascOd_Temporal" onClick="checkwnls();" <?php if($elem_vascOd_Temporal == 'Temporal') echo 'CHECKED'; ?> value="Temporal"><label for="elem_vascOd_Temporal"  >Temporal</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="1"><input  type="checkbox" onClick="checkwnls();" <?php if($elem_vascOs_Superior == 'Superior') echo 'CHECKED'; ?> id="elem_vascOs_Superior" name="elem_vascOs_Superior" value="Superior"><label for="elem_vascOs_Superior" >Superior</label>
		</td><td colspan="2"><input type="checkbox" id="elem_vascOs_Inferior" name="elem_vascOs_Inferior" onClick="checkwnls();" <?php if($elem_vascOs_Inferior == 'Inferior') echo 'CHECKED'; ?> value="Inferior"><label for="elem_vascOs_Inferior" >Inferior</label>
		</td><td colspan="2"><input type="checkbox" id="elem_vascOs_Nasal" name="elem_vascOs_Nasal" onClick="checkwnls();" <?php if($elem_vascOs_Nasal == 'Nasal') echo 'CHECKED'; ?> value="Nasal"><label for="elem_vascOs_Nasal" >Nasal</label>
		</td><td colspan="3"><input type="checkbox" id="elem_vascOs_Temporal" name="elem_vascOs_Temporal" onClick="checkwnls();" <?php if($elem_vascOs_Temporal == 'Temporal') echo 'CHECKED'; ?> value="Temporal"><label for="elem_vascOs_Temporal" >Temporal</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Vascularization"])){ echo $arr_exm_ext_htm["Cornea"]["Vascularization"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Cornea"]["Main"])){ echo $arr_exm_ext_htm["Cornea"]["Main"]; }  ?>
		
		<tr id="d_adOpt_Corn">
		<td align="left">Comments</td>
		<td colspan="8"><textarea  onblur="checkwnls()"  id="od_192" name="elem_corneaAdvanceOptionOd" class="form-control" ><?php echo ($elem_corneaAdvanceOptionOd);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('adOpt_Corn')">BL</td>
		<td align="left">Comments</td>
		<td colspan="8"><textarea  onblur="checkwnls()"  id="os_192" name="elem_corneaAdvanceOptionOs" class="form-control" ><?php echo ($elem_corneaAdvanceOptionOs);?></textarea></td>
		</tr>
		
		</table>
	</div>
	<div class="clearfix"> </div>	
	</div>
	<div role="tabpanel" class="tab-pane <?php echo (3 == $defTabKey) ? "active" : "" ?>" id="div3">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1">
				
			</div>
			<div class="col-sm-2 pharmo mt5">			
				<!-- Pen Light --*>					
				<input type="checkbox" id="elem_penLightAnt" name="elem_penLightAnt" value="1" <?php //echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
				<label id="lblPenLight" for="elem_penLightAnt"><strong>Pen Light</strong></label>				
				<!-- Pen Light -->
			</div>    
			<div class="col-sm-9"> 
				<span id="examFlag" class="glyphicon flagWnl "></span>
		
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChangeAnt"  name="elem_noChangeAnt" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncAnt == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChangeAnt">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
			</div>    
		</div>
	</div>    
	<div class="clearfix"> </div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="7" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		</td>
		<td width="100" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
		<td colspan="7" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>
		
		
		<tr id="d_Cell">
		<td align="left">Cell</td>
		<td>
		<input id="od_193" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOd_neg" value="Absent" <?php echo ($elem_cellOd_neg=="-ve" || $elem_cellOd_neg=="Absent") ? "checked=checked" : "";?>><label for="od_193" >Absent</label>
		</td><td><input id="od_194" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOd_T" value="T" <?php echo ($elem_cellOd_T=="T") ? "checked=checked" : "";?>><label for="od_194" >T</label>
		</td><td><input id="od_195" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOd_pos1" value="1+" <?php echo ($elem_cellOd_pos1=="+1" || $elem_cellOd_pos1=="1+") ? "checked=checked" : "";?>><label for="od_195" >1+</label>
		</td><td><input id="od_196" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOd_pos2" value="2+" <?php echo ($elem_cellOd_pos2=="+2" || $elem_cellOd_pos2=="2+") ? "checked=checked" : "";?>><label for="od_196" >2+</label>
		</td><td><input id="od_197" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOd_pos3" value="3+" <?php echo ($elem_cellOd_pos3=="+3" || $elem_cellOd_pos3=="3+") ? "checked=checked" : "";?>><label for="od_197" >3+</label>
		</td><td><input id="od_198" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOd_pos4" value="4+" <?php echo ($elem_cellOd_pos4=="+4" || $elem_cellOd_pos4=="4+") ? "checked=checked" : "";?>><label for="od_198"  class="aato">4+</label>
		</td>			
		<td align="center" class="bilat" onClick="check_bl('Cell')">BL</td>
		<td align="left">Cell</td>
		<td>
		<input id="os_193" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOs_neg" value="Absent" <?php echo ($elem_cellOs_neg=="-ve" || $elem_cellOs_neg=="Absent") ? "checked=checked" : "";?>><label for="os_193" >Absent</label>
		</td><td><input id="os_194" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOs_T" value="T" <?php echo ($elem_cellOs_T=="T") ? "checked=checked" : "";?>><label for="os_194" >T</label>
		</td><td><input id="os_195" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOs_pos1" value="1+" <?php echo ($elem_cellOs_pos1=="+1" || $elem_cellOs_pos1=="1+") ? "checked=checked" : "";?>><label for="os_195" >1+</label>
		</td><td><input id="os_196" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOs_pos2" value="2+" <?php echo ($elem_cellOs_pos2=="+2" || $elem_cellOs_pos2=="2+") ? "checked=checked" : "";?>><label for="os_196" >2+</label>
		</td><td><input id="os_197" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOs_pos3" value="3+" <?php echo ($elem_cellOs_pos3=="+3" || $elem_cellOs_pos3=="3+") ? "checked=checked" : "";?>><label for="os_197" >3+</label>
		</td><td><input id="os_198" type="checkbox"  onclick="checkAbsent(this)" name="elem_cellOs_pos4" value="4+" <?php echo ($elem_cellOs_pos4=="+4" || $elem_cellOs_pos4=="4+") ? "checked=checked" : "";?>><label for="os_198"  class="aato">4+</label>
		</td>			
		</tr>
		<?php if(isset($arr_exm_ext_htm["Ant. Chamber"]["Cell"])){ echo $arr_exm_ext_htm["Ant. Chamber"]["Cell"]; }  ?>
		
		<tr id="d_Flare" class="grp_Flare">
		<td align="left">Flare</td>
		<td>
		<input id="od_199" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOd_neg" value="Absent" <?php echo ($elem_flareOd_neg=="-ve" || $elem_flareOd_neg=="Absent") ? "checked=checked" : "";?>><label for="od_199" >Absent</label>
		</td><td><input id="od_200" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOd_T" value="T" <?php echo ($elem_flareOd_T=="T") ? "checked=checked" : "";?>><label for="od_200" >T</label>
		</td><td><input id="od_201" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOd_pos1" value="1+" <?php echo ($elem_flareOd_pos1=="+1" || $elem_flareOd_pos1=="1+") ? "checked=checked" : "";?>><label for="od_201" >1+</label>
		</td><td><input id="od_202" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOd_pos2" value="2+" <?php echo ($elem_flareOd_pos2=="+2" || $elem_flareOd_pos2=="2+") ? "checked=checked" : "";?>><label for="od_202" >2+</label>
		</td><td><input id="od_203" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOd_pos3" value="3+" <?php echo ($elem_flareOd_pos3=="+3" || $elem_flareOd_pos3=="3+") ? "checked=checked" : "";?>><label for="od_203" >3+</label>
		</td><td><input id="od_204" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOd_pos4" value="4+" <?php echo ($elem_flareOd_pos4=="+4" || $elem_flareOd_pos4=="4+") ? "checked=checked" : "";?>><label for="od_204"  class="aato">4+</label>
		</td>				
		<td align="center" class="bilat" onClick="check_bl('Flare')" rowspan="2">BL</td>
		<td align="left">Flare</td>
		<td>
		<input id="os_199" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOs_neg" value="Absent" <?php echo ($elem_flareOs_neg=="-ve" || $elem_flareOs_neg=="Absent") ? "checked=checked" : "";?>><label for="os_199" >Absent</label>
		</td><td><input id="os_200" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOs_T" value="T" <?php echo ($elem_flareOs_T=="T") ? "checked=checked" : "";?>><label for="os_200" >T</label>
		</td><td><input id="os_201" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOs_pos1" value="1+" <?php echo ($elem_flareOs_pos1=="+1" || $elem_flareOs_pos1=="1+") ? "checked=checked" : "";?>><label for="os_201" >1+</label>
		</td><td><input id="os_202" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOs_pos2" value="2+" <?php echo ($elem_flareOs_pos2=="+2" || $elem_flareOs_pos2=="2+") ? "checked=checked" : "";?>><label for="os_202" >2+</label>
		</td><td><input id="os_203" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOs_pos3" value="3+" <?php echo ($elem_flareOs_pos3=="+3" || $elem_flareOs_pos3=="3+") ? "checked=checked" : "";?>><label for="os_203" >3+</label>
		</td><td><input id="os_204" type="checkbox"  onclick="checkAbsent(this)" name="elem_flareOs_pos4" value="4+" <?php echo ($elem_flareOs_pos4=="+4" || $elem_flareOs_pos4=="4+") ? "checked=checked" : "";?>><label for="os_204"  class="aato">4+</label>
		</td>				
		</tr>
		
		<tr id="d_Flare1" class="grp_Flare"> 
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)" id="elem_flareOd_Plasmoid" name="elem_flareOd_Plasmoid" value="Plasmoid Aqueous" <?php echo ($elem_flareOd_Plasmoid=="Plasmoid Aqueous") ? "checked=checked" : "";?>><label for="elem_flareOd_Plasmoid"  class="plas_aq">Plasmoid Aqueous</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_flareOd_Fibrin" name="elem_flareOd_Fibrin" value="Fibrin" <?php echo ($elem_flareOd_Fibrin=="Fibrin") ? "checked=checked" : "";?>><label for="elem_flareOd_Fibrin" >Fibrin</label>
		</td>		
		<td></td>
		<td></td>
		<td></td>			
		<td align="left"></td>
		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)" id="elem_flareOs_Plasmoid" name="elem_flareOs_Plasmoid" value="Plasmoid Aqueous" <?php echo ($elem_flareOs_Plasmoid=="Plasmoid Aqueous") ? "checked=checked" : "";?>><label for="elem_flareOs_Plasmoid"  class="plas_aq">Plasmoid Aqueous</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_flareOs_Fibrin" name="elem_flareOs_Fibrin" value="Fibrin" <?php echo ($elem_flareOs_Fibrin=="Fibrin") ? "checked=checked" : "";?>><label for="elem_flareOs_Fibrin" >Fibrin</label>
		</td>		
		<td></td>
		<td></td>
		<td></td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Ant. Chamber"]["Flare"])){ echo $arr_exm_ext_htm["Ant. Chamber"]["Flare"]; }  ?>
		
		<tr id="d_KP">
		<td align="left">KP</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_kpOd_Pigmented" name="elem_kpOd_Pigmented" value="Pigmented" <?php echo ($elem_kpOd_Pigmented=="Pigmented") ? "checked=checked" : "";?>><label for="elem_kpOd_Pigmented"  class="kp_pig">Pigmented</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_kpOd_NonPigmented" name="elem_kpOd_NonPigmented" value="Non-Pigmented" <?php echo ($elem_kpOd_NonPigmented=="Non-Pigmented") ? "checked=checked" : "";?>><label for="elem_kpOd_NonPigmented"  class="kp_npig">Non-Pigmented</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_kpOd_Fine" name="elem_kpOd_Fine" value="Fine" <?php echo ($elem_kpOd_Fine=="Fine") ? "checked=checked" : "";?>><label for="elem_kpOd_Fine"  class="kp_fn">Fine</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_kpOd_Large" name="elem_kpOd_Large" value="Large" <?php echo ($elem_kpOd_Large=="Large") ? "checked=checked" : "";?>><label for="elem_kpOd_Large"  class="aato">Large</label>
		</td>		
		<td></td>
		<td></td>		
		<td align="center" class="bilat" onClick="check_bl('KP')">BL</td>
		<td align="left">KP</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_kpOs_Pigmented" name="elem_kpOs_Pigmented" value="Pigmented" <?php echo ($elem_kpOs_Pigmented=="Pigmented") ? "checked=checked" : "";?>><label for="elem_kpOs_Pigmented"  class="kp_pig">Pigmented</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_kpOs_NonPigmented" name="elem_kpOs_NonPigmented" value="Non-Pigmented" <?php echo ($elem_kpOs_NonPigmented=="Non-Pigmented") ? "checked=checked" : "";?>><label for="elem_kpOs_NonPigmented"  class="kp_npig">Non-Pigmented</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_kpOs_Fine" name="elem_kpOs_Fine" value="Fine" <?php echo ($elem_kpOs_Fine=="Fine") ? "checked=checked" : "";?>><label for="elem_kpOs_Fine"  class="kp_fn">Fine</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_kpOs_Large" name="elem_kpOs_Large" value="Large" <?php echo ($elem_kpOs_Large=="Large") ? "checked=checked" : "";?>><label for="elem_kpOs_Large"  class="aato">Large</label>
		</td>
		<td></td>
		<td></td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Ant. Chamber"]["KP"])){ echo $arr_exm_ext_htm["Ant. Chamber"]["KP"]; }  ?>
		
		<tr id="d_Depth" class="grp_Depth">
		<td align="left">Depth</td>
		<td colspan="6">
		<input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Formed" name="elem_depthOd_Formed" value="Formed" <?php echo ($elem_depthOd_Formed=="Formed") ? "checked=checked" : "";?>><label for="elem_depthOd_Formed" >Formed</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Depth')" rowspan="4">BL</td>
		<td align="left">Depth</td>
		<td colspan="6">
		<input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Formed" name="elem_depthOs_Formed" value="Formed" <?php echo ($elem_depthOs_Formed=="Formed") ? "checked=checked" : "";?>><label for="elem_depthOs_Formed" >Formed</label>
		</td>		
		</tr>
		
		
		<tr class="grp_Depth">
		<td align="left"></td>
		<td colspan="6"><input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Deep" name="elem_depthOd_Deep" value="Deep" <?php echo ($elem_depthOd_Deep=="Deep") ? "checked=checked" : "";?>><label for="elem_depthOd_Deep" >Deep</label>
		</td>		
		<td align="left"></td>
		<td colspan="6"><input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Deep" name="elem_depthOs_Deep" value="Deep" <?php echo ($elem_depthOs_Deep=="Deep") ? "checked=checked" : "";?>><label for="elem_depthOs_Deep" >Deep</label>
		</td>		
		</tr>
		
		<tr class="grp_Depth">
		<td align="left"></td>
		<td><input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Shallow" name="elem_depthOd_Shallow" value="Shallow" <?php echo ($elem_depthOd_Shallow=="Shallow") ? "checked=checked" : "";?>><label for="elem_depthOd_Shallow" >Shallow</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Mild" name="elem_depthOd_Mild" value="Mild" <?php echo ($elem_depthOd_Mild=="Mild") ? "checked=checked" : "";?>><label for="elem_depthOd_Mild" >Mild</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Moderate" name="elem_depthOd_Moderate" value="Moderate" <?php echo ($elem_depthOd_Moderate=="Moderate") ? "checked=checked" : "";?>><label for="elem_depthOd_Moderate"  class="dpth_mod">Moderate</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Severe" name="elem_depthOd_Severe" value="Severe" <?php echo ($elem_depthOd_Severe=="Severe") ? "checked=checked" : "";?>><label for="elem_depthOd_Severe" >Severe</label>
		</td>		
		<td align="left"></td>
		<td><input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Shallow" name="elem_depthOs_Shallow" value="Shallow" <?php echo ($elem_depthOs_Shallow=="Shallow") ? "checked=checked" : "";?>><label for="elem_depthOs_Shallow" >Shallow</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Mild" name="elem_depthOs_Mild" value="Mild" <?php echo ($elem_depthOs_Mild=="Mild") ? "checked=checked" : "";?>><label for="elem_depthOs_Mild" >Mild</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Moderate" name="elem_depthOs_Moderate" value="Moderate" <?php echo ($elem_depthOs_Moderate=="Moderate") ? "checked=checked" : "";?>><label for="elem_depthOs_Moderate"  class="dpth_mod">Moderate</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Severe" name="elem_depthOs_Severe" value="Severe" <?php echo ($elem_depthOs_Severe=="Severe") ? "checked=checked" : "";?>><label for="elem_depthOs_Severe" >Severe</label>
		</td>		
		</tr>
		
		<tr class="grp_Depth">
		<td align="left"></td>
		<td colspan="6"><input type="checkbox"  onclick="checkwnls()" id="elem_depthOd_Flat" name="elem_depthOd_Flat" value="Flat" <?php echo ($elem_depthOd_Flat=="Flat") ? "checked=checked" : "";?>><label for="elem_depthOd_Flat" >Flat</label>
		</td>
		<td align="left"></td>
		<td colspan="6"><input type="checkbox"  onclick="checkwnls()" id="elem_depthOs_Flat" name="elem_depthOs_Flat" value="Flat" <?php echo ($elem_depthOs_Flat=="Flat") ? "checked=checked" : "";?>><label for="elem_depthOs_Flat" >Flat</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Ant. Chamber"]["Depth"])){ echo $arr_exm_ext_htm["Ant. Chamber"]["Depth"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Ant. Chamber"]["Main"])){ echo $arr_exm_ext_htm["Ant. Chamber"]["Main"]; }  ?>
		
		<tr id="d_adOpt_AntCh">
		<td align="left">Comments</td>
		<td colspan="6"><textarea  onblur="checkwnls()" id="od_212" name="elem_antChamberAdvanceOptionsOd" class="form-control" ><?php echo ($elem_antChamberAdvanceOptionsOd);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('adOpt_AntCh')">BL</td>
		<td align="left">Comments</td>
		<td colspan="6"><textarea  onblur="checkwnls()" id="os_212" name="elem_antChamberAdvanceOptionsOs" class="form-control" ><?php echo ($elem_antChamberAdvanceOptionsOs);?></textarea></td>		
		</tr>		
		
		</table>
	</div>
	<div class="clearfix"> </div>	
	</div>
	<div role="tabpanel" class="tab-pane <?php echo (4 == $defTabKey) ? "active" : "" ?>" id="div4">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1">
				
			</div>
			<div class="col-sm-2 pharmo mt5">			
				<!-- Pen Light --*>					
				<input type="checkbox" id="elem_penLightIris" name="elem_penLightIris" value="1" <?php //echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
				<label id="lblPenLight" for="elem_penLightIris"><strong>Pen Light</strong></label>				
				<!-- Pen Light -->
			</div>    
			<div class="col-sm-9"> 
				<span id="examFlag" class="glyphicon flagWnl "></span>
		
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChangeIris"  name="elem_noChangeIris" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncIris == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChangeIris">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
			</div>    
		</div>
	</div>    
	<div class="clearfix"> </div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="7" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		</td>
		<td width="100" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
		<td colspan="7" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>		
		
		<tr id="d_PI">
		<td align="left">PI</td>
		<td colspan="2">
		<input id="od_213"  type="checkbox"  onclick="checkwnls()" name="elem_piOd_open" value="Open" <?php echo ($elem_piOd_open == "Open") ? "checked=\"checked\"" : "";?>><label for="od_213"  class="pi_opn">Open</label>
		</td><td colspan="4"><input id="od_214" type="checkbox"  onclick="checkwnls()" name="elem_piOd_close" value="Close" <?php echo ($elem_piOd_close == "Close") ? "checked=\"checked\"" : "";?>><label for="od_214" >Close</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('PI')">BL</td>
		<td align="left">PI</td>
		<td colspan="2">
		<input id="os_213" type="checkbox"  onclick="checkwnls()" name="elem_piOs_open" value="Open" <?php echo ($elem_piOs_open == "Open") ? "checked=\"checked\"" : "";?>><label for="os_213"  class="pi_opn">Open</label>
		</td><td colspan="4"><input id="os_214" type="checkbox"  onclick="checkwnls()" name="elem_piOs_close" value="Close" <?php echo ($elem_piOs_close == "Close") ? "checked=\"checked\"" : "";?>><label for="os_214" >Close</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["PI"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["PI"]; }  ?>
		
		<tr id="d_Iridectomy">
		<td align="left">Iridectomy</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkwnls()" id="elem_iridectomyOd_Iridectomy" name="elem_iridectomyOd_Iridectomy" value="Peripheral" <?php echo ($elem_iridectomyOd_Iridectomy == "Peripheral") ? "checked=\"checked\"" : "";?>><label for="elem_iridectomyOd_Iridectomy"  class="pi_opn">Peripheral</label>
		</td><td colspan="4"><input id="od_215" type="checkbox"  onclick="checkwnls()" name="elem_iridectomyOd_sector" value="Sector" <?php echo ($elem_iridectomyOd_sector == "Sector") ? "checked=\"checked\"" : "";?>><label for="od_215" >Sector</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Iridectomy')">BL</td>
		<td align="left">Iridectomy</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkwnls()" id="elem_iridectomyOs_Iridectomy" name="elem_iridectomyOs_Iridectomy" value="Peripheral" <?php echo ($elem_iridectomyOs_Iridectomy == "Peripheral") ? "checked=\"checked\"" : "";?>><label for="elem_iridectomyOs_Iridectomy"  class="pi_opn">Peripheral</label>
		</td><td colspan="4"><input id="os_215" type="checkbox"  onclick="checkwnls()" name="elem_iridectomyOs_sector" value="Sector" <?php echo ($elem_iridectomyOs_sector == "Sector") ? "checked=\"checked\"" : "";?>><label for="os_215" >Sector</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["Iridectomy"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["Iridectomy"]; }  ?>
		
		<tr id="d_Synechiae" class="grp_Synechiae">
		<td align="left">Synechiae</td>
		<td colspan="6"><textarea  onblur="checkwnls()"  id="od_216" name="elem_synechiaOd_text" class="form-control" ><?php echo ($elem_synechiaOd_text);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('Synechiae')" rowspan="3">BL</td>
		<td align="left">Synechiae</td>
		<td colspan="6"><textarea  onblur="checkwnls()"  id="os_216" name="elem_synechiaOs_text" class="form-control" ><?php echo ($elem_synechiaOs_text);?></textarea></td>		
		</tr>		
		
		<tr class="grp_Synechiae">
		<td align="left">Anterior</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaAnterioOd_neg" name="elem_synechiaAnterioOd_neg" value="Absent" <?php echo ($elem_synechiaAnterioOd_neg == "-ve" || $elem_synechiaAnterioOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaAnterioOd_neg" >Absent</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaAnterioOd_pos" name="elem_synechiaAnterioOd_pos" value="Present" <?php echo ($elem_synechiaAnterioOd_pos == "+ve" || $elem_synechiaAnterioOd_pos == "Present") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaAnterioOd_pos" >Present</label>
		</td>		
		<td align="left">Anterior</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaAnterioOs_neg" name="elem_synechiaAnterioOs_neg" value="Absent" <?php echo ($elem_synechiaAnterioOs_neg == "-ve" || $elem_synechiaAnterioOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaAnterioOs_neg" >Absent</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaAnterioOs_pos" name="elem_synechiaAnterioOs_pos" value="Present" <?php echo ($elem_synechiaAnterioOs_pos == "+ve" || $elem_synechiaAnterioOs_pos == "Present") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaAnterioOs_pos" >Present</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["Synechiae/Anterior"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["Synechiae/Anterior"]; }  ?>
		
		<tr class="grp_Synechiae">
		<td align="left">Posterior</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaPosteriorOd_neg" name="elem_synechiaPosteriorOd_neg" value="Absent" <?php echo ($elem_synechiaPosteriorOd_neg == "-ve" || $elem_synechiaPosteriorOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaPosteriorOd_neg" >Absent</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaPosteriorOd_pos" name="elem_synechiaPosteriorOd_pos" value="Present" <?php echo ($elem_synechiaPosteriorOd_pos == "+ve" || $elem_synechiaPosteriorOd_pos == "Present") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaPosteriorOd_pos" >Present</label>
		</td>		
		<td align="left">Posterior</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaPosteriorOs_neg" name="elem_synechiaPosteriorOs_neg" value="Absent" <?php echo ($elem_synechiaPosteriorOs_neg == "-ve" || $elem_synechiaPosteriorOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaPosteriorOs_neg" >Absent</label>
		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)" id="elem_synechiaPosteriorOs_pos" name="elem_synechiaPosteriorOs_pos" value="Present" <?php echo ($elem_synechiaPosteriorOs_pos == "+ve" || $elem_synechiaPosteriorOs_pos == "Present") ? "checked=\"checked\"" : "";?>><label for="elem_synechiaPosteriorOs_pos" >Present</label>
		</td>		
		</tr>	
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["Synechiae/Posterior"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["Synechiae/Posterior"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["Synechiae"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["Synechiae"]; }  ?>	
		
		<tr id="d_Nevus">
		<td align="left">Nevus</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOd_neg" name="elem_irisNevusOd_neg" value="Absent" <?php echo ($elem_irisNevusOd_neg=="-ve" || $elem_irisNevusOd_neg=="Absent") ? "checked" : ""; ?>><label for="elem_irisNevusOd_neg" >Absent</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOd_Pos" name="elem_irisNevusOd_Pos" value="Present" <?php echo ($elem_irisNevusOd_Pos=="+ve" || $elem_irisNevusOd_Pos=="Present") ? "checked" : ""; ?>><label for="elem_irisNevusOd_Pos" >Present</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOd_Inferior" name="elem_irisNevusOd_Inferior" value="Inferior" <?php echo ($elem_irisNevusOd_Inferior=="Inferior") ? "checked" : ""; ?>><label for="elem_irisNevusOd_Inferior" >Inferior</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOd_Superior" name="elem_irisNevusOd_Superior" value="Superior" <?php echo ($elem_irisNevusOd_Superior=="Superior") ? "checked" : ""; ?>><label for="elem_irisNevusOd_Superior" >Superior</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOd_Temporal" name="elem_irisNevusOd_Temporal" value="Temporal" <?php echo ($elem_irisNevusOd_Temporal=="Temporal") ? "checked" : ""; ?>><label for="elem_irisNevusOd_Temporal" >Temporal</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOd_Nasal" name="elem_irisNevusOd_Nasal" value="Nasal" <?php echo ($elem_irisNevusOd_Nasal=="Nasal") ? "checked" : ""; ?>><label for="elem_irisNevusOd_Nasal" >Nasal</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('Nevus')">BL</td>
		<td align="left">Nevus</td>
		<td>
		<input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOs_neg" name="elem_irisNevusOs_neg" value="Absent" <?php echo ($elem_irisNevusOs_neg=="-ve" || $elem_irisNevusOs_neg=="Absent") ? "checked" : ""; ?>><label for="elem_irisNevusOs_neg" >Absent</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOs_Pos" name="elem_irisNevusOs_Pos" value="Present" <?php echo ($elem_irisNevusOs_Pos=="+ve" || $elem_irisNevusOs_Pos=="Present") ? "checked" : ""; ?>><label for="elem_irisNevusOs_Pos" >Present</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOs_Inferior" name="elem_irisNevusOs_Inferior" value="Inferior" <?php echo ($elem_irisNevusOs_Inferior=="Inferior") ? "checked" : ""; ?>><label for="elem_irisNevusOs_Inferior" >Inferior</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOs_Superior" name="elem_irisNevusOs_Superior" value="Superior" <?php echo ($elem_irisNevusOs_Superior=="Superior") ? "checked" : ""; ?>><label for="elem_irisNevusOs_Superior" >Superior</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOs_Temporal" name="elem_irisNevusOs_Temporal" value="Temporal" <?php echo ($elem_irisNevusOs_Temporal=="Temporal") ? "checked" : ""; ?>><label for="elem_irisNevusOs_Temporal" >Temporal</label>
		</td><td><input type="checkbox"  onclick="checkAbsent(this)" id="elem_irisNevusOs_Nasal" name="elem_irisNevusOs_Nasal" value="Nasal" <?php echo ($elem_irisNevusOs_Nasal=="Nasal") ? "checked" : ""; ?>><label for="elem_irisNevusOs_Nasal" >Nasal</label>
		</td>		
		</tr>	
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["Nevus"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["Nevus"]; }  ?>
		
		<tr id="d_PSX">
		<td align="left">PSX</td>
		<td>
		<input id="elem_psxOd_neg" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOd_neg" value="Absent" <?php echo ($elem_psxOd_neg == "-ve" || $elem_psxOd_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOd_neg" >Absent</label>
		</td><td><input id="elem_psxOd_T" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOd_T" value="T" <?php echo ($elem_psxOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOd_T" >T</label>
		</td><td><input id="elem_psxOd_pos1" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOd_pos1" value="1+" <?php echo ($elem_psxOd_pos1 == "+1" || $elem_psxOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOd_pos1" >1+</label>
		</td><td><input id="elem_psxOd_pos2" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOd_pos2" value="2+" <?php echo ($elem_psxOd_pos2 == "+2" || $elem_psxOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOd_pos2" >2+</label>
		</td><td><input id="elem_psxOd_pos3" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOd_pos3" value="3+" <?php echo ($elem_psxOd_pos3 == "+3" || $elem_psxOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOd_pos3" >3+</label>
		</td><td><input id="elem_psxOd_pos4" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOd_pos4" value="4+" <?php echo ($elem_psxOd_pos4 == "+4" || $elem_psxOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOd_pos4" >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('PSX')">BL</td>
		<td align="left">PSX</td>
		<td>
		<input id="elem_psxOs_neg" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOs_neg" value="Absent" <?php echo ($elem_psxOs_neg == "-ve" || $elem_psxOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOs_neg" >Absent</label>
		</td><td><input id="elem_psxOs_T" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOs_T" value="T" <?php echo ($elem_psxOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOs_T" >T</label>
		</td><td><input id="elem_psxOs_pos1" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOs_pos1" value="1+" <?php echo ($elem_psxOs_pos1 == "+1" || $elem_psxOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOs_pos1" >1+</label>
		</td><td><input id="elem_psxOs_pos2" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOs_pos2" value="2+" <?php echo ($elem_psxOs_pos2 == "+2" || $elem_psxOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOs_pos2" >2+</label>
		</td><td><input id="elem_psxOs_pos3" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOs_pos3" value="3+" <?php echo ($elem_psxOs_pos3 == "+3" || $elem_psxOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOs_pos3" >3+</label>
		</td><td><input id="elem_psxOs_pos4" type="checkbox"  onclick="checkAbsent(this)" name="elem_psxOs_pos4" value="4+" <?php echo ($elem_psxOs_pos4 == "+4" || $elem_psxOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="elem_psxOs_pos4" >4+</label>
		</td>		
		</tr>	
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["PSX"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["PSX"]; }  ?>	
		
		<tr id="d_NVI">
		<td align="left">NVI</td>
		<td colspan="2">
		<input id="elem_iris_nviOd_neg" type="checkbox"  onclick="checkAbsent(this)" name="elem_iris_nviOd_neg" value="Absent" <?php echo ($elem_iris_nviOd_neg == "-ve" || $elem_iris_nviOd_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="elem_iris_nviOd_neg" >Absent</label>
		</td><td colspan="4"><input id="elem_iris_nviOd_pos" type="checkbox"  onclick="checkAbsent(this)" name="elem_iris_nviOd_pos" value="Present" <?php echo ($elem_iris_nviOd_pos == "Present") ? "checked=\"checked\"" : ""; ?>><label for="elem_iris_nviOd_pos" >Present</label>        
		</td>		
		<td align="center" class="bilat" onClick="check_bl('NVI')">BL</td>
		<td align="left">NVI</td>
		<td colspan="2">
		<input id="elem_iris_nviOs_neg" type="checkbox"  onclick="checkAbsent(this)" name="elem_iris_nviOs_neg" value="Absent" <?php echo ($elem_iris_nviOs_neg == "-ve" || $elem_iris_nviOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="elem_iris_nviOs_neg" >Absent</label>
		</td><td colspan="4"><input id="elem_iris_nviOs_pos" type="checkbox"  onclick="checkAbsent(this)" name="elem_iris_nviOs_pos" value="Present" <?php echo ($elem_iris_nviOs_pos == "Present") ? "checked=\"checked\"" : ""; ?>><label for="elem_iris_nviOs_pos" >Present</label>        
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["NVI"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["NVI"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Iris & Pupil"]["Main"])){ echo $arr_exm_ext_htm["Iris & Pupil"]["Main"]; }  ?>	
		
		<tr id="d_adOpt_Iris">
		<td align="left">Comments</td>
		<td colspan="6"><textarea  onblur="checkwnls()"  id="od_225" name="elem_irisAdvanceOptionsOd" class="form-control" ><?php echo ($elem_irisAdvanceOptionsOd);?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('adOpt_Iris')">BL</td>
		<td align="left">Comments</td>
		<td colspan="6"><textarea  onblur="checkwnls()"  id="os_225" name="elem_irisAdvanceOptionsOs" class="form-control" ><?php echo ($elem_irisAdvanceOptionsOs);?></textarea></td>		
		</tr>
		
		</table>
	</div>
	<div class="clearfix"> </div>	
	</div>
	<div role="tabpanel" class="tab-pane <?php echo (5 == $defTabKey) ? "active" : "" ?>" id="div5">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1">
				
			</div>
			<div class="col-sm-2 pharmo mt5">			
				<!-- Pen Light --*>					
				<input type="checkbox" id="elem_penLightLens" name="elem_penLightLens" value="1" <?php //echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
				<label id="lblPenLight" for="elem_penLightLens"><strong>Pen Light</strong></label>				
				<!-- Pen Light -->
			</div>    
			<div class="col-sm-9"> 
				<span id="examFlag" class="glyphicon flagWnl "></span>
		
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChangeLens"  name="elem_noChangeLens" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncLens == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChangeLens">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
			</div>    
		</div>
	</div>    
	<div class="clearfix"> </div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="8" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		</td>
		<td width="100" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
		<td colspan="8" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>		
		
		<tr id="d_NucScl">
		<td align="left">Nuclear Sclerosis</td>
		<td>
		<input id="od_226"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOd_neg" value="Absent" <?php echo ($elem_nuclearScOd_neg == "-ve" || $elem_nuclearScOd_neg == "Absent") ? "checked" : ""; ?>><label for="od_226" >Absent</label>
		</td><td><input id="od_227"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOd_T" value="T" <?php echo ($elem_nuclearScOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_227" >T</label>
		</td><td><input id="od_228"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOd_pos1" value="1+" <?php echo ($elem_nuclearScOd_pos1 == "+1" || $elem_nuclearScOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_228" >1+</label>
		</td><td><input id="od_229"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOd_pos2" value="2+" <?php echo ($elem_nuclearScOd_pos2 == "+2" || $elem_nuclearScOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_229" >2+</label>
		</td><td><input id="od_230"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOd_pos3" value="3+" <?php echo ($elem_nuclearScOd_pos3 == "+3" || $elem_nuclearScOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_230" >3+</label>
		</td><td colspan="2"><input id="od_231"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOd_pos4" value="4+" <?php echo ($elem_nuclearScOd_pos4 == "+4" || $elem_nuclearScOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_231" >4+</label>
		</td>			
		<td align="center" class="bilat" onClick="check_bl('NucScl')">BL</td>
		<td align="left">Nuclear Sclerosis</td>
		<td>
		<input id="os_226"  type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOs_neg" value="Absent" <?php echo ($elem_nuclearScOs_neg == "-ve" || $elem_nuclearScOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="os_226" >Absent</label>
		</td><td><input id="os_227" type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOs_T" value="T" <?php echo ($elem_nuclearScOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_227" >T</label>
		</td><td><input id="os_228" type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOs_pos1" value="1+" <?php echo ($elem_nuclearScOs_pos1 == "+1" || $elem_nuclearScOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_228" >1+</label>
		</td><td><input id="os_229" type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOs_pos2" value="2+" <?php echo ($elem_nuclearScOs_pos2 == "+2" || $elem_nuclearScOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_229" >2+</label>
		</td><td><input id="os_230" type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOs_pos3" value="3+" <?php echo ($elem_nuclearScOs_pos3 == "+3" || $elem_nuclearScOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_230" >3+</label>
		</td><td colspan="2"><input id="os_231" type="checkbox"  onclick="checkAbsent(this)" name="elem_nuclearScOs_pos4" value="4+" <?php echo ($elem_nuclearScOs_pos4 == "+4" || $elem_nuclearScOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_231" >4+</label>
		</td>	
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["Nuclear Sclerosis"])){ echo $arr_exm_ext_htm["Lens"]["Nuclear Sclerosis"]; }  ?>
		
		<tr id="d_Cortical">
		<td align="left">Cortical</td>
		<td>
		<input id="od_232" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOd_neg" value="Absent" <?php echo ($elem_corticalOd_neg == "-ve" || $elem_corticalOd_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="od_232" >Absent</label>
		</td><td><input id="od_233" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOd_T" value="T" <?php echo ($elem_corticalOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_233" >T</label>
		</td><td><input id="od_234" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOd_pos1" value="1+" <?php echo ($elem_corticalOd_pos1 == "+1" || $elem_corticalOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_234" >1+</label>
		</td><td><input id="od_235" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOd_pos2" value="2+" <?php echo ($elem_corticalOd_pos2 == "+2" || $elem_corticalOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_235" >2+</label>
		</td><td><input id="od_236" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOd_pos3" value="3+" <?php echo ($elem_corticalOd_pos3 == "+3" || $elem_corticalOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_236" >3+</label>
		</td><td colspan="2"><input id="od_237" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOd_pos4" value="4+" <?php echo ($elem_corticalOd_pos4 == "+4" || $elem_corticalOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_237" >4+</label>
		</td>			
		<td align="center" class="bilat" onClick="check_bl('Cortical')">BL</td>
		<td align="left">Cortical</td>
		<td>
		<input id="os_232" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOs_neg" value="Absent" <?php echo ($elem_corticalOs_neg == "-ve" || $elem_corticalOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="os_232" >Absent</label>
		</td><td><input id="os_233" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOs_T" value="T" <?php echo ($elem_corticalOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_233" >T</label>
		</td><td><input id="os_234" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOs_pos1" value="1+" <?php echo ($elem_corticalOs_pos1 == "+1" || $elem_corticalOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_234" >1+</label>
		</td><td><input id="os_235" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOs_pos2" value="2+" <?php echo ($elem_corticalOs_pos2 == "+2" || $elem_corticalOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_235" >2+</label>
		</td><td><input id="os_236" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOs_pos3" value="3+" <?php echo ($elem_corticalOs_pos3 == "+3" || $elem_corticalOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_236" >3+</label>
		</td><td colspan="2"><input id="os_237" type="checkbox"  onclick="checkAbsent(this)" name="elem_corticalOs_pos4" value="4+" <?php echo ($elem_corticalOs_pos4 == "+4" || $elem_corticalOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_237" >4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["Cortical"])){ echo $arr_exm_ext_htm["Lens"]["Cortical"]; }  ?>
		
		<tr id="d_PSC">
		<td align="left">PSC</td>
		<td>
		<input id="od_238" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOd_neg" value="Absent" <?php echo ($elem_pscOd_neg == "-ve" || $elem_pscOd_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="od_238" >Absent</label>
		</td><td><input id="od_239" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOd_T" value="T" <?php echo ($elem_pscOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_239" >T</label>
		</td><td><input id="od_240" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOd_pos1" value="1+" <?php echo ($elem_pscOd_pos1 == "+1" || $elem_pscOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_240" >1+</label>
		</td><td><input id="od_241" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOd_pos2" value="2+" <?php echo ($elem_pscOd_pos2 == "+2" || $elem_pscOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_241" >2+</label>
		</td><td><input id="od_242" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOd_pos3" value="3+" <?php echo ($elem_pscOd_pos3 == "+3" || $elem_pscOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_242" >3+</label>
		</td><td colspan="2"><input id="od_243" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOd_pos4" value="4+" <?php echo ($elem_pscOd_pos4 == "+4" || $elem_pscOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_243" >4+</label>
		</td>			
		<td align="center" class="bilat" onClick="check_bl('PSC')">BL</td>
		<td align="left">PSC</td>
		<td>
		<input id="os_238" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOs_neg" value="Absent" <?php echo ($elem_pscOs_neg == "-ve" || $elem_pscOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="os_238" >Absent</label>
		</td><td><input id="os_239" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOs_T" value="T" <?php echo ($elem_pscOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_239" >T</label>
		</td><td><input id="os_240" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOs_pos1" value="1+" <?php echo ($elem_pscOs_pos1 == "+1" || $elem_pscOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_240" >1+</label>
		</td><td><input id="os_241" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOs_pos2" value="2+" <?php echo ($elem_pscOs_pos2 == "+2" || $elem_pscOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_241" >2+</label>
		</td><td><input id="os_242" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOs_pos3" value="3+" <?php echo ($elem_pscOs_pos3 == "+3" || $elem_pscOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_242" >3+</label>
		</td><td colspan="2"><input id="os_243" type="checkbox"  onclick="checkAbsent(this)" name="elem_pscOs_pos4" value="4+" <?php echo ($elem_pscOs_pos4 == "+4" || $elem_pscOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_243" >4+</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["PSC"])){ echo $arr_exm_ext_htm["Lens"]["PSC"]; }  ?>
		
		<tr id="d_PXS">
		<td align="left">PSX</td>
		<td>
		<input id="od_264" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOd_neg" value="Absent" <?php echo ($elem_pxfOd_neg == "-ve" || $elem_pxfOd_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="od_264" >Absent</label>
		</td><td><input id="od_265" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOd_T" value="T" <?php echo ($elem_pxfOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_265" >T</label>
		</td><td><input id="od_266" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOd_pos1" value="1+" <?php echo ($elem_pxfOd_pos1 == "+1" || $elem_pxfOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_266" >1+</label>
		</td><td><input id="od_267" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOd_pos2" value="2+" <?php echo ($elem_pxfOd_pos2 == "+2" || $elem_pxfOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_267" >2+</label>
		</td><td><input id="od_268" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOd_pos3" value="3+" <?php echo ($elem_pxfOd_pos3 == "+3" || $elem_pxfOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_268" >3+</label>
		</td><td colspan="2"><input id="od_269" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOd_pos4" value="4+" <?php echo ($elem_pxfOd_pos4 == "+4" || $elem_pxfOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_269" >4+</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('PXS')">BL</td>
		<td align="left">PSX</td>
		<td>
		<input id="os_264" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOs_neg" value="Absent" <?php echo ($elem_pxfOs_neg == "-ve" || $elem_pxfOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="os_264" >Absent</label>
		</td><td><input id="os_265" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOs_T" value="T" <?php echo ($elem_pxfOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_265" >T</label>
		</td><td><input id="os_266" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOs_pos1" value="1+" <?php echo ($elem_pxfOs_pos1 == "+1" || $elem_pxfOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_266" >1+</label>
		</td><td><input id="os_267" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOs_pos2" value="2+" <?php echo ($elem_pxfOs_pos2 == "+2" || $elem_pxfOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_267" >2+</label>
		</td><td><input id="os_268" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOs_pos3" value="3+" <?php echo ($elem_pxfOs_pos3 == "+3" || $elem_pxfOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_268" >3+</label>
		</td><td colspan="2"><input id="os_269" type="checkbox"  onclick="checkAbsent(this)" name="elem_pxfOs_pos4" value="4+" <?php echo ($elem_pxfOs_pos4 == "+4" || $elem_pxfOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_269" >4+</label>
		</td>			
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["PSX"])){ echo $arr_exm_ext_htm["Lens"]["PSX"]; }  ?>
		
		<tr id="d_PCO">
		<td align="left">PCO/Secondary Membrane</td>
		<td>
		<input id="od_271" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_Open" value="open" <?php if($elem_pcoOd_Open=='open') echo 'CHECKED'; ?>><label for="od_271" >Open</label>
		</td><td><input id="od_272" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_Pos" value="Present" <?php if($elem_pcoOd_Pos=="+ve" || $elem_pcoOd_Pos=="Present") echo 'CHECKED'; ?>><label for="od_272" >Present</label>
		</td><td><input id="od_273" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_T" value="T" <?php if($elem_pcoOd_T=='T') echo 'CHECKED'; ?>><label for="od_273" >T</label>
		</td><td><input id="od_274" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_1" value="1+" <?php if($elem_pcoOd_1=="+1" || $elem_pcoOd_1=="1+") echo 'CHECKED'; ?>><label for="od_274" >1+</label>
		</td><td><input id="od_275" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_2" value="2+" <?php if($elem_pcoOd_2=="+2" || $elem_pcoOd_2=="2+") echo 'CHECKED'; ?>><label for="od_275" >2+</label>
		</td><td><input id="od_276" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_3" value="3+" <?php if($elem_pcoOd_3=="+3" || $elem_pcoOd_3=="3+") echo 'CHECKED'; ?>><label for="od_276" >3+</label>
		</td><td><input id="od_277" type="checkbox"  onclick="checkwnls()" name="elem_pcoOd_4" value="4+" <?php if($elem_pcoOd_4=="+4" || $elem_pcoOd_4=="4+") echo 'CHECKED'; ?>><label for="od_277"  >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('PCO')">BL</td>
		<td align="left">PCO/Secondary Membrane</td>
		<td>
		<input id="os_271" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_Open" value="open" <?php if($elem_pcoOs_Open=='open') echo 'CHECKED'; ?>><label for="os_271" >Open</label>
		</td><td><input id="os_272" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_Pos" value="Present" <?php if($elem_pcoOs_Pos=="+ve" || $elem_pcoOs_Pos=="Present") echo 'CHECKED'; ?>><label for="os_272" >Present</label>
		</td><td><input id="os_273" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_T" value="T" <?php if($elem_pcoOs_T=='T') echo 'CHECKED'; ?>><label for="os_273" >T</label>
		</td><td><input id="os_274" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_1" value="1+" <?php if($elem_pcoOs_1=="+1" || $elem_pcoOs_1=="1+") echo 'CHECKED'; ?>><label for="os_274" >1+</label>
		</td><td><input id="os_275" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_2" value="2+" <?php if($elem_pcoOs_2=="+2" || $elem_pcoOs_2=="2+") echo 'CHECKED'; ?>><label for="os_275" >2+</label>
		</td><td><input id="os_276" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_3" value="3+" <?php if($elem_pcoOs_3=="+3" || $elem_pcoOs_3=="3+") echo 'CHECKED'; ?>><label for="os_276" >3+</label>
		</td><td><input id="os_277" type="checkbox"  onclick="checkwnls()" name="elem_pcoOs_4" value="4+" <?php if($elem_pcoOs_4=="+4" || $elem_pcoOs_4=="4+") echo 'CHECKED';  ?>><label for="os_277"  >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["PCO/Secondary Membrane"])){ echo $arr_exm_ext_htm["Lens"]["PCO/Secondary Membrane"]; }  ?>
		
		<tr id="d_IOL" class="grp_IOL">
		<td align="left">IOL</td>
		<td>
		<input id="od_278" value="PC" name="elem_pciolOd_pciol" <?php if($elem_pciolOd_pciol == 'PCIOL'||$elem_pciolOd_pciol == 'PC') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="od_278" >PC</label>
		</td><td><input value="AC" id="elem_aciolOd_pciol" name="elem_aciolOd_pciol" <?php if($elem_aciolOd_pciol == 'ACIOL'||$elem_aciolOd_pciol == 'AC') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="elem_aciolOd_pciol" >AC</label>
		</td><td colspan="5"><input value="Toric" id="elem_toriciolOd_pciol" name="elem_toriciolOd_pciol" <?php if($elem_toriciolOd_pciol == 'Toric IOL'||$elem_toriciolOd_pciol == 'Toric') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="elem_toriciolOd_pciol" >Toric</label>		
		</td>			
		<td align="center" class="bilat" onClick="check_bl('IOL')" rowspan="3">BL</td>
		<td align="left">IOL</td>
		<td>
		<input id="os_278" value="PC" name="elem_pciolOs_pciol" <?php if($elem_pciolOs_pciol == 'PCIOL'||$elem_pciolOs_pciol == 'PC') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="os_278" >PC</label>
		</td><td><input value="AC" id="elem_aciolOs_pciol" name="elem_aciolOs_pciol" <?php if($elem_aciolOs_pciol == 'ACIOL'||$elem_aciolOs_pciol == 'AC') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="elem_aciolOs_pciol" >AC</label>
		</td><td colspan="5"><input value="Toric" id="elem_toriciolOs_pciol" name="elem_toriciolOs_pciol" <?php if($elem_toriciolOs_pciol == 'Toric IOL'||$elem_toriciolOs_pciol == 'Toric') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="elem_toriciolOs_pciol" >Toric</label>		
		</td>				
		</tr>
		<tr  class="grp_IOL">
		<td align="left"></td>
		<td colspan="2" class="form-inline">			
		<label for="elem_iolAxisOd_pciol">IOL axis</label>	
		<input value="<?php   $elem_iolAxisOd_pciol = str_replace(array("IOL", "Axis","degrees"),"", $elem_iolAxisOd_pciol); $elem_iolAxisOd_pciol=trim($elem_iolAxisOd_pciol);     echo($elem_iolAxisOd_pciol)?>" id="elem_iolAxisOd_pciol" name="elem_iolAxisOd_pciol" type="text" onChange="checkwnls()" size="8" class="form-control">
		<?php // MULTIFOCAL?>		
		</td>
		<td ><input value="Multifocal" id="elem_mfocalOd_pciol" name="elem_mfocalOd_pciol" <?php if($elem_mfocalOd_pciol == 'Multifocal') echo 'CHECKED'; ?> type="checkbox" ><label for="elem_mfocalOd_pciol" >Multifocal</label>
		</td>
		<td valign="top" colspan="2">
		<input type="text" class="form-control <?php echo $clsmfselod;?>" id="elem_mfocalOd_pciol_opts" name="elem_mfocalOd_pciol_opts" value="<?php echo $elem_mfocalOd_pciol_opts; ?>" size="6">
		<!--
		<select name="elem_mfocalOd_pciol_opts" onChange="showMOpts('mfodsel')" class="form-control <?php echo $clsmfselod;?>">
		<option value=""></option>
		<option value="Restor" <?php //if($elem_mfocalOd_pciol_opts == 'Restor') echo 'SELECTED'; ?>>Restor</option>
		<option value="Tecnis" <?php //if($elem_mfocalOd_pciol_opts == 'Tecnis') echo 'SELECTED'; ?>>Tecnis</option>
		<option value="Other" <?php //if($elem_mfocalOd_pciol_opts == 'Other') echo 'SELECTED'; ?>>Other</option>	
		</select>
		-->
		</td>
		<!--
		<td>	
		<span id="spn_elem_mfocalOd_pciol" class="<?php //echo $clsmfselod_othr;?>">
		<input value="<?php //echo($elem_mfocalOd_pciol_other)?>" name="elem_mfocalOd_pciol_other" type="text" onChange="checkwnls()" size="6" class="form-control ">
		<span class="spnFuDel glyphicon glyphicon-close" onClick="showMOpts(0);" ></span>
		</span>		
		</td>
		-->
		<?php //End MULTIFOCAL ?>
		<td colspan="2">
		<input id="od_symfony" value="Symfony" name="elem_symfonyOd_pciol" <?php if($elem_symfonyOd_pciol == 'Symfony') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="od_symfony"  >Symfony</label>
		</td>		
		
		<td align="left"></td>
		<td colspan="2" class="form-inline">	
		<label for="elem_iolAxisOs_pciol">IOL axis</label>
		<input value="<?php $elem_iolAxisOs_pciol = str_replace(array("IOL", "Axis","degrees"),"", $elem_iolAxisOs_pciol); $elem_iolAxisOs_pciol=trim($elem_iolAxisOs_pciol); echo($elem_iolAxisOs_pciol)?>" id="elem_iolAxisOs_pciol" name="elem_iolAxisOs_pciol" type="text" onChange="checkwnls()" size="8" class="form-control">
		</td><td >
		<?php // MULTIFOCAL ?>
		<input value="Multifocal" id="elem_mfocalOs_pciol" name="elem_mfocalOs_pciol" <?php if($elem_mfocalOs_pciol == 'Multifocal') echo 'CHECKED'; ?> type="checkbox"  ><label for="elem_mfocalOs_pciol"  class="iol_mul">Multifocal</label>
		</td><td valign="top" colspan="2">
		<input type="text" class="form-control <?php echo $clsmfselos;?>" id="elem_mfocalOs_pciol_opts" name="elem_mfocalOs_pciol_opts" value="<?php echo $elem_mfocalOs_pciol_opts; ?>" size="6">
		<!--
		<select name="elem_mfocalOs_pciol_opts" onChange="showMOpts('mfossel')" class="form-control <?php //echo $clsmfselos;?>">
		<option value=""></option>
		<option value="Restor" <?php //if($elem_mfocalOs_pciol_opts == 'Restor') echo 'SELECTED'; ?>>Restor</option>
		<option value="Tecnis" <?php //if($elem_mfocalOs_pciol_opts == 'Tecnis') echo 'SELECTED'; ?>>Tecnis</option>
		<option value="Other" <?php //if($elem_mfocalOs_pciol_opts == 'Other') echo 'SELECTED'; ?>>Other</option>	
		</select>
		-->
		</td>
		<!--
		<td>	
		<span id="spn_elem_mfocalOs_pciol" class="<?php //echo $clsmfselos_othr;?>">
		<input value="<?php //echo($elem_mfocalOs_pciol_other)?>" name="elem_mfocalOs_pciol_other" type="text" onChange="checkwnls()" size="6" class="form-control ">
		<span class="spnFuDel glyphicon glyphicon-close" onClick="showMOpts(1)"  ></span>
		</span>		
		</td>
		-->
		<?php //End MULTIFOCAL ?>
		<td colspan="2">
		<input id="os_symfony" value="Symfony" name="elem_symfonyOs_pciol" <?php if($elem_symfonyOs_pciol == 'Symfony') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="os_symfony"  >Symfony</label>
		</td>		
		</tr>
		
		<tr class="grp_IOL">
		<td align="left"></td>
		<td><input id="elem_CenteredOd_pciol" value="Centered" name="elem_CenteredOd_pciol" <?php if($elem_CenteredOd_pciol == 'Centered') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="elem_CenteredOd_pciol"  >Centered</label></td>
		<td colspan="2">
		<input id="od_279" value="Capsule Bag" name="elem_CapsuleBagOd_pciol" <?php if($elem_CapsuleBagOd_pciol == 'Capsule Bag') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="od_279"  class="lens_capbag">Capsule Bag</label>
		</td><td>
		<input id="od_280" value="Sulcus" name="elem_SulcusOd_pciol" <?php if($elem_SulcusOd_pciol == 'Sulcus') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="od_280" >Sulcus</label>
		</td><td colspan="3">
		<input value="Aphakia" name="elem_AphakiaOd_pciol" id="elem_AphakiaOd_pciol" 
		<?php if($elem_AphakiaOd_pciol == 'Aphakia') echo 'CHECKED'; ?> 
		type="checkbox" onClick="checkwnls()"><label for="elem_AphakiaOd_pciol" >Aphakia</label>
		</td>		
		
		<td align="left"></td>
		<td><input id="elem_CenteredOs_pciol" value="Centered" name="elem_CenteredOs_pciol" <?php if($elem_CenteredOs_pciol == 'Centered') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="elem_CenteredOs_pciol"  >Centered</label></td>
		<td colspan="2">		
		<input id="os_279" value="Capsule Bag" name="elem_CapsuleBagOs_pciol" <?php if($elem_CapsuleBagOs_pciol == 'Capsule Bag') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="os_279"  class="lens_capbag">Capsule Bag</label>
		</td><td>
		<input id="os_280" value="Sulcus" name="elem_SulcusOs_pciol" <?php if($elem_SulcusOs_pciol == 'Sulcus') echo 'CHECKED'; ?> type="checkbox" onClick="checkwnls()"><label for="os_280" >Sulcus</label>
		</td><td colspan="3">
		<input value="Aphakia" id="elem_AphakiaOs_pciol" name="elem_AphakiaOs_pciol" 
		<?php if($elem_AphakiaOs_pciol == 'Aphakia') echo 'CHECKED'; ?> 
		type="checkbox" onClick="checkwnls()"><label for="elem_AphakiaOs_pciol" >Aphakia</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["IOL/IOL"])){ echo $arr_exm_ext_htm["Lens"]["IOL/IOL"]; }  ?>
		<tr id="d_IOL_decen" class="grp_IOL_decen">
		<td align="left">De-centered</td>
		<td>
		<input id="od_254" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_sup" value="Sup" 
		<?php echo ($elem_deCenteredOd_sup == "Sup") ? "checked=\"checked\"" : ""; ?>><label for="od_254" >Sup</label>
		</td><td><input id="od_255" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_inf" value="Inf" 
		<?php echo ($elem_deCenteredOd_inf == "Inf") ? "checked=\"checked\"" : ""; ?>><label for="od_255" >Inf</label>
		</td><td><input id="od_256" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_nasal" value="Nasal" 
		<?php echo ($elem_deCenteredOd_nasal == "Nasal") ? "checked=\"checked\"" : ""; ?>><label for="od_256" >Nasal</label>
		</td><td><input id="od_257" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_temp" value="Temp" 
		<?php echo ($elem_deCenteredOd_temp == "Temp") ? "checked=\"checked\"" : ""; ?>><label for="od_257" >Temp</label>
		</td><td colspan="3"><input id="elem_deCenteredOd_iolgoodpos" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_iolgoodpos" value="IOL in good position" 
		<?php echo ($elem_deCenteredOd_iolgoodpos == "IOL in good position") ? "checked=\"checked\"" : ""; ?>><label for="elem_deCenteredOd_iolgoodpos" >IOL in good position</label>
		</td>		
		<td align="center" class="bilat" rowspan="2" onClick="check_bl('IOL_decen')">BL</td>
		<td align="left">De-centered</td>
		<td>
		<input id="os_254" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_sup" value="Sup" 
		<?php echo ($elem_deCenteredOs_sup == "Sup") ? "checked=\"checked\"" : ""; ?>><label for="os_254" >Sup</label>
		</td><td><input id="os_255" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_inf" value="Inf" 
		<?php echo ($elem_deCenteredOs_inf == "Inf") ? "checked=\"checked\"" : ""; ?>><label for="os_255" >Inf</label>
		</td><td><input id="os_256" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_nasal" value="Nasal" 
		<?php echo ($elem_deCenteredOs_nasal == "Nasal") ? "checked=\"checked\"" : ""; ?>><label for="os_256" >Nasal</label>
		</td><td><input id="os_257" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_temp" value="Temp" 
		<?php echo ($elem_deCenteredOs_temp == "Temp") ? "checked=\"checked\"" : ""; ?>><label for="os_257" >Temp</label>
		</td><td colspan="3" ><input id="elem_deCenteredOs_iolgoodpos" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_iolgoodpos" value="IOL in good position" 
		<?php echo ($elem_deCenteredOs_iolgoodpos == "IOL in good position") ? "checked=\"checked\"" : ""; ?>><label for="elem_deCenteredOs_iolgoodpos" >IOL in good position</label>
		</td>		
		</tr>
		
		<tr id="d_IOL_decen1" class="grp_IOL_decen">
		<td align="left"></td>
		<td><input id="od_258" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_neg" value="Absent" 
		<?php echo ($elem_deCenteredOd_neg == "-ve" || $elem_deCenteredOd_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="od_258" >Absent</label>
		</td><td><input id="od_259" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_T" value="T" 
		<?php echo ($elem_deCenteredOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_259" >T</label>
		</td><td><input id="od_260" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_pos1" value="1+" 
		<?php echo ($elem_deCenteredOd_pos1 == "+1" || $elem_deCenteredOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_260" >1+</label>
		</td><td><input id="od_261" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_pos2" value="2+" 
		<?php echo ($elem_deCenteredOd_pos2 == "+2" || $elem_deCenteredOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_261" >2+</label>
		</td><td><input id="od_262" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_pos3" value="3+" 
		<?php echo ($elem_deCenteredOd_pos3 == "+3" || $elem_deCenteredOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_262" >3+</label>
		</td><td colspan="2"><input id="od_263" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOd_pos4" value="4+" 
		<?php echo ($elem_deCenteredOd_pos4 == "+4" || $elem_deCenteredOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_263" >4+</label>
		</td>		
		
		<td align="left"></td>
		<td><input id="os_258" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_neg" value="Absent" 
		<?php echo ($elem_deCenteredOs_neg == "-ve" || $elem_deCenteredOs_neg == "Absent") ? "checked=\"checked\"" : ""; ?>><label for="os_258" >Absent</label>
		</td><td><input id="os_259" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_T" value="T" 
		<?php echo ($elem_deCenteredOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_259" >T</label>
		</td><td><input id="os_260" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_pos1" value="1+" 
		<?php echo ($elem_deCenteredOs_pos1 == "+1" || $elem_deCenteredOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_260" >1+</label>
		</td><td><input id="os_261" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_pos2" value="2+" 
		<?php echo ($elem_deCenteredOs_pos2 == "+2" || $elem_deCenteredOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_261" >2+</label>
		</td><td><input id="os_262" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_pos3" value="3+" 
		<?php echo ($elem_deCenteredOs_pos3 == "+3" || $elem_deCenteredOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_262" >3+</label>
		</td><td colspan="2"><input id="os_263" type="checkbox"  onclick="checkAbsent(this)" name="elem_deCenteredOs_pos4" value="4+" 
		<?php echo ($elem_deCenteredOs_pos4 == "+4" || $elem_deCenteredOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_263" >4+</label>	
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["IOL/De-centered"])){ echo $arr_exm_ext_htm["Lens"]["IOL/De-centered"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lens"]["IOL"])){ echo $arr_exm_ext_htm["Lens"]["IOL"]; }  ?>
		
		<tr id="d_pcHaze">
		<td align="left">PC Haze</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOd_T" name="elem_pcHazeOd_T" value="T" <?php echo ($elem_pcHazeOd_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOd_T" >T</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOd_pos1" name="elem_pcHazeOd_pos1" value="1+" <?php echo ($elem_pcHazeOd_pos1 == "+1" || $elem_pcHazeOd_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOd_pos1" >1+</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOd_pos2" name="elem_pcHazeOd_pos2" value="2+" <?php echo ($elem_pcHazeOd_pos2 == "+2" || $elem_pcHazeOd_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOd_pos2" >2+</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOd_pos3" name="elem_pcHazeOd_pos3" value="3+" <?php echo ($elem_pcHazeOd_pos3 == "+3" || $elem_pcHazeOd_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOd_pos3" >3+</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOd_pos4" name="elem_pcHazeOd_pos4" value="4+" <?php echo ($elem_pcHazeOd_pos4 == "+4" || $elem_pcHazeOd_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOd_pos4" >4+</label>
		</td>		
		<td align="center" class="bilat" onClick="check_bl('pcHaze')">BL</td>
		<td align="left">PC Haze</td>
		<td>
		<input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOs_T" name="elem_pcHazeOs_T" value="T" <?php echo ($elem_pcHazeOs_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOs_T" >T</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOs_pos1" name="elem_pcHazeOs_pos1" value="1+" <?php echo ($elem_pcHazeOs_pos1 == "+1" || $elem_pcHazeOs_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOs_pos1" >1+</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOs_pos2" name="elem_pcHazeOs_pos2" value="2+" <?php echo ($elem_pcHazeOs_pos2 == "+2" || $elem_pcHazeOs_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOs_pos2" >2+</label>
		</td><td><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOs_pos3" name="elem_pcHazeOs_pos3" value="3+" <?php echo ($elem_pcHazeOs_pos3 == "+3" || $elem_pcHazeOs_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOs_pos3" >3+</label>
		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls()" id="elem_pcHazeOs_pos4" name="elem_pcHazeOs_pos4" value="4+" <?php echo ($elem_pcHazeOs_pos4 == "+4" || $elem_pcHazeOs_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="elem_pcHazeOs_pos4" >4+</label>
		</td>		
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lens"]["PC Haze"])){ echo $arr_exm_ext_htm["Lens"]["PC Haze"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lens"]["Main"])){ echo $arr_exm_ext_htm["Lens"]["Main"]; }  ?>
		
		<tr id="d_adOpt_Lens">
		<td align="left">Comments</td>
		<td colspan="7"><textarea  onblur="checkwnls()"  id="od_270" name="elem_lensAdvanceOptionsOd" class="form-control" ><?php echo ($elem_lensAdvanceOptionsOd); ?></textarea></td>		
		<td align="center" class="bilat" onClick="check_bl('adOpt_Lens')">BL</td>
		<td align="left">Comments</td>
		<td colspan="7"><textarea  onblur="checkwnls()"  id="os_270" name="elem_lensAdvanceOptionsOs" class="form-control" ><?php echo ($elem_lensAdvanceOptionsOs); ?></textarea></td>		
		</tr>		
		
		</table>
	</div>
	<div class="clearfix"> </div>
	</div>
	<div role="tabpanel" class="tab-pane <?php echo (6 == $defTabKey) ? "active" : "" ?>" id="div6">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1">
				
			</div>
			<div class="col-sm-2 pharmo mt5">			
				<!-- Pen Light --*>					
				<input type="checkbox" id="elem_penLightDraw" name="elem_penLightDraw" value="1" <?php //echo !empty($elem_penLight) ? " checked=\"checked\"  " : "" ;?>  >
				<label id="lblPenLight" for="elem_penLightDraw"><strong>Pen Light</strong></label>				
				<!-- Pen Light -->
			</div>    
			<div class="col-sm-9"> 
				<span id="examFlag" class="glyphicon flagWnl "></span>
		
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChangeDraw"  name="elem_noChangeDraw" value="1" onClick="setNC2();" 
					<?php echo ($elem_ncDraw == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChangeDraw">NO Change</label>

				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
			</div>    
		</div>
	</div>    
	<div class="clearfix"> </div>
	<div class="row">
			<div class="col-sm-2">
				<textarea onBlur="checkwnls()" name="cornea_od_desc_1" id="cornea_od_desc_1" class="form-control drw_text_box" ><?php echo $cornea_od_desc_1;?></textarea>
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
							//$dbTollImage = "imgFaceCanvas";
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
							<input type="hidden" name="hidSLEDrawingId<?php echo $intTempDrawCount; ?>" id="hidSLEDrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
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
				<textarea onBlur="checkwnls()" name="cornea_os_desc" id="cornea_os_desc" class="form-control drw_text_box"><?php echo $cornea_os_desc;?></textarea>
			</div>
	</div>	
	<div class="clearfix"> </div>
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

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>
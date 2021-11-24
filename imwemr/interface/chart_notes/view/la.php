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
	var examName = "LA";
	var arrSubExams = new Array("Lids","Lesion","LidPos","LacSys","Draw");	
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
        <input id="elem_saveForm" type="hidden" name="elem_saveForm" value="l_and_atable1">
        <input  type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
        <input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
        <input type="hidden" name="elem_patientId" value="<?php echo $elem_patientId;?>">
        <input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
        <input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
        <input type="hidden" name="elem_laId" value="<?php echo $elem_laId;?>">
        <input type="hidden" name="elem_laId_LF" value="<?php echo $elem_laId_LF;?>">
        <input type="hidden" name="elem_drawId_LF" value="<?php echo $elem_drawId_LF;?>">	
        <input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
	<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">        
        <input type="hidden" name="elem_wnlLids" value="<?php echo $elem_wnlLids;?>">
        <input type="hidden" name="elem_wnlLesion" value="<?php echo $elem_wnlLesion;?>">
        <input type="hidden" name="elem_wnlLidPos" value="<?php echo $elem_wnlLidPos;?>">
        <input type="hidden" name="elem_wnlLacSys" value="<?php echo $elem_wnlLacSys;?>">
        <input type="hidden" name="elem_wnlDraw" value="<?php echo $elem_wnlDraw;?>">
        
        <input type="hidden" id="elem_posLids" name="elem_posLids" value="<?php echo $elem_posLids;?>">
        <input type="hidden" id="elem_posLesion" name="elem_posLesion" value="<?php echo $elem_posLesion;?>">
        <input type="hidden" id="elem_posLidPos" name="elem_posLidPos" value="<?php echo $elem_posLidPos;?>">
        <input type="hidden" id="elem_posLacSys" name="elem_posLacSys" value="<?php echo $elem_posLacSys;?>">
        <input type="hidden" id="elem_posDraw" name="elem_posDraw" value="<?php echo $elem_posDraw;?>">
        
        <input type="hidden" name="elem_ncLids" value="<?php echo $elem_ncLids;?>">
        <input type="hidden" name="elem_ncLesion" value="<?php echo $elem_ncLesion;?>">
        <input type="hidden" name="elem_ncLidPos" value="<?php echo $elem_ncLidPos;?>">
        <input type="hidden" name="elem_ncLacSys" value="<?php echo $elem_ncLacSys;?>">
        <input type="hidden" name="elem_ncDraw" value="<?php echo $elem_ncDraw;?>">
        <input type="hidden" id="elem_examined_no_change" name="la_examined_nochange" value="<?php echo $la_examined_nochange;?>">
        
        <input type="hidden" name="elem_wnlLidsOd" value="<?php echo $elem_wnlLidsOd;?>">
        <input type="hidden" name="elem_wnlLidsOs" value="<?php echo $elem_wnlLidsOs;?>">
        <input type="hidden" name="elem_wnlLesionOd" value="<?php echo $elem_wnlLesionOd;?>">
        <input type="hidden" name="elem_wnlLesionOs" value="<?php echo $elem_wnlLesionOs;?>">
        <input type="hidden" name="elem_wnlLidPosOd" value="<?php echo $elem_wnlLidPosOd;?>">
        <input type="hidden" name="elem_wnlLidPosOs" value="<?php echo $elem_wnlLidPosOs;?>">
        <input type="hidden" name="elem_wnlLacSysOd" value="<?php echo $elem_wnlLacSysOd;?>">
        <input type="hidden" name="elem_wnlLacSysOs" value="<?php echo $elem_wnlLacSysOs;?>">
        <input type="hidden" name="elem_wnlDrawOd" value="<?php echo $elem_wnlDrawOd;?>">
        <input type="hidden" name="elem_wnlDrawOs" value="<?php echo $elem_wnlDrawOs;?>">
        <input type="hidden" name="elem_descLa" value="<?php echo $elem_descLa;?>">
        <input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
        <!--<input type="hidden" name="hidLADrawingId" id="hidLADrawingId" value="<?php //echo $dbIdocDrawingId;?>">-->
        <input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">
        
        <input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
        <input type="hidden" id="elem_utElemsLids" name="elem_utElemsLids" value="<?php echo $elem_utElemsLids;?>">
        <input type="hidden" id="elem_utElemsLids_cur" name="elem_utElemsLids_cur" value="<?php echo $elem_utElemsLids_cur;?>">
        <input type="hidden" id="elem_utElemsLesion" name="elem_utElemsLesion" value="<?php echo $elem_utElemsLesion;?>">
        <input type="hidden" id="elem_utElemsLesion_cur" name="elem_utElemsLesion_cur" value="<?php echo $elem_utElemsLesion_cur;?>">	
        <input type="hidden" id="elem_utElemsLidPos" name="elem_utElemsLidPos" value="<?php echo $elem_utElemsLidPos;?>">
        <input type="hidden" id="elem_utElemsLidPos_cur" name="elem_utElemsLidPos_cur" value="<?php echo $elem_utElemsLidPos_cur;?>">
        <input type="hidden" id="elem_utElemsLacSys" name="elem_utElemsLacSys" value="<?php echo $elem_utElemsLacSys;?>">
        <input type="hidden" id="elem_utElemsLacSys_cur" name="elem_utElemsLacSys_cur" value="<?php echo $elem_utElemsLacSys_cur;?>">
	<input type="hidden" id="elem_utElemsDraw" name="elem_utElemsDraw" value="<?php echo $elem_utElemsDraw;?>">
        <input type="hidden" id="elem_utElemsDraw_cur" name="elem_utElemsDraw_cur" value="<?php echo $elem_utElemsDraw_cur;?>">	

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
		if($key == "3"){
		$tmp2="LidPos";
		}else if($key == "4"){
		$tmp2="LacSys";
		}else if($key == "5"){
		$tmp2="Draw";
		}else{
		$tmp2=$val;
		}
		$tmp = ($key == $defTabKey) ? "active" : "";
		
	?>
	<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab" onclick="changeTab('<?php echo $key;?>')" id="tab<?php echo $key;?>" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane <?php if(1 == $defTabKey){echo "active";} ?>" id="div1">
	<div class="examhd">
	<?php if($finalize_flag == 1){?>
		<label class="chart_status label label-danger pull-left">Finalized</label>
	<?php }?>

	<span id="examFlag" class="glyphicon flagWnl "></span>
		
	<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

	<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();" 
		<?php echo ($elem_ncLids == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
	<label class="lbl_nochange frcb" for="elem_noChange">NO Change</label>

	<?php /*if (constant('AV_MODULE')=='YES'){?>
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
	<?php }*/ ?>
	</div>    
	<div class="clearfix"> </div>
	
	<!-- LA -->
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
		
		<tr id="d_blep">
		<td align="left">Blepharitis</td>
		<td>
		<input id="od_1" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_neg" value="Absent" <?php echo ($elem_blephaOd_neg == "-ve" || $elem_blephaOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_1">Absent</label>
		</td>
		<td>
		<input id="od_2" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_T" value="T" <?php echo ($elem_blephaOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_2">T</label>
		</td>
		<td>
		<input id="od_3" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_pos1" value="1+" <?php echo ($elem_blephaOd_pos1 == "+1" || $elem_blephaOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_3">1+</label>
		</td>
		<td>
		<input id="od_4" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_pos2" value="2+" <?php echo ($elem_blephaOd_pos2 == "+2" || $elem_blephaOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_4">2+</label>
		</td>
		<td>
		<input id="od_5" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_pos3" value="3+" <?php echo ($elem_blephaOd_pos3 == "+3" || $elem_blephaOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_5">3+</label>
		</td>
		<td>
		<input id="od_6" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_pos4" value="4+" <?php echo ($elem_blephaOd_pos4 == "+4" || $elem_blephaOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_6">4+</label>
		</td>
		<td>
		<input id="od_8" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_rul" value="RUL" <?php echo ($elem_blephaOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_8">RUL</label>
		</td>
		<td>
		<input id="od_9" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOd_rll" value="RLL" <?php echo ($elem_blephaOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_9">RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('blep')">BL</td>
		<td align="left">Blepharitis</td>
		<td>
		<input id="os_1" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_neg" value="Absent" <?php echo ($elem_blephaOs_neg == "-ve" || $elem_blephaOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_1">Absent</label>				
		</td>
		<td>
		<input id="os_2" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_T" value="T" <?php echo ($elem_blephaOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_2">T</label>
		</td>
		<td>			
		<input id="os_3" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_pos1" value="1+" <?php echo ($elem_blephaOs_pos1 == "+1" || $elem_blephaOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_3">1+</label>
		</td>
		<td>			
		<input id="os_4" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_pos2" value="2+" <?php echo ($elem_blephaOs_pos2 == "+2" || $elem_blephaOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_4">2+</label>
		</td>
		<td>			
		<input id="os_5" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_pos3" value="3+" <?php echo ($elem_blephaOs_pos3 == "+3" || $elem_blephaOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_5">3+</label>
		</td>
		<td>			
		<input id="os_6" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_pos4" value="4+" <?php echo ($elem_blephaOs_pos4 == "+4" || $elem_blephaOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_6">4+</label>
		</td>
		<td>			
		<input id="os_8" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_rul" value="LUL" <?php echo ($elem_blephaOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_8">LUL</label>
		</td>
		<td>			
		<input id="os_9" type="checkbox"  onclick="checkAbsent(this)" name="elem_blephaOs_rll" value="LLL" <?php echo ($elem_blephaOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_9">LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Blepharitis"])){ echo $arr_exm_ext_htm["Lids"]["Blepharitis"]; }  ?>
		
		<tr id="d_angblep">
		<td align="left">Angular Blepharitis</td>
		<td>
		<input id="od_10" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_neg" value="Absent" <?php echo ($elem_angBlephaOd_neg == "-ve" || $elem_angBlephaOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_10">Absent</label>
		</td>
		<td>
		<input id="od_11" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_T" value="T" <?php echo ($elem_angBlephaOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_11">T</label>
		</td>
		<td>				
		<input id="od_12" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_pos1" value="1+" <?php echo ($elem_angBlephaOd_pos1 == "+1" || $elem_angBlephaOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_12">1+</label>
		</td>
		<td>				
		<input id="od_13" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_pos2" value="2+" <?php echo ($elem_angBlephaOd_pos2 == "+2" || $elem_angBlephaOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_13">2+</label>
		</td>
		<td>				
		<input id="od_14" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_pos3" value="3+" <?php echo ($elem_angBlephaOd_pos3 == "+3" || $elem_angBlephaOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_14">3+</label>
		</td>
		<td>				
		<input id="od_15" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_pos4" value="4+" <?php echo ($elem_angBlephaOd_pos4 == "+4" || $elem_angBlephaOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_15">4+</label>
		</td>
		<td>				
		<input id="od_17" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_rul" value="RUL" <?php echo ($elem_angBlephaOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_17">RUL</label>
		</td>
		<td>				
		<input id="od_18" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOd_rll" value="RLL" <?php echo ($elem_angBlephaOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_18">RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('angblep')">BL</td>
		<td align="left">Angular Blepharitis</td>
		<td>
		<input id="os_10" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_neg" value="Absent" <?php echo ($elem_angBlephaOs_neg == "-ve" || $elem_angBlephaOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_10" >Absent</label>
		</td>
		<td>
		<input id="os_11" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_T" value="T" <?php echo ($elem_angBlephaOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_11" >T</label>
		</td>
		<td>				
		<input id="os_12" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_pos1" value="1+" <?php echo ($elem_angBlephaOs_pos1 == "+1" || $elem_angBlephaOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_12" >1+</label>
		</td>
		<td>				
		<input id="os_13" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_pos2" value="2+" <?php echo ($elem_angBlephaOs_pos2 == "+2" || $elem_angBlephaOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_13" >2+</label>
		</td>
		<td>				
		<input id="os_14" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_pos3" value="3+" <?php echo ($elem_angBlephaOs_pos3 == "+3" || $elem_angBlephaOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_14" >3+</label>
		</td>
		<td>				
		<input id="os_15" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_pos4" value="4+" <?php echo ($elem_angBlephaOs_pos4 == "+4" || $elem_angBlephaOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_15" >4+</label>
		</td>
		<td>				
		<input id="os_17" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_rul" value="LUL" <?php echo ($elem_angBlephaOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_17" >LUL</label>
		</td>
		<td>				
		<input id="os_18" type="checkbox"  onclick="checkAbsent(this)" name="elem_angBlephaOs_rll" value="LLL" <?php echo ($elem_angBlephaOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_18" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Angular Blepharitis"])){ echo $arr_exm_ext_htm["Lids"]["Angular Blepharitis"]; }  ?>
		
		<tr id="d_Meibo">
		<td align="left">Meibomitis</td>
		<td>
		<input id="od_19" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_neg" value="Absent" <?php echo ($elem_meiboOd_neg == "-ve" || $elem_meiboOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_19" >Absent</label>
		</td>
		<td>	
		<input id="od_20" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_T" value="T" <?php echo ($elem_meiboOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_20" >T</label>
		</td>
		<td>	
		<input id="od_21" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_pos1" value="1+" <?php echo ($elem_meiboOd_pos1 == "+1" || $elem_meiboOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_21" >1+</label>
		</td>
		<td>				
		<input id="od_22" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_pos2" value="2+" <?php echo ($elem_meiboOd_pos2 == "+2" || $elem_meiboOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_22" >2+</label>
		</td>
		<td>				
		<input id="od_23" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_pos3" value="3+" <?php echo ($elem_meiboOd_pos3 == "+3" || $elem_meiboOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_23" >3+</label>
		</td>
		<td>				
		<input id="od_24" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_pos4" value="4+" <?php echo ($elem_meiboOd_pos4 == "+4" || $elem_meiboOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_24" >4+</label>
		</td>
		<td>				
		<input id="od_26" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_rul" value="RUL" <?php echo ($elem_meiboOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_26" >RUL</label>
		</td>
		<td>				
		<input id="od_27" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOd_rll" value="RLL" <?php echo ($elem_meiboOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_27" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Meibo')">BL</td>
		<td align="left">Meibomitis</td>
		<td>
		<input id="os_19" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_neg" value="Absent" <?php echo ($elem_meiboOs_neg == "-ve" || $elem_meiboOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_19" >Absent</label>
		</td>
		<td>
		<input id="os_20" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_T" value="T" <?php echo ($elem_meiboOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_20" >T</label>
		</td>
		<td>			
		<input id="os_21" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_pos1" value="1+" <?php echo ($elem_meiboOs_pos1 == "+1" || $elem_meiboOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_21" >1+</label>
		</td>
		<td>				
		<input id="os_22" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_pos2" value="2+" <?php echo ($elem_meiboOs_pos2 == "+2" || $elem_meiboOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_22" >2+</label>
		</td>
		<td>				
		<input id="os_23" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_pos3" value="3+" <?php echo ($elem_meiboOs_pos3 == "+3" || $elem_meiboOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_23" >3+</label>
		</td>
		<td>				
		<input id="os_24" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_pos4" value="4+" <?php echo ($elem_meiboOs_pos4 == "+4" || $elem_meiboOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_24" >4+</label>
		</td>
		<td>				
		<input id="os_26" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_rul" value="LUL" <?php echo ($elem_meiboOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_26" >LUL</label>
		</td>
		<td>				
		<input id="os_27" type="checkbox"  onclick="checkAbsent(this)" name="elem_meiboOs_rll" value="LLL" <?php echo ($elem_meiboOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_27" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Meibomitis"])){ echo $arr_exm_ext_htm["Lids"]["Meibomitis"]; }  ?>
		
		<tr id="d_AcRo">
		<td align="left">Acne Rosacea</td>
		<td>
		<input id="od_28" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_neg" value="Absent" <?php echo ($elem_arOd_neg == "-ve" || $elem_arOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_28" >Absent</label>
		</td>
		<td>
		<input id="od_29" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_T" value="T" <?php echo ($elem_arOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_29" >T</label>
		</td>
		<td>				
		<input id="od_30" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_pos1" value="1+" <?php echo ($elem_arOd_pos1 == "+1" || $elem_arOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_30" >1+</label>
		</td>
		<td>				
		<input id="od_31" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_pos2" value="2+" <?php echo ($elem_arOd_pos2 == "+2" || $elem_arOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_31" >2+</label>
		</td>
		<td>				
		<input id="od_32" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_pos3" value="3+" <?php echo ($elem_arOd_pos3 == "+3" || $elem_arOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_32" >3+</label>
		</td>
		<td>				
		<input id="od_33" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_pos4" value="4+" <?php echo ($elem_arOd_pos4 == "+4" || $elem_arOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_33" >4+</label>
		</td>
		<td>				
		<input id="od_35" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_rul" value="RUL" <?php echo ($elem_arOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_35" >RUL</label>
		</td>
		<td>				
		<input id="od_36" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOd_rll" value="RLL" <?php echo ($elem_arOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_36" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('AcRo')">BL</td>
		<td align="left">Acne Rosacea</td>
		<td>
		<input id="os_28" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_neg" value="Absent" <?php echo ($elem_arOs_neg == "-ve" || $elem_arOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_28" >Absent</label>
		</td>
		<td>
		<input id="os_29" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_T" value="T" <?php echo ($elem_arOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_29" >T</label>
		</td>
		<td>				
		<input id="os_30" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_pos1" value="1+" <?php echo ($elem_arOs_pos1 == "+1" || $elem_arOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_30" >1+</label>
		</td>
		<td>				
		<input id="os_31" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_pos2" value="2+" <?php echo ($elem_arOs_pos2 == "+2" || $elem_arOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_31" >2+</label>
		</td>
		<td>				
		<input id="os_32" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_pos3" value="3+" <?php echo ($elem_arOs_pos3 == "+3" || $elem_arOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_32" >3+</label>
		</td>
		<td>				
		<input id="os_33" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_pos4" value="4+" <?php echo ($elem_arOs_pos4 == "+4" || $elem_arOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_33" >4+</label>
		</td>
		<td>				
		<input id="os_35" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_rul" value="LUL" <?php echo ($elem_arOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_35" >LUL</label>
		</td>
		<td>				
		<input id="os_36" type="checkbox"  onclick="checkAbsent(this)" name="elem_arOs_rll" value="LLL" <?php echo ($elem_arOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_36" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Acne Rosacea"])){ echo $arr_exm_ext_htm["Lids"]["Acne Rosacea"]; }  ?>
		
		<tr id="d_Tric">
		<td align="left">Trichiasis</td>
		<td>
		<input id="od_37" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_neg" value="Absent" <?php echo ($elem_trichiasisOd_neg == "-ve" || $elem_trichiasisOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_37" >Absent</label>
		</td>
		<td>
		<input id="od_38" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_T" value="T" <?php echo ($elem_trichiasisOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_38" >T</label>
		</td>
		<td>				
		<input id="od_39" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_pos1" value="1+" <?php echo ($elem_trichiasisOd_pos1 == "+1" || $elem_trichiasisOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_39" >1+</label>
		</td>
		<td>				
		<input id="od_40" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_pos2" value="2+" <?php echo ($elem_trichiasisOd_pos2 == "+2" || $elem_trichiasisOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_40" >2+</label>
		</td>
		<td>				
		<input id="od_41" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_pos3" value="3+" <?php echo ($elem_trichiasisOd_pos3 == "+3" || $elem_trichiasisOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_41" >3+</label>
		</td>
		<td>				
		<input id="od_42" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_pos4" value="4+" <?php echo ($elem_trichiasisOd_pos4 == "+4" || $elem_trichiasisOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_42" >4+</label>
		</td>
		<td>				
		<input id="od_44" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_rul" value="RUL" <?php echo ($elem_trichiasisOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_44" >RUL</label>
		</td>
		<td>				
		<input id="od_45" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOd_rll" value="RLL" <?php echo ($elem_trichiasisOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_45" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Tric')">BL</td>
		<td align="left">Trichiasis</td>
		<td>
		<input id="os_37" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_neg" value="Absent" <?php echo ($elem_trichiasisOs_neg == "-ve" || $elem_trichiasisOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_37" >Absent</label>
		</td>
		<td>
		<input id="os_38" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_T" value="T" <?php echo ($elem_trichiasisOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_38" >T</label>
		</td>
		<td>				
		<input id="os_39" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_pos1" value="1+" <?php echo ($elem_trichiasisOs_pos1 == "+1" || $elem_trichiasisOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_39" >1+</label>
		</td>
		<td>				
		<input id="os_40" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_pos2" value="2+" <?php echo ($elem_trichiasisOs_pos2 == "+2" || $elem_trichiasisOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_40" >2+</label>
		</td>
		<td>				
		<input id="os_41" type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_pos3" value="3+" <?php echo ($elem_trichiasisOs_pos3 == "+3" || $elem_trichiasisOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_41" >3+</label>
		</td>
		<td>				
		<input id="os_42"  type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_pos4" value="4+" <?php echo ($elem_trichiasisOs_pos4 == "+4" || $elem_trichiasisOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_42" >4+</label>
		</td>
		<td>				
		<input id="os_44"  type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_rul" value="LUL" <?php echo ($elem_trichiasisOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_44" >LUL</label>
		</td>
		<td>				
		<input id="os_45"  type="checkbox"  onclick="checkAbsent(this)" name="elem_trichiasisOs_rll" value="LLL" <?php echo ($elem_trichiasisOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_45" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Trichiasis"])){ echo $arr_exm_ext_htm["Lids"]["Trichiasis"]; }  ?>	
		
		<tr class="exmhlgcol grp_Trauma sbGrpOpen" id="d_Trauma">
		<td  align="left" >Trauma</td>
		<td colspan="8" align="left"><textarea  onBlur="checkwnls();" id="od_153" name="elem_traumaOd_text" class="form-control"><?php echo ($elem_traumaOd_text);?></textarea></td>
		<td align="center" class="bilat" onClick="check_bl('Trauma')">BL</td>
		<td align="left">Trauma</td>
		<td colspan="8" align="left"><textarea  onBlur="checkwnls();" id="os_153" name="elem_traumaOs_text" class="form-control"><?php echo ($elem_traumaOs_text);?></textarea></td>		
		</tr>
		
		<tr class="exmhlgcol grp_Trauma sbGrpOpen" id="d_Ecchymosis">
		<td align="left">Ecchymosis</td>
		<td>
		<input  id="od_154" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_neg" value="Absent" <?php echo ($elem_ecchyOd_neg == "-ve" || $elem_ecchyOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_154" >Absent</label>
		</td>
		<td>
		<input id="od_155" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_T" value="T" <?php echo ($elem_ecchyOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_155" >T</label>
		</td>
		<td>
		<input id="od_156" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_pos1" value="1+" <?php echo ($elem_ecchyOd_pos1 == "+1" || $elem_ecchyOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_156" >1+</label>
		</td>
		<td>						
		<input id="od_157" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_pos2" value="2+" <?php echo ($elem_ecchyOd_pos2 == "+2" || $elem_ecchyOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_157" >2+</label>
		</td>
		<td>					    
		<input  id="od_158" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_pos3" value="3+" <?php echo ($elem_ecchyOd_pos3 == "+3" || $elem_ecchyOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_158" >3+</label>
		</td>
		<td>					    
		<input id="od_159" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_pos4" value="4+" <?php echo ($elem_ecchyOd_pos4 == "+4" || $elem_ecchyOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_159" >4+</label>
		</td>
		<td>					    
		<input id="od_161" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_rul" value="RUL" <?php echo ($elem_ecchyOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_161" >RUL</label>
		</td>
		<td>					    
		<input id="od_162" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOd_rll" value="RLL" <?php echo ($elem_ecchyOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_162" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Trauma')">BL</td>
		<td align="left">Ecchymosis</td>
		<td>
		<input id="os_154" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_neg" value="Absent" <?php echo ($elem_ecchyOs_neg == "-ve" || $elem_ecchyOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_154" >Absent</label>
		</td>
		<td>
		<input id="os_155" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_T" value="T" <?php echo ($elem_ecchyOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_155" >T</label>
		</td>
		<td>					    
		<input id="os_156" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_pos1" value="1+" <?php echo ($elem_ecchyOs_pos1 == "+1" || $elem_ecchyOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_156" >1+</label>
		</td>
		<td>					    
		<input id="os_157" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_pos2" value="2+" <?php echo ($elem_ecchyOs_pos2 == "+2" || $elem_ecchyOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_157" >2+</label>
		</td>
		<td>					    
		<input id="os_158" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_pos3" value="3+" <?php echo ($elem_ecchyOs_pos3 == "+3" || $elem_ecchyOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_158" >3+</label>
		</td>
		<td>					    
		<input id="os_159" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_pos4" value="4+" <?php echo ($elem_ecchyOs_pos4 == "+4" || $elem_ecchyOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_159" >4+</label>
		</td>
		<td>					    
		<input id="os_161" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_rul" value="LUL" <?php echo ($elem_ecchyOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_161" >LUL</label>
		</td>
		<td>					    
		<input id="os_162" type="checkbox"  onclick="checkAbsent(this)" name="elem_ecchyOs_rll" value="LLL" <?php echo ($elem_ecchyOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_162" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Trauma/Ecchymosis"])){ echo $arr_exm_ext_htm["Lids"]["Trauma/Ecchymosis"]; }  ?>
		 
		<tr class="exmhlgcol grp_Trauma sbGrpOpen" id="d_trauma_Edema">
		<td align="left">Edema</td>
		<td>
		<input id="od_172" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_neg" value="Absent" <?php echo ($elem_edemaOd_neg == "-ve" || $elem_edemaOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_172" >Absent</label>
		</td>
		<td>
		<input id="od_173" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_T" value="T" <?php echo ($elem_edemaOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_173" >T</label>
		</td>
		<td>					    
		<input id="od_174" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_pos1" value="1+" <?php echo ($elem_edemaOd_pos1 == "+1" || $elem_edemaOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_174" >1+</label>
		</td>
		<td>					    
		<input id="od_175" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_pos2" value="2+" <?php echo ($elem_edemaOd_pos2 == "+2" || $elem_edemaOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_175" >2+</label>
		</td>
		<td>					   
		<input id="od_176" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_pos3" value="3+" <?php echo ($elem_edemaOd_pos3 == "+3" || $elem_edemaOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_176" >3+</label>
		</td>
		<td>					    
		<input id="od_177" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_pos4" value="4+" <?php echo ($elem_edemaOd_pos4 == "+4" || $elem_edemaOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_177" >4+</label>
		</td>
		<td>					    
		<input id="od_179" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_rul" value="RUL" <?php echo ($elem_edemaOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_179" >RUL</label>
		</td>
		<td>					    
		<input id="od_180" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOd_rll" value="RLL" <?php echo ($elem_edemaOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_180" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Trauma')">BL</td>
		<td align="left">Edema</td>
		<td>
		<input id="os_172" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_neg" value="Absent" <?php echo ($elem_edemaOs_neg == "-ve" || $elem_edemaOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_172" >Absent</label>
		</td>
		<td>
		<input id="os_173" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_T" value="T" <?php echo ($elem_edemaOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_173" >T</label>
		</td>
		<td>					    
		<input id="os_174" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_pos1" value="1+" <?php echo ($elem_edemaOs_pos1 == "+1" || $elem_edemaOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_174" >1+</label>
		</td>
		<td>					    
		<input id="os_175" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_pos2" value="2+" <?php echo ($elem_edemaOs_pos2 == "+2" || $elem_edemaOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_175" >2+</label>
		</td>
		<td>					    
		<input id="os_176" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_pos3" value="3+" <?php echo ($elem_edemaOs_pos3 == "+3" || $elem_edemaOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_176" >3+</label>
		</td>
		<td>					    
		<input id="os_177" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_pos4" value="4+" <?php echo ($elem_edemaOs_pos4 == "+4" || $elem_edemaOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_177" >4+</label>
		</td>
		<td>					    
		<input id="os_179" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_rul" value="LUL" <?php echo ($elem_edemaOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_179" >LUL</label>
		</td>
		<td>					    
		<input id="os_180" type="checkbox"  onclick="checkAbsent(this)" name="elem_edemaOs_rll" value="LLL" <?php echo ($elem_edemaOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_180" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Trauma/Edema"])){ echo $arr_exm_ext_htm["Lids"]["Trauma/Edema"]; }  ?>
		
		<tr class="exmhlgcol grp_Trauma sbGrpOpen" id="d_trauma_Lacerations">
		<td align="left">Lacerations</td>
		<td class="lacerations_txt form-inline" colspan="6">
		<div class="form-group">
		<input name="elem_lacerationsOd_Lac" id="elem_lacerationsOd_Lac" value="<?php echo $elem_lacerationsOd_Lac; ?>" type="text" size="30" class="form-control" ><label for="elem_lacerationsOd_Lac">mm</label>
		</div>
		</td>
		<td>
		<input id="od_170" type="checkbox"  onclick="checkwnls()" name="elem_lacerationsOd_rul" value="RUL" <?php echo ($elem_lacerationsOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_170"   class="lac_rul">RUL</label>
		</td>
		<td>					    
		<input id="od_171" type="checkbox"  onclick="checkwnls()" name="elem_lacerationsOd_rll" value="RLL" <?php echo ($elem_lacerationsOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_171" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Trauma')">BL</td>
		<td align="left">Lacerations</td>
		<td colspan="6" align="left" class="form-inline">
		<div class="form-group">
		<input name="elem_lacerationsOs_Lac" value="<?php echo $elem_lacerationsOs_Lac; ?>" type="text" size="30" class="form-control" placeholder="Text input" ><label class="lac_mm">mm</label>
		</div>
		</td>
		<td>
		<input id="os_170" type="checkbox"  onclick="checkwnls()" name="elem_lacerationsOs_rul" value="LUL" <?php echo ($elem_lacerationsOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_170"  class="lac_rul">LUL</label>
		</td>
		<td>					    
		<input id="os_171" type="checkbox"  onclick="checkwnls()" name="elem_lacerationsOs_rll" value="LLL" <?php echo ($elem_lacerationsOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_171" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Trauma/Lacerations"])){ echo $arr_exm_ext_htm["Lids"]["Trauma/Lacerations"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Trauma"])){ echo $arr_exm_ext_htm["Lids"]["Trauma"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lids"]["Main"])){ echo $arr_exm_ext_htm["Lids"]["Main"]; }  ?>
		
		<tr id="d_adOpt_lids">
		<td align="left"><strong>Comments</strong></td>
		<td colspan="8" align="left" id="d_adOpt_lids_od"><textarea onBlur="checkwnls();" id="od_46" name="lidsAdOptionsOd"  rows="3" class="form-control"><?php echo ($lidsAdOptionsOd);?></textarea></td>
		<td align="center" class="bilat" onClick="check_bl('adOpt_lids')">BL</td>
		<td align="left"><strong>Comments</strong></td>
		<td colspan="8" align="left" id="d_adOpt_lids_os"><textarea onBlur="checkwnls();" id="os_46" name="lidsAdOptionsOs" rows="3" class="form-control"><?php echo ($lidsAdOptionsOs);?></textarea>   </td>
		</tr>
		
		</table>
	</div>
	<div class="clearfix"> </div>
	<!-- LA -->
	<!-- Advance -->
	<?php if($flg_showPlastic=="1"){ //Check Template 
		include("lids_advance_inc.php");
	}//End Template check  ?>
	<!-- Advance -->
   
    </div>
    <div role="tabpanel" class="tab-pane <?php if(2 == $defTabKey){echo "active";} ?>" id="div2">
	<div class="examhd">
	<?php if($finalize_flag == 1){?>
		<label class="chart_status label label-danger pull-left">Finalized</label>
	<?php }?>

	<span id="examFlag" class="glyphicon flagWnl "></span>
		
	<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

	<input type="checkbox" id="elem_noChangeLesion"  name="elem_noChangeLesion" value="1" onClick="setNC2();" 
		<?php echo ($elem_ncLesion == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
	<label class="lbl_nochange frcb" for="elem_noChangeLesion">NO Change</label>

	<?php /*if (constant('AV_MODULE')=='YES'){?>
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
	<?php }*/ ?>
	</div>    
	<div class="clearfix"> </div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped" >
		<tr>
		<td colspan="10" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		</td>
		<td width="100" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
		<td colspan="10" align="center" width="48%">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>
		
		<tr id="d_Chal">
		<td align="left">Chalazion</td>
		<td>
		<input id="od_47" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_neg" value="Absent" <?php echo ($elem_chalazionOd_neg == "-ve" || $elem_chalazionOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_47" >Absent</label>
		</td>
		<td>
		<input id="od_48" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_T" value="T" <?php echo ($elem_chalazionOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_48" >T</label>
		</td>
		<td>				
		<input id="od_49" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_pos1" value="1+" <?php echo ($elem_chalazionOd_pos1 == "+1" || $elem_chalazionOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_49" >1+</label>
		</td>
		<td>				
		<input id="od_50" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_pos2" value="2+" <?php echo ($elem_chalazionOd_pos2 == "+2" || $elem_chalazionOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_50" >2+</label>
		</td>
		<td>				
		<input id="od_51" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_pos3" value="3+" <?php echo ($elem_chalazionOd_pos3 == "+3" || $elem_chalazionOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_51" >3+</label>
		</td>
		<td>				
		<input id="od_52" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_pos4" value="4+" <?php echo ($elem_chalazionOd_pos4 == "+4" || $elem_chalazionOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_52" >4+</label>
		</td>
		<td>				
		<input id="od_54" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_rul" value="RUL" <?php echo ($elem_chalazionOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_54" >RUL</label>
		</td>
		<td>				
		<input id="od_55" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOd_rll" value="RLL" <?php echo ($elem_chalazionOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_55" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Chalazion_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Chal')">BL</td>
		<td align="left">Chalazion</td>
		<td>
		<input id="os_47" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_neg" value="Absent" <?php echo ($elem_chalazionOs_neg == "-ve" || $elem_chalazionOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_47" >Absent</label>
		</td>
		<td>
		<input id="os_48" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_T" value="T" <?php echo ($elem_chalazionOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_48" >T</label>
		</td>
		<td>				
		<input id="os_49" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_pos1" value="1+" <?php echo ($elem_chalazionOs_pos1 == "+1" || $elem_chalazionOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_49" >1+</label>
		</td>
		<td>				
		<input id="os_50" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_pos2" value="2+" <?php echo ($elem_chalazionOs_pos2 == "+2" || $elem_chalazionOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_50" >2+</label>
		</td>
		<td>				
		<input id="os_51" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_pos3" value="3+" <?php echo ($elem_chalazionOs_pos3 == "+3" || $elem_chalazionOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_51" >3+</label>
		</td>
		<td>				
		<input id="os_52" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_pos4" value="4+" <?php echo ($elem_chalazionOs_pos4 == "+4" || $elem_chalazionOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_52" >4+</label>
		</td>
		<td>				
		<input id="os_54" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_rul" value="LUL" <?php echo ($elem_chalazionOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_54" >LUL</label>
		</td>
		<td>				
		<input id="os_55" type="checkbox"  onclick="checkAbsent(this)" name="elem_chalazionOs_rll" value="LLL" <?php echo ($elem_chalazionOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_55" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Chalazion_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Chalazion"])){ echo $arr_exm_ext_htm["Lesion"]["Chalazion"]; }  ?>
		
		<tr id="d_Hord">
		<td align="left">Hordeolum</td>
		<td>
		<input id="od_56" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_neg" value="Absent" <?php echo ($elem_hordeolumOd_neg == "-ve" || $elem_hordeolumOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_56" >Absent</label>
		</td>
		<td>
		<input id="od_57" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_T" value="T" <?php echo ($elem_hordeolumOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_57" >T</label>
		</td>
		<td>				
		<input id="od_58" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_pos1" value="1+" <?php echo ($elem_hordeolumOd_pos1 == "+1" || $elem_hordeolumOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_58" >1+</label>
		</td>
		<td>				
		<input id="od_59" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_pos2" value="2+" <?php echo ($elem_hordeolumOd_pos2 == "+2" || $elem_hordeolumOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_59" >2+</label>
		</td>
		<td>				
		<input id="od_60" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_pos3" value="3+" <?php echo ($elem_hordeolumOd_pos3 == "+3" || $elem_hordeolumOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_60" >3+</label>
		</td>
		<td>				
		<input id="od_61" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_pos4" value="4+" <?php echo ($elem_hordeolumOd_pos4 == "+4" || $elem_hordeolumOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_61" >4+</label>
		</td>
		<td>				
		<input id="od_63" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_rul" value="RUL" <?php echo ($elem_hordeolumOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_63" >RUL</label>
		</td>
		<td>				
		<input id="od_64" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOd_rll" value="RLL" <?php echo ($elem_hordeolumOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_64" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Hordeolum_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Hord')">BL</td>
		<td align="left">Hordeolum</td>
		<td>
		<input id="os_56" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_neg" value="Absent" <?php echo ($elem_hordeolumOs_neg == "-ve" || $elem_hordeolumOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_56" >Absent</label>
		</td>
		<td>
		<input id="os_57" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_T" value="T" <?php echo ($elem_hordeolumOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_57" >T</label>
		</td>
		<td>				
		<input id="os_58" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_pos1" value="1+" <?php echo ($elem_hordeolumOs_pos1 == "+1" || $elem_hordeolumOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_58" >1+</label>
		</td>
		<td>				
		<input id="os_59" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_pos2" value="2+" <?php echo ($elem_hordeolumOs_pos2 == "+2" || $elem_hordeolumOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_59" >2+</label>
		</td>
		<td>				
		<input id="os_60" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_pos3" value="3+" <?php echo ($elem_hordeolumOs_pos3 == "+3" || $elem_hordeolumOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_60" >3+</label>
		</td>
		<td>				
		<input id="os_61" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_pos4" value="4+" <?php echo ($elem_hordeolumOs_pos4 == "+4" || $elem_hordeolumOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_61" >4+</label>
		</td>
		<td>				
		<input id="os_63" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_rul" value="LUL" <?php echo ($elem_hordeolumOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_63" >LUL</label>
		</td>
		<td>				
		<input id="os_64" type="checkbox"  onclick="checkAbsent(this)" name="elem_hordeolumOs_rll" value="LLL" <?php echo ($elem_hordeolumOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_64" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Hordeolum_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Hordeolum"])){ echo $arr_exm_ext_htm["Lesion"]["Hordeolum"]; }  ?>
		
		
		<tr id="d_Papi">
		<td align="left">Papilloma</td>
		<td>
		<input id="od_65"  type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_neg" value="Absent" <?php echo ($elem_papillomaOd_neg == "-ve" || $elem_papillomaOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_65" >Absent</label>
		</td>
		<td>
		<input id="od_66" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_T" value="T" <?php echo ($elem_papillomaOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_66" >T</label>
		</td>
		<td>				
		<input id="od_67" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_pos1" value="1+" <?php echo ($elem_papillomaOd_pos1 == "+1" || $elem_papillomaOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_67" >1+</label>
		</td>
		<td>				
		<input id="od_68" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_pos2" value="2+" <?php echo ($elem_papillomaOd_pos2 == "+2" || $elem_papillomaOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_68" >2+</label>
		</td>
		<td>				
		<input id="od_69" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_pos3" value="3+" <?php echo ($elem_papillomaOd_pos3 == "+3" || $elem_papillomaOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_69" >3+</label>
		</td>
		<td>				
		<input id="od_70" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_pos4" value="4+" <?php echo ($elem_papillomaOd_pos4 == "+4" || $elem_papillomaOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_70" >4+</label>
		</td>
		<td>				
		<input id="od_72" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_rul" value="RUL" <?php echo ($elem_papillomaOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_72" >RUL</label>
		</td>
		<td>				
		<input id="od_73" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOd_rll" value="RLL" <?php echo ($elem_papillomaOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_73" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Papilloma_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Papi')">BL</td>
		<td align="left">Papilloma</td>
		<td>
		<input id="os_65" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_neg" value="Absent" <?php echo ($elem_papillomaOs_neg == "-ve" || $elem_papillomaOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_65" >Absent</label>
		</td>
		<td>
		<input id="os_66" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_T" value="T" <?php echo ($elem_papillomaOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_66" >T</label>
		</td>
		<td>				
		<input id="os_67" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_pos1" value="1+" <?php echo ($elem_papillomaOs_pos1 == "+1" || $elem_papillomaOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_67" >1+</label>
		</td>
		<td>				
		<input id="os_68" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_pos2" value="2+" <?php echo ($elem_papillomaOs_pos2 == "+2" || $elem_papillomaOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_68" >2+</label>
		</td>
		<td>				
		<input id="os_69" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_pos3" value="3+" <?php echo ($elem_papillomaOs_pos3 == "+3" || $elem_papillomaOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_69" >3+</label>
		</td>
		<td>				
		<input id="os_70" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_pos4" value="4+" <?php echo ($elem_papillomaOs_pos4 == "+4" || $elem_papillomaOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_70" >4+</label>
		</td>
		<td>				
		<input id="os_72" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_rul" value="LUL" <?php echo ($elem_papillomaOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_72" >LUL</label>
		</td>
		<td>				
		<input id="os_73" type="checkbox"  onclick="checkAbsent(this)" name="elem_papillomaOs_rll" value="LLL" <?php echo ($elem_papillomaOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_73" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Papilloma_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Papilloma"])){ echo $arr_exm_ext_htm["Lesion"]["Papilloma"]; }  ?>
		
		<tr class="exmhlgcol grp_Cyst sbGrpOpen" id="d_Cyst">
		<td align="left">Cyst</td>
		<td colspan="8">
			<textarea id="od_74" onBlur="checkwnls();" name="elem_cystOd_text" class="form-control"><?php echo ($elem_cystOd_text);?></textarea>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Cyst_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Cyst')" >BL</td>
		<td align="left">Cyst</td>
		<td colspan="8">
			<textarea id="os_74" onBlur="checkwnls();" name="elem_cystOs_text" class="form-control"><?php echo ($elem_cystOs_text);?></textarea>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Cyst_os;
				} 
			?>
		</td>
		</tr>
		
		
		<tr class="exmhlgcol grp_Cyst sbGrpOpen" id="d_Cyst_Inclusion">
		<td align="left">Inclusion</td>
		<td>
		<input id="od_75" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_neg" value="Absent" <?php echo ($elem_inclusionOd_neg == "-ve" || $elem_inclusionOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_75" >Absent</label>
		</td>
		<td>
		<input id="od_76" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_T" value="T" <?php echo ($elem_inclusionOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_76" >T</label>
		</td>
		<td>					    
		<input id="od_77" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_pos1" value="1+" <?php echo ($elem_inclusionOd_pos1 == "+1" || $elem_inclusionOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_77" >1+</label>
		</td>
		<td>					    
		<input id="od_78" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_pos2" value="2+" <?php echo ($elem_inclusionOd_pos2 == "+2" || $elem_inclusionOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_78" >2+</label>
		</td>
		<td>					    
		<input id="od_79" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_pos3" value="3+" <?php echo ($elem_inclusionOd_pos3 == "+3" || $elem_inclusionOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_79" >3+</label>
		</td>
		<td>					    
		<input id="od_80" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_pos4" value="4+" <?php echo ($elem_inclusionOd_pos4 == "+4" || $elem_inclusionOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_80" >4+</label>
		</td>
		<td>					    
		<input id="od_82" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_rul" value="RUL" <?php echo ($elem_inclusionOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_82" >RUL</label>
		</td>
		<td>					    
		<input id="od_83" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOd_rll" value="RLL" <?php echo ($elem_inclusionOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_83" >RLL</label>
		</td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('Cyst')" >BL</td>
		<td align="left">Inclusion</td>
		<td>
		<input id="os_75" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_neg" value="Absent" <?php echo ($elem_inclusionOs_neg == "-ve" || $elem_inclusionOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_75" >Absent</label>
		</td>
		<td>
		<input id="os_76" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_T" value="T" <?php echo ($elem_inclusionOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_76" >T</label>
		</td>
		<td>					    
		<input id="os_77" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_pos1" value="1+" <?php echo ($elem_inclusionOs_pos1 == "+1" || $elem_inclusionOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_77" >1+</label>
		</td>
		<td>					    
		<input id="os_78" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_pos2" value="2+" <?php echo ($elem_inclusionOs_pos2 == "+2" || $elem_inclusionOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_78" >2+</label>
		</td>
		<td>					    
		<input id="os_79" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_pos3" value="3+" <?php echo ($elem_inclusionOs_pos3 == "+3" || $elem_inclusionOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_79" >3+</label>
		</td>
		<td>					    
		<input id="os_80" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_pos4" value="4+" <?php echo ($elem_inclusionOs_pos4 == "+4" || $elem_inclusionOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_80" >4+</label>
		</td>
		<td>					    
		<input id="os_82" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_rul" value="LUL" <?php echo ($elem_inclusionOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_82" >LUL</label>
		</td>
		<td>					    
		<input id="os_83" type="checkbox"  onclick="checkAbsent(this)" name="elem_inclusionOs_rll" value="LLL" <?php echo ($elem_inclusionOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_83" >LLL</label>
		</td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Cyst/Inclusion"])){ echo $arr_exm_ext_htm["Lesion"]["Cyst/Inclusion"]; }  ?>
		
		<tr class="exmhlgcol grp_Cyst sbGrpOpen" id="d_Cyst_Sudoriferous">
		<td align="left">Sudoriferous</td>
		<td>
		<input id="od_84" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_neg" value="Absent" <?php echo ($elem_psuedOd_neg == "-ve" || $elem_psuedOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_84" >Absent</label>
		</td>
		<td>
		<input id="od_85" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_T" value="T" <?php echo ($elem_psuedOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_85" >T</label>
		</td>
		<td>					    
		<input id="od_86" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_pos1" value="1+" <?php echo ($elem_psuedOd_pos1 == "+1" || $elem_psuedOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_86" >1+</label>
		</td>
		<td>					    
		<input id="od_87" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_pos2" value="2+" <?php echo ($elem_psuedOd_pos2 == "+2" || $elem_psuedOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_87" >2+</label>
		</td>
		<td>					    
		<input id="od_88" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_pos3" value="3+" <?php echo ($elem_psuedOd_pos3 == "+3" || $elem_psuedOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_88" >3+</label>
		</td>
		<td>					    
		<input id="od_89" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_pos4" value="4+" <?php echo ($elem_psuedOd_pos4 == "+4" || $elem_psuedOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_89" >4+</label>
		</td>
		<td>					    
		<input id="od_91" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_rul" value="RUL" <?php echo ($elem_psuedOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_91" >RUL</label>
		</td>
		<td>					    
		<input id="od_92" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOd_rll" value="RLL" <?php echo ($elem_psuedOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_92" >RLL</label>
		</td>
		<td></td>
		<td align="center" class="bilat" onClick="check_bl('Cyst')" >BL</td>
		<td align="left">Sudoriferous</td>
		<td>
		<input id="os_84" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_neg" value="Absent" <?php echo ($elem_psuedOs_neg == "-ve" || $elem_psuedOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_84" >Absent</label>
		</td>
		<td>
		<input id="os_85" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_T" value="T" <?php echo ($elem_psuedOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_85" >T</label>
		</td>
		<td>					    
		<input id="os_86" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_pos1" value="1+" <?php echo ($elem_psuedOs_pos1 == "+1" || $elem_psuedOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_86" >1+</label>
		</td>
		<td>					    
		<input id="os_87" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_pos2" value="2+" <?php echo ($elem_psuedOs_pos2 == "+2" || $elem_psuedOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_87" >2+</label>
		</td>
		<td>					    
		<input id="os_88" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_pos3" value="3+" <?php echo ($elem_psuedOs_pos3 == "+3" || $elem_psuedOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_88" >3+</label>
		</td>
		<td>					    
		<input id="os_89" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_pos4" value="4+" <?php echo ($elem_psuedOs_pos4 == "+4" || $elem_psuedOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_89" >4+</label>
		</td>
		<td>					    
		<input id="os_91" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_rul" value="LUL" <?php echo ($elem_psuedOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_91" >LUL</label>
		</td>
		<td>					    
		<input id="os_92" type="checkbox"  onclick="checkAbsent(this)" name="elem_psuedOs_rll" value="LLL" <?php echo ($elem_psuedOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_92" >LLL</label>
		</td>
		<td></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Cyst/Sudoriferous"])){ echo $arr_exm_ext_htm["Lesion"]["Cyst/Sudoriferous"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Cyst"])){ echo $arr_exm_ext_htm["Lesion"]["Cyst"]; }  ?>
		
		<tr id="d_Neoplasia">
		<td align="left">Neoplasia</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkwnls()" id="elem_neoplasOd_bcell" name="elem_neoplasOd_bcell" value="Basal Cell" 
		<?php echo ($elem_neoplasOd_bcell == "Basal Cell") ? "checked=\"checked\"" : "";?>><label for="elem_neoplasOd_bcell"  class="neo_basal">Basal Cell</label>
		</td>
		<td colspan="2">	
		<input type="checkbox"  onclick="checkwnls()" id="elem_neoplasOd_sqcell" name="elem_neoplasOd_sqcell" value="Squamous Cell" 
		<?php echo ($elem_neoplasOd_sqcell == "Squamous Cell") ? "checked=\"checked\"" : "";?>><label for="elem_neoplasOd_sqcell" >Squamous Cell</label>
		</td>
		<td class="neo_other form-inline" colspan="4">
		<div class="form-group">
		<label for="elem_neoplasOd_other">Other </label>
		<input type="text" onBlur="checkwnls();" size="18" name="elem_neoplasOd_other" id="elem_neoplasOd_other" value="<?php echo ($elem_neoplasOd_other);?>" class="form-control">
		</div>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Neoplasia_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Neoplasia')">BL</td>
		<td align="left">Neoplasia</td>
		<td colspan="2">
		<input type="checkbox"  onclick="checkwnls()" id="elem_neoplasOs_bcell" name="elem_neoplasOs_bcell" value="Basal Cell" 
		<?php echo ($elem_neoplasOs_bcell == "Basal Cell") ? "checked=\"checked\"" : "";?>><label for="elem_neoplasOs_bcell"  class="neo_basal">Basal Cell</label>
		</td>
		<td colspan="2">	
		<input type="checkbox"  onclick="checkwnls()" id="elem_neoplasOs_sqcell" name="elem_neoplasOs_sqcell" value="Squamous Cell" 
		<?php echo ($elem_neoplasOs_sqcell == "Squamous Cell") ? "checked=\"checked\"" : "";?>><label for="elem_neoplasOs_sqcell" >Squamous Cell</label>
		</td>
		<td class="neo_other form-inline" colspan="4">
		<div class="form-group">
		<label for="elem_neoplasOs_other">Other </label>
		<input type="text" onBlur="checkwnls();" size="18" name="elem_neoplasOs_other" id="elem_neoplasOs_other"
		value="<?php echo ($elem_neoplasOs_other);?>" class="form-control">
		</div>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Neoplasia_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Neoplasia"])){ echo $arr_exm_ext_htm["Lesion"]["Neoplasia"]; }  ?>
		
		<tr id="d_Heman">
		<td align="left">Hemangioma</td>
		<td>
		<input   type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_neg" name="elem_HemanOd_neg" value="Absent" <?php echo ($elem_HemanOd_neg == "-ve" || $elem_HemanOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_T" name="elem_HemanOd_T" value="T" <?php echo ($elem_HemanOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_pos1" name="elem_HemanOd_pos1" value="1+" <?php echo ($elem_HemanOd_pos1 == "+1" || $elem_HemanOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_pos2" name="elem_HemanOd_pos2" value="2+" <?php echo ($elem_HemanOd_pos2 == "+2" || $elem_HemanOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_pos3" name="elem_HemanOd_pos3" value="3+" <?php echo ($elem_HemanOd_pos3 == "+3" || $elem_HemanOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_pos4" name="elem_HemanOd_pos4" value="4+" <?php echo ($elem_HemanOd_pos4 == "+4" || $elem_HemanOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_rul" name="elem_HemanOd_rul" value="RUL" <?php echo ($elem_HemanOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_rul" >RUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOd_rll" name="elem_HemanOd_rll" value="RLL" <?php echo ($elem_HemanOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOd_rll" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Hemangioma_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Heman')">BL</td>
		<td align="left">Hemangioma</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_neg" name="elem_HemanOs_neg" value="Absent" <?php echo ($elem_HemanOs_neg == "-ve" || $elem_HemanOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_T" name="elem_HemanOs_T" value="T" <?php echo ($elem_HemanOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_pos1" name="elem_HemanOs_pos1" value="1+" <?php echo ($elem_HemanOs_pos1 == "+1" || $elem_HemanOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_pos2" name="elem_HemanOs_pos2" value="2+" <?php echo ($elem_HemanOs_pos2 == "+2" || $elem_HemanOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_pos3" name="elem_HemanOs_pos3" value="3+" <?php echo ($elem_HemanOs_pos3 == "+3" || $elem_HemanOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_pos4" name="elem_HemanOs_pos4" value="4+" <?php echo ($elem_HemanOs_pos4 == "+4" || $elem_HemanOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_rul" name="elem_HemanOs_rul" value="LUL" <?php echo ($elem_HemanOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_rul" >LUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HemanOs_rll" name="elem_HemanOs_rll" value="LLL" <?php echo ($elem_HemanOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_HemanOs_rll" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Hemangioma_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Hemangioma"])){ echo $arr_exm_ext_htm["Lesion"]["Hemangioma"]; }  ?>
		
		
		<tr id="d_SeboKera">
		<td align="left">Seborrheic Keratosis</td>
		<td>
		<input   type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_neg" name="elem_SeboKeraOd_neg" value="Absent" <?php echo ($elem_SeboKeraOd_neg == "-ve" || $elem_SeboKeraOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_T" name="elem_SeboKeraOd_T" value="T" <?php echo ($elem_SeboKeraOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_pos1" name="elem_SeboKeraOd_pos1" value="1+" <?php echo ($elem_SeboKeraOd_pos1 == "+1" || $elem_SeboKeraOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_pos2" name="elem_SeboKeraOd_pos2" value="2+" <?php echo ($elem_SeboKeraOd_pos2 == "+2" || $elem_SeboKeraOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_pos3" name="elem_SeboKeraOd_pos3" value="3+" <?php echo ($elem_SeboKeraOd_pos3 == "+3" || $elem_SeboKeraOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_pos4" name="elem_SeboKeraOd_pos4" value="4+" <?php echo ($elem_SeboKeraOd_pos4 == "+4" || $elem_SeboKeraOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_rul" name="elem_SeboKeraOd_rul" value="RUL" <?php echo ($elem_SeboKeraOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_rul" >RUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOd_rll" name="elem_SeboKeraOd_rll" value="RLL" <?php echo ($elem_SeboKeraOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOd_rll" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_SeboKera_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('SeboKera')">BL</td>
		<td align="left">Seborrheic Keratosis</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_neg" name="elem_SeboKeraOs_neg" value="Absent" <?php echo ($elem_SeboKeraOs_neg == "-ve" || $elem_SeboKeraOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_T" name="elem_SeboKeraOs_T" value="T" <?php echo ($elem_SeboKeraOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_pos1" name="elem_SeboKeraOs_pos1" value="1+" <?php echo ($elem_SeboKeraOs_pos1 == "+1" || $elem_SeboKeraOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_pos2" name="elem_SeboKeraOs_pos2" value="2+" <?php echo ($elem_SeboKeraOs_pos2 == "+2" || $elem_SeboKeraOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_pos3" name="elem_SeboKeraOs_pos3" value="3+" <?php echo ($elem_SeboKeraOs_pos3 == "+3" || $elem_SeboKeraOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_pos4" name="elem_SeboKeraOs_pos4" value="4+" <?php echo ($elem_SeboKeraOs_pos4 == "+4" || $elem_SeboKeraOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_rul" name="elem_SeboKeraOs_rul" value="LUL" <?php echo ($elem_SeboKeraOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_rul" >LUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_SeboKeraOs_rll" name="elem_SeboKeraOs_rll" value="LLL" <?php echo ($elem_SeboKeraOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_SeboKeraOs_rll" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_SeboKera_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Seborrheic Keratosis"])){ echo $arr_exm_ext_htm["Lesion"]["Seborrheic Keratosis"]; }  ?>
		
		<tr id="d_InNevus">
		<td align="left">Intradermal Nevus</td>
		<td>
		<input   type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_neg" name="elem_InNevusOd_neg" value="Absent" <?php echo ($elem_InNevusOd_neg == "-ve" || $elem_InNevusOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_T" name="elem_InNevusOd_T" value="T" <?php echo ($elem_InNevusOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_pos1" name="elem_InNevusOd_pos1" value="1+" <?php echo ($elem_InNevusOd_pos1 == "+1" || $elem_InNevusOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_pos2" name="elem_InNevusOd_pos2" value="2+" <?php echo ($elem_InNevusOd_pos2 == "+2" || $elem_InNevusOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_pos3" name="elem_InNevusOd_pos3" value="3+" <?php echo ($elem_InNevusOd_pos3 == "+3" || $elem_InNevusOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_pos4" name="elem_InNevusOd_pos4" value="4+" <?php echo ($elem_InNevusOd_pos4 == "+4" || $elem_InNevusOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_rul" name="elem_InNevusOd_rul" value="RUL" <?php echo ($elem_InNevusOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_rul" >RUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOd_rll" name="elem_InNevusOd_rll" value="RLL" <?php echo ($elem_InNevusOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOd_rll" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_InNevus_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('InNevus')">BL</td>
		<td align="left">Intradermal Nevus</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_neg" name="elem_InNevusOs_neg" value="Absent" <?php echo ($elem_InNevusOs_neg == "-ve" || $elem_InNevusOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_T" name="elem_InNevusOs_T" value="T" <?php echo ($elem_InNevusOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_pos1" name="elem_InNevusOs_pos1" value="1+" <?php echo ($elem_InNevusOs_pos1 == "+1" || $elem_InNevusOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_pos2" name="elem_InNevusOs_pos2" value="2+" <?php echo ($elem_InNevusOs_pos2 == "+2" || $elem_InNevusOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_pos3" name="elem_InNevusOs_pos3" value="3+" <?php echo ($elem_InNevusOs_pos3 == "+3" || $elem_InNevusOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_pos4" name="elem_InNevusOs_pos4" value="4+" <?php echo ($elem_InNevusOs_pos4 == "+4" || $elem_InNevusOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_rul" name="elem_InNevusOs_rul" value="LUL" <?php echo ($elem_InNevusOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_rul" >LUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_InNevusOs_rll" name="elem_InNevusOs_rll" value="LLL" <?php echo ($elem_InNevusOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_InNevusOs_rll" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_InNevus_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Intradermal Nevus"])){ echo $arr_exm_ext_htm["Lesion"]["Intradermal Nevus"]; }  ?>
		
		<tr id="d_HydCys">
		<td align="left">Hidrocystoma</td>
		<td>
		<input   type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_neg" name="elem_HydCysOd_neg" value="Absent" <?php echo ($elem_HydCysOd_neg == "-ve" || $elem_HydCysOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_T" name="elem_HydCysOd_T" value="T" <?php echo ($elem_HydCysOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_pos1" name="elem_HydCysOd_pos1" value="1+" <?php echo ($elem_HydCysOd_pos1 == "+1" || $elem_HydCysOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_pos2" name="elem_HydCysOd_pos2" value="2+" <?php echo ($elem_HydCysOd_pos2 == "+2" || $elem_HydCysOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_pos3" name="elem_HydCysOd_pos3" value="3+" <?php echo ($elem_HydCysOd_pos3 == "+3" || $elem_HydCysOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_pos4" name="elem_HydCysOd_pos4" value="4+" <?php echo ($elem_HydCysOd_pos4 == "+4" || $elem_HydCysOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_rul" name="elem_HydCysOd_rul" value="RUL" <?php echo ($elem_HydCysOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_rul" >RUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOd_rll" name="elem_HydCysOd_rll" value="RLL" <?php echo ($elem_HydCysOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOd_rll" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Hydrocystoma_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('HydCys')">BL</td>
		<td align="left">Hidrocystoma</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_neg" name="elem_HydCysOs_neg" value="Absent" <?php echo ($elem_HydCysOs_neg == "-ve" || $elem_HydCysOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_T" name="elem_HydCysOs_T" value="T" <?php echo ($elem_HydCysOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_pos1" name="elem_HydCysOs_pos1" value="1+" <?php echo ($elem_HydCysOs_pos1 == "+1" || $elem_HydCysOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_pos2" name="elem_HydCysOs_pos2" value="2+" <?php echo ($elem_HydCysOs_pos2 == "+2" || $elem_HydCysOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_pos3" name="elem_HydCysOs_pos3" value="3+" <?php echo ($elem_HydCysOs_pos3 == "+3" || $elem_HydCysOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_pos4" name="elem_HydCysOs_pos4" value="4+" <?php echo ($elem_HydCysOs_pos4 == "+4" || $elem_HydCysOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_rul" name="elem_HydCysOs_rul" value="LUL" <?php echo ($elem_HydCysOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_rul" >LUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_HydCysOs_rll" name="elem_HydCysOs_rll" value="LLL" <?php echo ($elem_HydCysOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_HydCysOs_rll" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Hydrocystoma_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Hidrocystoma"])){ echo $arr_exm_ext_htm["Lesion"]["Hidrocystoma"]; }  ?>
		
		<tr id="d_Xantha">
		<td align="left">Xanthelasma</td>
		<td>
		<input   type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_neg" name="elem_XanthaOd_neg" value="Absent" <?php echo ($elem_XanthaOd_neg == "-ve" || $elem_XanthaOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_T" name="elem_XanthaOd_T" value="T" <?php echo ($elem_XanthaOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_pos1" name="elem_XanthaOd_pos1" value="1+" <?php echo ($elem_XanthaOd_pos1 == "+1" || $elem_XanthaOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_pos2" name="elem_XanthaOd_pos2" value="2+" <?php echo ($elem_XanthaOd_pos2 == "+2" || $elem_XanthaOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_pos3" name="elem_XanthaOd_pos3" value="3+" <?php echo ($elem_XanthaOd_pos3 == "+3" || $elem_XanthaOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_pos4" name="elem_XanthaOd_pos4" value="4+" <?php echo ($elem_XanthaOd_pos4 == "+4" || $elem_XanthaOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_rul" name="elem_XanthaOd_rul" value="RUL" <?php echo ($elem_XanthaOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_rul" >RUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOd_rll" name="elem_XanthaOd_rll" value="RLL" <?php echo ($elem_XanthaOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOd_rll" >RLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Xanthelasma_od;
				} 
			?>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Xantha')">BL</td>
		<td align="left">Xanthelasma</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_neg" name="elem_XanthaOs_neg" value="Absent" <?php echo ($elem_XanthaOs_neg == "-ve" || $elem_XanthaOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_neg" >Absent</label>
		</td>
		<td>
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_T" name="elem_XanthaOs_T" value="T" <?php echo ($elem_XanthaOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_T" >T</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_pos1" name="elem_XanthaOs_pos1" value="1+" <?php echo ($elem_XanthaOs_pos1 == "+1" || $elem_XanthaOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_pos1" >1+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_pos2" name="elem_XanthaOs_pos2" value="2+" <?php echo ($elem_XanthaOs_pos2 == "+2" || $elem_XanthaOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_pos2" >2+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_pos3" name="elem_XanthaOs_pos3" value="3+" <?php echo ($elem_XanthaOs_pos3 == "+3" || $elem_XanthaOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_pos3" >3+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_pos4" name="elem_XanthaOs_pos4" value="4+" <?php echo ($elem_XanthaOs_pos4 == "+4" || $elem_XanthaOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_pos4" >4+</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_rul" name="elem_XanthaOs_rul" value="LUL" <?php echo ($elem_XanthaOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_rul" >LUL</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_XanthaOs_rll" name="elem_XanthaOs_rll" value="LLL" <?php echo ($elem_XanthaOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_XanthaOs_rll" >LLL</label>
		</td>
		<td>
			<?php  
				if($flg_showPlastic=="1"){ //Check Template 
					echo $tmp_htmadv_Xanthelasma_os;
				} 
			?>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Xanthelasma"])){ echo $arr_exm_ext_htm["Lesion"]["Xanthelasma"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lesion"]["Main"])){ echo $arr_exm_ext_htm["Lesion"]["Main"]; }  ?>
		
		<tr id="d_adOpt_lesion">
		<td align="left">Comments</td>
		<td colspan="9">
			<textarea id="od_93" onBlur="checkwnls();" name="elem_lesionAdOptionsOd" class="form-control"><?php echo ($elem_lesionAdOptionsOd);?></textarea>
		</td>
		<td align="center" class="bilat" onClick="check_bl('adOpt_lesion')">BL</td>
		<td align="left">Comments</td>
		<td colspan="9">
			<textarea id="os_93" onBlur="checkwnls();" name="elem_lesionAdOptionsOs" class="form-control"><?php echo ($elem_lesionAdOptionsOs);?></textarea>
		</td>
		</tr>		
		
		</table>
	</div>	
    </div>
    <div role="tabpanel" class="tab-pane <?php if(3 == $defTabKey){echo "active";} ?>" id="div3">
	<!-- Lid Pos -->
	<div class="examhd">
	<?php if($finalize_flag == 1){?>
		<label class="chart_status label label-danger pull-left">Finalized</label>
	<?php }?>

	<span id="examFlag" class="glyphicon flagWnl "></span>
		
	<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

	<input type="checkbox" id="elem_noChangeLidPos"  name="elem_noChangeLidPos" value="1" onClick="setNC2();" 
		<?php echo ($elem_ncLidPos == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
	<label class="lbl_nochange frcb" for="elem_noChangeLidPos">NO Change</label>

	<?php /*if (constant('AV_MODULE')=='YES'){?>
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
	<?php }*/ ?>
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
		
		<tr id="d_Entro">
		<td align="left">Entropion</td>
		<td>
		<input id="od_94" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_neg" value="Absent" <?php echo ($elem_entroOd_neg == "-ve" || $elem_entroOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_94" >Absent</label>
		</td>
		<td>
		<input  id="od_95" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_T" value="T" <?php echo ($elem_entroOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_95" >T</label>
		</td>
		<td>				
		<input  id="od_96" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_pos1" value="1+" <?php echo ($elem_entroOd_pos1 == "+1" || $elem_entroOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_96" >1+</label>
		</td>
		<td>				
		<input  id="od_97" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_pos2" value="2+" <?php echo ($elem_entroOd_pos2 == "+2" || $elem_entroOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_97" >2+</label>
		</td>
		<td>				
		<input  id="od_98" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_pos3" value="3+" <?php echo ($elem_entroOd_pos3 == "+3" || $elem_entroOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_98" >3+</label>
		</td>
		<td>				
		<input  id="od_99" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_pos4" value="4+" <?php echo ($elem_entroOd_pos4 == "+4" || $elem_entroOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_99" >4+</label>
		</td>
		<td>				
		<input  id="od_101" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_rul" value="RUL" <?php echo ($elem_entroOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_101" >RUL</label>
		</td>
		<td>				
		<input  id="od_102" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOd_rll" value="RLL" <?php echo ($elem_entroOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_102" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Entro')">BL</td>
		<td align="left">Entropion</td>
		<td>
		<input  id="os_94" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_neg" value="Absent" <?php echo ($elem_entroOs_neg == "-ve" || $elem_entroOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_94" >Absent</label>
		</td>
		<td>
		<input id="os_95" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_T" value="T" <?php echo ($elem_entroOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_95" >T</label>
		</td>
		<td>				
		<input id="os_96" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_pos1" value="1+" <?php echo ($elem_entroOs_pos1 == "+1" || $elem_entroOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_96" >1+</label>
		</td>
		<td>				
		<input id="os_97" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_pos2" value="2+" <?php echo ($elem_entroOs_pos2 == "+2" || $elem_entroOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_97" >2+</label>
		</td>
		<td>				
		<input id="os_98" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_pos3" value="3+" <?php echo ($elem_entroOs_pos3 == "+3" || $elem_entroOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_98" >3+</label>
		</td>
		<td>				
		<input id="os_99" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_pos4" value="4+" <?php echo ($elem_entroOs_pos4 == "+4" || $elem_entroOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_99" >4+</label>
		</td>
		<td>				
		<input id="os_101" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_rul" value="LUL" <?php echo ($elem_entroOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_101" >LUL</label>
		</td>
		<td>				
		<input id="os_102" type="checkbox"  onclick="checkAbsent(this)" name="elem_entroOs_rll" value="LLL" <?php echo ($elem_entroOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_102" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lid Position"]["Entropion"])){ echo $arr_exm_ext_htm["Lid Position"]["Entropion"]; }  ?>
		
		<tr id="d_Ectro">
		<td align="left">Ectropion</td>
		<td>
		<input id="od_103" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_neg" value="Absent" <?php echo ($elem_ectroOd_neg == "-ve" || $elem_ectroOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_103" >Absent</label>
		</td>
		<td>
		<input id="od_104" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_T" value="T" <?php echo ($elem_ectroOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_104" >T</label>
		</td>
		<td>				
		<input id="od_105" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_pos1" value="1+" <?php echo ($elem_ectroOd_pos1 == "+1" || $elem_ectroOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_105" >1+</label>
		</td>
		<td>				
		<input id="od_106" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_pos2" value="2+" <?php echo ($elem_ectroOd_pos2 == "+2" || $elem_ectroOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_106" >2+</label>
		</td>
		<td>				
		<input id="od_107" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_pos3" value="3+" <?php echo ($elem_ectroOd_pos3 == "+3" || $elem_ectroOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_107" >3+</label>
		</td>
		<td>				
		<input id="od_108" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_pos4" value="4+" <?php echo ($elem_ectroOd_pos4 == "+4" || $elem_ectroOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_108" >4+</label>
		</td>
		<td>				
		<input id="od_110" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_rul" value="RUL" <?php echo ($elem_ectroOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_110" >RUL</label>
		</td>
		<td>				
		<input id="od_111" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOd_rll" value="RLL" <?php echo ($elem_ectroOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_111" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Ectro')">BL</td>
		<td align="left">Ectropion</td>
		<td>
		<input id="os_103" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_neg" value="Absent" <?php echo ($elem_ectroOs_neg == "-ve" || $elem_ectroOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_103" >Absent</label>
		</td>
		<td>
		<input id="os_104" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_T" value="T" <?php echo ($elem_ectroOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_104" >T</label>
		</td>
		<td>				
		<input id="os_105" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_pos1" value="1+" <?php echo ($elem_ectroOs_pos1 == "+1" || $elem_ectroOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_105" >1+</label>
		</td>
		<td>				
		<input id="os_106" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_pos2" value="2+" <?php echo ($elem_ectroOs_pos2 == "+2" || $elem_ectroOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_106" >2+</label>
		</td>
		<td>				
		<input id="os_107" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_pos3" value="3+" <?php echo ($elem_ectroOs_pos3 == "+3" || $elem_ectroOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_107" >3+</label>
		</td>
		<td>				
		<input id="os_108" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_pos4" value="4+" <?php echo ($elem_ectroOs_pos4 == "+4" || $elem_ectroOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_108" >4+</label>
		</td>
		<td>				
		<input id="os_110" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_rul" value="LUL" <?php echo ($elem_ectroOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_110" >LUL</label>
		</td>
		<td>				
		<input id="os_111" type="checkbox"  onclick="checkAbsent(this)" name="elem_ectroOs_rll" value="LLL" <?php echo ($elem_ectroOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_111" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lid Position"]["Ectropion"])){ echo $arr_exm_ext_htm["Lid Position"]["Ectropion"]; } ?>
		
		<tr id="d_Ptosis">
		<td align="left">Ptosis</td>
		<td>
		<input id="od_112" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_neg" value="Absent" <?php echo ($elem_ptosisOd_neg == "-ve" || $elem_ptosisOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_112" >Absent</label>
		</td>
		<td>
		<input id="od_113" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_T" value="T" <?php echo ($elem_ptosisOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_113" >T</label>
		</td>
		<td>				
		<input id="od_114" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_pos1" value="1+" <?php echo ($elem_ptosisOd_pos1 == "+1" || $elem_ptosisOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_114" >1+</label>
		</td>
		<td>				
		<input id="od_115" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_pos2" value="2+" <?php echo ($elem_ptosisOd_pos2 == "+2" || $elem_ptosisOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_115" >2+</label>
		</td>
		<td>				
		<input id="od_116" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_pos3" value="3+" <?php echo ($elem_ptosisOd_pos3 == "+3" || $elem_ptosisOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_116" >3+</label>
		</td>
		<td>				
		<input id="od_117" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_pos4" value="4+" <?php echo ($elem_ptosisOd_pos4 == "+4" || $elem_ptosisOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_117" >4+</label>
		</td>
		<td>				
		<input id="od_119" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_rul" value="RUL" <?php echo ($elem_ptosisOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_119" >RUL</label>
		</td>
		<td>				
		<input id="od_120" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOd_rll" value="RLL" <?php echo ($elem_ptosisOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_120" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Ptosis')">BL</td>
		<td align="left">Ptosis</td>
		<td>
		<input id="os_112" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_neg" value="Absent" <?php echo ($elem_ptosisOs_neg == "-ve" || $elem_ptosisOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_112" >Absent</label>
		</td>
		<td>
		<input id="os_113" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_T" value="T" <?php echo ($elem_ptosisOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_113" >T</label>
		</td>
		<td>				
		<input id="os_114" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_pos1" value="1+" <?php echo ($elem_ptosisOs_pos1 == "+1" || $elem_ptosisOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_114" >1+</label>
		</td>
		<td>				
		<input id="os_115" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_pos2" value="2+" <?php echo ($elem_ptosisOs_pos2 == "+2" || $elem_ptosisOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_115" >2+</label>
		</td>
		<td>				
		<input id="os_116" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_pos3" value="3+" <?php echo ($elem_ptosisOs_pos3 == "+3" || $elem_ptosisOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_116" >3+</label>
		</td>
		<td>				
		<input id="os_117" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_pos4" value="4+" <?php echo ($elem_ptosisOs_pos4 == "+4" || $elem_ptosisOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_117" >4+</label>
		</td>
		<td>				
		<input id="os_119" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_rul" value="LUL" <?php echo ($elem_ptosisOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_119" >LUL</label>
		</td>
		<td>				
		<input id="os_120" type="checkbox"  onclick="checkAbsent(this)" name="elem_ptosisOs_rll" value="LLL" <?php echo ($elem_ptosisOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_120" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lid Position"]["Ptosis"])){ echo $arr_exm_ext_htm["Lid Position"]["Ptosis"]; } ?>
		
		<tr id="d_Derma">
		<td align="left">Dermatochalasis</td>
		<td>
		<input id="od_121" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_neg" value="Absent" <?php echo ($elem_dermaOd_neg == "-ve" || $elem_dermaOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_121" >Absent</label>
		</td>
		<td>
		<input id="od_122" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_T" value="T" <?php echo ($elem_dermaOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_122" >T</label>
		</td>
		<td>				
		<input id="od_123" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_pos1" value="1+" <?php echo ($elem_dermaOd_pos1 == "+1" || $elem_dermaOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_123" >1+</label>
		</td>
		<td>				
		<input id="od_124" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_pos2" value="2+" <?php echo ($elem_dermaOd_pos2 == "+2" || $elem_dermaOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_124" >2+</label>
		</td>
		<td>				
		<input id="od_125" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_pos3" value="3+" <?php echo ($elem_dermaOd_pos3 == "+3" || $elem_dermaOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_125" >3+</label>
		</td>
		<td>				
		<input id="od_126" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_pos4" value="4+" <?php echo ($elem_dermaOd_pos4 == "+4" || $elem_dermaOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_126" >4+</label>
		</td>
		<td>				
		<input id="od_128" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_rul" value="RUL" <?php echo ($elem_dermaOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_128" >RUL</label>
		</td>
		<td>				
		<input id="od_129" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOd_rll" value="RLL" <?php echo ($elem_dermaOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_129" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('Derma')">BL</td>
		<td align="left">Dermatochalasis</td>
		<td>
		<input id="os_121" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_neg" value="Absent" <?php echo ($elem_dermaOs_neg == "-ve" || $elem_dermaOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_121" >Absent</label>
		</td>
		<td>
		<input id="os_122" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_T" value="T" <?php echo ($elem_dermaOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_122" >T</label>
		</td>
		<td>				
		<input id="os_123" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_pos1" value="1+" <?php echo ($elem_dermaOs_pos1 == "+1" || $elem_dermaOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_123" >1+</label>
		</td>
		<td>				
		<input id="os_124" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_pos2" value="2+" <?php echo ($elem_dermaOs_pos2 == "+2" || $elem_dermaOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_124" >2+</label>
		</td>
		<td>				
		<input id="os_125" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_pos3" value="3+" <?php echo ($elem_dermaOs_pos3 == "+3" || $elem_dermaOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_125" >3+</label>
		</td>
		<td>				
		<input id="os_126" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_pos4" value="4+" <?php echo ($elem_dermaOs_pos4 == "+4" || $elem_dermaOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_126" >4+</label>
		</td>
		<td>				
		<input id="os_128" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_rul" value="LUL" <?php echo ($elem_dermaOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_128" >LUL</label>
		</td>
		<td>				
		<input id="os_129" type="checkbox"  onclick="checkAbsent(this)" name="elem_dermaOs_rll" value="LLL" <?php echo ($elem_dermaOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_129" >LLL</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lid Position"]["Dermatochalasis"])){ echo $arr_exm_ext_htm["Lid Position"]["Dermatochalasis"]; } ?>
		
		<tr id="d_3L">
		<td align="left">Lower Lid Lag</td>
		<td>
		<input id="od_130" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_neg" value="Absent" <?php echo ($elem_lowerLadLagOd_neg == "-ve" || $elem_lowerLadLagOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="od_130" >Absent</label>
		</td>
		<td>
		<input id="od_131" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_T" value="T" <?php echo ($elem_lowerLadLagOd_T == "T") ? "checked=\"checked\"" : "";?>><label for="od_131" >T</label>
		</td>
		<td>			    
		<input id="od_132" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_pos1" value="1+" <?php echo ($elem_lowerLadLagOd_pos1 == "+1" || $elem_lowerLadLagOd_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="od_132" >1+</label>
		</td>
		<td>			    
		<input id="od_133" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_pos2" value="2+" <?php echo ($elem_lowerLadLagOd_pos2 == "+2" || $elem_lowerLadLagOd_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="od_133" >2+</label>
		</td>
		<td>			    
		<input id="od_134" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_pos3" value="3+" <?php echo ($elem_lowerLadLagOd_pos3 == "+3" || $elem_lowerLadLagOd_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="od_134" >3+</label>
		</td>
		<td>			    
		<input id="od_135" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_pos4" value="4+" <?php echo ($elem_lowerLadLagOd_pos4 == "+4" || $elem_lowerLadLagOd_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="od_135" >4+</label>
		</td>
		<td>			    
		<input id="od_137" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_rul" value="RUL" <?php echo ($elem_lowerLadLagOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_137" >RUL</label>
		</td>
		<td>			    
		<input id="od_138" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOd_rll" value="RLL" <?php echo ($elem_lowerLadLagOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_138" >RLL</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('3L')">BL</td>
		<td align="left">Lower Lid Lag</td>
		<td>
		<input id="os_130" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_neg" value="Absent" <?php echo ($elem_lowerLadLagOs_neg == "-ve" || $elem_lowerLadLagOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="os_130" >Absent</label>
		</td>
		<td>
		<input id="os_131" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_T" value="T" <?php echo ($elem_lowerLadLagOs_T == "T") ? "checked=\"checked\"" : "";?>><label for="os_131" >T</label>
		</td>
		<td>				
		<input id="os_132" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_pos1" value="1+" <?php echo ($elem_lowerLadLagOs_pos1 == "+1" || $elem_lowerLadLagOs_pos1 == "1+") ? "checked=\"checked\"" : "";?>><label for="os_132" >1+</label>
		</td>
		<td>				
		<input id="os_133" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_pos2" value="2+" <?php echo ($elem_lowerLadLagOs_pos2 == "+2" || $elem_lowerLadLagOs_pos2 == "2+") ? "checked=\"checked\"" : "";?>><label for="os_133" >2+</label>
		</td>
		<td>				
		<input id="os_134" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_pos3" value="3+" <?php echo ($elem_lowerLadLagOs_pos3 == "+3" || $elem_lowerLadLagOs_pos3 == "3+") ? "checked=\"checked\"" : "";?>><label for="os_134" >3+</label>
		</td>
		<td>				
		<input id="os_135" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_pos4" value="4+" <?php echo ($elem_lowerLadLagOs_pos4 == "+4" || $elem_lowerLadLagOs_pos4 == "4+") ? "checked=\"checked\"" : "";?>><label for="os_135" >4+</label>
		</td>
		<td>				
		<input id="os_137" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_rul" value="LUL" <?php echo ($elem_lowerLadLagOs_rul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_137" >LUL</label>
		</td>
		<td>				
		<input id="os_138" type="checkbox"  onclick="checkAbsent(this)" name="elem_lowerLadLagOs_rll" value="LLL" <?php echo ($elem_lowerLadLagOs_rll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_138" >LLL</label>
		</td>
		</tr>
		<?php 
			if(isset($arr_exm_ext_htm["Lid Position"]["Lower Lid Lag"])){ echo $arr_exm_ext_htm["Lid Position"]["Lower Lid Lag"]; } 
			if(isset($arr_exm_ext_htm["Lid Position"]["Main"])){ echo $arr_exm_ext_htm["Lid Position"]["Main"]; }

			

		?>
		
		<tr id="d_adOpt_lidpos">
		<td align="left">Comments</td>
		<td colspan="8">
			<textarea id="od_139" onBlur="checkwnls();" name="elem_lidPosAdOptionsOd" class="form-control"><?php echo ($elem_lidPosAdOptionsOd);?></textarea>
		</td>
		<td align="center" class="bilat" onClick="check_bl('adOpt_lidpos')">BL</td>
		<td align="left">Comments</td>
		<td colspan="8">
			<textarea id="os_139" onBlur="checkwnls();" name="elem_lidPosAdOptionsOs" class="form-control"><?php echo ($elem_lidPosAdOptionsOs);?></textarea>
		</td>
		</tr>		
		</table>
	</div>	
	<!-- Lid Pos -->
    </div>
    <div role="tabpanel" class="tab-pane <?php if(4 == $defTabKey){echo "active";} ?>" id="div4">
	<!-- Lac Sys -->
	<div class="examhd">
	<?php if($finalize_flag == 1){?>
		<label class="chart_status label label-danger pull-left">Finalized</label>
	<?php }?>

	<span id="examFlag" class="glyphicon flagWnl "></span>
		
	<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

	<input type="checkbox" id="elem_noChangeLacSys"  name="elem_noChangeLacSys" value="1" onClick="setNC2();" 
		<?php echo ($elem_ncLacSys == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
	<label class="lbl_nochange frcb" for="elem_noChangeLacSys">NO Change</label>

	<?php /*if (constant('AV_MODULE')=='YES'){?>
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
	<?php }*/ ?>
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
		
		<tr class="exmhlgcol grp_Puncta sbGrpOpen" id="d_Puncta">
		<td align="left">Puncta</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_Absent" name="elem_lac_punctaOd_Absent" value="Absent" <?php echo ($elem_lac_punctaOd_Absent == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_Absent" >Absent</label>
		</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_AbsntUl" name="elem_lac_punctaOd_AbsntUl" value="RUL" <?php echo ($elem_lac_punctaOd_AbsntUl == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_AbsntUl" >RUL</label>
		</td>
		<td colspan="2">			
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_AbsntLl" name="elem_lac_punctaOd_AbsntLl" value="RLL" <?php echo ($elem_lac_punctaOd_AbsntLl == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_AbsntLl" >RLL</label>
		</td>
		<td align="center" class="bilat" rowspan="6" onClick="check_bl('Puncta')">BL</td>
		<td align="left">Puncta</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_Absent" name="elem_lac_punctaOs_Absent" value="Absent" <?php echo ($elem_lac_punctaOs_Absent == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_Absent" >Absent</label>
		</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_AbsntUl" name="elem_lac_punctaOs_AbsntUl" value="LUL" <?php echo ($elem_lac_punctaOs_AbsntUl == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_AbsntUl" >LUL</label>
		</td>
		<td colspan="2">			
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_AbsntLl" name="elem_lac_punctaOs_AbsntLl" value="LLL" <?php echo ($elem_lac_punctaOs_AbsntLl == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_AbsntLl" >LLL</label>
		</td>
		</tr>
		
		
		<tr class="exmhlgcol grp_Puncta sbGrpOpen" id="d_Puncta_sml">
		<td align="left"></td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_small" name="elem_lac_punctaOd_small" value="Small" <?php echo ($elem_lac_punctaOd_small == "Small") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_small" >Small</label>
		</td>
		<td colspan="3">				 
		 <input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_smUl" name="elem_lac_punctaOd_smUl" value="RUL" <?php echo ($elem_lac_punctaOd_smUl == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_smUl" >RUL</label>
		</td>
		<td colspan="2">			 
		 <input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_smLl" name="elem_lac_punctaOd_smLl" value="RLL" <?php echo ($elem_lac_punctaOd_smLl == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_smLl" >RLL</label>
		</td>
		
		<td align="left"></td>
		<td colspan="3">			 
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_small" name="elem_lac_punctaOs_small" value="Small" <?php echo ($elem_lac_punctaOs_small == "Small") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_small" >Small</label>
		</td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_smUl" name="elem_lac_punctaOs_smUl" value="LUL" <?php echo ($elem_lac_punctaOs_smUl == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_smUl" >LUL</label>
		</td>
		<td colspan="2">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_smLl" name="elem_lac_punctaOs_smLl" value="LLL" <?php echo ($elem_lac_punctaOs_smLl == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_smLl" >LLL</label>
		</td>
		</tr>
		
		<tr class="exmhlgcol grp_Puncta sbGrpOpen" id="d_Puncta_med">
		<td align="left"></td>
		<td colspan="3">	
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_med" name="elem_lac_punctaOd_med" value="Med" <?php echo ($elem_lac_punctaOd_med == "Med") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_med" >Med</label>
		</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_medUl" name="elem_lac_punctaOd_medUl" value="RUL" <?php echo ($elem_lac_punctaOd_medUl == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_medUl" >RUL</label>
		</td>
		<td colspan="2">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_medLl" name="elem_lac_punctaOd_medLl" value="RLL" <?php echo ($elem_lac_punctaOd_medLl == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_medLl" >RLL</label>
		</td>
		
		<td align="left"></td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_med" name="elem_lac_punctaOs_med" value="Med" <?php echo ($elem_lac_punctaOs_med == "Med") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_med" >Med</label>
		</td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_medUl" name="elem_lac_punctaOs_medUl" value="LUL" <?php echo ($elem_lac_punctaOs_medUl == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_medUl" >LUL</label>
		</td>
		<td colspan="2">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_medLl" name="elem_lac_punctaOs_medLl" value="LLL" <?php echo ($elem_lac_punctaOs_medLl == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_medLl" >LLL</label>
		</td>
		</tr>
		
		
		<tr class="exmhlgcol grp_Puncta sbGrpOpen" id="d_Puncta_lrg">
		<td align="left"></td>
		<td colspan="3">	
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_large" name="elem_lac_punctaOd_large" value="Large" <?php echo ($elem_lac_punctaOd_large == "Large") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_large" >Large</label>
		</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_largeUl" name="elem_lac_punctaOd_largeUl" value="RUL" <?php echo ($elem_lac_punctaOd_largeUl == "RUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_largeUl" >RUL</label>
		</td>
		<td colspan="2">    
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_largeLl" name="elem_lac_punctaOd_largeLl" value="RLL" <?php echo ($elem_lac_punctaOd_largeLl == "RLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_largeLl" >RLL</label>
		</td>
		
		<td align="left"></td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_large" name="elem_lac_punctaOs_large" value="Large" <?php echo ($elem_lac_punctaOs_large == "Large") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_large" >Large</label>
		</td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_largeUl" name="elem_lac_punctaOs_largeUl" value="LUL" <?php echo ($elem_lac_punctaOs_largeUl == "LUL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_largeUl" >LUL</label>
		</td>
		<td colspan="2">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_largeLl" name="elem_lac_punctaOs_largeLl" value="LLL" <?php echo ($elem_lac_punctaOs_largeLl == "LLL") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_largeLl" >LLL</label>
		</td>
		</tr>		
		
		<tr class="exmhlgcol grp_Puncta sbGrpOpen" id="d_Puncta_sclrsd">
		<td align="left"></td>
		<td colspan="3">				   
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_sclerosed" name="elem_lac_punctaOd_sclerosed" value="Sclerosed" <?php echo ($elem_lac_punctaOd_sclerosed == "Sclerosed") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_sclerosed" >Sclerosed</label>
		</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_elevate" name="elem_lac_punctaOd_elevate" value="RLL Eversion" <?php echo ($elem_lac_punctaOd_elevate == "RLL Eversion") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_elevate" >RLL Eversion</label>
		</td>
		<td colspan="2"></td>
		
		<td align="left"></td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_sclerosed" name="elem_lac_punctaOs_sclerosed" value="Sclerosed" <?php echo ($elem_lac_punctaOs_sclerosed == "Sclerosed") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_sclerosed" >Sclerosed</label>
		</td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_elevate" name="elem_lac_punctaOs_elevate" value="LLL Eversion" <?php echo ($elem_lac_punctaOs_elevate == "LLL Eversion") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_elevate" >LLL Eversion</label>
		</td>
		<td colspan="2"></td>
		</tr>
		
		
		<tr class="exmhlgcol grp_Puncta sbGrpOpen" id="d_Puncta_silp">
		<td align="left"></td>
		<td colspan="3">	
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_silPlug" name="elem_lac_punctaOd_silPlug" value="Sil Plug" <?php echo ($elem_lac_punctaOd_silPlug == "Sil Plug") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_silPlug" >Sil Plug</label>
		</td>
		<td colspan="3">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_colPlug" name="elem_lac_punctaOd_colPlug" value="Col Plug" <?php echo ($elem_lac_punctaOd_colPlug == "Col Plug") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_colPlug" >Col Plug</label>
		</td>
		<td colspan="2">
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOd_3monPlug" name="elem_lac_punctaOd_3monPlug" value="3Mon Plug" <?php echo ($elem_lac_punctaOd_3monPlug == "3Mon Plug") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOd_3monPlug" >3Mon Plug</label>
		</td>
		
		<td align="left"></td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_silPlug" name="elem_lac_punctaOs_silPlug" value="Sil Plug" <?php echo ($elem_lac_punctaOs_silPlug == "Sil Plug") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_silPlug" >Sil Plug</label>
		</td>
		<td colspan="3">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_colPlug" name="elem_lac_punctaOs_colPlug" value="Col Plug" <?php echo ($elem_lac_punctaOs_colPlug == "Col Plug") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_colPlug" >Col Plug</label>
		</td>
		<td colspan="2">				
		<input type="checkbox" onClick="checkAbsent(this)" id="elem_lac_punctaOs_3monPlug" name="elem_lac_punctaOs_3monPlug" value="3Mon Plug" <?php echo ($elem_lac_punctaOs_3monPlug == "3Mon Plug") ? "checked=\"checked\"" : "";?>><label for="elem_lac_punctaOs_3monPlug" >3Mon Plug</label>
		</td>
		</tr>		
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Puncta"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Puncta"]; }  ?>
		
		<tr class="exmhlgcol grp_LD sbGrpOpen" id="d_LD">
		<td align="left">Lacrimal Duct</td>
		<td colspan="8">
			<textarea id="od_147" onBlur="checkwnls();" name="elem_lacrimalDuctOd_text" class="form-control"><?php echo ($elem_lacrimalDuctOd_text);?></textarea>
		</td>
		<td align="center" class="bilat"  onClick="check_bl('LD')">BL</td>
		<td align="left">Lacrimal Duct</td>
		<td colspan="8">
			<textarea id="os_147" onBlur="checkwnls();" name="elem_lacrimalDuctOs_text" class="form-control"><?php echo ($elem_lacrimalDuctOs_text);?></textarea>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Lacrimal Duct"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Lacrimal Duct"]; }  ?>
		
		<tr class="exmhlgcol grp_LD sbGrpOpen" id="d_LD_ts">
		<td align="left">Tube Stent</td>
		<td colspan="3">
		<input  id="od_148" type="checkbox"  onclick="checkwnls()" name="elem_tubeStentOd_rul" value="RUL" <?php echo ($elem_tubeStentOd_rul == "RUL") ? "checked=\"checked\"" : "";?>><label for="od_148"  class="tube_rul">RUL</label>
		</td>
		<td colspan="3">
		<input  id="od_149" type="checkbox"  onclick="checkwnls()" name="elem_tubeStentOd_rll" value="RLL" <?php echo ($elem_tubeStentOd_rll == "RLL") ? "checked=\"checked\"" : "";?>><label for="od_149" >RLL</label>
		</td>
		<td colspan="2">					    
		<input  type="checkbox"  onclick="checkwnls()" id="elem_tubeStentOd_Bicanalicular" name="elem_tubeStentOd_Bicanalicular" value="Bicanalicular" <?php echo ($elem_tubeStentOd_Bicanalicular == "Bicanalicular") ? "checked=\"checked\"" : "";?>><label for="elem_tubeStentOd_Bicanalicular" >Bicanalicular</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('LD')">BL</td>
		<td align="left">Tube Stent</td>
		<td colspan="3">
		<input  id="os_148" type="checkbox"  onclick="checkwnls()" name="elem_tubeStentOs_lul" value="LUL" <?php echo ($elem_tubeStentOs_lul == "LUL") ? "checked=\"checked\"" : "";?>><label for="os_148"  class="tube_rul">LUL</label>
		</td>
		<td colspan="3">
		<input  id="os_149" type="checkbox"  onclick="checkwnls()" name="elem_tubeStentOs_lll" value="LLL" <?php echo ($elem_tubeStentOs_lll == "LLL") ? "checked=\"checked\"" : "";?>><label for="os_149" >LLL</label>
		</td>
		<td colspan="2">			
		<input  type="checkbox"  onclick="checkwnls()" id="elem_tubeStentOs_Bicanalicular" name="elem_tubeStentOs_Bicanalicular" value="Bicanalicular" <?php echo ($elem_tubeStentOs_Bicanalicular == "Bicanalicular") ? "checked=\"checked\"" : "";?>><label for="elem_tubeStentOs_Bicanalicular" >Bicanalicular</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Lacrimal Duct/Tube Stent"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Lacrimal Duct/Tube Stent"]; }  ?>
		
		
		<tr id="d_LS">
		<td align="left">Lacrimal Sac</td>
		<td colspan="3">
		<input  id="od_151" type="checkbox"  onclick="checkwnls()" name="elem_lacrimalSacOd_dacry" value="Dacryocystitis" <?php echo ($elem_lacrimalSacOd_dacry == "Dacryocystitis") ? "checked=\"checked\"" : "";?>><label for="od_151" >Dacryocystitis</label>
		</td>
		<td colspan="3">
		<input id="od_152" type="checkbox"  onclick="checkwnls()" name="elem_lacrimalSacOd_mass" value="Mass" <?php echo ($elem_lacrimalSacOd_mass == "Mass") ? "checked=\"checked\"" : "";?>><label for="od_152" >Mass</label>
		</td>
		<td colspan="2"></td>
		<td align="center" class="bilat" onClick="check_bl('LS')">BL</td>
		<td align="left">Lacrimal Sac</td>
		<td colspan="3">
		<input  id="os_151" type="checkbox"  onclick="checkwnls()" name="elem_lacrimalSacOs_dacry" value="Dacryocystitis" <?php echo ($elem_lacrimalSacOs_dacry == "Dacryocystitis") ? "checked=\"checked\"" : "";?>><label for="os_151" >Dacryocystitis</label>
		</td>
		<td colspan="3">
		<input  id="os_152" type="checkbox"  onclick="checkwnls()" name="elem_lacrimalSacOs_mass" value="Mass" <?php echo ($elem_lacrimalSacOs_mass == "Mass") ? "checked=\"checked\"" : "";?>><label for="os_152" >Mass</label>
		</td>
		<td colspan="2"></td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Lacrimal Sac"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Lacrimal Sac"]; }
				
		?>
		
		<tr id="d_PD">
		<td align="left">Punctal Discharge</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOd_neg" name="elem_punctalDischargeOd_neg" value="Absent" <?php echo ($elem_punctalDischargeOd_neg == "-ve" || $elem_punctalDischargeOd_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOd_neg"  class="tube_rul">Absent</label>
		</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOd_pos" name="elem_punctalDischargeOd_pos" value="Present" <?php echo ($elem_punctalDischargeOd_pos == "+ve" || $elem_punctalDischargeOd_pos == "Present") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOd_pos" >Present</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOd_Upper" name="elem_punctalDischargeOd_Upper" value="Upper" <?php echo ($elem_punctalDischargeOd_Upper == "Upper") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOd_Upper"  >Upper</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOd_Lower" name="elem_punctalDischargeOd_Lower" value="Lower" <?php echo ($elem_punctalDischargeOd_Lower == "Lower") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOd_Lower" >Lower</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOd_Left" name="elem_punctalDischargeOd_Left" value="Left" <?php echo ($elem_punctalDischargeOd_Left == "Left") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOd_Left"  >Left</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOd_Right" name="elem_punctalDischargeOd_Right" value="Right" <?php echo ($elem_punctalDischargeOd_Right == "Right") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOd_Right" >Right</label>
		</td>
		<td align="center" class="bilat" onClick="check_bl('PD')">BL</td>
		<td align="left">Punctal Discharge</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOs_neg" name="elem_punctalDischargeOs_neg" value="Absent" <?php echo ($elem_punctalDischargeOs_neg == "-ve" || $elem_punctalDischargeOs_neg == "Absent") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOs_neg"  class="tube_rul">Absent</label>
		</td>
		<td colspan="2">
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOs_pos" name="elem_punctalDischargeOs_pos" value="Present" <?php echo ($elem_punctalDischargeOs_pos == "+ve" || $elem_punctalDischargeOs_pos == "Present") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOs_pos" >Present</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOs_Upper" name="elem_punctalDischargeOs_Upper" value="Upper" <?php echo ($elem_punctalDischargeOs_Upper == "Upper") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOs_Upper"  >Upper</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOs_Lower" name="elem_punctalDischargeOs_Lower" value="Lower" <?php echo ($elem_punctalDischargeOs_Lower == "Lower") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOs_Lower" >Lower</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOs_Left" name="elem_punctalDischargeOs_Left" value="Left" <?php echo ($elem_punctalDischargeOs_Left == "Left") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOs_Left"  >Left</label>
		</td>
		<td>				
		<input  type="checkbox"  onclick="checkAbsent(this)" id="elem_punctalDischargeOs_Right" name="elem_punctalDischargeOs_Right" value="Right" <?php echo ($elem_punctalDischargeOs_Right == "Right") ? "checked=\"checked\"" : "";?>><label for="elem_punctalDischargeOs_Right" >Right</label>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Punctal Discharge"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Punctal Discharge"]; }  ?>
		
		<tr id="d_SchT">
		<td align="left">Schirmer's Test</td>
		<td colspan="3">
		<textarea name="elem_schirmerOd_finding" onBlur="insertTime(this);checkwnls();" class="form-control" ><?php echo $elem_schirmerOd_finding;?></textarea>				
		</td>
		<td colspan="1" class="form-inline schirmer">
		<input type="text" id="elem_schirmerOd_date" name="elem_schirmerOd_date" onBlur="checkwnls();"  size="10" value="<?php echo $elem_schirmerOd_date;?>" class="form-control" placeholder="Date">
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_schirmerOd_timeStamp" class="aato">Start</label>
		<input type="text" id="elem_schirmerOd_timeStamp" name="elem_schirmerOd_timeStamp" onBlur="checkwnls();" onClick="insertTime(this);" size="6" value="<?php echo $elem_schirmerOd_timeStamp;?>" class="form-control">
		</div>
		</td>
		<td colspan="2" class="form-inline">	
		<div class="form-group">
		<label class="aato" for="elem_schirmerOd_timeStamp_end">End</label>
		<input type="text" id="elem_schirmerOd_timeStamp_end" name="elem_schirmerOd_timeStamp_end" onBlur="checkwnls();" size="6" onClick="insertTime(this);" value="<?php echo $elem_schirmerOd_timeStamp_end;?>" class="form-control">
		</div>
		</td>
		<td align="center" class="bilat" onClick="check_bl('SchT')">BL</td>
		<td align="left">Schirmer's Test</td>
		<td colspan="3">		
		<textarea name="elem_schirmerOs_finding" onBlur="insertTime(this);checkwnls();" class="form-control" ><?php echo $elem_schirmerOs_finding;?></textarea>
		</td>
		<td colspan="1" class="form-inline schirmer">
		<input type="text" id="elem_schirmerOs_date" name="elem_schirmerOs_date" onBlur="checkwnls();"  size="10" value="<?php echo $elem_schirmerOs_date;?>" class="form-control" placeholder="Date">	
		</td>
		<td colspan="2" class="form-inline">
		<div class="form-group">
		<label for="elem_schirmerOs_timeStamp">Start</label>&nbsp;				
		<input type="text" id="elem_schirmerOs_timeStamp" name="elem_schirmerOs_timeStamp" onBlur="checkwnls();" size="6"  onClick="insertTime(this);" value="<?php echo $elem_schirmerOs_timeStamp;?>" class="form-control">
		</div>
		</td>
		<td colspan="2" class="form-inline"> 			
		<div class="form-group">
		<label for="elem_schirmerOs_timeStamp_end">End</label>
		<input type="text" id="elem_schirmerOs_timeStamp_end" name="elem_schirmerOs_timeStamp_end" onBlur="checkwnls();" 	size="6"  onClick="insertTime(this);" value="<?php echo $elem_schirmerOs_timeStamp_end;?>" class="form-control">
		</div>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Schirmer's Test"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Schirmer's Test"]; }  ?>
		
		<tr id="d_DDT">
		<td align="left">DDT</td>
		<td colspan="3">
		<textarea name="elem_ddtOd_finding" onBlur="insertTime(this);checkwnls();" class="form-control" ><?php echo $elem_ddtOd_finding;?></textarea>
		</td>
		<td colspan="3" class="form-inline">
		<div class="form-group">
		<label for="elem_ddtOd_timeStamp">Start</label>
		<input type="text" id="elem_ddtOd_timeStamp" name="elem_ddtOd_timeStamp" onBlur="checkwnls();" size="6" onClick="insertTime(this);" value="<?php echo $elem_ddtOd_timeStamp;?>" class="form-control">
		</div>
		</td>
		<td colspan="2" class="form-inline">    
		<div class="form-group">
		<label for="elem_ddtOd_timeStamp_end">End</label>
		<input type="text" id="elem_ddtOd_timeStamp_end" name="elem_ddtOd_timeStamp_end" onBlur="checkwnls();" size="6" onClick="insertTime(this);" value="<?php echo $elem_ddtOd_timeStamp_end;?>" class="form-control">
		</div>
		</td>
		<td align="center" class="bilat" onClick="check_bl('DDT')">BL</td>
		<td align="left">DDT</td>
		<td colspan="3">
		<textarea name="elem_ddtOs_finding" onBlur="insertTime(this);checkwnls();" class="form-control" ><?php echo $elem_ddtOs_finding;?></textarea>
		</td>
		<td colspan="3" class="form-inline">
		<div class="form-group">
		<label for="elem_ddtOs_timeStamp">Start</label>&nbsp;
		<input type="text" id="elem_ddtOs_timeStamp" name="elem_ddtOs_timeStamp" onBlur="checkwnls();" size="6" onClick="insertTime(this);" value="<?php echo $elem_ddtOs_timeStamp;?>" class="form-control">
		</div>
		</td>
		<td colspan="2" class="form-inline"> 
		<div class="form-group">
		<label for="elem_ddtOs_timeStamp_end">End</label>
		<input type="text" id="elem_ddtOs_timeStamp_end" name="elem_ddtOs_timeStamp_end" onBlur="checkwnls();" size="6" onClick="insertTime(this);" value="<?php echo $elem_ddtOs_timeStamp_end;?>" class="form-control">
		</div>
		</td>
		</tr>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["DDT"])){ echo $arr_exm_ext_htm["Lacrimal System"]["DDT"]; }  ?>
		<?php if(isset($arr_exm_ext_htm["Lacrimal System"]["Main"])){ echo $arr_exm_ext_htm["Lacrimal System"]["Main"]; }  ?>
		
		<tr id="d_adOpt_lacsys">
		<td align="left">Comments</td>
		<td id="d_adOpt_lacsys_od" colspan="8">
			<textarea onBlur="checkwnls();" id="od_181" name="elem_lacrimalSysAdOptionsOd" class="form-control"><?php echo ($elem_lacrimalSysAdOptionsOd);?></textarea>
		</td>
		<td align="center" class="bilat" onClick="check_bl('adOpt_lacsys')">BL</td>
		<td align="left">Comments</td>
		<td id="d_adOpt_lacsys_os" colspan="8">
			<textarea onBlur="checkwnls();" id="os_181" name="elem_lacrimalSysAdOptionsOs" class="form-control"><?php echo ($elem_lacrimalSysAdOptionsOs);?></textarea>
		</td>
		</tr>	
		
		</table>
	</div>
	<div class="clearfix"> </div>
	<?php 
		//Advance Options  
		include(dirname(__FILE__)."/lacrimal_advance_inc.php");
	?>
	
	<!-- Lac Sys -->
    </div>
    <div role="tabpanel" class="tab-pane" id="div5">
	<!-- Drawing -->
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
		<?php }*/ ?>
		</div>    
		<div class="clearfix"> </div>
		<div class="row">
			<div class="col-sm-2">
				<textarea onBlur="checkwnls()" name="el_la_od" id="el_la_od" class="form-control drw_text_box" ><?php echo $el_la_odd;?></textarea>
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
							$dbTollImage = "imgLidsAndLacrimalCanvas";
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
						<input type="hidden" name="hidLADrawingId<?php echo $intTempDrawCount; ?>" id="hidLADrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
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
				<textarea onBlur="checkwnls()" name="el_la_os" id="el_la_os" class="form-control drw_text_box"><?php echo $el_la_oss;?></textarea>
			</div>
		</div>
	</div>	
	<!-- Drawing -->
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
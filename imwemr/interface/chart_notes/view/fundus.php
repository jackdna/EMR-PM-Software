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
	var examName = "Fundus";
	var arrSubExams = new Array("Vitreous","Macula","Peri","BV","Draw","Optic","Retinal");
	var ProClr=<?php echo $ProClr;?>;
	var drawCntlNum=<?php echo $drawCntlNum; ?>;
	var blEnableHTMLDrawing="<?php echo $blEnableHTMLDrawing;?>";
	var def_pg='<?php echo $_GET["pg"];?>';
	var arr_lens_used=<?php echo $arr_lens_used;?>;

    </script>
</head>
<body class="exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->
<form name="frmRV" id="frmRV" action="saveCharts.php" method="post" onSubmit="freezeElemAll('0')" enctype="multipart/form-data" class="frcb">
<input type="hidden" name="elem_saveForm" value="rvtable1">
<input type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
<input type="hidden" name="elem_rvId" value="<?php echo $elem_rvId;?>">
<input type="hidden" name="elem_rvId_LF" value="<?php echo $elem_rvId_LF;?>">
<input type="hidden" name="elem_drawId_LF" value="<?php echo $elem_drawId_LF;?>">
<input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
<input type="hidden" name="elem_patientId" value="<?php echo $elem_patientId;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
<input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">
<input type="hidden" id="elem_retina_version" name="elem_retina_version" value="<?php echo $elem_retina_version;?>">

<input type="hidden" id="hid_icd10" value="<?php echo $hid_icd10;?>">
<input type="hidden" id="elem_dos" value="<?php echo $elem_dos;?>">

<input type="hidden" name="elem_wnlVitreous" value="<?php echo $elem_wnlVitreous;?>">
<input type="hidden" name="elem_wnlRetinal" value="<?php echo $elem_wnlRetinal;?>">
<input type="hidden" name="elem_wnlDraw" value="<?php echo $elem_wnlDraw;?>">

<input type="hidden" name="elem_posVitreous" value="<?php echo $elem_posVitreous;?>">
<input type="hidden" name="elem_posRetinal" value="<?php echo $elem_posRetinal;?>">
<input type="hidden" name="elem_posDraw" value="<?php echo $elem_posDraw;?>">

<input type="hidden" name="elem_ncVitreous" value="<?php echo $elem_ncVitreous;?>">
<input type="hidden" name="elem_ncRetinal" value="<?php echo $elem_ncRetinal;?>">
<input type="hidden" name="elem_ncDraw" value="<?php echo $elem_ncDraw;?>">
<input type="hidden" name="elem_examined_no_change" value="<?php echo $elem_noChangeRv;?>">

<input type="hidden" name="elem_wnlVitreousOd" value="<?php echo $elem_wnlVitreousOd;?>">
<input type="hidden" name="elem_wnlVitreousOs" value="<?php echo $elem_wnlVitreousOs;?>">
<input type="hidden" name="elem_wnlRetinalOd" value="<?php echo $elem_wnlRetinalOd;?>">
<input type="hidden" name="elem_wnlRetinalOs" value="<?php echo $elem_wnlRetinalOs;?>">

<?php
//if($elem_retina_version=="old"){ //Needed to do calculations for positive
?>
<input type="hidden" name="elem_wnlMacula" value="<?php echo $elem_wnlMacula;?>">
<input type="hidden" name="elem_wnlPeri" value="<?php echo $elem_wnlPeri;?>">
<input type="hidden" name="elem_wnlBV" value="<?php echo $elem_wnlBV;?>">
<input type="hidden" name="elem_posMacula" value="<?php echo $elem_posMacula;?>">
<input type="hidden" name="elem_posPeri" value="<?php echo $elem_posPeri;?>">
<input type="hidden" name="elem_posBV" value="<?php echo $elem_posBV;?>">
<input type="hidden" name="elem_ncMacula" value="<?php echo $elem_ncMacula;?>">
<input type="hidden" name="elem_ncPeri" value="<?php echo $elem_ncPeri;?>">
<input type="hidden" name="elem_ncBV" value="<?php echo $elem_ncBV;?>">
<input type="hidden" name="elem_wnlMaculaOd" value="<?php echo $elem_wnlMaculaOd;?>">
<input type="hidden" name="elem_wnlMaculaOs" value="<?php echo $elem_wnlMaculaOs;?>">
<input type="hidden" name="elem_wnlPeriOd" value="<?php echo $elem_wnlPeriOd;?>">
<input type="hidden" name="elem_wnlPeriOs" value="<?php echo $elem_wnlPeriOs;?>">
<input type="hidden" name="elem_wnlBVOd" value="<?php echo $elem_wnlBVOd;?>">
<input type="hidden" name="elem_wnlBVOs" value="<?php echo $elem_wnlBVOs;?>">
<?php
//}
?>

<input type="hidden" name="elem_wnlDrawOd" value="<?php echo $elem_wnlDrawOd;?>">
<input type="hidden" name="elem_wnlDrawOs" value="<?php echo $elem_wnlDrawOs;?>">
<input type="hidden" name="elem_drawType" id="elem_drawType" value="<?php echo $elem_drawType;?>">

<input type="hidden" name="elem_descRv" value="<?php echo $elem_descRv;?>">
<input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
<input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">

<!-- Optic Nerve -->
<input type="hidden" name="elem_examDateOptic" value="<?php echo $elem_examDateOptic;?>">
<input type="hidden" name="elem_wnlOptic" value="<?php echo $elem_wnlOptic;?>">
<input type="hidden" name="elem_posOptic" value="<?php echo $elem_posOptic;?>">
<input type="hidden" name="elem_wnlOpticOd" value="<?php echo $elem_wnlOpticOd;?>">
<input type="hidden" name="elem_wnlOpticOs" value="<?php echo $elem_wnlOpticOs;?>">
<input type="hidden" name="elem_ncOptic" value="<?php echo $elem_ncOptic;?>">
<!-- Optic Nerve -->

<input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
<!--<input type="hidden" name="hidFundusDrawingId" id="hidFundusDrawingId" value="<?php //echo $dbIdocDrawingId;?>">-->
<input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">

<input type="hidden" id="elem_utElemsVitreous" name="elem_utElemsVitreous" value="<?php echo $elem_utElemsVitreous;?>">
<input type="hidden" id="elem_utElemsVitreous_cur" name="elem_utElemsVitreous_cur" value="<?php echo $elem_utElemsVitreous_cur;?>">
<input type="hidden" id="elem_utElemsMacula" name="elem_utElemsMacula" value="<?php echo $elem_utElemsMacula;?>">
<input type="hidden" id="elem_utElemsMacula_cur" name="elem_utElemsMacula_cur" value="<?php echo $elem_utElemsMacula_cur;?>">
<input type="hidden" id="elem_utElemsPeri" name="elem_utElemsPeri" value="<?php echo $elem_utElemsPeri;?>">
<input type="hidden" id="elem_utElemsPeri_cur" name="elem_utElemsPeri_cur" value="<?php echo $elem_utElemsPeri_cur;?>">
<input type="hidden" id="elem_utElemsBV" name="elem_utElemsBV" value="<?php echo $elem_utElemsBV;?>">
<input type="hidden" id="elem_utElemsBV_cur" name="elem_utElemsBV_cur" value="<?php echo $elem_utElemsBV_cur;?>">
<input type="hidden" id="elem_utElemsDraw" name="elem_utElemsDraw" value="<?php echo $elem_utElemsDraw;?>">
<input type="hidden" id="elem_utElemsDraw_cur" name="elem_utElemsDraw_cur" value="<?php echo $elem_utElemsDraw_cur;?>">
<input type="hidden" id="elem_utElemsOptic" name="elem_utElemsOptic" value="<?php echo $elem_utElemsOptic;?>">
<input type="hidden" id="elem_utElemsOptic_cur" name="elem_utElemsOptic_cur" value="<?php echo $elem_utElemsOptic_cur;?>">
<input type="hidden" id="elem_utElemsRetinal" name="elem_utElemsRetinal" value="<?php echo $elem_utElemsRetinal;?>">
<input type="hidden" id="elem_utElemsRetinal_cur" name="elem_utElemsRetinal_cur" value="<?php echo $elem_utElemsRetinal_cur;?>">

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
		if($key == "6"){
			$tmp2="Optic";
		}else if($key == "3"){
			$tmp2="Peri";
		}else if($key == "4"){
			$tmp2="BV";
		}else if($key == "5"){
			$tmp2="Draw";
		}else if($key == "7"){
			$tmp2="Retinal";
		}else if($key == "8"){
			$tmp2="DrawON";
		}else if($key == "9"){
			$tmp2="DrawMA";
		}else{
			$tmp2=$val;
		}

		//CheckTemplate setting --
		    $chkTemp=$val;
		    if($chkTemp=="Optic Nerve"){ $chkTemp="Opt. Nev";}
		    else if($chkTemp=="Drawing"||$chkTemp=="Draw ON"||$chkTemp=="Draw MA"){ $chkTemp="DrawFundus"; }
        else if($chkTemp=="Vessels"){ $chkTemp="Blood Vessels"; }

		    if(isset($arrTempProc) && !in_array($chkTemp,$arrTempProc) && !in_array("All",$arrTempProc)){
			continue;
		    }
		//CheckTemplate setting --

		//check draw ON, MA --
		$key_org="".$key;
		if($key=="8" || $key=="9"){
			$key="5";
		}
		//check draw ON, MA --

		$tmp = ($key == $defTabKey) ? "active" : "";

		if($val=="Drawing"){ $val="Draw RT"; }

	?>
		<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab"  id="tab<?php echo $key_org;?>" onclick="changeTab('<?php echo $key;?>', this)" data-key_org="<?php echo $key_org; ?>" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

<!-- tbar -->
<div class="row exm_title_bar form-inline">
	<div class="col-sm-1">
	<?php if($finalize_flag == 1){?>
	<label class="chart_status label label-danger pull-left">Finalized</label>
	<?php }?>
	</div>
	<div class="col-sm-3 ">
    <!--Peri not examined -->
	</div>
	<div class="col-sm-3">
		<div class="form-group">
			<label for="el_lens_used">Lens Used:</label>
			<input type="text" class="form-control" id="el_lens_used" name="el_lens_used" value="<?php echo $el_lens_used; ?>">
		</div>
	</div>
  <div class="col-sm-1"></div>
  <div class="col-sm-4"></div>
</div>
<!-- tbar -->

  <!-- Tab panes -->
  <div class="tab-content">
	<!-- Optic.  -->
    <div role="tabpanel" class="tab-pane <?php echo (6 == $defTabKey) ? "active" : "" ?>" id="div6">
    	<div class="examhd form-inline">
    		<div class="row">
    			<div class="col-sm-1">
    			</div>
    			<div class="col-sm-2">
    			</div>
    			<div class="col-sm-2">
    			</div>
    			<div class="col-sm-7">
    			<span id="examFlag" class="glyphicon flagWnl "></span>
    			<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

    			<input type="checkbox" id="elem_noChangeOptic"  name="elem_noChangeOptic" value="1" onClick="setNC2();"
    						<?php echo ($elem_ncOptic == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
    			<label class="lbl_nochange frcb" for="elem_noChangeOptic">NO Change</label>

    			<?php /*if (constant('AV_MODULE')=='YES'){?>
    			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" />
    			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
    			<?php }*/?>
    			</div>
    		</div>
    	</div>
    	<div class="clearfix"> </div>

    	<div class="table-responsive">
    	<table class="table table-bordered table-striped" >

    	<tr>
    	<td colspan="7" align="center">
    		<span class="flgWnl_2" id="flagWnlOd" ></span>
    		<!--<img src="../../library/images/tstod.png" alt=""/>-->
    		<div class="checkboxO"><label class="od cbold">OD</label></div>
    	</td>
    	<td width="67" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
    	<td colspan="7" align="center">
    		<span class="flgWnl_2" id="flagWnlOs" ></span>
    		<!--<img src="../../library/images/tstos.png" alt=""/>-->
    		<div class="checkboxO"><label class="os cbold">OS</label></div>
    	</td>
    	</tr>

    	<tr class="exmhlgcol grp_CD <?php echo $cls_CD; ?>" id="d_CD">
    	<td  >C:D</td>
    	<td colspan="1" >
    		<select name="elem_cdValOd" id="elem_cdValOd" class="ignore4YF form-control" <?php if($blEnableHTMLDrawing == true){ echo "onChange=\"setDrawingCD(this);\""; } ?>>
    		<option value=""></option>
    		<?php
    		$sel="";
    		//echo $elem_cdValOd;
    		for($i="0.0";$i<=1.05;$i+=0.05){
    			if("".$i==$elem_cdValOd){
    				$sel= "selected";
    				$tmp= "selected";
    			}else{
    				$tmp= "";
    			}
    			echo "<option value=\"".$i."\" ".$tmp." >".$i."</option>";
    			if($i=="0.0"){ $i+=0.05; }
    		}

    		if(empty($sel) && !empty($elem_cdValOd)){
    			echo "<option value=\"".$elem_cdValOd."\" selected >".$elem_cdValOd."</option>";
    		}

    		?>
    		</select>
    	</td>
    	<td colspan="5" ><textarea name="elem_cdOd" class="ignore4YF form-control" ><?php echo $elem_cdOd;?></textarea></td>
    	<td rowspan="5" align="center" class="bilat" onClick="check_bl('CD')">BL</td>
    	<td >C:D</td>
    	<td  >
    		<select name="elem_cdValOs" id="elem_cdValOs" class="ignore4YF form-control" <?php if($blEnableHTMLDrawing == true){ echo "onChange=\"setDrawingCD(this);\""; } ?>>
    		<option value=""></option>
    		<?php
    		$sel="";
    		//echo $elem_cdValOs;
    		for($i="0.0";$i<1.05;$i+=0.05){
    			if("".$i==$elem_cdValOs){
    				$sel= "selected";
    				$tmp= "selected";
    			}else{
    				$tmp="";
    			}

    			echo "<option value=\"".$i."\" ".$tmp." >".$i."</option>";
    			if($i=="0.0"){ $i+=0.05; }
    		}

    		if(empty($sel) && !empty($elem_cdValOs)){
    			echo "<option value=\"".$elem_cdValOs."\" selected >".$elem_cdValOs."</option>";
    		}

    		?>
    		</select>
    	</td>
    	<td colspan="5" ><textarea name="elem_cdOs" class="ignore4YF form-control" ><?php echo $elem_cdOs;?></textarea></td>
    	</tr>

    	<tr class="exmhlgcol grp_CD <?php echo $cls_CD; ?>">
    	<td >CUP </td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_cup_small" name="elem_onOd_cup_small" value="Small" class="ignore4YF" <?php echo ($elem_onOd_cup_small == "Small") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_cup_small" >Small</label>
    	</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_cup_moderate" name="elem_onOd_cup_moderate" value="Moderate" class="ignore4YF" <?php echo ($elem_onOd_cup_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_cup_moderate" >Moderate</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_cup_large" name="elem_onOd_cup_large" value="Large" class="ignore4YF" <?php echo ($elem_onOd_cup_large == "Large") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_cup_large" >Large</label>
    	</td>
    	<td >CUP </td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_cup_small" name="elem_onOs_cup_small" value="Small" class="ignore4YF" <?php echo ($elem_onOs_cup_small == "Small") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_cup_small" >Small</label>
    	</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_cup_moderate" name="elem_onOs_cup_moderate" value="Moderate" class="ignore4YF" <?php echo ($elem_onOs_cup_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_cup_moderate" >Moderate</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_cup_large" name="elem_onOs_cup_large" value="Large" class="ignore4YF" <?php echo ($elem_onOs_cup_large == "Large") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_cup_large" >Large</label>
    	</td>
    	</tr>

    	<tr class="exmhlgcol grp_CD <?php echo $cls_CD; ?>">
    	<td >Superior Rim</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_srim_Intact" name="elem_onOd_srim_Intact" value="Intact" class="ignore4YF" <?php echo ($elem_onOd_srim_Intact == "Intact") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_srim_Intact" >Intact</label>
    	</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_srim_Thin" name="elem_onOd_srim_Thin" value="Thin" class="ignore4YF" <?php echo ($elem_onOd_srim_Thin == "Thin") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_srim_Thin" >Thin</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_srim_c2rim" name="elem_onOd_srim_c2rim" value="Cupped to rim" class="ignore4YF" <?php echo ($elem_onOd_srim_c2rim == "Cupped to rim") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_srim_c2rim"  class="cup2rim">Cupped to rim</label>
    	</td>
    	<td >Superior Rim</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_srim_Intact" name="elem_onOs_srim_Intact" value="Intact" class="ignore4YF" <?php echo ($elem_onOs_srim_Intact == "Intact") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_srim_Intact" >Intact</label>
    	</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_srim_Thin" name="elem_onOs_srim_Thin" value="Thin" class="ignore4YF" <?php echo ($elem_onOs_srim_Thin == "Thin") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_srim_Thin" >Thin</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_srim_c2rim" name="elem_onOs_srim_c2rim" value="Cupped to rim" class="ignore4YF" <?php echo ($elem_onOs_srim_c2rim == "Cupped to rim") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_srim_c2rim"  class="cup2rim">Cupped to rim</label>
    	</td>
    	</tr>

    	<tr class="exmhlgcol grp_CD <?php echo $cls_CD; ?>">
    	<td >Inferior Rim</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_irim_Intact" name="elem_onOd_irim_Intact" value="Intact" class="ignore4YF" <?php echo ($elem_onOd_irim_Intact == "Intact") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_irim_Intact" >Intact</label>
    	</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_irim_Thin" name="elem_onOd_irim_Thin" value="Thin" class="ignore4YF" <?php echo ($elem_onOd_irim_Thin == "Thin") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_irim_Thin" >Thin</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOd_irim_c2rim" name="elem_onOd_irim_c2rim" value="Cupped to rim" class="ignore4YF" <?php echo ($elem_onOd_irim_c2rim == "Cupped to rim") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_irim_c2rim"  class="cup2rim">Cupped to rim</label>
    	</td>
    	<td >Inferior Rim</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_irim_Intact" name="elem_onOs_irim_Intact" value="Intact" class="ignore4YF" <?php echo ($elem_onOs_irim_Intact == "Intact") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_irim_Intact" >Intact</label>
    	</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_irim_Thin" name="elem_onOs_irim_Thin" value="Thin" class="ignore4YF" <?php echo ($elem_onOs_irim_Thin == "Thin") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_irim_Thin" >Thin</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'CD');"   id="elem_onOs_irim_c2rim" name="elem_onOs_irim_c2rim" value="Cupped to rim" class="ignore4YF" <?php echo ($elem_onOs_irim_c2rim == "Cupped to rim") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_irim_c2rim"  class="cup2rim">Cupped to rim</label>
    	</td>
    	</tr>

    	<tr class="exmhlgcol grp_CD <?php echo $cls_CD; ?>">
    	<td >Optic Nerve</td>
    	<td class="form-inline optner" colspan="6">
    	at
    	<select name="elem_onOd_onhmg" class="ignore4YF form-control" onchange="checkwnls();checkSymClr(this,'CD');">
    	<option value=""></option>
    	<?php
    	for($i=1;$i<=12;$i++){
    		$t = "at ".$i." o' clock";
    		$sel = (strpos($elem_onOd_onhmg, "$i")!==false) ? "selected" : "";
    		echo "<option value=\"".$t."\"  ".$sel.">".$i."</option>";
    	}
    	?>
    	</select>
    	o' clock
    	</td>
    	<td >Optic Nerve</td>
    	<td class="form-inline optner" colspan="6">
    	at
    	<select name="elem_onOs_onhmg" class="ignore4YF form-control" onchange="checkwnls();checkSymClr(this,'CD');">
    	<option value=""></option>
    	<?php
    	for($i=1;$i<=12;$i++){
    		$t = "at ".$i." o' clock";
    		$sel = (strpos($elem_onOs_onhmg, "$i")!==false) ? "selected" : "";
    		echo "<option value=\"".$t."\"  ".$sel.">".$i."</option>";
    	}
    	?>
    	</select>
    	o' clock
    	</td>
    	</tr>

    	<tr id="d_nrim">
    	<td >Normal Rim</td>
    	<td colspan="2">
    	<input type="checkbox" id="elem_PinkOd_Sharp" name="elem_PinkOd_Sharp" value="Pink Sharp" onClick="checkwnls()"
    			<?php if($elem_PinkOd_Sharp == 'Pink Sharp') echo 'CHECKED'; ?>><label for="elem_PinkOd_Sharp"  class="pinknshrp">Pink&nbsp;&&nbsp;Sharp</label>
    	</td><td colspan="4"><input type="checkbox" id="elem_OdSVP" name="elem_OdSVP" value="+SVP" onClick="checkwnls()" <?php if($elem_OdSVP == '+SVP') echo 'CHECKED'; ?>><label for="elem_OdSVP" >+SVP</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('nrim')">BL</td>
    	<td >Normal Rim</td>
    	<td colspan="2">
    	<input type="checkbox" id="elem_PinkOs_Sharp" name="elem_PinkOs_Sharp" value="Pink Sharp" onClick="checkwnls()"
    			<?php if($elem_PinkOs_Sharp == 'Pink Sharp') echo 'CHECKED'; ?>><label for="elem_PinkOs_Sharp"  class="pinknshrp">Pink&nbsp;&&nbsp;Sharp</label>
    	</td><td colspan="4"><input type="checkbox" id="elem_OsSVP" name="elem_OsSVP" value="+SVP" onClick="checkwnls()" <?php if($elem_OsSVP == '+SVP') echo 'CHECKED'; ?>><label for="elem_OsSVP" >+SVP</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Normal Rim"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Normal Rim"]; }  ?>

    	<tr id="d_onhmg">
    	<td >Optic Nerve Hmg</td>
    	<td>
    	<input type="checkbox" id="elem_negOd_normal" name="elem_negOd_normal" value="Absent" onClick="checkAbsent(this)" <?php if($elem_negOd_normal == "-ve" || $elem_negOd_normal == "Absent") echo 'CHECKED'; ?>><label for="elem_negOd_normal" >Absent</label>
    	</td><td colspan="5"><input type="checkbox" id="elem_posOd_normal" name="elem_posOd_normal" value="Present" onClick="checkAbsent(this)" <?php if($elem_posOd_normal == "+ve" || $elem_posOd_normal == "Present") echo 'CHECKED'; ?>><label for="elem_posOd_normal" >Present</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('onhmg')">BL</td>
    	<td >Optic Nerve Hmg</td>
    	<td>
    	<input type="checkbox" id="elem_negOs_normal" name="elem_negOs_normal" value="Absent" onClick="checkAbsent(this)" <?php if($elem_negOs_normal == "-ve" || $elem_negOs_normal == "Absent") echo 'CHECKED'; ?>><label for="elem_negOs_normal" >Absent</label>
    	</td><td colspan="5"><input type="checkbox" id="elem_posOs_normal" name="elem_posOs_normal" value="Present" onClick="checkAbsent(this)" <?php if($elem_posOs_normal == "+ve" || $elem_posOs_normal == "Present") echo 'CHECKED'; ?>><label for="elem_posOs_normal" >Present</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Optic Nerve Hmg"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Optic Nerve Hmg"]; }  ?>

    	<tr id="d_Slopping" class="grp_Slopping">
    	<td >Sloping</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_Mild" name="elem_sloppingOd_Mild" value="Mild" <?php echo ($elem_sloppingOd_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_Mild" >Mild</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_Moderate"  name="elem_sloppingOd_Moderate" value="Moderate" <?php echo ($elem_sloppingOd_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_Moderate" >Moderate</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_Severe" name="elem_sloppingOd_Severe" value="Severe" <?php echo ($elem_sloppingOd_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_Severe" >Severe</label>
    	</td>
    	<td align="center" class="bilat" rowspan="2" onClick="check_bl('Slopping')">BL</td>
    	<td >Sloping</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_Mild" name="elem_sloppingOs_Mild" value="Mild"
    	<?php echo ($elem_sloppingOs_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_Moderate" name="elem_sloppingOs_Moderate" value="Moderate"
    	<?php echo ($elem_sloppingOs_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_Moderate" >Moderate</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_Severe" name="elem_sloppingOs_Severe" value="Severe"
    	<?php echo ($elem_sloppingOs_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_Severe" >Severe</label>
    	</td>
    	</tr>

    	<tr class="grp_Slopping" >
    	<td ></td>
    	<td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_superior" name="elem_sloppingOd_superior" value="Superior" <?php echo ($elem_sloppingOd_superior == "Superior") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_superior" >Superior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_inferior" name="elem_sloppingOd_inferior" value="Inferior" <?php echo ($elem_sloppingOd_inferior == "Inferior") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_inferior" >Inferior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_nasal" name="elem_sloppingOd_nasal" value="Nasal" <?php echo ($elem_sloppingOd_nasal == "Nasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_nasal" >Nasal</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOd_temporal" name="elem_sloppingOd_temporal" value="Temporal" <?php echo ($elem_sloppingOd_temporal == "Temporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOd_temporal" >Temporal</label>
    	</td>

    	<td ></td>
    	<td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_superior" name="elem_sloppingOs_superior" value="Superior"
    	<?php echo ($elem_sloppingOs_superior == "Superior") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_superior" >Superior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_inferior" name="elem_sloppingOs_inferior" value="Inferior"
    	<?php echo ($elem_sloppingOs_inferior == "Inferior") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_inferior" >Inferior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_nasal" name="elem_sloppingOs_nasal" value="Nasal"
    	<?php echo ($elem_sloppingOs_nasal == "Nasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_nasal" >Nasal</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_sloppingOs_temporal" name="elem_sloppingOs_temporal" value="Temporal"
    	<?php echo ($elem_sloppingOs_temporal == "Temporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_sloppingOs_temporal" >Temporal</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Sloping"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Sloping"]; }  ?>

    	<tr id="d_Notch" class="grp_Notch">
    	<td >Notch</td>
    	<td>
    	<input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_Mild" name="elem_notchOd_Mild" value="Mild" <?php echo ($elem_notchOd_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_Mild" >Mild</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_Moderate" name="elem_notchOd_Moderate" value="Moderate" <?php echo ($elem_notchOd_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_Moderate" >Moderate</label>
    	</td><td colspan="4"><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_Severe" name="elem_notchOd_Severe" value="Severe" <?php echo ($elem_notchOd_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_Severe" >Severe</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('Notch')" rowspan="2">BL</td>
    	<td >Notch</td>
    	<td>
    	<input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_Mild" name="elem_notchOs_Mild" value="Mild" <?php echo ($elem_notchOs_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_Mild" >Mild</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_Moderate" name="elem_notchOs_Moderate" value="Moderate" <?php echo ($elem_notchOs_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_Moderate" >Moderate</label>
    	</td><td colspan="4"><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_Severe" name="elem_notchOs_Severe" value="Severe" <?php echo ($elem_notchOs_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_Severe" >Severe</label>
    	</td>
    	</tr>

    	<tr class="grp_Notch">
    	<td ></td>
    	<td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_superior" name="elem_notchOd_superior" value="Superior" <?php echo ($elem_notchOd_superior == "Superior") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_superior" >Superior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_inferior" name="elem_notchOd_inferior" value="Inferior" <?php echo ($elem_notchOd_inferior == "Inferior") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_inferior" >Inferior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_nasal" name="elem_notchOd_nasal" value="Nasal" <?php echo ($elem_notchOd_nasal == "Nasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_nasal" >Nasal</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOd_temporal" name="elem_notchOd_temporal" value="Temporal" <?php echo ($elem_notchOd_temporal == "Temporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOd_temporal" >Temporal</label>
    	</td>
    	<td ></td>
    	<td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_superior" name="elem_notchOs_superior" value="Superior" <?php echo ($elem_notchOs_superior == "Superior") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_superior" >Superior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_inferior" name="elem_notchOs_inferior" value="Inferior" <?php echo ($elem_notchOs_inferior == "Inferior") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_inferior" >Inferior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_nasal" name="elem_notchOs_nasal" value="Nasal" <?php echo ($elem_notchOs_nasal == "Nasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_nasal" >Nasal</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_notchOs_temporal" name="elem_notchOs_temporal" value="Temporal" <?php echo ($elem_notchOs_temporal == "Temporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_notchOs_temporal" >Temporal</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Notch"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Notch"]; }  ?>

    	<tr id="d_Pallor" class="grp_Pallor">
    	<td >Pallor</td>
    	<td>
    	<input type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_mild" name="elem_pallorOd_mild" value="Mild" <?php echo ($elem_pallorOd_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_moderate" name="elem_pallorOd_moderate" value="Moderate" <?php echo ($elem_pallorOd_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_moderate" >Moderate</label>
    	</td><td colspan="4"><input type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_severe" name="elem_pallorOd_severe" value="Severe" <?php echo ($elem_pallorOd_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_severe" >Severe</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('Pallor')" rowspan="2">BL</td>
    	<td >Pallor</td>
    	<td>
    	<input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_mild" name="elem_pallorOs_mild" value="Mild" <?php echo ($elem_pallorOs_mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_moderate" name="elem_pallorOs_moderate" value="Moderate" <?php echo ($elem_pallorOs_moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_moderate" >Moderate</label>
    	</td><td colspan="4"><input   type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_severe" name="elem_pallorOs_severe" value="Severe" <?php echo ($elem_pallorOs_severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_severe" >Severe</label>
    	</td>
    	</tr>

    	<tr class="grp_Pallor">
    	<td ></td>
    	<td><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_superior" name="elem_pallorOd_superior" value="Superior" <?php echo ($elem_pallorOd_superior == "Superior") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_superior" >Superior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_inferior" name="elem_pallorOd_inferior" value="Inferior" <?php echo ($elem_pallorOd_inferior == "Inferior") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_inferior" >Inferior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_nasal" name="elem_pallorOd_nasal" value="Nasal" <?php echo ($elem_pallorOd_nasal == "Nasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_nasal" >Nasal</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOd_temporal" name="elem_pallorOd_temporal" value="Temporal" <?php echo ($elem_pallorOd_temporal == "Temporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOd_temporal" >Temporal</label>
    	</td>
    	<td ></td>
    	<td><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_superior" name="elem_pallorOs_superior" value="Superior" <?php echo ($elem_pallorOs_superior == "Superior") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_superior" >Superior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_inferior" name="elem_pallorOs_inferior" value="Inferior" <?php echo ($elem_pallorOs_inferior == "Inferior") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_inferior" >Inferior</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_nasal" name="elem_pallorOs_nasal" value="Nasal" <?php echo ($elem_pallorOs_nasal == "Nasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_nasal" >Nasal</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_pallorOs_temporal" name="elem_pallorOs_temporal" value="Temporal" <?php echo ($elem_pallorOs_temporal == "Temporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_pallorOs_temporal" >Temporal</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Pallor"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Pallor"]; }  ?>

    	<tr id="d_nApp">
    	<td >Nerve Appearance</td>
    	<td>
    	<input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOd_Small" name="elem_nerveAppOd_Small" value="Small" <?php echo ($elem_nerveAppOd_Small == "Small") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOd_Small" >Small</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOd_Medium" name="elem_nerveAppOd_Medium" value="Medium" <?php echo ($elem_nerveAppOd_Medium == "Medium") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOd_Medium" >Medium</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOd_Large" name="elem_nerveAppOd_Large" value="Large" <?php echo ($elem_nerveAppOd_Large == "Large") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOd_Large" >Large</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOd_TiltedDisc" name="elem_nerveAppOd_TiltedDisc" value="Tilted Disc" <?php echo ($elem_nerveAppOd_TiltedDisc == "Tilted Disc") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOd_TiltedDisc"  class="tiltdisc">Tilted Disc</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('nApp')">BL</td>
    	<td >Nerve Appearance</td>
    	<td>
    	<input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOs_Small" name="elem_nerveAppOs_Small" value="Small" <?php echo ($elem_nerveAppOs_Small == "Small") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOs_Small" >Small</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOs_Medium" name="elem_nerveAppOs_Medium" value="Medium" <?php echo ($elem_nerveAppOs_Medium == "Medium") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOs_Medium" >Medium</label>
    	</td><td><input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOs_Large" name="elem_nerveAppOs_Large" value="Large" <?php echo ($elem_nerveAppOs_Large == "Large") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOs_Large" >Large</label>
    	</td><td colspan="3"><input  type="checkbox"  onclick="checkwnls()"  id="elem_nerveAppOs_TiltedDisc" name="elem_nerveAppOs_TiltedDisc" value="Tilted Disc" <?php echo ($elem_nerveAppOs_TiltedDisc == "Tilted Disc") ? "checked=\"checked\"" : "" ;?>><label for="elem_nerveAppOs_TiltedDisc"  class="tiltdisc">Tilted Disc</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Nerve Appearance"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Nerve Appearance"]; }  ?>

    	<tr id="d_PPA">
    	<td >Peri-papillary atrophy</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOd_negative" name="elem_ppAtrophyOd_negative" value="Absent" <?php echo ($elem_ppAtrophyOd_negative == "-ve" || $elem_ppAtrophyOd_negative == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOd_negative" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOd_positive" name="elem_ppAtrophyOd_positive" value="Present" <?php echo ($elem_ppAtrophyOd_positive == "+ve" || $elem_ppAtrophyOd_positive == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOd_positive" >Present</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOd_Mild" name="elem_ppAtrophyOd_Mild" value="Mild" <?php echo ($elem_ppAtrophyOd_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOd_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOd_Moderate" name="elem_ppAtrophyOd_Moderate" value="Moderate" <?php echo ($elem_ppAtrophyOd_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOd_Moderate" >Moderate</label>
    	</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOd_Severe" name="elem_ppAtrophyOd_Severe" value="Severe" <?php echo ($elem_ppAtrophyOd_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOd_Severe" >Severe</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('PPA')">BL</td>
    	<td >Peri-papillary atrophy</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOs_negative" name="elem_ppAtrophyOs_negative" value="Absent" <?php echo ($elem_ppAtrophyOs_negative == "-ve" || $elem_ppAtrophyOs_negative == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOs_negative" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOs_positive" name="elem_ppAtrophyOs_positive" value="Present" <?php echo ($elem_ppAtrophyOs_positive == "+ve" || $elem_ppAtrophyOs_positive == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOs_positive" >Present</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOs_Mild" name="elem_ppAtrophyOs_Mild" value="Mild" <?php echo ($elem_ppAtrophyOs_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOs_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOs_Moderate" name="elem_ppAtrophyOs_Moderate" value="Moderate" <?php echo ($elem_ppAtrophyOs_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOs_Moderate" >Moderate</label>
    	</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_ppAtrophyOs_Severe" name="elem_ppAtrophyOs_Severe" value="Severe" <?php echo ($elem_ppAtrophyOs_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_ppAtrophyOs_Severe" >Severe</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Peri-papillary atrophy"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Peri-papillary atrophy"]; }  ?>

    	<tr id="d_Edm">
    	<td >Edema</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOd_Absent" name="elem_edemaOd_Absent" value="Absent" <?php echo ($elem_edemaOd_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOd_Absent" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOd_Present" name="elem_edemaOd_Present" value="Present" <?php echo ($elem_edemaOd_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOd_Present" >Present</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOd_Mild" name="elem_edemaOd_Mild" value="Mild" <?php echo ($elem_edemaOd_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOd_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOd_Moderate" name="elem_edemaOd_Moderate" value="Moderate" <?php echo ($elem_edemaOd_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOd_Moderate" >Moderate</label>
    	</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOd_Severe" name="elem_edemaOd_Severe" value="Severe" <?php echo ($elem_edemaOd_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOd_Severe" >Severe</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('Edm')">BL</td>
    	<td >Edema</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOs_Absent" name="elem_edemaOs_Absent" value="Absent" <?php echo ($elem_edemaOs_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOs_Absent" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOs_Present" name="elem_edemaOs_Present" value="Present" <?php echo ($elem_edemaOs_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOs_Present" >Present</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOs_Mild" name="elem_edemaOs_Mild" value="Mild" <?php echo ($elem_edemaOs_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOs_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOs_Moderate" name="elem_edemaOs_Moderate" value="Moderate" <?php echo ($elem_edemaOs_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOs_Moderate" >Moderate</label>
    	</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_edemaOs_Severe" name="elem_edemaOs_Severe" value="Severe" <?php echo ($elem_edemaOs_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_edemaOs_Severe" >Severe</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Edema"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Edema"]; }  ?>

    	<tr id="d_NVasc">
    	<td >Neo vascularization</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOd_Absent" name="elem_nvascOd_Absent" value="Absent" <?php echo ($elem_nvascOd_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOd_Absent" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOd_Present" name="elem_nvascOd_Present" value="Present" <?php echo ($elem_nvascOd_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOd_Present" >Present</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOd_Mild" name="elem_nvascOd_Mild" value="Mild" <?php echo ($elem_nvascOd_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOd_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOd_Moderate" name="elem_nvascOd_Moderate" value="Moderate" <?php echo ($elem_nvascOd_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOd_Moderate" >Moderate</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOd_Severe" name="elem_nvascOd_Severe" value="Severe" <?php echo ($elem_nvascOd_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOd_Severe" >Severe</label>
    	</td>
    	<td >&nbsp;</td>
    	<td align="center" class="bilat" onClick="check_bl('NVasc')">BL</td>
    	<td >Neo vascularization</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOs_Absent" name="elem_nvascOs_Absent" value="Absent" <?php echo ($elem_nvascOs_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOs_Absent" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOs_Present" name="elem_nvascOs_Present" value="Present" <?php echo ($elem_nvascOs_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOs_Present" >Present</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOs_Mild" name="elem_nvascOs_Mild" value="Mild" <?php echo ($elem_nvascOs_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOs_Mild" >Mild</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOs_Moderate" name="elem_nvascOs_Moderate" value="Moderate" <?php echo ($elem_nvascOs_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOs_Moderate" >Moderate</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_nvascOs_Severe" name="elem_nvascOs_Severe" value="Severe" <?php echo ($elem_nvascOs_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_nvascOs_Severe" >Severe</label>
    	</td>
    	<td >&nbsp;</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Neo vascularization"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Neo vascularization"]; }  ?>

    	<tr id="d_onDrusen" class="grp_onDrusen">
    	<td >Drusen</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_Absent" name="elem_onOd_drusen_Absent" value="Absent"
    	<?php echo ($elem_onOd_drusen_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_Absent" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_T" name="elem_onOd_drusen_T" value="T"
    	<?php echo ($elem_onOd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_T" >T</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_pos1" name="elem_onOd_drusen_pos1" value="1+"
    	<?php echo ($elem_onOd_drusen_pos1 == "+1" || $elem_onOd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_pos1" >1+</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_pos2" name="elem_onOd_drusen_pos2" value="2+"
    	<?php echo ($elem_onOd_drusen_pos2 == "+2" || $elem_onOd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_pos2" >2+</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_pos3" name="elem_onOd_drusen_pos3" value="3+"
    	<?php echo ($elem_onOd_drusen_pos3 == "+3" || $elem_onOd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_pos3" >3+</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_pos4" name="elem_onOd_drusen_pos4" value="4+"
    	<?php echo ($elem_onOd_drusen_pos4 == "+4" || $elem_onOd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_pos4" >4+</label>
    	</td>
    	<td align="center" class="bilat" onClick="check_bl('onDrusen')" rowspan='2'>BL</td>
    	<td >Drusen</td>
    	<td>
    	<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_Absent" name="elem_onOs_drusen_Absent" value="Absent"
    	<?php echo ($elem_onOs_drusen_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_Absent" >Absent</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_T" name="elem_onOs_drusen_T" value="T"
    	<?php echo ($elem_onOs_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_T" >T</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_pos1" name="elem_onOs_drusen_pos1" value="1+"
    	<?php echo ($elem_onOs_drusen_pos1 == "+1" || $elem_onOs_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_pos1" >1+</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_pos2" name="elem_onOs_drusen_pos2" value="2+"
    	<?php echo ($elem_onOs_drusen_pos2 == "+2" || $elem_onOs_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_pos2" >2+</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_pos3" name="elem_onOs_drusen_pos3" value="3+"
    	<?php echo ($elem_onOs_drusen_pos3 == "+3" || $elem_onOs_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_pos3" >3+</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_pos4" name="elem_onOs_drusen_pos4" value="4+"
    	<?php echo ($elem_onOs_drusen_pos4 == "+4" || $elem_onOs_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_pos4" >4+</label>
    	</td>
    	</tr>


    	<tr id="d_onDrusen1" class="grp_onDrusen">
    	<td ></td>
    	<td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_F" name="elem_onOd_drusen_F" value="F"
    	<?php echo ($elem_onOd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_F" >F</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_foveal" name="elem_onOd_drusen_foveal" value="PF"
    	<?php echo ($elem_onOd_drusen_foveal == "PF") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_foveal" >PF</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_EF" name="elem_onOd_drusen_EF" value="EF"
    	<?php echo ($elem_onOd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_EF" >EF</label>
    	</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOd_drusen_hard" name="elem_onOd_drusen_hard" value="Hard"
    	<?php echo ($elem_onOd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOd_drusen_hard" >Hard</label>
    	</td>
    	<td ></td>
    	<td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_F" name="elem_onOs_drusen_F" value="F"
    	<?php echo ($elem_onOs_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_F" >F</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_foveal" name="elem_onOs_drusen_foveal" value="PF"
    	<?php echo ($elem_onOs_drusen_foveal == "PF") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_foveal" >PF</label>
    	</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_EF" name="elem_onOs_drusen_EF" value="EF"
    	<?php echo ($elem_onOs_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_EF" >EF</label>
    	</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_onOs_drusen_hard" name="elem_onOs_drusen_hard" value="Hard"
    	<?php echo ($elem_onOs_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_onOs_drusen_hard" >Hard</label>
    	</td>
    	</tr>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Drusen"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Drusen"]; }  ?>
    	<?php if(isset($arr_exm_ext_htm["Optic Nerve"]["Main"])){ echo $arr_exm_ext_htm["Optic Nerve"]["Main"]; }  ?>

    	<tr id="d_adOpt_optic">
    	<td >Comments</td>
    	<td colspan="6" ><textarea  onblur="checkwnls()" name="elem_opticNerveDiscAdOptionsOd" class="form-control"><?php echo ($elem_opticNerveDiscAdOptionsOd);?></textarea></td>
    	<td align="center" class="bilat" onClick="check_bl('adOpt_optic')">BL</td>
    	<td >Comments</td>
    	<td colspan="6"><textarea  onblur="checkwnls()" name="elem_opticNerveDiscAdOptionsOs" class="form-control"><?php echo ($elem_opticNerveDiscAdOptionsOs);?></textarea></td>
    	</tr>

    	<!--
    	<tr >
    	<td >X</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td align="center" class="bilat">BL</td>
    	<td >X</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	<td >&nbsp;</td>
    	</tr>
    	-->


    	</table>
    	</div>
    </div>
	<!-- Optic.  -->
	<!-- Vitreous.  -->
  <div role="tabpanel" class="tab-pane <?php echo (1 == $defTabKey) ? "active" : "" ?>" id="div1">
    	<div class="examhd form-inline">
    		<div class="row">
    			<div class="col-sm-1">
    			</div>
    			<div class="col-sm-2">
    			</div>
    			<div class="col-sm-2">
    			</div>
    			<div class="col-sm-7">
    				<span id="examFlag" class="glyphicon flagWnl "></span>
    				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

    				<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();"
    							<?php echo ($elem_ncVitreous == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
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
    		<td colspan="8" align="center">
    			<span class="flgWnl_2" id="flagWnlOd" ></span>
    			<!--<img src="../../library/images/tstod.png" alt=""/>-->
    			<div class="checkboxO"><label class="od cbold">OD</label></div>
    		</td>
    		<td width="67" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
    		<td colspan="8" align="center">
    			<span class="flgWnl_2" id="flagWnlOs" ></span>
    			<!--<img src="../../library/images/tstos.png" alt=""/>-->
    			<div class="checkboxO"><label class="os cbold">OS</label></div>
    		</td>
    		</tr>

    		<tr id="d_Hemorrhage">
    		<td >Hemorrhage</td>
    		<td>
    		<input id="od_1" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_hemorr_neg" value="Absent"
    			<?php echo ($elem_vitOd_hemorr_neg == "-ve" || $elem_vitOd_hemorr_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label  for="od_1" >Absent</label>
    		</td><td><input id="od_2" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_hemorr_pos" value="Present"
    			<?php echo ($elem_vitOd_hemorr_pos == "+ve" || $elem_vitOd_hemorr_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label  for="od_2" >Present</label>
    		</td><td><input id="od_3" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_hemorr_Mild" value="Mild"
    			<?php echo ($elem_vitOd_hemorr_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label  for="od_3" >Mild</label>
    		</td><td><input id="od_4" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_hemorr_Moderate" value="Moderate"
    			<?php echo ($elem_vitOd_hemorr_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label  for="od_4" >Moderate</label>
    		</td><td colspan="3"><input id="od_5" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_hemorr_Severe" value="Severe"
    			<?php echo ($elem_vitOd_hemorr_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label  for="od_5" >Severe</label>
    		</td>
    		<td align="center" class="bilat" onClick="check_bl('Hemorrhage')">BL</td>
    		<td >Hemorrhage</td>
    		<td>
    		<input id="os_1" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_hemorr_neg" value="Absent"
    				<?php echo ($elem_vitOs_hemorr_neg == "-ve" || $elem_vitOs_hemorr_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label  for="os_1" >Absent</label>
    		</td><td><input id="os_2" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_hemorr_pos" value="Present"
    				<?php echo ($elem_vitOs_hemorr_pos == "+ve" || $elem_vitOs_hemorr_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label  for="os_2" >Present</label>
    		</td><td><input id="os_3" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_hemorr_Mild" value="Mild"
    				<?php echo ($elem_vitOs_hemorr_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label  for="os_3" >Mild</label>
    		</td><td><input id="os_4" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_hemorr_Moderate" value="Moderate"
    				<?php echo ($elem_vitOs_hemorr_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label  for="os_4" >Moderate</label>
    		</td><td colspan="3"><input id="os_5" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_hemorr_Severe" value="Severe"
    				<?php echo ($elem_vitOs_hemorr_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label  for="os_5" >Severe</label>
    		</td>
    		</tr>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["Hemorrhage"])){ echo $arr_exm_ext_htm["Vitreous"]["Hemorrhage"]; }  ?>

    		<tr id="d_PVD">
    		<td >PVD</td>
    		<td>
    		<input id="od_7" type="checkbox"  onclick="checkAbsent(this)" name="elem_vitOd_pvd_neg" value="Absent"
    			<?php echo ($elem_vitOd_pvd_neg == "-ve" || $elem_vitOd_pvd_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label  for="od_7" >Absent</label>
    		</td><td><input id="od_8"  type="checkbox"  onclick="checkAbsent(this)" name="elem_vitOd_pvd_pos" value="Present"
    			<?php echo ($elem_vitOd_pvd_pos == "+ve" || $elem_vitOd_pvd_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="od_8">Present</label>
    		</td><td colspan="5"><input id="od_9"  type="checkbox"  onclick="checkAbsent(this)" name="elem_vitOd_pvd_PWR" value="Prominent weiss ring"
    			<?php echo ($elem_vitOd_pvd_PWR == "Prominent weiss ring") ? "checked=\"checked\"" : "" ;?>><label for="od_9" >Prominent weiss ring</label>
    		</td>
    		<td align="center" class="bilat" onClick="check_bl('PVD')">BL</td>
    		<td >PVD</td>
    		<td>
    		<input id="os_7"  type="checkbox"  onclick="checkAbsent(this)" name="elem_vitOs_pvd_neg" value="Absent"
    			<?php echo ($elem_vitOs_pvd_neg == "-ve" || $elem_vitOs_pvd_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_7">Absent</label>
    		</td><td><input id="os_8"  type="checkbox"  onclick="checkAbsent(this)" name="elem_vitOs_pvd_pos" value="Present"
    			<?php echo ($elem_vitOs_pvd_pos == "+ve" || $elem_vitOs_pvd_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="os_8">Present</label>
    		</td><td colspan="5"><input id="os_9"  type="checkbox"  onclick="checkAbsent(this)" name="elem_vitOs_pvd_PWR" value="Prominent weiss ring"
    			<?php echo ($elem_vitOs_pvd_PWR == "Prominent weiss ring") ? "checked=\"checked\"" : "" ;?>><label for="os_9" >Prominent weiss ring</label>
    		</td>
    		</tr>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["PVD"])){ echo $arr_exm_ext_htm["Vitreous"]["PVD"]; }  ?>

    		<tr id="d_ast_hy">
    		<td >Asteroid hyalosis</td>
    		<td>
    		<input id="od_13" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_astHya_neg" value="Absent"
    		<?php echo ($elem_vitOd_astHya_neg == "-ve" || $elem_vitOd_astHya_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_13">Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_astHya_pos" name="elem_vitOd_astHya_pos" value="Present"
    		<?php echo ($elem_vitOd_astHya_pos == "+ve" || $elem_vitOd_astHya_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_astHya_pos">Present</label>
    		</td><td><input id="od_14" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_astHya_T" value="T"
    		<?php echo ($elem_vitOd_astHya_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="od_14"> T</label>
    		</td><td><input id="od_15" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_astHya_pos1" value="1+"
    		<?php echo ($elem_vitOd_astHya_pos1 == "+1" || $elem_vitOd_astHya_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="od_15" >1+</label>
    		</td><td><input id="od_16"  type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_astHya_pos2" value="2+"
    		<?php echo ($elem_vitOd_astHya_pos2 == "+2" || $elem_vitOd_astHya_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="od_16" >2+</label>
    		</td><td><input id="od_17" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_astHya_pos3" value="3+"
    		<?php echo ($elem_vitOd_astHya_pos3 == "+3" || $elem_vitOd_astHya_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="od_17" >3+</label>
    		</td><td><input id="od_18" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_astHya_pos4" value="4+"
    		<?php echo ($elem_vitOd_astHya_pos4 == "+4" || $elem_vitOd_astHya_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="od_18" >4+</label>
    		</td>
    		<td align="center" class="bilat" onClick="check_bl('ast_hy')">BL</td>
    		<td >Asteroid hyalosis</td>
    		<td>
    		<input id="os_13" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_astHya_neg" value="Absent"
    		<?php echo ($elem_vitOs_astHya_neg == "-ve" || $elem_vitOs_astHya_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_13" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"  id="elem_vitOs_astHya_pos"  name="elem_vitOs_astHya_pos" value="Present"
    		<?php echo ($elem_vitOs_astHya_pos == "+ve" || $elem_vitOs_astHya_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_astHya_pos" >Present</label>
    		</td><td><input id="os_14" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_astHya_T" value="T"
    		<?php echo ($elem_vitOs_astHya_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="os_14" >T</label>
    		</td><td><input id="os_15" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_astHya_pos1" value="1+"
    		<?php echo ($elem_vitOs_astHya_pos1 == "+1" || $elem_vitOs_astHya_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="os_15" >1+</label>
    		</td><td><input id="os_16" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_astHya_pos2" value="2+"
    		<?php echo ($elem_vitOs_astHya_pos2 == "+2" || $elem_vitOs_astHya_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="os_16" >2+</label>
    		</td><td><input id="os_17" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_astHya_pos3" value="3+"
    		<?php echo ($elem_vitOs_astHya_pos3 == "+3" || $elem_vitOs_astHya_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="os_17" >3+</label>
    		</td><td><input id="os_18" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_astHya_pos4" value="4+"
    		<?php echo ($elem_vitOs_astHya_pos4 == "+4" || $elem_vitOs_astHya_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="os_18" >4+</label>
    		</td>
    		</tr>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["Asteroid hyalosis"])){ echo $arr_exm_ext_htm["Vitreous"]["Asteroid hyalosis"]; }  ?>

    		<tr id="d_vit_cell">
    		<td >Vitreous Cells</td>
    		<td>
    		<input id="od_19" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_vitreCell_neg" value="Absent"
    			<?php echo ($elem_vitOd_vitreCell_neg == "-ve" || $elem_vitOd_vitreCell_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_19" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_vitreCell_pos" name="elem_vitOd_vitreCell_pos" value="Present"
    			<?php echo ($elem_vitOd_vitreCell_pos == "+ve" || $elem_vitOd_vitreCell_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_vitreCell_pos" >Present</label>
    		</td><td><input id="od_20" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_vitreCell_T" value="T"
    			<?php echo ($elem_vitOd_vitreCell_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="od_20" >T</label>
    		</td><td><input id="od_21" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_vitreCell_pos1" value="1+"
    			<?php echo ($elem_vitOd_vitreCell_pos1 == "+1" || $elem_vitOd_vitreCell_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="od_21" >1+</label>
    		</td><td><input id="od_22" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_vitreCell_pos2" value="2+"
    			<?php echo ($elem_vitOd_vitreCell_pos2 == "+2" || $elem_vitOd_vitreCell_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="od_22" >2+</label>
    		</td><td><input id="od_23" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_vitreCell_pos3" value="3+"
    			<?php echo ($elem_vitOd_vitreCell_pos3 == "+3" || $elem_vitOd_vitreCell_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="od_23" >3+</label>
    		</td><td><input id="od_24" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_vitreCell_pos4" value="4+"
    			<?php echo ($elem_vitOd_vitreCell_pos4 == "+4" || $elem_vitOd_vitreCell_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="od_24" >4+</label>
    		</td>
    		<td align="center" class="bilat" onClick="check_bl('vit_cell')">BL</td>
    		<td >Vitreous Cells</td>
    		<td>
    		<input id="os_19" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_vitreCell_neg" value="Absent"
    			<?php echo ($elem_vitOs_vitreCell_neg == "-ve" || $elem_vitOs_vitreCell_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_19" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_vitreCell_pos" name="elem_vitOs_vitreCell_pos" value="Present"
    			<?php echo ($elem_vitOs_vitreCell_pos == "+ve" || $elem_vitOs_vitreCell_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_vitreCell_pos" >Present</label>
    		</td><td><input id="os_20" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_vitreCell_T" value="T"
    			<?php echo ($elem_vitOs_vitreCell_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="os_20" >T</label>
    		</td><td><input id="os_21" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_vitreCell_pos1" value="1+"
    			<?php echo ($elem_vitOs_vitreCell_pos1 == "+1" || $elem_vitOs_vitreCell_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="os_21" >1+</label>
    		</td><td><input id="os_22" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_vitreCell_pos2" value="2+"
    			<?php echo ($elem_vitOs_vitreCell_pos2 == "+2" || $elem_vitOs_vitreCell_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="os_22" >2+</label>
    		</td><td><input id="os_23" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_vitreCell_pos3" value="3+"
    			<?php echo ($elem_vitOs_vitreCell_pos3 == "+3" || $elem_vitOs_vitreCell_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="os_23" >3+</label>
    		</td><td><input id="os_24" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_vitreCell_pos4" value="4+"
    			<?php echo ($elem_vitOs_vitreCell_pos4 == "+4" || $elem_vitOs_vitreCell_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="os_24" >4+</label>
    		</td>
    		</tr>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["Vitreous Cells"])){ echo $arr_exm_ext_htm["Vitreous"]["Vitreous Cells"]; }  ?>

    		<tr id="d_pig_deb">
    		<td >Pigment</td>
    		<td>
    		<input id="od_25" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_pigDeb_neg" value="Absent"
    			<?php echo ($elem_vitOd_pigDeb_neg == "-ve" || $elem_vitOd_pigDeb_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_25" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_pigDeb_pos" name="elem_vitOd_pigDeb_pos" value="Present"
    			<?php echo ($elem_vitOd_pigDeb_pos == "+ve" || $elem_vitOd_pigDeb_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_pigDeb_pos" >Present</label>
    		</td><td><input id="od_26" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_pigDeb_T" value="T"
    			<?php echo ($elem_vitOd_pigDeb_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="od_26" >T</label>
    		</td><td><input id="od_27" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_pigDeb_pos1" value="1+"
    			<?php echo ($elem_vitOd_pigDeb_pos1 == "+1" || $elem_vitOd_pigDeb_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="od_27" >1+</label>
    		</td><td><input id="od_28" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_pigDeb_pos2" value="2+"
    			<?php echo ($elem_vitOd_pigDeb_pos2 == "+2" || $elem_vitOd_pigDeb_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="od_28" >2+</label>
    		</td><td><input id="od_29" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_pigDeb_pos3" value="3+"
    			<?php echo ($elem_vitOd_pigDeb_pos3 == "+3" || $elem_vitOd_pigDeb_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="od_29" >3+</label>
    		</td><td><input id="od_30" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOd_pigDeb_pos4" value="4+"
    			<?php echo ($elem_vitOd_pigDeb_pos4 == "+4" || $elem_vitOd_pigDeb_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="od_30" >4+</label>

    		</td>
    		<td align="center" class="bilat" onClick="check_bl('pig_deb')">BL</td>
    		<td >Pigment</td>
    		<td>
    		<input id="os_25" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_pigDeb_neg" value="Absent"
    			<?php echo ($elem_vitOs_pigDeb_neg == "-ve" || $elem_vitOs_pigDeb_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_25" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_pigDeb_pos" name="elem_vitOs_pigDeb_pos" value="Present"
    			<?php echo ($elem_vitOs_pigDeb_pos == "+ve" || $elem_vitOs_pigDeb_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_pigDeb_pos" >Present</label>
    		</td><td><input  id="os_26" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_pigDeb_T" value="T"
    			<?php echo ($elem_vitOs_pigDeb_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="os_26" >T</label>
    		</td><td><input  id="os_27" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_pigDeb_pos1" value="1+"
    			<?php echo ($elem_vitOs_pigDeb_pos1 == "+1" || $elem_vitOs_pigDeb_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="os_27" >1+</label>
    		</td><td><input  id="os_28" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_pigDeb_pos2" value="2+"
    			<?php echo ($elem_vitOs_pigDeb_pos2 == "+2" || $elem_vitOs_pigDeb_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="os_28" >2+</label>
    		</td><td><input  id="os_29" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_pigDeb_pos3" value="3+"
    			<?php echo ($elem_vitOs_pigDeb_pos3 == "+3" || $elem_vitOs_pigDeb_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="os_29" >3+</label>
    		</td><td><input  id="os_30" type="checkbox"  onclick="checkAbsent(this)"   name="elem_vitOs_pigDeb_pos4" value="4+"
    			<?php echo ($elem_vitOs_pigDeb_pos4 == "+4" || $elem_vitOs_pigDeb_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="os_30" >4+</label>
    		</td>
    		</tr>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["Pigment"])){ echo $arr_exm_ext_htm["Vitreous"]["Pigment"]; }  ?>

    		<tr id="d_Floaters">
    		<td >Floaters</td>
    		<td>
    		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_neg" name="elem_vitOd_floaters_neg" value="Absent"
    			<?php echo ($elem_vitOd_floaters_neg == "-ve" || $elem_vitOd_floaters_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_neg" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_pos" name="elem_vitOd_floaters_pos" value="Present"
    			<?php echo ($elem_vitOd_floaters_pos == "+ve" || $elem_vitOd_floaters_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_pos" >Present</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_T" name="elem_vitOd_floaters_T" value="T"
    			<?php echo ($elem_vitOd_floaters_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_T" >T</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_pos1" name="elem_vitOd_floaters_pos1" value="1+"
    			<?php echo ($elem_vitOd_floaters_pos1 == "+1" || $elem_vitOd_floaters_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_pos1" >1+</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_pos2" name="elem_vitOd_floaters_pos2" value="2+"
    			<?php echo ($elem_vitOd_floaters_pos2 == "+2" || $elem_vitOd_floaters_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_pos2" >2+</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_pos3" name="elem_vitOd_floaters_pos3" value="3+"
    			<?php echo ($elem_vitOd_floaters_pos3 == "+3" || $elem_vitOd_floaters_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_pos3" >3+</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOd_floaters_pos4" name="elem_vitOd_floaters_pos4" value="4+"
    			<?php echo ($elem_vitOd_floaters_pos4 == "+4" || $elem_vitOd_floaters_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOd_floaters_pos4" >4+</label>
    		</td>
    		<td align="center" class="bilat" onClick="check_bl('Floaters')">BL</td>
    		<td >Floaters</td>
    		<td>
    		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_neg" name="elem_vitOs_floaters_neg" value="Absent"
    			<?php echo ($elem_vitOs_floaters_neg == "-ve" || $elem_vitOs_floaters_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_neg" >Absent</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_pos" name="elem_vitOs_floaters_pos" value="Present"
    			<?php echo ($elem_vitOs_floaters_pos == "+ve" || $elem_vitOs_floaters_pos == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_pos" >Present</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_T" name="elem_vitOs_floaters_T" value="T"
    			<?php echo ($elem_vitOs_floaters_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_T" >T</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_pos1" name="elem_vitOs_floaters_pos1" value="1+"
    			<?php echo ($elem_vitOs_floaters_pos1 == "+1" || $elem_vitOs_floaters_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_pos1" >1+</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_pos2" name="elem_vitOs_floaters_pos2" value="2+"
    			<?php echo ($elem_vitOs_floaters_pos2 == "+2" || $elem_vitOs_floaters_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_pos2" >2+</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_pos3" name="elem_vitOs_floaters_pos3" value="3+"
    			<?php echo ($elem_vitOs_floaters_pos3 == "+3" || $elem_vitOs_floaters_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_pos3" >3+</label>
    		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_vitOs_floaters_pos4" name="elem_vitOs_floaters_pos4" value="4+"
    			<?php echo ($elem_vitOs_floaters_pos4 == "+4" || $elem_vitOs_floaters_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_vitOs_floaters_pos4" >4+</label>
    		</td>
    		</tr>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["Floaters"])){ echo $arr_exm_ext_htm["Vitreous"]["Floaters"]; }  ?>
    		<?php if(isset($arr_exm_ext_htm["Vitreous"]["Main"])){ echo $arr_exm_ext_htm["Vitreous"]["Main"]; }  ?>

    		<tr id="d_adOpt_vit">
    		<td >Comments</td>
    		<td colspan="7"><textarea  onblur="checkwnls()" id="od_31" name="elem_vitAdOptionsOd" class="form-control"><?php echo ($elem_vitAdOptionsOd);?></textarea></td>
    		<td align="center" class="bilat" onClick="check_bl('adOpt_vit')">BL</td>
    		<td >Comments</td>
    		<td colspan="7"><textarea  onblur="checkwnls()" id="os_31" name="elem_vitAdOptionsOs" class="form-control"><?php echo ($elem_vitAdOptionsOs);?></textarea></td>
    		</tr>
    	</table>
    	</div>
  </div>
	<!-- Vitreous. -->
	<!-- Macula. -->
	<div role="tabpanel" class="tab-pane <?php echo (2 == $defTabKey) ? "active" : "" ?>" id="div2">
		<div class="examhd form-inline">
			<div class="row">
				<div class="col-sm-1">
				</div>
				<div class="col-sm-2">
				</div>
				<div class="col-sm-2">
				</div>
				<div class="col-sm-7">
					<span id="examFlag" class="glyphicon flagWnl "></span>
					<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

					<input type="checkbox" id="elem_noChangeMacula"  name="elem_noChangeMacula" value="1" onClick="setNC2();"
								<?php echo ($elem_ncMacula == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
					<label class="lbl_nochange frcb" for="elem_noChangeMacula">NO Change</label>

				</div>
			</div>
		</div>
		<div class="clearfix"> </div>

		<div class="table-responsive">
		<table class="table table-bordered table-striped" >
			<tr>
			<td colspan="7" align="center">
				<span class="flgWnl_2" id="flagWnlOd" ></span>
				<div class="checkboxO"><label class="od cbold">OD</label></div>
			</td>
			<td width="67" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
			<td colspan="7" align="center">
				<span class="flgWnl_2" id="flagWnlOs" ></span>
				<div class="checkboxO"><label class="os cbold">OS</label></div>
			</td>
			</tr>

			<tr id="d_macME">
			<td >Macular edema</td>
			<td>
			<input id="elem_macOd_ME_Absent" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_ME_Absent" name="elem_macOd_ME_Absent" value="Absent"
				<?php echo ($elem_macOd_ME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_ME_Absent" >Absent</label>
			</td><td><input id="elem_macOd_ME_Focal" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_ME_Focal" name="elem_macOd_ME_Focal" value="Focal"
				<?php echo ($elem_macOd_ME_Focal == "Focal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_ME_Focal" >Focal</label>
			</td><td colspan="2"><input id="elem_macOd_ME_Diffuse" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_ME_Diffuse" name="elem_macOd_ME_Diffuse" value="Diffuse"
				<?php echo ($elem_macOd_ME_Diffuse == "Diffuse") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_ME_Diffuse" >Diffuse</label>
			</td><td colspan="2"><input id="elem_macOd_ME_Cystoid" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_ME_Cystoid" name="elem_macOd_ME_Cystoid" value="Cystoid"
				<?php echo ($elem_macOd_ME_Cystoid == "Cystoid") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_ME_Cystoid" >Cystoid</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macME')">BL</td>
			<td >Macular edema</td>
			<td>
			<input id="elem_macOs_ME_Absent" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_ME_Absent" name="elem_macOs_ME_Absent" value="Absent"
			<?php echo ($elem_macOs_ME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_ME_Absent" >Absent</label>
			</td><td><input id="elem_macOs_ME_Focal" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_ME_Focal" name="elem_macOs_ME_Focal" value="Focal"
			<?php echo ($elem_macOs_ME_Focal == "Focal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_ME_Focal" >Focal</label>
			</td><td colspan="2"><input id="elem_macOs_ME_Diffuse" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_ME_Diffuse" name="elem_macOs_ME_Diffuse" value="Diffuse"
			<?php echo ($elem_macOs_ME_Diffuse == "Diffuse") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_ME_Diffuse" >Diffuse</label>
			</td><td colspan="2"><input id="elem_macOs_ME_Cystoid" type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_ME_Cystoid" name="elem_macOs_ME_Cystoid" value="Cystoid"
			<?php echo ($elem_macOs_ME_Cystoid == "Cystoid") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_ME_Cystoid" >Cystoid</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["Macular edema"])){ echo $arr_exm_ext_htm["Macula"]["Macular edema"]; }  ?>

			<tr id="d_Drusen_mac" class="grp_Drusen_mac">
			<td >Drusen</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_Absent" name="elem_macOd_drusen_Absent" value="Absent"
				<?php echo ($elem_macOd_drusen_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_Absent" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_T" name="elem_macOd_drusen_T" value="T"
				<?php echo ($elem_macOd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_pos1" name="elem_macOd_drusen_pos1" value="1+"
				<?php echo ($elem_macOd_drusen_pos1 == "+1" || $elem_macOd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_pos2" name="elem_macOd_drusen_pos2" value="2+"
				<?php echo ($elem_macOd_drusen_pos2 == "+2" || $elem_macOd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_pos3" name="elem_macOd_drusen_pos3" value="3+"
				<?php echo ($elem_macOd_drusen_pos3 == "+3" || $elem_macOd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_pos4" name="elem_macOd_drusen_pos4" value="4+"
				<?php echo ($elem_macOd_drusen_pos4 == "+4" || $elem_macOd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_pos4" >4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('Drusen_mac')" rowspan="2">BL</td>
			<td >Drusen</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_Absent" name="elem_macOs_drusen_Absent" value="Absent"
				<?php echo ($elem_macOs_drusen_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_Absent" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_T" name="elem_macOs_drusen_T" value="T"
				<?php echo ($elem_macOs_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_pos1" name="elem_macOs_drusen_pos1" value="1+"
				<?php echo ($elem_macOs_drusen_pos1 == "+1" || $elem_macOs_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_pos2" name="elem_macOs_drusen_pos2" value="2+"
				<?php echo ($elem_macOs_drusen_pos2 == "+2" || $elem_macOs_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_pos3" name="elem_macOs_drusen_pos3" value="3+"
				<?php echo ($elem_macOs_drusen_pos3 == "+3" || $elem_macOs_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_pos4" name="elem_macOs_drusen_pos4" value="4+"
				<?php echo ($elem_macOs_drusen_pos4 == "+4" || $elem_macOs_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_pos4" >4+</label>
			</td>
			</tr>
			<tr id="d_Drusen_mac1" class="grp_Drusen_mac">
			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_F" name="elem_macOd_drusen_F" value="F"
			<?php echo ($elem_macOd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_foveal" name="elem_macOd_drusen_foveal" value="PF"
			<?php echo ($elem_macOd_drusen_foveal == "PF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_foveal" >PF</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_EF" name="elem_macOd_drusen_EF" value="EF"
			<?php echo ($elem_macOd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_EF" >EF</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_drusen_hard" name="elem_macOd_drusen_hard" value="Hard"
			<?php echo ($elem_macOd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_drusen_hard" >Hard</label>
			</td>

			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_F" name="elem_macOs_drusen_F" value="F"
			<?php echo ($elem_macOs_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_foveal" name="elem_macOs_drusen_foveal" value="PF"
			<?php echo ($elem_macOs_drusen_foveal == "PF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_foveal" >PF</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_EF" name="elem_macOs_drusen_EF" value="EF"
			<?php echo ($elem_macOs_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_EF" >EF</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_drusen_hard" name="elem_macOs_drusen_hard" value="Hard"
			<?php echo ($elem_macOs_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_drusen_hard" >Hard</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["Drusen"])){ echo $arr_exm_ext_htm["Macula"]["Drusen"]; }  ?>

			<tr class="exmhlgcol grp_handle grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macARMD">
			<td class="grpbtn" onclick="openSubGrp('macARMD')">
				<label >AMD
				<span class="glyphicon <?php echo $arow_macARMD; ?>"></span></label>
			</td>
			<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'macARMD');" id="elem_macOd_armd_text" name="elem_macOd_armd_text" class="form-control"><?php echo ($elem_macOd_armd_text);?></textarea></td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')">BL</td>
			<td class="grpbtn" onclick="openSubGrp('macARMD')">
				<label >AMD
				<span class="glyphicon <?php echo $arow_macARMD; ?>"></span></label>
			</td>
			<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'macARMD');" id="elem_macOs_armd_text" name="elem_macOs_armd_text" class="form-control"><?php echo ($elem_macOs_armd_text);?></textarea></td>
			</tr>


			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_drusen" >
			<td >Drusen</td>
			<td>
			<input type="checkbox"   onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_neg" name="elem_macOd_armd_drusen_neg" value="Absent"
			<?php echo ($elem_macOd_armd_drusen_neg == "-ve" || $elem_macOd_armd_drusen_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_neg" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_T" name="elem_macOd_armd_drusen_T" value="T"
			<?php echo ($elem_macOd_armd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_pos1" name="elem_macOd_armd_drusen_pos1" value="1+"
			<?php echo ($elem_macOd_armd_drusen_pos1 == "+1" || $elem_macOd_armd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_pos2" name="elem_macOd_armd_drusen_pos2" value="2+"
			<?php echo ($elem_macOd_armd_drusen_pos2 == "+2" || $elem_macOd_armd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_pos3" name="elem_macOd_armd_drusen_pos3" value="3+"
			<?php echo ($elem_macOd_armd_drusen_pos3 == "+3" || $elem_macOd_armd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_pos4" name="elem_macOd_armd_drusen_pos4" value="4+"
			<?php echo ($elem_macOd_armd_drusen_pos4 == "+4" || $elem_macOd_armd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_pos4" >4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')" rowspan="2" >BL</td>
			<td >Drusen</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_neg" name="elem_macOs_armd_drusen_neg" value="Absent"
			<?php echo ($elem_macOs_armd_drusen_neg == "-ve" || $elem_macOs_armd_drusen_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_neg" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_T" name="elem_macOs_armd_drusen_T" value="T"
			<?php echo ($elem_macOs_armd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_pos1" name="elem_macOs_armd_drusen_pos1" value="1+"
			<?php echo ($elem_macOs_armd_drusen_pos1 == "+1" || $elem_macOs_armd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_pos2" name="elem_macOs_armd_drusen_pos2" value="2+"
			<?php echo ($elem_macOs_armd_drusen_pos2 == "+2" || $elem_macOs_armd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_pos3" name="elem_macOs_armd_drusen_pos3" value="3+"
			<?php echo ($elem_macOs_armd_drusen_pos3 == "+3" || $elem_macOs_armd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_pos4" name="elem_macOs_armd_drusen_pos4" value="4+"
			<?php echo ($elem_macOs_armd_drusen_pos4 == "+4" || $elem_macOs_armd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_pos4" >4+</label>
			</td>
			</tr>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_drusen1">
			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_F" name="elem_macOd_armd_drusen_F" value="F"
			<?php echo ($elem_macOd_armd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_foveal" name="elem_macOd_armd_drusen_foveal" value="PF"
			<?php echo (($elem_macOd_armd_drusen_foveal == "Perifovial") || ($elem_macOd_armd_drusen_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_foveal" >PF</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_EF" name="elem_macOd_armd_drusen_EF" value="EF"
			<?php echo ($elem_macOd_armd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_EF" >EF</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_hard" name="elem_macOd_armd_drusen_hard" value="Hard"
			<?php echo ($elem_macOd_armd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_hard" >Hard</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_drusen_soft" name="elem_macOd_armd_drusen_soft" value="Soft"
			<?php echo ($elem_macOd_armd_drusen_soft == "Soft") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_drusen_soft" >Soft</label>
			</td>

			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_F" name="elem_macOs_armd_drusen_F" value="F"
			<?php echo ($elem_macOs_armd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_foveal" name="elem_macOs_armd_drusen_foveal" value="PF"
			<?php echo (($elem_macOs_armd_drusen_foveal == "Perifovial") || ($elem_macOs_armd_drusen_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_foveal" >PF</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_EF" name="elem_macOs_armd_drusen_EF" value="EF"
			<?php echo ($elem_macOs_armd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_EF" >EF</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_hard" name="elem_macOs_armd_drusen_hard" value="Hard"
			<?php echo ($elem_macOs_armd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_hard" >Hard</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_drusen_soft" name="elem_macOs_armd_drusen_soft" value="Soft"
			<?php echo ($elem_macOs_armd_drusen_soft == "Soft") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_drusen_soft" >Soft</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/Drusen"])){ echo $arr_exm_ext_htm["Macula"]["AMD/Drusen"]; }  ?>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_rpec">
			<td >RPE Changes</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_neg" name="elem_macOd_armd_rpeCh_neg" value="Absent"
			<?php echo ($elem_macOd_armd_rpeCh_neg == "-ve" || $elem_macOd_armd_rpeCh_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_neg" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_T" name="elem_macOd_armd_rpeCh_T" value="T"
			<?php echo ($elem_macOd_armd_rpeCh_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_pos1" name="elem_macOd_armd_rpeCh_pos1" value="1+"
			<?php echo ($elem_macOd_armd_rpeCh_pos1 == "+1" || $elem_macOd_armd_rpeCh_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_pos2" name="elem_macOd_armd_rpeCh_pos2" value="2+"
			<?php echo ($elem_macOd_armd_rpeCh_pos2 == "+2" || $elem_macOd_armd_rpeCh_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_pos3" name="elem_macOd_armd_rpeCh_pos3" value="3+"
			<?php echo ($elem_macOd_armd_rpeCh_pos3 == "+3" || $elem_macOd_armd_rpeCh_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_pos4" name="elem_macOd_armd_rpeCh_pos4" value="4+"
			<?php echo ($elem_macOd_armd_rpeCh_pos4 == "+4" || $elem_macOd_armd_rpeCh_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_pos4" >4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')" rowspan="2" >BL</td>
			<td >RPE Changes</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_neg" name="elem_macOs_armd_rpeCh_neg" value="Absent"
			<?php echo ($elem_macOs_armd_rpeCh_neg == "-ve" || $elem_macOs_armd_rpeCh_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_neg" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_T" name="elem_macOs_armd_rpeCh_T" value="T"
			<?php echo ($elem_macOs_armd_rpeCh_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_pos1" name="elem_macOs_armd_rpeCh_pos1" value="1+"
			<?php echo ($elem_macOs_armd_rpeCh_pos1 == "+1" || $elem_macOs_armd_rpeCh_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_pos2" name="elem_macOs_armd_rpeCh_pos2" value="2+"
			<?php echo ($elem_macOs_armd_rpeCh_pos2 == "+2" || $elem_macOs_armd_rpeCh_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_pos3" name="elem_macOs_armd_rpeCh_pos3" value="3+"
			<?php echo ($elem_macOs_armd_rpeCh_pos3 == "+3" || $elem_macOs_armd_rpeCh_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_pos4" name="elem_macOs_armd_rpeCh_pos4" value="4+"
			<?php echo ($elem_macOs_armd_rpeCh_pos4 == "+4" || $elem_macOs_armd_rpeCh_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_pos4" >4+</label>
			</td>
			</tr>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_rpec1">
			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_F" name="elem_macOd_armd_rpeCh_F" value="F"
			<?php echo ($elem_macOd_armd_rpeCh_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_foveal" name="elem_macOd_armd_rpeCh_foveal" value="PF"
			<?php echo (($elem_macOd_armd_rpeCh_foveal == "Perifovial") || ($elem_macOd_armd_rpeCh_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_foveal" >PF</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rpeCh_EF" name="elem_macOd_armd_rpeCh_EF" value="EF"
			<?php echo (($elem_macOd_armd_rpeCh_EF == "EF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rpeCh_EF" >EF</label>
			</td>

			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_F" name="elem_macOs_armd_rpeCh_F" value="F"
			<?php echo ($elem_macOs_armd_rpeCh_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"  id="elem_macOs_armd_rpeCh_foveal" name="elem_macOs_armd_rpeCh_foveal" value="PF"
			<?php echo (($elem_macOs_armd_rpeCh_foveal == "Perifovial") || ($elem_macOs_armd_rpeCh_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_foveal" >PF</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rpeCh_EF"  name="elem_macOs_armd_rpeCh_EF" value="EF"
			<?php echo ($elem_macOs_armd_rpeCh_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rpeCh_EF" >EF</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/RPE Changes"])){ echo $arr_exm_ext_htm["Macula"]["AMD/RPE Changes"]; }  ?>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_geo_atr">
			<td >Geographic Atrophy</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"  id="elem_macOd_armd_geoAtro_neg" name="elem_macOd_armd_geoAtro_neg" value="Absent"
			<?php echo ($elem_macOd_armd_geoAtro_neg == "-ve" || $elem_macOd_armd_geoAtro_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_neg" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"  id="elem_macOd_armd_geoAtro_T" name="elem_macOd_armd_geoAtro_T" value="T"
			<?php echo ($elem_macOd_armd_geoAtro_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_pos1"  name="elem_macOd_armd_geoAtro_pos1" value="1+"
			<?php echo ($elem_macOd_armd_geoAtro_pos1 == "+1" || $elem_macOd_armd_geoAtro_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_pos2"  name="elem_macOd_armd_geoAtro_pos2" value="2+"
			<?php echo ($elem_macOd_armd_geoAtro_pos2 == "+2" || $elem_macOd_armd_geoAtro_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_pos3"  name="elem_macOd_armd_geoAtro_pos3" value="3+"
			<?php echo ($elem_macOd_armd_geoAtro_pos3 == "+3" || $elem_macOd_armd_geoAtro_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_pos4"  name="elem_macOd_armd_geoAtro_pos4" value="4+"
			<?php echo ($elem_macOd_armd_geoAtro_pos4 == "+4" || $elem_macOd_armd_geoAtro_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_pos4" >4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')" rowspan="2" >BL</td>
			<td >Geographic Atrophy</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"  id="elem_macOs_armd_geoAtro_neg" name="elem_macOs_armd_geoAtro_neg" value="Absent"
			<?php echo ($elem_macOs_armd_geoAtro_neg == "-ve" || $elem_macOs_armd_geoAtro_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_neg" >Absent</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_T" name="elem_macOs_armd_geoAtro_T" value="T"
			<?php echo ($elem_macOs_armd_geoAtro_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_T" >T</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_pos1" name="elem_macOs_armd_geoAtro_pos1" value="1+"
			<?php echo ($elem_macOs_armd_geoAtro_pos1 == "+1" || $elem_macOs_armd_geoAtro_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_pos1" >1+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_pos2" name="elem_macOs_armd_geoAtro_pos2" value="2+"
			<?php echo ($elem_macOs_armd_geoAtro_pos2 == "+2" || $elem_macOs_armd_geoAtro_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_pos2" >2+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_pos3" name="elem_macOs_armd_geoAtro_pos3" value="3+"
			<?php echo ($elem_macOs_armd_geoAtro_pos3 == "+3" || $elem_macOs_armd_geoAtro_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_pos3" >3+</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_pos4" name="elem_macOs_armd_geoAtro_pos4" value="4+"
			<?php echo ($elem_macOs_armd_geoAtro_pos4 == "+4" || $elem_macOs_armd_geoAtro_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_pos4" >4+</label>
			</td>
			</tr>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_geo_atr1">
			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_F"  name="elem_macOd_armd_geoAtro_F" value="F"
			<?php echo ($elem_macOd_armd_geoAtro_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_foveal"  name="elem_macOd_armd_geoAtro_foveal" value="PF"
			<?php echo (($elem_macOd_armd_geoAtro_foveal == "Perifovial") || ($elem_macOd_armd_geoAtro_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_foveal" >PF</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_geoAtro_EF"  name="elem_macOd_armd_geoAtro_EF" value="EF"
			<?php echo ($elem_macOd_armd_geoAtro_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_geoAtro_EF" >EF</label>
			</td>

			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_F" name="elem_macOs_armd_geoAtro_F" value="F"
			<?php echo ($elem_macOs_armd_geoAtro_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_F" >F</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_foveal" name="elem_macOs_armd_geoAtro_foveal" value="PF"
			<?php echo (($elem_macOs_armd_geoAtro_foveal == "Perifovial") || ($elem_macOs_armd_geoAtro_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_foveal" >PF</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_geoAtro_EF" name="elem_macOs_armd_geoAtro_EF" value="EF"
			<?php echo ($elem_macOs_armd_geoAtro_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_geoAtro_EF" >EF</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/Geographic Atrophy"])){ echo $arr_exm_ext_htm["Macula"]["AMD/Geographic Atrophy"]; }  ?>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>">
			<td >Retinal Pigment<br/> Epithelial Detachment</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rped_Absent" name="elem_macOd_armd_rped_Absent" value="Absent"
			<?php echo ($elem_macOd_armd_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rped_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_rped_Present" name="elem_macOd_armd_rped_Present" value="Present"
			<?php echo ($elem_macOd_armd_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_rped_Present" >Present</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')"  >BL</td>
			<td >Retinal Pigment<br/> Epithelial Detachment</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rped_Absent" name="elem_macOs_armd_rped_Absent" value="Absent"
			<?php echo ($elem_macOs_armd_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rped_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_rped_Present" name="elem_macOs_armd_rped_Present" value="Present"
			<?php echo ($elem_macOs_armd_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_rped_Present" >Present</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/Retinal Pigment Epithelial Detachment"])){ echo $arr_exm_ext_htm["Macula"]["AMD/Retinal Pigment Epithelial Detachment"]; }  ?>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_cnvm">
			<td >CNVM</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_cnvm_Absent" name="elem_macOd_armd_cnvm_Absent" value="Absent"
			<?php echo ($elem_macOd_armd_cnvm_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_cnvm_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_cnvm_Present" name="elem_macOd_armd_cnvm_Present" value="Present"
			<?php echo ($elem_macOd_armd_cnvm_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_cnvm_Present" >Present</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')" rowspan="2" >BL</td>
			<td >CNVM</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_cnvm_Absent" name="elem_macOs_armd_cnvm_Absent" value="Absent"
			<?php echo ($elem_macOs_armd_cnvm_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_cnvm_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_cnvm_Present" name="elem_macOs_armd_cnvm_Present" value="Present"
			<?php echo ($elem_macOs_armd_cnvm_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_cnvm_Present" >Present</label>
			</td>
			</tr>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_cnvm1">
			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_cnvm_Subfoveal" name="elem_macOd_armd_cnvm_Subfoveal" value="Subfoveal"
			<?php echo ($elem_macOd_armd_cnvm_Subfoveal == "Subfoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_cnvm_Subfoveal" >Subfoveal</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_cnvm_Perifoveal" name="elem_macOd_armd_cnvm_Perifoveal" value="Perifoveal"
			<?php echo ($elem_macOd_armd_cnvm_Perifoveal == "Perifoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_cnvm_Perifoveal" >Perifoveal</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_cnvm_Extrafoveal" name="elem_macOd_armd_cnvm_Extrafoveal" value="Extrafoveal"
			<?php echo ($elem_macOd_armd_cnvm_Extrafoveal == "Extrafoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_cnvm_Extrafoveal" >Extrafoveal</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_cnvm_Juxtapapillary" name="elem_macOd_armd_cnvm_Juxtapapillary" value="Juxtapapillary"
			<?php echo ($elem_macOd_armd_cnvm_Juxtapapillary == "Juxtapapillary") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_cnvm_Juxtapapillary" >Juxtapapillary</label>
			</td>

			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_cnvm_Subfoveal" name="elem_macOs_armd_cnvm_Subfoveal" value="Subfoveal"
			<?php echo ($elem_macOs_armd_cnvm_Subfoveal == "Subfoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_cnvm_Subfoveal" >Subfoveal</label>
			</td><td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_cnvm_Perifoveal" name="elem_macOs_armd_cnvm_Perifoveal" value="Perifoveal"
			<?php echo ($elem_macOs_armd_cnvm_Perifoveal == "Perifoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_cnvm_Perifoveal" >Perifoveal</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_cnvm_Extrafoveal" name="elem_macOs_armd_cnvm_Extrafoveal" value="Extrafoveal"
			<?php echo ($elem_macOs_armd_cnvm_Extrafoveal == "Extrafoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_cnvm_Extrafoveal" >Extrafoveal</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_cnvm_Juxtapapillary" name="elem_macOs_armd_cnvm_Juxtapapillary" value="Juxtapapillary"
			<?php echo ($elem_macOs_armd_cnvm_Juxtapapillary == "Juxtapapillary") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_cnvm_Juxtapapillary" >Juxtapapillary</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/CNVM"])){ echo $arr_exm_ext_htm["Macula"]["AMD/CNVM"]; }  ?>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_srh">
			<td >SRH</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_srh_Absent" name="elem_macOd_armd_srh_Absent" value="Absent"
			<?php echo ($elem_macOd_armd_srh_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_srh_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_srh_Present" name="elem_macOd_armd_srh_Present" value="Present"
			<?php echo ($elem_macOd_armd_srh_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_srh_Present" >Present</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')" rowspan="2" >BL</td>
			<td >SRH</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_srh_Absent" name="elem_macOs_armd_srh_Absent" value="Absent"
			<?php echo ($elem_macOs_armd_srh_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_srh_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_srh_Present" name="elem_macOs_armd_srh_Present" value="Present"
			<?php echo ($elem_macOs_armd_srh_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_srh_Present" >Present</label>
			</td>
			</tr>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>" id="d_macarm_srh1">
			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_srh_Mild" name="elem_macOd_armd_srh_Mild" value="Mild"
			<?php echo ($elem_macOd_armd_srh_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_srh_Mild" >Mild</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_srh_Moderate" name="elem_macOd_armd_srh_Moderate" value="Moderate"
			<?php echo ($elem_macOd_armd_srh_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_srh_Moderate" >Moderate</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_srh_Massive" name="elem_macOd_armd_srh_Massive" value="Massive"
			<?php echo ($elem_macOd_armd_srh_Massive == "Massive") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_srh_Massive" >Massive</label>
			</td>

			<td ></td>
			<td><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_srh_Mild" name="elem_macOs_armd_srh_Mild" value="Mild"
			<?php echo ($elem_macOs_armd_srh_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_srh_Mild" >Mild</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_srh_Moderate" name="elem_macOs_armd_srh_Moderate" value="Moderate"
			<?php echo ($elem_macOs_armd_srh_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_srh_Moderate" >Moderate</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_srh_Massive" name="elem_macOs_armd_srh_Massive" value="Massive"
			<?php echo ($elem_macOs_armd_srh_Massive == "Massive") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_srh_Massive" >Massive</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/SRH"])){ echo $arr_exm_ext_htm["Macula"]["AMD/SRH"]; }  ?>

			<tr class="exmhlgcol grp_macARMD <?php echo $cls_macARMD; ?>">
			<td >Subretinal Fluid</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_subret_Absent" name="elem_macOd_armd_subret_Absent" value="Absent"
			<?php echo ($elem_macOd_armd_subret_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_subret_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOd_armd_subret_Present" name="elem_macOd_armd_subret_Present" value="Present"
			<?php echo ($elem_macOd_armd_subret_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_armd_subret_Present" >Present</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macARMD')"  >BL</td>
			<td >Subretinal Fluid</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_subret_Absent" name="elem_macOs_armd_subret_Absent" value="Absent"
			<?php echo ($elem_macOs_armd_subret_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_subret_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'macARMD');"   id="elem_macOs_armd_subret_Present" name="elem_macOs_armd_subret_Present" value="Present"
			<?php echo ($elem_macOs_armd_subret_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_armd_subret_Present" >Present</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD/Subretinal Fluid"])){ echo $arr_exm_ext_htm["Macula"]["AMD/Subretinal Fluid"]; }  ?>
			<?php if(isset($arr_exm_ext_htm["Macula"]["AMD"])){ echo $arr_exm_ext_htm["Macula"]["AMD"]; }  ?>

			<tr id="d_macERM" class="grp_macERM">
			<td >ERM</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_erm_neg" name="elem_macOd_erm_neg" value="Absent"
			<?php echo ($elem_macOd_erm_neg == "-ve" || $elem_macOd_erm_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_neg" >Absent</label>
			</td><td><input id="elem_macOd_erm_T" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOd_erm_T" value="T"
			<?php echo ($elem_macOd_erm_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_T" >T</label>
			</td><td><input id="elem_macOd_erm_pos1" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOd_erm_pos1" value="1+"
			<?php echo ($elem_macOd_erm_pos1 == "+1" || $elem_macOd_erm_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_pos1" >1+</label>
			</td><td><input id="elem_macOd_erm_pos2" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOd_erm_pos2" value="2+"
			<?php echo ($elem_macOd_erm_pos2 == "+2" || $elem_macOd_erm_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_pos2" >2+</label>
			</td><td><input id="elem_macOd_erm_pos3" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOd_erm_pos3" value="3+"
			<?php echo ($elem_macOd_erm_pos3 == "+3"  || $elem_macOd_erm_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_pos3" >3+</label>
			</td><td><input id="elem_macOd_erm_pos4" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOd_erm_pos4" value="4+"
			<?php echo ($elem_macOd_erm_pos4 == "+4" || $elem_macOd_erm_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_pos4" >4+</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macERM')" rowspan="3">BL</td>
			<td >ERM</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_erm_neg" name="elem_macOs_erm_neg" value="Absent"
			<?php echo ($elem_macOs_erm_neg == "-ve" || $elem_macOs_erm_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_neg" >Absent</label>
			</td><td><input id="elem_macOs_erm_T" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOs_erm_T" value="T"
			<?php echo ($elem_macOs_erm_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_T" >T</label>
			</td><td><input id="elem_macOs_erm_pos1" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOs_erm_pos1" value="1+"
			<?php echo ($elem_macOs_erm_pos1 == "+1" || $elem_macOs_erm_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_pos1" >1+</label>
			</td><td><input id="elem_macOs_erm_pos2" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOs_erm_pos2" value="2+"
			<?php echo ($elem_macOs_erm_pos2 == "+2" || $elem_macOs_erm_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_pos2" >2+</label>
			</td><td><input id="elem_macOs_erm_pos3" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOs_erm_pos3" value="3+"
			<?php echo ($elem_macOs_erm_pos3 == "+3" || $elem_macOs_erm_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_pos3" >3+</label>
			</td><td><input id="elem_macOs_erm_pos4" type="checkbox"  onclick="checkAbsent(this)"   name="elem_macOs_erm_pos4" value="4+"
			<?php echo ($elem_macOs_erm_pos4 == "+4" || $elem_macOs_erm_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_pos4" >4+</label>
			</td>
			</tr>

			<tr id="d_macERM1" class="grp_macERM">
			<td ></td>
			<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_erm_Superotemporal" name="elem_macOd_erm_Superotemporal" value="Superotemporal"
			<?php echo ($elem_macOd_erm_Superotemporal == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_Superotemporal"  class="al">Superotemporal</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_erm_Inferotemporal" name="elem_macOd_erm_Inferotemporal" value="Inferotemporal"
			<?php echo ($elem_macOd_erm_Inferotemporal == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_Inferotemporal"  class="al">Inferotemporal</label>
			</td>
			<td ></td>
			<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_erm_Superotemporal" name="elem_macOs_erm_Superotemporal" value="Superotemporal"
			<?php echo ($elem_macOs_erm_Superotemporal == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_Superotemporal"  class="al">Superotemporal</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_erm_Inferotemporal" name="elem_macOs_erm_Inferotemporal" value="Inferotemporal"
			<?php echo ($elem_macOs_erm_Inferotemporal == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_Inferotemporal"  class="al">Inferotemporal</label>
			</td>
			</tr>

			<tr id="d_macERM2" class="grp_macERM">
			<td ></td>
			<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_erm_Superonasal" name="elem_macOd_erm_Superonasal" value="Superonasal"
			<?php echo ($elem_macOd_erm_Superonasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_Superonasal"  class="al">Superonasal</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOd_erm_Inferonasal" name="elem_macOd_erm_Inferonasal" value="Inferonasal"
			<?php echo ($elem_macOd_erm_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_erm_Inferonasal"  class="al">Inferonasal</label>
			</td>
			<td ></td>
			<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_erm_Superonasal" name="elem_macOs_erm_Superonasal" value="Superonasal"
			<?php echo ($elem_macOs_erm_Superonasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_Superonasal"  class="al">Superonasal</label>
			</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_macOs_erm_Inferonasal" name="elem_macOs_erm_Inferonasal" value="Inferonasal"
			<?php echo ($elem_macOs_erm_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_erm_Inferonasal"  class="al">Inferonasal</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["ERM"])){ echo $arr_exm_ext_htm["Macula"]["ERM"]; }  ?>

			<tr class="grp_macRPED">
			<td >Retinal Pigment<br/> Epithelial Detachment</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_rped_Absent" name="elem_macOd_rped_Absent" value="Absent"
			<?php echo ($elem_macOd_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_rped_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_rped_Present" name="elem_macOd_rped_Present" value="Present"
			<?php echo ($elem_macOd_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_rped_Present" >Present</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macRPED')">BL</td>
			<td >Retinal Pigment<br/> Epithelial Detachment</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_rped_Absent" name="elem_macOs_rped_Absent" value="Absent"
			<?php echo ($elem_macOs_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_rped_Absent" >Absent</label>
			</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_rped_Present" name="elem_macOs_rped_Present" value="Present"
			<?php echo ($elem_macOs_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_rped_Present" >Present</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["Retinal Pigment Epithelial Detachment"])){ echo $arr_exm_ext_htm["Macula"]["Retinal Pigment Epithelial Detachment"]; }  ?>

			<tr id="d_macCWS" class="grp_macCWS">
			<td >Cotton Wool Spot</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_cws_Absent" name="elem_macOd_cws_Absent" value="Absent"
			<?php echo ($elem_macOd_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_cws_Absent" >Absent</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_cws_Macula" name="elem_macOd_cws_Macula" value="Macula"
			<?php echo ($elem_macOd_cws_Macula == "Macula") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_cws_Macula" >Macula</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_cws_st" name="elem_macOd_cws_st" value="Superotemporal"
			<?php echo ($elem_macOd_cws_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_cws_st" >Superotemporal</label>
			</td>
			<td align="center" class="bilat" onClick="check_bl('macCWS')" rowspan="2">BL</td>
			<td >Cotton Wool Spot</td>
			<td>
			<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_cws_Absent" name="elem_macOs_cws_Absent" value="Absent"
			<?php echo ($elem_macOs_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_cws_Absent" >Absent</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_cws_Macula" name="elem_macOs_cws_Macula" value="Macula"
			<?php echo ($elem_macOs_cws_Macula == "Macula") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_cws_Macula" >Macula</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_cws_st" name="elem_macOs_cws_st" value="Superotemporal"
			<?php echo ($elem_macOs_cws_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_cws_st" >Superotemporal</label>
			</td>
			</tr>

			<tr id="d_macCWS1" class="grp_macCWS">
			<td ></td>
			<td ><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_cws_it" name="elem_macOd_cws_it" value="Inferotemporal"
			<?php echo ($elem_macOd_cws_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_cws_it" >Inferotemporal</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_cws_sn" name="elem_macOd_cws_sn" value="Superonasal"
			<?php echo ($elem_macOd_cws_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_cws_sn" >Superonasal</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOd_cws_in" name="elem_macOd_cws_in" value="Inferonasal"
			<?php echo ($elem_macOd_cws_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOd_cws_in" >Inferonasal</label>
			</td>

			<td ></td>
			<td ><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_cws_it" name="elem_macOs_cws_it" value="Inferotemporal"
			<?php echo ($elem_macOs_cws_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_cws_it" >Inferotemporal</label>
			</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_cws_sn" name="elem_macOs_cws_sn" value="Superonasal"
			<?php echo ($elem_macOs_cws_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_cws_sn" >Superonasal</label>
			</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_macOs_cws_in" name="elem_macOs_cws_in" value="Inferonasal"
			<?php echo ($elem_macOs_cws_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_macOs_cws_in" >Inferonasal</label>
			</td>
			</tr>
			<?php if(isset($arr_exm_ext_htm["Macula"]["Cotton Wool Spot"])){ echo $arr_exm_ext_htm["Macula"]["Cotton Wool Spot"]; }  ?>
			<?php if(isset($arr_exm_ext_htm["Macula"]["Main"])){ echo $arr_exm_ext_htm["Macula"]["Main"]; }  ?>

			<tr id="d_adOpt_mac">
			<td >Comments</td>
			<td colspan="6" ><textarea  onblur="checkwnls()" name="elem_maculaAdOptionsOd" class="form-control"><?php echo ($elem_maculaAdOptionsOd);?></textarea></td>
			<td align="center" class="bilat" onClick="check_bl('adOpt_mac')">BL</td>
			<td >Comments</td>
			<td colspan="6"><textarea  onblur="checkwnls()" name="elem_maculaAdOptionsOs" class="form-control"><?php echo ($elem_maculaAdOptionsOs);?></textarea></td>
			</tr>


		</table>
		</div>

	</div>
	<!-- Macula. -->

  <!-- BloodVessels -->
    <div role="tabpanel" class="tab-pane <?php echo (4 == $defTabKey) ? "active" : "" ?>" id="div4">
      <div class="examhd form-inline">
  			<div class="row">
  				<div class="col-sm-1">
  				</div>
  				<div class="col-sm-3">
  				</div>
  				<div class="col-sm-3">
  				</div>
          <div class="col-sm-1">
  				</div>
  				<div class="col-sm-4">
  					<span id="examFlag" class="glyphicon flagWnl "></span>
  					<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

  					<input type="checkbox" id="elem_noChangeBV"  name="elem_noChangeBV" value="1" onClick="setNC2();"
  								<?php echo ($elem_ncBV == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
  					<label class="lbl_nochange frcb" for="elem_noChangeBV">NO Change</label>

  				</div>
  			</div>
  		</div>
  		<div class="clearfix"> </div>
      <div class="table-responsive">
    		<table class="table table-bordered table-striped" >
    			<tr>
    			<td colspan="7" align="center">
    				<span class="flgWnl_2" id="flagWnlOd" ></span>
    				<div class="checkboxO"><label class="od cbold">OD</label></div>
    			</td>
    			<td width="67" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
    			<td colspan="7" align="center">
    				<span class="flgWnl_2" id="flagWnlOs" ></span>
    				<div class="checkboxO"><label class="os cbold">OS</label></div>
    			</td>
    			</tr>

          <tr id="d_bvDiabetes">
          <td >Diabetes</td>
          <td colspan="6">
          <input id="elem_bvOd_diabetes_noRetino" type="checkbox"  onclick="checkwnls()"   name="elem_bvOd_diabetes_noRetino" value="No Retinopathy"
          <?php echo ($elem_bvOd_diabetes_noRetino == "No Retinopathy") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_diabetes_noRetino"  class="noretino">No Retinopathy</label>
          </td>

          <td align="center" class="bilat" onClick="check_bl('bvDiabetes')">BL</td>
          <td >Diabetes</td>
          <td colspan="6">
          <input id="elem_bvOs_diabetes_noRetino" type="checkbox"  onclick="checkwnls()"   name="elem_bvOs_diabetes_noRetino" value="No Retinopathy"
          <?php echo ($elem_bvOs_diabetes_noRetino == "No Retinopathy") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_diabetes_noRetino"  class="noretino">No Retinopathy</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Diabetes"])){ echo $arr_exm_ext_htm["Vessels"]["Diabetes"]; }  ?>

          <tr class="exmhlgcol grp_handle grp_DRBV <?php echo $cls_DRBV; ?>" id="d_DRBV">
          <td class="grpbtn" onclick="openSubGrp('DRBV')">
            <label >DR
            <span class="glyphicon <?php echo $arow_DRBV; ?>"></span></label>
          </td>
          <td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'DRBV');"   id="elem_bvOd_dr_text" cols="40" rows="2" name="elem_bvOd_dr_text" class="form-control"><?php echo ($elem_bvOd_dr_text);?></textarea></td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td class="grpbtn" onclick="openSubGrp('DRBV')">
            <label >DR
            <span class="glyphicon <?php echo $arow_DRBV; ?>"></span></label>
          </td>
          <td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'DRBV');"   id="elem_bvOs_dr_text" cols="40" rows="2" name="elem_bvOs_dr_text" class="form-control"><?php echo ($elem_bvOs_dr_text);?></textarea></td>
          </tr>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >NPDR</td>
          <td>
          <input id="elem_bvOd_dr_npdr_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_npdr_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_npdr_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_npdr_Absent" >Absent</label>
          </td><td><input id="elem_bvOd_dr_npdr_T" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_npdr_T" value="T"
          <?php echo ($elem_bvOd_dr_npdr_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_npdr_T" >T</label>
          </td><td><input id="elem_bvOd_dr_npdr_Mild" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_npdr_Mild" value="Mild"
          <?php echo ($elem_bvOd_dr_npdr_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_npdr_Mild" >Mild</label>
          </td><td colspan="2"><input id="elem_bvOd_dr_npdr_Moderate" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_npdr_Moderate" value="Moderate"
          <?php echo ($elem_bvOd_dr_npdr_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_npdr_Moderate" >Moderate</label>
          </td><td><input id="elem_bvOd_dr_npdr_Severe" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_npdr_Severe" value="Severe"
          <?php echo ($elem_bvOd_dr_npdr_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_npdr_Severe" >Severe</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >NPDR</td>
          <td>
          <input type="checkbox"  onclick="checkAbsent(this,'DRBV');"   id="elem_bvOs_dr_npdr_Absent" name="elem_bvOs_dr_npdr_Absent" value="Absent"
            <?php echo ($elem_bvOs_dr_npdr_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_npdr_Absent" >Absent</label>
          </td><td><input id="elem_bvOs_dr_npdr_T" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_npdr_T" value="T"
            <?php echo ($elem_bvOs_dr_npdr_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_npdr_T" >T</label>
          </td><td><input id="elem_bvOs_dr_npdr_Mild" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_npdr_Mild" value="Mild"
            <?php echo ($elem_bvOs_dr_npdr_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_npdr_Mild" >Mild</label>
          </td><td colspan="2"><input id="elem_bvOs_dr_npdr_Moderate" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_npdr_Moderate" value="Moderate"
            <?php echo ($elem_bvOs_dr_npdr_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_npdr_Moderate" >Moderate</label>
          </td><td><input id="elem_bvOs_dr_npdr_Severe" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_npdr_Severe" value="Severe"
            <?php echo ($elem_bvOs_dr_npdr_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_npdr_Severe" >Severe</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/NPDR"])){ echo $arr_exm_ext_htm["Vessels"]["DR/NPDR"]; }  ?>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >Diabetic macular edema</td>
          <td>
          <input id="elem_bvOd_dr_CSME_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_CSME_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_CSME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_CSME_Absent" >Absent</label>
          </td><td><input id="elem_bvOd_dr_CSME_Present" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_CSME_Present" value="Present"
          <?php echo ($elem_bvOd_dr_CSME_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_CSME_Present" >Present</label>
          </td><td class="cntrl_invlv" colspan="2"><input id="elem_bvOd_dr_CSME_cenInvlv" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_CSME_cenInvlv" value="Center involving"
          <?php echo ($elem_bvOd_dr_CSME_cenInvlv == "Center involving") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_CSME_cenInvlv"  class="cntrl_invlv">Center involving</label>
          </td><td colspan="2"><input id="elem_bvOd_dr_CSME_cenSpar" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_CSME_cenSpar" value="Center Sparing"
          <?php echo ($elem_bvOd_dr_CSME_cenSpar == "Center Sparing") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_CSME_cenSpar"  class="cntrl_spar">Center Sparing</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >Diabetic macular edema</td>
          <td>
          <input id="elem_bvOs_dr_CSME_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_CSME_Absent" value="Absent"
          <?php echo ($elem_bvOs_dr_CSME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_CSME_Absent" >Absent</label>
          </td><td><input id="elem_bvOs_dr_CSME_Present" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_CSME_Present" value="Present"
          <?php echo ($elem_bvOs_dr_CSME_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_CSME_Present" >Present</label>
          </td><td class="cntrl_invlv" colspan="2"><input id="elem_bvOs_dr_CSME_cenInvlv" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_CSME_cenInvlv" value="Center involving"
          <?php echo ($elem_bvOs_dr_CSME_cenInvlv == "Center involving") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_CSME_cenInvlv"  class="cntrl_invlv">Center involving</label>
          </td><td colspan="2"><input id="elem_bvOs_dr_CSME_cenSpar" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_CSME_cenSpar" value="Center Sparing"
          <?php echo ($elem_bvOs_dr_CSME_cenSpar == "Center Sparing") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_CSME_cenSpar"  class="cntrl_spar">Center Sparing</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/Diabetic macular edema"])){ echo $arr_exm_ext_htm["Vessels"]["DR/Diabetic macular edema"]; }  ?>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >Hard Exudate</td>
          <td>
          <input id="elem_bvOd_dr_exu_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_exu_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_exu_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_exu_Absent" >Absent</label>
          </td><td><input id="elem_bvOd_dr_exu_T" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_exu_T" value="T"
          <?php echo ($elem_bvOd_dr_exu_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_exu_T" >T</label>
          </td><td><input id="elem_bvOd_dr_exu_pos1" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_exu_pos1" value="1+"
          <?php echo ($elem_bvOd_dr_exu_pos1 == "+1" || $elem_bvOd_dr_exu_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_exu_pos1" >1+</label>
          </td><td><input id="elem_bvOd_dr_exu_pos2" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_exu_pos2" value="2+"
          <?php echo ($elem_bvOd_dr_exu_pos2 == "+2" || $elem_bvOd_dr_exu_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_exu_pos2" >2+</label>
          </td><td><input id="elem_bvOd_dr_exu_pos3" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_exu_pos3" value="3+"
          <?php echo ($elem_bvOd_dr_exu_pos3 == "+3" || $elem_bvOd_dr_exu_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_exu_pos3" >3+</label>
          </td><td><input id="elem_bvOd_dr_exu_pos4" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_exu_pos4" value="4+"
          <?php echo ($elem_bvOd_dr_exu_pos4 == "+4" || $elem_bvOd_dr_exu_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_exu_pos4" >4+</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >Hard Exudate</td>
          <td>
          <input id="elem_bvOs_dr_exu_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_exu_Absent" value="Absent"
          <?php echo ($elem_bvOs_dr_exu_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_exu_Absent" >Absent</label>
          </td><td><input id="elem_bvOs_dr_exu_T" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_exu_T" value="T"
          <?php echo ($elem_bvOs_dr_exu_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_exu_T" >T</label>
          </td><td><input id="elem_bvOs_dr_exu_pos1" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_exu_pos1" value="1+"
          <?php echo ($elem_bvOs_dr_exu_pos1 == "+1" || $elem_bvOs_dr_exu_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_exu_pos1" >1+</label>
          </td><td><input id="elem_bvOs_dr_exu_pos2" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_exu_pos2" value="2+"
          <?php echo ($elem_bvOs_dr_exu_pos2 == "+2" || $elem_bvOs_dr_exu_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_exu_pos2" >2+</label>
          </td><td><input id="elem_bvOs_dr_exu_pos3" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_exu_pos3" value="3+"
          <?php echo ($elem_bvOs_dr_exu_pos3 == "+3" || $elem_bvOs_dr_exu_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_exu_pos3" >3+</label>
          </td><td><input id="elem_bvOs_dr_exu_pos4" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_exu_pos4" value="4+"
          <?php echo ($elem_bvOs_dr_exu_pos4 == "+4" || $elem_bvOs_dr_exu_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_exu_pos4" >4+</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/Hard Exudate"])){ echo $arr_exm_ext_htm["Vessels"]["DR/Hard Exudate"]; }  ?>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >Cotton Wool Spots</td>
          <td>
          <input id="elem_bvOd_dr_cws_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_cws_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_cws_Absent" >Absent</label>
          </td><td><input id="elem_bvOd_dr_cws_T" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_cws_T" value="T"
          <?php echo ($elem_bvOd_dr_cws_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_cws_T" >T</label>
          </td><td><input id="elem_bvOd_dr_cws_pos1" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_cws_pos1" value="1+"
          <?php echo ($elem_bvOd_dr_cws_pos1 == "+1" || $elem_bvOd_dr_cws_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_cws_pos1" >1+</label>
          </td><td><input id="elem_bvOd_dr_cws_pos2" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_cws_pos2" value="2+"
          <?php echo ($elem_bvOd_dr_cws_pos2 == "+2" || $elem_bvOd_dr_cws_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_cws_pos2" >2+</label>
          </td><td><input id="elem_bvOd_dr_cws_pos3" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_cws_pos3" value="3+"
          <?php echo ($elem_bvOd_dr_cws_pos3 == "+3" || $elem_bvOd_dr_cws_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_cws_pos3" >3+</label>
          </td><td><input id="elem_bvOd_dr_cws_pos4" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_cws_pos4" value="4+"
          <?php echo ($elem_bvOd_dr_cws_pos4 == "+4" || $elem_bvOd_dr_cws_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_cws_pos4" >4+</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >Cotton Wool Spots</td>
          <td>
          <input id="elem_bvOs_dr_cws_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_cws_Absent" value="Absent"
          <?php echo ($elem_bvOs_dr_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_cws_Absent" >Absent</label>
          </td><td><input id="elem_bvOs_dr_cws_T" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_cws_T" value="T"
          <?php echo ($elem_bvOs_dr_cws_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_cws_T" >T</label>
          </td><td><input id="elem_bvOs_dr_cws_pos1" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_cws_pos1" value="1+"
          <?php echo ($elem_bvOs_dr_cws_pos1 == "+1" || $elem_bvOs_dr_cws_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_cws_pos1" >1+</label>
          </td><td><input id="elem_bvOs_dr_cws_pos2" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_cws_pos2" value="2+"
          <?php echo ($elem_bvOs_dr_cws_pos2 == "+2" || $elem_bvOs_dr_cws_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_cws_pos2" >2+</label>
          </td><td><input id="elem_bvOs_dr_cws_pos3" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_cws_pos3" value="3+"
          <?php echo ($elem_bvOs_dr_cws_pos3 == "+3" || $elem_bvOs_dr_cws_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_cws_pos3" >3+</label>
          </td><td><input id="elem_bvOs_dr_cws_pos4" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_cws_pos4" value="4+"
          <?php echo ($elem_bvOs_dr_cws_pos4 == "+4" || $elem_bvOs_dr_cws_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_cws_pos4" >4+</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/Cotton Wool Spots"])){ echo $arr_exm_ext_htm["Vessels"]["DR/Cotton Wool Spots"]; }  ?>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >Focal Laser</td>
          <td>
          <input id="elem_bvOd_dr_fLaser_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_fLaser_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_fLaser_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_fLaser_Absent" >Absent</label>
          </td><td><input id="elem_bvOd_dr_fLaser_Present" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_fLaser_Present" value="Present"
          <?php echo ($elem_bvOd_dr_fLaser_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_fLaser_Present" >Present</label>
          </td><td colspan="4"><input id="elem_bvOd_dr_fLaser_comment" type="text"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_fLaser_comment"
          value="<?php echo $elem_bvOd_dr_fLaser_comment;?>" class="form-control">
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >Focal Laser</td>
          <td>
          <input id="elem_bvOs_dr_fLaser_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_fLaser_Absent" value="Absent"
          <?php echo ($elem_bvOs_dr_fLaser_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_fLaser_Absent" >Absent</label>
          </td><td><input id="elem_bvOs_dr_fLaser_Present" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_fLaser_Present" value="Present"
          <?php echo ($elem_bvOs_dr_fLaser_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_fLaser_Present" >Present</label>
          </td><td colspan="4"><input id="elem_bvOs_dr_fLaser_comment" type="text"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_fLaser_comment"
          value="<?php echo $elem_bvOs_dr_fLaser_comment;?>" class="form-control">
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/Focal Laser"])){ echo $arr_exm_ext_htm["Vessels"]["DR/Focal Laser"]; }  ?>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >PRP</td>
          <td>
          <input id="elem_bvOd_dr_prp_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_prp_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_prp_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_prp_Absent" >Absent</label>
          </td><td><input id="elem_bvOd_dr_prp_Partial" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_prp_Partial" value="Partial"
          <?php echo ($elem_bvOd_dr_prp_Partial == "Partial") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_prp_Partial" >Partial</label>
          </td><td colspan="2"><input id="elem_bvOd_dr_prp_Complete" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_prp_Complete" value="Complete"
          <?php echo ($elem_bvOd_dr_prp_Complete == "Complete") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_prp_Complete" >Complete</label>
          </td><td colspan="2"><input id="elem_bvOd_dr_prp_comment" type="text"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_prp_comment" value="<?php echo $elem_bvOd_dr_prp_comment  ;?>" class="form-control" >
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >PRP</td>
          <td>
          <input id="elem_bvOs_dr_prp_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_prp_Absent" value="Absent"
          <?php echo ($elem_bvOs_dr_prp_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_prp_Absent" >Absent</label>
          </td><td><input id="elem_bvOs_dr_prp_Partial" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_prp_Partial" value="Partial"
          <?php echo ($elem_bvOs_dr_prp_Partial == "Partial") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_prp_Partial" >Partial</label>
          </td><td colspan="2"><input id="elem_bvOs_dr_prp_Complete" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_prp_Complete" value="Complete"
          <?php echo ($elem_bvOs_dr_prp_Complete == "Complete") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_prp_Complete" >Complete</label>
          </td><td colspan="2"><input id="elem_bvOs_dr_prp_comment" type="text"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_prp_comment" value="<?php echo $elem_bvOs_dr_prp_comment  ;?>" class="form-control">
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/PRP"])){ echo $arr_exm_ext_htm["Vessels"]["DR/PRP"]; }  ?>

          <tr class="exmhlgcol  grp_DRBV <?php echo $cls_DRBV; ?>">
          <td >Neo vascularization</td>
          <td>
          <input id="elem_bvOd_dr_Neovasc_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_Neovasc_Absent" value="Absent"
          <?php echo ($elem_bvOd_dr_Neovasc_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_Neovasc_Absent" >Absent</label>
          </td><td colspan="5"><input id="elem_bvOd_dr_Neovasc_Present" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOd_dr_Neovasc_Present" value="Present"
          <?php echo ($elem_bvOd_dr_Neovasc_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_dr_Neovasc_Present" >Present</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('DRBV')" >BL</td>
          <td >Neo vascularization</td>
          <td>
          <input id="elem_bvOs_dr_Neovasc_Absent" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_Neovasc_Absent" value="Absent"
          <?php echo ($elem_bvOs_dr_Neovasc_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_Neovasc_Absent" >Absent</label>
          </td><td colspan="5"><input id="elem_bvOs_dr_Neovasc_Present" type="checkbox"  onclick="checkAbsent(this,'DRBV');"   name="elem_bvOs_dr_Neovasc_Present" value="Present"
          <?php echo ($elem_bvOs_dr_Neovasc_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_dr_Neovasc_Present" >Present</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR/Neovascularization"])){ echo $arr_exm_ext_htm["Vessels"]["DR/Neovascularization"]; }  ?>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["DR"])){ echo $arr_exm_ext_htm["Vessels"]["DR"]; }  ?>

          <tr class="exmhlgcol grp_handle grp_bvVasOcc <?php echo $cls_bvVasOcc; ?>" id="d_bvVasOcc">
          <td class="grpbtn" onclick="openSubGrp('bvVasOcc')">
            <label >Vascular<br/>Occlusion
            <span class="glyphicon <?php echo $arow_bvVasOcc; ?>"></span></label>
          </td>
          <td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'bvVasOcc');" id="elem_bvOd_vasocc_text" name="elem_bvOd_vasocc_text" class="form-control"><?php echo ($elem_bvOd_vasocc_text);?></textarea></td>

          <td align="center" class="bilat" onClick="check_bl('bvVasOcc')" >BL</td>
          <td class="grpbtn" onclick="openSubGrp('bvVasOcc')">
            <label >Vascular<br/>Occlusion
            <span class="glyphicon <?php echo $arow_bvVasOcc; ?>"></span></label>
          </td>
          <td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'bvVasOcc');" id="elem_bvOs_vasocc_text" name="elem_bvOs_vasocc_text" class="form-control"><?php echo ($elem_bvOs_vasocc_text);?></textarea></td>
          </tr>

          <tr class="exmhlgcol grp_bvVasOcc <?php echo $cls_bvVasOcc; ?>" >
          <td >BRVO</td>
          <td colspan="2">
          <input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brvo_st" name="elem_bvOd_vasocc_brvo_st" value="Superotemporal" <?php echo ($elem_bvOd_vasocc_brvo_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brvo_st" >Superotemporal</label>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brvo_it" name="elem_bvOd_vasocc_brvo_it" value="Inferotemporal" <?php echo ($elem_bvOd_vasocc_brvo_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brvo_it" >Inferotemporal</label>
          </td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brvo_sn" name="elem_bvOd_vasocc_brvo_sn" value="Superonasal" <?php echo ($elem_bvOd_vasocc_brvo_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brvo_sn" >Superonasal</label>
          </td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brvo_in" name="elem_bvOd_vasocc_brvo_in" value="Inferonasal" <?php echo ($elem_bvOd_vasocc_brvo_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brvo_in" >Inferonasal</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('bvVasOcc')" rowspan="2">BL</td>
          <td >BRVO</td>
          <td colspan="2">
          <input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brvo_st" name="elem_bvOs_vasocc_brvo_st" value="Superotemporal" <?php echo ($elem_bvOs_vasocc_brvo_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brvo_st" >Superotemporal</label>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brvo_it" name="elem_bvOs_vasocc_brvo_it" value="Inferotemporal" <?php echo ($elem_bvOs_vasocc_brvo_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brvo_it" >Inferotemporal</label>
          </td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brvo_sn" name="elem_bvOs_vasocc_brvo_sn" value="Superonasal" <?php echo ($elem_bvOs_vasocc_brvo_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brvo_sn" >Superonasal</label>
          </td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brvo_in" name="elem_bvOs_vasocc_brvo_in" value="Inferonasal" <?php echo ($elem_bvOs_vasocc_brvo_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brvo_in" >Inferonasal</label>
          </td>
          </tr>


          <tr class="exmhlgcol grp_bvVasOcc <?php echo $cls_bvVasOcc; ?>" >
          <td ></td>
          <td colspan="2">
          <label class="selevel3">Macular edema</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme')" class="bvme" id="elem_bvOd_vasocc_brvo_MEPresent" name="elem_bvOd_vasocc_brvo_MEPresent" value="Macular Edema Present" <?php echo ($elem_bvOd_vasocc_brvo_MEPresent == "Macular Edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brvo_MEPresent" >Present</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme')" class="bvme" id="elem_bvOd_vasocc_brvo_MEAbsent" name="elem_bvOd_vasocc_brvo_MEAbsent" value="Macular Edema Absent" <?php echo ($elem_bvOd_vasocc_brvo_MEAbsent == "Macular Edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brvo_MEAbsent" >Absent</label>
          </td><td colspan="2"><input type="text"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"  name="elem_bvOd_vasocc_brvo_text" value="<?php echo ($elem_bvOd_vasocc_brvo_text);?>">
          </td>
          <td ></td>
          <td colspan="2">
          <label class="selevel3">Macular edema</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme')" class="bvme"  id="elem_bvOs_vasocc_brvo_MEPresent" name="elem_bvOs_vasocc_brvo_MEPresent" value="Macular edema Present" <?php echo ($elem_bvOs_vasocc_brvo_MEPresent == "Macular edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brvo_MEPresent" >Present</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme')" class="bvme"  id="elem_bvOs_vasocc_brvo_MEAbsent" name="elem_bvOs_vasocc_brvo_MEAbsent" value="Macular edema Absent" <?php echo ($elem_bvOs_vasocc_brvo_MEAbsent == "Macular edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brvo_MEAbsent" >Absent</label>
          </td><td colspan="2"><input type="text"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   name="elem_bvOs_vasocc_brvo_text" value="<?php echo ($elem_bvOs_vasocc_brvo_text);?>">
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Vascular Occlusion/BRVO"])){ echo $arr_exm_ext_htm["Vessels"]["Vascular Occlusion/BRVO"]; }  ?>

          <tr class="exmhlgcol grp_VasOcc <?php echo $cls_bvVasOcc; ?>" >
          <td >CRVO</td>
          <td colspan="2">
          <input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_crvo_Present" name="elem_bvOd_vasocc_crvo_Present" value="Present" <?php echo ($elem_bvOd_vasocc_crvo_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_crvo_Present" >Present</label>
          </td><td colspan="2">
          <label class="selevel3"  >Macular edema</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme');" class="bvme"  id="elem_bvOd_vasocc_crvo_MEPresent" name="elem_bvOd_vasocc_crvo_MEPresent" value="Macular Edema Present" <?php echo ($elem_bvOd_vasocc_crvo_MEPresent == "Macular Edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_crvo_MEPresent" >Present</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme');" class="bvme"  id="elem_bvOd_vasocc_crvo_MEAbsent" name="elem_bvOd_vasocc_crvo_MEAbsent" value="Macular Edema Absent" <?php echo ($elem_bvOd_vasocc_crvo_MEAbsent == "Macular Edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_crvo_MEAbsent" >Absent</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('bvVasOcc')" >BL</td>
          <td >CRVO</td>
          <td colspan="2">
          <input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_crvo_Present" name="elem_bvOs_vasocc_crvo_Present" value="Present" <?php echo ($elem_bvOs_vasocc_crvo_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_crvo_Present" >Present</label>
          </td>
          <td colspan="2">
          <label class="selevel3" >Macular edema</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme')" class="bvme" id="elem_bvOs_vasocc_crvo_MEPresent" name="elem_bvOs_vasocc_crvo_MEPresent" value="Macular Edema Present" <?php echo ($elem_bvOs_vasocc_crvo_MEPresent == "Macular Edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_crvo_MEPresent" >Present</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvme')" class="bvme" id="elem_bvOs_vasocc_crvo_MEAbsent" name="elem_bvOs_vasocc_crvo_MEAbsent" value="Macular Edema Absent" <?php echo ($elem_bvOs_vasocc_crvo_MEAbsent == "Macular Edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_crvo_MEAbsent" >Absent</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Vascular Occlusion/CRVO"])){ echo $arr_exm_ext_htm["Vessels"]["Vascular Occlusion/CRVO"]; }  ?>

          <tr class="exmhlgcol grp_bvVasOcc <?php echo $cls_bvVasOcc; ?>" >
          <td >BRAO</td>
          <td colspan="2">
          <input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brao_st" name="elem_bvOd_vasocc_brao_st" value="Superotemporal" <?php echo ($elem_bvOd_vasocc_brao_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brao_st" >Superotemporal</label>
          </td><td colspan="2"><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brao_it" name="elem_bvOd_vasocc_brao_it" value="Inferotemporal" <?php echo ($elem_bvOd_vasocc_brao_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brao_it" >Inferotemporal</label>
          </td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brao_sn" name="elem_bvOd_vasocc_brao_sn" value="Superonasal" <?php echo ($elem_bvOd_vasocc_brao_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brao_sn" >Superonasal</label>
          </td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_brao_in" name="elem_bvOd_vasocc_brao_in" value="Inferonasal" <?php echo ($elem_bvOd_vasocc_brao_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_brao_in" >Inferonasal</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('bvVasOcc')" >BL</td>
          <td >BRAO</td>
          <td colspan="2">
          <input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brao_st" name="elem_bvOs_vasocc_brao_st" value="Superotemporal" <?php echo ($elem_bvOs_vasocc_brao_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brao_st" >Superotemporal</label>
          </td><td colspan="2"><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brao_it" name="elem_bvOs_vasocc_brao_it" value="Inferotemporal" <?php echo ($elem_bvOs_vasocc_brao_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brao_it" >Inferotemporal</label>
          </td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brao_sn" name="elem_bvOs_vasocc_brao_sn" value="Superonasal" <?php echo ($elem_bvOs_vasocc_brao_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brao_sn" >Superonasal</label>
          </td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_brao_in" name="elem_bvOs_vasocc_brao_in" value="Inferonasal" <?php echo ($elem_bvOs_vasocc_brao_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_brao_in" >Inferonasal</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Vascular Occlusion/BRAO"])){ echo $arr_exm_ext_htm["Vessels"]["Vascular Occlusion/BRAO"]; }  ?>

          <tr class="exmhlgcol grp_bvVasOcc <?php echo $cls_bvVasOcc; ?>" >
          <td >CRAO</td>
          <td>
          <input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOd_vasocc_crao_Present" name="elem_bvOd_vasocc_crao_Present" value="Present" <?php echo ($elem_bvOd_vasocc_crao_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_crao_Present" >Present</label>
          </td><td colspan="3">
          <label class="selevel3_cas">Ciliary Artery Sparing</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvcas')" class="bvcas"  id="elem_bvOd_vasocc_crao_CRSPresent" name="elem_bvOd_vasocc_crao_CRSPresent" value="Ciliary Artery Sparing Present" <?php echo ($elem_bvOd_vasocc_crao_CRSPresent == "Ciliary Artery Sparing Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_crao_CRSPresent" >Present</label>
          </td><td ><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvcas')" class="bvcas"  id="elem_bvOd_vasocc_crao_CRSAbsent" name="elem_bvOd_vasocc_crao_CRSAbsent" value="Ciliary Artery Sparing Absent" <?php echo ($elem_bvOd_vasocc_crao_CRSAbsent == "Ciliary Artery Sparing Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasocc_crao_CRSAbsent" >Absent</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('bvVasOcc')" >BL</td>
          <td >CRAO</td>
          <td>
          <input type="checkbox"  onclick="checkwnls();checkSymClr(this,'bvVasOcc');"   id="elem_bvOs_vasocc_crao_Present" name="elem_bvOs_vasocc_crao_Present" value="Present" <?php echo ($elem_bvOs_vasocc_crao_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_crao_Present" >Present</label>
          </td><td colspan="3">
          <label class="selevel3_cas">Ciliary Artery Sparing</label>
          </td><td><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvcas')" class="bvcas"  id="elem_bvOs_vasocc_crao_CRSPresent" name="elem_bvOs_vasocc_crao_CRSPresent" value="Ciliary Artery Sparing Present" <?php echo ($elem_bvOs_vasocc_crao_CRSPresent == "Ciliary Artery Sparing Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_crao_CRSPresent" >Present</label>
          </td><td ><input type="checkbox"  onclick="checkAbsent(this,'bvVasOcc','.bvcas')" class="bvcas"  id="elem_bvOs_vasocc_crao_CRSAbsent" name="elem_bvOs_vasocc_crao_CRSAbsent" value="Ciliary Artery Sparing Absent" <?php echo ($elem_bvOs_vasocc_crao_CRSAbsent == "Ciliary Artery Sparing Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasocc_crao_CRSAbsent" >Absent</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Vascular Occlusion/CRAO"])){ echo $arr_exm_ext_htm["Vessels"]["Vascular Occlusion/CRAO"]; }  ?>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Vascular Occlusion"])){ echo $arr_exm_ext_htm["Vessels"]["Vascular Occlusion"]; }  ?>

          <tr id="d_bvVasShth" class="grp_bvVasShth">
          <td >Vascular Sheathing</td>
          <td >
          <select name="elem_bvOd_vasshth_Measure" onchange="checkwnls();" class="form-control">
            <option value=""></option>
            <?php
            foreach($arrMeasure as $km=>$vm){
              $sel = ($vm==$elem_bvOd_vasshth_Measure) ? "selected" : "";
              echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
            }
            ?>
          </select>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOd_vasshth_supTemp" name="elem_bvOd_vasshth_supTemp" value="Superotemporal"
            <?php echo ($elem_bvOd_vasshth_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasshth_supTemp" >Superotemporal</label>
          </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOd_vasshth_infTemp" name="elem_bvOd_vasshth_infTemp" value="Inferotemporal"
            <?php echo ($elem_bvOd_vasshth_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasshth_infTemp" >Inferotemporal</label>

          </td>
          <td align="center" class="bilat" onClick="check_bl('bvVasShth')" rowspan="2">BL</td>
          <td >Vascular Sheathing</td>
          <td >
          <select name="elem_bvOs_vasshth_Measure" onchange="checkwnls();" class="form-control">
          <option value=""></option>
          <?php
          foreach($arrMeasure as $km=>$vm){
          $sel = ($vm==$elem_bvOs_vasshth_Measure) ? "selected" : "";
          echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
          }
          ?>
          </select>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOs_vasshth_supTemp" name="elem_bvOs_vasshth_supTemp" value="Superotemporal"
          <?php echo ($elem_bvOs_vasshth_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasshth_supTemp" >Superotemporal</label>
          </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOs_vasshth_infTemp" name="elem_bvOs_vasshth_infTemp" value="Inferotemporal"
          <?php echo ($elem_bvOs_vasshth_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasshth_infTemp" >Inferotemporal</label>
          </td>
          </tr>

          <tr class="grp_bvVasShth">
          <td ></td>
          <td ></td>
          <td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOd_vasshth_supNasal" name="elem_bvOd_vasshth_supNasal" value="Superonasal"
          <?php echo ($elem_bvOd_vasshth_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasshth_supNasal" >Superonasal</label>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOd_vasshth_infNasal" name="elem_bvOd_vasshth_infNasal" value="Inferonasal"
          <?php echo ($elem_bvOd_vasshth_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_vasshth_infNasal" >Inferonasal</label>
          </td><td ><input type="text"  onclick="checkwnls();"   name="elem_bvOd_vasshth_comment" name="elem_bvOd_vasshth_comment" value="<?php echo $elem_bvOd_vasshth_comment ;?>" class="form-control" >
          </td>

          <td ></td>
          <td ></td>
          <td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOs_vasshth_supNasal" name="elem_bvOs_vasshth_supNasal" value="Superonasal"
          <?php echo ($elem_bvOs_vasshth_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasshth_supNasal" >Superonasal</label>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_bvOs_vasshth_infNasal" name="elem_bvOs_vasshth_infNasal" value="Inferonasal"
          <?php echo ($elem_bvOs_vasshth_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_vasshth_infNasal" >Inferonasal</label>
          </td><td ><input type="text"  onclick="checkwnls();"   name="elem_bvOs_vasshth_comment" value="<?php echo $elem_bvOs_vasshth_comment ;?>" class="form-control" >
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Vascular Sheathing"])){ echo $arr_exm_ext_htm["Vessels"]["Vascular Sheathing"]; }  ?>

          <tr id="d_bvNevus" class="grp_bvNevus">
          <td >Nevus</td>
          <td colspan="3">
          <div class="form-group form-inline sptrDiscarea">
          Disc Area
          <select name="elem_bvOd_nevus_discarea" onChange="checkwnls()" class="form-control">
          <option></option>
          <option value="Disc Area 1/2" <?php if($elem_bvOd_nevus_discarea=="Disc Area 1/2")echo "selected";?>>1/2</option>
          <?php
          for($i=1;$i<=5;$i++){
          $sel= ($elem_bvOd_nevus_discarea=="Disc Area ".$i)? "selected" : "";
          echo "<option value=\"Disc Area ".$i."\" ".$sel.">".$i."</option>";
          $j=$i+"0.5";
          $sel= ($elem_bvOd_nevus_discarea=="Disc Area ".$j)? "selected" : "";
          echo "<option value=\"Disc Area ".$j."\" ".$sel.">".$j."</option>";
          }
          ?>
          </select>
          X
          <select name="elem_bvOd_nevus_discarea_verti" onChange="checkwnls()" class="form-control">
          <option></option>
          <option value="X 1/2" <?php if($elem_bvOd_nevus_discarea_verti=="X 1/2")echo "selected";?>>1/2</option>
          <?php
          for($i=1;$i<=5;$i++){
          $sel= ($elem_bvOd_nevus_discarea_verti=="X ".$i)? "selected" : "";
          echo "<option value=\"X ".$i."\" ".$sel.">".$i."</option>";

          $j=$i+"0.5";
          $sel= ($elem_bvOd_nevus_discarea_verti=="X ".$j)? "selected" : "";
          echo "<option value=\"X ".$j."\" ".$sel.">".$j."</option>";
          }
          ?>
          </select>
          </div>

          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls()"   id="elem_bvOd_nevus_supTemp" name="elem_bvOd_nevus_supTemp" value="Superotemporal"
          <?php echo ($elem_bvOd_nevus_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_nevus_supTemp" >Superotemporal</label>
        </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_bvOd_nevus_infTemp" name="elem_bvOd_nevus_infTemp" value="Inferotemporal"
          <?php echo ($elem_bvOd_nevus_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_nevus_infTemp" >Inferotemporal</label>
          </td>
          <td align="center" class="bilat" onClick="check_bl('bvNevus')" rowspan="2">BL</td>
          <td >Nevus</td>
          <td colspan="3">
          <div class="form-group form-inline sptrDiscarea">
          Disc Area
          <select name="elem_bvOs_nevus_discarea" onChange="checkwnls()" class="form-control">
          <option></option>
          <option value="Disc Area 1/2" <?php if($elem_bvOs_nevus_discarea=="Disc Area 1/2")echo "selected";?>>1/2</option>
          <?php
          for($i=1;$i<=5;$i++){
          $sel= ($elem_bvOs_nevus_discarea=="Disc Area ".$i)? "selected" : "";
          echo "<option value=\"Disc Area ".$i."\" ".$sel.">".$i."</option>";
          $j=$i+"0.5";
          $sel= ($elem_bvOs_nevus_discarea=="Disc Area ".$j)? "selected" : "";
          echo "<option value=\"Disc Area ".$j."\" ".$sel.">".$j."</option>";
          }
          ?>
          </select>
          X
          <select name="elem_bvOs_nevus_discarea_verti" onChange="checkwnls()" class="form-control">
          <option></option>
          <option value="X 1/2" <?php if($elem_bvOs_nevus_discarea_verti=="X 1/2")echo "selected";?>>1/2</option>
          <?php
          for($i=1;$i<=5;$i++){
          $sel= ($elem_bvOs_nevus_discarea_verti=="X ".$i)? "selected" : "";
          echo "<option value=\"X ".$i."\" ".$sel.">".$i."</option>";
          $j=$i+"0.5";
          $sel= ($elem_bvOs_nevus_discarea_verti=="X ".$j)? "selected" : "";
          echo "<option value=\"X ".$j."\" ".$sel.">".$j."</option>";
          }
          ?>
          </select>
          </div>
          </td><td colspan="2"><input type="checkbox"  onclick="checkwnls()"   id="elem_bvOs_nevus_supTemp" name="elem_bvOs_nevus_supTemp" value="Superotemporal"
          <?php echo ($elem_bvOs_nevus_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_nevus_supTemp" >Superotemporal</label>
        </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_bvOs_nevus_infTemp"  name="elem_bvOs_nevus_infTemp" value="Inferotemporal"
          <?php echo ($elem_bvOs_nevus_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_nevus_infTemp" >Inferotemporal</label>
          </td>
          </tr>

          <tr class="grp_bvNevus">
          <td ></td>
          <td colspan="3">
          <span class="sptrDiscarea"></span>
          </td><td colspan="2"><input  type="checkbox"  onclick="checkwnls()"   id="elem_bvOd_nevus_supNasal" name="elem_bvOd_nevus_supNasal" value="Superonasal"
          <?php echo ($elem_bvOd_nevus_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_nevus_supNasal" >Superonasal</label>
        </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_bvOd_nevus_Inferonasal" name="elem_bvOd_nevus_Inferonasal" value="Inferonasal"
          <?php echo ($elem_bvOd_nevus_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOd_nevus_Inferonasal" >Inferonasal</label>
          </td>
          <td ></td>
          <td colspan="3">
          <span class="sptrDiscarea"></span>
          </td><td colspan="2"><input  type="checkbox"  onclick="checkwnls()"   id="elem_bvOs_nevus_supNasal"  name="elem_bvOs_nevus_supNasal" value="Superonasal"
          <?php echo ($elem_bvOs_nevus_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_nevus_supNasal" >Superonasal</label>
        </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_bvOs_nevus_Inferonasal"  name="elem_bvOs_nevus_Inferonasal" value="Inferonasal"
          <?php echo ($elem_bvOs_nevus_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_bvOs_nevus_Inferonasal" >Inferonasal</label>
          </td>
          </tr>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Nevus"])){ echo $arr_exm_ext_htm["Vessels"]["Nevus"]; }  ?>
          <?php if(isset($arr_exm_ext_htm["Vessels"]["Main"])){ echo $arr_exm_ext_htm["Vessels"]["Main"]; }  ?>

          <tr id="d_adOpt_bv">
          <td >Comments</td>
          <td colspan="6" ><textarea  onblur="checkwnls()" id="elem_bvAdOptionsOd" name="elem_bvAdOptionsOd" class="form-control"><?php echo ($elem_bvAdOptionsOd);?></textarea></td>
          <td align="center" class="bilat" onClick="check_bl('adOpt_bv')">BL</td>
          <td >Comments</td>
          <td colspan="6"><textarea  onblur="checkwnls()" id="elem_bvAdOptionsOs" name="elem_bvAdOptionsOs" class="form-control"><?php echo ($elem_bvAdOptionsOs);?></textarea></td>
          </tr>

        </table>
      </div>
    </div>
  <!-- BloodVessels -->

  <!-- Periphery -->
  <div role="tabpanel" class="tab-pane <?php echo (3 == $defTabKey) ? "active" : "" ?>" id="div3">
    <div class="examhd form-inline">
      <div class="row">
        <div class="col-sm-1">
        </div>
        <div class="col-sm-3">
            <input type="checkbox"  id="elem_periNotExamined_peri" name="elem_periNotExamined_peri" value="1"
            <?php echo ($elem_periNotExamined_peri == "1") ? "checked=\"checked\"" : "" ;?>  onclick="checkPneEye(this)" >
            <label class="lbl_nochange" for="elem_periNotExamined_peri">Periphery not examined</label>
            <select id="elem_peri_ne_eye_peri" name="elem_peri_ne_eye_peri" class="form-control" onchange="checkPneEye(this)" >
            <option value=""></option>
            <option value="OU"  <?php if($elem_peri_ne_eye_peri=="OU") { echo "selected";} ?>>OU</option>
            <option value="OD" <?php if($elem_peri_ne_eye_peri=="OD") { echo "selected";} ?>>OD</option>
            <option value="OS" <?php if($elem_peri_ne_eye_peri=="OS") { echo "selected";} ?>>OS</option>
            </select>
        </div>
        <div class="col-sm-3">
        </div>
        <div class="col-sm-1">
        </div>
        <div class="col-sm-4">
          <span id="examFlag" class="glyphicon flagWnl "></span>
          <button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

          <input type="checkbox" id="elem_noChangePeri"  name="elem_noChangePeri" value="1" onClick="setNC2();"
                <?php echo ($elem_ncPeri == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
          <label class="lbl_nochange frcb" for="elem_noChangePeri">NO Change</label>

        </div>
      </div>
    </div>
    <div class="clearfix"> </div>
    <div class="table-responsive">
  		<table class="table table-bordered table-striped" >
  			<tr>
  			<td colspan="7" align="center">
  				<span class="flgWnl_2" id="flagWnlOd" ></span>
  				<div class="checkboxO"><label class="od cbold">OD</label></div>
  			</td>
  			<td width="67" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
  			<td colspan="7" align="center">
  				<span class="flgWnl_2" id="flagWnlOs" ></span>
  				<div class="checkboxO"><label class="os cbold">OS</label></div>
  			</td>
  			</tr>

        <tr class="exmhlgcol grp_handle grp_periPDeg <?php echo $cls_periPDeg; ?>" id="d_periPDeg">
        <td class="grpbtn" colspan="2" onclick="openSubGrp('periPDeg')">
          <label >Peripheral Degeneration
          <span class="glyphicon <?php echo $arow_periPDeg; ?>"></span></label>
        </td>
        <td colspan="5">&nbsp;</td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')">BL</td>
        <td class="grpbtn" colspan="2" onclick="openSubGrp('periPDeg')">
          <label >Peripheral Degeneration
          <span class="glyphicon <?php echo $arow_periPDeg; ?>"></span></label>
        </td>
        <td colspan="6">&nbsp;</td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>" >
        <td >Atrophic changes</td>
        <td>
        <select name="elem_periOd_pdeg_atroChan_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOd_pdeg_atroChan_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_atroChan_supTemp" name="elem_periOd_pdeg_atroChan_supTemp" value="Superotemporal"
        <?php echo ($elem_periOd_pdeg_atroChan_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_atroChan_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_atroChan_infTemp" name="elem_periOd_pdeg_atroChan_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOd_pdeg_atroChan_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_atroChan_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')" rowspan="2">BL</td>
        <td >Atrophic changes</td>
        <td>
        <select name="elem_periOs_pdeg_atroChan_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOs_pdeg_atroChan_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_atroChan_supTemp" name="elem_periOs_pdeg_atroChan_supTemp" value="Superotemporal"
        <?php echo ($elem_periOs_pdeg_atroChan_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_atroChan_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_atroChan_infTemp" name="elem_periOs_pdeg_atroChan_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOs_pdeg_atroChan_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_atroChan_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_atroChan_supNasal" name="elem_periOd_pdeg_atroChan_supNasal" value="Superonasal"
        <?php echo ($elem_periOd_pdeg_atroChan_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_atroChan_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_atroChan_infNasal" name="elem_periOd_pdeg_atroChan_infNasal" value="Inferonasal"
        <?php echo ($elem_periOd_pdeg_atroChan_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_atroChan_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOd_pdeg_atroChan_comment" value="<?php echo $elem_periOd_pdeg_atroChan_comment ;?>" class="form-control" >
        </td>
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_atroChan_supNasal" name="elem_periOs_pdeg_atroChan_supNasal" value="Superonasal"
        <?php echo ($elem_periOs_pdeg_atroChan_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_atroChan_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_atroChan_infNasal" name="elem_periOs_pdeg_atroChan_infNasal" value="Inferonasal"
        <?php echo ($elem_periOs_pdeg_atroChan_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_atroChan_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOs_pdeg_atroChan_comment" value="<?php echo $elem_periOs_pdeg_atroChan_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Atrophic changes"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Atrophic changes"]; }  ?>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td >Equatorial Drusen</td>
        <td>
        <select name="elem_periOd_pdeg_EqDru_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOd_pdeg_EqDru_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_EqDru_supTemp" name="elem_periOd_pdeg_EqDru_supTemp" value="Superotemporal"
        <?php echo ($elem_periOd_pdeg_EqDru_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_EqDru_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_EqDru_infTemp" name="elem_periOd_pdeg_EqDru_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOd_pdeg_EqDru_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_EqDru_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')" rowspan="2">BL</td>
        <td >Equatorial Drusen</td>
        <td>
        <select name="elem_periOs_pdeg_EqDru_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOs_pdeg_EqDru_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_EqDru_supTemp" name="elem_periOs_pdeg_EqDru_supTemp" value="Superotemporal"
        <?php echo ($elem_periOs_pdeg_EqDru_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_EqDru_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_EqDru_infTemp" name="elem_periOs_pdeg_EqDru_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOs_pdeg_EqDru_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_EqDru_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_EqDru_supNasal" name="elem_periOd_pdeg_EqDru_supNasal" value="Superonasal"
        <?php echo ($elem_periOd_pdeg_EqDru_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_EqDru_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_EqDru_infNasal" name="elem_periOd_pdeg_EqDru_infNasal" value="Inferonasal"
        <?php echo ($elem_periOd_pdeg_EqDru_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_EqDru_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOd_pdeg_EqDru_comment" value="<?php echo $elem_periOd_pdeg_EqDru_comment ;?>" class="form-control" >
        </td>
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_EqDru_supNasal" name="elem_periOs_pdeg_EqDru_supNasal" value="Superonasal"
        <?php echo ($elem_periOs_pdeg_EqDru_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_EqDru_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_EqDru_infNasal" name="elem_periOs_pdeg_EqDru_infNasal" value="Inferonasal"
        <?php echo ($elem_periOs_pdeg_EqDru_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_EqDru_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOs_pdeg_EqDru_comment" value="<?php echo $elem_periOs_pdeg_EqDru_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Equatorial Drusen"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Equatorial Drusen"]; }  ?>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td >Lattice Degeneration</td>
        <td>
        <select name="elem_periOd_pdeg_LatDeg_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOd_pdeg_LatDeg_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_LatDeg_supTemp" name="elem_periOd_pdeg_LatDeg_supTemp" value="Superotemporal"
        <?php echo ($elem_periOd_pdeg_LatDeg_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_LatDeg_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_LatDeg_infTemp" name="elem_periOd_pdeg_LatDeg_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOd_pdeg_LatDeg_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_LatDeg_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')" rowspan="2">BL</td>
        <td >Lattice Degeneration</td>
        <td>
        <select name="elem_periOs_pdeg_LatDeg_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOs_pdeg_LatDeg_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_LatDeg_supTemp" name="elem_periOs_pdeg_LatDeg_supTemp" value="Superotemporal"
        <?php echo ($elem_periOs_pdeg_LatDeg_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_LatDeg_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_LatDeg_infTemp" name="elem_periOs_pdeg_LatDeg_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOs_pdeg_LatDeg_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_LatDeg_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_LatDeg_supNasal" name="elem_periOd_pdeg_LatDeg_supNasal" value="Superonasal"
        <?php echo ($elem_periOd_pdeg_LatDeg_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_LatDeg_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_LatDeg_infNasal" name="elem_periOd_pdeg_LatDeg_infNasal" value="Inferonasal"
        <?php echo ($elem_periOd_pdeg_LatDeg_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_LatDeg_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOd_pdeg_LatDeg_comment" value="<?php echo $elem_periOd_pdeg_LatDeg_comment ;?>" class="form-control" >
        </td>
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_LatDeg_supNasal" name="elem_periOs_pdeg_LatDeg_supNasal" value="Superonasal"
        <?php echo ($elem_periOs_pdeg_LatDeg_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_LatDeg_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_LatDeg_infNasal" name="elem_periOs_pdeg_LatDeg_infNasal" value="Inferonasal"
        <?php echo ($elem_periOs_pdeg_LatDeg_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_LatDeg_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOs_pdeg_LatDeg_comment" value="<?php echo $elem_periOs_pdeg_LatDeg_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Lattice Degeneration"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Lattice Degeneration"]; }  ?>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td >Reticular Changes</td>
        <td>
        <select name="elem_periOd_pdeg_RetiCh_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOd_pdeg_RetiCh_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_RetiCh_supTemp" name="elem_periOd_pdeg_RetiCh_supTemp" value="Superotemporal"
        <?php echo ($elem_periOd_pdeg_RetiCh_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_RetiCh_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_RetiCh_infTemp" name="elem_periOd_pdeg_RetiCh_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOd_pdeg_RetiCh_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_RetiCh_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')" rowspan="2">BL</td>
        <td >Reticular Changes</td>
        <td>
        <select name="elem_periOs_pdeg_RetiCh_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOs_pdeg_RetiCh_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_RetiCh_supTemp" name="elem_periOs_pdeg_RetiCh_supTemp" value="Superotemporal"
        <?php echo ($elem_periOs_pdeg_RetiCh_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_RetiCh_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_RetiCh_infTemp" name="elem_periOs_pdeg_RetiCh_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOs_pdeg_RetiCh_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_RetiCh_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_RetiCh_supNasal" name="elem_periOd_pdeg_RetiCh_supNasal" value="Superonasal"
        <?php echo ($elem_periOd_pdeg_RetiCh_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_RetiCh_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_RetiCh_infNasal" name="elem_periOd_pdeg_RetiCh_infNasal" value="Inferonasal"
        <?php echo ($elem_periOd_pdeg_RetiCh_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_RetiCh_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOd_pdeg_RetiCh_comment" value="<?php echo $elem_periOd_pdeg_RetiCh_comment ;?>" class="form-control" >
        </td>
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_RetiCh_supNasal" name="elem_periOs_pdeg_RetiCh_supNasal" value="Superonasal"
        <?php echo ($elem_periOs_pdeg_RetiCh_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_RetiCh_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_RetiCh_infNasal" name="elem_periOs_pdeg_RetiCh_infNasal" value="Inferonasal"
        <?php echo ($elem_periOs_pdeg_RetiCh_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_RetiCh_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOs_pdeg_RetiCh_comment" value="<?php echo $elem_periOs_pdeg_RetiCh_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Reticular Changes"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Reticular Changes"]; }  ?>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td >Retinoschisis</td>
        <td>
        <select name="elem_periOd_pdeg_Rtchs_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOd_pdeg_Rtchs_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_Rtchs_supTemp" name="elem_periOd_pdeg_Rtchs_supTemp" value="Superotemporal"
        <?php echo ($elem_periOd_pdeg_Rtchs_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_Rtchs_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_Rtchs_infTemp" name="elem_periOd_pdeg_Rtchs_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOd_pdeg_Rtchs_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_Rtchs_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')" rowspan="2">BL</td>
        <td >Retinoschisis</td>
        <td>
        <select name="elem_periOs_pdeg_Rtchs_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOs_pdeg_Rtchs_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_Rtchs_supTemp" name="elem_periOs_pdeg_Rtchs_supTemp" value="Superotemporal"
        <?php echo ($elem_periOs_pdeg_Rtchs_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_Rtchs_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_Rtchs_infTemp" name="elem_periOs_pdeg_Rtchs_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOs_pdeg_Rtchs_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_Rtchs_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_Rtchs_supNasal" name="elem_periOd_pdeg_Rtchs_supNasal" value="Superonasal"
        <?php echo ($elem_periOd_pdeg_Rtchs_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_Rtchs_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_Rtchs_infNasal" name="elem_periOd_pdeg_Rtchs_infNasal" value="Inferonasal"
        <?php echo ($elem_periOd_pdeg_Rtchs_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_Rtchs_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOd_pdeg_Rtchs_comment" value="<?php echo $elem_periOd_pdeg_Rtchs_comment ;?>" class="form-control" >
        </td>

        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_Rtchs_supNasal" name="elem_periOs_pdeg_Rtchs_supNasal" value="Superonasal"
        <?php echo ($elem_periOs_pdeg_Rtchs_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_Rtchs_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_Rtchs_infNasal" name="elem_periOs_pdeg_Rtchs_infNasal" value="Inferonasal"
        <?php echo ($elem_periOs_pdeg_Rtchs_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_Rtchs_infNasal" >Inferonasal</label>
        </td><td ><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOs_pdeg_Rtchs_comment" value="<?php echo $elem_periOs_pdeg_Rtchs_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Retinoschisis"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/Retinoschisis"]; }  ?>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td >WWP</td>
        <td>
        <select name="elem_periOd_pdeg_wwp_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOd_pdeg_wwp_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_wwp_supTemp" name="elem_periOd_pdeg_wwp_supTemp" value="Superotemporal"
        <?php echo ($elem_periOd_pdeg_wwp_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_wwp_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_wwp_infTemp" name="elem_periOd_pdeg_wwp_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOd_pdeg_wwp_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_wwp_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periPDeg')" rowspan="2">BL</td>
        <td >WWP</td>
        <td>
        <select name="elem_periOs_pdeg_wwp_Measure" onchange="checkwnls();checkSymClr(this,'periPDeg');" class="form-control">
        <option value=""></option>
        <?php
        foreach($arrMeasure as $km=>$vm){
        $sel = ($vm==$elem_periOs_pdeg_wwp_Measure) ? "selected" : "";
        echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
        }
        ?>
        </select>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_wwp_supTemp" name="elem_periOs_pdeg_wwp_supTemp" value="Superotemporal"
        <?php echo ($elem_periOs_pdeg_wwp_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_wwp_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_wwp_infTemp" name="elem_periOs_pdeg_wwp_infTemp" value="Inferotemporal"
        <?php echo ($elem_periOs_pdeg_wwp_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_wwp_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr class="exmhlgcol grp_periPDeg <?php echo $cls_periPDeg; ?>">
        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_wwp_supNasal" name="elem_periOd_pdeg_wwp_supNasal" value="Superonasal"
        <?php echo ($elem_periOd_pdeg_wwp_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_wwp_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOd_pdeg_wwp_infNasal" name="elem_periOd_pdeg_wwp_infNasal" value="Inferonasal"
        <?php echo ($elem_periOd_pdeg_wwp_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pdeg_wwp_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOd_pdeg_wwp_comment" value="<?php echo $elem_periOd_pdeg_wwp_comment ;?>" class="form-control"  >
        </td>

        <td ></td>
        <td></td>
        <td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_wwp_supNasal" name="elem_periOs_pdeg_wwp_supNasal" value="Superonasal"
        <?php echo ($elem_periOs_pdeg_wwp_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_wwp_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   id="elem_periOs_pdeg_wwp_infNasal" name="elem_periOs_pdeg_wwp_infNasal" value="Inferonasal"
        <?php echo ($elem_periOs_pdeg_wwp_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pdeg_wwp_infNasal" >Inferonasal</label>
        </td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'periPDeg');"   name="elem_periOs_pdeg_wwp_comment" value="<?php echo $elem_periOs_pdeg_wwp_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/WWP"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration/WWP"]; }  ?>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Degeneration"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Degeneration"]; }  ?>


        <tr id="d_PRH" class="grp_PRH">
        <td >Peripheral Retinal Hemorrhage</td>
        <td >
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pih_Absent" name="elem_periOd_pih_Absent" value="Absent" <?php echo ($elem_periOd_pih_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pih_Absent" >Absent</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pih_supTemp" name="elem_periOd_pih_supTemp" value="Superotemporal" <?php echo ($elem_periOd_pih_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pih_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pih_infTemp" name="elem_periOd_pih_infTemp" value="Inferotemporal" <?php echo ($elem_periOd_pih_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pih_infTemp" >Inferotemporal</label>
        </td>

        <td align="center" class="bilat" onClick="check_bl('PRH')" rowspan="2">BL</td>
        <td >Peripheral Retinal Hemorrhage</td>
        <td >
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pih_Absent" name="elem_periOs_pih_Absent" value="Absent" <?php echo ($elem_periOs_pih_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pih_Absent" >Absent</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pih_supTemp" name="elem_periOs_pih_supTemp" value="Superotemporal" <?php echo ($elem_periOs_pih_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pih_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pih_infTemp" name="elem_periOs_pih_infTemp" value="Inferotemporal" <?php echo ($elem_periOs_pih_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pih_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr id="d_PRH1" class="grp_PRH">
        <td ></td>
        <td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pih_supNasal" name="elem_periOd_pih_supNasal" value="Superonasal" <?php echo ($elem_periOd_pih_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pih_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pih_infNasal" name="elem_periOd_pih_infNasal" value="Inferonasal" <?php echo ($elem_periOd_pih_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pih_infNasal" >Inferonasal</label>
        </td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"   id="elem_periOd_pih_comment" name="elem_periOd_pih_comment" value="<?php echo $elem_periOd_pih_comment ;?>" class="form-control" >
        </td>

        <td ></td>
        <td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pih_supNasal" name="elem_periOs_pih_supNasal" value="Superonasal" <?php echo ($elem_periOs_pih_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pih_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pih_infNasal" name="elem_periOs_pih_infNasal" value="Inferonasal" <?php echo ($elem_periOs_pih_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pih_infNasal" >Inferonasal</label>
        </td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"  id="elem_periOs_pih_comment"  name="elem_periOs_pih_comment" value="<?php echo $elem_periOs_pih_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Retinal Hemorrhage"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Retinal Hemorrhage"]; }  ?>

        <tr id="d_PPNV" class="grp_PPNV">
        <td >Peripheral <br/>Neo vascularization</td>
        <td >
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pnv_Absent" name="elem_periOd_pnv_Absent" value="Absent" <?php echo ($elem_periOd_pnv_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pnv_Absent" >Absent</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pnv_supTemp" name="elem_periOd_pnv_supTemp" value="Superotemporal" <?php echo ($elem_periOd_pnv_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pnv_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pnv_infTemp" name="elem_periOd_pnv_infTemp" value="Inferotemporal" <?php echo ($elem_periOd_pnv_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pnv_infTemp" >Inferotemporal</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('PPNV')" rowspan="2">BL</td>
        <td >Peripheral <br/>Neo vascularization</td>
        <td >
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pnv_Absent" name="elem_periOs_pnv_Absent" value="Absent" <?php echo ($elem_periOs_pnv_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pnv_Absent" >Absent</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pnv_supTemp" name="elem_periOs_pnv_supTemp" value="Superotemporal" <?php echo ($elem_periOs_pnv_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pnv_supTemp" >Superotemporal</label>
        </td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pnv_infTemp" name="elem_periOs_pnv_infTemp" value="Inferotemporal" <?php echo ($elem_periOs_pnv_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pnv_infTemp" >Inferotemporal</label>
        </td>
        </tr>

        <tr id="d_PPNV1" class="grp_PPNV">
        <td ></td>
        <td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pnv_supNasal" name="elem_periOd_pnv_supNasal" value="Superonasal" <?php echo ($elem_periOd_pnv_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pnv_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_pnv_infNasal" name="elem_periOd_pnv_infNasal" value="Inferonasal" <?php echo ($elem_periOd_pnv_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_pnv_infNasal" >Inferonasal</label>
        </td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"   name="elem_periOd_pnv_comment" value="<?php echo $elem_periOd_pnv_comment ;?>" class="form-control" >
        </td>

        <td ></td>
        <td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pnv_supNasal" name="elem_periOs_pnv_supNasal" value="Superonasal" <?php echo ($elem_periOs_pnv_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pnv_supNasal" >Superonasal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_pnv_infNasal" name="elem_periOs_pnv_infNasal" value="Inferonasal" <?php echo ($elem_periOs_pnv_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_pnv_infNasal" >Inferonasal</label>
        </td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"   name="elem_periOs_pnv_comment" value="<?php echo $elem_periOs_pnv_comment ;?>" class="form-control" >
        </td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Peripheral Neo vascularization"])){ echo $arr_exm_ext_htm["Periphery"]["Peripheral Neo vascularization"]; }  ?>

        <tr id="d_periRTear" class="grp_periRTear">
        <td >Retinal Tear</td>
        <td>
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalTear_Absent" name="elem_periOd_retinalTear_Absent" value="Absent"
        <?php echo ($elem_periOd_retinalTear_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalTear_Absent" >Absent</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalTear_Single" name="elem_periOd_retinalTear_Single" value="Single"
        <?php echo ($elem_periOd_retinalTear_Single == "Single") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalTear_Single" >Single</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalTear_Multiple" name="elem_periOd_retinalTear_Multiple" value="Multiple"
        <?php echo ($elem_periOd_retinalTear_Multiple == "Multiple") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalTear_Multiple" >Multiple</label>
        </td><td colspan="2">
        <div class="form-group form-inline">
        <select name="elem_periOd_retinalTear_time" onchange="checkAbsent(this)" class="form-control">
        <option value=""></option>
        <?php
        for($i=1;$i<=12;$i++){
        $t = "".$i." o' clock";
        $sel = (strpos($elem_periOd_retinalTear_time,"$i")!==false) ? "selected" : "";
        echo "<option value=\"".$t."\"  ".$sel.">".$i."</option>";
        }
        ?>
        </select>
        <label >o' clock</label>
        </div>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periRTear')" rowspan="2">BL</td>
        <td >Retinal Tear</td>
        <td>
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalTear_Absent" name="elem_periOs_retinalTear_Absent" value="Absent"
        <?php echo ($elem_periOs_retinalTear_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalTear_Absent" >Absent</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalTear_Single" name="elem_periOs_retinalTear_Single" value="Single"
        <?php echo ($elem_periOs_retinalTear_Single == "Single") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalTear_Single" >Single</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalTear_Multiple" name="elem_periOs_retinalTear_Multiple" value="Multiple"
        <?php echo ($elem_periOs_retinalTear_Multiple == "Multiple") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalTear_Multiple" >Multiple</label>
        </td><td colspan="2">
        <div class="form-group form-inline">
        <select name="elem_periOs_retinalTear_time" onchange="checkAbsent(this)" class="form-control">
        <option value=""></option>
        <?php
        for($i=1;$i<=12;$i++){
        $t = "".$i." o' clock";
        $sel = (strpos($elem_periOs_retinalTear_time,"$i")!==false) ? "selected" : "";
        echo "<option value=\"".$t."\"  ".$sel.">".$i."</option>";
        }
        ?>
        </select>
        <label >o' clock</label>
        </div>
        </td>
        </tr>

        <tr id="d_periRTear1" class="grp_periRTear">
        <td ></td>
        <td colspan="6" ><input type="text"  onclick="checkAbsent(this)"   name="elem_periOd_retinalTear_comment" value="<?php echo $elem_periOd_retinalTear_comment ;?>" class="form-control" ></td>

        <td ></td>
        <td colspan="6"><input type="text"  onclick="checkAbsent(this)"   name="elem_periOs_retinalTear_comment" value="<?php echo $elem_periOs_retinalTear_comment ;?>" class="form-control" ></td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Retinal Tear"])){ echo $arr_exm_ext_htm["Periphery"]["Retinal Tear"]; }  ?>

        <tr id="d_periRDet" class="grp_periRDet">
        <td >Retinal Detachment</td>
        <td>
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_Absent" name="elem_periOd_retinalDetach_Absent" value="Absent"
        <?php echo ($elem_periOd_retinalDetach_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_Absent" >Absent</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_Present" name="elem_periOd_retinalDetach_Present" value="Present"
        <?php echo ($elem_periOd_retinalDetach_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_Present" >Present</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_macon" name="elem_periOd_retinalDetach_macon" value="Macula On"
        <?php echo ($elem_periOd_retinalDetach_macon == "Macula On") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_macon" >Macula On</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_macoff" name="elem_periOd_retinalDetach_macoff" value="Macula Off"
        <?php echo ($elem_periOd_retinalDetach_macoff == "Macula Off") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_macoff"  class="mac">Macula Off</label>
        </td>
        <td align="center" class="bilat" onClick="check_bl('periRDet')" rowspan="3">BL</td>
        <td >Retinal Detachment</td>
        <td>
        <input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_Absent" name="elem_periOs_retinalDetach_Absent" value="Absent"
        <?php echo ($elem_periOs_retinalDetach_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_Absent" >Absent</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_Present" name="elem_periOs_retinalDetach_Present" value="Present"
        <?php echo ($elem_periOs_retinalDetach_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_Present" >Present</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_macon" name="elem_periOs_retinalDetach_macon" value="Macula On"
        <?php echo ($elem_periOs_retinalDetach_macon == "Macula On") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_macon" >Macula On</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_macoff" name="elem_periOs_retinalDetach_macoff" value="Macula Off"
        <?php echo ($elem_periOs_retinalDetach_macoff == "Macula Off") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_macoff"  class="mac">Macula Off</label>
        </td>
        </tr>

        <tr id="d_periRDet1" class="grp_periRDet">
        <td ></td>
        <td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_st" name="elem_periOd_retinalDetach_st" value="Superotemporal" <?php echo ($elem_periOd_retinalDetach_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_st" >Superotemporal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_it" name="elem_periOd_retinalDetach_it" value="Inferotemporal" <?php echo ($elem_periOd_retinalDetach_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_it" >Inferotemporal</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_sn" name="elem_periOd_retinalDetach_sn" value="Superonasal" <?php echo ($elem_periOd_retinalDetach_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_sn" >Superonasal</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOd_retinalDetach_in" name="elem_periOd_retinalDetach_in" value="Inferonasal" <?php echo ($elem_periOd_retinalDetach_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOd_retinalDetach_in"  style="width:auto;">Inferonasal</label>
        </td>
        <td ></td>
        <td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_st" name="elem_periOs_retinalDetach_st" value="Superotemporal" <?php echo ($elem_periOs_retinalDetach_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_st" >Superotemporal</label>
        </td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_it" name="elem_periOs_retinalDetach_it" value="Inferotemporal" <?php echo ($elem_periOs_retinalDetach_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_it" >Inferotemporal</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_sn" name="elem_periOs_retinalDetach_sn" value="Superonasal" <?php echo ($elem_periOs_retinalDetach_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_sn" >Superonasal</label>
        </td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_periOs_retinalDetach_in" name="elem_periOs_retinalDetach_in" value="Inferonasal" <?php echo ($elem_periOs_retinalDetach_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_periOs_retinalDetach_in"  style="width:auto;">Inferonasal</label>
        </td>
        </tr>

        <tr id="d_periRDet2" class="grp_periRDet">
        <td ></td>
        <td colspan="6"><input type="text"  onclick="checkAbsent(this)"   name="elem_periOd_retinalDetach_comment" value="<?php echo $elem_periOd_retinalDetach_comment ;?>" class="form-control" ></td>
        <td ></td>
        <td colspan="6"><input type="text"  onclick="checkAbsent(this)"   name="elem_periOs_retinalDetach_comment" value="<?php echo $elem_periOs_retinalDetach_comment ;?>" class="form-control" ></td>
        </tr>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Retinal Detachment"])){ echo $arr_exm_ext_htm["Periphery"]["Retinal Detachment"]; }  ?>
        <?php if(isset($arr_exm_ext_htm["Periphery"]["Main"])){ echo $arr_exm_ext_htm["Periphery"]["Main"]; }  ?>

        <tr id="d_adOpt_peri">
        <td >Comments</td>
        <td colspan="6" ><textarea  onblur="checkwnls()" id="elem_periAdOptionsOd" name="elem_periAdOptionsOd" class="form-control"><?php echo ($elem_periAdOptionsOd);?></textarea></td>
        <td align="center" class="bilat" onClick="check_bl('adOpt_peri')">BL</td>
        <td >Comments</td>
        <td colspan="6"><textarea  onblur="checkwnls()" id="elem_periAdOptionsOs" name="elem_periAdOptionsOs" class="form-control"><?php echo ($elem_periAdOptionsOs);?></textarea></td>
        </tr>

      </table>
    </div>
  </div>
  <!-- Periphery -->

	<!-- Retinal. -->
  <div role="tabpanel" class="tab-pane <?php echo (7 == $defTabKey) ? "active" : "" ?>" id="div7">
  	<div class="examhd form-inline" >
  		<div class="row">
  			<div class="col-sm-1">
  			</div>
  			<div class="col-sm-3">

            	<input type="checkbox"  id="elem_periNotExamined" name="elem_periNotExamined" value="1"
            	<?php echo ($elem_periNotExamined == "1") ? "checked=\"checked\"" : "" ;?>  onclick="checkPneEye(this)" >
            	<label class="lbl_nochange" for="elem_periNotExamined">Periphery not examined</label>
            	<select id="elem_peri_ne_eye" name="elem_peri_ne_eye" class="form-control" onchange="checkPneEye(this)" >
            	<option value=""></option>
            	<option value="OU"  <?php if($elem_peri_ne_eye=="OU") { echo "selected";} ?>>OU</option>
            	<option value="OD" <?php if($elem_peri_ne_eye=="OD") { echo "selected";} ?>>OD</option>
            	<option value="OS" <?php if($elem_peri_ne_eye=="OS") { echo "selected";} ?>>OS</option>
            	</select>

        </div>
        <div class="col-sm-3">
  			</div>
  			<div class="col-sm-1">
  				<!-- Emergency -->
  				<button type="button" id="btn_emrg" class="btn  <?php echo ($elem_emerstt_comm_p2p=="Not done") ? "btn-danger" : "btn-default" ; ?>" onclick="show_emergency_notes()"  >Exceptions</button>
  				<!-- Emergency -->
  			</div>
  			<div class="col-sm-4">
  				<span id="examFlag" class="glyphicon flagWnl "></span>
  				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

  				<input type="checkbox" id="elem_noChangeRetinal"  name="elem_noChangeRetinal" value="1" onClick="setNC2();"
  							<?php echo ($elem_ncRetinal == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
  				<label class="lbl_nochange frcb" for="elem_noChangeRetinal">NO Change</label>

  				<?php /*if (constant('AV_MODULE')=='YES'){?>
  				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" />
  				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
  				<?php }*/ ?>
  			</div>
  		</div>
  	</div>
  	<div class="clearfix"></div>

  	<!-- Emergency -->
  	<div id="div_emergency_status" class="modal fade" role="dialog">
  		<div class="modal-dialog">

  		<!-- Modal content-->
  		<div class="modal-content">
  			<div class="modal-header">
  			<button type="button" class="close" data-dismiss="modal" onclick="show_emergency_notes()">&times;</button>
  			<h4 class="modal-title">Exceptions</h4>
  			</div>
  			<div class="modal-body">
  				<table class="table table-bordered table-striped">
  				<tr>
  				<td >
  					<input type="checkbox" id="elem_emerstt_comm_p2p_nodone" name="elem_emerstt_comm_p2p_nodone" value="Not done"  <?php if($elem_emerstt_comm_p2p=="Not done") {echo "checked";} ?> onclick="chkEmerAbsPrt(this)">
  					<label for="elem_emerstt_comm_p2p_nodone">Communication From Provider to Provider Not Done</label>
  				</td><td>
  					<select name="elem_emerstt_lvlSeverityRetFind" onclick="chkEmerAbsPrt(this)" class="form-control">
  						<option value=""></option>
  						<option value="Medical Reason" <?php if(strpos($elem_emerstt_lvlSeverityRetFind,"Medical Reason")!==false) {echo "selected";} ?> >Medical Reason</option>
  						<option value="Patient Reason" <?php if(strpos($elem_emerstt_lvlSeverityRetFind,"Patient Reason")!==false) {echo "selected";} ?> >Patient Reason</option>
  					</select>
  				</td>
  				</tr>
  				<tr>
  				<td colspan="2">
  					<input type="checkbox" id="elem_emerstt_macEdFind_present" name="elem_emerstt_macEdFind_present" value="Present" <?php if($elem_emerstt_macEdFind=="Present") {echo "checked";} ?> onclick="chkEmerAbsPrt(this)">
  					<label for="elem_emerstt_macEdFind_present">Macular Edema Findings</label>
  				</td>
  				</tr>
  				<tr>
  				<td colspan="2">
  					<input type="checkbox" id="elem_emerstt_lvlSeverity" name="elem_emerstt_lvlSeverity" value="Present" <?php if($elem_emerstt_lvlSeverity=="Present") {echo "checked";} ?> onclick="chkEmerAbsPrt(this)">
  					<label for="elem_emerstt_lvlSeverity">Level of Severity of Retinopathy Findings</label>
  				</td>
  				</tr>
  				</table>
  			</div>
  		</div>

  		</div>
  		</div>
  	<!-- Emergency -->

  	<div class="table-responsive">
  	<table class="table table-bordered table-striped" >

  		<tr>
  		<td colspan="7" align="center">
  			<span class="flgWnl_2" id="flagWnlOd" ></span>
  			<!--<img src="../../library/images/tstod.png" alt=""/>-->
  			<div class="checkboxO"><label class="od cbold">OD</label></div>
  		</td>
  		<td width="67" align="center" class="bilat bilat_all" onClick="check_bilateral()"><strong>Bilateral</strong></td>
  		<td colspan="7" align="center">
  			<span class="flgWnl_2" id="flagWnlOs" ></span>
  			<!--<img src="../../library/images/tstos.png" alt=""/>-->
  			<div class="checkboxO"><label class="os cbold">OS</label></div>
  		</td>
  		</tr>

  		<tr id="d_ME">
  		<td >Macular edema</td>
  		<td>
  		<input id="elem_retOd_ME_Absent" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_ME_Absent" name="elem_retOd_ME_Absent" value="Absent"
  			<?php echo ($elem_retOd_ME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_ME_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_ME_Focal" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_ME_Focal" name="elem_retOd_ME_Focal" value="Focal"
  			<?php echo ($elem_retOd_ME_Focal == "Focal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_ME_Focal" >Focal</label>
  		</td><td colspan="2"><input id="elem_retOd_ME_Diffuse" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_ME_Diffuse" name="elem_retOd_ME_Diffuse" value="Diffuse"
  			<?php echo ($elem_retOd_ME_Diffuse == "Diffuse") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_ME_Diffuse" >Diffuse</label>
  		</td><td colspan="2"><input id="elem_retOd_ME_Cystoid" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_ME_Cystoid" name="elem_retOd_ME_Cystoid" value="Cystoid"
  			<?php echo ($elem_retOd_ME_Cystoid == "Cystoid") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_ME_Cystoid" >Cystoid</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ME')">BL</td>
  		<td >Macular edema</td>
  		<td>
  		<input id="elem_retOs_ME_Absent" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_ME_Absent" name="elem_retOs_ME_Absent" value="Absent"
  		<?php echo ($elem_retOs_ME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_ME_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_ME_Focal" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_ME_Focal" name="elem_retOs_ME_Focal" value="Focal"
  		<?php echo ($elem_retOs_ME_Focal == "Focal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_ME_Focal" >Focal</label>
  		</td><td colspan="2"><input id="elem_retOs_ME_Diffuse" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_ME_Diffuse" name="elem_retOs_ME_Diffuse" value="Diffuse"
  		<?php echo ($elem_retOs_ME_Diffuse == "Diffuse") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_ME_Diffuse" >Diffuse</label>
  		</td><td colspan="2"><input id="elem_retOs_ME_Cystoid" type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_ME_Cystoid" name="elem_retOs_ME_Cystoid" value="Cystoid"
  		<?php echo ($elem_retOs_ME_Cystoid == "Cystoid") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_ME_Cystoid" >Cystoid</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Macular edema"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Macular edema"]; }  ?>

  		<tr id="d_Drusen_retina" class="grp_Drusen_retina">
  		<td >Drusen</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_Absent" name="elem_retOd_drusen_Absent" value="Absent"
  			<?php echo ($elem_retOd_drusen_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_Absent" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_T" name="elem_retOd_drusen_T" value="T"
  			<?php echo ($elem_retOd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_pos1" name="elem_retOd_drusen_pos1" value="1+"
  			<?php echo ($elem_retOd_drusen_pos1 == "+1" || $elem_retOd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_pos2" name="elem_retOd_drusen_pos2" value="2+"
  			<?php echo ($elem_retOd_drusen_pos2 == "+2" || $elem_retOd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_pos3" name="elem_retOd_drusen_pos3" value="3+"
  			<?php echo ($elem_retOd_drusen_pos3 == "+3" || $elem_retOd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_pos4" name="elem_retOd_drusen_pos4" value="4+"
  			<?php echo ($elem_retOd_drusen_pos4 == "+4" || $elem_retOd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('Drusen_retina')" rowspan="2">BL</td>
  		<td >Drusen</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_Absent" name="elem_retOs_drusen_Absent" value="Absent"
  			<?php echo ($elem_retOs_drusen_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_Absent" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_T" name="elem_retOs_drusen_T" value="T"
  			<?php echo ($elem_retOs_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_pos1" name="elem_retOs_drusen_pos1" value="1+"
  			<?php echo ($elem_retOs_drusen_pos1 == "+1" || $elem_retOs_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_pos2" name="elem_retOs_drusen_pos2" value="2+"
  			<?php echo ($elem_retOs_drusen_pos2 == "+2" || $elem_retOs_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_pos3" name="elem_retOs_drusen_pos3" value="3+"
  			<?php echo ($elem_retOs_drusen_pos3 == "+3" || $elem_retOs_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_pos4" name="elem_retOs_drusen_pos4" value="4+"
  			<?php echo ($elem_retOs_drusen_pos4 == "+4" || $elem_retOs_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_pos4" >4+</label>
  		</td>
  		</tr>

  		<tr id="d_Drusen_retina1" class="grp_Drusen_retina">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_F" name="elem_retOd_drusen_F" value="F"
  		<?php echo ($elem_retOd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_foveal" name="elem_retOd_drusen_foveal" value="PF"
  		<?php echo ($elem_retOd_drusen_foveal == "PF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_foveal" >PF</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_EF" name="elem_retOd_drusen_EF" value="EF"
  		<?php echo ($elem_retOd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_EF" >EF</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_drusen_hard" name="elem_retOd_drusen_hard" value="Hard"
  		<?php echo ($elem_retOd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_drusen_hard" >Hard</label>
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_F" name="elem_retOs_drusen_F" value="F"
  		<?php echo ($elem_retOs_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_foveal" name="elem_retOs_drusen_foveal" value="PF"
  		<?php echo ($elem_retOs_drusen_foveal == "PF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_foveal" >PF</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_EF" name="elem_retOs_drusen_EF" value="EF"
  		<?php echo ($elem_retOs_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_EF" >EF</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_drusen_hard" name="elem_retOs_drusen_hard" value="Hard"
  		<?php echo ($elem_retOs_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_drusen_hard" >Hard</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Drusen"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Drusen"]; }  ?>

      <!-- AMD  -->
      <tr class="exmhlgcol grp_handle grp_ARMD <?php echo $cls_ARMD; ?>" id="d_ARMD">
  		<td class="grpbtn" onclick="openSubGrp('ARMD')">
  			<label >AMD
  			<span class="glyphicon <?php echo $arow_ARMD; ?>"></span></label>
  		</td>
  		<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'ARMD');" name="elem_retOd_armd_text" class="form-control"><?php echo ($elem_retOd_armd_text);?></textarea></td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')">BL</td>
  		<td class="grpbtn" onclick="openSubGrp('ARMD')">
  			<label >AMD
  			<span class="glyphicon <?php echo $arow_ARMD; ?>"></span></label>
  		</td>
  		<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'ARMD');" id="os_32" name="elem_retOs_armd_text" class="form-control"><?php echo ($elem_retOs_armd_text);?></textarea></td>
  		</tr>


  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_drusen" >
  		<td >Drusen</td>
  		<td>
  		<input type="checkbox"   onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_neg" name="elem_retOd_armd_drusen_neg" value="Absent"
  		<?php echo ($elem_retOd_armd_drusen_neg == "-ve" || $elem_retOd_armd_drusen_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_neg" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_T" name="elem_retOd_armd_drusen_T" value="T"
  		<?php echo ($elem_retOd_armd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_pos1" name="elem_retOd_armd_drusen_pos1" value="1+"
  		<?php echo ($elem_retOd_armd_drusen_pos1 == "+1" || $elem_retOd_armd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_pos2" name="elem_retOd_armd_drusen_pos2" value="2+"
  		<?php echo ($elem_retOd_armd_drusen_pos2 == "+2" || $elem_retOd_armd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_pos3" name="elem_retOd_armd_drusen_pos3" value="3+"
  		<?php echo ($elem_retOd_armd_drusen_pos3 == "+3" || $elem_retOd_armd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_pos4" name="elem_retOd_armd_drusen_pos4" value="4+"
  		<?php echo ($elem_retOd_armd_drusen_pos4 == "+4" || $elem_retOd_armd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')" rowspan="2" >BL</td>
  		<td >Drusen</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_neg" name="elem_retOs_armd_drusen_neg" value="Absent"
  		<?php echo ($elem_retOs_armd_drusen_neg == "-ve" || $elem_retOs_armd_drusen_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_neg" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_T" name="elem_retOs_armd_drusen_T" value="T"
  		<?php echo ($elem_retOs_armd_drusen_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_pos1" name="elem_retOs_armd_drusen_pos1" value="1+"
  		<?php echo ($elem_retOs_armd_drusen_pos1 == "+1" || $elem_retOs_armd_drusen_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_pos2" name="elem_retOs_armd_drusen_pos2" value="2+"
  		<?php echo ($elem_retOs_armd_drusen_pos2 == "+2" || $elem_retOs_armd_drusen_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_pos3" name="elem_retOs_armd_drusen_pos3" value="3+"
  		<?php echo ($elem_retOs_armd_drusen_pos3 == "+3" || $elem_retOs_armd_drusen_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_pos4" name="elem_retOs_armd_drusen_pos4" value="4+"
  		<?php echo ($elem_retOs_armd_drusen_pos4 == "+4" || $elem_retOs_armd_drusen_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_pos4" >4+</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_drusen1">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_F" name="elem_retOd_armd_drusen_F" value="F"
  		<?php echo ($elem_retOd_armd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_foveal" name="elem_retOd_armd_drusen_foveal" value="PF"
  		<?php echo (($elem_retOd_armd_drusen_foveal == "Perifovial") || ($elem_retOd_armd_drusen_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_foveal" >PF</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_EF" name="elem_retOd_armd_drusen_EF" value="EF"
  		<?php echo ($elem_retOd_armd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_EF" >EF</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_hard" name="elem_retOd_armd_drusen_hard" value="Hard"
  		<?php echo ($elem_retOd_armd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_hard" >Hard</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_drusen_soft" name="elem_retOd_armd_drusen_soft" value="Soft"
  		<?php echo ($elem_retOd_armd_drusen_soft == "Soft") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_drusen_soft" >Soft</label>
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_F" name="elem_retOs_armd_drusen_F" value="F"
  		<?php echo ($elem_retOs_armd_drusen_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_foveal" name="elem_retOs_armd_drusen_foveal" value="PF"
  		<?php echo (($elem_retOs_armd_drusen_foveal == "Perifovial") || ($elem_retOs_armd_drusen_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_foveal" >PF</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_EF" name="elem_retOs_armd_drusen_EF" value="EF"
  		<?php echo ($elem_retOs_armd_drusen_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_EF" >EF</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_hard" name="elem_retOs_armd_drusen_hard" value="Hard"
  		<?php echo ($elem_retOs_armd_drusen_hard == "Hard") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_hard" >Hard</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_drusen_soft" name="elem_retOs_armd_drusen_soft" value="Soft"
  		<?php echo ($elem_retOs_armd_drusen_soft == "Soft") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_drusen_soft" >Soft</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/Drusen"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/Drusen"]; }  ?>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_rpec">
  		<td >RPE Changes</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_neg" name="elem_retOd_armd_rpeCh_neg" value="Absent"
  		<?php echo ($elem_retOd_armd_rpeCh_neg == "-ve" || $elem_retOd_armd_rpeCh_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_neg" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_T" name="elem_retOd_armd_rpeCh_T" value="T"
  		<?php echo ($elem_retOd_armd_rpeCh_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_pos1" name="elem_retOd_armd_rpeCh_pos1" value="1+"
  		<?php echo ($elem_retOd_armd_rpeCh_pos1 == "+1" || $elem_retOd_armd_rpeCh_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_pos2" name="elem_retOd_armd_rpeCh_pos2" value="2+"
  		<?php echo ($elem_retOd_armd_rpeCh_pos2 == "+2" || $elem_retOd_armd_rpeCh_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_pos3" name="elem_retOd_armd_rpeCh_pos3" value="3+"
  		<?php echo ($elem_retOd_armd_rpeCh_pos3 == "+3" || $elem_retOd_armd_rpeCh_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_pos4" name="elem_retOd_armd_rpeCh_pos4" value="4+"
  		<?php echo ($elem_retOd_armd_rpeCh_pos4 == "+4" || $elem_retOd_armd_rpeCh_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')" rowspan="2" >BL</td>
  		<td >RPE Changes</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_neg" name="elem_retOs_armd_rpeCh_neg" value="Absent"
  		<?php echo ($elem_retOs_armd_rpeCh_neg == "-ve" || $elem_retOs_armd_rpeCh_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_neg" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_T" name="elem_retOs_armd_rpeCh_T" value="T"
  		<?php echo ($elem_retOs_armd_rpeCh_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_pos1" name="elem_retOs_armd_rpeCh_pos1" value="1+"
  		<?php echo ($elem_retOs_armd_rpeCh_pos1 == "+1" || $elem_retOs_armd_rpeCh_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_pos2" name="elem_retOs_armd_rpeCh_pos2" value="2+"
  		<?php echo ($elem_retOs_armd_rpeCh_pos2 == "+2" || $elem_retOs_armd_rpeCh_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_pos3" name="elem_retOs_armd_rpeCh_pos3" value="3+"
  		<?php echo ($elem_retOs_armd_rpeCh_pos3 == "+3" || $elem_retOs_armd_rpeCh_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_pos4" name="elem_retOs_armd_rpeCh_pos4" value="4+"
  		<?php echo ($elem_retOs_armd_rpeCh_pos4 == "+4" || $elem_retOs_armd_rpeCh_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_pos4" >4+</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_rpec1">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_F" name="elem_retOd_armd_rpeCh_F" value="F"
  		<?php echo ($elem_retOd_armd_rpeCh_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_foveal" name="elem_retOd_armd_rpeCh_foveal" value="PF"
  		<?php echo (($elem_retOd_armd_rpeCh_foveal == "Perifovial") || ($elem_retOd_armd_rpeCh_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_foveal" >PF</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rpeCh_EF" name="elem_retOd_armd_rpeCh_EF" value="EF"
  		<?php echo (($elem_retOd_armd_rpeCh_EF == "EF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rpeCh_EF" >EF</label>
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_F" name="elem_retOs_armd_rpeCh_F" value="F"
  		<?php echo ($elem_retOs_armd_rpeCh_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"  id="elem_retOs_armd_rpeCh_foveal" name="elem_retOs_armd_rpeCh_foveal" value="PF"
  		<?php echo (($elem_retOs_armd_rpeCh_foveal == "Perifovial") || ($elem_retOs_armd_rpeCh_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_foveal" >PF</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rpeCh_EF"  name="elem_retOs_armd_rpeCh_EF" value="EF"
  		<?php echo ($elem_retOs_armd_rpeCh_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rpeCh_EF" >EF</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/RPE Changes"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/RPE Changes"]; }  ?>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_geo_atr">
  		<td >Geographic Atrophy</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"  id="elem_retOd_armd_geoAtro_neg" name="elem_retOd_armd_geoAtro_neg" value="Absent"
  		<?php echo ($elem_retOd_armd_geoAtro_neg == "-ve" || $elem_retOd_armd_geoAtro_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_neg" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"  id="elem_retOd_armd_geoAtro_T" name="elem_retOd_armd_geoAtro_T" value="T"
  		<?php echo ($elem_retOd_armd_geoAtro_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_pos1"  name="elem_retOd_armd_geoAtro_pos1" value="1+"
  		<?php echo ($elem_retOd_armd_geoAtro_pos1 == "+1" || $elem_retOd_armd_geoAtro_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_pos2"  name="elem_retOd_armd_geoAtro_pos2" value="2+"
  		<?php echo ($elem_retOd_armd_geoAtro_pos2 == "+2" || $elem_retOd_armd_geoAtro_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_pos3"  name="elem_retOd_armd_geoAtro_pos3" value="3+"
  		<?php echo ($elem_retOd_armd_geoAtro_pos3 == "+3" || $elem_retOd_armd_geoAtro_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_pos4"  name="elem_retOd_armd_geoAtro_pos4" value="4+"
  		<?php echo ($elem_retOd_armd_geoAtro_pos4 == "+4" || $elem_retOd_armd_geoAtro_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')" rowspan="2" >BL</td>
  		<td >Geographic Atrophy</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"  id="elem_retOs_armd_geoAtro_neg" name="elem_retOs_armd_geoAtro_neg" value="Absent"
  		<?php echo ($elem_retOs_armd_geoAtro_neg == "-ve" || $elem_retOs_armd_geoAtro_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_neg" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_T" name="elem_retOs_armd_geoAtro_T" value="T"
  		<?php echo ($elem_retOs_armd_geoAtro_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_T" >T</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_pos1" name="elem_retOs_armd_geoAtro_pos1" value="1+"
  		<?php echo ($elem_retOs_armd_geoAtro_pos1 == "+1" || $elem_retOs_armd_geoAtro_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_pos1" >1+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_pos2" name="elem_retOs_armd_geoAtro_pos2" value="2+"
  		<?php echo ($elem_retOs_armd_geoAtro_pos2 == "+2" || $elem_retOs_armd_geoAtro_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_pos2" >2+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_pos3" name="elem_retOs_armd_geoAtro_pos3" value="3+"
  		<?php echo ($elem_retOs_armd_geoAtro_pos3 == "+3" || $elem_retOs_armd_geoAtro_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_pos3" >3+</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_pos4" name="elem_retOs_armd_geoAtro_pos4" value="4+"
  		<?php echo ($elem_retOs_armd_geoAtro_pos4 == "+4" || $elem_retOs_armd_geoAtro_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_pos4" >4+</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_geo_atr1">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_F"  name="elem_retOd_armd_geoAtro_F" value="F"
  		<?php echo ($elem_retOd_armd_geoAtro_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_foveal"  name="elem_retOd_armd_geoAtro_foveal" value="PF"
  		<?php echo (($elem_retOd_armd_geoAtro_foveal == "Perifovial") || ($elem_retOd_armd_geoAtro_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_foveal" >PF</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_geoAtro_EF"  name="elem_retOd_armd_geoAtro_EF" value="EF"
  		<?php echo ($elem_retOd_armd_geoAtro_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_geoAtro_EF" >EF</label>
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_F" name="elem_retOs_armd_geoAtro_F" value="F"
  		<?php echo ($elem_retOs_armd_geoAtro_F == "F") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_F" >F</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_foveal" name="elem_retOs_armd_geoAtro_foveal" value="PF"
  		<?php echo (($elem_retOs_armd_geoAtro_foveal == "Perifovial") || ($elem_retOs_armd_geoAtro_foveal == "PF")) ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_foveal" >PF</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_geoAtro_EF" name="elem_retOs_armd_geoAtro_EF" value="EF"
  		<?php echo ($elem_retOs_armd_geoAtro_EF == "EF") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_geoAtro_EF" >EF</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/Geographic Atrophy"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/Geographic Atrophy"]; }  ?>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>">
  		<td >Retinal Pigment<br/> Epithelial Detachment</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rped_Absent" name="elem_retOd_armd_rped_Absent" value="Absent"
  		<?php echo ($elem_retOd_armd_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rped_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_rped_Present" name="elem_retOd_armd_rped_Present" value="Present"
  		<?php echo ($elem_retOd_armd_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_rped_Present" >Present</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')"  >BL</td>
  		<td >Retinal Pigment<br/> Epithelial Detachment</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rped_Absent" name="elem_retOs_armd_rped_Absent" value="Absent"
  		<?php echo ($elem_retOs_armd_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rped_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_rped_Present" name="elem_retOs_armd_rped_Present" value="Present"
  		<?php echo ($elem_retOs_armd_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_rped_Present" >Present</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/Retinal Pigment Epithelial Detachment"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/Retinal Pigment Epithelial Detachment"]; }  ?>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_cnvm">
  		<td >CNVM</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_cnvm_Absent" name="elem_retOd_armd_cnvm_Absent" value="Absent"
  		<?php echo ($elem_retOd_armd_cnvm_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_cnvm_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_cnvm_Present" name="elem_retOd_armd_cnvm_Present" value="Present"
  		<?php echo ($elem_retOd_armd_cnvm_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_cnvm_Present" >Present</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')" rowspan="2" >BL</td>
  		<td >CNVM</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_cnvm_Absent" name="elem_retOs_armd_cnvm_Absent" value="Absent"
  		<?php echo ($elem_retOs_armd_cnvm_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_cnvm_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_cnvm_Present" name="elem_retOs_armd_cnvm_Present" value="Present"
  		<?php echo ($elem_retOs_armd_cnvm_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_cnvm_Present" >Present</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_cnvm1">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_cnvm_Subfoveal" name="elem_retOd_armd_cnvm_Subfoveal" value="Subfoveal"
  		<?php echo ($elem_retOd_armd_cnvm_Subfoveal == "Subfoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_cnvm_Subfoveal" >Subfoveal</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_cnvm_Perifoveal" name="elem_retOd_armd_cnvm_Perifoveal" value="Perifoveal"
  		<?php echo ($elem_retOd_armd_cnvm_Perifoveal == "Perifoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_cnvm_Perifoveal" >Perifoveal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_cnvm_Extrafoveal" name="elem_retOd_armd_cnvm_Extrafoveal" value="Extrafoveal"
  		<?php echo ($elem_retOd_armd_cnvm_Extrafoveal == "Extrafoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_cnvm_Extrafoveal" >Extrafoveal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_cnvm_Juxtapapillary" name="elem_retOd_armd_cnvm_Juxtapapillary" value="Juxtapapillary"
  		<?php echo ($elem_retOd_armd_cnvm_Juxtapapillary == "Juxtapapillary") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_cnvm_Juxtapapillary" >Juxtapapillary</label>
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_cnvm_Subfoveal" name="elem_retOs_armd_cnvm_Subfoveal" value="Subfoveal"
  		<?php echo ($elem_retOs_armd_cnvm_Subfoveal == "Subfoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_cnvm_Subfoveal" >Subfoveal</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_cnvm_Perifoveal" name="elem_retOs_armd_cnvm_Perifoveal" value="Perifoveal"
  		<?php echo ($elem_retOs_armd_cnvm_Perifoveal == "Perifoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_cnvm_Perifoveal" >Perifoveal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_cnvm_Extrafoveal" name="elem_retOs_armd_cnvm_Extrafoveal" value="Extrafoveal"
  		<?php echo ($elem_retOs_armd_cnvm_Extrafoveal == "Extrafoveal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_cnvm_Extrafoveal" >Extrafoveal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_cnvm_Juxtapapillary" name="elem_retOs_armd_cnvm_Juxtapapillary" value="Juxtapapillary"
  		<?php echo ($elem_retOs_armd_cnvm_Juxtapapillary == "Juxtapapillary") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_cnvm_Juxtapapillary" >Juxtapapillary</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/CNVM"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/CNVM"]; }  ?>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_srh">
  		<td >SRH</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_srh_Absent" name="elem_retOd_armd_srh_Absent" value="Absent"
  		<?php echo ($elem_retOd_armd_srh_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_srh_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_srh_Present" name="elem_retOd_armd_srh_Present" value="Present"
  		<?php echo ($elem_retOd_armd_srh_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_srh_Present" >Present</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')" rowspan="2" >BL</td>
  		<td >SRH</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_srh_Absent" name="elem_retOs_armd_srh_Absent" value="Absent"
  		<?php echo ($elem_retOs_armd_srh_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_srh_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_srh_Present" name="elem_retOs_armd_srh_Present" value="Present"
  		<?php echo ($elem_retOs_armd_srh_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_srh_Present" >Present</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>" id="d_arm_srh1">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_srh_Mild" name="elem_retOd_armd_srh_Mild" value="Mild"
  		<?php echo ($elem_retOd_armd_srh_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_srh_Mild" >Mild</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_srh_Moderate" name="elem_retOd_armd_srh_Moderate" value="Moderate"
  		<?php echo ($elem_retOd_armd_srh_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_srh_Moderate" >Moderate</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_srh_Massive" name="elem_retOd_armd_srh_Massive" value="Massive"
  		<?php echo ($elem_retOd_armd_srh_Massive == "Massive") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_srh_Massive" >Massive</label>
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_srh_Mild" name="elem_retOs_armd_srh_Mild" value="Mild"
  		<?php echo ($elem_retOs_armd_srh_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_srh_Mild" >Mild</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_srh_Moderate" name="elem_retOs_armd_srh_Moderate" value="Moderate"
  		<?php echo ($elem_retOs_armd_srh_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_srh_Moderate" >Moderate</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_srh_Massive" name="elem_retOs_armd_srh_Massive" value="Massive"
  		<?php echo ($elem_retOs_armd_srh_Massive == "Massive") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_srh_Massive" >Massive</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/SRH"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/SRH"]; }  ?>

  		<tr class="exmhlgcol grp_ARMD <?php echo $cls_ARMD; ?>">
  		<td >Subretinal Fluid</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_subret_Absent" name="elem_retOd_armd_subret_Absent" value="Absent"
  		<?php echo ($elem_retOd_armd_subret_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_subret_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOd_armd_subret_Present" name="elem_retOd_armd_subret_Present" value="Present"
  		<?php echo ($elem_retOd_armd_subret_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_armd_subret_Present" >Present</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ARMD')"  >BL</td>
  		<td >Subretinal Fluid</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_subret_Absent" name="elem_retOs_armd_subret_Absent" value="Absent"
  		<?php echo ($elem_retOs_armd_subret_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_subret_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this,'ARMD');"   id="elem_retOs_armd_subret_Present" name="elem_retOs_armd_subret_Present" value="Present"
  		<?php echo ($elem_retOs_armd_subret_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_armd_subret_Present" >Present</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD/Subretinal Fluid"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD/Subretinal Fluid"]; }  ?>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["AMD"])){ echo $arr_exm_ext_htm["Retinal Exam"]["AMD"]; }  ?>

      <!-- AMD  -->

      <!-- ERM  -->
      <tr id="d_ERM" class="grp_ERM">
  		<td >ERM</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_erm_neg" name="elem_retOd_erm_neg" value="Absent"
  		<?php echo ($elem_retOd_erm_neg == "-ve" || $elem_retOd_erm_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_neg" >Absent</label>
  		</td><td><input id="elem_retOd_erm_T" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOd_erm_T" value="T"
  		<?php echo ($elem_retOd_erm_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_T" >T</label>
  		</td><td><input id="elem_retOd_erm_pos1" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOd_erm_pos1" value="1+"
  		<?php echo ($elem_retOd_erm_pos1 == "+1" || $elem_retOd_erm_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_pos1" >1+</label>
  		</td><td><input id="elem_retOd_erm_pos2" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOd_erm_pos2" value="2+"
  		<?php echo ($elem_retOd_erm_pos2 == "+2" || $elem_retOd_erm_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_pos2" >2+</label>
  		</td><td><input id="elem_retOd_erm_pos3" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOd_erm_pos3" value="3+"
  		<?php echo ($elem_retOd_erm_pos3 == "+3"  || $elem_retOd_erm_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_pos3" >3+</label>
  		</td><td><input id="elem_retOd_erm_pos4" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOd_erm_pos4" value="4+"
  		<?php echo ($elem_retOd_erm_pos4 == "+4" || $elem_retOd_erm_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('ERM')" rowspan="3">BL</td>
  		<td >ERM</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_erm_neg" name="elem_retOs_erm_neg" value="Absent"
  		<?php echo ($elem_retOs_erm_neg == "-ve" || $elem_retOs_erm_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_neg" >Absent</label>
  		</td><td><input id="elem_retOs_erm_T" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOs_erm_T" value="T"
  		<?php echo ($elem_retOs_erm_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_T" >T</label>
  		</td><td><input id="elem_retOs_erm_pos1" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOs_erm_pos1" value="1+"
  		<?php echo ($elem_retOs_erm_pos1 == "+1" || $elem_retOs_erm_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_pos1" >1+</label>
  		</td><td><input id="elem_retOs_erm_pos2" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOs_erm_pos2" value="2+"
  		<?php echo ($elem_retOs_erm_pos2 == "+2" || $elem_retOs_erm_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_pos2" >2+</label>
  		</td><td><input id="elem_retOs_erm_pos3" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOs_erm_pos3" value="3+"
  		<?php echo ($elem_retOs_erm_pos3 == "+3" || $elem_retOs_erm_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_pos3" >3+</label>
  		</td><td><input id="elem_retOs_erm_pos4" type="checkbox"  onclick="checkAbsent(this)"   name="elem_retOs_erm_pos4" value="4+"
  		<?php echo ($elem_retOs_erm_pos4 == "+4" || $elem_retOs_erm_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_pos4" >4+</label>
  		</td>
  		</tr>

  		<tr id="d_ERM1" class="grp_ERM">
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_erm_Superotemporal" name="elem_retOd_erm_Superotemporal" value="Superotemporal"
  		<?php echo ($elem_retOd_erm_Superotemporal == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_Superotemporal"  class="al">Superotemporal</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_erm_Inferotemporal" name="elem_retOd_erm_Inferotemporal" value="Inferotemporal"
  		<?php echo ($elem_retOd_erm_Inferotemporal == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_Inferotemporal"  class="al">Inferotemporal</label>
  		</td>
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_erm_Superotemporal" name="elem_retOs_erm_Superotemporal" value="Superotemporal"
  		<?php echo ($elem_retOs_erm_Superotemporal == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_Superotemporal"  class="al">Superotemporal</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_erm_Inferotemporal" name="elem_retOs_erm_Inferotemporal" value="Inferotemporal"
  		<?php echo ($elem_retOs_erm_Inferotemporal == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_Inferotemporal"  class="al">Inferotemporal</label>
  		</td>
  		</tr>

  		<tr id="d_ERM2" class="grp_ERM">
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_erm_Superonasal" name="elem_retOd_erm_Superonasal" value="Superonasal"
  		<?php echo ($elem_retOd_erm_Superonasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_Superonasal"  class="al">Superonasal</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_erm_Inferonasal" name="elem_retOd_erm_Inferonasal" value="Inferonasal"
  		<?php echo ($elem_retOd_erm_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_erm_Inferonasal"  class="al">Inferonasal</label>
  		</td>
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_erm_Superonasal" name="elem_retOs_erm_Superonasal" value="Superonasal"
  		<?php echo ($elem_retOs_erm_Superonasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_Superonasal"  class="al">Superonasal</label>
  		</td><td colspan="4"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_erm_Inferonasal" name="elem_retOs_erm_Inferonasal" value="Inferonasal"
  		<?php echo ($elem_retOs_erm_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_erm_Inferonasal"  class="al">Inferonasal</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["ERM"])){ echo $arr_exm_ext_htm["Retinal Exam"]["ERM"]; }  ?>

      <!-- ERM  -->

      <!-- REPD -->
      <tr class="grp_RPED">
  		<td >Retinal Pigment<br/> Epithelial Detachment</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_rped_Absent" name="elem_retOd_rped_Absent" value="Absent"
  		<?php echo ($elem_retOd_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_rped_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_rped_Present" name="elem_retOd_rped_Present" value="Present"
  		<?php echo ($elem_retOd_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_rped_Present" >Present</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('RPED')">BL</td>
  		<td >Retinal Pigment<br/> Epithelial Detachment</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_rped_Absent" name="elem_retOs_rped_Absent" value="Absent"
  		<?php echo ($elem_retOs_rped_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_rped_Absent" >Absent</label>
  		</td><td colspan="5"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_rped_Present" name="elem_retOs_rped_Present" value="Present"
  		<?php echo ($elem_retOs_rped_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_rped_Present" >Present</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Retinal Pigment Epithelial Detachment"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Retinal Pigment Epithelial Detachment"]; }  ?>

      <!-- REPD -->

      <?php //Line Separator ?>
  		<tr>
  		<td style="height:1px;border-top:1px solid blue;" colspan="15"></td>
  		</tr>
  		<?php //Line Separator ?>

  		<tr id="d_CWS" class="grp_CWS">
  		<td >Cotton Wool Spot</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_cws_Absent" name="elem_retOd_cws_Absent" value="Absent"
  		<?php echo ($elem_retOd_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_cws_Absent" >Absent</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_cws_Macula" name="elem_retOd_cws_Macula" value="Macula"
  		<?php echo ($elem_retOd_cws_Macula == "Macula") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_cws_Macula" >Macula</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_cws_st" name="elem_retOd_cws_st" value="Superotemporal"
  		<?php echo ($elem_retOd_cws_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_cws_st" >Superotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('CWS')" rowspan="2">BL</td>
  		<td >Cotton Wool Spot</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_cws_Absent" name="elem_retOs_cws_Absent" value="Absent"
  		<?php echo ($elem_retOs_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_cws_Absent" >Absent</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_cws_Macula" name="elem_retOs_cws_Macula" value="Macula"
  		<?php echo ($elem_retOs_cws_Macula == "Macula") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_cws_Macula" >Macula</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_cws_st" name="elem_retOs_cws_st" value="Superotemporal"
  		<?php echo ($elem_retOs_cws_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_cws_st" >Superotemporal</label>
  		</td>
  		</tr>

  		<tr id="d_CWS1" class="grp_CWS">
  		<td ></td>
  		<td ><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_cws_it" name="elem_retOd_cws_it" value="Inferotemporal"
  		<?php echo ($elem_retOd_cws_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_cws_it" >Inferotemporal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_cws_sn" name="elem_retOd_cws_sn" value="Superonasal"
  		<?php echo ($elem_retOd_cws_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_cws_sn" >Superonasal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOd_cws_in" name="elem_retOd_cws_in" value="Inferonasal"
  		<?php echo ($elem_retOd_cws_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_cws_in" >Inferonasal</label>
  		</td>

  		<td ></td>
  		<td ><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_cws_it" name="elem_retOs_cws_it" value="Inferotemporal"
  		<?php echo ($elem_retOs_cws_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_cws_it" >Inferotemporal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_cws_sn" name="elem_retOs_cws_sn" value="Superonasal"
  		<?php echo ($elem_retOs_cws_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_cws_sn" >Superonasal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this);"   id="elem_retOs_cws_in" name="elem_retOs_cws_in" value="Inferonasal"
  		<?php echo ($elem_retOs_cws_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_cws_in" >Inferonasal</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Cotton Wool Spot"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Cotton Wool Spot"]; }  ?>

      <!-- Diabetes -->

      <tr id="d_Diabetes">
  		<td >Diabetes</td>
  		<td colspan="6">
  		<input id="elem_retOd_diabetes_noRetino" type="checkbox"  onclick="checkwnls()"   name="elem_retOd_diabetes_noRetino" value="No Retinopathy"
  		<?php echo ($elem_retOd_diabetes_noRetino == "No Retinopathy") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_diabetes_noRetino"  class="noretino">No Retinopathy</label>
  		</td>

  		<td align="center" class="bilat" onClick="check_bl('Diabetes')">BL</td>
  		<td >Diabetes</td>
  		<td colspan="6">
  		<input id="elem_retOs_diabetes_noRetino" type="checkbox"  onclick="checkwnls()"   name="elem_retOs_diabetes_noRetino" value="No Retinopathy"
  		<?php echo ($elem_retOs_diabetes_noRetino == "No Retinopathy") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_diabetes_noRetino"  class="noretino">No Retinopathy</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Diabetes"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Diabetes"]; }  ?>
      <!-- Diabetes -->

      <!-- DR -->
  		<tr class="exmhlgcol grp_handle grp_DRRet <?php echo $cls_DRRet; ?>" id="d_DRRet">
  		<td class="grpbtn" onclick="openSubGrp('DRRet')">
  			<label >DR
  			<span class="glyphicon <?php echo $arow_DRRet; ?>"></span></label>
  		</td>
  		<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'DRRet');"   id="od_110" cols="40" rows="2" name="elem_retOd_dr_text" class="form-control"><?php echo ($elem_retOd_dr_text);?></textarea></td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td class="grpbtn" onclick="openSubGrp('DRRet')">
  			<label >DR
  			<span class="glyphicon <?php echo $arow_DRRet; ?>"></span></label>
  		</td>
  		<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'DRRet');"   id="os_110" cols="40" rows="2" name="elem_retOs_dr_text" class="form-control"><?php echo ($elem_retOs_dr_text);?></textarea></td>
  		</tr>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >NPDR</td>
  		<td>
  		<input id="elem_retOd_dr_npdr_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_npdr_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_npdr_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_npdr_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_dr_npdr_T" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_npdr_T" value="T"
  		<?php echo ($elem_retOd_dr_npdr_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_npdr_T" >T</label>
  		</td><td><input id="elem_retOd_dr_npdr_Mild" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_npdr_Mild" value="Mild"
  		<?php echo ($elem_retOd_dr_npdr_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_npdr_Mild" >Mild</label>
  		</td><td colspan="2"><input id="elem_retOd_dr_npdr_Moderate" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_npdr_Moderate" value="Moderate"
  		<?php echo ($elem_retOd_dr_npdr_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_npdr_Moderate" >Moderate</label>
  		</td><td><input id="elem_retOd_dr_npdr_Severe" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_npdr_Severe" value="Severe"
  		<?php echo ($elem_retOd_dr_npdr_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_npdr_Severe" >Severe</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >NPDR</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this,'DRRet');"   id="elem_retOs_dr_npdr_Absent" name="elem_retOs_dr_npdr_Absent" value="Absent"
  			<?php echo ($elem_retOs_dr_npdr_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_npdr_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_dr_npdr_T" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_npdr_T" value="T"
  			<?php echo ($elem_retOs_dr_npdr_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_npdr_T" >T</label>
  		</td><td><input id="elem_retOs_dr_npdr_Mild" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_npdr_Mild" value="Mild"
  			<?php echo ($elem_retOs_dr_npdr_Mild == "Mild") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_npdr_Mild" >Mild</label>
  		</td><td colspan="2"><input id="elem_retOs_dr_npdr_Moderate" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_npdr_Moderate" value="Moderate"
  			<?php echo ($elem_retOs_dr_npdr_Moderate == "Moderate") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_npdr_Moderate" >Moderate</label>
  		</td><td><input id="elem_retOs_dr_npdr_Severe" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_npdr_Severe" value="Severe"
  			<?php echo ($elem_retOs_dr_npdr_Severe == "Severe") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_npdr_Severe" >Severe</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/NPDR"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/NPDR"]; }  ?>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >Diabetic macular edema</td>
  		<td>
  		<input id="elem_retOd_dr_CSME_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_CSME_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_CSME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_CSME_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_dr_CSME_Present" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_CSME_Present" value="Present"
  		<?php echo ($elem_retOd_dr_CSME_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_CSME_Present" >Present</label>
  		</td><td class="cntrl_invlv" colspan="2"><input id="elem_retOd_dr_CSME_cenInvlv" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_CSME_cenInvlv" value="Center involving"
  		<?php echo ($elem_retOd_dr_CSME_cenInvlv == "Center involving") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_CSME_cenInvlv"  class="cntrl_invlv">Center involving</label>
  		</td><td colspan="2"><input id="elem_retOd_dr_CSME_cenSpar" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_CSME_cenSpar" value="Center Sparing"
  		<?php echo ($elem_retOd_dr_CSME_cenSpar == "Center Sparing") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_CSME_cenSpar"  class="cntrl_spar">Center Sparing</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >Diabetic macular edema</td>
  		<td>
  		<input id="elem_retOs_dr_CSME_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_CSME_Absent" value="Absent"
  		<?php echo ($elem_retOs_dr_CSME_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_CSME_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_dr_CSME_Present" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_CSME_Present" value="Present"
  		<?php echo ($elem_retOs_dr_CSME_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_CSME_Present" >Present</label>
  		</td><td class="cntrl_invlv" colspan="2"><input id="elem_retOs_dr_CSME_cenInvlv" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_CSME_cenInvlv" value="Center involving"
  		<?php echo ($elem_retOs_dr_CSME_cenInvlv == "Center involving") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_CSME_cenInvlv"  class="cntrl_invlv">Center involving</label>
  		</td><td colspan="2"><input id="elem_retOs_dr_CSME_cenSpar" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_CSME_cenSpar" value="Center Sparing"
  		<?php echo ($elem_retOs_dr_CSME_cenSpar == "Center Sparing") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_CSME_cenSpar"  class="cntrl_spar">Center Sparing</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/Diabetic macular edema"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/Diabetic macular edema"]; }  ?>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >Hard Exudate</td>
  		<td>
  		<input id="elem_retOd_dr_exu_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_exu_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_exu_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_exu_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_dr_exu_T" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_exu_T" value="T"
  		<?php echo ($elem_retOd_dr_exu_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_exu_T" >T</label>
  		</td><td><input id="elem_retOd_dr_exu_pos1" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_exu_pos1" value="1+"
  		<?php echo ($elem_retOd_dr_exu_pos1 == "+1" || $elem_retOd_dr_exu_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_exu_pos1" >1+</label>
  		</td><td><input id="elem_retOd_dr_exu_pos2" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_exu_pos2" value="2+"
  		<?php echo ($elem_retOd_dr_exu_pos2 == "+2" || $elem_retOd_dr_exu_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_exu_pos2" >2+</label>
  		</td><td><input id="elem_retOd_dr_exu_pos3" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_exu_pos3" value="3+"
  		<?php echo ($elem_retOd_dr_exu_pos3 == "+3" || $elem_retOd_dr_exu_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_exu_pos3" >3+</label>
  		</td><td><input id="elem_retOd_dr_exu_pos4" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_exu_pos4" value="4+"
  		<?php echo ($elem_retOd_dr_exu_pos4 == "+4" || $elem_retOd_dr_exu_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_exu_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >Hard Exudate</td>
  		<td>
  		<input id="elem_retOs_dr_exu_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_exu_Absent" value="Absent"
  		<?php echo ($elem_retOs_dr_exu_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_exu_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_dr_exu_T" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_exu_T" value="T"
  		<?php echo ($elem_retOs_dr_exu_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_exu_T" >T</label>
  		</td><td><input id="elem_retOs_dr_exu_pos1" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_exu_pos1" value="1+"
  		<?php echo ($elem_retOs_dr_exu_pos1 == "+1" || $elem_retOs_dr_exu_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_exu_pos1" >1+</label>
  		</td><td><input id="elem_retOs_dr_exu_pos2" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_exu_pos2" value="2+"
  		<?php echo ($elem_retOs_dr_exu_pos2 == "+2" || $elem_retOs_dr_exu_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_exu_pos2" >2+</label>
  		</td><td><input id="elem_retOs_dr_exu_pos3" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_exu_pos3" value="3+"
  		<?php echo ($elem_retOs_dr_exu_pos3 == "+3" || $elem_retOs_dr_exu_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_exu_pos3" >3+</label>
  		</td><td><input id="elem_retOs_dr_exu_pos4" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_exu_pos4" value="4+"
  		<?php echo ($elem_retOs_dr_exu_pos4 == "+4" || $elem_retOs_dr_exu_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_exu_pos4" >4+</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/Hard Exudate"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/Hard Exudate"]; }  ?>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >Cotton Wool Spots</td>
  		<td>
  		<input id="elem_retOd_dr_cws_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_cws_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_cws_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_dr_cws_T" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_cws_T" value="T"
  		<?php echo ($elem_retOd_dr_cws_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_cws_T" >T</label>
  		</td><td><input id="elem_retOd_dr_cws_pos1" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_cws_pos1" value="1+"
  		<?php echo ($elem_retOd_dr_cws_pos1 == "+1" || $elem_retOd_dr_cws_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_cws_pos1" >1+</label>
  		</td><td><input id="elem_retOd_dr_cws_pos2" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_cws_pos2" value="2+"
  		<?php echo ($elem_retOd_dr_cws_pos2 == "+2" || $elem_retOd_dr_cws_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_cws_pos2" >2+</label>
  		</td><td><input id="elem_retOd_dr_cws_pos3" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_cws_pos3" value="3+"
  		<?php echo ($elem_retOd_dr_cws_pos3 == "+3" || $elem_retOd_dr_cws_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_cws_pos3" >3+</label>
  		</td><td><input id="elem_retOd_dr_cws_pos4" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_cws_pos4" value="4+"
  		<?php echo ($elem_retOd_dr_cws_pos4 == "+4" || $elem_retOd_dr_cws_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_cws_pos4" >4+</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >Cotton Wool Spots</td>
  		<td>
  		<input id="elem_retOs_dr_cws_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_cws_Absent" value="Absent"
  		<?php echo ($elem_retOs_dr_cws_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_cws_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_dr_cws_T" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_cws_T" value="T"
  		<?php echo ($elem_retOs_dr_cws_T == "T") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_cws_T" >T</label>
  		</td><td><input id="elem_retOs_dr_cws_pos1" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_cws_pos1" value="1+"
  		<?php echo ($elem_retOs_dr_cws_pos1 == "+1" || $elem_retOs_dr_cws_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_cws_pos1" >1+</label>
  		</td><td><input id="elem_retOs_dr_cws_pos2" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_cws_pos2" value="2+"
  		<?php echo ($elem_retOs_dr_cws_pos2 == "+2" || $elem_retOs_dr_cws_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_cws_pos2" >2+</label>
  		</td><td><input id="elem_retOs_dr_cws_pos3" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_cws_pos3" value="3+"
  		<?php echo ($elem_retOs_dr_cws_pos3 == "+3" || $elem_retOs_dr_cws_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_cws_pos3" >3+</label>
  		</td><td><input id="elem_retOs_dr_cws_pos4" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_cws_pos4" value="4+"
  		<?php echo ($elem_retOs_dr_cws_pos4 == "+4" || $elem_retOs_dr_cws_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_cws_pos4" >4+</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/Cotton Wool Spots"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/Cotton Wool Spots"]; }  ?>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >Focal Laser</td>
  		<td>
  		<input id="elem_retOd_dr_fLaser_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_fLaser_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_fLaser_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_fLaser_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_dr_fLaser_Present" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_fLaser_Present" value="Present"
  		<?php echo ($elem_retOd_dr_fLaser_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_fLaser_Present" >Present</label>
  		</td><td colspan="4"><input id="elem_retOd_dr_fLaser_comment" type="text"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_fLaser_comment"
  		value="<?php echo $elem_retOd_dr_fLaser_comment;?>" class="form-control">
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >Focal Laser</td>
  		<td>
  		<input id="elem_retOs_dr_fLaser_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_fLaser_Absent" value="Absent"
  		<?php echo ($elem_retOs_dr_fLaser_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_fLaser_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_dr_fLaser_Present" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_fLaser_Present" value="Present"
  		<?php echo ($elem_retOs_dr_fLaser_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_fLaser_Present" >Present</label>
  		</td><td colspan="4"><input id="elem_retOs_dr_fLaser_comment" type="text"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_fLaser_comment"
  		value="<?php echo $elem_retOs_dr_fLaser_comment;?>" class="form-control">
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/Focal Laser"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/Focal Laser"]; }  ?>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >PRP</td>
  		<td>
  		<input id="elem_retOd_dr_prp_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_prp_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_prp_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_prp_Absent" >Absent</label>
  		</td><td><input id="elem_retOd_dr_prp_Partial" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_prp_Partial" value="Partial"
  		<?php echo ($elem_retOd_dr_prp_Partial == "Partial") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_prp_Partial" >Partial</label>
  		</td><td colspan="2"><input id="elem_retOd_dr_prp_Complete" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_prp_Complete" value="Complete"
  		<?php echo ($elem_retOd_dr_prp_Complete == "Complete") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_prp_Complete" >Complete</label>
  		</td><td colspan="2"><input id="elem_retOd_dr_prp_comment" type="text"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_prp_comment" value="<?php echo $elem_retOd_dr_prp_comment  ;?>" class="form-control" >
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >PRP</td>
  		<td>
  		<input id="elem_retOs_dr_prp_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_prp_Absent" value="Absent"
  		<?php echo ($elem_retOs_dr_prp_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_prp_Absent" >Absent</label>
  		</td><td><input id="elem_retOs_dr_prp_Partial" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_prp_Partial" value="Partial"
  		<?php echo ($elem_retOs_dr_prp_Partial == "Partial") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_prp_Partial" >Partial</label>
  		</td><td colspan="2"><input id="elem_retOs_dr_prp_Complete" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_prp_Complete" value="Complete"
  		<?php echo ($elem_retOs_dr_prp_Complete == "Complete") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_prp_Complete" >Complete</label>
  		</td><td colspan="2"><input id="elem_retOs_dr_prp_comment" type="text"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_prp_comment" value="<?php echo $elem_retOs_dr_prp_comment  ;?>" class="form-control">
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/PRP"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/PRP"]; }  ?>

  		<tr class="exmhlgcol  grp_DRRet <?php echo $cls_DRRet; ?>">
  		<td >Neo vascularization</td>
  		<td>
  		<input id="elem_retOd_dr_Neovasc_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_Neovasc_Absent" value="Absent"
  		<?php echo ($elem_retOd_dr_Neovasc_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_Neovasc_Absent" >Absent</label>
  		</td><td colspan="5"><input id="elem_retOd_dr_Neovasc_Present" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOd_dr_Neovasc_Present" value="Present"
  		<?php echo ($elem_retOd_dr_Neovasc_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_dr_Neovasc_Present" >Present</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('DRRet')" >BL</td>
  		<td >Neo vascularization</td>
  		<td>
  		<input id="elem_retOs_dr_Neovasc_Absent" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_Neovasc_Absent" value="Absent"
  		<?php echo ($elem_retOs_dr_Neovasc_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_Neovasc_Absent" >Absent</label>
  		</td><td colspan="5"><input id="elem_retOs_dr_Neovasc_Present" type="checkbox"  onclick="checkAbsent(this,'DRRet');"   name="elem_retOs_dr_Neovasc_Present" value="Present"
  		<?php echo ($elem_retOs_dr_Neovasc_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_dr_Neovasc_Present" >Present</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR/Neovascularization"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR/Neovascularization"]; }  ?>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["DR"])){ echo $arr_exm_ext_htm["Retinal Exam"]["DR"]; }  ?>

      <!-- DR -->

      <!-- Vascular Occlusion -->
      <tr class="exmhlgcol grp_handle grp_VasOcc <?php echo $cls_VasOcc; ?>" id="d_VasOcc">
  		<td class="grpbtn" onclick="openSubGrp('VasOcc')">
  			<label >Vascular<br/>Occlusion
  			<span class="glyphicon <?php echo $arow_VasOcc; ?>"></span></label>
  		</td>
  		<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'VasOcc');" id="elem_retOd_vasocc_text" name="elem_retOd_vasocc_text" class="form-control"><?php echo ($elem_retOd_vasocc_text);?></textarea></td>

  		<td align="center" class="bilat" onClick="check_bl('VasOcc')" >BL</td>
  		<td class="grpbtn" onclick="openSubGrp('VasOcc')">
  			<label >Vascular<br/>Occlusion
  			<span class="glyphicon <?php echo $arow_VasOcc; ?>"></span></label>
  		</td>
  		<td colspan="6"><textarea  onblur="checkwnls();checkSymClr(this,'VasOcc');" id="elem_retOs_vasocc_text" name="elem_retOs_vasocc_text" class="form-control"><?php echo ($elem_retOs_vasocc_text);?></textarea></td>
  		</tr>

  		<tr class="exmhlgcol grp_VasOcc <?php echo $cls_VasOcc; ?>" >
  		<td >BRVO</td>
  		<td colspan="2">
  		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brvo_st" name="elem_retOd_vasocc_brvo_st" value="Superotemporal" <?php echo ($elem_retOd_vasocc_brvo_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brvo_st" >Superotemporal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brvo_it" name="elem_retOd_vasocc_brvo_it" value="Inferotemporal" <?php echo ($elem_retOd_vasocc_brvo_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brvo_it" >Inferotemporal</label>
  		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brvo_sn" name="elem_retOd_vasocc_brvo_sn" value="Superonasal" <?php echo ($elem_retOd_vasocc_brvo_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brvo_sn" >Superonasal</label>
  		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brvo_in" name="elem_retOd_vasocc_brvo_in" value="Inferonasal" <?php echo ($elem_retOd_vasocc_brvo_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brvo_in" >Inferonasal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('VasOcc')" rowspan="2">BL</td>
  		<td >BRVO</td>
  		<td colspan="2">
  		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brvo_st" name="elem_retOs_vasocc_brvo_st" value="Superotemporal" <?php echo ($elem_retOs_vasocc_brvo_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brvo_st" >Superotemporal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brvo_it" name="elem_retOs_vasocc_brvo_it" value="Inferotemporal" <?php echo ($elem_retOs_vasocc_brvo_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brvo_it" >Inferotemporal</label>
  		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brvo_sn" name="elem_retOs_vasocc_brvo_sn" value="Superonasal" <?php echo ($elem_retOs_vasocc_brvo_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brvo_sn" >Superonasal</label>
  		</td><td><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brvo_in" name="elem_retOs_vasocc_brvo_in" value="Inferonasal" <?php echo ($elem_retOs_vasocc_brvo_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brvo_in" >Inferonasal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_VasOcc <?php echo $cls_VasOcc; ?>" >
  		<td ></td>
  		<td colspan="2">
  		<label class="selevel3">Macular edema</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me')" class="me" id="elem_retOd_vasocc_brvo_MEPresent" name="elem_retOd_vasocc_brvo_MEPresent" value="Macular Edema Present" <?php echo ($elem_retOd_vasocc_brvo_MEPresent == "Macular Edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brvo_MEPresent" >Present</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me')" class="me" id="elem_retOd_vasocc_brvo_MEAbsent" name="elem_retOd_vasocc_brvo_MEAbsent" value="Macular Edema Absent" <?php echo ($elem_retOd_vasocc_brvo_MEAbsent == "Macular Edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brvo_MEAbsent" >Absent</label>
  		</td><td colspan="2"><input type="text"  onclick="checkwnls();checkSymClr(this,'VasOcc');"  name="elem_retOd_vasocc_brvo_text" value="<?php echo ($elem_retOd_vasocc_brvo_text);?>">
  		</td>
  		<td ></td>
  		<td colspan="2">
  		<label class="selevel3">Macular edema</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me')" class="me"  id="elem_retOs_vasocc_brvo_MEPresent" name="elem_retOs_vasocc_brvo_MEPresent" value="Macular edema Present" <?php echo ($elem_retOs_vasocc_brvo_MEPresent == "Macular edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brvo_MEPresent" >Present</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me')" class="me"  id="elem_retOs_vasocc_brvo_MEAbsent" name="elem_retOs_vasocc_brvo_MEAbsent" value="Macular edema Absent" <?php echo ($elem_retOs_vasocc_brvo_MEAbsent == "Macular edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brvo_MEAbsent" >Absent</label>
  		</td><td colspan="2"><input type="text"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   name="elem_retOs_vasocc_brvo_text" value="<?php echo ($elem_retOs_vasocc_brvo_text);?>">
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/BRVO"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/BRVO"]; }  ?>

  		<tr class="exmhlgcol grp_VasOcc <?php echo $cls_VasOcc; ?>" >
  		<td >CRVO</td>
  		<td colspan="2">
  		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_crvo_Present" name="elem_retOd_vasocc_crvo_Present" value="Present" <?php echo ($elem_retOd_vasocc_crvo_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_crvo_Present" >Present</label>
  		</td><td colspan="2">
  		<label class="selevel3"  >Macular edema</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me');" class="me"  id="elem_retOd_vasocc_crvo_MEPresent" name="elem_retOd_vasocc_crvo_MEPresent" value="Macular Edema Present" <?php echo ($elem_retOd_vasocc_crvo_MEPresent == "Macular Edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_crvo_MEPresent" >Present</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me');" class="me"  id="elem_retOd_vasocc_crvo_MEAbsent" name="elem_retOd_vasocc_crvo_MEAbsent" value="Macular Edema Absent" <?php echo ($elem_retOd_vasocc_crvo_MEAbsent == "Macular Edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_crvo_MEAbsent" >Absent</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('VasOcc')" >BL</td>
  		<td >CRVO</td>
  		<td colspan="2">
  		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_crvo_Present" name="elem_retOs_vasocc_crvo_Present" value="Present" <?php echo ($elem_retOs_vasocc_crvo_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_crvo_Present" >Present</label>
  		</td>
  		<td colspan="2">
  		<label class="selevel3" >Macular edema</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me')" class="me" id="elem_retOs_vasocc_crvo_MEPresent" name="elem_retOs_vasocc_crvo_MEPresent" value="Macular Edema Present" <?php echo ($elem_retOs_vasocc_crvo_MEPresent == "Macular Edema Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_crvo_MEPresent" >Present</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.me')" class="me" id="elem_retOs_vasocc_crvo_MEAbsent" name="elem_retOs_vasocc_crvo_MEAbsent" value="Macular Edema Absent" <?php echo ($elem_retOs_vasocc_crvo_MEAbsent == "Macular Edema Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_crvo_MEAbsent" >Absent</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/CRVO"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/CRVO"]; }  ?>

  		<tr class="exmhlgcol grp_VasOcc <?php echo $cls_VasOcc; ?>" >
  		<td >BRAO</td>
  		<td colspan="2">
  		<input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brao_st" name="elem_retOd_vasocc_brao_st" value="Superotemporal" <?php echo ($elem_retOd_vasocc_brao_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brao_st" >Superotemporal</label>
  		</td><td colspan="2"><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brao_it" name="elem_retOd_vasocc_brao_it" value="Inferotemporal" <?php echo ($elem_retOd_vasocc_brao_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brao_it" >Inferotemporal</label>
  		</td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brao_sn" name="elem_retOd_vasocc_brao_sn" value="Superonasal" <?php echo ($elem_retOd_vasocc_brao_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brao_sn" >Superonasal</label>
  		</td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_brao_in" name="elem_retOd_vasocc_brao_in" value="Inferonasal" <?php echo ($elem_retOd_vasocc_brao_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_brao_in" >Inferonasal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('VasOcc')" >BL</td>
  		<td >BRAO</td>
  		<td colspan="2">
  		<input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brao_st" name="elem_retOs_vasocc_brao_st" value="Superotemporal" <?php echo ($elem_retOs_vasocc_brao_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brao_st" >Superotemporal</label>
  		</td><td colspan="2"><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brao_it" name="elem_retOs_vasocc_brao_it" value="Inferotemporal" <?php echo ($elem_retOs_vasocc_brao_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brao_it" >Inferotemporal</label>
  		</td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brao_sn" name="elem_retOs_vasocc_brao_sn" value="Superonasal" <?php echo ($elem_retOs_vasocc_brao_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brao_sn" >Superonasal</label>
  		</td><td><input  type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_brao_in" name="elem_retOs_vasocc_brao_in" value="Inferonasal" <?php echo ($elem_retOs_vasocc_brao_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_brao_in" >Inferonasal</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/BRAO"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/BRAO"]; }  ?>

  		<tr class="exmhlgcol grp_VasOcc <?php echo $cls_VasOcc; ?>" >
  		<td >CRAO</td>
  		<td>
  		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOd_vasocc_crao_Present" name="elem_retOd_vasocc_crao_Present" value="Present" <?php echo ($elem_retOd_vasocc_crao_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_crao_Present" >Present</label>
  		</td><td colspan="3">
  		<label class="selevel3_cas">Ciliary Artery Sparing</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.cas')" class="cas"  id="elem_retOd_vasocc_crao_CRSPresent" name="elem_retOd_vasocc_crao_CRSPresent" value="Ciliary Artery Sparing Present" <?php echo ($elem_retOd_vasocc_crao_CRSPresent == "Ciliary Artery Sparing Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_crao_CRSPresent" >Present</label>
  		</td><td ><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.cas')" class="cas"  id="elem_retOd_vasocc_crao_CRSAbsent" name="elem_retOd_vasocc_crao_CRSAbsent" value="Ciliary Artery Sparing Absent" <?php echo ($elem_retOd_vasocc_crao_CRSAbsent == "Ciliary Artery Sparing Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasocc_crao_CRSAbsent" >Absent</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('VasOcc')" >BL</td>
  		<td >CRAO</td>
  		<td>
  		<input type="checkbox"  onclick="checkwnls();checkSymClr(this,'VasOcc');"   id="elem_retOs_vasocc_crao_Present" name="elem_retOs_vasocc_crao_Present" value="Present" <?php echo ($elem_retOs_vasocc_crao_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_crao_Present" >Present</label>
  		</td><td colspan="3">
  		<label class="selevel3_cas">Ciliary Artery Sparing</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.cas')" class="cas"  id="elem_retOs_vasocc_crao_CRSPresent" name="elem_retOs_vasocc_crao_CRSPresent" value="Ciliary Artery Sparing Present" <?php echo ($elem_retOs_vasocc_crao_CRSPresent == "Ciliary Artery Sparing Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_crao_CRSPresent" >Present</label>
  		</td><td ><input type="checkbox"  onclick="checkAbsent(this,'VasOcc','.cas')" class="cas"  id="elem_retOs_vasocc_crao_CRSAbsent" name="elem_retOs_vasocc_crao_CRSAbsent" value="Ciliary Artery Sparing Absent" <?php echo ($elem_retOs_vasocc_crao_CRSAbsent == "Ciliary Artery Sparing Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasocc_crao_CRSAbsent" >Absent</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/CRAO"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion/CRAO"]; }  ?>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Vascular Occlusion"]; }  ?>

      <!-- Vascular Occlusion -->

  		<tr id="d_VasShth" class="grp_VasShth">
  		<td >Vascular Sheathing</td>
  		<td >
  		<select name="elem_retOd_vasshth_Measure" onchange="checkwnls();" class="form-control">
  			<option value=""></option>
  			<?php
  			foreach($arrMeasure as $km=>$vm){
  				$sel = ($vm==$elem_retOd_vasshth_Measure) ? "selected" : "";
  				echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  			}
  			?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOd_vasshth_supTemp" name="elem_retOd_vasshth_supTemp" value="Superotemporal"
  			<?php echo ($elem_retOd_vasshth_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasshth_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOd_vasshth_infTemp" name="elem_retOd_vasshth_infTemp" value="Inferotemporal"
  			<?php echo ($elem_retOd_vasshth_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasshth_infTemp" >Inferotemporal</label>

  		</td>
  		<td align="center" class="bilat" onClick="check_bl('VasShth')" rowspan="2">BL</td>
  		<td >Vascular Sheathing</td>
  		<td >
  		<select name="elem_retOs_vasshth_Measure" onchange="checkwnls();" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_vasshth_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOs_vasshth_supTemp" name="elem_retOs_vasshth_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_vasshth_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasshth_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOs_vasshth_infTemp" name="elem_retOs_vasshth_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_vasshth_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasshth_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="grp_VasShth">
  		<td ></td>
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOd_vasshth_supNasal" name="elem_retOd_vasshth_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_vasshth_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasshth_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOd_vasshth_infNasal" name="elem_retOd_vasshth_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_vasshth_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_vasshth_infNasal" >Inferonasal</label>
  		</td><td ><input type="text"  onclick="checkwnls();"   name="elem_retOd_vasshth_comment" name="elem_retOd_vasshth_comment" value="<?php echo $elem_retOd_vasshth_comment ;?>" class="form-control" >
  		</td>

  		<td ></td>
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOs_vasshth_supNasal" name="elem_retOs_vasshth_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_vasshth_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasshth_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();"   id="elem_retOs_vasshth_infNasal" name="elem_retOs_vasshth_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_vasshth_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_vasshth_infNasal" >Inferonasal</label>
  		</td><td ><input type="text"  onclick="checkwnls();"   name="elem_retOs_vasshth_comment" value="<?php echo $elem_retOs_vasshth_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Vascular Sheathing"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Vascular Sheathing"]; }  ?>

      <?php //Line Separator ?>
  		<tr>
  		<td style="height:1px;border-top:1px solid blue;" colspan="15"></td>
  		</tr>
  		<?php //Line Separator ?>

      <!--Nevus-->
      <tr id="d_Nevus" class="grp_Nevus">
  		<td >Nevus</td>
  		<td colspan="3">
  		<div class="form-group form-inline sptrDiscarea">
  		Disc Area
  		<select name="elem_retOd_nevus_discarea" onChange="checkwnls()" class="form-control">
  		<option></option>
  		<option value="Disc Area 1/2" <?php if($elem_retOd_nevus_discarea=="Disc Area 1/2")echo "selected";?>>1/2</option>
  		<?php
  		for($i=1;$i<=5;$i++){
  		$sel= ($elem_retOd_nevus_discarea=="Disc Area ".$i)? "selected" : "";
  		echo "<option value=\"Disc Area ".$i."\" ".$sel.">".$i."</option>";
  		$j=$i+"0.5";
  		$sel= ($elem_retOd_nevus_discarea=="Disc Area ".$j)? "selected" : "";
  		echo "<option value=\"Disc Area ".$j."\" ".$sel.">".$j."</option>";
  		}
  		?>
  		</select>
  		X
  		<select name="elem_retOd_nevus_discarea_verti" onChange="checkwnls()" class="form-control">
  		<option></option>
  		<option value="X 1/2" <?php if($elem_retOd_nevus_discarea_verti=="X 1/2")echo "selected";?>>1/2</option>
  		<?php
  		for($i=1;$i<=5;$i++){
  		$sel= ($elem_retOd_nevus_discarea_verti=="X ".$i)? "selected" : "";
  		echo "<option value=\"X ".$i."\" ".$sel.">".$i."</option>";

  		$j=$i+"0.5";
  		$sel= ($elem_retOd_nevus_discarea_verti=="X ".$j)? "selected" : "";
  		echo "<option value=\"X ".$j."\" ".$sel.">".$j."</option>";
  		}
  		?>
  		</select>
  		</div>

  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls()"   id="elem_retOd_nevus_supTemp" name="elem_retOd_nevus_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_nevus_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_nevus_supTemp" >Superotemporal</label>
    </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_retOd_nevus_infTemp" name="elem_retOd_nevus_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_nevus_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_nevus_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('Nevus')" rowspan="2">BL</td>
  		<td >Nevus</td>
  		<td colspan="3">
  		<div class="form-group form-inline sptrDiscarea">
  		Disc Area
  		<select name="elem_retOs_nevus_discarea" onChange="checkwnls()" class="form-control">
  		<option></option>
  		<option value="Disc Area 1/2" <?php if($elem_retOs_nevus_discarea=="Disc Area 1/2")echo "selected";?>>1/2</option>
  		<?php
  		for($i=1;$i<=5;$i++){
  		$sel= ($elem_retOs_nevus_discarea=="Disc Area ".$i)? "selected" : "";
  		echo "<option value=\"Disc Area ".$i."\" ".$sel.">".$i."</option>";
  		$j=$i+"0.5";
  		$sel= ($elem_retOs_nevus_discarea=="Disc Area ".$j)? "selected" : "";
  		echo "<option value=\"Disc Area ".$j."\" ".$sel.">".$j."</option>";
  		}
  		?>
  		</select>
  		X
  		<select name="elem_retOs_nevus_discarea_verti" onChange="checkwnls()" class="form-control">
  		<option></option>
  		<option value="X 1/2" <?php if($elem_retOs_nevus_discarea_verti=="X 1/2")echo "selected";?>>1/2</option>
  		<?php
  		for($i=1;$i<=5;$i++){
  		$sel= ($elem_retOs_nevus_discarea_verti=="X ".$i)? "selected" : "";
  		echo "<option value=\"X ".$i."\" ".$sel.">".$i."</option>";
  		$j=$i+"0.5";
  		$sel= ($elem_retOs_nevus_discarea_verti=="X ".$j)? "selected" : "";
  		echo "<option value=\"X ".$j."\" ".$sel.">".$j."</option>";
  		}
  		?>
  		</select>
  		</div>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls()"   id="elem_retOs_nevus_supTemp" name="elem_retOs_nevus_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_nevus_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_nevus_supTemp" >Superotemporal</label>
    </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_retOs_nevus_infTemp"  name="elem_retOs_nevus_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_nevus_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_nevus_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="grp_Nevus">
  		<td ></td>
  		<td colspan="3">
  		<span class="sptrDiscarea"></span>
  		</td><td colspan="2"><input  type="checkbox"  onclick="checkwnls()"   id="elem_retOd_nevus_supNasal" name="elem_retOd_nevus_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_nevus_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_nevus_supNasal" >Superonasal</label>
    </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_retOd_nevus_Inferonasal" name="elem_retOd_nevus_Inferonasal" value="Inferonasal"
  		<?php echo ($elem_retOd_nevus_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_nevus_Inferonasal" >Inferonasal</label>
  		</td>
  		<td ></td>
  		<td colspan="3">
  		<span class="sptrDiscarea"></span>
  		</td><td colspan="2"><input  type="checkbox"  onclick="checkwnls()"   id="elem_retOs_nevus_supNasal"  name="elem_retOs_nevus_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_nevus_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_nevus_supNasal" >Superonasal</label>
    </td><td colspan="1"><input  type="checkbox"  onclick="checkwnls()"   id="elem_retOs_nevus_Inferonasal"  name="elem_retOs_nevus_Inferonasal" value="Inferonasal"
  		<?php echo ($elem_retOs_nevus_Inferonasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_nevus_Inferonasal" >Inferonasal</label>
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Nevus"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Nevus"]; }  ?>

      <!--Nevus-->

  		<tr class="exmhlgcol grp_handle grp_PeriDeg <?php echo $cls_PeriDeg; ?>" id="d_PeriDeg">
  		<td class="grpbtn" colspan="2" onclick="openSubGrp('PeriDeg')">
  			<label >Peripheral Degeneration
  			<span class="glyphicon <?php echo $arow_PeriDeg; ?>"></span></label>
  		</td>
  		<td colspan="5">&nbsp;</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')">BL</td>
  		<td class="grpbtn" colspan="2" onclick="openSubGrp('PeriDeg')">
  			<label >Peripheral Degeneration
  			<span class="glyphicon <?php echo $arow_PeriDeg; ?>"></span></label>
  		</td>
  		<td colspan="6">&nbsp;</td>
  		</tr>


  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>" >
  		<td >Atrophic changes</td>
  		<td>
  		<select name="elem_retOd_perideg_atroChan_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOd_perideg_atroChan_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_atroChan_supTemp" name="elem_retOd_perideg_atroChan_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_perideg_atroChan_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_atroChan_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_atroChan_infTemp" name="elem_retOd_perideg_atroChan_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_perideg_atroChan_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_atroChan_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')" rowspan="2">BL</td>
  		<td >Atrophic changes</td>
  		<td>
  		<select name="elem_retOs_perideg_atroChan_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_perideg_atroChan_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_atroChan_supTemp" name="elem_retOs_perideg_atroChan_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_perideg_atroChan_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_atroChan_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_atroChan_infTemp" name="elem_retOs_perideg_atroChan_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_perideg_atroChan_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_atroChan_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_atroChan_supNasal" name="elem_retOd_perideg_atroChan_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_perideg_atroChan_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_atroChan_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_atroChan_infNasal" name="elem_retOd_perideg_atroChan_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_perideg_atroChan_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_atroChan_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOd_perideg_atroChan_comment" value="<?php echo $elem_retOd_perideg_atroChan_comment ;?>" class="form-control" >
  		</td>
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_atroChan_supNasal" name="elem_retOs_perideg_atroChan_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_perideg_atroChan_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_atroChan_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_atroChan_infNasal" name="elem_retOs_perideg_atroChan_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_perideg_atroChan_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_atroChan_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOs_perideg_atroChan_comment" value="<?php echo $elem_retOs_perideg_atroChan_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Atrophic changes"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Atrophic changes"]; }  ?>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td >Equatorial Drusen</td>
  		<td>
  		<select name="elem_retOd_perideg_EqDru_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOd_perideg_EqDru_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_EqDru_supTemp" name="elem_retOd_perideg_EqDru_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_perideg_EqDru_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_EqDru_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_EqDru_infTemp" name="elem_retOd_perideg_EqDru_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_perideg_EqDru_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_EqDru_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')" rowspan="2">BL</td>
  		<td >Equatorial Drusen</td>
  		<td>
  		<select name="elem_retOs_perideg_EqDru_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_perideg_EqDru_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_EqDru_supTemp" name="elem_retOs_perideg_EqDru_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_perideg_EqDru_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_EqDru_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_EqDru_infTemp" name="elem_retOs_perideg_EqDru_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_perideg_EqDru_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_EqDru_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_EqDru_supNasal" name="elem_retOd_perideg_EqDru_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_perideg_EqDru_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_EqDru_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_EqDru_infNasal" name="elem_retOd_perideg_EqDru_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_perideg_EqDru_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_EqDru_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOd_perideg_EqDru_comment" value="<?php echo $elem_retOd_perideg_EqDru_comment ;?>" class="form-control" >
  		</td>
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_EqDru_supNasal" name="elem_retOs_perideg_EqDru_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_perideg_EqDru_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_EqDru_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_EqDru_infNasal" name="elem_retOs_perideg_EqDru_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_perideg_EqDru_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_EqDru_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOs_perideg_EqDru_comment" value="<?php echo $elem_retOs_perideg_EqDru_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Equatorial Drusen"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Equatorial Drusen"]; }  ?>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td >Lattice Degeneration</td>
  		<td>
  		<select name="elem_retOd_perideg_LatDeg_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOd_perideg_LatDeg_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_LatDeg_supTemp" name="elem_retOd_perideg_LatDeg_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_perideg_LatDeg_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_LatDeg_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_LatDeg_infTemp" name="elem_retOd_perideg_LatDeg_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_perideg_LatDeg_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_LatDeg_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')" rowspan="2">BL</td>
  		<td >Lattice Degeneration</td>
  		<td>
  		<select name="elem_retOs_perideg_LatDeg_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_perideg_LatDeg_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_LatDeg_supTemp" name="elem_retOs_perideg_LatDeg_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_perideg_LatDeg_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_LatDeg_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_LatDeg_infTemp" name="elem_retOs_perideg_LatDeg_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_perideg_LatDeg_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_LatDeg_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_LatDeg_supNasal" name="elem_retOd_perideg_LatDeg_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_perideg_LatDeg_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_LatDeg_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_LatDeg_infNasal" name="elem_retOd_perideg_LatDeg_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_perideg_LatDeg_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_LatDeg_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOd_perideg_LatDeg_comment" value="<?php echo $elem_retOd_perideg_LatDeg_comment ;?>" class="form-control" >
  		</td>
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_LatDeg_supNasal" name="elem_retOs_perideg_LatDeg_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_perideg_LatDeg_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_LatDeg_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_LatDeg_infNasal" name="elem_retOs_perideg_LatDeg_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_perideg_LatDeg_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_LatDeg_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOs_perideg_LatDeg_comment" value="<?php echo $elem_retOs_perideg_LatDeg_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Lattice Degeneration"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Lattice Degeneration"]; }  ?>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td >Reticular Changes</td>
  		<td>
  		<select name="elem_retOd_perideg_RetiCh_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOd_perideg_RetiCh_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_RetiCh_supTemp" name="elem_retOd_perideg_RetiCh_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_perideg_RetiCh_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_RetiCh_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_RetiCh_infTemp" name="elem_retOd_perideg_RetiCh_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_perideg_RetiCh_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_RetiCh_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')" rowspan="2">BL</td>
  		<td >Reticular Changes</td>
  		<td>
  		<select name="elem_retOs_perideg_RetiCh_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_perideg_RetiCh_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_RetiCh_supTemp" name="elem_retOs_perideg_RetiCh_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_perideg_RetiCh_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_RetiCh_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_RetiCh_infTemp" name="elem_retOs_perideg_RetiCh_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_perideg_RetiCh_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_RetiCh_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_RetiCh_supNasal" name="elem_retOd_perideg_RetiCh_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_perideg_RetiCh_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_RetiCh_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_RetiCh_infNasal" name="elem_retOd_perideg_RetiCh_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_perideg_RetiCh_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_RetiCh_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOd_perideg_RetiCh_comment" value="<?php echo $elem_retOd_perideg_RetiCh_comment ;?>" class="form-control" >
  		</td>
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_RetiCh_supNasal" name="elem_retOs_perideg_RetiCh_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_perideg_RetiCh_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_RetiCh_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_RetiCh_infNasal" name="elem_retOs_perideg_RetiCh_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_perideg_RetiCh_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_RetiCh_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOs_perideg_RetiCh_comment" value="<?php echo $elem_retOs_perideg_RetiCh_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Reticular Changes"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Reticular Changes"]; }  ?>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td >Retinoschisis</td>
  		<td>
  		<select name="elem_retOd_perideg_Rtchs_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOd_perideg_Rtchs_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_Rtchs_supTemp" name="elem_retOd_perideg_Rtchs_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_perideg_Rtchs_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_Rtchs_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_Rtchs_infTemp" name="elem_retOd_perideg_Rtchs_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_perideg_Rtchs_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_Rtchs_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')" rowspan="2">BL</td>
  		<td >Retinoschisis</td>
  		<td>
  		<select name="elem_retOs_perideg_Rtchs_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_perideg_Rtchs_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_Rtchs_supTemp" name="elem_retOs_perideg_Rtchs_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_perideg_Rtchs_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_Rtchs_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_Rtchs_infTemp" name="elem_retOs_perideg_Rtchs_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_perideg_Rtchs_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_Rtchs_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_Rtchs_supNasal" name="elem_retOd_perideg_Rtchs_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_perideg_Rtchs_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_Rtchs_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_Rtchs_infNasal" name="elem_retOd_perideg_Rtchs_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_perideg_Rtchs_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_Rtchs_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOd_perideg_Rtchs_comment" value="<?php echo $elem_retOd_perideg_Rtchs_comment ;?>" class="form-control" >
  		</td>

  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_Rtchs_supNasal" name="elem_retOs_perideg_Rtchs_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_perideg_Rtchs_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_Rtchs_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_Rtchs_infNasal" name="elem_retOs_perideg_Rtchs_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_perideg_Rtchs_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_Rtchs_infNasal" >Inferonasal</label>
  		</td><td ><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOs_perideg_Rtchs_comment" value="<?php echo $elem_retOs_perideg_Rtchs_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Retinoschisis"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/Retinoschisis"]; }  ?>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td >WWP</td>
  		<td>
  		<select name="elem_retOd_perideg_wwp_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOd_perideg_wwp_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_wwp_supTemp" name="elem_retOd_perideg_wwp_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOd_perideg_wwp_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_wwp_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_wwp_infTemp" name="elem_retOd_perideg_wwp_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOd_perideg_wwp_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_wwp_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PeriDeg')" rowspan="2">BL</td>
  		<td >WWP</td>
  		<td>
  		<select name="elem_retOs_perideg_wwp_Measure" onchange="checkwnls();checkSymClr(this,'PeriDeg');" class="form-control">
  		<option value=""></option>
  		<?php
  		foreach($arrMeasure as $km=>$vm){
  		$sel = ($vm==$elem_retOs_perideg_wwp_Measure) ? "selected" : "";
  		echo "<option value=\"".$vm."\"  ".$sel.">".$vm."</option>";
  		}
  		?>
  		</select>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_wwp_supTemp" name="elem_retOs_perideg_wwp_supTemp" value="Superotemporal"
  		<?php echo ($elem_retOs_perideg_wwp_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_wwp_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_wwp_infTemp" name="elem_retOs_perideg_wwp_infTemp" value="Inferotemporal"
  		<?php echo ($elem_retOs_perideg_wwp_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_wwp_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr class="exmhlgcol grp_PeriDeg <?php echo $cls_PeriDeg; ?>">
  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_wwp_supNasal" name="elem_retOd_perideg_wwp_supNasal" value="Superonasal"
  		<?php echo ($elem_retOd_perideg_wwp_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_wwp_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOd_perideg_wwp_infNasal" name="elem_retOd_perideg_wwp_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOd_perideg_wwp_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_perideg_wwp_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOd_perideg_wwp_comment" value="<?php echo $elem_retOd_perideg_wwp_comment ;?>" class="form-control"  >
  		</td>

  		<td ></td>
  		<td></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_wwp_supNasal" name="elem_retOs_perideg_wwp_supNasal" value="Superonasal"
  		<?php echo ($elem_retOs_perideg_wwp_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_wwp_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   id="elem_retOs_perideg_wwp_infNasal" name="elem_retOs_perideg_wwp_infNasal" value="Inferonasal"
  		<?php echo ($elem_retOs_perideg_wwp_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_perideg_wwp_infNasal" >Inferonasal</label>
  		</td><td><input type="text"  onclick="checkwnls();checkSymClr(this,'PeriDeg');"   name="elem_retOs_perideg_wwp_comment" value="<?php echo $elem_retOs_perideg_wwp_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/WWP"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration/WWP"]; }  ?>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Degeneration"]; }  ?>



  		<tr id="d_PIH" class="grp_PIH">
  		<td >Peripheral Retinal Hemorrhage</td>
  		<td >
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pih_Absent" name="elem_retOd_pih_Absent" value="Absent" <?php echo ($elem_retOd_pih_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pih_Absent" >Absent</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pih_supTemp" name="elem_retOd_pih_supTemp" value="Superotemporal" <?php echo ($elem_retOd_pih_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pih_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pih_infTemp" name="elem_retOd_pih_infTemp" value="Inferotemporal" <?php echo ($elem_retOd_pih_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pih_infTemp" >Inferotemporal</label>
  		</td>

  		<td align="center" class="bilat" onClick="check_bl('PIH')" rowspan="2">BL</td>
  		<td >Peripheral Retinal Hemorrhage</td>
  		<td >
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pih_Absent" name="elem_retOs_pih_Absent" value="Absent" <?php echo ($elem_retOs_pih_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pih_Absent" >Absent</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pih_supTemp" name="elem_retOs_pih_supTemp" value="Superotemporal" <?php echo ($elem_retOs_pih_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pih_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pih_infTemp" name="elem_retOs_pih_infTemp" value="Inferotemporal" <?php echo ($elem_retOs_pih_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pih_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr id="d_PIH1" class="grp_PIH">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pih_supNasal" name="elem_retOd_pih_supNasal" value="Superonasal" <?php echo ($elem_retOd_pih_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pih_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pih_infNasal" name="elem_retOd_pih_infNasal" value="Inferonasal" <?php echo ($elem_retOd_pih_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pih_infNasal" >Inferonasal</label>
  		</td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"   id="elem_retOd_pih_comment" name="elem_retOd_pih_comment" value="<?php echo $elem_retOd_pih_comment ;?>" class="form-control" >
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pih_supNasal" name="elem_retOs_pih_supNasal" value="Superonasal" <?php echo ($elem_retOs_pih_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pih_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pih_infNasal" name="elem_retOs_pih_infNasal" value="Inferonasal" <?php echo ($elem_retOs_pih_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pih_infNasal" >Inferonasal</label>
  		</td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"  id="elem_retOs_pih_comment"  name="elem_retOs_pih_comment" value="<?php echo $elem_retOs_pih_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Retinal Hemorrhage"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Retinal Hemorrhage"]; }  ?>

  		<tr id="d_PNV" class="grp_PNV">
  		<td >Peripheral <br/>Neo vascularization</td>
  		<td >
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pnv_Absent" name="elem_retOd_pnv_Absent" value="Absent" <?php echo ($elem_retOd_pnv_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pnv_Absent" >Absent</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pnv_supTemp" name="elem_retOd_pnv_supTemp" value="Superotemporal" <?php echo ($elem_retOd_pnv_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pnv_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pnv_infTemp" name="elem_retOd_pnv_infTemp" value="Inferotemporal" <?php echo ($elem_retOd_pnv_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pnv_infTemp" >Inferotemporal</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('PNV')" rowspan="2">BL</td>
  		<td >Peripheral <br/>Neo vascularization</td>
  		<td >
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pnv_Absent" name="elem_retOs_pnv_Absent" value="Absent" <?php echo ($elem_retOs_pnv_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pnv_Absent" >Absent</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pnv_supTemp" name="elem_retOs_pnv_supTemp" value="Superotemporal" <?php echo ($elem_retOs_pnv_supTemp == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pnv_supTemp" >Superotemporal</label>
  		</td><td colspan="3"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pnv_infTemp" name="elem_retOs_pnv_infTemp" value="Inferotemporal" <?php echo ($elem_retOs_pnv_infTemp == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pnv_infTemp" >Inferotemporal</label>
  		</td>
  		</tr>

  		<tr id="d_PNV1" class="grp_PNV">
  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pnv_supNasal" name="elem_retOd_pnv_supNasal" value="Superonasal" <?php echo ($elem_retOd_pnv_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pnv_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_pnv_infNasal" name="elem_retOd_pnv_infNasal" value="Inferonasal" <?php echo ($elem_retOd_pnv_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_pnv_infNasal" >Inferonasal</label>
  		</td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"   name="elem_retOd_pnv_comment" value="<?php echo $elem_retOd_pnv_comment ;?>" class="form-control" >
  		</td>

  		<td ></td>
  		<td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pnv_supNasal" name="elem_retOs_pnv_supNasal" value="Superonasal" <?php echo ($elem_retOs_pnv_supNasal == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pnv_supNasal" >Superonasal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_pnv_infNasal" name="elem_retOs_pnv_infNasal" value="Inferonasal" <?php echo ($elem_retOs_pnv_infNasal == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_pnv_infNasal" >Inferonasal</label>
  		</td><td colspan="3"><input type="text"  onclick="checkAbsent(this)"   name="elem_retOs_pnv_comment" value="<?php echo $elem_retOs_pnv_comment ;?>" class="form-control" >
  		</td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Peripheral Neo vascularization"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Peripheral Neo vascularization"]; }  ?>

  		<tr id="d_RetTear" class="grp_RetTear">
  		<td >Retinal Tear</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalTear_Absent" name="elem_retOd_retinalTear_Absent" value="Absent"
  		<?php echo ($elem_retOd_retinalTear_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalTear_Absent" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalTear_Single" name="elem_retOd_retinalTear_Single" value="Single"
  		<?php echo ($elem_retOd_retinalTear_Single == "Single") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalTear_Single" >Single</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalTear_Multiple" name="elem_retOd_retinalTear_Multiple" value="Multiple"
  		<?php echo ($elem_retOd_retinalTear_Multiple == "Multiple") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalTear_Multiple" >Multiple</label>
  		</td><td colspan="2">
  		<div class="form-group form-inline">
  		<select name="elem_retOd_retinalTear_time" onchange="checkAbsent(this)" class="form-control">
  		<option value=""></option>
  		<?php
  		for($i=1;$i<=12;$i++){
  		$t = "".$i." o' clock";
  		$sel = (strpos($elem_retOd_retinalTear_time,"$i")!==false) ? "selected" : "";
  		echo "<option value=\"".$t."\"  ".$sel.">".$i."</option>";
  		}
  		?>
  		</select>
  		<label >o' clock</label>
  		</div>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('RetTear')" rowspan="2">BL</td>
  		<td >Retinal Tear</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalTear_Absent" name="elem_retOs_retinalTear_Absent" value="Absent"
  		<?php echo ($elem_retOs_retinalTear_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalTear_Absent" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalTear_Single" name="elem_retOs_retinalTear_Single" value="Single"
  		<?php echo ($elem_retOs_retinalTear_Single == "Single") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalTear_Single" >Single</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalTear_Multiple" name="elem_retOs_retinalTear_Multiple" value="Multiple"
  		<?php echo ($elem_retOs_retinalTear_Multiple == "Multiple") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalTear_Multiple" >Multiple</label>
  		</td><td colspan="2">
  		<div class="form-group form-inline">
  		<select name="elem_retOs_retinalTear_time" onchange="checkAbsent(this)" class="form-control">
  		<option value=""></option>
  		<?php
  		for($i=1;$i<=12;$i++){
  		$t = "".$i." o' clock";
  		$sel = (strpos($elem_retOs_retinalTear_time,"$i")!==false) ? "selected" : "";
  		echo "<option value=\"".$t."\"  ".$sel.">".$i."</option>";
  		}
  		?>
  		</select>
  		<label >o' clock</label>
  		</div>
  		</td>
  		</tr>

  		<tr id="d_RetTear1" class="grp_RetTear">
  		<td ></td>
  		<td colspan="6" ><input type="text"  onclick="checkAbsent(this)"   name="elem_retOd_retinalTear_comment" value="<?php echo $elem_retOd_retinalTear_comment ;?>" class="form-control" ></td>

  		<td ></td>
  		<td colspan="6"><input type="text"  onclick="checkAbsent(this)"   name="elem_retOs_retinalTear_comment" value="<?php echo $elem_retOs_retinalTear_comment ;?>" class="form-control" ></td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Retinal Tear"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Retinal Tear"]; }  ?>

  		<tr id="d_RetDet" class="grp_RetDet">
  		<td >Retinal Detachment</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_Absent" name="elem_retOd_retinalDetach_Absent" value="Absent"
  		<?php echo ($elem_retOd_retinalDetach_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_Absent" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_Present" name="elem_retOd_retinalDetach_Present" value="Present"
  		<?php echo ($elem_retOd_retinalDetach_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_Present" >Present</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_macon" name="elem_retOd_retinalDetach_macon" value="Macula On"
  		<?php echo ($elem_retOd_retinalDetach_macon == "Macula On") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_macon" >Macula On</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_macoff" name="elem_retOd_retinalDetach_macoff" value="Macula Off"
  		<?php echo ($elem_retOd_retinalDetach_macoff == "Macula Off") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_macoff"  class="mac">Macula Off</label>
  		</td>
  		<td align="center" class="bilat" onClick="check_bl('RetDet')" rowspan="3">BL</td>
  		<td >Retinal Detachment</td>
  		<td>
  		<input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_Absent" name="elem_retOs_retinalDetach_Absent" value="Absent"
  		<?php echo ($elem_retOs_retinalDetach_Absent == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_Absent" >Absent</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_Present" name="elem_retOs_retinalDetach_Present" value="Present"
  		<?php echo ($elem_retOs_retinalDetach_Present == "Present") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_Present" >Present</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_macon" name="elem_retOs_retinalDetach_macon" value="Macula On"
  		<?php echo ($elem_retOs_retinalDetach_macon == "Macula On") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_macon" >Macula On</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_macoff" name="elem_retOs_retinalDetach_macoff" value="Macula Off"
  		<?php echo ($elem_retOs_retinalDetach_macoff == "Macula Off") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_macoff"  class="mac">Macula Off</label>
  		</td>
  		</tr>

  		<tr id="d_RetDet1" class="grp_RetDet">
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_st" name="elem_retOd_retinalDetach_st" value="Superotemporal" <?php echo ($elem_retOd_retinalDetach_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_st" >Superotemporal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_it" name="elem_retOd_retinalDetach_it" value="Inferotemporal" <?php echo ($elem_retOd_retinalDetach_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_it" >Inferotemporal</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_sn" name="elem_retOd_retinalDetach_sn" value="Superonasal" <?php echo ($elem_retOd_retinalDetach_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_sn" >Superonasal</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOd_retinalDetach_in" name="elem_retOd_retinalDetach_in" value="Inferonasal" <?php echo ($elem_retOd_retinalDetach_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOd_retinalDetach_in"  style="width:auto;">Inferonasal</label>
  		</td>
  		<td ></td>
  		<td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_st" name="elem_retOs_retinalDetach_st" value="Superotemporal" <?php echo ($elem_retOs_retinalDetach_st == "Superotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_st" >Superotemporal</label>
  		</td><td colspan="2"><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_it" name="elem_retOs_retinalDetach_it" value="Inferotemporal" <?php echo ($elem_retOs_retinalDetach_it == "Inferotemporal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_it" >Inferotemporal</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_sn" name="elem_retOs_retinalDetach_sn" value="Superonasal" <?php echo ($elem_retOs_retinalDetach_sn == "Superonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_sn" >Superonasal</label>
  		</td><td><input type="checkbox"  onclick="checkAbsent(this)"   id="elem_retOs_retinalDetach_in" name="elem_retOs_retinalDetach_in" value="Inferonasal" <?php echo ($elem_retOs_retinalDetach_in == "Inferonasal") ? "checked=\"checked\"" : "" ;?>><label for="elem_retOs_retinalDetach_in"  style="width:auto;">Inferonasal</label>
  		</td>
  		</tr>

  		<tr id="d_RetDet2" class="grp_RetDet">
  		<td ></td>
  		<td colspan="6"><input type="text"  onclick="checkAbsent(this)"   name="elem_retOd_retinalDetach_comment" value="<?php echo $elem_retOd_retinalDetach_comment ;?>" class="form-control" ></td>
  		<td ></td>
  		<td colspan="6"><input type="text"  onclick="checkAbsent(this)"   name="elem_retOs_retinalDetach_comment" value="<?php echo $elem_retOs_retinalDetach_comment ;?>" class="form-control" ></td>
  		</tr>
  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Retinal Detachment"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Retinal Detachment"]; }  ?>


  		<?php if(isset($arr_exm_ext_htm["Retinal Exam"]["Main"])){ echo $arr_exm_ext_htm["Retinal Exam"]["Main"]; }  ?>

  		<tr id="d_adOpt_retinal">
  		<td >Comments</td>
  		<td colspan="6" ><textarea  onblur="checkwnls()" name="elem_retinalAdOptionsOd" id="elem_retinalAdOptionsOd" class="form-control"><?php echo ($elem_retinalAdOptionsOd);?></textarea></td>
  		<td align="center" class="bilat" onClick="check_bl('adOpt_retinal')">BL</td>
  		<td >Comments</td>
  		<td colspan="6"><textarea  onblur="checkwnls()" name="elem_retinalAdOptionsOs" id="elem_retinalAdOptionsOs" class="form-control"><?php echo ($elem_retinalAdOptionsOs);?></textarea></td>
  		</tr>

  		<!---->
  		<!--
  		<tr >
  		<td >X</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td align="center" class="bilat">BL</td>
  		<td >X</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		<td >&nbsp;</td>
  		</tr>
  		-->
  	</table>
  	</div>
  </div>
	<!-- Retinal. -->

	<!-- Draw. -->
  <div role="tabpanel" class="tab-pane" id="div5">
  	<div class="examhd form-inline">
  		<div class="row">
  			<div class="col-sm-1">
  			</div>
  			<div class="col-sm-2">
  			</div>
  			<div class="col-sm-2">
  			</div>
  			<div class="col-sm-7">
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
  			<textarea  onblur="checkwnls()" name="od_desc" id="od_desc" class="form-control drw_text_box"  ><?php echo $od_desc;?></textarea>
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
  				<input type="hidden" id="hidCDRationOD" name="hidCDRationOD" value="<?php echo $elem_cdValOd; ?>" />
  				<input type="hidden" id="hidCDRationOS" name="hidCDRationOS" value="<?php echo $elem_cdValOs; ?>" />
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
  						$dbTollImage = "imgLaCanvas";
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
  					<input type="hidden" name="hidFundusDrawingId<?php echo $intTempDrawCount; ?>" id="hidFundusDrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
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
  			<textarea  onblur="checkwnls()" name="os_desc" id="os_desc" class="form-control drw_text_box"  ><?php echo $os_desc;?></textarea>
  		</div>
  	</div>
  	<div class="clearfix"> </div>
  </div>
	<!-- Draw. -->

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
		<button id="btn_rprt_intr" type="button" class="btn btn-success navbar-btn hidden" onclick="report_gentr()">Report and Interpretation</button>
		<?php }?>
		<button type="button" class="btn btn-danger navbar-btn pull-right" onclick="cancel_exam()">Cancel</button>
	</div>
</nav>


<!-- Report and Interpretation -->
<!-- Modal -->
<div id="rprtIntrModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Report and Interpretation</h4>
			</div>
			<div class="modal-body">
				<div class="row" >
					<div class="col-sm-2">
						<label  for="ir_ordered_by">Ordered By</label>
					</div>
					<div class="col-sm-4">
						<input type="hidden" class="form-control" id="ir_ordered_by" name="ir_ordered_by"  value="<?php echo $ir_ordered_by;?>">
						<label id="lbl_ordered_by" ><?php echo $ir_ordered_by_name;?></label>
					</div>

					<div class="col-sm-2">
						<label  for="ir_test_type">Test Type</label>
					</div>
					<div class="col-sm-4">
						<input type="hidden" class="form-control" id="ir_test_type" name="ir_test_type" value="<?php echo $ir_test_type;?>">
						<label id="ir_test_type_label" ><?php echo $ir_test_type;?></label>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label for="ir_assessment">Assessment</label>
						<textarea class="form-control" rows="5" id="elem_assessment1" name="elem_assessment[]"><?php echo $ir_assessment;?></textarea>
					</div>
					<div class="form-group">
						<label for="ir_dxcd">Dx Code</label>
						<textarea class="form-control" rows="1" id="elem_assessment_dxcode1" name="elem_assessment_dxcode[]"><?php echo $ir_dxcd;?></textarea>
						<input type="hidden" class="form-control" id="ir_dxid" name="ir_dxid" value="<?php echo $ir_dxid;?>">
					</div>
					<div class="form-group">
						<label for="ir_plan">Plan</label>
						<textarea class="form-control" rows="5" id="elem_plan1" name="elem_plan[]"><?php echo $ir_plan;?></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">Done</button>
				<?php if(!empty($chart_draw_inter_report_id)){ ?>
				<button type="button" class="btn btn-danger" onclick="del_inter_report()">Delete</button>
				<?php } ?>
				<input type="hidden" class="form-control" id="chart_draw_inter_report_id" name="chart_draw_inter_report_id" value="<?php echo $chart_draw_inter_report_id;?>">
			</div>
		</div>

	</div>
</div>

</form>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>

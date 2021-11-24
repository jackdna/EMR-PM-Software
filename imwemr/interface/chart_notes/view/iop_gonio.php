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
.iophght .form-control{margin-bottom:0px!important;}
.exammain .examhd .div_pachy .form-control[name*=_o],#trgtOd,#trgtOs,#tmaxOd,#tmaxOs{width:35px!important;}
.glyphicon-plus-sign, .glyphicon-remove-sign{font-size:30px; color:#8bc34a;}
.glyphicon-remove-sign{ color:red; }
.anthsopt .glyphicon-plus-sign{ color:white; }
.anelist{margin-top:10px;}
.anelist table{width:100%; }
#redflag{color:red; visibility:hidden;}
#icoflowSheet{ margin:10px; }
.mulPressure div.tabOff .pd10{visibility:hidden;}
#divIopElem .exambox .head{padding: 1px 10px;}
#divIop1 .iophght .head .iop_method{ margin-top:5px; }
.mulPressure{padding:5px;}
.mulPressure .form-horizontal .control-label{ padding-top:0px; }

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
var sess_pt = "<?php echo $_SESSION['patient']; ?>";
var rootdir = "<?php echo $rootdir;?>";
var finalize_flag = "<?php echo $finalize_flag;?>";
var isReviewable = "<?php echo $isReviewable;?>";
var logged_user_type = "<?php echo $logged_user_type;?>";
var examName = "Gonio";
var arrSubExams = new Array("Gonio","Draw");
var ProClr=<?php echo $ProClr;?>;
var drawCntlNum=<?php echo $drawCntlNum; ?>;
var blEnableHTMLDrawing="<?php echo $blEnableHTMLDrawing;?>";
var arr_db_anas = <?php echo json_encode($arr_db_anas);?>;
var arr_db_dilate = <?php echo json_encode($arr_db_dilate);?>;
var arr_db_ood = <?php echo json_encode($arr_db_ood);?>;
var tr_lm_gl=<?php echo json_encode($tr_lm_gl);?>;
var od_nm_ln=<?php echo $od_nm_ln;?>;
var def_pg='<?php echo $_GET["pg"];?>';
var z_js_dt_frmt = "<?php echo jQueryIntDateFormat(); ?>";
var iop_def_method = "<?php echo $iop_def_method; ?>";
</script>
</head>
<body class="exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->
<form name="frmIopGon" id="frmIopGon" action="saveCharts.php" method="post" onSubmit="freezeElemAll('0');" enctype="multipart/form-data" class="frcb">
    <input type="hidden" name="elem_saveForm" value="ioptable1">
    <input type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
    <input type="hidden" name="elem_iopGonId" value="<?php echo $elem_iopGonId;?>">
    <input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
    <input type="hidden" name="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
    <input type="hidden" name="elem_gonioId" value="<?php echo $elem_gonioId;?>">
    <input type="hidden" name="elem_gonioId_LF" value="<?php echo $elem_gonioId_LF;?>">

    <input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
    <input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
    <input type="hidden" name="elem_wnlGonioOd" value="<?php echo $elem_wnlGonioOd;?>">
    <input type="hidden" name="elem_wnlGonioOs" value="<?php echo $elem_wnlGonioOs;?>">

    <input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">
    <input type="hidden" name="elem_purged_IOP" value="<?php echo $elem_purged_IOP;?>">

    <input type="hidden" name="elem_wnlDrawOd" value="<?php echo $elem_wnlDrawOd;?>">
    <input type="hidden" name="elem_wnlDrawOs" value="<?php echo $elem_wnlDrawOs;?>">
    <input type="hidden" name="elem_posGonio" value="<?php echo $elem_posGonio;?>">
    <input type="hidden" name="elem_posDraw" value="<?php echo $elem_posDraw;?>">
    <input type="hidden" name="elem_wnlGonio" value="<?php echo $elem_wnlGonio;?>">
    <input type="hidden" name="elem_wnlDraw" value="<?php echo $elem_wnlDraw;?>">
    <input type="hidden" name="elem_desc_ig" value="<?php echo $elem_desc_ig;?>">
    <input type="hidden" name="fieldsCount" id="fieldsCount" value="<?php echo $fieldCount; ?>"  />

    <input type="hidden" name="elem_wnlIOP" value="">
    <input type="hidden" name="elem_posIOP" value="">
    <input type="hidden" name="elem_wnlIOPOd" value="">
    <input type="hidden" name="elem_wnlIOPOs" value="">
    <input type="hidden" name="elem_ncGonio" value="<?php echo $elem_noChange;?>">
    <input type="hidden" name="elem_ncDraw" value="<?php echo $elem_noChange_draw;?>">
    <input type="hidden" name="elem_examined_no_change" value="<?php echo $elem_noChange;?>">

    <!-- Change indicators-->
    <input type="hidden" name="elem_ci_iop" id="elem_ci_iop" value="<?php echo $elem_ci_iop; ?>">
    <input type="hidden" name="elem_ci_dilation" id="elem_ci_dilation" value="<?php echo $elem_ci_dilation; ?>">
    <input type="hidden" name="elem_ci_gonio" id="elem_ci_gonio" value="<?php echo $elem_ci_gonio; ?>">
    <input type="hidden" name="elem_ci_OOD" id="elem_ci_OOD" value="<?php echo $elem_ci_OOD; ?>">
    <!-- Change indicators-->

    <!-- newET_changeIndctr -->
    <input type="hidden" name="elem_chng_divIopElem_Od" id="elem_chng_divIopElem_Od" value="<?php echo $elem_chng_divIopElem_Od;?>">
    <input type="hidden" name="elem_chng_divIopElem_Os" id="elem_chng_divIopElem_Os" value="<?php echo $elem_chng_divIopElem_Os;?>">
    <input type="hidden" name="elem_chng_divIop_Od" id="elem_chng_divIop_Od" value="<?php echo $elem_chng_divIop_Od;?>">
    <input type="hidden" name="elem_chng_divIop_Os" id="elem_chng_divIop_Os" value="<?php echo $elem_chng_divIop_Os;?>">
    <input type="hidden" name="elem_chng_divIop3_Od" id="elem_chng_divIop3_Od" value="<?php echo $elem_chng_divIop3_Od;?>">
    <input type="hidden" name="elem_chng_divIop3_Os" id="elem_chng_divIop3_Os" value="<?php echo $elem_chng_divIop3_Os;?>">
    <input type="hidden" name="elem_chng_iop" id="elem_chng_iop" value="<?php echo $elem_chng_iop;?>">
    <input type="hidden" name="elem_chng_dilation" id="elem_chng_dilation" value="<?php echo $elem_chng_dilation;?>">
    <input type="hidden" name="elem_chng_OOD" id="elem_chng_OOD" value="<?php echo $elem_chng_OOD;?>">
    <!-- newET_changeIndctr -->

    <input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
    <!--<input type="hidden" name="hidIOPDrawingId" id="hidIOPDrawingId" value="<?php //echo $dbIdocDrawingId;?>">-->
    <input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">

    <input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
    <input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">

<div class=" container-fluid">
<div class="whtbox exammain ">

<div class="clearfix"></div>
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
	<?php
	foreach($arrTabs as $key => $val){
		if($key == "Iop1"){
			$tmp2="Iop1";
		}else if($key == "Iop"){
			$tmp2="Iop";
		}else if($key == "Iop3"){
			$tmp2="Draw";
		}
		$tmp = ($key == $defTabKey) ? "active" : "";
	?>
		<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab"  id="tab<?php echo $key;?>" onclick="changeTab('<?php echo $key;?>')" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane <?php echo ("Iop1" == $defTabKey) ? "active" : "" ?>" id="divIop1">
	<div class="examhd ">
	<div id="hdr_iop" class="row">
		<div class="col-sm-6 text-left" >
			<ul>
			<li>
				<?php if($finalize_flag == 1){?>
				<label class="chart_status label label-danger">Finalized</label>
				<?php }?>
			</li>

      <li class=" form-inline">Target
  			<span class=" odcol">OD</span> <input type="text" name="trgtOd" type="text" size="5" id="trgtOd" value="<?php echo $trgtOd;?>" class="form-control" placeholder="Text input">
  			<span class="oscol">OS</span> <input type="text" name="trgtOs" type="text" size="5" id="trgtOs" value="<?php echo $trgtOs;?>" class="form-control" placeholder="Text input">
			</li>

      <li class=" form-inline">Tmax
  			<span class=" odcol">OD</span> <input type="text" name="tmaxOd" type="text" size="5" id="tmaxOd" value="<?php echo $tmaxOd;?>" class="form-control " placeholder="Text input" disabled>
  			<span class="oscol">OS</span> <input type="text" name="tmaxOs" type="text" size="5" id="tmaxOs" value="<?php echo $tmaxOs;?>" class="form-control" placeholder="Text input" disabled>
			</li>

      <li>
			<span id="redflag" class="flg_trgt glyphicon glyphicon-flag " title="Pressure > Target" ></span>
			<span id="icoflowSheet" class="hand_cur glyphicon glyphicon-stats " onClick="show_iop_graphs()" alt="Click to see graphs" ></span>
			</li>
			</ul>
		</div>
		<div class="col-sm-5 form-inline text-left div_pachy" >
			Pachy
			<span class="odcol">OD</span>
			<input type="text" name="elem_od_readings" value="<?php echo $elem_od_readings;?>" size="3" onBlur="calCorrectionVal(this.value,'OD');" class="form-control" placeholder="Text input">
			<input type="text" name="elem_od_average" value="<?php echo $elem_od_average;?>" size="2" class="form-control <?php echo (!empty($elem_od_average)) ? "" : "invisible"; ?>">
			<input type="text" name="elem_od_correction_value" value="<?php echo $elem_od_correction_value;?>" size="2" class="form-control <?php echo (!empty($elem_od_readings) || !empty($elem_od_correction_value)) ? "" : "invisible"; ?>">
			<span class="oscol">OS</span>
			<input type="text" name="elem_os_readings" value="<?php echo $elem_os_readings;?>" size="3" onBlur="calCorrectionVal(this.value,'OS');" class="form-control" placeholder="Text input">
			<input type="text" name="elem_os_average" value="<?php echo $elem_os_average;?>" size="2" class="form-control <?php echo (!empty($elem_os_average)) ? "" : "invisible" ;?>">
			<input type="text" name="elem_os_correction_value" value="<?php echo $elem_os_correction_value;?>" size="2" class="form-control <?php echo (!empty($elem_os_readings) || !empty($elem_os_correction_value)) ? "" : "invisible"; ?>">
			Pachy Dt: <div class="input-group">
			<input type="text" class="form-control" name="elem_cor_date" id="elem_cor_date" value="<?php echo $elem_cor_date;?>" size="2" class="date-pick" placeholder="">
			<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true" onclick="$('#elem_cor_date').trigger('focus');"></span></div>
			</div>
		</div>
		<div class="col-sm-1" >
			<?php /*if (constant('AV_MODULE')=='YES'){?>
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" />
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
			<?php }*/ ?>
		</div>
	</div>
	</div>
	<div class="clearfix"> </div>
	<!--IOP-->
	<div id="divIopElem">
	<div id="parentTD" class="iop_presur_vals" >
  	<?php
      $inc_org = 0;
      $getNum="";
      $len =  count($mulPressureArr);
      do{

        $flg_IOP=0;
        $getNum = $inc_org;
        $inc_dv=$inc=$inc_org+1;
        if($inc=="1"){$inc="";$getNum="";}

        //Don't carry forward
        if($elem_editMode==0)
        {
          $mulPressureArr[$inc_org]["mthd"] = '';
          $mulPressureArr[$inc_org]["od"] = '';
          $mulPressureArr[$inc_org]["os"] = '';
          $mulPressureArr[$inc_org]["tm"] = "";
          $mulPressureArr[$inc_org]["dsc"] = "";
        }

        if(!isset($GLOBALS["STOP_PRV_IOP_DESC"]) || empty($GLOBALS["STOP_PRV_IOP_DESC"])){ //
          //
          if(!empty($rowU[$inc_org]["mthd"]) || !empty($rowU[$inc_org]["od"]) || !empty($rowU[$inc_org]["os"])){
              $tmp = "";
              $tmp = "OD:".$rowU[$inc_org]["od"];
              $tmp .= ",OS:".$rowU[$inc_org]["os"];
              $tmp .= ",Time:".$rowU[$inc_org]["tm"];
              if(empty($mulPressureArr[$inc_org]["dsc"])){
                $mulPressureArr[$inc_org]["dsc"] = "".$tmp;
              }
              $mulPressureArr[$inc_org]["dscPrev"] = "".$tmp;
          }
        }//end if

        $gryClss_ta = ($mulPressureArr[$inc_org]["dsc"] == $mulPressureArr[$inc_org]["dscPrev"]) ? "bgGray" : "" ;

        //Desc
        $tmp_mul_desc = trim($mulPressureArr[$inc_org]["dsc"]);
        if($inc==""){
          $dsc_opts ="";
          if($squeezing == "1"){ $dsc_opts .="Squeezing,"; }
          if($unreliable == "1"){ $dsc_opts .="Unreliable,"; }
          if($unable == "1"){ $dsc_opts .="Unable,"; }
          if($hold_lids == "1"){ $dsc_opts .="Hold Lids,"; }
          $dsc_opts = trim($dsc_opts,",");
          if(!empty($dsc_opts)){
              if(!empty($tmp_mul_desc)){ $tmp_mul_desc=$tmp_mul_desc." "; }
              $tmp_mul_desc = $tmp_mul_desc.$dsc_opts;
          }
        }
      ?>

      <div id="multiplePressure<?php echo $inc_dv;?>" class="mulPressure ">
          <div class="row">
            <div class="col-sm-2" >
              <div class="form-group form-inline" >
                <label for="elem_appMethod<?php echo $getNum; ?>">Method</label>
                <input type="text" name="elem_appMethod<?php echo $getNum; ?>"  id="elem_appMethod<?php echo $getNum; ?>" value="<?php echo $mulPressureArr[$inc_org]["mthd"];?>" class="form-control iop_method" placeholder="Method">
              </div>
            </div>
            <div class="col-sm-1">
              <div class="input-group">
                <div class="input-group-addon odcolo" >OD</div>
                <input name="elem_appOd<?php echo $getNum; ?>" type="text" id="elem_appOd<?php echo $getNum; ?>" onBlur="trgat(this,'<?php echo $getNum; ?>')" value="<?php echo $mulPressureArr[$inc_org]["od"];?>" class="form-control" placeholder="">
              </div>
            </div>
            <div class="col-sm-1">
              <div class="input-group">
                <div class="input-group-addon oscolo">OS</div>
                <input name="elem_appOs<?php echo $getNum; ?>" type="text" id="elem_appOs<?php echo $getNum; ?>" onBlur="trgat(this,'<?php echo $getNum; ?>')" value="<?php echo $mulPressureArr[$inc_org]["os"]; ?>" class="form-control" placeholder="">
              </div>
            </div>
            <div class="col-sm-1">
              <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></div>
                <input name="elem_appTime<?php echo $getNum; ?>" type="text" id="elem_appTime<?php echo $getNum; ?>" value="<?php echo $mulPressureArr[$inc_org]["tm"];?>" onClick="this.value=currenttime();" class="form-control" placeholder="">
              </div>
            </div>
            <div class="col-sm-6 form-horizontal">
              <div class="form-group ">
                <label class="control-label col-sm-2" >Description</label>
                <div class="col-sm-10">
                  <textarea name="elem_descTa<?php echo $getNum; ?>" id="elem_descTa<?php echo $getNum; ?>" rows="1" cols="31" class="form-control <?php echo $gryClss_ta; ?>" <?php echo ($elem_editMode==0) ? "onfocus=\"this.select();\" " : ""; ?> ><?php echo $tmp_mul_desc; ?></textarea>
                  <input type="hidden" name="elem_descTa<?php echo $getNum."Prev"; ?>" value="<?php echo $mulPressureArr[$inc_org]["dscPrev"]; ?>">
                </div>
              </div>
            </div>
            <div class="col-sm-1 text-center">
              <?php if($inc_dv=="1"){ ?>
              <figure><span class="glyphicon glyphicon-plus-sign" onclick="multiplePressure();"></span></figure>
              <?php }else{ ?>
              <figure><span class="glyphicon glyphicon-remove-sign" onclick="delImage2(this);"></span></figure>
              <?php } ?>
            </div>
          </div>
      </div>
      <!--IOP-->
    <?php
        $inc_org++;

        if($inc_org<$len){ $flg_IOP=1; } //run again

        if($inc_org>50){$flg_IOP=0;}//stop

      }while($flg_IOP);
  	?>
	</div><!-- ParentTD -->
	<!--Anesthetic-->
	<?php
		$lenAnes=count($arrAnes);
		if($lenAnes<=0) $lenAnes=1;
	?>
	<div class="iopanth " id="conAnes" data-AnesLen="<?php echo $lenAnes; ?>">
		<div class="divcontent">

		<?php
		$lenAnes=count($arrAnes);
		if($lenAnes<=0) $lenAnes=1;

		for($i=0;$i<$lenAnes;$i++){
		$j=$i+1;

		$strAnes_pt_db = $arrAnes[$i]["anes"];
		$arrAnes_pt_db = (!empty($strAnes_pt_db)) ? explode(",", $strAnes_pt_db) : array();
		$arrAnes_pt_db = array_map('trim', $arrAnes_pt_db);

		$strAnes_db="";
		$all_empty_flg=0;
		$td_c=1;

		foreach($arr_db_anas as $key_da => $val_da){

			if(strpos($arrAnes[$i]["anes"],$val_da)!==false){
				$tmp_chkd = "1";
				//$strAnes_pt_db=str_replace($val_da, "", $strAnes_pt_db);
				if (($key_rem = array_search(trim($val_da), $arrAnes_pt_db)) !== false){ unset($arrAnes_pt_db[$key_rem]);}
			}else{
				$tmp_chkd = "0";
			}

			$chkF= ($tmp_chkd == "1") ? "checked=\"checked\"" : "";
			$all_empty_flg+=$tmp_chkd;

			$val_da_el_nm=mk_var_nm($val_da, "anes");
			$val_da_dis = $val_da;
			if(strlen($val_da_dis)>$od_nm_ln){ $val_da_dis=substr($val_da_dis,0,$od_nm_ln-2).".."; }

			if($td_c==1){ $strAnes_db.= "<tr>"; }
			$strAnes_db.="<td>";
			$strAnes_db.="<input name=\"".$val_da_el_nm."[]\" type=\"checkbox\" id=\"".$val_da_el_nm."_".$j."\" value=\"".$j."\"
						onClick=\"checkAnsTime(this)\" ".$chkF." ><label for=\"".$val_da_el_nm."_".$j."\" title=\"".$val_da."\">".$val_da_dis."</label>";
			$strAnes_db.="</td>";

			$td_c++;
			if($td_c>$tr_lm_gl[0]){ $strAnes_db.= "</tr>"; $td_c=1; }

		}

		//
		$strAnes_pt_db = implode(",",$arrAnes_pt_db);
		$strAnes_pt_db = trim($strAnes_pt_db,",");
		$strAnes_pt_db = preg_replace("/\,+/",",",$strAnes_pt_db);

		$time_up = $arrAnes[$i]["time"];
		$dt_up = $arrAnes[$i]["dt"];
		if(isset($arrAnes[$i]["other"])&&!empty($arrAnes[$i]["other"])){
		$chkAO=$arrAnes[$i]["other"];
		}else{
		$chkAO="Other";
		}

		//previous saved into other
		if(!empty($strAnes_pt_db)){
		$chkAO = ($chkAO=="Other") ? "".$strAnes_pt_db : $chkAO.",".$strAnes_pt_db;
		}

		$anes_eye=$arrAnes[$i]["eye"];

		$chk_OU=$chk_OD=$chk_OS="";
		if($anes_eye=="OU"){
			$chk_OU= "checked";
		}else if($anes_eye=="OD"){
			$chk_OD= "checked";
		}else if($anes_eye=="OS"){
			$chk_OS= "checked";
		}

		//IOP ï¿½ anes_eye  - Make OU default.
		if(empty($all_empty_flg) && $chkAO=="Other"&&!empty($chkAO) && empty($chk_OU)){
			$chk_OU= "checked";
		}

		if($i==0){
		$imgAnes = "<span class=\"btnAddIop glyphicon glyphicon-plus-sign\" onClick=\"addAnes()\"></span>";
		}else{
		$imgAnes = "<span class=\"btnDelIop glyphicon glyphicon-remove-sign\" onClick=\"delAnes(".$j.")\"></span>";
		}

		if(!empty($strAnes_db)){
		$strAnes_db="<div class=\"dv_opthdrop col-lg-6 anelist\" ><table class=\"table-responsive\">".$strAnes_db."</table></div>";
		}

		$strAnes.="
		<div id=\"tr_ans_".$j."\" class=\"row\">
			<div class=\"col-lg-1 \"><span class=\"iopanthead\">Anesthetic</span></div>
		".$strAnes_db."
		<div class=\"dv_opthdrop_time col-lg-5 anthsopt\" >
		<ul>
			<li class=\"form-inline\">
				<input type=\"text\" name=\"anes_other[]\" id=\"anes_other".$j."\" value=\"".$chkAO."\" onfocus=\"this.value=(this.value=='Other')?'':this.value;\" onChange=\"checkAnsTime(this)\"  class=\"form-control\" placeholder=\"Other\">
				<input type=\"text\" name=\"dt_up[]\" id=\"dt_up".$j."\" value=\"".$dt_up."\" class=\"form-control date-pick\" placeholder=\"Date\"  size=\"10\">
				<input type=\"text\" name=\"time_up[]\" id=\"time_up".$j."\" value=\"".$time_up."\" onClick=\"this.value=currenttime();\" class=\"form-control\" placeholder=\"Time\" size=\"6\">
			</li>
			<li class=\"ousml\"><input type=\"radio\" name=\"aneseye".$j."\" value=\"OU\"  id=\"aneseye_ou".$j."\"  ".$chk_OU." /><label for=\"aneseye_ou".$j."\"></label></li>
			<li class=\"odsml\"><input type=\"radio\" name=\"aneseye".$j."\" value=\"OD\"  id=\"aneseye_od".$j."\"  ".$chk_OD."  /><label for=\"aneseye_od".$j."\"></label></li>
			<li class=\"ossml\"><input type=\"radio\" name=\"aneseye".$j."\" value=\"OS\" id=\"aneseye_os".$j."\"  ".$chk_OS." /><label for=\"aneseye_os".$j."\"></label></li>
			<li >".$imgAnes."</li>
		</ul>
		</div>
		</div>";
		}

		echo $strAnes;

		?>

		<!--
			<div class="row">
				<div class="col-lg-1 "><span class="iopanthead">Anesthetic</span></div>
				<div class="col-lg-6 anelist">
					<ul>
					<li>Alcaine </li>
					<li>Fluorescein St.. </li>
					<li>Fluorescein St..</li>
					<li>Fluorocaine </li>
					<li>Tetracaine</li>
					</ul>
				</div>
				<div class="col-lg-5 anthsopt">
					<ul>
					<li class="form-inline"><input type="text" class="form-control" placeholder="Other"> <input type="text" class="form-control" placeholder="Time"></li>
					<li class="ousml"><input type='radio' name='thing1' value='valuable' id="thing1"/><label for="thing1"></label></li>   <li class="odsml"><input type='radio' name='thing2' value='valuable' id="thing2"/><label for="thing2"></label></li>  <li class="ossml"><input type='radio' name='thing3' value='valuable' id="thing3"/><label for="thing3"></label></li>  <li ><img src="../../library/images/addinput1.png" alt=""/></li>
					</ul>
				</div>
			</div>
		-->
		</div>
	</div>
	<!--Anesthetic-->
	<div class="clearfix"></div>
	</div>
	<!-- divIopElem -->

	<!-- Dilation -->
	<div id="divDilation" class="exambox iophght">
		<div class="head">
			<div class="row">
				<div class="col-sm-2"><h2>Dilation</h2></div>
				<div class="col-sm-1 dilatinflt ">
					<input <?php if($elem_noDilation == '1') echo 'checked'; ?> value="1"
					type="checkbox" id="elem_noDilation" name="elem_noDilation"
					onclick="setNoDilation()"> <label for="elem_noDilation">No Dilation</label>
				</div>
        <!--
				<div class="col-sm-5 dilatinflt form-inline ">
					<div class="radio"><input <?php //if(($sideIop == 'OU') || ($eyeSide == "OU")||(!$sideIop)) echo 'checked'; ?> value="OU" type="radio" name="elem_sideIop" id="elem_sideIop_b"><label for="elem_sideIop_b">OU</label></div>
					<div class="radio"><input <?php //if(($sideIop == 'OD') || ($eyeSide == "OD")) echo 'checked'; ?> value="OD" type="radio" name="elem_sideIop" id="elem_sideIop_r" ><label for="elem_sideIop_r">OD</label></div>
					<div class="radio"><input <?php //if(($sideIop == 'OS') || ($eyeSide == "OS")) echo 'checked'; ?> value="OS" type="radio" name="elem_sideIop" id="elem_sideIop_l"><label for="elem_sideIop_l">OS</label></div>
				</div>
        -->
				<div class="col-sm-3 dilatinflt form-inline">
					<span>Rev - Eyes</span>
					<div class="radio"><input <?php if(($elem_revEyes == '1')) echo 'checked'; ?> value="1" type="radio" name="elem_revEyes" id="elem_revEyes_y"><label for="elem_revEyes_y">Yes</label></div>
					<div class="radio"><input <?php if(($elem_revEyes != '1')) echo 'checked'; ?> value="0" type="radio" name="elem_revEyes" id="elem_revEyes_n"><label for="elem_revEyes_n">No</label></div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>

		<?php
		$lenDilation = count($arrDilation);
		if(empty($lenDilation))$lenDilation=1;
		$strDilation="<div id=\"conDilation\" data-DilateLen=\"".$lenDilation."\">";

		for($i=0;$i<$lenDilation;$i++){
			$j=$i+1;
			//--
			$strDilate_pt_db = $arrDilation[$i]["dilate"];
			$arrDilate_pt_db = (!empty($strDilate_pt_db)) ? explode(",", $strDilate_pt_db) : array();
			$arrDilate_pt_db = array_map('trim', $arrDilate_pt_db);
			$strDilate_db="";
			$all_empty_flg=0;
			$td_c=1;

			foreach($arr_db_dilate as $key_da => $val_da){
				if(strpos($arrDilation[$i]["dilate"],$val_da)!==false){
					$tmp_chkd = "1";
					//$strDilate_pt_db=str_replace($val_da, "", $strDilate_pt_db);
					if (($key_rem = array_search(trim($val_da), $arrDilate_pt_db)) !== false){ unset($arrDilate_pt_db[$key_rem]);}
				}else{
					$tmp_chkd = "0";
				}

				$chkF= ($tmp_chkd == "1") ? "checked=\"checked\"" : "";
				$all_empty_flg+=$tmp_chkd;

				$val_da_el_nm=mk_var_nm($val_da,"dltn");
				$val_da_dis = $val_da;
				if(strlen($val_da_dis)>$od_nm_ln){ $val_da_dis=substr($val_da_dis,0,$od_nm_ln-2).".."; }

				if($td_c==1){ $strDilate_db.= "<tr>"; }
				$strDilate_db.="<td>";
				$strDilate_db.="<input name=\"".$val_da_el_nm."[]\" type=\"checkbox\" id=\"".$val_da_el_nm."_".$j."\" value=\"".$j."\"
							onClick=\"checkDTime(this)\" ".$chkF." ><label for=\"".$val_da_el_nm."_".$j."\" title=\"".$val_da."\">".$val_da_dis."</label>";
				$strDilate_db.="</td>";
				$td_c++;
				if($td_c>$tr_lm_gl[1]){ $strDilate_db.= "</tr>"; $td_c=1; }
			}

			//remove Other
			if (($key_rem = array_search("Other", $arrDilate_pt_db)) !== false){ unset($arrDilate_pt_db[$key_rem]);}
			$strDilate_pt_db = implode(",",$arrDilate_pt_db);
			$strDilate_pt_db = trim($strDilate_pt_db,",");
			$strDilate_pt_db = preg_replace("/\,+/",",",$strDilate_pt_db);
			//--

			$other=(strpos($arrDilation[$i]["dilate"],"Other")!==false)?"1":"0";
			$dilated_other=$arrDilation[$i]["other_desc"];
			$dilated_time=$arrDilation[$i]["time"];
			$dilated_date=$arrDilation[$i]["dt"];
      $dilated_eye=$arrDilation[$i]["eye"];
      $chk_OU="";$chk_OD="";$chk_OS="";
      if(empty($dilated_eye)){
        $dilated_eye = !empty($sideIop) ? $sideIop : "OU"; 
      }
      if($dilated_eye=="OD"){ $chk_OD="checked"; }
      if($dilated_eye=="OS"){ $chk_OS="checked"; }
      if($dilated_eye=="OU"){ $chk_OU="checked"; }



			$chk_other=($other==1)?'checked':"";

			//previous saved into other
			if(!empty($strDilate_pt_db)){
				$dilated_other = ($dilated_other=="Other"||empty($dilated_other)) ? "".$strDilate_pt_db : $dilated_other.",".$strDilate_pt_db;
			}

			if($i==0){
				$imgDilation = "<span class=\"btnAddIop glyphicon glyphicon-plus-sign\" onClick=\"addDilation()\" ></span>";
			}else{
				$imgDilation = "<span class=\"btnDelIop glyphicon glyphicon-remove-sign\" onClick=\"delDilation(".$j.")\" ></span>";
			}

			if(!empty($strDilate_db)){   $strDilate_db="<div class=\"dv_opthdrop col-lg-8 anelist\"><table class=\"table-responsive\">".$strDilate_db."</table></div>";	}

			$strDilation.="<div id=\"tr_dilate_".$j."\" class=\"row\" >".
			$strDilate_db."
				<div class=\"col-lg-4\">
					<div class=\"othbox\">
            <ul>
              <li class=\"form-inline\">
                <input type=\"text\" name=\"other_desc[]\" id=\"other_desc".$j."\" value=\"".$dilated_other."\" onchange=\"chkDOther(this)\" class=\"form-control\" placeholder=\"Other\" >
                <input type=\"text\" name=\"curdates[]\" id=\"curdates".$j."\" value=\"".$dilated_date."\"  class=\"form-control date-pick\" placeholder=\"Date\" size=\"10\">
                <input type=\"text\" name=\"curtimes[]\" id=\"curtimes".$j."\" value=\"".$dilated_time."\" onClick=\"this.value=currenttime();\" class=\"form-control\" placeholder=\"Time\" size=\"6\">
              </li>
              <li class=\"ousml\"><input type=\"radio\" name=\"dileye".$j."\" value=\"OU\"  id=\"dileye_ou".$j."\"  ".$chk_OU." /><label for=\"dileye_ou".$j."\"></label></li>
              <li class=\"odsml\"><input type=\"radio\" name=\"dileye".$j."\" value=\"OD\"  id=\"dileye_od".$j."\"  ".$chk_OD."  /><label for=\"dileye_od".$j."\"></label></li>
              <li class=\"ossml\"><input type=\"radio\" name=\"dileye".$j."\" value=\"OS\" id=\"dileye_os".$j."\"  ".$chk_OS." /><label for=\"dileye_os".$j."\"></label></li>
              <li >".$imgDilation."</li>
            </ul>
					</div>
				</div>
			</div>
			";
		}
		$strDilation.="</div>";
		echo $strDilation;
		//<!-- dilation -->

		//<!-- dilated -->
		$arrDilatedmm = explode("~!!~", $dilated_mm);
		$arrDilatedmm_Od = (isset($arrDilatedmm[0])&&!empty($arrDilatedmm[0])) ? explode(",",$arrDilatedmm[0]) : array();
		$arrDilatedmm_Os = (isset($arrDilatedmm[1])&&!empty($arrDilatedmm[1])) ? explode(",",$arrDilatedmm[1]) : array();

		//
		$strDltd="";
		$strDltd.="<table class=\"table table-bordered table-striped\"> ";
		$strDltd_od=$strDltd_os="";

		$strDltd_od = "<tr><td width=\"11%\" rowspan=\"2\" class=\"dtlbox\">Dilated</td><td align=\"center\"><img src=\"../../library/images/od_sm_h.png\" alt=\"\"/></td>";
		$strDltd_os = "<td align=\"center\"><img src=\"../../library/images/os_sm_h.png\" alt=\"\"/></td>";

		$arrDilatOPs = array("Well","Poorly");
		foreach($arrDilatOPs as $keyOpt => $valOpt){
			$nmod="elem_dilatedOd_".$valOpt."";
			$nmos="elem_dilatedOs_".$valOpt."";
			$vl=$valOpt;
			$chkod= (in_array($vl,$arrDilatedmm_Od)) ? "checked=\"checked\"" : "" ;
			$chkos= (in_array($vl,$arrDilatedmm_Os)) ? "checked=\"checked\"" : "" ;
			$strDltd_od.="<td >".
					"<input type=\"checkbox\" id=\"".$nmod."\" name=\"".$nmod."\" value=\"".$vl."\" ".$chkod." ><label for=\"".$nmod."\">".$vl."</label></td>";
			$strDltd_os.="<td ><input type=\"checkbox\" id=\"".$nmos."\" name=\"".$nmos."\" value=\"".$vl."\" ".$chkos." ><label for=\"".$nmos."\">".$vl."</label></td >";
		}

		for($i=1;$i<=8;$i++){
			$nmod="elem_dilatedOd_".$i."mm";
			$nmos="elem_dilatedOs_".$i."mm";
			$vl="".$i."mm";
			$chkod= (in_array($i."mm",$arrDilatedmm_Od)) ? "checked=\"checked\"" : "" ;
			$chkos= (in_array($i."mm",$arrDilatedmm_Os)) ? "checked=\"checked\"" : "" ;

			$strDltd_od.="<td >".
						"<input type=\"checkbox\" id=\"".$nmod."\" name=\"".$nmod."\" value=\"".$vl."\" ".$chkod." ><label for=\"".$nmod."\" >".$vl."</label></td>";
			$strDltd_os.="<td ><input type=\"checkbox\" id=\"".$nmos."\" name=\"".$nmos."\" value=\"".$vl."\" ".$chkos." ><label for=\"".$nmos."\">".$vl."</label></td >";
		}

		$strDltd = $strDltd.$strDltd_od."</tr><tr>".$strDltd_os."</tr></table>";

		?>
		<div class="pd10">
			<div class="row">
				<?php
					echo $strDltd;
				?>
			</div>
			<div class="row">
				<div class="clearfix"></div>
				<div class="form-inline">
					Permission given by
					<input type="checkbox"  id="permissionby_Mother" name="permissionby[]"  value="Mother" <?php if(in_array("Mother",$arrPermissionBy)) echo 'checked';?> ><label for="permissionby_Mother">Mother</label>,&nbsp;&nbsp;
					<input type="checkbox"  id="permissionby_Father" name="permissionby[]"  value="Father" <?php if(in_array("Father",$arrPermissionBy)) echo 'checked';?> ><label for="permissionby_Father">Father</label>,&nbsp;&nbsp;
					<input type="text" name="permissionby_other"  value="<?php echo $permissionby_other;?>" class="form-control" placeholder="Text input">
				</div>
				<div class="clearfix"></div>
				<div>
					<input type="checkbox"  id="patientwarneds" name="patientwarneds"  value="1" <?php if($warned_n_advised==1) echo 'checked';?> >
					<label for="patientwarneds">Patient warned of blurred vision from dilation and offered / advised to wear sunglasses</label>.
					<input type="checkbox" id="patientnot_Driving" name="patientnot_Driving"  value="1" <?php if($patient_not_driving==1) echo 'checked';?> >
					<label for="patientnot_Driving">Patient not driving</label>.
				</div>
				<div class="clearfix"></div>
				<div>
					<input <?php if($patientAllergic==1) echo 'CHECKED'; ?> onClick="return change_col(this, 'allergicTextArea', 'allergicTd');" id="patientAllergic" name="patientAllergic" value="1" type="checkbox">
					<label id="allergicTd" for="patientAllergic" <?php if($patientAllergic==1) echo ' class="text-danger" '; ?>   >Patient allergic to Dilation drops</label>
					<div id="allergicTextArea" class="<?php if($patientAllergic==1) echo ''; else echo 'hidden'; ?>">
						<textarea name="allergicComments" cols="100" rows="2" placeholder="Comments:" class="form-control"><?php echo $allergicComments; ?></textarea>
					</div>
				</div>
				<div class="clearfix"></div>
				<div>
					<input <?php if($unableDilation==1) echo 'CHECKED'; ?> onClick="return change_col(this, 'unableDilateTextArea', 'dilateUnableTd');" id="unableDilation" name="unableDilation" value="1" type="checkbox">
					<label id="dilateUnableTd" for="unableDilation" <?php if($unableDilation==1) echo ' class="text-danger" '; ?>>Patient refuses/unable to dilate</label>
					<div id="unableDilateTextArea" class="<?php if($unableDilation==1) echo ''; else echo 'hidden'; ?>">
						<textarea name="unableDilateComments" cols="100" rows="2" placeholder="Comments:" class="form-control"><?php echo $unableDilateComments; ?></textarea>
					</div>
				</div>
			</div>
		</div>

		<!--
		<div class="pd10">
			<div class="row">
				<div class="col-sm-8 table-responsive">
					<table class="table table-bordered table-bordered">
					<tr>
					<td width="12%">Cyclogyl <span class="cont">1%</span></td>
					<td width="13%">Mydriacy <span class="cont">1%</span></td>
					<td width="15%">Mydriacy <span class="cont">1/2% </span></td>
					<td width="8%">Paremyd</td>
					<td width="12%">Phenylephrine...</td>
					<td>Phenylephrine <span class="cont">5%</span></td>
					</tr>

					<tr>
					<td colspan="6">
						<table class="table table-bordered table-striped">
						<tr>
						<td width="11%" rowspan="2" class="dtlbox">Dilated</td>
						<td width="7%" align="center"><img src="../../library/images/od_sm_h.png" alt=""/></td>
						<td width="7%">Well</td>
						<td width="11%">Poorly </td>
						<td width="8%">1mm</td>
						<td width="8%" >2mm</td>
						<td width="8%" >3mm</td>
						<td width="8%" >4mm</td>
						<td width="8%" >5mm</td>
						<td width="8%" >6mm</td>
						<td width="8%" >7mm</td>
						<td width="8%" >8mm</td>
						</tr>
						<tr>
						<td align="center"><img src="../../library/images/os_sm_h.png" alt=""/></td>
						<td>Well</td>
						<td>Poorly </td>
						<td>1mm</td>
						<td >2mm</td>
						<td >3mm</td>
						<td >4mm</td>
						<td >5mm</td>
						<td >6mm</td>
						<td >7mm</td>
						<td >8mm</td>
						</tr>
						</table>
					</td>
					</tr>
					</table>
					<div class="clearfix"></div>
					<div class="form-inline"> Permission given by  <strong>Mother</strong>, <strong>Father</strong>, <input type="text" class="form-control" placeholder="Text input"></div>
					<div class="clearfix"></div>
					<div>Patient warned of blurred vision from dilation and offered / advised to wear sunglasses ______   Patient not driving_____ </div>
					<div class="clearfix"></div>
					<div>Patient allergic to Dilation drops</div>
					<div class="clearfix"></div>
					<div>Patient refuses / unable to dilate</div>
				</div>
				<div class="col-sm-4">
					<div class="othbox">
						<div class="row">
							<div class="col-sm-6 form-group"><label for="">Other</label>
							<input type="" class="form-control" id="" placeholder=""></div>
							<div class="col-sm-4 form-group"><label for="">Time</label>
							<input type="" class="form-control" id="" placeholder=""></div>
							<div class="col-sm-2 addmt"><img src="../../library/images/addinput.png" alt=""/></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		-->

	</div>
	<div class="clearfix"></div>
	<!-- Dilation -->

	<!-- OOD -->
	<div id="divOOD" class="iopanth">

	<?php

		$lenOOD = is_array($arrOOD) ? count($arrOOD) : 0 ;
		if(empty($lenOOD))$lenOOD=1;
		$strOOD="<div id=\"conOOD\" data-OODLen=\"".$lenOOD."\">";

		for($i=0;$i<$lenOOD;$i++){

		$j=$i+1;

		//--

		$strOod_pt_db = $arrOOD[$i]["ood"];
		$arrOod_pt_db = (!empty($strOod_pt_db)) ? explode(",", $strOod_pt_db) : array();
		$arrOod_pt_db = array_map('trim', $arrOod_pt_db);
		$strOod_db="";
		$all_empty_flg=0;
		$td_c=1;
		foreach($arr_db_ood as $key_da => $val_da){
			if(strpos($arrOOD[$i]["ood"],$val_da)!==false){
				$tmp_chkd = "1";
				//$strOod_pt_db=str_replace($val_da, "", $strOod_pt_db);
				if (($key_rem = array_search(trim($val_da), $arrOod_pt_db)) !== false){ unset($arrOod_pt_db[$key_rem]);}
			}else{
				$tmp_chkd = "0";
			}

			$chkF= ($tmp_chkd == "1") ? "checked=\"checked\"" : "";
			$all_empty_flg+=$tmp_chkd;

			$val_da_el_nm=mk_var_nm($val_da,"ood");
			$val_da_dis = $val_da;
			if(strlen($val_da_dis)>$od_nm_ln){ $val_da_dis=substr($val_da_dis,0,$od_nm_ln-2).".."; }

			if($td_c==1){ $strOod_db.= "<tr>"; }
			$strOod_db.="<td>";
			$strOod_db.="<input name=\"".$val_da_el_nm."[]\" type=\"checkbox\" id=\"".$val_da_el_nm."_".$j."\" value=\"".$j."\"
						onClick=\"checkDTime(this)\" ".$chkF." ><label for=\"".$val_da_el_nm."_".$j."\" title=\"".$val_da."\">".$val_da_dis."</label>";
			$strOod_db.="</td>";
			$td_c++;
			if($td_c>$tr_lm_gl[2]){ $strOod_db.= "</tr>"; $td_c=1; }
		}


		//remove Other
		if (($key_rem = array_search("Other", $arrOod_pt_db)) !== false){ unset($arrOod_pt_db[$key_rem]);}
		$strOod_pt_db = implode(",",$arrOod_pt_db);
		$strOod_pt_db = trim($strOod_pt_db,",");
		$strOod_pt_db = preg_replace("/\,+/",",",$strOod_pt_db);

		//--

		$other=(strpos($arrOOD[$i]["ood"],"Other")!==false)?"1":"0";
		$ood_other=$arrOOD[$i]["other_desc"];
		$ood_time=$arrOOD[$i]["time"];
		$ood_dt=$arrOOD[$i]["dt"];
		$ood_eye=$arrOOD[$i]["eye"];

		//previous saved into other
		if(!empty($strOod_pt_db)){
			$ood_other = ($ood_other=="Other"||empty($ood_other)) ? "".$strOod_pt_db : $ood_other.",".$strOod_pt_db;
		}

		$chk_OU=$chk_OD=$chk_OS="";
		if($ood_eye=="OU"){
			$chk_OU= "checked";
		}else if($ood_eye=="OD"){
			$chk_OD= "checked";
		}else if($ood_eye=="OS"){
			$chk_OS= "checked";
		}

		//IOP – Other Ophthalmic Drops  - Make OU default.
		if(empty($all_empty_flg) && empty($other) && empty($chk_OU)){
			$chk_OU= "checked";
		}

		if($i==0){
			$imgOOD = "<span class=\"btnAddIop glyphicon glyphicon-plus-sign\" onClick=\"addOOD()\" ></span>";
		}else{
			$imgOOD = "<span class=\"btnDelIop glyphicon glyphicon-remove-sign\" onClick=\"delOOD(".$j.",'OOD')\" ></span>";
		}


		if(!empty($strOod_db)){   $strOod_db="<div class=\"dv_opthdrop col-lg-5 anelist\"><table class=\"table-responsive\">".$strOod_db."</table></div>"; }

		$strOOD.= "
		<div id=\"tr_OOD_".$j."\" class=\"row\">
			<div class=\"col-lg-2 \"><span class=\"iopanthead\">Other Ophthalmic Drops</span></div>
		".
		$strOod_db.
		"<div class=\"dv_opthdrop_time col-lg-5 anthsopt\">".
		"	<ul>
			<li class=\"form-inline\">
			<input type=\"text\" name=\"other_desc_ood[]\" id=\"other_desc_ood".$j."\" value=\"".$ood_other."\" onchange=\"chkDOther(this)\" class=\"form-control\" placeholder=\"Other\" >
			<input type=\"text\" name=\"curdates_ood[]\" id=\"curdates_ood".$j."\"  value=\"".$ood_dt."\"  class=\"form-control date-pick\" placeholder=\"Date\" size=\"10\">
			<input type=\"text\" name=\"curtimes_ood[]\" id=\"curtimes_ood".$j."\"  value=\"".$ood_time."\" onClick=\"this.value=currenttime();\" class=\"form-control\" placeholder=\"Time\" size=\"6\">
			</li>
			<li class=\"ousml\"><input type=\"radio\" name=\"oodeye".$j."\" value=\"OU\"  id=\"oodeye_ou".$j."\"  ".$chk_OU."  /><label for=\"oodeye_ou".$j."\"></label></li>
			<li class=\"odsml\"><input type=\"radio\" name=\"oodeye".$j."\" value=\"OD\"  id=\"oodeye_od".$j."\"  ".$chk_OD."  /><label for=\"oodeye_od".$j."\"></label></li>
			<li class=\"ossml\"><input type=\"radio\" name=\"oodeye".$j."\" value=\"OS\" id=\"oodeye_os".$j."\"  ".$chk_OS." /><label for=\"oodeye_os".$j."\"></label></li>
			<li >".$imgOOD."</li>
			</ul>
		</div>
		</div>
		";

		}//End for loop

		$strOOD.="</div>";
		echo $strOOD;

	?>

	<!--
	<div class="row">
		<div class="col-lg-2 "><span class="iopanthead">Other Ophthalmic Drops</span></div>
		<div class="col-lg-5 anelist">
			<ul>
			<li>Alphagan P <span class="cont">0.1%</span></li>
			<li>Alphagan P <span class="cont">1.0%</span></li>
			<li>Diamox</li>
			<li>Iopidine <span class="cont">0.5%</span></li>
			<li>Pilo <span class="cont">1%</span></li>
			</ul>
		</div>
		<div class="col-lg-5 anthsopt">
			<ul>
			<li class="form-inline"><input type="text" class="form-control" placeholder="Other"> <input type="text" class="form-control" placeholder="Time"></li>
			<li class="ousml"><input type='radio' name='thing1' value='valuable' id="thing1"/><label for="thing1"></label></li>
			<li class="odsml"><input type='radio' name='thing2' value='valuable' id="thing2"/><label for="thing2"></label></li>
			<li class="ossml"><input type='radio' name='thing3' value='valuable' id="thing3"/><label for="thing3"></label></li>
			<li ><img src="../../library/images/addinput1.png" alt=""/></li>
			</ul>
		</div>
	</div>
	-->

	</div>
	<div class="clearfix"></div>
	<!-- OOD -->
	<div class="row"><div class="col-sm-4"><h2></h2></div></div>
    </div>
    <div role="tabpanel" class="tab-pane <?php echo ("Iop" == $defTabKey) ? "active" : "" ?>" id="divIop">
	<!-- GONIO -->

		<div class="examhd form-inline">
			<?php if($finalize_flag == 1){?>
				<label class="chart_status label label-danger pull-left">Finalized</label>
			<?php }?>
			<span id="examFlag" class="glyphicon flagWnl "></span>
			<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

			<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();"
						<?php echo ($elem_noChange == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
			<label class="lbl_nochange frcb" for="elem_noChange">NO Change</label>

			<?php /*if (constant('AV_MODULE')=='YES'){?>
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" />
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
			<?php }*/ ?>

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
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
		</td>
		</tr>

		<tr id="d_AllQuad" class="grp_AllQuad">
		<td >All Quadrant</td>
		<td>
		<input id="od_1" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_atm" value="ATM" <?php echo ($elem_allQuadOd_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="od_1">ATM</label>
		</td>
		<td>
		<input id="od_2" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_tm" value="TM" <?php echo ($elem_allQuadOd_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="od_2">TM</label>
		</td>
		<td>
		<input id="od_3" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_ptm" value="PTM" <?php echo ($elem_allQuadOd_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="od_3">PTM</label>
		</td>
		<td>
		<input id="od_5" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_ss" value="SS" <?php echo ($elem_allQuadOd_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="od_5">SS</label>
		</td>
		<td>
		<input id="od_4" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_cb" value="CB" <?php echo ($elem_allQuadOd_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="od_4">CB</label>
		</td>
		<td>
		<input id="od_6" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_notVisible" value="Not Visible" <?php echo ($elem_allQuadOd_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="od_6" class="aato">Not Visible</label>
		</td>
		<td ></td>
		<td align="center" class="bilat" onClick="check_bl('AllQuad')" rowspan="3">BL</td>
		<td >All Quadrant</td>
		<td>
		<input id="os_1" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_atm" value="ATM" <?php echo ($elem_allQuadOs_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="os_1">ATM</label>
		</td>
		<td>
		<input id="os_2" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_tm" value="TM" <?php echo ($elem_allQuadOs_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="os_2">TM</label>
		</td>
		<td>
		<input id="os_3" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_ptm" value="PTM" <?php echo ($elem_allQuadOs_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="os_3">PTM</label>
		</td>
		<td>
		<input id="os_5" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_ss" value="SS" <?php echo ($elem_allQuadOs_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="os_5">SS</label>
		</td>
		<td>
		<input id="os_4" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_cb" value="CB" <?php echo ($elem_allQuadOs_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="os_4">CB</label>
		</td>
		<td>
		<input id="os_6" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_notVisible" value="Not Visible" <?php echo ($elem_allQuadOs_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="os_6" class="aato">Not Visible</label>
		</td>
		<td ></td>
		</tr>

		<tr class="grp_AllQuad">
		<td >Pigmentation</td>
		<td>
		<input id="od_7" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_pig_T" value="T" <?php echo ($elem_allQuadOd_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_7">T</label>
		</td>
		<td>
		<input id="od_8" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_pig_pos1" value="1+" <?php echo ($elem_allQuadOd_pig_pos1 == "+1" || $elem_allQuadOd_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_8">1+</label>
		</td>
		<td>
		<input id="od_9" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_pig_pos2" value="2+" <?php echo ($elem_allQuadOd_pig_pos2 == "+2" || $elem_allQuadOd_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_9">2+</label>
		</td>
		<td>
		<input id="od_10" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_pig_pos3" value="3+" <?php echo ($elem_allQuadOd_pig_pos3 == "+3" || $elem_allQuadOd_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_10">3+</label>
		</td>
		<td>
		<input id="od_11" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_pig_pos4" value="4+" <?php echo ($elem_allQuadOd_pig_pos4 == "+4" || $elem_allQuadOd_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_11">4+</label>
		</td>
		<td ></td>
		<td ></td>
		<td >Pigmentation</td>
		<td>
		<input id="os_7" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_pig_T" value="T" <?php echo ($elem_allQuadOs_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_7">T</label>
		</td>
		<td>
		<input id="os_8" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_pig_pos1" value="1+" <?php echo ($elem_allQuadOs_pig_pos1 == "+1" || $elem_allQuadOs_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_8">1+</label>
		</td>
		<td>
		<input id="os_9" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_pig_pos2" value="2+" <?php echo ($elem_allQuadOs_pig_pos2 == "+2" || $elem_allQuadOs_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_9">2+</label>
		</td>
		<td>
		<input id="os_10" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_pig_pos3" value="3+" <?php echo ($elem_allQuadOs_pig_pos3 == "+3" || $elem_allQuadOs_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_10">3+</label>
		</td>
		<td>
		<input id="os_11" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_pig_pos4" value="4+" <?php echo ($elem_allQuadOs_pig_pos4 == "+4" || $elem_allQuadOs_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_11">4+</label>
		</td>
		<td ></td>
		<td ></td>
		</tr>

		<tr class="grp_AllQuad">
		<td >Iris Convexity</td>
		<td>
		<input id="od_12" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_T" value="T" <?php echo ($elem_allQuadOd_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_12">T</label>
		</td>
		<td>
		<input id="od_13" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_pos1" value="1+" <?php echo ($elem_allQuadOd_irisCon_pos1 == "+1" || $elem_allQuadOd_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_13">1+</label>
		</td>
		<td>
		<input id="od_14" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_pos2" value="2+" <?php echo ($elem_allQuadOd_irisCon_pos2 == "+2" || $elem_allQuadOd_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_14">2+</label>
		</td>
		<td>
		<input id="od_15" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_pos3" value="3+" <?php echo ($elem_allQuadOd_irisCon_pos3 == "+3" || $elem_allQuadOd_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_15">3+</label>
		</td>
		<td>
		<input id="od_16" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_pos4" value="4+" <?php echo ($elem_allQuadOd_irisCon_pos4 == "+4" || $elem_allQuadOd_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_16">4+</label>
		</td>
		<td>
		<input id="od_18" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_flat" value="Flat" <?php echo ($elem_allQuadOd_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="od_18">Flat</label>
		</td>
		<td>
		<input id="od_17" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOd_irisCon_concave" value="Concave" <?php echo ($elem_allQuadOd_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="od_17" class="aato">Concave</label>
		</td>
		<td >Iris Convexity</td>
		<td>
		<input id="os_12" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_T" value="T" <?php echo ($elem_allQuadOs_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_12">T</label>
		</td>
		<td>
		<input id="os_13" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_pos1" value="1+" <?php echo ($elem_allQuadOs_irisCon_pos1 == "+1" || $elem_allQuadOs_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_13">1+</label>
		</td>
		<td>
		<input id="os_14" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_pos2" value="2+" <?php echo ($elem_allQuadOs_irisCon_pos2 == "+2" || $elem_allQuadOs_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_14">2+</label>
		</td>
		<td>
		<input id="os_15" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_pos3" value="3+" <?php echo ($elem_allQuadOs_irisCon_pos3 == "+3" || $elem_allQuadOs_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_15">3+</label>
		</td>
		<td>
		<input id="os_16" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_pos4" value="4+" <?php echo ($elem_allQuadOs_irisCon_pos4 == "+4" || $elem_allQuadOs_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_16">4+</label>
		</td>
		<td>
		<input id="os_18" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_flat" value="Flat" <?php echo ($elem_allQuadOs_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="os_18">Flat</label>
		</td>
		<td>
		<input id="os_17" type="checkbox"  onclick="checkwnls()" name="elem_allQuadOs_irisCon_concave" value="Concave" <?php echo ($elem_allQuadOs_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="os_17" class="aato">Concave</label>
		</td>
		</tr>

		<tr id="d_Superior" class="grp_Superior">
		<td >Superior</td>
		<td>
		<input id="od_19" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_atm" value="ATM" <?php echo ($elem_superiorOd_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="od_19">ATM</label>
		</td>
		<td>
		<input id="od_20" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_tm" value="TM" <?php echo ($elem_superiorOd_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="od_20">TM</label>
		</td>
		<td>
		<input id="od_21" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_ptm" value="PTM" <?php echo ($elem_superiorOd_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="od_21">PTM</label>
		</td>
		<td>
		<input id="od_23" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_ss" value="SS" <?php echo ($elem_superiorOd_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="od_23">SS</label>
		</td>
		<td>
		<input id="od_22" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_cb" value="CB" <?php echo ($elem_superiorOd_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="od_22">CB</label>
		</td>
		<td>
		<input id="od_24" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_notVisible" value="Not Visible" <?php echo ($elem_superiorOd_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="od_24" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		<td align="center" class="bilat" onClick="check_bl('Superior')" rowspan="3">BL</td>
		<td >Superior</td>
		<td>
		<input id="os_19" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_atm" value="ATM" <?php echo ($elem_superiorOs_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="os_19">ATM</label>
		</td>
		<td>
		<input  id="os_20" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_tm" value="TM" <?php echo ($elem_superiorOs_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="os_20">TM</label>
		</td>
		<td>
		<input  id="os_21" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_ptm" value="PTM" <?php echo ($elem_superiorOs_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="os_21">PTM</label>
		</td>
		<td>
		<input id="os_23" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_ss" value="SS" <?php echo ($elem_superiorOs_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="os_23">SS</label>
		</td>
		<td>
		<input  id="os_22" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_cb" value="CB" <?php echo ($elem_superiorOs_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="os_22">CB</label>
		</td>
		<td>
		<input id="os_24" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_notVisible" value="Not Visible" <?php echo ($elem_superiorOs_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="os_24" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Superior">
		<td >Pigmentation</td>
		<td>
		<input id="od_25" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_pig_T" value="T" <?php echo ($elem_superiorOd_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_25">T</label>
		</td>
		<td>
		<input id="od_26" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_pig_pos1" value="1+" <?php echo ($elem_superiorOd_pig_pos1 == "+1" || $elem_superiorOd_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_26">1+</label>
		</td>
		<td>
		<input id="od_27" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_pig_pos2" value="2+" <?php echo ($elem_superiorOd_pig_pos2 == "+2" || $elem_superiorOd_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_27">2+</label>
		</td>
		<td>
		<input id="od_28"  type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_pig_pos3" value="3+" <?php echo ($elem_superiorOd_pig_pos3 == "+3" || $elem_superiorOd_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_28">3+</label>
		</td>
		<td>
		<input id="od_29" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_pig_pos4" value="4+" <?php echo ($elem_superiorOd_pig_pos4 == "+4" || $elem_superiorOd_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_29">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<td >Pigmentation</td>
		<td>
		<input id="os_25" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_pig_T" value="T" <?php echo ($elem_superiorOs_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_25">T</label>
		</td>
		<td>
		<input id="os_26" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_pig_pos1" value="1+" <?php echo ($elem_superiorOs_pig_pos1 == "+1" || $elem_superiorOs_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_26">1+</label>
		</td>
		<td>
		<input id="os_27" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_pig_pos2" value="2+" <?php echo ($elem_superiorOs_pig_pos2 == "+2" || $elem_superiorOs_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_27">2+</label>
		</td>
		<td>
		<input id="os_28" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_pig_pos3" value="3+" <?php echo ($elem_superiorOs_pig_pos3 == "+3" || $elem_superiorOs_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_28">3+</label>
		</td>
		<td>
		<input id="os_29" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_pig_pos4" value="4+" <?php echo ($elem_superiorOs_pig_pos4 == "+4" || $elem_superiorOs_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_29">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Superior">
		<td >Iris Convexity</td>
		<td>
		<input id="od_30" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_T" value="T" <?php echo ($elem_superiorOd_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_30">T</label>
		</td>
		<td>
		<input id="od_31" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_pos1" value="1+" <?php echo ($elem_superiorOd_irisCon_pos1 == "+1" || $elem_superiorOd_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_31">1+</label>
		</td>
		<td>
		<input id="od_32" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_pos2" value="2+" <?php echo ($elem_superiorOd_irisCon_pos2 == "+2" || $elem_superiorOd_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_32">2+</label>
		</td>
		<td>
		<input id="od_33" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_pos3" value="3+" <?php echo ($elem_superiorOd_irisCon_pos3 == "+3" || $elem_superiorOd_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_33">3+</label>
		</td>
		<td>
		<input id="od_34" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_pos4" value="4+" <?php echo ($elem_superiorOd_irisCon_pos4 == "+4" || $elem_superiorOd_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_34">4+</label>
		</td>
		<td>
		<input id="od_36" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_flat" value="Flat" <?php echo ($elem_superiorOd_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="od_36">Flat</label>
		</td>
		<td>
		<input id="od_35" type="checkbox"  onclick="checkwnls()" name="elem_superiorOd_irisCon_concave" value="Concave" <?php echo ($elem_superiorOd_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="od_35" class="aato">Concave</label>
		</td>
		<td >Iris Convexity</td>
		<td>
		<input  id="os_30" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_T" value="T" <?php echo ($elem_superiorOs_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_30">T</label>
		</td>
		<td>
		<input id="os_31" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_pos1" value="1+" <?php echo ($elem_superiorOs_irisCon_pos1 == "+1" || $elem_superiorOs_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_31">1+</label>
		</td>
		<td>
		<input id="os_32" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_pos2" value="2+" <?php echo ($elem_superiorOs_irisCon_pos2 == "+2" || $elem_superiorOs_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_32">2+</label>
		</td>
		<td>
		<input id="os_33" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_pos3" value="3+" <?php echo ($elem_superiorOs_irisCon_pos3 == "+3" || $elem_superiorOs_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_33">3+</label>
		</td>
		<td>
		<input id="os_34" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_pos4" value="4+" <?php echo ($elem_superiorOs_irisCon_pos4 == "+4" || $elem_superiorOs_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_34">4+</label>
		</td>
		<td>
		<input id="os_36" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_flat" value="Flat" <?php echo ($elem_superiorOs_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="os_36">Flat</label>
		</td>
		<td>
		<input id="os_35" type="checkbox"  onclick="checkwnls()" name="elem_superiorOs_irisCon_concave" value="Concave" <?php echo ($elem_superiorOs_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="os_35" class="aato">Concave</label>
		</td>
		</tr>

		<tr  id="d_Inferior" class="grp_Inferior">
		<td >Inferior</td>
		<td>
		<input id="od_37" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_atm" value="ATM" <?php echo ($elem_inferiorOd_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="od_37">ATM</label>
		</td>
		<td>
		<input  id="od_38" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_tm" value="TM" <?php echo ($elem_inferiorOd_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="od_38">TM</label>
		</td>
		<td>
		<input  id="od_39" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_ptm" value="PTM" <?php echo ($elem_inferiorOd_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="od_39">PTM</label>
		</td>
		<td>
		<input  id="od_41" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_ss" value="SS" <?php echo ($elem_inferiorOd_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="od_41">SS</label>
		</td>
		<td>
		<input  id="od_40" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_cb" value="CB" <?php echo ($elem_inferiorOd_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="od_40">CB</label>
		</td>
		<td>
		<input  id="od_42" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_notVisible" value="Not Visible" <?php echo ($elem_inferiorOd_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="od_42" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		<td align="center" class="bilat" onClick="check_bl('Inferior')" rowspan="3">BL</td>
		<td >Inferior</td>
		<td>
		<input  id="os_37" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_atm" value="ATM" <?php echo ($elem_inferiorOs_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="os_37">ATM</label>
		</td>
		<td>
		<input id="os_38" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_tm" value="TM" <?php echo ($elem_inferiorOs_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="os_38">TM</label>
		</td>
		<td>
		<input id="os_39" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_ptm" value="PTM" <?php echo ($elem_inferiorOs_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="os_39">PTM</label>
		</td>
		<td>
		<input id="os_41" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_ss" value="SS" <?php echo ($elem_inferiorOs_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="os_41">SS</label>
		</td>
		<td>
		<input id="os_40" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_cb" value="CB" <?php echo ($elem_inferiorOs_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="os_40">CB</label>
		</td>
		<td>
		<input id="os_42" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_notVisible" value="Not Visible" <?php echo ($elem_inferiorOs_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="os_42" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Inferior">
		<td >Pigmentation</td>
		<td>
		<input  id="od_43" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_pig_T" value="T" <?php echo ($elem_inferiorOd_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_43">T</label>
		</td>
		<td>
		<input id="od_44"  type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_pig_pos1" value="1+" <?php echo ($elem_inferiorOd_pig_pos1 == "+1" || $elem_inferiorOd_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_44">1+</label>
		</td>
		<td>
		<input id="od_45" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_pig_pos2" value="2+" <?php echo ($elem_inferiorOd_pig_pos2 == "+2" || $elem_inferiorOd_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_45">2+</label>
		</td>
		<td>
		<input id="od_46" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_pig_pos3" value="3+" <?php echo ($elem_inferiorOd_pig_pos3 == "+3" || $elem_inferiorOd_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_46">3+</label>
		</td>
		<td>
		<input id="od_47" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_pig_pos4" value="4+" <?php echo ($elem_inferiorOd_pig_pos4 == "+4" || $elem_inferiorOd_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_47">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<td >Pigmentation</td>
		<td>
		<input id="os_43" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_pig_T" value="T" <?php echo ($elem_inferiorOs_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_43">T</label>
		</td>
		<td>
		<input id="os_44" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_pig_pos1" value="1+" <?php echo ($elem_inferiorOs_pig_pos1 == "+1" || $elem_inferiorOs_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_44">1+</label>
		</td>
		<td>
		<input id="os_45" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_pig_pos2" value="2+" <?php echo ($elem_inferiorOs_pig_pos2 == "+2" || $elem_inferiorOs_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_45">2+</label>
		</td>
		<td>
		<input id="os_46" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_pig_pos3" value="3+" <?php echo ($elem_inferiorOs_pig_pos3 == "+3" || $elem_inferiorOs_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_46">3+</label>
		</td>
		<td>
		<input id="os_47" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_pig_pos4" value="4+" <?php echo ($elem_inferiorOs_pig_pos4 == "+4" || $elem_inferiorOs_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_47">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Inferior">
		<td >Iris Convexity</td>
		<td>
		<input id="od_48" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_T" value="T" <?php echo ($elem_inferiorOd_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_48">T</label>
		</td>
		<td>
		<input id="od_49" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_pos1" value="1+" <?php echo ($elem_inferiorOd_irisCon_pos1 == "+1" || $elem_inferiorOd_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_49">1+</label>
		</td>
		<td>
		<input id="od_50" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_pos2" value="2+" <?php echo ($elem_inferiorOd_irisCon_pos2 == "+2" || $elem_inferiorOd_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_50">2+</label>
		</td>
		<td>
		<input id="od_51" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_pos3" value="3+" <?php echo ($elem_inferiorOd_irisCon_pos3 == "+3" || $elem_inferiorOd_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_51">3+</label>
		</td>
		<td>
		<input id="od_52" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_pos4" value="4+" <?php echo ($elem_inferiorOd_irisCon_pos4 == "+4" || $elem_inferiorOd_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_52">4+</label>
		</td>
		<td>
		<input id="od_54" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_flat" value="Flat" <?php echo ($elem_inferiorOd_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="od_54">Flat</label>
		</td>
		<td>
		<input id="od_53" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOd_irisCon_concave" value="Concave" <?php echo ($elem_inferiorOd_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="od_53" class="aato">Concave</label>
		</td>
		<td >Iris Convexity</td>
		<td>
		<input id="os_48" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_T" value="T" <?php echo ($elem_inferiorOs_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_48">T</label>
		</td>
		<td>
		<input id="os_49" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_pos1" value="1+" <?php echo ($elem_inferiorOs_irisCon_pos1 == "+1" || $elem_inferiorOs_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_49">1+</label>
		</td>
		<td>
		<input id="os_50" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_pos2" value="2+" <?php echo ($elem_inferiorOs_irisCon_pos2 == "+2" || $elem_inferiorOs_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_50">2+</label>
		</td>
		<td>
		<input id="os_51" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_pos3" value="3+" <?php echo ($elem_inferiorOs_irisCon_pos3 == "+3" || $elem_inferiorOs_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_51">3+</label>
		</td>
		<td>
		<input id="os_52" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_pos4" value="4+" <?php echo ($elem_inferiorOs_irisCon_pos4 == "+4" || $elem_inferiorOs_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_52">4+</label>
		</td>
		<td>
		<input id="os_54" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_flat" value="Flat" <?php echo ($elem_inferiorOs_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="os_54">Flat</label>
		</td>
		<td>
		<input id="os_53" type="checkbox"  onclick="checkwnls()" name="elem_inferiorOs_irisCon_concave" value="Concave" <?php echo ($elem_inferiorOs_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="os_53" class="aato">Concave</label>
		</td>
		</tr>

		<tr id="d_Nasal" class="grp_Nasal">
		<td >Nasal</td>
		<td>
		<input id="od_55" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_atm" value="ATM" <?php echo ($elem_nasalOd_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="od_55">ATM</label>
		</td>
		<td>
		<input id="od_56" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_tm" value="TM" <?php echo ($elem_nasalOd_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="od_56">TM</label>
		</td>
		<td>
		<input id="od_57" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_ptm" value="PTM" <?php echo ($elem_nasalOd_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="od_57">PTM</label>
		</td>
		<td>
		<input id="od_59" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_ss" value="SS" <?php echo ($elem_nasalOd_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="od_59">SS</label>
		</td>
		<td>
		<input id="od_58" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_cb" value="CB" <?php echo ($elem_nasalOd_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="od_58">CB</label>
		</td>
		<td>
		<input id="od_60" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_notVisible" value="Not Visible" <?php echo ($elem_nasalOd_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="od_60" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		<td align="center" class="bilat" onClick="check_bl('Nasal')" rowspan="3">BL</td>
		<td >Nasal</td>
		<td>
		<input id="os_55" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_atm" value="ATM" <?php echo ($elem_nasalOs_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="os_55">ATM</label>
		</td>
		<td>
		<input id="os_56" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_tm" value="TM" <?php echo ($elem_nasalOs_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="os_56">TM</label>
		</td>
		<td>
		<input id="os_57" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_ptm" value="PTM" <?php echo ($elem_nasalOs_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="os_57">PTM</label>
		</td>
		<td>
		<input id="os_59" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_ss" value="SS" <?php echo ($elem_nasalOs_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="os_59">SS</label>
		</td>
		<td>
		<input id="os_58" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_cb" value="CB" <?php echo ($elem_nasalOs_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="os_58">CB</label>
		</td>
		<td>
		<input id="os_60" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_notVisible" value="Not Visible" <?php echo ($elem_nasalOs_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="os_60" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Nasal">
		<td >Pigmentation</td>
		<td>
		<input id="od_61" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_pig_T" value="T" <?php echo ($elem_nasalOd_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_61">T</label>
		</td>
		<td>
		<input id="od_62" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_pig_pos1" value="1+" <?php echo ($elem_nasalOd_pig_pos1 == "+1" || $elem_nasalOd_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_62">1+</label>
		</td>
		<td>
		<input id="od_63" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_pig_pos2" value="2+" <?php echo ($elem_nasalOd_pig_pos2 == "+2" || $elem_nasalOd_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_63">2+</label>
		</td>
		<td>
		<input id="od_64" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_pig_pos3" value="3+" <?php echo ($elem_nasalOd_pig_pos3 == "+3" || $elem_nasalOd_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_64">3+</label>
		</td>
		<td>
		<input id="od_65" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_pig_pos4" value="4+" <?php echo ($elem_nasalOd_pig_pos4 == "+4" || $elem_nasalOd_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_65">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<td >Pigmentation</td>
		<td>
		<input id="os_61" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_pig_T" value="T" <?php echo ($elem_nasalOs_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_61">T</label>
		</td>
		<td>
		<input  id="os_62" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_pig_pos1" value="1+" <?php echo ($elem_nasalOs_pig_pos1 == "+1" || $elem_nasalOs_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_62">1+</label>
		</td>
		<td>
		<input  id="os_63" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_pig_pos2" value="2+" <?php echo ($elem_nasalOs_pig_pos2 == "+2" || $elem_nasalOs_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_63">2+</label>
		</td>
		<td>
		<input  id="os_64" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_pig_pos3" value="3+" <?php echo ($elem_nasalOs_pig_pos3 == "+3" || $elem_nasalOs_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_64">3+</label>
		</td>
		<td>
		<input  id="os_65" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_pig_pos4" value="4+" <?php echo ($elem_nasalOs_pig_pos4 == "+4" || $elem_nasalOs_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_65">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Nasal">
		<td >Iris Convexity</td>
		<td>
		<input id="od_66" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_T" value="T" <?php echo ($elem_nasalOd_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_66">T</label>
		</td>
		<td>
		<input id="od_67" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_pos1" value="1+" <?php echo ($elem_nasalOd_irisCon_pos1 == "+1" || $elem_nasalOd_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_67">1+</label>
		</td>
		<td>
		<input id="od_68" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_pos2" value="2+" <?php echo ($elem_nasalOd_irisCon_pos2 == "+2" || $elem_nasalOd_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_68">2+</label>
		</td>
		<td>
		<input id="od_69" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_pos3" value="3+" <?php echo ($elem_nasalOd_irisCon_pos3 == "+3" || $elem_nasalOd_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_69">3+</label>
		</td>
		<td>
		<input id="od_70" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_pos4" value="4+" <?php echo ($elem_nasalOd_irisCon_pos4 == "+4" || $elem_nasalOd_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_70">4+</label>
		</td>
		<td>
		<input id="od_72" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_flat" value="Flat" <?php echo ($elem_nasalOd_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="od_72">Flat</label>
		</td>
		<td>
		<input id="od_71" type="checkbox"  onclick="checkwnls()" name="elem_nasalOd_irisCon_concave" value="Concave" <?php echo ($elem_nasalOd_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="od_71" class="aato">Concave</label>
		</td>
		<td >Iris Convexity</td>
		<td>
		<input id="os_66" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_T" value="T" <?php echo ($elem_nasalOs_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_66">T</label>
		</td>
		<td>
		<input id="os_67" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_pos1" value="1+" <?php echo ($elem_nasalOs_irisCon_pos1 == "+1" || $elem_nasalOs_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_67">1+</label>
		</td>
		<td>
		<input id="os_68" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_pos2" value="2+" <?php echo ($elem_nasalOs_irisCon_pos2 == "+2" || $elem_nasalOs_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_68">2+</label>
		</td>
		<td>
		<input id="os_69" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_pos3" value="3+" <?php echo ($elem_nasalOs_irisCon_pos3 == "+3" || $elem_nasalOs_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_69">3+</label>
		</td>
		<td>
		<input id="os_70" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_pos4" value="4+" <?php echo ($elem_nasalOs_irisCon_pos4 == "+4" || $elem_nasalOs_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_70">4+</label>
		</td>
		<td>
		<input id="os_72" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_flat" value="Flat" <?php echo ($elem_nasalOs_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="os_72">Flat</label>
		</td>
		<td>
		<input id="os_71" type="checkbox"  onclick="checkwnls()" name="elem_nasalOs_irisCon_concave" value="Concave" <?php echo ($elem_nasalOs_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="os_71" class="aato">Concave</label>
		</td>
		</tr>

		<tr id="d_Tempo" class="grp_Tempo">
		<td >Temporal</td>
		<td>
		<input id="od_73" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_atm" value="ATM" <?php echo ($elem_temporalOd_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="od_73">ATM</label>
		</td>
		<td>
		<input id="od_74" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_tm" value="TM" <?php echo ($elem_temporalOd_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="od_74">TM</label>
		</td>
		<td>
		<input id="od_75" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_ptm" value="PTM" <?php echo ($elem_temporalOd_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="od_75">PTM</label>
		</td>
		<td>
		<input id="od_77" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_ss" value="SS" <?php echo ($elem_temporalOd_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="od_77">SS</label>
		</td>
		<td>
		<input id="od_76" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_cb" value="CB" <?php echo ($elem_temporalOd_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="od_76">CB</label>
		</td>
		<td>
		<input id="od_78" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_notVisible" value="Not Visible" <?php echo ($elem_temporalOd_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="od_78" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		<td align="center" class="bilat" onClick="check_bl('Tempo')" rowspan="3">BL</td>
		<td >Temporal</td>
		<td>
		<input id="os_73" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_atm" value="ATM" <?php echo ($elem_temporalOs_atm == "ATM") ? "checked=\"checked\"" : ""; ?>><label for="os_73">ATM</label>
		</td>
		<td>
		<input id="os_74" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_tm" value="TM" <?php echo ($elem_temporalOs_tm == "TM") ? "checked=\"checked\"" : ""; ?>><label for="os_74">TM</label>
		</td>
		<td>
		<input id="os_75" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_ptm" value="PTM" <?php echo ($elem_temporalOs_ptm == "PTM") ? "checked=\"checked\"" : ""; ?>><label for="os_75">PTM</label>
		</td>
		<td>
		<input id="os_77" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_ss" value="SS" <?php echo ($elem_temporalOs_ss == "SS") ? "checked=\"checked\"" : ""; ?>><label for="os_77">SS</label>
		</td>
		<td>
		<input id="os_76" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_cb" value="CB" <?php echo ($elem_temporalOs_cb == "CB") ? "checked=\"checked\"" : ""; ?>><label for="os_76">CB</label>
		</td>
		<td>
		<input id="os_78" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_notVisible" value="Not Visible" <?php echo ($elem_temporalOs_notVisible == "Not Visible") ? "checked=\"checked\"" : ""; ?>><label for="os_78" class="aato">Not Visible</label>
		</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Tempo">
		<td >Pigmentation</td>
		<td>
		<input id="od_79" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_pig_T" value="T" <?php echo ($elem_temporalOd_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_79">T</label>
		</td>
		<td>
		<input id="od_80" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_pig_pos1" value="1+" <?php echo ($elem_temporalOd_pig_pos1 == "+1" || $elem_temporalOd_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_80">1+</label>
		</td>
		<td>
		<input id="od_81" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_pig_pos2" value="2+" <?php echo ($elem_temporalOd_pig_pos2 == "+2" || $elem_temporalOd_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_81">2+</label>
		</td>
		<td>
		<input id="od_82" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_pig_pos3" value="3+" <?php echo ($elem_temporalOd_pig_pos3 == "+3" || $elem_temporalOd_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_82">3+</label>
		</td>
		<td>
		<input id="od_83" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_pig_pos4" value="4+" <?php echo ($elem_temporalOd_pig_pos4 == "+4" || $elem_temporalOd_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_83">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<td >Pigmentation</td>
		<td>
		<input id="os_79" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_pig_T" value="T" <?php echo ($elem_temporalOs_pig_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_79">T</label>
		</td>
		<td>
		<input id="os_80"  type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_pig_pos1" value="1+" <?php echo ($elem_temporalOs_pig_pos1 == "+1" || $elem_temporalOs_pig_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_80">1+</label>
		</td>
		<td>
		<input id="os_81"  type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_pig_pos2" value="2+" <?php echo ($elem_temporalOs_pig_pos2 == "+2" || $elem_temporalOs_pig_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_81">2+</label>
		</td>
		<td>
		<input id="os_82"  type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_pig_pos3" value="3+" <?php echo ($elem_temporalOs_pig_pos3 == "+3" || $elem_temporalOs_pig_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_82">3+</label>
		</td>
		<td>
		<input id="os_83"  type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_pig_pos4" value="4+" <?php echo ($elem_temporalOs_pig_pos4 == "+4" || $elem_temporalOs_pig_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_83">4+</label>
		</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		</tr>

		<tr class="grp_Tempo">
		<td >Iris Convexity</td>
		<td>
		<input id="od_84"  type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_T" value="T" <?php echo ($elem_temporalOd_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="od_84">T</label>
		</td>
		<td>
		<input id="od_85" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_pos1" value="1+" <?php echo ($elem_temporalOd_irisCon_pos1 == "+1" || $elem_temporalOd_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="od_85">1+</label>
		</td>
		<td>
		<input id="od_86" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_pos2" value="2+" <?php echo ($elem_temporalOd_irisCon_pos2 == "+2" || $elem_temporalOd_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="od_86">2+</label>
		</td>
		<td>
		<input id="od_87" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_pos3" value="3+" <?php echo ($elem_temporalOd_irisCon_pos3 == "+3" || $elem_temporalOd_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="od_87">3+</label>
		</td>
		<td>
		<input id="od_88" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_pos4" value="4+" <?php echo ($elem_temporalOd_irisCon_pos4 == "+4" || $elem_temporalOd_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="od_88">4+</label>
		</td>
		<td>
		<input id="od_90" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_flat" value="Flat" <?php echo ($elem_temporalOd_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="od_90">Flat</label>
		</td>
		<td>
		<input id="od_89" type="checkbox"  onclick="checkwnls()" name="elem_temporalOd_irisCon_concave" value="Concave" <?php echo ($elem_temporalOd_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="od_89" class="aato">Concave</label>
		</td>
		<td >Iris Convexity</td>
		<td>
		<input id="os_84" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_T" value="T" <?php echo ($elem_temporalOs_irisCon_T == "T") ? "checked=\"checked\"" : ""; ?>><label for="os_84">T</label>
		</td>
		<td>
		<input id="os_85" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_pos1" value="1+" <?php echo ($elem_temporalOs_irisCon_pos1 == "+1" || $elem_temporalOs_irisCon_pos1 == "1+") ? "checked=\"checked\"" : ""; ?>><label for="os_85">1+</label>
		</td>
		<td>
		<input id="os_86" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_pos2" value="2+" <?php echo ($elem_temporalOs_irisCon_pos2 == "+2" || $elem_temporalOs_irisCon_pos2 == "2+") ? "checked=\"checked\"" : ""; ?>><label for="os_86">2+</label>
		</td>
		<td>
		<input id="os_87" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_pos3" value="3+" <?php echo ($elem_temporalOs_irisCon_pos3 == "+3" || $elem_temporalOs_irisCon_pos3 == "3+") ? "checked=\"checked\"" : ""; ?>><label for="os_87">3+</label>
		</td>
		<td>
		<input id="os_88" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_pos4" value="4+" <?php echo ($elem_temporalOs_irisCon_pos4 == "+4" || $elem_temporalOs_irisCon_pos4 == "4+") ? "checked=\"checked\"" : ""; ?>><label for="os_88">4+</label>
		</td>
		<td>
		<input id="os_90" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_flat" value="Flat" <?php echo ($elem_temporalOs_irisCon_flat == "Flat") ? "checked=\"checked\"" : ""; ?>><label for="os_90">Flat</label>
		</td>
		<td>
		<input id="os_89" type="checkbox"  onclick="checkwnls()" name="elem_temporalOs_irisCon_concave" value="Concave" <?php echo ($elem_temporalOs_irisCon_concave == "Concave") ? "checked=\"checked\"" : ""; ?>><label for="os_89" class="aato">Concave</label>
		</td>
		</tr>

		<tr id="d_adOpt_gonio">
		<td >Comments</td>
		<td colspan="7"><textarea onBlur="checkwnls()"  id="od_91" name="elem_gonioAdOptionsOd" class="form-control"><?php echo $elem_gonioAdOptionsOd;?></textarea></td>
		<td align="center" class="bilat" onClick="check_bl('adOpt_gonio')">BL</td>
		<td >Comments</td>
		<td colspan="7"><textarea onBlur="checkwnls()" id="os_91" name="elem_gonioAdOptionsOs" class="form-control"><?php echo $elem_gonioAdOptionsOs;?></textarea></td>
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
		<td >&nbsp;</td>
		<td align="center" class="bilat">BL</td>
		<td >X</td>
		<td >&nbsp;</td>
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

	<!-- GONIO -->
    </div>
    <div role="tabpanel" class="tab-pane" id="divIop3">
	<!-- Drawing -->
	<div class="examhd form-inline">
		<div class="row">
			<div class="col-sm-1">
				<?php if($finalize_flag == 1){?>
					<label class="chart_status label label-danger pull-left">Finalized</label>
				<?php }?>
			</div>
			<div class="col-sm-11">
				<span id="examFlag" class="glyphicon flagWnl "></span>
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>

				<input type="checkbox" id="elem_noChangeDraw"  name="elem_noChangeDraw" value="1" onClick="setNC2();"
							<?php echo ($elem_noChange_draw == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
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
			<textarea  onblur="checkwnls()" name="od_iop_gon" id="od_iop_gon" class="form-control drw_text_box"  ><?php echo $gonio_od_desc;?></textarea>
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
					if(empty($dbTollImage) == true){
						$dbTollImage = "imgGonioCanvas";
					}

					$stDisp = "hidden";
					if($intTempDrawCount == 0){
						$stDisp = "";
					}
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
					<input type="hidden" name="hidIOPDrawingId<?php echo $intTempDrawCount; ?>" id="hidIOPDrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
					<input type="hidden" name="hidDone<?php echo $intTempDrawCount; ?>" id="hidDone<?php echo $intTempDrawCount; ?>" >
					<input type="hidden" id="hidDrwDataJson<?php echo $intTempDrawCount; ?>" name="hidDrwDataJson<?php echo $intTempDrawCount; ?>" >
					<?php
						if($dbdrawID > 0){ echo "<input type=\"hidden\" name=\"hidLoad".$dbdrawID."\" id=\"hidLoad".$dbdrawID."\" >"; }
						include(dirname(__FILE__)."/drawing_new.php");
					?>
				</div>
			<?php
				}
				//Multi Drawing End
			}
			?>
		</div>
		<div class="col-sm-2">
			<textarea  onblur="checkwnls()" name="os_iop_gon" id="os_iop_gon" class="form-control drw_text_box"  ><?php echo $gonio_os_desc;?></textarea>
		</div>
	</div>
	<div class="clearfix"> </div>
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
		<?php if(!empty($elem_editMode) || !empty($elem_gonioId)){?>
		<button type="button" class="btn btn-success navbar-btn" onclick="purg_exam()">Purge</button>
		<?php }?>
		<?php }?>
		<button type="button" class="btn btn-danger navbar-btn pull-right" onclick="cancel_exam()">Cancel</button>

	</div>
</nav>
</form>

<?php

//include(dirname(__FILE__)."/iop_graphs.php");
?>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>

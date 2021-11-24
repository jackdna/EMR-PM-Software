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
	#imgAblat,#imgNomo{position:fixed; top:10px;left:230px; display:none;cursor:move;z-index:2;}
	#imgAblat{left:660px;top:60px;}
	#imgAblat div, #imgNomo div{float:right;background-color:white;color:red;font-weight:bold;padding:4px;cursor:hand;border:1px solid black;}	
</style>
<!--<link href="<?php echo $GLOBALS['webroot'];?>/library/css/drawing.css" rel="stylesheet" type="text/css">-->
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
	var rootdir = "<?php echo $GLOBALS['rootdir'];?>";
	var finalize_flag = "<?php echo $finalize_flag;?>";
	var isReviewable = "<?php echo $isReviewable;?>";
	var myflag = "<?php echo ($myflag)?1:0;?>";
	var ProClr=<?php echo $ProClr;?>;
	var logged_user_type = "<?php echo $logged_user_type;?>";	
	var examName = "Refractive Surgery";
	var arrSubExams = new Array("RefSurg");	
	var pachy = <?php echo json_encode(array("".$cor_od,"".$cor_os));?>;
	var arrAblat= <?php echo json_encode($arrAblat); ?>;

//--
function recalculateValues(){
	if(typeof(top.opener.getMRinfo_SC)!="undefined"){
		var ar = top.opener.getMRinfo_SC();
		//
		var url = zPath+"/chart_notes/requestHandler.php";
		var params="elem_formAction=recalculateRefSurg";
			params+="&sod="+ar["sod"]+"&sos="+ar["sos"]+"&cod="+ar["cod"]+"&cos="+ar["cos"]+"&aod="+ar["aod"]+"&aos="+ar["aos"];
			params+="&elem_fid="+$("input[name=elem_formId]").val();			
		if(typeof(setProcessImg)=="function")setProcessImg("1","d_sphrequi_od");
		$.post(url, params,
			function(data){
			//stop
			if(typeof(setProcessImg)=="function")setProcessImg(0,"d_sphrequi_od");			
			if(data){
				
				$("input[name=elem_mr_sOd]").val(""+ar["sod"]);
				$("input[name=elem_mr_cOd]").val(""+ar["cod"]);
				$("input[name=elem_mr_aOd]").val(""+ar["aod"]);
				$("input[name=elem_corrOd]").val(""+data.elem_corrOd);
				$("input[name=elem_mr_sOs]").val(""+ar["sos"]);
				$("input[name=elem_mr_cOs]").val(""+ar["cos"]);
				$("input[name=elem_mr_aOs]").val(""+ar["aos"]);
				$("input[name=elem_corrOs]").val(""+data.elem_corrOs);
				pachy = [""+data.elem_corrOd,""+data.elem_corrOs];
				
				var tmp = ""+ar["cod"];
				if(tmp.length>6)tmp=""+tmp.substr(0,4)+"";
				$("#sp_c_od").html("C='"+tmp+"'").attr("title",ar["cod"]);
				
				var tmp = ""+ar["aod"];
				if(tmp.length>3)tmp=""+tmp.substr(0,3)+"";
				$("#sp_a_od").html("A='"+tmp+"'").attr("title",ar["aod"]);
				
				var tmp = ""+ar["cos"];
				if(tmp.length>6)tmp=""+tmp.substr(0,4)+"";
				$("#sp_c_os").html("C='"+tmp+"'").attr("title",ar["cos"]);
				
				var tmp = ""+ar["aos"];
				if(tmp.length>3)tmp=""+tmp.substr(0,3)+"";
				$("#sp_a_os").html("A='"+tmp+"'").attr("title",ar["aos"]);
				
				if(!data.elem_corrOd){	data.elem_corrOd="";	}
				if(!data.elem_corrOs){	data.elem_corrOs="";	}
				
				$("#sp_corOd").html("Pachy='"+data.elem_corrOd+"'");
				$("#sp_corOs").html("Pachy='"+data.elem_corrOs+"'");			
			
				$("input[name=elem_sphericalEqOd]").val(""+data.elem_sphericalEqOd);
				$("input[name=elem_sphericalEqOs]").val(""+data.elem_sphericalEqOs);
				$("input[name=elem_adjstpppOd]").val(""+data.elem_adjstpppOd);
				$("input[name=elem_adjstpppOs]").val(""+data.elem_adjstpppOs);
				$("input[name=elem_laserEntryOd]").val(""+data.elem_laserEntryOd);
				$("input[name=elem_laserEntryOs]").val(""+data.elem_laserEntryOs);
				calcPhyPAdj('d');calcRSB('d');
				calcPhyPAdj('s');calcRSB('s');
				calcPhyDiop('d');calcPhyDiop('s');
				//$("input[name=elem_laserEnPhypadjstOd]").val(""+data.elem_laserEnPhypadjstOd).trigger("click");
				//$("input[name=elem_laserEnPhypadjstOs]").val(""+data.elem_laserEnPhypadjstOs).trigger("click");
			}
		},"json");
	}
}

function setRefSxMode(){
	var str=$(":checked[name=elem_mode_refsx]").val();
	$(".ecustom,.etrad").attr("readonly",false).css("background-color","white");
	if(str=="Traditional"){
		$(".ecustom").attr("readonly",true).css("background-color","#edebeb");		
	}else if(str=="Custom"){
		$(".etrad").attr("readonly",true).css("background-color","#edebeb");
	}
	
	$("input[name=elem_abDepthOd],input[name=elem_abDepthOs]").attr("readonly",true);
}

function checkRSB(obj){
	obj.style.color= (obj.value<250) ? "red" : "black";	
}

function calcRSB(s){
	var a=b=c=d=e=se=f=0;
	if(s=="d"){
		a="elem_capthickOd";
		b="elem_ablationOd";
		c="elem_RSBOd";
		se="elem_sphericalEqOd";
		e=pachy[0];
		f="elem_abDepthOd";		
	}else{
		a="elem_capthickOs";
		b="elem_ablationOs";
		c="elem_RSBOs";
		se="elem_sphericalEqOs";
		e=pachy[1];
		f="elem_abDepthOs";
	}
	
	a=$("textarea[name="+a+"]").val();
	b=$("select[name="+b+"]").val();
	se=$("input[name="+se+"]").val();
	if(isNaN(a)||a=="")a=0;
	if(isNaN(b)||b=="")b=0;
	if(isNaN(e)||e=="")e=0;
	if(isNaN(se)||se=="")se=0;
	
	//
	var ab=0;
	se = Math.abs(se);
	if(se>=1 && se<=12 && b>=5 && b<=7){
		var y,mn,mx;
		var ar2 = arrAblat[b];
		
		//deci
		var dec = Math.round((se % 1)*100)/100;
		for(var x in ar2){
			y=Math.abs(x);
			
			if(y==se){
				ab = Math.abs(ar2[x]);
				break;
			}else if(y<se){
				mn= Math.abs(ar2[x]);
			}else if(y>se){
				mx= Math.abs(ar2[x]);
				
				if(dec==0.50){
					//mean
					ab=(parseFloat(mn)+parseFloat(mx))/2;
					break;
				}else if(dec<0.50){
					ab=mn;
					break;
				}else if(dec>0.50){
					ab=mx;
					break;
				}
			}
		}
	}
	
	//a
	$("input[name="+f+"]").val(""+ab+"");
	
	d=parseFloat(e)-(parseFloat(a)+parseFloat(ab));
	//d=(parseFloat(a)+parseFloat(ab))-parseFloat(e);
	if(d==0){d="";}else{d+="u";}
	$("input[name="+c+"]").val(d).triggerHandler("change");
}

function calcPhyPAdj(s){
	var a=b=c=t=0;
	if(s=='d'){
		a="elem_phypadjstOd";
		b="elem_sphericalEqOd";
		c="elem_laserEnPhypadjstOd";
	}else{
		a="elem_phypadjstOs";
		b="elem_sphericalEqOs";
		c="elem_laserEnPhypadjstOs";
	}
	a=$("input[name="+a+"]").val();
	b=$("input[name="+b+"]").val();
	if(isNaN(a)||a==""||isNaN(b)||b==""){a=0;b=0;}	
	t=(b*(100-a))/100;
	if(t==0){t="";}else{t=(Math.round(t*100)/100).toFixed(2);}
	$("input[name="+c+"]").val(""+t);
}

function calcPhyDiop(s){
	var a=b=c=t=0;
	if(s=='d'){
		a="elem_phydioadjstOd";
		b="elem_laserEntryOd";
		c="elem_laserEnPhydioadjstOd";
	}else{
		a="elem_phydioadjstOs";
		b="elem_laserEntryOs";
		c="elem_laserEnPhydioadjstOs";
	}
	a=$("input[name="+a+"]").val();
	b=$("input[name="+b+"]").val();
	if(isNaN(a)||a==""){a=0;b=0;}
	if(isNaN(b)||b=="")b=0;
	t=parseFloat(a)+parseFloat(b);
	if(t==0){t="";}else{t=(Math.round(t*100)/100).toFixed(2); }
	$("input[name="+c+"]").val(""+t);
}	
    </script>
</head>
<body class="exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->

<form name="frmRefSurg" id="frmRefSurg" action="saveCharts.php" method="post" onSubmit="freezeElemAll('0')" enctype="multipart/form-data">
<input type="hidden" name="elem_saveForm" value="ref_surg">
<input type="hidden" name="elem_refSurgId" value="<?php echo $elem_refSurgId;?>">
<input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
<input type="hidden" name="elem_patientId" value="<?php echo $patient_id;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
<input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
<!--<input type="hidden" name="elem_notApplicable" value="<?php echo $elem_notApplicable;?>">--><!-- Not Required -->
<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">

<input type="hidden" name="elem_wnlRefSurgOd" value="<?php echo $elem_wnlRefSurgOd;?>">
<input type="hidden" name="elem_wnlRefSurgOs" value="<?php echo $elem_wnlRefSurgOs;?>">
<input type="hidden" name="elem_descRefSurg" value="<?php echo $elem_descRefSurg;?>">

<input type="hidden" name="elem_wnlRefSurg" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="elem_posRefSurg" value="<?php echo $elem_isPositive;?>">
<input type="hidden" name="elem_ncRefSurg" value="<?php echo $elem_noChange;?>">
<input type="hidden" name="elem_examined_no_change" value="<?php echo $elem_noChange;?>">

<input type="hidden" name="elem_mr_sOd" value="<?php echo $elem_mr_sOd;?>">
<input type="hidden" name="elem_mr_cOd" value="<?php echo $elem_mr_cOd;?>">
<input type="hidden" name="elem_mr_aOd" value="<?php echo $elem_mr_aOd;?>">
<input type="hidden" name="elem_corrOd" value="<?php echo $elem_corrOd;?>">

<input type="hidden" name="elem_mr_sOs" value="<?php echo $elem_mr_sOs;?>">
<input type="hidden" name="elem_mr_cOs" value="<?php echo $elem_mr_cOs;?>">
<input type="hidden" name="elem_mr_aOs" value="<?php echo $elem_mr_aOs;?>">
<input type="hidden" name="elem_corrOs" value="<?php echo $elem_corrOs;?>">

<!-- newET_changeIndctr -->
<input type="hidden" name="elem_chng_div1_Od" id="elem_chng_div1_Od" value="<?php echo $elem_chng_div1_Od;?>">
<input type="hidden" name="elem_chng_div1_Os" id="elem_chng_div1_Os" value="<?php echo $elem_chng_div1_Os;?>">
<input type="hidden" id="el_strPtrnGray" name="el_strPtrnGray" value="<?php echo $strPtrnGray;?>">
<!-- newET_changeIndctr -->
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
	$tmp2=$key;
	$tmp = ($key == "RefSurg") ? "active" : "";
	?>
	<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab" onclick="changeTab('<?php echo $key;?>')" id="tab<?php echo $key;?>" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>    
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="div1">
	<div class="examhd ">
		<div class="row">
			<div class="col-sm-1 pharmo mt5" >
				<?php if($finalize_flag == 1){?>
					<label class="chart_status label label-danger pull-left">Finalized</label>
				<?php }?>
			</div>	
			<div class="col-sm-5 text-center" >	
				<label id="ptName" ><?php echo $ptName;?></label>
			</div>
			<div class="col-sm-4 pharmo form-inline" >
				<div class="radio">
				<input type="radio" id="elem_mode_refsx_t" name="elem_mode_refsx" value="Traditional" onclick="setRefSxMode()" <?php if($elem_mode_refsx=="Traditional") echo "checked";?> >
				<label for="elem_mode_refsx_t">Traditional</label>
				</div>
				<div class="radio">				
				<input type="radio" id="elem_mode_refsx_c" name="elem_mode_refsx" value="Custom" onclick="setRefSxMode()" <?php if($elem_mode_refsx=="Custom") echo "checked";?>> 
				<label for="elem_mode_refsx_c">Custom</label>
				</div>
				<button id="btnNomo" class="btn btn-default" type="button">Nomogram</button> 
				<button id="btnAblat" class="btn btn-default" type="button">Ablation Table</button>				
			</div>	
			<div class="col-sm-2">	
				<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();" 
							<?php echo ($elem_noChange == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
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
	<td colspan="8" align="center"><!--<img src="../../library/images/tstod.png" alt=""/>--><div class="checkboxO"><label class="od cbold">OD</label></div></td>
	<td align="center" class="bilat bilat_all" onclick="check_bilateral()"><strong>Bilateral</strong></td>
	<td colspan="8" align="center"><!--<img src="../../library/images/tstos.png" alt=""/>--><div class="checkboxO"><label class="os cbold">OS</label></div></td>
	</tr>
	<tr id="d_trgtref">
	<td  align="left">Target Refraction</td>
	<td colspan="7" align="left"><textarea name="elem_trgtRefOd" class="etrad form-control"><?php echo ($elem_trgtRefOd); ?></textarea></td>
	<td align="center" class="bilat" onclick="check_bl('trgtref')">BL</td>
	<td  align="left">Target Refraction</td>
	<td colspan="7" align="left"><textarea name="elem_trgtRefOs" class="etrad form-control"><?php echo ($elem_trgtRefOs); ?></textarea></td>
	</tr>
	<tr id="d_sphrequi">
	<td align="left">Spherical equivalent</td>
	<td colspan="7" align="left"><input name="elem_sphericalEqOd" value="<?php echo ($elem_sphericalEqOd); ?>" class="etrad" onchange="calcPhyPAdj('d');calcRSB('d');" class="form-control" placeholder="Text input"></td>	
	<td align="center" class="bilat" onclick="check_bl('sphrequi')">BL</td>
	<td align="left">Spherical equivalent</td>
	<td colspan="7" align="left"><input name="elem_sphericalEqOs" value="<?php echo ($elem_sphericalEqOs); ?>" class="etrad" onchange="calcPhyPAdj('s');calcRSB('s');" class="form-control" placeholder="Text input"></td>	
	</tr>
	<tr id="d_adjstppp">
	<td align="left">Adjustment % per Nomo</td>
	<td align="left"><input type="text" name="elem_adjstpppOd" value="<?php echo ($elem_adjstpppOd); ?>" size="8" class="etrad form-control" placeholder="Text input"></td>
	<td  align="left"  >Phy % Adj</td>
	<td  align="left" ><input type="text" name="elem_phypadjstOd" value="<?php echo ($elem_phypadjstOd); ?>" size="8" onchange="calcPhyPAdj('d')" class="etrad form-control" placeholder="Text input"></td>
	<td  align="left" colspan="3">Phy Diopter Adj</td>
	<td align="left"><input type="text" name="elem_phydioadjstOd" value="<?php echo ($elem_phydioadjstOd); ?>" size="8" onchange="calcPhyDiop('d')" class="etrad form-control" placeholder="Text input"></td>
	<td align="center" class="bilat" onclick="check_bl('adjstppp')">BL</td>
	<td align="left">Adjustment % per Nomo</td>
	<td  align="left"><input type="text" name="elem_adjstpppOs" value="<?php echo ($elem_adjstpppOs); ?>"  size="8" class="etrad form-control" placeholder="Text input"></td>
	<td  align="left" >Phy % Adj</td>
	<td  align="left" ><input type="text" name="elem_phypadjstOs" value="<?php echo ($elem_phypadjstOs); ?>" size="8" onchange="calcPhyPAdj('s')" class=" etrad form-control" placeholder="Text input"></td>
	<td  align="left" colspan="3">Phy Diopter Adj</td>
	<td align="left"><input type="text" name="elem_phydioadjstOs" value="<?php echo ($elem_phydioadjstOs); ?>" size="8" onchange="calcPhyDiop('s')" class="etrad form-control" placeholder="Text input"></td>
	</tr>
	<tr id="d_capdia">
	<td align="left">Cap Diameter</td>
	<td colspan="7" align="left"><textarea name="elem_capDiaOd" rows="3" class="etrad form-control"><?php echo ($elem_capDiaOd); ?></textarea></td>
	<td align="center" class="bilat" onclick="check_bl('capdia')">BL</td>
	<td align="left">Cap Diameter</td>
	<td colspan="7" align="left"><textarea name="elem_capDiaOs" rows="3" class="etrad form-control"><?php echo ($elem_capDiaOs); ?></textarea></td>
	</tr>
	<tr id="d_capthick">
	<td align="left">Cap Thickness</td>
	<td colspan="7" align="left"><textarea name="elem_capthickOd" onchange="calcRSB('d')" rows="3" class="etrad form-control"><?php echo ($elem_capthickOd); ?></textarea></td>
	<td align="center" class="bilat" onclick="check_bl('capthick')">BL</td>
	<td align="left">Cap Thickness</td>
	<td colspan="7" align="left"><textarea name="elem_capthickOs" onchange="calcRSB('s')" rows="3" class="etrad form-control"><?php echo ($elem_capthickOs); ?></textarea></td>
	</tr>
	<tr id="d_ablation">
	<td align="left">Residual Stromal Bed</td>
	<td  align="left"><input type="text" name="elem_RSBOd" value="<?php echo ($elem_RSBOd); ?>" onchange="checkRSB(this)" size="8" class="etrad form-control" placeholder="Text input"></td>
	<td  align="left">Ablation Zone</td>
	<td  align="left"><select name="elem_ablationOd" class="etrad form-control " onchange="calcRSB('d')" >
	<option value=""></option>
	<option value="5" <?php if($elem_ablationOd=="5") echo "SELECTED";?> >5 mm</option>
	<option value="6" <?php if($elem_ablationOd=="6") echo "SELECTED";?> >6 mm</option>
	<option value="7" <?php if($elem_ablationOd=="7") echo "SELECTED";?> >7 mm</option>
	</select></td>
	<td align="left">Ablation depth </td>
	<td align="left"><input type="text" class="etrad form-control" name="elem_abDepthOd" value="<?php echo $elem_abDepthOd;?>" size="5" readonly /></td>
	<td align="left" colspan="2"><?php echo $strcor_od;?></td>	
	<td align="center" class="bilat" onclick="check_bl('ablation')">BL</td>
	<td align="left">Residual Stromal Bed</td>
	<td  align="left"><input type="text" name="elem_RSBOs" value="<?php echo ($elem_RSBOs); ?>" onchange="checkRSB(this)" size="8" class="etrad form-control" placeholder="Text input"></td>
	<td  align="left">Ablation Zone</td>
	<td  align="left"><select name="elem_ablationOs" class="etrad form-control " onchange="calcRSB('s')" >
	<option value=""></option>
	<option value="5" <?php if($elem_ablationOs=="5") echo "SELECTED";?> >5 mm</option>
	<option value="6" <?php if($elem_ablationOs=="6") echo "SELECTED";?> >6 mm</option>
	<option value="7" <?php if($elem_ablationOs=="7") echo "SELECTED";?> >7 mm</option>
	</select></td>
	<td align="left">Ablation depth </td>
	<td align="left"><input type="text" name="elem_abDepthOs" value="<?php echo $elem_abDepthOs;?>" size="5" readonly class="etrad form-control" placeholder="Text input"></td>
	<td align="left" colspan="2"><?php echo $strcor_os;?></td>	
	</tr>
	<tr id="d_laserEntry">
	<td align="left">Laser Entery</td>
	<td align="left"><input type="text" name="elem_laserEntryOd" value="<?php echo ($elem_laserEntryOd); ?>" size="7" onchange="calcPhyDiop('d')" class="etrad form-control" placeholder="Text input"></td>
	<td align="left">Phy % Adj</td>
	<td align="left"><input type="text" name="elem_laserEnPhypadjstOd" value="<?php echo ($elem_laserEnPhypadjstOd); ?>" size="7" class="etrad form-control" placeholder="Text input"></td>
	<td align="left">Phy Diopter Adj </td>
	<td align="left"><input type="text" name="elem_laserEnPhydioadjstOd" value="<?php echo ($elem_laserEnPhydioadjstOd); ?>" size="7" class="etrad form-control" placeholder="Text input"></td>
	<td align="left" colspan="2"><?php echo $sp_c_od." ".$sp_a_od; ?></td>	
	<td align="center" class="bilat" onclick="check_bl('laserEntry')">BL</td>
	<td align="left">Laser Entery</td>
	<td align="left"><input type="text" name="elem_laserEntryOs" value="<?php echo ($elem_laserEntryOs); ?>" size="7" onchange="calcPhyDiop('s')" class="etrad form-control" placeholder="Text input"></td>
	<td align="left">Phy % Adj</td>
	<td align="left"><input type="text" name="elem_laserEnPhypadjstOs" value="<?php echo ($elem_laserEnPhypadjstOs); ?>" size="7" class="etrad form-control" placeholder="Text input"></td>
	<td align="left">Phy Diopter Adj </td>
	<td align="left"><input type="text" name="elem_laserEnPhydioadjstOs" value="<?php echo ($elem_laserEnPhydioadjstOs); ?>" size="7" class="etrad form-control" placeholder="Text input"></td>
	<td align="left" colspan="2"><?php echo $sp_c_os." ".$sp_a_os; ?></td>	
	</tr>
	<tr id="d_CLE">
	<td align="left">Custom Laser Entry</td>
	<td colspan="7" align="left"><textarea name="elem_CLEOd" rows="3" class="ecustom form-control"><?php echo ($elem_CLEOd); ?></textarea></td>
	<td align="center" class="bilat" onclick="check_bl('CLE')">BL</td>
	<td align="left">Custom Laser Entry</td>
	<td colspan="7" align="left"><textarea name="elem_CLEOs" rows="3" class="ecustom form-control"><?php echo ($elem_CLEOs); ?></textarea></td>
	</tr>
	<tr id="d_adOpt_RefSx">
	<td align="left">Comments</td>
	<td colspan="7" align="left"><textarea name="elem_refSxAdvanceOptionOd" rows="3" class="form-control"><?php echo ($elem_refSxAdvanceOptionOd); ?></textarea></td>
	<td align="center" class="bilat" onclick="check_bl('adOpt_RefSx')">BL</td>
	<td align="left">Comments</td>
	<td colspan="7" align="left"><textarea name="elem_refSxAdvanceOptionOs" rows="3" class="form-control"><?php echo ($elem_refSxAdvanceOptionOs); ?></textarea></td>
	</tr>
	</table>
	</div>
	<div class="clearfix"> </div>
    </div>
  </div>
</div>
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
		<input type="button"  class="btn btn-success navbar-btn"  value="Recalculate" id="btnRecal" onClick="recalculateValues()" />
		<?php if(!empty($elem_editMode)){?>
		<button type="button" class="btn btn-success navbar-btn" onclick="purg_exam()">Purge</button>
		<?php }?>
		<?php }?>	
		
		<button type="button" class="btn btn-danger navbar-btn pull-right" onclick="cancel_exam()">Cancel</button>
		
	</div>
</nav>
</form>

<!--Nomogram-->
<div id="imgNomo" >
<div onclick="$('#btnNomo').click();">Close</div>
<img src="<?php echo $GLOBALS['webroot']; ?>/library/images/Nomogram.jpg" alt="Nomograph" width="428" height="570" />
</div>
<div id="imgAblat" >
<div onclick="$('#btnAblat').click();">Close</div>
<img  src="<?php echo $GLOBALS['webroot']; ?>/library/images/ablation.png" alt="Ablation" width="499" height="420" />
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>
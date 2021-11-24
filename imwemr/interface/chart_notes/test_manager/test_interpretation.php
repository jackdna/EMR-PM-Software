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

require_once("../../../config/globals.php");
//To check pt logged in or not
require_once("../../../library/patient_must_loaded.php");
$library_path = $GLOBALS['webroot'].'/library';

$session_patient = $_SESSION['patient'];
$pt_name_arr = core_get_patient_name($session_patient);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Test Manager</title>
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE10">
  <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
	<!-- Bootstrap -->
  <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
  <!-- Bootstrap Selctpicker CSS -->
  <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
  <!-- Messi Plugin for fancy alerts CSS -->
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
  <!-- DateTime Picker CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/colorbox.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.iviewer.css"/>
  
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]--> 
  
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
  <script src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js" type="text/javascript" ></script>
  <!-- Bootstrap -->
  <script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
  <script src="<?php echo $library_path; ?>/js/buttons.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/js/core_main.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/js/work_view/work_view.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/js/jquery.colorbox-min.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/js/jquery.iviewer.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		var oPO 	= window.opener.oPO;
		var oPUF 	= window.opener.oPUF;
		var oPF 	= window.opener.top.fmain.oPF;	
		var zPath 	= "<?php echo $GLOBALS['rootdir'];?>";
		var SelectedTestImages 	= window.opener.SelectedTestImages;
		var ArrScanHref			= window.opener.ArrScanHref;
		var ArrTestWiseScanHref	= window.opener.ArrTestWiseScanHref;
		var ArrTestSeq			= window.opener.ArrTestSeq;
		var firstTestName 		= ArrTestSeq[0];
		var ArrFirstTestImages = ArrTestWiseScanHref[firstTestName];
		//var fmain  = new Object();
		var arr_opened_popups = window.opener.window.opener.top.arr_opened_popups;
		//alert(ArrFirstTestImages + "\n\n" + ArrFirstTestImages.length);	
		window.focus;
		$ = jQuery;
		$(document).ready(function(e) {
    	Messi.prototype.options.center=false;
			Messi.prototype.options.viewport= {top: '30%', left: '10%'};		
    });
		
		function ShowSelected(){
			if(typeof(m)=='undefined') m=0;
			var hrefArr = new Array();
			var testInterArray = new Array();
			i		= 0;
			ht 		= '';
			hgt 	= $('#page_body').height();
			wdt 	= $('#page_body').width();
			for(id in SelectedTestImages){
				row = SelectedTestImages[id];
				tn = row['testName'];
				tid = row['testId'];
				scan_id = 'a'+row['scanId'];
				href_url = ArrScanHref[scan_id];
				hrefArr[i] = href_url;
				i++;
				if($.isArray(testInterArray[tid+'_aabb_'+tn])){
					testInterArray[tid+'_aabb_'+tn].push(href_url);
				}else{
					testInterArray[tid+'_aabb_'+tn] = Array(href_url);
				}
			}
			if(i==0){top.fAlert('Test image not selected.');return false;}
			for(k=0;k<hrefArr.length;k++){
				id = k+1;
				ht += '<div id="img_con_'+id+'" '+((k>0)?'style="margin-top:20px;"':'')+'></div>';
			}
			$('#page_body').html(ht);
			L = 1;
			numoftests = 0
			for(xx in testInterArray){
				numoftests++;
			}
			for(yy in testInterArray){
				yy_arr = yy.split('_aabb_');//tid+'_aabb_'+tn
				tid = yy_arr[0];
				tn  = yy_arr[1];
				openTest(tn,tid,1,L,testInterArray,numoftests);
				L++;
			}
		}
    
		function initDisp(e){
			h = window.innerHeight;
			var footerBtnH = parseInt($("#footer_div").outerHeight(true))+12;
			var headerRowH = parseInt($("#header_row").outerHeight(true))+12;
			hh 	= (h-(footerBtnH + headerRowH));
		//	alert('h='+h+', hh='+hh+', footerBtnH='+footerBtnH+', headerRowH='+headerRowH);
			$('#page_body').height(hh).css({'overflow': 'hidden','overflow-y': 'scroll'});
		}
		
		function openTest(tn,tid,m,L,testInterArray,numoftests){
			noP = 1;
			var_scroll = 'yes';
			new_w = 1000;
			switch(tn)
			{
				case "A/Scan":
				case "Ascan":
				case "AScan":
					url = "../../tests/ascan.php?pop=1";w = "1140";h = "775",n="docAS"; noP = 0; var_scroll = 'yes';
					new_w = 1120;
					break;
				case "Bscan":
				case "B-Scan":
				case "BScan":
					url = "../../tests/test_bscan.php?pop=1";w = "720";h = "670",n="docbscan";
					break;
				case "Cell Count":
				case "CellCount":
					url = "../../tests/test_cellcount.php?pop=1";w = "720";h = "670",n="doccellcnt";
					break;
				case "Topography":
				case "Topogrphy":
					url = "../../tests/test_topography.php?pop=1";w = "755";h = "720",n="docTopo";
				break;
				case "IVFA":
					url = "../../tests/test_ivfa.php?pop=1";w = "755";h = "720",n="docIvfa";
				break;
				case "HRT":
				case "NFA":
					url = "../../tests/test_nfa.php?pop=1";w = "750";h = "750",n="docNfa";
				break;
				case "ICG":
					url = "../../tests/test_icg.php?pop=1";w = "750";h = "670",n="docIcg";
					break;
				case "IOL Master":
				case "IOL_Master":
					url = "../../tests/iol_master.php?pop=1";w = "1140";h = "775",n="docIOL_Master"; 
					var_scroll = 'yes'; noP = 0;
					new_w = 1120;
					break;
				case "Other":
						url = "../../tests/test_other.php?pop=1";w = "720";h = "670",n="docOthr";
						break;
				case "test_labs":
				case "Laboratories":
						url = "../../tests/test_labs.php?pop=1";w = "720";h = "670",n="docLabs";
						break;
				case "Pachy":
				case "Pacchy":
					url = "../../tests/test_pacchy.php?pop=1";w = "755";h = "720",n="docPachy";
				break;
				case "VF":
					url = "../../tests/test_vf.php?pop=1";w = "755";h = "720",n="docVF";
				break;
				case "VF-GL":
					url = "../../tests/test_vf_gl.php?pop=1";w = "755";h = "720",n="docVFGL";
				break;
				case "OCT":
					url = "../../tests/test_oct.php?pop=1";w = "755";h = "720",n="docOCT";
				break;
				case "OCT-RNFL":
					url = "../../tests/test_oct_rnfl.php?pop=1";w = "830";h = "720",n="docOCTRNFL";
					str=n="OCTRNFL";
				break;
				case "GDX":
					url = "../../tests/test_gdx.php?pop=1";w = "755";h = "720",n="docGDX";
				break;
				case "External":
				case "ExternalAnterior":
					url = "../../tests/test_external.php?pop=1";w = "890";h = "72",n="docExAnt";
				break;
				case "Disc":
				case "Fundus":
					url = "../../tests/test_disc.php?pop=1";w = "755";h = "720",n="docDisc";
				break;
				case "TemplateTests":
					url = "../../tests/test_template.php?pop=1";w = "720";h = "670",n="docOthr";
					new_w = 1000;
				break;
				default: //test_template_custom_patient.php?pop=1&tId=15
					url = "../../tests/test_template_custom_patient.php?pop=1";w = "720";h = "670",n="docOthr";
					new_w = 1000;
					break;
			}
			h = parseInt(h); //Getting test height in Integer;
			h=$('html').innerHeight()-150; //Ignoring above setting and giving Available screen height to Tests.
			//w = parseInt(w);
			//if(numoftests>1) w = 755; //Comment out this line of Horizontal scrolling not required on Tests Interpretations.
			//================in case of Safari and Mac===================//
			if(parseInt(navigator.appVersion)==534 || (parseInt(navigator.appVersion)==537)){
				h=parseInt($(window).height()-160);
			}
			//=========================================================================//
			//w = 1120;
			w = new_w;
			url += "&tId="+tid+'&doNotShowRightSide=yes&noP='+noP;
			htmvar = '<table class="table_collapse bg1" border=0><tr><td class="valignTop" style="width:'+w+'px; height:'+h+'px;"><iframe style="width:'+w+'px; height:'+h+'px;" src="'+url+'" scrolling="'+var_scroll+'" frameborder=0></iframe></td><td class="valignTop" style="width:auto;" id="imageNTestInterImgBox'+L+'">&nbsp;</td></tr></table>';
			$("#img_con_"+L).html(htmvar);
			//a=window.open();a.document.write(htmvar);
			var imgArr = testInterArray[tid+'_aabb_'+tn];
			var imgTags = '';
			scrollMargin = 5;
			if(numoftests>1) scrollMargin = 20;
			ww = $('#page_body').width()-w-scrollMargin;
			cnt_images = imgArr.length;
			for(i=0;i<cnt_images;i++){
				href = imgArr[i];
				file_ext = href.substr(href.length-3);
				if(file_ext.toLowerCase()=='pdf'){
					pdf_iframe_height = parseInt(h);
					if(cnt_images>1){
						pdf_iframe_height = parseInt(h) / 2;
					}
					imgTags += '<iframe src="'+href+'" style="width:'+ww+'px; height:'+pdf_iframe_height+'px;"></iframe><br>';
				}else{
					imgTags += '<div id="iv'+(i+1)+'" class="col-xs-12 mb10 iviewer"><img src="'+href+'" style="width:'+(ww-10)+'px; max-height:'+(h-20)+'px;" onclick="showIviewInColorBox(this);"></div>';
				}
			}
			imgTags = '<div style="height:'+h+'px;width:'+ww+'px;overflow:auto; overflow-x:hidden;">'+imgTags+'</div>';
			$('#imageNTestInterImgBox'+L).html(imgTags);
			setIviewerImgHeight();
		}
	
		function setIviewerImgHeight(){return;
			$('div.iviewer,td.iviewer').each(function(){
				conH = $(this).height();
				conW = $(this).width();//alert(conH+' :: '+conW);
				$(this).find('img').css({'max-height':conH-25,'max-width':conW-20});
			});
		}
		
		function showIviewInColorBox(o){
			con = $(o).parent('div,td');
			src = $(o).attr('src');
			con.css({height:$(o).height(),width:$(o).width()});
			con.html('');
			var iv11 = con.iviewer({
					 src: src,
					 update_on_resize: false,
					 zoom_animation: true,
					 mousewheel: true,
					 onMouseMove: function(ev, coords) { },
					 onStartDrag: function(ev, coords) { }, //return false; this image will not be dragged
					 onDrag: function(ev, coords) { }
			});
		}
		
		window.onresize = initDisp;
		window.onload = function(){
			//popup_resize(screen.availWidth,screen.availHeight,0.98);
			initDisp();
			ShowSelected();
		}
	</script>
</head>
<body>
	<div class="container-fluid">
   	<div class="mainwhtbox" style="padding-left:10px; padding-right:10px;">
    	<div class="row">		
      	<div class="col-sm-12 purple_bar" id="header_row">
        	
          <div class="col-sm-6">
          	<label>Eye Test Manager - Test Interpretation</label>
					</div>	
					<div class="col-sm-4 text-left">
						<label><?php echo $pt_name_arr['2'].', '.$pt_name_arr['1'].' '.substr($pt_name_arr['3'],0,1).' - '.$pt_name_arr['0'];?></label>
					</div>	
				
        </div>
				
        <div id="page_body" class="col-sm-12 pt5"></div>
        
			</div>	
  	</div>
    
    <div class="mainwhtbox" style="padding-left:10px; padding-right:10px;" id="footer_div">
    	<div class="row">
      	<div class="col-sm-11 ad_modal_footer text-center" id="page_buttons"></div>
       	<div class="col-sm-1 ad_modal_footer text-center" id="module_buttons">
        	<input type="button" class="btn btn-danger" value="Close" onClick="window.close()">	
       	</div>
      </div>
   	</div>   
      
	</div>
</body>
</html>
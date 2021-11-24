<?php require_once('../globals.php'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Version Release Document</title>
		<link rel="stylesheet" type="text/css" href="../../library/css/imonitor_custom.css">
		<style type="text/css">
			.div_popup{ position:absolute; display:none; z-index:1000; background-color:#CCCCCC; width:auto; overflow:auto; }
			.div_popup_heading{ background:#FFCC66 url(../../library/images/popup_heading.gif) repeat-x; height:15px; padding:5px 5px; border-bottom:1px solid #E6B24A;}
			.section{border-top:1px solid #C5CFB9; border-left:1px solid #C5CFB9; border-right:1px solid #B4C1A5; border-bottom:1px solid #B4C1A5; background-color:#F4F9EE;}
			.subheading{background-color:#93B9DC; font-weight:bold; border-top:1px solid #cccccc; border-bottom:1px solid #dedede; padding:1px 5px; font-size:12px;}
			.closeBtn{display:block; float:right; height:16px; width:16px; background:transparent url(../../library/images/delete.gif) center no-repeat; cursor:pointer;}
			.a_clr1{ color:#9900CC; font-weight:bold; font-size:12px; }
			.m2{margin:2px;} .m5{margin:5px;} .m10{margin:10px;} /*--ALL SIDE MARGINS--*/
			.mt15{margin-top:15px;}.mt10{margin-top:10px;} .mt7{margin-top:7px;} .mt5{margin-top:5px;} .mt2{margin-top:2px;} .mt4{margin-top:4px;} .mt3{margin-top:3px;} /*--TOP MARGINS--*/
			.mr20{margin-right:20px;} .mr10{margin-right:10px;} .mr5{margin-right:5px;} .mr3{margin-right:3px;} .mr2{margin-right:2px;} /*--RIGHT MARGINS--*/
			.ml20{margin-left:20px;} .ml10{margin-left:10px;} .ml5{margin-left:5px;} .ml3{margin-left:3px;} .ml2{margin-left:2px;} /*--LEFT MARGINS--*/
			.mlr2{margin:0px 2px;} .mlr5{margin:0px 5px;} .mlr10{margin:0px 10px;}/*--MARGIN LEFT-RIGHT--*/
			.padd2{padding:2px;} .padd5{padding:5px;} .padd10{padding:10px;} .padd0{padding:0px;} /*--ALL SIDE PADDING--*/
			.pt4{padding-top:4px} .pt2{padding-top:2px} .pt5{padding-top:5px} .pt7{padding-top:7px} .pt15{padding-top:15px} /*--PADDING TOP--*/
			.plr5{padding:0px 5px;} .plr10{padding:0px 10px;}  /*--PADDING LEFT AND RIGHT--*/
			.pl10{padding-left:10px} .pl5{padding-left:5px} .pl2{padding-left:2px}  /*--PADDING LEFT--*/
			.pr10{padding-right:10px} .pr5{padding-right:5px} .pr2{padding-right:2px}  /*--PADDING RIGHT--*/
			.fl{float:left; display:block;} /*--FLOAT LEFT--*/
			.fr{float:right; display:block;} /*--FLOAT RIGHT--*/
			.border{border:1px solid #cccccc;} /*--ALL SIDE BORDER GRAY--*/
			.noborder{border:0px solid #fff; border-style:none;}
			.botborder{border-bottom:1px solid #cccccc;} /*--BOTTOM BORDER GRAY--*/
			.topborder{border-top:1px solid #cccccc;} /*--top BORDER GRAY--*/
			.leftborder{border-left:1px solid #cccccc;} /*--LEFT BORDER GRAY--*/
			td.valignTop{ 	vertical-align:top; }
			td.valignBottom{ vertical-align:bottom; }
			.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
			.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
			.bg1{background-color:#ECF1EA;} /* page bgcolor where forms are displayed */
			.clr1{color:#9900CC;} /*purple text */
			p{line-height:1.5; text-align:justify; margin:0px 0px 10px 0px;}
			.unBold{font-weight:normal;}
			#release_details li{margin-top:0px; margin-left:20px; line-height:1.5;}
			#release_details p{margin:0px 5px 10px 0px;}
			p ol li{margin:0px 5px 5px 10px;}
			.prplcolor{color:#5c2a79;}
			.grycolor{color:#58595b;}
			.grncolor{color:#329e9c}
			.text24{font-size:26px;}
			.text22{font-size:22px;}
			.text18{font-size:18px;}
			.text16{font-size:16px;}
			.text14{font-size:14px;}
			.text13{font-size:13px;}
			.bdrbtm{border-bottom:5px solid #CCC;}
			.textItalic{font-style:italic;}
			.lnhght{line-height:1.3;}
			.pt10{padding-top:10px;}
			.white{background-color:#FFFFFF; overflow:hidden;}
			.textBold{ font-weight:bold;}
			.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
			.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
			table tbody tr td { border:0px !important; }
		</style>
	</head>
	<body>
		<div class="section bg1" style="width:800px;">
			<div class="subheading m2">
				<!--window.close();-->
				<span class="closeBtn" onClick="$('#div_version_release_doc').slideUp('slow');"></span>
				<h3 class="m2" align="center">Version Release Document<br><span class="text13b">Version: <span class="unBold"><?php echo constant('PRODUCT_VERSION'); ?></span> &nbsp;&nbsp; Date: <span class="unBold"><?php echo constant('PRODUCT_VERSION_DATE'); ?></span></span></h3>
			</div>
			<div id="release_details" class="m5 white border padd10" style="max-height:450px; overflow-x:hidden; overflow-y:auto;">
				<div class="wapper">
					<div><img src="../../library/images/logo.png" alt=""/></div>
					<div class="clear"></div>
					<div><h1>iMedicMonitor Release Notes – Version R7</h1>
					</div>
					<div class="clear"></div>
					<div><h3>Auto and Manual Refresh</h3>
					</div>
					<div class="clear"></div>
					<p>Click the button to switch between <span class="bluhigh">Auto</span> and <span class="bluhigh">Manual Refresh</span>.
						Auto: As different users using different mapped computers open patient charts in iDoc, patients
						will move from room to room in iMedicMonitor automatically. Data refreshes every 10 seconds.
						You may click-to-move patients in this setting.
						<br>
						<br>
						<span class="bluhigh">Manual:</span> This setting pauses automatic patient movement to freely allow you to click-to-move
						patients to manually reorganize.</p>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/refresh_mode.jpg" width="2588" height="390" alt=""/></div>
					<div class="clear"></div>
					<h3>Click-to-Move</h3>
					<p>To move patients from room to room, you may click the patient’s name. The patient will be
						attached to your mouse cursor, and the original entry will be grayed out. Click the area of
						iMedicMonitor where you would like to place this patient, and the patient will move there. If, after
						clicking the patient’s name, you would like to cancel this operation, press the ESC (Escape) key
						on your keyboard.</p>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/click_to_move.jpg" alt=""/></div>
					<div class="clear mt60"></div>
					<div>
						<h3>Right-Click Menu</h3>
						<p>The right-click menu options have been reorganized.</p>
						<div class="clear"></div>
						<div><img src="../../library/images/version_release/right_click_menu.jpg" alt=""/></div>

					</div>
					<div class="clear"></div>
					<div>
						<h3>Done Option</h3>
						<p>Patients are removed from the active area of iMedicMonitor automatically when Check-out is
							selected in iDoc. Patients may be manually removed by selecting Done. Patients removed will
							appear in the Checked-Out Patients window (accessible by clicking the green check in the
							upper-right of iMedicMonitor).</p>
						<div class="clear"></div>
						<div><img src="../../library/images/version_release/done_option.jpg" alt=""/></div>

					</div>
					<div class="clear"></div>
					<div>

						<p>If a patient was marked as Done accidentally, you may move a patient back into the active area
							of iMedicMonitor by clicking Undo. The patient’s timer will display the amount of time that had
							passed since the patient was checked-in.</p>
						<div class="clear"></div>
						<div><img src="../../library/images/version_release/done_co.jpg" alt=""/></div>

					</div>
					<div class="clear"></div>

					<div>

						<h3>Patient Priority Shift</h3>
						<p>When a patient is marked as Done/Checked-Out, the patient’s priority will be set to Normal,
							even if they are brought back into iMedicMonitor. Other patients’ priority will shift accordingly.
							For example, if Priority 1 patient is Done, Priority 2 patient will become Priority 1. The patient
							that has been waiting longest with no priority marked will become Priority 3.</p><br>

						<h3>Patient Ready for Procedure</h3>
						<p>In addition to “Ready for Physician” and “Ready for Tech” you may mark a patient as ready for a
							procedure. Right-click the patient and under Ready For select a procedure. Procedures are
							color-coded, so you can differentiate between procedures at a glance.</p>
						<div class="clear"></div>
						<div></div>

					</div>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/pt_readyfor_procedure.jpg" alt=""/></div>
					<div class="clear"></div>
					<div><h3>Ready for Procedure Set-up</h3>
						<p>In iDoc, go to <span class="breadcum">Admin > iMedicMonitor</span>. In this screen, you may set which procedures you would
							like to make available in iMedicMonitor. You may also change the color associated with each
							procedure.</p></div>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/procedure_setup.jpg" alt=""/></div>
					<div class="clear"></div>
					<div><h3>Room View Labels</h3>
						<p>The room labels in Room View have a new look so that they are easier to read.</p></div>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/room_view_labels.jpg" alt=""/></div>
					<div class="clear"></div>
					<div><h3>Further Details</h3>
						<p>In Room View, hover over the black <span class="bluhigh">Appointment Details</span> button to see more information about
							this patient’s visit.</p></div>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/appointment_details.jpg" alt=""/></div>
					<div class="clear"></div>
					<div>You can also click the <span class="bluhigh">Clipboard</span> to see the Patient At A Glance in iMedicMonitor.<br>
						Release Notes</div>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/clipboard.jpg" alt=""/></div>
					<div class="clear"></div>
					<div><h3>Release Notes</h3>
						<p>You can view the latest iMedicMonitor Release Notes by clicking the version number.</p></div>
					<div class="clear"></div>
					<div><img src="../../library/images/version_release/release_notes.jpg" alt=""/></div>
					<div class="clear"></div>


					<div class="clear"></div>
				</div>    	
			</div>
			<div class="m5 alignCenter"><input onClick="$('#div_version_release_doc').slideUp('slow');" type="button" class="btn_normal" value="&nbsp;&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;&nbsp;&nbsp;"></div>
		</div>
	</body>
</html>
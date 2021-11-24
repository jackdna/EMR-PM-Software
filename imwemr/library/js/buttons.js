
//-- buttons.js --
function btn_htm(n,i,f,v,cls,bspn){
	if(n == "span"){ //for space		
	return "<span style=\"padding-left:"+v+"px;\"></span>";
	}else{
		btn_class = 'btn btn-success';
		switch(v.toLowerCase()){
			case 'cancel':
			case 'no':
			case 'close':
			case 'void':
			case 'delete':
			case 'archive':
				btn_class = 'btn btn-danger pull-right';
			break;
			/*case 'save':
			case 'done':
			case 'ok':
			case 'yes':
				btn_class = 'btn btn-success';				
				break;
			case 'delete selected':
			case 'delete':
				btn_class = 'btn btn-danger';
				break;
			case 'print':
			case 'download':
			case 'upload':
				btn_class = 'btn btn-info';
				break;*/
		}
		
		if(typeof(cls)!="undefined" && cls!=""){
			btn_class+=" "+cls;
		}
		//if(v.toLowerCase()=='cancel' && cls=='pull-right') btn_class += " "+"mlr10";
		
		bspn='';
		//if(typeof(bspn)!="undefined" && bspn!=""){
			//bspn="<span style=\"padding-left:8px;\"></span>";
		//}else{ bspn=""; }		
		
		var noFocs="";
		if(v=="<< Previous" || v=="Next >>"){noFocs=" onfocus='this.blur();' ";}
		
		return bspn+"<input type=\"button\" class=\""+btn_class+"\" align=\"bottom\""+
				"name=\""+n+"\" id=\""+i+"\" onClick=\""+f+"\" "+noFocs+
				"value=\""+v+"\"  />";
	}
}

function btn_show(id,ar){	
var str="";
var n="",v="",fn="",l=0,d="";
switch(id){
	case "DEF":
		var win_height = screen.height;
		var win_width = screen.availWidth * 0.9
		var arr = [ 
					/*["add_np", "Add New Patient", "top.change_main_Selection(eval('Patient_Info'), '"+JS_WEB_ROOT_PATH+"/interface/patient_info/demographics/expire_patient.php');"]
					];*/
					["add_np", "Add New Patient", "top.popup_win('"+JS_WEB_ROOT_PATH+"/interface/scheduler/common/new_patient_info_popup_new.php?source=demographics&search=true&frm_status=show_check_in&popheight="+win_height+"', 'newPatientWindow', 'width="+win_width+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1');"]
					];
		

		l = arr.length;
		for(var i = 0; i < l; i++){
			n = arr[i][0];
			v = arr[i][1];
			fn = arr[i][2];
			if(priv_vo_pt_info=="1")fn="top.view_only_pt_call(1);";
			str += btn_htm(n, n, fn, v);
		}
	break;


	case "WV":		
		var arr = [ ["btnPerNext","<< Previous","top.fmain.showPrevCharts('-1');","pull-left"],					
					["save","Save","top.fmain.saveMainPage();"],					
					["btnFinalize","Finalize","top.fmain.FinalizeMainPage();"],
					["btnUnfinalizeChart","Unfinalize","top.fmain.chartNoteUnfinalize();"],
					["btnEditChart","Edit","top.fmain.chartNoteEdit();"],
					["buttonPurge","Purge","top.fmain.getconfirmationPurgeRequest();"],
					["buttonChrtDel","Delete","top.fmain.deleteChart();"],
					["buttonPhyView","Phy. View","top.fmain.getPhyViewWV(this.value);"],
					["btnPerBck","Next >>","top.fmain.showPrevCharts('+1');","pull-right"],
					["cancel","Cancel","top.fmain.funClose()"],
				  ];
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];
			cls=arr[i][3];
			bs=arr[i][4];
			
			//filter
			if(ar["prgdel"]==1 && (v == "Purge" || v == "Delete")){ console.log("INSIDE: ");  }
			else{	
				if(ar["elem_per_vo"] == "1" && v != "Cancel") continue;
				if(ar["userTypeCn"] != "1" && (v == "Edit" || v == "Purge" || v == "Delete" || v == "Finalize" || v == "Unfinalize")) continue;
				if(ar["userTypeCn"] == "1" && v == "Phy. View") continue;
			}	
			
			if(v == "Unfinalize"){
				if((ar["f"]=="1") && (ar["r"]=="1")&&(ar["e"]==1)&&(ar["prg"]!="1")){}else{continue;}	
			}else if(v=="Save"||v=="Cancel"){
				if((ar["f"]=="1" && ar["r"]!="1") || ar["prg"]=="1") continue;
			}else if(v=="Finalize"){
				if(ar["f"]=="1" || ar["fz"]=="1") continue;
			}else if(v=="Edit"){
				if((ar["f"]=="1") && (ar["r"]!="1")&&(ar["e"]==1)&&(ar["prg"]!="1")){}else{continue;}
			}else if(v == "Purge"){
				if(ar["f"]=="1" && (ar["cvu"]=="1" || ar["prgdel"]==1)){
					if(ar["prg"]=="1"){
						v="Undo Purge";		
					}				
				}else{				
					continue;
				}
			}else if(v == "Phy. View"){ 				
				if((ar["f"]=="1" && ar["r"]!="1") || ar["prg"]=="1") continue;	
				if(ar["fpv"]=="1"){v="Tech. View";}
			}else if(v == "Delete"){
				if(ar["f"]=="1" && (ar["cvu"]=="1" || ar["prgdel"]==1) && ar["prg"]=="1"){}else{continue;}	
			}
			
			str += btn_htm(n,n,fn,v,cls,bs);			
		}	
		
	break;

	case "VF":
	case "VF-GL":	
	case "LABS":
	case "CELLCOUNT":
	case "BSCAN":
	case "EXTERNAL":
	case "DISC":
	case "IVFA":
	case "ICG":
	case "NFA":
	case "OCT":
	case "OCT-RNFL":	
	case "OTHER":
	case "PACHY":
	case "TOPO":
	case "GDX":	
	case "ASCAN":	

		if(id == "VF"||id == "VF-GL"){
			var savef = "top.fmain.saveVF();";
		}else if(id == "LABS"||id == "CELLCOUNT"||id == "BSCAN" || id == "PACHY" || id == "TOPO" || id == "OTHER"){
			var savef = "top.fmain.savePachy();";
		}else if(id == "DISC" || id == "EXTERNAL"){
			var savef = "top.fmain.saveDisc();";
		}else if(id == "IVFA"){
			var savef = "top.fmain.saveIvfa();";
		}else if(id == "ICG"){
			var savef = "top.fmain.saveIcg();";
		}else if(id == "NFA" || id == "OCT" || id == "OCT-RNFL" || id == "GDX"){
			var savef = "top.fmain.saveNfa();";
		}else if(id == "ASCAN"){
			var savef = "top.fmain.saveAscan();";
		}

		//filter
		var flg=0;
		if(ar["elem_per_vo"]=="1")flg=1;
		
		//Purge
		if(ar["purged"]!="0" && ar["purged"]!=""){
			purge="UndoPurge";
			flg=1;
		}else{
			purge="Purge";
		}
		
		var rtpath = ar["rtpath"];
		var arr = [ 
					["btnPrev","<< Previous","top.fmain.setPrevValues('-1');","pull-left"],
					["btn_interpret","Interpreted","top.fmain.test_interpreted();"],
					["btnReset","Reset","top.fmain.resetTestExam();"],
					//["span","10"],
					["save","Done",""+savef],
					//["span","10"],
					["btnEPost","ePost","top.fmain.epostpopTest();"],
					["btnPrint","Print","top.fmain.printTest();"],
					["btnPurge",""+purge,"top.fmain.resetTestExam(1);"],
					//["span","10"],
					["save","Order",""+savef],
					["Close","Cancel","top.fmain.location.href='"+rtpath+"/tests/index.php';"],
					["btnNxt","Next >>","top.fmain.setPrevValues('+1');","pull-right"]
				];//["span","20"],
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];
			cls=arr[i][3];
			bs=arr[i][4];
			
			if(v == "Interpreted" && ar["interpreted"]!="1")continue;
			if(v == 'Done' && ar['interpreted'] == '1' ) continue;
			
			if((flg==1) && (v != "Cancel" && v != "ePost" && v != "UndoPurge")) continue;
			//str += btn_htm(n,n,fn,v);
			str += btn_htm(n,n,fn,v,cls,bs);
		}

	break;
	
	case "OCU":
	case "GH":
	case "HP":
	case "MED":
	case "VS":
	case "ORDRST":
	case "PL":
	case "LAB":
	case "RAD":
	case "IMM":
	case "SX":
	case "ALRG":		
		
		var arr = [ ["save_medical_history","Save & Reviewed","top.fmain.save_medical_history();"],
					["print_medical_history","Print","top.fmain.data_action_function(this.id);"],
					["export_medical_history","Export","top.fmain.data_action_function(this.id);"],
					["import_medical_history","Import","top.fmain.data_action_function(this.id,'"+win_height+"');"],
					/*["scan_medical_history","Scan/Upload","top.fmain.data_action_function(this.id);"],*/
					["scan_medical_history","Scan/Upload","top.change_main_Selection(top.document.getElementById('Documents_Lab'));"],										
					["save_order_set","Save","top.fmain.save_order_set_data();"],
					["import_lab_hl7","HL7 Import","top.fmain.export_hl7('Import');"],
					["new_lab","New Lab Order","top.fmain.new_lab_order();"],
					["export_imm_hl7","HL7 Export","top.fmain.export_hl7();"],
					["med_exter_records","External Records","top.fmain.med_exter_records();"],
					["del_records","Delete","top.fmain.del_records();"],
					];
		//["upload_medical_history","Upload","top.fmain.call_scan_function(this.id);"],
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];
			if(priv_vo_clinical=="1") fn="top.fmain.view_only_acc_call(0);";
			
			//filter			
			//if(n=="save_medical_history" && (id=="VS" || id=="ORDRST" || id=="PL"))continue;
			if(n=="save_medical_history" && (id=="ORDRST" || id=="LAB"))continue;
			if(n!="save_medical_history" && (id=="OCU" || id=="GH"))continue;
			//if(n=="save_order_set" && id != "VS" && id!="ORDRST" && id!="PL")continue;
			if(n=="save_order_set" && id!="ORDRST")continue;
			if(n=="scan_medical_history" && id!="MED" && id!="LAB" && id!="RAD")continue;
			if(n=="export_imm_hl7" && (id!="IMM"))continue;
			if(n=="import_lab_hl7" && (id!="LAB"))continue;
			if(n=="med_exter_records" && (id!="MED"))continue;
			if(n=="new_lab" && (id!="LAB"))continue;
			if(n=="import_medical_history" && id!="MED" && id!="ALRG" && id!="PL")continue;
			if(n=="del_records" && id!="PL")continue;
			if(n=="export_medical_history" && id!="MED" && id!="SX" && id!="ALRG" && id!="IMM" && id!="VS")continue;			
			if(n=="print_medical_history" && id!="MED" && id!="SX" && id!="ALRG" && id!="IMM" && id!="VS" && id!="HP")continue;
			
			if((id=="VS" || id=="PL") && v=="Save & Reviewed") { v="Save"; }
			str += btn_htm(n,n,fn,v);
		}		
				
	break;

	case "DEMO":
	case "INS":
	case "ANP":
	case "CF":
	case "CF2":
	case "CF3":
	case "SCF":
	case "POSCF":
	case "CL1":
	case "CL2":
	case "CL3":
	case "PTD":
	case "PTINST":
	case "SCAN_DOCS":
	case "MULTI_UPLOAD":	
	case "ELIGIBILITY":	
		if( id == 'CF' || id == 'CF2' || id == 'SCF' || id == 'POSCF') {
			var AddApptExe = "top.change_main_Selection(top.document.getElementById('sch_icon_li'));"
			if(top.doc_pop)
				AddApptExe = "top.opener.top.change_main_Selection(top.opener.top.document.getElementById('sch_icon_li'));top.opener.top.fmain.focus();";
		}
		
		if(id == "DEMO"){
			// if(top.fmain.isUGAEnable == 1) {
			// var arr = [ ["demographic_save","Save Patient","top.fmain.call_functions('demographic_save');"],
			// 			["add_appt","Add Appt","top.change_main_Selection(top.document.getElementById('sch_icon_li'));"],
			// 			["uga_register","UGA Finance","top.apply_uga_finance('demographics');","btn-info"]
			// 			];
			// } else {
			var arr = [ ["demographic_save","Save Patient","top.fmain.call_functions('demographic_save');"],
						["add_appt","Add Appt","top.change_main_Selection(top.document.getElementById('sch_icon_li'));"]
						];
			// }
		}else if(id == "INS"){
			var arr = [ ["insurance_save","Save","top.fmain.call_functions('insurance_save');"],
						["add_appt","Add Appt","top.change_main_Selection(top.document.getElementById('sch_icon_li'));"]
						];
		}else if(id == "ANP"){
			var arr = [ ["butId2","Save","top.fmain.save_new_patient();"],
						["new_patient_saving_canceled","Cancel","top.fmain.location.href='"+WebRoot+"/interface/main/patient_tabs.php';"]
						];
		}else if(id == "CF"){
			var arr = [ ["consent_save","Save","top.fmain.consent_data.save_form('save_form');"],
						["consent_save_print","Save and Print","top.fmain.consent_data.save_form('print_form');"],
						//["consent_print","Print","top.fmain.consent_data.print_form()"],
						//["scan_consent_print","Print","top.fmain.consent_data_surgery.print_scan_form()"],
						["add_appt","Add Appt",AddApptExe]
						];
		}else if(id == "CF2"){
			var arr = [ ["consent_save","Save","top.fmain.consent_data.save_form('save_form');"],
						["consent_save_print","Save and Print","top.fmain.consent_data.save_form('print_form');"],
						//["consent_print","Print","top.fmain.consent_data.print_form()"],
						//["scan_consent_print","Print","top.fmain.consent_data_surgery.print_scan_form()"],
						["add_appt","Add Appt",AddApptExe],
						["consent_hold","On Hold for:","top.fmain.consent_data.hold_dr_sig()"]
						];
		}else if(id == "CF3"){
			var arr = [ ["consent_send_fax","Send Fax","top.fmain.show_consent_fax_div();"]
						];
		}else if(id == "SCF"){
			var arr = [ ["surgery_save","Save","top.fmain.consent_data_surgery.save_form('save_form');"],
						["surgery_save_print","Save and Print","top.fmain.consent_data_surgery.save_form('print_form');"],
						//["surgery_print","Print","top.fmain.consent_data_surgery.print_form();"],						
						["add_appt","Add Appt",AddApptExe],
						["consent_hold","On Hold for:","top.fmain.consent_data_surgery.hold_dr_sig()"]
						];
		}else if(id == "POSCF"){
			var arr = [ ["savePreOpHealth","Save","top.fmain.consent_data_surgery.savePreOpHealth();"],
						["preOpHealth_print","Print","top.fmain.consent_data_surgery.print_form_health_quest();"],
						["add_appt","Add Appt",AddApptExe]
						];	
		}else if(id == "CL1"){
			var arr = [ ["consult_fax_log","Fax Log","top.fmain.show_fax_log();"]
						];
		}else if(id == "CL2"){
			var arr = [ ["consult_send_fax","Send Fax","top.fmain.show_consult_fax_div();"]
						];
		}else if(id == "CL3"){
			var arr = [ ["consult_send_fax","Send Fax","top.fmain.show_consult_fax_div();"],
						["consult_fax_log","Fax Log","top.fmain.show_fax_log();"]	
						];
		}else if(id == "PTD"){
			var arr = [ ["savePtDocs","Save","top.fmain.ifrm_FolderContent.saveTemplateData();"]
						];
		}else if(id == "PTINST"){
			var arr = [ ["savePtInstructionDocs","Save","top.fmain.ifrm_FolderContent.submit_frm();"]
						];
		}else if(id == "SCAN_DOCS"){
			var arr = [ ["btSaveAsPDF","Save as PDF","top.fmain.ifrm_FolderContent.save_pdf_jpg('pdf');"],
						["btSaveAsJPG","Save as JPG","top.fmain.ifrm_FolderContent.save_pdf_jpg('jpg');"],
						["scnDocmntBtn","Scan Document","top.fmain.ifrm_FolderContent.scan_doc();"],
						["upldDocmntBtn","Upload Image","top.fmain.ifrm_FolderContent.upload_image();"],
						["btSaveComment","Save Comment","top.fmain.ifrm_FolderContent.save_comments();"],
						["btBackFolderCat","Go back to folder categories","top.fmain.ifrm_FolderContent.go_back_folder_cat();"],
						["btAddNew","Add New Folder","top.fmain.ifrm_FolderContent.add_new_folder_fun();"]
						];
		}else if(id == "MULTI_UPLOAD"){
			var arr = [ ["scanUploadBtnPdf","Upload","top.fmain.ifrm_FolderContent.scanUploadFun();"],
						["refreshBtnPdf","Refresh","top.fmain.ifrm_FolderContent.reload_frm();"],
						["savePdfBtn","Save","top.fmain.ifrm_FolderContent.submit_frm();"],
						["deletePdfSplitSelected","Delete","top.fmain.ifrm_FolderContent.delPdfSplitFun();"]
						];
		}
		else if(id == "ELIGIBILITY"){
			var arr = ar;
		}		
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];
			
			if(id == "DEMO") {
				b=arr[i][3];		
			} else {
				b='';
			}

			if(priv_vo_pt_info=="1" && 
				n!="consent_print" && n!="scan_consent_print" && 
				n!= "surgery_print" && n!="preOpHealth_print" && n!="add_appt") fn="top.fmain.view_only_call();";

			str += btn_htm(n,n,fn,v,b);
		}

	break;
	case "ELIGIBILITY_REPORT":		
		var arr = ar;		
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];			
			if(priv_vo_pt_info=="1" && 
				n!="consent_print" && n!="scan_consent_print" && 
				n!= "surgery_print" && n!="preOpHealth_print" && n!="add_appt") fn="top.fmain.view_only_call();";

			str += btn_htm(n,n,fn,v);
		}
	break;
	case "O4A":
	case "CLRJ":
	case "BP":
	case "PPR":
	case "OPTL":
	case "ADMN":
		var arr = ar;
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];

			str += btn_htm(n,n,fn,v);
		}
	break;

	case "NoPt":
		n="new_patient_add";
		v="Add New Patient";	
		fn="top.fmain.location = '"+JS_WEB_ROOT_PATH+"/interface/main/patient_tabs.php?new_patient=1';";
		str += btn_htm(n,n,fn,v);
	break;
	
	
	case "mur_stage1_2015":
	case "mur_stage2_2015":
	case "mur_stage1_2014":
	case "mur_stage2_2014":
	case "mur_stage2013":
		n=id;
		v="Cancel";
		loc = top.fmain.location.href;
		fn="top.fmain.location = "+loc;
		str += btn_htm(n,n,fn,v);
	break;
    
	case "mur_stage_qrda":
        var arr = ar;
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];

			str += btn_htm(n,n,fn,v);
		}
	break;
	
	/*case "MurAttestation":
	var arr = [ ["btn_pdf","Generate PDF","top.fmain.reportsFrame.checkBoxes(false);"] ];
	l=arr.length;
	for(var i=0;i<l;i++){
		n=arr[i][0];
		v=arr[i][1];
		fn=arr[i][2];			
		str += btn_htm(n,n,fn,v);
	}
	break;*/
	case "MURanalyzePrint":
	var arr = [ ["btn_pdf","Print ScoreCard","top.fmain.print_mur_scorecard();"] ];
	l=arr.length;
	for(var i=0;i<l;i++){
		n=arr[i][0];
		v=arr[i][1];
		fn=arr[i][2];			
		str += btn_htm(n,n,fn,v);
	}
	break;
	
	case "ACCOUNT":
		var arr = ar;
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];

			str += btn_htm(n,n,fn,v);
		}
	break;

	case 'VERIFICATION_SHEET':
		var arr = ar;
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];

			str += btn_htm(n,n,fn,v);
		}
	break;

	case 'AR_worksheet':
		var arr = ar;
		l=arr.length;
		for(var i=0;i<l;i++){
			n=arr[i][0];
			v=arr[i][1];
			fn=arr[i][2];

			str += btn_htm(n,n,fn,v);
		}
	break;
	
	default: 
		//Hide Buttons
	break;

}

var tempContainer = $('<div>').html(str);

var leftBtn = $('<div>').addClass('pull-left');
//var centerBtn = $('<div>');
var rightBtn = $('<div>').addClass('pull-right');

var wrapper = $('<div>').addClass('nowrap').css('overflow','hidden');

$(wrapper).append(leftBtn);
//$(wrapper).append(centerBtn);
$(wrapper).append(rightBtn);


var buttons = $(tempContainer).children();

$(buttons).each(function(count, obj){
	
	if( $(obj).hasClass('pull-right') )
	{
		$(obj).removeClass('pull-right');
		
		if($(obj).val() === 'Next >>')
			$(obj).appendTo(rightBtn);
		else
			$(obj).prependTo(rightBtn);
	}
	else if( $(obj).hasClass('pull-left') )
	{
		$(obj).removeClass('pull-left');
		
		if($(obj).val() === '<< Previous')
			$(obj).prependTo(leftBtn);
		else
			$(obj).appendTo(leftBtn);
	}
	else
	{
		$(obj).appendTo(wrapper);
	}
	
});

top.$('#page_buttons').html(wrapper);
//top.document.getElementById("page_buttons").innerHTML = $(.html(wrapper)).html();;

}
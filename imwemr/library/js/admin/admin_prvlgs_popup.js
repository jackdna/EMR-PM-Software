
function selectAll(o) {
	var ser = $(o).prop("checked");
	strnoncheck = "#priv_all_settings";
	if(ser==true){ strnoncheck+=",#priv_vo_clinical,#priv_vo_pt_info,#priv_purge_del_chart"; }
	$(o).parents(".recbox").find(":checkbox").not(""+strnoncheck).prop("checked", ser);
	$(o).parents(".recbox").find("i").each(function(){
			var a  = $(this).data("showdiv"); $("#"+a).find(":checkbox").prop("checked", ser);
			if(ser==true){ $(this).parents(".checkboxcolor").removeClass("checkboxcolor"); }
		});
	$("#priv_CnfdntlTxt_Read").prop("checked", false);
}

function selectDeselect_all(aId, aChecked) {
	var collection = document.getElementById(aId).getElementsByTagName('INPUT');
	for (var x=1; x<collection.length; x++) {
		if ((collection[x].type.toUpperCase()=='CHECKBOX') && ((collection[x].disabled)!=true) && collection[x].className!="view_only"){
			collection[x].checked = aChecked;
			if(collection[x].id=="priv_vo_pt_info"){
				collection[x].checked = false;
			}
		}
	}
	if(aId=="div_Reports_n_reports"){ $("#el_sel_rprt").prop("checked", aChecked); }
}

function selectDeselect_all_admin(aId, aChecked,rep,disbl_only) {
	$("#priv_all_settings").parent().removeClass("checkboxcolor");
	flg = $("#priv_all_settings").prop("checked");
	$("#el_sel_settings, #el_sel_clinical, #el_sel_fd, #el_sel_acc, #el_sel_rprt, #el_sel_portal, #el_sel_icon").each(function(){
				$(this).prop("checked", flg).triggerHandler("click");
				$(this).parent().removeClass("checkboxcolor");
			});
}

/*
function selectDeselect_all_admin_bak(aId, aChecked,rep,disbl_only) {
	if(typeof(disbl_only)=="undefined"){ disbl_only=0; }
	var flgchk = (aChecked==true && disbl_only=="1") ? 0 : 1;
	var collection = document.getElementById(aId).getElementsByTagName('INPUT');
	for (var x=0; x<collection.length; x++) {
		if ((collection[x].type.toUpperCase()=='CHECKBOX')){
			collection[x].disabled=false;
			if(flgchk){collection[x].checked=aChecked;}
		}
	}

	var collection = document.getElementById('el_div_clinic').getElementsByTagName('INPUT');
	for (var x=0; x<collection.length; x++) {
		if ((collection[x].type.toUpperCase()=='CHECKBOX')){
			collection[x].disabled=aChecked;
			if(flgchk){collection[x].checked=aChecked;}
			if(collection[x].id=="priv_pis"){
				collection[x].disabled=false;
			}
			if(collection[x].id=="priv_vo_clinical" || collection[x].id=="priv_purge_del_chart"){
				if(flgchk){collection[x].checked=false;}
			}

		}
	}
	var collection = document.getElementById('el_main_front_desk').getElementsByTagName('INPUT');
	for (var x=0; x<collection.length; x++) {
		if ((collection[x].type.toUpperCase()=='CHECKBOX')){
			collection[x].disabled=aChecked;
			if(flgchk){
			collection[x].checked=aChecked;
			if(collection[x].id=="priv_vo_pt_info" ){
				collection[x].checked=false;
			}
			}
		}
	}
	var collection = document.getElementById('div_acc_bll').getElementsByTagName('INPUT');
	for (var x=0; x<collection.length; x++) {
		if ((collection[x].type.toUpperCase()=='CHECKBOX')){
			collection[x].disabled=aChecked;
			if(flgchk){collection[x].checked=aChecked;}
			if(collection[x].id=="priv_ref_physician" || collection[x].id=="priv_ins_management" ||  collection[x].id=="priv_Billing" || collection[x].id==" priv_edit_financials" || collection[x].id=="priv_del_charges_enc" || collection[x].id=="priv_del_payment"){
				collection[x].disabled=false;
			}
		}
	}
	//if(rep==""){
		var collection = document.getElementById('div_Reports_n_reports').getElementsByTagName('INPUT');
		for (var x=0; x<collection.length; x++) {
			if ((collection[x].type.toUpperCase()=='CHECKBOX')){
				if(flgchk){collection[x].checked=aChecked;}
			}
		}
		if(flgchk){
		$("#allprivdivs").find("[data-parent=\"div_Reports_n_reports\"]").find("input[type=checkbox]").prop("checked", aChecked);
		}
	//}
}
*/
//Binding Events on checkbox checked
/*
$(".privTable input[type=checkbox]").on("click", function(){
    var chk = false;
    var sdv = $(this).siblings("i").data("showdiv");
    if($(this).is(":checked") == true) {
	$(this).siblings("i").trigger("click"); chk = true;
    }
    $("#"+sdv+" :checkbox").prop("checked", chk).triggerHandler("click");
    $("#priv_select_all").prop("checked", chk);
});
*/


$("#priv_div_modal").on("shown.bs.modal", function(event) {
    $(".adminPrivDiv").hide();

    var button = $(event.relatedTarget);
    var recipient = button.data("whatever");
    var showdiv = button.data("showdiv");
    var puchkbx = button[0].id;
    if(showdiv) {
	$("#"+showdiv).show();
    }
    var modal = $(this);
    var title_recipient=recipient.replace("_", " ");
    modal.find(".modal-title").text(title_recipient+" Privileges");
    modal.find("#popupsection").val(recipient);
    modal.find("#puchkbx").val(puchkbx);

    if ($("#admin"+recipient).find("input:checkbox:not(:checked)").length == 0) {
	$("#priv_select_all").prop("checked", true);
    } else {
	$("#priv_select_all").prop("checked", false);
    }

		$("#priv_select_all").parent().show();
		//if Confidential Text : hide Select all
		if(title_recipient == "Confidential Text"){
			$("#priv_select_all").parent().hide();
		}

    var oldvals="";
    $($("#admin"+recipient).find("input:checkbox")).each(function(key, elem) {
	if($("#"+elem.id).is(":checked")) {
	    oldvals+=elem.id+"::"+1+",";
	} else {
	    oldvals+=elem.id+"::"+0+",";
	}
    });
    document.getElementById("popupoldvals").value = oldvals;

});

function resotreOldpriv() {
    var oldvals = document.getElementById("popupoldvals").value;
    var result = oldvals.split(",");
    $(result).each(function(key, valu) {
	var privvalu = valu.split("::");
	if(privvalu[1]==1) {
	    $("#"+privvalu[0]).prop("checked", true);
	} else {
	    $("#"+privvalu[0]).prop("checked", false);
	}
    });

		if(oldvals.indexOf("CnfdntlTxt")!=-1 && oldvals.indexOf("1")==-1){
			$("#priv_cnfdntl_txt").prop("checked", false);
		}

    $("#priv_div_modal").modal("hide");
}

$("#priv_select_all").on("click", function() {
    var section = $("#popupsection").val();
    if($("#priv_select_all").is(":checked")) {
	$("#admin"+section).find("input:checkbox").prop("checked", true);
    } else {
	$("#admin"+section).find("input:checkbox").prop("checked", false);
    }
});


$(".priv_all_settings").on("click", function() {

    var parentId = $(this).closest(".el_main_priv").attr("id");

    var propVal = false;
    if($(this).is(":checked")) {
	propVal = true;
    }

    var elemLength = $("#allprivdivs").find("[data-parent=\""+parentId+"\"]");
    //if(elemLength > 0){
	$("#allprivdivs").find("[data-parent=\""+parentId+"\"]").find("input[type=checkbox]").prop("checked", propVal);
    //}

    privcheckboxcolor();

});


$(".privoptioncheck").on("click", function() {
    if($(".privoptioncheck").not(":checked")) {
	$("#priv_select_all").prop("checked", false);
	//$("#priv_all_settings").prop("checked", false);
    }

    //
    if($(this).parents(".adminPrivDiv").find(":checked").length == $(this).parents(".adminPrivDiv").find("input[type=checkbox]").length){
	$("#priv_select_all").prop("checked", true);
    }

		//Confedential text
		if(this.checked){
			if($(this).prop("id")=="priv_CnfdntlTxt_Full"){
					$("#priv_select_all").prop("checked", true);
					$("#priv_CnfdntlTxt_Read").prop("checked", false);
			}else if($(this).prop("id")=="priv_CnfdntlTxt_Read"){
					$("#priv_select_all, #priv_CnfdntlTxt_Full").prop("checked", false);
			}
		}

});

$("#new_priv_div").on("shown.bs.modal", function(event) {
     privcheckboxcolor();
});

function privcheckboxcolor_org() {
	$(".el_main_priv").each(function(key, parentelm) {
	var parentelem=$(parentelm);
	$("#allprivdivs").children().each(function(key, elem) {
	var id=elem.id;
	if ($("#"+id).find("input:checkbox:not(:checked)").length == 0 ) {
	    NewString = id.replace("admin", "");
		parentelem.find("input[type=checkbox]").each(function(key1, elem1) {
		var id1 = elem1.id

		var section1 = $("#"+id1).parent("div").children("i").data("whatever");
		if(section1==NewString) {
		    $("#"+id1).parent("div").removeClass("checkboxcolor");
		}
	    });
	} else {
	    NewString = id.replace("admin", "");
		parentelem.find("input[type=checkbox]").each(function(key1, elem1) {
		var id1 = elem1.id

		var section1 = $("#"+id1).parent("div").children("i").data("whatever");
		if(section1==NewString) {
		    $("#"+id1).parent("div").addClass("checkboxcolor");
				//
				if(section1=="Confidential_Text"){
					if($("#priv_CnfdntlTxt_Full").prop("checked")){
						$("#"+id1).parent("div").removeClass("checkboxcolor");
					}else{
						$("#"+id1).parent("div").addClass("checkboxcolor");
					}

				}
		}

	    });
	}
    });
    });
}

function privcheckboxcolor() {
   privcheckboxcolor_org();
   //check main checkbox
   var s = $("#puchkbx").val();
   var r = $("#priv_div_modal .adminPrivDiv:visible :checked").length == 0 ? false : true;
   var ochk = $("#"+s).parent().find("input[type=checkbox]");
   if(r){  ochk.prop("checked", r); }
   if(ochk[0]){  check_cat_all_checkbox(ochk[0], 1); }
   //ochk.triggerHandler("click"); //Commented as it is checkon all options in popup

    $("#priv_div_modal").modal("hide");
}

function set_api_op(flg){
	$("#priv_report_api_access, #priv_report_Access_Log, #priv_report_Call_Log").prop("checked", flg);
	privcheckboxcolor();
}

function onload_privilages_popup(){
	$("#ele_priv_chk .checkboxcolor").each(function(){ $(this).removeClass("checkboxcolor"); }); //

	privcheckboxcolor_org();

	//Select select
	$("#ele_priv_chk>div").each(function(){
			if($(this).find(".head :checkbox").get(0).checked){
				var allchkbx = $(this).find(".table :checkbox").not("#priv_vo_clinical,#priv_purge_del_chart,#priv_vo_pt_info, .invisible :checkbox");
				var to = $(this).find(".head :checkbox").get(0);

				if(allchkbx.length == allchkbx.filter(":checked").length && $(this).find(".table .checkboxcolor").length==0){
					$(to).prop("checked", true);
					$(to).parent().removeClass("checkboxcolor");
				}else{
					if(to.checked==true){
						$(to).parent().addClass("checkboxcolor");
					}
				}
			}
		});
	//I
	$("#ele_priv_chk i").each(function(){
			var id = $(this).data("showdiv"); if(typeof(id)!="undefined" && id!=""){ if($("#"+id).find(":checked").length<=0){ $(this).parent().addClass("checkboxcolor"); }  }
		});


	//
	if($("#priv_all_settings").prop("checked")){
		$("#ele_priv_chk>div").each(function(){
				var chkhd = $(this).find(".head :checkbox").get(0);
				if((chkhd.checked && $(chkhd).parent().hasClass("checkboxcolor")) || chkhd.checked==false){
					$("#priv_all_settings").parent().addClass("checkboxcolor");
				}
			});

		//$("#priv_all_settings").triggerHandler("click");
		//selectDeselect_all_admin('el_main_priv',true,'',1);//disable only
	}
}

function check_all_checkbox(){
	var flg_all=true;
	$("#ele_priv_chk .head :checkbox[id*=el_sel]").each(function(){
			if(this.checked==false || $(this).parent().hasClass("checkboxcolor")!=false){
				flg_all=false;
			}
		});

	if($("#priv_all_settings").is(":checked") && $("#priv_all_settings").parent().hasClass("checkboxcolor")==false){
		if(!flg_all){
			$("#priv_all_settings").parent().addClass("checkboxcolor");
		}
	}else if(flg_all){
		$("#priv_all_settings").prop("checked", true);
		$("#priv_all_settings").parent().removeClass("checkboxcolor");
	}
}

function chk_subOpts(obj){
	if($(obj).siblings("i").length>0 && $(obj).parents(".privTable").length>0){
		var chk = false;
		var sdv = $(obj).siblings("i").data("showdiv");
		if($(obj).is(":checked") == true) {
		$(obj).siblings("i").trigger("click"); chk = true;
		}
		$("#"+sdv+" :checkbox").prop("checked", chk);
		$("#priv_select_all").prop("checked", chk);
		//
		if(chk = true && $(obj).prop("id") == "priv_cnfdntl_txt"){
			$("#"+sdv+" :checkbox[id='priv_CnfdntlTxt_Read']").prop("checked", false);
		}
	}
}

function check_cat_all_checkbox(obj, flg_dn_chk_sub){
	if(typeof(obj)=="undefined"){return;}

	if( obj.id=="priv_all_settings" || obj.id=="priv_vo_clinical" || obj.id=="priv_purge_del_chart" || obj.id=="priv_vo_pt_info"){
		return false;
	}
	if(obj.id.indexOf("el_sel")!=-1){
		if(obj.checked==false){
			$(obj).parent().removeClass("checkboxcolor");
		}
		check_all_checkbox();
	}
	else{
		//for
		if(typeof(flg_dn_chk_sub)=="undefined"){
		chk_subOpts(obj);
		}
		
		var cat_chk_bx = $(obj).parents(".recbox").find(".head :checkbox[id*=el_sel]");
		if(obj.checked==false || $(obj).parent().hasClass("checkboxcolor")){
			if(cat_chk_bx.is(":checked")){
				cat_chk_bx.parent().addClass("checkboxcolor");
			}
			if($("#priv_all_settings").is(":checked")){
				$("#priv_all_settings").parent().addClass("checkboxcolor");
			}

		}else if(obj.checked==true){
			var alckbx = $(obj).parents(".recbox").find(":checkbox").not(".head :checkbox, #priv_vo_clinical, #priv_purge_del_chart, #priv_vo_pt_info, .invisible :checkbox");
			if(alckbx.length == alckbx.filter(":checked").length){
				if($(obj).parents(".recbox").find(".checkbox").not(".head .checkbox").filter(".checkboxcolor").length==0){
					cat_chk_bx.prop("checked", true);
					cat_chk_bx.parent().removeClass("checkboxcolor");
					check_all_checkbox();
				}else{
					if(cat_chk_bx.is(":checked")){
						cat_chk_bx.parent().addClass("checkboxcolor");
					}
					if($("#priv_all_settings").is(":checked")){
						$("#priv_all_settings").parent().addClass("checkboxcolor");
					}
				}
			}
		}
		if($(obj).prop("id") == "priv_cnfdntl_txt" && $(obj).prop("checked")){
				if($("#priv_CnfdntlTxt_Full").prop("checked")){
					$(obj).parent().removeClass("checkboxcolor");
				}else if($("#priv_CnfdntlTxt_Read").prop("checked")){
					$(obj).parent().addClass("checkboxcolor");
				}
		}
	}
}

$(document).ready(function(){

	$("#ele_priv_chk :checkbox").bind("click", function(){ check_cat_all_checkbox(this);});
	//purge/delete should enable when work view is enabled
	$("#priv_purge_del_chart, #priv_cl_work_view").on("click", function(){
				if(this.id.indexOf("priv_purge_del_chart")!=-1 && this.checked){ $("#priv_cl_work_view").prop("checked", true);  }
				else 	if(this.id.indexOf("priv_cl_work_view")!=-1 && this.checked==false){ $("#priv_purge_del_chart").prop("checked", false);  }
			});
	$("#priv_api_access").on("click", function(){ if(this.checked){ set_api_op(true); }  });

	$("#priv_billing_Denial_Mgmt, #priv_billing_Reason_Codes").on("click", function(){
			if(this.id.indexOf("priv_billing_Denial_Mgmt")!=-1 && this.checked){ $("#priv_billing_Reason_Codes").prop("checked", true);  }
			else 	if(this.id.indexOf("priv_billing_Reason_Codes")!=-1 && this.checked==false){ $("#priv_billing_Denial_Mgmt").prop("checked", false);  }
		});

	});

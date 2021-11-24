
// JavaScript Document
function dgi(id){return document.getElementById(id);}
function changeBackImage(obj,val){
	var cur_tab = document.getElementById("curr_tab").value;
	if(cur_tab != obj.id){
		var clasName = val == false ? 'hovertab' : 'normaltab';
		
		obj.className = clasName;
	}
}
function changeSelection(tdobj){
	showHideScanUploadBt("hide");		
	showLoadingImg("inline");
	var currTab = document.getElementById("curr_tab").value;
	dgi("submit_btn").style.display = 'none';
	var tdobj = tdobj.id;							
	document.getElementById("curr_tab").value=tdobj;							 
	eval(currTab +".className = 'normaltab';");
	var currContentTD = eval(currTab +".tbl");				
	eval(tdobj +".className = 'hovertab';");
	var tdContentTD = eval(tdobj +".tbl");
	switch(tdobj){
		case "Scan_Docs":
			window.frames['iFrameDocuments'].location.href = 'scan_docs.php';								
		break;
		case "Pt_Docs":
			window.frames['iFrameDocuments'].location.href = 'pt_docs.php';	
		break;
	}
}

function saveTemplateData(){
	showHideScanUploadBt("hide");
	dgi("submit_btn").disabled = true;
	showLoadingImg("block");
	window.frames["iFrameDocuments"].ifrm_FolderContent.template_frm.submit();
}

function showLoadingImg(val){
	showHideScanUploadBt("hide");
	dgi("loading_img").style.display = val;
}

function save_comments(){
	top.iFrameDocuments.ifrm_FolderContent.save_comments();
}

function go_back_folder_cat(){
	showHideScanUploadBt("hide");
	top.iFrameDocuments.ifrm_FolderContent.go_back_folder_cat();
}
function save_pdf_jpg(op){
	top.iFrameDocuments.ifrm_FolderContent.save_pdf_jpg(op);
}

function showHideScanUploadBt(op, page){
	page = page || "";
	if(op == "show"){
		if(page == upload){
			document.getElementById("btSaveComment").style.display = "inline-block";
			document.getElementById("btBackFolderCat").style.display = "inline-block";
		}
		else if(page == scan){
			document.getElementById("btSaveAsPDF").style.display = "inline-block";
			document.getElementById("btSaveAsJPG").style.display = "inline-block";
		}
	}
	else if(op == "hide"){		
		document.getElementById("btSaveComment").style.display = "none";
		document.getElementById("btBackFolderCat").style.display = "none";
	
		document.getElementById("btSaveAsPDF").style.display = "none";
		document.getElementById("btSaveAsJPG").style.display = "none";
	
	}
}

// JavaScript Document
document.onkeydown = keyCatcher;
function keyCatcher() 
{
	var e = event.srcElement.tagName;
	//var f= event.srcElement.tagType;
	//alert(f);
 	//
	if (event.keyCode == 8 && e != "INPUT" && e != "TEXTAREA") 
	{
		event.cancelBubble = true;
		event.returnValue = false;
	}
}
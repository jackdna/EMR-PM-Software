// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

var outT,inT,curid;
var leftPos,widthPos, moveBoth;
function slide(id, c, left, width, right){
	widthPos = -(parseInt(width));	
	var sliderRightOut = top.frames[0].document.getElementById("sliderRightOut").value;
	if(id == 'sliderBarLEFT'){		
		leftPos = parseInt(top.frames[0].document.getElementById(id).style.left);
		if(leftPos < 0){
			curid=id;
			if(inT){
				clearInterval(inT);clearInterval(outT);
			}
			outT = setInterval("moveOutPos2()", 10);				
			top.frames[0].document.getElementById(id).cursor="hand";
			top.frames[0].document.getElementById("imageLeft").innerHTML = '<img src="images/move_back.jpg">';
		}else{
			curid=id;
			if(outT){
				clearInterval(outT);clearInterval(inT);
			}
			inT = setInterval("moveInPos2()", 10);	
			top.frames[0].document.getElementById("imageLeft").innerHTML = '<img src="images/move_forward.jpg">';
		}
	}else{	
		leftPos=parseInt(top.frames[0].document.getElementById(id).style.right);
		if(leftPos < 0){
			curid=id;
			if(inT){
				clearInterval(inT);clearInterval(outT);
			}
			outT = setInterval("moveOutPosRight2()", 15);	
			top.frames[0].document.getElementById(id).cursor="hand";
			top.frames[0].document.getElementById("imageRight").innerHTML = '<img src="images/move_forward.jpg">';
		}else{
			curid=id;
			if(outT){
				clearInterval(outT);clearInterval(inT);
			}
			inT = setInterval("moveInPosRight2()", 15);	
			top.frames[0].document.getElementById("imageRight").innerHTML = '<img src="images/move_back.jpg">';
		}
	}
}

function moveInPos(p, id){
	document.getElementById(id).style.left = p;
}
function moveOutPos(p, id){
	document.getElementById(id).style.left = p;
}

function moveInPos2(){
	if(top.frames[0].document.getElementById("sliderBarLEFT").style.left=='-185px'){
		if(moveBoth == true){
		 	leftPos = top.frames[0].document.getElementById("sliderBarRight").style.right;
			pxPos = leftPos.indexOf('px');
			leftPos = leftPos.substr(0, pxPos);
			widthPos = -255;
			curid = 'sliderBarRight';	
			moveInPosRight2();
		}
	}else{
		if(leftPos >= widthPos){
			var obj = top.frames[0].document.getElementById(curid);
			obj.style.left = parseInt(obj.style.left) - 10 + "px";
			leftPos=parseInt(obj.style.left);
		}else{
			clearInterval(inT);			
		}	
	}
}
function moveOutPos2(){
	if(leftPos < 0){		
		var obj = top.frames[0].document.getElementById(curid);
		obj.style.left = parseInt(obj.style.left) + 10 + "px";
		leftPos=parseInt(obj.style.left);
	}else{
		clearInterval(outT);
	}
}
function moveInPosRight2(){
	if(leftPos >= widthPos){
		var obj = top.frames[0].document.getElementById(curid);
		obj.style.right = parseInt(obj.style.right) - 10 + "px";
		leftPos=parseInt(obj.style.right);
    }else{
		clearInterval(inT);			
	}	
}
function moveOutPosRight2(){
	if(leftPos < 0){
		var obj = top.frames[0].document.getElementById(curid);
		obj.style.right = parseInt(obj.style.right) + 10 + "px";
		leftPos=parseInt(obj.style.right);
	}else{
		clearInterval(outT);
	}
}
// HIDE AND DISPLAY
function showContents(innerC, t, mainC, bar){
	if(bar=="sliderBarLEFT"){
		var lastInnerC = document.getElementById("leftInnerOpen").value;
		var lastMainC = document.getElementById("leftMainOpen").value;
		document.getElementById("leftMainOpen").value = t;
		document.getElementById("leftInnerOpen").value = innerC;
		for(var j = 0; j<lastInnerC; j++){
			if(document.getElementById("innerContent"+lastMainC+''+bar+''+j)){
				document.getElementById("innerContent"+lastMainC+''+bar+''+j).style.display = "none";
			}
		}
	}else{
		var rightlastInnerC = document.getElementById("rightInnerOpen").value;
		var rightlastMainC = document.getElementById("rightMainOpen").value;
		document.getElementById("rightMainOpen").value = t;
		document.getElementById("rightInnerOpen").value = innerC;
		for(var j = 0; j<rightlastInnerC; j++){
			if(document.getElementById("innerContent"+rightlastMainC+''+bar+''+j)){
				document.getElementById("innerContent"+rightlastMainC+''+bar+''+j).style.display = "none";
			}
		}
	}
	for(var i=0; i<innerC; i++){
		document.getElementById("innerContent"+t+''+bar+''+i).style.display = "block";
	}	
}
// HIDE AND DISPLAY
// CODE TO HIDE AND DISPLAY Sliders DONE BY Mamta & Munisha ON BLANK PAGE
function hideSliders() 
{		
	
	if(top.document.getElementById("toggle_btn1")) { 
		if(parent.top.$("#sidebar-wrapper").hasClass('toggle_AGAIN'))
		{ 
			top.document.getElementById("toggle_btn1").click();
		}
	}
	
	if(top.document.getElementById("patient_form")) { 
		if(parent.top.$("#patient_form").hasClass('in'))
		{ 
			parent.top.$('#patient_form').modal('hide');
		}
	}
	if(top.document.getElementById("pt_ocular_sx_hx")) { 
		if(parent.top.$("#pt_ocular_sx_hx").hasClass('in'))
		{ 
			parent.top.$('#pt_ocular_sx_hx').modal('hide');
		}
	}
	
	if(top.document.getElementById("unfinalizeHistoryModal")) { 
		if(parent.top.$("#unfinalizeHistoryModal").hasClass('in'))
		{ 
			parent.top.$('#unfinalizeHistoryModal').modal('hide');
		}
	}
	
	
	//$('#toggle_btn1').click()
	//$('#toggle_btn1').hide('fast');
	//$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
	/*
	moveBoth = true;
	leftPos = parseInt(top.frames[0].document.getElementById("sliderBarLEFT").style.left);
	widthPos = -175;
	curid="sliderBarLEFT";
	if(outT){
		clearInterval(outT);				
	}
	inT = setInterval("moveInPos2()", 10);				
	top.frames[0].document.getElementById("imageLeft").innerHTML = '<img src="images/move_forward.jpg">';
	top.frames[0].document.getElementById("imageRight").innerHTML = '<img src="images/move_back.jpg">';
	*/
}
function changeColor(setColor){
	if( top.frames[0].document.getElementById("sidebar-wrapper") )
		top.frames[0].document.getElementById("sidebar-wrapper").style.background = setColor;
}

// END CODE TO HIDE AND DISPLAY Sliders DONE BY Mamta & Munisha ON BLANK PAGE


//CODE FOR APPLYING BUTTONS(save,cancel,print,save&print and finalize) ON MAIN PAGE
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; 
  document.MM_sr=new Array;
  for(i=0;i<(a.length-2);i+=3)
  	if ((x=MM_findObj(a[i]))!=null){
		document.MM_sr[j++]=x; 
		if(!x.oSrc) 
			x.oSrc=x.src;			
		x.src=a[i+2];
	}
}


$(window).load(function(event)
{	
	parent.top.$(".loader").fadeOut(1000).hide(1000);	
})

//END CODE TO APPLY BUTTONS DONE BY munisha

//FUNCTION TO CHECK SINGLE CHECKBOX
function checkSingleAdmin(elemId,grpName)
{
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true)
	{		
		for(var i=0;i<len;i++)
		{
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
			{
				//obgrp[i].click();
				obgrp[i].checked=false;
			}
		}	
	}
}
//FUNCTION TO CHECK SINGLE CHECKBOX

jQuery.fn.center = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
return this;
};


var unfinalizeHistory =	function(PCI, PID,ASCID,SID,HasAdminPriv)
{
	$.ajax(
	{
			url : './common/unfinalize.php',
			type : 'POST',
			data : {'PCI':PCI,'request':'history' },
			dataType:"json",
			beforeSend:function()
			{
				top.$('#patient_form').modal('hide');
				top.$('#pt_ocular_sx_hx').modal('hide');
				//top.showDialogBox('Chart Notes Finalize History','<i class="fa fa-spinner fa-pulse"></i>&nbsp;Please wait while we fetch chart notes history.<br />It may take a second(s).');
			},
			success:function(response)
			{
				
				if(response.success == 1)
				{
						top.$("#unfinalizeHistoryModal #ModalTitle").html('Chart Notes Finalized History');
						
						top.$("#unfinalizeHistoryModal #patientName").html('<b class="fa fa-user-plus "></b>&nbsp;' + response.data.patient_name+ '<small style="color:white">'+response.data.dos+'</small>');
						
						
						if(response.data.finalize_status == 'Finalized' && HasAdminPriv == 1 )
						{
							var V	=	"'blankform.php', '', '', '#D1E0C9', '"+PID+"' , '"+PCI+"', '" + ASCID+"' " ;
							top.$("#unfinalizeHistoryModal #unfinalizeButton").html("<a href=\"javascript:void(0);\"class=\"btn btn-success\" onclick=\"javascript:unfinalize('"+PCI+"','"+ PID + "', '"+ ASCID + "', '"+SID+"' );\" >Unfinalize Chart</a>");
						
						}
						else
						{
								top.$("#unfinalizeHistoryModal #unfinalizeButton").html('');
						}
						
						var html	=	'';
						html	+=		'<table  class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-hover">';
						
						html	+=		'<thead class="cf">';
						html	+=		'</thead>';
						html	+=		'<tr>';
						html	+=		'<th class="text-left" >Sr.No.</th>';
						html	+=		'<th class="text-left" >Action </th>';
						html	+=		'<th class="text-left" >Finalized Section</th>';
						html	+=		'<th class="text-left" >Finalized/Unfinalized By</th>';
						html	+=		'<th class="text-left" >Date</th>';
						html	+=		'<th class="text-left" >Time</th>';
						html	+=		'</tr>';	
						html	+=		'<tbody>';
						
						var counter	=	0
						$.each(response.data.finalize_history, function(index,record)
						{
								html	+=		'<tr>';
								html	+=		'<td class="text-left" >'+ (++counter)+'</td	>';
								html	+=		'<td class="text-left" >' + record.action+ '</td>';
								html	+=		'<td class="text-left" >' + record.action_mode+ '</td>';
								html	+=		'<td class="text-left" >' + record.user+ '</td>';
								html	+=		'<td class="text-left" >' + record.date+ '</td>';
								html	+=		'<td class="text-left" >' + record.time+ '</td>';
								html	+=		'</tr>';
							
						});
						
						
						html	+=		'</tbody>';
						html	+=		'</table>';
						
						top.$("#unfinalizeHistoryModal #myTabContent").html(html);
						
						top.$("#unfinalizeHistoryModal").modal({show:true,backdrop:false});
						
						top.closeDialogBox();
						
				}
				else
				{
					top.showDialogBox('Chart Notes Finalize Histroy','Error while fetching records.....please try again. <br/>Alert will be close in <span id="close_seconds">5</span> seconds');
					var closeDialog = setInterval(closeTimerDialogBox,1000);
					setTimeout( function(){
						clearInterval(closeDialog);
						 top.$('#patient_form').modal('show')
					},5100);
					
					
				}
				
			}
			
	});
	
	
}

var unfinalize	=	function(PCI,PID,ASCID,SID)
{

	if( typeof PID !== 'undefined'  && typeof PCI !== 'undefined'  &&  typeof SID !== 'undefined'  )
	{
		var Url = 'mainpage.php?patient_id='+PID+ '&pConfId='+PCI+'&multiwin=yes&stub_id='+SID
		if (ASCID !== '' )		Url = Url + '&ascId=' + ASCID ;
	}
	
	$.ajax(
	{
			url : './common/unfinalize.php',
			type : 'POST',
			data : {'PCI':PCI,'request':'unfinalize' },
			dataType:"json",
			beforeSend:function()
			{
				top.$('#patient_form').modal('hide');
				top.$("#unfinalizeHistoryModal").modal('hide');
				top.$('#pt_ocular_sx_hx').modal('hide');
				top.showDialogBox('Unfinalizing Chart Notes','<i class="fa fa-spinner fa-pulse"></i>&nbsp;Please wait while we are unfinalizing chart notes.<br />It may take a second(s).');
			},
			success:function(data)
			{
				if(data.success == 1)
				{
					top.showDialogBox('Unfinalizing Chart Notes','Reloading is required...please wait. ');
					window.location.href= Url;
					
				}
				else
				{
					top.showDialogBox('Unfinalizing Chart Notes','Error while unfinalizing.....please try again. <br/>Alert will be close in <span id="close_seconds">5</span> seconds');
					var closeDialog = setInterval(closeTimerDialogBox,1000);
					setTimeout( function(){
						clearInterval(closeDialog);
						 top.$('#patient_form').modal('show')
					},5050);
					
				}
			},
			
	});
	
	
}

function getUrlParam( name, url ) {
  if (!url) url = window.location.pathname+window.location.search;
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( url );
  return results == null ? null : results[1];
}

function showDialogBox(HTitle,BodyText,btnYesTitle, btnYesHref,btnNoTitle, btnNoHref,BoxID)
{
		if(typeof HTitle === 'undefined' || HTitle === '' )						HTitle = 'Sx EMR Alerts';
		if(typeof BodyText === 'undefined' || BodyText === '' )			BodyText = '';
		if(typeof btnYesTitle === 'undefined' || btnYesTitle === '' )	btnYesTitle = false;
		if(typeof btnNoTitle === 'undefined' || btnNoTitle === '' )		btnNoTitle = false;
		if(typeof BoxID === 'undefined' || BoxID === '' )						BoxID = 'dialogBoxScreen';
					
		var	html	=	'';
				
				html	+=	'<div class="panel panel-default bg_panel_sum dialogBox" >';
				
				html	+=	'<div class=" haed_p_clickable dialogHead " >';
				html	+=	'<h3 class="panel-title rob" >' + HTitle + '</h3>';
				html	+=	'</div>'; // panel-bod
				
				html	+=	'<div class=" text-center dialogBody"  >';
				html	+=	BodyText;
				
				if(btnYesTitle)
					html	+=	'<a class="btn btn-info"   href="' + btnYesHref + '">' + btnYesTitle + ' </a>&nbsp;';
				
				if(btnNoTitle)	
					html	+=	'<a class="btn btn-danger" href="' + btnNoHref + '">' + btnNoTitle + ' </a>';
				
				html	+=	'</div>';
				
				html	+=	'</div>';
				
				if(top.document.getElementById(BoxID))
				{
						top.document.getElementById(BoxID).innerHTML = html;	
						top.document.getElementById(BoxID).style.display = 'block';
				}
				
		}
		
function closeDialogBox(){
		var BoxID	=	'dialogBoxScreen';
		top.document.getElementById(BoxID).innerHTML = '';
		top.document.getElementById(BoxID).style.display = 'none';
}
			
function closeTimerDialogBox(){
		var sec_start = parseInt(top.document.getElementById("close_seconds").innerHTML);
		var sec_time = sec_start - 1;
		top.document.getElementById("close_seconds").innerHTML = sec_time;
		
		if(sec_time<1)
		{
			var BoxID	=	'dialogBoxScreen';
			clearInterval(top.close_dialog); 
			top.document.getElementById(BoxID).innerHTML = '';
			top.document.getElementById(BoxID).style.display = 'none';
		}
	}

function enable_chk_unchk_admin(chbx_food_yes_id,chbx_food_no_id,txtarea_id) {
	if(document.getElementById(chbx_food_no_id).checked==true) {
		
		document.getElementById(txtarea_id).disabled=true;
		//document.getElementById(acl_number_id).disabled=true;
		document.getElementById(txtarea_id).value = '';
		
	}else if(document.getElementById(chbx_food_yes_id).checked==true) {
		
		document.getElementById(txtarea_id).disabled=false;
		//document.getElementById(acl_number_id).disabled=false;
		if(document.getElementById('defaultListFoodTake'))
		{
			var D = document.getElementById('defaultListFoodTake').value;
			if(document.getElementById(txtarea_id).value == '')
			{
				document.getElementById(txtarea_id).value = D;
			}
		}
		
	}else {
		
		document.getElementById(txtarea_id).disabled=true;
		document.getElementById(txtarea_id).value = '';
		//document.getElementById(acl_number_id).disabled=true;

	}
}

function disp_new_admin(field_name,elem_id) {
	
	if ($(field_name).is(":checked")) {
		$('#'+elem_id).show('fast').removeClass('hide');
		//$("a[data-target='#"+elem_id+"'] > span").removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
	}
	else{
		$('#'+elem_id).hide('fast').addClass('hide');;
		//$("a[data-target='#"+elem_id+"'] > span").removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
	}
}

function disp_none_new_admin(field_name,elem_id) {
	
	if(!$('#'+elem_id).hasClass('hide')){
		$('#'+elem_id).hide('fast').addClass('hide');
		//$("a[data-target='#"+elem_id+"'] > span").removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
	}
}

//CLICK ON YES ON CHECKING THE SUB OPTION DONE BY MAMTA
function checkyes_admin(id,id2,id3)
{
  if(document.getElementById(id2).checked==true)
  {
  document.getElementById(id).checked=true;
  document.getElementById(id3).checked=false;
  }
  else if(document.getElementById(id2).checked==false)
  {
  document.getElementById(id).checked=false;
  document.getElementById(id3).checked=true;
  }
}

function disp_hide_id_admin(list_id,textarea_id) {
		//alert(document.getElementById(list_id).value);
		if(document.getElementById(list_id).value=='other') {
			document.getElementById(textarea_id).style.display="block";
		} else {
			document.getElementById(textarea_id).style.display="none";
		}
}

function showPreCommentsFnNewAdmin(name1, name2, c, posLeft, posTop){	
	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*20;
	}
	//alert(t);
	document.getElementById("AdminPreCommentsDiv").style.display = 'inline-block';
	document.getElementById("AdminPreCommentsDiv").style.left = posLeft+'px';
	document.getElementById("AdminPreCommentsDiv").style.top = t*1+parseInt(posTop)+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
}

function showNourishmentKindAdmin(name1, name2, c, posLeft, posTop){	

	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*21;
	}
	document.getElementById("AdminNourishKindDiv").style.display = 'inline-block';
	document.getElementById("AdminNourishKindDiv").style.left = posLeft+'px';
	document.getElementById("AdminNourishKindDiv").style.top = t*1+parseInt(posTop)+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
}

function showRecoveryCommentsFnAdmin(name1, name2, c, posLeft, posTop){	

	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*20;
	}
	document.getElementById("AdminRecoveryDiv").style.display = 'inline-block';
	document.getElementById("AdminRecoveryDiv").style.left = posLeft+'px';
	document.getElementById("AdminRecoveryDiv").style.top = t*1+parseInt(posTop)+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
}

function showPostSiteFnAdmin(name1, name2, c, posLeft, posTop){	

	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*21;
	}
	
	document.getElementById("AdminPostSiteDiv").style.display = 'inline-block';
	document.getElementById("AdminPostSiteDiv").style.left = posLeft+'px';
	document.getElementById("AdminPostSiteDiv").style.top = t+parseInt(posTop)+'px';
	//alert(t+parseInt(posTop));
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
}

function closeAdminPopupAssist(objCloseId){
		if(document.getElementById(objCloseId)) {
			if(document.getElementById(objCloseId).style.display != "none"){
				document.getElementById(objCloseId).style.display = "none"; 
			}
		} 
		if(top.document.getElementById(objCloseId)) {
			if(top.document.getElementById(objCloseId).style.display != "none"){
				top.document.getElementById(objCloseId).style.display = "none"; 
			}
		}
		
	}

var tOutAdminPopup = '';
function closeAdminPopup(divId){
	
	if( typeof divId == 'undefined') return false;
	tOutAdminPopup = setTimeout("closeAdminPopupAssist('"+divId+"')", 500);
}
function stopCloseAdminPopup() {
	clearTimeout(tOutAdminPopup);
}
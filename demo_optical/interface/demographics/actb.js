var actb_prtDiv="";
function actb(obj,ca,sfw,fclr,objHidden,objHiddenVal,cptPOS,objHidden2,objHiddenVal2,objHidden3,objHiddenVal3){
	/* ---- Public Variables ---- */
	this.actb_timeOut = -1; // Autocomplete Timeout in ms (-1: autocomplete never time out)
	this.actb_lim = 10;    // Number of elements autocomplete can show (-1: no limit)
	this.actb_firstText = true; // false should the auto complete be limited to the beginning of keyword?
	this.actb_mouse = true; // Enable Mouse Support
	this.actb_delimiter = new Array(' ',';',',','\r\n','\n');  // Delimiter for multiple autocomplete. Set it to empty array for single autocomplete
	if(cptPOS == ",")
	this.actb_delimiter = new Array(' ',';','\r\n','\n');  // Delimiter for multiple autocomplete. Set it to empty array for single autocomplete
	this.actb_startcheck = 1; // Show widget only after this number of characters is typed in.
	/* ---- Public Variables ---- */

	/* --- Styles --- */
	this.actb_bgColor = '#888888';
	this.actb_textColor = '#FFFFFF';
	this.actb_hColor = '#000000';
	this.actb_fFamily = 'Verdana';
	this.actb_fSize = '11px';
	this.actb_hStyle = 'text-decoration:underline;font-weight="bold"';
	this.actb_bgColorHdr = "#F8F2E5";
	this.actb_textColorHdr = "#D75A20";	
	/* --- Styles --- */

	/* ---- Private Variables ---- */
	var actb_delimwords = new Array();
	var actb_cdelimword = 0;
	var actb_delimchar = new Array();
	var actb_display = false;
	var actb_pos = 0;
	var actb_total = 0;
	var actb_curr = null;
	var actb_curr_hidden = null;
	var actb_curr_hidden2 = null;
	var actb_curr_hidden3 = null;
	var actb_rangeu = 0;
	var actb_ranged = 0;
	var actb_bool = new Array();
	var actb_pre = 0;
	var actb_toid;
	var actb_tomake = false;
	var actb_getpre = "";
	var actb_mouse_on_list = 1;
	var actb_kwcount = 0;
	var actb_caretmove = false;
	this.actb_keywords = new Array();		
	this.actb_uniqueOneWordArr = new Array(); //Test
	this.actb_similarOneWordArr = new Array(); //Test
	this.actb_uniWordArr = new Array(); //Test
	this.actb_penter = actb_penter;
	/* ---- Private Variables---- */
	this.actb_keywords = ca;
	var actb_self = this;
	this.singleFirstWord=(typeof sfw == "undefined" || sfw == "") ? 0 : sfw;
	//this.clrElemVal = (typeof fclr == "undefined" || fclr == "") ? 0 : fclr; //onblur hack
	actb_curr = obj;	
	
	addEvent(actb_curr,"focus",actb_setup);	
	
	actb_curr_hidden = objHidden;
	actb_curr_hidden2 = objHidden2;
	actb_curr_hidden3 = objHidden3;
	function actb_setup(){
		addEvent(document,"keydown",actb_checkkey);
		addEvent(actb_curr,"blur",actb_clear);
		addEvent(document,"keypress",actb_keypress);
	}

	function actb_clear(evt){
		if (!evt) evt = event;
		removeEvent(document,"keydown",actb_checkkey);
		removeEvent(actb_curr,"blur",actb_clear);
		removeEvent(document,"keypress",actb_keypress);		
		//actb_removedisp();		
	}
	function actb_parse(n){
		if (actb_self.actb_delimiter.length > 0){
			var t = actb_delimwords[actb_cdelimword].trim().addslashes();
			var plen = actb_delimwords[actb_cdelimword].trim().length;
		}else{
			var t = actb_curr.value.addslashes();
			var plen = actb_curr.value.length;
		}
		var tobuild = '';
		var i;

		if (actb_self.actb_firstText){
			var re = new RegExp("^" + t, "i");
		}else{
			var re = new RegExp(t, "i");
		}
		var p = n.search(re);
				
		for (i=0;i<p;i++){
			tobuild += n.substr(i,1);
		}
		tobuild += "<font style='"+(actb_self.actb_hStyle)+"'>"
		for (i=p;i<plen+p;i++){
			tobuild += n.substr(i,1);
		}
		tobuild += "</font>";
			for (i=plen+p;i<n.length;i++){
			tobuild += n.substr(i,1);
		}
		return tobuild;
	}
	
	//Test
	/*
	function setTypeAheadLeftPosition(objElem,divA)
	{
		var leftX = objElem.getBoundingClientRect().left;
		var thisScrollLeft = document.body.scrollLeft;
		var thisClientWidth = document.body.clientWidth;
		var lefter = leftX;
		var diffHgt = diffWidth = 0;
		var diffWidth = (lefter > thisClientWidth) ? lefter-thisClientWidth : 0 ; 	
		window.status = "leftX:"+leftX+",thisScrollLeft:"+thisScrollLeft+
						",thisClientWidth:"+thisClientWidth+",diffWidth:"+diffWidth+
						"divA:"+divA.offsetLeft;		
		return leftX + thisScrollLeft - diffWidth;
	}	
	*/
	function setTypeAheadTopPosition(objElem,divA)
	{
		var divOffsetTop = divA.offsetTop;	
		var divOffsetHeight = divA.offsetHeight;
		var bottomY = objElem.getBoundingClientRect().bottom;
		if(typeof(actb_prtDiv)!="undefined" && actb_prtDiv!=""){
			var o = document.getElementById(actb_prtDiv);
			//var thisScrollTop = o.scrollTop;
			var thisScrollTop = 0;
			var thisClientHeight = o.clientHeight;
		}else{
			var thisScrollTop = document.body.scrollTop;   
			var thisClientHeight = document.body.clientHeight;
		}
		var toper = bottomY+divOffsetHeight;		
		var diffHgt = diffWidth = 0;
		var diffHgt = (toper > thisClientHeight) ? toper-thisClientHeight : 0 ;
		/*
		var jqP = $(objElem).position();
		window.status = "objName:"+objElem.name+",bottomY:"+bottomY+",thisScrollTop:"+thisScrollTop+
						",thisClientHeight:"+thisClientHeight+",diffHgt:"+diffHgt+
						",divATop:"+divA.offsetTop+",divAHeight:"+divA.offsetHeight+",Return:"+eval(bottomY + thisScrollTop - diffHgt)+
						",Jq left: "+jqP.left+",Jq top:"+jqP.top;
		*/
		return bottomY + thisScrollTop - diffHgt;
	}
	
	/*
	function makeShowDiv()
	{
		var dv = document.createElement('div');	
		//var ifrm = document.createElement('iframe');			
		dv.style.position = "absolute";
		dv.style.left = "0px";
		dv.style.top = "0px";		
		window.status = "dvleft:"+dv.style.left+",top:"+dv.style.top+
						",Width:"+dv.style.width+",height:"+dv.style.height;						
		/*
		ifrm.style.position = "relative";
		ifrm.style.left = "0px";
		ifrm.style.top = "0px";
		ifrm.style.width = "100%";
		ifrm.style.height = "100%";
		ifrm.style.height = "100%";
		ifrm.setAttribute("frameborder","0");
		ifrm.setAttribute("src","../main/ss.html");		
		dv.appendChild(ifrm);		
		*-/
		dv.appendChild(a);
		document.body.appendChild(dv);
	}
	*/
	
	//Test
	
	function actb_generate(){
		if (document.getElementById('tat_table')){ 
			actb_display = false;
			document.body.removeChild(document.getElementById('tat_table')); 
			emptyUniqueWordArr();
		} 
		if (actb_kwcount == 0){
			actb_display = false;
			return;
		}
		a = document.createElement('table');
		a.cellSpacing='1px';
		a.cellPadding='2px';
		a.style.position='absolute';		
		a.style.zIndex='1000';
		//Test
		a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";		
		a.style.left = curLeft(actb_curr) + "px"; //to Rajan
		//setTypeAheadLeftPosition(actb_curr,a);
		//Test		
		a.style.backgroundColor=actb_self.actb_bgColor;
		a.id = 'tat_table';
		document.body.appendChild(a);
		var i;
		var first = true;
		var j = 1;
		if (actb_self.actb_mouse){
			a.onmouseout = actb_table_unfocus;
			a.onmouseover = actb_table_focus;
		}
		var counter = 0;
		for (i=0;i<actb_self.actb_keywords.length;i++){
			if (actb_bool[i]){
				counter++;
				r = a.insertRow(-1);
				if (first && !actb_tomake){
					r.style.backgroundColor = actb_self.actb_hColor;
					first = false;
					actb_pos = counter;
				}else if(actb_pre == i){
					r.style.backgroundColor = actb_self.actb_hColor;
					first = false;
					actb_pos = counter;
				}else{
					r.style.backgroundColor = actb_self.actb_bgColor;
				}
				r.id = 'tat_tr'+(j);
				c = r.insertCell(-1);
				c.style.color = actb_self.actb_textColor;
				c.style.fontFamily = actb_self.actb_fFamily;
				c.style.fontSize = actb_self.actb_fSize;
				c.innerHTML = actb_parse(actb_self.actb_keywords[i]);
				c.id = 'tat_td'+(j);
				c.setAttribute('pos',j);
				if (actb_self.actb_mouse){
					c.style.cursor = 'pointer';
					c.onclick=actb_mouseclick;
					c.onmouseover = actb_table_highlight;
				}
				j++;
			}
			if (j - 1 == actb_self.actb_lim && j < actb_total){
				r = a.insertRow(-1);
				r.style.backgroundColor = actb_self.actb_bgColorHdr;
				c = r.insertCell(-1);
				c.style.color = actb_self.actb_textColorHdr;
				c.style.fontFamily = 'arial Black';
				c.style.fontSize = actb_self.actb_fSize;
				c.style.fontWeight = 'bolder';
				c.align='center';
				replaceHTML(c,'\\/');
				if (actb_self.actb_mouse){
					c.style.cursor = 'pointer';
					c.onclick = actb_mouse_down;
				}
				break;
			}
		}
		actb_rangeu = 1;
		actb_ranged = j-1;
		actb_display = true;
		//Test
		if(cptPOS!="pos"){			//	for set position in Billing - > CPT Codes
			a.style.top = setTypeAheadTopPosition(actb_curr,a)+"px";
		}
		//makeShowDiv();
		//Test
		if (actb_pos <= 0) actb_pos = 1;
	}
	function actb_remake(){
		document.body.removeChild(document.getElementById('tat_table'));
		emptyUniqueWordArr();
		a = document.createElement('table');
		a.cellSpacing='1px';
		a.cellPadding='2px';
		a.style.zIndex='1000';
		a.style.position='absolute';
		a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
		a.style.left = curLeft(actb_curr) + "px";
		a.style.backgroundColor=actb_self.actb_bgColor;
		a.id = 'tat_table';
		if (actb_self.actb_mouse){
			a.onmouseout= actb_table_unfocus;
			a.onmouseover=actb_table_focus;
		}
		document.body.appendChild(a);

		//Test
		a.style.top = setTypeAheadTopPosition(actb_curr,a)+"px";
		//Test

		var i;
		var first = true;
		var j = 1;
		if (actb_rangeu > 1){
			r = a.insertRow(-1);
			r.style.backgroundColor = actb_self.actb_bgColorHdr;
			c = r.insertCell(-1);
			c.style.color = actb_self.actb_textColorHdr;
			c.style.fontFamily = 'arial Black';
			c.style.fontSize = actb_self.actb_fSize;
			c.style.fontWeight = 'bolder';
			c.align='center';
			replaceHTML(c,'/\\');
			if (actb_self.actb_mouse){
				c.style.cursor = 'pointer';
				c.onclick = actb_mouse_up;
			}
		}
		for (i=0;i<actb_self.actb_keywords.length;i++){
			if (actb_bool[i]){
				if (j >= actb_rangeu && j <= actb_ranged){
					r = a.insertRow(-1);
					r.style.backgroundColor = actb_self.actb_bgColor;
					r.id = 'tat_tr'+(j);
					c = r.insertCell(-1);
					c.style.color = actb_self.actb_textColor;
					c.style.fontFamily = actb_self.actb_fFamily;
					c.style.fontSize = actb_self.actb_fSize;
					c.innerHTML = actb_parse(actb_self.actb_keywords[i]);
					c.id = 'tat_td'+(j);
					c.setAttribute('pos',j);
					if (actb_self.actb_mouse){
						c.style.cursor = 'pointer';
						c.onclick=actb_mouseclick;
						c.onmouseover = actb_table_highlight;
					}
					j++;
				}else{
					j++;
				}
			}
			if (j > actb_ranged) break;
		}
		if (j-1 < actb_total){
			r = a.insertRow(-1);
			r.style.backgroundColor = actb_self.actb_bgColorHdr;
			c = r.insertCell(-1);
			c.style.color = actb_self.actb_textColorHdr;
			c.style.fontFamily = 'arial Black';
			c.style.fontSize = actb_self.actb_fSize;
			c.align='center';
			c.style.fontWeight = 'bolder';
			replaceHTML(c,'\\/');
			if (actb_self.actb_mouse){
				c.style.cursor = 'pointer';
				c.onclick = actb_mouse_down;
			}
		}
	}
	function actb_goup(){
		if (!actb_display) return;
		if (actb_pos == 1) return;
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
		actb_pos--;
		if (actb_pos < actb_rangeu) actb_moveup();
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_godown(){
		if (!actb_display) return;
		if (actb_pos == actb_total) return;
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
		actb_pos++;
		if (actb_pos > actb_ranged) actb_movedown();
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_movedown(){
		actb_rangeu++;
		actb_ranged++;
		actb_remake();
	}
	function actb_moveup(){
		actb_rangeu--;
		actb_ranged--;
		actb_remake();
	}

	/* Mouse */
	function actb_mouse_down(){
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
		actb_pos++;
		actb_movedown();
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
		if(actb_curr) actb_curr.focus();
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_mouse_up(evt){
		if (!evt) evt = event;
		if (evt.stopPropagation){
			evt.stopPropagation();
		}else{
			evt.cancelBubble = true;
		}
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
		actb_pos--;
		actb_moveup();
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
		if(actb_curr) actb_curr.focus();
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_mouseclick(evt){
		if (!evt) evt = event;
		if (!actb_display) return;
		actb_mouse_on_list = 0;
		actb_pos = this.getAttribute('pos');
		actb_penter();
	}
	function actb_table_focus(){
		actb_mouse_on_list = 1;
		
		/*//Test stopped
		if(actb_self.clrElemVal == 1)
		actb_curr.value = actb_curr.value.substring(0,1);
		//Test*/

	}
	function actb_table_unfocus(){
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_table_highlight(){
		actb_mouse_on_list = 1;
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_bgColor;
		actb_pos = this.getAttribute('pos');
		while (actb_pos < actb_rangeu) actb_moveup();
		while (actb_pos > actb_ranged) actb_movedown();
		document.getElementById('tat_tr'+actb_pos).style.backgroundColor = actb_self.actb_hColor;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
	}
	/* ---- */

	function actb_insertword(a,hiddenVal,hiddenVal2,hiddenVal3){
		if (actb_self.actb_delimiter.length > 0){
			str = '';
			l=0;
			for (i=0;i<actb_delimwords.length;i++){
				if (actb_cdelimword == i){
					prespace = postspace = '';
					gotbreak = false;
					for (j=0;j<actb_delimwords[i].length;++j){
						if (actb_delimwords[i].charAt(j) != ' '){
							gotbreak = true;
							break;
						}
						prespace += ' ';
					}
					for (j=actb_delimwords[i].length-1;j>=0;--j){
						if (actb_delimwords[i].charAt(j) != ' ') break;
						postspace += ' ';
					}
					str += prespace;
					str += a;
					l = str.length;
					if (gotbreak) str += postspace;
				}else{
					str += actb_delimwords[i];
				}
				if (i != actb_delimwords.length - 1){
					str += actb_delimchar[i];
				}
			}
			
			/*if(obj.id == 'SclTypeOD' || obj.id == 'SclTypeOS'){
				var tempVal = str.split("-");
				str = tempVal[1] + "-" + tempVal[0] + "-" + tempVal[2];
			}*/
			
			actb_curr.value = str;
			if(actb_curr_hidden && hiddenVal) {
				actb_curr_hidden.value = hiddenVal;
			}
			if(actb_curr_hidden2 && hiddenVal2) {
				actb_curr_hidden2.value = hiddenVal2;
			}
			if(actb_curr_hidden3 && hiddenVal3) {
				actb_curr_hidden3.value = hiddenVal3;
			}
			//Test
				if(actb_curr) 
				{
					setCaret(actb_curr,l);
				}
			//Test
			
		}else{
			actb_curr.value = a;
		}
		actb_mouse_on_list = 0;
		actb_removedisp();
		//Test
		if(actb_curr)
		{
			fireObjectFunctions(actb_curr);
		}
		//Test
	}
	function actb_penter(){
		if (!actb_display) return;
		actb_display = false;
		var word = '';
		var c = 0;
		var hiddVal = hiddVal2  = hiddVal3 = "";
		for (var i=0;i<=actb_self.actb_keywords.length;i++){
			if (actb_bool[i]) c++;
			if (c == actb_pos){
				word = actb_self.actb_keywords[i];//alert(ca[i]);
				if(objHiddenVal) {
					hiddVal = objHiddenVal[i];
				}
				if(objHiddenVal2) {
					hiddVal2 = objHiddenVal2[i];
				}
				if(objHiddenVal3) {
					hiddVal3 = objHiddenVal3[i];
				}
				break;
			}
		}
		actb_insertword(word,hiddVal,hiddVal2,hiddVal3);
		l = getCaretStart(actb_curr);
	}
	function actb_removedisp(){		
		if (actb_mouse_on_list==0){			
			actb_display = 0;
			if (document.getElementById('tat_table')){ document.body.removeChild(document.getElementById('tat_table')); }			
			if (actb_toid) clearTimeout(actb_toid);
			emptyUniqueWordArr();
		}
	}
	function actb_keypress(e){
		if (actb_caretmove) stopEvent(e);
		return !actb_caretmove;
	}
	function actb_checkkey(evt){
		if (!evt) evt = event;
		a = evt.keyCode;
		caret_pos_start = getCaretStart(actb_curr);
		actb_caretmove = 0;
		switch (a){
			///*
			case 38:
				actb_goup();
				actb_caretmove = 1;
				//return false;
				break;
			case 40:
				actb_godown();
				actb_caretmove = 1;
				//return false;
				break;
			//*/
			//Code
				case 88:					
					return true; //// Return true;
				break;
				case 9:	//Tab		
					if (document.getElementById('tat_table')){ document.body.removeChild(document.getElementById('tat_table')); }	 // Hide Div
					emptyUniqueWordArr();
					return true;				
				break;
			//Code	
			case 13:
				if (actb_display){
					actb_caretmove = 1;
					actb_penter();
					return false;
				}else{
					return true;
				}
				break;
			default:
				setTimeout(function(){actb_tocomplete(a)},50);
				break;
		}
	}

	function actb_getFirstWord(b){
		var ptrn = "^\\b([\\w]+)\\b";
		var reg = new RegExp(ptrn,"g");
		var match = b.match(reg);		
		return (match) ? match : "" ;
	}
	function actb_arrIndexOf(chk,arrchk,chkInit){
		if(arrchk.length > 0){
			if(chkInit == 1){
				for(var x in arrchk){
					if(arrchk[x].toString() == chk.toString()){				
						return x;
						break;
					}else if(""+arrchk[x].indexOf(""+chk+" (") != -1){				
						return x;
						break;
					}
				}
			
			}else{
				for(var x in arrchk){
					if(arrchk[x].toString() == chk.toString()){				
						return x;
						break;
					}
				}
			}
		}
		return -1;
	}
	
	function emptyUniqueWordArr(){
		actb_self.actb_uniqueOneWordArr.length = 0;
		actb_self.actb_similarOneWordArr.length = 0;
		actb_self.actb_uniWordArr.length=0;
	}

	function actb_tocomplete(kc){
		if (kc == 38 || kc == 40 || kc == 13) return;
		//if (kc == 13) return;
		var i;
		if (actb_display){ 
			var word = 0;
			var c = 0;
			for (var i=0;i<=actb_self.actb_keywords.length;i++){
				if (actb_bool[i]) c++;
				if (c == actb_pos){
					word = i;
					break;
				}
			}
			actb_pre = word;
		}else{ actb_pre = -1};
		
		if (actb_curr.value == ''){
			actb_mouse_on_list = 0;
			actb_removedisp();
			return;
		}
		if (actb_self.actb_delimiter.length > 0){
			caret_pos_start = getCaretStart(actb_curr);
			caret_pos_end = getCaretEnd(actb_curr);
			
			delim_split = '';
			for (i=0;i<actb_self.actb_delimiter.length;i++){
				delim_split += actb_self.actb_delimiter[i];
			}
			delim_split = delim_split.addslashes();
			delim_split_rx = new RegExp("(["+delim_split+"])");
			c = 0;
			actb_delimwords = new Array();
			actb_delimwords[0] = '';
			for (i=0,j=actb_curr.value.length;i<actb_curr.value.length;i++,j--){
				if (actb_curr.value.substr(i,j).search(delim_split_rx) == 0){
					ma = actb_curr.value.substr(i,j).match(delim_split_rx);
					actb_delimchar[c] = ma[1];
					c++;
					actb_delimwords[c] = '';
				}else{
					actb_delimwords[c] += actb_curr.value.charAt(i);
				}
			}

			var l = 0;
			actb_cdelimword = -1;
			for (i=0;i<actb_delimwords.length;i++){
				if (caret_pos_end >= l && caret_pos_end <= l + actb_delimwords[i].length){
					actb_cdelimword = i;
				}
				l+=actb_delimwords[i].length + 1;
			}
			var ot = actb_delimwords[actb_cdelimword].trim(); 
			var t = actb_delimwords[actb_cdelimword].addslashes().trim();
		}else{
			var ot = actb_curr.value;
			var t = actb_curr.value.addslashes();
		}
		if (ot.length == 0){
			actb_mouse_on_list = 0;
			actb_removedisp();
		}
		if (ot.length < actb_self.actb_startcheck) return this;
		if (actb_self.actb_firstText){
			var re = new RegExp("^" + t, "i");
		}else{
			var re = new RegExp(t, "i");
		}
		
		emptyUniqueWordArr();
		actb_total = 0;
		actb_tomake = false;
		actb_kwcount = 0;
		for (i=0;i<actb_self.actb_keywords.length;i++){
			actb_bool[i] = false;
			if (re.test(actb_self.actb_keywords[i])){
				
				//Test
				if(actb_self.singleFirstWord == 1){
					var tmpFword = actb_getFirstWord(actb_self.actb_keywords[i]);				
					if(tmpFword != ""){					
						var tmpIndx = actb_arrIndexOf(tmpFword,actb_self.actb_uniqueOneWordArr);
						if( (tmpIndx == -1) ){
							actb_self.actb_uniqueOneWordArr.push(tmpFword);
							actb_self.actb_uniWordArr.push(actb_self.actb_keywords[i]);
						}else{
							/*
							//Now strictly one first word entry will ve shown
							actb_self.actb_similarOneWordArr.push(tmpFword);
							continue;
							*/
							//if value is duplicate do not display
							var tmpIndx = actb_arrIndexOf(actb_self.actb_keywords[i],actb_self.actb_uniWordArr,1);
							///alert(actb_self.actb_keywords[i]+"\n"+actb_self.actb_uniWordArr+"\n"+tmpIndx);
							if(tmpIndx != -1){
								continue;
							}else{
								
								//Add Uniqvalus
								actb_self.actb_uniWordArr.push(actb_self.actb_keywords[i]);								
								
								/* This will increase similar values as user go toward a words 
								if(actb_self.actb_uniqueOneWordArr.length == 1){
									actb_self.actb_similarOneWordArr.push(tmpFword);
								}else{								
									continue;
								}
								//*/
								
								// This will show first unique word 's related all unique word + single word for other unique words
								var a = ""+actb_self.actb_uniqueOneWordArr[0];
								a = a.toLowerCase();
								var b = tmpFword.toString().toLowerCase();
								if(a == b){
									actb_self.actb_similarOneWordArr.push(tmpFword);
								}else{									
									continue;
								}								
							}
						}					
					}
				}
				//Test
				
				actb_total++;
				actb_bool[i] = true;
				actb_kwcount++;
				if (actb_pre == i) actb_tomake = true;
			}
		}
		
		if((actb_self.singleFirstWord == 1) && (actb_self.actb_similarOneWordArr.length > 0)){
			actb_self.actb_uniqueOneWordArr.concat(actb_self.actb_similarOneWordArr);
		}
		
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
		actb_generate();
	}
	return this;
}

/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
/*
File: buttons.js
Coded in PHP7
Purpose: This file sets buttons in application when page loads.
Access Type : Include file
*/
//-- buttons.js --
function btn_htm(n,i,f,v){
	if(n == "span"){ //for space		
		return "<span style=\"padding-left:"+v+"px;\"></span>";
	}else{
		var noFocs="";
		if(v=="<< Previous" || v=="Next >>"){noFocs=" onfocus='this.blur();' "}
		return "<input type=\"button\" class=\"rob_btn btn-search btn_custom\" "+
				"name=\""+n+"\" id=\""+i+"\" onClick=\""+f+"\" "+noFocs+
				"value=\""+v+"\"  />&nbsp;&nbsp;";
	}
}

function btn_show(id,ar){	
	var str="";
	var n="",v="",fn="",l=0,d="";
	switch(id){
	
		case "financial":
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

	top.document.getElementById("page_buttons").innerHTML = '<span>'+str+'</span>';

}

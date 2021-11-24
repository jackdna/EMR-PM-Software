
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019


function startTime()
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request");
		return false;
	}
	var url="user_agent.php"
	url=url+"?jsServerTimeRequest=fullDtTime&pste="+Math.random();
	xmlHttp.onreadystatechange=function() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		   if(document.getElementById('dt_tm')) {
				document.getElementById('dt_tm').innerHTML=xmlHttp.responseText;
		   }
		} 
	};
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
	t=setTimeout('startTime()',1000);
}


function checkTime(i)
{
	if (i<10)
	  {
	  i="0" + i;
	  }
	return i;
}

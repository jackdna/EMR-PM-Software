<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>imwemr-IBRA</title> 

<script src="../js/jquery.min.1.12.4.js"></script>
<script>
	function test(){
		/*
		var url = $("#url").val();
		var post = $("#post").prop("checked") ? "POST" : "GET";
		var input = jQuery.parseJSON($("#input").val()); 
		var headers = jQuery.parseJSON($("#headers").val());
		
		alert(url+'\n'+headers+'\n'+post+'\n'+input);
		*/
		//return;
		
		//https://www.zubisoft.eu/api/tokens
		
		$.ajax({
		    url: 'https://www.zubisoft.eu/api/tokens',
		    headers: {
			"Content-type": "application/json", 
			"X-Public-Key":"ioe5_780p3",
			"X-Signed-Request-Hash":"ZTc4MDVkYzBhMDdiMDg5NGYxMzM4MTU4ZTAyODM4NGUwNWZlZWI2NTRmMzMxMDU5NDA0MWM3MWJlMWEyMGU5ZQ=="
		    },
		    method: 'POST',
		    dataType: 'json',
		    data: {"userId":"kj9752rtzdsx","action":"created"},
		    success: function(data){
		      console.log('succes: '+data);
		    }
		  });
		
		
		/*
		$.ajax({
		    url: url,
		    headers: headers,
		    method: post,
		    dataType: 'json',
		    data: input,
		    success: function(data){
		      console.log('succes: '+data);
		      $("#output").val(data);
		    }
		  });
		*/
		
		/*
		
		if(post){
		$.post(''+url, param, function(d){  $("#output").val(d);  });
		}else{
		url = url+input;
		$.get(''+url, function(d){  $("#output").val(d);  });	
		}
		*/	
	}
	
</script>

</head>
<body>

	<H1>IBRA</H1>
<br/>

URL:	<textarea id="url" cols="100"></textarea><br/>
POST: <input type="checkbox" id="post" value="1"><br/>
HEADER:	<textarea id="headers" rows="5" cols="100"></textarea><br/>
INPUT:	<textarea id="input" rows="5" cols="100"></textarea><br/>
OUTPUT:	<textarea id="output" rows="10" cols="100"></textarea><br/>
<button onclick="test()">Test</button>

<?php 
$requestType = 'POST';
$content = '';
$privateKey = 'kuz32n87';
$apiHash = base64_encode(hash_hmac('SHA256',$requestType . "\n" . $content, $privateKey));  
echo $apiHash;
?>




<form name="frm" method="post" action="https://www.zubisoft.eu/api/tokens">
<input type="hidden" name="Content-type" value="application/json">
<input type="hidden" name="X-Public-Key" value="ioe5_780p3">
<input type="hidden" name="X-Signed-Request-Hash" value="ZTc4MDVkYzBhMDdiMDg5NGYxMzM4MTU4ZTAyODM4NGUwNWZlZWI2NTRmMzMxMDU5NDA0MWM3MWJlMWEyMGU5ZQ==">

<input type="hidden" name="userId" value="kj9752rtzdsx">
<input type="hidden" name="action" value="created">
<input type="submit" name="bsubmit" value="go">

</form>













</body>
</html>
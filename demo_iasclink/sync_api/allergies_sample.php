<?php
include_once("api_common.php");
$url_new = $httpHost."/".$iolinkDirectoryName."/sync_api/allergies_endpoint.php";
?>
<script>
//START CODE TO ENTER JSON DATA
	var d = {
				"Allergies": [    
								{
									"Date": "2019-03-20",
									"ExternalId": "123456",
									"AllergyCode": {          
														"Code": "QW",          
														"Description": "Ampicillin",          
														"CodeSystemName": "Test System",          
														"CodeSystem": "Test Code"        
													},           
									"Reaction": "RC",
									"ReactionDesc": "Skin Problem",
									"ReactionCodeSystem": "Test React Code",
									"ReactionCodeSystemName": "Test React Code Name",
									"SeverityCode": "Test Sev Code",
									"SeverityDescription": "Test Sev Desc",
									"SeverityCodeSystem": "Test Sev Code",
									"SeverityCodeSystemName": "Test Sev Code Name",
									"Status": "Test Status",                
									"IsActive" : "true"    
								},
							],
	
			};

//END CODE TO ENTER JSON DATA

$(function(){
	var url_new = "<?php echo $url_new;?>";
	$.ajax({
		type:'POST',
		url:url_new,
		data:{api_allergy_data: JSON.stringify(d) },
		//contentType: "application/json",
		//dataType: 'json',
		beforeSend: function(x) {
            if (x && x.overrideMimeType) {
              x.overrideMimeType("application/j-son;charset=UTF-8");
            }
          },
		success:function(r){
			//alert(r);
			document.write(r);
		}
	})
});
</script>

<?php
include_once("api_common.php");
$url_new = $httpHost."/".$iolinkDirectoryName."/sync_api/medications_endpoint.php";
?>
<script>
//START CODE TO ENTER JSON DATA
	var d = {
  "ResourceType": "MedicationOrder",
  "IsSource": "Test IsSource",
  "ReferenceNumber": "Test  VndrId.PracId.FacId.PhyId.PatId.JsontypeId.Primarykey",
  "PatientAccountNumber": "Test PatientAccountNumber",
  "Medication": [
    {
      "ExternalId": "654321",
      "ECNPI": "Test ECNPI",
      "ECTIN": "Test ECTIN",
      "DrugName": "Actonel",
      "DrugCode": "Test DrugCode",
      "DrugCodeSystem": "Test DrugCodeSystem",
      "DrugCodeSystemName": "Test DrugCodeSystemName",
      "MedicationType": "Test MedicationType Ocular|Systemic",
      "PrescriptionType": "Test PrescriptionType  New|Refill|AND ANY OTHER",
      "Directions": "Test Directions",
      "AdditionalInstructions": "Test AdditionalInstructions",
      "Strength": "Test Strength",
      "Substitution": "Test Substitution",
      "StartDate": "2019-03-20 15:18:20",
      "EffectiveDate": "2019-03-20 15:19:20",
      "DaysSupply": "Test DaysSupply",
      "DosageQuantity": {
       "Value": "123",
       "Units": "2"
      },
      "Route": {
       "Code": "Test Code",
       "Description": "Test Description",
       "CodeSystem": "Test CodeSystem",
       "CodeSystemName": "Test CodeSystemName"
      },
      "IsPrescribed": "true",
      "EprescribedDate": "2019-03-20 15:20:20",
      "IsQueriedForDrugFormulary": "true",
      "IsCPOE": "true",
      "IsControlSubstance": "true",
      "PharmacyName": "Test PharmacyName",
      "PharmacyPhone": {
        "Type": "Test QW",
        "Number": "123"
      },
      "PharmacyAddress": {
        "Type": "Test ER",
        "AddressLine1": "Test AddressLine1",
        "AddressLine2": "Test AddressLine2",
        "City": "Test City",
        "State": "Test State",
        "Zip": "Test Zip",
        "County": "US"
      },
      "Reason": { //1.1 OReason for not performing. This is for cqm.
        "Type": "Test  Patient|Medical|Other",
        "Code": "Test Code",
        "Description": "Test Description",
        "CodeSystemName": "Test CodeSystemName",
        "CodeSystem": "Test CodeSystem"
      },
      "ReconciledDate": "2019-03-20 15:21:20",
      "Status": "Cancel",  //These Status are for Cancel Medications Delete,Remove,Cancel,Discontinue
      "DateEnd": "2019-03-20 15:22:20"
    }
  ],
  
};

//END CODE TO ENTER JSON DATA

$(function(){
	var url_new = "<?php echo $url_new;?>";
	$.ajax({
		type:'POST',
		url:url_new,
		data:{api_med_data: JSON.stringify(d) },
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

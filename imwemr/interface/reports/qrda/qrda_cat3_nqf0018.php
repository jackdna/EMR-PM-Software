<?php
require_once(dirname(__FILE__) . "/class_qrda_3.php");
$objMUR = new QRDA_3;

//START 
$ARRAY_IPOP=$ARRAY_DENOMINATOR=$ARRAY_DENOMINATOR_EXCLUSION=$ARRAY_NUMERATOR=array();
$ARRAY_NQF0018=$objMUR->getNQF0018();
$ARRAY_IPOP=$ARRAY_NQF0018["ipop"];
$ARRAY_DENOMINATOR=$ARRAY_NQF0018["denominator"];
$ARRAY_DENOMINATOR_EXCLUSION=$ARRAY_NQF0018["exclusion"];
if(count($ARRAY_DENOMINATOR_EXCLUSION)==0){
	$ARRAY_DENOMINATOR_EXCLUSION=$ARRAY_NQF0018["denominatorException"];	
}
$ARRAY_NUMERATOR=$ARRAY_NQF0018["numerator"];
//==================================IPOP===========================================//
$ARR_IPP_GENDER_COUNT=$ARR_IPP_ETHNICITY=$ARR_IPP_RACE=$ARR_IPP_PAYER=array();
$ARR_IPP_GENDER_COUNT=$objMUR->_getTotalGender($ARRAY_IPOP);
$ARR_IPP_ETHNICITY=$objMUR->_getTotalEthnicity($ARRAY_IPOP);
$ARR_IPP_RACE=$objMUR->_getTotalRace($ARRAY_IPOP);
$ARR_IPP_PAYER=$objMUR->_getTotalPayer($ARRAY_IPOP);

$measuresArr = array();
$measuresArr['IPP']['GENDER_COUNT'] = $ARR_IPP_GENDER_COUNT['count'];//'23';
$measuresArr['IPP']['SUPPLEMENT_GENDER_COUNT']["F"] = $ARR_IPP_GENDER_COUNT['female'];//'11';
$measuresArr['IPP']['SUPPLEMENT_GENDER_COUNT']["M"] = $ARR_IPP_GENDER_COUNT['male'];//12';

foreach($ARR_IPP_ETHNICITY as $ETHNICITY_VALUE_CODE =>$ETHNICITY_VALUE_COUNT){
	$measuresArr['IPP']['ETHNICITY_VALUE_COUNT'][$ETHNICITY_VALUE_CODE] = $ETHNICITY_VALUE_COUNT;//'14';
}
foreach($ARR_IPP_RACE as $RACE_VALUE_CODE =>$RACE_VALUE_COUNT){
	$measuresArr['IPP']['RACE_VALUE_COUNT'][$RACE_VALUE_CODE]=$RACE_VALUE_COUNT;
}
foreach($ARR_IPP_PAYER as $PAYER_VALUE_CODE => $PAYER_VALUE_COUNT){
	$measuresArr['IPP']['PAYER_VALUE_COUNT'][$PAYER_VALUE_CODE]=$PAYER_VALUE_COUNT;
}
//===============================================================================================//

//===============================DENOMINATOR====================================================//
$ARR_DENOMINATOR_GENDER_COUNT=$ARR_DENOMINATOR_ETHNICITY=$ARR_DENOMINATOR_RACE=$ARR_DENOMINATOR_PAYER=array();
$ARR_DENOMINATOR_GENDER_COUNT=$objMUR->_getTotalGender($ARRAY_DENOMINATOR);
$ARR_DENOMINATOR_ETHNICITY=$objMUR->_getTotalEthnicity($ARRAY_DENOMINATOR);
$ARR_DENOMINATOR_RACE=$objMUR->_getTotalRace($ARRAY_DENOMINATOR);
$ARR_DENOMINATOR_PAYER=$objMUR->_getTotalPayer($ARRAY_DENOMINATOR);

$measuresArr['DENOM']['GENDER_COUNT'] = $ARR_DENOMINATOR_GENDER_COUNT['count'];//23';
$measuresArr['DENOM']['SUPPLEMENT_GENDER_COUNT']["F"] = $ARR_DENOMINATOR_GENDER_COUNT['female'];//'11';
$measuresArr['DENOM']['SUPPLEMENT_GENDER_COUNT']["M"] = $ARR_DENOMINATOR_GENDER_COUNT['male'];//'12';

foreach($ARR_DENOMINATOR_ETHNICITY as $DENOMINATOR_ETHNICITY_VALUE_CODE => $DENOMINATOR_ETHNICITY_VALUE_COUNT){
	$measuresArr['DENOM']['ETHNICITY_VALUE_COUNT'][$DENOMINATOR_ETHNICITY_VALUE_CODE] = $DENOMINATOR_ETHNICITY_VALUE_COUNT;
}

foreach($ARR_DENOMINATOR_RACE as $DENOMINATOR_RACE_VALUE_CODE => $DENOMINATOR_RACE_VALUE_COUNT){
	$measuresArr['DENOM']['RACE_VALUE_COUNT'][$DENOMINATOR_RACE_VALUE_CODE]=$DENOMINATOR_RACE_VALUE_COUNT;
}
foreach($ARR_DENOMINATOR_PAYER as $DENOMINATOR_PAYER_VALUE_CODE => $DENOMINATOR_PAYER_VALUE_COUNT){
	$measuresArr['DENOM']['PAYER_VALUE_COUNT'][$DENOMINATOR_PAYER_VALUE_CODE] = $DENOMINATOR_PAYER_VALUE_COUNT;
}

//===================================================================================================//

//===================================DENOMINATOR_EXCLUSION===========================================//
$ARR_DENOMINATOR_EXCLUSION_GENDER_COUNT=$ARR_DENOMINATOR_EXCLUSION_ETHNICITY=$ARR_DENOMINATOR_EXCLUSION_RACE=$ARR_DENOMINATOR_EXCLUSION_PAYER=array();
$ARR_DENOMINATOR_EXCLUSION_GENDER_COUNT=$objMUR->_getTotalGender($ARRAY_DENOMINATOR_EXCLUSION);
$ARR_DENOMINATOR_EXCLUSION_ETHNICITY=$objMUR->_getTotalEthnicity($ARRAY_DENOMINATOR_EXCLUSION);
$ARR_DENOMINATOR_EXCLUSION_RACE=$objMUR->_getTotalRace($ARRAY_DENOMINATOR_EXCLUSION);
$ARR_DENOMINATOR_EXCLUSION_PAYER=$objMUR->_getTotalPayer($ARRAY_DENOMINATOR_EXCLUSION);

$measuresArr['DENOM_EXCLUSION']['GENDER_COUNT'] = $ARR_DENOMINATOR_EXCLUSION_GENDER_COUNT['count'];
$measuresArr['DENOM_EXCLUSION']['SUPPLEMENT_GENDER_COUNT']["F"] = $ARR_DENOMINATOR_EXCLUSION_GENDER_COUNT['female'];
$measuresArr['DENOM_EXCLUSION']['SUPPLEMENT_GENDER_COUNT']["M"] = $ARR_DENOMINATOR_EXCLUSION_GENDER_COUNT['male'];
foreach($ARR_DENOMINATOR_EXCLUSION_ETHNICITY as $DENOMINATOR_EXCLUSION_ETHNICITY_VALUE_CODE =>$DENOMINATOR_EXCLUSION_ETHNICITY_VALUE_COUNT){
	$measuresArr['DENOM_EXCLUSION']['ETHNICITY_VALUE_COUNT'][$DENOMINATOR_EXCLUSION_ETHNICITY_VALUE_CODE] = $DENOMINATOR_EXCLUSION_ETHNICITY_VALUE_COUNT;
}

foreach($ARR_DENOMINATOR_EXCLUSION_RACE as $DENOMINATOR_EXCLUSION_RACE_VALUE_CODE =>$DENOMINATOR_EXCLUSION_RACE_VALUE_COUNT){
	$measuresArr['DENOM_EXCLUSION']['RACE_VALUE_COUNT'][$DENOMINATOR_EXCLUSION_RACE_VALUE_CODE] = $DENOMINATOR_EXCLUSION_RACE_VALUE_COUNT;
}

foreach($ARR_DENOMINATOR_EXCLUSION_PAYER as $DENOMINATOR_EXCLUSION_PAYER_VALUE_CODE => $DENOMINATOR_EXCLUSION_PAYER_VALUE_COUNT){
	$measuresArr['DENOM_EXCLUSION']['PAYER_VALUE_COUNT'][$DENOMINATOR_EXCLUSION_PAYER_VALUE_CODE] = $DENOMINATOR_EXCLUSION_PAYER_VALUE_COUNT;
}

//===================================================================================================//

//===================================NUMERATOR=======================================================//
$ARR_NUMERATOR_GENDER_COUNT=$ARR_NUMERATOR_ETHNICITY=$ARR_NUMERATOR_RACE=$ARR_NUMERATOR_PAYER=array();
$ARR_NUMERATOR_GENDER_COUNT=$objMUR->_getTotalGender($ARRAY_NUMERATOR);
$ARR_NUMERATOR_ETHNICITY=$objMUR->_getTotalEthnicity($ARRAY_NUMERATOR);
$ARR_NUMERATOR_RACE=$objMUR->_getTotalRace($ARRAY_NUMERATOR);
$ARR_NUMERATOR_PAYER=$objMUR->_getTotalPayer($ARRAY_NUMERATOR);

$measuresArr['NUMER']['GENDER_COUNT'] = $ARR_NUMERATOR_GENDER_COUNT['count'];
$measuresArr['NUMER']['SUPPLEMENT_GENDER_COUNT']["F"] = $ARR_NUMERATOR_GENDER_COUNT['female'];
$measuresArr['NUMER']['SUPPLEMENT_GENDER_COUNT']["M"] = $ARR_NUMERATOR_GENDER_COUNT['male'];

foreach($ARR_NUMERATOR_ETHNICITY as $NUMERATOR_ETHNICITY_VALUE_CODE => $NUMERATOR_ETHNICITY_VALUE_COUNT){
	$measuresArr['NUMER']['ETHNICITY_VALUE_COUNT'][$NUMERATOR_ETHNICITY_VALUE_CODE ] = $NUMERATOR_ETHNICITY_VALUE_COUNT;
}
foreach($ARR_NUMERATOR_RACE as $NUMERATOR_RACE_VALUE_CODE => $NUMERATOR_RACE_VALUE_COUNT){ 
	$measuresArr['NUMER']['RACE_VALUE_COUNT'][$NUMERATOR_RACE_VALUE_CODE] =$NUMERATOR_RACE_VALUE_COUNT;
}
foreach($ARR_NUMERATOR_PAYER as $NUMERATOR_PAYER_VALUE_CODE => $NUMERATOR_PAYER_VALUE_COUNT){
	$measuresArr['NUMER']['PAYER_VALUE_COUNT'][$NUMERATOR_PAYER_VALUE_CODE] = $NUMERATOR_PAYER_VALUE_COUNT;
}

//===================================================================================================//

$performance_rate = (float) $measuresArr['NUMER']['GENDER_COUNT']/($measuresArr['DENOM']['GENDER_COUNT'] - $measuresArr['DENOM_EXCLUSION']['GENDER_COUNT'] );
$performance_rate = number_format($performance_rate,6,'.','');


/*
//START 
$measuresArr = array();
$measuresArr['IPP']['GENDER_COUNT'] = '12';
$measuresArr['IPP']['SUPPLEMENT_GENDER_COUNT']["F"] = '4';
$measuresArr['IPP']['SUPPLEMENT_GENDER_COUNT']["M"] = '8';

$measuresArr['IPP']['ETHNICITY_VALUE_COUNT']['2135-2'] = '6';
$measuresArr['IPP']['ETHNICITY_VALUE_COUNT']['2186-5'] = '6';

$measuresArr['IPP']['RACE_VALUE_COUNT']['2028-9'] = '1';
$measuresArr['IPP']['RACE_VALUE_COUNT']['2106-3'] = '3';
$measuresArr['IPP']['RACE_VALUE_COUNT']['2076-8'] = '3';
$measuresArr['IPP']['RACE_VALUE_COUNT']['1002-5'] = '1';
$measuresArr['IPP']['RACE_VALUE_COUNT']['2131-1'] = '3';
$measuresArr['IPP']['RACE_VALUE_COUNT']['2054-5'] = '1';
$measuresArr['IPP']['PAYER_VALUE_COUNT']['1'] = '3';
$measuresArr['IPP']['PAYER_VALUE_COUNT']['2'] = '4';
$measuresArr['IPP']['PAYER_VALUE_COUNT']['349'] = '5';


$measuresArr['DENOM']['GENDER_COUNT'] = '12';
$measuresArr['DENOM']['SUPPLEMENT_GENDER_COUNT']["F"] = '4';
$measuresArr['DENOM']['SUPPLEMENT_GENDER_COUNT']["M"] = '8';
$measuresArr['DENOM']['ETHNICITY_VALUE_COUNT']['2135-2'] = '6';
$measuresArr['DENOM']['ETHNICITY_VALUE_COUNT']['2186-5'] = '6';
$measuresArr['DENOM']['RACE_VALUE_COUNT']['2028-9'] = '1';
$measuresArr['DENOM']['RACE_VALUE_COUNT']['2106-3'] = '3';
$measuresArr['DENOM']['RACE_VALUE_COUNT']['2076-8'] = '3';
$measuresArr['DENOM']['RACE_VALUE_COUNT']['1002-5'] = '1';
$measuresArr['DENOM']['RACE_VALUE_COUNT']['2131-1'] = '3';
$measuresArr['DENOM']['RACE_VALUE_COUNT']['2054-5'] = '1';
$measuresArr['DENOM']['PAYER_VALUE_COUNT']['1'] = '3';
$measuresArr['DENOM']['PAYER_VALUE_COUNT']['2'] = '4';
$measuresArr['DENOM']['PAYER_VALUE_COUNT']['349'] = '5';

$measuresArr['DENOM_EXCLUSION']['GENDER_COUNT'] = '6';
$measuresArr['DENOM_EXCLUSION']['SUPPLEMENT_GENDER_COUNT']["F"] = '3';
$measuresArr['DENOM_EXCLUSION']['SUPPLEMENT_GENDER_COUNT']["M"] = '3';
$measuresArr['DENOM_EXCLUSION']['ETHNICITY_VALUE_COUNT']['2135-2'] = '3';
$measuresArr['DENOM_EXCLUSION']['ETHNICITY_VALUE_COUNT']['2186-5'] = '3';
$measuresArr['DENOM_EXCLUSION']['RACE_VALUE_COUNT']['2076-8'] = '3';
$measuresArr['DENOM_EXCLUSION']['RACE_VALUE_COUNT']['2106-3'] = '1';
$measuresArr['DENOM_EXCLUSION']['RACE_VALUE_COUNT']['2131-1'] = '2';
$measuresArr['DENOM_EXCLUSION']['PAYER_VALUE_COUNT']['2'] = '2';
$measuresArr['DENOM_EXCLUSION']['PAYER_VALUE_COUNT']['349'] = '4';

$measuresArr['NUMER']['GENDER_COUNT'] = '2';
$measuresArr['NUMER']['SUPPLEMENT_GENDER_COUNT']["F"] = '0';
$measuresArr['NUMER']['SUPPLEMENT_GENDER_COUNT']["M"] = '2';
$measuresArr['NUMER']['ETHNICITY_VALUE_COUNT']['2135-2'] = '1';
$measuresArr['NUMER']['ETHNICITY_VALUE_COUNT']['2186-5'] = '1';
$measuresArr['NUMER']['RACE_VALUE_COUNT']['2106-3'] = '1';
$measuresArr['NUMER']['RACE_VALUE_COUNT']['2076-8'] = '0';
$measuresArr['NUMER']['RACE_VALUE_COUNT']['2131-1'] = '1';

$measuresArr['NUMER']['PAYER_VALUE_COUNT']['1'] = '1';
$measuresArr['NUMER']['PAYER_VALUE_COUNT']['2'] = '1';
$measuresArr['NUMER']['PAYER_VALUE_COUNT']['349'] = '0';
//END
*/



$xmlData = '<?xml version="1.0" encoding="utf-8"?>
<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns="urn:hl7-org:v3"
 xmlns:cda="urn:hl7-org:v3">

  <!--
    ********************************************************
    CDA Header
    ********************************************************
  -->
  <realmCode code="US"/>
  <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>
  <!-- QRDA Category III template ID (this template ID differs from QRDA III comment only template ID). -->
  <templateId root="2.16.840.1.113883.10.20.27.1.1" extension="2017-06-01"/>
  <id root="cc6d1e20-bbbd-0135-be88-0242ac120005" extension="CypressExtension"/>


  <!-- SHALL QRDA III document type code -->
  <code code="55184-6" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
    displayName="Quality Reporting Document Architecture Calculated Summary Report"/>
  <!-- SHALL Title, SHOULD have this content -->
  <title>QRDA Calculated Summary Report</title>
  <!-- SHALL  -->
  <effectiveTime value="'.$currentDateTime.'"/>
  <confidentialityCode codeSystem="2.16.840.1.113883.5.25" code="N"/>
  <languageCode code="en"/>
  <!-- SHOULD The version of the file being submitted. -->
  <versionNumber value="1"/>
  <!-- SHALL contain recordTarget and ID - but ID is nulled to NA. This is an aggregate summary report. Therefore CDA\'s required patient identifier is nulled. -->
  <recordTarget>
    <patientRole>
      <id nullFlavor="NA"/>
    </patientRole>
  </recordTarget>

   <!-- SHALL have 1..* author. MAY be device or person. 
    The author of the CDA document in this example is a device at a data submission vendor/registry. -->
  <author>
    <time value="'.$currentDateTime.'"/>
    <assignedAuthor>
      <!-- Registry author ID -->
      <id root="authorRoot" extension="authorExtension"/>

      
      

       <assignedAuthoringDevice>
         <manufacturerModelName>deviceModel</manufacturerModelName>
         <softwareName>deviceName</softwareName>
       </assignedAuthoringDevice>
     
     <representedOrganization>
  <!-- Represents unique registry organization TIN -->
   <id root="authorsOrganizationRoot" extension="authorsOrganizationExt"/>

  <!-- Contains name - specific registry not required-->
  <name>'.$row_facility['name'].'</name>
</representedOrganization>

    </assignedAuthor>
  </author>

  <!-- SHALL have 1..* author. MAY be device or person.
    The author of the CDA document in this example is a device at a data submission vendor/registry. -->

  <!-- The custodian of the CDA document is the same as the legal authenticator in this
  example and represents the reporting organization. -->
  <!-- SHALL -->
  <custodian>
    <assignedCustodian>
      <representedCustodianOrganization>
  <!-- Represents unique registry organization TIN -->
   <id root="custodianOrganizationRoot" extension="custodianOrganizationExt"/>

  <!-- Contains name - specific registry not required-->
  <name></name>
</representedCustodianOrganization>
    </assignedCustodian>
  </custodian>
  <!-- The legal authenticator of the CDA document is a single person who is at the
    same organization as the custodian in this example. This element must be present. -->
  <!-- SHALL -->
  <legalAuthenticator>
    <!-- SHALL -->
    <time value="'.$currentDateTime.'"/>
    <!-- SHALL -->
    <signatureCode code="S"/>
    <assignedEntity>
      <!-- SHALL ID -->
      <id root="legalAuthenticatorRoot" extension="legalAuthenticatorExt"/>

      
      <assignedPerson>
        <name>
           <given></given>
           <family></family>
        </name>
     </assignedPerson>

      <representedOrganization>
  <!-- Represents unique registry organization TIN -->
   <id root="legalAuthenticatorOrgRoot" extension="legalAuthenticatorOrgExt"/>

  <!-- Contains name - specific registry not required-->
  <name></name>
</representedOrganization>
    </assignedEntity>
  </legalAuthenticator>

  <documentationOf typeCode="DOC">
  <serviceEvent classCode="PCPR"> <!-- care provision -->
    <!-- No provider data found in the patient record
         putting in a fake provider -->
    <effectiveTime>
      <low value="'.preg_replace("/-/","",$dtfrom1).'"/>
      <high value="'.preg_replace("/-/","",$dtupto1Tm).'"/>
    </effectiveTime>
    <!-- You can include multiple performers, each with an NPI, TIN, CCN. -->
    <performer typeCode="PRF">
      <time>
        <low value="'.preg_replace("/-/","",$dtfrom1).'"/>
        <high value="'.preg_replace("/-/","",$dtupto1Tm).'"/>
      </time>
      <assignedEntity>
        <!-- This is the provider NPI -->
        <id root="2.16.840.1.113883.4.6" extension="111111111" />
        <representedOrganization>
          <!-- This is the organization TIN -->
          <id root="2.16.840.1.113883.4.2" extension="1234567" />
          <!-- This is the organization CCN -->
          <id root="2.16.840.1.113883.4.336" extension="54321" />
        </representedOrganization>
      </assignedEntity>
    </performer>
  </serviceEvent>
</documentationOf>



  <!--
********************************************************
CDA Body
********************************************************
-->
  <component>
    <structuredBody>
      <!--
********************************************************
QRDA Category III Reporting Parameters
********************************************************
-->
      <component>
        <section>
          <!-- This is the templateId for Reporting Parameters section -->
          <templateId root="2.16.840.1.113883.10.20.17.2.1"/>

          <!-- This is the templateId for the QRDA III Reporting Parameters Section -->
          <templateId root="2.16.840.1.113883.10.20.27.2.2"/>

          <code code="55187-9" codeSystem="2.16.840.1.113883.6.1"/>
          <title>Reporting Parameters</title>
          <text>
            <list>
              <item>Reporting period: '.date('%B %d, %Y 00:00',strtotime($dtfrom1)).' - '.date('%B %d, %Y 23:59',strtotime($dtupto1)).'</item>
            </list>
          </text>
          <entry typeCode="DRIV">
                      <act classCode="ACT" moodCode="EVN">
              <!-- This is the templateId for Reporting Parameters Act -->
              <templateId root="2.16.840.1.113883.10.20.17.3.8"/>
              <id extension="4C240BE81EBE2C2F2F97C7C79D1B0666" />
              <code code="252116004" codeSystem="2.16.840.1.113883.6.96" displayName="Observation Parameters"/>
              <effectiveTime>
                <low value="'.preg_replace("/-/","",$dtfrom1Tm).'"/>
                <high value="'.preg_replace("/-/","",$dtupto1Tm).'"/>
              </effectiveTime>
            </act>
          </entry>
        </section>
      </component>
      <!--
********************************************************
Measure Section
********************************************************
-->
      <component>
        <section>
          <!-- Implied template Measure Section templateId -->
          <templateId root="2.16.840.1.113883.10.20.24.2.2"/>
          <!-- In this case the query is using an eMeasure -->
          <!-- QRDA Category III Measure Section template -->
          <templateId root="2.16.840.1.113883.10.20.27.2.1" extension="2017-06-01"/>
          <code code="55186-1" codeSystem="2.16.840.1.113883.6.1"/>
          <title>Measure Section</title>
          <text>

          </text>
          <entry>
            <organizer classCode="CLUSTER" moodCode="EVN">
              <!-- Implied template Measure Reference templateId -->
              <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
              <!-- SHALL 1..* (one for each referenced measure) Measure Reference and Results template -->
              <templateId root="2.16.840.1.113883.10.20.27.3.1" extension="2016-09-01"/>
              <id extension="40280382-5ABD-FA46-015B-49ABB28D38B2"/>
              <statusCode code="completed"/>
              <reference typeCode="REFR">
                <externalDocument classCode="DOC" moodCode="EVN">
                  <!-- SHALL: required Id but not restricted to the eMeasure Document/Id-->
                  <!-- QualityMeasureDocument/id This is the version specific identifier for eMeasure -->
                  <id root="2.16.840.1.113883.4.738" extension="40280382-5ABD-FA46-015B-49ABB28D38B2"/>

                  <!-- SHOULD This is the title of the eMeasure -->
                  <text>Controlling High Blood Pressure</text>
                  <!-- SHOULD: setId is the eMeasure version neutral id  -->
                  <setId root="ABDC37CC-BAC6-4156-9B91-D1BE2C8B7268"/>
                  <!-- This is the sequential eMeasure Version number -->
                  <versionNumber value="1"/>
                </externalDocument>
              </reference>

              <component>
              <observation classCode="OBS" moodCode="EVN">
  <templateId root="2.16.840.1.113883.10.20.27.3.14" extension="2016-09-01"/>
  <templateId root="2.16.840.1.113883.10.20.27.3.30" extension="2016-09-01"/>
  <code code="72510-1" codeSystem="2.16.840.1.113883.6.1"
    displayName="Performance Rate" 
    codeSystemName="2.16.840.1.113883.6.1"/>
  <statusCode code="completed"/>
  <value xsi:type="REAL" value="'.$performance_rate.'"/>
  <reference typeCode="REFR">
     <externalObservation classCode="OBS" moodCode="EVN">
       <id root="709D84FA-637D-42CD-8934-43C3FBAD0979"/>
       <code code="NUMER" displayName="Numerator" codeSystem="2.16.840.1.113883.5.4" codeSystemName="ObservationValue"/>
    </externalObservation>
  </reference>
</observation>

              </component>
              <component>
              
<!--   MEASURE DATA REPORTING FOR    IPP  5C206C23-4CF9-4390-9E76-0F243FE59DCF  -->
<observation classCode="OBS" moodCode="EVN">
  <!-- Measure Data template -->
  <templateId root="2.16.840.1.113883.10.20.27.3.5" extension="2016-09-01"/>
  <code code="ASSERTION" 
        codeSystem="2.16.840.1.113883.5.4" 
        displayName="Assertion" 
        codeSystemName="ActCode"/>
  <statusCode code="completed"/>
  <value xsi:type="CD" code="IPOP"
         codeSystem="2.16.840.1.113883.5.4"  
         codeSystemName="ActCode"/>';
if($measuresArr['IPP']['GENDER_COUNT']){		 
	$xmlData .= ' <!-- Aggregate Count -->
  <entryRelationship typeCode="SUBJ" inversionInd="true">
    <observation classCode="OBS" moodCode="EVN">
      <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
      <code code="MSRAGG" 
        displayName="rate aggregation" 
        codeSystem="2.16.840.1.113883.5.4" 
        codeSystemName="ActCode"/>
      <value xsi:type="INT" value="'.$measuresArr['IPP']['GENDER_COUNT'].'"/>
      <methodCode code="COUNT" 
        displayName="Count" 
        codeSystem="2.16.840.1.113883.5.84" 
        codeSystemName="ObservationMethod"/>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['IPP']['SUPPLEMENT_GENDER_COUNT'] as $IPP_SUPPLE_VALUE_CODE => $IPP_SUPPLE_VALUE_COUNT) {
	if(!$IPP_SUPPLE_VALUE_COUNT){continue;}
	$xmlData .= ' 
   
   <!--    SEX Supplemental Data Reporting for IPP  5C206C23-4CF9-4390-9E76-0F243FE59DCF      --> 
         
    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Sex Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.6" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="76689-9" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$IPP_SUPPLE_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.5.1"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$IPP_SUPPLE_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['IPP']['ETHNICITY_VALUE_COUNT'] as $IPP_ETH_VALUE_CODE => $IPP_ETH_VALUE_COUNT) {
	if(!$IPP_ETH_VALUE_COUNT){continue;}
	$xmlData .= '    

  <!--     ETHNICITY Supplemental Data Reporting  for IPP  5C206C23-4CF9-4390-9E76-0F243FE59DCF     --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Ethnicity Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.7" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="69490-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$IPP_ETH_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$IPP_ETH_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship> ';
}
foreach($measuresArr['IPP']['RACE_VALUE_COUNT'] as $IPP_RAC_VALUE_CODE => $IPP_RAC_VALUE_COUNT) {
	if(!$IPP_RAC_VALUE_COUNT){continue;}
	$xmlData .= ' 
	<!--      RACE Supplemental Data Reporting  for IPP  5C206C23-4CF9-4390-9E76-0F243FE59DCF --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Race Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.8" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="72826-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$IPP_RAC_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$IPP_RAC_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship> ';   
}
foreach($measuresArr['IPP']['PAYER_VALUE_COUNT'] as $IPP_PAY_VALUE_CODE => $IPP_PAY_VALUE_COUNT) {
	if(!$IPP_PAY_VALUE_COUNT){continue;}
	$xmlData .= ' 
 <!--         PAYER Supplemental Data Reporting   for IPP  5C206C23-4CF9-4390-9E76-0F243FE59DCF   -->
   <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Payer Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.9" extension="2016-02-01"/>
        <id nullFlavor="NA" />
      <code code="48768-6" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$IPP_PAY_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.3.221.5"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$IPP_PAY_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship> '; 
}
$xmlData .= '<reference typeCode="REFR">
     <externalObservation classCode="OBS" moodCode="EVN">
        <id root="5C206C23-4CF9-4390-9E76-0F243FE59DCF"/>
     </externalObservation>
  </reference>
</observation>
              </component>
              <component>
              
<!--   MEASURE DATA REPORTING FOR    DENOM  1D456B20-71F7-4477-BD23-D39600D5A095  -->
<observation classCode="OBS" moodCode="EVN">
  <!-- Measure Data template -->
  <templateId root="2.16.840.1.113883.10.20.27.3.5" extension="2016-09-01"/>
  <code code="ASSERTION" 
        codeSystem="2.16.840.1.113883.5.4" 
        displayName="Assertion" 
        codeSystemName="ActCode"/>
  <statusCode code="completed"/>
  <value xsi:type="CD" code="DENOM"
         codeSystem="2.16.840.1.113883.5.4"  
         codeSystemName="ActCode"/>';
if($measuresArr['DENOM']['GENDER_COUNT']){		 
	$xmlData .= ' <!-- Aggregate Count -->
  <entryRelationship typeCode="SUBJ" inversionInd="true">
    <observation classCode="OBS" moodCode="EVN">
      <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
      <code code="MSRAGG" 
        displayName="rate aggregation" 
        codeSystem="2.16.840.1.113883.5.4" 
        codeSystemName="ActCode"/>
      <value xsi:type="INT" value="'.$measuresArr['DENOM']['GENDER_COUNT'].'"/>
      <methodCode code="COUNT" 
        displayName="Count" 
        codeSystem="2.16.840.1.113883.5.84" 
        codeSystemName="ObservationMethod"/>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['DENOM']['SUPPLEMENT_GENDER_COUNT'] as $DENOM_SUPPLE_VALUE_CODE => $DENOM_SUPPLE_VALUE_COUNT) {
	if(!$DENOM_SUPPLE_VALUE_COUNT){continue;}
	$xmlData .= ' 
  
   
  <!--    SEX Supplemental Data Reporting for DENOM  1D456B20-71F7-4477-BD23-D39600D5A095      --> 
         
    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Sex Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.6" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="76689-9" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_SUPPLE_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.5.1"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_SUPPLE_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship> ';
}
foreach($measuresArr['DENOM']['ETHNICITY_VALUE_COUNT'] as $DENOM_ETH_VALUE_CODE => $DENOM_ETH_VALUE_COUNT) {
	if(!$DENOM_ETH_VALUE_COUNT){continue;}
	$xmlData .= '    

    <!--     ETHNICITY Supplemental Data Reporting  for DENOM  1D456B20-71F7-4477-BD23-D39600D5A095     --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Ethnicity Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.7" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="69490-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_ETH_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_ETH_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['DENOM']['RACE_VALUE_COUNT'] as $DENOM_RAC_VALUE_CODE => $DENOM_RAC_VALUE_COUNT) {
	if(!$DENOM_RAC_VALUE_COUNT){continue;}
	$xmlData .= ' 
  <!--      RACE Supplemental Data Reporting  for DENOM  1D456B20-71F7-4477-BD23-D39600D5A095 --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Race Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.8" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="72826-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_RAC_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_RAC_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['DENOM']['PAYER_VALUE_COUNT'] as $DENOM_PAY_VALUE_CODE => $DENOM_PAY_VALUE_COUNT) {
	if(!$DENOM_PAY_VALUE_COUNT){continue;}
	$xmlData .= ' 
 <!--         PAYER Supplemental Data Reporting   for DENOM  1D456B20-71F7-4477-BD23-D39600D5A095   -->
   <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Payer Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.9" extension="2016-02-01"/>
        <id nullFlavor="NA" />
      <code code="48768-6" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_PAY_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.3.221.5"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_PAY_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
$xmlData .= '
  <reference typeCode="REFR">
     <externalObservation classCode="OBS" moodCode="EVN">
        <id root="1D456B20-71F7-4477-BD23-D39600D5A095"/>
     </externalObservation>
  </reference>
</observation>
              </component>
              <component>
              
<!--   MEASURE DATA REPORTING FOR    DENEX  9805BC7D-274C-4CCF-916A-B5BA34D62A31  -->
<observation classCode="OBS" moodCode="EVN">
  <!-- Measure Data template -->
  <templateId root="2.16.840.1.113883.10.20.27.3.5" extension="2016-09-01"/>
  <code code="ASSERTION" 
        codeSystem="2.16.840.1.113883.5.4" 
        displayName="Assertion" 
        codeSystemName="ActCode"/>
  <statusCode code="completed"/>
  <value xsi:type="CD" code="DENEX"
         codeSystem="2.16.840.1.113883.5.4"  
         codeSystemName="ActCode"/>';
if($measuresArr['DENOM_EXCLUSION']['GENDER_COUNT']){		 
	$xmlData .= ' 
  <!-- Aggregate Count -->
  <entryRelationship typeCode="SUBJ" inversionInd="true">
    <observation classCode="OBS" moodCode="EVN">
      <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
      <code code="MSRAGG" 
        displayName="rate aggregation" 
        codeSystem="2.16.840.1.113883.5.4" 
        codeSystemName="ActCode"/>
      <value xsi:type="INT" value="'.$measuresArr['DENOM_EXCLUSION']['GENDER_COUNT'].'"/>
      <methodCode code="COUNT" 
        displayName="Count" 
        codeSystem="2.16.840.1.113883.5.84" 
        codeSystemName="ObservationMethod"/>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['DENOM_EXCLUSION']['SUPPLEMENT_GENDER_COUNT'] as $DENOM_EXCL_SUPPLE_VALUE_CODE => $DENOM_EXCL_SUPPLE_VALUE_COUNT) {
	if(!$DENOM_EXCL_SUPPLE_VALUE_COUNT){continue;}
	$xmlData .= '
	<!--    SEX Supplemental Data Reporting for DENEX  9805BC7D-274C-4CCF-916A-B5BA34D62A31      --> 
         
    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Sex Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.6" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="76689-9" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_EXCL_SUPPLE_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.5.1"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_EXCL_SUPPLE_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['DENOM_EXCLUSION']['ETHNICITY_VALUE_COUNT'] as $DENOM_EXCL_ETH_VALUE_CODE => $DENOM_EXCL_ETH_VALUE_COUNT) {
	if(!$DENOM_EXCL_ETH_VALUE_COUNT){continue;}
	$xmlData .= ' 
  <!--     ETHNICITY Supplemental Data Reporting  for DENEX  9805BC7D-274C-4CCF-916A-B5BA34D62A31     --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Ethnicity Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.7" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="69490-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_EXCL_ETH_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_EXCL_ETH_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';    
}
foreach($measuresArr['DENOM_EXCLUSION']['RACE_VALUE_COUNT'] as $DENOM_EXCL_RAC_VALUE_CODE => $DENOM_EXCL_RAC_VALUE_COUNT) {
	if(!$DENOM_EXCL_RAC_VALUE_COUNT){continue;}
	$xmlData .= ' 
    <!--      RACE Supplemental Data Reporting  for DENEX  9805BC7D-274C-4CCF-916A-B5BA34D62A31 --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Race Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.8" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="72826-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_EXCL_RAC_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_EXCL_RAC_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship> ';
}
foreach($measuresArr['DENOM_EXCLUSION']['PAYER_VALUE_COUNT'] as $DENOM_EXCL_PAY_VALUE_CODE => $DENOM_EXCL_PAY_VALUE_COUNT) {
	if(!$DENOM_EXCL_PAY_VALUE_COUNT){continue;}
	$xmlData .= ' 
 <!--         PAYER Supplemental Data Reporting   for DENEX  9805BC7D-274C-4CCF-916A-B5BA34D62A31   -->
   <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Payer Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.9" extension="2016-02-01"/>
        <id nullFlavor="NA" />
      <code code="48768-6" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$DENOM_EXCL_PAY_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.3.221.5"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$DENOM_EXCL_PAY_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship> ';
}
$xmlData .= ' 
  <reference typeCode="REFR">
     <externalObservation classCode="OBS" moodCode="EVN">
        <id root="9805BC7D-274C-4CCF-916A-B5BA34D62A31"/>
     </externalObservation>
  </reference>
</observation>
              </component>
              <component>
              
<!--   MEASURE DATA REPORTING FOR    NUMER  709D84FA-637D-42CD-8934-43C3FBAD0979  -->
<observation classCode="OBS" moodCode="EVN">
  <!-- Measure Data template -->
  <templateId root="2.16.840.1.113883.10.20.27.3.5" extension="2016-09-01"/>
  <code code="ASSERTION" 
        codeSystem="2.16.840.1.113883.5.4" 
        displayName="Assertion" 
        codeSystemName="ActCode"/>
  <statusCode code="completed"/>
  <value xsi:type="CD" code="NUMER"
         codeSystem="2.16.840.1.113883.5.4"  
         codeSystemName="ActCode"/>';
if($measuresArr['NUMER']['GENDER_COUNT']){		 
	$xmlData .= ' 
  <!-- Aggregate Count -->
  <entryRelationship typeCode="SUBJ" inversionInd="true">
    <observation classCode="OBS" moodCode="EVN">
      <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
      <code code="MSRAGG" 
        displayName="rate aggregation" 
        codeSystem="2.16.840.1.113883.5.4" 
        codeSystemName="ActCode"/>
      <value xsi:type="INT" value="'.$measuresArr['NUMER']['GENDER_COUNT'].'"/>
      <methodCode code="COUNT" 
        displayName="Count" 
        codeSystem="2.16.840.1.113883.5.84" 
        codeSystemName="ObservationMethod"/>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['NUMER']['SUPPLEMENT_GENDER_COUNT'] as $NUMER_SUPPLE_VALUE_CODE => $NUMER_SUPPLE_VALUE_COUNT) {
	if(!$NUMER_SUPPLE_VALUE_COUNT){continue;}
	$xmlData .= ' 
  <!--    SEX Supplemental Data Reporting for NUMER  709D84FA-637D-42CD-8934-43C3FBAD0979      --> 
         
    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Sex Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.6" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="76689-9" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$NUMER_SUPPLE_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.5.1"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$NUMER_SUPPLE_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['NUMER']['ETHNICITY_VALUE_COUNT'] as $NUMER_ETH_VALUE_CODE => $NUMER_ETH_VALUE_COUNT) {
	if(!$NUMER_ETH_VALUE_COUNT){continue;}
	$xmlData .= ' 
    <!--     ETHNICITY Supplemental Data Reporting  for NUMER  709D84FA-637D-42CD-8934-43C3FBAD0979     --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Ethnicity Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.7" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="69490-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$NUMER_ETH_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$NUMER_ETH_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';    
}
foreach($measuresArr['NUMER']['RACE_VALUE_COUNT'] as $NUMER_RAC_VALUE_CODE => $NUMER_RAC_VALUE_COUNT) {
	if(!$NUMER_RAC_VALUE_COUNT){continue;}
	$xmlData .= ' 
    <!--      RACE Supplemental Data Reporting  for NUMER  709D84FA-637D-42CD-8934-43C3FBAD0979 --> 

    <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Race Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.8" extension="2016-09-01"/>
        <id nullFlavor="NA" />
      <code code="72826-1" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$NUMER_RAC_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.6.238"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$NUMER_RAC_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>';
}
foreach($measuresArr['NUMER']['PAYER_VALUE_COUNT'] as $NUMER_PAY_VALUE_CODE => $NUMER_PAY_VALUE_COUNT) {
	if(!$NUMER_PAY_VALUE_COUNT){continue;}
	$xmlData .= ' 
<!--         PAYER Supplemental Data Reporting   for NUMER  709D84FA-637D-42CD-8934-43C3FBAD0979   -->
   <!--                            Supplemental Data Template                                                  -->

  <entryRelationship typeCode="COMP">
    <observation classCode="OBS" moodCode="EVN">
      <!-- Payer Supplemental Data -->
        <templateId root="2.16.840.1.113883.10.20.27.3.9" extension="2016-02-01"/>
        <id nullFlavor="NA" />
      <code code="48768-6" 
            codeSystem="2.16.840.1.113883.6.1"/>
      <statusCode code="completed"/>
      
      <value xsi:type="CD" 
             code="'.$NUMER_PAY_VALUE_CODE.'"
             codeSystem="2.16.840.1.113883.3.221.5"/>
      <entryRelationship typeCode="SUBJ" inversionInd="true">
        <!-- Aggregate Count template -->
        <observation classCode="OBS" moodCode="EVN">
          <templateId root="2.16.840.1.113883.10.20.27.3.3"/>
          <code code="MSRAGG" 
                displayName="rate aggregation" 
                codeSystem="2.16.840.1.113883.5.4" 
                codeSystemName="ActCode"/>
          <value xsi:type="INT" value="'.$NUMER_PAY_VALUE_COUNT.'"/>
          <methodCode code="COUNT" 
                      displayName="Count" 
                      codeSystem="2.16.840.1.113883.5.84" 
                      codeSystemName="ObservationMethod"/>
        </observation>
      </entryRelationship>
    </observation>
  </entryRelationship>  ';
}
$xmlData .= '
  <reference typeCode="REFR">
     <externalObservation classCode="OBS" moodCode="EVN">
        <id root="709D84FA-637D-42CD-8934-43C3FBAD0979"/>
     </externalObservation>
  </reference>
</observation>
              </component>
            </organizer>
          </entry>
          <entry>
                      <act classCode="ACT" moodCode="EVN">
              <!-- This is the templateId for Reporting Parameters Act -->
              <templateId root="2.16.840.1.113883.10.20.17.3.8"/>
              <id extension="4C240BE81EBE2C2F2F97C7C79D1B0666" />
              <code code="252116004" codeSystem="2.16.840.1.113883.6.96" displayName="Observation Parameters"/>
              <effectiveTime>
                <low value="'.preg_replace("/-/","",$dtfrom1Tm).'"/>
                <high value="'.preg_replace("/-/","",$dtupto1Tm).'"/>
              </effectiveTime>
            </act>
          </entry>
        </section>
      </component>
    </structuredBody>
  </component>
</ClinicalDocument>
';
?>
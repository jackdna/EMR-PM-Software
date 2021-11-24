<?php

class ECQMData {

    protected $patient_id;
    protected $CMS_ID;
    protected $pro_id;

    public function __construct() {
        
    }
    
    protected function ecqm_v8_data($CMS_ID='') {
        //ICD10CM, ICD9CM, SNOMEDCT, CPT, CDCREC (Race), AdministrativeGender, SOP (Payer), RXNORM, HCPCS, ICD10PCS, LOINC
        $ecqm_v8_data=array();
        if($this->CMS_ID!='') {
            $data=' id,	CMS_ID,	NQF_Number,	Value_Set_Name,	Value_Set_OID, QDM_Category, Code, Description, Code_System, Code_System_OID, Expansion_ID ';
            $sql = "SELECT ".$data." FROM cqm_v8_valueset WHERE CMS_ID='".$this->CMS_ID."' ";
            $res = imw_query($sql);
            if($res && imw_num_rows($res)>0) {
                while ($row = imw_fetch_assoc($res)) {
                    $ecqm_v8_data[$row['Code_System']][$row['Code']] = $row;
                }
            }
        }
        
        return $ecqm_v8_data;
    }
    
    /* BEGIN AUTHOR DATA */
    public function user_data() {
        $row_user=array();
        $qry_user = "select * from users where id = '".$this->pro_id."' ";
        $res_user = imw_query($qry_user);
        $row_user = imw_fetch_assoc($res_user);
        
        $row_facility = $this->facility_data($row_user['facility']);
        $row_user['row_facility']=$row_facility;
        
        return $row_user;
    }
    /* END AUTHOR DATA   */

    
    /* BEGIN CUSTODIAN (FACILITY) DATA */
    public function facility_data($facility=0) {
        $row_facility=array();
        if ($facility > 0) {
            $qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."' ";
        } else {
            $qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
        }
        $res_facility = imw_query($qry_facility);
        $row_facility = imw_fetch_assoc($res_facility);
        
        return $row_facility;
    }
    /* END CUSTODIAN (FACILITY) DATA */
    
    
    
    //create xml content starts here
    
    /* BEGIN CUSTODIAN (FACILITY) DATA xml content*/
    public function get_custodian_data_xml($custodian_data=array()) {
        $XML_custodian_data = '<custodian>';
        $XML_custodian_data .= '<assignedCustodian>';
        $XML_custodian_data .= '<representedCustodianOrganization>';
        $XML_custodian_data .= '<id root="2.16.840.1.113883.19.5"/>';
        if ($custodian_data['name'] != "") {
            $XML_custodian_data .= '<name>' . $custodian_data['name'] . '</name>';
        }

        if ($custodian_data['phone'] != ""){
            $XML_custodian_data .= '<telecom use="WP" value="tel:+1-' . core_phone_format($custodian_data['phone']) . '"/>';
        }else{
            $XML_custodian_data .= '<telecom nullFlavor="NI"/>';
        }

        $XML_custodian_data .= '<addr>';
        if ($custodian_data['street'] != ""){
            $XML_custodian_data .= '<streetAddressLine>' . $custodian_data['street'] . '</streetAddressLine>';
        }
        if ($custodian_data['city'] != ""){
            $XML_custodian_data .= '<city>' . $custodian_data['city'] . '</city>';
        }
        if ($custodian_data['state'] != ""){
            $XML_custodian_data .= '<state>' . $custodian_data['state'] . '</state>';
        }
        if ($custodian_data['postal_code'] != ""){
            $XML_custodian_data .= '<postalCode>' . $custodian_data['postal_code'] . '</postalCode>';
        }
        $XML_custodian_data .= '<country>US</country>';
        $XML_custodian_data .= '</addr>';

        $XML_custodian_data .= '</representedCustodianOrganization>';
        $XML_custodian_data .= '</assignedCustodian>';
        $XML_custodian_data .= '</custodian>';

        return $XML_custodian_data;
    }
    /* END CUSTODIAN (FACILITY) DATA */


    /* BEGIN AUTHOR DATA */
    public function get_author_data_xml($author_data=array(),$currentDate='') {
        $XML_author_data = '<author>';
        $XML_author_data .= '<time value="' . $currentDate . '"/>';
        $XML_author_data .= '<assignedAuthor>';
        if ($author_data['user_npi'] != "") {
            $XML_author_data .= '<id extension="' . $author_data['user_npi'] . '" root="2.16.840.1.113883.4.6"/>';
        } else {
            $XML_author_data .= '<id root="2.16.840.1.113883.4.6"/>';
        }
        $row_facility = $author_data['row_facility'];

        $XML_author_data .= '<addr use="WP">';
        if ($row_facility['street'] != ""){
            $XML_author_data .= '<streetAddressLine>' . $row_facility['street'] . '</streetAddressLine>';
        }
        if ($row_facility['city'] != ""){
            $XML_author_data .= '<city>' . $row_facility['city'] . '</city>';
        }
        if ($row_facility['state'] != ""){
            $XML_author_data .= '<state>' . $row_facility['state'] . '</state>';
        }
        if ($row_facility['postal_code'] != ""){
            $XML_author_data .= '<postalCode>' . $row_facility['postal_code'] . '</postalCode>';
        }

        $XML_author_data .= '<country>US</country>';
        $XML_author_data .= '</addr>';

        if ($row_facility['phone'] != "")
            $XML_author_data .= '<telecom use="WP" value="tel:+1-' . core_phone_format($row_facility['phone']) . '"/>';

        $XML_author_data .= '<assignedAuthoringDevice>
                <manufacturerModelName>imwemr</manufacturerModelName>
                <softwareName>imwemr</softwareName>
              </assignedAuthoringDevice>';
        $XML_author_data .= '</assignedAuthor>';
        $XML_author_data .= '</author>';

        return $XML_author_data;
    }
     /* END AUTHOR DATA   */

    
    /* BEGIN Leagal Auth DATA    xml content*/
    public function get_leagalauth_data_xml($custodian_data=array(),$currentDate='') {
        $XML_leagalauth_data .= '<!-- This needs to take reporting program into account EH/EP-->
        <informationRecipient>
            <intendedRecipient>
                <id root="2.16.840.1.113883.3.249.7" extension="PQRS_MU_INDIVIDUAL"/>
            </intendedRecipient>
        </informationRecipient>';

        $XML_leagalauth_data .= '<legalAuthenticator>
        <time value="' . $currentDate . '"/>
        <signatureCode code="S"/>
        <assignedEntity>
          <id root="bc01a5d1-3a34-4286-82cc-43eb04c972a7"/>
          <addr>
            <streetAddressLine>' . $custodian_data['street'] . '</streetAddressLine>
            <city>' . $custodian_data['city'] . '</city>
            <state>' . $custodian_data['state'] . '</state>
            <postalCode>' . $custodian_data['postal_code'] . '</postalCode>
            <country>US</country>
          </addr>
          <telecom use="WP" value="tel:+1-' . core_phone_format($custodian_data['phone']) . '"/>
          <assignedPerson>
            <name>
               <given>ECL</given>
               <family>imwemr</family>
            </name>
         </assignedPerson>
          <representedOrganization>
            <id root="2.16.840.1.113883.19.5"/>
            <name>imwemr</name>
          </representedOrganization>
        </assignedEntity>
      </legalAuthenticator>';

        $XML_leagalauth_data .= '<participant typeCode="DEV">
            <associatedEntity classCode="RGPR">
                <id root="2.16.840.1.113883.3.2074.1" extension="0014ABC1D1EFG1H"/>
            </associatedEntity>
        </participant>';
        
        return $XML_leagalauth_data;
    }
    /* END Leagal Auth DATA */
    
    
    /* BEGIN CARE TEAM MEMBERS */
    public function get_documentationOf_data_xml($author_data=array(),$currentDate='') {
        $XML_documentationof_data = '<documentationOf typeCode="DOC">';
        $XML_documentationof_data .= '<serviceEvent classCode="PCPR">';
        $XML_documentationof_data .= '<effectiveTime>
                                     <low value="' . $currentDate . '"/>
                                     </effectiveTime>';

        $XML_documentationof_data .= '<performer typeCode="PRF">';
        $XML_documentationof_data .= '<time>
                                     <low value="' . $currentDate . '"/>
                                     </time>';

        $XML_documentationof_data .= '<assignedEntity>';
        if ($author_data['user_npi'] != ""){
            $XML_documentationof_data .= '<id extension="' . $author_data['user_npi'] . '" root="2.16.840.1.113883.4.6"/>';
        }else{
            $XML_documentationof_data .= '<id nullFlavor="NI"/>';
        }

        $XML_documentationof_data .= '<id root="2.16.840.1.113883.4.336" extension="54321" /> ';
        $XML_documentationof_data .= ' <representedOrganization>
            <!-- This is the organization TIN -->
            <id root="2.16.840.1.113883.4.2" extension="1234567" /> 
            <!-- This is the organization CCN -->
            </representedOrganization>';

        $XML_documentationof_data .= '</assignedEntity>';
        $XML_documentationof_data .= '</performer>';

        $XML_documentationof_data .= '</serviceEvent>';
        $XML_documentationof_data .= '</documentationOf>';

        return $XML_documentationof_data;
    }
    /* END CARE TEAM MEMBERS */


     /* START Measure SECTION */
    public function measure_section_xml($CMS_ID='') {
       
    $XML_measure_section = '<component>
        <section>
          <!-- ***************************************************************** Measure Section ***************************************************************** -->
          <!-- This is the templateId for Measure Section -->
          <templateId root="2.16.840.1.113883.10.20.24.2.2"/>
          <!-- This is the templateId for Measure Section QDM -->
          <templateId root="2.16.840.1.113883.10.20.24.2.3"/>
          <!-- This is the LOINC code for "Measure document". This stays the same for all measure section required by QRDA standard -->
          <code code="55186-1" codeSystem="2.16.840.1.113883.6.1"/>
          <title>Measure Section</title>
          <text>
            <table border="1" width="100%">
              <thead>
                 <tr>
					<th>eMeasure Title</th>
					<th>Version specific identifier</th>
				</tr>
			   </thead>
				<tbody>';
    //if (in_array("NQF0089", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS142v8") {
        $XML_measure_section .= '<tr>
							<td>Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</td>
							<td>40280382-6963-BF5E-0169-6D6F735D0283</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="15e67110-e0e8-0137-c67b-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id extension="40280382-6963-BF5E-0169-6D6F735D0283" root="2.16.840.1.113883.4.738"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="53D6D7C3-43FB-4D24-8099-17E74C022C05"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    //if (in_array("NQF0086", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS143v8") {
        $XML_measure_section .= '<tr>
							<td>Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation</td>
							<td>40280382-6963-BF5E-0169-6D6E4A420276</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						<!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						<!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="1ac8abf0-cd54-0137-82a0-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
						<reference typeCode="REFR">
						<externalDocument classCode="DOC" moodCode="EVN">
						<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-6D6E4A420276"/>
						<!-- SHOULD This is the title of the eMeasure -->
                                    <text>Primary Open-Angle Glaucoma (POAG): Optic Nerve Evaluation</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="DB9D9F09-6B6A-4749-A8B2-8C1FDB018823"/>
						</externalDocument>
						</reference>
						</organizer>
						</entry>';
    }
    //if (in_array("NQF0055", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS131v8") {
        $XML_measure_section .= '<tr>
							<td>Diabetes: Eye Exam</td>
							<td>40280382-6963-BF5E-0169-DA28B8F63854</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="9e09e360-c66d-0137-362d-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-DA28B8F63854"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Diabetes: Eye Exam</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="D90BDAB4-B9D2-4329-9993-5C34E2C0DC66"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    //if (in_array("NQF0018", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS165v8") {
        $XML_measure_section .= '<tr>
							 <td>Controlling High Blood Pressure</td>
							  <td>40280382-6963-BF5E-0169-DA5E74BE38BF</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280382-6963-BF5E-0169-DA5E74BE38BF"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-DA5E74BE38BF"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Controlling High Blood Pressure</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="ABDC37CC-BAC6-4156-9B91-D1BE2C8B7268"/>
							</externalDocument>
						  </reference>
						</organizer>
					 </entry>';
    }

    //if (in_array("NQF0022", $con_nqf_id_arr) || in_array("NQF0022a", $con_nqf_id_arr) || in_array("NQF0022b", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS156v8") {
        $XML_measure_section .= '<tr>
            <td>Use of High-Risk Medications in the Elderly</td>
            <td>40280382-6963-BF5E-0169-E86D29C73EE1</td>
            <td></td>
          </tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
							<!-- This is the templateId for Measure Reference -->
							<templateId root="2.16.840.1.113883.10.20.24.3.98"/>
							<!-- This is the templateId for eMeasure Reference QDM -->
							<templateId root="2.16.840.1.113883.10.20.24.3.97"/>
							<id root="1.3.6.1.4.1.115" extension="c20994c0-cd4e-0137-8237-0eca209bc306"/>
							<statusCode code="completed"/>
							<!-- Containing isBranch external references -->
							<reference typeCode="REFR">
								<externalDocument classCode="DOC" moodCode="EVN">
									<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
									<id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-E86D29C73EE1"/>
									<!-- SHOULD This is the title of the eMeasure -->
									<text>Use of High-Risk Medications in the Elderly</text>
									<!-- SHOULD: setId is the eMeasure version neutral id  -->
									<setId root="A3837FF8-1ABC-4BA9-800E-FD4E7953ADBD"/>
								</externalDocument>
							</reference>
						</organizer>
					 </entry>';
    }

    //if (in_array("NQF0028a", $con_nqf_id_arr) || in_array("NQF0028b", $con_nqf_id_arr) || in_array("NQF0028", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS138v8") {
        $XML_measure_section .= '<tr>
                                <td>Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention</td>
                                <td>40280382-6963-BF5E-0169-6D717FD50298</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="eb730f10-c646-0137-30f2-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-6D717FD50298"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="40280382-6963-BF5E-0169-6D717FD50298"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    //if (in_array("NQF0419", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS68v9") {
        $XML_measure_section .= '<tr>
            <td>Documentation of Current Medications in the Medical Record</td>
            <td>40280382-68D3-A5FE-0169-0C589537118F</td>
          </tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
						<entry>
							<organizer classCode="CLUSTER" moodCode="EVN">
								<!-- This is the templateId for Measure Reference -->
								<templateId root="2.16.840.1.113883.10.20.24.3.98"/>
								<!-- This is the templateId for eMeasure Reference QDM -->
								<templateId root="2.16.840.1.113883.10.20.24.3.97"/>
								<id root="1.3.6.1.4.1.115" extension="b32e7e80-c66d-0137-362d-0eca209bc306"/>
								<statusCode code="completed"/>
								<!-- Containing isBranch external references -->
								<reference typeCode="REFR">
									<externalDocument classCode="DOC" moodCode="EVN">
										<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
										<id root="2.16.840.1.113883.4.738" extension="40280382-68D3-A5FE-0169-0C589537118F"/>
										<!-- SHOULD This is the title of the eMeasure -->
										<text>Documentation of Current Medications in the Medical Record</text>
										<!-- SHOULD: setId is the eMeasure version neutral id  -->
										<setId root="9A032D9C-3D9B-11E1-8634-00237D5BF174"/>
									</externalDocument>
								</reference>
							</organizer>
						</entry>';
    }
    //if (in_array("NQF0564", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS132v8") {
        $XML_measure_section .= '<tr>
							  <td>Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures</td>
							  <td>40280382-6963-BF5E-0169-6D6CFBE80264</td>
						</tr>';

        $XML_CLUSTER_section .= '<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="5f8866f0-c019-0137-3bbb-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-6D6CFBE80264"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="9A0339C2-3D9B-11E1-8634-00237D5BF174"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    //if (in_array("NQF0565", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
    if ($CMS_ID == "CMS133v8") {
        $XML_measure_section .= '<tr>
							  <td>Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery</td>
							  <td>40280382-6963-BF5E-0169-6D6846910246</td>
						</tr>';

        $XML_CLUSTER_section .= '<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="b3d09250-c028-0137-3bba-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-6963-BF5E-0169-6D6846910246"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="39E0424A-1727-4629-89E2-C46C2FBB3F5F"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }
    
    if ($CMS_ID == "CMS50v8") {
        $XML_measure_section .= '<tr>
							  <td>Closing the Referral Loop: Receipt of Specialist Report</td>
							  <td>40280382-667F-ECC3-0167-575C0F0447F0</td>
						</tr>';

        $XML_CLUSTER_section .= '<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="1aa695a0-c801-0137-362d-0eca209bc306"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-667F-ECC3-0167-575C0F0447F0"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Closing the Referral Loop: Receipt of Specialist Report</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="F58FC0D6-EDF5-416A-8D29-79AFBFD24DEA"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    $XML_measure_section .= '</tbody>
            </table>
          </text>';


    $XML_measure_section .= $XML_CLUSTER_section;
    $XML_measure_section .= '</section></component>';

    return $XML_measure_section;
    }
    /* END Measure SECTION */
    
}

?>
<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once(__DIR__.'/class.mur_reports.php');

class qrda extends MUR_Reports
{

    /**
     * Performance year for the eCQM calculations
     * This basically affect the list of measures applicable and te CMS version of the measures as well
     */
    protected $performance_year;

    public $provider;
    public $dtfrom;
    public $dtupto;

    /**
     * Static patient list supplied by the user;
     */
    protected $patientIds;


    /**
     * Set the required property elements
     */
    public function __construct( int $performance_year )
    {
        $this->performance_year = $performance_year;

        $this->provider 		= isset($_REQUEST['provider']) ? trim(strip_tags($_REQUEST['provider'])) : 0;
		$this->dtfrom 			= isset($_REQUEST['dtfrom']) ? trim(strip_tags($_REQUEST['dtfrom'])) : 0;
		$this->dtupto 			= isset($_REQUEST['dtupto']) ? trim(strip_tags($_REQUEST['dtupto'])) : 0;

		$this->dbdtfrom			= getDateFormatDB($this->dtfrom);
        $this->dbdtupto			= getDateFormatDB($this->dtupto);
        
        parent::__construct();
    }

    /**
     * Retrieve a particular column from the query response
     */
    function getColumn($query, $column)
    {
		$resp = [];
        $result = imw_query($query);
        
        if($result && imw_num_rows($result)>=1)
        {
            while( $rs = imw_fetch_assoc($result) )
            {
				$resp[] 	= $rs[$column];
			}
        }
        
		return $resp;
    }


    /**
     * List he qurda measures applicable for the perfromance year in context
     */
    public function list_measures() : array
    {
        /**
         * Static list of Measures for 2020
         * This colud be enchanced further
         */
        $measuresList = [
            ['id'=>1, 'nqf'=>'NA', 'cms'=>'CMS165v8', 'measure'=>'Controlling High Blood Pressure', 'parent'=>NULL, 'is_opthalmic'=>0],
            ['id'=>2, 'nqf'=>'0565e', 'cms'=>'CMS133v8', 'measure'=>'Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery', 'parent'=>NULL, 'is_opthalmic'=>0],
            ['id'=>3, 'nqf'=>'0564e', 'cms'=>'CMS132v8', 'measure'=>'Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures', 'parent'=>NULL, 'is_opthalmic'=>0],
            ['id'=>4, 'nqf'=>'0419e', 'cms'=>'CMS68v9', 'measure'=>'Documentation of Current Medications in the Medical Record', 'parent'=>NULL, 'is_opthalmic'=>0],
            ['id'=>8, 'nqf'=>'NA', 'cms'=>'CMS156v8', 'measure'=>'Use of High-Risk Medications in the Elderly', 'parent'=>NULL, 'is_opthalmic'=>0],
            ['id'=>11, 'nqf'=>'0028e', 'cms'=>'CMS138v8', 'measure'=>'Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention', 'parent'=>NULL, 'is_opthalmic'=>0],
            ['id'=>15, 'nqf'=>'NA', 'cms'=>'CMS50v8', 'measure'=>'Closing the Referral Loop: Receipt of Specialist Report', 'parent'=>NULL, 'is_opthalmic'=>0],
            /*['id'=>9, 'nqf'=>'', 'cms'=>'', 'measure'=>'Measure I (1+)', 'parent'=>8, 'is_opthalmic'=>0],
            ['id'=>10, 'nqf'=>'', 'cms'=>'', 'measure'=>'Measure II (2+)', 'parent'=>8, 'is_opthalmic'=>0],
            ['id'=>12, 'nqf'=>'', 'cms'=>'', 'measure'=>'Measure I (1+)', 'parent'=>11, 'is_opthalmic'=>0],
            ['id'=>13, 'nqf'=>'', 'cms'=>'', 'measure'=>'Measure II (2+)', 'parent'=>11, 'is_opthalmic'=>0],
            ['id'=>14, 'nqf'=>'', 'cms'=>'', 'measure'=>'Measure III (3+)', 'parent'=>11, 'is_opthalmic'=>0],*/
            ['id'=>5, 'nqf'=>'0086e', 'cms'=>'CMS143v8', 'measure'=>'Primary Open-Angle Glaucoma (POAG): Optic Nerve Evaluation', 'parent'=>NULL, 'is_opthalmic'=>1],
            ['id'=>6, 'nqf'=>'0089e', 'cms'=>'CMS142v8', 'measure'=>'Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care', 'parent'=>NULL, 'is_opthalmic'=>1],
            ['id'=>7, 'nqf'=>'NA', 'cms'=>'CMS131v8', 'measure'=>'Diabetes: Eye Exam', 'parent'=>NULL, 'is_opthalmic'=>1],
        ];

        $measures = array();

        // Reset the array pointer to the first element
        reset($measuresList);

        while( $row = current($measuresList) )
        {
            $parentId = ($row['parent']) ?? false;

            if( $parentId )
            {
                /** Locate if child entry already exists and find the latest one */
                $values = array_column($measures, 'parent');
                krsort($values);
                $offset = array_search( $parentId, $values);

                /** Locate parent record, if sibling does not exists */
                $offset = ($offset) ? $offset : array_search( $parentId, array_column($measures, 'id') );

                /** Inject child element at the desired location  */
                array_splice( $measures, $offset+1, 0, array($row) );

            }
            else
            {
                /** Append to the master list */
                array_push($measures, $row);
            }

            next($measuresList);
        }

        if( count($measures) < 1 )
        {
            throw new Exception('Measures for the performance year does not exists in the definition table.');
        }

        return $measures;
    }


    /**
     * GET Initial Patient population values for eCQM measures
     */
    public function getipop( $patientids = '' )
    {
        $this->patientIds = $patientids;

        $resp = array();

        /** Controlling High Blood Pressure */
        $resp['CMS165v8'] = $this->cms165v8();
        
        /** Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery */
        $resp['CMS133v8'] = $this->cms133v8();

        /** Closing the Referral Loop: Receipt of Specialist Report */
        $resp['CMS50v8'] = $this->cms50v8();

        /** Primary Open-Angle Glaucoma (POAG): Optic Nerve Evaluation */
        $resp['CMS143v8'] = $this->cms143v8();
        
        /** Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures */
        $resp['CMS132v8'] = $this->cms132v8();


        /** Documentation of Current Medications in the Medical Record */
        $resp['CMS68v9'] = $this->cms68v9();

        /** Use of High-Risk Medications in the Elderly */
        $resp['CMS156v8'] = $this->cms156v8();
        
        /** Diabetes: Eye Exam */
        $resp['CMS131v8'] = $this->cms131v8();
        
        /** Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care */
        $resp['CMS142v8'] = $this->cms142v8();
        
        /** Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention */
        $resp['CMS138v8'] = $this->cms138v8();

        return $resp;
    }

    /**
     * Controlling high Blood Pressure
     */
    private function cms165v8()
    {
        /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => [],
            'ICD10CM' => [],
            'ICD9CM' => []
        ];

        $sql = "SELECT `Code` AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` = '2.16.840.1.113883.3.464.1003.104.12.1011'
                    AND `CMS_ID` = 'CMS165v8'
                    AND `Code_System` IN ('ICD10CM', 'ICD9CM', 'SNOMEDCT')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        $snomedCodes = implode('", "', $codes['SNOMEDCT']);
        $snomedCodes = ( !empty($snomedCodes) ) ? '"'.trim($snomedCodes).'"' : '';

        $icd10Codes = implode('|', $codes['ICD10CM']);
        $icd9Codes = implode('|', $codes['ICD9CM']);

        /** List patient based on Agae */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18-85');

        /** Trim down the list of futehr conditions */
		list($year,$month,$day) = explode('-',$this->dbdtfrom);
        $chkDtEnd = date('Y-m-d',mktime(0,0,0,$month+6,$day,$year));	/*Six months from start of measurement period*/
        

        /**
         * Create temperory table to list lates unique records from both live and log tables.
         */
        $query = "CREATE TEMPORARY TABLE `cms165v8_diagnosis`
                SELECT `p1`.`id`,
                    `p1`.`problem_id`,
                    `p1`.`pt_id`,
                    `p1`.`onset_date`,
                    DATE_FORMAT(`p1`.`statusDateTime`, '%Y-%m-%d') AS 'statusDate',
                    `p1`.`status`
                FROM `pt_problem_list_log` `p1`
                INNER JOIN (
                    SELECT MAX(`p1`.`id`) AS id
                    FROM `pt_problem_list_log` `p1`
                    GROUP BY `p1`.`problem_id`
                    ) `M`
                    ON `M`.`id` = `p1`.`id`
                    AND `p1`.`pt_id` IN (".$totalPtIDs.")";
        imw_query( $query );
        
        /**
         * Final query to pull data from both live and temperoty table 
         */
        $query = "SELECT `p`.`pt_id`
                FROM `pt_problem_list` `p`
                LEFT JOIN `cms165v8_diagnosis` `l`
                    ON (`p`.`id` = `l`.`problem_id`)
                WHERE `p`.`pt_id` IN (".$totalPtIDs.")
                    AND (
                        `p`.`problem_name` RLIKE '".$icd10Codes."'
                        OR `p`.`problem_name` RLIKE '".$icd9Codes."'
                        OR `p`.`ccda_code` IN (".$snomedCodes.")
                        )
                    AND (
                        `p`.`onset_date` BETWEEN '".$this->dbdtfrom."'
                            AND '".$chkDtEnd."'
                        OR (
                            `p`.`onset_date` < '".$this->dbdtfrom."'
                            AND (
                                (
                                    LOWER(`l`.`status`) = 'active'
                                    OR `l`.`status` IS NULL
                                    )
                                OR `l`.`statusDate` >= '".$this->dbdtfrom."'
                                )
                            )
                        )";
        
        $ptIDs = $this->getPtIdFun( $query,'pt_id' );
        $ptIDs = implode(',', $ptIDs);


        //getting patients having 1 or more visits.
        return $this->get_patients_by_visits( $ptIDs, 1 );
    }



    /** 
     * Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery
     **/
    private function cms133v8()
    {

       /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => [],
            'CPT' => []
        ];

        $sql = "SELECT `Code` AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` = '2.16.840.1.113883.3.526.3.1411'
                    AND `CMS_ID` = 'CMS133v8'
                    AND `Code_System` IN ('CPT', 'SNOMEDCT')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        $snomedCodes = implode('", "', $codes['SNOMEDCT']);
        $snomedCodes = ( !empty($snomedCodes) ) ? '"'.trim($snomedCodes).'"' : '';

        // $cptCodes = implode('|', $codes['CPT']);
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18');


        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;

        /* Pull final patients list */
        $query = "SELECT `pid`
                FROM `lists`
                WHERE `pid` IN (".$totalPtIDs.")
                    AND `type` = 5
                    AND `ccda_code` IN (".$snomedCodes.")
                    AND (`begdate` BETWEEN '".$minBeginDate."'
                            AND '".$maxBeginDate."'
                        )";
        $ptIDs = $this->getPtIdFun( $query,'pid' );

        return $ptIDs;
    }


    /**
     * Closing the Referral Loop: Receipt of Specialist Report 
     */
    private function cms50v8()
    {

       /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => []
        ];

        $sql = "SELECT `Code` AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` = '2.16.840.1.113883.3.464.1003.101.12.1046'
                    AND `CMS_ID` = 'CMS50v8'
                    AND `Code_System` = 'SNOMEDCT'";

        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        $snomedCodes = implode('|', $codes['SNOMEDCT']);

        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;

        /* Pull final patients list */
        $query = "SELECT `ca`.`patient_id`
                FROM `chart_assessment_plans` `ca`
                INNER JOIN `chart_master_table` `cm`
                    ON (`ca`.`form_id` = `cm`.`id`)
                INNER JOIN `patient_data` `pd`
                    ON (`pd`.`id` = `cm`.`patient_id`)
                WHERE `ca`.`refer_to_code` RLIKE '".$snomedCodes."'
                    AND (
                        `cm`.`date_of_service` BETWEEN '".$minBeginDate."'
                            AND '".$maxBeginDate."'
                        )
                    AND `ca`.`refer_to_id` > 0";

        /** If data queried for a specific patient only */
        if( !is_null($this->patientIds) && !empty( $this->patientIds) )
        {
            $query .= " AND `ca`.`patient_id` IN(".$this->patientIds.")";
        }
       
        $ptIDs = $this->getPtIdFun( $query,'patient_id' );
        $ptIDs = implode(',', $ptIDs);

       //getting patients having 1 or more visits.
       return $this->get_patients_by_visits( $ptIDs, 1 );
    }



    /**
     * Primary Open-Angle Glaucoma (POAG): Optic Nerve Evaluation
     */
    private function cms143v8()
    {
        /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => [],
            'ICD10CM' => [],
            'ICD9CM' => []
        ];

        $sql = "SELECT `Code` AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` = '2.16.840.1.113883.3.526.3.326'
                    AND `CMS_ID` = 'CMS143v8'
                    AND `Code_System` IN ('ICD10CM', 'ICD9CM', 'SNOMEDCT')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        $snomedCodes = implode('", "', $codes['SNOMEDCT']);
        $snomedCodes = ( !empty($snomedCodes) ) ? '"'.trim($snomedCodes).'"' : '';

        $icd10Codes = implode('|', $codes['ICD10CM']);
        $icd9Codes = implode('|', $codes['ICD9CM']);
        unset($codes);


        /**
         * List CPT codes for the supported Appointment/Visit Types
         */
        $codes = [
            'CPT' => []
        ];

        $sql = "SELECT `Code` AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `CMS_ID` = 'CMS143v8'
                    AND `Code_System` = 'CPT'
                    AND `Value_Set_OID` IN ('2.16.840.1.113883.3.464.1003.101.12.1001', '2.16.840.1.113883.3.526.3.1285', '2.16.840.1.113883.3.464.1003.101.12.1008', '2.16.840.1.113883.3.464.1003.101.12.1012', '2.16.840.1.113883.3.464.1003.101.12.1014')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        /* Final List of Visit codes for the measure */
        $visitCodes = implode('", "', $codes['CPT']);
        $visitCodes = ( !empty($visitCodes) ) ? '"'.trim($visitCodes).'"' : '';

        /** List patient based on Agae */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18');

        /** Trim down the list of futehr conditions */
		list($year,$month,$day) = explode('-',$this->dbdtfrom);
        
        /**
         * Create temperory table to list latest unique records from both live and log tables.
         */
        $query = "CREATE TEMPORARY TABLE `cms143v8_diagnosis`
                SELECT `p1`.`id`,
                    `p1`.`problem_id`,
                    `p1`.`pt_id`,
                    `p1`.`onset_date`,
                    DATE_FORMAT(`p1`.`statusDateTime`, '%Y-%m-%d') AS 'statusDate',
                    `p1`.`status`
                FROM `pt_problem_list_log` `p1`
                INNER JOIN (
                    SELECT MAX(`p1`.`id`) AS id
                    FROM `pt_problem_list_log` `p1`
                    GROUP BY `p1`.`problem_id`
                    ) `M`
                    ON `M`.`id` = `p1`.`id`
                    AND `p1`.`pt_id` IN (".$totalPtIDs.")";
        imw_query( $query );
        
        /**
         * Final query to pull data from both live and temporary table 
         */
        $query = "SELECT DISTINCT (`prob`.`pt_id`) AS 'pt_id'
                FROM `pt_problem_list` `prob`
                LEFT JOIN `cms143v8_diagnosis` `log`
                    ON (`prob`.`id` = `log`.`problem_id`)
                INNER JOIN `schedule_appointments` `sa`
                    ON (`prob`.`pt_id` = `sa`.`sa_patient_id`)
                INNER JOIN `superbill` `sb`
                    ON (
                            `sa`.`id` = `sb`.`sch_app_id`
                            AND `sa`.`sa_patient_id` = `sb`.`patientId`
                            )
                INNER JOIN `procedureinfo` `pi`
                    ON (`sb`.`idSuperBill` = `pi`.`idSuperBill`)
                WHERE `prob`.`pt_id` IN (".$totalPtIDs.")
                    AND (
                        `prob`.`ccda_code` IN (".$snomedCodes.")
                        OR `prob`.`problem_name` RLIKE '".$icd10Codes."'
                        OR `prob`.`problem_name` RLIKE '".$icd9Codes."'
                        )
                    AND `pi`.`cptCode` IN (".$visitCodes.")
                    AND (
                        `sa`.`sa_app_start_date` BETWEEN '".$this->dbdtfrom."'
                            AND '".$this->dbdtupto."'
                        )
                    AND (
                        (
                            LOWER(`log`.`status`) = 'active'
                            OR `log`.`status` IS NULL
                            )
                        OR `log`.`statusDate` >= `sa`.`sa_app_start_date`
                        )
                ";
        
        $ptIDs = $this->getPtIdFun( $query,'pt_id' );

        return $ptIDs;
    }
    
    
    /** 
     * Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures
     **/
    private function cms132v8()
    {

       /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => [],
            'CPT' => []
        ];

        $sql = "SELECT `Code` AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` IN ('2.16.840.1.113883.3.526.3.1408', '2.16.840.1.113883.3.526.3.1411', '2.16.840.1.113883.3.526.3.1422', '2.16.840.1.113883.3.526.3.1429', '2.16.840.1.113883.3.526.3.1436', '2.16.840.1.113883.3.526.3.1437', '2.16.840.1.113883.3.526.3.1439', '2.16.840.1.113883.3.526.3.1440', '2.16.840.1.113883.3.526.3.1447')
                    AND `CMS_ID` = 'CMS132v8'
                    AND `Code_System` IN ('CPT', 'SNOMEDCT')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        $snomedCodes = implode('", "', $codes['SNOMEDCT']);
        $snomedCodes = ( !empty($snomedCodes) ) ? '"'.trim($snomedCodes).'"' : '';

        // $cptCodes = implode('|', $codes['CPT']);
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18');


        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;
        
        /* Pull final patients list */
        $query = "SELECT `pid`
                FROM `lists`
                WHERE `pid` IN (".$totalPtIDs.")
                    AND `type` = 5
                    AND `ccda_code` IN (".$snomedCodes.")
                    AND (`begdate` BETWEEN '".$minBeginDate."'
                            AND '".$maxBeginDate."'
                        )";
        
        
        $ptIDs = $this->getPtIdFun( $query,'pid' );

        return $ptIDs;
    }
      

    /**
     * Documentation of Current Medications in the Medical Record
     */
    private function cms68v9()
    {
        /**
         * Retrieve Valueset 
         */
        $codes = [];

        $sql = "SELECT `Code` AS 'code'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` = '2.16.840.1.113883.3.600.1.1834'
                    AND `CMS_ID` = 'CMS68v9'
                    AND `Code_System` IN('CPT', 'SNOMEDCT', 'HCPCS')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                array_push($codes, $row['code']);
            }
        }


        $cptCodes = implode('", "', $codes);
        $cptCodes = ( !empty($cptCodes) ) ? '"'.trim($cptCodes).'"' : '';
        unset($codes);
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18');


        $sql = "SELECT sa.sa_patient_id AS 'patient_id'
                FROM schedule_appointments sa
                INNER JOIN superbill sb
                    ON (
                            sa.id = sb.sch_app_id
                            AND sa.sa_patient_id = sb.patientId
                            )
                INNER JOIN procedureinfo pi
                    ON (sb.idSuperBill = pi.idSuperBill)
                WHERE sa.sa_patient_id IN ($totalPtIDs)
                    AND (
                        sa.sa_app_start_date BETWEEN '$this->dbdtfrom'
                            AND '$this->dbdtupto'
                        )
                    AND pi.cptCode IN ($cptCodes)
                ";
        
        $pIds = $this->getColumn($sql, 'patient_id');
        
        return $pIds;
    }
    
    
    /** 
     * Use of High-Risk Medications in the Elderly
     **/
    private function cms156v8()
    {

       /**
         * Retrieve Valueset 
         */
        $codes = [
            'RXNORM' => []
        ];

        $sql = "SELECT DISTINCT(`Code`) AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` IN ('2.16.840.1.113883.3.464.1003.196.12.1253', '2.16.840.1.113883.3.464.1003.196.12.1254', '2.16.840.1.113883.3.464.1003.196.12.1272')
                    AND `CMS_ID` = 'CMS156v8'
                    AND `Code_System` IN ('RXNORM')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        $rxnormCodes = implode('", "', $codes['RXNORM']);
        $rxnormCodes = ( !empty($rxnormCodes) ) ? '"'.trim($rxnormCodes).'"' : '';

        /**
         * List CPT codes for the supported Appointment/Visit Types
         */
        $codes = [
            'CPT' => []
        ];

        $sql = "SELECT DISTINCT(`Code`) AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `CMS_ID` = 'CMS156v8'
                    AND `Code_System` = 'CPT'
                    AND `Value_Set_OID` IN ('2.16.840.1.113883.3.526.3.1240', '2.16.840.1.113883.3.464.1003.101.12.1014', '2.16.840.1.113883.3.464.1003.101.12.1013', '2.16.840.1.113883.3.666.5.307', '2.16.840.1.113883.3.464.1003.101.12.1016', '2.16.840.1.113883.3.464.1003.101.12.1012', '2.16.840.1.113883.3.464.1003.101.12.1001', '2.16.840.1.113883.3.464.1003.101.11.1206', '2.16.840.1.113883.3.464.1003.101.12.1025', '2.16.840.1.113883.3.464.1003.101.12.1023')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }

        /* Final List of Visit codes for the measure */
        $visitCodes = implode('", "', $codes['CPT']);
        $visitCodes = ( !empty($visitCodes) ) ? '"'.trim($visitCodes).'"' : '';
        
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'65');


        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;
        
        /* Pull final patients list */
        $query = "SELECT DISTINCT(`sb`.`patientId`) AS 'pt_id' 
                FROM `superbill` `sb` 
                INNER JOIN `procedureinfo` `pi` ON(`sb`.`idSuperBill` = `pi`.`idSuperBill`) 
                WHERE `sb`.`patientId` IN (".$totalPtIDs.")
                AND `pi`.`cptCode` IN (".$visitCodes.") 
                AND (`sb`.`dateOfService` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."')  
                ";
        
        $ptIDs = $this->getPtIdFun( $query,'pt_id' );
        
        return $ptIDs;
    }
    
    
    
    /** 
     * Diabetes: Eye Exam
     **/
    private function cms131v8()
    {
       /**
         * Retrieve Valueset 
         */
        $codes = [];      
        
        $sql = "SELECT DISTINCT(`Code`) AS 'code'
                FROM `cqm_v8_valueset`
                WHERE `CMS_ID` = 'CMS131v8'
                    AND `Code_System` IN('CPT', 'SNOMEDCT', 'HCPCS')
                    AND `Value_Set_OID` IN ('2.16.840.1.113883.3.464.1003.101.12.1083', '2.16.840.1.113883.3.526.3.1240', '2.16.840.1.113883.3.464.1003.101.12.1014', '2.16.840.1.113883.3.464.1003.101.12.1085', '2.16.840.1.113883.3.666.5.307', '2.16.840.1.113883.3.464.1003.101.12.1088', '2.16.840.1.113883.3.464.1003.101.12.1016', '2.16.840.1.113883.3.464.1003.101.12.1084', '2.16.840.1.113883.3.464.1003.101.12.1012', '2.16.840.1.113883.3.464.1003.101.12.1086', '2.16.840.1.113883.3.464.1003.101.12.1001', '2.16.840.1.113883.3.526.3.1285', '2.16.840.1.113883.3.464.1003.101.12.1087', '2.16.840.1.113883.3.464.1003.101.12.1025', '2.16.840.1.113883.3.464.1003.101.12.1023')";
        $resp = imw_query($sql);  
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                array_push($codes, $row['code']);
            }
        }
        
        $cptCodes = implode('", "', $codes);
        $cptCodes = ( !empty($cptCodes) ) ? '"'.trim($cptCodes).'"' : '';
        unset($codes);      
        
        /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => [],
            'ICD10CM' => [],
            'ICD9CM' => []
        ];
        
        $sql = "SELECT DISTINCT(`Code`) AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `CMS_ID` = 'CMS131v8'
                    AND `Code_System` IN ('ICD10CM', 'ICD9CM', 'SNOMEDCT')
                    AND `Value_Set_OID` IN ('2.16.840.1.113883.3.464.1003.103.12.1001', '2.16.840.1.113883.3.526.3.327', '2.16.840.1.113883.3.464.1003.113.12.1074')";
        $resp = imw_query($sql);      
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }
        
        $snomedCodes = implode('", "', $codes['SNOMEDCT']);
        $snomedCodes = ( !empty($snomedCodes) ) ? '"'.trim($snomedCodes).'"' : '';
        
        $icd10Codes = implode('|', $codes['ICD10CM']);
        $icd9Codes = implode('|', $codes['ICD9CM']);           
        
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18-75');    
        
        
        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;     
        
        
        /**
         * Create temporary table to list latest unique records from both live and log tables.
         */
        $query = "CREATE TEMPORARY TABLE `cms131v8_diagnosis`
                SELECT `p1`.`id`,
                    `p1`.`problem_id`,
                    `p1`.`pt_id`,
                    `p1`.`onset_date`,
                    DATE_FORMAT(`p1`.`statusDateTime`, '%Y-%m-%d') AS 'statusDate',
                    `p1`.`status`
                FROM `pt_problem_list_log` `p1`
                INNER JOIN (
                    SELECT MAX(`p1`.`id`) AS id
                    FROM `pt_problem_list_log` `p1`
                    GROUP BY `p1`.`problem_id`
                    ) `M`
                    ON `M`.`id` = `p1`.`id`
                    AND `p1`.`pt_id` IN (".$totalPtIDs.")";
        imw_query( $query );
        
        
        $query = "SELECT DISTINCT(`p`.`pt_id`) AS `pt_id`
                FROM `pt_problem_list` `p`
                LEFT JOIN `cms131v8_diagnosis` `log`
                    ON (`p`.`id` = `log`.`problem_id`)
                INNER JOIN `schedule_appointments` `sa`
                    ON (`sa`.`sa_patient_id` = `p`.`pt_id`)
                INNER JOIN `superbill` `sb`
                    ON (`sa`.`id` = `sb`.`sch_app_id`
                            AND `sa`.`sa_patient_id` = `sb`.`patientId`
                        )
                INNER JOIN `procedureinfo` `pi`
                    ON (`sb`.`idSuperBill` = `pi`.`idSuperBill`)
                WHERE `p`.`pt_id` IN (".$totalPtIDs.")
                    AND (
                        `p`.`problem_name` RLIKE '".$icd10Codes."'
                        OR `p`.`problem_name` RLIKE '".$icd9Codes."'
                        OR `p`.`ccda_code` IN (".$snomedCodes.")
                        )
                    AND (
                        `p`.`onset_date` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."'
                        OR (`p`.`onset_date` <= '".$maxBeginDate."')
                        OR `log`.`statusDate` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."'
                        )
                    AND (
                        LOWER(`p`.`status`) = 'active' 
                        OR (
                            LOWER(`log`.`status`) = 'active'
                            OR `log`.`status` IS NULL
                            )
                        )
                    AND ( 
                        `sa`.`sa_app_start_date` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."'
                        )
                    AND `pi`.`cptCode` IN (".$cptCodes.") ";        
        
        
        $ptIDs = $this->getPtIdFun( $query,'pt_id' ); 
        
        return $ptIDs;        
    }        
        
    
    /** 
     * Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care
     **/
    private function cms142v8()
    {
       /**
         * Retrieve Valueset 
         */
        $codes = [];      
        
        $sql = "SELECT DISTINCT(`Code`) AS 'code'
                FROM `cqm_v8_valueset`
                WHERE `CMS_ID` = 'CMS142v8'
                    AND `Code_System` IN('CPT', 'SNOMEDCT', 'HCPCS')
                    AND `Value_Set_OID` IN ('2.16.840.1.113883.3.464.1003.101.12.1014', '2.16.840.1.113883.3.464.1003.101.12.1012', '2.16.840.1.113883.3.464.1003.101.12.1001', '2.16.840.1.113883.3.526.3.1285', '2.16.840.1.113883.3.464.1003.101.12.1008')";
        $resp = imw_query($sql);  
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                array_push($codes, $row['code']);
            }
        }
        
        $cptCodes = implode('", "', $codes);
        $cptCodes = ( !empty($cptCodes) ) ? '"'.trim($cptCodes).'"' : '';
        unset($codes);      
        
        /**
         * Retrieve Valueset 
         */
        $codes = [
            'SNOMEDCT' => [],
            'ICD10CM' => [],
            'ICD9CM' => []
        ];
        
        $sql = "SELECT DISTINCT(`Code`) AS 'code',
                    `Code_System` AS 'code_system'
                FROM `cqm_v8_valueset`
                WHERE `CMS_ID` = 'CMS142v8'
                    AND `Code_System` IN ('ICD10CM', 'ICD9CM', 'SNOMEDCT')
                    AND `Value_Set_OID` = '2.16.840.1.113883.3.526.3.327' ";
        $resp = imw_query($sql);      
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                if( array_key_exists($row['code_system'], $codes) )
                {
                    array_push($codes[$row['code_system']], $row['code']);
                }
            }
        }
        
        $snomedCodes = implode('", "', $codes['SNOMEDCT']);
        $snomedCodes = ( !empty($snomedCodes) ) ? '"'.trim($snomedCodes).'"' : '';
        
        $icd10Codes = implode('|', $codes['ICD10CM']);
        $icd9Codes = implode('|', $codes['ICD9CM']);           
        
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18');    
        
        
        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;     
        
        
        /**
         * Create temporary table to list latest unique records from both live and log tables.
         */
        $query = "CREATE TEMPORARY TABLE `cms142v8_diagnosis`
                SELECT `p1`.`id`,
                    `p1`.`problem_id`,
                    `p1`.`pt_id`,
                    `p1`.`onset_date`,
                    DATE_FORMAT(`p1`.`statusDateTime`, '%Y-%m-%d') AS 'statusDate',
                    `p1`.`status`
                FROM `pt_problem_list_log` `p1`
                INNER JOIN (
                    SELECT MAX(`p1`.`id`) AS id
                    FROM `pt_problem_list_log` `p1`
                    GROUP BY `p1`.`problem_id`
                    ) `M`
                    ON `M`.`id` = `p1`.`id`
                    AND `p1`.`pt_id` IN (".$totalPtIDs.")";
        imw_query( $query );
        
        
        $query = "SELECT DISTINCT(`p`.`pt_id`) AS `pt_id`
                FROM `pt_problem_list` `p`
                LEFT JOIN `cms142v8_diagnosis` `log`
                    ON (`p`.`id` = `log`.`problem_id`)
                INNER JOIN `schedule_appointments` `sa`
                    ON (`sa`.`sa_patient_id` = `p`.`pt_id`)
                INNER JOIN `superbill` `sb`
                    ON (`sa`.`id` = `sb`.`sch_app_id`
                            AND `sa`.`sa_patient_id` = `sb`.`patientId`
                        )
                INNER JOIN `procedureinfo` `pi`
                    ON (`sb`.`idSuperBill` = `pi`.`idSuperBill`)
                WHERE `p`.`pt_id` IN (".$totalPtIDs.")
                    AND (
                        `p`.`problem_name` RLIKE '".$icd10Codes."'
                        OR `p`.`problem_name` RLIKE '".$icd9Codes."'
                        OR `p`.`ccda_code` IN (".$snomedCodes.")
                        )
                    AND (
                        `p`.`onset_date` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."'
                        OR (`p`.`onset_date` <= '".$maxBeginDate."')
                        OR `log`.`statusDate` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."'
                        )
                    AND (
                        LOWER(`p`.`status`) = 'active' 
                        OR (
                            LOWER(`log`.`status`) = 'active'
                            OR `log`.`status` IS NULL
                            )
                        )
                    AND ( 
                        `sa`.`sa_app_start_date` BETWEEN '".$minBeginDate."' AND '".$maxBeginDate."'
                        )
                    AND `pi`.`cptCode` IN (".$cptCodes.") ";        
        
        
        $ptIDs = $this->getPtIdFun( $query,'pt_id' ); 
        
        return $ptIDs;        
    }        
        
    
    /**
     * Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention
     */
    private function cms138v8()
    {
        /**
         * Retrieve Valueset 
         */
        $codes = [];

        $sql = "SELECT DISTINCT(`Code`) AS 'code'
                FROM `cqm_v8_valueset`
                WHERE `Value_Set_OID` IN('2.16.840.1.113883.3.526.3.1240', '2.16.840.1.113883.3.526.3.1020', '2.16.840.1.113883.3.526.3.1245', '2.16.840.1.113883.3.526.3.1529', '2.16.840.1.113883.3.464.1003.101.12.1016', '2.16.840.1.113883.3.526.3.1011', '2.16.840.1.113883.3.464.1003.101.12.1001', '2.16.840.1.113883.3.526.3.1285', '2.16.840.1.113883.3.464.1003.101.12.1025', '2.16.840.1.113883.3.464.1003.101.12.1027', '2.16.840.1.113883.3.464.1003.101.12.1030', '2.16.840.1.113883.3.464.1003.101.12.1026', '2.16.840.1.113883.3.464.1003.101.12.1023', '2.16.840.1.113883.3.526.3.1492', '2.16.840.1.113883.3.526.3.1496', '2.16.840.1.113883.3.526.3.1141', '2.16.840.1.113883.3.526.3.1530')
                    AND `CMS_ID` = 'CMS138v8'
                    AND `Code_System` IN('CPT', 'SNOMEDCT', 'HCPCS')";
        $resp = imw_query($sql);
        
        if( $resp && imw_num_rows($resp) > 0 )
        {
            while( $row = imw_fetch_assoc($resp) )
            {
                array_push($codes, $row['code']);
            }
        }


        $cptCodes = implode('", "', $codes);
        $cptCodes = ( !empty($cptCodes) ) ? '"'.trim($cptCodes).'"' : '';
        unset($codes);
        
        /** List patient based on Age */
        $totalPtIDs = ( !is_null($this->patientIds) && !empty($this->patientIds)) ? $this->patientIds : $this->aged_get_denominator($this->provider,'18');

        /**
         * Begin date range
         */
        $minBeginDate = $this->dbdtfrom;
        $maxBeginDate = $this->dbdtupto;  
        

        $sql = "SELECT count(`sa`.`id`) AS `cnt`, `sa`.`sa_patient_id` AS 'patient_id'
                FROM `schedule_appointments` `sa`
                INNER JOIN `superbill` `sb`
                    ON (
                            `sa`.`id` = `sb`.`sch_app_id`
                            AND `sa`.`sa_patient_id` = `sb`.`patientId`
                            )
                INNER JOIN `procedureinfo` `pi`
                    ON (`sb`.`idSuperBill` = `pi`.`idSuperBill`)
                WHERE `sa`.`sa_patient_id` IN (".$totalPtIDs.")
                    AND (
                        `sa`.`sa_app_start_date` BETWEEN '".$minBeginDate."'
                            AND '".$maxBeginDate."'
                        )
                    AND `pi`.`cptCode` IN (".$cptCodes.") 
                GROUP BY `patient_id` HAVING `cnt` >= 2
                UNION
                SELECT count(`sa`.`id`) AS `cnt`, `sa`.`sa_patient_id` AS 'patient_id'
                FROM `schedule_appointments` `sa`
                INNER JOIN superbill `sb`
                    ON (
                            `sa`.`id` = `sb`.`sch_app_id`
                            AND `sa`.`sa_patient_id` = `sb`.`patientId`
                            )
                INNER JOIN `procedureinfo` `pi`
                    ON (`sb`.`idSuperBill` = `pi`.`idSuperBill`)
                WHERE `sa`.`sa_patient_id` IN (".$totalPtIDs.")
                    AND (
                        `sa`.`sa_app_start_date` BETWEEN '".$minBeginDate."'
                            AND '".$maxBeginDate."'
                        )
                    AND `pi`.`cptCode` IN (".$cptCodes.") 
                GROUP BY `patient_id` HAVING `cnt` >= 1
                ";
        
        $pIds = $this->getColumn($sql, 'patient_id');
        
        return $pIds;
    }
    
    
    
}
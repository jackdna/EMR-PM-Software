<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php 
/*
File: class.sparcs.php
Purpose: Contains functions used in electronic billing.
Access Type: Include file 
*/
require_once(dirname(__FILE__).'/SaveFile.php');
class SPARCS{
	private $oSaveFile;
	function __construct(){ //constructor
		$this->oSaveFile		= new SaveFile();
		
		
	}

	/*TO PADD A STRING/WORD WITH SPECIFI CHARACTERS*/	
	function padd_string($str,$totLength,$paddwith=' ',$prefixOrSuffix = 'prefix'){
		$diff = $totLength - strlen($str);
		$padd = '';
		if($diff > 0){
			$padd = str_repeat($paddwith,$diff);
		}
		if($prefixOrSuffix=='prefix'){return $padd.$str;}else{return $str.$padd;}
	}
	
	function get_charge_list_id_details($chl_id){
		$q = "SELECT a.patient_id, a.date_of_service, a.primaryInsuranceCoId, a.secondaryInsuranceCoId, a.totalAmt, b.charge_list_detail_id, 
		b.procCode, b.start_date, b.type_of_service, b.place_of_service, 
		a.primary_provider_id_for_reports, b.procCharges, 
		b.diagnosis_id1, b.diagnosis_id2, b.diagnosis_id3, b.diagnosis_id4, b.paidForProc, b.balForProc, b.idSuperBill, b.proc_code_essi, 
		b.modifier_id1, b.del_status,a.case_type_id 
	FROM patient_charge_list a 
	LEFT JOIN patient_charge_list_details b ON a.charge_list_id = b.charge_list_id 
	WHERE a.charge_list_id = '$chl_id' AND b.procCharges !=0 
		AND b.del_status != '1' 
	GROUP BY a.charge_list_id 
	ORDER BY b.procCharges";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			return imw_fetch_assoc($res);
		}
		return false;
	}
	
	/*GETTING PATIENT INSURNACE DETAILS ACCORDING TO CASE, DOS, TYPE*/
	function get_patient_insurance($case_type_id,$PtId,$type,$DOS){
		$q = "SELECT id.*, ic.contact_address AS InsCompAddress, ic.City AS InsCompCity, ic.State AS InsCompState, 
		  CONCAT(ic.Zip,ic.zip_ext) AS InsCompZip, ic.phone AS InsCompPhone, ic.id AS InsCompId, 
		  ic.Payer_id AS InsCompPayerId, ic.Payer_id_pro AS InsCompPayerIdPro, ic.BatchFile, 
		  ic.in_house_code, ic.name as InsName, ic.Reciever_id, ic.payer_type, ic.claim_type, ic.ins_type, 
		  ic.Insurance_payment, ic.secondary_payment_method, ic.institutional_type  FROM insurance_data id 
		JOIN insurance_companies ic ON ic.id = id.provider 
		WHERE id.ins_caseid = '$case_type_id' AND id.pid = '$PtId' 
		AND LOWER(id.type) = '".strtolower($type)."' AND id.provider > '0' 
		AND ic.ins_del_status  = '0' AND ic.name NOT LIKE 'self pay' 
		AND DATE_FORMAT(id.effective_date,'%Y-%m-%d') <= '$DOS' 
		AND (id.expiration_date = '0000-00-00 00:00:00' OR DATE_FORMAT(id.expiration_date,'%Y-%m-%d') >= '$DOS') 
		ORDER BY id.actInsComp DESC LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)){
			return imw_fetch_assoc($res);
		}
		return false;
	}	
	
	function cptDisplayCheck($procCode, $avoid_cpts)
	{
		$res = imw_query("SELECT cpt4_code FROM cpt_fee_tbl WHERE cpt_fee_id = ".$procCode." LIMIT 0,1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			$cpt = $rs['cpt4_code'];
			if(!empty($cpt)) return $this->cptDisplay($cpt, $avoid_cpts);
			else return false;
		}else return false;
	}
	
	function cptDisplay($cpt, $avoid_cpts=array()){
		if(in_array($cpt,$avoid_cpts)) return false;
		return true;
	}

	/*GETTING PATIENT DETAILS FOR PROVIDED LIST*/
	function get_patient_details($patient_id){
		$return = false;
		$q = "SELECT fname, lname, mname, DOB, street, street2, postal_code, zip_ext, city, state, ss, sex, homeless,race, ethnicity, patientStatus, country_code FROM patient_data WHERE id = $patient_id LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			return imw_fetch_assoc($res);
		}
		return false;
	}
	
	function get_sparcs_insurance_details($ins_comp_id){
		$q = "SELECT name, sparcs_naic, sparcs_typology_of_payment, sparcs_expected_reimbursement_code, sparcs_source_of_payment FROM insurance_companies WHERE  id = ".$ins_comp_id." LIMIT 0,1";
		$res = imw_query($q);
		$err = imw_error();
		if(!$res && !empty($err) && stristr($err,'unknown column')){
			$q = "SELECT name, '' AS sparcs_naic, '' AS sparcs_typology_of_payment, '' AS sparcs_expected_reimbursement_code, '' AS sparcs_source_of_payment FROM insurance_companies WHERE  id = ".$ins_comp_id." LIMIT 0,1";
			$res = imw_query($q);
		}
		if($res && imw_num_rows($res)==1){
			return imw_fetch_array($res);
		}
		return false;
	}
	
	function getUniqueID($patient_id, $fname, $lname, $ssan)
    {
    	$n1 = substr($lname, 0,2);
    	$n2 = substr($lname, -2);
        $n3 = substr($fname, 0,2);
		$n3 = strlen($n3)<2 ? $n3.$n3 : $n3; // take into acount for first names with one letter version 3.3
        $n4 = substr($ssan,-4);
        $unique_id = $patient_id.$n1.$n2.$n3.$n4;
        $unique_id = strtoupper($unique_id);
		if (strlen($unique_id) > 50) {
			return false;
		}
    	return $unique_id;
    }
	
	function get_county_code($state,$country_code,$z){
		$Albany =		array(12007,12009,12023,12041,12045,12046,12047,12054,12055,12059,12067,12077,12084,12085,12107,
							  12110,12120,12128,12143,12147,12158,12159,12161,12183,12186,12189,12193,12201,12202,12203,
							  12204,12205,12206,12207,12208,12209,12210,12211,12212,12214,12220,12222,12223,12224,12225,
							  12226,12227,12228,12229,12230,12231,12232,12233,12234,12235,12236,12237,12238,12239,12240,
							  12241,12242,12243,12244,12245,12246,12247,12248,12249,12250,12252,12255,12256,12257,12260,
							  12261,12288,12469);
		$Allegany  =	array(14029,14707,14708,14709,14711,14714,14715,14717,14721,14727,14735,14739,14744,14745,14754,
							  14774,14777,14786,14802,14803,14804,14806,14813,14822,14880,14884,14895,14897);
		$Bronx  =		array(10451,10452,10453,10454,10455,10456,10457,10458,10459,10460,10461,10462,10463,10464,10465,
							  10466,10467,10468,10469,10470,10471,10472,10473,10474,10475,10499);
		$Broome =		array(13737,13744,13745,13746,13748,13749,13754,13760,13761,13762,13763,13777,13787,13790,13794,
							  13795,13797,13802,13813,13826,13833,13848,13850,13851,13862,13865,13901,13902,13903,13904,
							  13905);
		$Cattaraugus =	array(14041,14042,14060,14065,14070,14101,14129,14133,14138,14168,14171,14173,14706,14719,14726,
							  14729,14730,14731,14737,14741,14743,14748,14751,14753,14755,14760,14766,14770,14772,14778,
							  14779,14783,14788);      		
		$Cayuga =		array(13021,13022,13024,13026,13033,13034,13064,13071,13081,13092,13111,13113,13117,13118,13139,
							  13140,13147,13156,13160,13166);
		$Chautauqua =	array(14048,14062,14063,14135,14136,14166,14701,14702,14710,14712,14716,14718,14720,14722,14723,
							  14724,14728,14732,14733,14736,14738,14740,14742,14747,14750,14752,14756,14757,14758,14767,
							  14769,14775,14781,14782,14784,14785,14787);
		$Chemung =		array(14814,14816,14825,14838,14845,14861,14864,14871,14872,14889,14894,14901,14902,14903,14904,
							  14905,14925);
		$Chenango =		array(13124,13136,13155,13411,13460,13464,13730,13733,13758,13778,13780,13801,13809,13814,13815,
							  13830,13832,13841,13843,13844);
		$Clinton =		array(12901,12903,12910,12911,12912,12918,12919,12921,12923,12924,12929,12933,12934,12935,12952,
							  12955,12958,12959,12962,12972,12978,12979,12981,12985,12992);
		$Columbia =		array(12017,12024,12029,12037,12050,12060,12075,12106,12115,12125,12130,12132,12136,12165,12172,
							  12173,12174,12184,12195,12502,12503,12513,12516,12517,12521,12523,12526,12529,12530,12534,
							  12541,12544,12565,12593);        		
		$Cortland =		array(13040,13045,13056,13077,13087,13101,13141,13158,13738,13784,13803,13863);
		$Delaware =		array(12167,12406,12421,12430,12434,12438,12455,12459,12474,12760,12776,13731,13739,13740,13750,
							  13751,13752,13753,13755,13756,13757,13774,13775,13782,13783,13786,13788,13804,13806,13837,
							  13838,13839,13842,13846,13847,13856,13860);
		$Dutchess =		array(12501,12504,12506,12507,12508,12510,12511,12512,12514,12522,12524,12527,12531,12533,12537,
							  12538,12540,12545,12546,12564,12567,12569,12570,12571,12572,12574,12578,12580,12581,12582,
							  12583,12585,12590,12592,12594,12601,12602,12603,12604);
		$Erie =			array(14001,14004,14006,14010,14025,14026,14027,14030,14031,14032,14033,14034,14035,14038,14043,
							  14047,14051,14052,14055,14057,14059,14061,14068,14069,14072,14075,14080,14081,14085,14086,
							  14091,14102,14110,14111,14112,14127,14134,14139,14140,14141,14150,14151,14169,14170,14201,
							  14202,14203,14204,14205,14206,14207,14208,14209,14210,14211,14212,14213,14214,14215,14216,
							  14217,14218,14219,14220,14221,14222,14223,14224,14225,14226,14227,14228,14231,14233,14240,
							  14241,14260,14261,14263,14264,14265,14267,14269,14270,14272,14273,14276,14280);
		$Essex =		array(12851,12852,12855,12857,12858,12870,12872,12879,12883,12913,12928,12932,12936,12941,12942,
							  12943,12944,12946,12950,12956,12960,12961,12964,12974,12975,12977,12987,12993,12996,12997,
							  12998);
		$Franklin =		array(12914,12915,12916,12917,12920,12926,12930,12937,12939,12945,12953,12957,12966,12969,12970,
							  12976,12980,12983,12986,12989,12995,13655);
		$Fulton =		array(12025,12032,12078,12095,12117,12134,13329,13452,13470);
		$Genesee =		array(14005,14013,14020,14021,14036,14040,14054,14056,14058,14125,14143,14416,14422,14482,14525,
							  14557);
		$Greene =		array(12015,12042,12051,12058,12083,12087,12124,12176,12192,12405,12407,12413,12414,12418,12422,
							  12423,12424,12427,12431,12436,12439,12442,12444,12450,12451,12452,12454,12460,12463,12468,
							  12470,12473,12482,12485,12492,12496);
		$Hamilton =		array(12108,12139,12164,12190,12812,12842,12847,12864,13353,13360,13436);
		$Herkimer =		array(13324,13331,13340,13350,13357,13361,13365,13406,13407,13416,13420,13431,13454,13472,13491);
		$Jefferson =	array(13601,13602,13603,13605,13606,13607,13608,13611,13612,13615,13616,13618,13619,13622,13624,
							  13628,13632,13634,13636,13637,13638,13640,13641,13643,13650,13651,13656,13657,13659,13661,
							  13671,13673,13674,13675,13679,13682,13685,13691,13692,13693);
		$Kings =		array(11201,11202,11203,11204,11205,11206,11207,11208,11209,11210,11211,11212,11213,11214,11215,
							  11216,11217,11218,11219,11220,11221,11222,11223,11224,11225,11226,11228,11229,11230,11231,
							  11232,11233,11234,11235,11236,11237,11238,11239,11240,11241,11242,11243,11244,11245,11247,
							  11248,11249,11251,11252,11254,11255,11256);
		$Lewis =		array(13305,13312,13325,13327,13343,13345,13367,13368,13404,13433,13473,13489,13620,13626,13627,
							  13631,13665);
		$Livingston =	array(14414,14423,14435,14437,14454,14462,14480,14481,14485,14486,14487,14488,14510,14517,14533,
							  14539,14545,14556,14558,14592,14836,14846);
		$Madison =		array(13030,13032,13035,13037,13043,13052,13061,13072,13082,13122,13134,13163,13310,13314,13332,
							  13334,13346,13355,13364,13402,13408,13409,13418,13421,13465,13484,13485);
		$Monroe =		array(14410,14420,14428,14430,14445,14450,14464,14467,14468,14472,14506,14511,14514,14515,14526,
							  14534,14543,14546,14559,14580,14586,14602,14603,14604,14605,14606,14607,14608,14609,14610,
							  14611,14612,14613,14614,14615,14616,14617,14618,14619,14620,14621,14622,14623,14624,14625,
							  14626,14627,14638,14639,14642,14643,14644,14645,14646,14647,14649,14650,14651,14652,14653,
							  14664,14673,14683,14692,14694);
		$Montgomery =	array(12010,12016,12068,12069,12070,12072,12086,12166,12177,13317,13339,13410,13428);
		$Nassau =		array(11001,11003,11010,11020,11021,11022,11023,11024,11025,11026,11027,11030,11040,11041,11042,
							  11043,11044,11050,11051,11052,11053,11054,11055,11096,11099,11501,11507,11509,11510,11514,
							  11516,11518,11520,11530,11531,11535,11536,11542,11545,11547,11548,11549,11550,11551,11552,
							  11553,11554,11555,11556,11557,11558,11559,11560,11561,11563,11565,11566,11568,11569,11570,
							  11571,11572,11575,11576,11577,11579,11580,11581,11582,11590,11592,11594,11595,11596,11597,
							  11598,11599,11709,11710,11714,11732,11735,11736,11737,11753,11756,11758,11762,11765,11771,
							  11773,11774,11783,11791,11793,11797,11801,11802,11803,11804,11815,11819,11853,11854,11855);
		$NewYork =		array(10001,10002,10003,10004,10005,10006,10007,10008,10009,10010,10011,10012,10013,10014,10015,
							  10016,10017,10018,10019,10020,10021,10022,10023,10024,10025,10026,10027,10028,10029,10030,
							  10031,10032,10033,10034,10035,10036,10037,10038,10039,10040,10041,10043,10044,10045,10046,
							  10047,10048,10055,10060,10065,10069,10072,10075,10079,10080,10081,10082,10087,10090,10094,
							  10095,10096,10098,10099,10101,10102,10103,10104,10105,10106,10107,10108,10109,10110,10111,
							  10112,10113,10114,10115,10116,10117,10118,10119,10120,10121,10122,10123,10124,10125,10126,
							  10128,10129,10130,10131,10132,10133,10138,10149,10150,10151,10152,10153,10154,10155,10156,
							  10157,10158,10159,10160,10161,10162,10163,10164,10165,10166,10167,10168,10169,10170,10171,
							  10172,10173,10174,10175,10176,10177,10178,10179,10184,10185,10196,10197,10199,10200,10203,
							  10211,10212,10213,10242,10249,10256,10257,10258,10259,10260,10261,10265,10268,10269,10270,
							  10271,10272,10273,10274,10275,10276,10277,10278,10279,10280,10281,10282,10285,10286,10292);
		$Niagara =		array(14008,14012,14028,14067,14092,14094,14095,14105,4107,14108,14109,14120,14126,14131,14132,
							  14144,14172,14174,14301,14302,14303,14304,14305);
		$Oneida =		array(13042,13054,13123,13157,13162,13301,13303,13304,13308,13309,13313,13316,13318,13319,13321,
							  13322,13323,13328,13338,13341,13352,13354,13362,13363,13401,13403,13413,13417,13424,13425,
							  13435,13438,13440,13441,13442,13449,13455,13456,13461,13469,13471,13476,13477,13478,13479,
							  13480,13483,13486,13490,13492,13494,13495,13501,13502,13503,13504,13505,13599);
		$Onondaga =		array(13020,13027,13029,13031,13039,13041,13051,13057,13060,13063,13066,13078,13080,13084,13088,
							  13089,13090,13104,13108,13110,13112,13116,13119,13120,13137,13138,13152,13153,13159,13164,
							  13201,13202,13203,13204,13205,13206,13207,13208,13209,13210,13211,13212,13214,13215,13217,
							  13218,13219,13220,13221,13224,13225,13235,13244,13250,13251,13252,13261,13290);
		$Ontario =		array(14424,14425,14432,14443,14453,14456,14461,14463,14466,14469,14471,14475,14504,14512,14518,
							  14532,14537,14547,14548,14560,14561,14564,14585);
		$Orange =		array(10910,10912,10914,10915,10916,10917,10918,10919,10921,10922,10924,10925,10926,10928,10930,
							  10932,10933,10940,10941,10943,10949,10950,10953,10958,10959,10963,10969,10973,10975,10979,
							  10981,10985,10987,10988,10990,10992,10996,10997,10998,12518,12520,12543,12549,12550,12551,
							  12552,12553,12555,12575,12577,12584,12586,12729,12746,12771,12780);
		$Orleans =		array(14098,14103,14411,14429,14452,14470,14476,14477,14479,14508,14571);
		$Oswego =		array(13028,13036,13044,13069,13074,13076,13083,13093,13103,13107,13114,13115,13121,13126,13131,
							  13132,13135,13142,13144,13145,13167,13302,13426,13437,13493);
		$Otsego =		array(12064,12116,12155,12197,13315,13320,13326,13333,13335,13337,13342,13348,13415,13439,13450,
							  13457,13468,13475,13482,13488,13747,13776,13796,13807,13808,13810,13820,13825,13834,13849,
							  13859,13861);
		$Putnam =		array(10509,10512,10516,10524,10537,10541,10542,10579,12563);
		$Queens =		array(11002,11004,11005,11101,11102,11103,11104,11105,11106,11109,11120,11351,11352,11354,11355,
							  11356,11357,11358,11359,11360,11361,11362,11363,11364,11365,11366,11367,11368,11369,11370,
							  11371,11372,11373,11374,11375,11377,11378,11379,11380,11381,11385,11386,11390,11405,11411,
							  11412,11413,11414,11415,11416,11417,11418,11419,11420,11421,11422,11423,11424,11425,11426,
							  11427,11428,11429,11430,11431,11432,11433,11434,11435,11436,11439,11451,11499,11690,11691,
							  11692,11693,11694,11695,11697);
		$Rensselaer =	array(12018,12022,12028,12033,12040,12052,12061,12062,12063,12082,12089,12090,12094,12121,12123,
							  12133,12138,12140,12144,12153,12154,12156,12168,12169,12180,12181,12182,12185,12196,12198);
		$Richmond =		array(10301,10302,10303,10304,10305,10306,10307,10308,10309,10310,10311,10312,10313,10314);
		$Rockland =		array(10901,10911,10913,10920,10923,10927,10931,10952,10954,10956,10960,10962,10964,10965,10968,
							  10970,10974,10976,10977,10980,10982,10983,10984,10986,10989,10993,10994);
		$StLawrence =	array(12922,12927,12949,12965,12967,12973,13613,13614,13617,13621,13623,13625,13630,13633,13635,
							  13639,13642,13645,13646,13647,13648,13652,13654,13658,13660,13662,13664,13666,13667,13668,
							  13669,13670,13672,13676,13677,13680,13681,13683,13684,13687,13690,13694,13695,13696,13697,
							  13699,13649,13678);
		$Saratoga =		array(12019,12020,12027,12065,12074,12118,12148,12151,12170,12188,12803,12822,12828,12831,12833,
							  12835,12850,12859,12863,12866,12871,12884);
		$Schenectady =	array(12008,12053,12056,12137,12141,12150,12301,12302,12303,12304,12305,12306,12307,12308,12309,
							  12325,12345);
		$Schoharie =	array(12031,12035,12036,12043,12066,12071,12073,12076,12092,12093,12122,12131,12149,12157,12160,
							  12175,12187,12194,13459);
		$Schuyler =		array(14805,14812,14815,14818,14824,14841,14863,14865,14869,14876,14878,14887,14891,14893);
		$Seneca =		array(13065,13148,13165,14521,14541,14588,14847,14860);
		$Steuben =		array(14529,14572,14801,14807,14808,14809,14810,14819,14820,14821,14823,14826,14827,14830,14831,
							  14839,14840,14843,14855,14856,14858,14870,14873,14874,14877,14879,14885,14898);
		$Suffolk =		array("00501","00544","06390",11701,11702,11703,11704,11705,11706,11707,11708,11713,11715,11716,11717,
							  11718,11719,11720,11721,11722,11724,11725,11726,11727,11729,11730,11731,11733,11738,11739,
							  11740,11741,11742,11743,11746,11747,11749,11750,11751,11752,11754,11755,11757,11760,11763,
							  11764,11766,11767,11768,11769,11770,11772,11775,11776,11777,11778,11779,11780,11782,11784,
							  11786,11787,11788,11789,11790,11792,11794,11795,11796,11798,11901,11930,11931,11932,11933,
							  11934,11935,11937,11939,11940,11941,11942,11944,11946,11947,11948,11949,11950,11951,11952,
							  11953,11954,11955,11956,11957,11958,11959,11960,11961,11962,11963,11964,11965,11967,11968,
							  11969,11970,11971,11972,11973,11975,11976,11977,11978,11980);
		$Sullivan =		array(12701,12719,12720,12721,12722,12723,12724,12726,12727,12732,12733,12734,12736,12737,12738,
							  12740,12741,12742,12743,12745,12747,12748,12749,12750,12751,12752,12754,12758,12759,12762,
							  12763,12764,12765,12766,12767,12768,12769,12770,12775,12777,12778,12779,12781,12783,12784,
							  12785,12786,12787,12788,12789,12790,12791,12792);
		$Tioga =		array(13732,13734,13736,13743,13811,13812,13827,13835,13840,13845,13864,14859,14883,14892);
		$Tompkins =		array(13053,13062,13068,13073,13102,14817,14850,14851,14852,14853,14854,14867,14881,14882,14886);
	
		$Ulster =		array(12401,12402,12404,12409,12410,12411,12412,12416,12417,12419,12420,12428,12429,12432,12433,
							  12435,12440,12441,12443,12446,12448,12449,12453,12456,12457,12458,12461,12464,12465,12466,
							  12471,12472,12475,12477,12480,12481,12483,12484,12486,12487,12489,12490,12491,12493,12494,
							  12495,12498,12515,12525,12528,12542,12547,12548,12561,12566,12568,12588,12589,12725);
		$Warren =		array(12801,12804,12808,12810,12811,12814,12815,12817,12820,12824,12836,12843,12845,12846,12853,
							  12856,12860,12862,12874,12878,12885,12886);
		$Washington =	array(12057,12809,12816,12819,12821,12823,12827,12832,12834,12837,12838,12839,12841,12844,12848,
							  12849,12854,12861,12865,12873,12887);
		$Wayne =		array(13143,13146,13154,14413,14433,14449,14489,14502,14505,14513,14516,14519,14520,14522,14538,
							  14542,14551,14555,14563,14568,14589,14590);
		$Westchester =	array(10501,10502,10503,10504,10505,10506,10507,10510,10511,10514,10517,10518,10519,10520,10521,
							  10522,10523,10526,10527,10528,10530,10532,10533,10535,10536,10538,10540,10543,10545,10546,
							  10547,10548,10549,10550,10551,10552,10553,10557,10558,10560,10562,10566,10567,10570,10571,
							  10572,10573,10576,10577,10578,10580,10583,10587,10588,10589,10590,10591,10594,10595,10596,
							  10597,10598,10601,10602,10603,10604,10605,10606,10607,10610,10701,10702,10703,10704,10705,
							  10706,10707,10708,10709,10710,10801,10802,10803,10804,10805);
		$Wyoming =		array(14009,14011,14024,14037,14039,14066,14082,14083,14113,14130,14145,14167,14427,14530,14536,
							  14549,14550,14569,14591);
		$Yates =		array(14415,14418,14441,14478,14507,14527,14544,14837,14842,14857);
		
		if (is_null($country_code) || strtolower($country_code) == "usa" || strtolower($country_code) == "us" || strtolower($country_code) == "can") {
			if(strtolower($state) == 'ny'){
				if (in_array($z, $Albany)) 				$cc = '36001';
				elseif (in_array($z, $Allegany)) 		$cc = '36003';
				elseif (in_array($z, $Bronx)) 			$cc = '36005';
				elseif (in_array($z, $Broome))			$cc = '36007';
				elseif (in_array($z, $Cattaraugus))		$cc = '36009';
				elseif (in_array($z, $Cayuga))			$cc = '36011';
				elseif (in_array($z, $Chautauqua))		$cc = '36013';
				elseif (in_array($z, $Chemung))			$cc = '36015';
				elseif (in_array($z, $Chenango))		$cc = '36017';
				elseif (in_array($z, $Clinton))			$cc = '36019';
				elseif (in_array($z, $Columbia))		$cc = '36021';
				elseif (in_array($z, $Cortland))		$cc = '36023';
				elseif (in_array($z, $Delaware))		$cc = '36025';
				elseif (in_array($z, $Dutchess))		$cc = '36027';
				elseif (in_array($z, $Erie))			$cc = '36029';
				elseif (in_array($z, $Essex))			$cc = '36031';
				elseif (in_array($z, $Franklin))		$cc = '36033';
				elseif (in_array($z, $Fulton))			$cc = '36035';
				elseif (in_array($z, $Genesee))			$cc = '36037';
				elseif (in_array($z, $Greene))			$cc = '36039';
				elseif (in_array($z, $Hamilton))		$cc = '36041';
				elseif (in_array($z, $Herkimer))		$cc = '36043';
				elseif (in_array($z, $Jefferson))		$cc = '36045';
				elseif (in_array($z, $Kings))			$cc = '36047';
				elseif (in_array($z, $Lewis))			$cc = '36049';
				elseif (in_array($z, $Livingston))		$cc = '36051';
				elseif (in_array($z, $Madison))			$cc = '36053';
				elseif (in_array($z, $Monroe))			$cc = '36055';
				elseif (in_array($z, $Montgomery))		$cc = '36057';
				elseif (in_array($z, $Nassau))			$cc = '36059';
				elseif (in_array($z, $NewYork))			$cc = '36061';
				elseif (in_array($z, $Niagara))			$cc = '36063';
				elseif (in_array($z, $Oneida))			$cc = '36065';
				elseif (in_array($z, $Onondaga))		$cc = '36067';
				elseif (in_array($z, $Ontario))			$cc = '36069';
				elseif (in_array($z, $Orange))			$cc = '36071';
				elseif (in_array($z, $Orleans))			$cc = '36073';
				elseif (in_array($z, $Oswego))			$cc = '36075';
				elseif (in_array($z, $Otsego))			$cc = '36077';
				elseif (in_array($z, $Putnam))			$cc = '36079';
				elseif (in_array($z, $Queens))			$cc = '36081';
				elseif (in_array($z, $Rensselaer))		$cc = '36083';
				elseif (in_array($z, $Richmond))		$cc = '36085';
				elseif (in_array($z, $Rockland))		$cc = '36087';
				elseif (in_array($z, $StLawrence))		$cc = '36089';
				elseif (in_array($z, $Saratoga))		$cc = '36091';
				elseif (in_array($z, $Schenectady))		$cc = '36093';
				elseif (in_array($z, $Schoharie))		$cc = '36095';
				elseif (in_array($z, $Schuyler))		$cc = '36097';
				elseif (in_array($z, $Seneca))			$cc = '36099';
				elseif (in_array($z, $Steuben))			$cc = '36101';
				elseif (in_array($z, $Suffolk))			$cc = '36103';
				elseif (in_array($z, $Sullivan))		$cc = '36105';
				elseif (in_array($z, $Tioga))			$cc = '36107';
				elseif (in_array($z, $Tompkins))		$cc = '36109';
				elseif (in_array($z, $Ulster))			$cc = '36111';
				elseif (in_array($z, $Warren))			$cc = '36113';
				elseif (in_array($z, $Washington))		$cc = '36115';
				elseif (in_array($z, $Wayne))			$cc = '36117';
				elseif (in_array($z, $Westchester))		$cc = '36119';
				elseif (in_array($z, $Wyoming))			$cc = '36121';
				elseif (in_array($z, $Yates))			$cc = '36123';

				$county_code = $cc;
			} else {
				$county_code  =  '88';
			}
		} else {
			$county_code  =  '99'; // Unknown/Homeless County Code
		}
		return $county_code;
	}
	
	
	function getRaceCode($prace)
	{
		if (empty($prace)){
			$race = "R9";
		}else{
			switch ($prace) {
				case "White":
				$race = "R5";
				break;
			case "Black or African American":
				$race = "R3";
				break;
			case "American Indian or Alaska Native":
				$race = "R1";
				break;
			case "Asian":
				$race = "R2";
				break;
			case "Native Hawaiian or Other Pacific Islander":
				$race = "R4";
				break;
			default:
				$race = "R9";
			}
		}
		return $race;
	}
	
	function getEthnic($eth){
		if (empty($eth)){
			$e = "E9";
		}else{
			switch ($eth) {
			case "Unknown":
			case " ":
				$e = "E9";
				break;
			case "Hispanic or Latino":
				$e = "E1";
				break;
			default:
				$e = "E2";
			}
		}
		return $e;
	}
	
	function getTotalAmountExact($chl_id, $avoid_cpts=array()){
		$total_amount = 0.00;
		$q = "SELECT  procCharges, cpt4_code 
				FROM  patient_charge_list_details pcld 
				LEFT JOIN cpt_fee_tbl cft ON (pcld.procCode = cft.cpt_fee_id) 
				WHERE pcld.charge_list_id = ".$chl_id." AND pcld.del_status='0'";
		$res = imw_query($q);
		if($res){
			while(list($procCharges, $cpt) = imw_fetch_array($res)) {
				if($this->cptDisplay($cpt, $avoid_cpts)) {
					$total_amount += $procCharges;
				}
			}
			$total_amount = number_format($total_amount, 2, '.', '');
		}
		return $total_amount;
	}
	
	function get_all_dx_codes($chl_id){
		$total_amount = 0.00;
		$q = "SELECT diagnosis_id1,diagnosis_id2,diagnosis_id3,diagnosis_id4,diagnosis_id5,diagnosis_id6,diagnosis_id7,diagnosis_id8,diagnosis_id9,diagnosis_id10,diagnosis_id11,diagnosis_id12 
				FROM  patient_charge_list_details pcld 
				WHERE pcld.charge_list_id = ".$chl_id." AND pcld.del_status='0'";
		$res = imw_query($q);
		if($res){
			$diagnosis_id = array();
			while($rs = imw_fetch_assoc($res)){
				for($x=1; $x <= 12; $x++){
					if(isset($rs['diagnosis_id'.$x]) && empty($rs['diagnosis_id'.$x]) === false){
						$diagnosis_id[] = preg_replace("/\./","",$rs['diagnosis_id'.$x]);
					}
				}
			}
			$diagnosis_id = array_unique($diagnosis_id);
			return $diagnosis_id;
		}
		return false;
	}
	
	function get_sc_details($pt,$dos){
		if(defined('IMEDIC_SC')){
			$q = "SELECT pc.patientConfirmationId, pc.patientId AS sc_patient_id, orr.surgeryStartTime, orr.surgeryEndTime, stb.checked_in_time, stb.checked_out_time ";
			$q.= "FROM ".constant('IMEDIC_SC').".patientconfirmation pc ";
			$q.= "LEFT JOIN ".constant('IMEDIC_SC').".operatingroomrecords orr ON (orr.confirmation_id = pc.patientConfirmationId) ";
			$q.= "LEFT JOIN ".constant('IMEDIC_SC').".stub_tbl stb ON (stb.patient_confirmation_id  = pc.patientConfirmationId AND stb.dos = pc.dos) ";
			$q.= "WHERE pc.dos = '".$dos."' AND pc.imwPatientId = ".$pt." LIMIT 0,1";
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){
				return imw_fetch_assoc($res);
			}
		}
		return false;
	}
	
	function getHourCode($time){
		$amn = rand(1, 59);
		$amn = str_pad($amn, 2, "0", STR_PAD_LEFT);
		$hour = substr($time, 0, 2);
		$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
		$minute = substr($time, 3, 2);
		$minute = str_pad($minute, 2, "0", STR_PAD_LEFT);
		$ap = substr($time, -2);
		if ($ap == "PM") {
			$hourcode = intval($hour) + 12;
			if ($hourcode == 24) {
				$hourcode= "0000";
			} else {
				$hourcode = $hour.$minute;
			}
		} else {
			$hourcode = $hour.$minute;
		}
		if ($hourcode == 0) $hourcode = "01".$amn;
		if (empty($hourcode)) $hourcode = "01".$amn;
		
		return $hourcode;
    }
	
	function get_doctor_details($uid){
		$res = imw_query("SELECT fname, lname, licence, user_npi FROM users WHERE id = ".$uid." LIMIT 0,1");
		if($res && imw_num_rows($res)==1){
			return imw_fetch_array($res);
		}
		return false;
	}
	
	function get_charges_details($chl_id){
		$res = imw_query("SELECT  procCode,  procCharges,  modifier_id1 FROM patient_charge_list_details WHERE charge_list_id = $chl_id AND procCharges != 0 AND del_status != '1'");
		if($res && imw_num_rows($res) > 0){
			return $res;
		}
		return false;
	}
	
	function get_cpt_proc_table_array(){
		$cpt_proc_table_array = array(
										'0191T' => 13.41,
										64612 => 4.2,
										64615 => 4.3,
										10060 => 8.09,
										10061 => 8.09,
										67938 => 8.09,
										67700 => 8.10,
										67810 => 08.11,
										67840 => 08.20,
										11440 => 08.20,
										11441 => 08.20,
										11442 => 08.20,
										11443 => 08.20,
										11640 => 08.20,
										11641 => 08.20,
										11642 => 08.20,
										67800 => 08.21,
										67801 => 08.22,
										67805 => 08.23,
										67921 => 08.41,
										67916 => 08.43,
										67923 => 08.43,
										67917 => 08.49,
										67924 => 08.49,
										68705 => 08.49,
										67820 => 08.59,
										67961 => 08.61,
										15822 => 08.70,
										15820 => 08.70,
										67935 => 08.71,
										13151 => 08.72,
										13152 => 08.72,
										12011 => 08.81,
										12013 => 08.81,
										12014 => 08.81,
										12052 => 08.81,
										12051 => 08.81,
										67875 => 08.99,
										68801 => 09.41,
										68840 => 09.42,
										68440 => 09.51,
										68761 => 09.91,
										65210 => 10.0,
										68020 => 10.1,
										68115 => 10.31,
										68320 => 10.42,
										68362 => 10.49,
										65273 => 10.6,
										68200 => 10.91,
										65430 => 11.21,
										65420 => 11.31,
										65426 => 11.32,
										65400 => 11.49,
										65436 => 11.49,
										66250 => 11.52,
										65778 => 11.59,
										65710 => 11.62,
										65756 => 11.63,
										65755 => 11.63,
										65750 => 11.63,
										65730 => 11.63,
										65782 => 11.79,
										65779 => 11.79,
										65772 => 11.79,
										65780 => 11.79,
										65600 => 11.91,
										65235 => 12.00,
										66761 => 12.12,
										65800 => 12.21,
										65875 => 12.33,
										65855 => 12.59,
										66170 => 12.64,
										66172 => 12.64,
										66711 => 12.73,
										65815 => 12.91,
										65286 => 12.98,
										66840 => 13.3,
										66984 => 13.41,
										66982 => 13.41,
										66850 => 13.43,
										66821 => 13.64,
										66985 => 13.72,
										65920 => 13.8,
										66986 => 13.8,
										66825 => 13.90,
										67228 => 14.21,
										67210 => 14.24,
										67105 => 14.35,
										67005 => 14.71,
										67010 => 14.74,
										67036 => 14.74,
										67031 => 14.79,
										92226 => 16.21,
										92225 => 16.21,
										11200 => 86.3,
										92025 => 95.09,
										92310 => 95.23,
										92312 => 95.23,
										65222 => 99.29
										);
		return $cpt_proc_table_array;
	}
		
}//end of class.
?>

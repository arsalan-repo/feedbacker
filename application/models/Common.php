<?php

class Common extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('pushNotifications');        
    }

    //login user
    function check_login($user_name, $user_password) {
        $this->db->select("id,name,email,password,country,lang_id,photo,fbid,status");
        $this->db->where("email", $user_name);
        $this->db->where("password", md5($user_password));
        $this->db->where("deleted", 0);
        $this->db->from("users");
        $this->db->limit(1);

        $query = $this->db->get();
        //echo $this->db->last_query();die();
        if ($query->num_rows() == 1) {
            $result = $query->result_array();        
            return $result;
        } else {
            return array();
        }
    }

    //get user country
    function user_country($user_id) {
        $this->db->select("country");
        $this->db->where("id", $user_id);
        $this->db->from("users");
        $this->db->limit(1);

        $query = $this->db->get();
        
        if ($query->num_rows() == 1) {
            $result = $query->result_array();        
            return $result;
        } else {
            return array();
        }
    }

    // insert database
    function insert_data($data, $tablename) {
        if ($this->db->insert($tablename, $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    // insert database
    function insert_data_getid($data, $tablename) {
        if ($this->db->insert($tablename, $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    // update database
    function update_data($data, $tablename, $columnname, $columnid) {
        $this->db->where($columnname, $columnid);
        if ($this->db->update($tablename, $data)) {
            return true;
        } else {
            return false;
        }
    }

    // select data using colum id
    function select_data_by_id($tablename, $columnname, $columnid, $data = '*', $join_str = array()) {
        $this->db->select($data);
        if (!empty($join_str)) {
            foreach ($join_str as $join) {
                if ($join['join_type'] == '') {
                    $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
                } else {
                    $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id'], $join['join_type']);
                }
            }
        }
        $this->db->where($columnname, $columnid);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    // select data using multiple conditions
    function select_data_by_condition($tablename, $contition_array = array(), $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array(), $group_by='') {
        $this->db->select($data);

        if (!empty($join_str)) {
            foreach ($join_str as $join) {
                if ($join['join_type'] == '') {
                    $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
                } else {
                    $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id'], $join['join_type']);
                }
            }
        }

        $this->db->where($contition_array);


        //Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }
        //order by query
        if ($sortby != '' && $orderby != '') {
            $this->db->order_by($sortby, $orderby);
        }
        if ($group_by != '') {
            $this->db->group_by($group_by);
        }

        $query = $this->db->get($tablename);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    // select data using multiple conditions and search keyword
    function select_data_by_search($tablename, $search_condition, $contition_array = array(), $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = '') {
        $this->db->select($data);
        if (!empty($join_str)) {
            foreach ($join_str as $join) {
                $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
            }
        }
        $this->db->where($contition_array);
        $this->db->where($search_condition);

        //Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }
        //order by query
        if ($sortby != '' && $orderby != '') {
            $this->db->order_by($sortby, $orderby);
        }

        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    // delete data
    function delete_data($tablename, $columnname, $columnid) {
        $this->db->where($columnname, $columnid);
        if ($this->db->delete($tablename)) {
            return true;
        } else {
            return false;
        }
    }

    // check unique avaliblity
    function check_unique_avalibility($tablename, $columname1, $columnid1_value, $columname2, $columnid2_value, $condition_array = array()) {
        // if edit than $columnid2_value use

        if ($columnid2_value != '') {
            $this->db->where($columname2 . " !=", $columnid2_value);
        }

        if (!empty($condition_array)) {
            $this->db->where($condition_array);
        }

        $this->db->where($columname1, $columnid1_value);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    //get all record 
    function get_all_record($tablename, $data = '*', $sortby = '', $orderby = '') {
        $this->db->select($data);
        $this->db->from($tablename);
        $this->db->where('status', 'Enable');
        if ($sortby != '' && $orderby != "") {
            $this->db->order_by($sortby, $orderby);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    //table records count
    function get_count_of_table($table, $where = array()) {
        if (count($where) > 0) {
            $this->db->where($where);
        }

        $query = $this->db->count_all_results($table);

        return $query;
    }

    //table Next Auto Increment ID
    function get_autoincrement_id($table) {
        $this->db->select_max('class_id');
        $Q = $this->db->get($table);
        $row = $Q->row_array();
        return $row['class_id'];
    }

// check email id
    function chkemail($id, $email) {
        if ($id != 0) {
            $option = array('userid !=' => $id, 'useremail' => $email);
        } else {
            $option = array('useremail' => $email);
        }
        $query = $this->db->get_where('users', $option);
        if ($query->num_rows() > 0) {
            return 'old';
        } else {
            return 'new';
        }
    }

//This function get all records from table by name
    function getallrecordbytablename($tablename, $data, $conditionarray = '', $limit = '', $offset = '', $sortby = '', $orderby = '') {

//$this->db->order_by($sortby, $orderby);
//Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }

//Executing Query
        $this->db->select($data);
        $this->db->from($tablename);
        if ($conditionarray != '') {
            $this->db->where($conditionarray);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

//This function get all open record count

    function get_open_request_count($condition) {
        $this->db->select('COUNT(requestid) as count,maincategoryuniqid');
        $this->db->where($condition);
        $this->db->from('request');
        $this->db->group_by('maincategoryuniqid');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

// select data using colum id
    function select_database_id($tablename, $columnname, $columnid, $data = '*', $condition_array = array()) {
        $this->db->select($data);
        $this->db->where($columnname, $columnid);
        if (!empty($condition_array)) {
            $this->db->where($condition_array);
        }
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    // change status
    function change_status($data, $tablename, $columnname, $columnid) {
        $this->db->where($columnname, $columnid);
        if ($this->db->update($tablename, $data)) {
            return true;
        } else {
            return false;
        }
    }

    function get_name_by_id($tablename, $columnname, $condition) {
        $this->db->select($columnname);
        $this->db->where($condition);
        $this->db->from($tablename);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return array();
        }
    }

    function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Km') {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) *
                sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) *
                cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        switch ($unit) {
            case 'Mi': break;
            case 'Km' : $distance = $distance * 1.609344;
        }
        return (round($distance, 2));
    }
	
	// Random password
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /* function getuser_by_lat_long($latitude, $longitude, $distance) {
      $this->db->select("user_id,user_name,user_type,school_email,first_name,last_name,university_name,student_major,user_token_key,user_status,user_image, (((acos(sin((" . $latitude . "*pi()/180)) *
      sin((`user_lat`*pi()/180))+cos((" . $latitude . "*pi()/180)) *
      cos((`user_lat`*pi()/180)) * cos(((" . $longitude . "- `user_long`)
     * pi()/180))))*180/pi())*60*1.1515*1.609344) as 'distance'");
      $this->db->from("users");
      $this->db->where("(((acos(sin((" . $latitude . "*pi()/180)) *
      sin((`user_lat`*pi()/180))+cos((" . $latitude . "*pi()/180)) *
      cos((`user_lat`*pi()/180)) * cos(((" . $longitude . "- `user_long`)
     * pi()/180))))*180/pi())*60*1.1515*1.609344) <= " . $distance . " ");
      $query = $this->db->get();

      $result = $query->result_array();

      return $result;
      }
     */

    function getnearByEvent($latitude, $longitude, $distance, $offset='', $limit='') {
        
        $this->db->select("*");
        $this->db->from("events");
        $this->db->where("(((acos(sin((" . $latitude . "*pi()/180)) * 
sin((`lat`*pi()/180))+cos((" . $latitude . "*pi()/180)) * 
cos((`lat`*pi()/180)) * cos(((" . $longitude . "- `lng`)
*pi()/180))))*180/pi())*60*1.1515*1.609344) <= " . $distance . " ");
        $this->db->where(array('events.status !=' =>3, 'events.status' => 1, 'events.date >' => date('Y-m-d')));
        if($limit != '' && $offset != '')
        {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();

        $result = $query->result_array();
      
        return $result;
    }

    function getTitles($search_string=null, $order=null, $order_type='ASC', $offset='', $limit='') {
//        $this->db->select('title_id');
//        $this->db->select('title');
//        
//        $this->db->from('titles');
        
//        if($search_string){
//            $this->db->like('title', $search_string);
//        }

        if($order){
            $this->db->order_by($order, $order_type);
        }else{
            $this->db->order_by('title', $order_type);
        }

        if($limit != '' && $offset != '')
        {
            $this->db->limit($limit, $offset);
        }

        //$query = $this->db->get();   
		$query = $this->db->select('title_id, title')->from('titles')->where("title LIKE '$search_string%' AND deleted = 0")->get();  
        return $query->result_array();  
    }

    function getSettings($key = '') {
        $this->db->select('*');

        if($key != '') {
            $this->db->where("setting_key", $key);
        }
        
        $this->db->from("settings");

        $query = $this->db->get();
        //echo $this->db->last_query();die();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();        
            return $result;
        } else {
            return array();
        }   
    }
	
	function getCountries($code = '') {
		$countries = array
		(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua And Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia And Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Congo, Democratic Republic',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Cote D\'Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (Malvinas)',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island & Mcdonald Islands',
			'VA' => 'Holy See (Vatican City State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran, Islamic Republic Of',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle Of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KR' => 'Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao People\'s Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libyan Arab Jamahiriya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia, Federated States Of',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory, Occupied',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts And Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre And Miquelon',
			'VC' => 'Saint Vincent And Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome And Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia And Sandwich Isl.',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard And Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad And Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks And Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Viet Nam',
			'VG' => 'Virgin Islands, British',
			'VI' => 'Virgin Islands, U.S.',
			'WF' => 'Wallis And Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);
		
		if ($code != '') {
			return $countries[$code];
		} else {
			return $countries;	
		}
	}

    function timeAgo($time_ago) {
        $time_ago = strtotime($time_ago);
        $cur_time   = time();
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        // Seconds
        if($seconds <= 60){
            return "just now";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                return "1 min";
            }
            else{
                return "$minutes mins";
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                return "an hour";
            }else{
                return "$hours hrs";
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                return "yesterday";
            }else{
                return "$days days";
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                return "a week";
            }else{
                return "$weeks weeks";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                return "a month";
            }else{
                return "$months months";
            }
        }
        //Years
        else{
            if($years==1){
                return "one year";
            }else{
                return "$years years";
            }
        }
    }

    function sendMail ($to = '', $cc = '', $subject = '', $mail_body = '') {
        $this->load->library('email');

        if($subject == '') {
            $subject = 'Feedbacker - New Notification';
        }

        $result = $this->email
                ->from('smtp.feedbacker@gmail.com', 'Feedbacker')
                // ->reply_to('yoursecondemail@somedomain.com')    // Optional, an account where a human being reads.
                ->to($to)
                ->subject($subject)
                ->message($mail_body)
                ->send();

        if($result) {
            return true;
        } else {
            return false;
        }
    }
	
	function getFeedbackDetail ($user_id, $feedback_id) {
		// Get User name
		$join_str = array(
			array(
				'table' => 'users',
				'join_table_id' => 'users.id',
				'from_table_id' => 'feedback.user_id',
				'join_type' => 'left'
			),
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'left'
			)
		);
		
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, location, feedback.datetime as time';
		$feedback = $this->select_data_by_id('feedback', 'feedback_id', $feedback_id, $data, $join_str);
		
//		print_r($feedback);
//		exit();

		$return_array = array();
		
		$return_array['id'] = $feedback_id;
		$return_array['title_id'] = $feedback[0]['title_id'];				
		$return_array['title'] = $feedback[0]['title'];
		
		// Get likes for this feedback
		$contition_array_lk = array('feedback_id' => $feedback_id);
		$flikes = $this->select_data_by_condition('feedback_likes', $contition_array_lk, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
		
		$return_array['likes'] = "";
		
		if(count($flikes) > 1000) {
			$return_array['likes'] = (count($flikes)/1000)."k";
		} else {
			$return_array['likes'] = count($flikes);
		}
		
		// Get followers for this title
		$contition_array_fo = array('title_id' => $feedback[0]['title_id']);
		$followings = $this->select_data_by_condition('followings', $contition_array_fo, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
		
		$return['followers'] = "";
		
		if(count($followings) > 1000) {
			$return_array['followers'] = (count($followings)/1000)."k";
		} else {
			$return_array['followers'] = count($followings);
		}
		
		// Check If user reported this feedback
		$contition_array_rs = array('feedback_id' => $feedback_id, 'user_id' => $user_id);
		$spam = $this->select_data_by_condition('spam', $contition_array_rs, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
					
		if(count($spam) > 0) {
			$return_array['report_spam'] = TRUE;
		} else {
			$return_array['report_spam'] = FALSE;
		}
		
		// Check If user liked this feedback
		$contition_array_li = array('feedback_id' => $feedback_id, 'user_id' => $user_id);
		$likes = $this->select_data_by_condition('feedback_likes', $contition_array_li, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
					
		if(count($likes) > 0) {
			$return_array['is_liked'] = TRUE;
		} else {
			$return_array['is_liked'] = FALSE;
		}
		
		// Check If user followed this title
		$contition_array_ti = array('title_id' => $feedback[0]['title_id'], 'user_id' => $user_id);
		$followtitles = $this->select_data_by_condition('followings', $contition_array_ti, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
					
		if(count($followtitles) > 0) {
			$return_array['is_followed'] = TRUE;
		} else {
			$return_array['is_followed'] = FALSE;
		}
		
		$return_array['name'] = $feedback[0]['name'];
		
		if($feedback[0]['photo'] != '') {
			$return_array['user_avatar'] = S3_CDN . 'uploads/user/thumbs/' . $feedback[0]['photo'];
		} else {
			$return_array['user_avatar'] = ASSETS_URL . 'images/user-avatar.png';
		}
		
		if($feedback[0]['feedback_img'] != '') {
			$return_array['feedback_img'] = S3_CDN . 'uploads/feedback/main/' . $feedback[0]['feedback_img'];
		} else {
			$return_array['feedback_img'] = "";
		}

        if($feedback[0]['feedback_thumb'] != '') {
            $return_array['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/' . $feedback[0]['feedback_thumb'];
        } else {
            $return_array['feedback_thumb'] = "";
        }
		
		if($feedback[0]['feedback_video'] !== "") {
			$return_array['feedback_video'] = S3_CDN . 'uploads/feedback/video/' . $feedback[0]['feedback_video'];
		} else {
			$return_array['feedback_video'] = "";
		}
        
		$return_array['feedback'] = $feedback[0]['feedback_cont'];
		$return_array['location'] = $feedback[0]['location'];
		$return_array['time'] = $this->timeAgo($feedback[0]['time']);
		
		return $return_array;
	}

    function get_notification ($user_id, $notification_id) {
        $join_str = array(
            array(
                'table' => 'users',
                'join_table_id' => 'users.id',
                'from_table_id' => 'user_notifications.guest_id',
                'join_type' => 'left'
            ),
            array(
                'table' => 'titles',
                'join_table_id' => 'titles.title_id',
                'from_table_id' => 'user_notifications.title_id',
                'join_type' => 'left'
            )
        );

        $condition_array = array('notification_id' => $notification_id, 'user_notifications.user_id' => $user_id);
        $notifications = $this->select_data_by_condition('user_notifications', $condition_array, $data = 'user_notifications.id, users.name, users.photo, titles.title, titles.title_id, feedback_id, user_notifications.notification_id, user_notifications.datetime as time', $short_by = 'user_notifications.datetime', $order_by = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');

        $return_array = array();

        if(count($notifications) > 0) {
            foreach ($notifications as $row) {
                $return = array();
                $return['id'] = $row['id'];
                $return['feedback_id'] = $row['feedback_id'];

                if(isset($notifications[0]['photo'])) {
                    $return['photo'] = S3_CDN . 'uploads/user/thumbs/' . $row['photo'];
                } else {
                    $return['photo'] = ASSETS_URL . 'images/user-avatar.png';
                }

                switch ($notification_id) {
                    /* Titles I Follow */
                    case 2:
						$return['message'] = $row['name'].' wrote about '.'"'.$row['title'].'"';
                        /*if(count($notifications) == 1) {
                            $return['message'] = $row['name'].' wrote about '.'"'.$row['title'].'"';
                        } else {
                            $others = (count($notifications) - 1);
                            $return['message'] = $row['name'].' and '.$others. ($others == 1 ? " other" : " others") .' wrote about '.'"'.$row['title'].'"';
                        }*/
                    break;

                    /* Likes on the Feedbacks */
                    case 3:
						$return['message'] = $row['name'].' liked your feedback';
                        /*if(count($notifications) == 1) {
                            $return['message'] = $row['name'].' liked your feedback';
                        } else {
                            $others = (count($notifications) - 1);
                            $return['message'] = $row['name'].' and '.$others. ($others == 1 ? " other" : " others") .' liked your feedback';
                        }*/
                    break;

                    /* Feedbacks on my Titles */
                    case 4:
						$return['message'] = $row['name'].' replied on your feedback';
                        /*if(count($notifications) == 1) {
                            $return['message'] = $row['name'].' replied on your feedback';
                        } else {
                            $others = (count($notifications) - 1);
                            $return['message'] = $row['name'].' and '.$others. ($others == 1 ? " other" : " others") .' replied on your feedback';
                        }*/
                    break;
                }

                // Get Feedback
                $feedback = $this->select_data_by_id('feedback', 'feedback_id', $row['feedback_id'], 'feedback_cont, replied_to', $join_str = array());
                $return['feedback'] = $feedback[0]['feedback_cont'];

                // Notification Type
                if($row['notification_id'] == 2) {
                    $return['title_id'] = $row['title_id'];
                    $return['title'] = $row['title'];
                } else {
                    $return['title_id'] = "";   
                }

                if($row['notification_id'] == 4) {
                    unset($return['feedback_id']);
                    $return['feedback_id'] = $feedback[0]['replied_to'];
                }
				
				$return['time'] = $this->timeAgo($row['time']);

                array_push($return_array, $return);
            }
        }

        return $return_array;
    }

    function push_notification ($notif_id, $device_type, $token_key) {

        // echo $notif_id.",".$device_type.",". $token_key;
        $responseText = '';

        // Get notification detail
        $join_str = array(
            array(
                'table' => 'users',
                'join_table_id' => 'users.id',
                'from_table_id' => 'user_notifications.guest_id',
                'join_type' => 'left'
            ),
            array(
                'table' => 'titles',
                'join_table_id' => 'titles.title_id',
                'from_table_id' => 'user_notifications.title_id',
                'join_type' => 'left'
            )
        );

        $notif_detail = $this->select_data_by_id('user_notifications', 'user_notifications.id', $notif_id, $data = 'users.name, titles.title, user_notifications.title_id, user_notifications.feedback_id, user_notifications.notification_id', $join_str);

        // print_r($notif_detail);
        // exit();

        switch ($notif_detail[0]['notification_id']) {
            /* Titles I Follow */
            case 2:
                // Message payload
                $msg_payload = array (
                    'mtitle' => 'Feedbacker',
                    'mdesc' => $notif_detail[0]['name'].' wrote about '.'"'.$notif_detail[0]['title'].'"',
                    'title_id' => $notif_detail[0]['title_id'],
                    'feedback_id' => $notif_detail[0]['feedback_id'],
                );

                break;

            /* Likes on the Feedbacks */
            case 3:
                // Message payload
                $msg_payload = array (
                    'mtitle' => 'Feedbacker',
                    'mdesc' => $notif_detail[0]['name'].' liked your feedback',
                    'title_id' => '',
					'feedback_id' => $notif_detail[0]['feedback_id'],
                );

                break;

            /* Feedbacks on my Titles */
            case 4:
                // Message payload
                $msg_payload = array (
                    'mtitle' => 'Feedbacker',
                    'mdesc' => $notif_detail[0]['name'].' replied on your feedback',
                    'title_id' => '',
                );
				
				// Get Feedback
                $feedback = $this->select_data_by_id('feedback', 'feedback_id', $notif_detail[0]['feedback_id'], 'replied_to', $join_str = array());
                $msg_payload['feedback_id'] = $feedback[0]['replied_to'];

                break;
        }

        if($device_type == 'ios') {
            $responseText = $this->pushNotifications->iOS($msg_payload, $token_key);
        }

        if($device_type == 'android') {
            $responseText = $this->pushNotifications->android($msg_payload, array($token_key));
        } 
        
        // print_r($responseText); exit();
        return $responseText;
    }

    function notification ($user_id = '', $guest_id, $title_id, $feedback_id, $replied_to, $notification_id) {
        switch ($notification_id) {
            /* Titles I Follow */
            case 2:

                // Get users following this title
                $contition_array = array('title_id' => $title_id);
                $followings = $this->select_data_by_condition('followings', $contition_array, $data = 'user_id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                foreach ($followings as $user) {
                    // Check for user settings for this notification
                    $contition_array = array('user_id' => $user['user_id'], 'notification_id' => $notification_id, 'status' => 'on');
                    $preferences = $this->select_data_by_condition('user_preferences', $contition_array, $data = 'user_id, status', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                    if(count($preferences) > 0) {
                        // echo $user['user_id']." ".$preferences[0]['status'];
                        // Update notification
                        $condition_array = array('notification_id' => $notification_id, 'user_id' => $user['user_id'], 'guest_id' => $guest_id, 'title_id' => $title_id);
                        $notifications = $this->select_data_by_condition('user_notifications', $condition_array, $data = 'id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                        if (count($notifications) > 0) {
                            $notif_id = $notifications[0]['id'];

                            $update_data['feedback_id'] = $feedback_id;
                            $this->update_data($update_data, 'user_notifications', 'id', $notif_id);
                        } else {
                            if($user['user_id'] != $guest_id) {
                                // Insert new notification
                                $insert_array['user_id'] = $user['user_id'];
                                $insert_array['guest_id'] = $guest_id;
                                $insert_array['title_id'] = $title_id;
                                $insert_array['feedback_id'] = $feedback_id;
                                $insert_array['notification_id'] = $notification_id;
                                $insert_array['datetime'] = date('Y-m-d H:i:s');

                                // echo "<pre>";
                                // print_r($insert_array);

                                $notif_id = $this->insert_data_getid($insert_array, $tablename = 'user_notifications');
                            }
                        }

                        // Send push notification
                        if(isset($notif_id)) {
                            $push_array = array('user_id' => $user['user_id'], 'notification_id' => 1, 'status' => 'on');
                            $push_settings = $this->select_data_by_condition('user_preferences', $push_array, $data = 'id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                            if(count($push_settings) > 0) {
                                // Get device type and token key
                                $contition_array = array('id' => $user['user_id'], 'deleted' => 0, 'status' => 1);
                                $user_info = $this->select_data_by_condition('users', $contition_array, $data = 'device_type, token_key', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                                if($user_info[0]['device_type'] != '' && $user_info[0]['token_key'] != ''&& $user_info[0]['token_key'] != 'temp') {
                                    $this->push_notification($notif_id, $user_info[0]['device_type'], $user_info[0]['token_key']);
                                }
                            }
                        }
                    }
                }
                break;

            /* Likes on the Feedbacks */
            case 3:

                // Get user from this feedback
                $contition_array = array('feedback_id' => $feedback_id);
                $feedback = $this->select_data_by_condition('feedback', $contition_array, $data = 'user_id, title_id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                // Check for user settings for this notification
                $contition_array = array('user_id' => $feedback[0]['user_id'], 'notification_id' => $notification_id, 'status' => 'on');
                $preferences = $this->select_data_by_condition('user_preferences', $contition_array, $data = 'user_id, status', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                if(count($preferences) > 0) {

                    // Update notification
                    $condition_array = array('notification_id' => $notification_id, 'user_id' => $feedback[0]['user_id'], 'guest_id' => $guest_id, 'feedback_id' => $feedback_id);
                    $notifications = $this->select_data_by_condition('user_notifications', $condition_array, $data = 'id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                    if (count($notifications) > 0) {
                        $this->delete_data('user_notifications', 'id', $notifications[0]['id']);
                    } else {
                        if($feedback[0]['user_id'] != $guest_id) {
                            // Insert new notification
                            $insert_array['user_id'] = $feedback[0]['user_id'];
                            $insert_array['guest_id'] = $guest_id;
                            $insert_array['title_id'] = $feedback[0]['title_id'];
                            $insert_array['feedback_id'] = $feedback_id;
                            $insert_array['notification_id'] = $notification_id;
                            $insert_array['datetime'] = date('Y-m-d H:i:s');

                            // echo "<pre>";
                            // print_r($insert_array);

                            $notif_id = $this->insert_data_getid($insert_array, $tablename = 'user_notifications');
                        }
                    }

                }

                // Send push notification
                if(isset($notif_id)) {
                    $push_array = array('user_id' => $feedback[0]['user_id'], 'notification_id' => 1, 'status' => 'on');
                    $push_settings = $this->select_data_by_condition('user_preferences', $push_array, $data = 'id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                    if(count($push_settings) > 0) {
                        // Get device type and token key
                        $contition_array = array('id' => $feedback[0]['user_id'], 'deleted' => 0, 'status' => 1);
                        $user = $this->select_data_by_condition('users', $contition_array, $data = 'device_type, token_key', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                        if($user[0]['device_type'] != '' && $user[0]['token_key'] != ''&& $user[0]['token_key'] != 'temp') {
                            $this->push_notification($notif_id, $user[0]['device_type'], $user[0]['token_key']);
                        }
                    }
                }

                break;

            /* Feedbacks on my Titles */
            case 4:

                // Get user from this feedback
                $contition_array = array('feedback_id' => $replied_to);
                $feedback = $this->select_data_by_condition('feedback', $contition_array, $data = 'user_id, title_id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                // Check for user settings for this notification
                $contition_array = array('user_id' => $feedback[0]['user_id'], 'notification_id' => $notification_id, 'status' => 'on');
                $preferences = $this->select_data_by_condition('user_preferences', $contition_array, $data = 'user_id, status', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                if(count($preferences) > 0) {

                    // Update notification
                    $condition_array = array('notification_id' => $notification_id, 'user_id' => $feedback[0]['user_id'], 'guest_id' => $guest_id, 'feedback_id' => $feedback_id);
                    $notifications = $this->select_data_by_condition('user_notifications', $condition_array, $data = 'id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                    if (count($notifications) > 0) {
                        $notif_id = $notifications[0]['id'];

                        $update_data['feedback_id'] = $feedback_id;
                        $this->update_data($update_data, 'user_notifications', 'id', $notif_id);
                    } else {
                        if($feedback[0]['user_id'] != $guest_id) {
                            // Insert new notification
                            $insert_array['user_id'] = $feedback[0]['user_id'];
                            $insert_array['guest_id'] = $guest_id;
                            $insert_array['title_id'] = $feedback[0]['title_id'];
                            $insert_array['feedback_id'] = $feedback_id;
                            $insert_array['notification_id'] = $notification_id;
                            $insert_array['datetime'] = date('Y-m-d H:i:s');

                            // echo "<pre>";
                            // print_r($insert_array);

                            $notif_id = $this->insert_data_getid($insert_array, $tablename = 'user_notifications');
                        }
                    }
                }

                // Send push notification
                if(isset($notif_id)) {
                    $push_array = array('user_id' => $feedback[0]['user_id'], 'notification_id' => 1, 'status' => 'on');
                    $push_settings = $this->select_data_by_condition('user_preferences', $push_array, $data = 'id', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                    if(count($push_settings) > 0) {
                        // Get device type and token key
                        $contition_array = array('id' => $feedback[0]['user_id'], 'deleted' => 0, 'status' => 1);
                        $user = $this->select_data_by_condition('users', $contition_array, $data = 'device_type, token_key', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

                        if($user[0]['device_type'] != '' && $user[0]['token_key'] != ''&& $user[0]['token_key'] != 'temp') {
                            $this->push_notification($notif_id, $user[0]['device_type'], $user[0]['token_key']);
                        }
                    }
                }
                break;
        }

    }

}

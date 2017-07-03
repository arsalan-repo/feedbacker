<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public $data;

    public function __construct() {

        parent::__construct();
		
		// Prevent access without login
		if(!isset($this->session->userdata['mec_user'])){
			redirect();
		}
		
		// Load library
		$this->load->library('template', 'pagination');
		
        //site setting details
        $this->load->model('common');
        $site_name_values = $this->common->select_data_by_id('settings', 'setting_id', '1', '*');

        $this->data['site_name'] = $site_name = $site_name_values[0]['setting_value'];
		
        //set header, footer and leftmenu
        $this->data['title'] = 'Dashboard | ' . $site_name;

        //remove catch so after logout cannot view last visited page if that page is this
        $this->output->set_header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
    }

    //display dashboard
    public function index() {
		// Session data
		$user_info = $this->session->userdata['mec_user'];
		$this->data['user_info'] = $user_info;
		
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Dashboard';
		
		// Get user country
		$country = $this->input->post('country');
		
		if($country == '') {
			$getcountry = $this->common->select_data_by_id('users', 'id', $user_info['id'], 'country', '');
			$country = $getcountry[0]['country'];
		}
		
		if(!empty($country)) {
			$contition_array = array('replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1, 'feedback.country' => $country);
		} else {
			$contition_array = array('replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		}
		
		// Get all feedbacks
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
		
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');
		
		// Trends
		$join_str_tr = array(
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'inner'
			)
		);
		
		if(!empty($country)) {
			$contition_array = array('feedback.deleted' => 0, 'feedback.status' => 1, 'feedback.country' => $country);
		} else {
			$contition_array = array('feedback.deleted' => 0, 'feedback.status' => 1);
		}
		
		$this->data['trends'] = $this->common->select_data_by_condition('feedback', $contition_array, 'feedback_id, feedback.title_id, title, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time', $sortby = 'count(db_feedback.title_id)', $orderby = 'DESC', $limit = '10', $offset = '', $join_str_tr, $group_by = 'feedback.title_id');
		
		// What to Follow
		$join_str_wt = array(
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
		
		$this->data['to_follow'] = $this->common->select_data_by_condition('feedback', $contition_array, 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time', $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '10', $offset = '', $join_str_wt, $group_by = 'feedback.title_id');
		
		$return_array = array();
		$total_records = count($feedback);
		
		// Pagination
		/*
		$config = array();
		$config["base_url"] = base_url('dashboard/index');
		$config["total_rows"] = $total_records;
		$config["per_page"] = 15;
		$config['uri_segment'] = 3;
		$config['use_page_numbers'] = TRUE;
		$config['num_links'] = 2;
		$config['cur_tag_open'] = '&nbsp;<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Previous';
		
		$this->pagination->initialize($config);

		if ($this->uri->segment(3)) {
			$page = ($this->uri->segment(3)) ;
		} else {
			$page = 0;
		}
		
		$page > 0 ? $offset = ($page - 1) * $config['per_page'] + 1 : 0;
		$str_links = $this->pagination->create_links();
		$this->data['links'] = explode('&nbsp;',$str_links);
		
		$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $config["per_page"], $offset, $join_str, $group_by = '');
		*/
		$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');
		//echo $this->db->last_query();
		
		if($total_records > 0) {
			foreach ($feedback as $item) {
				$return = array();
				$return['id'] = $item['feedback_id'];
				$return['title_id'] = $item['title_id'];                
				$return['title'] = $item['title'];
				
				// Get likes for this feedback
				$contition_array_lk = array('feedback_id' => $item['feedback_id']);
				$flikes = $this->common->select_data_by_condition('feedback_likes', $contition_array_lk, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
				
				$return['likes'] = "";
				
				if(count($flikes) > 1000) {
					$return['likes'] = (count($flikes)/1000)."k";
				} else {
					$return['likes'] = count($flikes);
				}
				
				// Get followers for this title
				$contition_array_fo = array('title_id' => $item['title_id']);
				$followings = $this->common->select_data_by_condition('followings', $contition_array_fo, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
				
				$return['followers'] = "";
				
				if(count($followings) > 1000) {
					$return['followers'] = (count($followings)/1000)."k";
				} else {
					$return['followers'] = count($followings);
				}
				
				// Check If user liked this feedback
				$contition_array_li = array('feedback_id' => $item['feedback_id'], 'user_id' => $user_info['id']);
				$likes = $this->common->select_data_by_condition('feedback_likes', $contition_array_li, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
							
				if(count($likes) > 0) {
					$return['is_liked'] = TRUE;
				} else {
					$return['is_liked'] = FALSE;
				}
				
				// Check If user followed this title
				$contition_array_ti = array('title_id' => $item['title_id'], 'user_id' => $user_info['id']);
				$followtitles = $this->common->select_data_by_condition('followings', $contition_array_ti, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
							
				if(count($followtitles) > 0) {
					$return['is_followed'] = TRUE;
				} else {
					$return['is_followed'] = FALSE;
				}
				
				$return['name'] = $item['name'];
				
				if(isset($item['photo'])) {
					$return['user_avatar'] = S3_CDN . 'uploads/user/thumbs/' . $item['photo'];
				} else {
					$return['user_avatar'] = ASSETS_URL . 'images/user-avatar.png';
				}
				
				if($item['feedback_img'] !== "") {
					$return['feedback_img'] = S3_CDN . 'uploads/feedback/main/' . $item['feedback_img'];
				} else {
					$return['feedback_img'] = "";
				}

				if($item['feedback_thumb'] !== "") {
					$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/' . $item['feedback_thumb'];
				} elseif($item['feedback_img'] !== "") {
					$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/main/' . $item['feedback_img'];
				} else {
					$return['feedback_thumb'] = "";
				}
				
				if($item['feedback_video'] !== "") {
					$return['feedback_video'] = S3_CDN . 'uploads/feedback/video/' . $item['feedback_video'];
					//$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/video_thumbnail.png';
				} else {
					$return['feedback_video'] = "";
				}

				$return['location'] = $item['location'];
				$return['feedback'] = $item['feedback_cont'];
				$return['time'] = $this->common->timeAgo($item['time']);

				array_push($return_array, $return);
			}
			
			// Append Ad Banners
			$join_str = array(
				array(
					'table' => 'titles',
					'join_table_id' => 'titles.title_id',
					'from_table_id' => 'ads.title_id',
					'join_type' => 'left'
				)
			);
	
			$contition_array = array('ads.show_on' => 'home', 'ads.country' => $country, 'ads.status' => 1, 'ads.deleted' => 0);
			
			$data = 'ads_id, ads.title_id, title, usr_name, usr_img, ads_cont, ads_img, ads_thumb, ads_video, ads.country, ads.show_on, ads.show_after, ads.repeat_for, ads.status, ads.datetime as time';
			
			$ads_list = $this->common->select_data_by_condition('ads', $contition_array, $data, $short_by = 'ads.datetime', $order_by = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');
			
			foreach($ads_list as $ads) {
				$adArray = array(
					array(
						'id' => '',
						'title_id' => '',
						'title' => '',
						'likes' => 0,
						'followers' => 0,
						'is_liked' => '',
						'is_followed' => '',
						'name' => $ads['usr_name'],
						'feedback_video' => '',
						'location' => '',
						'feedback' => $ads['ads_cont'],
						'ads' => 1
					)
				);
				
				if(isset($ads['usr_img'])) {
					$adArray[0]['user_avatar'] = S3_CDN . 'uploads/user/thumbs/' . $ads['usr_img'];
				} else {
					$adArray[0]['user_avatar'] = ASSETS_URL . 'images/user-avatar.png';
				}
				
				if($ads['ads_img'] !== "") {
					$adArray[0]['feedback_img'] = S3_CDN . 'uploads/feedback/main/' . $ads['ads_img'];
				} else {
					$adArray[0]['feedback_img'] = "";
				}

				if($ads['ads_thumb'] !== "") {
					$adArray[0]['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/' . $ads['ads_thumb'];
				} elseif($ads['ads_img'] !== "") {
					$adArray[0]['feedback_thumb'] = S3_CDN . 'uploads/feedback/main/' . $ads['ads_img'];
				} else {
					$adArray[0]['feedback_thumb'] = "";
				}
					
				$adArray[0]['time'] = $this->common->timeAgo($ads['time']);
				
				// Check If banner has to be repeated
				if($ads['repeat_for'] > 0) {
					$i = 0;
					$total = $ads['show_after'] * $ads['repeat_for'];
					for($n = 1; $n <= $total; $n++) {
						if($n%$ads['show_after'] == 0) {
							array_splice($return_array, $n+$i, 0, $adArray);
							$i++;
						}
					}	
				} else {
					array_splice($return_array, $ads['show_after'], 0, $adArray);
				}
			}
			// End Ad Banners

			// Null to Empty String
			array_walk_recursive($return_array, function (&$item, $key) {
				$item = null === $item ? '' : $item;
			});
			
			$this->data['feedbacks'] = $return_array;
			
			//echo "<pre>";
			//print_r($return_array);
			
		} else {
			$this->data['feedbacks'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}
		
        /* Load Template */
		$this->template->front_render('user/dashboard', $this->data);
    }

}

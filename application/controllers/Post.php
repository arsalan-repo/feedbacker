<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller {
	
	public $data;

    public function __construct() {
        parent::__construct();

        // Prevent access without login
		if(!isset($this->session->userdata['mec_user'])){
			redirect();
		}
		
		// Load library
		$this->load->library('template');

        $this->data['title'] = "Post | Feedbacker ";

        // Load Login Model
        $this->load->model('common');
		
		// Load Language File
		$this->lang->load('message','english');

        //remove catch so after logout cannot view last visited page if that page is this
        $this->output->set_header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
    }

	public function index() {
		/* Load Template */
		$this->template->front_render('user/dashboard');
	}
	
	public function get_location() {
		if ($this->input->is_ajax_request()) {
			$latitude = $this->input->post('latitude');
			$longitude = $this->input->post('longitude');			
			
			if(!empty($latitude) && !empty($longitude)){
				//Send request and receive json data by latitude and longitude
				$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
				$json = @file_get_contents($url);
				$data = json_decode($json);
				$status = $data->status;
				if($status=="OK"){
					//Get address from json data
					$location = $data->results[0]->formatted_address;
				}else{
					$location =  '';
				}
				//Print address 
				echo json_encode(array("location" => $location));
			}
		}
	}
	
	public function create() {
		//check post and save data
        if ($this->input->is_ajax_request() && $this->input->post('btn_save')) {
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('user/profile');
			}
		}
		
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Create';
		
		/* Load Template */
		$this->template->front_render('post/create', $this->data);
	}
	
	public function title($id) {
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Title';
		
		// Session data
		$user_info = $this->session->userdata['mec_user'];
		$this->data['user_info'] = $user_info;
		
		// Trends
		$join_str_tr = array(
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'inner'
			)
		);
		
		if(!empty($user_info['country'])) {
			$contition_array = array('feedback.deleted' => 0, 'feedback.status' => 1, 'feedback.country' => $user_info['country']);
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
		
		// Get Feedbacks from Title ID
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
			
		$contition_array = array('feedback.title_id' => $id, 'feedback.replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');
		$return_array = array();
		
		if(!empty($feedback)) {
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
	
				// Check If user reported this feedback
				$contition_array_rs = array('feedback_id' => $item['feedback_id'], 'user_id' => $user_info['id']);
				$spam = $this->common->select_data_by_condition('spam', $contition_array_rs, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
							
				if(count($spam) > 0) {
					$return['report_spam'] = TRUE;
				} else {
					$return['report_spam'] = FALSE;
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
				} else {
					$return['feedback_thumb'] = "";
				}
				
				if($item['feedback_video'] !== "") {
					$return['feedback_video'] = S3_CDN . 'uploads/feedback/video/' . $item['feedback_video'];
					//$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/video_thumbnail.png';
				} else {
					$return['feedback_video'] = "";
				}
	
				$return['feedback'] = $item['feedback_cont'];
				$return['location'] = $item['location'];                
				$return['time'] = $this->common->timeAgo($item['time']);
	
				array_push($return_array, $return);
			}

			$this->data['feedbacks'] = $return_array;
		} else {
			$this->data['feedbacks'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}
		
		/* Load Template */
		$this->template->front_render('post/title', $this->data);
	}
	
	public function detail($id) {
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Detail';
		
		// Session data
		$user_info = $this->session->userdata['mec_user'];
		
		// Get Feedback Details
        $return_array = $this->common->getFeedbackDetail($user_info['id'], $id);

        // Get all replies for this feedback
        $contition_array = array('replied_to' => $id, 'feedback.deleted' => 0, 'feedback.status' => 1);
        $replies = $this->common->select_data_by_condition('feedback', $contition_array, 'feedback_id', $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str = array(), $group_by = '');
        
        $return_array['replies'] = array();
        foreach($replies as $reply) {
            $feedback = $this->common->getFeedbackDetail($id, $reply['feedback_id']);
            array_push($return_array['replies'], $feedback);
        }
		
		$this->data['feedback'] = $return_array;
		
		// Get feedbacks from same User
		$others_array = array();
		
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
		
		$contition_array = array('users.id' => $return_array['user_id'], 'replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		$others = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '2', $offset = '', $join_str, $group_by = '');
		
		if(count($others) > 0) {
			foreach ($others as $item) {
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

				array_push($others_array, $return);
			}
			
			$this->data['others'] = $others_array;
			
		} else {
			$this->data['others'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}
		
		/* Load Template */
		$this->template->front_render('post/detail', $this->data);
	}
	
	public function search() {
		//check post and save data
        if ($this->input->is_ajax_request() && $this->input->post('btn_save')) {
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('user/profile');
			}
		}
		
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Search';
		
		/* Load Template */
		$this->template->front_render('post/search', $this->data);
	}
	
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
	public $data;

    public function __construct() {
        parent::__construct();

        // Prevent access without login
		if(!isset($this->session->userdata['mec_user'])){
			redirect();
		}
		
		// Load library
		$this->load->library('template', 'facebook');

        $this->data['title'] = "User | Feedbacker ";

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
	
	public function profile() {
		//check post and save data
        if ($this->input->is_ajax_request() && $this->input->post('btn_save')) {
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('user/profile');
			}
		}
		
		// Session data
		$user_info = $this->session->userdata['mec_user'];
		$this->data['user_info'] = $user_info;
		
		// Get All Feedbacks by User
		$join_str = array(
			array(
				'table' => 'users',
				'join_table_id' => 'users.id',
				'from_table_id' => 'feedback.user_id',
				'join_type' => 'inner'
			),
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'inner'
			)
		);
		
		$contition_array = array('feedback.user_id' => $user_info['id'], 'feedback.replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');
		if(!empty($feedback)) {
			$return_array = array();
			
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
				
				$this->data['feedbacks'] = $return_array;
            }
		} else {
			$this->data['feedbacks'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}	
		
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Feedbacks';
		
		/* Load Template */
		$this->template->front_render('user/profile', $this->data);
	}
	
	public function followings() {
		// Session data
		$user_info = $this->session->userdata['mec_user'];
		$this->data['user_info'] = $user_info;
		
		// Get Followings for User
		$join_str = array(
			array(
				'table' => 'users',
				'join_table_id' => 'users.id',
				'from_table_id' => 'feedback.user_id',
				'join_type' => 'inner'
			),
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'inner'
			),
			array(
				'table' => 'followings',
				'join_table_id' => 'followings.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'inner'
			)
		);

		$contition_array = array('followings.user_id' => $user_info['id'], 'feedback.replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str, $group_by = '');
		if(!empty($feedback)) {
			$return_array = array();
            
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
			
			$this->data['followings'] = $return_array;
		} else {
			$this->data['feedbacks'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}
		
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Followings';
		
        /* Load Template */
		$this->template->front_render('user/followings', $this->data);
	}
	
	public function settings() {
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Settings';
		
		/* Load Template */
		$this->template->front_render('user/settings', $this->data);
	}
	
	public function notifications() {
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Notifications';
		
		$user_info = $this->session->userdata['mec_user'];
		
		$n_array = array();
		
		/* Titles I Follow */
		$n_follow = $this->common->get_notification($user_info['id'], 2);
		if(count($n_follow) > 0) {
			$n_array = array_merge($n_array, $n_follow);
		}

		/* Likes on the Feedbacks */
		$n_likes = $this->common->get_notification($user_info['id'], 3);
		if(count($n_likes) > 0) {
			$n_array = array_merge($n_array, $n_likes);
		}

		/* Feedbacks on my Titles */
		$n_reply = $this->common->get_notification($user_info['id'], 4);
		if(count($n_reply) > 0) {
			$n_array = array_merge($n_array, $n_reply);
		}
		
		// Sort array by id
		usort($n_array, function($a, $b) {
			return $b['id'] - $a['id'];
		});
		
		if(!empty($n_array)) {
			$this->data['notifications'] = $n_array;
		} else {
			$this->data['notifications'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}
		
		/* Load Template */
		$this->template->front_render('user/notifications', $this->data);
	}
	
    //logout user
    public function logout() {
		// Remove local Facebook session
		//$this->facebook->destroy_session();
		
        if ($this->session->userdata('mec_user')) {
            $this->session->unset_userdata('mec_user');
        }
        redirect();
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
	public $data;
	
	public $user;
	
	private $perPage = 10;

    public function __construct() {
        parent::__construct();

        // Prevent access without login
		if(!isset($this->session->userdata['mec_user'])){
			redirect();
		}
		
		// Load library
		$this->load->library('s3');
		$this->load->library('template', 'facebook');

        $this->data['title'] = "User | Feedbacker ";

        // Load Login Model
        $this->load->model('common');
		
		// Session data
		$this->user = $this->session->userdata['mec_user'];
		$this->data['user_info'] = $this->user;
		
		// Load Language File		
		if ($this->user['language'] == 'ar') {
			$this->lang->load('message','arabic');
			$this->lang->load('label','arabic');
		} else {
			$this->lang->load('message','english');
			$this->lang->load('label','english');
		}

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
	
	public function language($code) {
		$this->user['language'] = $code;  
		
		$this->session->set_userdata('mec_user', $this->user);
		redirect();
	}
	
	//display dashboard
    public function dashboard($country = '') {
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Dashboard';
		$this->data['user_id'] = $this->user['id'];
		
		// Get user country		
		if($country == '') {
			$getcountry = $this->common->select_data_by_id('users', 'id', $this->user['id'], 'country', '');
			//$country = $getcountry[0]['country'];
			$country = $this->user['country'];
		} else {
			$this->user['country'] = $country;  
			
			$this->session->set_userdata('mec_user', $this->user);	
		}
		
		// Trends
		$this->data['trends'] = $this->common->getTrends($country);
		
		// What to Follow
		$this->data['to_follow'] = $this->common->whatToFollow($this->user['id'], $country);
		
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
		
		if(!empty($country)) {
			$contition_array = array('replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1, 'feedback.country' => $country);
		} else {
			$contition_array = array('replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		}
		
		$data = 'feedback_id, feedback.title_id, title, users.id as user_id, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		if (!empty($this->input->get("page"))) {
			$start = ceil($this->input->get("page") * $this->perPage);
			
			$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $this->perPage, $start, $join_str, $group_by = '');
			
			if(count($feedback) > 0) {
				// Get Likes, Followings and Other details
				$result = $this->common->getFeedbacks($feedback, $this->user['id']);
				
				// Append Ad Banners
				$return_array = $this->common->adBanners($result, $country, $this->input->get("page"));
				
				$this->data['feedbacks'] = $return_array;
			} else {
				$this->data['feedbacks'] = array();
				$this->data['no_record_found'] = $this->lang->line('no_results');
			}
			
			$response = $this->load->view('user/ajax', $this->data);
			echo json_encode($response);
		} else {
			$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $this->perPage, 0, $join_str, $group_by = '');
			
			if(count($feedback) > 0) {
				// Get Likes, Followings and Other details
				$result = $this->common->getFeedbacks($feedback, $this->user['id']);
				
				// Append Ad Banners
				$return_array = $this->common->adBanners($result, $country);
				
				$this->data['feedbacks'] = $return_array;
			} else {
				$this->data['feedbacks'] = array();
				$this->data['no_record_found'] = $this->lang->line('no_results');
			}
		
			/* Load Template */
			$this->template->front_render('user/dashboard', $this->data);
		}			
    }
	
	public function profile() {
		// Check post and save data
        if ($this->input->is_ajax_request() && $this->input->post('btn_save')) {
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
//			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			$this->form_validation->set_rules('country', 'Country', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				echo json_encode(array('error' => validation_errors(), 'status' => 0));
                die();
			}
			
			$user_id = $this->input->post('user_id');
			$gender = $this->input->post('gender');
			$name = $this->input->post('name');
//			$email = $this->input->post('email');
			$country = $this->input->post('country');
			$dob = $this->input->post('dob');
			
			$update_data = array();
			
			if ($gender != '') {
                $update_data['gender'] = $gender;
            }
            if ($name != '') {
                $update_data['name'] = $name;
            }
            /*if ($email != '') {
                $update_data['email'] = $email;
            }*/
            if ($country != '') {
                $update_data['country'] = $country;
            }
            if ($dob != '') {
                $update_data['dob'] = $dob;
            }
            
			// Image Upload Starts
            if (isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != '') {
                $config['upload_path'] = $this->config->item('user_main_upload_path');
                $config['thumb_upload_path'] = $this->config->item('user_thumb_upload_path');
                $config['allowed_types'] = 'jpg|png|jpeg|gif';
                $config['file_name'] = time();

                $this->load->library('upload');
                $this->upload->initialize($config);
                
                //Uploading Image
                $this->upload->do_upload('photo');
                
                //Getting Uploaded Image File Data
                $imgdata = $this->upload->data();
                $imgerror = $this->upload->display_errors();
                
                if ($imgerror == '') {
                    
                    //Configuring Thumbnail 
                    $config_thumb['image_library'] = 'gd2';
                    $config_thumb['source_image'] = $config['upload_path'] . $imgdata['file_name'];
                    $config_thumb['new_image'] = $config['thumb_upload_path'] . $imgdata['file_name'];
                    $config_thumb['create_thumb'] = TRUE;
                    $config_thumb['maintain_ratio'] = FALSE;
                    $config_thumb['thumb_marker'] = '';
                    $config_thumb['width'] = $this->config->item('user_thumb_width');
                    $config_thumb['height'] = $this->config->item('user_thumb_height');

                    //Loading Image Library
                    $this->load->library('image_lib', $config_thumb);
                    $dataimage = $imgdata['file_name'];
                    
                    //Creating Thumbnail
                    $this->image_lib->resize();
                    $thumberror = $this->image_lib->display_errors();
                    
                    // AWS S3 Upload
                    $thumb_file_path = str_replace("main", "thumbs", $imgdata['file_path']);
                    $thumb_file_name = $config['thumb_upload_path'] . $imgdata['raw_name'].$imgdata['file_ext'];
                    
                    $this->s3->putObjectFile($imgdata['full_path'], S3_BUCKET, $config_thumb['source_image'], S3::ACL_PUBLIC_READ);
                    $this->s3->putObjectFile($thumb_file_path.$dataimage, S3_BUCKET, $thumb_file_name, S3::ACL_PUBLIC_READ);

                    // Remove File from Local Storage
                    unlink($config_thumb['source_image']);
                    unlink($thumb_file_name);
                } else {
                    $thumberror = '';
                }

                if ($imgerror != '' || $thumberror != '') {
                    $error[0] = $imgerror;
                    $error[1] = $thumberror;
                } else {
                    $main_old_file = $this->config->item('user_main_upload_path') . $this->user['photo'];
                    $thumb_old_file = $this->config->item('user_thumb_upload_path') . $this->user['photo'];

                    $error = array();
                }

                if ($error) {
                    echo json_encode(array('message' => $error[0], 'status' => 0));
                    die();
                }
				
                $update_data['photo'] = $dataimage;
				
				$this->user['photo'] = $dataimage;  
            } // Image Upload Ends
			
			if(!empty($update_data)) {
				// Update Session Data
				$this->user['name'] = $name;
				$this->user['country'] = $country;
				$this->user['gender'] = $gender;
				$this->user['dob'] = $dob;
				
				$this->session->set_userdata('mec_user', $this->user);
                $this->common->update_data($update_data, 'users', 'id', $user_id);
				
				echo json_encode(array('message' => $this->lang->line('success_msg_profile_saved'), 'status' => 1));
                die();
			} else {
				echo json_encode(array('message' => $this->lang->line('success_no_profile_update'), 'status' => 0));
                die();
			}
		}
		
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Profile';
		
		// Get User Information
		$contition_array = array('id' => $this->user['id']);
		$user_result = $this->common->select_data_by_condition('users', $contition_array, $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array());
		
		$this->data['user_data'] = $user_result[0];
		$this->data['country_list'] = $this->common->select_data_by_condition('countries', $contition_array = array(), '*', $short_by = 'country_name', $order_by = 'ASC', $limit = '', $offset = '');
		
		/* Load Template */
		$this->template->front_render('user/profile', $this->data);
	}
	
	public function feedbacks() {
		if ($this->input->is_ajax_request()) {
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
			
			$contition_array = array('feedback.user_id' => $this->user['id'], 'feedback.replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
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
					$contition_array_rs = array('feedback_id' => $item['feedback_id'], 'user_id' => $this->user['id']);
					$spam = $this->common->select_data_by_condition('spam', $contition_array_rs, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
								
					if(count($spam) > 0) {
						$return['report_spam'] = TRUE;
					} else {
						$return['report_spam'] = FALSE;
					}
					
					// Check If user liked this feedback
					$contition_array_li = array('feedback_id' => $item['feedback_id'], 'user_id' => $this->user['id']);
					$likes = $this->common->select_data_by_condition('feedback_likes', $contition_array_li, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
								
					if(count($likes) > 0) {
						$return['is_liked'] = TRUE;
					} else {
						$return['is_liked'] = FALSE;
					}
					
					// Check If user followed this title
					$contition_array_ti = array('title_id' => $item['title_id'], 'user_id' => $this->user['id']);
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
			$response = $this->load->view('user/feedbacks', $this->data);
			echo json_encode($response);
		}
	}
	
	public function followings() {
		if ($this->input->is_ajax_request()) {
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
	
			$contition_array = array('followings.user_id' => $this->user['id'], 'feedback.replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
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
					$contition_array_li = array('feedback_id' => $item['feedback_id'], 'user_id' => $this->user['id']);
					$likes = $this->common->select_data_by_condition('feedback_likes', $contition_array_li, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
								
					if(count($likes) > 0) {
						$return['is_liked'] = TRUE;
					} else {
						$return['is_liked'] = FALSE;
					}
					
					// Check If user followed this title
					$contition_array_ti = array('title_id' => $item['title_id'], 'user_id' => $this->user['id']);
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
				$this->data['followings'] = array();
				$this->data['no_record_found'] = $this->lang->line('no_record_found');
			}
			
			$this->data['module_name'] = 'User';
			$this->data['section_title'] = 'Followings';
			
			/* Load Template */
			$response = $this->load->view('user/followings', $this->data);
			echo json_encode($response);
		}
	}
	
	public function settings() {
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Settings';
		
		// Get Languages
		$contition_array = array('lang_status' => 1);
        $this->data['languages'] = $this->common->select_data_by_condition('languages', $contition_array, $data = 'lang_id, lang_code, lang_name');
		
		// Get Settings
		$condition_array = array('page_id' => 'terms_cond', 'pages.lang_code' => $this->user['language']);
        $this->data['terms'] = $this->common->select_data_by_condition('pages', $condition_array, 'id, name, description');
		
		/* Load Template */
		$this->template->front_render('user/settings', $this->data);
	}
	
	// Set Language
    function set_language() {
        $lang_id = $this->input->post('lang_id');

        if ($lang_id == '') {
            echo json_encode(array('RESULT' => array(), 'MESSAGE' => 'Please select your language', 'STATUS' => 0));
            die();
        }

        $data = array('lang_id' => $lang_id);
        $update_settings = $this->common->update_data($data, 'users', 'id', $this->user['id']);

        if($update_settings) {
			// Update Session Data
			$this->user['lang_id'] = $lang_id;
			
			$contition_array = array('lang_id' => $lang_id);
			$language = $this->common->select_data_by_condition('languages', $contition_array, $data = 'lang_code');
			$this->user['language'] = $language[0]['lang_code'];
			
			$this->session->set_userdata('mec_user', $this->user);
			
			echo json_encode(array('message' => $this->lang->line('success_language_set'), 'status' => 1));
            die();
        } else {
            echo json_encode(array('message' => $this->lang->line('error_something_wrong'), 'status' => 0));
            die();
        }
    }
	
	//Change Password
    function change_password() {
        $old_pass = $this->input->post('old_pass');
        $new_pass = $this->input->post('new_pass');

        $error = '';
        if ($old_pass == '') {
            $error = 1;
            echo json_encode(array('message' => $this->lang->line('error_msg_old_pass_message'), 'status' => 0));
            die();
        }
        if ($new_pass == '') {
            $error = 1;
            echo json_encode(array('message' => $this->lang->line('error_msg_new_pass_message'), 'status' => 0));
            die();
        }
        if ($error == 1) {
            echo json_encode(array('message' => $this->lang->line('error_msg_details'), 'status' => 0));
            die();
        } else {

            // check old password is correct or not
            $check_old_pass = $this->common->select_data_by_id('users', 'id', $this->user['id'], $data = 'password', $join_str = array());

            if (count($check_old_pass) > 0) {
                $old_db_pass = $check_old_pass[0]['password'];

                if (md5($old_pass) != $old_db_pass) {
                    echo json_encode(array('message' => $this->lang->line('error_msg_correct_old_password'), 'status' => 0));
                    die();
                } else {
                    $data = array('password' => md5($new_pass));
                    $update = $this->common->update_data($data, 'users', 'id', $this->user['id']);
					
                    echo json_encode(array('message' => $this->lang->line('success_change_password'), 'status' => 1));
                    die();
                }
            } else {
                echo json_encode(array('message' => $this->lang->line('no_record_found'), 'status' => 0));
                die();
            }
        }
    }
	
	// Contact Us
    function contact_us() {
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $message = $this->input->post('message');

        if ($name == '') {
            $error = 1;
            echo json_encode(array('RESULT' => array(), 'MESSAGE' => $this->lang->line('error_msg_name'), 'STATUS' => 0));
            exit();
        }
        if ($email == '') {
            $error = 1;
            echo json_encode(array('RESULT' => array(), 'MESSAGE' => $this->lang->line('error_msg_email'), 'STATUS' => 0));
            exit();
        }
		if ($message == '') {
            $error = 1;
            echo json_encode(array('RESULT' => array(), 'MESSAGE' => $this->lang->line('error_msg_message'), 'STATUS' => 0));
            exit();
        } else {

            $insert_array['name'] = $name;
            $insert_array['email'] = trim($email);
            $insert_array['message'] = $message;
            $insert_array['posted_on'] = date('Y-m-d h:i:s');

            $insert_result = $this->common->insert_data_getid($insert_array, $tablename = 'contactus');
            $condition_array = array('emailid' => '3');
            $emailformat = $this->common->select_data_by_condition('emails', $condition_array, '*');

            $mail_body = $emailformat[0]['varmailformat'];

            $phone = 'N/A';
            $subject = 'You\'ve got new enquiry!';

            $mail_body = html_entity_decode(str_replace("%name%", ucfirst($name), str_replace("%user_email%", $email, str_replace("%phone%", $phone, str_replace("%subject%", $subject, str_replace("%message%", $message, stripslashes($mail_body)))))));

            // Find where to send new enquiry
            $settings = $this->common->getSettings('contact_mail');

            $send_mail = $this->common->sendMail($settings[0]['setting_value'], '', $emailformat[0]['varsubject'], $mail_body);

            if ($send_mail) {
                echo json_encode(array('message' => $this->lang->line('success_msg_sent_message'), 'status' => 1));
                exit();
            } else {
                echo json_encode(array('message' => $this->lang->line('error_msg_not_able_to_send_msg'), 'status' => 0));
                exit();
            }
        }
    }
	
	public function notifications() {
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Notifications';
		
		$n_array = array();
		
		/* Titles I Follow */
		$n_follow = $this->common->get_notification($this->user['id'], 2);
		if(count($n_follow) > 0) {
			$n_array = array_merge($n_array, $n_follow);
		}

		/* Likes on the Feedbacks */
		$n_likes = $this->common->get_notification($this->user['id'], 3);
		if(count($n_likes) > 0) {
			$n_array = array_merge($n_array, $n_likes);
		}

		/* Feedbacks on my Titles */
		$n_reply = $this->common->get_notification($this->user['id'], 4);
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

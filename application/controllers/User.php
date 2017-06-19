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

    function update_profile() {
        $user_id = $this->input->post('user_id');
        $gender = $this->input->post('gender');
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $country = $this->input->post('country');
        $dob = $this->input->post('dob');
        
        $update_data = array();
        $error = '';
        
        if ($user_id == '') {
            $error = 1;
            echo json_encode(array('RESULT' => array(), 'MESSAGE' => 'Please enter user id', 'STATUS' => 0));
            die();
        } else {
            if ($email != '') {
                $condition_array = array('id !=' => $user_id);
                $check_result = $this->common->check_unique_avalibility('users', 'email', $email, '', '', $condition_array);
    
                if ($check_result == 1) {
                    $error = 1;
                    echo json_encode(array('RESULT' => array(), 'MESSAGE' => $this->lang->line('error_msg_email_exits'), 'STATUS' => 0));
                    //$this->returnData($data = array(), $message = "Email id already exits", $status = 0);
                    die();
                }
            }

            $condition_array = array('id' => $user_id);
            $user_data = $this->common->select_data_by_condition('users', $condition_array, $data = 'id, name, email, gender, dob, country, photo', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');

            if ($gender != '') {
                $update_data['gender'] = $gender;
            }
            if ($name != '') {
                $update_data['name'] = $name;
            }
            if ($email != '') {
                $update_data['email'] = $email;
            }
            if ($country != '') {
                $update_data['country'] = $country;
            }
            if ($dob != '') {
                $update_data['dob'] = $dob;
            }
            
            if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                $config['upload_path'] = $this->config->item('user_main_upload_path');
                $config['thumb_upload_path'] = $this->config->item('user_thumb_upload_path');
                $config['allowed_types'] = 'jpg|png|jpeg|gif';
                $config['file_name'] = time();

                $this->load->library('upload');
                $this->upload->initialize($config);
                
                //Uploading Image
                $this->upload->do_upload('image');
                
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
//                  echo $s3file = S3_CDN.$config_thumb['source_image'];
//                  echo "<br/>";
//                  echo $s3file = S3_CDN.$thumb_file_name; exit();

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
                    $main_old_file = $this->config->item('user_main_upload_path') . $user_data[0]['photo'];
                    $thumb_old_file = $this->config->item('user_thumb_upload_path') . $user_data[0]['photo'];

                    /*    if (file_exists($main_old_file)) {
                      unlink($main_old_file);
                      }
                      if (file_exists($thumb_old_file)) {
                      unlink($thumb_old_file);
                      } */
                    $error = array();
                }

                if ($error) {
                    echo json_encode(array('RESULT' => array(), 'MESSAGE' => $error[0], 'STATUS' => 0));
                    die();
                }
                $update_data['photo'] = $dataimage;
            }
            
            if(!empty($update_data)) {
                $this->common->update_data($update_data, 'users', 'id', $user_id);
                if(isset($update_data['gender'])) {
                    $user_data[0]['gender'] = $update_data['gender'];
                }
                if(isset($update_data['name'])) {
                    $user_data[0]['name'] = $update_data['name'];
                }
                if(isset($update_data['email'])) {
                    $user_data[0]['email'] = $update_data['email'];
                }
                if(isset($update_data['country'])) {
                    $user_data[0]['country'] = $update_data['country'];
                }
                if(isset($update_data['dob'])) {
                    $date = date_create($user_data[0]['dob']);
                    $user_data[0]['dob'] = date_format($date, 'd-M-Y');
                } else {
                    $user_data[0]['dob'] = "";
                }

                if(isset($update_data['photo'])) {
                    $user_data[0]['photo'] = S3_CDN . 'uploads/user/thumbs/' . $update_data['photo'];
                } elseif(isset($user_data[0]['photo'])) {
                    $user_data[0]['photo'] = S3_CDN . 'uploads/user/thumbs/' . $user_data[0]['photo'];
                } else {
                    $user_data[0]['photo'] = ASSETS_URL . 'images/user-avatar.png';
                }

                array_walk_recursive($user_data, function (&$item, $key) {
                    $item = null === $item ? '' : $item;
                });             
                                                
                echo json_encode(array('RESULT' => $user_data, 'MESSAGE' => $this->lang->line('success_msg_profile_saved'), 'STATUS' => 1));
                die();
            } else {
                if(isset($user_data[0]['photo'])) {
                    $user_data[0]['photo'] = S3_CDN . 'uploads/user/thumbs/' . $user_data[0]['photo'];
                } else {
                    $user_data[0]['photo'] = ASSETS_URL . 'images/user-avatar.png';
                }
                if(isset($user_data[0]['dob'])) {
                    $date = date_create($user_data[0]['dob']);
                    $user_data[0]['dob'] = date_format($date, 'd-M-Y');
                } else {
                    $user_data[0]['dob'] = "";
                }

                array_walk_recursive($user_data, function (&$item, $key) {
                    $item = null === $item ? '' : $item;
                });
                
                echo json_encode(array('RESULT' => $user_data, 'MESSAGE' => $this->lang->line('success_no_profile_update'), 'STATUS' => 0));
                //$this->returnData($data = array(), $message = "Email id already exits", $status = 0);
                die();
            }
        }
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends CI_Controller {
	
	public $data;

    public function __construct() {
        parent::__construct();

        if ($this->session->userdata('mec_user')) {
            redirect('user/dashboard');
        }

        $this->data['title'] = "Login | Feedbacker ";
		
		// Load facebook library
		$this->load->library('facebook');
		
		// Include the twitter oauth php libraries
		include_once APPPATH."libraries/twitter-oauth-php/twitteroauth.php";

        // Load Login Model
        $this->load->model('common');
		
		// Load Language File		
		if ($this->input->get('lang') == 'ar') {
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
		// Get Facebook Login URL
		$this->data['authUrl'] =  $this->facebook->login_url();
		
		// Twitter API Configuration
		$consumerKey = 'sHUkqavFNxaGgr7RvZq94xxE0';
		$consumerSecret = '90MBNCqkFAdETUgAv7YkAeTrZ8dXjTBLWvHAwwgbsYptSzFB1A';
		$oauthCallback = base_url().'signin/twauth/';
		
		//unset token and token secret from session
		$this->session->unset_userdata('token');
		$this->session->unset_userdata('token_secret');
		
		//Fresh authentication
		$connection = new TwitterOAuth($consumerKey, $consumerSecret);
		$requestToken = $connection->getRequestToken($oauthCallback);
		
		//Received token info from twitter
		$this->session->set_userdata('token',$requestToken['oauth_token']);
		$this->session->set_userdata('token_secret',$requestToken['oauth_token_secret']);
		
		//Any value other than 200 is failure, so continue only if http code is 200
		if ($connection->http_code == '200') {
			//redirect user to twitter
			$twitterUrl = $connection->getAuthorizeURL($requestToken['oauth_token']);
			$this->data['oauthURL'] = $twitterUrl;
		}
		

		/* Load Template */
		$this->load->view('user/signin', $this->data);
	}
	
	public function forgot_password() {
		//check post and save data
        if ($this->input->post('btn_save')) {
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('signin/forgot_password');
			}
			
			// Check If Email Exists
			$email = $this->input->post('email');
			
			$condition_array = array('email' => $email);
			$user_info = $this->common->select_data_by_condition('users', $condition_array, 'id, name, email');
			
			if (count($user_info) > 0) {
				// Get Email Template
				$condition_array = array('emailid' => '2');
                $emailformat = $this->common->select_data_by_condition('emails', $condition_array, '*');
                $mail_body = $emailformat[0]['varmailformat'];

                $rand_password = $this->common->randomPassword();
                $md5_rand_password = md5($rand_password);

                $data['password'] = $md5_rand_password;

                $this->common->update_data($data, 'users', 'email', $email);

                $mail_body = html_entity_decode(str_replace("%name%", ucfirst($user_info[0]['name']), str_replace("%user_email%", $user_info[0]['email'], str_replace("%password%", $rand_password, stripslashes($mail_body)))));

                $send_mail = $this->common->sendMail($email, '', $emailformat[0]['varsubject'], $mail_body);
                
                if ($send_mail) {
                    $this->session->set_flashdata('success', $this->lang->line('error_msg_password_sent_to_email'));
	            	redirect();
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('error_email_failed'));
					redirect('signin/forgot_password');
                }
			} else {
				$this->session->set_flashdata('error', $this->lang->line('error_msg_email_not_found'));
				redirect('signin/forgot_password');
			}
		}
		
		/* Load Template */
		$this->load->view('user/forgot_password');
	}
	
	public function auth() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
		
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect();
		}
		
		// Check User Is valid or not
		$userinfo = $this->common->check_login($email, $password);

		if (count($userinfo) > 0) {
			if ($userinfo[0]['status'] == "0") {
				echo json_encode(array('RESULT' => array(), 'MESSAGE' => $this->lang->line('error_account_blocked'), 'STATUS' => 0));
				exit();
			} else {
				$userinfo[0]['username'] = $this->input->post('user_name');
				unset($userinfo[0]['username']);
				unset($userinfo[0]['password']);
				
				if(isset($userinfo[0]['photo'])) {
					$userinfo[0]['user_avatar'] = S3_CDN . 'uploads/user/thumbs/' . $userinfo[0]['photo'];
				} else {
					$userinfo[0]['user_avatar'] = ASSETS_URL . 'images/user-avatar.png';
				}
				
				$languages = $this->common->select_data_by_id('languages', 'lang_id', $userinfo[0]['lang_id'], $data = 'lang_code', $join_str = array());
				$userinfo[0]['language'] = $languages[0]['lang_code'];
				
				if($languages[0]['lang_code'] == 'ar') {
					$this->lang->load('message','arabic');
				}
				
				// Update last login
				$data = array(
					'last_login' => date('Y-m-d h:i:s')
				);
				$this->common->update_data($data, 'users', 'id', $userinfo[0]['id']);
				
				// Add user data in session
				$this->session->set_userdata('mec_user', $userinfo[0]);
				
				$this->session->set_flashdata('success', $this->lang->line('msg_login_success'));
	            redirect('user/dashboard');
			}
		} else {
			$this->session->set_flashdata('error', $this->lang->line('error_msg_login'));
			redirect();
		}
    }
	
	public function fbauth() {
		$userData = array();
		
		// Check if user is logged in
		if ($this->facebook->is_authenticated()) {
			// Get user facebook profile details
			$userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture,location');
			
//            $userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
//            $userData['picture_url'] = $userProfile['picture']['data']['url'];

			// Set Language
			$lang_condition = array('lang_code' => 'en');
			$lang_info = $this->common->select_data_by_condition('languages', $lang_condition, 'lang_id, lang_code');
			
			$userData['lang_id'] = $lang_info[0]['lang_id'];
			$userData['language'] = $lang_info[0]['lang_code'];

			if ($userProfile['locale']) {
				$getLang = explode('_', $userProfile['locale']);
			
				$lang2_condition = array('lang_code' => $getLang[0]);
				$lang2_info = $this->common->select_data_by_condition('languages', $lang2_condition, 'lang_id, lang_code');
				
				if (count($lang2_info) > 0) {
					$userData['lang_id'] = $lang2_info[0]['lang_id'];
					$userData['language'] = $lang2_info[0]['lang_code'];
				}
			}
			
			// IF ALREADY EXISTS
			$condition_array = array('deleted' => 0);
            $check_result = $this->common->check_unique_avalibility('users', 'fbid', $userProfile['id'], '', '', $condition_array);

            if ($check_result == 1) {
				// CHECK IF USER BLOCKED
				$join_str = array(
					array(
						'table' => 'languages',
						'join_table_id' => 'languages.lang_id',
						'from_table_id' => 'users.lang_id',
						'join_type' => 'left'
					)
				);
				
				$contition_user = array('fbid' => $userProfile['id']);
				$user_result = $this->common->select_data_by_condition('users', $contition_user, $data = 'id, name, email, password, country, users.lang_id, languages.lang_code as language, photo, fbid, status', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str);
				
				if ($user_result[0]['status'] == "0") {
					$this->session->set_flashdata('error', $this->lang->line('error_account_blocked'));
					redirect();
				}
				
				// UPDATE LAST LOGIN
				$userData['last_login'] = date('Y-m-d h:i:s');				
				$this->common->update_data($userData, 'users', 'id', $user_result[0]['id']);
				
				// SET SESSION DATA
				$user_result[0]['social_login'] = TRUE;
				$user_result[0]['logout_url'] = $this->facebook->logout_url();
				$this->session->set_userdata('mec_user', $user_result[0]);
				
				$this->session->set_flashdata('success', $this->lang->line('msg_login_success'));
	            redirect('user/dashboard');
			} else {
				// NEW SIGNUP
				$userData['name'] = $userProfile['first_name'];
				
				if ($userProfile['last_name'] != '') {
					$userData['name'] .= " ".$userProfile['last_name'];
				}
				
				$userData['email'] = $userProfile['email'];
				
				if ($userProfile['gender']) {
					$userData['gender'] = ucfirst($userProfile['gender']);
				}
				
				$userData['fbid'] = trim($userProfile['id']);
				$userData['country'] = 'JO';
				$userData['status'] = 1;
				$userData['create_date'] = date('Y-m-d h:i:s');
				$userData['last_login'] = date('Y-m-d h:i:s');
	
				$insert_result = $this->common->insert_data_getid($userData, $tablename = 'users');
				
				// USER NOTIFICATIONS PREFERENCES
				$insert_pref_1['user_id'] = $insert_result;
				$insert_pref_1['notification_id'] = 1;
				$insert_pref_1['status'] = 'on';
				$insert_pref_1['updated_on'] = date('Y-m-d h:i:s');
				
				$pref_result_1 = $this->common->insert_data($insert_pref_1, $tablename = 'user_preferences');
				
				$insert_pref_2['user_id'] = $insert_result;
				$insert_pref_2['notification_id'] = 2;
				$insert_pref_2['status'] = 'on';
				$insert_pref_2['updated_on'] = date('Y-m-d h:i:s');
				
				$pref_result_2 = $this->common->insert_data($insert_pref_2, $tablename = 'user_preferences');
				
				$insert_pref_3['user_id'] = $insert_result;
				$insert_pref_3['notification_id'] = 3;
				$insert_pref_3['status'] = 'on';
				$insert_pref_3['updated_on'] = date('Y-m-d h:i:s');
				
				$pref_result_3 = $this->common->insert_data($insert_pref_3, $tablename = 'user_preferences');
				
				$insert_pref_4['user_id'] = $insert_result;
				$insert_pref_4['notification_id'] = 4;
				$insert_pref_4['status'] = 'on';
				$insert_pref_4['updated_on'] = date('Y-m-d h:i:s');
				
				$pref_result_4 = $this->common->insert_data($insert_pref_4, $tablename = 'user_preferences');
				
				// SET SESSION DATA
				$userData['id'] = $insert_result;
				$userData['social_login'] = TRUE;
				$userData['logout_url'] = $this->facebook->logout_url();
				$this->session->set_userdata('mec_user', $userData);
				
				$this->session->set_flashdata('success', $this->lang->line('msg_login_success'));
	            redirect('user/dashboard');
			}
			
		} else {		
			$this->session->set_flashdata('error', $this->lang->line('error_msg_login'));
			redirect();
        }
    }
	
	public function twauth(){
		$userData = array();
		
		// Twitter API Configuration
//		$consumerKey = 'RLqrrrVbalqcyPkkDA1pBVXsS';
//		$consumerSecret = '7w2hPokRkNJg0X1BLOj8QnnRPHyoW8qL7zgGRokQIXNkmHMNZx';
		$consumerKey = 'sHUkqavFNxaGgr7RvZq94xxE0';
		$consumerSecret = '90MBNCqkFAdETUgAv7YkAeTrZ8dXjTBLWvHAwwgbsYptSzFB1A';
		
		//Get existing token and token secret from session
		$sessToken = $this->session->userdata('token');
		$sessTokenSecret = $this->session->userdata('token_secret');
		
		if (isset($_REQUEST['oauth_token']) && $sessToken == $_REQUEST['oauth_token']) {
			//Successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $sessToken, $sessTokenSecret); //print_r($connection);die;
			$accessToken = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			if ($connection->http_code == '200') {
				//Get user profile info
				$userInfo = $connection->get('account/verify_credentials');
				
				// IF ALREADY EXISTS
				$condition_array = array('deleted' => 0);
				$check_result = $this->common->check_unique_avalibility('users', 'twitterid', $userInfo->id, '', '', $condition_array);
				
				if ($check_result == 1) {
					// CHECK IF USER BLOCKED
					$contition_user = array('twitterid' => $userInfo->id);
					$user_result = $this->common->select_data_by_condition('users', $contition_user, $data = 'id, name, email, password, country, lang_id, photo, twitterid, status', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array());
					
					if ($user_result[0]['status'] == "0") {
						$this->session->set_flashdata('error', $this->lang->line('error_account_blocked'));
						redirect();
					}
					
					// UPDATE LAST LOGIN
					$userData['last_login'] = date('Y-m-d h:i:s');
					$this->common->update_data($userData, 'users', 'id', $user_result[0]['id']);
					
					// SET SESSION DATA
					$user_result[0]['social_login'] = TRUE;
					$this->session->set_userdata('mec_user', $user_result[0]);
					
					$this->session->set_flashdata('success', $this->lang->line('msg_login_success'));
	            redirect('user/dashboard');
				} else {
					// NEW SIGNUP
					$userData['name'] = $userInfo->name;
//					$userData['lang_id'] = $userInfo->lang; // It gives en
					$userData['lang_id'] = 1;
					$userData['twitterid'] = trim($userInfo->id);
					$userData['country'] = 'JO';
					$userData['status'] = 1;
					$userData['create_date'] = date('Y-m-d h:i:s');
					$userData['last_login'] = date('Y-m-d h:i:s');
					
					$insert_result = $this->common->insert_data_getid($userData, $tablename = 'users');
					
					// USER NOTIFICATIONS PREFERENCES
					$insert_pref_1['user_id'] = $insert_result;
					$insert_pref_1['notification_id'] = 1;
					$insert_pref_1['status'] = 'on';
					$insert_pref_1['updated_on'] = date('Y-m-d h:i:s');
					
					$pref_result_1 = $this->common->insert_data($insert_pref_1, $tablename = 'user_preferences');
					
					$insert_pref_2['user_id'] = $insert_result;
					$insert_pref_2['notification_id'] = 2;
					$insert_pref_2['status'] = 'on';
					$insert_pref_2['updated_on'] = date('Y-m-d h:i:s');
					
					$pref_result_2 = $this->common->insert_data($insert_pref_2, $tablename = 'user_preferences');
					
					$insert_pref_3['user_id'] = $insert_result;
					$insert_pref_3['notification_id'] = 3;
					$insert_pref_3['status'] = 'on';
					$insert_pref_3['updated_on'] = date('Y-m-d h:i:s');
					
					$pref_result_3 = $this->common->insert_data($insert_pref_3, $tablename = 'user_preferences');
					
					$insert_pref_4['user_id'] = $insert_result;
					$insert_pref_4['notification_id'] = 4;
					$insert_pref_4['status'] = 'on';
					$insert_pref_4['updated_on'] = date('Y-m-d h:i:s');
					
					$pref_result_4 = $this->common->insert_data($insert_pref_4, $tablename = 'user_preferences');
					
					// SET SESSION DATA
					$userData['id'] = $insert_result;
					$userData['social_login'] = TRUE;
					$userData['logout_url'] = $this->facebook->logout_url();
					$this->session->set_userdata('mec_user', $userData);
					
					//Get latest tweets
					//$data['tweets'] = $connection->get('statuses/user_timeline', array('screen_name' => $userInfo->screen_name, 'count' => 5));
					
					$this->session->set_flashdata('success', $this->lang->line('msg_login_success'));
					redirect('user/dashboard');
				}
			} else {
				$this->session->set_flashdata('error', $this->lang->line('error_msg_login'));
				redirect();
			}
		} else {
			$this->session->set_flashdata('error', $this->lang->line('error_something_wrong'));
			redirect();
        }
    }

	public function fblogout() {
		$this->session->unset_userdata('token');
		$this->session->unset_userdata('token_secret');
		$this->session->unset_userdata('status');
		$this->session->unset_userdata('userData');
        $this->session->sess_destroy();
		redirect();
    }
}

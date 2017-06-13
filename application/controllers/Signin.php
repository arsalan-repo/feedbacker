<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends CI_Controller {
	
	public $data;

    public function __construct() {
        parent::__construct();

        if ($this->session->userdata('mec_user')) {
            redirect('dashboard');
        }

        $this->data['title'] = "Login | Feedbacker ";
		
		// Load facebook library
		$this->load->library('facebook');
		
		// Include the twitter oauth php libraries
		include_once APPPATH."libraries/twitter-oauth-php/twitteroauth.php";

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
		// Get Facebook Login URL
		$this->data['authUrl'] =  $this->facebook->login_url();
		/*
		// Twitter API Configuration
		$consumerKey = 'Insert_Twitter_API_Key';
		$consumerSecret = 'Insert_Twitter_API_Secret';
		$oauthCallback = base_url().'user_authentication/';
		
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
		*/
		$this->data['oauthURL'] = '';
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
	            redirect('dashboard');
			}
		} else {
			$this->session->set_flashdata('error', $this->lang->line('error_msg_login'));
			redirect();
		}
    }
	
	public function facebook() {
		$userData = array();
		
		// Check if user is logged in
		if ($this->facebook->is_authenticated()) {
			// Get user facebook profile details
			$userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture');

            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid'] = $userProfile['id'];
            $userData['first_name'] = $userProfile['first_name'];
            $userData['last_name'] = $userProfile['last_name'];
            $userData['email'] = $userProfile['email'];
            $userData['gender'] = $userProfile['gender'];
            $userData['locale'] = $userProfile['locale'];
            $userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
            $userData['picture_url'] = $userProfile['picture']['data']['url'];
			
            // Insert or update user data
            $userID = $this->user->checkUser($userData);
			
			// Check user data insert or update status
            if (!empty($userID)) {
                $data['userData'] = $userData;
                $this->session->set_userdata('userData',$userData);
            } else {
               $data['userData'] = array();
            }
			
			// Get logout URL
			$data['logoutUrl'] = $this->facebook->logout_url();
		}else{
            $fbuser = '';
			
			// Get login URL
            $data['authUrl'] =  $this->facebook->login_url();
        }
		
		// Load login & profile view
        $this->load->view('user_authentication/index', $data);
    }
	
	public function twitter(){
		$userData = array();
		
		//Get existing token and token secret from session
		$sessToken = $this->session->userdata('token');
		$sessTokenSecret = $this->session->userdata('token_secret');
		
		//Get status and user info from session
		$sessStatus = $this->session->userdata('status');
		$sessUserData = $this->session->userdata('userData');
		
		if (isset($sessStatus) && $sessStatus == 'verified') {
			//Connect and get latest tweets
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $sessUserData['accessToken']['oauth_token'], $sessUserData['accessToken']['oauth_token_secret']); 
			$data['tweets'] = $connection->get('statuses/user_timeline', array('screen_name' => $sessUserData['username'], 'count' => 5));

			//User info from session
			$userData = $sessUserData;
		} elseif (isset($_REQUEST['oauth_token']) && $sessToken == $_REQUEST['oauth_token']) {
			//Successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $sessToken, $sessTokenSecret); //print_r($connection);die;
			$accessToken = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			if ($connection->http_code == '200') {
				//Get user profile info
				$userInfo = $connection->get('account/verify_credentials');

				//Preparing data for database insertion
				$name = explode(" ",$userInfo->name);
				$first_name = isset($name[0])?$name[0]:'';
				$last_name = isset($name[1])?$name[1]:'';
				$userData = array(
					'oauth_provider' => 'twitter',
					'oauth_uid' => $userInfo->id,
					'username' => $userInfo->screen_name,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'locale' => $userInfo->lang,
					'profile_url' => 'https://twitter.com/'.$userInfo->screen_name,
					'picture_url' => $userInfo->profile_image_url
				);
				
				//Insert or update user data
				$userID = $this->user->checkUser($userData);
				
				//Store status and user profile info into session
				$userData['accessToken'] = $accessToken;
				$this->session->set_userdata('status','verified');
				$this->session->set_userdata('userData',$userData);
				
				//Get latest tweets
				$data['tweets'] = $connection->get('statuses/user_timeline', array('screen_name' => $userInfo->screen_name, 'count' => 5));
			} else {
				$data['error_msg'] = 'Some problem occurred, please try again later!';
			}
		} else {
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
				$data['oauthURL'] = $twitterUrl;
			} else {
				$data['oauthURL'] = base_url().'twitter';
				$data['error_msg'] = 'Error connecting to twitter! try again later!';
			}
        }

		$data['userData'] = $userData;
		$this->load->view('user_authentication/index',$data);
    }

	public function logout() {
		$this->session->unset_userdata('token');
		$this->session->unset_userdata('token_secret');
		$this->session->unset_userdata('status');
		$this->session->unset_userdata('userData');
        $this->session->sess_destroy();
		redirect('/user_authentication');
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {
	
	public $data;

    public function __construct() {
        parent::__construct();

        if ($this->session->userdata('mec_user')) {
            redirect('dashboard');
        }

        $this->data['title'] = "Sign Up | Feedbacker ";

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
		$this->load->view('signup');
	}
	
	public function submit() {
        $name = $this->input->post('name');
		$email = $this->input->post('email');
        $password = $this->input->post('password');
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		
		if ($this->form_validation->run() == FALSE) {
			redirect();
		}
		
		//Check User Is valid or not
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
}

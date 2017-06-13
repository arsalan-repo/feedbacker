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
		$this->load->view('user/signup');
	}
	
	public function submit() {
        $name = $this->input->post('name');
		$email = $this->input->post('email');
        $password = $this->input->post('password');
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
		
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('signup');
		}
		
		// Add User in database
		$md5_password = md5($password);

		$insert_array['name'] = $name;
		$insert_array['email'] = trim($email);
		$insert_array['password'] = trim($md5_password);
		//$insert_array['country'] = $country;
		$insert_array['status'] = 1;
		$insert_array['create_date'] = date('Y-m-d h:i:s');
		$insert_array['modify_date'] = date('Y-m-d h:i:s');

		$insert_result = $this->common->insert_data_getid($insert_array, $tablename = 'users');
		
		if (!$insert_result) {
			$this->session->set_flashdata('error', $this->lang->line('error_something_wrong'));
			redirect('signup');
		}
		
		// Add User Notifications Preferences
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
		
		// Add user data in session
		$user_info = array(
			'id'	=>	$insert_result,
			'name'	=>	$name,
			'email' =>	$email,
			'user_avatar'	=> ASSETS_URL . 'images/user-avatar.png',
			//'country'		=> $user_result[0]['country'],
		);
		
		/*if($language != '') {
			$return_array['language'] = $language;
		} else {
			$return_array['language'] = 'en';
		}*/
		
		$this->session->set_userdata('mec_user', $user_info);
		
		$this->session->set_flashdata('success', $this->lang->line('success_msg_sinup_done'));
		redirect('dashboard');
    }
}

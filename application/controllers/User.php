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
		
		$this->data['module_name'] = 'User';
        $this->data['section_title'] = 'Profile';
		
		/* Load Template */
		$this->template->front_render('user/profile', $this->data);
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

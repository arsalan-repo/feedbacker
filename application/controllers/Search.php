<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
		$json = [];

		$this->load->database();

		if(!empty($this->input->get("q"))){
			$this->db->like('title', $this->input->get("q"));
			$query = $this->db->select('title_id as id,title as text')
						->limit(10)
						->get("titles");
			$json = $query->result();
		}
		
		echo json_encode($json);
	}
}

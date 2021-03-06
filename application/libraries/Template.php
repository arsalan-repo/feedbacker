<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template {

    protected $CI;

    public function __construct()
    {	
		$this->CI =& get_instance();
    }

    public function front_render($content, $data = NULL)
    {
        if ( ! $content)
        {
            return NULL;
        }
        else
        {
            $this->template['header']          = $this->CI->load->view('_templates/header', $data, TRUE);
            $this->template['main_header']     = $this->CI->load->view('_templates/main_header', $data, TRUE);
            $this->template['content']         = $this->CI->load->view($content, $data, TRUE);
            $this->template['footer']          = $this->CI->load->view('_templates/footer', $data, TRUE);

            return $this->CI->load->view('_templates/template', $this->template);
        }
	}

}

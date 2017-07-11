<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>Welcome to Feedbacker</title>
	<link href="<?php echo base_url().'assets/css/font-awesome.min.css'; ?>" rel="stylesheet" type="text/css" />
	<!-- Autocomplete Dropdown -->
	<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet" />
	<!-- Country Flags -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/dd.css'; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/sprite.css'; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/flags.css'; ?>" />
	<?php 
	$user_info = $this->session->userdata['mec_user'];
	if ($user_info['lang_id'] == 1) { 
		$style = 'style.css';
		$responsive = 'responsive.css';
	} else {
		$style = 'style-rtl.css';
		$responsive = 'responsive-rtl.css';		
	}
	?>    
	<link href="<?php echo base_url().'assets/css/'.$style; ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url().'assets/css/'.$responsive; ?>" rel="stylesheet" type="text/css" />
	<!-- jQuery 1.12.1 -->
	<script src="<?php echo base_url().'assets/js/jquery-1.12.1.min.js';?>"></script>
	<script src="<?php echo base_url().'assets/js/jquery-ui.min.js';?>"></script>
	<!-- jQuery Validate -->
	<script src="<?php echo base_url().'assets/js/jquery.validate.min.js';?>"></script>
	<!-- jQuery Toastr -->
	<script src="<?php echo base_url().'assets/js/toastr.min.js';?>"></script>
	<link href="<?php echo base_url().'assets/css/toastr.min.css'; ?>" rel="stylesheet" />
	<!-- Autocomplete Dropdown -->
	<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
	<script src="<?php echo base_url().'assets/js/jquery.dd.js'; ?>"></script>
	<script src="<?php echo base_url().'assets/js/custom.js'; ?>"></script>
	<script type="application/javascript">
	$(document).ready(function(e) {		
		//no use
		try {
			var countries = $("#countries").msDropdown({on:{change:function(data, ui) {
				var url = $("#dashboard_url").val();
				var val = data.value;
				
				if(val != "")
					window.location = url+"/"+val;
			}}}).data("dd");
	
			var pagename = document.location.pathname.toString();
			pagename = pagename.split("/");
			pages.setIndexByValue(pagename[pagename.length-1]);
			$("#ver").html(msBeautify.version.msDropdown);
		} catch(e) {
			//console.log(e);	
		}
		
		$("#ver").html(msBeautify.version.msDropdown);
	
		//convert
		$("select").msDropdown({roundedBorder:false});
		$("#tech").data("dd");
	});
	</script>
	
</head>

<body>
	<div class="wrapper">

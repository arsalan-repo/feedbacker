<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container creatae-post-content">
      <h2><?php echo $this->lang->line('settings'); ?></h2>
      <ul class="tabs">
          <li class="tab-link current" data-tab="tab-1"><?php echo $this->lang->line('language'); ?></li>
          <li class="tab-link" data-tab="tab-2"><?php echo $this->lang->line('change_pass'); ?></li>
          <li class="tab-link" data-tab="tab-3"><?php echo $this->lang->line('contact_us'); ?></li>
          <li class="tab-link" data-tab="tab-4"><?php echo $this->lang->line('terms_cond'); ?></li>
      </ul>
      <div id="tab-1" class="tab-content language-tab current">
          <?php
			$attributes = array('id' => 'lang-form');
			echo form_open('user/set_language', $attributes); ?>
            <ul>
              <?php foreach($languages as $lang) { ?>
              <li>
				 <input type="radio" name="lang_id" id="<?php echo $lang['lang_code']; ?>" value="<?php echo $lang['lang_id']; ?>" class="css-checkbox" <?php if($user_info['lang_id'] == $lang['lang_id']) echo 'checked="checked"'; ?>/>
                 <label for="<?php echo $lang['lang_code']; ?>" class="css-label radGroup2"><?php echo $lang['lang_name']; ?></label>
              </li>
			  <?php } ?>
              <li>
			  	<input type="hidden" name="lang_code" id="lang_code" value="<?php echo $lang['lang_code']; ?>" />
                <input type="submit" name="btn_save" id="btn_save" value="<?php echo $this->lang->line('save'); ?>" />
              </li>
            </ul>
          <?php echo form_close(); ?>
      </div>
      <div id="tab-2" class="tab-content change-password-tab">
          <?php
			$attributes = array('id' => 'pass-form');
			echo form_open('user/change_password', $attributes); ?>
            <ul>
              <li>
                <label><?php echo $this->lang->line('old_pass'); ?></label>
                <input type="password" placeholder="" name="textfield" id="textfield" />
              </li>
              <li>
                <label><?php echo $this->lang->line('new_pass'); ?></label>
                <input type="text" placeholder="" name="textfield" id="textfield" />
              </li>
              <li>
                <label><?php echo $this->lang->line('confirm_pass'); ?></label>
                <input type="text" name="textfield" placeholder="" id="textfield" />
              </li>
              <li>
                <input type="submit" name="button" id="button" value="<?php echo $this->lang->line('save'); ?>" />
              </li>
              <li> </li>
            </ul>
          <?php echo form_close(); ?>
      </div>
      <div id="tab-3" class="tab-content contact-tab">
          <?php
			$attributes = array('id' => 'contact-form');
			echo form_open('user/contact_us', $attributes); ?>
            <ul>
              <li>
                <label><?php echo $this->lang->line('name'); ?></label>
                <input type="text" placeholder="" name="textfield" id="textfield" />
              </li>
              <li>
                <label><?php echo $this->lang->line('email'); ?></label>
                <input type="text" placeholder="" name="textfield" id="textfield" />
              </li>
              <li>
                <label><?php echo $this->lang->line('comment'); ?></label>
                <input type="text" name="textfield" placeholder="" id="textfield" />
              </li>
              <li>
                <input type="submit" name="button" id="button" value="<?php echo $this->lang->line('send'); ?>" />
              </li>
              <li> </li>
            </ul>
          <?php echo form_close(); ?>
      </div>
      <div id="tab-4" class="tab-content terms-tab">
          <?php echo $terms[0]['description']; ?>
      </div>
      
    </div>
  </div>
<!-- /.content-wrapper -->
<script type="text/javascript">
$(document).ready(function() {
	// Set Language
	$("#lang-form").submit(function(event) {	
		event.preventDefault();
		
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: this.action,
			data: $(this).serialize()
		}).done(function(data){
			if(data.status == 1) {
				toastr.success(data.message, 'Success Alert', {timeOut: 5000});
			} else {
				toastr.error(data.message, 'Failure Alert', {timeOut: 5000});
			}
		});
	});
});
</script>

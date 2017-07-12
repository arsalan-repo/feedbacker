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
          <form id="form1" name="form1" method="post" action="">
            <ul>
              <li>
              
				 <input type="radio" name="radiog_dark" id="radio4" class="css-checkbox" />
                 <label for="radio4" class="css-label radGroup2">English</label>
              </li>
              <li>
				 <input type="radio" name="radiog_dark" id="radio5" class="css-checkbox" checked="checked"/>
                 <label for="radio5" class="css-label radGroup2">Arabic</label>
              </li>
              <li>
                <input type="submit" name="button" id="button" value="<?php echo $this->lang->line('save'); ?>" />
              </li>
            </ul>
          </form>
      </div>
      <div id="tab-2" class="tab-content change-password-tab">
          <form id="form1" name="form1" method="post" action="">
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
          </form>
      </div>
      <div id="tab-3" class="tab-content contact-tab">
          <form id="form1" name="form1" method="post" action="">
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
          </form>
      </div>
      <div id="tab-4" class="tab-content terms-tab">
          <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis in nulla eget faucibus. In lacinia ligula sed condimentum consectetur. Integer magna felis, varius consectetur nisl ac, aliquam feugiat ipsum. Vestibulum aliquam lectus ac eros accumsan, quis porttitor enim auctor. Quisque id porttitor enim. Donec sit amet metus malesuada, condimentum ipsum in, vulputate quam. Vestibulum sit amet nisi ac odio ornare lobortis quis non tellus. Nunc sit amet eros orci. Cras nec lectus in turpis sodales ultrices. </p>
          <p>Nam ac tempor dolor, eu accumsan odio. Sed non sem convallis, gravida lacus eu, sagittis neque. Praesent ut felis mattis, vehicula turpis ut, rutrum orci. Donec elementum ipsum at ligula eleifend, id vulputate nibh gravida. Sed laoreet iaculis elit, sit amet elementum urna. Fusce massa ex, facilisis dapibus arcu in, lacinia ultricies diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec mollis commodo felis id placerat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut sit amet enim consequat, rutrum magna et, semper nunc. </p>
      </div>
      
    </div>
  </div>
<!-- /.content-wrapper -->

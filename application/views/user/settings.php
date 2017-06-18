<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container creatae-post-content">
      <h2>Settings</h2>
      <ul class="tabs">
          <li class="tab-link current" data-tab="tab-1">Language</li>
          <li class="tab-link" data-tab="tab-2">Change Password</li>
          <li class="tab-link" data-tab="tab-3">Contact Us</li>
          <li class="tab-link" data-tab="tab-4">Terms and Conditions</li>
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
                <input type="submit" name="button" id="button" value="Save" />
              </li>
            </ul>
          </form>
      </div>
      <div id="tab-2" class="tab-content change-password-tab">
          <form id="form1" name="form1" method="post" action="">
            <ul>
              <li>
                <label>Old Password</label>
                <input type="password" placeholder="**********" name="textfield" id="textfield" />
              </li>
              <li>
                <label>New Password</label>
                <input type="text" placeholder="**********" name="textfield" id="textfield" />
              </li>
              <li>
                <label>Confirm New Password</label>
                <input type="text" name="textfield" placeholder="**********" id="textfield" />
              </li>
              <li>
                <input type="submit" name="button" id="button" value="Save" />
              </li>
              <li> </li>
            </ul>
          </form>
      </div>
      <div id="tab-3" class="tab-content contact-tab">
          <form id="form1" name="form1" method="post" action="">
            <ul>
              <li>
                <label>Name</label>
                <input type="text" placeholder="John Doe" name="textfield" id="textfield" />
              </li>
              <li>
                <label>Email</label>
                <input type="text" placeholder="johndoe@gmail.com" name="textfield" id="textfield" />
              </li>
              <li>
                <label>Comment</label>
                <input type="text" name="textfield" placeholder="I think #boeing should step a head of a few changes n their new planes." id="textfield" />
              </li>
              <li>
                <input type="submit" name="button" id="button" value="Send" />
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container creatae-post-content">
      <h2>Create Post</h2>
      <form id="form1" name="form1" method="post" action="">
        <label>Write About?</label>
        <input type="text" name="textfield1" placeholder="Boeing 787" />
        <label>Location</label>
        <input type="text" name="textfield2" placeholder="5595 fincher road" />
        <label>Your Feedback</label>
        <input type="text" name="textfield2" placeholder="I think #boeing should step a head of a few changes n their new planes." />
      </form>
      <div class="post-btn-block">
        <div class="camera-map-icon"> <img src="<?php echo base_url().'assets/images/camera-icon.png'; ?>" alt="" /> <img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" alt="" /> </div>
        <span class="post-btn">Post</span> </div>
    </div>
</div>
<!-- /.content-wrapper -->

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion">
    <div class="container">
      <div class="left-content">
        <div class="home-left-profile">
          <div class="home-left-section">
            <div class="home-left-profile-block"> 
            	<span class="home-left-profile-thumb">
					<?php 
                    if(isset($user_info['photo'])) {
                        echo '<img src="'.S3_CDN . 'uploads/user/thumbs/' . $user_info['photo'].'" alt="" />';
                    } else {
                        echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
                    }
                    ?>
                </span> 
                <span class="home-left-profile-name"><?php echo $user_info['name']; ?></span> 
                <span class="home-left-profile-designation"><?php echo $this->common->getCountries($user_info['country']); ?></span> </div>
          </div>
        </div>
        <div class="home-left-text-block">
          <h2><span>Trends</span><!-- Change--></h2>
          <?php foreach($trends as $row) {
              echo '<h3><a href="'.site_url('post/title').'/'.$row['title_id'].'">'.$row['title'].'</a></h3>';
              echo '<p>'.$this->common->limitText($row['feedback_cont'], 20).'</p>';
          } ?>
        </div>
      </div>
      <div class="middle-content">
        <div class="middle-content-block">
          <?php if (!empty($feedbacks)) { ?>
          <!-- Loop Starts Here -->
          <?php foreach($feedbacks as $row) { ?>
          <div class="post-profile-block">
		   <div class="post-right-arrow">
		   <!--<i class="fa fa-angle-down" aria-hidden="true"></i>-->
		   <span class="post-followers-text"><?php echo $row['followers']; ?> Followers</span>
		   <span class="post-profile-time-text"><?php echo $row['time']; ?></span>
		   </div>
            <div class="post-img">
            	<?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </div>
            <div class="post-profile-content"> 
            	<span class="post-designation">
                	<a href="<?php echo site_url('post/detail').'/'.$row['id']; ?>"><?php echo $row['title']; ?></a>
                </span> 
            	<span class="post-name"><?php echo $row['name']; ?></span> 
            	<span class="post-address"><?php echo $row['location']; ?></span>
                <p><?php echo $row['feedback']; ?></p>
                <?php if (!empty($feedback_img)) { ?>
              	<div class="post-large-img"> 
                	<img src="<?php echo $feedback_img; ?>" alt="" />
              	</div>
                <?php } ?>
              	<div class="post-follow-block"> 
                	<span class="post-follow-back-arrow">
                    	<img src="<?php echo ASSETS_URL.'images/reply-arrow.png'; ?>" alt="" />
                    </span>
                    <span class="follow-btn-default">
                    	<?php if ($row['is_followed']) { ?>
                        	Unfollow
                        <?php } else { ?>    
                            Follow <i class="fa fa-plus" aria-hidden="true"></i>
                        <?php } ?>
                    </span>
                    <span class="post-wishlist">
                    	<i class="fa fa-heart" aria-hidden="true" <?php $row['is_liked'] ?  'style="color: #f32836;"' : '' ?>></i> 
						<?php echo $row['likes']; ?>
                    </span>
                </div>
				<div class="post-detail-comment-form">
              <h2>Write a comment</h2>
              <form id="form1" name="form1" method="post" action="">
                <label>Comment</label>
                <input type="text" name="textfield1" placeholder="Write comment here" />
              </form>
              <div class="post-btn-block">
                <div class="camera-map-icon"> 
				<div class="camera-icon-block">
					<span>Choose File</span>
					<input name="Select File" type="file" />
				</div>
				<?php /*?><img src="<?php echo base_url().'assets/images/camera-icon.png'; ?>" alt="" /> <?php */?>
				
				<img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" alt="" /> </div>
                <span class="post-btn">Post</span> </div>
            </div>
            </div>
          </div>
          <?php } ?>
          <!-- Loop Ends Here -->
          <?php } else { ?>
          <?php echo $no_record_found; ?>
          <?php } ?>
        </div>
      </div>
      <div class="right-content">
        <h3>What to Follow <!--<a href="#">View All</a>--></h3>
        <?php foreach($to_follow as $row) { ?>
        <div class="who-follow-block">
        	<span>
            	<?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </span>
            <div class="who-follow-text">
            	<span><?php echo $row['title']; ?></span> <?php echo $row['name']; ?>
            </div>
            <div class="who-follow-add"> Follow <i class="fa fa-plus" aria-hidden="true"></i></div>
        </div>
        <?php } ?>
      </div>
    </div>
</div>
<!-- /.content-wrapper -->

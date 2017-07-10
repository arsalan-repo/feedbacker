<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php foreach($feedbacks as $row) { ?>
  <div class="post-profile-block">
   <div class="post-right-arrow">
   <!--<i class="fa fa-angle-down" aria-hidden="true"></i>-->
   <?php if(isset($row['ads'])) { ?>
    <span class="post-profile-time-text">Promoted</span>
   <?php } else { ?>
    <span class="post-followers-text"><?php echo $row['followers']; ?> Followers</span>
    <span class="post-profile-time-text"><?php echo $row['time']; ?></span>            
   <?php } ?>
   </div>
    <div class="post-img">
        <?php
            if(isset($row['ads'])) {
                echo '<a href="'.$row['ads_url'].'" target="_blank">';
            } else {
                echo '<a href="'.site_url('post/detail').'/'.$row['id'].'">';	
            }
        ?>
        <?php
        if(isset($row['user_avatar'])) {
            echo '<img src="'.$row['user_avatar'].'" alt="" />';
        } else {
            echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
        }
        ?>
        </a>
    </div>
    <div class="post-profile-content"> 
        <span class="post-designation">
            <a href="<?php echo site_url('post/title').'/'.$row['title_id']; ?>"><?php echo $row['title']; ?></a>
        </span> 
        <span class="post-name"><?php echo $row['name']; ?></span> 
        <span class="post-address"><?php echo $row['location']; ?></span>
        <p><?php echo $row['feedback']; ?></p>
        <?php if (!empty($row['feedback_img'])) { ?>
            <div class="post-large-img">
            <?php if(isset($row['ads'])) { ?>
                <a href="<?php echo $row['ads_url']; ?>" target="_blank">
                    <img src="<?php echo $row['feedback_img']; ?>" alt="" />
                </a>
            <?php } else { ?>
                <img src="<?php echo $row['feedback_img']; ?>" alt="" />	
            <?php } ?>
            </div>
        <?php } ?>
        <?php if(!isset($row['ads'])) { ?>
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
                <i class="fa fa-heart-o" aria-hidden="true" <?php $row['is_liked'] ?  'style="color: #f32836;"' : '' ?>></i> 
                <?php echo $row['likes']; ?>
            </span>
        </div>
        <?php } ?>
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

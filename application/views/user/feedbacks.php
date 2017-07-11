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
            <span class="follow-btn-default" id="follow-btn-<?php echo $row['id']; ?>">
                <?php if ($row['is_followed']) { ?>
                    Unfollow
                <?php } else { ?>    
                    Follow <i class="fa fa-plus" aria-hidden="true"></i>
                <?php } ?>
            </span>
            <span class="post-wishlist" id="post-wishlist-<?php echo $row['id']; ?>">
				<?php if ($row['is_liked']) { ?>
					<i class="fa fa-heart" aria-hidden="true"></i> 
				<?php } else { ?>
					<i class="fa fa-heart-o" aria-hidden="true"></i>
				<?php } ?> 
				<?php echo $row['likes']; ?>
            </span>
			<input type="hidden" id="feedback_id" value="<?php echo $row['id']; ?>" />
			<input type="hidden" id="totl_likes" value="<?php echo $row['likes']; ?>" />			
            <input type="hidden" id="title_id" value="<?php echo $row['title_id']; ?>" />
            <input type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
        </div>
        <?php } ?>
        <div class="post-detail-comment-form">
          <h2>Write a comment</h2>
          <form id="form-reply-post" name="form-reply-post" method="post" action="">
            <label>Comment</label>
            <input type="text" name="feedback_cont" id="feedback_cont" placeholder="Write comment here" />
            <input type="text" name="location" id="location" placeholder="Location" />
          </form>
          <div class="post-btn-block">
            <div class="camera-map-icon"> 
            <div class="camera-icon-block">
                <span>Choose File</span>
                <input name="Select File" type="file" />
            </div>            
            <img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" alt="" /> </div>
            <span class="post-btn">Post</span> </div>
    	</div>
    </div>
  </div>
<?php } ?>
<script type="application/javascript">
	$('.follow-btn-default').off("click").on("click",function(e){
		e.preventDefault();
		
		var element = $(this).attr('id');
		var title_id = $(this).parent().find('#title_id').val();
		var user_id = $(this).parent().find('#user_id').val();		
	
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: '<?php echo site_url('title/follow'); ?>',
			data:{title_id:title_id, user_id:user_id}
		}).done(function(data){
			// console.log(data);
			if (data.is_followed == 1) {
				$('#'+element).html('Unfollow');
				toastr.success(data.message, 'Success Alert', {timeOut: 5000});
			}
			else
			{
				$('#'+element).html('Follow <i class="fa fa-plus" aria-hidden="true"></i>');
				toastr.warning(data.message, 'Success Alert', {timeOut: 5000});
			}
		});
	});
	
	$('.post-wishlist').off("click").on("click",function(e){
		e.preventDefault();
		
		var element = $(this).attr('id');
		var totl_likes = $(this).parent().find('#totl_likes').val();
		var feedback_id = $(this).parent().find('#feedback_id').val();
		var user_id = $(this).parent().find('#user_id').val();		
	
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: '<?php echo site_url('post/like'); ?>',
			data:{feedback_id:feedback_id, user_id:user_id, totl_likes:totl_likes}
		}).done(function(data){
			// console.log(data);
			if (data.is_liked == 1) {
				var totl = parseInt(data.likes) + 1;	
				$('#'+element).parent().find('#totl_likes').val(totl);
							
				$('#'+element).html('<i class="fa fa-heart" aria-hidden="true"></i><span class="total-likes"> '+totl+'</span>');
				toastr.success(data.message, 'Success Alert', {timeOut: 5000});
			}
			else
			{
				var totl = parseInt(data.likes) - 1;
				$('#'+element).parent().find('#totl_likes').val(totl);
				
				$('#'+element).html('<i class="fa fa-heart-o" aria-hidden="true"></i><span class="total-likes"> '+totl+'</span>');
				toastr.warning(data.message, 'Success Alert', {timeOut: 5000});
			}
		});
	});
</script>
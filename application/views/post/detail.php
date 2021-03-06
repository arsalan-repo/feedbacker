<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-secion post-details-wrapper">
    <div class="container">
      <div class="post-detail-left">
        <div class="profile-listing">
          <div class="listing-post-name-block">
          	<span class="listing-post-name"><a href="<?php echo site_url('post/title').'/'.$feedback['title_id']; ?>"><?php echo $feedback['title']; ?></a></span> 
            <span class="listing-post-followers">
				<?php 
				if($user_info['language'] == 'ar') {
					echo $this->lang->line('followers')." ".$feedback['followers'];
				} else {
					echo $feedback['followers']." ".$this->lang->line('followers');
				} ?>
			</span> 
		  </div>
          <div class="profile-listing-img-thumb-block">
            <div class="profile-listing-img-thumb">
            	<?php
				if(isset($feedback['user_avatar'])) {
					echo '<img src="'.$feedback['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
            </div>
            <span class="listing-post-profile-name"><?php echo $feedback['name']; ?></span> <span class="listing-post-profile-time"><?php echo $feedback['time']; ?></span> </div>
			<p><?php echo nl2br($feedback['feedback']); ?></p>
            <div class="listing-post-img">
				<?php 
				if (!empty($feedback['feedback_video'])) {
					echo '<video width="500" height="375" controls poster="'.$feedback['feedback_thumb'].'">
						<source src="'.$feedback['feedback_video'].'" type="video/mp4">
						Your browser does not support the video tag.
						</video>';
				} else {
					echo '<img src="'.$feedback['feedback_img'].'" alt="" />';
				} ?>
            </div>
          <div class="post-listing-follow-btn">
		  	<span class="post-follow-back-arrow" id="scrollToBottom">
                <img src="<?php echo ASSETS_URL.'images/reply-arrow.png'; ?>" alt="" title="<?php echo $this->lang->line('reply'); ?>" />
            </span> 
            <span class="follow-btn-default <?php if($feedback['is_followed']) echo 'unfollow-btn'; ?> follow-btn-<?php echo $feedback['title_id']; ?>">
                <?php if ($feedback['is_followed']) { ?>
                    <?php echo $this->lang->line('unfollow'); ?>
                <?php } else { ?>    
                    <?php echo $this->lang->line('follow'); ?> <i class="fa fa-plus" aria-hidden="true"></i>
                <?php } ?>
            </span>
            <span class="wishlist" id="post-wishlist-<?php echo $feedback['id']; ?>">
				<?php if ($feedback['is_liked']) { ?>
					<i class="fa fa-heart" aria-hidden="true" title="<?php echo $this->lang->line('unlike'); ?>"></i> 
				<?php } else { ?>
					<i class="fa fa-heart-o" aria-hidden="true" title="<?php echo $this->lang->line('like'); ?>"></i>
				<?php } ?> 
				<?php echo $feedback['likes']; ?>
            </span>
			<input type="hidden" id="feedback_id" value="<?php echo $feedback['id']; ?>" />
			<input type="hidden" id="totl_likes" value="<?php echo $feedback['likes']; ?>" />			
            <input type="hidden" id="title_id" value="<?php echo $feedback['title_id']; ?>" />
          </div>
          <div class="post-detail-comments-block">
          	<?php if(!empty($feedback['replies'])) { ?>
            <h3><?php echo $this->lang->line('comments'); ?></h3>
            <?php foreach($feedback['replies'] as $row) { ?>
            <div class="profile-listing-img-thumb-block">
              <div class="profile-listing-img-thumb">
              	<?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
              </div>
              <span class="listing-post-profile-name"><?php echo $row['name']; ?></span> 
			  <span class="post-address"><?php echo $row['location']; ?></span>
			  <span class="listing-post-profile-time"><?php echo $row['time']; ?></span> </div>
            <div class="comment-description">
              <p><?php echo nl2br($row['feedback']); ?></p>
			  <?php if (!empty($row['feedback_thumb'])) { ?>
				<div class="post-reply-img">
					<img src="<?php echo $row['feedback_thumb']; ?>" alt="" />	
				</div>
			  <?php } ?>
            </div>
            <?php } ?>
            <?php } ?>
            <div class="post-detail-comment-form">
              <h2><?php echo $this->lang->line('write_comment'); ?></h2>
              <?php
				$attributes = array('id' => 'reply-post-form', 'enctype' => 'multipart/form-data');
				echo form_open_multipart('post/reply', $attributes);
				?>
                <label><?php echo $this->lang->line('comment'); ?></label>
				<textarea name="feedback_cont" id="feedback_cont" placeholder="<?php echo $this->lang->line('comment_here'); ?>" rows="10"></textarea>
				<input type="text" name="location" id="location" placeholder="<?php echo $this->lang->line('location'); ?>" />
              
				<div class="post-btn-block">
					<div class="camera-map-icon">
						<div class="camera-icon-block">
							<span>Choose File</span>
							<input name="feedback_img" id="feedback_img" type="file" />
						</div>
						<img src="<?php echo base_url().'assets/images/map-icon.png'; ?>" class="geo-map" alt="" />
					</div>
					<span class="post-btn"><?php echo $this->lang->line('post'); ?></span>
				</div>
				<input type="hidden" name="id" id="id" value="<?php echo $feedback['id']; ?>" />
				<input type="hidden" name="latitude" id="latitude" value="" />
				<input type="hidden" name="longitude" id="longitude" value="" />
				<?php echo form_close(); ?>
				<img id="preview" src="" alt="" height="200" width="200" />
            </div>
          </div>
        </div>
      </div>
      <div class="post-detail-rgt">
      <?php if(count($others) > 0) { ?>
		  <?php foreach($others as $row) { ?>
            <div class="profile-listing">
              <div class="listing-post-name-block"> <span class="listing-post-name"><a href="<?php echo site_url('post/title').'/'.$row['title_id']; ?>"><?php echo $row['title']; ?></a></span> 
				  <span class="listing-post-followers">
				  <?php 
					if($user_info['language'] == 'ar') {
						echo $this->lang->line('followers')." ".$feedback['followers'];
					} else {
						echo $feedback['followers']." ".$this->lang->line('followers');
					} ?>
				  </span> 
			  </div>
              <div class="profile-listing-img-thumb-block">
                <div class="profile-listing-img-thumb">
				<a href="<?php echo site_url('post/detail').'/'.$row['id']; ?>">
                <?php
				if(isset($row['user_avatar'])) {
					echo '<img src="'.$row['user_avatar'].'" alt="" />';
				} else {
					echo '<img src="'.ASSETS_URL . 'images/user-avatar.png" alt="" />';
				}
				?>
				</a>
                </div>
                <span class="listing-post-profile-name"><?php echo $row['name']; ?></span> <span class="listing-post-profile-time"><?php echo $row['time']; ?></span> </div>
			<?php if($row['feedback_img'] != "") { ?>
              <div class="listing-post-img">
              	<img src="<?php echo $row['feedback_img']; ?>" alt="" />
              </div>
            <?php } ?>
              <p><?php echo nl2br($row['feedback']); ?></p>
              <div class="post-listing-follow-btn"> 
				<span class="post-follow-back-arrow" id="reply-btn-<?php echo $row['id']; ?>">
					<a href="<?php echo site_url('post/detail').'/'.$row['id']; ?>"><img src="<?php echo ASSETS_URL.'images/reply-arrow.png'; ?>" alt="" title="<?php echo $this->lang->line('reply'); ?>" /></a>
				</span> 
				<span class="follow-btn-default <?php if($row['is_followed']) echo 'unfollow-btn'; ?> follow-btn-<?php echo $row['title_id']; ?>">
					<?php if ($row['is_followed']) { ?>
						<?php echo $this->lang->line('unfollow'); ?>
					<?php } else { ?>    
						<?php echo $this->lang->line('follow'); ?> <i class="fa fa-plus" aria-hidden="true"></i>
					<?php } ?>
				</span>
				<span class="wishlist" id="post-wishlist-<?php echo $row['id']; ?>">
					<?php if ($row['is_liked']) { ?>
						<i class="fa fa-heart" aria-hidden="true" title="<?php echo $this->lang->line('unlike'); ?>"></i> 
					<?php } else { ?>
						<i class="fa fa-heart-o" aria-hidden="true" title="<?php echo $this->lang->line('like'); ?>"></i>
					<?php } ?> 
					<?php echo $row['likes']; ?>
				</span>
				<input type="hidden" id="feedback_id" value="<?php echo $row['id']; ?>" />
				<input type="hidden" id="totl_likes" value="<?php echo $row['likes']; ?>" />			
				<input type="hidden" id="title_id" value="<?php echo $row['title_id']; ?>" />
			 </div>
            </div>
          <?php } ?>
      <?php } ?>
      </div>
    </div>
</div>
<!-- /.content-wrapper -->
<script type="application/javascript">
// When the browser is ready...
$(function() {
	// jQuery Toastr
	if ($.trim($(".div-toastr-error").html()).length > 0) {
		$(".div-toastr-error p").each(function( index ) {
			toastr.error($(this).html(), 'Failure Alert', {timeOut: 5000});
		});
	}
	
	$('#scrollToBottom').bind("click", function () {
		$('html, body').animate({ scrollTop: $(document).height() }, 1200);
		return false;
	});

	// Set Autocomplete Off
	$("#create-post-form").attr('autocomplete', 'off');
	
	$("#feedback_img").change(function(){
		imagePreview(this);
	});

	$(".geo-map").click(function() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showLocation);
		} else { 
			$('#location').html('Geolocation is not supported by this browser.');
		}
	});
	
	// Setup form validation on the #register-form element
	$(".post-btn").click(function() {
		$("#reply-post-form").submit();
	});
	
	$("#reply-post-form").validate({
		// Specify the validation rules
		rules: {
			feedback_cont: {
				required: true
			}
		},
		
		// Specify the validation error messages
		messages: {
			feedback_cont: "Please enter a feedback"
		},
		
		submitHandler: function(form) {
			form.submit();
		}
	});
	
});

function showLocation(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
	
    $.ajax({
        type:'POST',
        url:'<?php echo site_url('post/get_location'); ?>',
        data:'latitude='+latitude+'&longitude='+longitude,
        success:function(response){
            if(response){
				var objJSON = JSON.parse(response);
            	$('#location').val(objJSON.location);
				
				$( "#latitude" ).val( latitude );
				$( "#longitude" ).val( longitude );
            }else{
				toastr.error('Error getting location. Try later!', 'Failure Alert', {timeOut: 5000});
            }
        }
    });
}

function imagePreview(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#preview').attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

$('.follow-btn-default').off("click").on("click",function(e){
	e.preventDefault();
	
	var title_id = $(this).parent().find('#title_id').val();
	
	$.ajax({
		dataType: 'json',
		type:'POST',
		url: '<?php echo site_url('title/follow'); ?>',
		data:{title_id:title_id}
	}).done(function(data){
		// console.log(data);
		if (data.is_followed == 1) {
			$('.follow-btn-'+title_id).each(function() {
				$(this).addClass('unfollow-btn');
				$(this).html('<?php echo $this->lang->line('unfollow'); ?>');
			});
			toastr.success(data.message, 'Success Alert', {timeOut: 5000});
		}
		else
		{
			$('.follow-btn-'+title_id).each(function() {
				$(this).removeClass('unfollow-btn');
				$(this).html('<?php echo $this->lang->line('follow'); ?> <i class="fa fa-plus" aria-hidden="true"></i>');
			});
			toastr.warning(data.message, 'Success Alert', {timeOut: 5000});
		}
	});
});

$('.wishlist').off("click").on("click",function(e){
	e.preventDefault();
	
	var element = $(this).attr('id');
	var totl_likes = $(this).parent().find('#totl_likes').val();
	var feedback_id = $(this).parent().find('#feedback_id').val();

	$.ajax({
		dataType: 'json',
		type:'POST',
		url: '<?php echo site_url('post/like'); ?>',
		data:{feedback_id:feedback_id, totl_likes:totl_likes}
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

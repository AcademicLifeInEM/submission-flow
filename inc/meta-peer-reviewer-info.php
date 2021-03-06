<div id="peer_reviewer_1">
    <div id="SF_photo_1_holder" class="photo_holder">
        <input type="hidden" name="SF_photo_1_url" id="SF_photo_1_url" value="<?php echo( ( $PR_first_name_1 === '' ? '' : urldecode($SF_photo_1_url) ) ); ?>">
        <?php if ( $SF_photo_1_url === '' || $SF_photo_1_url === NULL || $PR_first_name_1 === '' ) {
            echo '<button class="button-secondary" type="button" id="SF_photo_1">Upload Photo</button>';
        } else {
            echo '<div id="SF_photo_1" class="inserted_headshot has_hover" style="background-image: url(\'' . urldecode( $SF_photo_1_url ) . '\')"><div></div></div>';
        }
        ?>
    </div>
    <label for="PR_first_name_1">First Name</label><br>
    <input type="text" id="PR_first_name_1" name="PR_first_name_1" class="large-text js-required" value="<?php echo(esc_attr( $PR_first_name_1 )); ?>" /><br>
    <label for="PR_last_name_1">Last Name</label><br>
    <input type="text" id="PR_last_name_1" name="PR_last_name_1" class="large-text  js-required" value="<?php echo(esc_attr( $PR_last_name_1 )); ?>" /><br>
    <label for="PR_email_1">Email Address</label><br>
    <input type="text" id="PR_email_1" name="PR_email_1" class="large-text  js-required" value="<?php echo(esc_attr( $PR_email_1 )); ?>" /><br>
    <label for="PR_twitter_handle_1">Twitter</label><br>
    <input type="text" name="PR_twitter_handle_1" id="PR_twitter_handle_1" class="large-text" value="<?php echo(esc_attr( $PR_twitter_handle_1 )); ?>">
    <label for="PR_credentials_1">Credentials</label><br>
    <textarea name="PR_credentials_1" style="margin-bottom: 10px;" placeholder="(title, institution, etc.)" rows="3" class="fancy-textarea large-text  js-required"><?php echo(esc_attr( $PR_credentials_1 )); ?></textarea><br>
</div>
<button type="button" class="button-secondary" name="toggle_second_reviewer" style="width: 100%;">Add Second Reviewer</button>
<div id="peer_reviewer_2" class="js-hide">
    <hr>
    <div id="SF_photo_2_holder" class="photo_holder">
        <input type="hidden" name="SF_photo_2_url" id="SF_photo_2_url" value="<?php echo( ( $PR_first_name_2 === '' ? '' : urldecode($SF_photo_2_url) ) ); ?>">
        <?php if ( $SF_photo_2_url === '' || $SF_photo_2_url === NULL || $PR_first_name_2 === '' ) {
            echo '<button class="button-secondary" type="button" id="SF_photo_2">Upload Photo</button>';
        } else {
            echo '<div id="SF_photo_2" class="inserted_headshot has_hover" style="background-image: url(\'' . urldecode( $SF_photo_2_url ) . '\')"><div></div></div>';
        }
        ?>
    </div>
    <label for="PR_first_name_2">First Name</label><br>
    <input type="text" id="PR_first_name_2" name="PR_first_name_2" class="large-text  js-required" value="<?php echo(esc_attr( $PR_first_name_2 )); ?>" /><br>
    <label for="PR_last_name_2">Last Name</label><br>
    <input type="text" id="PR_last_name_2" name="PR_last_name_2" class="large-text  js-required" value="<?php echo(esc_attr( $PR_last_name_2 )); ?>" /><br>
    <label for="PR_email_2">Email Address</label><br>
    <input type="text" id="PR_email_2" name="PR_email_2" class="large-text  js-required" value="<?php echo(esc_attr( $PR_email_2 )); ?>" /><br>
    <label for="PR_twitter_handle_2">Twitter</label><br>
    <input type="text" name="PR_twitter_handle_2" id="PR_twitter_handle_2" class="large-text" value="<?php echo(esc_attr( $PR_twitter_handle_1 )); ?>">
    <label for="PR_credentials_2">Credentials</label><br>
    <textarea name="PR_credentials_2" placeholder="(title, institution, etc.)" rows="3" class="fancy-textarea large-text  js-required"><?php echo(esc_attr( $PR_credentials_2 )); ?></textarea><br>
</div>

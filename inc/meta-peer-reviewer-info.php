<div id="peer_reviewer_1">
    <label for="PR_first_name_1">First Name</label><br>
    <input type="text" id="PR_first_name_1" name="PR_first_name_1" class="large-text js-required" value="<?php echo(esc_attr( $PR_first_name_1 )); ?>" /><br>
    <label for="PR_last_name_1">Last Name</label><br>
    <input type="text" id="PR_last_name_1" name="PR_last_name_1" class="large-text  js-required" value="<?php echo(esc_attr( $PR_last_name_1 )); ?>" /><br>
    <label for="PR_email_1">Email Address</label><br>
    <input type="text" id="PR_email_1" name="PR_email_1" class="large-text  js-required" value="<?php echo(esc_attr( $PR_email_1 )); ?>" /><br>
    <label for="PR_twitter_handle_1" id="email_label1">Twitter</label><br>
    <input type="text" name="PR_twitter_handle_1" id="PR_twitter_handle_1" class="large-text" value="<?php echo(esc_attr( $PR_twitter_handle_1 )); ?>">
    <label for="PR_background_info_1">Background Information</label><br>
    <textarea name="PR_background_info_1" class="large-text  js-required" style="resize: vertical;"><?php echo(esc_attr( $PR_background_info_1 )); ?></textarea><br>
</div>
<button type="button" name="toggle_second_reviewer" style="width: 100%;">Add Second Reviewer</button>
<div id="peer_reviewer_2" class="js-hide">
    <hr>
    <label for="PR_first_name_2">First Name</label><br>
    <input type="text" id="PR_first_name_2" name="PR_first_name_2" class="large-text  js-required" value="<?php echo(esc_attr( $PR_first_name_2 )); ?>" /><br>
    <label for="PR_last_name_2">Last Name</label><br>
    <input type="text" id="PR_last_name_2" name="PR_last_name_2" class="large-text  js-required" value="<?php echo(esc_attr( $PR_last_name_2 )); ?>" /><br>
    <label for="PR_email_2">Email Address</label><br>
    <input type="text" id="PR_email_2" name="PR_email_2" class="large-text  js-required" value="<?php echo(esc_attr( $PR_email_2 )); ?>" /><br>
    <label for="PR_twitter_handle_2" id="email_label2">Twitter</label><br>
    <input type="text" name="PR_twitter_handle_2" id="PR_twitter_handle_2" class="large-text" value="<?php echo(esc_attr( $PR_twitter_handle_1 )); ?>">
    <label for="PR_background_info_2">Background Information</label><br>
    <textarea name="PR_background_info_2" class="large-text  js-required" style="resize: vertical;"><?php echo(esc_attr( $PR_background_info_2 )); ?></textarea><br>
</div>

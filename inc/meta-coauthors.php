<table>
    <tr>
        <td id="coauthor_container">
            <div id="coauthor_1_div">
                <label for="coauthor_1_first_name">First Name</label>
                <input type="text" name="coauthor_1_first_name" id="coauthor_1_first_name" value="<?php echo($coauthor_1_first_name); ?>">
                <label for="coauthor_1_last_name">Last Name</label>
                <input type="text" name="coauthor_1_last_name" id="coauthor_1_last_name" value="<?php echo($coauthor_1_last_name); ?>">
                <label for="coauthor_1_email">Email</label>
                <input type="text" name="coauthor_1_email" id="coauthor_1_email" value="<?php echo($coauthor_1_email); ?>">
                <label for="coauthor_1_twitter">Twitter</label>
                <input type="text" name="coauthor_1_twitter" id="coauthor_1_twitter" size="15" value="<?php echo($coauthor_1_twitter); ?>">
                <label for="coauthor_1_credentials">Credentials</label>
                <textarea style="vertical-align: middle; padding: 3px 5px; resize: vertical;" placeholder="(title, institution, etc.)" name="coauthor_1_credentials" rows="1" cols="39"><?php echo( parse_html_chars( $coauthor_1_credentials ) ); ?></textarea>
            </div>
            <div id="coauthor_2_div" class="js-hide">
                <label for="coauthor_2_first_name">First Name</label>
                <input type="text" name="coauthor_2_first_name" id="coauthor_2_first_name" value="<?php echo($coauthor_2_first_name); ?>">
                <label for="coauthor_2_last_name">Last Name</label>
                <input type="text" name="coauthor_2_last_name" id="coauthor_2_last_name" value="<?php echo($coauthor_2_last_name); ?>">
                <label for="coauthor_2_email">Email</label>
                <input type="text" name="coauthor_2_email" id="coauthor_2_email" value="<?php echo($coauthor_2_email); ?>">
                <label for="coauthor_2_twitter">Twitter</label>
                <input type="text" name="coauthor_2_twitter" id="coauthor_2_twitter" size="15" value="<?php echo($coauthor_2_twitter); ?>">
                <label for="coauthor_2_credentials">Credentials</label>
                <textarea style="vertical-align: middle; padding: 3px 5px; resize: vertical;" placeholder="(title, institution, etc.)" name="coauthor_2_credentials" rows="1" cols="39"><?php echo( parse_html_chars( $coauthor_2_credentials ) ); ?></textarea>
            </div>
            <div id="coauthor_3_div" class="js-hide">
                <label for="coauthor_3_first_name">First Name</label>
                <input type="text" name="coauthor_3_first_name" id="coauthor_3_first_name" value="<?php echo($coauthor_3_first_name); ?>">
                <label for="coauthor_3_last_name">Last Name</label>
                <input type="text" name="coauthor_3_last_name" id="coauthor_3_last_name" value="<?php echo($coauthor_3_last_name); ?>">
                <label for="coauthor_3_email">Email</label>
                <input type="text" name="coauthor_3_email" id="coauthor_3_email" value="<?php echo($coauthor_3_email); ?>">
                <label for="coauthor_3_twitter">Twitter</label>
                <input type="text" name="coauthor_3_twitter" id="coauthor_3_twitter" size="15" value="<?php echo($coauthor_3_twitter); ?>">
                <label for="coauthor_3_credentials">Credentials</label>
                <textarea style="vertical-align: middle; padding: 3px 5px; resize: vertical;" placeholder="(title, institution, etc.)" name="coauthor_3_credentials" rows="1" cols="39"><?php echo( parse_html_chars( $coauthor_3_credentials ) ); ?></textarea>
            </div>
            <div id="coauthor_4_div" class="js-hide">
                <label for="coauthor_4_first_name">First Name</label>
                <input type="text" name="coauthor_4_first_name" id="coauthor_4_first_name" value="<?php echo($coauthor_4_first_name); ?>">
                <label for="coauthor_4_last_name">Last Name</label>
                <input type="text" name="coauthor_4_last_name" id="coauthor_4_last_name" value="<?php echo($coauthor_4_last_name); ?>">
                <label for="coauthor_4_email">Email</label>
                <input type="text" name="coauthor_4_email" id="coauthor_4_email" value="<?php echo($coauthor_4_email); ?>">
                <label for="coauthor_4_twitter">Twitter</label>
                <input type="text" name="coauthor_4_twitter" id="coauthor_4_twitter" size="15" value="<?php echo($coauthor_4_twitter); ?>">
                <label for="coauthor_4_credentials">Credentials</label>
                <textarea style="vertical-align: middle; padding: 3px 5px; resize: vertical;" placeholder="(title, institution, etc.)" name="coauthor_4_credentials" rows="1" cols="39"><?php echo( parse_html_chars( $coauthor_4_credentials ) ); ?></textarea>
            </div>
        </td>
        <td valign="bottom">
            <input type="button" class="button-secondary" name="add_coauthor" id="add_coauthor" value="Add Another"><br>
        </td>
    </tr>
</table>

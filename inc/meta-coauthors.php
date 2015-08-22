<div class="error js-hide" id="invalid-email-alert"><strong>Error:</strong> Please enter a valid email address.</div>

    <!-- BEGIN GLOBAL OUTSIDE CONTAINER -->
    <div id="coauthor_container" class="flex-container">

        <!-- BEGIN INSIDE LEFT TOPMOST CONTAINER -->
        <div class="flex-container-vertical flex-box" id="coauthor_details">

            <!-- BEGIN COAUTHOR ROW CONTAINER 1 -->
            <div id="coauthor_1_div" class="flex-container flex-box">

                <!-- Upload Photo BUTTON -->
                <div class="flex-container-vertical flex-box-buttons photo_container" id="photo_button_1">
                    <input type="hidden" id="SF_photo_3_url" name="SF_photo_3_url" value="<?php echo( ( $coauthor_1_first_name === '' ? '' : urldecode($SF_photo_3_url) ) ); ?>">
                    <?php if ( $SF_photo_3_url === '' || $SF_photo_3_url === NULL || $coauthor_1_first_name === '' ) {
                        echo '<button class="button-secondary" type="button" id="SF_photo_3">Upload Photo</button>';
                    } else {
                        echo '<div id="SF_photo_3" class="inserted_headshot has_hover" style="background-image: url(\'' . urldecode( $SF_photo_3_url ) . '\')"><div></div></div>';
                    }
                    ?>
                </div>

                <!-- INPUT FIELDS -->
                <div class="flex-container flex-box" id="input-container-1">
                    <div class="flex-container flex-box" id="first_fields_1">

                        <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal input-label">
                            <label for="coauthor_1_first_name">First&nbsp;Name</label>
                            <label for="coauthor_1_last_name">Last&nbsp;Name</label>
                        </div>

                        <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_1_first_name" class="js-required" id="coauthor_1_first_name" value="<?php echo($coauthor_1_first_name); ?>">
                            <input type="text" name="coauthor_1_last_name" class="js-required" id="coauthor_1_last_name" value="<?php echo($coauthor_1_last_name); ?>">
                        </div>

                    </div>

                    <div class="flex-container flex-box" id="second_fields_1">
                        <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal input-label">
                            <label for="coauthor_1_email">Email</label>
                            <label for="coauthor_1_twitter">Twitter</label>
                        </div>

                        <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_1_email" class="js-required" id="coauthor_1_email" value="<?php echo($coauthor_1_email); ?>">
                            <input type="text" name="coauthor_1_twitter" id="coauthor_1_twitter" value="<?php echo($coauthor_1_twitter); ?>">
                        </div>

                    </div>
                </div>
                <!-- CREDENTIALS AREA -->
                <div class="flex-container flex-box" id="credentials_column_1">
                    <div class="flex-container-vertical flex-box-minimal input-label">
                        <label for="coauthor_1_credentials" style="text-align: center;">Credentials</label>
                    </div>
                    <div class="flex-container flex-box">
                        <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_1_credentials"><?php echo( parse_html_chars( $coauthor_1_credentials ) ); ?></textarea>
                    </div>
                </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 1 -->


            <!-- BEGIN COAUTHOR ROW CONTAINER 2 -->
            <div class="js-hide alternate flex-container flex-box" id="coauthor_2_div">

                    <!-- Upload Photo BUTTON -->
                    <div class="flex-container-vertical flex-box-buttons photo_container" id="photo_button_2">
                        <input type="hidden" id="SF_photo_4_url" name="SF_photo_4_url" value="<?php echo( ( $coauthor_2_first_name === '' ? '' : urldecode($SF_photo_4_url) ) ); ?>">
                        <?php if ( $SF_photo_4_url === '' || $SF_photo_4_url === NULL || $coauthor_2_first_name === '' ) {
                            echo '<button class="button-secondary" type="button" id="SF_photo_4">Upload Photo</button>';
                        } else {
                            echo '<div id="SF_photo_4" class="inserted_headshot has_hover" style="background-image: url(\'' . urldecode( $SF_photo_4_url ) . '\')"><div></div></div>';
                        }
                        ?>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="flex-container flex-box" id="input-container-2">
                        <div class="flex-container flex-box" id="first_fields_2">

                            <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                            <div class="flex-container-vertical flex-box-minimal input-label">
                                <label for="coauthor_2_first_name">First&nbsp;Name</label>
                                <label for="coauthor_2_last_name">Last&nbsp;Name</label>
                            </div>

                            <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                            <div class="flex-container-vertical flex-box">
                                <input type="text" name="coauthor_2_first_name" class="js-required" id="coauthor_2_first_name" value="<?php echo($coauthor_2_first_name); ?>">
                                <input type="text" name="coauthor_2_last_name" class="js-required" id="coauthor_2_last_name" value="<?php echo($coauthor_2_last_name); ?>">
                            </div>

                        </div>

                        <div class="flex-container flex-box" id="second_fields_2">
                            <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                            <div class="flex-container-vertical flex-box-minimal input-label">
                                <label for="coauthor_2_email">Email</label>
                                <label for="coauthor_2_twitter">Twitter</label>
                            </div>

                            <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                            <div class="flex-container-vertical flex-box">
                                <input type="text" name="coauthor_2_email" class="js-required" id="coauthor_2_email" value="<?php echo($coauthor_2_email); ?>">
                                <input type="text" name="coauthor_2_twitter" id="coauthor_2_twitter" value="<?php echo($coauthor_2_twitter); ?>">
                            </div>

                        </div>
                    </div>
                    <!-- CREDENTIALS AREA -->
                    <div class="flex-container flex-box" id="credentials_column_2">
                        <div class="flex-container-vertical flex-box-minimal input-label">
                            <label for="coauthor_2_credentials" style="text-align: center;">Credentials</label>
                        </div>
                        <div class="flex-container flex-box">
                            <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_2_credentials"><?php echo( parse_html_chars( $coauthor_2_credentials ) ); ?></textarea>
                        </div>

                    </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 2 -->


            <!-- BEGIN COAUTHOR ROW CONTAINER 3 -->
            <div class="js-hide  flex-container flex-box" id="coauthor_3_div">

                    <!-- Upload Photo BUTTON -->
                    <div class="flex-container-vertical flex-box-buttons photo_container" id="photo_button_3">
                        <input type="hidden" id="SF_photo_5_url" name="SF_photo_5_url" value="<?php echo( ( $coauthor_3_first_name === '' ? '' : urldecode($SF_photo_5_url) ) ); ?>">
                        <?php if ( $SF_photo_5_url === '' || $SF_photo_5_url === NULL || $coauthor_3_first_name === '' ) {
                            echo '<button class="button-secondary" type="button" id="SF_photo_5">Upload Photo</button>';
                        } else {
                            echo '<div id="SF_photo_5" class="inserted_headshot has_hover" style="background-image: url(\'' . urldecode( $SF_photo_5_url ) . '\')"><div></div></div>';
                        }
                        ?>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="flex-container flex-box" id="input-container-3">
                        <div class="flex-container flex-box" id="first_fields_3">

                            <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                            <div class="flex-container-vertical flex-box-minimal input-label">
                                <label for="coauthor_3_first_name">First&nbsp;Name</label>
                                <label for="coauthor_3_last_name">Last&nbsp;Name</label>
                            </div>

                            <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                            <div class="flex-container-vertical flex-box">
                                <input type="text" name="coauthor_3_first_name" class="js-required" id="coauthor_3_first_name" value="<?php echo($coauthor_3_first_name); ?>">
                                <input type="text" name="coauthor_3_last_name" class="js-required" id="coauthor_3_last_name" value="<?php echo($coauthor_3_last_name); ?>">
                            </div>

                        </div>

                        <div class="flex-container flex-box" id="second_fields_3">
                            <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                            <div class="flex-container-vertical flex-box-minimal input-label">
                                <label for="coauthor_3_email">Email</label>
                                <label for="coauthor_3_twitter">Twitter</label>
                            </div>

                            <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                            <div class="flex-container-vertical flex-box">
                                <input type="text" name="coauthor_3_email" class="js-required" id="coauthor_3_email" value="<?php echo($coauthor_3_email); ?>">
                                <input type="text" name="coauthor_3_twitter" id="coauthor_3_twitter" value="<?php echo($coauthor_3_twitter); ?>">
                            </div>

                        </div>
                    </div>
                    <!-- CREDENTIALS AREA -->
                    <div class="flex-container flex-box" id="credentials_column_3">
                    <div class="flex-container-vertical flex-box-minimal input-label">
                        <label for="coauthor_3_credentials" style="text-align: center;">Credentials</label>
                    </div>
                    <div class="flex-container flex-box">
                        <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_3_credentials"><?php echo( parse_html_chars( $coauthor_3_credentials ) ); ?></textarea>
                    </div>

                </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 3 -->


            <!-- BEGIN COAUTHOR ROW CONTAINER 4 -->
            <div class="js-hide alternate  flex-container flex-box" id="coauthor_4_div">

                    <!-- Upload Photo BUTTON -->
                    <div class="flex-container-vertical flex-box-buttons photo_container" id="photo_button_4">
                        <input type="hidden" id="SF_photo_6_url" name="SF_photo_6_url" value="<?php echo( ( $coauthor_4_first_name === '' ? '' : urldecode($SF_photo_6_url) ) ); ?>">
                        <?php if ( $SF_photo_6_url === '' || $SF_photo_6_url === NULL || $coauthor_4_first_name === '' ) {
                            echo '<button class="button-secondary" type="button" id="SF_photo_6">Upload Photo</button>';
                        } else {
                            echo '<div id="SF_photo_6" class="inserted_headshot has_hover" style="background-image: url(\'' . urldecode( $SF_photo_6_url ) . '\')"><div></div></div>';
                        }
                        ?>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="flex-container flex-box" id="input-container-4">
                        <div class="flex-container flex-box" id="first_fields_4">

                            <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                            <div class="flex-container-vertical flex-box-minimal input-label">
                                <label for="coauthor_4_first_name">First&nbsp;Name</label>
                                <label for="coauthor_4_last_name">Last&nbsp;Name</label>
                            </div>

                            <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                            <div class="flex-container-vertical flex-box">
                                <input type="text" name="coauthor_4_first_name" class="js-required" id="coauthor_4_first_name" value="<?php echo($coauthor_4_first_name); ?>">
                                <input type="text" name="coauthor_4_last_name" class="js-required" id="coauthor_4_last_name" value="<?php echo($coauthor_4_last_name); ?>">
                            </div>

                        </div>

                        <div class="flex-container flex-box" id="second_fields_4">
                            <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                            <div class="flex-container-vertical flex-box-minimal input-label">
                                <label for="coauthor_4_email">Email</label>
                                <label for="coauthor_4_twitter">Twitter</label>
                            </div>

                            <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                            <div class="flex-container-vertical flex-box">
                                <input type="text" name="coauthor_4_email" class="js-required" id="coauthor_4_email" value="<?php echo($coauthor_4_email); ?>">
                                <input type="text" name="coauthor_4_twitter" id="coauthor_4_twitter" value="<?php echo($coauthor_4_twitter); ?>">
                            </div>

                        </div>
                    </div>
                    <!-- CREDENTIALS AREA -->
                    <div class="flex-container flex-box" id="credentials_column_4">
                        <div class="flex-container-vertical flex-box-minimal input-label">
                            <label for="coauthor_4_credentials" style="text-align: center;">Credentials</label>
                        </div>
                        <div class="flex-container flex-box">
                            <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_4_credentials"><?php echo( parse_html_chars( $coauthor_4_credentials ) ); ?></textarea>
                        </div>

                    </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 4 -->

        </div>
        <!-- END INSIDE LEFT TOPMOST CONTAINER -->

        <!-- RIGHTMOST FLEXBOX -->
        <div class="flex-box-add">
            <input type="button" class="button-secondary" name="add_coauthor" id="add_coauthor" value="+">
        </div>

    </div>
    <!-- END GLOBAL OUTSIDE CONTAINER -->

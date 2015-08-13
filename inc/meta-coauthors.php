<div class="error form-invalid js-hide" id="invalid-email-alert"><strong>Error:</strong> Please enter a valid email address.</div>

    <!-- BEGIN GLOBAL OUTSIDE CONTAINER -->
    <div id="coauthor_container" class="flex-container">

        <!-- BEGIN INSIDE LEFT TOPMOST CONTAINER -->
        <div class="flex-container-vertical flex-box">

            <!-- BEGIN COAUTHOR ROW CONTAINER 1 -->
            <div id="coauthor_1_div" class="flex-container flex-box">

                <!-- Add Photo BUTTON -->
                <div class="flex-container-vertical flex-box-buttons">
                    <input type="hidden" id="SF_photo_3_url" value="">
                    <button type="button" id="SF_photo_3">Add Photo</button>
                </div>

                <!-- INPUT FIELDS -->
                <div class="flex-container flex-box">

                    <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                    <div class="flex-container-vertical flex-box-minimal">
                        <label for="coauthor_1_first_name">First&nbsp;Name</label>
                        <label for="coauthor_1_last_name">Last&nbsp;Name</label>
                    </div>

                    <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                    <div class="flex-container-vertical flex-box">
                        <input type="text" name="coauthor_1_first_name" class="js-required" id="coauthor_1_first_name" value="<?php echo($coauthor_1_first_name); ?>">
                        <input type="text" name="coauthor_1_last_name" class="js-required" id="coauthor_1_last_name" value="<?php echo($coauthor_1_last_name); ?>">
                    </div>

                    <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                    <div class="flex-container-vertical flex-box-minimal">
                        <label for="coauthor_1_email">Email</label>
                        <label for="coauthor_1_twitter">Twitter</label>
                    </div>

                    <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                    <div class="flex-container-vertical flex-box">
                        <input type="text" name="coauthor_1_email" class="js-required" id="coauthor_1_email" value="<?php echo($coauthor_1_email); ?>">
                        <input type="text" name="coauthor_1_twitter" id="coauthor_1_twitter" value="<?php echo($coauthor_1_twitter); ?>">
                    </div>

                </div>

                <!-- CREDENTIALS AREA -->
                <div class="flex-container-vertical flex-box">
                    <label for="coauthor_1_credentials" style="text-align: center;">Credentials</label>
                    <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_1_credentials"><?php echo( parse_html_chars( $coauthor_1_credentials ) ); ?></textarea>
                </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 1 -->


            <!-- BEGIN COAUTHOR ROW CONTAINER 2 -->
            <div class="js-hide" id="coauthor_2_div">

                <div class="flex-container flex-box">

                    <!-- Add Photo BUTTON -->
                    <div class="flex-container-vertical flex-box-buttons">
                        <input type="hidden" id="SF_photo_4_url" value="">
                        <button type="button" id="SF_photo_4">Add Photo</button>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="flex-container flex-box">

                        <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal">
                            <label for="coauthor_2_first_name">First&nbsp;Name</label>
                            <label for="coauthor_2_last_name">Last&nbsp;Name</label>
                        </div>

                        <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_2_first_name" class="js-required" id="coauthor_2_first_name" value="<?php echo($coauthor_2_first_name); ?>">
                            <input type="text" name="coauthor_2_last_name" class="js-required" id="coauthor_2_last_name" value="<?php echo($coauthor_2_last_name); ?>">
                        </div>

                        <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal">
                            <label for="coauthor_2_email">Email</label>
                            <label for="coauthor_2_twitter">Twitter</label>
                        </div>

                        <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_2_email" class="js-required" id="coauthor_2_email" value="<?php echo($coauthor_2_email); ?>">
                            <input type="text" name="coauthor_2_twitter" id="coauthor_2_twitter" value="<?php echo($coauthor_2_twitter); ?>">
                        </div>

                    </div>

                    <!-- CREDENTIALS AREA -->
                    <div class="flex-container-vertical flex-box">
                        <label for="coauthor_2_credentials" style="text-align: center;">Credentials</label>
                        <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_2_credentials"><?php echo( parse_html_chars( $coauthor_2_credentials ) ); ?></textarea>
                    </div>

                </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 2 -->


            <!-- BEGIN COAUTHOR ROW CONTAINER 3 -->
            <div class="js-hide" id="coauthor_3_div">
                <div class="flex-container flex-box">

                    <!-- Add Photo BUTTON -->
                    <div class="flex-container-vertical flex-box-buttons">
                        <input type="hidden" id="SF_photo_5_url" value="">
                        <button type="button" id="SF_photo_5">Add Photo</button>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="flex-container flex-box">

                        <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal">
                            <label for="coauthor_3_first_name">First&nbsp;Name</label>
                            <label for="coauthor_3_last_name">Last&nbsp;Name</label>
                        </div>

                        <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_3_first_name" class="js-required" id="coauthor_3_first_name" value="<?php echo($coauthor_3_first_name); ?>">
                            <input type="text" name="coauthor_3_last_name" class="js-required" id="coauthor_3_last_name" value="<?php echo($coauthor_3_last_name); ?>">
                        </div>

                        <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal">
                            <label for="coauthor_3_email">Email</label>
                            <label for="coauthor_3_twitter">Twitter</label>
                        </div>

                        <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_3_email" class="js-required" id="coauthor_3_email" value="<?php echo($coauthor_3_email); ?>">
                            <input type="text" name="coauthor_3_twitter" id="coauthor_3_twitter" value="<?php echo($coauthor_3_twitter); ?>">
                        </div>

                    </div>

                    <!-- CREDENTIALS AREA -->
                    <div class="flex-container-vertical flex-box">
                        <label for="coauthor_3_credentials" style="text-align: center;">Credentials</label>
                        <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_3_credentials"><?php echo( parse_html_chars( $coauthor_3_credentials ) ); ?></textarea>
                    </div>

                </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 3 -->


            <!-- BEGIN COAUTHOR ROW CONTAINER 4 -->
            <div class="js-hide" id="coauthor_4_div">
                <div class="flex-container flex-box">

                    <!-- Add Photo BUTTON -->
                    <div class="flex-container-vertical flex-box-buttons">
                        <input type="hidden" id="SF_photo_6_url" value="">
                        <button type="button" id="SF_photo_6">Add Photo</button>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="flex-container flex-box">

                        <!-- VERTICAL CONTAINER 1 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal">
                            <label for="coauthor_4_first_name">First&nbsp;Name</label>
                            <label for="coauthor_4_last_name">Last&nbsp;Name</label>
                        </div>

                        <!-- VERTICAL CONTAINER 2 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_4_first_name" class="js-required" id="coauthor_4_first_name" value="<?php echo($coauthor_4_first_name); ?>">
                            <input type="text" name="coauthor_4_last_name" class="js-required" id="coauthor_4_last_name" value="<?php echo($coauthor_4_last_name); ?>">
                        </div>

                        <!-- VERTICAL CONTAINER 3 ~~ LABELS -->
                        <div class="flex-container-vertical flex-box-minimal">
                            <label for="coauthor_4_email">Email</label>
                            <label for="coauthor_4_twitter">Twitter</label>
                        </div>

                        <!-- VERTICAL CONTAINER 4 ~~ INPUTS -->
                        <div class="flex-container-vertical flex-box">
                            <input type="text" name="coauthor_4_email" class="js-required" id="coauthor_4_email" value="<?php echo($coauthor_4_email); ?>">
                            <input type="text" name="coauthor_4_twitter" id="coauthor_4_twitter" value="<?php echo($coauthor_4_twitter); ?>">
                        </div>

                    </div>

                    <!-- CREDENTIALS AREA -->
                    <div class="flex-container-vertical flex-box">
                        <label for="coauthor_4_credentials" style="text-align: center;">Credentials</label>
                        <textarea style="width:100%;" height="100%" class="js-required" placeholder="(title, institution, etc.)" name="coauthor_4_credentials"><?php echo( parse_html_chars( $coauthor_4_credentials ) ); ?></textarea>
                    </div>

                </div>

            </div>
            <!-- END COAUTHOR ROW CONTAINER 4 -->

        </div>
        <!-- END INSIDE LEFT TOPMOST CONTAINER -->

        <!-- RIGHTMOST FLEXBOX ~~ ADD ANOTHER BUTTON -->
        <div class="flex-box-minimal flex-box-add">
            <input type="button" class="button-secondary" name="add_coauthor" id="add_coauthor" value="Add Another">
        </div>

    </div>
    <!-- END GLOBAL OUTSIDE CONTAINER -->

<?php

/*
 * Template Name: Dashboard - Personal Details
 */

get_header(); ?>

    <div class="dash-wrap">

    <div class="row collapse">

        <div class="small-1 medium-1 columns">
            <?php get_sidebar('dashboard'); ?>
        </div>


        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div class="small-11 medium-11 columns" role="main">

	        <div id="dash-main">
            <style>
            .error {
                border-color: #ff0000;
            }
            </style>
            <h2><?php the_title(); ?></h2>

            <?php
                $current_user = wp_get_current_user();
                $owner = false;
                if ($current_user->has_cap('partner')) {
                    $owner = true;
                }
                $parent = get_user_meta($current_user->ID,'2020_parent_account',true);
                if ($parent) {
                    $owner = false;
                }
                $type = get_user_meta($current_user->ID,'2020_account_type',true);
                $owner = true;
            ?>

            <div class="dash-block pers-det">
	            <div class="row collapse">

                	<div class="small-12 medium-2 columns">
	                    	<div class="profile-image">
							<?php
							$profilePic = get_user_meta($current_user->ID,'user_profile_image',true);
							if(!empty($profilePic)) { ?>
								<img src="<?php echo $profilePic; ?>" class="user-img" alt="">
							<?php } else { ?>
								<img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/dashboard/profile-image.jpg" class="user-img" alt="">
							<?php } ?>
	                        <a href="" id="updateProfilePicture" class="edit-prof edit-profile inline-edit" data-scope="details">Edit Profile Image</a>
							<form id="profilePicture" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>?action=update_profile_image" enctype="multipart/form-data">
								<input type="file" name="image" />
								<input type="submit" />
							</form>
	                    	</div>
                    </div>

				  <form action="" method="post" id="update_user">
					<div class="small-12 medium-8 columns" id="details">

                        <div class="row collapse">

                            <div class="small-12 medium-4 columns">
                              <div class="left-col">
                                <p>Name</p>
                            	</div>
                            </div>
                            <div class="small-12 medium-8 columns">
	                            <div class="right-col">
                                <span id="user-first-name" data-id="first-name" class="editable"><?php echo get_user_meta( $current_user->ID, 'first_name', true );?></span>
                                <span id="user-last-name" data-id="last-name" class="editable"><?php echo get_user_meta( $current_user->ID, 'last_name', true );?></span>
	                            </div>
                            </div>

                        </div>

                        <div class="row collapse">

                            <div class="small-12 medium-4 columns">
                                <div class="left-col">
                                <p>Company</p>
                                </div>
                            </div>

                            <div class="small-12 medium-8 columns">
                               <div class="right-col editable" data-type="company" data-id="company">
                                <?php echo get_user_meta( $current_user->ID, 'billing_company', true ); ?>
                               </div>
                            </div>

                        </div>



                        <div class="row collapse">

                            <div class="small-12 medium-4 columns">
	                            <div class="left-col">
                                <p>Email Address</p>
	                            </div>
                            </div>

                            <div class="small-12 medium-8 columns">
                               <div class="right-col editable" data-type="email" data-id="email">

                                <?php echo $current_user->user_email; ?>
                               </div>
                            </div>

                        </div>
                        <div class="row collapse">

                            <div class="small-12 medium-4 columns">
	                            <div class="left-col">
                                <p>Password</p>
	                            </div>
                            </div>

                            <div class="small-12 medium-8 columns">
                              <div class="right-col editable" data-type="password" data-id="password">
                                &bull;&bull;&bull;&bull;&bull;&bull;&bull;
                              </div>
                            </div>

                        </div>
                        <div class="row collapse">

                            <div class="small-12 medium-4 columns">
	                            <div class="left-col">
                                <p>Account type</p>
	                            </div>
                            </div>

                            <div class="small-12 medium-8 columns">

                                <div class="right-col">

	                                <?php if ($type) :
                                    echo ucwords($type);
                                    endif;
                                	?>

                                </div>

                            </div>

                        </div>
                        <?php if ($owner) : ?>
                            <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Telephone</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns" >
                                    <div class="right-col editable" data-type="phone" data-id="phone">

                                    	<?php echo get_user_meta( $current_user->ID, 'billing_phone', true );?>
                                    </div>
                                </div>

                            </div>
                            <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                   <div class="left-col">
																		 <p>Address</p>
                                   </div>
                                </div>

                                <div class="small-12 medium-8 columns">
	                                <div class="right-col">
                                    <?php
                                        echo '<div data-label="Line 1" data-id="line-1" class="editable">'.get_user_meta( $current_user->ID, 'billing_address_1', true ).'</div>';
                                        echo '<div data-label="Line 2" data-id="line-2" class="editable">'.get_user_meta( $current_user->ID, 'billing_address_2', true ).'</div>';
                                        echo '<div data-label="City" data-id="city" class="editable">'.get_user_meta( $current_user->ID, 'billing_city', true ).'</div>';
                                        echo '<div data-label="County" data-id="county" class="editable">'.get_user_meta( $current_user->ID, 'billing_state', true ).'</div>';
                                        echo '<div data-label="Postcode" data-id="postcode" class="editable">'.get_user_meta( $current_user->ID, 'billing_postcode', true ).'</div>';
                                    ?>
                                  </div>
                                </div>

                            </div>
                        <?php endif; ?>
                    </div>
                  <div class="small-12 medium-2 columns">
	                    <a class="inline-edit gen-btn orange dtls-edit" data-scope="details" id="editProfile">Edit</a>
                    </div>
                </form>
							</div>
            </div>

            <?php if ($owner) : ?>

                <?php if (user_has_international_subscription($current_user->ID)) : ?>
                    <h3>2020 International subscription details</h3>

                    <div class="dash-block subscription-det">

	                <div class="row" data-equalizer>

	                    <?php
	                        // check if the user has already a listing
	                        $entry = get_posts(array('post_type'=>'directory','author'=>$current_user->ID));
	                        if (count($entry)>0) {
	                            $entry = $entry[0];
	                        }
	                        else {
	                            $entry = false;
	                        }




	                    ?>
                        <form action="" method="post" id="international">

	                    <div class="small-12 medium-8 medium-offset-2 columns">


	                            <div class="row collapse">


	                                <div class="small-12 medium-4 columns">
	                                    <div class="left-col">
											<p>Contact Name</p>
	                                    </div>
	                                </div>


	                                <div class="small-12 medium-8 columns">


		                                <div class="right-col">

	                                    <?php /*<span class="editable-h" style="display: none;"><input type="text" name="intl_contact_name" class="inlinefield" value="<?php if (isset($entry->ID)) { the_field('contact_name',$entry->ID); }?>" /></span> */ ?>
	                                    <span class="editable" data-type="text" data-id="intl_contact_name"><?php if (isset($entry->ID)) { the_field('contact_name',$entry->ID); }?></span>


		                                </div>
	                                </div>


	                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
										<p>Company Name</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">


	                                <div class="right-col">

                                   <?php /* <span class="editable-h" style="display: none;"><input type="text" name="intl_company_name" class="inlinefield" value="<?php if ($entry) { echo $entry->post_title; }?>" /></span> */ ?>
                                    <span class="editable" data-type="text" data-id="intl_company_name"><?php if (isset($entry->ID)) { echo $entry->post_title; }?></span>


	                                </div>


                                </div>

                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Address</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">
                                    <div class="right-col">

	                                    <!-- <span class="editable-h" style="display: none;"><textarea name="intl_address" class="inlinefield"><?php if ($entry) { echo str_replace('<br />','',get_field('address',$entry->ID)); }?></textarea></span> -->
	                                    <span class="editable"  data-type="text" data-id="intl_address"><?php if (isset($entry->ID)) { echo get_field('address',$entry->ID); }?></span>
                                    </div>
                                </div>

                            </div>
															<div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Postcode</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">


	                                <div class="right-col">

<?php /*                            <span class="editable-h" style="display: none;"><input type="text" name="intl_postcode" class="inlinefield" value="<?php if ($entry) { echo get_field('postcode',$entry->ID); }?>" /></span> */ ?>
                                    <span class="editable" data-type="text" data-id="intl_postcode"><?php if (isset($entry->ID)) { echo get_field('postcode',$entry->ID); }?></span>
	                                </div>
                                </div>

                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Country</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">
	                                <div class="right-col">


                                   <span class="editable" data-type="text" data-id="intl_country"><?php if (isset($entry->ID)) { echo get_field('country',$entry->ID); }?></span>
	                                </div>
                                </div>


                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Phone</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">


	                                <div class="right-col">

                                    <span class="editable-h" style="display: none;"><input type="text" name="intl_phone" class="inlinefield" value="<?php if ($entry) { echo get_field('telephone',$entry->ID); }?>" /></span>


                                    <span class="editable" data-type="text" data-id="intl_phone"><?php if (isset($entry->ID)) { echo get_field('telephone',$entry->ID); }?></span>
                                	</div>
                                </div>
                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Fax</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">
                                    <div class="right-col">

	                                    <!-- <span class="editable-h" style="display: none;"><input type="text" name="intl_fax" class="inlinefield" value="<?php if ($entry) { echo get_field('fax',$entry->ID); }?>" /></span> -->
	                                    <span class="editable" data-type="text" data-id="intl_fax"><?php if (isset($entry->ID)) { echo get_field('fax',$entry->ID); }?></span>
                                    </div>
                                </div>

                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
										<p>Email</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">
	                                <div class="right-col">

                                    <!--<span class="editable-h" style="display: none;"><input type="text" name="intl_email" class="inlinefield" value="<?php if ($entry) { echo get_field('email',$entry->ID); }?>" /></span> -->
                                    <span class="editable" data-type="text" data-id="intl_email"><?php if (isset($entry->ID)) { echo get_field('email',$entry->ID);}?></span>
	                                </div>
                                </div>

                            </div>
	                            <div class="row collapse">


                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Web Link</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns">
	                                <div class="right-col">

		                               <!-- <span class="editable-h" style="display: none;"><input type="text" name="intl_web" class="inlinefield" value="<?php if ($entry) { echo get_field('web_link',$entry->ID); }?>" /></span> -->
                                        <span class="editable" data-type="text" data-id="intl_web"><?php if (isset($entry->ID)) { echo get_field('web_link',$entry->ID); }?></span>
	                                </div>
                                </div>

                            </div>

							<div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
										<p>Location</p>
                                    </div>
                                </div>

								<?php
								if (isset($entry->ID)) {
									$intlLocations = wp_get_post_terms($entry->ID, 'location');
									foreach($intlLocations as $intlLocation) {
										if($intlLocation->parent == 0) {
											$intlLocationContinent = $intlLocation;
										}
									}
									foreach($intlLocations as $intlLocation) {
										if(isset($intlLocationContinent) && $intlLocationContinent->term_id == $intlLocation->parent) {
											$intlLocationCountry = $intlLocation;
										}
									}
								}
								?>

                                <div class="small-12 medium-8 columns showHidden">
	                                <div class="right-col">

                                    <span class="editable-h" style="display: none;">
										<?php
										$args = array('parent'=>0,'orderby'=>'name','hide_empty'=>false);
										$continents = get_terms( 'location', $args );
										?>
										<select id="intl_continent" name="intl_continent" class="edit-field">
											<option value="">Select..</option>
											<?php foreach($continents as $continent) { ?>
												<option value="<?php echo $continent->term_id; ?>" <?php if(isset($intlLocationContinent) && $intlLocationContinent->term_id == $continent->term_id) echo 'selected="selected"'; ?>><?php echo $continent->name; ?></option>
											<?php } ?>
										</select>

										<select class="edit-field" id="intl_country" name="intl_country" <?php if(!isset($intlLocationContinent)) echo 'style="display:none"'; ?>>
											<?php
											foreach($continents as $continent) {
												$args = array('parent'=>$continent->term_id,'orderby'=>'name','hide_empty'=>false);
												$countrys = get_terms( 'location', $args );
												foreach($countrys as $country) { ?>
													<option data-continent="<?php echo $continent->term_id; ?>" value="<?php echo $country->term_id; ?>" <?php if(isset($intlLocationCountry) && $intlLocationCountry->term_id == $country->term_id) echo 'selected="selected"'; if(isset($intlLocationContinent) && $continent->term_id != $intlLocationContinent->term_id) echo 'style="display:none"'; ?>><?php echo $country->name; ?></option>
												<?php }
											} ?>
										</select>
									</span>
                                    <span class="editable"><?php if(isset($intlLocationContinent)) echo $intlLocationContinent->name; ?> <?php if(isset($intlLocationCountry)) echo '> '.$intlLocationCountry->name; ?></span>
	                                </div>
                                </div>

                            </div>

							<?php
							if (isset($entry->ID)) {
								$specialisms = wp_get_post_terms($entry->ID, 'directory_cat');
							}
							$args = array('parent'=>0,'orderby'=>'name','hide_empty'=>false);
							$specialismsList = get_terms('directory_cat', $args);
							?>

							<div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Specialisms</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-8 columns showHidden">
	                                <div class="right-col">

                                    <span class="editable-h" style="display: none;">
										<select name="intl_specialisms" class="edit-field" multiple="multiple" style="height:150px" id="intl_specialisms">
											<?php
											foreach($specialismsList as $specialism) {
												$match = 0;
                                                if(is_array($specialisms)) {
                                                    foreach($specialisms as $specialism2) {
                                                        if($specialism->term_id == $specialism2->term_id) {
                                                            $match = 1;
                                                        }
                                                    }
                                                }
												echo '<option value="'.$specialism->term_id.'" '.($match ? 'selected="selected"' : '').'>'.$specialism->name.'</option>';
											}
											?>
										</select>
									</span>
                                    <span class="editable">
										<?php if(isset($entry->ID) && isset($specialisms) && !empty($specialisms)) {
											echo '<ul>';
												foreach($specialisms as $specialism) {
													echo '<li>'.$specialism->name.'</li>';
												}
											echo '</ul>';
										} ?>
									</span>
	                                </div>
                                </div>

                            </div>

                            <?php /*
	                            <div class="row collapse">

	                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Categories</p>
                                    </div>
                                	</div>

	                                <div class="small-12 medium-8 columns">
		                                <div class="right-col">

	                                    <span class="editable-h"  style="display: none;">
	                                    <ul id="cat_list">
	                                    <?php

	                                        $selected_cats = wp_get_object_terms( $entry->ID,'directory_cat' );

	                                        $selected = array();

	                                        foreach ($selected_cats as $cat) {
	                                            $selected[] = $cat->name;
	                                        }
	                                        $cats = get_dir_categories();
	                                        foreach ($cats as $cat) {

	                                            echo '<li><input type="checkbox" name="intl_cat[]" class="intl_cat inlinefield" value="'.$cat->name.'"';
	                                            if (in_array($cat->name,$selected)) { echo ' checked '; }
	                                            echo ' />'.$cat->name.'</li>';

	                                        }
	                                        echo '</ul>';
	                                    ?>
	                                    </span>
	                                    <span class="editable"><ul><?php foreach ($selected as $sel) { echo '<li>'.$sel.'</li>';}?></ul></span>
	                                </div>
	                              </div>
	                            </div> */ ?>
	                        </form>
	                    </div>
	                    <div class="small-12 medium-2 columns">

	                          <a class="edit-int gen-btn orange dtls-edit"  name="international" data-scope="international"  id="editInt">Edit</a>

	                    </div>
	                </div>

                </div>
                <?php endif; ?>
                <?php if (!$parent && check_groups_user_capabilities(array(4,8,3,1,6))) : ?>


                <h3>Additional Users</h3>

                <form action="" method="post" id="new_user">

	                <div class="dash-block addi-user">

		                <div class="row">

			                    <div class="small-12 medium-2 columns">
			                        Add new user
			                    </div>

			                    <div class="small-12 medium-8 columns">

			                            <div class="row collapse">

			                                <div class="small-12 medium-4 columns">
			                                    <div class="left-col">
																						<p>Name</p>
			                                    </div>
			                                </div>

			                                <div class="small-12 medium-8 columns">
				                                	<div class="right-col">
				                                    <input type="text" name="new_first_name" id="new_first_name" placeholder="First name" />
				                                    <input type="text" name="new_last_name" id="new_last_name" placeholder="Last name" />
				                                	</div>
			                                </div>

			                            </div>
			                            <div class="row collapse">

		                                <div class="small-12 medium-4 columns">
		                                    <div class="left-col">
												<p>Email Address</p>
		                                    </div>
		                                </div>

		                                <div class="small-12 medium-8 columns">
		                                    <div class="right-col">
		                                    	<input type="email" name="new_email" id="new_email" />
		                                    </div>
		                                </div>

		                            </div>
                                  <?php if (check_groups_user_capabilities(4)) { ?>
			                            <div class="row collapse">

			                                <div class="small-12 medium-4 columns">
			                                    <div class="left-col">
													<p>Account type</p>
			                                    </div>
			                                </div>

			                                <div class="small-12 medium-8 columns">
				                                <div class="right-col">
			                                    <select name="account_type" id="account_type">
                                            <option value="partner">Partner</option>
                                            <option value="Employee">Employee</option>
                                          </select>
				                                </div>
			                                </div>

			                            </div>
			                            <div class="row collapse">

			                                <div class="small-12 medium-8 medium-offset-4 columns">
			                                    <p>A password will automatically be generated for a new user</p>
			                                </div>
			                            </div>
                                  <?php } ?>
			                    </div>
			                    <div class="small-12 medium-2 columns">
				                    <button type="submit" class="gen-btn orange" id="add-button">Add</button>
			                    </div>

												</div>

											</div>

                </form>

                <h3>Current Users</h3>

                <div class="dash-block">

                <div class="row" style="background: #edf3f3">

                    <div class="small-12 columns">
                        <div id="child-users"class="current-user-wrap">
                        <?php
                          
                            $results = getChildUserAccounts($ma_id);
                            if (count($results)>0) {

                                ?>
                                <table id="current-users">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $result) : ?>
                                        <tr>
                                            <td><?php echo $result->first_name; ?></td>
                                            <td><?php echo $result->last_name; ?></td>
                                            <td><?php echo $result->user_email; ?></td>
                                            <td><?php echo ucfirst($result->account_type); ?></td>
                                            <td><?php if (get_user_meta($result->ID,'2020_account_status',true)=='true') { echo 'Disabled';} else { echo 'Active'; }?></td>
                                            <th><a href="/dashboard/edit-user-details/?id=<?php echo $result->ID;?>" class="fancybox fancybox.iframe">Edit</a></th>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            else {
                                echo '<p>No related user accounts at the moment.</p>';
                            }
                        ?>
                        </div>
                    </div>

                </div>

                </div>

                <?php endif; ?>
            <?php endif; ?>

        </div>
        <?php endwhile; ?>
        <?php endif; ?>

				</div>
    </div>

    </div>

<?php get_footer();

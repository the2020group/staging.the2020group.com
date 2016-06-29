<?php 

/*
 * Template Name: Contact Us
 */

get_header();

  // Get all office details
  $offices = get_posts(
    array(
  		'post_type' => 'offices',
  		'posts_per_page' => -1,
  		'post_status' => 'publish'
    )
  );
?>
    
    <div class="row">
    
        <div class="small-12 medium-8 columns" role="main">
 <?php  

/*
                $args = array ('child_of'=>get_the_ID(),
                               'title_li'=> '',
                               'sort_column'=>'menu_order' );
                wp_list_pages( $args );
*/

            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
                
                <header class="article-header">
                    <h2><?php the_title(); ?></h2>
                </header>
                
                <section class="offices-content">
                  
                  <?php the_content(); ?>
                    
                  <div class="tab-set">
                    
        	          <dl id="officesTabs" class="tabs" data-tab data-options="scroll_to_content: false" data-equalizer>
        						  <dd class="active"><a href="#innovation" data-equalizer-watch>2020 Innovation</a></dd>
        						  <dd><a href="#practice-exchange" data-equalizer-watch>2020 Practice Exchange</a></dd>
        						  <dd><a href="#tax-protection" data-equalizer-watch>2020 Tax Protection</a></dd>
        						</dl>
        						
        						<div id="officeTabContent" class="tabs-content">

                      <?php 
                        
                        // Go find all the offices flagged as 'primary' and attribute to each division taxonomy
                        $primaryOffices = array();
                        foreach ( $offices as $key=>$office ):

                          $officeDivision = get_field('office_division', $office->ID);
                          $primaryFlag = get_field('office_primary', $office->ID);
                          
                          if($primaryFlag && $officeDivision != '') :
                            $primaryOffices[$officeDivision] = $office;
                            unset($offices[$key]);
                          endif;

                        endforeach;
                        

                        $setActiveTab = false;
        						    $primaryOfficeOrder = array(81=>'innovation',82=>'practice-exchange',83=>'tax-protection');          						    

        						    foreach($primaryOfficeOrder as $orderId=>$divId) :

                          // Set our variables to get all the info for re-use
                          $officeTitle = $primaryOffices[$orderId]->post_title;
                          $officeLocation = get_field('office_location', $primaryOffices[$orderId]->ID);
                          $officeAddress = get_field('office_business_address', $primaryOffices[$orderId]->ID);
                          $officePhone = get_field('office_contact_number', $primaryOffices[$orderId]->ID);
                          $officeEmail = get_field('office_contact_email', $primaryOffices[$orderId]->ID);
                          $officeMap = get_field('office_map_directions', $primaryOffices[$orderId]->ID);
                          $officeInfo = get_field('office_additional_info', $primaryOffices[$orderId]->ID);

                          // Get the featured image ( the country flag)
    											$thumb_src = null;
    											if ( has_post_thumbnail($post->ID) ) {
    												$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'team-thumb' );
    												$thumb_src = $src[0];
    											}
        						    
                          if(!$setActiveTab) :
                            $activeClass = ' active';
                            $setActiveTab = true;
                          else :
                            $activeClass = '';
                          endif;
                          ?>
                          
                          <div class="content<?php echo $activeClass; ?>" id="<?php echo $divId; ?>">
                          
                            <div class="office-item">
      
                              <h4><?php echo $officeTitle; ?></h4>
                              <?php if (strlen($officeLocation)) :
                                echo '<p class="office-location">' . $officeLocation . '</p>';
                              endif; ?>
        
                              <?php if (strlen($officeAddress)) :
                                echo '<p>' . $officeAddress . '</p>';
                              endif; ?>
                              
                              <?php if ( has_post_thumbnail($primaryOffices[$orderId]->ID) ) :
        												$src = wp_get_attachment_image_src( get_post_thumbnail_id( $primaryOffices[$orderId]->ID ), 'team-thumb' );
        												$thumb_src = $src[0];
                              ?>
      											   <img src="<?php echo $thumb_src; ?>" class="office-img">
                              <?php endif; ?>                              
        
                              <ul class="iconListing">
                                <?php if (strlen($officePhone)) : ?>
                                  <li><a href="tel:<?php echo $officePhone; ?>"><i class="icon-telephone icon"></i><?php echo $officePhone; ?></a></li>
                                <?php endif; ?>
                                
                                <?php if (strlen($officeEmail)) : ?>
                                  <li><a href="mailto:<?php echo $officeEmail; ?>"><i class="icon-letter icon"></i><?php echo $officeEmail; ?></a></li>
                                <?php endif; ?>
                              </ul>

                              <?php if ($officeMap) : ?>
                                <a href="<?php echo $officeMap; ?>" target="_blank" class="gen-btn orange icon down-arrow">Download PDF Map &amp; Directions</a>
                              <?php endif; ?>

                              <?php if (strlen($officeInfo)) : ?>
                                <?php echo $officeInfo; ?>
                              <?php endif; ?>
                              
                            </div>
                            
                            <?php if( have_rows('office_subdivision_contacts', $primaryOffices[$orderId]->ID) ): ?>
                            
                            	<div class="office-subcontacts row collapse" data-equalizer>
                            
                            	<?php while( have_rows('office_subdivision_contacts', $primaryOffices[$orderId]->ID) ): the_row(); 
                            
                            		// vars
                            		$subdivisionName = get_sub_field('subdivision_name', $primaryOffices[$orderId]->ID);
                            		$subdivisionPhone = get_sub_field('subdivision_phone', $primaryOffices[$orderId]->ID);
                            		$subdivisionEmail = get_sub_field('subdivision_email', $primaryOffices[$orderId]->ID);
                            
                            	?>
                              <?php if( $subdivisionName ): ?>
                            		<div class="small-12 large-6 columns officeContact" data-equalizer-watch>
                              		<div class="item">
                            
                              			<?php if( $subdivisionName ): ?>
                              				<h4><?php echo $subdivisionName; ?></h4>
                              			<?php endif; ?>
                             				
                                    <ul class="iconListing">
                                      <?php if (strlen($subdivisionPhone)) : ?>
                                        <li><a href="tel:<?php echo $subdivisionPhone; ?>"><i class="icon-telephone icon"></i><?php echo $subdivisionPhone; ?></a></li>
                                      <?php endif; ?>
                                      
                                      <?php if (strlen($subdivisionEmail)) : ?>
                                        <li><a href="mailto:<?php echo $subdivisionEmail; ?>"><i class="icon-letter icon"></i><?php echo $subdivisionEmail; ?></a></li>
                                      <?php endif; ?>
                                    </ul>
                                    
                              		</div>
                            		</div>
                              <?php endif; ?>

                            	<?php endwhile; ?>
                            
                            	</div>
                            
                            <?php endif; ?>
                            
                            <?php if($orderId == 81) : ?>
                            
                            <div class="row collapse office-sublist" data-equalizer>
        						
					                    <?php 
					                      // Get the regular secondary offices to appear underneath the main tabs
					                      
																foreach ( $offices as $office ):
																// setup_postdata($office);
					                      
					                      $officeTitle = $office->post_title;
					                      $officeLocation = get_field('office_location', $office->ID);
					                      $officePhone = get_field('office_contact_number', $office->ID);
					                      $officeEmail = get_field('office_contact_email', $office->ID);
					                      $officeWebsite = get_field('office_contact_website', $office->ID);
																
																$thumb_src = null;
																if ( has_post_thumbnail($post->ID) ) {
																	$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'team-thumb' );
																	$thumb_src = $src[0];
																}
					                    ?>
					                    
					                    <div class="small-12 large-6 column">
					                      <div class="office-item" data-equalizer-watch>
					  
					                        <?php // print_r($office); ?>
					                        <h4><?php echo $officeTitle; ?></h4>
					                        <?php if (strlen($officeLocation)) :
					                          echo '<p class="office-location">' . $officeLocation . '</p>'; ?>
					                        <?php endif; ?>
					  
					                        <?php if (strlen($officeAddress)) :
					                          // echo '<p>' . $officeAddress . '</p>'; ?>
					                        <?php endif; ?>
					                        
					                        <?php if ( has_post_thumbnail($office->ID) ) :
					  												$src = wp_get_attachment_image_src( get_post_thumbnail_id( $office->ID ), 'team-thumb' );
					  												$thumb_src = $src[0];
					                        ?>
																   <img src="<?php echo $thumb_src; ?>" class="office-img">
					                        <?php endif; ?>
					                        
					  
					                        <ul class="iconListing">
					                          <?php if (strlen($officePhone)) :
					                            echo '<li><a href="tel:' . $officePhone . '"><i class="icon-telephone icon"></i>' . $officePhone . '</a></li>'; ?>
					                          <?php endif; ?>
					                          
					                          <?php if (strlen($officeEmail)) :
					                            echo '<li><a href="mailto:' . $officeEmail . '"><i class="icon-letter icon"></i>' . $officeEmail . '</a></li>'; ?>
					                          <?php endif; ?>
					                          
					                          <?php if (strlen($officeWebsite)) :
					                            echo '<li><a href="http://' . $officeWebsite . '" target="_blank"><i class="icon-webinars icon"></i>' . $officeWebsite . '</a></li>'; ?>
					                          <?php endif; ?>
					                        </ul>
					                        
					                      </div>
					                    </div>
					                    
					     						    <?php endforeach; ?>
					      						</div>
                            
                            <?php endif; ?>
                            

                          </div>
        						  
        						  <?php
        						    endforeach;
          						?>
          						
        						</div>

                  </div>
        					
                  
                </section>
                
                
                
            </article>        
        </div>
        
        <div class="small-12 medium-4 columns">
        
            <?php get_sidebar('contact'); ?>
            
        </div>

    </div>

<?php get_footer();
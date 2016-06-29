<?php 

/*
 * Template Name: ACCA template
 */

get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    
    <div class="row">
    	<div class="small-12 large-10 large-offset-1 columns">
	    	<h1 class="acca-title"><?php the_title(); ?></h1>
	    </div>
    </div>
    <header class="acca-head">
	    <div class="row">
	        <div class="small-12 large-7 large-offset-1 columns">
		        <div class="intro-copy">
			        <?php the_content(); ?>
			      </div>
	        </div>
	        <div class="small-12 large-3 columns end webinar-img">
		        <a href="" class="">
		        <?php 
						$image = get_field('acca_main_download');
						
						if( !empty($image) ): ?>
						
							<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
						
						<?php endif; ?>
						</a>
		        <p><a href="" class="download">Download the 2015 Webinar Programme <span class="icon-down-arrow"></span></a></p>
	        </div>
			 </div>
    </header>
    <div id="packages">
	     <div class="row" data-equalizer>
			 		<div class="small-12 large-7 large-offset-1 columns">
				 		<?php the_field('acca_table'); ?>
		        <!--
<table id="acca-table" data-equalizer-watch>
						  <tbody>
						    <tr class="tab-head">
						      <td colspan="3">ACCA Webinar Subscription Packages</td>
						    </tr>
						    <tr class="tab-sub-head">
						      <td>Subscription Package Options:</td>
						      <td>Cost Per Delegate</td>
						      <td>Cost 3-9 * Delegates</td>
						    </tr>
						    <tr>
						      <td>CPD Webinars (12)</td>
						      <td>£199 net</td>
						      <td>£594 net</td>
						    </tr>
						    <tr>
						      <td>Monthly Tax update Webinars (10)</td>
						      <td>£185 net</td>
						      <td>£549 net</td>
						    </tr>
						    <tr>
						      <td>CPD (12) and Monthly Tax Updates (10)</td>
						      <td>£327 net</td>
						      <td>£979 net</td>
						    </tr>
						    <tr>
						      <td>Practice Management & Development (4)</td>
						      <td>£112 net</td>
						      <td>£337 net</td>
						    </tr>
						    <tr>
						      <td>Professional Development Workshop (8)</td>
						      <td>£400​ net</td>
						      <td>£1200 net</td>
						    </tr>
						    <tr>
						      <td>FCA Update Webinars (4)</td>
						      <td>£149 net</td>
						      <td>£447 net</td>
						    </tr>
						  </tbody>
						</table>
		        <p>Where applicable, course material is made available to all registered participants.<br>
			      Webinars are recorded and emailed to participants after each webinar so that you can listen again at your leisure.</p>
-->
		      </div>
	        <div class="small-12 large-3 end columns">
		        <div class="book-panel" data-equalizer-watch>
			        <h3>Book Online</h3>
			        
			        <ul class="participants">
			        <?php if( have_rows('acca_booking') ): ?>

							<?php while( have_rows('acca_booking') ): the_row(); 
						
								$content = get_sub_field('acca_booking_participant');
						
								?>
								
								<li class="participant">
						
								<?php echo $content; ?>
								
								</li>
						
							<?php endwhile; ?>
							
			        </ul>
						
							</div>
						
						<?php endif; ?>
			       
			      </div>
		      </div>
	     </div>
     </div>
     <div class="row">
        <div class="small-12 large-10 large-offset-1 columns">
	        
	        <ul id="accaTabs" class="tabs vertical" data-tab data-options="scroll_to_content: false">
					  <li class="tab-title active"><a href="#panel01">FCA Update</a></li>
					  <li class="tab-title"><a href="#panel02">Introduction to FCA Clients</a></li>
					  <li class="tab-title"><a href="#panel03">Introduction to MGI Clients</a></li>
					  <li class="tab-title"><a href="#panel04">Monthly Tax Update</a></li>
					  <li class="tab-title"><a href="#panel05">Practice Management & Development</a></li>
					  <li class="tab-title"><a href="#panel06">Professional Development Workshop</a></li>
					</ul>
	        
	        <?php if( have_rows('acca_tabbed_content') ): ?>
	        
	        	<?php 
		        	$counter = 0; 
		        	$active = 'active';
		        	
	        	?>

						<div id="accaTabsContent" class="tabs-content">
					
						<?php while( have_rows('acca_tabbed_content') ): the_row(); 
					
							// vars
							$tab_title = get_sub_field('acca_tab_title');
							$tab_content = get_sub_field('acca_tab_content');
							$tab_info = get_sub_field('acca_tab_info');
							
							?>
					
						 <div class="content <?php echo $active;?>" id="panel0<?php echo $counter; ?>">
							 
							 <h3><?php echo $tab_title; ?></h3>
				
								<div class="row">
									
									<div class="small-12 large-8 columns">
									
										<?php echo $tab_content; ?>
				
									</div>
									<div class="small-12 large-4 columns">
										<div class="web-info">
											
											<?php echo $tab_info; ?>
											
										</div>
									</div>
								</div>
						 </div>
						 
						 <?php 
							 $counter++; 
							 $active = '';
							 
						 ?>
					
						<?php endwhile; ?>
					
					<?php endif; ?>
	        <!--
	        <ul id="accaTabs" class="tabs vertical" data-tab data-options="scroll_to_content: false">
					  <li class="tab-title active"><a href="#panel01">FCA Update</a></li>
					  <li class="tab-title"><a href="#panel02">Introduction to FCA Clients</a></li>
					  <li class="tab-title"><a href="#panel03">Introduction to MGI Clients</a></li>
					  <li class="tab-title"><a href="#panel04">Monthly Tax Update</a></li>
					  <li class="tab-title"><a href="#panel05">Practice Management & Development</a></li>
					  <li class="tab-title"><a href="#panel06">Professional Development Workshop</a></li>
					</ul>
					<div id="accaTabsContent" class="tabs-content">
					  
					  <div class="content active" id="panel01">
					    <h3>FCA Update Webinar Programme - Ian Fletcher</h3>
					  
						  <div class="row">
							  <div class="small-12 large-8 columns">
						  
						    <p>Content<br>
							  This webinar is designed to refresh delegates on the FCA regime and to ensure they are up to date with all the latest FCA developments and rule changes.</p>
							  <p>Key Topics<br>
								Amongst other things, this webinar will cover:</p>
								 <ul>
									 <li>FCA Update</li>
									 <li>New FCA Policy Statements and Consultation Papers</li>
									 <li>Update on Retail Distribution Review</li>
									 <li>Practical advice when doing the work</li>
								 </ul>
								 <p>Who Should Attend?<br>
									 All fee earners who prepare accounts, and audit, or help FCA clients with their administration and want to be up to date with FCA and other pronouncements.</p>
								 <a href="" class="gen-btn icon acca check">Book Now</a>
						  
							  </div>
							  <div class="small-12 large-4 columns">
								  <div class="web-info">
										<h5>Date</h5>
										
										<ul>
											<li>08 Dec 2014</li>
											<li>26 Mar 2015</li>
											<li>23 Jun 2015</li>
											<li>28 Sep 2015</li>
											<li>08 Dec 2015</li>
										</ul>
										
										<h5>Time</h5>
										
										<ul>
											<li>11:00 to 13:00</li>
										</ul>
										
										<h5>Cost:</h5>
										
										<ul>
											<li>£44 plus VAT</li>
										</ul>
								  </div>
							  </div>
						  </div>
					  </div>
					  
					  <div class="content" id="panel02">
					    <h3>Introduction to FCA Clients - Ian Fletcher</h3>
					    
					    <div class="row">
							  <div class="small-12 large-8 columns">
								  
								  
					    <p>Content<br>
						    Most mortgage, general insurance, stock broking and IFA type entities are regulated by the FCA. This course is designed to introduce delegates to the FCA regime 
						    and to ensure that they have the compliance procedures and knowledge to comply fully with the rules and undertake audit and accounts work effectively.</p>
						  <p>Key Topics</p>
						  <ul>
								 <li>The key rules of the FSMA 2000 and FCA</li>
								 <li>Part IV authorisation</li>
								 <li>The FSA web site</li>
								 <li>Permanent information</li>
								 <li>Engagement and representation letters</li>
								 <li>RMAR reporting requirements</li>
								 <li>Client monies and assets</li>
								 <li>Audit exempt work</li>
								 <li>Risk and common errors</li>
								 <li>Audit procedures for planning, fieldwork and completion stages</li>
								 <li>Case studies will form part of the course</li>
							 </ul>
							 
							 <p>Who should attend?<br>
							Anyone who has clients who are regulated by the FCA and wants to get a thorough understanding of the rules and accounts/audit requirements.</p>
							
							<a href="" class="gen-btn icon acca check">Book Now</a>
							
							</div>
							  <div class="small-12 large-4 columns">
								  <div class="web-info">
										<h5>Date</h5>
										
										<ul>
											<li>26 Feb 2015</li>
											<li>09 Sep 2015</li>
										</ul>
										
										<h5>Time</h5>
										
										<ul>
											<li>11:00 to 13:00</li>
										</ul>
										
										<h5>Cost:</h5>
										
										<ul>
											<li>£64 plus VAT</li>
										</ul>
								  </div>
							  </div>
						  </div>
						</div>
					  
					  <div class="content" id="panel03">
					    <h3>Introduction to MGI Clients - Ian Fletcher</h3>
					    
					    <div class="row">
							  <div class="small-12 large-8 columns">
								  
					    <p>Content<br>
						    The objectives of this course are to ensure that all auditors of mortgage and general insurance brokers are fully prepared for the FCA regulatory environment and understand the procedures they must adopt to comply with their responsibilities to the client and the FCA.</p>
						  <ul>
								 <li>An overview of the FCA</li>
								 <li>Permanent information required</li>
								 <li>Preparation and resources needed</li>
								 <li>APB Bulletin 2011/12 and FRC Bulletin 3</li>
								 <li>The specific requirements of an FCA audit as compared to a true and fair audit</li>
								 <li>CASS 5 and 5A rules</li>
								 <li>Completion and partner review</li>
								 <li>Reporting and whistle blowing requirements</li>
								 <li>Accounting requirements for audit exempt clients</li>
								 <li>New FCA and other regulatory developments</li>
							 </ul>
							 
							 <a href="" class="gen-btn icon acca check">Book Now</a>
							 
							 </div>
							  <div class="small-12 large-4 columns">
								  <div class="web-info">
										<h5>Date</h5>
										
										<ul>
											<li>17 Mar 2015</li>
											<li>21 Oct 2015</li>
										</ul>
										
										<h5>Time</h5>
										
										<ul>
											<li>£64 plus VAT</li>
										</ul>
										
										<h5>Cost:</h5>
										
										<ul>
											<li>£44 plus VAT</li>
										</ul>
										
										<h5>Presenter:</h5>
										
										<ul>
											<li>Ian Fletcher</li>
										</ul>
								  </div>
							  </div>
						  </div>
						</div>
					  
					  <div class="content" id="panel04">
					    <h3>Monthly Tax Update - Martyn Ingles</h3>
					    
					    <div class="row">
							  <div class="small-12 large-8 columns">
								  
					    <p>Content<br>
						    These webinars will give you all you need for an essential tax update in just one hour per month.  Gerry Hart covers all the vital tax issues and will update you thoroughly, with the emphasis firmly on the practical issues so that action can be taken at the right time.  </p>
						  <p>Key Topics<br>
							  The monthly webinars will cover development from the following core sources:</p>
						  </p>
						  <ul>
								 <li>New legislation</li>
								 <li>HMRC practice & guidelines</li>
								 <li>Case law</li>
								 <li>‘From the tax adviser’s desk’</li>
								 <li>New thinking and forward planning</li>
							 </ul>
							 
							 <p>Who should attend?<br>
							These webinars are aimed at all fee earners who want to offer practical advice and ensure you are fully up to date with the latest legislation.</p>
							
							<a href="" class="gen-btn icon acca check">Book Now</a>
							
							</div>
							  <div class="small-12 large-4 columns">
								  <div class="web-info">
										<h5>Date</h5>
										
										<ul>
											<li>23 Feb 15</li>
											<li>23 Mar 15</li>
											<li>20 Apr 15</li>
											<li>18 May 15</li>
											<li>22 Jun 15</li>
											<li>20 Jul 15</li>
											<li>14 Sep 15</li>
											<li>19 Oct 15</li>
											<li>16 Nov 15</li>
											<li>11 Dec 15</li>
										</ul>
										
										<h5>Time</h5>
										
										<ul>
											<li>10:00 to 11:00</li>
										</ul>
										
										<h5>Cost:</h5>
										
										<ul>
											<li>£34 plus VAT</li>
										</ul>
								  </div>
							  </div>
						  </div>
						</div>
						
						<div class="content" id="panel05">
					    <h3>Practice Management & Development - Gordon Gilchrist</h3>
					    
					    <div class="row">
							  <div class="small-12 large-8 columns">
								  
					    <p>The world of marketing, like everything, seems to be changing ever faster and as we move our businesses forward into 2014, we can all agree that the need to focus on Practice Development is even stronger, trading out of some difficult conditions. It's not just about generating leads nowadays, it's about generating quality leads and being able to convert these leads into sales.</p>
					    
					    <p>During this series of four webinars, Gordon will bring you the latest trends, successful ideas and opportunities from firms around the world to motivate and inspire you.</p>
						 <ol><li style="text-align: justify"><strong>New services that clients are welcoming</strong>.<br />&nbsp;</li><li style="text-align: justify"><strong>The latest benchmarking:</strong><br />a. &nbsp;Financial<br />b. &nbsp;Client Surveys<br />c. &nbsp;Staff Research<br />d. &nbsp;IT<br />&nbsp;</li><li style="text-align: justify"><strong>Management succession issues.</strong><br />&nbsp;</li><li style="text-align: justify"><strong>Maximising the Lifetime Value of your Clients:</strong><br />a. &nbsp;Cross Selling<br />b. &nbsp;Up Selling&nbsp;<br />c. &nbsp;Client Referrals<br />&nbsp;</li><li style="text-align: justify"><strong>Technology</strong><br />a. &nbsp;Workflow Development<br />b. &nbsp;Client Portals<br />c. &nbsp;Social Media<br />&nbsp;</li><li style="text-align: justify"><strong>Marketing</strong><br />a. &nbsp;Niche development<br />b. &nbsp;Focusing marketing efforts<br />c. &nbsp;Materials and websites<br />d. &nbsp;Direct and indirect marketing<br />e. &nbsp;Networking for the impatient<br />f. &nbsp; Resources - how much ?<br />&nbsp;</li><li style="text-align: justify"><strong>Selling for accountants - it can be done!</strong><br />a. &nbsp;Things you need to know before you can sell successfully<br />b. &nbsp;The process of successful selling<br />c. &nbsp;Value Pricing - one way to really make money<br />&nbsp;</li><li style="text-align: justify"><strong>Maximising Sales</strong><br />a. &nbsp;Chargeable hours<br />b. &nbsp;Charge out rates<br />c. &nbsp;Recoveries<br />&nbsp;</li><li style="text-align: justify"><strong>Managing WIP and Debtors</strong><br />a. &nbsp;Different billing cycles<br />b. &nbsp;Turnaround<br />c. &nbsp;Various Payment Options<br />&nbsp;</li><li style="text-align: justify"><strong>Keeping jobs on track and on budget</strong><br />&nbsp;</li><li style="text-align: justify"><strong>Overcoming price resistance</strong><br />&nbsp;</li></ol>
						 
						 <a href="" class="gen-btn icon acca check">Book Now</a>
						 
						 </div>
							  <div class="small-12 large-4 columns">
								  <div class="web-info">
										<h5>Date</h5>
										
										<ul>
											<li>21 Nov 14</li>
											<li>02 Mar 15</li>
											<li>01 Jun 15</li>
											<li>07 Sep 15</li>
											<li>23 Nov 15</li>
										</ul>
										
										<h5>Time</h5>
										
										<ul>
											<li>All 10:00 to 12:00</li>
										</ul>
										
										<h5>Cost:</h5>
										
										<ul>
											<li>£44 plus VAT</li>
										</ul>
								  </div>
							  </div>
						  </div>
						</div>
						
						<div class="content" id="panel06">
					    <h3>Professional Development Workshop - Gordon Gilchrist</h3>
					    
					    <div class="row">
							  <div class="small-12 large-8 columns">
								  
					    <h4>Nurturing Your Top Talent</h4>
					    <p>Many firms are thinking about retaining their best team members as their possible successors and whether they are equipped to take on all the tasks required of a partner today. This webinar series has the following key attributes:</p>
					    <ul>
								 <li>Consistently increase revenue and profits</li>
								 <li>Build self-confidence</li>
								 <li>Demonstrate commitment to younger professionals</li>
							 </ul>
							 
							 <p>Key Topics<br>
								 
								 This series is divided into 8 individual webinars as follows:</p>
							 
							 <ul><li style="text-align: justify"><strong>25 February 2015</strong><br />What Makes a Great Firm Tick<br />&nbsp;</li><li style="text-align: justify"><b>21 April 2015</b><br />Client Development to Improve Firm Profits<br />&nbsp;</li><li style="text-align: justify"><strong>19 May 2015</strong><br />Understanding Great Performance and Key Performance Target Setting<br />&nbsp;</li><li style="text-align: justify"><strong>19 June 2015</strong><br />Converting Leads into Clients - Personal Selling Skills<br />&nbsp;</li><li style="text-align: justify"><strong>21 September 2015</strong><br />Moving to a Leadership Position and Building a High Performance Team<br />&nbsp;</li><li style="text-align: justify"><strong>23 October 2015</strong><br />Taking Clients from &#39;Compliance&#39; to &#39;Added Value&#39; Services<br />&nbsp;</li><li style="text-align: justify"><strong>20 November 2015</strong><br />Successful Pricing Skills for Non-Compliance Services<br />&nbsp;</li><li style="text-align: justify"><strong>14 December 2015</strong><br />Action Planning for Your Personal Development&#8203;</li></ul>
							 
							 <a href="" class="gen-btn icon acca check">Book Now</a>
							 
							 </div>
							  <div class="small-12 large-4 columns">
								  <div class="web-info">
										<h5>Date</h5>
										
										<ul>
											<li>12 Dec 2014</li>
											<li>25 Feb 2015</li>
											<li>21 Apr 2015</li>
											<li>19 May 2015</li>
											<li>19 Jun 2015</li>
											<li>21 Sep 2015</li>
											<li>23 Oct 2015</li>
											<li>20 Nov 2015</li>
											<li>14 Dec 2015</li>
										</ul>
										
										<h5>Time</h5>
										
										<ul>
											<li>09:30 to 11:00</li>
										</ul>
										
										<h5>Cost:</h5>
										
										<ul>
											<li>£64 plus VAT</li>
										</ul>
								  </div>
							  </div>
						  </div>
						</div>
					</div>-->
	        
        </div>
     </div>
     
     <?php endwhile; else : ?>
	
<?php endif; ?>

<?php get_footer();
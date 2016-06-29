<?php

/*
 * Template Name: Dashboard - My Upcoming Events
 */

get_header(); ?>

		<div class="dash-wrap">

	    <div class="row collapse">

	        <div class="small-1 medium-1 columns" role="main" style="background: #000; color: #fff">
	            <?php get_sidebar('dashboard'); ?>
	        </div>
	        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	        <div class="small-11 medium-11 columns" role="main">

		        <div id="dash-main">

	            <h2><?php the_title(); ?></h2>

	            <?php
	            	if (isset($_GET['d'])) {
	            		$month = date('F',strtotime($_GET['d'].'-12'));
	            		$month_num = date('m',strtotime($_GET['d'].'-12'));
	            		$year = date('Y',strtotime($_GET['d'].'-12'));
	            		$first_day_of_month = date('N',strtotime($_GET['d'].'-01'));
	            		$number_of_days = date('t',strtotime($_GET['d'].'-12'));
	            		$next = date('Y-m',strtotime('+1 month',strtotime($_GET['d'].'-01')));
	            		$prev = date('Y-m',strtotime('-1 month',strtotime($_GET['d'].'-01')));

	            	}
	            	else {
	            		$month = date('F');
	            		$month_num = date('m');
	            		$first_day_of_month = date('N');
	            		$year = date('Y');
	            		$number_of_days = date('t');
	            		$next = date('Y-m',strtotime('+1 month'));
	            		$prev = date('Y-m',strtotime('-1 month'));
	            	}

	            	$current_user = wp_get_current_user();

	            ?>


	            <div class="calendar">
		            <div class="calHeader">
			           	<h1>
				           	<a href="#my-upcoming-events" data-uri="/dashboard/my-upcoming-events?d=<?php echo $prev;?>" class="dataload calArrow">&lt;</a>
				           	<?php echo $month.' '.$year;?>
				           	<a href="#my-upcoming-events" data-uri="/dashboard/my-upcoming-events?d=<?php echo $next;?>" class="dataload calArrow">&gt;</a>
				          </h1>
			          </div>

		            <div class="calDayHead">
			            <div class="calWeekDay">
				            <p>M</p>
			            </div>
			            <div class="calWeekDay">
				            <p>T</p>
			            </div>
			            <div class="calWeekDay">
				            <p>W</p>
			            </div>
			            <div class="calWeekDay">
				            <p>T</p>
			            </div>
			            <div class="calWeekDay">
				            <p>F</p>
			            </div>
			            <div class="calWeekDay">
				            <p>S</p>
			            </div>
			            <div class="calWeekDay">
				            <p>S</p>
			            </div>
			          </div>

			          <div class="calDay" data-equalizer>
			          	<?php

			          	$empty_day_counter = 1;
			          	$total_day_counter = 0;
			          	while ($empty_day_counter < $first_day_of_month ) :
			          		?>
			          		<div class="calDayBlock" data-equalizer-watch>
					          <p>&nbsp;</p>
				          </div>
			          		<?php
			          		$empty_day_counter++;
			          		$total_day_counter++;
			          	endwhile;

			          	$temp_events = get_assigned_events($current_user->ID,$month_num,$year);

			          	$events = array();

			          	foreach ($temp_events as $ev) {
			          		$d = explode('|',$ev->meta_value);
			          		$p = (int)str_replace('event_', '', $ev->meta_key);
			          		$events[$d[0]]['product_id'][] = $p;

			          	}


			          	for ($i=1; $i<=$number_of_days; $i++) :
			          		if ($i<10) { $i='0'.$i; }

			          	?>


				          			<?php if (isset($events[$year.'-'.$month_num.'-'.$i])) : ?>
				          				<div class="calDayBlock alert"  data-equalizer-watch>
						          		<p class="booked"><?php echo $i;?></p>
							          	<?php foreach ( $events[$year.'-'.$month_num.'-'.$i]['product_id'] as $event_id) : ?>
							          		<?php $product = new WC_Product($event_id); ?>
							          		<p class="booking"><a href="<?php echo get_permalink($event_id);?>" class="fancybox"><?php echo $product->post->post_title;?></a></p>
							          	<?php endforeach; ?>
							          </div>
						          	<?php else : ?>


						          		<?php
						          			$formatday = str_pad($i, 2, '0', STR_PAD_LEFT);
						          			$datetocheck =  $year.$month_num.$formatday;
					          				$hasevent = get_todays_events($datetocheck);
					          				$eventtest = array_filter($hasevent);
						          			if(!empty($hasevent)) : ?>

							          			<div class="calDayBlock alert" data-equalizer-watch>
							          			<p><?php echo $i;?></p><span class="attention">!</span>
							          			<?php foreach ( $hasevent as $event_id) : ?>
								          				<?php $product = new WC_Product($event_id); ?>
								          				<p class="booking"><a href="<?php echo get_permalink($event_id);?>" class="fancybox"><?php echo $product->post->post_title;?></a></p>
							          			<?php endforeach; ?>
							          			</div>

							          		<?php else : ?>
							          			<div class="calDayBlock"  data-equalizer-watch>
						          					<p><?php echo $i;?></p>
						          				</div>
							          		<?php endif; ?>
						          	<?php endif; ?>
						          	<!-- <p class="attention">!</p> -->

			          		<?php
			          		$total_day_counter++;
			          	endfor;

			          	if (($total_day_counter%7) != 0) :

			          		while (($total_day_counter%7) != 0) :
								?>
								<div class="calDayBlock"  data-equalizer-watch>
					          		<p>&nbsp;</p>
				          		</div>
								<?php
								$total_day_counter++;
							endwhile;

			          	endif;

			          	?>

			          </div>
		          </div>

	            <div class="disclaimer">
		            <p><span class="booked">Booked</span> Denotes you are booked to attend</p>
		            <p><span class="att-prev"><span class="attention">!</span></span>Denotes a 2020 event that you are not yet booked to attend</p>
	            </div>

		        </div>

	        </div>
	        <?php endwhile; ?>
	        <?php endif; ?>
	    </div>

		</div>

<?php get_footer();
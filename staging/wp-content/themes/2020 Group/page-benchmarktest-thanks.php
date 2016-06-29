<?php

/*
 * Template Name: Benchmark Tool Thanks
 */

  get_header();

  if(isset($_GET)) :

    $eq_partners = (int)$_GET['1'];
    $turnover = (int)(str_replace(',', '', $_GET['2']));
    $turnoverca = (int)(str_replace(',', '', $_GET['3']));
    $chargeable = (int)(str_replace(',', '', $_GET['12']));
    $chargeablefee = (int)(str_replace(',', '', $_GET['4']));
    $chargemulti = (float)$_GET['5'];
    $recover = (int)$_GET['6'];
    $lockup = (int)(str_replace(',', '', $_GET['7']));
    $lockupdebt = (int)(str_replace(',', '', $_GET['8']));
    $yearend = str_replace(' Days', '', $_GET['9']);
    $tax = str_replace(' Weeks', '', $_GET['10']);
    $company = $_GET['11'];

  endif;

   ?>

	<div class="row">

		<div class="small-12 columns benchmark-practice-check benchmark-practice-check-thanks" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

        <header class="article-header">
					<h1><?php the_title(); ?></h1>
					<p>A link to your benchmark report has been emailed to you. You can see your benchmark report below:</p>
        </header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

					<table cellpadding="0" cellspacing="0" class="benchmark-results">
  					<thead>
    					<tr>
      					<th class="benchmark-point"></th>
                <th class="benchmark-upper center">Upper Quartile Benchmark</th>
      					<th class="benchmark-company center"><?php if($company) : echo $company; else: echo ''; endif; ?></th>
    					</tr>
  					</thead>
  					<tbody>
    					<tr>
      					<td><span>1.</span> Turnover per equity partner</td>
      					<td class="center">&pound;535,000</td>
      					<td class="center">&pound;<?php echo number_format($turnover/$eq_partners); ?></td>
    					</tr>
    					<tr>
      					<td><span>2.</span> Turnover for compliance/assurances services as a percentage of total turnover</td>
      					<td class="center">58%</td>
      					<td class="center"><?php echo number_format(($turnoverca/$turnover)*100); ?>%</td>
    					</tr>
    					<tr>
      					<td><span>3.</span> Average chargeable hours for equity partners</td>
      					<td class="center">850 Hrs</td>
      					<td class="center"><?php echo $chargeable; ?> Hrs</td>
    					</tr>
    					<tr>
      					<td><span>4.</span> Average chargeable hours for fee earners</td>
      					<td class="center">1480 Hrs</td>
      					<td class="center"><?php echo $chargeablefee; ?> Hrs</td>
    					</tr>
    					<tr>
      					<td><span>5.</span> Charge out rate multiplier</td>
      					<td class="center">0.0029</td>
      					<td class="center"><?php echo $chargemulti; ?>%</td>
    					</tr>
    					<tr>
      					<td><span>6.</span> Recovery Rates</td>
      					<td class="center">92%</td>
      					<td class="center"><?php echo $recover; ?>%</td>
    					</tr>
    					<tr>
      					<td><span>7.</span> Lockup - number of days in WIP</td>
      					<td class="center">29</td>
      					<td class="center"><?php echo $lockup; ?></td>
    					</tr>
    					<tr>
      					<td><span>8.</span> Lockup - number of days in Debtors</td>
      					<td class="center">62</td>
      					<td class="center"><?php echo $lockupdebt; ?></td>
    					</tr>
    					<tr>
      					<td><span>9.</span> Turnaround time for Year-end Accounts in weeks</td>
      					<td class="center">4 weeks</td>
      					<td class="center"><?php echo $yearend; ?></td>
    					</tr>
    					<tr>
      					<td><span>10.</span> Turnaround time for Tax Returns in weeks</td>
      					<td class="center">1 week</td>
      					<td class="center"><?php echo $tax; ?></td>
    					</tr>
  					</tbody>
					</table>

          <br />

          <?php if ( !is_user_logged_in() ) : ?>

          <div class="cta-block side-block text-block texturebg">
            <div class="row">
              <div class="small-12 medium-8 columns">
                <h2>Grow your practice with 2020</h2>
                <p>Join 1000's of other accountancy professionals. Benefit from our wealth of knowledge, tools, tips and downloads now.</p>
              </div>
              <div class="small-12 medium-4 columns">
                <a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn orange icon trophy">Join Now</a> Already a member? <a href="/login?ref=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Login now</a>
              </div>
            </div>
          </div>

          <?php endif; ?>


				</section>

			</article>

			<?php endwhile; else : ?>

			<article class="post-not-found">

				<header class="not-found-header">

					<h2><?php _e( 'Nothing Found!' ); ?></h2>

				</header>

				<section class="not-found-content">

					<p><?php _e( 'Please check what you are looking for.' ); ?></p>

				</section>

			</article>

			<?php endif; ?>

		</div>

	</div>

<?php get_footer(); ?>

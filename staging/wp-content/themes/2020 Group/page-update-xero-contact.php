<?php /* Template Name: Update Xero Contacts */ ?>

<?php get_header(); ?>

	<div class="row">

		<div class="small-12 large-8 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<?php
/*

        post_contacts_to_xero();


				if(!current_user_can( 'manage_options' )) {

					include ('includes/class-xero-contacts.php');
					$total_users = get_updated_users(TRUE);
					$batch_limit = 50;
					for ($i = 0; $i < $total_users; $i += $batch_limit) {
						$users_contact_info = get_contact_details();

						//convert multi array to XML
	          			// die(var_dump($users_contact_info));
						$contacts_xml = convertArrayToXML($users_contact_info, 'Contacts', 'Contact');
						//pass to API class
						$xeroAPI = new Xero_Update_Contact_Details();
						$xeroAPI->updateContacts($contacts_xml);
					}
				}
				exit;*/
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

				<header class="article-header">

					<h1 itemprop="headline"><?php the_title(); ?><h1>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

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

		<div class="small-12 large-4 columns">

			<?php get_sidebar(); ?>

		</div>

	</div>

<?php get_footer(); ?>

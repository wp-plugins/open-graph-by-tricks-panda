<?php
/*
Plugin Name: Open Graph
Plugin URI: http://www.trickspanda.com
Description: Easily add Open Graph tags to your WordPress website to make it ready to share on Facebook.
Version: 1.1
Author: Hardeep Asrani
Author URI: http://www.hardeepasrani.com
License: GPL v2 or later
*/

function tp_og_facebook($contactmethods)
{
	if (!isset($contactmethods['facebook'])) $contactmethods['facebook'] = 'Facebook';
	return $contactmethods;
}

add_filter('user_contactmethods', 'tp_og_facebook', 10, 1);

function tp_get_tax_data($data)
{
	if (!$data) return;
	global $wp_query;
	$term = $wp_query->get_queried_object();
	if ($data == 'title') return $term->name;
	if ($data == 'description') return strip_tags($term->description);
	if ($data == 'link') {
		$link = get_term_link($term);
		return $link;
	}
}


function tricks_panda_og()
{
	$default_image = get_option('tricks_panda_og_default');
	$site_type = get_option('tricks_panda_og_site_type');
	$fb_page = get_option('tricks_panda_og_fb_page');
	$app_id = get_option('tricks_panda_og_app_id');
	$home_desc = get_option('tricks_panda_og_home_desc');
	$og_locale = '<meta property="og:locale" content="' . get_locale() . '" />' . "\n";
	$og_site_name = '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />' . "\n";
	if (is_home() || is_front_page()) {
		$og_title = '<meta property="og:title" content="' . get_bloginfo('name') . '" />' . "\n";
		if (is_home() && is_front_page()) {
			$og_url = '<meta property="og:url" content="' . get_bloginfo('url') . '" />' . "\n";
		}
		elseif (is_home()) {
			$og_url = '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
		}
		$og_type = '<meta property="og:type" content="' . $site_type . '" />' . "\n";
		if (!empty($home_desc)) {
			$og_description = '<meta property="og:description" content="' . $home_desc . '" />' . "\n";
		}
		else {
			$og_description = '<meta property="og:description" content="' . get_bloginfo('description') . '" />' . "\n";
		}
		$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
	}

	if (is_singular()) {
		global $post;
		setup_postdata($post);
		$og_title = '<meta property="og:title" content="' . get_the_title() . '" />' . "\n";
		$og_url = '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
		if (is_front_page()) {
			$og_type = '<meta property="og:type" content="' . $site_type . '" />' . "\n";
		}
		else {
			$og_type = '<meta property="og:type" content="article" />' . "\n";
		}

		if (is_front_page()) {
			if (!empty($home_desc)) {
				$og_description = '<meta property="og:description" content="' . $home_desc . '" />' . "\n";
			}
			elseif (class_exists('WPSEO_Frontend')) {
				$tp_yoast = new WPSEO_Frontend();
				$tp_yoast_description = $tp_yoast->metadesc(false);
				$og_description = '<meta property="og:description" content="' . $tp_yoast_description . '" />' . "\n";
			}
			elseif (has_excerpt()) {
				$og_description = '<meta property="og:description" content="' . get_the_excerpt() . '" />' . "\n";
			}
			else {
				$og_description = '<meta property="og:description" content="' . get_bloginfo('description') . '" />' . "\n";
			}
		}
		else {
			if (class_exists('WPSEO_Frontend')) {
				$tp_yoast = new WPSEO_Frontend();
				$tp_yoast_description = $tp_yoast->metadesc(false);
				$og_description = '<meta property="og:description" content="' . $tp_yoast_description . '" />' . "\n";
			}
			else {
				$og_description = '<meta property="og:description" content="' . get_the_excerpt() . '" />' . "\n";
			}
		}

		if (has_post_thumbnail()) {
			$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , 'medium');
			$og_image = '<meta property="og:image" content="' . $imgsrc[0] . '" />' . "\n";
		}
		else {
			$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
		}

		if (is_front_page()) {
		}
		else {
			if (get_the_author_meta('facebook')) {
				$og_author = '<meta property="article:author" content="' . get_the_author_meta('facebook', $post->post_author) . '" />' . "\n";
			}

			$og_published = '<meta property="article:published_time" content="' . get_post_time('c', true) . '" />' . "\n";
			if (get_the_modified_time() != get_the_time()) {
				$og_modified = '<meta property="article:modified_time" content="' . get_post_modified_time('c', true) . '" />' . "\n";
				$og_updated = '<meta property="og:updated_time" content="' . get_post_modified_time('c', true) . '" />' . "\n";
			}
		}
	}

	if (is_category() || is_tag() || is_tax()) {
		$tp_category_id = get_query_var('cat');
		$tp_category_url = get_category_link($tp_category_id);
		$tp_tag_id = get_query_var('tag_id');
		$tp_tag_url = get_tag_link($tp_tag_id);
		$the_tax = tp_get_tax_data('link');
		$tp_description = trim(strip_tags(term_description()));
		$og_title = '<meta property="og:title" content="' . single_cat_title('', false) . ' - ' . get_bloginfo('name') . '" />' . "\n";
		if (is_category()) {
			$og_url = '<meta property="og:url" content="' . $tp_category_url . '" />' . "\n";
		}
		elseif (is_tag()) {
			$og_url = '<meta property="og:url" content="' . $tp_tag_url . '" />' . "\n";
		}
		elseif (is_tax()) {
			$og_url = '<meta property="og:url" content="' . $the_tax . '" />' . "\n";
		}

		$og_type = '<meta property="og:type" content="object" />' . "\n";
		if (!empty($tp_description)) {
			$tp_description = trim(strip_tags(term_description()));
			$og_description = '<meta property="og:description" content="' . $tp_description . '" />' . "\n";
		}
		elseif (!empty($home_desc)) {
			$og_description = '<meta property="og:description" content="' . $home_desc . '" />' . "\n";
		}
		else {
			$og_description = '<meta property="og:description" content="' . get_bloginfo('description') . '" />' . "\n";
		}

		$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
	}

	if (is_author()) {
		$author_id = $post->post_author;
		$og_title = '<meta property="og:title" content="' . get_the_author() . ' - ' . get_bloginfo('name') . '" />' . "\n";
		$og_url = '<meta property="og:url" content="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '" />' . "\n";
		$og_type = '<meta property="og:type" content="object" />' . "\n";
		if (get_the_author_meta('description')) {
			$og_description = '<meta property="og:description" content="' . get_the_author_meta('description', $author_id) . '" />' . "\n";
		}
		elseif (!empty($home_desc)) {
			$og_description = '<meta property="og:description" content="' . $home_desc . '" />' . "\n";
		}
		else {
			$og_description = '<meta property="og:description" content="' . get_bloginfo('description') . '" />' . "\n";
		}

		$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
	}

	if (is_day() || is_month() || is_year()) {
		$archive_year = get_the_time('Y');
		$archive_month = get_the_time('m');
		$archive_day = get_the_time('d');
		if (is_day()) {
			$og_title = '<meta property="og:title" content="Daily Archives: ' . get_the_date() . ' - ' . get_bloginfo('name') . '" />' . "\n";
		}
		elseif (is_month()) {
			$og_title = '<meta property="og:title" content="Monthly Archives: ' . get_the_date(_x('F Y', 'monthly archives date format')) . ' - ' . get_bloginfo('name') . '" />' . "\n";
		}
		elseif (is_year()) {
			$og_title = '<meta property="og:title" content="Yearly Archives: ' . get_the_date(_x('Y', 'yearly archives date format')) . ' - ' . get_bloginfo('name') . '" />' . "\n";
		}
		else {
			$og_title = '<meta property="og:title" content="Blog Archives - ' . get_bloginfo('name') . '" />' . "\n";
		}

		if (is_day()) {
			$og_url = '<meta property="og:url" content="' . get_day_link($archive_year, $archive_month, $archive_day) . '" />' . "\n";
		}
		elseif (is_month()) {
			$og_url = '<meta property="og:url" content="' . get_month_link($archive_year, $archive_month) . '" />' . "\n";
		}
		elseif (is_year()) {
			$og_url = '<meta property="og:url" content="' . get_year_link($archive_year) . '" />' . "\n";
		}

		$og_type = '<meta property="og:type" content="object" />' . "\n";
		$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
	}

	if (is_search()) {
		$og_title = '<meta property="og:title" content="Searching for ' . get_search_query() . ' - ' . get_bloginfo('name') . '" />' . "\n";
		$og_url = '<meta property="og:url" content="' . get_search_link($query) . '" />' . "\n";
		$og_type = '<meta property="og:type" content="object" />' . "\n";
		$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
	}

	if (is_404()) {
		$og_title = '<meta property="og:title" content="Page not found - ' . get_bloginfo('name') . '" />' . "\n";
		$og_type = '<meta property="og:type" content="object" />' . "\n";
		$og_image = '<meta property="og:image" content="' . $default_image . '" />' . "\n";
	}

	if (!empty($fb_page)) {
		$og_page = '<meta property="article:publisher" content="' . $fb_page . '" />' . "\n";
	}

	if (!empty($app_id)) {
		$og_app = '<meta property="fb:app_id" content="' . $app_id . '" />';
	}

	echo "<!-- Start Open Graph Meta Tags - TricksPanda.com --> \n";
	echo $og_locale . $og_site_name . $og_title . $og_url . $og_type . $og_description . $og_image . $og_author . $og_published . $og_modified . $og_updated . $og_page . $og_app;
	echo "\n<!-- End Open Graph Meta Tags - TricksPanda.com --> \n";
}

add_action('wp_head', 'tricks_panda_og');
require_once ('plugin-options.php');

?>
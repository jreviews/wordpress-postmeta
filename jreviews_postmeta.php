<?php

/**
 * Plugin Name: JReviews Post Meta
 * Plugin URI: https://github.com/jreviews/wordpress-postmeta
 * Description: Allows retrieving JReviews field data via get_post_metadata
 * Version: 1.0.0
 * Author: ClickFWD LLC
 * Author URI: https://www.jreviews.com
 * License: MIT
*/

defined('ABSPATH') or die;

use JReviews\WordPress\JReviewsPostMeta\PostMeta;

if (! class_exists(PostMeta::class)) {
    require_once __DIR__ . '/src/PostMeta.php';
}

new PostMeta();
<?php
/**
 * Plugin Name: CyberTerrain Open Graph tags
 * Description: og:* and twitter:* tags so links shared on LinkedIn and elsewhere get a title, description and image.
 *              Image = the post's featured image, else the site banner. Steps aside if Jetpack or an SEO plugin prints its own.
 */

if (!defined('ABSPATH')) {
    exit;
}

const CT_OG_FALLBACK_IMAGE_ID = 46; // cyberterrain-banner-1.png

add_action('wp_head', 'ct_open_graph_tags', 5);

function ct_open_graph_tags() {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || has_action('wp_head', 'jetpack_og_tags')) {
        return;
    }

    $site  = get_bloginfo('name');
    $type  = 'website';
    $title = $site;
    $desc  = get_bloginfo('description');
    $url   = home_url('/');
    $image = null;

    if (is_singular()) {
        $post  = get_queried_object();
        $title = get_the_title($post);
        $url   = get_permalink($post);
        $type  = $post->post_type === 'post' ? 'article' : 'website';
        $desc  = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 40, '…');
        if (has_post_thumbnail($post)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'full');
        }
    } elseif (is_home()) {
        $title = 'Blog · ' . $site;
        $url   = get_permalink(get_option('page_for_posts')) ?: $url;
    }

    if (!$image) {
        $image = wp_get_attachment_image_src(CT_OG_FALLBACK_IMAGE_ID, 'full');
    }

    $tags = [
        'og:site_name'   => $site,
        'og:type'        => $type,
        'og:title'       => $title,
        'og:description' => $desc,
        'og:url'         => $url,
    ];
    if ($image) {
        $tags['og:image']        = $image[0];
        $tags['og:image:width']  = $image[1];
        $tags['og:image:height'] = $image[2];
    }

    echo "\n<!-- CyberTerrain Open Graph -->\n";
    foreach ($tags as $property => $content) {
        if ($content === '' || $content === null) {
            continue;
        }
        printf("<meta property=\"%s\" content=\"%s\" />\n", esc_attr($property), esc_attr($content));
    }
    printf("<meta name=\"twitter:card\" content=\"%s\" />\n", $image ? 'summary_large_image' : 'summary');
    printf("<meta name=\"twitter:title\" content=\"%s\" />\n", esc_attr($title));
    if ($desc) {
        printf("<meta name=\"twitter:description\" content=\"%s\" />\n", esc_attr($desc));
    }
    if ($image) {
        printf("<meta name=\"twitter:image\" content=\"%s\" />\n", esc_url($image[0]));
    }
}

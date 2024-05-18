<?php
/*
 * Plugin Name: Alt Tags for Gravatar
 * Description: Adds alt tags to Gravatar images for post authors and comments
 * Version: 1.4.9
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: Kent Riboe
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
*/

defined( 'ABSPATH' ) || exit;

/**
 * Function to add alt tags to Gravatar images for post authors and comments.
 *
 * @param string $avatar The HTML for the avatar image.
 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash, user email, WP_User object, WP_Post object, or WP_Comment object.
 * @param int $size Square avatar width and height in pixels to retrieve.
 * @param string $default URL for the default image or a default type. Accepts '404' (return a 404 instead of a default image), 'retro' (8bit), 'monsterid' (monster), 'wavatar' (cartoon face), 'indenticon' (the "quilt"), 'mystery', 'mm', or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF), or 'gravatar_default' (the Gravatar logo).
 * @param string $alt Alternative text to use in the avatar image tag. Passed to the 'get_avatar' filter.
 * @return string $avatar The HTML for the avatar image with the alt attribute added.
 */

function krgravatar_alt_tags_for_gravatar($avatar, $id_or_email, $size, $default, $alt) {
    // If alt is not empty, return the avatar as is.
    if (!empty($alt)) {
        return $avatar;
    }

    $user = false;

    // Determine the type of $id_or_email and get the user data accordingly.
    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', (int) $id_or_email);
    } elseif ($id_or_email instanceof WP_Comment) {
        // When the input is a comment object
        if (!empty($id_or_email->user_id) && $id_or_email->user_id != 0) {
            // If the comment was made by a registered user
            $user = get_user_by('id', (int) $id_or_email->user_id);
        } else {
            // If the comment was made by an unregistered user
            $alt = $id_or_email->comment_author;
        }
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $user = get_user_by('id', (int) $id_or_email->user_id);
        }
    } else {
        $user = get_user_by('email', $id_or_email);
    }

    // If a user was found, set the alt to the user's display name.
    if ($user && is_object($user)) {
        $alt = $user->display_name;
    }

    // If alt is not empty, replace the alt attribute in the avatar with the new alt.
    if (!empty($alt)) {
        $avatar = preg_replace('/alt=([\'"]).*?\\1/', 'alt="' . esc_attr($alt) . '"', $avatar);
    }

    //error_log('Final Avatar Output: ' . $avatar);
    return $avatar;
}
 
// Set the priority high to override any other plugins/themes that might be modifying the avatar.
add_filter('get_avatar', 'krgravatar_alt_tags_for_gravatar', 999, 5);
?>

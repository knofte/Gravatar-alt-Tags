<?php
/*
Plugin Name: Gravatar Alt Tags for Post Author
Description: Adds alt tags to Gravatar images for post authors.
Version: 1.3
Author: Kent Riboe
*/

function add_gravatar_alt_tags($avatar, $id_or_email, $size, $default, $alt) {
    //error_log('Gravatar Function Called. ID or Email: ' . print_r($id_or_email, true) . ' Alt: ' . $alt);
    if (!empty($alt)) {
        return $avatar;
    }

    $user = false;

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

    if ($user && is_object($user)) {
        $alt = $user->display_name;
    }

    if (!empty($alt)) {
        $avatar = preg_replace('/alt=([\'"]).*?\\1/', 'alt="' . esc_attr($alt) . '"', $avatar);
    }

    //error_log('Final Avatar Output: ' . $avatar);
    return $avatar;
}
 
// Set the priority high to override any other plugins/themes that might be modifying the avatar.
add_filter('get_avatar', 'add_gravatar_alt_tags', 999, 5);
?>

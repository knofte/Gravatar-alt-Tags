<?php
/*
Plugin Name: Gravatar Alt Tags for Post Author
Description: Adds alt tags to Gravatar images for post authors.
Version: 1.2
Author: Kent Riboe
*/

function add_gravatar_alt_tags($avatar, $id_or_email, $size, $default, $alt) {
    // If alt is already set, we don't want to overwrite it.
    if (!empty($alt)) {
        return $avatar;
    }

    $user = false;

    if (is_numeric($id_or_email)) {
        $id = (int) $id_or_email;
        $user = get_user_by('id', $id);
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by('id', $id);
        }
    } else {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user && is_object($user)) {
        $alt = $user->display_name;
        // Use regex to replace the alt attribute.
        // This regex accounts for alt attributes with no value (alt="") or with some value (alt="some value")
        // It also handles single and double quotes interchangeably.
        $avatar = preg_replace('/alt=([\'"]).*?\\1/', 'alt="' . esc_attr($alt) . '"', $avatar);
    }

    return $avatar;
}

// Set the priority high to override any other plugins/themes that might be modifying the avatar.
add_filter('get_avatar', 'add_gravatar_alt_tags', 999, 5);
?>

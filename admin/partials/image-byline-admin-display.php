<?php

/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://www.devbackroom.com/
* @since      1.0.0
*
* @package    Image_Byline
* @subpackage Image_Byline/admin/partials
*/
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<form action='options.php' method='post'>

    <h1><?php esc_html_e( 'Image and Media Byline Credits', 'image-byline' ); ?></h1>

    <?php
    settings_fields( 'imageByline_group' );
    do_settings_sections( 'imageByline_group' );
    submit_button();
    ?>

</form>

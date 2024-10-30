<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$options = get_option( 'iconicr_settings' );
$keep_option = $options['on_uninstall'];
/** remembering keep options **/
/*
    0:  Keep all data of rating, as well as option settings
    1:  Keep only the options settings
    2:  Keep only the data of rating (averages & votes)
    3:  Delete all, rating data and settings
*/

if ($keep_option == '0') return;

global $wpdb;

/* Delete post metas */
if ($keep_option != '2')
$wpdb->query("
    DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '%iconicr_%'
");

/* Delete option settings */
if ($keep_option != '1')
    delete_option( 'iconicr_settings' );
?>

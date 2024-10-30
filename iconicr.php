<?php
/**
 * Plugin Name: Iconic Rating
 * Description: Rate any type of post with stars and other awesome icons, adding some effects on hover (and tooltips).
 * Version: 1.0.0
 * Author: Ernesto Ortiz
 * Author URI:
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: iconicr
 * Domain Path: languages
 */

// load plugin text domain
function iconicr_init() {
    load_plugin_textdomain( 'iconicr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'iconicr_init');


/** Enqueue styles & scripts **/
add_action('admin_enqueue_scripts', 'iconicr_backend_scripts');
add_action('wp_enqueue_scripts', 'iconicr_frontend_scripts');
function iconicr_frontend_scripts() {
    if(is_admin() || !is_singular()) return;
    global $post;
    //return if not in desired post types or taxs
    if (!iconicr_in()) return false;
    //font awesome
    wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
    //if hover effect selected
    $default = array('hover_class'=>'none');
    $options = wp_parse_args(get_option('iconicr_settings'), $default);
    $hover_class = $options['hover_class'];
    if ($hover_class !='none')
        wp_enqueue_style('iconicr_hvr_style', plugins_url('/css/hover2d-min.css',__FILE__));
    //style
    wp_enqueue_style('iconicr_style', plugins_url('/css/style.css',__FILE__));
    //scripts
    wp_register_script( 'iconicr_js', plugins_url('/js/iconicr.js',__FILE__), array('jquery'));
    wp_enqueue_script('iconicr_js');
}
function iconicr_backend_scripts() {
    if(!is_admin()) return;
    wp_enqueue_style('iconicr_admin_style', plugins_url('/css/admin_style.css',__FILE__));
    wp_register_script( 'backend_js', plugins_url('/js/backend.js',__FILE__), array('jquery'));
    wp_enqueue_script('backend_js');
}


/** AJAX FUNCTIONS **/
include "ajaxes.php";

/** OPTIONS PAGE **/
if (is_admin()) include "optionspage.php";
/* DEFAULT option values */
function iconicr_get_options(){
    //$options = get_option('iconicr_settings');
    $defaults = array(
        'numstars' => 5,
        'out_fa' => "circle-o",
        'in_fa' => "circle",
        'use_opacity' => true,
        'min_opacity' => 0.2,
        'rating_on'=>'post', //post types, separated by commas
        'rating_cats'=>'', //cats sep. by , (empty means 'any')
        'hover_class'=>'none',
        'rspeech_0' => "(%k%) ".__('I hated it', 'iconicr'),
        'rspeech_1' => "(%k%) ".__('I didnt like it', 'iconicr'),
        'rspeech_2' => "(%k%) ".__('It was OK', 'iconicr'),
        'rspeech_3' => "(%k%) ".__('I liked it', 'iconicr'),
        'rspeech_4' => "(%k%) ".__('I loved it', 'iconicr'),
        'nspl_votes' => __('Please, vote', 'iconicr'). " * " .__('One lonely vote', 'iconicr'). " * %v% ".__('votes', 'iconicr'),
        'rating_div' => '',
        'r_at_end' => '1',
        'txt_avg' => '%avg% (%tv% %not%)',
        'txt_novote' => __('/ One person, one vote', 'iconicr')
    );
    $options = wp_parse_args(get_option('iconicr_settings'), $defaults);
    return $options;
}

/** SHORTCODES
if (!is_admin()) include "shortcodes.php"; **/

/** other FUNCTIONS **/
function iconicr_in(){
    $default=array(
        'rating_on'=>'post', //post types, separated by commas
        'rating_cats'=>'', //cats sep. by , (empty means 'any')
        );
    $options = wp_parse_args(get_option('iconicr_settings'), $default);
    $rating_on = $options['rating_on'];
    $rating_cats = $options['rating_cats'];
    //return if not in desired post types
    if (!empty($rating_on) && !s_contains_word_w($rating_on,get_post_type()))
        return false;
    //return if not in desired categs
    if (!empty($rating_cats) && !tax_contains_term_z("z=".$rating_cats))
        return false;
    return true;
}

if (!function_exists('tax_contains_term_z')) {
    function tax_contains_term_z($args){
        global $post;
        if (!$post) return false;
        $parse = wp_parse_args($args, array(
            //tax--> "tags", "cats", post_tag, category, or any custom taxonomy...
            'id'      => $post->ID,
            'type'    => 'slug', //or name
            'tax'     => '', //empty means "all"
            'z'       => '', //terms, separated by commas
        ));
        //Must be any term
        $z = $parse['z']; //slug terms separated by commas
        if (empty($z)) return false;
        $z = explode(",", $z);
        $id = $parse['id'];
        $tax = $parse['tax'];
        $taxs = array();
        $likecats = array();
        $liketags = array();
        if (empty($tag) || $tax == "tags" || $tax == "cats"){
            $taxs = get_post_taxonomies( );
            foreach ($taxs as $t)
                if ($tax=="cats" && is_taxonomy_hierarchical($t))
                    $likecats[]=$t;
                elseif ($tax=="tags" && !is_taxonomy_hierarchical($t))
                    $liketags[]=$t;
        }else{
            $taxs[] = $tax;
        }
        if (!empty($likecats)) $taxs = $likecats;
        if (!empty($liketags)) $taxs = $liketags;
        foreach($taxs as $t){
            $terms = get_the_terms( $id, $t );
            $sn_terms = array();
            if (empty($terms)) return false;
            for ($i=0;$i<count($terms);$i++){
                if ($parse['type']=='slug')
                    $term=$terms[$i]->slug;
                    else $term=$terms[$i]->name;
                if (in_array($term, $z)) break;
            }
            if (in_array($term, $z)) return true;
        }
        return false;
    }
}

if (!function_exists('s_contains_word_w')) {
    function s_contains_word_w($s,$w){
        $strpos = strpos($s, $w);
        $result = false;
        if ($strpos !== false) {
            $result = true;
            //if any char after or before, it should be ' ' or ','
            $slast = $strpos+strlen($w);
            if ($strpos > 0 && strlen($s) > $slast):
                $nchar = substr($s, $slast, 1);
                $pchar = substr($w, $strpos-1, 1);
                if (empty($nchar) || $nchar==",")
                    $result = true;
                elseif (empty($pchar) || $pchar=="," || $pchar==" ")
                    $result = true;
                else
                    $result = false;
            endif;
        }
        return $result;
    }
}
?>

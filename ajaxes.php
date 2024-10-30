<?php
/* ***************************** */
/*     HANDLE AJAX requests      */
/* ***************************** */

/*** AJAX for visitors and users ***/
add_action( 'wp_ajax_iconicr_reqs', 'iconicr_reqs_callback' );
add_action( 'wp_ajax_nopriv_iconicr_reqs', 'iconicr_reqs_callback' );

/*** HANDLER function ***/
function iconicr_reqs_callback() {
    //retrieve rating values related with this post
    $post_id =  $_POST['iconicr_id'];
    $echo ='';
    //switch ACTIONS
    switch ($_POST['todo']) {
        case 'iconicr_options':
            //get options
            $options = iconicr_get_options();
            $avg_rate = get_post_meta($post_id, 'iconicr_avg', true);
            $num_votes = get_post_meta($post_id, 'iconicr_votes', true);
            $star_votes = get_post_meta($post_id, 'iconicr_star_v', true);
            if(empty($num_votes)):
                $avg_rate=0;
                $num_votes=0;
                //create votes per star
                $default = array('numstars'=>5);
                $settings = wp_parse_args(get_option('iconicr_settings'), $default);
                $numstars= $settings['numstars'];
                $s_votes = array();
                for ($s=1; $s<=$numstars;$s++)
                    $s_votes[] = 0;
                $star_votes = implode(",",$s_votes);
                update_post_meta($post_id, 'iconicr_star_v', $star_votes);
            else:
                $avg_rate = floatval($avg_rate);
                $num_votes= intval($num_votes);
            endif;
            $options['avg_rate']=$avg_rate;
            $options['num_votes']=$num_votes;
            $options['star_votes']=$star_votes;
            //see if user can vote
            $can_vote = true;
            if (!count($_COOKIE))
                $can_vote = false; //if cookies not enabled
            if(isset($_COOKIE['iconicr_cookie'])) {
                $cookie = $_COOKIE['iconicr_cookie'];
                $ids = explode(",", $cookie);
                if (in_array($post_id, $ids)) $can_vote = false;
            }
            $options['can_vote'] = $can_vote;
            //(RECODE all later?)
            //At this moment ' must be use on options page
            //but not in javascript; so change ' by "
            $options = str_replace ( "'" , '"' , $options);
            //options to return
            $echo = json_encode($options);
        break;
        case 'iconicr_voted':
            //update post meta
            update_post_meta($post_id, 'iconicr_avg', $_POST['avg_rate']);
            update_post_meta($post_id, 'iconicr_votes', $_POST['num_votes']);
            //average per star
            $itarget = $_POST['itarget'];
            $star_votes = get_post_meta($post_id, 'iconicr_star_v', true);
            $s_votes = explode(",",$star_votes);
            $s_votes[$itarget-1]++;
            //calculate new average (after vote)
            $star_votes = implode(",", $s_votes); update_post_meta($post_id, 'iconicr_star_v', $star_votes);
        break;
    }
    //return values
    echo $echo;
   // endif;
    wp_die();
}



/*** WP Ajax Magic ***/
if(!function_exists('ajaxes_js')){
    function ajaxes_js() {
    ?>
    <script>
        var ajaxurl = <?php echo json_encode(admin_url("admin-ajax.php")); ?>;
        var ajaxnonce = <?php echo json_encode(wp_create_nonce("__ajax_nonce" )); ?>;
    </script>
    <?php
    }
}
// Add hook for admin <head></head>
//add_action('admin_head', 'ajaxes_js');
// Add hook for front-end <head></head>
add_action('wp_head', 'ajaxes_js');


/*** get post id ***/
add_action('wp_head', 'iconicr_id');
function iconicr_id(){
    global $post;
    if (!is_singular()) return;
    if (!iconicr_in()) return;
    ?>
        <script>
            var iconicr_id = <?php echo $post->ID; ?>;
        </script>
    <?php
}

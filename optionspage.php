<?php

/** Add plugin options page **/
add_action( 'admin_menu', 'iconicr_menu' );
function iconicr_menu() {
    // params. title, menu title, capability, slug and function
    add_options_page( 'Iconic Rating Plugin Options', 'Iconic Rating', 'manage_options', 'iconicr', 'iconicr_options' );
}

/** Set options form **/
function iconicr_options() {
    if ( !current_user_can( 'manage_options' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    ?>
    <div id = "optionspage" class="wrap">
        <h2><?php echo __('Iconic Rating Plugin Options', 'iconicr');?></h2>
        <br/>
        <form action="options.php" method="post">
            <?php
            settings_fields('iconicr_ffields');
            do_settings_sections('iconicr_sections');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/** Set form fields **/
add_action( 'admin_init', 'iconicr_setfields' );
function iconicr_setfields(  ) {
    register_setting( 'iconicr_ffields', 'iconicr_settings');
    /* sections */
    $section_titles = array(
        __( 'Font Awesome Icons', 'iconicr' ),
        __( 'Text on votes &amp; averages', 'iconicr' ),
        __( '2D Hover Transitions', 'iconicr' ),
        __( 'On Uninstall', 'iconicr' )
        );
    //settings section
    foreach ($section_titles as $k=>$section_title)
        add_settings_section(
            'iconicr_section_'.($k+1),
            $section_title,
            'iconicr_section_callback',
            'iconicr_sections'
        );
    //section callback
    function iconicr_section_callback(  ) {
        return false;
    }

    /* fields */
    $field_names = array(
        'fawicons',
        'averagr',
        'hoversr',
        'on_uninstall'
    );
    $field_titles = array(
        __('Where the icons appear and how they looks', 'iconicr'),
        __('The text for icons (in tooltip), votes and averages', 'iconicr'),
        __('Some behaviour thanks to CSS3 hover transitions', 'iconicr'),
        __('Choose what to do...', 'iconicr')
    );
    //setting fields
    foreach ($field_names as $k=>$field_name)
        add_settings_field(
            $field_name,
            $field_titles[$k],
            'iconicr_field_'.($k+1).'_callback',
            'iconicr_sections',
            'iconicr_section_'.($k+1)
        );
}


/** Draw fields' content **/
function iconicr_field_1_callback() {
    $options = iconicr_get_options();
    $numstars = $options['numstars'];
    $out_fa = $options['out_fa'];
    $in_fa = $options['in_fa'];
    $use_opacity = $options['use_opacity'];
    $min_opacity = $options['min_opacity'];
    if (empty($use_opacity)) $use_opacity="0";
    $rating_on   = $options['rating_on'];
    $rating_cats = $options['rating_cats'];
    $rating_div = $options['rating_div'];
    $r_at_end  = $options['r_at_end'];
    if (empty($r_at_end)) $r_at_end="0";
    ?>
    <h4><?php echo __('<em>Star</em> (or whatever Font Awsome)  Icons', 'iconicr');?></h4>
    <label><?php echo __('Number of icons', 'iconicr');?></label>:&nbsp;&nbsp;<input name='iconicr_settings[numstars]' type="number" min="1" value="<?php echo $numstars; ?>" />
    <p class="description"><?php echo __('This should be established at start and should remain unchanged, or the average could be misleading.', 'iconicr');?></p>
    <br />
    <label><?php echo __('Font Awesome icon', 'iconicr');?></label>:&nbsp;&nbsp;<input name='iconicr_settings[out_fa]' type="text" value="<?php echo $out_fa; ?>" />
    <label><?php echo __('Font Awesome icon when filled (or hovered)', 'iconicr');?></label>:&nbsp;&nbsp;<input name='iconicr_settings[in_fa]' type="text" value="<?php echo $in_fa; ?>" />
    <p class="description">Font Awesome <?php echo __('is the source for our iconic rating system; please visit its ', 'iconicr');?><a href="http://fontawesome.io/icons/" target="_blank">
    <?php echo __('complete set of icons', 'iconicr');?></a> <?php echo __('to select the appropriate ones for you.', 'iconicr');?></p>
    <br />
    <h4><?php echo __('Opacity for icons when not <em>filled</em> with integer values', 'iconicr');?></h4>
    <label><?php echo __('Minimal Opacity value', 'iconicr');?></label>:&nbsp;&nbsp;
    <input name='iconicr_settings[min_opacity]' type="number" min="0" max="1" step="0.05" value="<?php echo $min_opacity; ?>" />
    <br />
    <p class="description"><input type='checkbox' name='iconicr_settings[use_opacity]' <?php checked( $use_opacity, 1 ); ?> value='1'><?php echo __('Use the opacity to distinguish partial filled stars', 'iconicr');?></p>
    <br />
    <h4><?php echo __('Where to show the </em>stars</em> icons', 'iconicr');?></h4>
    <input name='iconicr_settings[rating_on]' type="text" value="<?php echo $rating_on; ?>" />
    <p class="description"><?php echo __('Write the post types where the icons will appear (separated by commas)', 'iconicr');?></p>
    <br />
    <input name='iconicr_settings[rating_cats]' type="text" value="<?php echo $rating_cats; ?>" />
    <p class="description"><?php echo __('And write the specific categories on such post types (separated by commas); if empty, it means every category.', 'iconicr');?></p>
    <br />
    <input name='iconicr_settings[rating_div]' type="text" value="<?php echo $rating_div; ?>" />
    <p class="description"><?php echo __('Write the DIV  <em>.class</em> or <em>#id</em> where it must appear; or...', 'iconicr');?></p>
    <p class="description"><input type='checkbox' name='iconicr_settings[r_at_end]' <?php checked( $r_at_end, 1 ); ?> value='1'><?php echo __('...try to put it at the end of the content (DIV has priority).', 'iconicr');?></p>
    <p class="description"><?php echo __('Of course, we can always have shortcodes... (which overrides the two positions above considered)', 'iconicr');?></p>
    <br />
    <?php
}

function iconicr_field_2_callback() {
    $options = iconicr_get_options();
    $rspeech  = array(
        $options['rspeech_0'], $options['rspeech_1'], $options['rspeech_2'], $options['rspeech_3'],  $options['rspeech_4']
    );
    $nspl_votes = $options['nspl_votes'];
    $txt_avg = $options['txt_avg'];
    $txt_novote  = $options['txt_novote'];
    ?>
    <h4><?php echo __('Text for <em>star</em> icons, votes &amp; averages', 'iconicr');?></h4>
    <label><?php echo __('Text for each <em>star</em> icons (on a tooltip, when over)', 'iconicr');?></label>
    <?php
    foreach($rspeech as $k=>$speech){?>
        <input name='iconicr_settings[rspeech_<?php echo $k;?>]' type="text" value="<?php echo $speech; ?>" />
    <?php } ?>
    <p class="description"><?php echo __('If there is more than 5 stars, the text of the first field will be chosen for all of them. The expression <em>%k%</em> in the text represents the value of each <em>star</em>; and <em>%kv%</em> represents the votes on each star.', 'iconicr');?></p>
    <br />
    <label><?php echo __('Texts for votes when there is none, there is only one, and there are more than one votes, separated by asterisks (*)', 'iconicr');?></label>
    <input name='iconicr_settings[nspl_votes]' type="text" value="<?php echo $nspl_votes; ?>" />
    <p class="description"><?php echo __('For example: <em>Please, vote * %v% vote * %v% votes</em>. The expression <em>%v%</em> in the text represents the number of votes.', 'iconicr');?></p>
    <br />
    <label><?php echo __('Text when already voted', 'iconicr');?></label>
    <input name='iconicr_settings[txt_novote]' type="text" value="<?php echo $txt_novote; ?>" />
    <p class="description"><?php echo __('For example: <em>/ One person, one vote</em>', 'iconicr');?></p>
    <br />
    <label><?php echo __('Text for displaying average', 'iconicr');?></label>
    <input name='iconicr_settings[txt_avg]' type="text" value="<?php echo $txt_avg; ?>" />
    <p class="description"><?php echo __('For example: <em>%avg% (%tv% %not%)</em>. The expression <em>%avg%</em> in the text represents the decimal average; the expression <em>%tv%</em> represents the text for zero, one or many votes; and the expression %not% represents the text when already voted (and it only appears on such cases).', 'iconicr');?></p>
    <br />
    <?php
}

function iconicr_field_3_callback() {
    $options = iconicr_get_options();
    $hover_class = $options['hover_class'];
    $hvrs = array('hvr-grow', 'hvr-shrink', 'hvr-pulse', 'hvr-pulse-grow', 'hvr-pulse-shrink', 'hvr-push', 'hvr-pop', 'hvr-bounce-in', 'hvr-bounce-out', 'hvr-rotate', 'hvr-grow-rotate', 'hvr-float', 'hvr-sink', 'hvr-bob', 'hvr-hang', 'hvr-skew', 'hvr-skew-forward', 'hvr-skew-backward', 'hvr-wobble-vertical', 'hvr-wobble-horizontal', 'hvr-wobble-to-bottom-right', 'hvr-wobble-to-top-right', 'hvr-wobble-top', 'hvr-wobble-bottom', 'hvr-wobble-skew', 'hvr-buzz', 'hvr-buzz-out');
    $hvr_options = array(__('Grow','iconicr'), __('Shrink','iconicr'), __('Pulse','iconicr'), __('Pulse Grow','iconicr'), __('Pulse Shrink','iconicr'), __('Push','iconicr'), __('Pop','iconicr'), __('Bounce In','iconicr'), __('Bounce Out','iconicr'), __('Rotate','iconicr'), __('Grow Rotate','iconicr'), __('Float','iconicr'), __('Sink','iconicr'), __('Bob','iconicr'), __('Hang','iconicr'), __('Skew','iconicr'), __('Skew Forward','iconicr'), __('Skew Backward','iconicr'), __('Wobble Vertical','iconicr'), __('Wobble Horizontal','iconicr'), __('Wobble to Bottom Right','iconicr'), __('Wobble to Top Right','iconicr'), __('Wobble Top','iconicr'), __('Wobble Bottom','iconicr'), __('Wobble Skew','iconicr'), __('Buzz','iconicr'), __('Buzz Out','iconicr'));
    ?>
    <label><?php echo __('Select one of the following hover transitions','iconicr');?></label>
    <br /><br />
    <select name='iconicr_settings[hover_class]'>
        <option value='none' <?php selected( $hover_class, 'none' ); ?>><?php echo __('No hover transition selected at this moment','iconicr');?></option>
        <?php
        foreach ($hvr_options as $key=>$hvr_option){ ?>
            <option value='<?php echo $hvrs[$key];?>' <?php selected( $hover_class, $hvrs[$key]); ?>><?php echo $hvr_option;?></option>
        <?php
        }
        ?>
    </select>
    <p class="description"><?php echo __( 'Credits for the CSS3 transitions goes to IanLunn, at ', 'iconicr' );?><a href="https://github.com/IanLunn/Hover/"  target="_blank">GitHub</a>.</p>
    <br />
<?php
}

function iconicr_field_4_callback() {
    //$options = get_option( 'iconicr_settings' );
    $options = iconicr_get_options();
    $value = $options['on_uninstall'];
    $keep_options = array(
        __('Keep all data of rating, as well as option settings', 'iconicr' ),
        __('Keep only the options settings', 'iconicr' ),
        __('Keep only the data of rating (averages & votes)', 'iconicr' ),
        __('Delete all, rating data and settings', 'iconicr' )
    )
    ?>
    <select name='iconicr_settings[on_uninstall]'>
        <?php
        foreach ($keep_options as $key=>$keep_option){ ?>
            <option value='<?php echo $key;?>' <?php selected( $value, $key ); ?>><?php echo $keep_option;?></option>
        <?php
        }
        ?>
    </select>
    <p class="description"><?php echo __( 'Choose what to do once uninstalled this plugin', 'iconicr' );?></p>
    <?php
}

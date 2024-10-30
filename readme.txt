=== Iconic Rating ===

Contributors: ernestortiz
Plugin URI: https://github.com/ernestortiz
Donate link:
Tags: 5 stars, dislike, five stars, rate, review, post review, custom post review, voting, rate product, rating, icon rating, rating platform, font awesome, tooltip, rating plugin, rating system, ratings system, ratings tool, ratings tools, ratings widget, review platform, review system, review tool, star rating, voting, votings system, wordpress rating, wp rating
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: 1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Review or rating any post type, with stars or other awesome icons, adding some effects on hover (and tooltips).


== Description ==

An iconic rating system. You can select in which type of post the review appears, where in a DIV, the number and look of the review stars (or whatever icon from font awesome) as well as its behaviour (some CSS3 transformation actions and a tooltip), etc.


== Installation ==

1. Upload unzipped plugin directory to the /wp-content/plugins/ directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.


== Frequently Asked Questions ==

= Why I get 5 circles instead of 5 stars? =

You can get almost what you want, if is provided by font awesome ;) As you can see (at http://fontawesome.io/icons/) the stars still exists! So, in the options page of the plugin, just erase "circle-o" and copy the name of the star icon ("star-o") instead.

Please, note that there are two fields for the font awesome's icons: the regular one, and the icon which replaces it when hovered or "filled" (once the user click on it, in order to rating). You can play for a while, changing those font awesome names as well as other settings on the options page of this plugin, to get what more fits your needs or desires. With the settings options and using some CSS styles, there are a lot of combinations for you to be happy and give this plugin a good rating ;).

= What if someone prefer a like rating with a lonely big pink heart?  =

Well, I hope the options page of this plugin is self-explanatory, and once you read it (maybe twice), you realize how to do that. Nope?... OK.

Let's use the font awesome icons named (haven't you guessed?) "heart-o"; and "heart", for the icon when hover or "filled" (or maybe "heartbeat" if you feel dramatic today). Of course, the option "number of icons" should be 1. And the resulting text will show only the number of votes, right? (Please, look at the screenshots). Last but not least, on "2d hover transitions" tab select a suitable transition ("pulse grow" maybe?). That's all in the options page.

But do not forget, at the CSS style, give a big size to the font and a pink color, or you just get an ordinary heart.

For example:

    .iconicr_wrap{
        width: 2em;
    }
    i.iconicr_in, i.iconicr_out {
        color:deeppink;
        font-size:2em;
        }
    .iconicr_avg {
        color:deeppink;
        margin-left: 2.4em;
        margin-top: -1.5em;
    }


= How to change the style of tooltip?  =

Please, refer to the style.css of the plugin, and you can use it as reference, in order to change the values to others you prefer.


= How to know the votes on each star?  =

Is good to know how many people think your item is good, and how many voted otherwise. Please, read on the options page that this could be known by using the <em>%kv%</em> expression (when hover on <em>k</em>-th star). IN next versions of the plugin this will be development a bit more.

(Options page shows other wildcard expressions that you can use...)


= Some HTML can be used? =

That is correct (as you can see here at the screenshot tab, at the last images). But always use single quotation marks instead of double quotations; beware of <em>"</em>. If you know what you're doing, it will be fun a little of HTML.


= And what if I did not find my question on this FAQs? =

Please, be welcome to ask whatever worries you.
(To be continued...)


== Screenshots ==

1. The iconic rating...
2. Playing with options...
3. Playing with options...
4. The result of play...
5. Playing with html...
6. You get this big 5stars...


== Donations ==

If you want to help me in writing more code or better poetry, please invite me to a beer (or coffee, maybe) by sending your thanks to my PayPal account (ernestortizcu at yahoo.es). Thanks in advance.


== Changelog ==

= 1.0.0 =
* Stable Release

jQuery(document).ready(function($){

    /**** AJAX request ****/
    $.ajax({
        url: ajaxurl,
        type:'POST',
        data: {
            action: 'iconicr_reqs',
            todo: 'iconicr_options',
            iconicr_id: iconicr_id,
        },
        success:function(results){
            iconicr_rating(JSON.parse(results));
        }
    });

    function iconicr_rating(obj){
        //default options
        var numstars = obj.numstars;
        var out_fa = obj.out_fa;
        var in_fa = obj.in_fa;
        var use_opacity = obj.use_opacity;
        var min_opacity = obj.min_opacity;
        var hover_class = obj.hover_class;
        var rspeech_0 = obj.rspeech_0;
        var rspeech_1 = obj.rspeech_1;
        var rspeech_2 = obj.rspeech_2;
        var rspeech_3 = obj.rspeech_3;
        var rspeech_4 = obj.rspeech_4;
        var avg_rate = parseFloat(obj.avg_rate);
        var num_votes = parseInt(obj.num_votes);
        var can_vote = obj.can_vote;
        var rating_div = obj.rating_div;
        var r_at_end = obj.r_at_end;
        var nspl_votes = obj.nspl_votes;
        var txt_avg  = obj.txt_avg;
        var txt_novote = obj.txt_novote;
        var star_votes = obj.star_votes;
        //classes
        var out_class = "fa fa-" + out_fa +" iconicr_out";
        var in_class = "fa fa-" + in_fa +" iconicr_in";
        if (hover_class != "none")
        in_class = in_class + " " + hover_class;

        //if there is no .iconic_wrap (it means not shortcode)
        //append it to a given .class or #id, or at the end
        //of the given POST or custom POST
        $r_dom = "<div class='iconicr_wrap'></div>";
        if ( !$(".iconicr_wrap").length ){
            if ( $(rating_div).length )
                $(rating_div).append($r_dom)
            else if(r_at_end){
                if($(".post-inner").length)
                    $(".post-inner").append ($r_dom);
                else
                    $(".post-"+iconicr_id).append ($r_dom);
            }
        }
        //draw icons
        var rspeech = [rspeech_0, rspeech_1, rspeech_2, rspeech_3, rspeech_4];
        var s_vote = star_votes.split(",");
        for (i=0; i< numstars; i++){
            var kstar = i+1;
            var speech = rspeech[i];
            if (numstars > 5) speech = rspeech[0];
            var title = iconicr_replace(speech);
            title = title.replace(/%kv%/gi, s_vote[i]);
            title = title.replace(/%k%/gi, kstar.toString());
            //that's all
            $(".iconicr_wrap")
                .append(
                    "<i class='' title='" + title + "' rel='tooltip'  data-val='" + (i+1) + "' data-vote='" + s_vote[i] + "' style=''></i>"
            );
        }
        $(".iconicr_wrap")
            .after("<div class='iconicr_avg'></div>");
        iconicr_set_rating();

        //set rating on stars
        function iconicr_set_rating(){
            for (i=0; i< numstars; i++){
                var iclass = out_class;
                if (avg_rate > i) iclass = in_class;
                //set opacity
                if (use_opacity){
                    var opacity = "opacity:1;";
                    var avg_floor = Math.floor(avg_rate);
                    var avg_decimal = parseFloat((avg_rate%1).toFixed(2));
                    if (avg_floor == i && avg_decimal)
                        if (avg_decimal >= min_opacity)
                            opacity = "opacity:" + avg_decimal + ";";
                        else
                            opacity = "opacity:" + min_opacity + ";";
                }
                //set icon
                $(".iconicr_wrap i:eq("+i+")")
                    .attr("style", opacity)
                    .attr("class", iclass);
            }

            //SHOW votes && averages
            if (txt_avg.trim()){
                if (can_vote) txt_novote = '';
                var _avg = txt_avg;
                var _replaced = iconicr_replace(_avg);
                $('.iconicr_avg')
                    .html(_replaced);
            }
        }

        //make replacements
        function iconicr_replace(_str){
            if (_str.includes("%tv%")){
                var vspeechs = nspl_votes.split("*");
                if (num_votes > 1)
                    txt_vote = vspeechs[2].trim();
                else txt_vote = vspeechs[num_votes].trim();
                if (txt_vote)
                    _str = _str.replace(/%tv%/gi,txt_vote);
            }
            if (_str.includes("%v%"))
                _str = _str.replace(/%v%/gi, num_votes.toString());
            if (_str.includes("%not%"))
                _str = _str.replace(/%not%/gi,txt_novote);
            if (_str.includes("%avg%"))
                _str = _str.replace(/%avg%/gi, avg_rate.toString());
            return _str;
        }

        //icons leave
        $('.iconicr_wrap').mouseleave(function() {
            iconicr_set_rating();
        });

        //icon hover
        $('.iconicr_wrap i').hover(function() {
            if (!can_vote) return;
            var itarget = this.getAttribute('data-val');
            var iclass = in_class;
            for (i=0; i< numstars; i++){
                if (i >= itarget) iclass = out_class;
                $(".iconicr_wrap i:eq("+i+")")
                    .attr("class", iclass)
                    .attr("style", "opacity:1;");
            }
            //$('.iconicr_avg').html(this.getAttribute('title'));
        });
        //icon click
        $('.iconicr_wrap i').click(function() {
            if (!can_vote) return;
            can_vote = false;
            //general average
            if (numstars>1){
                var itarget = parseInt(this.getAttribute('data-val'));
                var avg = (avg_rate*num_votes + itarget)/(num_votes +1);
                avg_rate = parseFloat(avg.toFixed(2));
            }
            //average per star
            var svote = parseInt(this.getAttribute('data-vote'))+1;
            this.setAttribute('data-vote', svote);
            num_votes++;
            iconicr_set_rating();
            //set new values
            $.ajax({
                url: ajaxurl,
                type:'POST',
                data: {
                    action: 'iconicr_reqs',
                    todo: 'iconicr_voted',
                    itarget: itarget,
                    avg_rate: avg_rate,
                    num_votes: num_votes,
                    iconicr_id: iconicr_id
                }
            });
            //set cookie
            var cookie = readCookie('iconicr_cookie');
            if (cookie)  //update cookie
                cookie += ","+iconicr_id;
            else  //create cookie
                cookie = iconicr_id;
            createCookie('iconicr_cookie',cookie,30);
        });

        /** TOOLTIP **/
        //code from osvaldas.info
        var targets = $( '[rel~=tooltip]' ),
            target	= false,
            tooltip = false,
            title	= false;
        targets.bind( 'mouseenter', function(){
            target	= $(this);
            tip		= target.attr('title');
            tooltip	= $( '<div id="tooltip"></div>' );
            if( !tip || tip == '' )
                return false;
            target.removeAttr('title');
            tooltip.css('opacity', 0)
                   .html(tip)
                   .appendTo('body');
            //init
            var init_tooltip = function(){
                var ww = $(window).width();
                var ttowidth = tooltip.outerWidth();
                var ttoheight = tooltip.outerHeight();
                var towidth = target.outerWidth();
                var toleft = target.offset().left;
                var totop = target.offset().top;
                var toheight = target.outerHeight();
                if( ww < ttowidth * 1.5 )
                    tooltip.css('max-width', ww/2);
                else
                    tooltip.css( 'max-width', 340 );
                var pos_left = toleft + (towidth/2) - (ttowidth/2),
                    pos_top	 = totop - ttoheight - 20;
                if( pos_left < 0 ){
                    pos_left = toleft + towidth/2 - 20;
                    tooltip.addClass( 'left' );
                }else tooltip.removeClass( 'left' );

                if( pos_left + ttowidth > ww ){
                    pos_left = toleft - ttowidth + towidth/2 + 20;
                    tooltip.addClass( 'right' );
                }else tooltip.removeClass( 'right' );
                if( pos_top < 0 ){
                    var pos_top	 = totop + toheight;
                    tooltip.addClass( 'top' );
                }else tooltip.removeClass( 'top' );
                tooltip
                    .css( { left: pos_left, top: pos_top } )
                    .animate( { top: '+=10', opacity: 1 }, 50 );
            };
            init_tooltip();
            $( window ).resize( init_tooltip );
            //remove
            var remove_tooltip = function(){
                tooltip
                    .animate( { top: '-=10', opacity: 0 }, 50, function() {
                        $( this ).remove();
                });
                target.attr( 'title', tip );
            };
            //mouse leave
            target.bind( 'mouseleave', remove_tooltip );
            tooltip.bind( 'click', remove_tooltip );
        });
    }

    /** cookie functions **/
    function createCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*86400*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }
    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

});

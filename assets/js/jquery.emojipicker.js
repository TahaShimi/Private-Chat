!function(e){var t={width:"200",height:"350",position:"right",fadeTime:100,iconColor:"black",iconBackgroundColor:"#eee",recentCount:36,emojiSet:"apple",container:"body",button:!0},i=280,s=600,n=100,o=350,a=[{name:"people",label:"People"},{name:"object",label:"Objects"},{name:"symbol",label:"Symbols"}],r=[];function c(c,h){this.element=c,this.$el=e(c),this.settings=e.extend({},t,h),r=JSON.parse(h.categories),e.each(a,function(){this.label=r[this.name]}),this.$container=e(this.settings.container),this.settings.width=parseInt(this.settings.width),this.settings.height=parseInt(this.settings.height),this.settings.width>=s?this.settings.width=s:this.settings.width<i&&(this.settings.width=i),this.settings.height>=o?this.settings.height=o:this.settings.height<n&&(this.settings.height=n);-1==e.inArray(this.settings.position,["left","right"])&&(this.settings.position=t.position),/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)?this.isMobile=!0:this.init()}e.extend(c.prototype,{init:function(){this.active=!1,this.addPickerIcon(),this.createPicker(),this.listen()},addPickerIcon:function(){if(this.settings.button){this.$el.outerHeight();this.$wrapper=this.$el.wrap("<div class='emojiPickerIconWrap w-100'></div>").parent(),this.$icon=e('<div class="emojiPickerIcon"><label><i class="mdi mdi-emoticon" style="font-size:22px"></i></label></div>').addClass(this.settings.iconColor),this.$wrapper.prepend(this.$icon),this.$wrapper.prepend('<label for="file-input" class="m-r-10" style="cursor:pointer"><i class="mdi mdi-file-outline" style="font-size: 20px;display: block;"></i><input id="file-input" name="file" type="file" accept="application/pdf,.txt,.doc, .docx,.csv,.xlsx" style="display: none;" onchange="selected(this);"></label><label for="picture-input" class="m-r-10" style="cursor:pointer"><i class="mdi mdi-file-image" style="font-size: 20px;display: block;"></i><input id="picture-input" name="picture" type="file" accept="image/*" style="display: none;" onchange="selected(this);"></label>')}},createPicker:function(){this.$picker=e(function(){var t=[],i={undefined:"object"},s={},n="undefined"!=typeof Storage;e.each(e.fn.emojiPicker.emojis,function(e,t){var n=i[t.category]||t.category;s[n]=s[n]||[],s[n].push(t)}),t.push('<div class="emojiPicker">'),t.push("<nav>");for(var o=a.length,c=0;c<o;c++)t.push('<div class="tab'+(n||0!=c?"":" active")+'" data-tab="'+a[c].name+'"><div class="emoji emoji-tab-'+a[c].name+'"></div></div>');if(t.push("</nav>"),t.push('<div class="sections">'),t.push('<section class="search">'),t.push('<input type="search" placeholder="Search...">'),t.push('<div class="wrap" style="display:none;"><h1>Search Results</h1></div>'),t.push("</section>"),n){var h=[],l=' style="display:none;"';localStorage.emojis&&(h=JSON.parse(localStorage.emojis),h.length,l=' style="display:block;"'),t.push('<section class="recent" data-count="'+h.length+'"'+l+">"),t.push("<h1>"+r.RecentlyUsed+'</h1><div class="wrap">');for(var c=h.length-1;c>-1;c--)t.push('<em><span class="emoji emoji-'+h[c]+'"></span></em>');t.push("</div></section>")}for(var c=0;c<o;c++){var p=s[a[c].name].length;t.push('<section class="'+a[c].name+'" data-count="'+p+'">'),t.push("<h1>"+a[c].label+'</h1><div class="wrap">');for(var d=0;d<p;d++){var u=s[a[c].name][d];t.push('<em><span class="emoji emoji-'+u.shortcode+'"></span></em>')}t.push("</div></section>")}return t.push("</div>"),t.push('<div class="shortcode"><span class="random">'),t.push('<em class="tabTitle">'+function(){var t=e.fn.emojiPicker.emojis,i=Math.floor(364*Math.random()+0),s=t[i];return'Daily Emoji: <span class="eod"><span class="emoji emoji-'+s.name+'"></span> <span class="emojiName">'+s.name+"</span></span>"}()+"</em>"),t.push('</span><span class="info"></span></div>'),t.push("</div>"),t.join("\n")}()).appendTo(e(".chat-texture")).width(this.settings.width).css("z-index",1e4),this.settings.width<240&&this.$picker.find(".emoji").css({width:"1em",height:"1em"})},destroyPicker:function(){return this.isMobile?this:(this.$picker.unbind("mouseover"),this.$picker.unbind("mouseout"),this.$picker.unbind("click"),this.$picker.remove(),e.removeData(this.$el.get(0),"emojiPicker"),this)},listen:function(){this.settings.button&&this.$wrapper.find(".emojiPickerIcon").click(e.proxy(this.iconClicked,this)),this.$picker.on("click","em",e.proxy(this.emojiClicked,this)),this.$picker.on("mouseover","em",e.proxy(this.emojiMouseover,this)),this.$picker.on("mouseout","em",e.proxy(this.emojiMouseout,this)),this.$picker.find("nav .tab").click(e.proxy(this.emojiCategoryClicked,this)).mouseover(e.proxy(this.emojiTabMouseover,this)).mouseout(e.proxy(this.emojiMouseout,this)),this.$picker.find(".sections").scroll(e.proxy(this.emojiScroll,this)),this.$picker.click(e.proxy(this.pickerClicked,this)),this.$picker.find("section.search input").on("keyup search",e.proxy(this.searchCharEntered,this)),this.$picker.find(".shortcode").mouseover(function(e){e.stopPropagation()}),e(document.body).click(e.proxy(this.clickOutside,this)),e(window).resize(e.proxy(this.updatePosition,this))},updatePosition:function(){return this.$picker.css({bottom:"15%",width:"auto","max-width":"390px"}),this},hide:function(){this.$picker.hide(this.settings.fadeTime,"linear",function(){this.active=!1,this.settings.onHide&&this.settings.onHide(this.$picker,this.settings,this.active)}.bind(this))},show:function(){this.$el.focus(),this.updatePosition(),this.$picker.show(this.settings.fadeTime,"linear",function(){this.active=!0,this.settings.onShow&&this.settings.onShow(this.$picker,this.settings,this.active)}.bind(this))},iconClicked:function(){"none"===e(this.$picker).css("display")?(this.show(),this.$picker.find(".search input").length>0&&this.$picker.find(".search input").focus()):this.hide()},emojiClicked:function(i){var s,n,o=e(i.target),a=(o.is("em")?o.find("span"):o.parent().find(".emoji")).attr("class").split("emoji-")[1],r=(s=function(t){for(var i=e.fn.emojiPicker.emojis,s=0;s<i.length;s++)if(i[s].shortcode==t)return i[s]}(a).unicode[t.emojiSet],n=s.split("-").map(function(e,t){return parseInt(e,16)}),String.fromCodePoint.apply(null,n));if(0==e("#content_watcher").length||parseInt(e("#content_watcher").text())>1){!function(e,t){if(document.selection){e.focus();var i=document.selection.createRange();i.text=t,e.focus()}else if(e.selectionStart||"0"==e.selectionStart){var s=e.selectionStart,n=e.selectionEnd,o=e.scrollTop;e.value=e.value.substring(0,s)+t+e.value.substring(n,e.value.length),e.focus(),e.selectionStart=s+t.length,e.selectionEnd=s+t.length,e.scrollTop=o}else e.focus(),e.value+=t}(this.element,r),function(e){var i=[];localStorage.emojis&&(i=JSON.parse(localStorage.emojis));var s=i.indexOf(e);s>-1&&i.splice(s,1);i.push(e),i.length>t.recentCount&&i.shift();localStorage.emojis=JSON.stringify(i)}(a),function(t){for(var i=JSON.parse(localStorage.emojis),s=[],n=e("section.recent"),o=i.length-1;o>=0;o--)s.push('<em><span class="emoji emoji-'+i[o]+'"></span></em>');var a=n.outerHeight();e("section.recent .wrap").html(s.join(""));var r=e(".sections").scrollTop(),c=n.outerHeight(),h=0;e("section.recent").is(":visible")?a!=c&&(h=c-a):(n.show(),h=c);e(".sections").animate({scrollTop:r+h},0)}(),e(this.element).trigger("keyup");var c=document.createEvent("HTMLEvents");c.initEvent("input",!0,!0),this.element.dispatchEvent(c)}},emojiMouseover:function(t){var i=e(t.target).parent().find(".emoji").attr("class").split("emoji-")[1],s=e(t.target).parents(".emojiPicker").find(".shortcode");s.find(".random").hide(),s.find(".info").show().html('<div class="emoji emoji-'+i+'"></div><em>'+i+"</em>")},emojiMouseout:function(t){e(t.target).parents(".emojiPicker").find(".shortcode .info").empty().hide(),e(t.target).parents(".emojiPicker").find(".shortcode .random").show()},emojiCategoryClicked:function(t){var i="";this.$picker.find("nav .tab").removeClass("active"),e(t.target).parent().hasClass("tab")?(i=e(t.target).parent().attr("data-tab"),e(t.target).parent(".tab").addClass("active")):(i=e(t.target).attr("data-tab"),e(t.target).addClass("active"));var s=this.$picker.find("section."+i),n=s.parent().scrollTop()+s.offset().top-s.parent().offset().top;e(".sections").off("scroll");var o=this;e(".sections").animate({scrollTop:n},250,function(){o.$picker.find(".sections").on("scroll",e.proxy(o.emojiScroll,o))})},emojiTabMouseover:function(t){var i="";i=e(t.target).parent().hasClass("tab")?e(t.target).parent().attr("data-tab"):e(t.target).attr("data-tab");for(var s="",n=0;n<a.length;n++)a[n].name==i&&(s=a[n].label);""==s&&(s=r.RecentlyUsed);var o='<em class="tabTitle">'+s+' <span class="count">('+e("section."+i).attr("data-count")+" emojis)</span></em>",c=e(t.target).parents(".emojiPicker").find(".shortcode");c.find(".random").hide(),c.find(".info").show().html(o)},emojiScroll:function(t){var i=e("section");e.each(i,function(t,s){var n=i[t],o=e(n).position().top;"search"==n.className||"people"==n.className&&o>0?e(n).parents(".emojiPicker").find("nav tab.recent").addClass("active"):o<=0&&(e(n).parents(".emojiPicker").find("nav .tab").removeClass("active"),e(n).parents(".emojiPicker").find("nav .tab[data-tab="+n.className+"]").addClass("active"))})},pickerClicked:function(e){e.stopPropagation()},clickOutside:function(e){this.active&&this.hide()},searchCharEntered:function(t){var i=e(t.target).val(),s=e(t.target).parents(".sections").find("section.search"),n=s.find(".wrap"),o=e(t.target).parents(".sections").find("section");if(""==i&&(o.show(),n.hide()),i.length>0){o.hide(),s.show(),n.show();var a=[];n.find("em").remove(),e.each(e.fn.emojiPicker.emojis,function(e,t){var s=t.shortcode;s.indexOf(i)>-1&&a.push('<em><div class="emoji emoji-'+s+'"></div></em>')}),n.append(a.join(""))}else o.show(),n.hide()}}),e.fn.emojiPicker=function(t){return"string"==typeof t?(this.each(function(){var i=e.data(this,"emojiPicker");switch(t){case"toggle":i.iconClicked();break;case"destroy":i.destroyPicker()}}),this):(this.each(function(){e.data(this,"emojiPicker")||e.data(this,"emojiPicker",new c(this,t))}),this)},String.fromCodePoint||(String.fromCodePoint=function(){var e,t,i,s,n=[];for(s=0;s<arguments.length;++s)t=(e=arguments[s])-65536,i=e>65535?[55296+(t>>10),56320+(1023&t)]:[e],n.push(String.fromCharCode.apply(null,i));return n.join("")})}(jQuery);
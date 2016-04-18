jQuery(document).ready(function($) {

	var add_fav_btn = function() {
		$('.pp_details').each(function() {

			/**
			 * jQuery caching.
			 * @type {Object}
			 */
			var $this = $(this);

			/**
			 * Favorites from cookies.
			 * @type {Array}
			 */
			var favs = Cookies.getJSON('kds_favorites')
				? Cookies.getJSON('kds_favorites')
				: []
			;

			/**
			 * Current PrettyPhoto image URL.
			 * @type {String}
			 */
			var image_URL = $('#fullResImage').attr("src");

			/**
			 * Holds true/false if the current image is/isn't in favorites.
			 * @type {Boolean}
			 */
			var is_fav = (favs.indexOf(image_URL) != -1);

			/**
			 * Holds true/false if the favorites buttons have/haven't been added.
			 * @type {Boolean}
			 */
			var has_fav_btns = $('.pp_add_to_fav', $this).length > 0;

			/**
			 * Favorite buttons HTML code.
			 * @type {Object}
			 */
			var fav_btns_HTML = {
				add: '<a href="#" class="pp_add_to_fav"><span class="hint--top" data-hint="Add to favorites."></span></a>',
				remove: '<a href="#" class="pp_remove_from_fav"><span class="hint--top" data-hint="Added to favorites, click to remove."></span></a>'
			};

			/**
			 * Add buttons.
			 */
			if (!has_fav_btns) {
				$this.append(fav_btns_HTML.add);
				$this.append(fav_btns_HTML.remove);
			}

			/**
			 * 
			 */
			$('.pp_add_to_fav', $this).click(function(e) {
				e.preventDefault();
				var image_URL = $('#fullResImage').attr("src");
				var favs = Cookies.getJSON('kds_favorites')
					? Cookies.getJSON('kds_favorites')
					: []
				;
				favs.push(image_URL);
				favs = favs.filter(function(item, index) {
					return favs.indexOf(item) == index;
				});
				Cookies.set('kds_favorites', favs);
			});
			$('.pp_remove_from_fav', $this).click(function(e) {
				e.preventDefault();
				var image_URL = $('#fullResImage').attr("src");
				var favs = Cookies.getJSON('kds_favorites')
					? Cookies.getJSON('kds_favorites')
					: []
				;
				var index = favs.indexOf(image_URL);
				if (index != -1) {
					favs.splice(index, 1);
				}
				Cookies.set('kds_favorites', favs);
			});
			

			/**
			 * Show/hide add/remove buttons depending on is_fav.
			 */
			if (is_fav) {
				$('.pp_add_to_fav', $this).hide();
				$('.pp_remove_from_fav', $this).show();
			} else {
				$('.pp_add_to_fav', $this).show();
				$('.pp_remove_from_fav', $this).hide();
			}
		});
	};

	/**
	 * Main Loop.
	 */
	setInterval(add_fav_btn, 1000);
});

/*!
 * JavaScript Cookie v2.1.1
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
!function(e){if("function"==typeof define&&define.amd)define(e);else if("object"==typeof exports)module.exports=e();else{var n=window.Cookies,o=window.Cookies=e();o.noConflict=function(){return window.Cookies=n,o}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}function n(o){function t(n,i,r){var c;if("undefined"!=typeof document){if(arguments.length>1){if(r=e({path:"/"},t.defaults,r),"number"==typeof r.expires){var s=new Date;s.setMilliseconds(s.getMilliseconds()+864e5*r.expires),r.expires=s}try{c=JSON.stringify(i),/^[\{\[]/.test(c)&&(i=c)}catch(a){}return i=o.write?o.write(i,n):encodeURIComponent(String(i)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=encodeURIComponent(String(n)),n=n.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),n=n.replace(/[\(\)]/g,escape),document.cookie=[n,"=",i,r.expires&&"; expires="+r.expires.toUTCString(),r.path&&"; path="+r.path,r.domain&&"; domain="+r.domain,r.secure?"; secure":""].join("")}n||(c={});for(var p=document.cookie?document.cookie.split("; "):[],d=/(%[0-9A-Z]{2})+/g,f=0;f<p.length;f++){var u=p[f].split("="),l=u[0].replace(d,decodeURIComponent),m=u.slice(1).join("=");'"'===m.charAt(0)&&(m=m.slice(1,-1));try{if(m=o.read?o.read(m,l):o(m,l)||m.replace(d,decodeURIComponent),this.json)try{m=JSON.parse(m)}catch(a){}if(n===l){c=m;break}n||(c[l]=m)}catch(a){}}return c}}return t.set=t,t.get=function(e){return t(e)},t.getJSON=function(){return t.apply({json:!0},[].slice.call(arguments))},t.defaults={},t.remove=function(n,o){t(n,"",e(o,{expires:-1}))},t.withConverter=n,t}return n(function(){})});

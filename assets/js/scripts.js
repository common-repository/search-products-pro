/* Fuzzysort - MIT License Copyright (c) 2018 Stephen Kamenar */
!function(e,r){"function"==typeof define&&define.amd?define([],r):"object"==typeof module&&module.exports?module.exports=r():e.fuzzysort=r()}(this,function(){var P="undefined"!=typeof require&&"undefined"==typeof window,n=new Map,o=new Map,j=[];j.total=0;var m=[],T=[];function r(){n.clear(),o.clear(),m=[],T=[]}function N(e){for(var r=-9007199254740991,n=e.length-1;0<=n;--n){var o,t=e[n];null!==t&&(r<(o=t.score)&&(r=o))}return-9007199254740991===r?null:r}function S(e,r){var n=e[r];if(void 0!==n)return n;var o=r;Array.isArray(r)||(o=r.split("."));for(var t=o.length,i=-1;e&&++i<t;)e=e[o[i]];return e}function D(e){return"object"==typeof e}var t=function(){var i=[],a=0,e={};function r(){for(var e=i[o=0],r=1;r<a;){var n=r+1,o=r;n<a&&i[n].score<i[r].score&&(o=n),i[o-1>>1]=i[o],r=1+(o<<1)}for(var t=o-1>>1;0<o&&e.score<i[t].score;t=(o=t)-1>>1)i[o]=i[t];i[o]=e}return e.add=function(e){var r=a;i[a++]=e;for(var n=r-1>>1;0<r&&e.score<i[n].score;n=(r=n)-1>>1)i[r]=i[n];i[r]=e},e.poll=function(){if(0!==a){var e=i[0];return i[0]=i[--a],r(),e}},e.peek=function(e){if(0!==a)return i[0]},e.replaceTop=function(e){i[0]=e,r()},e},k=t();return function e(A){var L={single:function(e,r,n){return e?(D(e)||(e=L.getPreparedSearch(e)),r?(D(r)||(r=L.getPrepared(r)),((n&&void 0!==n.allowTypo?n.allowTypo:!A||void 0===A.allowTypo||A.allowTypo)?L.algorithm:L.algorithmNoTypo)(e,r,e[0])):null):null},go:function(e,r,n){if(!e)return j;var o=(e=L.prepareSearch(e))[0],t=n&&n.threshold||A&&A.threshold||-9007199254740991,i=n&&n.limit||A&&A.limit||9007199254740991,a=(n&&void 0!==n.allowTypo?n.allowTypo:!A||void 0===A.allowTypo||A.allowTypo)?L.algorithm:L.algorithmNoTypo,l=0,f=0,u=r.length;if(n&&n.keys)for(var s=n.scoreFn||N,p=n.keys,d=p.length,c=u-1;0<=c;--c){for(var g=r[c],v=new Array(d),h=d-1;0<=h;--h)(y=S(g,x=p[h]))?(D(y)||(y=L.getPrepared(y)),v[h]=a(e,y,o)):v[h]=null;v.obj=g;var w=s(v);null!==w&&(w<t||(v.score=w,l<i?(k.add(v),++l):(++f,w>k.peek().score&&k.replaceTop(v))))}else if(n&&n.key)for(var x=n.key,c=u-1;0<=c;--c)(y=S(g=r[c],x))&&(D(y)||(y=L.getPrepared(y)),null!==(m=a(e,y,o))&&(m.score<t||(m={target:m.target,_targetLowerCodes:null,_nextBeginningIndexes:null,score:m.score,indexes:m.indexes,obj:g},l<i?(k.add(m),++l):(++f,m.score>k.peek().score&&k.replaceTop(m)))));else for(var y,m,c=u-1;0<=c;--c)(y=r[c])&&(D(y)||(y=L.getPrepared(y)),null!==(m=a(e,y,o))&&(m.score<t||(l<i?(k.add(m),++l):(++f,m.score>k.peek().score&&k.replaceTop(m)))));if(0===l)return j;for(var T=new Array(l),c=l-1;0<=c;--c)T[c]=k.poll();return T.total=l+f,T},goAsync:function(I,B,_){var C=!1,e=new Promise(function(c,g){if(!I)return c(j);var v=(I=L.prepareSearch(I))[0],h=t(),w=B.length-1,x=_&&_.threshold||A&&A.threshold||-9007199254740991,y=_&&_.limit||A&&A.limit||9007199254740991,m=(_&&void 0!==_.allowTypo?_.allowTypo:!A||void 0===A.allowTypo||A.allowTypo)?L.algorithm:L.algorithmNoTypo,T=0,k=0;function b(){if(C)return g("canceled");var e=Date.now();if(_&&_.keys)for(var r=_.scoreFn||N,n=_.keys,o=n.length;0<=w;--w){for(var t=B[w],i=new Array(o),a=o-1;0<=a;--a)(f=S(t,s=n[a]))?(D(f)||(f=L.getPrepared(f)),i[a]=m(I,f,v)):i[a]=null;i.obj=t;var l=r(i);if(null!==l&&!(l<x)&&(i.score=l,T<y?(h.add(i),++T):(++k,l>h.peek().score&&h.replaceTop(i)),w%1e3==0&&10<=Date.now()-e))return void(P?setImmediate:setTimeout)(b)}else if(_&&_.key){for(var f,u,s=_.key;0<=w;--w)if((f=S(t=B[w],s))&&(D(f)||(f=L.getPrepared(f)),null!==(u=m(I,f,v))&&!(u.score<x)&&(u={target:u.target,_targetLowerCodes:null,_nextBeginningIndexes:null,score:u.score,indexes:u.indexes,obj:t},T<y?(h.add(u),++T):(++k,u.score>h.peek().score&&h.replaceTop(u)),w%1e3==0&&10<=Date.now()-e)))return void(P?setImmediate:setTimeout)(b)}else for(;0<=w;--w)if((f=B[w])&&(D(f)||(f=L.getPrepared(f)),null!==(u=m(I,f,v))&&!(u.score<x)&&(T<y?(h.add(u),++T):(++k,u.score>h.peek().score&&h.replaceTop(u)),w%1e3==0&&10<=Date.now()-e)))return void(P?setImmediate:setTimeout)(b);if(0===T)return c(j);for(var p=new Array(T),d=T-1;0<=d;--d)p[d]=h.poll();p.total=T+k,c(p)}P?setImmediate(b):b()});return e.cancel=function(){C=!0},e},highlight:function(e,r,n){if(null===e)return null;void 0===r&&(r="<b>"),void 0===n&&(n="</b>");for(var o="",t=0,i=!1,a=e.target,l=a.length,f=e.indexes,u=0;u<l;++u){var s=a[u];if(f[t]===u){if(i||(i=!0,o+=r),++t===f.length){o+=s+n+a.substr(u+1);break}}else i&&(i=!1,o+=n);o+=s}return o},prepare:function(e){if(e)return{target:e,_targetLowerCodes:L.prepareLowerCodes(e),_nextBeginningIndexes:null,score:null,indexes:null,obj:null}},prepareSlow:function(e){if(e)return{target:e,_targetLowerCodes:L.prepareLowerCodes(e),_nextBeginningIndexes:L.prepareNextBeginningIndexes(e),score:null,indexes:null,obj:null}},prepareSearch:function(e){if(e)return L.prepareLowerCodes(e)},getPrepared:function(e){if(999<e.length)return L.prepare(e);var r=n.get(e);return void 0!==r||(r=L.prepare(e),n.set(e,r)),r},getPreparedSearch:function(e){if(999<e.length)return L.prepareSearch(e);var r=o.get(e);return void 0!==r||(r=L.prepareSearch(e),o.set(e,r)),r},algorithm:function(e,r,n){for(var o=r._targetLowerCodes,t=e.length,i=o.length,a=0,l=0,f=0,u=0;;){if(n===o[l]){if(m[u++]=l,++a===t)break;n=e[0===f?a:f===a?a+1:f===a-1?a-1:a]}if(i<=++l)for(;;){if(a<=1)return null;if(0===f){if(n===e[--a])continue;f=a}else{if(1===f)return null;if((n=e[1+(a=--f)])===e[a])continue}l=m[(u=a)-1]+1;break}}var a=0,s=0,p=!1,d=0,c=r._nextBeginningIndexes;null===c&&(c=r._nextBeginningIndexes=L.prepareNextBeginningIndexes(r.target));var g,v,h=l=0===m[0]?0:c[m[0]-1];if(l!==i)for(;;)if(i<=l){if(a<=0){if(t-2<++s)break;if(e[s]===e[s+1])continue;l=h;continue}--a,l=c[T[--d]]}else if(e[0===s?a:s===a?a+1:s===a-1?a-1:a]===o[l]){if(T[d++]=l,++a===t){p=!0;break}++l}else l=c[l];v=p?(g=T,d):(g=m,u);for(var w=0,x=-1,y=0;y<t;++y)x!==(l=g[y])-1&&(w-=l),x=l;for(p?0!==s&&(w+=-20):(w*=1e3,0!==f&&(w+=-20)),w-=i-t,r.score=w,r.indexes=new Array(v),y=v-1;0<=y;--y)r.indexes[y]=g[y];return r},algorithmNoTypo:function(e,r,n){for(var o=r._targetLowerCodes,t=e.length,i=o.length,a=0,l=0,f=0;;){if(n===o[l]){if(m[f++]=l,++a===t)break;n=e[a]}if(i<=++l)return null}var u,s,a=0,p=!1,d=0,c=r._nextBeginningIndexes;if(null===c&&(c=r._nextBeginningIndexes=L.prepareNextBeginningIndexes(r.target)),(l=0===m[0]?0:c[m[0]-1])!==i)for(;;)if(i<=l){if(a<=0)break;--a,l=c[T[--d]]}else if(e[a]===o[l]){if(T[d++]=l,++a===t){p=!0;break}++l}else l=c[l];s=p?(u=T,d):(u=m,f);for(var g=0,v=-1,h=0;h<t;++h)v!==(l=u[h])-1&&(g-=l),v=l;for(p||(g*=1e3),g-=i-t,r.score=g,r.indexes=new Array(s),h=s-1;0<=h;--h)r.indexes[h]=u[h];return r},prepareLowerCodes:function(e){for(var r=e.length,n=[],o=e.toLowerCase(),t=0;t<r;++t)n[t]=o.charCodeAt(t);return n},prepareBeginningIndexes:function(e){for(var r=e.length,n=[],o=0,t=!1,i=!1,a=0;a<r;++a){var l=e.charCodeAt(a),f=65<=l&&l<=90,u=f||97<=l&&l<=122||48<=l&&l<=57,s=f&&!t||!i||!u,t=f,i=u;s&&(n[o++]=a)}return n},prepareNextBeginningIndexes:function(e){for(var r=e.length,n=L.prepareBeginningIndexes(e),o=[],t=n[0],i=0,a=0;a<r;++a)a<t?o[a]=t:(t=n[++i],o[a]=void 0===t?r:t);return o},cleanup:r,new:e};return L}()});

(function($){
    "use strict";

    var doAction = false;
    var willDestroy = true;
    var ajaxOn = 'notactive';
    
    $(document).on( 'keyup', '.spp--element', function(e) {
        if ( e.target && e.target.matches('.spp--input') ) {
            if ( doAction ) {
                clearTimeout( doAction );
            }

            doAction = setTimeout( function() {
                _do_live_search(e);
            }, 250 );
        }
    } );

    $(document).on( 'click', function(e) {
        if ( willDestroy !== false ) {
            $('.spp--focus').removeClass('spp--focus');

            setTimeout( function() {
                $('.spp--results').remove();
            }, 250 );
        }
    } );

    function rClass( c, n ) {
        setTimeout( function(b) {
            b[1].removeClass(b[0]);
        }, 200, [c,n] );
    }

    $(document).on( 'click', '.spp--element-wrap.xwc--active', function(e) {
        if (e.target.classList.contains('xwc--active')) {
            $(this).removeClass('xwc--active').addClass('xwc--preparing');

            rClass( 'xwc--preparing', $(this) );
        }
    } );

    $(document).on( 'click', '.spp--has-search .spp--button', function(e) {
        $(this).parent().removeClass('spp--has-search').find('input:first').val('').trigger('focus');
        _destroy(e);
    } );

    $(document).on( 'click', '.spp--callout', function(e) {
        $('#'+$(this).attr('data-callout')+'-wrap').addClass('xwc--active');
    } );

    $(document).on( 'click', '.spp--element-wrap .spp--result a', function(e) {
        $(this).closest('.spp--element-wrap').removeClass('xwc--active').addClass('xwc--preparing');

        rClass( 'xwc--preparing', $(this) );
    } );

    $(document).on( 'focusin', '.spp--element', function(e) {
        if ( e.target && e.target.matches('.spp--input') ) {
            e.target.classList.add('spp--focus');

            if ( e.target.value !== '' ) {
                _do_live_search(e);
            }
        }
    } );

    $('.spp--element').mouseover(function(e){ 
        willDestroy = false;
    }); 
      
    $('.spp--element').mouseleave(function(){ 
        willDestroy = true;
    });

    $(document).on( 'focusout', '.spp--element', function(e) {
        if ( willDestroy !== false ) {
            if ( e.target && e.target.matches('.spp--input') ) {
                e.target.classList.remove('spp--focus');

                setTimeout( function() {
                    _destroy(e);
                }, 500 );
            }
        }
    } );

    function _destroy(e) {
        $(e.target.parentNode).find('.spp--results').remove();
    }
    
    function _check_field_status(e) {
        if (e.target.value.length===0) {
            e.target.parentNode.classList.remove('spp--has-search');
        } else {
            e.target.parentNode.classList.add('spp--has-search');
        }
    }

    function _do_live_search(e) {
        _check_field_status(e);

        if ( ajaxOn == 'active' ) {
            return false;
        }

        if ( e.target.value.length<3 ) {
            _destroy(e);

            return false;
        }
        
        ajaxOn = 'active';
        
        var s = [ e.target.value, $(e.target).closest('.spp--element').attr('data-category'), JSON.stringify(_see_taxonomies(e.target.value)) ];

        $.when( _ajax(e,s) ).done( function(f) {
			_do_after_search(e,f,s);
		} );
    }

    function _see_taxonomies(e) {
        let data = [];
        const results = fuzzysort.go(e, sp.es, {key:1})

        for( var i=0; i<results.total; i++ ) {
            data.push({'id':results[i].obj[0],'taxonomy':results[i].obj[2]})
        }

        return data;
    }

    function _do_after_search(e,f,s) {
        _build_results(e,f);

        doAction = false;
    }

    function _build_results(e,f) {
        if ( f.length>0 ) {
            var p = '';
            $.each( f, function(i,o) {
                p += '<div class="spp--result"><div class="spp--image">'+o.image+'</div><div class="spp--path">'+o.path+'</div><div class="spp--title">'+o.title+'<div class="spp--price">'+o.price+'</div></div></div>';
            } );
           
            _destroy(e);
            $(e.target.parentNode).append('<div class="spp--results">'+p+'</div>');
        }
        else {
            _destroy(e);
            $(e.target.parentNode).append('<div class="spp--results"><div class="spp--result">'+sp.localize.notfound+'</div></div>');
        }
    }

    function _ajax(e,s) {
        var data = {
            action: 'search_products_pro',
            nonce: e.target.parentNode.dataset.nonce,
            settings: s,
		};

		return $.ajax( {
			type: 'POST',
			url: sp.ajax,
			data: data,
			success: function(r) {
				ajaxOn = 'notactive';
			},
			error: function() {
				alert( 'AJAX Error!' );
				ajaxOn = 'notactive';
			}
		} );
    }

    function setCallouts() {
        var callouts;

        callouts = document.body.getElementsByClassName( 'spp--callout' );
		if ( 'undefined' === typeof callouts ) {
			return;
        }
        
        for ( let callout of callouts ) {
            var next, wrap;

            next = callout.nextElementSibling;

            wrap = document.createElement('div');
            wrap.id = next.id+"-wrap";
            wrap.className = "spp--element-wrap";

            wrap.appendChild(next);
            document.body.appendChild(wrap);
        }
    }
    setCallouts();



})(jQuery);
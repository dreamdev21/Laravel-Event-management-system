/*!
 * Modernizr v2.8.3
 * www.modernizr.com
 *
 * Copyright (c) Faruk Ates, Paul Irish, Alex Sexton
 * Available under the BSD and MIT licenses: www.modernizr.com/license/
 */

/*
 * Modernizr tests which native CSS3 and HTML5 features are available in
 * the current UA and makes the results available to you in two ways:
 * as properties on a global Modernizr object, and as classes on the
 * <html> element. This information allows you to progressively enhance
 * your pages with a granular level of control over the experience.
 *
 * Modernizr has an optional (not included) conditional resource loader
 * called Modernizr.load(), based on Yepnope.js (yepnopejs.com).
 * To get a build that includes Modernizr.load(), as well as choosing
 * which tests to include, go to www.modernizr.com/download/
 *
 * Authors        Faruk Ates, Paul Irish, Alex Sexton
 * Contributors   Ryan Seddon, Ben Alman
 */

window.Modernizr = (function( window, document, undefined ) {

    var version = '2.8.3',

    Modernizr = {},

    /*>>cssclasses*/
    // option for enabling the HTML classes to be added
    enableClasses = true,
    /*>>cssclasses*/

    docElement = document.documentElement,

    /**
     * Create our "modernizr" element that we do most feature tests on.
     */
    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,

    /**
     * Create the input element for various Web Forms feature tests.
     */
    inputElem /*>>inputelem*/ = document.createElement('input') /*>>inputelem*/ ,

    /*>>smile*/
    smile = ':)',
    /*>>smile*/

    toString = {}.toString,

    // TODO :: make the prefixes more granular
    /*>>prefixes*/
    // List of property values to set for css tests. See ticket #21
    prefixes = ' -webkit- -moz- -o- -ms- '.split(' '),
    /*>>prefixes*/

    /*>>domprefixes*/
    // Following spec is to expose vendor-specific style properties as:
    //   elem.style.WebkitBorderRadius
    // and the following would be incorrect:
    //   elem.style.webkitBorderRadius

    // Webkit ghosts their properties in lowercase but Opera & Moz do not.
    // Microsoft uses a lowercase `ms` instead of the correct `Ms` in IE8+
    //   erik.eae.net/archives/2008/03/10/21.48.10/

    // More here: github.com/Modernizr/Modernizr/issues/issue/21
    omPrefixes = 'Webkit Moz O ms',

    cssomPrefixes = omPrefixes.split(' '),

    domPrefixes = omPrefixes.toLowerCase().split(' '),
    /*>>domprefixes*/

    /*>>ns*/
    ns = {'svg': 'http://www.w3.org/2000/svg'},
    /*>>ns*/

    tests = {},
    inputs = {},
    attrs = {},

    classes = [],

    slice = classes.slice,

    featureName, // used in testing loop


    /*>>teststyles*/
    // Inject element with style element and some CSS rules
    injectElementWithStyles = function( rule, callback, nodes, testnames ) {

      var style, ret, node, docOverflow,
          div = document.createElement('div'),
          // After page load injecting a fake body doesn't work so check if body exists
          body = document.body,
          // IE6 and 7 won't return offsetWidth or offsetHeight unless it's in the body element, so we fake it.
          fakeBody = body || document.createElement('body');

      if ( parseInt(nodes, 10) ) {
          // In order not to give false positives we create a node for each test
          // This also allows the method to scale for unspecified uses
          while ( nodes-- ) {
              node = document.createElement('div');
              node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
              div.appendChild(node);
          }
      }

      // <style> elements in IE6-9 are considered 'NoScope' elements and therefore will be removed
      // when injected with innerHTML. To get around this you need to prepend the 'NoScope' element
      // with a 'scoped' element, in our case the soft-hyphen entity as it won't mess with our measurements.
      // msdn.microsoft.com/en-us/library/ms533897%28VS.85%29.aspx
      // Documents served as xml will throw if using &shy; so use xml friendly encoded version. See issue #277
      style = ['&#173;','<style id="s', mod, '">', rule, '</style>'].join('');
      div.id = mod;
      // IE6 will false positive on some tests due to the style element inside the test div somehow interfering offsetHeight, so insert it into body or fakebody.
      // Opera will act all quirky when injecting elements in documentElement when page is served as xml, needs fakebody too. #270
      (body ? div : fakeBody).innerHTML += style;
      fakeBody.appendChild(div);
      if ( !body ) {
          //avoid crashing IE8, if background image is used
          fakeBody.style.background = '';
          //Safari 5.13/5.1.4 OSX stops loading if ::-webkit-scrollbar is used and scrollbars are visible
          fakeBody.style.overflow = 'hidden';
          docOverflow = docElement.style.overflow;
          docElement.style.overflow = 'hidden';
          docElement.appendChild(fakeBody);
      }

      ret = callback(div, rule);
      // If this is done after page load we don't want to remove the body so check if body exists
      if ( !body ) {
          fakeBody.parentNode.removeChild(fakeBody);
          docElement.style.overflow = docOverflow;
      } else {
          div.parentNode.removeChild(div);
      }

      return !!ret;

    },
    /*>>teststyles*/

    /*>>mq*/
    // adapted from matchMedia polyfill
    // by Scott Jehl and Paul Irish
    // gist.github.com/786768
    testMediaQuery = function( mq ) {

      var matchMedia = window.matchMedia || window.msMatchMedia;
      if ( matchMedia ) {
        return matchMedia(mq) && matchMedia(mq).matches || false;
      }

      var bool;

      injectElementWithStyles('@media ' + mq + ' { #' + mod + ' { position: absolute; } }', function( node ) {
        bool = (window.getComputedStyle ?
                  getComputedStyle(node, null) :
                  node.currentStyle)['position'] == 'absolute';
      });

      return bool;

     },
     /*>>mq*/


    /*>>hasevent*/
    //
    // isEventSupported determines if a given element supports the given event
    // kangax.github.com/iseventsupported/
    //
    // The following results are known incorrects:
    //   Modernizr.hasEvent("webkitTransitionEnd", elem) // false negative
    //   Modernizr.hasEvent("textInput") // in Webkit. github.com/Modernizr/Modernizr/issues/333
    //   ...
    isEventSupported = (function() {

      var TAGNAMES = {
        'select': 'input', 'change': 'input',
        'submit': 'form', 'reset': 'form',
        'error': 'img', 'load': 'img', 'abort': 'img'
      };

      function isEventSupported( eventName, element ) {

        element = element || document.createElement(TAGNAMES[eventName] || 'div');
        eventName = 'on' + eventName;

        // When using `setAttribute`, IE skips "unload", WebKit skips "unload" and "resize", whereas `in` "catches" those
        var isSupported = eventName in element;

        if ( !isSupported ) {
          // If it has no `setAttribute` (i.e. doesn't implement Node interface), try generic element
          if ( !element.setAttribute ) {
            element = document.createElement('div');
          }
          if ( element.setAttribute && element.removeAttribute ) {
            element.setAttribute(eventName, '');
            isSupported = is(element[eventName], 'function');

            // If property was created, "remove it" (by setting value to `undefined`)
            if ( !is(element[eventName], 'undefined') ) {
              element[eventName] = undefined;
            }
            element.removeAttribute(eventName);
          }
        }

        element = null;
        return isSupported;
      }
      return isEventSupported;
    })(),
    /*>>hasevent*/

    // TODO :: Add flag for hasownprop ? didn't last time

    // hasOwnProperty shim by kangax needed for Safari 2.0 support
    _hasOwnProperty = ({}).hasOwnProperty, hasOwnProp;

    if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
      hasOwnProp = function (object, property) {
        return _hasOwnProperty.call(object, property);
      };
    }
    else {
      hasOwnProp = function (object, property) { /* yes, this can give false positives/negatives, but most of the time we don't care about those */
        return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
      };
    }

    // Adapted from ES5-shim https://github.com/kriskowal/es5-shim/blob/master/es5-shim.js
    // es5.github.com/#x15.3.4.5

    if (!Function.prototype.bind) {
      Function.prototype.bind = function bind(that) {

        var target = this;

        if (typeof target != "function") {
            throw new TypeError();
        }

        var args = slice.call(arguments, 1),
            bound = function () {

            if (this instanceof bound) {

              var F = function(){};
              F.prototype = target.prototype;
              var self = new F();

              var result = target.apply(
                  self,
                  args.concat(slice.call(arguments))
              );
              if (Object(result) === result) {
                  return result;
              }
              return self;

            } else {

              return target.apply(
                  that,
                  args.concat(slice.call(arguments))
              );

            }

        };

        return bound;
      };
    }

    /**
     * setCss applies given styles to the Modernizr DOM node.
     */
    function setCss( str ) {
        mStyle.cssText = str;
    }

    /**
     * setCssAll extrapolates all vendor-specific css strings.
     */
    function setCssAll( str1, str2 ) {
        return setCss(prefixes.join(str1 + ';') + ( str2 || '' ));
    }

    /**
     * is returns a boolean for if typeof obj is exactly type.
     */
    function is( obj, type ) {
        return typeof obj === type;
    }

    /**
     * contains returns a boolean for if substr is found within str.
     */
    function contains( str, substr ) {
        return !!~('' + str).indexOf(substr);
    }

    /*>>testprop*/

    // testProps is a generic CSS / DOM property test.

    // In testing support for a given CSS property, it's legit to test:
    //    `elem.style[styleName] !== undefined`
    // If the property is supported it will return an empty string,
    // if unsupported it will return undefined.

    // We'll take advantage of this quick test and skip setting a style
    // on our modernizr element, but instead just testing undefined vs
    // empty string.

    // Because the testing of the CSS property names (with "-", as
    // opposed to the camelCase DOM properties) is non-portable and
    // non-standard but works in WebKit and IE (but not Gecko or Opera),
    // we explicitly reject properties with dashes so that authors
    // developing in WebKit or IE first don't end up with
    // browser-specific content by accident.

    function testProps( props, prefixed ) {
        for ( var i in props ) {
            var prop = props[i];
            if ( !contains(prop, "-") && mStyle[prop] !== undefined ) {
                return prefixed == 'pfx' ? prop : true;
            }
        }
        return false;
    }
    /*>>testprop*/

    // TODO :: add testDOMProps
    /**
     * testDOMProps is a generic DOM property test; if a browser supports
     *   a certain property, it won't return undefined for it.
     */
    function testDOMProps( props, obj, elem ) {
        for ( var i in props ) {
            var item = obj[props[i]];
            if ( item !== undefined) {

                // return the property name as a string
                if (elem === false) return props[i];

                // let's bind a function
                if (is(item, 'function')){
                  // default to autobind unless override
                  return item.bind(elem || obj);
                }

                // return the unbound function or obj or value
                return item;
            }
        }
        return false;
    }

    /*>>testallprops*/
    /**
     * testPropsAll tests a list of DOM properties we want to check against.
     *   We specify literally ALL possible (known and/or likely) properties on
     *   the element including the non-vendor prefixed one, for forward-
     *   compatibility.
     */
    function testPropsAll( prop, prefixed, elem ) {

        var ucProp  = prop.charAt(0).toUpperCase() + prop.slice(1),
            props   = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

        // did they call .prefixed('boxSizing') or are we just testing a prop?
        if(is(prefixed, "string") || is(prefixed, "undefined")) {
          return testProps(props, prefixed);

        // otherwise, they called .prefixed('requestAnimationFrame', window[, elem])
        } else {
          props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
          return testDOMProps(props, prefixed, elem);
        }
    }
    /*>>testallprops*/


    /**
     * Tests
     * -----
     */

    // The *new* flexbox
    // dev.w3.org/csswg/css3-flexbox

    tests['flexbox'] = function() {
      return testPropsAll('flexWrap');
    };

    // The *old* flexbox
    // www.w3.org/TR/2009/WD-css3-flexbox-20090723/

    tests['flexboxlegacy'] = function() {
        return testPropsAll('boxDirection');
    };

    // On the S60 and BB Storm, getContext exists, but always returns undefined
    // so we actually have to call getContext() to verify
    // github.com/Modernizr/Modernizr/issues/issue/97/

    tests['canvas'] = function() {
        var elem = document.createElement('canvas');
        return !!(elem.getContext && elem.getContext('2d'));
    };

    tests['canvastext'] = function() {
        return !!(Modernizr['canvas'] && is(document.createElement('canvas').getContext('2d').fillText, 'function'));
    };

    // webk.it/70117 is tracking a legit WebGL feature detect proposal

    // We do a soft detect which may false positive in order to avoid
    // an expensive context creation: bugzil.la/732441

    tests['webgl'] = function() {
        return !!window.WebGLRenderingContext;
    };

    /*
     * The Modernizr.touch test only indicates if the browser supports
     *    touch events, which does not necessarily reflect a touchscreen
     *    device, as evidenced by tablets running Windows 7 or, alas,
     *    the Palm Pre / WebOS (touch) phones.
     *
     * Additionally, Chrome (desktop) used to lie about its support on this,
     *    but that has since been rectified: crbug.com/36415
     *
     * We also test for Firefox 4 Multitouch Support.
     *
     * For more info, see: modernizr.github.com/Modernizr/touch.html
     */

    tests['touch'] = function() {
        var bool;

        if(('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
          bool = true;
        } else {
          injectElementWithStyles(['@media (',prefixes.join('touch-enabled),('),mod,')','{#modernizr{top:9px;position:absolute}}'].join(''), function( node ) {
            bool = node.offsetTop === 9;
          });
        }

        return bool;
    };


    // geolocation is often considered a trivial feature detect...
    // Turns out, it's quite tricky to get right:
    //
    // Using !!navigator.geolocation does two things we don't want. It:
    //   1. Leaks memory in IE9: github.com/Modernizr/Modernizr/issues/513
    //   2. Disables page caching in WebKit: webk.it/43956
    //
    // Meanwhile, in Firefox < 8, an about:config setting could expose
    // a false positive that would throw an exception: bugzil.la/688158

    tests['geolocation'] = function() {
        return 'geolocation' in navigator;
    };


    tests['postmessage'] = function() {
      return !!window.postMessage;
    };


    // Chrome incognito mode used to throw an exception when using openDatabase
    // It doesn't anymore.
    tests['websqldatabase'] = function() {
      return !!window.openDatabase;
    };

    // Vendors had inconsistent prefixing with the experimental Indexed DB:
    // - Webkit's implementation is accessible through webkitIndexedDB
    // - Firefox shipped moz_indexedDB before FF4b9, but since then has been mozIndexedDB
    // For speed, we don't test the legacy (and beta-only) indexedDB
    tests['indexedDB'] = function() {
      return !!testPropsAll("indexedDB", window);
    };

    // documentMode logic from YUI to filter out IE8 Compat Mode
    //   which false positives.
    tests['hashchange'] = function() {
      return isEventSupported('hashchange', window) && (document.documentMode === undefined || document.documentMode > 7);
    };

    // Per 1.6:
    // This used to be Modernizr.historymanagement but the longer
    // name has been deprecated in favor of a shorter and property-matching one.
    // The old API is still available in 1.6, but as of 2.0 will throw a warning,
    // and in the first release thereafter disappear entirely.
    tests['history'] = function() {
      return !!(window.history && history.pushState);
    };

    tests['draganddrop'] = function() {
        var div = document.createElement('div');
        return ('draggable' in div) || ('ondragstart' in div && 'ondrop' in div);
    };

    // FF3.6 was EOL'ed on 4/24/12, but the ESR version of FF10
    // will be supported until FF19 (2/12/13), at which time, ESR becomes FF17.
    // FF10 still uses prefixes, so check for it until then.
    // for more ESR info, see: mozilla.org/en-US/firefox/organizations/faq/
    tests['websockets'] = function() {
        return 'WebSocket' in window || 'MozWebSocket' in window;
    };


    // css-tricks.com/rgba-browser-support/
    tests['rgba'] = function() {
        // Set an rgba() color and check the returned value

        setCss('background-color:rgba(150,255,150,.5)');

        return contains(mStyle.backgroundColor, 'rgba');
    };

    tests['hsla'] = function() {
        // Same as rgba(), in fact, browsers re-map hsla() to rgba() internally,
        //   except IE9 who retains it as hsla

        setCss('background-color:hsla(120,40%,100%,.5)');

        return contains(mStyle.backgroundColor, 'rgba') || contains(mStyle.backgroundColor, 'hsla');
    };

    tests['multiplebgs'] = function() {
        // Setting multiple images AND a color on the background shorthand property
        //  and then querying the style.background property value for the number of
        //  occurrences of "url(" is a reliable method for detecting ACTUAL support for this!

        setCss('background:url(https://),url(https://),red url(https://)');

        // If the UA supports multiple backgrounds, there should be three occurrences
        //   of the string "url(" in the return value for elemStyle.background

        return (/(url\s*\(.*?){3}/).test(mStyle.background);
    };



    // this will false positive in Opera Mini
    //   github.com/Modernizr/Modernizr/issues/396

    tests['backgroundsize'] = function() {
        return testPropsAll('backgroundSize');
    };

    tests['borderimage'] = function() {
        return testPropsAll('borderImage');
    };


    // Super comprehensive table about all the unique implementations of
    // border-radius: muddledramblings.com/table-of-css3-border-radius-compliance

    tests['borderradius'] = function() {
        return testPropsAll('borderRadius');
    };

    // WebOS unfortunately false positives on this test.
    tests['boxshadow'] = function() {
        return testPropsAll('boxShadow');
    };

    // FF3.0 will false positive on this test
    tests['textshadow'] = function() {
        return document.createElement('div').style.textShadow === '';
    };


    tests['opacity'] = function() {
        // Browsers that actually have CSS Opacity implemented have done so
        //  according to spec, which means their return values are within the
        //  range of [0.0,1.0] - including the leading zero.

        setCssAll('opacity:.55');

        // The non-literal . in this regex is intentional:
        //   German Chrome returns this value as 0,55
        // github.com/Modernizr/Modernizr/issues/#issue/59/comment/516632
        return (/^0.55$/).test(mStyle.opacity);
    };


    // Note, Android < 4 will pass this test, but can only animate
    //   a single property at a time
    //   goo.gl/v3V4Gp
    tests['cssanimations'] = function() {
        return testPropsAll('animationName');
    };


    tests['csscolumns'] = function() {
        return testPropsAll('columnCount');
    };


    tests['cssgradients'] = function() {
        /**
         * For CSS Gradients syntax, please see:
         * webkit.org/blog/175/introducing-css-gradients/
         * developer.mozilla.org/en/CSS/-moz-linear-gradient
         * developer.mozilla.org/en/CSS/-moz-radial-gradient
         * dev.w3.org/csswg/css3-images/#gradients-
         */

        var str1 = 'background-image:',
            str2 = 'gradient(linear,left top,right bottom,from(#9f9),to(white));',
            str3 = 'linear-gradient(left top,#9f9, white);';

        setCss(
             // legacy webkit syntax (FIXME: remove when syntax not in use anymore)
              (str1 + '-webkit- '.split(' ').join(str2 + str1) +
             // standard syntax             // trailing 'background-image:'
              prefixes.join(str3 + str1)).slice(0, -str1.length)
        );

        return contains(mStyle.backgroundImage, 'gradient');
    };


    tests['cssreflections'] = function() {
        return testPropsAll('boxReflect');
    };


    tests['csstransforms'] = function() {
        return !!testPropsAll('transform');
    };


    tests['csstransforms3d'] = function() {

        var ret = !!testPropsAll('perspective');

        // Webkit's 3D transforms are passed off to the browser's own graphics renderer.
        //   It works fine in Safari on Leopard and Snow Leopard, but not in Chrome in
        //   some conditions. As a result, Webkit typically recognizes the syntax but
        //   will sometimes throw a false positive, thus we must do a more thorough check:
        if ( ret && 'webkitPerspective' in docElement.style ) {

          // Webkit allows this media query to succeed only if the feature is enabled.
          // `@media (transform-3d),(-webkit-transform-3d){ ... }`
          injectElementWithStyles('@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}', function( node, rule ) {
            ret = node.offsetLeft === 9 && node.offsetHeight === 3;
          });
        }
        return ret;
    };


    tests['csstransitions'] = function() {
        return testPropsAll('transition');
    };


    /*>>fontface*/
    // @font-face detection routine by Diego Perini
    // javascript.nwbox.com/CSSSupport/

    // false positives:
    //   WebOS github.com/Modernizr/Modernizr/issues/342
    //   WP7   github.com/Modernizr/Modernizr/issues/538
    tests['fontface'] = function() {
        var bool;

        injectElementWithStyles('@font-face {font-family:"font";src:url("https://")}', function( node, rule ) {
          var style = document.getElementById('smodernizr'),
              sheet = style.sheet || style.styleSheet,
              cssText = sheet ? (sheet.cssRules && sheet.cssRules[0] ? sheet.cssRules[0].cssText : sheet.cssText || '') : '';

          bool = /src/i.test(cssText) && cssText.indexOf(rule.split(' ')[0]) === 0;
        });

        return bool;
    };
    /*>>fontface*/

    // CSS generated content detection
    tests['generatedcontent'] = function() {
        var bool;

        injectElementWithStyles(['#',mod,'{font:0/0 a}#',mod,':after{content:"',smile,'";visibility:hidden;font:3px/1 a}'].join(''), function( node ) {
          bool = node.offsetHeight >= 3;
        });

        return bool;
    };



    // These tests evaluate support of the video/audio elements, as well as
    // testing what types of content they support.
    //
    // We're using the Boolean constructor here, so that we can extend the value
    // e.g.  Modernizr.video     // true
    //       Modernizr.video.ogg // 'probably'
    //
    // Codec values from : github.com/NielsLeenheer/html5test/blob/9106a8/index.html#L845
    //                     thx to NielsLeenheer and zcorpan

    // Note: in some older browsers, "no" was a return value instead of empty string.
    //   It was live in FF3.5.0 and 3.5.1, but fixed in 3.5.2
    //   It was also live in Safari 4.0.0 - 4.0.4, but fixed in 4.0.5

    tests['video'] = function() {
        var elem = document.createElement('video'),
            bool = false;

        // IE9 Running on Windows Server SKU can cause an exception to be thrown, bug #224
        try {
            if ( bool = !!elem.canPlayType ) {
                bool      = new Boolean(bool);
                bool.ogg  = elem.canPlayType('video/ogg; codecs="theora"')      .replace(/^no$/,'');

                // Without QuickTime, this value will be `undefined`. github.com/Modernizr/Modernizr/issues/546
                bool.h264 = elem.canPlayType('video/mp4; codecs="avc1.42E01E"') .replace(/^no$/,'');

                bool.webm = elem.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,'');
            }

        } catch(e) { }

        return bool;
    };

    tests['audio'] = function() {
        var elem = document.createElement('audio'),
            bool = false;

        try {
            if ( bool = !!elem.canPlayType ) {
                bool      = new Boolean(bool);
                bool.ogg  = elem.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,'');
                bool.mp3  = elem.canPlayType('audio/mpeg;')               .replace(/^no$/,'');

                // Mimetypes accepted:
                //   developer.mozilla.org/En/Media_formats_supported_by_the_audio_and_video_elements
                //   bit.ly/iphoneoscodecs
                bool.wav  = elem.canPlayType('audio/wav; codecs="1"')     .replace(/^no$/,'');
                bool.m4a  = ( elem.canPlayType('audio/x-m4a;')            ||
                              elem.canPlayType('audio/aac;'))             .replace(/^no$/,'');
            }
        } catch(e) { }

        return bool;
    };


    // In FF4, if disabled, window.localStorage should === null.

    // Normally, we could not test that directly and need to do a
    //   `('localStorage' in window) && ` test first because otherwise Firefox will
    //   throw bugzil.la/365772 if cookies are disabled

    // Also in iOS5 Private Browsing mode, attempting to use localStorage.setItem
    // will throw the exception:
    //   QUOTA_EXCEEDED_ERRROR DOM Exception 22.
    // Peculiarly, getItem and removeItem calls do not throw.

    // Because we are forced to try/catch this, we'll go aggressive.

    // Just FWIW: IE8 Compat mode supports these features completely:
    //   www.quirksmode.org/dom/html5.html
    // But IE8 doesn't support either with local files

    tests['localstorage'] = function() {
        try {
            localStorage.setItem(mod, mod);
            localStorage.removeItem(mod);
            return true;
        } catch(e) {
            return false;
        }
    };

    tests['sessionstorage'] = function() {
        try {
            sessionStorage.setItem(mod, mod);
            sessionStorage.removeItem(mod);
            return true;
        } catch(e) {
            return false;
        }
    };


    tests['webworkers'] = function() {
        return !!window.Worker;
    };


    tests['applicationcache'] = function() {
        return !!window.applicationCache;
    };


    // Thanks to Erik Dahlstrom
    tests['svg'] = function() {
        return !!document.createElementNS && !!document.createElementNS(ns.svg, 'svg').createSVGRect;
    };

    // specifically for SVG inline in HTML, not within XHTML
    // test page: paulirish.com/demo/inline-svg
    tests['inlinesvg'] = function() {
      var div = document.createElement('div');
      div.innerHTML = '<svg/>';
      return (div.firstChild && div.firstChild.namespaceURI) == ns.svg;
    };

    // SVG SMIL animation
    tests['smil'] = function() {
        return !!document.createElementNS && /SVGAnimate/.test(toString.call(document.createElementNS(ns.svg, 'animate')));
    };

    // This test is only for clip paths in SVG proper, not clip paths on HTML content
    // demo: srufaculty.sru.edu/david.dailey/svg/newstuff/clipPath4.svg

    // However read the comments to dig into applying SVG clippaths to HTML content here:
    //   github.com/Modernizr/Modernizr/issues/213#issuecomment-1149491
    tests['svgclippaths'] = function() {
        return !!document.createElementNS && /SVGClipPath/.test(toString.call(document.createElementNS(ns.svg, 'clipPath')));
    };

    /*>>webforms*/
    // input features and input types go directly onto the ret object, bypassing the tests loop.
    // Hold this guy to execute in a moment.
    function webforms() {
        /*>>input*/
        // Run through HTML5's new input attributes to see if the UA understands any.
        // We're using f which is the <input> element created early on
        // Mike Taylr has created a comprehensive resource for testing these attributes
        //   when applied to all input types:
        //   miketaylr.com/code/input-type-attr.html
        // spec: www.whatwg.org/specs/web-apps/current-work/multipage/the-input-element.html#input-type-attr-summary

        // Only input placeholder is tested while textarea's placeholder is not.
        // Currently Safari 4 and Opera 11 have support only for the input placeholder
        // Both tests are available in feature-detects/forms-placeholder.js
        Modernizr['input'] = (function( props ) {
            for ( var i = 0, len = props.length; i < len; i++ ) {
                attrs[ props[i] ] = !!(props[i] in inputElem);
            }
            if (attrs.list){
              // safari false positive's on datalist: webk.it/74252
              // see also github.com/Modernizr/Modernizr/issues/146
              attrs.list = !!(document.createElement('datalist') && window.HTMLDataListElement);
            }
            return attrs;
        })('autocomplete autofocus list placeholder max min multiple pattern required step'.split(' '));
        /*>>input*/

        /*>>inputtypes*/
        // Run through HTML5's new input types to see if the UA understands any.
        //   This is put behind the tests runloop because it doesn't return a
        //   true/false like all the other tests; instead, it returns an object
        //   containing each input type with its corresponding true/false value

        // Big thanks to @miketaylr for the html5 forms expertise. miketaylr.com/
        Modernizr['inputtypes'] = (function(props) {

            for ( var i = 0, bool, inputElemType, defaultView, len = props.length; i < len; i++ ) {

                inputElem.setAttribute('type', inputElemType = props[i]);
                bool = inputElem.type !== 'text';

                // We first check to see if the type we give it sticks..
                // If the type does, we feed it a textual value, which shouldn't be valid.
                // If the value doesn't stick, we know there's input sanitization which infers a custom UI
                if ( bool ) {

                    inputElem.value         = smile;
                    inputElem.style.cssText = 'position:absolute;visibility:hidden;';

                    if ( /^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined ) {

                      docElement.appendChild(inputElem);
                      defaultView = document.defaultView;

                      // Safari 2-4 allows the smiley as a value, despite making a slider
                      bool =  defaultView.getComputedStyle &&
                              defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' &&
                              // Mobile android web browser has false positive, so must
                              // check the height to see if the widget is actually there.
                              (inputElem.offsetHeight !== 0);

                      docElement.removeChild(inputElem);

                    } else if ( /^(search|tel)$/.test(inputElemType) ){
                      // Spec doesn't define any special parsing or detectable UI
                      //   behaviors so we pass these through as true

                      // Interestingly, opera fails the earlier test, so it doesn't
                      //  even make it here.

                    } else if ( /^(url|email)$/.test(inputElemType) ) {
                      // Real url and email support comes with prebaked validation.
                      bool = inputElem.checkValidity && inputElem.checkValidity() === false;

                    } else {
                      // If the upgraded input compontent rejects the :) text, we got a winner
                      bool = inputElem.value != smile;
                    }
                }

                inputs[ props[i] ] = !!bool;
            }
            return inputs;
        })('search tel url email datetime date month week time datetime-local number range color'.split(' '));
        /*>>inputtypes*/
    }
    /*>>webforms*/


    // End of test definitions
    // -----------------------



    // Run through all tests and detect their support in the current UA.
    // todo: hypothetically we could be doing an array of tests and use a basic loop here.
    for ( var feature in tests ) {
        if ( hasOwnProp(tests, feature) ) {
            // run the test, throw the return value into the Modernizr,
            //   then based on that boolean, define an appropriate className
            //   and push it into an array of classes we'll join later.
            featureName  = feature.toLowerCase();
            Modernizr[featureName] = tests[feature]();

            classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
        }
    }

    /*>>webforms*/
    // input tests need to run.
    Modernizr.input || webforms();
    /*>>webforms*/


    /**
     * addTest allows the user to define their own feature tests
     * the result will be added onto the Modernizr object,
     * as well as an appropriate className set on the html element
     *
     * @param feature - String naming the feature
     * @param test - Function returning true if feature is supported, false if not
     */
     Modernizr.addTest = function ( feature, test ) {
       if ( typeof feature == 'object' ) {
         for ( var key in feature ) {
           if ( hasOwnProp( feature, key ) ) {
             Modernizr.addTest( key, feature[ key ] );
           }
         }
       } else {

         feature = feature.toLowerCase();

         if ( Modernizr[feature] !== undefined ) {
           // we're going to quit if you're trying to overwrite an existing test
           // if we were to allow it, we'd do this:
           //   var re = new RegExp("\\b(no-)?" + feature + "\\b");
           //   docElement.className = docElement.className.replace( re, '' );
           // but, no rly, stuff 'em.
           return Modernizr;
         }

         test = typeof test == 'function' ? test() : test;

         if (typeof enableClasses !== "undefined" && enableClasses) {
           docElement.className += ' ' + (test ? '' : 'no-') + feature;
         }
         Modernizr[feature] = test;

       }

       return Modernizr; // allow chaining.
     };


    // Reset modElem.cssText to nothing to reduce memory footprint.
    setCss('');
    modElem = inputElem = null;

    /*>>shiv*/
    /**
     * @preserve HTML5 Shiv prev3.7.1 | @afarkas @jdalton @jon_neal @rem | MIT/GPL2 Licensed
     */
    ;(function(window, document) {
        /*jshint evil:true */
        /** version */
        var version = '3.7.0';

        /** Preset options */
        var options = window.html5 || {};

        /** Used to skip problem elements */
        var reSkip = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;

        /** Not all elements can be cloned in IE **/
        var saveClones = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;

        /** Detect whether the browser supports default html5 styles */
        var supportsHtml5Styles;

        /** Name of the expando, to work with multiple documents or to re-shiv one document */
        var expando = '_html5shiv';

        /** The id for the the documents expando */
        var expanID = 0;

        /** Cached data for each document */
        var expandoData = {};

        /** Detect whether the browser supports unknown elements */
        var supportsUnknownElements;

        (function() {
          try {
            var a = document.createElement('a');
            a.innerHTML = '<xyz></xyz>';
            //if the hidden property is implemented we can assume, that the browser supports basic HTML5 Styles
            supportsHtml5Styles = ('hidden' in a);

            supportsUnknownElements = a.childNodes.length == 1 || (function() {
              // assign a false positive if unable to shiv
              (document.createElement)('a');
              var frag = document.createDocumentFragment();
              return (
                typeof frag.cloneNode == 'undefined' ||
                typeof frag.createDocumentFragment == 'undefined' ||
                typeof frag.createElement == 'undefined'
              );
            }());
          } catch(e) {
            // assign a false positive if detection fails => unable to shiv
            supportsHtml5Styles = true;
            supportsUnknownElements = true;
          }

        }());

        /*--------------------------------------------------------------------------*/

        /**
         * Creates a style sheet with the given CSS text and adds it to the document.
         * @private
         * @param {Document} ownerDocument The document.
         * @param {String} cssText The CSS text.
         * @returns {StyleSheet} The style element.
         */
        function addStyleSheet(ownerDocument, cssText) {
          var p = ownerDocument.createElement('p'),
          parent = ownerDocument.getElementsByTagName('head')[0] || ownerDocument.documentElement;

          p.innerHTML = 'x<style>' + cssText + '</style>';
          return parent.insertBefore(p.lastChild, parent.firstChild);
        }

        /**
         * Returns the value of `html5.elements` as an array.
         * @private
         * @returns {Array} An array of shived element node names.
         */
        function getElements() {
          var elements = html5.elements;
          return typeof elements == 'string' ? elements.split(' ') : elements;
        }

        /**
         * Returns the data associated to the given document
         * @private
         * @param {Document} ownerDocument The document.
         * @returns {Object} An object of data.
         */
        function getExpandoData(ownerDocument) {
          var data = expandoData[ownerDocument[expando]];
          if (!data) {
            data = {};
            expanID++;
            ownerDocument[expando] = expanID;
            expandoData[expanID] = data;
          }
          return data;
        }

        /**
         * returns a shived element for the given nodeName and document
         * @memberOf html5
         * @param {String} nodeName name of the element
         * @param {Document} ownerDocument The context document.
         * @returns {Object} The shived element.
         */
        function createElement(nodeName, ownerDocument, data){
          if (!ownerDocument) {
            ownerDocument = document;
          }
          if(supportsUnknownElements){
            return ownerDocument.createElement(nodeName);
          }
          if (!data) {
            data = getExpandoData(ownerDocument);
          }
          var node;

          if (data.cache[nodeName]) {
            node = data.cache[nodeName].cloneNode();
          } else if (saveClones.test(nodeName)) {
            node = (data.cache[nodeName] = data.createElem(nodeName)).cloneNode();
          } else {
            node = data.createElem(nodeName);
          }

          // Avoid adding some elements to fragments in IE < 9 because
          // * Attributes like `name` or `type` cannot be set/changed once an element
          //   is inserted into a document/fragment
          // * Link elements with `src` attributes that are inaccessible, as with
          //   a 403 response, will cause the tab/window to crash
          // * Script elements appended to fragments will execute when their `src`
          //   or `text` property is set
          return node.canHaveChildren && !reSkip.test(nodeName) && !node.tagUrn ? data.frag.appendChild(node) : node;
        }

        /**
         * returns a shived DocumentFragment for the given document
         * @memberOf html5
         * @param {Document} ownerDocument The context document.
         * @returns {Object} The shived DocumentFragment.
         */
        function createDocumentFragment(ownerDocument, data){
          if (!ownerDocument) {
            ownerDocument = document;
          }
          if(supportsUnknownElements){
            return ownerDocument.createDocumentFragment();
          }
          data = data || getExpandoData(ownerDocument);
          var clone = data.frag.cloneNode(),
          i = 0,
          elems = getElements(),
          l = elems.length;
          for(;i<l;i++){
            clone.createElement(elems[i]);
          }
          return clone;
        }

        /**
         * Shivs the `createElement` and `createDocumentFragment` methods of the document.
         * @private
         * @param {Document|DocumentFragment} ownerDocument The document.
         * @param {Object} data of the document.
         */
        function shivMethods(ownerDocument, data) {
          if (!data.cache) {
            data.cache = {};
            data.createElem = ownerDocument.createElement;
            data.createFrag = ownerDocument.createDocumentFragment;
            data.frag = data.createFrag();
          }


          ownerDocument.createElement = function(nodeName) {
            //abort shiv
            if (!html5.shivMethods) {
              return data.createElem(nodeName);
            }
            return createElement(nodeName, ownerDocument, data);
          };

          ownerDocument.createDocumentFragment = Function('h,f', 'return function(){' +
                                                          'var n=f.cloneNode(),c=n.createElement;' +
                                                          'h.shivMethods&&(' +
                                                          // unroll the `createElement` calls
                                                          getElements().join().replace(/[\w\-]+/g, function(nodeName) {
            data.createElem(nodeName);
            data.frag.createElement(nodeName);
            return 'c("' + nodeName + '")';
          }) +
            ');return n}'
                                                         )(html5, data.frag);
        }

        /*--------------------------------------------------------------------------*/

        /**
         * Shivs the given document.
         * @memberOf html5
         * @param {Document} ownerDocument The document to shiv.
         * @returns {Document} The shived document.
         */
        function shivDocument(ownerDocument) {
          if (!ownerDocument) {
            ownerDocument = document;
          }
          var data = getExpandoData(ownerDocument);

          if (html5.shivCSS && !supportsHtml5Styles && !data.hasCSS) {
            data.hasCSS = !!addStyleSheet(ownerDocument,
                                          // corrects block display not defined in IE6/7/8/9
                                          'article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}' +
                                            // adds styling not present in IE6/7/8/9
                                            'mark{background:#FF0;color:#000}' +
                                            // hides non-rendered elements
                                            'template{display:none}'
                                         );
          }
          if (!supportsUnknownElements) {
            shivMethods(ownerDocument, data);
          }
          return ownerDocument;
        }

        /*--------------------------------------------------------------------------*/

        /**
         * The `html5` object is exposed so that more elements can be shived and
         * existing shiving can be detected on iframes.
         * @type Object
         * @example
         *
         * // options can be changed before the script is included
         * html5 = { 'elements': 'mark section', 'shivCSS': false, 'shivMethods': false };
         */
        var html5 = {

          /**
           * An array or space separated string of node names of the elements to shiv.
           * @memberOf html5
           * @type Array|String
           */
          'elements': options.elements || 'abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video',

          /**
           * current version of html5shiv
           */
          'version': version,

          /**
           * A flag to indicate that the HTML5 style sheet should be inserted.
           * @memberOf html5
           * @type Boolean
           */
          'shivCSS': (options.shivCSS !== false),

          /**
           * Is equal to true if a browser supports creating unknown/HTML5 elements
           * @memberOf html5
           * @type boolean
           */
          'supportsUnknownElements': supportsUnknownElements,

          /**
           * A flag to indicate that the document's `createElement` and `createDocumentFragment`
           * methods should be overwritten.
           * @memberOf html5
           * @type Boolean
           */
          'shivMethods': (options.shivMethods !== false),

          /**
           * A string to describe the type of `html5` object ("default" or "default print").
           * @memberOf html5
           * @type String
           */
          'type': 'default',

          // shivs the document according to the specified `html5` object options
          'shivDocument': shivDocument,

          //creates a shived element
          createElement: createElement,

          //creates a shived documentFragment
          createDocumentFragment: createDocumentFragment
        };

        /*--------------------------------------------------------------------------*/

        // expose html5
        window.html5 = html5;

        // shiv the document
        shivDocument(document);

    }(this, document));
    /*>>shiv*/

    // Assign private properties to the return object with prefix
    Modernizr._version      = version;

    // expose these for the plugin API. Look in the source for how to join() them against your input
    /*>>prefixes*/
    Modernizr._prefixes     = prefixes;
    /*>>prefixes*/
    /*>>domprefixes*/
    Modernizr._domPrefixes  = domPrefixes;
    Modernizr._cssomPrefixes  = cssomPrefixes;
    /*>>domprefixes*/

    /*>>mq*/
    // Modernizr.mq tests a given media query, live against the current state of the window
    // A few important notes:
    //   * If a browser does not support media queries at all (eg. oldIE) the mq() will always return false
    //   * A max-width or orientation query will be evaluated against the current state, which may change later.
    //   * You must specify values. Eg. If you are testing support for the min-width media query use:
    //       Modernizr.mq('(min-width:0)')
    // usage:
    // Modernizr.mq('only screen and (max-width:768)')
    Modernizr.mq            = testMediaQuery;
    /*>>mq*/

    /*>>hasevent*/
    // Modernizr.hasEvent() detects support for a given event, with an optional element to test on
    // Modernizr.hasEvent('gesturestart', elem)
    Modernizr.hasEvent      = isEventSupported;
    /*>>hasevent*/

    /*>>testprop*/
    // Modernizr.testProp() investigates whether a given style property is recognized
    // Note that the property names must be provided in the camelCase variant.
    // Modernizr.testProp('pointerEvents')
    Modernizr.testProp      = function(prop){
        return testProps([prop]);
    };
    /*>>testprop*/

    /*>>testallprops*/
    // Modernizr.testAllProps() investigates whether a given style property,
    //   or any of its vendor-prefixed variants, is recognized
    // Note that the property names must be provided in the camelCase variant.
    // Modernizr.testAllProps('boxSizing')
    Modernizr.testAllProps  = testPropsAll;
    /*>>testallprops*/


    /*>>teststyles*/
    // Modernizr.testStyles() allows you to add custom styles to the document and test an element afterwards
    // Modernizr.testStyles('#modernizr { position:absolute }', function(elem, rule){ ... })
    Modernizr.testStyles    = injectElementWithStyles;
    /*>>teststyles*/


    /*>>prefixed*/
    // Modernizr.prefixed() returns the prefixed or nonprefixed property name variant of your input
    // Modernizr.prefixed('boxSizing') // 'MozBoxSizing'

    // Properties must be passed as dom-style camelcase, rather than `box-sizing` hypentated style.
    // Return values will also be the camelCase variant, if you need to translate that to hypenated style use:
    //
    //     str.replace(/([A-Z])/g, function(str,m1){ return '-' + m1.toLowerCase(); }).replace(/^ms-/,'-ms-');

    // If you're trying to ascertain which transition end event to bind to, you might do something like...
    //
    //     var transEndEventNames = {
    //       'WebkitTransition' : 'webkitTransitionEnd',
    //       'MozTransition'    : 'transitionend',
    //       'OTransition'      : 'oTransitionEnd',
    //       'msTransition'     : 'MSTransitionEnd',
    //       'transition'       : 'transitionend'
    //     },
    //     transEndEventName = transEndEventNames[ Modernizr.prefixed('transition') ];

    Modernizr.prefixed      = function(prop, obj, elem){
      if(!obj) {
        return testPropsAll(prop, 'pfx');
      } else {
        // Testing DOM property e.g. Modernizr.prefixed('requestAnimationFrame', window) // 'mozRequestAnimationFrame'
        return testPropsAll(prop, obj, elem);
      }
    };
    /*>>prefixed*/


    /*>>cssclasses*/
    // Remove "no-js" class from <html> element, if it exists:
    docElement.className = docElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +

                            // Add the new classes to the <html> element.
                            (enableClasses ? ' js ' + classes.join(' ') : '');
    /*>>cssclasses*/

    return Modernizr;

})(this, this.document);
;/*!
 * Bootstrap v3.2.0 (http://getbootstrap.com)
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

if (typeof jQuery === 'undefined') { throw new Error('Bootstrap\'s JavaScript requires jQuery') }

/* ========================================================================
 * Bootstrap: transition.js v3.2.0
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('bsTransitionEnd', function () { called = true })
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition) return

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: alert.js v3.2.0
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // ALERT CLASS DEFINITION
  // ======================

  var dismiss = '[data-dismiss="alert"]'
  var Alert   = function (el) {
    $(el).on('click', dismiss, this.close)
  }

  Alert.VERSION = '3.2.0'

  Alert.prototype.close = function (e) {
    var $this    = $(this)
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = $(selector)

    if (e) e.preventDefault()

    if (!$parent.length) {
      $parent = $this.hasClass('alert') ? $this : $this.parent()
    }

    $parent.trigger(e = $.Event('close.bs.alert'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      // detach from parent, fire event then clean up data
      $parent.detach().trigger('closed.bs.alert').remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent
        .one('bsTransitionEnd', removeElement)
        .emulateTransitionEnd(150) :
      removeElement()
  }


  // ALERT PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.alert')

      if (!data) $this.data('bs.alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.alert

  $.fn.alert             = Plugin
  $.fn.alert.Constructor = Alert


  // ALERT NO CONFLICT
  // =================

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


  // ALERT DATA-API
  // ==============

  $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

}(jQuery);

/* ========================================================================
 * Bootstrap: button.js v3.2.0
 * http://getbootstrap.com/javascript/#buttons
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // BUTTON PUBLIC CLASS DEFINITION
  // ==============================

  var Button = function (element, options) {
    this.$element  = $(element)
    this.options   = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
  }

  Button.VERSION  = '3.2.0'

  Button.DEFAULTS = {
    loadingText: 'loading...'
  }

  Button.prototype.setState = function (state) {
    var d    = 'disabled'
    var $el  = this.$element
    var val  = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state = state + 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    $el[val](data[state] == null ? this.options[state] : data[state])

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function () {
      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d)
      }
    }, this), 0)
  }

  Button.prototype.toggle = function () {
    var changed = true
    var $parent = this.$element.closest('[data-toggle="buttons"]')

    if ($parent.length) {
      var $input = this.$element.find('input')
      if ($input.prop('type') == 'radio') {
        if ($input.prop('checked') && this.$element.hasClass('active')) changed = false
        else $parent.find('.active').removeClass('active')
      }
      if (changed) $input.prop('checked', !this.$element.hasClass('active')).trigger('change')
    }

    if (changed) this.$element.toggleClass('active')
  }


  // BUTTON PLUGIN DEFINITION
  // ========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.button')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.button', (data = new Button(this, options)))

      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  var old = $.fn.button

  $.fn.button             = Plugin
  $.fn.button.Constructor = Button


  // BUTTON NO CONFLICT
  // ==================

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }


  // BUTTON DATA-API
  // ===============

  $(document).on('click.bs.button.data-api', '[data-toggle^="button"]', function (e) {
    var $btn = $(e.target)
    if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
    Plugin.call($btn, 'toggle')
    e.preventDefault()
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: carousel.js v3.2.0
 * http://getbootstrap.com/javascript/#carousel
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CAROUSEL CLASS DEFINITION
  // =========================

  var Carousel = function (element, options) {
    this.$element    = $(element).on('keydown.bs.carousel', $.proxy(this.keydown, this))
    this.$indicators = this.$element.find('.carousel-indicators')
    this.options     = options
    this.paused      =
    this.sliding     =
    this.interval    =
    this.$active     =
    this.$items      = null

    this.options.pause == 'hover' && this.$element
      .on('mouseenter.bs.carousel', $.proxy(this.pause, this))
      .on('mouseleave.bs.carousel', $.proxy(this.cycle, this))
  }

  Carousel.VERSION  = '3.2.0'

  Carousel.DEFAULTS = {
    interval: 5000,
    pause: 'hover',
    wrap: true
  }

  Carousel.prototype.keydown = function (e) {
    switch (e.which) {
      case 37: this.prev(); break
      case 39: this.next(); break
      default: return
    }

    e.preventDefault()
  }

  Carousel.prototype.cycle = function (e) {
    e || (this.paused = false)

    this.interval && clearInterval(this.interval)

    this.options.interval
      && !this.paused
      && (this.interval = setInterval($.proxy(this.next, this), this.options.interval))

    return this
  }

  Carousel.prototype.getItemIndex = function (item) {
    this.$items = item.parent().children('.item')
    return this.$items.index(item || this.$active)
  }

  Carousel.prototype.to = function (pos) {
    var that        = this
    var activeIndex = this.getItemIndex(this.$active = this.$element.find('.item.active'))

    if (pos > (this.$items.length - 1) || pos < 0) return

    if (this.sliding)       return this.$element.one('slid.bs.carousel', function () { that.to(pos) }) // yes, "slid"
    if (activeIndex == pos) return this.pause().cycle()

    return this.slide(pos > activeIndex ? 'next' : 'prev', $(this.$items[pos]))
  }

  Carousel.prototype.pause = function (e) {
    e || (this.paused = true)

    if (this.$element.find('.next, .prev').length && $.support.transition) {
      this.$element.trigger($.support.transition.end)
      this.cycle(true)
    }

    this.interval = clearInterval(this.interval)

    return this
  }

  Carousel.prototype.next = function () {
    if (this.sliding) return
    return this.slide('next')
  }

  Carousel.prototype.prev = function () {
    if (this.sliding) return
    return this.slide('prev')
  }

  Carousel.prototype.slide = function (type, next) {
    var $active   = this.$element.find('.item.active')
    var $next     = next || $active[type]()
    var isCycling = this.interval
    var direction = type == 'next' ? 'left' : 'right'
    var fallback  = type == 'next' ? 'first' : 'last'
    var that      = this

    if (!$next.length) {
      if (!this.options.wrap) return
      $next = this.$element.find('.item')[fallback]()
    }

    if ($next.hasClass('active')) return (this.sliding = false)

    var relatedTarget = $next[0]
    var slideEvent = $.Event('slide.bs.carousel', {
      relatedTarget: relatedTarget,
      direction: direction
    })
    this.$element.trigger(slideEvent)
    if (slideEvent.isDefaultPrevented()) return

    this.sliding = true

    isCycling && this.pause()

    if (this.$indicators.length) {
      this.$indicators.find('.active').removeClass('active')
      var $nextIndicator = $(this.$indicators.children()[this.getItemIndex($next)])
      $nextIndicator && $nextIndicator.addClass('active')
    }

    var slidEvent = $.Event('slid.bs.carousel', { relatedTarget: relatedTarget, direction: direction }) // yes, "slid"
    if ($.support.transition && this.$element.hasClass('slide')) {
      $next.addClass(type)
      $next[0].offsetWidth // force reflow
      $active.addClass(direction)
      $next.addClass(direction)
      $active
        .one('bsTransitionEnd', function () {
          $next.removeClass([type, direction].join(' ')).addClass('active')
          $active.removeClass(['active', direction].join(' '))
          that.sliding = false
          setTimeout(function () {
            that.$element.trigger(slidEvent)
          }, 0)
        })
        .emulateTransitionEnd($active.css('transition-duration').slice(0, -1) * 1000)
    } else {
      $active.removeClass('active')
      $next.addClass('active')
      this.sliding = false
      this.$element.trigger(slidEvent)
    }

    isCycling && this.cycle()

    return this
  }


  // CAROUSEL PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.carousel')
      var options = $.extend({}, Carousel.DEFAULTS, $this.data(), typeof option == 'object' && option)
      var action  = typeof option == 'string' ? option : options.slide

      if (!data) $this.data('bs.carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action]()
      else if (options.interval) data.pause().cycle()
    })
  }

  var old = $.fn.carousel

  $.fn.carousel             = Plugin
  $.fn.carousel.Constructor = Carousel


  // CAROUSEL NO CONFLICT
  // ====================

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old
    return this
  }


  // CAROUSEL DATA-API
  // =================

  $(document).on('click.bs.carousel.data-api', '[data-slide], [data-slide-to]', function (e) {
    var href
    var $this   = $(this)
    var $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) // strip for ie7
    if (!$target.hasClass('carousel')) return
    var options = $.extend({}, $target.data(), $this.data())
    var slideIndex = $this.attr('data-slide-to')
    if (slideIndex) options.interval = false

    Plugin.call($target, options)

    if (slideIndex) {
      $target.data('bs.carousel').to(slideIndex)
    }

    e.preventDefault()
  })

  $(window).on('load', function () {
    $('[data-ride="carousel"]').each(function () {
      var $carousel = $(this)
      Plugin.call($carousel, $carousel.data())
    })
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: collapse.js v3.2.0
 * http://getbootstrap.com/javascript/#collapse
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // COLLAPSE PUBLIC CLASS DEFINITION
  // ================================

  var Collapse = function (element, options) {
    this.$element      = $(element)
    this.options       = $.extend({}, Collapse.DEFAULTS, options)
    this.transitioning = null

    if (this.options.parent) this.$parent = $(this.options.parent)
    if (this.options.toggle) this.toggle()
  }

  Collapse.VERSION  = '3.2.0'

  Collapse.DEFAULTS = {
    toggle: true
  }

  Collapse.prototype.dimension = function () {
    var hasWidth = this.$element.hasClass('width')
    return hasWidth ? 'width' : 'height'
  }

  Collapse.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('in')) return

    var startEvent = $.Event('show.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var actives = this.$parent && this.$parent.find('> .panel > .in')

    if (actives && actives.length) {
      var hasData = actives.data('bs.collapse')
      if (hasData && hasData.transitioning) return
      Plugin.call(actives, 'hide')
      hasData || actives.data('bs.collapse', null)
    }

    var dimension = this.dimension()

    this.$element
      .removeClass('collapse')
      .addClass('collapsing')[dimension](0)

    this.transitioning = 1

    var complete = function () {
      this.$element
        .removeClass('collapsing')
        .addClass('collapse in')[dimension]('')
      this.transitioning = 0
      this.$element
        .trigger('shown.bs.collapse')
    }

    if (!$.support.transition) return complete.call(this)

    var scrollSize = $.camelCase(['scroll', dimension].join('-'))

    this.$element
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(350)[dimension](this.$element[0][scrollSize])
  }

  Collapse.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('in')) return

    var startEvent = $.Event('hide.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var dimension = this.dimension()

    this.$element[dimension](this.$element[dimension]())[0].offsetHeight

    this.$element
      .addClass('collapsing')
      .removeClass('collapse')
      .removeClass('in')

    this.transitioning = 1

    var complete = function () {
      this.transitioning = 0
      this.$element
        .trigger('hidden.bs.collapse')
        .removeClass('collapsing')
        .addClass('collapse')
    }

    if (!$.support.transition) return complete.call(this)

    this.$element
      [dimension](0)
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(350)
  }

  Collapse.prototype.toggle = function () {
    this[this.$element.hasClass('in') ? 'hide' : 'show']()
  }


  // COLLAPSE PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.collapse')
      var options = $.extend({}, Collapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data && options.toggle && option == 'show') option = !option
      if (!data) $this.data('bs.collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.collapse

  $.fn.collapse             = Plugin
  $.fn.collapse.Constructor = Collapse


  // COLLAPSE NO CONFLICT
  // ====================

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


  // COLLAPSE DATA-API
  // =================

  $(document).on('click.bs.collapse.data-api', '[data-toggle="collapse"]', function (e) {
    var href
    var $this   = $(this)
    var target  = $this.attr('data-target')
        || e.preventDefault()
        || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7
    var $target = $(target)
    var data    = $target.data('bs.collapse')
    var option  = data ? 'toggle' : $this.data()
    var parent  = $this.attr('data-parent')
    var $parent = parent && $(parent)

    if (!data || !data.transitioning) {
      if ($parent) $parent.find('[data-toggle="collapse"][data-parent="' + parent + '"]').not($this).addClass('collapsed')
      $this[$target.hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
    }

    Plugin.call($target, option)
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: dropdown.js v3.2.0
 * http://getbootstrap.com/javascript/#dropdowns
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // DROPDOWN CLASS DEFINITION
  // =========================

  var backdrop = '.dropdown-backdrop'
  var toggle   = '[data-toggle="dropdown"]'
  var Dropdown = function (element) {
    $(element).on('click.bs.dropdown', this.toggle)
  }

  Dropdown.VERSION = '3.2.0'

  Dropdown.prototype.toggle = function (e) {
    var $this = $(this)

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    clearMenus()

    if (!isActive) {
      if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
        // if mobile we use a backdrop because click events don't delegate
        $('<div class="dropdown-backdrop"/>').insertAfter($(this)).on('click', clearMenus)
      }

      var relatedTarget = { relatedTarget: this }
      $parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this.trigger('focus')

      $parent
        .toggleClass('open')
        .trigger('shown.bs.dropdown', relatedTarget)
    }

    return false
  }

  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27)/.test(e.keyCode)) return

    var $this = $(this)

    e.preventDefault()
    e.stopPropagation()

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    if (!isActive || (isActive && e.keyCode == 27)) {
      if (e.which == 27) $parent.find(toggle).trigger('focus')
      return $this.trigger('click')
    }

    var desc = ' li:not(.divider):visible a'
    var $items = $parent.find('[role="menu"]' + desc + ', [role="listbox"]' + desc)

    if (!$items.length) return

    var index = $items.index($items.filter(':focus'))

    if (e.keyCode == 38 && index > 0)                 index--                        // up
    if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
    if (!~index)                                      index = 0

    $items.eq(index).trigger('focus')
  }

  function clearMenus(e) {
    if (e && e.which === 3) return
    $(backdrop).remove()
    $(toggle).each(function () {
      var $parent = getParent($(this))
      var relatedTarget = { relatedTarget: this }
      if (!$parent.hasClass('open')) return
      $parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))
      if (e.isDefaultPrevented()) return
      $parent.removeClass('open').trigger('hidden.bs.dropdown', relatedTarget)
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = selector && $(selector)

    return $parent && $parent.length ? $parent : $this.parent()
  }


  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.dropdown')

      if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.dropdown

  $.fn.dropdown             = Plugin
  $.fn.dropdown.Constructor = Dropdown


  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document)
    .on('click.bs.dropdown.data-api', clearMenus)
    .on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    .on('keydown.bs.dropdown.data-api', toggle + ', [role="menu"], [role="listbox"]', Dropdown.prototype.keydown)

}(jQuery);

/* ========================================================================
 * Bootstrap: modal.js v3.2.0
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Modal = function (element, options) {
    this.options        = options
    this.$body          = $(document.body)
    this.$element       = $(element)
    this.$backdrop      =
    this.isShown        = null
    this.scrollbarWidth = 0

    if (this.options.remote) {
      this.$element
        .find('.modal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.modal')
        }, this))
    }
  }

  Modal.VERSION  = '3.2.0'

  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  Modal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.$body.addClass('modal-open')

    this.setScrollbar()
    this.escape()

    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element
        .addClass('in')
        .attr('aria-hidden', false)

      that.enforceFocus()

      var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

      transition ?
        that.$element.find('.modal-dialog') // wait for modal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(300) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.$body.removeClass('modal-open')

    this.resetScrollbar()
    this.escape()

    $(document).off('focusin.bs.modal')

    this.$element
      .removeClass('in')
      .attr('aria-hidden', true)
      .off('click.dismiss.bs.modal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideModal, this))
        .emulateTransitionEnd(300) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.modal') // guard against infinite focus loop
      .on('focusin.bs.modal', $.proxy(function (e) {
        if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keyup.dismiss.bs.modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keyup.dismiss.bs.modal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$element.trigger('hidden.bs.modal')
    })
  }

  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Modal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus.call(this.$element[0])
          : this.hide.call(this)
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(150) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(150) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  Modal.prototype.checkScrollbar = function () {
    if (document.body.clientWidth >= window.innerWidth) return
    this.scrollbarWidth = this.scrollbarWidth || this.measureScrollbar()
  }

  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    if (this.scrollbarWidth) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  Modal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', '')
  }

  Modal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'modal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.modal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.modal

  $.fn.modal             = Plugin
  $.fn.modal.Constructor = Modal


  // MODAL NO CONFLICT
  // =================

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.modal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
      $target.one('hidden.bs.modal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: tooltip.js v3.2.0
 * http://getbootstrap.com/javascript/#tooltip
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // TOOLTIP PUBLIC CLASS DEFINITION
  // ===============================

  var Tooltip = function (element, options) {
    this.type       =
    this.options    =
    this.enabled    =
    this.timeout    =
    this.hoverState =
    this.$element   = null

    this.init('tooltip', element, options)
  }

  Tooltip.VERSION  = '3.2.0'

  Tooltip.DEFAULTS = {
    animation: true,
    placement: 'top',
    selector: false,
    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    container: false,
    viewport: {
      selector: 'body',
      padding: 0
    }
  }

  Tooltip.prototype.init = function (type, element, options) {
    this.enabled   = true
    this.type      = type
    this.$element  = $(element)
    this.options   = this.getOptions(options)
    this.$viewport = this.options.viewport && $(this.options.viewport.selector || this.options.viewport)

    var triggers = this.options.trigger.split(' ')

    for (var i = triggers.length; i--;) {
      var trigger = triggers[i]

      if (trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (trigger != 'manual') {
        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin'
        var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout'

        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }
    }

    this.options.selector ?
      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
      this.fixTitle()
  }

  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS
  }

  Tooltip.prototype.getOptions = function (options) {
    options = $.extend({}, this.getDefaults(), this.$element.data(), options)

    if (options.delay && typeof options.delay == 'number') {
      options.delay = {
        show: options.delay,
        hide: options.delay
      }
    }

    return options
  }

  Tooltip.prototype.getDelegateOptions = function () {
    var options  = {}
    var defaults = this.getDefaults()

    this._options && $.each(this._options, function (key, value) {
      if (defaults[key] != value) options[key] = value
    })

    return options
  }

  Tooltip.prototype.enter = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    clearTimeout(self.timeout)

    self.hoverState = 'in'

    if (!self.options.delay || !self.options.delay.show) return self.show()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'in') self.show()
    }, self.options.delay.show)
  }

  Tooltip.prototype.leave = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    clearTimeout(self.timeout)

    self.hoverState = 'out'

    if (!self.options.delay || !self.options.delay.hide) return self.hide()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'out') self.hide()
    }, self.options.delay.hide)
  }

  Tooltip.prototype.show = function () {
    var e = $.Event('show.bs.' + this.type)

    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e)

      var inDom = $.contains(document.documentElement, this.$element[0])
      if (e.isDefaultPrevented() || !inDom) return
      var that = this

      var $tip = this.tip()

      var tipId = this.getUID(this.type)

      this.setContent()
      $tip.attr('id', tipId)
      this.$element.attr('aria-describedby', tipId)

      if (this.options.animation) $tip.addClass('fade')

      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement

      var autoToken = /\s?auto?\s?/i
      var autoPlace = autoToken.test(placement)
      if (autoPlace) placement = placement.replace(autoToken, '') || 'top'

      $tip
        .detach()
        .css({ top: 0, left: 0, display: 'block' })
        .addClass(placement)
        .data('bs.' + this.type, this)

      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

      var pos          = this.getPosition()
      var actualWidth  = $tip[0].offsetWidth
      var actualHeight = $tip[0].offsetHeight

      if (autoPlace) {
        var orgPlacement = placement
        var $parent      = this.$element.parent()
        var parentDim    = this.getPosition($parent)

        placement = placement == 'bottom' && pos.top   + pos.height       + actualHeight - parentDim.scroll > parentDim.height ? 'top'    :
                    placement == 'top'    && pos.top   - parentDim.scroll - actualHeight < 0                                   ? 'bottom' :
                    placement == 'right'  && pos.right + actualWidth      > parentDim.width                                    ? 'left'   :
                    placement == 'left'   && pos.left  - actualWidth      < parentDim.left                                     ? 'right'  :
                    placement

        $tip
          .removeClass(orgPlacement)
          .addClass(placement)
      }

      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

      this.applyPlacement(calculatedOffset, placement)

      var complete = function () {
        that.$element.trigger('shown.bs.' + that.type)
        that.hoverState = null
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        $tip
          .one('bsTransitionEnd', complete)
          .emulateTransitionEnd(150) :
        complete()
    }
  }

  Tooltip.prototype.applyPlacement = function (offset, placement) {
    var $tip   = this.tip()
    var width  = $tip[0].offsetWidth
    var height = $tip[0].offsetHeight

    // manually read margins because getBoundingClientRect includes difference
    var marginTop = parseInt($tip.css('margin-top'), 10)
    var marginLeft = parseInt($tip.css('margin-left'), 10)

    // we must check for NaN for ie 8/9
    if (isNaN(marginTop))  marginTop  = 0
    if (isNaN(marginLeft)) marginLeft = 0

    offset.top  = offset.top  + marginTop
    offset.left = offset.left + marginLeft

    // $.fn.offset doesn't round pixel values
    // so we use setOffset directly with our own function B-0
    $.offset.setOffset($tip[0], $.extend({
      using: function (props) {
        $tip.css({
          top: Math.round(props.top),
          left: Math.round(props.left)
        })
      }
    }, offset), 0)

    $tip.addClass('in')

    // check to see if placing tip in new offset caused the tip to resize itself
    var actualWidth  = $tip[0].offsetWidth
    var actualHeight = $tip[0].offsetHeight

    if (placement == 'top' && actualHeight != height) {
      offset.top = offset.top + height - actualHeight
    }

    var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight)

    if (delta.left) offset.left += delta.left
    else offset.top += delta.top

    var arrowDelta          = delta.left ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight
    var arrowPosition       = delta.left ? 'left'        : 'top'
    var arrowOffsetPosition = delta.left ? 'offsetWidth' : 'offsetHeight'

    $tip.offset(offset)
    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], arrowPosition)
  }

  Tooltip.prototype.replaceArrow = function (delta, dimension, position) {
    this.arrow().css(position, delta ? (50 * (1 - delta / dimension) + '%') : '')
  }

  Tooltip.prototype.setContent = function () {
    var $tip  = this.tip()
    var title = this.getTitle()

    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
    $tip.removeClass('fade in top bottom left right')
  }

  Tooltip.prototype.hide = function () {
    var that = this
    var $tip = this.tip()
    var e    = $.Event('hide.bs.' + this.type)

    this.$element.removeAttr('aria-describedby')

    function complete() {
      if (that.hoverState != 'in') $tip.detach()
      that.$element.trigger('hidden.bs.' + that.type)
    }

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    $tip.removeClass('in')

    $.support.transition && this.$tip.hasClass('fade') ?
      $tip
        .one('bsTransitionEnd', complete)
        .emulateTransitionEnd(150) :
      complete()

    this.hoverState = null

    return this
  }

  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element
    if ($e.attr('title') || typeof ($e.attr('data-original-title')) != 'string') {
      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
    }
  }

  Tooltip.prototype.hasContent = function () {
    return this.getTitle()
  }

  Tooltip.prototype.getPosition = function ($element) {
    $element   = $element || this.$element
    var el     = $element[0]
    var isBody = el.tagName == 'BODY'
    return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : null, {
      scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop(),
      width:  isBody ? $(window).width()  : $element.outerWidth(),
      height: isBody ? $(window).height() : $element.outerHeight()
    }, isBody ? { top: 0, left: 0 } : $element.offset())
  }

  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2  } :
           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2  } :
           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width   }

  }

  Tooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
    var delta = { top: 0, left: 0 }
    if (!this.$viewport) return delta

    var viewportPadding = this.options.viewport && this.options.viewport.padding || 0
    var viewportDimensions = this.getPosition(this.$viewport)

    if (/right|left/.test(placement)) {
      var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll
      var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight
      if (topEdgeOffset < viewportDimensions.top) { // top overflow
        delta.top = viewportDimensions.top - topEdgeOffset
      } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
        delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
      }
    } else {
      var leftEdgeOffset  = pos.left - viewportPadding
      var rightEdgeOffset = pos.left + viewportPadding + actualWidth
      if (leftEdgeOffset < viewportDimensions.left) { // left overflow
        delta.left = viewportDimensions.left - leftEdgeOffset
      } else if (rightEdgeOffset > viewportDimensions.width) { // right overflow
        delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
      }
    }

    return delta
  }

  Tooltip.prototype.getTitle = function () {
    var title
    var $e = this.$element
    var o  = this.options

    title = $e.attr('data-original-title')
      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

    return title
  }

  Tooltip.prototype.getUID = function (prefix) {
    do prefix += ~~(Math.random() * 1000000)
    while (document.getElementById(prefix))
    return prefix
  }

  Tooltip.prototype.tip = function () {
    return (this.$tip = this.$tip || $(this.options.template))
  }

  Tooltip.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'))
  }

  Tooltip.prototype.validate = function () {
    if (!this.$element[0].parentNode) {
      this.hide()
      this.$element = null
      this.options  = null
    }
  }

  Tooltip.prototype.enable = function () {
    this.enabled = true
  }

  Tooltip.prototype.disable = function () {
    this.enabled = false
  }

  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled
  }

  Tooltip.prototype.toggle = function (e) {
    var self = this
    if (e) {
      self = $(e.currentTarget).data('bs.' + this.type)
      if (!self) {
        self = new this.constructor(e.currentTarget, this.getDelegateOptions())
        $(e.currentTarget).data('bs.' + this.type, self)
      }
    }

    self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
  }

  Tooltip.prototype.destroy = function () {
    clearTimeout(this.timeout)
    this.hide().$element.off('.' + this.type).removeData('bs.' + this.type)
  }


  // TOOLTIP PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.tooltip')
      var options = typeof option == 'object' && option

      if (!data && option == 'destroy') return
      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tooltip

  $.fn.tooltip             = Plugin
  $.fn.tooltip.Constructor = Tooltip


  // TOOLTIP NO CONFLICT
  // ===================

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(jQuery);

/* ========================================================================
 * Bootstrap: popover.js v3.2.0
 * http://getbootstrap.com/javascript/#popovers
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // POPOVER PUBLIC CLASS DEFINITION
  // ===============================

  var Popover = function (element, options) {
    this.init('popover', element, options)
  }

  if (!$.fn.tooltip) throw new Error('Popover requires tooltip.js')

  Popover.VERSION  = '3.2.0'

  Popover.DEFAULTS = $.extend({}, $.fn.tooltip.Constructor.DEFAULTS, {
    placement: 'right',
    trigger: 'click',
    content: '',
    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
  })


  // NOTE: POPOVER EXTENDS tooltip.js
  // ================================

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype)

  Popover.prototype.constructor = Popover

  Popover.prototype.getDefaults = function () {
    return Popover.DEFAULTS
  }

  Popover.prototype.setContent = function () {
    var $tip    = this.tip()
    var title   = this.getTitle()
    var content = this.getContent()

    $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
    $tip.find('.popover-content').empty()[ // we use append for html objects to maintain js events
      this.options.html ? (typeof content == 'string' ? 'html' : 'append') : 'text'
    ](content)

    $tip.removeClass('fade top bottom left right in')

    // IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
    // this manually by checking the contents.
    if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide()
  }

  Popover.prototype.hasContent = function () {
    return this.getTitle() || this.getContent()
  }

  Popover.prototype.getContent = function () {
    var $e = this.$element
    var o  = this.options

    return $e.attr('data-content')
      || (typeof o.content == 'function' ?
            o.content.call($e[0]) :
            o.content)
  }

  Popover.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.arrow'))
  }

  Popover.prototype.tip = function () {
    if (!this.$tip) this.$tip = $(this.options.template)
    return this.$tip
  }


  // POPOVER PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.popover')
      var options = typeof option == 'object' && option

      if (!data && option == 'destroy') return
      if (!data) $this.data('bs.popover', (data = new Popover(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.popover

  $.fn.popover             = Plugin
  $.fn.popover.Constructor = Popover


  // POPOVER NO CONFLICT
  // ===================

  $.fn.popover.noConflict = function () {
    $.fn.popover = old
    return this
  }

}(jQuery);

/* ========================================================================
 * Bootstrap: scrollspy.js v3.2.0
 * http://getbootstrap.com/javascript/#scrollspy
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // SCROLLSPY CLASS DEFINITION
  // ==========================

  function ScrollSpy(element, options) {
    var process  = $.proxy(this.process, this)

    this.$body          = $('body')
    this.$scrollElement = $(element).is('body') ? $(window) : $(element)
    this.options        = $.extend({}, ScrollSpy.DEFAULTS, options)
    this.selector       = (this.options.target || '') + ' .nav li > a'
    this.offsets        = []
    this.targets        = []
    this.activeTarget   = null
    this.scrollHeight   = 0

    this.$scrollElement.on('scroll.bs.scrollspy', process)
    this.refresh()
    this.process()
  }

  ScrollSpy.VERSION  = '3.2.0'

  ScrollSpy.DEFAULTS = {
    offset: 10
  }

  ScrollSpy.prototype.getScrollHeight = function () {
    return this.$scrollElement[0].scrollHeight || Math.max(this.$body[0].scrollHeight, document.documentElement.scrollHeight)
  }

  ScrollSpy.prototype.refresh = function () {
    var offsetMethod = 'offset'
    var offsetBase   = 0

    if (!$.isWindow(this.$scrollElement[0])) {
      offsetMethod = 'position'
      offsetBase   = this.$scrollElement.scrollTop()
    }

    this.offsets = []
    this.targets = []
    this.scrollHeight = this.getScrollHeight()

    var self     = this

    this.$body
      .find(this.selector)
      .map(function () {
        var $el   = $(this)
        var href  = $el.data('target') || $el.attr('href')
        var $href = /^#./.test(href) && $(href)

        return ($href
          && $href.length
          && $href.is(':visible')
          && [[$href[offsetMethod]().top + offsetBase, href]]) || null
      })
      .sort(function (a, b) { return a[0] - b[0] })
      .each(function () {
        self.offsets.push(this[0])
        self.targets.push(this[1])
      })
  }

  ScrollSpy.prototype.process = function () {
    var scrollTop    = this.$scrollElement.scrollTop() + this.options.offset
    var scrollHeight = this.getScrollHeight()
    var maxScroll    = this.options.offset + scrollHeight - this.$scrollElement.height()
    var offsets      = this.offsets
    var targets      = this.targets
    var activeTarget = this.activeTarget
    var i

    if (this.scrollHeight != scrollHeight) {
      this.refresh()
    }

    if (scrollTop >= maxScroll) {
      return activeTarget != (i = targets[targets.length - 1]) && this.activate(i)
    }

    if (activeTarget && scrollTop <= offsets[0]) {
      return activeTarget != (i = targets[0]) && this.activate(i)
    }

    for (i = offsets.length; i--;) {
      activeTarget != targets[i]
        && scrollTop >= offsets[i]
        && (!offsets[i + 1] || scrollTop <= offsets[i + 1])
        && this.activate(targets[i])
    }
  }

  ScrollSpy.prototype.activate = function (target) {
    this.activeTarget = target

    $(this.selector)
      .parentsUntil(this.options.target, '.active')
      .removeClass('active')

    var selector = this.selector +
        '[data-target="' + target + '"],' +
        this.selector + '[href="' + target + '"]'

    var active = $(selector)
      .parents('li')
      .addClass('active')

    if (active.parent('.dropdown-menu').length) {
      active = active
        .closest('li.dropdown')
        .addClass('active')
    }

    active.trigger('activate.bs.scrollspy')
  }


  // SCROLLSPY PLUGIN DEFINITION
  // ===========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.scrollspy')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.scrollspy', (data = new ScrollSpy(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.scrollspy

  $.fn.scrollspy             = Plugin
  $.fn.scrollspy.Constructor = ScrollSpy


  // SCROLLSPY NO CONFLICT
  // =====================

  $.fn.scrollspy.noConflict = function () {
    $.fn.scrollspy = old
    return this
  }


  // SCROLLSPY DATA-API
  // ==================

  $(window).on('load.bs.scrollspy.data-api', function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this)
      Plugin.call($spy, $spy.data())
    })
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: tab.js v3.2.0
 * http://getbootstrap.com/javascript/#tabs
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // TAB CLASS DEFINITION
  // ====================

  var Tab = function (element) {
    this.element = $(element)
  }

  Tab.VERSION = '3.2.0'

  Tab.prototype.show = function () {
    var $this    = this.element
    var $ul      = $this.closest('ul:not(.dropdown-menu)')
    var selector = $this.data('target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    if ($this.parent('li').hasClass('active')) return

    var previous = $ul.find('.active:last a')[0]
    var e        = $.Event('show.bs.tab', {
      relatedTarget: previous
    })

    $this.trigger(e)

    if (e.isDefaultPrevented()) return

    var $target = $(selector)

    this.activate($this.closest('li'), $ul)
    this.activate($target, $target.parent(), function () {
      $this.trigger({
        type: 'shown.bs.tab',
        relatedTarget: previous
      })
    })
  }

  Tab.prototype.activate = function (element, container, callback) {
    var $active    = container.find('> .active')
    var transition = callback
      && $.support.transition
      && $active.hasClass('fade')

    function next() {
      $active
        .removeClass('active')
        .find('> .dropdown-menu > .active')
        .removeClass('active')

      element.addClass('active')

      if (transition) {
        element[0].offsetWidth // reflow for transition
        element.addClass('in')
      } else {
        element.removeClass('fade')
      }

      if (element.parent('.dropdown-menu')) {
        element.closest('li.dropdown').addClass('active')
      }

      callback && callback()
    }

    transition ?
      $active
        .one('bsTransitionEnd', next)
        .emulateTransitionEnd(150) :
      next()

    $active.removeClass('in')
  }


  // TAB PLUGIN DEFINITION
  // =====================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.tab')

      if (!data) $this.data('bs.tab', (data = new Tab(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tab

  $.fn.tab             = Plugin
  $.fn.tab.Constructor = Tab


  // TAB NO CONFLICT
  // ===============

  $.fn.tab.noConflict = function () {
    $.fn.tab = old
    return this
  }


  // TAB DATA-API
  // ============

  $(document).on('click.bs.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
    e.preventDefault()
    Plugin.call($(this), 'show')
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: affix.js v3.2.0
 * http://getbootstrap.com/javascript/#affix
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // AFFIX CLASS DEFINITION
  // ======================

  var Affix = function (element, options) {
    this.options = $.extend({}, Affix.DEFAULTS, options)

    this.$target = $(this.options.target)
      .on('scroll.bs.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.bs.affix.data-api',  $.proxy(this.checkPositionWithEventLoop, this))

    this.$element     = $(element)
    this.affixed      =
    this.unpin        =
    this.pinnedOffset = null

    this.checkPosition()
  }

  Affix.VERSION  = '3.2.0'

  Affix.RESET    = 'affix affix-top affix-bottom'

  Affix.DEFAULTS = {
    offset: 0,
    target: window
  }

  Affix.prototype.getPinnedOffset = function () {
    if (this.pinnedOffset) return this.pinnedOffset
    this.$element.removeClass(Affix.RESET).addClass('affix')
    var scrollTop = this.$target.scrollTop()
    var position  = this.$element.offset()
    return (this.pinnedOffset = position.top - scrollTop)
  }

  Affix.prototype.checkPositionWithEventLoop = function () {
    setTimeout($.proxy(this.checkPosition, this), 1)
  }

  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(':visible')) return

    var scrollHeight = $(document).height()
    var scrollTop    = this.$target.scrollTop()
    var position     = this.$element.offset()
    var offset       = this.options.offset
    var offsetTop    = offset.top
    var offsetBottom = offset.bottom

    if (typeof offset != 'object')         offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function')    offsetTop    = offset.top(this.$element)
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom(this.$element)

    var affix = this.unpin   != null && (scrollTop + this.unpin <= position.top) ? false :
                offsetBottom != null && (position.top + this.$element.height() >= scrollHeight - offsetBottom) ? 'bottom' :
                offsetTop    != null && (scrollTop <= offsetTop) ? 'top' : false

    if (this.affixed === affix) return
    if (this.unpin != null) this.$element.css('top', '')

    var affixType = 'affix' + (affix ? '-' + affix : '')
    var e         = $.Event(affixType + '.bs.affix')

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    this.affixed = affix
    this.unpin = affix == 'bottom' ? this.getPinnedOffset() : null

    this.$element
      .removeClass(Affix.RESET)
      .addClass(affixType)
      .trigger($.Event(affixType.replace('affix', 'affixed')))

    if (affix == 'bottom') {
      this.$element.offset({
        top: scrollHeight - this.$element.height() - offsetBottom
      })
    }
  }


  // AFFIX PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.affix')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.affix', (data = new Affix(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.affix

  $.fn.affix             = Plugin
  $.fn.affix.Constructor = Affix


  // AFFIX NO CONFLICT
  // =================

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }


  // AFFIX DATA-API
  // ==============

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
      var data = $spy.data()

      data.offset = data.offset || {}

      if (data.offsetBottom) data.offset.bottom = data.offsetBottom
      if (data.offsetTop)    data.offset.top    = data.offsetTop

      Plugin.call($spy, data)
    })
  })

}(jQuery);
;/*!
 * jQuery Form Plugin
 * version: 3.46.0-2013.11.21
 * Requires jQuery v1.5 or later
 * Copyright (c) 2013 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
/*global ActiveXObject */

// AMD support
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // using AMD; register as anon module
        define(['jquery'], factory);
    } else {
        // no AMD; invoke directly
        factory( (typeof(jQuery) != 'undefined') ? jQuery : window.Zepto );
    }
}

(function($) {
"use strict";

/*
    Usage Note:
    -----------
    Do not use both ajaxSubmit and ajaxForm on the same form.  These
    functions are mutually exclusive.  Use ajaxSubmit if you want
    to bind your own submit handler to the form.  For example,

    $(document).ready(function() {
        $('#myForm').on('submit', function(e) {
            e.preventDefault(); // <-- important
            $(this).ajaxSubmit({
                target: '#output'
            });
        });
    });

    Use ajaxForm when you want the plugin to manage all the event binding
    for you.  For example,

    $(document).ready(function() {
        $('#myForm').ajaxForm({
            target: '#output'
        });
    });

    You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
    form does not have to exist when you invoke ajaxForm:

    $('#myForm').ajaxForm({
        delegation: true,
        target: '#output'
    });

    When using ajaxForm, the ajaxSubmit function will be invoked for you
    at the appropriate time.
*/

/**
 * Feature detection
 */
var feature = {};
feature.fileapi = $("<input type='file'/>").get(0).files !== undefined;
feature.formdata = window.FormData !== undefined;

var hasProp = !!$.fn.prop;

// attr2 uses prop when it can but checks the return type for
// an expected string.  this accounts for the case where a form 
// contains inputs with names like "action" or "method"; in those
// cases "prop" returns the element
$.fn.attr2 = function() {
    if ( ! hasProp )
        return this.attr.apply(this, arguments);
    var val = this.prop.apply(this, arguments);
    if ( ( val && val.jquery ) || typeof val === 'string' )
        return val;
    return this.attr.apply(this, arguments);
};

/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */
$.fn.ajaxSubmit = function(options) {
    /*jshint scripturl:true */

    // fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
    if (!this.length) {
        log('ajaxSubmit: skipping submit process - no element selected');
        return this;
    }

    var method, action, url, $form = this;

    if (typeof options == 'function') {
        options = { success: options };
    }
    else if ( options === undefined ) {
        options = {};
    }

    method = options.type || this.attr2('method');
    action = options.url  || this.attr2('action');

    url = (typeof action === 'string') ? $.trim(action) : '';
    url = url || window.location.href || '';
    if (url) {
        // clean url (don't include hash vaue)
        url = (url.match(/^([^#]+)/)||[])[1];
    }

    options = $.extend(true, {
        url:  url,
        success: $.ajaxSettings.success,
        type: method || $.ajaxSettings.type,
        iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
    }, options);

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    this.trigger('form-pre-serialize', [this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
        return this;
    }

    // provide opportunity to alter form data before it is serialized
    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSerialize callback');
        return this;
    }

    var traditional = options.traditional;
    if ( traditional === undefined ) {
        traditional = $.ajaxSettings.traditional;
    }

    var elements = [];
    var qx, a = this.formToArray(options.semantic, elements);
    if (options.data) {
        options.extraData = options.data;
        qx = $.param(options.data, traditional);
    }

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSubmit callback');
        return this;
    }

    // fire vetoable 'validate' event
    this.trigger('form-submit-validate', [a, this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
        return this;
    }

    var q = $.param(a, traditional);
    if (qx) {
        q = ( q ? (q + '&' + qx) : qx );
    }
    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else {
        options.data = q; // data is the query string for 'post'
    }

    var callbacks = [];
    if (options.resetForm) {
        callbacks.push(function() { $form.resetForm(); });
    }
    if (options.clearForm) {
        callbacks.push(function() { $form.clearForm(options.includeHidden); });
    }

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            var fn = options.replaceTarget ? 'replaceWith' : 'html';
            $(options.target)[fn](data).each(oldSuccess, arguments);
        });
    }
    else if (options.success) {
        callbacks.push(options.success);
    }

    options.success = function(data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
        var context = options.context || this ;    // jQuery 1.4+ supports scope context
        for (var i=0, max=callbacks.length; i < max; i++) {
            callbacks[i].apply(context, [data, status, xhr || $form, $form]);
        }
    };

    if (options.error) {
        var oldError = options.error;
        options.error = function(xhr, status, error) {
            var context = options.context || this;
            oldError.apply(context, [xhr, status, error, $form]);
        };
    }

     if (options.complete) {
        var oldComplete = options.complete;
        options.complete = function(xhr, status) {
            var context = options.context || this;
            oldComplete.apply(context, [xhr, status, $form]);
        };
    }

    // are there files to upload?

    // [value] (issue #113), also see comment:
    // https://github.com/malsup/form/commit/588306aedba1de01388032d5f42a60159eea9228#commitcomment-2180219
    var fileInputs = $('input[type=file]:enabled', this).filter(function() { return $(this).val() !== ''; });

    var hasFileInputs = fileInputs.length > 0;
    var mp = 'multipart/form-data';
    var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

    var fileAPI = feature.fileapi && feature.formdata;
    log("fileAPI :" + fileAPI);
    var shouldUseFrame = (hasFileInputs || multipart) && !fileAPI;

    var jqxhr;

    // options.iframe allows user to force iframe mode
    // 06-NOV-09: now defaulting to iframe mode if file input is detected
    if (options.iframe !== false && (options.iframe || shouldUseFrame)) {
        // hack to fix Safari hang (thanks to Tim Molendijk for this)
        // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
        if (options.closeKeepAlive) {
            $.get(options.closeKeepAlive, function() {
                jqxhr = fileUploadIframe(a);
            });
        }
        else {
            jqxhr = fileUploadIframe(a);
        }
    }
    else if ((hasFileInputs || multipart) && fileAPI) {
        jqxhr = fileUploadXhr(a);
    }
    else {
        jqxhr = $.ajax(options);
    }

    $form.removeData('jqxhr').data('jqxhr', jqxhr);

    // clear element array
    for (var k=0; k < elements.length; k++)
        elements[k] = null;

    // fire 'notify' event
    this.trigger('form-submit-notify', [this, options]);
    return this;

    // utility fn for deep serialization
    function deepSerialize(extraData){
        var serialized = $.param(extraData, options.traditional).split('&');
        var len = serialized.length;
        var result = [];
        var i, part;
        for (i=0; i < len; i++) {
            // #252; undo param space replacement
            serialized[i] = serialized[i].replace(/\+/g,' ');
            part = serialized[i].split('=');
            // #278; use array instead of object storage, favoring array serializations
            result.push([decodeURIComponent(part[0]), decodeURIComponent(part[1])]);
        }
        return result;
    }

     // XMLHttpRequest Level 2 file uploads (big hat tip to francois2metz)
    function fileUploadXhr(a) {
        var formdata = new FormData();

        for (var i=0; i < a.length; i++) {
            formdata.append(a[i].name, a[i].value);
        }

        if (options.extraData) {
            var serializedData = deepSerialize(options.extraData);
            for (i=0; i < serializedData.length; i++)
                if (serializedData[i])
                    formdata.append(serializedData[i][0], serializedData[i][1]);
        }

        options.data = null;

        var s = $.extend(true, {}, $.ajaxSettings, options, {
            contentType: false,
            processData: false,
            cache: false,
            type: method || 'POST'
        });

        if (options.uploadProgress) {
            // workaround because jqXHR does not expose upload property
            s.xhr = function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position; /*event.position is deprecated*/
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        options.uploadProgress(event, position, total, percent);
                    }, false);
                }
                return xhr;
            };
        }

        s.data = null;
        var beforeSend = s.beforeSend;
        s.beforeSend = function(xhr, o) {
            //Send FormData() provided by user
            if (options.formData)
                o.data = options.formData;
            else
                o.data = formdata;
            if(beforeSend)
                beforeSend.call(this, xhr, o);
        };
        return $.ajax(s);
    }

    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUploadIframe(a) {
        var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
        var deferred = $.Deferred();

        // #341
        deferred.abort = function(status) {
            xhr.abort(status);
        };

        if (a) {
            // ensure that every serialized input is still enabled
            for (i=0; i < elements.length; i++) {
                el = $(elements[i]);
                if ( hasProp )
                    el.prop('disabled', false);
                else
                    el.removeAttr('disabled');
            }
        }

        s = $.extend(true, {}, $.ajaxSettings, options);
        s.context = s.context || s;
        id = 'jqFormIO' + (new Date().getTime());
        if (s.iframeTarget) {
            $io = $(s.iframeTarget);
            n = $io.attr2('name');
            if (!n)
                 $io.attr2('name', id);
            else
                id = n;
        }
        else {
            $io = $('<iframe name="' + id + '" src="'+ s.iframeSrc +'" />');
            $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });
        }
        io = $io[0];


        xhr = { // mock object
            aborted: 0,
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {},
            abort: function(status) {
                var e = (status === 'timeout' ? 'timeout' : 'aborted');
                log('aborting upload... ' + e);
                this.aborted = 1;

                try { // #214, #257
                    if (io.contentWindow.document.execCommand) {
                        io.contentWindow.document.execCommand('Stop');
                    }
                }
                catch(ignore) {}

                $io.attr('src', s.iframeSrc); // abort op in progress
                xhr.error = e;
                if (s.error)
                    s.error.call(s.context, xhr, e, status);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, e]);
                if (s.complete)
                    s.complete.call(s.context, xhr, e);
            }
        };

        g = s.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && 0 === $.active++) {
            $.event.trigger("ajaxStart");
        }
        if (g) {
            $.event.trigger("ajaxSend", [xhr, s]);
        }

        if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
            if (s.global) {
                $.active--;
            }
            deferred.reject();
            return deferred;
        }
        if (xhr.aborted) {
            deferred.reject();
            return deferred;
        }

        // add submitting element to data if we know it
        sub = form.clk;
        if (sub) {
            n = sub.name;
            if (n && !sub.disabled) {
                s.extraData = s.extraData || {};
                s.extraData[n] = sub.value;
                if (sub.type == "image") {
                    s.extraData[n+'.x'] = form.clk_x;
                    s.extraData[n+'.y'] = form.clk_y;
                }
            }
        }

        var CLIENT_TIMEOUT_ABORT = 1;
        var SERVER_ABORT = 2;
                
        function getDoc(frame) {
            /* it looks like contentWindow or contentDocument do not
             * carry the protocol property in ie8, when running under ssl
             * frame.document is the only valid response document, since
             * the protocol is know but not on the other two objects. strange?
             * "Same origin policy" http://en.wikipedia.org/wiki/Same_origin_policy
             */
            
            var doc = null;
            
            // IE8 cascading access check
            try {
                if (frame.contentWindow) {
                    doc = frame.contentWindow.document;
                }
            } catch(err) {
                // IE8 access denied under ssl & missing protocol
                log('cannot get iframe.contentWindow document: ' + err);
            }

            if (doc) { // successful getting content
                return doc;
            }

            try { // simply checking may throw in ie8 under ssl or mismatched protocol
                doc = frame.contentDocument ? frame.contentDocument : frame.document;
            } catch(err) {
                // last attempt
                log('cannot get iframe.contentDocument: ' + err);
                doc = frame.document;
            }
            return doc;
        }

        // Rails CSRF hack (thanks to Yvan Barthelemy)
        var csrf_token = $('meta[name=csrf-token]').attr('content');
        var csrf_param = $('meta[name=csrf-param]').attr('content');
        if (csrf_param && csrf_token) {
            s.extraData = s.extraData || {};
            s.extraData[csrf_param] = csrf_token;
        }

        // take a breath so that pending repaints get some cpu time before the upload starts
        function doSubmit() {
            // make sure form attrs are set
            var t = $form.attr2('target'), a = $form.attr2('action');

            // update form attrs in IE friendly way
            form.setAttribute('target',id);
            if (!method || /post/i.test(method) ) {
                form.setAttribute('method', 'POST');
            }
            if (a != s.url) {
                form.setAttribute('action', s.url);
            }

            // ie borks in some cases when setting encoding
            if (! s.skipEncodingOverride && (!method || /post/i.test(method))) {
                $form.attr({
                    encoding: 'multipart/form-data',
                    enctype:  'multipart/form-data'
                });
            }

            // support timout
            if (s.timeout) {
                timeoutHandle = setTimeout(function() { timedOut = true; cb(CLIENT_TIMEOUT_ABORT); }, s.timeout);
            }

            // look for server aborts
            function checkState() {
                try {
                    var state = getDoc(io).readyState;
                    log('state = ' + state);
                    if (state && state.toLowerCase() == 'uninitialized')
                        setTimeout(checkState,50);
                }
                catch(e) {
                    log('Server abort: ' , e, ' (', e.name, ')');
                    cb(SERVER_ABORT);
                    if (timeoutHandle)
                        clearTimeout(timeoutHandle);
                    timeoutHandle = undefined;
                }
            }

            // add "extra" data to form if provided in options
            var extraInputs = [];
            try {
                if (s.extraData) {
                    for (var n in s.extraData) {
                        if (s.extraData.hasOwnProperty(n)) {
                           // if using the $.param format that allows for multiple values with the same name
                           if($.isPlainObject(s.extraData[n]) && s.extraData[n].hasOwnProperty('name') && s.extraData[n].hasOwnProperty('value')) {
                               extraInputs.push(
                               $('<input type="hidden" name="'+s.extraData[n].name+'">').val(s.extraData[n].value)
                                   .appendTo(form)[0]);
                           } else {
                               extraInputs.push(
                               $('<input type="hidden" name="'+n+'">').val(s.extraData[n])
                                   .appendTo(form)[0]);
                           }
                        }
                    }
                }

                if (!s.iframeTarget) {
                    // add iframe to doc and submit the form
                    $io.appendTo('body');
                }
                if (io.attachEvent)
                    io.attachEvent('onload', cb);
                else
                    io.addEventListener('load', cb, false);
                setTimeout(checkState,15);

                try {
                    form.submit();
                } catch(err) {
                    // just in case form has element with name/id of 'submit'
                    var submitFn = document.createElement('form').submit;
                    submitFn.apply(form);
                }
            }
            finally {
                // reset attrs and remove "extra" input elements
                form.setAttribute('action',a);
                if(t) {
                    form.setAttribute('target', t);
                } else {
                    $form.removeAttr('target');
                }
                $(extraInputs).remove();
            }
        }

        if (s.forceSync) {
            doSubmit();
        }
        else {
            setTimeout(doSubmit, 10); // this lets dom updates render
        }

        var data, doc, domCheckCount = 50, callbackProcessed;

        function cb(e) {
            if (xhr.aborted || callbackProcessed) {
                return;
            }
            
            doc = getDoc(io);
            if(!doc) {
                log('cannot access response document');
                e = SERVER_ABORT;
            }
            if (e === CLIENT_TIMEOUT_ABORT && xhr) {
                xhr.abort('timeout');
                deferred.reject(xhr, 'timeout');
                return;
            }
            else if (e == SERVER_ABORT && xhr) {
                xhr.abort('server abort');
                deferred.reject(xhr, 'error', 'server abort');
                return;
            }

            if (!doc || doc.location.href == s.iframeSrc) {
                // response not received yet
                if (!timedOut)
                    return;
            }
            if (io.detachEvent)
                io.detachEvent('onload', cb);
            else
                io.removeEventListener('load', cb, false);

            var status = 'success', errMsg;
            try {
                if (timedOut) {
                    throw 'timeout';
                }

                var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
                log('isXml='+isXml);
                if (!isXml && window.opera && (doc.body === null || !doc.body.innerHTML)) {
                    if (--domCheckCount) {
                        // in some browsers (Opera) the iframe DOM is not always traversable when
                        // the onload callback fires, so we loop a bit to accommodate
                        log('requeing onLoad callback, DOM not available');
                        setTimeout(cb, 250);
                        return;
                    }
                    // let this fall through because server response could be an empty document
                    //log('Could not access iframe DOM after mutiple tries.');
                    //throw 'DOMException: not available';
                }

                //log('response detected');
                var docRoot = doc.body ? doc.body : doc.documentElement;
                xhr.responseText = docRoot ? docRoot.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                if (isXml)
                    s.dataType = 'xml';
                xhr.getResponseHeader = function(header){
                    var headers = {'content-type': s.dataType};
                    return headers[header.toLowerCase()];
                };
                // support for XHR 'status' & 'statusText' emulation :
                if (docRoot) {
                    xhr.status = Number( docRoot.getAttribute('status') ) || xhr.status;
                    xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
                }

                var dt = (s.dataType || '').toLowerCase();
                var scr = /(json|script|text)/.test(dt);
                if (scr || s.textarea) {
                    // see if user embedded response in textarea
                    var ta = doc.getElementsByTagName('textarea')[0];
                    if (ta) {
                        xhr.responseText = ta.value;
                        // support for XHR 'status' & 'statusText' emulation :
                        xhr.status = Number( ta.getAttribute('status') ) || xhr.status;
                        xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
                    }
                    else if (scr) {
                        // account for browsers injecting pre around json response
                        var pre = doc.getElementsByTagName('pre')[0];
                        var b = doc.getElementsByTagName('body')[0];
                        if (pre) {
                            xhr.responseText = pre.textContent ? pre.textContent : pre.innerText;
                        }
                        else if (b) {
                            xhr.responseText = b.textContent ? b.textContent : b.innerText;
                        }
                    }
                }
                else if (dt == 'xml' && !xhr.responseXML && xhr.responseText) {
                    xhr.responseXML = toXml(xhr.responseText);
                }

                try {
                    data = httpData(xhr, dt, s);
                }
                catch (err) {
                    status = 'parsererror';
                    xhr.error = errMsg = (err || status);
                }
            }
            catch (err) {
                log('error caught: ',err);
                status = 'error';
                xhr.error = errMsg = (err || status);
            }

            if (xhr.aborted) {
                log('upload aborted');
                status = null;
            }

            if (xhr.status) { // we've set xhr.status
                status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (status === 'success') {
                if (s.success)
                    s.success.call(s.context, data, 'success', xhr);
                deferred.resolve(xhr.responseText, 'success', xhr);
                if (g)
                    $.event.trigger("ajaxSuccess", [xhr, s]);
            }
            else if (status) {
                if (errMsg === undefined)
                    errMsg = xhr.statusText;
                if (s.error)
                    s.error.call(s.context, xhr, status, errMsg);
                deferred.reject(xhr, 'error', errMsg);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, errMsg]);
            }

            if (g)
                $.event.trigger("ajaxComplete", [xhr, s]);

            if (g && ! --$.active) {
                $.event.trigger("ajaxStop");
            }

            if (s.complete)
                s.complete.call(s.context, xhr, status);

            callbackProcessed = true;
            if (s.timeout)
                clearTimeout(timeoutHandle);

            // clean up
            setTimeout(function() {
                if (!s.iframeTarget)
                    $io.remove();
                else  //adding else to clean up existing iframe response.
                    $io.attr('src', s.iframeSrc);
                xhr.responseXML = null;
            }, 100);
        }

        var toXml = $.parseXML || function(s, doc) { // use parseXML if available (jQuery 1.5+)
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else {
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            }
            return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
        };
        var parseJSON = $.parseJSON || function(s) {
            /*jslint evil:true */
            return window['eval']('(' + s + ')');
        };

        var httpData = function( xhr, type, s ) { // mostly lifted from jq1.4.4

            var ct = xhr.getResponseHeader('content-type') || '',
                xml = type === 'xml' || !type && ct.indexOf('xml') >= 0,
                data = xml ? xhr.responseXML : xhr.responseText;

            if (xml && data.documentElement.nodeName === 'parsererror') {
                if ($.error)
                    $.error('parsererror');
            }
            if (s && s.dataFilter) {
                data = s.dataFilter(data, type);
            }
            if (typeof data === 'string') {
                if (type === 'json' || !type && ct.indexOf('json') >= 0) {
                    data = parseJSON(data);
                } else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
                    $.globalEval(data);
                }
            }
            return data;
        };

        return deferred;
    }
};

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *    is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *    used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.
 */
$.fn.ajaxForm = function(options) {
    options = options || {};
    options.delegation = options.delegation && $.isFunction($.fn.on);

    // in jQuery 1.3+ we can fix mistakes with the ready state
    if (!options.delegation && this.length === 0) {
        var o = { s: this.selector, c: this.context };
        if (!$.isReady && o.s) {
            log('DOM not ready, queuing ajaxForm');
            $(function() {
                $(o.s,o.c).ajaxForm(options);
            });
            return this;
        }
        // is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
        log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
        return this;
    }

    if ( options.delegation ) {
        $(document)
            .off('submit.form-plugin', this.selector, doAjaxSubmit)
            .off('click.form-plugin', this.selector, captureSubmittingElement)
            .on('submit.form-plugin', this.selector, options, doAjaxSubmit)
            .on('click.form-plugin', this.selector, options, captureSubmittingElement);
        return this;
    }

    return this.ajaxFormUnbind()
        .bind('submit.form-plugin', options, doAjaxSubmit)
        .bind('click.form-plugin', options, captureSubmittingElement);
};

// private event handlers
function doAjaxSubmit(e) {
    /*jshint validthis:true */
    var options = e.data;
    if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
        e.preventDefault();
        $(e.target).ajaxSubmit(options); // #365
    }
}

function captureSubmittingElement(e) {
    /*jshint validthis:true */
    var target = e.target;
    var $el = $(target);
    if (!($el.is("[type=submit],[type=image]"))) {
        // is this a child element of the submit el?  (ex: a span within a button)
        var t = $el.closest('[type=submit]');
        if (t.length === 0) {
            return;
        }
        target = t[0];
    }
    var form = this;
    form.clk = target;
    if (target.type == 'image') {
        if (e.offsetX !== undefined) {
            form.clk_x = e.offsetX;
            form.clk_y = e.offsetY;
        } else if (typeof $.fn.offset == 'function') {
            var offset = $el.offset();
            form.clk_x = e.pageX - offset.left;
            form.clk_y = e.pageY - offset.top;
        } else {
            form.clk_x = e.pageX - target.offsetLeft;
            form.clk_y = e.pageY - target.offsetTop;
        }
    }
    // clear form vars
    setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100);
}


// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
$.fn.ajaxFormUnbind = function() {
    return this.unbind('submit.form-plugin click.form-plugin');
};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 */
$.fn.formToArray = function(semantic, elements) {
    var a = [];
    if (this.length === 0) {
        return a;
    }

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) {
        return a;
    }

    var i,j,n,v,el,max,jmax;
    for(i=0, max=els.length; i < max; i++) {
        el = els[i];
        n = el.name;
        if (!n || el.disabled) {
            continue;
        }

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(form.clk == el) {
                a.push({name: n, value: $(el).val(), type: el.type });
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            }
            continue;
        }

        v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            if (elements)
                elements.push(el);
            for(j=0, jmax=v.length; j < jmax; j++) {
                a.push({name: n, value: v[j]});
            }
        }
        else if (feature.fileapi && el.type == 'file') {
            if (elements)
                elements.push(el);
            var files = el.files;
            if (files.length) {
                for (j=0; j < files.length; j++) {
                    a.push({name: n, value: files[j], type: el.type});
                }
            }
            else {
                // #180
                a.push({ name: n, value: '', type: el.type });
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            if (elements)
                elements.push(el);
            a.push({name: n, value: v, type: el.type, required: el.required});
        }
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle it here
        var $input = $(form.clk), input = $input[0];
        n = input.name;
        if (n && !input.disabled && input.type == 'image') {
            a.push({name: n, value: $input.val()});
            a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};

/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 */
$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};

/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 */
$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) {
            return;
        }
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++) {
                a.push({name: n, value: v[i]});
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            a.push({name: this.name, value: v});
        }
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};

/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *      <input name="A" type="text" />
 *      <input name="A" type="text" />
 *      <input name="B" type="checkbox" value="B1" />
 *      <input name="B" type="checkbox" value="B2"/>
 *      <input name="C" type="radio" value="C1" />
 *      <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $('input[type=text]').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $('input[type=checkbox]').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $('input[type=radio]').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *    array will be empty, otherwise it will contain one or more values.
 */
$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
            continue;
        }
        if (v.constructor == Array)
            $.merge(val, v);
        else
            val.push(v);
    }
    return val;
};

/**
 * Returns the value of the field element.
 */
$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (successful === undefined) {
        successful = true;
    }

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1)) {
            return null;
    }

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) {
            return null;
        }
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                var v = op.value;
                if (!v) { // extra pain for IE...
                    v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
                }
                if (one) {
                    return v;
                }
                a.push(v);
            }
        }
        return a;
    }
    return $(el).val();
};

/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 */
$.fn.clearForm = function(includeHidden) {
    return this.each(function() {
        $('input,select,textarea', this).clearFields(includeHidden);
    });
};

/**
 * Clears the selected form elements.
 */
$.fn.clearFields = $.fn.clearInputs = function(includeHidden) {
    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (re.test(t) || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
		else if (t == "file") {
			if (/MSIE/.test(navigator.userAgent)) {
				$(this).replaceWith($(this).clone(true));
			} else {
				$(this).val('');
			}
		}
        else if (includeHidden) {
            // includeHidden can be the value true, or it can be a selector string
            // indicating a special test; for example:
            //  $('#myForm').clearForm('.special:hidden')
            // the above would clean hidden inputs that have the class of 'special'
            if ( (includeHidden === true && /hidden/.test(t)) ||
                 (typeof includeHidden == 'string' && $(this).is(includeHidden)) )
                this.value = '';
        }
    });
};

/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 */
$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
            this.reset();
        }
    });
};

/**
 * Enables or disables any matching elements.
 */
$.fn.enable = function(b) {
    if (b === undefined) {
        b = true;
    }
    return this.each(function() {
        this.disabled = !b;
    });
};

/**
 * Checks/unchecks any matching checkboxes or radio buttons and
 * selects/deselects and matching option elements.
 */
$.fn.selected = function(select) {
    if (select === undefined) {
        select = true;
    }
    return this.each(function() {
        var t = this.type;
        if (t == 'checkbox' || t == 'radio') {
            this.checked = select;
        }
        else if (this.tagName.toLowerCase() == 'option') {
            var $sel = $(this).parent('select');
            if (select && $sel[0] && $sel[0].type == 'select-one') {
                // deselect all other options
                $sel.find('option').selected(false);
            }
            this.selected = select;
        }
    });
};

// expose debug var
$.fn.ajaxSubmit.debug = false;

// helper fn for console logging
function log() {
    if (!$.fn.ajaxSubmit.debug)
        return;
    var msg = '[jquery.form] ' + Array.prototype.join.call(arguments,'');
    if (window.console && window.console.log) {
        window.console.log(msg);
    }
    else if (window.opera && window.opera.postError) {
        window.opera.postError(msg);
    }
}

}));

;;!function (name, context, definition) {
   if (typeof module !== 'undefined') module.exports = definition(name, context)
   else if (typeof define === 'function' && typeof define.amd  === 'object') define(definition)
   else context[name] = definition(name, context)
}('humane', this, function (name, context) {
   var win = window
   var doc = document

   var ENV = {
      on: function (el, type, cb) {
         'addEventListener' in win ? el.addEventListener(type,cb,false) : el.attachEvent('on'+type,cb)
      },
      off: function (el, type, cb) {
         'removeEventListener' in win ? el.removeEventListener(type,cb,false) : el.detachEvent('on'+type,cb)
      },
      bind: function (fn, ctx) {
         return function () { fn.apply(ctx,arguments) }
      },
      isArray: Array.isArray || function (obj) { return Object.prototype.toString.call(obj) === '[object Array]' },
      config: function (preferred, fallback) {
         return preferred != null ? preferred : fallback
      },
      transSupport: false,
      useFilter: /msie [678]/i.test(navigator.userAgent), // sniff, sniff
      _checkTransition: function () {
         var el = doc.createElement('div')
         var vendors = { webkit: 'webkit', Moz: '', O: 'o', ms: 'MS' }

         for (var vendor in vendors)
            if (vendor + 'Transition' in el.style) {
               this.vendorPrefix = vendors[vendor]
               this.transSupport = true
            }
      }
   }
   ENV._checkTransition()

   var Humane = function (o) {
      o || (o = {})
      this.queue = []
      this.baseCls = o.baseCls || 'humane'
      this.addnCls = o.addnCls || ''
      this.timeout = 'timeout' in o ? o.timeout : 2500
      this.waitForMove = o.waitForMove || false
      this.clickToClose = o.clickToClose || false
      this.timeoutAfterMove = o.timeoutAfterMove || false
      this.container = o.container

      try { this._setupEl() } // attempt to setup elements
      catch (e) {
        ENV.on(win,'load',ENV.bind(this._setupEl, this)) // dom wasn't ready, wait till ready
      }
   }

   Humane.prototype = {
      constructor: Humane,
      _setupEl: function () {
         var el = doc.createElement('div')
         el.style.display = 'none'
         if (!this.container){
           if(doc.body) this.container = doc.body;
           else throw 'document.body is null'
         }
         this.container.appendChild(el)
         this.el = el
         this.removeEvent = ENV.bind(function(){
            var timeoutAfterMove = ENV.config(this.currentMsg.timeoutAfterMove,this.timeoutAfterMove)
            if (!timeoutAfterMove){
               this.remove()
            } else {
               setTimeout(ENV.bind(this.remove,this),timeoutAfterMove)
            }
         },this)

         this.transEvent = ENV.bind(this._afterAnimation,this)
         this._run()
      },
      _afterTimeout: function () {
         if (!ENV.config(this.currentMsg.waitForMove,this.waitForMove)) this.remove()

         else if (!this.removeEventsSet) {
            ENV.on(doc.body,'mousemove',this.removeEvent)
            ENV.on(doc.body,'click',this.removeEvent)
            ENV.on(doc.body,'keypress',this.removeEvent)
            ENV.on(doc.body,'touchstart',this.removeEvent)
            this.removeEventsSet = true
         }
      },
      _run: function () {
         if (this._animating || !this.queue.length || !this.el) return

         this._animating = true
         if (this.currentTimer) {
            clearTimeout(this.currentTimer)
            this.currentTimer = null
         }

         var msg = this.queue.shift()
         var clickToClose = ENV.config(msg.clickToClose,this.clickToClose)

         if (clickToClose) {
            ENV.on(this.el,'click',this.removeEvent)
            ENV.on(this.el,'touchstart',this.removeEvent)
         }

         var timeout = ENV.config(msg.timeout,this.timeout)

         if (timeout > 0)
            this.currentTimer = setTimeout(ENV.bind(this._afterTimeout,this), timeout)

         if (ENV.isArray(msg.html)) msg.html = '<ul><li>'+msg.html.join('<li>')+'</ul>'

         this.el.innerHTML = msg.html
         this.currentMsg = msg
         this.el.className = this.baseCls
         if (ENV.transSupport) {
            this.el.style.display = 'block'
            setTimeout(ENV.bind(this._showMsg,this),50)
         } else {
            this._showMsg()
         }

      },
      _setOpacity: function (opacity) {
         if (ENV.useFilter){
            try{
               this.el.filters.item('DXImageTransform.Microsoft.Alpha').Opacity = opacity*100
            } catch(err){}
         } else {
            this.el.style.opacity = String(opacity)
         }
      },
      _showMsg: function () {
         var addnCls = ENV.config(this.currentMsg.addnCls,this.addnCls)
         if (ENV.transSupport) {
            this.el.className = this.baseCls+' '+addnCls+' '+this.baseCls+'-animate'
         }
         else {
            var opacity = 0
            this.el.className = this.baseCls+' '+addnCls+' '+this.baseCls+'-js-animate'
            this._setOpacity(0) // reset value so hover states work
            this.el.style.display = 'block'

            var self = this
            var interval = setInterval(function(){
               if (opacity < 1) {
                  opacity += 0.1
                  if (opacity > 1) opacity = 1
                  self._setOpacity(opacity)
               }
               else clearInterval(interval)
            }, 30)
         }
      },
      _hideMsg: function () {
         var addnCls = ENV.config(this.currentMsg.addnCls,this.addnCls)
         if (ENV.transSupport) {
            this.el.className = this.baseCls+' '+addnCls
            ENV.on(this.el,ENV.vendorPrefix ? ENV.vendorPrefix+'TransitionEnd' : 'transitionend',this.transEvent)
         }
         else {
            var opacity = 1
            var self = this
            var interval = setInterval(function(){
               if(opacity > 0) {
                  opacity -= 0.1
                  if (opacity < 0) opacity = 0
                  self._setOpacity(opacity);
               }
               else {
                  self.el.className = self.baseCls+' '+addnCls
                  clearInterval(interval)
                  self._afterAnimation()
               }
            }, 30)
         }
      },
      _afterAnimation: function () {
         if (ENV.transSupport) ENV.off(this.el,ENV.vendorPrefix ? ENV.vendorPrefix+'TransitionEnd' : 'transitionend',this.transEvent)

         if (this.currentMsg.cb) this.currentMsg.cb()
         this.el.style.display = 'none'

         this._animating = false
         this._run()
      },
      remove: function (e) {
         var cb = typeof e == 'function' ? e : null

         ENV.off(doc.body,'mousemove',this.removeEvent)
         ENV.off(doc.body,'click',this.removeEvent)
         ENV.off(doc.body,'keypress',this.removeEvent)
         ENV.off(doc.body,'touchstart',this.removeEvent)
         ENV.off(this.el,'click',this.removeEvent)
         ENV.off(this.el,'touchstart',this.removeEvent)
         this.removeEventsSet = false

         if (cb && this.currentMsg) this.currentMsg.cb = cb
         if (this._animating) this._hideMsg()
         else if (cb) cb()
      },
      log: function (html, o, cb, defaults) {
         var msg = {}
         if (defaults)
           for (var opt in defaults)
               msg[opt] = defaults[opt]

         if (typeof o == 'function') cb = o
         else if (o)
            for (var opt in o) msg[opt] = o[opt]

         msg.html = html
         if (cb) msg.cb = cb
         this.queue.push(msg)
         this._run()
         return this
      },
      spawn: function (defaults) {
         var self = this
         return function (html, o, cb) {
            self.log.call(self,html,o,cb,defaults)
            return self
         }
      },
      create: function (o) { return new Humane(o) }
   }
   return new Humane()
});
;;(function(window, jQuery, undefined) {
	'use strict';


	/*
	 * Utility functions
	 */
	var setPercentBtns = function() {
		// loop through each instance of buttons
		jQuery('.rrssb-buttons').each(function(index) {
			var self = jQuery(this);
			var numOfButtons = jQuery('li', self).length;
			var initBtnWidth = 100 / numOfButtons;

			// set initial width of buttons
			jQuery('li', self).css('width', initBtnWidth + '%').attr('data-initwidth',initBtnWidth);
		});
	};

	var makeExtremityBtns = function() {
		// loop through each instance of buttons
		jQuery('.rrssb-buttons').each(function(index) {
			var self = jQuery(this);
			//get button width
			var containerWidth = parseFloat(jQuery(self).width());
			var buttonWidth = jQuery('li', self).not('.small').first().width();
			var smallBtnCount = jQuery('li.small', self).length;

			// enlarge buttons if they get wide enough
			if (buttonWidth > 170 && smallBtnCount < 1) {
				jQuery(self).addClass('large-format');
			} else {
				jQuery(self).removeClass('large-format');
			}

			if (containerWidth < 200) {
				jQuery(self).removeClass('small-format').addClass('tiny-format');
			} else {
				jQuery(self).removeClass('tiny-format');
			}
		});
	};

	var backUpFromSmall = function() {
		// loop through each instance of buttons
		jQuery('.rrssb-buttons').each(function(index) {
			var self = jQuery(this);
			var totalBtnSze = 0, totalTxtSze = 0, upCandidate, nextBackUp;
			var smallBtnCount = jQuery('li.small', self).length;

			if (smallBtnCount === jQuery('li', self).length) {
				var btnCalc = smallBtnCount * 42;
				var containerWidth = parseFloat(jQuery(self).width());
				upCandidate = jQuery('li.small', self).first();
				nextBackUp = parseFloat(jQuery(upCandidate).attr('data-size')) + 55;

				if ((btnCalc + nextBackUp) < containerWidth) {
					jQuery(self).removeClass('small-format');
					jQuery('li.small', self).first().removeClass('small');

					sizeSmallBtns();
				}

			} else {
				jQuery('li', self).not('.small').each(function(index) {
					var txtWidth = parseFloat(jQuery(this).attr('data-size')) + 55;
					var btnWidth = parseFloat(jQuery(this).width());

					totalBtnSze = totalBtnSze + btnWidth;
					totalTxtSze = totalTxtSze + txtWidth;
				});

				var spaceLeft = totalBtnSze - totalTxtSze;
				upCandidate = jQuery('li.small', self).first();
				nextBackUp = parseFloat(jQuery(upCandidate).attr('data-size')) + 55;

				if (nextBackUp < spaceLeft) {
					jQuery(upCandidate).removeClass('small');
					sizeSmallBtns();
				}
			}
		});
	};

	var checkSize = function(init) {
		// loop through each instance of buttons
		jQuery('.rrssb-buttons').each(function(index) {
			//console.log('starting check for: '+(index+1));
			var self = jQuery(this);
			var elems = jQuery('li', self).nextAll(), count = elems.length;

			// get buttons in reverse order and loop through each
			jQuery(jQuery('li', self).get().reverse()).each(function(index, count) {

				if (jQuery(this).hasClass('small') === false) {
					var txtWidth = parseFloat(jQuery(this).attr('data-size')) + 55;
					var btnWidth = parseFloat(jQuery(this).width());

					if (txtWidth > btnWidth) {
						//console.log($(self).attr('class')+' '+$(this).attr('class')+' txtWidth: '+txtWidth+ ' & btnWidth: '+btnWidth);
						var btn2small = jQuery('li', self).not('.small').last();
						jQuery(btn2small).addClass('small');
						//console.log($(btn2small).attr('class'));
						sizeSmallBtns();
					}
				}

				if (!--count) backUpFromSmall();
			});
		});

		// if first time running, put it through the magic layout
		if (init === true) {
			rrssbMagicLayout(sizeSmallBtns);
		}
	};

	var sizeSmallBtns = function() {
		// loop through each instance of buttons
		jQuery('.rrssb-buttons').each(function(index) {
			var self = jQuery(this);
			var regButtonCount,
					regPercent,
					pixelsOff,
					magicWidth,
					smallBtnFraction;

			// readjust buttons for small display
			var smallBtnCount = jQuery('li.small', self).length;

			// make sure there are small buttons
			if (smallBtnCount > 0 && smallBtnCount !== jQuery('li', self).length) {
				jQuery(self).removeClass('small-format');

				//make sure small buttons are square when not all small
				jQuery('li.small', self).css('width','42px');
				pixelsOff = smallBtnCount * 42;
				regButtonCount = jQuery('li', self).not('.small').length;
				regPercent = 100 / regButtonCount;
				smallBtnFraction = pixelsOff / regButtonCount;

				if (navigator.userAgent.indexOf('Chrome') >= 0 || navigator.userAgent.indexOf('Safari') >= 0) {
					magicWidth = '-webkit-calc('+regPercent+'% - '+smallBtnFraction+'px)';
				} else if (navigator.userAgent.indexOf('Firefox') >= 0) {
					magicWidth = '-moz-calc('+regPercent+'% - '+smallBtnFraction+'px)';
				} else {
					magicWidth = 'calc('+regPercent+'% - '+smallBtnFraction+'px)';
				}
				jQuery('li', self).not('.small').css('width', magicWidth);

			} else if (smallBtnCount === jQuery('li', self).length) {
				// if all buttons are small, change back to percentage
				jQuery(self).addClass('small-format');
				setPercentBtns();
			} else {
				jQuery(self).removeClass('small-format');
				setPercentBtns();
			}
		}); //end loop

		makeExtremityBtns();
	};

	var rrssbInit = function() {
		jQuery('.rrssb-buttons').each(function(index) {
			jQuery(this).addClass('rrssb-'+(index + 1));
		});

		setPercentBtns();

		// grab initial text width of each button and add as data attr
		jQuery('.rrssb-buttons li .rrssb-text').each(function(index) {
			var txtWdth = parseFloat(jQuery(this).width());
			jQuery(this).closest('li').attr('data-size', txtWdth);
		});

		checkSize(true);
	};

	var rrssbMagicLayout = function(callback) {
		//remove small buttons before each conversion try
		jQuery('.rrssb-buttons li.small').removeClass('small');

		checkSize();

		callback();
	};

	var popupCenter = function(url, title, w, h) {
		// Fixes dual-screen position                         Most browsers      Firefox
		var dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : screen.left;
		var dualScreenTop = window.screenTop !== undefined ? window.screenTop : screen.top;

		var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
		var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

		var left = ((width / 2) - (w / 2)) + dualScreenLeft;
		var top = ((height / 3) - (h / 3)) + dualScreenTop;

		var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

		// Puts focus on the newWindow
		if (window.focus) {
			newWindow.focus();
		}
	};

	var waitForFinalEvent = (function () {
		var timers = {};
		return function (callback, ms, uniqueId) {
			if (!uniqueId) {
				uniqueId = "Don't call this twice without a uniqueId";
			}
			if (timers[uniqueId]) {
				clearTimeout (timers[uniqueId]);
			}
			timers[uniqueId] = setTimeout(callback, ms);
		};
	})();

	// init load
	jQuery(document).ready(function(){
		/*
		 * Event listners
		 */

		jQuery('.rrssb-buttons a.popup').on('click', function(e){
			var _this = jQuery(this);
			popupCenter(_this.attr('href'), _this.find('.rrssb-text').html(), 580, 470);
			e.preventDefault();
		});

		// resize function
		jQuery(window).resize(function () {

			rrssbMagicLayout(sizeSmallBtns);

			waitForFinalEvent(function(){
				rrssbMagicLayout(sizeSmallBtns);
			}, 200, "finished resizing");
		});

		rrssbInit();
	});

	// Make global
	window.rrssbInit = rrssbInit;

})(window, jQuery);
;(function($) {
  'use strict';

  var _currentSpinnerId = 0;

  function _scopedEventName(name, id) {
    return name + '.touchspin_' + id;
  }

  function _scopeEventNames(names, id) {
    return $.map(names, function(name) {
      return _scopedEventName(name, id);
    });
  }

  $.fn.TouchSpin = function(options) {

    if (options === 'destroy') {
      this.each(function() {
        var originalinput = $(this),
            originalinput_data = originalinput.data();
        $(document).off(_scopeEventNames([
          'mouseup',
          'touchend',
          'touchcancel',
          'mousemove',
          'touchmove',
          'scroll',
          'scrollstart'], originalinput_data.spinnerid).join(' '));
      });
      return;
    }

    var defaults = {
      min: 0,
      max: 100,
      initval: '',
      step: 1,
      decimals: 0,
      stepinterval: 100,
      forcestepdivisibility: 'round', // none | floor | round | ceil
      stepintervaldelay: 500,
      verticalbuttons: false,
      verticalupclass: 'glyphicon glyphicon-chevron-up',
      verticaldownclass: 'glyphicon glyphicon-chevron-down',
      prefix: '',
      postfix: '',
      prefix_extraclass: '',
      postfix_extraclass: '',
      booster: true,
      boostat: 10,
      maxboostedstep: false,
      mousewheel: true,
      buttondown_class: 'btn btn-default',
      buttonup_class: 'btn btn-default'
    };

    var attributeMap = {
      min: 'min',
      max: 'max',
      initval: 'init-val',
      step: 'step',
      decimals: 'decimals',
      stepinterval: 'step-interval',
      verticalbuttons: 'vertical-buttons',
      verticalupclass: 'vertical-up-class',
      verticaldownclass: 'vertical-down-class',
      forcestepdivisibility: 'force-step-divisibility',
      stepintervaldelay: 'step-interval-delay',
      prefix: 'prefix',
      postfix: 'postfix',
      prefix_extraclass: 'prefix-extra-class',
      postfix_extraclass: 'postfix-extra-class',
      booster: 'booster',
      boostat: 'boostat',
      maxboostedstep: 'max-boosted-step',
      mousewheel: 'mouse-wheel',
      buttondown_class: 'button-down-class',
      buttonup_class: 'button-up-class'
    };

    return this.each(function() {

      var settings,
          originalinput = $(this),
          originalinput_data = originalinput.data(),
          container,
          elements,
          value,
          downSpinTimer,
          upSpinTimer,
          downDelayTimeout,
          upDelayTimeout,
          spincount = 0,
          spinning = false;

      init();


      function init() {
        if (originalinput.data('alreadyinitialized')) {
          return;
        }

        originalinput.data('alreadyinitialized', true);
        _currentSpinnerId += 1;
        originalinput.data('spinnerid', _currentSpinnerId);


        if (!originalinput.is('input')) {
          console.log('Must be an input.');
          return;
        }

        _initSettings();
        _setInitval();
        _checkValue();
        _buildHtml();
        _initElements();
        _hideEmptyPrefixPostfix();
        _bindEvents();
        _bindEventsInterface();
        elements.input.css('display', 'block');
      }

      function _setInitval() {
        if (settings.initval !== '' && originalinput.val() === '') {
          originalinput.val(settings.initval);
        }
      }

      function changeSettings(newsettings) {
        _updateSettings(newsettings);
        _checkValue();

        var value = elements.input.val();

        if (value !== '') {
          value = Number(elements.input.val());
          elements.input.val(value.toFixed(settings.decimals));
        }
      }

      function _initSettings() {
        settings = $.extend({}, defaults, originalinput_data, _parseAttributes(), options);
      }

      function _parseAttributes() {
        var data = {};
        $.each(attributeMap, function(key, value) {
          var attrName = 'bts-' + value + '';
          if (originalinput.is('[data-' + attrName + ']')) {
            data[key] = originalinput.data(attrName);
          }
        });
        return data;
      }

      function _updateSettings(newsettings) {
        settings = $.extend({}, settings, newsettings);
      }

      function _buildHtml() {
        var initval = originalinput.val(),
            parentelement = originalinput.parent();

        if (initval !== '') {
          initval = Number(initval).toFixed(settings.decimals);
        }

        originalinput.data('initvalue', initval).val(initval);
        originalinput.addClass('form-control');

        if (parentelement.hasClass('input-group')) {
          _advanceInputGroup(parentelement);
        }
        else {
          _buildInputGroup();
        }
      }

      function _advanceInputGroup(parentelement) {
        parentelement.addClass('bootstrap-touchspin');

        var prev = originalinput.prev(),
            next = originalinput.next();

        var downhtml,
            uphtml,
            prefixhtml = '<span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span>',
            postfixhtml = '<span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span>';

        if (prev.hasClass('input-group-btn')) {
          downhtml = '<button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button>';
          prev.append(downhtml);
        }
        else {
          downhtml = '<span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button></span>';
          $(downhtml).insertBefore(originalinput);
        }

        if (next.hasClass('input-group-btn')) {
          uphtml = '<button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button>';
          next.prepend(uphtml);
        }
        else {
          uphtml = '<span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button></span>';
          $(uphtml).insertAfter(originalinput);
        }

        $(prefixhtml).insertBefore(originalinput);
        $(postfixhtml).insertAfter(originalinput);

        container = parentelement;
      }

      function _buildInputGroup() {
        var html;

        if (settings.verticalbuttons) {
          html = '<div class="input-group bootstrap-touchspin"><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn-vertical"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-up" type="button"><i class="' + settings.verticalupclass + '"></i></button><button class="' + settings.buttonup_class + ' bootstrap-touchspin-down" type="button"><i class="' + settings.verticaldownclass + '"></i></button></span></div>';
        }
        else {
          html = '<div class="input-group bootstrap-touchspin"><span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button></span><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button></span></div>';
        }

        container = $(html).insertBefore(originalinput);

        $('.bootstrap-touchspin-prefix', container).after(originalinput);

        if (originalinput.hasClass('input-sm')) {
          container.addClass('input-group-sm');
        }
        else if (originalinput.hasClass('input-lg')) {
          container.addClass('input-group-lg');
        }
      }

      function _initElements() {
        elements = {
          down: $('.bootstrap-touchspin-down', container),
          up: $('.bootstrap-touchspin-up', container),
          input: $('input', container),
          prefix: $('.bootstrap-touchspin-prefix', container).addClass(settings.prefix_extraclass),
          postfix: $('.bootstrap-touchspin-postfix', container).addClass(settings.postfix_extraclass)
        };
      }

      function _hideEmptyPrefixPostfix() {
        if (settings.prefix === '') {
          elements.prefix.hide();
        }

        if (settings.postfix === '') {
          elements.postfix.hide();
        }
      }

      function _bindEvents() {
        originalinput.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
          else if (code === 40) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        originalinput.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            stopSpin();
          }
          else if (code === 40) {
            stopSpin();
          }
        });

        originalinput.on('blur', function() {
          _checkValue();
        });

        elements.down.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        elements.down.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.up.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
        });

        elements.up.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.down.on('mousedown.touchspin', function(ev) {
          elements.down.off('touchstart.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.down.on('touchstart.touchspin', function(ev) {
          elements.down.off('mousedown.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mousedown.touchspin', function(ev) {
          elements.up.off('touchstart.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('touchstart.touchspin', function(ev) {
          elements.up.off('mousedown.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mouseout touchleave touchend touchcancel', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mouseout touchleave touchend touchcancel', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mousemove touchmove', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        elements.up.on('mousemove touchmove', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        $(document).on(_scopeEventNames(['mouseup', 'touchend', 'touchcancel'], _currentSpinnerId).join(' '), function(ev) {
          if (!spinning) {
            return;
          }

          ev.preventDefault();
          stopSpin();
        });

        $(document).on(_scopeEventNames(['mousemove', 'touchmove', 'scroll', 'scrollstart'], _currentSpinnerId).join(' '), function(ev) {
          if (!spinning) {
            return;
          }

          ev.preventDefault();
          stopSpin();
        });

        originalinput.on('mousewheel DOMMouseScroll', function(ev) {
          if (!settings.mousewheel || !originalinput.is(':focus')) {
            return;
          }

          var delta = ev.originalEvent.wheelDelta || -ev.originalEvent.deltaY || -ev.originalEvent.detail;

          ev.stopPropagation();
          ev.preventDefault();

          if (delta < 0) {
            downOnce();
          }
          else {
            upOnce();
          }
        });
      }

      function _bindEventsInterface() {
        originalinput.on('touchspin.uponce', function() {
          stopSpin();
          upOnce();
        });

        originalinput.on('touchspin.downonce', function() {
          stopSpin();
          downOnce();
        });

        originalinput.on('touchspin.startupspin', function() {
          startUpSpin();
        });

        originalinput.on('touchspin.startdownspin', function() {
          startDownSpin();
        });

        originalinput.on('touchspin.stopspin', function() {
          stopSpin();
        });

        originalinput.on('touchspin.updatesettings', function(e, newsettings) {
          changeSettings(newsettings);
        });
      }

      function _forcestepdivisibility(value) {
        switch (settings.forcestepdivisibility) {
          case 'round':
            return (Math.round(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'floor':
            return (Math.floor(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'ceil':
            return (Math.ceil(value / settings.step) * settings.step).toFixed(settings.decimals);
          default:
            return value;
        }
      }

      function _checkValue() {
        var val, parsedval, returnval;

        val = originalinput.val();

        if (val === '') {
          return;
        }

        if (settings.decimals > 0 && val === '.') {
          return;
        }

        parsedval = parseFloat(val);

        if (isNaN(parsedval)) {
          parsedval = 0;
        }

        returnval = parsedval;

        if (parsedval.toString() !== val) {
          returnval = parsedval;
        }

        if (parsedval < settings.min) {
          returnval = settings.min;
        }

        if (parsedval > settings.max) {
          returnval = settings.max;
        }

        returnval = _forcestepdivisibility(returnval);

        if (Number(val).toString() !== returnval.toString()) {
          originalinput.val(returnval);
          originalinput.trigger('change');
        }
      }

      function _getBoostedStep() {
        if (!settings.booster) {
          return settings.step;
        }
        else {
          var boosted = Math.pow(2, Math.floor(spincount / settings.boostat)) * settings.step;

          if (settings.maxboostedstep) {
            if (boosted > settings.maxboostedstep) {
              boosted = settings.maxboostedstep;
              value = Math.round((value / boosted)) * boosted;
            }
          }

          return Math.max(settings.step, boosted);
        }
      }

      function upOnce() {
        _checkValue();

        value = parseFloat(elements.input.val());
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
            boostedstep = _getBoostedStep();

        value = value + boostedstep;

        if (value > settings.max) {
          value = settings.max;
          originalinput.trigger('touchspin.on.max');
          stopSpin();
        }

        elements.input.val(Number(value).toFixed(settings.decimals));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function downOnce() {
        _checkValue();

        value = parseFloat(elements.input.val());
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
            boostedstep = _getBoostedStep();

        value = value - boostedstep;

        if (value < settings.min) {
          value = settings.min;
          originalinput.trigger('touchspin.on.min');
          stopSpin();
        }

        elements.input.val(value.toFixed(settings.decimals));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function startDownSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'down';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startdownspin');

        downDelayTimeout = setTimeout(function() {
          downSpinTimer = setInterval(function() {
            spincount++;
            downOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function startUpSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'up';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startupspin');

        upDelayTimeout = setTimeout(function() {
          upSpinTimer = setInterval(function() {
            spincount++;
            upOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function stopSpin() {
        clearTimeout(downDelayTimeout);
        clearTimeout(upDelayTimeout);
        clearInterval(downSpinTimer);
        clearInterval(upSpinTimer);

        switch (spinning) {
          case 'up':
            originalinput.trigger('touchspin.on.stopupspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
          case 'down':
            originalinput.trigger('touchspin.on.stopdownspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
        }

        spincount = 0;
        spinning = false;
      }

    });

  };

})(jQuery);
;;(function ( $, window, document, undefined ) {
	
		var pluginName = "DateTimePicker";
	
		var defaults = {
		
			mode: "date",
			defaultDate: new Date(),
		
			dateSeparator: "-",
			timeSeparator: ":",
			timeMeridiemSeparator: " ",
			dateTimeSeparator: " ",
		
			dateTimeFormat: "dd-MM-yyyy HH:mm:ss",
			dateFormat: "dd-MM-yyyy",
			timeFormat: "HH:mm",
		
			maxDate: null,
			minDate:  null,
		
			maxTime: null,
			minTime: null,
		
			maxDateTime: null,
			minDateTime: null,
		
			shortDayNames: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
			fullDayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
			shortMonthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			fullMonthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		
			titleContentDate: "Set Date",
			titleContentTime: "Set Time",
			titleContentDateTime: "Set Date & Time",
		
			setButtonContent: "Set",
			clearButtonContent: "Clear",
		
			animationDuration: 400,
		
			isPopup: true,
		
			addEventHandlers: null
		};
	
		var dataObject = {
		
			dCurrentDate: new Date(),
			iCurrentDay: 0,
			iCurrentMonth: 0,
			iCurrentYear: 0,
			iCurrentHour: 0,
			iCurrentMinutes: 0,
			sCurrentMeridiem: "",
			iMaxNumberOfDays: 0,
		
			sDateFormat: "",
			sTimeFormat: "",
			sDateTimeFormat: "",
		
			dMinValue: null,
			dMaxValue: null,
		
			sArrInputDateFormats: [],
			sArrInputTimeFormats: [],
			sArrInputDateTimeFormats: [],
		
			oInputElement: null,
		
			bIs12Hour: false	
		};
	
		function DateTimePicker(element, options)
		{
			this.element = element;
			this.settings = $.extend({}, defaults, options);
			this.dataObject = dataObject;
			this._defaults = defaults;
			this._name = pluginName;
		
			this.init();
		}
	
		$.fn.DateTimePicker = function (options)
		{
			return this.each(function() 
			{
				if (!$.data(this, "plugin_" + pluginName)) 
				{
					$.data(this, "plugin_" + pluginName, new DateTimePicker(this, options));
				}
			});
		};
	
		DateTimePicker.prototype = {
		
			init: function () 
			{
				var dtPickerObj = this;					
			
				dtPickerObj._setDateFormatArray(); // Set DateFormatArray
				dtPickerObj._setTimeFormatArray(); // Set TimeFormatArray
				dtPickerObj._setDateTimeFormatArray(); // Set DateTimeFormatArray
			
				if(dtPickerObj.settings.isPopup)
				{
					dtPickerObj._createPicker();
					$(dtPickerObj.element).addClass("dtpicker-mobile");
				}
				dtPickerObj._addEventHandlersForInput();
			},
		
			_setDateFormatArray: function()
			{
				var dtPickerObj = this;
			
				dtPickerObj.dataObject.sArrInputDateFormats = new Array();		
				var sDate = "";
			
				//  "dd-MM-yyyy"
				sDate = "dd" + dtPickerObj.settings.dateSeparator + "MM" + dtPickerObj.settings.dateSeparator + "yyyy";
				dtPickerObj.dataObject.sArrInputDateFormats.push(sDate);
			
				//  "MM-dd-yyyy"
				sDate = "MM" + dtPickerObj.settings.dateSeparator + "dd" + dtPickerObj.settings.dateSeparator + "yyyy";
				dtPickerObj.dataObject.sArrInputDateFormats.push(sDate);
			
				//  "yyyy-MM-dd"
				sDate = "yyyy" + dtPickerObj.settings.dateSeparator + "MM" + dtPickerObj.settings.dateSeparator + "dd";
				dtPickerObj.dataObject.sArrInputDateFormats.push(sDate);
				
				// "dd-MMM-yyyy"
				sDate = "dd" + dtPickerObj.settings.dateSeparator + "MMM" + dtPickerObj.settings.dateSeparator + "yyyy";
				dtPickerObj.dataObject.sArrInputDateFormats.push(sDate);
			},
		
			_setTimeFormatArray: function()
			{
				var dtPickerObj = this;
			
				dtPickerObj.dataObject.sArrInputTimeFormats = new Array();
				var sTime = "";
			
				//  "hh:mm AA"
				sTime = "hh" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeMeridiemSeparator + "AA";
				dtPickerObj.dataObject.sArrInputTimeFormats.push(sTime);
			
				//  "HH:mm"
				sTime = "HH" + dtPickerObj.settings.timeSeparator + "mm";
				dtPickerObj.dataObject.sArrInputTimeFormats.push(sTime);
			},
		
			_setDateTimeFormatArray: function()
			{
				var dtPickerObj = this;
			
				dtPickerObj.dataObject.sArrInputDateTimeFormats = new Array();
				var sDate = "", sTime = "", sDateTime = "";
			
				//  "dd-MM-yyyy HH:mm:ss"
				sDate = "dd" + dtPickerObj.settings.dateSeparator + "MM" + dtPickerObj.settings.dateSeparator + "yyyy";
				sTime = "HH" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
			
				//  "dd-MM-yyyy hh:mm:ss AA"
				sDate = "dd" + dtPickerObj.settings.dateSeparator + "MM" + dtPickerObj.settings.dateSeparator + "yyyy";
				sTime = "hh" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss" + dtPickerObj.settings.timeMeridiemSeparator + "AA";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
			
				//  "MM-dd-yyyy HH:mm:ss"
				sDate = "MM" + dtPickerObj.settings.dateSeparator + "dd" + dtPickerObj.settings.dateSeparator + "yyyy";
				sTime = "HH" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
			
				//  "MM-dd-yyyy hh:mm:ss AA"
				sDate = "MM" + dtPickerObj.settings.dateSeparator + "dd" + dtPickerObj.settings.dateSeparator + "yyyy";
				sTime = "hh" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss" + dtPickerObj.settings.timeMeridiemSeparator + "AA";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
			
				//  "yyyy-MM-dd HH:mm:ss"
				sDate = "yyyy" + dtPickerObj.settings.dateSeparator + "MM" + dtPickerObj.settings.dateSeparator + "dd";
				sTime = "HH" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
			
				//  "yyyy-MM-dd hh:mm:ss AA"
				sDate = "yyyy" + dtPickerObj.settings.dateSeparator + "MM" + dtPickerObj.settings.dateSeparator + "dd";
				sTime = "hh" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss" + dtPickerObj.settings.timeMeridiemSeparator + "AA";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
				
				//  "dd-MMM-yyyy hh:mm:ss"
				sDate = "dd" + dtPickerObj.settings.dateSeparator + "MMM" + dtPickerObj.settings.dateSeparator + "yyyy";
				sTime = "hh" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
				
				//  "dd-MMM-yyyy hh:mm:ss AA"
				sDate = "dd" + dtPickerObj.settings.dateSeparator + "MMM" + dtPickerObj.settings.dateSeparator + "yyyy";
				sTime = "hh" + dtPickerObj.settings.timeSeparator + "mm" + dtPickerObj.settings.timeSeparator + "ss" + dtPickerObj.settings.timeMeridiemSeparator + "AA";
				sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				dtPickerObj.dataObject.sArrInputDateTimeFormats.push(sDateTime);
			},
		
			_createPicker: function()
			{
				var dtPickerObj = this;
			
				$(dtPickerObj.element).addClass("dtpicker-overlay");
			
				var sTempStr = "";	
				sTempStr += "<div class='dtpicker-bg'>";
				sTempStr += "<div class='dtpicker-cont'>";
				sTempStr += "<div class='dtpicker-content'>";
				sTempStr += "<div class='dtpicker-subcontent'>";
				sTempStr += "</div>";
				sTempStr += "</div>";
				sTempStr += "</div>";
				sTempStr += "</div>";
				$(dtPickerObj.element).html(sTempStr);
			},
		
			_addEventHandlersForInput: function()
			{
				var dtPickerObj = this;
			
				$("input[type='date'], input[type='time'], input[type='datetime']").each(function()
				{
					var sType = $(this).attr("type");
					$(this).attr("type", "text");
					$(this).attr("data-field", sType);
				});
			
				$("[data-field='date'], [data-field='time'], [data-field='datetime']").unbind("focus", dtPickerObj._inputFieldFocus);
				$("[data-field='date'], [data-field='time'], [data-field='datetime']").on("focus", {"obj": dtPickerObj}, dtPickerObj._inputFieldFocus);
			
				$("[data-field='date'], [data-field='time'], [data-field='datetime']").not('input').click(function()
				{
					if(dtPickerObj.dataObject.oInputElement == null)
					{
						dtPickerObj.showDateTimePicker(this);
					}
				});
			
				if(dtPickerObj.settings.addEventHandlers)
					dtPickerObj.settings.addEventHandlers.call(dtPickerObj);
			},
		
			_inputFieldFocus: function(e)
			{
				var dtPickerObj = e.data.obj;
			
				if(dtPickerObj.dataObject.oInputElement == null)
				{
					dtPickerObj.showDateTimePicker(e.target);
				}
			},
		
			showDateTimePicker: function(element)
			{
				var dtPickerObj = this;
			
				if(dtPickerObj.dataObject.oInputElement == null)
				{
					dtPickerObj.dataObject.oInputElement = element;
				
					var sMode = $(element).data("field") || "";
					var sMinValue = $(element).data("min") || "";
					var sMaxValue = $(element).data("max") || "";
					var sFormat = $(element).data("format") || "";
					var sView = $(element).data("view") || "";
					var sStartEnd = $(element).data("startend") || "";
					var sStartEndElem = $(element).data("startendelem") || "";
					var sCurrent = dtPickerObj._getValueOfElement(element) || "";
				
					if(sView != "")
					{
						if(dtPickerObj._compare(sView, "Popup"))
							dtPickerObj.setIsPopup(true);
						else 
							dtPickerObj.setIsPopup(false);
					}
				
					if(! dtPickerObj.settings.isPopup)
					{
						dtPickerObj._createPicker();
					
						var iElemTop = $(dtPickerObj.dataObject.oInputElement).offset().top + $(dtPickerObj.dataObject.oInputElement).outerHeight();
						var iElemLeft = $(dtPickerObj.dataObject.oInputElement).offset().left;
						var iElemWidth =  $(dtPickerObj.dataObject.oInputElement).outerWidth();
					
						$(dtPickerObj.element).css({position: "absolute", top: iElemTop, left: iElemLeft, width: iElemWidth, height: "auto"});
					}
				
					dtPickerObj._showPicker(sMode, sMinValue, sMaxValue, sFormat, sCurrent, element, sStartEnd, sStartEndElem);
				}
			},
		
			_setButtonAction: function()
			{
				var dtPickerObj = this;
			
				if(dtPickerObj.dataObject.oInputElement != null)
				{
					var sOutput = dtPickerObj._setOutput();
					dtPickerObj._setValueOfElement(sOutput);
					dtPickerObj._hidePicker();
				}
			},
		
			_setOutput: function()
			{
				var dtPickerObj = this;
			
				var sOutput = "";
			
				var iDate = dtPickerObj.dataObject.dCurrentDate.getDate();
				var iMonth = dtPickerObj.dataObject.dCurrentDate.getMonth();
				var iYear = dtPickerObj.dataObject.dCurrentDate.getFullYear();
				var iHour = dtPickerObj.dataObject.dCurrentDate.getHours();
				var iMinutes = dtPickerObj.dataObject.dCurrentDate.getMinutes();
			
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "date"))
				{
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[0]))
					{
						iMonth++;
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = (iMonth < 10) ? ("0" + iMonth) : iMonth;
						
						sOutput = sDate + dtPickerObj.settings.dateSeparator + sMonth + dtPickerObj.settings.dateSeparator + iYear;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[1]))
					{
						iMonth++;
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = (iMonth < 10) ? ("0" + iMonth) : iMonth;
						
						sOutput = sMonth + dtPickerObj.settings.dateSeparator + sDate + dtPickerObj.settings.dateSeparator + iYear;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[2]))
					{
						iMonth++;
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = (iMonth < 10) ? ("0" + iMonth) : iMonth;
						
						sOutput = iYear + dtPickerObj.settings.dateSeparator + sMonth + dtPickerObj.settings.dateSeparator + sDate;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[3]))
					{
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = dtPickerObj.settings.shortMonthNames[iMonth];
					
						sOutput = sDate + dtPickerObj.settings.dateSeparator + sMonth + dtPickerObj.settings.dateSeparator + iYear;
					}
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
				{
					if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[0]))
					{
						var sMeridiem = "";
						if(iHour > 12)
						{
							iHour -= 12;
							sMeridiem = "PM";
						}
						else if(iHour == 12 && iMinutes > 0)
						{
							sMeridiem = "PM";
						}
						else
						{
							sMeridiem = "AM";
						}
					
						var sHour = (iHour < 10) ? ("0" + iHour) : iHour;
						var sMinutes = (iMinutes < 10) ? ("0" + iMinutes) : iMinutes;
					
						sOutput = sHour + dtPickerObj.settings.timeSeparator + sMinutes + dtPickerObj.settings.timeMeridiemSeparator + sMeridiem;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[1]))
					{
						var sHour = (iHour < 10) ? ("0" + iHour) : iHour;
						var sMinutes = (iMinutes < 10) ? ("0" + iMinutes) : iMinutes;
					
						sOutput = sHour + dtPickerObj.settings.timeSeparator + sMinutes;
					}
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					var sDateStr = "";
					var sTimeStr = "";
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[0]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[1]))
					{
						iMonth++;
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = (iMonth < 10) ? ("0" + iMonth) : iMonth;
					
						sDateStr = sDate + dtPickerObj.settings.dateSeparator + sMonth + dtPickerObj.settings.dateSeparator + iYear;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[2]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[3]))
					{
						iMonth++;
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = (iMonth < 10) ? ("0" + iMonth) : iMonth;
					
						sDateStr = sMonth + dtPickerObj.settings.dateSeparator + sDate + dtPickerObj.settings.dateSeparator + iYear;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[4]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[5]))
					{
						iMonth++;
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = (iMonth < 10) ? ("0" + iMonth) : iMonth;
					
						sDateStr = iYear + dtPickerObj.settings.dateSeparator + sMonth + dtPickerObj.settings.dateSeparator + sDate;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[6]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[7]))
					{
						var sDate = (iDate < 10) ? ("0" + iDate) : iDate;
						var sMonth = dtPickerObj.settings.shortMonthNames[iMonth];
					
						sDateStr = sDate + dtPickerObj.settings.dateSeparator + sMonth + dtPickerObj.settings.dateSeparator + iYear;
					}
				
					if(dtPickerObj.dataObject.bIs12Hour)
					{
						var sMeridiem = "";
						if(iHour > 12)
						{
							iHour -= 12;
							sMeridiem = "PM";
						}
						else if(iHour == 12 && iMinutes > 0)
						{
							sMeridiem = "PM";
						}
						else
						{
							sMeridiem = "AM";
						}
					
						var sHour = (iHour < 10) ? ("0" + iHour) : iHour;
						var sMinutes = (iMinutes < 10) ? ("0" + iMinutes) : iMinutes;
					
						sTimeStr = sHour + dtPickerObj.settings.timeSeparator + sMinutes + dtPickerObj.settings.timeMeridiemSeparator + sMeridiem;
					}
					else
					{
						var sHour = (iHour < 10) ? ("0" + iHour) : iHour;
						var sMinutes = (iMinutes < 10) ? ("0" + iMinutes) : iMinutes;
					
						sTimeStr = sHour + dtPickerObj.settings.timeSeparator + sMinutes;
					}
				
					sOutput = sDateStr + dtPickerObj.settings.dateTimeSeparator + sTimeStr;
				}
			
				return sOutput;
			},
		
			_clearButtonAction: function()
			{
				var dtPickerObj = this;
			
				if(dtPickerObj.dataObject.oInputElement != null)
				{
					dtPickerObj._setValueOfElement("");
				}
				dtPickerObj._hidePicker();
			},
		
			_showPicker: function(sMode, sMinValue, sMaxValue, sFormat, sCurrent, oElement, sStartEnd, sStartEndElem)
			{
				var dtPickerObj = this;
			
				if(sMode != "")
					dtPickerObj.settings.mode = sMode;
			
				dtPickerObj.dataObject.dMinValue = null;
				dtPickerObj.dataObject.dMaxValue = null;
				dtPickerObj.dataObject.bIs12Hour = false;
			
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "date"))
				{
					var sMin = sMinValue || dtPickerObj.settings.minDate;
					var sMax = sMaxValue || dtPickerObj.settings.maxDate;
				
					var sDateFormat = sFormat || dtPickerObj.settings.dateFormat;
					if(sDateFormat != "" && sDateFormat != null)
						dtPickerObj.dataObject.sDateFormat = sDateFormat;
				
					if(sMin != "" && sMin != null)
						dtPickerObj.dataObject.dMinValue = dtPickerObj._parseDate(sMin);
					if(sMax != "" && sMax != null)
						dtPickerObj.dataObject.dMaxValue = dtPickerObj._parseDate(sMax);
				
					
				
					if(sStartEnd != "" && (dtPickerObj._compare(sStartEnd, "start") || dtPickerObj._compare(sStartEnd, "end")) && sStartEndElem != "")
					{
						if($(sStartEndElem).length >= 1)
						{
							var sTempDate = dtPickerObj._getValueOfElement($(sStartEndElem));
							if(sTempDate != "")
							{
								var dTempDate = dtPickerObj._parseDate(sTempDate);
								if(dtPickerObj._compare(sStartEnd, "start"))
								{
									if(sMax != "" && sMax != null)
									{
										if(dtPickerObj._compareDates(dTempDate, dtPickerObj.dataObject.dMaxValue) == 2)
											dtPickerObj.dataObject.dMaxValue = new Date(dTempDate);
									}
									else
										dtPickerObj.dataObject.dMaxValue = new Date(dTempDate);
								}
								else if(dtPickerObj._compare(sStartEnd, "end"))
								{
									if(sMin != "" && sMin != null)
									{
										if(dtPickerObj._compareDates(dTempDate, dtPickerObj.dataObject.dMinValue) == 3)
											dtPickerObj.dataObject.dMinValue = new Date(dTempDate);
									}
									else
										dtPickerObj.dataObject.dMinValue = new Date(dTempDate);
								}
							}
						}
					}
				
					dtPickerObj.dataObject.dCurrentDate = dtPickerObj._parseDate(sCurrent);
					dtPickerObj.dataObject.dCurrentDate.setHours(0);
					dtPickerObj.dataObject.dCurrentDate.setMinutes(0);
					dtPickerObj.dataObject.dCurrentDate.setSeconds(0);
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
				{
					var sMin = sMinValue || dtPickerObj.settings.minTime;
					var sMax = sMaxValue || dtPickerObj.settings.maxTime;
				
					var sTimeFormat = sFormat || dtPickerObj.settings.timeFormat;
					if(sTimeFormat != "" && sTimeFormat != null)
						dtPickerObj.dataObject.sTimeFormat = sTimeFormat;
				
					if(sMin != "" && sMin != null)
						dtPickerObj.dataObject.dMinValue = dtPickerObj._parseTime(sMin);
					if(sMax != "" && sMax != null)
						dtPickerObj.dataObject.dMaxValue = dtPickerObj._parseTime(sMax);
				
					if(sStartEnd != "" && (dtPickerObj._compare(sStartEnd, "start") || dtPickerObj._compare(sStartEnd, "end")) && sStartEndElem != "")
					{
						if($(sStartEndElem).length >= 1)
						{
							var sTempTime = dtPickerObj._getValueOfElement($(sStartEndElem));
						
							if(sTempTime != "")
							{
								var dTempTime = dtPickerObj._parseTime(sTempTime);
								if(dtPickerObj._compare(sStartEnd, "start"))
								{
									dTempTime.setMinutes(dTempTime.getMinutes() - 1);
									if(sMax != "" && sMax != null)
									{
										if(dtPickerObj._compareTime(dTempTime, dtPickerObj.dataObject.dMaxValue) == 2)
											dtPickerObj.dataObject.dMaxValue = new Date(dTempTime);
									}
									else
										dtPickerObj.dataObject.dMaxValue = new Date(dTempTime);
								}
								else if(dtPickerObj._compare(sStartEnd, "end"))
								{
									dTempTime.setMinutes(dTempTime.getMinutes() + 1);
									if(sMin != "" && sMin != null)
									{
										if(dtPickerObj._compareTime(dTempTime, dtPickerObj.dataObject.dMinValue) == 3)
											dtPickerObj.dataObject.dMinValue = new Date(dTempTime);
									}
									else
										dtPickerObj.dataObject.dMinValue = new Date(dTempTime);
								}
							}
						}
					}
				
					dtPickerObj.dataObject.dCurrentDate = dtPickerObj._parseTime(sCurrent);
					dtPickerObj.dataObject.bIs12Hour = dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[0]);
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					var sMin = sMinValue || dtPickerObj.settings.minDateTime;
					var sMax = sMaxValue || dtPickerObj.settings.maxDateTime;
				
					var sDateTimeFormat = sFormat || dtPickerObj.settings.dateTimeFormat;
					if(sDateTimeFormat != "" && sDateTimeFormat != null)
						dtPickerObj.dataObject.sDateTimeFormat = sDateTimeFormat;
				
					if(sMin != "" && sMin != null)
						dtPickerObj.dataObject.dMinValue = dtPickerObj._parseDateTime(sMin);
					if(sMax != "" && sMax != null)
						dtPickerObj.dataObject.dMaxValue = dtPickerObj._parseDateTime(sMax);
				
					if(sStartEnd != "" && (dtPickerObj._compare(sStartEnd, "start") || dtPickerObj._compare(sStartEnd, "end")) && sStartEndElem != "")
					{
						if($(sStartEndElem).length >= 1)
						{
							var sTempDateTime = dtPickerObj._getValueOfElement($(sStartEndElem));
							if(sTempDateTime != "")
							{
								var dTempDateTime = dtPickerObj._parseDateTime(sTempDateTime);
								if(dtPickerObj._compare(sStartEnd, "start"))
								{
									if(sMax != "" && sMax != null)
									{
										if(dtPickerObj._compareDateTime(dTempDateTime, dtPickerObj.dataObject.dMaxValue) == 2)
											dtPickerObj.dataObject.dMaxValue = new Date(dTempDateTime);
									}
									else
										dtPickerObj.dataObject.dMaxValue = new Date(dTempDateTime);
								}
								else if(dtPickerObj._compare(sStartEnd, "end"))
								{
									if(sMin != "" && sMin != null)
									{
										if(dtPickerObj._compareDateTime(dTempDateTime, dtPickerObj.dataObject.dMinValue) == 3)
											dtPickerObj.dataObject.dMinValue = new Date(dTempDateTime);
									}
									else
										dtPickerObj.dataObject.dMinValue = new Date(dTempDateTime);
								}
							}
						}
					}
				
					dtPickerObj.dataObject.dCurrentDate = dtPickerObj._parseDateTime(sCurrent);
				
					dtPickerObj.dataObject.bIs12Hour = dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[1]) ||
					dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[3]) ||
					dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[5]) ||
					dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[7]);
				}
			
				dtPickerObj._setVariablesForDate();
				dtPickerObj._modifyPicker();
				$(dtPickerObj.element).fadeIn(dtPickerObj.settings.animationDuration);
			},
		
			_hidePicker: function(iDuration)
			{
				var dtPickerObj = this;
			
				if(dtPickerObj.dataObject.oInputElement != null)
				{
					$(dtPickerObj.dataObject.oInputElement).blur();
					dtPickerObj.dataObject.oInputElement = null;
				}
			
				$(dtPickerObj.element).fadeOut(iDuration || dtPickerObj.settings.animationDuration);
				setTimeout(function()
				{
					$(dtPickerObj.element).find('.dtpicker-subcontent').html("");
				}, (iDuration || dtPickerObj.settings.animationDuration));
			},
		
			_modifyPicker: function()
			{
				var dtPickerObj = this;
			
				var sTitleContent, iNumberOfColumns;
				var sArrFields = new Array();
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "date"))
				{
					sTitleContent = dtPickerObj.settings.titleContentDate;
					iNumberOfColumns = 3;
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[0]))  // "dd-MM-yyyy"
					{
						sArrFields = ["day", "month", "year"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[1]))  // "MM-dd-yyyy"
					{
						sArrFields = ["month", "day", "year"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[2]))  // "yyyy-MM-dd"
					{
						sArrFields = ["year", "month", "day"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[3]))  // "dd-MMM-yyyy"
					{
						sArrFields = ["day", "month", "year"];
					}
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
				{
					sTitleContent = dtPickerObj.settings.titleContentTime;
					if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[0]))
					{
						iNumberOfColumns = 3;
						sArrFields = ["hour", "minutes", "meridiem"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[1]))
					{
						iNumberOfColumns = 2;
						sArrFields = ["hour", "minutes"];
					}
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					sTitleContent = dtPickerObj.settings.titleContentDateTime;
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[0]))
					{
						iNumberOfColumns = 5;
						sArrFields = ["day", "month", "year", "hour", "minutes"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[1]))
					{
						iNumberOfColumns = 6;
						sArrFields = ["day", "month", "year", "hour", "minutes", "meridiem"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[2]))
					{
						iNumberOfColumns = 5;
						sArrFields = ["month", "day", "year", "hour", "minutes"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[3]))
					{
						iNumberOfColumns = 6;
						sArrFields = ["month", "day", "year", "hour", "minutes", "meridiem"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[4]))
					{
						iNumberOfColumns = 5;
						sArrFields = ["year", "month", "day", "hour", "minutes"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[5]))
					{
						iNumberOfColumns = 6;
						sArrFields = ["year", "month", "day", "hour", "minutes", "meridiem"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[6]))
					{
						iNumberOfColumns = 5;
						sArrFields = ["day", "month", "year", "hour", "minutes"];
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[7]))
					{
						iNumberOfColumns = 6;
						sArrFields = ["day", "month", "year", "hour", "minutes", "meridiem"];
					}
				}
				var sColumnClass = "dtpicker-comp" + iNumberOfColumns;
			
				//--------------------------------------------------------------------
			
				var sHeader = "";
				sHeader += "<div class='dtpicker-header'>";
				sHeader += "<div class='dtpicker-title'>" + sTitleContent + "</div>";
				sHeader += "<a class='dtpicker-close'>X</a>";
				sHeader += "<div class='dtpicker-value'></div>";
				sHeader += "</div>";
			
				//--------------------------------------------------------------------
			
				var sDTPickerComp = "";
				sDTPickerComp += "<div class='dtpicker-components'>";
			
				for(var iTempIndex = 0; iTempIndex < iNumberOfColumns; iTempIndex++)
				{
					var sFieldName = sArrFields[iTempIndex];
				
					sDTPickerComp += "<div class='dtpicker-compOutline " + sColumnClass + "'>";
					sDTPickerComp += "<div class='dtpicker-comp " + sFieldName + "'>";
					sDTPickerComp += "<a class='dtpicker-compButton increment'>+</a>";
					sDTPickerComp += "<input type='text' class='dtpicker-compValue'></input>";
					sDTPickerComp += "<a class='dtpicker-compButton decrement'>-</a>";
					sDTPickerComp += "</div>";
					sDTPickerComp += "</div>";
				}
			
				sDTPickerComp += "</div>";
			
				//--------------------------------------------------------------------
			
				var sDTPickerButtons = "";
				sDTPickerButtons += "<div class='dtpicker-buttonCont'>";
				sDTPickerButtons += "<a class='dtpicker-button dtpicker-buttonSet'>" + dtPickerObj.settings.setButtonContent + "</a>";
				sDTPickerButtons += "<a class='dtpicker-button dtpicker-buttonClear'>" + dtPickerObj.settings.clearButtonContent + "</a>";
				sDTPickerButtons += "</div>";
			
				//--------------------------------------------------------------------
			
				sTempStr = sHeader + sDTPickerComp + sDTPickerButtons;
			
				$(dtPickerObj.element).find('.dtpicker-subcontent').html(sTempStr);
			
				dtPickerObj._setCurrentDate();
				dtPickerObj._addEventHandlersForPicker();
			},
		
			_addEventHandlersForPicker: function()
			{
				var dtPickerObj = this;
			
				$('.dtpicker-compValue').not('.month .dtpicker-compValue, .meridiem .dtpicker-compValue').keyup(function() 
				{ 
					this.value = this.value.replace(/[^0-9\.]/g,'');
				});
			
				$('.dtpicker-compValue').blur(function()
				{
					dtPickerObj._getValuesFromInputBoxes();
					dtPickerObj._setCurrentDate();
				});
			
				$(".dtpicker-comp .dtpicker-compValue").keyup(function()
				{
					var $oTextField = $(this);
				
					var sTextBoxVal = $oTextField.val();
					var iLength = sTextBoxVal.length;
				
					if($oTextField.parent().hasClass("day") || $oTextField.parent().hasClass("hour") || $oTextField.parent().hasClass("minutes") || $oTextField.parent().hasClass("meridiem"))
					{
						if(iLength > 2)
						{
							var sNewTextBoxVal = sTextBoxVal.slice(0, 2);
							$oTextField.val(sNewTextBoxVal);
						}
					}
					else if($oTextField.parent().hasClass("month"))
					{
						if(iLength > 3)
						{
							var sNewTextBoxVal = sTextBoxVal.slice(0, 3);
							$oTextField.val(sNewTextBoxVal);
						}
					}
					else if($oTextField.parent().hasClass("year"))
					{
						if(iLength > 4)
						{
							var sNewTextBoxVal = sTextBoxVal.slice(0, 4);
							$oTextField.val(sNewTextBoxVal);
						}
					}					
				});
			
				//-----------------------------------------------------------------------
			
				$(dtPickerObj.element).find('.dtpicker-close').click(function()
				{
					dtPickerObj._hidePicker();
				});
			
				$(dtPickerObj.element).find('.dtpicker-buttonSet').click(function()
				{
					dtPickerObj._setButtonAction();
				});
			
				$(dtPickerObj.element).find('.dtpicker-buttonClear').click(function()
				{
					dtPickerObj._clearButtonAction();
				});
			
				// ----------------------------------------------------------------------------
			
				$(dtPickerObj.element).find(".day .increment").click(function()
				{
					 dtPickerObj.dataObject.iCurrentDay++;
					 dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".day .decrement").click(function()
				{
					dtPickerObj.dataObject.iCurrentDay--;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".month .increment").click(function()
				{
					dtPickerObj.dataObject.iCurrentMonth++;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".month .decrement").click(function()
				{
					dtPickerObj.dataObject.iCurrentMonth--;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".year .increment").click(function()
				{
					dtPickerObj.dataObject.iCurrentYear++;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".year .decrement").click(function()
				{
					dtPickerObj.dataObject.iCurrentYear--;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".hour .increment").click(function()
				{
					dtPickerObj.dataObject.iCurrentHour++;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".hour .decrement").click(function()
				{
					dtPickerObj.dataObject.iCurrentHour--;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".minutes .increment").click(function()
				{
					dtPickerObj.dataObject.iCurrentMinutes++;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".minutes .decrement").click(function()
				{
					dtPickerObj.dataObject.iCurrentMinutes--;
					dtPickerObj._setCurrentDate();
				});
			
				$(dtPickerObj.element).find(".meridiem .dtpicker-compButton").click(function()
				{
					if(dtPickerObj._compare(dtPickerObj.dataObject.sCurrentMeridiem, "AM"))
					{
						dtPickerObj.dataObject.sCurrentMeridiem = "PM";
						dtPickerObj.dataObject.iCurrentHour += 12;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sCurrentMeridiem, "PM"))
					{
						dtPickerObj.dataObject.sCurrentMeridiem = "AM";
						dtPickerObj.dataObject.iCurrentHour -= 12;
					}				
					dtPickerObj._setCurrentDate();
				});
			},
		
			_getValueOfElement: function(oElem)
			{
				var dtPickerObj = this;
				var sElemValue = "";
			
				if(dtPickerObj._compare($(oElem).prop("tagName"), "INPUT"))
					sElemValue = $(oElem).val();
				else
					sElemValue = $(oElem).html();
			
				return sElemValue;
			},
		
			_setValueOfElement: function(sElemValue)
			{
				var dtPickerObj = this;
			
				var $oElem = $(dtPickerObj.dataObject.oInputElement);
				if(dtPickerObj._compare($oElem.prop("tagName"), "INPUT"))
					$oElem.val(sElemValue);
				else
					$oElem.html(sElemValue);
					
				$oElem.change();
			
				return sElemValue;
			},
		
			//-----------------------------------------------------------------
		
			_parseDate: function(sDate)
			{
				var dtPickerObj = this;
			
				var dTempDate = new Date(dtPickerObj.settings.defaultDate);
				var iDate = dTempDate.getDate();
				var iMonth = dTempDate.getMonth();
				var iYear = dTempDate.getFullYear();
			
				if(sDate != "" &&  sDate != undefined && sDate != null)
				{
					var sArrDate = sDate.split(dtPickerObj.settings.dateSeparator);
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[0]))  // "dd-MM-yyyy"
					{
						iDate = parseInt(sArrDate[0]);
						iMonth = parseInt(sArrDate[1] - 1);
						iYear = parseInt(sArrDate[2]);
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[1]))  // "MM-dd-yyyy"
					{
						iMonth = parseInt(sArrDate[0] - 1);
						iDate = parseInt(sArrDate[1]);
						iYear = parseInt(sArrDate[2]);
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[2]))  // "yyyy-MM-dd"
					{
						iYear = parseInt(sArrDate[0]);
						iMonth = parseInt(sArrDate[1] - 1);
						iDate = parseInt(sArrDate[2]);
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateFormat, dtPickerObj.dataObject.sArrInputDateFormats[3]))  // "dd-MMM-yyyy"
					{
						iDate = parseInt(sArrDate[0]);
						iMonth = dtPickerObj.getShortMonthIndex(sArrDate[1]);
						iYear = parseInt(sArrDate[2]);
					}
				}
			
				dTempDate = new Date(iYear, iMonth, iDate, 0, 0, 0, 0);
				return dTempDate;
			},
		
			_parseTime: function(sTime)
			{
				var dtPickerObj = this;
			
				var dTempDate = new Date(dtPickerObj.settings.defaultDate);
				var iDate = dTempDate.getDate();
				var iMonth = dTempDate.getMonth();
				var iYear = dTempDate.getFullYear();
				var iHour = dTempDate.getHours();
				var iMinutes = dTempDate.getMinutes();
			
				if(sTime != "" &&  sTime != undefined && sTime != null)
				{
					if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[0]))  //  "hh:mm AA"
					{
						var sArrTime = sTime.split(dtPickerObj.settings.timeMeridiemSeparator);
						var sMeridiem = sArrTime[1];
					
						var sArrTimeComp = sArrTime[0].split(dtPickerObj.settings.timeSeparator);
						iHour = parseInt(sArrTimeComp[0]);
						iMinutes = parseInt(sArrTimeComp[1]);
					
						if(dtPickerObj._compare(sMeridiem, "PM"))
							iHour += 12;
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[1]))  //  "HH:mm"
					{
						var sArrTimeComp = sTime.split(dtPickerObj.settings.timeSeparator);
						iHour = parseInt(sArrTimeComp[0]);
						iMinutes = parseInt(sArrTimeComp[1]);
					}
				}
			
				dTempDate = new Date(iYear, iMonth, iDate, iHour, iMinutes, 0, 0);
			
				return dTempDate;
			},
		
			_parseDateTime: function(sDateTime)
			{
				var dtPickerObj = this;
			
				var dTempDate = new Date(dtPickerObj.settings.defaultDate);
				var iDate = dTempDate.getDate();
				var iMonth = dTempDate.getMonth();
				var iYear = dTempDate.getFullYear();
				var iHour = dTempDate.getHours();
				var iMinutes = dTempDate.getMinutes();
				var sMeridiem = "";
			
				if(sDateTime != "" &&  sDateTime != undefined && sDateTime != null)
				{
					var sArrDateTime = sDateTime.split(dtPickerObj.settings.dateTimeSeparator);
					var sArrDate = sArrDateTime[0].split(dtPickerObj.settings.dateSeparator);
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[0]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[1])) // "dd-MM-yyyy HH:mm:ss", "dd-MM-yyyy hh:mm:ss AA"
					{
						iDate = parseInt(sArrDate[0]);
						iMonth = parseInt(sArrDate[1] - 1);
						iYear = parseInt(sArrDate[2]);
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[2]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[3])) // "MM-dd-yyyy HH:mm:ss", "MM-dd-yyyy hh:mm:ss AA"
					{
						iMonth = parseInt(sArrDate[0] - 1);
						iDate = parseInt(sArrDate[1]);
						iYear = parseInt(sArrDate[2]);
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[4]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[5])) // "yyyy-MM-dd HH:mm:ss", "yyyy-MM-dd hh:mm:ss AA"
					{
						iYear = parseInt(sArrDate[0]);
						iMonth = parseInt(sArrDate[1] - 1);
						iDate = parseInt(sArrDate[2]);
					}
					else if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[6]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[7])) // "dd-MMM-yyyy HH:mm:ss", "dd-MMM-yyyy hh:mm:ss AA"
					{
						iDate = parseInt(sArrDate[0]);
						iMonth = dtPickerObj.getShortMonthIndex(sArrDate[1]);
						iYear = parseInt(sArrDate[2]);
					}
				
					var sTime;
					if(dtPickerObj.dataObject.bIs12Hour)
					{
						if(dtPickerObj._compare(dtPickerObj.settings.dateTimeSeparator, dtPickerObj.settings.timeMeridiemSeparator) && (sArrDateTime.length == 3))
						{
							sMeridiem = sArrDateTime[2];
						}
						else
						{
							var sArrTimeComp = sArrDateTime[1].split(dtPickerObj.settings.timeMeridiemSeparator);
							sTime = sArrTimeComp[0];
							sMeridiem = sArrTimeComp[1];
						}
					
						if(!(!dtPickerObj._compare(sMeridiem, "AM") || dtPickerObj._compare(sMeridiem, "PM")))
							sMeridiem = "";
					}
					else
					{
						sTime = sArrDateTime[1];
					}
				
					var sArrTime = sTime.split(dtPickerObj.settings.timeSeparator);
					iHour = parseInt(sArrTime[0]);
					iMinutes = parseInt(sArrTime[1]);
					if(dtPickerObj._compare(sMeridiem, "PM"))
						iHour += 12;
				}
			
				dTempDate = new Date(iYear, iMonth, iDate, iHour, iMinutes, 0, 0);
			
				return dTempDate;
			},
		
			getShortMonthIndex: function(sMonthName)
			{
				var dtPickerObj = this;
				
				for(var iTempIndex = 0; iTempIndex < dtPickerObj.settings.shortMonthNames.length; iTempIndex++)
				{
					if(dtPickerObj._compare(sMonthName, dtPickerObj.settings.shortMonthNames[iTempIndex]))
						return iTempIndex;
				}
			},
		
			//-----------------------------------------------------------------
		
			_setVariablesForDate: function()
			{
				var dtPickerObj = this;
			
				dtPickerObj.dataObject.iCurrentDay = dtPickerObj.dataObject.dCurrentDate.getDate();
				dtPickerObj.dataObject.iCurrentMonth = dtPickerObj.dataObject.dCurrentDate.getMonth();
				dtPickerObj.dataObject.iCurrentYear = dtPickerObj.dataObject.dCurrentDate.getFullYear();
			
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
				{
					dtPickerObj.dataObject.iCurrentHour = dtPickerObj.dataObject.dCurrentDate.getHours();
					dtPickerObj.dataObject.iCurrentMinutes = dtPickerObj.dataObject.dCurrentDate.getMinutes();
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sTimeFormat, dtPickerObj.dataObject.sArrInputTimeFormats[0]))
					{
						if(dtPickerObj.dataObject.iCurrentHour > 12)
							dtPickerObj.dataObject.sCurrentMeridiem = "PM";
						else if(dtPickerObj.dataObject.iCurrentHour == 12 && dtPickerObj.dataObject.iCurrentMinutes > 0)
							dtPickerObj.dataObject.sCurrentMeridiem = "PM";
						else
							dtPickerObj.dataObject.sCurrentMeridiem = "AM";
					}
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					dtPickerObj.dataObject.iCurrentHour = dtPickerObj.dataObject.dCurrentDate.getHours();
					dtPickerObj.dataObject.iCurrentMinutes = dtPickerObj.dataObject.dCurrentDate.getMinutes();
				
					if(dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[1]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[3]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[5]) || dtPickerObj._compare(dtPickerObj.dataObject.sDateTimeFormat, dtPickerObj.dataObject.sArrInputDateTimeFormats[7]))
					{
						if(dtPickerObj.dataObject.iCurrentHour > 12)
							dtPickerObj.dataObject.sCurrentMeridiem = "PM";
						else if(dtPickerObj.dataObject.iCurrentHour == 12 && dtPickerObj.dataObject.iCurrentMinutes > 0)
							dtPickerObj.dataObject.sCurrentMeridiem = "PM";
						else
							dtPickerObj.dataObject.sCurrentMeridiem = "AM";
					}
				}
			},
		
			_getValuesFromInputBoxes: function()
			{
				var dtPickerObj = this;
			
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "date") || dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					var sMonth = $(dtPickerObj.element).find(".month .dtpicker-compValue").val();
					if(sMonth.length > 1)
						sMonth = sMonth.charAt(0).toUpperCase() + sMonth.slice(1);
					var iMonth = dtPickerObj.settings.shortMonthNames.indexOf(sMonth);
					if(iMonth != -1)
					{
						dtPickerObj.dataObject.iCurrentMonth = parseInt(iMonth);
					}
					else
					{
						if(sMonth.match("^[+|-]?[0-9]+$"))
						{
							dtPickerObj.dataObject.iCurrentMonth = parseInt(sMonth - 1);
						}
					}
				
					dtPickerObj.dataObject.iCurrentDay = parseInt($(dtPickerObj.element).find(".day .dtpicker-compValue").val()) || dtPickerObj.dataObject.iCurrentDay;					
					dtPickerObj.dataObject.iCurrentYear = parseInt($(dtPickerObj.element).find(".year .dtpicker-compValue").val()) || dtPickerObj.dataObject.iCurrentYear;
				}
			
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "time") || dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					dtPickerObj.dataObject.iCurrentHour = parseInt($(dtPickerObj.element).find(".hour .dtpicker-compValue").val());
					dtPickerObj.dataObject.iCurrentMinutes = parseInt($(dtPickerObj.element).find(".minutes .dtpicker-compValue").val());
				
					if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
					{
						if(dtPickerObj.dataObject.bIs12Hour)
						{
							if(dtPickerObj.dataObject.iCurrentHour > 12)
								dtPickerObj.dataObject.iCurrentHour = (dtPickerObj.dataObject.iCurrentHour % 12);
							if(dtPickerObj.dataObject.iCurrentMinutes > 59)
							{
								var iExtraHour = dtPickerObj.dataObject.iCurrentMinutes / 60;
								var iExtraMinutes = dtPickerObj.dataObject.iCurrentMinutes % 59;
							
								var iNewHour = dtPickerObj.dataObject.iCurrentHour + iExtraHour;
								if(iNewHour > 12)
									dtPickerObj.dataObject.iCurrentHour = (iNewHour % 12);
								dtPickerObj.dataObject.iCurrentMinutes = iExtraMinutes;
							}
						}
						else
						{
							if(dtPickerObj.dataObject.iCurrentHour > 23)
								dtPickerObj.dataObject.iCurrentHour = (dtPickerObj.dataObject.iCurrentHour % 23);
						
							if(dtPickerObj.dataObject.iCurrentMinutes > 59)
							{
								var iExtraHour = dtPickerObj.dataObject.iCurrentMinutes / 60;
								var iExtraMinutes = dtPickerObj.dataObject.iCurrentMinutes % 59;
							
								var iNewHour = dtPickerObj.dataObject.iCurrentHour + iExtraHour;
								if(iNewHour > 23)
								dtPickerObj.dataObject.iCurrentHour = (iNewHour % 23);
								dtPickerObj.dataObject.iCurrentMinutes = iExtraMinutes;
							}
						}
					}
				
					if(dtPickerObj.dataObject.bIs12Hour)
					{
						var sMeridiem = $(dtPickerObj.element).find(".meridiem .dtpicker-compValue").val();
						if(dtPickerObj._compare(sMeridiem, "AM") || dtPickerObj._compare(sMeridiem, "PM"))
							dtPickerObj.dataObject.sCurrentMeridiem = sMeridiem;
					
						if(dtPickerObj._compare(dtPickerObj.dataObject.sCurrentMeridiem, "PM") && dtPickerObj.dataObject.iCurrentHour < 13)
							dtPickerObj.dataObject.iCurrentHour += 12;
						if(dtPickerObj._compare(dtPickerObj.dataObject.sCurrentMeridiem, "AM") && dtPickerObj.dataObject.iCurrentHour == 12)
							dtPickerObj.dataObject.iCurrentHour = 0;
					}
				}
			},
		
			_setCurrentDate: function()
			{
				var dtPickerObj = this;
			
				var dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
				var bGTMaxDate = false, bLTMinDate = false;
			
				if(dtPickerObj.dataObject.dMaxValue != null)
					bGTMaxDate = (dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime());
				if(dtPickerObj.dataObject.dMinValue != null)
					bLTMinDate = (dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime());
			
				if(bGTMaxDate || bLTMinDate)
				{
					var bCDGTMaxDate = false, bCDLTMinDate = false; 
					if(dtPickerObj.dataObject.dMaxValue != null)
						bCDGTMaxDate = (dtPickerObj.dataObject.dCurrentDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime());
					if(dtPickerObj.dataObject.dMinValue != null)
						bCDLTMinDate = (dtPickerObj.dataObject.dCurrentDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime());
				
					if(!(bCDGTMaxDate || bCDLTMinDate))
						dTempDate = new Date(dtPickerObj.dataObject.dCurrentDate);
					else
					{
						if(bCDGTMaxDate)
							dTempDate = new Date(dtPickerObj.dataObject.dMaxValue);
						if(bCDLTMinDate)
							dTempDate = new Date(dtPickerObj.dataObject.dMinValue);
					}
				}
			
				dtPickerObj.dataObject.dCurrentDate = new Date(dTempDate);
				dtPickerObj._setVariablesForDate();
			
				if(dtPickerObj._compare(dtPickerObj.settings.mode, "date"))
				{
					var sDay = dtPickerObj.dataObject.iCurrentDay;
					sDay = (sDay < 10) ? ("0" + sDay) : sDay;
					var iMonth = dtPickerObj.dataObject.iCurrentMonth;
					var sMonthShort = dtPickerObj.settings.shortMonthNames[iMonth];
					var sMonthFull = dtPickerObj.settings.fullMonthNames[iMonth];
					var sYear = dtPickerObj.dataObject.iCurrentYear;
					var iDayOfTheWeek = dtPickerObj.dataObject.dCurrentDate.getDay();
					var sDayOfTheWeek = dtPickerObj.settings.shortDayNames[iDayOfTheWeek];
				
					$(dtPickerObj.element).find('.day .dtpicker-compValue').val(sDay);
					$(dtPickerObj.element).find('.month .dtpicker-compValue').val(sMonthShort);
					$(dtPickerObj.element).find('.year .dtpicker-compValue').val(sYear);
				
					var sDate = sDayOfTheWeek + ", " + sMonthFull + " " + sDay + ", " + sYear;
					$(dtPickerObj.element).find('.dtpicker-value').html(sDate);
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
				{
					var sHour = dtPickerObj.dataObject.iCurrentHour;
					if(dtPickerObj.dataObject.bIs12Hour)
					{
						if(sHour > 12)
							sHour -= 12;
					
						$(dtPickerObj.element).find('.meridiem .dtpicker-compValue').val(dtPickerObj.dataObject.sCurrentMeridiem);
					}
					sHour = (sHour < 10) ? ("0" + sHour) : sHour;
					if(dtPickerObj.dataObject.bIs12Hour && sHour == "00")
						sHour = 12;
					var sMinutes = dtPickerObj.dataObject.iCurrentMinutes;
					sMinutes = (sMinutes < 10) ? ("0" + sMinutes) : sMinutes;
				
					$(dtPickerObj.element).find('.hour .dtpicker-compValue').val(sHour);
					$(dtPickerObj.element).find('.minutes .dtpicker-compValue').val(sMinutes);
				
					var sTime = sHour + dtPickerObj.settings.timeSeparator + sMinutes;
					if(dtPickerObj.dataObject.bIs12Hour)
						sTime += dtPickerObj.settings.timeMeridiemSeparator + dtPickerObj.dataObject.sCurrentMeridiem;
					$(dtPickerObj.element).find('.dtpicker-value').html(sTime);
				}
				else if(dtPickerObj._compare(dtPickerObj.settings.mode, "datetime"))
				{
					var sDay = dtPickerObj.dataObject.iCurrentDay;
					sDay = (sDay < 10) ? ("0" + sDay) : sDay;
					var iMonth = dtPickerObj.dataObject.iCurrentMonth;
					var sMonthShort = dtPickerObj.settings.shortMonthNames[iMonth];
					var sMonthFull = dtPickerObj.settings.fullMonthNames[iMonth];
					var sYear = dtPickerObj.dataObject.iCurrentYear;
					var iDayOfTheWeek = dtPickerObj.dataObject.dCurrentDate.getDay();
					var sDayOfTheWeek = dtPickerObj.settings.shortDayNames[iDayOfTheWeek];
				
					$(dtPickerObj.element).find('.day .dtpicker-compValue').val(sDay);
					$(dtPickerObj.element).find('.month .dtpicker-compValue').val(sMonthShort);
					$(dtPickerObj.element).find('.year .dtpicker-compValue').val(sYear);
				
					var sDate = sDayOfTheWeek + ", " + sMonthFull + " " + sDay + ", " + sYear;
				
					//------------------------------------------------------------------
				
					var sHour = dtPickerObj.dataObject.iCurrentHour;
					if(dtPickerObj.dataObject.bIs12Hour)
					{
						if(sHour > 12)
							sHour -= 12;
					
						$(dtPickerObj.element).find('.meridiem .dtpicker-compValue').val(dtPickerObj.dataObject.sCurrentMeridiem);
					}
					sHour = (sHour < 10) ? ("0" + sHour) : sHour;
					if(dtPickerObj.dataObject.bIs12Hour && sHour == "00")
						sHour = 12;
					var sMinutes = dtPickerObj.dataObject.iCurrentMinutes;
					sMinutes = (sMinutes < 10) ? ("0" + sMinutes) : sMinutes;
				
					$(dtPickerObj.element).find('.hour .dtpicker-compValue').val(sHour);
					$(dtPickerObj.element).find('.minutes .dtpicker-compValue').val(sMinutes);
				
					var sTime = sHour + dtPickerObj.settings.timeSeparator + sMinutes;
					if(dtPickerObj.dataObject.bIs12Hour)
						sTime += dtPickerObj.settings.timeMeridiemSeparator + dtPickerObj.dataObject.sCurrentMeridiem;
				
					//------------------------------------------------------------------
				
					var sDateTime = sDate + dtPickerObj.settings.dateTimeSeparator + sTime;
				
					$(dtPickerObj.element).find('.dtpicker-value').html(sDateTime);
				}
			
				dtPickerObj._setButtons();
			},
		
			_setButtons: function()
			{
				var dtPickerObj = this;
				$(dtPickerObj.element).find('.dtpicker-compButton').removeClass("dtpicker-compButtonDisable").addClass('dtpicker-compButtonEnable');
			
				var dTempDate;
				if(dtPickerObj.dataObject.dMaxValue != null)
				{
					if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
					{
						// Decrement Hour
						if((dtPickerObj.dataObject.iCurrentHour + 1) > dtPickerObj.dataObject.dMaxValue.getHours() || ((dtPickerObj.dataObject.iCurrentHour + 1) == dtPickerObj.dataObject.dMaxValue.getHours() && dtPickerObj.dataObject.iCurrentMinutes > dtPickerObj.dataObject.dMaxValue.getMinutes()))
							$(dtPickerObj.element).find(".hour .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Decrement Minutes
						if(dtPickerObj.dataObject.iCurrentHour >= dtPickerObj.dataObject.dMaxValue.getHours() && (dtPickerObj.dataObject.iCurrentMinutes + 1) > dtPickerObj.dataObject.dMaxValue.getMinutes())
							$(dtPickerObj.element).find(".minutes .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					}
					else
					{
						// Increment Day
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, (dtPickerObj.dataObject.iCurrentDay + 1), dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime())
							$(dtPickerObj.element).find(".day .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Increment Month
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, (dtPickerObj.dataObject.iCurrentMonth + 1), dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime())
							$(dtPickerObj.element).find(".month .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Increment Year
						dTempDate = new Date((dtPickerObj.dataObject.iCurrentYear + 1), dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime())
							$(dtPickerObj.element).find(".year .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Increment Hour
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, (dtPickerObj.dataObject.iCurrentHour + 1), dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime())
							$(dtPickerObj.element).find(".hour .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Increment Minutes
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, (dtPickerObj.dataObject.iCurrentMinutes + 1), 0, 0);
						if(dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime())
							$(dtPickerObj.element).find(".minutes .increment").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					}
				}
			
				if(dtPickerObj.dataObject.dMinValue != null)
				{
					if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
					{
						// Decrement Hour
						if((dtPickerObj.dataObject.iCurrentHour - 1) < dtPickerObj.dataObject.dMinValue.getHours() || ((dtPickerObj.dataObject.iCurrentHour - 1) == dtPickerObj.dataObject.dMinValue.getHours() && dtPickerObj.dataObject.iCurrentMinutes < dtPickerObj.dataObject.dMinValue.getMinutes()))
							$(dtPickerObj.element).find(".hour .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Decrement Minutes
						if(dtPickerObj.dataObject.iCurrentHour <= dtPickerObj.dataObject.dMinValue.getHours() && (dtPickerObj.dataObject.iCurrentMinutes - 1) < dtPickerObj.dataObject.dMinValue.getMinutes())
							$(dtPickerObj.element).find(".minutes .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					}
					else
					{
						// Decrement Day 
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, (dtPickerObj.dataObject.iCurrentDay - 1), dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime())
							$(dtPickerObj.element).find(".day .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Decrement Month 
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, (dtPickerObj.dataObject.iCurrentMonth - 1), dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime())
							$(dtPickerObj.element).find(".month .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Decrement Year 
						dTempDate = new Date((dtPickerObj.dataObject.iCurrentYear - 1), dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime())
							$(dtPickerObj.element).find(".year .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Decrement Hour
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, (dtPickerObj.dataObject.iCurrentHour - 1), dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
						if(dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime())
							$(dtPickerObj.element).find(".hour .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					
						// Decrement Minutes
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, dtPickerObj.dataObject.iCurrentHour, (dtPickerObj.dataObject.iCurrentMinutes - 1), 0, 0);
						if(dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime())
							$(dtPickerObj.element).find(".minutes .decrement").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
					}
				}
				
				if(dtPickerObj.dataObject.bIs12Hour)
				{
					if(dtPickerObj.dataObject.dMaxValue != null || dtPickerObj.dataObject.dMinValue != null)
					{
						var iTempHour = dtPickerObj.dataObject.iCurrentHour;
						if(dtPickerObj._compare(dtPickerObj.dataObject.sCurrentMeridiem, "AM"))
							iTempHour += 12;
						else if(dtPickerObj._compare(dtPickerObj.dataObject.sCurrentMeridiem, "PM"))
							iTempHour -= 12;
					
						dTempDate = new Date(dtPickerObj.dataObject.iCurrentYear, dtPickerObj.dataObject.iCurrentMonth, dtPickerObj.dataObject.iCurrentDay, iTempHour, dtPickerObj.dataObject.iCurrentMinutes, 0, 0);
					
						if(dtPickerObj.dataObject.dMaxValue != null)
						{
							if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
							{
								var iTempMinutes = dtPickerObj.dataObject.iCurrentMinutes;
								if(iTempHour > dtPickerObj.dataObject.dMaxValue.getHours() || (iTempHour == dtPickerObj.dataObject.dMaxValue.getHours() && iTempMinutes > dtPickerObj.dataObject.dMaxValue.getMinutes()))
									$(dtPickerObj.element).find(".meridiem .dtpicker-compButton").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
							}
							else
							{
								if(dTempDate.getTime() > dtPickerObj.dataObject.dMaxValue.getTime())
									$(dtPickerObj.element).find(".meridiem .dtpicker-compButton").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
							}
						}
					
						if(dtPickerObj.dataObject.dMinValue != null)
						{
							if(dtPickerObj._compare(dtPickerObj.settings.mode, "time"))
							{
								var iTempMinutes = dtPickerObj.dataObject.iCurrentMinutes;
								if(iTempHour < dtPickerObj.dataObject.dMinValue.getHours() || (iTempHour == dtPickerObj.dataObject.dMinValue.getHours() && iTempMinutes < dtPickerObj.dataObject.dMinValue.getMinutes()))
									$(dtPickerObj.element).find(".meridiem .dtpicker-compButton").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
							}
							else
							{
								if(dTempDate.getTime() < dtPickerObj.dataObject.dMinValue.getTime())
									$(dtPickerObj.element).find(".meridiem .dtpicker-compButton").removeClass("dtpicker-compButtonEnable").addClass("dtpicker-compButtonDisable");
							}
						}
					}
				}
			},
		
			_compare: function(sString1, sString2)
			{
				if(sString1.toLowerCase() == sString2.toLowerCase())
					return true;
				else
					return false;				
			},
		
			setIsPopup: function(isPopup)
			{
				var dtPickerObj = this;
				dtPickerObj.settings.isPopup = isPopup;
			
				if($(dtPickerObj.element).css("display") != "none")
					dtPickerObj._hidePicker(1);
				if(dtPickerObj.settings.isPopup)
				{
					$(dtPickerObj.element).addClass("dtpicker-mobile");
					
					$(dtPickerObj.element).css({position: "fixed", top: 0, left: 0, width: "100%", height: "100%"});
				}
				else
				{
					$(dtPickerObj.element).removeClass("dtpicker-mobile");
					
					if(dtPickerObj.dataObject.oInputElement != null)
					{
						var iElemTop = $(dtPickerObj.dataObject.oInputElement).offset().top + $(dtPickerObj.dataObject.oInputElement).outerHeight();
						var iElemLeft = $(dtPickerObj.dataObject.oInputElement).offset().left;
						var iElemWidth =  $(dtPickerObj.dataObject.oInputElement).outerWidth();
				
						$(dtPickerObj.element).css({position: "absolute", top: iElemTop, left: iElemLeft, width: iElemWidth, height: "auto"});
					}
				}
			},
		
			_compareDates: function(dDate1, dDate2)
			{
				var iDateMatch = 0;
				if(dDate1.getDate() == dDate2.getDate() && dDate1.getMonth() == dDate2.getMonth() && dDate1.getFullYear() == dDate2.getFullYear())
					iDateMatch = 1;  	// 1 = Exact Match
				else
				{
					if(dDate1.getFullYear() < dDate2.getFullYear())
						iDateMatch = 2;	 // date1 < date2
					else if(dDate1.getFullYear() > dDate2.getFullYear())
						iDateMatch = 3; 	// date1 > date2
					else if(dDate1.getFullYear() == dDate2.getFullYear())
					{
						if(dDate1.getMonth() < dDate2.getMonth())
							iDateMatch = 2;	 // date1 < date2
						else if(dDate1.getMonth() > dDate2.getMonth())
							iDateMatch = 3; 	// date1 > date2
						else if(dDate1.getMonth() == dDate2.getMonth())
						{
							if(dDate1.getDate() < dDate2.getDate())
								iDateMatch = 2;	 // date1 < date2
							else if(dDate1.getDate() > dDate2.getDate())
								iDateMatch = 3; 	// date1 > date2
						}
					}
				}
				return iDateMatch;
			},
		
			_compareTime: function(dTime1, dTime2)
			{
				var iTimeMatch = 0;
				if((dTime1.getHours() == dTime2.getHours()) && (dTime1.getMinutes() == dTime2.getMinutes()))
					iTimeMatch = 1;  	// 1 = Exact Match
				else
				{
					if(dTime1.getHours() < dTime2.getHours())
						iTimeMatch = 2;	 // time1 < time2
					else if(dTime1.getHours() > dTime2.getHours())
						iTimeMatch = 3; 	// time1 > time2
					else if(dTime1.getHours() == dTime2.getHours())
					{
						if(dTime1.getMinutes() < dTime2.getMinutes())
							iTimeMatch = 2;	 // time1 < time2
						else if(dTime1.getMinutes() > dTime2.getMinutes())
							iTimeMatch = 3; 	// time1 > time2
					}
				}
				return iTimeMatch;
			},
		
			_compareDateTime: function(dDate1, dDate2)
			{
				var iDateTimeMatch = 0;
				if((dDate1.getDate() == dDate2.getDate() && dDate1.getMonth() == dDate2.getMonth() && dDate1.getFullYear() == dDate2.getFullYear()) && (dDate1.getHours() == dDate2.getHours()) && (dDate1.getMinutes() == dDate2.getMinutes()))
					iDateTimeMatch = 1;  	// 1 = Exact Match
				else
				{
					if(dDate1.getFullYear() < dDate2.getFullYear())
						iDateTimeMatch = 2;	 // date1 < date2
					else if(dDate1.getFullYear() > dDate2.getFullYear())
						iDateTimeMatch = 3; 	// date1 > date2
					else if(dDate1.getFullYear() == dDate2.getFullYear())
					{
						if(dDate1.getMonth() < dDate2.getMonth())
							iDateTimeMatch = 2;	 // date1 < date2
						else if(dDate1.getMonth() > dDate2.getMonth())
							iDateTimeMatch = 3; 	// date1 > date2
						else if(dDate1.getMonth() == dDate2.getMonth())
						{
							if(dDate1.getDate() < dDate2.getDate())
								iDateTimeMatch = 2;	 // date1 < date2
							else if(dDate1.getDate() > dDate2.getDate())
								iDateTimeMatch = 3; 	// date1 > date2
							else if(dDate1.getDate() == dDate2.getDate())
							{
								if(dDate1.getHours() < dDate2.getHours())
									iDateTimeMatch = 2;	 // date1 < date2
								else if(dDate1.getHours() > dDate2.getHours())
									iDateTimeMatch = 3; 	// date1 > date2
								else if(dDate1.getHours() == dDate2.getHours())
								{
									if(dDate1.getMinutes() < dDate2.getMinutes())
										iDateTimeMatch = 2;	 // date1 < date2
									else if(dDate1.getMinutes() > dDate2.getMinutes())
										iDateTimeMatch = 3; 	// date1 > date2
								}
							}
						}
					}
				}
				return iDateTimeMatch;
			}
		
		};
	
})( jQuery, window, document );


;/*! ========================================================================
 * Copyright 2014 Dave Earley
 * ========================================================================
 *
 * dave@earley.email
 * 
 * All backend related JS is contained in this file.
 *
 * ======================================================================== */


window.Attendize = {
    DateFormat: 'dd-MM-yyyy',
    DateTimeFormat: 'dd-MM-yyyy hh:mm:ss',
    GenericErrorMessage: 'Whoops!, An unknown error has occurred.'
    + 'Please try again or contact support if the problem persists. '
};

$(function () {

    /*
     * --------------------------
     * Set up all our required plugins
     * --------------------------
     */

    /* Datepciker */
    $(document).ajaxComplete(function () {
        $('#DatePicker').remove();
        var $div = $("<div>", {id: "DatePicker"});
        $("body").append($div);
        $div.DateTimePicker({
            dateTimeFormat: window.Attendize.DateTimeFormat
        });

    });

    /* Responsive sidebar */
    $(document.body).on('click', '.toggleSidebar', function (e) {
        $('html').toggleClass('sidebar-open-ltr');
        e.preventDefault();
    });

    /* Scroll to top */
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.totop').fadeIn();
        } else {
            $('.totop').fadeOut();
        }
    });

    $(".totop").click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 200);
    });


    /*
     * --------------------
     * Ajaxify those forms
     * --------------------
     * 
     * All forms with the 'ajax' class will automatically handle showing errors etc.
     * 
     */
    $('form.ajax').ajaxForm({
        delegation: true,
        beforeSubmit: function (formData, jqForm, options) {

            $(jqForm[0])
                .find('.error.help-block')
                .remove();
            $(jqForm[0]).find('.has-error')
                .removeClass('has-error');

            var $submitButton = $(jqForm[0]).find('input[type=submit]');
            toggleSubmitDisabled($submitButton);


        },
        uploadProgress: function (event, position, total, percentComplete) {
            $('.uploadProgress').show().html('Uploading Images - ' + percentComplete + '% Complete...    ');
        },
        error: function (data, statusText, xhr, $form) {

            showMessage('Whoops!, it looks like something went wrong on our servers.\n\
                   Please try again, or contact support if the problem persists.');

            var $submitButton = $form.find('input[type=submit]');
            toggleSubmitDisabled($submitButton);

            $('.uploadProgress').hide();
        },
        success: function (data, statusText, xhr, $form) {

            switch (data.status) {
                case 'success':

                    if ($form.hasClass('reset')) {
                        $form.resetForm();
                    }

                    if ($form.hasClass('closeModalAfter')) {
                        $('.modal, .modal-backdrop').fadeOut().remove();
                    }

                    var $submitButton = $form.find('input[type=submit]');
                    toggleSubmitDisabled($submitButton);

                    if (typeof data.message !== 'undefined') {
                        showMessage(data.message);
                    }

                    if (typeof data.runThis !== 'undefined') {
                        eval(data.runThis);
                    }

                    if (typeof data.redirectUrl !== 'undefined') {
                        window.location = data.redirectUrl;
                    }

                    break;

                case 'error':
                    $.each(data.messages, function (index, error) {
                        var $input = $(':input[name=' + index + ']', $form);

                        if ($input.prop('type') === 'file') {
                            $('#input-' + $input.prop('name')).append('<div class="help-block error">' + error + '</div>')
                                .parent()
                                .addClass('has-error');
                        } else {
                            $input.after('<div class="help-block error">' + error + '</div>')
                                .parent()
                                .addClass('has-error');
                        }

                    });

                    var $submitButton = $form.find('input[type=submit]');
                    toggleSubmitDisabled($submitButton);

                    break;

                default:
                    break;
            }

            $('.uploadProgress').hide();
        },
        dataType: 'json'
    });


    /*
     * --------------------
     * Create a simple way to show remote dynamic modals from the frontend
     * --------------------
     * 
     * E.g : 
     * <a href='/route/to/modal' class='loadModal'>
     *  Click For Modal
     * </a>
     * 
     */
    $(document.body).on('click', '.loadModal, [data-invoke~=modal]', function (e) {

        var loadUrl = $(this).data('href'),
            modalId = $(this).data('modal-id'),
            cacheResult = $(this).data('cache') === 'on' ? true : false;

        // $('#' + modalId).remove();
        $('.modal').remove();
        $('html').addClass('working');

        /*
         * Hopefully this message will rarely show
         */
        setTimeout(function () {
            //showMessage('One second...'); #far to annoying
        }, 750);

        $.ajax({
            url: loadUrl,
            data: {'modal_id': modalId},
            localCache: cacheResult,
            dataType: 'html',
            success: function (data) {
                hideMessage();

                //history.pushState({}, '', window.location + '#' + modalId);

                $('body').append(data);

                var $modal = $('#' + modalId);

                $modal.modal({
                    'backdrop': 'static'
                });

                $modal.modal('show');

                $modal.on('hidden.bs.modal', function (e) {
                    // window
                    location.hash = '';
                });

                $('html').removeClass('working');
            }
        }).done().fail(function (data) {
            $('html').removeClass('working');
            showMessage('Whoops!, something has gone wrong.<br><br>' + data.status + ' ' + data.statusText);
        });

        e.preventDefault();
    });

    /*
     * ------------------------------------------------------------
     * A slightly hackish way to close modals on back button press.
     * ------------------------------------------------------------
     */
    $(window).on('hashchange', function (e) {
        $('.modal').modal('hide');
    });


    /*
     * -------------------------------------------------------------
     * Simple way for any type of object to be deleted. 
     * -------------------------------------------------------------
     * 
     * E.g markup:
     * <a data-route='/route/to/delete' data-id='123' data-type='objectType'>
     *  Delete This Object
     * </a>
     * 
     */
    $('.deleteThis').on('click', function (e) {

        /*
         * Confirm if the user wants to delete this object
         */
        if ($(this).data('confirm-delete') !== 'yes') {
            $(this).data('original-text', $(this).html()).html('Click To Confirm?').data('confirm-delete', 'yes');

            var that = $(this);
            setTimeout(function () {
                that.data('confirm-delete', 'no').html(that.data('original-text'));
            }, 2000);

            return;
        }

        var deleteId = $(this).data('id'),
            deleteType = $(this).data('type'),
            route = $(this).data('route');

        $.post(route, deleteType + '_id=' + deleteId)
            .done(function (data) {

                if (typeof data.message !== 'undefined') {
                    showMessage(data.message);
                }

                switch (data.status) {
                    case 'success':
                        $('#' + deleteType + '_' + deleteId).fadeOut();
                        break;
                    case 'error':
                        /* Error */
                        break;

                    default:
                        break;
                }
            }).fail(function (data) {
            showMessage(Attendize.GenericErrorMessages);
        });

        e.preventDefault();
    });


    $(document.body).on('click', '.pauseTicketSales', function (e) {

        var ticketId = $(this).data('id'),
            route = $(this).data('route');

        $.post(route, 'ticket_id=' + ticketId)
            .done(function (data) {

                if (typeof data.message !== 'undefined') {
                    showMessage(data.message);
                }

                switch (data.status) {
                    case 'success':
                        setTimeout(function () {
                            document.location.reload();
                        }, 300);
                        break;
                    case 'error':
                        /* Error */
                        break;

                    default:
                        break;
                }
            }).fail(function (data) {
            showMessage(Attendize.GenericErrorMessages);
        });


        e.preventDefault();
    });

    /**
     * Toggle checkboxes
     */




    $(document.body).on('click', '.check-all', function (e) {
        var toggleClass = $(this).data('check-class');
        $('.' + toggleClass).each(function () {
            this.checked = $(this).checked;
        });
    });


    /*
     * ------------------------------------------------------------
     * Toggle hidden content when a.show-more-content is clicked
     * ------------------------------------------------------------
     */
    $(document.body).on('click', '.show-more-options', function (e) {

        var toggleClass = !$(this).data('toggle-class')
            ? '.more-options'
            : $(this).data('toggle-class');


        if ($(this).hasClass('toggled')) {
            $(this).html($(this)
                .data('original-text'));

        } else {

            if (!$(this).data('original-text')) {
                $(this).data('original-text', $(this).html());
            }
            $(this).html(!$(this).data('show-less-text') ? 'Show Less' : $(this).data('show-less-text'));
        }

        $(this).toggleClass('toggled');

        /*
         * ?
         */
        if ($(this).data('clear-field')) {
            $($(this).data('clear-field')).val('');
        }

        $(toggleClass).slideToggle();
        e.preventDefault();
    });


    /*
     * Sort by trigger
     */
    $('select[name=sort_by_select]').on('change', function () {
        $('input[name=sort_by]').val($(this).val()).closest('form').submit();
    });

    /**
     * Custom file inputs
     */
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        input.trigger('fileselect', [
            numFiles,
            label
        ]);
    });

    $(document.body).on('fileselect', '.btn-file :file', function (event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        if (input.length) {
            input.val(log);
        } else {
            if (log) {
                console.log(log);
            }

        }
    });

});


/**
 *
 * @param elm $submitButton
 * @returns void
 */
function toggleSubmitDisabled($submitButton) {

    if ($submitButton.hasClass('disabled')) {
        $submitButton.attr('disabled', false)
            .removeClass('disabled')
            .val($submitButton.data('original-text'));
        return;
    }

    $submitButton.data('original-text', $submitButton.val())
        .attr('disabled', true)
        .addClass('disabled')
        .val('Working...');
}

/**
 * Shows users a message.
 * Currently uses humane.js
 *
 * @param string message
 * @returns void
 */
function showMessage(message) {
    humane.log(message, {
        timeout: 3500
    });
}

function showHelp(message) {
    humane.log(message, {
        timeout: 12000
    });
}

function hideMessage() {
    humane.remove();
}

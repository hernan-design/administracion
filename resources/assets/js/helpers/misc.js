 /**
  * Utilities for seg component
  *
  */
 (function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', window, window.document], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        if(window) {
            module.exports = exports.default = factory(window.jQuery || require('jquery'), window, window.document);
        }else {
            module.exports = exports.default = factory(require('jquery'), global, global.document);
        }

    } else {
        // Browser globals
        window.Misc = factory(window.jQuery, window, window.document);
    }
}(function( $, window, document, undefined ){
    var Misc = {
            /**
             *  Serialize form into json format
             *
             *  @param { string } name class or id of the html element to embed the loader
             *  @return { object } form into json
             *
             */
            formToJson: function( selector ){
                var o = {}, a = [];
                if( $.prototype.isPrototypeOf(selector) ){
                    a = selector.serializeArray();
                }
                else{
                    a = $(selector).serializeArray();
                }

                $.each( a, function() {
                    if ( o[ this.name ] !== undefined ) {
                        if ( ! o[this.name].push ) {
                            o[ this.name ] = [ o[ this.name ] ];
                        }

                        o[ this.name ].push( this.value || '' );

                    } else {
                        o[ this.name ] = this.value || '';
                    }
                });

                return o;

            }

           /**
             * Helps in the process of making a ajax requests
             *
             * @param { object } Options for configuring the ajax request
             * @param { object } data object to be sent
             */
        ,   ajaxHandler: function( options, data ) {

                 var result
                 ,   defaults = {
                         type: 'post'
                     ,   url: 'index.php'
                     ,   data: data
                     ,   async: false
                     ,   success: function( data ) {
                                 result = data;
                         }

                     ,   error: function ( XMLHttpRequest, textStatus, errorThrown ) {
                                 console.log( "error :" + XMLHttpRequest.responseText );
                         }
                     }

                 // Merge defaults and options
                 options = $.extend( {}, defaults, options );

                 // Do the ajax request
                 $.ajax( options );

                 // Return the response object
                 return result;

            }

            /**
            * Given an array of required fields, this function
            * checks whether the second argument have them
            */
        ,   validateEmptyFields: function( required, objectData, errors ) {


                $.each( required, function( key, value ) {

                    if ( objectData[ value ] == null || objectData[ value ] == "" ) {

                        errors.push( value );

                    }

                });

                return errors;

            }

            /**
            * Given an array of required fields, this function
            * checks whether the second argument have them
            */
        ,   validateEmptyObjectAttrs: function( _object, errors ) {

                $.each( _object, function( key, value ) {

                    if ( value == null || value == "" ) {

                        errors.push( key );

                    }

                });

                return errors;

            }

            /**
            *
            * Validate only numbers
            * @param { string } the string to validate
            *
            */
        ,   justNumbers: function( value ){

                var pattern = /^\d+$/
                ,   exp = new RegExp( pattern );


                if( typeof value == 'undefined' )
                    return false;


                return exp.test( value );

            }

            /**
            *
            * Validate only letters
            * @param { string } the string to validate
            *
            */
        ,   justLetters: function( value ){

                var pattern = /^[ñA-Za-z _]*[ñA-Za-z][ñA-Za-z _]*$/
                ,   exp = new RegExp( pattern );


                if( typeof value == 'undefined' )
                    return false;


                return exp.test( value );

            }

            /**
            * Converts latin string to anglo
            *
            * @param { string } the string to be sanitized
            * @param { bool } numbers are allowed or not
            * @param { bool } special characters are allowed or not
            * @param { bool } blank spaces are allowed or not
            * @param { string } blank spaces are allowed or not
            * @param { string } blank spaces are replaced to
            * @return { string } the string sanitized
            *
            */
        ,   latinToAnglo: function( value, allowNumbers, allowSpecial, allowSpaces, replace ){

                if( typeof replace == 'undefined' || replace == null )
                    replace = '';

                value = value.replace(/[ÀÁÂÃÄÅ]/,"A");
                value = value.replace(/[àáâãäå]/,"a");
                value = value.replace(/[ÈÉÊË]/,"E");
                value = value.replace(/[èéêë]/,"e");
                value = value.replace(/[íìîï]/,"i");
                value = value.replace(/[ÍÌÎÏ]/,"I");
                value = value.replace(/[óòôö]/,"o");
                value = value.replace(/[ÒÓÔÖ]/,"O");
                value = value.replace(/[úùûü]/,"u");
                value = value.replace(/[ÚÙÛÜ]/,"U");
                value = value.replace(/[çÇ]/,"c");
                value = value.replace(/[ñ]/,"n");
                value = value.replace(/[Ñ]/,"N");

                if( ! allowNumbers )
                    value = value.replace(/[1234567890]/g, '');

                if( ! allowSpecial )
                    value = value.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '' );

                if( ! allowSpaces )
                    value = value.replace( /[  ]/g, replace);

                return value;
            }

            /**
            * Check whether an string is a correct email
            * @param { str } String to test
            * @return { bool }
            */
        ,   isEmail: function( string ) {

                var emailExpression = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                return emailExpression.test( string );
            }

            /**
            * Sets a countdown
            *
            * @param { object } arguments to set: count, limit, selector, callback
            * @return { function } the callback passed, return otherwise
            *
            */
        ,   setCountdown: function( args ){

                var counter=setInterval( reverse, 1000); //1000 will  run it every 1 second

                function reverse(){

                    args.count = args.count - 1;

                    if ( args.count <= args.limit    ) {

                        clearInterval( counter);

                        if ( args.callback !== null ) {

                            args.callback.call();
                        }

                        return;
                    }

                    $( args.selector ).text( args.count );
                }
            }

            /**
            * Sets a countdown a display the text in a HTML Object, triggers a function callback when reaches the limit
            *
            * @param { object } args with data {

                        duration: ( int ) duration in milliseconds
                    ,   interval: ( int ) interval of the count down in milliseconds
                    ,   limit: ( int ) limit until callback executes
                    ,   selector: ( string ) jQuery selector string
                    ,   callback: ( function ) function callback
                }
            *
            * @return { function } Callback function
            *
            */
        ,   humanCountDown: function( args ){

                var counter = setInterval( humanReverse, 1000)
                ,   _this = this;

                function humanReverse(){

                    args.duration = args.duration - args.interval;

                    var mom = moment( args.duration ).format( 'mm:ss' );

                    if ( args.duration < args.limit ) {

                        clearInterval( counter);

                        if ( args.callback !== null ) {

                            args.callback.call();
                        }

                        return;
                    }

                    $( args.selector ).text( mom );
                }

            }

        ,   showNotification: function( type, message, time ){

                var div = $( '<div>' ),
                    span = $( '<span>' ),
                    elmIcon = $( '<i>' );

                span.html( message );

                div.addClass('global-notification');
                div.addClass( type ); //error, success, warning, info
                div.css('display', 'none');
                div.append(elmIcon);
                div.append(span);

                $( 'body' ).append( div );
                $( '.global-notification' ).fadeIn();

                // $( '.global-notification' ).removeClass( 'error, success, warning, info' );
                // $( '.global-notification' ).addClass( type );
                // $( '.global-notification' ).text( message );
                // $( '.global-notification' ).fadeIn();

                this.closeOnClickOut( div[0], true );

                if( time != 0 ){
                    setTimeout( function(){
                        $( '.global-notification' ).fadeOut( 'normal', function(){ $(this).remove(); } );
                    }, time);

                    return;
                }
            }

            /**
            * validates if object is empty
            *
            */
        ,   isEmptyObject: function( obj ){

                // Speed up calls to hasOwnProperty
                var hasOwnProperty = Object.prototype.hasOwnProperty;

                // null and undefined are "empty"
                if (obj == null) return true;

                // Assume if it has a length property with a non-zero value
                // that that property is correct.
                if (obj.length && obj.length > 0)    return false;
                if (obj.length === 0)  return true;

                // Otherwise, does it have any properties of its own?
                // Note that this doesn't handle
                // toString and toValue enumeration bugs in IE < 9
                for (var key in obj) {
                    if (hasOwnProperty.call(obj, key)) return false;
                }

                return true;
            }

            /**
            * Hides an element when it is clicked outiside
            * @param { string } string for the jQuery selector like: ".my-class"
            * @return { null }
            */
        ,   closeOnClickOut: function( selector, remove, callback ) {

                if ( typeof remove == 'undefined' );
                        remove = false;

                $( document ).mouseup( function( e ) {

                    if ( ! $( selector ).is( ":visible" ) ) {
                        return;
                    }

                    if ( $( selector ).has( e.target ).length === 0 ) {

                        if( remove ){
                            $( selector ).fadeOut('normal', function() {
                                $( this ).remove();
                            });
                        }else{
                            $( selector ).fadeOut();
                        }
                        if ( callback != null ) {
                            callback.call();
                        }
                    }
                });

            }

            /**
            * Sets dots in numbers
            *
            * @param { numeric } the number to be changed
            * @return { string } the number with dots
            *
            */
        ,   numberDots: function( num ){

                var number = new String( num );

                var result = '';

                while( number.length > 3 ){

                 result = '.' + number.substr(number.length - 3) + result;

                 number = number.substring(0, number.length - 3);

                }

                result = number + result;

                return result;

            }

            /**
            * Clear all window intervals started previously
            *
            */
        ,   clearAllIntervals: function(){

                var interval_id = window.setInterval("", 9999);
                // Get a reference to the last interval +1
                for (var i = 1; i < interval_id; i++)
                    window.clearInterval( i );
            }

            /**
            * Show modal window and render a template, runs a callback function too
            *
            * @param { object } configuration object {

                  @param { string } modal header title
                , @param { string } html content
                , @param { numeric } width
                , @param { numeric } height
                , @param { function } a callback function to be called after show
            }
            *
            */
        ,   showModalWindow: function( config ){
                // console.log( config );

                if( !({}).toString.call(config).slice(8,-1) === 'Object' )
                    return;

                var modalHtml = '<div class="modal-over-screen"></div><div class="box-modal-single">';
                modalHtml += '<a href="../images" class="close-modal-button"></a>';
                if( typeof config.title != undefined){
                    modalHtml += '<h4 class="title-modal-single">'+config.title+'</h4>';
                }
                modalHtml += '<div class="content-modal-single">'+config.content+'</div>';
                modalHtml += '</div>';

                $( 'body ').append( modalHtml );

                //estilos a la ventana modal
                $( '.box-modal-pqr' ).css({
                    'display': 'none',
                    'position': 'fixed',
                    'z-index': '99999',
                    'left': '50%',
                    'top': '50%',
                    'width': config.width - 28,
                    'height': config.height - 28,
                    'margin-left': '-' + (config.width / 2) + 'px',
                    'margin-top': '-'+ ( config.height / 2) + 'px'
                });

                //estilos a la pantalla negra de fondo
                $( '.modal-over-screen' ).css( {
                        'display': 'none',
                        'position': 'fixed',
                        'top': '0',
                        'left':'0',
                        'width': '100%',
                        'height': '100%',
                        'z-index': '9999',
                        'background-color': 'rgba(0, 0, 0, 0.5)',
                        'color': '#FFF'
                    } );

                //estilos a el contenido que se le pasa
                $( '.content-modal-pqr' ).css({
                        'width': '100%',
                        'height': '100%'
                });

                $( '.titulo-modal-pqr' ).css({
                        'text-align': 'left',
                        'margin': '0px',
                        'padding': '0px',
                        'font-family': 'Open Sans',
                        'font-style': 'italic',
                        'font-weight': 'bold',
                        'font-size': '16px',
                        'color': '#E02229'
                });

                $( '.modal-over-screen' ).fadeIn();
                $( '.box-modal-pqr' ).fadeIn();

                this.closeModalWindow();

                // runs a callback after show
                if( typeof callback != 'undefined' ){
                    callback.call();
                }
            }

            /**
            * Close modal window
            *
            */
        ,   closeModalWindow: function(){

                var callback_remove = function(){ $(this).remove(); }

                $( document ).ready( function(){

                    $( 'body' ).delegate( '.close-modal-button', 'click', function( e ){

                        e.preventDefault();

                        $( '.content-modal-pqr' ).html( '' );
                        $( '.modal-over-screen' ).fadeOut( 'normal' , callback_remove);
                        $( '.box-modal-pqr' ).fadeOut( 'normal' , callback_remove);

                    });

                    $( 'body' ).delegate( '.modal-over-screen', 'click', function(e){

                        $( '.content-modal-pqr' ).html( '' );
                        $( '.modal-over-screen' ).fadeOut( 'normal' , callback_remove);
                        $( '.box-modal-pqr' ).fadeOut( 'normal' , callback_remove);
                    });
                });


                // Esc key press
                $( document ).keyup( function( e ){

                    var keyCode = e.which || e.keyCode;

                    if( keyCode == 27 ){
                        $( '.content-modal-pqr' ).html( '' );
                        $( '.modal-over-screen' ).fadeOut( 'normal' , callback_remove );
                        $( '.box-modal-pqr' ).fadeOut( 'normal' , callback_remove);
                    }
                });
            }

            /**
            * Close modal window
            *
            */
        ,   _closeModalWindow: function(){

                $( '.box-modal-pqr .body' ).html( '' );
                $( '.modal-over-screen' ).fadeOut();
                $( '.box-modal-pqr' ).fadeOut();
            }

            /**
            *
            *
            */
        ,   htmlEntities: function( str ) {
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            /**
            * Parse a json string to JS format
            *
            */
        ,   jsonToObject: function( text ){


                text = text.replace(/'/g, '"');

                var object = JSON.parse( text, function (key, value) {
                        var type;
                    if (value && typeof value === 'object') {
                        type = value.type;
                        if (typeof type === 'string' && typeof window[type] === 'function') {
                            return new (window[type])(value);
                        }
                    }
                    return value;
                });

                return object;
            }

            /**
            * Gets any elements from array in random and withot repeat
            *
            * @param { array } array of elements
            * @param { numeric } number of items to be get
            *
            *
            */
        ,   randomFrom: function ( array, n ) {
                var at = 0;
                var tmp, current, top = array.length;

                if(top) while(--top && at++ < n) {
                    current = Math.floor(Math.random() * (top - 1));
                    tmp = array[current];
                    array[current] = array[top];
                    array[top] = tmp;
                }

                return array.slice(-n);
            }

            /**
            * Customize some functions from Date
            *
            */
        ,   customizeDate: function(){

                Date.prototype.getHoursTwoDigits = function(){
                    var retval = this.getHours();
                    if (retval < 10){
                        return ("0" + retval.toString());
                    }else {
                        return retval.toString();
                    }
                }

                Date.prototype.getMinutesTwoDigits = function(){
                    var retval = this.getMinutes();
                    if (retval < 10){
                        return ("0" + retval.toString());
                    }else {
                        return retval.toString();
                    }
                }
            }

            /**
            * Redirect to an specific url or refresh the page
            * @param { string } the url to be redirect to
            *
            */
        ,   redirect: function( url ){
                if( url != undefined && url != '' && this.isUrl(url) ){
                    window.location = url;
                }else{
                    window.location.reload();
                }
            }

        /**
            * random array
            *
            * @param { Array } the number to be changed
            * @return { Array } the number with dots
            *
            */
        ,   shuffle: function(array){
              var j, temp;
              for(var i = array.length - 1; i > 0; i--){
                j = Math.floor(Math.random() * (i + 1));
                temp = array[i];
                array[i] = array[j];
                array[j] = temp;
              }
              return array;
            }

        /**
        * metodo rtrim como el del lenguaje php
        */
        , rtrim: function( str, filter ){

            if( str === null || str === undefined)
                return str;

            filter || ( filter = '\\s|\\&nbsp;' );

            var pattern = new RegExp('(' + filter + ')*$', 'g');
            return str.toString().replace(pattern, "");
        }

        /**
        * metodo ltrim como el del lenguaje php
        */
        , ltrim: function( str, filter ){

            if( str === null || str === undefined)
                return str;

            filter || ( filter = '\\s|\\&nbsp;' );

            var pattern = new RegExp('^(' + filter + ')*', 'g');
            return str.toString().replace(pattern, "");
        }

        /**
        * metodo trim como el del lenguaje php
        */
        , trim: function( str, filter ){

            filter || ( filter = '\\s|\\&nbsp;' );
            return this.ltrim( this.rtrim(str, filter), filter );
        }

        /**
        * validate the urls
        */
        , isUrl : function( str ){

            // var patt = /^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+\.[a-zA-Z]{2,3}(\/([^\n\r\s])*)?(\?([^\n\r\s])*)?/i;
            var patt = /^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+(\/(.)*)?(\?(.)*)?/i;

            return patt.test( str );
        }

        /**
        * extrac the urls
        */
        , pullUrl : function( str ){

            var patt = /((http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+\.[a-zA-Z]{2,3}(\/([^\n\r\s])*)?(\?([^\n\r\s])*)?)/gi;

            var _return = patt.exec( str );

            if( ({}).toString.call(_return).slice(8,-1) === 'Array' )
                return _return[0];

            return _return;
        }

        /**
        * wrap url to a link element
        */
        , urlWrapLink : function( str ){

            var patt = /((http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+\.[a-zA-Z]{2,3}(\/([^\n\r\s])*)?(\?([^\n\r\s])*)?)/gi;

            return str.replace( patt, '<a href="$&" target="_blank">$&</a>' );
        }

        /**
        *  Sets a loading spinner in a box
        * @param { selector } String|Object Selector jQuery
        * @param { text } String Message of alert
        */
        ,   setSpinner: function( selector, text ){
                text || (text = '');
                if ( !selector ) return;

                var $spinner = $(selector).find( '.wrapper-spinner' ).length ? $(selector).find( '.wrapper-spinner' ) : $('<div class="wrapper-spinner">');

                $spinner.html('<div class="inner-spinner"></div>');
                if( text.length ){
                    $spinner.find( '> .inner-spinner' ).prepend( '<p>'+ text +'</p>' );
                }

                // attach the spinner to the selector
                $spinner.appendTo( selector );

                $spinner.each(function(index, elm) {

                    var $parent = $(elm).parent();
                    var parentWidth = $parent.outerWidth(true);
                    var parentHeight = $parent.outerHeight(true);

                    $(elm).css({
                            'display': 'block',
                            'position': 'absolute',
                            'top': '0px',
                            'left': '0px',
                            'width': parentWidth,
                            'min-height': '30px',
                            'height': '100%',
                            'z-index': '1006'
                          }).find('.inner-spinner')
                              .css({
                                'display': 'block',
                                'position': 'absolute',
                                'top': '50%',
                                'left': '50%',
                                'margin-left': function (dim) {
                                    return '-'+ ( $(this).outerWidth(true) / 2 ) +'px';
                                },
                                'margin-top': function (dim) {
                                    return '-'+ ( $(this).outerHeight(true) / 2 ) +'px';
                                }
                              })
                            .end().parent()
                                  .css({
                                        'position': function (indx, pos) {

                                            var posUp = pos;
                                            if( posUp == 'static' ) posUp = 'relative';
                                            return posUp;
                                        },
                                        'min-height': function (dim) {
                                            return $(elm).css('min-height');
                                        }
                                  });
                });
            }

        /**
        * Removes the loading spinner and trigger a callback
        * @param { time } Integer timeout
        * @param { callback } Function
        * @param { wrap } String|Object wrapper jQuery element
        * @return { type } return description
        *
        */
        ,   removeSpinner: function( options ){

                options || (options = {});

                var defaults = {
                    'time': 0,
                    'callback': null,
                    'wrap': 'body',
                    'direct': false
                }, settings = {};

                settings = $.extend({}, defaults, options);

                var selector = (settings.direct && settings.wrap) ? '> .wrapper-spinner' : '.wrapper-spinner',
                    $selector = null;

                //Remove spinner of parent element if wrap is passed
                if( $.prototype.isPrototypeOf(settings.wrap) ){
                    $selector = settings.wrap.find( selector );

                }else if( settings.wrap ){
                    $selector = $( settings.wrap ).find( selector );
                }else{
                    $selector = $( selector );
                }

                setTimeout( function(){

                    $selector.remove();
                    if( ({}).toString.call(settings.callback).slice(8,-1) === 'Function' )
                        settings.callback.call();

                }, settings.time );

                return;
            }

        /**
        * convierte por el momento links de youtube a url embed
        * @param { String } url video
        * @return { String } url video embed
        */
        ,   convertVideoEmbed: function( urlVideo, type ) {
                urlVideo = this.trim(urlVideo);
                type || (type = 'default');
                var isVideo = false;

                if( !this.isUrl(urlVideo) ){
                    return '';
                }

                var patt = null;
                var convertVideo = {
                    'youtube' : function(type) {
                        patt = /watch\?v\=/gi;

                        switch( type.toLowerCase() ) {
                            case 'videoid':
                                patt = /^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}youtube\.com\/(watch\?v=|v\/|embed\/)/i;
                            break;

                            case 'watch':
                                patt = /(v\/|u\/\w\/|embed\/|\&v=)/gi;
                            break;

                            case 'embed':
                                patt || (patt = patt);
                            break;

                            default: patt = ''; break;
                        }

                        var typeEmbed = {
                            'embed': 'embed/',
                            'swfobject': 'v/',
                            'videoid': '',
                            'default': 'embed/',
                            'watch': 'watch?v='
                        };

                        urlVideo = urlVideo.replace( patt, typeEmbed[type] );
                    },
                    'youtu.be' : function (type) {
                        patt = /^http[s]?:\/\/youtu\.be\/([\w]+)/gi;

                        switch( type.toLowerCase() ) {
                            case 'videoid':
                                patt = /^(http[s]?:\/\/youtu\.be\/)/gi;
                            break;

                            case 'embed':
                                patt || (patt = patt);
                            break;

                            default: patt = ''; break;
                        }

                        var typeEmbed = {
                            'embed': 'https://www.youtube.com/embed/$1',
                            'swfobject': 'https://www.youtube.com/v/$1',
                            'videoid': '',
                            'default': 'https://www.youtube.com/embed/$1',
                            'watch': 'https://www.youtube.com/watch?v=$1'
                        };

                        urlVideo = urlVideo.replace( patt, typeEmbed[type] );
                    },
                    'vimeo' : function(type) {},
                    'default': function (type) { /*urlVideo = false;*/ }
                };

                $.each(convertVideo, function(index, value) {

                    if( urlVideo.search(index) !== -1 ){
                        convertVideo[index](type);
                        isVideo = true;
                    }
                });

                if( !isVideo ){
                    convertVideo['default'](type);
                }

                return urlVideo;
            }
        /**
        * formatear fecha a lenguaje humano
        * @param { Object | String } date
        * @return { String } date formate
        *
        */
        ,   formatDateSpanish: function( fecha, mark ){

                // var reg = /^\d{4}\-\d{2}\-\d{2}\s\d{2}\:\d{2}:\d{2}$/;
                //  reg.test( fecha );

                if( typeof mark == 'undefined' )
                    mark = false;

                var dayNames = [
                    "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"
                ],
                monthNames = [
                    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                ]

                if( ! fecha instanceof Date )
                    return false;

                var curr_date = fecha.getDate();
                var curr_month = fecha.getMonth();
                var curr_year = fecha.getFullYear();

                var strReturn = monthNames[ curr_month ]+' '+curr_date+' de '+curr_year;

                if( mark ){

                    var date_day = curr_date,
                        date_month = curr_month+1,
                        date_year = curr_year;

                    if( date_day.length == 1 )
                        date_day = '0'+date_day;

                    if( date_month.length == 1 )
                        date_month = '0'+date_month;

                    strReturn = date_year+'-'+date_month+'-'+date_day;
                }

                return strReturn;
            }

        /**
        * Build URI with route and base url
        */
        ,   urlFull: function ( route ){

            if( !this.isUrl(document.url) || this.isUrl(route)  )
                return route;

            route || (route = '');
            var patt = /^\//;
            patt.test(route) || (route = '/'+route);

            return document.url + route;
        }

         /**
        * Build URI with route and base url
        */
        ,   urlPlatform: function ( route ){

            if( !this.isUrl(document.url) || this.isUrl(route)  )
                return route;

            route || (route = '');
            var patt = /^\//;
            patt.test(route) || (route = '/'+route);

            return document.platform + route;
        }

        /**
        * Show Alert box
        * @param Object options
        */
        ,   showAlertBox: function ( options ) {

                options || (options = {});

                var defaults = {
                    'title': false,
                    'text': '',
                    'wrapper': 'body',
                    'type': '',
                    'close': true,
                    'closeTime': 0,
                    'speedOpen': 'slow',
                    'speedClose': 'slow'
                }, settings = {};

                settings = $.extend({}, defaults, options);

                new PNotify({
                    title: settings.title,
                    text: settings.text,
                    type: settings.type,
                    hide: settings.close,
                    styling: 'bootstrap3',
                    onShow: function (e, notice) {
                        setTimeout(notice.remove, settings.closeTime);
                    }
                });
        }

        /**
        * Anchor with animation
        * @param Object options
        */
        ,   anchorJumpTo: function ( options ) {

                options || (options = {});

                var defaults = {
                    'parentDelegate': 'body',
                    'anchorLink': '',
                    'duration': 500,
                    'easing': 'swing',
                    'complete': null,
                    'gutter': 0
                }, settings = {};

                settings = $.extend({}, defaults, options);
                var $parentDeleg = '';

                if( $.prototype.isPrototypeOf(settings.parentDelegate) ){
                    $parentDeleg = settings.parentDelegate;
                }else{
                    $parentDeleg = $( settings.parentDelegate );
                }

                $parentDeleg.on('click', settings.anchorLink, function(event) {
                    event.preventDefault();
                    // event.stopPropagation();
                    $target = $( $(this).attr("href") );

                    if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {
                        window.scrollTo(0, $target.offset().top + settings.gutter) // first value for left offset, second value for top offset
                    }else{

                        $("html, body").animate({
                            scrollTop: ( $target.offset().top + settings.gutter ) + "px"
                        }, {
                            duration: settings.duration,
                            easing: settings.easing,
                            complete: settings.complete
                        });
                    }
                });
        }

        /**
        * Parse String True or false to Boolean value
        * @param String str
        */
        ,   strToBool: function (str) {

            return (/^(true|1)$/i).test( str.toString() );
        }

        /**
        * Removes html tags
        * @param String str
        */
        ,   stripTags: function (str, tags) {

            tags || (tags = 'a-z0-9');
            if( str === null || str === undefined)
                return str;

            // var pattern = /(<\?[a-z]*(\s[^>]*)?\?(>|$)|<!\[[a-z]*\[|\]\]>|<!DOCTYPE[^>]*?(>|$)|<\!\-\-[\s\S]*?(\-\->|$)|<[a-z\?\!\/]([a-z0-9\_\:\.])*(\s[^>]*)?(>|$))/gi;

            var pattern = new RegExp('(<\\?['+tags+']*(\\s[^>]*)?\\?(>|$)|<!\\[['+tags+']*\\[|\\]\\]>|<!DOCTYPE[^>]*?(>|$)|<\\!\\-\\-[\\s\\S]*?(\\-\\->|$)|<['+tags+'\\?\\!\\/](['+tags+'\\_\\:\\.])*(\\s[^>]*)?(>|$))', 'gi');

            return str.replace(pattern, "");
        }

        /**
        * Removes html tags
        * @param String str
        */
        ,   strToNickname: function (str, prefix) {
            prefix || (prefix = '@');

            return prefix + str;
        }

        /**
        * link show more
        * @param Object options
        */
        ,   showMore: function ( options ) {

            options || (options = {});

            var defaults = {
                'parentDelegate': document,
                'container': '.container-more',
                'maxlen': 150,
                'toggle': false

            }, settings = {};

            settings = $.extend({}, defaults, options);
            var $container = '',
                $parentDeleg = '',
                $wrapperMore = '',
                fullContent = '',
                trunk = '';

            if( $.prototype.isPrototypeOf(settings.container) ){
                $container = settings.container;
            }else{
                $container = $( settings.container );
            }

            if( $container.find( '.wrapper-more' ).length ) return;

            $container.wrapInner('<div class="wrapper-more"></div>');
            $wrapperMore = $container.find( '.wrapper-more' );

            var $moreLink = $('<a href="#" class="link-more">').text('Ver más');
            var $lessLink = $('<a href="#" class="link-less">').text('Ver menos');

            $moreLink.appendTo( $container );
            $lessLink.appendTo( $container );

            fullContent  = $wrapperMore.html();

            if( $wrapperMore.text().length > settings.maxlen ){

                trunk = $wrapperMore.text().slice(0, settings.maxlen) + "...";
                $wrapperMore.text( trunk );
            }

            $moreLink.click(function (event) {
                event.stopPropagation();
                event.preventDefault();
                $wrapperMore.html( fullContent );
                $moreLink.hide();
                $lessLink.show();
            });

            $lessLink.click(function (event) {
                event.stopPropagation();
                event.preventDefault();
                $wrapperMore.text( trunk );
                $moreLink.show();
                $lessLink.hide();
            });

            if ( $wrapperMore.text().length < settings.maxlen ) {

                $moreLink.hide();
                $lessLink.hide();
                return;
            }

            $moreLink.show();
            $lessLink.hide();
        }
        /**
        * link show more
        * @param Object options
        */
        ,   getCsrf: function () {

            var csrf = {};

            return csrf = {
                getCookie: function (name) {
                    var cookieValue = null;
                    if (document.cookie && document.cookie != '') {
                        var cookies = document.cookie.split(';');
                        for (var i = 0; i < cookies.length; i++) {
                            var cookie = jQuery.trim(cookies[i]);
                            // Does this cookie string begin with the name we want?
                            if (cookie.substring(0, name.length + 1) == (name + '=')) {
                                cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                                break;
                            }
                        }
                    }
                    return cookieValue;
                },
                sameOrigin: function (url) {
                    // url could be relative or scheme relative or absolute
                    var host = document.location.host; // host + port
                    var protocol = document.location.protocol;
                    var sr_origin = '//' + host;
                    var origin = protocol + sr_origin;
                    // Allow absolute or scheme relative URLs to same origin
                    return (url == origin || url.slice(0, origin.length + 1) == origin + '/') ||
                        (url == sr_origin || url.slice(0, sr_origin.length + 1) == sr_origin + '/') ||
                        // or any other URL that isn't scheme relative or absolute i.e relative.
                        !(/^(\/\/|http:|https:).*/.test(url));
                },
                safeMethod: function(method) {
                    return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
                }
            };
        }

        /**
        * Check session app
        * @param { redirect } the boolean to action redirect
        */
        ,   checkSession: function (redirect) {
            redirect = redirect === undefined ? true : redirect;
            var login_tp = $('meta[name="login-tp"]').attr('content');

            if(parseInt(login_tp) === 1){
                return true;
            }

            if(redirect === true){
                this.redirect();
            }
            return false;
        }

        /**
        * get platform stata
        * @param String state
        */
        ,   getState: function( state ) {

            switch (state) {
                case 'progress': return 1;
                case 'review': return 2;
                case 'cancelled': return 3;
                case 'live': return 4;
                case 'correction': return 5;
                case 'confirm': return 6;
                case 'confirmed': return 7;
                case 'active': return 8;
                case 'inactive': return 9;
                case 'feedback': return 10;
                case 'complete': return 11;
                case 'qualified': return 12;
                case 'removed': return 13;
                case 'pending': return 14;
                case 'evaluated': return 15;
                case 'closed': return 16;
                default: return 'Not state identified';
            }
        }
        /**
        * get platform stata
        * @param String orientation
        */
        ,   getOrientation: function( orientation ) {

            switch (orientation) {
                case 'internal': return 1;
                case 'external': return 2;
                default: return 'Not orientation identified';
            }
        }

        /**
        * get param (Default values)
        */
        ,   dv: function(  ) {

            return {
                'avatar': 'http://static.tuproyecto.com/images/svg/no-user-image.svg'
            }
        }

        /**
        * reload social buttons share
        */
        ,   renderSocialBtns: function () {
            // Render twitter button share
            if( window.twttr && window.twttr.widgets ){
                window.twttr.widgets.load();
            }
            // Render facebook button share
            if( window.FB && window.FB.XFBML ){
                window.FB.XFBML.parse();
            }
            // Render linkedin button share
            if( window.IN && typeof(window.IN.parse) == 'function' ){
                window.IN.parse();
            }
        }

        /**
        * set max length in a string
        * @param String str
        * @param Number maxlength
        */
        ,   maxLen: function (str, maxlength, strCont) {

            if ( ({}).toString.call(str).slice(8,-1) !== 'String' )
                return false

            maxlength || (maxlength = str.length);
            strCont || (strCont = '...');

            var strTrunk = str.slice(0, maxlength);

            if( str.length != strTrunk.length ) {
                strTrunk += '...';
            }

            return strTrunk;
        }

        /**
        * validate type file of attached
        * @param Numeric cod_type_attached
        */
        ,   codInDoc: function (cod_type_attached) {

            var inDoc = _.findIndex(window._tp.inDocs, function (value, index, list) {

                var typeFile = _.findKey(window._tp.getTypeAttached, function (v, k, o) {
                    return v == cod_type_attached;
                });

                return typeFile == value;
            });

            return inDoc;
        }

        /**
        * Format time
        * @param Numeric timeInSeconds
        */
        ,   formatTime: function (timeInSeconds) {

            var sec_numb = parseInt(timeInSeconds);
            var hours = Math.floor(sec_numb / 3600);
            var minutes = Math.floor((sec_numb - (hours * 3600)) / 60) + hours * 60;
            var seconds = sec_numb - (hours * 3600) - (minutes * 60);

            if (seconds < 10) { seconds = "0" + seconds; }
            return minutes + ':' + seconds;
        }

        /**
        * get cookies
        * @param String name
        */
        ,   getCookie: function (name) {
            var cookieValue = null;
            if (document.cookie && document.cookie != '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    // Does this cookie string begin with the name we want?
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }

        /**
        * get cookies as object
        * @param String name
        */
        ,   parseCookies: function (cookies) {
            cookies || (cookies = document.cookie);

            var list = {};

            cookies && cookies.split(';').forEach(function( cookie ) {
                var parts = cookie.split('=');
                list[parts.shift().trim()] = decodeURI(parts.join('='));
            });

            return list;
        }

        /**
        * Display the DataTables interface in Spanish
        */
        ,   dataTableES: function (){
            return {
                "sProcessing":     "<div class=\"overlay\"><i class=\"fa fa-refresh fa-spin\"></i></div>",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty":      "Registros del 0 al 0 de un total de 0",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        }
        /**
        * alert dialog
        * @param options Object
        */
        ,   alertDialog: function (options) {
            options || (options = {});

            var defaults = {
                title: false,
                text: ''
            };
            var settings = $.extend({}, defaults, options);

            new PNotify({
                title: settings.title,
                text: settings.text,
                icon: 'glyphicon glyphicon-info-sign',
                hide: false,
                type: 'info',
                confirm: {
                    confirm: true,
                    buttons: [{
                        text: 'Ok',
                        addClass: 'btn-default',
                        click: function(notice) {
                            notice.remove();
                        }
                    },
                    null]
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                styling: 'bootstrap3',
                stack: {'dir1': 'down', 'dir2': 'right', 'modal': false, 'overlay_close': true}
            });
        }

        /**
        * confirm dialog
        * @param options Object
        */
        ,   confirmDialog: function (options) {
            options || (options = {});

            var defaults = {
                title: false,
                text: '',
                onConfirm: function () {},
                onCancel: function () {}
            };
            var settings = $.extend({}, defaults, options);

            (new PNotify({
                title: settings.title,
                text: settings.text,
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                type: 'info',
                confirm: {
                    confirm: true
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                styling: 'bootstrap3',
                stack: {'dir1': 'down', 'dir2': 'right', 'modal': false, 'overlay_close': true}

            })).get().on('pnotify.confirm', function(notice) {
                settings.onConfirm.call(null, notice, true);
            }).on('pnotify.cancel', function(notice) {
                settings.onCancel.call(null, notice, false);
            });
        }

        /**
        * prompt dialog
        * @param options Object
        */
        ,   promptDialog: function (options) {
            options || (options = {});

            var defaults = {
                title: false,
                text: '',
                prompt_default: '',
                onConfirm: function () {},
                onCancel: function () {}
            };
            var settings = $.extend({}, defaults, options);

            (new PNotify({
                title: settings.title,
                text: settings.text,
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                type: 'info',
                confirm: {
                    prompt: true,
                    prompt_default: settings.prompt_default
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                styling: 'bootstrap3',
                stack: {'dir1': 'down', 'dir2': 'right', 'modal': false, 'overlay_close': true}

            })).get().on('pnotify.confirm', function(e, notice, val) {
                settings.onConfirm.call(null, notice, val)
            }).on('pnotify.cancel', function(e, notice) {
                settings.onCancel.call(null, notice);
            });
        }
    };

    return Misc;

}));

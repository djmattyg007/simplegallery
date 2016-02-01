/**
 * jQuery Unveil
 * A very lightweight jQuery plugin to lazy load images
 * http://luis-almeida.github.com/unveil
 * Modifications were made to this library by Matthew Gamble for
 * the purpose of use within the surrounding codebase where this
 * file is found.
 *
 * Licensed under the MIT license.
 * Copyright 2013 Luís Almeida, 2016 Matthew Gamble
 * https://github.com/luis-almeida
 *
 * The MIT License (MIT)
 * Copyright (c) 2013 Luis Almeida, 2016 Matthew Gamble
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

(function($) {
    var $w = $(window);
    $.fn.unveil = function(callback, threshold, timeout) {
        var th = threshold || 0,
            to = timeout || 100,
            images = this,
            running = false,
            timer;

        var isInView = function() {
            var $e = $(this);
            var winTop = $w.scrollTop(),
                winBot = winTop + $w.height(),
                elTop = $e.offset().top,
                elBot = elTop + $e.height();
            return elBot >= winTop - th && elTop <= winBot + th;
        };

        var unveil = function() {
            clearTimeout(timer);
            running = false;
            timer = setTimeout(function() {
                running = true;
                clearTimeout(timer);
                var inview = images.filter(isInView);
                var loaded = [];
                var callbackReturn;
                inview.each(function(index, element) {
                    callbackReturn = callback(element);
                    if (callbackReturn === false || running === false) {
                        return false;
                    }
                    loaded.push(element);
                });
                images = images.not(loaded);
            }, to);
        };

        $w.on("scroll.unveil resize.unveil", unveil);
        unveil();

        return this;
    };
})(jQuery);

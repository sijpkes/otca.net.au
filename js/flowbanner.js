/*
*The MIT License (MIT)
*
*Copyright (c) 2013 Paul Sijpkes.
*
*Permission is hereby granted, free of charge, to any person obtaining a copy
*of this software and associated documentation files (the "Software"), to deal
*in the Software without restriction, including without limitation the rights
*to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*copies of the Software, and to permit persons to whom the Software is
*furnished to do so, subject to the following conditions:
*
*The above copyright notice and this permission notice shall be included in
*all copies or substantial portions of the Software.
*
*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
*THE SOFTWARE.
*/
$(document).ready(function(){window.banners=$(".slidingbanners");if(0<window.banners.length){window.bl=0;window.bi=0;window.last=-1;window.used=[];var b;window.c=0;window.t=0;window.rf=function(){if(!(window.c<window.t)){clearInterval(b);for(var a=0;-1!=$.inArray(a,window.used)||0==a||a>window.bl||a==window.last;)a=Math.ceil(Math.random()*window.bl);-1==$.inArray(a,window.used)&&window.used.push(a);window.used.length>=window.bl&&(window.used=[]);$(".slidingbanners #banner"+a).show(100);$(".slidingbanners #banner"+
window.last).hide(100);window.last=a;window.setTimeout("window.rf()",8E3)}};b=window.setInterval("window.rf()",1E3);$(window.banners).load("banners.html",function(){window.t=0;$(".slidingbanners img").each(function(a,b){$(b).load(function(){window.c++;console.log(c+" loaded")})});$(".slidingbanners a").each(function(a){$(this).wrap("<div id='banner"+(a+1)+"' style='display: none;' class='banner'/>");window.bl++})})}});

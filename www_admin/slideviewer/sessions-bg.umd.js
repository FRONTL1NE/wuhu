(function(i,e){typeof exports=="object"&&typeof module<"u"?e(exports):typeof define=="function"&&define.amd?define(["exports"],e):(i=typeof globalThis<"u"?globalThis:i||self,e(i.SESSIONS_BG={}))})(this,function(i){"use strict";var T=Object.defineProperty;var y=(i,e,o)=>e in i?T(i,e,{enumerable:!0,configurable:!0,writable:!0,value:o}):i[e]=o;var h=(i,e,o)=>(y(i,typeof e!="symbol"?e+"":e,o),o);/*!
* @0b5vr/experimental v0.9.5
* Experimental edition of 0b5vr
*
* Copyright (c) 2019-2023 0b5vr
* @0b5vr/experimental is distributed under MIT License
* https://github.com/0b5vr/experimental-npm/blob/release/LICENSE
*/var e=class{constructor(){this.__time=0,this.__deltaTime=0,this.__isPlaying=!1}get time(){return this.__time}get deltaTime(){return this.__deltaTime}get isPlaying(){return this.__isPlaying}update(s){const t=this.__time;this.__time=s||0,this.__deltaTime=this.__time-t}play(){this.__isPlaying=!0}pause(){this.__isPlaying=!1}setTime(s){this.__time=s}},o=class extends e{constructor(){super(...arguments),this.__rtTime=0,this.__rtDate=performance.now()}get isRealtime(){return!0}update(){const s=performance.now();if(this.__isPlaying){const t=this.__time,n=s-this.__rtDate;this.__time=this.__rtTime+n/1e3,this.__deltaTime=this.time-t}else this.__rtTime=this.time,this.__rtDate=s,this.__deltaTime=0}setTime(s){this.__time=s,this.__rtTime=this.time,this.__rtDate=performance.now()}};function l(s,t){return s-Math.floor(s/t)*t}/*!
 * Turbo colormap
 *
 * Copyright 2019 Google LLC. (Apache-2.0)
 *
 * https://gist.github.com/mikhailov-work/0d177465a8151eb6ede1768d51d476c7
 */const _=384,m=.25*_;class d{constructor(t){h(this,"canvas");h(this,"context");h(this,"clock");h(this,"logoImage");h(this,"width");h(this,"height");this.canvas=t,this.context=t.getContext("2d"),this.clock=new o,this.clock.play(),this.logoImage=new Image,this.width=1920,this.height=1080,this.setSize(1920,1080)}update(){const{context:t,clock:n,width:c,height:g}=this;n.update();const f=n.time;t.fillStyle="#000",t.fillRect(0,0,c,g),t.save();{t.translate(.5*c,.5*g),t.rotate(-.3);for(let a=-15;a<=15;a++){const u=.1-.2*l(a,2),p=l(.2*a+u*f,1);for(let r=-6;r<6;r++)t.drawImage(this.logoImage,_*(.9*(r+p)-.5),m*(.8*a-.5),_,m)}}t.restore()}loadLogoImage(t){this.logoImage.src=t}setSize(t,n){this.width=this.canvas.width=t,this.height=this.canvas.height=n}}i.SessionsBg=d,Object.defineProperty(i,Symbol.toStringTag,{value:"Module"})});

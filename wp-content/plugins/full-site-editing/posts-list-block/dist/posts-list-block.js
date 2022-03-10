!function(){"use strict";var e={880:function(){},670:function(){},654:function(e,t,o){var n=o(307),i=o(175),l=o(981),r=o(609),s=o(818),a=o(736),c=o(638),u=o(153);o(880),o(670);const __=a.__,p=(0,n.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24"},(0,n.createElement)("path",{opacity:".87",fill:"none",d:"M0 0h24v24H0V0z"}),(0,n.createElement)("path",{d:"M3 5v14h17V5H3zm4 2v2H5V7h2zm-2 6v-2h2v2H5zm0 2h2v2H5v-2zm13 2H9v-2h9v2zm0-4H9v-2h9v2zm0-4H9V7h9v2z"}));(0,l.registerBlockType)(c.u2,{title:__("Blog Posts Listing","full-site-editing"),description:__("Displays your latest Blog Posts.","full-site-editing"),icon:p,category:"layout",supports:{html:!1,multiple:!1,reusable:!1,inserter:!1},attributes:c.Y4,edit:e=>{let{attributes:t,setAttributes:o,clientId:a,isSelected:c}=e;const d=(0,s.select)("core/block-editor").getBlock(a),f=(0,l.getPossibleBlockTransformations)([d]).find((e=>e&&(0,u.i)(e.name))),m=!!f;return(0,n.createElement)(n.Fragment,null,m&&(0,n.createElement)(r.Notice,{actions:[{label:__("Update Block","full-site-editing"),onClick:()=>{(0,s.dispatch)("core/block-editor").replaceBlocks(d.clientId,(0,l.switchToBlockType)(d,f.name))}}],className:"posts-list__notice",isDismissible:!1},__("An improved version of this block is available. Update for a better, more natural way to manage your blog post listings. There may be small visual changes.","full-site-editing")),(0,n.createElement)(r.Placeholder,{icon:p,label:__("Your recent blog posts will be displayed here.","full-site-editing")},c?(0,n.createElement)(r.RangeControl,{label:__("Number of posts to show","full-site-editing"),value:t.postsPerPage,onChange:e=>o({postsPerPage:e}),min:1,max:50}):null),(0,n.createElement)(i.InspectorControls,null,(0,n.createElement)(r.PanelBody,null,(0,n.createElement)(r.RangeControl,{label:__("Number of posts","full-site-editing"),value:t.postsPerPage,onChange:e=>o({postsPerPage:e}),min:1,max:50}))))},save:()=>null,transforms:u.L})},153:function(e,t,o){o.d(t,{i:function(){return r},L:function(){return s}});var n=o(981);const i=["a8c/blog-posts","newspack-blocks/homepage-articles"],l=e=>t=>{let{postsPerPage:o}=t;return(0,n.createBlock)(e,{postsToShow:o,showAvatar:!1,displayPostDate:!0,displayPostContent:!0})},r=e=>i.indexOf(e)>-1,s={to:i.map((e=>({type:"block",blocks:[e],transform:l(e)})))}},175:function(e){e.exports=window.wp.blockEditor},981:function(e){e.exports=window.wp.blocks},609:function(e){e.exports=window.wp.components},818:function(e){e.exports=window.wp.data},307:function(e){e.exports=window.wp.element},736:function(e){e.exports=window.wp.i18n},638:function(e){e.exports=JSON.parse('{"u2":"a8c/posts-list","Y4":{"postsPerPage":{"type":"number","default":10}}}')}},t={};function o(n){var i=t[n];if(void 0!==i)return i.exports;var l=t[n]={exports:{}};return e[n](l,l.exports,o),l.exports}o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,{a:t}),t},o.d=function(e,t){for(var n in t)o.o(t,n)&&!o.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var n={};!function(){o.r(n);o(654)}(),window.EditingToolkit=n}();
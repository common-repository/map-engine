!function(){"use strict";const{wpvme:e}=window,o=e.ajax_nonce,n=e.ajax_url,i=e.post_id,t=e=>{if(!e)return null;let o=-5e3,n=0,i=0;const t=e.length;for(;i!==t;++i)e[i].lat>o&&(o=e[i].lat,n=e[i].lng);return{lat:o,lng:n}},s=e=>{let{parent:o="marker",id:n,images:i,name:t,displayAddress:s,description:a,shouldHide:l=!0,cardClassName:p="",isAnchor:d=!1,isCardArrowShown:c}=e;return`\n  <div class="wme-popup  ${d?"wme-popup-anchored":"wme-popup-not-anchored"}   ${p} ${l?"wme-invisible":""}">\n    <div class="wme-popup-close wme-popup-cross">\n      <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" class="" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>\n    </div>\n    <div class=" wme-popup-${o}  wme-popup-card wme-popup-arrow ${c?"":"wme-popup-hide-arrow"}">\n      <div>\n        ${r({images:i,id:n})}\n        <div class="wme-popup-content">\n          ${t?`<div class="wme-popup-name">${t||""}</div>`:""}\n          ${s?`<div class="wme-popup-address">${s||""}</div>`:""}\n          ${a?`<div class="wme-popup-description">${a||""}</div>`:""}\n          \n        </div>\n      </div>\n    </div>\n  </div>\n`},r=e=>{let{images:o,id:n}=e;return`\n     ${o?`\n              <div class="wme-popup-carousel">\n                <div class="glide-${n} glide">\n                <div class="glide__track" data-glide-el="track">\n                  <ul class="glide__slides">\n                    ${Object.keys(o).map((e=>`<li class="glide__slide"> \n                      <div\n                      class="wme-popup-carousel-image"\n                      style="background-image: url(${o[e]})"\n                    >\n                    ${Object.keys(o).length<=1?"":`<div\n                      class="wme-popup-carousel-image-count"\n                    >\n                      ${Object.keys(o).length} photos\n                    </div>`}\n                    </div>\n                      </li>`)).join("")}\n                  </ul>\n                </div>\n                <div class="glide__arrows" data-glide-el="controls">\n                ${Object.keys(o).length<=1?"":'<button\n                type="button"\n                class="glide__arrow glide__arrow--left"\n                data-glide-dir="<"\n              >\n\n              </button>\n              <button\n                type="button"\n                class="glide__arrow glide__arrow--right"\n                data-glide-dir=">"\n              >\n              </button>'}\n            </div>\n\n            ${Object.keys(o).length<=1?"":`<div class="glide__bullets" data-glide-el="controls[nav]">\n                    ${Array(Object.keys(o).length).fill("").map(((e,o)=>`<button\n                    type="button"\n                    class="glide__bullet"\n                    data-glide-dir='=${o}'\n                  ></button>`)).join("")}\n            </div>`}\n              </div>\n              </div>\n            `:"<div></div>"}\n  `},a=(e,o)=>{const n={},{length:i}=Object.keys(o);i<=1&&(n.swipeThreshold=!1,n.dragThreshold=!1),new window.Glide(e,{type:"carousel",startAt:0,animationTimingFunc:"ease-in-out",perView:1,focusAt:"center",gap:0,...n}).mount()};var l=e=>{function o(e,o,n,i){this.latlng=e,this.setMap(o),this.appendTo=i,this.html=n,this.div=document.createElement("div")}return o.prototype=new e.maps.OverlayView,o.prototype.setPosition=function(e){e&&(this.latlng=e);const o=this.getProjection();if(!o)return;const n=o.fromLatLngToDivPixel(this.latlng);n&&n.x&&n.y&&(this.div.style.left=`${n.x}px`,this.div.style.top=`${n.y}px`)},o.prototype.init=function(){const e=this.div;if(this.html){const o=document.createElement("div");o.innerHTML=this.html,e.appendChild(o)}this.set("container",e),this.appendTo&&(this.appendTo.appendChild(e),this.set("container",this.appendTo))},o.prototype.draw=function(){this.div||(this.div=document.createElement("div")),this.div.classList.contains("wme-custom-marker")||(this.div.classList.add("wme-custom-marker"),this.getPanes().overlayImage.appendChild(this.div),this.init()),this.setPosition()},o.prototype.hide=function(){this.div&&this.div.querySelector(".wme-popup").classList.add("wme-invisible")},o.prototype.show=function(){this.div&&this.div.querySelector(".wme-popup").classList.remove("wme-invisible")},o.prototype.getIsDisplayed=function(){return!!this.div&&!this.div.querySelector(".wme-popup").classList.contains("wme-invisible")},o};const p=e=>{let{lat:o,lng:n}=e;return new window.google.maps.LatLng(o,n)},d=(e,o,n)=>{if(e&&o&&n)return window.google.maps.event.addListener(e,o,n)},c=function(e){let o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"top";const n=e.querySelector(".wme-popup"),i=n.getBoundingClientRect(),t=i.height/2,s=i.width/2,r={x:0,y:-t};return"right"===o&&(r.x=s,r.y=0),"left"===o&&(r.x=-s,r.y=0),r},m=e=>{(e=>{e.customMarkerOverlays.forEach((e=>{const o=e.container.querySelector(".wme-popup");o&&o.classList.add("wme-invisible")})),e.customMarkerAnchoredOverlays.forEach((e=>{const o=e.container.querySelectorAll(".wme-popup-anchored .wme-popup");o&&o.forEach((e=>{e.classList.add("wme-invisible")}))}))})(e),(e=>{e.customPolygonOverlays.forEach((e=>{null==e||e.hide()}))})(e)};var u=e=>{function o(e,o,n,i){this.latlng=e,this.icon=o,this.div=document.createElement("div"),this.setMap(n),this.dragged=!1,this.wrapperId=i}return o.prototype=new e.maps.OverlayView,o.prototype.setDraggable=function(e){this.draggable=e},o.prototype.getIsDisplayed=function(){return!!this.div&&"hidden"!==this.div.style.visibility},o.prototype.hide=function(){this.div&&(this.div.style.visibility="hidden")},o.prototype.show=function(){this.div&&(this.div.style.visibility="visible")},o.prototype.getElement=function(e){return e||this.div},o.prototype.init=function(){const o=this.div;if(o.style.position="absolute",this.icon){const e=document.createElement("div");e.innerHTML=this.icon,o.appendChild(e)}if(this.set("container",o),this.getPanes().floatPane.appendChild(o),this.wrapperId){const e=document.getElementById(this.wrapperId);e&&e.appendChild(o)}this.div=o,this.getElement(o),e.maps.OverlayView.preventMapHitsAndGesturesFrom(o)},o.prototype.draw=function(){this.div||(this.div=document.createElement("div")),this.div.classList.contains("wme-custom-marker")||(this.div.classList.add("wme-custom-marker"),this.getPanes().overlayImage.appendChild(this.div),this.init()),this.setPosition()},o.prototype.setPosition=function(e){e&&(this.latlng=e);const o=this.getProjection();if(!o)return;const n=o.fromLatLngToDivPixel(this.latlng);n&&n.x&&n.y&&(this.div.style.left=`${n.x}px`,this.div.style.top=`${n.y}px`)},o};const g=(e,o,n,i)=>{let t={};"anchored"===i?(t.x=0,t.y=0):t=h(n.getElement(),i),L.Map.prototype.panToOffset=function(e,o,n){const i=this.latLngToContainerPoint(e).x+o.x,t=this.latLngToContainerPoint(e).y+o.y,s=this.containerPointToLatLng([i,t]);return this.setView(s,this._zoom,{pan:n})},e.panToOffset(o,t,{})},h=function(e){let o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"top";const n=e.querySelector(".wme-popup"),i=n.getBoundingClientRect(),t=i.height/2,s=i.width/2,r={x:0,y:-t};return"right"===o&&(r.x=s,r.y=0),"left"===o&&(r.x=-s,r.y=0),r},w=(e,o)=>{document.querySelectorAll(".wpv-me-map-container-os .wme-marker-wrapper").forEach((e=>{e.id!==o&&e.querySelector(".wme-popup").classList.add("wme-invisible")}))},y=(e,o)=>{null==e||e.setStyle({color:o.color,weight:o.thickness,opacity:o.opacity})},v=(e,o)=>{e.setStyle({fillColor:o.color,fillOpacity:o.opacity||"0.5"})},k=e=>{let{markerId:o,name:n,displayAddress:i,description:t,markerIconUrl:r,iconType:a,iconUrl:l,images:p,mapId:d,isAnchored:c,popupPosition:m,isCardArrowShown:u}=e;return`<div class="wme-marker-wrapper wme-marker-popup-position-${m}  wme-global-style-${d}-marker  "  id="${o}">\n          ${c?"":s({parent:"marker",id:o,images:p,name:n,displayAddress:i,description:t,isCardArrowShown:u})}\n            ${r?`\n              <div class="wme-marker wme-marker-custom-icon wme-marker-circle">\n                <img src="${r}" alt="${n}" />\n              </div>\n            `:`\n              <div class="wme-marker ${f(a)}">\n                ${l?`<img src="${l}" alt="${n}" />`:""}\n              </div>\n            `}\n        </div>`},f=e=>{switch(e){case"circle":return"wme-marker-circle";case"marker":default:return"wme-marker-marker";case"markerAlt":return"wme-marker-marker-alt";case"rect":return"wme-marker-rect"}};let $=0;const _=e=>{let o=null;if(o=e.isAnchored?e.anchoredPopupOverlay.container:e.markerOverlay.container,$<100&&!o)return $++,void setTimeout((()=>{_(e)}),100);const{markerId:n,map:i,markerProperty:t,markerOverlay:s,images:r,isAnchored:l,rawId:p}=e;if(!o||!n)return;r&&a(`.glide-${n}`,r);const d=o.querySelector(`#${n} .wme-marker`);let u=o.querySelector(`#${n} .wme-popup`),g=o.querySelector(`#${n} .wme-popup .wme-popup-close`);l&&(u=o.querySelector(`.wme-popup-anchored-${p} .wme-popup`),g=o.querySelector(`.wme-popup-anchored-${p} .wme-popup .wme-popup-close`));let h={x:0,y:0};l||(h=c(o.querySelector(`#${n}`),null==t?void 0:t.position)),g.addEventListener("click",(()=>{u.classList.add("wme-invisible")})),"hover"===(null==t?void 0:t.popup_open_type)?(d.onmouseover=()=>{m(i),u.classList.remove("wme-invisible"),i.setCenter(s.latlng),i.panBy(h.x,h.y)},d.onmouseleave=()=>{}):d.onclick=()=>{m(i),u.classList.toggle("wme-invisible"),i.setCenter(s.latlng),i.panBy(h.x,h.y)},u.onmouseover=()=>{i.setOptions({draggable:!1})},u.onmouseleave=()=>{i.setOptions({draggable:!0})}},b=(e,o)=>{e.setOptions({strokeColor:o.color||"#000",strokeOpacity:o.opacity,strokeWeight:o.thickness})},I=(e,o)=>{e.setOptions({fillColor:o.color||"#000",fillOpacity:o.opacity||"0.5"})};let A=0;const S=e=>{const o=e.popupOverlay.container;if(A<100&&!o)return A++,void setTimeout((()=>{S(e)}),100);const{polygonId:n,polygon:i,popupOverlay:t,polygonProperty:s,map:r,images:l,isAnchored:u,rawId:g}=e;if(!o||!g)return;l&&a(`.glide-${n}`,l);const{container:h}=t,w="none"!==(null==s?void 0:s.card_display);["click","mouseover","mouseout"].forEach((e=>{var o,n;n=e,(o=i)&&n&&window.google.maps.event.clearListeners(o,n)}));const{stroke:y,fill:v}=s;let k=o.querySelector(`#${n} .wme-popup .wme-popup-close`);const f=c(h,null==s?void 0:s.popup_position);u&&(k=o.querySelector(`.wme-popup-anchored-${g} .wme-popup .wme-popup-close`)),k.addEventListener("click",(()=>{t.hide()})),d(i,"mouseover",(e=>{if(b(i,y.hover),I(i,v.hover),"hover"===(null==s?void 0:s.popup_open_type)&&w){m(r),t.show();const o={lat:e.latLng.lat(),lng:e.latLng.lng()};r.setCenter(o),r.panBy(f.x,f.y)}})),d(i,"mouseout",(()=>{b(i,y.normal),I(i,v.normal),null==s||s.popup_open_type})),d(i,"click",(e=>{if(!w)return;const o={lat:e.latLng.lat(),lng:e.latLng.lng()};if(m(r),"click"!==(null==s?void 0:s.popup_open_type)&&""!==(null==s?void 0:s.popup_open_type)&&null!=s&&s.popup_open_type||(t.getIsDisplayed()?t.hide():(t.show(),r.setCenter(o),r.panBy(f.x,f.y))),null!=s&&s.origin_point&&"click"===(null==s?void 0:s.origin_point))t.setPosition(p({...o})),t.show(),r.setCenter(o),r.panBy(f.x,f.y);else{const e=t.getIsDisplayed();t.active&&(e?null==t||t.hide():(null==t||t.show(),r.setCenter(o),r.panBy(f.x,f.y)))}}))},C=e=>{const o=t(e.points);return L.marker(o,{icon:L.divIcon({html:"<div/>",className:"wme-polygon-popup-overlay"})})},P=(e,o,n,i,t)=>{const{fill:s,stroke:r}=n;let a="";a="anchored"===(null==n?void 0:n.popup_position)?document.querySelector(`#${t} .wme-popup`):o._icon.querySelector(`#${t} .wme-popup`);const l=a.querySelector(".wme-popup-close");a.classList.add("wme-invisible"),l.addEventListener("click",(()=>{a.classList.add("wme-invisible")})),e.on("mouseover",(()=>{y(e,r.hover),v(e,s.hover),"hover"===(null==n?void 0:n.popup_open_type)&&(w(0,t),a.classList.remove("wme-invisible"),g(i,e.getCenter(),o,null==n?void 0:n.popup_position),a.addEventListener("click",(e=>{e.stopPropagation()})))})),e.on("mouseout",(()=>{y(e,r.normal),v(e,s.normal)})),e.on("click",(s=>{L.DomEvent.stopPropagation(s),"click"===(null==n?void 0:n.origin_point)&&o.setLatLng(s.latlng),"click"!==(null==n?void 0:n.popup_open_type)&&""!==(null==n?void 0:n.popup_open_type)||(w(0,t),a.classList.toggle("wme-invisible"),g(i,e.getCenter(),o,null==n?void 0:n.popup_position))}))},O=e=>{let{polygonId:o,popupPositionClassName:n,name:i,description:t,mapId:r,images:a,isCardArrowShown:l}=e;return`<div id="${o}" class="wme-marker-wrapper wme-global-style-${r}-polygon ">\n      ${s({parent:"polygon",id:o,images:a,name:i,displayAddress:null,description:t,shouldHide:!1,cardClassName:n,isCardArrowShown:l})}\n    </div>`};window.onload=()=>{!async function(){const{google:e}=window,r=document.querySelectorAll(".wpv-me-map-container-google"),d=Array.from(r).map((e=>e.dataset.mapId));let c={};await Promise.all(d.map((e=>async function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};const s=`wpvme_${e}`,r=new URLSearchParams;r.append("action",s),r.append("ajax_nonce",o),r.append("post_id",i),Object.keys(t).forEach((e=>{r.append(e,t[e])}));try{const e=await fetch(n,{method:"POST",credentials:"same-origin",body:r}),o=await e.json();if(null!=o&&o.success)return o;throw new Error(o.data)}catch(e){return{success:!1,data:e.message}}}("frontend_map_data",{map_id:e})))).then((e=>{e.forEach((e=>{if(null==e||!e.success)throw new Error("error while loading google map");{const{data:o}=e,{id:n,...i}=o;c[n]=i}}))})).catch((e=>{console.error(e),c=null})),c?r.forEach((o=>{const{mapId:n}=o.dataset,i=c[n];if(new e.maps.LatLngBounds,i){const{map_settings:r,map_entities:d,map_engine:c,markers_properties:h,polygons_properties:f}=i,{markers:$,polygons:A}=d,E=((e,o)=>e.length>0?e[0].position:o.length>0?o[0].points[0]:"")($,A),x={lat:r.center.lat||(null==E?void 0:E.lat)||37.78987698793556,lng:r.center.lng||(null==E?void 0:E.lng)||-122.39010860240992};r.disableDoubleClickZoom=!r.disableDoubleClickZoom;const T=new e.maps.Map(o,{...r,center:x}),q=(e=>[{key:"traffic",value:new e.maps.TrafficLayer},{key:"bicycling",value:new e.maps.BicyclingLayer},{key:"transit",value:new e.maps.TransitLayer}])(e),{mapLayers:M}=r;M&&M.length&&q.forEach((e=>{let{key:o,value:n}=e;null!=M&&M.includes(o)?n.setMap(T):n.setMap(null)})),T.customMarkerOverlays=[],T.customPolygonOverlays=[],T.customMarkerAnchoredOverlays=[],T.addListener("click",(()=>{m(T)})),$.forEach((e=>{((e,o,n,i,t)=>{"google"===n?((e,o,n,i)=>{const{displayAddress:t,name:r,description:a,images:d,id:c}=e,m=i[c],g=(null==m?void 0:m.popup_position)||"top",h=`wme-marker-${e.id}`,w="none"!==(null==m?void 0:m.card_display),y="off"!==(null==m?void 0:m.card_arrow),v=null!=m&&m.markerIconId||null!=e&&e.markerIconId?"":e.iconType,f=(e=>{let{position:o,map:n,html:i,wrapperId:t}=e;const{google:s}=window,r=u(s),a=new s.maps.LatLngBounds,l=new r(p({...o}),i,n,t);return a.extend(l.latlng),l})({position:e.position,map:o,html:k({markerId:h,name:r,displayAddress:t,description:a,markerIconUrl:e.markerIconUrl||m.markerIconUrl,iconType:v||m.icon_type,iconUrl:e.iconUrl||m.icon_url,images:d,mapId:n,isAnchored:"anchored"===g,popupPosition:g,isCardArrowShown:y})});let $=null;"anchored"===g&&($=(e=>{let{map:o,id:n,mapId:i,isOverrideGlobalStyles:t,markerId:r,images:a,name:d,displayAddress:c,description:m,isAnchored:u}=e;const g=l(window.google),h=new window.google.maps.LatLngBounds,w=new g(p({lat:0,lng:0}),o,`<div \n    id="${r}"\n    style="position: absolute;top: 0;\n     right: 0;"\n    class=" wme-popup-anchored-${n} wme-popup-anchored wme-marker-wrapper ${t?"":`wme-global-style-${i}-marker`} " >\n          ${s({parent:"marker",id:r,images:a,name:d,displayAddress:c,description:m,isAnchor:!0})}\n        </div>`,document.querySelector(`#wpv-me-map-container-${i}`));return h.extend(w.latlng),w.setMap(o),o.customMarkerAnchoredOverlays.push(w),w})({map:o,id:e.id,mapId:n,markerId:h,images:d,name:r,displayAddress:t,description:a,isAnchored:"anchored"===g})),o.customMarkerOverlays.push(f),w&&_({markerId:h,map:o,markerProperty:m,markerOverlay:f,images:d,isAnchored:"anchored"===g,rawId:e.id,anchoredPopupOverlay:$}),f.setMap(o)})(e,o,i,t):"os"===n&&((e,o,n,i)=>{const{displayAddress:t,name:r,description:l,position:p,images:d,id:c}=e,m=i[c],u=(null==m?void 0:m.popup_position)||"top",h="off"!==(null==m?void 0:m.card_arrow),y=null!=m&&m.markerIconId||null!=e&&e.markerIconId?"":e.iconType,v=`wme-marker-${e.id}`,f=k({markerId:v,name:r,displayAddress:t,description:l,images:d,mapId:n,popupPosition:u,markerIconUrl:e.markerIconUrl||m.markerIconUrl,iconType:y||m.icon_type,iconUrl:e.iconUrl||m.iconUrl,isCardArrowShown:h}),$=new L.marker(p,{icon:L.divIcon({html:f})});$.addTo(o);let _=$.getElement().querySelector(`#${v} .wme-popup`);"anchored"===u&&((e=>{let{map:o,id:n,mapId:i,isOverrideGlobalStyles:t,markerId:r,images:a,name:l,displayAddress:p,description:d}=e;const c=`<div \n    id="${r}"\n    style="position: absolute;top: 0;\n     right: 0; z-index:999;"\n    class=" wme-popup-anchored-${n} wme-popup-anchored wme-marker-wrapper ${t?"":`wme-global-style-${i}-marker`} "  id="${r}">\n          ${s({parent:"marker",id:r,images:a,name:l,displayAddress:p,description:d,isAnchor:!0})}\n        </div>`;document.querySelector(`#wpv-me-map-container-${i}`).insertAdjacentHTML("afterbegin",c)})({map:o,id:e.id,mapId:n,markerId:v,images:d,name:r,displayAddress:t,description:l}),_=document.querySelector(`#${v} .wme-popup`));const b=_.querySelector(".wme-popup-close");d&&a(`.glide-${v}`,d),L.DomEvent.disableScrollPropagation(_),b.addEventListener("click",(()=>{_.classList.add("wme-invisible")})),_.addEventListener("click",(e=>{e.stopPropagation()}));const I=$.getElement().querySelector(`#${v} .wme-marker`),A=null==m?void 0:m.popup_open_type;"click"===A||""===A?I.addEventListener("click",(e=>{e.stopPropagation(),w(0,v),_.classList.toggle("wme-invisible"),g(o,p,$,null==m?void 0:m.popup_position)})):I.addEventListener("mouseover",(()=>{w(0,v),_.classList.remove("wme-invisible"),g(o,p,$,null==m?void 0:m.popup_position)}))})(e,o,i,t)})(e,T,c,n,h)})),A.forEach((e=>{((e,o,n,i,r)=>{"google"===n?((e,o,n,i)=>{const{google:r}=window,{points:a,id:d,name:c,description:m,images:g}=e,h=n[d],w=(null==h?void 0:h.popup_position)||"top",y="off"!==(null==h?void 0:h.card_arrow),v=`wme-polygon-${d}`,{fill:k,stroke:f}=h,$=`wme-polygon-popup-position-${w}`,L=new r.maps.Polygon({path:a});b(L,f.normal),I(L,k.normal);let _=null;_="anchored"===w?(e=>{let{map:o,id:n,isOverrideGlobalStyles:i,mapId:t,polygonId:r,images:a,name:d,description:c,popupPositionClassName:m,isAnchored:u}=e;const g=l(window.google),h=new window.google.maps.LatLngBounds,w=new g(p({lat:0,lng:0}),o,`<div \n    style="position: absolute;top: 0;\n     right: 0;"\n    class="wme-popup-anchored-${n} wme-popup-anchored wme-marker-wrapper ${i?"":`wme-global-style-${t}-polygon`} "  id="${r}">\n          ${s({parent:"polygon",id:r,images:a,name:d,displayAddress:null,description:c,shouldHide:!0,cardClassName:m,isAnchor:!0})}\n        </div>`,document.querySelector(`#wpv-me-map-container-${t}`));return h.extend(w.latlng),w.setMap(o),w})({map:o,id:d,mapId:i,polygonId:v,images:g,name:c,description:m,popupPositionClassName:$,isAnchored:"anchored"===w}):((e,o,n)=>{const{points:i}=e,{google:s}=window,r=u(s),a=new s.maps.LatLngBounds,l=t(i),d=new r(p({...l}),n,o);return a.extend(d.latlng),d.setDraggable(!1),d.hide(),d})(e,o,O({polygonId:v,popupPositionClassName:$,name:c,description:m,mapId:i,images:g,isCardArrowShown:y})),o.customPolygonOverlays.push(_),L.setMap(o),S({polygonId:v,polygon:L,popupOverlay:_,polygonProperty:h,map:o,images:g,isAnchored:"anchored"===w,rawId:d})})(e,o,r,i):"os"===n&&((e,o,n,i)=>{const{points:t,id:r,name:l,description:p,images:d}=e,c=n[r],m=`wme-polygon-${r}`,{fill:u,stroke:g}=c,h=(null==c?void 0:c.popup_position)||"top",w=`wme-polygon-popup-position-${h}`,k="off"!==(null==c?void 0:c.card_arrow),f=L.polygon(t).addTo(o);y(f,g.normal),v(f,u.normal);let $=null;"anchored"===h?$=(e=>{let{map:o,id:n,mapId:i,polygonId:t,images:r,name:a,description:l,popupPositionClassName:p,isAnchored:d}=e;const c=`<div \n    style="position: absolute;top: 0;\n     right: 0; z-index:999;"\n    class="wme-popup-anchored-${n} wme-popup-anchored wme-marker-wrapper wme-global-style-${i}-polygon " id="${t}">\n          ${s({parent:"polygon",id:t,images:r,name:a,displayAddress:null,description:l,shouldHide:!0,cardClassName:p,isAnchor:!0})}\n        </div>`;return document.querySelector(`#wpv-me-map-container-${i}`).insertAdjacentHTML("afterbegin",c),c})({map:o,id:r,mapId:i,polygonId:m,images:d,name:l,description:p,popupPositionClassName:w,isAnchored:"anchored"===h}):($=C(e),$.setIcon(L.divIcon({html:O({polygonId:m,popupPositionClassName:w,name:l,description:p,mapId:i,images:d,isCardArrowShown:k}),className:"wme-polygon-popup-overlay"})),$.addTo(o)),d&&a(`.glide-${m}`,d),L.DomEvent.disableScrollPropagation($.getElement()),P(f,$,c,o,m)})(e,o,r,i)})(e,T,c,n,f)}))}})):console.error("Failed to load maps data")}()}}();
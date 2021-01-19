var getParents=function(elem,selector){var parents=[];var firstChar;if(selector){firstChar=selector.charAt(0);}
for(;elem&&elem!==document;elem=elem.parentNode){if(selector){if(firstChar==='.'){if(elem.classList.contains(selector.substr(1))){parents.push(elem);}}
if(firstChar==='#'){if(elem.id===selector.substr(1)){parents.push(elem);}}
if(firstChar==='['){if(elem.hasAttribute(selector.substr(1,selector.length-1))){parents.push(elem);}}
if(elem.tagName.toLowerCase()===selector){parents.push(elem);}}else{parents.push(elem);}}
if(parents.length===0){return null;}else{return parents;}};
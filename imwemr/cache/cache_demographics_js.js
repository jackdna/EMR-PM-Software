/*! jQuery v1.12.4 | (c) jQuery Foundation | jquery.org/license */
!function(a,b){"object"==typeof module&&"object"==typeof module.exports?module.exports=a.document?b(a,!0):function(a){if(!a.document)throw new Error("jQuery requires a window with a document");return b(a)}:b(a)}("undefined"!=typeof window?window:this,function(a,b){var c=[],d=a.document,e=c.slice,f=c.concat,g=c.push,h=c.indexOf,i={},j=i.toString,k=i.hasOwnProperty,l={},m="1.12.4",n=function(a,b){return new n.fn.init(a,b)},o=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,p=/^-ms-/,q=/-([\da-z])/gi,r=function(a,b){return b.toUpperCase()};n.fn=n.prototype={jquery:m,constructor:n,selector:"",length:0,toArray:function(){return e.call(this)},get:function(a){return null!=a?0>a?this[a+this.length]:this[a]:e.call(this)},pushStack:function(a){var b=n.merge(this.constructor(),a);return b.prevObject=this,b.context=this.context,b},each:function(a){return n.each(this,a)},map:function(a){return this.pushStack(n.map(this,function(b,c){return a.call(b,c,b)}))},slice:function(){return this.pushStack(e.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(a){var b=this.length,c=+a+(0>a?b:0);return this.pushStack(c>=0&&b>c?[this[c]]:[])},end:function(){return this.prevObject||this.constructor()},push:g,sort:c.sort,splice:c.splice},n.extend=n.fn.extend=function(){var a,b,c,d,e,f,g=arguments[0]||{},h=1,i=arguments.length,j=!1;for("boolean"==typeof g&&(j=g,g=arguments[h]||{},h++),"object"==typeof g||n.isFunction(g)||(g={}),h===i&&(g=this,h--);i>h;h++)if(null!=(e=arguments[h]))for(d in e)a=g[d],c=e[d],g!==c&&(j&&c&&(n.isPlainObject(c)||(b=n.isArray(c)))?(b?(b=!1,f=a&&n.isArray(a)?a:[]):f=a&&n.isPlainObject(a)?a:{},g[d]=n.extend(j,f,c)):void 0!==c&&(g[d]=c));return g},n.extend({expando:"jQuery"+(m+Math.random()).replace(/\D/g,""),isReady:!0,error:function(a){throw new Error(a)},noop:function(){},isFunction:function(a){return"function"===n.type(a)},isArray:Array.isArray||function(a){return"array"===n.type(a)},isWindow:function(a){return null!=a&&a==a.window},isNumeric:function(a){var b=a&&a.toString();return!n.isArray(a)&&b-parseFloat(b)+1>=0},isEmptyObject:function(a){var b;for(b in a)return!1;return!0},isPlainObject:function(a){var b;if(!a||"object"!==n.type(a)||a.nodeType||n.isWindow(a))return!1;try{if(a.constructor&&!k.call(a,"constructor")&&!k.call(a.constructor.prototype,"isPrototypeOf"))return!1}catch(c){return!1}if(!l.ownFirst)for(b in a)return k.call(a,b);for(b in a);return void 0===b||k.call(a,b)},type:function(a){return null==a?a+"":"object"==typeof a||"function"==typeof a?i[j.call(a)]||"object":typeof a},globalEval:function(b){b&&n.trim(b)&&(a.execScript||function(b){a.eval.call(a,b)})(b)},camelCase:function(a){return a.replace(p,"ms-").replace(q,r)},nodeName:function(a,b){return a.nodeName&&a.nodeName.toLowerCase()===b.toLowerCase()},each:function(a,b){var c,d=0;if(s(a)){for(c=a.length;c>d;d++)if(b.call(a[d],d,a[d])===!1)break}else for(d in a)if(b.call(a[d],d,a[d])===!1)break;return a},trim:function(a){return null==a?"":(a+"").replace(o,"")},makeArray:function(a,b){var c=b||[];return null!=a&&(s(Object(a))?n.merge(c,"string"==typeof a?[a]:a):g.call(c,a)),c},inArray:function(a,b,c){var d;if(b){if(h)return h.call(b,a,c);for(d=b.length,c=c?0>c?Math.max(0,d+c):c:0;d>c;c++)if(c in b&&b[c]===a)return c}return-1},merge:function(a,b){var c=+b.length,d=0,e=a.length;while(c>d)a[e++]=b[d++];if(c!==c)while(void 0!==b[d])a[e++]=b[d++];return a.length=e,a},grep:function(a,b,c){for(var d,e=[],f=0,g=a.length,h=!c;g>f;f++)d=!b(a[f],f),d!==h&&e.push(a[f]);return e},map:function(a,b,c){var d,e,g=0,h=[];if(s(a))for(d=a.length;d>g;g++)e=b(a[g],g,c),null!=e&&h.push(e);else for(g in a)e=b(a[g],g,c),null!=e&&h.push(e);return f.apply([],h)},guid:1,proxy:function(a,b){var c,d,f;return"string"==typeof b&&(f=a[b],b=a,a=f),n.isFunction(a)?(c=e.call(arguments,2),d=function(){return a.apply(b||this,c.concat(e.call(arguments)))},d.guid=a.guid=a.guid||n.guid++,d):void 0},now:function(){return+new Date},support:l}),"function"==typeof Symbol&&(n.fn[Symbol.iterator]=c[Symbol.iterator]),n.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(a,b){i["[object "+b+"]"]=b.toLowerCase()});function s(a){var b=!!a&&"length"in a&&a.length,c=n.type(a);return"function"===c||n.isWindow(a)?!1:"array"===c||0===b||"number"==typeof b&&b>0&&b-1 in a}var t=function(a){var b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u="sizzle"+1*new Date,v=a.document,w=0,x=0,y=ga(),z=ga(),A=ga(),B=function(a,b){return a===b&&(l=!0),0},C=1<<31,D={}.hasOwnProperty,E=[],F=E.pop,G=E.push,H=E.push,I=E.slice,J=function(a,b){for(var c=0,d=a.length;d>c;c++)if(a[c]===b)return c;return-1},K="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",L="[\\x20\\t\\r\\n\\f]",M="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",N="\\["+L+"*("+M+")(?:"+L+"*([*^$|!~]?=)"+L+"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|("+M+"))|)"+L+"*\\]",O=":("+M+")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|"+N+")*)|.*)\\)|)",P=new RegExp(L+"+","g"),Q=new RegExp("^"+L+"+|((?:^|[^\\\\])(?:\\\\.)*)"+L+"+$","g"),R=new RegExp("^"+L+"*,"+L+"*"),S=new RegExp("^"+L+"*([>+~]|"+L+")"+L+"*"),T=new RegExp("="+L+"*([^\\]'\"]*?)"+L+"*\\]","g"),U=new RegExp(O),V=new RegExp("^"+M+"$"),W={ID:new RegExp("^#("+M+")"),CLASS:new RegExp("^\\.("+M+")"),TAG:new RegExp("^("+M+"|[*])"),ATTR:new RegExp("^"+N),PSEUDO:new RegExp("^"+O),CHILD:new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+L+"*(even|odd|(([+-]|)(\\d*)n|)"+L+"*(?:([+-]|)"+L+"*(\\d+)|))"+L+"*\\)|)","i"),bool:new RegExp("^(?:"+K+")$","i"),needsContext:new RegExp("^"+L+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+L+"*((?:-\\d)?\\d*)"+L+"*\\)|)(?=[^-]|$)","i")},X=/^(?:input|select|textarea|button)$/i,Y=/^h\d$/i,Z=/^[^{]+\{\s*\[native \w/,$=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,_=/[+~]/,aa=/'|\\/g,ba=new RegExp("\\\\([\\da-f]{1,6}"+L+"?|("+L+")|.)","ig"),ca=function(a,b,c){var d="0x"+b-65536;return d!==d||c?b:0>d?String.fromCharCode(d+65536):String.fromCharCode(d>>10|55296,1023&d|56320)},da=function(){m()};try{H.apply(E=I.call(v.childNodes),v.childNodes),E[v.childNodes.length].nodeType}catch(ea){H={apply:E.length?function(a,b){G.apply(a,I.call(b))}:function(a,b){var c=a.length,d=0;while(a[c++]=b[d++]);a.length=c-1}}}function fa(a,b,d,e){var f,h,j,k,l,o,r,s,w=b&&b.ownerDocument,x=b?b.nodeType:9;if(d=d||[],"string"!=typeof a||!a||1!==x&&9!==x&&11!==x)return d;if(!e&&((b?b.ownerDocument||b:v)!==n&&m(b),b=b||n,p)){if(11!==x&&(o=$.exec(a)))if(f=o[1]){if(9===x){if(!(j=b.getElementById(f)))return d;if(j.id===f)return d.push(j),d}else if(w&&(j=w.getElementById(f))&&t(b,j)&&j.id===f)return d.push(j),d}else{if(o[2])return H.apply(d,b.getElementsByTagName(a)),d;if((f=o[3])&&c.getElementsByClassName&&b.getElementsByClassName)return H.apply(d,b.getElementsByClassName(f)),d}if(c.qsa&&!A[a+" "]&&(!q||!q.test(a))){if(1!==x)w=b,s=a;else if("object"!==b.nodeName.toLowerCase()){(k=b.getAttribute("id"))?k=k.replace(aa,"\\$&"):b.setAttribute("id",k=u),r=g(a),h=r.length,l=V.test(k)?"#"+k:"[id='"+k+"']";while(h--)r[h]=l+" "+qa(r[h]);s=r.join(","),w=_.test(a)&&oa(b.parentNode)||b}if(s)try{return H.apply(d,w.querySelectorAll(s)),d}catch(y){}finally{k===u&&b.removeAttribute("id")}}}return i(a.replace(Q,"$1"),b,d,e)}function ga(){var a=[];function b(c,e){return a.push(c+" ")>d.cacheLength&&delete b[a.shift()],b[c+" "]=e}return b}function ha(a){return a[u]=!0,a}function ia(a){var b=n.createElement("div");try{return!!a(b)}catch(c){return!1}finally{b.parentNode&&b.parentNode.removeChild(b),b=null}}function ja(a,b){var c=a.split("|"),e=c.length;while(e--)d.attrHandle[c[e]]=b}function ka(a,b){var c=b&&a,d=c&&1===a.nodeType&&1===b.nodeType&&(~b.sourceIndex||C)-(~a.sourceIndex||C);if(d)return d;if(c)while(c=c.nextSibling)if(c===b)return-1;return a?1:-1}function la(a){return function(b){var c=b.nodeName.toLowerCase();return"input"===c&&b.type===a}}function ma(a){return function(b){var c=b.nodeName.toLowerCase();return("input"===c||"button"===c)&&b.type===a}}function na(a){return ha(function(b){return b=+b,ha(function(c,d){var e,f=a([],c.length,b),g=f.length;while(g--)c[e=f[g]]&&(c[e]=!(d[e]=c[e]))})})}function oa(a){return a&&"undefined"!=typeof a.getElementsByTagName&&a}c=fa.support={},f=fa.isXML=function(a){var b=a&&(a.ownerDocument||a).documentElement;return b?"HTML"!==b.nodeName:!1},m=fa.setDocument=function(a){var b,e,g=a?a.ownerDocument||a:v;return g!==n&&9===g.nodeType&&g.documentElement?(n=g,o=n.documentElement,p=!f(n),(e=n.defaultView)&&e.top!==e&&(e.addEventListener?e.addEventListener("unload",da,!1):e.attachEvent&&e.attachEvent("onunload",da)),c.attributes=ia(function(a){return a.className="i",!a.getAttribute("className")}),c.getElementsByTagName=ia(function(a){return a.appendChild(n.createComment("")),!a.getElementsByTagName("*").length}),c.getElementsByClassName=Z.test(n.getElementsByClassName),c.getById=ia(function(a){return o.appendChild(a).id=u,!n.getElementsByName||!n.getElementsByName(u).length}),c.getById?(d.find.ID=function(a,b){if("undefined"!=typeof b.getElementById&&p){var c=b.getElementById(a);return c?[c]:[]}},d.filter.ID=function(a){var b=a.replace(ba,ca);return function(a){return a.getAttribute("id")===b}}):(delete d.find.ID,d.filter.ID=function(a){var b=a.replace(ba,ca);return function(a){var c="undefined"!=typeof a.getAttributeNode&&a.getAttributeNode("id");return c&&c.value===b}}),d.find.TAG=c.getElementsByTagName?function(a,b){return"undefined"!=typeof b.getElementsByTagName?b.getElementsByTagName(a):c.qsa?b.querySelectorAll(a):void 0}:function(a,b){var c,d=[],e=0,f=b.getElementsByTagName(a);if("*"===a){while(c=f[e++])1===c.nodeType&&d.push(c);return d}return f},d.find.CLASS=c.getElementsByClassName&&function(a,b){return"undefined"!=typeof b.getElementsByClassName&&p?b.getElementsByClassName(a):void 0},r=[],q=[],(c.qsa=Z.test(n.querySelectorAll))&&(ia(function(a){o.appendChild(a).innerHTML="<a id='"+u+"'></a><select id='"+u+"-\r\\' msallowcapture=''><option selected=''></option></select>",a.querySelectorAll("[msallowcapture^='']").length&&q.push("[*^$]="+L+"*(?:''|\"\")"),a.querySelectorAll("[selected]").length||q.push("\\["+L+"*(?:value|"+K+")"),a.querySelectorAll("[id~="+u+"-]").length||q.push("~="),a.querySelectorAll(":checked").length||q.push(":checked"),a.querySelectorAll("a#"+u+"+*").length||q.push(".#.+[+~]")}),ia(function(a){var b=n.createElement("input");b.setAttribute("type","hidden"),a.appendChild(b).setAttribute("name","D"),a.querySelectorAll("[name=d]").length&&q.push("name"+L+"*[*^$|!~]?="),a.querySelectorAll(":enabled").length||q.push(":enabled",":disabled"),a.querySelectorAll("*,:x"),q.push(",.*:")})),(c.matchesSelector=Z.test(s=o.matches||o.webkitMatchesSelector||o.mozMatchesSelector||o.oMatchesSelector||o.msMatchesSelector))&&ia(function(a){c.disconnectedMatch=s.call(a,"div"),s.call(a,"[s!='']:x"),r.push("!=",O)}),q=q.length&&new RegExp(q.join("|")),r=r.length&&new RegExp(r.join("|")),b=Z.test(o.compareDocumentPosition),t=b||Z.test(o.contains)?function(a,b){var c=9===a.nodeType?a.documentElement:a,d=b&&b.parentNode;return a===d||!(!d||1!==d.nodeType||!(c.contains?c.contains(d):a.compareDocumentPosition&&16&a.compareDocumentPosition(d)))}:function(a,b){if(b)while(b=b.parentNode)if(b===a)return!0;return!1},B=b?function(a,b){if(a===b)return l=!0,0;var d=!a.compareDocumentPosition-!b.compareDocumentPosition;return d?d:(d=(a.ownerDocument||a)===(b.ownerDocument||b)?a.compareDocumentPosition(b):1,1&d||!c.sortDetached&&b.compareDocumentPosition(a)===d?a===n||a.ownerDocument===v&&t(v,a)?-1:b===n||b.ownerDocument===v&&t(v,b)?1:k?J(k,a)-J(k,b):0:4&d?-1:1)}:function(a,b){if(a===b)return l=!0,0;var c,d=0,e=a.parentNode,f=b.parentNode,g=[a],h=[b];if(!e||!f)return a===n?-1:b===n?1:e?-1:f?1:k?J(k,a)-J(k,b):0;if(e===f)return ka(a,b);c=a;while(c=c.parentNode)g.unshift(c);c=b;while(c=c.parentNode)h.unshift(c);while(g[d]===h[d])d++;return d?ka(g[d],h[d]):g[d]===v?-1:h[d]===v?1:0},n):n},fa.matches=function(a,b){return fa(a,null,null,b)},fa.matchesSelector=function(a,b){if((a.ownerDocument||a)!==n&&m(a),b=b.replace(T,"='$1']"),c.matchesSelector&&p&&!A[b+" "]&&(!r||!r.test(b))&&(!q||!q.test(b)))try{var d=s.call(a,b);if(d||c.disconnectedMatch||a.document&&11!==a.document.nodeType)return d}catch(e){}return fa(b,n,null,[a]).length>0},fa.contains=function(a,b){return(a.ownerDocument||a)!==n&&m(a),t(a,b)},fa.attr=function(a,b){(a.ownerDocument||a)!==n&&m(a);var e=d.attrHandle[b.toLowerCase()],f=e&&D.call(d.attrHandle,b.toLowerCase())?e(a,b,!p):void 0;return void 0!==f?f:c.attributes||!p?a.getAttribute(b):(f=a.getAttributeNode(b))&&f.specified?f.value:null},fa.error=function(a){throw new Error("Syntax error, unrecognized expression: "+a)},fa.uniqueSort=function(a){var b,d=[],e=0,f=0;if(l=!c.detectDuplicates,k=!c.sortStable&&a.slice(0),a.sort(B),l){while(b=a[f++])b===a[f]&&(e=d.push(f));while(e--)a.splice(d[e],1)}return k=null,a},e=fa.getText=function(a){var b,c="",d=0,f=a.nodeType;if(f){if(1===f||9===f||11===f){if("string"==typeof a.textContent)return a.textContent;for(a=a.firstChild;a;a=a.nextSibling)c+=e(a)}else if(3===f||4===f)return a.nodeValue}else while(b=a[d++])c+=e(b);return c},d=fa.selectors={cacheLength:50,createPseudo:ha,match:W,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(a){return a[1]=a[1].replace(ba,ca),a[3]=(a[3]||a[4]||a[5]||"").replace(ba,ca),"~="===a[2]&&(a[3]=" "+a[3]+" "),a.slice(0,4)},CHILD:function(a){return a[1]=a[1].toLowerCase(),"nth"===a[1].slice(0,3)?(a[3]||fa.error(a[0]),a[4]=+(a[4]?a[5]+(a[6]||1):2*("even"===a[3]||"odd"===a[3])),a[5]=+(a[7]+a[8]||"odd"===a[3])):a[3]&&fa.error(a[0]),a},PSEUDO:function(a){var b,c=!a[6]&&a[2];return W.CHILD.test(a[0])?null:(a[3]?a[2]=a[4]||a[5]||"":c&&U.test(c)&&(b=g(c,!0))&&(b=c.indexOf(")",c.length-b)-c.length)&&(a[0]=a[0].slice(0,b),a[2]=c.slice(0,b)),a.slice(0,3))}},filter:{TAG:function(a){var b=a.replace(ba,ca).toLowerCase();return"*"===a?function(){return!0}:function(a){return a.nodeName&&a.nodeName.toLowerCase()===b}},CLASS:function(a){var b=y[a+" "];return b||(b=new RegExp("(^|"+L+")"+a+"("+L+"|$)"))&&y(a,function(a){return b.test("string"==typeof a.className&&a.className||"undefined"!=typeof a.getAttribute&&a.getAttribute("class")||"")})},ATTR:function(a,b,c){return function(d){var e=fa.attr(d,a);return null==e?"!="===b:b?(e+="","="===b?e===c:"!="===b?e!==c:"^="===b?c&&0===e.indexOf(c):"*="===b?c&&e.indexOf(c)>-1:"$="===b?c&&e.slice(-c.length)===c:"~="===b?(" "+e.replace(P," ")+" ").indexOf(c)>-1:"|="===b?e===c||e.slice(0,c.length+1)===c+"-":!1):!0}},CHILD:function(a,b,c,d,e){var f="nth"!==a.slice(0,3),g="last"!==a.slice(-4),h="of-type"===b;return 1===d&&0===e?function(a){return!!a.parentNode}:function(b,c,i){var j,k,l,m,n,o,p=f!==g?"nextSibling":"previousSibling",q=b.parentNode,r=h&&b.nodeName.toLowerCase(),s=!i&&!h,t=!1;if(q){if(f){while(p){m=b;while(m=m[p])if(h?m.nodeName.toLowerCase()===r:1===m.nodeType)return!1;o=p="only"===a&&!o&&"nextSibling"}return!0}if(o=[g?q.firstChild:q.lastChild],g&&s){m=q,l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),j=k[a]||[],n=j[0]===w&&j[1],t=n&&j[2],m=n&&q.childNodes[n];while(m=++n&&m&&m[p]||(t=n=0)||o.pop())if(1===m.nodeType&&++t&&m===b){k[a]=[w,n,t];break}}else if(s&&(m=b,l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),j=k[a]||[],n=j[0]===w&&j[1],t=n),t===!1)while(m=++n&&m&&m[p]||(t=n=0)||o.pop())if((h?m.nodeName.toLowerCase()===r:1===m.nodeType)&&++t&&(s&&(l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),k[a]=[w,t]),m===b))break;return t-=e,t===d||t%d===0&&t/d>=0}}},PSEUDO:function(a,b){var c,e=d.pseudos[a]||d.setFilters[a.toLowerCase()]||fa.error("unsupported pseudo: "+a);return e[u]?e(b):e.length>1?(c=[a,a,"",b],d.setFilters.hasOwnProperty(a.toLowerCase())?ha(function(a,c){var d,f=e(a,b),g=f.length;while(g--)d=J(a,f[g]),a[d]=!(c[d]=f[g])}):function(a){return e(a,0,c)}):e}},pseudos:{not:ha(function(a){var b=[],c=[],d=h(a.replace(Q,"$1"));return d[u]?ha(function(a,b,c,e){var f,g=d(a,null,e,[]),h=a.length;while(h--)(f=g[h])&&(a[h]=!(b[h]=f))}):function(a,e,f){return b[0]=a,d(b,null,f,c),b[0]=null,!c.pop()}}),has:ha(function(a){return function(b){return fa(a,b).length>0}}),contains:ha(function(a){return a=a.replace(ba,ca),function(b){return(b.textContent||b.innerText||e(b)).indexOf(a)>-1}}),lang:ha(function(a){return V.test(a||"")||fa.error("unsupported lang: "+a),a=a.replace(ba,ca).toLowerCase(),function(b){var c;do if(c=p?b.lang:b.getAttribute("xml:lang")||b.getAttribute("lang"))return c=c.toLowerCase(),c===a||0===c.indexOf(a+"-");while((b=b.parentNode)&&1===b.nodeType);return!1}}),target:function(b){var c=a.location&&a.location.hash;return c&&c.slice(1)===b.id},root:function(a){return a===o},focus:function(a){return a===n.activeElement&&(!n.hasFocus||n.hasFocus())&&!!(a.type||a.href||~a.tabIndex)},enabled:function(a){return a.disabled===!1},disabled:function(a){return a.disabled===!0},checked:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&!!a.checked||"option"===b&&!!a.selected},selected:function(a){return a.parentNode&&a.parentNode.selectedIndex,a.selected===!0},empty:function(a){for(a=a.firstChild;a;a=a.nextSibling)if(a.nodeType<6)return!1;return!0},parent:function(a){return!d.pseudos.empty(a)},header:function(a){return Y.test(a.nodeName)},input:function(a){return X.test(a.nodeName)},button:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&"button"===a.type||"button"===b},text:function(a){var b;return"input"===a.nodeName.toLowerCase()&&"text"===a.type&&(null==(b=a.getAttribute("type"))||"text"===b.toLowerCase())},first:na(function(){return[0]}),last:na(function(a,b){return[b-1]}),eq:na(function(a,b,c){return[0>c?c+b:c]}),even:na(function(a,b){for(var c=0;b>c;c+=2)a.push(c);return a}),odd:na(function(a,b){for(var c=1;b>c;c+=2)a.push(c);return a}),lt:na(function(a,b,c){for(var d=0>c?c+b:c;--d>=0;)a.push(d);return a}),gt:na(function(a,b,c){for(var d=0>c?c+b:c;++d<b;)a.push(d);return a})}},d.pseudos.nth=d.pseudos.eq;for(b in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})d.pseudos[b]=la(b);for(b in{submit:!0,reset:!0})d.pseudos[b]=ma(b);function pa(){}pa.prototype=d.filters=d.pseudos,d.setFilters=new pa,g=fa.tokenize=function(a,b){var c,e,f,g,h,i,j,k=z[a+" "];if(k)return b?0:k.slice(0);h=a,i=[],j=d.preFilter;while(h){c&&!(e=R.exec(h))||(e&&(h=h.slice(e[0].length)||h),i.push(f=[])),c=!1,(e=S.exec(h))&&(c=e.shift(),f.push({value:c,type:e[0].replace(Q," ")}),h=h.slice(c.length));for(g in d.filter)!(e=W[g].exec(h))||j[g]&&!(e=j[g](e))||(c=e.shift(),f.push({value:c,type:g,matches:e}),h=h.slice(c.length));if(!c)break}return b?h.length:h?fa.error(a):z(a,i).slice(0)};function qa(a){for(var b=0,c=a.length,d="";c>b;b++)d+=a[b].value;return d}function ra(a,b,c){var d=b.dir,e=c&&"parentNode"===d,f=x++;return b.first?function(b,c,f){while(b=b[d])if(1===b.nodeType||e)return a(b,c,f)}:function(b,c,g){var h,i,j,k=[w,f];if(g){while(b=b[d])if((1===b.nodeType||e)&&a(b,c,g))return!0}else while(b=b[d])if(1===b.nodeType||e){if(j=b[u]||(b[u]={}),i=j[b.uniqueID]||(j[b.uniqueID]={}),(h=i[d])&&h[0]===w&&h[1]===f)return k[2]=h[2];if(i[d]=k,k[2]=a(b,c,g))return!0}}}function sa(a){return a.length>1?function(b,c,d){var e=a.length;while(e--)if(!a[e](b,c,d))return!1;return!0}:a[0]}function ta(a,b,c){for(var d=0,e=b.length;e>d;d++)fa(a,b[d],c);return c}function ua(a,b,c,d,e){for(var f,g=[],h=0,i=a.length,j=null!=b;i>h;h++)(f=a[h])&&(c&&!c(f,d,e)||(g.push(f),j&&b.push(h)));return g}function va(a,b,c,d,e,f){return d&&!d[u]&&(d=va(d)),e&&!e[u]&&(e=va(e,f)),ha(function(f,g,h,i){var j,k,l,m=[],n=[],o=g.length,p=f||ta(b||"*",h.nodeType?[h]:h,[]),q=!a||!f&&b?p:ua(p,m,a,h,i),r=c?e||(f?a:o||d)?[]:g:q;if(c&&c(q,r,h,i),d){j=ua(r,n),d(j,[],h,i),k=j.length;while(k--)(l=j[k])&&(r[n[k]]=!(q[n[k]]=l))}if(f){if(e||a){if(e){j=[],k=r.length;while(k--)(l=r[k])&&j.push(q[k]=l);e(null,r=[],j,i)}k=r.length;while(k--)(l=r[k])&&(j=e?J(f,l):m[k])>-1&&(f[j]=!(g[j]=l))}}else r=ua(r===g?r.splice(o,r.length):r),e?e(null,g,r,i):H.apply(g,r)})}function wa(a){for(var b,c,e,f=a.length,g=d.relative[a[0].type],h=g||d.relative[" "],i=g?1:0,k=ra(function(a){return a===b},h,!0),l=ra(function(a){return J(b,a)>-1},h,!0),m=[function(a,c,d){var e=!g&&(d||c!==j)||((b=c).nodeType?k(a,c,d):l(a,c,d));return b=null,e}];f>i;i++)if(c=d.relative[a[i].type])m=[ra(sa(m),c)];else{if(c=d.filter[a[i].type].apply(null,a[i].matches),c[u]){for(e=++i;f>e;e++)if(d.relative[a[e].type])break;return va(i>1&&sa(m),i>1&&qa(a.slice(0,i-1).concat({value:" "===a[i-2].type?"*":""})).replace(Q,"$1"),c,e>i&&wa(a.slice(i,e)),f>e&&wa(a=a.slice(e)),f>e&&qa(a))}m.push(c)}return sa(m)}function xa(a,b){var c=b.length>0,e=a.length>0,f=function(f,g,h,i,k){var l,o,q,r=0,s="0",t=f&&[],u=[],v=j,x=f||e&&d.find.TAG("*",k),y=w+=null==v?1:Math.random()||.1,z=x.length;for(k&&(j=g===n||g||k);s!==z&&null!=(l=x[s]);s++){if(e&&l){o=0,g||l.ownerDocument===n||(m(l),h=!p);while(q=a[o++])if(q(l,g||n,h)){i.push(l);break}k&&(w=y)}c&&((l=!q&&l)&&r--,f&&t.push(l))}if(r+=s,c&&s!==r){o=0;while(q=b[o++])q(t,u,g,h);if(f){if(r>0)while(s--)t[s]||u[s]||(u[s]=F.call(i));u=ua(u)}H.apply(i,u),k&&!f&&u.length>0&&r+b.length>1&&fa.uniqueSort(i)}return k&&(w=y,j=v),t};return c?ha(f):f}return h=fa.compile=function(a,b){var c,d=[],e=[],f=A[a+" "];if(!f){b||(b=g(a)),c=b.length;while(c--)f=wa(b[c]),f[u]?d.push(f):e.push(f);f=A(a,xa(e,d)),f.selector=a}return f},i=fa.select=function(a,b,e,f){var i,j,k,l,m,n="function"==typeof a&&a,o=!f&&g(a=n.selector||a);if(e=e||[],1===o.length){if(j=o[0]=o[0].slice(0),j.length>2&&"ID"===(k=j[0]).type&&c.getById&&9===b.nodeType&&p&&d.relative[j[1].type]){if(b=(d.find.ID(k.matches[0].replace(ba,ca),b)||[])[0],!b)return e;n&&(b=b.parentNode),a=a.slice(j.shift().value.length)}i=W.needsContext.test(a)?0:j.length;while(i--){if(k=j[i],d.relative[l=k.type])break;if((m=d.find[l])&&(f=m(k.matches[0].replace(ba,ca),_.test(j[0].type)&&oa(b.parentNode)||b))){if(j.splice(i,1),a=f.length&&qa(j),!a)return H.apply(e,f),e;break}}}return(n||h(a,o))(f,b,!p,e,!b||_.test(a)&&oa(b.parentNode)||b),e},c.sortStable=u.split("").sort(B).join("")===u,c.detectDuplicates=!!l,m(),c.sortDetached=ia(function(a){return 1&a.compareDocumentPosition(n.createElement("div"))}),ia(function(a){return a.innerHTML="<a href='#'></a>","#"===a.firstChild.getAttribute("href")})||ja("type|href|height|width",function(a,b,c){return c?void 0:a.getAttribute(b,"type"===b.toLowerCase()?1:2)}),c.attributes&&ia(function(a){return a.innerHTML="<input/>",a.firstChild.setAttribute("value",""),""===a.firstChild.getAttribute("value")})||ja("value",function(a,b,c){return c||"input"!==a.nodeName.toLowerCase()?void 0:a.defaultValue}),ia(function(a){return null==a.getAttribute("disabled")})||ja(K,function(a,b,c){var d;return c?void 0:a[b]===!0?b.toLowerCase():(d=a.getAttributeNode(b))&&d.specified?d.value:null}),fa}(a);n.find=t,n.expr=t.selectors,n.expr[":"]=n.expr.pseudos,n.uniqueSort=n.unique=t.uniqueSort,n.text=t.getText,n.isXMLDoc=t.isXML,n.contains=t.contains;var u=function(a,b,c){var d=[],e=void 0!==c;while((a=a[b])&&9!==a.nodeType)if(1===a.nodeType){if(e&&n(a).is(c))break;d.push(a)}return d},v=function(a,b){for(var c=[];a;a=a.nextSibling)1===a.nodeType&&a!==b&&c.push(a);return c},w=n.expr.match.needsContext,x=/^<([\w-]+)\s*\/?>(?:<\/\1>|)$/,y=/^.[^:#\[\.,]*$/;function z(a,b,c){if(n.isFunction(b))return n.grep(a,function(a,d){return!!b.call(a,d,a)!==c});if(b.nodeType)return n.grep(a,function(a){return a===b!==c});if("string"==typeof b){if(y.test(b))return n.filter(b,a,c);b=n.filter(b,a)}return n.grep(a,function(a){return n.inArray(a,b)>-1!==c})}n.filter=function(a,b,c){var d=b[0];return c&&(a=":not("+a+")"),1===b.length&&1===d.nodeType?n.find.matchesSelector(d,a)?[d]:[]:n.find.matches(a,n.grep(b,function(a){return 1===a.nodeType}))},n.fn.extend({find:function(a){var b,c=[],d=this,e=d.length;if("string"!=typeof a)return this.pushStack(n(a).filter(function(){for(b=0;e>b;b++)if(n.contains(d[b],this))return!0}));for(b=0;e>b;b++)n.find(a,d[b],c);return c=this.pushStack(e>1?n.unique(c):c),c.selector=this.selector?this.selector+" "+a:a,c},filter:function(a){return this.pushStack(z(this,a||[],!1))},not:function(a){return this.pushStack(z(this,a||[],!0))},is:function(a){return!!z(this,"string"==typeof a&&w.test(a)?n(a):a||[],!1).length}});var A,B=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,C=n.fn.init=function(a,b,c){var e,f;if(!a)return this;if(c=c||A,"string"==typeof a){if(e="<"===a.charAt(0)&&">"===a.charAt(a.length-1)&&a.length>=3?[null,a,null]:B.exec(a),!e||!e[1]&&b)return!b||b.jquery?(b||c).find(a):this.constructor(b).find(a);if(e[1]){if(b=b instanceof n?b[0]:b,n.merge(this,n.parseHTML(e[1],b&&b.nodeType?b.ownerDocument||b:d,!0)),x.test(e[1])&&n.isPlainObject(b))for(e in b)n.isFunction(this[e])?this[e](b[e]):this.attr(e,b[e]);return this}if(f=d.getElementById(e[2]),f&&f.parentNode){if(f.id!==e[2])return A.find(a);this.length=1,this[0]=f}return this.context=d,this.selector=a,this}return a.nodeType?(this.context=this[0]=a,this.length=1,this):n.isFunction(a)?"undefined"!=typeof c.ready?c.ready(a):a(n):(void 0!==a.selector&&(this.selector=a.selector,this.context=a.context),n.makeArray(a,this))};C.prototype=n.fn,A=n(d);var D=/^(?:parents|prev(?:Until|All))/,E={children:!0,contents:!0,next:!0,prev:!0};n.fn.extend({has:function(a){var b,c=n(a,this),d=c.length;return this.filter(function(){for(b=0;d>b;b++)if(n.contains(this,c[b]))return!0})},closest:function(a,b){for(var c,d=0,e=this.length,f=[],g=w.test(a)||"string"!=typeof a?n(a,b||this.context):0;e>d;d++)for(c=this[d];c&&c!==b;c=c.parentNode)if(c.nodeType<11&&(g?g.index(c)>-1:1===c.nodeType&&n.find.matchesSelector(c,a))){f.push(c);break}return this.pushStack(f.length>1?n.uniqueSort(f):f)},index:function(a){return a?"string"==typeof a?n.inArray(this[0],n(a)):n.inArray(a.jquery?a[0]:a,this):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(a,b){return this.pushStack(n.uniqueSort(n.merge(this.get(),n(a,b))))},addBack:function(a){return this.add(null==a?this.prevObject:this.prevObject.filter(a))}});function F(a,b){do a=a[b];while(a&&1!==a.nodeType);return a}n.each({parent:function(a){var b=a.parentNode;return b&&11!==b.nodeType?b:null},parents:function(a){return u(a,"parentNode")},parentsUntil:function(a,b,c){return u(a,"parentNode",c)},next:function(a){return F(a,"nextSibling")},prev:function(a){return F(a,"previousSibling")},nextAll:function(a){return u(a,"nextSibling")},prevAll:function(a){return u(a,"previousSibling")},nextUntil:function(a,b,c){return u(a,"nextSibling",c)},prevUntil:function(a,b,c){return u(a,"previousSibling",c)},siblings:function(a){return v((a.parentNode||{}).firstChild,a)},children:function(a){return v(a.firstChild)},contents:function(a){return n.nodeName(a,"iframe")?a.contentDocument||a.contentWindow.document:n.merge([],a.childNodes)}},function(a,b){n.fn[a]=function(c,d){var e=n.map(this,b,c);return"Until"!==a.slice(-5)&&(d=c),d&&"string"==typeof d&&(e=n.filter(d,e)),this.length>1&&(E[a]||(e=n.uniqueSort(e)),D.test(a)&&(e=e.reverse())),this.pushStack(e)}});var G=/\S+/g;function H(a){var b={};return n.each(a.match(G)||[],function(a,c){b[c]=!0}),b}n.Callbacks=function(a){a="string"==typeof a?H(a):n.extend({},a);var b,c,d,e,f=[],g=[],h=-1,i=function(){for(e=a.once,d=b=!0;g.length;h=-1){c=g.shift();while(++h<f.length)f[h].apply(c[0],c[1])===!1&&a.stopOnFalse&&(h=f.length,c=!1)}a.memory||(c=!1),b=!1,e&&(f=c?[]:"")},j={add:function(){return f&&(c&&!b&&(h=f.length-1,g.push(c)),function d(b){n.each(b,function(b,c){n.isFunction(c)?a.unique&&j.has(c)||f.push(c):c&&c.length&&"string"!==n.type(c)&&d(c)})}(arguments),c&&!b&&i()),this},remove:function(){return n.each(arguments,function(a,b){var c;while((c=n.inArray(b,f,c))>-1)f.splice(c,1),h>=c&&h--}),this},has:function(a){return a?n.inArray(a,f)>-1:f.length>0},empty:function(){return f&&(f=[]),this},disable:function(){return e=g=[],f=c="",this},disabled:function(){return!f},lock:function(){return e=!0,c||j.disable(),this},locked:function(){return!!e},fireWith:function(a,c){return e||(c=c||[],c=[a,c.slice?c.slice():c],g.push(c),b||i()),this},fire:function(){return j.fireWith(this,arguments),this},fired:function(){return!!d}};return j},n.extend({Deferred:function(a){var b=[["resolve","done",n.Callbacks("once memory"),"resolved"],["reject","fail",n.Callbacks("once memory"),"rejected"],["notify","progress",n.Callbacks("memory")]],c="pending",d={state:function(){return c},always:function(){return e.done(arguments).fail(arguments),this},then:function(){var a=arguments;return n.Deferred(function(c){n.each(b,function(b,f){var g=n.isFunction(a[b])&&a[b];e[f[1]](function(){var a=g&&g.apply(this,arguments);a&&n.isFunction(a.promise)?a.promise().progress(c.notify).done(c.resolve).fail(c.reject):c[f[0]+"With"](this===d?c.promise():this,g?[a]:arguments)})}),a=null}).promise()},promise:function(a){return null!=a?n.extend(a,d):d}},e={};return d.pipe=d.then,n.each(b,function(a,f){var g=f[2],h=f[3];d[f[1]]=g.add,h&&g.add(function(){c=h},b[1^a][2].disable,b[2][2].lock),e[f[0]]=function(){return e[f[0]+"With"](this===e?d:this,arguments),this},e[f[0]+"With"]=g.fireWith}),d.promise(e),a&&a.call(e,e),e},when:function(a){var b=0,c=e.call(arguments),d=c.length,f=1!==d||a&&n.isFunction(a.promise)?d:0,g=1===f?a:n.Deferred(),h=function(a,b,c){return function(d){b[a]=this,c[a]=arguments.length>1?e.call(arguments):d,c===i?g.notifyWith(b,c):--f||g.resolveWith(b,c)}},i,j,k;if(d>1)for(i=new Array(d),j=new Array(d),k=new Array(d);d>b;b++)c[b]&&n.isFunction(c[b].promise)?c[b].promise().progress(h(b,j,i)).done(h(b,k,c)).fail(g.reject):--f;return f||g.resolveWith(k,c),g.promise()}});var I;n.fn.ready=function(a){return n.ready.promise().done(a),this},n.extend({isReady:!1,readyWait:1,holdReady:function(a){a?n.readyWait++:n.ready(!0)},ready:function(a){(a===!0?--n.readyWait:n.isReady)||(n.isReady=!0,a!==!0&&--n.readyWait>0||(I.resolveWith(d,[n]),n.fn.triggerHandler&&(n(d).triggerHandler("ready"),n(d).off("ready"))))}});function J(){d.addEventListener?(d.removeEventListener("DOMContentLoaded",K),a.removeEventListener("load",K)):(d.detachEvent("onreadystatechange",K),a.detachEvent("onload",K))}function K(){(d.addEventListener||"load"===a.event.type||"complete"===d.readyState)&&(J(),n.ready())}n.ready.promise=function(b){if(!I)if(I=n.Deferred(),"complete"===d.readyState||"loading"!==d.readyState&&!d.documentElement.doScroll)a.setTimeout(n.ready);else if(d.addEventListener)d.addEventListener("DOMContentLoaded",K),a.addEventListener("load",K);else{d.attachEvent("onreadystatechange",K),a.attachEvent("onload",K);var c=!1;try{c=null==a.frameElement&&d.documentElement}catch(e){}c&&c.doScroll&&!function f(){if(!n.isReady){try{c.doScroll("left")}catch(b){return a.setTimeout(f,50)}J(),n.ready()}}()}return I.promise(b)},n.ready.promise();var L;for(L in n(l))break;l.ownFirst="0"===L,l.inlineBlockNeedsLayout=!1,n(function(){var a,b,c,e;c=d.getElementsByTagName("body")[0],c&&c.style&&(b=d.createElement("div"),e=d.createElement("div"),e.style.cssText="position:absolute;border:0;width:0;height:0;top:0;left:-9999px",c.appendChild(e).appendChild(b),"undefined"!=typeof b.style.zoom&&(b.style.cssText="display:inline;margin:0;border:0;padding:1px;width:1px;zoom:1",l.inlineBlockNeedsLayout=a=3===b.offsetWidth,a&&(c.style.zoom=1)),c.removeChild(e))}),function(){var a=d.createElement("div");l.deleteExpando=!0;try{delete a.test}catch(b){l.deleteExpando=!1}a=null}();var M=function(a){var b=n.noData[(a.nodeName+" ").toLowerCase()],c=+a.nodeType||1;return 1!==c&&9!==c?!1:!b||b!==!0&&a.getAttribute("classid")===b},N=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,O=/([A-Z])/g;function P(a,b,c){if(void 0===c&&1===a.nodeType){var d="data-"+b.replace(O,"-$1").toLowerCase();if(c=a.getAttribute(d),"string"==typeof c){try{c="true"===c?!0:"false"===c?!1:"null"===c?null:+c+""===c?+c:N.test(c)?n.parseJSON(c):c}catch(e){}n.data(a,b,c)}else c=void 0;
}return c}function Q(a){var b;for(b in a)if(("data"!==b||!n.isEmptyObject(a[b]))&&"toJSON"!==b)return!1;return!0}function R(a,b,d,e){if(M(a)){var f,g,h=n.expando,i=a.nodeType,j=i?n.cache:a,k=i?a[h]:a[h]&&h;if(k&&j[k]&&(e||j[k].data)||void 0!==d||"string"!=typeof b)return k||(k=i?a[h]=c.pop()||n.guid++:h),j[k]||(j[k]=i?{}:{toJSON:n.noop}),"object"!=typeof b&&"function"!=typeof b||(e?j[k]=n.extend(j[k],b):j[k].data=n.extend(j[k].data,b)),g=j[k],e||(g.data||(g.data={}),g=g.data),void 0!==d&&(g[n.camelCase(b)]=d),"string"==typeof b?(f=g[b],null==f&&(f=g[n.camelCase(b)])):f=g,f}}function S(a,b,c){if(M(a)){var d,e,f=a.nodeType,g=f?n.cache:a,h=f?a[n.expando]:n.expando;if(g[h]){if(b&&(d=c?g[h]:g[h].data)){n.isArray(b)?b=b.concat(n.map(b,n.camelCase)):b in d?b=[b]:(b=n.camelCase(b),b=b in d?[b]:b.split(" ")),e=b.length;while(e--)delete d[b[e]];if(c?!Q(d):!n.isEmptyObject(d))return}(c||(delete g[h].data,Q(g[h])))&&(f?n.cleanData([a],!0):l.deleteExpando||g!=g.window?delete g[h]:g[h]=void 0)}}}n.extend({cache:{},noData:{"applet ":!0,"embed ":!0,"object ":"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"},hasData:function(a){return a=a.nodeType?n.cache[a[n.expando]]:a[n.expando],!!a&&!Q(a)},data:function(a,b,c){return R(a,b,c)},removeData:function(a,b){return S(a,b)},_data:function(a,b,c){return R(a,b,c,!0)},_removeData:function(a,b){return S(a,b,!0)}}),n.fn.extend({data:function(a,b){var c,d,e,f=this[0],g=f&&f.attributes;if(void 0===a){if(this.length&&(e=n.data(f),1===f.nodeType&&!n._data(f,"parsedAttrs"))){c=g.length;while(c--)g[c]&&(d=g[c].name,0===d.indexOf("data-")&&(d=n.camelCase(d.slice(5)),P(f,d,e[d])));n._data(f,"parsedAttrs",!0)}return e}return"object"==typeof a?this.each(function(){n.data(this,a)}):arguments.length>1?this.each(function(){n.data(this,a,b)}):f?P(f,a,n.data(f,a)):void 0},removeData:function(a){return this.each(function(){n.removeData(this,a)})}}),n.extend({queue:function(a,b,c){var d;return a?(b=(b||"fx")+"queue",d=n._data(a,b),c&&(!d||n.isArray(c)?d=n._data(a,b,n.makeArray(c)):d.push(c)),d||[]):void 0},dequeue:function(a,b){b=b||"fx";var c=n.queue(a,b),d=c.length,e=c.shift(),f=n._queueHooks(a,b),g=function(){n.dequeue(a,b)};"inprogress"===e&&(e=c.shift(),d--),e&&("fx"===b&&c.unshift("inprogress"),delete f.stop,e.call(a,g,f)),!d&&f&&f.empty.fire()},_queueHooks:function(a,b){var c=b+"queueHooks";return n._data(a,c)||n._data(a,c,{empty:n.Callbacks("once memory").add(function(){n._removeData(a,b+"queue"),n._removeData(a,c)})})}}),n.fn.extend({queue:function(a,b){var c=2;return"string"!=typeof a&&(b=a,a="fx",c--),arguments.length<c?n.queue(this[0],a):void 0===b?this:this.each(function(){var c=n.queue(this,a,b);n._queueHooks(this,a),"fx"===a&&"inprogress"!==c[0]&&n.dequeue(this,a)})},dequeue:function(a){return this.each(function(){n.dequeue(this,a)})},clearQueue:function(a){return this.queue(a||"fx",[])},promise:function(a,b){var c,d=1,e=n.Deferred(),f=this,g=this.length,h=function(){--d||e.resolveWith(f,[f])};"string"!=typeof a&&(b=a,a=void 0),a=a||"fx";while(g--)c=n._data(f[g],a+"queueHooks"),c&&c.empty&&(d++,c.empty.add(h));return h(),e.promise(b)}}),function(){var a;l.shrinkWrapBlocks=function(){if(null!=a)return a;a=!1;var b,c,e;return c=d.getElementsByTagName("body")[0],c&&c.style?(b=d.createElement("div"),e=d.createElement("div"),e.style.cssText="position:absolute;border:0;width:0;height:0;top:0;left:-9999px",c.appendChild(e).appendChild(b),"undefined"!=typeof b.style.zoom&&(b.style.cssText="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:1px;width:1px;zoom:1",b.appendChild(d.createElement("div")).style.width="5px",a=3!==b.offsetWidth),c.removeChild(e),a):void 0}}();var T=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,U=new RegExp("^(?:([+-])=|)("+T+")([a-z%]*)$","i"),V=["Top","Right","Bottom","Left"],W=function(a,b){return a=b||a,"none"===n.css(a,"display")||!n.contains(a.ownerDocument,a)};function X(a,b,c,d){var e,f=1,g=20,h=d?function(){return d.cur()}:function(){return n.css(a,b,"")},i=h(),j=c&&c[3]||(n.cssNumber[b]?"":"px"),k=(n.cssNumber[b]||"px"!==j&&+i)&&U.exec(n.css(a,b));if(k&&k[3]!==j){j=j||k[3],c=c||[],k=+i||1;do f=f||".5",k/=f,n.style(a,b,k+j);while(f!==(f=h()/i)&&1!==f&&--g)}return c&&(k=+k||+i||0,e=c[1]?k+(c[1]+1)*c[2]:+c[2],d&&(d.unit=j,d.start=k,d.end=e)),e}var Y=function(a,b,c,d,e,f,g){var h=0,i=a.length,j=null==c;if("object"===n.type(c)){e=!0;for(h in c)Y(a,b,h,c[h],!0,f,g)}else if(void 0!==d&&(e=!0,n.isFunction(d)||(g=!0),j&&(g?(b.call(a,d),b=null):(j=b,b=function(a,b,c){return j.call(n(a),c)})),b))for(;i>h;h++)b(a[h],c,g?d:d.call(a[h],h,b(a[h],c)));return e?a:j?b.call(a):i?b(a[0],c):f},Z=/^(?:checkbox|radio)$/i,$=/<([\w:-]+)/,_=/^$|\/(?:java|ecma)script/i,aa=/^\s+/,ba="abbr|article|aside|audio|bdi|canvas|data|datalist|details|dialog|figcaption|figure|footer|header|hgroup|main|mark|meter|nav|output|picture|progress|section|summary|template|time|video";function ca(a){var b=ba.split("|"),c=a.createDocumentFragment();if(c.createElement)while(b.length)c.createElement(b.pop());return c}!function(){var a=d.createElement("div"),b=d.createDocumentFragment(),c=d.createElement("input");a.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",l.leadingWhitespace=3===a.firstChild.nodeType,l.tbody=!a.getElementsByTagName("tbody").length,l.htmlSerialize=!!a.getElementsByTagName("link").length,l.html5Clone="<:nav></:nav>"!==d.createElement("nav").cloneNode(!0).outerHTML,c.type="checkbox",c.checked=!0,b.appendChild(c),l.appendChecked=c.checked,a.innerHTML="<textarea>x</textarea>",l.noCloneChecked=!!a.cloneNode(!0).lastChild.defaultValue,b.appendChild(a),c=d.createElement("input"),c.setAttribute("type","radio"),c.setAttribute("checked","checked"),c.setAttribute("name","t"),a.appendChild(c),l.checkClone=a.cloneNode(!0).cloneNode(!0).lastChild.checked,l.noCloneEvent=!!a.addEventListener,a[n.expando]=1,l.attributes=!a.getAttribute(n.expando)}();var da={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],area:[1,"<map>","</map>"],param:[1,"<object>","</object>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:l.htmlSerialize?[0,"",""]:[1,"X<div>","</div>"]};da.optgroup=da.option,da.tbody=da.tfoot=da.colgroup=da.caption=da.thead,da.th=da.td;function ea(a,b){var c,d,e=0,f="undefined"!=typeof a.getElementsByTagName?a.getElementsByTagName(b||"*"):"undefined"!=typeof a.querySelectorAll?a.querySelectorAll(b||"*"):void 0;if(!f)for(f=[],c=a.childNodes||a;null!=(d=c[e]);e++)!b||n.nodeName(d,b)?f.push(d):n.merge(f,ea(d,b));return void 0===b||b&&n.nodeName(a,b)?n.merge([a],f):f}function fa(a,b){for(var c,d=0;null!=(c=a[d]);d++)n._data(c,"globalEval",!b||n._data(b[d],"globalEval"))}var ga=/<|&#?\w+;/,ha=/<tbody/i;function ia(a){Z.test(a.type)&&(a.defaultChecked=a.checked)}function ja(a,b,c,d,e){for(var f,g,h,i,j,k,m,o=a.length,p=ca(b),q=[],r=0;o>r;r++)if(g=a[r],g||0===g)if("object"===n.type(g))n.merge(q,g.nodeType?[g]:g);else if(ga.test(g)){i=i||p.appendChild(b.createElement("div")),j=($.exec(g)||["",""])[1].toLowerCase(),m=da[j]||da._default,i.innerHTML=m[1]+n.htmlPrefilter(g)+m[2],f=m[0];while(f--)i=i.lastChild;if(!l.leadingWhitespace&&aa.test(g)&&q.push(b.createTextNode(aa.exec(g)[0])),!l.tbody){g="table"!==j||ha.test(g)?"<table>"!==m[1]||ha.test(g)?0:i:i.firstChild,f=g&&g.childNodes.length;while(f--)n.nodeName(k=g.childNodes[f],"tbody")&&!k.childNodes.length&&g.removeChild(k)}n.merge(q,i.childNodes),i.textContent="";while(i.firstChild)i.removeChild(i.firstChild);i=p.lastChild}else q.push(b.createTextNode(g));i&&p.removeChild(i),l.appendChecked||n.grep(ea(q,"input"),ia),r=0;while(g=q[r++])if(d&&n.inArray(g,d)>-1)e&&e.push(g);else if(h=n.contains(g.ownerDocument,g),i=ea(p.appendChild(g),"script"),h&&fa(i),c){f=0;while(g=i[f++])_.test(g.type||"")&&c.push(g)}return i=null,p}!function(){var b,c,e=d.createElement("div");for(b in{submit:!0,change:!0,focusin:!0})c="on"+b,(l[b]=c in a)||(e.setAttribute(c,"t"),l[b]=e.attributes[c].expando===!1);e=null}();var ka=/^(?:input|select|textarea)$/i,la=/^key/,ma=/^(?:mouse|pointer|contextmenu|drag|drop)|click/,na=/^(?:focusinfocus|focusoutblur)$/,oa=/^([^.]*)(?:\.(.+)|)/;function pa(){return!0}function qa(){return!1}function ra(){try{return d.activeElement}catch(a){}}function sa(a,b,c,d,e,f){var g,h;if("object"==typeof b){"string"!=typeof c&&(d=d||c,c=void 0);for(h in b)sa(a,h,c,d,b[h],f);return a}if(null==d&&null==e?(e=c,d=c=void 0):null==e&&("string"==typeof c?(e=d,d=void 0):(e=d,d=c,c=void 0)),e===!1)e=qa;else if(!e)return a;return 1===f&&(g=e,e=function(a){return n().off(a),g.apply(this,arguments)},e.guid=g.guid||(g.guid=n.guid++)),a.each(function(){n.event.add(this,b,e,d,c)})}n.event={global:{},add:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n._data(a);if(r){c.handler&&(i=c,c=i.handler,e=i.selector),c.guid||(c.guid=n.guid++),(g=r.events)||(g=r.events={}),(k=r.handle)||(k=r.handle=function(a){return"undefined"==typeof n||a&&n.event.triggered===a.type?void 0:n.event.dispatch.apply(k.elem,arguments)},k.elem=a),b=(b||"").match(G)||[""],h=b.length;while(h--)f=oa.exec(b[h])||[],o=q=f[1],p=(f[2]||"").split(".").sort(),o&&(j=n.event.special[o]||{},o=(e?j.delegateType:j.bindType)||o,j=n.event.special[o]||{},l=n.extend({type:o,origType:q,data:d,handler:c,guid:c.guid,selector:e,needsContext:e&&n.expr.match.needsContext.test(e),namespace:p.join(".")},i),(m=g[o])||(m=g[o]=[],m.delegateCount=0,j.setup&&j.setup.call(a,d,p,k)!==!1||(a.addEventListener?a.addEventListener(o,k,!1):a.attachEvent&&a.attachEvent("on"+o,k))),j.add&&(j.add.call(a,l),l.handler.guid||(l.handler.guid=c.guid)),e?m.splice(m.delegateCount++,0,l):m.push(l),n.event.global[o]=!0);a=null}},remove:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n.hasData(a)&&n._data(a);if(r&&(k=r.events)){b=(b||"").match(G)||[""],j=b.length;while(j--)if(h=oa.exec(b[j])||[],o=q=h[1],p=(h[2]||"").split(".").sort(),o){l=n.event.special[o]||{},o=(d?l.delegateType:l.bindType)||o,m=k[o]||[],h=h[2]&&new RegExp("(^|\\.)"+p.join("\\.(?:.*\\.|)")+"(\\.|$)"),i=f=m.length;while(f--)g=m[f],!e&&q!==g.origType||c&&c.guid!==g.guid||h&&!h.test(g.namespace)||d&&d!==g.selector&&("**"!==d||!g.selector)||(m.splice(f,1),g.selector&&m.delegateCount--,l.remove&&l.remove.call(a,g));i&&!m.length&&(l.teardown&&l.teardown.call(a,p,r.handle)!==!1||n.removeEvent(a,o,r.handle),delete k[o])}else for(o in k)n.event.remove(a,o+b[j],c,d,!0);n.isEmptyObject(k)&&(delete r.handle,n._removeData(a,"events"))}},trigger:function(b,c,e,f){var g,h,i,j,l,m,o,p=[e||d],q=k.call(b,"type")?b.type:b,r=k.call(b,"namespace")?b.namespace.split("."):[];if(i=m=e=e||d,3!==e.nodeType&&8!==e.nodeType&&!na.test(q+n.event.triggered)&&(q.indexOf(".")>-1&&(r=q.split("."),q=r.shift(),r.sort()),h=q.indexOf(":")<0&&"on"+q,b=b[n.expando]?b:new n.Event(q,"object"==typeof b&&b),b.isTrigger=f?2:3,b.namespace=r.join("."),b.rnamespace=b.namespace?new RegExp("(^|\\.)"+r.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,b.result=void 0,b.target||(b.target=e),c=null==c?[b]:n.makeArray(c,[b]),l=n.event.special[q]||{},f||!l.trigger||l.trigger.apply(e,c)!==!1)){if(!f&&!l.noBubble&&!n.isWindow(e)){for(j=l.delegateType||q,na.test(j+q)||(i=i.parentNode);i;i=i.parentNode)p.push(i),m=i;m===(e.ownerDocument||d)&&p.push(m.defaultView||m.parentWindow||a)}o=0;while((i=p[o++])&&!b.isPropagationStopped())b.type=o>1?j:l.bindType||q,g=(n._data(i,"events")||{})[b.type]&&n._data(i,"handle"),g&&g.apply(i,c),g=h&&i[h],g&&g.apply&&M(i)&&(b.result=g.apply(i,c),b.result===!1&&b.preventDefault());if(b.type=q,!f&&!b.isDefaultPrevented()&&(!l._default||l._default.apply(p.pop(),c)===!1)&&M(e)&&h&&e[q]&&!n.isWindow(e)){m=e[h],m&&(e[h]=null),n.event.triggered=q;try{e[q]()}catch(s){}n.event.triggered=void 0,m&&(e[h]=m)}return b.result}},dispatch:function(a){a=n.event.fix(a);var b,c,d,f,g,h=[],i=e.call(arguments),j=(n._data(this,"events")||{})[a.type]||[],k=n.event.special[a.type]||{};if(i[0]=a,a.delegateTarget=this,!k.preDispatch||k.preDispatch.call(this,a)!==!1){h=n.event.handlers.call(this,a,j),b=0;while((f=h[b++])&&!a.isPropagationStopped()){a.currentTarget=f.elem,c=0;while((g=f.handlers[c++])&&!a.isImmediatePropagationStopped())a.rnamespace&&!a.rnamespace.test(g.namespace)||(a.handleObj=g,a.data=g.data,d=((n.event.special[g.origType]||{}).handle||g.handler).apply(f.elem,i),void 0!==d&&(a.result=d)===!1&&(a.preventDefault(),a.stopPropagation()))}return k.postDispatch&&k.postDispatch.call(this,a),a.result}},handlers:function(a,b){var c,d,e,f,g=[],h=b.delegateCount,i=a.target;if(h&&i.nodeType&&("click"!==a.type||isNaN(a.button)||a.button<1))for(;i!=this;i=i.parentNode||this)if(1===i.nodeType&&(i.disabled!==!0||"click"!==a.type)){for(d=[],c=0;h>c;c++)f=b[c],e=f.selector+" ",void 0===d[e]&&(d[e]=f.needsContext?n(e,this).index(i)>-1:n.find(e,this,null,[i]).length),d[e]&&d.push(f);d.length&&g.push({elem:i,handlers:d})}return h<b.length&&g.push({elem:this,handlers:b.slice(h)}),g},fix:function(a){if(a[n.expando])return a;var b,c,e,f=a.type,g=a,h=this.fixHooks[f];h||(this.fixHooks[f]=h=ma.test(f)?this.mouseHooks:la.test(f)?this.keyHooks:{}),e=h.props?this.props.concat(h.props):this.props,a=new n.Event(g),b=e.length;while(b--)c=e[b],a[c]=g[c];return a.target||(a.target=g.srcElement||d),3===a.target.nodeType&&(a.target=a.target.parentNode),a.metaKey=!!a.metaKey,h.filter?h.filter(a,g):a},props:"altKey bubbles cancelable ctrlKey currentTarget detail eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(a,b){return null==a.which&&(a.which=null!=b.charCode?b.charCode:b.keyCode),a}},mouseHooks:{props:"button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(a,b){var c,e,f,g=b.button,h=b.fromElement;return null==a.pageX&&null!=b.clientX&&(e=a.target.ownerDocument||d,f=e.documentElement,c=e.body,a.pageX=b.clientX+(f&&f.scrollLeft||c&&c.scrollLeft||0)-(f&&f.clientLeft||c&&c.clientLeft||0),a.pageY=b.clientY+(f&&f.scrollTop||c&&c.scrollTop||0)-(f&&f.clientTop||c&&c.clientTop||0)),!a.relatedTarget&&h&&(a.relatedTarget=h===a.target?b.toElement:h),a.which||void 0===g||(a.which=1&g?1:2&g?3:4&g?2:0),a}},special:{load:{noBubble:!0},focus:{trigger:function(){if(this!==ra()&&this.focus)try{return this.focus(),!1}catch(a){}},delegateType:"focusin"},blur:{trigger:function(){return this===ra()&&this.blur?(this.blur(),!1):void 0},delegateType:"focusout"},click:{trigger:function(){return n.nodeName(this,"input")&&"checkbox"===this.type&&this.click?(this.click(),!1):void 0},_default:function(a){return n.nodeName(a.target,"a")}},beforeunload:{postDispatch:function(a){void 0!==a.result&&a.originalEvent&&(a.originalEvent.returnValue=a.result)}}},simulate:function(a,b,c){var d=n.extend(new n.Event,c,{type:a,isSimulated:!0});n.event.trigger(d,null,b),d.isDefaultPrevented()&&c.preventDefault()}},n.removeEvent=d.removeEventListener?function(a,b,c){a.removeEventListener&&a.removeEventListener(b,c)}:function(a,b,c){var d="on"+b;a.detachEvent&&("undefined"==typeof a[d]&&(a[d]=null),a.detachEvent(d,c))},n.Event=function(a,b){return this instanceof n.Event?(a&&a.type?(this.originalEvent=a,this.type=a.type,this.isDefaultPrevented=a.defaultPrevented||void 0===a.defaultPrevented&&a.returnValue===!1?pa:qa):this.type=a,b&&n.extend(this,b),this.timeStamp=a&&a.timeStamp||n.now(),void(this[n.expando]=!0)):new n.Event(a,b)},n.Event.prototype={constructor:n.Event,isDefaultPrevented:qa,isPropagationStopped:qa,isImmediatePropagationStopped:qa,preventDefault:function(){var a=this.originalEvent;this.isDefaultPrevented=pa,a&&(a.preventDefault?a.preventDefault():a.returnValue=!1)},stopPropagation:function(){var a=this.originalEvent;this.isPropagationStopped=pa,a&&!this.isSimulated&&(a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)},stopImmediatePropagation:function(){var a=this.originalEvent;this.isImmediatePropagationStopped=pa,a&&a.stopImmediatePropagation&&a.stopImmediatePropagation(),this.stopPropagation()}},n.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(a,b){n.event.special[a]={delegateType:b,bindType:b,handle:function(a){var c,d=this,e=a.relatedTarget,f=a.handleObj;return e&&(e===d||n.contains(d,e))||(a.type=f.origType,c=f.handler.apply(this,arguments),a.type=b),c}}}),l.submit||(n.event.special.submit={setup:function(){return n.nodeName(this,"form")?!1:void n.event.add(this,"click._submit keypress._submit",function(a){var b=a.target,c=n.nodeName(b,"input")||n.nodeName(b,"button")?n.prop(b,"form"):void 0;c&&!n._data(c,"submit")&&(n.event.add(c,"submit._submit",function(a){a._submitBubble=!0}),n._data(c,"submit",!0))})},postDispatch:function(a){a._submitBubble&&(delete a._submitBubble,this.parentNode&&!a.isTrigger&&n.event.simulate("submit",this.parentNode,a))},teardown:function(){return n.nodeName(this,"form")?!1:void n.event.remove(this,"._submit")}}),l.change||(n.event.special.change={setup:function(){return ka.test(this.nodeName)?("checkbox"!==this.type&&"radio"!==this.type||(n.event.add(this,"propertychange._change",function(a){"checked"===a.originalEvent.propertyName&&(this._justChanged=!0)}),n.event.add(this,"click._change",function(a){this._justChanged&&!a.isTrigger&&(this._justChanged=!1),n.event.simulate("change",this,a)})),!1):void n.event.add(this,"beforeactivate._change",function(a){var b=a.target;ka.test(b.nodeName)&&!n._data(b,"change")&&(n.event.add(b,"change._change",function(a){!this.parentNode||a.isSimulated||a.isTrigger||n.event.simulate("change",this.parentNode,a)}),n._data(b,"change",!0))})},handle:function(a){var b=a.target;return this!==b||a.isSimulated||a.isTrigger||"radio"!==b.type&&"checkbox"!==b.type?a.handleObj.handler.apply(this,arguments):void 0},teardown:function(){return n.event.remove(this,"._change"),!ka.test(this.nodeName)}}),l.focusin||n.each({focus:"focusin",blur:"focusout"},function(a,b){var c=function(a){n.event.simulate(b,a.target,n.event.fix(a))};n.event.special[b]={setup:function(){var d=this.ownerDocument||this,e=n._data(d,b);e||d.addEventListener(a,c,!0),n._data(d,b,(e||0)+1)},teardown:function(){var d=this.ownerDocument||this,e=n._data(d,b)-1;e?n._data(d,b,e):(d.removeEventListener(a,c,!0),n._removeData(d,b))}}}),n.fn.extend({on:function(a,b,c,d){return sa(this,a,b,c,d)},one:function(a,b,c,d){return sa(this,a,b,c,d,1)},off:function(a,b,c){var d,e;if(a&&a.preventDefault&&a.handleObj)return d=a.handleObj,n(a.delegateTarget).off(d.namespace?d.origType+"."+d.namespace:d.origType,d.selector,d.handler),this;if("object"==typeof a){for(e in a)this.off(e,b,a[e]);return this}return b!==!1&&"function"!=typeof b||(c=b,b=void 0),c===!1&&(c=qa),this.each(function(){n.event.remove(this,a,c,b)})},trigger:function(a,b){return this.each(function(){n.event.trigger(a,b,this)})},triggerHandler:function(a,b){var c=this[0];return c?n.event.trigger(a,b,c,!0):void 0}});var ta=/ jQuery\d+="(?:null|\d+)"/g,ua=new RegExp("<(?:"+ba+")[\\s/>]","i"),va=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi,wa=/<script|<style|<link/i,xa=/checked\s*(?:[^=]|=\s*.checked.)/i,ya=/^true\/(.*)/,za=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,Aa=ca(d),Ba=Aa.appendChild(d.createElement("div"));function Ca(a,b){return n.nodeName(a,"table")&&n.nodeName(11!==b.nodeType?b:b.firstChild,"tr")?a.getElementsByTagName("tbody")[0]||a.appendChild(a.ownerDocument.createElement("tbody")):a}function Da(a){return a.type=(null!==n.find.attr(a,"type"))+"/"+a.type,a}function Ea(a){var b=ya.exec(a.type);return b?a.type=b[1]:a.removeAttribute("type"),a}function Fa(a,b){if(1===b.nodeType&&n.hasData(a)){var c,d,e,f=n._data(a),g=n._data(b,f),h=f.events;if(h){delete g.handle,g.events={};for(c in h)for(d=0,e=h[c].length;e>d;d++)n.event.add(b,c,h[c][d])}g.data&&(g.data=n.extend({},g.data))}}function Ga(a,b){var c,d,e;if(1===b.nodeType){if(c=b.nodeName.toLowerCase(),!l.noCloneEvent&&b[n.expando]){e=n._data(b);for(d in e.events)n.removeEvent(b,d,e.handle);b.removeAttribute(n.expando)}"script"===c&&b.text!==a.text?(Da(b).text=a.text,Ea(b)):"object"===c?(b.parentNode&&(b.outerHTML=a.outerHTML),l.html5Clone&&a.innerHTML&&!n.trim(b.innerHTML)&&(b.innerHTML=a.innerHTML)):"input"===c&&Z.test(a.type)?(b.defaultChecked=b.checked=a.checked,b.value!==a.value&&(b.value=a.value)):"option"===c?b.defaultSelected=b.selected=a.defaultSelected:"input"!==c&&"textarea"!==c||(b.defaultValue=a.defaultValue)}}function Ha(a,b,c,d){b=f.apply([],b);var e,g,h,i,j,k,m=0,o=a.length,p=o-1,q=b[0],r=n.isFunction(q);if(r||o>1&&"string"==typeof q&&!l.checkClone&&xa.test(q))return a.each(function(e){var f=a.eq(e);r&&(b[0]=q.call(this,e,f.html())),Ha(f,b,c,d)});if(o&&(k=ja(b,a[0].ownerDocument,!1,a,d),e=k.firstChild,1===k.childNodes.length&&(k=e),e||d)){for(i=n.map(ea(k,"script"),Da),h=i.length;o>m;m++)g=k,m!==p&&(g=n.clone(g,!0,!0),h&&n.merge(i,ea(g,"script"))),c.call(a[m],g,m);if(h)for(j=i[i.length-1].ownerDocument,n.map(i,Ea),m=0;h>m;m++)g=i[m],_.test(g.type||"")&&!n._data(g,"globalEval")&&n.contains(j,g)&&(g.src?n._evalUrl&&n._evalUrl(g.src):n.globalEval((g.text||g.textContent||g.innerHTML||"").replace(za,"")));k=e=null}return a}function Ia(a,b,c){for(var d,e=b?n.filter(b,a):a,f=0;null!=(d=e[f]);f++)c||1!==d.nodeType||n.cleanData(ea(d)),d.parentNode&&(c&&n.contains(d.ownerDocument,d)&&fa(ea(d,"script")),d.parentNode.removeChild(d));return a}n.extend({htmlPrefilter:function(a){return a.replace(va,"<$1></$2>")},clone:function(a,b,c){var d,e,f,g,h,i=n.contains(a.ownerDocument,a);if(l.html5Clone||n.isXMLDoc(a)||!ua.test("<"+a.nodeName+">")?f=a.cloneNode(!0):(Ba.innerHTML=a.outerHTML,Ba.removeChild(f=Ba.firstChild)),!(l.noCloneEvent&&l.noCloneChecked||1!==a.nodeType&&11!==a.nodeType||n.isXMLDoc(a)))for(d=ea(f),h=ea(a),g=0;null!=(e=h[g]);++g)d[g]&&Ga(e,d[g]);if(b)if(c)for(h=h||ea(a),d=d||ea(f),g=0;null!=(e=h[g]);g++)Fa(e,d[g]);else Fa(a,f);return d=ea(f,"script"),d.length>0&&fa(d,!i&&ea(a,"script")),d=h=e=null,f},cleanData:function(a,b){for(var d,e,f,g,h=0,i=n.expando,j=n.cache,k=l.attributes,m=n.event.special;null!=(d=a[h]);h++)if((b||M(d))&&(f=d[i],g=f&&j[f])){if(g.events)for(e in g.events)m[e]?n.event.remove(d,e):n.removeEvent(d,e,g.handle);j[f]&&(delete j[f],k||"undefined"==typeof d.removeAttribute?d[i]=void 0:d.removeAttribute(i),c.push(f))}}}),n.fn.extend({domManip:Ha,detach:function(a){return Ia(this,a,!0)},remove:function(a){return Ia(this,a)},text:function(a){return Y(this,function(a){return void 0===a?n.text(this):this.empty().append((this[0]&&this[0].ownerDocument||d).createTextNode(a))},null,a,arguments.length)},append:function(){return Ha(this,arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=Ca(this,a);b.appendChild(a)}})},prepend:function(){return Ha(this,arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=Ca(this,a);b.insertBefore(a,b.firstChild)}})},before:function(){return Ha(this,arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this)})},after:function(){return Ha(this,arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this.nextSibling)})},empty:function(){for(var a,b=0;null!=(a=this[b]);b++){1===a.nodeType&&n.cleanData(ea(a,!1));while(a.firstChild)a.removeChild(a.firstChild);a.options&&n.nodeName(a,"select")&&(a.options.length=0)}return this},clone:function(a,b){return a=null==a?!1:a,b=null==b?a:b,this.map(function(){return n.clone(this,a,b)})},html:function(a){return Y(this,function(a){var b=this[0]||{},c=0,d=this.length;if(void 0===a)return 1===b.nodeType?b.innerHTML.replace(ta,""):void 0;if("string"==typeof a&&!wa.test(a)&&(l.htmlSerialize||!ua.test(a))&&(l.leadingWhitespace||!aa.test(a))&&!da[($.exec(a)||["",""])[1].toLowerCase()]){a=n.htmlPrefilter(a);try{for(;d>c;c++)b=this[c]||{},1===b.nodeType&&(n.cleanData(ea(b,!1)),b.innerHTML=a);b=0}catch(e){}}b&&this.empty().append(a)},null,a,arguments.length)},replaceWith:function(){var a=[];return Ha(this,arguments,function(b){var c=this.parentNode;n.inArray(this,a)<0&&(n.cleanData(ea(this)),c&&c.replaceChild(b,this))},a)}}),n.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(a,b){n.fn[a]=function(a){for(var c,d=0,e=[],f=n(a),h=f.length-1;h>=d;d++)c=d===h?this:this.clone(!0),n(f[d])[b](c),g.apply(e,c.get());return this.pushStack(e)}});var Ja,Ka={HTML:"block",BODY:"block"};function La(a,b){var c=n(b.createElement(a)).appendTo(b.body),d=n.css(c[0],"display");return c.detach(),d}function Ma(a){var b=d,c=Ka[a];return c||(c=La(a,b),"none"!==c&&c||(Ja=(Ja||n("<iframe frameborder='0' width='0' height='0'/>")).appendTo(b.documentElement),b=(Ja[0].contentWindow||Ja[0].contentDocument).document,b.write(),b.close(),c=La(a,b),Ja.detach()),Ka[a]=c),c}var Na=/^margin/,Oa=new RegExp("^("+T+")(?!px)[a-z%]+$","i"),Pa=function(a,b,c,d){var e,f,g={};for(f in b)g[f]=a.style[f],a.style[f]=b[f];e=c.apply(a,d||[]);for(f in b)a.style[f]=g[f];return e},Qa=d.documentElement;!function(){var b,c,e,f,g,h,i=d.createElement("div"),j=d.createElement("div");if(j.style){j.style.cssText="float:left;opacity:.5",l.opacity="0.5"===j.style.opacity,l.cssFloat=!!j.style.cssFloat,j.style.backgroundClip="content-box",j.cloneNode(!0).style.backgroundClip="",l.clearCloneStyle="content-box"===j.style.backgroundClip,i=d.createElement("div"),i.style.cssText="border:0;width:8px;height:0;top:0;left:-9999px;padding:0;margin-top:1px;position:absolute",j.innerHTML="",i.appendChild(j),l.boxSizing=""===j.style.boxSizing||""===j.style.MozBoxSizing||""===j.style.WebkitBoxSizing,n.extend(l,{reliableHiddenOffsets:function(){return null==b&&k(),f},boxSizingReliable:function(){return null==b&&k(),e},pixelMarginRight:function(){return null==b&&k(),c},pixelPosition:function(){return null==b&&k(),b},reliableMarginRight:function(){return null==b&&k(),g},reliableMarginLeft:function(){return null==b&&k(),h}});function k(){var k,l,m=d.documentElement;m.appendChild(i),j.style.cssText="-webkit-box-sizing:border-box;box-sizing:border-box;position:relative;display:block;margin:auto;border:1px;padding:1px;top:1%;width:50%",b=e=h=!1,c=g=!0,a.getComputedStyle&&(l=a.getComputedStyle(j),b="1%"!==(l||{}).top,h="2px"===(l||{}).marginLeft,e="4px"===(l||{width:"4px"}).width,j.style.marginRight="50%",c="4px"===(l||{marginRight:"4px"}).marginRight,k=j.appendChild(d.createElement("div")),k.style.cssText=j.style.cssText="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:0",k.style.marginRight=k.style.width="0",j.style.width="1px",g=!parseFloat((a.getComputedStyle(k)||{}).marginRight),j.removeChild(k)),j.style.display="none",f=0===j.getClientRects().length,f&&(j.style.display="",j.innerHTML="<table><tr><td></td><td>t</td></tr></table>",j.childNodes[0].style.borderCollapse="separate",k=j.getElementsByTagName("td"),k[0].style.cssText="margin:0;border:0;padding:0;display:none",f=0===k[0].offsetHeight,f&&(k[0].style.display="",k[1].style.display="none",f=0===k[0].offsetHeight)),m.removeChild(i)}}}();var Ra,Sa,Ta=/^(top|right|bottom|left)$/;a.getComputedStyle?(Ra=function(b){var c=b.ownerDocument.defaultView;return c&&c.opener||(c=a),c.getComputedStyle(b)},Sa=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Ra(a),g=c?c.getPropertyValue(b)||c[b]:void 0,""!==g&&void 0!==g||n.contains(a.ownerDocument,a)||(g=n.style(a,b)),c&&!l.pixelMarginRight()&&Oa.test(g)&&Na.test(b)&&(d=h.width,e=h.minWidth,f=h.maxWidth,h.minWidth=h.maxWidth=h.width=g,g=c.width,h.width=d,h.minWidth=e,h.maxWidth=f),void 0===g?g:g+""}):Qa.currentStyle&&(Ra=function(a){return a.currentStyle},Sa=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Ra(a),g=c?c[b]:void 0,null==g&&h&&h[b]&&(g=h[b]),Oa.test(g)&&!Ta.test(b)&&(d=h.left,e=a.runtimeStyle,f=e&&e.left,f&&(e.left=a.currentStyle.left),h.left="fontSize"===b?"1em":g,g=h.pixelLeft+"px",h.left=d,f&&(e.left=f)),void 0===g?g:g+""||"auto"});function Ua(a,b){return{get:function(){return a()?void delete this.get:(this.get=b).apply(this,arguments)}}}var Va=/alpha\([^)]*\)/i,Wa=/opacity\s*=\s*([^)]*)/i,Xa=/^(none|table(?!-c[ea]).+)/,Ya=new RegExp("^("+T+")(.*)$","i"),Za={position:"absolute",visibility:"hidden",display:"block"},$a={letterSpacing:"0",fontWeight:"400"},_a=["Webkit","O","Moz","ms"],ab=d.createElement("div").style;function bb(a){if(a in ab)return a;var b=a.charAt(0).toUpperCase()+a.slice(1),c=_a.length;while(c--)if(a=_a[c]+b,a in ab)return a}function cb(a,b){for(var c,d,e,f=[],g=0,h=a.length;h>g;g++)d=a[g],d.style&&(f[g]=n._data(d,"olddisplay"),c=d.style.display,b?(f[g]||"none"!==c||(d.style.display=""),""===d.style.display&&W(d)&&(f[g]=n._data(d,"olddisplay",Ma(d.nodeName)))):(e=W(d),(c&&"none"!==c||!e)&&n._data(d,"olddisplay",e?c:n.css(d,"display"))));for(g=0;h>g;g++)d=a[g],d.style&&(b&&"none"!==d.style.display&&""!==d.style.display||(d.style.display=b?f[g]||"":"none"));return a}function db(a,b,c){var d=Ya.exec(b);return d?Math.max(0,d[1]-(c||0))+(d[2]||"px"):b}function eb(a,b,c,d,e){for(var f=c===(d?"border":"content")?4:"width"===b?1:0,g=0;4>f;f+=2)"margin"===c&&(g+=n.css(a,c+V[f],!0,e)),d?("content"===c&&(g-=n.css(a,"padding"+V[f],!0,e)),"margin"!==c&&(g-=n.css(a,"border"+V[f]+"Width",!0,e))):(g+=n.css(a,"padding"+V[f],!0,e),"padding"!==c&&(g+=n.css(a,"border"+V[f]+"Width",!0,e)));return g}function fb(a,b,c){var d=!0,e="width"===b?a.offsetWidth:a.offsetHeight,f=Ra(a),g=l.boxSizing&&"border-box"===n.css(a,"boxSizing",!1,f);if(0>=e||null==e){if(e=Sa(a,b,f),(0>e||null==e)&&(e=a.style[b]),Oa.test(e))return e;d=g&&(l.boxSizingReliable()||e===a.style[b]),e=parseFloat(e)||0}return e+eb(a,b,c||(g?"border":"content"),d,f)+"px"}n.extend({cssHooks:{opacity:{get:function(a,b){if(b){var c=Sa(a,"opacity");return""===c?"1":c}}}},cssNumber:{animationIterationCount:!0,columnCount:!0,fillOpacity:!0,flexGrow:!0,flexShrink:!0,fontWeight:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":l.cssFloat?"cssFloat":"styleFloat"},style:function(a,b,c,d){if(a&&3!==a.nodeType&&8!==a.nodeType&&a.style){var e,f,g,h=n.camelCase(b),i=a.style;if(b=n.cssProps[h]||(n.cssProps[h]=bb(h)||h),g=n.cssHooks[b]||n.cssHooks[h],void 0===c)return g&&"get"in g&&void 0!==(e=g.get(a,!1,d))?e:i[b];if(f=typeof c,"string"===f&&(e=U.exec(c))&&e[1]&&(c=X(a,b,e),f="number"),null!=c&&c===c&&("number"===f&&(c+=e&&e[3]||(n.cssNumber[h]?"":"px")),l.clearCloneStyle||""!==c||0!==b.indexOf("background")||(i[b]="inherit"),!(g&&"set"in g&&void 0===(c=g.set(a,c,d)))))try{i[b]=c}catch(j){}}},css:function(a,b,c,d){var e,f,g,h=n.camelCase(b);return b=n.cssProps[h]||(n.cssProps[h]=bb(h)||h),g=n.cssHooks[b]||n.cssHooks[h],g&&"get"in g&&(f=g.get(a,!0,c)),void 0===f&&(f=Sa(a,b,d)),"normal"===f&&b in $a&&(f=$a[b]),""===c||c?(e=parseFloat(f),c===!0||isFinite(e)?e||0:f):f}}),n.each(["height","width"],function(a,b){n.cssHooks[b]={get:function(a,c,d){return c?Xa.test(n.css(a,"display"))&&0===a.offsetWidth?Pa(a,Za,function(){return fb(a,b,d)}):fb(a,b,d):void 0},set:function(a,c,d){var e=d&&Ra(a);return db(a,c,d?eb(a,b,d,l.boxSizing&&"border-box"===n.css(a,"boxSizing",!1,e),e):0)}}}),l.opacity||(n.cssHooks.opacity={get:function(a,b){return Wa.test((b&&a.currentStyle?a.currentStyle.filter:a.style.filter)||"")?.01*parseFloat(RegExp.$1)+"":b?"1":""},set:function(a,b){var c=a.style,d=a.currentStyle,e=n.isNumeric(b)?"alpha(opacity="+100*b+")":"",f=d&&d.filter||c.filter||"";c.zoom=1,(b>=1||""===b)&&""===n.trim(f.replace(Va,""))&&c.removeAttribute&&(c.removeAttribute("filter"),""===b||d&&!d.filter)||(c.filter=Va.test(f)?f.replace(Va,e):f+" "+e)}}),n.cssHooks.marginRight=Ua(l.reliableMarginRight,function(a,b){return b?Pa(a,{display:"inline-block"},Sa,[a,"marginRight"]):void 0}),n.cssHooks.marginLeft=Ua(l.reliableMarginLeft,function(a,b){return b?(parseFloat(Sa(a,"marginLeft"))||(n.contains(a.ownerDocument,a)?a.getBoundingClientRect().left-Pa(a,{
marginLeft:0},function(){return a.getBoundingClientRect().left}):0))+"px":void 0}),n.each({margin:"",padding:"",border:"Width"},function(a,b){n.cssHooks[a+b]={expand:function(c){for(var d=0,e={},f="string"==typeof c?c.split(" "):[c];4>d;d++)e[a+V[d]+b]=f[d]||f[d-2]||f[0];return e}},Na.test(a)||(n.cssHooks[a+b].set=db)}),n.fn.extend({css:function(a,b){return Y(this,function(a,b,c){var d,e,f={},g=0;if(n.isArray(b)){for(d=Ra(a),e=b.length;e>g;g++)f[b[g]]=n.css(a,b[g],!1,d);return f}return void 0!==c?n.style(a,b,c):n.css(a,b)},a,b,arguments.length>1)},show:function(){return cb(this,!0)},hide:function(){return cb(this)},toggle:function(a){return"boolean"==typeof a?a?this.show():this.hide():this.each(function(){W(this)?n(this).show():n(this).hide()})}});function gb(a,b,c,d,e){return new gb.prototype.init(a,b,c,d,e)}n.Tween=gb,gb.prototype={constructor:gb,init:function(a,b,c,d,e,f){this.elem=a,this.prop=c,this.easing=e||n.easing._default,this.options=b,this.start=this.now=this.cur(),this.end=d,this.unit=f||(n.cssNumber[c]?"":"px")},cur:function(){var a=gb.propHooks[this.prop];return a&&a.get?a.get(this):gb.propHooks._default.get(this)},run:function(a){var b,c=gb.propHooks[this.prop];return this.options.duration?this.pos=b=n.easing[this.easing](a,this.options.duration*a,0,1,this.options.duration):this.pos=b=a,this.now=(this.end-this.start)*b+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),c&&c.set?c.set(this):gb.propHooks._default.set(this),this}},gb.prototype.init.prototype=gb.prototype,gb.propHooks={_default:{get:function(a){var b;return 1!==a.elem.nodeType||null!=a.elem[a.prop]&&null==a.elem.style[a.prop]?a.elem[a.prop]:(b=n.css(a.elem,a.prop,""),b&&"auto"!==b?b:0)},set:function(a){n.fx.step[a.prop]?n.fx.step[a.prop](a):1!==a.elem.nodeType||null==a.elem.style[n.cssProps[a.prop]]&&!n.cssHooks[a.prop]?a.elem[a.prop]=a.now:n.style(a.elem,a.prop,a.now+a.unit)}}},gb.propHooks.scrollTop=gb.propHooks.scrollLeft={set:function(a){a.elem.nodeType&&a.elem.parentNode&&(a.elem[a.prop]=a.now)}},n.easing={linear:function(a){return a},swing:function(a){return.5-Math.cos(a*Math.PI)/2},_default:"swing"},n.fx=gb.prototype.init,n.fx.step={};var hb,ib,jb=/^(?:toggle|show|hide)$/,kb=/queueHooks$/;function lb(){return a.setTimeout(function(){hb=void 0}),hb=n.now()}function mb(a,b){var c,d={height:a},e=0;for(b=b?1:0;4>e;e+=2-b)c=V[e],d["margin"+c]=d["padding"+c]=a;return b&&(d.opacity=d.width=a),d}function nb(a,b,c){for(var d,e=(qb.tweeners[b]||[]).concat(qb.tweeners["*"]),f=0,g=e.length;g>f;f++)if(d=e[f].call(c,b,a))return d}function ob(a,b,c){var d,e,f,g,h,i,j,k,m=this,o={},p=a.style,q=a.nodeType&&W(a),r=n._data(a,"fxshow");c.queue||(h=n._queueHooks(a,"fx"),null==h.unqueued&&(h.unqueued=0,i=h.empty.fire,h.empty.fire=function(){h.unqueued||i()}),h.unqueued++,m.always(function(){m.always(function(){h.unqueued--,n.queue(a,"fx").length||h.empty.fire()})})),1===a.nodeType&&("height"in b||"width"in b)&&(c.overflow=[p.overflow,p.overflowX,p.overflowY],j=n.css(a,"display"),k="none"===j?n._data(a,"olddisplay")||Ma(a.nodeName):j,"inline"===k&&"none"===n.css(a,"float")&&(l.inlineBlockNeedsLayout&&"inline"!==Ma(a.nodeName)?p.zoom=1:p.display="inline-block")),c.overflow&&(p.overflow="hidden",l.shrinkWrapBlocks()||m.always(function(){p.overflow=c.overflow[0],p.overflowX=c.overflow[1],p.overflowY=c.overflow[2]}));for(d in b)if(e=b[d],jb.exec(e)){if(delete b[d],f=f||"toggle"===e,e===(q?"hide":"show")){if("show"!==e||!r||void 0===r[d])continue;q=!0}o[d]=r&&r[d]||n.style(a,d)}else j=void 0;if(n.isEmptyObject(o))"inline"===("none"===j?Ma(a.nodeName):j)&&(p.display=j);else{r?"hidden"in r&&(q=r.hidden):r=n._data(a,"fxshow",{}),f&&(r.hidden=!q),q?n(a).show():m.done(function(){n(a).hide()}),m.done(function(){var b;n._removeData(a,"fxshow");for(b in o)n.style(a,b,o[b])});for(d in o)g=nb(q?r[d]:0,d,m),d in r||(r[d]=g.start,q&&(g.end=g.start,g.start="width"===d||"height"===d?1:0))}}function pb(a,b){var c,d,e,f,g;for(c in a)if(d=n.camelCase(c),e=b[d],f=a[c],n.isArray(f)&&(e=f[1],f=a[c]=f[0]),c!==d&&(a[d]=f,delete a[c]),g=n.cssHooks[d],g&&"expand"in g){f=g.expand(f),delete a[d];for(c in f)c in a||(a[c]=f[c],b[c]=e)}else b[d]=e}function qb(a,b,c){var d,e,f=0,g=qb.prefilters.length,h=n.Deferred().always(function(){delete i.elem}),i=function(){if(e)return!1;for(var b=hb||lb(),c=Math.max(0,j.startTime+j.duration-b),d=c/j.duration||0,f=1-d,g=0,i=j.tweens.length;i>g;g++)j.tweens[g].run(f);return h.notifyWith(a,[j,f,c]),1>f&&i?c:(h.resolveWith(a,[j]),!1)},j=h.promise({elem:a,props:n.extend({},b),opts:n.extend(!0,{specialEasing:{},easing:n.easing._default},c),originalProperties:b,originalOptions:c,startTime:hb||lb(),duration:c.duration,tweens:[],createTween:function(b,c){var d=n.Tween(a,j.opts,b,c,j.opts.specialEasing[b]||j.opts.easing);return j.tweens.push(d),d},stop:function(b){var c=0,d=b?j.tweens.length:0;if(e)return this;for(e=!0;d>c;c++)j.tweens[c].run(1);return b?(h.notifyWith(a,[j,1,0]),h.resolveWith(a,[j,b])):h.rejectWith(a,[j,b]),this}}),k=j.props;for(pb(k,j.opts.specialEasing);g>f;f++)if(d=qb.prefilters[f].call(j,a,k,j.opts))return n.isFunction(d.stop)&&(n._queueHooks(j.elem,j.opts.queue).stop=n.proxy(d.stop,d)),d;return n.map(k,nb,j),n.isFunction(j.opts.start)&&j.opts.start.call(a,j),n.fx.timer(n.extend(i,{elem:a,anim:j,queue:j.opts.queue})),j.progress(j.opts.progress).done(j.opts.done,j.opts.complete).fail(j.opts.fail).always(j.opts.always)}n.Animation=n.extend(qb,{tweeners:{"*":[function(a,b){var c=this.createTween(a,b);return X(c.elem,a,U.exec(b),c),c}]},tweener:function(a,b){n.isFunction(a)?(b=a,a=["*"]):a=a.match(G);for(var c,d=0,e=a.length;e>d;d++)c=a[d],qb.tweeners[c]=qb.tweeners[c]||[],qb.tweeners[c].unshift(b)},prefilters:[ob],prefilter:function(a,b){b?qb.prefilters.unshift(a):qb.prefilters.push(a)}}),n.speed=function(a,b,c){var d=a&&"object"==typeof a?n.extend({},a):{complete:c||!c&&b||n.isFunction(a)&&a,duration:a,easing:c&&b||b&&!n.isFunction(b)&&b};return d.duration=n.fx.off?0:"number"==typeof d.duration?d.duration:d.duration in n.fx.speeds?n.fx.speeds[d.duration]:n.fx.speeds._default,null!=d.queue&&d.queue!==!0||(d.queue="fx"),d.old=d.complete,d.complete=function(){n.isFunction(d.old)&&d.old.call(this),d.queue&&n.dequeue(this,d.queue)},d},n.fn.extend({fadeTo:function(a,b,c,d){return this.filter(W).css("opacity",0).show().end().animate({opacity:b},a,c,d)},animate:function(a,b,c,d){var e=n.isEmptyObject(a),f=n.speed(b,c,d),g=function(){var b=qb(this,n.extend({},a),f);(e||n._data(this,"finish"))&&b.stop(!0)};return g.finish=g,e||f.queue===!1?this.each(g):this.queue(f.queue,g)},stop:function(a,b,c){var d=function(a){var b=a.stop;delete a.stop,b(c)};return"string"!=typeof a&&(c=b,b=a,a=void 0),b&&a!==!1&&this.queue(a||"fx",[]),this.each(function(){var b=!0,e=null!=a&&a+"queueHooks",f=n.timers,g=n._data(this);if(e)g[e]&&g[e].stop&&d(g[e]);else for(e in g)g[e]&&g[e].stop&&kb.test(e)&&d(g[e]);for(e=f.length;e--;)f[e].elem!==this||null!=a&&f[e].queue!==a||(f[e].anim.stop(c),b=!1,f.splice(e,1));!b&&c||n.dequeue(this,a)})},finish:function(a){return a!==!1&&(a=a||"fx"),this.each(function(){var b,c=n._data(this),d=c[a+"queue"],e=c[a+"queueHooks"],f=n.timers,g=d?d.length:0;for(c.finish=!0,n.queue(this,a,[]),e&&e.stop&&e.stop.call(this,!0),b=f.length;b--;)f[b].elem===this&&f[b].queue===a&&(f[b].anim.stop(!0),f.splice(b,1));for(b=0;g>b;b++)d[b]&&d[b].finish&&d[b].finish.call(this);delete c.finish})}}),n.each(["toggle","show","hide"],function(a,b){var c=n.fn[b];n.fn[b]=function(a,d,e){return null==a||"boolean"==typeof a?c.apply(this,arguments):this.animate(mb(b,!0),a,d,e)}}),n.each({slideDown:mb("show"),slideUp:mb("hide"),slideToggle:mb("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(a,b){n.fn[a]=function(a,c,d){return this.animate(b,a,c,d)}}),n.timers=[],n.fx.tick=function(){var a,b=n.timers,c=0;for(hb=n.now();c<b.length;c++)a=b[c],a()||b[c]!==a||b.splice(c--,1);b.length||n.fx.stop(),hb=void 0},n.fx.timer=function(a){n.timers.push(a),a()?n.fx.start():n.timers.pop()},n.fx.interval=13,n.fx.start=function(){ib||(ib=a.setInterval(n.fx.tick,n.fx.interval))},n.fx.stop=function(){a.clearInterval(ib),ib=null},n.fx.speeds={slow:600,fast:200,_default:400},n.fn.delay=function(b,c){return b=n.fx?n.fx.speeds[b]||b:b,c=c||"fx",this.queue(c,function(c,d){var e=a.setTimeout(c,b);d.stop=function(){a.clearTimeout(e)}})},function(){var a,b=d.createElement("input"),c=d.createElement("div"),e=d.createElement("select"),f=e.appendChild(d.createElement("option"));c=d.createElement("div"),c.setAttribute("className","t"),c.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",a=c.getElementsByTagName("a")[0],b.setAttribute("type","checkbox"),c.appendChild(b),a=c.getElementsByTagName("a")[0],a.style.cssText="top:1px",l.getSetAttribute="t"!==c.className,l.style=/top/.test(a.getAttribute("style")),l.hrefNormalized="/a"===a.getAttribute("href"),l.checkOn=!!b.value,l.optSelected=f.selected,l.enctype=!!d.createElement("form").enctype,e.disabled=!0,l.optDisabled=!f.disabled,b=d.createElement("input"),b.setAttribute("value",""),l.input=""===b.getAttribute("value"),b.value="t",b.setAttribute("type","radio"),l.radioValue="t"===b.value}();var rb=/\r/g,sb=/[\x20\t\r\n\f]+/g;n.fn.extend({val:function(a){var b,c,d,e=this[0];{if(arguments.length)return d=n.isFunction(a),this.each(function(c){var e;1===this.nodeType&&(e=d?a.call(this,c,n(this).val()):a,null==e?e="":"number"==typeof e?e+="":n.isArray(e)&&(e=n.map(e,function(a){return null==a?"":a+""})),b=n.valHooks[this.type]||n.valHooks[this.nodeName.toLowerCase()],b&&"set"in b&&void 0!==b.set(this,e,"value")||(this.value=e))});if(e)return b=n.valHooks[e.type]||n.valHooks[e.nodeName.toLowerCase()],b&&"get"in b&&void 0!==(c=b.get(e,"value"))?c:(c=e.value,"string"==typeof c?c.replace(rb,""):null==c?"":c)}}}),n.extend({valHooks:{option:{get:function(a){var b=n.find.attr(a,"value");return null!=b?b:n.trim(n.text(a)).replace(sb," ")}},select:{get:function(a){for(var b,c,d=a.options,e=a.selectedIndex,f="select-one"===a.type||0>e,g=f?null:[],h=f?e+1:d.length,i=0>e?h:f?e:0;h>i;i++)if(c=d[i],(c.selected||i===e)&&(l.optDisabled?!c.disabled:null===c.getAttribute("disabled"))&&(!c.parentNode.disabled||!n.nodeName(c.parentNode,"optgroup"))){if(b=n(c).val(),f)return b;g.push(b)}return g},set:function(a,b){var c,d,e=a.options,f=n.makeArray(b),g=e.length;while(g--)if(d=e[g],n.inArray(n.valHooks.option.get(d),f)>-1)try{d.selected=c=!0}catch(h){d.scrollHeight}else d.selected=!1;return c||(a.selectedIndex=-1),e}}}}),n.each(["radio","checkbox"],function(){n.valHooks[this]={set:function(a,b){return n.isArray(b)?a.checked=n.inArray(n(a).val(),b)>-1:void 0}},l.checkOn||(n.valHooks[this].get=function(a){return null===a.getAttribute("value")?"on":a.value})});var tb,ub,vb=n.expr.attrHandle,wb=/^(?:checked|selected)$/i,xb=l.getSetAttribute,yb=l.input;n.fn.extend({attr:function(a,b){return Y(this,n.attr,a,b,arguments.length>1)},removeAttr:function(a){return this.each(function(){n.removeAttr(this,a)})}}),n.extend({attr:function(a,b,c){var d,e,f=a.nodeType;if(3!==f&&8!==f&&2!==f)return"undefined"==typeof a.getAttribute?n.prop(a,b,c):(1===f&&n.isXMLDoc(a)||(b=b.toLowerCase(),e=n.attrHooks[b]||(n.expr.match.bool.test(b)?ub:tb)),void 0!==c?null===c?void n.removeAttr(a,b):e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:(a.setAttribute(b,c+""),c):e&&"get"in e&&null!==(d=e.get(a,b))?d:(d=n.find.attr(a,b),null==d?void 0:d))},attrHooks:{type:{set:function(a,b){if(!l.radioValue&&"radio"===b&&n.nodeName(a,"input")){var c=a.value;return a.setAttribute("type",b),c&&(a.value=c),b}}}},removeAttr:function(a,b){var c,d,e=0,f=b&&b.match(G);if(f&&1===a.nodeType)while(c=f[e++])d=n.propFix[c]||c,n.expr.match.bool.test(c)?yb&&xb||!wb.test(c)?a[d]=!1:a[n.camelCase("default-"+c)]=a[d]=!1:n.attr(a,c,""),a.removeAttribute(xb?c:d)}}),ub={set:function(a,b,c){return b===!1?n.removeAttr(a,c):yb&&xb||!wb.test(c)?a.setAttribute(!xb&&n.propFix[c]||c,c):a[n.camelCase("default-"+c)]=a[c]=!0,c}},n.each(n.expr.match.bool.source.match(/\w+/g),function(a,b){var c=vb[b]||n.find.attr;yb&&xb||!wb.test(b)?vb[b]=function(a,b,d){var e,f;return d||(f=vb[b],vb[b]=e,e=null!=c(a,b,d)?b.toLowerCase():null,vb[b]=f),e}:vb[b]=function(a,b,c){return c?void 0:a[n.camelCase("default-"+b)]?b.toLowerCase():null}}),yb&&xb||(n.attrHooks.value={set:function(a,b,c){return n.nodeName(a,"input")?void(a.defaultValue=b):tb&&tb.set(a,b,c)}}),xb||(tb={set:function(a,b,c){var d=a.getAttributeNode(c);return d||a.setAttributeNode(d=a.ownerDocument.createAttribute(c)),d.value=b+="","value"===c||b===a.getAttribute(c)?b:void 0}},vb.id=vb.name=vb.coords=function(a,b,c){var d;return c?void 0:(d=a.getAttributeNode(b))&&""!==d.value?d.value:null},n.valHooks.button={get:function(a,b){var c=a.getAttributeNode(b);return c&&c.specified?c.value:void 0},set:tb.set},n.attrHooks.contenteditable={set:function(a,b,c){tb.set(a,""===b?!1:b,c)}},n.each(["width","height"],function(a,b){n.attrHooks[b]={set:function(a,c){return""===c?(a.setAttribute(b,"auto"),c):void 0}}})),l.style||(n.attrHooks.style={get:function(a){return a.style.cssText||void 0},set:function(a,b){return a.style.cssText=b+""}});var zb=/^(?:input|select|textarea|button|object)$/i,Ab=/^(?:a|area)$/i;n.fn.extend({prop:function(a,b){return Y(this,n.prop,a,b,arguments.length>1)},removeProp:function(a){return a=n.propFix[a]||a,this.each(function(){try{this[a]=void 0,delete this[a]}catch(b){}})}}),n.extend({prop:function(a,b,c){var d,e,f=a.nodeType;if(3!==f&&8!==f&&2!==f)return 1===f&&n.isXMLDoc(a)||(b=n.propFix[b]||b,e=n.propHooks[b]),void 0!==c?e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:a[b]=c:e&&"get"in e&&null!==(d=e.get(a,b))?d:a[b]},propHooks:{tabIndex:{get:function(a){var b=n.find.attr(a,"tabindex");return b?parseInt(b,10):zb.test(a.nodeName)||Ab.test(a.nodeName)&&a.href?0:-1}}},propFix:{"for":"htmlFor","class":"className"}}),l.hrefNormalized||n.each(["href","src"],function(a,b){n.propHooks[b]={get:function(a){return a.getAttribute(b,4)}}}),l.optSelected||(n.propHooks.selected={get:function(a){var b=a.parentNode;return b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex),null},set:function(a){var b=a.parentNode;b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex)}}),n.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){n.propFix[this.toLowerCase()]=this}),l.enctype||(n.propFix.enctype="encoding");var Bb=/[\t\r\n\f]/g;function Cb(a){return n.attr(a,"class")||""}n.fn.extend({addClass:function(a){var b,c,d,e,f,g,h,i=0;if(n.isFunction(a))return this.each(function(b){n(this).addClass(a.call(this,b,Cb(this)))});if("string"==typeof a&&a){b=a.match(G)||[];while(c=this[i++])if(e=Cb(c),d=1===c.nodeType&&(" "+e+" ").replace(Bb," ")){g=0;while(f=b[g++])d.indexOf(" "+f+" ")<0&&(d+=f+" ");h=n.trim(d),e!==h&&n.attr(c,"class",h)}}return this},removeClass:function(a){var b,c,d,e,f,g,h,i=0;if(n.isFunction(a))return this.each(function(b){n(this).removeClass(a.call(this,b,Cb(this)))});if(!arguments.length)return this.attr("class","");if("string"==typeof a&&a){b=a.match(G)||[];while(c=this[i++])if(e=Cb(c),d=1===c.nodeType&&(" "+e+" ").replace(Bb," ")){g=0;while(f=b[g++])while(d.indexOf(" "+f+" ")>-1)d=d.replace(" "+f+" "," ");h=n.trim(d),e!==h&&n.attr(c,"class",h)}}return this},toggleClass:function(a,b){var c=typeof a;return"boolean"==typeof b&&"string"===c?b?this.addClass(a):this.removeClass(a):n.isFunction(a)?this.each(function(c){n(this).toggleClass(a.call(this,c,Cb(this),b),b)}):this.each(function(){var b,d,e,f;if("string"===c){d=0,e=n(this),f=a.match(G)||[];while(b=f[d++])e.hasClass(b)?e.removeClass(b):e.addClass(b)}else void 0!==a&&"boolean"!==c||(b=Cb(this),b&&n._data(this,"__className__",b),n.attr(this,"class",b||a===!1?"":n._data(this,"__className__")||""))})},hasClass:function(a){var b,c,d=0;b=" "+a+" ";while(c=this[d++])if(1===c.nodeType&&(" "+Cb(c)+" ").replace(Bb," ").indexOf(b)>-1)return!0;return!1}}),n.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(a,b){n.fn[b]=function(a,c){return arguments.length>0?this.on(b,null,a,c):this.trigger(b)}}),n.fn.extend({hover:function(a,b){return this.mouseenter(a).mouseleave(b||a)}});var Db=a.location,Eb=n.now(),Fb=/\?/,Gb=/(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;n.parseJSON=function(b){if(a.JSON&&a.JSON.parse)return a.JSON.parse(b+"");var c,d=null,e=n.trim(b+"");return e&&!n.trim(e.replace(Gb,function(a,b,e,f){return c&&b&&(d=0),0===d?a:(c=e||b,d+=!f-!e,"")}))?Function("return "+e)():n.error("Invalid JSON: "+b)},n.parseXML=function(b){var c,d;if(!b||"string"!=typeof b)return null;try{a.DOMParser?(d=new a.DOMParser,c=d.parseFromString(b,"text/xml")):(c=new a.ActiveXObject("Microsoft.XMLDOM"),c.async="false",c.loadXML(b))}catch(e){c=void 0}return c&&c.documentElement&&!c.getElementsByTagName("parsererror").length||n.error("Invalid XML: "+b),c};var Hb=/#.*$/,Ib=/([?&])_=[^&]*/,Jb=/^(.*?):[ \t]*([^\r\n]*)\r?$/gm,Kb=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Lb=/^(?:GET|HEAD)$/,Mb=/^\/\//,Nb=/^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,Ob={},Pb={},Qb="*/".concat("*"),Rb=Db.href,Sb=Nb.exec(Rb.toLowerCase())||[];function Tb(a){return function(b,c){"string"!=typeof b&&(c=b,b="*");var d,e=0,f=b.toLowerCase().match(G)||[];if(n.isFunction(c))while(d=f[e++])"+"===d.charAt(0)?(d=d.slice(1)||"*",(a[d]=a[d]||[]).unshift(c)):(a[d]=a[d]||[]).push(c)}}function Ub(a,b,c,d){var e={},f=a===Pb;function g(h){var i;return e[h]=!0,n.each(a[h]||[],function(a,h){var j=h(b,c,d);return"string"!=typeof j||f||e[j]?f?!(i=j):void 0:(b.dataTypes.unshift(j),g(j),!1)}),i}return g(b.dataTypes[0])||!e["*"]&&g("*")}function Vb(a,b){var c,d,e=n.ajaxSettings.flatOptions||{};for(d in b)void 0!==b[d]&&((e[d]?a:c||(c={}))[d]=b[d]);return c&&n.extend(!0,a,c),a}function Wb(a,b,c){var d,e,f,g,h=a.contents,i=a.dataTypes;while("*"===i[0])i.shift(),void 0===e&&(e=a.mimeType||b.getResponseHeader("Content-Type"));if(e)for(g in h)if(h[g]&&h[g].test(e)){i.unshift(g);break}if(i[0]in c)f=i[0];else{for(g in c){if(!i[0]||a.converters[g+" "+i[0]]){f=g;break}d||(d=g)}f=f||d}return f?(f!==i[0]&&i.unshift(f),c[f]):void 0}function Xb(a,b,c,d){var e,f,g,h,i,j={},k=a.dataTypes.slice();if(k[1])for(g in a.converters)j[g.toLowerCase()]=a.converters[g];f=k.shift();while(f)if(a.responseFields[f]&&(c[a.responseFields[f]]=b),!i&&d&&a.dataFilter&&(b=a.dataFilter(b,a.dataType)),i=f,f=k.shift())if("*"===f)f=i;else if("*"!==i&&i!==f){if(g=j[i+" "+f]||j["* "+f],!g)for(e in j)if(h=e.split(" "),h[1]===f&&(g=j[i+" "+h[0]]||j["* "+h[0]])){g===!0?g=j[e]:j[e]!==!0&&(f=h[0],k.unshift(h[1]));break}if(g!==!0)if(g&&a["throws"])b=g(b);else try{b=g(b)}catch(l){return{state:"parsererror",error:g?l:"No conversion from "+i+" to "+f}}}return{state:"success",data:b}}n.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Rb,type:"GET",isLocal:Kb.test(Sb[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":Qb,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/\bxml\b/,html:/\bhtml/,json:/\bjson\b/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":n.parseJSON,"text xml":n.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(a,b){return b?Vb(Vb(a,n.ajaxSettings),b):Vb(n.ajaxSettings,a)},ajaxPrefilter:Tb(Ob),ajaxTransport:Tb(Pb),ajax:function(b,c){"object"==typeof b&&(c=b,b=void 0),c=c||{};var d,e,f,g,h,i,j,k,l=n.ajaxSetup({},c),m=l.context||l,o=l.context&&(m.nodeType||m.jquery)?n(m):n.event,p=n.Deferred(),q=n.Callbacks("once memory"),r=l.statusCode||{},s={},t={},u=0,v="canceled",w={readyState:0,getResponseHeader:function(a){var b;if(2===u){if(!k){k={};while(b=Jb.exec(g))k[b[1].toLowerCase()]=b[2]}b=k[a.toLowerCase()]}return null==b?null:b},getAllResponseHeaders:function(){return 2===u?g:null},setRequestHeader:function(a,b){var c=a.toLowerCase();return u||(a=t[c]=t[c]||a,s[a]=b),this},overrideMimeType:function(a){return u||(l.mimeType=a),this},statusCode:function(a){var b;if(a)if(2>u)for(b in a)r[b]=[r[b],a[b]];else w.always(a[w.status]);return this},abort:function(a){var b=a||v;return j&&j.abort(b),y(0,b),this}};if(p.promise(w).complete=q.add,w.success=w.done,w.error=w.fail,l.url=((b||l.url||Rb)+"").replace(Hb,"").replace(Mb,Sb[1]+"//"),l.type=c.method||c.type||l.method||l.type,l.dataTypes=n.trim(l.dataType||"*").toLowerCase().match(G)||[""],null==l.crossDomain&&(d=Nb.exec(l.url.toLowerCase()),l.crossDomain=!(!d||d[1]===Sb[1]&&d[2]===Sb[2]&&(d[3]||("http:"===d[1]?"80":"443"))===(Sb[3]||("http:"===Sb[1]?"80":"443")))),l.data&&l.processData&&"string"!=typeof l.data&&(l.data=n.param(l.data,l.traditional)),Ub(Ob,l,c,w),2===u)return w;i=n.event&&l.global,i&&0===n.active++&&n.event.trigger("ajaxStart"),l.type=l.type.toUpperCase(),l.hasContent=!Lb.test(l.type),f=l.url,l.hasContent||(l.data&&(f=l.url+=(Fb.test(f)?"&":"?")+l.data,delete l.data),l.cache===!1&&(l.url=Ib.test(f)?f.replace(Ib,"$1_="+Eb++):f+(Fb.test(f)?"&":"?")+"_="+Eb++)),l.ifModified&&(n.lastModified[f]&&w.setRequestHeader("If-Modified-Since",n.lastModified[f]),n.etag[f]&&w.setRequestHeader("If-None-Match",n.etag[f])),(l.data&&l.hasContent&&l.contentType!==!1||c.contentType)&&w.setRequestHeader("Content-Type",l.contentType),w.setRequestHeader("Accept",l.dataTypes[0]&&l.accepts[l.dataTypes[0]]?l.accepts[l.dataTypes[0]]+("*"!==l.dataTypes[0]?", "+Qb+"; q=0.01":""):l.accepts["*"]);for(e in l.headers)w.setRequestHeader(e,l.headers[e]);if(l.beforeSend&&(l.beforeSend.call(m,w,l)===!1||2===u))return w.abort();v="abort";for(e in{success:1,error:1,complete:1})w[e](l[e]);if(j=Ub(Pb,l,c,w)){if(w.readyState=1,i&&o.trigger("ajaxSend",[w,l]),2===u)return w;l.async&&l.timeout>0&&(h=a.setTimeout(function(){w.abort("timeout")},l.timeout));try{u=1,j.send(s,y)}catch(x){if(!(2>u))throw x;y(-1,x)}}else y(-1,"No Transport");function y(b,c,d,e){var k,s,t,v,x,y=c;2!==u&&(u=2,h&&a.clearTimeout(h),j=void 0,g=e||"",w.readyState=b>0?4:0,k=b>=200&&300>b||304===b,d&&(v=Wb(l,w,d)),v=Xb(l,v,w,k),k?(l.ifModified&&(x=w.getResponseHeader("Last-Modified"),x&&(n.lastModified[f]=x),x=w.getResponseHeader("etag"),x&&(n.etag[f]=x)),204===b||"HEAD"===l.type?y="nocontent":304===b?y="notmodified":(y=v.state,s=v.data,t=v.error,k=!t)):(t=y,!b&&y||(y="error",0>b&&(b=0))),w.status=b,w.statusText=(c||y)+"",k?p.resolveWith(m,[s,y,w]):p.rejectWith(m,[w,y,t]),w.statusCode(r),r=void 0,i&&o.trigger(k?"ajaxSuccess":"ajaxError",[w,l,k?s:t]),q.fireWith(m,[w,y]),i&&(o.trigger("ajaxComplete",[w,l]),--n.active||n.event.trigger("ajaxStop")))}return w},getJSON:function(a,b,c){return n.get(a,b,c,"json")},getScript:function(a,b){return n.get(a,void 0,b,"script")}}),n.each(["get","post"],function(a,b){n[b]=function(a,c,d,e){return n.isFunction(c)&&(e=e||d,d=c,c=void 0),n.ajax(n.extend({url:a,type:b,dataType:e,data:c,success:d},n.isPlainObject(a)&&a))}}),n._evalUrl=function(a){return n.ajax({url:a,type:"GET",dataType:"script",cache:!0,async:!1,global:!1,"throws":!0})},n.fn.extend({wrapAll:function(a){if(n.isFunction(a))return this.each(function(b){n(this).wrapAll(a.call(this,b))});if(this[0]){var b=n(a,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&b.insertBefore(this[0]),b.map(function(){var a=this;while(a.firstChild&&1===a.firstChild.nodeType)a=a.firstChild;return a}).append(this)}return this},wrapInner:function(a){return n.isFunction(a)?this.each(function(b){n(this).wrapInner(a.call(this,b))}):this.each(function(){var b=n(this),c=b.contents();c.length?c.wrapAll(a):b.append(a)})},wrap:function(a){var b=n.isFunction(a);return this.each(function(c){n(this).wrapAll(b?a.call(this,c):a)})},unwrap:function(){return this.parent().each(function(){n.nodeName(this,"body")||n(this).replaceWith(this.childNodes)}).end()}});function Yb(a){return a.style&&a.style.display||n.css(a,"display")}function Zb(a){if(!n.contains(a.ownerDocument||d,a))return!0;while(a&&1===a.nodeType){if("none"===Yb(a)||"hidden"===a.type)return!0;a=a.parentNode}return!1}n.expr.filters.hidden=function(a){return l.reliableHiddenOffsets()?a.offsetWidth<=0&&a.offsetHeight<=0&&!a.getClientRects().length:Zb(a)},n.expr.filters.visible=function(a){return!n.expr.filters.hidden(a)};var $b=/%20/g,_b=/\[\]$/,ac=/\r?\n/g,bc=/^(?:submit|button|image|reset|file)$/i,cc=/^(?:input|select|textarea|keygen)/i;function dc(a,b,c,d){var e;if(n.isArray(b))n.each(b,function(b,e){c||_b.test(a)?d(a,e):dc(a+"["+("object"==typeof e&&null!=e?b:"")+"]",e,c,d)});else if(c||"object"!==n.type(b))d(a,b);else for(e in b)dc(a+"["+e+"]",b[e],c,d)}n.param=function(a,b){var c,d=[],e=function(a,b){b=n.isFunction(b)?b():null==b?"":b,d[d.length]=encodeURIComponent(a)+"="+encodeURIComponent(b)};if(void 0===b&&(b=n.ajaxSettings&&n.ajaxSettings.traditional),n.isArray(a)||a.jquery&&!n.isPlainObject(a))n.each(a,function(){e(this.name,this.value)});else for(c in a)dc(c,a[c],b,e);return d.join("&").replace($b,"+")},n.fn.extend({serialize:function(){return n.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var a=n.prop(this,"elements");return a?n.makeArray(a):this}).filter(function(){var a=this.type;return this.name&&!n(this).is(":disabled")&&cc.test(this.nodeName)&&!bc.test(a)&&(this.checked||!Z.test(a))}).map(function(a,b){var c=n(this).val();return null==c?null:n.isArray(c)?n.map(c,function(a){return{name:b.name,value:a.replace(ac,"\r\n")}}):{name:b.name,value:c.replace(ac,"\r\n")}}).get()}}),n.ajaxSettings.xhr=void 0!==a.ActiveXObject?function(){return this.isLocal?ic():d.documentMode>8?hc():/^(get|post|head|put|delete|options)$/i.test(this.type)&&hc()||ic()}:hc;var ec=0,fc={},gc=n.ajaxSettings.xhr();a.attachEvent&&a.attachEvent("onunload",function(){for(var a in fc)fc[a](void 0,!0)}),l.cors=!!gc&&"withCredentials"in gc,gc=l.ajax=!!gc,gc&&n.ajaxTransport(function(b){if(!b.crossDomain||l.cors){var c;return{send:function(d,e){var f,g=b.xhr(),h=++ec;if(g.open(b.type,b.url,b.async,b.username,b.password),b.xhrFields)for(f in b.xhrFields)g[f]=b.xhrFields[f];b.mimeType&&g.overrideMimeType&&g.overrideMimeType(b.mimeType),b.crossDomain||d["X-Requested-With"]||(d["X-Requested-With"]="XMLHttpRequest");for(f in d)void 0!==d[f]&&g.setRequestHeader(f,d[f]+"");g.send(b.hasContent&&b.data||null),c=function(a,d){var f,i,j;if(c&&(d||4===g.readyState))if(delete fc[h],c=void 0,g.onreadystatechange=n.noop,d)4!==g.readyState&&g.abort();else{j={},f=g.status,"string"==typeof g.responseText&&(j.text=g.responseText);try{i=g.statusText}catch(k){i=""}f||!b.isLocal||b.crossDomain?1223===f&&(f=204):f=j.text?200:404}j&&e(f,i,j,g.getAllResponseHeaders())},b.async?4===g.readyState?a.setTimeout(c):g.onreadystatechange=fc[h]=c:c()},abort:function(){c&&c(void 0,!0)}}}});function hc(){try{return new a.XMLHttpRequest}catch(b){}}function ic(){try{return new a.ActiveXObject("Microsoft.XMLHTTP")}catch(b){}}n.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function(a){return n.globalEval(a),a}}}),n.ajaxPrefilter("script",function(a){void 0===a.cache&&(a.cache=!1),a.crossDomain&&(a.type="GET",a.global=!1)}),n.ajaxTransport("script",function(a){if(a.crossDomain){var b,c=d.head||n("head")[0]||d.documentElement;return{send:function(e,f){b=d.createElement("script"),b.async=!0,a.scriptCharset&&(b.charset=a.scriptCharset),b.src=a.url,b.onload=b.onreadystatechange=function(a,c){(c||!b.readyState||/loaded|complete/.test(b.readyState))&&(b.onload=b.onreadystatechange=null,b.parentNode&&b.parentNode.removeChild(b),b=null,c||f(200,"success"))},c.insertBefore(b,c.firstChild)},abort:function(){b&&b.onload(void 0,!0)}}}});var jc=[],kc=/(=)\?(?=&|$)|\?\?/;n.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var a=jc.pop()||n.expando+"_"+Eb++;return this[a]=!0,a}}),n.ajaxPrefilter("json jsonp",function(b,c,d){var e,f,g,h=b.jsonp!==!1&&(kc.test(b.url)?"url":"string"==typeof b.data&&0===(b.contentType||"").indexOf("application/x-www-form-urlencoded")&&kc.test(b.data)&&"data");return h||"jsonp"===b.dataTypes[0]?(e=b.jsonpCallback=n.isFunction(b.jsonpCallback)?b.jsonpCallback():b.jsonpCallback,h?b[h]=b[h].replace(kc,"$1"+e):b.jsonp!==!1&&(b.url+=(Fb.test(b.url)?"&":"?")+b.jsonp+"="+e),b.converters["script json"]=function(){return g||n.error(e+" was not called"),g[0]},b.dataTypes[0]="json",f=a[e],a[e]=function(){g=arguments},d.always(function(){void 0===f?n(a).removeProp(e):a[e]=f,b[e]&&(b.jsonpCallback=c.jsonpCallback,jc.push(e)),g&&n.isFunction(f)&&f(g[0]),g=f=void 0}),"script"):void 0}),n.parseHTML=function(a,b,c){if(!a||"string"!=typeof a)return null;"boolean"==typeof b&&(c=b,b=!1),b=b||d;var e=x.exec(a),f=!c&&[];return e?[b.createElement(e[1])]:(e=ja([a],b,f),f&&f.length&&n(f).remove(),n.merge([],e.childNodes))};var lc=n.fn.load;n.fn.load=function(a,b,c){if("string"!=typeof a&&lc)return lc.apply(this,arguments);var d,e,f,g=this,h=a.indexOf(" ");return h>-1&&(d=n.trim(a.slice(h,a.length)),a=a.slice(0,h)),n.isFunction(b)?(c=b,b=void 0):b&&"object"==typeof b&&(e="POST"),g.length>0&&n.ajax({url:a,type:e||"GET",dataType:"html",data:b}).done(function(a){f=arguments,g.html(d?n("<div>").append(n.parseHTML(a)).find(d):a)}).always(c&&function(a,b){g.each(function(){c.apply(this,f||[a.responseText,b,a])})}),this},n.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(a,b){n.fn[b]=function(a){return this.on(b,a)}}),n.expr.filters.animated=function(a){return n.grep(n.timers,function(b){return a===b.elem}).length};function mc(a){return n.isWindow(a)?a:9===a.nodeType?a.defaultView||a.parentWindow:!1}n.offset={setOffset:function(a,b,c){var d,e,f,g,h,i,j,k=n.css(a,"position"),l=n(a),m={};"static"===k&&(a.style.position="relative"),h=l.offset(),f=n.css(a,"top"),i=n.css(a,"left"),j=("absolute"===k||"fixed"===k)&&n.inArray("auto",[f,i])>-1,j?(d=l.position(),g=d.top,e=d.left):(g=parseFloat(f)||0,e=parseFloat(i)||0),n.isFunction(b)&&(b=b.call(a,c,n.extend({},h))),null!=b.top&&(m.top=b.top-h.top+g),null!=b.left&&(m.left=b.left-h.left+e),"using"in b?b.using.call(a,m):l.css(m)}},n.fn.extend({offset:function(a){if(arguments.length)return void 0===a?this:this.each(function(b){n.offset.setOffset(this,a,b)});var b,c,d={top:0,left:0},e=this[0],f=e&&e.ownerDocument;if(f)return b=f.documentElement,n.contains(b,e)?("undefined"!=typeof e.getBoundingClientRect&&(d=e.getBoundingClientRect()),c=mc(f),{top:d.top+(c.pageYOffset||b.scrollTop)-(b.clientTop||0),left:d.left+(c.pageXOffset||b.scrollLeft)-(b.clientLeft||0)}):d},position:function(){if(this[0]){var a,b,c={top:0,left:0},d=this[0];return"fixed"===n.css(d,"position")?b=d.getBoundingClientRect():(a=this.offsetParent(),b=this.offset(),n.nodeName(a[0],"html")||(c=a.offset()),c.top+=n.css(a[0],"borderTopWidth",!0),c.left+=n.css(a[0],"borderLeftWidth",!0)),{top:b.top-c.top-n.css(d,"marginTop",!0),left:b.left-c.left-n.css(d,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var a=this.offsetParent;while(a&&!n.nodeName(a,"html")&&"static"===n.css(a,"position"))a=a.offsetParent;return a||Qa})}}),n.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(a,b){var c=/Y/.test(b);n.fn[a]=function(d){return Y(this,function(a,d,e){var f=mc(a);return void 0===e?f?b in f?f[b]:f.document.documentElement[d]:a[d]:void(f?f.scrollTo(c?n(f).scrollLeft():e,c?e:n(f).scrollTop()):a[d]=e)},a,d,arguments.length,null)}}),n.each(["top","left"],function(a,b){n.cssHooks[b]=Ua(l.pixelPosition,function(a,c){return c?(c=Sa(a,b),Oa.test(c)?n(a).position()[b]+"px":c):void 0})}),n.each({Height:"height",Width:"width"},function(a,b){n.each({
padding:"inner"+a,content:b,"":"outer"+a},function(c,d){n.fn[d]=function(d,e){var f=arguments.length&&(c||"boolean"!=typeof d),g=c||(d===!0||e===!0?"margin":"border");return Y(this,function(b,c,d){var e;return n.isWindow(b)?b.document.documentElement["client"+a]:9===b.nodeType?(e=b.documentElement,Math.max(b.body["scroll"+a],e["scroll"+a],b.body["offset"+a],e["offset"+a],e["client"+a])):void 0===d?n.css(b,c,g):n.style(b,c,d,g)},b,f?d:void 0,f,null)}})}),n.fn.extend({bind:function(a,b,c){return this.on(a,null,b,c)},unbind:function(a,b){return this.off(a,null,b)},delegate:function(a,b,c,d){return this.on(b,a,c,d)},undelegate:function(a,b,c){return 1===arguments.length?this.off(a,"**"):this.off(b,a||"**",c)}}),n.fn.size=function(){return this.length},n.fn.andSelf=n.fn.addBack,"function"==typeof define&&define.amd&&define("jquery",[],function(){return n});var nc=a.jQuery,oc=a.$;return n.noConflict=function(b){return a.$===n&&(a.$=oc),b&&a.jQuery===n&&(a.jQuery=nc),n},b||(a.jQuery=a.$=n),n});
var DateFormatter;!function(){"use strict";var e,t,a,r,n,o,i;o=864e5,i=3600,e=function(e,t){return"string"==typeof e&&"string"==typeof t&&e.toLowerCase()===t.toLowerCase()},t=function(e,a,r){var n=r||"0",o=e.toString();return o.length<a?t(n+o,a):o},a=function(e){var t,r;for(e=e||{},t=1;t<arguments.length;t++)if(r=arguments[t])for(var n in r)r.hasOwnProperty(n)&&("object"==typeof r[n]?a(e[n],r[n]):e[n]=r[n]);return e},r=function(e,t){for(var a=0;a<t.length;a++)if(t[a].toLowerCase()===e.toLowerCase())return a;return-1},n={dateSettings:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],meridiem:["AM","PM"],ordinal:function(e){var t=e%10,a={1:"st",2:"nd",3:"rd"};return 1!==Math.floor(e%100/10)&&a[t]?a[t]:"th"}},separators:/[ \-+\/\.T:@]/g,validParts:/[dDjlNSwzWFmMntLoYyaABgGhHisueTIOPZcrU]/g,intParts:/[djwNzmnyYhHgGis]/g,tzParts:/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,tzClip:/[^-+\dA-Z]/g},(DateFormatter=function(e){var t=this,r=a(n,e);t.dateSettings=r.dateSettings,t.separators=r.separators,t.validParts=r.validParts,t.intParts=r.intParts,t.tzParts=r.tzParts,t.tzClip=r.tzClip}).prototype={constructor:DateFormatter,getMonth:function(e){var t,a=this;return 0===(t=r(e,a.dateSettings.monthsShort)+1)&&(t=r(e,a.dateSettings.months)+1),t},parseDate:function(t,a){var r,n,o,i,s,u,d,l,f,c,m=this,h=!1,g=!1,p=m.dateSettings,D={date:null,year:null,month:null,day:null,hour:0,min:0,sec:0};if(!t)return null;if(t instanceof Date)return t;if("U"===a)return(o=parseInt(t))?new Date(1e3*o):t;switch(typeof t){case"number":return new Date(t);case"string":break;default:return null}if(!(r=a.match(m.validParts))||0===r.length)throw new Error("Invalid date format definition.");for(n=t.replace(m.separators,"\0").split("\0"),o=0;o<n.length;o++)switch(i=n[o],s=parseInt(i),r[o]){case"y":case"Y":if(!s)return null;f=i.length,D.year=2===f?parseInt((70>s?"20":"19")+i):s,h=!0;break;case"m":case"n":case"M":case"F":if(isNaN(s)){if(!((u=m.getMonth(i))>0))return null;D.month=u}else{if(!(s>=1&&12>=s))return null;D.month=s}h=!0;break;case"d":case"j":if(!(s>=1&&31>=s))return null;D.day=s,h=!0;break;case"g":case"h":if(d=r.indexOf("a")>-1?r.indexOf("a"):r.indexOf("A")>-1?r.indexOf("A"):-1,c=n[d],d>-1)l=e(c,p.meridiem[0])?0:e(c,p.meridiem[1])?12:-1,s>=1&&12>=s&&l>-1?D.hour=s+l-1:s>=0&&23>=s&&(D.hour=s);else{if(!(s>=0&&23>=s))return null;D.hour=s}g=!0;break;case"G":case"H":if(!(s>=0&&23>=s))return null;D.hour=s,g=!0;break;case"i":if(!(s>=0&&59>=s))return null;D.min=s,g=!0;break;case"s":if(!(s>=0&&59>=s))return null;D.sec=s,g=!0}if(!0===h&&D.year&&D.month&&D.day)D.date=new Date(D.year,D.month-1,D.day,D.hour,D.min,D.sec,0);else{if(!0!==g)return null;D.date=new Date(0,0,0,D.hour,D.min,D.sec,0)}return D.date},guessDate:function(e,t){if("string"!=typeof e)return e;var a,r,n,o,i,s,u=this,d=e.replace(u.separators,"\0").split("\0"),l=/^[djmn]/g,f=t.match(u.validParts),c=new Date,m=0;if(!l.test(f[0]))return e;for(n=0;n<d.length;n++){if(m=2,i=d[n],s=parseInt(i.substr(0,2)),isNaN(s))return null;switch(n){case 0:"m"===f[0]||"n"===f[0]?c.setMonth(s-1):c.setDate(s);break;case 1:"m"===f[0]||"n"===f[0]?c.setDate(s):c.setMonth(s-1);break;case 2:if(r=c.getFullYear(),a=i.length,m=4>a?a:4,!(r=parseInt(4>a?r.toString().substr(0,4-a)+i:i.substr(0,4))))return null;c.setFullYear(r);break;case 3:c.setHours(s);break;case 4:c.setMinutes(s);break;case 5:c.setSeconds(s)}(o=i.substr(m)).length>0&&d.splice(n+1,0,o)}return c},parseFormat:function(e,a){var r,n=this,s=n.dateSettings,u=/\\?(.?)/gi,d=function(e,t){return r[e]?r[e]():t};return r={d:function(){return t(r.j(),2)},D:function(){return s.daysShort[r.w()]},j:function(){return a.getDate()},l:function(){return s.days[r.w()]},N:function(){return r.w()||7},w:function(){return a.getDay()},z:function(){var e=new Date(r.Y(),r.n()-1,r.j()),t=new Date(r.Y(),0,1);return Math.round((e-t)/o)},W:function(){var e=new Date(r.Y(),r.n()-1,r.j()-r.N()+3),a=new Date(e.getFullYear(),0,4);return t(1+Math.round((e-a)/o/7),2)},F:function(){return s.months[a.getMonth()]},m:function(){return t(r.n(),2)},M:function(){return s.monthsShort[a.getMonth()]},n:function(){return a.getMonth()+1},t:function(){return new Date(r.Y(),r.n(),0).getDate()},L:function(){var e=r.Y();return e%4==0&&e%100!=0||e%400==0?1:0},o:function(){var e=r.n(),t=r.W();return r.Y()+(12===e&&9>t?1:1===e&&t>9?-1:0)},Y:function(){return a.getFullYear()},y:function(){return r.Y().toString().slice(-2)},a:function(){return r.A().toLowerCase()},A:function(){var e=r.G()<12?0:1;return s.meridiem[e]},B:function(){var e=a.getUTCHours()*i,r=60*a.getUTCMinutes(),n=a.getUTCSeconds();return t(Math.floor((e+r+n+i)/86.4)%1e3,3)},g:function(){return r.G()%12||12},G:function(){return a.getHours()},h:function(){return t(r.g(),2)},H:function(){return t(r.G(),2)},i:function(){return t(a.getMinutes(),2)},s:function(){return t(a.getSeconds(),2)},u:function(){return t(1e3*a.getMilliseconds(),6)},e:function(){return/\((.*)\)/.exec(String(a))[1]||"Coordinated Universal Time"},I:function(){return new Date(r.Y(),0)-Date.UTC(r.Y(),0)!=new Date(r.Y(),6)-Date.UTC(r.Y(),6)?1:0},O:function(){var e=a.getTimezoneOffset(),r=Math.abs(e);return(e>0?"-":"+")+t(100*Math.floor(r/60)+r%60,4)},P:function(){var e=r.O();return e.substr(0,3)+":"+e.substr(3,2)},T:function(){return(String(a).match(n.tzParts)||[""]).pop().replace(n.tzClip,"")||"UTC"},Z:function(){return 60*-a.getTimezoneOffset()},c:function(){return"Y-m-d\\TH:i:sP".replace(u,d)},r:function(){return"D, d M Y H:i:s O".replace(u,d)},U:function(){return a.getTime()/1e3||0}},d(e,e)},formatDate:function(e,t){var a,r,n,o,i,s=this,u="";if("string"==typeof e&&!(e=s.parseDate(e,t)))return null;if(e instanceof Date){for(n=t.length,a=0;n>a;a++)"S"!==(i=t.charAt(a))&&"\\"!==i&&(a>0&&"\\"===t.charAt(a-1)?u+=i:(o=s.parseFormat(i,e),a!==n-1&&s.intParts.test(i)&&"S"===t.charAt(a+1)&&(r=parseInt(o)||0,o+=s.dateSettings.ordinal(r)),u+=o));return u}return""}}}();var datetimepickerFactory=function(e){"use strict";function t(e,t,a){this.date=e,this.desc=t,this.style=a}var a={i18n:{ar:{months:["كانون الثاني","شباط","آذار","نيسان","مايو","حزيران","تموز","آب","أيلول","تشرين الأول","تشرين الثاني","كانون الأول"],dayOfWeekShort:["ن","ث","ع","خ","ج","س","ح"],dayOfWeek:["الأحد","الاثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت","الأحد"]},ro:{months:["Ianuarie","Februarie","Martie","Aprilie","Mai","Iunie","Iulie","August","Septembrie","Octombrie","Noiembrie","Decembrie"],dayOfWeekShort:["Du","Lu","Ma","Mi","Jo","Vi","Sâ"],dayOfWeek:["Duminică","Luni","Marţi","Miercuri","Joi","Vineri","Sâmbătă"]},id:{months:["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],dayOfWeekShort:["Min","Sen","Sel","Rab","Kam","Jum","Sab"],dayOfWeek:["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"]},is:{months:["Janúar","Febrúar","Mars","Apríl","Maí","Júní","Júlí","Ágúst","September","Október","Nóvember","Desember"],dayOfWeekShort:["Sun","Mán","Þrið","Mið","Fim","Fös","Lau"],dayOfWeek:["Sunnudagur","Mánudagur","Þriðjudagur","Miðvikudagur","Fimmtudagur","Föstudagur","Laugardagur"]},bg:{months:["Януари","Февруари","Март","Април","Май","Юни","Юли","Август","Септември","Октомври","Ноември","Декември"],dayOfWeekShort:["Нд","Пн","Вт","Ср","Чт","Пт","Сб"],dayOfWeek:["Неделя","Понеделник","Вторник","Сряда","Четвъртък","Петък","Събота"]},fa:{months:["فروردین","اردیبهشت","خرداد","تیر","مرداد","شهریور","مهر","آبان","آذر","دی","بهمن","اسفند"],dayOfWeekShort:["یکشنبه","دوشنبه","سه شنبه","چهارشنبه","پنجشنبه","جمعه","شنبه"],dayOfWeek:["یک‌شنبه","دوشنبه","سه‌شنبه","چهارشنبه","پنج‌شنبه","جمعه","شنبه","یک‌شنبه"]},ru:{months:["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],dayOfWeekShort:["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],dayOfWeek:["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"]},uk:{months:["Січень","Лютий","Березень","Квітень","Травень","Червень","Липень","Серпень","Вересень","Жовтень","Листопад","Грудень"],dayOfWeekShort:["Ндл","Пнд","Втр","Срд","Чтв","Птн","Сбт"],dayOfWeek:["Неділя","Понеділок","Вівторок","Середа","Четвер","П'ятниця","Субота"]},en:{months:["January","February","March","April","May","June","July","August","September","October","November","December"],dayOfWeekShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayOfWeek:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]},el:{months:["Ιανουάριος","Φεβρουάριος","Μάρτιος","Απρίλιος","Μάιος","Ιούνιος","Ιούλιος","Αύγουστος","Σεπτέμβριος","Οκτώβριος","Νοέμβριος","Δεκέμβριος"],dayOfWeekShort:["Κυρ","Δευ","Τρι","Τετ","Πεμ","Παρ","Σαβ"],dayOfWeek:["Κυριακή","Δευτέρα","Τρίτη","Τετάρτη","Πέμπτη","Παρασκευή","Σάββατο"]},de:{months:["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"],dayOfWeekShort:["So","Mo","Di","Mi","Do","Fr","Sa"],dayOfWeek:["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"]},nl:{months:["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],dayOfWeekShort:["zo","ma","di","wo","do","vr","za"],dayOfWeek:["zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag"]},tr:{months:["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"],dayOfWeekShort:["Paz","Pts","Sal","Çar","Per","Cum","Cts"],dayOfWeek:["Pazar","Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi"]},fr:{months:["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"],dayOfWeekShort:["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"],dayOfWeek:["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"]},es:{months:["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],dayOfWeekShort:["Dom","Lun","Mar","Mié","Jue","Vie","Sáb"],dayOfWeek:["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"]},th:{months:["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"],dayOfWeekShort:["อา.","จ.","อ.","พ.","พฤ.","ศ.","ส."],dayOfWeek:["อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัส","ศุกร์","เสาร์","อาทิตย์"]},pl:{months:["styczeń","luty","marzec","kwiecień","maj","czerwiec","lipiec","sierpień","wrzesień","październik","listopad","grudzień"],dayOfWeekShort:["nd","pn","wt","śr","cz","pt","sb"],dayOfWeek:["niedziela","poniedziałek","wtorek","środa","czwartek","piątek","sobota"]},pt:{months:["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],dayOfWeekShort:["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],dayOfWeek:["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"]},ch:{months:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],dayOfWeekShort:["日","一","二","三","四","五","六"]},se:{months:["Januari","Februari","Mars","April","Maj","Juni","Juli","Augusti","September","Oktober","November","December"],dayOfWeekShort:["Sön","Mån","Tis","Ons","Tor","Fre","Lör"]},km:{months:["មករា​","កុម្ភៈ","មិនា​","មេសា​","ឧសភា​","មិថុនា​","កក្កដា​","សីហា​","កញ្ញា​","តុលា​","វិច្ឆិកា","ធ្នូ​"],dayOfWeekShort:["អាទិ​","ច័ន្ទ​","អង្គារ​","ពុធ​","ព្រហ​​","សុក្រ​","សៅរ៍"],dayOfWeek:["អាទិត្យ​","ច័ន្ទ​","អង្គារ​","ពុធ​","ព្រហស្បតិ៍​","សុក្រ​","សៅរ៍"]},kr:{months:["1월","2월","3월","4월","5월","6월","7월","8월","9월","10월","11월","12월"],dayOfWeekShort:["일","월","화","수","목","금","토"],dayOfWeek:["일요일","월요일","화요일","수요일","목요일","금요일","토요일"]},it:{months:["Gennaio","Febbraio","Marzo","Aprile","Maggio","Giugno","Luglio","Agosto","Settembre","Ottobre","Novembre","Dicembre"],dayOfWeekShort:["Dom","Lun","Mar","Mer","Gio","Ven","Sab"],dayOfWeek:["Domenica","Lunedì","Martedì","Mercoledì","Giovedì","Venerdì","Sabato"]},da:{months:["Januar","Februar","Marts","April","Maj","Juni","Juli","August","September","Oktober","November","December"],dayOfWeekShort:["Søn","Man","Tir","Ons","Tor","Fre","Lør"],dayOfWeek:["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"]},no:{months:["Januar","Februar","Mars","April","Mai","Juni","Juli","August","September","Oktober","November","Desember"],dayOfWeekShort:["Søn","Man","Tir","Ons","Tor","Fre","Lør"],dayOfWeek:["Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag"]},ja:{months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],dayOfWeekShort:["日","月","火","水","木","金","土"],dayOfWeek:["日曜","月曜","火曜","水曜","木曜","金曜","土曜"]},vi:{months:["Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6","Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"],dayOfWeekShort:["CN","T2","T3","T4","T5","T6","T7"],dayOfWeek:["Chủ nhật","Thứ hai","Thứ ba","Thứ tư","Thứ năm","Thứ sáu","Thứ bảy"]},sl:{months:["Januar","Februar","Marec","April","Maj","Junij","Julij","Avgust","September","Oktober","November","December"],dayOfWeekShort:["Ned","Pon","Tor","Sre","Čet","Pet","Sob"],dayOfWeek:["Nedelja","Ponedeljek","Torek","Sreda","Četrtek","Petek","Sobota"]},cs:{months:["Leden","Únor","Březen","Duben","Květen","Červen","Červenec","Srpen","Září","Říjen","Listopad","Prosinec"],dayOfWeekShort:["Ne","Po","Út","St","Čt","Pá","So"]},hu:{months:["Január","Február","Március","Április","Május","Június","Július","Augusztus","Szeptember","Október","November","December"],dayOfWeekShort:["Va","Hé","Ke","Sze","Cs","Pé","Szo"],dayOfWeek:["vasárnap","hétfő","kedd","szerda","csütörtök","péntek","szombat"]},az:{months:["Yanvar","Fevral","Mart","Aprel","May","Iyun","Iyul","Avqust","Sentyabr","Oktyabr","Noyabr","Dekabr"],dayOfWeekShort:["B","Be","Ça","Ç","Ca","C","Ş"],dayOfWeek:["Bazar","Bazar ertəsi","Çərşənbə axşamı","Çərşənbə","Cümə axşamı","Cümə","Şənbə"]},bs:{months:["Januar","Februar","Mart","April","Maj","Jun","Jul","Avgust","Septembar","Oktobar","Novembar","Decembar"],dayOfWeekShort:["Ned","Pon","Uto","Sri","Čet","Pet","Sub"],dayOfWeek:["Nedjelja","Ponedjeljak","Utorak","Srijeda","Četvrtak","Petak","Subota"]},ca:{months:["Gener","Febrer","Març","Abril","Maig","Juny","Juliol","Agost","Setembre","Octubre","Novembre","Desembre"],dayOfWeekShort:["Dg","Dl","Dt","Dc","Dj","Dv","Ds"],dayOfWeek:["Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte"]},"en-GB":{months:["January","February","March","April","May","June","July","August","September","October","November","December"],dayOfWeekShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayOfWeek:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]},et:{months:["Jaanuar","Veebruar","Märts","Aprill","Mai","Juuni","Juuli","August","September","Oktoober","November","Detsember"],dayOfWeekShort:["P","E","T","K","N","R","L"],dayOfWeek:["Pühapäev","Esmaspäev","Teisipäev","Kolmapäev","Neljapäev","Reede","Laupäev"]},eu:{months:["Urtarrila","Otsaila","Martxoa","Apirila","Maiatza","Ekaina","Uztaila","Abuztua","Iraila","Urria","Azaroa","Abendua"],dayOfWeekShort:["Ig.","Al.","Ar.","Az.","Og.","Or.","La."],dayOfWeek:["Igandea","Astelehena","Asteartea","Asteazkena","Osteguna","Ostirala","Larunbata"]},fi:{months:["Tammikuu","Helmikuu","Maaliskuu","Huhtikuu","Toukokuu","Kesäkuu","Heinäkuu","Elokuu","Syyskuu","Lokakuu","Marraskuu","Joulukuu"],dayOfWeekShort:["Su","Ma","Ti","Ke","To","Pe","La"],dayOfWeek:["sunnuntai","maanantai","tiistai","keskiviikko","torstai","perjantai","lauantai"]},gl:{months:["Xan","Feb","Maz","Abr","Mai","Xun","Xul","Ago","Set","Out","Nov","Dec"],dayOfWeekShort:["Dom","Lun","Mar","Mer","Xov","Ven","Sab"],dayOfWeek:["Domingo","Luns","Martes","Mércores","Xoves","Venres","Sábado"]},hr:{months:["Siječanj","Veljača","Ožujak","Travanj","Svibanj","Lipanj","Srpanj","Kolovoz","Rujan","Listopad","Studeni","Prosinac"],dayOfWeekShort:["Ned","Pon","Uto","Sri","Čet","Pet","Sub"],dayOfWeek:["Nedjelja","Ponedjeljak","Utorak","Srijeda","Četvrtak","Petak","Subota"]},ko:{months:["1월","2월","3월","4월","5월","6월","7월","8월","9월","10월","11월","12월"],dayOfWeekShort:["일","월","화","수","목","금","토"],dayOfWeek:["일요일","월요일","화요일","수요일","목요일","금요일","토요일"]},lt:{months:["Sausio","Vasario","Kovo","Balandžio","Gegužės","Birželio","Liepos","Rugpjūčio","Rugsėjo","Spalio","Lapkričio","Gruodžio"],dayOfWeekShort:["Sek","Pir","Ant","Tre","Ket","Pen","Šeš"],dayOfWeek:["Sekmadienis","Pirmadienis","Antradienis","Trečiadienis","Ketvirtadienis","Penktadienis","Šeštadienis"]},lv:{months:["Janvāris","Februāris","Marts","Aprīlis ","Maijs","Jūnijs","Jūlijs","Augusts","Septembris","Oktobris","Novembris","Decembris"],dayOfWeekShort:["Sv","Pr","Ot","Tr","Ct","Pk","St"],dayOfWeek:["Svētdiena","Pirmdiena","Otrdiena","Trešdiena","Ceturtdiena","Piektdiena","Sestdiena"]},mk:{months:["јануари","февруари","март","април","мај","јуни","јули","август","септември","октомври","ноември","декември"],dayOfWeekShort:["нед","пон","вто","сре","чет","пет","саб"],dayOfWeek:["Недела","Понеделник","Вторник","Среда","Четврток","Петок","Сабота"]},mn:{months:["1-р сар","2-р сар","3-р сар","4-р сар","5-р сар","6-р сар","7-р сар","8-р сар","9-р сар","10-р сар","11-р сар","12-р сар"],dayOfWeekShort:["Дав","Мяг","Лха","Пүр","Бсн","Бям","Ням"],dayOfWeek:["Даваа","Мягмар","Лхагва","Пүрэв","Баасан","Бямба","Ням"]},"pt-BR":{months:["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],dayOfWeekShort:["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],dayOfWeek:["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"]},sk:{months:["Január","Február","Marec","Apríl","Máj","Jún","Júl","August","September","Október","November","December"],dayOfWeekShort:["Ne","Po","Ut","St","Št","Pi","So"],dayOfWeek:["Nedeľa","Pondelok","Utorok","Streda","Štvrtok","Piatok","Sobota"]},sq:{months:["Janar","Shkurt","Mars","Prill","Maj","Qershor","Korrik","Gusht","Shtator","Tetor","Nëntor","Dhjetor"],dayOfWeekShort:["Die","Hën","Mar","Mër","Enj","Pre","Shtu"],dayOfWeek:["E Diel","E Hënë","E Martē","E Mërkurë","E Enjte","E Premte","E Shtunë"]},"sr-YU":{months:["Januar","Februar","Mart","April","Maj","Jun","Jul","Avgust","Septembar","Oktobar","Novembar","Decembar"],dayOfWeekShort:["Ned","Pon","Uto","Sre","čet","Pet","Sub"],dayOfWeek:["Nedelja","Ponedeljak","Utorak","Sreda","Četvrtak","Petak","Subota"]},sr:{months:["јануар","фебруар","март","април","мај","јун","јул","август","септембар","октобар","новембар","децембар"],dayOfWeekShort:["нед","пон","уто","сре","чет","пет","суб"],dayOfWeek:["Недеља","Понедељак","Уторак","Среда","Четвртак","Петак","Субота"]},sv:{months:["Januari","Februari","Mars","April","Maj","Juni","Juli","Augusti","September","Oktober","November","December"],dayOfWeekShort:["Sön","Mån","Tis","Ons","Tor","Fre","Lör"],dayOfWeek:["Söndag","Måndag","Tisdag","Onsdag","Torsdag","Fredag","Lördag"]},"zh-TW":{months:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],dayOfWeekShort:["日","一","二","三","四","五","六"],dayOfWeek:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"]},zh:{months:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],dayOfWeekShort:["日","一","二","三","四","五","六"],dayOfWeek:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"]},ug:{months:["1-ئاي","2-ئاي","3-ئاي","4-ئاي","5-ئاي","6-ئاي","7-ئاي","8-ئاي","9-ئاي","10-ئاي","11-ئاي","12-ئاي"],dayOfWeek:["يەكشەنبە","دۈشەنبە","سەيشەنبە","چارشەنبە","پەيشەنبە","جۈمە","شەنبە"]},he:{months:["ינואר","פברואר","מרץ","אפריל","מאי","יוני","יולי","אוגוסט","ספטמבר","אוקטובר","נובמבר","דצמבר"],dayOfWeekShort:["א'","ב'","ג'","ד'","ה'","ו'","שבת"],dayOfWeek:["ראשון","שני","שלישי","רביעי","חמישי","שישי","שבת","ראשון"]},hy:{months:["Հունվար","Փետրվար","Մարտ","Ապրիլ","Մայիս","Հունիս","Հուլիս","Օգոստոս","Սեպտեմբեր","Հոկտեմբեր","Նոյեմբեր","Դեկտեմբեր"],dayOfWeekShort:["Կի","Երկ","Երք","Չոր","Հնգ","Ուրբ","Շբթ"],dayOfWeek:["Կիրակի","Երկուշաբթի","Երեքշաբթի","Չորեքշաբթի","Հինգշաբթի","Ուրբաթ","Շաբաթ"]},kg:{months:["Үчтүн айы","Бирдин айы","Жалган Куран","Чын Куран","Бугу","Кулжа","Теке","Баш Оона","Аяк Оона","Тогуздун айы","Жетинин айы","Бештин айы"],dayOfWeekShort:["Жек","Дүй","Шей","Шар","Бей","Жум","Ише"],dayOfWeek:["Жекшемб","Дүйшөмб","Шейшемб","Шаршемб","Бейшемби","Жума","Ишенб"]},rm:{months:["Schaner","Favrer","Mars","Avrigl","Matg","Zercladur","Fanadur","Avust","Settember","October","November","December"],dayOfWeekShort:["Du","Gli","Ma","Me","Gie","Ve","So"],dayOfWeek:["Dumengia","Glindesdi","Mardi","Mesemna","Gievgia","Venderdi","Sonda"]},ka:{months:["იანვარი","თებერვალი","მარტი","აპრილი","მაისი","ივნისი","ივლისი","აგვისტო","სექტემბერი","ოქტომბერი","ნოემბერი","დეკემბერი"],dayOfWeekShort:["კვ","ორშ","სამშ","ოთხ","ხუთ","პარ","შაბ"],dayOfWeek:["კვირა","ორშაბათი","სამშაბათი","ოთხშაბათი","ხუთშაბათი","პარასკევი","შაბათი"]}},ownerDocument:document,contentWindow:window,value:"",rtl:!1,format:"Y/m/d H:i",formatTime:"H:i",formatDate:"Y/m/d",startDate:!1,step:60,monthChangeSpinner:!0,closeOnDateSelect:!1,closeOnTimeSelect:!0,closeOnWithoutClick:!0,closeOnInputClick:!0,openOnFocus:!0,timepicker:!0,datepicker:!0,weeks:!1,defaultTime:!1,defaultDate:!1,minDate:!1,maxDate:!1,minTime:!1,maxTime:!1,minDateTime:!1,maxDateTime:!1,allowTimes:[],opened:!1,initTime:!0,inline:!1,theme:"",touchMovedThreshold:5,onSelectDate:function(){},onSelectTime:function(){},onChangeMonth:function(){},onGetWeekOfYear:function(){},onChangeYear:function(){},onChangeDateTime:function(){},onShow:function(){},onClose:function(){},onGenerate:function(){},withoutCopyright:!0,inverseButton:!1,hours12:!1,next:"xdsoft_next",prev:"xdsoft_prev",dayOfWeekStart:0,parentID:"body",timeHeightInTimePicker:25,timepickerScrollbar:!0,todayButton:!0,prevButton:!0,nextButton:!0,defaultSelect:!0,scrollMonth:!0,scrollTime:!0,scrollInput:!0,lazyInit:!1,mask:!1,validateOnBlur:!0,allowBlank:!0,yearStart:1900,yearEnd:2050,monthStart:0,monthEnd:11,style:"",id:"",fixed:!1,roundTime:"round",className:"",weekends:[],highlightedDates:[],highlightedPeriods:[],allowDates:[],allowDateRe:null,disabledDates:[],disabledWeekDays:[],yearOffset:0,beforeShowDay:null,enterLikeTab:!0,showApplyButton:!1},r=null,n=null,o="en",i={meridiem:["AM","PM"]},s=function(){var t=a.i18n[o],s={days:t.dayOfWeek,daysShort:t.dayOfWeekShort,months:t.months,monthsShort:e.map(t.months,function(e){return e.substring(0,3)})};"function"==typeof DateFormatter&&(r=n=new DateFormatter({dateSettings:e.extend({},i,s)}))},u={moment:{default_options:{format:"YYYY/MM/DD HH:mm",formatDate:"YYYY/MM/DD",formatTime:"HH:mm"},formatter:{parseDate:function(e,t){if(l(t))return n.parseDate(e,t);var a=moment(e,t);return!!a.isValid()&&a.toDate()},formatDate:function(e,t){return l(t)?n.formatDate(e,t):moment(e).format(t)},formatMask:function(e){return e.replace(/Y{4}/g,"9999").replace(/Y{2}/g,"99").replace(/M{2}/g,"19").replace(/D{2}/g,"39").replace(/H{2}/g,"29").replace(/m{2}/g,"59").replace(/s{2}/g,"59")}}}};e.datetimepicker={setLocale:function(e){var t=a.i18n[e]?e:"en";o!==t&&(o=t,s())},setDateFormatter:function(t){if("string"==typeof t&&u.hasOwnProperty(t)){var n=u[t];e.extend(a,n.default_options),r=n.formatter}else r=t}};var d={RFC_2822:"D, d M Y H:i:s O",ATOM:"Y-m-dTH:i:sP",ISO_8601:"Y-m-dTH:i:sO",RFC_822:"D, d M y H:i:s O",RFC_850:"l, d-M-y H:i:s T",RFC_1036:"D, d M y H:i:s O",RFC_1123:"D, d M Y H:i:s O",RSS:"D, d M Y H:i:s O",W3C:"Y-m-dTH:i:sP"},l=function(e){return-1!==Object.values(d).indexOf(e)};e.extend(e.datetimepicker,d),s(),window.getComputedStyle||(window.getComputedStyle=function(e){return this.el=e,this.getPropertyValue=function(t){var a=/(-([a-z]))/g;return"float"===t&&(t="styleFloat"),a.test(t)&&(t=t.replace(a,function(e,t,a){return a.toUpperCase()})),e.currentStyle[t]||null},this}),Array.prototype.indexOf||(Array.prototype.indexOf=function(e,t){var a,r;for(a=t||0,r=this.length;a<r;a+=1)if(this[a]===e)return a;return-1}),Date.prototype.countDaysInMonth=function(){return new Date(this.getFullYear(),this.getMonth()+1,0).getDate()},e.fn.xdsoftScroller=function(t,a){return this.each(function(){var r,n,o,i,s,u=e(this),d=function(e){var t,a={x:0,y:0};return"touchstart"===e.type||"touchmove"===e.type||"touchend"===e.type||"touchcancel"===e.type?(t=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0],a.x=t.clientX,a.y=t.clientY):"mousedown"!==e.type&&"mouseup"!==e.type&&"mousemove"!==e.type&&"mouseover"!==e.type&&"mouseout"!==e.type&&"mouseenter"!==e.type&&"mouseleave"!==e.type||(a.x=e.clientX,a.y=e.clientY),a},l=100,f=!1,c=0,m=0,h=0,g=!1,p=0,D=function(){};"hide"!==a?(e(this).hasClass("xdsoft_scroller_box")||(r=u.children().eq(0),n=u[0].clientHeight,o=r[0].offsetHeight,i=e('<div class="xdsoft_scrollbar"></div>'),s=e('<div class="xdsoft_scroller"></div>'),i.append(s),u.addClass("xdsoft_scroller_box").append(i),D=function(e){var t=d(e).y-c+p;t<0&&(t=0),t+s[0].offsetHeight>h&&(t=h-s[0].offsetHeight),u.trigger("scroll_element.xdsoft_scroller",[l?t/l:0])},s.on("touchstart.xdsoft_scroller mousedown.xdsoft_scroller",function(r){n||u.trigger("resize_scroll.xdsoft_scroller",[a]),c=d(r).y,p=parseInt(s.css("margin-top"),10),h=i[0].offsetHeight,"mousedown"===r.type||"touchstart"===r.type?(t.ownerDocument&&e(t.ownerDocument.body).addClass("xdsoft_noselect"),e([t.ownerDocument.body,t.contentWindow]).on("touchend mouseup.xdsoft_scroller",function a(){e([t.ownerDocument.body,t.contentWindow]).off("touchend mouseup.xdsoft_scroller",a).off("mousemove.xdsoft_scroller",D).removeClass("xdsoft_noselect")}),e(t.ownerDocument.body).on("mousemove.xdsoft_scroller",D)):(g=!0,r.stopPropagation(),r.preventDefault())}).on("touchmove",function(e){g&&(e.preventDefault(),D(e))}).on("touchend touchcancel",function(){g=!1,p=0}),u.on("scroll_element.xdsoft_scroller",function(e,t){n||u.trigger("resize_scroll.xdsoft_scroller",[t,!0]),t=t>1?1:t<0||isNaN(t)?0:t,s.css("margin-top",l*t),setTimeout(function(){r.css("marginTop",-parseInt((r[0].offsetHeight-n)*t,10))},10)}).on("resize_scroll.xdsoft_scroller",function(e,t,a){var d,f;n=u[0].clientHeight,o=r[0].offsetHeight,f=(d=n/o)*i[0].offsetHeight,d>1?s.hide():(s.show(),s.css("height",parseInt(f>10?f:10,10)),l=i[0].offsetHeight-s[0].offsetHeight,!0!==a&&u.trigger("scroll_element.xdsoft_scroller",[t||Math.abs(parseInt(r.css("marginTop"),10))/(o-n)]))}),u.on("mousewheel",function(e){var t=Math.abs(parseInt(r.css("marginTop"),10));return(t-=20*e.deltaY)<0&&(t=0),u.trigger("scroll_element.xdsoft_scroller",[t/(o-n)]),e.stopPropagation(),!1}),u.on("touchstart",function(e){f=d(e),m=Math.abs(parseInt(r.css("marginTop"),10))}),u.on("touchmove",function(e){if(f){e.preventDefault();var t=d(e);u.trigger("scroll_element.xdsoft_scroller",[(m-(t.y-f.y))/(o-n)])}}),u.on("touchend touchcancel",function(){f=!1,m=0})),u.trigger("resize_scroll.xdsoft_scroller",[a])):u.find(".xdsoft_scrollbar").hide()})},e.fn.datetimepicker=function(n,i){var s,u,d=this,l=48,f=57,c=96,m=105,h=17,g=46,p=13,D=27,v=8,y=37,b=38,k=39,x=40,T=9,S=116,M=65,w=67,O=86,W=90,_=89,F=!1,C=e.isPlainObject(n)||!n?e.extend(!0,{},a,n):e.extend(!0,{},a),P=0,Y=function(e){e.on("open.xdsoft focusin.xdsoft mousedown.xdsoft touchstart",function t(){e.is(":disabled")||e.data("xdsoft_datetimepicker")||(clearTimeout(P),P=setTimeout(function(){e.data("xdsoft_datetimepicker")||s(e),e.off("open.xdsoft focusin.xdsoft mousedown.xdsoft touchstart",t).trigger("open.xdsoft")},100))})};return s=function(a){function i(){var e,t=!1;return C.startDate?t=A.strToDate(C.startDate):(t=C.value||(a&&a.val&&a.val()?a.val():""))?(t=A.strToDateTime(t),C.yearOffset&&(t=new Date(t.getFullYear()-C.yearOffset,t.getMonth(),t.getDate(),t.getHours(),t.getMinutes(),t.getSeconds(),t.getMilliseconds()))):C.defaultDate&&(t=A.strToDateTime(C.defaultDate),C.defaultTime&&(e=A.strtotime(C.defaultTime),t.setHours(e.getHours()),t.setMinutes(e.getMinutes()))),t&&A.isValidDate(t)?j.data("changed",!0):t="",t||0}function s(t){var n=function(e,t){var a=e.replace(/([\[\]\/\{\}\(\)\-\.\+]{1})/g,"\\$1").replace(/_/g,"{digit+}").replace(/([0-9]{1})/g,"{digit$1}").replace(/\{digit([0-9]{1})\}/g,"[0-$1_]{1}").replace(/\{digit[\+]\}/g,"[0-9_]{1}");return new RegExp(a).test(t)},o=function(e,a){if(!(e="string"==typeof e||e instanceof String?t.ownerDocument.getElementById(e):e))return!1;if(e.createTextRange){var r=e.createTextRange();return r.collapse(!0),r.moveEnd("character",a),r.moveStart("character",a),r.select(),!0}return!!e.setSelectionRange&&(e.setSelectionRange(a,a),!0)};t.mask&&a.off("keydown.xdsoft"),!0===t.mask&&(r.formatMask?t.mask=r.formatMask(t.format):t.mask=t.format.replace(/Y/g,"9999").replace(/F/g,"9999").replace(/m/g,"19").replace(/d/g,"39").replace(/H/g,"29").replace(/i/g,"59").replace(/s/g,"59")),"string"===e.type(t.mask)&&(n(t.mask,a.val())||(a.val(t.mask.replace(/[0-9]/g,"_")),o(a[0],0)),a.on("paste.xdsoft",function(r){var i=(r.clipboardData||r.originalEvent.clipboardData||window.clipboardData).getData("text"),s=this.value,u=this.selectionStart;return s=s.substr(0,u)+i+s.substr(u+i.length),u+=i.length,n(t.mask,s)?(this.value=s,o(this,u)):""===e.trim(s)?this.value=t.mask.replace(/[0-9]/g,"_"):a.trigger("error_input.xdsoft"),r.preventDefault(),!1}),a.on("keydown.xdsoft",function(r){var i,s=this.value,u=r.which,d=this.selectionStart,C=this.selectionEnd,P=d!==C;if(u>=l&&u<=f||u>=c&&u<=m||u===v||u===g){for(i=u===v||u===g?"_":String.fromCharCode(c<=u&&u<=m?u-l:u),u===v&&d&&!P&&(d-=1);;){var Y=t.mask.substr(d,1),A=d<t.mask.length,H=d>0;if(!(/[^0-9_]/.test(Y)&&A&&H))break;d+=u!==v||P?1:-1}if(P){var j=C-d,J=t.mask.replace(/[0-9]/g,"_"),z=J.substr(d,j).substr(1);s=s.substr(0,d)+(i+z)+s.substr(d+j)}else s=s.substr(0,d)+i+s.substr(d+1);if(""===e.trim(s))s=J;else if(d===t.mask.length)return r.preventDefault(),!1;for(d+=u===v?0:1;/[^0-9_]/.test(t.mask.substr(d,1))&&d<t.mask.length&&d>0;)d+=u===v?0:1;n(t.mask,s)?(this.value=s,o(this,d)):""===e.trim(s)?this.value=t.mask.replace(/[0-9]/g,"_"):a.trigger("error_input.xdsoft")}else if(-1!==[M,w,O,W,_].indexOf(u)&&F||-1!==[D,b,x,y,k,S,h,T,p].indexOf(u))return!0;return r.preventDefault(),!1}))}var u,d,P,Y,A,H,j=e('<div class="xdsoft_datetimepicker xdsoft_noselect"></div>'),J=e('<div class="xdsoft_copyright"><a target="_blank" href="http://xdsoft.net/jqplugins/datetimepicker/">xdsoft.net</a></div>'),z=e('<div class="xdsoft_datepicker active"></div>'),I=e('<div class="xdsoft_monthpicker"><button type="button" class="xdsoft_prev"></button><button type="button" class="xdsoft_today_button"></button><div class="xdsoft_label xdsoft_month"><span></span><i></i></div><div class="xdsoft_label xdsoft_year"><span></span><i></i></div><button type="button" class="xdsoft_next"></button></div>'),N=e('<div class="xdsoft_calendar"></div>'),L=e('<div class="xdsoft_timepicker active"><button type="button" class="xdsoft_prev"></button><div class="xdsoft_time_box"></div><button type="button" class="xdsoft_next"></button></div>'),E=L.find(".xdsoft_time_box").eq(0),R=e('<div class="xdsoft_time_variant"></div>'),V=e('<button type="button" class="xdsoft_save_selected blue-gradient-button">Save Selected</button>'),B=e('<div class="xdsoft_select xdsoft_monthselect"><div></div></div>'),G=e('<div class="xdsoft_select xdsoft_yearselect"><div></div></div>'),U=!1,q=0;C.id&&j.attr("id",C.id),C.style&&j.attr("style",C.style),C.weeks&&j.addClass("xdsoft_showweeks"),C.rtl&&j.addClass("xdsoft_rtl"),j.addClass("xdsoft_"+C.theme),j.addClass(C.className),I.find(".xdsoft_month span").after(B),I.find(".xdsoft_year span").after(G),I.find(".xdsoft_month,.xdsoft_year").on("touchstart mousedown.xdsoft",function(t){var a,r,n=e(this).find(".xdsoft_select").eq(0),o=0,i=0,s=n.is(":visible");for(I.find(".xdsoft_select").hide(),A.currentTime&&(o=A.currentTime[e(this).hasClass("xdsoft_month")?"getMonth":"getFullYear"]()),n[s?"hide":"show"](),a=n.find("div.xdsoft_option"),r=0;r<a.length&&a.eq(r).data("value")!==o;r+=1)i+=a[0].offsetHeight;return n.xdsoftScroller(C,i/(n.children()[0].offsetHeight-n[0].clientHeight)),t.stopPropagation(),!1});var X=function(e){var t=e.originalEvent,a=t.touches?t.touches[0]:t;this.touchStartPosition=this.touchStartPosition||a;var r=Math.abs(this.touchStartPosition.clientX-a.clientX),n=Math.abs(this.touchStartPosition.clientY-a.clientY);Math.sqrt(r*r+n*n)>C.touchMovedThreshold&&(this.touchMoved=!0)};I.find(".xdsoft_select").xdsoftScroller(C).on("touchstart mousedown.xdsoft",function(e){try{var t=e.originalEvent;this.touchMoved=!1,this.touchStartPosition=t.touches?t.touches[0]:t,e.stopPropagation(),e.preventDefault()}catch(e){}}).on("touchmove",".xdsoft_option",X).on("touchend mousedown.xdsoft",".xdsoft_option",function(){if(!this.touchMoved){void 0!==A.currentTime&&null!==A.currentTime||(A.currentTime=A.now());var t=A.currentTime.getFullYear();A&&A.currentTime&&A.currentTime[e(this).parent().parent().hasClass("xdsoft_monthselect")?"setMonth":"setFullYear"](e(this).data("value")),e(this).parent().parent().hide(),j.trigger("xchange.xdsoft"),C.onChangeMonth&&e.isFunction(C.onChangeMonth)&&C.onChangeMonth.call(j,A.currentTime,j.data("input")),t!==A.currentTime.getFullYear()&&e.isFunction(C.onChangeYear)&&C.onChangeYear.call(j,A.currentTime,j.data("input"))}}),j.getValue=function(){return A.getCurrentTime()},j.setOptions=function(n){var o={};C=e.extend(!0,{},C,n),n.allowTimes&&e.isArray(n.allowTimes)&&n.allowTimes.length&&(C.allowTimes=e.extend(!0,[],n.allowTimes)),n.weekends&&e.isArray(n.weekends)&&n.weekends.length&&(C.weekends=e.extend(!0,[],n.weekends)),n.allowDates&&e.isArray(n.allowDates)&&n.allowDates.length&&(C.allowDates=e.extend(!0,[],n.allowDates)),n.allowDateRe&&"[object String]"===Object.prototype.toString.call(n.allowDateRe)&&(C.allowDateRe=new RegExp(n.allowDateRe)),n.highlightedDates&&e.isArray(n.highlightedDates)&&n.highlightedDates.length&&(e.each(n.highlightedDates,function(a,n){var i,s=e.map(n.split(","),e.trim),u=new t(r.parseDate(s[0],C.formatDate),s[1],s[2]),d=r.formatDate(u.date,C.formatDate);void 0!==o[d]?(i=o[d].desc)&&i.length&&u.desc&&u.desc.length&&(o[d].desc=i+"\n"+u.desc):o[d]=u}),C.highlightedDates=e.extend(!0,[],o)),n.highlightedPeriods&&e.isArray(n.highlightedPeriods)&&n.highlightedPeriods.length&&(o=e.extend(!0,[],C.highlightedDates),e.each(n.highlightedPeriods,function(a,n){var i,s,u,d,l,f,c;if(e.isArray(n))i=n[0],s=n[1],u=n[2],c=n[3];else{var m=e.map(n.split(","),e.trim);i=r.parseDate(m[0],C.formatDate),s=r.parseDate(m[1],C.formatDate),u=m[2],c=m[3]}for(;i<=s;)d=new t(i,u,c),l=r.formatDate(i,C.formatDate),i.setDate(i.getDate()+1),void 0!==o[l]?(f=o[l].desc)&&f.length&&d.desc&&d.desc.length&&(o[l].desc=f+"\n"+d.desc):o[l]=d}),C.highlightedDates=e.extend(!0,[],o)),n.disabledDates&&e.isArray(n.disabledDates)&&n.disabledDates.length&&(C.disabledDates=e.extend(!0,[],n.disabledDates)),n.disabledWeekDays&&e.isArray(n.disabledWeekDays)&&n.disabledWeekDays.length&&(C.disabledWeekDays=e.extend(!0,[],n.disabledWeekDays)),!C.open&&!C.opened||C.inline||a.trigger("open.xdsoft"),C.inline&&(U=!0,j.addClass("xdsoft_inline"),a.after(j).hide()),C.inverseButton&&(C.next="xdsoft_prev",C.prev="xdsoft_next"),C.datepicker?z.addClass("active"):z.removeClass("active"),C.timepicker?L.addClass("active"):L.removeClass("active"),C.value&&(A.setCurrentTime(C.value),a&&a.val&&a.val(A.str)),isNaN(C.dayOfWeekStart)?C.dayOfWeekStart=0:C.dayOfWeekStart=parseInt(C.dayOfWeekStart,10)%7,C.timepickerScrollbar||E.xdsoftScroller(C,"hide"),C.minDate&&/^[\+\-](.*)$/.test(C.minDate)&&(C.minDate=r.formatDate(A.strToDateTime(C.minDate),C.formatDate)),C.maxDate&&/^[\+\-](.*)$/.test(C.maxDate)&&(C.maxDate=r.formatDate(A.strToDateTime(C.maxDate),C.formatDate)),C.minDateTime&&/^\+(.*)$/.test(C.minDateTime)&&(C.minDateTime=A.strToDateTime(C.minDateTime).dateFormat(C.formatDate)),C.maxDateTime&&/^\+(.*)$/.test(C.maxDateTime)&&(C.maxDateTime=A.strToDateTime(C.maxDateTime).dateFormat(C.formatDate)),V.toggle(C.showApplyButton),I.find(".xdsoft_today_button").css("visibility",C.todayButton?"visible":"hidden"),I.find("."+C.prev).css("visibility",C.prevButton?"visible":"hidden"),I.find("."+C.next).css("visibility",C.nextButton?"visible":"hidden"),s(C),C.validateOnBlur&&a.off("blur.xdsoft").on("blur.xdsoft",function(){if(C.allowBlank&&(!e.trim(e(this).val()).length||"string"==typeof C.mask&&e.trim(e(this).val())===C.mask.replace(/[0-9]/g,"_")))e(this).val(null),j.data("xdsoft_datetime").empty();else{var t=r.parseDate(e(this).val(),C.format);if(t)e(this).val(r.formatDate(t,C.format));else{var a=+[e(this).val()[0],e(this).val()[1]].join(""),n=+[e(this).val()[2],e(this).val()[3]].join("");!C.datepicker&&C.timepicker&&a>=0&&a<24&&n>=0&&n<60?e(this).val([a,n].map(function(e){return e>9?e:"0"+e}).join(":")):e(this).val(r.formatDate(A.now(),C.format))}j.data("xdsoft_datetime").setCurrentTime(e(this).val())}j.trigger("changedatetime.xdsoft"),j.trigger("close.xdsoft")}),C.dayOfWeekStartPrev=0===C.dayOfWeekStart?6:C.dayOfWeekStart-1,j.trigger("xchange.xdsoft").trigger("afterOpen.xdsoft")},j.data("options",C).on("touchstart mousedown.xdsoft",function(e){return e.stopPropagation(),e.preventDefault(),G.hide(),B.hide(),!1}),E.append(R),E.xdsoftScroller(C),j.on("afterOpen.xdsoft",function(){E.xdsoftScroller(C)}),j.append(z).append(L),!0!==C.withoutCopyright&&j.append(J),z.append(I).append(N).append(V),e(C.parentID).append(j),A=new function(){var t=this;t.now=function(e){var a,r,n=new Date;return!e&&C.defaultDate&&(a=t.strToDateTime(C.defaultDate),n.setFullYear(a.getFullYear()),n.setMonth(a.getMonth()),n.setDate(a.getDate())),n.setFullYear(n.getFullYear()),!e&&C.defaultTime&&(r=t.strtotime(C.defaultTime),n.setHours(r.getHours()),n.setMinutes(r.getMinutes()),n.setSeconds(r.getSeconds()),n.setMilliseconds(r.getMilliseconds())),n},t.isValidDate=function(e){return"[object Date]"===Object.prototype.toString.call(e)&&!isNaN(e.getTime())},t.setCurrentTime=function(e,a){"string"==typeof e?t.currentTime=t.strToDateTime(e):t.isValidDate(e)?t.currentTime=e:e||a||!C.allowBlank||C.inline?t.currentTime=t.now():t.currentTime=null,j.trigger("xchange.xdsoft")},t.empty=function(){t.currentTime=null},t.getCurrentTime=function(){return t.currentTime},t.nextMonth=function(){void 0!==t.currentTime&&null!==t.currentTime||(t.currentTime=t.now());var a,r=t.currentTime.getMonth()+1;return 12===r&&(t.currentTime.setFullYear(t.currentTime.getFullYear()+1),r=0),a=t.currentTime.getFullYear(),t.currentTime.setDate(Math.min(new Date(t.currentTime.getFullYear(),r+1,0).getDate(),t.currentTime.getDate())),t.currentTime.setMonth(r),C.onChangeMonth&&e.isFunction(C.onChangeMonth)&&C.onChangeMonth.call(j,A.currentTime,j.data("input")),a!==t.currentTime.getFullYear()&&e.isFunction(C.onChangeYear)&&C.onChangeYear.call(j,A.currentTime,j.data("input")),j.trigger("xchange.xdsoft"),r},t.prevMonth=function(){void 0!==t.currentTime&&null!==t.currentTime||(t.currentTime=t.now());var a=t.currentTime.getMonth()-1;return-1===a&&(t.currentTime.setFullYear(t.currentTime.getFullYear()-1),a=11),t.currentTime.setDate(Math.min(new Date(t.currentTime.getFullYear(),a+1,0).getDate(),t.currentTime.getDate())),t.currentTime.setMonth(a),C.onChangeMonth&&e.isFunction(C.onChangeMonth)&&C.onChangeMonth.call(j,A.currentTime,j.data("input")),j.trigger("xchange.xdsoft"),a},t.getWeekOfYear=function(t){if(C.onGetWeekOfYear&&e.isFunction(C.onGetWeekOfYear)){var a=C.onGetWeekOfYear.call(j,t);if(void 0!==a)return a}var r=new Date(t.getFullYear(),0,1);return 4!==r.getDay()&&r.setMonth(0,1+(4-r.getDay()+7)%7),Math.ceil(((t-r)/864e5+r.getDay()+1)/7)},t.strToDateTime=function(e){var a,n,o=[];return e&&e instanceof Date&&t.isValidDate(e)?e:((o=/^([+-]{1})(.*)$/.exec(e))&&(o[2]=r.parseDate(o[2],C.formatDate)),o&&o[2]?(a=o[2].getTime()-6e4*o[2].getTimezoneOffset(),n=new Date(t.now(!0).getTime()+parseInt(o[1]+"1",10)*a)):n=e?r.parseDate(e,C.format):t.now(),t.isValidDate(n)||(n=t.now()),n)},t.strToDate=function(e){if(e&&e instanceof Date&&t.isValidDate(e))return e;var a=e?r.parseDate(e,C.formatDate):t.now(!0);return t.isValidDate(a)||(a=t.now(!0)),a},t.strtotime=function(e){if(e&&e instanceof Date&&t.isValidDate(e))return e;var a=e?r.parseDate(e,C.formatTime):t.now(!0);return t.isValidDate(a)||(a=t.now(!0)),a},t.str=function(){var e=C.format;return C.yearOffset&&(e=(e=e.replace("Y",t.currentTime.getFullYear()+C.yearOffset)).replace("y",String(t.currentTime.getFullYear()+C.yearOffset).substring(2,4))),r.formatDate(t.currentTime,e)},t.currentTime=this.now()},V.on("touchend click",function(e){e.preventDefault(),j.data("changed",!0),A.setCurrentTime(i()),a.val(A.str()),j.trigger("close.xdsoft")}),I.find(".xdsoft_today_button").on("touchend mousedown.xdsoft",function(){j.data("changed",!0),A.setCurrentTime(0,!0),j.trigger("afterOpen.xdsoft")}).on("dblclick.xdsoft",function(){var e,t,r=A.getCurrentTime();r=new Date(r.getFullYear(),r.getMonth(),r.getDate()),e=A.strToDate(C.minDate),r<(e=new Date(e.getFullYear(),e.getMonth(),e.getDate()))||(t=A.strToDate(C.maxDate),r>(t=new Date(t.getFullYear(),t.getMonth(),t.getDate()))||(a.val(A.str()),a.trigger("change"),j.trigger("close.xdsoft")))}),I.find(".xdsoft_prev,.xdsoft_next").on("touchend mousedown.xdsoft",function(){var t=e(this),a=0,r=!1;!function e(n){t.hasClass(C.next)?A.nextMonth():t.hasClass(C.prev)&&A.prevMonth(),C.monthChangeSpinner&&(r||(a=setTimeout(e,n||100)))}(500),e([C.ownerDocument.body,C.contentWindow]).on("touchend mouseup.xdsoft",function t(){clearTimeout(a),r=!0,e([C.ownerDocument.body,C.contentWindow]).off("touchend mouseup.xdsoft",t)})}),L.find(".xdsoft_prev,.xdsoft_next").on("touchend mousedown.xdsoft",function(){var t=e(this),a=0,r=!1,n=110;!function e(o){var i=E[0].clientHeight,s=R[0].offsetHeight,u=Math.abs(parseInt(R.css("marginTop"),10));t.hasClass(C.next)&&s-i-C.timeHeightInTimePicker>=u?R.css("marginTop","-"+(u+C.timeHeightInTimePicker)+"px"):t.hasClass(C.prev)&&u-C.timeHeightInTimePicker>=0&&R.css("marginTop","-"+(u-C.timeHeightInTimePicker)+"px"),E.trigger("scroll_element.xdsoft_scroller",[Math.abs(parseInt(R[0].style.marginTop,10)/(s-i))]),n=n>10?10:n-10,r||(a=setTimeout(e,o||n))}(500),e([C.ownerDocument.body,C.contentWindow]).on("touchend mouseup.xdsoft",function t(){clearTimeout(a),r=!0,e([C.ownerDocument.body,C.contentWindow]).off("touchend mouseup.xdsoft",t)})}),u=0,j.on("xchange.xdsoft",function(t){clearTimeout(u),u=setTimeout(function(){void 0!==A.currentTime&&null!==A.currentTime||(A.currentTime=A.now());for(var t,i,s,u,d,l,f,c,m,h,g="",p=new Date(A.currentTime.getFullYear(),A.currentTime.getMonth(),1,12,0,0),D=0,v=A.now(),y=!1,b=!1,k=!1,x=!1,T=[],S=!0,M="";p.getDay()!==C.dayOfWeekStart;)p.setDate(p.getDate()-1);for(g+="<table><thead><tr>",C.weeks&&(g+="<th></th>"),t=0;t<7;t+=1)g+="<th>"+C.i18n[o].dayOfWeekShort[(t+C.dayOfWeekStart)%7]+"</th>";g+="</tr></thead>",g+="<tbody>",!1!==C.maxDate&&(y=A.strToDate(C.maxDate),y=new Date(y.getFullYear(),y.getMonth(),y.getDate(),23,59,59,999)),!1!==C.minDate&&(b=A.strToDate(C.minDate),b=new Date(b.getFullYear(),b.getMonth(),b.getDate())),!1!==C.minDateTime&&(k=A.strToDate(C.minDateTime),k=new Date(k.getFullYear(),k.getMonth(),k.getDate(),k.getHours(),k.getMinutes(),k.getSeconds())),!1!==C.maxDateTime&&(x=A.strToDate(C.maxDateTime),x=new Date(x.getFullYear(),x.getMonth(),x.getDate(),x.getHours(),x.getMinutes(),x.getSeconds()));var w;for(!1!==x&&(w=31*(12*x.getFullYear()+x.getMonth())+x.getDate());D<A.currentTime.countDaysInMonth()||p.getDay()!==C.dayOfWeekStart||A.currentTime.getMonth()===p.getMonth();){T=[],D+=1,s=p.getDay(),u=p.getDate(),d=p.getFullYear(),l=p.getMonth(),f=A.getWeekOfYear(p),h="",T.push("xdsoft_date"),c=C.beforeShowDay&&e.isFunction(C.beforeShowDay.call)?C.beforeShowDay.call(j,p):null,C.allowDateRe&&"[object RegExp]"===Object.prototype.toString.call(C.allowDateRe)&&(C.allowDateRe.test(r.formatDate(p,C.formatDate))||T.push("xdsoft_disabled")),C.allowDates&&C.allowDates.length>0&&-1===C.allowDates.indexOf(r.formatDate(p,C.formatDate))&&T.push("xdsoft_disabled");var O=31*(12*p.getFullYear()+p.getMonth())+p.getDate();(!1!==y&&p>y||!1!==k&&p<k||!1!==b&&p<b||!1!==x&&O>w||c&&!1===c[0])&&T.push("xdsoft_disabled"),-1!==C.disabledDates.indexOf(r.formatDate(p,C.formatDate))&&T.push("xdsoft_disabled"),-1!==C.disabledWeekDays.indexOf(s)&&T.push("xdsoft_disabled"),a.is("[disabled]")&&T.push("xdsoft_disabled"),c&&""!==c[1]&&T.push(c[1]),A.currentTime.getMonth()!==l&&T.push("xdsoft_other_month"),(C.defaultSelect||j.data("changed"))&&r.formatDate(A.currentTime,C.formatDate)===r.formatDate(p,C.formatDate)&&T.push("xdsoft_current"),r.formatDate(v,C.formatDate)===r.formatDate(p,C.formatDate)&&T.push("xdsoft_today"),0!==p.getDay()&&6!==p.getDay()&&-1===C.weekends.indexOf(r.formatDate(p,C.formatDate))||T.push("xdsoft_weekend"),void 0!==C.highlightedDates[r.formatDate(p,C.formatDate)]&&(i=C.highlightedDates[r.formatDate(p,C.formatDate)],T.push(void 0===i.style?"xdsoft_highlighted_default":i.style),h=void 0===i.desc?"":i.desc),C.beforeShowDay&&e.isFunction(C.beforeShowDay)&&T.push(C.beforeShowDay(p)),S&&(g+="<tr>",S=!1,C.weeks&&(g+="<th>"+f+"</th>")),g+='<td data-date="'+u+'" data-month="'+l+'" data-year="'+d+'" class="xdsoft_date xdsoft_day_of_week'+p.getDay()+" "+T.join(" ")+'" title="'+h+'"><div>'+u+"</div></td>",p.getDay()===C.dayOfWeekStartPrev&&(g+="</tr>",S=!0),p.setDate(u+1)}g+="</tbody></table>",N.html(g),I.find(".xdsoft_label span").eq(0).text(C.i18n[o].months[A.currentTime.getMonth()]),I.find(".xdsoft_label span").eq(1).text(A.currentTime.getFullYear()+C.yearOffset),M="",l="";var W=0;if(!1!==C.minTime){F=A.strtotime(C.minTime);W=60*F.getHours()+F.getMinutes()}var _=1440;if(!1!==C.maxTime){F=A.strtotime(C.maxTime);_=60*F.getHours()+F.getMinutes()}if(!1!==C.minDateTime){F=A.strToDateTime(C.minDateTime);r.formatDate(A.currentTime,C.formatDate)===r.formatDate(F,C.formatDate)&&(l=60*F.getHours()+F.getMinutes())>W&&(W=l)}if(!1!==C.maxDateTime){var F=A.strToDateTime(C.maxDateTime);r.formatDate(A.currentTime,C.formatDate)===r.formatDate(F,C.formatDate)&&(l=60*F.getHours()+F.getMinutes())<_&&(_=l)}if(m=function(t,n){var o,i=A.now(),s=C.allowTimes&&e.isArray(C.allowTimes)&&C.allowTimes.length;i.setHours(t),t=parseInt(i.getHours(),10),i.setMinutes(n),n=parseInt(i.getMinutes(),10),T=[];var u=60*t+n;(a.is("[disabled]")||u>=_||u<W)&&T.push("xdsoft_disabled"),(o=new Date(A.currentTime)).setHours(parseInt(A.currentTime.getHours(),10)),s||o.setMinutes(Math[C.roundTime](A.currentTime.getMinutes()/C.step)*C.step),(C.initTime||C.defaultSelect||j.data("changed"))&&o.getHours()===parseInt(t,10)&&(!s&&C.step>59||o.getMinutes()===parseInt(n,10))&&(C.defaultSelect||j.data("changed")?T.push("xdsoft_current"):C.initTime&&T.push("xdsoft_init_time")),parseInt(v.getHours(),10)===parseInt(t,10)&&parseInt(v.getMinutes(),10)===parseInt(n,10)&&T.push("xdsoft_today"),M+='<div class="xdsoft_time '+T.join(" ")+'" data-hour="'+t+'" data-minute="'+n+'">'+r.formatDate(i,C.formatTime)+"</div>"},C.allowTimes&&e.isArray(C.allowTimes)&&C.allowTimes.length)for(D=0;D<C.allowTimes.length;D+=1)m(A.strtotime(C.allowTimes[D]).getHours(),l=A.strtotime(C.allowTimes[D]).getMinutes());else for(D=0,t=0;D<(C.hours12?12:24);D+=1)for(t=0;t<60;t+=C.step){var P=60*D+t;P<W||(P>=_||m((D<10?"0":"")+D,l=(t<10?"0":"")+t))}for(R.html(M),n="",D=parseInt(C.yearStart,10);D<=parseInt(C.yearEnd,10);D+=1)n+='<div class="xdsoft_option '+(A.currentTime.getFullYear()===D?"xdsoft_current":"")+'" data-value="'+D+'">'+(D+C.yearOffset)+"</div>";for(G.children().eq(0).html(n),D=parseInt(C.monthStart,10),n="";D<=parseInt(C.monthEnd,10);D+=1)n+='<div class="xdsoft_option '+(A.currentTime.getMonth()===D?"xdsoft_current":"")+'" data-value="'+D+'">'+C.i18n[o].months[D]+"</div>";B.children().eq(0).html(n),e(j).trigger("generate.xdsoft")},10),t.stopPropagation()}).on("afterOpen.xdsoft",function(){if(C.timepicker){var e,t,a,r;R.find(".xdsoft_current").length?e=".xdsoft_current":R.find(".xdsoft_init_time").length&&(e=".xdsoft_init_time"),e?(t=E[0].clientHeight,(a=R[0].offsetHeight)-t<(r=R.find(e).index()*C.timeHeightInTimePicker+1)&&(r=a-t),E.trigger("scroll_element.xdsoft_scroller",[parseInt(r,10)/(a-t)])):E.trigger("scroll_element.xdsoft_scroller",[0])}}),d=0,N.on("touchend click.xdsoft","td",function(t){t.stopPropagation(),d+=1;var r=e(this),n=A.currentTime;if(void 0!==n&&null!==n||(A.currentTime=A.now(),n=A.currentTime),r.hasClass("xdsoft_disabled"))return!1;n.setDate(1),n.setFullYear(r.data("year")),n.setMonth(r.data("month")),n.setDate(r.data("date")),j.trigger("select.xdsoft",[n]),a.val(A.str()),C.onSelectDate&&e.isFunction(C.onSelectDate)&&C.onSelectDate.call(j,A.currentTime,j.data("input"),t),j.data("changed",!0),j.trigger("xchange.xdsoft"),j.trigger("changedatetime.xdsoft"),(d>1||!0===C.closeOnDateSelect||!1===C.closeOnDateSelect&&!C.timepicker)&&!C.inline&&j.trigger("close.xdsoft"),setTimeout(function(){d=0},200)}),R.on("touchstart","div",function(e){this.touchMoved=!1}).on("touchmove","div",X).on("touchend click.xdsoft","div",function(t){if(!this.touchMoved){t.stopPropagation();var a=e(this),r=A.currentTime;if(void 0!==r&&null!==r||(A.currentTime=A.now(),r=A.currentTime),a.hasClass("xdsoft_disabled"))return!1;r.setHours(a.data("hour")),r.setMinutes(a.data("minute")),j.trigger("select.xdsoft",[r]),j.data("input").val(A.str()),C.onSelectTime&&e.isFunction(C.onSelectTime)&&C.onSelectTime.call(j,A.currentTime,j.data("input"),t),j.data("changed",!0),j.trigger("xchange.xdsoft"),j.trigger("changedatetime.xdsoft"),!0!==C.inline&&!0===C.closeOnTimeSelect&&j.trigger("close.xdsoft")}}),z.on("mousewheel.xdsoft",function(e){return!C.scrollMonth||(e.deltaY<0?A.nextMonth():A.prevMonth(),!1)}),a.on("mousewheel.xdsoft",function(e){return!C.scrollInput||(!C.datepicker&&C.timepicker?((P=R.find(".xdsoft_current").length?R.find(".xdsoft_current").eq(0).index():0)+e.deltaY>=0&&P+e.deltaY<R.children().length&&(P+=e.deltaY),R.children().eq(P).length&&R.children().eq(P).trigger("mousedown"),!1):C.datepicker&&!C.timepicker?(z.trigger(e,[e.deltaY,e.deltaX,e.deltaY]),a.val&&a.val(A.str()),j.trigger("changedatetime.xdsoft"),!1):void 0)}),j.on("changedatetime.xdsoft",function(t){if(C.onChangeDateTime&&e.isFunction(C.onChangeDateTime)){var a=j.data("input");C.onChangeDateTime.call(j,A.currentTime,a,t),delete C.value,a.trigger("change")}}).on("generate.xdsoft",function(){C.onGenerate&&e.isFunction(C.onGenerate)&&C.onGenerate.call(j,A.currentTime,j.data("input")),U&&(j.trigger("afterOpen.xdsoft"),U=!1)}).on("click.xdsoft",function(e){e.stopPropagation()}),P=0,H=function(e,t){do{if(!(e=e.parentNode)||!1===t(e))break}while("HTML"!==e.nodeName)},Y=function(){var t,a,r,n,o,i,s,u,d,l,f,c,m;if(u=j.data("input"),t=u.offset(),a=u[0],l="top",r=t.top+a.offsetHeight-1,n=t.left,o="absolute",d=e(C.contentWindow).width(),c=e(C.contentWindow).height(),m=e(C.contentWindow).scrollTop(),C.ownerDocument.documentElement.clientWidth-t.left<z.parent().outerWidth(!0)){var h=z.parent().outerWidth(!0)-a.offsetWidth;n-=h}"rtl"===u.parent().css("direction")&&(n-=j.outerWidth()-u.outerWidth()),C.fixed?(r-=m,n-=e(C.contentWindow).scrollLeft(),o="fixed"):(s=!1,H(a,function(e){return null!==e&&("fixed"===C.contentWindow.getComputedStyle(e).getPropertyValue("position")?(s=!0,!1):void 0)}),s?(o="fixed",r+j.outerHeight()>c+m?(l="bottom",r=c+m-t.top):r-=m):r+j[0].offsetHeight>c+m&&(r=t.top-j[0].offsetHeight+1),r<0&&(r=0),n+a.offsetWidth>d&&(n=d-a.offsetWidth)),i=j[0],H(i,function(e){if("relative"===C.contentWindow.getComputedStyle(e).getPropertyValue("position")&&d>=e.offsetWidth)return n-=(d-e.offsetWidth)/2,!1}),(f={position:o,left:n,top:"",bottom:""})[l]=r,j.css(f)},j.on("open.xdsoft",function(t){var a=!0;C.onShow&&e.isFunction(C.onShow)&&(a=C.onShow.call(j,A.currentTime,j.data("input"),t)),!1!==a&&(j.show(),Y(),e(C.contentWindow).off("resize.xdsoft",Y).on("resize.xdsoft",Y),C.closeOnWithoutClick&&e([C.ownerDocument.body,C.contentWindow]).on("touchstart mousedown.xdsoft",function t(){j.trigger("close.xdsoft"),e([C.ownerDocument.body,C.contentWindow]).off("touchstart mousedown.xdsoft",t)}))}).on("close.xdsoft",function(t){var a=!0;I.find(".xdsoft_month,.xdsoft_year").find(".xdsoft_select").hide(),C.onClose&&e.isFunction(C.onClose)&&(a=C.onClose.call(j,A.currentTime,j.data("input"),t)),!1===a||C.opened||C.inline||j.hide(),t.stopPropagation()}).on("toggle.xdsoft",function(){j.is(":visible")?j.trigger("close.xdsoft"):j.trigger("open.xdsoft")}).data("input",a),q=0,j.data("xdsoft_datetime",A),j.setOptions(C),A.setCurrentTime(i()),a.data("xdsoft_datetimepicker",j).on("open.xdsoft focusin.xdsoft mousedown.xdsoft touchstart",function(){a.is(":disabled")||a.data("xdsoft_datetimepicker").is(":visible")&&C.closeOnInputClick||C.openOnFocus&&(clearTimeout(q),q=setTimeout(function(){a.is(":disabled")||(U=!0,A.setCurrentTime(i(),!0),C.mask&&s(C),j.trigger("open.xdsoft"))},100))}).on("keydown.xdsoft",function(t){var a,r=t.which;return-1!==[p].indexOf(r)&&C.enterLikeTab?(a=e("input:visible,textarea:visible,button:visible,a:visible"),j.trigger("close.xdsoft"),a.eq(a.index(this)+1).focus(),!1):-1!==[T].indexOf(r)?(j.trigger("close.xdsoft"),!0):void 0}).on("blur.xdsoft",function(){j.trigger("close.xdsoft")})},u=function(t){var a=t.data("xdsoft_datetimepicker");a&&(a.data("xdsoft_datetime",null),a.remove(),t.data("xdsoft_datetimepicker",null).off(".xdsoft"),e(C.contentWindow).off("resize.xdsoft"),e([C.contentWindow,C.ownerDocument.body]).off("mousedown.xdsoft touchstart"),t.unmousewheel&&t.unmousewheel())},e(C.ownerDocument).off("keydown.xdsoftctrl keyup.xdsoftctrl").on("keydown.xdsoftctrl",function(e){e.keyCode===h&&(F=!0)}).on("keyup.xdsoftctrl",function(e){e.keyCode===h&&(F=!1)}),this.each(function(){var t=e(this).data("xdsoft_datetimepicker");if(t){if("string"===e.type(n))switch(n){case"show":e(this).select().focus(),t.trigger("open.xdsoft");break;case"hide":t.trigger("close.xdsoft");break;case"toggle":t.trigger("toggle.xdsoft");break;case"destroy":u(e(this));break;case"reset":this.value=this.defaultValue,this.value&&t.data("xdsoft_datetime").isValidDate(r.parseDate(this.value,C.format))||t.data("changed",!1),t.data("xdsoft_datetime").setCurrentTime(this.value);break;case"validate":t.data("input").trigger("blur.xdsoft");break;default:t[n]&&e.isFunction(t[n])&&(d=t[n](i))}else t.setOptions(n);return 0}"string"!==e.type(n)&&(!C.lazyInit||C.open||C.inline?s(e(this)):Y(e(this)))}),d},e.fn.datetimepicker.defaults=a};!function(e){"function"==typeof define&&define.amd?define(["jquery","jquery-mousewheel"],e):"object"==typeof exports?module.exports=e(require("jquery")):e(jQuery)}(datetimepickerFactory),function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?module.exports=e:e(jQuery)}(function(e){function t(t){var i=t||window.event,s=u.call(arguments,1),d=0,f=0,c=0,m=0,h=0,g=0;if(t=e.event.fix(i),t.type="mousewheel","detail"in i&&(c=-1*i.detail),"wheelDelta"in i&&(c=i.wheelDelta),"wheelDeltaY"in i&&(c=i.wheelDeltaY),"wheelDeltaX"in i&&(f=-1*i.wheelDeltaX),"axis"in i&&i.axis===i.HORIZONTAL_AXIS&&(f=-1*c,c=0),d=0===c?f:c,"deltaY"in i&&(d=c=-1*i.deltaY),"deltaX"in i&&(f=i.deltaX,0===c&&(d=-1*f)),0!==c||0!==f){if(1===i.deltaMode){var p=e.data(this,"mousewheel-line-height");d*=p,c*=p,f*=p}else if(2===i.deltaMode){var D=e.data(this,"mousewheel-page-height");d*=D,c*=D,f*=D}if(m=Math.max(Math.abs(c),Math.abs(f)),(!o||m<o)&&(o=m,r(i,m)&&(o/=40)),r(i,m)&&(d/=40,f/=40,c/=40),d=Math[d>=1?"floor":"ceil"](d/o),f=Math[f>=1?"floor":"ceil"](f/o),c=Math[c>=1?"floor":"ceil"](c/o),l.settings.normalizeOffset&&this.getBoundingClientRect){var v=this.getBoundingClientRect();h=t.clientX-v.left,g=t.clientY-v.top}return t.deltaX=f,t.deltaY=c,t.deltaFactor=o,t.offsetX=h,t.offsetY=g,t.deltaMode=0,s.unshift(t,d,f,c),n&&clearTimeout(n),n=setTimeout(a,200),(e.event.dispatch||e.event.handle).apply(this,s)}}function a(){o=null}function r(e,t){return l.settings.adjustOldDeltas&&"mousewheel"===e.type&&t%120==0}var n,o,i=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],s="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],u=Array.prototype.slice;if(e.event.fixHooks)for(var d=i.length;d;)e.event.fixHooks[i[--d]]=e.event.mouseHooks;var l=e.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var a=s.length;a;)this.addEventListener(s[--a],t,!1);else this.onmousewheel=t;e.data(this,"mousewheel-line-height",l.getLineHeight(this)),e.data(this,"mousewheel-page-height",l.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var a=s.length;a;)this.removeEventListener(s[--a],t,!1);else this.onmousewheel=null;e.removeData(this,"mousewheel-line-height"),e.removeData(this,"mousewheel-page-height")},getLineHeight:function(t){var a=e(t),r=a["offsetParent"in e.fn?"offsetParent":"parent"]();return r.length||(r=e("body")),parseInt(r.css("fontSize"),10)||parseInt(a.css("fontSize"),10)||16},getPageHeight:function(t){return e(t).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};e.fn.extend({mousewheel:function(e){return e?this.bind("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}})});/*!
 * Bootstrap v3.3.7 (http://getbootstrap.com)
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under the MIT license
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){"use strict";var b=a.fn.jquery.split(" ")[0].split(".");if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1||b[0]>3)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4")}(jQuery),+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){if(a(b.target).is(this))return b.handleObj.handler.apply(this,arguments)}})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var c=a(this),e=c.data("bs.alert");e||c.data("bs.alert",e=new d(this)),"string"==typeof b&&e[b].call(c)})}var c='[data-dismiss="alert"]',d=function(b){a(b).on("click",c,this.close)};d.VERSION="3.3.7",d.TRANSITION_DURATION=150,d.prototype.close=function(b){function c(){g.detach().trigger("closed.bs.alert").remove()}var e=a(this),f=e.attr("data-target");f||(f=e.attr("href"),f=f&&f.replace(/.*(?=#[^\s]*$)/,""));var g=a("#"===f?[]:f);b&&b.preventDefault(),g.length||(g=e.closest(".alert")),g.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(g.removeClass("in"),a.support.transition&&g.hasClass("fade")?g.one("bsTransitionEnd",c).emulateTransitionEnd(d.TRANSITION_DURATION):c())};var e=a.fn.alert;a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){return a.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof b&&b;e||d.data("bs.button",e=new c(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.isLoading=!1};c.VERSION="3.3.7",c.DEFAULTS={loadingText:"loading..."},c.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",null==f.resetText&&d.data("resetText",d[e]()),setTimeout(a.proxy(function(){d[e](null==f[b]?this.options[b]:f[b]),"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c).prop(c,!0)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c).prop(c,!1))},this),0)},c.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")?(c.prop("checked")&&(a=!1),b.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==c.prop("type")&&(c.prop("checked")!==this.$element.hasClass("active")&&(a=!1),this.$element.toggleClass("active")),c.prop("checked",this.$element.hasClass("active")),a&&c.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var d=a.fn.button;a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){return a.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){var d=a(c.target).closest(".btn");b.call(d,"toggle"),a(c.target).is('input[type="radio"], input[type="checkbox"]')||(c.preventDefault(),d.is("input,button")?d.trigger("focus"):d.find("input:visible,button:visible").first().trigger("focus"))}).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',function(b){a(b.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(b.type))})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b),g="string"==typeof b?b:f.slide;e||d.data("bs.carousel",e=new c(this,f)),"number"==typeof b?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}var c=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=null,this.sliding=null,this.interval=null,this.$active=null,this.$items=null,this.options.keyboard&&this.$element.on("keydown.bs.carousel",a.proxy(this.keydown,this)),"hover"==this.options.pause&&!("ontouchstart"in document.documentElement)&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};c.VERSION="3.3.7",c.TRANSITION_DURATION=600,c.DEFAULTS={interval:5e3,pause:"hover",wrap:!0,keyboard:!0},c.prototype.keydown=function(a){if(!/input|textarea/i.test(a.target.tagName)){switch(a.which){case 37:this.prev();break;case 39:this.next();break;default:return}a.preventDefault()}},c.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){return this.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.getItemForDirection=function(a,b){var c=this.getItemIndex(b),d="prev"==a&&0===c||"next"==a&&c==this.$items.length-1;if(d&&!this.options.wrap)return b;var e="prev"==a?-1:1,f=(c+e)%this.$items.length;return this.$items.eq(f)},c.prototype.to=function(a){var b=this,c=this.getItemIndex(this.$active=this.$element.find(".item.active"));if(!(a>this.$items.length-1||a<0))return this.sliding?this.$element.one("slid.bs.carousel",function(){b.to(a)}):c==a?this.pause().cycle():this.slide(a>c?"next":"prev",this.$items.eq(a))},c.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){if(!this.sliding)return this.slide("next")},c.prototype.prev=function(){if(!this.sliding)return this.slide("prev")},c.prototype.slide=function(b,d){var e=this.$element.find(".item.active"),f=d||this.getItemForDirection(b,e),g=this.interval,h="next"==b?"left":"right",i=this;if(f.hasClass("active"))return this.sliding=!1;var j=f[0],k=a.Event("slide.bs.carousel",{relatedTarget:j,direction:h});if(this.$element.trigger(k),!k.isDefaultPrevented()){if(this.sliding=!0,g&&this.pause(),this.$indicators.length){this.$indicators.find(".active").removeClass("active");var l=a(this.$indicators.children()[this.getItemIndex(f)]);l&&l.addClass("active")}var m=a.Event("slid.bs.carousel",{relatedTarget:j,direction:h});return a.support.transition&&this.$element.hasClass("slide")?(f.addClass(b),f[0].offsetWidth,e.addClass(h),f.addClass(h),e.one("bsTransitionEnd",function(){f.removeClass([b,h].join(" ")).addClass("active"),e.removeClass(["active",h].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger(m)},0)}).emulateTransitionEnd(c.TRANSITION_DURATION)):(e.removeClass("active"),f.addClass("active"),this.sliding=!1,this.$element.trigger(m)),g&&this.cycle(),this}};var d=a.fn.carousel;a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){return a.fn.carousel=d,this};var e=function(c){var d,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));if(f.hasClass("carousel")){var g=a.extend({},f.data(),e.data()),h=e.attr("data-slide-to");h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}};a(document).on("click.bs.carousel.data-api","[data-slide]",e).on("click.bs.carousel.data-api","[data-slide-to]",e),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var c=a(this);b.call(c,c.data())})})}(jQuery),+function(a){"use strict";function b(b){var c,d=b.attr("data-target")||(c=b.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"");return a(d)}function c(b){return this.each(function(){var c=a(this),e=c.data("bs.collapse"),f=a.extend({},d.DEFAULTS,c.data(),"object"==typeof b&&b);!e&&f.toggle&&/show|hide/.test(b)&&(f.toggle=!1),e||c.data("bs.collapse",e=new d(this,f)),"string"==typeof b&&e[b]()})}var d=function(b,c){this.$element=a(b),this.options=a.extend({},d.DEFAULTS,c),this.$trigger=a('[data-toggle="collapse"][href="#'+b.id+'"],[data-toggle="collapse"][data-target="#'+b.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};d.VERSION="3.3.7",d.TRANSITION_DURATION=350,d.DEFAULTS={toggle:!0},d.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},d.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var b,e=this.$parent&&this.$parent.children(".panel").children(".in, .collapsing");if(!(e&&e.length&&(b=e.data("bs.collapse"),b&&b.transitioning))){var f=a.Event("show.bs.collapse");if(this.$element.trigger(f),!f.isDefaultPrevented()){e&&e.length&&(c.call(e,"hide"),b||e.data("bs.collapse",null));var g=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[g](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;var h=function(){this.$element.removeClass("collapsing").addClass("collapse in")[g](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return h.call(this);var i=a.camelCase(["scroll",g].join("-"));this.$element.one("bsTransitionEnd",a.proxy(h,this)).emulateTransitionEnd(d.TRANSITION_DURATION)[g](this.$element[0][i])}}}},d.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;var e=function(){this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};return a.support.transition?void this.$element[c](0).one("bsTransitionEnd",a.proxy(e,this)).emulateTransitionEnd(d.TRANSITION_DURATION):e.call(this)}}},d.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()},d.prototype.getParent=function(){return a(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(a.proxy(function(c,d){var e=a(d);this.addAriaAndCollapsedClass(b(e),e)},this)).end()},d.prototype.addAriaAndCollapsedClass=function(a,b){var c=a.hasClass("in");a.attr("aria-expanded",c),b.toggleClass("collapsed",!c).attr("aria-expanded",c)};var e=a.fn.collapse;a.fn.collapse=c,a.fn.collapse.Constructor=d,a.fn.collapse.noConflict=function(){return a.fn.collapse=e,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(d){var e=a(this);e.attr("data-target")||d.preventDefault();var f=b(e),g=f.data("bs.collapse"),h=g?"toggle":e.data();c.call(f,h)})}(jQuery),+function(a){"use strict";function b(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}function c(c){c&&3===c.which||(a(e).remove(),a(f).each(function(){var d=a(this),e=b(d),f={relatedTarget:this};e.hasClass("open")&&(c&&"click"==c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(e[0],c.target)||(e.trigger(c=a.Event("hide.bs.dropdown",f)),c.isDefaultPrevented()||(d.attr("aria-expanded","false"),e.removeClass("open").trigger(a.Event("hidden.bs.dropdown",f)))))}))}function d(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new g(this)),"string"==typeof b&&d[b].call(c)})}var e=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){a(b).on("click.bs.dropdown",this.toggle)};g.VERSION="3.3.7",g.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=b(e),g=f.hasClass("open");if(c(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click",c);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;e.trigger("focus").attr("aria-expanded","true"),f.toggleClass("open").trigger(a.Event("shown.bs.dropdown",h))}return!1}},g.prototype.keydown=function(c){if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)){var d=a(this);if(c.preventDefault(),c.stopPropagation(),!d.is(".disabled, :disabled")){var e=b(d),g=e.hasClass("open");if(!g&&27!=c.which||g&&27==c.which)return 27==c.which&&e.find(f).trigger("focus"),d.trigger("click");var h=" li:not(.disabled):visible a",i=e.find(".dropdown-menu"+h);if(i.length){var j=i.index(c.target);38==c.which&&j>0&&j--,40==c.which&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};var h=a.fn.dropdown;a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",c).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery),+function(a){"use strict";function b(b,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},c.DEFAULTS,e.data(),"object"==typeof b&&b);f||e.data("bs.modal",f=new c(this,g)),"string"==typeof b?f[b](d):g.show&&f.show(d)})}var c=function(b,c){this.options=c,this.$body=a(document.body),this.$element=a(b),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};c.VERSION="3.3.7",c.TRANSITION_DURATION=300,c.BACKDROP_TRANSITION_DURATION=150,c.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){return this.isShown?this.hide():this.show(a)},c.prototype.show=function(b){var d=this,e=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(e),this.isShown||e.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){d.$element.one("mouseup.dismiss.bs.modal",function(b){a(b.target).is(d.$element)&&(d.ignoreBackdropClick=!0)})}),this.backdrop(function(){var e=a.support.transition&&d.$element.hasClass("fade");d.$element.parent().length||d.$element.appendTo(d.$body),d.$element.show().scrollTop(0),d.adjustDialog(),e&&d.$element[0].offsetWidth,d.$element.addClass("in"),d.enforceFocus();var f=a.Event("shown.bs.modal",{relatedTarget:b});e?d.$dialog.one("bsTransitionEnd",function(){d.$element.trigger("focus").trigger(f)}).emulateTransitionEnd(c.TRANSITION_DURATION):d.$element.trigger("focus").trigger(f)}))},c.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(c.TRANSITION_DURATION):this.hideModal())},c.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){document===a.target||this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},c.prototype.resize=function(){this.isShown?a(window).on("resize.bs.modal",a.proxy(this.handleUpdate,this)):a(window).off("resize.bs.modal")},c.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.$body.removeClass("modal-open"),a.resetAdjustments(),a.resetScrollbar(),a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){var d=this,e=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var f=a.support.transition&&e;if(this.$backdrop=a(document.createElement("div")).addClass("modal-backdrop "+e).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){return this.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),f&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;f?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var g=function(){d.removeBackdrop(),b&&b()};a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):g()}else b&&b()},c.prototype.handleUpdate=function(){this.adjustDialog()},c.prototype.adjustDialog=function(){var a=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&a?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!a?this.scrollbarWidth:""})},c.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},c.prototype.checkScrollbar=function(){var a=window.innerWidth;if(!a){var b=document.documentElement.getBoundingClientRect();a=b.right-Math.abs(b.left)}this.bodyIsOverflowing=document.body.clientWidth<a,this.scrollbarWidth=this.measureScrollbar()},c.prototype.setScrollbar=function(){var a=parseInt(this.$body.css("padding-right")||0,10);this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad)},c.prototype.measureScrollbar=function(){var a=document.createElement("div");a.className="modal-scrollbar-measure",this.$body.append(a);var b=a.offsetWidth-a.clientWidth;return this.$body[0].removeChild(a),b};var d=a.fn.modal;a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){return a.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){var d=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(e)&&e},f.data(),d.data());d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof b&&b;!e&&/destroy|hide/.test(b)||(e||d.data("bs.tooltip",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",a,b)};c.VERSION="3.3.7",c.TRANSITION_DURATION=150,c.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},c.prototype.init=function(b,c,d){if(this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(a.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusin"==b.type?"focus":"hover"]=!0),c.tip().hasClass("in")||"in"==c.hoverState?void(c.hoverState="in"):(clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show())},c.prototype.isInStateTrue=function(){for(var a in this.inState)if(this.inState[a])return!0;return!1},c.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);if(c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusout"==b.type?"focus":"hover"]=!1),!c.isInStateTrue())return clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide()},c.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(b);var d=a.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(b.isDefaultPrevented()||!d)return;var e=this,f=this.tip(),g=this.getUID(this.type);this.setContent(),f.attr("id",g),this.$element.attr("aria-describedby",g),this.options.animation&&f.addClass("fade");var h="function"==typeof this.options.placement?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,i=/\s?auto?\s?/i,j=i.test(h);j&&(h=h.replace(i,"")||"top"),f.detach().css({top:0,left:0,display:"block"}).addClass(h).data("bs."+this.type,this),this.options.container?f.appendTo(this.options.container):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var k=this.getPosition(),l=f[0].offsetWidth,m=f[0].offsetHeight;if(j){var n=h,o=this.getPosition(this.$viewport);h="bottom"==h&&k.bottom+m>o.bottom?"top":"top"==h&&k.top-m<o.top?"bottom":"right"==h&&k.right+l>o.width?"left":"left"==h&&k.left-l<o.left?"right":h,f.removeClass(n).addClass(h)}var p=this.getCalculatedOffset(h,k,l,m);this.applyPlacement(p,h);var q=function(){var a=e.hoverState;e.$element.trigger("shown.bs."+e.type),e.hoverState=null,"out"==a&&e.leave(e)};a.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",q).emulateTransitionEnd(c.TRANSITION_DURATION):q()}},c.prototype.applyPlacement=function(b,c){var d=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top+=g,b.left+=h,a.offset.setOffset(d[0],a.extend({using:function(a){d.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");var i=d[0].offsetWidth,j=d[0].offsetHeight;"top"==c&&j!=f&&(b.top=b.top+f-j);var k=this.getViewportAdjustedDelta(c,b,i,j);k.left?b.left+=k.left:b.top+=k.top;var l=/top|bottom/.test(c),m=l?2*k.left-e+i:2*k.top-f+j,n=l?"offsetWidth":"offsetHeight";d.offset(b),this.replaceArrow(m,d[0][n],l)},c.prototype.replaceArrow=function(a,b,c){this.arrow().css(c?"left":"top",50*(1-a/b)+"%").css(c?"top":"left","")},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},c.prototype.hide=function(b){function d(){"in"!=e.hoverState&&f.detach(),e.$element&&e.$element.removeAttr("aria-describedby").trigger("hidden.bs."+e.type),b&&b()}var e=this,f=a(this.$tip),g=a.Event("hide.bs."+this.type);if(this.$element.trigger(g),!g.isDefaultPrevented())return f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",d).emulateTransitionEnd(c.TRANSITION_DURATION):d(),this.hoverState=null,this},c.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){return this.getTitle()},c.prototype.getPosition=function(b){b=b||this.$element;var c=b[0],d="BODY"==c.tagName,e=c.getBoundingClientRect();null==e.width&&(e=a.extend({},e,{width:e.right-e.left,height:e.bottom-e.top}));var f=window.SVGElement&&c instanceof window.SVGElement,g=d?{top:0,left:0}:f?null:b.offset(),h={scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop()},i=d?{width:a(window).width(),height:a(window).height()}:null;return a.extend({},e,h,i,g)},c.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){var e={top:0,left:0};if(!this.$viewport)return e;var f=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);if(/right|left/.test(a)){var h=b.top-f-g.scroll,i=b.top+f-g.scroll+d;h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{var j=b.left-f,k=b.left+f+c;j<g.left?e.left=g.left-j:k>g.right&&(e.left=g.left+g.width-k)}return e},c.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){do a+=~~(1e6*Math.random());while(document.getElementById(a));return a},c.prototype.tip=function(){if(!this.$tip&&(this.$tip=a(this.options.template),1!=this.$tip.length))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.enable=function(){this.enabled=!0},c.prototype.disable=function(){this.enabled=!1},c.prototype.toggleEnabled=function(){this.enabled=!this.enabled},c.prototype.toggle=function(b){var c=this;b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),b?(c.inState.click=!c.inState.click,c.isInStateTrue()?c.enter(c):c.leave(c)):c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){var a=this;clearTimeout(this.timeout),this.hide(function(){a.$element.off("."+a.type).removeData("bs."+a.type),a.$tip&&a.$tip.detach(),a.$tip=null,a.$arrow=null,a.$viewport=null,a.$element=null})};var d=a.fn.tooltip;a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=d,this}}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof b&&b;!e&&/destroy|hide/.test(b)||(e||d.data("bs.popover",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");c.VERSION="3.3.7",c.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),c.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").children().detach().end()[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){return this.getTitle()||this.getContent()},c.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")};var d=a.fn.popover;a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){return a.fn.popover=d,this}}(jQuery),+function(a){"use strict";function b(c,d){this.$body=a(document.body),this.$scrollElement=a(a(c).is(document.body)?window:c),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",a.proxy(this.process,this)),this.refresh(),this.process()}function c(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})}b.VERSION="3.3.7",b.DEFAULTS={offset:10},b.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){var b=this,c="offset",d=0;this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),a.isWindow(this.$scrollElement[0])||(c="position",d=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){var b=a(this),e=b.data("target")||b.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[c]().top+d,e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){b.offsets.push(this[0]),b.targets.push(this[1])})},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(this.scrollHeight!=c&&this.refresh(),b>=d)return g!=(a=f[f.length-1])&&this.activate(a);if(g&&b<e[0])return this.activeTarget=null,this.clear();for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(void 0===e[a+1]||b<e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){
this.activeTarget=b,this.clear();var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate.bs.scrollspy")},b.prototype.clear=function(){a(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};var d=a.fn.scrollspy;a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new c(this)),"string"==typeof b&&e[b]()})}var c=function(b){this.element=a(b)};c.VERSION="3.3.7",c.TRANSITION_DURATION=150,c.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a"),f=a.Event("hide.bs.tab",{relatedTarget:b[0]}),g=a.Event("show.bs.tab",{relatedTarget:e[0]});if(e.trigger(f),b.trigger(g),!g.isDefaultPrevented()&&!f.isDefaultPrevented()){var h=a(d);this.activate(b.closest("li"),c),this.activate(h,h.parent(),function(){e.trigger({type:"hidden.bs.tab",relatedTarget:b[0]}),b.trigger({type:"shown.bs.tab",relatedTarget:e[0]})})}}},c.prototype.activate=function(b,d,e){function f(){g.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),h?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu").length&&b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),e&&e()}var g=d.find("> .active"),h=e&&a.support.transition&&(g.length&&g.hasClass("fade")||!!d.find("> .fade").length);g.length&&h?g.one("bsTransitionEnd",f).emulateTransitionEnd(c.TRANSITION_DURATION):f(),g.removeClass("in")};var d=a.fn.tab;a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){return a.fn.tab=d,this};var e=function(c){c.preventDefault(),b.call(a(this),"show")};a(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',e).on("click.bs.tab.data-api",'[data-toggle="pill"]',e)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof b&&b;e||d.data("bs.affix",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.options=a.extend({},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};c.VERSION="3.3.7",c.RESET="affix affix-top affix-bottom",c.DEFAULTS={offset:0,target:window},c.prototype.getState=function(a,b,c,d){var e=this.$target.scrollTop(),f=this.$element.offset(),g=this.$target.height();if(null!=c&&"top"==this.affixed)return e<c&&"top";if("bottom"==this.affixed)return null!=c?!(e+this.unpin<=f.top)&&"bottom":!(e+g<=a-d)&&"bottom";var h=null==this.affixed,i=h?e:f.top,j=h?g:b;return null!=c&&e<=c?"top":null!=d&&i+j>=a-d&&"bottom"},c.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(c.RESET).addClass("affix");var a=this.$target.scrollTop(),b=this.$element.offset();return this.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){if(this.$element.is(":visible")){var b=this.$element.height(),d=this.options.offset,e=d.top,f=d.bottom,g=Math.max(a(document).height(),a(document.body).height());"object"!=typeof d&&(f=e=d),"function"==typeof e&&(e=d.top(this.$element)),"function"==typeof f&&(f=d.bottom(this.$element));var h=this.getState(g,b,e,f);if(this.affixed!=h){null!=this.unpin&&this.$element.css("top","");var i="affix"+(h?"-"+h:""),j=a.Event(i+".bs.affix");if(this.$element.trigger(j),j.isDefaultPrevented())return;this.affixed=h,this.unpin="bottom"==h?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(i).trigger(i.replace("affix","affixed")+".bs.affix")}"bottom"==h&&this.$element.offset({top:g-b-f})}};var d=a.fn.affix;a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){return a.fn.affix=d,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var c=a(this),d=c.data();d.offset=d.offset||{},null!=d.offsetBottom&&(d.offset.bottom=d.offsetBottom),null!=d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);window.onkeydown=function(event){if(event.keyCode==78){if(event.ctrlKey){event.returnValue=false;event.keyCode=0;window.status='New window is disabled';return false;}}};/*!
 * Bootstrap-select v1.11.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2016 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function (jQuery) {

(function ($) {
  'use strict';

  //<editor-fold desc="Shims">
  if (!String.prototype.includes) {
    (function () {
      'use strict'; // needed to support `apply`/`call` with `undefined`/`null`
      var toString = {}.toString;
      var defineProperty = (function () {
        // IE 8 only supports `Object.defineProperty` on DOM elements
        try {
          var object = {};
          var $defineProperty = Object.defineProperty;
          var result = $defineProperty(object, object, object) && $defineProperty;
        } catch (error) {
        }
        return result;
      }());
      var indexOf = ''.indexOf;
      var includes = function (search) {
        if (this == null) {
          throw new TypeError();
        }
        var string = String(this);
        if (search && toString.call(search) == '[object RegExp]') {
          throw new TypeError();
        }
        var stringLength = string.length;
        var searchString = String(search);
        var searchLength = searchString.length;
        var position = arguments.length > 1 ? arguments[1] : undefined;
        // `ToInteger`
        var pos = position ? Number(position) : 0;
        if (pos != pos) { // better `isNaN`
          pos = 0;
        }
        var start = Math.min(Math.max(pos, 0), stringLength);
        // Avoid the `indexOf` call if no match is possible
        if (searchLength + start > stringLength) {
          return false;
        }
        return indexOf.call(string, searchString, pos) != -1;
      };
      if (defineProperty) {
        defineProperty(String.prototype, 'includes', {
          'value': includes,
          'configurable': true,
          'writable': true
        });
      } else {
        String.prototype.includes = includes;
      }
    }());
  }

  if (!String.prototype.startsWith) {
    (function () {
      'use strict'; // needed to support `apply`/`call` with `undefined`/`null`
      var defineProperty = (function () {
        // IE 8 only supports `Object.defineProperty` on DOM elements
        try {
          var object = {};
          var $defineProperty = Object.defineProperty;
          var result = $defineProperty(object, object, object) && $defineProperty;
        } catch (error) {
        }
        return result;
      }());
      var toString = {}.toString;
      var startsWith = function (search) {
        if (this == null) {
          throw new TypeError();
        }
        var string = String(this);
        if (search && toString.call(search) == '[object RegExp]') {
          throw new TypeError();
        }
        var stringLength = string.length;
        var searchString = String(search);
        var searchLength = searchString.length;
        var position = arguments.length > 1 ? arguments[1] : undefined;
        // `ToInteger`
        var pos = position ? Number(position) : 0;
        if (pos != pos) { // better `isNaN`
          pos = 0;
        }
        var start = Math.min(Math.max(pos, 0), stringLength);
        // Avoid the `indexOf` call if no match is possible
        if (searchLength + start > stringLength) {
          return false;
        }
        var index = -1;
        while (++index < searchLength) {
          if (string.charCodeAt(start + index) != searchString.charCodeAt(index)) {
            return false;
          }
        }
        return true;
      };
      if (defineProperty) {
        defineProperty(String.prototype, 'startsWith', {
          'value': startsWith,
          'configurable': true,
          'writable': true
        });
      } else {
        String.prototype.startsWith = startsWith;
      }
    }());
  }

  if (!Object.keys) {
    Object.keys = function (
      o, // object
      k, // key
      r  // result array
      ){
      // initialize object and result
      r=[];
      // iterate over object keys
      for (k in o)
          // fill result array with non-prototypical keys
        r.hasOwnProperty.call(o, k) && r.push(k);
      // return result
      return r;
    };
  }

  // set data-selected on select element if the value has been programmatically selected
  // prior to initialization of bootstrap-select
  // * consider removing or replacing an alternative method *
  var valHooks = {
    useDefault: false,
    _set: $.valHooks.select.set
  };

  $.valHooks.select.set = function(elem, value) {
    if (value && !valHooks.useDefault) $(elem).data('selected', true);

    return valHooks._set.apply(this, arguments);
  };

  var changed_arguments = null;
  $.fn.triggerNative = function (eventName) {
    var el = this[0],
        event;

    if (el.dispatchEvent) { // for modern browsers & IE9+
      if (typeof Event === 'function') {
        // For modern browsers
        event = new Event(eventName, {
          bubbles: true
        });
      } else {
        // For IE since it doesn't support Event constructor
        event = document.createEvent('Event');
        event.initEvent(eventName, true, false);
      }

      el.dispatchEvent(event);
    } else if (el.fireEvent) { // for IE8
      event = document.createEventObject();
      event.eventType = eventName;
      el.fireEvent('on' + eventName, event);
    } else {
      // fall back to jQuery.trigger
      this.trigger(eventName);
    }
  };
  //</editor-fold>

  // Case insensitive contains search
  $.expr.pseudos.icontains = function (obj, index, meta) {
    var $obj = $(obj);
    var haystack = ($obj.data('tokens') || $obj.text()).toString().toUpperCase();
    return haystack.includes(meta[3].toUpperCase());
  };

  // Case insensitive begins search
  $.expr.pseudos.ibegins = function (obj, index, meta) {
    var $obj = $(obj);
    var haystack = ($obj.data('tokens') || $obj.text()).toString().toUpperCase();
    return haystack.startsWith(meta[3].toUpperCase());
  };

  // Case and accent insensitive contains search
  $.expr.pseudos.aicontains = function (obj, index, meta) {
    var $obj = $(obj);
    var haystack = ($obj.data('tokens') || $obj.data('normalizedText') || $obj.text()).toString().toUpperCase();
    return haystack.includes(meta[3].toUpperCase());
  };

  // Case and accent insensitive begins search
  $.expr.pseudos.aibegins = function (obj, index, meta) {
    var $obj = $(obj);
    var haystack = ($obj.data('tokens') || $obj.data('normalizedText') || $obj.text()).toString().toUpperCase();
    return haystack.startsWith(meta[3].toUpperCase());
  };

  /**
   * Remove all diatrics from the given text.
   * @access private
   * @param {String} text
   * @returns {String}
   */
  function normalizeToBase(text) {
    var rExps = [
      {re: /[\xC0-\xC6]/g, ch: "A"},
      {re: /[\xE0-\xE6]/g, ch: "a"},
      {re: /[\xC8-\xCB]/g, ch: "E"},
      {re: /[\xE8-\xEB]/g, ch: "e"},
      {re: /[\xCC-\xCF]/g, ch: "I"},
      {re: /[\xEC-\xEF]/g, ch: "i"},
      {re: /[\xD2-\xD6]/g, ch: "O"},
      {re: /[\xF2-\xF6]/g, ch: "o"},
      {re: /[\xD9-\xDC]/g, ch: "U"},
      {re: /[\xF9-\xFC]/g, ch: "u"},
      {re: /[\xC7-\xE7]/g, ch: "c"},
      {re: /[\xD1]/g, ch: "N"},
      {re: /[\xF1]/g, ch: "n"}
    ];
    $.each(rExps, function () {
      text = text.replace(this.re, this.ch);
    });
    return text;
  }


  function htmlEscape(html) {
    var escapeMap = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#x27;',
      '`': '&#x60;'
    };
    var source = '(?:' + Object.keys(escapeMap).join('|') + ')',
        testRegexp = new RegExp(source),
        replaceRegexp = new RegExp(source, 'g'),
        string = html == null ? '' : '' + html;
    return testRegexp.test(string) ? string.replace(replaceRegexp, function (match) {
      return escapeMap[match];
    }) : string;
  }

  var Selectpicker = function (element, options, e) {
    // bootstrap-select has been initialized - revert valHooks.select.set back to its original function
    if (!valHooks.useDefault) {
      $.valHooks.select.set = valHooks._set;
      valHooks.useDefault = true;
    }

    if (e) {
      e.stopPropagation();
      e.preventDefault();
    }

    this.$element = $(element);
    this.$newElement = null;
    this.$button = null;
    this.$menu = null;
    this.$lis = null;
    this.options = options;

    // If we have no title yet, try to pull it from the html title attribute (jQuery doesnt' pick it up as it's not a
    // data-attribute)
    if (this.options.title === null) {
      this.options.title = this.$element.attr('title');
    }

    //Expose public methods
    this.val = Selectpicker.prototype.val;
    this.render = Selectpicker.prototype.render;
    this.refresh = Selectpicker.prototype.refresh;
    this.setStyle = Selectpicker.prototype.setStyle;
    this.selectAll = Selectpicker.prototype.selectAll;
    this.deselectAll = Selectpicker.prototype.deselectAll;
    this.destroy = Selectpicker.prototype.destroy;
    this.remove = Selectpicker.prototype.remove;
    this.show = Selectpicker.prototype.show;
    this.hide = Selectpicker.prototype.hide;

    this.init();
  };

  Selectpicker.VERSION = '1.11.2';

  // part of this is duplicated in i18n/defaults-en_US.js. Make sure to update both.
  Selectpicker.DEFAULTS = {
    noneSelectedText: 'Nothing selected',
    noneResultsText: 'No results matched {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? "{0} item selected" : "{0} items selected";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Limit reached ({n} item max)' : 'Limit reached ({n} items max)',
        (numGroup == 1) ? 'Group limit reached ({n} item max)' : 'Group limit reached ({n} items max)'
      ];
    },
    selectAllText: 'Select All',
    deselectAllText: 'Deselect All',
    doneButton: false,
    doneButtonText: 'Close',
    multipleSeparator: ', ',
    styleBase: 'btn',
    style: 'btn-default',
    size: 'auto',
    title: null,
    selectedTextFormat: 'values',
    width: false,
    container: false,
    hideDisabled: false,
    showSubtext: false,
    showIcon: true,
    showContent: true,
    dropupAuto: true,
    header: false,
    liveSearch: false,
    liveSearchPlaceholder: null,
    liveSearchNormalize: false,
    liveSearchStyle: 'contains',
    actionsBox: false,
    iconBase: 'glyphicon',
    tickIcon: 'glyphicon-ok',
    showTick: false,
    template: {
      caret: '<span class="caret"></span>'
    },
    maxOptions: false,
    mobile: false,
    selectOnTab: false,
    dropdownAlignRight: false
  };

  Selectpicker.prototype = {

    constructor: Selectpicker,

    init: function () {
      var that = this,
          id = this.$element.attr('id');

      this.$element.addClass('bs-select-hidden');

      // store originalIndex (key) and newIndex (value) in this.liObj for fast accessibility
      // allows us to do this.$lis.eq(that.liObj[index]) instead of this.$lis.filter('[data-original-index="' + index + '"]')
      this.liObj = {};
      this.multiple = this.$element.prop('multiple');
      this.autofocus = this.$element.prop('autofocus');
      this.$newElement = this.createView();
      this.$element
        .after(this.$newElement)
        .appendTo(this.$newElement);
      this.$button = this.$newElement.children('button');
      this.$menu = this.$newElement.children('.dropdown-menu');
      this.$menuInner = this.$menu.children('.inner');
      this.$searchbox = this.$menu.find('input');

      this.$element.removeClass('bs-select-hidden');

      if (this.options.dropdownAlignRight === true) this.$menu.addClass('dropdown-menu-right');

      if (typeof id !== 'undefined') {
        this.$button.attr('data-id', id);
        $('label[for="' + id + '"]').click(function (e) {
          e.preventDefault();
          that.$button.focus();
        });
      }

      this.checkDisabled();
      this.clickListener();
      if (this.options.liveSearch) this.liveSearchListener();
      this.render();
      this.setStyle();
      this.setWidth();
      if (this.options.container) this.selectPosition();
      this.$menu.data('this', this);
      this.$newElement.data('this', this);
      if (this.options.mobile) this.mobile();

      this.$newElement.on({
        'hide.bs.dropdown': function (e) {
          that.$menuInner.attr('aria-expanded', false);
          that.$element.trigger('hide.bs.select', e);
        },
        'hidden.bs.dropdown': function (e) {
          that.$element.trigger('hidden.bs.select', e);
        },
        'show.bs.dropdown': function (e) {
          that.$menuInner.attr('aria-expanded', true);
          that.$element.trigger('show.bs.select', e);
        },
        'shown.bs.dropdown': function (e) {
          that.$element.trigger('shown.bs.select', e);
        }
      });

      if (that.$element[0].hasAttribute('required')) {
        this.$element.on('invalid', function () {
          that.$button
            .addClass('bs-invalid')
            .focus();

          that.$element.on({
            'focus.bs.select': function () {
              that.$button.focus();
              that.$element.off('focus.bs.select');
            },
            'shown.bs.select': function () {
              that.$element
                .val(that.$element.val()) // set the value to hide the validation message in Chrome when menu is opened
                .off('shown.bs.select');
            },
            'rendered.bs.select': function () {
              // if select is no longer invalid, remove the bs-invalid class
              if (this.validity.valid) that.$button.removeClass('bs-invalid');
              that.$element.off('rendered.bs.select');
            }
          });
        });
      }

      setTimeout(function () {
        that.$element.trigger('loaded.bs.select');
      });
    },

    createDropdown: function () {
      // Options
      // If we are multiple or showTick option is set, then add the show-tick class
      var showTick = (this.multiple || this.options.showTick) ? ' show-tick' : '',
          inputGroup = this.$element.parent().hasClass('input-group') ? ' input-group-btn' : '',
          autofocus = this.autofocus ? ' autofocus' : '';
      // Elements
      var header = this.options.header ? '<div class="popover-title"><button type="button" class="close" aria-hidden="true">&times;</button>' + this.options.header + '</div>' : '';
      var searchbox = this.options.liveSearch ?
      '<div class="bs-searchbox">' +
      '<input type="text" class="form-control" autocomplete="off"' +
      (null === this.options.liveSearchPlaceholder ? '' : ' placeholder="' + htmlEscape(this.options.liveSearchPlaceholder) + '"') + ' role="textbox" aria-label="Search">' +
      '</div>'
          : '';
      var actionsbox = this.multiple && this.options.actionsBox ?
      '<div class="bs-actionsbox">' +
      '<div class="btn-group btn-group-sm btn-block">' +
      '<button type="button" class="actions-btn bs-select-all btn btn-default">' +
      this.options.selectAllText +
      '</button>' +
      '<button type="button" class="actions-btn bs-deselect-all btn btn-default">' +
      this.options.deselectAllText +
      '</button>' +
      '</div>' +
      '</div>'
          : '';
      var donebutton = this.multiple && this.options.doneButton ?
      '<div class="bs-donebutton">' +
      '<div class="btn-group btn-block">' +
      '<button type="button" class="btn btn-sm btn-default">' +
      this.options.doneButtonText +
      '</button>' +
      '</div>' +
      '</div>'
          : '';
      var drop =
          '<div class="btn-group bootstrap-select' + showTick + inputGroup + '">' +
          '<button type="button" class="' + this.options.styleBase + ' dropdown-toggle" data-toggle="dropdown"' + autofocus + ' role="button">' +
          '<span class="filter-option pull-left"></span>&nbsp;' +
          '<span class="bs-caret">' +
          this.options.template.caret +
          '</span>' +
          '</button>' +
          '<div class="dropdown-menu open" role="combobox">' +
          header +
          searchbox +
          actionsbox +
          '<ul class="dropdown-menu inner" role="listbox" aria-expanded="false">' +
          '</ul>' +
          donebutton +
          '</div>' +
          '</div>';

      return $(drop);
    },

    createView: function () {
      var $drop = this.createDropdown(),
          li = this.createLi();

      $drop.find('ul')[0].innerHTML = li;
      return $drop;
    },

    reloadLi: function () {
      //Remove all children.
      this.destroyLi();
      //Re build
      var li = this.createLi();
      this.$menuInner[0].innerHTML = li;
    },

    destroyLi: function () {
      this.$menu.find('li').remove();
    },

    createLi: function () {
      var that = this,
          _li = [],
          optID = 0,
          titleOption = document.createElement('option'),
          liIndex = -1; // increment liIndex whenever a new <li> element is created to ensure liObj is correct

      // Helper functions
      /**
       * @param content
       * @param [index]
       * @param [classes]
       * @param [optgroup]
       * @returns {string}
       */
      var generateLI = function (content, index, classes, optgroup) {
        return '<li' +
            ((typeof classes !== 'undefined' & '' !== classes) ? ' class="' + classes + '"' : '') +
            ((typeof index !== 'undefined' & null !== index) ? ' data-original-index="' + index + '"' : '') +
            ((typeof optgroup !== 'undefined' & null !== optgroup) ? 'data-optgroup="' + optgroup + '"' : '') +
            '>' + content + '</li>';
      };

      /**
       * @param text
       * @param [classes]
       * @param [inline]
       * @param [tokens]
       * @returns {string}
       */
      var generateA = function (text, classes, inline, tokens) {
        return '<a tabindex="0"' +
            (typeof classes !== 'undefined' ? ' class="' + classes + '"' : '') +
            (typeof inline !== 'undefined' ? ' style="' + inline + '"' : '') +
            (that.options.liveSearchNormalize ? ' data-normalized-text="' + normalizeToBase(htmlEscape(text)) + '"' : '') +
            (typeof tokens !== 'undefined' || tokens !== null ? ' data-tokens="' + tokens + '"' : '') +
            ' role="option">' + text +
            '<span class="' + that.options.iconBase + ' ' + that.options.tickIcon + ' check-mark"></span>' +
            '</a>';
      };

      if (this.options.title && !this.multiple) {
        // this option doesn't create a new <li> element, but does add a new option, so liIndex is decreased
        // since liObj is recalculated on every refresh, liIndex needs to be decreased even if the titleOption is already appended
        liIndex--;

        if (!this.$element.find('.bs-title-option').length) {
          // Use native JS to prepend option (faster)
          var element = this.$element[0];
          titleOption.className = 'bs-title-option';
          titleOption.appendChild(document.createTextNode(this.options.title));
          titleOption.value = '';
          element.insertBefore(titleOption, element.firstChild);
          // Check if selected or data-selected attribute is already set on an option. If not, select the titleOption option.
          // the selected item may have been changed by user or programmatically before the bootstrap select plugin runs,
          // if so, the select will have the data-selected attribute
          var $opt = $(element.options[element.selectedIndex]);
          if ($opt.attr('selected') === undefined && this.$element.data('selected') === undefined) {
            titleOption.selected = true;
          }
        }
      }

      this.$element.find('option').each(function (index) {
        var $this = $(this);

        liIndex++;

        if ($this.hasClass('bs-title-option')) return;

        // Get the class and text for the option
        var optionClass = this.className || '',
            inline = this.style.cssText,
            text = $this.data('content') ? $this.data('content') : $this.html(),
            tokens = $this.data('tokens') ? $this.data('tokens') : null,
            subtext = typeof $this.data('subtext') !== 'undefined' ? '<small class="text-muted">' + $this.data('subtext') + '</small>' : '',
            icon = typeof $this.data('icon') !== 'undefined' ? '<span class="' + that.options.iconBase + ' ' + $this.data('icon') + '"></span> ' : '',
            $parent = $this.parent(),
            isOptgroup = $parent[0].tagName === 'OPTGROUP',
            isOptgroupDisabled = isOptgroup && $parent[0].disabled,
            isDisabled = this.disabled || isOptgroupDisabled;

        if (icon !== '' && isDisabled) {
          icon = '<span>' + icon + '</span>';
        }

        if (that.options.hideDisabled && (isDisabled && !isOptgroup || isOptgroupDisabled)) {
          liIndex--;
          return;
        }

        if (!$this.data('content')) {
          // Prepend any icon and append any subtext to the main text.
          text = icon + '<span class="text">' + text + subtext + '</span>';
        }

        if (isOptgroup && $this.data('divider') !== true) {
          if (that.options.hideDisabled && isDisabled) {
            if ($parent.data('allOptionsDisabled') === undefined) {
              var $options = $parent.children();
              $parent.data('allOptionsDisabled', $options.filter(':disabled').length === $options.length);
            }

            if ($parent.data('allOptionsDisabled')) {
              liIndex--;
              return;
            }
          }

          var optGroupClass = ' ' + $parent[0].className || '';

          if ($this.index() === 0) { // Is it the first option of the optgroup?
            optID += 1;

            // Get the opt group label
            var label = $parent[0].label,
                labelSubtext = typeof $parent.data('subtext') !== 'undefined' ? '<small class="text-muted">' + $parent.data('subtext') + '</small>' : '',
                labelIcon = $parent.data('icon') ? '<span class="' + that.options.iconBase + ' ' + $parent.data('icon') + '"></span> ' : '';

            label = labelIcon + '<span class="text">' + label + labelSubtext + '</span>';

            if (index !== 0 && _li.length > 0) { // Is it NOT the first option of the select && are there elements in the dropdown?
              liIndex++;
              _li.push(generateLI('', null, 'divider', optID + 'div'));
            }
            liIndex++;
            _li.push(generateLI(label, null, 'dropdown-header' + optGroupClass, optID));
          }

          if (that.options.hideDisabled && isDisabled) {
            liIndex--;
            return;
          }

          _li.push(generateLI(generateA(text, 'opt ' + optionClass + optGroupClass, inline, tokens), index, '', optID));
        } else if ($this.data('divider') === true) {
          _li.push(generateLI('', index, 'divider'));
        } else if ($this.data('hidden') === true) {
          _li.push(generateLI(generateA(text, optionClass, inline, tokens), index, 'hidden is-hidden'));
        } else {
          var showDivider = this.previousElementSibling && this.previousElementSibling.tagName === 'OPTGROUP';

          // if previous element is not an optgroup and hideDisabled is true
          if (!showDivider && that.options.hideDisabled) {
            // get previous elements
            var $prev = $(this).prevAll();

            for (var i = 0; i < $prev.length; i++) {
              // find the first element in the previous elements that is an optgroup
              if ($prev[i].tagName === 'OPTGROUP') {
                var optGroupDistance = 0;

                // loop through the options in between the current option and the optgroup
                // and check if they are hidden or disabled
                for (var d = 0; d < i; d++) {
                  var prevOption = $prev[d];
                  if (prevOption.disabled || $(prevOption).data('hidden') === true) optGroupDistance++;
                }

                // if all of the options between the current option and the optgroup are hidden or disabled, show the divider
                if (optGroupDistance === i) showDivider = true;

                break;
              }
            }
          }

          if (showDivider) {
            liIndex++;
            _li.push(generateLI('', null, 'divider', optID + 'div'));
          }
          _li.push(generateLI(generateA(text, optionClass, inline, tokens), index));
        }

        that.liObj[index] = liIndex;
      });

      //If we are not multiple, we don't have a selected item, and we don't have a title, select the first element so something is set in the button
      if (!this.multiple && this.$element.find('option:selected').length === 0 && !this.options.title) {
        this.$element.find('option').eq(0).prop('selected', true).attr('selected', 'selected');
      }

      return _li.join('');
    },

    findLis: function () {
      if (this.$lis == null) this.$lis = this.$menu.find('li');
      return this.$lis;
    },

    /**
     * @param [updateLi] defaults to true
     */
    render: function (updateLi) {
      var that = this,
          notDisabled;

      //Update the LI to match the SELECT
      if (updateLi !== false) {
        this.$element.find('option').each(function (index) {
          var $lis = that.findLis().eq(that.liObj[index]);

          that.setDisabled(index, this.disabled || this.parentNode.tagName === 'OPTGROUP' && this.parentNode.disabled, $lis);
          that.setSelected(index, this.selected, $lis);
        });
      }

      this.togglePlaceholder();

      this.tabIndex();

      var selectedItems = this.$element.find('option').map(function () {
        if (this.selected) {
          if (that.options.hideDisabled && (this.disabled || this.parentNode.tagName === 'OPTGROUP' && this.parentNode.disabled)) return;

          var $this = $(this),
              icon = $this.data('icon') && that.options.showIcon ? '<i class="' + that.options.iconBase + ' ' + $this.data('icon') + '"></i> ' : '',
              subtext;

          if (that.options.showSubtext && $this.data('subtext') && !that.multiple) {
            subtext = ' <small class="text-muted">' + $this.data('subtext') + '</small>';
          } else {
            subtext = '';
          }
          if (typeof $this.attr('title') !== 'undefined') {
            return $this.attr('title');
          } else if ($this.data('content') && that.options.showContent) {
            return $this.data('content');
          } else {
            return icon + $this.html() + subtext;
          }
        }
      }).toArray();

      //Fixes issue in IE10 occurring when no default option is selected and at least one option is disabled
      //Convert all the values into a comma delimited string
      var title = !this.multiple ? selectedItems[0] : selectedItems.join(this.options.multipleSeparator);

      //If this is multi select, and the selectText type is count, the show 1 of 2 selected etc..
      if (this.multiple && this.options.selectedTextFormat.indexOf('count') > -1) {
        var max = this.options.selectedTextFormat.split('>');
        if ((max.length > 1 && selectedItems.length > max[1]) || (max.length == 1 && selectedItems.length >= 2)) {
          notDisabled = this.options.hideDisabled ? ', [disabled]' : '';
          var totalCount = this.$element.find('option').not('[data-divider="true"], [data-hidden="true"]' + notDisabled).length,
              tr8nText = (typeof this.options.countSelectedText === 'function') ? this.options.countSelectedText(selectedItems.length, totalCount) : this.options.countSelectedText;
          title = tr8nText.replace('{0}', selectedItems.length.toString()).replace('{1}', totalCount.toString());
        }
      }

      if (this.options.title == undefined) {
        this.options.title = this.$element.attr('title');
      }

      if (this.options.selectedTextFormat == 'static') {
        title = this.options.title;
      }

      //If we dont have a title, then use the default, or if nothing is set at all, use the not selected text
      if (!title) {
        title = typeof this.options.title !== 'undefined' ? this.options.title : this.options.noneSelectedText;
      }

      //strip all html-tags and trim the result
      this.$button.attr('title', $.trim(title.replace(/<[^>]*>?/g, '')));
      this.$button.children('.filter-option').html(title);

      this.$element.trigger('rendered.bs.select');
    },

    /**
     * @param [style]
     * @param [status]
     */
    setStyle: function (style, status) {
      if (this.$element.attr('class')) {
        this.$newElement.addClass(this.$element.attr('class').replace(/selectpicker|mobile-device|bs-select-hidden|validate\[.*\]/gi, ''));
      }

      var buttonClass = style ? style : this.options.style;

      if (status == 'add') {
        this.$button.addClass(buttonClass);
      } else if (status == 'remove') {
        this.$button.removeClass(buttonClass);
      } else {
        this.$button.removeClass(this.options.style);
        this.$button.addClass(buttonClass);
      }
    },

    liHeight: function (refresh) {
      if (!refresh && (this.options.size === false || this.sizeInfo)) return;

      var newElement = document.createElement('div'),
          menu = document.createElement('div'),
          menuInner = document.createElement('ul'),
          divider = document.createElement('li'),
          li = document.createElement('li'),
          a = document.createElement('a'),
          text = document.createElement('span'),
          header = this.options.header && this.$menu.find('.popover-title').length > 0 ? this.$menu.find('.popover-title')[0].cloneNode(true) : null,
          search = this.options.liveSearch ? document.createElement('div') : null,
          actions = this.options.actionsBox && this.multiple && this.$menu.find('.bs-actionsbox').length > 0 ? this.$menu.find('.bs-actionsbox')[0].cloneNode(true) : null,
          doneButton = this.options.doneButton && this.multiple && this.$menu.find('.bs-donebutton').length > 0 ? this.$menu.find('.bs-donebutton')[0].cloneNode(true) : null;

      text.className = 'text';
      newElement.className = this.$menu[0].parentNode.className + ' open';
      menu.className = 'dropdown-menu open';
      menuInner.className = 'dropdown-menu inner';
      divider.className = 'divider';

      text.appendChild(document.createTextNode('Inner text'));
      a.appendChild(text);
      li.appendChild(a);
      menuInner.appendChild(li);
      menuInner.appendChild(divider);
      if (header) menu.appendChild(header);
      if (search) {
        // create a span instead of input as creating an input element is slower
        var input = document.createElement('span');
        search.className = 'bs-searchbox';
        input.className = 'form-control';
        search.appendChild(input);
        menu.appendChild(search);
      }
      if (actions) menu.appendChild(actions);
      menu.appendChild(menuInner);
      if (doneButton) menu.appendChild(doneButton);
      newElement.appendChild(menu);

      document.body.appendChild(newElement);

      var liHeight = a.offsetHeight,
          headerHeight = header ? header.offsetHeight : 0,
          searchHeight = search ? search.offsetHeight : 0,
          actionsHeight = actions ? actions.offsetHeight : 0,
          doneButtonHeight = doneButton ? doneButton.offsetHeight : 0,
          dividerHeight = $(divider).outerHeight(true),
          // fall back to jQuery if getComputedStyle is not supported
          menuStyle = typeof getComputedStyle === 'function' ? getComputedStyle(menu) : false,
          $menu = menuStyle ? null : $(menu),
          menuPadding = {
            vert: parseInt(menuStyle ? menuStyle.paddingTop : $menu.css('paddingTop')) +
                  parseInt(menuStyle ? menuStyle.paddingBottom : $menu.css('paddingBottom')) +
                  parseInt(menuStyle ? menuStyle.borderTopWidth : $menu.css('borderTopWidth')) +
                  parseInt(menuStyle ? menuStyle.borderBottomWidth : $menu.css('borderBottomWidth')),
            horiz: parseInt(menuStyle ? menuStyle.paddingLeft : $menu.css('paddingLeft')) +
                  parseInt(menuStyle ? menuStyle.paddingRight : $menu.css('paddingRight')) +
                  parseInt(menuStyle ? menuStyle.borderLeftWidth : $menu.css('borderLeftWidth')) +
                  parseInt(menuStyle ? menuStyle.borderRightWidth : $menu.css('borderRightWidth'))
          },
          menuExtras =  {
            vert: menuPadding.vert +
                  parseInt(menuStyle ? menuStyle.marginTop : $menu.css('marginTop')) +
                  parseInt(menuStyle ? menuStyle.marginBottom : $menu.css('marginBottom')) + 2,
            horiz: menuPadding.horiz +
                  parseInt(menuStyle ? menuStyle.marginLeft : $menu.css('marginLeft')) +
                  parseInt(menuStyle ? menuStyle.marginRight : $menu.css('marginRight')) + 2
          }

      document.body.removeChild(newElement);

      this.sizeInfo = {
        liHeight: liHeight,
        headerHeight: headerHeight,
        searchHeight: searchHeight,
        actionsHeight: actionsHeight,
        doneButtonHeight: doneButtonHeight,
        dividerHeight: dividerHeight,
        menuPadding: menuPadding,
        menuExtras: menuExtras
      };
    },

    setSize: function () {
      this.findLis();
      this.liHeight();

      if (this.options.header) this.$menu.css('padding-top', 0);
      if (this.options.size === false) return;

      var that = this,
          $menu = this.$menu,
          $menuInner = this.$menuInner,
          $window = $(window),
          selectHeight = this.$newElement[0].offsetHeight,
          selectWidth = this.$newElement[0].offsetWidth,
          liHeight = this.sizeInfo['liHeight'],
          headerHeight = this.sizeInfo['headerHeight'],
          searchHeight = this.sizeInfo['searchHeight'],
          actionsHeight = this.sizeInfo['actionsHeight'],
          doneButtonHeight = this.sizeInfo['doneButtonHeight'],
          divHeight = this.sizeInfo['dividerHeight'],
          menuPadding = this.sizeInfo['menuPadding'],
          menuExtras = this.sizeInfo['menuExtras'],
          notDisabled = this.options.hideDisabled ? '.disabled' : '',
          menuHeight,
          menuWidth,
          getHeight,
          getWidth,
          selectOffsetTop,
          selectOffsetBot,
          selectOffsetLeft,
          selectOffsetRight,
          getPos = function() {
            var pos = that.$newElement.offset(),
                $container = $(that.options.container),
                containerPos;

            if (that.options.container && !$container.is('body')) {
              containerPos = $container.offset();
              containerPos.top += parseInt($container.css('borderTopWidth'));
              containerPos.left += parseInt($container.css('borderLeftWidth'));
            } else {
              containerPos = { top: 0, left: 0 };
            }

            selectOffsetTop = pos.top - containerPos.top - $window.scrollTop();
            selectOffsetBot = $window.height() - selectOffsetTop - selectHeight - containerPos.top;
            selectOffsetLeft = pos.left - containerPos.left - $window.scrollLeft();
            selectOffsetRight = $window.width() - selectOffsetLeft - selectWidth - containerPos.left;
			
			//Setting Dropdown position depending on window width
			var menu_width = that.$menu.width();
			var dropdown_position = that.$menu.position();
			var menu_position = that.$newElement.offset();
			var select_width = parseInt(menu_position.left + menu_width);
			if(select_width > $(window).width() && that.$menu.hasClass('adjust_hg') === false){
				var diff = parseInt(select_width - $(window).width());
				var set_left = parseInt(dropdown_position.left - diff - 10);
				that.$menu.css('left',set_left).addClass('adjust_hg');
			}
          };

      getPos();

      if (this.options.size === 'auto') {
        var getSize = function () {
          var minHeight,
              hasClass = function (className, include) {
                return function (element) {
                    if (include) {
                        return (element.classList ? element.classList.contains(className) : $(element).hasClass(className));
                    } else {
                        return !(element.classList ? element.classList.contains(className) : $(element).hasClass(className));
                    }
                };
              },
              lis = that.$menuInner[0].getElementsByTagName('li'),
              lisVisible = Array.prototype.filter ? Array.prototype.filter.call(lis, hasClass('hidden', false)) : that.$lis.not('.hidden'),
              optGroup = Array.prototype.filter ? Array.prototype.filter.call(lisVisible, hasClass('dropdown-header', true)) : lisVisible.filter('.dropdown-header');

          getPos();
          menuHeight = selectOffsetBot - menuExtras.vert;
          menuWidth = selectOffsetRight - menuExtras.horiz;

          if (that.options.container) {
            if (!$menu.data('height')) $menu.data('height', $menu.height());
            getHeight = $menu.data('height');

            if (!$menu.data('width')) $menu.data('width', $menu.width());
            getWidth = $menu.data('width');
          } else {
            getHeight = $menu.height();
            getWidth = $menu.width();
          }

          if (that.options.dropupAuto) {
            that.$newElement.toggleClass('dropup', selectOffsetTop > selectOffsetBot && (menuHeight - menuExtras.vert) < getHeight);
          }

          if (that.$newElement.hasClass('dropup')) {
            menuHeight = selectOffsetTop - menuExtras.vert;
          }

          if (that.options.dropdownAlignRight === 'auto') {
            $menu.toggleClass('dropdown-menu-right', selectOffsetLeft > selectOffsetRight && (menuWidth - menuExtras.horiz) < (getWidth - selectWidth));
          }

          if ((lisVisible.length + optGroup.length) > 3) {
            minHeight = liHeight * 3 + menuExtras.vert - 2;
          } else {
            minHeight = 0;
          }

          $menu.css({
            'max-height': menuHeight + 'px',
            'overflow': 'hidden',
            'min-height': minHeight + headerHeight + searchHeight + actionsHeight + doneButtonHeight + 'px'
          });
          $menuInner.css({
            'max-height': menuHeight - headerHeight - searchHeight - actionsHeight - doneButtonHeight - menuPadding.vert + 'px',
            'overflow-y': 'auto',
            'min-height': Math.max(minHeight - menuPadding.vert, 0) + 'px'
          });
        };
        getSize();
        this.$searchbox.off('input.getSize propertychange.getSize').on('input.getSize propertychange.getSize', getSize);
        $window.off('resize.getSize scroll.getSize').on('resize.getSize scroll.getSize', getSize);
      } else if (this.options.size && this.options.size != 'auto' && this.$lis.not(notDisabled).length > this.options.size) {
        var optIndex = this.$lis.not('.divider').not(notDisabled).children().slice(0, this.options.size).last().parent().index(),
            divLength = this.$lis.slice(0, optIndex + 1).filter('.divider').length;
        menuHeight = liHeight * this.options.size + divLength * divHeight + menuPadding.vert;

        if (that.options.container) {
          if (!$menu.data('height')) $menu.data('height', $menu.height());
          getHeight = $menu.data('height');
        } else {
          getHeight = $menu.height();
        }

        if (that.options.dropupAuto) {
          //noinspection JSUnusedAssignment
          this.$newElement.toggleClass('dropup', selectOffsetTop > selectOffsetBot && (menuHeight - menuExtras.vert) < getHeight);
        }
        $menu.css({
          'max-height': menuHeight + headerHeight + searchHeight + actionsHeight + doneButtonHeight + 'px',
          'overflow': 'hidden',
          'min-height': ''
        });
        $menuInner.css({
          'max-height': menuHeight - menuPadding.vert + 'px',
          'overflow-y': 'auto',
          'min-height': ''
        });
      }
    },

    setWidth: function () {
      if (this.options.width === 'auto') {
        this.$menu.css('min-width', '0');

        // Get correct width if element is hidden
        var $selectClone = this.$menu.parent().clone().appendTo('body'),
            $selectClone2 = this.options.container ? this.$newElement.clone().appendTo('body') : $selectClone,
            ulWidth = $selectClone.children('.dropdown-menu').outerWidth(),
            btnWidth = $selectClone2.css('width', 'auto').children('button').outerWidth();

        $selectClone.remove();
        $selectClone2.remove();

        // Set width to whatever's larger, button title or longest option
        this.$newElement.css('width', Math.max(ulWidth, btnWidth) + 'px');
      } else if (this.options.width === 'fit') {
        // Remove inline min-width so width can be changed from 'auto'
        this.$menu.css('min-width', '');
        this.$newElement.css('width', '').addClass('fit-width');
      } else if (this.options.width) {
        // Remove inline min-width so width can be changed from 'auto'
        this.$menu.css('min-width', '');
        this.$newElement.css('width', this.options.width);
      } else {
        // Remove inline min-width/width so width can be changed
        this.$menu.css('min-width', '');
        this.$newElement.css('width', '');
      }
      // Remove fit-width class if width is changed programmatically
      if (this.$newElement.hasClass('fit-width') && this.options.width !== 'fit') {
        this.$newElement.removeClass('fit-width');
      }
    },

    selectPosition: function () {
      this.$bsContainer = $('<div class="bs-container" />');

      var that = this,
          $container = $(this.options.container),
          pos,
          containerPos,
          actualHeight,
          getPlacement = function ($element) {
            that.$bsContainer.addClass($element.attr('class').replace(/form-control|fit-width/gi, '')).toggleClass('dropup', $element.hasClass('dropup'));
            pos = $element.offset();

            if (!$container.is('body')) {
              containerPos = $container.offset();
              containerPos.top += parseInt($container.css('borderTopWidth')) - $container.scrollTop();
              containerPos.left += parseInt($container.css('borderLeftWidth')) - $container.scrollLeft();
            } else {
              containerPos = { top: 0, left: 0 };
            }

            actualHeight = $element.hasClass('dropup') ? 0 : $element[0].offsetHeight;

            that.$bsContainer.css({
              'top': pos.top - containerPos.top + actualHeight,
              'left': pos.left - containerPos.left,
              'width': $element[0].offsetWidth
            });
          };

      this.$button.on('click', function () {
        var $this = $(this);

        if (that.isDisabled()) {
          return;
        }

        getPlacement(that.$newElement);

        that.$bsContainer
          .appendTo(that.options.container)
          .toggleClass('open', !$this.hasClass('open'))
          .append(that.$menu);
      });

      $(window).on('resize scroll', function () {
        getPlacement(that.$newElement);
      });

      this.$element.on('hide.bs.select', function () {
        that.$menu.data('height', that.$menu.height());
        that.$bsContainer.detach();
      });
    },

    /**
     * @param {number} index - the index of the option that is being changed
     * @param {boolean} selected - true if the option is being selected, false if being deselected
     * @param {JQuery} $lis - the 'li' element that is being modified
     */
    setSelected: function (index, selected, $lis) {
      if (!$lis) {
        this.togglePlaceholder(); // check if setSelected is being called by changing the value of the select
        $lis = this.findLis().eq(this.liObj[index]);
      }

      $lis.toggleClass('selected', selected).find('a').attr('aria-selected', selected);
    },

    /**
     * @param {number} index - the index of the option that is being disabled
     * @param {boolean} disabled - true if the option is being disabled, false if being enabled
     * @param {JQuery} $lis - the 'li' element that is being modified
     */
    setDisabled: function (index, disabled, $lis) {
      if (!$lis) {
        $lis = this.findLis().eq(this.liObj[index]);
      }

      if (disabled) {
        $lis.addClass('disabled').children('a').attr('href', '#').attr('tabindex', -1).attr('aria-disabled', true);
      } else {
        $lis.removeClass('disabled').children('a').removeAttr('href').attr('tabindex', 0).attr('aria-disabled', false);
      }
    },

    isDisabled: function () {
      return this.$element[0].disabled;
    },

    checkDisabled: function () {
      var that = this;

      if (this.isDisabled()) {
        this.$newElement.addClass('disabled');
        this.$button.addClass('disabled').attr('tabindex', -1);
      } else {
        if (this.$button.hasClass('disabled')) {
          this.$newElement.removeClass('disabled');
          this.$button.removeClass('disabled');
        }

        if (this.$button.attr('tabindex') == -1 && !this.$element.data('tabindex')) {
          this.$button.removeAttr('tabindex');
        }
      }

      this.$button.click(function () {
        return !that.isDisabled();
      });
    },

    togglePlaceholder: function () {
      var value = this.$element.val();
      this.$button.toggleClass('bs-placeholder', value === null || value === '');
    },

    tabIndex: function () {
      if (this.$element.data('tabindex') !== this.$element.attr('tabindex') && 
        (this.$element.attr('tabindex') !== -98 && this.$element.attr('tabindex') !== '-98')) {
        this.$element.data('tabindex', this.$element.attr('tabindex'));
        this.$button.attr('tabindex', this.$element.data('tabindex'));
      }

      this.$element.attr('tabindex', -98);
    },

    clickListener: function () {
      var that = this,
          $document = $(document);

      this.$newElement.on('touchstart.dropdown', '.dropdown-menu', function (e) {
        e.stopPropagation();
      });

      $document.data('spaceSelect', false);

      this.$button.on('keyup', function (e) {
        if (/(32)/.test(e.keyCode.toString(10)) && $document.data('spaceSelect')) {
            e.preventDefault();
            $document.data('spaceSelect', false);
        }
      });

      this.$button.on('click', function () {
        that.setSize();
      });

      this.$element.on('shown.bs.select', function () {
        if (!that.options.liveSearch && !that.multiple) {
          that.$menuInner.find('.selected a').focus();
        } else if (!that.multiple) {
          var selectedIndex = that.liObj[that.$element[0].selectedIndex];

          if (typeof selectedIndex !== 'number' || that.options.size === false) return;

          // scroll to selected option
          var offset = that.$lis.eq(selectedIndex)[0].offsetTop - that.$menuInner[0].offsetTop;
          offset = offset - that.$menuInner[0].offsetHeight/2 + that.sizeInfo.liHeight/2;
          that.$menuInner[0].scrollTop = offset;
        }
      });

      this.$menuInner.on('click', 'li a', function (e) {
        var $this = $(this),
            clickedIndex = $this.parent().data('originalIndex'),
            prevValue = that.$element.val(),
            prevIndex = that.$element.prop('selectedIndex'),
            triggerChange = true;

        // Don't close on multi choice menu
        if (that.multiple && that.options.maxOptions !== 1) {
          e.stopPropagation();
        }

        e.preventDefault();

        //Don't run if we have been disabled
        if (!that.isDisabled() && !$this.parent().hasClass('disabled')) {
          var $options = that.$element.find('option'),
              $option = $options.eq(clickedIndex),
              state = $option.prop('selected'),
              $optgroup = $option.parent('optgroup'),
              maxOptions = that.options.maxOptions,
              maxOptionsGrp = $optgroup.data('maxOptions') || false;

          if (!that.multiple) { // Deselect all others if not multi select box
            $options.prop('selected', false);
            $option.prop('selected', true);
            that.$menuInner.find('.selected').removeClass('selected').find('a').attr('aria-selected', false);
            that.setSelected(clickedIndex, true);
          } else { // Toggle the one we have chosen if we are multi select.
            $option.prop('selected', !state);
            that.setSelected(clickedIndex, !state);
            $this.blur();

            if (maxOptions !== false || maxOptionsGrp !== false) {
              var maxReached = maxOptions < $options.filter(':selected').length,
                  maxReachedGrp = maxOptionsGrp < $optgroup.find('option:selected').length;

              if ((maxOptions && maxReached) || (maxOptionsGrp && maxReachedGrp)) {
                if (maxOptions && maxOptions == 1) {
                  $options.prop('selected', false);
                  $option.prop('selected', true);
                  that.$menuInner.find('.selected').removeClass('selected');
                  that.setSelected(clickedIndex, true);
                } else if (maxOptionsGrp && maxOptionsGrp == 1) {
                  $optgroup.find('option:selected').prop('selected', false);
                  $option.prop('selected', true);
                  var optgroupID = $this.parent().data('optgroup');
                  that.$menuInner.find('[data-optgroup="' + optgroupID + '"]').removeClass('selected');
                  that.setSelected(clickedIndex, true);
                } else {
                  var maxOptionsText = typeof that.options.maxOptionsText === 'string' ? [that.options.maxOptionsText, that.options.maxOptionsText] : that.options.maxOptionsText,
                      maxOptionsArr = typeof maxOptionsText === 'function' ? maxOptionsText(maxOptions, maxOptionsGrp) : maxOptionsText,
                      maxTxt = maxOptionsArr[0].replace('{n}', maxOptions),
                      maxTxtGrp = maxOptionsArr[1].replace('{n}', maxOptionsGrp),
                      $notify = $('<div class="notify"></div>');
                  // If {var} is set in array, replace it
                  /** @deprecated */
                  if (maxOptionsArr[2]) {
                    maxTxt = maxTxt.replace('{var}', maxOptionsArr[2][maxOptions > 1 ? 0 : 1]);
                    maxTxtGrp = maxTxtGrp.replace('{var}', maxOptionsArr[2][maxOptionsGrp > 1 ? 0 : 1]);
                  }

                  $option.prop('selected', false);

                  that.$menu.append($notify);

                  if (maxOptions && maxReached) {
                    $notify.append($('<div>' + maxTxt + '</div>'));
                    triggerChange = false;
                    that.$element.trigger('maxReached.bs.select');
                  }

                  if (maxOptionsGrp && maxReachedGrp) {
                    $notify.append($('<div>' + maxTxtGrp + '</div>'));
                    triggerChange = false;
                    that.$element.trigger('maxReachedGrp.bs.select');
                  }

                  setTimeout(function () {
                    that.setSelected(clickedIndex, false);
                  }, 10);

                  $notify.delay(750).fadeOut(300, function () {
                    $(this).remove();
                  });
                }
              }
            }
          }

          if (!that.multiple || (that.multiple && that.options.maxOptions === 1)) {
            that.$button.focus();
          } else if (that.options.liveSearch) {
            that.$searchbox.focus();
          }

          // Trigger select 'change'
          if (triggerChange) {
            if ((prevValue != that.$element.val() && that.multiple) || (prevIndex != that.$element.prop('selectedIndex') && !that.multiple)) {
              // $option.prop('selected') is current option state (selected/unselected). state is previous option state.
              changed_arguments = [clickedIndex, $option.prop('selected'), state];
              that.$element
                .triggerNative('change');
            }
          }
        }
      });

      this.$menu.on('click', 'li.disabled a, .popover-title, .popover-title :not(.close)', function (e) {
        if (e.currentTarget == this) {
          e.preventDefault();
          e.stopPropagation();
          if (that.options.liveSearch && !$(e.target).hasClass('close')) {
            that.$searchbox.focus();
          } else {
            that.$button.focus();
          }
        }
      });

      this.$menuInner.on('click', '.divider, .dropdown-header', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (that.options.liveSearch) {
          that.$searchbox.focus();
        } else {
          that.$button.focus();
        }
      });

      this.$menu.on('click', '.popover-title .close', function () {
        that.$button.click();
      });

      this.$searchbox.on('click', function (e) {
        e.stopPropagation();
      });

      this.$menu.on('click', '.actions-btn', function (e) {
        if (that.options.liveSearch) {
          that.$searchbox.focus();
        } else {
          that.$button.focus();
        }

        e.preventDefault();
        e.stopPropagation();

        if ($(this).hasClass('bs-select-all')) {
          that.selectAll();
        } else {
          that.deselectAll();
        }
      });

      this.$element.change(function () {
        that.render(false);
        that.$element.trigger('changed.bs.select', changed_arguments);
        changed_arguments = null;
      });
    },

    liveSearchListener: function () {
      var that = this,
          $no_results = $('<li class="no-results"></li>');

      this.$button.on('click.dropdown.data-api touchstart.dropdown.data-api', function () {
        that.$menuInner.find('.active').removeClass('active');
        if (!!that.$searchbox.val()) {
          that.$searchbox.val('');
          that.$lis.not('.is-hidden').removeClass('hidden');
          if (!!$no_results.parent().length) $no_results.remove();
        }
        if (!that.multiple) that.$menuInner.find('.selected').addClass('active');
        setTimeout(function () {
          that.$searchbox.focus();
        }, 10);
      });

      this.$searchbox.on('click.dropdown.data-api focus.dropdown.data-api touchend.dropdown.data-api', function (e) {
        e.stopPropagation();
      });

      this.$searchbox.on('input propertychange', function () {
        if (that.$searchbox.val()) {
          var $searchBase = that.$lis.not('.is-hidden').removeClass('hidden').children('a');
          if (that.options.liveSearchNormalize) {
            $searchBase = $searchBase.not(':a' + that._searchStyle() + '("' + normalizeToBase(that.$searchbox.val()) + '")');
          } else {
            $searchBase = $searchBase.not(':' + that._searchStyle() + '("' + that.$searchbox.val() + '")');
          }
          $searchBase.parent().addClass('hidden');

          that.$lis.filter('.dropdown-header').each(function () {
            var $this = $(this),
                optgroup = $this.data('optgroup');

            if (that.$lis.filter('[data-optgroup=' + optgroup + ']').not($this).not('.hidden').length === 0) {
              $this.addClass('hidden');
              that.$lis.filter('[data-optgroup=' + optgroup + 'div]').addClass('hidden');
            }
          });

          var $lisVisible = that.$lis.not('.hidden');

          // hide divider if first or last visible, or if followed by another divider
          $lisVisible.each(function (index) {
            var $this = $(this);

            if ($this.hasClass('divider') && (
              $this.index() === $lisVisible.first().index() ||
              $this.index() === $lisVisible.last().index() ||
              $lisVisible.eq(index + 1).hasClass('divider'))) {
              $this.addClass('hidden');
            }
          });

          if (!that.$lis.not('.hidden, .no-results').length) {
            if (!!$no_results.parent().length) {
              $no_results.remove();
            }
            $no_results.html(that.options.noneResultsText.replace('{0}', '"' + htmlEscape(that.$searchbox.val()) + '"')).show();
            that.$menuInner.append($no_results);
          } else if (!!$no_results.parent().length) {
            $no_results.remove();
          }
        } else {
          that.$lis.not('.is-hidden').removeClass('hidden');
          if (!!$no_results.parent().length) {
            $no_results.remove();
          }
        }

        that.$lis.filter('.active').removeClass('active');
        if (that.$searchbox.val()) that.$lis.not('.hidden, .divider, .dropdown-header').eq(0).addClass('active').children('a').focus();
        $(this).focus();
      });
    },

    _searchStyle: function () {
      var styles = {
        begins: 'ibegins',
        startsWith: 'ibegins'
      };

      return styles[this.options.liveSearchStyle] || 'icontains';
    },

    val: function (value) {
      if (typeof value !== 'undefined') {
        this.$element.val(value);
        this.render();

        return this.$element;
      } else {
        return this.$element.val();
      }
    },

    changeAll: function (status) {
      if (!this.multiple) return;
      if (typeof status === 'undefined') status = true;

      this.findLis();

      var $options = this.$element.find('option'),
          $lisVisible = this.$lis.not('.divider, .dropdown-header, .disabled, .hidden'),
          lisVisLen = $lisVisible.length,
          selectedOptions = [];
          
      if (status) {
        if ($lisVisible.filter('.selected').length === $lisVisible.length) return;
      } else {
        if ($lisVisible.filter('.selected').length === 0) return;
      }
          
      $lisVisible.toggleClass('selected', status);

      for (var i = 0; i < lisVisLen; i++) {
        var origIndex = $lisVisible[i].getAttribute('data-original-index');
        selectedOptions[selectedOptions.length] = $options.eq(origIndex)[0];
      }

      $(selectedOptions).prop('selected', status);

      this.render(false);

      this.togglePlaceholder();

      this.$element
        .triggerNative('change');
    },

    selectAll: function () {
      return this.changeAll(true);
    },

    deselectAll: function () {
      return this.changeAll(false);
    },

    toggle: function (e) {
      e = e || window.event;

      if (e) e.stopPropagation();

      this.$button.trigger('click');
    },

    keydown: function (e) {
      var $this = $(this),
          $parent = $this.is('input') ? $this.parent().parent() : $this.parent(),
          $items,
          that = $parent.data('this'),
          index,
          next,
          first,
          last,
          prev,
          nextPrev,
          prevIndex,
          isActive,
          selector = ':not(.disabled, .hidden, .dropdown-header, .divider)',
          keyCodeMap = {
            32: ' ',
            48: '0',
            49: '1',
            50: '2',
            51: '3',
            52: '4',
            53: '5',
            54: '6',
            55: '7',
            56: '8',
            57: '9',
            59: ';',
            65: 'a',
            66: 'b',
            67: 'c',
            68: 'd',
            69: 'e',
            70: 'f',
            71: 'g',
            72: 'h',
            73: 'i',
            74: 'j',
            75: 'k',
            76: 'l',
            77: 'm',
            78: 'n',
            79: 'o',
            80: 'p',
            81: 'q',
            82: 'r',
            83: 's',
            84: 't',
            85: 'u',
            86: 'v',
            87: 'w',
            88: 'x',
            89: 'y',
            90: 'z',
            96: '0',
            97: '1',
            98: '2',
            99: '3',
            100: '4',
            101: '5',
            102: '6',
            103: '7',
            104: '8',
            105: '9'
          };
	  if ( typeof(that)=="undefined"){return;}
      if (that.options.liveSearch) $parent = $this.parent().parent();

      if (that.options.container) $parent = that.$menu;

      $items = $('[role="listbox"] li', $parent);

      isActive = that.$newElement.hasClass('open');

      if (!isActive && (e.keyCode >= 48 && e.keyCode <= 57 || e.keyCode >= 96 && e.keyCode <= 105 || e.keyCode >= 65 && e.keyCode <= 90)) {
        if (!that.options.container) {
          that.setSize();
          that.$menu.parent().addClass('open');
          isActive = true;
        } else {
          that.$button.trigger('click');
        }
        that.$searchbox.focus();
        return;
      }

      if (that.options.liveSearch) {
        if (/(^9$|27)/.test(e.keyCode.toString(10)) && isActive) {
          e.preventDefault();
          e.stopPropagation();
          that.$button.click().focus();
        }
        // $items contains li elements when liveSearch is enabled
        $items = $('[role="listbox"] li' + selector, $parent);
        if (!$this.val() && !/(38|40)/.test(e.keyCode.toString(10))) {
          if ($items.filter('.active').length === 0) {
            $items = that.$menuInner.find('li');
            if (that.options.liveSearchNormalize) {
              $items = $items.filter(':a' + that._searchStyle() + '(' + normalizeToBase(keyCodeMap[e.keyCode]) + ')');
            } else {
              $items = $items.filter(':' + that._searchStyle() + '(' + keyCodeMap[e.keyCode] + ')');
            }
          }
        }
      }

      if (!$items.length) return;

      if (/(38|40)/.test(e.keyCode.toString(10))) {
        index = $items.index($items.find('a').filter(':focus').parent());
        first = $items.filter(selector).first().index();
        last = $items.filter(selector).last().index();
        next = $items.eq(index).nextAll(selector).eq(0).index();
        prev = $items.eq(index).prevAll(selector).eq(0).index();
        nextPrev = $items.eq(next).prevAll(selector).eq(0).index();

        if (that.options.liveSearch) {
          $items.each(function (i) {
            if (!$(this).hasClass('disabled')) {
              $(this).data('index', i);
            }
          });
          index = $items.index($items.filter('.active'));
          first = $items.first().data('index');
          last = $items.last().data('index');
          next = $items.eq(index).nextAll().eq(0).data('index');
          prev = $items.eq(index).prevAll().eq(0).data('index');
          nextPrev = $items.eq(next).prevAll().eq(0).data('index');
        }

        prevIndex = $this.data('prevIndex');

        if (e.keyCode == 38) {
          if (that.options.liveSearch) index--;
          if (index != nextPrev && index > prev) index = prev;
          if (index < first) index = first;
          if (index == prevIndex) index = last;
        } else if (e.keyCode == 40) {
          if (that.options.liveSearch) index++;
          if (index == -1) index = 0;
          if (index != nextPrev && index < next) index = next;
          if (index > last) index = last;
          if (index == prevIndex) index = first;
        }

        $this.data('prevIndex', index);

        if (!that.options.liveSearch) {
          $items.eq(index).children('a').focus();
        } else {
          e.preventDefault();
          if (!$this.hasClass('dropdown-toggle')) {
            $items.removeClass('active').eq(index).addClass('active').children('a').focus();
            $this.focus();
          }
        }

      } else if (!$this.is('input')) {
        var keyIndex = [],
            count,
            prevKey;

        $items.each(function () {
          if (!$(this).hasClass('disabled')) {
            if ($.trim($(this).children('a').text().toLowerCase()).substring(0, 1) == keyCodeMap[e.keyCode]) {
              keyIndex.push($(this).index());
            }
          }
        });

        count = $(document).data('keycount');
        count++;
        $(document).data('keycount', count);

        prevKey = $.trim($(':focus').text().toLowerCase()).substring(0, 1);

        if (prevKey != keyCodeMap[e.keyCode]) {
          count = 1;
          $(document).data('keycount', count);
        } else if (count >= keyIndex.length) {
          $(document).data('keycount', 0);
          if (count > keyIndex.length) count = 1;
        }

        $items.eq(keyIndex[count - 1]).children('a').focus();
      }

      // Select focused option if "Enter", "Spacebar" or "Tab" (when selectOnTab is true) are pressed inside the menu.
      if ((/(13|32)/.test(e.keyCode.toString(10)) || (/(^9$)/.test(e.keyCode.toString(10)) && that.options.selectOnTab)) && isActive) {
        if (!/(32)/.test(e.keyCode.toString(10))) e.preventDefault();
        if (!that.options.liveSearch) {
          var elem = $(':focus');
          elem.click();
          // Bring back focus for multiselects
          elem.focus();
          // Prevent screen from scrolling if the user hit the spacebar
          e.preventDefault();
          // Fixes spacebar selection of dropdown items in FF & IE
          $(document).data('spaceSelect', true);
        } else if (!/(32)/.test(e.keyCode.toString(10))) {
          that.$menuInner.find('.active a').click();
          $this.focus();
        }
        $(document).data('keycount', 0);
      }

      if ((/(^9$|27)/.test(e.keyCode.toString(10)) && isActive && (that.multiple || that.options.liveSearch)) || (/(27)/.test(e.keyCode.toString(10)) && !isActive)) {
        that.$menu.parent().removeClass('open');
        if (that.options.container) that.$newElement.removeClass('open');
        that.$button.focus();
      }
    },

    mobile: function () {
      this.$element.addClass('mobile-device');
    },

    refresh: function () {
      this.$lis = null;
      this.liObj = {};
      this.reloadLi();
      this.render();
      this.checkDisabled();
      this.liHeight(true);
      this.setStyle();
      this.setWidth();
      if (this.$lis) this.$searchbox.trigger('propertychange');

      this.$element.trigger('refreshed.bs.select');
    },

    hide: function () {
      this.$newElement.hide();
    },

    show: function () {
      this.$newElement.show();
    },

    remove: function () {
      this.$newElement.remove();
      this.$element.remove();
    },

    destroy: function () {
      this.$newElement.before(this.$element).remove();

      if (this.$bsContainer) {
        this.$bsContainer.remove();
      } else {
        this.$menu.remove();
      }

      this.$element
        .off('.bs.select')
        .removeData('selectpicker')
        .removeClass('bs-select-hidden selectpicker');
    }
  };

  // SELECTPICKER PLUGIN DEFINITION
  // ==============================
  function Plugin(option, event) {
    // get the args of the outer function..
    var args = arguments;
    // The arguments of the function are explicitly re-defined from the argument list, because the shift causes them
    // to get lost/corrupted in android 2.3 and IE9 #715 #775
    var _option = option,
        _event = event;
    [].shift.apply(args);

    var value;
    var chain = this.each(function () {
      var $this = $(this);
      if ($this.is('select')) {
        var data = $this.data('selectpicker'),
            options = typeof _option == 'object' && _option;

        if (!data) {
          var config = $.extend({}, Selectpicker.DEFAULTS, $.fn.selectpicker.defaults || {}, $this.data(), options);
          config.template = $.extend({}, Selectpicker.DEFAULTS.template, ($.fn.selectpicker.defaults ? $.fn.selectpicker.defaults.template : {}), $this.data().template, options.template);
          $this.data('selectpicker', (data = new Selectpicker(this, config, _event)));
        } else if (options) {
          for (var i in options) {
            if (options.hasOwnProperty(i)) {
              data.options[i] = options[i];
            }
          }
        }

        if (typeof _option == 'string') {
          if (data[_option] instanceof Function) {
            value = data[_option].apply(data, args);
          } else {
            value = data.options[_option];
          }
        }
      }
    });

    if (typeof value !== 'undefined') {
      //noinspection JSUnusedAssignment
      return value;
    } else {
      return chain;
    }
  }

  var old = $.fn.selectpicker;
  $.fn.selectpicker = Plugin;
  $.fn.selectpicker.Constructor = Selectpicker;

  // SELECTPICKER NO CONFLICT
  // ========================
  $.fn.selectpicker.noConflict = function () {
    $.fn.selectpicker = old;
    return this;
  };

  $(document)
      .data('keycount', 0)
      .on('keydown.bs.select', '.bootstrap-select [data-toggle=dropdown], .bootstrap-select [role="listbox"], .bs-searchbox input', Selectpicker.prototype.keydown)
      .on('focusin.modal', '.bootstrap-select [data-toggle=dropdown], .bootstrap-select [role="listbox"], .bs-searchbox input', function (e) {
        e.stopPropagation();
      });

  // SELECTPICKER DATA-API
  // =====================
  $(window).on('load.bs.select.data-api', function () {
    $('.selectpicker').each(function () {
      var $selectpicker = $(this);
      Plugin.call($selectpicker, $selectpicker.data());
    })
  });
})(jQuery);


}));
!function ($) {

    "use strict"; // jshint ;_;

    /* TYPEAHEAD PUBLIC CLASS DEFINITION
     * ================================= */

    var Typeahead = function (element, options) {

        //deal with scrollBar
        var defaultOptions = $.fn.typeahead.defaults;
        if (options.scrollBar) {
            options.items = 500;
            options.menu = '<ul class="typeahead dropdown-menu" style="max-height:220px;overflow:auto;"></ul>';
        }

        var that = this;
        that.$element = $(element);
        that.options = $.extend({}, $.fn.typeahead.defaults, options);
        that.$menu = $(that.options.menu).insertAfter(that.$element);

        // Method overrides
        that.eventSupported = that.options.eventSupported || that.eventSupported;
        that.grepper = that.options.grepper || that.grepper;
        that.highlighter = that.options.highlighter || that.highlighter;
        that.lookup = that.options.lookup || that.lookup;
        that.matcher = that.options.matcher || that.matcher;
        that.render = that.options.render || that.render;
        that.onSelect = that.options.onSelect || null;
        that.sorter = that.options.sorter || that.sorter;
        that.source = that.options.source || that.source;
        that.displayField = that.options.displayField || that.displayField;
        that.valueField = that.options.valueField || that.valueField;
        that.autoSelect = that.options.autoSelect || that.autoSelect;
				
				if (that.options.ajax) {
            var ajax = that.options.ajax;

            if (typeof ajax === 'string') {
                that.ajax = $.extend({}, $.fn.typeahead.defaults.ajax, {
                    url: ajax
                });
            } else {
                if (typeof ajax.displayField === 'string') {
                    that.displayField = that.options.displayField = ajax.displayField;
                }
                if (typeof ajax.valueField === 'string') {
                    that.valueField = that.options.valueField = ajax.valueField;
                }

                that.ajax = $.extend({}, $.fn.typeahead.defaults.ajax, ajax);
            }

            if (!that.ajax.url) {
                that.ajax = null;
            }
            that.query = "";
        } else {
            that.source = that.options.source;
            that.ajax = null;
        }
        that.shown = false;
        that.listen();
    };

    Typeahead.prototype = {
        constructor: Typeahead,
        //=============================================================================================================
        //  Utils
        //  Check if an event is supported by the browser eg. 'keypress'
        //  * This was included to handle the "exhaustive deprecation" of jQuery.browser in jQuery 1.8
        //=============================================================================================================
        eventSupported: function (eventName) {
            var isSupported = (eventName in this.$element);

            if (!isSupported) {
                this.$element.setAttribute(eventName, 'return;');
                isSupported = typeof this.$element[eventName] === 'function';
            }

            return isSupported;
        },
        select: function () {
            var $selectedItem = this.$menu.find('.active');
            if($selectedItem.length) {
                var value = $selectedItem.attr('data-value');
                var text = this.$menu.find('.active a').text();

                if (this.options.onSelect) {
                    this.options.onSelect({
                        value: value,
                        text: text
                    });
                }
                this.$element
                    .val(this.updater(text))
                    .change();
            }
            return this.hide();
        },
        updater: function (item) {
					var m  = this.$element.attr('data-provide');
					var ms = this.$element.attr('data-seperator');
					if(m === 'multiple')
					{
						if(ms === 'newline')
							return this.$element.val().replace(/[^\n]*$/,'')+item;
						else if(ms == 'semicolon')
							return this.$element.val().replace(/[^;]*$/,'')+item;
						else
							return this.$element.val().replace(/[^,]*$/,'')+item;	
					}
					else
					{
						return item;
					}
          
				},
        show: function () {
            var pos = $.extend({}, this.$element.position(), {
                height: this.$element[0].offsetHeight
            });
			
			// Checking if menu overlaps body element
			if(pos.top + this.$menu.height() > $(window).height()){	
				pos.top = pos.top - pos.height - this.$menu.height() - 15;
			}
			
			if(this.$menu.height() + pos.top > $(window).height()){
				var menuHeight = this.$menu.height() + pos.top;
				var windowHeight = $(window).height();
				var diff = (menuHeight - windowHeight);
				this.$menu.css(
					'max-height',''+(menuHeight-diff)+'px'
				).css('overflow-y','scroll');
			}
			
			this.$menu.css({
                top: pos.top + pos.height,
				left: pos.left
            });
			
            if(this.options.alignWidth){
				// Default
				//var width = $(this.$element[0]).outerWidth();
				
				// Setting dynamic width based on the search results
				// Modified
				var width = this.$menu.width();
				var menuWidth = (this.$menu.width()+pos.left);
				var windowWidth = $(window).width();
				if(menuWidth > windowWidth){
					var diff = (menuWidth - windowWidth + 200);
					width = (menuWidth - diff);
				}
				
				this.$menu.css({
                    width: width
                });
            }
			
            this.$menu.show();
            this.shown = true;
            return this;
        },
        hide: function () {
            this.$menu.hide();
			this.$menu.css({
                    width: ''
                });
            this.shown = false;
            return this;
        },
        ajaxLookup: function () {

            var query = $.trim(this.$element.val());

            if (query === this.query) {
                return this;
            }

            // Query changed
            this.query = query;

            // Cancel last timer if set
            if (this.ajax.timerId) {
                clearTimeout(this.ajax.timerId);
                this.ajax.timerId = null;
            }

            if (!query || query.length < this.ajax.triggerLength) {
                // cancel the ajax callback if in progress
                if (this.ajax.xhr) {
                    this.ajax.xhr.abort();
                    this.ajax.xhr = null;
                    this.ajaxToggleLoadClass(false);
                }

                return this.shown ? this.hide() : this;
            }

            function execute() {
                this.ajaxToggleLoadClass(true);

                // Cancel last call if already in progress
                if (this.ajax.xhr)
                    this.ajax.xhr.abort();

                var params = this.ajax.preDispatch ? this.ajax.preDispatch(query) : {
                    query: query
                };
                this.ajax.xhr = $.ajax({
                    url: this.ajax.url,
                    data: params,
                    success: $.proxy(this.ajaxSource, this),
                    type: this.ajax.method || 'get',
                    dataType: 'json',
                    headers: this.ajax.headers || {}
                });
                this.ajax.timerId = null;
            }

            // Query is good to send, set a timer
            this.ajax.timerId = setTimeout($.proxy(execute, this), this.ajax.timeout);

            return this;
        },
        ajaxSource: function (data) {
            this.ajaxToggleLoadClass(false);
            var that = this, items;
            if (!that.ajax.xhr)
                return;
            if (that.ajax.preProcess) {
                data = that.ajax.preProcess(data);
            }
            // Save for selection retreival
            that.ajax.data = data;

            // Manipulate objects
            items = that.grepper(that.ajax.data) || [];
            if (!items.length) {
                return that.shown ? that.hide() : that;
            }

            that.ajax.xhr = null;
            return that.render(items.slice(0, that.options.items)).show();
        },
        ajaxToggleLoadClass: function (enable) {
            if (!this.ajax.loadingClass)
                return;
            this.$element.toggleClass(this.ajax.loadingClass, enable);
        },
        lookup: function (event) {
            var that = this, items;
            if (that.ajax) {
                that.ajaxer();
            }
            else {
                that.query = that.$element.val();
                if (!that.query) {
                    return that.shown ? that.hide() : that;
                }

                items = that.grepper(that.source);

				//console.log(items);
                if (!items) {
                    return that.shown ? that.hide() : that;
                }
                //Bhanu added a custom message- Result not Found when no result is found
                if (items.length == 0) {
                    //items[0] = {'id': -21, 'name': "Result not Found"}
					return that.render(items).hide();
                }
                return that.render(items.slice(0, that.options.items)).hide().show();
            }
        },
       	matcher: function (item) {
            var m  = this.$element.attr('data-provide');
						var ms = this.$element.attr('data-seperator');
						var sortType = this.$element.attr('data-sort');
						var tquery, result = ''
						if(m === 'multiple')
						{
							if(ms === 'newline')
								result = /([^\n]+)$/.exec(this.query);
							else if(ms == 'semicolon')
								result = /([^;]+)$/.exec(this.query);
							else
								result = /([^,]+)$/.exec(this.query);
							if(result && result[1])
								tquery = result[1].trim();
							if(!tquery) return false;
							
							if( item.toLowerCase().indexOf(tquery.toLowerCase()) === 0 )
								return ~item.toLowerCase().indexOf(tquery.toLowerCase());
							else
								return 0;
						}
						else
						{
							if( sortType  === 'contain'){
								return ~item.toLowerCase().indexOf(this.query.toLowerCase());
							}
							else{
							if( item.toLowerCase().indexOf(this.query.toLowerCase()) === 0 )
								return ~item.toLowerCase().indexOf(this.query.toLowerCase());
							else
								return 0;
							}
						}
						
						
				},
				sorter: function (items) {
            if (!this.options.ajax) {
                var beginswith = [],
                    caseSensitive = [],
                    caseInsensitive = [],
                    item;

                while (item = items.shift()) {
                    if (!item.toLowerCase().indexOf(this.query.toLowerCase()))
                        beginswith.push(item);
                    else if (~item.indexOf(this.query))
                        caseSensitive.push(item);
                    else
                        caseInsensitive.push(item);
                }

                return beginswith.concat(caseSensitive, caseInsensitive);
            } else {
                return items;
            }
        },
        highlighter: function (item) {
						var m  = this.$element.attr('data-provide');
						var ms = this.$element.attr('data-seperator');
						var tquery, result = '';
						if(m === 'multiple')
						{
							if(ms === 'newline')
								result = /([^\n]+)$/.exec(this.query);
							else if(ms == 'semicolon')
								result = /([^;]+)$/.exec(this.query);	
							else
								result = /([^,]+)$/.exec(this.query);
							
							if(result && result[1])
								tquery = result[1].trim();
							if(!tquery) return false;	
						}
						else
						{
							tquery = this.query
						}
          	
						var query = tquery.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
          	return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
            	return '<strong>' + match + '</strong>'
          	});
				},
        render: function (items) {
            var that = this, display, isString = typeof that.options.displayField === 'string';

            items = $(items).map(function (i, item) {
                if (typeof item === 'object') {
                    display = isString ? item[that.options.displayField] : that.options.displayField(item);
                    i = $(that.options.item).attr('data-value', item[that.options.valueField]);
                } else {
                    display = item;
                    i = $(that.options.item).attr('data-value', item);
                }
                i.find('a').html(that.highlighter(display));
                return i[0];
            });

            if(that.autoSelect){
                items.first().addClass('active');
            }
			this.$menu.html(items);
			return this;
        },
        //------------------------------------------------------------------
        //  Filters relevent results
        //
        grepper: function (data) {
            var that = this, items, display, isString = typeof that.options.displayField === 'string';

            if (isString && data && data.length) {
                if (data[0].hasOwnProperty(that.options.displayField)) {
                    items = $.grep(data, function (item) {
                        display = isString ? item[that.options.displayField] : that.options.displayField(item);
                        return that.matcher(display);
                    });
                } else if (typeof data[0] === 'string') {
                    items = $.grep(data, function (item) {
                        return that.matcher(item);
                    });
                } else {
                    return null;
                }
            } else {
                return null;
            }
            return this.sorter(items);
        },
        next: function (event) {
            var active = this.$menu.find('.active').removeClass('active'),
                next = active.next();

            if (!next.length) {
                next = $(this.$menu.find('li')[0]);
            }

            if (this.options.scrollBar) {
                var index = this.$menu.children("li").index(next);
                if (index % 8 == 0) {
                    this.$menu.scrollTop(index * 26);
                }
            }

            next.addClass('active');
        },
        prev: function (event) {
            var active = this.$menu.find('.active').removeClass('active'),
                prev = active.prev();

            if (!prev.length) {
                prev = this.$menu.find('li').last();
            }

            if (this.options.scrollBar) {

                var $li = this.$menu.children("li");
                var total = $li.length - 1;
                var index = $li.index(prev);

                if ((total - index) % 8 == 0) {
                    this.$menu.scrollTop((index - 7) * 26);
                }

            }

            prev.addClass('active');

        },
        listen: function () {
            this.$element
                .on('focus', $.proxy(this.focus, this))
                .on('blur', $.proxy(this.blur, this))
                .on('keypress', $.proxy(this.keypress, this))
                .on('keyup', $.proxy(this.keyup, this));

            if (this.eventSupported('keydown')) {
                this.$element.on('keydown', $.proxy(this.keydown, this))
            }

            this.$menu
                .on('click', $.proxy(this.click, this))
                .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
                .on('mouseleave', 'li', $.proxy(this.mouseleave, this))
                .on('mousedown', $.proxy(this.mousedown,this));
        },
        move: function (e) {
            if (!this.shown)
                return

            switch (e.keyCode) {
                case 9: // tab
                case 13: // enter
                case 27: // escape
                    e.preventDefault();
                    break

                case 38: // up arrow
                    e.preventDefault()
                    this.prev()
                    break

                case 40: // down arrow
                    e.preventDefault()
                    this.next()
                    break
            }

            e.stopPropagation();
        },
        keydown: function (e) {
            this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40, 38, 9, 13, 27])
            this.move(e)
        },
        keypress: function (e) {
            if (this.suppressKeyPressRepeat)
                return
            this.move(e)
        },
        keyup: function (e) {
            switch (e.keyCode) {
                case 40: // down arrow
                case 38: // up arrow
                case 16: // shift
                case 17: // ctrl
                case 18: // alt
                    break

                case 9: // tab
                case 13: // enter
                    if (!this.shown)
                        return
                    this.select()
                    break

                case 27: // escape
                    if (!this.shown)
                        return
                    this.hide()
                    break

                default:
                    if (this.ajax)
                        this.ajaxLookup()
                    else
                        this.lookup();
            }

            e.stopPropagation()
            e.preventDefault()
        },
        focus: function (e) {
            this.focused = true
        },
        blur: function (e) {
            this.focused = false;
            if (!this.mousedover && !this.mouseddown && this.shown) {
                this.hide();
            } else if (this.mouseddown) {
                // This is for IE that blurs the input when user clicks on scroll.
                // We set the focus back on the input and prevent the lookup to occur again
               	this.$element.focus();
                this.mouseddown = false;
            }
        },
        click: function (e) {
            e.stopPropagation()
            e.preventDefault()
            this.select()
            this.$element.focus()
        },
        mouseenter: function (e) {
            this.mousedover = true
            this.$menu.find('.active').removeClass('active')
            $(e.currentTarget).addClass('active')
        },
        mouseleave: function (e) {
            this.mousedover = false
            if (typeof this.focused !== 'undefined' && !this.focused && typeof this.shown !== 'undefined' && this.shown)
                this.hide()
        },
        mousedown: function (e) {
            this.mouseddown = true;
        },
        destroy: function() {
            this.$element
                .off('focus', $.proxy(this.focus, this))
                .off('blur', $.proxy(this.blur, this))
                .off('keypress', $.proxy(this.keypress, this))
                .off('keyup', $.proxy(this.keyup, this));

            if (this.eventSupported('keydown')) {
                this.$element.off('keydown', $.proxy(this.keydown, this))
            }

            this.$menu
                .off('click', $.proxy(this.click, this))
                .off('mouseenter', 'li', $.proxy(this.mouseenter, this))
                .off('mouseleave', 'li', $.proxy(this.mouseleave, this))
            this.$element.removeData('typeahead');
        }
    };


    /* TYPEAHEAD PLUGIN DEFINITION
     * =========================== */

    $.fn.typeahead = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('typeahead'),
                options = typeof option === 'object' && option;
            if (!data)
                $this.data('typeahead', (data = new Typeahead(this, options)));
            if (typeof option === 'string')
                data[option]();
        });
    };

    $.fn.typeahead.defaults = {
        source: [],
        items: 10,
        scrollBar: false,
        alignWidth: true,
        menu: '<ul class="typeahead dropdown-menu"></ul>',
        item: '<li><a href="#"></a></li>',
        valueField: 'id',
        displayField: 'name',
        autoSelect: true,
				onSelect: function () {
        },
        ajax: {
            url: null,
            timeout: 300,
            method: 'get',
            triggerLength: 1,
            loadingClass: null,
            preDispatch: null,
            preProcess: null
        }
    };

    $.fn.typeahead.Constructor = Typeahead;

    /* TYPEAHEAD DATA-API
     * ================== */

    $(function () {
        $('body').on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
            var $this = $(this);
            if ($this.data('typeahead'))
                return;
            e.preventDefault();
            $this.typeahead($this.data());
        });
    });

}(window.jQuery);
// common.js JavaScript Document   ......
function invalid_input_msg(obj,msg,class_name){
	class_name = typeof(class_name) == 'undefined' ? 'mandatory' : class_name;
	obj.className += " " + class_name;
	//obj.className = class_name;
	//alert(msg);
	
	if(typeof(top.fAlert)!="undefined") {
		top.fAlert(msg);
	}else if(typeof(fancyAlert)!="undefined") {
		top.fancyAlert(msg);
	}
	 
	obj.select();
	obj.focus();
	obj.value='';
	//return false;
}
String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, "");
};
String.prototype.trimMultiSpace = function() {
    return this.replace(/\s{2,}/g, " ");
};
String.prototype.trimHyphen = function() {
    return this.replace(/^\-|\-$/g, "");
};
String.prototype.trimMultiHyphen = function() {
    return this.replace(/\-{2,}/g, "-");
};
function set_phone_format(objPhone,default_format,phone_length,msg_txt,class_name){
	
	phone_length = phone_length || top.phone_length;
	phone_min_length = top.phone_min_length;
	default_format = default_format || top.phone_format;
	msg_txt = msg_txt || "phone";
	class_name = typeof(class_name) == 'undefined' ? 'mandatory' : class_name;
	phone_reg_exp_js =  (typeof(top.phone_reg_exp_js)!="undefined" && typeof(top.phone_reg_exp_js)!=null && top.phone_reg_exp_js!='') ? top.phone_reg_exp_js : "[^0-9+]";
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = objPhone.value.replace(regExp,"");	
	refinedPh = refinedPh.trim();
	refinedPh = refinedPh.trimHyphen();
	refinedPh = refinedPh.trimMultiHyphen();
	refinedPh = refinedPh.trimMultiSpace();
	if(refinedPh.length < phone_min_length){
		invalid_input_msg(objPhone, "Please Enter a valid "+msg_txt+" number",class_name);return;
	}else{
			refinedPh = refinedPh.substr(0,phone_length);
			//invalid_input_msg(objPhone, "Please Enter a valid phone number");
			switch(default_format){
				case "###-###-####":
					objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(###) ###-####":
					objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(##) ###-####":
					objPhone.value = "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5,9);
				break;
				case "(###) ###-###":
					objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,9);
				break;
				case "(####) ######":
					objPhone.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,10);
				break;
				case "(####) #####":
					objPhone.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,9);
				break;
				case "(#####) #####":
					objPhone.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,10);
				break;
				case "(#####) ####":
					objPhone.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,9);
				break;
				default:
					objPhone.value = refinedPh;
				break;
			}
	}
	//changeClass(objPhone);
}
function core_phone_format(phone,default_format,phone_length){
	phone_length = phone_length || top.phone_length;
	phone_min_length = top.phone_min_length;
	default_format = default_format || top.phone_format;
	phone_reg_exp_js =  (typeof(top.phone_reg_exp_js)!="undefined" && typeof(top.phone_reg_exp_js)!=null && top.phone_reg_exp_js!='') ? top.phone_reg_exp_js : "[^0-9+]";
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = phone.replace(regExp,"");	
	refinedPh = refinedPh.trim();
	refinedPh = refinedPh.trimHyphen();
	refinedPh = refinedPh.trimMultiHyphen();
	refinedPh = refinedPh.trimMultiSpace();
	var formatted_phone = '';
	if(refinedPh.length < phone_min_length){
		return formatted_phone;
	}else{
			refinedPh = refinedPh.substr(0,phone_length);
			//invalid_input_msg(objPhone, "Please Enter a valid phone number");
			switch(default_format){
				case "###-###-####":
					formatted_phone = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(###) ###-####":
					formatted_phone = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(##) ###-####":
					formatted_phone= "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5,9);
				break;
				case "(###) ###-###":
					formatted_phone = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,9);
				break;
				case "(####) ######":
					formatted_phone = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,10);
				break;
				case "(####) #####":
					formatted_phone= "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,9);
				break;
				case "(#####) #####":
					formatted_phone = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,10);
				break;
				case "(#####) ####":
					formatted_phone = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,9);
				break;
				default:
					formatted_phone = refinedPh;
				break;
		}
		return formatted_phone;
	}
}

function isDefined(variable){return (!(!( variable||false )))}
window.onload = make_maindiv_full;
function make_maindiv_full(){wh = window.screen.availHeight-100;//alert(typeof(main));
if(isDefined(dgi('main'))) {dgi('main').style.height=wh+'px';if(isDefined(dgi('iMedicDiv')))dgi('iMedicDiv').style.height=wh-180 +"px"; if(isDefined(dgi('iMedicFrame')))dgi('iMedicFrame').style.height=wh-182 +"px";}
	//	alert(dgi('main').style.height+'::'+screen.availHeight);
}//end of make_maindiv_full

function foo(){} // only to pass in href of Anchors which fires event for onClick.
function ele(id,task){dgi(id).style.display=task;} // to show or hide any block with id.

//functions below are just the short codes
function dgi(id){return document.getElementById(id);}
function dgn(name){return document.getElementsByName(name);}
function dgt(tag){return document.getElementsByTagName(tag);}
function charlimit(field,count,showin){var tex = field.value; var len = tex.length; if(len > count){tex = tex.substring(0,count); field.value = tex; return false;}dgi(showin).innerHTML = count-len;}
function trim(val) { return val.replace(/^\s+|\s+$/, ''); }
function alphanum(o,w){var r={'special':/[\W]/g}; o.value = o.value.replace(r[w],'');}
function emv(entered){with (entered){apos=entered.indexOf("@");dotpos=entered.lastIndexOf(".");lastpos=entered.length-1;if (apos<1 || dotpos-apos<2 || lastpos-dotpos>3 || lastpos-dotpos<2) {return false;}else {return true;}}}
function wl(url){window.location.href=url;}

function pageWidth() { return window.innerWidth != null ? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body != null ? document.body.clientWidth : null;}

// calculate the current window height //
function pageHeight() { return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;}

// calculate the current window vertical offset //
function topPosition() { return typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;}

// calculate the position starting at the left of the window //
function leftPosition() { return typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement && document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ? document.body.scrollLeft : 0;}


//fancyAlert is just a replace of traditional javascript alert msgbox.
function fancyAlert(msg,title,callback, obj,facnyBtn1,facnyBtn2,func1,func2,mask,maskOpacity, width, height_adjustment, autoClose, showClose, left, top, drag,facnyBtn3,func3){
	//alert(height_adjustment);
	var isCenter = "";
	left = left || "";
	top = top || "";
	if(typeof(width) == "undefined") width = 300;
	if(typeof(autoClose) == "undefined" || !autoClose) autoClose = "yes";
	if(typeof(title)!="string") title='imwemr';
	if(title =="") title='imwemr';
	if(callback==null){callback='';}
	if(!facnyBtn1) {facnyBtn1='OK';}
	if(!facnyBtn2) {facnyBtn2=false;}
	if(!facnyBtn3) {facnyBtn3=false;}
	if(!func1) {func1=callback;}
	if(!func2) {func2=callback;}
	if(!func3) {func3=callback;}
	if(typeof(mask) != "boolean"){mask=true;}
	if(typeof(drag) != "boolean"){drag=true;}
	if(autoClose == "yes"){
		func1 = 'closeDialog();'+func1;
		func2 = 'closeDialog();'+func2;
	}
	if((left != "") || (top != "")){
		isCenter = false
	}
	dialogBox(title,msg,facnyBtn1,facnyBtn2,func1,func2,false,mask,drag,callback, width, "", isCenter, left, top, showClose, obj,maskOpacity, height_adjustment,facnyBtn3,func3);
}
function dialogBox(title,msgg,btn1,btn2,func1,func2,btnCancel,mask,drag,callback,w,h,isCenter,l,t,showClose,inThis,maskOpacity, height_adjustment,btn3,func3)
{
/*********************A MASTER FUNCTION TO DISPLAY ALL TYPES OF DIALOG BOXES***************/
/*  
	Date: 13 August 2010
	
PARAMETER DESCRIPTION
title 	  -> to diaplay text on the title bar of the dialog box.
msg   	  -> this text will be displayed as the body of the dialog box.
btn1      -> text value displayed as the caption of 1st button. ("false" if want no button)
btn2      -> text value displayed as the caption of 2nd button. ("false" if want no button)
func1     -> Function(with parameter if applicable) Executed by click of btn1. ("" if you are using no button)
func2     -> Function(with parameter if applicable) Executed by click of btn2. ("" if you are using no button)
btnCancel -> OPTIONAL. value can be true/false. Default="false". To display an additonal CANCEL hide dialog box.
mask	  -> OPTIONAL. value can be true/false. Setting it to "true" will lock & cover the body with transparency.
drag      -> OPTIONAL. value can be true/false. Setting it to "true" will enable the user to drag the dialog box.
callback  -> OPTIONAL. Any function which you want to triger when dialog box will be closed/dis-appeared.
w		  -> OPTIONAL. Integer value. By Default is 300 (pixels). Width of the dialog box.
h		  -> OPTIONAL. Integer value. By Default is "auto". Height of the dialog box.
isCenter  -> OPTIONAL. value can be true/fale. Default="true". This will appear dialog box in centre of web page.
l		  -> OPTIONAL. Integer Value. Left spacing of dialog box. Work only if "isCenter" property is 'false'.
t		  -> OPTIONAL. Integer Value. Top spacing of dialog box. Work only if "isCenter" property is 'false'.
showClose -> OPTIONAL. Value can be true or false. Default=true. To show close button on top-right corner the title bar of dialog box.
*****************************************************************************************************/

	var text = ''; 
	if((typeof(w) == "string" && w=='default') || typeof(w) == "undefined" || w<=0){w="500px";}else{w=(parseInt(w)+60)+"px";}
	if((typeof(h) == "string" && h=='default') || typeof(h) == "undefined" || h<=0){h="auto";}else{h=parseInt(h)+"px";}
	if(typeof(isCenter) != "boolean"){isCenter=true;}
	if(!maskOpacity) {maskOpacity=50;maskBrowserOpacity=0.5;}
	if(typeof(mask) != "boolean"){mask=false;maskOpacity=0;maskBrowserOpacity=0;}
	if(maskOpacity > 0) { maskBrowserOpacity = maskOpacity/100; }
	if(typeof(drag) != "boolean"){drag=false;}
	if(typeof(btnCancel) != "boolean"){btnCancel=false;}
	if(typeof(showClose) != "boolean"){showClose=true;}	
	var width = pageWidth();
  	var height = pageHeight();
	var left = leftPosition();
	var topP = topPosition();
	if(typeof(l) != "undefined" && !isCenter){left=l+"px";}
	if(typeof(t) != "undefined" && !isCenter){topP=t+"px";}
	var dialogwidth = w;  
	var dialogheight = h; //alert('TypeofLeft='+typeof(dialogheight)+', height='+typeof(height))
	var topposition = 100;
	var leftposition = parseInt(left) + (width / 2) - (parseInt(dialogwidth) / 2);
	if(typeof(dialogBoxCounter)=="undefined") dialogBoxCounter=0; else dialogBoxCounter++;
	if(callback==null){callback=false;}else if(callback==''){callback=false;}


	
	/*trying to embedd Messi--*/
	var butonnsArr = new Array();
	YsAction = NoAction = ThirdAction = '';
	if(typeof(btn1) == "string" && typeof(func1) == "string"){
		butonnsArr[0] = {'id': 0, 'label': btn1, 'val': 'Y'}
		YsAction = func1;
		if(YsAction.indexOf('window.top.')==-1 && YsAction.indexOf('top.')==0){YsAction = "window."+YsAction;}
	}
	if(typeof(btn2) == "string" && typeof(func2) == "string"){
		butonnsArr[butonnsArr.length] = {'id': 1, 'label': btn2, 'val': 'N'}
		if(func2!='return' && func2!='return false'){
			//func2 = func2.replace(Array('return false;','return;','return false'),'');
			NoAction = func2;
			if(NoAction!=false && NoAction.indexOf('window.top.')==-1 && NoAction.indexOf('top.')==0){NoAction = "window."+NoAction;}
		}
	}
	if(typeof(btn3) == "string" && typeof(func3) == "string"){
		butonnsArr[butonnsArr.length] = {'id': 2, 'label': btn3, 'val': 'three'}
		if(func3!='return' && func3!='return false'){
			ThirdAction = func3;
			if(ThirdAction!=false && ThirdAction.indexOf('window.top.')==-1 && ThirdAction.indexOf('top.')==0){ThirdAction = "window."+NoAction;}
		}
	}
	
	MessiOptionsArr = {
		'title':		title,
		'modal':		true,
		'closeButton':	true,
		'width':		w,
		'height':		h,
		'buttons':		butonnsArr,
		'callback':		function(val){
							if(val=='Y'){
								eval(YsAction);
							//	try{eval(YsAction);} catch(e){alert(e.message);}
							}else if(val=='N' && NoAction!=false){
								eval(NoAction);
							//	try{eval(NoAction);}catch(e){alert(e.message);}
							}else if(val=='three' && ThirdAction!=false){
								eval(ThirdAction);
							//	try{eval(ThirdAction);}catch(e){alert(e.message);}
							}
						}
	};
	//a=window.open();a.document.write(btn1+"<hr>"+func1+'<hr><hr>'+btn2+"<hr>"+func2);
	max_h = parseInt(height)-180;
	msgg = '<div style="max-height:'+max_h+'px; overflow:auto;">'+msgg+'</div>';
	new window.top.Messi(msgg, MessiOptionsArr);
	/* Messi embedding end---*/


}//end of function dialogBox.

function closeDialog(callback){return;
	if(callback==null){callback=false;}else if(callback==''){callback=false;}else{callback='function(){'+callback+';}';}
	//alert(callback);
	$('body .dialogMask').fadeOut('slow');
	$('#divCon').fadeOut('slow');
	$('#divCon').parent('div,span').html('');
//	el = dgi('divCon');
//	el.parentNode.removeChild(el);
}

//--DRAG HANDLER START
var DragHandler = {
	// private property.
	_oElem : null,

	// public method. Attach drag handler to an element.
	attach : function(oElem) {
		oElem.onmousedown = DragHandler._dragBegin;
		// callbacks
		oElem.dragBegin = new Function();
		oElem.drag = new Function();
		oElem.dragEnd = new Function();
		return oElem;
	},

	// private method. Begin drag process.
	_dragBegin : function(e) {
		var oElem = DragHandler._oElem = this;
		if (isNaN(parseInt(oElem.style.left))) { oElem.style.left = '0px'; }
		if (isNaN(parseInt(oElem.style.top))) { oElem.style.top = '0px'; }
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
		e = e ? e : window.event;
		oElem.mouseX = e.clientX;
		oElem.mouseY = e.clientY;
		oElem.dragBegin(oElem, x, y);
		document.onmousemove = DragHandler._drag;
		document.onmouseup = DragHandler._dragEnd;
		return false;
	},
 
	// private method. Drag (move) element.
	_drag : function(e) {
		var oElem = DragHandler._oElem;
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
		e = e ? e : window.event;
		oElem.style.left = x + (e.clientX - oElem.mouseX) + 'px';
		oElem.style.top = y + (e.clientY - oElem.mouseY) + 'px';
		oElem.mouseX = e.clientX;
		oElem.mouseY = e.clientY;
 		oElem.drag(oElem, x, y);
 		return false;
	},
 
 // private method. Stop drag process.
	_dragEnd : function() {
		var oElem = DragHandler._oElem;
 		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
 		oElem.dragEnd(oElem, x, y);
 		document.onmousemove = null;
		document.onmouseup = null;
		DragHandler._oElem = null;
	}
}
//----END OF DRAG HANDLER----------

/*-----------function to get, provided object is full vertically scrolled or not---*/
function isScrolledV(id){id='#'+id;OuterHeight = $(id).height();InnerHeight = $(id).prop('scrollHeight');Scrolled = $(id).scrollTop();
//console.log('id='+id+', OuterHeight='+OuterHeight+', InnerHeight='+InnerHeight+', Scrolled='+Scrolled);
if(OuterHeight+Scrolled >= InnerHeight) return true; else return false;}

//---STARTING FUNCTION TO STOP F5, CTRL+F5, CTRL+N --------

window.document.onkeydown=function(event)
{
	switch (event.keyCode){
		case 121 : //F10 logout
			event.returnValue=false;
			event.keyCode=0;
			alert('This will Close all the work and LogOut');
			return false;
		break;
		case 122 : //F11 stop full screen 
			event.returnValue=false;
			event.keyCode=0;
			top.preview_patient_details(event);
			return false;
		break;
		case 27 : //Escape
		case 116 : //F5
			do_audit = 'no';
			auditHome = 'no';
			audit = 'no';
			
			var switchUser = ($("#div_switch_user").data('bs.modal') || {}).isShown;
			if(switchUser === true){
				//event.stopPropagation();
				return false;	
			}
			
			break;
		case 82 : //ctrl+r
			if (event.ctrlKey){
				do_audit = 'no';
				auditHome = 'no';
				audit = 'no';		
				var switchUser = ($("#div_switch_user").data('bs.modal') || {}).isShown;
				if(switchUser === true){
					event.preventDefault();
					return false;	
				}	
			}
		break;
	}
}
window.document.oncontextmenu=function(event){
	var switchUser = ($("#div_switch_user").data('bs.modal') || {}).isShown;
	if(switchUser === true){
		 event.stopPropagation();
		return false;	
	}
}

//--------END OF REFRESH AND NEW WINDOW KEY STROKE PREVENTION-----

/*---MODIFIED FANCY ALERT (USED IN TESTS)--*/
function fAlert(msg, title, actionToPerform, width, height, BtnCaption,ModalMode) {
	if(typeof(title)=='undefined' || title=='') 			title='imwemr';
	if(typeof(actionToPerform)=='undefined') 				actionToPerform='';
	if(typeof(width)=='undefined' || width=='') 			width='500px';
	if(typeof(height)=='undefined' || height=='') 			height='auto';
	if(typeof(BtnCaption)=='undefined' || BtnCaption=='') 	BtnCaption='OK';
	if(typeof(ModalMode)!='boolean')						ModalMode=true;
	if(BtnCaption=='CLOSE' || BtnCaption=='Close'){closeBtn=false;} else{closeBtn=true;}
	//if(actionToPerform.indexOf('window.top.')== -1 && actionToPerform.indexOf('top.')== 0){actionToPerform = "window."+actionToPerform;}
	msg = '<div style="max-height:500px; overflow:auto;">'+msg+'</div>';
	if(typeof(Messi)=='function'){
		new Messi(msg, {'title': title, modal: ModalMode, 'width': width, 'closeButton':closeBtn, buttons: [{id: 0, label: BtnCaption, val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')>=0){actionToPerform;}else if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')<0){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});
	}else if(typeof(window.top.Messi)=='function'){
		new window.top.Messi(escape(msg), {'title': title, modal: ModalMode, 'width': width, 'closeButton':closeBtn, buttons: [{id: 0, label: BtnCaption, val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')>=0){actionToPerform;}else if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')<0){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});	
	}
}
function fancyConfirm(msg, title, YsAction, NoAction, width, height,btn1,btn2) {
	if(typeof(YsAction)=='undefined') {YsAction=title; title='';}
	if(typeof(title)=='undefined' || title=='') title='imwemr';
	if(typeof(width)=='undefined' || width=='') width='500px';
	if(typeof(NoAction)=='undefined' || NoAction=='') NoAction=false;
	if(YsAction.indexOf('window.top.')==-1 && YsAction.indexOf('top.')==0){YsAction = "window."+YsAction;}
	if(NoAction!=false && NoAction.indexOf('window.top.')==-1 && NoAction.indexOf('top.')==0){NoAction = "window."+NoAction;}
	if(typeof(btn1)=='undefined' || btn1=='') btn1='Yes';
	if(typeof(btn2)=='undefined' || btn2=='') btn2='No';
	
	new window.top.Messi(msg, {'title': title, modal: true, 'width': width, buttons: [{id: 0, label: btn1, val: 'Y', "class": 'btn-success'}, {id: 1, label: btn2, val: 'N', "class": 'btn-danger'}],callback: function(val){if(val=='Y'){eval(YsAction);}else if(val=='N' && NoAction!=false){eval(NoAction);}}});
}
function fancyModal(html,title,w,h,closeBtn,po,ModalMode){//positionObject=po;
	if(typeof(title)!="string") 			title=null;
	if(typeof(w)!="string") 				w='';
	if(typeof(h)!="string") 				h='';
	if(typeof(closeBtn)=="undefined")		closeBtn=true;
	if(typeof(po)!='undefined') 			{of = $(po).offset();}else po = false;
	if(typeof(ModalMode)!='boolean')		ModalMode=true;
	if(po){new top.Messi(html,{'title':title,modal:ModalMode,'width':w,'height':h,'closeButton':closeBtn,center:false,viewport:{top:of.top+25,left:of.left+25}});}
	else{new top.Messi(html,{'title':title,modal:ModalMode,'width':w,'height':h,'closeButton':closeBtn});}
}
function removeMessi(){window.top.$('.messi-modal,.messi').remove();}


//Stopping value save/pwd save prompts and autocomplete.
if(typeof($)!='undefined'){
	$(document).ready(function(e){$('form, input').attr('autocomplete','off');});
	$(document).ajaxComplete(function(event, request, settings) {
    if (request.getResponseHeader('REQUIRES_AUTH') === '1') {
			top.window.location = top.JS_WEB_ROOT_PATH + '/interface/login/index.php';
    }
	});
}
/*---END OF MODIFIED (USED IN TESTS)--*/
/*----------------BEGIN FUNCTIONS CREATED FOR INTERNATIONALIZATION WORK-----------------*/
function fnArrDate(dateString,format){
	dateString = dateString || '';
	format = format || 'yyyy-mm-dd'
	separator = get_date_separator(dateString)
	arrBirthDate = dateString.split(separator);
	switch(format){
		case "dd"+separator+"mm"+separator+"yyyy":
			date =  arrBirthDate[0];
			month = arrBirthDate[1];
			year = arrBirthDate[2];
		break;
		case "mm"+separator+"dd"+separator+"yyyy":
			month =  arrBirthDate[0];
			date = arrBirthDate[1];
			year = arrBirthDate[2];
		break;
		case "yyyy"+separator+"mm"+separator+"dd":
			year =  arrBirthDate[0];
			month = arrBirthDate[1];
			date = arrBirthDate[2];
		break;
		case "yyyy"+separator+"dd"+separator+"mm":
			year =  arrBirthDate[0];
			month = arrBirthDate[2];
			date = arrBirthDate[1];
		break;
		default://---"mm-dd-yyyy":
			month =  arrBirthDate[0];
			date = arrBirthDate[1];
			year = arrBirthDate[2];
	}
	return [year,month,date]
}
function get_date_separator(date){
	date = date || '';
	separator = '-';
	if(date.search("-")!="-1")
	separator = "-";
	else if(date.search("/")!="-1")
	separator = "/";
	else if(date.search("/\\/")!="-1")
	separator = "\\";
	return separator;
}
function validate_date(objName, format) {
	date = typeof(objName)=="object" ? objName.value : objName;
	if(date!=''){
		//jquery_date_format = (typeof(top.opener) != 'undefined' ? top.opener.top.jquery_date_format:(typeof(opener) != 'undefined' ? opener.top.jquery_date_format:top.jquery_date_format));
		if(typeof(top.jquery_date_format) != "undefined"){
			jquery_date_format = top.jquery_date_format;
		}else if(typeof(opener.top.jquery_date_format) != "undefined"){
			jquery_date_format = opener.top.jquery_date_format;
		}else if(typeof(top.opener.top.jquery_date_format) != "undefined"){
			jquery_date_format = top.opener.top.jquery_date_format;
		}
		format = format || jquery_date_format;
		separator = get_date_separator(format);
		var regex = "";
		switch(format){
			case "dd"+separator+"mm"+separator+"yyyy":
			var regex = new RegExp("(((0[1-9]|[12][0-9]|3[01])["+separator+"](0[13578]|1[02])["+separator+"](19|20[0-9]{2}))|((0[1-9]|[12][0-9]|3[0])["+separator+"](0[469]|1[1])["+separator+"](19|20[0-9]{2}))|((0[1-9]|1[0-9]|2[0-9])["+separator+"](02)["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((0[1-9]|1[0-9]|2[0-8])["+separator+"](02)["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{4})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "mm"+separator+"dd"+separator+"yyyy":
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{4})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "mm"+separator+"dd"+separator+"yy":alert("mm"+separator+"dd"+separator+"yy");
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "yyyy"+separator+"mm"+separator+"dd":
			var regex = new RegExp("(((19|20[0-9]{2})["+separator+"](0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01]))|((19|20[0-9]{2})["+separator+"](0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0]))|(((19|20)(04|08|[2468][048]|[13579][26]))["+separator+"](02)["+separator+"](0[1-9]|1[0-9]|2[0-9]))|(((19|20)[0-9]{2}["+separator+"](02)["+separator+"](0[1-9]|1[0-9]|2[0-8]))))");
			//var regex = new RegExp("([1-9]{4}["+separator+"][0-9]{2}["+separator+"][0-9]{2})");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "yyyy"+separator+"dd"+separator+"mm":
			var regex = new RegExp("(((19|20[0-9]{2})["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](0[13578]|1[02]))|((19|20[0-9]{2})["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](0[469]|1[1]))|(((19|20)(04|08|[2468][048]|[13579][26]))["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"](02))|(((19|20)[0-9]{2}["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"](02))))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			default://----mm-dd-yyyy
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
		}
		if(regex.test(date)){
			flag = true;
			objName.value = date;
		}
		else flag = false;
		return flag;
	}
}
function getDateFormat(date,inFormat,outFormat){ //--------------This function takes date and return date  in desired format
	arrDate = fnArrDate(date,inFormat);			//---------------return array of date 0-yyyy 1-mm 2-dd
	yy = arrDate[0];mm = arrDate[1]; dd = arrDate[2];
	date_result = '';
	if(date != ''){
		opener_top_date_format=''
		if(opener!=null)opener_top_date_format=opener.top.date_format
		outFormat = outFormat || top.date_format || opener_top_date_format;
		separator = get_date_separator(outFormat);
		switch(outFormat){
			case "dd"+separator+"mm"+separator+"yyyy":
			date_result = dd+separator+mm+separator+yy;
			break;
			case "yyyy"+separator+"mm"+separator+"dd":
			date_result = yy+separator+mm+separator+dd;
			break;
			case "yyyy"+separator+"dd"+separator+"mm":
			date_result = yy+separator+dd+separator+mm;
			break;
			default://---mm-dd-yyyy
			date_result = mm+separator+dd+separator+yy;
		}
	}
	return date_result;
}
function validate_zip(inpObj){
	var borwser_name=get_browser();
	if(typeof(top.int_country) != "undefined" && top.int_country == "UK")return true;
	zip_type = (typeof(opener) != 'undefined' && borwser_name!='chrome') ? opener.top.zip_type : top.zip_type;
	switch(zip_type){
		case "numeric":
			regex = new RegExp("^[0-9- ]{0,"+top.zip_length+"}$","gi");
			replaceExp = new RegExp("[^0-9- ]","gi");
		break;
		case "alphanumeric":
			regex = new RegExp("^[0-9a-zA-Z- ]{0,"+top.zip_length+"}$","gi");
			replaceExp = new RegExp("[^0-9a-zA-Z- ]","gi");
		break;
		default:
			regex = new RegExp("^[0-9- ]{0,"+top.zip_length+"}$","gi");
			replaceExp = new RegExp("[^0-9- ]","gi");
	}
	zip = inpObj.value;
	newZip = '';
	if(zip != "")
	newZip = zip.replace(replaceExp,"");
	if(zip!=newZip)
	inpObj.value = newZip 
	if(regex.test(zip))
	return true
	else{
		return false;
	}
}
function validate_ssn(objName,format,length){
	ssn = typeof(objName)=="object" ? objName.value : objName;
	format = format || top.ssn_format;
	length = length || top.ssn_length;
	ssn_reg_exp_js =  (typeof(top.ssn_reg_exp_js)!="undefined" && typeof(top.ssn_reg_exp_js)!=null && top.ssn_reg_exp_js!='') ? top.ssn_reg_exp_js : "[^0-9\-+]";
	regExp = new RegExp(ssn_reg_exp_js,'g');
	refinedSSN = ssn.replace(regExp,"");
	refinedSSN = refinedSSN.replace(/[^0-9A-Za-z+]/g,'');
	refinedSSN = refinedSSN.substr(0,length);
	ssnLength = refinedSSN.replace(/[^0-9A-Za-z+]/g,'').length;
	if(ssnLength >0 && ((format!='' && ssnLength != length) || (format=='' && ssnLength > length))){
		//invalid_input_msg(objName, "Please Enter a valid SSN");
		if(typeof(opener) != "undefined"){
			top.fAlert("Invalid Social Security Number (SSN)");
		}else{
			top.fAlert("Invalid Social Security Number (SSN)");
		}
		objName.value = '';
		return false;
	}else{
		switch(format){
			case "###-###-####":
			objName.value = refinedSSN.replace(/(\d{3})(\d{3})(\d{4})/,'$1-$2-$3');
			break;
			case "###-##-####":
			objName.value = refinedSSN.replace(/(\d{3})(\d{2})(\d{4})/,'$1-$2-$3');
			break;
			default:	
			objName.value = refinedSSN;
		}
	}
	return true;
}
function checkdate(objName, type) {
	type = type || '';
	if (validate_date(objName,type) == false && objName.value!='') {
		objName.value="";
		objName.focus();
		if(typeof(opener) == 'undefined' && typeof(top.opener) == 'undefined'){
			fAlert('Please enter a valid Date');
			top.document.getElementById("divCommonAlertMsg").style.display = "block";
		}else{
			fAlert('Please enter a valid Date');
		}
		return false;
	}
	else {
		return true;
   }
}
/*----------------END FUNCTIONS FOR INTERNATIONALIZATION WORK-----------------*/

function get_browser(){
	browser = '';
	if(navigator.userAgent.indexOf("MSIE") != -1 || !!navigator.userAgent.match(/Trident\/7\./)){
		browser =  "ie";
	}
	else if(typeof(window.mozilla) == "object"){
		browser =  "mozilla";
	}
	else if(typeof(window.chrome) == "object"){
		browser =  "chrome";
	}else if(navigator.userAgent.indexOf("Safari") != -1){
		browser =  "safari";
	}
	return browser;
}

function show_clock(){ 
	//var newDate = new Date().toLocaleString("en-US", {timeZone: top.currentTimeZone});
	var newDate = new Date();
	var C=new Date(newDate);var h=C.getHours();var m=C.getMinutes();var s=C.getSeconds();
	var dn="PM";if(h<12)dn="AM";if(h>12)h=h-12;if(h==0)h=12;if(h<=9)h="0"+h;if(m<=9)m="0"+m;if(s<=9)s="0"+s;var tm=h+":"+m+":"+s+" "+dn;
	dgi("tick2").innerHTML=tm;setTimeout("show_clock()",1000);
}

function set_caret_position(elemId, caretPos)
{
	var elem = document.getElementById(elemId);
	if(elem != null)
	{
		if(elem.createTextRange)
		{
			var range = elem.createTextRange();
			range.move('character', caretPos);
			range.select();
		}
		else
		{
			if(elem.selectionStart)
			{
				elem.focus();
				elem.setSelectionRange(caretPos, caretPos);
			}
			else
				elem.focus();
		}
	}
}

function get_focus_obj(obj)
{
	var obj_id 	= obj.id;
	if(document.getElementById(obj_id))
	{
		var str = document.getElementById(obj_id).value;
        if(str && str.length > 0)
		set_caret_position(obj_id, str.length);
	}
	
}

function alert_notification_show(m)
{
	o1 = $('#div_alert_notifications');
	o2 = $('#div_alert_notifications').find('.notification_span').html(m);
	o1.fadeIn('slow');
	ACT = 2;//Auto Close Time (seconds).
	t = setInterval(function(){if(ACT>0){ACT--;}else{clearInterval(t);o1.fadeOut('slow');o2.html('');}},1000);
	
}

// Common Bootstrap Modal Box 
function show_modal(div_id,header_title,content,footer_cont,cont_height,size,dismissible,cross_btn){
	//Arguments
		//div_id -> id that will be used for modal box. [string]
		//header_title -> title of the modal box. [string]
		//content -> content that will be shown in the modal box. [string]
		//footer_cont -> content that will be shown in the footer of modal box. [string]
		//cont_height -> defines the height of content inside the modal box. [string -> 300]	
		//size -> defines the size of modal box. [modal-lg / modal-sm / empty -> default size]	
		//dismissible -> whether modal box will be closed or not when user click outside of the modal box. [true / false]
		//cross_btn -> if some value send in this variable then top right cross image will not display.
	
	if(header_title == '' || typeof(header_title) != 'string'){
		header_title = 'imwemr';
	}
	
	if(footer_cont == '' || typeof(footer_cont) != 'string'){
		footer_cont = '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
	}
	
	if(cont_height == '' || typeof(cont_height) != 'string'){
		cont_height = '300';
	}
	
	var cross_btn_html='<button type="button" class="close" data-dismiss="modal">×</button>';
	if(typeof(cross_btn) !== 'undefined' && cross_btn!=''){
		cross_btn_html='';
	}
	
	var header_content = '<div class="modal-header bg-primary">'+cross_btn_html+'<h4 class="modal-title">'+header_title+'</h4></div>';
	var body_content   = '<div class="modal-body" style="max-height:'+cont_height+'px;overflow:auto;overflow-y:scroll">'+content+'</div>';	
	var footer_content = '<div id="module_buttons" class="modal-footer ad_modal_footer"><div class="col-sm-12">'+footer_cont+'</div></div>';
	var final_str = header_content+body_content+footer_content;
	var modal = '';
	modal += '<div class="common_modal_wrapper">';
	modal += '<div class="modal-dialog '+size+'"><div class="modal-content">'+final_str+'</div></div>';
	modal += '</div>';
	
	if($('#'+div_id+'').length > 0){
		$('#'+div_id+'').html(modal); 
	}else{
		$('body').append('<div class="modal" id="'+div_id+'" role="dialog"><div>');
		$('#'+div_id+'').html(modal);
	} 
	
	if(dismissible != 'false'){
		$('#'+div_id+'').modal({
			backdrop: 'static',
			keyboard: false,
		});
	}	
	
	$('#'+div_id+'').modal('show');
	return false;
}

function checkIfAlphabet(e, ctrlId)
{
	txtVal ='';
	var txtVal = dgi(ctrlId).value;
	if(txtVal!=''){
		var unicode= e.keyCode? e.keyCode : e.charCode;
		//var selChar = String.fromCharCode(unicode);
		if(!(unicode>=65 && unicode<=90) && !(unicode==8 || unicode==46 || unicode==13 || unicode==9
		 || unicode==16 || unicode==17 || unicode==18 || unicode==37 || unicode==38 || unicode==39 || unicode==40 || unicode==32)){
			alert("Only Character values accepted");
			var rightVal = txtVal.substr(0, parseInt(txtVal.length)-1);
			dgi(ctrlId).value = rightVal;
			return false;
		}
	}
}

//Messages Management  -- Same function exists for php also
function imw_msg_js(key){
	var msgArray = { 
		'no_rec' : 'No record found.',
		'drop_sel': 'Please Select.',
		'sel_rec' : 'Please select a record.',
		'del_rec': 'Are you sure to delete the selected record(s) ?'};
		
	if(key!=""){
		return msgArray[key];
	}else{
		return msgArray;
	}
}

// Multi Level Dropdown [Simple Menu]
function set_val_text(ths,menuId,elemId,liId){
	if(ths === '' || typeof(ths) === 'undefined'){
		if(typeof(liId) != 'undefined'){
			var parent_elem = $('#drop_li_'+liId).parent();
			parent_elem.find('.dropdown-submenu').each(function(id,elem){
				if($(elem).attr('id') != 'drop_li_'+liId){
					$(elem).find('.dropdown-menu').removeClass('show');
				}
			});
		}
	}else{
		if($('.dropdown-menu').hasClass("show")){
			$('.dropdown-menu').removeClass('show');
			$('#'+elemId).parent().find('.open').removeClass('open');
		}
		var elem_val = $(ths).parent().find('input').val();
		if(elem_val != '' || typeof(elem_val) != 'undefined'){
			$('#'+elemId+'').val(elem_val).change();
			$('#'+menuId+'').dropdown('toggle');
		}
	}
}

if(window.jQuery) {
	$(document).ready(function(){
		$('body').on("click",".dropdown-submenu a",function(e){

			if ($(this).next('ul').hasClass("show")) {
				$('.dropdown-submenu').removeClass('show');
			} else {
				$('.dropdown-submenu').removeClass('show');
				$(this).next('ul').toggleClass('show');
			}

			e.stopPropagation();
			e.preventDefault();	
			$('.dropdown').on('hide.bs.dropdown', function () {
				if($('.dropdown-menu .dropdown-submenu').find('ul').hasClass('show')){
					$('.dropdown-menu .dropdown-submenu').find('ul').removeClass('show').addClass('hide');
				}
			});		
		});

		$('body').on('show.bs.dropdown','.dropdown',function(elem){
			var target_dropdown = elem.currentTarget;
			var get_dropDown = $(target_dropdown).find('ul.dropdown-menu').width();
			var obj_offset = $(target_dropdown).offset();
			if($(window).width() < ( obj_offset.left + get_dropDown)){
				var difference = $(window).width() - ( obj_offset.left + get_dropDown + 10);
				$(target_dropdown).find('ul.dropdown-menu').css('left',difference);
			}
		});
	});
}

function reload_simple_menu(id,val){
	if($('#'+id)){
		$('#'+id).find('label').remove();
		var final_val=$('#'+id).html() + val;
		$('#'+id).html(final_val);
	}
}

function html_to_pdf(html_file_loc,op,pdf_name,on_page,one_page_pdf,call_for){
	//html_file_loc => where is the html file located [use write_html() to get html file location and pass it here ]
	//op => orientation of pdf
	//pdf_name => name of the generated pdf
	var on_page_dir=false;
	if(on_page != '' && typeof(on_page) != 'undefined')
	{on_page_dir=on_page;}
	if(html_file_loc != '' && typeof(html_file_loc) != 'undefined' && html_file_loc.length > 0){
		if(op == '' || typeof(op) == 'undefined'){
			op = 'p';
		}
		if(pdf_name == '' || typeof(pdf_name) == 'undefined'){
			pdf_name = 'new_pdf';
		}
		if(call_for == '' || typeof(call_for) == 'undefined'){
			call_for = 'html_to_pdf';
		}
		var wo=top.JS_WEB_ROOT_PATH;
		/* if(window.opener!=null){
			wo=window.opener.top.JS_WEB_ROOT_PATH;
		} */
		if(on_page_dir==true)
		{	
			if(typeof(wo) == 'undefined'){wo=window.opener.top.JS_WEB_ROOT_PATH;}
			window.location.href=wo+'/library/'+call_for+'/createPdf.php?onePage='+one_page_pdf+'&op='+op+'&file_location='+html_file_loc+'&pdf_name='+pdf_name+'';	
		}
		else
		{
		window.open(''+wo+'/library/'+call_for+'/createPdf.php?onePage='+one_page_pdf+'&op='+op+'&file_location='+html_file_loc+'&pdf_name='+pdf_name+'');
		}
	}else{
		alert('Invalid file location provided');
	}
}

function era_file_fun(){
	var enc_id="";
	if(top.fmain.$('#enc_id_read').length>0){
		enc_id = top.fmain.$('#enc_id_read').val();
	}
	var wname = "ERA";
	var features = "width=1200px,height=450px,resizable=1,scrollbars=1";
	top.popup_win("../../interface/accounting/era_file.php?enc_id="+enc_id,features);	
}

function claim_file_fun(clm_status){
	var enc_id="";
	if(top.fmain.$('#enc_id_read').length>0){
		enc_id = top.fmain.$('#enc_id_read').val();
	}
	var url = '../../interface/accounting/claims_file.php?enc_id='+enc_id;
	var wname = "Claims";
	var features = "width=1200px,height=450px,resizable=1,scrollbars=1";
	if(claim_status_request_js=='YES'){
		if(typeof(clm_status)!='undefined' && clm_status != ''){
			var url = '../../interface/accounting/claims_status.php?clm_status=true&enc_id='+enc_id;
			var wname = "ClaimsStatus";
		}
	}
	top.popup_win(url,features);
}

function statement_file_fun(){
	var patid="";
	if(typeof(pat_js)!='undefined' && pat_js != ''){
		patid=pat_js;
	}else{
		if($('#patient_id').length>0){
			patid=$('#patient_id').val();
		}
	}
	var url = '../../interface/reports/accountingStatementsResult.php?Submit=Get Report&pevious=yes&patientId='+patid;
	var wname = "statements";
	var features = "width=900px,height=650px,resizable=1,scrollbars=1";
	if(patid>0){
		top.popup_win(url,features);
	}else{
		top.fAlert('Please select a patient.');
	}
}

/***To prevent pressing enter on any textarea****/
function stop_enter_key_on(idd){
	$('#'+idd).bind('keypress', function(e) {
	  if ((e.keyCode || e.which) == 13) {
		e.preventDefault();
		return false;
	  }
	});
}

/****CHECK IF STRING HAVING A NUMBER IN IT****/
function hasNumbers(t){
    return /\d/.test(t);
}

// -- Opens Pt. search popup
function search_pt(callback_function){
	var parWidth = parseInt($(window).width() - 500);
	var win_height = parseInt($(window).height());
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/billing/search_patient_popup.php?callback_function='+callback_function+'',"width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
}

function check_checkboxes() {
	$('#chk_sel_all').click(function(){
		var status = this.checked; // "select all" checked status
		$('.chk_sel').each(function(){ //iterate all listed checkbox items
			this.checked = status; //change ".checkbox" checked status
		});
	});
}

/*
* Function : current_date 
* Purpose - return current Date
* Params -	
* format - Holds Date format in which date will be parsed
*					 IF Not Defined Then Default will be Used from Top 
*/
function current_date(d_format,year_digit,time,t_format)
{ 
	d_format = d_format || top.jquery_date_format || window.top.opener.jquery_date_format;
	d_format = d_format.toLowerCase()
	year_digit = parseInt(year_digit)
	year_digit = (year_digit !== 2 && year_digit !== 4) ? 4 : year_digit;
	
	time = time || false;
	time = (typeof time != 'boolean') ? false : time;
	t_format = t_format || '12'
	t_format = (t_format == '24' || t_format == '12') ? t_format : '12'
	
	var t = new Date();
	var mon = (parseInt(t.getMonth()) + 1).toString();
	
	var d = ('0'+t.getDate()).slice(-2);
	var m = ('0'+mon).slice(-2); 
	var y = t.getFullYear()
	if(year_digit === 2) y = y.toString().substr(2,2);
	
	d_format = d_format.replace(/y/g, y);
	d_format = d_format.replace(/m/g, m);
	d_format = d_format.replace(/d/g, d);
	
	var time_string = '';
	if( time )
	{ 
		var h = parseInt(t.getHours());
		var m = parseInt(t.getMinutes());
		var a = (h < 12) ? 'AM' : 'PM';
		
		m = m < 10 ? '0' + m : m;
		h = (t_format == '12' && h > 12) ? h - 12 : h;
		time_string = ' ' + h + ':' + m + (t_format == 12 ? ' ' + a : '');
	} 	
	return d_format + time_string; 
}

function section_highlight(section_id,arr_all_sections,di_highlight_all){
	$(arr_all_sections).each(function(){
		$('#' + this).css({'background-color':'#FFF'});
	});
	if(typeof(di_highlight_all) == 'undefined' || !di_highlight_all){
		$('#' + section_id).css({'background-color':'#FFFFCC'});//removeClass('tblBg').addClass('bg3');
	}
}

function search_email(e, div_object, left, top)
{
	var object = document.getElementById(div_object);
	var nextObject = object.getElementsByTagName('select').item(0);
	
	if(e.keyCode == 64 && object.style.display == "none"){
		object.style.left = left;
		object.style.top = top;
		object.style.display = "block";
		nextObject.options[0].selected = true;
		nextObject.focus();
	}else if(e.keyCode != 64 && object.style.display == "block"){
		object.style.display = "none";
	}
}

var typed_val = "";
function select_option(e, object, object_id){
	var text = document.getElementById(object_id);
	var value = text.value;
	var splited_value = value.split("@");
	
	if(e.keyCode==13){
		var value_without_website_address = splited_value[0];
		var value_with_new_website_address = value_without_website_address+object.value;
		//text.setAttribute("value", value_with_new_website_address);
		text.value= value_with_new_website_address;
		object.parentNode.style.display = "none";
		text.focus();
	}else if(e.keyCode!=103 && e.keyCode!=104 && e.keyCode!=109 && e.keyCode!=97){
		if(typed_val != ""){
			var value_without_website_address = splited_value[0]+"@"+typed_val;
		}else{
			var value_without_website_address = splited_value[0]+"@";
		}
		//alert("amit");
		var value_with_new_website_address = value_without_website_address;
		//text.setAttribute("value", value_with_new_website_address);
		text.value= value_with_new_website_address;
		typed_val = "";
		object.parentNode.style.display = "none";
		text.focus();
	}else{
		typed_val = typed_val + String.fromCharCode(e.keyCode);
	}
}

function select_option_with_mouse(object, object_id){
	var text = document.getElementById(object_id);
	var value = text.value;
	var splited_value = value.split("@");	
	var value_without_website_address = splited_value[0];	
	var value_with_new_website_address = value_without_website_address+object.value;
	//text.setAttribute("value", value_with_new_website_address);
	text.value= value_with_new_website_address;
	object.parentNode.style.display = "none";
}

//short form of big function.
function gebi(el){return document.getElementById(el);}

function zip_vs_state_R6(objZip,objCity,objState,objCountry,objCounty)
{
	zip_code = $(objZip).val();
	if(zip_code == ''){
		$(objCity).val('');
		$(objState).val('');
		return false;
	}
	$.ajax({
			url:top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php?zipcode="+zip_code,
			success:function(data)
			{
				var changecolor='#F6C67A';	
				var val=data.split("-");
				var city = $.trim(val[0]);
				var state = $.trim(val[1]);
				var country = $.trim(val[3]);
				var county = $.trim(val[4]); 
				if(city!="")
				{
					$(objCity).val(city);
					$(objState).val(state);
					if(country){$(objCountry).val(country);}
					if(county){$(objCounty).val(county);}
					return;
					
					if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
						if(typeof(document.getElementById("zipCodeStatus"))!="undefined" && document.getElementById("zipCodeStatus") != null && document.getElementById("zipCodeStatus") != "undefined")
						document.getElementById("zipCodeStatus").value="OK";
						return;
					}
					if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
						//DO NOT VALIDATE ZIPCODE
						return;
					}
					
					$(objZip).val("");
					changeClass(document.getElementById($(objZip).attr("id")),1)
					if(typeof(top.fAlert) != "undefined")
						top.fAlert('Please enter correct '+top.zipLabel);
					else{
						alert('Please enter correct '+top.zipLabel);
					}
					$(objZip).select();
					$(objCity).val("");
					$(objState).val("");
					$(objCounty).val("");
				}
		  }
	});
}

function set_header_title(title){
	top.$('#acc_page_name').html(title); 
}

function showHidemodal(action, div_id, dismissible, backdrop){
	
	action = (action == 'show' || 'hide') ? action : 'show';
	backdrop = (typeof backdrop === 'boolean') ? backdrop : false;
	dismissible = (typeof dismissible === 'boolean') ? dismissible : false;
	
	if( action == 'hide') {
		$('#'+div_id+'').modal(action);		
	}
	else {
		backdrop = ( dismissible) ? 'static' : backdrop;
		$('#'+div_id+'').modal({backdrop: backdrop,keyboard:false});
		if( !$('#'+div_id+'').hasClass('in') ){
			$('#'+div_id+'').modal('show');
		}
	}
	
	return false;
}
function ado_scan_fun(show,formNme,editId){
	if( typeof show == 'undefined')	show = 'scan';
	if( typeof formNme == 'undefined')	return;
	if( typeof editId == 'undefined' || editId == '')	editId = 0;
	
	var url = top.JS_WEB_ROOT_PATH + '/library/classes/scan_ptinfo_medhx_images.php?formName='+formNme+'&show='+show;
	url += (editId > 0) ? '&edit_id='+editId : '';
	top.popup_win(url,'scan_patient_info');
}

/* Creating buttons for modals */
function set_modal_btns(parent_id,btn_arr){
	var parent_elem = '';
	var btn_str = '';
	var footer_str = '';
	var default_type = 'button';
	var btn_debug_arr = new Array;
	
	//Getting Parent element
	if($('#'+parent_id).length > 0){
		parent_elem = $('#'+parent_id);
	}else if($('.'+parent_id).length > 0){
		parent_elem = $('.'+parent_id);
	}
    
    var onclick='';
    if(parent_id=='new_priv_div .modal-footer') {
        onclick='onclick="resetToOldPriv();"';
    }
	
	if(parent_elem.length){
		parent_elem.addClass('btn-ftr-unique');
		parent_elem.attr('id','module_buttons');
		if(btn_arr.length > 0){ //If something is there in array
			$.each(btn_arr,function(id,val){
				var btn_name_str = '';
				var btn_val = btn_arr[id][0].charAt(0).toUpperCase() + btn_arr[id][0].slice(1);
				var btn_class = get_btn_class(btn_val);
				var btn_name = btn_arr[id][1];
				var btn_func = btn_arr[id][2];
				var btn_type = default_type;
				if(btn_name.length){
					btn_type = 'submit';
					btn_name_str = 'name="'+btn_name+'" value="'+btn_name+'"';
				}
				btn_str += '<button type="'+btn_type+'" class="'+btn_class+'" '+btn_name_str+' id="'+btn_val+'" onclick="'+btn_func+'">'+btn_val+'</button>';
			});
			btn_str += '<button type="button" class="btn btn-danger" data-dismiss="modal" '+onclick+'>Close</button>';
			footer_str = '<div class="row mdl_btns_dp"><div class="col-sm-12 text-center">'+btn_str+'</div></div>';
		}else{ // else send only close button
			btn_str += '<button type="button" class="btn btn-danger" data-dismiss="modal" '+onclick+'>Close</button>';
			footer_str = '<div class="row mdl_btns_dp"><div class="col-sm-12 text-center">'+btn_str+'</div></div>';
		}
		
		//Appending buttons
		if($('body').find('.btn-ftr-unique .mdl_btns_dp').length == 0){
			//Removing already existing buttons or inputs in the footer
			$('body').find('.btn-ftr-unique').find('[type=button],[type=submit]').not('input[type=hidden]').remove();
			$('body').find('.btn-ftr-unique').append(footer_str);
		}else{
			$('body').find('.btn-ftr-unique .mdl_btns_dp').html(footer_str);
		}
		
		if($('body').find('.btn-ftr-unique').hasClass('ad_modal_footer') === false){
			$('body').find('.btn-ftr-unique').addClass('ad_modal_footer');
		}
		
		
		if(parent_elem.hasClass('btn-ftr-unique')){
			parent_elem.removeClass('btn-ftr-unique');
		}
	}
}

function get_btn_class(btn_val){
	var btn_class = 'btn btn-default';
	switch(btn_val.toLowerCase()){
		case 'save':
		case 'done':
		case 'ok':
		case 'yes':
			btn_class = 'btn btn-success';				
			break;
		case 'cancel':
		case 'no':
		case 'Close':
			btn_class = 'btn btn-danger';
			break;
		case 'delete selected':
			btn_class = 'btn btn-danger';
			break;
		case 'print':
		case 'download':
		case 'add':
		case 'upload':
			btn_class = 'btn btn-info';
			break;
	}
	return btn_class;
}

//Sets modal dynamic max height
function set_modal_height(object,debug){
	var obj = '';
	var debug_arr = new Array;
	if(typeof(object) == 'object'){
		obj = $(object);
	}else{
		if($('#'+object).length > 0){
			obj = $('#'+object);
		}else if($('.'+object).length > 0){
			obj = $('.'+object);
		}
	}
	
	debug_arr['object'] = obj;
	if(obj != ''){
		if(obj.hasClass('in')){
			var elem = obj;
			var elem_height = elem.outerHeight();
			
			debug_arr['modal_height'] = elem_height;
			
			var header_position = $('#first_toolbar',top.document).position();
			var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight() - header_position.top - $('#first_toolbar').outerHeight());
			
			debug_arr['window_height'] = window_height;
			
			if(elem_height > window_height){
				var diff = parseInt(elem_height - window_height);
				var new_height = parseInt(elem_height - diff);
				//Subtracting modal header and footer 
				var modal_extra = parseInt(elem.find('.modal-header').outerHeight() + elem.find('.modal-footer').outerHeight());
				new_height = parseInt(new_height - modal_extra)
				debug_arr['modal_extra_height'] = new_height;
				elem.find('.modal-body').css('max-height',new_height);
				if (elem.find('.modal-body').css("overflow-x") != 'hidden'){
					elem.find('.modal-body').css({
						overflow: 'auto',
						'overflow-x': 'hidden'
					});
				}
			}
		}
	}
	if(debug){
		console.log(debug_arr);
	}
	
}

function checkSingle(elemId,grpName){
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true){		
		for(var i=0;i<len;i++){
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) ){
				obgrp[i].checked=false;
			}
		}	
	}
}

function changeChbxColor(grpNode){
	var objGrp = $('input[name="'+grpNode+'"]');
	var len = objGrp.length;
	var is_checked = false;
	for(var i=0;i<len;i++){
		if(objGrp[i].checked == true){
			is_checked = true;
		}
	}
	if( is_checked )	
		objGrp.parent('div').removeClass('checkbox-mandatory');
	else 
		objGrp.parent('div').addClass('checkbox-mandatory');	
}

function day_charges(){
	var sc_wd=(screen.availWidth)-100;
	var sc_hg=(screen.availHeight)-120;
	var features = 'width='+sc_wd+',height='+sc_hg+',left=150,top=80,location=0,status=1,resizable=1,left=0,top=0,scrollbars=0';
	top.popup_win("../../interface/accounting/day_charges_list.php",features);
}

function popup_resize(maxWidth, maxHeight, maxAvail)
{
	maxWidth 	= maxWidth || 1270;
	maxHeight = maxHeight || 850;
	maxAvail 	= maxAvail || 0.9;
	
	var avail_w_90 = (screen.availWidth * maxAvail )
	var avail_h_90 = (screen.availHeight * maxAvail )
	var parWidth = (screen.availWidth > maxWidth) ? maxWidth :  avail_w_90;
	var parHeight = (screen.availHeight > maxHeight) ? maxHeight :  avail_h_90;
		
	window.resizeTo(parWidth,parHeight);
	
	var t = parseInt((screen.availHeight - window.outerHeight) / 2)
	var l = parseInt((screen.availWidth - window.outerWidth) / 2)
	window.moveTo(l,t);
}

function show_iportal_changes_alert_data(filter,pt_id){
	var form_data = 'iportal_filter='+filter+'&iportal_pt='+pt_id;
	var modal_title = '';
	var modal_id = '';
	
	switch(filter){
		case 'demographics':
		case 'medical':
			modal_title = 'Patient Portal - Demographics Changes';
			modal_id = 'iportal_changes_demograph';
		break;
		
		case 'cl_order':
			modal_title = 'Patient Portal - Contact Lens Order';
			modal_id = 'iportal_changes_cl_order';
		break;
		
		case 'registered_patients':
			modal_title = 'Patient Portal - Patient Registration';
			modal_id = 'iportal_changes_reg_pt';
		break;
        
		case 'iportal_payments':
			modal_title = 'Patient Portal - Payment(s)';
			modal_id = 'iportal_patient_payments';
		break;
	}
	
	
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + '/interface/physician_console/handle_pt_changes.php',
		data:form_data,
		type:'POST',
		dataType:'JSON',
		beforeSend:function(){
//			top.show_loading_image('show');
		},
		success:function(response){
			if(response){
			if(response.data_length > 0 && response.data.length){
				var modal_html = generate_iportal_alert_html(response,filter);
				if(modal_html){
					var div_id = modal_id;
					var title = modal_title;
					var footer = '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> <button type="button" class="btn btn-success" onclick="window.top.fmain.location.reload();">Reload with approved changes</button> <button type="button" class="btn btn-success" onclick="top.fmain.approve_all_operation(0,\''+modal_id+'\', \''+filter+'\');">Approve All</button>';
					if(filter=='iportal_payments'){
                        footer = '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> <button type="button" class="btn btn-success" onclick="top.fmain.iportal_payment_message_read(0,\''+modal_id+'\', \''+filter+'\');">Ok</button>';
                    }
					
					if(!top.$('#iportal_modal_val_'+modal_id).length){
						show_modal(div_id,title,modal_html,footer,'400','modal_90');
					}
					
					$('body').on('hide.bs.modal','#'+modal_id,function(event){
						var iportal_input = $('<input>');
						iportal_input.attr({
							'id' : 'iportal_modal_val_'+modal_id,
							'value':'shown',
							'type':'hidden',
						});
						top.$('body').append(iportal_input);
					});
				}
				else{
					if($("#"+modal_id).length > 0 ) $("#"+modal_id).html('').modal('hide');
				}
			}
			}
		},
		complete:function(){
			top.show_loading_image('hide');
		}
	});
}

function generate_iportal_alert_html(result,filter)
{
	var $_html = '';
	if(	result.data_length ==  0 ) return false;
	var table_header = '';
	var table_rows = '';
	if(filter == 'medical' || filter == 'demographics'){
		table_header += '<th class="col-xs-1 text-nowrap">Field Changed</th>';
		table_header += '<th class="col-xs-4">Old Value</th>';
		table_header += '<th class="col-xs-4">New Value</th>';
		table_header += '<th class="col-xs-3">Action</th>';	
		
		$.each(result.data,function(id,val){
			table_rows += '<tr>';
				table_rows += '<td>'+val['col_lbl']+'</td>';
				table_rows += '<td>'+val['old_val']+'</td>';
				table_rows += '<td>'+val['new_val']+'</td>';
				table_rows += '<td id="iportal_approve_'+val['id']+'" class="text-center">';
					table_rows += '<div class="btn-group">';
					table_rows += '<button class="btn btn-success" data-filter="'+filter+'" onClick="top.fmain.approve_operation(\''+val['id']+'\', this);">Approve</button> ';
					table_rows += '<button class="btn btn-danger"data-filter="'+filter+'" onClick="top.fmain.disapprove_operation(\''+val['id']+'\', this);">Decline</button>';
					table_rows += '</div>';
				table_rows += '</td>';
			table_rows += '</tr>';
		});
	}
	
	if(filter == 'iportal_payments'){
		table_header += '<th class="col-xs-4 text-nowrap">Field Changed</th>';
		table_header += '<th class="col-xs-8">New Value</th>';
		
		$.each(result.data,function(id,val){
			table_rows += '<tr>';
				table_rows += '<td>'+val['col_lbl']+'</td>';
				table_rows += '<td>'+val['new_val']+'</td>';
			table_rows += '</tr>';
		});
	}
	
	if(filter == 'cl_order'){
		table_header += '<th>Patient</th>';
		table_header += '<th class="text-nowrap">Date</th>';
		table_header += '<th>Eye</th>';
		table_header += '<th>Brand</th>';
		table_header += '<th>Manufacturer</th>';
		table_header += '<th>Disposable</th>';
		table_header += '<th>Supplies</th>';
		table_header += '<th>#Boxes</th>';
		table_header += '<th>Action</th>';
		
		var eye_str = '';
		var old_order_num = 0;
		$.each(result.data,function(id,val){	
			var td_date = '';
			var td_patient = '';
			var td_approval = '';
			var ordNum = val['temp_order_num'];
			if(old_order_num != ordNum){ cnt=0;}
			
			if(cnt==0){
				rowspan = val['order_count'];
				td_date = '<td rowspan="'+rowspan+'">'+val['orderedDate']+'</td>';
				td_patient = '<td rowspan="'+rowspan+'">'+val['pt_name']+'</td>';
				td_approval += '<td id="iportal_approve_'+val['id']+'" class="text-center" rowspan="'+rowspan+'">';
					td_approval += '<div class="btn-group">';
					td_approval += '<button class="btn btn-success" data-filter="'+filter+'" onClick="top.fmain.approve_operation(\''+ordNum+'\', this);">Approve</button> ';
					td_approval += '<button class="btn btn-danger" data-filter="'+filter+'" onClick="top.fmain.disapprove_operation(\''+ordNum+'\', this);">Decline</button>';
					td_approval += '</div>';
				td_approval += '</td>';
			}
			
			table_rows += '<tr>';
				table_rows += td_patient;	
				table_rows += td_date;	
				table_rows += '<td>'+val['eye']+'</td>';	
				table_rows += '<td>'+val['brand']+'</td>';	
				table_rows += '<td>'+val['manufacturer']+'</td>';	
				table_rows += '<td>'+val['disposable']+'</td>';	
				table_rows += '<td>'+val['supplies']+'</td>';	
				table_rows += '<td>'+val['boxes']+'</td>';	
				table_rows += td_approval;
			table_rows += '</tr>';
			
			old_order_num = ordNum;		
			cnt++;
		});
	}
	
	if(filter == 'registered_patients'){
		table_header += '<th>Patient Data</th>';
		table_header += '<th>Email Verification</th>';
		table_header += '<th>Action</th>';
		$.each(result.data,function(id,val){
			var pt_details = '';
			
			pt_details += '<div class="row">';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>First Name :</strong>    '+val['fname'];	
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Last Name :</strong>    '+val['lname'];	
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="clearfix"></div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Sex :</strong>    '+val['sex'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>DOB :</strong>    '+val['dob'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Email :</strong>    '+val['email'];
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="clearfix"></div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Home Phone :</strong>    '+val['phone_home'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Cell Phone :</strong>    '+val['phone_cell'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Work Phone :</strong>    '+val['phone_biz_ext']+' '+val['phone_biz'];
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="clearfix"></div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<div class="row">';
							pt_details += '<div class="col-sm-4">';
								pt_details += '<strong>Address :</strong>';
							pt_details += '</div>';
							pt_details += '<div class="col-sm-8">';
								pt_details += val['address'];
							pt_details += '</div>';
						pt_details += '</div>';
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>City :</strong>    '+val['city'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>'+top.zipLabel+' :</strong>    '+val['postal_code'];
					pt_details += '</div>';
				pt_details += '</div>';
			pt_details += '</div>';
			
			table_rows += '<tr>';
				table_rows += '<td>'+pt_details+'</td>';
				table_rows += '<td>'+val['auth_status']+'</td>';
				table_rows += '<td id="iportal_approve_'+val['id']+'" class="text-center">';
					table_rows += '<div class="btn-group">';
					table_rows += '<button class="btn btn-success" data-filter="'+filter+'" onClick="top.fmain.approve_operation(\''+val['id']+'\', this);">Approve</button> ';
					table_rows += '<button class="btn btn-danger" data-filter="'+filter+'"  onClick="top.fmain.disapprove_operation(\''+val['id']+'\', this);">Decline</button>';
					table_rows += '</div>';
				table_rows += '</td>';
			table_rows += '</tr>';
		});
	}
	
	
	$_html += '<div class="row">';
    var h1_title=' Following Patients has registered from Patient Portal ';
    var h2_title=' Please take the appropriate action.';
    if(filter == 'iportal_payments'){
        h1_title=' Following Payment(s) made from Patient Portal ';
        h2_title=' This is alert message for patient payment(s). ';
    }
    $_html += '<h1 style="font-size:18px;font-weight:normal;margin:0px;padding:0px;">'+h1_title+'</h1>';
    $_html += '<h2 style="font-size:14px;font-weight:normal;margin:5px 0px 10px 0px;padding:0px;">'+h2_title+'</h2>';
	$_html += '<div class="col-xs-12">';
	$_html += '<table class="table table-bordered table-hover table-striped">';
	$_html += '<thead class="header">';
	$_html += '<tr class="grythead">';
	$_html += table_header;
	$_html += '</tr>';
	$_html += '</thead>';
	$_html += '<tbody id="newTbodyBorder">';
	$_html	+=	table_rows;
	$_html	+=	'</tbody>';
	$_html	+=	'</table>';
	$_html	+=	'<input type="hidden" name="hidd_iportal_approve" id="hidd_iportal_approve" value="'+result.row_id_str+'">';
	$_html	+=	'</div>';
	$_html	+=	'</div>';
	 
	return $_html;
}

function approve_operation(row_id,ths)
{
	var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
	var approved_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Approved</button>';
	
	var filter = $(ths).data('filter');
	var url = '/interface/physician_console/handle_pt_changes.php';
	if(filter == 'cl_order'){
		url = '/iportal_config/approve_cl_orders.php';
	}
	
	if(filter == 'registered_patients'){
		url = '/iportal_config/handle_pt_registration.php';
	}
	
	ths_parent = $(ths).parent();	
	if(row_id != "" && parseInt(row_id))
	{
		ths_parent.html(processing_btn);	
	}
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + url,
		data:'sel_op=approve&row_id='+row_id+'&alert_filter='+filter,
		type:'POST',
		success:function(response){
			if(filter == 'registered_patients'){
				var resp = JSON.parse(response);
				if(resp.status=="success"){
					ths_parent.html(approved_btn).append('<div style="color:#090;font-weight:bold;">Approved<br />Patient Id: '+resp.pt_id+'</div>');		
				}
				else if(resp.status=="error"){
					ths_parent.html(processing_btn).append('<div style="color:#CC0000;font-weight:bold;">Error while approving</div>');			
				}
			}
		},
		complete:function(respData){
			if(filter == 'registered_patients'){
				
			}else{
				ths_parent.html(approved_btn);
			}
		}
	});
	
	return false;
}

function disapprove_operation(row_id,ths)
{
	var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
	var declined_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Declined</button>';
	
	var filter = $(ths).data('filter');
	var url = '/interface/physician_console/handle_pt_changes.php';
	if(filter == 'cl_order'){
		url = '/iportal_config/approve_cl_orders.php';
	}
	
	if(filter == 'registered_patients'){
		url = '/iportal_config/handle_pt_registration.php';
	}
	
	ths_parent = $(ths).parent();
	if(row_id != "" && parseInt(row_id))
	{
		ths_parent.html(processing_btn);	
	}
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + url,
		data:'sel_op=decline&row_id='+row_id,
		type:'POST',
		complete:function(respData)
		{
			resultData = respData.responseText;
			ths_parent.html(declined_btn);				
		}
	});		
}

function approve_all_operation(indx,modal_id,filter) 
{
	var url = '/interface/physician_console/handle_pt_changes.php';
	if(filter == 'cl_order'){
		url = '/iportal_config/approve_cl_orders.php';
	}
	
	if(filter == 'registered_patients'){
		url = '/iportal_config/handle_pt_registration.php';
	}
	
	var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
	var approved_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Approved</button>';
	
	all_id = $('#'+modal_id+' #hidd_iportal_approve').val();
	var all_id_arr = new Array();
	if(all_id) 
	{
		all_id_arr = all_id.split(',');
		row_id = all_id_arr[indx];
		if(all_id_arr.length>indx)
		{
			if(row_id != "" && parseInt(row_id))
			{
				if($('#iportal_approve_'+row_id).text() !="Declined")
					$('#iportal_approve_'+row_id).html(processing_btn);
			}
			
			$.ajax({
				url:top.JS_WEB_ROOT_PATH + url,
				data:'sel_op=approve&row_id='+row_id+'&alert_filter='+filter,
				type:'POST',
				complete:function(r)
				{
					if(r.responseText == "approved")
					$('#iportal_approve_'+row_id).html(approved_btn);		
					approve_all_operation(parseInt(indx)+1,modal_id,filter);
				}
			});	
		}
		if(all_id_arr.length == indx) top.fmain.location.reload(true);
	}else{
		if($("#"+modal_id).length > 0 ) $("#"+modal_id).html('').modal('hide');
	}
	return false;
}

function iportal_payment_message_read(indx,modal_id,filter){
    var url = '/interface/physician_console/handle_pt_changes.php';
    var all_id = $('#'+modal_id+' #hidd_iportal_approve').val();
	var all_id_arr = new Array();
	if(all_id) 
	{
		all_id_arr = all_id.split(',');
		var row_id = all_id_arr[indx];
		if(all_id_arr.length>indx)
		{
			$.ajax({
				url:top.JS_WEB_ROOT_PATH + url,
				data:'sel_op=iportal_payment_read&row_id='+row_id+'&alert_filter='+filter,
				type:'POST',
				complete:function(r)
				{ iportal_payment_message_read(parseInt(indx)+1,modal_id,filter); }
			});	
		}
		if(all_id_arr.length == indx) top.fmain.location.reload(true);
	}else{
		if($("#"+modal_id).length > 0 ) $("#"+modal_id).html('').modal('hide');
	}
	return false;
}

function view_only_acc_call(mode){
	top.fAlert("You do not have permission to perform this action.",'',340);
	if(mode == 1){
		return false;
	}
}

function changeColumnSelection(e) {
	var fid = e.currentTarget.id
	var obj = $('#'+fid);
	var values = obj.val();
	var page = obj.data('page');
	
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + '/interface/common/column_settings.php',
		type:'post',
		dataType:'json',
		data:{page:page,cols:(values?values.join(','):'')},
		beforeSend:function(){
			top.show_loading_image('show');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(r){
			if( r.success ) {
					obj.find('option').each(function(){
					var v = $(this).attr('value'); 
					var t = v.replace(/-/g, "");
					if( $.inArray(v,values) === -1 ){
						$("td."+t).addClass('hide');}
					else 
						$("td."+t).removeClass('hide');
				});	
						
			}
			else
				top.fAlert('Error: please try again');
		}
		
	})
	
	
}

// Functions for Pt Alerts
function loadItsDesc(selObj,textObj){
	var val = selObj.value; 
	var arrVal = val.split('-'); 	
	textObj.style.visibility = "visible";						
	if(arrVal[2]){
		textObj.value = arrVal[2];								
	}
	else{								
		textObj.value = "";
	}

}
function loadItsDescImmu(selObj,textObj){
	var val = selObj.value; 
	var arrVal = val.split('-'); 	
	textObj.style.visibility = "visible";													
	if(arrVal[2]){
		textObj.value = arrVal[2];								
	}
	else if(arrVal[0] == ""){
		textObj.value = "";
		textObj.style.visibility = "hidden";
	}
	else{								
		textObj.value = "";
	}

}
function drag_div_move(ele, ev) {
	 $(ele).draggable();
}
function acknowledged(op,form){
	op = op || 0; 
	if(document.getElementById('patSpesificDivAlert')){
		if(op ==1) {
			$(form).children('#disablePatAlertThisSession').val('yes');
		}
		else {
			$(form).children('#disablePatAlertThisSession').val('');
		}
		$(form).children('#patSpesificDivAlert').css('display','none');
	}
}
// End Functions for Pt Alerts

/*********FUNCTION BELOW TO MANAGE AN CONTAINER HEIGHT ACCORDING TO OTHER OR REMAINING AFTER OTHERS.***/
function manage_section_height(dest_obj,source_obj_array,flexibility){
	//---OBJ = .class_name or #id_of_obj.
	if(typeof(flexibility)=='undefined') flexibility = 0;
	h = 0;
	//console.log(source_obj_array);
	$(source_obj_array).each(function(index, element) {
		//console.log($(source_obj_array[index]));
        h += $(source_obj_array[index]).height();
    });
	if(flexibility < 0) h = h - parseInt(flexibility);
	else h = h + parseInt(flexibility);
	wh = window.innerHeight;
	//alert(h+' :: '+wh+' :: '+flexibility);
	$(dest_obj).height(wh-h);
}

//Open new patient window
function newPatient_info_main(){
	var parWidth = parseInt($(window).width() - 500);
	var win_height = $(window).height();
	top.popup_win(top.JS_WEB_ROOT_PATH+"/interface/scheduler/common/new_patient_info_popup_new.php?source=demographics&search=true&frm_status=show_check_in&popheight="+win_height, "width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1")
	//window.open(top.JS_WEB_ROOT_PATH+"/interface/scheduler/common/new_patient_info_popup_new.php?source=demographics&search=true&frm_status=show_check_in&popheight="+win_height, "newPatientWindow", "width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
}


function check_rule_manager(obj_value, section) {
    section=(section)?section:'';
    var post_data = {obj_value: obj_value,section:section}
    $.ajax({
		url:top.JS_WEB_ROOT_PATH + '/interface/common/assign_new_task.php',
		type:'post',
		dataType:'json',
		data:post_data,
		beforeSend:function(){
			top.show_loading_image('show');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(r){
            
		}
		
	});
	
}
var o = '';
function del_doc(__this,confirm){
	o = __this || o;
	if( typeof confirm == 'undefined' ) {
		top.fmain.frames[0].document.write('');
		top.fancyConfirm("Sure! you want to delete this document ?","top.del_doc('',true)");
	}
	else { 
		var u = $(o).data('url');
		if($(o).length > 0 && typeof u !== 'undefined' ) {
			if( top.fmain ) top.fmain.location.href=u;
			if( top.all_data ) top.all_data.location.href=u;
		}
		else	
			top.fAlert('Unable to delete. Please refresh the tab and try again. ')
	}

}
 
function dismissTaskAlert () {
	top.master_ajax_tunnel(top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php?task=task_alert_shown');
	$("#task_alerts",top.document).modal('hide');
}

/*---for zeiss integration***/
function ZeissViewer(pid, user, pass, path){
	if(pid=="")pid = window.top.$("#patient_id").val();
    u=user;
	p=pass;
	params = " -username "+u+" -password "+p+" -patientId "+pid;
	exeURL= '"'+path+'\\launchFORUM.cmd"'+params;
	try{
		WSH=new ActiveXObject("WScript.Shell");
		WSH.run(exeURL);
	}catch(e){
		if(e!=''){
			try{
				exeURL = exeURL.replace('Program Files','Program Files (x86)');
				WSH=new ActiveXObject("WScript.Shell");
				WSH.run(exeURL);
			}catch(er){
				top.fAlert('<div class="text-center">Check if Zeiss Application installed.<br>'+er.message+'</div>');
			}
		}
	}
}

function sig_web_alert(){
	var msg = 'Error, make sure you have installed SigWeb.<br> <a href="https://www.topazsystems.com/software/sigweb.exe" target="_BLANK" class="purple-text">Click here</a> to download SigWeb service.<br>';
	msg += '<a href="https://www.topazsystems.com/Software/sigweb_install.pdf" target="_BLANK" class="purple-text">Click here</a> to read installation instructions before installing';
	top.fAlert(msg);
}

var heardAboutSearch = ['Family','Friends','Doctor','Previous Patient.','Previous Patient'];
function searchHeardAbout(){
	var WRP = top.JS_WEB_ROOT_PATH
	var search_val = $("#heardAbtSearch").val();
	search_val = $.trim(search_val);
	target_search_val = '';
	if(search_val!="")
	{
		search_val_arr = search_val.split(',');
		if(search_val_arr[0]!="" && typeof search_val_arr[0] != "undefined")
		{
			last_name_val = $.trim(search_val_arr[0]); 
			last_mid_name_arr = last_name_val.split(' ');			
			target_search_val = $.trim(last_mid_name_arr[1]); 	
			if(target_search_val == "" || typeof target_search_val == "undefined")
			{
				target_search_val = last_mid_name_arr[0];			
			}
		}
		else
		{
			target_search_val = $.trim(search_val_arr[0]); 	
		}
	}
	
	if( !target_search_val ) return false;
	
	var heardAbtVal = $("#elem_heardAbtUs").val();
	var tmpArr = heardAbtVal.split("-");
	
	heardAbtVal = tmpArr[1].trim();
	
	if( !heardAbtVal ) return false;
	
	if( $.inArray(heardAbtVal, heardAboutSearch) !== -1) {
		
		if( WRP == 'undefined') {
			if( typeof window.opener !== 'undefined' ){
				WRP = window.opener.top.JS_WEB_ROOT_PATH;
			}
		}
		if( heardAbtVal == 'Doctor' ) {
			window.open(WRP + "/interface/admin/users/searchPhysician.php?btn_sub=search&sel_by=LastName&txt_for="+target_search_val,"window1","width=800,height=500,scrollbars=yes, status=1");	
		}
		else { 
			var w = window.open(WRP + "/interface/scheduler/search_patient_popup.php?sel_by=Active&txt_for="+target_search_val+"&btn_sub=Search&call_from=demographics","PatientWindow","width=800,height=500,top=420,left=150,scrollbars=yes");
		}
	}
	
}

function setHeardAboutUsVal(pid,name){
	document.getElementById('heardAbtSearchId').value = pid;
	document.getElementById('heardAbtSearch').value = name + '-' + pid;
}

function get_phy_name_from_search(strVal,id){
	if(top.$('#appt_scheduler_status').val()=='loaded') {
		document.getElementById('front_primary_care_id').value = id;
		document.getElementById('front_primary_care_name').value = strVal;
	}
	else{
		document.getElementById('heardAbtSearchId').value = id;
		document.getElementById('heardAbtSearch').value = strVal;
	}
	
}
//number only
function validate_num(obj){
	var num = $(obj).val();
	num = num.trim();
	if(num!=""){
		num = num.replace(/[^\d.]/, '');	/*Strip non int values*/
		if(num=="")num=0;
	}
	$(obj).val(num);
}

function capitalize_letter(str) {
	return str.replace(/(^\S|\s\S)/g, function(match, g1, offset, origstr) {
  	return g1.toUpperCase(); 
  });
}

function view_only_call(){
	top.fAlert("You do not have permission to perform this action.");
	return false;
}	var scan_completed = 0;
	function set_Focus(){
		//Resets the value of the invis text box to null
		document.getElementById("scanner").value="";
		if( document.getElementById("divAjaxLoader") ) {
			document.getElementById("divAjaxLoader").style.display = 'block';
		} else if( top.show_loading_image) {
				top.show_loading_image('show');
		}	
		//sets the focus to the invisiable text box.
		document.getElementById("scanner").focus();
		scan_completed=0;
		
		//Run this check 6 times at 10 seconds intervals. This is the dirty way to do this.
		setTimeout(function(){scan_Complete()},5000);
		setTimeout(function(){scan_Complete()},10000);
		setTimeout(function(){scan_Complete()},15000);
		setTimeout(function(){scan_Complete()},20000);
		setTimeout(function(){scan_Complete()},25000);
		setTimeout(function(){scan_Complete()},30000);
	}

	function scan_Complete(){
		//Check this variable before doing anything else. 
		if(scan_completed == 1){
			if( document.getElementById("divAjaxLoader") ) {
					document.getElementById("divAjaxLoader").style.display = 'none';
			} else if( top.show_loading_image) {
				top.show_loading_image('hide');
			}
 			
			return;
		}else{
			//Pulled from the scanner element	
			x = document.getElementById("scanner").value;
			n = x.indexOf("*");
			//checks for a * in the string
			if(n > 0 ){
				//also kill this function
					
				//If the check comes back greater then 0 then run the string through the parsing function
				parse_Driving_License_Feed(x);	
			}			
		}
	}


	function parse_Driving_License_Feed(data){
		console.log(data);
		scan_completed = 1;
		
		processing = data.split(";");

		processing_fname = processing[0];
		processing_mname = processing[1];
		processing_lname = processing[2];
		processing_dob = processing[3];
		processing_address1 = processing[4];
		processing_address2 = processing[5];
		processing_city = processing[6];
		processing_state = processing[7];
		processing_zip = processing[8];
		processing_driving_license = processing[9];
		processing_gender = processing[10];

		parsed_first_name = processing_fname.split(":");
		parsed_m_name = processing_mname.split(":");
		parsed_last_name = processing_lname.split(":");
		parsed_dob = processing_dob.split(":");
		parsed_address1 = processing_address1.split(":");
		parsed_address2 = processing_address2.split(":");
		parsed_city = processing_city.split(":");
		parsed_state = processing_state.split(":");
		parsed_zip = processing_zip.split(":");
		parsed_driving_license = processing_driving_license.split(":");
		parsed_gender = processing_gender.split(":");


		first_name = parsed_first_name[1];	
		m_name = parsed_m_name[1];
		last_name = parsed_last_name[1];
		dob = parsed_dob[1];
		address1 = parsed_address1[1];
		address2 = parsed_address2[1];
		city = parsed_city[1];
		state = parsed_state[1];
		zip = parsed_zip[1];
		driving_license = parsed_driving_license[1];
		gender =  parsed_gender[1];

		
		//Sometimes DL list gender with a 1 or 2, 1 being male, 2 being female

			if(gender == 1||gender == "M"){
				gender = "Male";				
			}
			if(gender == 2|| gender == "F"){
				gender = "Female";				
			}

		//Cut up the zip code
			if(zip.length>5){
				//grabs the zipcode
				zip1 = zip.substr(0,5);
				//grabs the extention
				zip2 = zip.substr(5,8);
			}else{	
				//This meaning that only the first 5 digits of the zip code are present.
				zip1 = zip;
				zip2 = "";
			}

		
		//DOB REFORMATING

		//Grabs the year
			DOB1 = dob.substr(0,4);
		//grabs the month
			DOB2 = dob.substr(4,2);
		//grabs the day
			DOB3 = dob.substr(6,2);

			dob = DOB2 + "-" + DOB3 + "-" + DOB1;
			
		push_Data_To_Document(first_name, m_name, last_name, dob, address1, address2, city, state, zip1, zip2, driving_license, gender);
	}



	function push_Data_To_Document(first_name, m_name, last_name, dob, address1, address2, city, state, zip1, zip2, driving_license, gender){
		if(confirm("Do you want to auto populate these values \n First Name: " + first_name + "\n Middle Name: " + m_name + "\n Last Name : " + last_name + "\n Date of Birth: " + dob + "\n Address 1: " + address1 + "\n Address 2: " + address2 + "\n City: " + city + "\n State: " + state + "\n Zipcode: " + zip1 + "\n Zipcode Extension: " + zip2 + "\n Driving License: " + driving_license + "\n Gender: " + gender )){
			document.getElementById("fname").value = first_name;
			document.getElementById("mname").value = m_name;
			document.getElementById("lname").value = last_name;
			document.getElementById("dob").value = dob;
			document.getElementById("street").value = address1;
			document.getElementById("city").value = city;
			document.getElementById("state").value = state;
			document.getElementById("code").value = zip1;
			document.getElementById("dlicence").value = driving_license;															
			if(gender == "Male"){
				document.getElementById("title").selectedIndex = 1;
				if(document.getElementById("sex")!=null){
					document.getElementById("sex").selectedIndex = 1;
				}
				if(document.getElementById("selGender")!=null){
					document.getElementById("selGender").selectedIndex = 1;
				}
			}
				
			if(gender == "Female"){
				document.getElementById("title").selectedIndex = 3;		
				if(document.getElementById("sex")!=null){
					document.getElementById("sex").selectedIndex = 2;
				}
				if(document.getElementById("selGender")!=null){
					document.getElementById("selGender").selectedIndex = 2;
				}
			}
		}
		
		if( document.getElementById("divAjaxLoader") ) {
			document.getElementById("divAjaxLoader").style.display = 'none';
		} else if( top.show_loading_image) {
			top.show_loading_image('hide');
		}	
		
	}// Global Javascript variable for Demographics
var count=0;
var grid_id = 0;
var search_data = [];
var search_header_html = '<h4 class="modal-title col-xs-4 col-sm-5 col-md-3" id="modal_title">Select Patient</h4><span class="col-xs-6 col-md-4 input-group"><input type="text" id="sp_ajax" class="form-control col-xs-5" title="Search Patient (by last name) " placeholder="Search Patient (by last name)" /><label class="input-group-addon btn" for="sp_ajax"><span class="glyphicon glyphicon-search"></span></label></span>';

var myvar = top.fmain;

/*
* Function : xhr_ajax 
* Purpose - To handle Ajax Request and Response
*						This function will use all data attributes
*						to send params in ajax request 	
* Params -
*	r:	
* $_this - Holds this object from which event Occured 
* c : Boolean true|false whether to get current field value
* f : file name to which ajax request send must be in patient info/ajax folder
*/
function xhr_ajax(r,$_this,c,f)
{ 
	f = f || 'demographics/ajax_handler';
	if(typeof c !== 'boolean') { c = false; }
	
	if(typeof $_this == 'object')
	{ 
		var p = $_this.data();
		var d = '';
		$.each(p,function(i,v){ if(i !== 'prevVal') { d	+= '&' + i + '=' + v;} });
		if(c) d += '&val='+$_this.val();
		d = d.substr(1);
		
		var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/"+f+".php?"+d;
		var callback = top.xhr_ajax;
		if(top.fmain) callback = top.fmain.xhr_ajax;
		top.master_ajax_tunnel(url,callback,'','json');
		top.show_loading_image('hide');
	}
	else if(typeof r !== 'undefined')
	{ 
		if(r.action === 'search_patient')
		{ 
				grid_id = r.grid; search_data = r.data;
				$("#search_patient_result #sp_ajax").data('grid',r.grid);
				var ht = ['Name','ID','Address','Phone'];
				if(r.grid == 0) ht = ['Name','SS','DOB','ID'];
				var html = '';
				
					html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
					html	+=	'<thead class="header">';
					html	+=	'<tr class="grythead">';	
					html	+=	'<th class="col-xs-3">'+ht[0]+'</th>';
					html	+=	'<th class="col-xs-2">'+ht[1]+'</th>';
					html	+=	'<th class="col-xs-4">'+ht[2]+'</th>';
					html	+=	'<th class="col-xs-3">'+ht[3]+'</th>';
					html	+=	'</tr>';
					html	+=	'</thead>';
					html	+=	'<tbody>';
					
				if(r.pdata.length > 0 )
				{ 
					var g = (r.grid > 0) ? 'family' : 'resp';
					var k = (r.iKey > 0) ? 'data-i-key="'+r.iKey+'"' : '';
					for(i in r.pdata) {
						
						var f1 = (r.grid > 0) ? r.pdata[i].name 	: r.pdata[i].name;
						var f2 = (r.grid > 0) ? r.pdata[i].id 		: r.pdata[i].ss;
						var f3 = (r.grid > 0) ? r.pdata[i].address: r.pdata[i].dob;
						var f4 = (r.grid > 0) ? r.pdata[i].phone 	: r.pdata[i].id;
						
						html	+=	'<tr >';
						html	+=	'<td data-label="'+ht[0]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f1+'</a></td>';
						html	+=	'<td data-label="'+ht[1]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f2+'</a></td>';
						html	+=	'<td data-label="'+ht[2]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f3+'</a></td>';
						html	+=	'<td data-label="'+ht[3]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f4+'</a></td>';
						html	+=	'</tr>';
					}
				}
				else
				{
					html += '<tr><td colspan="4" class="bg-warning">No Patient Found.</td></tr>'
				}
				html	+=	'</tbody>';
				html	+=	'</table>';
				
				$("#search_patient_result .modal-body").html(html);
				
		}
		
		else if(r.action === 'search_physician')
		{ 
			$("#search_physician_result #phy_ajax").data('text-box',r.text_box).data('id-box',r.id_box).val('');
			$("#search_physician_result .modal-body").html(r.html);
		}
		
		else if (r.action === 'show_patient_access_log')
		{
				var html = '';
				html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
				html	+=	'<thead class="header">';
				html	+=	'<tr class="grythead">';	
				html	+=	'<th class="col-xs-4">Action</th>';
				html	+=	'<th class="col-xs-4">Description</th>';
				html	+=	'<th class="col-xs-4">Access Time</th>';
				html	+=	'</tr>';
				html	+=	'</thead>';
				html	+=	'<tbody>';
					
				if(r.data.length > 0 )
				{ 
					for(i in r.data)
					{
						html	+=	'<tr >';
						html	+=	'<td data-label="Action">'+r.data[i].action+'</td>';
						html	+=	'<td data-label="Description">'+r.data[i].desc+'</td>';
						html	+=	'<td data-label="Access Time">'+r.data[i].time+'</td>';
						html	+=	'</tr>';
					}
				}
				else
				{
					html += '<tr><td colspan="3" class="text-center">No Record Found.</td></tr>'
				}
				html	+=	'</tbody>';
				html	+=	'</table>';
				
				
				show_modal('access_log_modal',r.title,html,'',350,'modal-md',false);
			
		}
		
		else if ( r.action === 'login_history' )
		{
			var html = '';
				html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
				html	+=	'<thead class="header">';
				html	+=	'<tr class="grythead">';	
				html	+=	'<th class="col-xs-4">#</th>';
				html	+=	'<th class="col-xs-4">Login Date</th>';
				html	+=	'<th class="col-xs-4">Login Time</th>';
				html	+=	'</tr>';
				html	+=	'</thead>';
				html	+=	'<tbody>';
					
				if(r.data.length > 0 )
				{ 
					for(i in r.data)
					{
						html	+=	'<tr >';
						html	+=	'<td data-label="Action">'+r.data[i].counter+'</td>';
						html	+=	'<td data-label="Description">'+r.data[i].date+'</td>';
						html	+=	'<td data-label="Access Time">'+r.data[i].time+'</td>';
						html	+=	'</tr>';
					}
				}
				else
				{
					html += '<tr><td colspan="3" class="text-center">No Record Found.</td></tr>'
				}
				html	+=	'</tbody>';
				html	+=	'</table>';
				
				
				show_modal('login_history_modal',r.title,html,'',350,'modal-md',false);
			
		}
		
		else if( r.action === 'temp_key_generate')
		{
			var response = $.trim(r.response);
			if(response == 'no_priv')
			{
				$("#pt_override_div").modal('show');
				$("#user_password").focus();
				$("#done_btn_pt_override").attr('data-temp-key-size',r.tempKeySize);
			}
			else if(response == 'user_has_no_priv'){
				top.fAlert("Incorrect Password",'',$("#user_password"));
			}
			else if(response=='user_incorrect')
			{
				top.fAlert("Incorrect Password",'',$("#user_password"));
			}
			else
			{
				$('#temp_key').val(response);
				$("#pt_override_div").modal('hide');
				$("#user_password").val("");
				$("#temp_key_chk_val").prop("checked",false);
			}
			
		}
		
		else if(r.action === 'demographics_history')
		{
			show_modal('demographics_history',r.title,r.html,'',400,'modal-lg',false);
		}
		
		else if(r.action === 'validate_form')
		{ 
			var responseText = r.response;
			
			var msg_code_str = '';
			var matched_ssn_pt = '';
			
			var p_obj 	= myvar.$("#pass1");
			var cp_obj	= myvar.$("#pass2"); 
			var rp_obj	=	myvar.$("#elem_physicianName");	
			var pp_obj	=	myvar.$("#primaryCarePhy");	
			var ss_obj	=	myvar.$("#ss");	
			var ss_obj1	=	myvar.$("#ss1");	
			var un_obj1	=	myvar.$("#usernm");	
			
			if(responseText !== "")
			{
				var arrAJAXResp = responseText.split("~~");
				
				//Referring Physician
				if(rp_obj)
				{		
					var refPhyNameNew = rp_obj.val().trim();
					if(refPhyNameNew !== "")
					{								
						var arrPhyNameNewFull = refPhyNameNew.split("; ");
						var arrPhyNameNew = arrPhyNameNewFull[0].split(",");
						if(arrPhyNameNew.length < 2 || arrPhyNameNew.length > 2){
							msg_code_str += "6,";
						}
					}								
				}
				
				//Referring Physician
				if(pp_obj)
				{			
					if(pp_obj.val() !== ""){								
						var primaryPhyNameNew = trim(pp_obj.val());
						var arrPriPhyNameNewFull = primaryPhyNameNew.split("; ");
						var arrPriPhyNameNew = arrPriPhyNameNewFull[0].split(",");
						if(arrPriPhyNameNew.length < 2 || arrPriPhyNameNew.length > 2){
							msg_code_str += "12,";
						}
					}								
				}
				
				//ssn format check
				if(ss_obj.val() !== "")
				{
					var ssn_format = validate_ssn_format(ss_obj.val());
					if(ssn_format == false){
						msg_code_str += "2,";
					}
				}

				//ssn format for resp party check
				if(ss_obj1.val() != "")
				{
					var ssn_format = validate_ssn_format(ss_obj1.val());
					if(ssn_format == false){
						msg_code_str += "11,";
					}
				}
				
				//unique ssn			
				if(arrAJAXResp[0] == "1")
				{
					msg_code_str += "3,";
					ss_obj.trigger('change');
					matched_ssn_pt = arrAJAXResp[2];
				}
				
				//unique login id
				if(arrAJAXResp[1] == "1"){
					//msg_code_str += "4,";
					//un_obj1.trigger('change');
				}
				
				//responsible party alert
				if(resSsnNumber !='' && arrAJAXResp[3] == "1"){
					document.getElementById("hid_resp_party_sel_our_sys").value = "yes";
				}
				
				//confirm pass
				
				if(p_obj){ 
					if(p_obj.val() !== cp_obj.val() ){	
						msg_code_str += "1,";
						p_obj.trigger('change')
						cp_obj.trigger('change')
					}
				}
				
				if(msg_code_str != "")
				{
					var arr_func = [];
					arr_func[0] = "return false";
					arr_func[1] = "";
					window.top.show_loading_image("hide");
					window.top.fmain.pi_show_alert("alert", msg_code_str, arr_msg, arr_focus, arr_func, null, '', matched_ssn_pt);
				}
				else
				{
					var mandatory_fields = window.top.fmain.getMandatoryMsg();
					var advisory_fields = window.top.fmain.showPracMendAlert();
					
					var str_msg = "";
					var str_focus = "";
					for(i = 0; i < arr_msg.length; i++){
						str_msg += arr_msg[i]+"__";
					}
					for(i = 0; i < arr_focus.length; i++){
						str_focus += arr_focus[i]+"__";
					}
					
					if(mandatory_fields != true)
					{
						var arr_func = new Array();
						arr_func[0] = "window.top.show_loading_image('hide');";
						arr_func[1] = "window.top.show_loading_image('hide');";
						window.top.fmain.pi_show_alert("mandatory", mandatory_fields, arr_msg, arr_focus, arr_func, "", 400);
					}
					else if(advisory_fields != true)
					{
						var arr_func = [];
						arr_func[0] = "window.top.show_loading_image('show',250, 'Please wait..');window.top.fmain.ask_for_after_save_actions('"+escape(str_msg)+"', '"+escape(str_focus)+"')";
						arr_func[1] = "window.top.show_loading_image('hide');";
						window.top.fmain.pi_show_alert("confirm", advisory_fields, arr_msg, arr_focus, arr_func, "", 400);
					}
					else{
						window.top.fmain.ask_for_after_save_actions('','');
						//window.top.fmain.process_save();
					}
				}
			}
		}
		
		// insurance ajax response
		else if(r.action === 'insCompsAnchors')
		{
			var j = r.data;
			arr_r =	j.split("||~***~||");
			top.fmain.$('#priInsCompData').html(arr_r[0]);
			top.fmain.$('#secInsCompData').html(arr_r[1]);
			top.fmain.$('#terInsCompData').html(arr_r[2]);
		}
		
		else if(r.action === 'ins_comp_practice_code')
		{
			$("#tool_tip_div").html(r.data).show('fast');
		}
		
		else if(r.action === 'ins_eligibility')
		{
			var strResp = r.data;
			var arrResp = strResp.split("~~");
			if(arrResp[0] == "1" || arrResp[0] == 1)
			{
				var alertResp = "";
				if(arrResp[1] != ""){
					alertResp += "Patient Eligibility Or Benefit Information Status :"+arrResp[1]+"\n";
				}
				if(arrResp[2] != ""){
					alertResp += "With Insurance Type Code :"+arrResp[2]+"\n \n";
				}
				if(alertResp != "")
				{
					if(arrResp[3] == "A")
					{
					//	document.getElementById('imgEligibility').src = "../../../images/eligibility_green.png";
					}
					else if(arrResp[3] == "INA")
					{
					//	document.getElementById('imgEligibility').src = "../../../images/eligibility_red.png";
					}
					//document.getElementById('imgEligibility').title = alertResp;
					var elId = parseInt(arrResp[4]);
					var strShowMsg = arrResp[5];
					if((elId > 0) && (strShowMsg) == "yes")
					{
						alertResp += "Would you like to set Co-Pay, Deductible and Co-Insurance!\n"
					}
					if((elId > 0) && (strShowMsg) == "yes")
					{
						if(confirm(alertResp) == true)
						{
							var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/eligibility/eligibility_report.php?set_rte_amt=yes&id='+elId;
							window.open(url,'setAmountRTE','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,left=10,top=10');
						}
					}
					else{
						top.fAlert(alertResp);
					}
				}
			}
			else if(arrResp[0] == "2" || arrResp[0] == 2)
			{
				if(arrResp[1] != "")
				{
					top.fAlert(arrResp[1]);
					document.getElementById('imgEligibility').src = "../../../images/eligibility_red.png";
					document.getElementById('imgEligibility').title = arrResp[1];
				}
			}
			else
			{
				top.fAlert(arrResp[0]);
			}
		}
		
		else if(r.action === 'ins_history')
		{
			$("#ins_history_modal .modal-body").html(r.html);
			$("#ins_history_modal").modal('show');
			set_modal_height('ins_history_modal');
		}
		
		else if(r.action === 'check_exist_ins')
		{
			if(r.is_exist)
			{
				top.fAlert('Please expire previous '+r.ins_type+' insurance company.');
			}
			else
			{
				$("#copy_ins_submit_txt").val('Submit');
				top.show_loading_image('show');
				$("#copy_ins_form").submit();
			}
			
		}
		
	}
}

/*
* Function : chk_change 
* Purpose - To Detect change in Prev and current value
* Params -	
* olddata - Holds Previous Values saved in DB
* newData - Holds Current displaying value in Input/Select Box
*/
function chk_change(olddata,newData,e)
{
	e = event ? event : Event;
	var character_code = e.which ? e.which : e.keyCode;
	if(character_code!== 9 && character_code !== 16 ){
		if(olddata !== newData){
			change_flag = true;
		}else{
			if(change_flag !== true){
				change_flag = false;
			}
		}
	}	
}


/*
* Function : search_patient 
* Purpose - to search for patient in Family Info || Responsible Party
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function search_patient($_this)
{
		var v = $_this.val().trim();
		if(v)
		{
			if(!$("#search_patient_result").hasClass('in'))
			{
				$("#search_patient_result").modal();	
			}
			$("#search_patient_result .modal-body").html('<div class="loader"></div>');	
			xhr_ajax('',$_this,true,$_this.attr('data-action'));
		}
		
		if($_this.attr('id') !== 'sp_ajax' && !v)
		{
			top.fAlert('Please enter last name to precede search');
		}
	
}

/*
* Function : fill_grid_info 
* Purpose - to fill fields info from search result
* Params -	
* id - hold grid id | t = type either for family||responsible party 
*/	
function fill_grid_info(id,t,call_from)
{		
		call_from = call_from || 1;
		if(id == '0' || typeof id == 'undefined') return;
		
		var d = search_data[id];
		
		if(t == 'family')
		{
			// Chk Mobile Field is missing -chkMobileTableFamilyInformation_
			var flds = Array('fname_table_family_information','mname_table_family_information','lname_table_family_information','street1_table_family_information','street2_table_family_information','code_table_family_information','city_table_family_information','state_table_family_information','email_table_family_information','phone_home_table_family_information','phone_work_table_family_information','phone_cell_table_family_information');
			
			$("#"+flds[0]+grid_id).val(d.fname);
			$("#"+flds[1]+grid_id).val(d.mname);
			$("#"+flds[2]+grid_id).val(d.lname);
			$("#"+flds[3]+grid_id).val(d.street);
			$("#"+flds[4]+grid_id).val(d.street2);
			$("#"+flds[5]+grid_id).val(d.postal_code);
			$("#"+flds[6]+grid_id).val(d.city);
			$("#"+flds[7]+grid_id).val(d.state);
			$("#"+flds[8]+grid_id).val(d.email);
			$("#"+flds[9]+grid_id).val(d.phone_home);
			$("#"+flds[10]+grid_id).val(d.phone_biz);
			$("#"+flds[11]+grid_id).val(d.phone_cell);
			//$("#"+flds[12]+grid_id).val(d.chk_mobile);
			
			var grid_obj = $("#table_family_information_"+grid_id);
		}
		else
		{
			if(top.fmain.insuranceCaseFrm)
			{
				popUpRelationValue(call_from,d)
				var grid_obj = $("#insPolicy"+call_from+"_table");
			}
			else
			{
				$("#title1").val(d.title);
				$("#fname1").val(d.fname);
				$("#mname1").val(d.mname);
				$("#lname1").val(d.lname);
				$("#suffix1").val(d.suffix);
				$("#status1").val(d.status);
				$("#dob1").val(d.DOB);
				$("#sex1").val(d.sex);
				$("#street1").val(d.street);
				$("#street_emp").val(d.street2);
				$("#rcode").val(d.postal_code);
				$("#rcity").val(d.city);
				$("#rstate").val(d.state);	
				$("#ss1").val(d.ss);	
				$("#phone_home1").val(d.phone_home);	
				$("#phone_biz1").val(d.phone_biz);	
				$("#phone_cell1").val(d.phone_cell);	
				$("#hid_resp_party_sel_our_sys").val('yes');	
				
				$("#title1.selectpicker,#status1.selectpicker").selectpicker('refresh')
					
				var grid_obj = $("#resp_container");
			}
		}
		
		$("#search_patient_result .modal-body").html('<div class="loader"></div>');
		$("#search_patient_result").modal('hide');
		grid_obj.find('input[type="text"],select.minimal,select.selectpicker').triggerHandler('change');
			
}

/*
* Function : search_physician 
* Purpose - to search for Reffering || Primary Care || Co Managed Physicians
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function search_physician($_this)
{
	var id = $_this.attr('id');
	if( id === 'phy_ajax')
	{
		$_this.data('search-by',$("#search_by").val())
	}
	var v = $.trim($_this.val());
	if(v)
	{
		if(!$("#search_physician_result").hasClass('in'))
		{
			$("#search_physician_result").modal();
			$("#search_physician_result").find('#phy_ajax').val('');
		}
		$("#search_physician_result .modal-body").html('<div class="loader"></div>');	
		xhr_ajax('',$_this,true,$_this.data('action'));
	}
	
	else
	{
		top.fAlert('Please enter some text for search');
	}
		
}

function save_data(e)
{
	var characterCode //literal character code will be stored in this variable
	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e
		characterCode = e.which //character code is contained in NN4's which property
	}
	else{
		e = event
		characterCode = e.keyCode //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		if(change_flag==true){
			//top.fmain.front.validSaves();
		}
		else{
			chkCall();				
		}
	}
	else{
		return true 
	}	
}

/*
* Function : do_date_check 
* Purpose - To check From date is greater from To Date
* Params -	
* from - Holds From date field object
* to - Holds To date field object
*/
function do_date_check(from, to)
{	
	if(validate_date(to) && validate_date(from) )
	{
		if (from.value != "" && to.value != "")
		{ 
			if (parse_date(from.value, top.jquery_date_format) >= parse_date(to.value, top.jquery_date_format)) {	
				return true;
			}
			else 
			{	
				if (from.value == "" || to.value == ""){ }
				else{ 
					to.value="";
					top.fAlert("Date of birth can not be greater than current date.");
					return false;
				}
			}
		}
	}
}

/*
* Function : parse_date 
* Purpose - Date Parser according to given format
* Params -	
* input - Holds Date String
* format - Holds Date format in which date will be parsed
*					 IF Not Defined Then Default will be Used from Top 
*/
function parse_date(input, format)
{ 
	if(input)
	{
 		format = format || top.jquery_date_format;
  	var parts = input.match(/(\d+)/g),  
 		i = 0, fmt = {};
		// extract date-part indexes from the format 
  	format.replace(/(Y|d|m)/g, function(part) { fmt[part] = i++; }); 
 		return new Date(parts[fmt['Y']], parts[fmt['m']]-1, parts[fmt['d']]); 
	}
	return;
}


function getPosition(e)
{
	e = window.event;
	var cursor = {x:0, y:0};
	var de = document.documentElement;
	var b = document.body;
	cursor.x = e.clientX + 
		(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	cursor.y = e.clientY + 
		(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	return cursor;
}

var newzip_code;
//To get city and state on the basis of zipcode in add new patient
function zip_vs_state(zip_code,page){
		if((page=="add_patient") || (page=="edit_patient"))	{
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;			
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChanged)
		}else if(page=="occupation"){
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateoccupation)
		}else if(page=="resp_party"){
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateresp_party)
		}else if(page=="add_facility")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChanged)
		}else if(page=="primary")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstate_primary)
		}else if(page=="secondary")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstate_secondary)
		}else if(page=="tertiary")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstate_tertiary)
		}else if(page=="new_patient_popup")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChanged)
		}
		else if(page=="RefferringPhysician"){
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedRef)
		}
		else if(page=="add_insurance")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			newzip_code = zip_code;
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedIns)
		}
		else if(page=="PosFacility")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedPosFacility)
		}
		else if(page=="PosFacilityGroup")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedPosFacilityGroup)
		}
		else if(page=="add_groups")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedAdd_groups)
		}
		else if(page=="add_rem_groups")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedAdd_groups_rem)
		}
		else if(page=="policy")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.setPolicyState)
		}
}

var family_index = '';
function zip_vs_state_family_state(zip_code, num_id)
{
	if(zip_code == '') { return false; }
	var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
	url=url+"?zipcode="+zip_code
	family_index = num_id;
	top.master_ajax_tunnel(url,top.fmain.setfamilyZipCode)
}

function setfamilyZipCode(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city_table_family_information" + family_index).value=trim(val[0]);
		document.getElementById("state_table_family_information" + family_index).value=trim(val[1]);
		$("#city_table_family_information"+family_index+",#state_table_family_information"+family_index).trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code_table_family_information"+family_index+",#city_table_family_information"+family_index+",#state_table_family_information"+family_index+"").val('');
		$("#code_table_family_information"+family_index).trigger('change').focus();	$("#code_table_family_information"+family_index+",#city_table_family_information"+family_index+",#state_table_family_information"+family_index+"").trigger('change');
	}
	family_index = '';// Reset family index
}

function setPolicyState(result){
	if(result){
		var val=result.split("-");
		document.getElementById("City").value=trim(val[0]);
		document.getElementById("State").value=trim(val[1]);
		$("#City,#State").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#Zip,#City,#State").val('');
		$("#Zip").trigger('change').focus();
		$("#Zip,#City,#State").trigger('change');
	}
}

function zipstateChangedAdd_groups(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city").value=trim(val[0]);
		document.getElementById("state").value=trim(val[1]);
		$("#city,#state").trigger('change');
        if(document.getElementById("state").value=='TX') {
            if($('#THCICSubmitterId_col').length>0) {
                $('#THCICSubmitterId').val('');
                $('#THCICSubmitterId_col').show();
            }
        } else {
            if($('#THCICSubmitterId_col').length>0) {
                $('#THCICSubmitterId').val('');
                $('#THCICSubmitterId_col').hide();
            }
        }
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code,#city,#state").val('');
		$("#code").trigger('blur').focus();
		$("#city,#state").trigger('change');
	}
}

function zipstateChangedAdd_groups_rem(result){
	if(result){
		var val=result.split("-");
		document.getElementById("rem_city").value=trim(val[0]);
		document.getElementById("rem_state").value=trim(val[1]);
		d$("#rem_city,#rem_state").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#rem_zip,#rem_city,#rem_state").val('');
		$("#rem_zip").trigger('blur').focus();
		$("#rem_city,#rem_state").trigger('change');
	}
}

function zipstateChangedRef(result){
	if(result){
		var val=result.split("-");
		document.getElementById("rcity").value=trim(val[0]);
		document.getElementById("rstate").value=trim(val[1]);
		$("#rcity,#rstate").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#rcode,#rcity,#rstate").val('');
		$("#rcode").trigger('change').focus();
		$("#rcode,#rcity,#rstate").trigger('change');
	}
}

function zipstateChangedPosFacility(result){
	if(result){
		var val=result.split("-");
		document.getElementById("pos_facility_city").value=trim(val[0]);
		document.getElementById("pos_facility_state").value=trim(val[1]);
		$("#pos_facility_city,#pos_facility_state").trigger('change');
        if(document.getElementById("pos_facility_state").value=='TX') {
            if($('#THCICID_col').length>0) {
                $('#thcic_id').val('');
                $('#THCICID_col').show();
            }
        } else {
            if($('#THCICID_col').length>0) {
                $('#thcic_id').val('');
                $('#THCICID_col').hide();
            }
        }
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#pos_facility_zip,#pos_facility_city,#pos_facility_state").val('');
		$("#pos_facility_zip").trigger('change').focus();
		$("#pos_facility_zip,#pos_facility_city,#pos_facility_state").trigger('change');
	}	
}

function zipstateChangedIns(result){
	if(result){
		var val=result.split("-");
		document.getElementById("City").value=trim(val[0]);
		document.getElementById("State").value=trim(val[1]);
		$("#City,#State").trigger('change');
	}else{
		//window.open('../../common/addZipCode.php?code='+newzip_code,'mywindow','width=800,height=100');				
	}
}

function zipstateresp_party(result){
	if(result){
		var val=result.split("-");
		document.getElementById("rcity").value=trim(val[0]);
		document.getElementById("rstate").value=trim(val[1]);
		$("#rcity,#rstate").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#rcode,#rcity,#rstate").val('');
		$("#rcode").trigger('change').focus();
		$("#rcode,#rcity,#rstate").trigger('change');
		
	}
}

function zipstateoccupation(result){
	if(result){
		var val=result.split("-");
		document.getElementById("ecity").value=trim(val[0]);
		document.getElementById("estate").value=trim(val[1]);
		$("#ecity,#estate").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#ecode,#ecity,#estate").val('');
		$("#ecode").trigger('change').focus();
		$("#ecode,#ecity,#estate").trigger('change');
	}
}

function zipstate_primary(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city1").value=trim(val[0]);
		document.getElementById("state1").value=trim(val[1]);
		$("#city1,#state1").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code1,#city1,#state1").val('');
		$("#code1").trigger('change').focus();
		$("#code1,#city1,#state1").trigger('change');
	}
}

function zipstate_secondary(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city2").value=trim(val[0]);
		document.getElementById("state2").value=trim(val[1]);
		$("#city2,#state2").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code2,#city2,#state2").val('');
		$("#code2").trigger('change').focus();
		$("#code2,#city2,#state2").trigger('change');
	}
}

function zipstate_tertiary(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city3").value=trim(val[0]);
		document.getElementById("state3").value=trim(val[1]);
		$("#city3,#state3").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code3,#city3,#state3").val('');
		$("#code3").trigger('change').focus();
		$("#code3,#city3,#state3").trigger('change');
	}
}

function zipstateChanged(result){ 
	if(result){
		var val=result.split("-");
		document.getElementById("city").value=trim(val[0]);
		document.getElementById("state").value=trim(val[1]);
		$("#city,#state").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code").trigger('change');
		$("#city,#state").val('').trigger('change');
		$("#code").trigger('change');
	}
} 

//end here

/*
* Function : call_functions 
* Purpose - Call On click of save button
* Params -
*	val :	Holds Sub Tab name
*/
function call_functions(val)
{
		switch(val)
		{
			case 'demographic_save':
				//required fields
				var reqd_fields = top.fmain.validate_reqd_fields();
				
				if(reqd_fields != true){
					var arr_func = [];
					arr_func[0] = "return false";
					arr_func[1] = "";
					var alertType = "alert";
					if(reqd_fields == '5,') {alertType = "confirm";	arr_func[0] = "top.fmain.call_functions(\'demographic_save_proceed\')";}
					top.fmain.pi_show_alert(alertType, reqd_fields, arr_msg, arr_focus, arr_func);
					return false;
				}
				
				//validate filled fields
				top.fmain.validate_filled_fields();			
			break;
			
			case 'demographic_save_proceed':
			 	//validate filled fields 
				top.fmain.validate_filled_fields();			
			break;
			
			case 'insurance_save':
				//top.resetHidVal();
				var result = top.fmain.showPracMendAlertInsurence();
			break;
		}
}

// Demographics Save && Validation Functions	
function validate_reqd_fields()
{	
	var msg_code_str = "";
	if(myvar.document.getElementById("fname").value == "" || myvar.document.getElementById("lname").value == ""){
		msg_code_str += "0,";
	}
	
	//responsible party
	var patient_age = parseInt(myvar.document.getElementById("patient_age").innerHTML);
	if(patient_age != 0 && patient_age < 18 && myvar.document.getElementById("fname1").value == ""){
		msg_code_str += "5,";
	}
    
	//family info
	var ans = myvar.validateFamilyInfo();
	if(ans == false){
		msg_code_str += "10,";
	}
		
	if(msg_code_str != ""){
		return msg_code_str;
	}
	return true;
}

function pi_show_alert(mode, msg_str, arr_msg, arr_focus, arr_func, response_mode, height_adjustment, optional_val)
{	
	
	var msg_to_show = "Please fill the following fields correctly:<br><br>";
	var set_focus_to = "";
	var focus_set = false;

	if(mode == "mandatory"){
		msg_to_show = "Following fields are mandatory:<br><br>";
	}
	if(mode == "confirm"){
		msg_to_show = "You have not filled the following fields:<br><br>";
	}
	
	var default_width = 375;
	if(response_mode == "multi"){
		msg_to_show = "Please confirm the following actions:<br><br>";
		default_width = 675;
	}
	
	if(msg_str.substring(0,2) == "!!"){
		msg_to_show += msg_str.substring(2);
	}else{
		var arr_show_msg = msg_str.split(",");
		for(i = 0; i < arr_show_msg.length-1; i++){
			if(focus_set == false){
				if(arr_focus[arr_show_msg[i]] != ""){
					set_focus_to = arr_focus[arr_show_msg[i]];
					focus_set = true;
				}				
			}
			
			if(response_mode == "multi"){
				msg_to_show += "<div class=\"col-sm-12\">"+arr_msg[arr_show_msg[i]] + "</div><div class=\"col-sm-2\"><div class=\"radio\"><input type=\"radio\" name=\"r"+arr_show_msg[i]+"\" id=\"r"+arr_show_msg[i]+"_yes\" checked onclick=\"window.top.fmain.set_after_save_actions('"+arr_show_msg[i]+"', '1');\" /><label for=\"r"+arr_show_msg[i]+"_yes\">Yes</label></div></div><div class=\"col-sm-2\"><div class=\"radio\"><input type=\"radio\" name=\"r"+arr_show_msg[i]+"\" id=\"r"+arr_show_msg[i]+"_no\" onclick=\"window.top.fmain.set_after_save_actions('"+arr_show_msg[i]+"', 0);\" /><label for=\"r"+arr_show_msg[i]+"_no\">No</label></div></div><div class=\"clearfix\"></div>";
			}else{
				msg_to_show += arr_msg[arr_show_msg[i]] + "<br>";
				if(set_focus_to=='ss'){
					msg_to_show += optional_val+'<br><br>';
				}
			}
		}
	}
	
	if(mode == "mandatory"){
		if(set_focus_to != ""){
			window.top.fAlert(msg_to_show);
		}else{
			window.top.fAlert(msg_to_show);
		}
	}
	else if(mode == "confirm"){
		msg_to_show += "<br>Do you wish to continue?";
		if(set_focus_to != ""){
			window.top.fancyConfirm(msg_to_show, "", arr_func[0], "window.top.fmain.document.getElementById('"+set_focus_to+"').focus(); "+arr_func[1]);
		}else{
			window.top.fancyConfirm(msg_to_show, "", arr_func[0], arr_func[1]);
		}
	}
	else{
		if(response_mode == ""){
			msg_to_show += "<br>Click OK Button to continue saving.";
		}
		if(set_focus_to != ""){
			if( set_focus_to == 'ss') {
				msg_to_show = '<div style="max-height:500px; overflow:auto;">'+msg_to_show+'</div>';
			}
			window.top.fAlert(msg_to_show, "", "window.top.fmain.document.getElementById('"+set_focus_to+"').focus();");//+arr_func[0]
		}else{
			window.top.fAlert(msg_to_show, "", arr_func[0],700);
		}
	}
}

function validate_filled_fields(resp_cred)
{
    resp_cred = resp_cred || '';

	if(isERPPortalEnabled){
		if( (myvar.document.getElementById("fname1").value != "" || myvar.document.getElementById("lname1").value != "") && resp_cred=='' ){
			var msg='';
			var set_focus_to='erp_resp_username';
			if(document.getElementById("erp_resp_username").value == ""){
				msg=" - Without Username and password the Representative account cannot be created on Patient Portal. ";
				set_focus_to="erp_resp_username";
			}else if(document.getElementById("erp_resp_passwd").value == ""){
				msg=" - Without Username and password the Representative account cannot be created on Patient Portal. ";
				set_focus_to="erp_resp_passwd";
			}
			if(document.getElementById("erp_hidd_passwd").value == ""){
				var new_pass = document.getElementById("erp_resp_passwd").value;
				var confirm_pass = document.getElementById("erp_resp_cpasswd").value;
				if(new_pass != confirm_pass){
					if(confirm_pass==''){
						msg=" - Confirm Representative Password is required. ";
						set_focus_to="erp_resp_cpasswd";
					} else {
						msg=" - Confirm Password does not matches with Password. ";
						set_focus_to="erp_resp_cpasswd";
					}	
				}
			}

			var arr_func = [];
			arr_func[0] = "return false";
			arr_func[1] = "";
			
			if(msg!='') {
				window.top.fancyConfirm(msg, "",  "top.fmain.validate_filled_fields(\'1\')",  "window.top.fmain.document.getElementById('"+set_focus_to+"').focus(); "+arr_func[1]);
				return false;
			}
		}
    }
    
	var userName, ssnNumber;
	userName = myvar.demographics_edit_form.usernm.value;
	ssnNumber = myvar.demographics_edit_form.ss.value;
	resSsnNumber = myvar.demographics_edit_form.ss1.value;
	
	var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/ajax_validation.php?action=validate_form";
	url = url + "&userName=" + userName + "&ssnNumber=" + ssnNumber + "&resSsnNumber=" + resSsnNumber;
	
	var ptStatusObj = $("#elem_patientStatus");
	var pt_status = ptStatusObj.val();
	var prev_pt_status = ptStatusObj.data('prev-val');
	if( prev_pt_status != 'Deceased' && pt_status == 'Deceased' ) {
		msg = "Patient status changed to deceased.<br>All future appointments will be canceled.";
		//window.top.fancyConfirm(msg, "", "top.fmain.onChangePtStatus('"+url+"');", false);
		window.top.fAlert(msg,'',"top.fmain.onChangePtStatus('"+url+"');",'','','Ok',true);
	}
	else {
		top.master_ajax_tunnel(url,top.fmain.xhr_ajax,'','json');
	}
	
}

function onChangePtStatus(url){

	if( typeof url == 'undefined') return false;

	top.master_ajax_tunnel(url,top.fmain.xhr_ajax,'','json');

}
function validateFamilyInfo()
{ 
	for(var a=0; a<25; a++)
	{
		if(document.getElementById("family_information_relatives"+a))
		{
			if((document.getElementById("family_information_relatives"+a).value != "") && (document.getElementById("fname_table_family_information"+a).value == "" || document.getElementById("lname_table_family_information"+a).value == ""))
			{
				if(document.getElementById("fname_table_family_information"+a).value == "")
				{
					familyMemberTextBoxFName = "fname_table_family_information"+a;
				}
				else if(document.getElementById("lname_table_family_information"+a).value == "")
				{
					familyMemberTextBoxLName = "lname_table_family_information"+a;	
				}
				return false;
			}
		}
	}
	return true;
}

function validate_ssn_format(ssn)
{
	var not_valid = false;
	not_valid = validate_ssn(ssn);
	if(not_valid != false){
		myvar.$("#ss").trigger('change');
	}
	return not_valid;
}

function showPracMendAlert()
{ 
 	var msg = "";
	var alertActive = false;
	$.each(mandatory,function(i,v){
		if(typeof i === 'string') {
			var obj = $("#"+i);
			if(obj.length)
			{
				var t_msg = typeof(vocabulary[i]) == 'undefined' ? '' : vocabulary[i];
				t_msg = t_msg.replace(/\\n/g,''); 	
				if(obj.val() === '' && v == '1' ) {
					if(i == "race" || i == "ethnicity" || i == "language")
						msg = msg + '<span class="red-txt">'+t_msg+'</span><br>';
					else
						msg = msg + t_msg +'<br>';
					alertActive = true;
				}
			}
		}
	});
	
	if(alertActive == true){
		return "!!"+msg;
	}
	return true;
}

function getMandatoryMsg()
{ 
	var msg = "";
	var alertActive = false;
	$.each(mandatory,function(i,v){
		if(typeof i === 'string') {
			var obj = $("#" + i);
			if(obj.length) {
				var t_msg = typeof(vocabulary[i]) == 'undefined' ? '' : vocabulary[i];
				t_msg = t_msg.replace(/\\n/g,'');	
				if((obj.val() === '' || obj.val() == null) && v == '2' ) {
					msg = msg + t_msg + '<br>';
					alertActive = true;
				}
			}
		}
	});
			
	if(alertActive == true){
		return "!!"+msg;
	}
	return true;
}

function ask_for_after_save_actions(str_msg, str_focus)
{
	var msg_code_str = "";
	var str_msg = unescape(str_msg);
	var str_focus = unescape(str_focus);
	
	var arr_new_msg = (str_msg) ? str_msg.split("__") : arr_msg;
	var arr_new_focus = (str_focus) ? str_focus.split("__") : arr_focus;
	
	//zip code does not exits
	var zipCodeStatus = myvar.document.getElementById("zipCodeStatus");
	var zipCode = myvar.document.getElementById("postal_code");				
	if(zipCodeStatus.value == 'NA'){
		msg_code_str += "7,";
		myvar.document.getElementById("zipCodeStatus").value = "NA";
	}
	
	//erx registration
	var erx_entry = myvar.demographics_edit_form.erx_entry.value;
	var Allow_erx_medicare = myvar.demographics_edit_form.Allow_erx_medicare.value;
	if(Allow_erx_medicare == 'Yes' && erx_entry == 0){
		msg_code_str += "8,";
		myvar.demographics_edit_form.erx_entry.value = 1;
		myvar.demographics_edit_form.chkErxAsk.value = 1;
	}
	
	//new a/c for resp party
	if( myvar.document.getElementById("hid_resp_party_sel_our_sys")){
		if((myvar.document.getElementById("hid_resp_party_sel_our_sys").value == "no" || myvar.document.getElementById("hid_resp_party_sel_our_sys").value == "") && myvar.document.getElementById('fname1').value != "" && myvar.document.getElementById('lname1').value != ""){																										
			msg_code_str += "9,";
			myvar.document.getElementById("hid_create_acc_resp_party").value = "yes";
		}
	}
	
	if(msg_code_str != ""){
		var arr_func = new Array();
		arr_func[0] = "window.top.fmain.process_save();";
		arr_func[1] = "";
		window.top.fmain.pi_show_alert("alert", msg_code_str, arr_new_msg, arr_new_focus, arr_func, "multi");
		return false;
	}
	window.top.fmain.process_save();
}

function set_after_save_actions(msg_index, val)
{
	if(msg_index == "7"){
		if(val == "1") { val = "NA"; } else { val = "NotOk"; }		
		if(typeof(window.top.int_country) != "undefined" && window.top.int_country == "UK")val = "OK";
		myvar.document.getElementById("zipCodeStatus").value = val;
	}
	if(msg_index == "8"){
		if(val == "1") { val = "1"; } else { val = "0"; }		
		myvar.demographics_edit_form.erx_entry.value = val;
		myvar.demographics_edit_form.chkErxAsk.value = val;
	}
	if(msg_index == "9"){
		if(val == "1") { val = "yes"; } else { val = "no"; }		
		myvar.document.getElementById("hid_create_acc_resp_party").value = val;
	}
}

//to submit form
function process_save(){
	window.top.fmain.demographics_edit_form.submit();
	window.top.show_loading_image("show", "150", "");
}

//Opens Public Health Syndromic Surveillance Data window
function get_phssi_data(){
	var parWidth = parseInt($(window).width() - 500);
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/patient_info/common/pt_phss_info.php','ptInfoPHSSDHL7Export','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width='+parWidth+',height=600,left=10,top=100');
}

//Opens merge patient window
function showMergePatients(){		
	var parWidth = parseInt($(window).width() - 500);		
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/patient_info/common/merge_patient.php','MergePatients','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+'px,height=800px,left=10,top=80');
}

//Opens Medical History Tab Scan Upload And Print actions
function showPtProviders(){		
	var parWidth = parent.document.body.clientWidth;	
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/pt_providers/index.php','patientProvider','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=600,left=10,top=80');
}

//Opens complete patient record print window
function openPrint(){
	var parWidth = parent.document.body.clientWidth;		
	top.popup_win(top.JS_WEB_ROOT_PATH+"/interface/patient_info/common/print_function.php?call_from=demo",'imedic_print','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width='+parWidth+',height=680');
}

// End Demographics Save && Validation Functions

function zipstateChangedPosFacilityGroup(result){
	if(result){
		var val=result.split("-");
		document.getElementById("fac_group_city").value=trim(val[0]);
		document.getElementById("fac_group_state").value=trim(val[1]);
		$("#fac_group_city,#fac_group_state").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#fac_group_zip,#fac_group_city,#fac_group_state").val('');
		$("#fac_group_zip").trigger('change').focus();
		$("#fac_group_zip,#fac_group_city,#fac_group_state").trigger('change');
	}	
}
// Demographics JS File
// Code Written By : Gurpreet Singh

var arr_msg = [];
arr_msg[0] 	= vocabulary.first_last_name;
arr_msg[1] 	= vocabulary.pass_confirm;
arr_msg[2] 	= vocabulary.invalid_ssn;
arr_msg[3] 	= vocabulary.unique_ssn;
arr_msg[4] 	= vocabulary.login_already_exist;
arr_msg[5] 	= vocabulary.patient_not_adult;
arr_msg[6] 	= vocabulary.format_ref_phy;
arr_msg[7] 	= vocabulary.zip_not_exist;
arr_msg[8] 	= vocabulary.pt_not_reg_erx;
arr_msg[9] 	= vocabulary.create_acc_grantor;
arr_msg[10] = vocabulary.family_member_not_enter;
arr_msg[11] = vocabulary.invalid_resp_party_ssn;
arr_msg[12] = vocabulary.format_primary_care_phy;

var arr_focus = [];
arr_focus[0] = 'fname';
arr_focus[1] = 'pass2';
arr_focus[2] = 'ss';
arr_focus[3] = 'ss';
arr_focus[4] = 'usernm';
arr_focus[5] = 'fname1';
arr_focus[6] = 'elem_physicianName';
arr_focus[7] = '';
arr_focus[8] = '';
arr_focus[9] = '';
arr_focus[10] = '';
for(var b=0; b<25; b++){
	if(myvar.document.getElementById("fname_table_family_information"+b)) {
		if(myvar.document.getElementById("fname_table_family_information"+b).value == ""){
			arr_focus[10] = "fname_table_family_information"+b;
			break;
		}else if(myvar.document.getElementById("lname_table_family_information"+b).value == ""){								
			arr_focus[10] = "lname_table_family_information"+b;
			break;	
		}
	}
}
arr_focus[11] = 'ss1';

/* 
* Function add_new_address
* Purpose - To add Multiple Address Grids in All Communication grid
* Params = callFrom 
*/
function add_new_address(callFrom)
{
	callFrom = callFrom || '';
	i = $('input[name^="street["]').length;
	html	='';
	html += '<div class="col-xs-12 pt-box"><div class="row grid-box" tabindex="0">';
	html += '<div id="div_address'+i+'">';
	// Header 
	html	+=	'<div class="">';
	html	+=	'<div class="col-sm-12">';
	html	+=	'<h2 class="head"> ';
	html	+=	'<div class="radio radio-inline">';
	html	+=	'<input type="radio" name="all_communication" id="all_communication'+i+'" autocomplete="off" value="'+i+'" />';
	html	+=	'<label for="all_communication'+i+'" >All Communication</label>';
	html	+=	'</div>';
	html	+=	'<span id="address_close'+i+'" title="Delete Address" onClick="$(\'#div_address'+i+'\').remove();" class="pull-right margin-top-20 pointer"><i class="glyphicon glyphicon-remove"></i></span>';
	html	+=	'</h2>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	
	html	+=	'<div class="col-xs-12 ">';
	// Street 1
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="street_'+i+'">Street 1</label>';
	html	+=	'<input name="street['+i+']" id="street_'+i+'" type="text" class="form-control" value="" />';
	html	+=	'</div>';
	
	// Street 2
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="street2_'+i+'">Street 2</label>';
	html	+=	'<input name="street2['+i+']" id="street2_'+i+'" type="text" class="form-control" value="" />';
	html	+=	'</div>';
	
	// Zip Code
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="code'+i+'">'+top.zipLabel+'</label>';
	
	html	+=	'<div class="row">';
	html	+=	'<div class="col-xs-'+(top.zip_ext ? '6' : '12')+'">';
	html	+=	'<input name="postal_code['+i+']" type="text" class="form-control" id="code'+i+'" onBlur="zip_vs_state_R6(this,document.getElementsByName(\'city['+i+']\'),document.getElementsByName(\'state['+i+']\'),document.getElementsByName(\'country_code['+i+']\'),document.getElementsByName(\'county['+i+']\'));" value="" maxlength="'+top.zip_length+'" />';
	html	+=	'</div>';
	
	if(top.zip_ext)
	{
		html	+=	'<div class="col-xs-6">';
		html	+=	'<input name="zip_ext['+i+']" type="text" id="zip_ext_'+i+'" value="" class="form-control" maxlength="4">';
		html	+=	'</div>';
	}
	html	+=	'</div>';
	html	+=	'</div>';
	
	//City
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="city_'+i+'">City</label><br>';
	html += '<input name="city['+i+']" type="text" id="city_'+i+'" value="" class="form-control" >';
	html	+=	'</div>';
	
	//State
	var StateLabel = top.state_label;
	StateLabel = StateLabel.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    return letter.toUpperCase();
	});
											
	html	+=	'<div class="col-xs-2">';
	html	+=	'<label for="state_'+i+'">'+StateLabel+'</label><br>';
	html += '<input name="state['+i+']" type="text" maxlength="'+top.state_length+'" id="state_'+i+'" value="" class="form-control" />';
	html	+=	'</div>';
	
	//County
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="county_'+i+'">County</label><br>';
	html	+=	'<input name="county['+i+']" type="text" class="form-control" id="county_'+i+'" value="" />';
	html	+=	'</div>';
	
	
	//Country
	html	+=	'<div class="col-xs-4">';
	html	+=	'<label for="country_code_'+i+'">Country</label><br>';
	html	+=	'<input name="country_code['+i+']" type="text" class="form-control" id="country_code_'+i+'" value="'+top.int_country+'" >';
	html	+=	'</div>';
	
	html	+=	'<div class="clearfix mb5"></div>';
	
	html	+=	'</div>';		
	html	+=	'</div></div>';
	var cont = $('#address_grid');
	cont.append(html);
	//cont.animate({ scrollTop: cont.prop("scrollHeight")}, 1000);
}

/* 
* Function del_address
* Purpose- To Delete address grid in ALL Communication Section
* Params - 
*/
function del_address(cnt,add_id,r)
{
	if(typeof r === 'undefined'){
		top.fancyConfirm("Are you sure, you want to delete ?", '','top.fmain.del_address('+cnt+','+add_id+','+true+')',false);
		return false;	
	} 
	
	else
	{
		jId = "#div_address"+cnt;
		$(jId).remove();
		ids = $("#address_del_id").val();
		$("#address_del_id").val(ids+","+add_id);
	}
	
	
	
}

/*
* Function : add_family_info_row
* Purpose - Add New Family Info Grid 
* Params -
* 	grid_id - Last Inserted Grid ID 
* 	rows : total count 
* 	delete_msg : Confirmation Message before deletion 
* 	state_label : Label for state fields
*/

function add_family_info_row(grid_id,rows,delete_msg,state_label)
{
		var pre_cnt = rows;
		state_label = state_label || 'State';
		$("#imgRowTd"+pre_cnt).html('<br><span id="imgDeleteRow'+pre_cnt+'" class="pull-right pointer" title="Delete Family Information" onClick="delete_family_info(\''+grid_id+'\',\''+pre_cnt+'\',\''+delete_msg+'\');"><i class="glyphicon glyphicon-remove"></i></span>');
		
		rows++;
		var altClass= (rows%2 === 0) ? ' alternate' : '';
		
		var html = '';
		html += '<div id="table_family_information_' + rows + '" class="family-grid margin-top-10 pt-box '+altClass+' "  >';
		html += '<div id="family_info_name_table_' + rows + '" class="grid-box" tabindex="0">';
		// Row 1 -->
		html += '<div class="col-xs-12 ">';
		
		// Relative -->
    html += '<div class="col-xs-3" >';
		html += '<label>Relative</label>';
		html += '<br>';
		html += '<select class="form-control minimal" data-width="100%" name="family_information_relatives' + rows + '" id="family_information_relatives' + rows + '" data-tab-num="' + rows + '" title="Relative" data-header="Relative" data-container="#familySelectContainer">';
		var arrFamily = Array("","Brother","Daughter","Father","Mother","Sister","Son","Spouse","Other");
		for(var i=0; i < arrFamily.length;i++){
			opval = arrFamily[i];
			html += '<option value="'+opval+'" '+(opval ? '' : 'selected')+'>'+opval+' </option>';
		}
		html += '</select>';
		
		html 	+= '<div id="family_rel_other_box_'+rows+'" class="hidden">';
		html 	+= '<div class="input-group ">';
		html 	+= '<input type="text" class="form-control" id="family_information_relatives_other_txt'+rows+'" name="family_information_relatives_other_txt' + rows + '" value="" >';
		html 	+= '<label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackFamilyInformation' + rows + '" data-tab-name="family_information_relatives" data-tab-num="'+rows+'">';
		html 	+= '<span class="glyphicon glyphicon-arrow-left"></span>';
		html 	+= '</label>';
		html 	+= '</div>';
		html 	+= '</div>';
		
		html += '</div>';
		
		/* Title */
		html += '<div class="col-xs-3"	>';
		html += '<label>Title</label>';
		html += '<br>';
		html += '<select name="title_table_family_information' + rows + '" id="title_table_family_information' + rows + '" class="form-control minimal" data-width="100%" data-header="Title" title="Title">';
		html += '<option value="" selected> </option>';
		html += '<option value="Mr." >Mr.</option>';
		html += '<option value="Mrs.">Mrs.</option>';
		html += '<option value="Ms.">Ms.</option>';
		html += '<option value="Miss">Miss</option>';
		html += '<option value="Master">Master</option>';
		html += '<option value="Prof.">Prof.</option>';
		html += '<option value="Dr.">Dr.</option>';
		html += '</select>';
		html += '</div>';
		
		html += '<div class="col-xs-6" id="imgRowTd' + rows + '">';
		html += '</div>';
											
		html += '</div>';
		
    //Row 2
		
    html += '<div class="col-xs-12 ">';
		
		// First Name 
		html += '<div class="col-xs-4 "	>';
		html += '<label>First Name</label>';
		html += '<br>';
		html += '<input type="text" name="fname_table_family_information' + rows + '" id="fname_table_family_information' + rows + '" class="form-control" value="" />';
		html += '</div>';
		
		// Middle Name 
		html += '<div class="col-xs-4"	>';
		html += '<label>Middle Name</label>';
		html += '<br>';
		html += '<input type="text" name="mname_table_family_information' + rows + '" id="mname_table_family_information' + rows + '" class="form-control" value="" />';
		html += '</div>';
                      
    // Last Name 
		html += '<div class="col-xs-4"	>';
		html += '<label>Last Name</label>';
		html += '<br>';
		html += '<input type="text" name="lname_table_family_information' + rows + '" id="lname_table_family_information' + rows + '" class="form-control" value="" data-action="search_patient" data-grid="' + rows + '" data-fld="Active"  />';
		html += '</div>';
		
		html += '</div>';
			
    // Row 3  
		html += '<div class="col-xs-12 ">';
		
    // Suffix 
		html += '<div class="col-xs-2 "	>';
		html += '<label>Suffix</label>';
		html += '<br>';
		html += '<input type="text" name="suffix_table_family_information' + rows + '" id="suffix_table_family_information' + rows + '" class="form-control" value="" />';
		html += '</div>';
		
		// Relaese HIPAA Info 
		html += '<div class="col-xs-5 form-inline"	>';
		html += '<br>';
		html += '<div class="checkbox">';
		html += '<input type="checkbox" class="form-control" id="chkHippaFamilyInformation_' + rows + '" name="chkHippaFamilyInformation_' + rows + '" value="1" >';
		html += '<label for="chkHippaFamilyInformation_'+ rows +'"><span class="text-red">Relase HIPAA Info</span></label>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		
		// Row 4 
		html += '<div class="col-xs-12 ">';
		
		// Street1 
		html += '<div class="col-xs-5"	>';
		html += '<label>Street 1</label>';
		html += '<br>';
		html += '<input name="street1_table_family_information' + rows + '" id="street1_table_family_information' + rows + '"  type="text" class="form-control" value="" />';
		html += '</div>';
		
		// Street2 
		html += '<div class="col-xs-4"	>';
		html += '<label>Street 2</label>';
		html += '<br>';
		html += '<input name="street2_table_family_information' + rows + '" id="street2_table_family_information' + rows + '" type="text" class="form-control" value="" />';
		html += '</div>';
		
		var col 	= (top.zip_ext)	?	7 : 12;
		var col2	=	(top.zip_ext)	?	4 : 0;	
		
		// Zip Code 
		html += '<div class="col-xs-3"	>';
		html += '<label>'+top.zipLabel+'</label>';
		html += '<br>';
		html += '<div class="col-xs-12">';
		html += '<div class="row">';
		
		html += '<div class="col-xs-'+col+'" >';
		
		html += '<input name="postal_code_table_family_information' + rows + '" type="text" class="form-control" id="code_table_family_information' + rows + '" onChange="zip_vs_state_family_state(this.value,\'' + rows + '\'); " value=""  maxlength="'+top.zip_length+'" size="'+top.zip_length+'" >';
		html += '</div>';
		
		if(top.zip_ext)
		{
     	html += '<div class="col-xs-1 text-center padding_0"><b>-</b></div>';
			html += '<div class="col-xs-'+col2+'">';
			html += '<input name="zip_ext_table_family_information' + rows + '" type="text" class="form-control" id="zip_ext_table_family_information' + rows + '" value="" maxlength="4">';
			html += '</div>';
		}
		
		html += '</div>';
		
		html += '</div>';
		
		html += '</div>';
		
		html += '</div>';
		
    // Row 5 
    html += '<div class="col-xs-12 ">';
		
		// City 
		html += '<div class="col-xs-3"	>';
		html += '<label>City</label>';
		html += '<br>';
		html += '<input name="city_table_family_information' + rows + '" type="text" class="form-control" id="city_table_family_information' + rows + '" value="" />';
		html += '</div>';
		
		// State 
		html += '<div class="col-xs-2"	>';
		html += '<label>'+state_label+'</label>';
		html += '<br>';
		html += '<input name="state_table_family_information' + rows + '" type="text" maxlength="2" class="form-control" id="state_table_family_information' + rows + '" value="" />';
		html += '</div>';
		
		// Email ID 
		html += '<div class="col-xs-7"	>';
		html += '<label>Email-Id</label>';
		html += '<br>';
		html += '<input name="email_table_family_information' + rows + '" id="email_table_family_information' + rows + '" type="text" class="form-control" value="">';
		html += '</div>';
		
		html += '</div>';
		
		
    // Row 6 
    html += '<div class="col-xs-12 ">';
		
		// Home Phone 
		html += '<div class="col-xs-4" >';
		html += '<label>Home Phone '+top.hashOrNo+'</label>';
		html += '<br>';
		html += '<input name="phone_home_table_family_information' + rows + '" id="phone_home_table_family_information' + rows + '" type="text" class="form-control" maxlength="'+top.phone_min_length+'" value="" />';
		html += '</div>';
		
		
    // Work Phone 
		html += '<div class="col-xs-4" >';
		html += '<label>Work Phone '+top.hashOrNo+'</label>';
		html += '<br>';
		html += '<input name="phone_work_table_family_information' + rows + '" id="phone_work_table_family_information' + rows + '" type="text" class="form-control" maxlength="'+top.phone_min_length+'" value="" />';
		html += '</div>';
		
		// Mobile Phone 
		
		html += '<div class="col-xs-4" >';
		html += '<label>Mobile Phone '+top.hashOrNo+'</label>';
		html += '<br>';
		html += '<input name="phone_cell_table_family_information' + rows + '" id="phone_cell_table_family_information' + rows + '" type="text"  class="form-control" maxlength="'+top.phone_min_length+'" value="" />';
		html += '</div>';
		
                    
    html += '</div>';
		
          		
   	html += '</div>';
		html += '</div>';
		
		// End -->
		
		// Add More Button 
		var AddMoreButton	=	'';
		AddMoreButton	=	'<span id="imgAddNewRow'+rows+'" title="Add More" onClick="add_family_info_row(\'\',\''+rows+'\',\''+delete_msg+'\',\''+state_label+'\');" class="pull-right pointer"><i class="glyphicon glyphicon-plus"></i></span>';
		
		var tbl = $("#patient_family_table");
		tbl.last().append(html);
		dgi("last_family_inf_cnt").value = rows;
		//$("select.selectpicker").selectpicker('refresh');
		$("#ImageAddRow").html(AddMoreButton);
		tbl.animate({ scrollTop: tbl.prop("scrollHeight")}, 1000);
		/*--updating jquery table highliting for newly generated data--*/
		/*$('.div_table').click(function(){
			$('.div_table').removeClass('bg3');
			$(this).addClass('bg3');
		});*/
	}

/*
* Function : delete_family_info
* Purpose - To Delete Family Info Grid
* Params - 
* 	family_info_id : Id With which information saved in DB
* 	row_id : Grid Index Generated while create new
* 	confirmMsg : Message text Before Deletion
*/
function delete_family_info(family_info_id,row_id,confirmMsg)
{
		if(family_info_id){
			var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/delete_family_info.php?id="+family_info_id;
			top.fancyConfirm(confirmMsg,"", "window.top.show_loading_image('show');master_ajax_tunnel('"+url+"',top.fmain.handle_ajax_response,'','json');","window.top.show_loading_image('hide');")
		}
		else{
			$("#table_family_information_"+row_id).fadeOut('fast');
		}
}

/*
* Function : handle_ajax_response
* Purpose - To handle response send by Ajax tunnel method
* 					Called From delete_family_info ^| METHOD			
* Params - r : holds JSON ENCODED information
*/

function handle_ajax_response(r)
{
	if(r.success == true)
	{
		var m = vocabulary[r.msg_key]; 
		top.fmain.location.reload(true);
		top.alert_notification_show(m);
	}
	return;
}

/*
* Function : set_release_information
* Purpose - To Set Name, Phone and Relationship info in 
*						Release Grid While checked Release Info	Checkbox 
*						in Responsible Part and Family Information Grid	
* Params - num : holds row no to set values in input/select
*/

function set_release_information(num)
{
	/*
		when you checked the "Release Hipaa information "checkbox"in Family Information then name, home phone and relation of patient
		goes to the "Reminder Choices"
	*/
	
	var name_id_array = new Array("relInfoName1", "relInfoName2", "relInfoName3", "relInfoName4");
	var phone_id_array = new Array("relInfoPhone1", "relInfoPhone2", "relInfoPhone3", "relInfoPhone4");
	var rel_id_array = new Array("relInfoReletion1", "relInfoReletion2", "relInfoReletion3", "relInfoReletion4");
	var rel_other_id_array = new Array("otherRelInfoReletion1", "otherRelInfoReletion2", "otherRelInfoReletion3", "otherRelInfoReletion4");
		
	if(num == "resp"){
		if(document.getElementById("chkHippaRelResp").checked == true){
			var family_fname_object = document.getElementById("fname1");
			var family_lname_object = document.getElementById("lname1");
			var family_phone_object = document.getElementById("phone_home1");
			var family_relation_ship_object = document.getElementById("relation1");
			var family_relation_ship_other_object = document.getElementById("oth");
			var name = '';
			if(family_lname_object.value){
				name = family_lname_object.value;			
			}
			if(family_fname_object.value){
				name += ", "+family_fname_object.value;			
			}

			var phone = family_phone_object.value;
			var relationship = family_relation_ship_object.value;
			var relationship_other = family_relation_ship_other_object.value;
			
			if(name !== "" && name !== 'undefined')
			{
				var blHaveInGrid = false;
				for(var a=0;a<4;a++){
					txtName = document.getElementById(name_id_array[a]).value;
					if(txtName.toLowerCase() == name.toLowerCase() ){
						document.getElementById(phone_id_array[a]).value = phone;
						document.getElementById(rel_id_array[a]).value = relationship;
						document.getElementById(rel_other_id_array[a]).value = relationship_other;
						$("#"+rel_id_array[a]).trigger('change');//.selectpicker('refresh');	
						blHaveInGrid = true;	
						a = 4;
						
					}
				}
				if(blHaveInGrid == false)
				{
					for(var a=0;a<4;a++){
						if(document.getElementById(name_id_array[a]).value == ""){
							document.getElementById(name_id_array[a]).value = name;
							document.getElementById(phone_id_array[a]).value = phone;
							document.getElementById(rel_id_array[a]).value = relationship;
							document.getElementById(rel_other_id_array[a]).value = relationship_other;
							$("#"+rel_id_array[a]).trigger('change');//.selectpicker('refresh');	
							
							a = 4;
						}
					}						
				}
				
			}
		}
		else if(document.getElementById("chkHippaRelResp").checked == false){
			var family_fname_object = document.getElementById("fname1");
			var family_lname_object = document.getElementById("lname1");
			var family_phone_object = document.getElementById("phone_home1");
			var family_relation_ship_object = document.getElementById("relation1");
			var family_relation_ship_other_object = document.getElementById("oth");
			
			var name = '';
			if(family_lname_object.value){
				name = family_lname_object.value;			
			}
			if(family_fname_object.value){
				name += ", "+family_fname_object.value;			
			}

			var phone = family_phone_object.value;
			var relationship = family_relation_ship_object.value;
			var relationship_other = family_relation_ship_other_object.value;
			for(var a=0;a<4;a++){
				
				txtName = document.getElementById(name_id_array[a]).value;
				if(txtName.toLowerCase() == name.toLowerCase()){
					document.getElementById(name_id_array[a]).value = '';
					document.getElementById(phone_id_array[a]).value = '';
					document.getElementById(rel_id_array[a]).value = '';				
					document.getElementById(rel_other_id_array[a]).value = '';
					$("#"+rel_id_array[a]).trigger('change');	//.selectpicker('refresh')
					a = 4;
					
				}
			}				
		}		
	
	}
	else{
		num = parseInt(num);
		i = num;
		if(num > 4) { return false; }
		
		if(document.getElementById("chkHippaFamilyInformation_"+num).checked){
			var family_fname_object = document.getElementById("fname_table_family_information"+num);
			var family_lname_object = document.getElementById("lname_table_family_information"+num);
			var family_phone_object = document.getElementById("phone_home_table_family_information"+num);
			var family_relation_ship_object = document.getElementById("family_information_relatives"+num);
			var family_relation_ship_other_object = document.getElementById("family_information_relatives_other_txt"+num);
			var name = '';
			if(family_lname_object.value){
				name = family_lname_object.value;			
			}
			if(family_fname_object.value){
				name += ", "+family_fname_object.value;			
			}

			var phone = family_phone_object.value;
			var relationship = family_relation_ship_object.value;
			var relationship_other = family_relation_ship_other_object.value;
			
			if(name != "" && name != 'undefined')
			{
				var blHaveInGrid = false;
				for(var a=0;a<4;a++){
					if(document.getElementById(name_id_array[a]).value == name && document.getElementById(phone_id_array[a]).value == phone && document.getElementById(rel_id_array[a]).value == relationship){
						blHaveInGrid = true;	
						a = 4;
					}
				}
				if(blHaveInGrid == false){
					document.getElementById(name_id_array[i]).value = name;
					document.getElementById(phone_id_array[i]).value = phone;
					document.getElementById(rel_id_array[i]).value = relationship;				
					document.getElementById(rel_other_id_array[i]).value = relationship_other;
					$("#"+rel_id_array[i]).trigger('change');//.selectpicker('refresh');	
				}
				
			}
		}//end of main if
		else if(!document.getElementById("chkHippaFamilyInformation_"+num).checked){
			document.getElementById(name_id_array[i]).value = '';
			document.getElementById(phone_id_array[i]).value = '';
			document.getElementById(rel_id_array[i]).value = '';				
			document.getElementById(rel_other_id_array[i]).value = '';
			$("#"+rel_id_array[i]).trigger('change');//.selectpicker('refresh');	
		}//end of main else.
	}

}
	
/*
* Function : collect_source
* Purpose - To collect typehead option for selected option
* Params - _key : holds key info to for which typehead to collect
*/	
function collect_source(_key) 
{
	if(_key !== '') {
		type_head_source = suggestions_ha[_key];
	} else { type_head_source = []; }
}

/*
* Function : switch_advisory_class
* Purpose - To set field as advisory if fields is empty
* Params - o: holds this object for which event has occured
*/
function switch_advisory_class(o)
{
	var c = 'advisory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('advisory-chk')) { m.removeClass(c); } else { m.addClass(c);}
}

/*
* Function : switch_advisory_class_s 
* Purpose - To set field as advisory if not selected
*						specialy for select with selectpicker class							
* Params -	o: holds this object for which event has occured
*/
function switch_advisory_class_s(o)
{
	var c = 'advisory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('advisory-chk')) { m.selectpicker('setStyle', c, 'remove'); }
	else { m.selectpicker('setStyle', c, 'add'); }
}

/*
* Function : switch_mandatory_class
* Purpose - To set field as mandatory if fields is empty
* Params - o: holds this object for which event has occured
*/
function switch_mandatory_class(o)
{
	var c = 'mandatory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('mandatory-chk')) { m.removeClass(c); } else { m.addClass(c);}
}

/*
* Function : switch_mandatory_class 
* Purpose - To set field as mandatory if not selected
*						specialy for select with selectpicker class							
* Params -	o: holds this object for which event has occured
*/
function switch_mandatory_class_s(o)
{
	var c = 'mandatory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('mandatory-chk')) { m.selectpicker('setStyle', c, 'remove'); }
	else { m.selectpicker('setStyle', c, 'add'); }
}

/*
* Function : swap_combo_other 
* Purpose - To Change Appearance of fields If Option is selected as 'Others'
* Params -	
* showIDArr - Holds Array of HTML Tags ID which will appear
* hideIDArr - Holds Array of HTML Tags ID which will hidden
* comboObj	- Holds this object of Tag from which event occured
*/
function swap_combo_other(showIDArr,hideIDArr,comboObj)
{
	if((typeof(comboObj) != 'object') || (comboObj.value == 'Other') || (comboObj.value == 'Others')){
		if(hideIDArr){ 
			$(hideIDArr).each(function(){
				if($('#'+this).hasClass('selectpicker')) $('#'+this).selectpicker('hide'); 
				else $('#'+this).removeClass('inline').addClass('hidden'); 	
			});
		} 
		$(showIDArr).each(function(){ 
			if($('#'+this).hasClass('selectpicker')) 
				$('#'+this).val('').selectpicker('show').selectpicker('refresh').trigger('change'); 
			else $('#'+this).removeClass('hidden').addClass('inline'); 
		});
	}
}


/*
* Function : str_exists 
* Purpose - To check if substring exists in given string
* Params -	
* str - Holds string from which search
* search_string - Holds string to search
*/
function str_exists(str,search_string)
{
	return (str.indexOf(search_string) === -1 ) ? false : true;
}

function get_operator_name_date()
{
	$('#chkNotesScheduler, #chkNotesChartNotes, #chkNotesAccounting, #chkNotesOptical').prop('checked',true);
	var t = current_date(top.jquery_date_format,2) + ' ' + operator + ': \n' + $("#patient_notes").val();
	$("#patient_notes").val(t);
	set_caret_position("patient_notes", 13);
}

function remove_operator_name_date()
{
	var str_text = $("#patient_notes").val();
	var match_string = current_date(top.jquery_date_format,2) + ' ' + operator + ': \n';
	if(str_text == match_string )	$("#patient_notes").val('');
	
	if(str_text != "" && str_text != match_string) 
		$("#chkNotesScheduler").prop('checked',true);
	else
		$("#chkNotesScheduler").prop('checked',false);
		
}
			
/*
* Function : set_heard_type 
* Purpose - To set typehead in textarea according to selected 
*						value in HEARD ABOUT US Field	
* Params -	obj: this object of HEARD ABOUT US Field	
*/
function set_heard_type(obj)
{
	var orignalVal 	= obj.val();	
	var arrOrignalVal = orignalVal.split("-");
	
	collect_source('');
	
	if(arrOrignalVal.length > 1){
		var val = arrOrignalVal[1];
		val = val.replace(/[0-9]/,'num');
		if(val !== 'Dr.')
		{
			repObj = val.replace(/\s/g,'_');
			try{
				collect_source(repObj);
			}catch(e){
				collect_source('');
			}
		}
	}
	
}

/*
* Function : set_patient_ac_status 
* Purpose - 
* Params -
*	$_this - Holds this object from which event Occured 
*/
var other_val ='';
function set_patient_ac_status($_this)
{
	var set_status = 0; 
	var src = $_this.data('source'); var a = $_this.data('action');
	var v = $_this.val();
	
	if(src == 'btn')
	{
		if($('#other_status').val() == ''){
			top.fAlert("Please fill other status value in text field.");
			return false;
		}else{
			set_status = 1;
		}
	}
	else
	{
		
		if(v == 'other') {
			$_this.addClass('hidden').removeClass('inline');//selectpicker('hide');
			$('#otherStsDiv').removeClass('hidden').addClass('inline');
		} else {
			set_status=1;
			$_this.addClass('inline').removeClass('hidden');//selectpicker('show');
			$('#other_status').val('');
			$('#otherStsDiv').removeClass('inline').addClass('hidden');
		}
	}
	
	if(set_status == 1)
	{
		other_val = $('#other_status').val();
		var old_status = $('#oldStatus').val();
		var selected_text=$("#account_status option:selected").text();
		var url = top.JS_WEB_ROOT_PATH+'/interface/accounting/setPatAccountStatus.php?';
		var d = 'acId=' + v + '&otherVal=' + other_val + "&selectedText=" + selected_text + "&oldStatus=" + old_status
		url = url + d;
		top.master_ajax_tunnel(url,top.fmain.patient_ac_status_handler);
        
        //Assign task according to rule manager
        top.check_rule_manager(v, 'pt_account_status');
	}
}

/*
* Function : patient_ac_status_handler 
* Purpose - Handle Ajax Response After Setting PAtient Account Status
* Params -
*	r - Holds return date from ajax call
*/
function patient_ac_status_handler(r)
{
	var retVals= r.split('~~~');
	if(retVals[0]==1)
	{
		top.alert_notification_show("Patient Account Status is saved successfully.");
	}
	if(other_val!='')
	{
		var selected_text = $('#other_status').val();
		$('#other_status').val('');
		$('#otherStsDiv').removeClass('inline').addClass('hidden');
		$('#account_status').addClass('inline').removeClass('hidden').html(retVals[1]);// .selectpicker('show') .selectpicker('refresh');
	}
	var is_active = 'no';
	if(selected_text)
	{
		if(selected_text.toLowerCase() == 'active'){ is_active='yes'; } 
	}
	//top.changePatNameColor(is_active);
	top.update_iconbar();
}
/*
* Function : xhr_ajax_delete 
* Purpose - To handle Delete request using Ajax
*						This function will use all data attributes
*						to send params in ajax request 	
* Params -	
* $_this - Holds this object from which event Occured 
*/
function xhr_ajax_delete($_this)
{ 
	var p = $_this.data();
	var d = '';
	$.each(p,function(i,v){ d	+= '&' + i + '=' + v; });
	d = d.substr(1);
	var c_msg = "Are you sure, you want to delete ?";
	var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/ajax_save_update.php?"+d;
	top.fancyConfirm(c_msg,"", "window.top.show_loading_image('show');master_ajax_tunnel('"+url+"',top.fmain.xhr_ajax_delete_handler,'','json');","window.top.show_loading_image('hide');")
}

/*
* Function : xhr_ajax_delete_handler 
* Purpose - Handler/CallBack Method of xhr_ajax_delete METHOD
*						It handles the response returned by ajax request
* Params -	
* $_this - Holds this object from which event Occured 
*/

function xhr_ajax_delete_handler(r)
{
	if(r.success)
	{
		if(r.action == 'delete_resp_party')
		{
			$('#resp_container').find('input[type="text"],input[type="hidden"]').val('');
			$('#resp_container').find('input[type="checkbox"]').prop('checked',false);
			$('#resp_container').find('select').val('');//.selectpicker('refresh');
			$('#btn_del_resp_party,#viewText,#viewTexticon,#btn_del_rli,#btn_view_rli').hide();
		}
		else if(r.action == 'delete_resp_license')
		{
			$('#resp_container').find('#btn_del_rli,#btn_view_rli').remove();
		}
		top.alert_notification_show(r.msg);
	}
	else
	{
		top.fAlert("Unable to delete record");
	}	
	
	return;
}


/*
* Function : show_log 
* Purpose - to view Patient Access Log, login hostory
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function show_log($_this)
{
	xhr_ajax('',$_this);
}

/*
* Function : delete_call_timming 
* Purpose - to Reset Reminder Choice Values
* Params -	
* num - Holds row number in reminder choice 
*				box to reset the value
*/
function delete_call_timming(num)
{
	if(num>0){
		$('#hourFrom'+num+', #hourTo'+num).val('');
		$('#minFrom'+num+', #minTo'+num).val('00');
	}
}
	
/*
* Function : generate_activation_key 
* Purpose - Generate Activation Key in Patient Portal
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function generate_activation_key($_this)
{
	var d	=	$_this.data();
	var user_pass	=	$("#user_password").val();
	$_this.data('user-pass',user_pass);
	if(d.tempKeyChk == 1 || d.respUserName)
	{
		var c_msg	=	'Reset Temporary Key, will reduce count from MU Report until this key is given to Patient. Are you sure?';
		top.fancyConfirm(c_msg,"", "top.fmain.gen_temp_key('"+d.tempKeySize+"','1');","");
	}
	else{
		gen_temp_key(d.tempKeySize,'0'); 
	}
	return;
}

function gen_temp_key(tempKeySize,regen_key)
{
	$("#usernm,#pass1,#pass2").val('');
	var obj = $(".activation-key");
	obj.data('regen-key',regen_key); 
	xhr_ajax('',obj);
}

/*
* Function : collect_changes 
* Purpose - Combined function for all input/Select/Textarea tags 
*						If any change occured in any field
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function collect_changes($_this)
{
	_this = $_this[0];
	var p_val = $_this.attr('data-prev-val');
	var c_val = $_this.val();
	top.chk_change_in_form(p_val,_this,'DemoTabDb',event);
	chk_change(p_val,c_val);
}

function add_lang_code(code)
{
	if( code ) $("#lang_code").val(code);
}

/*
* Section Below is used for on load event as below:
* To bind Event with HTML Fields
* To trigger default event of HTML Fields
* To Attach Datepicker/Selectpicker Scripts
*/
$(function(){
	var noChange = ['scanner','created_by_name','reg_date','temp_key','usernm','fakeField'];
	$('body').on('keyup','#heardAbtDesc',function(event){
			_this =	$(this)[0]; $_this = $(this);
			var old_val = $("#elem_heardAbtUs").attr('data-desc');
			collect_changes($(this));
	});
	$('[data-toggle="popover"]').popover();
	//.pt-box input, .pt-box select, .pt-box textarea, .pt-box button[type=button], 
	$("body").on("focus", ".pt-box .grid-box", function(){
		$(this).closest('.pt-box').css({'background-color':'#ffffcc'});
	});
	
	$("body").on("blur", ".pt-box .grid-box", function(){
		$(".pt-box").css({'background':'transparent'})
	});
	
	$('body').on('keyup change keyupSwitch','input.mandatory-chk',function(e){ switch_mandatory_class($(this)[0]); });
	$('body').on('keyup change keyupSwitch','input.advisory-chk',function(e){ switch_advisory_class($(this)[0]); });
	
	$('body').on('change','select.mandatory-chk',function(e){
		if($(this).hasClass('selectpicker'))
			switch_mandatory_class_s($(this)[0]);
		else
			switch_mandatory_class($(this)[0]);
	});
	$('body').on('change','select.advisory-chk',function(e){
		if($(this).hasClass('selectpicker'))
			switch_advisory_class_s($(this)[0]);
		else
			switch_advisory_class($(this)[0]);
	});
	
	$('body').on('keyup blur','input[type="text"]',function(event){ if( $.inArray($(this).attr('id'),noChange) >=0 ){ return false; } collect_changes($(this));  });
	
	$("body").on('focusin','input[type="text"],textarea',function(event){ if( $.inArray($(this).attr('id'),noChange) >= 0){ return false; } get_focus_obj($(this)[0]); });
	
	$('body').on('change blur','#fname, #fname1, #mname, #mname1,  #lname, #lname1, #nick_name, #suffix, #suffix1, #birth_name, #maiden_fname, #maiden_mname, #maiden_lname, #contact_relationship, input[id^="street"], input[id^="street2"], #interpretter, #ename, #occupation, #estreet, input[id^="fname_table_family_information"], input[id^="mname_table_family_information"], input[id^="lname_table_family_information"], input[id^="street1_table_family_information"], input[id^="street2_table_family_information"] ',function(event){ 
		//console.log($(this).attr('id'));
		var v = $(this).val();
		var c = capitalize_letter(v);
		$(this).val(c);
	});
	
	$("body").on('keyup','#heardAbtSearch',function(){
		$("#heardAbtSearchId").val('');
	})
	$('body').on('change','#phone_home,#phone_biz, #phone_cell, input[id^="relInfoPhone"], #phone_home1, #phone_biz1, #phone_cell1, input[id^="phone_home_table_family_information"],input[id^="phone_work_table_family_information"],input[id^="phone_cell_table_family_information"]',function(event){ var c = 'form-control'; if($(this).hasClass('mandatory-chk')) { c = c + ' mandatory-chk mandatory'; } if($(this).hasClass('advisory-chk')) { c = c + ' advisory-chk advisory'; } if( $(this).val() ) { set_phone_format($(this)[0],phone_format,'','',c);} });
	
	$('body').on('paste','#phone_home,#phone_biz, #phone_cell, input[id^="relInfoPhone"], #phone_home1, #phone_biz1, #phone_cell1, input[id^="phone_home_table_family_information"],input[id^="phone_work_table_family_information"],input[id^="phone_cell_table_family_information"]',function(event){ 
		var pasteData = '';
		if( typeof event.originalEvent.clipboardData !== 'undefined' )
			pasteData = event.originalEvent.clipboardData.getData('text');
		else 
			pasteData = window.clipboardData.getData('text');
			
		pasteData = pasteData.replace(new RegExp('-','g'), '');
		$(this).val(pasteData).trigger('change');
	});
	
	$('body').on('blur change','#dob1',function(event){ 
		top.checkdate($(this)[0]);
		do_date_check($("#from_date_byram1")[0],$(this)[0]); 
	});
	
	$('body').on('change','#ss,#ss1',function(event){ validate_ssn($(this)[0]); });
	
	$('body').on('keyup','#code,#rcode,input[id^="code_table_family_information"]',function(event){ validate_zip($(this)[0]); });
	
	$('body').on('keyup','#state,#estate,#rstate,input[id^="state_table_family_information"]',function(event){ 
		checkIfAlphabet(event,$(this).attr('id')); 
	});
	
	$('body').on('keypress','input[type="text"]',function(event){ save_data(event) });
	
	$('body').on('change','input[type="checkbox"],#phone_home1',function(event){ 
		collect_changes($(this));	
		var n = $(this).attr('id');
		if(n === 'chkHippaRelResp' || n === 'phone_home1') { set_release_information('resp'); }
		else if(n === 'hipaa_voice') { display_hide_timmings(); }
		else if(str_exists(n,'chkHippaFamilyInformation')) { var s = n.split('_'); set_release_information(s[1]);  }
	});
	
	$('body').on('change','select',function(){
		_this = $(this)[0]; $_this= $(this);
		var n = $_this.attr('name');
		if(n)
		{
			if(n === 'relation1') { swap_combo_other(Array('relation1_oth'),Array('relation1'),_this); }
			else if(n === 'ado_option') { swap_combo_other(Array('ado_other_box'),Array('ado_option'),_this); }
			else if(n === 'emerRelation') { swap_combo_other(Array('relation_other_box'),Array('emerRelation'),_this); }
			else if(n === 'language') { swap_combo_other(Array('otherLanguageBox'),Array('language'),_this); add_lang_code($_this.find('option:selected').data('code'))  }
			else if(n === 'sexual_orientation') { swap_combo_other(Array('otherSORBox'),Array('sexual_orientation'),_this);  }
			else if(n === 'gender_identity') { swap_combo_other(Array('otherGIBox'),Array('gender_identity'),_this);  }
			else if(str_exists(n,'family_information_relatives')) { var t = $_this.attr('data-tab-num'); swap_combo_other(Array('family_rel_other_box_'+t),Array(n),_this); }
			else if(str_exists(n,'relInfoReletion')) { var t = $_this.attr('data-tab-num'); swap_combo_other(Array('otherRelInfoBox'+t),Array(n),_this); }
			else if( n === 'elem_heardAbtUs')
			{
				swap_combo_other(Array('otherHeardAboutBox'),Array('elem_heardAbtUs'),_this);
				var heardAbtVal = $("#elem_heardAbtUs").val();
				
				if(heardAbtVal !== '') {
					
					if( heardAbtVal !== 'Other' ) {
						var tmpArr = heardAbtVal.split("-");
						heardAbtVal = tmpArr[1].trim();
					}
					
					if($.inArray(heardAbtVal,heardAboutSearch ) !== -1 ) {
						if( heardAbtVal == 'Doctor') {
								$("#heardAbtSearch").attr('onkeyup',"top.loadPhysicians(this,'heardAbtSearchId')")
																		.attr('onfocus',"top.loadPhysicians(this,'heardAbtSearchId')")
																		.removeAttr('onKeydown');				
						}
						else {
							$("#heardAbtSearch").removeAttr('onkeyup onkeyup')
																	.attr('onKeydown','if( event.keyCode == 13) { searchHeardAbout(); }');	
						}
						$("#tdHeardAboutSearch").removeClass('hidden').addClass('inline');
						$("#heardAbtDesc").removeClass('inline').addClass('hidden');
					}
					else {
						$("#heardAbtDesc").removeClass('hidden').addClass('inline');
						$("#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
						set_heard_type($_this);
						$('#heardAbtDesc').typeahead({source:type_head_source});
					}
				}
				else {
					$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
				}
				
			}
			else if(n === 'elem_patientStatus')
			{
				var v = $_this.val();
				var s_arr = h_arr = [];
				if(v === 'Transferred' || v === 'Moved' || v === 'Other' )
				{
					s_arr = Array('tdOtherPatientStatus'); h_arr	=	Array('dod_patient_td');
				}
				else if(v === "Deceased")
				{
					s_arr = Array('dod_patient_td'); h_arr	=	Array('tdOtherPatientStatus');	
				}
				else
				{
					h_arr	=	Array('dod_patient_td','tdOtherPatientStatus');
				}
				swap_combo_other(s_arr,h_arr);		
			}
			collect_changes($(this));
		}
	});
	
	$('body').on('click','.back_other',function(){
		_this = $(this)[0]; $_this= $(this);
		var n = $_this.attr('data-tab-name'); 
		if(n === 'relation1') { swap_combo_other(Array('relation1'),Array('relation1_oth')); }
		else if(n === 'ado_option') { swap_combo_other(Array('ado_option'),Array('ado_other_box')); }
		else if(n === 'emerRelation') { swap_combo_other(Array('emerRelation'), Array('relation_other_box')); }
		else if(n === 'language') { swap_combo_other(Array('language'),Array('otherLanguageBox')); }
		else if(n === 'sexual_orientation') { swap_combo_other(Array('sexual_orientation'),Array('otherSORBox')); }
		else if(n === 'gender_identity') { swap_combo_other(Array('gender_identity'),Array('otherGIBox')); }
		else if(n === 'family_information_relatives') { var t = $_this.attr('data-tab-num'); swap_combo_other(Array(n+t),Array('family_rel_other_box_'+t)); }
		else if(n === 'relInfoReletion') { var t = $_this.attr('data-tab-num'); swap_combo_other(Array(n+t),Array('otherRelInfoBox'+t)); }
		else if( n === 'elem_heardAbtUs')
		{ 
			swap_combo_other(Array('elem_heardAbtUs'),Array('otherHeardAboutBox'));	
			$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
			set_heard_type($_this);
			$('#heardAbtDesc').typeahead({source:type_head_source});	
		}
		else if( n === 'account_status')
		{ 
			swap_combo_other(Array('account_status'),Array('otherStsDiv'));	
		}
		
		
	});
	
	$("body").on('click','#btn_del_rli,#btn_del_resp_party',function(){ xhr_ajax_delete($(this)); });
	
	$("body").on('click','#add_new_address',function(e){ add_new_address(); });
	
	$("body").on('blur','#lname1,input[id^="lname_table_family_information"]',function(e){ $('#sp_ajax').val($(this).val()); search_patient($(this)); });
	
	$("body").on('click','#sp_ajax_btn',function(e){ search_patient($('#sp_ajax')); });
	
	$("body").on('keyup','#sp_ajax',function(e){ if(e.keyCode == 13 ) { search_patient($('#sp_ajax')); } });
	
	$("body").on('click keyup','.search_physician',function(event){ 
		if( $(this).attr('id') == 'phy_ajax' && event.keyCode !== 13) {
			return false;
		}
		var d = $(this).data('source'); var o = $("#"+d); search_physician(o); 
	});
	
	$("body").on('click','a[data-click="pick_physician"]',function(e){ 
		var d = $(this).data(); $("#"+d.idBox).val(d.refId); $("#"+d.textBox).val(d.name);$("#"+d.textBox).removeClass('red-font');
		$("#phy_ajax").val('');
		$("#search_physician_result .modal-body").html('<div class="loader"></div>');
		$("#search_physician_result").modal('hide');
	});
	
	$("body").on('focus','#patient_notes',function(e){ get_operator_name_date(); });
	
	$("body").on('blur','#patient_notes',function(e){ remove_operator_name_date(); });
	
	$("body").on('click','[data-grid="family"], [data-grid="resp"]',function(e){
			var i = $(this).attr('data-row'); var t = $(this).attr('data-grid'); fill_grid_info(i,t);
	});
	
	$("body").on('click','.show_log',function(){ show_log($(this)); });
	
	$("body").on('click','.activation-key',function(){ generate_activation_key($(this)); });
	
	$("body").on('click','#done_btn_pt_override',function(){ gen_temp_key($(this).attr('data-temp-key-size'),'1'); });
	
	$("body").on('click','#demographics_hx',function(){ xhr_ajax('',$(this),false,'demographics_history'); });
	
	$("body").on('change','#account_status',function(){ set_patient_ac_status($(this)); });
	
	$("body").on('click','#btnSetStatus',function(){ set_patient_ac_status($(this)); });
	
	$("body").on('click','.physician_del',function(){  });
	
	$("body").on('click','.physician_add',function(){ });
	
	$("body").on('keypress','#elem_physicianName,#primaryCarePhy,#co_man_phy',function(e){ make_id_empty($(this).data('id-box')); });
	
	var _selectors = document.querySelectorAll('#elem_physicianName,#primaryCarePhy,#co_man_phy');
	for(var i = 0; i < _selectors.length; i++) {
			_selectors[i].addEventListener('keyup', function(event){
				var _this = $(this)[0];
				if( _this.hasAttribute('data-content') ) {
					if( _this.value == '' || _this.getAttribute('data-prev-val') !== _this.value ) {
						_this.setAttribute('data-content','');
						$(this).popover('destroy');
					}
					else {
						$(this).popover('hide');
					}
				}
			});
	}

	$("body").on('blur','#elem_physicianName,#primaryCarePhy,#co_man_phy',function(e){ refine_data($(this)[0]); });
	
	// Binding Date Picker with fields  
	setTimeout(function(){$('#dod_patient, #dob, #dob1').datetimepicker({timepicker:false,format:top.jquery_date_format,maxDate:new Date(),autoclose: true, scrollInput:false,onChangeDateTime:function(r,$input){ if($input[0].id == "dob"){get_age($input[0].defaultValue,$input[0].id,'patient_age','patient_age_month');}}});}, 200);
	// Binding Select Picker to select Tags having class selectpicker
	$('.selectpicker').selectpicker();
	// Add Mandatory Class to fields 
	$.each(mandatory_fld,function(i,v){ 
		$('#'+v).addClass('mandatory-chk');
		if(v == 'language') { $('#otherLanguage').addClass('mandatory-chk'); }
		else if(v == 'email') { $('#ptDemoEmail').addClass('mandatory-chk'); }
	});
		// Add Advisory Class to fields 
		$.each(advisory_fld,function(i,v){ 
			$('#'+v).addClass('advisory-chk');
			if(v == 'language') { $('#otherLanguage').addClass('advisory-chk'); }
			else if(v == 'email') { $('#ptDemoEmail').addClass('advisory-chk'); }
		});
	
	// Triggering events on window load
	$('select.mandatory-chk, select.advisory-chk, #elem_patientStatus, select[id^=relInfoReletion], #relation1, select[id^=family_information_relatives], #emerRelation,#sexual_orientation,#gender_identity').trigger('change');
	$('input.mandatory-chk,input.advisory-chk').trigger('keyupSwitch');
	// Set Default value to no || value changed to yes
	// by triggering event above for select and input tags 
	$("#hidChkChangeDemoTabDb",top.document).val('no');
	$("#hidChkDemoTabDbStatus",top.document).val('loaded');
 
	top.btn_show("DEMO");
	
	var r_modal = false;
	var e_modal = false;
	var l_modal = false;
	var i_modal = false;
	$("body").on('click','.load_modal',function(e){
		e.preventDefault();
		var t = $(this).data('modal');	
		var chk = false;
		if( t == 'race_modal') chk = r_modal; 
		else if( t == 'ethnicity_modal') chk = e_modal;
		else if( t == 'language_modal') chk = l_modal;
		else if( t == 'interpreter_modal') chk = i_modal;
		
		if(chk) {
			$("#" + t).modal('show');
			return false;	
		}
		
		var u = top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/demographics/ajax_handler.php';
		var p = {action:t};
		$.ajax({
				url:u,
				type:'post',
				data:p,
				dataType:'json',
				beforeSend:function(){
					$("#" + t).modal('show');
				},
				success:function(r){
					var d = r.data;
					$("#" + t + " .modal-body").html(d);
					if( t == 'race_modal') r_modal = true
					else if( t == 'ethnicity_modal') e_modal = true;
					else if( t == 'language_modal') l_modal = true;
					else if( t == 'interpreter_modal') i_modal = true;
				}
		});
		
	});
    
	$('body').on('show.bs.modal','#pt_account_status',function(){
		var btn_array = [];
		top.fmain.set_modal_btns('pt_account_status .modal-footer:first',btn_array);
	});
	$('body').on('show.bs.modal','#reminderChoice',function(){
		var btn_array = [];
		top.fmain.set_modal_btns('reminderChoice .modal-footer:first',btn_array);
	});
	$('body').on('show.bs.modal','#emergencyContact',function(){
		var btn_array = [];
		top.fmain.set_modal_btns('emergencyContact .modal-footer:first',btn_array);
	});
	
	//Start Temp Key generation By Default
	if($("#temp_key").val()=="" && $("#usernm").val()=="") {
		$(".activation-key").trigger('click');
	}
	//top.btn_show("DEMO");
});

// function for display current age in Years and months 
function get_age(fromdate, dob_id, yearBox, monthBox)
{
	if(dob_id != ''){
		var val_date = $('#'+dob_id+'').val();
		fromdate = val_date;
	}
	todate = new Date();
	var age= [], fromdate= new Date(fromdate),
	y= [todate.getFullYear(), fromdate.getFullYear()],
  ydiff= y[0]-y[1],
  m= [todate.getMonth(), fromdate.getMonth()],
  mdiff= m[0]-m[1],
  d= [todate.getDate(), fromdate.getDate()],
  ddiff= d[0]-d[1];
	
	if(mdiff < 0 || (mdiff=== 0 && ddiff<0))
		--ydiff;
	
	if(mdiff<0 || (mdiff === 0 && ddiff<0 ))
		mdiff += 12;
		
  if(ddiff<0)
	{
		fromdate.setMonth(m[1]+1, 0);
		ddiff= fromdate.getDate()-d[1]+d[0];
		--mdiff;
	}
	
	age.push(ydiff);
  age.push(mdiff);
	age.push(ddiff);
	
	$('#'+yearBox+'').html(age[0]);
	$('#'+monthBox+'').html(age[1]);
}

function checkIfAlphabet(e, ctrlId){
	txtVal ='';
	var txtVal = $("#"+ctrlId).val();
	if(txtVal!=''){
		var unicode= e.keyCode? e.keyCode : e.charCode;
		if(typeof unicode !== 'undefined'){
			if(!(unicode>=65 && unicode<=90) && !(unicode==8 || unicode==46 || unicode==13 || unicode==9
			 || unicode==16 || unicode==17 || unicode==18 || unicode==37 || unicode==38 || unicode==39 || unicode==40 || unicode==32)){
				top.fAlert("Only Character values accepted");
				var rightVal = txtVal.substr(0, parseInt(txtVal.length)-1);
				$("#"+ctrlId).val(rightVal);
				return false;
			}
		}
	}
}

function make_id_empty(odjId)
{
	if($("#"+odjId)) {
		if($("#"+odjId).val() !== "") {
			$("#"+odjId).val('');
		}
	}
}

function refine_data(obj)
{
	var data = obj.value;
	data = data.replace('"','"');
	obj.value = data;

	if( obj.hasAttribute('data-content') ) {
		if( obj.value == '' ) {
			obj.setAttribute('data-content','');
			$(obj).popover('destroy');
		}
	}
}	
		
var ModalID			=	"";
var txtFieldArr	=	"";
var hiddFieldTxt	=	"";
function show_multi_phy(op, phyType)
{
			op = op || 0;
			phyType = phyType || 0;
			
			var pTypeStr		=	"";
			var pTypeHidStr		=	"";
				
			if(phyType == 1)
			{
					ModalID			=	"referringPhysician";
					txtFieldArr		=	"txtRefPhyArr[]";
					pTypeStr		=	"strRefPhy";
					pTypeHidStr		=	"strRefPhyHid";
					hiddFieldTxt	=	"hidRefPhy";
			}
			else if(phyType == 2)
			{
					ModalID			=	"coManagedPhysician";
					txtFieldArr		=	"txtCoPhyArr[]";
					pTypeStr		=	"strCoPhy";
					pTypeHidStr		=	"strCoPhyHid";
					hiddFieldTxt	=	"hidCoPhy";
			}
			else if(phyType == 4)
			{
					ModalID			=	"primaryCarePhysician";
					txtFieldArr		=	"txtPCPDemoArr[]";
					pTypeStr		=	"strPCPDemoPhy";
					pTypeHidStr		=	"strPCPDemoHid";
					hiddFieldTxt	=	"hidPCPDemo";
			}
				
			if(op == 1)
			{
				var arrPhy 		= new Array();
				var arrPhyHid 	= new Array();
				var strPhy 		= "";
				var strPhyHid 	= "";
				
				if(document.getElementsByName(txtFieldArr))
				{
						var objPhyArr = document.getElementsByName(txtFieldArr);
						for(var i = 0; i < objPhyArr.length; i++){
							var objPhyArrID = objPhyArr[i].id;
							var arrPhyArrID = objPhyArrID.split("-");
							var hidPhyArrID = "hidPhyArr-" + arrPhyArrID[1];
							if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
								arrPhy[i] = document.getElementById(objPhyArrID).value;
								arrPhyHid[i] = document.getElementById(hidPhyArrID).value;
							}							
						}
						if(arrPhy.length > 0)
						{
							strPhy = arrPhy.join("!~#~!");
							strPhyHid = arrPhyHid.join("!~#~!");
						}
				}
				
				var d = 'mode=get&phyType='+phyType+'&'+pTypeStr+'='+strPhy+'&'+pTypeHidStr+'='+strPhyHid;
				var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
				
				top.master_ajax_tunnel(url,top.fmain.show_multi_phy_handler);
				
			}
			else if(op == 0){
				if($("#tat_table"))
					$("#tat_table").hide();	
				$("#"+ModalID).modal('hide');
			}
			else if(op == 2){
				var selectedEffect = "blind";
				if(phyType == 1){
					var strTxtRefPhyArr = "";
					var strHidRefPhyArrID = "";	
					var strHidRefPhyIdID = "";				
					if(document.getElementsByName("txtRefPhyArr[]")){
						var objRefPhyArr = document.getElementsByName("txtRefPhyArr[]");
						for(var i = 0; i < objRefPhyArr.length; i++){
							var objRefPhyArrID = objRefPhyArr[i].id;
							var arrRefPhyArrID = objRefPhyArrID.split("-");
							var hidRefPhyArrID = "hidRefPhyArr-" + arrRefPhyArrID[1];
							var hidRefPhyIdID = "hidRefPhyId" + arrRefPhyArrID[1];
							if((document.getElementById(objRefPhyArrID)) && (document.getElementById(hidRefPhyArrID))){
								strTxtRefPhyArr += document.getElementById(objRefPhyArrID).value + "!$@$!";
								strHidRefPhyArrID += document.getElementById(hidRefPhyArrID).value + "!$@$!";
								if(document.getElementById(hidRefPhyIdID)){
									strHidRefPhyIdID += document.getElementById(hidRefPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteRefPhyVal = document.getElementById("hidDeleteRefPhy").value;
					
					var d = 'mode=save&phyType='+phyType+'&strTxtRefPhyArr='+strTxtRefPhyArr+'&strHidRefPhyIdID='+strHidRefPhyIdID+'&strHidRefPhyArrID='+strHidRefPhyArrID+'&hidDeleteRefPhyVal='+hidDeleteRefPhyVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler);
				}
				else if(phyType == 2){
					var strTxtCoPhyArr = "";
					var strHidCoPhyArrID = "";
					var strHidCoPhyIdID = "";
					if(document.getElementsByName("txtCoPhyArr[]")){
						var objCoPhyArr = document.getElementsByName("txtCoPhyArr[]");
						for(var i = 0; i < objCoPhyArr.length; i++){
							var objCoPhyArrID = objCoPhyArr[i].id;
							var arrCoPhyArrID = objCoPhyArrID.split("-");
							var hidCoPhyArrID = "hidCoPhyArr-" + arrCoPhyArrID[1];
							var hidCoPhyIdID = "hidCoPhyId" + arrCoPhyArrID[1];
							if((document.getElementById(objCoPhyArrID)) && document.getElementById(hidCoPhyArrID)){
								strTxtCoPhyArr += document.getElementById(objCoPhyArrID).value + "!$@$!";
								strHidCoPhyArrID += document.getElementById(hidCoPhyArrID).value + "!$@$!";
								if(document.getElementById(hidCoPhyIdID)){
									strHidCoPhyIdID += document.getElementById(hidCoPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteCoPhyVal = document.getElementById("hidDeleteCoPhy").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtCoPhyArr="+strTxtCoPhyArr+"&strHidCoPhyIdID="+strHidCoPhyIdID+"&strHidCoPhyArrID="+strHidCoPhyArrID+"&hidDeleteCoPhyVal="+hidDeleteCoPhyVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_2);
				}
				else if(phyType == 4){
					var strTxtPCPDemoArr = "";
					var strHidPCPDemoArrID = "";
					var strHidPCPDemoIdID = "";
					if(document.getElementsByName("txtPCPDemoArr[]")){
						var objPCPDemoArr = document.getElementsByName("txtPCPDemoArr[]");
						for(var i = 0; i < objPCPDemoArr.length; i++){
							var objPCPDemoArrID = objPCPDemoArr[i].id;
							var arrPCPDemoArrID = objPCPDemoArrID.split("-");
							var hidPCPDemoArrID = "hidPCPDemoArr-" + arrPCPDemoArrID[1];
							var hidPCPDemoIdID = "hidPCPDemoId" + arrPCPDemoArrID[1];
							if((document.getElementById(objPCPDemoArrID)) && document.getElementById(hidPCPDemoArrID)){
								strTxtPCPDemoArr += document.getElementById(objPCPDemoArrID).value + "!$@$!";
								strHidPCPDemoArrID += document.getElementById(hidPCPDemoArrID).value + "!$@$!";
								if(document.getElementById(hidPCPDemoIdID)){
									strHidPCPDemoIdID += document.getElementById(hidPCPDemoIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeletePCPDemoVal = document.getElementById("hidDeletePCPDemo").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtPCPDemoArr="+strTxtPCPDemoArr+"&strHidPCPDemoArrID="+strHidPCPDemoArrID+"&strHidPCPDemoIdID="+strHidPCPDemoIdID+"&hidDeletePCPDemoVal="+hidDeletePCPDemoVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_4);
					
				}
			}
		}
		
function show_multi_phy_handler(respRes)
{
		var arrResp = respRes.split("!~-1-~!");
		var arrTemp = arrResp[1].split("~-~");
		var phyName = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyName[a] = arrTemp[a];
		}
		arrTemp = arrResp[2].split("~-~");
		var phyNameID = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyNameID[a] = arrTemp[a];
		}
		
		$("#"+ModalID).html(arrResp[0]);
		
		if(document.getElementsByName(txtFieldArr)){
			var objPhyArr = document.getElementsByName(txtFieldArr);
			for(var i = 0; i < objPhyArr.length; i++){
				var objPhyArrID = objPhyArr[i].id;
				var arrPhyArrID = objPhyArrID.split("-");
				var hidPhyArrID = hiddFieldTxt + "Arr-" + arrPhyArrID[1];
				if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
				}							
			}
		}
		
		$("#"+ModalID).modal('toggle');

}

function show_multi_phy_save_handler(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("elem_physicianName").className = "form-control";
		document.getElementById("elem_physicianName").value = arrRespRes[1];
		document.getElementById("pcare").value = arrRespRes[2];
		document.getElementById("elem_physicianName").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}
}

function show_multi_phy_save_handler_2(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("co_man_phy").className = "form-control";
		document.getElementById("co_man_phy").value = arrRespRes[1];
		document.getElementById("co_man_phy_id").value = arrRespRes[2];
		document.getElementById("co_man_phy").setAttribute('data-content',arrRespRes[3]);
		//$("#divMultiCoPhy").hide(selectedEffect,"", 500);
		$("#"+ModalID).modal('hide');
	}		
}

function show_multi_phy_save_handler_4(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("primaryCarePhy").className = "form-control";
		document.getElementById("primaryCarePhy").value = arrRespRes[1];
		document.getElementById("pCarePhy").value = arrRespRes[2];
		//$("#divMultiPCPDemo").hide(selectedEffect,"", 500);
		document.getElementById("primaryCarePhy").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}
}
		
function add_phy_row(add_image_id, del_image_id, intCounter, phyType)
{
			var objDelImg = $("#"+del_image_id);
			var objAddImg = $("#"+add_image_id);
			
			if(objAddImg){ objAddImg.addClass('hidden') }			
			if(objDelImg){ objDelImg.removeClass('hidden');	}
			
			var intCounterTemp = parseInt(intCounter) + 1;
			var divTrTag = document.createElement("div");
			divTrTag.id = "divTR" + "-" + phyType + "-" + intCounterTemp;
			divTrTag.className = "col-xs-12 margin-top-5";
			//divTrTag.style.marginBottom = "5px";
			
			var divTDTag1 = document.createElement("div");
			divTDTag1.className = "col-xs-2 text-center";
			divTDTag1.innerHTML = intCounterTemp;			
			divTrTag.appendChild(divTDTag1);
			
			var divTDTag2 = document.createElement("div");
			divTDTag2.className = "col-xs-9";
			
			if(phyType == 1){
				var txtId = "txtRefPhyArr-"+intCounterTemp;
			}
			else if(phyType == 2){
				var txtId = "txtCoPhyArr-"+intCounterTemp;
			}
			else if(phyType == 4){
				var txtId = "txtPCPDemoArr-"+intCounterTemp;
			}
			var txtBox = document.createElement("input");
			txtBox.type = "text";
			if(phyType == 1){
				txtBox.name = "txtRefPhyArr[]";
			}
			else if(phyType == 2){
				txtBox.name = "txtCoPhyArr[]";
			}
			else if(phyType == 4){
				txtBox.name = "txtPCPDemoArr[]";
			}
			txtBox.id = txtId;
			txtBox.value = "";
			txtBox.className = "form-control";
			
			if(phyType == 1){
				var hidId = "hidRefPhyArr-"+intCounterTemp;
			}
			else if(phyType == 2){
				var hidId = "hidCoPhyArr-"+intCounterTemp;
			}
			else if(phyType == 4){
				var hidId = "hidPCPDemoArr-"+intCounterTemp;
			}
			//
			txtBox.setAttribute('onKeyup',"top.loadPhysicians(this,'"+hidId+"');");
			txtBox.setAttribute('onFocus',"top.loadPhysicians(this,'"+hidId+"');");
			
			
			var hidBox = document.createElement("input");
			hidBox.type = "hidden";
			if(phyType == 1){
				hidBox.name = "hidRefPhyArr[]";
			}
			else if(phyType == 2){
				hidBox.name = "hidCoPhyArr[]";
			}
			else if(phyType == 4){
				hidBox.name = "hidPCPDemoArr[]";
			}
			divTDTag2.appendChild(txtBox);
			hidBox.id = hidId;
			hidBox.value = "";
			divTDTag2.appendChild(hidBox);
			divTrTag.appendChild(divTDTag2);
			
			var divTDTag3 = document.createElement("div");
			divTDTag3.className = "col-xs-1";
			var imgDelId = "imgDel" + "-" + phyType + "-" + intCounterTemp;
			var imgAddId = "imgAdd" + "-" + phyType + "-" + intCounterTemp;
			var strImgHTML = "<span id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"pointer hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" ><i class=\"glyphicon glyphicon-remove\"></i></span>";
			//var strImgHTML = "<img src=\""+top.JS_WEB_ROOT_PATH+"/library/images/close_small.png\" id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"physician_del hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" />";
			strImgHTML += "<span id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"pointer\" ><i class=\"glyphicon glyphicon-plus\"></i></span>"
			//strImgHTML += "<img src=\""+top.JS_WEB_ROOT_PATH+"/library/images/add_small.png\" id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"physician_add\" />"
			divTDTag3.innerHTML = strImgHTML;
			
			divTrTag.appendChild(divTDTag3);
			if(phyType == 1){
				document.getElementById("divMultiPhyInner1").appendChild(divTrTag);
			}
			else if(phyType == 2){
				document.getElementById("divMultiPhyInner2").appendChild(divTrTag);
			}
			else if(phyType == 4){
				document.getElementById("divMultiPhyInner4").appendChild(divTrTag);
			}
			//txtBox.addEventListener("keyup",function(){loadPhysicians(txtBox,hidId,'','','popup')});
			//txtBox.addEventListener("focus",function(){loadPhysicians(txtBox,hidId,'','','popup')});
			document.getElementById(txtId).focus();
		}
		
function del_phy_row(del_image_id, intCounter, intPhyIdDB, phyType)
{
			var objDelImg = $("#"+del_image_id);
			
			intPhyIdDB = intPhyIdDB || 0;
			//var divTrTag = "divTR" + intCounter;
			var divTrTag = "divTR" + "-" + phyType + "-" + intCounter
			if((intPhyIdDB > 0) && phyType == 1){				
				document.getElementById("hidDeleteRefPhy").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 2){				
				document.getElementById("hidDeleteCoPhy").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 4){				
				document.getElementById("hidDeletePCPDemo").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			if(document.getElementById(divTrTag)){
				var divType = "divMultiPhyInner" + phyType;
				var objMainDiv = document.getElementById(divType);
				objMainDiv.removeChild(document.getElementById(divTrTag));
			}
		}
		
function search_email2(event)
{
	if(event.keyCode == 64){
		if(document.getElementById('ptDemoEmail').value.length> 3)
		{
			document.getElementById('valDiv').style.display='block';
			document.getElementById('valDiv').style.visibility='visible';
		}
	}
}		

function display_hide_timmings()
{
	if($("#hipaa_voice").is(':checked'))
	{
		$('#trVoiceTimmings').removeClass('hidden').addClass('show');
	}
	else
	{
		$('#trVoiceTimmings').removeClass('show').addClass('hidden');
	}
}

function scan_patient_image()
{
	var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60";
	var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/demographics/webcam/flash.php";
	var wname = "lic";
	top.popup_win(url,wname,features);
}

function image_DIV(imageSrc,adiv)
{
	if(imageSrc)
	{
		var tmpImg = imageSrc;
		imageSrc = top.JS_WEB_ROOT_PATH + '/data/'+ top.practice_dir +'/'+imageSrc;
		if(adiv == "ptImage")
		{
			$("#ptImageDiv").html('<img src="'+imageSrc+'" >');
			$("#ptImage td").html('<img src="'+imageSrc+'" >');
			$("#div_pt_name img#pt_img_tmb",window.top.document).attr('src',imageSrc);
			/*document.getElementById('ptImageDiv').onclick=function() {
				$('#ptImage').show();
			};*/
		}
		else if(adiv == "ptLic")
		{
			var tmpArr = imageSrc.split('/');
			var lKey = tmpArr.length-1;
			tmpArr[lKey] = 'thumbnail/'+tmpArr[lKey];
			var thumbSrc = tmpArr.join('/');
			var html = '<span><img src="'+thumbSrc+'" /><span class="layer" data-toggle="modal" data-target="#imageLicense"></span></span>';
			dgi("ptLicDiv").innerHTML = html;
			$("#imageLicense .modal-body").html('<img src="'+imageSrc+'">');
		}
		else if(adiv == "respLic")
		{
			var tmpArr = imageSrc.split('/');
			var lKey = tmpArr.length-1;
			tmpArr[lKey] = 'thumbnail/'+tmpArr[lKey];
			var thumbSrc = tmpArr.join('/');
			var html = '<span><img src="'+thumbSrc+'" /><span class="layer" data-toggle="modal" data-target="#resp_party_license"></span></span>';
			dgi("respLicDiv").innerHTML = html;
			dgi('resp_license_image').value = tmpImg;
			$("#resp_party_license .modal-body").html('<img src="'+imageSrc+'">');
		}
	}
}

function scan_licence(pid,type)
{
	if( typeof type === 'undefined') type = '';
	var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1";
	var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/demographics/scan_licence.php"+(type?'?type='+type:'');
	var wname = type+"lic";
	sc_wd=(screen.availWidth-100);
	sc_hg=(screen.availHeight-100);
	features = "location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width="+sc_wd+",height="+sc_hg
	top.popup_win(url,wname,features);
}

function get271Report(id){
		var h = "<?php echo $_SESSION['wn_height'] - 140; ?>";
		window.open('../eligibility/eligibility_report.php?id='+id,'eligibility_report','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function ado_scan_fun(show,formName,scan_id)
{									
	scan_id = scan_id || 0;
	var w = 950 ;
	var h = 660 ;
	var l = parseInt((screen.availWidth - w ) / 2);
	var t = parseInt((screen.availHeight - h ) / 2);
	
	var features = 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width='+w+',height='+h+',left='+l+',top='+t;
	ado_scan_obj = window.open(top.JS_WEB_ROOT_PATH + '/library/classes/scan_ptinfo_medhx_images.php?formName='+formName+"&edit_id="+scan_id+""+'&show='+show,'PtMed'+show, features);
	ado_scan_obj.focus();
}
				 	
function showpdf(id,pdf,image_form){
	if( (typeof id != "undefined") && (id != "") ){
		
		var w = 950 ;
		var h = 700 ;
		var l = parseInt((screen.availWidth - w ) / 2);
		var t = parseInt((screen.availHeight - h ) / 2);
	
		pdf = pdf || '';
		image_form = image_form || '';
		var n = "scan_"+id;
		var url = top.JS_WEB_ROOT_PATH + "/interface/chart_notes/logoImg.php?from=scanImage&scan_id="+id+"&headery="+pdf+"&image_form="+image_form;
		var v = window.open(url,"","width="+w+",height="+h+",resizable=1,scrollbars=1,top="+t+",left="+l+"");				
		v.focus;
	}
}

function addExtra(_this)
{
	var isChecked = $(_this).is(':checked') ? true : false;
	var fldName = $(_this).data('object-id');
	var v = $(_this).val();
	var fldObj = $("#"+fldName);
	
	var tmpObj = fldObj.find('option[value="'+v+'"]');
	
	if( tmpObj.length > 0 ) {
		
		var isCommon = tmpObj.data('common');
		
		if( isCommon == '0' )	tmpObj.remove();
		else tmpObj.prop('selected',isChecked);
	
	} else {
		if( fldName == 'race')
			$("#"+fldName + " option:eq(-1)").before($('<option></option>').val(v).text(v).data('common','0').prop('selected',isChecked));
		else
			$("#"+fldName + " option:eq(-2)").before($('<option></option>').val(v).text(v).data('common','0').prop('selected',isChecked));
	}
	
	fldObj.selectpicker('refresh');
}

function addLanguage(_this)
{
	var isChecked = $(_this).is(':checked') ? true : false;
	
	$("#language_modal [type=checkbox]").prop('checked',false);
	$(_this).prop('checked',isChecked);
	
	var fldName = $(_this).data('object-id');
	var code = $(_this).data('code-name');
	var v = $(_this).val();
	var fldObj = $("#"+fldName);
	
	var c_value = fldObj.val();
	if( c_value == 'Other')	$("#imgBackLanguage").trigger('click');
	
	var tmpObj = fldObj.find('option[value="'+v+'"]');
	
	if( tmpObj.length > 0 ) {
		
		var isCommon = tmpObj.data('common');
		
		if( isCommon == '0' )	tmpObj.remove();
		else tmpObj.prop('selected',isChecked);
	
	} else {
			$("#"+fldName + " option:eq(-2)").before('<option value="'+v+'" data-common="0" data-code="'+code+'" '+(isChecked ? 'selected' : '')+' >'+v+'</option>');
	}
	
	//fldObj.selectpicker('refresh').trigger('change');
	fldObj.trigger('change');
}
function addInterpreter(_this)
{
	var isChecked = $(_this).is(':checked') ? true : false;
	
	$("#interpreter_modal [type=checkbox]").prop('checked',false);
	$(_this).prop('checked',isChecked);
	
	var fldName = $(_this).data('object-id');
	var code = $(_this).data('code-name');
	var v = $(_this).val();
	var fldObj = $("#"+fldName);
	
	var c_value = fldObj.val();
	
	var tmpObj = fldObj.find('option[value="'+v+'"]');
	
	if( tmpObj.length > 0 ) {
		
		var isCommon = tmpObj.data('common');
		
		if( isCommon == '0' )	tmpObj.remove();
		else tmpObj.prop('selected',isChecked);
	
	} else {
			$("#"+fldName + " option:eq(-2)").before('<option value="'+v+'" data-common="0" data-code="'+code+'" '+(isChecked ? 'selected' : '')+' >'+v+'</option>');
	}
	
	//fldObj.selectpicker('refresh').trigger('change');
	fldObj.trigger('change');
}

//Auto fill the Responsible party/guarantor address if patient's age is below 18 years
$('#resp_container').on('click', function(){
    $("#fname1").bind( "focus blur keyup",function(){ autofillRespParty(); });
    $("#lname1").bind( "focus blur keyup",function(){ autofillRespParty(); }); 
});

function autofillRespParty() {
    if(resp_party_arr && resp_party_arr!='' && resp_party_arr!='null' ) {
        if( ($("#fname1").val()!='' && $("#lname1").val()!='') && $("#street1").val()=='' && $("#rcode").val()=='' && $("#rcity").val()=='' && $("#rstate").val()=='') {
            var d=JSON.parse(resp_party_arr);
            $("#street1").val(d.resp_ptStreet);
            $("#street_emp").val(d.resp_ptStreet2);
            $("#rcode").val(d.resp_ptPostalCode);
            $("#rzip_ext").val(d.resp_ptzip_ext);
            $("#rcity").val(d.resp_ptCity);
            $("#rstate").val(d.resp_ptState);	
        }
    }
}
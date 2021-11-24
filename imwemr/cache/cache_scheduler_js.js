/*! jQuery v1.12.4 | (c) jQuery Foundation | jquery.org/license */
!function(a,b){"object"==typeof module&&"object"==typeof module.exports?module.exports=a.document?b(a,!0):function(a){if(!a.document)throw new Error("jQuery requires a window with a document");return b(a)}:b(a)}("undefined"!=typeof window?window:this,function(a,b){var c=[],d=a.document,e=c.slice,f=c.concat,g=c.push,h=c.indexOf,i={},j=i.toString,k=i.hasOwnProperty,l={},m="1.12.4",n=function(a,b){return new n.fn.init(a,b)},o=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,p=/^-ms-/,q=/-([\da-z])/gi,r=function(a,b){return b.toUpperCase()};n.fn=n.prototype={jquery:m,constructor:n,selector:"",length:0,toArray:function(){return e.call(this)},get:function(a){return null!=a?0>a?this[a+this.length]:this[a]:e.call(this)},pushStack:function(a){var b=n.merge(this.constructor(),a);return b.prevObject=this,b.context=this.context,b},each:function(a){return n.each(this,a)},map:function(a){return this.pushStack(n.map(this,function(b,c){return a.call(b,c,b)}))},slice:function(){return this.pushStack(e.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(a){var b=this.length,c=+a+(0>a?b:0);return this.pushStack(c>=0&&b>c?[this[c]]:[])},end:function(){return this.prevObject||this.constructor()},push:g,sort:c.sort,splice:c.splice},n.extend=n.fn.extend=function(){var a,b,c,d,e,f,g=arguments[0]||{},h=1,i=arguments.length,j=!1;for("boolean"==typeof g&&(j=g,g=arguments[h]||{},h++),"object"==typeof g||n.isFunction(g)||(g={}),h===i&&(g=this,h--);i>h;h++)if(null!=(e=arguments[h]))for(d in e)a=g[d],c=e[d],g!==c&&(j&&c&&(n.isPlainObject(c)||(b=n.isArray(c)))?(b?(b=!1,f=a&&n.isArray(a)?a:[]):f=a&&n.isPlainObject(a)?a:{},g[d]=n.extend(j,f,c)):void 0!==c&&(g[d]=c));return g},n.extend({expando:"jQuery"+(m+Math.random()).replace(/\D/g,""),isReady:!0,error:function(a){throw new Error(a)},noop:function(){},isFunction:function(a){return"function"===n.type(a)},isArray:Array.isArray||function(a){return"array"===n.type(a)},isWindow:function(a){return null!=a&&a==a.window},isNumeric:function(a){var b=a&&a.toString();return!n.isArray(a)&&b-parseFloat(b)+1>=0},isEmptyObject:function(a){var b;for(b in a)return!1;return!0},isPlainObject:function(a){var b;if(!a||"object"!==n.type(a)||a.nodeType||n.isWindow(a))return!1;try{if(a.constructor&&!k.call(a,"constructor")&&!k.call(a.constructor.prototype,"isPrototypeOf"))return!1}catch(c){return!1}if(!l.ownFirst)for(b in a)return k.call(a,b);for(b in a);return void 0===b||k.call(a,b)},type:function(a){return null==a?a+"":"object"==typeof a||"function"==typeof a?i[j.call(a)]||"object":typeof a},globalEval:function(b){b&&n.trim(b)&&(a.execScript||function(b){a.eval.call(a,b)})(b)},camelCase:function(a){return a.replace(p,"ms-").replace(q,r)},nodeName:function(a,b){return a.nodeName&&a.nodeName.toLowerCase()===b.toLowerCase()},each:function(a,b){var c,d=0;if(s(a)){for(c=a.length;c>d;d++)if(b.call(a[d],d,a[d])===!1)break}else for(d in a)if(b.call(a[d],d,a[d])===!1)break;return a},trim:function(a){return null==a?"":(a+"").replace(o,"")},makeArray:function(a,b){var c=b||[];return null!=a&&(s(Object(a))?n.merge(c,"string"==typeof a?[a]:a):g.call(c,a)),c},inArray:function(a,b,c){var d;if(b){if(h)return h.call(b,a,c);for(d=b.length,c=c?0>c?Math.max(0,d+c):c:0;d>c;c++)if(c in b&&b[c]===a)return c}return-1},merge:function(a,b){var c=+b.length,d=0,e=a.length;while(c>d)a[e++]=b[d++];if(c!==c)while(void 0!==b[d])a[e++]=b[d++];return a.length=e,a},grep:function(a,b,c){for(var d,e=[],f=0,g=a.length,h=!c;g>f;f++)d=!b(a[f],f),d!==h&&e.push(a[f]);return e},map:function(a,b,c){var d,e,g=0,h=[];if(s(a))for(d=a.length;d>g;g++)e=b(a[g],g,c),null!=e&&h.push(e);else for(g in a)e=b(a[g],g,c),null!=e&&h.push(e);return f.apply([],h)},guid:1,proxy:function(a,b){var c,d,f;return"string"==typeof b&&(f=a[b],b=a,a=f),n.isFunction(a)?(c=e.call(arguments,2),d=function(){return a.apply(b||this,c.concat(e.call(arguments)))},d.guid=a.guid=a.guid||n.guid++,d):void 0},now:function(){return+new Date},support:l}),"function"==typeof Symbol&&(n.fn[Symbol.iterator]=c[Symbol.iterator]),n.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(a,b){i["[object "+b+"]"]=b.toLowerCase()});function s(a){var b=!!a&&"length"in a&&a.length,c=n.type(a);return"function"===c||n.isWindow(a)?!1:"array"===c||0===b||"number"==typeof b&&b>0&&b-1 in a}var t=function(a){var b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u="sizzle"+1*new Date,v=a.document,w=0,x=0,y=ga(),z=ga(),A=ga(),B=function(a,b){return a===b&&(l=!0),0},C=1<<31,D={}.hasOwnProperty,E=[],F=E.pop,G=E.push,H=E.push,I=E.slice,J=function(a,b){for(var c=0,d=a.length;d>c;c++)if(a[c]===b)return c;return-1},K="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",L="[\\x20\\t\\r\\n\\f]",M="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",N="\\["+L+"*("+M+")(?:"+L+"*([*^$|!~]?=)"+L+"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|("+M+"))|)"+L+"*\\]",O=":("+M+")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|"+N+")*)|.*)\\)|)",P=new RegExp(L+"+","g"),Q=new RegExp("^"+L+"+|((?:^|[^\\\\])(?:\\\\.)*)"+L+"+$","g"),R=new RegExp("^"+L+"*,"+L+"*"),S=new RegExp("^"+L+"*([>+~]|"+L+")"+L+"*"),T=new RegExp("="+L+"*([^\\]'\"]*?)"+L+"*\\]","g"),U=new RegExp(O),V=new RegExp("^"+M+"$"),W={ID:new RegExp("^#("+M+")"),CLASS:new RegExp("^\\.("+M+")"),TAG:new RegExp("^("+M+"|[*])"),ATTR:new RegExp("^"+N),PSEUDO:new RegExp("^"+O),CHILD:new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+L+"*(even|odd|(([+-]|)(\\d*)n|)"+L+"*(?:([+-]|)"+L+"*(\\d+)|))"+L+"*\\)|)","i"),bool:new RegExp("^(?:"+K+")$","i"),needsContext:new RegExp("^"+L+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+L+"*((?:-\\d)?\\d*)"+L+"*\\)|)(?=[^-]|$)","i")},X=/^(?:input|select|textarea|button)$/i,Y=/^h\d$/i,Z=/^[^{]+\{\s*\[native \w/,$=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,_=/[+~]/,aa=/'|\\/g,ba=new RegExp("\\\\([\\da-f]{1,6}"+L+"?|("+L+")|.)","ig"),ca=function(a,b,c){var d="0x"+b-65536;return d!==d||c?b:0>d?String.fromCharCode(d+65536):String.fromCharCode(d>>10|55296,1023&d|56320)},da=function(){m()};try{H.apply(E=I.call(v.childNodes),v.childNodes),E[v.childNodes.length].nodeType}catch(ea){H={apply:E.length?function(a,b){G.apply(a,I.call(b))}:function(a,b){var c=a.length,d=0;while(a[c++]=b[d++]);a.length=c-1}}}function fa(a,b,d,e){var f,h,j,k,l,o,r,s,w=b&&b.ownerDocument,x=b?b.nodeType:9;if(d=d||[],"string"!=typeof a||!a||1!==x&&9!==x&&11!==x)return d;if(!e&&((b?b.ownerDocument||b:v)!==n&&m(b),b=b||n,p)){if(11!==x&&(o=$.exec(a)))if(f=o[1]){if(9===x){if(!(j=b.getElementById(f)))return d;if(j.id===f)return d.push(j),d}else if(w&&(j=w.getElementById(f))&&t(b,j)&&j.id===f)return d.push(j),d}else{if(o[2])return H.apply(d,b.getElementsByTagName(a)),d;if((f=o[3])&&c.getElementsByClassName&&b.getElementsByClassName)return H.apply(d,b.getElementsByClassName(f)),d}if(c.qsa&&!A[a+" "]&&(!q||!q.test(a))){if(1!==x)w=b,s=a;else if("object"!==b.nodeName.toLowerCase()){(k=b.getAttribute("id"))?k=k.replace(aa,"\\$&"):b.setAttribute("id",k=u),r=g(a),h=r.length,l=V.test(k)?"#"+k:"[id='"+k+"']";while(h--)r[h]=l+" "+qa(r[h]);s=r.join(","),w=_.test(a)&&oa(b.parentNode)||b}if(s)try{return H.apply(d,w.querySelectorAll(s)),d}catch(y){}finally{k===u&&b.removeAttribute("id")}}}return i(a.replace(Q,"$1"),b,d,e)}function ga(){var a=[];function b(c,e){return a.push(c+" ")>d.cacheLength&&delete b[a.shift()],b[c+" "]=e}return b}function ha(a){return a[u]=!0,a}function ia(a){var b=n.createElement("div");try{return!!a(b)}catch(c){return!1}finally{b.parentNode&&b.parentNode.removeChild(b),b=null}}function ja(a,b){var c=a.split("|"),e=c.length;while(e--)d.attrHandle[c[e]]=b}function ka(a,b){var c=b&&a,d=c&&1===a.nodeType&&1===b.nodeType&&(~b.sourceIndex||C)-(~a.sourceIndex||C);if(d)return d;if(c)while(c=c.nextSibling)if(c===b)return-1;return a?1:-1}function la(a){return function(b){var c=b.nodeName.toLowerCase();return"input"===c&&b.type===a}}function ma(a){return function(b){var c=b.nodeName.toLowerCase();return("input"===c||"button"===c)&&b.type===a}}function na(a){return ha(function(b){return b=+b,ha(function(c,d){var e,f=a([],c.length,b),g=f.length;while(g--)c[e=f[g]]&&(c[e]=!(d[e]=c[e]))})})}function oa(a){return a&&"undefined"!=typeof a.getElementsByTagName&&a}c=fa.support={},f=fa.isXML=function(a){var b=a&&(a.ownerDocument||a).documentElement;return b?"HTML"!==b.nodeName:!1},m=fa.setDocument=function(a){var b,e,g=a?a.ownerDocument||a:v;return g!==n&&9===g.nodeType&&g.documentElement?(n=g,o=n.documentElement,p=!f(n),(e=n.defaultView)&&e.top!==e&&(e.addEventListener?e.addEventListener("unload",da,!1):e.attachEvent&&e.attachEvent("onunload",da)),c.attributes=ia(function(a){return a.className="i",!a.getAttribute("className")}),c.getElementsByTagName=ia(function(a){return a.appendChild(n.createComment("")),!a.getElementsByTagName("*").length}),c.getElementsByClassName=Z.test(n.getElementsByClassName),c.getById=ia(function(a){return o.appendChild(a).id=u,!n.getElementsByName||!n.getElementsByName(u).length}),c.getById?(d.find.ID=function(a,b){if("undefined"!=typeof b.getElementById&&p){var c=b.getElementById(a);return c?[c]:[]}},d.filter.ID=function(a){var b=a.replace(ba,ca);return function(a){return a.getAttribute("id")===b}}):(delete d.find.ID,d.filter.ID=function(a){var b=a.replace(ba,ca);return function(a){var c="undefined"!=typeof a.getAttributeNode&&a.getAttributeNode("id");return c&&c.value===b}}),d.find.TAG=c.getElementsByTagName?function(a,b){return"undefined"!=typeof b.getElementsByTagName?b.getElementsByTagName(a):c.qsa?b.querySelectorAll(a):void 0}:function(a,b){var c,d=[],e=0,f=b.getElementsByTagName(a);if("*"===a){while(c=f[e++])1===c.nodeType&&d.push(c);return d}return f},d.find.CLASS=c.getElementsByClassName&&function(a,b){return"undefined"!=typeof b.getElementsByClassName&&p?b.getElementsByClassName(a):void 0},r=[],q=[],(c.qsa=Z.test(n.querySelectorAll))&&(ia(function(a){o.appendChild(a).innerHTML="<a id='"+u+"'></a><select id='"+u+"-\r\\' msallowcapture=''><option selected=''></option></select>",a.querySelectorAll("[msallowcapture^='']").length&&q.push("[*^$]="+L+"*(?:''|\"\")"),a.querySelectorAll("[selected]").length||q.push("\\["+L+"*(?:value|"+K+")"),a.querySelectorAll("[id~="+u+"-]").length||q.push("~="),a.querySelectorAll(":checked").length||q.push(":checked"),a.querySelectorAll("a#"+u+"+*").length||q.push(".#.+[+~]")}),ia(function(a){var b=n.createElement("input");b.setAttribute("type","hidden"),a.appendChild(b).setAttribute("name","D"),a.querySelectorAll("[name=d]").length&&q.push("name"+L+"*[*^$|!~]?="),a.querySelectorAll(":enabled").length||q.push(":enabled",":disabled"),a.querySelectorAll("*,:x"),q.push(",.*:")})),(c.matchesSelector=Z.test(s=o.matches||o.webkitMatchesSelector||o.mozMatchesSelector||o.oMatchesSelector||o.msMatchesSelector))&&ia(function(a){c.disconnectedMatch=s.call(a,"div"),s.call(a,"[s!='']:x"),r.push("!=",O)}),q=q.length&&new RegExp(q.join("|")),r=r.length&&new RegExp(r.join("|")),b=Z.test(o.compareDocumentPosition),t=b||Z.test(o.contains)?function(a,b){var c=9===a.nodeType?a.documentElement:a,d=b&&b.parentNode;return a===d||!(!d||1!==d.nodeType||!(c.contains?c.contains(d):a.compareDocumentPosition&&16&a.compareDocumentPosition(d)))}:function(a,b){if(b)while(b=b.parentNode)if(b===a)return!0;return!1},B=b?function(a,b){if(a===b)return l=!0,0;var d=!a.compareDocumentPosition-!b.compareDocumentPosition;return d?d:(d=(a.ownerDocument||a)===(b.ownerDocument||b)?a.compareDocumentPosition(b):1,1&d||!c.sortDetached&&b.compareDocumentPosition(a)===d?a===n||a.ownerDocument===v&&t(v,a)?-1:b===n||b.ownerDocument===v&&t(v,b)?1:k?J(k,a)-J(k,b):0:4&d?-1:1)}:function(a,b){if(a===b)return l=!0,0;var c,d=0,e=a.parentNode,f=b.parentNode,g=[a],h=[b];if(!e||!f)return a===n?-1:b===n?1:e?-1:f?1:k?J(k,a)-J(k,b):0;if(e===f)return ka(a,b);c=a;while(c=c.parentNode)g.unshift(c);c=b;while(c=c.parentNode)h.unshift(c);while(g[d]===h[d])d++;return d?ka(g[d],h[d]):g[d]===v?-1:h[d]===v?1:0},n):n},fa.matches=function(a,b){return fa(a,null,null,b)},fa.matchesSelector=function(a,b){if((a.ownerDocument||a)!==n&&m(a),b=b.replace(T,"='$1']"),c.matchesSelector&&p&&!A[b+" "]&&(!r||!r.test(b))&&(!q||!q.test(b)))try{var d=s.call(a,b);if(d||c.disconnectedMatch||a.document&&11!==a.document.nodeType)return d}catch(e){}return fa(b,n,null,[a]).length>0},fa.contains=function(a,b){return(a.ownerDocument||a)!==n&&m(a),t(a,b)},fa.attr=function(a,b){(a.ownerDocument||a)!==n&&m(a);var e=d.attrHandle[b.toLowerCase()],f=e&&D.call(d.attrHandle,b.toLowerCase())?e(a,b,!p):void 0;return void 0!==f?f:c.attributes||!p?a.getAttribute(b):(f=a.getAttributeNode(b))&&f.specified?f.value:null},fa.error=function(a){throw new Error("Syntax error, unrecognized expression: "+a)},fa.uniqueSort=function(a){var b,d=[],e=0,f=0;if(l=!c.detectDuplicates,k=!c.sortStable&&a.slice(0),a.sort(B),l){while(b=a[f++])b===a[f]&&(e=d.push(f));while(e--)a.splice(d[e],1)}return k=null,a},e=fa.getText=function(a){var b,c="",d=0,f=a.nodeType;if(f){if(1===f||9===f||11===f){if("string"==typeof a.textContent)return a.textContent;for(a=a.firstChild;a;a=a.nextSibling)c+=e(a)}else if(3===f||4===f)return a.nodeValue}else while(b=a[d++])c+=e(b);return c},d=fa.selectors={cacheLength:50,createPseudo:ha,match:W,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(a){return a[1]=a[1].replace(ba,ca),a[3]=(a[3]||a[4]||a[5]||"").replace(ba,ca),"~="===a[2]&&(a[3]=" "+a[3]+" "),a.slice(0,4)},CHILD:function(a){return a[1]=a[1].toLowerCase(),"nth"===a[1].slice(0,3)?(a[3]||fa.error(a[0]),a[4]=+(a[4]?a[5]+(a[6]||1):2*("even"===a[3]||"odd"===a[3])),a[5]=+(a[7]+a[8]||"odd"===a[3])):a[3]&&fa.error(a[0]),a},PSEUDO:function(a){var b,c=!a[6]&&a[2];return W.CHILD.test(a[0])?null:(a[3]?a[2]=a[4]||a[5]||"":c&&U.test(c)&&(b=g(c,!0))&&(b=c.indexOf(")",c.length-b)-c.length)&&(a[0]=a[0].slice(0,b),a[2]=c.slice(0,b)),a.slice(0,3))}},filter:{TAG:function(a){var b=a.replace(ba,ca).toLowerCase();return"*"===a?function(){return!0}:function(a){return a.nodeName&&a.nodeName.toLowerCase()===b}},CLASS:function(a){var b=y[a+" "];return b||(b=new RegExp("(^|"+L+")"+a+"("+L+"|$)"))&&y(a,function(a){return b.test("string"==typeof a.className&&a.className||"undefined"!=typeof a.getAttribute&&a.getAttribute("class")||"")})},ATTR:function(a,b,c){return function(d){var e=fa.attr(d,a);return null==e?"!="===b:b?(e+="","="===b?e===c:"!="===b?e!==c:"^="===b?c&&0===e.indexOf(c):"*="===b?c&&e.indexOf(c)>-1:"$="===b?c&&e.slice(-c.length)===c:"~="===b?(" "+e.replace(P," ")+" ").indexOf(c)>-1:"|="===b?e===c||e.slice(0,c.length+1)===c+"-":!1):!0}},CHILD:function(a,b,c,d,e){var f="nth"!==a.slice(0,3),g="last"!==a.slice(-4),h="of-type"===b;return 1===d&&0===e?function(a){return!!a.parentNode}:function(b,c,i){var j,k,l,m,n,o,p=f!==g?"nextSibling":"previousSibling",q=b.parentNode,r=h&&b.nodeName.toLowerCase(),s=!i&&!h,t=!1;if(q){if(f){while(p){m=b;while(m=m[p])if(h?m.nodeName.toLowerCase()===r:1===m.nodeType)return!1;o=p="only"===a&&!o&&"nextSibling"}return!0}if(o=[g?q.firstChild:q.lastChild],g&&s){m=q,l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),j=k[a]||[],n=j[0]===w&&j[1],t=n&&j[2],m=n&&q.childNodes[n];while(m=++n&&m&&m[p]||(t=n=0)||o.pop())if(1===m.nodeType&&++t&&m===b){k[a]=[w,n,t];break}}else if(s&&(m=b,l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),j=k[a]||[],n=j[0]===w&&j[1],t=n),t===!1)while(m=++n&&m&&m[p]||(t=n=0)||o.pop())if((h?m.nodeName.toLowerCase()===r:1===m.nodeType)&&++t&&(s&&(l=m[u]||(m[u]={}),k=l[m.uniqueID]||(l[m.uniqueID]={}),k[a]=[w,t]),m===b))break;return t-=e,t===d||t%d===0&&t/d>=0}}},PSEUDO:function(a,b){var c,e=d.pseudos[a]||d.setFilters[a.toLowerCase()]||fa.error("unsupported pseudo: "+a);return e[u]?e(b):e.length>1?(c=[a,a,"",b],d.setFilters.hasOwnProperty(a.toLowerCase())?ha(function(a,c){var d,f=e(a,b),g=f.length;while(g--)d=J(a,f[g]),a[d]=!(c[d]=f[g])}):function(a){return e(a,0,c)}):e}},pseudos:{not:ha(function(a){var b=[],c=[],d=h(a.replace(Q,"$1"));return d[u]?ha(function(a,b,c,e){var f,g=d(a,null,e,[]),h=a.length;while(h--)(f=g[h])&&(a[h]=!(b[h]=f))}):function(a,e,f){return b[0]=a,d(b,null,f,c),b[0]=null,!c.pop()}}),has:ha(function(a){return function(b){return fa(a,b).length>0}}),contains:ha(function(a){return a=a.replace(ba,ca),function(b){return(b.textContent||b.innerText||e(b)).indexOf(a)>-1}}),lang:ha(function(a){return V.test(a||"")||fa.error("unsupported lang: "+a),a=a.replace(ba,ca).toLowerCase(),function(b){var c;do if(c=p?b.lang:b.getAttribute("xml:lang")||b.getAttribute("lang"))return c=c.toLowerCase(),c===a||0===c.indexOf(a+"-");while((b=b.parentNode)&&1===b.nodeType);return!1}}),target:function(b){var c=a.location&&a.location.hash;return c&&c.slice(1)===b.id},root:function(a){return a===o},focus:function(a){return a===n.activeElement&&(!n.hasFocus||n.hasFocus())&&!!(a.type||a.href||~a.tabIndex)},enabled:function(a){return a.disabled===!1},disabled:function(a){return a.disabled===!0},checked:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&!!a.checked||"option"===b&&!!a.selected},selected:function(a){return a.parentNode&&a.parentNode.selectedIndex,a.selected===!0},empty:function(a){for(a=a.firstChild;a;a=a.nextSibling)if(a.nodeType<6)return!1;return!0},parent:function(a){return!d.pseudos.empty(a)},header:function(a){return Y.test(a.nodeName)},input:function(a){return X.test(a.nodeName)},button:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&"button"===a.type||"button"===b},text:function(a){var b;return"input"===a.nodeName.toLowerCase()&&"text"===a.type&&(null==(b=a.getAttribute("type"))||"text"===b.toLowerCase())},first:na(function(){return[0]}),last:na(function(a,b){return[b-1]}),eq:na(function(a,b,c){return[0>c?c+b:c]}),even:na(function(a,b){for(var c=0;b>c;c+=2)a.push(c);return a}),odd:na(function(a,b){for(var c=1;b>c;c+=2)a.push(c);return a}),lt:na(function(a,b,c){for(var d=0>c?c+b:c;--d>=0;)a.push(d);return a}),gt:na(function(a,b,c){for(var d=0>c?c+b:c;++d<b;)a.push(d);return a})}},d.pseudos.nth=d.pseudos.eq;for(b in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})d.pseudos[b]=la(b);for(b in{submit:!0,reset:!0})d.pseudos[b]=ma(b);function pa(){}pa.prototype=d.filters=d.pseudos,d.setFilters=new pa,g=fa.tokenize=function(a,b){var c,e,f,g,h,i,j,k=z[a+" "];if(k)return b?0:k.slice(0);h=a,i=[],j=d.preFilter;while(h){c&&!(e=R.exec(h))||(e&&(h=h.slice(e[0].length)||h),i.push(f=[])),c=!1,(e=S.exec(h))&&(c=e.shift(),f.push({value:c,type:e[0].replace(Q," ")}),h=h.slice(c.length));for(g in d.filter)!(e=W[g].exec(h))||j[g]&&!(e=j[g](e))||(c=e.shift(),f.push({value:c,type:g,matches:e}),h=h.slice(c.length));if(!c)break}return b?h.length:h?fa.error(a):z(a,i).slice(0)};function qa(a){for(var b=0,c=a.length,d="";c>b;b++)d+=a[b].value;return d}function ra(a,b,c){var d=b.dir,e=c&&"parentNode"===d,f=x++;return b.first?function(b,c,f){while(b=b[d])if(1===b.nodeType||e)return a(b,c,f)}:function(b,c,g){var h,i,j,k=[w,f];if(g){while(b=b[d])if((1===b.nodeType||e)&&a(b,c,g))return!0}else while(b=b[d])if(1===b.nodeType||e){if(j=b[u]||(b[u]={}),i=j[b.uniqueID]||(j[b.uniqueID]={}),(h=i[d])&&h[0]===w&&h[1]===f)return k[2]=h[2];if(i[d]=k,k[2]=a(b,c,g))return!0}}}function sa(a){return a.length>1?function(b,c,d){var e=a.length;while(e--)if(!a[e](b,c,d))return!1;return!0}:a[0]}function ta(a,b,c){for(var d=0,e=b.length;e>d;d++)fa(a,b[d],c);return c}function ua(a,b,c,d,e){for(var f,g=[],h=0,i=a.length,j=null!=b;i>h;h++)(f=a[h])&&(c&&!c(f,d,e)||(g.push(f),j&&b.push(h)));return g}function va(a,b,c,d,e,f){return d&&!d[u]&&(d=va(d)),e&&!e[u]&&(e=va(e,f)),ha(function(f,g,h,i){var j,k,l,m=[],n=[],o=g.length,p=f||ta(b||"*",h.nodeType?[h]:h,[]),q=!a||!f&&b?p:ua(p,m,a,h,i),r=c?e||(f?a:o||d)?[]:g:q;if(c&&c(q,r,h,i),d){j=ua(r,n),d(j,[],h,i),k=j.length;while(k--)(l=j[k])&&(r[n[k]]=!(q[n[k]]=l))}if(f){if(e||a){if(e){j=[],k=r.length;while(k--)(l=r[k])&&j.push(q[k]=l);e(null,r=[],j,i)}k=r.length;while(k--)(l=r[k])&&(j=e?J(f,l):m[k])>-1&&(f[j]=!(g[j]=l))}}else r=ua(r===g?r.splice(o,r.length):r),e?e(null,g,r,i):H.apply(g,r)})}function wa(a){for(var b,c,e,f=a.length,g=d.relative[a[0].type],h=g||d.relative[" "],i=g?1:0,k=ra(function(a){return a===b},h,!0),l=ra(function(a){return J(b,a)>-1},h,!0),m=[function(a,c,d){var e=!g&&(d||c!==j)||((b=c).nodeType?k(a,c,d):l(a,c,d));return b=null,e}];f>i;i++)if(c=d.relative[a[i].type])m=[ra(sa(m),c)];else{if(c=d.filter[a[i].type].apply(null,a[i].matches),c[u]){for(e=++i;f>e;e++)if(d.relative[a[e].type])break;return va(i>1&&sa(m),i>1&&qa(a.slice(0,i-1).concat({value:" "===a[i-2].type?"*":""})).replace(Q,"$1"),c,e>i&&wa(a.slice(i,e)),f>e&&wa(a=a.slice(e)),f>e&&qa(a))}m.push(c)}return sa(m)}function xa(a,b){var c=b.length>0,e=a.length>0,f=function(f,g,h,i,k){var l,o,q,r=0,s="0",t=f&&[],u=[],v=j,x=f||e&&d.find.TAG("*",k),y=w+=null==v?1:Math.random()||.1,z=x.length;for(k&&(j=g===n||g||k);s!==z&&null!=(l=x[s]);s++){if(e&&l){o=0,g||l.ownerDocument===n||(m(l),h=!p);while(q=a[o++])if(q(l,g||n,h)){i.push(l);break}k&&(w=y)}c&&((l=!q&&l)&&r--,f&&t.push(l))}if(r+=s,c&&s!==r){o=0;while(q=b[o++])q(t,u,g,h);if(f){if(r>0)while(s--)t[s]||u[s]||(u[s]=F.call(i));u=ua(u)}H.apply(i,u),k&&!f&&u.length>0&&r+b.length>1&&fa.uniqueSort(i)}return k&&(w=y,j=v),t};return c?ha(f):f}return h=fa.compile=function(a,b){var c,d=[],e=[],f=A[a+" "];if(!f){b||(b=g(a)),c=b.length;while(c--)f=wa(b[c]),f[u]?d.push(f):e.push(f);f=A(a,xa(e,d)),f.selector=a}return f},i=fa.select=function(a,b,e,f){var i,j,k,l,m,n="function"==typeof a&&a,o=!f&&g(a=n.selector||a);if(e=e||[],1===o.length){if(j=o[0]=o[0].slice(0),j.length>2&&"ID"===(k=j[0]).type&&c.getById&&9===b.nodeType&&p&&d.relative[j[1].type]){if(b=(d.find.ID(k.matches[0].replace(ba,ca),b)||[])[0],!b)return e;n&&(b=b.parentNode),a=a.slice(j.shift().value.length)}i=W.needsContext.test(a)?0:j.length;while(i--){if(k=j[i],d.relative[l=k.type])break;if((m=d.find[l])&&(f=m(k.matches[0].replace(ba,ca),_.test(j[0].type)&&oa(b.parentNode)||b))){if(j.splice(i,1),a=f.length&&qa(j),!a)return H.apply(e,f),e;break}}}return(n||h(a,o))(f,b,!p,e,!b||_.test(a)&&oa(b.parentNode)||b),e},c.sortStable=u.split("").sort(B).join("")===u,c.detectDuplicates=!!l,m(),c.sortDetached=ia(function(a){return 1&a.compareDocumentPosition(n.createElement("div"))}),ia(function(a){return a.innerHTML="<a href='#'></a>","#"===a.firstChild.getAttribute("href")})||ja("type|href|height|width",function(a,b,c){return c?void 0:a.getAttribute(b,"type"===b.toLowerCase()?1:2)}),c.attributes&&ia(function(a){return a.innerHTML="<input/>",a.firstChild.setAttribute("value",""),""===a.firstChild.getAttribute("value")})||ja("value",function(a,b,c){return c||"input"!==a.nodeName.toLowerCase()?void 0:a.defaultValue}),ia(function(a){return null==a.getAttribute("disabled")})||ja(K,function(a,b,c){var d;return c?void 0:a[b]===!0?b.toLowerCase():(d=a.getAttributeNode(b))&&d.specified?d.value:null}),fa}(a);n.find=t,n.expr=t.selectors,n.expr[":"]=n.expr.pseudos,n.uniqueSort=n.unique=t.uniqueSort,n.text=t.getText,n.isXMLDoc=t.isXML,n.contains=t.contains;var u=function(a,b,c){var d=[],e=void 0!==c;while((a=a[b])&&9!==a.nodeType)if(1===a.nodeType){if(e&&n(a).is(c))break;d.push(a)}return d},v=function(a,b){for(var c=[];a;a=a.nextSibling)1===a.nodeType&&a!==b&&c.push(a);return c},w=n.expr.match.needsContext,x=/^<([\w-]+)\s*\/?>(?:<\/\1>|)$/,y=/^.[^:#\[\.,]*$/;function z(a,b,c){if(n.isFunction(b))return n.grep(a,function(a,d){return!!b.call(a,d,a)!==c});if(b.nodeType)return n.grep(a,function(a){return a===b!==c});if("string"==typeof b){if(y.test(b))return n.filter(b,a,c);b=n.filter(b,a)}return n.grep(a,function(a){return n.inArray(a,b)>-1!==c})}n.filter=function(a,b,c){var d=b[0];return c&&(a=":not("+a+")"),1===b.length&&1===d.nodeType?n.find.matchesSelector(d,a)?[d]:[]:n.find.matches(a,n.grep(b,function(a){return 1===a.nodeType}))},n.fn.extend({find:function(a){var b,c=[],d=this,e=d.length;if("string"!=typeof a)return this.pushStack(n(a).filter(function(){for(b=0;e>b;b++)if(n.contains(d[b],this))return!0}));for(b=0;e>b;b++)n.find(a,d[b],c);return c=this.pushStack(e>1?n.unique(c):c),c.selector=this.selector?this.selector+" "+a:a,c},filter:function(a){return this.pushStack(z(this,a||[],!1))},not:function(a){return this.pushStack(z(this,a||[],!0))},is:function(a){return!!z(this,"string"==typeof a&&w.test(a)?n(a):a||[],!1).length}});var A,B=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,C=n.fn.init=function(a,b,c){var e,f;if(!a)return this;if(c=c||A,"string"==typeof a){if(e="<"===a.charAt(0)&&">"===a.charAt(a.length-1)&&a.length>=3?[null,a,null]:B.exec(a),!e||!e[1]&&b)return!b||b.jquery?(b||c).find(a):this.constructor(b).find(a);if(e[1]){if(b=b instanceof n?b[0]:b,n.merge(this,n.parseHTML(e[1],b&&b.nodeType?b.ownerDocument||b:d,!0)),x.test(e[1])&&n.isPlainObject(b))for(e in b)n.isFunction(this[e])?this[e](b[e]):this.attr(e,b[e]);return this}if(f=d.getElementById(e[2]),f&&f.parentNode){if(f.id!==e[2])return A.find(a);this.length=1,this[0]=f}return this.context=d,this.selector=a,this}return a.nodeType?(this.context=this[0]=a,this.length=1,this):n.isFunction(a)?"undefined"!=typeof c.ready?c.ready(a):a(n):(void 0!==a.selector&&(this.selector=a.selector,this.context=a.context),n.makeArray(a,this))};C.prototype=n.fn,A=n(d);var D=/^(?:parents|prev(?:Until|All))/,E={children:!0,contents:!0,next:!0,prev:!0};n.fn.extend({has:function(a){var b,c=n(a,this),d=c.length;return this.filter(function(){for(b=0;d>b;b++)if(n.contains(this,c[b]))return!0})},closest:function(a,b){for(var c,d=0,e=this.length,f=[],g=w.test(a)||"string"!=typeof a?n(a,b||this.context):0;e>d;d++)for(c=this[d];c&&c!==b;c=c.parentNode)if(c.nodeType<11&&(g?g.index(c)>-1:1===c.nodeType&&n.find.matchesSelector(c,a))){f.push(c);break}return this.pushStack(f.length>1?n.uniqueSort(f):f)},index:function(a){return a?"string"==typeof a?n.inArray(this[0],n(a)):n.inArray(a.jquery?a[0]:a,this):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(a,b){return this.pushStack(n.uniqueSort(n.merge(this.get(),n(a,b))))},addBack:function(a){return this.add(null==a?this.prevObject:this.prevObject.filter(a))}});function F(a,b){do a=a[b];while(a&&1!==a.nodeType);return a}n.each({parent:function(a){var b=a.parentNode;return b&&11!==b.nodeType?b:null},parents:function(a){return u(a,"parentNode")},parentsUntil:function(a,b,c){return u(a,"parentNode",c)},next:function(a){return F(a,"nextSibling")},prev:function(a){return F(a,"previousSibling")},nextAll:function(a){return u(a,"nextSibling")},prevAll:function(a){return u(a,"previousSibling")},nextUntil:function(a,b,c){return u(a,"nextSibling",c)},prevUntil:function(a,b,c){return u(a,"previousSibling",c)},siblings:function(a){return v((a.parentNode||{}).firstChild,a)},children:function(a){return v(a.firstChild)},contents:function(a){return n.nodeName(a,"iframe")?a.contentDocument||a.contentWindow.document:n.merge([],a.childNodes)}},function(a,b){n.fn[a]=function(c,d){var e=n.map(this,b,c);return"Until"!==a.slice(-5)&&(d=c),d&&"string"==typeof d&&(e=n.filter(d,e)),this.length>1&&(E[a]||(e=n.uniqueSort(e)),D.test(a)&&(e=e.reverse())),this.pushStack(e)}});var G=/\S+/g;function H(a){var b={};return n.each(a.match(G)||[],function(a,c){b[c]=!0}),b}n.Callbacks=function(a){a="string"==typeof a?H(a):n.extend({},a);var b,c,d,e,f=[],g=[],h=-1,i=function(){for(e=a.once,d=b=!0;g.length;h=-1){c=g.shift();while(++h<f.length)f[h].apply(c[0],c[1])===!1&&a.stopOnFalse&&(h=f.length,c=!1)}a.memory||(c=!1),b=!1,e&&(f=c?[]:"")},j={add:function(){return f&&(c&&!b&&(h=f.length-1,g.push(c)),function d(b){n.each(b,function(b,c){n.isFunction(c)?a.unique&&j.has(c)||f.push(c):c&&c.length&&"string"!==n.type(c)&&d(c)})}(arguments),c&&!b&&i()),this},remove:function(){return n.each(arguments,function(a,b){var c;while((c=n.inArray(b,f,c))>-1)f.splice(c,1),h>=c&&h--}),this},has:function(a){return a?n.inArray(a,f)>-1:f.length>0},empty:function(){return f&&(f=[]),this},disable:function(){return e=g=[],f=c="",this},disabled:function(){return!f},lock:function(){return e=!0,c||j.disable(),this},locked:function(){return!!e},fireWith:function(a,c){return e||(c=c||[],c=[a,c.slice?c.slice():c],g.push(c),b||i()),this},fire:function(){return j.fireWith(this,arguments),this},fired:function(){return!!d}};return j},n.extend({Deferred:function(a){var b=[["resolve","done",n.Callbacks("once memory"),"resolved"],["reject","fail",n.Callbacks("once memory"),"rejected"],["notify","progress",n.Callbacks("memory")]],c="pending",d={state:function(){return c},always:function(){return e.done(arguments).fail(arguments),this},then:function(){var a=arguments;return n.Deferred(function(c){n.each(b,function(b,f){var g=n.isFunction(a[b])&&a[b];e[f[1]](function(){var a=g&&g.apply(this,arguments);a&&n.isFunction(a.promise)?a.promise().progress(c.notify).done(c.resolve).fail(c.reject):c[f[0]+"With"](this===d?c.promise():this,g?[a]:arguments)})}),a=null}).promise()},promise:function(a){return null!=a?n.extend(a,d):d}},e={};return d.pipe=d.then,n.each(b,function(a,f){var g=f[2],h=f[3];d[f[1]]=g.add,h&&g.add(function(){c=h},b[1^a][2].disable,b[2][2].lock),e[f[0]]=function(){return e[f[0]+"With"](this===e?d:this,arguments),this},e[f[0]+"With"]=g.fireWith}),d.promise(e),a&&a.call(e,e),e},when:function(a){var b=0,c=e.call(arguments),d=c.length,f=1!==d||a&&n.isFunction(a.promise)?d:0,g=1===f?a:n.Deferred(),h=function(a,b,c){return function(d){b[a]=this,c[a]=arguments.length>1?e.call(arguments):d,c===i?g.notifyWith(b,c):--f||g.resolveWith(b,c)}},i,j,k;if(d>1)for(i=new Array(d),j=new Array(d),k=new Array(d);d>b;b++)c[b]&&n.isFunction(c[b].promise)?c[b].promise().progress(h(b,j,i)).done(h(b,k,c)).fail(g.reject):--f;return f||g.resolveWith(k,c),g.promise()}});var I;n.fn.ready=function(a){return n.ready.promise().done(a),this},n.extend({isReady:!1,readyWait:1,holdReady:function(a){a?n.readyWait++:n.ready(!0)},ready:function(a){(a===!0?--n.readyWait:n.isReady)||(n.isReady=!0,a!==!0&&--n.readyWait>0||(I.resolveWith(d,[n]),n.fn.triggerHandler&&(n(d).triggerHandler("ready"),n(d).off("ready"))))}});function J(){d.addEventListener?(d.removeEventListener("DOMContentLoaded",K),a.removeEventListener("load",K)):(d.detachEvent("onreadystatechange",K),a.detachEvent("onload",K))}function K(){(d.addEventListener||"load"===a.event.type||"complete"===d.readyState)&&(J(),n.ready())}n.ready.promise=function(b){if(!I)if(I=n.Deferred(),"complete"===d.readyState||"loading"!==d.readyState&&!d.documentElement.doScroll)a.setTimeout(n.ready);else if(d.addEventListener)d.addEventListener("DOMContentLoaded",K),a.addEventListener("load",K);else{d.attachEvent("onreadystatechange",K),a.attachEvent("onload",K);var c=!1;try{c=null==a.frameElement&&d.documentElement}catch(e){}c&&c.doScroll&&!function f(){if(!n.isReady){try{c.doScroll("left")}catch(b){return a.setTimeout(f,50)}J(),n.ready()}}()}return I.promise(b)},n.ready.promise();var L;for(L in n(l))break;l.ownFirst="0"===L,l.inlineBlockNeedsLayout=!1,n(function(){var a,b,c,e;c=d.getElementsByTagName("body")[0],c&&c.style&&(b=d.createElement("div"),e=d.createElement("div"),e.style.cssText="position:absolute;border:0;width:0;height:0;top:0;left:-9999px",c.appendChild(e).appendChild(b),"undefined"!=typeof b.style.zoom&&(b.style.cssText="display:inline;margin:0;border:0;padding:1px;width:1px;zoom:1",l.inlineBlockNeedsLayout=a=3===b.offsetWidth,a&&(c.style.zoom=1)),c.removeChild(e))}),function(){var a=d.createElement("div");l.deleteExpando=!0;try{delete a.test}catch(b){l.deleteExpando=!1}a=null}();var M=function(a){var b=n.noData[(a.nodeName+" ").toLowerCase()],c=+a.nodeType||1;return 1!==c&&9!==c?!1:!b||b!==!0&&a.getAttribute("classid")===b},N=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,O=/([A-Z])/g;function P(a,b,c){if(void 0===c&&1===a.nodeType){var d="data-"+b.replace(O,"-$1").toLowerCase();if(c=a.getAttribute(d),"string"==typeof c){try{c="true"===c?!0:"false"===c?!1:"null"===c?null:+c+""===c?+c:N.test(c)?n.parseJSON(c):c}catch(e){}n.data(a,b,c)}else c=void 0;
}return c}function Q(a){var b;for(b in a)if(("data"!==b||!n.isEmptyObject(a[b]))&&"toJSON"!==b)return!1;return!0}function R(a,b,d,e){if(M(a)){var f,g,h=n.expando,i=a.nodeType,j=i?n.cache:a,k=i?a[h]:a[h]&&h;if(k&&j[k]&&(e||j[k].data)||void 0!==d||"string"!=typeof b)return k||(k=i?a[h]=c.pop()||n.guid++:h),j[k]||(j[k]=i?{}:{toJSON:n.noop}),"object"!=typeof b&&"function"!=typeof b||(e?j[k]=n.extend(j[k],b):j[k].data=n.extend(j[k].data,b)),g=j[k],e||(g.data||(g.data={}),g=g.data),void 0!==d&&(g[n.camelCase(b)]=d),"string"==typeof b?(f=g[b],null==f&&(f=g[n.camelCase(b)])):f=g,f}}function S(a,b,c){if(M(a)){var d,e,f=a.nodeType,g=f?n.cache:a,h=f?a[n.expando]:n.expando;if(g[h]){if(b&&(d=c?g[h]:g[h].data)){n.isArray(b)?b=b.concat(n.map(b,n.camelCase)):b in d?b=[b]:(b=n.camelCase(b),b=b in d?[b]:b.split(" ")),e=b.length;while(e--)delete d[b[e]];if(c?!Q(d):!n.isEmptyObject(d))return}(c||(delete g[h].data,Q(g[h])))&&(f?n.cleanData([a],!0):l.deleteExpando||g!=g.window?delete g[h]:g[h]=void 0)}}}n.extend({cache:{},noData:{"applet ":!0,"embed ":!0,"object ":"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"},hasData:function(a){return a=a.nodeType?n.cache[a[n.expando]]:a[n.expando],!!a&&!Q(a)},data:function(a,b,c){return R(a,b,c)},removeData:function(a,b){return S(a,b)},_data:function(a,b,c){return R(a,b,c,!0)},_removeData:function(a,b){return S(a,b,!0)}}),n.fn.extend({data:function(a,b){var c,d,e,f=this[0],g=f&&f.attributes;if(void 0===a){if(this.length&&(e=n.data(f),1===f.nodeType&&!n._data(f,"parsedAttrs"))){c=g.length;while(c--)g[c]&&(d=g[c].name,0===d.indexOf("data-")&&(d=n.camelCase(d.slice(5)),P(f,d,e[d])));n._data(f,"parsedAttrs",!0)}return e}return"object"==typeof a?this.each(function(){n.data(this,a)}):arguments.length>1?this.each(function(){n.data(this,a,b)}):f?P(f,a,n.data(f,a)):void 0},removeData:function(a){return this.each(function(){n.removeData(this,a)})}}),n.extend({queue:function(a,b,c){var d;return a?(b=(b||"fx")+"queue",d=n._data(a,b),c&&(!d||n.isArray(c)?d=n._data(a,b,n.makeArray(c)):d.push(c)),d||[]):void 0},dequeue:function(a,b){b=b||"fx";var c=n.queue(a,b),d=c.length,e=c.shift(),f=n._queueHooks(a,b),g=function(){n.dequeue(a,b)};"inprogress"===e&&(e=c.shift(),d--),e&&("fx"===b&&c.unshift("inprogress"),delete f.stop,e.call(a,g,f)),!d&&f&&f.empty.fire()},_queueHooks:function(a,b){var c=b+"queueHooks";return n._data(a,c)||n._data(a,c,{empty:n.Callbacks("once memory").add(function(){n._removeData(a,b+"queue"),n._removeData(a,c)})})}}),n.fn.extend({queue:function(a,b){var c=2;return"string"!=typeof a&&(b=a,a="fx",c--),arguments.length<c?n.queue(this[0],a):void 0===b?this:this.each(function(){var c=n.queue(this,a,b);n._queueHooks(this,a),"fx"===a&&"inprogress"!==c[0]&&n.dequeue(this,a)})},dequeue:function(a){return this.each(function(){n.dequeue(this,a)})},clearQueue:function(a){return this.queue(a||"fx",[])},promise:function(a,b){var c,d=1,e=n.Deferred(),f=this,g=this.length,h=function(){--d||e.resolveWith(f,[f])};"string"!=typeof a&&(b=a,a=void 0),a=a||"fx";while(g--)c=n._data(f[g],a+"queueHooks"),c&&c.empty&&(d++,c.empty.add(h));return h(),e.promise(b)}}),function(){var a;l.shrinkWrapBlocks=function(){if(null!=a)return a;a=!1;var b,c,e;return c=d.getElementsByTagName("body")[0],c&&c.style?(b=d.createElement("div"),e=d.createElement("div"),e.style.cssText="position:absolute;border:0;width:0;height:0;top:0;left:-9999px",c.appendChild(e).appendChild(b),"undefined"!=typeof b.style.zoom&&(b.style.cssText="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:1px;width:1px;zoom:1",b.appendChild(d.createElement("div")).style.width="5px",a=3!==b.offsetWidth),c.removeChild(e),a):void 0}}();var T=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,U=new RegExp("^(?:([+-])=|)("+T+")([a-z%]*)$","i"),V=["Top","Right","Bottom","Left"],W=function(a,b){return a=b||a,"none"===n.css(a,"display")||!n.contains(a.ownerDocument,a)};function X(a,b,c,d){var e,f=1,g=20,h=d?function(){return d.cur()}:function(){return n.css(a,b,"")},i=h(),j=c&&c[3]||(n.cssNumber[b]?"":"px"),k=(n.cssNumber[b]||"px"!==j&&+i)&&U.exec(n.css(a,b));if(k&&k[3]!==j){j=j||k[3],c=c||[],k=+i||1;do f=f||".5",k/=f,n.style(a,b,k+j);while(f!==(f=h()/i)&&1!==f&&--g)}return c&&(k=+k||+i||0,e=c[1]?k+(c[1]+1)*c[2]:+c[2],d&&(d.unit=j,d.start=k,d.end=e)),e}var Y=function(a,b,c,d,e,f,g){var h=0,i=a.length,j=null==c;if("object"===n.type(c)){e=!0;for(h in c)Y(a,b,h,c[h],!0,f,g)}else if(void 0!==d&&(e=!0,n.isFunction(d)||(g=!0),j&&(g?(b.call(a,d),b=null):(j=b,b=function(a,b,c){return j.call(n(a),c)})),b))for(;i>h;h++)b(a[h],c,g?d:d.call(a[h],h,b(a[h],c)));return e?a:j?b.call(a):i?b(a[0],c):f},Z=/^(?:checkbox|radio)$/i,$=/<([\w:-]+)/,_=/^$|\/(?:java|ecma)script/i,aa=/^\s+/,ba="abbr|article|aside|audio|bdi|canvas|data|datalist|details|dialog|figcaption|figure|footer|header|hgroup|main|mark|meter|nav|output|picture|progress|section|summary|template|time|video";function ca(a){var b=ba.split("|"),c=a.createDocumentFragment();if(c.createElement)while(b.length)c.createElement(b.pop());return c}!function(){var a=d.createElement("div"),b=d.createDocumentFragment(),c=d.createElement("input");a.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",l.leadingWhitespace=3===a.firstChild.nodeType,l.tbody=!a.getElementsByTagName("tbody").length,l.htmlSerialize=!!a.getElementsByTagName("link").length,l.html5Clone="<:nav></:nav>"!==d.createElement("nav").cloneNode(!0).outerHTML,c.type="checkbox",c.checked=!0,b.appendChild(c),l.appendChecked=c.checked,a.innerHTML="<textarea>x</textarea>",l.noCloneChecked=!!a.cloneNode(!0).lastChild.defaultValue,b.appendChild(a),c=d.createElement("input"),c.setAttribute("type","radio"),c.setAttribute("checked","checked"),c.setAttribute("name","t"),a.appendChild(c),l.checkClone=a.cloneNode(!0).cloneNode(!0).lastChild.checked,l.noCloneEvent=!!a.addEventListener,a[n.expando]=1,l.attributes=!a.getAttribute(n.expando)}();var da={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],area:[1,"<map>","</map>"],param:[1,"<object>","</object>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:l.htmlSerialize?[0,"",""]:[1,"X<div>","</div>"]};da.optgroup=da.option,da.tbody=da.tfoot=da.colgroup=da.caption=da.thead,da.th=da.td;function ea(a,b){var c,d,e=0,f="undefined"!=typeof a.getElementsByTagName?a.getElementsByTagName(b||"*"):"undefined"!=typeof a.querySelectorAll?a.querySelectorAll(b||"*"):void 0;if(!f)for(f=[],c=a.childNodes||a;null!=(d=c[e]);e++)!b||n.nodeName(d,b)?f.push(d):n.merge(f,ea(d,b));return void 0===b||b&&n.nodeName(a,b)?n.merge([a],f):f}function fa(a,b){for(var c,d=0;null!=(c=a[d]);d++)n._data(c,"globalEval",!b||n._data(b[d],"globalEval"))}var ga=/<|&#?\w+;/,ha=/<tbody/i;function ia(a){Z.test(a.type)&&(a.defaultChecked=a.checked)}function ja(a,b,c,d,e){for(var f,g,h,i,j,k,m,o=a.length,p=ca(b),q=[],r=0;o>r;r++)if(g=a[r],g||0===g)if("object"===n.type(g))n.merge(q,g.nodeType?[g]:g);else if(ga.test(g)){i=i||p.appendChild(b.createElement("div")),j=($.exec(g)||["",""])[1].toLowerCase(),m=da[j]||da._default,i.innerHTML=m[1]+n.htmlPrefilter(g)+m[2],f=m[0];while(f--)i=i.lastChild;if(!l.leadingWhitespace&&aa.test(g)&&q.push(b.createTextNode(aa.exec(g)[0])),!l.tbody){g="table"!==j||ha.test(g)?"<table>"!==m[1]||ha.test(g)?0:i:i.firstChild,f=g&&g.childNodes.length;while(f--)n.nodeName(k=g.childNodes[f],"tbody")&&!k.childNodes.length&&g.removeChild(k)}n.merge(q,i.childNodes),i.textContent="";while(i.firstChild)i.removeChild(i.firstChild);i=p.lastChild}else q.push(b.createTextNode(g));i&&p.removeChild(i),l.appendChecked||n.grep(ea(q,"input"),ia),r=0;while(g=q[r++])if(d&&n.inArray(g,d)>-1)e&&e.push(g);else if(h=n.contains(g.ownerDocument,g),i=ea(p.appendChild(g),"script"),h&&fa(i),c){f=0;while(g=i[f++])_.test(g.type||"")&&c.push(g)}return i=null,p}!function(){var b,c,e=d.createElement("div");for(b in{submit:!0,change:!0,focusin:!0})c="on"+b,(l[b]=c in a)||(e.setAttribute(c,"t"),l[b]=e.attributes[c].expando===!1);e=null}();var ka=/^(?:input|select|textarea)$/i,la=/^key/,ma=/^(?:mouse|pointer|contextmenu|drag|drop)|click/,na=/^(?:focusinfocus|focusoutblur)$/,oa=/^([^.]*)(?:\.(.+)|)/;function pa(){return!0}function qa(){return!1}function ra(){try{return d.activeElement}catch(a){}}function sa(a,b,c,d,e,f){var g,h;if("object"==typeof b){"string"!=typeof c&&(d=d||c,c=void 0);for(h in b)sa(a,h,c,d,b[h],f);return a}if(null==d&&null==e?(e=c,d=c=void 0):null==e&&("string"==typeof c?(e=d,d=void 0):(e=d,d=c,c=void 0)),e===!1)e=qa;else if(!e)return a;return 1===f&&(g=e,e=function(a){return n().off(a),g.apply(this,arguments)},e.guid=g.guid||(g.guid=n.guid++)),a.each(function(){n.event.add(this,b,e,d,c)})}n.event={global:{},add:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n._data(a);if(r){c.handler&&(i=c,c=i.handler,e=i.selector),c.guid||(c.guid=n.guid++),(g=r.events)||(g=r.events={}),(k=r.handle)||(k=r.handle=function(a){return"undefined"==typeof n||a&&n.event.triggered===a.type?void 0:n.event.dispatch.apply(k.elem,arguments)},k.elem=a),b=(b||"").match(G)||[""],h=b.length;while(h--)f=oa.exec(b[h])||[],o=q=f[1],p=(f[2]||"").split(".").sort(),o&&(j=n.event.special[o]||{},o=(e?j.delegateType:j.bindType)||o,j=n.event.special[o]||{},l=n.extend({type:o,origType:q,data:d,handler:c,guid:c.guid,selector:e,needsContext:e&&n.expr.match.needsContext.test(e),namespace:p.join(".")},i),(m=g[o])||(m=g[o]=[],m.delegateCount=0,j.setup&&j.setup.call(a,d,p,k)!==!1||(a.addEventListener?a.addEventListener(o,k,!1):a.attachEvent&&a.attachEvent("on"+o,k))),j.add&&(j.add.call(a,l),l.handler.guid||(l.handler.guid=c.guid)),e?m.splice(m.delegateCount++,0,l):m.push(l),n.event.global[o]=!0);a=null}},remove:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n.hasData(a)&&n._data(a);if(r&&(k=r.events)){b=(b||"").match(G)||[""],j=b.length;while(j--)if(h=oa.exec(b[j])||[],o=q=h[1],p=(h[2]||"").split(".").sort(),o){l=n.event.special[o]||{},o=(d?l.delegateType:l.bindType)||o,m=k[o]||[],h=h[2]&&new RegExp("(^|\\.)"+p.join("\\.(?:.*\\.|)")+"(\\.|$)"),i=f=m.length;while(f--)g=m[f],!e&&q!==g.origType||c&&c.guid!==g.guid||h&&!h.test(g.namespace)||d&&d!==g.selector&&("**"!==d||!g.selector)||(m.splice(f,1),g.selector&&m.delegateCount--,l.remove&&l.remove.call(a,g));i&&!m.length&&(l.teardown&&l.teardown.call(a,p,r.handle)!==!1||n.removeEvent(a,o,r.handle),delete k[o])}else for(o in k)n.event.remove(a,o+b[j],c,d,!0);n.isEmptyObject(k)&&(delete r.handle,n._removeData(a,"events"))}},trigger:function(b,c,e,f){var g,h,i,j,l,m,o,p=[e||d],q=k.call(b,"type")?b.type:b,r=k.call(b,"namespace")?b.namespace.split("."):[];if(i=m=e=e||d,3!==e.nodeType&&8!==e.nodeType&&!na.test(q+n.event.triggered)&&(q.indexOf(".")>-1&&(r=q.split("."),q=r.shift(),r.sort()),h=q.indexOf(":")<0&&"on"+q,b=b[n.expando]?b:new n.Event(q,"object"==typeof b&&b),b.isTrigger=f?2:3,b.namespace=r.join("."),b.rnamespace=b.namespace?new RegExp("(^|\\.)"+r.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,b.result=void 0,b.target||(b.target=e),c=null==c?[b]:n.makeArray(c,[b]),l=n.event.special[q]||{},f||!l.trigger||l.trigger.apply(e,c)!==!1)){if(!f&&!l.noBubble&&!n.isWindow(e)){for(j=l.delegateType||q,na.test(j+q)||(i=i.parentNode);i;i=i.parentNode)p.push(i),m=i;m===(e.ownerDocument||d)&&p.push(m.defaultView||m.parentWindow||a)}o=0;while((i=p[o++])&&!b.isPropagationStopped())b.type=o>1?j:l.bindType||q,g=(n._data(i,"events")||{})[b.type]&&n._data(i,"handle"),g&&g.apply(i,c),g=h&&i[h],g&&g.apply&&M(i)&&(b.result=g.apply(i,c),b.result===!1&&b.preventDefault());if(b.type=q,!f&&!b.isDefaultPrevented()&&(!l._default||l._default.apply(p.pop(),c)===!1)&&M(e)&&h&&e[q]&&!n.isWindow(e)){m=e[h],m&&(e[h]=null),n.event.triggered=q;try{e[q]()}catch(s){}n.event.triggered=void 0,m&&(e[h]=m)}return b.result}},dispatch:function(a){a=n.event.fix(a);var b,c,d,f,g,h=[],i=e.call(arguments),j=(n._data(this,"events")||{})[a.type]||[],k=n.event.special[a.type]||{};if(i[0]=a,a.delegateTarget=this,!k.preDispatch||k.preDispatch.call(this,a)!==!1){h=n.event.handlers.call(this,a,j),b=0;while((f=h[b++])&&!a.isPropagationStopped()){a.currentTarget=f.elem,c=0;while((g=f.handlers[c++])&&!a.isImmediatePropagationStopped())a.rnamespace&&!a.rnamespace.test(g.namespace)||(a.handleObj=g,a.data=g.data,d=((n.event.special[g.origType]||{}).handle||g.handler).apply(f.elem,i),void 0!==d&&(a.result=d)===!1&&(a.preventDefault(),a.stopPropagation()))}return k.postDispatch&&k.postDispatch.call(this,a),a.result}},handlers:function(a,b){var c,d,e,f,g=[],h=b.delegateCount,i=a.target;if(h&&i.nodeType&&("click"!==a.type||isNaN(a.button)||a.button<1))for(;i!=this;i=i.parentNode||this)if(1===i.nodeType&&(i.disabled!==!0||"click"!==a.type)){for(d=[],c=0;h>c;c++)f=b[c],e=f.selector+" ",void 0===d[e]&&(d[e]=f.needsContext?n(e,this).index(i)>-1:n.find(e,this,null,[i]).length),d[e]&&d.push(f);d.length&&g.push({elem:i,handlers:d})}return h<b.length&&g.push({elem:this,handlers:b.slice(h)}),g},fix:function(a){if(a[n.expando])return a;var b,c,e,f=a.type,g=a,h=this.fixHooks[f];h||(this.fixHooks[f]=h=ma.test(f)?this.mouseHooks:la.test(f)?this.keyHooks:{}),e=h.props?this.props.concat(h.props):this.props,a=new n.Event(g),b=e.length;while(b--)c=e[b],a[c]=g[c];return a.target||(a.target=g.srcElement||d),3===a.target.nodeType&&(a.target=a.target.parentNode),a.metaKey=!!a.metaKey,h.filter?h.filter(a,g):a},props:"altKey bubbles cancelable ctrlKey currentTarget detail eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(a,b){return null==a.which&&(a.which=null!=b.charCode?b.charCode:b.keyCode),a}},mouseHooks:{props:"button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(a,b){var c,e,f,g=b.button,h=b.fromElement;return null==a.pageX&&null!=b.clientX&&(e=a.target.ownerDocument||d,f=e.documentElement,c=e.body,a.pageX=b.clientX+(f&&f.scrollLeft||c&&c.scrollLeft||0)-(f&&f.clientLeft||c&&c.clientLeft||0),a.pageY=b.clientY+(f&&f.scrollTop||c&&c.scrollTop||0)-(f&&f.clientTop||c&&c.clientTop||0)),!a.relatedTarget&&h&&(a.relatedTarget=h===a.target?b.toElement:h),a.which||void 0===g||(a.which=1&g?1:2&g?3:4&g?2:0),a}},special:{load:{noBubble:!0},focus:{trigger:function(){if(this!==ra()&&this.focus)try{return this.focus(),!1}catch(a){}},delegateType:"focusin"},blur:{trigger:function(){return this===ra()&&this.blur?(this.blur(),!1):void 0},delegateType:"focusout"},click:{trigger:function(){return n.nodeName(this,"input")&&"checkbox"===this.type&&this.click?(this.click(),!1):void 0},_default:function(a){return n.nodeName(a.target,"a")}},beforeunload:{postDispatch:function(a){void 0!==a.result&&a.originalEvent&&(a.originalEvent.returnValue=a.result)}}},simulate:function(a,b,c){var d=n.extend(new n.Event,c,{type:a,isSimulated:!0});n.event.trigger(d,null,b),d.isDefaultPrevented()&&c.preventDefault()}},n.removeEvent=d.removeEventListener?function(a,b,c){a.removeEventListener&&a.removeEventListener(b,c)}:function(a,b,c){var d="on"+b;a.detachEvent&&("undefined"==typeof a[d]&&(a[d]=null),a.detachEvent(d,c))},n.Event=function(a,b){return this instanceof n.Event?(a&&a.type?(this.originalEvent=a,this.type=a.type,this.isDefaultPrevented=a.defaultPrevented||void 0===a.defaultPrevented&&a.returnValue===!1?pa:qa):this.type=a,b&&n.extend(this,b),this.timeStamp=a&&a.timeStamp||n.now(),void(this[n.expando]=!0)):new n.Event(a,b)},n.Event.prototype={constructor:n.Event,isDefaultPrevented:qa,isPropagationStopped:qa,isImmediatePropagationStopped:qa,preventDefault:function(){var a=this.originalEvent;this.isDefaultPrevented=pa,a&&(a.preventDefault?a.preventDefault():a.returnValue=!1)},stopPropagation:function(){var a=this.originalEvent;this.isPropagationStopped=pa,a&&!this.isSimulated&&(a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)},stopImmediatePropagation:function(){var a=this.originalEvent;this.isImmediatePropagationStopped=pa,a&&a.stopImmediatePropagation&&a.stopImmediatePropagation(),this.stopPropagation()}},n.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(a,b){n.event.special[a]={delegateType:b,bindType:b,handle:function(a){var c,d=this,e=a.relatedTarget,f=a.handleObj;return e&&(e===d||n.contains(d,e))||(a.type=f.origType,c=f.handler.apply(this,arguments),a.type=b),c}}}),l.submit||(n.event.special.submit={setup:function(){return n.nodeName(this,"form")?!1:void n.event.add(this,"click._submit keypress._submit",function(a){var b=a.target,c=n.nodeName(b,"input")||n.nodeName(b,"button")?n.prop(b,"form"):void 0;c&&!n._data(c,"submit")&&(n.event.add(c,"submit._submit",function(a){a._submitBubble=!0}),n._data(c,"submit",!0))})},postDispatch:function(a){a._submitBubble&&(delete a._submitBubble,this.parentNode&&!a.isTrigger&&n.event.simulate("submit",this.parentNode,a))},teardown:function(){return n.nodeName(this,"form")?!1:void n.event.remove(this,"._submit")}}),l.change||(n.event.special.change={setup:function(){return ka.test(this.nodeName)?("checkbox"!==this.type&&"radio"!==this.type||(n.event.add(this,"propertychange._change",function(a){"checked"===a.originalEvent.propertyName&&(this._justChanged=!0)}),n.event.add(this,"click._change",function(a){this._justChanged&&!a.isTrigger&&(this._justChanged=!1),n.event.simulate("change",this,a)})),!1):void n.event.add(this,"beforeactivate._change",function(a){var b=a.target;ka.test(b.nodeName)&&!n._data(b,"change")&&(n.event.add(b,"change._change",function(a){!this.parentNode||a.isSimulated||a.isTrigger||n.event.simulate("change",this.parentNode,a)}),n._data(b,"change",!0))})},handle:function(a){var b=a.target;return this!==b||a.isSimulated||a.isTrigger||"radio"!==b.type&&"checkbox"!==b.type?a.handleObj.handler.apply(this,arguments):void 0},teardown:function(){return n.event.remove(this,"._change"),!ka.test(this.nodeName)}}),l.focusin||n.each({focus:"focusin",blur:"focusout"},function(a,b){var c=function(a){n.event.simulate(b,a.target,n.event.fix(a))};n.event.special[b]={setup:function(){var d=this.ownerDocument||this,e=n._data(d,b);e||d.addEventListener(a,c,!0),n._data(d,b,(e||0)+1)},teardown:function(){var d=this.ownerDocument||this,e=n._data(d,b)-1;e?n._data(d,b,e):(d.removeEventListener(a,c,!0),n._removeData(d,b))}}}),n.fn.extend({on:function(a,b,c,d){return sa(this,a,b,c,d)},one:function(a,b,c,d){return sa(this,a,b,c,d,1)},off:function(a,b,c){var d,e;if(a&&a.preventDefault&&a.handleObj)return d=a.handleObj,n(a.delegateTarget).off(d.namespace?d.origType+"."+d.namespace:d.origType,d.selector,d.handler),this;if("object"==typeof a){for(e in a)this.off(e,b,a[e]);return this}return b!==!1&&"function"!=typeof b||(c=b,b=void 0),c===!1&&(c=qa),this.each(function(){n.event.remove(this,a,c,b)})},trigger:function(a,b){return this.each(function(){n.event.trigger(a,b,this)})},triggerHandler:function(a,b){var c=this[0];return c?n.event.trigger(a,b,c,!0):void 0}});var ta=/ jQuery\d+="(?:null|\d+)"/g,ua=new RegExp("<(?:"+ba+")[\\s/>]","i"),va=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi,wa=/<script|<style|<link/i,xa=/checked\s*(?:[^=]|=\s*.checked.)/i,ya=/^true\/(.*)/,za=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,Aa=ca(d),Ba=Aa.appendChild(d.createElement("div"));function Ca(a,b){return n.nodeName(a,"table")&&n.nodeName(11!==b.nodeType?b:b.firstChild,"tr")?a.getElementsByTagName("tbody")[0]||a.appendChild(a.ownerDocument.createElement("tbody")):a}function Da(a){return a.type=(null!==n.find.attr(a,"type"))+"/"+a.type,a}function Ea(a){var b=ya.exec(a.type);return b?a.type=b[1]:a.removeAttribute("type"),a}function Fa(a,b){if(1===b.nodeType&&n.hasData(a)){var c,d,e,f=n._data(a),g=n._data(b,f),h=f.events;if(h){delete g.handle,g.events={};for(c in h)for(d=0,e=h[c].length;e>d;d++)n.event.add(b,c,h[c][d])}g.data&&(g.data=n.extend({},g.data))}}function Ga(a,b){var c,d,e;if(1===b.nodeType){if(c=b.nodeName.toLowerCase(),!l.noCloneEvent&&b[n.expando]){e=n._data(b);for(d in e.events)n.removeEvent(b,d,e.handle);b.removeAttribute(n.expando)}"script"===c&&b.text!==a.text?(Da(b).text=a.text,Ea(b)):"object"===c?(b.parentNode&&(b.outerHTML=a.outerHTML),l.html5Clone&&a.innerHTML&&!n.trim(b.innerHTML)&&(b.innerHTML=a.innerHTML)):"input"===c&&Z.test(a.type)?(b.defaultChecked=b.checked=a.checked,b.value!==a.value&&(b.value=a.value)):"option"===c?b.defaultSelected=b.selected=a.defaultSelected:"input"!==c&&"textarea"!==c||(b.defaultValue=a.defaultValue)}}function Ha(a,b,c,d){b=f.apply([],b);var e,g,h,i,j,k,m=0,o=a.length,p=o-1,q=b[0],r=n.isFunction(q);if(r||o>1&&"string"==typeof q&&!l.checkClone&&xa.test(q))return a.each(function(e){var f=a.eq(e);r&&(b[0]=q.call(this,e,f.html())),Ha(f,b,c,d)});if(o&&(k=ja(b,a[0].ownerDocument,!1,a,d),e=k.firstChild,1===k.childNodes.length&&(k=e),e||d)){for(i=n.map(ea(k,"script"),Da),h=i.length;o>m;m++)g=k,m!==p&&(g=n.clone(g,!0,!0),h&&n.merge(i,ea(g,"script"))),c.call(a[m],g,m);if(h)for(j=i[i.length-1].ownerDocument,n.map(i,Ea),m=0;h>m;m++)g=i[m],_.test(g.type||"")&&!n._data(g,"globalEval")&&n.contains(j,g)&&(g.src?n._evalUrl&&n._evalUrl(g.src):n.globalEval((g.text||g.textContent||g.innerHTML||"").replace(za,"")));k=e=null}return a}function Ia(a,b,c){for(var d,e=b?n.filter(b,a):a,f=0;null!=(d=e[f]);f++)c||1!==d.nodeType||n.cleanData(ea(d)),d.parentNode&&(c&&n.contains(d.ownerDocument,d)&&fa(ea(d,"script")),d.parentNode.removeChild(d));return a}n.extend({htmlPrefilter:function(a){return a.replace(va,"<$1></$2>")},clone:function(a,b,c){var d,e,f,g,h,i=n.contains(a.ownerDocument,a);if(l.html5Clone||n.isXMLDoc(a)||!ua.test("<"+a.nodeName+">")?f=a.cloneNode(!0):(Ba.innerHTML=a.outerHTML,Ba.removeChild(f=Ba.firstChild)),!(l.noCloneEvent&&l.noCloneChecked||1!==a.nodeType&&11!==a.nodeType||n.isXMLDoc(a)))for(d=ea(f),h=ea(a),g=0;null!=(e=h[g]);++g)d[g]&&Ga(e,d[g]);if(b)if(c)for(h=h||ea(a),d=d||ea(f),g=0;null!=(e=h[g]);g++)Fa(e,d[g]);else Fa(a,f);return d=ea(f,"script"),d.length>0&&fa(d,!i&&ea(a,"script")),d=h=e=null,f},cleanData:function(a,b){for(var d,e,f,g,h=0,i=n.expando,j=n.cache,k=l.attributes,m=n.event.special;null!=(d=a[h]);h++)if((b||M(d))&&(f=d[i],g=f&&j[f])){if(g.events)for(e in g.events)m[e]?n.event.remove(d,e):n.removeEvent(d,e,g.handle);j[f]&&(delete j[f],k||"undefined"==typeof d.removeAttribute?d[i]=void 0:d.removeAttribute(i),c.push(f))}}}),n.fn.extend({domManip:Ha,detach:function(a){return Ia(this,a,!0)},remove:function(a){return Ia(this,a)},text:function(a){return Y(this,function(a){return void 0===a?n.text(this):this.empty().append((this[0]&&this[0].ownerDocument||d).createTextNode(a))},null,a,arguments.length)},append:function(){return Ha(this,arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=Ca(this,a);b.appendChild(a)}})},prepend:function(){return Ha(this,arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=Ca(this,a);b.insertBefore(a,b.firstChild)}})},before:function(){return Ha(this,arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this)})},after:function(){return Ha(this,arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this.nextSibling)})},empty:function(){for(var a,b=0;null!=(a=this[b]);b++){1===a.nodeType&&n.cleanData(ea(a,!1));while(a.firstChild)a.removeChild(a.firstChild);a.options&&n.nodeName(a,"select")&&(a.options.length=0)}return this},clone:function(a,b){return a=null==a?!1:a,b=null==b?a:b,this.map(function(){return n.clone(this,a,b)})},html:function(a){return Y(this,function(a){var b=this[0]||{},c=0,d=this.length;if(void 0===a)return 1===b.nodeType?b.innerHTML.replace(ta,""):void 0;if("string"==typeof a&&!wa.test(a)&&(l.htmlSerialize||!ua.test(a))&&(l.leadingWhitespace||!aa.test(a))&&!da[($.exec(a)||["",""])[1].toLowerCase()]){a=n.htmlPrefilter(a);try{for(;d>c;c++)b=this[c]||{},1===b.nodeType&&(n.cleanData(ea(b,!1)),b.innerHTML=a);b=0}catch(e){}}b&&this.empty().append(a)},null,a,arguments.length)},replaceWith:function(){var a=[];return Ha(this,arguments,function(b){var c=this.parentNode;n.inArray(this,a)<0&&(n.cleanData(ea(this)),c&&c.replaceChild(b,this))},a)}}),n.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(a,b){n.fn[a]=function(a){for(var c,d=0,e=[],f=n(a),h=f.length-1;h>=d;d++)c=d===h?this:this.clone(!0),n(f[d])[b](c),g.apply(e,c.get());return this.pushStack(e)}});var Ja,Ka={HTML:"block",BODY:"block"};function La(a,b){var c=n(b.createElement(a)).appendTo(b.body),d=n.css(c[0],"display");return c.detach(),d}function Ma(a){var b=d,c=Ka[a];return c||(c=La(a,b),"none"!==c&&c||(Ja=(Ja||n("<iframe frameborder='0' width='0' height='0'/>")).appendTo(b.documentElement),b=(Ja[0].contentWindow||Ja[0].contentDocument).document,b.write(),b.close(),c=La(a,b),Ja.detach()),Ka[a]=c),c}var Na=/^margin/,Oa=new RegExp("^("+T+")(?!px)[a-z%]+$","i"),Pa=function(a,b,c,d){var e,f,g={};for(f in b)g[f]=a.style[f],a.style[f]=b[f];e=c.apply(a,d||[]);for(f in b)a.style[f]=g[f];return e},Qa=d.documentElement;!function(){var b,c,e,f,g,h,i=d.createElement("div"),j=d.createElement("div");if(j.style){j.style.cssText="float:left;opacity:.5",l.opacity="0.5"===j.style.opacity,l.cssFloat=!!j.style.cssFloat,j.style.backgroundClip="content-box",j.cloneNode(!0).style.backgroundClip="",l.clearCloneStyle="content-box"===j.style.backgroundClip,i=d.createElement("div"),i.style.cssText="border:0;width:8px;height:0;top:0;left:-9999px;padding:0;margin-top:1px;position:absolute",j.innerHTML="",i.appendChild(j),l.boxSizing=""===j.style.boxSizing||""===j.style.MozBoxSizing||""===j.style.WebkitBoxSizing,n.extend(l,{reliableHiddenOffsets:function(){return null==b&&k(),f},boxSizingReliable:function(){return null==b&&k(),e},pixelMarginRight:function(){return null==b&&k(),c},pixelPosition:function(){return null==b&&k(),b},reliableMarginRight:function(){return null==b&&k(),g},reliableMarginLeft:function(){return null==b&&k(),h}});function k(){var k,l,m=d.documentElement;m.appendChild(i),j.style.cssText="-webkit-box-sizing:border-box;box-sizing:border-box;position:relative;display:block;margin:auto;border:1px;padding:1px;top:1%;width:50%",b=e=h=!1,c=g=!0,a.getComputedStyle&&(l=a.getComputedStyle(j),b="1%"!==(l||{}).top,h="2px"===(l||{}).marginLeft,e="4px"===(l||{width:"4px"}).width,j.style.marginRight="50%",c="4px"===(l||{marginRight:"4px"}).marginRight,k=j.appendChild(d.createElement("div")),k.style.cssText=j.style.cssText="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:0",k.style.marginRight=k.style.width="0",j.style.width="1px",g=!parseFloat((a.getComputedStyle(k)||{}).marginRight),j.removeChild(k)),j.style.display="none",f=0===j.getClientRects().length,f&&(j.style.display="",j.innerHTML="<table><tr><td></td><td>t</td></tr></table>",j.childNodes[0].style.borderCollapse="separate",k=j.getElementsByTagName("td"),k[0].style.cssText="margin:0;border:0;padding:0;display:none",f=0===k[0].offsetHeight,f&&(k[0].style.display="",k[1].style.display="none",f=0===k[0].offsetHeight)),m.removeChild(i)}}}();var Ra,Sa,Ta=/^(top|right|bottom|left)$/;a.getComputedStyle?(Ra=function(b){var c=b.ownerDocument.defaultView;return c&&c.opener||(c=a),c.getComputedStyle(b)},Sa=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Ra(a),g=c?c.getPropertyValue(b)||c[b]:void 0,""!==g&&void 0!==g||n.contains(a.ownerDocument,a)||(g=n.style(a,b)),c&&!l.pixelMarginRight()&&Oa.test(g)&&Na.test(b)&&(d=h.width,e=h.minWidth,f=h.maxWidth,h.minWidth=h.maxWidth=h.width=g,g=c.width,h.width=d,h.minWidth=e,h.maxWidth=f),void 0===g?g:g+""}):Qa.currentStyle&&(Ra=function(a){return a.currentStyle},Sa=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Ra(a),g=c?c[b]:void 0,null==g&&h&&h[b]&&(g=h[b]),Oa.test(g)&&!Ta.test(b)&&(d=h.left,e=a.runtimeStyle,f=e&&e.left,f&&(e.left=a.currentStyle.left),h.left="fontSize"===b?"1em":g,g=h.pixelLeft+"px",h.left=d,f&&(e.left=f)),void 0===g?g:g+""||"auto"});function Ua(a,b){return{get:function(){return a()?void delete this.get:(this.get=b).apply(this,arguments)}}}var Va=/alpha\([^)]*\)/i,Wa=/opacity\s*=\s*([^)]*)/i,Xa=/^(none|table(?!-c[ea]).+)/,Ya=new RegExp("^("+T+")(.*)$","i"),Za={position:"absolute",visibility:"hidden",display:"block"},$a={letterSpacing:"0",fontWeight:"400"},_a=["Webkit","O","Moz","ms"],ab=d.createElement("div").style;function bb(a){if(a in ab)return a;var b=a.charAt(0).toUpperCase()+a.slice(1),c=_a.length;while(c--)if(a=_a[c]+b,a in ab)return a}function cb(a,b){for(var c,d,e,f=[],g=0,h=a.length;h>g;g++)d=a[g],d.style&&(f[g]=n._data(d,"olddisplay"),c=d.style.display,b?(f[g]||"none"!==c||(d.style.display=""),""===d.style.display&&W(d)&&(f[g]=n._data(d,"olddisplay",Ma(d.nodeName)))):(e=W(d),(c&&"none"!==c||!e)&&n._data(d,"olddisplay",e?c:n.css(d,"display"))));for(g=0;h>g;g++)d=a[g],d.style&&(b&&"none"!==d.style.display&&""!==d.style.display||(d.style.display=b?f[g]||"":"none"));return a}function db(a,b,c){var d=Ya.exec(b);return d?Math.max(0,d[1]-(c||0))+(d[2]||"px"):b}function eb(a,b,c,d,e){for(var f=c===(d?"border":"content")?4:"width"===b?1:0,g=0;4>f;f+=2)"margin"===c&&(g+=n.css(a,c+V[f],!0,e)),d?("content"===c&&(g-=n.css(a,"padding"+V[f],!0,e)),"margin"!==c&&(g-=n.css(a,"border"+V[f]+"Width",!0,e))):(g+=n.css(a,"padding"+V[f],!0,e),"padding"!==c&&(g+=n.css(a,"border"+V[f]+"Width",!0,e)));return g}function fb(a,b,c){var d=!0,e="width"===b?a.offsetWidth:a.offsetHeight,f=Ra(a),g=l.boxSizing&&"border-box"===n.css(a,"boxSizing",!1,f);if(0>=e||null==e){if(e=Sa(a,b,f),(0>e||null==e)&&(e=a.style[b]),Oa.test(e))return e;d=g&&(l.boxSizingReliable()||e===a.style[b]),e=parseFloat(e)||0}return e+eb(a,b,c||(g?"border":"content"),d,f)+"px"}n.extend({cssHooks:{opacity:{get:function(a,b){if(b){var c=Sa(a,"opacity");return""===c?"1":c}}}},cssNumber:{animationIterationCount:!0,columnCount:!0,fillOpacity:!0,flexGrow:!0,flexShrink:!0,fontWeight:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":l.cssFloat?"cssFloat":"styleFloat"},style:function(a,b,c,d){if(a&&3!==a.nodeType&&8!==a.nodeType&&a.style){var e,f,g,h=n.camelCase(b),i=a.style;if(b=n.cssProps[h]||(n.cssProps[h]=bb(h)||h),g=n.cssHooks[b]||n.cssHooks[h],void 0===c)return g&&"get"in g&&void 0!==(e=g.get(a,!1,d))?e:i[b];if(f=typeof c,"string"===f&&(e=U.exec(c))&&e[1]&&(c=X(a,b,e),f="number"),null!=c&&c===c&&("number"===f&&(c+=e&&e[3]||(n.cssNumber[h]?"":"px")),l.clearCloneStyle||""!==c||0!==b.indexOf("background")||(i[b]="inherit"),!(g&&"set"in g&&void 0===(c=g.set(a,c,d)))))try{i[b]=c}catch(j){}}},css:function(a,b,c,d){var e,f,g,h=n.camelCase(b);return b=n.cssProps[h]||(n.cssProps[h]=bb(h)||h),g=n.cssHooks[b]||n.cssHooks[h],g&&"get"in g&&(f=g.get(a,!0,c)),void 0===f&&(f=Sa(a,b,d)),"normal"===f&&b in $a&&(f=$a[b]),""===c||c?(e=parseFloat(f),c===!0||isFinite(e)?e||0:f):f}}),n.each(["height","width"],function(a,b){n.cssHooks[b]={get:function(a,c,d){return c?Xa.test(n.css(a,"display"))&&0===a.offsetWidth?Pa(a,Za,function(){return fb(a,b,d)}):fb(a,b,d):void 0},set:function(a,c,d){var e=d&&Ra(a);return db(a,c,d?eb(a,b,d,l.boxSizing&&"border-box"===n.css(a,"boxSizing",!1,e),e):0)}}}),l.opacity||(n.cssHooks.opacity={get:function(a,b){return Wa.test((b&&a.currentStyle?a.currentStyle.filter:a.style.filter)||"")?.01*parseFloat(RegExp.$1)+"":b?"1":""},set:function(a,b){var c=a.style,d=a.currentStyle,e=n.isNumeric(b)?"alpha(opacity="+100*b+")":"",f=d&&d.filter||c.filter||"";c.zoom=1,(b>=1||""===b)&&""===n.trim(f.replace(Va,""))&&c.removeAttribute&&(c.removeAttribute("filter"),""===b||d&&!d.filter)||(c.filter=Va.test(f)?f.replace(Va,e):f+" "+e)}}),n.cssHooks.marginRight=Ua(l.reliableMarginRight,function(a,b){return b?Pa(a,{display:"inline-block"},Sa,[a,"marginRight"]):void 0}),n.cssHooks.marginLeft=Ua(l.reliableMarginLeft,function(a,b){return b?(parseFloat(Sa(a,"marginLeft"))||(n.contains(a.ownerDocument,a)?a.getBoundingClientRect().left-Pa(a,{
marginLeft:0},function(){return a.getBoundingClientRect().left}):0))+"px":void 0}),n.each({margin:"",padding:"",border:"Width"},function(a,b){n.cssHooks[a+b]={expand:function(c){for(var d=0,e={},f="string"==typeof c?c.split(" "):[c];4>d;d++)e[a+V[d]+b]=f[d]||f[d-2]||f[0];return e}},Na.test(a)||(n.cssHooks[a+b].set=db)}),n.fn.extend({css:function(a,b){return Y(this,function(a,b,c){var d,e,f={},g=0;if(n.isArray(b)){for(d=Ra(a),e=b.length;e>g;g++)f[b[g]]=n.css(a,b[g],!1,d);return f}return void 0!==c?n.style(a,b,c):n.css(a,b)},a,b,arguments.length>1)},show:function(){return cb(this,!0)},hide:function(){return cb(this)},toggle:function(a){return"boolean"==typeof a?a?this.show():this.hide():this.each(function(){W(this)?n(this).show():n(this).hide()})}});function gb(a,b,c,d,e){return new gb.prototype.init(a,b,c,d,e)}n.Tween=gb,gb.prototype={constructor:gb,init:function(a,b,c,d,e,f){this.elem=a,this.prop=c,this.easing=e||n.easing._default,this.options=b,this.start=this.now=this.cur(),this.end=d,this.unit=f||(n.cssNumber[c]?"":"px")},cur:function(){var a=gb.propHooks[this.prop];return a&&a.get?a.get(this):gb.propHooks._default.get(this)},run:function(a){var b,c=gb.propHooks[this.prop];return this.options.duration?this.pos=b=n.easing[this.easing](a,this.options.duration*a,0,1,this.options.duration):this.pos=b=a,this.now=(this.end-this.start)*b+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),c&&c.set?c.set(this):gb.propHooks._default.set(this),this}},gb.prototype.init.prototype=gb.prototype,gb.propHooks={_default:{get:function(a){var b;return 1!==a.elem.nodeType||null!=a.elem[a.prop]&&null==a.elem.style[a.prop]?a.elem[a.prop]:(b=n.css(a.elem,a.prop,""),b&&"auto"!==b?b:0)},set:function(a){n.fx.step[a.prop]?n.fx.step[a.prop](a):1!==a.elem.nodeType||null==a.elem.style[n.cssProps[a.prop]]&&!n.cssHooks[a.prop]?a.elem[a.prop]=a.now:n.style(a.elem,a.prop,a.now+a.unit)}}},gb.propHooks.scrollTop=gb.propHooks.scrollLeft={set:function(a){a.elem.nodeType&&a.elem.parentNode&&(a.elem[a.prop]=a.now)}},n.easing={linear:function(a){return a},swing:function(a){return.5-Math.cos(a*Math.PI)/2},_default:"swing"},n.fx=gb.prototype.init,n.fx.step={};var hb,ib,jb=/^(?:toggle|show|hide)$/,kb=/queueHooks$/;function lb(){return a.setTimeout(function(){hb=void 0}),hb=n.now()}function mb(a,b){var c,d={height:a},e=0;for(b=b?1:0;4>e;e+=2-b)c=V[e],d["margin"+c]=d["padding"+c]=a;return b&&(d.opacity=d.width=a),d}function nb(a,b,c){for(var d,e=(qb.tweeners[b]||[]).concat(qb.tweeners["*"]),f=0,g=e.length;g>f;f++)if(d=e[f].call(c,b,a))return d}function ob(a,b,c){var d,e,f,g,h,i,j,k,m=this,o={},p=a.style,q=a.nodeType&&W(a),r=n._data(a,"fxshow");c.queue||(h=n._queueHooks(a,"fx"),null==h.unqueued&&(h.unqueued=0,i=h.empty.fire,h.empty.fire=function(){h.unqueued||i()}),h.unqueued++,m.always(function(){m.always(function(){h.unqueued--,n.queue(a,"fx").length||h.empty.fire()})})),1===a.nodeType&&("height"in b||"width"in b)&&(c.overflow=[p.overflow,p.overflowX,p.overflowY],j=n.css(a,"display"),k="none"===j?n._data(a,"olddisplay")||Ma(a.nodeName):j,"inline"===k&&"none"===n.css(a,"float")&&(l.inlineBlockNeedsLayout&&"inline"!==Ma(a.nodeName)?p.zoom=1:p.display="inline-block")),c.overflow&&(p.overflow="hidden",l.shrinkWrapBlocks()||m.always(function(){p.overflow=c.overflow[0],p.overflowX=c.overflow[1],p.overflowY=c.overflow[2]}));for(d in b)if(e=b[d],jb.exec(e)){if(delete b[d],f=f||"toggle"===e,e===(q?"hide":"show")){if("show"!==e||!r||void 0===r[d])continue;q=!0}o[d]=r&&r[d]||n.style(a,d)}else j=void 0;if(n.isEmptyObject(o))"inline"===("none"===j?Ma(a.nodeName):j)&&(p.display=j);else{r?"hidden"in r&&(q=r.hidden):r=n._data(a,"fxshow",{}),f&&(r.hidden=!q),q?n(a).show():m.done(function(){n(a).hide()}),m.done(function(){var b;n._removeData(a,"fxshow");for(b in o)n.style(a,b,o[b])});for(d in o)g=nb(q?r[d]:0,d,m),d in r||(r[d]=g.start,q&&(g.end=g.start,g.start="width"===d||"height"===d?1:0))}}function pb(a,b){var c,d,e,f,g;for(c in a)if(d=n.camelCase(c),e=b[d],f=a[c],n.isArray(f)&&(e=f[1],f=a[c]=f[0]),c!==d&&(a[d]=f,delete a[c]),g=n.cssHooks[d],g&&"expand"in g){f=g.expand(f),delete a[d];for(c in f)c in a||(a[c]=f[c],b[c]=e)}else b[d]=e}function qb(a,b,c){var d,e,f=0,g=qb.prefilters.length,h=n.Deferred().always(function(){delete i.elem}),i=function(){if(e)return!1;for(var b=hb||lb(),c=Math.max(0,j.startTime+j.duration-b),d=c/j.duration||0,f=1-d,g=0,i=j.tweens.length;i>g;g++)j.tweens[g].run(f);return h.notifyWith(a,[j,f,c]),1>f&&i?c:(h.resolveWith(a,[j]),!1)},j=h.promise({elem:a,props:n.extend({},b),opts:n.extend(!0,{specialEasing:{},easing:n.easing._default},c),originalProperties:b,originalOptions:c,startTime:hb||lb(),duration:c.duration,tweens:[],createTween:function(b,c){var d=n.Tween(a,j.opts,b,c,j.opts.specialEasing[b]||j.opts.easing);return j.tweens.push(d),d},stop:function(b){var c=0,d=b?j.tweens.length:0;if(e)return this;for(e=!0;d>c;c++)j.tweens[c].run(1);return b?(h.notifyWith(a,[j,1,0]),h.resolveWith(a,[j,b])):h.rejectWith(a,[j,b]),this}}),k=j.props;for(pb(k,j.opts.specialEasing);g>f;f++)if(d=qb.prefilters[f].call(j,a,k,j.opts))return n.isFunction(d.stop)&&(n._queueHooks(j.elem,j.opts.queue).stop=n.proxy(d.stop,d)),d;return n.map(k,nb,j),n.isFunction(j.opts.start)&&j.opts.start.call(a,j),n.fx.timer(n.extend(i,{elem:a,anim:j,queue:j.opts.queue})),j.progress(j.opts.progress).done(j.opts.done,j.opts.complete).fail(j.opts.fail).always(j.opts.always)}n.Animation=n.extend(qb,{tweeners:{"*":[function(a,b){var c=this.createTween(a,b);return X(c.elem,a,U.exec(b),c),c}]},tweener:function(a,b){n.isFunction(a)?(b=a,a=["*"]):a=a.match(G);for(var c,d=0,e=a.length;e>d;d++)c=a[d],qb.tweeners[c]=qb.tweeners[c]||[],qb.tweeners[c].unshift(b)},prefilters:[ob],prefilter:function(a,b){b?qb.prefilters.unshift(a):qb.prefilters.push(a)}}),n.speed=function(a,b,c){var d=a&&"object"==typeof a?n.extend({},a):{complete:c||!c&&b||n.isFunction(a)&&a,duration:a,easing:c&&b||b&&!n.isFunction(b)&&b};return d.duration=n.fx.off?0:"number"==typeof d.duration?d.duration:d.duration in n.fx.speeds?n.fx.speeds[d.duration]:n.fx.speeds._default,null!=d.queue&&d.queue!==!0||(d.queue="fx"),d.old=d.complete,d.complete=function(){n.isFunction(d.old)&&d.old.call(this),d.queue&&n.dequeue(this,d.queue)},d},n.fn.extend({fadeTo:function(a,b,c,d){return this.filter(W).css("opacity",0).show().end().animate({opacity:b},a,c,d)},animate:function(a,b,c,d){var e=n.isEmptyObject(a),f=n.speed(b,c,d),g=function(){var b=qb(this,n.extend({},a),f);(e||n._data(this,"finish"))&&b.stop(!0)};return g.finish=g,e||f.queue===!1?this.each(g):this.queue(f.queue,g)},stop:function(a,b,c){var d=function(a){var b=a.stop;delete a.stop,b(c)};return"string"!=typeof a&&(c=b,b=a,a=void 0),b&&a!==!1&&this.queue(a||"fx",[]),this.each(function(){var b=!0,e=null!=a&&a+"queueHooks",f=n.timers,g=n._data(this);if(e)g[e]&&g[e].stop&&d(g[e]);else for(e in g)g[e]&&g[e].stop&&kb.test(e)&&d(g[e]);for(e=f.length;e--;)f[e].elem!==this||null!=a&&f[e].queue!==a||(f[e].anim.stop(c),b=!1,f.splice(e,1));!b&&c||n.dequeue(this,a)})},finish:function(a){return a!==!1&&(a=a||"fx"),this.each(function(){var b,c=n._data(this),d=c[a+"queue"],e=c[a+"queueHooks"],f=n.timers,g=d?d.length:0;for(c.finish=!0,n.queue(this,a,[]),e&&e.stop&&e.stop.call(this,!0),b=f.length;b--;)f[b].elem===this&&f[b].queue===a&&(f[b].anim.stop(!0),f.splice(b,1));for(b=0;g>b;b++)d[b]&&d[b].finish&&d[b].finish.call(this);delete c.finish})}}),n.each(["toggle","show","hide"],function(a,b){var c=n.fn[b];n.fn[b]=function(a,d,e){return null==a||"boolean"==typeof a?c.apply(this,arguments):this.animate(mb(b,!0),a,d,e)}}),n.each({slideDown:mb("show"),slideUp:mb("hide"),slideToggle:mb("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(a,b){n.fn[a]=function(a,c,d){return this.animate(b,a,c,d)}}),n.timers=[],n.fx.tick=function(){var a,b=n.timers,c=0;for(hb=n.now();c<b.length;c++)a=b[c],a()||b[c]!==a||b.splice(c--,1);b.length||n.fx.stop(),hb=void 0},n.fx.timer=function(a){n.timers.push(a),a()?n.fx.start():n.timers.pop()},n.fx.interval=13,n.fx.start=function(){ib||(ib=a.setInterval(n.fx.tick,n.fx.interval))},n.fx.stop=function(){a.clearInterval(ib),ib=null},n.fx.speeds={slow:600,fast:200,_default:400},n.fn.delay=function(b,c){return b=n.fx?n.fx.speeds[b]||b:b,c=c||"fx",this.queue(c,function(c,d){var e=a.setTimeout(c,b);d.stop=function(){a.clearTimeout(e)}})},function(){var a,b=d.createElement("input"),c=d.createElement("div"),e=d.createElement("select"),f=e.appendChild(d.createElement("option"));c=d.createElement("div"),c.setAttribute("className","t"),c.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",a=c.getElementsByTagName("a")[0],b.setAttribute("type","checkbox"),c.appendChild(b),a=c.getElementsByTagName("a")[0],a.style.cssText="top:1px",l.getSetAttribute="t"!==c.className,l.style=/top/.test(a.getAttribute("style")),l.hrefNormalized="/a"===a.getAttribute("href"),l.checkOn=!!b.value,l.optSelected=f.selected,l.enctype=!!d.createElement("form").enctype,e.disabled=!0,l.optDisabled=!f.disabled,b=d.createElement("input"),b.setAttribute("value",""),l.input=""===b.getAttribute("value"),b.value="t",b.setAttribute("type","radio"),l.radioValue="t"===b.value}();var rb=/\r/g,sb=/[\x20\t\r\n\f]+/g;n.fn.extend({val:function(a){var b,c,d,e=this[0];{if(arguments.length)return d=n.isFunction(a),this.each(function(c){var e;1===this.nodeType&&(e=d?a.call(this,c,n(this).val()):a,null==e?e="":"number"==typeof e?e+="":n.isArray(e)&&(e=n.map(e,function(a){return null==a?"":a+""})),b=n.valHooks[this.type]||n.valHooks[this.nodeName.toLowerCase()],b&&"set"in b&&void 0!==b.set(this,e,"value")||(this.value=e))});if(e)return b=n.valHooks[e.type]||n.valHooks[e.nodeName.toLowerCase()],b&&"get"in b&&void 0!==(c=b.get(e,"value"))?c:(c=e.value,"string"==typeof c?c.replace(rb,""):null==c?"":c)}}}),n.extend({valHooks:{option:{get:function(a){var b=n.find.attr(a,"value");return null!=b?b:n.trim(n.text(a)).replace(sb," ")}},select:{get:function(a){for(var b,c,d=a.options,e=a.selectedIndex,f="select-one"===a.type||0>e,g=f?null:[],h=f?e+1:d.length,i=0>e?h:f?e:0;h>i;i++)if(c=d[i],(c.selected||i===e)&&(l.optDisabled?!c.disabled:null===c.getAttribute("disabled"))&&(!c.parentNode.disabled||!n.nodeName(c.parentNode,"optgroup"))){if(b=n(c).val(),f)return b;g.push(b)}return g},set:function(a,b){var c,d,e=a.options,f=n.makeArray(b),g=e.length;while(g--)if(d=e[g],n.inArray(n.valHooks.option.get(d),f)>-1)try{d.selected=c=!0}catch(h){d.scrollHeight}else d.selected=!1;return c||(a.selectedIndex=-1),e}}}}),n.each(["radio","checkbox"],function(){n.valHooks[this]={set:function(a,b){return n.isArray(b)?a.checked=n.inArray(n(a).val(),b)>-1:void 0}},l.checkOn||(n.valHooks[this].get=function(a){return null===a.getAttribute("value")?"on":a.value})});var tb,ub,vb=n.expr.attrHandle,wb=/^(?:checked|selected)$/i,xb=l.getSetAttribute,yb=l.input;n.fn.extend({attr:function(a,b){return Y(this,n.attr,a,b,arguments.length>1)},removeAttr:function(a){return this.each(function(){n.removeAttr(this,a)})}}),n.extend({attr:function(a,b,c){var d,e,f=a.nodeType;if(3!==f&&8!==f&&2!==f)return"undefined"==typeof a.getAttribute?n.prop(a,b,c):(1===f&&n.isXMLDoc(a)||(b=b.toLowerCase(),e=n.attrHooks[b]||(n.expr.match.bool.test(b)?ub:tb)),void 0!==c?null===c?void n.removeAttr(a,b):e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:(a.setAttribute(b,c+""),c):e&&"get"in e&&null!==(d=e.get(a,b))?d:(d=n.find.attr(a,b),null==d?void 0:d))},attrHooks:{type:{set:function(a,b){if(!l.radioValue&&"radio"===b&&n.nodeName(a,"input")){var c=a.value;return a.setAttribute("type",b),c&&(a.value=c),b}}}},removeAttr:function(a,b){var c,d,e=0,f=b&&b.match(G);if(f&&1===a.nodeType)while(c=f[e++])d=n.propFix[c]||c,n.expr.match.bool.test(c)?yb&&xb||!wb.test(c)?a[d]=!1:a[n.camelCase("default-"+c)]=a[d]=!1:n.attr(a,c,""),a.removeAttribute(xb?c:d)}}),ub={set:function(a,b,c){return b===!1?n.removeAttr(a,c):yb&&xb||!wb.test(c)?a.setAttribute(!xb&&n.propFix[c]||c,c):a[n.camelCase("default-"+c)]=a[c]=!0,c}},n.each(n.expr.match.bool.source.match(/\w+/g),function(a,b){var c=vb[b]||n.find.attr;yb&&xb||!wb.test(b)?vb[b]=function(a,b,d){var e,f;return d||(f=vb[b],vb[b]=e,e=null!=c(a,b,d)?b.toLowerCase():null,vb[b]=f),e}:vb[b]=function(a,b,c){return c?void 0:a[n.camelCase("default-"+b)]?b.toLowerCase():null}}),yb&&xb||(n.attrHooks.value={set:function(a,b,c){return n.nodeName(a,"input")?void(a.defaultValue=b):tb&&tb.set(a,b,c)}}),xb||(tb={set:function(a,b,c){var d=a.getAttributeNode(c);return d||a.setAttributeNode(d=a.ownerDocument.createAttribute(c)),d.value=b+="","value"===c||b===a.getAttribute(c)?b:void 0}},vb.id=vb.name=vb.coords=function(a,b,c){var d;return c?void 0:(d=a.getAttributeNode(b))&&""!==d.value?d.value:null},n.valHooks.button={get:function(a,b){var c=a.getAttributeNode(b);return c&&c.specified?c.value:void 0},set:tb.set},n.attrHooks.contenteditable={set:function(a,b,c){tb.set(a,""===b?!1:b,c)}},n.each(["width","height"],function(a,b){n.attrHooks[b]={set:function(a,c){return""===c?(a.setAttribute(b,"auto"),c):void 0}}})),l.style||(n.attrHooks.style={get:function(a){return a.style.cssText||void 0},set:function(a,b){return a.style.cssText=b+""}});var zb=/^(?:input|select|textarea|button|object)$/i,Ab=/^(?:a|area)$/i;n.fn.extend({prop:function(a,b){return Y(this,n.prop,a,b,arguments.length>1)},removeProp:function(a){return a=n.propFix[a]||a,this.each(function(){try{this[a]=void 0,delete this[a]}catch(b){}})}}),n.extend({prop:function(a,b,c){var d,e,f=a.nodeType;if(3!==f&&8!==f&&2!==f)return 1===f&&n.isXMLDoc(a)||(b=n.propFix[b]||b,e=n.propHooks[b]),void 0!==c?e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:a[b]=c:e&&"get"in e&&null!==(d=e.get(a,b))?d:a[b]},propHooks:{tabIndex:{get:function(a){var b=n.find.attr(a,"tabindex");return b?parseInt(b,10):zb.test(a.nodeName)||Ab.test(a.nodeName)&&a.href?0:-1}}},propFix:{"for":"htmlFor","class":"className"}}),l.hrefNormalized||n.each(["href","src"],function(a,b){n.propHooks[b]={get:function(a){return a.getAttribute(b,4)}}}),l.optSelected||(n.propHooks.selected={get:function(a){var b=a.parentNode;return b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex),null},set:function(a){var b=a.parentNode;b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex)}}),n.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){n.propFix[this.toLowerCase()]=this}),l.enctype||(n.propFix.enctype="encoding");var Bb=/[\t\r\n\f]/g;function Cb(a){return n.attr(a,"class")||""}n.fn.extend({addClass:function(a){var b,c,d,e,f,g,h,i=0;if(n.isFunction(a))return this.each(function(b){n(this).addClass(a.call(this,b,Cb(this)))});if("string"==typeof a&&a){b=a.match(G)||[];while(c=this[i++])if(e=Cb(c),d=1===c.nodeType&&(" "+e+" ").replace(Bb," ")){g=0;while(f=b[g++])d.indexOf(" "+f+" ")<0&&(d+=f+" ");h=n.trim(d),e!==h&&n.attr(c,"class",h)}}return this},removeClass:function(a){var b,c,d,e,f,g,h,i=0;if(n.isFunction(a))return this.each(function(b){n(this).removeClass(a.call(this,b,Cb(this)))});if(!arguments.length)return this.attr("class","");if("string"==typeof a&&a){b=a.match(G)||[];while(c=this[i++])if(e=Cb(c),d=1===c.nodeType&&(" "+e+" ").replace(Bb," ")){g=0;while(f=b[g++])while(d.indexOf(" "+f+" ")>-1)d=d.replace(" "+f+" "," ");h=n.trim(d),e!==h&&n.attr(c,"class",h)}}return this},toggleClass:function(a,b){var c=typeof a;return"boolean"==typeof b&&"string"===c?b?this.addClass(a):this.removeClass(a):n.isFunction(a)?this.each(function(c){n(this).toggleClass(a.call(this,c,Cb(this),b),b)}):this.each(function(){var b,d,e,f;if("string"===c){d=0,e=n(this),f=a.match(G)||[];while(b=f[d++])e.hasClass(b)?e.removeClass(b):e.addClass(b)}else void 0!==a&&"boolean"!==c||(b=Cb(this),b&&n._data(this,"__className__",b),n.attr(this,"class",b||a===!1?"":n._data(this,"__className__")||""))})},hasClass:function(a){var b,c,d=0;b=" "+a+" ";while(c=this[d++])if(1===c.nodeType&&(" "+Cb(c)+" ").replace(Bb," ").indexOf(b)>-1)return!0;return!1}}),n.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(a,b){n.fn[b]=function(a,c){return arguments.length>0?this.on(b,null,a,c):this.trigger(b)}}),n.fn.extend({hover:function(a,b){return this.mouseenter(a).mouseleave(b||a)}});var Db=a.location,Eb=n.now(),Fb=/\?/,Gb=/(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;n.parseJSON=function(b){if(a.JSON&&a.JSON.parse)return a.JSON.parse(b+"");var c,d=null,e=n.trim(b+"");return e&&!n.trim(e.replace(Gb,function(a,b,e,f){return c&&b&&(d=0),0===d?a:(c=e||b,d+=!f-!e,"")}))?Function("return "+e)():n.error("Invalid JSON: "+b)},n.parseXML=function(b){var c,d;if(!b||"string"!=typeof b)return null;try{a.DOMParser?(d=new a.DOMParser,c=d.parseFromString(b,"text/xml")):(c=new a.ActiveXObject("Microsoft.XMLDOM"),c.async="false",c.loadXML(b))}catch(e){c=void 0}return c&&c.documentElement&&!c.getElementsByTagName("parsererror").length||n.error("Invalid XML: "+b),c};var Hb=/#.*$/,Ib=/([?&])_=[^&]*/,Jb=/^(.*?):[ \t]*([^\r\n]*)\r?$/gm,Kb=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Lb=/^(?:GET|HEAD)$/,Mb=/^\/\//,Nb=/^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,Ob={},Pb={},Qb="*/".concat("*"),Rb=Db.href,Sb=Nb.exec(Rb.toLowerCase())||[];function Tb(a){return function(b,c){"string"!=typeof b&&(c=b,b="*");var d,e=0,f=b.toLowerCase().match(G)||[];if(n.isFunction(c))while(d=f[e++])"+"===d.charAt(0)?(d=d.slice(1)||"*",(a[d]=a[d]||[]).unshift(c)):(a[d]=a[d]||[]).push(c)}}function Ub(a,b,c,d){var e={},f=a===Pb;function g(h){var i;return e[h]=!0,n.each(a[h]||[],function(a,h){var j=h(b,c,d);return"string"!=typeof j||f||e[j]?f?!(i=j):void 0:(b.dataTypes.unshift(j),g(j),!1)}),i}return g(b.dataTypes[0])||!e["*"]&&g("*")}function Vb(a,b){var c,d,e=n.ajaxSettings.flatOptions||{};for(d in b)void 0!==b[d]&&((e[d]?a:c||(c={}))[d]=b[d]);return c&&n.extend(!0,a,c),a}function Wb(a,b,c){var d,e,f,g,h=a.contents,i=a.dataTypes;while("*"===i[0])i.shift(),void 0===e&&(e=a.mimeType||b.getResponseHeader("Content-Type"));if(e)for(g in h)if(h[g]&&h[g].test(e)){i.unshift(g);break}if(i[0]in c)f=i[0];else{for(g in c){if(!i[0]||a.converters[g+" "+i[0]]){f=g;break}d||(d=g)}f=f||d}return f?(f!==i[0]&&i.unshift(f),c[f]):void 0}function Xb(a,b,c,d){var e,f,g,h,i,j={},k=a.dataTypes.slice();if(k[1])for(g in a.converters)j[g.toLowerCase()]=a.converters[g];f=k.shift();while(f)if(a.responseFields[f]&&(c[a.responseFields[f]]=b),!i&&d&&a.dataFilter&&(b=a.dataFilter(b,a.dataType)),i=f,f=k.shift())if("*"===f)f=i;else if("*"!==i&&i!==f){if(g=j[i+" "+f]||j["* "+f],!g)for(e in j)if(h=e.split(" "),h[1]===f&&(g=j[i+" "+h[0]]||j["* "+h[0]])){g===!0?g=j[e]:j[e]!==!0&&(f=h[0],k.unshift(h[1]));break}if(g!==!0)if(g&&a["throws"])b=g(b);else try{b=g(b)}catch(l){return{state:"parsererror",error:g?l:"No conversion from "+i+" to "+f}}}return{state:"success",data:b}}n.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Rb,type:"GET",isLocal:Kb.test(Sb[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":Qb,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/\bxml\b/,html:/\bhtml/,json:/\bjson\b/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":n.parseJSON,"text xml":n.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(a,b){return b?Vb(Vb(a,n.ajaxSettings),b):Vb(n.ajaxSettings,a)},ajaxPrefilter:Tb(Ob),ajaxTransport:Tb(Pb),ajax:function(b,c){"object"==typeof b&&(c=b,b=void 0),c=c||{};var d,e,f,g,h,i,j,k,l=n.ajaxSetup({},c),m=l.context||l,o=l.context&&(m.nodeType||m.jquery)?n(m):n.event,p=n.Deferred(),q=n.Callbacks("once memory"),r=l.statusCode||{},s={},t={},u=0,v="canceled",w={readyState:0,getResponseHeader:function(a){var b;if(2===u){if(!k){k={};while(b=Jb.exec(g))k[b[1].toLowerCase()]=b[2]}b=k[a.toLowerCase()]}return null==b?null:b},getAllResponseHeaders:function(){return 2===u?g:null},setRequestHeader:function(a,b){var c=a.toLowerCase();return u||(a=t[c]=t[c]||a,s[a]=b),this},overrideMimeType:function(a){return u||(l.mimeType=a),this},statusCode:function(a){var b;if(a)if(2>u)for(b in a)r[b]=[r[b],a[b]];else w.always(a[w.status]);return this},abort:function(a){var b=a||v;return j&&j.abort(b),y(0,b),this}};if(p.promise(w).complete=q.add,w.success=w.done,w.error=w.fail,l.url=((b||l.url||Rb)+"").replace(Hb,"").replace(Mb,Sb[1]+"//"),l.type=c.method||c.type||l.method||l.type,l.dataTypes=n.trim(l.dataType||"*").toLowerCase().match(G)||[""],null==l.crossDomain&&(d=Nb.exec(l.url.toLowerCase()),l.crossDomain=!(!d||d[1]===Sb[1]&&d[2]===Sb[2]&&(d[3]||("http:"===d[1]?"80":"443"))===(Sb[3]||("http:"===Sb[1]?"80":"443")))),l.data&&l.processData&&"string"!=typeof l.data&&(l.data=n.param(l.data,l.traditional)),Ub(Ob,l,c,w),2===u)return w;i=n.event&&l.global,i&&0===n.active++&&n.event.trigger("ajaxStart"),l.type=l.type.toUpperCase(),l.hasContent=!Lb.test(l.type),f=l.url,l.hasContent||(l.data&&(f=l.url+=(Fb.test(f)?"&":"?")+l.data,delete l.data),l.cache===!1&&(l.url=Ib.test(f)?f.replace(Ib,"$1_="+Eb++):f+(Fb.test(f)?"&":"?")+"_="+Eb++)),l.ifModified&&(n.lastModified[f]&&w.setRequestHeader("If-Modified-Since",n.lastModified[f]),n.etag[f]&&w.setRequestHeader("If-None-Match",n.etag[f])),(l.data&&l.hasContent&&l.contentType!==!1||c.contentType)&&w.setRequestHeader("Content-Type",l.contentType),w.setRequestHeader("Accept",l.dataTypes[0]&&l.accepts[l.dataTypes[0]]?l.accepts[l.dataTypes[0]]+("*"!==l.dataTypes[0]?", "+Qb+"; q=0.01":""):l.accepts["*"]);for(e in l.headers)w.setRequestHeader(e,l.headers[e]);if(l.beforeSend&&(l.beforeSend.call(m,w,l)===!1||2===u))return w.abort();v="abort";for(e in{success:1,error:1,complete:1})w[e](l[e]);if(j=Ub(Pb,l,c,w)){if(w.readyState=1,i&&o.trigger("ajaxSend",[w,l]),2===u)return w;l.async&&l.timeout>0&&(h=a.setTimeout(function(){w.abort("timeout")},l.timeout));try{u=1,j.send(s,y)}catch(x){if(!(2>u))throw x;y(-1,x)}}else y(-1,"No Transport");function y(b,c,d,e){var k,s,t,v,x,y=c;2!==u&&(u=2,h&&a.clearTimeout(h),j=void 0,g=e||"",w.readyState=b>0?4:0,k=b>=200&&300>b||304===b,d&&(v=Wb(l,w,d)),v=Xb(l,v,w,k),k?(l.ifModified&&(x=w.getResponseHeader("Last-Modified"),x&&(n.lastModified[f]=x),x=w.getResponseHeader("etag"),x&&(n.etag[f]=x)),204===b||"HEAD"===l.type?y="nocontent":304===b?y="notmodified":(y=v.state,s=v.data,t=v.error,k=!t)):(t=y,!b&&y||(y="error",0>b&&(b=0))),w.status=b,w.statusText=(c||y)+"",k?p.resolveWith(m,[s,y,w]):p.rejectWith(m,[w,y,t]),w.statusCode(r),r=void 0,i&&o.trigger(k?"ajaxSuccess":"ajaxError",[w,l,k?s:t]),q.fireWith(m,[w,y]),i&&(o.trigger("ajaxComplete",[w,l]),--n.active||n.event.trigger("ajaxStop")))}return w},getJSON:function(a,b,c){return n.get(a,b,c,"json")},getScript:function(a,b){return n.get(a,void 0,b,"script")}}),n.each(["get","post"],function(a,b){n[b]=function(a,c,d,e){return n.isFunction(c)&&(e=e||d,d=c,c=void 0),n.ajax(n.extend({url:a,type:b,dataType:e,data:c,success:d},n.isPlainObject(a)&&a))}}),n._evalUrl=function(a){return n.ajax({url:a,type:"GET",dataType:"script",cache:!0,async:!1,global:!1,"throws":!0})},n.fn.extend({wrapAll:function(a){if(n.isFunction(a))return this.each(function(b){n(this).wrapAll(a.call(this,b))});if(this[0]){var b=n(a,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&b.insertBefore(this[0]),b.map(function(){var a=this;while(a.firstChild&&1===a.firstChild.nodeType)a=a.firstChild;return a}).append(this)}return this},wrapInner:function(a){return n.isFunction(a)?this.each(function(b){n(this).wrapInner(a.call(this,b))}):this.each(function(){var b=n(this),c=b.contents();c.length?c.wrapAll(a):b.append(a)})},wrap:function(a){var b=n.isFunction(a);return this.each(function(c){n(this).wrapAll(b?a.call(this,c):a)})},unwrap:function(){return this.parent().each(function(){n.nodeName(this,"body")||n(this).replaceWith(this.childNodes)}).end()}});function Yb(a){return a.style&&a.style.display||n.css(a,"display")}function Zb(a){if(!n.contains(a.ownerDocument||d,a))return!0;while(a&&1===a.nodeType){if("none"===Yb(a)||"hidden"===a.type)return!0;a=a.parentNode}return!1}n.expr.filters.hidden=function(a){return l.reliableHiddenOffsets()?a.offsetWidth<=0&&a.offsetHeight<=0&&!a.getClientRects().length:Zb(a)},n.expr.filters.visible=function(a){return!n.expr.filters.hidden(a)};var $b=/%20/g,_b=/\[\]$/,ac=/\r?\n/g,bc=/^(?:submit|button|image|reset|file)$/i,cc=/^(?:input|select|textarea|keygen)/i;function dc(a,b,c,d){var e;if(n.isArray(b))n.each(b,function(b,e){c||_b.test(a)?d(a,e):dc(a+"["+("object"==typeof e&&null!=e?b:"")+"]",e,c,d)});else if(c||"object"!==n.type(b))d(a,b);else for(e in b)dc(a+"["+e+"]",b[e],c,d)}n.param=function(a,b){var c,d=[],e=function(a,b){b=n.isFunction(b)?b():null==b?"":b,d[d.length]=encodeURIComponent(a)+"="+encodeURIComponent(b)};if(void 0===b&&(b=n.ajaxSettings&&n.ajaxSettings.traditional),n.isArray(a)||a.jquery&&!n.isPlainObject(a))n.each(a,function(){e(this.name,this.value)});else for(c in a)dc(c,a[c],b,e);return d.join("&").replace($b,"+")},n.fn.extend({serialize:function(){return n.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var a=n.prop(this,"elements");return a?n.makeArray(a):this}).filter(function(){var a=this.type;return this.name&&!n(this).is(":disabled")&&cc.test(this.nodeName)&&!bc.test(a)&&(this.checked||!Z.test(a))}).map(function(a,b){var c=n(this).val();return null==c?null:n.isArray(c)?n.map(c,function(a){return{name:b.name,value:a.replace(ac,"\r\n")}}):{name:b.name,value:c.replace(ac,"\r\n")}}).get()}}),n.ajaxSettings.xhr=void 0!==a.ActiveXObject?function(){return this.isLocal?ic():d.documentMode>8?hc():/^(get|post|head|put|delete|options)$/i.test(this.type)&&hc()||ic()}:hc;var ec=0,fc={},gc=n.ajaxSettings.xhr();a.attachEvent&&a.attachEvent("onunload",function(){for(var a in fc)fc[a](void 0,!0)}),l.cors=!!gc&&"withCredentials"in gc,gc=l.ajax=!!gc,gc&&n.ajaxTransport(function(b){if(!b.crossDomain||l.cors){var c;return{send:function(d,e){var f,g=b.xhr(),h=++ec;if(g.open(b.type,b.url,b.async,b.username,b.password),b.xhrFields)for(f in b.xhrFields)g[f]=b.xhrFields[f];b.mimeType&&g.overrideMimeType&&g.overrideMimeType(b.mimeType),b.crossDomain||d["X-Requested-With"]||(d["X-Requested-With"]="XMLHttpRequest");for(f in d)void 0!==d[f]&&g.setRequestHeader(f,d[f]+"");g.send(b.hasContent&&b.data||null),c=function(a,d){var f,i,j;if(c&&(d||4===g.readyState))if(delete fc[h],c=void 0,g.onreadystatechange=n.noop,d)4!==g.readyState&&g.abort();else{j={},f=g.status,"string"==typeof g.responseText&&(j.text=g.responseText);try{i=g.statusText}catch(k){i=""}f||!b.isLocal||b.crossDomain?1223===f&&(f=204):f=j.text?200:404}j&&e(f,i,j,g.getAllResponseHeaders())},b.async?4===g.readyState?a.setTimeout(c):g.onreadystatechange=fc[h]=c:c()},abort:function(){c&&c(void 0,!0)}}}});function hc(){try{return new a.XMLHttpRequest}catch(b){}}function ic(){try{return new a.ActiveXObject("Microsoft.XMLHTTP")}catch(b){}}n.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function(a){return n.globalEval(a),a}}}),n.ajaxPrefilter("script",function(a){void 0===a.cache&&(a.cache=!1),a.crossDomain&&(a.type="GET",a.global=!1)}),n.ajaxTransport("script",function(a){if(a.crossDomain){var b,c=d.head||n("head")[0]||d.documentElement;return{send:function(e,f){b=d.createElement("script"),b.async=!0,a.scriptCharset&&(b.charset=a.scriptCharset),b.src=a.url,b.onload=b.onreadystatechange=function(a,c){(c||!b.readyState||/loaded|complete/.test(b.readyState))&&(b.onload=b.onreadystatechange=null,b.parentNode&&b.parentNode.removeChild(b),b=null,c||f(200,"success"))},c.insertBefore(b,c.firstChild)},abort:function(){b&&b.onload(void 0,!0)}}}});var jc=[],kc=/(=)\?(?=&|$)|\?\?/;n.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var a=jc.pop()||n.expando+"_"+Eb++;return this[a]=!0,a}}),n.ajaxPrefilter("json jsonp",function(b,c,d){var e,f,g,h=b.jsonp!==!1&&(kc.test(b.url)?"url":"string"==typeof b.data&&0===(b.contentType||"").indexOf("application/x-www-form-urlencoded")&&kc.test(b.data)&&"data");return h||"jsonp"===b.dataTypes[0]?(e=b.jsonpCallback=n.isFunction(b.jsonpCallback)?b.jsonpCallback():b.jsonpCallback,h?b[h]=b[h].replace(kc,"$1"+e):b.jsonp!==!1&&(b.url+=(Fb.test(b.url)?"&":"?")+b.jsonp+"="+e),b.converters["script json"]=function(){return g||n.error(e+" was not called"),g[0]},b.dataTypes[0]="json",f=a[e],a[e]=function(){g=arguments},d.always(function(){void 0===f?n(a).removeProp(e):a[e]=f,b[e]&&(b.jsonpCallback=c.jsonpCallback,jc.push(e)),g&&n.isFunction(f)&&f(g[0]),g=f=void 0}),"script"):void 0}),n.parseHTML=function(a,b,c){if(!a||"string"!=typeof a)return null;"boolean"==typeof b&&(c=b,b=!1),b=b||d;var e=x.exec(a),f=!c&&[];return e?[b.createElement(e[1])]:(e=ja([a],b,f),f&&f.length&&n(f).remove(),n.merge([],e.childNodes))};var lc=n.fn.load;n.fn.load=function(a,b,c){if("string"!=typeof a&&lc)return lc.apply(this,arguments);var d,e,f,g=this,h=a.indexOf(" ");return h>-1&&(d=n.trim(a.slice(h,a.length)),a=a.slice(0,h)),n.isFunction(b)?(c=b,b=void 0):b&&"object"==typeof b&&(e="POST"),g.length>0&&n.ajax({url:a,type:e||"GET",dataType:"html",data:b}).done(function(a){f=arguments,g.html(d?n("<div>").append(n.parseHTML(a)).find(d):a)}).always(c&&function(a,b){g.each(function(){c.apply(this,f||[a.responseText,b,a])})}),this},n.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(a,b){n.fn[b]=function(a){return this.on(b,a)}}),n.expr.filters.animated=function(a){return n.grep(n.timers,function(b){return a===b.elem}).length};function mc(a){return n.isWindow(a)?a:9===a.nodeType?a.defaultView||a.parentWindow:!1}n.offset={setOffset:function(a,b,c){var d,e,f,g,h,i,j,k=n.css(a,"position"),l=n(a),m={};"static"===k&&(a.style.position="relative"),h=l.offset(),f=n.css(a,"top"),i=n.css(a,"left"),j=("absolute"===k||"fixed"===k)&&n.inArray("auto",[f,i])>-1,j?(d=l.position(),g=d.top,e=d.left):(g=parseFloat(f)||0,e=parseFloat(i)||0),n.isFunction(b)&&(b=b.call(a,c,n.extend({},h))),null!=b.top&&(m.top=b.top-h.top+g),null!=b.left&&(m.left=b.left-h.left+e),"using"in b?b.using.call(a,m):l.css(m)}},n.fn.extend({offset:function(a){if(arguments.length)return void 0===a?this:this.each(function(b){n.offset.setOffset(this,a,b)});var b,c,d={top:0,left:0},e=this[0],f=e&&e.ownerDocument;if(f)return b=f.documentElement,n.contains(b,e)?("undefined"!=typeof e.getBoundingClientRect&&(d=e.getBoundingClientRect()),c=mc(f),{top:d.top+(c.pageYOffset||b.scrollTop)-(b.clientTop||0),left:d.left+(c.pageXOffset||b.scrollLeft)-(b.clientLeft||0)}):d},position:function(){if(this[0]){var a,b,c={top:0,left:0},d=this[0];return"fixed"===n.css(d,"position")?b=d.getBoundingClientRect():(a=this.offsetParent(),b=this.offset(),n.nodeName(a[0],"html")||(c=a.offset()),c.top+=n.css(a[0],"borderTopWidth",!0),c.left+=n.css(a[0],"borderLeftWidth",!0)),{top:b.top-c.top-n.css(d,"marginTop",!0),left:b.left-c.left-n.css(d,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var a=this.offsetParent;while(a&&!n.nodeName(a,"html")&&"static"===n.css(a,"position"))a=a.offsetParent;return a||Qa})}}),n.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(a,b){var c=/Y/.test(b);n.fn[a]=function(d){return Y(this,function(a,d,e){var f=mc(a);return void 0===e?f?b in f?f[b]:f.document.documentElement[d]:a[d]:void(f?f.scrollTo(c?n(f).scrollLeft():e,c?e:n(f).scrollTop()):a[d]=e)},a,d,arguments.length,null)}}),n.each(["top","left"],function(a,b){n.cssHooks[b]=Ua(l.pixelPosition,function(a,c){return c?(c=Sa(a,b),Oa.test(c)?n(a).position()[b]+"px":c):void 0})}),n.each({Height:"height",Width:"width"},function(a,b){n.each({
padding:"inner"+a,content:b,"":"outer"+a},function(c,d){n.fn[d]=function(d,e){var f=arguments.length&&(c||"boolean"!=typeof d),g=c||(d===!0||e===!0?"margin":"border");return Y(this,function(b,c,d){var e;return n.isWindow(b)?b.document.documentElement["client"+a]:9===b.nodeType?(e=b.documentElement,Math.max(b.body["scroll"+a],e["scroll"+a],b.body["offset"+a],e["offset"+a],e["client"+a])):void 0===d?n.css(b,c,g):n.style(b,c,d,g)},b,f?d:void 0,f,null)}})}),n.fn.extend({bind:function(a,b,c){return this.on(a,null,b,c)},unbind:function(a,b){return this.off(a,null,b)},delegate:function(a,b,c,d){return this.on(b,a,c,d)},undelegate:function(a,b,c){return 1===arguments.length?this.off(a,"**"):this.off(b,a||"**",c)}}),n.fn.size=function(){return this.length},n.fn.andSelf=n.fn.addBack,"function"==typeof define&&define.amd&&define("jquery",[],function(){return n});var nc=a.jQuery,oc=a.$;return n.noConflict=function(b){return a.$===n&&(a.$=oc),b&&a.jQuery===n&&(a.jQuery=nc),n},b||(a.jQuery=a.$=n),n});
/*! jQuery UI - v1.11.4 - 2015-08-16
* http://jqueryui.com
* Includes: core.js, widget.js, mouse.js, position.js, draggable.js, droppable.js, resizable.js, selectable.js, sortable.js, accordion.js, autocomplete.js, button.js, datepicker.js, dialog.js, menu.js, progressbar.js, selectmenu.js, slider.js, spinner.js, tabs.js, tooltip.js, effect.js, effect-blind.js, effect-bounce.js, effect-clip.js, effect-drop.js, effect-explode.js, effect-fade.js, effect-fold.js, effect-highlight.js, effect-puff.js, effect-pulsate.js, effect-scale.js, effect-shake.js, effect-size.js, effect-slide.js, effect-transfer.js
* Copyright 2015 jQuery Foundation and other contributors; Licensed MIT */

(function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e(jQuery)})(function(e){function t(t,s){var n,a,o,r=t.nodeName.toLowerCase();return"area"===r?(n=t.parentNode,a=n.name,t.href&&a&&"map"===n.nodeName.toLowerCase()?(o=e("img[usemap='#"+a+"']")[0],!!o&&i(o)):!1):(/^(input|select|textarea|button|object)$/.test(r)?!t.disabled:"a"===r?t.href||s:s)&&i(t)}function i(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return"hidden"===e.css(this,"visibility")}).length}function s(e){for(var t,i;e.length&&e[0]!==document;){if(t=e.css("position"),("absolute"===t||"relative"===t||"fixed"===t)&&(i=parseInt(e.css("zIndex"),10),!isNaN(i)&&0!==i))return i;e=e.parent()}return 0}function n(){this._curInst=null,this._keyEvent=!1,this._disabledInputs=[],this._datepickerShowing=!1,this._inDialog=!1,this._mainDivId="ui-datepicker-div",this._inlineClass="ui-datepicker-inline",this._appendClass="ui-datepicker-append",this._triggerClass="ui-datepicker-trigger",this._dialogClass="ui-datepicker-dialog",this._disableClass="ui-datepicker-disabled",this._unselectableClass="ui-datepicker-unselectable",this._currentClass="ui-datepicker-current-day",this._dayOverClass="ui-datepicker-days-cell-over",this.regional=[],this.regional[""]={closeText:"Done",prevText:"Prev",nextText:"Next",currentText:"Today",monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayNamesMin:["Su","Mo","Tu","We","Th","Fr","Sa"],weekHeader:"Wk",dateFormat:"mm/dd/yy",firstDay:0,isRTL:!1,showMonthAfterYear:!1,yearSuffix:""},this._defaults={showOn:"focus",showAnim:"fadeIn",showOptions:{},defaultDate:null,appendText:"",buttonText:"...",buttonImage:"",buttonImageOnly:!1,hideIfNoPrevNext:!1,navigationAsDateFormat:!1,gotoCurrent:!1,changeMonth:!1,changeYear:!1,yearRange:"c-10:c+10",showOtherMonths:!1,selectOtherMonths:!1,showWeek:!1,calculateWeek:this.iso8601Week,shortYearCutoff:"+10",minDate:null,maxDate:null,duration:"fast",beforeShowDay:null,beforeShow:null,onSelect:null,onChangeMonthYear:null,onClose:null,numberOfMonths:1,showCurrentAtPos:0,stepMonths:1,stepBigMonths:12,altField:"",altFormat:"",constrainInput:!0,showButtonPanel:!1,autoSize:!1,disabled:!1},e.extend(this._defaults,this.regional[""]),this.regional.en=e.extend(!0,{},this.regional[""]),this.regional["en-US"]=e.extend(!0,{},this.regional.en),this.dpDiv=a(e("<div id='"+this._mainDivId+"' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"))}function a(t){var i="button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";return t.delegate(i,"mouseout",function(){e(this).removeClass("ui-state-hover"),-1!==this.className.indexOf("ui-datepicker-prev")&&e(this).removeClass("ui-datepicker-prev-hover"),-1!==this.className.indexOf("ui-datepicker-next")&&e(this).removeClass("ui-datepicker-next-hover")}).delegate(i,"mouseover",o)}function o(){e.datepicker._isDisabledDatepicker(v.inline?v.dpDiv.parent()[0]:v.input[0])||(e(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"),e(this).addClass("ui-state-hover"),-1!==this.className.indexOf("ui-datepicker-prev")&&e(this).addClass("ui-datepicker-prev-hover"),-1!==this.className.indexOf("ui-datepicker-next")&&e(this).addClass("ui-datepicker-next-hover"))}function r(t,i){e.extend(t,i);for(var s in i)null==i[s]&&(t[s]=i[s]);return t}function h(e){return function(){var t=this.element.val();e.apply(this,arguments),this._refresh(),t!==this.element.val()&&this._trigger("change")}}e.ui=e.ui||{},e.extend(e.ui,{version:"1.11.4",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({scrollParent:function(t){var i=this.css("position"),s="absolute"===i,n=t?/(auto|scroll|hidden)/:/(auto|scroll)/,a=this.parents().filter(function(){var t=e(this);return s&&"static"===t.css("position")?!1:n.test(t.css("overflow")+t.css("overflow-y")+t.css("overflow-x"))}).eq(0);return"fixed"!==i&&a.length?a:e(this[0].ownerDocument||document)},uniqueId:function(){var e=0;return function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++e)})}}(),removeUniqueId:function(){return this.each(function(){/^ui-id-\d+$/.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,s){return!!e.data(t,s[3])},focusable:function(i){return t(i,!isNaN(e.attr(i,"tabindex")))},tabbable:function(i){var s=e.attr(i,"tabindex"),n=isNaN(s);return(n||s>=0)&&t(i,!n)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(t,i){function s(t,i,s,a){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,s&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),a&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===i?["Left","Right"]:["Top","Bottom"],a=i.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+i]=function(t){return void 0===t?o["inner"+i].call(this):this.each(function(){e(this).css(a,s(this,t)+"px")})},e.fn["outer"+i]=function(t,n){return"number"!=typeof t?o["outer"+i].call(this,t):this.each(function(){e(this).css(a,s(this,t,!0,n)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.fn.extend({focus:function(t){return function(i,s){return"number"==typeof i?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),s&&s.call(t)},i)}):t.apply(this,arguments)}}(e.fn.focus),disableSelection:function(){var e="onselectstart"in document.createElement("div")?"selectstart":"mousedown";return function(){return this.bind(e+".ui-disableSelection",function(e){e.preventDefault()})}}(),enableSelection:function(){return this.unbind(".ui-disableSelection")},zIndex:function(t){if(void 0!==t)return this.css("zIndex",t);if(this.length)for(var i,s,n=e(this[0]);n.length&&n[0]!==document;){if(i=n.css("position"),("absolute"===i||"relative"===i||"fixed"===i)&&(s=parseInt(n.css("zIndex"),10),!isNaN(s)&&0!==s))return s;n=n.parent()}return 0}}),e.ui.plugin={add:function(t,i,s){var n,a=e.ui[t].prototype;for(n in s)a.plugins[n]=a.plugins[n]||[],a.plugins[n].push([i,s[n]])},call:function(e,t,i,s){var n,a=e.plugins[t];if(a&&(s||e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType))for(n=0;a.length>n;n++)e.options[a[n][0]]&&a[n][1].apply(e.element,i)}};var l=0,u=Array.prototype.slice;e.cleanData=function(t){return function(i){var s,n,a;for(a=0;null!=(n=i[a]);a++)try{s=e._data(n,"events"),s&&s.remove&&e(n).triggerHandler("remove")}catch(o){}t(i)}}(e.cleanData),e.widget=function(t,i,s){var n,a,o,r,h={},l=t.split(".")[0];return t=t.split(".")[1],n=l+"-"+t,s||(s=i,i=e.Widget),e.expr[":"][n.toLowerCase()]=function(t){return!!e.data(t,n)},e[l]=e[l]||{},a=e[l][t],o=e[l][t]=function(e,t){return this._createWidget?(arguments.length&&this._createWidget(e,t),void 0):new o(e,t)},e.extend(o,a,{version:s.version,_proto:e.extend({},s),_childConstructors:[]}),r=new i,r.options=e.widget.extend({},r.options),e.each(s,function(t,s){return e.isFunction(s)?(h[t]=function(){var e=function(){return i.prototype[t].apply(this,arguments)},n=function(e){return i.prototype[t].apply(this,e)};return function(){var t,i=this._super,a=this._superApply;return this._super=e,this._superApply=n,t=s.apply(this,arguments),this._super=i,this._superApply=a,t}}(),void 0):(h[t]=s,void 0)}),o.prototype=e.widget.extend(r,{widgetEventPrefix:a?r.widgetEventPrefix||t:t},h,{constructor:o,namespace:l,widgetName:t,widgetFullName:n}),a?(e.each(a._childConstructors,function(t,i){var s=i.prototype;e.widget(s.namespace+"."+s.widgetName,o,i._proto)}),delete a._childConstructors):i._childConstructors.push(o),e.widget.bridge(t,o),o},e.widget.extend=function(t){for(var i,s,n=u.call(arguments,1),a=0,o=n.length;o>a;a++)for(i in n[a])s=n[a][i],n[a].hasOwnProperty(i)&&void 0!==s&&(t[i]=e.isPlainObject(s)?e.isPlainObject(t[i])?e.widget.extend({},t[i],s):e.widget.extend({},s):s);return t},e.widget.bridge=function(t,i){var s=i.prototype.widgetFullName||t;e.fn[t]=function(n){var a="string"==typeof n,o=u.call(arguments,1),r=this;return a?this.each(function(){var i,a=e.data(this,s);return"instance"===n?(r=a,!1):a?e.isFunction(a[n])&&"_"!==n.charAt(0)?(i=a[n].apply(a,o),i!==a&&void 0!==i?(r=i&&i.jquery?r.pushStack(i.get()):i,!1):void 0):e.error("no such method '"+n+"' for "+t+" widget instance"):e.error("cannot call methods on "+t+" prior to initialization; "+"attempted to call method '"+n+"'")}):(o.length&&(n=e.widget.extend.apply(null,[n].concat(o))),this.each(function(){var t=e.data(this,s);t?(t.option(n||{}),t._init&&t._init()):e.data(this,s,new i(n,this))})),r}},e.Widget=function(){},e.Widget._childConstructors=[],e.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(t,i){i=e(i||this.defaultElement||this)[0],this.element=e(i),this.uuid=l++,this.eventNamespace="."+this.widgetName+this.uuid,this.bindings=e(),this.hoverable=e(),this.focusable=e(),i!==this&&(e.data(i,this.widgetFullName,this),this._on(!0,this.element,{remove:function(e){e.target===i&&this.destroy()}}),this.document=e(i.style?i.ownerDocument:i.document||i),this.window=e(this.document[0].defaultView||this.document[0].parentWindow)),this.options=e.widget.extend({},this.options,this._getCreateOptions(),t),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:e.noop,_getCreateEventData:e.noop,_create:e.noop,_init:e.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:e.noop,widget:function(){return this.element},option:function(t,i){var s,n,a,o=t;if(0===arguments.length)return e.widget.extend({},this.options);if("string"==typeof t)if(o={},s=t.split("."),t=s.shift(),s.length){for(n=o[t]=e.widget.extend({},this.options[t]),a=0;s.length-1>a;a++)n[s[a]]=n[s[a]]||{},n=n[s[a]];if(t=s.pop(),1===arguments.length)return void 0===n[t]?null:n[t];n[t]=i}else{if(1===arguments.length)return void 0===this.options[t]?null:this.options[t];o[t]=i}return this._setOptions(o),this},_setOptions:function(e){var t;for(t in e)this._setOption(t,e[t]);return this},_setOption:function(e,t){return this.options[e]=t,"disabled"===e&&(this.widget().toggleClass(this.widgetFullName+"-disabled",!!t),t&&(this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus"))),this},enable:function(){return this._setOptions({disabled:!1})},disable:function(){return this._setOptions({disabled:!0})},_on:function(t,i,s){var n,a=this;"boolean"!=typeof t&&(s=i,i=t,t=!1),s?(i=n=e(i),this.bindings=this.bindings.add(i)):(s=i,i=this.element,n=this.widget()),e.each(s,function(s,o){function r(){return t||a.options.disabled!==!0&&!e(this).hasClass("ui-state-disabled")?("string"==typeof o?a[o]:o).apply(a,arguments):void 0}"string"!=typeof o&&(r.guid=o.guid=o.guid||r.guid||e.guid++);var h=s.match(/^([\w:-]*)\s*(.*)$/),l=h[1]+a.eventNamespace,u=h[2];u?n.delegate(u,l,r):i.bind(l,r)})},_off:function(t,i){i=(i||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,t.unbind(i).undelegate(i),this.bindings=e(this.bindings.not(t).get()),this.focusable=e(this.focusable.not(t).get()),this.hoverable=e(this.hoverable.not(t).get())},_delay:function(e,t){function i(){return("string"==typeof e?s[e]:e).apply(s,arguments)}var s=this;return setTimeout(i,t||0)},_hoverable:function(t){this.hoverable=this.hoverable.add(t),this._on(t,{mouseenter:function(t){e(t.currentTarget).addClass("ui-state-hover")},mouseleave:function(t){e(t.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(t){this.focusable=this.focusable.add(t),this._on(t,{focusin:function(t){e(t.currentTarget).addClass("ui-state-focus")},focusout:function(t){e(t.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(t,i,s){var n,a,o=this.options[t];if(s=s||{},i=e.Event(i),i.type=(t===this.widgetEventPrefix?t:this.widgetEventPrefix+t).toLowerCase(),i.target=this.element[0],a=i.originalEvent)for(n in a)n in i||(i[n]=a[n]);return this.element.trigger(i,s),!(e.isFunction(o)&&o.apply(this.element[0],[i].concat(s))===!1||i.isDefaultPrevented())}},e.each({show:"fadeIn",hide:"fadeOut"},function(t,i){e.Widget.prototype["_"+t]=function(s,n,a){"string"==typeof n&&(n={effect:n});var o,r=n?n===!0||"number"==typeof n?i:n.effect||i:t;n=n||{},"number"==typeof n&&(n={duration:n}),o=!e.isEmptyObject(n),n.complete=a,n.delay&&s.delay(n.delay),o&&e.effects&&e.effects.effect[r]?s[t](n):r!==t&&s[r]?s[r](n.duration,n.easing,a):s.queue(function(i){e(this)[t](),a&&a.call(s[0]),i()})}}),e.widget;var d=!1;e(document).mouseup(function(){d=!1}),e.widget("ui.mouse",{version:"1.11.4",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function(){var t=this;this.element.bind("mousedown."+this.widgetName,function(e){return t._mouseDown(e)}).bind("click."+this.widgetName,function(i){return!0===e.data(i.target,t.widgetName+".preventClickEvent")?(e.removeData(i.target,t.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1):void 0}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),this._mouseMoveDelegate&&this.document.unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(t){if(!d){this._mouseMoved=!1,this._mouseStarted&&this._mouseUp(t),this._mouseDownEvent=t;var i=this,s=1===t.which,n="string"==typeof this.options.cancel&&t.target.nodeName?e(t.target).closest(this.options.cancel).length:!1;return s&&!n&&this._mouseCapture(t)?(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){i.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(t)&&this._mouseDelayMet(t)&&(this._mouseStarted=this._mouseStart(t)!==!1,!this._mouseStarted)?(t.preventDefault(),!0):(!0===e.data(t.target,this.widgetName+".preventClickEvent")&&e.removeData(t.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(e){return i._mouseMove(e)},this._mouseUpDelegate=function(e){return i._mouseUp(e)},this.document.bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),t.preventDefault(),d=!0,!0)):!0}},_mouseMove:function(t){if(this._mouseMoved){if(e.ui.ie&&(!document.documentMode||9>document.documentMode)&&!t.button)return this._mouseUp(t);if(!t.which)return this._mouseUp(t)}return(t.which||t.button)&&(this._mouseMoved=!0),this._mouseStarted?(this._mouseDrag(t),t.preventDefault()):(this._mouseDistanceMet(t)&&this._mouseDelayMet(t)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,t)!==!1,this._mouseStarted?this._mouseDrag(t):this._mouseUp(t)),!this._mouseStarted)},_mouseUp:function(t){return this.document.unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,t.target===this._mouseDownEvent.target&&e.data(t.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(t)),d=!1,!1},_mouseDistanceMet:function(e){return Math.max(Math.abs(this._mouseDownEvent.pageX-e.pageX),Math.abs(this._mouseDownEvent.pageY-e.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}}),function(){function t(e,t,i){return[parseFloat(e[0])*(p.test(e[0])?t/100:1),parseFloat(e[1])*(p.test(e[1])?i/100:1)]}function i(t,i){return parseInt(e.css(t,i),10)||0}function s(t){var i=t[0];return 9===i.nodeType?{width:t.width(),height:t.height(),offset:{top:0,left:0}}:e.isWindow(i)?{width:t.width(),height:t.height(),offset:{top:t.scrollTop(),left:t.scrollLeft()}}:i.preventDefault?{width:0,height:0,offset:{top:i.pageY,left:i.pageX}}:{width:t.outerWidth(),height:t.outerHeight(),offset:t.offset()}}e.ui=e.ui||{};var n,a,o=Math.max,r=Math.abs,h=Math.round,l=/left|center|right/,u=/top|center|bottom/,d=/[\+\-]\d+(\.[\d]+)?%?/,c=/^\w+/,p=/%$/,f=e.fn.position;e.position={scrollbarWidth:function(){if(void 0!==n)return n;var t,i,s=e("<div style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),a=s.children()[0];return e("body").append(s),t=a.offsetWidth,s.css("overflow","scroll"),i=a.offsetWidth,t===i&&(i=s[0].clientWidth),s.remove(),n=t-i},getScrollInfo:function(t){var i=t.isWindow||t.isDocument?"":t.element.css("overflow-x"),s=t.isWindow||t.isDocument?"":t.element.css("overflow-y"),n="scroll"===i||"auto"===i&&t.width<t.element[0].scrollWidth,a="scroll"===s||"auto"===s&&t.height<t.element[0].scrollHeight;return{width:a?e.position.scrollbarWidth():0,height:n?e.position.scrollbarWidth():0}},getWithinInfo:function(t){var i=e(t||window),s=e.isWindow(i[0]),n=!!i[0]&&9===i[0].nodeType;return{element:i,isWindow:s,isDocument:n,offset:i.offset()||{left:0,top:0},scrollLeft:i.scrollLeft(),scrollTop:i.scrollTop(),width:s||n?i.width():i.outerWidth(),height:s||n?i.height():i.outerHeight()}}},e.fn.position=function(n){if(!n||!n.of)return f.apply(this,arguments);n=e.extend({},n);var p,m,g,v,y,b,_=e(n.of),x=e.position.getWithinInfo(n.within),w=e.position.getScrollInfo(x),k=(n.collision||"flip").split(" "),T={};return b=s(_),_[0].preventDefault&&(n.at="left top"),m=b.width,g=b.height,v=b.offset,y=e.extend({},v),e.each(["my","at"],function(){var e,t,i=(n[this]||"").split(" ");1===i.length&&(i=l.test(i[0])?i.concat(["center"]):u.test(i[0])?["center"].concat(i):["center","center"]),i[0]=l.test(i[0])?i[0]:"center",i[1]=u.test(i[1])?i[1]:"center",e=d.exec(i[0]),t=d.exec(i[1]),T[this]=[e?e[0]:0,t?t[0]:0],n[this]=[c.exec(i[0])[0],c.exec(i[1])[0]]}),1===k.length&&(k[1]=k[0]),"right"===n.at[0]?y.left+=m:"center"===n.at[0]&&(y.left+=m/2),"bottom"===n.at[1]?y.top+=g:"center"===n.at[1]&&(y.top+=g/2),p=t(T.at,m,g),y.left+=p[0],y.top+=p[1],this.each(function(){var s,l,u=e(this),d=u.outerWidth(),c=u.outerHeight(),f=i(this,"marginLeft"),b=i(this,"marginTop"),D=d+f+i(this,"marginRight")+w.width,S=c+b+i(this,"marginBottom")+w.height,M=e.extend({},y),C=t(T.my,u.outerWidth(),u.outerHeight());"right"===n.my[0]?M.left-=d:"center"===n.my[0]&&(M.left-=d/2),"bottom"===n.my[1]?M.top-=c:"center"===n.my[1]&&(M.top-=c/2),M.left+=C[0],M.top+=C[1],a||(M.left=h(M.left),M.top=h(M.top)),s={marginLeft:f,marginTop:b},e.each(["left","top"],function(t,i){e.ui.position[k[t]]&&e.ui.position[k[t]][i](M,{targetWidth:m,targetHeight:g,elemWidth:d,elemHeight:c,collisionPosition:s,collisionWidth:D,collisionHeight:S,offset:[p[0]+C[0],p[1]+C[1]],my:n.my,at:n.at,within:x,elem:u})}),n.using&&(l=function(e){var t=v.left-M.left,i=t+m-d,s=v.top-M.top,a=s+g-c,h={target:{element:_,left:v.left,top:v.top,width:m,height:g},element:{element:u,left:M.left,top:M.top,width:d,height:c},horizontal:0>i?"left":t>0?"right":"center",vertical:0>a?"top":s>0?"bottom":"middle"};d>m&&m>r(t+i)&&(h.horizontal="center"),c>g&&g>r(s+a)&&(h.vertical="middle"),h.important=o(r(t),r(i))>o(r(s),r(a))?"horizontal":"vertical",n.using.call(this,e,h)}),u.offset(e.extend(M,{using:l}))})},e.ui.position={fit:{left:function(e,t){var i,s=t.within,n=s.isWindow?s.scrollLeft:s.offset.left,a=s.width,r=e.left-t.collisionPosition.marginLeft,h=n-r,l=r+t.collisionWidth-a-n;t.collisionWidth>a?h>0&&0>=l?(i=e.left+h+t.collisionWidth-a-n,e.left+=h-i):e.left=l>0&&0>=h?n:h>l?n+a-t.collisionWidth:n:h>0?e.left+=h:l>0?e.left-=l:e.left=o(e.left-r,e.left)},top:function(e,t){var i,s=t.within,n=s.isWindow?s.scrollTop:s.offset.top,a=t.within.height,r=e.top-t.collisionPosition.marginTop,h=n-r,l=r+t.collisionHeight-a-n;t.collisionHeight>a?h>0&&0>=l?(i=e.top+h+t.collisionHeight-a-n,e.top+=h-i):e.top=l>0&&0>=h?n:h>l?n+a-t.collisionHeight:n:h>0?e.top+=h:l>0?e.top-=l:e.top=o(e.top-r,e.top)}},flip:{left:function(e,t){var i,s,n=t.within,a=n.offset.left+n.scrollLeft,o=n.width,h=n.isWindow?n.scrollLeft:n.offset.left,l=e.left-t.collisionPosition.marginLeft,u=l-h,d=l+t.collisionWidth-o-h,c="left"===t.my[0]?-t.elemWidth:"right"===t.my[0]?t.elemWidth:0,p="left"===t.at[0]?t.targetWidth:"right"===t.at[0]?-t.targetWidth:0,f=-2*t.offset[0];0>u?(i=e.left+c+p+f+t.collisionWidth-o-a,(0>i||r(u)>i)&&(e.left+=c+p+f)):d>0&&(s=e.left-t.collisionPosition.marginLeft+c+p+f-h,(s>0||d>r(s))&&(e.left+=c+p+f))},top:function(e,t){var i,s,n=t.within,a=n.offset.top+n.scrollTop,o=n.height,h=n.isWindow?n.scrollTop:n.offset.top,l=e.top-t.collisionPosition.marginTop,u=l-h,d=l+t.collisionHeight-o-h,c="top"===t.my[1],p=c?-t.elemHeight:"bottom"===t.my[1]?t.elemHeight:0,f="top"===t.at[1]?t.targetHeight:"bottom"===t.at[1]?-t.targetHeight:0,m=-2*t.offset[1];0>u?(s=e.top+p+f+m+t.collisionHeight-o-a,(0>s||r(u)>s)&&(e.top+=p+f+m)):d>0&&(i=e.top-t.collisionPosition.marginTop+p+f+m-h,(i>0||d>r(i))&&(e.top+=p+f+m))}},flipfit:{left:function(){e.ui.position.flip.left.apply(this,arguments),e.ui.position.fit.left.apply(this,arguments)},top:function(){e.ui.position.flip.top.apply(this,arguments),e.ui.position.fit.top.apply(this,arguments)}}},function(){var t,i,s,n,o,r=document.getElementsByTagName("body")[0],h=document.createElement("div");t=document.createElement(r?"div":"body"),s={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"},r&&e.extend(s,{position:"absolute",left:"-1000px",top:"-1000px"});for(o in s)t.style[o]=s[o];t.appendChild(h),i=r||document.documentElement,i.insertBefore(t,i.firstChild),h.style.cssText="position: absolute; left: 10.7432222px;",n=e(h).offset().left,a=n>10&&11>n,t.innerHTML="",i.removeChild(t)}()}(),e.ui.position,e.widget("ui.draggable",e.ui.mouse,{version:"1.11.4",widgetEventPrefix:"drag",options:{addClasses:!0,appendTo:"parent",axis:!1,connectToSortable:!1,containment:!1,cursor:"auto",cursorAt:!1,grid:!1,handle:!1,helper:"original",iframeFix:!1,opacity:!1,refreshPositions:!1,revert:!1,revertDuration:500,scope:"default",scroll:!0,scrollSensitivity:20,scrollSpeed:20,snap:!1,snapMode:"both",snapTolerance:20,stack:!1,zIndex:!1,drag:null,start:null,stop:null},_create:function(){"original"===this.options.helper&&this._setPositionRelative(),this.options.addClasses&&this.element.addClass("ui-draggable"),this.options.disabled&&this.element.addClass("ui-draggable-disabled"),this._setHandleClassName(),this._mouseInit()},_setOption:function(e,t){this._super(e,t),"handle"===e&&(this._removeHandleClassName(),this._setHandleClassName())},_destroy:function(){return(this.helper||this.element).is(".ui-draggable-dragging")?(this.destroyOnClear=!0,void 0):(this.element.removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled"),this._removeHandleClassName(),this._mouseDestroy(),void 0)},_mouseCapture:function(t){var i=this.options;return this._blurActiveElement(t),this.helper||i.disabled||e(t.target).closest(".ui-resizable-handle").length>0?!1:(this.handle=this._getHandle(t),this.handle?(this._blockFrames(i.iframeFix===!0?"iframe":i.iframeFix),!0):!1)},_blockFrames:function(t){this.iframeBlocks=this.document.find(t).map(function(){var t=e(this);return e("<div>").css("position","absolute").appendTo(t.parent()).outerWidth(t.outerWidth()).outerHeight(t.outerHeight()).offset(t.offset())[0]})},_unblockFrames:function(){this.iframeBlocks&&(this.iframeBlocks.remove(),delete this.iframeBlocks)},_blurActiveElement:function(t){var i=this.document[0];if(this.handleElement.is(t.target))try{i.activeElement&&"body"!==i.activeElement.nodeName.toLowerCase()&&e(i.activeElement).blur()}catch(s){}},_mouseStart:function(t){var i=this.options;return this.helper=this._createHelper(t),this.helper.addClass("ui-draggable-dragging"),this._cacheHelperProportions(),e.ui.ddmanager&&(e.ui.ddmanager.current=this),this._cacheMargins(),this.cssPosition=this.helper.css("position"),this.scrollParent=this.helper.scrollParent(!0),this.offsetParent=this.helper.offsetParent(),this.hasFixedAncestor=this.helper.parents().filter(function(){return"fixed"===e(this).css("position")}).length>0,this.positionAbs=this.element.offset(),this._refreshOffsets(t),this.originalPosition=this.position=this._generatePosition(t,!1),this.originalPageX=t.pageX,this.originalPageY=t.pageY,i.cursorAt&&this._adjustOffsetFromHelper(i.cursorAt),this._setContainment(),this._trigger("start",t)===!1?(this._clear(),!1):(this._cacheHelperProportions(),e.ui.ddmanager&&!i.dropBehaviour&&e.ui.ddmanager.prepareOffsets(this,t),this._normalizeRightBottom(),this._mouseDrag(t,!0),e.ui.ddmanager&&e.ui.ddmanager.dragStart(this,t),!0)},_refreshOffsets:function(e){this.offset={top:this.positionAbs.top-this.margins.top,left:this.positionAbs.left-this.margins.left,scroll:!1,parent:this._getParentOffset(),relative:this._getRelativeOffset()},this.offset.click={left:e.pageX-this.offset.left,top:e.pageY-this.offset.top}},_mouseDrag:function(t,i){if(this.hasFixedAncestor&&(this.offset.parent=this._getParentOffset()),this.position=this._generatePosition(t,!0),this.positionAbs=this._convertPositionTo("absolute"),!i){var s=this._uiHash();if(this._trigger("drag",t,s)===!1)return this._mouseUp({}),!1;this.position=s.position}return this.helper[0].style.left=this.position.left+"px",this.helper[0].style.top=this.position.top+"px",e.ui.ddmanager&&e.ui.ddmanager.drag(this,t),!1},_mouseStop:function(t){var i=this,s=!1;return e.ui.ddmanager&&!this.options.dropBehaviour&&(s=e.ui.ddmanager.drop(this,t)),this.dropped&&(s=this.dropped,this.dropped=!1),"invalid"===this.options.revert&&!s||"valid"===this.options.revert&&s||this.options.revert===!0||e.isFunction(this.options.revert)&&this.options.revert.call(this.element,s)?e(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){i._trigger("stop",t)!==!1&&i._clear()}):this._trigger("stop",t)!==!1&&this._clear(),!1},_mouseUp:function(t){return this._unblockFrames(),e.ui.ddmanager&&e.ui.ddmanager.dragStop(this,t),this.handleElement.is(t.target)&&this.element.focus(),e.ui.mouse.prototype._mouseUp.call(this,t)},cancel:function(){return this.helper.is(".ui-draggable-dragging")?this._mouseUp({}):this._clear(),this},_getHandle:function(t){return this.options.handle?!!e(t.target).closest(this.element.find(this.options.handle)).length:!0},_setHandleClassName:function(){this.handleElement=this.options.handle?this.element.find(this.options.handle):this.element,this.handleElement.addClass("ui-draggable-handle")},_removeHandleClassName:function(){this.handleElement.removeClass("ui-draggable-handle")},_createHelper:function(t){var i=this.options,s=e.isFunction(i.helper),n=s?e(i.helper.apply(this.element[0],[t])):"clone"===i.helper?this.element.clone().removeAttr("id"):this.element;return n.parents("body").length||n.appendTo("parent"===i.appendTo?this.element[0].parentNode:i.appendTo),s&&n[0]===this.element[0]&&this._setPositionRelative(),n[0]===this.element[0]||/(fixed|absolute)/.test(n.css("position"))||n.css("position","absolute"),n},_setPositionRelative:function(){/^(?:r|a|f)/.test(this.element.css("position"))||(this.element[0].style.position="relative")},_adjustOffsetFromHelper:function(t){"string"==typeof t&&(t=t.split(" ")),e.isArray(t)&&(t={left:+t[0],top:+t[1]||0}),"left"in t&&(this.offset.click.left=t.left+this.margins.left),"right"in t&&(this.offset.click.left=this.helperProportions.width-t.right+this.margins.left),"top"in t&&(this.offset.click.top=t.top+this.margins.top),"bottom"in t&&(this.offset.click.top=this.helperProportions.height-t.bottom+this.margins.top)},_isRootNode:function(e){return/(html|body)/i.test(e.tagName)||e===this.document[0]},_getParentOffset:function(){var t=this.offsetParent.offset(),i=this.document[0];return"absolute"===this.cssPosition&&this.scrollParent[0]!==i&&e.contains(this.scrollParent[0],this.offsetParent[0])&&(t.left+=this.scrollParent.scrollLeft(),t.top+=this.scrollParent.scrollTop()),this._isRootNode(this.offsetParent[0])&&(t={top:0,left:0}),{top:t.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:t.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"!==this.cssPosition)return{top:0,left:0};var e=this.element.position(),t=this._isRootNode(this.scrollParent[0]);return{top:e.top-(parseInt(this.helper.css("top"),10)||0)+(t?0:this.scrollParent.scrollTop()),left:e.left-(parseInt(this.helper.css("left"),10)||0)+(t?0:this.scrollParent.scrollLeft())}},_cacheMargins:function(){this.margins={left:parseInt(this.element.css("marginLeft"),10)||0,top:parseInt(this.element.css("marginTop"),10)||0,right:parseInt(this.element.css("marginRight"),10)||0,bottom:parseInt(this.element.css("marginBottom"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var t,i,s,n=this.options,a=this.document[0];return this.relativeContainer=null,n.containment?"window"===n.containment?(this.containment=[e(window).scrollLeft()-this.offset.relative.left-this.offset.parent.left,e(window).scrollTop()-this.offset.relative.top-this.offset.parent.top,e(window).scrollLeft()+e(window).width()-this.helperProportions.width-this.margins.left,e(window).scrollTop()+(e(window).height()||a.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top],void 0):"document"===n.containment?(this.containment=[0,0,e(a).width()-this.helperProportions.width-this.margins.left,(e(a).height()||a.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top],void 0):n.containment.constructor===Array?(this.containment=n.containment,void 0):("parent"===n.containment&&(n.containment=this.helper[0].parentNode),i=e(n.containment),s=i[0],s&&(t=/(scroll|auto)/.test(i.css("overflow")),this.containment=[(parseInt(i.css("borderLeftWidth"),10)||0)+(parseInt(i.css("paddingLeft"),10)||0),(parseInt(i.css("borderTopWidth"),10)||0)+(parseInt(i.css("paddingTop"),10)||0),(t?Math.max(s.scrollWidth,s.offsetWidth):s.offsetWidth)-(parseInt(i.css("borderRightWidth"),10)||0)-(parseInt(i.css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left-this.margins.right,(t?Math.max(s.scrollHeight,s.offsetHeight):s.offsetHeight)-(parseInt(i.css("borderBottomWidth"),10)||0)-(parseInt(i.css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top-this.margins.bottom],this.relativeContainer=i),void 0):(this.containment=null,void 0)},_convertPositionTo:function(e,t){t||(t=this.position);var i="absolute"===e?1:-1,s=this._isRootNode(this.scrollParent[0]);return{top:t.top+this.offset.relative.top*i+this.offset.parent.top*i-("fixed"===this.cssPosition?-this.offset.scroll.top:s?0:this.offset.scroll.top)*i,left:t.left+this.offset.relative.left*i+this.offset.parent.left*i-("fixed"===this.cssPosition?-this.offset.scroll.left:s?0:this.offset.scroll.left)*i}},_generatePosition:function(e,t){var i,s,n,a,o=this.options,r=this._isRootNode(this.scrollParent[0]),h=e.pageX,l=e.pageY;return r&&this.offset.scroll||(this.offset.scroll={top:this.scrollParent.scrollTop(),left:this.scrollParent.scrollLeft()}),t&&(this.containment&&(this.relativeContainer?(s=this.relativeContainer.offset(),i=[this.containment[0]+s.left,this.containment[1]+s.top,this.containment[2]+s.left,this.containment[3]+s.top]):i=this.containment,e.pageX-this.offset.click.left<i[0]&&(h=i[0]+this.offset.click.left),e.pageY-this.offset.click.top<i[1]&&(l=i[1]+this.offset.click.top),e.pageX-this.offset.click.left>i[2]&&(h=i[2]+this.offset.click.left),e.pageY-this.offset.click.top>i[3]&&(l=i[3]+this.offset.click.top)),o.grid&&(n=o.grid[1]?this.originalPageY+Math.round((l-this.originalPageY)/o.grid[1])*o.grid[1]:this.originalPageY,l=i?n-this.offset.click.top>=i[1]||n-this.offset.click.top>i[3]?n:n-this.offset.click.top>=i[1]?n-o.grid[1]:n+o.grid[1]:n,a=o.grid[0]?this.originalPageX+Math.round((h-this.originalPageX)/o.grid[0])*o.grid[0]:this.originalPageX,h=i?a-this.offset.click.left>=i[0]||a-this.offset.click.left>i[2]?a:a-this.offset.click.left>=i[0]?a-o.grid[0]:a+o.grid[0]:a),"y"===o.axis&&(h=this.originalPageX),"x"===o.axis&&(l=this.originalPageY)),{top:l-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.offset.scroll.top:r?0:this.offset.scroll.top),left:h-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.offset.scroll.left:r?0:this.offset.scroll.left)}
},_clear:function(){this.helper.removeClass("ui-draggable-dragging"),this.helper[0]===this.element[0]||this.cancelHelperRemoval||this.helper.remove(),this.helper=null,this.cancelHelperRemoval=!1,this.destroyOnClear&&this.destroy()},_normalizeRightBottom:function(){"y"!==this.options.axis&&"auto"!==this.helper.css("right")&&(this.helper.width(this.helper.width()),this.helper.css("right","auto")),"x"!==this.options.axis&&"auto"!==this.helper.css("bottom")&&(this.helper.height(this.helper.height()),this.helper.css("bottom","auto"))},_trigger:function(t,i,s){return s=s||this._uiHash(),e.ui.plugin.call(this,t,[i,s,this],!0),/^(drag|start|stop)/.test(t)&&(this.positionAbs=this._convertPositionTo("absolute"),s.offset=this.positionAbs),e.Widget.prototype._trigger.call(this,t,i,s)},plugins:{},_uiHash:function(){return{helper:this.helper,position:this.position,originalPosition:this.originalPosition,offset:this.positionAbs}}}),e.ui.plugin.add("draggable","connectToSortable",{start:function(t,i,s){var n=e.extend({},i,{item:s.element});s.sortables=[],e(s.options.connectToSortable).each(function(){var i=e(this).sortable("instance");i&&!i.options.disabled&&(s.sortables.push(i),i.refreshPositions(),i._trigger("activate",t,n))})},stop:function(t,i,s){var n=e.extend({},i,{item:s.element});s.cancelHelperRemoval=!1,e.each(s.sortables,function(){var e=this;e.isOver?(e.isOver=0,s.cancelHelperRemoval=!0,e.cancelHelperRemoval=!1,e._storedCSS={position:e.placeholder.css("position"),top:e.placeholder.css("top"),left:e.placeholder.css("left")},e._mouseStop(t),e.options.helper=e.options._helper):(e.cancelHelperRemoval=!0,e._trigger("deactivate",t,n))})},drag:function(t,i,s){e.each(s.sortables,function(){var n=!1,a=this;a.positionAbs=s.positionAbs,a.helperProportions=s.helperProportions,a.offset.click=s.offset.click,a._intersectsWith(a.containerCache)&&(n=!0,e.each(s.sortables,function(){return this.positionAbs=s.positionAbs,this.helperProportions=s.helperProportions,this.offset.click=s.offset.click,this!==a&&this._intersectsWith(this.containerCache)&&e.contains(a.element[0],this.element[0])&&(n=!1),n})),n?(a.isOver||(a.isOver=1,s._parent=i.helper.parent(),a.currentItem=i.helper.appendTo(a.element).data("ui-sortable-item",!0),a.options._helper=a.options.helper,a.options.helper=function(){return i.helper[0]},t.target=a.currentItem[0],a._mouseCapture(t,!0),a._mouseStart(t,!0,!0),a.offset.click.top=s.offset.click.top,a.offset.click.left=s.offset.click.left,a.offset.parent.left-=s.offset.parent.left-a.offset.parent.left,a.offset.parent.top-=s.offset.parent.top-a.offset.parent.top,s._trigger("toSortable",t),s.dropped=a.element,e.each(s.sortables,function(){this.refreshPositions()}),s.currentItem=s.element,a.fromOutside=s),a.currentItem&&(a._mouseDrag(t),i.position=a.position)):a.isOver&&(a.isOver=0,a.cancelHelperRemoval=!0,a.options._revert=a.options.revert,a.options.revert=!1,a._trigger("out",t,a._uiHash(a)),a._mouseStop(t,!0),a.options.revert=a.options._revert,a.options.helper=a.options._helper,a.placeholder&&a.placeholder.remove(),i.helper.appendTo(s._parent),s._refreshOffsets(t),i.position=s._generatePosition(t,!0),s._trigger("fromSortable",t),s.dropped=!1,e.each(s.sortables,function(){this.refreshPositions()}))})}}),e.ui.plugin.add("draggable","cursor",{start:function(t,i,s){var n=e("body"),a=s.options;n.css("cursor")&&(a._cursor=n.css("cursor")),n.css("cursor",a.cursor)},stop:function(t,i,s){var n=s.options;n._cursor&&e("body").css("cursor",n._cursor)}}),e.ui.plugin.add("draggable","opacity",{start:function(t,i,s){var n=e(i.helper),a=s.options;n.css("opacity")&&(a._opacity=n.css("opacity")),n.css("opacity",a.opacity)},stop:function(t,i,s){var n=s.options;n._opacity&&e(i.helper).css("opacity",n._opacity)}}),e.ui.plugin.add("draggable","scroll",{start:function(e,t,i){i.scrollParentNotHidden||(i.scrollParentNotHidden=i.helper.scrollParent(!1)),i.scrollParentNotHidden[0]!==i.document[0]&&"HTML"!==i.scrollParentNotHidden[0].tagName&&(i.overflowOffset=i.scrollParentNotHidden.offset())},drag:function(t,i,s){var n=s.options,a=!1,o=s.scrollParentNotHidden[0],r=s.document[0];o!==r&&"HTML"!==o.tagName?(n.axis&&"x"===n.axis||(s.overflowOffset.top+o.offsetHeight-t.pageY<n.scrollSensitivity?o.scrollTop=a=o.scrollTop+n.scrollSpeed:t.pageY-s.overflowOffset.top<n.scrollSensitivity&&(o.scrollTop=a=o.scrollTop-n.scrollSpeed)),n.axis&&"y"===n.axis||(s.overflowOffset.left+o.offsetWidth-t.pageX<n.scrollSensitivity?o.scrollLeft=a=o.scrollLeft+n.scrollSpeed:t.pageX-s.overflowOffset.left<n.scrollSensitivity&&(o.scrollLeft=a=o.scrollLeft-n.scrollSpeed))):(n.axis&&"x"===n.axis||(t.pageY-e(r).scrollTop()<n.scrollSensitivity?a=e(r).scrollTop(e(r).scrollTop()-n.scrollSpeed):e(window).height()-(t.pageY-e(r).scrollTop())<n.scrollSensitivity&&(a=e(r).scrollTop(e(r).scrollTop()+n.scrollSpeed))),n.axis&&"y"===n.axis||(t.pageX-e(r).scrollLeft()<n.scrollSensitivity?a=e(r).scrollLeft(e(r).scrollLeft()-n.scrollSpeed):e(window).width()-(t.pageX-e(r).scrollLeft())<n.scrollSensitivity&&(a=e(r).scrollLeft(e(r).scrollLeft()+n.scrollSpeed)))),a!==!1&&e.ui.ddmanager&&!n.dropBehaviour&&e.ui.ddmanager.prepareOffsets(s,t)}}),e.ui.plugin.add("draggable","snap",{start:function(t,i,s){var n=s.options;s.snapElements=[],e(n.snap.constructor!==String?n.snap.items||":data(ui-draggable)":n.snap).each(function(){var t=e(this),i=t.offset();this!==s.element[0]&&s.snapElements.push({item:this,width:t.outerWidth(),height:t.outerHeight(),top:i.top,left:i.left})})},drag:function(t,i,s){var n,a,o,r,h,l,u,d,c,p,f=s.options,m=f.snapTolerance,g=i.offset.left,v=g+s.helperProportions.width,y=i.offset.top,b=y+s.helperProportions.height;for(c=s.snapElements.length-1;c>=0;c--)h=s.snapElements[c].left-s.margins.left,l=h+s.snapElements[c].width,u=s.snapElements[c].top-s.margins.top,d=u+s.snapElements[c].height,h-m>v||g>l+m||u-m>b||y>d+m||!e.contains(s.snapElements[c].item.ownerDocument,s.snapElements[c].item)?(s.snapElements[c].snapping&&s.options.snap.release&&s.options.snap.release.call(s.element,t,e.extend(s._uiHash(),{snapItem:s.snapElements[c].item})),s.snapElements[c].snapping=!1):("inner"!==f.snapMode&&(n=m>=Math.abs(u-b),a=m>=Math.abs(d-y),o=m>=Math.abs(h-v),r=m>=Math.abs(l-g),n&&(i.position.top=s._convertPositionTo("relative",{top:u-s.helperProportions.height,left:0}).top),a&&(i.position.top=s._convertPositionTo("relative",{top:d,left:0}).top),o&&(i.position.left=s._convertPositionTo("relative",{top:0,left:h-s.helperProportions.width}).left),r&&(i.position.left=s._convertPositionTo("relative",{top:0,left:l}).left)),p=n||a||o||r,"outer"!==f.snapMode&&(n=m>=Math.abs(u-y),a=m>=Math.abs(d-b),o=m>=Math.abs(h-g),r=m>=Math.abs(l-v),n&&(i.position.top=s._convertPositionTo("relative",{top:u,left:0}).top),a&&(i.position.top=s._convertPositionTo("relative",{top:d-s.helperProportions.height,left:0}).top),o&&(i.position.left=s._convertPositionTo("relative",{top:0,left:h}).left),r&&(i.position.left=s._convertPositionTo("relative",{top:0,left:l-s.helperProportions.width}).left)),!s.snapElements[c].snapping&&(n||a||o||r||p)&&s.options.snap.snap&&s.options.snap.snap.call(s.element,t,e.extend(s._uiHash(),{snapItem:s.snapElements[c].item})),s.snapElements[c].snapping=n||a||o||r||p)}}),e.ui.plugin.add("draggable","stack",{start:function(t,i,s){var n,a=s.options,o=e.makeArray(e(a.stack)).sort(function(t,i){return(parseInt(e(t).css("zIndex"),10)||0)-(parseInt(e(i).css("zIndex"),10)||0)});o.length&&(n=parseInt(e(o[0]).css("zIndex"),10)||0,e(o).each(function(t){e(this).css("zIndex",n+t)}),this.css("zIndex",n+o.length))}}),e.ui.plugin.add("draggable","zIndex",{start:function(t,i,s){var n=e(i.helper),a=s.options;n.css("zIndex")&&(a._zIndex=n.css("zIndex")),n.css("zIndex",a.zIndex)},stop:function(t,i,s){var n=s.options;n._zIndex&&e(i.helper).css("zIndex",n._zIndex)}}),e.ui.draggable,e.widget("ui.droppable",{version:"1.11.4",widgetEventPrefix:"drop",options:{accept:"*",activeClass:!1,addClasses:!0,greedy:!1,hoverClass:!1,scope:"default",tolerance:"intersect",activate:null,deactivate:null,drop:null,out:null,over:null},_create:function(){var t,i=this.options,s=i.accept;this.isover=!1,this.isout=!0,this.accept=e.isFunction(s)?s:function(e){return e.is(s)},this.proportions=function(){return arguments.length?(t=arguments[0],void 0):t?t:t={width:this.element[0].offsetWidth,height:this.element[0].offsetHeight}},this._addToManager(i.scope),i.addClasses&&this.element.addClass("ui-droppable")},_addToManager:function(t){e.ui.ddmanager.droppables[t]=e.ui.ddmanager.droppables[t]||[],e.ui.ddmanager.droppables[t].push(this)},_splice:function(e){for(var t=0;e.length>t;t++)e[t]===this&&e.splice(t,1)},_destroy:function(){var t=e.ui.ddmanager.droppables[this.options.scope];this._splice(t),this.element.removeClass("ui-droppable ui-droppable-disabled")},_setOption:function(t,i){if("accept"===t)this.accept=e.isFunction(i)?i:function(e){return e.is(i)};else if("scope"===t){var s=e.ui.ddmanager.droppables[this.options.scope];this._splice(s),this._addToManager(i)}this._super(t,i)},_activate:function(t){var i=e.ui.ddmanager.current;this.options.activeClass&&this.element.addClass(this.options.activeClass),i&&this._trigger("activate",t,this.ui(i))},_deactivate:function(t){var i=e.ui.ddmanager.current;this.options.activeClass&&this.element.removeClass(this.options.activeClass),i&&this._trigger("deactivate",t,this.ui(i))},_over:function(t){var i=e.ui.ddmanager.current;i&&(i.currentItem||i.element)[0]!==this.element[0]&&this.accept.call(this.element[0],i.currentItem||i.element)&&(this.options.hoverClass&&this.element.addClass(this.options.hoverClass),this._trigger("over",t,this.ui(i)))},_out:function(t){var i=e.ui.ddmanager.current;i&&(i.currentItem||i.element)[0]!==this.element[0]&&this.accept.call(this.element[0],i.currentItem||i.element)&&(this.options.hoverClass&&this.element.removeClass(this.options.hoverClass),this._trigger("out",t,this.ui(i)))},_drop:function(t,i){var s=i||e.ui.ddmanager.current,n=!1;return s&&(s.currentItem||s.element)[0]!==this.element[0]?(this.element.find(":data(ui-droppable)").not(".ui-draggable-dragging").each(function(){var i=e(this).droppable("instance");return i.options.greedy&&!i.options.disabled&&i.options.scope===s.options.scope&&i.accept.call(i.element[0],s.currentItem||s.element)&&e.ui.intersect(s,e.extend(i,{offset:i.element.offset()}),i.options.tolerance,t)?(n=!0,!1):void 0}),n?!1:this.accept.call(this.element[0],s.currentItem||s.element)?(this.options.activeClass&&this.element.removeClass(this.options.activeClass),this.options.hoverClass&&this.element.removeClass(this.options.hoverClass),this._trigger("drop",t,this.ui(s)),this.element):!1):!1},ui:function(e){return{draggable:e.currentItem||e.element,helper:e.helper,position:e.position,offset:e.positionAbs}}}),e.ui.intersect=function(){function e(e,t,i){return e>=t&&t+i>e}return function(t,i,s,n){if(!i.offset)return!1;var a=(t.positionAbs||t.position.absolute).left+t.margins.left,o=(t.positionAbs||t.position.absolute).top+t.margins.top,r=a+t.helperProportions.width,h=o+t.helperProportions.height,l=i.offset.left,u=i.offset.top,d=l+i.proportions().width,c=u+i.proportions().height;switch(s){case"fit":return a>=l&&d>=r&&o>=u&&c>=h;case"intersect":return a+t.helperProportions.width/2>l&&d>r-t.helperProportions.width/2&&o+t.helperProportions.height/2>u&&c>h-t.helperProportions.height/2;case"pointer":return e(n.pageY,u,i.proportions().height)&&e(n.pageX,l,i.proportions().width);case"touch":return(o>=u&&c>=o||h>=u&&c>=h||u>o&&h>c)&&(a>=l&&d>=a||r>=l&&d>=r||l>a&&r>d);default:return!1}}}(),e.ui.ddmanager={current:null,droppables:{"default":[]},prepareOffsets:function(t,i){var s,n,a=e.ui.ddmanager.droppables[t.options.scope]||[],o=i?i.type:null,r=(t.currentItem||t.element).find(":data(ui-droppable)").addBack();e:for(s=0;a.length>s;s++)if(!(a[s].options.disabled||t&&!a[s].accept.call(a[s].element[0],t.currentItem||t.element))){for(n=0;r.length>n;n++)if(r[n]===a[s].element[0]){a[s].proportions().height=0;continue e}a[s].visible="none"!==a[s].element.css("display"),a[s].visible&&("mousedown"===o&&a[s]._activate.call(a[s],i),a[s].offset=a[s].element.offset(),a[s].proportions({width:a[s].element[0].offsetWidth,height:a[s].element[0].offsetHeight}))}},drop:function(t,i){var s=!1;return e.each((e.ui.ddmanager.droppables[t.options.scope]||[]).slice(),function(){this.options&&(!this.options.disabled&&this.visible&&e.ui.intersect(t,this,this.options.tolerance,i)&&(s=this._drop.call(this,i)||s),!this.options.disabled&&this.visible&&this.accept.call(this.element[0],t.currentItem||t.element)&&(this.isout=!0,this.isover=!1,this._deactivate.call(this,i)))}),s},dragStart:function(t,i){t.element.parentsUntil("body").bind("scroll.droppable",function(){t.options.refreshPositions||e.ui.ddmanager.prepareOffsets(t,i)})},drag:function(t,i){t.options.refreshPositions&&e.ui.ddmanager.prepareOffsets(t,i),e.each(e.ui.ddmanager.droppables[t.options.scope]||[],function(){if(!this.options.disabled&&!this.greedyChild&&this.visible){var s,n,a,o=e.ui.intersect(t,this,this.options.tolerance,i),r=!o&&this.isover?"isout":o&&!this.isover?"isover":null;r&&(this.options.greedy&&(n=this.options.scope,a=this.element.parents(":data(ui-droppable)").filter(function(){return e(this).droppable("instance").options.scope===n}),a.length&&(s=e(a[0]).droppable("instance"),s.greedyChild="isover"===r)),s&&"isover"===r&&(s.isover=!1,s.isout=!0,s._out.call(s,i)),this[r]=!0,this["isout"===r?"isover":"isout"]=!1,this["isover"===r?"_over":"_out"].call(this,i),s&&"isout"===r&&(s.isout=!1,s.isover=!0,s._over.call(s,i)))}})},dragStop:function(t,i){t.element.parentsUntil("body").unbind("scroll.droppable"),t.options.refreshPositions||e.ui.ddmanager.prepareOffsets(t,i)}},e.ui.droppable,e.widget("ui.resizable",e.ui.mouse,{version:"1.11.4",widgetEventPrefix:"resize",options:{alsoResize:!1,animate:!1,animateDuration:"slow",animateEasing:"swing",aspectRatio:!1,autoHide:!1,containment:!1,ghost:!1,grid:!1,handles:"e,s,se",helper:!1,maxHeight:null,maxWidth:null,minHeight:10,minWidth:10,zIndex:90,resize:null,start:null,stop:null},_num:function(e){return parseInt(e,10)||0},_isNumber:function(e){return!isNaN(parseInt(e,10))},_hasScroll:function(t,i){if("hidden"===e(t).css("overflow"))return!1;var s=i&&"left"===i?"scrollLeft":"scrollTop",n=!1;return t[s]>0?!0:(t[s]=1,n=t[s]>0,t[s]=0,n)},_create:function(){var t,i,s,n,a,o=this,r=this.options;if(this.element.addClass("ui-resizable"),e.extend(this,{_aspectRatio:!!r.aspectRatio,aspectRatio:r.aspectRatio,originalElement:this.element,_proportionallyResizeElements:[],_helper:r.helper||r.ghost||r.animate?r.helper||"ui-resizable-helper":null}),this.element[0].nodeName.match(/^(canvas|textarea|input|select|button|img)$/i)&&(this.element.wrap(e("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({position:this.element.css("position"),width:this.element.outerWidth(),height:this.element.outerHeight(),top:this.element.css("top"),left:this.element.css("left")})),this.element=this.element.parent().data("ui-resizable",this.element.resizable("instance")),this.elementIsWrapper=!0,this.element.css({marginLeft:this.originalElement.css("marginLeft"),marginTop:this.originalElement.css("marginTop"),marginRight:this.originalElement.css("marginRight"),marginBottom:this.originalElement.css("marginBottom")}),this.originalElement.css({marginLeft:0,marginTop:0,marginRight:0,marginBottom:0}),this.originalResizeStyle=this.originalElement.css("resize"),this.originalElement.css("resize","none"),this._proportionallyResizeElements.push(this.originalElement.css({position:"static",zoom:1,display:"block"})),this.originalElement.css({margin:this.originalElement.css("margin")}),this._proportionallyResize()),this.handles=r.handles||(e(".ui-resizable-handle",this.element).length?{n:".ui-resizable-n",e:".ui-resizable-e",s:".ui-resizable-s",w:".ui-resizable-w",se:".ui-resizable-se",sw:".ui-resizable-sw",ne:".ui-resizable-ne",nw:".ui-resizable-nw"}:"e,s,se"),this._handles=e(),this.handles.constructor===String)for("all"===this.handles&&(this.handles="n,e,s,w,se,sw,ne,nw"),t=this.handles.split(","),this.handles={},i=0;t.length>i;i++)s=e.trim(t[i]),a="ui-resizable-"+s,n=e("<div class='ui-resizable-handle "+a+"'></div>"),n.css({zIndex:r.zIndex}),"se"===s&&n.addClass("ui-icon ui-icon-gripsmall-diagonal-se"),this.handles[s]=".ui-resizable-"+s,this.element.append(n);this._renderAxis=function(t){var i,s,n,a;t=t||this.element;for(i in this.handles)this.handles[i].constructor===String?this.handles[i]=this.element.children(this.handles[i]).first().show():(this.handles[i].jquery||this.handles[i].nodeType)&&(this.handles[i]=e(this.handles[i]),this._on(this.handles[i],{mousedown:o._mouseDown})),this.elementIsWrapper&&this.originalElement[0].nodeName.match(/^(textarea|input|select|button)$/i)&&(s=e(this.handles[i],this.element),a=/sw|ne|nw|se|n|s/.test(i)?s.outerHeight():s.outerWidth(),n=["padding",/ne|nw|n/.test(i)?"Top":/se|sw|s/.test(i)?"Bottom":/^e$/.test(i)?"Right":"Left"].join(""),t.css(n,a),this._proportionallyResize()),this._handles=this._handles.add(this.handles[i])},this._renderAxis(this.element),this._handles=this._handles.add(this.element.find(".ui-resizable-handle")),this._handles.disableSelection(),this._handles.mouseover(function(){o.resizing||(this.className&&(n=this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i)),o.axis=n&&n[1]?n[1]:"se")}),r.autoHide&&(this._handles.hide(),e(this.element).addClass("ui-resizable-autohide").mouseenter(function(){r.disabled||(e(this).removeClass("ui-resizable-autohide"),o._handles.show())}).mouseleave(function(){r.disabled||o.resizing||(e(this).addClass("ui-resizable-autohide"),o._handles.hide())})),this._mouseInit()},_destroy:function(){this._mouseDestroy();var t,i=function(t){e(t).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").removeData("ui-resizable").unbind(".resizable").find(".ui-resizable-handle").remove()};return this.elementIsWrapper&&(i(this.element),t=this.element,this.originalElement.css({position:t.css("position"),width:t.outerWidth(),height:t.outerHeight(),top:t.css("top"),left:t.css("left")}).insertAfter(t),t.remove()),this.originalElement.css("resize",this.originalResizeStyle),i(this.originalElement),this},_mouseCapture:function(t){var i,s,n=!1;for(i in this.handles)s=e(this.handles[i])[0],(s===t.target||e.contains(s,t.target))&&(n=!0);return!this.options.disabled&&n},_mouseStart:function(t){var i,s,n,a=this.options,o=this.element;return this.resizing=!0,this._renderProxy(),i=this._num(this.helper.css("left")),s=this._num(this.helper.css("top")),a.containment&&(i+=e(a.containment).scrollLeft()||0,s+=e(a.containment).scrollTop()||0),this.offset=this.helper.offset(),this.position={left:i,top:s},this.size=this._helper?{width:this.helper.width(),height:this.helper.height()}:{width:o.width(),height:o.height()},this.originalSize=this._helper?{width:o.outerWidth(),height:o.outerHeight()}:{width:o.width(),height:o.height()},this.sizeDiff={width:o.outerWidth()-o.width(),height:o.outerHeight()-o.height()},this.originalPosition={left:i,top:s},this.originalMousePosition={left:t.pageX,top:t.pageY},this.aspectRatio="number"==typeof a.aspectRatio?a.aspectRatio:this.originalSize.width/this.originalSize.height||1,n=e(".ui-resizable-"+this.axis).css("cursor"),e("body").css("cursor","auto"===n?this.axis+"-resize":n),o.addClass("ui-resizable-resizing"),this._propagate("start",t),!0},_mouseDrag:function(t){var i,s,n=this.originalMousePosition,a=this.axis,o=t.pageX-n.left||0,r=t.pageY-n.top||0,h=this._change[a];return this._updatePrevProperties(),h?(i=h.apply(this,[t,o,r]),this._updateVirtualBoundaries(t.shiftKey),(this._aspectRatio||t.shiftKey)&&(i=this._updateRatio(i,t)),i=this._respectSize(i,t),this._updateCache(i),this._propagate("resize",t),s=this._applyChanges(),!this._helper&&this._proportionallyResizeElements.length&&this._proportionallyResize(),e.isEmptyObject(s)||(this._updatePrevProperties(),this._trigger("resize",t,this.ui()),this._applyChanges()),!1):!1},_mouseStop:function(t){this.resizing=!1;var i,s,n,a,o,r,h,l=this.options,u=this;return this._helper&&(i=this._proportionallyResizeElements,s=i.length&&/textarea/i.test(i[0].nodeName),n=s&&this._hasScroll(i[0],"left")?0:u.sizeDiff.height,a=s?0:u.sizeDiff.width,o={width:u.helper.width()-a,height:u.helper.height()-n},r=parseInt(u.element.css("left"),10)+(u.position.left-u.originalPosition.left)||null,h=parseInt(u.element.css("top"),10)+(u.position.top-u.originalPosition.top)||null,l.animate||this.element.css(e.extend(o,{top:h,left:r})),u.helper.height(u.size.height),u.helper.width(u.size.width),this._helper&&!l.animate&&this._proportionallyResize()),e("body").css("cursor","auto"),this.element.removeClass("ui-resizable-resizing"),this._propagate("stop",t),this._helper&&this.helper.remove(),!1},_updatePrevProperties:function(){this.prevPosition={top:this.position.top,left:this.position.left},this.prevSize={width:this.size.width,height:this.size.height}},_applyChanges:function(){var e={};return this.position.top!==this.prevPosition.top&&(e.top=this.position.top+"px"),this.position.left!==this.prevPosition.left&&(e.left=this.position.left+"px"),this.size.width!==this.prevSize.width&&(e.width=this.size.width+"px"),this.size.height!==this.prevSize.height&&(e.height=this.size.height+"px"),this.helper.css(e),e},_updateVirtualBoundaries:function(e){var t,i,s,n,a,o=this.options;a={minWidth:this._isNumber(o.minWidth)?o.minWidth:0,maxWidth:this._isNumber(o.maxWidth)?o.maxWidth:1/0,minHeight:this._isNumber(o.minHeight)?o.minHeight:0,maxHeight:this._isNumber(o.maxHeight)?o.maxHeight:1/0},(this._aspectRatio||e)&&(t=a.minHeight*this.aspectRatio,s=a.minWidth/this.aspectRatio,i=a.maxHeight*this.aspectRatio,n=a.maxWidth/this.aspectRatio,t>a.minWidth&&(a.minWidth=t),s>a.minHeight&&(a.minHeight=s),a.maxWidth>i&&(a.maxWidth=i),a.maxHeight>n&&(a.maxHeight=n)),this._vBoundaries=a},_updateCache:function(e){this.offset=this.helper.offset(),this._isNumber(e.left)&&(this.position.left=e.left),this._isNumber(e.top)&&(this.position.top=e.top),this._isNumber(e.height)&&(this.size.height=e.height),this._isNumber(e.width)&&(this.size.width=e.width)},_updateRatio:function(e){var t=this.position,i=this.size,s=this.axis;return this._isNumber(e.height)?e.width=e.height*this.aspectRatio:this._isNumber(e.width)&&(e.height=e.width/this.aspectRatio),"sw"===s&&(e.left=t.left+(i.width-e.width),e.top=null),"nw"===s&&(e.top=t.top+(i.height-e.height),e.left=t.left+(i.width-e.width)),e},_respectSize:function(e){var t=this._vBoundaries,i=this.axis,s=this._isNumber(e.width)&&t.maxWidth&&t.maxWidth<e.width,n=this._isNumber(e.height)&&t.maxHeight&&t.maxHeight<e.height,a=this._isNumber(e.width)&&t.minWidth&&t.minWidth>e.width,o=this._isNumber(e.height)&&t.minHeight&&t.minHeight>e.height,r=this.originalPosition.left+this.originalSize.width,h=this.position.top+this.size.height,l=/sw|nw|w/.test(i),u=/nw|ne|n/.test(i);return a&&(e.width=t.minWidth),o&&(e.height=t.minHeight),s&&(e.width=t.maxWidth),n&&(e.height=t.maxHeight),a&&l&&(e.left=r-t.minWidth),s&&l&&(e.left=r-t.maxWidth),o&&u&&(e.top=h-t.minHeight),n&&u&&(e.top=h-t.maxHeight),e.width||e.height||e.left||!e.top?e.width||e.height||e.top||!e.left||(e.left=null):e.top=null,e},_getPaddingPlusBorderDimensions:function(e){for(var t=0,i=[],s=[e.css("borderTopWidth"),e.css("borderRightWidth"),e.css("borderBottomWidth"),e.css("borderLeftWidth")],n=[e.css("paddingTop"),e.css("paddingRight"),e.css("paddingBottom"),e.css("paddingLeft")];4>t;t++)i[t]=parseInt(s[t],10)||0,i[t]+=parseInt(n[t],10)||0;return{height:i[0]+i[2],width:i[1]+i[3]}},_proportionallyResize:function(){if(this._proportionallyResizeElements.length)for(var e,t=0,i=this.helper||this.element;this._proportionallyResizeElements.length>t;t++)e=this._proportionallyResizeElements[t],this.outerDimensions||(this.outerDimensions=this._getPaddingPlusBorderDimensions(e)),e.css({height:i.height()-this.outerDimensions.height||0,width:i.width()-this.outerDimensions.width||0})},_renderProxy:function(){var t=this.element,i=this.options;this.elementOffset=t.offset(),this._helper?(this.helper=this.helper||e("<div style='overflow:hidden;'></div>"),this.helper.addClass(this._helper).css({width:this.element.outerWidth()-1,height:this.element.outerHeight()-1,position:"absolute",left:this.elementOffset.left+"px",top:this.elementOffset.top+"px",zIndex:++i.zIndex}),this.helper.appendTo("body").disableSelection()):this.helper=this.element},_change:{e:function(e,t){return{width:this.originalSize.width+t}},w:function(e,t){var i=this.originalSize,s=this.originalPosition;return{left:s.left+t,width:i.width-t}},n:function(e,t,i){var s=this.originalSize,n=this.originalPosition;return{top:n.top+i,height:s.height-i}},s:function(e,t,i){return{height:this.originalSize.height+i}},se:function(t,i,s){return e.extend(this._change.s.apply(this,arguments),this._change.e.apply(this,[t,i,s]))},sw:function(t,i,s){return e.extend(this._change.s.apply(this,arguments),this._change.w.apply(this,[t,i,s]))},ne:function(t,i,s){return e.extend(this._change.n.apply(this,arguments),this._change.e.apply(this,[t,i,s]))},nw:function(t,i,s){return e.extend(this._change.n.apply(this,arguments),this._change.w.apply(this,[t,i,s]))}},_propagate:function(t,i){e.ui.plugin.call(this,t,[i,this.ui()]),"resize"!==t&&this._trigger(t,i,this.ui())},plugins:{},ui:function(){return{originalElement:this.originalElement,element:this.element,helper:this.helper,position:this.position,size:this.size,originalSize:this.originalSize,originalPosition:this.originalPosition}}}),e.ui.plugin.add("resizable","animate",{stop:function(t){var i=e(this).resizable("instance"),s=i.options,n=i._proportionallyResizeElements,a=n.length&&/textarea/i.test(n[0].nodeName),o=a&&i._hasScroll(n[0],"left")?0:i.sizeDiff.height,r=a?0:i.sizeDiff.width,h={width:i.size.width-r,height:i.size.height-o},l=parseInt(i.element.css("left"),10)+(i.position.left-i.originalPosition.left)||null,u=parseInt(i.element.css("top"),10)+(i.position.top-i.originalPosition.top)||null;i.element.animate(e.extend(h,u&&l?{top:u,left:l}:{}),{duration:s.animateDuration,easing:s.animateEasing,step:function(){var s={width:parseInt(i.element.css("width"),10),height:parseInt(i.element.css("height"),10),top:parseInt(i.element.css("top"),10),left:parseInt(i.element.css("left"),10)};n&&n.length&&e(n[0]).css({width:s.width,height:s.height}),i._updateCache(s),i._propagate("resize",t)}})}}),e.ui.plugin.add("resizable","containment",{start:function(){var t,i,s,n,a,o,r,h=e(this).resizable("instance"),l=h.options,u=h.element,d=l.containment,c=d instanceof e?d.get(0):/parent/.test(d)?u.parent().get(0):d;c&&(h.containerElement=e(c),/document/.test(d)||d===document?(h.containerOffset={left:0,top:0},h.containerPosition={left:0,top:0},h.parentData={element:e(document),left:0,top:0,width:e(document).width(),height:e(document).height()||document.body.parentNode.scrollHeight}):(t=e(c),i=[],e(["Top","Right","Left","Bottom"]).each(function(e,s){i[e]=h._num(t.css("padding"+s))}),h.containerOffset=t.offset(),h.containerPosition=t.position(),h.containerSize={height:t.innerHeight()-i[3],width:t.innerWidth()-i[1]},s=h.containerOffset,n=h.containerSize.height,a=h.containerSize.width,o=h._hasScroll(c,"left")?c.scrollWidth:a,r=h._hasScroll(c)?c.scrollHeight:n,h.parentData={element:c,left:s.left,top:s.top,width:o,height:r}))},resize:function(t){var i,s,n,a,o=e(this).resizable("instance"),r=o.options,h=o.containerOffset,l=o.position,u=o._aspectRatio||t.shiftKey,d={top:0,left:0},c=o.containerElement,p=!0;c[0]!==document&&/static/.test(c.css("position"))&&(d=h),l.left<(o._helper?h.left:0)&&(o.size.width=o.size.width+(o._helper?o.position.left-h.left:o.position.left-d.left),u&&(o.size.height=o.size.width/o.aspectRatio,p=!1),o.position.left=r.helper?h.left:0),l.top<(o._helper?h.top:0)&&(o.size.height=o.size.height+(o._helper?o.position.top-h.top:o.position.top),u&&(o.size.width=o.size.height*o.aspectRatio,p=!1),o.position.top=o._helper?h.top:0),n=o.containerElement.get(0)===o.element.parent().get(0),a=/relative|absolute/.test(o.containerElement.css("position")),n&&a?(o.offset.left=o.parentData.left+o.position.left,o.offset.top=o.parentData.top+o.position.top):(o.offset.left=o.element.offset().left,o.offset.top=o.element.offset().top),i=Math.abs(o.sizeDiff.width+(o._helper?o.offset.left-d.left:o.offset.left-h.left)),s=Math.abs(o.sizeDiff.height+(o._helper?o.offset.top-d.top:o.offset.top-h.top)),i+o.size.width>=o.parentData.width&&(o.size.width=o.parentData.width-i,u&&(o.size.height=o.size.width/o.aspectRatio,p=!1)),s+o.size.height>=o.parentData.height&&(o.size.height=o.parentData.height-s,u&&(o.size.width=o.size.height*o.aspectRatio,p=!1)),p||(o.position.left=o.prevPosition.left,o.position.top=o.prevPosition.top,o.size.width=o.prevSize.width,o.size.height=o.prevSize.height)},stop:function(){var t=e(this).resizable("instance"),i=t.options,s=t.containerOffset,n=t.containerPosition,a=t.containerElement,o=e(t.helper),r=o.offset(),h=o.outerWidth()-t.sizeDiff.width,l=o.outerHeight()-t.sizeDiff.height;t._helper&&!i.animate&&/relative/.test(a.css("position"))&&e(this).css({left:r.left-n.left-s.left,width:h,height:l}),t._helper&&!i.animate&&/static/.test(a.css("position"))&&e(this).css({left:r.left-n.left-s.left,width:h,height:l})}}),e.ui.plugin.add("resizable","alsoResize",{start:function(){var t=e(this).resizable("instance"),i=t.options;e(i.alsoResize).each(function(){var t=e(this);t.data("ui-resizable-alsoresize",{width:parseInt(t.width(),10),height:parseInt(t.height(),10),left:parseInt(t.css("left"),10),top:parseInt(t.css("top"),10)})})},resize:function(t,i){var s=e(this).resizable("instance"),n=s.options,a=s.originalSize,o=s.originalPosition,r={height:s.size.height-a.height||0,width:s.size.width-a.width||0,top:s.position.top-o.top||0,left:s.position.left-o.left||0};e(n.alsoResize).each(function(){var t=e(this),s=e(this).data("ui-resizable-alsoresize"),n={},a=t.parents(i.originalElement[0]).length?["width","height"]:["width","height","top","left"];e.each(a,function(e,t){var i=(s[t]||0)+(r[t]||0);i&&i>=0&&(n[t]=i||null)}),t.css(n)})},stop:function(){e(this).removeData("resizable-alsoresize")}}),e.ui.plugin.add("resizable","ghost",{start:function(){var t=e(this).resizable("instance"),i=t.options,s=t.size;t.ghost=t.originalElement.clone(),t.ghost.css({opacity:.25,display:"block",position:"relative",height:s.height,width:s.width,margin:0,left:0,top:0}).addClass("ui-resizable-ghost").addClass("string"==typeof i.ghost?i.ghost:""),t.ghost.appendTo(t.helper)},resize:function(){var t=e(this).resizable("instance");t.ghost&&t.ghost.css({position:"relative",height:t.size.height,width:t.size.width})},stop:function(){var t=e(this).resizable("instance");t.ghost&&t.helper&&t.helper.get(0).removeChild(t.ghost.get(0))}}),e.ui.plugin.add("resizable","grid",{resize:function(){var t,i=e(this).resizable("instance"),s=i.options,n=i.size,a=i.originalSize,o=i.originalPosition,r=i.axis,h="number"==typeof s.grid?[s.grid,s.grid]:s.grid,l=h[0]||1,u=h[1]||1,d=Math.round((n.width-a.width)/l)*l,c=Math.round((n.height-a.height)/u)*u,p=a.width+d,f=a.height+c,m=s.maxWidth&&p>s.maxWidth,g=s.maxHeight&&f>s.maxHeight,v=s.minWidth&&s.minWidth>p,y=s.minHeight&&s.minHeight>f;s.grid=h,v&&(p+=l),y&&(f+=u),m&&(p-=l),g&&(f-=u),/^(se|s|e)$/.test(r)?(i.size.width=p,i.size.height=f):/^(ne)$/.test(r)?(i.size.width=p,i.size.height=f,i.position.top=o.top-c):/^(sw)$/.test(r)?(i.size.width=p,i.size.height=f,i.position.left=o.left-d):((0>=f-u||0>=p-l)&&(t=i._getPaddingPlusBorderDimensions(this)),f-u>0?(i.size.height=f,i.position.top=o.top-c):(f=u-t.height,i.size.height=f,i.position.top=o.top+a.height-f),p-l>0?(i.size.width=p,i.position.left=o.left-d):(p=l-t.width,i.size.width=p,i.position.left=o.left+a.width-p))}}),e.ui.resizable,e.widget("ui.selectable",e.ui.mouse,{version:"1.11.4",options:{appendTo:"body",autoRefresh:!0,distance:0,filter:"*",tolerance:"touch",selected:null,selecting:null,start:null,stop:null,unselected:null,unselecting:null},_create:function(){var t,i=this;
this.element.addClass("ui-selectable"),this.dragged=!1,this.refresh=function(){t=e(i.options.filter,i.element[0]),t.addClass("ui-selectee"),t.each(function(){var t=e(this),i=t.offset();e.data(this,"selectable-item",{element:this,$element:t,left:i.left,top:i.top,right:i.left+t.outerWidth(),bottom:i.top+t.outerHeight(),startselected:!1,selected:t.hasClass("ui-selected"),selecting:t.hasClass("ui-selecting"),unselecting:t.hasClass("ui-unselecting")})})},this.refresh(),this.selectees=t.addClass("ui-selectee"),this._mouseInit(),this.helper=e("<div class='ui-selectable-helper'></div>")},_destroy:function(){this.selectees.removeClass("ui-selectee").removeData("selectable-item"),this.element.removeClass("ui-selectable ui-selectable-disabled"),this._mouseDestroy()},_mouseStart:function(t){var i=this,s=this.options;this.opos=[t.pageX,t.pageY],this.options.disabled||(this.selectees=e(s.filter,this.element[0]),this._trigger("start",t),e(s.appendTo).append(this.helper),this.helper.css({left:t.pageX,top:t.pageY,width:0,height:0}),s.autoRefresh&&this.refresh(),this.selectees.filter(".ui-selected").each(function(){var s=e.data(this,"selectable-item");s.startselected=!0,t.metaKey||t.ctrlKey||(s.$element.removeClass("ui-selected"),s.selected=!1,s.$element.addClass("ui-unselecting"),s.unselecting=!0,i._trigger("unselecting",t,{unselecting:s.element}))}),e(t.target).parents().addBack().each(function(){var s,n=e.data(this,"selectable-item");return n?(s=!t.metaKey&&!t.ctrlKey||!n.$element.hasClass("ui-selected"),n.$element.removeClass(s?"ui-unselecting":"ui-selected").addClass(s?"ui-selecting":"ui-unselecting"),n.unselecting=!s,n.selecting=s,n.selected=s,s?i._trigger("selecting",t,{selecting:n.element}):i._trigger("unselecting",t,{unselecting:n.element}),!1):void 0}))},_mouseDrag:function(t){if(this.dragged=!0,!this.options.disabled){var i,s=this,n=this.options,a=this.opos[0],o=this.opos[1],r=t.pageX,h=t.pageY;return a>r&&(i=r,r=a,a=i),o>h&&(i=h,h=o,o=i),this.helper.css({left:a,top:o,width:r-a,height:h-o}),this.selectees.each(function(){var i=e.data(this,"selectable-item"),l=!1;i&&i.element!==s.element[0]&&("touch"===n.tolerance?l=!(i.left>r||a>i.right||i.top>h||o>i.bottom):"fit"===n.tolerance&&(l=i.left>a&&r>i.right&&i.top>o&&h>i.bottom),l?(i.selected&&(i.$element.removeClass("ui-selected"),i.selected=!1),i.unselecting&&(i.$element.removeClass("ui-unselecting"),i.unselecting=!1),i.selecting||(i.$element.addClass("ui-selecting"),i.selecting=!0,s._trigger("selecting",t,{selecting:i.element}))):(i.selecting&&((t.metaKey||t.ctrlKey)&&i.startselected?(i.$element.removeClass("ui-selecting"),i.selecting=!1,i.$element.addClass("ui-selected"),i.selected=!0):(i.$element.removeClass("ui-selecting"),i.selecting=!1,i.startselected&&(i.$element.addClass("ui-unselecting"),i.unselecting=!0),s._trigger("unselecting",t,{unselecting:i.element}))),i.selected&&(t.metaKey||t.ctrlKey||i.startselected||(i.$element.removeClass("ui-selected"),i.selected=!1,i.$element.addClass("ui-unselecting"),i.unselecting=!0,s._trigger("unselecting",t,{unselecting:i.element})))))}),!1}},_mouseStop:function(t){var i=this;return this.dragged=!1,e(".ui-unselecting",this.element[0]).each(function(){var s=e.data(this,"selectable-item");s.$element.removeClass("ui-unselecting"),s.unselecting=!1,s.startselected=!1,i._trigger("unselected",t,{unselected:s.element})}),e(".ui-selecting",this.element[0]).each(function(){var s=e.data(this,"selectable-item");s.$element.removeClass("ui-selecting").addClass("ui-selected"),s.selecting=!1,s.selected=!0,s.startselected=!0,i._trigger("selected",t,{selected:s.element})}),this._trigger("stop",t),this.helper.remove(),!1}}),e.widget("ui.sortable",e.ui.mouse,{version:"1.11.4",widgetEventPrefix:"sort",ready:!1,options:{appendTo:"parent",axis:!1,connectWith:!1,containment:!1,cursor:"auto",cursorAt:!1,dropOnEmpty:!0,forcePlaceholderSize:!1,forceHelperSize:!1,grid:!1,handle:!1,helper:"original",items:"> *",opacity:!1,placeholder:!1,revert:!1,scroll:!0,scrollSensitivity:20,scrollSpeed:20,scope:"default",tolerance:"intersect",zIndex:1e3,activate:null,beforeStop:null,change:null,deactivate:null,out:null,over:null,receive:null,remove:null,sort:null,start:null,stop:null,update:null},_isOverAxis:function(e,t,i){return e>=t&&t+i>e},_isFloating:function(e){return/left|right/.test(e.css("float"))||/inline|table-cell/.test(e.css("display"))},_create:function(){this.containerCache={},this.element.addClass("ui-sortable"),this.refresh(),this.offset=this.element.offset(),this._mouseInit(),this._setHandleClassName(),this.ready=!0},_setOption:function(e,t){this._super(e,t),"handle"===e&&this._setHandleClassName()},_setHandleClassName:function(){this.element.find(".ui-sortable-handle").removeClass("ui-sortable-handle"),e.each(this.items,function(){(this.instance.options.handle?this.item.find(this.instance.options.handle):this.item).addClass("ui-sortable-handle")})},_destroy:function(){this.element.removeClass("ui-sortable ui-sortable-disabled").find(".ui-sortable-handle").removeClass("ui-sortable-handle"),this._mouseDestroy();for(var e=this.items.length-1;e>=0;e--)this.items[e].item.removeData(this.widgetName+"-item");return this},_mouseCapture:function(t,i){var s=null,n=!1,a=this;return this.reverting?!1:this.options.disabled||"static"===this.options.type?!1:(this._refreshItems(t),e(t.target).parents().each(function(){return e.data(this,a.widgetName+"-item")===a?(s=e(this),!1):void 0}),e.data(t.target,a.widgetName+"-item")===a&&(s=e(t.target)),s?!this.options.handle||i||(e(this.options.handle,s).find("*").addBack().each(function(){this===t.target&&(n=!0)}),n)?(this.currentItem=s,this._removeCurrentsFromItems(),!0):!1:!1)},_mouseStart:function(t,i,s){var n,a,o=this.options;if(this.currentContainer=this,this.refreshPositions(),this.helper=this._createHelper(t),this._cacheHelperProportions(),this._cacheMargins(),this.scrollParent=this.helper.scrollParent(),this.offset=this.currentItem.offset(),this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left},e.extend(this.offset,{click:{left:t.pageX-this.offset.left,top:t.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()}),this.helper.css("position","absolute"),this.cssPosition=this.helper.css("position"),this.originalPosition=this._generatePosition(t),this.originalPageX=t.pageX,this.originalPageY=t.pageY,o.cursorAt&&this._adjustOffsetFromHelper(o.cursorAt),this.domPosition={prev:this.currentItem.prev()[0],parent:this.currentItem.parent()[0]},this.helper[0]!==this.currentItem[0]&&this.currentItem.hide(),this._createPlaceholder(),o.containment&&this._setContainment(),o.cursor&&"auto"!==o.cursor&&(a=this.document.find("body"),this.storedCursor=a.css("cursor"),a.css("cursor",o.cursor),this.storedStylesheet=e("<style>*{ cursor: "+o.cursor+" !important; }</style>").appendTo(a)),o.opacity&&(this.helper.css("opacity")&&(this._storedOpacity=this.helper.css("opacity")),this.helper.css("opacity",o.opacity)),o.zIndex&&(this.helper.css("zIndex")&&(this._storedZIndex=this.helper.css("zIndex")),this.helper.css("zIndex",o.zIndex)),this.scrollParent[0]!==this.document[0]&&"HTML"!==this.scrollParent[0].tagName&&(this.overflowOffset=this.scrollParent.offset()),this._trigger("start",t,this._uiHash()),this._preserveHelperProportions||this._cacheHelperProportions(),!s)for(n=this.containers.length-1;n>=0;n--)this.containers[n]._trigger("activate",t,this._uiHash(this));return e.ui.ddmanager&&(e.ui.ddmanager.current=this),e.ui.ddmanager&&!o.dropBehaviour&&e.ui.ddmanager.prepareOffsets(this,t),this.dragging=!0,this.helper.addClass("ui-sortable-helper"),this._mouseDrag(t),!0},_mouseDrag:function(t){var i,s,n,a,o=this.options,r=!1;for(this.position=this._generatePosition(t),this.positionAbs=this._convertPositionTo("absolute"),this.lastPositionAbs||(this.lastPositionAbs=this.positionAbs),this.options.scroll&&(this.scrollParent[0]!==this.document[0]&&"HTML"!==this.scrollParent[0].tagName?(this.overflowOffset.top+this.scrollParent[0].offsetHeight-t.pageY<o.scrollSensitivity?this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop+o.scrollSpeed:t.pageY-this.overflowOffset.top<o.scrollSensitivity&&(this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop-o.scrollSpeed),this.overflowOffset.left+this.scrollParent[0].offsetWidth-t.pageX<o.scrollSensitivity?this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft+o.scrollSpeed:t.pageX-this.overflowOffset.left<o.scrollSensitivity&&(this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft-o.scrollSpeed)):(t.pageY-this.document.scrollTop()<o.scrollSensitivity?r=this.document.scrollTop(this.document.scrollTop()-o.scrollSpeed):this.window.height()-(t.pageY-this.document.scrollTop())<o.scrollSensitivity&&(r=this.document.scrollTop(this.document.scrollTop()+o.scrollSpeed)),t.pageX-this.document.scrollLeft()<o.scrollSensitivity?r=this.document.scrollLeft(this.document.scrollLeft()-o.scrollSpeed):this.window.width()-(t.pageX-this.document.scrollLeft())<o.scrollSensitivity&&(r=this.document.scrollLeft(this.document.scrollLeft()+o.scrollSpeed))),r!==!1&&e.ui.ddmanager&&!o.dropBehaviour&&e.ui.ddmanager.prepareOffsets(this,t)),this.positionAbs=this._convertPositionTo("absolute"),this.options.axis&&"y"===this.options.axis||(this.helper[0].style.left=this.position.left+"px"),this.options.axis&&"x"===this.options.axis||(this.helper[0].style.top=this.position.top+"px"),i=this.items.length-1;i>=0;i--)if(s=this.items[i],n=s.item[0],a=this._intersectsWithPointer(s),a&&s.instance===this.currentContainer&&n!==this.currentItem[0]&&this.placeholder[1===a?"next":"prev"]()[0]!==n&&!e.contains(this.placeholder[0],n)&&("semi-dynamic"===this.options.type?!e.contains(this.element[0],n):!0)){if(this.direction=1===a?"down":"up","pointer"!==this.options.tolerance&&!this._intersectsWithSides(s))break;this._rearrange(t,s),this._trigger("change",t,this._uiHash());break}return this._contactContainers(t),e.ui.ddmanager&&e.ui.ddmanager.drag(this,t),this._trigger("sort",t,this._uiHash()),this.lastPositionAbs=this.positionAbs,!1},_mouseStop:function(t,i){if(t){if(e.ui.ddmanager&&!this.options.dropBehaviour&&e.ui.ddmanager.drop(this,t),this.options.revert){var s=this,n=this.placeholder.offset(),a=this.options.axis,o={};a&&"x"!==a||(o.left=n.left-this.offset.parent.left-this.margins.left+(this.offsetParent[0]===this.document[0].body?0:this.offsetParent[0].scrollLeft)),a&&"y"!==a||(o.top=n.top-this.offset.parent.top-this.margins.top+(this.offsetParent[0]===this.document[0].body?0:this.offsetParent[0].scrollTop)),this.reverting=!0,e(this.helper).animate(o,parseInt(this.options.revert,10)||500,function(){s._clear(t)})}else this._clear(t,i);return!1}},cancel:function(){if(this.dragging){this._mouseUp({target:null}),"original"===this.options.helper?this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper"):this.currentItem.show();for(var t=this.containers.length-1;t>=0;t--)this.containers[t]._trigger("deactivate",null,this._uiHash(this)),this.containers[t].containerCache.over&&(this.containers[t]._trigger("out",null,this._uiHash(this)),this.containers[t].containerCache.over=0)}return this.placeholder&&(this.placeholder[0].parentNode&&this.placeholder[0].parentNode.removeChild(this.placeholder[0]),"original"!==this.options.helper&&this.helper&&this.helper[0].parentNode&&this.helper.remove(),e.extend(this,{helper:null,dragging:!1,reverting:!1,_noFinalSort:null}),this.domPosition.prev?e(this.domPosition.prev).after(this.currentItem):e(this.domPosition.parent).prepend(this.currentItem)),this},serialize:function(t){var i=this._getItemsAsjQuery(t&&t.connected),s=[];return t=t||{},e(i).each(function(){var i=(e(t.item||this).attr(t.attribute||"id")||"").match(t.expression||/(.+)[\-=_](.+)/);i&&s.push((t.key||i[1]+"[]")+"="+(t.key&&t.expression?i[1]:i[2]))}),!s.length&&t.key&&s.push(t.key+"="),s.join("&")},toArray:function(t){var i=this._getItemsAsjQuery(t&&t.connected),s=[];return t=t||{},i.each(function(){s.push(e(t.item||this).attr(t.attribute||"id")||"")}),s},_intersectsWith:function(e){var t=this.positionAbs.left,i=t+this.helperProportions.width,s=this.positionAbs.top,n=s+this.helperProportions.height,a=e.left,o=a+e.width,r=e.top,h=r+e.height,l=this.offset.click.top,u=this.offset.click.left,d="x"===this.options.axis||s+l>r&&h>s+l,c="y"===this.options.axis||t+u>a&&o>t+u,p=d&&c;return"pointer"===this.options.tolerance||this.options.forcePointerForContainers||"pointer"!==this.options.tolerance&&this.helperProportions[this.floating?"width":"height"]>e[this.floating?"width":"height"]?p:t+this.helperProportions.width/2>a&&o>i-this.helperProportions.width/2&&s+this.helperProportions.height/2>r&&h>n-this.helperProportions.height/2},_intersectsWithPointer:function(e){var t="x"===this.options.axis||this._isOverAxis(this.positionAbs.top+this.offset.click.top,e.top,e.height),i="y"===this.options.axis||this._isOverAxis(this.positionAbs.left+this.offset.click.left,e.left,e.width),s=t&&i,n=this._getDragVerticalDirection(),a=this._getDragHorizontalDirection();return s?this.floating?a&&"right"===a||"down"===n?2:1:n&&("down"===n?2:1):!1},_intersectsWithSides:function(e){var t=this._isOverAxis(this.positionAbs.top+this.offset.click.top,e.top+e.height/2,e.height),i=this._isOverAxis(this.positionAbs.left+this.offset.click.left,e.left+e.width/2,e.width),s=this._getDragVerticalDirection(),n=this._getDragHorizontalDirection();return this.floating&&n?"right"===n&&i||"left"===n&&!i:s&&("down"===s&&t||"up"===s&&!t)},_getDragVerticalDirection:function(){var e=this.positionAbs.top-this.lastPositionAbs.top;return 0!==e&&(e>0?"down":"up")},_getDragHorizontalDirection:function(){var e=this.positionAbs.left-this.lastPositionAbs.left;return 0!==e&&(e>0?"right":"left")},refresh:function(e){return this._refreshItems(e),this._setHandleClassName(),this.refreshPositions(),this},_connectWith:function(){var e=this.options;return e.connectWith.constructor===String?[e.connectWith]:e.connectWith},_getItemsAsjQuery:function(t){function i(){r.push(this)}var s,n,a,o,r=[],h=[],l=this._connectWith();if(l&&t)for(s=l.length-1;s>=0;s--)for(a=e(l[s],this.document[0]),n=a.length-1;n>=0;n--)o=e.data(a[n],this.widgetFullName),o&&o!==this&&!o.options.disabled&&h.push([e.isFunction(o.options.items)?o.options.items.call(o.element):e(o.options.items,o.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),o]);for(h.push([e.isFunction(this.options.items)?this.options.items.call(this.element,null,{options:this.options,item:this.currentItem}):e(this.options.items,this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),this]),s=h.length-1;s>=0;s--)h[s][0].each(i);return e(r)},_removeCurrentsFromItems:function(){var t=this.currentItem.find(":data("+this.widgetName+"-item)");this.items=e.grep(this.items,function(e){for(var i=0;t.length>i;i++)if(t[i]===e.item[0])return!1;return!0})},_refreshItems:function(t){this.items=[],this.containers=[this];var i,s,n,a,o,r,h,l,u=this.items,d=[[e.isFunction(this.options.items)?this.options.items.call(this.element[0],t,{item:this.currentItem}):e(this.options.items,this.element),this]],c=this._connectWith();if(c&&this.ready)for(i=c.length-1;i>=0;i--)for(n=e(c[i],this.document[0]),s=n.length-1;s>=0;s--)a=e.data(n[s],this.widgetFullName),a&&a!==this&&!a.options.disabled&&(d.push([e.isFunction(a.options.items)?a.options.items.call(a.element[0],t,{item:this.currentItem}):e(a.options.items,a.element),a]),this.containers.push(a));for(i=d.length-1;i>=0;i--)for(o=d[i][1],r=d[i][0],s=0,l=r.length;l>s;s++)h=e(r[s]),h.data(this.widgetName+"-item",o),u.push({item:h,instance:o,width:0,height:0,left:0,top:0})},refreshPositions:function(t){this.floating=this.items.length?"x"===this.options.axis||this._isFloating(this.items[0].item):!1,this.offsetParent&&this.helper&&(this.offset.parent=this._getParentOffset());var i,s,n,a;for(i=this.items.length-1;i>=0;i--)s=this.items[i],s.instance!==this.currentContainer&&this.currentContainer&&s.item[0]!==this.currentItem[0]||(n=this.options.toleranceElement?e(this.options.toleranceElement,s.item):s.item,t||(s.width=n.outerWidth(),s.height=n.outerHeight()),a=n.offset(),s.left=a.left,s.top=a.top);if(this.options.custom&&this.options.custom.refreshContainers)this.options.custom.refreshContainers.call(this);else for(i=this.containers.length-1;i>=0;i--)a=this.containers[i].element.offset(),this.containers[i].containerCache.left=a.left,this.containers[i].containerCache.top=a.top,this.containers[i].containerCache.width=this.containers[i].element.outerWidth(),this.containers[i].containerCache.height=this.containers[i].element.outerHeight();return this},_createPlaceholder:function(t){t=t||this;var i,s=t.options;s.placeholder&&s.placeholder.constructor!==String||(i=s.placeholder,s.placeholder={element:function(){var s=t.currentItem[0].nodeName.toLowerCase(),n=e("<"+s+">",t.document[0]).addClass(i||t.currentItem[0].className+" ui-sortable-placeholder").removeClass("ui-sortable-helper");return"tbody"===s?t._createTrPlaceholder(t.currentItem.find("tr").eq(0),e("<tr>",t.document[0]).appendTo(n)):"tr"===s?t._createTrPlaceholder(t.currentItem,n):"img"===s&&n.attr("src",t.currentItem.attr("src")),i||n.css("visibility","hidden"),n},update:function(e,n){(!i||s.forcePlaceholderSize)&&(n.height()||n.height(t.currentItem.innerHeight()-parseInt(t.currentItem.css("paddingTop")||0,10)-parseInt(t.currentItem.css("paddingBottom")||0,10)),n.width()||n.width(t.currentItem.innerWidth()-parseInt(t.currentItem.css("paddingLeft")||0,10)-parseInt(t.currentItem.css("paddingRight")||0,10)))}}),t.placeholder=e(s.placeholder.element.call(t.element,t.currentItem)),t.currentItem.after(t.placeholder),s.placeholder.update(t,t.placeholder)},_createTrPlaceholder:function(t,i){var s=this;t.children().each(function(){e("<td>&#160;</td>",s.document[0]).attr("colspan",e(this).attr("colspan")||1).appendTo(i)})},_contactContainers:function(t){var i,s,n,a,o,r,h,l,u,d,c=null,p=null;for(i=this.containers.length-1;i>=0;i--)if(!e.contains(this.currentItem[0],this.containers[i].element[0]))if(this._intersectsWith(this.containers[i].containerCache)){if(c&&e.contains(this.containers[i].element[0],c.element[0]))continue;c=this.containers[i],p=i}else this.containers[i].containerCache.over&&(this.containers[i]._trigger("out",t,this._uiHash(this)),this.containers[i].containerCache.over=0);if(c)if(1===this.containers.length)this.containers[p].containerCache.over||(this.containers[p]._trigger("over",t,this._uiHash(this)),this.containers[p].containerCache.over=1);else{for(n=1e4,a=null,u=c.floating||this._isFloating(this.currentItem),o=u?"left":"top",r=u?"width":"height",d=u?"clientX":"clientY",s=this.items.length-1;s>=0;s--)e.contains(this.containers[p].element[0],this.items[s].item[0])&&this.items[s].item[0]!==this.currentItem[0]&&(h=this.items[s].item.offset()[o],l=!1,t[d]-h>this.items[s][r]/2&&(l=!0),n>Math.abs(t[d]-h)&&(n=Math.abs(t[d]-h),a=this.items[s],this.direction=l?"up":"down"));if(!a&&!this.options.dropOnEmpty)return;if(this.currentContainer===this.containers[p])return this.currentContainer.containerCache.over||(this.containers[p]._trigger("over",t,this._uiHash()),this.currentContainer.containerCache.over=1),void 0;a?this._rearrange(t,a,null,!0):this._rearrange(t,null,this.containers[p].element,!0),this._trigger("change",t,this._uiHash()),this.containers[p]._trigger("change",t,this._uiHash(this)),this.currentContainer=this.containers[p],this.options.placeholder.update(this.currentContainer,this.placeholder),this.containers[p]._trigger("over",t,this._uiHash(this)),this.containers[p].containerCache.over=1}},_createHelper:function(t){var i=this.options,s=e.isFunction(i.helper)?e(i.helper.apply(this.element[0],[t,this.currentItem])):"clone"===i.helper?this.currentItem.clone():this.currentItem;return s.parents("body").length||e("parent"!==i.appendTo?i.appendTo:this.currentItem[0].parentNode)[0].appendChild(s[0]),s[0]===this.currentItem[0]&&(this._storedCSS={width:this.currentItem[0].style.width,height:this.currentItem[0].style.height,position:this.currentItem.css("position"),top:this.currentItem.css("top"),left:this.currentItem.css("left")}),(!s[0].style.width||i.forceHelperSize)&&s.width(this.currentItem.width()),(!s[0].style.height||i.forceHelperSize)&&s.height(this.currentItem.height()),s},_adjustOffsetFromHelper:function(t){"string"==typeof t&&(t=t.split(" ")),e.isArray(t)&&(t={left:+t[0],top:+t[1]||0}),"left"in t&&(this.offset.click.left=t.left+this.margins.left),"right"in t&&(this.offset.click.left=this.helperProportions.width-t.right+this.margins.left),"top"in t&&(this.offset.click.top=t.top+this.margins.top),"bottom"in t&&(this.offset.click.top=this.helperProportions.height-t.bottom+this.margins.top)},_getParentOffset:function(){this.offsetParent=this.helper.offsetParent();var t=this.offsetParent.offset();return"absolute"===this.cssPosition&&this.scrollParent[0]!==this.document[0]&&e.contains(this.scrollParent[0],this.offsetParent[0])&&(t.left+=this.scrollParent.scrollLeft(),t.top+=this.scrollParent.scrollTop()),(this.offsetParent[0]===this.document[0].body||this.offsetParent[0].tagName&&"html"===this.offsetParent[0].tagName.toLowerCase()&&e.ui.ie)&&(t={top:0,left:0}),{top:t.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:t.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"===this.cssPosition){var e=this.currentItem.position();return{top:e.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:e.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()}}return{top:0,left:0}},_cacheMargins:function(){this.margins={left:parseInt(this.currentItem.css("marginLeft"),10)||0,top:parseInt(this.currentItem.css("marginTop"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var t,i,s,n=this.options;"parent"===n.containment&&(n.containment=this.helper[0].parentNode),("document"===n.containment||"window"===n.containment)&&(this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,"document"===n.containment?this.document.width():this.window.width()-this.helperProportions.width-this.margins.left,("document"===n.containment?this.document.width():this.window.height()||this.document[0].body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top]),/^(document|window|parent)$/.test(n.containment)||(t=e(n.containment)[0],i=e(n.containment).offset(),s="hidden"!==e(t).css("overflow"),this.containment=[i.left+(parseInt(e(t).css("borderLeftWidth"),10)||0)+(parseInt(e(t).css("paddingLeft"),10)||0)-this.margins.left,i.top+(parseInt(e(t).css("borderTopWidth"),10)||0)+(parseInt(e(t).css("paddingTop"),10)||0)-this.margins.top,i.left+(s?Math.max(t.scrollWidth,t.offsetWidth):t.offsetWidth)-(parseInt(e(t).css("borderLeftWidth"),10)||0)-(parseInt(e(t).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,i.top+(s?Math.max(t.scrollHeight,t.offsetHeight):t.offsetHeight)-(parseInt(e(t).css("borderTopWidth"),10)||0)-(parseInt(e(t).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top])},_convertPositionTo:function(t,i){i||(i=this.position);var s="absolute"===t?1:-1,n="absolute"!==this.cssPosition||this.scrollParent[0]!==this.document[0]&&e.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,a=/(html|body)/i.test(n[0].tagName);return{top:i.top+this.offset.relative.top*s+this.offset.parent.top*s-("fixed"===this.cssPosition?-this.scrollParent.scrollTop():a?0:n.scrollTop())*s,left:i.left+this.offset.relative.left*s+this.offset.parent.left*s-("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():a?0:n.scrollLeft())*s}},_generatePosition:function(t){var i,s,n=this.options,a=t.pageX,o=t.pageY,r="absolute"!==this.cssPosition||this.scrollParent[0]!==this.document[0]&&e.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,h=/(html|body)/i.test(r[0].tagName);return"relative"!==this.cssPosition||this.scrollParent[0]!==this.document[0]&&this.scrollParent[0]!==this.offsetParent[0]||(this.offset.relative=this._getRelativeOffset()),this.originalPosition&&(this.containment&&(t.pageX-this.offset.click.left<this.containment[0]&&(a=this.containment[0]+this.offset.click.left),t.pageY-this.offset.click.top<this.containment[1]&&(o=this.containment[1]+this.offset.click.top),t.pageX-this.offset.click.left>this.containment[2]&&(a=this.containment[2]+this.offset.click.left),t.pageY-this.offset.click.top>this.containment[3]&&(o=this.containment[3]+this.offset.click.top)),n.grid&&(i=this.originalPageY+Math.round((o-this.originalPageY)/n.grid[1])*n.grid[1],o=this.containment?i-this.offset.click.top>=this.containment[1]&&i-this.offset.click.top<=this.containment[3]?i:i-this.offset.click.top>=this.containment[1]?i-n.grid[1]:i+n.grid[1]:i,s=this.originalPageX+Math.round((a-this.originalPageX)/n.grid[0])*n.grid[0],a=this.containment?s-this.offset.click.left>=this.containment[0]&&s-this.offset.click.left<=this.containment[2]?s:s-this.offset.click.left>=this.containment[0]?s-n.grid[0]:s+n.grid[0]:s)),{top:o-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.scrollParent.scrollTop():h?0:r.scrollTop()),left:a-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():h?0:r.scrollLeft())}},_rearrange:function(e,t,i,s){i?i[0].appendChild(this.placeholder[0]):t.item[0].parentNode.insertBefore(this.placeholder[0],"down"===this.direction?t.item[0]:t.item[0].nextSibling),this.counter=this.counter?++this.counter:1;var n=this.counter;this._delay(function(){n===this.counter&&this.refreshPositions(!s)})},_clear:function(e,t){function i(e,t,i){return function(s){i._trigger(e,s,t._uiHash(t))}}this.reverting=!1;var s,n=[];if(!this._noFinalSort&&this.currentItem.parent().length&&this.placeholder.before(this.currentItem),this._noFinalSort=null,this.helper[0]===this.currentItem[0]){for(s in this._storedCSS)("auto"===this._storedCSS[s]||"static"===this._storedCSS[s])&&(this._storedCSS[s]="");this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")}else this.currentItem.show();for(this.fromOutside&&!t&&n.push(function(e){this._trigger("receive",e,this._uiHash(this.fromOutside))}),!this.fromOutside&&this.domPosition.prev===this.currentItem.prev().not(".ui-sortable-helper")[0]&&this.domPosition.parent===this.currentItem.parent()[0]||t||n.push(function(e){this._trigger("update",e,this._uiHash())}),this!==this.currentContainer&&(t||(n.push(function(e){this._trigger("remove",e,this._uiHash())}),n.push(function(e){return function(t){e._trigger("receive",t,this._uiHash(this))}}.call(this,this.currentContainer)),n.push(function(e){return function(t){e._trigger("update",t,this._uiHash(this))}}.call(this,this.currentContainer)))),s=this.containers.length-1;s>=0;s--)t||n.push(i("deactivate",this,this.containers[s])),this.containers[s].containerCache.over&&(n.push(i("out",this,this.containers[s])),this.containers[s].containerCache.over=0);if(this.storedCursor&&(this.document.find("body").css("cursor",this.storedCursor),this.storedStylesheet.remove()),this._storedOpacity&&this.helper.css("opacity",this._storedOpacity),this._storedZIndex&&this.helper.css("zIndex","auto"===this._storedZIndex?"":this._storedZIndex),this.dragging=!1,t||this._trigger("beforeStop",e,this._uiHash()),this.placeholder[0].parentNode.removeChild(this.placeholder[0]),this.cancelHelperRemoval||(this.helper[0]!==this.currentItem[0]&&this.helper.remove(),this.helper=null),!t){for(s=0;n.length>s;s++)n[s].call(this,e);this._trigger("stop",e,this._uiHash())}return this.fromOutside=!1,!this.cancelHelperRemoval},_trigger:function(){e.Widget.prototype._trigger.apply(this,arguments)===!1&&this.cancel()},_uiHash:function(t){var i=t||this;return{helper:i.helper,placeholder:i.placeholder||e([]),position:i.position,originalPosition:i.originalPosition,offset:i.positionAbs,item:i.currentItem,sender:t?t.element:null}}}),e.widget("ui.accordion",{version:"1.11.4",options:{active:0,animate:{},collapsible:!1,event:"click",header:"> li > :first-child,> :not(li):even",heightStyle:"auto",icons:{activeHeader:"ui-icon-triangle-1-s",header:"ui-icon-triangle-1-e"},activate:null,beforeActivate:null},hideProps:{borderTopWidth:"hide",borderBottomWidth:"hide",paddingTop:"hide",paddingBottom:"hide",height:"hide"},showProps:{borderTopWidth:"show",borderBottomWidth:"show",paddingTop:"show",paddingBottom:"show",height:"show"},_create:function(){var t=this.options;this.prevShow=this.prevHide=e(),this.element.addClass("ui-accordion ui-widget ui-helper-reset").attr("role","tablist"),t.collapsible||t.active!==!1&&null!=t.active||(t.active=0),this._processPanels(),0>t.active&&(t.active+=this.headers.length),this._refresh()},_getCreateEventData:function(){return{header:this.active,panel:this.active.length?this.active.next():e()}},_createIcons:function(){var t=this.options.icons;t&&(e("<span>").addClass("ui-accordion-header-icon ui-icon "+t.header).prependTo(this.headers),this.active.children(".ui-accordion-header-icon").removeClass(t.header).addClass(t.activeHeader),this.headers.addClass("ui-accordion-icons"))},_destroyIcons:function(){this.headers.removeClass("ui-accordion-icons").children(".ui-accordion-header-icon").remove()},_destroy:function(){var e;this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role"),this.headers.removeClass("ui-accordion-header ui-accordion-header-active ui-state-default ui-corner-all ui-state-active ui-state-disabled ui-corner-top").removeAttr("role").removeAttr("aria-expanded").removeAttr("aria-selected").removeAttr("aria-controls").removeAttr("tabIndex").removeUniqueId(),this._destroyIcons(),e=this.headers.next().removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active ui-state-disabled").css("display","").removeAttr("role").removeAttr("aria-hidden").removeAttr("aria-labelledby").removeUniqueId(),"content"!==this.options.heightStyle&&e.css("height","")},_setOption:function(e,t){return"active"===e?(this._activate(t),void 0):("event"===e&&(this.options.event&&this._off(this.headers,this.options.event),this._setupEvents(t)),this._super(e,t),"collapsible"!==e||t||this.options.active!==!1||this._activate(0),"icons"===e&&(this._destroyIcons(),t&&this._createIcons()),"disabled"===e&&(this.element.toggleClass("ui-state-disabled",!!t).attr("aria-disabled",t),this.headers.add(this.headers.next()).toggleClass("ui-state-disabled",!!t)),void 0)},_keydown:function(t){if(!t.altKey&&!t.ctrlKey){var i=e.ui.keyCode,s=this.headers.length,n=this.headers.index(t.target),a=!1;switch(t.keyCode){case i.RIGHT:case i.DOWN:a=this.headers[(n+1)%s];break;case i.LEFT:case i.UP:a=this.headers[(n-1+s)%s];break;case i.SPACE:case i.ENTER:this._eventHandler(t);break;case i.HOME:a=this.headers[0];break;case i.END:a=this.headers[s-1]}a&&(e(t.target).attr("tabIndex",-1),e(a).attr("tabIndex",0),a.focus(),t.preventDefault())}},_panelKeyDown:function(t){t.keyCode===e.ui.keyCode.UP&&t.ctrlKey&&e(t.currentTarget).prev().focus()},refresh:function(){var t=this.options;this._processPanels(),t.active===!1&&t.collapsible===!0||!this.headers.length?(t.active=!1,this.active=e()):t.active===!1?this._activate(0):this.active.length&&!e.contains(this.element[0],this.active[0])?this.headers.length===this.headers.find(".ui-state-disabled").length?(t.active=!1,this.active=e()):this._activate(Math.max(0,t.active-1)):t.active=this.headers.index(this.active),this._destroyIcons(),this._refresh()},_processPanels:function(){var e=this.headers,t=this.panels;this.headers=this.element.find(this.options.header).addClass("ui-accordion-header ui-state-default ui-corner-all"),this.panels=this.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").filter(":not(.ui-accordion-content-active)").hide(),t&&(this._off(e.not(this.headers)),this._off(t.not(this.panels)))
},_refresh:function(){var t,i=this.options,s=i.heightStyle,n=this.element.parent();this.active=this._findActive(i.active).addClass("ui-accordion-header-active ui-state-active ui-corner-top").removeClass("ui-corner-all"),this.active.next().addClass("ui-accordion-content-active").show(),this.headers.attr("role","tab").each(function(){var t=e(this),i=t.uniqueId().attr("id"),s=t.next(),n=s.uniqueId().attr("id");t.attr("aria-controls",n),s.attr("aria-labelledby",i)}).next().attr("role","tabpanel"),this.headers.not(this.active).attr({"aria-selected":"false","aria-expanded":"false",tabIndex:-1}).next().attr({"aria-hidden":"true"}).hide(),this.active.length?this.active.attr({"aria-selected":"true","aria-expanded":"true",tabIndex:0}).next().attr({"aria-hidden":"false"}):this.headers.eq(0).attr("tabIndex",0),this._createIcons(),this._setupEvents(i.event),"fill"===s?(t=n.height(),this.element.siblings(":visible").each(function(){var i=e(this),s=i.css("position");"absolute"!==s&&"fixed"!==s&&(t-=i.outerHeight(!0))}),this.headers.each(function(){t-=e(this).outerHeight(!0)}),this.headers.next().each(function(){e(this).height(Math.max(0,t-e(this).innerHeight()+e(this).height()))}).css("overflow","auto")):"auto"===s&&(t=0,this.headers.next().each(function(){t=Math.max(t,e(this).css("height","").height())}).height(t))},_activate:function(t){var i=this._findActive(t)[0];i!==this.active[0]&&(i=i||this.active[0],this._eventHandler({target:i,currentTarget:i,preventDefault:e.noop}))},_findActive:function(t){return"number"==typeof t?this.headers.eq(t):e()},_setupEvents:function(t){var i={keydown:"_keydown"};t&&e.each(t.split(" "),function(e,t){i[t]="_eventHandler"}),this._off(this.headers.add(this.headers.next())),this._on(this.headers,i),this._on(this.headers.next(),{keydown:"_panelKeyDown"}),this._hoverable(this.headers),this._focusable(this.headers)},_eventHandler:function(t){var i=this.options,s=this.active,n=e(t.currentTarget),a=n[0]===s[0],o=a&&i.collapsible,r=o?e():n.next(),h=s.next(),l={oldHeader:s,oldPanel:h,newHeader:o?e():n,newPanel:r};t.preventDefault(),a&&!i.collapsible||this._trigger("beforeActivate",t,l)===!1||(i.active=o?!1:this.headers.index(n),this.active=a?e():n,this._toggle(l),s.removeClass("ui-accordion-header-active ui-state-active"),i.icons&&s.children(".ui-accordion-header-icon").removeClass(i.icons.activeHeader).addClass(i.icons.header),a||(n.removeClass("ui-corner-all").addClass("ui-accordion-header-active ui-state-active ui-corner-top"),i.icons&&n.children(".ui-accordion-header-icon").removeClass(i.icons.header).addClass(i.icons.activeHeader),n.next().addClass("ui-accordion-content-active")))},_toggle:function(t){var i=t.newPanel,s=this.prevShow.length?this.prevShow:t.oldPanel;this.prevShow.add(this.prevHide).stop(!0,!0),this.prevShow=i,this.prevHide=s,this.options.animate?this._animate(i,s,t):(s.hide(),i.show(),this._toggleComplete(t)),s.attr({"aria-hidden":"true"}),s.prev().attr({"aria-selected":"false","aria-expanded":"false"}),i.length&&s.length?s.prev().attr({tabIndex:-1,"aria-expanded":"false"}):i.length&&this.headers.filter(function(){return 0===parseInt(e(this).attr("tabIndex"),10)}).attr("tabIndex",-1),i.attr("aria-hidden","false").prev().attr({"aria-selected":"true","aria-expanded":"true",tabIndex:0})},_animate:function(e,t,i){var s,n,a,o=this,r=0,h=e.css("box-sizing"),l=e.length&&(!t.length||e.index()<t.index()),u=this.options.animate||{},d=l&&u.down||u,c=function(){o._toggleComplete(i)};return"number"==typeof d&&(a=d),"string"==typeof d&&(n=d),n=n||d.easing||u.easing,a=a||d.duration||u.duration,t.length?e.length?(s=e.show().outerHeight(),t.animate(this.hideProps,{duration:a,easing:n,step:function(e,t){t.now=Math.round(e)}}),e.hide().animate(this.showProps,{duration:a,easing:n,complete:c,step:function(e,i){i.now=Math.round(e),"height"!==i.prop?"content-box"===h&&(r+=i.now):"content"!==o.options.heightStyle&&(i.now=Math.round(s-t.outerHeight()-r),r=0)}}),void 0):t.animate(this.hideProps,a,n,c):e.animate(this.showProps,a,n,c)},_toggleComplete:function(e){var t=e.oldPanel;t.removeClass("ui-accordion-content-active").prev().removeClass("ui-corner-top").addClass("ui-corner-all"),t.length&&(t.parent()[0].className=t.parent()[0].className),this._trigger("activate",null,e)}}),e.widget("ui.menu",{version:"1.11.4",defaultElement:"<ul>",delay:300,options:{icons:{submenu:"ui-icon-carat-1-e"},items:"> *",menus:"ul",position:{my:"left-1 top",at:"right top"},role:"menu",blur:null,focus:null,select:null},_create:function(){this.activeMenu=this.element,this.mouseHandled=!1,this.element.uniqueId().addClass("ui-menu ui-widget ui-widget-content").toggleClass("ui-menu-icons",!!this.element.find(".ui-icon").length).attr({role:this.options.role,tabIndex:0}),this.options.disabled&&this.element.addClass("ui-state-disabled").attr("aria-disabled","true"),this._on({"mousedown .ui-menu-item":function(e){e.preventDefault()},"click .ui-menu-item":function(t){var i=e(t.target);!this.mouseHandled&&i.not(".ui-state-disabled").length&&(this.select(t),t.isPropagationStopped()||(this.mouseHandled=!0),i.has(".ui-menu").length?this.expand(t):!this.element.is(":focus")&&e(this.document[0].activeElement).closest(".ui-menu").length&&(this.element.trigger("focus",[!0]),this.active&&1===this.active.parents(".ui-menu").length&&clearTimeout(this.timer)))},"mouseenter .ui-menu-item":function(t){if(!this.previousFilter){var i=e(t.currentTarget);i.siblings(".ui-state-active").removeClass("ui-state-active"),this.focus(t,i)}},mouseleave:"collapseAll","mouseleave .ui-menu":"collapseAll",focus:function(e,t){var i=this.active||this.element.find(this.options.items).eq(0);t||this.focus(e,i)},blur:function(t){this._delay(function(){e.contains(this.element[0],this.document[0].activeElement)||this.collapseAll(t)})},keydown:"_keydown"}),this.refresh(),this._on(this.document,{click:function(e){this._closeOnDocumentClick(e)&&this.collapseAll(e),this.mouseHandled=!1}})},_destroy:function(){this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeClass("ui-menu ui-widget ui-widget-content ui-menu-icons ui-front").removeAttr("role").removeAttr("tabIndex").removeAttr("aria-labelledby").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-disabled").removeUniqueId().show(),this.element.find(".ui-menu-item").removeClass("ui-menu-item").removeAttr("role").removeAttr("aria-disabled").removeUniqueId().removeClass("ui-state-hover").removeAttr("tabIndex").removeAttr("role").removeAttr("aria-haspopup").children().each(function(){var t=e(this);t.data("ui-menu-submenu-carat")&&t.remove()}),this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content")},_keydown:function(t){var i,s,n,a,o=!0;switch(t.keyCode){case e.ui.keyCode.PAGE_UP:this.previousPage(t);break;case e.ui.keyCode.PAGE_DOWN:this.nextPage(t);break;case e.ui.keyCode.HOME:this._move("first","first",t);break;case e.ui.keyCode.END:this._move("last","last",t);break;case e.ui.keyCode.UP:this.previous(t);break;case e.ui.keyCode.DOWN:this.next(t);break;case e.ui.keyCode.LEFT:this.collapse(t);break;case e.ui.keyCode.RIGHT:this.active&&!this.active.is(".ui-state-disabled")&&this.expand(t);break;case e.ui.keyCode.ENTER:case e.ui.keyCode.SPACE:this._activate(t);break;case e.ui.keyCode.ESCAPE:this.collapse(t);break;default:o=!1,s=this.previousFilter||"",n=String.fromCharCode(t.keyCode),a=!1,clearTimeout(this.filterTimer),n===s?a=!0:n=s+n,i=this._filterMenuItems(n),i=a&&-1!==i.index(this.active.next())?this.active.nextAll(".ui-menu-item"):i,i.length||(n=String.fromCharCode(t.keyCode),i=this._filterMenuItems(n)),i.length?(this.focus(t,i),this.previousFilter=n,this.filterTimer=this._delay(function(){delete this.previousFilter},1e3)):delete this.previousFilter}o&&t.preventDefault()},_activate:function(e){this.active.is(".ui-state-disabled")||(this.active.is("[aria-haspopup='true']")?this.expand(e):this.select(e))},refresh:function(){var t,i,s=this,n=this.options.icons.submenu,a=this.element.find(this.options.menus);this.element.toggleClass("ui-menu-icons",!!this.element.find(".ui-icon").length),a.filter(":not(.ui-menu)").addClass("ui-menu ui-widget ui-widget-content ui-front").hide().attr({role:this.options.role,"aria-hidden":"true","aria-expanded":"false"}).each(function(){var t=e(this),i=t.parent(),s=e("<span>").addClass("ui-menu-icon ui-icon "+n).data("ui-menu-submenu-carat",!0);i.attr("aria-haspopup","true").prepend(s),t.attr("aria-labelledby",i.attr("id"))}),t=a.add(this.element),i=t.find(this.options.items),i.not(".ui-menu-item").each(function(){var t=e(this);s._isDivider(t)&&t.addClass("ui-widget-content ui-menu-divider")}),i.not(".ui-menu-item, .ui-menu-divider").addClass("ui-menu-item").uniqueId().attr({tabIndex:-1,role:this._itemRole()}),i.filter(".ui-state-disabled").attr("aria-disabled","true"),this.active&&!e.contains(this.element[0],this.active[0])&&this.blur()},_itemRole:function(){return{menu:"menuitem",listbox:"option"}[this.options.role]},_setOption:function(e,t){"icons"===e&&this.element.find(".ui-menu-icon").removeClass(this.options.icons.submenu).addClass(t.submenu),"disabled"===e&&this.element.toggleClass("ui-state-disabled",!!t).attr("aria-disabled",t),this._super(e,t)},focus:function(e,t){var i,s;this.blur(e,e&&"focus"===e.type),this._scrollIntoView(t),this.active=t.first(),s=this.active.addClass("ui-state-focus").removeClass("ui-state-active"),this.options.role&&this.element.attr("aria-activedescendant",s.attr("id")),this.active.parent().closest(".ui-menu-item").addClass("ui-state-active"),e&&"keydown"===e.type?this._close():this.timer=this._delay(function(){this._close()},this.delay),i=t.children(".ui-menu"),i.length&&e&&/^mouse/.test(e.type)&&this._startOpening(i),this.activeMenu=t.parent(),this._trigger("focus",e,{item:t})},_scrollIntoView:function(t){var i,s,n,a,o,r;this._hasScroll()&&(i=parseFloat(e.css(this.activeMenu[0],"borderTopWidth"))||0,s=parseFloat(e.css(this.activeMenu[0],"paddingTop"))||0,n=t.offset().top-this.activeMenu.offset().top-i-s,a=this.activeMenu.scrollTop(),o=this.activeMenu.height(),r=t.outerHeight(),0>n?this.activeMenu.scrollTop(a+n):n+r>o&&this.activeMenu.scrollTop(a+n-o+r))},blur:function(e,t){t||clearTimeout(this.timer),this.active&&(this.active.removeClass("ui-state-focus"),this.active=null,this._trigger("blur",e,{item:this.active}))},_startOpening:function(e){clearTimeout(this.timer),"true"===e.attr("aria-hidden")&&(this.timer=this._delay(function(){this._close(),this._open(e)},this.delay))},_open:function(t){var i=e.extend({of:this.active},this.options.position);clearTimeout(this.timer),this.element.find(".ui-menu").not(t.parents(".ui-menu")).hide().attr("aria-hidden","true"),t.show().removeAttr("aria-hidden").attr("aria-expanded","true").position(i)},collapseAll:function(t,i){clearTimeout(this.timer),this.timer=this._delay(function(){var s=i?this.element:e(t&&t.target).closest(this.element.find(".ui-menu"));s.length||(s=this.element),this._close(s),this.blur(t),this.activeMenu=s},this.delay)},_close:function(e){e||(e=this.active?this.active.parent():this.element),e.find(".ui-menu").hide().attr("aria-hidden","true").attr("aria-expanded","false").end().find(".ui-state-active").not(".ui-state-focus").removeClass("ui-state-active")},_closeOnDocumentClick:function(t){return!e(t.target).closest(".ui-menu").length},_isDivider:function(e){return!/[^\-\u2014\u2013\s]/.test(e.text())},collapse:function(e){var t=this.active&&this.active.parent().closest(".ui-menu-item",this.element);t&&t.length&&(this._close(),this.focus(e,t))},expand:function(e){var t=this.active&&this.active.children(".ui-menu ").find(this.options.items).first();t&&t.length&&(this._open(t.parent()),this._delay(function(){this.focus(e,t)}))},next:function(e){this._move("next","first",e)},previous:function(e){this._move("prev","last",e)},isFirstItem:function(){return this.active&&!this.active.prevAll(".ui-menu-item").length},isLastItem:function(){return this.active&&!this.active.nextAll(".ui-menu-item").length},_move:function(e,t,i){var s;this.active&&(s="first"===e||"last"===e?this.active["first"===e?"prevAll":"nextAll"](".ui-menu-item").eq(-1):this.active[e+"All"](".ui-menu-item").eq(0)),s&&s.length&&this.active||(s=this.activeMenu.find(this.options.items)[t]()),this.focus(i,s)},nextPage:function(t){var i,s,n;return this.active?(this.isLastItem()||(this._hasScroll()?(s=this.active.offset().top,n=this.element.height(),this.active.nextAll(".ui-menu-item").each(function(){return i=e(this),0>i.offset().top-s-n}),this.focus(t,i)):this.focus(t,this.activeMenu.find(this.options.items)[this.active?"last":"first"]())),void 0):(this.next(t),void 0)},previousPage:function(t){var i,s,n;return this.active?(this.isFirstItem()||(this._hasScroll()?(s=this.active.offset().top,n=this.element.height(),this.active.prevAll(".ui-menu-item").each(function(){return i=e(this),i.offset().top-s+n>0}),this.focus(t,i)):this.focus(t,this.activeMenu.find(this.options.items).first())),void 0):(this.next(t),void 0)},_hasScroll:function(){return this.element.outerHeight()<this.element.prop("scrollHeight")},select:function(t){this.active=this.active||e(t.target).closest(".ui-menu-item");var i={item:this.active};this.active.has(".ui-menu").length||this.collapseAll(t,!0),this._trigger("select",t,i)},_filterMenuItems:function(t){var i=t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&"),s=RegExp("^"+i,"i");return this.activeMenu.find(this.options.items).filter(".ui-menu-item").filter(function(){return s.test(e.trim(e(this).text()))})}}),e.widget("ui.autocomplete",{version:"1.11.4",defaultElement:"<input>",options:{appendTo:null,autoFocus:!1,delay:300,minLength:1,position:{my:"left top",at:"left bottom",collision:"none"},source:null,change:null,close:null,focus:null,open:null,response:null,search:null,select:null},requestIndex:0,pending:0,_create:function(){var t,i,s,n=this.element[0].nodeName.toLowerCase(),a="textarea"===n,o="input"===n;this.isMultiLine=a?!0:o?!1:this.element.prop("isContentEditable"),this.valueMethod=this.element[a||o?"val":"text"],this.isNewMenu=!0,this.element.addClass("ui-autocomplete-input").attr("autocomplete","off"),this._on(this.element,{keydown:function(n){if(this.element.prop("readOnly"))return t=!0,s=!0,i=!0,void 0;t=!1,s=!1,i=!1;var a=e.ui.keyCode;switch(n.keyCode){case a.PAGE_UP:t=!0,this._move("previousPage",n);break;case a.PAGE_DOWN:t=!0,this._move("nextPage",n);break;case a.UP:t=!0,this._keyEvent("previous",n);break;case a.DOWN:t=!0,this._keyEvent("next",n);break;case a.ENTER:this.menu.active&&(t=!0,n.preventDefault(),this.menu.select(n));break;case a.TAB:this.menu.active&&this.menu.select(n);break;case a.ESCAPE:this.menu.element.is(":visible")&&(this.isMultiLine||this._value(this.term),this.close(n),n.preventDefault());break;default:i=!0,this._searchTimeout(n)}},keypress:function(s){if(t)return t=!1,(!this.isMultiLine||this.menu.element.is(":visible"))&&s.preventDefault(),void 0;if(!i){var n=e.ui.keyCode;switch(s.keyCode){case n.PAGE_UP:this._move("previousPage",s);break;case n.PAGE_DOWN:this._move("nextPage",s);break;case n.UP:this._keyEvent("previous",s);break;case n.DOWN:this._keyEvent("next",s)}}},input:function(e){return s?(s=!1,e.preventDefault(),void 0):(this._searchTimeout(e),void 0)},focus:function(){this.selectedItem=null,this.previous=this._value()},blur:function(e){return this.cancelBlur?(delete this.cancelBlur,void 0):(clearTimeout(this.searching),this.close(e),this._change(e),void 0)}}),this._initSource(),this.menu=e("<ul>").addClass("ui-autocomplete ui-front").appendTo(this._appendTo()).menu({role:null}).hide().menu("instance"),this._on(this.menu.element,{mousedown:function(t){t.preventDefault(),this.cancelBlur=!0,this._delay(function(){delete this.cancelBlur});var i=this.menu.element[0];e(t.target).closest(".ui-menu-item").length||this._delay(function(){var t=this;this.document.one("mousedown",function(s){s.target===t.element[0]||s.target===i||e.contains(i,s.target)||t.close()})})},menufocus:function(t,i){var s,n;return this.isNewMenu&&(this.isNewMenu=!1,t.originalEvent&&/^mouse/.test(t.originalEvent.type))?(this.menu.blur(),this.document.one("mousemove",function(){e(t.target).trigger(t.originalEvent)}),void 0):(n=i.item.data("ui-autocomplete-item"),!1!==this._trigger("focus",t,{item:n})&&t.originalEvent&&/^key/.test(t.originalEvent.type)&&this._value(n.value),s=i.item.attr("aria-label")||n.value,s&&e.trim(s).length&&(this.liveRegion.children().hide(),e("<div>").text(s).appendTo(this.liveRegion)),void 0)},menuselect:function(e,t){var i=t.item.data("ui-autocomplete-item"),s=this.previous;this.element[0]!==this.document[0].activeElement&&(this.element.focus(),this.previous=s,this._delay(function(){this.previous=s,this.selectedItem=i})),!1!==this._trigger("select",e,{item:i})&&this._value(i.value),this.term=this._value(),this.close(e),this.selectedItem=i}}),this.liveRegion=e("<span>",{role:"status","aria-live":"assertive","aria-relevant":"additions"}).addClass("ui-helper-hidden-accessible").appendTo(this.document[0].body),this._on(this.window,{beforeunload:function(){this.element.removeAttr("autocomplete")}})},_destroy:function(){clearTimeout(this.searching),this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete"),this.menu.element.remove(),this.liveRegion.remove()},_setOption:function(e,t){this._super(e,t),"source"===e&&this._initSource(),"appendTo"===e&&this.menu.element.appendTo(this._appendTo()),"disabled"===e&&t&&this.xhr&&this.xhr.abort()},_appendTo:function(){var t=this.options.appendTo;return t&&(t=t.jquery||t.nodeType?e(t):this.document.find(t).eq(0)),t&&t[0]||(t=this.element.closest(".ui-front")),t.length||(t=this.document[0].body),t},_initSource:function(){var t,i,s=this;e.isArray(this.options.source)?(t=this.options.source,this.source=function(i,s){s(e.ui.autocomplete.filter(t,i.term))}):"string"==typeof this.options.source?(i=this.options.source,this.source=function(t,n){s.xhr&&s.xhr.abort(),s.xhr=e.ajax({url:i,data:t,dataType:"json",success:function(e){n(e)},error:function(){n([])}})}):this.source=this.options.source},_searchTimeout:function(e){clearTimeout(this.searching),this.searching=this._delay(function(){var t=this.term===this._value(),i=this.menu.element.is(":visible"),s=e.altKey||e.ctrlKey||e.metaKey||e.shiftKey;(!t||t&&!i&&!s)&&(this.selectedItem=null,this.search(null,e))},this.options.delay)},search:function(e,t){return e=null!=e?e:this._value(),this.term=this._value(),e.length<this.options.minLength?this.close(t):this._trigger("search",t)!==!1?this._search(e):void 0},_search:function(e){this.pending++,this.element.addClass("ui-autocomplete-loading"),this.cancelSearch=!1,this.source({term:e},this._response())},_response:function(){var t=++this.requestIndex;return e.proxy(function(e){t===this.requestIndex&&this.__response(e),this.pending--,this.pending||this.element.removeClass("ui-autocomplete-loading")},this)},__response:function(e){e&&(e=this._normalize(e)),this._trigger("response",null,{content:e}),!this.options.disabled&&e&&e.length&&!this.cancelSearch?(this._suggest(e),this._trigger("open")):this._close()},close:function(e){this.cancelSearch=!0,this._close(e)},_close:function(e){this.menu.element.is(":visible")&&(this.menu.element.hide(),this.menu.blur(),this.isNewMenu=!0,this._trigger("close",e))},_change:function(e){this.previous!==this._value()&&this._trigger("change",e,{item:this.selectedItem})},_normalize:function(t){return t.length&&t[0].label&&t[0].value?t:e.map(t,function(t){return"string"==typeof t?{label:t,value:t}:e.extend({},t,{label:t.label||t.value,value:t.value||t.label})})},_suggest:function(t){var i=this.menu.element.empty();this._renderMenu(i,t),this.isNewMenu=!0,this.menu.refresh(),i.show(),this._resizeMenu(),i.position(e.extend({of:this.element},this.options.position)),this.options.autoFocus&&this.menu.next()},_resizeMenu:function(){var e=this.menu.element;e.outerWidth(Math.max(e.width("").outerWidth()+1,this.element.outerWidth()))},_renderMenu:function(t,i){var s=this;e.each(i,function(e,i){s._renderItemData(t,i)})},_renderItemData:function(e,t){return this._renderItem(e,t).data("ui-autocomplete-item",t)},_renderItem:function(t,i){return e("<li>").text(i.label).appendTo(t)},_move:function(e,t){return this.menu.element.is(":visible")?this.menu.isFirstItem()&&/^previous/.test(e)||this.menu.isLastItem()&&/^next/.test(e)?(this.isMultiLine||this._value(this.term),this.menu.blur(),void 0):(this.menu[e](t),void 0):(this.search(null,t),void 0)},widget:function(){return this.menu.element},_value:function(){return this.valueMethod.apply(this.element,arguments)},_keyEvent:function(e,t){(!this.isMultiLine||this.menu.element.is(":visible"))&&(this._move(e,t),t.preventDefault())}}),e.extend(e.ui.autocomplete,{escapeRegex:function(e){return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")},filter:function(t,i){var s=RegExp(e.ui.autocomplete.escapeRegex(i),"i");return e.grep(t,function(e){return s.test(e.label||e.value||e)})}}),e.widget("ui.autocomplete",e.ui.autocomplete,{options:{messages:{noResults:"No search results.",results:function(e){return e+(e>1?" results are":" result is")+" available, use up and down arrow keys to navigate."}}},__response:function(t){var i;this._superApply(arguments),this.options.disabled||this.cancelSearch||(i=t&&t.length?this.options.messages.results(t.length):this.options.messages.noResults,this.liveRegion.children().hide(),e("<div>").text(i).appendTo(this.liveRegion))}}),e.ui.autocomplete;var c,p="ui-button ui-widget ui-state-default ui-corner-all",f="ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",m=function(){var t=e(this);setTimeout(function(){t.find(":ui-button").button("refresh")},1)},g=function(t){var i=t.name,s=t.form,n=e([]);return i&&(i=i.replace(/'/g,"\\'"),n=s?e(s).find("[name='"+i+"'][type=radio]"):e("[name='"+i+"'][type=radio]",t.ownerDocument).filter(function(){return!this.form})),n};e.widget("ui.button",{version:"1.11.4",defaultElement:"<button>",options:{disabled:null,text:!0,label:null,icons:{primary:null,secondary:null}},_create:function(){this.element.closest("form").unbind("reset"+this.eventNamespace).bind("reset"+this.eventNamespace,m),"boolean"!=typeof this.options.disabled?this.options.disabled=!!this.element.prop("disabled"):this.element.prop("disabled",this.options.disabled),this._determineButtonType(),this.hasTitle=!!this.buttonElement.attr("title");var t=this,i=this.options,s="checkbox"===this.type||"radio"===this.type,n=s?"":"ui-state-active";null===i.label&&(i.label="input"===this.type?this.buttonElement.val():this.buttonElement.html()),this._hoverable(this.buttonElement),this.buttonElement.addClass(p).attr("role","button").bind("mouseenter"+this.eventNamespace,function(){i.disabled||this===c&&e(this).addClass("ui-state-active")}).bind("mouseleave"+this.eventNamespace,function(){i.disabled||e(this).removeClass(n)}).bind("click"+this.eventNamespace,function(e){i.disabled&&(e.preventDefault(),e.stopImmediatePropagation())}),this._on({focus:function(){this.buttonElement.addClass("ui-state-focus")},blur:function(){this.buttonElement.removeClass("ui-state-focus")}}),s&&this.element.bind("change"+this.eventNamespace,function(){t.refresh()}),"checkbox"===this.type?this.buttonElement.bind("click"+this.eventNamespace,function(){return i.disabled?!1:void 0}):"radio"===this.type?this.buttonElement.bind("click"+this.eventNamespace,function(){if(i.disabled)return!1;e(this).addClass("ui-state-active"),t.buttonElement.attr("aria-pressed","true");var s=t.element[0];g(s).not(s).map(function(){return e(this).button("widget")[0]}).removeClass("ui-state-active").attr("aria-pressed","false")}):(this.buttonElement.bind("mousedown"+this.eventNamespace,function(){return i.disabled?!1:(e(this).addClass("ui-state-active"),c=this,t.document.one("mouseup",function(){c=null}),void 0)}).bind("mouseup"+this.eventNamespace,function(){return i.disabled?!1:(e(this).removeClass("ui-state-active"),void 0)}).bind("keydown"+this.eventNamespace,function(t){return i.disabled?!1:((t.keyCode===e.ui.keyCode.SPACE||t.keyCode===e.ui.keyCode.ENTER)&&e(this).addClass("ui-state-active"),void 0)}).bind("keyup"+this.eventNamespace+" blur"+this.eventNamespace,function(){e(this).removeClass("ui-state-active")}),this.buttonElement.is("a")&&this.buttonElement.keyup(function(t){t.keyCode===e.ui.keyCode.SPACE&&e(this).click()})),this._setOption("disabled",i.disabled),this._resetButton()},_determineButtonType:function(){var e,t,i;this.type=this.element.is("[type=checkbox]")?"checkbox":this.element.is("[type=radio]")?"radio":this.element.is("input")?"input":"button","checkbox"===this.type||"radio"===this.type?(e=this.element.parents().last(),t="label[for='"+this.element.attr("id")+"']",this.buttonElement=e.find(t),this.buttonElement.length||(e=e.length?e.siblings():this.element.siblings(),this.buttonElement=e.filter(t),this.buttonElement.length||(this.buttonElement=e.find(t))),this.element.addClass("ui-helper-hidden-accessible"),i=this.element.is(":checked"),i&&this.buttonElement.addClass("ui-state-active"),this.buttonElement.prop("aria-pressed",i)):this.buttonElement=this.element},widget:function(){return this.buttonElement},_destroy:function(){this.element.removeClass("ui-helper-hidden-accessible"),this.buttonElement.removeClass(p+" ui-state-active "+f).removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html()),this.hasTitle||this.buttonElement.removeAttr("title")},_setOption:function(e,t){return this._super(e,t),"disabled"===e?(this.widget().toggleClass("ui-state-disabled",!!t),this.element.prop("disabled",!!t),t&&("checkbox"===this.type||"radio"===this.type?this.buttonElement.removeClass("ui-state-focus"):this.buttonElement.removeClass("ui-state-focus ui-state-active")),void 0):(this._resetButton(),void 0)},refresh:function(){var t=this.element.is("input, button")?this.element.is(":disabled"):this.element.hasClass("ui-button-disabled");t!==this.options.disabled&&this._setOption("disabled",t),"radio"===this.type?g(this.element[0]).each(function(){e(this).is(":checked")?e(this).button("widget").addClass("ui-state-active").attr("aria-pressed","true"):e(this).button("widget").removeClass("ui-state-active").attr("aria-pressed","false")}):"checkbox"===this.type&&(this.element.is(":checked")?this.buttonElement.addClass("ui-state-active").attr("aria-pressed","true"):this.buttonElement.removeClass("ui-state-active").attr("aria-pressed","false"))},_resetButton:function(){if("input"===this.type)return this.options.label&&this.element.val(this.options.label),void 0;var t=this.buttonElement.removeClass(f),i=e("<span></span>",this.document[0]).addClass("ui-button-text").html(this.options.label).appendTo(t.empty()).text(),s=this.options.icons,n=s.primary&&s.secondary,a=[];s.primary||s.secondary?(this.options.text&&a.push("ui-button-text-icon"+(n?"s":s.primary?"-primary":"-secondary")),s.primary&&t.prepend("<span class='ui-button-icon-primary ui-icon "+s.primary+"'></span>"),s.secondary&&t.append("<span class='ui-button-icon-secondary ui-icon "+s.secondary+"'></span>"),this.options.text||(a.push(n?"ui-button-icons-only":"ui-button-icon-only"),this.hasTitle||t.attr("title",e.trim(i)))):a.push("ui-button-text-only"),t.addClass(a.join(" "))}}),e.widget("ui.buttonset",{version:"1.11.4",options:{items:"button, input[type=button], input[type=submit], input[type=reset], input[type=checkbox], input[type=radio], a, :data(ui-button)"},_create:function(){this.element.addClass("ui-buttonset")},_init:function(){this.refresh()},_setOption:function(e,t){"disabled"===e&&this.buttons.button("option",e,t),this._super(e,t)},refresh:function(){var t="rtl"===this.element.css("direction"),i=this.element.find(this.options.items),s=i.filter(":ui-button");i.not(":ui-button").button(),s.button("refresh"),this.buttons=i.map(function(){return e(this).button("widget")[0]}).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass(t?"ui-corner-right":"ui-corner-left").end().filter(":last").addClass(t?"ui-corner-left":"ui-corner-right").end().end()},_destroy:function(){this.element.removeClass("ui-buttonset"),this.buttons.map(function(){return e(this).button("widget")[0]}).removeClass("ui-corner-left ui-corner-right").end().button("destroy")}}),e.ui.button,e.extend(e.ui,{datepicker:{version:"1.11.4"}});var v;e.extend(n.prototype,{markerClassName:"hasDatepicker",maxRows:4,_widgetDatepicker:function(){return this.dpDiv},setDefaults:function(e){return r(this._defaults,e||{}),this},_attachDatepicker:function(t,i){var s,n,a;s=t.nodeName.toLowerCase(),n="div"===s||"span"===s,t.id||(this.uuid+=1,t.id="dp"+this.uuid),a=this._newInst(e(t),n),a.settings=e.extend({},i||{}),"input"===s?this._connectDatepicker(t,a):n&&this._inlineDatepicker(t,a)},_newInst:function(t,i){var s=t[0].id.replace(/([^A-Za-z0-9_\-])/g,"\\\\$1");return{id:s,input:t,selectedDay:0,selectedMonth:0,selectedYear:0,drawMonth:0,drawYear:0,inline:i,dpDiv:i?a(e("<div class='"+this._inlineClass+" ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>")):this.dpDiv}},_connectDatepicker:function(t,i){var s=e(t);i.append=e([]),i.trigger=e([]),s.hasClass(this.markerClassName)||(this._attachments(s,i),s.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp),this._autoSize(i),e.data(t,"datepicker",i),i.settings.disabled&&this._disableDatepicker(t))},_attachments:function(t,i){var s,n,a,o=this._get(i,"appendText"),r=this._get(i,"isRTL");i.append&&i.append.remove(),o&&(i.append=e("<span class='"+this._appendClass+"'>"+o+"</span>"),t[r?"before":"after"](i.append)),t.unbind("focus",this._showDatepicker),i.trigger&&i.trigger.remove(),s=this._get(i,"showOn"),("focus"===s||"both"===s)&&t.focus(this._showDatepicker),("button"===s||"both"===s)&&(n=this._get(i,"buttonText"),a=this._get(i,"buttonImage"),i.trigger=e(this._get(i,"buttonImageOnly")?e("<img/>").addClass(this._triggerClass).attr({src:a,alt:n,title:n}):e("<button type='button'></button>").addClass(this._triggerClass).html(a?e("<img/>").attr({src:a,alt:n,title:n}):n)),t[r?"before":"after"](i.trigger),i.trigger.click(function(){return e.datepicker._datepickerShowing&&e.datepicker._lastInput===t[0]?e.datepicker._hideDatepicker():e.datepicker._datepickerShowing&&e.datepicker._lastInput!==t[0]?(e.datepicker._hideDatepicker(),e.datepicker._showDatepicker(t[0])):e.datepicker._showDatepicker(t[0]),!1}))},_autoSize:function(e){if(this._get(e,"autoSize")&&!e.inline){var t,i,s,n,a=new Date(2009,11,20),o=this._get(e,"dateFormat");o.match(/[DM]/)&&(t=function(e){for(i=0,s=0,n=0;e.length>n;n++)e[n].length>i&&(i=e[n].length,s=n);return s},a.setMonth(t(this._get(e,o.match(/MM/)?"monthNames":"monthNamesShort"))),a.setDate(t(this._get(e,o.match(/DD/)?"dayNames":"dayNamesShort"))+20-a.getDay())),e.input.attr("size",this._formatDate(e,a).length)}},_inlineDatepicker:function(t,i){var s=e(t);s.hasClass(this.markerClassName)||(s.addClass(this.markerClassName).append(i.dpDiv),e.data(t,"datepicker",i),this._setDate(i,this._getDefaultDate(i),!0),this._updateDatepicker(i),this._updateAlternate(i),i.settings.disabled&&this._disableDatepicker(t),i.dpDiv.css("display","block"))},_dialogDatepicker:function(t,i,s,n,a){var o,h,l,u,d,c=this._dialogInst;return c||(this.uuid+=1,o="dp"+this.uuid,this._dialogInput=e("<input type='text' id='"+o+"' style='position: absolute; top: -100px; width: 0px;'/>"),this._dialogInput.keydown(this._doKeyDown),e("body").append(this._dialogInput),c=this._dialogInst=this._newInst(this._dialogInput,!1),c.settings={},e.data(this._dialogInput[0],"datepicker",c)),r(c.settings,n||{}),i=i&&i.constructor===Date?this._formatDate(c,i):i,this._dialogInput.val(i),this._pos=a?a.length?a:[a.pageX,a.pageY]:null,this._pos||(h=document.documentElement.clientWidth,l=document.documentElement.clientHeight,u=document.documentElement.scrollLeft||document.body.scrollLeft,d=document.documentElement.scrollTop||document.body.scrollTop,this._pos=[h/2-100+u,l/2-150+d]),this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px"),c.settings.onSelect=s,this._inDialog=!0,this.dpDiv.addClass(this._dialogClass),this._showDatepicker(this._dialogInput[0]),e.blockUI&&e.blockUI(this.dpDiv),e.data(this._dialogInput[0],"datepicker",c),this
},_destroyDatepicker:function(t){var i,s=e(t),n=e.data(t,"datepicker");s.hasClass(this.markerClassName)&&(i=t.nodeName.toLowerCase(),e.removeData(t,"datepicker"),"input"===i?(n.append.remove(),n.trigger.remove(),s.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp)):("div"===i||"span"===i)&&s.removeClass(this.markerClassName).empty(),v===n&&(v=null))},_enableDatepicker:function(t){var i,s,n=e(t),a=e.data(t,"datepicker");n.hasClass(this.markerClassName)&&(i=t.nodeName.toLowerCase(),"input"===i?(t.disabled=!1,a.trigger.filter("button").each(function(){this.disabled=!1}).end().filter("img").css({opacity:"1.0",cursor:""})):("div"===i||"span"===i)&&(s=n.children("."+this._inlineClass),s.children().removeClass("ui-state-disabled"),s.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled",!1)),this._disabledInputs=e.map(this._disabledInputs,function(e){return e===t?null:e}))},_disableDatepicker:function(t){var i,s,n=e(t),a=e.data(t,"datepicker");n.hasClass(this.markerClassName)&&(i=t.nodeName.toLowerCase(),"input"===i?(t.disabled=!0,a.trigger.filter("button").each(function(){this.disabled=!0}).end().filter("img").css({opacity:"0.5",cursor:"default"})):("div"===i||"span"===i)&&(s=n.children("."+this._inlineClass),s.children().addClass("ui-state-disabled"),s.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled",!0)),this._disabledInputs=e.map(this._disabledInputs,function(e){return e===t?null:e}),this._disabledInputs[this._disabledInputs.length]=t)},_isDisabledDatepicker:function(e){if(!e)return!1;for(var t=0;this._disabledInputs.length>t;t++)if(this._disabledInputs[t]===e)return!0;return!1},_getInst:function(t){try{return e.data(t,"datepicker")}catch(i){throw"Missing instance data for this datepicker"}},_optionDatepicker:function(t,i,s){var n,a,o,h,l=this._getInst(t);return 2===arguments.length&&"string"==typeof i?"defaults"===i?e.extend({},e.datepicker._defaults):l?"all"===i?e.extend({},l.settings):this._get(l,i):null:(n=i||{},"string"==typeof i&&(n={},n[i]=s),l&&(this._curInst===l&&this._hideDatepicker(),a=this._getDateDatepicker(t,!0),o=this._getMinMaxDate(l,"min"),h=this._getMinMaxDate(l,"max"),r(l.settings,n),null!==o&&void 0!==n.dateFormat&&void 0===n.minDate&&(l.settings.minDate=this._formatDate(l,o)),null!==h&&void 0!==n.dateFormat&&void 0===n.maxDate&&(l.settings.maxDate=this._formatDate(l,h)),"disabled"in n&&(n.disabled?this._disableDatepicker(t):this._enableDatepicker(t)),this._attachments(e(t),l),this._autoSize(l),this._setDate(l,a),this._updateAlternate(l),this._updateDatepicker(l)),void 0)},_changeDatepicker:function(e,t,i){this._optionDatepicker(e,t,i)},_refreshDatepicker:function(e){var t=this._getInst(e);t&&this._updateDatepicker(t)},_setDateDatepicker:function(e,t){var i=this._getInst(e);i&&(this._setDate(i,t),this._updateDatepicker(i),this._updateAlternate(i))},_getDateDatepicker:function(e,t){var i=this._getInst(e);return i&&!i.inline&&this._setDateFromField(i,t),i?this._getDate(i):null},_doKeyDown:function(t){var i,s,n,a=e.datepicker._getInst(t.target),o=!0,r=a.dpDiv.is(".ui-datepicker-rtl");if(a._keyEvent=!0,e.datepicker._datepickerShowing)switch(t.keyCode){case 9:e.datepicker._hideDatepicker(),o=!1;break;case 13:return n=e("td."+e.datepicker._dayOverClass+":not(."+e.datepicker._currentClass+")",a.dpDiv),n[0]&&e.datepicker._selectDay(t.target,a.selectedMonth,a.selectedYear,n[0]),i=e.datepicker._get(a,"onSelect"),i?(s=e.datepicker._formatDate(a),i.apply(a.input?a.input[0]:null,[s,a])):e.datepicker._hideDatepicker(),!1;case 27:e.datepicker._hideDatepicker();break;case 33:e.datepicker._adjustDate(t.target,t.ctrlKey?-e.datepicker._get(a,"stepBigMonths"):-e.datepicker._get(a,"stepMonths"),"M");break;case 34:e.datepicker._adjustDate(t.target,t.ctrlKey?+e.datepicker._get(a,"stepBigMonths"):+e.datepicker._get(a,"stepMonths"),"M");break;case 35:(t.ctrlKey||t.metaKey)&&e.datepicker._clearDate(t.target),o=t.ctrlKey||t.metaKey;break;case 36:(t.ctrlKey||t.metaKey)&&e.datepicker._gotoToday(t.target),o=t.ctrlKey||t.metaKey;break;case 37:(t.ctrlKey||t.metaKey)&&e.datepicker._adjustDate(t.target,r?1:-1,"D"),o=t.ctrlKey||t.metaKey,t.originalEvent.altKey&&e.datepicker._adjustDate(t.target,t.ctrlKey?-e.datepicker._get(a,"stepBigMonths"):-e.datepicker._get(a,"stepMonths"),"M");break;case 38:(t.ctrlKey||t.metaKey)&&e.datepicker._adjustDate(t.target,-7,"D"),o=t.ctrlKey||t.metaKey;break;case 39:(t.ctrlKey||t.metaKey)&&e.datepicker._adjustDate(t.target,r?-1:1,"D"),o=t.ctrlKey||t.metaKey,t.originalEvent.altKey&&e.datepicker._adjustDate(t.target,t.ctrlKey?+e.datepicker._get(a,"stepBigMonths"):+e.datepicker._get(a,"stepMonths"),"M");break;case 40:(t.ctrlKey||t.metaKey)&&e.datepicker._adjustDate(t.target,7,"D"),o=t.ctrlKey||t.metaKey;break;default:o=!1}else 36===t.keyCode&&t.ctrlKey?e.datepicker._showDatepicker(this):o=!1;o&&(t.preventDefault(),t.stopPropagation())},_doKeyPress:function(t){var i,s,n=e.datepicker._getInst(t.target);return e.datepicker._get(n,"constrainInput")?(i=e.datepicker._possibleChars(e.datepicker._get(n,"dateFormat")),s=String.fromCharCode(null==t.charCode?t.keyCode:t.charCode),t.ctrlKey||t.metaKey||" ">s||!i||i.indexOf(s)>-1):void 0},_doKeyUp:function(t){var i,s=e.datepicker._getInst(t.target);if(s.input.val()!==s.lastVal)try{i=e.datepicker.parseDate(e.datepicker._get(s,"dateFormat"),s.input?s.input.val():null,e.datepicker._getFormatConfig(s)),i&&(e.datepicker._setDateFromField(s),e.datepicker._updateAlternate(s),e.datepicker._updateDatepicker(s))}catch(n){}return!0},_showDatepicker:function(t){if(t=t.target||t,"input"!==t.nodeName.toLowerCase()&&(t=e("input",t.parentNode)[0]),!e.datepicker._isDisabledDatepicker(t)&&e.datepicker._lastInput!==t){var i,n,a,o,h,l,u;i=e.datepicker._getInst(t),e.datepicker._curInst&&e.datepicker._curInst!==i&&(e.datepicker._curInst.dpDiv.stop(!0,!0),i&&e.datepicker._datepickerShowing&&e.datepicker._hideDatepicker(e.datepicker._curInst.input[0])),n=e.datepicker._get(i,"beforeShow"),a=n?n.apply(t,[t,i]):{},a!==!1&&(r(i.settings,a),i.lastVal=null,e.datepicker._lastInput=t,e.datepicker._setDateFromField(i),e.datepicker._inDialog&&(t.value=""),e.datepicker._pos||(e.datepicker._pos=e.datepicker._findPos(t),e.datepicker._pos[1]+=t.offsetHeight),o=!1,e(t).parents().each(function(){return o|="fixed"===e(this).css("position"),!o}),h={left:e.datepicker._pos[0],top:e.datepicker._pos[1]},e.datepicker._pos=null,i.dpDiv.empty(),i.dpDiv.css({position:"absolute",display:"block",top:"-1000px"}),e.datepicker._updateDatepicker(i),h=e.datepicker._checkOffset(i,h,o),i.dpDiv.css({position:e.datepicker._inDialog&&e.blockUI?"static":o?"fixed":"absolute",display:"none",left:h.left+"px",top:h.top+"px"}),i.inline||(l=e.datepicker._get(i,"showAnim"),u=e.datepicker._get(i,"duration"),i.dpDiv.css("z-index",s(e(t))+1),e.datepicker._datepickerShowing=!0,e.effects&&e.effects.effect[l]?i.dpDiv.show(l,e.datepicker._get(i,"showOptions"),u):i.dpDiv[l||"show"](l?u:null),e.datepicker._shouldFocusInput(i)&&i.input.focus(),e.datepicker._curInst=i))}},_updateDatepicker:function(t){this.maxRows=4,v=t,t.dpDiv.empty().append(this._generateHTML(t)),this._attachHandlers(t);var i,s=this._getNumberOfMonths(t),n=s[1],a=17,r=t.dpDiv.find("."+this._dayOverClass+" a");r.length>0&&o.apply(r.get(0)),t.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""),n>1&&t.dpDiv.addClass("ui-datepicker-multi-"+n).css("width",a*n+"em"),t.dpDiv[(1!==s[0]||1!==s[1]?"add":"remove")+"Class"]("ui-datepicker-multi"),t.dpDiv[(this._get(t,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl"),t===e.datepicker._curInst&&e.datepicker._datepickerShowing&&e.datepicker._shouldFocusInput(t)&&t.input.focus(),t.yearshtml&&(i=t.yearshtml,setTimeout(function(){i===t.yearshtml&&t.yearshtml&&t.dpDiv.find("select.ui-datepicker-year:first").replaceWith(t.yearshtml),i=t.yearshtml=null},0))},_shouldFocusInput:function(e){return e.input&&e.input.is(":visible")&&!e.input.is(":disabled")&&!e.input.is(":focus")},_checkOffset:function(t,i,s){var n=t.dpDiv.outerWidth(),a=t.dpDiv.outerHeight(),o=t.input?t.input.outerWidth():0,r=t.input?t.input.outerHeight():0,h=document.documentElement.clientWidth+(s?0:e(document).scrollLeft()),l=document.documentElement.clientHeight+(s?0:e(document).scrollTop());return i.left-=this._get(t,"isRTL")?n-o:0,i.left-=s&&i.left===t.input.offset().left?e(document).scrollLeft():0,i.top-=s&&i.top===t.input.offset().top+r?e(document).scrollTop():0,i.left-=Math.min(i.left,i.left+n>h&&h>n?Math.abs(i.left+n-h):0),i.top-=Math.min(i.top,i.top+a>l&&l>a?Math.abs(a+r):0),i},_findPos:function(t){for(var i,s=this._getInst(t),n=this._get(s,"isRTL");t&&("hidden"===t.type||1!==t.nodeType||e.expr.filters.hidden(t));)t=t[n?"previousSibling":"nextSibling"];return i=e(t).offset(),[i.left,i.top]},_hideDatepicker:function(t){var i,s,n,a,o=this._curInst;!o||t&&o!==e.data(t,"datepicker")||this._datepickerShowing&&(i=this._get(o,"showAnim"),s=this._get(o,"duration"),n=function(){e.datepicker._tidyDialog(o)},e.effects&&(e.effects.effect[i]||e.effects[i])?o.dpDiv.hide(i,e.datepicker._get(o,"showOptions"),s,n):o.dpDiv["slideDown"===i?"slideUp":"fadeIn"===i?"fadeOut":"hide"](i?s:null,n),i||n(),this._datepickerShowing=!1,a=this._get(o,"onClose"),a&&a.apply(o.input?o.input[0]:null,[o.input?o.input.val():"",o]),this._lastInput=null,this._inDialog&&(this._dialogInput.css({position:"absolute",left:"0",top:"-100px"}),e.blockUI&&(e.unblockUI(),e("body").append(this.dpDiv))),this._inDialog=!1)},_tidyDialog:function(e){e.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")},_checkExternalClick:function(t){if(e.datepicker._curInst){var i=e(t.target),s=e.datepicker._getInst(i[0]);(i[0].id!==e.datepicker._mainDivId&&0===i.parents("#"+e.datepicker._mainDivId).length&&!i.hasClass(e.datepicker.markerClassName)&&!i.closest("."+e.datepicker._triggerClass).length&&e.datepicker._datepickerShowing&&(!e.datepicker._inDialog||!e.blockUI)||i.hasClass(e.datepicker.markerClassName)&&e.datepicker._curInst!==s)&&e.datepicker._hideDatepicker()}},_adjustDate:function(t,i,s){var n=e(t),a=this._getInst(n[0]);this._isDisabledDatepicker(n[0])||(this._adjustInstDate(a,i+("M"===s?this._get(a,"showCurrentAtPos"):0),s),this._updateDatepicker(a))},_gotoToday:function(t){var i,s=e(t),n=this._getInst(s[0]);this._get(n,"gotoCurrent")&&n.currentDay?(n.selectedDay=n.currentDay,n.drawMonth=n.selectedMonth=n.currentMonth,n.drawYear=n.selectedYear=n.currentYear):(i=new Date,n.selectedDay=i.getDate(),n.drawMonth=n.selectedMonth=i.getMonth(),n.drawYear=n.selectedYear=i.getFullYear()),this._notifyChange(n),this._adjustDate(s)},_selectMonthYear:function(t,i,s){var n=e(t),a=this._getInst(n[0]);a["selected"+("M"===s?"Month":"Year")]=a["draw"+("M"===s?"Month":"Year")]=parseInt(i.options[i.selectedIndex].value,10),this._notifyChange(a),this._adjustDate(n)},_selectDay:function(t,i,s,n){var a,o=e(t);e(n).hasClass(this._unselectableClass)||this._isDisabledDatepicker(o[0])||(a=this._getInst(o[0]),a.selectedDay=a.currentDay=e("a",n).html(),a.selectedMonth=a.currentMonth=i,a.selectedYear=a.currentYear=s,this._selectDate(t,this._formatDate(a,a.currentDay,a.currentMonth,a.currentYear)))},_clearDate:function(t){var i=e(t);this._selectDate(i,"")},_selectDate:function(t,i){var s,n=e(t),a=this._getInst(n[0]);i=null!=i?i:this._formatDate(a),a.input&&a.input.val(i),this._updateAlternate(a),s=this._get(a,"onSelect"),s?s.apply(a.input?a.input[0]:null,[i,a]):a.input&&a.input.trigger("change"),a.inline?this._updateDatepicker(a):(this._hideDatepicker(),this._lastInput=a.input[0],"object"!=typeof a.input[0]&&a.input.focus(),this._lastInput=null)},_updateAlternate:function(t){var i,s,n,a=this._get(t,"altField");a&&(i=this._get(t,"altFormat")||this._get(t,"dateFormat"),s=this._getDate(t),n=this.formatDate(i,s,this._getFormatConfig(t)),e(a).each(function(){e(this).val(n)}))},noWeekends:function(e){var t=e.getDay();return[t>0&&6>t,""]},iso8601Week:function(e){var t,i=new Date(e.getTime());return i.setDate(i.getDate()+4-(i.getDay()||7)),t=i.getTime(),i.setMonth(0),i.setDate(1),Math.floor(Math.round((t-i)/864e5)/7)+1},parseDate:function(t,i,s){if(null==t||null==i)throw"Invalid arguments";if(i="object"==typeof i?""+i:i+"",""===i)return null;var n,a,o,r,h=0,l=(s?s.shortYearCutoff:null)||this._defaults.shortYearCutoff,u="string"!=typeof l?l:(new Date).getFullYear()%100+parseInt(l,10),d=(s?s.dayNamesShort:null)||this._defaults.dayNamesShort,c=(s?s.dayNames:null)||this._defaults.dayNames,p=(s?s.monthNamesShort:null)||this._defaults.monthNamesShort,f=(s?s.monthNames:null)||this._defaults.monthNames,m=-1,g=-1,v=-1,y=-1,b=!1,_=function(e){var i=t.length>n+1&&t.charAt(n+1)===e;return i&&n++,i},x=function(e){var t=_(e),s="@"===e?14:"!"===e?20:"y"===e&&t?4:"o"===e?3:2,n="y"===e?s:1,a=RegExp("^\\d{"+n+","+s+"}"),o=i.substring(h).match(a);if(!o)throw"Missing number at position "+h;return h+=o[0].length,parseInt(o[0],10)},w=function(t,s,n){var a=-1,o=e.map(_(t)?n:s,function(e,t){return[[t,e]]}).sort(function(e,t){return-(e[1].length-t[1].length)});if(e.each(o,function(e,t){var s=t[1];return i.substr(h,s.length).toLowerCase()===s.toLowerCase()?(a=t[0],h+=s.length,!1):void 0}),-1!==a)return a+1;throw"Unknown name at position "+h},k=function(){if(i.charAt(h)!==t.charAt(n))throw"Unexpected literal at position "+h;h++};for(n=0;t.length>n;n++)if(b)"'"!==t.charAt(n)||_("'")?k():b=!1;else switch(t.charAt(n)){case"d":v=x("d");break;case"D":w("D",d,c);break;case"o":y=x("o");break;case"m":g=x("m");break;case"M":g=w("M",p,f);break;case"y":m=x("y");break;case"@":r=new Date(x("@")),m=r.getFullYear(),g=r.getMonth()+1,v=r.getDate();break;case"!":r=new Date((x("!")-this._ticksTo1970)/1e4),m=r.getFullYear(),g=r.getMonth()+1,v=r.getDate();break;case"'":_("'")?k():b=!0;break;default:k()}if(i.length>h&&(o=i.substr(h),!/^\s+/.test(o)))throw"Extra/unparsed characters found in date: "+o;if(-1===m?m=(new Date).getFullYear():100>m&&(m+=(new Date).getFullYear()-(new Date).getFullYear()%100+(u>=m?0:-100)),y>-1)for(g=1,v=y;;){if(a=this._getDaysInMonth(m,g-1),a>=v)break;g++,v-=a}if(r=this._daylightSavingAdjust(new Date(m,g-1,v)),r.getFullYear()!==m||r.getMonth()+1!==g||r.getDate()!==v)throw"Invalid date";return r},ATOM:"yy-mm-dd",COOKIE:"D, dd M yy",ISO_8601:"yy-mm-dd",RFC_822:"D, d M y",RFC_850:"DD, dd-M-y",RFC_1036:"D, d M y",RFC_1123:"D, d M yy",RFC_2822:"D, d M yy",RSS:"D, d M y",TICKS:"!",TIMESTAMP:"@",W3C:"yy-mm-dd",_ticksTo1970:1e7*60*60*24*(718685+Math.floor(492.5)-Math.floor(19.7)+Math.floor(4.925)),formatDate:function(e,t,i){if(!t)return"";var s,n=(i?i.dayNamesShort:null)||this._defaults.dayNamesShort,a=(i?i.dayNames:null)||this._defaults.dayNames,o=(i?i.monthNamesShort:null)||this._defaults.monthNamesShort,r=(i?i.monthNames:null)||this._defaults.monthNames,h=function(t){var i=e.length>s+1&&e.charAt(s+1)===t;return i&&s++,i},l=function(e,t,i){var s=""+t;if(h(e))for(;i>s.length;)s="0"+s;return s},u=function(e,t,i,s){return h(e)?s[t]:i[t]},d="",c=!1;if(t)for(s=0;e.length>s;s++)if(c)"'"!==e.charAt(s)||h("'")?d+=e.charAt(s):c=!1;else switch(e.charAt(s)){case"d":d+=l("d",t.getDate(),2);break;case"D":d+=u("D",t.getDay(),n,a);break;case"o":d+=l("o",Math.round((new Date(t.getFullYear(),t.getMonth(),t.getDate()).getTime()-new Date(t.getFullYear(),0,0).getTime())/864e5),3);break;case"m":d+=l("m",t.getMonth()+1,2);break;case"M":d+=u("M",t.getMonth(),o,r);break;case"y":d+=h("y")?t.getFullYear():(10>t.getYear()%100?"0":"")+t.getYear()%100;break;case"@":d+=t.getTime();break;case"!":d+=1e4*t.getTime()+this._ticksTo1970;break;case"'":h("'")?d+="'":c=!0;break;default:d+=e.charAt(s)}return d},_possibleChars:function(e){var t,i="",s=!1,n=function(i){var s=e.length>t+1&&e.charAt(t+1)===i;return s&&t++,s};for(t=0;e.length>t;t++)if(s)"'"!==e.charAt(t)||n("'")?i+=e.charAt(t):s=!1;else switch(e.charAt(t)){case"d":case"m":case"y":case"@":i+="0123456789";break;case"D":case"M":return null;case"'":n("'")?i+="'":s=!0;break;default:i+=e.charAt(t)}return i},_get:function(e,t){return void 0!==e.settings[t]?e.settings[t]:this._defaults[t]},_setDateFromField:function(e,t){if(e.input.val()!==e.lastVal){var i=this._get(e,"dateFormat"),s=e.lastVal=e.input?e.input.val():null,n=this._getDefaultDate(e),a=n,o=this._getFormatConfig(e);try{a=this.parseDate(i,s,o)||n}catch(r){s=t?"":s}e.selectedDay=a.getDate(),e.drawMonth=e.selectedMonth=a.getMonth(),e.drawYear=e.selectedYear=a.getFullYear(),e.currentDay=s?a.getDate():0,e.currentMonth=s?a.getMonth():0,e.currentYear=s?a.getFullYear():0,this._adjustInstDate(e)}},_getDefaultDate:function(e){return this._restrictMinMax(e,this._determineDate(e,this._get(e,"defaultDate"),new Date))},_determineDate:function(t,i,s){var n=function(e){var t=new Date;return t.setDate(t.getDate()+e),t},a=function(i){try{return e.datepicker.parseDate(e.datepicker._get(t,"dateFormat"),i,e.datepicker._getFormatConfig(t))}catch(s){}for(var n=(i.toLowerCase().match(/^c/)?e.datepicker._getDate(t):null)||new Date,a=n.getFullYear(),o=n.getMonth(),r=n.getDate(),h=/([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,l=h.exec(i);l;){switch(l[2]||"d"){case"d":case"D":r+=parseInt(l[1],10);break;case"w":case"W":r+=7*parseInt(l[1],10);break;case"m":case"M":o+=parseInt(l[1],10),r=Math.min(r,e.datepicker._getDaysInMonth(a,o));break;case"y":case"Y":a+=parseInt(l[1],10),r=Math.min(r,e.datepicker._getDaysInMonth(a,o))}l=h.exec(i)}return new Date(a,o,r)},o=null==i||""===i?s:"string"==typeof i?a(i):"number"==typeof i?isNaN(i)?s:n(i):new Date(i.getTime());return o=o&&"Invalid Date"==""+o?s:o,o&&(o.setHours(0),o.setMinutes(0),o.setSeconds(0),o.setMilliseconds(0)),this._daylightSavingAdjust(o)},_daylightSavingAdjust:function(e){return e?(e.setHours(e.getHours()>12?e.getHours()+2:0),e):null},_setDate:function(e,t,i){var s=!t,n=e.selectedMonth,a=e.selectedYear,o=this._restrictMinMax(e,this._determineDate(e,t,new Date));e.selectedDay=e.currentDay=o.getDate(),e.drawMonth=e.selectedMonth=e.currentMonth=o.getMonth(),e.drawYear=e.selectedYear=e.currentYear=o.getFullYear(),n===e.selectedMonth&&a===e.selectedYear||i||this._notifyChange(e),this._adjustInstDate(e),e.input&&e.input.val(s?"":this._formatDate(e))},_getDate:function(e){var t=!e.currentYear||e.input&&""===e.input.val()?null:this._daylightSavingAdjust(new Date(e.currentYear,e.currentMonth,e.currentDay));return t},_attachHandlers:function(t){var i=this._get(t,"stepMonths"),s="#"+t.id.replace(/\\\\/g,"\\");t.dpDiv.find("[data-handler]").map(function(){var t={prev:function(){e.datepicker._adjustDate(s,-i,"M")},next:function(){e.datepicker._adjustDate(s,+i,"M")},hide:function(){e.datepicker._hideDatepicker()},today:function(){e.datepicker._gotoToday(s)},selectDay:function(){return e.datepicker._selectDay(s,+this.getAttribute("data-month"),+this.getAttribute("data-year"),this),!1},selectMonth:function(){return e.datepicker._selectMonthYear(s,this,"M"),!1},selectYear:function(){return e.datepicker._selectMonthYear(s,this,"Y"),!1}};e(this).bind(this.getAttribute("data-event"),t[this.getAttribute("data-handler")])})},_generateHTML:function(e){var t,i,s,n,a,o,r,h,l,u,d,c,p,f,m,g,v,y,b,_,x,w,k,T,D,S,M,C,N,A,P,I,H,z,F,E,O,j,W,L=new Date,R=this._daylightSavingAdjust(new Date(L.getFullYear(),L.getMonth(),L.getDate())),Y=this._get(e,"isRTL"),B=this._get(e,"showButtonPanel"),J=this._get(e,"hideIfNoPrevNext"),q=this._get(e,"navigationAsDateFormat"),K=this._getNumberOfMonths(e),V=this._get(e,"showCurrentAtPos"),U=this._get(e,"stepMonths"),Q=1!==K[0]||1!==K[1],G=this._daylightSavingAdjust(e.currentDay?new Date(e.currentYear,e.currentMonth,e.currentDay):new Date(9999,9,9)),X=this._getMinMaxDate(e,"min"),$=this._getMinMaxDate(e,"max"),Z=e.drawMonth-V,et=e.drawYear;if(0>Z&&(Z+=12,et--),$)for(t=this._daylightSavingAdjust(new Date($.getFullYear(),$.getMonth()-K[0]*K[1]+1,$.getDate())),t=X&&X>t?X:t;this._daylightSavingAdjust(new Date(et,Z,1))>t;)Z--,0>Z&&(Z=11,et--);for(e.drawMonth=Z,e.drawYear=et,i=this._get(e,"prevText"),i=q?this.formatDate(i,this._daylightSavingAdjust(new Date(et,Z-U,1)),this._getFormatConfig(e)):i,s=this._canAdjustMonth(e,-1,et,Z)?"<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click' title='"+i+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"e":"w")+"'>"+i+"</span></a>":J?"":"<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='"+i+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"e":"w")+"'>"+i+"</span></a>",n=this._get(e,"nextText"),n=q?this.formatDate(n,this._daylightSavingAdjust(new Date(et,Z+U,1)),this._getFormatConfig(e)):n,a=this._canAdjustMonth(e,1,et,Z)?"<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click' title='"+n+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"w":"e")+"'>"+n+"</span></a>":J?"":"<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='"+n+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"w":"e")+"'>"+n+"</span></a>",o=this._get(e,"currentText"),r=this._get(e,"gotoCurrent")&&e.currentDay?G:R,o=q?this.formatDate(o,r,this._getFormatConfig(e)):o,h=e.inline?"":"<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>"+this._get(e,"closeText")+"</button>",l=B?"<div class='ui-datepicker-buttonpane ui-widget-content'>"+(Y?h:"")+(this._isInRange(e,r)?"<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'>"+o+"</button>":"")+(Y?"":h)+"</div>":"",u=parseInt(this._get(e,"firstDay"),10),u=isNaN(u)?0:u,d=this._get(e,"showWeek"),c=this._get(e,"dayNames"),p=this._get(e,"dayNamesMin"),f=this._get(e,"monthNames"),m=this._get(e,"monthNamesShort"),g=this._get(e,"beforeShowDay"),v=this._get(e,"showOtherMonths"),y=this._get(e,"selectOtherMonths"),b=this._getDefaultDate(e),_="",w=0;K[0]>w;w++){for(k="",this.maxRows=4,T=0;K[1]>T;T++){if(D=this._daylightSavingAdjust(new Date(et,Z,e.selectedDay)),S=" ui-corner-all",M="",Q){if(M+="<div class='ui-datepicker-group",K[1]>1)switch(T){case 0:M+=" ui-datepicker-group-first",S=" ui-corner-"+(Y?"right":"left");break;case K[1]-1:M+=" ui-datepicker-group-last",S=" ui-corner-"+(Y?"left":"right");break;default:M+=" ui-datepicker-group-middle",S=""}M+="'>"}for(M+="<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix"+S+"'>"+(/all|left/.test(S)&&0===w?Y?a:s:"")+(/all|right/.test(S)&&0===w?Y?s:a:"")+this._generateMonthYearHeader(e,Z,et,X,$,w>0||T>0,f,m)+"</div><table class='ui-datepicker-calendar'><thead>"+"<tr>",C=d?"<th class='ui-datepicker-week-col'>"+this._get(e,"weekHeader")+"</th>":"",x=0;7>x;x++)N=(x+u)%7,C+="<th scope='col'"+((x+u+6)%7>=5?" class='ui-datepicker-week-end'":"")+">"+"<span title='"+c[N]+"'>"+p[N]+"</span></th>";for(M+=C+"</tr></thead><tbody>",A=this._getDaysInMonth(et,Z),et===e.selectedYear&&Z===e.selectedMonth&&(e.selectedDay=Math.min(e.selectedDay,A)),P=(this._getFirstDayOfMonth(et,Z)-u+7)%7,I=Math.ceil((P+A)/7),H=Q?this.maxRows>I?this.maxRows:I:I,this.maxRows=H,z=this._daylightSavingAdjust(new Date(et,Z,1-P)),F=0;H>F;F++){for(M+="<tr>",E=d?"<td class='ui-datepicker-week-col'>"+this._get(e,"calculateWeek")(z)+"</td>":"",x=0;7>x;x++)O=g?g.apply(e.input?e.input[0]:null,[z]):[!0,""],j=z.getMonth()!==Z,W=j&&!y||!O[0]||X&&X>z||$&&z>$,E+="<td class='"+((x+u+6)%7>=5?" ui-datepicker-week-end":"")+(j?" ui-datepicker-other-month":"")+(z.getTime()===D.getTime()&&Z===e.selectedMonth&&e._keyEvent||b.getTime()===z.getTime()&&b.getTime()===D.getTime()?" "+this._dayOverClass:"")+(W?" "+this._unselectableClass+" ui-state-disabled":"")+(j&&!v?"":" "+O[1]+(z.getTime()===G.getTime()?" "+this._currentClass:"")+(z.getTime()===R.getTime()?" ui-datepicker-today":""))+"'"+(j&&!v||!O[2]?"":" title='"+O[2].replace(/'/g,"&#39;")+"'")+(W?"":" data-handler='selectDay' data-event='click' data-month='"+z.getMonth()+"' data-year='"+z.getFullYear()+"'")+">"+(j&&!v?"&#xa0;":W?"<span class='ui-state-default'>"+z.getDate()+"</span>":"<a class='ui-state-default"+(z.getTime()===R.getTime()?" ui-state-highlight":"")+(z.getTime()===G.getTime()?" ui-state-active":"")+(j?" ui-priority-secondary":"")+"' href='#'>"+z.getDate()+"</a>")+"</td>",z.setDate(z.getDate()+1),z=this._daylightSavingAdjust(z);M+=E+"</tr>"}Z++,Z>11&&(Z=0,et++),M+="</tbody></table>"+(Q?"</div>"+(K[0]>0&&T===K[1]-1?"<div class='ui-datepicker-row-break'></div>":""):""),k+=M}_+=k}return _+=l,e._keyEvent=!1,_},_generateMonthYearHeader:function(e,t,i,s,n,a,o,r){var h,l,u,d,c,p,f,m,g=this._get(e,"changeMonth"),v=this._get(e,"changeYear"),y=this._get(e,"showMonthAfterYear"),b="<div class='ui-datepicker-title'>",_="";if(a||!g)_+="<span class='ui-datepicker-month'>"+o[t]+"</span>";else{for(h=s&&s.getFullYear()===i,l=n&&n.getFullYear()===i,_+="<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>",u=0;12>u;u++)(!h||u>=s.getMonth())&&(!l||n.getMonth()>=u)&&(_+="<option value='"+u+"'"+(u===t?" selected='selected'":"")+">"+r[u]+"</option>");_+="</select>"}if(y||(b+=_+(!a&&g&&v?"":"&#xa0;")),!e.yearshtml)if(e.yearshtml="",a||!v)b+="<span class='ui-datepicker-year'>"+i+"</span>";else{for(d=this._get(e,"yearRange").split(":"),c=(new Date).getFullYear(),p=function(e){var t=e.match(/c[+\-].*/)?i+parseInt(e.substring(1),10):e.match(/[+\-].*/)?c+parseInt(e,10):parseInt(e,10);return isNaN(t)?c:t},f=p(d[0]),m=Math.max(f,p(d[1]||"")),f=s?Math.max(f,s.getFullYear()):f,m=n?Math.min(m,n.getFullYear()):m,e.yearshtml+="<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>";m>=f;f++)e.yearshtml+="<option value='"+f+"'"+(f===i?" selected='selected'":"")+">"+f+"</option>";e.yearshtml+="</select>",b+=e.yearshtml,e.yearshtml=null}return b+=this._get(e,"yearSuffix"),y&&(b+=(!a&&g&&v?"":"&#xa0;")+_),b+="</div>"},_adjustInstDate:function(e,t,i){var s=e.drawYear+("Y"===i?t:0),n=e.drawMonth+("M"===i?t:0),a=Math.min(e.selectedDay,this._getDaysInMonth(s,n))+("D"===i?t:0),o=this._restrictMinMax(e,this._daylightSavingAdjust(new Date(s,n,a)));e.selectedDay=o.getDate(),e.drawMonth=e.selectedMonth=o.getMonth(),e.drawYear=e.selectedYear=o.getFullYear(),("M"===i||"Y"===i)&&this._notifyChange(e)},_restrictMinMax:function(e,t){var i=this._getMinMaxDate(e,"min"),s=this._getMinMaxDate(e,"max"),n=i&&i>t?i:t;return s&&n>s?s:n},_notifyChange:function(e){var t=this._get(e,"onChangeMonthYear");t&&t.apply(e.input?e.input[0]:null,[e.selectedYear,e.selectedMonth+1,e])},_getNumberOfMonths:function(e){var t=this._get(e,"numberOfMonths");return null==t?[1,1]:"number"==typeof t?[1,t]:t},_getMinMaxDate:function(e,t){return this._determineDate(e,this._get(e,t+"Date"),null)},_getDaysInMonth:function(e,t){return 32-this._daylightSavingAdjust(new Date(e,t,32)).getDate()},_getFirstDayOfMonth:function(e,t){return new Date(e,t,1).getDay()},_canAdjustMonth:function(e,t,i,s){var n=this._getNumberOfMonths(e),a=this._daylightSavingAdjust(new Date(i,s+(0>t?t:n[0]*n[1]),1));return 0>t&&a.setDate(this._getDaysInMonth(a.getFullYear(),a.getMonth())),this._isInRange(e,a)},_isInRange:function(e,t){var i,s,n=this._getMinMaxDate(e,"min"),a=this._getMinMaxDate(e,"max"),o=null,r=null,h=this._get(e,"yearRange");return h&&(i=h.split(":"),s=(new Date).getFullYear(),o=parseInt(i[0],10),r=parseInt(i[1],10),i[0].match(/[+\-].*/)&&(o+=s),i[1].match(/[+\-].*/)&&(r+=s)),(!n||t.getTime()>=n.getTime())&&(!a||t.getTime()<=a.getTime())&&(!o||t.getFullYear()>=o)&&(!r||r>=t.getFullYear())},_getFormatConfig:function(e){var t=this._get(e,"shortYearCutoff");return t="string"!=typeof t?t:(new Date).getFullYear()%100+parseInt(t,10),{shortYearCutoff:t,dayNamesShort:this._get(e,"dayNamesShort"),dayNames:this._get(e,"dayNames"),monthNamesShort:this._get(e,"monthNamesShort"),monthNames:this._get(e,"monthNames")}},_formatDate:function(e,t,i,s){t||(e.currentDay=e.selectedDay,e.currentMonth=e.selectedMonth,e.currentYear=e.selectedYear);var n=t?"object"==typeof t?t:this._daylightSavingAdjust(new Date(s,i,t)):this._daylightSavingAdjust(new Date(e.currentYear,e.currentMonth,e.currentDay));return this.formatDate(this._get(e,"dateFormat"),n,this._getFormatConfig(e))}}),e.fn.datepicker=function(t){if(!this.length)return this;e.datepicker.initialized||(e(document).mousedown(e.datepicker._checkExternalClick),e.datepicker.initialized=!0),0===e("#"+e.datepicker._mainDivId).length&&e("body").append(e.datepicker.dpDiv);var i=Array.prototype.slice.call(arguments,1);return"string"!=typeof t||"isDisabled"!==t&&"getDate"!==t&&"widget"!==t?"option"===t&&2===arguments.length&&"string"==typeof arguments[1]?e.datepicker["_"+t+"Datepicker"].apply(e.datepicker,[this[0]].concat(i)):this.each(function(){"string"==typeof t?e.datepicker["_"+t+"Datepicker"].apply(e.datepicker,[this].concat(i)):e.datepicker._attachDatepicker(this,t)}):e.datepicker["_"+t+"Datepicker"].apply(e.datepicker,[this[0]].concat(i))},e.datepicker=new n,e.datepicker.initialized=!1,e.datepicker.uuid=(new Date).getTime(),e.datepicker.version="1.11.4",e.datepicker,e.widget("ui.dialog",{version:"1.11.4",options:{appendTo:"body",autoOpen:!0,buttons:[],closeOnEscape:!0,closeText:"Close",dialogClass:"",draggable:!0,hide:null,height:"auto",maxHeight:null,maxWidth:null,minHeight:150,minWidth:150,modal:!1,position:{my:"center",at:"center",of:window,collision:"fit",using:function(t){var i=e(this).css(t).offset().top;0>i&&e(this).css("top",t.top-i)}},resizable:!0,show:null,title:null,width:300,beforeClose:null,close:null,drag:null,dragStart:null,dragStop:null,focus:null,open:null,resize:null,resizeStart:null,resizeStop:null},sizeRelatedOptions:{buttons:!0,height:!0,maxHeight:!0,maxWidth:!0,minHeight:!0,minWidth:!0,width:!0},resizableRelatedOptions:{maxHeight:!0,maxWidth:!0,minHeight:!0,minWidth:!0},_create:function(){this.originalCss={display:this.element[0].style.display,width:this.element[0].style.width,minHeight:this.element[0].style.minHeight,maxHeight:this.element[0].style.maxHeight,height:this.element[0].style.height},this.originalPosition={parent:this.element.parent(),index:this.element.parent().children().index(this.element)},this.originalTitle=this.element.attr("title"),this.options.title=this.options.title||this.originalTitle,this._createWrapper(),this.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(this.uiDialog),this._createTitlebar(),this._createButtonPane(),this.options.draggable&&e.fn.draggable&&this._makeDraggable(),this.options.resizable&&e.fn.resizable&&this._makeResizable(),this._isOpen=!1,this._trackFocus()},_init:function(){this.options.autoOpen&&this.open()},_appendTo:function(){var t=this.options.appendTo;return t&&(t.jquery||t.nodeType)?e(t):this.document.find(t||"body").eq(0)},_destroy:function(){var e,t=this.originalPosition;this._untrackInstance(),this._destroyOverlay(),this.element.removeUniqueId().removeClass("ui-dialog-content ui-widget-content").css(this.originalCss).detach(),this.uiDialog.stop(!0,!0).remove(),this.originalTitle&&this.element.attr("title",this.originalTitle),e=t.parent.children().eq(t.index),e.length&&e[0]!==this.element[0]?e.before(this.element):t.parent.append(this.element)},widget:function(){return this.uiDialog},disable:e.noop,enable:e.noop,close:function(t){var i,s=this;if(this._isOpen&&this._trigger("beforeClose",t)!==!1){if(this._isOpen=!1,this._focusedElement=null,this._destroyOverlay(),this._untrackInstance(),!this.opener.filter(":focusable").focus().length)try{i=this.document[0].activeElement,i&&"body"!==i.nodeName.toLowerCase()&&e(i).blur()}catch(n){}this._hide(this.uiDialog,this.options.hide,function(){s._trigger("close",t)})}},isOpen:function(){return this._isOpen},moveToTop:function(){this._moveToTop()},_moveToTop:function(t,i){var s=!1,n=this.uiDialog.siblings(".ui-front:visible").map(function(){return+e(this).css("z-index")}).get(),a=Math.max.apply(null,n);return a>=+this.uiDialog.css("z-index")&&(this.uiDialog.css("z-index",a+1),s=!0),s&&!i&&this._trigger("focus",t),s},open:function(){var t=this;
return this._isOpen?(this._moveToTop()&&this._focusTabbable(),void 0):(this._isOpen=!0,this.opener=e(this.document[0].activeElement),this._size(),this._position(),this._createOverlay(),this._moveToTop(null,!0),this.overlay&&this.overlay.css("z-index",this.uiDialog.css("z-index")-1),this._show(this.uiDialog,this.options.show,function(){t._focusTabbable(),t._trigger("focus")}),this._makeFocusTarget(),this._trigger("open"),void 0)},_focusTabbable:function(){var e=this._focusedElement;e||(e=this.element.find("[autofocus]")),e.length||(e=this.element.find(":tabbable")),e.length||(e=this.uiDialogButtonPane.find(":tabbable")),e.length||(e=this.uiDialogTitlebarClose.filter(":tabbable")),e.length||(e=this.uiDialog),e.eq(0).focus()},_keepFocus:function(t){function i(){var t=this.document[0].activeElement,i=this.uiDialog[0]===t||e.contains(this.uiDialog[0],t);i||this._focusTabbable()}t.preventDefault(),i.call(this),this._delay(i)},_createWrapper:function(){this.uiDialog=e("<div>").addClass("ui-dialog ui-widget ui-widget-content ui-corner-all ui-front "+this.options.dialogClass).hide().attr({tabIndex:-1,role:"dialog"}).appendTo(this._appendTo()),this._on(this.uiDialog,{keydown:function(t){if(this.options.closeOnEscape&&!t.isDefaultPrevented()&&t.keyCode&&t.keyCode===e.ui.keyCode.ESCAPE)return t.preventDefault(),this.close(t),void 0;if(t.keyCode===e.ui.keyCode.TAB&&!t.isDefaultPrevented()){var i=this.uiDialog.find(":tabbable"),s=i.filter(":first"),n=i.filter(":last");t.target!==n[0]&&t.target!==this.uiDialog[0]||t.shiftKey?t.target!==s[0]&&t.target!==this.uiDialog[0]||!t.shiftKey||(this._delay(function(){n.focus()}),t.preventDefault()):(this._delay(function(){s.focus()}),t.preventDefault())}},mousedown:function(e){this._moveToTop(e)&&this._focusTabbable()}}),this.element.find("[aria-describedby]").length||this.uiDialog.attr({"aria-describedby":this.element.uniqueId().attr("id")})},_createTitlebar:function(){var t;this.uiDialogTitlebar=e("<div>").addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(this.uiDialog),this._on(this.uiDialogTitlebar,{mousedown:function(t){e(t.target).closest(".ui-dialog-titlebar-close")||this.uiDialog.focus()}}),this.uiDialogTitlebarClose=e("<button type='button'></button>").button({label:this.options.closeText,icons:{primary:"ui-icon-closethick"},text:!1}).addClass("ui-dialog-titlebar-close").appendTo(this.uiDialogTitlebar),this._on(this.uiDialogTitlebarClose,{click:function(e){e.preventDefault(),this.close(e)}}),t=e("<span>").uniqueId().addClass("ui-dialog-title").prependTo(this.uiDialogTitlebar),this._title(t),this.uiDialog.attr({"aria-labelledby":t.attr("id")})},_title:function(e){this.options.title||e.html("&#160;"),e.text(this.options.title)},_createButtonPane:function(){this.uiDialogButtonPane=e("<div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"),this.uiButtonSet=e("<div>").addClass("ui-dialog-buttonset").appendTo(this.uiDialogButtonPane),this._createButtons()},_createButtons:function(){var t=this,i=this.options.buttons;return this.uiDialogButtonPane.remove(),this.uiButtonSet.empty(),e.isEmptyObject(i)||e.isArray(i)&&!i.length?(this.uiDialog.removeClass("ui-dialog-buttons"),void 0):(e.each(i,function(i,s){var n,a;s=e.isFunction(s)?{click:s,text:i}:s,s=e.extend({type:"button"},s),n=s.click,s.click=function(){n.apply(t.element[0],arguments)},a={icons:s.icons,text:s.showText},delete s.icons,delete s.showText,e("<button></button>",s).button(a).appendTo(t.uiButtonSet)}),this.uiDialog.addClass("ui-dialog-buttons"),this.uiDialogButtonPane.appendTo(this.uiDialog),void 0)},_makeDraggable:function(){function t(e){return{position:e.position,offset:e.offset}}var i=this,s=this.options;this.uiDialog.draggable({cancel:".ui-dialog-content, .ui-dialog-titlebar-close",handle:".ui-dialog-titlebar",containment:"document",start:function(s,n){e(this).addClass("ui-dialog-dragging"),i._blockFrames(),i._trigger("dragStart",s,t(n))},drag:function(e,s){i._trigger("drag",e,t(s))},stop:function(n,a){var o=a.offset.left-i.document.scrollLeft(),r=a.offset.top-i.document.scrollTop();s.position={my:"left top",at:"left"+(o>=0?"+":"")+o+" "+"top"+(r>=0?"+":"")+r,of:i.window},e(this).removeClass("ui-dialog-dragging"),i._unblockFrames(),i._trigger("dragStop",n,t(a))}})},_makeResizable:function(){function t(e){return{originalPosition:e.originalPosition,originalSize:e.originalSize,position:e.position,size:e.size}}var i=this,s=this.options,n=s.resizable,a=this.uiDialog.css("position"),o="string"==typeof n?n:"n,e,s,w,se,sw,ne,nw";this.uiDialog.resizable({cancel:".ui-dialog-content",containment:"document",alsoResize:this.element,maxWidth:s.maxWidth,maxHeight:s.maxHeight,minWidth:s.minWidth,minHeight:this._minHeight(),handles:o,start:function(s,n){e(this).addClass("ui-dialog-resizing"),i._blockFrames(),i._trigger("resizeStart",s,t(n))},resize:function(e,s){i._trigger("resize",e,t(s))},stop:function(n,a){var o=i.uiDialog.offset(),r=o.left-i.document.scrollLeft(),h=o.top-i.document.scrollTop();s.height=i.uiDialog.height(),s.width=i.uiDialog.width(),s.position={my:"left top",at:"left"+(r>=0?"+":"")+r+" "+"top"+(h>=0?"+":"")+h,of:i.window},e(this).removeClass("ui-dialog-resizing"),i._unblockFrames(),i._trigger("resizeStop",n,t(a))}}).css("position",a)},_trackFocus:function(){this._on(this.widget(),{focusin:function(t){this._makeFocusTarget(),this._focusedElement=e(t.target)}})},_makeFocusTarget:function(){this._untrackInstance(),this._trackingInstances().unshift(this)},_untrackInstance:function(){var t=this._trackingInstances(),i=e.inArray(this,t);-1!==i&&t.splice(i,1)},_trackingInstances:function(){var e=this.document.data("ui-dialog-instances");return e||(e=[],this.document.data("ui-dialog-instances",e)),e},_minHeight:function(){var e=this.options;return"auto"===e.height?e.minHeight:Math.min(e.minHeight,e.height)},_position:function(){var e=this.uiDialog.is(":visible");e||this.uiDialog.show(),this.uiDialog.position(this.options.position),e||this.uiDialog.hide()},_setOptions:function(t){var i=this,s=!1,n={};e.each(t,function(e,t){i._setOption(e,t),e in i.sizeRelatedOptions&&(s=!0),e in i.resizableRelatedOptions&&(n[e]=t)}),s&&(this._size(),this._position()),this.uiDialog.is(":data(ui-resizable)")&&this.uiDialog.resizable("option",n)},_setOption:function(e,t){var i,s,n=this.uiDialog;"dialogClass"===e&&n.removeClass(this.options.dialogClass).addClass(t),"disabled"!==e&&(this._super(e,t),"appendTo"===e&&this.uiDialog.appendTo(this._appendTo()),"buttons"===e&&this._createButtons(),"closeText"===e&&this.uiDialogTitlebarClose.button({label:""+t}),"draggable"===e&&(i=n.is(":data(ui-draggable)"),i&&!t&&n.draggable("destroy"),!i&&t&&this._makeDraggable()),"position"===e&&this._position(),"resizable"===e&&(s=n.is(":data(ui-resizable)"),s&&!t&&n.resizable("destroy"),s&&"string"==typeof t&&n.resizable("option","handles",t),s||t===!1||this._makeResizable()),"title"===e&&this._title(this.uiDialogTitlebar.find(".ui-dialog-title")))},_size:function(){var e,t,i,s=this.options;this.element.show().css({width:"auto",minHeight:0,maxHeight:"none",height:0}),s.minWidth>s.width&&(s.width=s.minWidth),e=this.uiDialog.css({height:"auto",width:s.width}).outerHeight(),t=Math.max(0,s.minHeight-e),i="number"==typeof s.maxHeight?Math.max(0,s.maxHeight-e):"none","auto"===s.height?this.element.css({minHeight:t,maxHeight:i,height:"auto"}):this.element.height(Math.max(0,s.height-e)),this.uiDialog.is(":data(ui-resizable)")&&this.uiDialog.resizable("option","minHeight",this._minHeight())},_blockFrames:function(){this.iframeBlocks=this.document.find("iframe").map(function(){var t=e(this);return e("<div>").css({position:"absolute",width:t.outerWidth(),height:t.outerHeight()}).appendTo(t.parent()).offset(t.offset())[0]})},_unblockFrames:function(){this.iframeBlocks&&(this.iframeBlocks.remove(),delete this.iframeBlocks)},_allowInteraction:function(t){return e(t.target).closest(".ui-dialog").length?!0:!!e(t.target).closest(".ui-datepicker").length},_createOverlay:function(){if(this.options.modal){var t=!0;this._delay(function(){t=!1}),this.document.data("ui-dialog-overlays")||this._on(this.document,{focusin:function(e){t||this._allowInteraction(e)||(e.preventDefault(),this._trackingInstances()[0]._focusTabbable())}}),this.overlay=e("<div>").addClass("ui-widget-overlay ui-front").appendTo(this._appendTo()),this._on(this.overlay,{mousedown:"_keepFocus"}),this.document.data("ui-dialog-overlays",(this.document.data("ui-dialog-overlays")||0)+1)}},_destroyOverlay:function(){if(this.options.modal&&this.overlay){var e=this.document.data("ui-dialog-overlays")-1;e?this.document.data("ui-dialog-overlays",e):this.document.unbind("focusin").removeData("ui-dialog-overlays"),this.overlay.remove(),this.overlay=null}}}),e.widget("ui.progressbar",{version:"1.11.4",options:{max:100,value:0,change:null,complete:null},min:0,_create:function(){this.oldValue=this.options.value=this._constrainedValue(),this.element.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").attr({role:"progressbar","aria-valuemin":this.min}),this.valueDiv=e("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>").appendTo(this.element),this._refreshValue()},_destroy:function(){this.element.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow"),this.valueDiv.remove()},value:function(e){return void 0===e?this.options.value:(this.options.value=this._constrainedValue(e),this._refreshValue(),void 0)},_constrainedValue:function(e){return void 0===e&&(e=this.options.value),this.indeterminate=e===!1,"number"!=typeof e&&(e=0),this.indeterminate?!1:Math.min(this.options.max,Math.max(this.min,e))},_setOptions:function(e){var t=e.value;delete e.value,this._super(e),this.options.value=this._constrainedValue(t),this._refreshValue()},_setOption:function(e,t){"max"===e&&(t=Math.max(this.min,t)),"disabled"===e&&this.element.toggleClass("ui-state-disabled",!!t).attr("aria-disabled",t),this._super(e,t)},_percentage:function(){return this.indeterminate?100:100*(this.options.value-this.min)/(this.options.max-this.min)},_refreshValue:function(){var t=this.options.value,i=this._percentage();this.valueDiv.toggle(this.indeterminate||t>this.min).toggleClass("ui-corner-right",t===this.options.max).width(i.toFixed(0)+"%"),this.element.toggleClass("ui-progressbar-indeterminate",this.indeterminate),this.indeterminate?(this.element.removeAttr("aria-valuenow"),this.overlayDiv||(this.overlayDiv=e("<div class='ui-progressbar-overlay'></div>").appendTo(this.valueDiv))):(this.element.attr({"aria-valuemax":this.options.max,"aria-valuenow":t}),this.overlayDiv&&(this.overlayDiv.remove(),this.overlayDiv=null)),this.oldValue!==t&&(this.oldValue=t,this._trigger("change")),t===this.options.max&&this._trigger("complete")}}),e.widget("ui.selectmenu",{version:"1.11.4",defaultElement:"<select>",options:{appendTo:null,disabled:null,icons:{button:"ui-icon-triangle-1-s"},position:{my:"left top",at:"left bottom",collision:"none"},width:null,change:null,close:null,focus:null,open:null,select:null},_create:function(){var e=this.element.uniqueId().attr("id");this.ids={element:e,button:e+"-button",menu:e+"-menu"},this._drawButton(),this._drawMenu(),this.options.disabled&&this.disable()},_drawButton:function(){var t=this;this.label=e("label[for='"+this.ids.element+"']").attr("for",this.ids.button),this._on(this.label,{click:function(e){this.button.focus(),e.preventDefault()}}),this.element.hide(),this.button=e("<span>",{"class":"ui-selectmenu-button ui-widget ui-state-default ui-corner-all",tabindex:this.options.disabled?-1:0,id:this.ids.button,role:"combobox","aria-expanded":"false","aria-autocomplete":"list","aria-owns":this.ids.menu,"aria-haspopup":"true"}).insertAfter(this.element),e("<span>",{"class":"ui-icon "+this.options.icons.button}).prependTo(this.button),this.buttonText=e("<span>",{"class":"ui-selectmenu-text"}).appendTo(this.button),this._setText(this.buttonText,this.element.find("option:selected").text()),this._resizeButton(),this._on(this.button,this._buttonEvents),this.button.one("focusin",function(){t.menuItems||t._refreshMenu()}),this._hoverable(this.button),this._focusable(this.button)},_drawMenu:function(){var t=this;this.menu=e("<ul>",{"aria-hidden":"true","aria-labelledby":this.ids.button,id:this.ids.menu}),this.menuWrap=e("<div>",{"class":"ui-selectmenu-menu ui-front"}).append(this.menu).appendTo(this._appendTo()),this.menuInstance=this.menu.menu({role:"listbox",select:function(e,i){e.preventDefault(),t._setSelection(),t._select(i.item.data("ui-selectmenu-item"),e)},focus:function(e,i){var s=i.item.data("ui-selectmenu-item");null!=t.focusIndex&&s.index!==t.focusIndex&&(t._trigger("focus",e,{item:s}),t.isOpen||t._select(s,e)),t.focusIndex=s.index,t.button.attr("aria-activedescendant",t.menuItems.eq(s.index).attr("id"))}}).menu("instance"),this.menu.addClass("ui-corner-bottom").removeClass("ui-corner-all"),this.menuInstance._off(this.menu,"mouseleave"),this.menuInstance._closeOnDocumentClick=function(){return!1},this.menuInstance._isDivider=function(){return!1}},refresh:function(){this._refreshMenu(),this._setText(this.buttonText,this._getSelectedItem().text()),this.options.width||this._resizeButton()},_refreshMenu:function(){this.menu.empty();var e,t=this.element.find("option");t.length&&(this._parseOptions(t),this._renderMenu(this.menu,this.items),this.menuInstance.refresh(),this.menuItems=this.menu.find("li").not(".ui-selectmenu-optgroup"),e=this._getSelectedItem(),this.menuInstance.focus(null,e),this._setAria(e.data("ui-selectmenu-item")),this._setOption("disabled",this.element.prop("disabled")))},open:function(e){this.options.disabled||(this.menuItems?(this.menu.find(".ui-state-focus").removeClass("ui-state-focus"),this.menuInstance.focus(null,this._getSelectedItem())):this._refreshMenu(),this.isOpen=!0,this._toggleAttr(),this._resizeMenu(),this._position(),this._on(this.document,this._documentClick),this._trigger("open",e))},_position:function(){this.menuWrap.position(e.extend({of:this.button},this.options.position))},close:function(e){this.isOpen&&(this.isOpen=!1,this._toggleAttr(),this.range=null,this._off(this.document),this._trigger("close",e))},widget:function(){return this.button},menuWidget:function(){return this.menu},_renderMenu:function(t,i){var s=this,n="";e.each(i,function(i,a){a.optgroup!==n&&(e("<li>",{"class":"ui-selectmenu-optgroup ui-menu-divider"+(a.element.parent("optgroup").prop("disabled")?" ui-state-disabled":""),text:a.optgroup}).appendTo(t),n=a.optgroup),s._renderItemData(t,a)})},_renderItemData:function(e,t){return this._renderItem(e,t).data("ui-selectmenu-item",t)},_renderItem:function(t,i){var s=e("<li>");return i.disabled&&s.addClass("ui-state-disabled"),this._setText(s,i.label),s.appendTo(t)},_setText:function(e,t){t?e.text(t):e.html("&#160;")},_move:function(e,t){var i,s,n=".ui-menu-item";this.isOpen?i=this.menuItems.eq(this.focusIndex):(i=this.menuItems.eq(this.element[0].selectedIndex),n+=":not(.ui-state-disabled)"),s="first"===e||"last"===e?i["first"===e?"prevAll":"nextAll"](n).eq(-1):i[e+"All"](n).eq(0),s.length&&this.menuInstance.focus(t,s)},_getSelectedItem:function(){return this.menuItems.eq(this.element[0].selectedIndex)},_toggle:function(e){this[this.isOpen?"close":"open"](e)},_setSelection:function(){var e;this.range&&(window.getSelection?(e=window.getSelection(),e.removeAllRanges(),e.addRange(this.range)):this.range.select(),this.button.focus())},_documentClick:{mousedown:function(t){this.isOpen&&(e(t.target).closest(".ui-selectmenu-menu, #"+this.ids.button).length||this.close(t))}},_buttonEvents:{mousedown:function(){var e;window.getSelection?(e=window.getSelection(),e.rangeCount&&(this.range=e.getRangeAt(0))):this.range=document.selection.createRange()},click:function(e){this._setSelection(),this._toggle(e)},keydown:function(t){var i=!0;switch(t.keyCode){case e.ui.keyCode.TAB:case e.ui.keyCode.ESCAPE:this.close(t),i=!1;break;case e.ui.keyCode.ENTER:this.isOpen&&this._selectFocusedItem(t);break;case e.ui.keyCode.UP:t.altKey?this._toggle(t):this._move("prev",t);break;case e.ui.keyCode.DOWN:t.altKey?this._toggle(t):this._move("next",t);break;case e.ui.keyCode.SPACE:this.isOpen?this._selectFocusedItem(t):this._toggle(t);break;case e.ui.keyCode.LEFT:this._move("prev",t);break;case e.ui.keyCode.RIGHT:this._move("next",t);break;case e.ui.keyCode.HOME:case e.ui.keyCode.PAGE_UP:this._move("first",t);break;case e.ui.keyCode.END:case e.ui.keyCode.PAGE_DOWN:this._move("last",t);break;default:this.menu.trigger(t),i=!1}i&&t.preventDefault()}},_selectFocusedItem:function(e){var t=this.menuItems.eq(this.focusIndex);t.hasClass("ui-state-disabled")||this._select(t.data("ui-selectmenu-item"),e)},_select:function(e,t){var i=this.element[0].selectedIndex;this.element[0].selectedIndex=e.index,this._setText(this.buttonText,e.label),this._setAria(e),this._trigger("select",t,{item:e}),e.index!==i&&this._trigger("change",t,{item:e}),this.close(t)},_setAria:function(e){var t=this.menuItems.eq(e.index).attr("id");this.button.attr({"aria-labelledby":t,"aria-activedescendant":t}),this.menu.attr("aria-activedescendant",t)},_setOption:function(e,t){"icons"===e&&this.button.find("span.ui-icon").removeClass(this.options.icons.button).addClass(t.button),this._super(e,t),"appendTo"===e&&this.menuWrap.appendTo(this._appendTo()),"disabled"===e&&(this.menuInstance.option("disabled",t),this.button.toggleClass("ui-state-disabled",t).attr("aria-disabled",t),this.element.prop("disabled",t),t?(this.button.attr("tabindex",-1),this.close()):this.button.attr("tabindex",0)),"width"===e&&this._resizeButton()},_appendTo:function(){var t=this.options.appendTo;return t&&(t=t.jquery||t.nodeType?e(t):this.document.find(t).eq(0)),t&&t[0]||(t=this.element.closest(".ui-front")),t.length||(t=this.document[0].body),t},_toggleAttr:function(){this.button.toggleClass("ui-corner-top",this.isOpen).toggleClass("ui-corner-all",!this.isOpen).attr("aria-expanded",this.isOpen),this.menuWrap.toggleClass("ui-selectmenu-open",this.isOpen),this.menu.attr("aria-hidden",!this.isOpen)},_resizeButton:function(){var e=this.options.width;e||(e=this.element.show().outerWidth(),this.element.hide()),this.button.outerWidth(e)},_resizeMenu:function(){this.menu.outerWidth(Math.max(this.button.outerWidth(),this.menu.width("").outerWidth()+1))},_getCreateOptions:function(){return{disabled:this.element.prop("disabled")}},_parseOptions:function(t){var i=[];t.each(function(t,s){var n=e(s),a=n.parent("optgroup");i.push({element:n,index:t,value:n.val(),label:n.text(),optgroup:a.attr("label")||"",disabled:a.prop("disabled")||n.prop("disabled")})}),this.items=i},_destroy:function(){this.menuWrap.remove(),this.button.remove(),this.element.show(),this.element.removeUniqueId(),this.label.attr("for",this.ids.element)}}),e.widget("ui.slider",e.ui.mouse,{version:"1.11.4",widgetEventPrefix:"slide",options:{animate:!1,distance:0,max:100,min:0,orientation:"horizontal",range:!1,step:1,value:0,values:null,change:null,slide:null,start:null,stop:null},numPages:5,_create:function(){this._keySliding=!1,this._mouseSliding=!1,this._animateOff=!0,this._handleIndex=null,this._detectOrientation(),this._mouseInit(),this._calculateNewMax(),this.element.addClass("ui-slider ui-slider-"+this.orientation+" ui-widget"+" ui-widget-content"+" ui-corner-all"),this._refresh(),this._setOption("disabled",this.options.disabled),this._animateOff=!1},_refresh:function(){this._createRange(),this._createHandles(),this._setupEvents(),this._refreshValue()},_createHandles:function(){var t,i,s=this.options,n=this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),a="<span class='ui-slider-handle ui-state-default ui-corner-all' tabindex='0'></span>",o=[];for(i=s.values&&s.values.length||1,n.length>i&&(n.slice(i).remove(),n=n.slice(0,i)),t=n.length;i>t;t++)o.push(a);this.handles=n.add(e(o.join("")).appendTo(this.element)),this.handle=this.handles.eq(0),this.handles.each(function(t){e(this).data("ui-slider-handle-index",t)})},_createRange:function(){var t=this.options,i="";t.range?(t.range===!0&&(t.values?t.values.length&&2!==t.values.length?t.values=[t.values[0],t.values[0]]:e.isArray(t.values)&&(t.values=t.values.slice(0)):t.values=[this._valueMin(),this._valueMin()]),this.range&&this.range.length?this.range.removeClass("ui-slider-range-min ui-slider-range-max").css({left:"",bottom:""}):(this.range=e("<div></div>").appendTo(this.element),i="ui-slider-range ui-widget-header ui-corner-all"),this.range.addClass(i+("min"===t.range||"max"===t.range?" ui-slider-range-"+t.range:""))):(this.range&&this.range.remove(),this.range=null)},_setupEvents:function(){this._off(this.handles),this._on(this.handles,this._handleEvents),this._hoverable(this.handles),this._focusable(this.handles)},_destroy:function(){this.handles.remove(),this.range&&this.range.remove(),this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-widget ui-widget-content ui-corner-all"),this._mouseDestroy()},_mouseCapture:function(t){var i,s,n,a,o,r,h,l,u=this,d=this.options;return d.disabled?!1:(this.elementSize={width:this.element.outerWidth(),height:this.element.outerHeight()},this.elementOffset=this.element.offset(),i={x:t.pageX,y:t.pageY},s=this._normValueFromMouse(i),n=this._valueMax()-this._valueMin()+1,this.handles.each(function(t){var i=Math.abs(s-u.values(t));(n>i||n===i&&(t===u._lastChangedValue||u.values(t)===d.min))&&(n=i,a=e(this),o=t)}),r=this._start(t,o),r===!1?!1:(this._mouseSliding=!0,this._handleIndex=o,a.addClass("ui-state-active").focus(),h=a.offset(),l=!e(t.target).parents().addBack().is(".ui-slider-handle"),this._clickOffset=l?{left:0,top:0}:{left:t.pageX-h.left-a.width()/2,top:t.pageY-h.top-a.height()/2-(parseInt(a.css("borderTopWidth"),10)||0)-(parseInt(a.css("borderBottomWidth"),10)||0)+(parseInt(a.css("marginTop"),10)||0)},this.handles.hasClass("ui-state-hover")||this._slide(t,o,s),this._animateOff=!0,!0))},_mouseStart:function(){return!0},_mouseDrag:function(e){var t={x:e.pageX,y:e.pageY},i=this._normValueFromMouse(t);return this._slide(e,this._handleIndex,i),!1},_mouseStop:function(e){return this.handles.removeClass("ui-state-active"),this._mouseSliding=!1,this._stop(e,this._handleIndex),this._change(e,this._handleIndex),this._handleIndex=null,this._clickOffset=null,this._animateOff=!1,!1},_detectOrientation:function(){this.orientation="vertical"===this.options.orientation?"vertical":"horizontal"},_normValueFromMouse:function(e){var t,i,s,n,a;return"horizontal"===this.orientation?(t=this.elementSize.width,i=e.x-this.elementOffset.left-(this._clickOffset?this._clickOffset.left:0)):(t=this.elementSize.height,i=e.y-this.elementOffset.top-(this._clickOffset?this._clickOffset.top:0)),s=i/t,s>1&&(s=1),0>s&&(s=0),"vertical"===this.orientation&&(s=1-s),n=this._valueMax()-this._valueMin(),a=this._valueMin()+s*n,this._trimAlignValue(a)},_start:function(e,t){var i={handle:this.handles[t],value:this.value()};return this.options.values&&this.options.values.length&&(i.value=this.values(t),i.values=this.values()),this._trigger("start",e,i)},_slide:function(e,t,i){var s,n,a;this.options.values&&this.options.values.length?(s=this.values(t?0:1),2===this.options.values.length&&this.options.range===!0&&(0===t&&i>s||1===t&&s>i)&&(i=s),i!==this.values(t)&&(n=this.values(),n[t]=i,a=this._trigger("slide",e,{handle:this.handles[t],value:i,values:n}),s=this.values(t?0:1),a!==!1&&this.values(t,i))):i!==this.value()&&(a=this._trigger("slide",e,{handle:this.handles[t],value:i}),a!==!1&&this.value(i))},_stop:function(e,t){var i={handle:this.handles[t],value:this.value()};this.options.values&&this.options.values.length&&(i.value=this.values(t),i.values=this.values()),this._trigger("stop",e,i)},_change:function(e,t){if(!this._keySliding&&!this._mouseSliding){var i={handle:this.handles[t],value:this.value()};this.options.values&&this.options.values.length&&(i.value=this.values(t),i.values=this.values()),this._lastChangedValue=t,this._trigger("change",e,i)}},value:function(e){return arguments.length?(this.options.value=this._trimAlignValue(e),this._refreshValue(),this._change(null,0),void 0):this._value()},values:function(t,i){var s,n,a;if(arguments.length>1)return this.options.values[t]=this._trimAlignValue(i),this._refreshValue(),this._change(null,t),void 0;if(!arguments.length)return this._values();if(!e.isArray(arguments[0]))return this.options.values&&this.options.values.length?this._values(t):this.value();for(s=this.options.values,n=arguments[0],a=0;s.length>a;a+=1)s[a]=this._trimAlignValue(n[a]),this._change(null,a);this._refreshValue()},_setOption:function(t,i){var s,n=0;switch("range"===t&&this.options.range===!0&&("min"===i?(this.options.value=this._values(0),this.options.values=null):"max"===i&&(this.options.value=this._values(this.options.values.length-1),this.options.values=null)),e.isArray(this.options.values)&&(n=this.options.values.length),"disabled"===t&&this.element.toggleClass("ui-state-disabled",!!i),this._super(t,i),t){case"orientation":this._detectOrientation(),this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-"+this.orientation),this._refreshValue(),this.handles.css("horizontal"===i?"bottom":"left","");break;case"value":this._animateOff=!0,this._refreshValue(),this._change(null,0),this._animateOff=!1;break;case"values":for(this._animateOff=!0,this._refreshValue(),s=0;n>s;s+=1)this._change(null,s);this._animateOff=!1;break;case"step":case"min":case"max":this._animateOff=!0,this._calculateNewMax(),this._refreshValue(),this._animateOff=!1;break;case"range":this._animateOff=!0,this._refresh(),this._animateOff=!1}},_value:function(){var e=this.options.value;return e=this._trimAlignValue(e)},_values:function(e){var t,i,s;if(arguments.length)return t=this.options.values[e],t=this._trimAlignValue(t);if(this.options.values&&this.options.values.length){for(i=this.options.values.slice(),s=0;i.length>s;s+=1)i[s]=this._trimAlignValue(i[s]);return i}return[]},_trimAlignValue:function(e){if(this._valueMin()>=e)return this._valueMin();if(e>=this._valueMax())return this._valueMax();var t=this.options.step>0?this.options.step:1,i=(e-this._valueMin())%t,s=e-i;return 2*Math.abs(i)>=t&&(s+=i>0?t:-t),parseFloat(s.toFixed(5))},_calculateNewMax:function(){var e=this.options.max,t=this._valueMin(),i=this.options.step,s=Math.floor(+(e-t).toFixed(this._precision())/i)*i;e=s+t,this.max=parseFloat(e.toFixed(this._precision()))},_precision:function(){var e=this._precisionOf(this.options.step);return null!==this.options.min&&(e=Math.max(e,this._precisionOf(this.options.min))),e},_precisionOf:function(e){var t=""+e,i=t.indexOf(".");return-1===i?0:t.length-i-1},_valueMin:function(){return this.options.min},_valueMax:function(){return this.max},_refreshValue:function(){var t,i,s,n,a,o=this.options.range,r=this.options,h=this,l=this._animateOff?!1:r.animate,u={};this.options.values&&this.options.values.length?this.handles.each(function(s){i=100*((h.values(s)-h._valueMin())/(h._valueMax()-h._valueMin())),u["horizontal"===h.orientation?"left":"bottom"]=i+"%",e(this).stop(1,1)[l?"animate":"css"](u,r.animate),h.options.range===!0&&("horizontal"===h.orientation?(0===s&&h.range.stop(1,1)[l?"animate":"css"]({left:i+"%"},r.animate),1===s&&h.range[l?"animate":"css"]({width:i-t+"%"},{queue:!1,duration:r.animate})):(0===s&&h.range.stop(1,1)[l?"animate":"css"]({bottom:i+"%"},r.animate),1===s&&h.range[l?"animate":"css"]({height:i-t+"%"},{queue:!1,duration:r.animate}))),t=i}):(s=this.value(),n=this._valueMin(),a=this._valueMax(),i=a!==n?100*((s-n)/(a-n)):0,u["horizontal"===this.orientation?"left":"bottom"]=i+"%",this.handle.stop(1,1)[l?"animate":"css"](u,r.animate),"min"===o&&"horizontal"===this.orientation&&this.range.stop(1,1)[l?"animate":"css"]({width:i+"%"},r.animate),"max"===o&&"horizontal"===this.orientation&&this.range[l?"animate":"css"]({width:100-i+"%"},{queue:!1,duration:r.animate}),"min"===o&&"vertical"===this.orientation&&this.range.stop(1,1)[l?"animate":"css"]({height:i+"%"},r.animate),"max"===o&&"vertical"===this.orientation&&this.range[l?"animate":"css"]({height:100-i+"%"},{queue:!1,duration:r.animate}))},_handleEvents:{keydown:function(t){var i,s,n,a,o=e(t.target).data("ui-slider-handle-index");switch(t.keyCode){case e.ui.keyCode.HOME:case e.ui.keyCode.END:case e.ui.keyCode.PAGE_UP:case e.ui.keyCode.PAGE_DOWN:case e.ui.keyCode.UP:case e.ui.keyCode.RIGHT:case e.ui.keyCode.DOWN:case e.ui.keyCode.LEFT:if(t.preventDefault(),!this._keySliding&&(this._keySliding=!0,e(t.target).addClass("ui-state-active"),i=this._start(t,o),i===!1))return}switch(a=this.options.step,s=n=this.options.values&&this.options.values.length?this.values(o):this.value(),t.keyCode){case e.ui.keyCode.HOME:n=this._valueMin();break;case e.ui.keyCode.END:n=this._valueMax();break;case e.ui.keyCode.PAGE_UP:n=this._trimAlignValue(s+(this._valueMax()-this._valueMin())/this.numPages);break;case e.ui.keyCode.PAGE_DOWN:n=this._trimAlignValue(s-(this._valueMax()-this._valueMin())/this.numPages);break;case e.ui.keyCode.UP:case e.ui.keyCode.RIGHT:if(s===this._valueMax())return;n=this._trimAlignValue(s+a);break;case e.ui.keyCode.DOWN:case e.ui.keyCode.LEFT:if(s===this._valueMin())return;n=this._trimAlignValue(s-a)}this._slide(t,o,n)},keyup:function(t){var i=e(t.target).data("ui-slider-handle-index");this._keySliding&&(this._keySliding=!1,this._stop(t,i),this._change(t,i),e(t.target).removeClass("ui-state-active"))}}}),e.widget("ui.spinner",{version:"1.11.4",defaultElement:"<input>",widgetEventPrefix:"spin",options:{culture:null,icons:{down:"ui-icon-triangle-1-s",up:"ui-icon-triangle-1-n"},incremental:!0,max:null,min:null,numberFormat:null,page:10,step:1,change:null,spin:null,start:null,stop:null},_create:function(){this._setOption("max",this.options.max),this._setOption("min",this.options.min),this._setOption("step",this.options.step),""!==this.value()&&this._value(this.element.val(),!0),this._draw(),this._on(this._events),this._refresh(),this._on(this.window,{beforeunload:function(){this.element.removeAttr("autocomplete")}})},_getCreateOptions:function(){var t={},i=this.element;return e.each(["min","max","step"],function(e,s){var n=i.attr(s);void 0!==n&&n.length&&(t[s]=n)}),t},_events:{keydown:function(e){this._start(e)&&this._keydown(e)&&e.preventDefault()},keyup:"_stop",focus:function(){this.previous=this.element.val()},blur:function(e){return this.cancelBlur?(delete this.cancelBlur,void 0):(this._stop(),this._refresh(),this.previous!==this.element.val()&&this._trigger("change",e),void 0)},mousewheel:function(e,t){if(t){if(!this.spinning&&!this._start(e))return!1;this._spin((t>0?1:-1)*this.options.step,e),clearTimeout(this.mousewheelTimer),this.mousewheelTimer=this._delay(function(){this.spinning&&this._stop(e)},100),e.preventDefault()}},"mousedown .ui-spinner-button":function(t){function i(){var e=this.element[0]===this.document[0].activeElement;e||(this.element.focus(),this.previous=s,this._delay(function(){this.previous=s}))}var s;s=this.element[0]===this.document[0].activeElement?this.previous:this.element.val(),t.preventDefault(),i.call(this),this.cancelBlur=!0,this._delay(function(){delete this.cancelBlur,i.call(this)}),this._start(t)!==!1&&this._repeat(null,e(t.currentTarget).hasClass("ui-spinner-up")?1:-1,t)},"mouseup .ui-spinner-button":"_stop","mouseenter .ui-spinner-button":function(t){return e(t.currentTarget).hasClass("ui-state-active")?this._start(t)===!1?!1:(this._repeat(null,e(t.currentTarget).hasClass("ui-spinner-up")?1:-1,t),void 0):void 0},"mouseleave .ui-spinner-button":"_stop"},_draw:function(){var e=this.uiSpinner=this.element.addClass("ui-spinner-input").attr("autocomplete","off").wrap(this._uiSpinnerHtml()).parent().append(this._buttonHtml());this.element.attr("role","spinbutton"),this.buttons=e.find(".ui-spinner-button").attr("tabIndex",-1).button().removeClass("ui-corner-all"),this.buttons.height()>Math.ceil(.5*e.height())&&e.height()>0&&e.height(e.height()),this.options.disabled&&this.disable()
},_keydown:function(t){var i=this.options,s=e.ui.keyCode;switch(t.keyCode){case s.UP:return this._repeat(null,1,t),!0;case s.DOWN:return this._repeat(null,-1,t),!0;case s.PAGE_UP:return this._repeat(null,i.page,t),!0;case s.PAGE_DOWN:return this._repeat(null,-i.page,t),!0}return!1},_uiSpinnerHtml:function(){return"<span class='ui-spinner ui-widget ui-widget-content ui-corner-all'></span>"},_buttonHtml:function(){return"<a class='ui-spinner-button ui-spinner-up ui-corner-tr'><span class='ui-icon "+this.options.icons.up+"'>&#9650;</span>"+"</a>"+"<a class='ui-spinner-button ui-spinner-down ui-corner-br'>"+"<span class='ui-icon "+this.options.icons.down+"'>&#9660;</span>"+"</a>"},_start:function(e){return this.spinning||this._trigger("start",e)!==!1?(this.counter||(this.counter=1),this.spinning=!0,!0):!1},_repeat:function(e,t,i){e=e||500,clearTimeout(this.timer),this.timer=this._delay(function(){this._repeat(40,t,i)},e),this._spin(t*this.options.step,i)},_spin:function(e,t){var i=this.value()||0;this.counter||(this.counter=1),i=this._adjustValue(i+e*this._increment(this.counter)),this.spinning&&this._trigger("spin",t,{value:i})===!1||(this._value(i),this.counter++)},_increment:function(t){var i=this.options.incremental;return i?e.isFunction(i)?i(t):Math.floor(t*t*t/5e4-t*t/500+17*t/200+1):1},_precision:function(){var e=this._precisionOf(this.options.step);return null!==this.options.min&&(e=Math.max(e,this._precisionOf(this.options.min))),e},_precisionOf:function(e){var t=""+e,i=t.indexOf(".");return-1===i?0:t.length-i-1},_adjustValue:function(e){var t,i,s=this.options;return t=null!==s.min?s.min:0,i=e-t,i=Math.round(i/s.step)*s.step,e=t+i,e=parseFloat(e.toFixed(this._precision())),null!==s.max&&e>s.max?s.max:null!==s.min&&s.min>e?s.min:e},_stop:function(e){this.spinning&&(clearTimeout(this.timer),clearTimeout(this.mousewheelTimer),this.counter=0,this.spinning=!1,this._trigger("stop",e))},_setOption:function(e,t){if("culture"===e||"numberFormat"===e){var i=this._parse(this.element.val());return this.options[e]=t,this.element.val(this._format(i)),void 0}("max"===e||"min"===e||"step"===e)&&"string"==typeof t&&(t=this._parse(t)),"icons"===e&&(this.buttons.first().find(".ui-icon").removeClass(this.options.icons.up).addClass(t.up),this.buttons.last().find(".ui-icon").removeClass(this.options.icons.down).addClass(t.down)),this._super(e,t),"disabled"===e&&(this.widget().toggleClass("ui-state-disabled",!!t),this.element.prop("disabled",!!t),this.buttons.button(t?"disable":"enable"))},_setOptions:h(function(e){this._super(e)}),_parse:function(e){return"string"==typeof e&&""!==e&&(e=window.Globalize&&this.options.numberFormat?Globalize.parseFloat(e,10,this.options.culture):+e),""===e||isNaN(e)?null:e},_format:function(e){return""===e?"":window.Globalize&&this.options.numberFormat?Globalize.format(e,this.options.numberFormat,this.options.culture):e},_refresh:function(){this.element.attr({"aria-valuemin":this.options.min,"aria-valuemax":this.options.max,"aria-valuenow":this._parse(this.element.val())})},isValid:function(){var e=this.value();return null===e?!1:e===this._adjustValue(e)},_value:function(e,t){var i;""!==e&&(i=this._parse(e),null!==i&&(t||(i=this._adjustValue(i)),e=this._format(i))),this.element.val(e),this._refresh()},_destroy:function(){this.element.removeClass("ui-spinner-input").prop("disabled",!1).removeAttr("autocomplete").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow"),this.uiSpinner.replaceWith(this.element)},stepUp:h(function(e){this._stepUp(e)}),_stepUp:function(e){this._start()&&(this._spin((e||1)*this.options.step),this._stop())},stepDown:h(function(e){this._stepDown(e)}),_stepDown:function(e){this._start()&&(this._spin((e||1)*-this.options.step),this._stop())},pageUp:h(function(e){this._stepUp((e||1)*this.options.page)}),pageDown:h(function(e){this._stepDown((e||1)*this.options.page)}),value:function(e){return arguments.length?(h(this._value).call(this,e),void 0):this._parse(this.element.val())},widget:function(){return this.uiSpinner}}),e.widget("ui.tabs",{version:"1.11.4",delay:300,options:{active:null,collapsible:!1,event:"click",heightStyle:"content",hide:null,show:null,activate:null,beforeActivate:null,beforeLoad:null,load:null},_isLocal:function(){var e=/#.*$/;return function(t){var i,s;t=t.cloneNode(!1),i=t.href.replace(e,""),s=location.href.replace(e,"");try{i=decodeURIComponent(i)}catch(n){}try{s=decodeURIComponent(s)}catch(n){}return t.hash.length>1&&i===s}}(),_create:function(){var t=this,i=this.options;this.running=!1,this.element.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all").toggleClass("ui-tabs-collapsible",i.collapsible),this._processTabs(),i.active=this._initialActive(),e.isArray(i.disabled)&&(i.disabled=e.unique(i.disabled.concat(e.map(this.tabs.filter(".ui-state-disabled"),function(e){return t.tabs.index(e)}))).sort()),this.active=this.options.active!==!1&&this.anchors.length?this._findActive(i.active):e(),this._refresh(),this.active.length&&this.load(i.active)},_initialActive:function(){var t=this.options.active,i=this.options.collapsible,s=location.hash.substring(1);return null===t&&(s&&this.tabs.each(function(i,n){return e(n).attr("aria-controls")===s?(t=i,!1):void 0}),null===t&&(t=this.tabs.index(this.tabs.filter(".ui-tabs-active"))),(null===t||-1===t)&&(t=this.tabs.length?0:!1)),t!==!1&&(t=this.tabs.index(this.tabs.eq(t)),-1===t&&(t=i?!1:0)),!i&&t===!1&&this.anchors.length&&(t=0),t},_getCreateEventData:function(){return{tab:this.active,panel:this.active.length?this._getPanelForTab(this.active):e()}},_tabKeydown:function(t){var i=e(this.document[0].activeElement).closest("li"),s=this.tabs.index(i),n=!0;if(!this._handlePageNav(t)){switch(t.keyCode){case e.ui.keyCode.RIGHT:case e.ui.keyCode.DOWN:s++;break;case e.ui.keyCode.UP:case e.ui.keyCode.LEFT:n=!1,s--;break;case e.ui.keyCode.END:s=this.anchors.length-1;break;case e.ui.keyCode.HOME:s=0;break;case e.ui.keyCode.SPACE:return t.preventDefault(),clearTimeout(this.activating),this._activate(s),void 0;case e.ui.keyCode.ENTER:return t.preventDefault(),clearTimeout(this.activating),this._activate(s===this.options.active?!1:s),void 0;default:return}t.preventDefault(),clearTimeout(this.activating),s=this._focusNextTab(s,n),t.ctrlKey||t.metaKey||(i.attr("aria-selected","false"),this.tabs.eq(s).attr("aria-selected","true"),this.activating=this._delay(function(){this.option("active",s)},this.delay))}},_panelKeydown:function(t){this._handlePageNav(t)||t.ctrlKey&&t.keyCode===e.ui.keyCode.UP&&(t.preventDefault(),this.active.focus())},_handlePageNav:function(t){return t.altKey&&t.keyCode===e.ui.keyCode.PAGE_UP?(this._activate(this._focusNextTab(this.options.active-1,!1)),!0):t.altKey&&t.keyCode===e.ui.keyCode.PAGE_DOWN?(this._activate(this._focusNextTab(this.options.active+1,!0)),!0):void 0},_findNextTab:function(t,i){function s(){return t>n&&(t=0),0>t&&(t=n),t}for(var n=this.tabs.length-1;-1!==e.inArray(s(),this.options.disabled);)t=i?t+1:t-1;return t},_focusNextTab:function(e,t){return e=this._findNextTab(e,t),this.tabs.eq(e).focus(),e},_setOption:function(e,t){return"active"===e?(this._activate(t),void 0):"disabled"===e?(this._setupDisabled(t),void 0):(this._super(e,t),"collapsible"===e&&(this.element.toggleClass("ui-tabs-collapsible",t),t||this.options.active!==!1||this._activate(0)),"event"===e&&this._setupEvents(t),"heightStyle"===e&&this._setupHeightStyle(t),void 0)},_sanitizeSelector:function(e){return e?e.replace(/[!"$%&'()*+,.\/:;<=>?@\[\]\^`{|}~]/g,"\\$&"):""},refresh:function(){var t=this.options,i=this.tablist.children(":has(a[href])");t.disabled=e.map(i.filter(".ui-state-disabled"),function(e){return i.index(e)}),this._processTabs(),t.active!==!1&&this.anchors.length?this.active.length&&!e.contains(this.tablist[0],this.active[0])?this.tabs.length===t.disabled.length?(t.active=!1,this.active=e()):this._activate(this._findNextTab(Math.max(0,t.active-1),!1)):t.active=this.tabs.index(this.active):(t.active=!1,this.active=e()),this._refresh()},_refresh:function(){this._setupDisabled(this.options.disabled),this._setupEvents(this.options.event),this._setupHeightStyle(this.options.heightStyle),this.tabs.not(this.active).attr({"aria-selected":"false","aria-expanded":"false",tabIndex:-1}),this.panels.not(this._getPanelForTab(this.active)).hide().attr({"aria-hidden":"true"}),this.active.length?(this.active.addClass("ui-tabs-active ui-state-active").attr({"aria-selected":"true","aria-expanded":"true",tabIndex:0}),this._getPanelForTab(this.active).show().attr({"aria-hidden":"false"})):this.tabs.eq(0).attr("tabIndex",0)},_processTabs:function(){var t=this,i=this.tabs,s=this.anchors,n=this.panels;this.tablist=this._getList().addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all").attr("role","tablist").delegate("> li","mousedown"+this.eventNamespace,function(t){e(this).is(".ui-state-disabled")&&t.preventDefault()}).delegate(".ui-tabs-anchor","focus"+this.eventNamespace,function(){e(this).closest("li").is(".ui-state-disabled")&&this.blur()}),this.tabs=this.tablist.find("> li:has(a[href])").addClass("ui-state-default ui-corner-top").attr({role:"tab",tabIndex:-1}),this.anchors=this.tabs.map(function(){return e("a",this)[0]}).addClass("ui-tabs-anchor").attr({role:"presentation",tabIndex:-1}),this.panels=e(),this.anchors.each(function(i,s){var n,a,o,r=e(s).uniqueId().attr("id"),h=e(s).closest("li"),l=h.attr("aria-controls");t._isLocal(s)?(n=s.hash,o=n.substring(1),a=t.element.find(t._sanitizeSelector(n))):(o=h.attr("aria-controls")||e({}).uniqueId()[0].id,n="#"+o,a=t.element.find(n),a.length||(a=t._createPanel(o),a.insertAfter(t.panels[i-1]||t.tablist)),a.attr("aria-live","polite")),a.length&&(t.panels=t.panels.add(a)),l&&h.data("ui-tabs-aria-controls",l),h.attr({"aria-controls":o,"aria-labelledby":r}),a.attr("aria-labelledby",r)}),this.panels.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").attr("role","tabpanel"),i&&(this._off(i.not(this.tabs)),this._off(s.not(this.anchors)),this._off(n.not(this.panels)))},_getList:function(){return this.tablist||this.element.find("ol,ul").eq(0)},_createPanel:function(t){return e("<div>").attr("id",t).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").data("ui-tabs-destroy",!0)},_setupDisabled:function(t){e.isArray(t)&&(t.length?t.length===this.anchors.length&&(t=!0):t=!1);for(var i,s=0;i=this.tabs[s];s++)t===!0||-1!==e.inArray(s,t)?e(i).addClass("ui-state-disabled").attr("aria-disabled","true"):e(i).removeClass("ui-state-disabled").removeAttr("aria-disabled");this.options.disabled=t},_setupEvents:function(t){var i={};t&&e.each(t.split(" "),function(e,t){i[t]="_eventHandler"}),this._off(this.anchors.add(this.tabs).add(this.panels)),this._on(!0,this.anchors,{click:function(e){e.preventDefault()}}),this._on(this.anchors,i),this._on(this.tabs,{keydown:"_tabKeydown"}),this._on(this.panels,{keydown:"_panelKeydown"}),this._focusable(this.tabs),this._hoverable(this.tabs)},_setupHeightStyle:function(t){var i,s=this.element.parent();"fill"===t?(i=s.height(),i-=this.element.outerHeight()-this.element.height(),this.element.siblings(":visible").each(function(){var t=e(this),s=t.css("position");"absolute"!==s&&"fixed"!==s&&(i-=t.outerHeight(!0))}),this.element.children().not(this.panels).each(function(){i-=e(this).outerHeight(!0)}),this.panels.each(function(){e(this).height(Math.max(0,i-e(this).innerHeight()+e(this).height()))}).css("overflow","auto")):"auto"===t&&(i=0,this.panels.each(function(){i=Math.max(i,e(this).height("").height())}).height(i))},_eventHandler:function(t){var i=this.options,s=this.active,n=e(t.currentTarget),a=n.closest("li"),o=a[0]===s[0],r=o&&i.collapsible,h=r?e():this._getPanelForTab(a),l=s.length?this._getPanelForTab(s):e(),u={oldTab:s,oldPanel:l,newTab:r?e():a,newPanel:h};t.preventDefault(),a.hasClass("ui-state-disabled")||a.hasClass("ui-tabs-loading")||this.running||o&&!i.collapsible||this._trigger("beforeActivate",t,u)===!1||(i.active=r?!1:this.tabs.index(a),this.active=o?e():a,this.xhr&&this.xhr.abort(),l.length||h.length||e.error("jQuery UI Tabs: Mismatching fragment identifier."),h.length&&this.load(this.tabs.index(a),t),this._toggle(t,u))},_toggle:function(t,i){function s(){a.running=!1,a._trigger("activate",t,i)}function n(){i.newTab.closest("li").addClass("ui-tabs-active ui-state-active"),o.length&&a.options.show?a._show(o,a.options.show,s):(o.show(),s())}var a=this,o=i.newPanel,r=i.oldPanel;this.running=!0,r.length&&this.options.hide?this._hide(r,this.options.hide,function(){i.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active"),n()}):(i.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active"),r.hide(),n()),r.attr("aria-hidden","true"),i.oldTab.attr({"aria-selected":"false","aria-expanded":"false"}),o.length&&r.length?i.oldTab.attr("tabIndex",-1):o.length&&this.tabs.filter(function(){return 0===e(this).attr("tabIndex")}).attr("tabIndex",-1),o.attr("aria-hidden","false"),i.newTab.attr({"aria-selected":"true","aria-expanded":"true",tabIndex:0})},_activate:function(t){var i,s=this._findActive(t);s[0]!==this.active[0]&&(s.length||(s=this.active),i=s.find(".ui-tabs-anchor")[0],this._eventHandler({target:i,currentTarget:i,preventDefault:e.noop}))},_findActive:function(t){return t===!1?e():this.tabs.eq(t)},_getIndex:function(e){return"string"==typeof e&&(e=this.anchors.index(this.anchors.filter("[href$='"+e+"']"))),e},_destroy:function(){this.xhr&&this.xhr.abort(),this.element.removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible"),this.tablist.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all").removeAttr("role"),this.anchors.removeClass("ui-tabs-anchor").removeAttr("role").removeAttr("tabIndex").removeUniqueId(),this.tablist.unbind(this.eventNamespace),this.tabs.add(this.panels).each(function(){e.data(this,"ui-tabs-destroy")?e(this).remove():e(this).removeClass("ui-state-default ui-state-active ui-state-disabled ui-corner-top ui-corner-bottom ui-widget-content ui-tabs-active ui-tabs-panel").removeAttr("tabIndex").removeAttr("aria-live").removeAttr("aria-busy").removeAttr("aria-selected").removeAttr("aria-labelledby").removeAttr("aria-hidden").removeAttr("aria-expanded").removeAttr("role")}),this.tabs.each(function(){var t=e(this),i=t.data("ui-tabs-aria-controls");i?t.attr("aria-controls",i).removeData("ui-tabs-aria-controls"):t.removeAttr("aria-controls")}),this.panels.show(),"content"!==this.options.heightStyle&&this.panels.css("height","")},enable:function(t){var i=this.options.disabled;i!==!1&&(void 0===t?i=!1:(t=this._getIndex(t),i=e.isArray(i)?e.map(i,function(e){return e!==t?e:null}):e.map(this.tabs,function(e,i){return i!==t?i:null})),this._setupDisabled(i))},disable:function(t){var i=this.options.disabled;if(i!==!0){if(void 0===t)i=!0;else{if(t=this._getIndex(t),-1!==e.inArray(t,i))return;i=e.isArray(i)?e.merge([t],i).sort():[t]}this._setupDisabled(i)}},load:function(t,i){t=this._getIndex(t);var s=this,n=this.tabs.eq(t),a=n.find(".ui-tabs-anchor"),o=this._getPanelForTab(n),r={tab:n,panel:o},h=function(e,t){"abort"===t&&s.panels.stop(!1,!0),n.removeClass("ui-tabs-loading"),o.removeAttr("aria-busy"),e===s.xhr&&delete s.xhr};this._isLocal(a[0])||(this.xhr=e.ajax(this._ajaxSettings(a,i,r)),this.xhr&&"canceled"!==this.xhr.statusText&&(n.addClass("ui-tabs-loading"),o.attr("aria-busy","true"),this.xhr.done(function(e,t,n){setTimeout(function(){o.html(e),s._trigger("load",i,r),h(n,t)},1)}).fail(function(e,t){setTimeout(function(){h(e,t)},1)})))},_ajaxSettings:function(t,i,s){var n=this;return{url:t.attr("href"),beforeSend:function(t,a){return n._trigger("beforeLoad",i,e.extend({jqXHR:t,ajaxSettings:a},s))}}},_getPanelForTab:function(t){var i=e(t).attr("aria-controls");return this.element.find(this._sanitizeSelector("#"+i))}}),e.widget("ui.tooltip",{version:"1.11.4",options:{content:function(){var t=e(this).attr("title")||"";return e("<a>").text(t).html()},hide:!0,items:"[title]:not([disabled])",position:{my:"left top+15",at:"left bottom",collision:"flipfit flip"},show:!0,tooltipClass:null,track:!1,close:null,open:null},_addDescribedBy:function(t,i){var s=(t.attr("aria-describedby")||"").split(/\s+/);s.push(i),t.data("ui-tooltip-id",i).attr("aria-describedby",e.trim(s.join(" ")))},_removeDescribedBy:function(t){var i=t.data("ui-tooltip-id"),s=(t.attr("aria-describedby")||"").split(/\s+/),n=e.inArray(i,s);-1!==n&&s.splice(n,1),t.removeData("ui-tooltip-id"),s=e.trim(s.join(" ")),s?t.attr("aria-describedby",s):t.removeAttr("aria-describedby")},_create:function(){this._on({mouseover:"open",focusin:"open"}),this.tooltips={},this.parents={},this.options.disabled&&this._disable(),this.liveRegion=e("<div>").attr({role:"log","aria-live":"assertive","aria-relevant":"additions"}).addClass("ui-helper-hidden-accessible").appendTo(this.document[0].body)},_setOption:function(t,i){var s=this;return"disabled"===t?(this[i?"_disable":"_enable"](),this.options[t]=i,void 0):(this._super(t,i),"content"===t&&e.each(this.tooltips,function(e,t){s._updateContent(t.element)}),void 0)},_disable:function(){var t=this;e.each(this.tooltips,function(i,s){var n=e.Event("blur");n.target=n.currentTarget=s.element[0],t.close(n,!0)}),this.element.find(this.options.items).addBack().each(function(){var t=e(this);t.is("[title]")&&t.data("ui-tooltip-title",t.attr("title")).removeAttr("title")})},_enable:function(){this.element.find(this.options.items).addBack().each(function(){var t=e(this);t.data("ui-tooltip-title")&&t.attr("title",t.data("ui-tooltip-title"))})},open:function(t){var i=this,s=e(t?t.target:this.element).closest(this.options.items);s.length&&!s.data("ui-tooltip-id")&&(s.attr("title")&&s.data("ui-tooltip-title",s.attr("title")),s.data("ui-tooltip-open",!0),t&&"mouseover"===t.type&&s.parents().each(function(){var t,s=e(this);s.data("ui-tooltip-open")&&(t=e.Event("blur"),t.target=t.currentTarget=this,i.close(t,!0)),s.attr("title")&&(s.uniqueId(),i.parents[this.id]={element:this,title:s.attr("title")},s.attr("title",""))}),this._registerCloseHandlers(t,s),this._updateContent(s,t))},_updateContent:function(e,t){var i,s=this.options.content,n=this,a=t?t.type:null;return"string"==typeof s?this._open(t,e,s):(i=s.call(e[0],function(i){n._delay(function(){e.data("ui-tooltip-open")&&(t&&(t.type=a),this._open(t,e,i))})}),i&&this._open(t,e,i),void 0)},_open:function(t,i,s){function n(e){l.of=e,o.is(":hidden")||o.position(l)}var a,o,r,h,l=e.extend({},this.options.position);if(s){if(a=this._find(i))return a.tooltip.find(".ui-tooltip-content").html(s),void 0;i.is("[title]")&&(t&&"mouseover"===t.type?i.attr("title",""):i.removeAttr("title")),a=this._tooltip(i),o=a.tooltip,this._addDescribedBy(i,o.attr("id")),o.find(".ui-tooltip-content").html(s),this.liveRegion.children().hide(),s.clone?(h=s.clone(),h.removeAttr("id").find("[id]").removeAttr("id")):h=s,e("<div>").html(h).appendTo(this.liveRegion),this.options.track&&t&&/^mouse/.test(t.type)?(this._on(this.document,{mousemove:n}),n(t)):o.position(e.extend({of:i},this.options.position)),o.hide(),this._show(o,this.options.show),this.options.show&&this.options.show.delay&&(r=this.delayedShow=setInterval(function(){o.is(":visible")&&(n(l.of),clearInterval(r))},e.fx.interval)),this._trigger("open",t,{tooltip:o})}},_registerCloseHandlers:function(t,i){var s={keyup:function(t){if(t.keyCode===e.ui.keyCode.ESCAPE){var s=e.Event(t);s.currentTarget=i[0],this.close(s,!0)}}};i[0]!==this.element[0]&&(s.remove=function(){this._removeTooltip(this._find(i).tooltip)}),t&&"mouseover"!==t.type||(s.mouseleave="close"),t&&"focusin"!==t.type||(s.focusout="close"),this._on(!0,i,s)},close:function(t){var i,s=this,n=e(t?t.currentTarget:this.element),a=this._find(n);return a?(i=a.tooltip,a.closing||(clearInterval(this.delayedShow),n.data("ui-tooltip-title")&&!n.attr("title")&&n.attr("title",n.data("ui-tooltip-title")),this._removeDescribedBy(n),a.hiding=!0,i.stop(!0),this._hide(i,this.options.hide,function(){s._removeTooltip(e(this))}),n.removeData("ui-tooltip-open"),this._off(n,"mouseleave focusout keyup"),n[0]!==this.element[0]&&this._off(n,"remove"),this._off(this.document,"mousemove"),t&&"mouseleave"===t.type&&e.each(this.parents,function(t,i){e(i.element).attr("title",i.title),delete s.parents[t]}),a.closing=!0,this._trigger("close",t,{tooltip:i}),a.hiding||(a.closing=!1)),void 0):(n.removeData("ui-tooltip-open"),void 0)},_tooltip:function(t){var i=e("<div>").attr("role","tooltip").addClass("ui-tooltip ui-widget ui-corner-all ui-widget-content "+(this.options.tooltipClass||"")),s=i.uniqueId().attr("id");return e("<div>").addClass("ui-tooltip-content").appendTo(i),i.appendTo(this.document[0].body),this.tooltips[s]={element:t,tooltip:i}},_find:function(e){var t=e.data("ui-tooltip-id");return t?this.tooltips[t]:null},_removeTooltip:function(e){e.remove(),delete this.tooltips[e.attr("id")]},_destroy:function(){var t=this;e.each(this.tooltips,function(i,s){var n=e.Event("blur"),a=s.element;n.target=n.currentTarget=a[0],t.close(n,!0),e("#"+i).remove(),a.data("ui-tooltip-title")&&(a.attr("title")||a.attr("title",a.data("ui-tooltip-title")),a.removeData("ui-tooltip-title"))}),this.liveRegion.remove()}});var y="ui-effects-",b=e;e.effects={effect:{}},function(e,t){function i(e,t,i){var s=d[t.type]||{};return null==e?i||!t.def?null:t.def:(e=s.floor?~~e:parseFloat(e),isNaN(e)?t.def:s.mod?(e+s.mod)%s.mod:0>e?0:e>s.max?s.max:e)}function s(i){var s=l(),n=s._rgba=[];return i=i.toLowerCase(),f(h,function(e,a){var o,r=a.re.exec(i),h=r&&a.parse(r),l=a.space||"rgba";return h?(o=s[l](h),s[u[l].cache]=o[u[l].cache],n=s._rgba=o._rgba,!1):t}),n.length?("0,0,0,0"===n.join()&&e.extend(n,a.transparent),s):a[i]}function n(e,t,i){return i=(i+1)%1,1>6*i?e+6*(t-e)*i:1>2*i?t:2>3*i?e+6*(t-e)*(2/3-i):e}var a,o="backgroundColor borderBottomColor borderLeftColor borderRightColor borderTopColor color columnRuleColor outlineColor textDecorationColor textEmphasisColor",r=/^([\-+])=\s*(\d+\.?\d*)/,h=[{re:/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,parse:function(e){return[e[1],e[2],e[3],e[4]]}},{re:/rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,parse:function(e){return[2.55*e[1],2.55*e[2],2.55*e[3],e[4]]}},{re:/#([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})/,parse:function(e){return[parseInt(e[1],16),parseInt(e[2],16),parseInt(e[3],16)]}},{re:/#([a-f0-9])([a-f0-9])([a-f0-9])/,parse:function(e){return[parseInt(e[1]+e[1],16),parseInt(e[2]+e[2],16),parseInt(e[3]+e[3],16)]}},{re:/hsla?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,space:"hsla",parse:function(e){return[e[1],e[2]/100,e[3]/100,e[4]]}}],l=e.Color=function(t,i,s,n){return new e.Color.fn.parse(t,i,s,n)},u={rgba:{props:{red:{idx:0,type:"byte"},green:{idx:1,type:"byte"},blue:{idx:2,type:"byte"}}},hsla:{props:{hue:{idx:0,type:"degrees"},saturation:{idx:1,type:"percent"},lightness:{idx:2,type:"percent"}}}},d={"byte":{floor:!0,max:255},percent:{max:1},degrees:{mod:360,floor:!0}},c=l.support={},p=e("<p>")[0],f=e.each;p.style.cssText="background-color:rgba(1,1,1,.5)",c.rgba=p.style.backgroundColor.indexOf("rgba")>-1,f(u,function(e,t){t.cache="_"+e,t.props.alpha={idx:3,type:"percent",def:1}}),l.fn=e.extend(l.prototype,{parse:function(n,o,r,h){if(n===t)return this._rgba=[null,null,null,null],this;(n.jquery||n.nodeType)&&(n=e(n).css(o),o=t);var d=this,c=e.type(n),p=this._rgba=[];return o!==t&&(n=[n,o,r,h],c="array"),"string"===c?this.parse(s(n)||a._default):"array"===c?(f(u.rgba.props,function(e,t){p[t.idx]=i(n[t.idx],t)}),this):"object"===c?(n instanceof l?f(u,function(e,t){n[t.cache]&&(d[t.cache]=n[t.cache].slice())}):f(u,function(t,s){var a=s.cache;f(s.props,function(e,t){if(!d[a]&&s.to){if("alpha"===e||null==n[e])return;d[a]=s.to(d._rgba)}d[a][t.idx]=i(n[e],t,!0)}),d[a]&&0>e.inArray(null,d[a].slice(0,3))&&(d[a][3]=1,s.from&&(d._rgba=s.from(d[a])))}),this):t},is:function(e){var i=l(e),s=!0,n=this;return f(u,function(e,a){var o,r=i[a.cache];return r&&(o=n[a.cache]||a.to&&a.to(n._rgba)||[],f(a.props,function(e,i){return null!=r[i.idx]?s=r[i.idx]===o[i.idx]:t})),s}),s},_space:function(){var e=[],t=this;return f(u,function(i,s){t[s.cache]&&e.push(i)}),e.pop()},transition:function(e,t){var s=l(e),n=s._space(),a=u[n],o=0===this.alpha()?l("transparent"):this,r=o[a.cache]||a.to(o._rgba),h=r.slice();return s=s[a.cache],f(a.props,function(e,n){var a=n.idx,o=r[a],l=s[a],u=d[n.type]||{};null!==l&&(null===o?h[a]=l:(u.mod&&(l-o>u.mod/2?o+=u.mod:o-l>u.mod/2&&(o-=u.mod)),h[a]=i((l-o)*t+o,n)))}),this[n](h)},blend:function(t){if(1===this._rgba[3])return this;var i=this._rgba.slice(),s=i.pop(),n=l(t)._rgba;return l(e.map(i,function(e,t){return(1-s)*n[t]+s*e}))},toRgbaString:function(){var t="rgba(",i=e.map(this._rgba,function(e,t){return null==e?t>2?1:0:e});return 1===i[3]&&(i.pop(),t="rgb("),t+i.join()+")"},toHslaString:function(){var t="hsla(",i=e.map(this.hsla(),function(e,t){return null==e&&(e=t>2?1:0),t&&3>t&&(e=Math.round(100*e)+"%"),e});return 1===i[3]&&(i.pop(),t="hsl("),t+i.join()+")"},toHexString:function(t){var i=this._rgba.slice(),s=i.pop();return t&&i.push(~~(255*s)),"#"+e.map(i,function(e){return e=(e||0).toString(16),1===e.length?"0"+e:e}).join("")},toString:function(){return 0===this._rgba[3]?"transparent":this.toRgbaString()}}),l.fn.parse.prototype=l.fn,u.hsla.to=function(e){if(null==e[0]||null==e[1]||null==e[2])return[null,null,null,e[3]];var t,i,s=e[0]/255,n=e[1]/255,a=e[2]/255,o=e[3],r=Math.max(s,n,a),h=Math.min(s,n,a),l=r-h,u=r+h,d=.5*u;return t=h===r?0:s===r?60*(n-a)/l+360:n===r?60*(a-s)/l+120:60*(s-n)/l+240,i=0===l?0:.5>=d?l/u:l/(2-u),[Math.round(t)%360,i,d,null==o?1:o]},u.hsla.from=function(e){if(null==e[0]||null==e[1]||null==e[2])return[null,null,null,e[3]];var t=e[0]/360,i=e[1],s=e[2],a=e[3],o=.5>=s?s*(1+i):s+i-s*i,r=2*s-o;return[Math.round(255*n(r,o,t+1/3)),Math.round(255*n(r,o,t)),Math.round(255*n(r,o,t-1/3)),a]},f(u,function(s,n){var a=n.props,o=n.cache,h=n.to,u=n.from;l.fn[s]=function(s){if(h&&!this[o]&&(this[o]=h(this._rgba)),s===t)return this[o].slice();var n,r=e.type(s),d="array"===r||"object"===r?s:arguments,c=this[o].slice();return f(a,function(e,t){var s=d["object"===r?e:t.idx];null==s&&(s=c[t.idx]),c[t.idx]=i(s,t)}),u?(n=l(u(c)),n[o]=c,n):l(c)},f(a,function(t,i){l.fn[t]||(l.fn[t]=function(n){var a,o=e.type(n),h="alpha"===t?this._hsla?"hsla":"rgba":s,l=this[h](),u=l[i.idx];return"undefined"===o?u:("function"===o&&(n=n.call(this,u),o=e.type(n)),null==n&&i.empty?this:("string"===o&&(a=r.exec(n),a&&(n=u+parseFloat(a[2])*("+"===a[1]?1:-1))),l[i.idx]=n,this[h](l)))})})}),l.hook=function(t){var i=t.split(" ");f(i,function(t,i){e.cssHooks[i]={set:function(t,n){var a,o,r="";if("transparent"!==n&&("string"!==e.type(n)||(a=s(n)))){if(n=l(a||n),!c.rgba&&1!==n._rgba[3]){for(o="backgroundColor"===i?t.parentNode:t;(""===r||"transparent"===r)&&o&&o.style;)try{r=e.css(o,"backgroundColor"),o=o.parentNode}catch(h){}n=n.blend(r&&"transparent"!==r?r:"_default")}n=n.toRgbaString()}try{t.style[i]=n}catch(h){}}},e.fx.step[i]=function(t){t.colorInit||(t.start=l(t.elem,i),t.end=l(t.end),t.colorInit=!0),e.cssHooks[i].set(t.elem,t.start.transition(t.end,t.pos))}})},l.hook(o),e.cssHooks.borderColor={expand:function(e){var t={};return f(["Top","Right","Bottom","Left"],function(i,s){t["border"+s+"Color"]=e}),t}},a=e.Color.names={aqua:"#00ffff",black:"#000000",blue:"#0000ff",fuchsia:"#ff00ff",gray:"#808080",green:"#008000",lime:"#00ff00",maroon:"#800000",navy:"#000080",olive:"#808000",purple:"#800080",red:"#ff0000",silver:"#c0c0c0",teal:"#008080",white:"#ffffff",yellow:"#ffff00",transparent:[null,null,null,0],_default:"#ffffff"}}(b),function(){function t(t){var i,s,n=t.ownerDocument.defaultView?t.ownerDocument.defaultView.getComputedStyle(t,null):t.currentStyle,a={};if(n&&n.length&&n[0]&&n[n[0]])for(s=n.length;s--;)i=n[s],"string"==typeof n[i]&&(a[e.camelCase(i)]=n[i]);else for(i in n)"string"==typeof n[i]&&(a[i]=n[i]);return a}function i(t,i){var s,a,o={};for(s in i)a=i[s],t[s]!==a&&(n[s]||(e.fx.step[s]||!isNaN(parseFloat(a)))&&(o[s]=a));return o}var s=["add","remove","toggle"],n={border:1,borderBottom:1,borderColor:1,borderLeft:1,borderRight:1,borderTop:1,borderWidth:1,margin:1,padding:1};e.each(["borderLeftStyle","borderRightStyle","borderBottomStyle","borderTopStyle"],function(t,i){e.fx.step[i]=function(e){("none"!==e.end&&!e.setAttr||1===e.pos&&!e.setAttr)&&(b.style(e.elem,i,e.end),e.setAttr=!0)}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e.effects.animateClass=function(n,a,o,r){var h=e.speed(a,o,r);return this.queue(function(){var a,o=e(this),r=o.attr("class")||"",l=h.children?o.find("*").addBack():o;l=l.map(function(){var i=e(this);return{el:i,start:t(this)}}),a=function(){e.each(s,function(e,t){n[t]&&o[t+"Class"](n[t])})},a(),l=l.map(function(){return this.end=t(this.el[0]),this.diff=i(this.start,this.end),this}),o.attr("class",r),l=l.map(function(){var t=this,i=e.Deferred(),s=e.extend({},h,{queue:!1,complete:function(){i.resolve(t)}});return this.el.animate(this.diff,s),i.promise()}),e.when.apply(e,l.get()).done(function(){a(),e.each(arguments,function(){var t=this.el;e.each(this.diff,function(e){t.css(e,"")})}),h.complete.call(o[0])})})},e.fn.extend({addClass:function(t){return function(i,s,n,a){return s?e.effects.animateClass.call(this,{add:i},s,n,a):t.apply(this,arguments)}}(e.fn.addClass),removeClass:function(t){return function(i,s,n,a){return arguments.length>1?e.effects.animateClass.call(this,{remove:i},s,n,a):t.apply(this,arguments)}}(e.fn.removeClass),toggleClass:function(t){return function(i,s,n,a,o){return"boolean"==typeof s||void 0===s?n?e.effects.animateClass.call(this,s?{add:i}:{remove:i},n,a,o):t.apply(this,arguments):e.effects.animateClass.call(this,{toggle:i},s,n,a)}}(e.fn.toggleClass),switchClass:function(t,i,s,n,a){return e.effects.animateClass.call(this,{add:i,remove:t},s,n,a)}})}(),function(){function t(t,i,s,n){return e.isPlainObject(t)&&(i=t,t=t.effect),t={effect:t},null==i&&(i={}),e.isFunction(i)&&(n=i,s=null,i={}),("number"==typeof i||e.fx.speeds[i])&&(n=s,s=i,i={}),e.isFunction(s)&&(n=s,s=null),i&&e.extend(t,i),s=s||i.duration,t.duration=e.fx.off?0:"number"==typeof s?s:s in e.fx.speeds?e.fx.speeds[s]:e.fx.speeds._default,t.complete=n||i.complete,t}function i(t){return!t||"number"==typeof t||e.fx.speeds[t]?!0:"string"!=typeof t||e.effects.effect[t]?e.isFunction(t)?!0:"object"!=typeof t||t.effect?!1:!0:!0}e.extend(e.effects,{version:"1.11.4",save:function(e,t){for(var i=0;t.length>i;i++)null!==t[i]&&e.data(y+t[i],e[0].style[t[i]])},restore:function(e,t){var i,s;for(s=0;t.length>s;s++)null!==t[s]&&(i=e.data(y+t[s]),void 0===i&&(i=""),e.css(t[s],i))},setMode:function(e,t){return"toggle"===t&&(t=e.is(":hidden")?"show":"hide"),t},getBaseline:function(e,t){var i,s;switch(e[0]){case"top":i=0;break;case"middle":i=.5;break;case"bottom":i=1;break;default:i=e[0]/t.height}switch(e[1]){case"left":s=0;break;case"center":s=.5;break;case"right":s=1;break;default:s=e[1]/t.width}return{x:s,y:i}},createWrapper:function(t){if(t.parent().is(".ui-effects-wrapper"))return t.parent();var i={width:t.outerWidth(!0),height:t.outerHeight(!0),"float":t.css("float")},s=e("<div></div>").addClass("ui-effects-wrapper").css({fontSize:"100%",background:"transparent",border:"none",margin:0,padding:0}),n={width:t.width(),height:t.height()},a=document.activeElement;try{a.id}catch(o){a=document.body}return t.wrap(s),(t[0]===a||e.contains(t[0],a))&&e(a).focus(),s=t.parent(),"static"===t.css("position")?(s.css({position:"relative"}),t.css({position:"relative"})):(e.extend(i,{position:t.css("position"),zIndex:t.css("z-index")}),e.each(["top","left","bottom","right"],function(e,s){i[s]=t.css(s),isNaN(parseInt(i[s],10))&&(i[s]="auto")}),t.css({position:"relative",top:0,left:0,right:"auto",bottom:"auto"})),t.css(n),s.css(i).show()},removeWrapper:function(t){var i=document.activeElement;
return t.parent().is(".ui-effects-wrapper")&&(t.parent().replaceWith(t),(t[0]===i||e.contains(t[0],i))&&e(i).focus()),t},setTransition:function(t,i,s,n){return n=n||{},e.each(i,function(e,i){var a=t.cssUnit(i);a[0]>0&&(n[i]=a[0]*s+a[1])}),n}}),e.fn.extend({effect:function(){function i(t){function i(){e.isFunction(a)&&a.call(n[0]),e.isFunction(t)&&t()}var n=e(this),a=s.complete,r=s.mode;(n.is(":hidden")?"hide"===r:"show"===r)?(n[r](),i()):o.call(n[0],s,i)}var s=t.apply(this,arguments),n=s.mode,a=s.queue,o=e.effects.effect[s.effect];return e.fx.off||!o?n?this[n](s.duration,s.complete):this.each(function(){s.complete&&s.complete.call(this)}):a===!1?this.each(i):this.queue(a||"fx",i)},show:function(e){return function(s){if(i(s))return e.apply(this,arguments);var n=t.apply(this,arguments);return n.mode="show",this.effect.call(this,n)}}(e.fn.show),hide:function(e){return function(s){if(i(s))return e.apply(this,arguments);var n=t.apply(this,arguments);return n.mode="hide",this.effect.call(this,n)}}(e.fn.hide),toggle:function(e){return function(s){if(i(s)||"boolean"==typeof s)return e.apply(this,arguments);var n=t.apply(this,arguments);return n.mode="toggle",this.effect.call(this,n)}}(e.fn.toggle),cssUnit:function(t){var i=this.css(t),s=[];return e.each(["em","px","%","pt"],function(e,t){i.indexOf(t)>0&&(s=[parseFloat(i),t])}),s}})}(),function(){var t={};e.each(["Quad","Cubic","Quart","Quint","Expo"],function(e,i){t[i]=function(t){return Math.pow(t,e+2)}}),e.extend(t,{Sine:function(e){return 1-Math.cos(e*Math.PI/2)},Circ:function(e){return 1-Math.sqrt(1-e*e)},Elastic:function(e){return 0===e||1===e?e:-Math.pow(2,8*(e-1))*Math.sin((80*(e-1)-7.5)*Math.PI/15)},Back:function(e){return e*e*(3*e-2)},Bounce:function(e){for(var t,i=4;((t=Math.pow(2,--i))-1)/11>e;);return 1/Math.pow(4,3-i)-7.5625*Math.pow((3*t-2)/22-e,2)}}),e.each(t,function(t,i){e.easing["easeIn"+t]=i,e.easing["easeOut"+t]=function(e){return 1-i(1-e)},e.easing["easeInOut"+t]=function(e){return.5>e?i(2*e)/2:1-i(-2*e+2)/2}})}(),e.effects,e.effects.effect.blind=function(t,i){var s,n,a,o=e(this),r=/up|down|vertical/,h=/up|left|vertical|horizontal/,l=["position","top","bottom","left","right","height","width"],u=e.effects.setMode(o,t.mode||"hide"),d=t.direction||"up",c=r.test(d),p=c?"height":"width",f=c?"top":"left",m=h.test(d),g={},v="show"===u;o.parent().is(".ui-effects-wrapper")?e.effects.save(o.parent(),l):e.effects.save(o,l),o.show(),s=e.effects.createWrapper(o).css({overflow:"hidden"}),n=s[p](),a=parseFloat(s.css(f))||0,g[p]=v?n:0,m||(o.css(c?"bottom":"right",0).css(c?"top":"left","auto").css({position:"absolute"}),g[f]=v?a:n+a),v&&(s.css(p,0),m||s.css(f,a+n)),s.animate(g,{duration:t.duration,easing:t.easing,queue:!1,complete:function(){"hide"===u&&o.hide(),e.effects.restore(o,l),e.effects.removeWrapper(o),i()}})},e.effects.effect.bounce=function(t,i){var s,n,a,o=e(this),r=["position","top","bottom","left","right","height","width"],h=e.effects.setMode(o,t.mode||"effect"),l="hide"===h,u="show"===h,d=t.direction||"up",c=t.distance,p=t.times||5,f=2*p+(u||l?1:0),m=t.duration/f,g=t.easing,v="up"===d||"down"===d?"top":"left",y="up"===d||"left"===d,b=o.queue(),_=b.length;for((u||l)&&r.push("opacity"),e.effects.save(o,r),o.show(),e.effects.createWrapper(o),c||(c=o["top"===v?"outerHeight":"outerWidth"]()/3),u&&(a={opacity:1},a[v]=0,o.css("opacity",0).css(v,y?2*-c:2*c).animate(a,m,g)),l&&(c/=Math.pow(2,p-1)),a={},a[v]=0,s=0;p>s;s++)n={},n[v]=(y?"-=":"+=")+c,o.animate(n,m,g).animate(a,m,g),c=l?2*c:c/2;l&&(n={opacity:0},n[v]=(y?"-=":"+=")+c,o.animate(n,m,g)),o.queue(function(){l&&o.hide(),e.effects.restore(o,r),e.effects.removeWrapper(o),i()}),_>1&&b.splice.apply(b,[1,0].concat(b.splice(_,f+1))),o.dequeue()},e.effects.effect.clip=function(t,i){var s,n,a,o=e(this),r=["position","top","bottom","left","right","height","width"],h=e.effects.setMode(o,t.mode||"hide"),l="show"===h,u=t.direction||"vertical",d="vertical"===u,c=d?"height":"width",p=d?"top":"left",f={};e.effects.save(o,r),o.show(),s=e.effects.createWrapper(o).css({overflow:"hidden"}),n="IMG"===o[0].tagName?s:o,a=n[c](),l&&(n.css(c,0),n.css(p,a/2)),f[c]=l?a:0,f[p]=l?0:a/2,n.animate(f,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){l||o.hide(),e.effects.restore(o,r),e.effects.removeWrapper(o),i()}})},e.effects.effect.drop=function(t,i){var s,n=e(this),a=["position","top","bottom","left","right","opacity","height","width"],o=e.effects.setMode(n,t.mode||"hide"),r="show"===o,h=t.direction||"left",l="up"===h||"down"===h?"top":"left",u="up"===h||"left"===h?"pos":"neg",d={opacity:r?1:0};e.effects.save(n,a),n.show(),e.effects.createWrapper(n),s=t.distance||n["top"===l?"outerHeight":"outerWidth"](!0)/2,r&&n.css("opacity",0).css(l,"pos"===u?-s:s),d[l]=(r?"pos"===u?"+=":"-=":"pos"===u?"-=":"+=")+s,n.animate(d,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){"hide"===o&&n.hide(),e.effects.restore(n,a),e.effects.removeWrapper(n),i()}})},e.effects.effect.explode=function(t,i){function s(){b.push(this),b.length===d*c&&n()}function n(){p.css({visibility:"visible"}),e(b).remove(),m||p.hide(),i()}var a,o,r,h,l,u,d=t.pieces?Math.round(Math.sqrt(t.pieces)):3,c=d,p=e(this),f=e.effects.setMode(p,t.mode||"hide"),m="show"===f,g=p.show().css("visibility","hidden").offset(),v=Math.ceil(p.outerWidth()/c),y=Math.ceil(p.outerHeight()/d),b=[];for(a=0;d>a;a++)for(h=g.top+a*y,u=a-(d-1)/2,o=0;c>o;o++)r=g.left+o*v,l=o-(c-1)/2,p.clone().appendTo("body").wrap("<div></div>").css({position:"absolute",visibility:"visible",left:-o*v,top:-a*y}).parent().addClass("ui-effects-explode").css({position:"absolute",overflow:"hidden",width:v,height:y,left:r+(m?l*v:0),top:h+(m?u*y:0),opacity:m?0:1}).animate({left:r+(m?0:l*v),top:h+(m?0:u*y),opacity:m?1:0},t.duration||500,t.easing,s)},e.effects.effect.fade=function(t,i){var s=e(this),n=e.effects.setMode(s,t.mode||"toggle");s.animate({opacity:n},{queue:!1,duration:t.duration,easing:t.easing,complete:i})},e.effects.effect.fold=function(t,i){var s,n,a=e(this),o=["position","top","bottom","left","right","height","width"],r=e.effects.setMode(a,t.mode||"hide"),h="show"===r,l="hide"===r,u=t.size||15,d=/([0-9]+)%/.exec(u),c=!!t.horizFirst,p=h!==c,f=p?["width","height"]:["height","width"],m=t.duration/2,g={},v={};e.effects.save(a,o),a.show(),s=e.effects.createWrapper(a).css({overflow:"hidden"}),n=p?[s.width(),s.height()]:[s.height(),s.width()],d&&(u=parseInt(d[1],10)/100*n[l?0:1]),h&&s.css(c?{height:0,width:u}:{height:u,width:0}),g[f[0]]=h?n[0]:u,v[f[1]]=h?n[1]:0,s.animate(g,m,t.easing).animate(v,m,t.easing,function(){l&&a.hide(),e.effects.restore(a,o),e.effects.removeWrapper(a),i()})},e.effects.effect.highlight=function(t,i){var s=e(this),n=["backgroundImage","backgroundColor","opacity"],a=e.effects.setMode(s,t.mode||"show"),o={backgroundColor:s.css("backgroundColor")};"hide"===a&&(o.opacity=0),e.effects.save(s,n),s.show().css({backgroundImage:"none",backgroundColor:t.color||"#ffff99"}).animate(o,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){"hide"===a&&s.hide(),e.effects.restore(s,n),i()}})},e.effects.effect.size=function(t,i){var s,n,a,o=e(this),r=["position","top","bottom","left","right","width","height","overflow","opacity"],h=["position","top","bottom","left","right","overflow","opacity"],l=["width","height","overflow"],u=["fontSize"],d=["borderTopWidth","borderBottomWidth","paddingTop","paddingBottom"],c=["borderLeftWidth","borderRightWidth","paddingLeft","paddingRight"],p=e.effects.setMode(o,t.mode||"effect"),f=t.restore||"effect"!==p,m=t.scale||"both",g=t.origin||["middle","center"],v=o.css("position"),y=f?r:h,b={height:0,width:0,outerHeight:0,outerWidth:0};"show"===p&&o.show(),s={height:o.height(),width:o.width(),outerHeight:o.outerHeight(),outerWidth:o.outerWidth()},"toggle"===t.mode&&"show"===p?(o.from=t.to||b,o.to=t.from||s):(o.from=t.from||("show"===p?b:s),o.to=t.to||("hide"===p?b:s)),a={from:{y:o.from.height/s.height,x:o.from.width/s.width},to:{y:o.to.height/s.height,x:o.to.width/s.width}},("box"===m||"both"===m)&&(a.from.y!==a.to.y&&(y=y.concat(d),o.from=e.effects.setTransition(o,d,a.from.y,o.from),o.to=e.effects.setTransition(o,d,a.to.y,o.to)),a.from.x!==a.to.x&&(y=y.concat(c),o.from=e.effects.setTransition(o,c,a.from.x,o.from),o.to=e.effects.setTransition(o,c,a.to.x,o.to))),("content"===m||"both"===m)&&a.from.y!==a.to.y&&(y=y.concat(u).concat(l),o.from=e.effects.setTransition(o,u,a.from.y,o.from),o.to=e.effects.setTransition(o,u,a.to.y,o.to)),e.effects.save(o,y),o.show(),e.effects.createWrapper(o),o.css("overflow","hidden").css(o.from),g&&(n=e.effects.getBaseline(g,s),o.from.top=(s.outerHeight-o.outerHeight())*n.y,o.from.left=(s.outerWidth-o.outerWidth())*n.x,o.to.top=(s.outerHeight-o.to.outerHeight)*n.y,o.to.left=(s.outerWidth-o.to.outerWidth)*n.x),o.css(o.from),("content"===m||"both"===m)&&(d=d.concat(["marginTop","marginBottom"]).concat(u),c=c.concat(["marginLeft","marginRight"]),l=r.concat(d).concat(c),o.find("*[width]").each(function(){var i=e(this),s={height:i.height(),width:i.width(),outerHeight:i.outerHeight(),outerWidth:i.outerWidth()};f&&e.effects.save(i,l),i.from={height:s.height*a.from.y,width:s.width*a.from.x,outerHeight:s.outerHeight*a.from.y,outerWidth:s.outerWidth*a.from.x},i.to={height:s.height*a.to.y,width:s.width*a.to.x,outerHeight:s.height*a.to.y,outerWidth:s.width*a.to.x},a.from.y!==a.to.y&&(i.from=e.effects.setTransition(i,d,a.from.y,i.from),i.to=e.effects.setTransition(i,d,a.to.y,i.to)),a.from.x!==a.to.x&&(i.from=e.effects.setTransition(i,c,a.from.x,i.from),i.to=e.effects.setTransition(i,c,a.to.x,i.to)),i.css(i.from),i.animate(i.to,t.duration,t.easing,function(){f&&e.effects.restore(i,l)})})),o.animate(o.to,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){0===o.to.opacity&&o.css("opacity",o.from.opacity),"hide"===p&&o.hide(),e.effects.restore(o,y),f||("static"===v?o.css({position:"relative",top:o.to.top,left:o.to.left}):e.each(["top","left"],function(e,t){o.css(t,function(t,i){var s=parseInt(i,10),n=e?o.to.left:o.to.top;return"auto"===i?n+"px":s+n+"px"})})),e.effects.removeWrapper(o),i()}})},e.effects.effect.scale=function(t,i){var s=e(this),n=e.extend(!0,{},t),a=e.effects.setMode(s,t.mode||"effect"),o=parseInt(t.percent,10)||(0===parseInt(t.percent,10)?0:"hide"===a?0:100),r=t.direction||"both",h=t.origin,l={height:s.height(),width:s.width(),outerHeight:s.outerHeight(),outerWidth:s.outerWidth()},u={y:"horizontal"!==r?o/100:1,x:"vertical"!==r?o/100:1};n.effect="size",n.queue=!1,n.complete=i,"effect"!==a&&(n.origin=h||["middle","center"],n.restore=!0),n.from=t.from||("show"===a?{height:0,width:0,outerHeight:0,outerWidth:0}:l),n.to={height:l.height*u.y,width:l.width*u.x,outerHeight:l.outerHeight*u.y,outerWidth:l.outerWidth*u.x},n.fade&&("show"===a&&(n.from.opacity=0,n.to.opacity=1),"hide"===a&&(n.from.opacity=1,n.to.opacity=0)),s.effect(n)},e.effects.effect.puff=function(t,i){var s=e(this),n=e.effects.setMode(s,t.mode||"hide"),a="hide"===n,o=parseInt(t.percent,10)||150,r=o/100,h={height:s.height(),width:s.width(),outerHeight:s.outerHeight(),outerWidth:s.outerWidth()};e.extend(t,{effect:"scale",queue:!1,fade:!0,mode:n,complete:i,percent:a?o:100,from:a?h:{height:h.height*r,width:h.width*r,outerHeight:h.outerHeight*r,outerWidth:h.outerWidth*r}}),s.effect(t)},e.effects.effect.pulsate=function(t,i){var s,n=e(this),a=e.effects.setMode(n,t.mode||"show"),o="show"===a,r="hide"===a,h=o||"hide"===a,l=2*(t.times||5)+(h?1:0),u=t.duration/l,d=0,c=n.queue(),p=c.length;for((o||!n.is(":visible"))&&(n.css("opacity",0).show(),d=1),s=1;l>s;s++)n.animate({opacity:d},u,t.easing),d=1-d;n.animate({opacity:d},u,t.easing),n.queue(function(){r&&n.hide(),i()}),p>1&&c.splice.apply(c,[1,0].concat(c.splice(p,l+1))),n.dequeue()},e.effects.effect.shake=function(t,i){var s,n=e(this),a=["position","top","bottom","left","right","height","width"],o=e.effects.setMode(n,t.mode||"effect"),r=t.direction||"left",h=t.distance||20,l=t.times||3,u=2*l+1,d=Math.round(t.duration/u),c="up"===r||"down"===r?"top":"left",p="up"===r||"left"===r,f={},m={},g={},v=n.queue(),y=v.length;for(e.effects.save(n,a),n.show(),e.effects.createWrapper(n),f[c]=(p?"-=":"+=")+h,m[c]=(p?"+=":"-=")+2*h,g[c]=(p?"-=":"+=")+2*h,n.animate(f,d,t.easing),s=1;l>s;s++)n.animate(m,d,t.easing).animate(g,d,t.easing);n.animate(m,d,t.easing).animate(f,d/2,t.easing).queue(function(){"hide"===o&&n.hide(),e.effects.restore(n,a),e.effects.removeWrapper(n),i()}),y>1&&v.splice.apply(v,[1,0].concat(v.splice(y,u+1))),n.dequeue()},e.effects.effect.slide=function(t,i){var s,n=e(this),a=["position","top","bottom","left","right","width","height"],o=e.effects.setMode(n,t.mode||"show"),r="show"===o,h=t.direction||"left",l="up"===h||"down"===h?"top":"left",u="up"===h||"left"===h,d={};e.effects.save(n,a),n.show(),s=t.distance||n["top"===l?"outerHeight":"outerWidth"](!0),e.effects.createWrapper(n).css({overflow:"hidden"}),r&&n.css(l,u?isNaN(s)?"-"+s:-s:s),d[l]=(r?u?"+=":"-=":u?"-=":"+=")+s,n.animate(d,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){"hide"===o&&n.hide(),e.effects.restore(n,a),e.effects.removeWrapper(n),i()}})},e.effects.effect.transfer=function(t,i){var s=e(this),n=e(t.to),a="fixed"===n.css("position"),o=e("body"),r=a?o.scrollTop():0,h=a?o.scrollLeft():0,l=n.offset(),u={top:l.top-r,left:l.left-h,height:n.innerHeight(),width:n.innerWidth()},d=s.offset(),c=e("<div class='ui-effects-transfer'></div>").appendTo(document.body).addClass(t.className).css({top:d.top-r,left:d.left-h,height:s.innerHeight(),width:s.innerWidth(),position:a?"fixed":"absolute"}).animate(u,t.duration,t.easing,function(){c.remove(),i()})}});/*!
 * Bootstrap v3.3.7 (http://getbootstrap.com)
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under the MIT license
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){"use strict";var b=a.fn.jquery.split(" ")[0].split(".");if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1||b[0]>3)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4")}(jQuery),+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){if(a(b.target).is(this))return b.handleObj.handler.apply(this,arguments)}})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var c=a(this),e=c.data("bs.alert");e||c.data("bs.alert",e=new d(this)),"string"==typeof b&&e[b].call(c)})}var c='[data-dismiss="alert"]',d=function(b){a(b).on("click",c,this.close)};d.VERSION="3.3.7",d.TRANSITION_DURATION=150,d.prototype.close=function(b){function c(){g.detach().trigger("closed.bs.alert").remove()}var e=a(this),f=e.attr("data-target");f||(f=e.attr("href"),f=f&&f.replace(/.*(?=#[^\s]*$)/,""));var g=a("#"===f?[]:f);b&&b.preventDefault(),g.length||(g=e.closest(".alert")),g.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(g.removeClass("in"),a.support.transition&&g.hasClass("fade")?g.one("bsTransitionEnd",c).emulateTransitionEnd(d.TRANSITION_DURATION):c())};var e=a.fn.alert;a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){return a.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof b&&b;e||d.data("bs.button",e=new c(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.isLoading=!1};c.VERSION="3.3.7",c.DEFAULTS={loadingText:"loading..."},c.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",null==f.resetText&&d.data("resetText",d[e]()),setTimeout(a.proxy(function(){d[e](null==f[b]?this.options[b]:f[b]),"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c).prop(c,!0)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c).prop(c,!1))},this),0)},c.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")?(c.prop("checked")&&(a=!1),b.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==c.prop("type")&&(c.prop("checked")!==this.$element.hasClass("active")&&(a=!1),this.$element.toggleClass("active")),c.prop("checked",this.$element.hasClass("active")),a&&c.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var d=a.fn.button;a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){return a.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){var d=a(c.target).closest(".btn");b.call(d,"toggle"),a(c.target).is('input[type="radio"], input[type="checkbox"]')||(c.preventDefault(),d.is("input,button")?d.trigger("focus"):d.find("input:visible,button:visible").first().trigger("focus"))}).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',function(b){a(b.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(b.type))})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b),g="string"==typeof b?b:f.slide;e||d.data("bs.carousel",e=new c(this,f)),"number"==typeof b?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}var c=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=null,this.sliding=null,this.interval=null,this.$active=null,this.$items=null,this.options.keyboard&&this.$element.on("keydown.bs.carousel",a.proxy(this.keydown,this)),"hover"==this.options.pause&&!("ontouchstart"in document.documentElement)&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};c.VERSION="3.3.7",c.TRANSITION_DURATION=600,c.DEFAULTS={interval:5e3,pause:"hover",wrap:!0,keyboard:!0},c.prototype.keydown=function(a){if(!/input|textarea/i.test(a.target.tagName)){switch(a.which){case 37:this.prev();break;case 39:this.next();break;default:return}a.preventDefault()}},c.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){return this.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.getItemForDirection=function(a,b){var c=this.getItemIndex(b),d="prev"==a&&0===c||"next"==a&&c==this.$items.length-1;if(d&&!this.options.wrap)return b;var e="prev"==a?-1:1,f=(c+e)%this.$items.length;return this.$items.eq(f)},c.prototype.to=function(a){var b=this,c=this.getItemIndex(this.$active=this.$element.find(".item.active"));if(!(a>this.$items.length-1||a<0))return this.sliding?this.$element.one("slid.bs.carousel",function(){b.to(a)}):c==a?this.pause().cycle():this.slide(a>c?"next":"prev",this.$items.eq(a))},c.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){if(!this.sliding)return this.slide("next")},c.prototype.prev=function(){if(!this.sliding)return this.slide("prev")},c.prototype.slide=function(b,d){var e=this.$element.find(".item.active"),f=d||this.getItemForDirection(b,e),g=this.interval,h="next"==b?"left":"right",i=this;if(f.hasClass("active"))return this.sliding=!1;var j=f[0],k=a.Event("slide.bs.carousel",{relatedTarget:j,direction:h});if(this.$element.trigger(k),!k.isDefaultPrevented()){if(this.sliding=!0,g&&this.pause(),this.$indicators.length){this.$indicators.find(".active").removeClass("active");var l=a(this.$indicators.children()[this.getItemIndex(f)]);l&&l.addClass("active")}var m=a.Event("slid.bs.carousel",{relatedTarget:j,direction:h});return a.support.transition&&this.$element.hasClass("slide")?(f.addClass(b),f[0].offsetWidth,e.addClass(h),f.addClass(h),e.one("bsTransitionEnd",function(){f.removeClass([b,h].join(" ")).addClass("active"),e.removeClass(["active",h].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger(m)},0)}).emulateTransitionEnd(c.TRANSITION_DURATION)):(e.removeClass("active"),f.addClass("active"),this.sliding=!1,this.$element.trigger(m)),g&&this.cycle(),this}};var d=a.fn.carousel;a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){return a.fn.carousel=d,this};var e=function(c){var d,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));if(f.hasClass("carousel")){var g=a.extend({},f.data(),e.data()),h=e.attr("data-slide-to");h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}};a(document).on("click.bs.carousel.data-api","[data-slide]",e).on("click.bs.carousel.data-api","[data-slide-to]",e),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var c=a(this);b.call(c,c.data())})})}(jQuery),+function(a){"use strict";function b(b){var c,d=b.attr("data-target")||(c=b.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"");return a(d)}function c(b){return this.each(function(){var c=a(this),e=c.data("bs.collapse"),f=a.extend({},d.DEFAULTS,c.data(),"object"==typeof b&&b);!e&&f.toggle&&/show|hide/.test(b)&&(f.toggle=!1),e||c.data("bs.collapse",e=new d(this,f)),"string"==typeof b&&e[b]()})}var d=function(b,c){this.$element=a(b),this.options=a.extend({},d.DEFAULTS,c),this.$trigger=a('[data-toggle="collapse"][href="#'+b.id+'"],[data-toggle="collapse"][data-target="#'+b.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};d.VERSION="3.3.7",d.TRANSITION_DURATION=350,d.DEFAULTS={toggle:!0},d.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},d.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var b,e=this.$parent&&this.$parent.children(".panel").children(".in, .collapsing");if(!(e&&e.length&&(b=e.data("bs.collapse"),b&&b.transitioning))){var f=a.Event("show.bs.collapse");if(this.$element.trigger(f),!f.isDefaultPrevented()){e&&e.length&&(c.call(e,"hide"),b||e.data("bs.collapse",null));var g=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[g](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;var h=function(){this.$element.removeClass("collapsing").addClass("collapse in")[g](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return h.call(this);var i=a.camelCase(["scroll",g].join("-"));this.$element.one("bsTransitionEnd",a.proxy(h,this)).emulateTransitionEnd(d.TRANSITION_DURATION)[g](this.$element[0][i])}}}},d.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;var e=function(){this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};return a.support.transition?void this.$element[c](0).one("bsTransitionEnd",a.proxy(e,this)).emulateTransitionEnd(d.TRANSITION_DURATION):e.call(this)}}},d.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()},d.prototype.getParent=function(){return a(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(a.proxy(function(c,d){var e=a(d);this.addAriaAndCollapsedClass(b(e),e)},this)).end()},d.prototype.addAriaAndCollapsedClass=function(a,b){var c=a.hasClass("in");a.attr("aria-expanded",c),b.toggleClass("collapsed",!c).attr("aria-expanded",c)};var e=a.fn.collapse;a.fn.collapse=c,a.fn.collapse.Constructor=d,a.fn.collapse.noConflict=function(){return a.fn.collapse=e,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(d){var e=a(this);e.attr("data-target")||d.preventDefault();var f=b(e),g=f.data("bs.collapse"),h=g?"toggle":e.data();c.call(f,h)})}(jQuery),+function(a){"use strict";function b(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}function c(c){c&&3===c.which||(a(e).remove(),a(f).each(function(){var d=a(this),e=b(d),f={relatedTarget:this};e.hasClass("open")&&(c&&"click"==c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(e[0],c.target)||(e.trigger(c=a.Event("hide.bs.dropdown",f)),c.isDefaultPrevented()||(d.attr("aria-expanded","false"),e.removeClass("open").trigger(a.Event("hidden.bs.dropdown",f)))))}))}function d(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new g(this)),"string"==typeof b&&d[b].call(c)})}var e=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){a(b).on("click.bs.dropdown",this.toggle)};g.VERSION="3.3.7",g.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=b(e),g=f.hasClass("open");if(c(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click",c);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;e.trigger("focus").attr("aria-expanded","true"),f.toggleClass("open").trigger(a.Event("shown.bs.dropdown",h))}return!1}},g.prototype.keydown=function(c){if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)){var d=a(this);if(c.preventDefault(),c.stopPropagation(),!d.is(".disabled, :disabled")){var e=b(d),g=e.hasClass("open");if(!g&&27!=c.which||g&&27==c.which)return 27==c.which&&e.find(f).trigger("focus"),d.trigger("click");var h=" li:not(.disabled):visible a",i=e.find(".dropdown-menu"+h);if(i.length){var j=i.index(c.target);38==c.which&&j>0&&j--,40==c.which&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};var h=a.fn.dropdown;a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",c).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery),+function(a){"use strict";function b(b,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},c.DEFAULTS,e.data(),"object"==typeof b&&b);f||e.data("bs.modal",f=new c(this,g)),"string"==typeof b?f[b](d):g.show&&f.show(d)})}var c=function(b,c){this.options=c,this.$body=a(document.body),this.$element=a(b),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};c.VERSION="3.3.7",c.TRANSITION_DURATION=300,c.BACKDROP_TRANSITION_DURATION=150,c.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){return this.isShown?this.hide():this.show(a)},c.prototype.show=function(b){var d=this,e=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(e),this.isShown||e.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){d.$element.one("mouseup.dismiss.bs.modal",function(b){a(b.target).is(d.$element)&&(d.ignoreBackdropClick=!0)})}),this.backdrop(function(){var e=a.support.transition&&d.$element.hasClass("fade");d.$element.parent().length||d.$element.appendTo(d.$body),d.$element.show().scrollTop(0),d.adjustDialog(),e&&d.$element[0].offsetWidth,d.$element.addClass("in"),d.enforceFocus();var f=a.Event("shown.bs.modal",{relatedTarget:b});e?d.$dialog.one("bsTransitionEnd",function(){d.$element.trigger("focus").trigger(f)}).emulateTransitionEnd(c.TRANSITION_DURATION):d.$element.trigger("focus").trigger(f)}))},c.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(c.TRANSITION_DURATION):this.hideModal())},c.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){document===a.target||this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},c.prototype.resize=function(){this.isShown?a(window).on("resize.bs.modal",a.proxy(this.handleUpdate,this)):a(window).off("resize.bs.modal")},c.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.$body.removeClass("modal-open"),a.resetAdjustments(),a.resetScrollbar(),a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){var d=this,e=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var f=a.support.transition&&e;if(this.$backdrop=a(document.createElement("div")).addClass("modal-backdrop "+e).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){return this.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),f&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;f?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var g=function(){d.removeBackdrop(),b&&b()};a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):g()}else b&&b()},c.prototype.handleUpdate=function(){this.adjustDialog()},c.prototype.adjustDialog=function(){var a=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&a?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!a?this.scrollbarWidth:""})},c.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},c.prototype.checkScrollbar=function(){var a=window.innerWidth;if(!a){var b=document.documentElement.getBoundingClientRect();a=b.right-Math.abs(b.left)}this.bodyIsOverflowing=document.body.clientWidth<a,this.scrollbarWidth=this.measureScrollbar()},c.prototype.setScrollbar=function(){var a=parseInt(this.$body.css("padding-right")||0,10);this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad)},c.prototype.measureScrollbar=function(){var a=document.createElement("div");a.className="modal-scrollbar-measure",this.$body.append(a);var b=a.offsetWidth-a.clientWidth;return this.$body[0].removeChild(a),b};var d=a.fn.modal;a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){return a.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){var d=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(e)&&e},f.data(),d.data());d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof b&&b;!e&&/destroy|hide/.test(b)||(e||d.data("bs.tooltip",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",a,b)};c.VERSION="3.3.7",c.TRANSITION_DURATION=150,c.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},c.prototype.init=function(b,c,d){if(this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(a.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusin"==b.type?"focus":"hover"]=!0),c.tip().hasClass("in")||"in"==c.hoverState?void(c.hoverState="in"):(clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show())},c.prototype.isInStateTrue=function(){for(var a in this.inState)if(this.inState[a])return!0;return!1},c.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);if(c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusout"==b.type?"focus":"hover"]=!1),!c.isInStateTrue())return clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide()},c.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(b);var d=a.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(b.isDefaultPrevented()||!d)return;var e=this,f=this.tip(),g=this.getUID(this.type);this.setContent(),f.attr("id",g),this.$element.attr("aria-describedby",g),this.options.animation&&f.addClass("fade");var h="function"==typeof this.options.placement?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,i=/\s?auto?\s?/i,j=i.test(h);j&&(h=h.replace(i,"")||"top"),f.detach().css({top:0,left:0,display:"block"}).addClass(h).data("bs."+this.type,this),this.options.container?f.appendTo(this.options.container):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var k=this.getPosition(),l=f[0].offsetWidth,m=f[0].offsetHeight;if(j){var n=h,o=this.getPosition(this.$viewport);h="bottom"==h&&k.bottom+m>o.bottom?"top":"top"==h&&k.top-m<o.top?"bottom":"right"==h&&k.right+l>o.width?"left":"left"==h&&k.left-l<o.left?"right":h,f.removeClass(n).addClass(h)}var p=this.getCalculatedOffset(h,k,l,m);this.applyPlacement(p,h);var q=function(){var a=e.hoverState;e.$element.trigger("shown.bs."+e.type),e.hoverState=null,"out"==a&&e.leave(e)};a.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",q).emulateTransitionEnd(c.TRANSITION_DURATION):q()}},c.prototype.applyPlacement=function(b,c){var d=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top+=g,b.left+=h,a.offset.setOffset(d[0],a.extend({using:function(a){d.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");var i=d[0].offsetWidth,j=d[0].offsetHeight;"top"==c&&j!=f&&(b.top=b.top+f-j);var k=this.getViewportAdjustedDelta(c,b,i,j);k.left?b.left+=k.left:b.top+=k.top;var l=/top|bottom/.test(c),m=l?2*k.left-e+i:2*k.top-f+j,n=l?"offsetWidth":"offsetHeight";d.offset(b),this.replaceArrow(m,d[0][n],l)},c.prototype.replaceArrow=function(a,b,c){this.arrow().css(c?"left":"top",50*(1-a/b)+"%").css(c?"top":"left","")},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},c.prototype.hide=function(b){function d(){"in"!=e.hoverState&&f.detach(),e.$element&&e.$element.removeAttr("aria-describedby").trigger("hidden.bs."+e.type),b&&b()}var e=this,f=a(this.$tip),g=a.Event("hide.bs."+this.type);if(this.$element.trigger(g),!g.isDefaultPrevented())return f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",d).emulateTransitionEnd(c.TRANSITION_DURATION):d(),this.hoverState=null,this},c.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){return this.getTitle()},c.prototype.getPosition=function(b){b=b||this.$element;var c=b[0],d="BODY"==c.tagName,e=c.getBoundingClientRect();null==e.width&&(e=a.extend({},e,{width:e.right-e.left,height:e.bottom-e.top}));var f=window.SVGElement&&c instanceof window.SVGElement,g=d?{top:0,left:0}:f?null:b.offset(),h={scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop()},i=d?{width:a(window).width(),height:a(window).height()}:null;return a.extend({},e,h,i,g)},c.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){var e={top:0,left:0};if(!this.$viewport)return e;var f=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);if(/right|left/.test(a)){var h=b.top-f-g.scroll,i=b.top+f-g.scroll+d;h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{var j=b.left-f,k=b.left+f+c;j<g.left?e.left=g.left-j:k>g.right&&(e.left=g.left+g.width-k)}return e},c.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){do a+=~~(1e6*Math.random());while(document.getElementById(a));return a},c.prototype.tip=function(){if(!this.$tip&&(this.$tip=a(this.options.template),1!=this.$tip.length))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.enable=function(){this.enabled=!0},c.prototype.disable=function(){this.enabled=!1},c.prototype.toggleEnabled=function(){this.enabled=!this.enabled},c.prototype.toggle=function(b){var c=this;b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),b?(c.inState.click=!c.inState.click,c.isInStateTrue()?c.enter(c):c.leave(c)):c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){var a=this;clearTimeout(this.timeout),this.hide(function(){a.$element.off("."+a.type).removeData("bs."+a.type),a.$tip&&a.$tip.detach(),a.$tip=null,a.$arrow=null,a.$viewport=null,a.$element=null})};var d=a.fn.tooltip;a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=d,this}}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof b&&b;!e&&/destroy|hide/.test(b)||(e||d.data("bs.popover",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");c.VERSION="3.3.7",c.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),c.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").children().detach().end()[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){return this.getTitle()||this.getContent()},c.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")};var d=a.fn.popover;a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){return a.fn.popover=d,this}}(jQuery),+function(a){"use strict";function b(c,d){this.$body=a(document.body),this.$scrollElement=a(a(c).is(document.body)?window:c),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",a.proxy(this.process,this)),this.refresh(),this.process()}function c(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})}b.VERSION="3.3.7",b.DEFAULTS={offset:10},b.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){var b=this,c="offset",d=0;this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),a.isWindow(this.$scrollElement[0])||(c="position",d=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){var b=a(this),e=b.data("target")||b.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[c]().top+d,e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){b.offsets.push(this[0]),b.targets.push(this[1])})},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(this.scrollHeight!=c&&this.refresh(),b>=d)return g!=(a=f[f.length-1])&&this.activate(a);if(g&&b<e[0])return this.activeTarget=null,this.clear();for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(void 0===e[a+1]||b<e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){
this.activeTarget=b,this.clear();var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate.bs.scrollspy")},b.prototype.clear=function(){a(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};var d=a.fn.scrollspy;a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new c(this)),"string"==typeof b&&e[b]()})}var c=function(b){this.element=a(b)};c.VERSION="3.3.7",c.TRANSITION_DURATION=150,c.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a"),f=a.Event("hide.bs.tab",{relatedTarget:b[0]}),g=a.Event("show.bs.tab",{relatedTarget:e[0]});if(e.trigger(f),b.trigger(g),!g.isDefaultPrevented()&&!f.isDefaultPrevented()){var h=a(d);this.activate(b.closest("li"),c),this.activate(h,h.parent(),function(){e.trigger({type:"hidden.bs.tab",relatedTarget:b[0]}),b.trigger({type:"shown.bs.tab",relatedTarget:e[0]})})}}},c.prototype.activate=function(b,d,e){function f(){g.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),h?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu").length&&b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),e&&e()}var g=d.find("> .active"),h=e&&a.support.transition&&(g.length&&g.hasClass("fade")||!!d.find("> .fade").length);g.length&&h?g.one("bsTransitionEnd",f).emulateTransitionEnd(c.TRANSITION_DURATION):f(),g.removeClass("in")};var d=a.fn.tab;a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){return a.fn.tab=d,this};var e=function(c){c.preventDefault(),b.call(a(this),"show")};a(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',e).on("click.bs.tab.data-api",'[data-toggle="pill"]',e)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof b&&b;e||d.data("bs.affix",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.options=a.extend({},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};c.VERSION="3.3.7",c.RESET="affix affix-top affix-bottom",c.DEFAULTS={offset:0,target:window},c.prototype.getState=function(a,b,c,d){var e=this.$target.scrollTop(),f=this.$element.offset(),g=this.$target.height();if(null!=c&&"top"==this.affixed)return e<c&&"top";if("bottom"==this.affixed)return null!=c?!(e+this.unpin<=f.top)&&"bottom":!(e+g<=a-d)&&"bottom";var h=null==this.affixed,i=h?e:f.top,j=h?g:b;return null!=c&&e<=c?"top":null!=d&&i+j>=a-d&&"bottom"},c.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(c.RESET).addClass("affix");var a=this.$target.scrollTop(),b=this.$element.offset();return this.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){if(this.$element.is(":visible")){var b=this.$element.height(),d=this.options.offset,e=d.top,f=d.bottom,g=Math.max(a(document).height(),a(document.body).height());"object"!=typeof d&&(f=e=d),"function"==typeof e&&(e=d.top(this.$element)),"function"==typeof f&&(f=d.bottom(this.$element));var h=this.getState(g,b,e,f);if(this.affixed!=h){null!=this.unpin&&this.$element.css("top","");var i="affix"+(h?"-"+h:""),j=a.Event(i+".bs.affix");if(this.$element.trigger(j),j.isDefaultPrevented())return;this.affixed=h,this.unpin="bottom"==h?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(i).trigger(i.replace("affix","affixed")+".bs.affix")}"bottom"==h&&this.$element.offset({top:g-b-f})}};var d=a.fn.affix;a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){return a.fn.affix=d,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var c=a(this),d=c.data();d.offset=d.offset||{},null!=d.offsetBottom&&(d.offset.bottom=d.offsetBottom),null!=d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);window.onkeydown=function(event){if(event.keyCode==78){if(event.ctrlKey){event.returnValue=false;event.keyCode=0;window.status='New window is disabled';return false;}}};/* ==========================================================
 * bootstrap-formhelpers-colorpicker.js
 * https://github.com/vlamanna/BootstrapFormHelpers
 * ==========================================================
 * Copyright 2012 Vincent Lamanna
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
 
 
+function ($) {

  'use strict';
  
  
  /* COLORPICKER CLASS DEFINITION
  * ========================= */

  var toggle = '[data-toggle=bfh-colorpicker]',
      BFHColorPicker = function (element, options) {
        this.options = $.extend({}, $.fn.bfhcolorpicker.defaults, options);
        this.$element = $(element);

        this.initPopover();
      };

  BFHColorPicker.prototype = {

    constructor: BFHColorPicker,

    initPalette: function() {
      var $canvas,
          context,
          gradient;
          
      $canvas = this.$element.find('canvas');
      context = $canvas[0].getContext('2d');
      
      gradient = context.createLinearGradient(0, 0, $canvas.width(), 0);
      
      gradient.addColorStop(0,    'rgb(255, 255, 255)');
      gradient.addColorStop(0.1,  'rgb(255,   0,   0)');
      gradient.addColorStop(0.25, 'rgb(255,   0, 255)');
      gradient.addColorStop(0.4,  'rgb(0,     0, 255)');
      gradient.addColorStop(0.55, 'rgb(0,   255, 255)');
      gradient.addColorStop(0.7,  'rgb(0,   255,   0)');
      gradient.addColorStop(0.85, 'rgb(255, 255,   0)');
      gradient.addColorStop(1,    'rgb(255,   0,   0)');
      
      context.fillStyle = gradient;
      context.fillRect(0, 0, context.canvas.width, context.canvas.height);
      
      gradient = context.createLinearGradient(0, 0, 0, $canvas.height());
      gradient.addColorStop(0,   'rgba(255, 255, 255, 1)');
      gradient.addColorStop(0.5, 'rgba(255, 255, 255, 0)');
      gradient.addColorStop(0.5, 'rgba(0,     0,   0, 0)');
      gradient.addColorStop(1,   'rgba(0,     0,   0, 1)');
      
      context.fillStyle = gradient;
      context.fillRect(0, 0, context.canvas.width, context.canvas.height);
    },
    
    initPopover: function() {
      var iconLeft,
          iconRight;

      iconLeft = '';
      iconRight = '';
      if (this.options.align === 'right') {
        iconRight = '<span class="input-group-addon"><span class="bfh-colorpicker-icon"></span></span>';
      } else {
        iconLeft = '<span class="input-group-addon"><span class="bfh-colorpicker-icon"></span></span>';
      }

      this.$element.html(
        '<div class="input-group bfh-colorpicker-toggle" data-toggle="bfh-colorpicker">' +
        iconLeft +
        '<input type="text" id="' + this.options.name + '" name="' + this.options.name + '" class="' + this.options.input + '" placeholder="' + this.options.placeholder + '" readonly>' +
        iconRight +
        '</div>' +
        '<div class="bfh-colorpicker-popover">' +
        '<canvas class="bfh-colorpicker-palette" width="384" height="256"></canvas>' +
        '</div>'
      );

      this.$element
        .on('click.bfhcolorpicker.data-api touchstart.bfhcolorpicker.data-api', toggle, BFHColorPicker.prototype.toggle)
        .on('mousedown.bfhcolorpicker.data-api', 'canvas', BFHColorPicker.prototype.mouseDown)
        .on('click.bfhcolorpicker.data-api touchstart.bfhcolorpicker.data-api', '.bfh-colorpicker-popover', function() { return false; });

      this.initPalette();
      
      this.$element.val(this.options.color);
    },
    
    updateVal: function(positionX, positionY) {
      var $canvas,
          context,
          colorX,
          colorY,
          snappiness,
          imageData,
          newColor;
      
      snappiness = 5;
      
      $canvas = this.$element.find('canvas');
      context = $canvas[0].getContext('2d');
      
      colorX = positionX - $canvas.offset().left;
      colorY = positionY - $canvas.offset().top;
      
      colorX = Math.round(colorX / snappiness) * snappiness;
      colorY = Math.round(colorY / snappiness) * snappiness;
      
      if (colorX < 0) {
        colorX = 0;
      }
      if (colorX >= $canvas.width()) {
        colorX = $canvas.width() - 1;
      }
      
      if (colorY < 0) {
        colorY = 0;
      }
      if (colorY > $canvas.height()) {
        colorY = $canvas.height();
      }
      
      imageData = context.getImageData(colorX, colorY, 1, 1);
      newColor = rgbToHex(imageData.data[0], imageData.data[1], imageData.data[2]);
      
      if (newColor !== this.$element.val()) {
        this.$element.val(newColor);
        
        this.$element.trigger('change.bfhcolorpicker');
      }
    },
    
    mouseDown: function(e) {
      var $this,
          $parent;
      
      $this = $(this);
      $parent = getParent($this);
      
      $(document)
        .on('mousemove.bfhcolorpicker.data-api', {colorpicker: $parent}, BFHColorPicker.prototype.mouseMove)
        .one('mouseup.bfhcolorpicker.data-api', {colorpicker: $parent}, BFHColorPicker.prototype.mouseUp);
    },
    
    mouseMove: function(e) {
      var $this;
      
      $this = e.data.colorpicker;
      
      $this.data('bfhcolorpicker').updateVal(e.pageX, e.pageY);
    },
    
    mouseUp: function(e) {
      var $this;
      
      $this = e.data.colorpicker;
      
      $this.data('bfhcolorpicker').updateVal(e.pageX, e.pageY);
      
      $(document).off('mousemove.bfhcolorpicker.data-api');
      
      if ($this.data('bfhcolorpicker').options.close === true) {
        clearMenus();
      }
    },

    toggle: function (e) {
      var $this,
          $parent,
          isActive;

      $this = $(this);
      $parent = getParent($this);

      if ($parent.is('.disabled') || $parent.attr('disabled') !== undefined) {
        return true;
      }

      isActive = $parent.hasClass('open');

      clearMenus();

      if (!isActive) {
        $parent.trigger(e = $.Event('show.bfhcolorpicker'));

        if (e.isDefaultPrevented()) {
          return true;
        }

        $parent
          .toggleClass('open')
          .trigger('shown.bfhcolorpicker');

        $this.focus();
		
		//Setting colorbox positions
			
			//Getting a parent to check dimensions
			var parent_dimensions = {};
			var parent_container = $this.closest('.modal-content');	// if colorpicker is in a modal parent will be modal
			if(parent_container.length == 0){
				parent_container = $this.closest('body');	// else closet body tag will be its parent
			}
			
			parent_dimensions.width = parent_container.width();
			parent_dimensions.height = parent_container.outerHeight();
			
			var target_elem = $parent.find('.bfh-colorpicker-popover');
			target_elem.css('top','');
			var target_dimensions = target_elem.offset();
			var target_width = parseInt(target_dimensions.left + target_elem.outerWidth());
			
			//Setting horizontal positions
			if(target_width > parent_dimensions.width){
				var diff = parseInt(target_elem.outerWidth() - $this.outerWidth());
				var new_width = (-diff);
				target_elem.css('left',new_width);
			}
			
			//Setting Vertical positions
			var parent_offset = $parent.offset();
			var popover_height = parseInt(parent_offset.top + target_elem.height());
			if(popover_height > parent_dimensions.height){
				var diff_height = parseInt(parent_offset.top - target_elem.height());
				var height_offset = parseInt(target_dimensions.top - (diff_height - 10));
				var target_nw_height = height_offset+'px';
				if(target_nw_height.search("-") == -1){
					target_nw_height = '-'+height_offset+'px';
				}
				
				if(parent_offset.top < target_dimensions.top && parent_dimensions.height > target_elem.height()){
					target_elem.css('top',target_nw_height);
				}
			}
	  }

      return false;
    }
  };
  
  function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length === 1 ? '0' + hex : hex;
  }

  function rgbToHex(r, g, b) {
    return '#' + componentToHex(r) + componentToHex(g) + componentToHex(b);
  }
      
  function clearMenus() {
    var $parent;

    $(toggle).each(function (e) {
      $parent = getParent($(this));

      if (!$parent.hasClass('open')) {
        return true;
      }

      $parent.trigger(e = $.Event('hide.bfhcolorpicker'));

      if (e.isDefaultPrevented()) {
        return true;
      }

      $parent
        .removeClass('open')
        .trigger('hidden.bfhcolorpicker');
    });
  }

  function getParent($this) {
    return $this.closest('.bfh-colorpicker');
  }
  
  
  /* COLORPICKER PLUGIN DEFINITION
   * ========================== */

  var old = $.fn.bfhcolorpicker;

  $.fn.bfhcolorpicker = function (option) {
    return this.each(function () {
      var $this,
          data,
          options;

      $this = $(this);
      data = $this.data('bfhcolorpicker');
      options = typeof option === 'object' && option;
      this.type = 'bfhcolorpicker';

      if (!data) {
        $this.data('bfhcolorpicker', (data = new BFHColorPicker(this, options)));
      }
      if (typeof option === 'string') {
        data[option].call($this);
      }
    });
  };

  $.fn.bfhcolorpicker.Constructor = BFHColorPicker;

  $.fn.bfhcolorpicker.defaults = {
    align: 'left',
    input: 'form-control',
    placeholder: '',
    name: '',
    color: '#000000',
    close: true
  };
  
  
  /* COLORPICKER NO CONFLICT
   * ========================== */

  $.fn.bfhcolorpicker.noConflict = function () {
    $.fn.bfhcolorpicker = old;
    return this;
  };
  
  
  /* COLORPICKER VALHOOKS
   * ========================== */

  var origHook;
  if ($.valHooks.div){
    origHook = $.valHooks.div;
  }
  $.valHooks.div = {
    get: function(el) {
      if ($(el).hasClass('bfh-colorpicker')) {
        return $(el).find('input[type="text"]').val();
      } else if (origHook) {
        return origHook.get(el);
      }
    },
    set: function(el, val) {
      if ($(el).hasClass('bfh-colorpicker')) {
        $(el).find('.bfh-colorpicker-icon').css('background-color', val);
        $(el).find('input[type="text"]').val(val);
      } else if (origHook) {
        return origHook.set(el,val);
      }
    }
  };
  
  
  /* COLORPICKER DATA-API
   * ============== */

  $(document).ready( function () {
    $('div.bfh-colorpicker').each(function () {
      var $colorpicker;

      $colorpicker = $(this);
	  $colorpicker.bfhcolorpicker($colorpicker.data());
	});
  });
  
  
  /* APPLY TO STANDARD COLORPICKER ELEMENTS
   * =================================== */

  $(document)
    .on('click.bfhcolorpicker.data-api', clearMenus);

}(window.jQuery);/*!
 * Dropdownhover v1.0.0 (http://bs-dropdownhover.kybarg.com)
 */
+function(o){"use strict";function t(t){return this.each(function(){var e=o(this),r=e.data("bs.dropdownhover"),i=e.data();void 0!==e.data("animations")&&null!==e.data("animations")&&(i.animations=o.isArray(i.animations)?i.animations:i.animations.split(" "));var s=o.extend({},n.DEFAULTS,i,"object"==typeof t&&t);r||e.data("bs.dropdownhover",r=new n(this,s))})}var n=function(t,n){this.options=n,this.$element=o(t);var e=this;this.dropdowns=this.$element.hasClass("dropdown-toggle")?this.$element.parent().find(".dropdown-menu").parent(".dropdown"):this.$element.find(".dropdown"),this.dropdowns.each(function(){o(this).on("mouseenter.bs.dropdownhover",function(t){e.show(o(this).children("a, button"))})}),this.dropdowns.each(function(){o(this).on("mouseleave.bs.dropdownhover",function(t){e.hide(o(this).children("a, button"))})})};n.TRANSITION_DURATION=300,n.DELAY=150,n.TIMEOUT,n.DEFAULTS={animations:["fadeIn","fadeInDown","fadeInRight","fadeInUp","fadeInLeft"]},n.prototype.show=function(t){var e=o(t);window.clearTimeout(n.TIMEOUT),o(".dropdown").not(e.parents()).each(function(){o(this).removeClass("open")});var r=this.options.animations[0];if(!e.is(".disabled, :disabled")){var i=e.parent(),s=i.hasClass("open");if(!s){var d=e.next(".dropdown-menu");i.addClass("open");var a=this.position(d);r="top"==a?this.options.animations[2]:"right"==a?this.options.animations[3]:"left"==a?this.options.animations[1]:this.options.animations[0],d.addClass("animated "+r);var h=o.support.transition&&d.hasClass("animated");h?d.one("bsTransitionEnd",function(){d.removeClass("animated "+r)}).emulateTransitionEnd(n.TRANSITION_DURATION):d.removeClass("animated "+r)}return!1}},n.prototype.hide=function(t){var e=o(t),r=e.parent();n.TIMEOUT=window.setTimeout(function(){r.removeClass("open")},n.DELAY)},n.prototype.position=function(t){var n=o(window);t.css({bottom:"",left:"",top:"",right:""}).removeClass("dropdownhover-top");var e={top:n.scrollTop(),left:n.scrollLeft()};e.right=e.left+n.width(),e.bottom=e.top+n.height();var r=t.offset();r.right=r.left+t.outerWidth(),r.bottom=r.top+t.outerHeight();var i=t.position();i.right=r.left+t.outerWidth(),i.bottom=r.top+t.outerHeight();var s="",d=t.parents(".dropdown-menu").length;if(d)i.left<0?(s="left",t.removeClass("dropdownhover-right").addClass("dropdownhover-left")):(s="right",t.addClass("dropdownhover-right").removeClass("dropdownhover-left")),r.left<e.left?(s="right",t.css({left:"100%",right:"auto"}).addClass("dropdownhover-right").removeClass("dropdownhover-left")):r.right>e.right&&(s="left",t.css({left:"auto",right:"100%"}).removeClass("dropdownhover-right").addClass("dropdownhover-left")),r.bottom>e.bottom?t.css({bottom:"auto",top:-(r.bottom-e.bottom)}):r.top<e.top&&t.css({bottom:-(e.top-r.top),top:"auto"});else{var a=t.parent(".dropdown"),h=a.offset();h.right=h.left+a.outerWidth(),h.bottom=h.top+a.outerHeight(),r.right>e.right&&t.css({left:-(r.right-e.right),right:"auto"}),r.bottom>e.bottom&&h.top-e.top>e.bottom-h.bottom||t.position().top<0?(s="top",t.css({bottom:"100%",top:"auto"}).addClass("dropdownhover-top").removeClass("dropdownhover-bottom")):(s="bottom",t.addClass("dropdownhover-bottom"))}return s};var e=o.fn.dropdownhover;o.fn.dropdownhover=t,o.fn.dropdownhover.Constructor=n,o.fn.dropdownhover.noConflict=function(){return o.fn.dropdownhover=e,this};var r;o(document).ready(function(){o(window).width()>=768&&o('[data-hover="dropdown"]').each(function(){var n=o(this);t.call(n,n.data())})}),o(window).on("resize",function(){clearTimeout(r),r=setTimeout(function(){o(window).width()>=768?o('[data-hover="dropdown"]').each(function(){var n=o(this);t.call(n,n.data())}):o('[data-hover="dropdown"]').each(function(){o(this).removeData("bs.dropdownhover"),o(this).hasClass("dropdown-toggle")?o(this).parent(".dropdown").find(".dropdown").andSelf().off("mouseenter.bs.dropdownhover mouseleave.bs.dropdownhover"):o(this).find(".dropdown").off("mouseenter.bs.dropdownhover mouseleave.bs.dropdownhover")})},200)})}(jQuery);
/* == jquery mousewheel plugin == Version: 3.1.12, License: MIT License (MIT) */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});
/* == malihu jquery custom scrollbar plugin == Version: 3.0.8, License: MIT License (MIT) */
!function(e){"undefined"!=typeof module&&module.exports?module.exports=e:e(jQuery,window,document)}(function(e){!function(t){var o="function"==typeof define&&define.amd,a="undefined"!=typeof module&&module.exports,n="https:"==document.location.protocol?"https:":"http:",i="cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.12/jquery.mousewheel.min.js";o||(a?require("jquery-mousewheel")(e):e.event.special.mousewheel||e("head").append(decodeURI("%3Cscript src="+n+"//"+i+"%3E%3C/script%3E"))),t()}(function(){var t,o="mCustomScrollbar",a="mCS",n=".mCustomScrollbar",i={setTop:0,setLeft:0,axis:"y",scrollbarPosition:"inside",scrollInertia:950,autoDraggerLength:!0,alwaysShowScrollbar:0,snapOffset:0,mouseWheel:{enable:!0,scrollAmount:"auto",axis:"y",deltaFactor:"auto",disableOver:["select","option","keygen","datalist","textarea"]},scrollButtons:{scrollType:"stepless",scrollAmount:"auto"},keyboard:{enable:!0,scrollType:"stepless",scrollAmount:"auto"},contentTouchScroll:25,advanced:{autoScrollOnFocus:"input,textarea,select,button,datalist,keygen,a[tabindex],area,object,[contenteditable='true']",updateOnContentResize:!0,updateOnImageLoad:!0},theme:"light",callbacks:{onTotalScrollOffset:0,onTotalScrollBackOffset:0,alwaysTriggerOffsets:!0}},r=0,l={},s=window.attachEvent&&!window.addEventListener?1:0,c=!1,d=["mCSB_dragger_onDrag","mCSB_scrollTools_onDrag","mCS_img_loaded","mCS_disabled","mCS_destroyed","mCS_no_scrollbar","mCS-autoHide","mCS-dir-rtl","mCS_no_scrollbar_y","mCS_no_scrollbar_x","mCS_y_hidden","mCS_x_hidden","mCSB_draggerContainer","mCSB_buttonUp","mCSB_buttonDown","mCSB_buttonLeft","mCSB_buttonRight"],u={init:function(t){var t=e.extend(!0,{},i,t),o=f.call(this);if(t.live){var s=t.liveSelector||this.selector||n,c=e(s);if("off"===t.live)return void m(s);l[s]=setTimeout(function(){c.mCustomScrollbar(t),"once"===t.live&&c.length&&m(s)},500)}else m(s);return t.setWidth=t.set_width?t.set_width:t.setWidth,t.setHeight=t.set_height?t.set_height:t.setHeight,t.axis=t.horizontalScroll?"x":p(t.axis),t.scrollInertia=t.scrollInertia>0&&t.scrollInertia<17?17:t.scrollInertia,"object"!=typeof t.mouseWheel&&1==t.mouseWheel&&(t.mouseWheel={enable:!0,scrollAmount:"auto",axis:"y",preventDefault:!1,deltaFactor:"auto",normalizeDelta:!1,invert:!1}),t.mouseWheel.scrollAmount=t.mouseWheelPixels?t.mouseWheelPixels:t.mouseWheel.scrollAmount,t.mouseWheel.normalizeDelta=t.advanced.normalizeMouseWheelDelta?t.advanced.normalizeMouseWheelDelta:t.mouseWheel.normalizeDelta,t.scrollButtons.scrollType=g(t.scrollButtons.scrollType),h(t),e(o).each(function(){var o=e(this);if(!o.data(a)){o.data(a,{idx:++r,opt:t,scrollRatio:{y:null,x:null},overflowed:null,contentReset:{y:null,x:null},bindEvents:!1,tweenRunning:!1,sequential:{},langDir:o.css("direction"),cbOffsets:null,trigger:null});var n=o.data(a),i=n.opt,l=o.data("mcs-axis"),s=o.data("mcs-scrollbar-position"),c=o.data("mcs-theme");l&&(i.axis=l),s&&(i.scrollbarPosition=s),c&&(i.theme=c,h(i)),v.call(this),e("#mCSB_"+n.idx+"_container img:not(."+d[2]+")").addClass(d[2]),u.update.call(null,o)}})},update:function(t,o){var n=t||f.call(this);return e(n).each(function(){var t=e(this);if(t.data(a)){var n=t.data(a),i=n.opt,r=e("#mCSB_"+n.idx+"_container"),l=[e("#mCSB_"+n.idx+"_dragger_vertical"),e("#mCSB_"+n.idx+"_dragger_horizontal")];if(!r.length)return;n.tweenRunning&&V(t),t.hasClass(d[3])&&t.removeClass(d[3]),t.hasClass(d[4])&&t.removeClass(d[4]),S.call(this),_.call(this),"y"===i.axis||i.advanced.autoExpandHorizontalScroll||r.css("width",x(r.children())),n.overflowed=B.call(this),O.call(this),i.autoDraggerLength&&b.call(this),C.call(this),k.call(this);var s=[Math.abs(r[0].offsetTop),Math.abs(r[0].offsetLeft)];"x"!==i.axis&&(n.overflowed[0]?l[0].height()>l[0].parent().height()?T.call(this):(Q(t,s[0].toString(),{dir:"y",dur:0,overwrite:"none"}),n.contentReset.y=null):(T.call(this),"y"===i.axis?M.call(this):"yx"===i.axis&&n.overflowed[1]&&Q(t,s[1].toString(),{dir:"x",dur:0,overwrite:"none"}))),"y"!==i.axis&&(n.overflowed[1]?l[1].width()>l[1].parent().width()?T.call(this):(Q(t,s[1].toString(),{dir:"x",dur:0,overwrite:"none"}),n.contentReset.x=null):(T.call(this),"x"===i.axis?M.call(this):"yx"===i.axis&&n.overflowed[0]&&Q(t,s[0].toString(),{dir:"y",dur:0,overwrite:"none"}))),o&&n&&(2===o&&i.callbacks.onImageLoad&&"function"==typeof i.callbacks.onImageLoad?i.callbacks.onImageLoad.call(this):3===o&&i.callbacks.onSelectorChange&&"function"==typeof i.callbacks.onSelectorChange?i.callbacks.onSelectorChange.call(this):i.callbacks.onUpdate&&"function"==typeof i.callbacks.onUpdate&&i.callbacks.onUpdate.call(this)),X.call(this)}})},scrollTo:function(t,o){if("undefined"!=typeof t&&null!=t){var n=f.call(this);return e(n).each(function(){var n=e(this);if(n.data(a)){var i=n.data(a),r=i.opt,l={trigger:"external",scrollInertia:r.scrollInertia,scrollEasing:"mcsEaseInOut",moveDragger:!1,timeout:60,callbacks:!0,onStart:!0,onUpdate:!0,onComplete:!0},s=e.extend(!0,{},l,o),c=Y.call(this,t),d=s.scrollInertia>0&&s.scrollInertia<17?17:s.scrollInertia;c[0]=j.call(this,c[0],"y"),c[1]=j.call(this,c[1],"x"),s.moveDragger&&(c[0]*=i.scrollRatio.y,c[1]*=i.scrollRatio.x),s.dur=d,setTimeout(function(){null!==c[0]&&"undefined"!=typeof c[0]&&"x"!==r.axis&&i.overflowed[0]&&(s.dir="y",s.overwrite="all",Q(n,c[0].toString(),s)),null!==c[1]&&"undefined"!=typeof c[1]&&"y"!==r.axis&&i.overflowed[1]&&(s.dir="x",s.overwrite="none",Q(n,c[1].toString(),s))},s.timeout)}})}},stop:function(){var t=f.call(this);return e(t).each(function(){var t=e(this);t.data(a)&&V(t)})},disable:function(t){var o=f.call(this);return e(o).each(function(){var o=e(this);if(o.data(a)){{o.data(a)}X.call(this,"remove"),M.call(this),t&&T.call(this),O.call(this,!0),o.addClass(d[3])}})},destroy:function(){var t=f.call(this);return e(t).each(function(){var n=e(this);if(n.data(a)){var i=n.data(a),r=i.opt,l=e("#mCSB_"+i.idx),s=e("#mCSB_"+i.idx+"_container"),c=e(".mCSB_"+i.idx+"_scrollbar");r.live&&m(r.liveSelector||e(t).selector),X.call(this,"remove"),M.call(this),T.call(this),n.removeData(a),Z(this,"mcs"),c.remove(),s.find("img."+d[2]).removeClass(d[2]),l.replaceWith(s.contents()),n.removeClass(o+" _"+a+"_"+i.idx+" "+d[6]+" "+d[7]+" "+d[5]+" "+d[3]).addClass(d[4])}})}},f=function(){return"object"!=typeof e(this)||e(this).length<1?n:this},h=function(t){var o=["rounded","rounded-dark","rounded-dots","rounded-dots-dark"],a=["rounded-dots","rounded-dots-dark","3d","3d-dark","3d-thick","3d-thick-dark","inset","inset-dark","inset-2","inset-2-dark","inset-3","inset-3-dark"],n=["minimal","minimal-dark"],i=["minimal","minimal-dark"],r=["minimal","minimal-dark"];t.autoDraggerLength=e.inArray(t.theme,o)>-1?!1:t.autoDraggerLength,t.autoExpandScrollbar=e.inArray(t.theme,a)>-1?!1:t.autoExpandScrollbar,t.scrollButtons.enable=e.inArray(t.theme,n)>-1?!1:t.scrollButtons.enable,t.autoHideScrollbar=e.inArray(t.theme,i)>-1?!0:t.autoHideScrollbar,t.scrollbarPosition=e.inArray(t.theme,r)>-1?"outside":t.scrollbarPosition},m=function(e){l[e]&&(clearTimeout(l[e]),Z(l,e))},p=function(e){return"yx"===e||"xy"===e||"auto"===e?"yx":"x"===e||"horizontal"===e?"x":"y"},g=function(e){return"stepped"===e||"pixels"===e||"step"===e||"click"===e?"stepped":"stepless"},v=function(){var t=e(this),n=t.data(a),i=n.opt,r=i.autoExpandScrollbar?" "+d[1]+"_expand":"",l=["<div id='mCSB_"+n.idx+"_scrollbar_vertical' class='mCSB_scrollTools mCSB_"+n.idx+"_scrollbar mCS-"+i.theme+" mCSB_scrollTools_vertical"+r+"'><div class='"+d[12]+"'><div id='mCSB_"+n.idx+"_dragger_vertical' class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' /></div><div class='mCSB_draggerRail' /></div></div>","<div id='mCSB_"+n.idx+"_scrollbar_horizontal' class='mCSB_scrollTools mCSB_"+n.idx+"_scrollbar mCS-"+i.theme+" mCSB_scrollTools_horizontal"+r+"'><div class='"+d[12]+"'><div id='mCSB_"+n.idx+"_dragger_horizontal' class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' /></div><div class='mCSB_draggerRail' /></div></div>"],s="yx"===i.axis?"mCSB_vertical_horizontal":"x"===i.axis?"mCSB_horizontal":"mCSB_vertical",c="yx"===i.axis?l[0]+l[1]:"x"===i.axis?l[1]:l[0],u="yx"===i.axis?"<div id='mCSB_"+n.idx+"_container_wrapper' class='mCSB_container_wrapper' />":"",f=i.autoHideScrollbar?" "+d[6]:"",h="x"!==i.axis&&"rtl"===n.langDir?" "+d[7]:"";i.setWidth&&t.css("width",i.setWidth),i.setHeight&&t.css("height",i.setHeight),i.setLeft="y"!==i.axis&&"rtl"===n.langDir?"989999px":i.setLeft,t.addClass(o+" _"+a+"_"+n.idx+f+h).wrapInner("<div id='mCSB_"+n.idx+"' class='mCustomScrollBox mCS-"+i.theme+" "+s+"'><div id='mCSB_"+n.idx+"_container' class='mCSB_container' style='position:relative; top:"+i.setTop+"; left:"+i.setLeft+";' dir="+n.langDir+" /></div>");var m=e("#mCSB_"+n.idx),p=e("#mCSB_"+n.idx+"_container");"y"===i.axis||i.advanced.autoExpandHorizontalScroll||p.css("width",x(p.children())),"outside"===i.scrollbarPosition?("static"===t.css("position")&&t.css("position","relative"),t.css("overflow","visible"),m.addClass("mCSB_outside").after(c)):(m.addClass("mCSB_inside").append(c),p.wrap(u)),w.call(this);var g=[e("#mCSB_"+n.idx+"_dragger_vertical"),e("#mCSB_"+n.idx+"_dragger_horizontal")];g[0].css("min-height",g[0].height()),g[1].css("min-width",g[1].width())},x=function(t){return Math.max.apply(Math,t.map(function(){return e(this).outerWidth(!0)}).get())},_=function(){var t=e(this),o=t.data(a),n=o.opt,i=e("#mCSB_"+o.idx+"_container");n.advanced.autoExpandHorizontalScroll&&"y"!==n.axis&&i.css({position:"absolute",width:"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({width:Math.ceil(i[0].getBoundingClientRect().right+.4)-Math.floor(i[0].getBoundingClientRect().left),position:"relative"}).unwrap()},w=function(){var t=e(this),o=t.data(a),n=o.opt,i=e(".mCSB_"+o.idx+"_scrollbar:first"),r=tt(n.scrollButtons.tabindex)?"tabindex='"+n.scrollButtons.tabindex+"'":"",l=["<a href='#' class='"+d[13]+"' oncontextmenu='return false;' "+r+" />","<a href='#' class='"+d[14]+"' oncontextmenu='return false;' "+r+" />","<a href='#' class='"+d[15]+"' oncontextmenu='return false;' "+r+" />","<a href='#' class='"+d[16]+"' oncontextmenu='return false;' "+r+" />"],s=["x"===n.axis?l[2]:l[0],"x"===n.axis?l[3]:l[1],l[2],l[3]];n.scrollButtons.enable&&i.prepend(s[0]).append(s[1]).next(".mCSB_scrollTools").prepend(s[2]).append(s[3])},S=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=t.css("max-height")||"none",r=-1!==i.indexOf("%"),l=t.css("box-sizing");if("none"!==i){var s=r?t.parent().height()*parseInt(i)/100:parseInt(i);"border-box"===l&&(s-=t.innerHeight()-t.height()+(t.outerHeight()-t.innerHeight())),n.css("max-height",Math.round(s))}},b=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=e("#mCSB_"+o.idx+"_container"),r=[e("#mCSB_"+o.idx+"_dragger_vertical"),e("#mCSB_"+o.idx+"_dragger_horizontal")],l=[n.height()/i.outerHeight(!1),n.width()/i.outerWidth(!1)],c=[parseInt(r[0].css("min-height")),Math.round(l[0]*r[0].parent().height()),parseInt(r[1].css("min-width")),Math.round(l[1]*r[1].parent().width())],d=s&&c[1]<c[0]?c[0]:c[1],u=s&&c[3]<c[2]?c[2]:c[3];r[0].css({height:d,"max-height":r[0].parent().height()-10}).find(".mCSB_dragger_bar").css({"line-height":c[0]+"px"}),r[1].css({width:u,"max-width":r[1].parent().width()-10})},C=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=e("#mCSB_"+o.idx+"_container"),r=[e("#mCSB_"+o.idx+"_dragger_vertical"),e("#mCSB_"+o.idx+"_dragger_horizontal")],l=[i.outerHeight(!1)-n.height(),i.outerWidth(!1)-n.width()],s=[l[0]/(r[0].parent().height()-r[0].height()),l[1]/(r[1].parent().width()-r[1].width())];o.scrollRatio={y:s[0],x:s[1]}},y=function(e,t,o){var a=o?d[0]+"_expanded":"",n=e.closest(".mCSB_scrollTools");"active"===t?(e.toggleClass(d[0]+" "+a),n.toggleClass(d[1]),e[0]._draggable=e[0]._draggable?0:1):e[0]._draggable||("hide"===t?(e.removeClass(d[0]),n.removeClass(d[1])):(e.addClass(d[0]),n.addClass(d[1])))},B=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=e("#mCSB_"+o.idx+"_container"),r=null==o.overflowed?i.height():i.outerHeight(!1),l=null==o.overflowed?i.width():i.outerWidth(!1);return[r>n.height(),l>n.width()]},T=function(){var t=e(this),o=t.data(a),n=o.opt,i=e("#mCSB_"+o.idx),r=e("#mCSB_"+o.idx+"_container"),l=[e("#mCSB_"+o.idx+"_dragger_vertical"),e("#mCSB_"+o.idx+"_dragger_horizontal")];if(V(t),("x"!==n.axis&&!o.overflowed[0]||"y"===n.axis&&o.overflowed[0])&&(l[0].add(r).css("top",0),Q(t,"_resetY")),"y"!==n.axis&&!o.overflowed[1]||"x"===n.axis&&o.overflowed[1]){var s=dx=0;"rtl"===o.langDir&&(s=i.width()-r.outerWidth(!1),dx=Math.abs(s/o.scrollRatio.x)),r.css("left",s),l[1].css("left",dx),Q(t,"_resetX")}},k=function(){function t(){r=setTimeout(function(){e.event.special.mousewheel?(clearTimeout(r),W.call(o[0])):t()},100)}var o=e(this),n=o.data(a),i=n.opt;if(!n.bindEvents){if(R.call(this),i.contentTouchScroll&&E.call(this),D.call(this),i.mouseWheel.enable){var r;t()}P.call(this),H.call(this),i.advanced.autoScrollOnFocus&&z.call(this),i.scrollButtons.enable&&U.call(this),i.keyboard.enable&&q.call(this),n.bindEvents=!0}},M=function(){var t=e(this),o=t.data(a),n=o.opt,i=a+"_"+o.idx,r=".mCSB_"+o.idx+"_scrollbar",l=e("#mCSB_"+o.idx+",#mCSB_"+o.idx+"_container,#mCSB_"+o.idx+"_container_wrapper,"+r+" ."+d[12]+",#mCSB_"+o.idx+"_dragger_vertical,#mCSB_"+o.idx+"_dragger_horizontal,"+r+">a"),s=e("#mCSB_"+o.idx+"_container");n.advanced.releaseDraggableSelectors&&l.add(e(n.advanced.releaseDraggableSelectors)),o.bindEvents&&(e(document).unbind("."+i),l.each(function(){e(this).unbind("."+i)}),clearTimeout(t[0]._focusTimeout),Z(t[0],"_focusTimeout"),clearTimeout(o.sequential.step),Z(o.sequential,"step"),clearTimeout(s[0].onCompleteTimeout),Z(s[0],"onCompleteTimeout"),o.bindEvents=!1)},O=function(t){var o=e(this),n=o.data(a),i=n.opt,r=e("#mCSB_"+n.idx+"_container_wrapper"),l=r.length?r:e("#mCSB_"+n.idx+"_container"),s=[e("#mCSB_"+n.idx+"_scrollbar_vertical"),e("#mCSB_"+n.idx+"_scrollbar_horizontal")],c=[s[0].find(".mCSB_dragger"),s[1].find(".mCSB_dragger")];"x"!==i.axis&&(n.overflowed[0]&&!t?(s[0].add(c[0]).add(s[0].children("a")).css("display","block"),l.removeClass(d[8]+" "+d[10])):(i.alwaysShowScrollbar?(2!==i.alwaysShowScrollbar&&c[0].css("display","none"),l.removeClass(d[10])):(s[0].css("display","none"),l.addClass(d[10])),l.addClass(d[8]))),"y"!==i.axis&&(n.overflowed[1]&&!t?(s[1].add(c[1]).add(s[1].children("a")).css("display","block"),l.removeClass(d[9]+" "+d[11])):(i.alwaysShowScrollbar?(2!==i.alwaysShowScrollbar&&c[1].css("display","none"),l.removeClass(d[11])):(s[1].css("display","none"),l.addClass(d[11])),l.addClass(d[9]))),n.overflowed[0]||n.overflowed[1]?o.removeClass(d[5]):o.addClass(d[5])},I=function(e){var t=e.type;switch(t){case"pointerdown":case"MSPointerDown":case"pointermove":case"MSPointerMove":case"pointerup":case"MSPointerUp":return e.target.ownerDocument!==document?[e.originalEvent.screenY,e.originalEvent.screenX,!1]:[e.originalEvent.pageY,e.originalEvent.pageX,!1];case"touchstart":case"touchmove":case"touchend":var o=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0],a=e.originalEvent.touches.length||e.originalEvent.changedTouches.length;return e.target.ownerDocument!==document?[o.screenY,o.screenX,a>1]:[o.pageY,o.pageX,a>1];default:return[e.pageY,e.pageX,!1]}},R=function(){function t(e){var t=m.find("iframe");if(t.length){var o=e?"auto":"none";t.css("pointer-events",o)}}function o(e,t,o,a){if(m[0].idleTimer=u.scrollInertia<233?250:0,n.attr("id")===h[1])var i="x",r=(n[0].offsetLeft-t+a)*d.scrollRatio.x;else var i="y",r=(n[0].offsetTop-e+o)*d.scrollRatio.y;Q(l,r.toString(),{dir:i,drag:!0})}var n,i,r,l=e(this),d=l.data(a),u=d.opt,f=a+"_"+d.idx,h=["mCSB_"+d.idx+"_dragger_vertical","mCSB_"+d.idx+"_dragger_horizontal"],m=e("#mCSB_"+d.idx+"_container"),p=e("#"+h[0]+",#"+h[1]),g=u.advanced.releaseDraggableSelectors?p.add(e(u.advanced.releaseDraggableSelectors)):p;p.bind("mousedown."+f+" touchstart."+f+" pointerdown."+f+" MSPointerDown."+f,function(o){if(o.stopImmediatePropagation(),o.preventDefault(),$(o)){c=!0,s&&(document.onselectstart=function(){return!1}),t(!1),V(l),n=e(this);var a=n.offset(),d=I(o)[0]-a.top,f=I(o)[1]-a.left,h=n.height()+a.top,m=n.width()+a.left;h>d&&d>0&&m>f&&f>0&&(i=d,r=f),y(n,"active",u.autoExpandScrollbar)}}).bind("touchmove."+f,function(e){e.stopImmediatePropagation(),e.preventDefault();var t=n.offset(),a=I(e)[0]-t.top,l=I(e)[1]-t.left;o(i,r,a,l)}),e(document).bind("mousemove."+f+" pointermove."+f+" MSPointerMove."+f,function(e){if(n){var t=n.offset(),a=I(e)[0]-t.top,l=I(e)[1]-t.left;if(i===a)return;o(i,r,a,l)}}).add(g).bind("mouseup."+f+" touchend."+f+" pointerup."+f+" MSPointerUp."+f,function(){n&&(y(n,"active",u.autoExpandScrollbar),n=null),c=!1,s&&(document.onselectstart=null),t(!0)})},E=function(){function o(e){if(!et(e)||c||I(e)[2])return void(t=0);t=1,S=0,b=0;var o=M.offset();d=I(e)[0]-o.top,u=I(e)[1]-o.left,A=[I(e)[0],I(e)[1]]}function n(e){if(et(e)&&!c&&!I(e)[2]&&(e.stopImmediatePropagation(),!b||S)){p=J();var t=k.offset(),o=I(e)[0]-t.top,a=I(e)[1]-t.left,n="mcsLinearOut";if(R.push(o),E.push(a),A[2]=Math.abs(I(e)[0]-A[0]),A[3]=Math.abs(I(e)[1]-A[1]),y.overflowed[0])var i=O[0].parent().height()-O[0].height(),r=d-o>0&&o-d>-(i*y.scrollRatio.y)&&(2*A[3]<A[2]||"yx"===B.axis);if(y.overflowed[1])var l=O[1].parent().width()-O[1].width(),f=u-a>0&&a-u>-(l*y.scrollRatio.x)&&(2*A[2]<A[3]||"yx"===B.axis);r||f?(e.preventDefault(),S=1):b=1,_="yx"===B.axis?[d-o,u-a]:"x"===B.axis?[null,u-a]:[d-o,null],M[0].idleTimer=250,y.overflowed[0]&&s(_[0],D,n,"y","all",!0),y.overflowed[1]&&s(_[1],D,n,"x",W,!0)}}function i(e){if(!et(e)||c||I(e)[2])return void(t=0);t=1,e.stopImmediatePropagation(),V(C),m=J();var o=k.offset();f=I(e)[0]-o.top,h=I(e)[1]-o.left,R=[],E=[]}function r(e){if(et(e)&&!c&&!I(e)[2]){e.stopImmediatePropagation(),S=0,b=0,g=J();var t=k.offset(),o=I(e)[0]-t.top,a=I(e)[1]-t.left;if(!(g-p>30)){x=1e3/(g-m);var n="mcsEaseOut",i=2.5>x,r=i?[R[R.length-2],E[E.length-2]]:[0,0];v=i?[o-r[0],a-r[1]]:[o-f,a-h];var d=[Math.abs(v[0]),Math.abs(v[1])];x=i?[Math.abs(v[0]/4),Math.abs(v[1]/4)]:[x,x];var u=[Math.abs(M[0].offsetTop)-v[0]*l(d[0]/x[0],x[0]),Math.abs(M[0].offsetLeft)-v[1]*l(d[1]/x[1],x[1])];_="yx"===B.axis?[u[0],u[1]]:"x"===B.axis?[null,u[1]]:[u[0],null],w=[4*d[0]+B.scrollInertia,4*d[1]+B.scrollInertia];var C=parseInt(B.contentTouchScroll)||0;_[0]=d[0]>C?_[0]:0,_[1]=d[1]>C?_[1]:0,y.overflowed[0]&&s(_[0],w[0],n,"y",W,!1),y.overflowed[1]&&s(_[1],w[1],n,"x",W,!1)}}}function l(e,t){var o=[1.5*t,2*t,t/1.5,t/2];return e>90?t>4?o[0]:o[3]:e>60?t>3?o[3]:o[2]:e>30?t>8?o[1]:t>6?o[0]:t>4?t:o[2]:t>8?t:o[3]}function s(e,t,o,a,n,i){e&&Q(C,e.toString(),{dur:t,scrollEasing:o,dir:a,overwrite:n,drag:i})}var d,u,f,h,m,p,g,v,x,_,w,S,b,C=e(this),y=C.data(a),B=y.opt,T=a+"_"+y.idx,k=e("#mCSB_"+y.idx),M=e("#mCSB_"+y.idx+"_container"),O=[e("#mCSB_"+y.idx+"_dragger_vertical"),e("#mCSB_"+y.idx+"_dragger_horizontal")],R=[],E=[],D=0,W="yx"===B.axis?"none":"all",A=[],P=M.find("iframe"),z=["touchstart."+T+" pointerdown."+T+" MSPointerDown."+T,"touchmove."+T+" pointermove."+T+" MSPointerMove."+T,"touchend."+T+" pointerup."+T+" MSPointerUp."+T];M.bind(z[0],function(e){o(e)}).bind(z[1],function(e){n(e)}),k.bind(z[0],function(e){i(e)}).bind(z[2],function(e){r(e)}),P.length&&P.each(function(){e(this).load(function(){L(this)&&e(this.contentDocument||this.contentWindow.document).bind(z[0],function(e){o(e),i(e)}).bind(z[1],function(e){n(e)}).bind(z[2],function(e){r(e)})})})},D=function(){function o(){return window.getSelection?window.getSelection().toString():document.selection&&"Control"!=document.selection.type?document.selection.createRange().text:0}function n(e,t,o){d.type=o&&i?"stepped":"stepless",d.scrollAmount=10,F(r,e,t,"mcsLinearOut",o?60:null)}var i,r=e(this),l=r.data(a),s=l.opt,d=l.sequential,u=a+"_"+l.idx,f=e("#mCSB_"+l.idx+"_container"),h=f.parent();f.bind("mousedown."+u,function(){t||i||(i=1,c=!0)}).add(document).bind("mousemove."+u,function(e){if(!t&&i&&o()){var a=f.offset(),r=I(e)[0]-a.top+f[0].offsetTop,c=I(e)[1]-a.left+f[0].offsetLeft;r>0&&r<h.height()&&c>0&&c<h.width()?d.step&&n("off",null,"stepped"):("x"!==s.axis&&l.overflowed[0]&&(0>r?n("on",38):r>h.height()&&n("on",40)),"y"!==s.axis&&l.overflowed[1]&&(0>c?n("on",37):c>h.width()&&n("on",39)))}}).bind("mouseup."+u,function(){t||(i&&(i=0,n("off",null)),c=!1)})},W=function(){function t(t,a){if(V(o),!A(o,t.target)){var r="auto"!==i.mouseWheel.deltaFactor?parseInt(i.mouseWheel.deltaFactor):s&&t.deltaFactor<100?100:t.deltaFactor||100;if("x"===i.axis||"x"===i.mouseWheel.axis)var d="x",u=[Math.round(r*n.scrollRatio.x),parseInt(i.mouseWheel.scrollAmount)],f="auto"!==i.mouseWheel.scrollAmount?u[1]:u[0]>=l.width()?.9*l.width():u[0],h=Math.abs(e("#mCSB_"+n.idx+"_container")[0].offsetLeft),m=c[1][0].offsetLeft,p=c[1].parent().width()-c[1].width(),g=t.deltaX||t.deltaY||a;else var d="y",u=[Math.round(r*n.scrollRatio.y),parseInt(i.mouseWheel.scrollAmount)],f="auto"!==i.mouseWheel.scrollAmount?u[1]:u[0]>=l.height()?.9*l.height():u[0],h=Math.abs(e("#mCSB_"+n.idx+"_container")[0].offsetTop),m=c[0][0].offsetTop,p=c[0].parent().height()-c[0].height(),g=t.deltaY||a;"y"===d&&!n.overflowed[0]||"x"===d&&!n.overflowed[1]||(i.mouseWheel.invert&&(g=-g),i.mouseWheel.normalizeDelta&&(g=0>g?-1:1),(g>0&&0!==m||0>g&&m!==p||i.mouseWheel.preventDefault)&&(t.stopImmediatePropagation(),t.preventDefault()),Q(o,(h-g*f).toString(),{dir:d}))}}var o=e(this),n=o.data(a),i=n.opt,r=a+"_"+n.idx,l=e("#mCSB_"+n.idx),c=[e("#mCSB_"+n.idx+"_dragger_vertical"),e("#mCSB_"+n.idx+"_dragger_horizontal")],d=e("#mCSB_"+n.idx+"_container").find("iframe");n&&(d.length&&d.each(function(){e(this).load(function(){L(this)&&e(this.contentDocument||this.contentWindow.document).bind("mousewheel."+r,function(e,o){t(e,o)})})}),l.bind("mousewheel."+r,function(e,o){t(e,o)}))},L=function(e){var t=null;try{var o=e.contentDocument||e.contentWindow.document;t=o.body.innerHTML}catch(a){}return null!==t},A=function(t,o){var n=o.nodeName.toLowerCase(),i=t.data(a).opt.mouseWheel.disableOver,r=["select","textarea"];return e.inArray(n,i)>-1&&!(e.inArray(n,r)>-1&&!e(o).is(":focus"))},P=function(){var t=e(this),o=t.data(a),n=a+"_"+o.idx,i=e("#mCSB_"+o.idx+"_container"),r=i.parent(),l=e(".mCSB_"+o.idx+"_scrollbar ."+d[12]);l.bind("touchstart."+n+" pointerdown."+n+" MSPointerDown."+n,function(){c=!0}).bind("touchend."+n+" pointerup."+n+" MSPointerUp."+n,function(){c=!1}).bind("click."+n,function(a){if(e(a.target).hasClass(d[12])||e(a.target).hasClass("mCSB_draggerRail")){V(t);var n=e(this),l=n.find(".mCSB_dragger");if(n.parent(".mCSB_scrollTools_horizontal").length>0){if(!o.overflowed[1])return;var s="x",c=a.pageX>l.offset().left?-1:1,u=Math.abs(i[0].offsetLeft)-.9*c*r.width()}else{if(!o.overflowed[0])return;var s="y",c=a.pageY>l.offset().top?-1:1,u=Math.abs(i[0].offsetTop)-.9*c*r.height()}Q(t,u.toString(),{dir:s,scrollEasing:"mcsEaseInOut"})}})},z=function(){var t=e(this),o=t.data(a),n=o.opt,i=a+"_"+o.idx,r=e("#mCSB_"+o.idx+"_container"),l=r.parent();r.bind("focusin."+i,function(){var o=e(document.activeElement),a=r.find(".mCustomScrollBox").length,i=0;o.is(n.advanced.autoScrollOnFocus)&&(V(t),clearTimeout(t[0]._focusTimeout),t[0]._focusTimer=a?(i+17)*a:0,t[0]._focusTimeout=setTimeout(function(){var e=[ot(o)[0],ot(o)[1]],a=[r[0].offsetTop,r[0].offsetLeft],s=[a[0]+e[0]>=0&&a[0]+e[0]<l.height()-o.outerHeight(!1),a[1]+e[1]>=0&&a[0]+e[1]<l.width()-o.outerWidth(!1)],c="yx"!==n.axis||s[0]||s[1]?"all":"none";"x"===n.axis||s[0]||Q(t,e[0].toString(),{dir:"y",scrollEasing:"mcsEaseInOut",overwrite:c,dur:i}),"y"===n.axis||s[1]||Q(t,e[1].toString(),{dir:"x",scrollEasing:"mcsEaseInOut",overwrite:c,dur:i})},t[0]._focusTimer))})},H=function(){var t=e(this),o=t.data(a),n=a+"_"+o.idx,i=e("#mCSB_"+o.idx+"_container").parent();i.bind("scroll."+n,function(){(0!==i.scrollTop()||0!==i.scrollLeft())&&e(".mCSB_"+o.idx+"_scrollbar").css("visibility","hidden")})},U=function(){var t=e(this),o=t.data(a),n=o.opt,i=o.sequential,r=a+"_"+o.idx,l=".mCSB_"+o.idx+"_scrollbar",s=e(l+">a");s.bind("mousedown."+r+" touchstart."+r+" pointerdown."+r+" MSPointerDown."+r+" mouseup."+r+" touchend."+r+" pointerup."+r+" MSPointerUp."+r+" mouseout."+r+" pointerout."+r+" MSPointerOut."+r+" click."+r,function(a){function r(e,o){i.scrollAmount=n.snapAmount||n.scrollButtons.scrollAmount,F(t,e,o)}if(a.preventDefault(),$(a)){var l=e(this).attr("class");switch(i.type=n.scrollButtons.scrollType,a.type){case"mousedown":case"touchstart":case"pointerdown":case"MSPointerDown":if("stepped"===i.type)return;c=!0,o.tweenRunning=!1,r("on",l);break;case"mouseup":case"touchend":case"pointerup":case"MSPointerUp":case"mouseout":case"pointerout":case"MSPointerOut":if("stepped"===i.type)return;c=!1,i.dir&&r("off",l);break;case"click":if("stepped"!==i.type||o.tweenRunning)return;r("on",l)}}})},q=function(){function t(t){function a(e,t){r.type=i.keyboard.scrollType,r.scrollAmount=i.snapAmount||i.keyboard.scrollAmount,"stepped"===r.type&&n.tweenRunning||F(o,e,t)}switch(t.type){case"blur":n.tweenRunning&&r.dir&&a("off",null);break;case"keydown":case"keyup":var l=t.keyCode?t.keyCode:t.which,s="on";if("x"!==i.axis&&(38===l||40===l)||"y"!==i.axis&&(37===l||39===l)){if((38===l||40===l)&&!n.overflowed[0]||(37===l||39===l)&&!n.overflowed[1])return;"keyup"===t.type&&(s="off"),e(document.activeElement).is(u)||(t.preventDefault(),t.stopImmediatePropagation(),a(s,l))}else if(33===l||34===l){if((n.overflowed[0]||n.overflowed[1])&&(t.preventDefault(),t.stopImmediatePropagation()),"keyup"===t.type){V(o);var f=34===l?-1:1;if("x"===i.axis||"yx"===i.axis&&n.overflowed[1]&&!n.overflowed[0])var h="x",m=Math.abs(c[0].offsetLeft)-.9*f*d.width();else var h="y",m=Math.abs(c[0].offsetTop)-.9*f*d.height();Q(o,m.toString(),{dir:h,scrollEasing:"mcsEaseInOut"})}}else if((35===l||36===l)&&!e(document.activeElement).is(u)&&((n.overflowed[0]||n.overflowed[1])&&(t.preventDefault(),t.stopImmediatePropagation()),"keyup"===t.type)){if("x"===i.axis||"yx"===i.axis&&n.overflowed[1]&&!n.overflowed[0])var h="x",m=35===l?Math.abs(d.width()-c.outerWidth(!1)):0;else var h="y",m=35===l?Math.abs(d.height()-c.outerHeight(!1)):0;Q(o,m.toString(),{dir:h,scrollEasing:"mcsEaseInOut"})}}}var o=e(this),n=o.data(a),i=n.opt,r=n.sequential,l=a+"_"+n.idx,s=e("#mCSB_"+n.idx),c=e("#mCSB_"+n.idx+"_container"),d=c.parent(),u="input,textarea,select,datalist,keygen,[contenteditable='true']",f=c.find("iframe"),h=["blur."+l+" keydown."+l+" keyup."+l];f.length&&f.each(function(){e(this).load(function(){L(this)&&e(this.contentDocument||this.contentWindow.document).bind(h[0],function(e){t(e)})})}),s.attr("tabindex","0").bind(h[0],function(e){t(e)})},F=function(t,o,n,i,r){function l(e){var o="stepped"!==f.type,a=r?r:e?o?p/1.5:g:1e3/60,n=e?o?7.5:40:2.5,s=[Math.abs(h[0].offsetTop),Math.abs(h[0].offsetLeft)],d=[c.scrollRatio.y>10?10:c.scrollRatio.y,c.scrollRatio.x>10?10:c.scrollRatio.x],u="x"===f.dir[0]?s[1]+f.dir[1]*d[1]*n:s[0]+f.dir[1]*d[0]*n,m="x"===f.dir[0]?s[1]+f.dir[1]*parseInt(f.scrollAmount):s[0]+f.dir[1]*parseInt(f.scrollAmount),v="auto"!==f.scrollAmount?m:u,x=i?i:e?o?"mcsLinearOut":"mcsEaseInOut":"mcsLinear",_=e?!0:!1;return e&&17>a&&(v="x"===f.dir[0]?s[1]:s[0]),Q(t,v.toString(),{dir:f.dir[0],scrollEasing:x,dur:a,onComplete:_}),e?void(f.dir=!1):(clearTimeout(f.step),void(f.step=setTimeout(function(){l()},a)))}function s(){clearTimeout(f.step),Z(f,"step"),V(t)}var c=t.data(a),u=c.opt,f=c.sequential,h=e("#mCSB_"+c.idx+"_container"),m="stepped"===f.type?!0:!1,p=u.scrollInertia<26?26:u.scrollInertia,g=u.scrollInertia<1?17:u.scrollInertia;switch(o){case"on":if(f.dir=[n===d[16]||n===d[15]||39===n||37===n?"x":"y",n===d[13]||n===d[15]||38===n||37===n?-1:1],V(t),tt(n)&&"stepped"===f.type)return;l(m);break;case"off":s(),(m||c.tweenRunning&&f.dir)&&l(!0)}},Y=function(t){var o=e(this).data(a).opt,n=[];return"function"==typeof t&&(t=t()),t instanceof Array?n=t.length>1?[t[0],t[1]]:"x"===o.axis?[null,t[0]]:[t[0],null]:(n[0]=t.y?t.y:t.x||"x"===o.axis?null:t,n[1]=t.x?t.x:t.y||"y"===o.axis?null:t),"function"==typeof n[0]&&(n[0]=n[0]()),"function"==typeof n[1]&&(n[1]=n[1]()),n},j=function(t,o){if(null!=t&&"undefined"!=typeof t){var n=e(this),i=n.data(a),r=i.opt,l=e("#mCSB_"+i.idx+"_container"),s=l.parent(),c=typeof t;o||(o="x"===r.axis?"x":"y");var d="x"===o?l.outerWidth(!1):l.outerHeight(!1),f="x"===o?l[0].offsetLeft:l[0].offsetTop,h="x"===o?"left":"top";switch(c){case"function":return t();case"object":var m=t.jquery?t:e(t);if(!m.length)return;return"x"===o?ot(m)[1]:ot(m)[0];case"string":case"number":if(tt(t))return Math.abs(t);if(-1!==t.indexOf("%"))return Math.abs(d*parseInt(t)/100);if(-1!==t.indexOf("-="))return Math.abs(f-parseInt(t.split("-=")[1]));if(-1!==t.indexOf("+=")){var p=f+parseInt(t.split("+=")[1]);return p>=0?0:Math.abs(p)}if(-1!==t.indexOf("px")&&tt(t.split("px")[0]))return Math.abs(t.split("px")[0]);if("top"===t||"left"===t)return 0;if("bottom"===t)return Math.abs(s.height()-l.outerHeight(!1));if("right"===t)return Math.abs(s.width()-l.outerWidth(!1));if("first"===t||"last"===t){var m=l.find(":"+t);return"x"===o?ot(m)[1]:ot(m)[0]}return e(t).length?"x"===o?ot(e(t))[1]:ot(e(t))[0]:(l.css(h,t),void u.update.call(null,n[0]))}}},X=function(t){function o(){clearTimeout(h[0].autoUpdate),h[0].autoUpdate=setTimeout(function(){return f.advanced.updateOnSelectorChange&&(m=r(),m!==w)?(l(3),void(w=m)):(f.advanced.updateOnContentResize&&(p=[h.outerHeight(!1),h.outerWidth(!1),v.height(),v.width(),_()[0],_()[1]],(p[0]!==S[0]||p[1]!==S[1]||p[2]!==S[2]||p[3]!==S[3]||p[4]!==S[4]||p[5]!==S[5])&&(l(p[0]!==S[0]||p[1]!==S[1]),S=p)),f.advanced.updateOnImageLoad&&(g=n(),g!==b&&(h.find("img").each(function(){i(this)}),b=g)),void((f.advanced.updateOnSelectorChange||f.advanced.updateOnContentResize||f.advanced.updateOnImageLoad)&&o()))},60)}function n(){var e=0;return f.advanced.updateOnImageLoad&&(e=h.find("img").length),e}function i(t){function o(e,t){return function(){return t.apply(e,arguments)}}function a(){this.onload=null,e(t).addClass(d[2]),l(2)}if(e(t).hasClass(d[2]))return void l();var n=new Image;n.onload=o(n,a),n.src=t.src}function r(){f.advanced.updateOnSelectorChange===!0&&(f.advanced.updateOnSelectorChange="*");var t=0,o=h.find(f.advanced.updateOnSelectorChange);return f.advanced.updateOnSelectorChange&&o.length>0&&o.each(function(){t+=e(this).height()+e(this).width()}),t}function l(e){clearTimeout(h[0].autoUpdate),u.update.call(null,s[0],e)}var s=e(this),c=s.data(a),f=c.opt,h=e("#mCSB_"+c.idx+"_container");if(t)return clearTimeout(h[0].autoUpdate),void Z(h[0],"autoUpdate");var m,p,g,v=h.parent(),x=[e("#mCSB_"+c.idx+"_scrollbar_vertical"),e("#mCSB_"+c.idx+"_scrollbar_horizontal")],_=function(){return[x[0].is(":visible")?x[0].outerHeight(!0):0,x[1].is(":visible")?x[1].outerWidth(!0):0]},w=r(),S=[h.outerHeight(!1),h.outerWidth(!1),v.height(),v.width(),_()[0],_()[1]],b=n();o()},N=function(e,t,o){return Math.round(e/t)*t-o},V=function(t){var o=t.data(a),n=e("#mCSB_"+o.idx+"_container,#mCSB_"+o.idx+"_container_wrapper,#mCSB_"+o.idx+"_dragger_vertical,#mCSB_"+o.idx+"_dragger_horizontal");n.each(function(){K.call(this)})},Q=function(t,o,n){function i(e){return s&&c.callbacks[e]&&"function"==typeof c.callbacks[e]}function r(){return[c.callbacks.alwaysTriggerOffsets||_>=w[0]+b,c.callbacks.alwaysTriggerOffsets||-C>=_]}function l(){var e=[h[0].offsetTop,h[0].offsetLeft],o=[v[0].offsetTop,v[0].offsetLeft],a=[h.outerHeight(!1),h.outerWidth(!1)],i=[f.height(),f.width()];t[0].mcs={content:h,top:e[0],left:e[1],draggerTop:o[0],draggerLeft:o[1],topPct:Math.round(100*Math.abs(e[0])/(Math.abs(a[0])-i[0])),leftPct:Math.round(100*Math.abs(e[1])/(Math.abs(a[1])-i[1])),direction:n.dir}}var s=t.data(a),c=s.opt,d={trigger:"internal",dir:"y",scrollEasing:"mcsEaseOut",drag:!1,dur:c.scrollInertia,overwrite:"all",callbacks:!0,onStart:!0,onUpdate:!0,onComplete:!0},n=e.extend(d,n),u=[n.dur,n.drag?0:n.dur],f=e("#mCSB_"+s.idx),h=e("#mCSB_"+s.idx+"_container"),m=h.parent(),p=c.callbacks.onTotalScrollOffset?Y.call(t,c.callbacks.onTotalScrollOffset):[0,0],g=c.callbacks.onTotalScrollBackOffset?Y.call(t,c.callbacks.onTotalScrollBackOffset):[0,0];
if(s.trigger=n.trigger,(0!==m.scrollTop()||0!==m.scrollLeft())&&(e(".mCSB_"+s.idx+"_scrollbar").css("visibility","visible"),m.scrollTop(0).scrollLeft(0)),"_resetY"!==o||s.contentReset.y||(i("onOverflowYNone")&&c.callbacks.onOverflowYNone.call(t[0]),s.contentReset.y=1),"_resetX"!==o||s.contentReset.x||(i("onOverflowXNone")&&c.callbacks.onOverflowXNone.call(t[0]),s.contentReset.x=1),"_resetY"!==o&&"_resetX"!==o){switch(!s.contentReset.y&&t[0].mcs||!s.overflowed[0]||(i("onOverflowY")&&c.callbacks.onOverflowY.call(t[0]),s.contentReset.x=null),!s.contentReset.x&&t[0].mcs||!s.overflowed[1]||(i("onOverflowX")&&c.callbacks.onOverflowX.call(t[0]),s.contentReset.x=null),c.snapAmount&&(o=N(o,c.snapAmount,c.snapOffset)),n.dir){case"x":var v=e("#mCSB_"+s.idx+"_dragger_horizontal"),x="left",_=h[0].offsetLeft,w=[f.width()-h.outerWidth(!1),v.parent().width()-v.width()],S=[o,0===o?0:o/s.scrollRatio.x],b=p[1],C=g[1],B=b>0?b/s.scrollRatio.x:0,T=C>0?C/s.scrollRatio.x:0;break;case"y":var v=e("#mCSB_"+s.idx+"_dragger_vertical"),x="top",_=h[0].offsetTop,w=[f.height()-h.outerHeight(!1),v.parent().height()-v.height()],S=[o,0===o?0:o/s.scrollRatio.y],b=p[0],C=g[0],B=b>0?b/s.scrollRatio.y:0,T=C>0?C/s.scrollRatio.y:0}S[1]<0||0===S[0]&&0===S[1]?S=[0,0]:S[1]>=w[1]?S=[w[0],w[1]]:S[0]=-S[0],t[0].mcs||(l(),i("onInit")&&c.callbacks.onInit.call(t[0])),clearTimeout(h[0].onCompleteTimeout),(s.tweenRunning||!(0===_&&S[0]>=0||_===w[0]&&S[0]<=w[0]))&&(G(v[0],x,Math.round(S[1]),u[1],n.scrollEasing),G(h[0],x,Math.round(S[0]),u[0],n.scrollEasing,n.overwrite,{onStart:function(){n.callbacks&&n.onStart&&!s.tweenRunning&&(i("onScrollStart")&&(l(),c.callbacks.onScrollStart.call(t[0])),s.tweenRunning=!0,y(v),s.cbOffsets=r())},onUpdate:function(){n.callbacks&&n.onUpdate&&i("whileScrolling")&&(l(),c.callbacks.whileScrolling.call(t[0]))},onComplete:function(){if(n.callbacks&&n.onComplete){"yx"===c.axis&&clearTimeout(h[0].onCompleteTimeout);var e=h[0].idleTimer||0;h[0].onCompleteTimeout=setTimeout(function(){i("onScroll")&&(l(),c.callbacks.onScroll.call(t[0])),i("onTotalScroll")&&S[1]>=w[1]-B&&s.cbOffsets[0]&&(l(),c.callbacks.onTotalScroll.call(t[0])),i("onTotalScrollBack")&&S[1]<=T&&s.cbOffsets[1]&&(l(),c.callbacks.onTotalScrollBack.call(t[0])),s.tweenRunning=!1,h[0].idleTimer=0,y(v,"hide")},e)}}}))}},G=function(e,t,o,a,n,i,r){function l(){S.stop||(x||m.call(),x=J()-v,s(),x>=S.time&&(S.time=x>S.time?x+f-(x-S.time):x+f-1,S.time<x+1&&(S.time=x+1)),S.time<a?S.id=h(l):g.call())}function s(){a>0?(S.currVal=u(S.time,_,b,a,n),w[t]=Math.round(S.currVal)+"px"):w[t]=o+"px",p.call()}function c(){f=1e3/60,S.time=x+f,h=window.requestAnimationFrame?window.requestAnimationFrame:function(e){return s(),setTimeout(e,.01)},S.id=h(l)}function d(){null!=S.id&&(window.requestAnimationFrame?window.cancelAnimationFrame(S.id):clearTimeout(S.id),S.id=null)}function u(e,t,o,a,n){switch(n){case"linear":case"mcsLinear":return o*e/a+t;case"mcsLinearOut":return e/=a,e--,o*Math.sqrt(1-e*e)+t;case"easeInOutSmooth":return e/=a/2,1>e?o/2*e*e+t:(e--,-o/2*(e*(e-2)-1)+t);case"easeInOutStrong":return e/=a/2,1>e?o/2*Math.pow(2,10*(e-1))+t:(e--,o/2*(-Math.pow(2,-10*e)+2)+t);case"easeInOut":case"mcsEaseInOut":return e/=a/2,1>e?o/2*e*e*e+t:(e-=2,o/2*(e*e*e+2)+t);case"easeOutSmooth":return e/=a,e--,-o*(e*e*e*e-1)+t;case"easeOutStrong":return o*(-Math.pow(2,-10*e/a)+1)+t;case"easeOut":case"mcsEaseOut":default:var i=(e/=a)*e,r=i*e;return t+o*(.499999999999997*r*i+-2.5*i*i+5.5*r+-6.5*i+4*e)}}e._mTween||(e._mTween={top:{},left:{}});var f,h,r=r||{},m=r.onStart||function(){},p=r.onUpdate||function(){},g=r.onComplete||function(){},v=J(),x=0,_=e.offsetTop,w=e.style,S=e._mTween[t];"left"===t&&(_=e.offsetLeft);var b=o-_;S.stop=0,"none"!==i&&d(),c()},J=function(){return window.performance&&window.performance.now?window.performance.now():window.performance&&window.performance.webkitNow?window.performance.webkitNow():Date.now?Date.now():(new Date).getTime()},K=function(){var e=this;e._mTween||(e._mTween={top:{},left:{}});for(var t=["top","left"],o=0;o<t.length;o++){var a=t[o];e._mTween[a].id&&(window.requestAnimationFrame?window.cancelAnimationFrame(e._mTween[a].id):clearTimeout(e._mTween[a].id),e._mTween[a].id=null,e._mTween[a].stop=1)}},Z=function(e,t){try{delete e[t]}catch(o){e[t]=null}},$=function(e){return!(e.which&&1!==e.which)},et=function(e){var t=e.originalEvent.pointerType;return!(t&&"touch"!==t&&2!==t)},tt=function(e){return!isNaN(parseFloat(e))&&isFinite(e)},ot=function(e){var t=e.parents(".mCSB_container");return[e.offset().top-t.offset().top,e.offset().left-t.offset().left]};e.fn[o]=function(t){return u[t]?u[t].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof t&&t?void e.error("Method "+t+" does not exist"):u.init.apply(this,arguments)},e[o]=function(t){return u[t]?u[t].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof t&&t?void e.error("Method "+t+" does not exist"):u.init.apply(this,arguments)},e[o].defaults=i,e[o].methods=u,window[o]=!0,e(window).load(function(){e(n)[o](),e.extend(e.expr[":"],{mcsInView:e.expr[":"].mcsInView||function(t){var o,a,n=e(t),i=n.parents(".mCSB_container");if(i.length)return o=i.parent(),a=[i[0].offsetTop,i[0].offsetLeft],a[0]+ot(n)[0]>=0&&a[0]+ot(n)[0]<o.height()-n.outerHeight(!1)&&a[1]+ot(n)[1]>=0&&a[1]+ot(n)[1]<o.width()-n.outerWidth(!1)},mcsOverflow:e.expr[":"].mcsOverflow||function(t){var o=e(t).data(a);if(o)return o.overflowed[0]||o.overflowed[1]}})})})});/*!
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
/*!
 * jQuery Migrate - v1.2.1 - 2013-05-08
 * https://github.com/jquery/jquery-migrate
 * Copyright 2005, 2013 jQuery Foundation, Inc. and other contributors; Licensed MIT
 */
(function( jQuery, window, undefined ) {
// See http://bugs.jquery.com/ticket/13335
// "use strict";


var warnedAbout = {};

// List of warnings already given; public read only
jQuery.migrateWarnings = [];

// Set to true to prevent console output; migrateWarnings still maintained
// jQuery.migrateMute = false;

// Show a message on the console so devs know we're active
if ( !jQuery.migrateMute && window.console && window.console.log ) {
	window.console.log("JQMIGRATE: Logging is active");
}

// Set to false to disable traces that appear with warnings
if ( jQuery.migrateTrace === undefined ) {
	jQuery.migrateTrace = true;
}

// Forget any warnings we've already given; public
jQuery.migrateReset = function() {
	warnedAbout = {};
	jQuery.migrateWarnings.length = 0;
};

function migrateWarn( msg) {
	var console = window.console;
	if ( !warnedAbout[ msg ] ) {
		warnedAbout[ msg ] = true;
		jQuery.migrateWarnings.push( msg );
		if ( console && console.warn && !jQuery.migrateMute ) {
			console.warn( "JQMIGRATE: " + msg );
			if ( jQuery.migrateTrace && console.trace ) {
				console.trace();
			}
		}
	}
}

function migrateWarnProp( obj, prop, value, msg ) {
	if ( Object.defineProperty ) {
		// On ES5 browsers (non-oldIE), warn if the code tries to get prop;
		// allow property to be overwritten in case some other plugin wants it
		try {
			Object.defineProperty( obj, prop, {
				configurable: true,
				enumerable: true,
				get: function() {
					migrateWarn( msg );
					return value;
				},
				set: function( newValue ) {
					migrateWarn( msg );
					value = newValue;
				}
			});
			return;
		} catch( err ) {
			// IE8 is a dope about Object.defineProperty, can't warn there
		}
	}

	// Non-ES5 (or broken) browser; just set the property
	jQuery._definePropertyBroken = true;
	obj[ prop ] = value;
}

if ( document.compatMode === "BackCompat" ) {
	// jQuery has never supported or tested Quirks Mode
	migrateWarn( "jQuery is not compatible with Quirks Mode" );
}


var attrFn = jQuery( "<input/>", { size: 1 } ).attr("size") && jQuery.attrFn,
	oldAttr = jQuery.attr,
	valueAttrGet = jQuery.attrHooks.value && jQuery.attrHooks.value.get ||
		function() { return null; },
	valueAttrSet = jQuery.attrHooks.value && jQuery.attrHooks.value.set ||
		function() { return undefined; },
	rnoType = /^(?:input|button)$/i,
	rnoAttrNodeType = /^[238]$/,
	rboolean = /^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,
	ruseDefault = /^(?:checked|selected)$/i;

// jQuery.attrFn
migrateWarnProp( jQuery, "attrFn", attrFn || {}, "jQuery.attrFn is deprecated" );

jQuery.attr = function( elem, name, value, pass ) {
	var lowerName = name.toLowerCase(),
		nType = elem && elem.nodeType;

	if ( pass ) {
		// Since pass is used internally, we only warn for new jQuery
		// versions where there isn't a pass arg in the formal params
		if ( oldAttr.length < 4 ) {
			migrateWarn("jQuery.fn.attr( props, pass ) is deprecated");
		}
		if ( elem && !rnoAttrNodeType.test( nType ) &&
			(attrFn ? name in attrFn : jQuery.isFunction(jQuery.fn[name])) ) {
			return jQuery( elem )[ name ]( value );
		}
	}

	// Warn if user tries to set `type`, since it breaks on IE 6/7/8; by checking
	// for disconnected elements we don't warn on $( "<button>", { type: "button" } ).
	if ( name === "type" && value !== undefined && rnoType.test( elem.nodeName ) && elem.parentNode ) {
		migrateWarn("Can't change the 'type' of an input or button in IE 6/7/8");
	}

	// Restore boolHook for boolean property/attribute synchronization
	if ( !jQuery.attrHooks[ lowerName ] && rboolean.test( lowerName ) ) {
		jQuery.attrHooks[ lowerName ] = {
			get: function( elem, name ) {
				// Align boolean attributes with corresponding properties
				// Fall back to attribute presence where some booleans are not supported
				var attrNode,
					property = jQuery.prop( elem, name );
				return property === true || typeof property !== "boolean" &&
					( attrNode = elem.getAttributeNode(name) ) && attrNode.nodeValue !== false ?

					name.toLowerCase() :
					undefined;
			},
			set: function( elem, value, name ) {
				var propName;
				if ( value === false ) {
					// Remove boolean attributes when set to false
					jQuery.removeAttr( elem, name );
				} else {
					// value is true since we know at this point it's type boolean and not false
					// Set boolean attributes to the same name and set the DOM property
					propName = jQuery.propFix[ name ] || name;
					if ( propName in elem ) {
						// Only set the IDL specifically if it already exists on the element
						elem[ propName ] = true;
					}

					elem.setAttribute( name, name.toLowerCase() );
				}
				return name;
			}
		};

		// Warn only for attributes that can remain distinct from their properties post-1.9
		if ( ruseDefault.test( lowerName ) ) {
			migrateWarn( "jQuery.fn.attr('" + lowerName + "') may use property instead of attribute" );
		}
	}

	return oldAttr.call( jQuery, elem, name, value );
};

// attrHooks: value
jQuery.attrHooks.value = {
	get: function( elem, name ) {
		var nodeName = ( elem.nodeName || "" ).toLowerCase();
		if ( nodeName === "button" ) {
			return valueAttrGet.apply( this, arguments );
		}
		if ( nodeName !== "input" && nodeName !== "option" ) {
			migrateWarn("jQuery.fn.attr('value') no longer gets properties");
		}
		return name in elem ?
			elem.value :
			null;
	},
	set: function( elem, value ) {
		var nodeName = ( elem.nodeName || "" ).toLowerCase();
		if ( nodeName === "button" ) {
			return valueAttrSet.apply( this, arguments );
		}
		if ( nodeName !== "input" && nodeName !== "option" ) {
			migrateWarn("jQuery.fn.attr('value', val) no longer sets properties");
		}
		// Does not return so that setAttribute is also used
		elem.value = value;
	}
};


var matched, browser,
	oldInit = jQuery.fn.init,
	oldParseJSON = jQuery.parseJSON,
	// Note: XSS check is done below after string is trimmed
	rquickExpr = /^([^<]*)(<[\w\W]+>)([^>]*)$/;

// $(html) "looks like html" rule change
jQuery.fn.init = function( selector, context, rootjQuery ) {
	var match;

	if ( selector && typeof selector === "string" && !jQuery.isPlainObject( context ) &&
			(match = rquickExpr.exec( jQuery.trim( selector ) )) && match[ 0 ] ) {
		// This is an HTML string according to the "old" rules; is it still?
		if ( selector.charAt( 0 ) !== "<" ) {
			migrateWarn("$(html) HTML strings must start with '<' character");
		}
		if ( match[ 3 ] ) {
			migrateWarn("$(html) HTML text after last tag is ignored");
		}
		// Consistently reject any HTML-like string starting with a hash (#9521)
		// Note that this may break jQuery 1.6.x code that otherwise would work.
		if ( match[ 0 ].charAt( 0 ) === "#" ) {
			migrateWarn("HTML string cannot start with a '#' character");
			jQuery.error("JQMIGRATE: Invalid selector string (XSS)");
		}
		// Now process using loose rules; let pre-1.8 play too
		if ( context && context.context ) {
			// jQuery object as context; parseHTML expects a DOM object
			context = context.context;
		}
		if ( jQuery.parseHTML ) {
			return oldInit.call( this, jQuery.parseHTML( match[ 2 ], context, true ),
					context, rootjQuery );
		}
	}
	return oldInit.apply( this, arguments );
};
jQuery.fn.init.prototype = jQuery.fn;

// Let $.parseJSON(falsy_value) return null
jQuery.parseJSON = function( json ) {
	if ( !json && json !== null ) {
		migrateWarn("jQuery.parseJSON requires a valid JSON string");
		return null;
	}
	return oldParseJSON.apply( this, arguments );
};

jQuery.uaMatch = function( ua ) {
	ua = ua.toLowerCase();

	var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
		/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
		/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
		/(msie) ([\w.]+)/.exec( ua ) ||
		ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
		[];

	return {
		browser: match[ 1 ] || "",
		version: match[ 2 ] || "0"
	};
};

// Don't clobber any existing jQuery.browser in case it's different
if ( !jQuery.browser ) {
	matched = jQuery.uaMatch( navigator.userAgent );
	browser = {};

	if ( matched.browser ) {
		browser[ matched.browser ] = true;
		browser.version = matched.version;
	}

	// Chrome is Webkit, but Webkit is also Safari.
	if ( browser.chrome ) {
		browser.webkit = true;
	} else if ( browser.webkit ) {
		browser.safari = true;
	}

	jQuery.browser = browser;
}

// Warn if the code tries to get jQuery.browser
migrateWarnProp( jQuery, "browser", jQuery.browser, "jQuery.browser is deprecated" );

jQuery.sub = function() {
	function jQuerySub( selector, context ) {
		return new jQuerySub.fn.init( selector, context );
	}
	jQuery.extend( true, jQuerySub, this );
	jQuerySub.superclass = this;
	jQuerySub.fn = jQuerySub.prototype = this();
	jQuerySub.fn.constructor = jQuerySub;
	jQuerySub.sub = this.sub;
	jQuerySub.fn.init = function init( selector, context ) {
		if ( context && context instanceof jQuery && !(context instanceof jQuerySub) ) {
			context = jQuerySub( context );
		}

		return jQuery.fn.init.call( this, selector, context, rootjQuerySub );
	};
	jQuerySub.fn.init.prototype = jQuerySub.fn;
	var rootjQuerySub = jQuerySub(document);
	migrateWarn( "jQuery.sub() is deprecated" );
	return jQuerySub;
};


// Ensure that $.ajax gets the new parseJSON defined in core.js
jQuery.ajaxSetup({
	converters: {
		"text json": jQuery.parseJSON
	}
});


var oldFnData = jQuery.fn.data;

jQuery.fn.data = function( name ) {
	var ret, evt,
		elem = this[0];

	// Handles 1.7 which has this behavior and 1.8 which doesn't
	if ( elem && name === "events" && arguments.length === 1 ) {
		ret = jQuery.data( elem, name );
		evt = jQuery._data( elem, name );
		if ( ( ret === undefined || ret === evt ) && evt !== undefined ) {
			migrateWarn("Use of jQuery.fn.data('events') is deprecated");
			return evt;
		}
	}
	return oldFnData.apply( this, arguments );
};


var rscriptType = /\/(java|ecma)script/i,
	oldSelf = jQuery.fn.andSelf || jQuery.fn.addBack;

jQuery.fn.andSelf = function() {
	migrateWarn("jQuery.fn.andSelf() replaced by jQuery.fn.addBack()");
	return oldSelf.apply( this, arguments );
};

// Since jQuery.clean is used internally on older versions, we only shim if it's missing
if ( !jQuery.clean ) {
	jQuery.clean = function( elems, context, fragment, scripts ) {
		// Set context per 1.8 logic
		context = context || document;
		context = !context.nodeType && context[0] || context;
		context = context.ownerDocument || context;

		migrateWarn("jQuery.clean() is deprecated");

		var i, elem, handleScript, jsTags,
			ret = [];

		jQuery.merge( ret, jQuery.buildFragment( elems, context ).childNodes );

		// Complex logic lifted directly from jQuery 1.8
		if ( fragment ) {
			// Special handling of each script element
			handleScript = function( elem ) {
				// Check if we consider it executable
				if ( !elem.type || rscriptType.test( elem.type ) ) {
					// Detach the script and store it in the scripts array (if provided) or the fragment
					// Return truthy to indicate that it has been handled
					return scripts ?
						scripts.push( elem.parentNode ? elem.parentNode.removeChild( elem ) : elem ) :
						fragment.appendChild( elem );
				}
			};

			for ( i = 0; (elem = ret[i]) != null; i++ ) {
				// Check if we're done after handling an executable script
				if ( !( jQuery.nodeName( elem, "script" ) && handleScript( elem ) ) ) {
					// Append to fragment and handle embedded scripts
					fragment.appendChild( elem );
					if ( typeof elem.getElementsByTagName !== "undefined" ) {
						// handleScript alters the DOM, so use jQuery.merge to ensure snapshot iteration
						jsTags = jQuery.grep( jQuery.merge( [], elem.getElementsByTagName("script") ), handleScript );

						// Splice the scripts into ret after their former ancestor and advance our index beyond them
						ret.splice.apply( ret, [i + 1, 0].concat( jsTags ) );
						i += jsTags.length;
					}
				}
			}
		}

		return ret;
	};
}

var eventAdd = jQuery.event.add,
	eventRemove = jQuery.event.remove,
	eventTrigger = jQuery.event.trigger,
	oldToggle = jQuery.fn.toggle,
	oldLive = jQuery.fn.live,
	oldDie = jQuery.fn.die,
	ajaxEvents = "ajaxStart|ajaxStop|ajaxSend|ajaxComplete|ajaxError|ajaxSuccess",
	rajaxEvent = new RegExp( "\\b(?:" + ajaxEvents + ")\\b" ),
	rhoverHack = /(?:^|\s)hover(\.\S+|)\b/,
	hoverHack = function( events ) {
		if ( typeof( events ) !== "string" || jQuery.event.special.hover ) {
			return events;
		}
		if ( rhoverHack.test( events ) ) {
			migrateWarn("'hover' pseudo-event is deprecated, use 'mouseenter mouseleave'");
		}
		return events && events.replace( rhoverHack, "mouseenter$1 mouseleave$1" );
	};

// Event props removed in 1.9, put them back if needed; no practical way to warn them
if ( jQuery.event.props && jQuery.event.props[ 0 ] !== "attrChange" ) {
	jQuery.event.props.unshift( "attrChange", "attrName", "relatedNode", "srcElement" );
}

// Undocumented jQuery.event.handle was "deprecated" in jQuery 1.7
if ( jQuery.event.dispatch ) {
	migrateWarnProp( jQuery.event, "handle", jQuery.event.dispatch, "jQuery.event.handle is undocumented and deprecated" );
}

// Support for 'hover' pseudo-event and ajax event warnings
jQuery.event.add = function( elem, types, handler, data, selector ){
	if ( elem !== document && rajaxEvent.test( types ) ) {
		migrateWarn( "AJAX events should be attached to document: " + types );
	}
	eventAdd.call( this, elem, hoverHack( types || "" ), handler, data, selector );
};
jQuery.event.remove = function( elem, types, handler, selector, mappedTypes ){
	eventRemove.call( this, elem, hoverHack( types ) || "", handler, selector, mappedTypes );
};

jQuery.fn.error = function() {
	var args = Array.prototype.slice.call( arguments, 0);
	migrateWarn("jQuery.fn.error() is deprecated");
	args.splice( 0, 0, "error" );
	if ( arguments.length ) {
		return this.bind.apply( this, args );
	}
	// error event should not bubble to window, although it does pre-1.7
	this.triggerHandler.apply( this, args );
	return this;
};

jQuery.fn.toggle = function( fn, fn2 ) {

	// Don't mess with animation or css toggles
	if ( !jQuery.isFunction( fn ) || !jQuery.isFunction( fn2 ) ) {
		return oldToggle.apply( this, arguments );
	}
	migrateWarn("jQuery.fn.toggle(handler, handler...) is deprecated");

	// Save reference to arguments for access in closure
	var args = arguments,
		guid = fn.guid || jQuery.guid++,
		i = 0,
		toggler = function( event ) {
			// Figure out which function to execute
			var lastToggle = ( jQuery._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
			jQuery._data( this, "lastToggle" + fn.guid, lastToggle + 1 );

			// Make sure that clicks stop
			event.preventDefault();

			// and execute the function
			return args[ lastToggle ].apply( this, arguments ) || false;
		};

	// link all the functions, so any of them can unbind this click handler
	toggler.guid = guid;
	while ( i < args.length ) {
		args[ i++ ].guid = guid;
	}

	return this.click( toggler );
};

jQuery.fn.live = function( types, data, fn ) {
	migrateWarn("jQuery.fn.live() is deprecated");
	if ( oldLive ) {
		return oldLive.apply( this, arguments );
	}
	jQuery( this.context ).on( types, this.selector, data, fn );
	return this;
};

jQuery.fn.die = function( types, fn ) {
	migrateWarn("jQuery.fn.die() is deprecated");
	if ( oldDie ) {
		return oldDie.apply( this, arguments );
	}
	jQuery( this.context ).off( types, this.selector || "**", fn );
	return this;
};

// Turn global events into document-triggered events
jQuery.event.trigger = function( event, data, elem, onlyHandlers  ){
	if ( !elem && !rajaxEvent.test( event ) ) {
		migrateWarn( "Global events are undocumented and deprecated" );
	}
	return eventTrigger.call( this,  event, data, elem || document, onlyHandlers  );
};
jQuery.each( ajaxEvents.split("|"),
	function( _, name ) {
		jQuery.event.special[ name ] = {
			setup: function() {
				var elem = this;

				// The document needs no shimming; must be !== for oldIE
				if ( elem !== document ) {
					jQuery.event.add( document, name + "." + jQuery.guid, function() {
						jQuery.event.trigger( name, null, elem, true );
					});
					jQuery._data( this, name, jQuery.guid++ );
				}
				return false;
			},
			teardown: function() {
				if ( this !== document ) {
					jQuery.event.remove( document, name + "." + jQuery._data( this, name ) );
				}
				return false;
			}
		};
	}
);


})( jQuery, window );
// Spectrum Colorpicker v1.8.0
// https://github.com/bgrins/spectrum
// Author: Brian Grinstead
// License: MIT

(function (factory) {
    "use strict";

    if (typeof define === 'function' && define.amd) { // AMD
        define(['jquery'], factory);
    }
    else if (typeof exports == "object" && typeof module == "object") { // CommonJS
        module.exports = factory(require('jquery'));
    }
    else { // Browser
        factory(jQuery);
    }
})(function($, undefined) {
    "use strict";

    var defaultOpts = {

        // Callbacks
        beforeShow: noop,
        move: noop,
        change: noop,
        show: noop,
        hide: noop,

        // Options
        color: false,
        flat: false,
        showInput: false,
        allowEmpty: false,
        showButtons: true,
        clickoutFiresChange: true,
        showInitial: false,
        showPalette: false,
        showPaletteOnly: false,
        hideAfterPaletteSelect: false,
        togglePaletteOnly: false,
        showSelectionPalette: true,
        localStorageKey: false,
        appendTo: "body",
        maxSelectionSize: 7,
        cancelText: "cancel",
        chooseText: "choose",
        togglePaletteMoreText: "more",
        togglePaletteLessText: "less",
        clearText: "Clear Color Selection",
        noColorSelectedText: "No Color Selected",
        preferredFormat: false,
        className: "", // Deprecated - use containerClassName and replacerClassName instead.
        containerClassName: "",
        replacerClassName: "",
        showAlpha: false,
        theme: "sp-light",
        palette: [["#ffffff", "#000000", "#ff0000", "#ff8000", "#ffff00", "#008000", "#0000ff", "#4b0082", "#9400d3"]],
        selectionPalette: [],
        disabled: false,
        offset: null
    },
    spectrums = [],
    IE = !!/msie/i.exec( window.navigator.userAgent ),
    rgbaSupport = (function() {
        function contains( str, substr ) {
            return !!~('' + str).indexOf(substr);
        }

        var elem = document.createElement('div');
        var style = elem.style;
        style.cssText = 'background-color:rgba(0,0,0,.5)';
        return contains(style.backgroundColor, 'rgba') || contains(style.backgroundColor, 'hsla');
    })(),
    replaceInput = [
        "<div class='sp-replacer'>",
            "<div class='sp-preview'><div class='sp-preview-inner'></div></div>",
            "<div class='sp-dd'>&#9660;</div>",
        "</div>"
    ].join(''),
    markup = (function () {

        // IE does not support gradients with multiple stops, so we need to simulate
        //  that for the rainbow slider with 8 divs that each have a single gradient
        var gradientFix = "";
        if (IE) {
            for (var i = 1; i <= 6; i++) {
                gradientFix += "<div class='sp-" + i + "'></div>";
            }
        }

        return [
            "<div class='sp-container sp-hidden'>",
                "<div class='sp-palette-container'>",
                    "<div class='sp-palette sp-thumb sp-cf'></div>",
                    "<div class='sp-palette-button-container sp-cf'>",
                        "<button type='button' class='sp-palette-toggle'></button>",
                    "</div>",
                "</div>",
                "<div class='sp-picker-container'>",
                    "<div class='sp-top sp-cf'>",
                        "<div class='sp-fill'></div>",
                        "<div class='sp-top-inner'>",
                            "<div class='sp-color'>",
                                "<div class='sp-sat'>",
                                    "<div class='sp-val'>",
                                        "<div class='sp-dragger'></div>",
                                    "</div>",
                                "</div>",
                            "</div>",
                            "<div class='sp-clear sp-clear-display'>",
                            "</div>",
                            "<div class='sp-hue'>",
                                "<div class='sp-slider'></div>",
                                gradientFix,
                            "</div>",
                        "</div>",
                        "<div class='sp-alpha'><div class='sp-alpha-inner'><div class='sp-alpha-handle'></div></div></div>",
                    "</div>",
                    "<div class='sp-input-container sp-cf'>",
                        "<input class='sp-input' type='text' spellcheck='false'  />",
                    "</div>",
                    "<div class='sp-initial sp-thumb sp-cf'></div>",
                    "<div class='sp-button-container sp-cf'>",
                        "<a class='sp-cancel' href='#'></a>",
                        "<button type='button' class='sp-choose'></button>",
                    "</div>",
                "</div>",
            "</div>"
        ].join("");
    })();

    function paletteTemplate (p, color, className, opts) {
        var html = [];
        for (var i = 0; i < p.length; i++) {
            var current = p[i];
            if(current) {
                var tiny = tinycolor(current);
                var c = tiny.toHsl().l < 0.5 ? "sp-thumb-el sp-thumb-dark" : "sp-thumb-el sp-thumb-light";
                c += (tinycolor.equals(color, current)) ? " sp-thumb-active" : "";
                var formattedString = tiny.toString(opts.preferredFormat || "rgb");
                var swatchStyle = rgbaSupport ? ("background-color:" + tiny.toRgbString()) : "filter:" + tiny.toFilter();
                html.push('<span title="' + formattedString + '" data-color="' + tiny.toRgbString() + '" class="' + c + '"><span class="sp-thumb-inner" style="' + swatchStyle + ';" /></span>');
            } else {
                var cls = 'sp-clear-display';
                html.push($('<div />')
                    .append($('<span data-color="" style="background-color:transparent;" class="' + cls + '"></span>')
                        .attr('title', opts.noColorSelectedText)
                    )
                    .html()
                );
            }
        }
        return "<div class='sp-cf " + className + "'>" + html.join('') + "</div>";
    }

    function hideAll() {
        for (var i = 0; i < spectrums.length; i++) {
            if (spectrums[i]) {
                spectrums[i].hide();
            }
        }
    }

    function instanceOptions(o, callbackContext) {
        var opts = $.extend({}, defaultOpts, o);
        opts.callbacks = {
            'move': bind(opts.move, callbackContext),
            'change': bind(opts.change, callbackContext),
            'show': bind(opts.show, callbackContext),
            'hide': bind(opts.hide, callbackContext),
            'beforeShow': bind(opts.beforeShow, callbackContext)
        };

        return opts;
    }

    function spectrum(element, o) {

        var opts = instanceOptions(o, element),
            flat = opts.flat,
            showSelectionPalette = opts.showSelectionPalette,
            localStorageKey = opts.localStorageKey,
            theme = opts.theme,
            callbacks = opts.callbacks,
            resize = throttle(reflow, 10),
            visible = false,
            isDragging = false,
            dragWidth = 0,
            dragHeight = 0,
            dragHelperHeight = 0,
            slideHeight = 0,
            slideWidth = 0,
            alphaWidth = 0,
            alphaSlideHelperWidth = 0,
            slideHelperHeight = 0,
            currentHue = 0,
            currentSaturation = 0,
            currentValue = 0,
            currentAlpha = 1,
            palette = [],
            paletteArray = [],
            paletteLookup = {},
            selectionPalette = opts.selectionPalette.slice(0),
            maxSelectionSize = opts.maxSelectionSize,
            draggingClass = "sp-dragging",
            shiftMovementDirection = null;

        var doc = element.ownerDocument,
            body = doc.body,
            boundElement = $(element),
            disabled = false,
            container = $(markup, doc).addClass(theme),
            pickerContainer = container.find(".sp-picker-container"),
            dragger = container.find(".sp-color"),
            dragHelper = container.find(".sp-dragger"),
            slider = container.find(".sp-hue"),
            slideHelper = container.find(".sp-slider"),
            alphaSliderInner = container.find(".sp-alpha-inner"),
            alphaSlider = container.find(".sp-alpha"),
            alphaSlideHelper = container.find(".sp-alpha-handle"),
            textInput = container.find(".sp-input"),
            paletteContainer = container.find(".sp-palette"),
            initialColorContainer = container.find(".sp-initial"),
            cancelButton = container.find(".sp-cancel"),
            clearButton = container.find(".sp-clear"),
            chooseButton = container.find(".sp-choose"),
            toggleButton = container.find(".sp-palette-toggle"),
            isInput = boundElement.is("input"),
            isInputTypeColor = isInput && boundElement.attr("type") === "color" && inputTypeColorSupport(),
            shouldReplace = isInput && !flat,
            replacer = (shouldReplace) ? $(replaceInput).addClass(theme).addClass(opts.className).addClass(opts.replacerClassName) : $([]),
            offsetElement = (shouldReplace) ? replacer : boundElement,
            previewElement = replacer.find(".sp-preview-inner"),
            initialColor = opts.color || (isInput && boundElement.val()),
            colorOnShow = false,
            currentPreferredFormat = opts.preferredFormat,
            clickoutFiresChange = !opts.showButtons || opts.clickoutFiresChange,
            isEmpty = !initialColor,
            allowEmpty = opts.allowEmpty && !isInputTypeColor;

        function applyOptions() {

            if (opts.showPaletteOnly) {
                opts.showPalette = true;
            }

            toggleButton.text(opts.showPaletteOnly ? opts.togglePaletteMoreText : opts.togglePaletteLessText);

            if (opts.palette) {
                palette = opts.palette.slice(0);
                paletteArray = $.isArray(palette[0]) ? palette : [palette];
                paletteLookup = {};
                for (var i = 0; i < paletteArray.length; i++) {
                    for (var j = 0; j < paletteArray[i].length; j++) {
                        var rgb = tinycolor(paletteArray[i][j]).toRgbString();
                        paletteLookup[rgb] = true;
                    }
                }
            }

            container.toggleClass("sp-flat", flat);
            container.toggleClass("sp-input-disabled", !opts.showInput);
            container.toggleClass("sp-alpha-enabled", opts.showAlpha);
            container.toggleClass("sp-clear-enabled", allowEmpty);
            container.toggleClass("sp-buttons-disabled", !opts.showButtons);
            container.toggleClass("sp-palette-buttons-disabled", !opts.togglePaletteOnly);
            container.toggleClass("sp-palette-disabled", !opts.showPalette);
            container.toggleClass("sp-palette-only", opts.showPaletteOnly);
            container.toggleClass("sp-initial-disabled", !opts.showInitial);
            container.addClass(opts.className).addClass(opts.containerClassName);

            reflow();
        }

        function initialize() {

            if (IE) {
                container.find("*:not(input)").attr("unselectable", "on");
            }

            applyOptions();

            if (shouldReplace) {
                boundElement.after(replacer).hide();
            }

            if (!allowEmpty) {
                clearButton.hide();
            }

            if (flat) {
                boundElement.after(container).hide();
            }
            else {

                var appendTo = opts.appendTo === "parent" ? boundElement.parent() : $(opts.appendTo);
                if (appendTo.length !== 1) {
                    appendTo = $("body");
                }

                appendTo.append(container);
            }

            updateSelectionPaletteFromStorage();

            offsetElement.on("click.spectrum touchstart.spectrum", function (e) {
                if (!disabled) {
                    toggle();
                }

                e.stopPropagation();

                if (!$(e.target).is("input")) {
                    e.preventDefault();
                }
            });

            if(boundElement.is(":disabled") || (opts.disabled === true)) {
                disable();
            }

            // Prevent clicks from bubbling up to document.  This would cause it to be hidden.
            container.click(stopPropagation);

            // Handle user typed input
            textInput.change(setFromTextInput);
            textInput.on("paste", function () {
                setTimeout(setFromTextInput, 1);
            });
            textInput.keydown(function (e) { if (e.keyCode == 13) { setFromTextInput(); } });

            cancelButton.text(opts.cancelText);
            cancelButton.on("click.spectrum", function (e) {
                e.stopPropagation();
                e.preventDefault();
                revert();
                hide();
            });

            clearButton.attr("title", opts.clearText);
            clearButton.on("click.spectrum", function (e) {
                e.stopPropagation();
                e.preventDefault();
                isEmpty = true;
                move();

                if(flat) {
                    //for the flat style, this is a change event
                    updateOriginalInput(true);
                }
            });

            chooseButton.text(opts.chooseText);
            chooseButton.on("click.spectrum", function (e) {
                e.stopPropagation();
                e.preventDefault();

                if (IE && textInput.is(":focus")) {
                    textInput.trigger('change');
                }

                if (isValid()) {
                    updateOriginalInput(true);
                    hide();
                }
            });

            toggleButton.text(opts.showPaletteOnly ? opts.togglePaletteMoreText : opts.togglePaletteLessText);
            toggleButton.on("click.spectrum", function (e) {
                e.stopPropagation();
                e.preventDefault();

                opts.showPaletteOnly = !opts.showPaletteOnly;

                // To make sure the Picker area is drawn on the right, next to the
                // Palette area (and not below the palette), first move the Palette
                // to the left to make space for the picker, plus 5px extra.
                // The 'applyOptions' function puts the whole container back into place
                // and takes care of the button-text and the sp-palette-only CSS class.
                if (!opts.showPaletteOnly && !flat) {
                    container.css('left', '-=' + (pickerContainer.outerWidth(true) + 5));
                }
                applyOptions();
            });

            draggable(alphaSlider, function (dragX, dragY, e) {
                currentAlpha = (dragX / alphaWidth);
                isEmpty = false;
                if (e.shiftKey) {
                    currentAlpha = Math.round(currentAlpha * 10) / 10;
                }

                move();
            }, dragStart, dragStop);

            draggable(slider, function (dragX, dragY) {
                currentHue = parseFloat(dragY / slideHeight);
                isEmpty = false;
                if (!opts.showAlpha) {
                    currentAlpha = 1;
                }
                move();
            }, dragStart, dragStop);

            draggable(dragger, function (dragX, dragY, e) {

                // shift+drag should snap the movement to either the x or y axis.
                if (!e.shiftKey) {
                    shiftMovementDirection = null;
                }
                else if (!shiftMovementDirection) {
                    var oldDragX = currentSaturation * dragWidth;
                    var oldDragY = dragHeight - (currentValue * dragHeight);
                    var furtherFromX = Math.abs(dragX - oldDragX) > Math.abs(dragY - oldDragY);

                    shiftMovementDirection = furtherFromX ? "x" : "y";
                }

                var setSaturation = !shiftMovementDirection || shiftMovementDirection === "x";
                var setValue = !shiftMovementDirection || shiftMovementDirection === "y";

                if (setSaturation) {
                    currentSaturation = parseFloat(dragX / dragWidth);
                }
                if (setValue) {
                    currentValue = parseFloat((dragHeight - dragY) / dragHeight);
                }

                isEmpty = false;
                if (!opts.showAlpha) {
                    currentAlpha = 1;
                }

                move();

            }, dragStart, dragStop);

            if (!!initialColor) {
                set(initialColor);

                // In case color was black - update the preview UI and set the format
                // since the set function will not run (default color is black).
                updateUI();
                currentPreferredFormat = opts.preferredFormat || tinycolor(initialColor).format;

                addColorToSelectionPalette(initialColor);
            }
            else {
                updateUI();
            }

            if (flat) {
                show();
            }

            function paletteElementClick(e) {
                if (e.data && e.data.ignore) {
                    set($(e.target).closest(".sp-thumb-el").data("color"));
                    move();
                }
                else {
                    set($(e.target).closest(".sp-thumb-el").data("color"));
                    move();

                    // If the picker is going to close immediately, a palette selection
                    // is a change.  Otherwise, it's a move only.
                    if (opts.hideAfterPaletteSelect) {
                        updateOriginalInput(true);
                        hide();
                    } else {
                        updateOriginalInput();
                    }
                }

                return false;
            }

            var paletteEvent = IE ? "mousedown.spectrum" : "click.spectrum touchstart.spectrum";
            paletteContainer.on(paletteEvent, ".sp-thumb-el", paletteElementClick);
            initialColorContainer.on(paletteEvent, ".sp-thumb-el:nth-child(1)", { ignore: true }, paletteElementClick);
        }

        function updateSelectionPaletteFromStorage() {

            if (localStorageKey && window.localStorage) {

                // Migrate old palettes over to new format.  May want to remove this eventually.
                try {
                    var oldPalette = window.localStorage[localStorageKey].split(",#");
                    if (oldPalette.length > 1) {
                        delete window.localStorage[localStorageKey];
                        $.each(oldPalette, function(i, c) {
                             addColorToSelectionPalette(c);
                        });
                    }
                }
                catch(e) { }

                try {
                    selectionPalette = window.localStorage[localStorageKey].split(";");
                }
                catch (e) { }
            }
        }

        function addColorToSelectionPalette(color) {
            if (showSelectionPalette) {
                var rgb = tinycolor(color).toRgbString();
                if (!paletteLookup[rgb] && $.inArray(rgb, selectionPalette) === -1) {
                    selectionPalette.push(rgb);
                    while(selectionPalette.length > maxSelectionSize) {
                        selectionPalette.shift();
                    }
                }

                if (localStorageKey && window.localStorage) {
                    try {
                        window.localStorage[localStorageKey] = selectionPalette.join(";");
                    }
                    catch(e) { }
                }
            }
        }

        function getUniqueSelectionPalette() {
            var unique = [];
            if (opts.showPalette) {
                for (var i = 0; i < selectionPalette.length; i++) {
                    var rgb = tinycolor(selectionPalette[i]).toRgbString();

                    if (!paletteLookup[rgb]) {
                        unique.push(selectionPalette[i]);
                    }
                }
            }

            return unique.reverse().slice(0, opts.maxSelectionSize);
        }

        function drawPalette() {

            var currentColor = get();

            var html = $.map(paletteArray, function (palette, i) {
                return paletteTemplate(palette, currentColor, "sp-palette-row sp-palette-row-" + i, opts);
            });

            updateSelectionPaletteFromStorage();

            if (selectionPalette) {
                html.push(paletteTemplate(getUniqueSelectionPalette(), currentColor, "sp-palette-row sp-palette-row-selection", opts));
            }

            paletteContainer.html(html.join(""));
        }

        function drawInitial() {
            if (opts.showInitial) {
                var initial = colorOnShow;
                var current = get();
                initialColorContainer.html(paletteTemplate([initial, current], current, "sp-palette-row-initial", opts));
            }
        }

        function dragStart() {
            if (dragHeight <= 0 || dragWidth <= 0 || slideHeight <= 0) {
                reflow();
            }
            isDragging = true;
            container.addClass(draggingClass);
            shiftMovementDirection = null;
            boundElement.trigger('dragstart.spectrum', [ get() ]);
        }

        function dragStop() {
            isDragging = false;
            container.removeClass(draggingClass);
            boundElement.trigger('dragstop.spectrum', [ get() ]);
        }

        function setFromTextInput() {

            var value = textInput.val();

            if ((value === null || value === "") && allowEmpty) {
                set(null);
                move();
                updateOriginalInput();
            }
            else {
                var tiny = tinycolor(value);
                if (tiny.isValid()) {
                    set(tiny);
                    move();
                    updateOriginalInput();
                }
                else {
                    textInput.addClass("sp-validation-error");
                }
            }
        }

        function toggle() {
            if (visible) {
                hide();
            }
            else {
                show();
            }
        }

        function show() {
            var event = $.Event('beforeShow.spectrum');

            if (visible) {
                reflow();
                return;
            }

            boundElement.trigger(event, [ get() ]);

            if (callbacks.beforeShow(get()) === false || event.isDefaultPrevented()) {
                return;
            }

            hideAll();
            visible = true;

            $(doc).on("keydown.spectrum", onkeydown);
            $(doc).on("click.spectrum", clickout);
            $(window).on("resize.spectrum", resize);
            replacer.addClass("sp-active");
            container.removeClass("sp-hidden");

            reflow();
            updateUI();

            colorOnShow = get();

            drawInitial();
            callbacks.show(colorOnShow);
            boundElement.trigger('show.spectrum', [ colorOnShow ]);
        }

        function onkeydown(e) {
            // Close on ESC
            if (e.keyCode === 27) {
                hide();
            }
        }

        function clickout(e) {
            // Return on right click.
            if (e.button == 2) { return; }

            // If a drag event was happening during the mouseup, don't hide
            // on click.
            if (isDragging) { return; }

            if (clickoutFiresChange) {
                updateOriginalInput(true);
            }
            else {
                revert();
            }
            hide();
        }

        function hide() {
            // Return if hiding is unnecessary
            if (!visible || flat) { return; }
            visible = false;

            $(doc).off("keydown.spectrum", onkeydown);
            $(doc).off("click.spectrum", clickout);
            $(window).off("resize.spectrum", resize);

            replacer.removeClass("sp-active");
            container.addClass("sp-hidden");

            callbacks.hide(get());
            boundElement.trigger('hide.spectrum', [ get() ]);
        }

        function revert() {
            set(colorOnShow, true);
            updateOriginalInput(true);
        }

        function set(color, ignoreFormatChange) {
            if (tinycolor.equals(color, get())) {
                // Update UI just in case a validation error needs
                // to be cleared.
                updateUI();
                return;
            }

            var newColor, newHsv;
            if (!color && allowEmpty) {
                isEmpty = true;
            } else {
                isEmpty = false;
                newColor = tinycolor(color);
                newHsv = newColor.toHsv();

                currentHue = (newHsv.h % 360) / 360;
                currentSaturation = newHsv.s;
                currentValue = newHsv.v;
                currentAlpha = newHsv.a;
            }
            updateUI();

            if (newColor && newColor.isValid() && !ignoreFormatChange) {
                currentPreferredFormat = opts.preferredFormat || newColor.getFormat();
            }
        }

        function get(opts) {
            opts = opts || { };

            if (allowEmpty && isEmpty) {
                return null;
            }

            return tinycolor.fromRatio({
                h: currentHue,
                s: currentSaturation,
                v: currentValue,
                a: Math.round(currentAlpha * 1000) / 1000
            }, { format: opts.format || currentPreferredFormat });
        }

        function isValid() {
            return !textInput.hasClass("sp-validation-error");
        }

        function move() {
            updateUI();

            callbacks.move(get());
            boundElement.trigger('move.spectrum', [ get() ]);
        }

        function updateUI() {

            textInput.removeClass("sp-validation-error");

            updateHelperLocations();

            // Update dragger background color (gradients take care of saturation and value).
            var flatColor = tinycolor.fromRatio({ h: currentHue, s: 1, v: 1 });
            dragger.css("background-color", flatColor.toHexString());

            // Get a format that alpha will be included in (hex and names ignore alpha)
            var format = currentPreferredFormat;
            if (currentAlpha < 1 && !(currentAlpha === 0 && format === "name")) {
                if (format === "hex" || format === "hex3" || format === "hex6" || format === "name") {
                    format = "rgb";
                }
            }

            var realColor = get({ format: format }),
                displayColor = '';

             //reset background info for preview element
            previewElement.removeClass("sp-clear-display");
            previewElement.css('background-color', 'transparent');

            if (!realColor && allowEmpty) {
                // Update the replaced elements background with icon indicating no color selection
                previewElement.addClass("sp-clear-display");
            }
            else {
                var realHex = realColor.toHexString(),
                    realRgb = realColor.toRgbString();

                // Update the replaced elements background color (with actual selected color)
                if (rgbaSupport || realColor.alpha === 1) {
                    previewElement.css("background-color", realRgb);
                }
                else {
                    previewElement.css("background-color", "transparent");
                    previewElement.css("filter", realColor.toFilter());
                }

                if (opts.showAlpha) {
                    var rgb = realColor.toRgb();
                    rgb.a = 0;
                    var realAlpha = tinycolor(rgb).toRgbString();
                    var gradient = "linear-gradient(left, " + realAlpha + ", " + realHex + ")";

                    if (IE) {
                        alphaSliderInner.css("filter", tinycolor(realAlpha).toFilter({ gradientType: 1 }, realHex));
                    }
                    else {
                        alphaSliderInner.css("background", "-webkit-" + gradient);
                        alphaSliderInner.css("background", "-moz-" + gradient);
                        alphaSliderInner.css("background", "-ms-" + gradient);
                        // Use current syntax gradient on unprefixed property.
                        alphaSliderInner.css("background",
                            "linear-gradient(to right, " + realAlpha + ", " + realHex + ")");
                    }
                }

                displayColor = realColor.toString(format);
            }

            // Update the text entry input as it changes happen
            if (opts.showInput) {
                textInput.val(displayColor);
            }

            if (opts.showPalette) {
                drawPalette();
            }

            drawInitial();
        }

        function updateHelperLocations() {
            var s = currentSaturation;
            var v = currentValue;

            if(allowEmpty && isEmpty) {
                //if selected color is empty, hide the helpers
                alphaSlideHelper.hide();
                slideHelper.hide();
                dragHelper.hide();
            }
            else {
                //make sure helpers are visible
                alphaSlideHelper.show();
                slideHelper.show();
                dragHelper.show();

                // Where to show the little circle in that displays your current selected color
                var dragX = s * dragWidth;
                var dragY = dragHeight - (v * dragHeight);
                dragX = Math.max(
                    -dragHelperHeight,
                    Math.min(dragWidth - dragHelperHeight, dragX - dragHelperHeight)
                );
                dragY = Math.max(
                    -dragHelperHeight,
                    Math.min(dragHeight - dragHelperHeight, dragY - dragHelperHeight)
                );
                dragHelper.css({
                    "top": dragY + "px",
                    "left": dragX + "px"
                });

                var alphaX = currentAlpha * alphaWidth;
                alphaSlideHelper.css({
                    "left": (alphaX - (alphaSlideHelperWidth / 2)) + "px"
                });

                // Where to show the bar that displays your current selected hue
                var slideY = (currentHue) * slideHeight;
                slideHelper.css({
                    "top": (slideY - slideHelperHeight) + "px"
                });
            }
        }

        function updateOriginalInput(fireCallback) {
            var color = get(),
                displayColor = '',
                hasChanged = !tinycolor.equals(color, colorOnShow);

            if (color) {
                displayColor = color.toString(currentPreferredFormat);
                // Update the selection palette with the current color
                addColorToSelectionPalette(color);
            }

            if (isInput) {
                boundElement.val(displayColor);
            }

            if (fireCallback && hasChanged) {
                callbacks.change(color);
                boundElement.trigger('change', [ color ]);
            }
        }

        function reflow() {
            if (!visible) {
                return; // Calculations would be useless and wouldn't be reliable anyways
            }
            dragWidth = dragger.width();
            dragHeight = dragger.height();
            dragHelperHeight = dragHelper.height();
            slideWidth = slider.width();
            slideHeight = slider.height();
            slideHelperHeight = slideHelper.height();
            alphaWidth = alphaSlider.width();
            alphaSlideHelperWidth = alphaSlideHelper.width();

            if (!flat) {
                container.css("position", "absolute");
                if (opts.offset) {
                    container.offset(opts.offset);
                } else {
                    container.offset(getOffset(container, offsetElement));
                }
            }

            updateHelperLocations();

            if (opts.showPalette) {
                drawPalette();
            }

            boundElement.trigger('reflow.spectrum');
        }

        function destroy() {
            boundElement.show();
            offsetElement.off("click.spectrum touchstart.spectrum");
            container.remove();
            replacer.remove();
            spectrums[spect.id] = null;
        }

        function option(optionName, optionValue) {
            if (optionName === undefined) {
                return $.extend({}, opts);
            }
            if (optionValue === undefined) {
                return opts[optionName];
            }

            opts[optionName] = optionValue;

            if (optionName === "preferredFormat") {
                currentPreferredFormat = opts.preferredFormat;
            }
            applyOptions();
        }

        function enable() {
            disabled = false;
            boundElement.attr("disabled", false);
            offsetElement.removeClass("sp-disabled");
        }

        function disable() {
            hide();
            disabled = true;
            boundElement.attr("disabled", true);
            offsetElement.addClass("sp-disabled");
        }

        function setOffset(coord) {
            opts.offset = coord;
            reflow();
        }

        initialize();

        var spect = {
            show: show,
            hide: hide,
            toggle: toggle,
            reflow: reflow,
            option: option,
            enable: enable,
            disable: disable,
            offset: setOffset,
            set: function (c) {
                set(c);
                updateOriginalInput();
            },
            get: get,
            destroy: destroy,
            container: container
        };

        spect.id = spectrums.push(spect) - 1;

        return spect;
    }

    /**
    * checkOffset - get the offset below/above and left/right element depending on screen position
    * Thanks https://github.com/jquery/jquery-ui/blob/master/ui/jquery.ui.datepicker.js
    */
    function getOffset(picker, input) {
        var extraY = 0;
        var dpWidth = picker.outerWidth();
        var dpHeight = picker.outerHeight();
        var inputHeight = input.outerHeight();
        var doc = picker[0].ownerDocument;
        var docElem = doc.documentElement;
        var viewWidth = docElem.clientWidth + $(doc).scrollLeft();
        var viewHeight = docElem.clientHeight + $(doc).scrollTop();
        var offset = input.offset();
        var offsetLeft = offset.left;
        var offsetTop = offset.top;

        offsetTop += inputHeight;

        offsetLeft -=
            Math.min(offsetLeft, (offsetLeft + dpWidth > viewWidth && viewWidth > dpWidth) ?
            Math.abs(offsetLeft + dpWidth - viewWidth) : 0);

        offsetTop -=
            Math.min(offsetTop, ((offsetTop + dpHeight > viewHeight && viewHeight > dpHeight) ?
            Math.abs(dpHeight + inputHeight - extraY) : extraY));

        return {
            top: offsetTop,
            bottom: offset.bottom,
            left: offsetLeft,
            right: offset.right,
            width: offset.width,
            height: offset.height
        };
    }

    /**
    * noop - do nothing
    */
    function noop() {

    }

    /**
    * stopPropagation - makes the code only doing this a little easier to read in line
    */
    function stopPropagation(e) {
        e.stopPropagation();
    }

    /**
    * Create a function bound to a given object
    * Thanks to underscore.js
    */
    function bind(func, obj) {
        var slice = Array.prototype.slice;
        var args = slice.call(arguments, 2);
        return function () {
            return func.apply(obj, args.concat(slice.call(arguments)));
        };
    }

    /**
    * Lightweight drag helper.  Handles containment within the element, so that
    * when dragging, the x is within [0,element.width] and y is within [0,element.height]
    */
    function draggable(element, onmove, onstart, onstop) {
        onmove = onmove || function () { };
        onstart = onstart || function () { };
        onstop = onstop || function () { };
        var doc = document;
        var dragging = false;
        var offset = {};
        var maxHeight = 0;
        var maxWidth = 0;
        var hasTouch = ('ontouchstart' in window);

        var duringDragEvents = {};
        duringDragEvents["selectstart"] = prevent;
        duringDragEvents["dragstart"] = prevent;
        duringDragEvents["touchmove mousemove"] = move;
        duringDragEvents["touchend mouseup"] = stop;

        function prevent(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.returnValue = false;
        }

        function move(e) {
            if (dragging) {
                // Mouseup happened outside of window
                if (IE && doc.documentMode < 9 && !e.button) {
                    return stop();
                }

                var t0 = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0];
                var pageX = t0 && t0.pageX || e.pageX;
                var pageY = t0 && t0.pageY || e.pageY;

                var dragX = Math.max(0, Math.min(pageX - offset.left, maxWidth));
                var dragY = Math.max(0, Math.min(pageY - offset.top, maxHeight));

                if (hasTouch) {
                    // Stop scrolling in iOS
                    prevent(e);
                }

                onmove.apply(element, [dragX, dragY, e]);
            }
        }

        function start(e) {
            var rightclick = (e.which) ? (e.which == 3) : (e.button == 2);

            if (!rightclick && !dragging) {
                if (onstart.apply(element, arguments) !== false) {
                    dragging = true;
                    maxHeight = $(element).height();
                    maxWidth = $(element).width();
                    offset = $(element).offset();

                    $(doc).on(duringDragEvents);
                    $(doc.body).addClass("sp-dragging");

                    move(e);

                    prevent(e);
                }
            }
        }

        function stop() {
            if (dragging) {
                $(doc).off(duringDragEvents);
                $(doc.body).removeClass("sp-dragging");

                // Wait a tick before notifying observers to allow the click event
                // to fire in Chrome.
                setTimeout(function() {
                    onstop.apply(element, arguments);
                }, 0);
            }
            dragging = false;
        }

        $(element).on("touchstart mousedown", start);
    }

    function throttle(func, wait, debounce) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var throttler = function () {
                timeout = null;
                func.apply(context, args);
            };
            if (debounce) clearTimeout(timeout);
            if (debounce || !timeout) timeout = setTimeout(throttler, wait);
        };
    }

    function inputTypeColorSupport() {
        return $.fn.spectrum.inputTypeColorSupport();
    }

    /**
    * Define a jQuery plugin
    */
    var dataID = "spectrum.id";
    $.fn.spectrum = function (opts, extra) {

        if (typeof opts == "string") {

            var returnValue = this;
            var args = Array.prototype.slice.call( arguments, 1 );

            this.each(function () {
                var spect = spectrums[$(this).data(dataID)];
                if (spect) {
                    var method = spect[opts];
                    if (!method) {
                        throw new Error( "Spectrum: no such method: '" + opts + "'" );
                    }

                    if (opts == "get") {
                        returnValue = spect.get();
                    }
                    else if (opts == "container") {
                        returnValue = spect.container;
                    }
                    else if (opts == "option") {
                        returnValue = spect.option.apply(spect, args);
                    }
                    else if (opts == "destroy") {
                        spect.destroy();
                        $(this).removeData(dataID);
                    }
                    else {
                        method.apply(spect, args);
                    }
                }
            });

            return returnValue;
        }

        // Initializing a new instance of spectrum
        return this.spectrum("destroy").each(function () {
            var options = $.extend({}, $(this).data(), opts);
            var spect = spectrum(this, options);
            $(this).data(dataID, spect.id);
        });
    };

    $.fn.spectrum.load = true;
    $.fn.spectrum.loadOpts = {};
    $.fn.spectrum.draggable = draggable;
    $.fn.spectrum.defaults = defaultOpts;
    $.fn.spectrum.inputTypeColorSupport = function inputTypeColorSupport() {
        if (typeof inputTypeColorSupport._cachedResult === "undefined") {
            var colorInput = $("<input type='color'/>")[0]; // if color element is supported, value will default to not null
            inputTypeColorSupport._cachedResult = colorInput.type === "color" && colorInput.value !== "";
        }
        return inputTypeColorSupport._cachedResult;
    };

    $.spectrum = { };
    $.spectrum.localization = { };
    $.spectrum.palettes = { };

    $.fn.spectrum.processNativeColorInputs = function () {
        var colorInputs = $("input[type=color]");
        if (colorInputs.length && !inputTypeColorSupport()) {
            colorInputs.spectrum({
                preferredFormat: "hex6"
            });
        }
    };

    // TinyColor v1.1.2
    // https://github.com/bgrins/TinyColor
    // Brian Grinstead, MIT License

    (function() {

    var trimLeft = /^[\s,#]+/,
        trimRight = /\s+$/,
        tinyCounter = 0,
        math = Math,
        mathRound = math.round,
        mathMin = math.min,
        mathMax = math.max,
        mathRandom = math.random;

    var tinycolor = function(color, opts) {

        color = (color) ? color : '';
        opts = opts || { };

        // If input is already a tinycolor, return itself
        if (color instanceof tinycolor) {
           return color;
        }
        // If we are called as a function, call using new instead
        if (!(this instanceof tinycolor)) {
            return new tinycolor(color, opts);
        }

        var rgb = inputToRGB(color);
        this._originalInput = color,
        this._r = rgb.r,
        this._g = rgb.g,
        this._b = rgb.b,
        this._a = rgb.a,
        this._roundA = mathRound(1000 * this._a) / 1000,
        this._format = opts.format || rgb.format;
        this._gradientType = opts.gradientType;

        // Don't let the range of [0,255] come back in [0,1].
        // Potentially lose a little bit of precision here, but will fix issues where
        // .5 gets interpreted as half of the total, instead of half of 1
        // If it was supposed to be 128, this was already taken care of by `inputToRgb`
        if (this._r < 1) { this._r = mathRound(this._r); }
        if (this._g < 1) { this._g = mathRound(this._g); }
        if (this._b < 1) { this._b = mathRound(this._b); }

        this._ok = rgb.ok;
        this._tc_id = tinyCounter++;
    };

    tinycolor.prototype = {
        isDark: function() {
            return this.getBrightness() < 128;
        },
        isLight: function() {
            return !this.isDark();
        },
        isValid: function() {
            return this._ok;
        },
        getOriginalInput: function() {
          return this._originalInput;
        },
        getFormat: function() {
            return this._format;
        },
        getAlpha: function() {
            return this._a;
        },
        getBrightness: function() {
            var rgb = this.toRgb();
            return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
        },
        setAlpha: function(value) {
            this._a = boundAlpha(value);
            this._roundA = mathRound(1000 * this._a) / 1000;
            return this;
        },
        toHsv: function() {
            var hsv = rgbToHsv(this._r, this._g, this._b);
            return { h: hsv.h * 360, s: hsv.s, v: hsv.v, a: this._a };
        },
        toHsvString: function() {
            var hsv = rgbToHsv(this._r, this._g, this._b);
            var h = mathRound(hsv.h * 360), s = mathRound(hsv.s * 100), v = mathRound(hsv.v * 100);
            return (this._a == 1) ?
              "hsv("  + h + ", " + s + "%, " + v + "%)" :
              "hsva(" + h + ", " + s + "%, " + v + "%, "+ this._roundA + ")";
        },
        toHsl: function() {
            var hsl = rgbToHsl(this._r, this._g, this._b);
            return { h: hsl.h * 360, s: hsl.s, l: hsl.l, a: this._a };
        },
        toHslString: function() {
            var hsl = rgbToHsl(this._r, this._g, this._b);
            var h = mathRound(hsl.h * 360), s = mathRound(hsl.s * 100), l = mathRound(hsl.l * 100);
            return (this._a == 1) ?
              "hsl("  + h + ", " + s + "%, " + l + "%)" :
              "hsla(" + h + ", " + s + "%, " + l + "%, "+ this._roundA + ")";
        },
        toHex: function(allow3Char) {
            return rgbToHex(this._r, this._g, this._b, allow3Char);
        },
        toHexString: function(allow3Char) {
            return '#' + this.toHex(allow3Char);
        },
        toHex8: function() {
            return rgbaToHex(this._r, this._g, this._b, this._a);
        },
        toHex8String: function() {
            return '#' + this.toHex8();
        },
        toRgb: function() {
            return { r: mathRound(this._r), g: mathRound(this._g), b: mathRound(this._b), a: this._a };
        },
        toRgbString: function() {
            return (this._a == 1) ?
              "rgb("  + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ")" :
              "rgba(" + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ", " + this._roundA + ")";
        },
        toPercentageRgb: function() {
            return { r: mathRound(bound01(this._r, 255) * 100) + "%", g: mathRound(bound01(this._g, 255) * 100) + "%", b: mathRound(bound01(this._b, 255) * 100) + "%", a: this._a };
        },
        toPercentageRgbString: function() {
            return (this._a == 1) ?
              "rgb("  + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%)" :
              "rgba(" + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%, " + this._roundA + ")";
        },
        toName: function() {
            if (this._a === 0) {
                return "transparent";
            }

            if (this._a < 1) {
                return false;
            }

            return hexNames[rgbToHex(this._r, this._g, this._b, true)] || false;
        },
        toFilter: function(secondColor) {
            var hex8String = '#' + rgbaToHex(this._r, this._g, this._b, this._a);
            var secondHex8String = hex8String;
            var gradientType = this._gradientType ? "GradientType = 1, " : "";

            if (secondColor) {
                var s = tinycolor(secondColor);
                secondHex8String = s.toHex8String();
            }

            return "progid:DXImageTransform.Microsoft.gradient("+gradientType+"startColorstr="+hex8String+",endColorstr="+secondHex8String+")";
        },
        toString: function(format) {
            var formatSet = !!format;
            format = format || this._format;

            var formattedString = false;
            var hasAlpha = this._a < 1 && this._a >= 0;
            var needsAlphaFormat = !formatSet && hasAlpha && (format === "hex" || format === "hex6" || format === "hex3" || format === "name");

            if (needsAlphaFormat) {
                // Special case for "transparent", all other non-alpha formats
                // will return rgba when there is transparency.
                if (format === "name" && this._a === 0) {
                    return this.toName();
                }
                return this.toRgbString();
            }
            if (format === "rgb") {
                formattedString = this.toRgbString();
            }
            if (format === "prgb") {
                formattedString = this.toPercentageRgbString();
            }
            if (format === "hex" || format === "hex6") {
                formattedString = this.toHexString();
            }
            if (format === "hex3") {
                formattedString = this.toHexString(true);
            }
            if (format === "hex8") {
                formattedString = this.toHex8String();
            }
            if (format === "name") {
                formattedString = this.toName();
            }
            if (format === "hsl") {
                formattedString = this.toHslString();
            }
            if (format === "hsv") {
                formattedString = this.toHsvString();
            }

            return formattedString || this.toHexString();
        },

        _applyModification: function(fn, args) {
            var color = fn.apply(null, [this].concat([].slice.call(args)));
            this._r = color._r;
            this._g = color._g;
            this._b = color._b;
            this.setAlpha(color._a);
            return this;
        },
        lighten: function() {
            return this._applyModification(lighten, arguments);
        },
        brighten: function() {
            return this._applyModification(brighten, arguments);
        },
        darken: function() {
            return this._applyModification(darken, arguments);
        },
        desaturate: function() {
            return this._applyModification(desaturate, arguments);
        },
        saturate: function() {
            return this._applyModification(saturate, arguments);
        },
        greyscale: function() {
            return this._applyModification(greyscale, arguments);
        },
        spin: function() {
            return this._applyModification(spin, arguments);
        },

        _applyCombination: function(fn, args) {
            return fn.apply(null, [this].concat([].slice.call(args)));
        },
        analogous: function() {
            return this._applyCombination(analogous, arguments);
        },
        complement: function() {
            return this._applyCombination(complement, arguments);
        },
        monochromatic: function() {
            return this._applyCombination(monochromatic, arguments);
        },
        splitcomplement: function() {
            return this._applyCombination(splitcomplement, arguments);
        },
        triad: function() {
            return this._applyCombination(triad, arguments);
        },
        tetrad: function() {
            return this._applyCombination(tetrad, arguments);
        }
    };

    // If input is an object, force 1 into "1.0" to handle ratios properly
    // String input requires "1.0" as input, so 1 will be treated as 1
    tinycolor.fromRatio = function(color, opts) {
        if (typeof color == "object") {
            var newColor = {};
            for (var i in color) {
                if (color.hasOwnProperty(i)) {
                    if (i === "a") {
                        newColor[i] = color[i];
                    }
                    else {
                        newColor[i] = convertToPercentage(color[i]);
                    }
                }
            }
            color = newColor;
        }

        return tinycolor(color, opts);
    };

    // Given a string or object, convert that input to RGB
    // Possible string inputs:
    //
    //     "red"
    //     "#f00" or "f00"
    //     "#ff0000" or "ff0000"
    //     "#ff000000" or "ff000000"
    //     "rgb 255 0 0" or "rgb (255, 0, 0)"
    //     "rgb 1.0 0 0" or "rgb (1, 0, 0)"
    //     "rgba (255, 0, 0, 1)" or "rgba 255, 0, 0, 1"
    //     "rgba (1.0, 0, 0, 1)" or "rgba 1.0, 0, 0, 1"
    //     "hsl(0, 100%, 50%)" or "hsl 0 100% 50%"
    //     "hsla(0, 100%, 50%, 1)" or "hsla 0 100% 50%, 1"
    //     "hsv(0, 100%, 100%)" or "hsv 0 100% 100%"
    //
    function inputToRGB(color) {

        var rgb = { r: 0, g: 0, b: 0 };
        var a = 1;
        var ok = false;
        var format = false;

        if (typeof color == "string") {
            color = stringInputToObject(color);
        }

        if (typeof color == "object") {
            if (color.hasOwnProperty("r") && color.hasOwnProperty("g") && color.hasOwnProperty("b")) {
                rgb = rgbToRgb(color.r, color.g, color.b);
                ok = true;
                format = String(color.r).substr(-1) === "%" ? "prgb" : "rgb";
            }
            else if (color.hasOwnProperty("h") && color.hasOwnProperty("s") && color.hasOwnProperty("v")) {
                color.s = convertToPercentage(color.s);
                color.v = convertToPercentage(color.v);
                rgb = hsvToRgb(color.h, color.s, color.v);
                ok = true;
                format = "hsv";
            }
            else if (color.hasOwnProperty("h") && color.hasOwnProperty("s") && color.hasOwnProperty("l")) {
                color.s = convertToPercentage(color.s);
                color.l = convertToPercentage(color.l);
                rgb = hslToRgb(color.h, color.s, color.l);
                ok = true;
                format = "hsl";
            }

            if (color.hasOwnProperty("a")) {
                a = color.a;
            }
        }

        a = boundAlpha(a);

        return {
            ok: ok,
            format: color.format || format,
            r: mathMin(255, mathMax(rgb.r, 0)),
            g: mathMin(255, mathMax(rgb.g, 0)),
            b: mathMin(255, mathMax(rgb.b, 0)),
            a: a
        };
    }


    // Conversion Functions
    // --------------------

    // `rgbToHsl`, `rgbToHsv`, `hslToRgb`, `hsvToRgb` modified from:
    // <http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript>

    // `rgbToRgb`
    // Handle bounds / percentage checking to conform to CSS color spec
    // <http://www.w3.org/TR/css3-color/>
    // *Assumes:* r, g, b in [0, 255] or [0, 1]
    // *Returns:* { r, g, b } in [0, 255]
    function rgbToRgb(r, g, b){
        return {
            r: bound01(r, 255) * 255,
            g: bound01(g, 255) * 255,
            b: bound01(b, 255) * 255
        };
    }

    // `rgbToHsl`
    // Converts an RGB color value to HSL.
    // *Assumes:* r, g, and b are contained in [0, 255] or [0, 1]
    // *Returns:* { h, s, l } in [0,1]
    function rgbToHsl(r, g, b) {

        r = bound01(r, 255);
        g = bound01(g, 255);
        b = bound01(b, 255);

        var max = mathMax(r, g, b), min = mathMin(r, g, b);
        var h, s, l = (max + min) / 2;

        if(max == min) {
            h = s = 0; // achromatic
        }
        else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch(max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }

            h /= 6;
        }

        return { h: h, s: s, l: l };
    }

    // `hslToRgb`
    // Converts an HSL color value to RGB.
    // *Assumes:* h is contained in [0, 1] or [0, 360] and s and l are contained [0, 1] or [0, 100]
    // *Returns:* { r, g, b } in the set [0, 255]
    function hslToRgb(h, s, l) {
        var r, g, b;

        h = bound01(h, 360);
        s = bound01(s, 100);
        l = bound01(l, 100);

        function hue2rgb(p, q, t) {
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        if(s === 0) {
            r = g = b = l; // achromatic
        }
        else {
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }

        return { r: r * 255, g: g * 255, b: b * 255 };
    }

    // `rgbToHsv`
    // Converts an RGB color value to HSV
    // *Assumes:* r, g, and b are contained in the set [0, 255] or [0, 1]
    // *Returns:* { h, s, v } in [0,1]
    function rgbToHsv(r, g, b) {

        r = bound01(r, 255);
        g = bound01(g, 255);
        b = bound01(b, 255);

        var max = mathMax(r, g, b), min = mathMin(r, g, b);
        var h, s, v = max;

        var d = max - min;
        s = max === 0 ? 0 : d / max;

        if(max == min) {
            h = 0; // achromatic
        }
        else {
            switch(max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return { h: h, s: s, v: v };
    }

    // `hsvToRgb`
    // Converts an HSV color value to RGB.
    // *Assumes:* h is contained in [0, 1] or [0, 360] and s and v are contained in [0, 1] or [0, 100]
    // *Returns:* { r, g, b } in the set [0, 255]
     function hsvToRgb(h, s, v) {

        h = bound01(h, 360) * 6;
        s = bound01(s, 100);
        v = bound01(v, 100);

        var i = math.floor(h),
            f = h - i,
            p = v * (1 - s),
            q = v * (1 - f * s),
            t = v * (1 - (1 - f) * s),
            mod = i % 6,
            r = [v, q, p, p, t, v][mod],
            g = [t, v, v, q, p, p][mod],
            b = [p, p, t, v, v, q][mod];

        return { r: r * 255, g: g * 255, b: b * 255 };
    }

    // `rgbToHex`
    // Converts an RGB color to hex
    // Assumes r, g, and b are contained in the set [0, 255]
    // Returns a 3 or 6 character hex
    function rgbToHex(r, g, b, allow3Char) {

        var hex = [
            pad2(mathRound(r).toString(16)),
            pad2(mathRound(g).toString(16)),
            pad2(mathRound(b).toString(16))
        ];

        // Return a 3 character hex if possible
        if (allow3Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1)) {
            return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0);
        }

        return hex.join("");
    }
        // `rgbaToHex`
        // Converts an RGBA color plus alpha transparency to hex
        // Assumes r, g, b and a are contained in the set [0, 255]
        // Returns an 8 character hex
        function rgbaToHex(r, g, b, a) {

            var hex = [
                pad2(convertDecimalToHex(a)),
                pad2(mathRound(r).toString(16)),
                pad2(mathRound(g).toString(16)),
                pad2(mathRound(b).toString(16))
            ];

            return hex.join("");
        }

    // `equals`
    // Can be called with any tinycolor input
    tinycolor.equals = function (color1, color2) {
        if (!color1 || !color2) { return false; }
        return tinycolor(color1).toRgbString() == tinycolor(color2).toRgbString();
    };
    tinycolor.random = function() {
        return tinycolor.fromRatio({
            r: mathRandom(),
            g: mathRandom(),
            b: mathRandom()
        });
    };


    // Modification Functions
    // ----------------------
    // Thanks to less.js for some of the basics here
    // <https://github.com/cloudhead/less.js/blob/master/lib/less/functions.js>

    function desaturate(color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.s -= amount / 100;
        hsl.s = clamp01(hsl.s);
        return tinycolor(hsl);
    }

    function saturate(color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.s += amount / 100;
        hsl.s = clamp01(hsl.s);
        return tinycolor(hsl);
    }

    function greyscale(color) {
        return tinycolor(color).desaturate(100);
    }

    function lighten (color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.l += amount / 100;
        hsl.l = clamp01(hsl.l);
        return tinycolor(hsl);
    }

    function brighten(color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var rgb = tinycolor(color).toRgb();
        rgb.r = mathMax(0, mathMin(255, rgb.r - mathRound(255 * - (amount / 100))));
        rgb.g = mathMax(0, mathMin(255, rgb.g - mathRound(255 * - (amount / 100))));
        rgb.b = mathMax(0, mathMin(255, rgb.b - mathRound(255 * - (amount / 100))));
        return tinycolor(rgb);
    }

    function darken (color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.l -= amount / 100;
        hsl.l = clamp01(hsl.l);
        return tinycolor(hsl);
    }

    // Spin takes a positive or negative amount within [-360, 360] indicating the change of hue.
    // Values outside of this range will be wrapped into this range.
    function spin(color, amount) {
        var hsl = tinycolor(color).toHsl();
        var hue = (mathRound(hsl.h) + amount) % 360;
        hsl.h = hue < 0 ? 360 + hue : hue;
        return tinycolor(hsl);
    }

    // Combination Functions
    // ---------------------
    // Thanks to jQuery xColor for some of the ideas behind these
    // <https://github.com/infusion/jQuery-xcolor/blob/master/jquery.xcolor.js>

    function complement(color) {
        var hsl = tinycolor(color).toHsl();
        hsl.h = (hsl.h + 180) % 360;
        return tinycolor(hsl);
    }

    function triad(color) {
        var hsl = tinycolor(color).toHsl();
        var h = hsl.h;
        return [
            tinycolor(color),
            tinycolor({ h: (h + 120) % 360, s: hsl.s, l: hsl.l }),
            tinycolor({ h: (h + 240) % 360, s: hsl.s, l: hsl.l })
        ];
    }

    function tetrad(color) {
        var hsl = tinycolor(color).toHsl();
        var h = hsl.h;
        return [
            tinycolor(color),
            tinycolor({ h: (h + 90) % 360, s: hsl.s, l: hsl.l }),
            tinycolor({ h: (h + 180) % 360, s: hsl.s, l: hsl.l }),
            tinycolor({ h: (h + 270) % 360, s: hsl.s, l: hsl.l })
        ];
    }

    function splitcomplement(color) {
        var hsl = tinycolor(color).toHsl();
        var h = hsl.h;
        return [
            tinycolor(color),
            tinycolor({ h: (h + 72) % 360, s: hsl.s, l: hsl.l}),
            tinycolor({ h: (h + 216) % 360, s: hsl.s, l: hsl.l})
        ];
    }

    function analogous(color, results, slices) {
        results = results || 6;
        slices = slices || 30;

        var hsl = tinycolor(color).toHsl();
        var part = 360 / slices;
        var ret = [tinycolor(color)];

        for (hsl.h = ((hsl.h - (part * results >> 1)) + 720) % 360; --results; ) {
            hsl.h = (hsl.h + part) % 360;
            ret.push(tinycolor(hsl));
        }
        return ret;
    }

    function monochromatic(color, results) {
        results = results || 6;
        var hsv = tinycolor(color).toHsv();
        var h = hsv.h, s = hsv.s, v = hsv.v;
        var ret = [];
        var modification = 1 / results;

        while (results--) {
            ret.push(tinycolor({ h: h, s: s, v: v}));
            v = (v + modification) % 1;
        }

        return ret;
    }

    // Utility Functions
    // ---------------------

    tinycolor.mix = function(color1, color2, amount) {
        amount = (amount === 0) ? 0 : (amount || 50);

        var rgb1 = tinycolor(color1).toRgb();
        var rgb2 = tinycolor(color2).toRgb();

        var p = amount / 100;
        var w = p * 2 - 1;
        var a = rgb2.a - rgb1.a;

        var w1;

        if (w * a == -1) {
            w1 = w;
        } else {
            w1 = (w + a) / (1 + w * a);
        }

        w1 = (w1 + 1) / 2;

        var w2 = 1 - w1;

        var rgba = {
            r: rgb2.r * w1 + rgb1.r * w2,
            g: rgb2.g * w1 + rgb1.g * w2,
            b: rgb2.b * w1 + rgb1.b * w2,
            a: rgb2.a * p  + rgb1.a * (1 - p)
        };

        return tinycolor(rgba);
    };


    // Readability Functions
    // ---------------------
    // <http://www.w3.org/TR/AERT#color-contrast>

    // `readability`
    // Analyze the 2 colors and returns an object with the following properties:
    //    `brightness`: difference in brightness between the two colors
    //    `color`: difference in color/hue between the two colors
    tinycolor.readability = function(color1, color2) {
        var c1 = tinycolor(color1);
        var c2 = tinycolor(color2);
        var rgb1 = c1.toRgb();
        var rgb2 = c2.toRgb();
        var brightnessA = c1.getBrightness();
        var brightnessB = c2.getBrightness();
        var colorDiff = (
            Math.max(rgb1.r, rgb2.r) - Math.min(rgb1.r, rgb2.r) +
            Math.max(rgb1.g, rgb2.g) - Math.min(rgb1.g, rgb2.g) +
            Math.max(rgb1.b, rgb2.b) - Math.min(rgb1.b, rgb2.b)
        );

        return {
            brightness: Math.abs(brightnessA - brightnessB),
            color: colorDiff
        };
    };

    // `readable`
    // http://www.w3.org/TR/AERT#color-contrast
    // Ensure that foreground and background color combinations provide sufficient contrast.
    // *Example*
    //    tinycolor.isReadable("#000", "#111") => false
    tinycolor.isReadable = function(color1, color2) {
        var readability = tinycolor.readability(color1, color2);
        return readability.brightness > 125 && readability.color > 500;
    };

    // `mostReadable`
    // Given a base color and a list of possible foreground or background
    // colors for that base, returns the most readable color.
    // *Example*
    //    tinycolor.mostReadable("#123", ["#fff", "#000"]) => "#000"
    tinycolor.mostReadable = function(baseColor, colorList) {
        var bestColor = null;
        var bestScore = 0;
        var bestIsReadable = false;
        for (var i=0; i < colorList.length; i++) {

            // We normalize both around the "acceptable" breaking point,
            // but rank brightness constrast higher than hue.

            var readability = tinycolor.readability(baseColor, colorList[i]);
            var readable = readability.brightness > 125 && readability.color > 500;
            var score = 3 * (readability.brightness / 125) + (readability.color / 500);

            if ((readable && ! bestIsReadable) ||
                (readable && bestIsReadable && score > bestScore) ||
                ((! readable) && (! bestIsReadable) && score > bestScore)) {
                bestIsReadable = readable;
                bestScore = score;
                bestColor = tinycolor(colorList[i]);
            }
        }
        return bestColor;
    };


    // Big List of Colors
    // ------------------
    // <http://www.w3.org/TR/css3-color/#svg-color>
    var names = tinycolor.names = {
        aliceblue: "f0f8ff",
        antiquewhite: "faebd7",
        aqua: "0ff",
        aquamarine: "7fffd4",
        azure: "f0ffff",
        beige: "f5f5dc",
        bisque: "ffe4c4",
        black: "000",
        blanchedalmond: "ffebcd",
        blue: "00f",
        blueviolet: "8a2be2",
        brown: "a52a2a",
        burlywood: "deb887",
        burntsienna: "ea7e5d",
        cadetblue: "5f9ea0",
        chartreuse: "7fff00",
        chocolate: "d2691e",
        coral: "ff7f50",
        cornflowerblue: "6495ed",
        cornsilk: "fff8dc",
        crimson: "dc143c",
        cyan: "0ff",
        darkblue: "00008b",
        darkcyan: "008b8b",
        darkgoldenrod: "b8860b",
        darkgray: "a9a9a9",
        darkgreen: "006400",
        darkgrey: "a9a9a9",
        darkkhaki: "bdb76b",
        darkmagenta: "8b008b",
        darkolivegreen: "556b2f",
        darkorange: "ff8c00",
        darkorchid: "9932cc",
        darkred: "8b0000",
        darksalmon: "e9967a",
        darkseagreen: "8fbc8f",
        darkslateblue: "483d8b",
        darkslategray: "2f4f4f",
        darkslategrey: "2f4f4f",
        darkturquoise: "00ced1",
        darkviolet: "9400d3",
        deeppink: "ff1493",
        deepskyblue: "00bfff",
        dimgray: "696969",
        dimgrey: "696969",
        dodgerblue: "1e90ff",
        firebrick: "b22222",
        floralwhite: "fffaf0",
        forestgreen: "228b22",
        fuchsia: "f0f",
        gainsboro: "dcdcdc",
        ghostwhite: "f8f8ff",
        gold: "ffd700",
        goldenrod: "daa520",
        gray: "808080",
        green: "008000",
        greenyellow: "adff2f",
        grey: "808080",
        honeydew: "f0fff0",
        hotpink: "ff69b4",
        indianred: "cd5c5c",
        indigo: "4b0082",
        ivory: "fffff0",
        khaki: "f0e68c",
        lavender: "e6e6fa",
        lavenderblush: "fff0f5",
        lawngreen: "7cfc00",
        lemonchiffon: "fffacd",
        lightblue: "add8e6",
        lightcoral: "f08080",
        lightcyan: "e0ffff",
        lightgoldenrodyellow: "fafad2",
        lightgray: "d3d3d3",
        lightgreen: "90ee90",
        lightgrey: "d3d3d3",
        lightpink: "ffb6c1",
        lightsalmon: "ffa07a",
        lightseagreen: "20b2aa",
        lightskyblue: "87cefa",
        lightslategray: "789",
        lightslategrey: "789",
        lightsteelblue: "b0c4de",
        lightyellow: "ffffe0",
        lime: "0f0",
        limegreen: "32cd32",
        linen: "faf0e6",
        magenta: "f0f",
        maroon: "800000",
        mediumaquamarine: "66cdaa",
        mediumblue: "0000cd",
        mediumorchid: "ba55d3",
        mediumpurple: "9370db",
        mediumseagreen: "3cb371",
        mediumslateblue: "7b68ee",
        mediumspringgreen: "00fa9a",
        mediumturquoise: "48d1cc",
        mediumvioletred: "c71585",
        midnightblue: "191970",
        mintcream: "f5fffa",
        mistyrose: "ffe4e1",
        moccasin: "ffe4b5",
        navajowhite: "ffdead",
        navy: "000080",
        oldlace: "fdf5e6",
        olive: "808000",
        olivedrab: "6b8e23",
        orange: "ffa500",
        orangered: "ff4500",
        orchid: "da70d6",
        palegoldenrod: "eee8aa",
        palegreen: "98fb98",
        paleturquoise: "afeeee",
        palevioletred: "db7093",
        papayawhip: "ffefd5",
        peachpuff: "ffdab9",
        peru: "cd853f",
        pink: "ffc0cb",
        plum: "dda0dd",
        powderblue: "b0e0e6",
        purple: "800080",
        rebeccapurple: "663399",
        red: "f00",
        rosybrown: "bc8f8f",
        royalblue: "4169e1",
        saddlebrown: "8b4513",
        salmon: "fa8072",
        sandybrown: "f4a460",
        seagreen: "2e8b57",
        seashell: "fff5ee",
        sienna: "a0522d",
        silver: "c0c0c0",
        skyblue: "87ceeb",
        slateblue: "6a5acd",
        slategray: "708090",
        slategrey: "708090",
        snow: "fffafa",
        springgreen: "00ff7f",
        steelblue: "4682b4",
        tan: "d2b48c",
        teal: "008080",
        thistle: "d8bfd8",
        tomato: "ff6347",
        turquoise: "40e0d0",
        violet: "ee82ee",
        wheat: "f5deb3",
        white: "fff",
        whitesmoke: "f5f5f5",
        yellow: "ff0",
        yellowgreen: "9acd32"
    };

    // Make it easy to access colors via `hexNames[hex]`
    var hexNames = tinycolor.hexNames = flip(names);


    // Utilities
    // ---------

    // `{ 'name1': 'val1' }` becomes `{ 'val1': 'name1' }`
    function flip(o) {
        var flipped = { };
        for (var i in o) {
            if (o.hasOwnProperty(i)) {
                flipped[o[i]] = i;
            }
        }
        return flipped;
    }

    // Return a valid alpha value [0,1] with all invalid values being set to 1
    function boundAlpha(a) {
        a = parseFloat(a);

        if (isNaN(a) || a < 0 || a > 1) {
            a = 1;
        }

        return a;
    }

    // Take input from [0, n] and return it as [0, 1]
    function bound01(n, max) {
        if (isOnePointZero(n)) { n = "100%"; }

        var processPercent = isPercentage(n);
        n = mathMin(max, mathMax(0, parseFloat(n)));

        // Automatically convert percentage into number
        if (processPercent) {
            n = parseInt(n * max, 10) / 100;
        }

        // Handle floating point rounding errors
        if ((math.abs(n - max) < 0.000001)) {
            return 1;
        }

        // Convert into [0, 1] range if it isn't already
        return (n % max) / parseFloat(max);
    }

    // Force a number between 0 and 1
    function clamp01(val) {
        return mathMin(1, mathMax(0, val));
    }

    // Parse a base-16 hex value into a base-10 integer
    function parseIntFromHex(val) {
        return parseInt(val, 16);
    }

    // Need to handle 1.0 as 100%, since once it is a number, there is no difference between it and 1
    // <http://stackoverflow.com/questions/7422072/javascript-how-to-detect-number-as-a-decimal-including-1-0>
    function isOnePointZero(n) {
        return typeof n == "string" && n.indexOf('.') != -1 && parseFloat(n) === 1;
    }

    // Check to see if string passed in is a percentage
    function isPercentage(n) {
        return typeof n === "string" && n.indexOf('%') != -1;
    }

    // Force a hex value to have 2 characters
    function pad2(c) {
        return c.length == 1 ? '0' + c : '' + c;
    }

    // Replace a decimal with it's percentage value
    function convertToPercentage(n) {
        if (n <= 1) {
            n = (n * 100) + "%";
        }

        return n;
    }

    // Converts a decimal to a hex value
    function convertDecimalToHex(d) {
        return Math.round(parseFloat(d) * 255).toString(16);
    }
    // Converts a hex value to a decimal
    function convertHexToDecimal(h) {
        return (parseIntFromHex(h) / 255);
    }

    var matchers = (function() {

        // <http://www.w3.org/TR/css3-values/#integers>
        var CSS_INTEGER = "[-\\+]?\\d+%?";

        // <http://www.w3.org/TR/css3-values/#number-value>
        var CSS_NUMBER = "[-\\+]?\\d*\\.\\d+%?";

        // Allow positive/negative integer/number.  Don't capture the either/or, just the entire outcome.
        var CSS_UNIT = "(?:" + CSS_NUMBER + ")|(?:" + CSS_INTEGER + ")";

        // Actual matching.
        // Parentheses and commas are optional, but not required.
        // Whitespace can take the place of commas or opening paren
        var PERMISSIVE_MATCH3 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";
        var PERMISSIVE_MATCH4 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";

        return {
            rgb: new RegExp("rgb" + PERMISSIVE_MATCH3),
            rgba: new RegExp("rgba" + PERMISSIVE_MATCH4),
            hsl: new RegExp("hsl" + PERMISSIVE_MATCH3),
            hsla: new RegExp("hsla" + PERMISSIVE_MATCH4),
            hsv: new RegExp("hsv" + PERMISSIVE_MATCH3),
            hsva: new RegExp("hsva" + PERMISSIVE_MATCH4),
            hex3: /^([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
            hex6: /^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,
            hex8: /^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/
        };
    })();

    // `stringInputToObject`
    // Permissive string parsing.  Take in a number of formats, and output an object
    // based on detected format.  Returns `{ r, g, b }` or `{ h, s, l }` or `{ h, s, v}`
    function stringInputToObject(color) {

        color = color.replace(trimLeft,'').replace(trimRight, '').toLowerCase();
        var named = false;
        if (names[color]) {
            color = names[color];
            named = true;
        }
        else if (color == 'transparent') {
            return { r: 0, g: 0, b: 0, a: 0, format: "name" };
        }

        // Try to match string input using regular expressions.
        // Keep most of the number bounding out of this function - don't worry about [0,1] or [0,100] or [0,360]
        // Just return an object and let the conversion functions handle that.
        // This way the result will be the same whether the tinycolor is initialized with string or object.
        var match;
        if ((match = matchers.rgb.exec(color))) {
            return { r: match[1], g: match[2], b: match[3] };
        }
        if ((match = matchers.rgba.exec(color))) {
            return { r: match[1], g: match[2], b: match[3], a: match[4] };
        }
        if ((match = matchers.hsl.exec(color))) {
            return { h: match[1], s: match[2], l: match[3] };
        }
        if ((match = matchers.hsla.exec(color))) {
            return { h: match[1], s: match[2], l: match[3], a: match[4] };
        }
        if ((match = matchers.hsv.exec(color))) {
            return { h: match[1], s: match[2], v: match[3] };
        }
        if ((match = matchers.hsva.exec(color))) {
            return { h: match[1], s: match[2], v: match[3], a: match[4] };
        }
        if ((match = matchers.hex8.exec(color))) {
            return {
                a: convertHexToDecimal(match[1]),
                r: parseIntFromHex(match[2]),
                g: parseIntFromHex(match[3]),
                b: parseIntFromHex(match[4]),
                format: named ? "name" : "hex8"
            };
        }
        if ((match = matchers.hex6.exec(color))) {
            return {
                r: parseIntFromHex(match[1]),
                g: parseIntFromHex(match[2]),
                b: parseIntFromHex(match[3]),
                format: named ? "name" : "hex"
            };
        }
        if ((match = matchers.hex3.exec(color))) {
            return {
                r: parseIntFromHex(match[1] + '' + match[1]),
                g: parseIntFromHex(match[2] + '' + match[2]),
                b: parseIntFromHex(match[3] + '' + match[3]),
                format: named ? "name" : "hex"
            };
        }

        return false;
    }

    window.tinycolor = tinycolor;
    })();

    $(function () {
        if ($.fn.spectrum.load) {
            $.fn.spectrum.processNativeColorInputs();
        }
    });

});
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*SCHEDULER VERSION 1.1.0 SCRIPTS*/

function dgi(id){return document.getElementById(id);}
function dgn(name){return document.getElementsByName(name);}


//loading images
var edit_appt_img;
var add_appt_img;
var hid_appt_img;
var datesRange;
var proc_lbls_arr;
var owl='';
// global patient change controller for primary phy. selection
var gl_pt_ch_ct='';
var pri_phy_chk_flag=1;

var cur_first_month = '';

var ga_ap_id='';
var ga_st_type='';
var ga_sel_date='';
var ga_sel_fac='';
var ga_pt_id='';

var firstAvail_keepOrg=1;
/*
Function: preloader
Purpose: to pre load images in memoryy
Author: AA
*/
function preloader() {
	rel_url = "../../";
	if(typeof(top.JS_WEB_ROOT_PATH)!='undefined') rel_url = top.JS_WEB_ROOT_PATH+"/";
	//edit appt image
	edit_appt_img = new Image();
	edit_appt_img.src = rel_url+"library/images/b_edit.png";
	edit_appt_img.id = "TestImage";
	
	//add appt image
	add_appt_img = new Image();
	add_appt_img.src=rel_url+"library/images/add_appoint.gif";

	//hidden appt image
	hid_appt_img = new Image();
	hid_appt_img.src=rel_url+"library/images/grippy.gif";
}

//setting image paths
//preloader();

/*
Function: image_replace
Purpose: to render images on interface
Author: AA
*/
function image_replace(){

	var obj_edit_appt_img = document.getElementsByName("edit_appt_img");
	if(edit_appt_img){
		for(var i = 0; i < obj_edit_appt_img.length; i++){
			obj_edit_appt_img[i].src = edit_appt_img.src;
		}
	}

	var obj_add_appt_img = document.getElementsByName("addImage");
	if(add_appt_img){
		for(var i = 0; i < obj_add_appt_img.length; i++){
			obj_add_appt_img[i].src = add_appt_img.src;
		}
	}

	var obj_hid_appt_img = document.getElementsByName("grippy");
	if(hid_appt_img){
		for(var i = 0; i < obj_hid_appt_img.length; i++){
			obj_hid_appt_img[i].src = hid_appt_img.src;
		}
	}
}

/*
Function: fac_change_load
Purpose: Combined actions to be performed when loading scheduler base on change in facility
Author: AA
*/
function fac_change_load(mode){
	var sel_date = get_selected_date();
	var arr_sel_date = sel_date.split("-"); //ymd
	sel_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0];
	day_sel_date = arr_sel_date[0]+"-"+arr_sel_date[1]+"-"+arr_sel_date[2];
	
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();	
	if(mode == "day"){
		//alert("load day appt shedule based on these facilities...");
		$.ajax({
			url: "get_day_name.php?load_dt="+day_sel_date,
			success: function(day_name){
				//loading scheduler
				load_calendar(day_sel_date, day_name, '', false);
				datesRange='';
				collect_labels_by_provider();						
								
			}
		});
	}else if(mode == "week"){
		load_week_appt_schedule();
		//top.fmain.document.location.href = "base_week_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}else if(mode == "month"){
		top.fmain.document.location.href = "base_month_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}
}

/*
Function: pro_change_load
Purpose: Combined actions to be performed when loading scheduler base on change in provider
Author: AA
*/
function pro_change_load(mode){	
	var sel_date = get_selected_date();

	var arr_sel_date = sel_date.split("-"); //ymd
	sel_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0];
	day_sel_date = arr_sel_date[0]+"-"+arr_sel_date[1]+"-"+arr_sel_date[2];
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	if(mode == "day"){
		//alert("load day appt shedule based on these providers...");
		$.ajax({
			url: "get_day_name.php?load_dt="+day_sel_date,
			success: function(day_name){
				//loading scheduler
				load_calendar(day_sel_date, day_name, '', false);
				datesRange='';
				collect_labels_by_provider();						
			}
		});
	}else if(mode == "week"){
		load_week_appt_schedule();
		//top.fmain.document.location.href = "base_week_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}else if(mode == "month"){
		top.fmain.document.location.href = "base_month_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}	
}

function load_week_appt_schedule(selected_sess_facs, selected_sess_prov){
	if(!selected_sess_prov) selected_sess_prov = "";
	//alert(selected_sess_prov);
	//show loading image
	top.show_loading_image("show");
	
	var sel_date = get_selected_date();
	if(selected_sess_prov != ""){
		var sel_pro_month = selected_sess_prov;
	}else{
		var sel_pro_month = selectedValuesStr("sel_pro_month");
	}
	if(selected_sess_facs){
		var facilities = selected_sess_facs;
	}else{
		var facilities = selectedValuesStr("facilities");
	}	
	//alert("appt_week_load.php?dt="+sel_date+"&sel_pro_month="+sel_pro_month+"&facilities="+facilities);

	$.ajax({
		url: "appt_week_load.php?dt="+sel_date+"&sel_pro_month="+sel_pro_month+"&facilities="+facilities,
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~~~~");
			//loading week scheduler
			$("#scroll_controls").hide();
			document.getElementById("week_save").innerHTML = arr_resp[0];
			//alert(arr_resp[1]);
			if(arr_resp[1]){
			$("#scroll_controls").css('display','inline-block');
				
				var dateObj = new Date();
				var cur_hr = dateObj.getHours();
				
				image_replace();
			}
			//show loading image
			top.show_loading_image("hide");
			//change main div width for horizental scrolling
			$('#week_save').css({
					'width':parseInt(window.screen.availWidth)-20
				});
		}
	});
}

function to_do(JS_SCHEDULER_VERSION){
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var file_path='../'+JS_SCHEDULER_VERSION+'/to_do_first_avai.php';
	if($("#global_apptact").val() == "reschedule")
	{
		hide_tool_tip();		
		change_status('201');
		file_path='../'+JS_SCHEDULER_VERSION+'/to_do.php';
	}
	window.open(file_path,'to_do','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+parseInt(parentWid-50)+'px,height=780px,left=10px,top=100px');			
}

function appt_cancel_portal(JS_SCHEDULER_VERSION){
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var file_path='../'+JS_SCHEDULER_VERSION+'/appt_cancel_portal.php?apptload=1';
	/*
	if($("#global_apptact").val() == "reschedule")
	{
		//hide_tool_tip();		
		//change_status('201');
		//file_path='../'+JS_SCHEDULER_VERSION+'/appt_cancel_portal.php';
	}
	*/
	window.open(file_path,'appt_cancel_portal','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+parseInt(parentWid-50)+'px,height=780px,left=10px,top=100px');			
}

function change_date(mode, load_this_date){
	if(!mode) mode = "current";
	if(!load_this_date) load_this_date = "";	
	
	$("#month_h_disable").css("display", "block");

	var sel_date = get_selected_date();
	var arr_sel_date = sel_date.split("-"); //ymd
	sel_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0];

	if(mode == "this_date"){
		//alert(load_this_date);
		var arr_temp = load_this_date.split("|");
		//alert(arr_temp[0]);
		var arr_load_this_dt = arr_temp[0].split("-");		
		var load_this_dt = arr_sel_date[1];
		//alert(load_this_dt);
		if(arr_sel_date[2] > 28 && arr_load_this_dt[0] == 2){
			load_this_dt = 28;
		}		
		load_this_date = arr_load_this_dt[2]+"-"+arr_load_this_dt[0]+"-"+load_this_dt;
		//alert(load_this_date);
	}

	var inc_dec_no = document.getElementById("jmpto").value;
	var inc_dec_mode = document.getElementById("op_typ").value;
	//alert("get_change_date.php?load_dt="+sel_date+"&load_dt_mode="+mode+"&inc_dec_no="+inc_dec_no+"&inc_dec_mode="+inc_dec_mode+"&load_this_date="+load_this_date);
	$.ajax({
		url: "get_change_date.php?load_dt="+sel_date+"&load_dt_mode="+mode+"&inc_dec_no="+inc_dec_no+"&inc_dec_mode="+inc_dec_mode+"&load_this_date="+load_this_date,
		success: function(resp){
			
			var arr_resp = resp.split(";;");
			var load_dt = arr_resp[0];
			var load_day = arr_resp[1];
			
			var dd_option = arr_resp[2];
			
			//if(document.getElementById("sel_month_year_container")){
			//	document.getElementById("sel_month_year_container").innerHTML = "<select id=\"sel_month_year\" name=\"sel_month_year\" onChange=\"change_date('this_date', this.value);\" >"+dd_option+"</select>";
			//}
			
			//loading scheduler
			//alert(load_dt+", "+load_day);
			load_calendar(load_dt, load_day, '', false);
		}
	});
}

/*
Function: fresh_load
Purpose: Combined actions to be performed when loading scheduler base
Author: AA
*/
function fresh_load(load_dt, pt_id, selected_sess_facs, selected_sess_prov){
	
	if(!load_dt) load_dt = "";
	if(!pt_id) pt_id = "";
	if(!selected_sess_facs) selected_sess_facs = "";
	if(!selected_sess_prov) selected_sess_prov = "";
	
	if(load_dt != ""){

		//scheduler has been opened
		top.$('#appt_scheduler_status').val('loaded');
		
		//setting patient id if any
		document.getElementById("global_ptid").value = pt_id;

		//selecting facility from session, if any
		set_facilities(selected_sess_facs);

		//selecting providers from session, if any
		set_providers(selected_sess_prov);

		//hide buttons
		//top.btn_show();--------------------commented

		//refresh title bar
		//top.refresh_control_panel("Patient_Info",pt_id);--------------------commented

		//todo button
		if(top.document.getElementById("tl_2to")){
			top.document.getElementById("tl_2to").style.display = 'block';
		}

		$.ajax({
			url: "get_day_name.php?load_dt="+load_dt,
			success: function(day_name){
				//hide loading image
				top.show_loading_image("hide");

				//loading scheduler
				load_calendar(load_dt, day_name);
			}
		});
	}else{
		top.fAlert("Invalid date.");
		return false;
	}
}

/*
Function: get_selected_facilities
Purpose: to get selectd facilites by the user
Author: AA
*/
function get_selected_facilities(){
	if($("#facilities").val()){
		return ($("#facilities").val()).join(",");
	}else return false;
}

/*
Function: set_facilities
Purpose: to select facilites
Author: AA
*/
function set_facilities(selected_sess_facs){
	if(!selected_sess_facs) selected_sess_facs = "";
	var selectbox = document.getElementById('facilities');
	if(selected_sess_facs != ""){
		var arr_selected_sess_facs = selected_sess_facs.split(",");
		for ( var i = 0, l = selectbox.options.length, o; i < l; i++ ){ 
			o = selectbox.options[i];
			var bl_fac_add = false;
			for(var j = 0; j < arr_selected_sess_facs.length; j++){
				if(o.value == arr_selected_sess_facs[j]){
					bl_fac_add = true;
					break;
				}
			}
			if(bl_fac_add == true){
				o.selected = true;
			}
		}
	}else{
		for ( var i = 0, l = selectbox.options.length, o; i < l; i++ ){  
			o = selectbox.options[i];  
			o.selected = true;
		}
	}
}

/*
Function: get_selected_providers
Purpose: to get selectd providers by the user
Author: AA
*/
function get_selected_providers(){
	return selectedValuesStr("sel_pro_month");
}

/*
Function: get_selected_providers
Purpose: to get selectd providers by the user
Author: AA
*/
function selectedValuesStr(div_id){
	if($("#"+div_id).val()){
	return ($("#"+div_id).val()).join(",");
	}else return false;
}

/*
Function: set_providers
Purpose: to select providers
Author: AA
*/
function set_providers(selected_sess_prov){
	var selectbox2 = document.getElementById('sel_pro_month');
	var selectbox_l = document.getElementById('provider_label');
	if(selected_sess_prov != ""){
		var arr_selected_sess_prov = selected_sess_prov.split(",");
		$("#sel_pro_month option").each(function(id,elem){
			var value = $(elem).val();
			if(value.length > 0 || typeof(value) != 'undefined'){
				if($.inArray(value,arr_selected_sess_prov)!=-1){
					$(elem).prop('selected',true);
				}
			}
		});
		//$("#sel_pro_month").selectpicker("val",array);
		$("#sel_pro_month").selectpicker("refresh");

		/*for(var j = 0; j < arr_selected_sess_prov.length; j++){
			$('select[id=sel_pro_month]').val(arr_selected_sess_prov[j]);
			$("#sel_pro_month").selectpicker("refresh");
		}*/
	
	}else if(selected_sess_prov == "-1"){
		//null
	}else{
		for ( var i = 0, l = selectbox2.options.length, o; i < l; i++ ){  
			o = selectbox2.options[i]; 
			ol=selectbox_l.options[i]; 
			o.selected = true;
			ol.selected = true;
		}
	}
	$("#sel_pro_month").selectpicker("refresh");
}

/*
Function: get_selected_date
Purpose: to get selectd date by the user
Author: AA
*/
function get_selected_date(){
	var dt = document.getElementById("global_date").value;
	var mn = document.getElementById("global_month").value;
	var yr = document.getElementById("global_year").value;	
	
	//adjustment for feb month
	var adj_dt = ((yr % 4) == 0) ? 29 : 28;
	if(dt > adj_dt && mn == 2){
		dt = adj_dt;
	}
	return yr+"-"+mn+"-"+dt;
}

/*
Function: set_date
Purpose: to set global date 
Author: AA
*/
function set_date(yr, mn, dt){
	document.getElementById("global_year").value = yr;
	document.getElementById("global_month").value = mn;
	document.getElementById("global_date").value = dt;	
}

function get_dt_div_obj_nm(dt, mn){
	var returnval = "dtblk-fl-cl_hili-curr_"+parseInt(dt)+"_"+parseInt(mn);
	if($("#"+returnval).get(0)){
		return returnval; 
	}
	var returnval = "dtblk-fl-cl_s_d-curr_"+parseInt(dt)+"_"+parseInt(mn);
	if($("#"+returnval).get(0)){
		return returnval; 
	}
	var returnval = "dtblk-fl-cl_d_d-curr_"+parseInt(dt)+"_"+parseInt(mn);
	if($("#"+returnval).get(0)){
		return returnval;
	}
}

/*
Function: load_calendar
Purpose: to load calendar
Author: AA
Arguments: load_dt - Y-m-d format
*/
function load_calendar(load_dt, day_name, load_appt, showAlert, int_appt){	
	if(!load_appt) load_appt = "";
	if(!int_appt) int_appt = "";
	//alert(int_appt);
	if(typeof(showAlert) == "undefined") showAlert = true;
	
	//show loading image
	top.show_loading_image("show");
	
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	
	//getting month
	var str_sel_date = $("#sel_month_year").val()
	var arr_sel_date = str_sel_date.split("-");
	
	//getting date to deselect
	var str_hg_date = get_selected_date();
	var arr_hg_date = str_hg_date.split("-");
	var hl_box_id = get_dt_div_obj_nm(arr_hg_date[2], arr_hg_date[1]);
	
	if(hl_box_id){
		var arr_hl_box_id = hl_box_id.split("-");
		if(document.getElementById(hl_box_id)){
			document.getElementById(hl_box_id).className = arr_hl_box_id[1]+" "+arr_hl_box_id[2];
		}
	}

	//setting date
	var arr_date = load_dt.split("-");

	//getting selectd month
	var curr_month_val = $("#loaded_first_month").val();
	var arr_curr_month_val = curr_month_val.split("-");
	var curr_month = parseInt(arr_curr_month_val[1]);
	var curr_year = parseInt(arr_curr_month_val[0]);

	set_date(arr_date[0], arr_date[1], arr_date[2]);
	//alert("get_cal_month_view.php?sel_dat="+load_dt+"&sel_pro="+sel_pro+"&sel_fac="+sel_fac+"&curr_month="+curr_month+"&curr_year="+curr_year);
	$.ajax({
		url: "get_cal_month_view.php?int_appt="+int_appt+"&sel_dat="+load_dt+"&sel_pro="+sel_pro+"&sel_fac="+sel_fac+"&curr_month="+curr_month+"&curr_year="+curr_year,
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~~~~");

			if($.trim(arr_resp[0]) != "nonono"){
				//alert("here");
				document.getElementById("month_h").innerHTML = arr_resp[0];
				$("#loaded_first_month").val(arr_resp[1]);
			}else{
				//alert("there");
			}
			
			//loading nav bar
			load_navigation_bar(day_name);
			
			//highlighting selected date
			var hl_box_id = get_dt_div_obj_nm(arr_date[2], arr_date[1]);
			//var hl_box_id = "dtblk_curr_"+parseInt(arr_date[2])+"_"+parseInt(arr_date[1]);
			if(document.getElementById(hl_box_id)){
				document.getElementById(hl_box_id).className = "fl cl_hili";
			}

			//highlighting date on based on provider schedules
			var arr_sel_pro = "";
			if(sel_pro){arr_sel_pro =sel_pro.split(",");}
			
			//adding new funtion to exclude testing providers -------------------
			
			// code for selection of dates by single provider
			if(arr_sel_pro.length == 1){
				if(arr_sel_pro[0] != ""){
					highlight_provider_schedules(arr_sel_pro[0]);
				}else{
					reset_highlighted_schedules();
				}
			}else{
				var uri='validateProviders.php?p='+sel_pro;
			
			$.ajax({
					url:uri,
					complete:function(respData){
					no_of_providers= respData.responseText;
					var no_of_providers_arr=no_of_providers.split(',');
			
					if(no_of_providers_arr.length == 2)
					{
						if(no_of_providers_arr[0] != "" && no_of_providers_arr[1] != "")
						{
							highlight_provider_schedules(no_of_providers_arr);
						}else{
							reset_highlighted_schedules();
						}
					}
					else
					{
						reset_highlighted_schedules();
					}
				}
			});	
			}
			
			
			
			
			//-------------------------------------------------------------------
	
            // code for selection of dates by single provider
			
			//--- THIS CODE IS COMMENTED AO APPLY NEW SINGLE + ONE OR MANY TESTING PROVIDERS-------
			
			/*if(arr_sel_pro.length == 1)
			{
				if(arr_sel_pro[0] != "")
				{
					highlight_provider_schedules(arr_sel_pro[0]);
				}else{
					reset_highlighted_schedules();
				}
			}
			else
			{
				reset_highlighted_schedules();
			}*/
			//--------------------------------------------------------------------------------------
			
			
			// code for selection of dates by multiple provider
           /*                                    
			if(arr_sel_pro.length > 0){                                                    
                                                    highlight_provider_schedules(arr_sel_pro);
                                                           
			}else{
				reset_highlighted_schedules();
			}
			*/
			if(load_appt == ""){
				//loading appt schedule
				load_appt_schedule(load_dt, day_name, '', '', showAlert);
			}else{
				top.show_loading_image("hide");
			}

		}
	});
        
}

function show_schedule_details(obj_name, e){
	var sch_details = $("#"+obj_name).html();
	
	if(!e) e = window.event || event;
	else e = e || window.event || event;
	
	eve_obj=e;
	
	if(sch_details != ""){
		$("#show_highlighted_prov_sch").html(sch_details);
		display_block_none("show_highlighted_prov_sch", "block");
		//document.getElementById("show_highlighted_prov_sch").style.width = 90;
		document.getElementById("show_highlighted_prov_sch").style.position = 'absolute';
		document.getElementById("show_highlighted_prov_sch").style.display = 'block';
		document.getElementById("show_highlighted_prov_sch").style.zIndex = 999;
		document.getElementById("show_highlighted_prov_sch").style.pixelLeft = eve_obj.clientX + 25;		
		document.getElementById("show_highlighted_prov_sch").style.pixelTop = eve_obj.clientY + 25;
		
		var bro_ver=navigator.userAgent.toLowerCase();
		//if browser is crhome or firfox or safari then we need to placement issue
		if(bro_ver.search("chrome")>1 || bro_ver.search("firefox")>1){
			$("#show_highlighted_prov_sch").css({"display":"inline-block",top: eve_obj.clientY+25, left: eve_obj.clientX+25});
			
		}
		
		
	}else{
		hide_schedule_details();
	}
}

function hide_schedule_details(){
	$("#show_highlighted_prov_sch").html("");
	display_block_none("show_highlighted_prov_sch", "none");
}

function reset_highlighted_schedules(){
	var working_day_dt = get_selected_date();
	var arr_w_dt = working_day_dt.split("-");
	var loaded_month = $("#loaded_first_month").val();
	
	var uri = "reset_highlighted_schedules.php?loaded_month="+loaded_month;
	//alert(uri);

	$.ajax({
		url: uri,
		success: function(resp){
			
			var arr_resp = resp.split(":~:~:");

			for(divs = 0; divs < arr_resp.length - 1; divs++){
				var str_this_div = arr_resp[divs];
				var arr_this_div = str_this_div.split("~~~");
				var arr_t_dt = arr_this_div[2].split("_");
				//alert(arr_this_div[0]+ " " +arr_this_div[1] + " " + arr_this_div[2] + " " + arr_this_div[3]);
				//alert(parseInt(arr_w_dt[0])+" == "+parseInt(arr_t_dt[1])+" && "+parseInt(arr_w_dt[1])+" == "+parseInt(arr_t_dt[2]));
				if(parseInt(arr_w_dt[2]) == parseInt(arr_t_dt[1]) && parseInt(arr_w_dt[1]) == parseInt(arr_t_dt[2])){
					//alert("thenga");
				}else{
					if($("#"+arr_this_div[0]).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(arr_this_div[0]).className = "fl cl_s_d";
					}
					if($("#"+arr_this_div[1]).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(arr_this_div[1]).className = "fl cl_d_d";
					}
					
					//checking last month dates
					var last_normal = arr_this_div[0].replace("curr", "last");
					//checking for default class
					var last_normal_plus = last_normal.replace("cl_s_d", "cl_p_d");
					
					if($("#"+last_normal_plus).get(0)){
						document.getElementById(last_normal_plus).className = "fl cl_d_d";
					}
				}
				
				//overwrite color if we do have facility color value
				if($("#"+arr_this_div[0]).get(0))
				$("#"+arr_this_div[0]).css("background-color", '');
				
				if($("#"+arr_this_div[1]).get(0))
				$("#"+arr_this_div[1]).css("background-color", '');
				
				if($("#"+last_normal_plus).get(0))
				$("#"+last_normal_plus).css("background-color", '');
						
				document.getElementById(arr_this_div[2]).innerHTML = "";
			}
			$("#month_h_disable").css("display", "none");
            highLightDatesByLabels();    		                                
		}
	});
}

/*
Function: highlight_provider_schedules
Purpose: to highlight dates with purple color based on provider schdules
Author: AA
*/
function highlight_provider_schedules(sel_pro){
	
	var working_day_dt = get_selected_date();
	var arr_w_dt = working_day_dt.split("-");
	var loca = get_selected_facilities();
	var loaded_month = $("#loaded_first_month").val();
	var uri = "highlight_provider_schedules.php?working_day_dt="+working_day_dt+"&loca="+loca+"&prov_id="+sel_pro+"&loaded_month="+loaded_month;
	
	$.ajax({
		url: uri,
		success: function(resp){
			var arr_resp = resp.split(":~:~:");
			
			if(arr_resp.length>1)
			{
			for(divs = 0; divs < arr_resp.length - 1; divs++){
				var str_this_div = arr_resp[divs];
				var arr_this_div = str_this_div.split("~~~");
				var arr_t_dt = arr_this_div[2].split("_");
				//alert(arr_this_div[0]+ " " +arr_this_div[1] + " " + arr_this_div[2] + " " + arr_this_div[3]);
				//alert(parseInt(arr_w_dt[0])+" == "+parseInt(arr_t_dt[1])+" && "+parseInt(arr_w_dt[1])+" == "+parseInt(arr_t_dt[2]));
				if(parseInt(arr_w_dt[2]) == parseInt(arr_t_dt[1]) && parseInt(arr_w_dt[1]) == parseInt(arr_t_dt[2])){
					//alert("thenga");
				}else{
                                    
					var detail_div_name = "";
					if($("#"+arr_this_div[0]).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(arr_this_div[0]).className = "fl cl_s_d";
						detail_div_name = arr_this_div[2];
					}
					if($("#"+arr_this_div[1]).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(arr_this_div[1]).className = "fl cl_d_d";
						detail_div_name = arr_this_div[2];
					}

					//checking last month dates
					var last_normal = arr_this_div[0].replace("curr", "last");
					var last_special = arr_this_div[1].replace("curr", "last");
					
					//checking for default class for last month
					var last_normal_plus = last_normal.replace("cl_s_d", "cl_p_d");
					
					if($("#"+last_normal_plus).get(0)){
						document.getElementById(last_normal_plus).className = "fl cl_d_d";//overwrite color if we do have facility color value
						if(arr_this_div[5])
						{
							$("#"+last_normal_plus).css("background-color", arr_this_div[5]);
						}
					}
						
					if($("#"+last_normal).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(arr_this_div[0]).className = "fl cl_s_d";
						detail_div_name = arr_this_div[2].replace("curr", "last");
					}
					if($("#"+last_special).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(arr_this_div[1]).className = "fl cl_d_d";
						detail_div_name = arr_this_div[2].replace("curr", "last");
					}
	
					//checking next month dates
					var next_normal = arr_this_div[0].replace("curr", "next");
					var next_special = arr_this_div[1].replace("curr", "next");
					if($("#"+next_normal).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(next_normal).className = "fl cl_s_d";
						if(arr_this_div[5])
						{
							$("#"+next_normal).css("background-color", arr_this_div[5]);
						}
						detail_div_name = arr_this_div[2].replace("curr", "next");
					}
					if($("#"+next_special).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(next_special).className = "fl cl_d_d";
						if(arr_this_div[5])
						{
							$("#"+next_special).css("background-color", arr_this_div[5]);
						}
						detail_div_name = arr_this_div[2].replace("curr", "next");
					}
                                        
				}
				if(detail_div_name != "")
				$('#'+detail_div_name).html('');
					// document.getElementById(detail_div_name).innerHTML = "";
				
				//overwrite color if we do have facility color value
				if(arr_this_div[5])
				{
					if($("#"+arr_this_div[0]).get(0))$("#"+arr_this_div[0]).css("background-color", arr_this_div[5]);
					if($("#"+arr_this_div[1]).get(0))$("#"+arr_this_div[1]).css("background-color", arr_this_div[5]);
					if($("#"+arr_this_div[2]).get(0))$("#"+arr_this_div[2]).css("background-color", arr_this_div[5]);
					
				}
			}
			
			for(divs = 0; divs < arr_resp.length - 1; divs++){
				var str_this_div = arr_resp[divs];
				var arr_this_div = str_this_div.split("~~~");
				var arr_t_dt = arr_this_div[2].split("_");
				var show_ob_alert = arr_this_div[4];
				if(arr_this_div[4] == "default"){
					var set_class = "fl cl_h_d";
				}else if(arr_this_div[4] == "exceed_appt"){
					var set_class ="fl cl_exceed_appt_d";
				}else{
					var set_class = "fl cl_a_d";
				}
				//alert(arr_this_div[0]+ " " +arr_this_div[1] + " " + arr_this_div[2] + " " + arr_this_div[3] + " " + arr_this_div[4]);
				//alert(parseInt(arr_w_dt[0])+" == "+parseInt(arr_t_dt[1])+" && "+parseInt(arr_w_dt[1])+" == "+parseInt(arr_t_dt[2]));
				if(parseInt(arr_w_dt[2]) == parseInt(arr_t_dt[1]) && parseInt(arr_w_dt[1]) == parseInt(arr_t_dt[2])){
					//alert("thenga");
				}else{
					if($("#"+arr_this_div[0]).get(0) && arr_this_div[3] != ""){
						document.getElementById(arr_this_div[0]).className = set_class;                                                                                                
					}
					if($("#"+arr_this_div[1]).get(0) && arr_this_div[3] != ""){
						document.getElementById(arr_this_div[1]).className = set_class;
					}
					
					//-------------------------------------------------------------------
					//checking last month dates - Highlight previous month dates
					var last_normal = arr_this_div[0].replace("curr", "last");
					//checking for default class
					var last_normal_plus = last_normal.replace("cl_s_d", "cl_p_d");
					
					if($("#"+last_normal_plus).get(0) && arr_this_div[3] != ""){
						document.getElementById(last_normal_plus).className = set_class;
					}
					//-------------------------------------------------------------------
					
					//overwrite color if we do have facility color value
					if(arr_this_div[5])
					{
						if($("#"+arr_this_div[0]).get(0))
						{
							$("#"+arr_this_div[0]).css("background-color", arr_this_div[5]);
						}
					}
					
				}
				document.getElementById(arr_this_div[2]).innerHTML = arr_this_div[3];
				
				
			}
                                                                        
			$("#month_h_disable").css("display", "none");

			//hLDatesByLbl(); commented on 5 sep as front end interface related to this is already removed.
			}
			else
			{
				reset_highlighted_schedules();	
			}
			/*
			use this code if the slow speed issue come
			cur_first_mnth_in_sel = $('.cl_m_h:first').html();		
			if(cur_first_month == '' || cur_first_mnth_in_sel != cur_first_month)
			{
				cur_first_month = cur_first_mnth_in_sel;
				hLDatesByLbl();  				                                 				
			}
			else
			{
				highLightDatesByLabels();
			}			
			// call function highLightDatesByLabels() only in these cases in which the month is not modified
			*/
		}
                
	});
}

function highlight_date(obj_name, cls_name,e){
        /*
	if(document.getElementById(obj_name)){
		//alert("here");
		var get_date_from_name = obj_name.split("-");
		var day_mon = get_date_from_name[3].split("_");
		var load_dt = day_mon[1];
		var load_mn = day_mon[2];

		var loaded_dt = get_selected_date();
		var arr_loaded_dt = loaded_dt.split("-");

		//alert(get_date_from_name+" "+day_mon+" "+load_dt+" == "+arr_loaded_dt[2]+" && "+load_mn+" == "+arr_loaded_dt[1]);
		
		if(parseInt(load_dt) == parseInt(arr_loaded_dt[2]) && parseInt(load_mn) == parseInt(arr_loaded_dt[1])){
			//thenga
		}else{
			//alert(document.getElementById(obj_name).className);
			if(document.getElementById("loaded_cls").value != ""){// && obj_name == document.getElementById("loaded_cls_obj").value){
				//alert("OBJECT NAME: "+obj_name+"; CLASS TO SET: "+document.getElementById("loaded_cls").value+"; EXISTING CLASS: "+document.getElementById(obj_name).className+"; STATUS: good girl");
				document.getElementById(obj_name).className = document.getElementById("loaded_cls").value;
				document.getElementById("loaded_cls").value = "";
			}else{
				//alert("OBJECT NAME: "+obj_name+"; CLASS TO SET: "+document.getElementById("loaded_cls").value+"; EXISTING CLASS: "+document.getElementById(obj_name).className+"; STATUS: bad girl");
				//alert(document.getElementById(obj_name).className);
				if(document.getElementById(obj_name).className == "fl cl_h_d" || document.getElementById(obj_name).className == "fl cl_a_d"){
					document.getElementById("loaded_cls").value = document.getElementById(obj_name).className;
					//document.getElementById("loaded_cls_obj").value = obj_name;
				}
				document.getElementById(obj_name).className = cls_name;
			}
		}
	}
    */
   
}

/*
Function: load_appt_schedule
Purpose: to load appt templates
Author: AA
*/
function load_appt_schedule(load_dt, day_name, appt_id, load_fd, showAlert){
	
	var elemObjAvail = $('#sch_left_portion').parent().css('display');
	var max_slides=1
	if(elemObjAvail=='block'){max_slides=MAX_SCHEDULE_PER_SLIDE;}
	else{max_slides=9;}
	
	if(!appt_id) appt_id = "";
	if(!load_fd) load_fd = "";
	if(typeof(showAlert) == "undefined") showAlert = true;

	//alert(load_dt+", "+day_name+", "+appt_id+", "+load_fd);
	
	//show loading image
	top.show_loading_image("show");
	

	//loading image in template section
	//document.getElementById("day_save").innerHTML = '<div class="sc_appt_loader sc_title_font">Loading...</div>';

	//tapping vars
	if(!appt_id) var appt_id = "";

	if(load_dt){
		var arr_load_dt = load_dt.split("-");
		set_date(arr_load_dt[0], arr_load_dt[1], arr_load_dt[2]);
	}else{
		load_dt = get_selected_date();
	}

	var arr_load_dt = load_dt.split("-");

	//getting selected facilities & providers
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	
	//setting date in navigation bar
	if(date_format == "mm-dd-yyyy")
	$("#DayNameId").html(day_name+", "+arr_load_dt[1]+"-"+arr_load_dt[2]+"-"+arr_load_dt[0]);
	else if(date_format == "dd-mm-yyyy")
	$("#DayNameId").html(day_name+", "+arr_load_dt[2]+"-"+arr_load_dt[1]+"-"+arr_load_dt[0]);
	else//show default
	$("#DayNameId").html(day_name+", "+arr_load_dt[1]+"-"+arr_load_dt[2]+"-"+arr_load_dt[0]);
	
	//document.getElementById("DayNameId").innerHTML = day_name+', '+arr_load_dt[2]+'-'+arr_load_dt[1]+'-'+arr_load_dt[0];
	var appt_load_url = "appt_load.php?loca="+sel_fac+"&dt="+load_dt+"&prov="+sel_pro+"&appt_id="+appt_id+"&max_slide_user="+max_slides+"&sid="+Math.random();
		
	$.ajax({
		url: appt_load_url,
		success: function(resp){
			//alert(resp);
			var arr_response = resp.split("____");

			//loading front desk
			if(load_fd == ""){
				pre_load_front_desk('', '', showAlert);	
			}

			//hide loading image
			top.show_loading_image("hide");
			
			//hiding provider notes if shonw
			hide_provider_notes();
			
			document.getElementById("day_save").innerHTML = arr_response[0];


			var office_status = arr_response[1];
			$("#hid_prov_count").val(arr_response[2]);
			//document.getElementById("div_summary_common").innerHTML = arr_response[3];
			
			if(office_status == "NOPROVIDER" || office_status == "NOFACILITY" || office_status == "CLOSED"){
				$("#Print_Day_Appt_Link").hide();
				$("#Day_Summary_Link").hide();
				$("#Day_Block").hide();
				
				// auto mode change from expand to collapse/shorten.
				//$('#sch_left_portion').css({'display':'inline-block'});
				$('#sch_left_portion').parent().css({'display':'block'});
				sch_expand_mode = 0;
			}else{
				$('#Print_Day_Appt_Link').show();
				$('#Day_Summary_Link').show();
				$("#Day_Block").show();
				//if($("#global_admin").val() == "1"){
					//$('#Day_Block').css({'display':'inline-block'});
				//}
				$("#scroll_controls").css('display','inline-block');
				
				var dateObj = new Date();
				var cur_hr = dateObj.getHours();
				//enable disable scroll buttons
				manage_slide_buttons();
				//set number of schedule to show per scroll according to physician schedule coming
				if(arr_response[4]==1)
				{
					item1=1;
					item2=1;
					item3=1;	
				}else if(arr_response[4]==2)
				{
					item1=1;
					item2=2;
					item3=2;	
				}else if(arr_response[4]>=3)
				{
					item1=1;
					item2=2;
					item3=3;	
				}
				/*//initialize new scroll object using OWL script
				if(arr_response[4]>1)//initialize only in case of more than one provider
				{
				owl = $('.owl-carousel');
				  owl.owlCarousel({
					loop:true,
					margin:0,
					navText:false,
					nav:false,
					dots:false,
					responsive: {
					  0: {
						items: item1
					  },
					  600: {
						items: item2
					  },
					  1000: {
						items: item3
					  }
					}
				  })
				}else
				{
					//remove owl class from scheduler
					$("#owl-carousel_div").removeClass('owl-carousel');	
					//disable button for owl scrolling
					$("#scroll_control1").attr("disabled",true);
					$("#scroll_control2").attr("disabled",true);
				}
				  */

				//day start time
				var day_st_tm = "";
				if($("#scroll_tim_limit3").get(0)){
					day_st_tm = parseInt($("#scroll_tim_limit3").val());
				}
				if(day_st_tm != ""){
					var last_logged_time = "";
					if($("#tim_cur2").get(0)){
						last_logged_time = $("#tim_cur2").val();
					}
					if(last_logged_time != ""){
						var arr_last_logged_time = last_logged_time.split(":");
						var last_logged_st_time = parseInt(arr_last_logged_time[0],10);
						//alert(day_st_tm + " > " + last_logged_st_time);
						if(day_st_tm < last_logged_st_time){

							last_logged_st_time = last_logged_st_time - day_st_tm;
							var global_time_slot = $("#global_time_slot").val();
							var slot_height = (11 * (global_time_slot / 5))*2;
							
							if(global_time_slot==5)slot_height+=10;
							else if(global_time_slot==15)slot_height-=18;
							else if(global_time_slot==30)slot_height-=40;
							
							var scroll_px = (60 / global_time_slot) * last_logged_st_time * slot_height;

							//taking minutes into consideration
							var last_logged_st_min = parseInt(arr_last_logged_time[1],10);
							var min_slots = Math.ceil(last_logged_st_min / global_time_slot);
							scroll_px += (min_slots * slot_height); 
							
							// top offset setting							
							var top_min_gap = parseInt($('#scroll_tim_limit4').val(),10);
							if(top_min_gap > 0)
							{						
								var top_min_gap_offset_min = 60 - top_min_gap;
								var top_min_gap_offset_val = Math.ceil(top_min_gap_offset_min/global_time_slot);
								scroll_px -= (top_min_gap_offset_val * slot_height);
							}
							//alert(scroll_px);
							var t = setTimeout("setMaxY('"+scroll_px+"')", 2);
						}
					}
				}
				
				//show appt edit images
				image_replace();

				prov_sch_sel_load_str = $('#prov_sch_sel_load').val();
				if(sch_expand_mode == 1)
				{	
					$('#sch_left_portion').parent().css({'display':'none'});
					
				}
			}                       
		}
	});
}

//to set the scroll parameters
function setMaxY(mY){
	if(document.getElementById('mn1_1')){
		abcTest = document.getElementById('mn1_1');
		abcTest.scrollTop = mY;
	}
	return true;
}

/* REMOTE SYNC - FUNCTIONS */
function set_parent_on_master(pid, appt_id){
	top.show_loading_image("show");
	$.ajax({
		url: '../../remote_sync/patient_parent_set.php?mode=set_parent_server&patient='+pid,
		dataType: 'text',
		complete: function(r){
			parent_id = r;//alert(r.responseText);return;
			if(parent_id != "" && parent_id != 0){				
				top.show_loading_image("hide");
				chk_pt_access_before_load_fd(pid, appt_id);
			}
		}
	});
}
function get_parent_data_frm_master(pid, appt_id){
	top.show_loading_image("show");
	$.ajax({
		url: '../../remote_sync/patient_parent_set.php?mode=get_parent_data_frm_master&patient='+pid,
		dataType: 'text',
		complete: function(r){
			parent_id = r;//alert(r.responseText);return;
			if(parent_id != "" && parent_id != 0){				
				top.show_loading_image("hide");
				chk_pt_access_before_load_fd(pid, appt_id);
			}
		}
	});
}
function load_rm_patient(pid,parent_id,appt_id){
	if(parent_id == "" || parent_id == "0"){
		top.fancyConfirm("Patient master server not defined or incorrect. Do you want to set current server as parent","", "window.top.fmain.set_parent_on_master('"+pid+"', '"+appt_id+"')","window.top.fmain.get_parent_data_frm_master('"+pid+"', '"+appt_id+"')")
	}
}						

/*
Function: pre_load_front_desk
Purpose: to perform pre load tasks before loading front desk like restricted provider access
Author: AA
*/
var local_pat_data_rm = '';
function pre_load_front_desk(pat_id, appt_id, showAlert){
	local_pat_data_rm = '';	
	if(!pat_id) pat_id = "";
	if(!appt_id) appt_id = "";
	if(typeof(showAlert) == "undefined") showAlert = true;
	if( typeof top.patient_pop_up !== 'undefined') {top.patient_pop_up = [];}
	
	var loaded_pat_id = $("#global_ptid").val();
	if(loaded_pat_id != "" && pat_id == loaded_pat_id){
		showAlert = false;
	}
	if( pat_id && loaded_pat_id && pat_id != loaded_pat_id ) {
		top.close_popwin();
	}
	if(pat_id == ""){
		var pat_id = document.getElementById("global_ptid").value;
	}

	if(appt_id == "" && document.getElementById("global_apptact").value == "reschedule"){
		var appt_id = document.getElementById("global_apptid").value;
	}
	
	top.show_loading_image("show");	
	
	/*if(gl_remote_sync_status == 1 && pat_id != "")
	{
		$.ajax({
			url : "local_pt_exists.php?pat_id="+pat_id+"&rn="+Math.random(),
			success:function(resp)
			{
				resp = $.trim(resp);
				resp = $.parseJSON(resp);
				var resp_act = resp.load_action;
				if(resp_act == "not_parent_server")
				{
					top.show_loading_image("hide");
					$('#ContextMenu').css({'display':'none'});	
					load_rm_patient(resp.pt_data_arr.pid,0,appt_id);									
				}
				else if(resp_act == "not_found")
				{
					top.fAlert('Remote Patient can not be pulled');
					top.show_loading_image("hide");
					$('#ContextMenu').css({'display':'none'});					
				}
				else
				{
					local_pat_data_rm = resp.pt_data_arr;
					chk_pt_access_before_load_fd(pat_id, appt_id, showAlert);				
				}
			}
		});
	}
	else
	{*/	
		chk_pt_access_before_load_fd(pat_id, appt_id, showAlert);
	/*}*/
}

/*
Function: check the patient access is restricted or not before loading this patient in to the front desk
*/
var prevent_dupli_pt_access_fun = 0;
function chk_pt_access_before_load_fd(pat_id, appt_id, showAlert)
{
	if(isNaN(pat_id) == false && pat_id>0 && prevent_dupli_pt_access_fun!=pat_id){
		$("#fd_pt_controls").html('<div style="height:29px">Updating Controls...</div>');
		prevent_dupli_pt_access_fun = pat_id;
		//check for restricted access
        var lname_val = pat_id;
        var findValArr = document.getElementById("findByShow").value.split(':');
        if(isNaN(findValArr[0])){
            var findBy = findValArr[0];
        }else{
            var findBy = findValArr[2];	
        }
        if(isNaN(lname_val) == true){
            if((lname_val[0].toLowerCase() == "e") && (parseInt(lname_val.substring(1, lname_val.length)) > 0)){
                lname_val = lname_val.substring(1, lname_val.length);
                findBy = "External MRN";
            }
        }
    
        $.ajax({
			url: 'chk_patient_exists.php',
			type: 'POST',
			data: 'pid='+lname_val+'&findBy='+findBy,
			success: function(resultData)
			{
                prevent_dupli_pt_access_fun = 0;
                
                if(resultData.length > 1) resultData = JSON.parse(resultData);

                if(resultData.hasOwnProperty('askForReason')==true)
                {
                    top.show_loading_image("hide");	
                    var patId = resultData.patId;
					var bgPriv = resultData.bgPriv;
                    TestOnMenu();
					top.core_restricted_prov_alert(patId, bgPriv,'','');
                    return false;
                }
                else if(resultData == 'n')
				{
					top.fAlert('Patient not found');	
				}
				else
				{
					pid = eval(resultData);
					load_front_desk(pid, appt_id, showAlert);
				}
            }
        });
        
        //There is no handling done for the below code in "core/index.php" It never passes the if condition and enters the else case.
        //So commented the code to implement the check-restricted-access code in scheduler
        /*
		$.ajax({
			url: "../../interface/core/index.php?pg=check-restricted-access&p_id=" + pat_id + "&resp_type=ajax",
			success: function(resp){
				prevent_dupli_pt_access_fun = 0;
				var arr_resp = resp.split("~~~");

				top.show_loading_image("hide");
				top.close_popwin();
				if(arr_resp[0] == "y"){
					top.core_restricted_prov_alert(arr_resp[1], arr_resp[2], showAlert);
				}else{
					load_front_desk(pat_id, appt_id, showAlert);
				}
			}
		});	
        
        */
	}
}

function update_recent_pt_list(recent_search)
{
	$('ul#main_search_dd').html(recent_search);
}
/*
Function: load_recent_pt_search
Purpose: to update recently searched patients list in front desk and top search panel
Author: AA
*/
function load_recent_pt_search(pat_id){
	/*$.ajax({
		url: "app_get_recent_search.php?pat_id="+pat_id,
		success: function(resp){
			var arr_resp = resp.split("~~~~~~~~~~"); //10 times
			if(top.document.getElementById('homeDropDown')){
				top.document.getElementById('homeDropDown').innerHTML = arr_resp[0];				
			}
			if(top.fmain.document.getElementById('homeDropDownSCH')){
				temp_old_val = $('#txt_patient_app_name').val();
				top.fmain.document.getElementById('homeDropDownSCH').innerHTML = arr_resp[1];
				temp_old_val = $('#txt_patient_app_name').val(temp_old_val);
			}
		}
	});*/
}

function submitFrontInsuraceForm(){
	$('#frmFrontdeskInsurance').unbind('submit');
	$('#frmFrontdeskInsurance').bind('submit',save_insurance);
	$('#frmFrontdeskInsurance').trigger('submit');
}

function save_insurance()
{
	top.show_loading_image("show");
	serialize_data = $(this).serialize();
	$.ajax({
		url: 'insurance_active_case.php',
		type: 'POST',
		data: serialize_data,
		complete : function(respData)
		{
			resultData = respData.responseText;						
			var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
			var pat_id = $('#global_ptid').val();		
			var appt_id = $("#global_apptid").val();
			selected_dt = $('#global_year').val()+'-'+$('#global_month').val()+'-'+$('#global_date').val();
			if($.trim(appt_id)!="" && typeof(appt_id) != "undefined")
			{
				get_copay(pat_id,ap_ins_case_id,appt_id,selected_dt);
			}			
			top.show_loading_image("hide");			
		}
	});
	return false;
}

function reset_pt_data(){
	//collection flag in front desk base file
	$("#collection_flag_space").html('');
	$("#collection_flag_space").css("display","none");
	$("#todo_flag_space").css("display","none");
	$("#AssesmentDiv").css("display","none");
	
	$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');	
	//top.document.getElementById("divPtDemographicAlert").style.display = "none";
	//top.document.getElementById("divPtSpecificAlert").style.display = "none";
	document.getElementById("global_ptid").value = "";
}

/*
Function: load_front_desk
Purpose: to load patient in front desk
Author: AA
*/
function load_front_desk(pat_id, sch_id, showAlert){
	if(!pat_id) pat_id = "";
	if(!sch_id) sch_id = "";
	if(typeof(showAlert) == "undefined") showAlert = true;
	//loading front desk
	if(pat_id != ""){
		hidePatientImage();
		//show loading image
		top.show_loading_image("show");
		
		//reset
		reset_pt_data();
		
		//hide if any msg opened for previously loaded patient
		hide_msg_stack();
		
		//hiding add appt trail
		//display_block_none("imageDiv1", "none");

		//hiding appt history & recalls related buttons in front desk base layer
	
		//display_block_none("frontdesk2", "none");
		display_block_none("frontdesk3", "none");

		if(document.getElementById("fd_base_controls")){
			display_block_none("fd_base_controls", "none");
		}

		var sel_date = get_selected_date();
		
		if($("#global_apptid").val() == ""){
			var force_comment = $("#txt_comments").val();
			var force_proc = $("#sel_proc_id").val();
			var force_proc2 = $("#sec_sel_proc_id").val();
			var force_proc3 = $("#ter_sel_proc_id").val();
			var force_pri_site = $("#pri_eye_site").val();
			var force_sec_site = $("#sec_eye_site").val();
			var force_ter_site = $("#ter_eye_site").val();
		}else{
			var force_comment = "";
			var force_proc = "";
			var force_proc2 = "";
			var force_proc3 = "";			
			var force_pri_site = "";
		}
		
		//loading front desk
		var frontdk_url = "frontDeskPatient.php?pat_id="+pat_id+"&sch_id="+sch_id+"&sel_date="+sel_date+"&showAlert="+showAlert+"&force_comment="+force_comment+"&force_proc="+force_proc+"&force_proc2="+force_proc2+"&force_proc3="+force_proc3+"&force_pri_site="+force_pri_site+"&force_sec_site="+force_sec_site+"&force_ter_site="+force_ter_site;
			
		$.ajax({ 
			url: frontdk_url, 
			success: function(resp){
				if(is_remote_server() == true)
				{
					resp = json_resp_handle(resp); 
				}				
				var arr_resp = resp.split("~~~~~~~~~~");

				$("#getImageCross").css("display", "inline-block");
				$("#fd_scan_links").css("display", "inline-block");
				
				//loading content
				document.getElementById("frontdesk").innerHTML = arr_resp[0];							

				$("#global_ptid").val(pat_id);
				if(arr_resp[14] == "-1"){
					arr_resp[14] = "";
				}

				top.fmain.patientDeceased = (arr_resp[38] == 'Deceased') ? true : false;
				
				$("#global_apptid").val(arr_resp[14]);
				$("#global_apptpro").val(arr_resp[19]);
				$('#global_apptsecpro').val(arr_resp[33]);
				$('#global_apptterpro').val(arr_resp[34]);
				$("#global_context_apptid").val(arr_resp[14]);
				$("#global_ptfname").val(arr_resp[1]);
				$("#global_ptmname").val(arr_resp[2]);
				$("#global_ptlname").val(arr_resp[3]);
				$("#global_ptemr").val(arr_resp[4]);
				
				var nickName = arr_resp[39].trim();
				var phoneticName = arr_resp[40].trim();
				var language = arr_resp[41].trim(); 
				
				var Xbtn="<span class=\"top_pt_close\" onclick=\"top.clean_patient_session('scheduler');\" class=\"link_cursor\" title=\"Close Patient\">X</span>";
				//show patient name in top bar
				$("#show_pt_name").html(arr_resp[3]+', '+arr_resp[1]+' '+arr_resp[2]+'-'+pat_id+' '+Xbtn);
				if(nickName.length > 0 || phoneticName.length > 0 || language.length > 0){
					var nickAndPhoneticName = "";
					if(nickName.length > 0 && phoneticName.length > 0){
						nickAndPhoneticName = "Nick Name: " + nickName + "<br />" + "Phonetic Name: " + phoneticName;
					}else if(nickName.length > 0 && phoneticName.length <= 0){
						nickAndPhoneticName = "Nick Name: " + nickName;
					}else if(nickName.length <= 0 && phoneticName.length > 0){
						nickAndPhoneticName = "Phonetic Name: " + phoneticName;
					}
					if(language.length > 0){
						nickAndPhoneticName = (nickAndPhoneticName!="") ? nickAndPhoneticName+"<br />Language: " + language:"Language: " + language ;
					}

					$("#show_pt_name").attr("data-toggle", "tooltip");
					$("#show_pt_name").attr("data-html", "true");
					$("#show_pt_name").attr("data-placement", "bottom");
					$("#show_pt_name").attr("data-original-title", nickAndPhoneticName);
					$('[data-toggle="tooltip"]').tooltip();
				}else{
					$("#show_pt_name").attr("data-original-title", '');
				}
				//update recent patient
				update_recent_pt_list(arr_resp[37]);
				
				var ins_case_id = arr_resp[5];
				
				/*-----UPDATING RECENT PATIENTS LIST--*/
				if(typeof(top.update_iconbar)=='function') {top.update_iconbar(); }
				
				$.ajax({ 
					url: "insurance_active_case.php?current_caseids="+ins_case_id, 
					success: function(resp2){
						document.getElementById("load_pt_insurance").innerHTML = resp2;
						if($("#choose_prevcase").val()==0){
							load_insurance(0);
						}
						var tmp_appt_id = parseInt($("#global_context_apptid").val());
						if( !tmp_appt_id ) { chk_referral('load_front_desk'); chk_verif_sheet('load_front_desk'); }
						
						$.ajax({ 
							url: "load_appt_hx.php?pid="+pat_id, 
							success: function(resp3){							
								get_copay(pat_id, ins_case_id, arr_resp[14], arr_resp[20]);
								document.getElementById("load_pt_appointments").innerHTML = resp3;
								$('[data-toggle="tooltip"]').tooltip(); 
							}
						});

					}
				});					
				
				//showing collection flag //arr_resp[1] - collection flag status //arr_resp[2] - collection date sent if any
				if(arr_resp[12] == true){
					if(arr_resp[13] != ""){
						document.getElementById("collection_flag_space").innerHTML = " <img src=\"../../library/images/flag_red_collection.png\" title=\""+arr_resp[13]+"\">";
					}else{
						document.getElementById("collection_flag_space").innerHTML = " <img src=\"../../library/images/flag_red_collection.png\">";
					}
					document.getElementById("collection_flag_space").style.display = "block";
				}
				
				//loading pt image
				var pt_photo_path = arr_resp[6];
				//alert(pt_photo_path);
				if(pt_photo_path != ""){
					$.ajax({ 
						url: "patient_photos.php?path="+pt_photo_path, 
						success: function(ph_resp){
							//alert(ph_resp);
							if(ph_resp != ""){
								//var img = $(ph_resp);
								//var height = $(img).css('height');
								//var width = $(img).css('width');
								//$("#patient_photo_container").css({display:'inline-block', height:height, width:width});
								$("#patient_photo_container").html(ph_resp);
								//$("#patient_photo_container").css("display", "block");
							}else
							{
								$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');	
							}
						}
					});
				}else
				{
					$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');	
				}

				//show todo flag
				var appt_status = parseInt(arr_resp[7]);
				if(appt_status ==  201){
					document.getElementById("todo_flag_space").style.display = "block";
				}
				
				//show pt demographic alert
				var pt_dg_alert = arr_resp[8];
				if(typeof(top.patient_pop_up)=="undefined"){ top.patient_pop_up=new Array(); }
				if(pt_dg_alert != "" && (jQuery.inArray("divPtDemographicAlertSC", top.patient_pop_up) == "-1")){
					//pt_alert_div += "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_dg_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtDemographicAlert');\" /></div>";
					top.fAlert('<div style="max-height:450px;overflow-y:scroll;">'+pt_dg_alert+'</div>', 'imwemr - Patient Notes');
					top.patient_pop_up.push('divPtDemographicAlertSC');
					//alert_box("imwemr - Patient Info Alert", pt_dg_alert, 300, "", 500, 150, "divPtDemographicAlert", false, false);
					
				}
				//remove previous pt alert content
				$( "form",top.fmain.document ).remove(".class_chart_alerts_patient_specific");
				//show pt specific alert
				var pt_sp_alert = arr_resp[9];				
				pt_sp_alert_arr = pt_sp_alert.split('^^^');
				if(pt_sp_alert_arr[0] != ""){
					var pt_sp_alert_div = "";
					pt_sp_alert_div += "<form class=\"class_chart_alerts_patient_specific\" name=\"chart_alerts_patient_specific\" action=\""+top.JS_WEB_ROOT_PATH+"/interface/patient_info/alerts_reason_save.php\" target=\"chart_alerts_patient_specific\" method=\"post\">";
					pt_sp_alert_div += "<div id=\"patSpesificDivAlert\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"display:block;  z-index:2000; top:200px; width:400px; left:550px; position:absolute;cursor:move;\" class=\"confirmTable3 panel panel-success\">";							
					pt_sp_alert_div += "<div class=\"boxhead panel-heading\">imwemr - Pt. Alerts</div>";
					var strTemp = top.JS_WEB_ROOT_PATH+"/library/images/confirmYesNo.gif";
					pt_sp_alert_div += "<div clasws=\"panel-body\" style=\"max-height:300px; overflow:hidden; overflow-y:auto;\"><div class=\"row pt10\">";
					pt_sp_alert_div += "<div class=\"col-sm-2 text-center\"><img src=\""+strTemp+"\" alt=\"Confirm\"></div>";
					pt_sp_alert_div += "<div id=\"patientAlertMsg\" class=\"col-sm-10\"><p>"+pt_sp_alert_arr[0]+"</p></div></div>";
					pt_sp_alert_div += "</div>";
					pt_sp_alert_div += "<div class=\"panel-footer text-center\" id=\"module_buttons\">";
					pt_sp_alert_div += "<input type=\"button\" id=\"patAlertDisable\" name=\"patAlertDisable\" value=\"OK\" class=\"btn btn-success\" onClick=\"acknowledged('1', this.form); this.form.submit();\" > ";
					pt_sp_alert_div += "<input type=\"button\" value=\"Remove\" name=\"patAlertAcknowledged\" id=\"patAlertAcknowledged\" class=\"btn btn-danger\" onClick=\"javascript: acknowledged('', this.form); this.form.submit();\" >";			
					pt_sp_alert_div += "</div>";
					pt_sp_alert_div += "</div>";								
					pt_sp_alert_div += "<input type=\"hidden\" name=\"patientSpecificFrm\" value=\"SCH\">";
					pt_sp_alert_div += "<input type=\"hidden\" id=\"disablePatAlertThisSession\" name=\"disablePatAlertThisSession\" >";
					pt_sp_alert_div += "<input type=\"hidden\" name=\"cancel_pt_alert\" id=\"cancel_pt_alert\" value=\""+pt_sp_alert_arr[1]+"\">";
					pt_sp_alert_div += "</form>";
					pt_sp_alert_div += "<iframe name=\"chart_alerts_patient_specific\" src=\"\" style=\"display:block;\" frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";
					
					/*pt_sp_alert = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_sp_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><form name=\"chart_alerts_patient_specific\" target=\"chart_alerts_patient_specific\" action=\"../patient_info/common/alerts_reason_save.php\" method=\"post\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript: top.document.getElementById('disablePatAlertThisSession').value = 'yes'; this.form.submit();top.fmain.close_alert_box('divPtSpecificAlert');\" /> <input type=\"button\" class=\"dff_button\" value=\"Remove\" onclick=\"this.form.submit();top.fmain.close_alert_box('divPtSpecificAlert');\" /><input type=\"hidden\" name=\"patientSpecificFrm\" value=\"SCH\"><input type=\"hidden\" id=\"disablePatAlertThisSession\" name=\"disablePatAlertThisSession\" ></form></div><iframe name=\"chart_alerts_patient_specific\"  src=\"\" style=\"display:block;\" frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";
					alert_box("imwemr - Pt. Alerts", pt_sp_alert, 300, "", 550, 200, "divPtSpecificAlert", false, false);			*/
					$("body",top.fmain.document).append(pt_sp_alert_div);
					
				}

				//show pt specific alert
				var pt_poe_alert = arr_resp[11];
				if(pt_poe_alert != ""){					
					//pt_poe_alert = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_poe_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtPOEAlert');\" /></div>";
					//alert_box("imwemr - POE Alert", pt_poe_alert, 300, "", 600, 250, "divPtPOEAlert", false, true);
					//alert(pt_poe_alert);
				}
				if($("#poeModal").length>0 && !$("#poeModal").hasClass("hidden")){$("#poeModal").modal('show');}

				//to do flag
				var pt_to_do = arr_resp[10];
				if(pt_to_do == true){
					document.getElementById("todo_flag_space").style.display = "block";
				}
				
				//Collection Alert
				var coll_alert = arr_resp[30];
				if(coll_alert == 1){
					top.fAlert("Patient account status is <font color='#ff0000'><b>"+arr_resp[35]+"</b></font>.");
				}
				
				//first available Alert
				if($.trim(arr_resp[36])!=''){
					top.fAlert(arr_resp[36],'First Available time slot found');
				}
				//updating recently searched patients list in front desk and top search panel
				//load_recent_pt_search(pat_id);

				//reload title bar
				//top.refresh_control_panel("Patient_Info",pat_id);

				//display to do button
				if(top.document.getElementById("tl_2to")){
					top.document.getElementById("tl_2to").style.display = 'block';
				}
				
				//hiding loading image
				top.show_loading_image("hide");

				// CALL FUNCTION TO DISPLAY MESSAGE OF SELECTED PROCEDURE 
				if(document.getElementById('sel_proc_id').value!='') {
					getProcMessage(document.getElementById('sel_fd_provider').value,document.getElementById('sel_proc_id').value);	
				}
				//---------------
				////Eligibility
				if(document.getElementById("div_fd_el_links")){
					var strElLinkInnerHTML = arr_resp[31];
					if(strElLinkInnerHTML != "undefined"){
						$("#div_fd_el_links").css("display","inline-block");
						//alert(strElLinkInnerHTML);
						document.getElementById("div_fd_el_links").innerHTML = strElLinkInnerHTML;
					}
				}
				
				if(document.getElementById('rte_info')){
					var rte_icon = arr_resp[32];
					if(strElLinkInnerHTML != "undefined"){
						$("#rte_info").css("display","inline-block");
						//alert(strElLinkInnerHTML);
						document.getElementById("rte_info").innerHTML = rte_icon;
					}
					
				}
				
				//Setting Pt. Alert notification counter
				set_pt_allert_notification_counter();
			}
		});
	}
}

function set_pt_allert_notification_counter(){
	$.ajax({
		url:'get_pt_alert.php',
		type:'POST',
		success:function(response){
			if($.trim(response) != ''){
				$('.pt_alert_container').not('.portal').html(response);
			}
		}
	});
}

function getRealTimeEligibilityApp(insRecId, askElFrom, strRootDir, schId, strAppDate, intClentWinH){
	if(strRootDir != ""){	
		askElFrom = askElFrom || 0;
		
		//top.show_loading_image("show", 100, "Please wait while Real Time Eligibility is processing");
		top.show_loading_image("show", 100);
		$.ajax({ 
			url: strRootDir +'/patient_info/ajax/make_270_edi.php?action=ins_eligibility&insRecId='+insRecId+'&askElFrom='+askElFrom+'&schId='+schId+'&strAppDate='+strAppDate, 
			success: function(responseText){
				res = JSON.parse(responseText);
				var strResp=res.data;
				var arrResp = strResp.split("~~");
				if(arrResp[0] == "1" || arrResp[0] == 1){
					var alertResp = "";
					if(arrResp[1] != ""){
						if(arrResp[3] == "A"){
							alertResp += "Patient Eligibility Or Benefit Information Status :<label style='color:green;'>"+arrResp[1]+"</label><br>";
						}
						else if(arrResp[3] == "INA"){
							alertResp += "Patient Eligibility Or Benefit Information Status :<label style='color:red;'>"+arrResp[1]+"</label><br>";
						}
						else{
							alertResp += "Patient Eligibility Or Benefit Information Status :<label style='color:black;'>"+arrResp[1]+"</label><br>";
						}
					}
					if(arrResp[2] != ""){
						alertResp += "With Insurance Type Code :"+arrResp[2]+"<br><br>";
					}
					if(alertResp != ""){
						if(arrResp[3] == "A"){
							document.getElementById('imgEligibility').src = "../../library/images/eligibility_green.png";
						}
						else if(arrResp[3] == "INA"){
							document.getElementById('imgEligibility').src = "../../library/images/eligibility_red.png";
						}
						document.getElementById('imgEligibility').title = alertResp;
						//alert(alertResp);
						
						var elId = parseInt(arrResp[4]);
						var strShowMsg = arrResp[5];
						if((elId > 0) && (strShowMsg) == "yes"){
							alertResp += "Would you like to set Co-Pay, Deductible and Co-Insurance!<br>"
						}
						if((elId > 0) && (strShowMsg) == "yes"){
							top.fancyConfirm(alertResp,"","window.top.fmain.send_request('"+strRootDir+"','"+elId+"','"+intClentWinH+"')" );
						}
						else{
							top.fAlert(alertResp);
						}
						
						var schedule_id = $("#global_context_apptid").val();
						var sa_date = get_selected_date();						
						$.ajax({
							url: "get_day_name.php?load_dt="+sa_date,
							success: function(day_name){
								//load_appt_schedule(sa_date, day_name, schedule_id, '', false);
								load_calendar(sa_date, day_name, '', '', schedule_id);
								top.show_loading_image("hide");
								
							}
						});
					}
				}
				else if(arrResp[0] == "2" || arrResp[0] == 2){						
					if(arrResp[1] != ""){
						document.getElementById('imgEligibility').src = "../../library/images/eligibility_red.png";
						document.getElementById('imgEligibility').title = arrResp[1];
						//alert(arrResp[1]);
						var schedule_id = $("#global_context_apptid").val();
						var sa_date = get_selected_date();						
						$.ajax({
							url: "get_day_name.php?load_dt="+sa_date,
							success: function(day_name){
								//load_appt_schedule(sa_date, day_name, schedule_id, '', false);
								load_calendar(sa_date, day_name, '', '', schedule_id);
								top.show_loading_image("hide");
							}
						});
					}
				}
				else{
					//document.write(arrResp[0]);
					top.fAlert(arrResp[0]);
					var schedule_id = $("#global_context_apptid").val();
					var sa_date = get_selected_date();						
					$.ajax({
						url: "get_day_name.php?load_dt="+sa_date,
						success: function(day_name){
							//load_appt_schedule(sa_date, day_name, schedule_id, '', false);
							load_calendar(sa_date, day_name, '', '', schedule_id);
							top.show_loading_image("hide");
						}
					});
				}
				top.show_loading_image("hide");
			}
		});
		
	}
}

function send_request(strRootDir, elId, intClentWinH){
	var urlAmount = strRootDir + '/patient_info/eligibility/eligibility_report.php?set_rte_amt=yes&id='+elId;
	var h = intClentWinH;
	window.open(urlAmount,'setAmountRTE','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	
}

function load_insurance(ins_case_id){
	$.ajax({ 
		url: "insurance_active_case.php?current_caseids="+ins_case_id, 
		success: function(resp2){
			document.getElementById("load_pt_insurance").innerHTML = resp2;
			chk_referral('load_insurance');
			chk_verif_sheet('load_insurance');
			var pat_id = $("#global_ptid").val();			
			get_copay(pat_id, ins_case_id);
		}
	});
}

function load_last_app(){
	var pat_id = $("#global_ptid").val();
	$.ajax({ 
		url: "load_last_app.php?pid="+pat_id, 
		success: function(resp){
			var sch_arr = resp.split("~~");
			if(sch_arr[0] != "" && sch_arr[1] != "" && sch_arr[2] != ""){
				//load_calendar(sch_arr[2], sch_arr[3], 'nonono');				
				pre_load_front_desk(sch_arr[1], sch_arr[0], false);	
				//load_appt_schedule(sch_arr[2], sch_arr[3], '', 'nonono')
			}
		}
	});
}

function showAssesmentList(mode,dated){
	if(mode == 1){
		$.ajax({
			url: "../accounting/accountingAPResult.php?scheduler_call=yes&dat_id="+dated,
			success: function(resp){
				var response = resp.split('<body>');
				if(response[1]){
					response = response[1].replace('</body></html>','');
					response = response.replace('msgDiv','msgDivReplaced');
					resp = response;
				}
				
				$('#AssesmentDiv .modal-body').html(resp);
				$('#AssesmentDiv').modal('show');
			}
		});	
	}else if(mode == 0){
		$('#AssesmentDiv .modal-body').html('');
		$('#AssesmentDiv').modal('hide');
	}
}

function fd_scan_patient_image(){
	var webcam_window = window.open("../patient_info/demographics/webcam/flash.php",'webcam_window_popup','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=600,left=150,top=60');		
}

function close_patient_info(){
	reset_pt_data();
	$.ajax({
		url: "pre_fd_search_patient.php",
		success: function(resp){
			$("#front_desk_container").html(resp);
		}
	});
	$("#show_pt_name").html('');
}

function scan_licence(){
	var scan_window = window.open("../patient_info/demographics/scan_licence.php#scan_license",'scan_window_popup','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=600,left=150,top=60');
		
}

/*
Function: load_navigation_bar
Purpose: to change values in nav bar
Author: AA
*/
function load_navigation_bar(day_name){
	var sel_dat = get_selected_date();
	var arr_dat = sel_dat.split("-");

	var month_arr = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
	var selBoxOptCount = document.getElementById("sel_month_year").options;
	var selBoxCheck = false;
	for(i=0;i<selBoxOptCount.length;i++){
		if(selBoxOptCount[i].value == arr_dat[1]+"-01-"+arr_dat[0]+"|"){
			selBoxOptCount.selectedIndex = i;
			selBoxCheck = true;
		}
	}
	if(selBoxCheck == false){
		var optn = document.createElement("OPTION");
		var arr_index = arr_dat[1] - 1;
		optn.text = month_arr[arr_index]+" "+arr_dat[0];
		optn.value = arr_dat[1]+"-01-"+arr_dat[0]+"|";
		selBoxOptCount.add(optn);
		selBoxOptCount.selectedIndex = i;
	}
	if(date_format == "mm-dd-yyyy")
	document.getElementById("DayNameId").innerHTML = day_name+", "+arr_dat[1]+"-"+arr_dat[2]+"-"+arr_dat[0];
	else if(date_format == "dd-mm-yyyy")
	document.getElementById("DayNameId").innerHTML = day_name+", "+arr_dat[2]+"-"+arr_dat[1]+"-"+arr_dat[0];
}
/*
Function: display_block_none
Purpose: to show / hide a particular html element
Author: AA
Arguments: object id and action - block / none
*/
function display_block_none(obj_id, action){
	document.getElementById(obj_id).style.display = action;
}

/*
Function: show_msg_in_stack
Purpose: to show a particular message in the message stack
Author: AA
Arguments: msg content
*/
var global_stack_msg_identifier = 0;
var global_stack_msg_count = 0;
function show_msg_in_stack(msg){
	var left = global_stack_msg_identifier * 25;
	var top = global_stack_msg_identifier * 25;
	var this_msg = "<div id=\"msg"+global_stack_msg_identifier+"\" style=\"display:block;position:absolute;background-color:#FFFFFF;left:"+left+"px;top:"+top+"px;text-align:center;\">"+msg+"<br><br><input type=\"button\" value=\"OK\" onclick=\"javscript:hide_msg_in_stack('msg"+global_stack_msg_identifier+"');\" /></div>";
	document.getElementById("global_msg_stack").innerHTML = document.getElementById("global_msg_stack").innerHTML + this_msg;
	display_block_none("global_msg_stack", "inline-block");
	global_stack_msg_identifier++;
	global_stack_msg_count++;
}

/*
Function: hide_msg_in_stack
Purpose: to hide a particular message in the message stack
Author: AA
Arguments: hide div obj
*/
function hide_msg_in_stack(obj_id){
	display_block_none(obj_id, "none");
	global_stack_msg_count--;
	if(global_stack_msg_count == 0){
		hide_msg_stack();
	}
}

/*
Function: hide_msg_stack
Purpose: to hide the whole msg stack
Author: AA
*/
function hide_msg_stack(){
	document.getElementById("global_msg_stack").innerHTML = "";
	display_block_none("global_msg_stack", "none");
	global_stack_msg_identifier = 0;
	global_stack_msg_count = 0;
}


function show_cal_context_menu(obj_name, sel_y_m_d, e){
	//alert(sel_y_m_d);
	if(document.getElementById("global_admin").value == "1"){
			
		//this code commented because it haulting process in safari
		//$("#"+obj_name).mouseup(function(event) {
				
			//alert(event.which);
			switch (event.which) //WhichButton(event)
			{
				case 3:	
					document.oncontextmenu = function(){ return false; };
					document.getElementById("global_context_caldt").value = sel_y_m_d;
					display_block_none("div_add_prov_button", "block");
					document.getElementById("div_add_prov_button").style.width = 90;
					document.getElementById("div_add_prov_button").style.position = 'absolute';
					document.getElementById("div_add_prov_button").style.display = 'block';
					document.getElementById("div_add_prov_button").style.pixelLeft = event.clientX;		
					document.getElementById("div_add_prov_button").style.pixelTop = event.clientY;
					
					var bro_ver=navigator.userAgent.toLowerCase();
					//if browser is crhome or firfox or safari then we need to placement issue
					if(bro_ver.search("chrome")>1 || bro_ver.search("firefox")>1){
						$("#div_add_prov_button").css({"display":"inline-block",top: event.clientY, left: event.clientX});
					}
				break;
				default:
					display_block_none("div_add_prov_button", "none");
			}
		//});
	}
}

function open_add_schedule_option(mode){
	if(!mode) mode = "";
    $('#div_add_prov_form').modal('show');
	TestOnMenu();

	//setting this date
	if(mode == "appt_scheduler"){
		
		var ap_sttm = $("#global_context_slsttm").val();
		var global_time_slot = $("#global_time_slot").val();
		//alert(ap_sttm+" "+global_time_slot);
		var arr_ap_sttm = ap_sttm.split(":");
		var ap_hr = parseInt(arr_ap_sttm[0]);
		var ap_mn = parseInt(arr_ap_sttm[1]);
		var ap_sc = parseInt(arr_ap_sttm[2]);

		var ap_doc = $("#global_context_sldoc").val();
		$("#anps_sel_pro").val(ap_doc);

		//alert(ap_hr+" "+ap_mn+" "+ap_dt);
		
		var set_st_ampm = "AM";
		var set_st_hr = ap_hr;
		if(set_st_hr > 12){
			set_st_hr = set_st_hr - 12;	
			set_st_ampm = "PM";
		}
		if(set_st_hr == 12){
			set_st_ampm = "PM";
		}
		if(parseInt(set_st_hr) < 10){
			set_st_hr = "0" + parseInt(set_st_hr);
		}
		var set_st_mn = ap_mn;
		if(parseInt(set_st_mn) < 10){
			set_st_mn = "0" + parseInt(set_st_mn);
		}
		
		var set_ed_ampm = "AM";
		var set_ed_mn = ap_mn + 60;
		var set_ed_hr = set_st_hr;
		if(set_ed_mn >= 60){	//assuming slot will be never be more that  1hr
			set_ed_mn = set_ed_mn - 60;
			set_ed_hr = parseInt(set_ed_hr) + 1;
		}
		if(parseInt(set_ed_hr) >= 12){
			set_ed_ampm = "PM";
		}
		if(parseInt(set_ed_hr) > 12){
			set_ed_hr = set_ed_hr - 12;
		}
		if(parseInt(set_ed_hr) < 10){
			set_ed_hr = "0" + parseInt(set_ed_hr);
		}
		if(parseInt(set_ed_mn) < 10){
			set_ed_mn = "0" + parseInt(set_ed_mn);
		}
		
		//set title
		$("#setTimeTitle").html("Open Physician Schedule");
		//setting time
		//alert(set_st_hr+" "+set_st_mn+" "+set_st_ampm+" "+set_ed_hr+" "+set_ed_mn+" "+set_ed_ampm);
		$("#start_hour").val(set_st_hr);
		$("#start_min").val(set_st_mn);
		$("#start_time").val(set_st_ampm);

		//$("#end_hour").val(set_ed_hr);
		//$("#end_min").val(set_ed_mn);
		//$("#end_time").val(set_ed_ampm);
		
		var cal_sel_date = get_selected_date();
		var arr_cal_date = cal_sel_date.split("-");
		$("#anps_day_name").html(arr_cal_date[1]+"-"+arr_cal_date[2]+"-"+arr_cal_date[0]);
		
		document.getElementById('show_tmp_option').style.display = 'none';
		//document.getElementById('temp_dd').style.display = 'none';
		//document.getElementById('temp_text').style.display = 'inline-block';
		document.getElementById('commentDiv').style.display = 'inline-block';
		document.getElementById('prov_sch_add_type').value = "SYSTEM";
		
	}else{

		$("#start_hour").val("");
		$("#start_min").val("");
		$("#start_time").val("");

		$("#end_hour").val("");
		$("#end_min").val("");
		$("#end_time").val("");

		$("#setTimeTitle").html("Add New Provider Schedule");
		var cal_sel_date = document.getElementById("global_context_caldt").value;
		var arr_cal_date = cal_sel_date.split("-");
		$("#anps_day_name").html(arr_cal_date[1]+"-"+arr_cal_date[2]+"-"+arr_cal_date[0]);
		
		document.getElementById('show_tmp_option').style.display = 'inline-block';
		document.getElementById('temp_dd').style.display = 'inline-block';
		document.getElementById('temp_text').style.display = 'none';
		document.getElementById('commentDiv').style.display = 'none';
		//document.getElementById('prov_sch_add_type').value = "USER";
		document.getElementById('prov_sch_add_type').value = "SYSTEM";
	}
	
	//document.getElementById("div_add_prov_form").style.position = 'absolute';
	//document.getElementById("div_add_prov_form").style.display = 'block';
	//document.getElementById("div_add_prov_form").style.pixelLeft = 100;		
	//document.getElementById("div_add_prov_form").style.pixelTop = 130;
	//display_block_none("div_add_prov_button", "none");
}

function load_template_timings(tmp_id){
	if(!tmp_id) tmp_id = "new";
	if(tmp_id == "new"){
		$("#start_hour").val("");
		$("#start_min").val("");
		$("#start_time").val("");

		$("#end_hour").val("");
		$("#end_min").val("");
		$("#end_time").val("");
	}else{
		$.ajax({
			url: "get_template_timings.php?tmp_id="+tmp_id,
			success: function(resp){
				//alert(resp);
				if(resp == ""){
					$("#start_hour").val("");
					$("#start_min").val("");
					$("#start_time").val("");

					$("#end_hour").val("");
					$("#end_min").val("");
					$("#end_time").val("");
				}else{
					var arr_resp = resp.split("~");

					$("#start_hour").val(arr_resp[0]);
					$("#start_min").val(arr_resp[1]);
					$("#start_time").val(arr_resp[2]);

					$("#end_hour").val(arr_resp[3]);
					$("#end_min").val(arr_resp[4]);
					$("#end_time").val(arr_resp[5]);
				}
			}
		});
	}
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function save_provider_schedule(){

	var err = "";
	
	if($("#anps_sel_fac").val() == ""){
		err += " - Facility\n";
	}
	if($("#anps_sel_pro").val() == ""){
		err += " - Provider\n";
	}
	if($("#start_hour").val() == "" || $("#start_min").val() == "" || $("#start_time").val() == ""){
		err += " - Start Time\n";
	}
	if($("#end_hour").val() == "" || $("#end_min").val() == "" || $("#end_time").val() == ""){
		err += " - End Time\n";
	}
	
	if(err != ""){
		err = "Please provide input for the following:\n\n" + err;
		top.fAlert(err);
		return false;
	}else{
		top.show_loading_image("show");
		//var cal_sel_date = document.getElementById("global_context_caldt").value;
		//alert($("#anps_day_name").html());
		var cal_sel_date_tmp = $("#anps_day_name").html();
		var cal_sel_date_arr = cal_sel_date_tmp.split("-");
		var cal_sel_date = cal_sel_date_arr[2]+"-"+cal_sel_date_arr[0]+"-"+cal_sel_date_arr[1];
		//alert(cal_sel_date);
		//return false;

		var anps_sel_fac = document.getElementById("anps_sel_fac").value;
		var anps_sel_pro = document.getElementById("anps_sel_pro").value;
		var anps_sel_tmp = document.getElementById("anps_sel_tmp").value;

		var start_hour = document.getElementById("start_hour").value;
		var start_min = document.getElementById("start_min").value;
		var start_time = document.getElementById("start_time").value;

		var end_hour = document.getElementById("end_hour").value;
		var end_min = document.getElementById("end_min").value;
		var end_time = document.getElementById("end_time").value;
		
		var comm = document.getElementById("comments").value;
		var template_type = document.getElementById('prov_sch_add_type').value;
		//alert("save_prov_sch.php?anps_sel_tmp="+anps_sel_tmp+"&cal_sel_date="+cal_sel_date+"&start_hour="+start_hour+"&start_min="+start_min+"&start_time="+start_time+"&end_hour="+end_hour+"&end_min="+end_min+"&end_time="+end_time+"&anps_sel_pro="+anps_sel_pro+"&anps_sel_fac="+anps_sel_fac);
		$.ajax({
			url: "save_prov_sch.php?anps_sel_tmp="+anps_sel_tmp+"&cal_sel_date="+cal_sel_date+"&start_hour="+start_hour+"&start_min="+start_min+"&start_time="+start_time+"&end_hour="+end_hour+"&end_min="+end_min+"&end_time="+end_time+"&anps_sel_pro="+anps_sel_pro+"&anps_sel_fac="+anps_sel_fac+"&comm="+escape(comm)+"&template_type="+template_type,
			success: function(resp){	
				//document.write(resp);
				var arr_resp = resp.split("~");
				top.show_loading_image("hide");
				load_calendar(arr_resp[0], arr_resp[1], '', false);
				$('#div_add_prov_form').modal('hide');
			}
		});
	}
}

function checkBoxMultiValChk(){	
	
	var str_multi = "";
	var obj_multi = document.getElementsByName("facilities[]");
	for(i = 0; i < obj_multi.length; i++){
		if(obj_multi[i].checked == true){
			str_multi += obj_multi[i].value + ",";
		}
	}

	if(str_multi != ""){
		str_multi = str_multi.substr(0,str_multi.length-1);

		//setting cookie
		var name = 'facility';
				
		var date = new Date();
		date.setTime(date.getTime()+(1*24*60*60*1000));
		
		var expires = "; expires="+date.toGMTString();
		document.cookie = name+"="+str_multi+expires+"; path=/";
		
		//reloading templates
		var vmonth = document.getElementById("theMonth").value;
		var vyear = document.getElementById("theYear").value;
		var dt = document.getElementById("theDate").value;
		var strDayName = document.getElementById("strDayName").value;
		var dtVal = vyear+"-"+vmonth+"-"+dt;
		
		var prov = "";
		var selectbox = document.getElementsByName('sel_pro_month[]');
		if(selectbox.length == 1){
			var selectbox = document.getElementById('sel_pro_month');
			for ( var i = 0, l = selectbox.options.length, o; i < l; i++ ){  
				o = selectbox.options[i];
				if(o.selected == true){
					if(prov == ""){
						prov = o.value;
					}else{
						prov = o.value + "," + prov;
					}
				}
			}
		}else{
			for(i = 0; i < selectbox.length; i++){
				if(selectbox[i].checked == true){
					if(prov == ""){
						prov = selectbox[i].value;
					}else{
						prov = selectbox[i].value + "," + prov;
					}
				}
			}
		}
		
		var arr_prov = prov.split(",");
		var int_multi_p = "";
		//var obj_multi_p = document.getElementsByName("sel_pro_month[]");
		//alert(obj_multi_p);
		if(arr_prov.length == 1){
			int_multi_p = arr_prov[0];			
			//alert(int_multi_p);
			//var sel_pro_monthTEMP=document.getElementById("sel_pro_month").value;
			changeDay_physician(int_multi_p,vmonth,dt,vyear,'');
		}else{		
			//loadScheduler(dtVal,strDayName);
			see_sel_month();
		}
	}else{
		document.getElementById("day_save").innerHTML = '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:775px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Facility has been selected.</div>';
	}
}

/*
Function: toggle_sch_type
Purpose: to swtich between day, week , month schedulers
Author: AA
*/
function toggle_sch_type(mode, set_dt){
	
	if(!set_dt) set_dt = "";
	
	if(set_dt != ""){
		var arr_st_ed = set_dt.split("|");
		var arr_dt_st = arr_st_ed[0].split("-");
		var dt = arr_dt_st[1];
		var mn = arr_dt_st[0];
		var yr = arr_dt_st[2];
	}else{
		var dt = $("#global_date").val();
		var mn = $("#global_month").val();
		var yr = $("#global_year").val();
	}
	top.show_loading_image("show");
	
	if(mode == "week"){		
		top.fmain.document.location.href = "base_week_scheduler.php?sel_date="+mn+"-"+dt+"-"+yr;
	}else if(mode == "month"){
		top.fmain.document.location.href = "base_month_scheduler.php?sel_date="+mn+"-"+dt+"-"+yr;
	}else if(mode == "day"){
		top.fmain.document.location.href = "base_day_scheduler.php?sel_date="+mn+"-"+dt+"-"+yr;
	}
}

/**WEEK SCHEDULER VIEWER*/
function load_week_scheduler(selected_sess_facs, selected_sess_prov){
	//selecting facility from session, if any
	set_facilities(selected_sess_facs);
	$("#facilities").selectpicker("refresh");
	//selecting providers from session, if any
	set_providers(selected_sess_prov);
	
	load_week_appt_schedule(selected_sess_facs, selected_sess_prov);
}

function initScrollLayer(){	
/*
	 wndo0 = new dw_scrollObj('wn_0', 'lyr1_0');			
	 dw_scrollObj.GeckoTableBugFix('wn_0');

	 wndo1 = new dw_scrollObj('wn_1', 'lyr1_1');			
	 dw_scrollObj.GeckoTableBugFix('wn_1');
	
	 wndo2 = new dw_scrollObj('wn_2', 'lyr1_2');			
	 dw_scrollObj.GeckoTableBugFix('wn_2');	
	
	 wndo3 = new dw_scrollObj('wn_3', 'lyr1_3');			
	 dw_scrollObj.GeckoTableBugFix('wn_3');
	 
	 wndo4 = new dw_scrollObj('wn_4', 'lyr1_4');			
	 dw_scrollObj.GeckoTableBugFix('wn_4');	
	 
	 wndo5 = new dw_scrollObj('wn_5', 'lyr1_5');			
	 dw_scrollObj.GeckoTableBugFix('wn_5');	
	 
	 wndo6 = new dw_scrollObj('wn_6', 'lyr1_6');			
	 dw_scrollObj.GeckoTableBugFix('wn_6');
	 */
}

/**MONTH SCHEDULER VIEWER*/
function load_month_scheduler(selected_sess_facs, selected_sess_prov){
	top.show_loading_image("hide");
	//selecting facility from session, if any
	set_facilities(selected_sess_facs);
	$("#facilities").selectpicker("refresh");
	//selecting providers from session, if any
	set_providers(selected_sess_prov);
}

function searchPatientInFrontDesk(obj){
	var patientdetails = obj.value.split(':');
	if(isNaN(patientdetails[0]) == false){
		document.getElementById("txt_patient_app_name").value = patientdetails[1];
		document.getElementById("hd_patient_id").value = patientdetails[0];
		pre_load_front_desk(patientdetails[0],'','');	
	}
}

function selPatient_frontdesk(){
	if(typeof(top.update_iconbar)!='undefined') top.update_iconbar();
	var lname_val = document.getElementById("txt_patient_app_name").value;
	var findValArr = document.getElementById("findByShow").value.split(':');
	if(isNaN(findValArr[0])){
		var findBy = findValArr[0];
	}else{
		var findBy = findValArr[2];	
	}
	if(isNaN(lname_val) == true){
		if((lname_val[0].toLowerCase() == "e") && (parseInt(lname_val.substring(1, lname_val.length)) > 0)){
			lname_val = lname_val.substring(1, lname_val.length);
			findBy = "External MRN";
		}
	}
	if(isNaN(lname_val) || (isNaN(lname_val)==false && findBy=='Ins.Policy')){
		window.open("search_patient_popup.php?sel_by="+findBy+"&txt_for="+lname_val+"&btn_sub=Search&call_from=scheduler","PatientWindow","width=800,height=500,top=420,left=150,scrollbars=yes");
	}else{
		$.ajax({
			url: 'chk_patient_exists.php',
			type: 'POST',
			data: 'pid='+lname_val+'&findBy='+findBy,
			success: function(resultData)
			{
                if(resultData.length > 1) resultData = JSON.parse(resultData);
                
                if(resultData.hasOwnProperty('askForReason')==true)
                {
                    var patId = resultData.patId;
					var bgPriv = resultData.bgPriv;
					if( findBy=='External MRN' )
					{
						document.getElementById( "findByShow" ).value = 'Active';
						document.getElementById( "txt_patient_app_name" ).value = patId;
					}
					top.core_restricted_prov_alert(patId, bgPriv,'','');
                }
                else if(resultData == 'n')
				{
					top.fAlert('Patient not found');	
				}
				else
				{
					pid = eval(resultData);
					if( findBy=='External MRN' )
					{
						document.getElementById( "findByShow" ).value = 'Active';
						document.getElementById( "txt_patient_app_name" ).value = pid;
					}
					pre_load_front_desk(pid);
				}				
			}
		});		
	}
	return false;
}

function patient_erx_registration(){	
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	window.open("load_patient_erx_registration.php?sel_date="+sel_date+"&facility_id="+sel_fac,'erx_reg','width=500,height=300');	
}

function send_to_forum(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var urlFile = "forum_send_to.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date;
	var h = intClentWinH;
	top.popup_win(urlFile,'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	return;
}

function pre_auth(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var h = parseInt(intClentWinH-100);
	var urlFile = "pre_auth_send.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date+"&height="+h;
	top.popup_win(urlFile,'width=1200px,height='+h+'px,toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,left=10,top=10');
	return;
}


function realtime_medicare_eligibility(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var urlRTEFile = "realtime_eligibility_All.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date;
	var h = intClentWinH;
	window.open(urlRTEFile,'allAPPRTE','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	return;
	/*if(confirm("Eligibility Checking process may take several minutes.\nAre you sure to start the process?")){
		top.show_loading_image("show");

		var sel_date = get_selected_date();
		var sel_fac = get_selected_facilities();
		var sel_pro = get_selected_providers();

		$.ajax({
			url: "realtime_medicare_eligibility.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date,
			success: function(resp){
				//document.getElementById("txt_comments").value = resp;
				//return;
				if(typeof(resp) != "undefined"){
					var arrResp = resp.split("-");					
					if((arrResp[0] == 1) || (arrResp[0] == 2) || (arrResp[0] == 3)){
						top.show_loading_image("hide");
						
						//loading scheduler
						//load_calendar(day_sel_date, arrResp[2], '', false);
						alert(arrResp[1]);
						return false;
					}
				}
				var schedule_id = $("#global_context_apptid").val();
				var sa_date = get_selected_date();						
				$.ajax({
					url: "get_day_name.php?load_dt="+sa_date,
					success: function(day_name){
						load_appt_schedule(sa_date, day_name, schedule_id, '', false);
						top.show_loading_image("hide");
					}
				});
				//top.show_loading_image("hide");
				//loading scheduler
				//load_calendar(day_sel_date, arrResp[2], '', false);
			}
		});
	}*/
}
function realtime_eligibility_file(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var urlRTEFile = "realtime_eligibility_file.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date;
	var h = intClentWinH;
	window.open(urlRTEFile,'winRTEFile','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function print_day_appt_report(div_name, mode){
	if(document.getElementById(div_name).style.display == "none"){
		document.getElementById(div_name).style.display = "block";
	}else{
		document.getElementById(div_name).style.display = "none";
	}
	if(mode){
		var window_count = document.getElementById("hid_prov_count").value;
		var window_new_width = window_count * 200;
		if(window_new_width > 1200){
			window_new_width = 1200;
			document.getElementById(div_name).style.overflow = "auto";
		}
		document.getElementById(div_name).style.width = window_new_width+"px";
		document.getElementById(div_name).style.right = "20px";
	}
}

function print_day_appt_report_process(providerID, rep_fac, eff_date, selMidDay, div_name){
	if(providerID == "get"){
		var prov = get_selected_providers();
		providerID = prov;
	}

	if(rep_fac == "get"){
		var loca = get_selected_facilities();
		rep_fac = loca;
	}

	if(eff_date == "get"){
		var sel_date = get_selected_date();
		var arr_sel_date = sel_date.split("-"); //ymd
		eff_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0]; //mdy
	}
	
	document.getElementById("eff_date").value = eff_date;
	document.getElementById("rep_fac").value = rep_fac;
	document.getElementById("providerID").value = providerID;
	document.getElementById("selMidDay").value = selMidDay;

	if(div_name != ""){
		print_day_appt_report(div_name);
	}
	document.frm_day_appt_print.submit();
}

function newPatient_info(id, sch_id, mode){
	if(!sch_id) sch_id = "";
	if(!mode) mode = "";
	var win_height = screen.height;
	if(id>0){
		if(document.getElementById("show_ci_demographics").value == "yes"){
			top.core_set_pt_session(top.fmain, id, '../patient_info/demographics/index.php');
		}else{
			top.popup_win("../scheduler/common/new_patient_info_popup_new.php?source=scheduler&mode="+mode+"&search=true&ci_pid="+id+"&sch_id="+sch_id+"&sel_date="+ga_sel_date+"&frm_status=show_check_in&popheight="+win_height, "width=1200,scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
		}
	}else{
			top.popup_win("../scheduler/common/new_patient_info_popup_new.php?source=scheduler&mode="+mode+"&search=true&ci_pid="+id+"&sch_id="+sch_id+"&frm_status=show_check_in&popheight="+win_height,  "width=1200,scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
	}
}

function superbill_info(id) {
	
	var current_caseId_sch = dgi("choose_prevcase").value;	
	var app_id = $(global_apptid).val();
	//alert(app_id);
	$.ajax({
		url: "get_latest_encounter.php?pid="+id+"&app_id="+app_id,
		success: function(resp){
			//alert(resp);
			if(resp==''){
				top.fAlert("No SuperBill is associated with this DOS.");
			}else {
				//../chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id="+resp+"&neww=1
				top.popup_win("../chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id="+resp+"&neww=1",'width=1170,height=630,top=10,left=10,scrollbars=yes,resizable=yes');	
			}
		}
	});
}

function showClSupplyOrderFromFrontDesk() {
	var SupplyUrl="../chart_notes/print_order.php?callFrom=clSupply";
	top.popup_win(SupplyUrl,"width=1090,scrollbars=1,height=690,top=2,left=0");
}

function contactLensDispense(){
	$.ajax({
		url: "getCldispenseDetails.php?",
		success: function(resp){
			if(resp > 0){
				var DispUrl="../chart_notes/cl_dispense.php?print_order_id="+resp;
				window.open(DispUrl,"ClDispOrderWindow","width=1060,scrollbars=0,height=370,top=2,left=0");
				//redirectToEnterCharges(pid);
			}else{
				top.fAlert("Sorry no encounter exist for this Recieved Supply.");
			}
		}
	});
}

function open_erx(id){
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var url="../chart_notes/erx_patient_selection.php?patientFromSheduler="+id;
	window.open(url,'erx_window','scrollbars=1,resizable=1,width='+parentWid+',height='+parenthei+'');
}

function openChartNotes(){
	//top.refresh_control_panel("Work_View");
	//top.fmain.location.href="../chart_notes/home.php";
	top.core_redirect_to("Work_View", "../chart_notes/main_page.php");
}

function descrip(pt_id, sch_id){
	var locs = get_selected_facilities();		
	url="recall_desc_save.php?patient_id="+pt_id+"&sch_id="+sch_id+"&loc="+locs;
	//top.popup_win(url,'repeata',''dependent=yes,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=800px,height=350px,left=30px,top=50px'');
	window.open(url,'repeata','dependent=yes,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=1200px,height=504px,left=30px,top=50px');
}

function get_Maketoday(varStr) {
	
}
function printFaceSheetFromFrontDesk(ptDocTempId,apptId) {
	var PrintUrl="../../interface/patient_info/common/process_pt_print_req.php?dont_print_medical=1&"+"print_form_id="+"&chart_nopro=3&face_sheet_scan=1&from=frontDesk&patient_info[]=face_sheet&apptId="+apptId;
	if(ptDocTempId) {
		var PrintUrl="../../interface/chart_notes/scan_docs/load_pt_docs.php?temp_id="+ptDocTempId+"&mode=facesheet&apptId="+apptId;
	}
	top.popup_win(PrintUrl,'printPatientFaceSheetWindow',"width=1050,resizable=yes,scrollbars=0,height=750,top=2,left=0");
}

function change_status_weekly(st_type, pt_id, ap_id){
	if(!pt_id) pt_id = $("#global_context_ptid").val();
	if(!ap_id) ap_id = $("#global_context_apptid").val();
	//alert(pt_id);
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);
	TestOnMenu();
	switch(st_type){
		case "201":

			break;
		case "18":
			
			break;
		case "17":

			break;
		case "13":
			newPatient_info(pt_id, ap_id, "weekly");
			break;
		case "11":
			$.ajax({
				url: "check_future_appt.php?ap_id="+ap_id,
				success: function(resp){alert('result received : '+resp);
					if(resp == "justdoit"){
						if(document.getElementById("show_payment_box_chk_out").value == "check out"){
						/*	var check_in_out_payment = top.popup_win("common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pt_id+"&sch_id="+ap_id,"width=1200,scrollbars=0,height=500,top=100,left=10");
							check_in_out_payment.focus();  */
							top.popup_win("common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pt_id+"&sch_id="+ap_id,"width=1200,scrollbars=0,height=500,top=100,left=10");
						}
					}else{
						top.fAlert('A patient cannot be checked out for a future appointment.');
						/*
						var pt_co_alert = "A patient cannot be checked out for a future appointment.";
						var checkout_msg = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_co_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtCheckoutAlert');\" /></div>";
						alert_box("imwemr - Check-out", checkout_msg, 300, "", 500, 150, "divPtCheckoutAlert", false, false);*/
						return false;
					}
				}
			});
			break;
	}
	
	var sel_fac = get_selected_facilities();
	//alert(sch_id+" "+st_type+" "+sel_date+" "+sel_fac+" "+pat_id);
	if(st_type == "18"){
		//loading reasons
		$.ajax({
			url: "load_reasons.php?pt_id="+pt_id+"&ap_id="+ap_id,
			success: function(resp){
				var arr_resp = resp.split("~~~~~");
				var title = "Cancellation Reason";
				
				var msg ='';
				msg ='<div class="row">';
				msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
				msg +='</div>';
				
				msg +='<div class="row">';
				msg +='<div class="col-sm-12">';
				msg +='<div class="form-group">';
				msg +='<label for="">Reason</label>';
				msg +='<select id="cancellation_reason" name="cancellation_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
				msg +='</div>';
				msg +='</div>';
				msg +='</div>';
					

				var btn1 = 'OK';
				var btn2 = 'Cancel';

				var misc = "DONOTASKPASSWORD";
				
				var func1 = 'save_cancellation_reason::::weekly';
				var func2 = 'hideConfirmYesNo';
				
				scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
			}
		});
	}else{
		getchange_status_weekly(ap_id, st_type, sel_fac, pt_id);
	}
}
function getchange_status_weekly(schedule_id, chg_to, loca, id){ 
	//alert("getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&dt=notrequied&patId="+id+"&sid="+Math.random());

	var reason = $("#global_apptactreason").val();

	$.ajax({
		url: "getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&reason="+reason+"&dt=notrequied&patId="+id+"&sid="+Math.random(),
		success: function(resp){			
			$("#global_apptactreason").val("");
			ids = resp.split("-");
			if(ids[1] == "11"){
				if(document.getElementById("show_payment_box_chk_out").value != "check out"){
					//top.core_redirect_to("Accounting", "../accounting/accountingTabs.php");
					top.change_main_Selection(top.document.getElementById('AccountingSB'));
				}else{
					load_week_appt_schedule();
				}
			}else{
				load_week_appt_schedule();
			}
		}
	});
}

function change_status_to_do(st_type){

	pt_id = $("#global_context_ptid").val();
	ap_id = $("#global_context_apptid").val();
	//alert(pt_id);
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);	
	TestOnMenu();
	//alert($("#global_context_ptid").val(pt_id));
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
     //alert('Desc : '+pt_id+' '+ap_id+' '+' '+sel_date+' '+sel_fac); 
	//alert(sch_id+" "+st_type+" "+sel_date+" "+sel_fac+" "+pat_id);
	
	getchange_status(ap_id, st_type, sel_date, sel_fac, pt_id);
	
	save_first_available_reason_todo();
}

function change_status(st_type, pt_id, ap_id){
	if(!pt_id) pt_id = $("#global_context_ptid").val();
	if(!ap_id) ap_id = $("#global_context_apptid").val();
	//alert(pt_id);
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);	
	TestOnMenu();
	switch(st_type){
		case "201":

			break;
		case "18":
			
			break;
		case "17":

			break;
		case "13":
			//newPatient_info(pt_id, ap_id);
			break;
		case "11":
			$.ajax({
				url: "check_future_appt.php?ap_id="+ap_id,
				success: function(resp){
					resultresp = resp;
					resultarr = resultresp.split('-');
					resp = resultarr[0];
					if(resp == "justdoit"){
						
						ga_ap_id=ap_id;
						ga_st_type=st_type;
						ga_sel_date=sel_date;
						ga_sel_fac=sel_fac;
						ga_pt_id=pt_id;
						
						$.ajax({
							url: 'get_ap_ids_by_patient.php',
							type: "POST",
							data: 'pt_id='+pt_id+'&sel_date='+sel_date+'&typ=CO',
							success: function(resp){
								appt_ids_result=$.parseJSON(resp);
								if(appt_ids_result.length > 1)
								{
									var msg="Is Check-Out be applied to the other appointments of the same patient";
									var trueFun="applyCOtrue('"+appt_ids_result +"','"+st_type+"')";
									var falseFun="applyCOFalse('"+st_type+"')";
									top.fancyConfirm(msg,"", "window.top.fmain."+trueFun,"window.top.fmain."+falseFun);
								}
								else 
								{
									ga_ap_id=ap_id;
									if(document.getElementById("show_payment_box_chk_out").value == "check out")
									{
										top.popup_win("common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pt_id+"&sch_id="+ap_id,"width=1305,scrollbars=0,height=600,top=100,left=10,resizable=yes");
									}
									getchange_status(ga_ap_id, st_type, sel_date, sel_fac, pt_id);
								}
							}
						});
						
						
						
						if(typeof resultarr[1] == "string" && resultarr[1] == "clTeach")
						{
							top.fAlert("Schedule CL teach Appointment");	
						}
						
					}else{
						var pt_co_alert = "A patient cannot be checked out for a future appointment.";
						top.fAlert(pt_co_alert);
						/*var checkout_msg = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_co_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtCheckoutAlert');\" /></div>";
						alert_box("imwemr - Check-out", checkout_msg, 300, "", 500, 150, "divPtCheckoutAlert", false, false);*/
						return false;
					}
				}
			});
			break;
	}
	
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
                //alert('Desc : '+pt_id+' '+ap_id+' '+' '+sel_date+' '+sel_fac); 
	//alert(sch_id+" "+st_type+" "+sel_date+" "+sel_fac+" "+pat_id);
	if(st_type == "18"){
		//loading reasons
		var lr_url = "load_reasons.php?pt_id="+pt_id+"&ap_id="+ap_id+"&cancel="+1;
		$.ajax({
			url: lr_url,
			success: function(resp){
				var arr_resp = resp.split("~~~~~");
				var title = "Cancellation Reason";
				var msg ='';
				msg ='<div class="row">';
				msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
				msg +='</div>';
				
				msg +='<div class="row">';
				msg +='<div class="col-sm-12">';
				msg +='<div class="form-group">';
				msg +='<label for="">Reason</label>';
				msg +='<select id="cancellation_reason" name="cancellation_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
				msg +='</div>';
				msg +='</div>';
				msg +='</div>';
				
				msg +=arr_resp[2];
				
				var btn1 = 'OK';
				var btn2 = 'Cancel';

				var misc = "DONOTASKPASSWORD";
				
				var func1 = 'save_cancellation_reason';
				var func2 = 'hideConfirmYesNo';
				
				scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
			}
		});
	}
	else if(st_type == "271"){
		
		//loading reasons
		var lr_url = "load_reasons.php?pt_id="+pt_id+"&ap_id="+ap_id+"&ctype=first_available&cancel="+1;
				
		$.ajax({
			url: lr_url,
			success: function(resp){
				if(is_remote_server() == true)
				{
					resp = json_resp_handle(resp);				
				}				
				var arr_resp = resp.split("~~~~~");
				var title = "First Available";
				
				var msg ='';
				msg ='<div class="row">';
				msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
				msg +='</div>';
				
				msg +='<div class="row">';
				msg +='<div class="col-sm-12">';
				msg +='<div class="form-group">';
				msg +='<label for="">Reason</label>';
				msg +='<select id="cancellation_reason" name="cancellation_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
				msg +='</div>';
				msg +='</div>';
				msg +='</div>';
				
				msg +=arr_resp[2];

				var btn1 = 'OK';
				var btn2 = 'Cancel';

				var misc = "DONOTASKPASSWORD";
				
				var func1 = 'save_first_available_reason';
				var func2 = 'hideConfirmYesNo';
				
				scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
			}
		});
	}else if(st_type != "11")
	{
		if(st_type=="13")
		{
			ga_ap_id=ap_id;
			ga_st_type=st_type;
			ga_sel_date=sel_date;
			ga_sel_fac=sel_fac;
			ga_pt_id=pt_id;
			$.ajax({url:'get_ap_ids_by_patient.php',type:'POST',data:'pt_id='+pt_id+'&sel_date='+sel_date, complete:get_ap_ids_by_patient_and_sel_date});
		}
		else
		{
			getchange_status(ap_id, st_type, sel_date, sel_fac, pt_id);
		}
		
	}
	
	if(MD_API=='On'){
		//CODE ADDED TO SEND INFO TO API :
		var sel_proc_name=$('#sel_proc_id option:selected').text();	
		$.ajax({
			url: 'sending_info_to_api.php',
			type: "POST",
			data: 'pt_id='+pt_id+'&ap_id='+ap_id+'&st_type='+st_type+'&sel_proc_name='+sel_proc_name,
			success: function(resp){
			}
		});	
	}
}

function applyCOtrue(ga_ap_id, st_type)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var ap_id = $("#global_context_apptid").val();
	if(document.getElementById("show_payment_box_chk_out").value == "check out")
	{
		top.popup_win("../scheduler/common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pat_id+"&sch_id="+ap_id,"width=1305,scrollbars=0,height=600,top=100,left=10,resizable=yes");
	}
	getchange_status(ga_ap_id, st_type, sel_date, sel_fac, pat_id);
}
function applyCOFalse(st_type)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var ap_id = $("#global_context_apptid").val();
	if(document.getElementById("show_payment_box_chk_out").value == "check out")
	{
		top.popup_win("../scheduler/common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pat_id+"&sch_id="+ap_id,"width=1305,scrollbars=0,height=600,top=100,left=10,resizable=yes");
	}
	getchange_status(ap_id, st_type, sel_date, sel_fac, pat_id);
}

function get_ap_ids_by_patient_and_sel_date(respData)
{
    appt_ids_result=$.parseJSON(respData.responseText);
    if(appt_ids_result.length > 1)
	{
		var msg="Is Check-in be applied to the other appointments of the same patient";
		var trueFun="applyCItrue('"+appt_ids_result +"')";
		var falseFun="applyCIFalse()";
		top.fancyConfirm(msg,"", "window.top.fmain."+trueFun,"window.top.fmain."+falseFun);
	}
	else
	{
		newPatient_info(ga_pt_id, ga_ap_id);
		if(document.getElementById("show_ci_demographics").value == "yes" || !$("#CHECKIN_ON_DONE").val() )
		{
			getchange_status(ga_ap_id, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
		}
	}
}

function applyCItrue(appt_ids_result)
{
	newPatient_info(ga_pt_id, ga_ap_id);
	if(document.getElementById("show_ci_demographics").value == "yes" || !$("#CHECKIN_ON_DONE").val())
	{
		getchange_status(appt_ids_result, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
	}
}
function applyCIFalse()
{
	newPatient_info(ga_pt_id, ga_ap_id);
	if(document.getElementById("show_ci_demographics").value == "yes" || !$("#CHECKIN_ON_DONE").val())
	{
		getchange_status(ga_ap_id, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
	}
}

function saveFirstAvailable(keep_orignal,schedule_id, sel_month, sel_week, sel_time)
{
	//saving first available request
	var reason = $("#global_apptactreason").val();
	
	var send_uri = "saveFirstAvailable.php?keep_orignal="+keep_orignal+"&sel_month="+sel_month+"&sel_week="+sel_week+"&sel_time="+sel_time+"&sch_id="+schedule_id+"&reason="+reason+"&sid="+Math.random();
	if(is_remote_server() == true)
	{
		sever_id_val = get_server_id();
		rN = Math.random();
		reqTaskArray = {"sch":{"req_mode":"change_appt_status","server_id":sever_id_val,"keep_orignal":keep_orignal,"sel_month":sel_month,"sel_week":sel_week,"sel_time":sel_time,"sch_id":schedule_id,"reason":reason,"sid":rN}};
		send_uri = gl_api_addr+"?taskArray="+ju_encode_reqArr(reqTaskArray);
	}	
	$.ajax({
		url: send_uri,
		success: function(resp){
			$("#global_apptactreason").val("");
			if(is_remote_server() == true)
			{
				resp = json_resp_handle(resp);				
			}				
		}
	});	
}


function getchange_status(schedule_id, chg_to, dt, loca, id){ 
               	
	//alert("getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&dt="+dt+"&patId="+id+"&sid="+Math.random());
	var reason = $("#global_apptactreason").val();
	var send_uri = "getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&dt="+dt+"&patId="+id+"&reason="+reason+"&keepOrg="+firstAvail_keepOrg+"&sid="+Math.random();
		
	$.ajax({
		url: send_uri,
		success: function(resp){
			$("#global_apptactreason").val("");
			if(is_remote_server() == true)
			{
				resp = json_resp_handle(resp);				
			}				
			ids = resp.split("-");
			var curDate = ids[3]+'-'+ids[4]+'-'+ids[5];
			if(ids[1] == "11"){
				if(document.getElementById("show_payment_box_chk_out").value != "check out"){
					//top.core_redirect_to("Accounting", "../accounting/accountingTabs.php");
					top.change_main_Selection(top.document.getElementById('AccountingSB'));
				}else{
					pre_load_front_desk(ids[0], ids[2], false);	
					load_appt_schedule(curDate, ids[6], '', "nonono");
				}
			}else{
				pre_load_front_desk(ids[0], ids[2], false);	
				load_appt_schedule(curDate, ids[6], '', "nonono");
			}
		}
	});
}

function showPateintOtherTxtBox(obj1, obj2, val, otherPatientStatusVal){
	var obj1Id = obj1;
	var obj2Id = obj2;
	var allowVal = val;
	var arrAllowVal = allowVal.split('-');
	for(var a=0; a < arrAllowVal.length; a++ ){
		if(document.getElementById(obj1Id).value == arrAllowVal[a]){
			//document.getElementById(obj2Id).style.display = 'block';
			//var statusTdData = "<input name=\"otherPatientStatus\" id=\"otherPatientStatus\" style=\"display:block;\" class=\"form-control\" value=\""+otherPatientStatusVal+"\" />";
			$("#otherPatientStatus").val(otherPatientStatusVal);
			$("#otherPatientStatus").css('display','inline-block');
			//document.getElementById('tdOtherPatientStatus').innerHTML = statusTdData;
			a = arrAllowVal.length;
		}else{
			//document.getElementById(obj2Id).style.display = 'none';
			//var statusTdData = "<input name=\"otherPatientStatus\" id=\"otherPatientStatus\" style=\"display:none;\" value=\""+otherPatientStatusVal+"\"  class=\"form-control\"/>";
			//document.getElementById('tdOtherPatientStatus').innerHTML = statusTdData;
			//document.getElementById('tdOtherPatientStatus').style.display = 'inline-block';
			
			
			$("#otherPatientStatus").val(otherPatientStatusVal);
			$("#otherPatientStatus").css('display','none');
			$("#tdOtherPatientStatus").css('display','inline-block');
		}
	}
	if(document.getElementById(obj1Id).value == 'Deceased'){
		$("#dod_patient_td").css('display','inline-block');
		$("#tdOtherPatientStatus").css('display','none');
	}else{
		$("#dod_patient").val('');
		$("#dod_patient_td").css('display','none');
	}
}

function showEditAddress(mode){
	if(mode == "open"){
		//document.getElementById("display_area").style.display = "none";
		//document.getElementById("editable_area").style.display = "block";
		$('#editable_area').modal('show');
	}
	if(mode == "close"){
		//document.getElementById("display_area").style.display = "block";
		//document.getElementById("editable_area").style.display = "none";
		$('#editable_area').modal('hide');
	}
}

function before_save_changes(pt_id, ap_id,fac_type){

	if(!pt_id) pt_id = "";
	if(!ap_id) ap_id = "";
	if( pt_id ) {
		
		if( $("#elem_patientStatus").length > 0 )
		{
			var pt_status = $("#elem_patientStatus").val();
			var prev_pt_status = $("#elem_patientStatus").data('prev-val');
			if( pt_status == 'Deceased' && prev_pt_status != 'Deceased' ) {
				msg = "Patient status changed to deceased.<br>All future appointments will be canceled.";
				window.top.fAlert(msg,'',"top.fmain.save_changes('"+pt_id+"', '"+ap_id+"','"+fac_type+"')",'','','Ok',true);
			}
			else {
				save_changes(pt_id, ap_id,fac_type)	
			}
		}
		else {
			save_changes(pt_id, ap_id,fac_type)
		}
	}
}
/*
Function: save_changes
Purpose: to save_changes from front desk
Author: AA
*/
function save_changes(pt_id, ap_id,fac_type){
	if(!pt_id) pt_id = "";
	if(!ap_id) ap_id = "";
	
	if(pt_id != ""){
		//patient specific data
		var pt_doctor_id = ($("#sel_fd_provider").length !== 0) ? $("#sel_fd_provider").val() : "";
		
		var pt_pcp_phy_id = ($("#pcp_id").length !== 0) ? $("#pcp_id").val() : "";
		var pt_pcp_phy = ($("#pcp_name").length !== 0) ? escape($("#pcp_name").val()) : "";
		var hidd_pt_pcp_phy = ($("#hidd_pcp_name").length !== 0) ? escape($("#hidd_pcp_name").val()) : "";

		var pt_ref_phy_id = ($("#front_primary_care_id").length !== 0) ? $("#front_primary_care_id").val() : "";
		var pt_ref_phy = ($("#front_primary_care_name").length !== 0) ? escape($("#front_primary_care_name").val()) : "";
		var hidd_pt_ref_phy = ($("#hidd_front_primary_care_name").length !== 0) ? escape($("#hidd_front_primary_care_name").val()) : "";
		
		var pt_street1 = ($("#frontAddressStreet").length !== 0) ? escape($("#frontAddressStreet").val()) : "";
		var pt_street2 = ($("#frontAddressStreet2").length !== 0) ? escape($("#frontAddressStreet2").val()) : "";
		var pt_city = ($("#frontAddressCity").length !== 0) ? escape($("#frontAddressCity").val()) : "";
		var pt_state = ($("#frontAddressState").length !== 0) ? escape($("#frontAddressState").val()) : "";
		var pt_zip = ($("#frontAddressZip").length !== 0) ? escape($("#frontAddressZip").val()) : "";
		var pt_zip_ext = ($("#frontAddressZip_ext").length !== 0) ? escape($("#frontAddressZip_ext").val()) : "";

		var pt_email = ($("#email").length !== 0) ? escape($("#email").val()) : "";
		var pt_photo_ref = ($("#photo_ref").is(":checked")==true) ? 1 : 0;

		var pt_home_ph = ($("#phone_home").length !== 0) ? escape($("#phone_home").val()) : "";
		var pt_work_ph = ($("#phone_biz").length !== 0) ? escape($("#phone_biz").val()) : "";
		var pt_cell_ph = ($("#phone_cell").length !== 0) ? escape($("#phone_cell").val()) : "";
		
		//hidden fields regarding demographics change log/HX entry
		var hidd_prev_pt_street1 = ($("#hidd_prev_frontAddressStreet").length !== 0) ? escape($("#hidd_prev_frontAddressStreet").val()) : "";
		var hidd_prev_pt_street2 = ($("#hidd_prev_frontAddressStreet2").length !== 0) ? escape($("#hidd_prev_frontAddressStreet2").val()) : "";
		var hidd_prev_pt_city = ($("#hidd_prev_frontAddressCity").length !== 0) ? escape($("#hidd_prev_frontAddressCity").val()) : "";
		var hidd_prev_pt_state = ($("#hidd_prev_frontAddressState").length !== 0) ? escape($("#hidd_prev_frontAddressState").val()) : "";
		var hidd_prev_pt_zip = ($("#hidd_prev_frontAddressZip").length !== 0) ? escape($("#hidd_prev_frontAddressZip").val()) : "";
		var hidd_prev_pt_zip_ext = ($("#hidd_prev_frontAddressZip_ext").length !== 0) ? escape($("#hidd_prev_frontAddressZip_ext").val()) : "";
		var hidd_prev_pt_email = ($("#hidd_prev_email").length !== 0) ? escape($("#hidd_prev_email").val()) : "";
		var hidd_prev_pt_home_ph = ($("#hidd_prev_phone_home").length !== 0) ? escape($("#hidd_prev_phone_home").val()) : "";
		var hidd_prev_pt_work_ph = ($("#hidd_prev_phone_biz").length !== 0) ? escape($("#hidd_prev_phone_biz").val()) : "";
		var hidd_prev_pt_cell_ph = ($("#hidd_prev_phone_cell").length !== 0) ? escape($("#hidd_prev_phone_cell").val()) : "";
		
		var hidd_prev="&hidd_prev_pt_street1="+ hidd_prev_pt_street1 +"&hidd_prev_pt_street2="+ hidd_prev_pt_street2 +"&hidd_prev_pt_city="+ hidd_prev_pt_city +"&hidd_prev_pt_state="+ hidd_prev_pt_state +"&hidd_prev_pt_zip="+ hidd_prev_pt_zip +"&hidd_prev_pt_zip_ext="+ hidd_prev_pt_zip_ext +"&hidd_prev_pt_email="+ hidd_prev_pt_email +"&hidd_prev_pt_home_ph="+ hidd_prev_pt_home_ph +"&hidd_prev_pt_work_ph="+ hidd_prev_pt_work_ph +"&hidd_prev_pt_cell_ph="+ hidd_prev_pt_cell_ph;
		
		var pt_status = ($("#elem_patientStatus").length !== 0) ? $("#elem_patientStatus").val() : "";
		var pt_other_status = ($("#otherPatientStatus").length !== 0) ? escape($("#otherPatientStatus").val()) : "";
		var pt_dod_patient = ($("#dod_patient").length !== 0) ? escape($("#dod_patient").val()) : "";

		//appointment specific data
		if(ap_id != ""){
			var ap_routine_exam = ($("#chkRoutineExam").length !== 0) ? $("#chkRoutineExam").val() : "";
			var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
			var ap_notes = ($("#txt_comments").length !== 0) ? encodeURIComponent($("#txt_comments").val()) : "";
			
			var pri_eye_site = ($("#pri_eye_site").length !== 0) ? $("#pri_eye_site").val() : "";
			var sec_eye_site = ($("#sec_eye_site").length !== 0) ? $("#sec_eye_site").val() : "";
			var ter_eye_site = ($("#ter_eye_site").length !== 0) ? $("#ter_eye_site").val() : "";
			
			var ap_pickup_time = ($("#pick_up_time").length !== 0) ? escape($("#pick_up_time").val()) : "";
			var ap_arrival_time = ($("#arrival_time").length !== 0) ? escape($("#arrival_time").val()) : "";
			var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";
			var sec_ap_procedure = ($("#sec_sel_proc_id").length !== 0) ? $("#sec_sel_proc_id").val() : "";
			var ter_ap_procedure = ($("#ter_sel_proc_id").length !== 0) ? $("#ter_sel_proc_id").val() : "";
		}
		var facility_type_provider = ($("#facility_type_provider").length !== 0) ? escape($("#facility_type_provider").val()) : "";
		if(typeof(fac_type)!='undefined' && fac_type!='' && fac_type!='0' && facility_type_provider==""){
			top.fAlert("Please select Surgeon","",$("#facility_type_provider").focus());
			top.show_loading_image("hide");
			return false;
		}
		if(typeof(fac_type)!='undefined' && (fac_type=='' || fac_type=='0')){
		facility_type_provider = "";
		}
		var appt_duration = ($("#appt_duration").length !== 0) ? escape($("#appt_duration").val()) : "";
		var chk_prev_slot_val = ($("#chk_prev_slot_val").length !== 0) ? escape($("#chk_prev_slot_val").val()) : "";
		var slot_time_changed=0;var add_appt_duration="";
		
		add_appt_duration="&appt_duration="+appt_duration;
		
		var referral = ($("input[type=checkbox]#sa_ref_management").is(":checked") ? 1 : 0);
		var ref_management = "&pt_referral="+referral;
		if(chk_prev_slot_val!='' && appt_duration!=''){
			if(chk_prev_slot_val!=appt_duration){slot_time_changed=1;}
		}
		
        var sa_verification = ($("input[type=checkbox]#sa_verification_req").is(":checked") ? 1 : 0);
        var verification_req = "&pt_verification="+sa_verification;
		
		var send_uri = "save_changes.php?save_type=save&pt_id="+pt_id+"&ap_id="+ap_id+"&pt_doctor_id="+pt_doctor_id+"&pt_pcp_phy_id="+pt_pcp_phy_id+"&pt_pcp_phy="+pt_pcp_phy+"&pt_ref_phy_id="+pt_ref_phy_id+"&pt_ref_phy="+pt_ref_phy+"&hidd_ref="+hidd_pt_ref_phy+"&hidd_pcp="+hidd_pt_pcp_phy+"&pt_street1="+pt_street1+"&pt_street2="+pt_street2+"&pt_city="+pt_city+"&pt_state="+pt_state+"&pt_zip="+pt_zip+"&pt_zip_ext="+pt_zip_ext+"&pt_email="+pt_email+"&pt_photo_ref="+pt_photo_ref+"&pt_home_ph="+pt_home_ph+"&pt_work_ph="+pt_work_ph+"&pt_cell_ph="+pt_cell_ph+"&pt_status="+pt_status+"&pt_other_status="+pt_other_status+"&pt_dod_patient="+pt_dod_patient+"&ap_routine_exam="+ap_routine_exam+"&ap_ins_case_id="+ap_ins_case_id+"&ap_notes="+ap_notes+"&pri_eye_site="+pri_eye_site+"&sec_eye_site="+sec_eye_site+"&ter_eye_site="+ter_eye_site+"&ap_pickup_time="+ap_pickup_time+"&ap_arrival_time="+ap_arrival_time+"&ap_procedure="+ap_procedure+"&sec_ap_procedure="+sec_ap_procedure+"&ter_ap_procedure="+ter_ap_procedure+"&facility_type_provider="+facility_type_provider+add_appt_duration+ref_management+verification_req+hidd_prev;
		//return false;
		$.ajax({
			url: send_uri,
			type: "POST",
			success: function(resp){
				var arr_resp = resp.split("~");
				if(arr_resp[0] == "save"){
					if($('#editable_area').hasClass('in'))
					{
						$('#editable_area').on('hidden.bs.modal', function () {
							reload_on_save(pt_id, ap_id, false, ap_procedure, sec_ap_procedure, ter_ap_procedure, slot_time_changed, arr_resp);
						});
						$('#editable_area').modal('hide');//hide pt info edit div
					}else {reload_on_save(pt_id, ap_id, false, ap_procedure, sec_ap_procedure, ter_ap_procedure, slot_time_changed, arr_resp);}
				}

				if( arr_resp[3] > 0 ) { 
					if( arr_resp[2]) {
						load_appt_schedule(arr_resp[2], arr_resp[1], ap_id, false);
					}
				}

			}
		});
	}
}
function reload_on_save(pt_id, ap_id, fal, ap_procedure, sec_ap_procedure, ter_ap_procedure, slot_time_changed, arr_resp)
{
	pre_load_front_desk(pt_id, ap_id, fal); 
	if($("#global_apptsecpro").val() == 0) {$("#global_apptsecpro").val("");} 
	if($("#global_apptterpro").val() == 0) {$("#global_apptterpro").val("");}

	if((ap_procedure != "" && $("#global_apptpro").val() != "" && $("#global_apptpro").val() != ap_procedure) || ($("#global_apptsecpro").val() != sec_ap_procedure) || ($("#global_apptterpro").val() != ter_ap_procedure) || slot_time_changed==1){
		load_appt_schedule(arr_resp[2], arr_resp[1], "", "", false);
	}
}
function drag_name(ap_id, pt_id, mode, e){

	if( check_deceased() ) return false;

	if(!ap_id) ap_id = "";
	if(!pt_id) pt_id = "";
	if(!e) e = window.event;
	
	if(ap_id == "get") TestOnMenu();
	if(pt_id == "get") pt_id = $("#global_context_ptid").val();
	if(ap_id == "get") ap_id = $("#global_context_apptid").val();	

	$("#global_ptid").val(pt_id);
	$("#global_apptid").val(ap_id);
	$("#global_apptact").val(mode);

	$("#appt_drag").addClass("sc_title_font");
	$("#appt_drag").css("backgroundColor", "");			
	$("#appt_drag").width("320");
	$("#appt_drag").css("top", e.clientY);
	$("#appt_drag").css("left", e.clientX);
	
	var sel_proc_id = $("#sel_proc_id").val();
	var proc_lbls=$("#sel_proc_id").find('option:selected').attr('data-labels');
	if(proc_lbls)
	{
		proc_lbls_arr = proc_lbls.split('~:~');
	}
	var sec_sel_proc_id = $('#sec_sel_proc_id').val();
	var ter_sel_proc_id = $('#ter_sel_proc_id').val();
	
	var send_uri = "schedule_new_tooltip.php?tool_sch_id="+ap_id+"&pate_id="+pt_id+"&sel_proc_idR="+sel_proc_id+"&sec_sel_proc_id="+sec_sel_proc_id+"&ter_sel_proc_id="+ter_sel_proc_id;
		
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			if(is_remote_server() == true)
			{
				resp = json_resp_handle(resp);				
			}			
			$("#appt_drag").html(resp);
			$("#appt_drag").css("display", "block");
			//document.attachEvent('onmousemove', move_trail);
			$(document).bind("mousemove", move_trail);
		}
	});
}

function move_trail(){
	window.status = window.event.clientX + "," + window.event.clientY
	$("#appt_drag").css("top", window.event.clientY - 5);
	$("#appt_drag").css("left", window.event.clientX + 20);
}

function keyPressHandler(){
	if(event.keyCode==27){
		$(document).unbind("mousemove", move_trail);
		$("#global_apptact").val('');
		hide_tool_tip();
	}
}
//jquery function to handle ESCAPE key pressed because above function does work in IE only
$(document).keydown(function(e) {
    if (e.keyCode == 27) {
		try{
		   $(document).unbind("mousemove", move_trail);
			$("#global_apptact").val('');
			hide_tool_tip();
		}catch(e){//do nothing
		}
    }
});
function hide_tool_tip(){
	$("#appt_drag").css("display", "none");
}

function pop_menu_time(ap_fac, ap_doc, ap_sttm, ap_stdt, mode, ap_lbty, ap_lbtx, ap_lbcl, ap_tmp_id){
	//alert(ap_lbty+", "+ap_lbtx+", "+ap_lbcl);
	if(mode == "open" || mode == "on"){
		document.getElementById("ContextMenu_1_blk_block").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_open").style.display = "none";
		document.getElementById("ContextMenu_1_blk_label").style.display = "inline-block";
	}else if(mode == "off"){
		document.getElementById("ContextMenu_1_blk_block").style.display = "none";
		document.getElementById("ContextMenu_1_blk_open").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_label").style.display = "none";
	}else{
		document.getElementById("ContextMenu_1_blk_block").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_open").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_label").style.display = "inline-block";
	}
	
	if($.trim(ap_lbty) != "" && ap_lbtx != "")
	{
		$("#ContextMenu_1_blk_remove_label").css({'display':'inline-block'});
	}
	else
	{
		$("#ContextMenu_1_blk_remove_label").css({'display':'none'});				
	}
	
	$("#global_context_slsttm").val(ap_sttm);
	$("#global_context_sldoc").val(ap_doc);
	$("#global_context_slfac").val(ap_fac);
	$("#global_context_slstdt").val(ap_stdt);
	$("#global_context_apptlbty").val(ap_lbty);
	$("#global_context_apptlbtx").val(ap_lbtx);
	$("#global_context_apptlbcl").val(ap_lbcl);
	$("#global_context_appt_tmp_id").val(ap_tmp_id);
	
	if (window.event.button == 2){
		var evt = window.event || event;

		var rqOffset = 0;
		if(document.getElementById('mn1_1'))
		{
			rqOffset = document.getElementById('mn1_1').getBoundingClientRect().left;	
		}		
		var left_offset = Math.abs(parseInt($("#lyr1").css('left')));
		var cony = (evt.clientX + left_offset) - rqOffset;
		var maxy = (evt.clientY + $("#mn1_1").scrollTop()) - 110;			

		//================Scroll issue in case of Safari and Mac===================//
		if(parseInt(jQuery.browser.version)==534 || (parseInt(jQuery.browser.version)==537) || (parseInt(jQuery.browser.version)==600) || (parseInt(jQuery.browser.version)==601)){
			var scroll_hei=$("#mn1_1").scrollTop();
			if(parseInt(scroll_hei)>0){
				scroll_hei=parseInt(scroll_hei);
			}
			var topheight=parseInt(maxy+scroll_hei);
			var elemObjAvail = $('#sch_left_portion').css('display');
			if(elemObjAvail=='block'){
			cony=parseInt(cony-580);
			}
			maxy=parseInt(topheight-120);
		}
		//code to check if pop menu going out of window width
		var posCheck=parseInt(window.innerWidth)-parseInt($("#mn1_1").width());
		posCheck=parseInt(posCheck)+340+parseInt(cony);

		if(posCheck>window.innerWidth){
			cony-=(parseInt(posCheck)-parseInt(window.innerWidth))+20;
		}
		//=========================================================================//
		if(mode == "block"){
			TestOnMenu();			
			ToggleContext_2_blk_only(cony, maxy);
		}else{
			TestOnMenu();
			ToggleContext_2_blk(cony, maxy);
		}
		document.oncontextmenu = blank_function;
	}else{
		TestOnMenu();
	}
}

function set_replace_label(req_lbl)
{
	$("#global_replace_lbl").val(req_lbl);	
}

function ToggleContext_2_blk(cony,maxy){		
	document.getElementById("ContextMenu_blk").style.position = 'absolute';
	document.getElementById("ContextMenu_blk").style.display = 'block';
	document.getElementById("ContextMenu_blk").style.pixelLeft = cony;		
	document.getElementById("ContextMenu_blk").style.pixelTop = maxy;
	IsOn = true;		
	window.event.returnValue = false;	
	var bro_ver=navigator.userAgent.toLowerCase();
	if(bro_ver.search("chrome")>1){
		$("#ContextMenu_blk").css({"display":"inline-block",top: maxy, left: cony});
	}			
}

function ToggleContext_2_blk_only(cony,maxy){	
	//document.getElementById("ContextMenu_blk_only").style.width = "100px";		
	document.getElementById("ContextMenu_blk_only").style.position = 'absolute';
	document.getElementById("ContextMenu_blk_only").style.display = 'block';
	document.getElementById("ContextMenu_blk_only").style.pixelLeft = cony;		
	document.getElementById("ContextMenu_blk_only").style.pixelTop = maxy;
	
	var bro_ver=navigator.userAgent.toLowerCase();
	if(bro_ver.search("chrome")>1){
		$("#ContextMenu_blk_only").css({"display":"inline-block",top: maxy, left: cony});
	}	
	IsOn = true;		
	window.event.returnValue = false;
}

function todo_options(ap_doc, ap_fac, mode){
	if (window.event.button == 2){
		TestOnMenu();
		var load_dt = get_selected_date();
		$.ajax({ 
			url: "todo_options.php?load_dt="+load_dt+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac,
			success: function(resp){
				var arr_resp = resp.split("~~~~~");
				
				$("#todo_div").modal("show");
				$("#todo_content").html(arr_resp[1]);
				$("#todo_date").html(arr_resp[0]);
				$("#blk_lk_loca").selectpicker("refresh");
			}
		});
		document.oncontextmenu = blank_function;
	}else {
		TestOnMenu();
	}
}

function save_todo(){		
		var loca = selectedValuesStr("blk_lk_loca");
		if(loca=='')
		{
			top.fAlert('Select any facility to proceed')
			return false;
		}else {

			var load_dt = get_selected_date();
	
			
			var phy_id = $("#phy_id").val();
			var time_from_hour = $("#todo_time_from_hour").val();
			var time_from_mins = $("#todo_time_from_mins").val();
			var ap1 = $("#todo_ap1").val();
			var time_to_hour = $("#todo_time_to_hour").val();
			var time_to_mins = $("#todo_time_to_mins").val();
			var ap2 = $("#todo_ap2").val();
			var send_uri = "save_todo.php?load_dt="+load_dt+"&phy_id="+phy_id+"&loca="+loca+"&time_from_hour="+time_from_hour+"&time_from_mins="+time_from_mins+"&ap1="+ap1+"&time_to_hour="+time_to_hour+"&time_to_mins="+time_to_mins+"&ap2="+ap2;
			//alert(send_uri);
			$.ajax({
				url: send_uri,
				success: function(resp){
					var arr_resp = resp.split("~~~~~");
					load_appt_schedule(arr_resp[0], arr_resp[1], "", "nonono", false); //dt and dayname in response
				}
			});
		}
}

function change_time(chg_to){	
	var sl_sttm = $("#global_context_slsttm").val();
	var sl_doc = $("#global_context_sldoc").val();
	var sl_fac = $("#global_context_slfac").val();
	var sl_stdt = $("#global_context_slstdt").val();
	
	//alert(sl_sttm+" "+sl_doc+" "+sl_fac+" "+sl_stdt);	
	TestOnMenu();
	var win_change_time = window.open("change_time.php?sl_doc="+sl_doc+"&sl_fac="+sl_fac+"&sl_sttm="+sl_sttm+"&sl_stdt="+sl_stdt+'&act='+chg_to, "block_open_time", "toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=400,height=220,left=200,top=120");
	win_change_time.focus();
}

function load_appt_hx(){
	//document.getElementById("frontdesk3").style.display = "none";

	var pat_id = $("#global_ptid").val();
	top.show_loading_image("show");
	$.ajax({
		url: "common/appointment_status.php?pat_id="+pat_id+"&mode=tiny",
		type: "GET",
		success: function(resp){			
			//change title
			$("#frontdesk_mdl .modal-title").html('Appointment History');			
			//add body html
			$("#frontdesk_mdl .modal-body").html(resp);			
			//show button in footer
			$("#frontdesk_mdl .modal-footer #print_app").css("display","inline-block");
			//show modal
			$("#frontdesk_mdl").modal('show');
			top.show_loading_image("hide");
		}
	});
}

function close_patient_appoitment(){
	var pat_id = $("#global_ptid").val();
	var sch_id = $("#global_apptid").val();
	pre_load_front_desk(pat_id, sch_id, false);	
}

function openPrintWindow(){
	var pat_id = $("#global_ptid").val();
	url="print-appointment-history.php?pat_id="+pat_id;
	window.open(url,'printAppt','dependent=yes,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=200,left=30,top=10');
}

function pop_menu(ap_id, ap_fac, ap_doc, ap_sttm, ap_stdt, pt_id, mode, ap_lbty, ap_lbtx, ap_lbcl, ap_iolink_csi, ap_iolink_practice,ap_iolink_ocfi,askforreason){
	//alert(ap_lbty+", "+ap_lbtx+", "+ap_lbcl);
	if(!mode) mode = "";
	if(!askforreason) askforreason = false;
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);
	$("#global_context_apptsttm").val(ap_sttm);
	$("#global_context_apptdoc").val(ap_doc);
	$("#global_context_apptfac").val(ap_fac);
	$("#global_context_apptstdt").val(ap_stdt);

	$("#global_context_slsttm").val(ap_sttm);
	$("#global_context_sldoc").val(ap_doc);
	$("#global_context_slfac").val(ap_fac);
	$("#global_context_slstdt").val(ap_stdt);
	$("#global_context_apptlbty").val(ap_lbty);
	$("#global_context_apptlbtx").val(ap_lbtx);
	$("#global_context_apptlbcl").val(ap_lbcl);
	$("#global_iolink_connection_settings_id").val(ap_iolink_csi);
	$("#global_iolink_ocular_hx_form_id").val(ap_iolink_ocfi);

	$("#iolink_connection_span_id").css("display", "inline-block");
	$("#iolink_re_connection_span_id").css("display", "none");
	if(ap_iolink_csi!=0 && ap_iolink_csi!='') {
		$("#iolink_connection_span_id").css("display", "none");
		$("#iolink_re_connection_span_id").css("display", "inline-block");
		$("#iolink_remove_connection_id").text("Remove from iASC Link - "+ap_iolink_practice);
		$("#iolink_resyncro_connection_id").text("Resynchronise with iASC Link - "+ap_iolink_practice);
	}
	if(window.event.button==2){
		if(mode == "weekly"){
			
		}else{
			if($("#global_ptid").val() == pt_id && $("#global_apptid").val() == ap_id){
                if(askforreason==true){pre_load_front_desk(pt_id, ap_id,'');TestOnMenu();return false};
			}else{
				pre_load_front_desk(pt_id, ap_id,'');
                if(askforreason==true){TestOnMenu();return false};
			}
		}
		evt=window.event || event;
	
		//if(parseInt(jQuery.browser.version) >= 10)
//		{
//			var cony = evt.x;
//			var maxy = evt.y;			
//		}
//		else
//		{
			var rqOffset = 0;
			if(document.getElementById('mn1_1'))
			{
				rqOffset = document.getElementById('mn1_1').getBoundingClientRect().left;	
			}		
			var left_offset = Math.abs(parseInt($("#lyr1").css('left')));
			var cony = (evt.clientX + left_offset) - rqOffset;
			var maxy = (evt.clientY + $("#mn1_1").scrollTop()) - 110;		
		//}
					
		TestOnMenu();
		ToggleContext("ContextMenu", cony, maxy);	
		document.oncontextmenu = blank_function;
		// Stop an event to further propagate.
		evt=window.event;
		target = (evt.currentTarget) ? evt.currentTarget : evt.srcElement;
		
		evt.cancelBubble=true;
		evt.returnValue=false;
	}else{
		TestOnMenu();
	}
}

function set_init_timings(start_time, end_time, acronym, prov_id, fac_id)
{
	init_rs_date = $('#global_year').val()+'-'+ $('#global_month').val()+'-'+$('#global_date').val();
	$('#init_date_rs').attr({'value':init_rs_date});
	$('#init_st_time_rs').attr({'value':start_time});
	$('#init_et_time_rs').attr({'value':end_time});
	$('#init_acronym_rs').attr({'value':acronym});
	$('#init_prov_id').attr({'value':prov_id});
	$('#init_fac_id').attr({'value':fac_id});
}

function TestOnMenu(){		
	if($("#ContextMenu").get(0)){
		$("#ContextMenu").css("display", "none");
	}
	if($("#ContextMenu_blk").get(0)){
		$("#ContextMenu_blk").css("display", "none");
	}
	if($("#ContextMenu_opn").get(0)){
		$("#ContextMenu_opn").css("display", "none");
	}
	if($("#ContextMenu_blk_only").get(0)){
		$("#ContextMenu_blk_only").css("display", "none");
	}
	//if($("#todo_div").get(0)){
		$("#todo_div").modal("hide");
	//}

}

function ToggleContext(menu_name, cony, maxy){
	if(is_remote_server() == true){ if(cony == ""){ cony = 0;} cony = parseInt(cony,10); cony -= 70; }
	//document.getElementById(menu_name).style.width = "130px";		
	document.getElementById(menu_name).style.position = 'absolute';
	document.getElementById(menu_name).style.display = 'block';
	//================Scroll issue in case of Safari and Mac===================//
	if(parseInt(jQuery.browser.version)==534 || (parseInt(jQuery.browser.version)==537) || (parseInt(jQuery.browser.version)==600) || (parseInt(jQuery.browser.version)==601)){
		var scroll_hei=$("#mn1_1").scrollTop();
		if(parseInt(scroll_hei)>0){
			scroll_hei=parseInt(scroll_hei);
		}
		var topheight=parseInt(maxy+scroll_hei);
		var elemObjAvail = $('#sch_left_portion').css('display');
		if(elemObjAvail=='block'){
			cony=parseInt(cony-580);
		}
		maxy=parseInt(topheight-120);
	}
	
	//code to check if pop menu going out of window width
	var posCheck=parseInt(window.innerWidth)-parseInt($("#mn1_1").width());
	posCheck=parseInt(posCheck)+340+parseInt(cony);
	
	if(posCheck>window.innerWidth){
		cony-=(parseInt(posCheck)-parseInt(window.innerWidth))+20;
	}
		
	//=========================================================================//
	document.getElementById(menu_name).style.pixelLeft = cony;
	document.getElementById(menu_name).style.pixelTop = maxy;
	if((window.event.y) > 210){
		document.getElementById(menu_name).style.pixelTop = document.getElementById(menu_name).style.pixelTop - 80;			
	}
	var bro_ver=navigator.userAgent.toLowerCase();
	if(bro_ver.search("chrome")>1){
		$("#"+menu_name).css({"display":"inline-block",top: maxy, left: cony});
	}
	IsOn = true;		
	window.event.returnValue = false;
}

function blank_function(){
	return false;
}
var var_procedure_limit='';
var var_times_from='';
var var_eff_date_add='';
var var_loc='';
var var_ro1='';
var var_sch_tmp_id='';
var var_procedure='';
var var_label_type='';
var var_label_group='';
var var_is_valid_proc='';
var var_targetLabel='';
var var_fac_type_provider='';

function sch_drag_id(times_from, eff_date_add, loc, pro1, sch_tmp_id, procedure, label_type, is_valid_proc, targetLabel, procedure_limit, fac_type_provider, label_group){
	
	var_times_from=times_from;
	var_eff_date_add=eff_date_add;
	var_loc=loc;
	var_pro1=pro1;
	var_sch_tmp_id=sch_tmp_id;
	var_procedure=procedure;
	var_label_type=label_type;
	var_label_group=label_group;
	var_is_valid_proc=is_valid_proc;
	var_targetLabel=targetLabel;
	var_procedure_limit=procedure_limit;
	var_fac_type_provider=fac_type_provider;
	
	if(!procedure) procedure = "";
	if(!label_type) label_type = "";
	if(!is_valid_proc) is_valid_proc = "no";
	var sel_label='';
	var n=0;
	if(targetLabel)
	{
		n=targetLabel.indexOf(";");
	}
	if($('#ENABLE_SCHEDULER_RAIL_CHECK').val()==1)
	{
		
		if(targetLabel && label_type=='Procedure')
		{
			if(procedure=='')
			{
				if(n==-1)
				{
					procedure=targetLabel;
					is_valid_proc='yes';
				}else
				{
					procedure=targetLabel;
				}
			}
		}else if(targetLabel && procedure=='')
		{
			if(proc_lbls_arr)
			{
				//if we do have only on available label
				if(n==-1)
				{
					//if we unable to find matching label then forcefully replace first encountered label
					//amendment made on 4 july 16, on arun request
					procedure=targetLabel;	
				}
				else
				{
				
					var labelArr=targetLabel.split('; ');
					//check is there any matching procedure in selected slot and procedure
					for (var i = 0; i < labelArr.length; i++) {
						 if(labelArr[i])
						 {
							 for (var n = 0; n < proc_lbls_arr.length; n++) 
							 {
								if(proc_lbls_arr[n].toLowerCase()==labelArr[i].toLowerCase())
								{
									sel_label=proc_lbls_arr[n];
									break;
								}
							}
						}if(sel_label)break;
					}
					//if we unable to find matching label then forcefully replace first encountered label
					//amendment made on 4 july 16, on arun request
					if(!sel_label)sel_label=labelArr[0];
					if(!procedure)procedure=sel_label;
				}
			}
		}
		//alert(procedure);return false;
		if(n==-1 || n==0 || label_type!='Procedure')
		{
			$("#global_appttempproc").val(procedure);
		}
	}
	else
	{
		if(procedure=='')
		{
			if(n==-1)
			{
				procedure=targetLabel;
				is_valid_proc='yes';
			}else
			{
				procedure=targetLabel;
			}
		}
		$("#global_appttempproc").val(procedure);	
	}
	var save_type = $("#global_apptact").val();

	$.ajax({
		url: "match_enforced_proc.php?selected_proc="+$("#sel_proc_id").val()+"&landing_proc="+procedure+"&label_type="+label_type+"&label_group="+var_label_group,
		success: function(resp){
			if(resp == "no"){
				top.fAlert("Procedures do not match. This slot is reserved for "+procedure+".");
				return false;
			}			
			else if((resp == "yes" || resp == "schovrtrue" ) && save_type != ""){
				if(resp == "schovrtrue")
				{
					if($('#ENABLE_SCHEDULER_RAIL_CHECK').val()==1)
					{
						//if we have single procedure then move on, otherwise return to choose a label
						if(n==-1 || n==0)
						{
							var askMsg='This appointment replace its target label(s). Do you want to continue ?';
							top.fancyConfirm(askMsg,'','window.top.fmain.replaceLblConfirm(1)','window.top.fmain.replaceLblConfirm(2)');
							/*var cResult= confirm(askMsg);
							if(cResult==false)
							{
								return false;
							}*/
						}
						else
						{
							top.fAlert('No matching procedure found.');
							$(document).unbind("mousemove", move_trail);
							hide_tool_tip();
							return false;	
						}
					}
					else
					{
						var askMsg= 'This appointment replace its target label(s). Do you want to continue ?';
						top.fancyConfirm(askMsg,'','window.top.fmain.replaceLblConfirm(1)','window.top.fmain.replaceLblConfirm(2)');
					}
				}else{
				//call add appt function for default processing
				replaceLblConfirm(1);
				}
			}
		}
	});
}

function replaceLblConfirm(response)
{
	if(response==1)
	{
		var procedure_limit=var_procedure_limit;
		var times_from=var_times_from;
		var eff_date_add=var_eff_date_add;
		var loc=var_loc;
		var pro1=var_pro1;
		var sch_tmp_id=var_sch_tmp_id;
		var procedure=var_procedure;
		var label_type=var_label_type;
		var label_group=var_label_group;
		var is_valid_proc=var_is_valid_proc;
		var targetLabel=var_targetLabel;
		var fac_type_provider=var_fac_type_provider;
		
		var save_type = $("#global_apptact").val();
		
		$("#global_apptstid").val(sch_tmp_id);
		$("#global_apptsttm").val(times_from);
		$("#global_apptdoc").val(pro1);
		$("#global_apptfac").val(loc);
		$("#global_apptstdt").val(eff_date_add);

		//patient id
		var pat_id = $("#global_ptid").val();					//setting patient id
		var ap_id = $("#global_apptid").val();

		//document.detachEvent("onmousemove", move_trail);
		$(document).unbind("mousemove", move_trail);
		hide_tool_tip();
		//alert(is_valid_proc);
		//procedure id

		if($("#sel_proc_id").val() == "" && (procedure == "" || is_valid_proc == "no")){				
			$("#sel_proc_id").focus();
			top.fAlert("Please select Procedure.");
			return false;
		}else{
			var proc_id = $("#sel_proc_id").val();				//setting procedure id
		}
		if(fac_type_provider=="1"){
			if($("#facility_type_provider").val()==""){
				top.fAlert("Please select Surgeon","",$("#facility_type_provider").focus());
				top.show_loading_image("hide");
				return false;
			}
		}else{
			$("#facility_type_provider").val("");
		}
		if(pat_id != "" && pro1 != ""){
			var url_validity = "check_appt_validity.php?st_date=" + eff_date_add + "&pat_id=" + pat_id + "&st_time=" + times_from + "&sl_pro=" + proc_id + "&pro_id=" + pro1 + "&template_id=" + sch_tmp_id + "&fac_id=" + loc + "&querytype=" + save_type +"&procedure_limit="+ procedure_limit;		
			$.ajax({
				url: url_validity,
				success: function(resp){
					arr_resp = resp.split("~~~");
					if(arr_resp[0] == "y"){	
						btn1 = 'Yes';
						btn2 = 'No';

						func1 = 'validate_or_password';
						func2 = 'hideConfirmYesNo';

						if(arr_resp[1] == "y"){
							misc = "ASKPASSWORD";
							title = 'Admin Override Required!';
						}else{
							misc = "DONOTASKPASSWORD";
							title = 'Warning!';
						}
						//alert(title+", "+arr_resp[2]+", "+btn1+", "+btn2+", "+func1+", "+func2+", "+misc);
						scheduler_warning_disp(title, arr_resp[2], btn1, btn2, func1, func2, misc);
					}else{
						if(save_type == "reschedule"){
							//loading reasons
							var lr_url = "load_reasons.php?pt_id="+pat_id+"&ap_id="+ap_id;
							$.ajax({
								url: lr_url,
								success: function(resp){											
									var arr_resp = resp.split("~~~~~");
									var title = "Reschedule Reason";

									var msg ='';
									msg ='<div class="row">';
									msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
									msg +='</div>';

									msg +='<div class="row">';
									msg +='<div class="col-sm-12">';
									msg +='<div class="form-group">';
									msg +='<label for="">Reason:</label>';
									msg +='<select id="reschedule_reason" name="reschedule_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
									msg +='</div>';
									msg +='</div>';
									msg +='</div>';

									var btn1 = 'OK';
									var btn2 = 'Cancel';

									var misc = "DONOTASKPASSWORD";

									var func1 = 'save_reschedule_reason';
									var func2 = 'hideConfirmYesNo';

									scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
								}
							});
						}else{
							drag_drop();
						}
						return true;
					}
				}
			});
		}
	}
	else{
		hideConfirmYesNo();
	}
}

function save_reschedule_reason(){
	var reason = $("#reschedule_reason").val();
	if(reason == ""){
		top.fAlert("Please select a reason to continue.");
		return false;
	}else{
		if(reason == "Other"){
			reason = escape($("#OtherReason").val());
		}
		$("#global_apptactreason").val(reason);
		//alert($("#global_apptactreason").val());
		drag_drop();
		open_todo();			
	}
}

function save_cancellation_reason(mode){
	if(!mode) mode = "";
	var reason = $("#cancellation_reason").val();
	var fa_available_ch = $("#fa_available_rd").is(":checked");
	if(reason == "" && fa_available_ch == false){
		top.fAlert("Please select a reason to continue.");
		return false;
	}else{
		if(reason == "Other"){
			reason = escape($("#OtherReason").val());
		}
		$("#global_apptactreason").val(reason);
		var sel_date = get_selected_date();
		var sel_fac = get_selected_facilities();
		var pat_id = $("#global_context_ptid").val();
		var sch_id = $("#global_context_apptid").val();
		
		if(mode == "weekly"){
			getchange_status_weekly(sch_id, '18', sel_date, sel_fac, pat_id);
		}else{
			var send_status_code_val = '18';
			if(fa_available_ch == true)
			{
				send_status_code_val = '271';
			}
			//do confirm about to cancel same day future appt for patient in canes of cancel only
			if(send_status_code_val=='18')
			{
				$.ajax({
					url: 'get_ap_ids_by_patient.php',
					type: "POST",
					data: 'pt_id='+pat_id+'&sel_date='+sel_date+'&typ=cancel',
					success: function(resp){
						appt_ids_result=$.parseJSON(resp);
						if(appt_ids_result.length > 1)
						{
							var msg="Cancel – All other appointments for the day";
							var trueFun="applyCancelTrue('"+appt_ids_result +"','"+send_status_code_val +"')";
							var falseFun="applyCancelFalse('"+send_status_code_val +"')";
							top.fancyConfirm(msg,"", "window.top.fmain."+trueFun,"window.top.fmain."+falseFun);
						}
						else 
						{
							getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
						}
					}
				});
			}
			else 
			{
				getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
			}
			hideConfirmYesNo();
			open_todo();
		}
		hideConfirmYesNo();
	}
}

function applyCancelTrue(appt_ids_result, send_status_code_val)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	
	getchange_status(appt_ids_result, send_status_code_val, sel_date, sel_fac, pat_id);
	hideConfirmYesNo();
	open_todo();
}

function applyCancelFalse(send_status_code_val)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var sch_id = $("#global_context_apptid").val();
	
	getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
	hideConfirmYesNo();
	open_todo();
}

function save_first_available_reason_todo(mode)
{
	if(!mode) mode = "";
	var keep_orignal = false;
	
	var sch_id = $("#global_context_apptid").val();
	
	if(mode == "weekly"){
		//this function is onhold will work after sorting else condition  ------------- NOTE
		//getchange_status_weekly(sch_id, '18', sel_date, sel_fac, pat_id);
	}else{
		//function to save first available criterian in new table
		saveFirstAvailable(1,sch_id, '', '', '');
		
	}
	
}

function save_first_available_reason(mode)
{
	if(!mode) mode = "";
	var reason = $("#cancellation_reason").val();//getting reason from list box
	var keep_orignal = $("#keep_sa").is(":checked");
	
	if(reason == "Other"){
		reason = escape($("#OtherReason").val());
	}
	$("#global_apptactreason").val(reason);
	var sel_month= $("#month").val()
	var sel_week= $("#week").val()
	var sel_time= $("#time").val()
	
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var sch_id = $("#global_context_apptid").val();
	
	if(mode == "weekly"){
		//this function is onhold will work after sorting else condition  ------------- NOTE
		//getchange_status_weekly(sch_id, '18', sel_date, sel_fac, pat_id);
	}else{
		var send_status_code_val = '271';
		//cal this function only if user have not selected keep present appointment option
		if(keep_orignal==false)
		{
			firstAvail_keepOrg=1;
			getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
			//function to save first available criterian in new table
			saveFirstAvailable(1,sch_id, sel_month, sel_week, sel_time);
			hideConfirmYesNo();
			open_todo();
		}
		else
		{
			firstAvail_keepOrg=0;
			getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
			//function to save first available criterian in new table
			saveFirstAvailable(0,sch_id, sel_month, sel_week, sel_time);
			hideConfirmYesNo();
			open_todo();
		}
	}
	hideConfirmYesNo();
	
}
function show_hide_other(reason){
	if(reason == "Other"){
		display_block_none("OtherReasonContainer", "block");
		document.getElementById("OtherReasonContainer").focus();
	}else{
		display_block_none("OtherReasonContainer", "none");
	}
}

function scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc){
	var arrfunc1 = func1.split("::::");
	func1 = arrfunc1[0];
	var arg1 = arrfunc1[1];
	if(arg1 == "") arg1 = "1";
	$("#msgTitle").html("<div id=\"msgDiv-handle\">"+title+"</div>");
	var text_msg=msg+"<div class=\"clearfix\"></div><div class=\"sc_line\"></div><div id=\"OtherReasonContainer\" style=\"display:none;\"><input type=\"text\" id=\"OtherReason\" name=\"OtherReason\" class=\"form-control\" placeholder=\"Other Reason\"></div>";
	
	if(misc == "ASKPASSWORD"){
		text_msg += "<div class=\"clearfix\"></div><div>Admin Password: </div><div><input type=\"password\" id=\"AdminPass\" name=\"AdminPass\" class=\"form-control\"></div>";
	}
	
	$("#msgBody").html(text_msg);
	
	var btns= "<button type=\"button\" class=\"btn btn-success\" value=\""+btn1+"\"onClick=\"window."+func1+"('"+arg1+"')\">"+btn1+"</button>";
	btns+="<button type=\"button\" class=\"btn btn-danger\" value=\""+btn2+"\"onClick=\"window."+func2+"('-1')\">"+btn2+"</button>";
	
	$("#msgFooter").html(btns);
	//$("#msgDiv_scheduler").modal('show');
	show_custom_modal();
}

function show_custom_modal()
{
	$("#msgDiv_scheduler").draggable();
	$("#msgDiv_scheduler").show();
	$("#msgDiv_scheduler_overlay").show();
}

function hide_custom_modal()
{
	$("#msgDiv_scheduler").hide();
	$("#msgDiv_scheduler_overlay").hide();
}
function validate_or_password(mode){
	if(window.opener == "undefined")
	{
		top.show_loading_image("show");	
	}	
	//patient id
	var pat_id = $("#global_ptid").val();					//setting patient id
	var ap_id = $("#global_apptid").val();

	if(dgi("AdminPass")){
		if(dgi("AdminPass").value == ""){
			top.fAlert("Please enter password.");
			return false;
		}else{
			var hashMehtod=dgi("hash_method").value;
			if(hashMehtod=="MD5"){
				dgi("AdminPass").value=md5(dgi("AdminPass").value);
			}else{
				dgi("AdminPass").value=Sha256.hash(dgi("AdminPass").value);
			}
			url_pass_check = "check_or_password.php?AdminPass=" + dgi("AdminPass").value;
			$.ajax({ 
				url: url_pass_check,
				success: function(resp){
					//alert(resp);
					if(resp == "grant_access"){
						drag_drop();
						return true;
					}else if(resp == "revoke_access"){
						dgi("AdminPass").value = "";
						top.fAlert("Incorrect Password.");
					}
				}
			});
		}
	}else{
		var save_type = $("#global_apptact").val();
		if(save_type == "reschedule"){
			//loading reasons
			lr_url = "load_reasons.php?pt_id="+pat_id+"&ap_id="+ap_id;
			$.ajax({
				url: lr_url,
				success: function(resp){				
					var arr_resp = resp.split("~~~~~");
					var title = "Reschedule Reason";
					
					var msg ='';
					msg ='<div class="row">';
					msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
					msg +='</div>';
					
					msg +='<div class="row">';
					msg +='<div class="col-sm-12">';
					msg +='<div class="form-group">';
					msg +='<label for="">Reason:</label>';
					msg +='<select id="reschedule_reason" name="reschedule_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
					msg +='</div>';
					msg +='</div>';
					msg +='</div>';
					
					
					var btn1 = 'OK';
					var btn2 = 'Cancel';

					var misc = "DONOTASKPASSWORD";
					
					var func2 = 'hideConfirmYesNo';

					if(mode=='week') {	
						var func1 = 'save_reschedule_reason_weekly';
						scheduler_warning_disp_weekly(title, msg, btn1, btn2, func1, func2, misc);
					}else {
						var func1 = 'save_reschedule_reason';
						scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
					}
				}
			});
		}else{
			if(mode=='week') {
				addApptWeek();
			}else {
				drag_drop();
			}
		}
		return true;
	}
}

function hideConfirmYesNo(){
	
	$("#msgTitle").html('');
	$("#msgBody").html('');
	$("#msgFooter").html('');
	//$("#msgDiv_scheduler").hide();
	hide_custom_modal();
	$(document).unbind("onmousemove", move_trail);
	hide_tool_tip();
	$("#global_apptact").val('');
	top.show_loading_image("hide");	
}

function drag_drop(){
	var pt_id = $("#global_ptid").val();
	var ap_id = $("#global_apptid").val();
	var mode = $("#global_apptact").val();
	var tmp_id = $("#global_apptstid").val();
	var start_time = $("#global_apptsttm").val();
	var doctor_id = $("#global_apptdoc").val();
	var facility_id = $("#global_apptfac").val();
	var start_date = $("#global_apptstdt").val();
	var tempproc = encodeURIComponent($("#global_appttempproc").val());	

	var pt_fname =  $("#global_ptfname").val();
	var pt_lname =  $("#global_ptlname").val();
	var pt_mname =  $("#global_ptmname").val();
	var pt_emr = $("#global_ptemr").val();
	
	var init_date_rs = $('#init_date_rs').val();
	var init_st_time_rs=$('#init_st_time_rs').val();
	var init_et_time_rs=$('#init_et_time_rs').val();
	var init_acronym_rs=$('#init_acronym_rs').val();
	var init_provider_id = $('#init_prov_id').val();
	var init_fac_id = $('#init_fac_id').val();		
	
	var ap_act_reason = ($("#global_apptactreason").length !== 0) ? escape($("#global_apptactreason").val()) : "";
	//alert(ap_act_reason);
	
	if(pt_id != ""){
		//patient specific data
		var pt_doctor_id = ($("#sel_fd_provider").length !== 0) ? $("#sel_fd_provider").val() : "";
		var pt_pcp_phy_id = ($("#pcp_id").length !== 0) ? $("#pcp_id").val() : "";
		var pt_pcp_phy = ($("#pcp_name").length !== 0) ? escape($("#pcp_name").val()) : "";
		var pt_ref_phy_id = ($("#front_primary_care_id").length !== 0) ? $("#front_primary_care_id").val() : "";
		var pt_ref_phy = ($("#front_primary_care_name").length !== 0) ? escape($("#front_primary_care_name").val()) : "";
		var pt_street1 = ($("#frontAddressStreet").length !== 0) ? escape($("#frontAddressStreet").val()) : "";
		var pt_street2 = ($("#frontAddressStreet2").length !== 0) ? escape($("#frontAddressStreet2").val()) : "";
		var pt_city = ($("#frontAddressCity").length !== 0) ? escape($("#frontAddressCity").val()) : "";
		var pt_state = ($("#frontAddressState").length !== 0) ? escape($("#frontAddressState").val()) : "";
		var pt_zip = ($("#frontAddressZip").length !== 0) ? escape($("#frontAddressZip").val()) : "";
		var pt_zip_ext = ($("#frontAddressZip_ext").length !== 0) ? escape($("#frontAddressZip_ext").val()) : "";
		var pt_email = ($("#email").length !== 0) ? escape($("#email").val()) : "";
		var pt_photo_ref = ($("#photo_ref").is(":checked")==true) ? 1 : 0;
		var pt_home_ph = ($("#phone_home").length !== 0) ? escape($("#phone_home").val()) : "";
		var pt_work_ph = ($("#phone_biz").length !== 0) ? escape($("#phone_biz").val()) : "";
		var pt_cell_ph = ($("#phone_cell").length !== 0) ? escape($("#phone_cell").val()) : "";

		var pt_status = ($("#elem_patientStatus").length !== 0) ? $("#elem_patientStatus").val() : "";
		var pt_other_status = ($("#otherPatientStatus").length !== 0) ? escape($("#otherPatientStatus").val()) : "";
		var pt_dod_patient = ($("#dod_patient").length !== 0) ? escape($("#dod_patient").val()) : "";

		//appointment specific data
		var ap_routine_exam = ($("#chkRoutineExam").length !== 0) ? $("#chkRoutineExam").val() : "";
		var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
		var ap_notes = ($("#txt_comments").length !== 0) ? encodeURIComponent($("#txt_comments").val()) : "";
		var ap_pickup_time = ($("#pick_up_time").length !== 0) ? escape($("#pick_up_time").val()) : "";
		var ap_arrival_time = ($("#arrival_time").length !== 0) ? escape($("#arrival_time").val()) : "";
		
		var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";
		var sec_ap_procedure = ($("#sec_sel_proc_id").length !== 0) ? $("#sec_sel_proc_id").val() : "";
		var ter_ap_procedure = ($("#ter_sel_proc_id").length !== 0) ? $("#ter_sel_proc_id").val() : "";	
		
		var pri_eye_site = ($("#pri_eye_site").length !== 0) ? $("#pri_eye_site").val() : "";
		var sec_eye_site = ($("#sec_eye_site").length !== 0) ? $("#sec_eye_site").val() : "";
		var ter_eye_site = ($("#ter_eye_site").length !== 0) ? $("#ter_eye_site").val() : "";
		
		var facility_type_provider = ($("#facility_type_provider").length !== 0) ? $("#facility_type_provider").val() : "";

		var referral = ($("input[type=checkbox]#sa_ref_management").is(":checked") ? 1 : 0);
		var ref_management = "&pt_referral="+referral;
        
        var sa_verification = ($("input[type=checkbox]#sa_verification_req").is(":checked") ? 1 : 0);
        var verification_req = "&pt_verification="+sa_verification;
		
			var send_uri = "save_changes.php?save_type="+mode+"&pt_id="+pt_id+"&ap_id="+ap_id+"&pt_doctor_id="+pt_doctor_id+"&pt_pcp_phy_id="+pt_pcp_phy_id+"&pt_pcp_phy="+pt_pcp_phy+"&pt_ref_phy_id="+pt_ref_phy_id+"&pt_ref_phy="+pt_ref_phy+"&pt_street1="+pt_street1+"&pt_street2="+pt_street2+"&pt_city="+pt_city+"&pt_state="+pt_state+"&pt_zip="+pt_zip+"&pt_zip_ext="+pt_zip_ext+"&pt_email="+pt_email+"&pt_photo_ref="+pt_photo_ref+"&pt_home_ph="+pt_home_ph+"&pt_work_ph="+pt_work_ph+"&pt_cell_ph="+pt_cell_ph+"&pt_status="+pt_status+"&pt_other_status="+pt_other_status+"&pt_dod_patient="+pt_dod_patient+"&ap_routine_exam="+ap_routine_exam+"&ap_ins_case_id="+ap_ins_case_id+"&ap_notes="+ap_notes+"&pri_eye_site="+pri_eye_site+"&sec_eye_site="+sec_eye_site+"&ter_eye_site="+ter_eye_site+"&ap_pickup_time="+ap_pickup_time+"&ap_arrival_time="+ap_arrival_time+"&ap_procedure="+ap_procedure+"&start_date="+start_date+"&start_time="+start_time+"&doctor_id="+doctor_id+"&facility_id="+facility_id+"&tmp_id="+tmp_id+"&pt_fname="+pt_fname+"&pt_mname="+pt_mname+"&pt_lname="+pt_lname+"&pt_emr="+pt_emr+"&ap_act_reason="+ap_act_reason+"&tempproc="+tempproc+'&init_st_time_rs='+init_st_time_rs+'&init_et_time_rs='+init_et_time_rs+'&init_acronym_rs='+init_acronym_rs+'&init_provider_id='+init_provider_id+'&init_fac_id='+init_fac_id+'&sec_ap_procedure='+sec_ap_procedure+'&ter_ap_procedure='+ter_ap_procedure+'&init_date_rs='+init_date_rs+"&facility_type_provider="+facility_type_provider+ref_management+verification_req;
		$.ajax({
			url: send_uri,
			type: "POST",
			success: function(resp){
				//alert(resp);
				//return false;
				if(is_remote_server() == true)
				{
					resp = json_resp_handle(resp);				
				}
				var arr_resp = resp.split("~");
				if(arr_resp[0] == "save"){
					pre_load_front_desk(pt_id, ap_id, false);
				}
				if(arr_resp[0] == "addnew" || arr_resp[0] == "reschedule"){
					var arr_start_date = start_date.split("-");
					var new_start_date = arr_start_date[2]+"-"+arr_start_date[0]+"-"+arr_start_date[1];					
					load_calendar(new_start_date, arr_resp[1], '', false);					
				}
				$("#global_apptact").val("");
				$("#global_apptactreason").val("");
				$("#global_apptstid").val("");
				$("#global_apptsttm").val("");
				$("#global_apptdoc").val("");
				$("#global_apptfac").val("");
				$("#global_apptstdt").val("");
				$("#global_appttempproc").val("");
				$('#init_fac_id').val("");
				$('#init_prov_id').val("");
				hideConfirmYesNo();
			}
		});
	}
}

function popUpMe(intId, patId, server_data){
	//window.open("common/appt_hx_popup.php?schId="+intId+"&patId="+patId,"AppointmentHxDetails","width=700,height=400,top=175,left=125,resizable=yes");			
	var url = WEB_ROOT+"/interface/scheduler/common/appt_hx_popup.php?schId="+intId+"&patId="+patId;
	$('#div_app_hx').modal('show');
	top.master_ajax_tunnel(url,popUpMe_callBack);
}

function popUpMe_callBack(reponse,etc)
{
	$('#div_app_hx_detail').html(reponse);
}
//cal write



function printContactRx(method, workSheetId, opnr_status){
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	if(opnr_status == 1)
	{
		winPrintMr = window.open('../../chart_notes/print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
	else
	{
		winPrintMr = window.open('../chart_notes/print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
}

function printMr(value, opnr_status){
	var givenMrValue = value;
	var pr = "";
	pr = (pr == "") ? "0" : "1";
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	if(opnr_status == 1)
	{
		winPrintMr = window.open('../../chart_notes/requestHandler.php?printType=1&elem_formAction=print_mr&givenMr='+givenMrValue,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
	else
	{
		winPrintMr = window.open('../chart_notes/requestHandler.php?printType=1&elem_formAction=print_mr&givenMr='+givenMrValue,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
}

function get_copay(pat_id, casid, appt_id, appt_date){
	if(!casid) casid = "";
	if(!appt_id) appt_id = "";
	if(!appt_id) appt_date = "";
	if(casid != "" && casid != 0){

		send_uri = "common/get_copay_refrral.php?pat_id="+pat_id+"&case_typeid="+casid+"&appt_id="+appt_id+"&appt_date="+appt_date;
		
		$.ajax({
			url: send_uri,
			type: "GET",
			success: function(resp){
				var res = resp.split("~");
				if(res[0] == 1){
					if(res[1] != 0){
						document.getElementById("cpays").innerHTML = res[1];
					}else{
						document.getElementById("cpays").innerHTML = "$0.00";
					}
					if(res[7] == "1" || res[7] == 1){
						if(document.getElementById("RoutineExamVisionCase")){
							document.getElementById("RoutineExamVisionCase").style.display = "block";
						}
					}else{
						if(document.getElementById("RoutineExamVisionCase")){
							document.getElementById("RoutineExamVisionCase").style.display = "none";
						}
					}
				}else{
					document.getElementById("cpays").innerHTML = "$0.00";
				}
				if(res[8]!=""){
					get_accept_assignment(res[8]);
				}
			}
		});
	}else{
		get_accept_assignment(0);
	}
}

function show_test(procid,sch_id){

		if(sch_id=='' || sch_id==0)
		{
			if(procid != ""){
				document.getElementById('txt_comments').disabled=false;
				document.getElementById('txt_comments').value='';
			} else {
				document.getElementById('txt_comments').value= 'Appointment Comment';
				document.getElementById('txt_comments').disabled=true;
			}
		}
		if(procid != ""){
			var provider_id;
			if(document.getElementById("sel_fd_provider")){
					provider_id =document.getElementById("sel_fd_provider").value;
					getProcMessage(provider_id,procid);
			}
		}
}

function getProcMessage(provider_id,proc_id){

	send_uri = "common/getproceduretime.php?pro_id="+provider_id+"&proc_id="+proc_id;
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			var res = resp.split("~");
			var notes= res[1];
			if(notes!='') {
				top.fAlert(notes);
			}
		}
	});
}


function image_DIV(imageSrc, div){	
	if(imageSrc){
		getPatientImage();
	}
}

function getPatientImage(){
	var patId = $("#global_ptid").val();
	$.ajax({ 
		url: "patient_photos.php?pat_id="+patId,
		success: function(resp){
			if(resp != ""){
				if (document.getElementById('patient_photo_container')){
					//var img = $(resp);
					//var height = $(img).css('height');
					//var width = $(img).css('width');
					//$("#patient_photo_container").css({display:'inline-block', height:height, width:width});
					$("#patient_photo_container").html(resp);
				}
				else{
				$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');
				}
			}
		}
	});	
}

function hidePatientImage(){
	if (document.getElementById('patient_photo_container')){
		document.getElementById('patient_photo_container').style.display = 'none';
	}
}


/*
Name:		mk_appt
Purpose:	to Make Appt Based upon the follow up set in latest chart note for this patient
Author:		AA
*/
function mk_appt(pt_id){
	if(!pt_id) pt_id = "";

	if(pt_id != ""){
		//alert("mk_appt.php?pt_id="+pt_id);
		$.ajax({ 
			url: "mk_appt.php?pt_id="+pt_id,
			success: function(resp){
				if(resp != "no_response"){					
					var arr_resp = resp.split("||||");				
					set_date(arr_resp[0]);
					if($("#txt_comments").get(0) && arr_resp[2].trim() != ""){
						$("#txt_comments").val('');
						$("#txt_comments").val(arr_resp[2]);
					}
					if($("#sel_proc_id").get(0)){
						$("#sel_proc_id").val(arr_resp[3]);
					}
					load_calendar(arr_resp[0], arr_resp[1], 'nonono', false);
					load_appt_schedule(arr_resp[0], arr_resp[1], '', 'nonono', false);
				}else{
					top.fAlert("No Follow Up added for the patient.");
				}
			}
		});
	}else{
		top.fAlert("Please select patient.");
		return false;
	}
}

/*PT DEMOGRAPHICS ALERTS*/
function patient_note_alert(title,msg,btn1,btn2,func,showCancel,showImage,misc)
{
	//
	
	  text = '<div id="divCon_pt_alert" style="position:relative; z-index:1000; left:450px;">';
			 
	  text += '<table align="center" width="400px" border=0 cellpadding=2 cellspacing=0 class="confirmTable3" style="position:absolute;top:0px;left:0px;z-index:10;">';
	  text += '<tr><td height="25" class="text_b_w" colspan="2" >';		  		  
	  text += title;
	  text += '</td></tr>';
	
	  text += '<tr class="confirmBackground"><Td colspan="2" class="text_10b">';
	  
	  if((typeof showImage == "undefined") || (showImage != 0))
	  {
		//text += '<img src="../../library/images/stop.gif" alt="stop">';
	  }
	   
	  text += '</td>';
	  text += '</tr>';
	  text += '<tr  class="confirmBackground">';
	 
	  text += '<td colspan="2" valign="middle" class="text_10b" align="center">';
	  text += msg;
	 
	  text += '</td></tr>';
	  text += '<tr  height="25"  class="confirmBackground"><td class="confirmBackground"  colspan="2" ><center>';
	  text += '<input type="button" value="'+btn1+'" onClick="window.'+func+'(1)" class=\"dff_button\" id="okbut1" onMouseOver="button_over(\'okbut1\')" onMouseOut="button_over(\'okbut1\', \'\')">';
	 
	  if((typeof showCancel == "undefined") || (showCancel != 0))
	  {
		text += '<input type="button" value="Cancel" onClick="window.'+func+'(-1)" class=\"dff_button\" id="okbut2" onMouseOver="button_over(\'okbut2\')" onMouseOut="button_over(\'okbut2\', \'\')">';
	  }
	  text += '</center></td></tr></table>';		 
	  text += '</div>';		  
	  
	  if (document.getElementById('msgDiv2')) 
	  {
		 mDiv = document.getElementById('msgDiv2');
		 mDiv.innerHTML = text;
		 mDiv.style.visibility = 'visible';
	  }	
	  
}// end of function
function alertReasonsShow_pt_alert(strVal){
	alertReasonsHide_pt_alert();
	var objDiv=document.getElementById("msgDiv2");
	if(objDiv && strVal==1){		
		objDiv.style.display="block";
	}
}
function alertReasonsHide_pt_alert(){

	var objDiv=document.getElementById("msgDiv2");
	if(objDiv){		
		objDiv.style.display="none";
	}
}
function show_pop_up_pt_alert(msg){
	var title = "Patient Alerts";
	var showCancel = 0;
	var showImage = 1;
	var btn1 = "OK";
	var btn2 = "Cancel";
	var func = "alertReasonsHide_pt_alert";	
	var oPDiv = patient_note_alert(title,msg,btn1,btn2,func,showCancel,showImage,0);
	if(oPDiv){
		oPDiv.style.left = "350px";
	}
}

function alert_box(title, content, w, h, l, t, divName, showClose, showMask){	
	if(top.dgi(divName)){
		if(typeof(w) == "undefined"){ w = "300px"; }else{ w = parseInt(w) + "px"; }
		if(typeof(h) == "undefined"){ h = "auto"; }else{ h = parseInt(h) + "px"; }
		if(typeof(l) == "undefined"){ l = "100px"; }else{ l = parseInt(l) + "px";}
		if(typeof(t) == "undefined"){ t = "100px"; }else{ t = parseInt(t) + "px";}
		if(typeof(showClose) == "undefined"){ showClose = true; }	
		if(showClose){title = '<span class="closeBtn" onClick="close_alert_box('+divName+');"></span>'+title;}
		
		var text = "";
		text += '<div class="section" style="cursor:pointer;width:'+w+';">';
			text += '<div id="'+divName+'-handle" class="section_header">'+title+'</div>';
			text += '<div style="margin-top:0px;text-align:left;height:'+h+';overflow:auto;overflow-x:hidden;background-color:#FFFFFF;" class="mt10 padd10">'+content+'</div>';
		text += '</div>';
		top.dgi(divName).innerHTML = text;
		top.dgi(divName).style.width = w;
		//top.dgi(divName).style.height = h;
		top.dgi(divName).style.left = l;
		top.dgi(divName).style.top = t;
		top.dgi(divName).style.display = "block";
	}
}

function close_alert_box(divName){
	top.dgi(divName).innerHTML = "";
	top.pop_up_handler(divName);
	top.dgi(divName).style.display = "none";
}

function show_provider_notes(prov_id, load_dt){
	$.ajax({ 
		url: "provider_notes.php?prov_id="+prov_id+"&load_dt="+load_dt,
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			$("#provider_notes_div_header").html(arr_resp[0]);
			$("#provider_notes_div_content").html(arr_resp[1]);
			$("#provider_notes_div_footer").html(arr_resp[2]);
			$("#provider_notes_div").modal("show");
		}
	});
}
function reload_provider_notes(prov_id, load_dt){
	
	$("#provider_notes_div_content").html("Loading...");
	$("#provider_notes_div_footer").html("");
	$.ajax({ 
		url: "provider_notes.php?prov_id="+prov_id+"&load_dt="+load_dt,
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			$("#provider_notes_div_header").html(arr_resp[0]);
			$("#provider_notes_div_content").html(arr_resp[1]);
			$("#provider_notes_div_footer").html(arr_resp[2]);
		}
	});
}

function hide_provider_notes(){
	$("#provider_notes_div_header").html("");
	$("#provider_notes_div_content").html("");
	$("#provider_notes_div_footer").html("");
	$("#provider_notes_div").modal("hide");
}

function save_provider_notes(load_dt){
	if(!load_dt) load_dt = "";
	var act_id = $("#new_prov_note_act").val();
	var prov_id = $("#new_prov_note_id").val();
	var notes = $("#new_prov_note").val();
	if($.trim(notes))
	{
		if(load_dt == ""){
			var note_date = get_selected_date();
		}else{
			var note_date = load_dt;
		}
		$.ajax({ 
			url: "save_provider_notes.php?prov_id="+prov_id+"&note_date="+note_date+"&act_id="+act_id+"&notes="+encodeURIComponent(notes),
			success: function(resp){
				//hide_provider_notes();
				//show_provider_notes(prov_id, note_date);
				reload_provider_notes(prov_id, note_date);
				var notes_cnt_name = "sticky_"+prov_id+"_"+load_dt;
				$("#"+notes_cnt_name).html($.trim(resp));
			}
		});
	}
}

function edit_provider_notes(note_id){
	var note_div_name = "existing_notes"+note_id;
	var notes = $("#"+note_div_name).html();
	$("#new_prov_note_act").val(note_id);
	$("#new_prov_note").val($.trim(notes));
}

function new_provider_notes(){
	$("#new_prov_note_act").val("0");
	$("#new_prov_note").val("");
}

function delete_provider_notes(note_id, load_dt){
	if(!load_dt) load_dt = "";
	var prov_id = $("#new_prov_note_id").val();
	if(load_dt == ""){
		var note_date = get_selected_date();
	}else{
		var note_date = load_dt;
	}
	$.ajax({ 
		url: "delete_provider_notes.php?prov_id="+prov_id+"&note_date="+note_date+"&act_id="+note_id,
		success: function(resp){
			//hide_provider_notes();
			//show_provider_notes(prov_id, note_date);
			reload_provider_notes(prov_id, note_date);
			var notes_cnt_name = "sticky_"+prov_id+"_"+load_dt;
			$("#"+notes_cnt_name).html(resp);
		}
	});
}

function changeTimings(ids, load_dt)
{
	$.ajax({ 
		url: "load_block_timings.php?ids="+ids+"&load_dt="+load_dt,
		success: function(resp){
			$("#timeContainer").html(resp);
		}
	});
}

function blk_lk_options(mode, act_type){
	if(!mode) mode = "";
	TestOnMenu();
	if(mode == "get"){
		var ap_sttm = $("#global_context_slsttm").val();
		var ap_doc = $("#global_context_sldoc").val();
		var ap_fac = $("#global_context_slfac").val();
	}
	var load_dt = get_selected_date();
	$.ajax({ 
		url: "load_block_options.php?load_dt="+load_dt+"&mode="+mode+"&ap_sttm="+ap_sttm+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac+"&act_type="+act_type+"&sid="+Math.random(),
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			
			$("#block_lock_div").modal("show");
			$("#blk_lk_content").html(arr_resp[1]);

			if(act_type == "block"){
				swap_block_unblock('block');
			}
			if(act_type == "unblock"){
				swap_block_unblock('none');
			}
			
			$("#blk_lk_date").html(arr_resp[0]);
			$("#block_lock_div_footer").html(arr_resp[2]);
			
			//refresh select picker
			$("#blk_lk_loca").selectpicker("refresh");
			$("#blk_lk_prov").selectpicker("refresh");

			/*var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select All";
			$("#blk_lk_prov").multiSelect(dd_pro, function(){ changeTimings(selectedValuesStr("blk_lk_prov"), load_dt)});
			
			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select All";
			$("#blk_lk_loca").multiSelect(dd_fac);*/			
		}
	});
}

//add / edit labels - starts here
//ajax div swap by amit - starts here
var selectedList;
var availableList;
function populateProcLabel(){
	var strReturn = document.getElementById("tempSelectedCache").value;   
	var arrReturn = strReturn.split("~:~");
	var strLen = arrReturn.length;
	strReturn = "";
	for(i = 0; i < strLen-1; i++){
		var arrTemp = arrReturn[i].split("~~~");
		strReturn += arrTemp[1]+"; ";
	} 
	var strLength = parseInt(strReturn.length)-2;
	strReturn = strReturn.substring(0,strLength);
	//document.frm_proc_time.template_label.value=strReturn;
	document.getElementById('proc_acro').value=strReturn;
	//document.frm_proc_time.chkLunch.checked=false; 
	//document.frm_proc_time.chkReserved.checked=false;
}
function createListObjects(){
	availableList = document.getElementById("availableOptions");
	selectedList = document.getElementById("selectedOptions");
}
	 
function setSize(list1,list2){
	list1.size = getSize(list1);
	list2.size = getSize(list2);
}

function selectNone(list1,list2){
	list1.selectedIndex = -1;
	list2.selectedIndex = -1;
	addIndex = -1;
	selIndex = -1;
}

function getSize(list){
	var len = list.childNodes.length;
	var nsLen = 0;
	for(i=0; i<len; i++){
		if(list.childNodes.item(i).nodeType==1)
		nsLen++;
	}
	if(nsLen<2)
	return 2;
	else
	return nsLen;
}     

function refreshProcList(strSelectType,strMode){

	var dir="../admin/scheduler_admin/schedule_template/";
	if(strSelectType == "custom"){
		var strAttribs = document.getElementById("tempSelectedCache").value;
		var url_dt = dir+"proc_list.php?strSelectType="+strSelectType+"&strAttribs="+strAttribs;                                
	}else{
		var url_dt = dir+"proc_list.php?strSelectType="+strSelectType;
	}   
	$("#loading_img").css("display","block");
	$.ajax({ 
		url: url_dt,
		success: function(resp){
			var arrResponse = resp.split("[{(^)}]");
			
			if(strSelectType == "available"){
				$("#divAvailableOptions").html(arrResponse[0]);
				$("#divSelectedOptions").html(arrResponse[1]);
				
			}else if(strSelectType == "custom"){
				$("#divAvailableOptions").html(arrResponse[0]);
				$("#divSelectedOptions").html(arrResponse[1]);
				
				if(arrResponse[2] == 1){
					 document.getElementById('addall').disabled = false;
					 document.getElementById('addsel').disabled = false;
				}else{
					 document.getElementById('addall').disabled = true;
					 document.getElementById('addsel').disabled = true;
				}
				
				if(arrResponse[3] == 1){    
					 document.getElementById('remall').disabled = false;
					 document.getElementById('remsel').disabled = false;
				}else{
					 document.getElementById('remall').disabled = true;
					 document.getElementById('remsel').disabled = true;
				}
	
			}else{
				$("#divAvailableOptions").html(arrResponse[1]);
				$("#divSelectedOptions").html(arrResponse[0]);
				
				var selectedList = document.getElementById("selectedOptions");
				//alert(selectedList.length);
				var strReturn = "";
				for(i = 0; i < selectedList.length; i++){               
					strReturn += selectedList.options.item(i).value+",";
				}
				
				
				document.getElementById("tempSelectedCache").value=strReturn;
			}    
			populateProcLabel();
			if(strMode == "lunch"){
				document.getElementById("proc_acro").value='lunch'; document.getElementById("chkLunch").checked=true; document.getElementById("chkReserved").checked=false;
				//template_label
			}
			
			if(strMode == "Reserved"){
				document.getElementById("proc_acro").value='Reserved'; document.getElementById("chkLunch").checked=false; document.getElementById("chkReserved").checked=true;
				//template_label
			}            
			document.getElementById("loading_img").style.display = "none";
					
		}
	});
	
}

function delAll(strMode){
	document.getElementById("tempSelectedCache").value="";
	refreshProcList("available", strMode);
	selectedList.options.length = 0;
	selectNone(selectedList,availableList);
	setSize(selectedList,availableList);
	document.getElementById('addall').disabled = false;
	document.getElementById('addsel').disabled = false;
	document.getElementById('remall').disabled = true;
	document.getElementById('remsel').disabled = true;
}

function addAll(){
	document.getElementById("tempSelectedCache").value="";
	refreshProcList("selectedOptions");
	availableList.options.length = 0; 
	selectNone(selectedList,availableList);
	setSize(selectedList,availableList);
	document.getElementById('addall').disabled = true;
	document.getElementById('addsel').disabled = true;
	document.getElementById('remall').disabled = false;
	document.getElementById('remsel').disabled = false;
}


function delAttribute(){
	var strToSendAttrib = ""; 
	var selectedList = document.getElementById("selectedOptions");
	var selIndex = selectedList.selectedIndex;
	if(selIndex < 0){
		top.fAlert("Please select some procedure(s) to continue.");
		return;
	}
	var arrRefinedSelection = new Array();
	var j = 0;
	var existingValue = document.getElementById("tempSelectedCache").value;
	var arrExistingValue = existingValue.split(",");
	
	for(i = 0; i < selectedList.length; i++){
		blRemove = "";
		if(selectedList.options.item(i).selected == true){             
			var blRemove = selectedList.options.item(i).value;
			for(z = 0; z < arrExistingValue.length-1; z++){
				if(arrExistingValue[z] == selectedList.options.item(i).value){
					arrExistingValue[z] = "";            
				}                    
			}                
		}
	}
	
	for(i = 0; i < arrExistingValue.length ; i++){
		if(arrExistingValue[i] != "undefined" && arrExistingValue[i] != "")
			strToSendAttrib += arrExistingValue[i]+"~:~";
	}
	
	if(strToSendAttrib == ""){
		delAll();
	}else{
		document.getElementById("tempSelectedCache").value = strToSendAttrib;
		refreshProcList("custom");
	}
}

function addAttribute(){
	var strToSendAttrib = "";
	var availableList = document.getElementById("availableOptions");
	var addIndex = availableList.selectedIndex;
	if(addIndex < 0){
		top.fAlert("Please select some procedure(s) to continue.");
		return;
	}

	for(i = availableList.length-1; i >= 0 ; i--){
		if(availableList.options.item(i).selected == true){
			strToSendAttrib += availableList.options.item(i).value+",";
		}
	}
	
	document.getElementById("tempSelectedCache").value += strToSendAttrib;
	//refreshProcList("custom");
}

function set_reset_options(mode){
	/*if(mode == "Lunch"){
		document.getElementById("template_label").value = "Lunch";
		document.getElementById("select_acro").style.display = "none";
		document.getElementById("input_acro").style.display = "block";
	}else if(mode == "Reserved"){
		document.getElementById("template_label").value = "Reserved";
		document.getElementById("select_acro").style.display = "none";
		document.getElementById("input_acro").style.display = "block";
	}else if(mode == "Information"){
		document.getElementById("template_label").value = "";
		document.getElementById("select_acro").style.display = "none";
		document.getElementById("input_acro").style.display = "block";
	}else if(mode == "Procedure"){
		document.getElementById("template_label").value = "";
		document.getElementById("select_acro").style.display = "block";
		document.getElementById("input_acro").style.display = "none";
	}*/
	
	$("#proc_acro").prop("readonly", false);
	if(mode == "Lunch"){
			document.getElementById("proc_acro").value = "Lunch";
			document.getElementById("show_proc_options").style.display = "none";
			$("#proc_acro").prop("readonly", true);
		}else if(mode == "Reserved"){
			document.getElementById("proc_acro").value = "Reserved";
			document.getElementById("show_proc_options").style.display = "none";
		}else if(mode == "Information"){
			document.getElementById("proc_acro").value = "";
			document.getElementById("show_proc_options").style.display = "block";
		}else if(mode == "Procedure"){
			document.getElementById("proc_acro").value = "";
			document.getElementById("show_proc_options").style.display = "block";
		}
}

function load_label_options(){
	TestOnMenu();
	var ap_sttm = $("#global_context_slsttm").val();
	var ap_doc = $("#global_context_sldoc").val();
	var ap_fac = $("#global_context_slfac").val();
	var ap_lbty = $("#global_context_apptlbty").val();
	var ap_lbtx = $("#global_context_apptlbtx").val();
	var ap_lbcl = $("#global_context_apptlbcl").val();
	var ap_tmp_id = $("#global_context_appt_tmp_id").val();
	var load_dt = get_selected_date();
	var send_uri = "load_label_options.php?load_dt="+load_dt+"&ap_sttm="+ap_sttm+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac+"&ap_lbty="+escape(ap_lbty)+"&ap_lbtx="+escape(ap_lbtx)+"&ap_lbcl="+escape(ap_lbcl)+"&ap_tmp_id="+escape(ap_tmp_id);
	//alert(send_uri);
	$.ajax({ 
		url: send_uri,
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			
			$("#label_opt_div").modal("show");
			$("#label_opt_date").html(arr_resp[0]);
			$("#label_opt_content").html(arr_resp[1]);
			$("#label_opt_footer").html(arr_resp[2]);	
			/*var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select All";
			$("#label_opt_prov").multiSelect(dd_pro, function(){ changeTimings(selectedValuesStr("label_opt_prov"), load_dt)});
			
			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select All";
			$("#label_opt_loca").multiSelect(dd_fac);	
			
			$("#proc_acro").multiSelect({noneSelected:'Select All'})*/;
			//refresh multiselect dropdown options
			//$("#label_opt_loca").selectpicker("refresh");
			//$("#label_opt_prov").selectpicker("refresh");
			$("#availableOptions").selectpicker("refresh");
			
		/*	var colorPickObj = $('.bfh-colorpicker');
			if(colorPickObj.data('bfhcolorpicker')){
				console.log(colorPickObj, 'Initialized');
				colorPickObj.setValue = 'transparent';
			}else{
				var newPicker = colorPickObj.colorpicker();
				newPicker.setValue = 'transparent';
				console.log(newPicker, 'New Initialized');
			}*/
			load_color_picker("#FFF");
			
		}
	});
}

function remove_labels_by_slot()
{
	TestOnMenu();
	var ap_sttm = $("#global_context_slsttm").val();
	var ap_doc = $("#global_context_sldoc").val();
	var ap_fac = $("#global_context_slfac").val();
	var ap_lbty = encodeURIComponent($("#global_context_apptlbty").val());
	var ap_lbtx = encodeURIComponent($("#global_context_apptlbtx").val());
	var ap_lbcl = encodeURIComponent($("#global_context_apptlbcl").val());
	var ap_tmp_id = encodeURIComponent($("#global_context_appt_tmp_id").val());
	var replace_lbl = encodeURIComponent($("#global_replace_lbl").val());
	var load_dt = get_selected_date();

	var rm_lbl_url = "remove_labels_by_slot.php?ap_sttm="+ap_sttm+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac+"&ap_lbty="+ap_lbty+"&ap_lbtx="+ap_lbtx+"&ap_lbcl="+ap_lbcl+"&load_dt="+load_dt+"&replace_lbl="+replace_lbl+"&ap_tmp_id="+escape(ap_tmp_id);	
	$.ajax({
			url:rm_lbl_url,
			success : function(resp)
			{
				if(resp != "notdone")
				{
					load_appt_schedule(load_dt, resp, '', "nonono");
				}
			}
		});
}

function save_label_options(mode){
	if(!mode) mode = "";

	var err = "";
	if($("#label_time_to_hour").val() == "" || $("#label_time_to_mins").val() == "" || $("#label_ap2").val() == ""){
		err += " - End Time\n";
	}
	if(err != ""){
		err = "Please provide input for the following:\n\n" + err;
		top.fAlert(err);
		return false;
	}else{
		
		//show loading image
		top.show_loading_image("show");

		var load_dt = get_selected_date();
		
		var prov = $("#label_opt_prov").val();
		var loca = $("#label_opt_loca").val();

		var time_from_hour = $("#label_time_from_hour").val();
		var time_from_mins = $("#label_time_from_mins").val();
		var ap1 = $("#label_ap1").val();

		var time_to_hour = $("#label_time_to_hour").val();
		var time_to_mins = $("#label_time_to_mins").val();
		var ap2 = $("#label_ap2").val();

		var label_type = $("#label_type").val();
		var label_text = $("#template_label").val();
		var proc_acro = $("#proc_acro").val();
		var label_color = $("#label_color").val();
		var ap_tmp_id = $("#global_context_appt_tmp_id").val();
			
		var send_uri = "save_label_options.php?proc_acro="+proc_acro+"&load_dt="+load_dt+"&prov="+prov+"&loca="+loca+"&time_from_hour="+time_from_hour+"&time_from_mins="+time_from_mins+"&ap1="+ap1+"&time_to_hour="+time_to_hour+"&time_to_mins="+time_to_mins+"&ap2="+ap2+"&label_type="+escape(label_type)+"&label_text="+escape(label_text)+"&label_color="+escape(label_color)+"&mode="+mode+"&ap_tmp_id="+ap_tmp_id;
		$.ajax({
			url: send_uri,
			success: function(resp){
				//alert(resp);
				var arr_resp = resp.split("~~~~~");
				//document.write(resp);
				//return false;
				$("#label_opt_div").modal('hide');

				//show loading image
				top.show_loading_image("hide");
				load_appt_schedule(arr_resp[0], arr_resp[1], "", "nonono", false); //dt and dayname in response
			}
		});
	}
}
//add/edit labels - ends here

function swap_block_unblock(action,elemValue){
 if(elemValue =='locked')
 {
	display_block_none('comments_section', action);
	display_block_none('block_warning', 'none');
	$('#blk_lk_comment').val('Locked');
 }else {
	display_block_none('comments_section', action);
	display_block_none('block_warning', action);
	$('#blk_lk_comment').val('Blocked');	
 }
}

function chgBlockTime(ids)
{
	top.fAlert(ids);
}
	


function save_blk_lk(){
	
	var err = "";
	if($("#block_time_to_hour").val() == "" || $("#block_time_to_mins").val() == "" || $("#block_ap2").val() == ""){
		err += " - End Time<br />";
	}
	if(err != ""){
		err = "Please provide input for the following:<br /><br />" + err;
		top.fAlert(err);
		return false;
	}else{

		//show loading image
		top.show_loading_image("show");

		var load_dt = get_selected_date();

		var block_mode = "";
		var blk_lk_act = document.getElementsByName("blk_lk_act");
		for(act = 0; act < blk_lk_act.length; act++){
			if(blk_lk_act[act].checked == true){
				if(blk_lk_act[act].id == "blk_lk_act_block"){
					block_mode = "block"
				}
				if(blk_lk_act[act].id == "blk_lk_act_unblock"){
					block_mode = "open"
				}
				if(blk_lk_act[act].id == "lk_act_block"){
					block_mode = "lock"
				}
				if(blk_lk_act[act].id == "lk_act_unblock"){
					block_mode = "unlock"
				}
			}
		}
		if(block_mode != ""){
			var prov = selectedValuesStr("blk_lk_prov");
			var loca = selectedValuesStr("blk_lk_loca");

			var time_from_hour = $("#block_time_from_hour").val();
			var time_from_mins = $("#block_time_from_mins").val();
			var ap1 = $("#block_ap1").val();

			var time_to_hour = $("#block_time_to_hour").val();
			var time_to_mins = $("#block_time_to_mins").val();
			var ap2 = $("#block_ap2").val();
			var ap_tmp_id = $("#global_context_appt_tmp_id").val();

			var comments = $("#blk_lk_comment").val();
			
			var send_uri = "save_block_options.php?load_dt="+load_dt+"&block_mode="+block_mode+"&prov="+prov+"&loca="+loca+"&time_from_hour="+time_from_hour+"&time_from_mins="+time_from_mins+"&ap1="+ap1+"&time_to_hour="+time_to_hour+"&time_to_mins="+time_to_mins+"&ap2="+ap2+"&comments="+escape(comments)+"&ap_tmp_id="+ap_tmp_id;
			//alert(send_uri);
			$.ajax({
				url: send_uri,
				success: function(resp){
					//alert(resp);
					var arr_resp = resp.split("~~~~~");
					$("#block_lock_div").modal("hide");

					//show loading image
					top.show_loading_image("hide");

					load_appt_schedule(arr_resp[0], arr_resp[1], "", "nonono", false); //dt and dayname in response
				}
			});
		}
	}
}

function day_print_options(sel_pro, load_dt, level){
	if(!sel_pro) sel_pro = "";
	if(!load_dt) load_dt = "";
	
	if(load_dt == ""){
		var load_dt = get_selected_date();
	}
	
	var selProCombo = get_selected_providers();
	var selFacCombo = get_selected_facilities();
	
	$.ajax({ 
		url: "load_print_options.php?load_dt="+load_dt+"&sel_pro="+sel_pro+"&level="+level+"&selProCombo="+selProCombo+"&selFacCombo="+selFacCombo,
		success: function(resp){			
			var arr_resp = resp.split("~~~~~");
			
			$("#day_print_options_div").modal("show");
			$("#print_options_content").html(arr_resp[1]);
			var arr_exclusion= ["Patient DOB","Phone","Procedure","Comments","Appt Made","CoPay","Pt. Prv Bal"];
			var exclusion="<span class='a_clr1' style='margin-left:80px;float:right;font-weight:bold;cursor:pointer;	 font-size:13px;' id='excl_link' onClick='$(\"#exc_div\").show(\"slide\");'>Exclude</span>";
			var exculsion_ele;
			exculsion_ele="<div class='section_header'>Exclusion<span class='fr' onClick='$(\"#exc_div\").hide(\"slide\");'><img src='../../library/images/close14.png'> </span></div><table class='section' style='width:100%'>";
			exculsion_ele+="<tr>";
			var label_str='';
			for(var e=0;e<arr_exclusion.length;e++){
				
				label_str=arr_exclusion[e].replace('_');
				exculsion_ele+="<td><div class='checkbox'><input id='chkbox"+e+"' name='excusion_chkbox[]' type='checkbox' value='"+arr_exclusion[e]+"' checked='checked'><label for='chkbox"+e+"'>"+label_str+"</label></div></td>";
				if(e==3){exculsion_ele+='</tr><tr>';}
				
			}
			exculsion_ele+="</tr>";
			exculsion_ele+='</table>';
			$("#exc_div").html(exculsion_ele);
			if(level == 1){
				$("#print_options_caption").html(" - Print Options All"+exclusion);
			}
			else if(level == 2){
				$("#print_options_caption").html(" - Print Options"+exclusion);
			}
			$("#print_options_date").html(arr_resp[0]);
			//add buttons
			$("#day_print_options_footer").html(arr_resp[2]);
			//refresh select picker
			$("#print_loca").selectpicker("refresh");
			$("#print_prov").selectpicker("refresh");
		}
	});
}

function day_print_process(load_dt){
	if(!load_dt) load_dt = "";
	var prov = selectedValuesStr("print_prov");
	if(prov === false) prov = '';
	
	var loca = selectedValuesStr("print_loca");
	if(loca === false) loca = '';
	
	if(load_dt == ""){
		var sel_date = get_selected_date();
	}else{
		var sel_date = load_dt;
	}
	var arr_sel_date = sel_date.split("-"); //ymd
	var eff_date = getDateFormat(sel_date);
	var selMidDay = "";
	var selMidDay_act = document.getElementsByName("print_act");
	for(act = 0; act < selMidDay_act.length; act++){
		if(selMidDay_act[act].checked == true){
			if(selMidDay_act[act].id == "print_fullday"){
				selMidDay = "full"
			}
			if(selMidDay_act[act].id == "print_morning"){
				selMidDay = "morning"
			}
			if(selMidDay_act[act].id == "print_evening"){
				selMidDay = "afternoon"
			}
		}
	}
	
	if(document.getElementById("from_date"))
		document.getElementById("from_date").value = eff_date;
	if(document.getElementById("comboFac"))
		document.getElementById("comboFac").value = loca;
	if(document.getElementById("comboProvider"))
		document.getElementById("comboProvider").value = prov;
	if(document.getElementById("selMidDay"))
		document.getElementById("selMidDay").value = selMidDay;

	document.frm_day_appt_print.submit();
	$("#day_print_options_div").modal("hide");
	$("#exc_div").hide();
}	


function day_proc_summary(sel_pro, load_dt){
	if(!sel_pro) sel_pro = "";
	if(!load_dt) load_dt = "";
	if(load_dt == ""){
		var load_dt = get_selected_date();
	}
	if(sel_pro == ""){
		sel_pro = get_selected_providers();
	}
	var sel_fac = get_selected_facilities();
	
	$.ajax({ 
		url: "load_day_summary.php?load_dt="+load_dt+"&sel_pro="+sel_pro+"&sel_fac="+sel_fac,
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~~~~");

			var arr_resp1 = resp.split("<div id=\"docDiv\"");
			var noOfDoctors =(arr_resp1.length) - 1;
			if(noOfDoctors ==0) { noOfDoctors=1; }
			
			var width = parseInt(noOfDoctors) * 200;
			
			if(width > 800){
				var scrollWidth= width;
				width = 800;
				$("#baseContentDiv").css("overflow-x", "scroll");
				$("#day_proc_summ_content").css("width", scrollWidth+"px");
			}

			$("#day_proc_summ_div").modal("show");			

			$("#baseContentDiv").css("width", width+"px");			

			$("#day_proc_summ_content").html(arr_resp[1]);
			
			$("#day_proc_summ_date").html(arr_resp[0]);		
		}
	});
}


// Function Uses - Function used for CL-Sply Button on Frontdesk,
// function called when Contact Lens Order Submitted at Popup.
function redirectToEnterCharges(pid){
	//var send_url ="../accounting/accountingTabs.php?flagSetPid=true&tab=enterCharges";
	//top.core_redirect_to("Accounting", send_url);
	top.change_main_Selection(top.document.getElementById('AccountingEC'));
}



// WEEKLY ADD APPOINTMENT 
function add_appt_weekly(pr_id, pat_id, fac_id, appt_from, eff_date, temp_id)
{
	if($('#global_apptact').val() == 'reschedule') {
		$("#appt_drag").css("display", "none");
	}
	var url = "add_appt_weekly.php?pr_id="+pr_id+"&fac_id="+fac_id+"&pat_id="+pat_id+"&appt_from="+appt_from+"&eff_date="+eff_date+"&temp_id="+temp_id;
	window.open(url,'addApptWeek','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=260,left=20,top=100');
}


function send_values_weekly(times_from, eff_date_add, loc, pro1, sch_tmp_id){

	var save_type = $("#global_apptact").val();

	if(save_type != ""){
		//$("#global_apptstid").val(sch_tmp_id);
		//$("#global_apptsttm").val(times_from);
		//$("#global_apptdoc").val(pro1);
		//$("#global_apptfac").val(loc);
		//$("#global_apptstdt").val(eff_date_add);

		//patient id
		var pat_id = $("#pat_id").val();					//setting patient id
		var ap_id = $("#global_apptid").val();

		//document.detachEvent("onmousemove", move_trail);
		$(document).unbind("mousemove", move_trail);
		hide_tool_tip();

		//procedure id
		if($("#sel_proc_id").val() == ""){				
			$("#sel_proc_id").focus();
			top.fAlert("Please select Procedure.");
			return false;
		}else{
			var proc_id = $("#sel_proc_id").val();				//setting procedure id
		}

		if(pat_id != "" && pro1 != ""){
			
			var url_validity = "check_appt_validity.php?st_date=" + eff_date_add + "&pat_id=" + pat_id + "&st_time=" + times_from + "&sl_pro=" + proc_id + "&pro_id=" + pro1 + "&template_id=" + sch_tmp_id + "&fac_id=" + loc + "&querytype=" + save_type;		
			//alert(url_validity);
			
			$.ajax({
				url: url_validity,
				success: function(resp){				
					
					arr_resp = resp.split("~~~");
					if(arr_resp[0] == "y"){	
						
						btn1 = 'Yes';
						btn2 = 'No';

						func1 = 'validate_or_password';
						func2 = 'hideConfirmYesNo';
						
						if(arr_resp[1] == "y"){
							misc = "ASKPASSWORD";
							title = 'Admin Override Required!';
						}else{
							misc = "DONOTASKPASSWORD";
							title = 'Warning!';
						}

						//alert(title+", "+arr_resp[2]+", "+btn1+", "+btn2+", "+func1+", "+func2+", "+misc);
						scheduler_warning_disp_weekly(title, arr_resp[2], btn1, btn2, func1, func2, misc);
					}else{
						if(save_type == "reschedule"){
							//loading reasons							
							$.ajax({
								url: "load_reasons.php?pt_id="+pat_id+"&ap_id="+ap_id,
								success: function(resp){									
									var arr_resp = resp.split("~~~~~");
									var title = "Reschedule Reason";
									
									var msg ='';
									msg ='<div class="row">';
									msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
									msg +='</div>';
									
									msg +='<div class="row">';
									msg +='<div class="col-sm-12">';
									msg +='<div class="form-group">';
									msg +='<label for="">Reason:</label>';
									msg +='<select id="reschedule_reason" name="reschedule_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
									msg +='</div>';
									msg +='</div>';
									msg +='</div>';

									var btn1 = 'OK';
									var btn2 = 'Cancel';

									var misc = "DONOTASKPASSWORD";
									
									var func1 = 'save_reschedule_reason_weekly';
									var func2 = 'hideConfirmYesNo';

									scheduler_warning_disp_weekly(title, msg, btn1, btn2, func1, func2, misc);
								}
							});
						}else{
							addApptWeek();
						}
						return true;
					}
				}
			});
		}
	}
}

function scheduler_warning_disp_weekly(title, msg, btn1, btn2, func1, func2, misc){

	text = "<div id=\"msgDiv-handle\" class=\"fl section_header\" style=\"width:395px\">"+title+"</div><div class=\"sc_line\" style=\"text-align:left;\">"+msg+"</div>";
	if(misc == "ASKPASSWORD"){
		text += "<div class=\"sc_line\"></div><div class=\"fl\" style=\"text-align:left;\">Admin Password: </div><div class=\"fl\" style=\"text-align:left;\"><input type=\"password\" id=\"AdminPass\" name=\"AdminPass\"></div>";
	}
	text += "<div class=\"sc_line\"></div><div class=\"fl\" style=\"margin-left:145px;text-align:right;\"><input type=\"button\" style=\"display:block;\" value=\""+btn1+"\" onClick=\"window."+func1+"('week')\" class=\"dff_button\"/></div><div class=\"fl\" style=\"width:195px;text-align:left;\"><input type=\"button\" value=\""+btn2+"\" onClick=\"window."+func2+"(-1)\" class=\"dff_button\"/><br><br></div>";			  
	
	document.getElementById('msgDiv').innerHTML = text;
	document.getElementById('msgDiv').style.display = 'block';
}


function addApptWeek(){
//self.close();
	var pt_id = $("#pat_id").val();
	var ap_id = $("#global_apptid").val();
	var mode = $("#global_apptact").val();
	var tmp_id = $("#global_tempid").val();
	var start_time = $("#global_apptsttm").val();
	var doctor_id = $("#global_apptdoc").val();
	var facility_id = $("#global_apptfac").val();
	var start_date = $("#global_apptstdt").val();

	if(pt_id != ""){
		//patient specific data
		
		//appointment specific data
		var ap_notes = ($("#txt_comments").length !== 0) ? escape($("#txt_comments").val()) : "";
		var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";

		var send_uri = "save_appt_weekly.php?save_type="+mode+"&pt_id="+pt_id+"&ap_id="+ap_id+"&ap_notes="+ap_notes+"&ap_procedure="+ap_procedure+"&start_date="+start_date+"&start_time="+start_time+"&doctor_id="+doctor_id+"&facility_id="+facility_id+"&tmp_id="+tmp_id;

		//alert(send_uri);

		$.ajax({
			url: send_uri,
			type: "POST",
			success: function(resp){
				var arr_resp = resp.split("~");
				if(arr_resp[0] == "save"){
					pre_load_front_desk(pt_id, ap_id, false);
				}
				if(arr_resp[0] == "addnew" || arr_resp[0] == "reschedule"){
//					var arr_start_date = start_date.split("-");
//					var new_start_date = arr_start_date[2]+"-"+arr_start_date[0]+"-"+arr_start_date[1];					
					hideConfirmYesNo();
					self.close();
					window.opener.$('#global_apptactreason').val('');
					//window.opener.$('#global_ptid').val('');
					window.opener.$('#sel_pat_name').val('');
					window.opener.$('#sel_proc_id').val('');
					window.opener.$('#global_apptid').val('');
					window.opener.$('#global_apptact').val('addnew');
					
					window.opener.load_week_appt_schedule();					
				}
			}
		});
	
	}
}

function drag_name_weekly(ap_id, pt_id, sel_pat_name, sel_proc_id, mode, e){

	if(!ap_id) ap_id = "";
	if(!pt_id) pt_id = "";
	if(!e) e = window.event;
	
	if(ap_id == "get") TestOnMenu();
	if(pt_id == "get") pt_id = $("#global_context_ptid").val();
	if(ap_id == "get") ap_id = $("#global_context_apptid").val();	

	$("#global_ptid").val(pt_id);
	$("#sel_proc_id").val(sel_proc_id);	
	$("#global_apptid").val(ap_id);
	$("#global_apptact").val(mode);
	$("#sel_pat_name").val(sel_pat_name);
	

	$("#appt_drag").addClass("sc_title_font");
	$("#appt_drag").css("backgroundColor", "");			
	$("#appt_drag").width("320");
	$("#appt_drag").css("top", e.clientY);
	$("#appt_drag").css("left", e.clientX);
	
	
	
	var send_uri = "schedule_new_tooltip.php?tool_sch_id="+ap_id+"&pate_id="+pt_id+"&sel_proc_idR="+sel_proc_id;
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			$("#appt_drag").html(resp);
			$("#appt_drag").css("display", "block");
			//document.attachEvent('onmousemove', move_trail);
			$(document).bind("mousemove", move_trail);
		}
	});
}


function save_reschedule_reason_weekly(){
	var reason = $("#reschedule_reason").val();
	if(reason == ""){
		top.fAlert("Please select a reason to continue.");
		return false;
	}else{
		$("#global_apptactreason").val(reason);
		//alert($("#global_apptactreason").val());
		addApptWeek();
	}
}

/*ref phy and pcp popup window*/
function searchPhysicianWindow(){
	search_val =  $('#front_primary_care_name').val();
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

	if(target_search_val != '')
	{
		window.open("../admin/users/searchPhysician.php?btn_sub=search&sel_by=LastName&txt_for="+target_search_val,"window1","width=800,height=500,scrollbars=yes, status=1");	
	}
	else
	{
		window.open("../admin/users/searchPhysician.php","window1","width=800,height=500,scrollbars=yes, status=1");	
	}
	
}
function searchPCPWindow(){
	search_val = $('#pcp_name').val();
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
	
	if(target_search_val != '')
	{
		window.open("../admin/users/searchPCP.php?btn_sub=search&sel_by=LastName&txt_for="+target_search_val,"window2","width=800,height=500,scrollbars=yes, status=1");
	}
	else
	{
		window.open("../admin/users/searchPCP.php","window2","width=800,height=500,scrollbars=yes, status=1");
	}
}
function get_phy_name_from_search(strVal,id){
	//document.getElementById('front_primary_care_id').value = id;
	//document.getElementById('front_primary_care_name').value = strVal;
	console.log('function from common js file with same name is in usr');
	
}
function get_pcp_name_from_search(strVal,id){
	document.getElementById('pcp_id').value = id;
	document.getElementById('pcp_name').value = strVal;
	
}

//function to sync iolink
//var current_form_id=0;
function funChbxOcHx(obj) {
	obj.checked=true;
	var cur_obj=obj.checked;
	$(".chbx_ochx").each(function(index, element) {
        this.checked=false;
    });
	obj.checked=cur_obj;
	//current_form_id=obj.value;
	$("#global_iolink_ocular_hx_form_id").val(obj.value);
}
function iolink_sync_ocular(mode,iolink_connection_setting_id){
	var schedule_id = $("#global_context_apptid").val();
	var iolink_ocular_hx_form_id = $("#global_iolink_ocular_hx_form_id").val();
	$("#global_iolink_mode").val(mode);
	$("#global_iolink_connection_settings_id").val(iolink_connection_setting_id);
	if((mode=="resync" && iolink_ocular_hx_form_id!=0) || mode=="remove") {
		iolink_sync();
		return;	
	}
	var title = "Select DOS For Ocular History";
	
	$.ajax({
			url: "ocular_hx_dos_ajax.php?ap_id="+schedule_id,
			success: function(resp){
					if(resp) {
						var arr_resp = resp.split(",");
						var arr_respNew = new Array();
						var i="";
						var newVal = "";
						var checkedVal = "";
						var len = arr_resp.length;
						if(len>0) {
							newVal+='<table>';
							for(i=0;i<len;i++) {
								arr_respNew = arr_resp[i].split("~~");
								checkedVal="";
								if(i==0) { 
									checkedVal='checked';
									$("#global_iolink_ocular_hx_form_id").val(arr_respNew[0]); 
								}
								
								newVal+='<tr><td style="padding-left:10px;"><input type="checkbox" class="chbx_ochx" name="chbx_ochx'+i+'" id="chbx_ochx'+i+'" value="'+arr_respNew[0]+'" '+checkedVal+' onClick="funChbxOcHx(this)"></td><td>'+arr_respNew[1]+'</td></tr>';
								if(len==1) {
									$("#global_iolink_ocular_hx_form_id").val(arr_respNew[0]);
								}
							}
							newVal+='</table>';
							
						}
					
						var msg = "<div style=\"text-align:center;\">"+newVal+"</div><div class=\"ml10\"></div>";
						var btn1 = 'OK';
						var btn2 = 'Close';
					
						var misc = "";
						
						var func1 = 'iolink_sync';
						var func2 = 'hideConfirmYesNo';
						
						if(len==0 || len==1) {
							iolink_sync();
						}else {
							scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
						}
						//iolink_sync(mode,iolink_connection_setting_id);
					}else {
						iolink_sync();	
					}
				}
		});
}

function iolink_sync(){
	top.show_loading_image("show");
	
	//$("#msgDiv_scheduler").hide();
	hide_custom_modal();
	var mode=$("#global_iolink_mode").val();
	var iolink_connection_setting_id=$("#global_iolink_connection_settings_id").val();
	var iolink_ocular_hx_form_id = $("#global_iolink_ocular_hx_form_id").val();
	var facility_type_provider = $("#facility_type_provider").val();
	var schedule_id = $("#global_context_apptid").val();
	var sa_date = get_selected_date();

	var send_uri = "common/iolink_sync.php?mode="+mode+"&sch_id="+schedule_id+"&sa_date="+sa_date+"&iolink_connection_setting_id="+iolink_connection_setting_id+"&iolink_ocular_hx_form_id="+iolink_ocular_hx_form_id+'&facility_type_provider='+facility_type_provider;
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			top.fAlert(resp);//alert(resp);
			$.ajax({
				url: "get_day_name.php?load_dt="+sa_date,
				success: function(day_name){
					top.show_loading_image("hide");
					load_appt_schedule(sa_date, day_name, schedule_id, '', false);
				}
			});
		}
	});
	TestOnMenu();
}
function showIolinkPdf(patentId){ 
	var parWidth = parent.document.body.clientWidth-100;
	window.open('iolink_pdf_page.php?patentId='+patentId,'iolinkPdf','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=550,left=30,top=100');
}

function show_more_procs(div_id){
	document.getElementById("more_options_"+div_id).style.display = "block";
//	document.getElementById("more_options_"+div_id).style.zIndex = 999;
	//$("#more_options_"+div_id).css("display", "block");
}

function hide_more_procs(div_id){
	document.getElementById("more_options_"+div_id).style.display = "none";
	//$("#more_options_"+div_id).css("display", "none");
}

function show_proc_fullname(proc_id){
	var div_name = "proc" + proc_id;
	var new_title = $("#"+div_name).html();
	$("#sel_proc_id").attr("title", new_title);
}

function get_Report(rte_id){
			var h = window.outerHeight-70;
			window.open('../patient_info/eligibility/eligibility_report.php?id='+rte_id,'eligibility_report','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}
function show_rte_div(id){
	$.ajax({
				url: "show_rte_detail.php?rte_id="+id,
				success: function(data){
					$('#rte_information').html(data);
					$('#rte_information').show();
				}
			}); 
}
function hide_rte_div(){
	$('#rte_information').hide();
}

var scroll_response_flag = 0;
var common_ch_color = 1;
var sch_expand_mode = 0;

function manage_slide_buttons()
{
	elemObjAvail = $('#sch_left_portion').parent().css('display');
	
	$("#scroll_control1").attr('disabled',true);//previous
	$("#scroll_control2").attr('disabled',true);//next
	
	var total_provider=$("#hid_prov_count").val();
	var max_slides=1
	if(elemObjAvail=='block'){max_slides=MAX_SCHEDULE_PER_SLIDE;}
	else{max_slides=9;}
	
	var total_slides=Math.ceil(total_provider / max_slides);
	var current_slide=$("#current_slide").val();
	
	if(current_slide>1)$("#scroll_control1").attr('disabled',false);//previous
	else $("#scroll_control1").attr('disabled',true);//previous
	 
	if(current_slide<total_slides)$("#scroll_control2").attr('disabled',false);//next
	else if(current_slide==total_slides)$("#scroll_control2").attr('disabled',true);//next
}

function get_slide(slide)
{
	var current_slide=$("#current_slide").val();
	if(slide=='next')next_slide=parseInt(current_slide)+1;
	else next_slide=parseInt(current_slide)-1;
	//update current slide 
	$("#current_slide").val(next_slide);
	//update slide buttons 
	manage_slide_buttons();
	return next_slide;
}

function load_sch_on_scroll(slide)
{
	var total_provider=$("#hid_prov_count").val();
	var elemObjAvail = $('#sch_left_portion').parent().css('display');
	var max_slides=1
	if(elemObjAvail=='block'){max_slides=MAX_SCHEDULE_PER_SLIDE;}
	else{max_slides=9;}
	var total_slides=Math.floor(total_provider / max_slides);
	var current_slide=$("#current_slide").val();
	top.show_loading_image("show");
	//hide curent slide
	$("#slide_"+current_slide).css('display','none');
	$("#slide_"+current_slide+"_header").css('display','none');
	//get next slide to show
	var next_slide=get_slide(slide);
	$("#slide_"+next_slide).css('display','block');
	$("#slide_"+next_slide+"_header").css('display','block');
	if($("#slide_"+next_slide).html()=='<strong>Loading ...</strong>')
	{
		active_providers = $('#prov_sch_rem_load').val();
		if($.trim(active_providers) != "")
		{
			remain_providers_arr = new Array();
			remain_providers_str = '';
			req_active_providers_arr = new Array();
			req_active_providers_str = '';		
			
			active_providers_arr = active_providers.split(',');
			apal = active_providers_arr.length;
			if(apal > max_slides)
			{
				for(i=0;i<max_slides;i++)
				{
					req_active_providers_arr[i] = active_providers_arr[i];	
				}
				req_active_providers_str = req_active_providers_arr.join(',');
				
				remain_providers_str = remain_providers_arr.join(',');			
				for(i=max_slides;i<apal;i++)
				{
					j = i-max_slides;
					remain_providers_arr[j] = active_providers_arr[i];	
				}
				remain_providers_str = remain_providers_arr.join(',');
			}
			else
			{
				req_active_providers_str = active_providers_arr.join(',');
			}
			
			if($.trim(req_active_providers_str)!="")
			{
				prov_sch_sel_load_val = $('#prov_sch_sel_load').val();
				prov_sch_sel_load_val += ','+req_active_providers_str;	
				$('#prov_sch_sel_load').attr({'value':prov_sch_sel_load_val});
			}
			
			$('#prov_sch_rem_load').attr({'value':remain_providers_str});
			
			load_appt_schedule_on_scroll(req_active_providers_str, '','','','','', "slide_"+next_slide);
		}
	}else
	{
		top.show_loading_image("hide");	
	}
}


/*
Function: load_appt_schedule_on_scroll
Purpose: to load appt templates on scroll
*/

function load_appt_schedule_on_scroll(load_prov, load_dt, day_name, appt_id, load_fd, showAlert, slide){
	if(!appt_id) appt_id = "";
	if(!load_fd) load_fd = "";
	if(typeof(showAlert) == "undefined") showAlert = true;	

	if(!appt_id) var appt_id = "";

	if(load_dt){
		var arr_load_dt = load_dt.split("-");
		set_date(arr_load_dt[0], arr_load_dt[1], arr_load_dt[2]);
	}else{
		load_dt = get_selected_date();
	}

	var arr_load_dt = load_dt.split("-");

	//getting selected facilities & providers
	var sel_fac = get_selected_facilities();
	var sel_pro = load_prov;
	
	scroll_response_flag = 1;
	scrollTopPos = $('#mn1_1').scrollTop();
	scrollTopPos += 80;
	/*ld = document.createElement('div');
	ld.setAttribute('class','fl sch_scroll_loading');
	ld.style.marginTop = scrollTopPos+"px";
	ld.style.marginLeft = "20px";
	ld.innerHTML = '<div class="sc_appt_loader_common">Loading...</div>';
	document.getElementById('appt_slots_cont').appendChild(ld);*/
	
	$.ajax({
		url: "appt_load_on_scroll.php?loca="+sel_fac+"&dt="+load_dt+"&prov="+sel_pro+"&appt_id="+appt_id+"&sid="+Math.random(),
		success: function(resp){
			//$('.sch_scroll_loading',$('#appt_slots_cont')).remove();
			var arr_response = resp.split("____");
			
			//var sh = document.createElement('span');
			//sh.innerHTML = arr_response[0];
			//document.getElementById('lr5').appendChild(sh);
			$("#"+slide+"_header").html(arr_response[0]);
			
			//var s = document.createElement('span');
			//s.innerHTML = arr_response[1];
			//document.getElementById('appt_slots_cont').appendChild(s);
			$("#"+slide).html(arr_response[1]);
			
			scroll_response_flag = 0;
			result_color = 333333 + common_ch_color;
			common_ch_color++;
			document.getElementById('lyr1').style.color = '#'+result_color;	
			top.show_loading_image("hide");
		}
		
	});
	
}

/*
 * Purpose : Collect labels by provider
 */


getMonthNoFromName={'January':'01','Jan':'01','February':'02','Feb':'02','March':'03','Mar':'03','April':'04','Apr':'04','May':'05','June':'06','Jun':'06','July':'07','Jul':'07','August':'08','Aug':'08','September':'09','Sep':'09','October':'10','Oct':'10','November':'11','Nov':'11','December':'12','Dec':'12'};

datesRange='';
label_options='';
label_options_loaded=0;

function collect_labels_by_provider()
{	
	/*var providers_arr='';
	providers=get_selected_providers();
	if(providers){providers_arr=providers.split(',');}
	if(providers_arr.length!=1)
	{
		//$('#sel_pro_labels').parent().parent().css({'visibility':'hidden'});
		return false;
	}
	if(label_options_loaded==0)
	{
		$.ajax({
			url:'get_labels_by_provider_avail_dts.php',
			complete:function(respData)
			{
				label_options=respData.responseText;
				label_options_loaded=1;
				if(label_options!='')
				{
					//$('#sel_pro_labels').html(label_options);

					var dd_pro = new Array();
					dd_pro["listHeight"] = 300;
					dd_pro["noneSelected"] = "Select Appt Type";
					dd_pro["onMouseOut"] = function(){//$("#sel_pro_labels").multiSelectOptionsHide();hLDatesByLbl();
					};

					//$('#sel_pro_labels').multiSelect(dd_pro);
					//$('#sel_pro_labels').parent().parent().css({'visibility':'visible'});				
				}
				else
				{
					//$('#sel_pro_labels').parent().parent().css({'visibility':'hidden'});				
				}
			}
		});	
	}
	else
	{
		if(label_options!='')
		{
			//$('#sel_pro_labels').html(label_options);

			var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select Appt Type";
			dd_pro["onMouseOut"] = function(){
				//$("#sel_pro_labels").multiSelectOptionsHide();hLDatesByLbl();
				};

			//$('#sel_pro_labels').multiSelect(dd_pro);
			//$('#sel_pro_labels').parent().parent().css({'visibility':'visible'});				
		}
		else
		{
			//$('#sel_pro_labels').parent().parent().css({'visibility':'hidden'});				
		}
	}	*/
}

function selPrimaryPhy()
{
    patient_id=$('#global_ptid').attr('value');
    patient_id=eval(patient_id);
    primary_phy_id=eval($('#sel_fd_provider').attr('value'));
    if(patient_id!="" && typeof primary_phy_id!="undefined")
        {			
			$('INPUT',$('#sel_pro_month').parent()).each(function()
			{
				cur_input_val= $(this).attr('value');
				if(cur_input_val==primary_phy_id)
					{
						$('input:checked',$('#sel_pro_month').parent()).attr({'checked':''}).parent().removeClass('checked');
						$(this).attr('checked','checked');
						if($(this).parent().hasClass('checked')!=true)
							{
								$(this).parent().addClass('checked');
								$('#sel_pro_month span').html($(this).parent().text());																
							}
						pro_change_load('day');	
					}
			}); 
        }
		
		return false;
}

/*
 * Purpose : Highlight dates by label or labels selected
 */


function hLDatesByLbl()
{
    datesRange='';
	provider_dates_avail=new Array();
    top.show_loading_image("show");

    facilities1=get_selected_facilities();
    providers=get_selected_providers();
	providers_arr=providers.split(',');
    sel_date=get_selected_date();
    
    labels=get_selected_labels();
	var cur_date_val='';
    pInd=0;
    context_obj='';
	if($.trim(labels)!="" && providers_arr.length==1)
	{	$('.cl_m_h').each(function(ind)
		{
			month_year_html=$.trim($(this).html());
			month_year_arr=month_year_html.split('&nbsp;');
	
			$('.cl_h_d',$(this).parent()).each(function()
			{
				cur_date_val=$.trim($(this).html());
				if (cur_date_val.toLowerCase().indexOf("<br>") == 0)
				{
					if(cur_date_val<10)
					{
						cur_date_val="0"+cur_date_val;
					}
					provider_dates_avail[pInd]=month_year_arr[1]+'-'+getMonthNoFromName[month_year_arr[0]]+'-'+cur_date_val;
		
					pInd++;
				}
			});
	
		}); 
		reqData='selected_date='+sel_date+'&provider_dates='+provider_dates_avail+'&provider_id='+providers+'&selected_facilities='+facilities1+'&labels='+labels;
		
		$.ajax({url:'get_hl_dates_by_label.php',type:'POST',data:reqData,complete:highLightDatesAct});
	}   
	else
	{
		$('div',$('.cl_m_h').parent()).removeClass('l_s_ds');
		top.show_loading_image("hide");
	}
}

function get_selected_labels()
{
   return selectedValuesStr("sel_pro_labels");
}

function highLightDatesAct(respData)
{
    //alert(respData.responseText); return false;
    datesRange=respData.responseText;
    //alert(datesRange); return false;
    datesRange=$.parseJSON(datesRange);   
    //alert(datesRange);return false;
    highLightDatesByLabels();
}

function highLightDatesByLabels()
{
    var providers=get_selected_providers();
	var providers_arr='';
	if(providers){providers_arr=providers.split(',');}
	
	var cur_date_val=0;
	$('div',$('.cl_m_h').parent()).removeClass('l_s_ds');

    if(datesRange!="" && datesRange!=null)
        {
            selected_date=get_selected_date();
			if(providers_arr.length==1)//earlier condition 
			{
				$('.cl_m_h').each(function(ind)
					{
						month_year_html=$.trim($(this).html());
						month_year_arr=month_year_html.split(' ');
	
						$('.cl_h_d',$(this).parent()).each(function()
						{
							cur_date_val=eval($.trim($(this).html()));
							if($.contains(cur_date_val,'<br>')==true)
							{
								cur_date_val_arr=cur_date_val.split('<br>');									
								
								if($.trim(cur_date_val[1])!="")
	
								{
									month_name_str=$('.cl_m_f',$(this)).html();
									if($.trim(month_name_str)!="")
									{
										//month_no=getMonthNoFromName[month_name_str];													
									}
									else
									{
										cur_date_val=cur_date_val_arr[0];	
									}
								}
								
							}
							if(cur_date_val<10)
							{
								cur_date_val="0"+cur_date_val;
							}
							cl_h_d="'"+month_year_arr[1]+'-'+getMonthNoFromName[month_year_arr[0]]+'-'+cur_date_val+"'";
							//alert(cl_h_d+'|'+datesRange);
							if($.inArray(cl_h_d,datesRange)!=-1)
							{
								if(cl_h_d==selected_date)
								{
									$(this).addClass('cl_hili');
								}
								else
								{
									if($(this).hasClass('l_s_ds')!=true)
									{
										$(this).addClass('l_s_ds');
									}
								}
							}
						});
	
					});  
			}
			else
			{
				$('.cl_m_h').each(function(ind)
				{
					month_year_html=$.trim($(this).html());
					month_year_arr=month_year_html.split(' ');
					//alert($(this).parent().html()); return false;
					$('.cl_p_d,.cl_s_d,.cl_d_d,.cl_hili',$(this).parent()).each(function()
					{
						cur_date_val=$.trim($(this).html());	
						month_no=$.trim(getMonthNoFromName[month_year_arr[0]]);		
						
						if($.contains(cur_date_val,'<br>')==true)
						{
							cur_date_val_arr=cur_date_val.split('<br>');									
							
							if($.trim(cur_date_val[1])!="")

							{
								month_name_str=$('.cl_m_f',$(this)).html();
								if($.trim(month_name_str)!="")
								{
									//month_no=getMonthNoFromName[month_name_str];													
								}
								else
								{
									cur_date_val=cur_date_val_arr[0];	
								}
							}
							
						}
						
						if(cur_date_val<10)
							{
								cur_date_val="0"+cur_date_val;
							}
						cl_h_d="'"+month_year_arr[1]+'-'+month_no+'-'+cur_date_val+"'";
						//alert(cl_h_d+'|'+datesRange);
						if($.inArray(cl_h_d,datesRange)!=-1)
							{
								if(cl_h_d==selected_date)
									{
										$(this).addClass('cl_hili');
									}
									else
									{
											if($(this).hasClass('l_s_ds')!=true)
												{
													$(this).addClass('l_s_ds');
												}
									}
							}
					});

				});  				
			}
        }
		
		top.show_loading_image("hide");
}

function setPriPhyOnPatientChange()
{
	if($('#global_ptid').val()!="" && eval(gl_pt_ch_ct)!=eval($('#global_ptid').val()))
	{		
		gl_pt_ch_ct=$('#global_ptid').val();	
		//selPrimaryPhy() is for set the primary phy. autmatically from the selected providers.
		selPrimaryPhy();					
	}
}
function PriPhyFlagSet()
{
	pri_phy_chk_flag=1;
}

// OR stands for Operating Room
function add_or_record(provider_id,facility_id,date_or,ths)
{
	assign_or = ths.value;
	if(provider_id!="" && facility_id!="" && date_or!="")
	{
		top.show_loading_image("show");
		$.ajax(
		{
			url : 'operating_room_allocation.php',
			type : "POST",
			data : 'provider_id='+provider_id+'&facility_id='+facility_id+'&date_or='+date_or+'&assign_or='+assign_or,
			complete : function(resultData)
			{
				top.show_loading_image("hide");
			}
		});			
	}
}

function EnableSaveButton()
{
	top.fmain.document.getElementById("btnsaveInsurance").disabled=false;
}

function connectToRemote(server_id)
{
	window.open("remoteConnect.php?server_c="+server_id,'','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=100,left=150,top=60')
}

function getToolTip(id,providerRCOId){
	if(id>0){
		var url="../patient_info/insurance/insuranceResult.php?dofrom=acc_reviewpt&id="+id+"&providerRCOId="+providerRCOId;
		$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					document.getElementById('ins_show_div').innerHTML = resp;
				}
		});
		var curPos = getPositionCoords();
		$('#ins_show_div').fadeIn();
		
		document.getElementById('ins_show_div').style.pixelTop = curPos.y;
		document.getElementById('ins_show_div').style.pixelLeft = curPos.x+10;
		var bro_ver=navigator.userAgent.toLowerCase();
		//if browser is crhome or firfox or safari then we need to placement issue
		if(bro_ver.search("chrome")>1 || bro_ver.search("firefox")>1){
			$("#ins_show_div").css({"display":"inline-block",top: parseInt(curPos.y-50), left: curPos.x+10});
			
		}
	}else{
		$('#ins_show_div').fadeOut()
	}
}

function hideToolTip()
{
	$('#ins_show_div').fadeOut()	
}

function getPositionCoords(e) {
	if(!e) e = window.event || event;
	else e = e || window.event || event;
	
	var cursor = {x:0, y:0};
	var de = document.documentElement;
	var b = document.body;
	cursor.x = e.clientX + 
		(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	cursor.y = e.clientY + 
		(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	//cursor.x = e.clientX;
	//cursor.y = e.clientY;
	return cursor;
}

function expand_shorten_sch()
{
	elemObjAvail = $('#sch_left_portion').parent().css('display');
	//remove any binded appt or reschedule appt action
	$(document).unbind("mousemove", move_trail);
	$("#global_apptact").val('');
	hide_tool_tip();
	
	if(elemObjAvail == 'block')
	{
		$('#sch_left_portion').parent().css({'display':'none'});
		$('#day_save').removeClass('col-lg-7');
		$('#day_save').addClass('col-lg-12');
		
		//$('#wn20,#mn1_1').css({'width':'100%'});
		//$('#hold2,#wn2').css({'width':'97%'});
		//rq_width = $('#hold').width();
		//$('#hold,#wn').width(rq_width+570);
		sch_expand_mode = 1;
		/*		
		wndo9 = new dw_scrollObj('mn1_1', 'mnlyr1_1');			
		lyr_1=document.getElementById('mnlyr1_1');
		dw_scrollObj.GeckoTableBugFix('mn1_1');	
		
		wndo = new dw_scrollObj('wn', 'lyr1');			
		dw_scrollObj.GeckoTableBugFix('wn');
		lyr=document.getElementById('lyr1');
				
		wndo1 = new dw_scrollObj('wn_1', 'lyr1_1');			
		dw_scrollObj.GeckoTableBugFix('wn_1');	
				 
		wndo3 = new dw_scrollObj('wn2', 'lyr2');			
		dw_scrollObj.GeckoTableBugFix('wn2');
				 
		wndo2 = new dw_scrollObj('ContextMenu', 'ContextMenu_1');			
		dw_scrollObj.GeckoTableBugFix('ContextMenu');		
		*/
	}
	else
	{
		$('#sch_left_portion').parent().css({'display':'block'});
		$('#day_save').addClass('col-lg-7');
		$('#day_save').removeClass('col-lg-12');
		//$('#wn20,#mn1_1').css({'width':'100%'});
		//$('#hold2,#wn2').css({'width':'97%'});
		//rq_width = $('#hold').width();
		//$('#hold,#wn').width(rq_width-570);
		
		sch_expand_mode = 0;		
		/*		
		wndo9 = new dw_scrollObj('mn1_1', 'mnlyr1_1');			
		lyr_1=document.getElementById('mnlyr1_1');
		dw_scrollObj.GeckoTableBugFix('mn1_1');	
		
		wndo = new dw_scrollObj('wn', 'lyr1');			
		dw_scrollObj.GeckoTableBugFix('wn');
		lyr=document.getElementById('lyr1');
				
		wndo1 = new dw_scrollObj('wn_1', 'lyr1_1');
		dw_scrollObj.GeckoTableBugFix('wn_1');	
				 
		wndo3 = new dw_scrollObj('wn2', 'lyr2');			
		dw_scrollObj.GeckoTableBugFix('wn2');
				 
		wndo2 = new dw_scrollObj('ContextMenu', 'ContextMenu_1');			
		dw_scrollObj.GeckoTableBugFix('ContextMenu');				
		*/
	}
		/*dw_scrollObj.resetPos('mn1_1');
		dw_scrollObj.resetPos('wn');
		dw_scrollObj.resetPos('wn_1');
		dw_scrollObj.resetPos('wn2');
		
		dw_scrollObj.setArrParams = [];
		dw_scrollObj.masterArr('mn1_1', 'mnlyr1_1')
		dw_scrollObj.masterArr('wn', 'lyr1');
		dw_scrollObj.masterArr('wn_1', 'lyr1_1');
		dw_scrollObj.masterArr('wn2', 'lyr2');	*/
	
		/*get_sch_width_on_scroll();
	
	result_color = 333333 + common_ch_color;
	common_ch_color++;
	document.getElementById('lyr1').style.color = '#'+result_color;	*/
	 pro_change_load('day');
}

function stopEventsinSch(e)
{
	if (!e)
	{
		var e = window.event;
		e.cancelBubble = true;
	}
	if (e.stopPropagation) e.stopPropagation();
}

var sadc_times_from = '';
var sadc_sch_date = '';
var sadc_fac_id = '';
var sadc_provider_id = '';
var sadc_temp_id = '';
var sadc_label_type = '';
var sadc_status = '';
var sadc_user_type = '';
var sadc_is_group_label = '';
var sadc_un_val = '';
function load_set_appt(times_from,sch_date,fac_id,provider_id,temp_id,un_val,label_type,status,user_type,is_group_label)
{
	//holding add appt on double click on time slot
	pat_name = '';
	pat_fname = $('#global_ptfname').val();
	if($.trim(pat_fname) != "")
	{
		pat_lname = $('#global_ptlname').val();
		pat_mname = $('#global_ptmname').val() != "" ? " "+$('#global_ptmname').val() : "";
		pat_name = pat_lname+pat_mname+", "+pat_fname;
	}
	
	times_from_arr = times_from.split(':');
	day_pattern = 'AM';
	if(times_from_arr[0] == 12) {day_pattern = 'PM';}
	if(times_from_arr[0] > 12) {day_pattern = 'PM'; times_from_arr[0] -= 12;}
	
	$("#sadc_procedure_site").val("");
	$("#sadc_sel_proc_id").val("");
	$("#sadc_sec_sel_proc_id").val("");
	$("#sadc_ter_sel_proc_id").val("");
		
	$('#sadc_txt_patient_name').val(pat_name);
	$('#sadc_appt_tm_view').html(times_from_arr[0]+':'+times_from_arr[1]+" "+day_pattern);
	$('#set_appt_div_slot_dc').modal("show");

	if(sadc_times_from != times_from || (un_val && sadc_un_val!=un_val))
	{
		sadc_times_from = times_from;
		sadc_label_type = label_type;
		sadc_status = status;
		sadc_user_type = user_type;
 		sadc_is_group_label = is_group_label;
		sadc_un_val = un_val;	
	}
	sadc_sch_date = sch_date;
	sadc_fac_id = fac_id;
	sadc_provider_id = provider_id;
	sadc_temp_id = temp_id;
}

function add_appt_bydcon_slot()
{
	if( check_deceased() ) return false;

	$("#global_apptact").val("addnew");
	ap_id = ''; $("#global_apptid").val(ap_id);
	$("#pri_eye_site").val($('#sadc_site_pri').val());
	$("#sec_eye_site").val($('#sadc_site_sec').val());
	$("#ter_eye_site").val($('#sadc_site_ter').val());
	
	$("#procedure_site").val($('#sadc_site_pri').val());
	$("#sel_proc_id").val($('#sadc_sel_proc_id').val());
	$("#sec_sel_proc_id").val($('#sadc_sec_sel_proc_id').val());
	$("#ter_sel_proc_id").val($('#sadc_ter_sel_proc_id').val());
	if(!$('#global_ptfname').val())
	{
		top.fAlert('Please select an Patient to add appointment');
		return false;	
	}
	top.fmain.sch_drag_id(sadc_times_from, sadc_sch_date, sadc_fac_id, sadc_provider_id, sadc_temp_id, sadc_un_val, sadc_label_type, sadc_status, '', '-1', sadc_user_type, sadc_is_group_label);
	$("#set_appt_div_slot_dc").modal("hide");
	top.show_loading_image("show");
}

function searchPatient(){
	var name = document.getElementById("sadc_txt_patient_name").value;
	var findBy = document.getElementById("sadc_txt_findBy").value;
	var msg = "";
	if(name == ""){
		msg = "Please Fill Name For Search.\n";
	}
	if(findBy == ""){
		msg += "Please Select Field For Search.";	
	}
	if(msg){
		top.fAlert(msg);
	}
	else{
		if(isNaN(name)){
			//window.open("../scheduler_v1_1_1/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name,"mywindow","width=800,height=500,scrollbars=yes");
			window.open("search_patient_popup.php?sel_by="+findBy+"&txt_for="+name+"&btn_sub=Search&call_from=scheduler","mywindow","width=800,height=500,scrollbars=yes");
		}
		else{
			$.ajax({
				url: 'chk_patient_exists.php',
				type: 'POST',
				data: 'pid='+name+'&findBy='+findBy,
				success: function(resultData)
				{
					if(resultData == 'n')
					{
						top.fAlert('Patient not found');	
					}
					else
					{
						pid = eval(resultData);
						pre_load_front_desk(pid,'','');	
					}				
				}
			});						
		}
	}
	return false;
}

function searchPatient2(obj){
	var patientdetails = obj.value.split(':');
	if(isNaN(patientdetails[0]) == false){
		document.getElementById("sadc_patientId").value = patientdetails[0];
		document.getElementById("sadc_txt_patient_name").value = patientdetails[1];
	}
}

//Print Vision PC --
function print_vision_pc_1(form_id){
	if(form_id == "undefined" || typeof form_id == "undefined")
	{
		form_id = 0;	
	}
	var str="";
	if(top.JS_WEB_ROOT_PATH){
		str = top.JS_WEB_ROOT_PATH;	
	}else if(opener && opener.top.JS_WEB_ROOT_PATH){
		str = opener.top.JS_WEB_ROOT_PATH;
	}
	
	if(str!=""){str+='/interface/main/';}
	
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;				
	//window.open(str+'print_patient_pc.php?printType=1&print_form_id='+form_id,'printPatientPC','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	window.open('../chart_notes/requestHandler.php?printType=1&elem_formAction=print_pc&print_form_id='+form_id,'printPatientPC','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	
}
//Print Vision PC --

/*-----  Remote Server Functions  -----*/

function is_remote_server()
{
	var return_bool = false;
	return return_bool;
}

function get_server_id()
{
	return $('#sel_server').val();
}

function change_server(ths)
{
	server_val = ths.value;
	if(server_val == 0)
	{
		top.changeSrcFun(top.fmain,'../scheduler_v1_1_1/base_day_scheduler.php','1','Scheduler');
	}
	else
	{
		get_prov_fac_drop_down(server_val);		
	}
}

function json_resp_handle(resp)
{
	respData = $.parseJSON(resp);	
	if($.trim(respData.sch.error) != "")
	{
		top.fAlert('Remote Connection Error - '+respData.sch.error);	
		return '';
	}
	return $.trim(respData.sch.data);
}

function ju_encode_reqArr(reqTaskArray)
{
	return encodeURIComponent(JSON.stringify(reqTaskArray));	
}

function get_prov_fac_drop_down(server_val)
{
	var data_sender = "server_id="+server_val;
	top.show_loading_image("show");	
	$.ajax({
		url : 'get_prov_fac_drop_down.php',
		type: 'POST',
		data: data_sender,
		complete : function(resp)
		{
			resultData = resp.responseText;
			resultData_arr = resultData.split('___*___');
			var fac_data = resultData_arr[0];
			var prov_data = resultData_arr[1]; 
			$('#facilities_cnt').html(fac_data);
			$('#sel_pro_month_cnt').html(prov_data);

			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select All";
			dd_fac["onMouseOut"] = function(){$("#facilities").multiSelectOptionsHide();fac_change_load('day');};
			$("#facilities").multiSelect(dd_fac);			

			if(server_val == 0)
			{
				var dd_pro = new Array();
				dd_pro["listHeight"] = 300;
				dd_pro["noneSelected"] = "Select All";
				dd_pro["onMouseOut"] = function(){$("#sel_pro_month").multiSelectOptionsHide();pro_change_load('day');};
				$("#sel_pro_month").multiSelect(dd_pro);				
			}
			fac_change_load('day');
			top.show_loading_image("hide");				
		}
	});
}

/*-------- Remote Server Code Ends here  ---------*/

function pt_deposits_fun(){
	top.popup_win("common/patient_pre_payment.php","width=1580,scrollbars=0,height=700,top=100,left=5");
}
function get_appt_hx_for_sort()
{
	var json_appthx_date_arr = {};
	$(".remote_apptHx_row",$("#appt_hx_sort")).each(function()
	{
		var date_val = $(".remote_apptHX_date",$(this)).val();
		//alert(date_val);
		var date_val_arr = date_val.split(',');
		var year_val = parseInt(date_val_arr[0],10);
		var month_val = parseInt(date_val_arr[1],10);
		var day_val = parseInt(date_val_arr[2],10);
		var hour_val = parseInt(date_val_arr[3],10);
		var minutes_val = parseInt(date_val_arr[4],10); 
		
		var date_val_extract = new Date(year_val,month_val,day_val,hour_val,minutes_val,0).getTime();		
		json_appthx_date_arr[date_val_extract] = $(this)[0].outerHTML;		
	});
	var sort_init_arr = new Array();
	var ix_ind = 0;
	for(x in json_appthx_date_arr)
	{
		sort_init_arr[ix_ind] = x; 	
		ix_ind++;
	}
	sort_init_arr.sort(function(a,b){return b-a});
	
	var result_json_appthx_arr = {};
	for(var xn=0;xn<sort_init_arr.length;xn++)
	{
		var date_val_extract = sort_init_arr[xn];

		result_json_appthx_arr[date_val_extract] = json_appthx_date_arr[date_val_extract];
	}	
	var result_appthx_str = "";
	for(xs in result_json_appthx_arr)
	{
		result_appthx_str += result_json_appthx_arr[xs];
	}
	var apptHxHeader = '<tr height="20" bgcolor="#4684ab"><td width="19%" class="text_b_w" align="left" nowrap="nowrap">Date Time</td><td width="19%" class="text_b_w" align="left">Provider</td><td width="15%" class="text_b_w" align="left">Location</td><td width="21%" class="text_b_w" align="left">Procedure</td><td width="20%" class="text_b_w" align="left">Comments</td></tr>';
	result_appthx_str = "<table>"+apptHxHeader+result_appthx_str+"</table>";
	return result_appthx_str;	
}
function get_avaiable_slot(selected_labels,act,sch_timing,c_date,pid){
	if(selected_labels=="" || typeof(selected_labels)=="undefined"){
		selected_labels=selectedValuesStr("sel_all_labels");
	}
	
	var event_id=$("#chain_event").val();
	if(!event_id && !selected_labels || event_id)
	{
		selected_labels="Slot without labels~~NA";		
	}
	var action_c="";
	var	get_current_provider=selectedValuesStr("provider_label");
	//var	get_current_provider=$("#provider_label").val();
	if(selected_labels && get_current_provider){
		var get_current_date=get_selected_date();
		var	get_current_facility=selectedValuesStr("facilities_label");
		if($('#current_avail_date').val()==""){
			$('#current_avail_date').val(get_current_date);
		}
		if(act){
			var curr_d=$('#current_avail_date').val()
			var date_c=curr_d.split("-");
			var dd=mm=yy=new_date="";
			yy=date_c[0];mm=date_c[1];dd=date_c[2];
			if(act=="next"){
				mm=parseInt(mm)+1;
				if(mm>12){
					mm=1;
					yy=parseInt(yy)+1;
				}		
			}
			if(act=="prev"){
				mm=parseInt(mm)-1;
				if(mm==0){
					mm=12;
					yy=parseInt(yy)-1;
				}
			}
			
			get_current_date=yy+"-"+mm+"-01";
			$('#current_avail_date').val(get_current_date);
		}
		if($('#current_avail_date').val()){
			get_current_date=$('#current_avail_date').val();
		}
		var patient_p="";
		if(pid){
			patient_p="&pat_id="+pid;
		}
		if(sch_timing=="" || typeof sch_timing == "undefined"){
			var sch_timing=$(".sch_timing_radio:checked").val();
		}
		
		var fac_conc='';
		if(get_current_facility){fac_conc='&facility_sel='+get_current_facility}
		var sel_day_option='';
		var day_option=selectedValuesStr("days_of_week");
		if(day_option){sel_day_option='&days_sel='+day_option;}
		//$("#next_available_slot").html("<img src='../../library/images/sch-loader.gif'>");
		var file_url = WEB_ROOT+"/interface/scheduler/";
			file_url+='ajax_next_appointment.php?current_date='+get_current_date+'&current_provider='+get_current_provider+"&sel_label="+ encodeURIComponent(selected_labels)+action_c+fac_conc+"&event_id="+ event_id +"&sch_timing="+ sch_timing+patient_p+sel_day_option+ "&random_string="+Math.random();
		
		top.master_ajax_tunnel(file_url,get_avaiable_slot_callBack);
		/*$.ajax({
			url:file_url,
			complete:function(respData){
				if(respData.responseText){
					$("#next_available_slot").html(respData.responseText);
					var d=respData.responseText;
					$('#next_available_slot_div').modal('show');
				}
			}
		});*/	
	}
}

//cal back function for get_avaiable_slot
function get_avaiable_slot_callBack(response, etc)
{
	if(response){
		$("#next_available_slot").html(response);
		$('#next_available_slot_div').modal('show');
	}		
}
function add_appointment_next_sch(time_from,eff_date_add,fac_id,provider_id,tmp_id,proc,label_t,valid_proc,p_date,cday,obj, label){
	//this condition is removed because of it when we add appt from next available without procedure it does given us "choose procedure first" alert
	// && $("#sel_proc_id").val()
	if($("#global_ptid").val()){
		if(!$("#sel_proc_id").val())
		{
			top.fAlert('Please select Procedure.');
		}
		else if( check_deceased() ) { return false; }
		else
		{
			var ap_id=0;
			var mode ='addnew';
			var pt__id=$("#global_ptid").val();
			var ap_id=$("#global_apptid").val();
			var mode = $("#global_apptact").val();
			if(mode=='reschedule' && (ap_id!='' || typeof(ap_id)!='undefined'))
			{
				//do nothing
			}else
			{
				$("#global_apptact").val('');
				$("#global_apptact").val('');
				ap_id='';mode='addnew';
			}
			drag_name(ap_id, pt__id, mode);
			sch_drag_id(time_from,eff_date_add,fac_id,provider_id,tmp_id,proc,label_t,valid_proc,label,'-1');
		}
	}else{
		top.fAlert('Please select patient first');
		load_calendar(p_date, cday,'nonono');
		load_appt_schedule(p_date, cday,'nonono');
		setTimeout(function(){$('#'+obj).focus()},2500);
	}
}

function add_appointment_next_sch_multi(procedures,appt_count,start_date,facility_id,valid_proc,p_date,cday,obj){
	if($("#global_ptid").val()){
		
		var val_string="";
		var found_val=0;
		//check times~:~template~:~label type~:~provider~:~procedure
		var pro_arr=procedures.split(',');
		 for(drop_down=0;drop_down<appt_count;drop_down++)
		 {
			var sel_string= $("#timing_"+pro_arr[drop_down]).val();
			if(typeof(sel_string)!='undefined')
			{
				if(val_string)
				val_string=val_string+'~::~'+sel_string;
				else
				val_string=sel_string; 
				found_val++;
			}

		 }
		 
		 if(found_val<appt_count)
		 {
			 top.fAlert('Please select Appt Time for each procedure')
			 return false;
		 }
		 var pt__id=$("#global_ptid").val();
		 drag_name('', pt__id, 'addnew');
		 //for(drop_down=1;drop_down<=appt_count;drop_down++)
		 //{
			//var val_string= $("#timing_"+drop_down).val();
//			var val_arr=val_string.split("~:~");
//			start_time=val_arr[0];
//			tmp_id=val_arr[1];
//			label_t=val_arr[2];
//			doctor_id=val_arr[3];
//			tempproc=val_arr[4];

			start_time='';
			tmp_id='';
			label_t='';
			doctor_id='';
			tempproc='';
			//alert(time_from,eff_date_add,fac_id,provider_id,tmp_id,label_t,valid_proc);
			//sch_drag_id(time_from,eff_date_add,fac_id,provider_id,tmp_id,label_t,valid_proc,'','-1');
		
			var pt_id = $("#global_ptid").val();
			var ap_id = $("#global_apptid").val();
			var mode = $("#global_apptact").val();
		
			var pt_fname =  $("#global_ptfname").val();
			var pt_lname =  $("#global_ptlname").val();
			var pt_mname =  $("#global_ptmname").val();
			var pt_emr = $("#global_ptemr").val();
			
			var init_date_rs = $('#init_date_rs').val();
			var init_st_time_rs=$('#init_st_time_rs').val();
			var init_et_time_rs=$('#init_et_time_rs').val();
			var init_acronym_rs=$('#init_acronym_rs').val();
			var init_provider_id = $('#init_prov_id').val();
			var init_fac_id = $('#init_fac_id').val();		
			
			var ap_act_reason = ($("#global_apptactreason").length !== 0) ? escape($("#global_apptactreason").val()) : "";
			//alert(ap_act_reason);
			
			if(pt_id != ""){
				//patient specific data
				var pt_doctor_id = ($("#sel_fd_provider").length !== 0) ? $("#sel_fd_provider").val() : "";
				
				var pt_pcp_phy_id = ($("#pcp_id").length !== 0) ? $("#pcp_id").val() : "";
				var pt_pcp_phy = ($("#pcp_name").length !== 0) ? escape($("#pcp_name").val()) : "";
		
				var pt_ref_phy_id = ($("#front_primary_care_id").length !== 0) ? $("#front_primary_care_id").val() : "";
				var pt_ref_phy = ($("#front_primary_care_name").length !== 0) ? escape($("#front_primary_care_name").val()) : "";
		
				var pt_street1 = ($("#frontAddressStreet").length !== 0) ? escape($("#frontAddressStreet").val()) : "";
				var pt_street2 = ($("#frontAddressStreet2").length !== 0) ? escape($("#frontAddressStreet2").val()) : "";
				var pt_city = ($("#frontAddressCity").length !== 0) ? escape($("#frontAddressCity").val()) : "";
				var pt_state = ($("#frontAddressState").length !== 0) ? escape($("#frontAddressState").val()) : "";
				var pt_zip = ($("#frontAddressZip").length !== 0) ? escape($("#frontAddressZip").val()) : "";
				var pt_zip_ext = ($("#frontAddressZip_ext").length !== 0) ? escape($("#frontAddressZip_ext").val()) : "";
		
				var pt_email = ($("#email").length !== 0) ? escape($("#email").val()) : "";
				var pt_photo_ref = ($("#photo_ref").is(":checked")==true) ? 1 : 0;
		
				var pt_home_ph = ($("#phone_home").length !== 0) ? escape($("#phone_home").val()) : "";
				var pt_work_ph = ($("#phone_biz").length !== 0) ? escape($("#phone_biz").val()) : "";
				var pt_cell_ph = ($("#phone_cell").length !== 0) ? escape($("#phone_cell").val()) : "";
		
				var pt_status = ($("#elem_patientStatus").length !== 0) ? $("#elem_patientStatus").val() : "";
				var pt_other_status = ($("#otherPatientStatus").length !== 0) ? escape($("#otherPatientStatus").val()) : "";
				var pt_dod_patient = ($("#dod_patient").length !== 0) ? escape($("#dod_patient").val()) : "";
		
				//appointment specific data
				var ap_routine_exam = ($("#chkRoutineExam").length !== 0) ? $("#chkRoutineExam").val() : "";
				var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
				var ap_notes = ($("#txt_comments").length !== 0) ? encodeURIComponent($("#txt_comments").val()) : "";
				var ap_pickup_time = ($("#pick_up_time").length !== 0) ? escape($("#pick_up_time").val()) : "";
				var ap_arrival_time = ($("#arrival_time").length !== 0) ? escape($("#arrival_time").val()) : "";
				//var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";
				var ap_procedure = tempproc;
				var sec_ap_procedure = ($("#sec_sel_proc_id").length !== 0) ? $("#sec_sel_proc_id").val() : "";
				var ter_ap_procedure = ($("#ter_sel_proc_id").length !== 0) ? $("#ter_sel_proc_id").val() : "";	
				
				var pri_eye_site = ($("#pri_eye_site").length !== 0) ? $("#pri_eye_site").val() : "";
				var sec_eye_site = ($("#sec_eye_site").length !== 0) ? $("#sec_eye_site").val() : "";
				var ter_eye_site = ($("#ter_eye_site").length !== 0) ? $("#ter_eye_site").val() : "";
				
				var facility_type_provider = ($("#facility_type_provider").length !== 0) ? $("#facility_type_provider").val() : "";				
				var referral = ($("input[type=checkbox]#sa_ref_management").is(":checked") ? 1 : 0);
				var ref_management = "&pt_referral="+referral;
                
                var sa_verification = ($("input[type=checkbox]#sa_verification_req").is(":checked") ? 1 : 0);
                var verification_req = "&pt_verification="+sa_verification;
				
				var send_uri = "save_changes.php?save_type="+mode+"&pt_id="+pt_id+"&ap_id="+ap_id+"&pt_doctor_id="+pt_doctor_id+"&pt_pcp_phy_id="+pt_pcp_phy_id+"&pt_pcp_phy="+pt_pcp_phy+"&pt_ref_phy_id="+pt_ref_phy_id+"&pt_ref_phy="+pt_ref_phy+"&pt_street1="+pt_street1+"&pt_street2="+pt_street2+"&pt_city="+pt_city+"&pt_state="+pt_state+"&pt_zip="+pt_zip+"&pt_zip_ext="+pt_zip_ext+"&pt_email="+pt_email+"&pt_photo_ref="+pt_photo_ref+"&pt_home_ph="+pt_home_ph+"&pt_work_ph="+pt_work_ph+"&pt_cell_ph="+pt_cell_ph+"&pt_status="+pt_status+"&pt_other_status="+pt_other_status+"&pt_dod_patient="+pt_dod_patient+"&ap_routine_exam="+ap_routine_exam+"&ap_ins_case_id="+ap_ins_case_id+"&ap_notes="+ap_notes+"&pri_eye_site="+pri_eye_site+"&sec_eye_site="+sec_eye_site+"&ter_eye_site="+ter_eye_site+"&ap_pickup_time="+ap_pickup_time+"&ap_arrival_time="+ap_arrival_time+"&ap_procedure="+ap_procedure+"&start_date="+start_date+"&start_time="+start_time+"&doctor_id="+doctor_id+"&facility_id="+facility_id+"&tmp_id="+tmp_id+"&pt_fname="+pt_fname+"&pt_mname="+pt_mname+"&pt_lname="+pt_lname+"&pt_emr="+pt_emr+"&ap_act_reason="+ap_act_reason+"&tempproc="+tempproc+'&init_st_time_rs='+init_st_time_rs+'&init_et_time_rs='+init_et_time_rs+'&init_acronym_rs='+init_acronym_rs+'&init_provider_id='+init_provider_id+'&init_fac_id='+init_fac_id+'&sec_ap_procedure='+sec_ap_procedure+'&ter_ap_procedure='+ter_ap_procedure+'&init_date_rs='+init_date_rs+'&multi_sel_string='+val_string+'&facility_type_provider='+facility_type_provider+ref_management+verification_req;
			//alert(send_uri);false;
				$.ajax({
					url: send_uri,
					type: "POST",
					success: function(resp){
						//alert(resp);
						//return false;
						if(is_remote_server() == true)
						{
							resp = json_resp_handle(resp);				
						}
						var arr_resp = resp.split("~");
						if(arr_resp[0] == "save"){
							pre_load_front_desk(pt_id, ap_id, false);	
						}
						/*if(arr_resp[0] == "addnew" || arr_resp[0] == "reschedule"){
							var arr_start_date = start_date.split("-");
							var new_start_date = arr_start_date[2]+"-"+arr_start_date[0]+"-"+arr_start_date[1];					
							load_calendar(new_start_date, arr_resp[1], '', false);					
						}*/
						$("#global_apptact").val("");
						$("#global_apptactreason").val("");
						$("#global_apptstid").val("");
						$("#global_apptsttm").val("");
						$("#global_apptdoc").val("");
						$("#global_apptfac").val("");
						$("#global_apptstdt").val("");
						$("#global_appttempproc").val("");
						$('#init_fac_id').val("");
						$('#init_prov_id').val("");
						hideConfirmYesNo();
					}
				});
			}


		// }
		 
		 load_calendar(p_date, cday, '', false);	
	}else{
		top.fAlert('Please select patient first');
		load_calendar(p_date, cday,'nonono');
		load_appt_schedule(p_date, cday,'nonono');
		setTimeout(function(){$('#'+obj).focus()},2500);
	}
}

var label_load=false;
var facility_load=false; 
function collect_labels(){
	var pid=$("#global_ptid").val();

	if( check_deceased() ) return false;

	$("#next_available_slot_div").modal('show');
	//================Facility Label Options======================//
	var provier_id=facility_id=fac_pro="";
	var label_options='';var proc_s=opslot=false;
	var label_p;
	$.ajax({
		url:'ajax_get_pat_fac.php?pat_id='+pid,
		complete:function(respData){
			fac_pro=respData.responseText;
			if(fac_pro){
				var arr_facpro=fac_pro.split("~||~");
				provier_id=arr_facpro[0];
				facility_id=arr_facpro[1];
			}
			var d_pro=selectedValuesStr("sel_pro_month");
			var d_fac=selectedValuesStr("facilities");
			
			
			if(provier_id){
				//$("#provider_label").val(provier_id);
				
				var arr_selected_prov = provier_id.split(",");
				$("#provider_label option").each(function(id,elem){
					var value = $(elem).val();
					if(value.length > 0 || typeof(value) != 'undefined'){
						if($.inArray(value,arr_selected_prov)!=-1){
							$(elem).prop('selected',true);
						}
					}
				});
				
			}else{
				//provier_id_get=d_pro.split(",");
				//$("#provider_label").val(provier_id_get[0]);
				
				var arr_selected_prov = d_pro.split(",");
				$("#provider_label option").each(function(id,elem){
					var value = $(elem).val();
					if(value.length > 0 || typeof(value) != 'undefined'){
						if($.inArray(value,arr_selected_prov)!=-1){
							$(elem).prop('selected',true);
						}
					}
				});
				
			}
			
			$("#provider_label").selectpicker("refresh");
			
			if(facility_id==""){
				facility_id=d_fac;
			}
			var facility_option=$("#facility_options").html();
			$('#facilities_label').append(label_options);
						
			var facility_op=document.getElementById("facilities_label");
			var l;
			l = facility_op.options.length;
			
			var arr_selected_sess_facs= '';
			if(facility_id){
				arr_selected_sess_facs= facility_id.split(",");
			}
			var bl_fac_add;
			for(var t=0;t<(l);t++){ 
				o = facility_op.options[t];
				
				for(var j = 0; j < arr_selected_sess_facs.length; j++){
					//o.selected = false;
					bl_fac_add = false;
					if(o.value == arr_selected_sess_facs[j]){
						bl_fac_add = true;
							break;
					}
				}
				if(bl_fac_add==true){
					o.selected = true;
				}
			}
			
			$("#facilities_label").selectpicker("refresh");
			/*var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select Provider";
			dd_pro["onMouseOut"] = function(){$("#provider_label").multiSelectOptionsHide();}
			$("#provider_label").multiSelect(dd_pro);	
			
			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select Facilities";
			dd_fac["onMouseOut"] = function(){$("#facilities_label").multiSelectOptionsHide();}
			$("#facilities_label").multiSelect(dd_fac);	
		*/
			label_p=$("#sel_proc_id option:selected").text();
			if(label_p && label_p!='-Reason-'){
				get_avaiable_slot(label_p,'','','',pid);
				proc_s=true;
			}
			if(proc_s==false){
				get_avaiable_slot('Slot without labels','','all_day','',pid);opslot=true;
			}
			facility_load=false;
			if($('select#sel_all_labels option').length==0){
				$.ajax({
					url:'get_labels_by_provider_avail_dts.php',
					complete:function(respData){
						label_options=respData.responseText;
						if(label_options){
							label_options=label_options.replace("Slot without labels-NA","Open Time Slot");
							if(opslot==true){
								label_options=label_options.replace('value="Slot without labels~~NA"','value="Slot without labels~~NA" Selected');
							} 
							if(proc_s==true && proc_s!="-Procedure-"){
									label_options=label_options.replace('value="'+label_p+'~~Procedure"','value="'+label_p+'~~Procedure" Selected');
							}
							$('#sel_all_labels').append(label_options);
							$("#sel_all_labels").selectpicker("refresh");
							/*var dd_label = new Array();
							dd_label["listHeight"] = 300;
							dd_label["noneSelected"] = "Select Labels";
							dd_label["onMouseOut"] = function(){$("#sel_all_labels").multiSelectOptionsHide();};
							$('#sel_all_labels').multiSelect(dd_label);
							$('#sel_all_labels').css({"width":"180px"});*/
						}
					}
				});
			}
			$("#next_available_slot").html("<div>Please select the label</div>");
			$("#div_curr_month").html("");
		}
	})			
}

function get_current_month(month_val,provier_id,facility_id){
	var pid=$("#global_ptid").val();
 	if(month_val){
		$("#div_curr_month").html(month_val);
	}
}

//function created to sort out mouse button click detect in new version browser
 function WhichButton (event) 
 {
		// all browsers except IE before version 9
	if ('which' in event) {
		switch (event.which) {
		case 1:
			return 0;
			break;
		case 2:
			return 1;
			break;
		case 3:
			return 2;
			break;
		}
	}
	else {
			// Internet Explorer before version 9
		if ('button' in event) {
			var buttons = "";
			if (event.button & 1) {
				return 0;
			}
			if (event.button & 2) {
				if (buttons == "") {
					return 2;
				}
				else {
					return 2;
				}
			}
			if (event.button & 4) {
				if (buttons == "") {
					return 1;
				}
				else {
					return 1;
				}
			}
			
		}
	}
}


function open_todo(){
	var record_exist;
	$.ajax({
			url:'to_do_first_avai.php?check_rows_todo=y',
			complete:function(respData){
			record_exist=respData.responseText;
		}
	});
	
	if(record_exist==2){
		var file_path='to_do_first_avai.php';
		var parentWid = parent.document.body.clientWidth;
		var parenthei = parent.document.body.clientHeight;
		window.open(file_path,'to_do','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+parentWid+',height='+parenthei+',left=10,top=100');
	}
}
function getWks(load_wk)
{
	if(load_wk){
	$.ajax({
		url: "to_do_first_avai.php?load_wk="+load_wk,
		success: function(resp){
			if(resp){
				//document.getElementById('patientSearcchResponse').style.display='block';
				//document.getElementById('day').innerHTML=resp;	
				//$("#day").html(resp);	
				$("#week").html(resp);
			}			
		}
	});
	}
}
function print_pt_key(){
	TestOnMenu();
	var pt_id = $("#global_context_ptid").val();
	window.open("print_pt_key.php?patient_id="+pt_id,"Print_PT_KEY","width=900","height=700","left=250","scrollbars=yes","resizable=yes");	
}
function get_accept_assignment(priInsStr){
	//"AA – Courtesy Billing"
	//"NAA - Courtesy Billing"
	//"NAA - No Courtesy Billing"
	$('#accept_assignment_div').html("AA");
	$('#accept_assignment_div').attr('title', 'Accept Assignment');
	if(priInsStr!=0){
		var val_arr = priInsStr.split("-|S|-");
		if(val_arr[1]==1){
			 $('#accept_assignment_div').html("NAA - CB");
			 $('#accept_assignment_div').attr('title', 'NAA - Courtesy Billing');
		}else if(val_arr[1]==2){
			 $('#accept_assignment_div').html("NAA - No CB");
			 $('#accept_assignment_div').attr('title', 'NAA - No Courtesy Billing');
		}
	}
}

function hide_div(val){
	document.getElementById("re_schedule_menu").style.display = val;
}

function Highlight(Object)
{
	if(!MouseOn)
	{
		TempColor = Object.style.color;
		Object.style.color = '#9F5000';
		Object.style.textDecoration = 'underline';
		MouseOn = true;
	}
	else
	{
		Object.style.color = TempColor;
		Object.style.textDecoration = 'none';
		MouseOn = false;
	}
}

 
function get271Report(id){
	var h = get271Report_hight;
	top.popup_win('../patient_info/eligibility/eligibility_report.php?id='+id,'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function save_new_prov_sch(){
	var msg = '';
	if($("#anps_sel_fac").val() == "" && msg == ""){
		msg = "- Please select Facility.";
	}
	if($("#anps_sel_pro").val() == "" && msg == ""){
		msg = "- Please select Provider name.";
	}
	if(($("#ansp_start_hour").val() == "" || $("#ansp_start_min").val() == "") && msg == ""){
		msg = '- Please select Template start time.';
	}
	if(($("#ansp_end_hour").val() == "" || $("#ansp_end_min").val() == "") && msg == ""){
		msg = '- Please select Template end time.';
	}
	if(msg){
		alert(msg);
	}else{
		EnableDisable(1);
		document.frm_dump_month12.submit();
	}
}
//patient search related functions
function setShowFindByVal(){
	document.getElementById("findByShow").value = document.getElementById("findBy").value;
}
function setSearchParameters(){
	searchPatientInFrontDesk(document.getElementById("findBy"));
}
function setDefaultShowFindByVal(){
	document.getElementById("findByShow").value = "Active";
}
//function related patient action taken from iportal-  approval
function approve_operation(row_id,ths){
	var dt = approve_operation_dt;
	ths_parent = $(ths).parent();	
	if(row_id != "" && parseInt(row_id)){
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
	}	
	$.ajax({
		url:'../../iportal_config/handle_pt_registration.php',
		data:'sel_op=approve&row_id='+row_id,
		type:'POST',
		complete:function(respData){
			var resp = jQuery.parseJSON(respData.responseText);
			if(resp.status=="success"){
				ths_parent.html('<div style="color:#090;font-weight:bold;">Approved<br />Patient Id: '+resp.pt_id+'</div>');				
			}
			else if(resp.status=="error"){
				ths_parent.html('<div style="color:#CC0000;font-weight:bold;">Error while approving</div>');				
			}
		}
	});
}
function disapprove_operation(row_id,ths){
	ths_parent = $(ths).parent();
	if(row_id != "" && parseInt(row_id)){
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
	}	
	$.ajax({
		url:'../../iportal_config/handle_pt_registration.php',
		data:'sel_op=decline&row_id='+row_id,
		type:'POST',
		complete:function(respData){
			var resp = jQuery.parseJSON(respData.responseText);
			if(resp.status=="success"){
				ths_parent.html('<div style="color:#F00;font-weight:bold;">Declined</div>');
			}
		}
	});		
}
function approve_all_operation(indx) {
	var all_id = window.top.$('input#hidd_iportal_approve').val();
	if(typeof(all_id)=="undefined"){all_id=window.top.fmain.hidden_approveIds;}
	var all_id_arr = new Array();
	if(all_id) {
		all_id_arr = all_id.split(',');
		row_id = all_id_arr[indx];
		if(all_id_arr.length>indx) {
			if(row_id != "" && parseInt(row_id)){
				if($('#iportal_approve_'+row_id).text() !="Declined")
				$('#iportal_approve_'+row_id).html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
			}
			$.ajax({
				url:'../../iportal_config/handle_pt_registration.php',
				data:'sel_op=approve&row_id='+row_id,
				type:'POST',
				complete:function(respData){
					var resp = jQuery.parseJSON(respData.responseText);							
					if(resp.status=="success"){
						$('#iportal_approve_'+row_id).html('<div style="color:#090;font-weight:bold;">Approved<br />Patient Id: '+resp.pt_id+'</div>');		
						approve_all_operation(parseInt(indx)+1);
					}
					else if(resp.status=="error"){
						$('#iportal_approve_'+row_id).html('<div style="color:#CC0000;font-weight:bold;"> Error while approving.</div>');				
					}
				}
			});	
		}
		//if(all_id_arr.length == indx)
		//location.reload();		
	}
}

function approve_cl_operation(all_id, mode) {
	if(mode=='approve_all'){
		var all_id = window.top.$('input#hidd_iportal_cl_approve').val();
		if(typeof(all_id)=="undefined"){all_id=window.top.fmain.hidd_iportal_cl_approve;}
	}
	
	var resultLabel='';
	if(mode=='approve' || mode=='approve_all'){resultLabel='Approved';}else{ resultLabel='Declinded';}

	var all_id_arr = new Array();
	if(all_id || mode=='approve' || mode=='decline') {
		if(mode=='approve_all'){
			all_id_arr = all_id.split(',');
		}else{
			all_id_arr[0]=all_id;
		}

		if(all_id_arr.length>0) {
			for(x in all_id_arr){
				orderNum=all_id_arr[x];
				if(top.$('#iportal_cl_approve_'+orderNum).text() !="Declined" && top.$('#iportal_cl_approve_'+orderNum).text()!="Approved")
				top.$('#iportal_cl_approve_'+orderNum).html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
			}
			$.ajax({
				url:'../../iportal_config/approve_cl_orders.php',
				data:'mode='+mode+'&orderNum='+all_id,
				type:'POST',
				success:function(respData){
					resp = jQuery.parseJSON(respData);
					arrResult1=resp.arrResult;
					
					for(x in arrResult1){
						if(arrResult1[x]=='success'){
							top.$('#iportal_cl_approve_'+x).html('<div style="color:#090;font-weight:bold;">Order '+resultLabel+'</div>');		
						}else if(arrResult1[x]=="error"){
							top.$('#iportal_cl_approve_'+x).html('<div style="color:#CC0000;font-weight:bold;"> Error </div>');
						}
						
					}
				}
			});	
		}
	}
}	

// Multi Level Dropdown [Simple Menu]
function set_val_text_sch(ths,menuId,elemId, valTxt){
	if(ths === '' || typeof(ths) === 'undefined'){}else{
		var elem_val = $(ths).parent().find('input').val();
		if(elem_val != '' || typeof(elem_val) != 'undefined'){
			$('#'+elemId+'').val(elem_val).change();
			$('#'+menuId+'').dropdown('toggle');
			//change display value
			if(valTxt && valTxt!='Clear')
			$('#'+menuId+'').html("<a href=\"javascript:void(0)\" class='status_icon '>"+valTxt+"</a>");	
			else
			$('#'+menuId+'').html("<img src='../../library/images/eyeicon1.png' width='20' height='13' alt='Site' title='Site' class='pointer'/>");
		}
	}
}

function ref_management(){
	var url = top.JS_WEB_ROOT_PATH + "/interface/scheduler/referral/index.php";
	top.fmain.location = url;
}

function chk_referral(call_from) {
	
	if( typeof call_from !== 'string') return false;
	
	var tmp_appt_id = parseInt($("#global_context_apptid").val());
	
	var chk = false;
	if( call_from =='proc_sel') {
		var obj1 = $("#sel_proc_id option:selected",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#tmp_provider_id0",top.fmain.document);
	}
	else if( call_from == 'load_front_desk' || call_from == 'load_insurance' ) {
		var obj1 = $("#tmp_provider_id0",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#sel_proc_id option:selected",top.fmain.document);
	}
	if(obj1.length > 0) { if( obj1.data('referral') ) { chk = true;	}}
	
	if( !chk ) {
		if(typeof obj2 == 'object' && obj2.length > 0) {
			if( obj2.data('referral') ) { chk = true;	} 
		}
	}
	$("input[type='checkbox']#sa_ref_management").prop('checked',chk);
	
}

function verification_sheet(){
	var url = top.JS_WEB_ROOT_PATH + "/interface/scheduler/verification/index.php?height:650px";
	top.fmain.location = url;
}

function chk_verif_sheet(call_from) {
	if( typeof call_from !== 'string') return false;
	
	var tmp_appt_id = parseInt($("#global_context_apptid").val());
	var chk = false;
	if( call_from =='proc_sel') {
		var obj1 = $("#sel_proc_id option:selected",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#tmp_provider_id0",top.fmain.document);
	}
	else if( call_from == 'load_front_desk' ) {
		var obj1 = $("#tmp_provider_id0",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#sel_proc_id option:selected",top.fmain.document);
	}

    if(typeof(obj1)!='undefined' && obj1.length > 0) { if( obj1.data('verification') ) { chk = true;	}}
	
	if( !chk ) {
		if(typeof obj2 == 'object' && obj2.length > 0) {
			if( obj2.data('verification') ) { chk = true;	} 
		}
	}
	$("input[type='checkbox']#sa_verification_req").prop('checked',chk);
	
}

function view_only_pt_call(mode){
	top.fAlert("You do not have permission to perform this action.");
	if(mode == 1){
		return false;
	}
}
//need library/js/grid_color/spectrum.js and library/js/grid_color/spectrum.css
function load_color_picker(color)
{
	$(".grid_color_picker").spectrum({
	color: color,
	showInput: true,
	className: "full-spectrum",
	showInitial: true,
	showPalette: true,
	showSelectionPalette: true,
	showAlpha: true,
	maxPaletteSize: 10,
	preferredFormat: "hex",
	localStorageKey: "spectrum.demo",
	move: function (color) {
		//if($.isFunction(updateBorders)) updateBorders(color);
	},
	show: function () {

	},
	beforeShow: function () {

	},
	hide: function (color) {
		//if($.isFunction(updateBorders)) updateBorders(color);
	},

	palette: [
		["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", /*"rgb(153, 153, 153)","rgb(183, 183, 183)",*/
		"rgb(204, 204, 204)", "rgb(217, 217, 217)", /*"rgb(239, 239, 239)", "rgb(243, 243, 243)",*/ "rgb(255, 255, 255)"],
		["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
		"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
		["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
		"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
		"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
		"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
		"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
		"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
		"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
		"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
		/*"rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)",
		"rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)",*/
		"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
		"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
	]
});
}

function check_deceased(){

	if( typeof top.fmain.patientDeceased !== 'undefined' ) {
		if( top.fmain.patientDeceased ) {
			var msg = (top.fmain.pd_alert !== 'undefined' && top.fmain.pd_alert !== '') ? top.fmain.pd_alert : 'Not allowed';
			top.fAlert(msg);
			return true;
		}
	}
	return false;
}

function loadInsHx()
{
	var _modal = $("#InsuranceHx",top.document);
	if( _modal.length > 0 )
	{
		_modal.modal('show');
	}
	else 
	{
		$.ajax({
			url: "../../interface/patient_info/ajax/insurance/act_exp_open_insu_case.php"
		})
		.done(function(resp){
			resp = JSON.parse(resp);
			if(typeof(resp.html)!= "undefined"){
				show_modal('InsuranceHx','Patient All Insurance History', resp.html,'','','modal-lg');
				set_modal_height('InsuranceHx');
			}	
		});
	}

}

function show_scanned(obj,id,val,type){
	var img_src = $(obj).data('src');
	var img_type = $(obj).data('type');
	if( img_type == 'pdf')
		var modal_src = '<object style="width:100%; min-height:500px;" type="application/pdf" data="'+img_src+'"></object>';
	else
		var modal_src = '<img src="'+img_src+'" style="max-width:100%; width:auto; height:auto;" >';
	
	var modal_content = '<div class="row"><div class="col-sm-12">'+modal_src+'</div></div>';
	var modal_footer = '<div class="row"><div class="col-sm-12 text-center"><button class="btn btn-danger" data-dismiss="modal">Close</button></div></div>';
	show_modal('ins_scan_modal','Insurance Scan Documents',modal_content,modal_footer,'400','modal-lg');
	set_modal_height('ins_scan_modal');
	//window.open('show_scan_img_acc.php?id='+id+'&val='+val+'&type='+type,'scan','');
}

//function related appointment action taken from iportal-  approval
function approve_disapprove_appt(row_id,ths,operation_status){
	ths_parent = $(ths).parent().parent();	
	
	if(row_id != "" && parseInt(row_id)){
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
	}	
	$.ajax({
		url:'../../interface/scheduler/appt_cancel_portal_handle.php',
		data:'sel_op='+operation_status+'&row_id='+row_id,
		type:'POST',
		complete:function(respData){
			var resp = jQuery.parseJSON(respData.responseText);
			if(resp.status=="success"){
				var color = '#FF0000';
				if(resp.approval == 'Approved') {
					color = '#090';
				}	
				ths_parent.html('<div style="color:'+color+';font-weight:bold;">'+resp.approval+'<br />Patient Id: '+resp.pt_id+' '+resp.msg+'</div>');				
				$('#cbk'+row_id).prop("disabled", true);
			}
			else if(resp.status=="error"){
				ths_parent.html('<div style="color:#CC0000;font-weight:bold;">Error while approving/declining '+resp.msg+'</div>');				
			}
		}
	});
}

function approve_disapprove_all_appt(operation_status,indx,sel){
	
	cbkObj =  top.document.getElementsByName('cbkPrev');
	var row_id = sub_obj = '';
	var row_id_sub = '';
	var ths = '';
	var sel = sel || false;
	var row_id_arr = new Array();
	for(var a = 0; a < cbkObj.length; a++){
		sub_obj = cbkObj.item(a);
		if(sub_obj.checked == true && sub_obj.disabled==false) {
			row_id_sub = sub_obj.getAttribute('data-id');
			row_id_arr.push(row_id_sub);
			sel=true;
		}
	}
	
	row_id = row_id_arr[indx];
	if(row_id_arr.length>indx) {
		ths = $('#btn_approve_'+row_id);
		ths_parent = $(ths).parent().parent();	
		if(row_id != "" && parseInt(row_id)){
			ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
		}	
		$.ajax({
			url:'../../interface/scheduler/appt_cancel_portal_handle.php',
			data:'sel_op='+operation_status+'&row_id='+row_id,
			type:'POST',
			complete:function(respData){
				var resp = jQuery.parseJSON(respData.responseText);
				if(resp.status=="success"){
					var color = '#FF0000';
					if(resp.approval == 'Approved') {
						color = '#090';
					}	
					ths_parent.html('<div style="color:'+color+';font-weight:bold;">'+resp.approval+'<br />Patient Id: '+resp.pt_id+' '+resp.msg+'</div>');				
					$('#cbk'+row_id).prop("disabled", true);
					
					approve_disapprove_all_appt(operation_status,indx,sel)
				}
				else if(resp.status=="error"){
					ths_parent.html('<div style="color:#CC0000;font-weight:bold;">Error while approving/declining '+resp.msg+'</div>');				
				}
			}
		});
	}
	if(sel==false) {
		top.fAlert("Please select appointment's request(s) from Portal");
	}
}

function pull_cancel_request(apptload) {
	top.show_loading_image("show");
	$.ajax({
		url:'../../interface/scheduler/appt_cancel_portal_handle.php',
		data:'pull_cancel_appt=yes',
		type:'POST',
		complete:function(respData){
			top.show_loading_image("hide");
			var resp = respData.responseText;
			var vl = '';
			//window.top.location.reload();
			if(resp.search('success')>=0 || apptload=='1') { vl = "window.top.location = '../../interface/scheduler/appt_cancel_portal.php';";/*vl = 'window.top.location.reload();'*/}
			top.fAlert(resp,'',vl);
		}
	});
}

function launch_telemedicine()
{
	var parentWidth = parent.document.body.clientWidth;
	var parentheight = parent.document.body.clientHeight;

	top.show_loading_image("show");
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/scheduler/telemedicine_url.php',
		type:'GET',
		dataType: 'json',
		success: function(resp)
		{
			if( typeof resp.ssoUrl !== 'undefined' )
			{
				window.open(resp.ssoUrl, 'telemedicine', 'width='+parseInt(parentWidth-50)+'px,height='+parentheight+'px,');
			}
			else if( typeof resp.message !== 'undefined' )
			{
				top.fAlert(resp.message,'SSO Token Error');
			}
			else
			{
				top.fAlert('Unable to launch Updox telemedicine','Error');
			}
			
		},
		complete: function()
		{
			top.show_loading_image("hide");
		}
	});
}// common.js JavaScript Document   ......
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
}
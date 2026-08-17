<?php 



$wo__ignoreList__javascript = array (
	// general
	'', '/*', '*/', '{}', 			
	
	'json', 'js',
	
	'localhost',
	'media.localhost',
	'lib.localhost',
	'new.localhost',
	'seductiveapps',
	'sa',
	
	'exception', 'msg', 'json', 
	
	'apps', 'php',

	// javascript core 
	'major', 'minor', 'title', 'parseInt', 'parseFloat', 'constructor', 'toExponential', 'toFixed', 'toLocaleString', 'toPrecision', 'toString', 
	'valueOf', 'Boolean', 'Integer', 'Float', 'Number', 'String', 'Object', 'Array', 'Infinity', 'NaN', 'undefined', 
	'decodeURI', 'decodeURIComponent', 'encodeURI', 'encodeURIComponent', 'escape', 'eval', 'isFinite', 'isNaN', 'unescape', 'hasOwnProperty', 
	'createStyleSheet', 'QUOTA_EXCEEDED_ERR', 'arguments', 'callee', 'caller', 
	'undefined', 'null', 'false', 'true', 'undefined', 'instanceof', 'new', 'typeof', 'var', 'string', 'number', 'delete', 'unset', 'prototype', 'throw', 
	'Event', 'Error', 'event', 'preventDefault', 'Infinity', 'Date', 'getDate', 'getTime', 'Array', 'Function', 'Object', 'String', 'Image', 'fromCharCode', 
	'match', 'replace', 'indexOf', 'substr', 'function', 'if', 'else', 'while', 'for', 'as', 'switch', 'case', 'default', 'continue', 'break', 'return', 
	'try', 'catch', 'this', 'length', 'trim', 'append', 'top', 'left', 'width', 'height', 'css', 'documentElement', 'innerHTML', 'src',
	'cookie', 'each', 'alert', 'navigator', 'userAgent', 'console', 'log', 'window', 'push', 'slice', 'concat', 'call', 'apply', 'style', 'color', 'document', 
	'html', 'href', 'createElement', 'attachEvent', 'detachEvent', 'addEventListener', 'removeEventListener', 'debugger', 'Math', 'abs', 'sin', 'asin', 'pow', 
	'sqrt', 'PI', 'parentNode', 'removeChild', 'appendChild', 'target', 'remove', 'DOMParser', 'rgba', 'span', 'text', 'in', 'test', 'extend', 'callee', 
	'caller', 'before', 'random', 'RegExp', 'always', 'progress', 'unbind', 'plugin', 'iframe', 'focus', 'isNaN', 'webgl', 'first', 'title', 'opera', 
	'value'	, 'input', 'swing', 'default', 'delete', 'void', 'with', 'event', 
	'naturalWidth', 'naturalHeight', 'or', 'and', 'use strict', 
	
	// reserved by javascript for future extensions to ecmascript (javascript language)
	'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 
	'static', 
	
	// try..catch.. 
	'stack', 'throw', 'try', 'catch', 'finally', 
	
	
	
	// DOMparser 
	'DOMParser', 'parseFromString', 'async', 'loadXML', 
	
	// XMLHttpRequest 
	'XMLHttpRequest', 'abort', 'getAllResponseHeaders', 'getResponseHeader', 'open', 'send', 'setRequestHeader', 'onreadystatechange', 'readyState', 
	'responseText', 'responseXML', 'status', 'statusText', 
	
	// Boolean Object 
	'toSource', 'toString', 'valueOf', 
	
	// String HTML wrappers 
	'anchor', 'big', 'blink', 'bold', 'fixed', 'fontcolor', 'fontsize', 'italics', 'link', 'small', 'strike', 'sub', 'sup', 

	// Object 
	'constructor', 'length', 'prototype', 'assign', 'create', 'defineProperty', 'defineProperties', 'freeze', 'getOwnPropertyDescriptor', 
	'getOwnPropertyNames', 'getOwnPropertySymbols', 'getPrototypeOf', 'is', 'isExtensible', 'isFrozen', 'isSealed', 'keys', 'observe', 'preventExtensions', 
	'seal', 'setPrototypeOf',
	
	// Array Object 
	'concat', 'indexOf', 'join', 'lastIndexOf', 'pop', 'push', 'reverse', 'shift', 'slice', 'sort', 'splice', 'toString', 'unshift', 'valueOf',

	// String Object 
	'charAt', 'charCodeAt', 'concat', 'fromCharCode', 'indexOf', 'lastIndexOf', 'localeCompare', 'match', 'replace', 'search', 'slice', 'split', 
	'substr', 'substring', 'toLocaleLowerCase', 'toLocaleUpperCase', 'toLowerCase', 'toString', 'toUpperCase', 'trim', 'valueOf',
	
	// Date Object
	'Date', 'getDate', 'getDay', 'getFullYear', 'getHours', 'getMilliseconds', 'getMinutes', 'getMonth', 'getSeconds', 'getTime', 'getTimezoneOffset', 
	'getUTCDate', 'getUTCDay', 'getUTCFullYear', 'getUTCHours', 'getUTCMilliseconds', 'getUTCMinutes', 'getURCMonth', 'getUTCSeconds', 'getYear', 'parse',
	'setDate', 'setFullYear', 'setHours', 'setMilliseconds', 'setMinutes', 'setMonth', 'setSeconds', 'setTime', 'setUTCDate', 'setUTCFullYear', 
	'setUTCHours', 'setUTCMilliseconds', 'setUTCMinutes', 'setUTCMonth', 'setUTCSeconds', 'setYear', 'toDateString', 'toGMTString', 'toISOString', 
	'toJSON', 'toLocaleDateString', 'toLocaleTimeString', 'toLocaleString', 'toString', 'toTimeString', 'toUTCString', 'UTC', 'valueOf', 'locale', 
	
	// Date.locale extension 
		'en', 
			'month_names', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 
			'month_names_short', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 
	
	// Math Object
	'Math', 'LN2', 'LN10', 'LOG2E', 'LOG10E', 'PI', 'SQRT1_2', 'SQRT2', 'abs', 'acos', 'asin', 'atan', 'atan2', 'ceil', 'cos', 'exp', 'floor', 'log', 'max', 'min', 
	'pow', 'random', 'round', 'sin', 'sqrt', 'tan',
	
	// console Object (CHROME) 
	'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'profile', 'profileEnd', 'time', 
	'timeEnd', 'timeStamp', 'trace', 'warn', 'debugger',
	
	// HTMLElement 
	'accessKey', 'addEventListener', 'appendChild', 'attributes', 'blur', 'childElementCount', 'childNodes', 'children', 'classList', 'className', 'click', 
	'clientHeight', 'clientLeft', 'clientTop', 'clientWidth', 'coneNode', 'compareDocumentPosition', 'contains', 'contentEditable', 'dir', 'firstChild', 
	'firstElementChild', 'focus', 'getAttribute', 'getAttributeNode', 'getElementsByClassName', 'getElementsByTagName', 'getFeature', 'hasAttribute', 
	'hasAttributes', 'hasChildNodes', 'id', 'innerHTML', 'insertBefore', 'isContentEditable', 'isDefaultNamespace', 'isEqualNode', 'isSameNode', 'isSupported', 
	'lang', 'lastChild', 'lastElementChild', 'namespaceURI', 'nextSibling', 'nextElementSibling', 'nodeName', 'nodeType', 'nodeValue', 'normalize', 
	'offsetHeight', 'offsetWidth', 'offsetLeft', 'offsetParent', 'offsetTop', 'ownerDocument', 'parentNode', 'parentElement', 'previousSibling', 
	'previousElementSibling', 'querySelector', 'querySelectorAll', 'removeAttribute', 'removeAttributeNode', 'removeChild', 'replaceChild', 
	'removeEventListener', 'scrollHeight', 'scrollLeft', 'scrollTop', 'scrollWidth', 'setAttribute', 'setAttributeNode', 'style', 'tabIndex', 'tagName', 
	'textContent', 'title', 'toString', 'item', 'length',


	// HTML events 
	'onload', 'onclick', 'onmousemove', 'onmouseenter', 'onmouseout', 'onmouseover', 'onmousewheel', 'onwheel', 'oncontextmenu', 'ondblclick', 
	'onmousedown', 'onmouseenter', 'onmouseleave', 'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup', 'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 
	'onload', 'onpageshow', 'onpagehide', 'onresize', 'onscroll', 'onunload', 'onblur', 'onchange', 'onfocus', 'onfocusin', 'onfocusout', 'oninput', 
	'oninvalid', 'onreset', 'onselect', 'onsubmit', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'oncopy', 
	'oncut', 'onpaste', 'onafterprint', 'onbeforeprint', 'onabort', 'oncanplay', 'oncanplaythrough', 'ondurationchange', 'onemptied', 'onended', 'onerror', 
	'onloadeddata', 'onloadedmetadata', 'onloadstart', 'onpause', 'onplay', 'onplaying', 'onprogress', 'onratechange', 'onseeked', 'onseeking', 'onstalled', 
	'onsuspend', 'ontimeupdate', 'onvolumechange', 'onwaiting', 'animationend', 'animationiteration', 'animationstart', 'transitioned', 'onerror', 
	'onmessage', 'onopen', 'onmessage', 'ononline', 'onoffline', 'onpopstate', 'onshow', 'onstorage', 'ontoggle', 'onwheel', 'ontouchcancel', 
	'ontouchend', 'ontouchmove', 'ontouchstart', 'CAPTURING_PHASE', 'AT_TARGET', 'BUBBLING_PHASE', 'touches', 'changedTouches', 
	
	// HTML Event Object 
	'Event', 'bubbles', 'cancelable', 'currentTarget', 'defaultPrevented', 'eventPhase', 'explicitOriginalTarget', 'originalTarget', 'target', 
	'timestamp', 'timeStamp', 'type', 'isTrusted', 'initEvent', 'preventBubble', 'preventCapture', 'preventDefault', 'stopImmediatePropagation', 
	'stopPropagation', 'getPreventDefault', 
	
	// HTML UIEvent 
	'UIEvent', 'cancelBubble', 'detail', 'isChar', 'layerX', 'layerY', 'pageX', 'pageY', 'view', 'which', 'initUIEvent', 
	
	// HTML MouseEvent 
	'MouseEvent', 'altKey', 'button', 'buttons', 'clientX', 'clientY', 'ctrlKey', 'metaKey', 'movementX', 'movementY', 'region', 'relatedTarget', 
	'screenX', 'screenY', 'shiftKey', 'which', 'mozPressure', 'mozInputSource', 'MOZ_SOURCE_UNKNOWN', 'MOZ_SOURCE_MOUSE', 'MOZ_SOURCE_PEN', 
	'MOZ_SOURCE_ERASER', 'MOZ_SOURCE_CURSOR', 'MOZ_SOURCE_TOUCH', 'MOZ_SOURCE_KEYBOARD', 'getModifierState', 'initMouseEvent', 
	
	// HTML WheelEvent 
	'WheelEvent', 'deltaX', 'deltaY', 'deltaZ', 'deltaMode', 'DOM_DELTA_PIXEL', 'DOM_DELTA_LINE', 'DOM_DELTA_PAGE', 'wheelDelta', 
	
	// HTML .style 
	'style', 'css', 'alignContent', 'alignItems', 'alignSelf', 'animation', 'animationDelay', 'animationDirection', 'animationDuration', 
	'animationFillMode', 'animationIterationCount', 'animationName', 'animationTimingFunction', 'animationPlayState', 'background', 'backgroundAttachment', 
	'backgroundColor', 'backgroundImage', 'backgroundPosition', 'backgroundRepeat', 'backgroundClip', 'backgroundOrigin', 'backgroundSize', 
	'backfaceVisibility', 'border', 'borderBottom', 'borderBottomColor', 'borderBottomLeftRadius', 'borderBottomRightRadius', 'borderBottomStyle', 
	'borderBottomWidth', 'borderCollapse', 'borderColor', 'borderImage', 'borderImageOutset', 'borderImageRepeat', 'borderImageSlice', 'borderImageSource', 
	'borderImageWidth', 'borderLeft', 'borderLeftColor', 'borderLeftStyle', 'borderLeftWidth', 'borderRadius', 'borderRight', 'borderRightColor', 
	'borderRightStyle', 'borderRightWidth', 'borderSpacing', 'borderStyle', 'borderTop', 'borderTopColor', 'borderTopLeftRadius', 'borderTopRightRadius', 
	'borderTopStyle', 'borderTopWidth', 'borderWidth', 'bottom', 'boxDecorationBreak', 'boxShadow', 'boxSizing', 'captionSide', 'clear', 'clip', 'color', 
	'columnCount', 'columnFill', 'columnGap', 'columnRule', 'columnRuleColor', 'columnRuleStyle', 'columnRuleWidth', 'columns', 'columnSpan', 
	'columnWidth', 'content', 'counterIncrement', 'counterReset', 'cursor', 'direction', 'display', 'emptyCells', 'flex', 'flexBasis', 'flexDirection', 
	'flexFlow', 'flewGrow', 'flexShrink', 'flexWrap', 'cssFloat', 'font', 'fontFamily', 'fontSize', 'fontStyle', 'fontVariant', 'fontSizeAdjust', 
	'fontStretch', 'hangingPunctuation', 'height', 'hyphens', 'icon', 'imageOrientation', 'justifyContent', 'left', 'letterSpacing', 'lineHeight', 
	'listStyle', 'listStyleImage', 'listStylePosition', 'listStyleType', 'margin', 'marginBottom', 'marginLeft', 'marginRight', 'marginTop', 'maxHeight', 
	'maxWidth', 'minHeight', 'minWidth', 'navDown', 'navIndex', 'navLeft', 'navRight', 'navUp', 'opacity', 'order', 'orphans', 'outline', 'outlineColor', 
	'outlineOffset', 'outlineStyle', 'outlineWidth', 'overflow', 'overflowX', 'overflowY', 'padding', 'paddingBottom', 'paddingLeft', 'paddingRight', 
	'paddingTop', 'pageBreakAfter', 'pageBreakBefore', 'pageBreakInside', 'perspective', 'perspectiveOrigin', 'position', 'quotes', 'resize', 'right', 
	'tableLayout', 'tabSize', 'textAlign', 'textAlignLast', 'textDecoration', 'textDecorationColor', 'textDecorationLine', 'textDecorationStyle', 'textIndent', 
	'textJustify', 'textOverflow', 'textShadow', 'textTransform' ,'top', 'transform', 'transformOrigin', 'transformStyle', 'transition', 'transitionProperty', 
	'transitionDuration', 'transitionTimingFunction', 'transitionDelay', 'unicodeBidi', 'verticalAlign', 'visibility', 'whiteSpace', 'width', 'wordBreak', 
	'wordSpacing', 'wordWrap', 'widows', 'zIndex', 'px', 'em', 
	
	// Navigator Object 
	'navigator', 'appCodeName', 'appName', 'appVersion', 'cookieEnabled', 'geolocation', 'language', 'onLine', 'platform', 'product', 'userAgent', 
	'javaEnabled', 'taintEnabled',
	
	// Screen Object 
	'Screen', 'availHeight', 'availWidth', 'colorDepth', 'height', 'pixelDepth', 'width', 
	
	// History Object (BROWSER) 
	'length', 'back', 'forward', 'go', 
	
	// Location Object 
	'hash', 'host', 'hostname', 'href', 'origin', 'pathname', 'port', 'protocol', 'search', 'assign', 'reload', 'replace', 			
	
	// Window Object 
	'closed', 'defaultStatus', 'document', 'frameElement', 'frames', 'history', 'innerHeight', 'innerWidth', 'length', 'location', 'name', 
	'navigator', 'opener', 'outerHeight', 'outerWidth', 'pageXOffset', 'pageYOffset', 'parent', 'screen', 'screenLeft', 'screenTop', 'screenX', 'screenY', 
	'scrollX', 'scrollY', 'self', 'status', 'top', 'alert', 'atob', 'blur', 'btoa', 'clearInterval', 'clearTimeout', 'close', 'confirm', 'createPopup', 
	'focus', 'moveBy', 'moveTo', 'open', 'print', 'prompt', 'resizeBy', 'resizeTo', 'scroll', 'scrollBy', 'scrollTo', 'setInterval', 'setTimeout', 'stop',
	
	// Document Object 
	'activeElement', 'addEventListener', 'adoptNode', 'anchors', 'applets', 'baseURI', 'body', 'close', 'cookie', 'createAttribute', 'createComment', 
	'createDocumentFragment', 'createElement', 'createTextNode', 'doctype', 'documentElement', 'documentMode', 'documentURI', 'domain', 'domConfig', 
	'embeds', 'forms', 'getElementById', 'getElementsByClassName', 'getElementsByName', 'getElementsByTagName', 'hasFocus', 'head', 'images', 
	'implementation', 'importNode', 'inputEncoding', 'lastModified', 'links', 'normalize', 'normalizeDocument', 'open', 'querySelector', 
	'querySelectorAll', 'readyState', 'referrer', 'removeEventListener', 'renameNode', 'scripts', 'strictErrorChecking', 'title', 'URL', 'write', 
	'writeln', 'attributes', 'hasAttributes', 'nextSibling', 'nodeName', 'nodeType', 'nodeValue', 'ownerDocument', 'ownerElement', 'parentNode', 
	'previousSibling', 'textContent',
	
	// RegExp Object 
	'constructor', 'global', 'ignoreCase', 'lastIndex', 'multiline', 'source', 'compile', 'exec', 'test', 'toString'
	
); 

$wo__ignoreList__javascript = array_unique($wo__ignoreList__javascript);
global $wo__ignoreList__javascript;

?>

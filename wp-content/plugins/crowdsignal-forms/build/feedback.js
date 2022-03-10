(function(e, a) { for(var i in a) e[i] = a[i]; }(window, /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/feedback.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/blocks/feedback/constants.js":
/*!*********************************************!*\
  !*** ./client/blocks/feedback/constants.js ***!
  \*********************************************/
/*! exports provided: views, FeedbackStatus, FeedbackToggleMode */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"views\", function() { return views; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"FeedbackStatus\", function() { return FeedbackStatus; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"FeedbackToggleMode\", function() { return FeedbackToggleMode; });\nvar views = {\n  QUESTION: 'question',\n  SUBMIT: 'submit'\n};\nvar FeedbackStatus = Object.freeze({\n  OPEN: 'open',\n  CLOSED: 'closed',\n  CLOSED_AFTER: 'closed-after'\n});\nvar FeedbackToggleMode = Object.freeze({\n  CLICK: 'click',\n  HOVER: 'hover',\n  PAGE_LOAD: 'load'\n});\n\n//# sourceURL=webpack:///./client/blocks/feedback/constants.js?");

/***/ }),

/***/ "./client/blocks/feedback/util.js":
/*!****************************************!*\
  !*** ./client/blocks/feedback/util.js ***!
  \****************************************/
/*! exports provided: getStyleVars, isWidgetEditor */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getStyleVars\", function() { return getStyleVars; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isWidgetEditor\", function() { return isWidgetEditor; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * External dependencies\n */\n\nvar getStyleVars = function getStyleVars(attributes, fallbackStyles) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"mapKeys\"])({\n    backgroundColor: attributes.backgroundColor || '#ffffff',\n    buttonColor: attributes.buttonColor || fallbackStyles.accentColor,\n    buttonTextColor: attributes.buttonTextColor || fallbackStyles.textColorInverted,\n    textColor: attributes.textColor || fallbackStyles.textColor,\n    textSize: fallbackStyles.textSize,\n    triggerBackgroundColor: attributes.triggerBackgroundColor || fallbackStyles.accentColor,\n    triggerTextColor: attributes.triggerTextColor || fallbackStyles.textColorInverted\n  }, function (_, key) {\n    return \"--crowdsignal-forms-\".concat(Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"kebabCase\"])(key));\n  });\n};\nvar isWidgetEditor = function isWidgetEditor() {\n  return !!window.wp.widgets;\n};\n\n//# sourceURL=webpack:///./client/blocks/feedback/util.js?");

/***/ }),

/***/ "./client/components/feedback/form.js":
/*!********************************************!*\
  !*** ./client/components/feedback/form.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ \"@babel/runtime/regenerator\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ \"./node_modules/@babel/runtime/helpers/slicedToArray.js\");\n/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! classnames */ \"./node_modules/classnames/index.js\");\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/block-editor */ \"@wordpress/block-editor\");\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var data_feedback__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! data/feedback */ \"./client/data/feedback/index.js\");\n\n\n\n\n\n/**\n * External dependencies\n */\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\nvar FeedbackForm = function FeedbackForm(_ref) {\n  var attributes = _ref.attributes,\n      onSubmit = _ref.onSubmit;\n\n  var _useState = Object(react__WEBPACK_IMPORTED_MODULE_4__[\"useState\"])(''),\n      _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState, 2),\n      feedback = _useState2[0],\n      setFeedback = _useState2[1];\n\n  var _useState3 = Object(react__WEBPACK_IMPORTED_MODULE_4__[\"useState\"])(''),\n      _useState4 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState3, 2),\n      email = _useState4[0],\n      setEmail = _useState4[1];\n\n  var _useState5 = Object(react__WEBPACK_IMPORTED_MODULE_4__[\"useState\"])({}),\n      _useState6 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState5, 2),\n      errors = _useState6[0],\n      setErrors = _useState6[1];\n\n  var handleSubmit = /*#__PURE__*/function () {\n    var _ref2 = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee(event) {\n      var validation;\n      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {\n        while (1) {\n          switch (_context.prev = _context.next) {\n            case 0:\n              event.preventDefault();\n              validation = {\n                feedback: Object(lodash__WEBPACK_IMPORTED_MODULE_5__[\"isEmpty\"])(feedback),\n                email: attributes.emailRequired && (Object(lodash__WEBPACK_IMPORTED_MODULE_5__[\"isEmpty\"])(email) || email.match(/^\\s+@\\s+$/))\n              };\n              setErrors(validation);\n\n              if (!(validation.feedback || validation.email)) {\n                _context.next = 5;\n                break;\n              }\n\n              return _context.abrupt(\"return\");\n\n            case 5:\n              Object(data_feedback__WEBPACK_IMPORTED_MODULE_9__[\"updateFeedbackResponse\"])(attributes.surveyId, {\n                nonce: attributes.nonce,\n                feedback: feedback,\n                email: email\n              });\n              onSubmit();\n\n            case 7:\n            case \"end\":\n              return _context.stop();\n          }\n        }\n      }, _callee);\n    }));\n\n    return function handleSubmit(_x) {\n      return _ref2.apply(this, arguments);\n    };\n  }();\n\n  var feedbackClasses = classnames__WEBPACK_IMPORTED_MODULE_6___default()('crowdsignal-forms-feedback__input', {\n    'is-error': errors.feedback\n  });\n  var emailClasses = classnames__WEBPACK_IMPORTED_MODULE_6___default()('crowdsignal-forms-feedback__input', {\n    'is-error': errors.email\n  });\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__[\"createElement\"])(\"form\", {\n    onSubmit: handleSubmit\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__[\"createElement\"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_7__[\"RichText\"].Content, {\n    tagName: \"h3\",\n    className: \"crowdsignal-forms-feedback__header\",\n    value: attributes.header\n  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__[\"TextareaControl\"], {\n    className: feedbackClasses,\n    rows: 6,\n    placeholder: attributes.feedbackPlaceholder,\n    value: feedback,\n    onChange: setFeedback\n  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__[\"TextControl\"], {\n    className: emailClasses,\n    placeholder: attributes.emailPlaceholder,\n    value: email,\n    onChange: setEmail\n  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__[\"createElement\"])(\"div\", {\n    className: \"wp-block-button crowdsignal-forms-feedback__button-wrapper\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__[\"createElement\"])(\"button\", {\n    className: \"wp-block-button__link crowdsignal-forms-feedback__feedback-button\",\n    type: \"submit\"\n  }, attributes.submitButtonLabel)));\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (FeedbackForm);\n\n//# sourceURL=webpack:///./client/components/feedback/form.js?");

/***/ }),

/***/ "./client/components/feedback/index.js":
/*!*********************************************!*\
  !*** ./client/components/feedback/index.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ \"./node_modules/@babel/runtime/helpers/slicedToArray.js\");\n/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ \"./node_modules/@babel/runtime/helpers/defineProperty.js\");\n/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! classnames */ \"./node_modules/classnames/index.js\");\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var blocks_feedback_util__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! blocks/feedback/util */ \"./client/blocks/feedback/util.js\");\n/* harmony import */ var components_with_fallback_styles__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! components/with-fallback-styles */ \"./client/components/with-fallback-styles/index.js\");\n/* harmony import */ var _toggle__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./toggle */ \"./client/components/feedback/toggle.js\");\n/* harmony import */ var _popover__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./popover */ \"./client/components/feedback/popover.js\");\n/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./util */ \"./client/components/feedback/util.js\");\n\n\n\n\nfunction ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }\n\nfunction _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }\n\n/**\n * External dependencies\n */\n\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\n\n\n\nvar getPopoverPosition = function getPopoverPosition(x, y) {\n  if (y !== 'center') {\n    return '';\n  }\n\n  return x === 'left' ? 'middle right' : 'middle left';\n};\n\nvar adjustFrameOffset = function adjustFrameOffset(position, verticalAlign, width, height) {\n  if (verticalAlign !== 'center') {\n    return position;\n  }\n\n  return _objectSpread({}, position, {\n    left: position.left !== null ? position.left - width + height : null,\n    right: position.right !== null ? position.right - width + height : null\n  });\n};\n\nvar Feedback = function Feedback(_ref) {\n  var attributes = _ref.attributes,\n      fallbackStyles = _ref.fallbackStyles,\n      renderStyleProbe = _ref.renderStyleProbe;\n\n  var _useState = Object(react__WEBPACK_IMPORTED_MODULE_3__[\"useState\"])({}),\n      _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),\n      position = _useState2[0],\n      setPosition = _useState2[1];\n\n  var toggle = Object(react__WEBPACK_IMPORTED_MODULE_3__[\"useRef\"])(null);\n  var updatePosition = Object(react__WEBPACK_IMPORTED_MODULE_3__[\"useCallback\"])(function () {\n    setPosition(adjustFrameOffset(Object(_util__WEBPACK_IMPORTED_MODULE_10__[\"getFeedbackButtonPosition\"])(attributes.x, attributes.y, toggle.current.offsetWidth, attributes.y === 'center' ? toggle.current.offsetWidth : toggle.current.offsetHeight, {\n      top: 20,\n      bottom: 20,\n      left: attributes.y === 'center' ? 0 : 20,\n      right: attributes.y === 'center' ? 0 : 20\n    }, document.body), attributes.y, toggle.current.offsetWidth, toggle.current.offsetHeight));\n  }, [attributes.x, attributes.y, toggle.current]);\n  Object(react__WEBPACK_IMPORTED_MODULE_3__[\"useLayoutEffect\"])(function () {\n    updatePosition();\n  }, [attributes.x, attributes.y, updatePosition]);\n  var classes = classnames__WEBPACK_IMPORTED_MODULE_4___default()('crowdsignal-forms-feedback', \"align-\".concat(attributes.x), {\n    'no-shadow': attributes.hideTriggerShadow,\n    'is-vertical': attributes.y === 'center'\n  });\n\n  var styles = _objectSpread({}, position, {}, Object(blocks_feedback_util__WEBPACK_IMPORTED_MODULE_6__[\"getStyleVars\"])(attributes, fallbackStyles));\n\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"Fragment\"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"div\", {\n    className: classes,\n    style: styles\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"Dropdown\"], {\n    popoverProps: {\n      className: 'crowdsignal-forms-feedback__popover-wrapper',\n      position: getPopoverPosition(attributes.x, attributes.y)\n    },\n    renderToggle: function renderToggle(_ref2) {\n      var isOpen = _ref2.isOpen,\n          onToggle = _ref2.onToggle;\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(_toggle__WEBPACK_IMPORTED_MODULE_8__[\"default\"], {\n        ref: toggle,\n        isOpen: isOpen,\n        onClick: onToggle,\n        onToggle: updatePosition,\n        attributes: attributes\n      });\n    },\n    renderContent: function renderContent() {\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(_popover__WEBPACK_IMPORTED_MODULE_9__[\"default\"], {\n        attributes: attributes\n      });\n    }\n  })), renderStyleProbe());\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(components_with_fallback_styles__WEBPACK_IMPORTED_MODULE_7__[\"withFallbackStyles\"])(Feedback));\n\n//# sourceURL=webpack:///./client/components/feedback/index.js?");

/***/ }),

/***/ "./client/components/feedback/popover.js":
/*!***********************************************!*\
  !*** ./client/components/feedback/popover.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ \"./node_modules/@babel/runtime/helpers/slicedToArray.js\");\n/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! blocks/feedback/constants */ \"./client/blocks/feedback/constants.js\");\n/* harmony import */ var _form__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./form */ \"./client/components/feedback/form.js\");\n/* harmony import */ var _submit__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./submit */ \"./client/components/feedback/submit.js\");\n/* harmony import */ var components_footer_branding__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! components/footer-branding */ \"./client/components/footer-branding/index.js\");\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\n\n\nvar FeedbackPopover = function FeedbackPopover(_ref) {\n  var attributes = _ref.attributes;\n\n  var _useState = Object(react__WEBPACK_IMPORTED_MODULE_2__[\"useState\"])(blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_4__[\"views\"].QUESTION),\n      _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),\n      view = _useState2[0],\n      setView = _useState2[1];\n\n  var _useState3 = Object(react__WEBPACK_IMPORTED_MODULE_2__[\"useState\"])('auto'),\n      _useState4 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState3, 2),\n      height = _useState4[0],\n      setHeight = _useState4[1];\n\n  var popover = Object(react__WEBPACK_IMPORTED_MODULE_2__[\"useRef\"])(null);\n\n  var handleSubmit = function handleSubmit() {\n    setHeight(popover.current.offsetHeight);\n    setView(blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_4__[\"views\"].SUBMIT);\n  };\n\n  var styles = {\n    height: height\n  };\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createElement\"])(\"div\", {\n    ref: popover,\n    className: \"crowdsignal-forms-feedback__popover\",\n    style: styles\n  }, view === blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_4__[\"views\"].QUESTION && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createElement\"])(_form__WEBPACK_IMPORTED_MODULE_5__[\"default\"], {\n    attributes: attributes,\n    onSubmit: handleSubmit\n  }), view === blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_4__[\"views\"].SUBMIT && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createElement\"])(_submit__WEBPACK_IMPORTED_MODULE_6__[\"default\"], {\n    attributes: attributes\n  }), !attributes.hideBranding && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createElement\"])(components_footer_branding__WEBPACK_IMPORTED_MODULE_7__[\"default\"], {\n    trackRef: \"cs-forms-feedback\",\n    showLogo: view === blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_4__[\"views\"].SUBMIT,\n    message: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Collect your own feedback with Crowdsignal', 'crowdsignal-forms')\n  }));\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (FeedbackPopover);\n\n//# sourceURL=webpack:///./client/components/feedback/popover.js?");

/***/ }),

/***/ "./client/components/feedback/submit.js":
/*!**********************************************!*\
  !*** ./client/components/feedback/submit.js ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ \"@wordpress/block-editor\");\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);\n\n\n/**\n * External dependencies\n */\n\n/**\n * Wordpress dependencies\n */\n\n\n\nvar FeedbackSubmit = function FeedbackSubmit(_ref) {\n  var attributes = _ref.attributes;\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__[\"RichText\"].Content, {\n    tagName: \"h3\",\n    className: \"crowdsignal-forms-feedback__header\",\n    value: attributes.submitText\n  });\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (FeedbackSubmit);\n\n//# sourceURL=webpack:///./client/components/feedback/submit.js?");

/***/ }),

/***/ "./client/components/feedback/toggle.js":
/*!**********************************************!*\
  !*** ./client/components/feedback/toggle.js ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ \"./node_modules/classnames/index.js\");\n/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ \"@wordpress/block-editor\");\n/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var components_icon_close_small__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! components/icon/close-small */ \"./client/components/icon/close-small.js\");\n/* harmony import */ var blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! blocks/feedback/constants */ \"./client/blocks/feedback/constants.js\");\n\n\n/**\n * External dependencies\n */\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\nvar FeedbackToggle = function FeedbackToggle(_ref, ref) {\n  var attributes = _ref.attributes,\n      className = _ref.className,\n      isOpen = _ref.isOpen,\n      onClick = _ref.onClick,\n      onToggle = _ref.onToggle;\n  Object(react__WEBPACK_IMPORTED_MODULE_1__[\"useLayoutEffect\"])(onToggle, [isOpen]);\n  Object(react__WEBPACK_IMPORTED_MODULE_1__[\"useEffect\"])(function () {\n    if (isOpen || attributes.toggleOn !== blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_6__[\"FeedbackToggleMode\"].PAGE_LOAD) {\n      return;\n    }\n\n    onClick();\n  }, []);\n  var handleHover = Object(react__WEBPACK_IMPORTED_MODULE_1__[\"useCallback\"])(function () {\n    if (isOpen || attributes.toggleOn !== blocks_feedback_constants__WEBPACK_IMPORTED_MODULE_6__[\"FeedbackToggleMode\"].HOVER) {\n      return;\n    }\n\n    onClick();\n  }, [attributes.toggleOn, isOpen]);\n  var classes = classnames__WEBPACK_IMPORTED_MODULE_2___default()('crowdsignal-forms-feedback__trigger', 'wp-block-button__link', className, {\n    'is-active': isOpen\n  });\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"div\", {\n    className: \"wp-block-button crowdsignal-forms-feedback__trigger-wrapper\"\n  }, !isOpen && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"button\", {\n    ref: ref,\n    className: classes,\n    onClick: onClick,\n    onMouseEnter: handleHover\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__[\"RichText\"].Content, {\n    className: \"crowdsignal-forms-feedback__trigger-text\",\n    value: attributes.triggerLabel\n  })), isOpen && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"button\", {\n    ref: ref,\n    className: classes,\n    onClick: onClick\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(components_icon_close_small__WEBPACK_IMPORTED_MODULE_5__[\"default\"], null), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Close', 'crowdsignal-forms')));\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(react__WEBPACK_IMPORTED_MODULE_1__[\"forwardRef\"])(FeedbackToggle));\n\n//# sourceURL=webpack:///./client/components/feedback/toggle.js?");

/***/ }),

/***/ "./client/components/feedback/util.js":
/*!********************************************!*\
  !*** ./client/components/feedback/util.js ***!
  \********************************************/
/*! exports provided: getFeedbackButtonPosition */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getFeedbackButtonPosition\", function() { return getFeedbackButtonPosition; });\n/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ \"./node_modules/@babel/runtime/helpers/defineProperty.js\");\n/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);\n\n\nfunction ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }\n\nfunction _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }\n\n/**\n * External dependencies\n */\n\n\nvar addFrameOffsets = function addFrameOffsets(offset, frame) {\n  return {\n    left: offset.left + frame.x + window.scrollX,\n    right: offset.right + (window.innerWidth > frame.left + frame.width ? window.innerWidth - frame.left - frame.width : 0),\n    top: offset.top + frame.y + window.scrollY,\n    bottom: offset.bottom + (window.innerHeight > frame.top + frame.height ? window.innerHeight - frame.top - frame.height : 0)\n  };\n};\n\nvar getFeedbackButtonHorizontalPosition = function getFeedbackButtonHorizontalPosition(align, width, offset) {\n  return {\n    left: align === 'left' ? offset.left : null,\n    right: align === 'right' ? offset.right : null\n  };\n};\n\nvar getFeedbackButtonVerticalPosition = function getFeedbackButtonVerticalPosition(verticalAlign, height, offset) {\n  if (verticalAlign === 'center') {\n    return {\n      top: (window.innerHeight - height) / 2,\n      bottom: null\n    };\n  }\n\n  return {\n    top: verticalAlign === 'top' ? offset.top : null,\n    bottom: verticalAlign === 'bottom' ? offset.bottom : null\n  };\n};\n\nvar getFeedbackButtonPosition = function getFeedbackButtonPosition(align, verticalAlign, width, height, padding) {\n  var frameElement = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : null;\n  var offset = {\n    left: Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isObject\"])(padding) ? padding.left : padding,\n    right: Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isObject\"])(padding) ? padding.right : padding,\n    top: Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isObject\"])(padding) ? padding.top : padding,\n    bottom: Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isObject\"])(padding) ? padding.bottom : padding\n  };\n\n  if (frameElement) {\n    offset = addFrameOffsets(offset, frameElement.getBoundingClientRect());\n  }\n\n  return _objectSpread({}, getFeedbackButtonHorizontalPosition(align, width, offset), {}, getFeedbackButtonVerticalPosition(verticalAlign, height, offset));\n};\n\n//# sourceURL=webpack:///./client/components/feedback/util.js?");

/***/ }),

/***/ "./client/components/footer-branding/index.js":
/*!****************************************************!*\
  !*** ./client/components/footer-branding/index.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);\n\n\n/**\n * WordPress dependencies\n */\n\n\nvar promoteLink = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"span\", null, \"Hide Crowdsignal ads\", Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"br\", null), \"and get unlimited\", Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"br\", null), \"signals -\", ' ', Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"a\", {\n  href: \"https://crowdsignal.com/pricing\",\n  target: \"_blank\",\n  rel: \"noopener noreferrer\"\n}, \"Upgrade\"));\n\nvar FooterBranding = function FooterBranding(_ref) {\n  var showLogo = _ref.showLogo,\n      editing = _ref.editing,\n      message = _ref.message,\n      _ref$trackRef = _ref.trackRef,\n      trackRef = _ref$trackRef === void 0 ? 'cs-forms-poll' : _ref$trackRef;\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"div\", {\n    className: \"crowdsignal-forms__footer-branding\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"a\", {\n    className: \"crowdsignal-forms__footer-cs-link\",\n    href: 'https://crowdsignal.com?ref=' + trackRef,\n    target: \"_blank\",\n    rel: \"noopener noreferrer\"\n  }, message || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Create your own poll with Crowdsignal', 'crowdsignal-forms')), editing && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__[\"Tooltip\"], {\n    text: promoteLink,\n    position: \"top center\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"a\", {\n    href: \"https://crowdsignal.com/pricing\",\n    target: \"_blank\",\n    rel: \"noopener noreferrer\",\n    className: \"crowdsignal-forms__branding-promote\"\n  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Hide', 'crowdsignal-forms'))), showLogo && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"a\", {\n    href: 'https://crowdsignal.com?ref=' + trackRef,\n    target: \"_blank\",\n    rel: \"noopener noreferrer\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"img\", {\n    className: \"crowdsignal-forms__footer-branding-logo\",\n    src: \"https://app.crowdsignal.com/images/svg/cs-logo-dots.svg\",\n    alt: \"Crowdsignal sticker\"\n  })));\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (FooterBranding);\n\n//# sourceURL=webpack:///./client/components/footer-branding/index.js?");

/***/ }),

/***/ "./client/components/icon/close-small.js":
/*!***********************************************!*\
  !*** ./client/components/icon/close-small.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (function () {\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"svg\", {\n    width: \"24\",\n    height: \"24\",\n    viewBox: \"0 0 24 24\",\n    fill: \"none\",\n    xmlns: \"http://www.w3.org/2000/svg\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"path\", {\n    d: \"M17.7049 7.70504L16.2949 6.29504L11.9999 10.59L7.70492 6.29504L6.29492 7.70504L10.5899 12L6.29492 16.295L7.70492 17.705L11.9999 13.41L16.2949 17.705L17.7049 16.295L13.4099 12L17.7049 7.70504Z\"\n  }));\n});\n\n//# sourceURL=webpack:///./client/components/icon/close-small.js?");

/***/ }),

/***/ "./client/components/with-fallback-styles/index.js":
/*!*********************************************************!*\
  !*** ./client/components/with-fallback-styles/index.js ***!
  \*********************************************************/
/*! exports provided: withFallbackStyles */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"withFallbackStyles\", function() { return withFallbackStyles; });\n/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/extends */ \"./node_modules/@babel/runtime/helpers/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/objectWithoutProperties.js\");\n/* harmony import */ var _babel_runtime_helpers_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./util */ \"./client/components/with-fallback-styles/util.js\");\n\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n\nvar StyleProbe = function StyleProbe() {\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"div\", {\n    className: \"crowdsignal-forms__style-probe\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"p\", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"h3\", null, \"Text\"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"div\", {\n    className: \"wp-block-button\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"div\", {\n    className: \"wp-block-button__link\"\n  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"div\", {\n    className: \"entry-content\"\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(\"div\", {\n    className: \"alignwide\"\n  })));\n};\n\nvar getStyles = function getStyles(node) {\n  if (null === node) {\n    return {};\n  }\n\n  var buttonNode = node.querySelector('.wp-block-button__link');\n  var textNode = node.querySelector('p');\n  var h3Node = node.querySelector('h3');\n  var wideContentNode = node.querySelector('.alignwide');\n  var accentColor = Object(_util__WEBPACK_IMPORTED_MODULE_5__[\"getBackgroundColor\"])(buttonNode);\n  var backgroundColor = Object(_util__WEBPACK_IMPORTED_MODULE_5__[\"getBackgroundColor\"])(textNode);\n  var textColor = window.getComputedStyle(textNode).color; // Ensure that we don't end up with the same color for surface and accent.\n  // Falls back to button border color, then text color.\n\n  if (accentColor === backgroundColor) {\n    var borderColor = Object(_util__WEBPACK_IMPORTED_MODULE_5__[\"getBorderColor\"])(buttonNode);\n    accentColor = borderColor ? borderColor : textColor;\n  }\n\n  return {\n    accentColor: accentColor,\n    backgroundColor: backgroundColor,\n    textColor: textColor,\n    textColorInverted: window.getComputedStyle(buttonNode).color,\n    textFont: window.getComputedStyle(textNode).fontFamily,\n    textSize: window.getComputedStyle(textNode).fontSize,\n    headingFont: window.getComputedStyle(h3Node).fontFamily,\n    contentWideWidth: window.getComputedStyle(wideContentNode).maxWidth\n  };\n};\n\nvar withFallbackStyles = function withFallbackStyles(WrappedComponent) {\n  var getFallbackStyles = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__[\"withFallbackStyles\"])(function (node) {\n    return {\n      fallbackStyles: getStyles(node.querySelector('.crowdsignal-forms__style-probe'))\n    };\n  });\n  return getFallbackStyles(function (_ref) {\n    var fallbackStyles = _ref.fallbackStyles,\n        props = _babel_runtime_helpers_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1___default()(_ref, [\"fallbackStyles\"]);\n\n    var renderProbe = function renderProbe() {\n      if (fallbackStyles) {\n        return null;\n      }\n\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(StyleProbe, null);\n    };\n\n    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__[\"createElement\"])(WrappedComponent, _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0___default()({\n      fallbackStyles: fallbackStyles || {},\n      renderStyleProbe: renderProbe\n    }, props));\n  });\n};\n\n//# sourceURL=webpack:///./client/components/with-fallback-styles/index.js?");

/***/ }),

/***/ "./client/components/with-fallback-styles/util.js":
/*!********************************************************!*\
  !*** ./client/components/with-fallback-styles/util.js ***!
  \********************************************************/
/*! exports provided: getBackgroundColor, getBorderColor */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getBackgroundColor\", function() { return getBackgroundColor; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getBorderColor\", function() { return getBorderColor; });\n/**\n * Traverses the parent chain of the given node to get a 'best guess' of\n * what the background color is if the provided node has a transparent background.\n * Algorithm for traversing parent chain \"borrowed\" from\n * https://github.com/WordPress/gutenberg/blob/0c6e369/packages/block-editor/src/components/colors/use-colors.js#L201-L216\n *\n * @param  {Element} backgroundColorNode The element to check for background color\n * @return {string}  The background colour of the node\n */\nvar getBackgroundColor = function getBackgroundColor(backgroundColorNode) {\n  var backgroundColor = window.getComputedStyle(backgroundColorNode).backgroundColor;\n\n  while (backgroundColor === 'rgba(0, 0, 0, 0)' && backgroundColorNode.parentNode && backgroundColorNode.parentNode.nodeType === window.Node.ELEMENT_NODE) {\n    backgroundColorNode = backgroundColorNode.parentNode;\n    backgroundColor = window.getComputedStyle(backgroundColorNode).backgroundColor;\n  }\n\n  return backgroundColor;\n};\n/**\n * Gets the border color for a node, if it appears valid.\n * If we get '0px' for the width, then we likely don't have a border and return null.\n * We use 'borderBlockStartWidth' because of FF: https://bugzilla.mozilla.org/show_bug.cgi?id=137688\n *\n * @param {Element} borderNode The element to check for a border color\n * @return {string|null} The border colour value of null if invalid\n */\n\nvar getBorderColor = function getBorderColor(borderNode) {\n  var borderWidth = window.getComputedStyle(borderNode).borderBlockStartWidth;\n  return borderWidth !== '0px' ? window.getComputedStyle(borderNode).borderBlockStartColor : null;\n};\n\n//# sourceURL=webpack:///./client/components/with-fallback-styles/util.js?");

/***/ }),

/***/ "./client/data/feedback/index.js":
/*!***************************************!*\
  !*** ./client/data/feedback/index.js ***!
  \***************************************/
/*! exports provided: updateFeedbackResponse */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"updateFeedbackResponse\", function() { return updateFeedbackResponse; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _crowdsignalForms_apifetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @crowdsignalForms/apifetch */ \"@crowdsignalForms/apifetch\");\n/* harmony import */ var _crowdsignalForms_apifetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_crowdsignalForms_apifetch__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var data_util__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! data/util */ \"./client/data/util.js\");\n/**\n * External dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\nvar updateFeedbackResponse = function updateFeedbackResponse(surveyId, data) {\n  return Object(data_util__WEBPACK_IMPORTED_MODULE_2__[\"withRequestTimeout\"])(_crowdsignalForms_apifetch__WEBPACK_IMPORTED_MODULE_1___default()({\n    path: Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"trimEnd\"])(\"/crowdsignal-forms/v1/feedback/\".concat(surveyId || '', \"/response\")),\n    method: 'POST',\n    data: data\n  }));\n};\n\n//# sourceURL=webpack:///./client/data/feedback/index.js?");

/***/ }),

/***/ "./client/data/util.js":
/*!*****************************!*\
  !*** ./client/data/util.js ***!
  \*****************************/
/*! exports provided: withRequestTimeout */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"withRequestTimeout\", function() { return withRequestTimeout; });\nvar WP_API_REQUEST_TIMEOUT = 10000;\n/**\n * Wraps a promise in a timeout that will reject\n * when it fails to complite within given time.\n *\n * @param  {Promise} promise Promise\n * @return {Promise}         Promise wrapped in a request timeout\n */\n\nvar withRequestTimeout = function withRequestTimeout(promise) {\n  return new Promise(function (resolve, reject) {\n    var timer = setTimeout(function () {\n      return reject(new Error('Request timed out'));\n    }, WP_API_REQUEST_TIMEOUT);\n    promise.then(resolve, reject).finally(function () {\n      return clearTimeout(timer);\n    });\n  });\n};\n\n//# sourceURL=webpack:///./client/data/util.js?");

/***/ }),

/***/ "./client/feedback.js":
/*!****************************!*\
  !*** ./client/feedback.js ***!
  \****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ \"react\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var components_feedback__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! components/feedback */ \"./client/components/feedback/index.js\");\n/* harmony import */ var lib_mutation_observer__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lib/mutation-observer */ \"./client/lib/mutation-observer/index.js\");\n\n\n/**\n * External dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\nObject(lib_mutation_observer__WEBPACK_IMPORTED_MODULE_3__[\"default\"])('data-crowdsignal-feedback', function (attributes) {\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(components_feedback__WEBPACK_IMPORTED_MODULE_2__[\"default\"], {\n    attributes: attributes\n  });\n});\n\n//# sourceURL=webpack:///./client/feedback.js?");

/***/ }),

/***/ "./client/lib/mutation-observer/index.js":
/*!***********************************************!*\
  !*** ./client/lib/mutation-observer/index.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-dom */ \"react-dom\");\n/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);\n/**\n * External dependencies\n */\n\n\n\nvar MutationObserver = function MutationObserver(dataAttributeName, blockBuilder) {\n  if ('complete' === document.readyState) {\n    return blockObserver(dataAttributeName, blockBuilder);\n  }\n\n  window.addEventListener('load', function () {\n    return blockObserver(dataAttributeName, blockBuilder);\n  });\n};\n\nvar initBlocks = function initBlocks(dataAttributeName, blockBuilder) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"forEach\"])(document.querySelectorAll(\"div[\".concat(dataAttributeName, \"]\")), function (element) {\n    // Try-catch potentially prevents other blocks from breaking\n    // when there's more then one on the page\n    try {\n      var attributes = JSON.parse(element.dataset[Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"camelCase\"])(dataAttributeName.substr('data-'.length))]);\n      var block = blockBuilder(attributes, element);\n      element.removeAttribute(dataAttributeName);\n      Object(react_dom__WEBPACK_IMPORTED_MODULE_0__[\"render\"])(block, element);\n    } catch (error) {\n      // eslint-disable-next-line\n      console.error('Crowdsignal Forms: Failed to parse block data for: %s', dataAttributeName);\n    }\n  });\n};\n\nvar blockObserver = function blockObserver(dataAttributeName, blockBuilder) {\n  if (!Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isEmpty\"])(window.CrowdsignalMutationObservers) && true === window.CrowdsignalMutationObservers[dataAttributeName]) {\n    return;\n  }\n\n  var observer = new window.MutationObserver(function () {\n    return initBlocks(dataAttributeName, blockBuilder);\n  });\n  observer.observe(document.body, {\n    attributes: true,\n    attributeFilter: [dataAttributeName],\n    childList: true,\n    subtree: true\n  });\n\n  if (Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"isEmpty\"])(window.CrowdsignalMutationObservers)) {\n    window.CrowdsignalMutationObservers = [];\n  }\n\n  window.CrowdsignalMutationObservers[dataAttributeName] = true; // Run the first pass on load\n\n  initBlocks(dataAttributeName, blockBuilder);\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (MutationObserver);\n\n//# sourceURL=webpack:///./client/lib/mutation-observer/index.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _arrayLikeToArray(arr, len) {\n  if (len == null || len > arr.length) len = arr.length;\n\n  for (var i = 0, arr2 = new Array(len); i < len; i++) {\n    arr2[i] = arr[i];\n  }\n\n  return arr2;\n}\n\nmodule.exports = _arrayLikeToArray;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/arrayLikeToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _arrayWithHoles(arr) {\n  if (Array.isArray(arr)) return arr;\n}\n\nmodule.exports = _arrayWithHoles;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/arrayWithHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {\n  try {\n    var info = gen[key](arg);\n    var value = info.value;\n  } catch (error) {\n    reject(error);\n    return;\n  }\n\n  if (info.done) {\n    resolve(value);\n  } else {\n    Promise.resolve(value).then(_next, _throw);\n  }\n}\n\nfunction _asyncToGenerator(fn) {\n  return function () {\n    var self = this,\n        args = arguments;\n    return new Promise(function (resolve, reject) {\n      var gen = fn.apply(self, args);\n\n      function _next(value) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"next\", value);\n      }\n\n      function _throw(err) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"throw\", err);\n      }\n\n      _next(undefined);\n    });\n  };\n}\n\nmodule.exports = _asyncToGenerator;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/asyncToGenerator.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\nmodule.exports = _defineProperty;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/extends.js":
/*!********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/extends.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _extends() {\n  module.exports = _extends = Object.assign || function (target) {\n    for (var i = 1; i < arguments.length; i++) {\n      var source = arguments[i];\n\n      for (var key in source) {\n        if (Object.prototype.hasOwnProperty.call(source, key)) {\n          target[key] = source[key];\n        }\n      }\n    }\n\n    return target;\n  };\n\n  return _extends.apply(this, arguments);\n}\n\nmodule.exports = _extends;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/extends.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _iterableToArrayLimit(arr, i) {\n  if (typeof Symbol === \"undefined\" || !(Symbol.iterator in Object(arr))) return;\n  var _arr = [];\n  var _n = true;\n  var _d = false;\n  var _e = undefined;\n\n  try {\n    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {\n      _arr.push(_s.value);\n\n      if (i && _arr.length === i) break;\n    }\n  } catch (err) {\n    _d = true;\n    _e = err;\n  } finally {\n    try {\n      if (!_n && _i[\"return\"] != null) _i[\"return\"]();\n    } finally {\n      if (_d) throw _e;\n    }\n  }\n\n  return _arr;\n}\n\nmodule.exports = _iterableToArrayLimit;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _nonIterableRest() {\n  throw new TypeError(\"Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\");\n}\n\nmodule.exports = _nonIterableRest;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/nonIterableRest.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/objectWithoutProperties.js":
/*!************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/objectWithoutProperties.js ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var objectWithoutPropertiesLoose = __webpack_require__(/*! ./objectWithoutPropertiesLoose */ \"./node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js\");\n\nfunction _objectWithoutProperties(source, excluded) {\n  if (source == null) return {};\n  var target = objectWithoutPropertiesLoose(source, excluded);\n  var key, i;\n\n  if (Object.getOwnPropertySymbols) {\n    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);\n\n    for (i = 0; i < sourceSymbolKeys.length; i++) {\n      key = sourceSymbolKeys[i];\n      if (excluded.indexOf(key) >= 0) continue;\n      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;\n      target[key] = source[key];\n    }\n  }\n\n  return target;\n}\n\nmodule.exports = _objectWithoutProperties;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/objectWithoutProperties.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _objectWithoutPropertiesLoose(source, excluded) {\n  if (source == null) return {};\n  var target = {};\n  var sourceKeys = Object.keys(source);\n  var key, i;\n\n  for (i = 0; i < sourceKeys.length; i++) {\n    key = sourceKeys[i];\n    if (excluded.indexOf(key) >= 0) continue;\n    target[key] = source[key];\n  }\n\n  return target;\n}\n\nmodule.exports = _objectWithoutPropertiesLoose;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!**************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles */ \"./node_modules/@babel/runtime/helpers/arrayWithHoles.js\");\n\nvar iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit */ \"./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js\");\n\nvar unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray */ \"./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js\");\n\nvar nonIterableRest = __webpack_require__(/*! ./nonIterableRest */ \"./node_modules/@babel/runtime/helpers/nonIterableRest.js\");\n\nfunction _slicedToArray(arr, i) {\n  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || unsupportedIterableToArray(arr, i) || nonIterableRest();\n}\n\nmodule.exports = _slicedToArray;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/slicedToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ \"./node_modules/@babel/runtime/helpers/arrayLikeToArray.js\");\n\nfunction _unsupportedIterableToArray(o, minLen) {\n  if (!o) return;\n  if (typeof o === \"string\") return arrayLikeToArray(o, minLen);\n  var n = Object.prototype.toString.call(o).slice(8, -1);\n  if (n === \"Object\" && o.constructor) n = o.constructor.name;\n  if (n === \"Map\" || n === \"Set\") return Array.from(n);\n  if (n === \"Arguments\" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);\n}\n\nmodule.exports = _unsupportedIterableToArray;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js?");

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!\n  Copyright (c) 2017 Jed Watson.\n  Licensed under the MIT License (MIT), see\n  http://jedwatson.github.io/classnames\n*/\n/* global define */\n\n(function () {\n\t'use strict';\n\n\tvar hasOwn = {}.hasOwnProperty;\n\n\tfunction classNames () {\n\t\tvar classes = [];\n\n\t\tfor (var i = 0; i < arguments.length; i++) {\n\t\t\tvar arg = arguments[i];\n\t\t\tif (!arg) continue;\n\n\t\t\tvar argType = typeof arg;\n\n\t\t\tif (argType === 'string' || argType === 'number') {\n\t\t\t\tclasses.push(arg);\n\t\t\t} else if (Array.isArray(arg) && arg.length) {\n\t\t\t\tvar inner = classNames.apply(null, arg);\n\t\t\t\tif (inner) {\n\t\t\t\t\tclasses.push(inner);\n\t\t\t\t}\n\t\t\t} else if (argType === 'object') {\n\t\t\t\tfor (var key in arg) {\n\t\t\t\t\tif (hasOwn.call(arg, key) && arg[key]) {\n\t\t\t\t\t\tclasses.push(key);\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t}\n\t\t}\n\n\t\treturn classes.join(' ');\n\t}\n\n\tif ( true && module.exports) {\n\t\tclassNames.default = classNames;\n\t\tmodule.exports = classNames;\n\t} else if (true) {\n\t\t// register as 'classnames', consistent with npm package name\n\t\t!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {\n\t\t\treturn classNames;\n\t\t}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),\n\t\t\t\t__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));\n\t} else {}\n}());\n\n\n//# sourceURL=webpack:///./node_modules/classnames/index.js?");

/***/ }),

/***/ "@babel/runtime/regenerator":
/*!**********************************************!*\
  !*** external {"this":"regeneratorRuntime"} ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"regeneratorRuntime\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%22regeneratorRuntime%22%7D?");

/***/ }),

/***/ "@crowdsignalForms/apifetch":
/*!*********************************************************!*\
  !*** external {"this":["crowdsignalForms","apiFetch"]} ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"crowdsignalForms\"][\"apiFetch\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%5B%22crowdsignalForms%22,%22apiFetch%22%5D%7D?");

/***/ }),

/***/ "@wordpress/block-editor":
/*!**********************************************!*\
  !*** external {"this":["wp","blockEditor"]} ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"blockEditor\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%5B%22wp%22,%22blockEditor%22%5D%7D?");

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"components\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%5B%22wp%22,%22components%22%5D%7D?");

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"element\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%5B%22wp%22,%22element%22%5D%7D?");

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"i18n\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%5B%22wp%22,%22i18n%22%5D%7D?");

/***/ }),

/***/ "lodash":
/*!**********************************!*\
  !*** external {"this":"lodash"} ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%22lodash%22%7D?");

/***/ }),

/***/ "react":
/*!*********************************!*\
  !*** external {"this":"React"} ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"React\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%22React%22%7D?");

/***/ }),

/***/ "react-dom":
/*!************************************!*\
  !*** external {"this":"ReactDOM"} ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"ReactDOM\"]; }());\n\n//# sourceURL=webpack:///external_%7B%22this%22:%22ReactDOM%22%7D?");

/***/ })

/******/ })));
/**
 * Load the form instance
 */
require('./form');

/**
 * Add http helpers to the main instance
 */
$.extend(Shortener, require('./http'));

/**
 * Load form components
 */
require('./components');
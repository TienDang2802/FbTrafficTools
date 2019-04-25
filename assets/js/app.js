/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/material-dashboard.min.css');

require('jquery');
import $ from 'jquery';

require('perfect-scrollbar');
require('./core/bootstrap-material-design.min.js');
require('./material-dashboard.min.js');

window.$ = window.jQuery = $;

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);


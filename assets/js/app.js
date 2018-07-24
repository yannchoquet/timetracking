require('../css/app.scss');

const $ = require('jquery');
require('selectize');

$('select').selectize({
    create: false,
    sortField: 'text'
});

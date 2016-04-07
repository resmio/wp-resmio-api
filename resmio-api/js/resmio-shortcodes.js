
(function() {
    tinymce.PluginManager.add('custom_mce_button', function(editor, url) {
        editor.addButton('custom_mce_button', {
            type: 'menubutton',
            text: 'resmio',
            icon: 'icon resmio-editor-icon',
            menu: [
                {text: 'Button', onclick: function() {editor.insertContent('[resmio-button]');}},
                {text: 'Widget', onclick: function() {editor.insertContent('[resmio-widget]');}},
                {text: 'Name', onclick: function() {editor.insertContent('[resmio-name]');}},
                {text: 'Straße', onclick: function() {editor.insertContent('[resmio-street]');}},
                {text: 'Postleitzahl', onclick: function() {editor.insertContent('[resmio-zipcode]');}},
                {text: 'Ort', onclick: function() {editor.insertContent('[resmio-city]');}},
                {text: 'Adresse', onclick: function() {editor.insertContent('[resmio-address]');}},
                {text: 'Telefon', onclick: function() {editor.insertContent('[resmio-phone]');}},
                {text: 'E-Mail', onclick: function() {editor.insertContent('[resmio-email]');}},
                {text: 'Kontakt', onclick: function() {editor.insertContent('[resmio-contact]');}},
                {text: 'Facebook', onclick: function() {editor.insertContent('[resmio-facebook]');}},
                {text: 'Google +', onclick: function() {editor.insertContent('[resmio-google]');}},
                {text: 'Soziale Medien', onclick: function() {editor.insertContent('[resmio-social-media]');}},
                {text: 'Öffnungszeiten', onclick: function() {editor.insertContent('[resmio-opening-times]');}},
                {text: 'Beschreibung', onclick: function() {editor.insertContent('[resmio-description]');}}
            ]
        });
    });
})();

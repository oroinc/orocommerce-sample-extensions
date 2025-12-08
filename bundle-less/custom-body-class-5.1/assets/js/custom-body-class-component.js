define(function(require) {
    'use strict';

    const BaseComponent = require('oroui/js/app/components/base/component');

    const CustomBodyClassComponent = BaseComponent.extend({
        initialize: function(options) {
            // Accept single or multiple classes separated by spaces
            this.classes = options.bodyClass
                ? String(options.bodyClass).split(' ')
                : [];

            this.classes.forEach(function(cls) {
                if (cls) {
                    document.body.classList.add(cls);
                }
            });

            CustomBodyClassComponent.__super__.initialize.call(this, options);
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.classes.forEach(function(cls) {
                if (cls) {
                    document.body.classList.remove(cls);
                }
            });

            CustomBodyClassComponent.__super__.dispose.call(this);
        }
    });

    return CustomBodyClassComponent;
});

import BaseComponent from 'oroui/js/app/components/base/component';

/**
 * Component that adds CSS class(es) to the document body when initialized
 * and removes them when disposed (e.g., when the user navigates to another page).
 *
 * @class CustomBodyClassComponent
 * @extends BaseComponent
 *
 * @example
 * // In a back-office Twig template:
 * {% block content %}
 *     {# Configure the page component for this page #}
 *     {% set pageComponent = {
 *         module: 'js/custom-body-class-component',
 *         options: {
 *             bodyClass: 'my-custom-class-for-this-page'
 *         }
 *     } %}
 *     {{ parent() }}
 * {% endblock %}
 */
const CustomBodyClassComponent = BaseComponent.extend({
    /**
     * Array of CSS classes to add/remove from <body>
     * @property {string[]}
     */
    classes: null,

    /**
     * @constructor
     * @param {Object} options
     */
    constructor: function CustomBodyClassComponent(options) {
        CustomBodyClassComponent.__super__.constructor.call(this, options);
    },

    /**
     * @param {Object} options - Component options
     * @param {string} [options.bodyClass] - CSS class(es) to add to <body>, space-separated
     */
    initialize(options) {
        this.classes = options.bodyClass
            ? String(options.bodyClass).split(' ')
            : [];

        this.classes.forEach(cls => {
            if (cls) {
                document.body.classList.add(cls);
            }
        });

        CustomBodyClassComponent.__super__.initialize.call(this, options);
    },

    /** @inheritdoc */
    dispose() {
        if (this.disposed) {
            return;
        }

        this.classes.forEach(cls => {
            if (cls) {
                document.body.classList.remove(cls);
            }
        });

        CustomBodyClassComponent.__super__.dispose.call(this);
    }
});

export default CustomBodyClassComponent;

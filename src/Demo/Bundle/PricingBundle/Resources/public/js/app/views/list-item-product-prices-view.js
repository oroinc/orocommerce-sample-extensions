define(function(require) {
    'use strict';

    var ListItemProductPricesView;
    var BaseView = require('oropricing/js/app/views/list-item-product-prices-view');
    var PriceLoader = require('demopricing/js/app/deferred-price-loader');
    var _ = require('underscore');

    ListItemProductPricesView = BaseView.extend({
        render: function() {
            if (_.isEmpty(this.model.get('prices'))) {
                PriceLoader.scheduleLoading(
                    this.model,
                    _.bind(ListItemProductPricesView.__super__.render, this)
                );
                return false;
            }

            ListItemProductPricesView.__super__.render.apply(this, arguments);
        }
    });

    return ListItemProductPricesView;
});

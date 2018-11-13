define(function(require) {
    'use strict';

    var DeferredPriceLoader;
    var routing = require('routing');
    var $ = require('jquery');
    var mediator = require('oroui/js/mediator');
    var _ = require('underscore');

    DeferredPriceLoader = {
        loadQueue: {},

        /**
         * Sign that afterChange event fired
         */
        afterChangeFired: false,

        /**
         * Keep index of setTimeout function
         */
        timeout: null,

        /**
         * Keep link to the current jqXHR object
         */
        currentRequest: null,

        scheduleLoading: function(model, callback) {
            if (model.has('id') && _.isEmpty(model.get('prices'))) {
                var productId = model.get('id');
                if (!_.has(this.loadQueue, productId)) {
                    this.loadQueue[productId] = [];
                }
                this.loadQueue[productId].push({
                    'model': model,
                    'callback': callback
                });

                if (this.afterChangeFired && null === this.currentRequest && null === this.timeout) {
                    this.timeout = setTimeout(_.bind(this.loadPrices, this), 1000);
                }
            }
        },

        loadPrices: function() {
            var productIds = _.keys(this.loadQueue);
            if (productIds.length) {
                this.currentRequest = $.getJSON(
                    routing.generate('demo_pricing_frontend_prices_by_products', {'product_ids': productIds}),
                    _.bind(this.onPricesLoad, this)
                );
            }
        },

        onPricesLoad: function(responseData) {
            _.each(this.loadQueue, function(queueStack, productId) {
                _.each(queueStack, function(data, id) {
                    data.model.set('prices', responseData[productId]);
                    data.callback();
                    delete this.loadQueue[productId][id];
                }, this);

                if (_.isEmpty(_.compact(this.loadQueue[productId]))) {
                    this.loadQueue = _.omit(this.loadQueue, productId);
                }
            }, this);

            if (!_.isEmpty(this.loadQueue)) {
                this.timeout = setTimeout(_.bind(this.loadPrices, this), 1000);
            }

            this.currentRequest = null;
            this.timeout = null;
        }
    };

    mediator.on('page:afterChange', function() {
        this.afterChangeFired = true;
        this.loadPrices();
    }, DeferredPriceLoader);

    return DeferredPriceLoader;
});

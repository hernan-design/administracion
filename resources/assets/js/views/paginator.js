/**
* Class Paginator extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.Paginator = Backbone.View.extend({

        tagName: 'ul',
        className: 'pagination',
        template: _.template(($('#pagination-tpl').html() || '')),
        maxLen: 8,
        events: {
            'click .arrow.start-page a': 'onStartPage',
            'click .arrow.end-page a': 'onEndPage',
            'click .arrow.prev-page a': 'onPrevPage',
            'click .arrow.next-page a': 'onNextPage',
            'click li:not(.arrow) a': 'onGetPage'
        },
        parameters: {
            nameForm: '',
            data: {}
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            _.bindAll(this, 'onReasignLen');

            // extend view parameters
            if (opts !== undefined && _.isObject(opts.parameters))
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            if (!this.collection instanceof Backbone.PageableCollection)
                return;

            //Init Attributes
            this.$formFilter = this.parameters['nameForm'];

            // Events Listener
            this.listenTo(this.collection, 'reset', this.render);
            this.listenTo(this.collection, 'pageable:state:change', this.updatedState);

            // handler with resize window event
            this.onReasignLen();
            $(window).off('resize load', this.onReasignLen);
            $(window).on('resize load', this.onReasignLen);
        },

        /*
        * Render View Element
        */
        render: function () {
            var items = this.buildItems();
            var attributes = { items: items };
            this.$el.html(this.template(attributes));

            this.$el.find('li.arrow').removeClass('unavailable')
                .addClass('waves-effect')
                .find('a').removeClass('disabled');

            if (this.collection.state.totalPages != this.collection.state.currentPage) {
                this.$el.find('li.current.active').addClass('unavailable disabled')
                    .removeClass('waves-effect')
                    .find('a').addClass('disabled').attr('readonly', true);
            }

            if (this.collection.state.firstPage == this.collection.state.currentPage) {
                this.$el.find('li.arrow.prev-page, li.arrow.start-page').addClass('unavailable disabled')
                    .removeClass('waves-effect')
                    .find('a').addClass('disabled');
            }

            if (this.collection.state.lastPage == this.collection.state.currentPage) {
                this.$el.find('li.arrow.next-page, li.arrow.end-page').addClass('unavailable disabled')
                    .removeClass('waves-effect')
                    .find('a').addClass('disabled');
            }

            return this;
        },

        /**
        * to calculate split on pagination
        */
        calSplit: function (start) {

            var splitAt = [],
                len = this.collection.state.totalPages,
                maxLen1_2 = Math.floor(this.maxLen / 2),
                startPag = 0;
            maxLen1_2End = len - maxLen1_2;

            splitAt[0] = maxLen1_2;
            startPag = this.collection.state.currentPage - maxLen1_2 > 0 ? this.collection.state.currentPage - maxLen1_2 : 0;

            if (this.collection.state.currentPage >= maxLen1_2) {
                splitAt[0] = this.collection.state.currentPage + 1;
            }
            splitAt[1] = maxLen1_2End;

            return {
                'startPag': startPag,
                'splitAt': splitAt
            };
        },

        /**
        * build pagination items object
        */
        buildItems: function () {
            var items = new Array(),
                startPag = 0,
                splitAt = [0, 0],
                flagHellip = true;

            confPagination = this.calSplit();
            startPag = confPagination.startPag;
            splitAt = confPagination.splitAt;

            for (var i = startPag, len = this.collection.state.totalPages; i < len; i++) {

                if (i > splitAt[0] && i <= splitAt[1]) {

                    if (flagHellip) {
                        items.push({
                            indx: '',
                            title: '...',
                            classes: 'unavailable'
                        });
                        flagHellip = false;
                    }
                    continue;
                }

                items.push({
                    indx: i,
                    title: i + 1,
                    classes: this.collection.state.currentPage == i ? 'current active' : ''
                });
            }

            return items;
        },

        /**
        * re asign length for mobile resolution
        */
        onReasignLen: function (e) {

            // if browser window is least 1024px
            if ($(window).width() <= 480) {
                this.maxLen = 4;
            } else if ($(window).width() <= 640) {
                this.maxLen = 6;
            } else {
                this.maxLen = 8;
            }

            // reload pagination
            this.render();
        },

        /**
        * event to fetch start page
        */
        onStartPage: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.dataFilter = window.Misc.formToJson(this.$formFilter);

            if (this.dataFilter !== undefined) {
                this.parameters.dataFilter = this.dataFilter;
            } else {
                this.parameters.dataFilter = this.parameters.data;
            }

            this.collection.getFirstPage({ reset: true, data: this.parameters.dataFilter });
        },

        /**
        * event to fetch end page
        */
        onEndPage: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.dataFilter = window.Misc.formToJson(this.$formFilter);

            if (this.dataFilter !== undefined) {
                this.parameters.dataFilter = this.dataFilter;
            } else {
                this.parameters.dataFilter = this.parameters.data;
            }

            this.collection.getLastPage({ reset: true, data: this.parameters.dataFilter });
        },

        /**
        * event to fetch the previous page
        */
        onPrevPage: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                $link = $(e.currentTarget);

            this.dataFilter = window.Misc.formToJson(this.$formFilter);

            if (Object.keys(this.dataFilter).length > 2 && this.dataFilter !== undefined) {
                this.parameters.dataFilter = _.extend(this.dataFilter, this.parameters.data);
            } else {
                this.parameters.dataFilter = this.parameters.data;
            }

            if (this.collection.state.currentPage > this.collection.state.firstPage) {
                this.collection.getPreviousPage({ reset: true, data: this.parameters.dataFilter });
                this.updatedState();
            }
        },

        /**
        * event to fetch the next page
        */
        onNextPage: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                $link = $(e.currentTarget);

            this.dataFilter = window.Misc.formToJson(this.$formFilter);

            if (Object.keys(this.dataFilter).length > 2 && this.dataFilter !== undefined) {
                this.parameters.dataFilter = _.extend(this.dataFilter, this.parameters.data);
            } else {
                this.parameters.dataFilter = this.parameters.data;
            }

            if (this.collection.state.currentPage < this.collection.state.lastPage) {
                this.collection.getNextPage({ reset: true, data: this.parameters.dataFilter });
                this.updatedState();
            }
        },

        /**
        * event to fecth a page
        */
        onGetPage: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var indx = $(e.currentTarget).attr('href').replace(/^#/, '');

            this.dataFilter = window.Misc.formToJson(this.$formFilter);

            if (Object.keys(this.dataFilter).length > 2 && this.dataFilter !== undefined) {
                this.parameters.dataFilter = _.extend(this.dataFilter, this.parameters.data);
            } else {
                this.parameters.dataFilter = this.parameters.data;
            }

            this.collection.getPage(parseInt(indx), { reset: true, data: this.parameters.dataFilter });
        },

        /**
        * State change event
        */
        updatedState: function (newStates) {
            var currentPage = this.collection.state.currentPage,
                firstPage = this.collection.state.firstPage;

            if (Object.keys(this.parameters.data).length > 0) {
                app.AppRouter.navigate(
                    Backbone.history.fragment.split('?')[0] + '?' + $.param(_.extend(
                        this.parameters.data,
                        currentPage > firstPage ? { page: (firstPage && currentPage) || currentPage + 1 } : delete this.parameters.data.page
                    )),
                    { trigger: false, replace: true }
                )
            }
        }
    });

})(window.jQuery, window, document);

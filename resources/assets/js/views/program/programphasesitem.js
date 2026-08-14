/**
* Class ProgramPhasesItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramPhasesItemView = Backbone.View.extend({
        tagName: 'li',
        className: 'dd-item dd3-item',
        templateAttachmentPhases: _.template( ($('#item-attachment-phase-tpl').html() || '') ),

        events: {
            'click .edit-phases': 'onEditPhases'
        },
		parameters: {
		   template: 'templateAttachmentPhases',
            data: {}
		},
        /**
        * Constructor Method
        */
        initialize: function (opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ) {

                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            this.$el.data('codigo_adjunto', this.model.get('codigo_adjunto'));
            this.$el.data('codigo_adjunto_fase', this.model.get('codigo_adjunto_fase'));
            this.$el.data('orden_adjunto_fase', this.model.get('orden_adjunto_fase'));

            // Collection
            this.phaseAttachList = new app.AttachmentPhaseList();

            // Events Listener
			this.listenTo( this.model, "change", this.render );
        },

        /*
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();
			this.$el.html( this[this.parameters.template](attributes) );

            return this;
        },

        /*
        * Modal edit content
        */
        onEditPhases: function(e) {
            e.preventDefault();
            e.stopPropagation();
            var _this = this;
            var resource_id = $(e.currentTarget).attr('id');

            this.phaseAttachList.fetch({data: {program: this.model.get('codigo_convocatoria'), phase: this.model.get('codigo_fase'), codigo_recurso: resource_id}, success: function() {
                    var data_resource = _this.phaseAttachList.toJSON();
                    _this.programPhasesWindow = new app.ProgramPhasesWindow({
                        model: _this.model,
                        parameters: {
                            template: 'templateAttachment',
                            data:{
                                codigo_fase: _this.model.get('codigo_fase'),
                                action: _this.parameters.data.action,
                            },
                            data_resource: data_resource,
                        }
                    });

                    _this.programPhasesWindow.render();
                },
            });

        }
    });

})(jQuery, this, this.document);

/**
* Class MenuHideView extend of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MenuHideView = Backbone.View.extend({

        el: '#menu-content',
        template: _.template( ($('#menu-confirm-hide-tpl').html() || '') ),
        parameters: {
            'titleConfirm': 'Eliminar Menu'
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'render');   
            // Init Attributes
        },

        /*
        * Render View Element
        */
        render: function() {
            _this = this;
            var attributes = {};
            this.$el.find('.content-modal').html( this.template(attributes) );

            // Change modal title
            this.$el.find('.inner-title-modal').html( this.parameters['titleConfirm'] );
            this.$el.modal('show');
            // delegate events
            $(this.el).off();
            this.delegateEvents({
                'click .confirm-action': 'onConfirm'
            });
     
                (new PNotify({
                    title: 'Eliminar Menu',
                    text: this.template(attributes),
                    icon: 'glyphicon glyphicon-question-sign',
                    hide: false,
                    confirm: {
                        confirm: true,
                        // buttons:[{
                        //     text: "Confirmar", 
                        //     addClass: "confirm-action", 
                        //     // promptTrigger: true, 
                        //     click: function(){ 
                        //         this.onConfirm()
                        //     }
                        // },{
                        //    text: "Cancelar",
                        //    // addClass: "", 
                        //    click: function(notice){ 
                        //    notice.remove(); 
                        //    // notice.removeAll();
                        //    // notice.get().trigger("pnotify.cancel", notice); 
                        //     }
                        // }]
                        buttons:[{
                            text: "Ok", 
                            addClass: "", 
                            promptTrigger: true, 
                            click: function(notice){ 
                                alert('coso');
                                // this.onConfirm();
                                notice.remove(); 
                                // notice.get().trigger("pnotify.confirm", [notice, value]); 
                            }
                            },{
                                text: "Cancel", 
                                addClass: "", 
                                click: function(notice){ 
                                notice.remove(); 
                                notice.get().trigger("pnotify.cancel", notice); }
                        }]
                    },
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    history: {
                        history: false
                    },
                    addclass: 'stack-modal',
                    stack: {'dir1': 'down', 'dir2': 'right', 'modal': false}
                })).get().on('pnotify.confirm', function() {
                    //
                }).on('pnotify.cancel', function() {
                     notice.pnotify_remove();
                });


            return this;
        },

        /**
        * Menu cancel hide
        */
        onCancel: function (e) {
            e.preventDefault();
            // console.log( 'cancel' );
            // this.$el.modal('hide');
        },

        /**
        * Menu confirm hide
        */
        onConfirm: function (e) {
            e.preventDefault();
            this.$el.modal('hide');
            this.model.destroy({
                processData: true,
                wait: true,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content') 
                }
            });
        }
   });

})(window.jQuery, window, document);

/**
* Class MarketplaceAssignmentModel extends of Backbone Model
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.MarketplaceVoucherModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('marketplace.vouchers.index') );
        },
        idAttribute: 'codigo_detalle_voucher',
        defaults: {
            'nombre_usuario' : '',
            'correo_electronico' : '',
            'nombre_entidad' : '',
            'valor_detalle_voucher' : 0,
            'saldo_detalle_voucher' : 0,
        },

        /**
        * Constructor Method
        * @param Object attrs, model attributes
        * @param Object opts, model Options
        */
        initialize: function(attrs, opts){
            //
        },
    });

})(this, this.document);

/**
* Class DiscussionSaleAgreementModel extends of Backbone Model
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.DiscussionSaleAgreementModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('marketplace.transacciones.discusion.index') );
        },
        idAttribute: 'codigo_discucion',
        defaults: {
            'id_discucion': 0,
            'codigo_estado': 0,
            'codigo_usuario': 0,
            'texto_discucion': '',
            'contador_comentarios_discucion': 0,
            'contador_like': 0,
            'nickname_usuario': '',
            'imagen_usuario': '',
            'nombre_usuario': '',
            'fecha_discusion': '',
            'srcparams': '',
            'url_profile': '',
            'origin': '',
            'url_discusion': ''
        }
    });

})(this, this.document);

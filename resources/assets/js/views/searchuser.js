/**
* Class ComponentSearchUserView  of Backbone
* @author TuProyecto || @dropecamargo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ComponentSearchUserView = Backbone.View.extend({

      	el: 'body',
        template: _.template( ($('#form-users-modal-tpl').html() || '') ),
        templateSearchEntity: _.template( ($('#form-users-entity-modal-tpl').html()  || '') ),
        templateSearchPrincipalOrganizer: _.template( ($('#form-users-principal-organizer-modal-tpl').html() || '') ),
        templateSearchEntityInvitation: _.template( ($('#form-users-entity-invitation-modal-tpl').html() || '' ) ),
        templateSearchUserResponsable: _.template( ($('#form-user-responsable-modal-tpl').html() || '' ) ),
		events: {
            'click .btn-search-user': 'search',
            'click .btn-search-entity-user': 'searchEntity',
            'click .btn-clear-entity-user' : 'clearEntity',
            'click .btn-search-principal-organizer-user' : 'searchPrincipalOrganizer',
            'click .btn-clear-principal-organizer-user': 'clearPrincipalOrganizer',
            'click .btn-search-entity-invitation-user' : 'searchEntityInvitation',
            'click .btn-search-user-responsable': 'searchUserResponsable',
            'click .btn-clear-user-responsable' : 'clearUserResponsable',
            'click .btn-clear-entity-invitations-user': 'clearEntityInvitation',
            'click .btn-clear-user': 'clear',
            'click .tp_input-group-addon': 'onModalUserSearch',
            'click .a-search-tercero-component-table': 'setUser',
            'click .a-search-entity-tercero-component-table': 'setUserEntity',
            'click .a-search-principal-organizer-tercero-component-table': 'setPrincipalOrganizer',
            'click .a-search-entity-invitation-tercero-component-table': 'setUserEntityInvitation',
            'click .a-search-user-responsable-tercero-component-table' : 'setUserResponsable',
            'change input.correo_electronico_user': 'emailChanged',
            'click .tp_input-email-search': 'byemailUserSearch',
            'click .tp_input-search_entity' : 'onModalSearchEntity',
            'click .tp_input-search_organizer' : 'onModalSearchPrincipalOrganizer',
            'click .tp_input-search_user_responsable' : 'onModalSearchUserResponsable',
            'click .tp_input-search_entity_invitation': 'onModalSearchEntityInvitation'
		},

        /**
        * Constructor Method
        */
		initialize: function() {
			// Initialize
            this.$modalComponent = this.$('#modal-form-search');
            this.$modalSearchEntity = this.$('#modal-form-entity');
            this.$modalSearchPrincipalOrganizer = this.$('#modal-form-principal-organizer');
            this.$modalSearchEntityInvitation = this.$('#modal-form-entity-invitation');
            this.$modalSearchUserResponsable = this.$('#modal-form-user-responsable');

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
		},

        onModalSearchUserResponsable: function(e) {
		    e.preventDefault();

            var _this = this;

            this.$modalSearchUserResponsable.find('.modal-title').html( $(e.target).attr('data-title') );
            this.$modalSearchUserResponsable.find('.content-modal').html( this.templateSearchUserResponsable({ }) );

            this.$searchNombreUsuarioResponsable      = this.$('#nombre_usuario_responsable_datatable');
            this.$searchNicknameUsuarioResponsable   = this.$('#nickname_usuario_responsable_datatable');
            this.$searchCityUsuarioResponsable        = this.$('#codigo_ciudad_usuario_responsable_datatable');

            this.$userResponsableSearchTable = this.$('#user-responsable-list-search-table');
            this.$inputContent = this.$("#"+$(e.currentTarget).attr("data-field"));
            this.$inputName = this.$("#"+this.$inputContent.attr("data-name"));

            this.userResponsableSearchTable = this.$userResponsableSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.index') ),
                    data: function( data ) {
                        data.nombre_usuario     = _this.$searchNombreUsuarioResponsable.val();
                        data.nickname_usuario   = _this.$searchNicknameUsuarioResponsable.val();
                        data.nombre_ciudad      = _this.$searchCityUsuarioResponsable.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'ciudad',               name: 'ciudad'              },
                    { data: 'codigo_usuario',       name: 'codigo_usuario'      },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="" title="Ver usuario" style="color:#3FC1A5" class="a-search-user-responsable-tercero-component-table">'+full.nombre_usuario+'</a>';
                        }
                    },
                    {
                        targets : 3 ,
                        visible : false
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });

            // Modal show
            this.ready();
            this.$modalSearchUserResponsable.modal('show');
        },

        onModalSearchPrincipalOrganizer: function(e) {
		    e.preventDefault();

		    var _this = this;

            this.$modalSearchPrincipalOrganizer.find('.modal-title').html( $(e.target).attr('data-title') );
            this.$modalSearchPrincipalOrganizer.find('.content-modal').html( this.templateSearchPrincipalOrganizer({ }) );

            this.$searchNombrePrincipalOrganizer      = this.$('#nombre_usuario_principal_organizer_datatable');
            this.$searchNicknamePrincipalOrganizer   = this.$('#nickname_usuario_principal_organizer_datatable');
            this.$searchCityEntityPrincipalOrganizer        = this.$('#codigo_ciudad_principal_organizer_datatable');

            this.$userPrincipalOrganizerListSearchTable = this.$('#user-principal-organizer-list-search-table');
            this.$inputContent = this.$("#"+$(e.currentTarget).attr("data-field"));
            this.$inputName = this.$("#"+this.$inputContent.attr("data-name"));

            this.userPrincipalOrganizerListSearchTable = this.$userPrincipalOrganizerListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.organizerev') ),
                    data: function( data ) {
                        data.nombre_usuario     = _this.$searchNombrePrincipalOrganizer.val();
                        data.nickname_usuario   = _this.$searchNicknamePrincipalOrganizer.val();
                        data.nombre_ciudad      = _this.$searchCityEntityPrincipalOrganizer.val();
                        data.tableModal         = true;
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'ciudad',               name: 'ciudad'              },
                    { data: 'codigo_usuario',       name: 'codigo_usuario'      },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="" title="Ver usuario" style="color:#3FC1A5" class="a-search-principal-organizer-tercero-component-table">'+full.nombre_usuario+'</a>';
                        }
                    },
                    {
                        targets : 3 ,
                        visible : false
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });

            // Modal show
            this.ready();
            this.$modalSearchPrincipalOrganizer.modal('show');
        },

        searchPrincipalOrganizer: function(e) {
		  e.preventDefault();

		  this.userPrincipalOrganizerListSearchTable.ajax.reload();
        },

        clearPrincipalOrganizer: function(e) {
		  e.preventDefault(e);

		  this.$searchNombrePrincipalOrganizer.val('');
		  this.$searchNicknamePrincipalOrganizer.val('');
		  this.$searchCityEntityPrincipalOrganizer.select2('val', '');

		  this.userPrincipalOrganizerListSearchTable.ajax.reload();
        },

        clearUserResponsable: function(e) {
		  e.preventDefault(e);

            this.$searchNombreUsuarioResponsable.val('');
            this.$searchNicknameUsuarioResponsable.val('');
            this.$searchCityUsuarioResponsable.select2('val', '');

            this.userResponsableSearchTable.ajax.reload();
        },

        setPrincipalOrganizer: function(e) {
		    e.preventDefault();

            var data = this.userPrincipalOrganizerListSearchTable.row( $(e.currentTarget).parents('tr') ).data();

            this.$inputContent.val( data.nombre_usuario );
            this.$inputName.val( data.codigo_usuario );

            this.$modalSearchPrincipalOrganizer.modal('hide');
        },

        onModalSearchEntityInvitation: function(e) {
		  e.preventDefault();

            var _this = this;

            // Render template
            this.$modalSearchEntityInvitation.find('.modal-title').html( $(e.target).attr('data-title') );
            this.$modalSearchEntityInvitation.find('.content-modal').html( this.templateSearchEntityInvitation({ }) );

            // References
            this.$searchNombreEntityInvitation      = this.$('#nombre_usuario_entity_invitation_datatable');
            this.$searchNicknameEntityInvitation   = this.$('#nickname_usuario_entity_invitation_datatable');
            this.$searchCityEntityInvitation        = this.$('#codigo_ciudad_entity_invitation_datatable');
            this.$searchIdentificationUserInvitation = this.$('#codigo_identification_invitation_datatable');

            this.$userEntityInvitationListSearchTable = this.$('#user-entity-invitation-list-search-table');
            this.$inputContent = this.$("#"+$(e.currentTarget).attr("data-field"));
            this.$inputName = this.$("#"+this.$inputContent.attr("data-name"));

            this.userEntityInvitationListSearchTable = this.$userEntityInvitationListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.index') ),
                    data: function( data ) {
                        data.nombre_usuario     = _this.$searchNombreEntityInvitation.val();
                        data.nickname_usuario   = _this.$searchNicknameEntityInvitation.val();
                        data.nombre_ciudad      = _this.$searchCityEntityInvitation.val();
                        data.identificacion_usuario = _this.$searchIdentificationUserInvitation.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'ciudad',               name: 'ciudad'              },
                    { data: 'codigo_usuario',       name: 'codigo_usuario'      },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="" title="Ver usuario" style="color:#3FC1A5" class="a-search-entity-invitation-tercero-component-table">'+full.nombre_usuario+'</a>';
                        }
                    },
                    {
                        targets : 3 ,
                        visible : false
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });

            // Modal show
            this.ready();
            this.$modalSearchEntityInvitation.modal('show');

        },

        onModalSearchEntity: function(e) {
            e.preventDefault();
            var _this = this;

            // Render template
            this.$modalSearchEntity.find('.modal-title').html( $(e.target).attr('data-title') );
            this.$modalSearchEntity.find('.content-modal').html( this.templateSearchEntity({ }) );


            // References
            this.$searchNombreEntity      = this.$('#nombre_usuario_entity_datatable');
            this.$searchNicknameEntity   = this.$('#nickname_usuario_entity_datatable');
            this.$searchCityEntity        = this.$('#codigo_ciudad_entity_datatable');

            this.$userEntityListSearchTable = this.$('#user-entity-list-search-table');
            this.$inputContent = this.$("#"+$(e.currentTarget).attr("data-field"));
            this.$inputName = this.$("#"+this.$inputContent.attr("data-name"));

            this.userEntityListSearchTable = this.$userEntityListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.searchEntities') ),
                    data: function( data ) {
                        data.nombre_usuario     = _this.$searchNombreEntity.val();
                        data.nickname_usuario   = _this.$searchNicknameEntity.val();
                        data.nombre_ciudad      = _this.$searchCityEntity.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'ciudad',               name: 'ciudad'              },
                    { data: 'codigo_usuario',       name: 'codigo_usuario'      },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="" title="Ver usuario" style="color:#3FC1A5" class="a-search-entity-tercero-component-table">'+full.nombre_usuario+'</a>';
                        }
                    },
                    {
                        targets : 3 ,
                        visible : false
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });

            // Modal show
            this.ready();
            this.$modalSearchEntity.modal('show');
        },

		onModalUserSearch: function(e) {
            e.preventDefault();
            var _this = this;

            // Render template
            this.$modalComponent.find('.modal-title').html( $(e.target).attr('data-title') );
            this.$modalComponent.find('.content-modal').html( this.template({ }) );

            // References
            this.$searchNombre              = this.$('#nombre_usuario_datatable');
            this.$searchNickname            = this.$('#nickname_usuario_datatable');
            this.$searchIdentification      = this.$('#cedula_usuario_datatable');
            this.$searchEmail               = this.$('#correo_electronico_datatable');
            this.$searchCity                = this.$('#codigo_ciudad_datatable');

            this.$userListSearchTable = this.$('#user-list-search-table');
			this.$inputContent = this.$("#"+$(e.currentTarget).attr("data-field"));
            this.$inputName = this.$("#"+this.$inputContent.attr("data-name"));
            this.role = this.$inputContent.attr("data-role");

            this.userListSearchTable = this.$userListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.search') ),
                    data: function( data ) {
                        data.nombre_usuario             = _this.$searchNombre.val();
                        data.nickname_usuario           = _this.$searchNickname.val();
                        data.identificacion_usuario     = _this.$searchIdentification.val();
                        data.correo_electronico         = _this.$searchEmail.val();
                        data.nombre_ciudad              = _this.$searchCity.val();
                        data.role                       = _this.role;
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'correo_electronico',   name: 'correo_electronico'  },
                    { data: 'ciudad',               name: 'ciudad'              },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="" title="Ver usuario" style="color:#3FC1A5" class="a-search-tercero-component-table">'+full.nombre_usuario+'</a>';
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });

            // Modal show
            this.ready();
			this.$modalComponent.modal('show');
		},

        setUserEntity: function(e) {
            e.preventDefault();
            var data = this.userEntityListSearchTable.row( $(e.currentTarget).parents('tr') ).data();

            this.$inputContent.val( data.nombre_usuario );
            this.$inputName.val( data.codigo_usuario );

            this.$modalSearchEntity.modal('hide');
        },

        setUserEntityInvitation: function(e) {
		  e.preventDefault();

            var data = this.userEntityInvitationListSearchTable.row( $(e.currentTarget).parents('tr') ).data();

            this.$inputContent.val( data.nombre_usuario );
            this.$inputName.val( data.codigo_usuario );

            this.$modalSearchEntityInvitation.modal('hide');
        },

        setUserResponsable: function(e) {
		    e.preventDefault();

            var data = this.userResponsableSearchTable.row( $(e.currentTarget).parents('tr') ).data();

            this.$inputContent.val( data.nombre_usuario );
            this.$inputName.val( data.codigo_usuario );

            this.$modalSearchUserResponsable.modal('hide');
        },

		setUser: function(e) {
			e.preventDefault();

	        var data = this.userListSearchTable.row( $(e.currentTarget).parents('tr') ).data();

			this.$inputContent.val( data.correo_electronico );
			this.$inputName.val( data.nombre_usuario );

			this.$modalComponent.modal('hide');
		},

		search: function(e) {
			e.preventDefault();

		    this.userListSearchTable.ajax.reload();
		},

        searchEntity: function(e) {
		    e.preventDefault(e);

		    this.userEntityListSearchTable.ajax.reload();
        },

        searchEntityInvitation: function(e) {
		  e.preventDefault();

		  this.userEntityInvitationListSearchTable.ajax.reload();
        },

        searchUserResponsable: function(e) {
		    e.preventDefault();

		    this.userResponsableSearchTable.ajax.reload();
        },


        clearEntity: function(e) {
            e.preventDefault();

            this.$searchNombreEntity.val('');
            this.$searchNicknameEntity.val('');
            this.$searchCityEntity.select2('val', '');

            this.userEntityListSearchTable.ajax.reload();
        },

        clearEntityInvitation: function(e) {
		    e.preventDefault();

            this.$searchNombreEntityInvitation.val('');
            this.$searchNicknameEntityInvitation.val('');
            this.$searchCityEntityInvitation.select2('val', '');
            this.$searchIdentificationUserInvitation.val('');

            this.userEntityInvitationListSearchTable.ajax.reload();
        },

		clear: function(e) {
			e.preventDefault();

            this.$searchNombre.val('');
            this.$searchEmail.val('');
            this.$searchNickname.val('');
            this.$searchIdentification.val('');
            this.$searchCity.select2('val', '');

            this.userListSearchTable.ajax.reload();
		},

		emailChanged: function(e) {
            var _this = this;

            this.$inputContent = $(e.currentTarget);
            this.$inputName = this.$("#"+$(e.currentTarget).attr("data-name"));
            this.$wraperConten = this.$("#users-search-toggle");

            var userSearch = this.$inputContent.val(),
                role = $(e.currentTarget).attr("data-role");

            // Before eval clear data
            this.$inputName.val('');

            if(!_.isUndefined(userSearch) && !_.isNull(userSearch) && userSearch != '') {
                // Get user
                $.ajax({
                    url: window.Misc.urlFull(Route.route('usuario.valid.email')),
                    type: 'GET',
                    data: { email: userSearch, role: role },
                    beforeSend: function() {
                        _this.$inputName.val('');
                        window.Misc.setSpinner( _this.$wraperConten );
                    }
                })
                    .done(function(resp) {

                        window.Misc.removeSpinner({
                            'wrap': this.$wraperConten,
                            'direct': false
                        });

                        // response success or error
                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ? (resp.message !== undefined ? resp.message : '...') : resp.errors;

                        if( _.isObject( resp.errors ) ){

                            var listError = '<ul>';

                            $.each(resp.errors, function(field, item) {
                                listError += '<li>'+ item[0] +'</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        }
                        _.extend(_this.configAlert, config);

                        if(resp.success) {

                            console.log(resp);
                            if(!_.isUndefined(resp.labelname+' '+resp.labellastname) && !_.isNull(resp.labelname+' '+resp.labellastname)){
                                _this.$inputName.val(resp.labelname+' '+resp.labellastname);
                            }
                        }else {
                            window.Misc.showAlertBox( _this.configAlert );
                            _this.$inputContent.val('');
                        }
                    });
            }
        },

        byemailUserSearch: function(e) {
            var _this = this;

            this.$inputContent =this.$("#correo_electronico_filter_data");
            this.$inputName = this.$('#nombre_usuario_filter_data');
            this.$wraperConten = this.$("#users-search-toggle");

            var userSearch = this.$inputContent.val(),
                role = $(e.currentTarget).attr("data-role");

            // Before eval clear data
            this.$inputName.val('');

            if(!_.isUndefined(userSearch) && !_.isNull(userSearch) && userSearch != '') {
                // Get user
                $.ajax({
                    url: window.Misc.urlFull(Route.route('usuario.valid.email')),
                    type: 'GET',
                    data: { email: userSearch, role: '' },
                    beforeSend: function() {
                        _this.$inputName.val('');
                        window.Misc.setSpinner( _this.$wraperConten );
                    }
                })
                    .done(function(resp) {

                        window.Misc.removeSpinner({
                            'wrap': this.$wraperConten,
                            'direct': false
                        });

                        // response success or error
                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ? (resp.message !== undefined ? resp.message : '...') : resp.errors;

                        if( _.isObject( resp.errors ) ){

                            var listError = '<ul>';

                            $.each(resp.errors, function(field, item) {
                                listError += '<li>'+ item[0] +'</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        }
                        _.extend(_this.configAlert, config);

                        if(resp.success) {

                            console.log(resp);
                            if(!_.isUndefined(resp.labelname+' '+resp.labellastname) && !_.isNull(resp.labelname+' '+resp.labellastname)){
                                _this.$inputName.val(resp.labelname+' '+resp.labellastname);
                            }
                        }else {
                            window.Misc.showAlertBox( _this.configAlert );
                            _this.$inputContent.val('');
                        }
                    });
            }
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // to fire plugins
            if(typeof window.initComponent.select2Trigger == 'function' )
                window.initComponent.select2Trigger();
        }
    });


})(jQuery, this, this.document);


// parameterization file
// --------------------

(function () {

	var _tp = (function () {

		return {
		    getState: {
		        'progress': 1,
		        'review': 2,
		        'cancelled': 3,
		        'live': 4,
		        'correction': 5,
		        'confirm': 6,
		        'confirmed': 7,
		        'active': 8,
		        'inactive': 9,
		        'feedback': 10,
		        'complete': 11,
		        'qualified': 12,
		        'removed': 13,
		        'pending': 14,
		        'evaluated': 15,
		        'closed': 16,
		        'rejected': 25,
                'finalized': 29,
                'to_finalize': 30
		    },
			getStateName: {
				1:'Progreso',
				2:'Revisión',
				3:'Cancelado',
				4:'live',
				5:'Corrección',
				6:'Confirmar',
				7:'Confirmado',
				8:'Sesión en desarrollo',
				9:'Inactivo',
				10:'feedback',
				11:'Completo',
				12:'qualified',
				13:'Sesión Cancelada',
				14:'Pendiente',
				15:'Evaluado',
				16:'Sesión Finalizada',
				17:'Bloqueado',
				18:'pendinginstitution',
				19:'preinscription',
				20:'Notificado',
				21:'forward',
				22:'offer',
				23:'no_linked',
				24:'linked',
				25:'Rechazado',
				26:'Devuelto',
				29:'Finalizado',
				30:'Finalizar',
				32:'Revisión entregables',
				33:'Rechazo de los entregables',
				34:'Pendiente aceptar sesión',
			},

			typeEvent: {
		        'mentoring': 1,
		        'call': 2,
		        'meeting': 3
			},

		    thumbs: {
		        'avatar': function () {
		            return 'http://static.tuproyecto.com/images/svg/no-user-image.svg';
		        },
		        'global': function () {
		            return 'http://static.tuproyecto.com/images/svg/no-image.svg';
		        }
		    },

		    product: {
		        'class' : {
		            'product': 1,
		            'reward': 2
		        }
		    },

		    getClassEntrepreneurship: {
		        'idea': 1,
		        'entrepreneurship': 2,
		        'group': 3,
		        'company': 4,
		        'grouptwo': 5
			},
			
			typeParameterInstitution: {
				'color': 1,
				'image_logo_header':  2,
				'image':  3,
				'value':  4,
				'video':  5
			},

			getTypeCourseContent: {
				'attachment': 1,
				'article': 2,
				'evaluation': 3,
				'homework': 4
			},

		    getTypeAttachment: {
		        'pdf': 1,
		        'youtube': 2,
		        'flash': 3,
		        'vimeo': 4,
		        'html': 5,
				'link':8,
				'survey':9
		    },

		    getTypeRole: {
		        'platform': 1,
		        'administrator': 2
		    },

		    getTypeModule: {
		        'platform': 1,
		        'landing': 2,
		        'administrator': 3
		    },

            docAttached: {
                'image': 5,
                'curriculum': 10
            },

		    typePart: {
		        'seller': 1,
		        'purchaser': 2
		    },

		    typeUser: {
		        'person': 1,
		        'entity': 2
		    },

		    typeProgram: {
                'internal': 1,
                'external': 2
            },

            courseOrientation: {
		        'internal': 1,
		        'external': 2
		    },

			typePrivacy: {
				'private': 1,
				'public': 2,
                'hidden': 3
			},

		    orientationProgram: {
		        'entity': 1,
		        'user': 2
		    },

		    getOrientationSpace: {
		        'user': 1,
		        'project': 2
		    },

		    inDocs: [
		        'pdf',
		        'doc',
		        'docx',
		        'xls',
		        'xlsx',
		        'ppt',
		        'pptx',
                'csv',
                'zip',
                'jpg',
                'jpeg',
                'png'
		    ],

		    inVideos: [
		        'swf',
		        'flash',
		        'youtube',
		        'vimeo',
		        'html',
		        'mp4',
		        'webm',
		        'ogg',
		        '3gp',
		        'flv'
		    ],

		    typeQuestionSurvey: {
		        'open': 1,
		        'single': 2,
		        'multiple': 3,
		        'upload': 4,
		        'matriz': 5
			},
			
			typeCriteriaEvaluation: {
		        'selector': 1,
		        'boolean': 2,
		        'stars': 3
		    },

		    classQuestionSurvey: {
		        'visible': 1,
		        'hidden': 2
		    },

		    typeQuestionField: {
		        'singleText': 1,
		        'longText': 2,
		        'number_format': 3,
		        'full_year': 4,
		        'cities': 5,
		        'pass_confirm': 6,
		        'agree_terms': 7,
		        'email': 8,
		        'url': 9,
		        'url_simple': 10,
		        'image': 11,
		        'image_multiple': 12,
		        'attachment': 13,
		        'date': 14,
		        'select': 15,
		        'ciiu': 16,
		        'education_level': 17,
		        'economic_sector': 18,
		        'currency': 19,
		        'single_check': 20,
		        'multiple_check': 21,
		        'phone': 22,
		        'percentage': 23,
		        'integer': 24,
		        'identification': 25
		    },

		    getInstitution: {
				'redcoo': 29,
		        'terpel': 37,
				'innpulsa': 51,
		        'ecopetrol': 53
		    },

		    typeNotify: {
		    	'general': 1,
		    	'friendship': 2
		    },

		    getTypePayment: {
		        'voucher': 2
		    },

		    currencyMasks : {
		    	'cop': 'currency-mask-cop',
		    	'usd': 'currency-mask-usd'
		    },

		    typeItemMentoring: {
		        'mentor': 1,
		        'entrepreneurship': 2
		    },

		    getPhases: {
		        'general': 1,
		        'one_challenge': 134,
		        'two_challenge': 137,
		        'three_challenge': 138,
		        'four_challenge': 139,
				'challenge_1.2': 150,
				'empodera2020_two': 250,
				'empodera2020_one': 249,
				'empodera2020_intermedate': 251,
				'aldea__20__challenge_1': 253,
				'aldea__20__challenge_1_2': 254,
				'aldea__20__challenge_1_3': 255,
				'aldea__20__challenge_2': 256,
				'aldea__20__challenge_3': 271
		    },

		    getProgram: {
				'invitation_entrepreneurs': 113,
				'empodera': 198,
				'empodera2020': 471 ,
                'specialized_service_advisors': 185,
		        'club_best': 115,
		        'award_innova': 121,
				'participants_only': 122,
				'register': 196,
                'reg_mentores_provedores': 350,
				'reg_entidades_provedores': 351,
				'empodera__2020': 471,
				'aldea__20': 504,
				'terpel__20': 436,
				'courses_propace': 579,
				'courses_propace_two': 595,
				'program_apps_co': 631,
				'ruta_emprendimiento': 634,
				'diagnostico_siinova': 652
		    },

		    getStateAgreement: {
                'progress': 1,
                'correction': 5,
		        'confirm': 6,
		        'confirmed': 7 ,
		        'active': 8,
		        'removed': 13,
		        'finalized': 29,
		        'to_finalize': 30
		    },

            getIconTypeAttachment: {
				1: 'fa fa-file-archive-o',
                2: 'fa fa-youtube-play',
                4: 'fa fa-vimeo-square',
                5: 'fa fa-html5',
                8: 'fa fa-link'
			},

			getIconTypeCourseContent: {
				1: 'fa fa-file-text-o',
                2: 'fa fa-newspaper-o',
                3: 'fa fa-file-word-o',
                4: 'fa fa-files-o'
			},

            getFormPaginationType: {
                'questions': 1,
                'pages': 2
            },

			getModule: {
				'calendar': 13,
                'blog': 14,
				'program': 28,
				'course': 24,
                'agreements': 52
			},

            getProgramClass: {
                'project': 1,
                'entity': 2
            },

            getRole: {
				'admin': 2,
                'mentor': 7,
                'seller': 14
            },

            messages: {
		        notBeneficie: "No existen beneficios creados ¿ Estas seguro de marcar asistencia ?",
                typeError: '{file} tiene una extensión inválida. Extensiones válidas {extensions}.',
                sizeError: "{file} es demasiado grande, el tamaño máximo de archivo es {sizeLimit}.",
                minSizeError: "{file} es demasiado pequeño, el tamaño mínimo de archivo es {minSizeLimit}.",
                emptyError: "{file} está vacío, por favor seleccione los archivos nuevamente sin él.",
                noFilesError: "No hay archivos para cargar",
                tooManyItemsError: "Demasiados articulos ({netItems}) sería cargado. Límite de artículo es {itemLimit}.",
                maxHeightImageError: 'La imagen es muy alta.',
                maxWidthImageError: "La imagen es muy amplia.",
                minHeightImageError: "La imagen no es lo suficientemente alta",
                minWidthImageError: "La imagen no es lo suficientemente amplia",
                retryFailTooManyItems: "Error de reintentos - ha alcanzado el límite de su archivo.",
                onLeave: "Los archivos se cargan, si se va ahora, la carga se cancelará.",
                unsupportedBrowserIos8Safari: "Error irrecuperable: este navegador no permite la carga de archivos de ningún tipo debido a errores graves en iOS8 Safari. Utilice iOS8 Chrome hasta que Apple resuelva estos problemas."
            },

			getResource: {
				professions: 1
			},

            getTypePromo: {
                promotion: 1,
                advertisement: 2
			},

			getTypeNotification: {
				module: 1,
				text: 2
			},

			getAddressee: {
				addressee_to: 1,
				global: 2,
				filters: 3
			},

            messages: {
                typeError: '{file} tiene una extensión inválida. Extensiones válidas {extensions}.',
                sizeError: "{file} es demasiado grande, el tamaño máximo de archivo es {sizeLimit}.",
                minSizeError: "{file} es demasiado pequeño, el tamaño mínimo de archivo es {minSizeLimit}.",
                emptyError: "{file} está vacío, por favor seleccione los archivos nuevamente sin él.",
                noFilesError: "No hay archivos para cargar",
                tooManyItemsError: "Demasiados artículos ({netItems}) sería cargado. Límite de artículo es {itemLimit}.",
                maxHeightImageError: 'La imagen es muy alta.',
                maxWidthImageError: "La imagen es muy amplia.",
                minHeightImageError: "La imagen no es lo suficientemente alta",
                minWidthImageError: "La imagen no es lo suficientemente amplia",
                retryFailTooManyItems: "Error de reintentos - ha alcanzado el límite de su archivo.",
                onLeave: "Los archivos se cargan, si se va ahora, la carga se cancelará.",
                unsupportedBrowserIos8Safari: "Error irrecuperable: este navegador no permite la carga de archivos de ningún tipo debido a errores graves en iOS8 Safari. Utilice iOS8 Chrome hasta que Apple resuelva estos problemas."
            },

            text: {
                fileInputTitle: 'Carga un archivo',
                failUpload: 'La carga falló',
                paused: 'Pausado',
                waitingForResponse: 'Procesando...'
            },

            confirmMessage: "Estás seguro que quieres eliminar {filename}?",

			getTypeID: {
                'cc': 1,
                'ce': 2,
                'nit': 3,
                'ti': 4,
                'p': 5,
                'rc': 6,
                'rut': 7,
                'nui': 8
            },
		};

	}).call(this);


	/**
	* Expose the class either via AMD, CommonJS or the global object
	*/
	if (typeof define === 'function' && define.amd) {
		define(function () {
		    return _tp;
		});
	}
	else if (typeof module === 'object' && module.exports){
		module.exports = _tp;
	}
	else {
		window._tp = _tp;
	}

}).call(this);

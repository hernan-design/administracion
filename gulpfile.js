'use strict';

let elixir = require('laravel-elixir'),
		bourbon = require('node-bourbon').includePaths;

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

let paths = {
	'libs': './resources/assets/libs/',
	'bootstrap': './resources/assets/libs/bootstrap-sass/assets/',
	'gentelella': './resources/assets/libs/gentelella/',
	'modules': './node_modules/'
}

elixir(function(mix) {
		// Sass compile
		// ------------
		mix.sass("app.scss", 'public/css/', {
			includePaths: [
				paths.libs,
				paths.bootstrap + 'stylesheets/',
				paths.gentelella + 'src/scss/',
				paths.gentelella + 'vendors/'
			].concat(bourbon)
		});

		// Minify css
		// ----------
		mix.styles([
			paths.gentelella + 'vendors/pnotify/dist/pnotify.css',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.buttons.css',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.nonblock.css',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.brighttheme.css',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.mobile.css',
			paths.modules + 'datatables.net-select-dt/css/select.dataTables.css',
			paths.gentelella + 'vendors/bootstrap/dist/css/bootstrap.min.css',
			paths.gentelella + 'vendors/bootstrap-daterangepicker/daterangepicker.css',
			paths.gentelella + 'vendors/select2/dist/css/select2.css',
			paths.gentelella + 'vendors/google-code-prettify/bin/prettify.min.css',
			paths.gentelella + 'vendors/dropzone/dist/min/dropzone.min.css',
			paths.gentelella + 'vendors/datatables.net-bs/css/dataTables.bootstrap.min.css',
			paths.gentelella + 'vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css',
			paths.gentelella + 'vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css',
			paths.gentelella + 'vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css',
			paths.gentelella + 'vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css',
			paths.gentelella + 'vendors/parsleyjs/src/parsley.css',
			paths.gentelella + 'vendors/dropzone/dist/dropzone.css',
			paths.gentelella + 'vendors/iCheck/skins/flat/green.css',
			paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
			paths.gentelella + 'build/css/custom.css',
			'../libs/fine-uploader/dist/fine-uploader-new.css',
			'../libs/jcrop/css/jquery.Jcrop.css',
			'../libs/image-uploader-crop/src/image-uploader-crop.css',
			'../libs/flexboxgrid/dist/flexboxgrid.css',
			'../libs/multiselect/css/multi-select.css',
			'../libs/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
			'../libs/jquery-ui/themes/base/base.css',
			'../libs/jquery-ui/themes/base/dialog.css',
			'../libs/jquery-ui/themes/base/draggable.css',
			'../libs/jquery-ui/themes/base/selectable.css',
			'../libs/jquery-ui/themes/base/tooltip.css',
			'../libs/jquery-ui/themes/base/theme.css',
			'../libs/jquery-ui/themes/base/all.css',
			'../libs/jquery-ui/themes/base/menu.css',
			'../libs/jquery-ui/themes/base/selectmenu.css',
			'../libs/jquery-ui/themes/base/autocomplete.css',
			'../libs/jquery.tagsinput/src/jquery.tagsinput.css',
			paths.modules + 'codemirror/lib/codemirror.css',
			paths.modules + 'codemirror/theme/mdn-like.css',
			paths.modules + 'codemirror/addon/hint/show-hint.css',
			paths.modules + 'react-summernote/dist/react-summernote.css',
		]);

		// Minify javascript
		mix.scripts([
			paths.gentelella + 'vendors/jquery/dist/jquery.min.js',
			paths.gentelella + 'vendors/bootstrap/dist/js/bootstrap.min.js',
			'../libs/underscore/underscore.js',
			'../libs/backbone/backbone.js',
			'../libs/backbone.paginator/lib/backbone.paginator.js',
			paths.modules + 'datatables.net/js/jquery.dataTables.min.js',
			paths.modules + 'datatables.net-select/js/dataTables.select.min.js',
			paths.gentelella + 'vendors/datatables.net-bs/js/dataTables.bootstrap.min.js',
			paths.gentelella + 'vendors/datatables.net-buttons/js/dataTables.buttons.min.js',
			paths.gentelella + 'vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js',
			paths.gentelella + 'vendors/datatables.net-buttons/js/buttons.flash.min.js',
			paths.gentelella + 'vendors/datatables.net-buttons/js/buttons.html5.min.js',
			paths.gentelella + 'vendors/datatables.net-buttons/js/buttons.print.min.js',
			paths.gentelella + 'vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js',
			paths.gentelella + 'vendors/datatables.net-keytable/js/dataTables.keyTable.min.js',
			paths.gentelella + 'vendors/datatables.net-responsive/js/dataTables.responsive.min.js',
			paths.gentelella + 'vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js',
			paths.gentelella + 'vendors/datatables.net-scroller/js/dataTables.scroller.min.js',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.js',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.buttons.js',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.nonblock.js',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.animate.js',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.mobile.js',
			paths.gentelella + 'vendors/pnotify/dist/pnotify.confirm.js',
			paths.gentelella + 'vendors/fastclick/lib/fastclick.js',
			paths.gentelella + 'vendors/bootstrap-progressbar/bootstrap-progressbar.js',
			paths.gentelella + 'vendors/iCheck/icheck.js',
			paths.gentelella + 'vendors/moment/moment.js',
			paths.gentelella + 'vendors/moment/locale/es.js',
			paths.gentelella + 'vendors/bootstrap-daterangepicker/daterangepicker.js',
			paths.gentelella + 'vendors/select2/dist/js/select2.full.js',
			paths.gentelella + 'vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js',
			paths.gentelella + 'vendors/google-code-prettify/src/prettify.js',
			paths.gentelella + 'vendors/jquery.hotkeys/jquery.hotkeys.js',
			paths.gentelella + 'vendors/fastclick/lib/fastclick.js',
			paths.gentelella + 'vendors/nprogress/nprogress.js',
			paths.gentelella + 'vendors/dropzone/dist/min/dropzone.min.js',
			paths.gentelella + 'vendors/autosize/dist/autosize.min.js',
			paths.gentelella + 'vendors/validator/validator.min.js',
			paths.gentelella + 'vendors/parsleyjs/dist/parsley.min.js',
			paths.gentelella + 'vendors/parsleyjs/dist/i18n/es.js',
			paths.gentelella + 'vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js',
			paths.gentelella + 'vendors/dropzone/dist/dropzone.js',
			paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
			paths.gentelella + 'build/js/custom.min.js',
			'../libs/fine-uploader/dist/all.fine-uploader.js',
			'../libs/fine-uploader/dist/jquery.fine-uploader.js',
			'../libs/wnumb/wNumb.js',
			'../libs/jcrop/js/jquery.Jcrop.js',
			'../libs/image-uploader-crop/src/image-uploader-crop.js',
			'../libs/jQuery.dotdotdot/src/jquery.dotdotdot.js',
			'../libs/nestable/jquery.nestable.js',
			'../libs/multiselect/js/jquery.multi-select.js',
			'../libs/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
	 		'../libs/jquery-ui/ui/data.js',
	 		'../libs/jquery-ui/ui/disable-selection.js',
	 		'../libs/jquery-ui/ui/form.js',
	 		'../libs/jquery-ui/ui/labels.js',
	 		'../libs/jquery-ui/ui/scroll-parent.js',
	 		'../libs/jquery-ui/ui/tabbable.js',
	 		'../libs/jquery-ui/ui/unique-id.js',
	 		'../libs/jquery-ui/ui/version.js',
			'../libs/jquery-ui/ui/widget.js',
			'../libs/jquery-ui/ui/effect.js',
			'../libs/jquery-ui/ui/position.js',
			'../libs/jquery-ui/ui/widgets/menu.js',
			'../libs/jquery-ui/ui/widgets/button.js',
	 		'../libs/jquery-ui/ui/widgets/mouse.js',
			'../libs/jquery-ui/ui/safe-active-element.js',
			'../libs/jquery-ui/ui/safe-blur.js',
			'../libs/jquery-ui/ui/focusable.js',
			'../libs/jquery-ui/ui/plugin.js',
			'../libs/jquery-ui/ui/widgets/draggable.js',
			'../libs/jquery-ui/ui/keycode.js',
			'../libs/jquery-ui/ui/widgets/selectable.js',
			'../libs/jquery-ui/ui/widgets/tabs.js',
			'../libs/jquery-ui/ui/widgets/selectmenu.js',
			'../libs/jquery-ui/ui/widgets/autocomplete.js',
		 	'../libs/jquery-ui/ui/widgets/dialog.js',
			'../libs/jquery.tagsinput/src/jquery.tagsinput.js',
			paths.modules + 'codemirror/lib/codemirror.js',
			paths.modules + 'codemirror/addon/hint/show-hint.js',
			paths.modules + 'codemirror/addon/hint/xml-hint.js',
			paths.modules + 'codemirror/addon/hint/html-hint.js',
			paths.modules + 'codemirror/mode/xml/xml.js',
			paths.modules + 'echarts/dist/echarts.js',
		], 'public/js/vendor.js')
		.scripts([
			'helpers/misc.js',
			'helpers/routes.js',
			'helpers/states.js',
			'models/*.js',
			'collections/*.js',
			'views/**/**.js',
			'routers/*.js',
			'init.js'
		], 'public/js/app.js');

		// browserify bundle
		if(elixir.config.production) {
			elixir.config.js.browserify.transformers.push({
				name: 'envify',
				options: {
					NODE_ENV: 'production'
				}
			});
			elixir.config.js.browserify.transformers.push({ name: 'uglifyify' });
			elixir.config.js.browserify.plugins.push({ name: 'bundle-collapser/plugin' });
		}
		mix.browserify('/realtime/main.js', elixir.config.publicPath +'/js/realtime.js', elixir.config.assetsPath);
		mix.browserify('/js/components/main.js', elixir.config.publicPath +'/js/component.js', elixir.config.assetsPath);

		// Cache busting
		// -------------
		mix.version([
			'css/all.css', 
			'css/app.css', 
			'js/app.js', 
			'js/vendor.js', 
			'js/component.js',
			'js/realtime.js'
		]);

		// Copy files
		// ----------
		mix.copy( 'resources/assets/libs/font-awesome/fonts', 'public/build/css/fonts');
		mix.copy(paths.gentelella + 'vendors/iCheck/skins/flat/green**.png', 'public/build/css');
		mix.copy(paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img/bootstrap-colorpicker/saturation.png', 'public/build/img/bootstrap-colorpicker/saturation.png');
		mix.copy(paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img/bootstrap-colorpicker/hue.png', 'public/build/img/bootstrap-colorpicker/hue.png');
		mix.copy(paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img/bootstrap-colorpicker/alpha.png', 'public/build/img/bootstrap-colorpicker/alpha.png');
		mix.copy(paths.bootstrap + 'fonts/bootstrap', 'public/build/css/fonts');
		mix.copy(paths.modules + 'react-summernote/dist/**.{eot,ttf,woff}', 'public/build/css/fonts');

		// copy fineuploader images
		mix.copy('resources/assets/libs/fine-uploader/dist/placeholders/**.{png,gif}', 'public/img/');
		mix.copy('resources/assets/libs/fine-uploader/dist/loading.gif', 'public/img/loading.gif');
		mix.copy('resources/assets/libs/fine-uploader/dist/processing.gif', 'public/img/processing.gif');
		mix.copy('resources/assets/libs/fine-uploader/dist/edit.gif', 'public/build/css/edit.gif');

		// copy jcrop image
		mix.copy('resources/assets/libs/jcrop/css/Jcrop.gif', 'public/build/css/Jcrop.gif');

		// copy multiselect image
		mix.copy('resources/assets/libs/multiselect/img/switch.png', 'public/build/img/switch.png');
});
'use strict';

let gulp = require('gulp');
let sass = require('gulp-dart-sass');
let concat = require('gulp-concat');
let uglify = require('gulp-uglify');
let rev = require('gulp-rev');
let copy = require('gulp-copy');
let browserify = require('browserify');
let source = require('vinyl-source-stream');
let buffer = require('vinyl-buffer');
let sourcemaps = require('gulp-sourcemaps');
let envify = require('envify/custom');
let uglifyify = require('uglifyify');
let babelify = require('babelify');
let bundleCollapser = require('bundle-collapser/plugin');
let bourbon = [__dirname + '/node_modules/bourbon/app/assets/stylesheets'];
let fs = require('fs');
let stream = require('stream');
let path = require('path');

let paths = {
	'libs': './resources/assets/libs/',
	'bootstrap': './resources/assets/libs/bootstrap-sass/assets/',
	'gentelella': './resources/assets/libs/gentelella/',
	'modules': './node_modules/'
};

gulp.task('sass', function() {
	return gulp.src('resources/assets/sass/app.scss')
		.pipe(sourcemaps.init())
		.pipe(sass({
			sassOptions: {
				silenceDeprecations: ['import', 'slash-div']
			},
			includePaths: [
				paths.libs,
				paths.bootstrap + 'stylesheets/',
				paths.gentelella + 'src/scss/',
				paths.gentelella + 'vendors/',
				'node_modules/bourbon/app/assets/stylesheets'
			]
		}).on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('public/css/'));
});

gulp.task('styles', function() {
	return gulp.src([
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
		paths.libs + 'jcrop/css/jquery.Jcrop.css',
		paths.libs + 'image-uploader-crop/src/image-uploader-crop.css',
		paths.libs + 'flexboxgrid/dist/flexboxgrid.css',
		paths.libs + 'multiselect/css/multi-select.css',
		paths.libs + 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
		paths.libs + 'jquery-ui/themes/base/base.css',
		paths.libs + 'jquery-ui/themes/base/dialog.css',
		paths.libs + 'jquery-ui/themes/base/draggable.css',
		paths.libs + 'jquery-ui/themes/base/selectable.css',
		paths.libs + 'jquery-ui/themes/base/theme.css',
		paths.libs + 'jquery-ui/themes/base/all.css',
		paths.libs + 'jquery-ui/themes/base/menu.css',
		paths.libs + 'jquery-ui/themes/base/selectmenu.css',
		paths.libs + 'jquery.tagsinput/src/jquery.tagsinput.css',
		paths.modules + 'codemirror/lib/codemirror.css',
		paths.modules + 'codemirror/theme/mdn-like.css',
		paths.modules + 'codemirror/addon/hint/show-hint.css',
		paths.modules + 'react-summernote/dist/react-summernote.css',
	], { allowEmpty: true })
	.pipe(concat('all.css'))
	.pipe(gulp.dest('public/css/'));
});

gulp.task('vendor-scripts', function() {
	return gulp.src([
		paths.gentelella + 'vendors/jquery/dist/jquery.min.js',
		paths.gentelella + 'vendors/bootstrap/dist/js/bootstrap.min.js',
		paths.libs + 'underscore/underscore.js',
		paths.libs + 'backbone/backbone.js',
		paths.libs + 'backbone.paginator/lib/backbone.paginator.js',
		paths.modules + 'datatables.net/js/jquery.dataTables.min.js',
		paths.modules + 'datatables.net-select/js/dataTables.select.min.js',
		paths.gentelella + 'vendors/datatables.net-bs/js/dataTables.bootstrap.min.js',
		paths.gentelella + 'vendors/datatables.net-buttons/js/buttons.bootstrap.min.js',
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
		paths.libs + 'wnumb/wNumb.js',
		paths.libs + 'jcrop/js/jquery.Jcrop.js',
		paths.libs + 'image-uploader-crop/src/image-uploader-crop.js',
		paths.libs + 'jQuery.dotdotdot/src/jquery.dotdotdot.js',
		paths.libs + 'nestable/jquery.nestable.js',
		paths.libs + 'multiselect/js/jquery.multi-select.js',
		paths.libs + 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
		paths.libs + 'jquery-ui/ui/data.js',
		paths.libs + 'jquery-ui/ui/disable-selection.js',
		paths.libs + 'jquery-ui/ui/form.js',
		paths.libs + 'jquery-ui/ui/scroll-parent.js',
		paths.libs + 'jquery-ui/ui/tabbable.js',
		paths.libs + 'jquery-ui/ui/unique-id.js',
		paths.libs + 'jquery-ui/ui/version.js',
		paths.libs + 'jquery-ui/ui/widget.js',
		paths.libs + 'jquery-ui/ui/effect.js',
		paths.libs + 'jquery-ui/ui/position.js',
		paths.libs + 'jquery-ui/ui/widgets/menu.js',
		paths.libs + 'jquery-ui/ui/widgets/button.js',
		paths.libs + 'jquery-ui/ui/widgets/mouse.js',
		paths.libs + 'jquery-ui/ui/safe-active-element.js',
		paths.libs + 'jquery-ui/ui/safe-blur.js',
		paths.libs + 'jquery-ui/ui/focusable.js',
		paths.libs + 'jquery-ui/ui/plugin.js',
		paths.libs + 'jquery-ui/ui/widgets/draggable.js',
		paths.libs + 'jquery-ui/ui/keycode.js',
		paths.libs + 'jquery-ui/ui/widgets/selectable.js',
		paths.libs + 'jquery-ui/ui/widgets/tabs.js',
		paths.libs + 'jquery-ui/ui/widgets/selectmenu.js',
		paths.libs + 'jquery-ui/ui/widgets/dialog.js',
		paths.libs + 'jquery.tagsinput/src/jquery.tagsinput.js',
		paths.modules + 'codemirror/lib/codemirror.js',
		paths.modules + 'codemirror/addon/hint/show-hint.js',
		paths.modules + 'codemirror/addon/hint/xml-hint.js',
		paths.modules + 'codemirror/mode/xml/xml.js',
		paths.modules + 'echarts/dist/echarts.js',
	], { allowEmpty: true })
	.pipe(concat('vendor.js'))
	.pipe(gulp.dest('public/js/'));
});

gulp.task('app-scripts', function() {
	return gulp.src([
		'helpers/misc.js',
		'helpers/routes.js',
		'helpers/states.js',
		'models/*.js',
		'collections/*.js',
		'views/**/**.js',
		'routers/*.js',
		'init.js'
	], { allowEmpty: true })
	.pipe(concat('app.js'))
	.pipe(gulp.dest('public/js/'));
});

// Cada bundle de Browserify necesita su propio proceso — no se puede reutilizar un stream
function makeBrowserifyBundle(entry, outputName) {
	let b = browserify({
		entries: [entry],
		debug: true,
		transform: [
			babelify.configure({ presets: ['env', 'react', 'stage-3'] }),
			envify({ NODE_ENV: process.env.NODE_ENV || 'development' })
		]
	});
	if (process.env.NODE_ENV === 'production') {
		b.transform(uglifyify);
		b.plugin(bundleCollapser);
	}
	return b.bundle()
		.on('error', function(err) { console.error(err); })
		.pipe(source(outputName))
		.pipe(buffer())
		.pipe(gulp.dest('public/js/'));
}

gulp.task('browserify-realtime', function() {
	if (!fs.existsSync('./resources/assets/realtime/main.js')) return Promise.resolve();
	return makeBrowserifyBundle('./resources/assets/realtime/main.js', 'realtime.js');
});

gulp.task('browserify-components', function() {
	if (!fs.existsSync('./resources/assets/js/components/main.js')) return Promise.resolve();
	return makeBrowserifyBundle('./resources/assets/js/components/main.js', 'component.js');
});

gulp.task('browserify', gulp.parallel('browserify-realtime', 'browserify-components'));

gulp.task('version', function() {
	return gulp.src([
		'public/css/all.css',
		'public/css/app.css',
		'public/js/app.js',
		'public/js/vendor.js',
		'public/js/component.js',
		'public/js/realtime.js'
	], { base: 'public' })
	.pipe(gulp.dest('public/'));
});

gulp.task('copy', function() {
	return gulp.src(paths.libs + 'font-awesome/fonts/**/*', { allowEmpty: true, base: paths.libs + 'font-awesome/fonts' })
		.pipe(gulp.dest('public/build/css/fonts/'));
	gulp.src(paths.gentelella + 'vendors/iCheck/skins/flat/green**.png', { allowEmpty: true, base: paths.gentelella + 'vendors/iCheck/skins/flat' })
		.pipe(gulp.dest('public/build/css'));
	gulp.src(paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img/bootstrap-colorpicker/saturation.png', { allowEmpty: true, base: paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img' })
		.pipe(gulp.dest('public/build/img/bootstrap-colorpicker/'));
	gulp.src(paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img/bootstrap-colorpicker/hue.png', { allowEmpty: true, base: paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img' })
		.pipe(gulp.dest('public/build/img/bootstrap-colorpicker/'));
	gulp.src(paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img/bootstrap-colorpicker/alpha.png', { allowEmpty: true, base: paths.gentelella + 'vendors/mjolnic-bootstrap-colorpicker/dist/img' })
		.pipe(gulp.dest('public/build/img/bootstrap-colorpicker/'));
	gulp.src(paths.bootstrap + 'fonts/**/*', { allowEmpty: true, base: paths.bootstrap + 'fonts' })
		.pipe(gulp.dest('public/build/css/fonts/'));
	gulp.src(paths.modules + 'react-summernote/dist/*.{eot,ttf,woff}', { allowEmpty: true, base: paths.modules + 'react-summernote/dist' })
		.pipe(gulp.dest('public/build/css/fonts/'));
	gulp.src(paths.libs + 'fine-uploader/dist/placeholders/*.{png,gif}', { allowEmpty: true, base: paths.libs + 'fine-uploader/dist/placeholders' })
		.pipe(gulp.dest('public/img/'));
	gulp.src(paths.libs + 'fine-uploader/dist/loading.gif', { allowEmpty: true, base: paths.libs + 'fine-uploader/dist' })
		.pipe(gulp.dest('public/img/'));
	gulp.src(paths.libs + 'fine-uploader/dist/processing.gif', { allowEmpty: true, base: paths.libs + 'fine-uploader/dist' })
		.pipe(gulp.dest('public/build/img/'));
	gulp.src(paths.libs + 'fine-uploader/dist/edit.gif', { allowEmpty: true, base: paths.libs + 'fine-uploader/dist' })
		.pipe(gulp.dest('public/build/css/'));
	gulp.src(paths.libs + 'jcrop/css/Jcrop.gif', { allowEmpty: true, base: paths.libs + 'jcrop/css' })
		.pipe(gulp.dest('public/build/css/'));
	gulp.src(paths.libs + 'multiselect/img/switch.png', { allowEmpty: true, base: paths.libs + 'multiselect/img' })
		.pipe(gulp.dest('public/build/img/'));
});

gulp.task('default', gulp.series(
	gulp.parallel('sass', 'styles', 'vendor-scripts', 'app-scripts', 'browserify', 'copy'),
	'version'
));

/**
 * https://www.alsacreations.com/tuto/lire/1686-introduction-a-gulp.html
 */

var gulp		= require('gulp');
var plugins		= require('gulp-load-plugins')();
var indi		= './indi';

/** CSS **/

/* Reordonner declarations, reindentation et reformatage */
gulp.task('cleanCss', function () {
	return gulp.src(indi + '/web/css/*.css')
			.pipe(plugins.csscomb())
			.pipe(plugins.cssbeautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/web/css/'));
	});

gulp.task('cleanCssMap', function () {
	return gulp.src(indi + '/web/css/map/*.css')
			.pipe(plugins.csscomb())
			.pipe(plugins.cssbeautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/web/css/map/'));
	});

/* Minifier */
gulp.task('minifyCss', function () {
	return gulp.src(indi + '/web/css/*.css')
			.pipe(plugins.csso())
			.pipe(gulp.dest(indi + '/web/css/'));
	});

gulp.task('minifyCssMap', function () {
	return gulp.src(indi + '/web/css/map/*.css')
			.pipe(plugins.csso())
			.pipe(gulp.dest(indi + '/web/css/map/'));
	});


/** JS **/

/* Reordonner decla, reindentation et reformatage */
gulp.task('cleanJS', function () {
	return gulp.src(indi + '/web/js/*.js')
			.pipe(plugins.beautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/web/js/'));
	});

gulp.task('cleanJSMap', function () {
	return gulp.src(indi + '/web/js/map/*.js')
			.pipe(plugins.beautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/web/js/map/'));
	});


/* Minifier */
gulp.task('minifyJS', function () {
	return gulp.src(indi + '/web/js/*.js')
			.pipe(plugins.uglify())
			.pipe(gulp.dest(indi + '/web/js/'));
	});

gulp.task('minifyJSMap', function () {
	return gulp.src(indi + '/web/js/map/*.js')
			.pipe(plugins.uglify())
			.pipe(gulp.dest(indi + '/web/js/map/'));
	});



// Run 'gulp indi' at www/
gulp.task('indi', ['cleanCss', 'cleanCssMap', 'minifyCss', 'minifyCssMap', 'cleanJS', 'cleanJSMap', 'minifyJS', 'minifyJSMap']);

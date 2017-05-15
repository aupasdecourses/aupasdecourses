/**
 * https://www.alsacreations.com/tuto/lire/1686-introduction-a-gulp.html
 */

var gulp		= require('gulp');
var plugins		= require('gulp-load-plugins')();
var indi		= './indi';

/* Reordonner declarations CSS + reindentation et reformatage */
gulp.task('css', function () {
	return gulp.src([
			indi + '/web/css/*.css',
			indi + '/web/css/map/*.css'
			])
			.pipe(plugins.csscomb())
			.pipe(plugins.cssbeautify({indent: '  '}))
			.pipe(gulp.dest(indi + '/web/css/'))
			.pipe(gulp.dest(indi + '/web/css/map/'));
	});


/* Minification CSS */
gulp.task('minify', function () {
	return gulp.src([
			indi + '/web/css/*.css',
			indi + '/web/css/map/*.css'
			])
			.pipe(plugins.csso())
			.pipe(gulp.dest(indi + '/web/css/'))
			.pipe(gulp.dest(indi + '/web/css/map/'));
	});


// Run gulp indi at www/
gulp.task('indi', ['css', 'minify']);

/**
 * https://www.alsacreations.com/tuto/lire/1686-introduction-a-gulp.html
 */

var gulp		= require('gulp');
var cssbeautify = require('gulp-cssbeautify');
var csscomb		= require('gulp-csscomb');
var csso		= require('gulp-csso');

var indi = './indi';

/* Reordonner declarations CSS + reindentation et reformatage */
gulp.task('css', function () {
	return gulp.src([
			indi + '/web/css/*.css',
			indi + '/web/css/map/*.css'
			])
			.pipe(plugins.csscomb())
			.pipe(plugins.cssbeautify({indent: '  '})); // choix d'indent de 2 espaces
	});


/* Minification CSS */
gulp.task('minify', function () {
	return gulp.src([
			indi + '/web/css/*.css',
			indi + '/web/css/map/*.css'
			])
			.pipe(plugins.csso());
	});



// execute avec 'gulp css' et 'gulp minify'

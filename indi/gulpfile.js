/**
 * https://www.alsacreations.com/tuto/lire/1686-introduction-a-gulp.html
 */

<<<<<<< HEAD
var gulp               = require('gulp');
var plugins            = require('gulp-load-plugins')();
var indi               = './web';


/** CSS à minifier obligatoirement UNE SEULE FOIS
 *     On les minifie car on écrira JAMAIS dedans
=======
var gulp		= require('gulp');
var plugins		= require('gulp-load-plugins')();
var indi		= './web';


/** CSS à minifier obligatoirement UNE SEULE FOIS
 *	On les minifie car on écrira JAMAIS dedans
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6
 **/
gulp.task('minifyRequiredCSSMap', function () {
	return gulp.src([
			indi + '/css/map/jquery-ui-1.10.0.custom.css',
			indi + '/css/map/leaflet.awesome-markers.css',
			indi + '/css/map/leaflet-routing-machine.css',
			indi + '/css/map/leaflet-search.css',
			indi + '/css/map/leaflet-search.mobile.css',
			indi + '/css/map/MarkerCluster.css',
			indi + '/css/map/MarkerCluster.Default.css'
<<<<<<< HEAD
	])
		.pipe(plugins.csso())
		.pipe(gulp.dest(indi + '/css/map/'));
});

/** JS à minifier obligatoirement UNE SEULE FOIS
 *     Meme systeme que le CSS. On ecrira JAMAIS dedans */
=======
			])
			.pipe(plugins.csso())
			.pipe(gulp.dest(indi + '/css/map/'));
});

/** JS à minifier obligatoirement UNE SEULE FOIS
 *	Meme systeme que le CSS. On ecrira JAMAIS dedans */
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6
gulp.task('minifyRequiredJS', function () {
	return gulp.src([
			indi + '/js/jquery.fileupload.js',
			indi + '/js/jquery.iframe-transport.js',
			indi + '/js/jquery.ui.widget.js'
<<<<<<< HEAD
	])
		.pipe(plugins.uglify())
		.pipe(gulp.dest(indi + '/js/'));
=======
			])
			.pipe(plugins.uglify())
			.pipe(gulp.dest(indi + '/js/'));
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6
});

/** JS à minifier obligatoirement (cartes) */
gulp.task('minifyRequiredJSMap', function () {
	return gulp.src([
			indi + '/js/map/Control.Geocoder.js',
			indi + '/js/map/leaflet.awesome-markers.js',
			indi + '/js/map/leaflet.markercluster-src.js',
			indi + '/js/map/leaflet-routing-machine.js',
			indi + '/js/map/leaflet-search.js',
			indi + '/js/map/underscore.js'
<<<<<<< HEAD
	])
		.pipe(plugins.uglify())
		.pipe(gulp.dest(indi + '/js/map/'));
=======
			])
			.pipe(plugins.uglify())
			.pipe(gulp.dest(indi + '/js/map/'));
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6
});



/** Reordonner les proprietes CSS 
<<<<<<< HEAD
 *     Reindenter et reformater */
gulp.task('formatCSS', function () {
	return gulp.src(indi + '/css/style.css')
		.pipe(plugins.csscomb())
		.pipe(plugins.cssbeautify({indent_size: 2}))
		.pipe(gulp.dest(indi + '/css/'));
});
=======
 *	Reindenter et reformater */
gulp.task('formatCSS', function () {
	return gulp.src(indi + '/css/style.css')
			.pipe(plugins.csscomb())
			.pipe(plugins.cssbeautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/css/'));
	});
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6

/** Reindenter et reformater le JS */
gulp.task('formatJS', function () {
	return gulp.src([
			indi + '/js/datepicker.js',
			indi + '/js/digest-gallery.js',
			indi + '/js/input-compute.js',
			indi + '/js/input-ticket.js'
<<<<<<< HEAD
	])
		.pipe(plugins.beautify({indent_size: 2}))
		.pipe(gulp.dest(indi + '/js/'));
});
=======
			])
			.pipe(plugins.beautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/js/'));
	});
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6

gulp.task('formatJSMap', function () {
	return gulp.src([
			indi + '/js/map/clients',
			indi + '/js/map/commercants.js'
<<<<<<< HEAD
	])
		.pipe(plugins.beautify({indent_size: 2}))
		.pipe(gulp.dest(indi + '/js/map/'));
});
=======
			])
			.pipe(plugins.beautify({indent_size: 2}))
			.pipe(gulp.dest(indi + '/js/map/'));
	});
>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6



// Run UNE SEULE FOIS 'gulp required' : 
gulp.task('required', ['minifyRequiredCSSMap', 'minifyRequiredJS', 'minifyRequiredJSMap']);

// Run de temps en temps pour reindent et reformat 'gulp format' :
gulp.task('format', ['formatCSS', 'formatJS', 'formatJSMap']);
<<<<<<< HEAD
=======

>>>>>>> 00077ff25fd8f979707398e2077d7b7b500456d6

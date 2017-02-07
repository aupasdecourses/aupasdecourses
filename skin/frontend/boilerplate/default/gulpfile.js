/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2011-2014 Webcomm Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

// Load plugins
var
  gulp         = require('gulp'),
  sass         = require('gulp-sass'),
  minifycss    = require('gulp-minify-css'),
  uglify       = require('gulp-uglify'),
  concat       = require('gulp-concat'),
  cache        = require('gulp-cache');

const autoprefixer = require('gulp-autoprefixer');

var config = {

  // Should CSS & JS be compressed?
  minifyCss: true,
  uglifyJS: true

}

// CSS
'use strict';

gulp.task('css', function () {
    var styles = [
        'node_module/bootstrap/dist/css/bootstrap.min.css'
        //'node_modules/font-awesome/font-awesome.scss'
    ];
    
    return gulp.src([
        'node_module/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/font-awesome/font-awesome.scss'])
    .pipe(concat('styles.css'))
    .pipe(gulp.dest('dist/css'));
});
 
gulp.task('sass', function () {
  return gulp.src('./sass/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
    .pipe(gulp.dest('dist/css'));
});

// JS
gulp.task('js', function() {
  var scripts = [
    'node_modules/jquery/dist/jquery.js',
    'node_modules/bootstrap/js/transition.js',
    'node_modules/bootstrap/js/collapse.js',
    'node_modules/bootstrap/js/carousel.js',
    'node_modules/bootstrap/js/dropdown.js',
    'node_modules/bootstrap/js/modal.js',
    'js/script.js'
  ];

  var stream = gulp
    .src(scripts)
    .pipe(concat('script.js'));

  if (config.uglifyJS === true) {
    stream.pipe(uglify());
  }

  return stream
    .pipe(gulp.dest('dist/js'));
});

// Images
gulp.task('images', function() {
  return gulp
    .src('images/**/*')
    .pipe(gulp.dest('dist/images'));
});

// Fonts
gulp.task('fonts', function() {
  return gulp
    .src([
      'node_modules/bootstrap/fonts/**/*',
      'node_modules/font-awesome/fonts/**/*'
    ])
    .pipe(gulp.dest('dist/fonts'));
});

// Watch
gulp.task('watch', function() {

  // Watch .scss files
  gulp.watch('sass/**/*.scss', ['sass']);

  // Watch .js files
  gulp.watch('js/**/*.js', ['js']);

  // Watch image files
  gulp.watch('images/**/*', ['images']);

});

gulp.task('default', [ 'css', 'sass', 'js', 'images', 'fonts', 'watch' ] );

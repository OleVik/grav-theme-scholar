const gulp = require('gulp');
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const sourcemaps = require('gulp-sourcemaps');
const log = require('fancy-log');

const sassSourceFile = 'scss/normal.scss';
const outputFolder = 'css';
const watchedResources = 'scss/**/*';

gulp.task('scss', function (done) {
  gulp.src(sassSourceFile)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', function (err) {
      log.error(err.message);
    }))
    .pipe(postcss([autoprefixer, cssnano]))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(outputFolder))
    .on('end', done);
});

gulp.task('watch', gulp.series('scss', function (done) {
  gulp.watch(watchedResources, gulp.parallel('scss'));
  done();
}));

gulp.task('default', gulp.series('watch', function () {}));
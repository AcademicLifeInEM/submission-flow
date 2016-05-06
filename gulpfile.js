/* eslint-env node, es6 */
const gulp = require('gulp');
const browserSync = require('browser-sync').create();


gulp.task('default', () =>
    gulp.src([
        'submission-flow.php',
        'inc/**/*.*',
    ], { base: './' })
    .pipe(gulp.dest('dist'))
);

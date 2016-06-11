/* eslint-env node, es6 */
const gulp = require('gulp');
const browserSync = require('browser-sync').create();

const stylus        = require('gulp-stylus');
const poststylus    = require('poststylus');
const autoprefixer  = require('autoprefixer')({ browsers: ['last 2 versions'] });
const sourcemaps    = require('gulp-sourcemaps');

const uglify        = require('gulp-uglify');

gulp.task('static', () =>
    gulp.src([
        'submission-flow.php',
        'inc/**/*.*',
        '!inc/**/*.styl',
    ], { base: './' })
    .pipe(gulp.dest('dist'))
);

gulp.task('styles', () =>
    gulp.src([
        'inc/**/*.styl',
    ], { base: '.', })
    .pipe(sourcemaps.init())
    .pipe(stylus({
        use: [ poststylus([autoprefixer]), ],
        compress: true,
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('dist'))
);

gulp.task('styles:build', () =>
    gulp.src([
        'inc/**/*.styl',
    ], { base: '.', })
    .pipe(stylus({
        use: [ poststylus([autoprefixer]), ],
        compress: true,
    }))
    .pipe(gulp.dest('dist'))
);

gulp.task('js', () =>
    gulp.src([
        'dist/**/*.js',
    ])
    .pipe(uglify({
        compress: {
            'dead_code': true,
            'unused': true,
            'drop_debugger': true,
            'drop_console': true,
        }
    }))
    .pipe(gulp.dest('./dist/'))
);

gulp.task('default', gulp.parallel('static', 'styles'));
gulp.task('build', gulp.series(
    gulp.parallel('static', 'styles:build'),
    'js'
));

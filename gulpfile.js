var gulp        = require('gulp'),

    concat      = require("gulp-concat"),
    rename      = require("gulp-rename"),
    uglify      = require("gulp-uglify"),
    minifyCSS   = require("gulp-clean-css"),
    sass        = require('gulp-sass'),
    prefixer    = require('gulp-autoprefixer')
    ;

var path = {
    js: {
        src: "assets/",
        dest: "assets/js/"
    },
    css: {
        src: "assets/",
        dest: "assets/css/"
    },
    sass: {
        dest: 'assets/css/',
        src: 'assets/scss/',
    },
    dragula: {
        dest: 'assets/vendor/',
        src: 'node_modules/dragula/dist/',
    }
};

var files = {
    js: [
        // path.js.src + "vendor/isotope.pkgd.js",
        // path.js.src + "vendor/simple-lightbox.js",
        // path.js.src + "js/gallery.js",
    ],
    css: [
        // path.css.src + "vendor/simple-lightbox.css",
        // path.css.src + "css/gallery.css",
    ],
    sass: [
        path.sass.src + "frontpage.scss",
    ],
    dragula: [
        path.dragula.src + "dragula.min.css",
        path.dragula.src + "dragula.min.js",
    ],
};

// //Create a js file
// gulp.task("js", function () {
//     return gulp.src(src.js)
//      .pipe(concat("iffgallery.js"))
//      .pipe(uglify())
//      .pipe(gulp.dest(path.js.dest));
// });

// //Create a js file
// gulp.task("css", function () {
//     return gulp.src(src.css)
//      .pipe(concat("iffgallery.css"))
//      .pipe(minifyCSS())
//      .pipe(gulp.dest(path.css.dest));
// });

// Copy dragula
gulp.task("dragula", function () {
    return gulp.src(files.dragula)
     .pipe(gulp.dest(path.dragula.dest));
});

//Create css files
gulp.task("sass", function () {
    return gulp.src(files.sass)
        .pipe(sass({outputStyle: 'nested'}).on('error', sass.logError))
        .pipe(prefixer({
            browsers: ['last 2 versions', 'ie >= 11', '>= 1%'],
            cascade: true
        }))
        // .pipe(minifyCSS())
        .pipe(rename('frontpage.css'))
        .pipe(gulp.dest(path.sass.dest));
});


// watch sass folder
gulp.task("watch:sass", function () {
    gulp.watch(files.sass, ['sass']);
});

// gulp.task('build', ['js', 'css']);
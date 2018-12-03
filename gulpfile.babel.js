// Imports plugins

import gulp            from 'gulp'
import gulpLoadPlugins from 'gulp-load-plugins'
import browserSync     from 'browser-sync'
import babelify        from 'babelify'
import browserify      from 'browserify'
import source          from 'vinyl-source-stream'
import buffer          from 'vinyl-buffer'
import watchify        from 'watchify'

// Launch plugins loader

const $ = gulpLoadPlugins()

// Create session

browserSync.create()

// Build paths

const server  = 'htdocs'
const folders = process.cwd().split('/')
const project = `${folders[folders.length - 1]}/`

let local = ''

const buildLocal = () => {
  let index = 0
  for (const folder of folders) {
    if (folder == server) {
      index = folders.indexOf(server)
      for (let i = index + 1 ; i < folders.length ; i++) {
        if (folders[i] != project) local += `${folders[i]}/`
        else break
      }
      break
    }
  }
}
buildLocal()

const config = {
  public  : 'public/',
  sources : 'sources/'
}

const message = {
  compiled   : '<%= file.relative %>: file compiled',
  exported   : '<%= file.relative %>: file exported',
  transpiled : '<%= file.relative %>: file transpiled',
  minified   : '<%= file.relative %>: file minified',
  cleaned    : '<%= file.relative %>: file cleaned',
  error      : '<%= error.message %>'
}

/**
 * 
 * Development
 * 
 */

// Server

gulp.task('server', ['assets', 'styles', 'scripts'], () => {
  browserSync.init({
    proxy   : `http://localhost/${local}public/`,
    browser : 'Google Chrome'
  })
  gulp.watch(`${config.src}assets/**/*.*`, ['assets'])
  gulp.watch([
    `${config.src}scss/**/*.scss`,
    `${config.src}scss/*.scss`
  ], ['styles'])
})

// Assets

gulp.task('assets', () => {
  return gulp.src(`${config.sources}assets/**/*.*`)
    .pipe($.plumber())
    .on('error', $.notify.onError({
      title   : 'Assets',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe(gulp.dest(`${config.public}assets/`))
    .pipe(browserSync.stream())
    .pipe($.notify({
      title   : 'Assets',
      message : message.exported,
      sound   : 'beep'
    }))
})

// Styles

gulp.task('styles', () => {
  return gulp.src(`${config.sources}styles/app.scss`)
    .pipe($.plumber())
    .pipe($.sourcemaps.init({ loadMaps: true }))
    .pipe($.sass())
    .on('error', $.notify.onError({
      title   : 'Styles',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe($.sourcemaps.write('./'))
    .pipe(gulp.dest(`${config.public}styles/`))
    .pipe(browserSync.stream())
    .pipe($.notify({
      title   : 'Styles',
      message : message.compiled,
      sound   : 'beep'
    }))
})

// Scripts

let bundler = null

const bundle = () => {
  bundler.bundle()
    .pipe($.plumber())
    .on('error', $.notify.onError({
      title   : 'Scripts',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe(source('app.js'))
    .pipe(buffer())
    .pipe($.sourcemaps.init({ loadMaps: true }))
    .pipe($.sourcemaps.write('./'))
    .pipe(gulp.dest(`${config.public}scripts/`))
    .pipe(browserSync.stream())
    .pipe($.notify({
      title   : 'Scripts',
      message : message.transpiled,
      sound   : 'beep'
    }))
}

gulp.task('scripts', () => {
  bundler = browserify({
    entries : `${config.sources}scripts/app.js`,
    debug   : true,
    paths   : ['./node_modules', `${config.sources}scripts/`]
  }).transform(babelify)
  bundler.plugin(watchify)
  bundler.on('update', bundle)
  bundle()
})

/**
 * 
 * Production
 * 
 */

// CSS

gulp.task('css', () => {
  return gulp.src(`${config.public}styles/app.css`)
    .pipe($.cssnano())
    .on('error', $.notify.onError({
      title   : 'Styles',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe(gulp.dest(`${config.public}styles/`))
    .pipe($.notify({
      title   : 'Styles',
      message : message.minified,
      sound   : 'beep'
    }))
})

// JS

gulp.task('js', () => {
  return gulp.src(`${config.public}scripts/app.js`)
    .pipe($.uglify())
    .on('error', $.notify.onError({
      title   : 'Scripts',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe(gulp.dest(`${config.public}scripts/`))
    .pipe($.notify({
      title   : 'Scripts',
      message : message.minified,
      sound   : 'beep'
    }))
})

// Images

gulp.task('img', () => {
  return gulp.src(`${config.public}assets/images/*.+(png|jpg|jpeg|gif|svg)`)
    .pipe($.imagemin())
    .on('error', $.notify.onError({
      title   : 'Images',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe(gulp.dest(`${config.public}assets/images/`))
    .pipe($.notify({
      title   : 'Images',
      message : message.minified,
      sound   : 'beep'
    }))
})

// Maps

gulp.task('maps', () => {
  return gulp.src([
    `${config.public}scripts/app.js.map`,
    `${config.public}styles/app.css.map`
  ])
    .pipe($.clean({
      force: true,
      read: false
    }))
    .on('error', $.notify.onError({
      title   : 'Maps',
      message : message.error,
      sound   : 'beep'
    }))
    .pipe($.notify({
      title   : 'Maps',
      message : message.cleaned,
      sound   : 'beep'
    }))
})

/**
 * 
 * Run
 * 
 */

gulp.task('default', ['server'])
gulp.task('prod', ['css', 'js', 'img', 'maps'])
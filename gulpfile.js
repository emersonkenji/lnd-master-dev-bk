const fs = require("fs");
const gulp = require('gulp');
const webpackStream = require('webpack-stream');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');
const webpackDev = require('./webpack.config.dev.js');
const sass = require('gulp-sass')(require('sass'));
const addtextdomain = require('gulp-wp-pot');
const zip = require('@vscode/gulp-vinyl-zip');
const pkg = require('./package.json');
const browserSync = require('browser-sync').create();
const plumber = require('gulp-plumber');
const cache = require('gulp-cached');
const remember = require('gulp-remember');
const path = require('path');
const named = require('vinyl-named');
const rename = require("gulp-rename");
const postcss = require('gulp-postcss');
const gulpif =  require("gulp-if");
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("autoprefixer");
const cleanCss = require("gulp-clean-css");
const yargs = require("yargs");
const dotenv = require('dotenv');
const merge = require('gulp-merge-json');
const babel = require('gulp-babel');
const wpPot = require('gulp-wp-pot');
// const through2 = require('through2');
// const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
// const debounce = require('lodash.debounce');
// const gettext = require('gulp-gettext-parser');
// const cssnano = require('cssnano');
// const i18nextParser = require('i18next-parser');
dotenv.config();

const PRODUCTION = yargs.argv.prod;

const packageJsonPath = './package.json';

function getCurrentVersion() {
  const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
  return packageJson.version;
}

// Função para gerar a nova versão
function getNewVersion() {
  const currentVersion = getCurrentVersion();
  const parts = currentVersion.split('.').map(Number);
  parts[2]++; // Incrementa o último número da versão
  return parts.join('.');
}
// Tarefa para atualizar a versão no arquivo PHP do plugin
gulp.task('updatePluginVersion', () => {
  const pluginFilePath = path.join(process.cwd(), 'lnd-master-dev.php');
  const packageJsonPath = path.join(process.cwd(), 'package.json');
  console.log('Plugin file path:', pluginFilePath);
  console.log('Package.json path:', packageJsonPath);

  return new Promise((resolve, reject) => {
    if (!fs.existsSync(pluginFilePath)) {
      console.error('Plugin file does not exist:', pluginFilePath);
      reject(new Error('Plugin file does not exist'));
      return;
    }

    if (!fs.existsSync(packageJsonPath)) {
      console.error('Package.json does not exist:', packageJsonPath);
      reject(new Error('Package.json does not exist'));
      return;
    }

    let content = fs.readFileSync(pluginFilePath, 'utf8');

    const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
    const newVersion = packageJson.version;

    // Atualize a versão no cabeçalho do plugin
    content = content.replace(/Version:\s*\d+\.\d+\.\d+/g, `Version: ${newVersion}`);

    // Atualize a constante LND_CATALOGO_VERSION
    content = content.replace(/define\('MASTER_LND_VERSION',\s*'\d+\.\d+\.\d+'\);/g, `define('MASTER_LND_VERSION', '${newVersion}');`);

    fs.writeFileSync(pluginFilePath, content, 'utf8');
    console.log('File has been written');

    const finalContent = fs.readFileSync(pluginFilePath, 'utf8');

    if (finalContent.includes(newVersion)) {
      console.log('File successfully updated');
      resolve(); // Resolve a Promise indicando que a tarefa foi concluída
    } else {
      console.error('Warning: File content did not change as expected');
      reject(new Error('File content did not change as expected'));
    }
  });
});

// Tarefa para atualizar a versão no package.json
gulp.task('updatePackageJson', (done) => {
  const packageJsonPath = path.join(process.cwd(), 'package.json');
  console.log('Package.json path:', packageJsonPath);

  if (!fs.existsSync(packageJsonPath)) {
    console.error('Package.json does not exist:', packageJsonPath);
    return done(new Error('package.json not found'));
  }

  let packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
  console.log('Current version:', packageJson.version);

  const newVersion = getNewVersion();
  console.log('New version:', newVersion);

  packageJson.version = newVersion;

  fs.writeFileSync(packageJsonPath, JSON.stringify(packageJson, null, 2), 'utf8');
  console.log('package.json has been updated');

  // Verify the update
  const updatedPackageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
  console.log('Updated version in package.json:', updatedPackageJson.version);

  if (updatedPackageJson.version === newVersion) {
    console.log('package.json version successfully updated');
    done();
  } else {
    console.error('Failed to update package.json version');
    done(new Error('Failed to update package.json version'));
  }
});

gulp.task('webpackProd', function () {
    return gulp.src('./src/js/*')
        .pipe(plumber())
        .pipe(named())
        .pipe(webpackStream(webpackConfig))
        .pipe(gulp.dest('./assets/js'));
});

gulp.task('webpackDev', function () {
    return gulp.src('./src/js/*')
        .pipe(plumber())
        .pipe(named())
        .pipe(webpackStream(webpackDev))
        .pipe(gulp.dest('./assets/js'));
});

// gulp.task('webpackDev', function(callback) {
//   const compiler = webpack(webpackDev);
  
//   compiler.watch({
//       aggregateTimeout: 300,
//       poll: undefined
//   }, (err, stats) => {
//       if (err) {
//           console.error(err.stack || err);
//           if (err.details) {
//               console.error(err.details);
//           }
//           return;
//       }

//       const info = stats.toJson();

//       if (stats.hasErrors()) {
//           console.error(info.errors);
//       }

//       if (stats.hasWarnings()) {
//           console.warn(info.warnings);
//       }

//       console.log(stats.toString({
//           chunks: false,
//           colors: true
//       }));
//   });

//   callback();
// });

function handleError(err) {
    console.log(err.toString());
    this.emit('end');
}

// Adicione este manipulador de erro às suas tarefas, por exemplo:
gulp.task('sassCompile', function () {
    return gulp.src('./src/sass/**/*.scss')
        .pipe(plumber({errorHandler: handleError}))
        .pipe(cache('sassCompile'))
        .pipe(sass({ outputStyle: 'compressed' }))
        .on('error', handleError)
        .pipe(remember('sassCompile'))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('tailwind', function () {
    return gulp.src('./src/css/global.css')
        .pipe(plumber())
        .pipe(postcss([
                        require('precss'),
                        require('tailwindcss'),
                        require('autoprefixer')
        ]))
        .pipe(rename(function (path) {
            path.extname = ".scss";
        }))
        .pipe(gulp.dest('./src/scss/'));
});
  
gulp.task('styles', function () {
return gulp.src("./src/scss/*.scss")
    .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
    .pipe(sass().on("error", sass.logError))
    .pipe(gulpif(PRODUCTION, postcss([autoprefixer])))
    .pipe(gulpif(PRODUCTION, cleanCss({ compatibility: "ie8" })))
    .pipe(gulpif(!PRODUCTION, sourcemaps.write()))
    .pipe(rename(function (path) {
    path.basename += ".min";
    // path.extname = ".scss";
    }))
    .pipe(gulp.dest("./assets/css"));
});


gulp.task('i18n-php', function () {
  return gulp.src(["**/*.php", "!vendor/**/*"])
      .pipe(plumber({errorHandler: handleError}))
      .pipe(addtextdomain({
          domain: 'lnd-master-dev',
          package: 'LND Master Dev',
      }))
      .pipe(gulp.dest('languages/php.pot'));
});

gulp.task('i18n-jsx', function () {
  return gulp.src(['**/*.js', '**/*.jsx', '!node_modules/**/*', '!build/**/*'])
    .pipe(plumber({errorHandler: handleError}))
    .pipe(babel({
      presets: ['@babel/preset-react']
    }))
    .pipe(wpPot({
      domain: 'lnd-master-dev',
      package: 'LND Master Dev',
      headers: {
        'Report-Msgid-Bugs-To': 'https://wordpress.org/support/plugin/lnd-master-dev',
        'Last-Translator': 'FULL NAME <EMAIL@ADDRESS>',
        'Language-Team': 'LANGUAGE <LL@li.org>'
      }
    }))
    .pipe(gulp.dest('languages/jsx.pot'));
});

gulp.task('merge-pots', function () {
  return gulp.src('languages/*.pot')
      .pipe(merge({
          fileName: `${process.env.TEXT_DOMAIN}.pot`,
          edit: (parsedJson, file) => {
              // Você pode adicionar lógica aqui para modificar o conteúdo do arquivo POT se necessário
              return parsedJson;
          }
      }))
      .pipe(gulp.dest('languages'));
});

gulp.task('readme', function () {
    return gulp.src('readme.txt')
        .pipe(plumber())
        .pipe(gulp.dest('.'));
});

gulp.task('compress', function () {
    return gulp.src([
        '**/*',
        '!node_modules/**',
        '!dist/**',
        '!lnd-master-dev.zip',
        '!*.zip',
        '!src/**',
        '!materiais/**',
        '!Gruntfile.js',
        '!composer.json',
        '!composer.lock',
        '!.gitgnore',
        '!.gitattributes',
        '!package-lock.json',
        '!package.json',
        '!.backup',
        '!.backup',

    ], { base: "../." })
        .pipe(zip.dest(`./dist/lnd-master-dev-v${pkg.version}.zip`))
});

gulp.task('serve', function (cb) {
    browserSync.init({
        proxy: 'https://lojanegociosdigital.local/',
        https: {
            key: '/home/emerson/.wplocaldocker/global/ssl-certs/lojanegociosdigital-local.key',
            cert: '/home/emerson/.wplocaldocker/global/ssl-certs/lojanegociosdigital-local.crt'
        },
        notify: true,
        injectChanges: true,
        open: false,        
    }, function(err, bs) {
        if (err) {
            console.log(err);
            cb(err);
        } else {
            console.log('BrowserSync is running');
        }
    });
  browserSync.watch("assets/css/*.css", function (event, file) {
    if (event === "change") {
      browserSync.reload("*.css");
    }
  });
});

gulp.task('reload',  (done) => {
    browserSync.reload();
    done();
});

gulp.task('watching', function () {
    gulp.watch("src/scss/**/*.scss", gulp.series('styles'));
    gulp.watch("src/**/*.js*", gulp.series(gulp.parallel('webpackDev','tailwind'),  'reload'));
    gulp.watch(["**/*.php"], gulp.series('tailwind', 'reload'));
});

gulp.task('dev', function(done) {
    gulp.series(gulp.parallel('webpackDev', 'tailwind', 'styles'), function() {
        gulp.series(gulp.parallel('watching','serve',) )(done);
    })();
});

gulp.task('version', gulp.series('updatePackageJson', 'updatePluginVersion'));

gulp.task('i18n', gulp.series('i18n-php', 'i18n-jsx', 'merge-pots'));

gulp.task('build', gulp.series('version', 'tailwind', 'styles', 'webpackProd'));





// gulp.task('default', gulp.series('dashboard', 'serve'));
// gulp.task('watchJs', function () {
//     gulp.watch('src/**/*.jsx', gulp.series('webpackDev'));
// });
// function watchJSX() {
//     console.log('Watching JSX files...');
//     return gulp.watch('src/**/*.js*', 
//         gulp.series(
//             (done) => {
//                 console.log('JSX file changed');
//                 done();
//             },
//             gulp.parallel('compile-css', 'webpackDev')
//         )
//     );
// }

// function watchSCSS() {
//     console.log('Watching SCSS files...');
//     return gulp.watch('src/**/*.scss', 
//         gulp.series(
//             (done) => {
//                 console.log('SCSS file changed');
//                 done();
//             },
//             'sassCompile'
//         )
//     );
// }

// gulp.task('watchCSS', 
//     gulp.series('serve', 
//         gulp.parallel(
//             watchJSX,
//             watchSCSS,
//             function(done) {
//                 console.log('Watching for changes...');
//                 // Esta função nunca chama 'done()', mantendo o processo ativo
//             }
//         )
//     )
// );


// gulp.task('serve', function (cb) {
//     browserSync.init({
//         proxy: 'https://lojanegociosdigital.local/',
//         https: {
//             key: '/home/emerson/.wplocaldocker/global/ssl-certs/lojanegociosdigital-local.key',
//             cert: '/home/emerson/.wplocaldocker/global/ssl-certs/lojanegociosdigital-local.crt'
//         },
//         notify: true,
//         injectChanges: true,
//         open: false,
//         // host: 'lojanegociosdigital.local',
//         // open: 'external',
//         // port: 3000,
//         // reloadDebounce: 3000,
//         // reloadDelay: 2000,
//         // reloadThrottle: 2
        
//     }, function(err, bs) {
//         if (err) {
//             console.log(err);
//             cb(err);
//         } else {
//             console.log('BrowserSync is running');
//             // Não chame cb() aqui para manter o processo ativo
//         }
//     });
    // browserSync.watch("assets/css/*.css", function (event, file) {
    //     if (event === "change") {
    //         browserSync.reload("*.css");
    //     }
    // });

    // browserSync.watch("assets/js/*.js", function (event, file) {
    //     if (event === "change") {
    //         browserSync.reload("*.js");
    //     }
    // });

    // gulp.watch('*.php').on('change', browserSync.reload);
    // cb();
// });

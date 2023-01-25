const mix = require('laravel-mix');
const WebpackJsObfuscator = require('webpack-obfuscator');
const ObfuscatorConfig = {
   compact: true,
   controlFlowFlattening: false,
   deadCodeInjection: false,
   debugProtection: false,
   debugProtectionInterval: false,
   disableConsoleOutput: false,
   identifierNamesGenerator: 'hexadecimal',
   log: false,
   numbersToExpressions: false,
   renameGlobals: false,
   rotateStringArray: true,
   selfDefending: true,
   shuffleStringArray: true,
   simplify: false,
   splitStrings: false,
   stringArray: true,
   stringArrayEncoding: [],
   stringArrayIndexShift: true,
   stringArrayWrappersCount: 1,
   stringArrayWrappersChainedCalls: true,
   stringArrayWrappersParametersMaxCount: 2,
   stringArrayWrappersType: 'variable',
   stringArrayThreshold: 0.75,
   unicodeEscapeSequence: false,
   reservedStrings: ['(.*?)']
};

mix.webpackConfig({
   plugins: [
      new WebpackJsObfuscator(ObfuscatorConfig, ['exclude.js'])
   ],
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.setPublicPath('public')
   .options({
      processCssUrls: false
   })
   .copyDirectory('resources/js/libs', 'public/js/libs')
   .sass('resources/scss/prestyle.scss', 'public/css')
   .less('resources/less/inc/user-device.less', 'public/css/inc')
   .less('resources/less/inc/tree.less', 'public/css/inc')
   .less('resources/less/inc/chat.less', 'public/css/inc')
   .less('resources/less/inc/stream.less', 'public/css/inc')
   .less('resources/less/inc/gallery.less', 'public/css/inc')
   .less('resources/less/inc/gallery-modal.less', 'public/css/inc')
   .less('resources/less/style.less', 'public/css')
   .less('resources/less/auth.less', 'public/css')
   .less('resources/less/admin.less', 'public/css')
   .copy('resources/less/lib', 'public/css/lib')
   .js('resources/js/admin.js', 'public/js')
   .js('resources/js/bootstrap.js', 'public/js')
   .js('resources/js/echo-init.js', 'public/js')
   .js('resources/js/inc/chat.js', 'public/js/inc')
   .js('resources/js/inc/stream.js', 'public/js/inc')
   .js('resources/js/inc/record-rtc-handler.js', 'public/js/inc')
   .js('resources/js/inc/gallery.js', 'public/js/inc')
   .js('resources/js/inc/copy-to-clipboard.js', 'public/js/inc')
   .js('resources/js/inc/show-user-info.js', 'public/js/inc')
   .js('resources/js/inc/show-user-selector.js', 'public/js/inc')
   .js('resources/js/inc/video-hls.js', 'public/js/inc')
   .js('resources/js/inc/yandex-map.js', 'public/js/inc')
   .browserSync({
      proxy: 'http://eurodentist'
   })
   .sourceMaps();

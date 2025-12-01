const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (Encore.isRuntimeEnvironmentConfigured() === false) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Directorio donde se compilan los assets (public/build)
    .setOutputPath('public/build/')
    
    // Directorio público para acceder a los assets compilados
    .setPublicPath('/build/')

    // Limpia el directorio de compilación antes de cada build
    .enableSingleRuntimeChunk()

    /*
     * ENTRADA: 'app' es el nombre que usarás en Twig. 
     * './assets/app.js' es la ruta al archivo de entrada.
     */
    .addEntry('app', './assets/app.js')

    // Habilitar React
    .enableReactPreset()

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // Habilitar mapas de origen (source maps) para debugging
    .enableSourceMaps(!Encore.isProduction())

    // Versioneado de archivos para evitar problemas de caché en producción
    .enableVersioning(Encore.isProduction())

    // Copiar imágenes o fuentes estáticas
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
        pattern: /\.(png|jpg|jpeg|svg|webp)$/
    })
;

module.exports = Encore.getWebpackConfig();
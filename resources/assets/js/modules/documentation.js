let SwaggerUIBundle = require('../libs/swagger-ui-bundle');
let SwaggerUIStandalonePreset = require('../../../../node_modules/swagger-ui/dist/swagger-ui-standalone-preset');

window.onload = function () {
    window.ui = SwaggerUIBundle({
        url: swaggerUrl,
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout"
    })
}
var gulp = require('gulp');
var gulpSequence = require('gulp-sequence');
var prompt = require('prompt');

var fse = require('fs-extra');
var mustache = require('mustache');

gulp.task('start', gulpSequence('condition','deploy'));

gulp.task('condition', function (cb) {
    mustache.tags = ['<%', '%>'];


            var schema = {
                properties: {
                    google_idp_client_id: {
                        description: 'Enter the OAuth Client Id to use Google Sign-in',
                        type: 'string',
                        required: true
                    },
                    google_idp_redirect_url: {
                        description: 'Enter the OAuth redirect url registered with Google Sign-in',
                        type: 'string',
                        required: true
                    }
                }
            };

            prompt.start();

            prompt.get(schema, function (err, results) {
                updateConfig(results, cb);
            });


})


function updateConfig(required_values, cb) {

    fse.copy('config.yml.orig', 'config.yml', function (error) {
        if (error) {
            cb(error);
        }
        else {
            replace_variables(['config.yml'], required_values, function (error, res) {
                mustache.tags = ['{{', '}}'];
                require('edge-launchpad')(gulp);
                cb(error, res)
            });
        }
    })

}

function replace_variables(paths, inject_object, cb) {
    mustache.escape = function (value) {
        return value;
    };
    for (var i = 0; i < paths.length; i++) {
        var path_to_template = paths[i];
        var data;
        try {
            data = fse.readFileSync(path_to_template, 'utf8')
        } catch (e) {
            console.log(e);
            cb(e);
        }
        var mu_template = String(data);
        try {
            var output = mustache.render(mu_template, inject_object)
        } catch (e) {
            console.log(e);
            cb(e);
        }
        try {
            fse.outputFileSync(path_to_template.split('.').slice(0, 2).join('.'),
                output)
        } catch (e) {
            console.log(e);
            cb(e);
        }
        output = '< yet to copy from original template >';
    }
    cb(null, inject_object);
}
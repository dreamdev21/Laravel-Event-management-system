module.exports = function (grunt) {
    //Initializing the configuration object
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // Task configuration
        less: {
            development: {
                options: {
                    compress: true,
                },
                files: {
                    "./public/assets/stylesheet/application.css": "./public/assets/stylesheet/application.less",
                    "./public/assets/stylesheet/frontend.css": "./public/assets/stylesheet/frontend.less",
                }
            },

        },
        concat: {
            options: {
                separator: ';',
                stripBanners: {
                    block: true,
                    line: true
                },
            },
            js_frontend: {
                src: [
                    './public/vendor/jquery/dist/jquery.min.js',
                    './public/vendor/bootstrap/dist/js/bootstrap.js',
                    './public/vendor/jquery-form/jquery.form.js',
                    './public/vendor/RRSSB/js/rrssb.js',
                    './public/vendor/humane-js/humane.js',
                    './public/vendor/jquery.payment/lib/jquery.payment.js',
                    './public/assets/javascript/app-public.js'
                ],
                dest: './public/assets/javascript/frontend.js',
            },
            js_backend: {
                src: [
                    './public/vendor/modernizr/modernizr.js',
                    './public/vendor/html.sortable/dist/html.sortable.js',
                    './public/vendor/bootstrap/dist/js/bootstrap.js',
                    './public/vendor/jquery-form/jquery.form.js',
                    './public/vendor/humane-js/humane.js',
                    './public/vendor/RRSSB/js/rrssb.js',
                    './public/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js',
                    './public/vendor/curioussolutions-datetimepicker/dist/DateTimePicker.js',
                    './public/vendor/jquery-minicolors/jquery.minicolors.min.js',
                    './public/assets/javascript/app.js'
                ],
                dest: './public/assets/javascript/backend.js',
            },
        },
        uglify: {
            options: {
                mangle: true,  // Use if you want the names of your functions and variables unchanged
                preserveComments: false,
                banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                '<%= grunt.template.today("yyyy-mm-dd") %> */',

            },
            frontend: {
                files: {
                    './public/assets/javascript/frontend.js': ['<%= concat.js_frontend.dest %>'],
                }
            },
            backend: {
                files: {
                    './public/assets/javascript/backend.js': './public/assets/javascript/backend.js',
                }
            },
        },
        phpunit: {
            classes: {},
            options: {}
        },
    });

    // Plugin loading
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    //grunt.loadNpmTasks('grunt-phpunit');
    // Task definition
    grunt.registerTask('default', ['less', 'concat']);
    grunt.registerTask('deploy', ['less', 'concat', 'uglify']);
    grunt.registerTask('js', ['concat']);
    grunt.registerTask('styles', ['concat']);
    grunt.registerTask('minify', ['uglify']);
};
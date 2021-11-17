<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
return [
    /**
     * Url path to use for this package routes
     */
    'url_path' => '/form-builder',

    /**
     * Template layout file. This is the path to the layout file your application uses
     */
    'layout_file' => 'layouts.app',

    /**
     * The stack section in the layout file to output js content
     * Define something like @stack('stack_name') and provide the 'stack_name here'
     */
    'layout_js_stack' => 'script',

    /**
     * The stack section in the layout file to output css content
     */
    'layout_css_stack' => 'css',

    /**
     * The class that will provide the roles we will display on form create or edit pages?
     */
    'roles_provider' => abd\FormBuilder\Services\RolesProvider::class,

    /**
     * Models used in form builder
     */
    'models' => [
        'user' => \App\Models\User::class,
    ],
];

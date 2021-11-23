<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
namespace abd\FormBuilder;

use Throwable;

class Helper
{
    /**
     * Get the roles to use from the configured roles provider
     *
     * @return array
     */
    public static function getConfiguredRoles()
    {
        return (auth()->user()->user_type == config('formbuilder.admin_type')) ? 1 : 0 ;
    }

    /**
     * Add random string to the url to help bust the cache in development
     *
     * @return string
     */
    public static function bustCache() : string
    {
        if (! config('app.debug')) return '';

        return '?b=' . static::randomString();
    }

    /**
     * Generate and return a random characters string
     *
     * @link https://gist.github.com/irazasyed/5382685
     * @param   integer $length  Length of the string to be generated, Default: 8 characters long.
     * @param   string  $seedings the characters to use for the random string seeding
     * @return  string
     */
    public static function randomString($length = 8, $seedings = null) : string
    {
        $seedings = $seedings ?? '123456789ABCDEFGHJKLM';

        $pool = $seedings;

        $str = '';
        for ($i = 0; $i < $length; $i++)
        {
            $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
        }

        return $str;
    }

    /**
     * Return a generic error message
     *
     * @return string
     */
    public static function wtf() : string
    {
        return 'There was an error when processing your request.';
    }
}

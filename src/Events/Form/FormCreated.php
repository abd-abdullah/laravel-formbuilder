<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
namespace abd\FormBuilder\Events\Form;

use abd\FormBuilder\Models\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class FormCreated
{
    use Queueable, SerializesModels;

    /**
     * The deleted form
     *
     * @var abd\FormBuilder\Models\Form
     */
    public $form;

    /**
     * Create a new event instance.
     *
     * @param abd\FormBuilder\Models\Form $form
     * @return void
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }
}

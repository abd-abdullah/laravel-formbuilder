<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
namespace abd\FormBuilder\Controllers;

use App\Http\Controllers\Controller;
use abd\FormBuilder\Helper;
use abd\FormBuilder\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RenderFormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('public-form-access');
    }

    /**
     * Render the form so a user can fill it
     *
     * @param string $identifier
     * @return Response
     */
    public function render($identifier)
    {
        $form = Form::where('identifier', $identifier)->firstOrFail();
        $submission = auth()->user()->submissions->where('form_id', $form->id)->first();

        $pageTitle = "{$form->name}";

        return view('formbuilder::render.index', compact('form', 'submission',  'pageTitle'));
    }

    /**
     * Process the form submission
     *
     * @param Request $request
     * @param string $identifier
     * @return Response
     */
    public function submit(Request $request, $identifier)
    {
        $form = Form::where('identifier', $identifier)->firstOrFail();
        $input = $request->except('_token');

        $rules = [];


        if($form->payment_enable){
            $paymentDetails = json_decode($form->payment_details);
            if($request->has('payment_option_name')){
                $rules['payment_option_name.*'] = 'required|min:2';
            }
        }

        $formField = json_decode($form->form_builder_json);

        foreach ($formField as $index => $field){
            $rule = 'bail';
            $rule .= (isset($field->required) ? '|required' : '');
            $rule .= (isset($field->subtype) && $field->subtype == 'email') ? '|email' : '';
            $rule .= ($field->type == 'text') ? '|min:0' : '';
            $rule .= (isset($field->maxlength) && is_int($field->maxlength)) ? '|max:'.$field->maxlength : '';
            $rule .= (isset($field->minlength) && is_int($field->minlength)) ? '|min:'.$field->minlength : '';

            $rules[$field->name] = $rule;

        }

        $request->validate($rules);

        DB::beginTransaction();


        try {
            $input = $request->except('_token');

            // check if files were uploaded and process them
            $uploadedFiles = $request->allFiles();
            foreach ($uploadedFiles as $key => $file) {
                // store the file and set it's path to the value of the key holding it
                if ($file->isValid()) {
                    $input[$key] = $file->store(confg('file_path').'/form_files');
                }
            }

            $user_id = auth()->user()->id ?? null;

            $data = [
                'user_id' => $user_id,
                'content' => $input,
            ];

            if($form->payment_enable){
                $paymentDetails = collect(json_decode($form->payment_details));
                if($request->has('payment_option_name')){
                    foreach($request->payment_option_name as $payment_option){
                        $amount = $paymentDetails->where('payment_option_name', $payment_option)->first()->payment_option_value;
                        $payment_details[] = [
                            'payment_option_name' => $payment_option,
                            'payment_option_amount' => $amount,
                        ];
                    }
                }
            }

            $data['payment_details'] = json_encode($payment_details);

            $submission = $form->submissions()->create($data);

            DB::commit();

            if($form->payment_enable) {
                return redirect()->away($form->payment_route.'/'.$submission->id);
            }

            return redirect()
                    ->route('formbuilder::form.feedback', $identifier)
                    ->with('success', 'Form successfully submitted.');
        } catch (Throwable $e) {
            info($e);

            DB::rollback();

            return back()->withInput()->with('error', Helper::wtf());
        }
    }

    /**
     * Display a feedback page
     *
     * @param string $identifier
     * @return Response
     */
    public function feedback($identifier)
    {
        $form = Form::where('identifier', $identifier)->firstOrFail();

        $pageTitle = "Form Submitted!";

        return view('formbuilder::render.feedback', compact('form', 'pageTitle'));
    }
}

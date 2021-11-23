<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
namespace abd\FormBuilder\Controllers;

use abd\FormBuilder\Models\Form;
use App\Http\Controllers\Controller;
use abd\FormBuilder\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MySubmissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // only allow submission edit on forms that allow it
        $this->middleware('submisson-editable')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $submissions = Submission::getForUser($user);

        $pageTitle = "My Submissions";

        return view('formbuilder::my_submissions.index', compact('submissions', 'pageTitle'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $submission = Submission::where(['user_id' => $user->id, 'id' => $id])
                            ->with('form')
                            ->firstOrFail();

        $form_headers = $submission->form->getEntriesHeader();

        $pageTitle = "View Submission";

        return view('formbuilder::my_submissions.show', compact('submission', 'pageTitle', 'form_headers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $submission = Submission::where(['user_id' => $user->id, 'id' => $id])
                            ->with('form')
                            ->firstOrFail();

        // load up my current submissions into the form json data so that the
        // form is pre-filled with the previous submission we are trying to edit.
        $submission->loadSubmissionIntoFormJson();

        $form = Form::where('id', $submission->form_id)->first();

        $pageTitle = "Edit My Submission for {$submission->form->name}";

        return view('formbuilder::my_submissions.edit', compact('submission', 'pageTitle', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $submission = Submission::where(['user_id' => $user->id, 'id' => $id])->firstOrFail();

        $form = Form::where('id', $submission->form_id)->firstOrFail();
        $input = $request->except(['_token', '_method']);

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

            // check if files were uploaded and process them
            $uploadedFiles = $request->allFiles();
            foreach ($uploadedFiles as $key => $file) {
                // store the file and set it's path to the value of the key holding it
                if ($file->isValid()) {
                    $input[$key] = $file->store('fb_uploads', 'public');
                }
            }

            $data = [
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

            $submission->update($data);

            DB::commit();
            return redirect()
                        ->route('formbuilder::my-submissions.index')
                        ->with('success', 'Submission updated.');
        } catch (Throwable $e) {
            info($e);

            DB::rollback();

            return back()->withInput()->with('error', Helper::wtf());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $submission = Submission::where(['user_id' => $user->id, 'id' => $id])->firstOrFail();
        $submission->delete();

        return redirect()
                    ->route('formbuilder::my-submissions.index')
                    ->with('success', 'Submission deleted!');
    }
}

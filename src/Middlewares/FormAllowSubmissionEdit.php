<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
namespace abd\FormBuilder\Middlewares;

use Closure;
use abd\FormBuilder\Models\Submission;

class FormAllowSubmissionEdit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $submission_id = $request->route('my_submission');

        $user = $request->user();
        $submission = Submission::where(['user_id' => $user->id, 'id' => $submission_id])->firstOrFail();

        if (! $submission->form->allowsEdit()) {
            // this form does not allow edit
            return redirect()
                        ->route('formbuilder::my-submissions.show', $submission->id)
                        ->with('error', "Form '{$submission->form->name}' does not allow submission edit.");
        }

        return $next($request);
    }
}

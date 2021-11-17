@extends('formbuilder::layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card rounded-0">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3>Form Successfully submitted</h3>
                        </div>
                        <div class="col-lg-6">
                            @auth
                                <a href="{{ route('formbuilder::my-submissions.index') }}" class="btn btn-primary btn-sm float-md-right">
                                    <i class="fa fa-th-list"></i> Go To My Submissions
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h3 class="text-center text-success">
                        Your entry for <strong>{{ $form->name }}</strong> was successfully submitted.
                    </h3>
                </div>

                <div class="card-footer">
                    <a href="{{ route('home') }}" class="btn btn-primary confirm-form">
                        <i class="fa fa-home"></i> Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('formbuilder::layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card rounded-0">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-10">
                                <h4>Viewing my submission for form <strong>{{ $submission->form->name }}</strong></h4>
                            </div>
                            <div class="col-lg-2">
                                <a href="{{ route('formbuilder::my-submissions.index') }}" class="btn btn-primary btn-sm pull-right" title="Back To My Submissions">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                                @if($submission->form->allowsEdit())
                                    <a href="{{ route('formbuilder::my-submissions.edit', $submission) }}" class="pull-right btn btn-primary btn-sm mr-1" title="Edit this submission">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush">
                        @foreach($form_headers as $header)
                            <li class="list-group-item">
                                <strong>{{ $header['label'] ?? title_case($header['name']) }}: </strong>
                                <span class="float-right">
                                {{ $submission->renderEntryContent($header['name'], $header['type']) }}
                            </span>
                            </li>
                        @endforeach
                        @if((json_decode($submission->payment_details)))
                            <li class="list-group-item text-center">
                                <strong>Payment Details</strong>
                            </li>
                            @foreach(json_decode($submission->payment_details) as $details)
                                @if(isset($details->payment_option_name))
                                    <li class="list-group-item">
                                        <strong>{{ $details->payment_option_name }}: </strong>
                                        <span class="float-right">{{$details->payment_option_amount}}</span>
                                    </li>
                                @elseif(isset($details->status))
                                    <li class="list-group-item">
                                        <strong>Payment Status: </strong>
                                        <span class="float-right">{{$details->status}}</span>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h5 class="card-title">Details</h5>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Form: </strong>
                            <span class="float-right">{{ $submission->form->name }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Submitted By: </strong>
                            <span class="float-right">{{ $submission->user->name ?? 'Guest' }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Last Updated On: </strong>
                            <span class="float-right">{{ $submission->updated_at->toDayDateTimeString() }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Submitted On: </strong>
                            <span class="float-right">{{ $submission->created_at->toDayDateTimeString() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

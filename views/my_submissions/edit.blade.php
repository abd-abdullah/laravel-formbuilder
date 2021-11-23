@extends('formbuilder::layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card rounded-0">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-8">
                            <h3>{{ $pageTitle }}</h3>
                        </div>
                        <div class="col-lg-4">
                            <a href="{{ route('formbuilder::my-submissions.index') }}" class="btn btn-primary float-md-right btn-sm" title="Back To My Submissions">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <form action="{{ route('formbuilder::my-submissions.update', $submission->id) }}" method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @php
                        $paymentDetails = json_decode($form->payment_details);
                        $paymentDetails = $paymentDetails ? $paymentDetails : []
                    @endphp

                    <div class="card-body">
                        <div id="fb-render"></div>

                        @if(count($paymentDetails))
                            <div class="row">
                                <div class="col-12">
                                    <h4>Services</h4>
                                </div>
                                <div class="col-12">
                                    <table class="w-100">
                                        <tr class="border-bottom border-top text-bold">
                                            <td>Service Name</td>
                                            <td>Amount</td>
                                        </tr>
                                        @foreach($paymentDetails as $index => $details)
                                            <tr class="border-bottom">
                                                <td>
                                                    <div class="checkbox">
                                                        <input name="payment_option_name[]" id="payment_option_name_{{$index}}" required="required" aria-required="true" data-value="{{$details->payment_option_value}}" value="{{$details->payment_option_name}}" type="checkbox">
                                                        <label class="font-weight-normal" for="payment_option_name_{{$index}}">{{$details->payment_option_name}}</label>
                                                    </div>
                                                </td>
                                                <td>{{$details->payment_option_value}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    <h6 class="mt-3 text-bold">Total amount to be paid : <input type="text" readonly id="totalAmount"></h6>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary confirm-form" data-form="submitForm" data-message="Submit update to your entry for '{{ $submission->form->name }}'?">
                            <i class="fa fa-submit"></i> Submit Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push(config('formbuilder.layout_js_stack', 'scripts'))
<script type="text/javascript">
    window._form_builder_content = jQuery.parseJSON({!! json_encode($form->form_builder_json) !!});
    var __form_errors = @json($errors->getMessages());
    var __old_inputs = @json(session()->getOldInput());

    $(document).on('change', ':input[name^=payment_option_name]', function (){
        let checked_list = $(':input[name^=payment_option_name]:checked');
        let grandTotal = 0;
        $.each(checked_list, function (ind, val){
            grandTotal += parseInt($(val).data('value'));
        });

        $('#totalAmount').val(grandTotal);
    });

    $.each(__old_inputs, function (ind, val) {
        if (ind == '_token') {
            return;
        }

        let input = window._form_builder_content.find(input => input.name == ind);

        if ((input.type == 'select' && typeof input.multiple == 'undefined') || input.type == 'radio-group') {
            input.values.find(x => x.value == val).selected = true;
            input.values = input.values.map(function (item) {

                if (item.value != val && typeof item.selected != 'undefined') {
                    delete item.selected;
                }

                return item;
            });
        } else if ((input.type == 'select' && typeof input.multiple != 'undefined') || input.type == 'checkbox-group') {
            $.each(val, function (indC, valC) {
                input.values.find(x => x.value == valC).selected = true;
                input.values = input.values.map(function (item) {
                    if (val.indexOf(item.value) == -1 && typeof item.selected != 'undefined') {
                        delete item.selected;
                    }

                    return item;
                });
            });
        } else {
            input.value = val;
        }
    });

    window._form_builder_content = JSON.stringify(window._form_builder_content);
</script>

<script src="{{ asset('vendor/formbuilder/js/render-form.js') }}{{ abd\FormBuilder\Helper::bustCache() }}" defer></script>

@endpush

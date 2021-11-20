@extends('formbuilder::layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card rounded-0">
                <div class="card-header">
                    <h5 class="card-title">{{ $pageTitle }}</h5>
                </div>

                <form action="{{ route('formbuilder::form.submit', $form->identifier) }}" method="POST" id="submitForm" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div id="fb-render"></div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary confirm-form" data-form="submitForm" data-message="Submit your entry for '{{ $form->name }}'?">
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

        $.each(__old_inputs, function(ind, val){
            if(ind == '_token'){
                return ;
            }

            let input = window._form_builder_content.find(input => input.name == ind);

            if((input.type == 'select' && typeof input.multiple == 'undefined') || input.type == 'radio-group') {
                input.values.find(x => x.value == val).selected = true;
                input.values = input.values.map(function (item) {

                    if(item.value != val && typeof item.selected != 'undefined'){
                        delete item.selected;
                    }

                    return item;
                });
            }
            else if((input.type == 'select' && typeof input.multiple != 'undefined') || input.type == 'checkbox-group'){
                $.each(val, function (indC, valC){
                    input.values.find(x => x.value == valC).selected = true;
                    input.values = input.values.map(function (item){
                        if(val.indexOf(item.value) == -1 && typeof item.selected != 'undefined'){
                            delete item.selected;
                        }

                        return item;
                    });
                });
            }
            else{
                input.value = val;
            }
        });

        window._form_builder_content = JSON.stringify(window._form_builder_content);
    </script>

    <script src="{{ asset('vendor/formbuilder/js/render-form.js') }}{{ abd\FormBuilder\Helper::bustCache() }}" defer></script>

@endpush

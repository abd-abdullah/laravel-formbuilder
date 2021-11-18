jQuery(function() {
	var fbRenderOptions = {
		container: false,
		dataType: 'json',
		formData: window._form_builder_content ? window._form_builder_content : '',
		render: true,
	}

	$('#fb-render').formRender(fbRenderOptions)
})

window.addEventListener('load', function (){
	$.each(__form_errors, function (index, value){
		var html = '<span class="text-danger help-block">';
		if (typeof value == "object") {
			$.each(value, function (ind, val){
				html += val+'<br>';
			});
		}
		else{
			html += value;
		}

		html += '</span>';

		html = html.replace(index+' field', 'value');

		$(document).find('.field-'+index).append(html);
	});

	$(':input').on('propertychange input', function (e) {
		$(e.currentTarget).closest('.form-group').find('.help-block').remove();
	});
});

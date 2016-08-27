document.observe('dom:loaded', function() {
  var variableFieldset = document.getElementById('preview_variables');
  var variableInputs = variableFieldset.querySelectorAll('.preview-variable-field');
  debugger;
  var previewForm = document.querySelector('#email_template_preview_form div.no-display');
  for (i = 0; i < variableInputs.length; i++) {
    var input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('id', 'preview_' + variableInputs[i].getAttribute('id'));
    input.setAttribute('name', variableInputs[i].getAttribute('id'));
    input.setAttribute('value', variableInputs[i].value);
    previewForm.appendChild(input);

    variableInputs[i].addEventListener('change', function () {
      var previewInput = document.getElementById('preview_' + this.getAttribute('id'));
      previewInput.setAttribute('value', this.value);
    }, false);
  }
});

cj(function($) {
  // A few CSS/visual hacks FIXME: put in CSS.
  $('#Main .crm-section').css('padding-bottom', '0.5em');
  $('#Main input.form-text').addClass('form-control').css('width', '20em');
  $('#Main input#cvv2').css('width', '4em').css('display', 'inline-block');
  $('#Main select.form-select').addClass('form-control').css('width', '20em');
  $('input#custom_7_1').addClass('required');

  // Display of the site URL
  $('input#custom_4').parent().addClass('input-group col-sm-4');
  $('input#custom_4').css('width', '15em');
  $('input#custom_4').parent().prepend('<span class="input-group-addon">https://</span>');
  $('input#custom_4').parent().append('<span class="input-group-addon">.symbiotic.coop</span>'); // HARDCODE FIXME

  // Suggest a domain
  $('#onbehalf_organization_name').change(function() {
    var name = $(this).val();

    if (! $('input#custom_4').val()) {
      // TODO: validate if the name is available.
      $('input#custom_4').val(name).trigger('change');
    }
  });

  $('input#custom_4').change(function() {
    var name = $(this).val();

    name = name.toLowerCase();

    name = name.replace(/[àáâãäå]/g, "a");
    name = name.replace(/[èéêë]/g, "e");
    name = name.replace(/[ôöò]/g, "o");
    name = name.replace(/[ùû]/g, "u");
    name = name.replace(/[îì]/g, "i");

    name = name.replace(/[^a-zA-Z0-9]/g, '');
    $(this).val(name);
  });
});

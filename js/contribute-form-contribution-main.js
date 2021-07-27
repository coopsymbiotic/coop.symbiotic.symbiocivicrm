(function($, _, ts) {
  if (typeof CRM.vars.aegir.domain_fieldid == 'undefined') {
    return;
  }

  var domain_name_selector = 'input#custom_' + CRM.vars.aegir.domain_fieldid;

  // Display of the site URL
  $(domain_name_selector).css('width', '10em');
  $(domain_name_selector).parent().prepend('<span class="input-group-addon" style="display: inline;">https://</span>');

  if (CRM.vars.aegir.domain_suffix) {
    $(domain_name_selector).parent().append('<span class="input-group-addon" style="display: inline;">.' + CRM.vars.aegir.domain_suffix + '</span>');
  }

  // Suggest a domain
  $('#onbehalf_organization_name').change(function() {
    var name = $(this).val();

    if (! $(domain_name_selector).val()) {
      // TODO: validate if the name is available.
      $(domain_name_selector).val(name).trigger('change');
    }
  });

  $(domain_name_selector).change(function() {
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
})(CRM.$, CRM._, CRM.ts('symbiocivicrm'));

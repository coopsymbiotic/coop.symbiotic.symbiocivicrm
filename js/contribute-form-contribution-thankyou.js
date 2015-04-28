cj(function($) {
  console.log("Hi there! Nice to meet you. Let's get this show on the road!");

  // Move the status box higher up
  $('form#ThankYou #help').after($('#symbiocivicrm-statusbox'));

  // Create the site
  var data = {
    url: CRM.symbiocivicrm.url,
    invoice: CRM.symbiocivicrm.trxn_id,
    email: CRM.symbiocivicrm.email
  }

  var timeoutID = null;

  $.ajax({
    type: "POST",
    url: 'https://av102.symbiotic.coop/hosting/api/site',
    data: data,
    dataType: 'json',
    crossDomain: true,
    success: function(data) {
      if (data.status == 'success') {
        $('#symbiocivicrm-statusbox-message').html("Site creation request was validated!");
        $('#symbiocivicrm-statusbox-progress .progress-bar').css('width', '10%').attr('aria-valuenow', 10);

        // Start polling regularly, until the operation is finished
        timeoutID = window.setTimeout(symbiocivicrmWaitForSite, 2000);
      }
      else {
        $('#symbiocivicrm-statusbox-message').html("Site creation failed... ouch!").addClass('text-danger');
        $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-exclamation-circle text-danger');
        $('#symbiocivicrm-statusbox-extra').html(data.message);
      }
    }
  });

  function symbiocivicrmWaitForSite() {
    $.ajax({
      type: 'GET',
      url: 'https://av102.symbiotic.coop/hosting/api/site',
      data: data,
      dataType: 'json',
      crossDomain: true,
      success: function(data) {
        console.log('GET response received from av102:');
        console.log(data);

        if (data.status == 'success') {
          var total_steps = 6;
          var p = (data.data.site_status == total_steps ? 100 : data.data.site_status * 16);
          $('#symbiocivicrm-statusbox-progressbar .progress-bar').css('width', p + '%').attr('aria-valuenow', p);

          if (data.data.site_status == total_steps) {
            $('#symbiocivicrm-statusbox h2').html('Ready');
            $('#symbiocivicrm-statusbox-message').html("Site creation complete!").addClass('text-success');
            $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-check-square-o text-success');
            $('#symbiocivicrm-statusbox-extra').html('<a href="' + data.data.login_link + '">' + data.data.login_link + '</a>' + '<p>' + data.data.login_message + '</p>').show();
            $('#symbiocivicrm-statusbox-ready').removeClass('hidden');
            $('#symbiocivicrm-statusbox-ready').show();
          }
          else {
            $('#symbiocivicrm-statusbox-message').html("Site creation in progress! " + data.data.site_status + "/" + total_steps);

            if (data.data.site_status == 2) {
              $('#symbiocivicrm-statusbox-extra').html('This step takes a bit more time...').show();
            }
            else {
              $('#symbiocivicrm-statusbox-extra').fadeOut();
            }

            timeoutID = window.setTimeout(symbiocivicrmWaitForSite, 2000);
          }
        }
        else {
          $('#symbiocivicrm-statusbox h2').html('Error');
          $('#symbiocivicrm-statusbox-message').html("Site creation failed... ouch!").addClass('text-danger');
          $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-exclamation-circle text-danger');
          $('#symbiocivicrm-statusbox-extra').html(data.message);
        }
      }
    });
  }
});

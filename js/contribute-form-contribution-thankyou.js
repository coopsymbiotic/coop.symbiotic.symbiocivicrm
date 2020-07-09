(function($, _, ts) {
  if (typeof CRM.symbiocivicrm == 'undefined' || typeof CRM.symbiocivicrm.url == 'undefined') {
    return;
  }

  console.log("Hi there! Nice to meet you. Let's get this show on the road!");

  // Move the status box higher up
  $('form#ThankYou .help').after($('#symbiocivicrm-statusbox'));

  // Create the site
  var data = {
    url: CRM.symbiocivicrm.url,
    invoice: CRM.symbiocivicrm.trxn_id,
    email: CRM.symbiocivicrm.email
  };

  var timeoutID = null;
  var reqtype = 'POST';

  CRM.symbiocivicrmWaitForSite = function() {
    $.ajax({
      type: reqtype,
      url: 'https://' + CRM.symbiocivicrm.server + '/hosting/api/site',
      data: data,
      dataType: 'json',
      crossDomain: true,
      success: function(data) {
        console.log('GET response received from Aegir server:');
        console.log(data);

        if (data.status == 'success') {
          var total_steps = 8;
          var p = (data.data.site_status == total_steps ? 100 : data.data.site_status * 16);
          $('#symbiocivicrm-statusbox-progressbar .progress-bar').css('width', p + '%').attr('aria-valuenow', p);

          // Assuming the first request to create the site was a success,
          // we now loop to get the site status. Otherwise, if the was an error,
          // the user can still re-send the POST.
          if (reqtype == 'POST') {
            reqtype = 'GET';
          }

          if (data.data.site_status == total_steps) {
            $('#symbiocivicrm-statusbox h2').html('Ready');
            $('#symbiocivicrm-statusbox-message').html("Site creation complete!").addClass('text-success');
            $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-check text-success');
            $('#symbiocivicrm-statusbox-extra').html('').hide();
            $('#symbiocivicrm-statusbox-ready .btn-success').attr('href', data.data.login_link);
            $('#symbiocivicrm-statusbox-ready').removeClass('hidden');
            $('#symbiocivicrm-statusbox-ready').show();
          }
          else {
            // Sometimes site_status is undefined.
            var site_status = (data.data.site_status ? data.data.site_status : '...');
            $('#symbiocivicrm-statusbox-message').html("Site creation in progress! Step " + site_status + " of " + total_steps + ".");

            if (data.data.site_status <= 2) {
              $('#symbiocivicrm-statusbox-extra').html('This can take a minute or two.').show();
            }
            else {
              $('#symbiocivicrm-statusbox-extra').fadeOut();
            }

            timeoutID = window.setTimeout(CRM.symbiocivicrmWaitForSite, 2000);
          }
        }
        else {
          $('#symbiocivicrm-statusbox h2').html(ts('Error'));
          $('#symbiocivicrm-statusbox-message').html(ts('Site creation failed. Very sorry for the inconvenience, we will fix it as soon as possible. Invoice ID:') + CRM.symbiocivicrm.trxn_id).addClass('text-danger');
          $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-exclamation-circle text-danger');
          $('#symbiocivicrm-statusbox-extra').html(data.message + ' ' + '<a href="#" id="symbiocivicrm-retry">' + ts('Click here to retry') + '</a>');
          $('#symbiocivicrm-statusbox-extra').show();

          $('a#symbiocivicrm-retry').click(function(event) {
            event.stopPropagation();
            $('#symbiocivicrm-statusbox-icon i').removeClass('fa-exclamation-circle text-danger');
            $('#symbiocivicrm-statusbox-message').removeClass('text-danger');
            $('#symbiocivicrm-statusbox-extra').fadeOut();
            timeoutID = window.setTimeout(CRM.symbiocivicrmWaitForSite, 2000);
          });
        }
      }
    });
  };

  // Start polling regularly, until the operation is finished
  timeoutID = window.setTimeout(CRM.symbiocivicrmWaitForSite, 2000);

})(CRM.$, CRM._, CRM.ts('symbiocivicrm'));

cj(function($) {
  if (typeof CRM.symbiocivicrm == 'undefined' || typeof CRM.symbiocivicrm.url == 'undefined') {
    return;
  }

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
  var reqtype = 'POST';

  // Start polling regularly, until the operation is finished
  timeoutID = window.setTimeout(symbiocivicrmWaitForSite, 2000);

  function symbiocivicrmWaitForSite() {
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
          var total_steps = 6;
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
            $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-check-square-o text-success');
            $('#symbiocivicrm-statusbox-extra').html('<a href="' + data.data.login_link + '">' + data.data.login_link + '</a>' + '<p>' + data.data.login_message + '</p>').show();
            $('#symbiocivicrm-statusbox-ready').removeClass('hidden');
            $('#symbiocivicrm-statusbox-ready').show();
          }
          else {
            // Sometimes site_status is undefined.
            var site_status = (data.data.site_status ? data.data.site_status : '...');
            $('#symbiocivicrm-statusbox-message').html("Site creation in progress! " + site_status + "/" + total_steps);

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
          $('#symbiocivicrm-statusbox h2').html(ts('Error'));
          $('#symbiocivicrm-statusbox-message').html(ts('Site creation failed... ouch!')).addClass('text-danger');
          $('#symbiocivicrm-statusbox-icon i').removeClass('fa-spin fa-refresh').addClass('fa-exclamation-circle text-danger');
          $('#symbiocivicrm-statusbox-extra').html(data.message + ' ' + '<a href="#" id="symbiocivicrm-retry">' + ts('Click here to retry') + '</a>');
          $('#symbiocivicrm-statusbox-extra').show();

          $('a#symbiocivicrm-retry').click(function(event) {
            event.stopPropagation();
            $('#symbiocivicrm-statusbox-icon i').removeClass('fa-exclamation-circle text-danger');
            $('#symbiocivicrm-statusbox-message').removeClass('text-danger');
            $('#symbiocivicrm-statusbox-extra').fadeOut();
            timeoutID = window.setTimeout(symbiocivicrmWaitForSite, 2000);
          });
        }
      }
    });
  }
});

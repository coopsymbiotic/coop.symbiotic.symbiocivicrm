<div id="symbiocivicrm-statusbox" class="jumbotron hidden-print">
  <div id="symbiocivicrm-statusbox-progress">
    <div id="symbiocivicrm-statusbox-icon" class="pull-right"><i class="fa fa-refresh fa-spin fa-2x"></i></div>
    <h2>{ts domain="coop.symbiotic.symbiocivicrm"}Preparing your CiviCRM instance...{/ts}</h2>

    <div id="symbiocivicrm-statusbox-progressbar" class="progress">
      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
        <span class="sr-only">0% complete</span>
      </div>
    </div>
    <p id="symbiocivicrm-statusbox-message">{ts domain="coop.symbiotic.symbiocivicrm"}Initializing...{/ts}</p>
    <p id="symbiocivicrm-statusbox-extra"></p>

    {* Added here (and hidden initially) to reduce the quantity of JS to generate the HTML *}
    <div id="symbiocivicrm-statusbox-ready" class="hidden">
      <div class="symbiocivicrm-statusbox-btn-wrapper">
        <a class="btn btn-success" href="{$symbiocivicrm_url}" target="_blank">{ts domain="coop.symbiotic.symbiocivicrm"}Click here to access your CiviCRM</a>{/ts}
      </div>
      <p>{ts domain="coop.symbiotic.symbiocivicrm"}This is a one-time link that expires in 48 hours. It will open a new browser tab. You will also soon receive a welcome email with a one-time login link and more information about Spark. If the login link has expired, you can get a new one clicking on the "Forgot Password?" link under the login form. The username is "admin".{/ts}</p>
      <p>{ts domain="coop.symbiotic.symbiocivicrm"}Welcome to CiviCRM Spark!{/ts}</p>
    </div>
  </div>
</div>

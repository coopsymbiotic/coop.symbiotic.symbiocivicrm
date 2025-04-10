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
        <a target="_blank" class="btn btn-success" href="{$symbiocivicrm_url}">{ts domain="coop.symbiotic.symbiocivicrm"}Click here to access your CiviCRM instance</a>{/ts}
      </div>
    </div>
  </div>
</div>

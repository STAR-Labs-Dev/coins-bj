<footer>
  <div class="container">
      <div class="row">
          <div class="col-md-12">
          	<?php
          		$company_name = getCompanyName();
          	?>
             <p class="copyright">@lang('message.footer.copyright')&nbsp;© ezrkzeljrzel {{date('Y')}} &nbsp;&nbsp; {{ $company_name . "text" }} | @lang('message.footer.copyright-text')</p>
          </div>
      </div>
  </div>
</footer>
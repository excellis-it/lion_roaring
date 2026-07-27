@if (!empty($isUsPrefill))
    <div class="alert alert-info notranslate" translate="no" role="status">
        Showing US content as default because this country has no saved content yet.
        Saving will create content for
        <strong>{{ $prefillCountryName ?? \App\Helpers\Helper::cmsPrefillCountryName($cmsEditCountryCode ?? 'US') }}</strong>.
    </div>
@endif

<script id="session-languages-data" type="application/json">@json(\App\Helpers\Helper::getVisitorCountryLanguages())</script>
<script>
    window.sessionLanguages = JSON.parse(document.getElementById('session-languages-data').textContent);
</script>

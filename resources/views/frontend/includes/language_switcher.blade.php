@php
    use App\Helpers\Helper;

    $languageOptions = collect(Helper::getVisitorCountryLanguages())
        ->filter(fn ($lang) => !empty($lang->code))
        ->unique('code')
        ->sortBy('name')
        ->values();

    if ($languageOptions->where('code', 'en')->isEmpty()) {
        $languageOptions->prepend((object) ['code' => 'en', 'name' => 'English']);
    }

    $activeLang = null;
    if (!empty($_COOKIE['googtrans'])) {
        $gt = urldecode((string) $_COOKIE['googtrans']);
        if ($gt !== '/en/en' && preg_match('#^/auto/([^;]+)$#', $gt, $matches) && $matches[1] !== 'en') {
            $activeLang = $matches[1];
        } elseif ($gt !== '/en/en' && preg_match('#^/([^/]+)/([^;]+)$#', $gt, $matches) && $matches[1] !== $matches[2]) {
            $activeLang = $matches[2];
        }
    }
    if ($activeLang === null && !empty($_COOKIE['content_lang'])) {
        $contentLang = (string) $_COOKIE['content_lang'];
        if ($contentLang !== '__original__') {
            $activeLang = $contentLang;
        }
    }
@endphp

<select id="languageSwitcher"
    class="languageSwitcher form-select form-select-sm cst-select cst-select-bottom notranslate"
    translate="no"
    aria-label="{{ __('Select language') }}">
    <option value="__original__" {{ $activeLang === null ? 'selected' : '' }}>Original</option>
    @foreach ($languageOptions as $lang)
        <option value="{{ $lang->code }}" {{ $activeLang !== null && $lang->code === $activeLang ? 'selected' : '' }}>{{ $lang->name }}</option>
    @endforeach
</select>

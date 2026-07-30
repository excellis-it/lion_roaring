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

    // `content_lang` is the single source of truth. LrTranslate keeps it in sync
    // with localStorage on every switch; the old googtrans cookie is not read.
    $activeLang = '__original__';
    if (!empty($_COOKIE['content_lang'])) {
        $cookieLang = trim((string) $_COOKIE['content_lang']);
        if ($cookieLang !== '' && $cookieLang !== '__original__') {
            $activeLang = $cookieLang;
        }
    }
@endphp

<select id="languageSwitcher"
    class="languageSwitcher form-select form-select-sm cst-select cst-select-bottom notranslate"
    translate="no"
    data-nt
    aria-label="Select language">
    <option value="__original__" {{ $activeLang === '__original__' ? 'selected' : '' }}>Original</option>
    @foreach ($languageOptions as $lang)
        <option value="{{ $lang->code }}" {{ $lang->code === $activeLang ? 'selected' : '' }}>{{ $lang->name }}</option>
    @endforeach
</select>

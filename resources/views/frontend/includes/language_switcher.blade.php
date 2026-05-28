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

    $activeLang = 'en';
    if (!empty($_COOKIE['googtrans']) && preg_match('/\/auto\/([^;]+)/', $_COOKIE['googtrans'], $matches)) {
        $activeLang = $matches[1];
    }
@endphp

<select id="languageSwitcher"
    class="languageSwitcher form-select form-select-sm cst-select cst-select-bottom"
    aria-label="{{ __('Select language') }}">
    @foreach ($languageOptions as $lang)
        <option value="{{ $lang->code }}" {{ $lang->code === $activeLang ? 'selected' : '' }}>{{ $lang->name }}</option>
    @endforeach
</select>

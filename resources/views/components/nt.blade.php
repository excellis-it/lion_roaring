{{-- Never-translate wrapper. Use for person names and any other verbatim text:
     <x-nt>{{ $user->full_name }}</x-nt> --}}
<span {{ $attributes->merge(['class' => 'notranslate']) }} translate="no" data-nt>{{ $slot }}</span>

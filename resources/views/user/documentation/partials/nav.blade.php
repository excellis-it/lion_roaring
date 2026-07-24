<nav class="pma-docs-nav" aria-label="Documentation sections">
    <a href="{{ route('user.documentation.index') }}"
       class="pma-docs-nav-link {{ request()->routeIs('user.documentation.index') ? 'is-active' : '' }}">
        Overview
    </a>
    @foreach ($sections as $navSection)
        <a href="{{ route('user.documentation.show', $navSection['slug']) }}"
           class="pma-docs-nav-link {{ isset($section) && ($section['slug'] ?? '') === $navSection['slug'] ? 'is-active' : '' }}">
            <span>{{ $navSection['title'] }}</span>
            @if (($navSection['status'] ?? '') === 'coming_soon')
                <span class="pma-docs-badge pma-docs-badge--soon">Soon</span>
            @endif
        </a>
    @endforeach
</nav>

<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ data_get($content, 'meta.title', 'ChilamVPN') }}</title>
    <meta name="description" content="{{ data_get($content, 'meta.description', '') }}" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}" />
  </head>
  <body>
    <header class="site-header">
      <nav class="top-nav">
        <a href="{{ url('/') }}" class="top-nav__brand" aria-label="{{ data_get($content, 'hero.brand', 'ChilamVPN') }}">
          <svg viewBox="0 0 100 100" class="logo-icon" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="ChilamVPN logo">
            <path d="M70 20a40 40 0 1 0 0 60" fill="none" stroke="#f2b705" stroke-width="12" stroke-linecap="round" />
            <path d="M60 30a25 25 0 0 0 0 40" fill="none" stroke="#1c8c5e" stroke-width="8" stroke-linecap="round" />
            <path d="M50 35a18 18 0 0 0 0 30" fill="none" stroke="#00a389" stroke-width="6" stroke-linecap="round" />
          </svg>
          <span class="top-nav__brand-name">{{ data_get($content, 'hero.brand', 'ChilamVPN') }}</span>
        </a>

        <div class="top-nav__menu">
          <details class="nav-item nav-item--dropdown">
            <summary class="nav-link">
              <span>{{ data_get($content, 'nav.tools') }}</span>
              <svg aria-hidden="true" class="nav-link__caret" viewBox="0 0 10 6">
                <path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
              </svg>
            </summary>
            <ul class="nav-dropdown" role="menu">
              @foreach(data_get($content, 'nav.tools_items', []) as $tool)
                <li role="none">
                  <a role="menuitem" href="{{ $tool['href'] ?? '#' }}">{{ $tool['label'] ?? '' }}</a>
                </li>
              @endforeach
            </ul>
          </details>

          <a class="nav-link" href="{{ data_get($content, 'nav.login_href', '#') }}">{{ data_get($content, 'nav.login') }}</a>
        </div>

        <a href="{{ data_get($content, 'nav.cta_href', data_get($content, 'hero.cta_href', '#benefits')) }}" class="nav-cta">
          {{ data_get($content, 'nav.cta', data_get($content, 'hero.cta', 'Comienza ahora')) }}
        </a>
      </nav>

      <div class="hero">
        <div class="hero__content">
          <h1 class="hero__title">{{ data_get($content, 'hero.brand', 'ChilamVPN') }}</h1>
          <p class="hero__tagline">{{ data_get($content, 'hero.tagline') }}</p>
          @if(data_get($content, 'hero.cta'))
            <a href="{{ data_get($content, 'hero.cta_href', '#benefits') }}" class="cta-button">
              {{ data_get($content, 'hero.cta') }}
            </a>
          @endif
        </div>
      </div>
    </header>

    <main>
      <section id="benefits">
        <h2 class="section-title">{{ data_get($content, 'sections.benefits.title') }}</h2>
        <div class="features">
          @foreach(data_get($content, 'sections.benefits.items', []) as $item)
            <div class="feature">
              <h3>{{ data_get($item, 'title') }}</h3>
              <p>{{ data_get($item, 'body') }}</p>
            </div>
          @endforeach
        </div>
      </section>

      <section id="how-it-works">
        <h2 class="section-title">{{ data_get($content, 'sections.how.title') }}</h2>
        <div class="steps">
          @foreach(data_get($content, 'sections.how.steps', []) as $index => $step)
            <div class="step">
              <div class="step-number">{{ $index + 1 }}</div>
              <div class="step-content">
                <h4>{{ data_get($step, 'title') }}</h4>
                <p>{{ data_get($step, 'body') }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </section>

      <section id="public-ip">
        <h2 class="section-title section-title--compact">{{ data_get($content, 'sections.legal.title') }}</h2>
        <p class="section-text">
          {{ data_get($content, 'sections.legal.body') }}
        </p>
      </section>
    </main>

    <footer>
      <div class="footer__grid">
        <div class="footer__info">
          <p>{{ data_get($content, 'footer.notice') }}</p>
          <p>
            @foreach(data_get($content, 'footer.links', []) as $link)
              <a href="{{ $link['href'] ?? '#' }}">{{ $link['label'] ?? '' }}</a>@if(! $loop->last) • @endif
            @endforeach
          </p>
        </div>
        <form class="footer-language" action="{{ route('home') }}" method="get">
          <label for="language-select" class="footer-language__label">
            <svg aria-hidden="true" class="footer-language__icon" viewBox="0 0 20 20">
              <path d="M10 1.5a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17zM2.75 10h14.5M10 2.5c2 2 3.25 4.5 3.25 7.5S12 15 10 17.5M10 2.5C8 4.5 6.75 7 6.75 10S8 15 10 17.5" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sr-only">{{ data_get($content, 'ui.language_switcher_title', 'Change language') }}</span>
          </label>
          <select id="language-select" name="lang" class="footer-language__select">
            @foreach($locales as $option)
              <option value="{{ $option['code'] }}" @selected($option['active'])>{{ $option['label'] }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </footer>

    <script>
      (function () {
        const select = document.getElementById('language-select');
        if (select) {
          select.addEventListener('change', function (event) {
            const params = new URLSearchParams(window.location.search);
            params.set('lang', event.target.value);
            window.location.href = `${window.location.pathname}?${params.toString()}`;
          });
        }

        document.querySelectorAll('.nav-dropdown a').forEach(function (link) {
          link.addEventListener('click', function () {
            const parent = link.closest('details');
            if (parent) {
              parent.removeAttribute('open');
            }
          });
        });
      })();
    </script>
  </body>
</html>

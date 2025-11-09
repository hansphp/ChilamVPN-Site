<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ data_get($meta, 'title', __('ipv4.hero.title')) }}</title>
    <meta name="description" content="{{ data_get($meta, 'description', __('ipv4.desc')) }}" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="{{ asset('styles.css') }}" />
    @foreach($alternates as $alternate)
      <link rel="alternate" hreflang="{{ $alternate['hreflang'] }}" href="{{ $alternate['url'] }}">
    @endforeach
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
              @foreach($toolLinks as $tool)
                <li role="none">
                  <a role="menuitem" href="{{ $tool['href'] }}">{{ $tool['label'] }}</a>
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
          <h1 class="hero__title">{{ __('ipv4.hero.title') }}</h1>
          <p class="hero__tagline">{{ __('ipv4.hero.tagline') }}</p>
          <a href="#ipv4-calculator" class="cta-button">
            {{ __('ipv4.hero.cta') }}
          </a>
        </div>
      </div>
    </header>

    <main>
      <section
        id="ipv4-calculator"
        class="tool-section tool-section--center"
        data-ipv4-root
        data-invalid-ip="{{ __('ipv4.results.error_invalid_ip') }}"
        data-invalid-mask="{{ __('ipv4.results.error_invalid_mask') }}"
        data-locale="{{ str_replace('_', '-', app()->getLocale()) }}"
      >
        <div class="tool-headings tool-headings--center">
          <h2 class="section-title section-title--light">{{ __('ipv4.form.heading') }}</h2>
          <p class="section-text section-text--muted">{{ __('ipv4.form.hint') }}</p>
        </div>
        <div class="calculator-stack">
          <article class="calc-card calc-card--form calc-card--center">
            <h3 class="calc-card__title text-center">{{ __('ipv4.hero.title') }}</h3>
            <p class="calc-card__subtitle text-center">{{ __('ipv4.hero.tagline') }}</p>
            <form data-calculator novalidate>
              <div class="input-grid input-grid--single">
                <div class="input-row">
                  <label class="input-field input-field--grow">
                    <span>{{ __('ipv4.form.ip_label') }}</span>
                    <input id="ip-address" class="form-control form-control-lg fancy-input" type="text" inputmode="decimal" autocomplete="off" value="192.168.0.1" data-ip-input />
                  </label>
                  <label class="input-field input-field--compact">
                    <span>{{ __('ipv4.form.cidr_label') }}</span>
                    <input id="cidr-prefix" class="form-control form-control-lg fancy-input" type="number" min="0" max="32" value="24" data-cidr-input />
                  </label>
                </div>
                <label class="input-field">
                  <span>{{ __('ipv4.form.netmask_label') }}</span>
                  <input id="netmask" class="form-control form-control-lg fancy-input" type="text" inputmode="decimal" placeholder="255.255.255.0" data-netmask-input />
                </label>
              </div>
              <div class="input-actions input-actions--center">
                <div class="live-indicator">
                  <span class="live-indicator__dot" aria-hidden="true"></span>
                  <span>{{ __('ipv4.form.live_indicator') }}</span>
                </div>
                <p>{{ __('ipv4.form.helper_mobile') }}</p>
              </div>
              <div class="form-status alert alert-danger text-center" data-status role="alert" hidden></div>
            </form>
          </article>

          <article class="calc-card calc-card--results calc-card--center" data-results>
            <h3 class="calc-panel__title text-center">{{ __('ipv4.results.heading') }}</h3>
            @php
              $resultMap = [
                  'networkBits' => __('ipv4.results.network_bits'),
                  'hostBits' => __('ipv4.results.host_bits'),
                  'network' => __('ipv4.results.network_address'),
                  'broadcast' => __('ipv4.results.broadcast_address'),
                  'netmask' => __('ipv4.results.cidr_netmask'),
                  'wildcard' => __('ipv4.results.wildcard_mask'),
                  'totalHosts' => __('ipv4.results.total_hosts'),
                  'usableHosts' => __('ipv4.results.usable_hosts'),
                  'firstHost' => __('ipv4.results.first_host'),
                  'lastHost' => __('ipv4.results.last_host'),
                  'subnetCount' => __('ipv4.results.subnet_count'),
              ];
            @endphp
            <div class="calc-results calc-results--center">
              @foreach($resultMap as $key => $label)
                <div class="calc-results__item">
                  <span class="result-label">{{ $label }}</span>
                  <span class="result-value result-value--accent" data-field="{{ $key }}">-</span>
                </div>
              @endforeach
            </div>
          </article>
        </div>
      </section>

      <section class="ip-diff">
        <h2 class="section-title">{{ __('ipv4.seo.faq_title') }}</h2>
        <div class="ip-diff__grid">
          @foreach(trans('ipv4.seo.points') as $point)
            <article class="ip-card ip-card--info">
              <h3>{{ data_get($point, 'title') }}</h3>
              <p>{{ data_get($point, 'body') }}</p>
            </article>
          @endforeach
        </div>
      </section>

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

      <section id="responsible-use">
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
          @if($email = data_get($content, 'footer.contact.email'))
            <p>
              @if($label = data_get($content, 'footer.contact.label'))
                {{ $label }}
              @endif
              <a href="mailto:{{ $email }}">{{ $email }}</a>
            </p>
          @endif
          <p>
            @foreach(data_get($content, 'footer.links', []) as $link)
              <a href="{{ $link['href'] ?? '#' }}">{{ $link['label'] ?? '' }}</a>@if(! $loop->last) • @endif
            @endforeach
          </p>
        </div>
        <div class="footer-language">
          <label for="language-select" class="footer-language__label">
            <svg aria-hidden="true" class="footer-language__icon" viewBox="0 0 20 20">
              <path d="M10 1.5a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17zM2.75 10h14.5M10 2.5c2 2 3.25 4.5 3.25 7.5S12 15 10 17.5M10 2.5C8 4.5 6.75 7 6.75 10S8 15 10 17.5" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sr-only">{{ data_get($content, 'ui.language_switcher_title', 'Change language') }}</span>
          </label>
          <select id="language-select" class="footer-language__select">
            @foreach($locales as $option)
              <option value="{{ $option['code'] }}" data-url="{{ $option['url'] }}" @selected($option['active'])>
                {{ $option['label'] }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </footer>

    <script>
      (() => {
        const dropdownLinks = document.querySelectorAll('.nav-dropdown a');
        dropdownLinks.forEach((link) => {
          link.addEventListener('click', () => {
            const parent = link.closest('details');
            if (parent) {
              parent.removeAttribute('open');
            }
          });
        });

        const select = document.getElementById('language-select');
        if (select) {
          select.addEventListener('change', (event) => {
            const option = event.target.selectedOptions[0];
            const targetUrl = option?.dataset?.url;
            if (targetUrl) {
              window.location.href = targetUrl;
            }
          });
        }

      })();
    </script>
    <script type="module" src="{{ asset('js/ipv4-calculator.js') }}"></script>
  </body>
</html>

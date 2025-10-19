<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ data_get($meta, 'title', __('ip.title')) }}</title>
    <meta name="description" content="{{ data_get($meta, 'description', __('ip.desc')) }}" />
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
          <h1 class="hero__title">{{ __('ip.title') }}</h1>
          <p class="hero__tagline">{{ __('ip.desc') }}</p>
          @if(!empty($ipDetails['ip']))
            <div class="hero-ip">
              <span class="hero-ip__label">{{ __('ip.your_ip') }}</span>
              <span class="hero-ip__value" data-copy-value>{{ $ipDetails['ip'] }}</span>
              <button type="button" class="hero-ip__copy" data-copy-button data-copy-label="{{ __('ip.copy') }}">
                {{ __('ip.copy') }}
              </button>
              <span class="hero-ip__status" data-copy-status aria-live="polite"></span>
            </div>
          @endif
          @if(data_get($content, 'hero.cta'))
            <a href="{{ data_get($content, 'hero.cta_href', '#ip-insights') }}" class="cta-button">
              {{ data_get($content, 'hero.cta') }}
            </a>
          @endif
        </div>
      </div>
    </header>

    <main>
      <section id="ip-insights" class="ip-insights">
        <h2 class="section-title section-title--light">{{ __('ip.heading') }}</h2>
        <div class="ip-insights__grid">
          <article class="ip-card ip-card--primary">
            <h3>{{ __('ip.your_ip') }}</h3>
            <p class="ip-card__value" aria-live="polite">
              {{ $ipDetails['ip'] ?? __('ip.unknown') }}
            </p>
          </article>
          <article class="ip-card">
            <h3>{{ __('ip.ip_type') }}</h3>
            <p><strong>{{ $ipDetails['version'] }}</strong></p>
          </article>
          <article class="ip-card">
            <h3>{{ __('ip.location') }}</h3>
            <p>
              @if(!empty($ipDetails['country_code']))
                <img class="ip-flag" src="{{ asset('flags/' . strtolower($ipDetails['country_code']) . '.svg') }}" alt="{{ $ipDetails['country_code'] }} flag" />
              @endif
              <strong>{{ $ipDetails['location'] }}</strong>
            </p>
          </article>
          <article class="ip-card">
            <h3>{{ __('ip.isp') }}</h3>
            <p><strong>{{ $ipDetails['isp'] }}</strong></p>
          </article>
        </div>
        <p class="ip-insights__meta">
          {{ __('ip.updated') }}:
          {{ \Illuminate\Support\Carbon::parse($ipDetails['updated_at'])->locale(str_replace('-', '_', $locale))->isoFormat('LLL') }}
          <span class="ip-insights__status" data-copy-status aria-live="polite"></span>
        </p>
      </section>

      <section class="ip-diff">
        <h2 class="section-title">{{ __('ip.educational_title') }}</h2>
        <p class="ip-diff__intro">{{ __('ip.educational_body') }}</p>
        <div class="ip-diff__grid">
          <article class="ip-card ip-card--info">
            <h3>{{ __('ip.public_ip_title') }}</h3>
            <p>{{ __('ip.public_ip_body') }}</p>
          </article>
          <article class="ip-card ip-card--info">
            <h3>{{ __('ip.private_ip_title') }}</h3>
            <p>{{ __('ip.private_ip_body') }}</p>
          </article>
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
        document.querySelectorAll('.nav-dropdown a').forEach((link) => {
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

        const copyButton = document.querySelector('[data-copy-button]');
        const valueElement = document.querySelector('[data-copy-value]');
        const statusElements = document.querySelectorAll('[data-copy-status]');

        if (copyButton && valueElement) {
          copyButton.addEventListener('click', async () => {
            const value = valueElement.textContent.trim();
            if (!value || value === '{{ __('ip.unknown') }}') {
              return;
            }

            try {
              await navigator.clipboard.writeText(value);
              statusElements.forEach((node) => {
                node.textContent = ' · {{ __('ip.copied') }}';
              });
            } catch {
              statusElements.forEach((node) => {
                node.textContent = '';
              });
            }
          });
        }
      })();
    </script>
  </body>
</html>

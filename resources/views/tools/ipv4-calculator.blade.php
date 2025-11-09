<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ data_get($meta, 'title', __('ipv4.hero.title')) }}</title>
    <meta name="description" content="{{ data_get($meta, 'description', __('ipv4.desc')) }}" />
    <link rel="canonical" href="{{ url()->current() }}" />
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
      <section id="ipv4-calculator" class="tool-section">
        <div class="tool-headings">
          <h2 class="section-title section-title--light">{{ __('ipv4.form.heading') }}</h2>
          <p class="section-text section-text--muted">{{ __('ipv4.form.hint') }}</p>
        </div>
        <div class="calculator-layout">
          <article class="calc-card calc-card--form">
            <form data-calculator novalidate>
              <div class="input-grid">
                <label class="input-field">
                  <span>{{ __('ipv4.form.ip_label') }}</span>
                  <input id="ip-address" type="text" inputmode="decimal" autocomplete="off" value="192.168.0.1" data-ip-input />
                </label>
                <label class="input-field input-field--compact">
                  <span>{{ __('ipv4.form.cidr_label') }}</span>
                  <input id="cidr-prefix" type="number" min="0" max="32" value="24" data-cidr-input />
                </label>
                <label class="input-field">
                  <span>{{ __('ipv4.form.netmask_label') }}</span>
                  <input id="netmask" type="text" inputmode="decimal" placeholder="255.255.255.0" data-netmask-input />
                </label>
              </div>
              <div class="input-actions">
                <p>{{ __('ipv4.form.hint') }}</p>
                <button type="submit" class="button button--primary">{{ __('ipv4.form.submit') }}</button>
              </div>
              <p class="form-status" data-status role="alert"></p>
            </form>
          </article>

          <article class="calc-card calc-card--results" data-results>
            <h3>{{ __('ipv4.results.heading') }}</h3>
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
            <div class="calc-results">
              @foreach($resultMap as $key => $label)
                <div class="calc-results__item">
                  <span class="result-label">{{ $label }}</span>
                  <span class="result-value" data-field="{{ $key }}">-</span>
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

        const calculator = document.querySelector('[data-calculator]');
        if (!calculator) {
          return;
        }

        const ipInput = calculator.querySelector('[data-ip-input]');
        const cidrInput = calculator.querySelector('[data-cidr-input]');
        const netmaskInput = calculator.querySelector('[data-netmask-input]');
        const status = calculator.querySelector('[data-status]');
        const resultFields = document.querySelectorAll('[data-field]');
        const numberFormat = new Intl.NumberFormat(document.documentElement.lang || 'en');
        const messages = {
          invalidIp: '{{ __('ipv4.results.error_invalid_ip') }}',
          invalidMask: '{{ __('ipv4.results.error_invalid_mask') }}',
        };

        const padBinary = (value) => value.toString(2).padStart(8, '0');

        const ipToInt = (value) => {
          const parts = value.split('.').map((chunk) => Number(chunk.trim()));
          if (parts.length !== 4 || parts.some((part) => Number.isNaN(part) || part < 0 || part > 255)) {
            return null;
          }

          return parts.reduce((acc, part) => acc * 256 + part, 0);
        };

        const intToIp = (value) => {
          const normalized = value >>> 0;
          return [
            (normalized >>> 24) & 255,
            (normalized >>> 16) & 255,
            (normalized >>> 8) & 255,
            normalized & 255,
          ].join('.');
        };

        const prefixToMask = (prefix) => {
          if (!Number.isInteger(prefix) || prefix < 0 || prefix > 32) {
            return null;
          }

          const mask = prefix === 0 ? 0 : (0xffffffff << (32 - prefix)) >>> 0;
          return intToIp(mask);
        };

        const maskToPrefix = (mask) => {
          const maskInt = ipToInt(mask);
          if (maskInt === null) {
            return null;
          }

          const binary = padBinary((maskInt >>> 24) & 255) + padBinary((maskInt >>> 16) & 255) + padBinary((maskInt >>> 8) & 255) + padBinary(maskInt & 255);
          if (!/^1*0*$/.test(binary)) {
            return null;
          }

          return binary.replace(/0+/g, '').length;
        };

        const wildcardFromMask = (mask) => {
          const maskInt = ipToInt(mask);
          if (maskInt === null) {
            return null;
          }

          const wildcard = (~maskInt) >>> 0;
          return intToIp(wildcard);
        };

        const setStatus = (message = '') => {
          status.textContent = message;
          status.hidden = !message;
        };

        const formatNumber = (value) => numberFormat.format(value);

        const hydrateResults = (payload) => {
          const mapping = {
            networkBits: payload.networkBits,
            hostBits: payload.hostBits,
            network: payload.network,
            broadcast: payload.broadcast,
            netmask: payload.netmask,
            wildcard: payload.wildcard,
            totalHosts: formatNumber(payload.totalHosts),
            usableHosts: formatNumber(payload.usableHosts),
            firstHost: payload.firstHost,
            lastHost: payload.lastHost,
            subnetCount: formatNumber(payload.subnetCount),
          };

          resultFields.forEach((field) => {
            const key = field.dataset.field;
            field.textContent = mapping[key] ?? '-';
          });
        };

        const calculate = () => {
          const ipValue = ipInput.value.trim();
          const ipInt = ipToInt(ipValue);

          if (ipInt === null) {
            setStatus(messages.invalidIp);
            return;
          }

          let prefix = Number.parseInt(cidrInput.value, 10);
          if (!Number.isInteger(prefix) || prefix < 0 || prefix > 32) {
            setStatus(messages.invalidMask);
            return;
          }

          const netmaskValue = prefixToMask(prefix);
          if (!netmaskValue) {
            setStatus(messages.invalidMask);
            return;
          }

          if (netmaskInput.value.trim() !== netmaskValue) {
            netmaskInput.value = netmaskValue;
          }

          const wildcard = wildcardFromMask(netmaskValue);
          const hostBits = 32 - prefix;
          const network = (ipInt & ipToInt(netmaskValue)) >>> 0;
          const broadcast = (network | ipToInt(wildcard ?? '0.0.0.0')) >>> 0;
          const totalHosts = hostBits === 0 ? 1 : 2 ** hostBits;
          let usableHosts;
          if (prefix === 31) {
            usableHosts = 2;
          } else if (prefix === 32) {
            usableHosts = 1;
          } else {
            usableHosts = Math.max(totalHosts - 2, 0);
          }
          const firstHost = prefix >= 31 ? network : network + 1;
          const lastHost = prefix >= 31 ? broadcast : broadcast - 1;
          const subnetCount = prefix === 0 ? 1 : 2 ** prefix;

          hydrateResults({
            networkBits: prefix,
            hostBits,
            network: intToIp(network),
            broadcast: intToIp(broadcast),
            netmask: netmaskValue,
            wildcard: wildcard ?? '0.0.0.0',
            totalHosts,
            usableHosts,
            firstHost: intToIp(firstHost >>> 0),
            lastHost: intToIp(lastHost >>> 0),
            subnetCount,
          });
          setStatus('');
        };

        cidrInput.addEventListener('input', () => {
          const prefix = Number.parseInt(cidrInput.value, 10);
          const mask = prefixToMask(prefix);
          if (mask) {
            netmaskInput.value = mask;
          }
        });

        netmaskInput.addEventListener('blur', () => {
          const prefix = maskToPrefix(netmaskInput.value.trim());
          if (prefix !== null) {
            cidrInput.value = prefix;
          }
        });

        calculator.addEventListener('submit', (event) => {
          event.preventDefault();
          calculate();
        });

        calculate();
      })();
    </script>
  </body>
</html>

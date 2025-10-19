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
    <header>
      <div class="logo-wrapper">
        <svg viewBox="0 0 100 100" class="logo-icon" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="ChilamVPN logo">
          <path d="M70 20a40 40 0 1 0 0 60" fill="none" stroke="#f2b705" stroke-width="12" stroke-linecap="round" />
          <path d="M60 30a25 25 0 0 0 0 40" fill="none" stroke="#1c8c5e" stroke-width="8" stroke-linecap="round" />
          <path d="M50 35a18 18 0 0 0 0 30" fill="none" stroke="#00a389" stroke-width="6" stroke-linecap="round" />
        </svg>
        <h1>{{ data_get($content, 'hero.brand', 'ChilamVPN') }}</h1>
      </div>
      <p class="tagline">{{ data_get($content, 'hero.tagline') }}</p>
      @if(data_get($content, 'hero.cta'))
        <a href="{{ data_get($content, 'hero.cta_href', '#benefits') }}" class="cta-button">
          {{ data_get($content, 'hero.cta') }}
        </a>
      @endif
    </header>

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

    <section class="language-switcher" aria-labelledby="language-switcher-title">
      <div class="language-switcher__content">
        <h2 id="language-switcher-title">{{ data_get($content, 'ui.language_switcher_title', 'Change language') }}</h2>
        <p>{{ data_get($content, 'ui.language_switcher_hint') }}</p>
        <ul class="language-switcher__list" role="list">
          @foreach($locales as $option)
            <li>
              @if($option['active'])
                <span class="language-switcher__link language-switcher__link--active" aria-current="true">
                  {{ $option['label'] }}
                </span>
              @else
                <a class="language-switcher__link" href="{{ $option['url'] }}">
                  {{ $option['label'] }}
                </a>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    </section>

    <footer>
      <p>{{ data_get($content, 'footer.notice') }}</p>
      <p>
        @foreach(data_get($content, 'footer.links', []) as $link)
          <a href="{{ $link['href'] ?? '#' }}">{{ $link['label'] ?? '' }}</a>@if(! $loop->last) • @endif
        @endforeach
      </p>
    </footer>
  </body>
</html>


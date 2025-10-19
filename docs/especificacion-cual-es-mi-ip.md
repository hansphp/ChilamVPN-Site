ESPECIFICACIÓN FUNCIONAL Y TÉCNICA – “¿Cuál es mi IP?” (Laravel 11)
Versión: 1.0  
Autor: _pendiente_  
Fecha: _pendiente_

## 1. Objetivo
Construir una aplicación web tipo “¿Cuál es mi IP?” sobre Laravel 11 que:
- Muestre la IP pública del visitante (IPv4/IPv6), su ubicación aproximada y datos de red (ISP/ASN).
- Rinda SEO mundial gracias a rutas legibles por idioma (`/{locale}/{slug}`) y hreflang.
- Aproveche el diseño existente de `public/index.html` como base visual, reutilizándolo en la versión SSR.
- Ofrezca i18n con al menos cuatro locales.
- Mantenga buenas prácticas de rendimiento, seguridad, privacidad y caché.

## 2. Alcance (MVP)
### A. Página principal (SSR)
Replica el diseño actual de `index.html` e incluye los bloques dinámicos:
- Tu IP (con indicador aria-live).
- Tipo de IP (IPv4/IPv6).
- Ubicación aproximada (país, región, ciudad).
- Datos de red (ISP/ASN).
- Botón “Copiar IP”.
- Texto educativo breve localizado.

### B. API interna
- `GET /api/ip` → IP del visitante actual.
- `GET /api/geo` → Geo del visitante actual.
- `GET /api/lookup?ip=1.2.3.4` (opcional) → Geo de una IP arbitraria, protegido con throttle y `X-Robots-Tag: noindex`.

### C. Internacionalización
Contenido y rutas localizadas para cuatro locales iniciales, escalables a más.

### D. SEO
Hreflang para cada idioma y `x-default`. Las rutas ` /api/* ` quedan fuera del índice de buscadores.

### E. Caché y rendimiento
Uso de Redis (si está disponible) para cachear geodatos. Cabeceras `Cache-Control` adecuadas en la API.

## 3. Arquitectura general
- **Framework**: Laravel 11 (PHP 8.2+).
- **Frontend**: Blade + assets estáticos existentes (`public/index.html`, `styles.css`).
- **SSR**: Controladores renderizan vistas Blade con datos IP/Geo.
- **Geo lookups**: Servicio `GeoService` con soporte para MaxMind local y/o API remota.
- **Capa de soporte**: `IpResolver` para obtener la IP real del visitante.
- **Internacionalización**: Archivos `resources/lang/{locale}/home.php` + middleware para fijar locale.
- **Caché**: Redis (vía cache driver de Laravel) con HMAC de IP.
- **Seguridad**: Cabeceras anti-indexación en la API, CSP estricta, validaciones de IP, rate limiting.

## 4. Rutas
### 4.1 Web (`routes/web.php`)
| Ruta | Método | Controlador | Descripción |
|------|--------|-------------|-------------|
| `/` | GET | _Closure_ | Sirve el archivo estático `public/index.html` sin redirecciones. |
| `/{locale}/{slug}` | GET | `HomeController@index` | Render SSR del sitio en el idioma solicitado. |

- `locale` debe igualar alguna entrada de `config('app.supported_locales')`.
- `slug` se valida contra un mapa de slugs por locale (ver sección 8).
- Middleware `SetLocaleFromRoute` fija el locale antes de ejecutar el controlador.

### 4.2 API (`routes/api.php`)
| Ruta | Método | Controlador | Notas |
|------|--------|-------------|-------|
| `/api/ip` | GET | `Api\IpController@show` | Respuesta JSON con IP y versión. |
| `/api/geo` | GET | `Api\GeoController@show` | Respuesta JSON con datos geo. |
| `/api/lookup` | GET | `Api\GeoController@lookup` (opcional) | Requiere parámetro `ip`, throttle `geo-lookup`, cabecera `X-Robots-Tag: noindex`. |

Todas las rutas `/api/*` añaden `X-Robots-Tag: noindex`.

## 5. Middleware
### `app/Http/Middleware/SetLocaleFromRoute.php`
- Obtiene `locale` desde la ruta.
- Verifica presencia en `config('app.supported_locales')`.
- Llama a `App::setLocale($locale)` y exporta el valor en `app()->setLocale()`.
- No realiza redireccionamientos en `/`.

## 6. Servicios de soporte
### 6.1 `app/Support/IpResolver.php`
Orden de resolución (anti-spoofing):
1. Header `Forwarded` (RFC 7239) → extraer primer `for=` válido.
2. Header `CF-Connecting-IP` o `True-Client-IP`.
3. Header `X-Forwarded-For` → primera IP válida.
4. Fallback a `REMOTE_ADDR`.

Validación:
- `FILTER_VALIDATE_IP` con `FILTER_FLAG_NO_PRIV_RANGE` y `FILTER_FLAG_NO_RES_RANGE`.
- Retorna IP pública válida o `null`.
- Puede exponerse como `IpResolver::clientIp(Request $request): ?string`.

### 6.2 `app/Services/GeoService.php`
- Método `lookup(string $ip, string $locale): array`.
- Clave de caché: `geo:` + `hash_hmac('sha256', "{$ip}|{$locale}", config('services.geo.ip_hash_key'))`.
- TTL: 10 minutos.
- Fuentes de datos:
  - Si existen archivos MaxMind (`GeoLite2-City.mmdb`, `GeoLite2-ASN.mmdb`), utilizarlos.
  - Si no, fallback HTTP (configurable vía `.env`: `GEO_API_URL`, `GEO_API_TOKEN`).
- Campos mínimos devueltos:
  - `ip`, `country`, `region`, `city`
  - `lat`, `lon`, `accuracy_radius_km`
  - `timezone`
  - `asn` (`number`, `name`)
- No almacenar IP cruda en caché (solo HMAC de la clave).

## 7. Controladores
### 7.1 `app/Http/Controllers/HomeController.php`
- Dependencias: `IpResolver`, `GeoService`, mapa de slugs/locales.
- Flujo:
  1. Resolver IP del visitante.
  2. Determinar versión (`IPv4`/`IPv6`).
  3. Consultar `GeoService::lookup($ip, $locale)` si la IP existe.
  4. Construir `alternates` (`locale` → URL) para hreflang, incluyendo `x-default`.
  5. Preparar textos localizados (`title`, `desc`, etc.).
  6. Renderizar `view('ip.index', [...])`.
  7. Adjuntar cabecera `Content-Language: {locale}` y CSP (`Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self'; ...`).

### 7.2 `app/Http/Controllers/Api/IpController.php`
- Respuesta JSON:
  ```json
  {
    "ip": "203.0.113.42",
    "version": "IPv4"
  }
  ```
- Cabeceras:
  - `Cache-Control: public, s-maxage=30, stale-while-revalidate=60`
  - `X-Robots-Tag: noindex`

### 7.3 `app/Http/Controllers/Api/GeoController.php`
- `show()`:
  - Usa `IpResolver` para obtener IP.
  - `GeoService::lookup`.
  - Mismo modelo de respuesta descrito en la sección 9.
  - Cabeceras: `Cache-Control: public, s-maxage=300, stale-while-revalidate=600`, `X-Robots-Tag: noindex`.
- `lookup()` (opcional):
  - Valida parámetro `ip` (`FILTER_VALIDATE_IP` + flags NO_PRIV/NO_RES).
  - Aplica `RateLimiter::for('geo-lookup', ...)` (30 solicitudes/minuto por IP origen).
  - Respuesta JSON con geodatos.
  - Cabeceras: `Cache-Control` similar a `show`, `X-Robots-Tag: noindex`.

## 8. Internacionalización (i18n)
- Configuración: `config/app.php` agrega `'supported_locales' => ['es', 'es-LA', 'en', 'pt-BR', 'fr-FR']`.
- Archivos de idioma:
  - `resources/lang/en/ip.php`
  - `resources/lang/es/ip.php`
  - `resources/lang/es-LA/ip.php`
  - `resources/lang/pt-BR/ip.php`
  - `resources/lang/fr-FR/ip.php`
- Claves mínimas:
  - `title`, `desc`, `your_ip`, `ip_type`, `ipv4`, `ipv6`, `location`, `isp`, `copy`, `copied`, `updated`, `educational_text`.
  - Otros textos del diseño (CTA, encabezados, descripciones).
- Slugs por locale (config centralizada, p. ej. `config/seo.php`):
  ```php
  return [
      'slugs' => [
          'es'    => 'cual-es-mi-ip',
          'es-LA' => 'cual-es-mi-ip',
          'en'    => 'what-is-my-ip',
          'pt-BR' => 'qual-e-meu-ip',
          'fr-FR' => 'quelle-est-mon-ip',
      ],
  ];
  ```
- Las rutas deben validar que `{slug}` coincide con el valor configurado para `{locale}`.

## 9. Modelo de datos – API
### 9.1 `GET /api/ip`
```json
{
  "ip": "203.0.113.42",
  "version": "IPv4"
}
```

### 9.2 `GET /api/geo`
```json
{
  "ip": "203.0.113.42",
  "country": "Mexico",
  "region": "Jalisco",
  "city": "Guadalajara",
  "lat": 20.6597,
  "lon": -103.3496,
  "accuracy_radius_km": 10,
  "timezone": "America/Mexico_City",
  "asn": { "number": 8151, "name": "Uninet S.A. de C.V." }
}
```

### 9.3 `GET /api/lookup?ip=8.8.8.8` (opcional)
- Misma estructura que `/api/geo`.
- Cabecera `X-Robots-Tag: noindex`.

## 10. Vistas
- `public/index.html`: se mantiene como artefacto estático y se sirve en `/`.
- `resources/views/ip/index.blade.php`:
  - Replica el diseño existente (`header`, secciones de beneficios, pasos, etc.).
  - Inserta variables Blade (`{{ $ip }}`, `{{ $geo['country'] ?? trans('home.unknown') }}`, etc.).
  - Incluye `<link rel="alternate" hreflang="xx-YY" href="...">` para cada locale soportado.
  - Incluye `<link rel="alternate" hreflang="x-default" href="/en/what-is-my-ip">`.
  - Añade `<meta name="description" content="{{ $desc }}">` localizado.
  - Botón “Copiar IP” con JavaScript mínimo (sin frameworks) y feedback accesible (`aria-live`).
  - Mantiene los estilos de `styles.css` (puede importarse con `<link rel="stylesheet" href="{{ mix('css/app.css') }}">` o referenciar `styles.css` trasladado a `resources/css` si se usa Vite).
  - Agrega cabeceras `<script>` restringidas para la funcionalidad mínima (`navigator.clipboard.writeText`).

## 11. SEO y Metadatos
- Hreflang completo + `x-default`.
- Cabeceras `Content-Language` y `X-Robots-Tag` (solo API).
- Opción de `<link rel="canonical" href="{{ url()->current() }}">` por cada locale.
- Evitar indexación de `/api/*` (cabecera + robots.txt).
- Metadatos localizados (`title`, `description`).

## 12. Seguridad y Privacidad
- No persistir IPs crudas; las claves de caché usan HMAC.
- Validar IP de entrada y descartar privadas/loopback.
- `X-Robots-Tag: noindex` para `/api/*`.
- CSP estricta en la vista SSR:
  ```
  default-src 'self';
  style-src 'self';
  script-src 'self';
  img-src 'self';
  font-src 'self';
  base-uri 'self';
  frame-ancestors 'none';
  ```
- HTTPS obligatorio (configurar en servidor).
- Rate limiting para `/api/lookup`.

## 13. Rendimiento y Caché
- Redis recomendado para `GeoService` (configurable vía `.env`).
- Cabeceras `Cache-Control` para API:
  - `/api/ip`: `public, s-maxage=30, stale-while-revalidate=60`
  - `/api/geo`: `public, s-maxage=300, stale-while-revalidate=600`
- `GeoService` TTL de 10 minutos.
- Opcional: ETag/Last-Modified en `/api/geo`.
- CDN/Proxy configurado para respetar cabeceras.

## 14. Configuración (.env)
Agregar variables:
```
IP_HASH_KEY=change-me
MAXMIND_CITY_DB=storage/app/maxmind/GeoLite2-City.mmdb
MAXMIND_ASN_DB=storage/app/maxmind/GeoLite2-ASN.mmdb
GEO_API_URL=
GEO_API_TOKEN=
```
- `IP_HASH_KEY` debe ser secreta y larga.
- Rutas a bases MaxMind ajustadas a la infraestructura.

## 15. Rate limiting
- Definir en `RouteServiceProvider::boot()`:
  ```php
  RateLimiter::for('geo-lookup', function (Request $request) {
      return Limit::perMinute(30)->by($request->ip());
  });
  ```
- Aplicar middleware `throttle:geo-lookup` a `/api/lookup`.

## 16. Pruebas (aceptación)
- `GET /` → devuelve `public/index.html`.
- `GET /es-LA/cual-es-mi-ip` → render SSR con IP real y geodatos (cuando disponibles), hreflang completo.
- `GET /en/what-is-my-ip`, `/pt-BR/qual-e-meu-ip`, `/fr-FR/quelle-est-mon-ip` → contenidos localizados correctos.
- `GET /api/ip` → JSON con IP y versión en <200 ms (local).
- `GET /api/geo` → JSON con campos completos y cabeceras de caché.
- `GET /api/*` → cabecera `X-Robots-Tag: noindex`.
- Inspección de caché → no hay IPs crudas, solo claves HMAC.
- Verificación de CSP y headers de seguridad (Content-Language, Cache-Control).

## 17. Despliegue y servidor web
- Configurar Nginx/Apache para servir `/public/index.html` en `/`.
- `try_files` apuntando a `public/index.php` para rutas `/{locale}/{slug}` y `/api/*`.
- Asegurar que `/api/*` responda JSON sin caché de página completa del servidor (respetar cabeceras).
- Considerar cacheo en CDN para `/api/ip` y `/api/geo` según cabeceras.

## 18. Futuras mejoras (no bloqueantes)
- Selector de idioma en UI con enlaces directos a `/{locale}/{slug}`.
- Subdominios específicos para IPv4/IPv6.
- Contenido educativo ampliado y páginas secundarias.
- Pseudolocalización y soporte RTL.
- CI que valide locales y slugs (linters personalizados).

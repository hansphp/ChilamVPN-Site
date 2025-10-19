![ChilamVPN banner](public/logo.svg)

# ChilamVPN Site

Landing multilenguaje de ChilamVPN construida con Laravel 11. La página inicial se renderiza en servidor reutilizando el diseño estático del proyecto y consume contenido localizado desde archivos JSON cacheados vía Redis.

> Estado actual: MVP centrado en marketing. La obtención de IP y geodatos llegará en la siguiente fase.

---

## Contenido
- [Requisitos](#requisitos)
- [Configuracion inicial](#configuracion-inicial)
- [Ejecucion en desarrollo](#ejecucion-en-desarrollo)
- [Internacionalizacion](#internacionalizacion)
- [Cache y Redis](#cache-y-redis)
- [Pruebas](#pruebas)
- [Checklist de despliegue](#checklist-de-despliegue)
- [Despliegue en produccion](#despliegue-en-produccion)
- [Mantenimiento](#mantenimiento)

---

## Requisitos

| Componente | Version recomendada |
|------------|---------------------|
| PHP        | 8.2.x con extensiones `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml` |
| Composer   | 2.x |
| Node.js    | >= 18 LTS (solo si se construyen assets con Vite) |
| Redis      | 6.x o superior (opcional, recomendado para cache) |
| Base de datos | No requerida en este MVP |

---

## Configuracion inicial

1. Clonar el repositorio:
   ```bash
   git clone git@github.com:tu-org/chilamvpn-site.git
   cd chilamvpn-site
   ```

2. Instalar dependencias PHP:
   ```bash
   composer install
   ```

3. (Opcional) Instalar dependencias front-end:
   ```bash
   npm install
   ```

4. Copiar variables de entorno:
   ```bash
   cp .env.example .env
   ```

5. Generar la clave de aplicacion:
   ```bash
   php artisan key:generate
   ```

6. Ajustar `.env`:
   ```dotenv
   APP_NAME="ChilamVPN"
   APP_ENV=local
   APP_URL=http://localhost

   CACHE_DRIVER=file        # usar redis en produccion
   SESSION_DRIVER=file

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   APP_LOCALE=es
   ```

7. (Opcional) Construir assets:
   ```bash
   npm run build
   ```

---

## Ejecucion en desarrollo

```bash
php artisan serve
```

Entrar a `http://localhost:8000`. El idioma activo se decide asi:
1. Parametro `?lang=xx`.
2. Valor guardado en sesion.
3. Cabecera `Accept-Language`.
4. Locale por defecto (`config/locales.php`).

---

## Internacionalizacion

- Configuracion de idiomas: `config/locales.php`.
- Contenido por idioma: `resources/content/home/{locale}.json`.
- Vista SSR: `resources/views/home.blade.php`.

Agregar un idioma nuevo:
1. Registrar el locale y sus alias en `config/locales.php`.
2. Crear el archivo JSON con la estructura existente.
3. Limpiar cache (`php artisan cache:clear`) si el contenido estaba almacenado.

---

## Cache y Redis

El servicio `LocalizedContent` carga los JSON y los guarda en cache por una hora usando el driver definido en `CACHE_DRIVER`. Para aprovechar Redis:

```dotenv
CACHE_DRIVER=redis
REDIS_PREFIX=chilamvpn_site_
```

Comandos utiles:
```bash
php artisan cache:clear
php artisan config:clear
```

---

## Pruebas

```bash
phpunit
```

Validaciones manuales sugeridas:
- Cambiar el idioma del navegador y recargar la pagina.
- Probar `/?lang=en` o `/?lang=pt-br`.
- Confirmar que el idioma se mantiene al navegar y recargar.

---

## Checklist de despliegue

- [ ] `APP_ENV=production` y `APP_DEBUG=false`.
- [ ] `APP_URL` apunta al dominio publico.
- [ ] Redis configurado y accesible (`CACHE_DRIVER=redis`).
- [ ] `php artisan config:cache` y `php artisan route:cache` ejecutados.
- [ ] HTTPS forzado desde el servidor o CDN.
- [ ] Document root configurado en `public/`.
- [ ] Compresion gzip o brotli habilitada.
- [ ] `robots.txt` ajustado si se requieren reglas especiales para `/api/*`.

---

## Despliegue en produccion

Ejemplo de pasos (Forge, Vapor, Envoy u otro pipeline):

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force  # opcional si se agregan tablas
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Configurar el servidor web:
- Document root: `/ruta/al/proyecto/public`.
- Regla `try_files $uri $uri/ /index.php?$query_string;`.
- Variables de entorno seguras gestionadas via `.env` o secretos del proveedor.

---

## Mantenimiento

- Actualizar traducciones: editar JSON y limpiar cache.
- Agregar idiomas: seguir los pasos de [Internacionalizacion](#internacionalizacion).
- Supervisar Redis: revisar memoria, claves caducadas y TTL.
- Mantener dependencias:
  ```bash
  composer update
  npm update
  ```
- Verificar periodicamente SEO (hreflang, canonical) y Core Web Vitals.

---

(c) 2025 ChilamVPN. Todos los derechos reservados.

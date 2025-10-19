# Guía de configuración de MaxMind

Este documento explica cómo descargar y mantener las bases GeoLite2 de MaxMind para la aplicación ChilamVPN. El servicio `GeoService` utiliza los archivos `.mmdb` para resolver ubicación, ISP y ASN a partir de la IP pública del visitante.

---

## 1. Credenciales necesarias

Para usar GeoLite2 debes tener una cuenta en [MaxMind](https://www.maxmind.com/). Dentro de tu cuenta encontrarás:

- **Account ID**
- **License Key**
- **Edition IDs** (colección de bases que deseas descargar)

Con estos datos se completa el archivo de configuración `GeoIP.conf` que usará la herramienta `geoipupdate`.

**Ejemplo de GeoIP.conf:**

```
AccountID 123456
LicenseKey ABCDEF1234567890
EditionIDs GeoLite2-City GeoLite2-ASN
DatabaseDirectory /absolute/path/to/storage/app/maxmind
```

> ⚠️ No agregues este archivo al repositorio; contiene credenciales.

---

## 2. Preparar el proyecto

1. **Crear carpeta para las bases:**
   ```bash
   mkdir -p storage/app/maxmind
   ```

2. **Configura el `.env`:**
   ```dotenv
   IP_HASH_KEY=define-una-clave-larga
   MAXMIND_CITY_DB=storage/app/maxmind/GeoLite2-City.mmdb
   MAXMIND_ASN_DB=storage/app/maxmind/GeoLite2-ASN.mmdb
   ```
   Estas rutas deben coincidir con la carpeta donde se almacenarán las bases.

3. (Opcional) Si vas a utilizar un proveedor HTTP como fallback, define también:
   ```dotenv
   GEO_API_URL=https://tu-api-geo/
   GEO_API_TOKEN=token
   ```

---

## 3. Instalar `geoipupdate`

`geoipupdate` es la utilidad oficial de MaxMind para descargar y actualizar las bases. Instálala según tu sistema:

- **macOS (Homebrew):**
  ```bash
  brew install geoipupdate
  ```
- **Debian/Ubuntu:**
  ```bash
  sudo apt-get install geoipupdate
  ```
- **Rocky Linux 9 / RHEL 9:**
  ```bash
  sudo dnf install epel-release
  sudo dnf install geoipupdate
  ```
- **Windows:** descarga el binario desde la [página oficial](https://dev.maxmind.com/geoip/updating-databases?lang=en#installing-geoip-update).

---

## 4. Guardar `GeoIP.conf`

Coloca tu archivo `GeoIP.conf` en un directorio seguro (por ejemplo `~/.config/GeoIP.conf`). Asegúrate de que `DatabaseDirectory` apunte a la ubicación creada en el paso 2.

Ejemplo en macOS/Linux:
```bash
mkdir -p ~/.config
cp GeoIP.conf ~/.config/GeoIP.conf
```

---

## 5. Descargar las bases

Ejecuta el comando:
```bash
geoipupdate -f ~/.config/GeoIP.conf
```
Esto descargará los archivos `.mmdb` en `storage/app/maxmind`.

Verifica:
```bash
ls storage/app/maxmind
# GeoLite2-City.mmdb  GeoLite2-ASN.mmdb
```

Una vez presentes, la aplicación podrá resolver datos de geolocalización. No hace falta reiniciar Laravel.

---

## 6. Automatizar actualizaciones

Las bases GeoLite2 se actualizan mensualmente. Configura una tarea programada en tu servidor o pipeline CI/CD:

- **Cron en Linux:**
  ```cron
  0 3 1 * * geoipupdate -f ~/.config/GeoIP.conf >/var/log/geoipupdate.log 2>&1
  ```
  Esto ejecuta la actualización el día 1 de cada mes a las 03:00.

- **GitHub Actions / CI:** agrega un job que descargue e instale `geoipupdate` y ejecute el comando anterior antes del despliegue.

---

## 7. Fallos y fallback HTTP

- Si `geoipupdate` falla, `GeoService` intentará usar el proveedor HTTP configurado con `GEO_API_URL` y `GEO_API_TOKEN`.
- Verifica los logs en `storage/logs/laravel.log` para detectar errores de lectura de MaxMind.
- Asegúrate de que `IP_HASH_KEY` nunca quede vacío; se usa para anonimizar la IP en caché.

---

## 8. Troubleshooting

| Problema | Posible causa | Solución |
|----------|---------------|----------|
| `RuntimeException: IP_HASH_KEY is missing.` | Variable no definida en `.env`. | Añade `IP_HASH_KEY` y limpia la caché de configuración (`php artisan config:clear`). |
| Las respuestas JSON no tienen datos geo | Archivos `.mmdb` no existen o están corruptos. | Repite el paso 5. Si persiste, verifica permisos de la carpeta `storage/app/maxmind`. |
| `geoipupdate` devuelve 401 | Credenciales incorrectas. | Revisa `AccountID`, `LicenseKey` y `EditionIDs` en `GeoIP.conf`. |
| Archivos `.mmdb` no se actualizan | Comando no programado. | Configura cron o ejecuta manualmente `geoipupdate`. |

---

## 9. Resumen

1. Define rutas y llaves en `.env`.
2. Instala `geoipupdate` y prepara `GeoIP.conf`.
3. Ejecuta `geoipupdate` para descargar `GeoLite2-City.mmdb` y `GeoLite2-ASN.mmdb`.
4. Automiza la actualización mensual.

Con estos pasos, ChilamVPN dispondrá de datos de ubicación precisos sin exponer las IPs originales en caché.

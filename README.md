<p align="center">
    <a href=""_blank>
      <img src="docs/assets/logoTipo.png" width="260" alt="Logo de CoffeeNova API">
    </a>
</p>

<p align="center">
  <a href="https://www.linkedin.com/in/maicol-duvan-gasca-rodas-4483923a4/?trk=public-profile-join-page" target="_blank" title="LinkedIn" style="text-decoration:none;">
    <img src="docs/assets/social/linkedin.png" height="22" alt="LinkedIn" style="vertical-align:middle;">
    <span style="margin-left:6px; vertical-align:middle;">LinkedIn</span>
  </a>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="https://www.instagram.com/maicolddox_?utm_source=qr&igsh=cTV6enRlMW05bjY3" target="_blank" title="Instagram" style="text-decoration:none;">
    <img src="docs/assets/social/instagram.png" height="22" alt="Instagram" style="vertical-align:middle;">
    <span style="margin-left:6px; vertical-align:middle;">Instagram</span>
  </a>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="https://github.com/MaicolDdox" target="_blank" title="GitHub" style="text-decoration:none;">
    <img src="docs/assets/social/github.png" height="22" alt="GitHub" style="vertical-align:middle;">
    <span style="margin-left:6px; vertical-align:middle;">GitHub</span>
  </a>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="https://discordapp.com/users/1425631850453270543" target="_blank" title="Discord" style="text-decoration:none;">
    <img src="docs/assets/social/discord.png" height="22" alt="Discord" style="vertical-align:middle;">
    <span style="margin-left:6px; vertical-align:middle;">Discord</span>
  </a>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="mailto:maicolindustriascode@gmail.com" target="_blank" title="Email" style="text-decoration:none;">
    <img src="docs/assets/social/gmail.png" height="22" alt="Email" style="vertical-align:middle;">
    <span style="margin-left:6px; vertical-align:middle;">Email</span>
  </a>
</p>

<div align="center">
  <h1>CoffeeNova API</h1>
  <p>API REST para el catalogo de cafe, carrito y pedidos de la demo CoffeeNova.</p>
</div>

## Descripcion
CoffeeNova API es un backend en Laravel 12 para una tienda de cafe.
Incluye autenticacion por tokens (Laravel Sanctum) y roles/permisos (Spatie).
Permite listar y gestionar cafes, manejar carrito y crear pedidos con checkout simulado.
Pensada para ejecutar un demo local con datos precargados.

## Requisitos
- PHP 8.2+
- Composer
- MySQL o MariaDB
- Node.js + npm (solo si vas a compilar assets con Vite)

## Instalacion rapida
```bash
git clone https://github.com/MaicolDdox/-Coffeenova-API.git
cd coffeenova-api
composer install
```

```bash
# Windows (PowerShell/CMD)
copy .env.example .env

# macOS/Linux
cp .env.example .env

php artisan key:generate
```

Configura la base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backendapi
DB_USERNAME=root
DB_PASSWORD=
```

## Demo y Seeders (OBLIGATORIO)
**OBLIGATORIO:** Ejecuta los seeders porque:
- cargan el demo (catalogo de cafes)
- crean roles y permisos
- crean la cuenta administradora

Opcion A:
```bash
php artisan migrate --seed
```

Opcion B:
```bash
php artisan migrate
php artisan db:seed
```

## Credenciales Admin (Demo)
- Email: `admin@coffee.test`
- Password: `password`

## Ejecucion
```bash
php artisan serve
```
La API quedara disponible en `http://127.0.0.1:8000`.

## Endpoints / Documentacion
No hay Swagger/Postman incluidos. Revisa `routes/api.php` para la lista completa de rutas.

## Troubleshooting
- `APP_KEY` vacia: ejecuta `php artisan key:generate`.
- `.env` no creado o sin DB correcta: revisa credenciales y crea la base de datos.
- Errores de permisos en `storage/` o `bootstrap/cache/`: ajusta permisos.
- Migraciones/seeders no ejecutados: corre `php artisan migrate --seed`.
- Imagenes locales no se sirven: ejecuta `php artisan storage:link`.

## Autor / Creditos
Este README aplica exclusivamente para **coffeenova-api**.

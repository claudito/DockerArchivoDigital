# Despliegue de contenedor en Producción
---

### `1. Clonar Repositorio`
Ingresar directorio:
`cd /opt`
Clonar repositorio:
```bash
git clone  https://github.com/claudito/DockerArchivoDigital.git
```
### `2. Desplegar Contenedor`
```bash
docker compose up -d --build
```

### `3. Crear Archivo .env`
Listar Contenedores:
```bash
docker ps
```
Ingresar al contenedor:
```bash
docker exec -it <Id del Contenedor> bash
```

Crear archivo .env:
```bash
nano .env
```
Copiar los variables de entorno en el `.env`

Actualizar Valores .env de acuerdo al entorno :
```bash
# Modo Local
APP_ENV=local
APP_URL=http://localhost 

# Modo Producción
APP_ENV=production
APP_URL=https://dirislimaeste.xyz/
APP_DEBUG=false
```


### `4. Ejecutar Composer Install `
Ingresar al contenedor:
```bash
docker exec -it <Id del Contenedor> bash
```

Ejecutar Composer Install 
```bash
composer install
```

### `5. Validar Conexión a Base de Datos`
Verificar Parametros de Conexión cargados en el .env
```bash
php artisan tinker
config('database.connections.sqlsrv');
```
Validar conexión:
```bash
php artisan tinker
DB::connection('sqlsrv')->getPdo();
```

### 6. Limpiar temporales dentro de Laravel cuando se modifique el .env

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### `6. Asociar Certificado SSL con Caddy`
* Instalación Caddy:
  ```bash
    sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
    curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
    curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
    sudo apt update
    sudo apt install caddy
  ```
* Crear Archivo de configuración:
  Crear el archivo de configuración
  ```bash
  nano  /etc/caddy/Caddyfile
    ```
  Agregar la configuración
  ```bash
    dirislimaeste.xyz {
        reverse_proxy localhost:8080
    }
  ```
  Caddy se encargara del enrutamiento y configuración y renovación del SSL
* Reiniciar Caddy
  ```bash
    sudo systemctl reload caddy
  ```
* Consultar Sitio:  
  https://dirislimaeste.xyz

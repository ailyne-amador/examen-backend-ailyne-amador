# TodoCamisetas API

API RESTful para gestión de inventario de camisetas de fútbol. 
## Tecnologías
- PHP 8.4 + Laravel 12
- MySQL 8
- Nginx
- Docker / Docker Compose
- Swagger (l5-swagger)

## Levantar el proyecto

```bash
docker-compose up -d --build
docker exec -it todocamisetas_app php artisan migrate --seed
```

## Documentación Swagger
http://localhost:8080/api/documentation#/

## Verificación BD (DBeaver)
- Host: localhost | Puerto: 3306
- DB: todocamisetas | User: todocamisetas_user | Pass: todocamisetas_pass

---

## Arquitectura de archivos

```
app/
├── app/
│   ├── Http/Controllers/
│   │   ├── CamisetaController.php   ← CRUD camisetas + precio final
│   │   ├── ClienteController.php    ← CRUD clientes B2B
│   │   └── TallaController.php      ← CRUD tallas
│   └── Models/
│       ├── Camiseta.php             ← Relación many-to-many con Talla + lógica precio
│       ├── Cliente.php              ← Modelo cliente B2B
│       └── Talla.php                ← Relación many-to-many con Camiseta
├── database/
│   ├── migrations/                  ← Estructura de tablas
│   └── seeders/                     ← Datos iniciales (tallas, clientes, camisetas)
└── routes/
│    └── api.php                      ← Todas las rutas REST
└── storage/
│    └── api-docs-yaml                ← yaml de Swagger

nginx/default.conf                   ← Configuración Nginx → PHP-FPM
docker-compose.yml                   ← Orquestación de servicios
```

---

## Modelo de datos

```
clientes                    camisetas
--------                    ---------
id (PK)                     id (PK)
nombre_comercial            titulo
rut (unique)                club
direccion                   pais
categoria                   tipo
contacto_nombre             color
contacto_email              precio
porcentaje_oferta           precio_oferta (nullable)
                            detalles
                            codigo_producto (unique)

                    camiseta_talla (pivot)        tallas
                    --------------                ------
                    camiseta_id (FK)              id (PK)
                    talla_id (FK)                 nombre (XS/S/M/L/XL/XXL)
```

---

## Resumen de endpoints documentados

### Camisetas

| Método | Ruta | Request | Response 2xx | Errores |
|--------|------|---------|--------------|---------|
| GET | `/api/camisetas` | — | 200: array de camisetas con tallas | — |
| POST | `/api/camisetas` | JSON: `titulo*`, `club*`, `pais*`, `tipo*`, `color*`, `precio*`, `codigo_producto*`, `precio_oferta`, `detalles`, `tallas[]` | 201: camiseta creada con tallas | 422: validación |
| GET | `/api/camisetas/{id}` | Path: `id` | 200: camiseta con tallas | 404: no encontrada |
| PUT | `/api/camisetas/{id}` | Path: `id`; JSON: campos a actualizar + `tallas[]` opcional | 200: camiseta actualizada | 404, 422 |
| DELETE | `/api/camisetas/{id}` | Path: `id` | 200: `{"message": "Camiseta eliminada"}` | 404 |
| GET | `/api/camisetas/{id}/precio` | Path: `id`; Query: `cliente_id*` | 200: `{"camiseta_id", "titulo", "cliente", "precio_base", "precio_oferta", "precio_final"}` | 404: camiseta o cliente no encontrado |

### Clientes

| Método | Ruta | Request | Response 2xx | Errores |
|--------|------|---------|--------------|---------|
| GET | `/api/clientes` | — | 200: array de clientes | — |
| POST | `/api/clientes` | JSON: `nombre_comercial*`, `rut*`, `direccion*`, `categoria*`, `contacto_nombre*`, `contacto_email*`, `porcentaje_oferta` | 201: cliente creado | 422: validación |
| GET | `/api/clientes/{id}` | Path: `id` | 200: cliente | 404: no encontrado |
| PUT | `/api/clientes/{id}` | Path: `id`; JSON: campos a actualizar | 200: cliente actualizado | 404, 422 |
| DELETE | `/api/clientes/{id}` | Path: `id` | 200: `{"message": "Cliente eliminado"}` | 404 |
| GET | `/api/clientes/{id}/camisetas` | Path: `id` (cliente) | 200: array de camisetas con `precio_final` calculado según categoría del cliente | 404: cliente no encontrado |

### Tallas

| Método | Ruta | Request | Response 2xx | Errores |
|--------|------|---------|--------------|---------|
| GET | `/api/tallas` | — | 200: array de tallas | — |
| POST | `/api/tallas` | JSON: `nombre*` | 201: talla creada | 422: nombre duplicado |
| GET | `/api/tallas/{id}` | Path: `id` | 200: talla con camisetas asociadas | 404: no encontrada |
| PUT | `/api/tallas/{id}` | Path: `id`; JSON: `nombre*` | 200: talla actualizada | 404, 422 |
| DELETE | `/api/tallas/{id}` | Path: `id` | 200: `{"message": "Talla eliminada"}` | 404, 422: tiene camisetas asociadas |

`*` campo obligatorio

## Lógica de precio final

- Cliente **Preferencial** + camiseta con `precio_oferta` → devuelve `precio_oferta`
- Cliente **Regular** o sin oferta → devuelve `precio` (base)

---

## Colección Postman

Importa el archivo `TodoCamisetas.postman_collection.json` incluido en el repo.

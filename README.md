# DentariaSys — Sistema de Gestión Odontológica

**DentariaSys** es un sistema de gestión para un consultorio odontológico en
Ecuador. El módulo clínico está modelado directamente sobre el **Formulario
033 del MSP Ecuador** (SNS-MSP/HCU-form.033/2021, "Historia Clínica Única —
Odontología"), un documento médico-legal obligatorio — casi todas las
decisiones de esquema del sistema existen porque el instructivo oficial las
exige de una forma específica, a veces contraintuitiva.

## Estado actual

El sistema cubre:

* **Pacientes** — CRUD completo, cédula ecuatoriana validada con dígito
  verificador real (o pasaporte / carné de refugiado / código temporal de
  17 dígitos), desactivación en vez de borrado, búsqueda y filtrado.
* **Historias clínicas** — vigencia general (365 días), por embarazo (hasta
  la fecha probable de parto) o escolar (hasta fin de período lectivo). Un
  paciente puede tener varias historias a lo largo del tiempo.
* **Consultas** — antecedentes personales/familiares, examen del sistema
  estomatognático, constantes vitales, diagnóstico CIE-10 (subconjunto de
  26 códigos, bloque K00-K14 + Z01.2 — ver `pendientes-dentariasys.md`) y
  tratamientos.
* **Odontograma** — el módulo más complejo: catálogo de condiciones con
  reglas de índice CPO-D/ceo-d verificadas contra el instructivo oficial
  (endodoncia por realizar = Cariada, realizada = Obturada; prótesis total
  excluye terceros molares; etc.). **Inmutable por diseño**: una vez
  firmado no se puede editar ni borrar — corregir significa registrar un
  odontograma nuevo de tipo "evolutivo". Incluye Índice de Higiene Oral
  Simplificada (IHOS), enfermedad periodontal, oclusión (Angle) y
  fluorosis (Dean).
* **Agenda y citas** — desacoplada del expediente clínico (se puede agendar
  sin historia abierta), con máquina de estados explícita
  (`pendiente → confirmada → atendida`, con `cancelada`/`no_asistio`) y
  detección de solapamiento de horarios por profesional.
* **Inventario** — productos → lotes (con caducidad propia) → movimientos
  de stock, con algoritmo **FEFO** (first-expired-first-out) y bloqueo de
  filas (`lockForUpdate`) para evitar condiciones de carrera. Los insumos
  consumidos en un tratamiento descuentan stock automáticamente dentro de
  la misma transacción que crea la consulta.
* **Impresión del 033 en PDF** — exportación completa vía `laravel-dompdf`.
  El odontograma se imprime como tabla (pieza/cara/condición), no como el
  dibujo gráfico, porque dompdf maneja mal SVG complejo.
* **Roles y permisos** — `admin`, `odontologo`, `auxiliar`, `recepcion` vía
  Spatie Permission. **No hay registro público**: el personal se crea
  desde `/admin/usuarios` o `php artisan admin:crear`. Las cuentas nunca se
  borran, solo se desactivan (conservan su historial de auditoría y sus
  registros clínicos firmados).
* **Auditoría** — `owen-it/laravel-auditing` en todos los modelos clínicos
  y de inventario, incluyendo el odontograma y sus tablas relacionadas.

## Tecnologías

* **Backend**: PHP 8.2+ / **Laravel 12**
* **Frontend**: Blade + **Alpine.js** (el odontograma es una "isla" de
  Alpine, no una razón para migrar a SPA) + **Tailwind CSS**
* **Base de datos**: **PostgreSQL** (los tests corren sobre SQLite en
  memoria — todo el código evita sintaxis específica de un motor: sin
  `ILIKE`, sin `::interval`, sin `HAVING` sin `GROUP BY` real)
* **PDF**: `barryvdh/laravel-dompdf`
* **Entorno de desarrollo**: Docker Compose (PostgreSQL + pgAdmin), con PHP
  corriendo en el host vía `php artisan serve`

## Identidad visual

Verde clínico (`#3B8E87`) como color de marca, Montserrat para títulos e
Inter para el resto de la interfaz — configurado como tokens de Tailwind
(`brand`, `ink`) en `tailwind.config.js`.

## Puesta en marcha

```bash
cp .env.example .env
composer install
php artisan key:generate

docker compose up -d          # PostgreSQL + pgAdmin
php artisan migrate --seed
php artisan admin:crear        # crea el primer usuario admin

npm install && npm run build   # o npm run dev en desarrollo
php artisan serve
```

## Tests

```bash
php artisan test
```

---
*Este proyecto se desarrolla como una herramienta de aprendizaje y un demo
funcional, modelado sobre un documento médico-legal real.*
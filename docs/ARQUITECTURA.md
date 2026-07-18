# Arquitectura del sistema

SIGI utiliza una arquitectura cliente-servidor de tres capas:

1. Presentación: HTML5, Bootstrap 5 y JavaScript.
2. Lógica de negocio: PHP 8.2 con controladores, modelos, middleware y helpers.
3. Datos: SQL Server con restricciones, transacciones e índices.

## Seguridad CIA

- Confidencialidad: autenticación, RBAC, sesiones seguras y mínimo privilegio.
- Integridad: claves foráneas, restricciones, transacciones, control de concurrencia y auditoría.
- Disponibilidad: manejo de errores, índices, respaldo y arquitectura modular.

## Normalización

El modelo cumple 3FN: usuarios, roles, permisos, categorías, unidades y tipos de movimiento se almacenan en entidades separadas. Las tablas puente resuelven relaciones muchos-a-muchos y no se almacenan listas en un mismo campo.

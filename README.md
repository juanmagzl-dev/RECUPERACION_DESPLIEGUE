# Proyecto de Recuperación - Despliegue

## Descripción
Proyecto de desarrollo con estructura de ramas Git y arquitectura backend/frontend.

## Estructura del Proyecto

```
/src
  /backend    → Código PHP del servidor
  /frontend   → HTML/CSS/JS del cliente
/tests        → Pruebas PHPUnit
README.md     → Este archivo
.gitignore    → Archivos ignorados por Git
```

## Ramas del Proyecto

- **main**: Rama principal estable
- **develop**: Último desarrollo funcional
- **feature/map-generator**: Funcionalidad de generador de mapas
- **feature/save-load**: Funcionalidad de guardado y carga
- **feature/frontend-ui**: Interfaz de usuario frontend

## Instalación

```bash
git clone <repository-url>
cd recuperacion_Despliegue
```

## Desarrollo

1. Trabajar en ramas feature correspondientes
2. Hacer merge a develop para integración
3. Hacer merge a main para releases estables 
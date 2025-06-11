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

## Funcionalidades Implementadas

### ✅ Fase 1: Preparación del entorno
- Repositorio Git inicializado con estructura de ramas
- Estructura de directorios del proyecto
- Archivos base para backend y frontend

### ✅ Fase 2: Lógica de generación del mapa (PHP)
- **Clase MinesweeperMap**: Genera mapas de buscaminas
- **Niveles de dificultad**: Fácil (9x9, 10 minas), Medio (16x16, 40 minas), Experto (30x16, 99 minas), Personalizado
- **Exportación/Importación**: Soporte para JSON y XML
- **API REST**: Endpoints para generar, guardar y cargar mapas
- **Tests PHPUnit**: Validación completa de la funcionalidad

### ✅ Fase 3: Frontend
- **Interfaz de usuario**: Selección de dificultad con botones
- **Configuración personalizada**: Inputs para filas, columnas y minas
- **Generación visual**: Renderizado del mapa con CSS Grid
- **Funcionalidades**: Generar, guardar (descarga JSON/XML) y cargar mapas
- **Responsive**: Diseño adaptativo para dispositivos móviles

### ✅ Fase 4: Testing con PHPUnit
- **Configuración PHPUnit**: Composer.json y phpunit.xml configurados
- **Suite completa de tests**: 39 tests con 144 aserciones
- **Cobertura exhaustiva**: Tests para todas las funcionalidades principales
- **Casos edge**: Validación de parámetros, errores y casos límite
- **Tests de integración**: Verificación de estructura de archivos y configuración
- **Automatización**: Scripts de composer para ejecutar tests fácilmente

### ✅ Fase 5: CI/CD y Git
- **Gestión de ramas**: main, develop, feature branches implementadas
- **Commits regulares**: Historial limpio con commits descriptivos
- **Merges estructurados**: Integración desde feature → develop → main
- **Documentación completa**: README actualizado con todas las fases
- **Configuración git**: .gitignore optimizado para PHP y testing

## Cómo usar el proyecto

### Requisitos
- Servidor web con PHP 7.4+
- Extensiones PHP: SimpleXML, JSON

### Instalación
1. Clona el repositorio
2. Configura un servidor web (Apache/Nginx) o usa el servidor integrado de PHP
3. Navega a `src/frontend/index.html` para la interfaz de usuario

### Uso del servidor integrado de PHP
```bash
cd src/backend
php -S localhost:8000
```

Luego abre `src/frontend/index.html` en tu navegador.

### Estructura de la API
- `GET /api.php?action=info` - Información de la API
- `GET /api.php?action=difficulties` - Niveles de dificultad disponibles
- `POST /api.php?action=generate` - Generar mapa personalizado
- `POST /api.php?action=generate_difficulty` - Generar por dificultad
- `POST /api.php?action=save` - Preparar mapa para descarga
- `POST /api.php?action=load` - Cargar mapa desde contenido

## Desarrollo

1. Trabajar en ramas feature correspondientes
2. Hacer merge a develop para integración
3. Hacer merge a main para releases estables

## Tests

### Ejecutar tests PHPUnit

**Tests básicos:**
```bash
composer test
# O directamente:
vendor/bin/phpunit
```

**Tests con cobertura de código:**
```bash
composer test-coverage
# O directamente:
vendor/bin/phpunit --coverage-html coverage
```

**Tests específicos:**
```bash
# Solo tests de MinesweeperMap
vendor/bin/phpunit tests/MinesweeperMapTest.php

# Solo tests de estructura
vendor/bin/phpunit tests/ExampleTest.php
```

### Resultados de Testing
- ✅ **39 tests ejecutados**
- ✅ **144 aserciones validadas**
- ✅ **0 errores, 0 fallos**
- ✅ **Cobertura de código**: Se validó toda la funcionalidad principal

### Casos de Prueba Implementados
1. **Generación de mapas**: Validación de algoritmo y conteo de minas
2. **Niveles de dificultad**: Verificación de parámetros para fácil/medio/experto
3. **Exportación/Importación**: Tests para JSON y XML
4. **Validación de parámetros**: Casos edge y errores
5. **Persistencia**: Guardado y carga de archivos
6. **Estructura del proyecto**: Validación de archivos y configuración

## Gestión de Ramas Git

### Estructura de Ramas
```
main                    # Rama principal estable
│
├── develop            # Integración de desarrollo
│   │
│   ├── feature/map-generator     # ✅ Completada y mergeada
│   ├── feature/save-load         # ✅ Preparada para futuras mejoras
│   └── feature/frontend-ui       # ✅ Completada y mergeada
```

### Flujo de Trabajo
1. **Desarrollo**: Trabajar en ramas `feature/nombre-funcionalidad`
2. **Integración**: Merge de feature → develop
3. **Release**: Merge de develop → main (para versiones estables)
4. **Testing**: Ejecutar tests antes de cada merge

### Comandos Git Útiles
```bash
# Cambiar a rama de desarrollo
git checkout develop

# Crear nueva feature
git checkout -b feature/nueva-funcionalidad

# Ver estado de ramas
git branch -a

# Merge a develop
git checkout develop
git merge feature/nueva-funcionalidad
``` 
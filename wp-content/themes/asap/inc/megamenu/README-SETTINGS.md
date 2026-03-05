# 🚀 ASAP Megamenu Settings - Sistema Completo

## 📋 Descripción

El nuevo sistema de Megamenu en Settings permite crear megamenus profesionales con constructor visual drag & drop, sin depender de los menús de WordPress. Es una solución completa y escalable.

## 🎯 Características

### ✅ **Constructor Visual Completo**
- Drag & drop para reordenar columnas
- Editor visual para cada columna
- Agregar/eliminar items dinámicamente
- Upload de imágenes integrado
- Iconos SVG personalizables

### ✅ **Múltiples Layouts**
- **Grid**: Columnas iguales (2-6 columnas)
- **Featured**: 1 columna destacada + grid
- **Cards**: Con imágenes prominentes

### ✅ **Gestión Completa**
- Crear, editar, eliminar megamenus
- Vista previa en tiempo real
- Migración desde sistema anterior
- Base de datos dedicada

### ✅ **Integración Flexible**
- Shortcode: `[asap_megamenu id="1"]`
- PHP: `ASAP_Megamenu::instance()->render_megamenu_shortcode(['id' => 1])`
- Compatible con sistema anterior

## 🗂️ Estructura de Archivos

```
inc/megamenu/
├── class-asap-megamenu-settings.php    # Clase principal del sistema Settings
├── class-asap-megamenu.php             # Clase original (actualizada)
└── README-SETTINGS.md                  # Esta documentación

settings/
└── megamenu.php                        # Página de Settings

assets/
├── css/
│   └── megamenu-settings.css           # Estilos del constructor
└── js/
    └── megamenu-settings.js            # JavaScript del constructor
```

## 🗄️ Base de Datos

### Tabla: `wp_asap_megamenus`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | mediumint(9) | ID único |
| `name` | varchar(255) | Nombre del megamenu |
| `content` | longtext | JSON con contenido de columnas |
| `settings` | longtext | JSON con configuración (layout, columnas) |
| `created_at` | datetime | Fecha de creación |
| `updated_at` | datetime | Fecha de actualización |

### Estructura del JSON `content`:

```json
[
  {
    "image": "https://example.com/image.jpg",
    "icon": "<svg>...</svg>",
    "title": "Título de la columna",
    "description": "Descripción de la columna",
    "items": [
      {
        "icon": "🔍",
        "title": "Item del menú",
        "url": "https://example.com"
      }
    ],
    "cta_text": "Ver más",
    "cta_url": "https://example.com/cta"
  }
]
```

### Estructura del JSON `settings`:

```json
{
  "layout": "grid",
  "columns": 4
}
```

## 🎨 Uso del Constructor

### 1. **Acceder al Constructor**
```
Settings > ASAP > Megamenu > ➕ Crear Nuevo Megamenu
```

### 2. **Configuración Básica**
- **Nombre**: Identificador único
- **Layout**: Grid, Featured, Cards
- **Columnas**: 2-6 columnas

### 3. **Constructor Visual**
- **Agregar Columna**: Click en "➕ Agregar Columna"
- **Eliminar Columna**: Click en "🗑️" en el header de la columna
- **Reordenar**: Drag & drop en el header de la columna
- **Agregar Items**: Click en "➕ Agregar" dentro de cada columna

### 4. **Configurar Columna**
- **Imagen**: URL o upload con media picker
- **Icono**: SVG o clase de icono
- **Título**: Título de la columna
- **Descripción**: Texto descriptivo
- **Items**: Lista de enlaces del menú
- **CTA**: Botón de llamada a la acción

## 🔧 API y Métodos

### **Clase Principal: `ASAP_Megamenu_Settings`**

#### Métodos Públicos:
- `render_page()` - Renderiza la página principal
- `ajax_save_megamenu()` - Guarda megamenu via AJAX
- `ajax_load_megamenu()` - Carga megamenu via AJAX
- `ajax_delete_megamenu()` - Elimina megamenu via AJAX
- `ajax_preview_megamenu()` - Vista previa via AJAX
- `ajax_migrate_megamenus()` - Migra megamenus existentes

#### Métodos Privados:
- `get_all_megamenus()` - Obtiene todos los megamenus
- `has_legacy_megamenus()` - Verifica si hay megamenus para migrar
- `migrate_legacy_megamenus()` - Migra megamenus del sistema anterior
- `render_megamenu_preview()` - Renderiza vista previa

### **Clase Extendida: `ASAP_Megamenu`**

#### Nuevos Métodos:
- `render_megamenu_shortcode($atts)` - Shortcode handler
- `get_megamenu_by_id($id)` - Obtiene megamenu por ID
- `get_megamenu_by_name($name)` - Obtiene megamenu por nombre
- `render_megamenu_from_data($content, $settings)` - Renderiza desde datos
- `render_column_from_data($column)` - Renderiza columna individual

## 📱 Frontend

### **Shortcode**
```php
// Por ID
[asap_megamenu id="1"]

// Por nombre
[asap_megamenu name="Mi Megamenu"]
```

### **PHP**
```php
// En templates
echo ASAP_Megamenu::instance()->render_megamenu_shortcode(['id' => 1]);

// Obtener datos
$megamenu = ASAP_Megamenu::instance()->get_megamenu_by_id(1);
$content = json_decode($megamenu->content, true);
$settings = json_decode($megamenu->settings, true);
```

### **CSS Classes**
```css
.asap-megamenu-content          /* Contenedor principal */
.asap-megamenu-column          /* Columna individual */
.asap-megamenu-column-image    /* Imagen de la columna */
.asap-megamenu-column-icon     /* Icono de la columna */
.asap-megamenu-column-title    /* Título de la columna */
.asap-megamenu-column-items    /* Lista de items */
.asap-megamenu-item           /* Item individual */
.asap-megamenu-cta-button     /* Botón CTA */
```

## 🔄 Migración

### **Detección Automática**
El sistema detecta automáticamente si hay megamenus en el sistema anterior (Apariencia > Menús) y muestra un botón de migración.

### **Proceso de Migración**
1. Busca todos los menús con `_asap_megamenu_enabled = '1'`
2. Extrae el contenido y configuración
3. Crea nuevos registros en `wp_asap_megamenus`
4. Agrega "(Migrado)" al nombre para evitar conflictos

### **Compatibilidad**
- Los megamenus migrados mantienen toda su funcionalidad
- El sistema anterior sigue funcionando
- No se pierden datos en el proceso

## 🎯 Ventajas del Nuevo Sistema

### ✅ **UX Mejorada**
- **1 click** vs 7 pasos del sistema anterior
- Constructor visual dedicado
- Vista previa en tiempo real
- Gestión centralizada

### ✅ **Escalabilidad**
- Múltiples megamenus
- Base de datos optimizada
- Sistema independiente de WordPress
- Fácil importar/exportar

### ✅ **Flexibilidad**
- Shortcode en cualquier lugar
- PHP en templates
- Compatible con sistema anterior
- Migración automática

### ✅ **Profesional**
- Constructor drag & drop
- Múltiples layouts
- Media uploader integrado
- Iconos SVG personalizables

## 🚀 Próximas Mejoras

- [ ] Templates predefinidos
- [ ] Importar/Exportar configuraciones
- [ ] Animaciones personalizables
- [ ] Integración con page builders
- [ ] Analytics de uso
- [ ] A/B testing de layouts

## 🐛 Troubleshooting

### **Problema**: No se ve la tab "Megamenu"
**Solución**: Verificar que `functions.php` incluya la nueva tab en el array `$tabs`

### **Problema**: Error al crear tabla
**Solución**: Verificar permisos de base de datos y que `dbDelta` esté disponible

### **Problema**: Constructor no carga
**Solución**: Verificar que los assets CSS/JS se carguen correctamente

### **Problema**: Shortcode no funciona
**Solución**: Verificar que la clase `ASAP_Megamenu` esté inicializada

## 📞 Soporte

Para soporte técnico o reportar bugs, contactar al equipo de desarrollo.

---

**Versión**: 1.0.0  
**Última actualización**: Diciembre 2024  
**Compatibilidad**: WordPress 5.0+, PHP 7.4+





# 🚀 ASAP Megamenu - Professional Megamenu System

Sistema completo de megamenú con **3 estilos visuales** y constructor visual drag & drop.

---

## ✨ CARACTERÍSTICAS

✅ **3 Estilos Visuales** - Full Screen, Dropdown Compact, Sidebar Slide  
✅ **Constructor Visual** - Drag & drop para crear megamenus  
✅ **3 Layouts** - Grid, Featured, Cards  
✅ **Responsive** - Accordion en mobile  
✅ **Animaciones** - Smooth con CSS + Stagger effect  
✅ **Imágenes** - Soporta imágenes por columna  
✅ **Iconos** - Emojis o HTML/SVG  
✅ **CTAs** - Botones call-to-action personalizados  
✅ **Items** - Enlaces con iconos dentro de cada columna  
✅ **Accesibilidad** - ARIA, keyboard navigation

---

## 🎨 ESTILOS VISUALES DISPONIBLES

### **1. Full Screen (Profesional)** 🌐

**Ideal para:** Sitios premium, landing pages, Black Friday, promociones especiales

**Características:**
- Overlay oscuro completo que cubre toda la pantalla
- Máximo impacto visual y atención del usuario
- Perfecto para destacar productos o servicios
- Efecto WOW profesional

**Cuándo usarlo:**
- Tiendas online con muchos productos
- Sitios de servicios con múltiples categorías
- Promociones y ofertas especiales
- Cuando quieres captar toda la atención del usuario

---

### **2. Dropdown Compact (Minimalista)** 📐

**Ideal para:** Blogs, sitios corporativos, WordPress tradicional

**Características:**
- Se integra perfectamente debajo del header ASAP (60px)
- Sin overlay oscuro completo
- Dropdown limpio y profesional
- Fondo blanco con sombra elegante
- No bloquea el scroll de la página
- Borde superior de color (#667eea)

**Cuándo usarlo:**
- Blogs y sitios de contenido
- Sitios corporativos institucionales
- Cuando quieres un megamenu discreto e integrado
- Sitios con mucha navegación

---

### **3. Sidebar Slide (Moderno)** 🎯

**Ideal para:** Portfolios, tiendas, sitios modernos

**Características:**
- Panel lateral que aparece desde la derecha
- 450px de ancho
- Overlay semi-transparente (rgba(0,0,0,0.6))
- Columnas apiladas verticalmente
- Efecto "drawer" tipo app moderna
- Header con gradiente de color

**Cuándo usarlo:**
- Portfolios y sitios creativos
- E-commerce modernos
- Aplicaciones web
- Cuando quieres un diseño tipo app mobile  

---

## 📋 INSTALACIÓN

Ya está instalado! Los archivos necesarios están en:

```
inc/megamenu/
├── class-asap-megamenu.php  (Clase principal - YA REFERENCIADO en functions.php)
assets/css/
├── megamenu.css             (Frontend styles)
├── megamenu-admin.css       (Admin constructor styles)
assets/js/
├── megamenu.js              (Frontend interactions)
└── megamenu-admin.js        (Admin constructor)
```

---

## 🎯 CÓMO USAR

### **PASO 1: Activar y Elegir Estilo**

```
ASAP Options > Megamenu Settings
(https://tu-sitio.com/wp-admin/admin.php?page=asap-menu&tab=megamenu_settings)

1. ☑ Activar Megamenu
2. Elegir estilo:
   • 🌐 Full Screen - Overlay completo profesional
   • 📐 Dropdown Compact - Minimalista integrado
   • 🎯 Sidebar Slide - Panel lateral moderno
3. Click "Abrir Constructor Visual"
```

### **PASO 2: Configurar en el Menú**

```
Apariencia > Menús > [Tu menú principal]
1. Click en un ítem del menú (nivel superior)
2. Verás "🚀 Activar Megamenu Full Screen"
3. Marca el checkbox
4. Selecciona Layout: Grid / Featured / Cards
5. Selecciona columnas: 2-6
6. Click "✨ Abrir Constructor Visual"
```

### **PASO 3: Usar el Constructor Visual**

#### **A. Agregar Columnas**
```
1. Click "Agregar Columna"
2. Se crea una nueva columna vacía
3. Click en la columna para editarla
```

#### **B. Editar Columna**
En el panel derecho puedes configurar:

```
- Título: "Servicios"
- Descripción: Texto breve
- Imagen: Subir desde media library
- Icono: Emoji (🚀) o HTML (<svg>...)
- Items/Enlaces: Lista de links con iconos
- CTA: Botón call-to-action con URL
- Featured: Marcar como destacada (solo layout Featured)
```

#### **C. Agregar Items**
```
1. En el editor de columna, scroll hasta "Items / Enlaces"
2. Click "+ Agregar Item"
3. Completa:
   - Texto del enlace: "SEO"
   - URL: https://...
   - Icono (opcional): 📊
4. Puedes agregar múltiples items
5. Arrastra ⠿ para reordenar
```

#### **D. Reordenar Columnas**
```
- Arrastra el icono ⠿ en el header de cada columna
- El orden se actualiza automáticamente
```

#### **E. Guardar**
```
Click "Guardar" (botón verde arriba a la derecha)
```

---

## 🎨 LAYOUTS DISPONIBLES

### **1. Grid (Columnas Iguales)**
```css
Ideal para: Servicios, categorías, secciones
Distribución: Todas las columnas del mismo tamaño
Uso: Contenido homogéneo
```

**Ejemplo visual:**
```
┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐
│  SEO    │ │  SEM    │ │  Social │ │  Email  │
│ ─────── │ │ ─────── │ │ ─────── │ │ ─────── │
│ • On-pg │ │ • Ads   │ │ • FB    │ │ • Camp  │
│ • Off-pg│ │ • PPC   │ │ • IG    │ │ • Auto  │
│ [Ver →] │ │ [Ver →] │ │ [Ver →] │ │ [Ver →] │
└─────────┘ └─────────┘ └─────────┘ └─────────┘
```

### **2. Featured (Destacada + Grid)**
```css
Ideal para: Promociones, productos principales
Distribución: 1 columna grande + varias pequeñas
Uso: Llamar atención a algo específico
```

**Ejemplo visual:**
```
┌──────────────────┐  ┌────────┐ ┌────────┐
│   DESTACADO      │  │ Plugin │ │ Themes │
│   [Imagen]       │  │────────│ │────────│
│                  │  │ • WP   │ │ • Blog │
│ Theme PRO        │  │ • Shop │ │ • Shop │
│ El mejor theme   │  └────────┘ └────────┘
│ [Ver ahora →]    │
└──────────────────┘
```

### **3. Cards (Con Imágenes)**
```css
Ideal para: Blog, portfolio, e-commerce
Distribución: Tarjetas visuales con imágenes
Uso: Contenido visual
```

**Ejemplo visual:**
```
┌──────────┐  ┌──────────┐  ┌──────────┐
│ [Imagen] │  │ [Imagen] │  │ [Imagen] │
│──────────│  │──────────│  │──────────│
│ Guías    │  │ Cursos   │  │ eBooks   │
│ 50+ posts│  │ 12 video │  │ 8 libros │
│ [Ver →]  │  │ [Ver →]  │  │ [Ver →]  │
└──────────┘  └──────────┘  └──────────┘
```

---

## 💡 EJEMPLOS DE USO

### **Ejemplo 1: Megamenu de Servicios**

**Configuración:**
- Layout: Grid
- Columnas: 4
- Contenido: 4 columnas (SEO, SEM, Social Media, Email)

**Columna 1 - SEO:**
```
Título: SEO
Icono: 📊
Items:
  - SEO On-Page → /seo-onpage
  - SEO Off-Page → /seo-offpage
  - SEO Técnico → /seo-tecnico
CTA: "Ver todos los servicios" → /servicios
```

**Resultado:** Megamenu profesional con 4 servicios organizados

---

### **Ejemplo 2: Megamenu de Productos (Featured)**

**Configuración:**
- Layout: Featured
- Columnas: 4 (1 featured + 3 normales)

**Columna 1 (Featured):**
```
Título: ASAP Theme PRO
Descripción: El theme más completo para WordPress
Imagen: [Screenshot del theme]
Featured: ✓ (marcado)
CTA: "Comprar ahora" → /checkout
```

**Columnas 2-4:** Plugins, Themes, Services (sin featured)

**Resultado:** Producto principal destacado + 3 categorías

---

### **Ejemplo 3: Megamenu de Recursos (Cards)**

**Configuración:**
- Layout: Cards
- Columnas: 3

**Columnas:**
1. Guías - Imagen + "50+ guías gratis"
2. Cursos - Imagen + "12 cursos premium"
3. eBooks - Imagen + "8 libros descargables"

**Resultado:** Megamenu visual tipo e-commerce

---

## 🎨 PERSONALIZACIÓN CSS

### **Cambiar colores del megamenu:**

```css
/* En tu child theme o CSS adicional */

/* Overlay */
.asap-megamenu-overlay {
    background: rgba(0, 0, 0, 0.95); /* Más oscuro */
}

/* Columnas */
.asap-megamenu-column {
    background: #f9f9f9; /* Color de fondo */
}

/* CTA Button */
.asap-megamenu-cta {
    background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
}
```

### **Cambiar animaciones:**

```css
/* Cambiar duración */
.asap-megamenu {
    transition: all 0.6s ease; /* Más lento */
}

/* Cambiar stagger delay */
.asap-megamenu-column:nth-child(1) { animation-delay: 0.1s; }
.asap-megamenu-column:nth-child(2) { animation-delay: 0.2s; }
/* ... */
```

---

## 📱 RESPONSIVE

### **Desktop (>992px):**
- Full screen overlay
- Animaciones smooth
- Hover effects

### **Mobile (<992px):**
- Accordion dentro del menú hamburguesa
- Sin overlay
- Tap para expandir/contraer

---

## ⌨️ KEYBOARD NAVIGATION

- `Tab` - Navegar entre elementos
- `Enter` - Abrir megamenu
- `Escape` - Cerrar megamenu
- `Arrow keys` - Navegar items

---

## 🐛 TROUBLESHOOTING

### **Problema: El megamenu no aparece**

**Solución:**
1. Verifica que esté activado en: Personalizar > Header options
2. Verifica que el ítem del menú tenga el checkbox activado
3. Guarda el menú después de configurar

---

### **Problema: El constructor visual no se abre**

**Solución:**
1. Verifica que jQuery UI Sortable esté cargado
2. Verifica consola del navegador por errores JavaScript
3. Limpia caché del navegador

---

### **Problema: Las imágenes no se suben**

**Solución:**
1. Verifica que `wp_enqueue_media()` esté funcionando
2. Verifica permisos de uploads en WordPress
3. Intenta con una imagen más pequeña (<2MB)

---

### **Problema: El megamenu no cierra en mobile**

**Solución:**
1. El megamenu en mobile es un accordion, no un overlay
2. Tap en el item del menú para expandir/contraer
3. Si hay conflicto, revisa otros JS del theme

---

## 🚀 OPTIMIZACIÓN

### **Performance:**
- CSS y JS solo se cargan si el megamenu está activado
- Imágenes lazy load (usar plugin)
- Assets minificados en producción

### **SEO:**
- Estructura HTML semántica
- Links accesibles para crawlers
- No hay contenido oculto (accordion en mobile)

---

## 🎯 MEJORES PRÁCTICAS

✅ **DO:**
- Usa 3-6 columnas máximo (Grid)
- Imágenes optimizadas (WebP, <100KB)
- Títulos cortos y descriptivos
- 3-5 items por columna
- CTA claro y visible

❌ **DON'T:**
- No uses más de 8 columnas (muy pesado)
- No uses imágenes gigantes (>500KB)
- No llenes de texto (usar bullet points)
- No uses más de 10 items por columna
- No uses colores que no contrasten

---

## 📊 ANALYTICS

Para trackear interacciones con el megamenu:

```javascript
// En tu archivo JS personalizado
jQuery(document).on('asap_megamenu_opened', function(e, $item) {
    // Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'megamenu_opened', {
            'item_label': $item.find('> a').text()
        });
    }
});
```

---

## 🆘 SOPORTE

Si tienes problemas:

1. Revisa esta documentación
2. Revisa la consola del navegador (F12)
3. Desactiva otros plugins temporalmente
4. Verifica que WordPress y el theme estén actualizados

---

## 🎉 ¡LISTO PARA BLACK FRIDAY!

El megamenu está diseñado para:
- Destacar promociones
- Organizar muchos productos/servicios
- Mejorar conversión
- Verse SUPER profesional

**¡Úsalo sabiamente y aumenta tus ventas!** 🚀






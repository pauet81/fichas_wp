# Reglas operativas acordadas

## UX y pedagogia
1. Siempre adaptar cada ficha a la edad objetivo.
2. Evitar estructura generica repetida en todas las fichas.
3. Preguntas simples en pares para no dejar huecos visuales en grid.
4. Progresion de dificultad dentro de la ficha.
5. Priorizar legibilidad y acciones claras.

## Estructura tecnica (compatibilidad actual)
1. Campo clave: `contenido_ficha_html`.
2. Clases compatibles:
- `.ficha-educativa`
- `.bloque-ejercicio`
- `.ejercicio-fillblanks` + `.fillblank-input`
- `.ejercicio-dragdrop`
- `.ejercicio-memory`
- `.ejercicio-trazo`
- `.ejercicio-wordsearch`
- `.acciones`, `.btn-comprobar`, `.btn-descargar-pdf`, `.feedback`
3. ACF key para contenido:
- `_contenido_ficha_html = field_contenido_ficha_html`

## SEO
1. Seguir reglas de `Instrucciones especificas/prompt_meta_tags_seo_compacto.txt`.
2. Meta title objetivo: 50-60 chars.
3. Meta description objetivo: 150-160 chars con CTA y beneficio.

## Encoding
1. Evitar acentos rotos (`?`) en DB.
2. Si hay riesgo por consola, escribir en UTF-8 seguro (hex + `CONVERT(... USING utf8mb4)`).

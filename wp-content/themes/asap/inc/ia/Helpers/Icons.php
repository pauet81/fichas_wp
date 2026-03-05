<?php
/**
 * Icon Helper
 * 
 * Provee íconos SVG para la interfaz de administración de IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Helpers
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Helpers_Icons {
    
    /**
     * Ícono de lápiz/escritura
     * 
     * @return string SVG markup
     */
    public static function pen() {
        return '
<svg
  xmlns="http://www.w3.org/2000/svg"
  width="32"
  height="32"
  viewBox="0 0 24 24"
  fill="none"
  stroke="#464646"
  stroke-width="1.25"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
  <path d="M7 8h10" />
  <path d="M7 12h10" />
  <path d="M7 16h10" />
</svg>
';
    }
    
    /**
     * Ícono de configuración/ajustes
     * 
     * @return string SVG markup
     */
    public static function settings() {
        return '<!--
category: System
tags: [equalizer, sliders, controls, settings, filter]
version: "1.0"
unicode: "ea03"
-->
<svg
  xmlns="http://www.w3.org/2000/svg"
  width="32"
  height="32"
  viewBox="0 0 24 24"
  fill="none"
  stroke="#464646"
  stroke-width="1.25"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
  <path d="M6 4v4" />
  <path d="M6 12v8" />
  <path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
  <path d="M12 4v10" />
  <path d="M12 18v2" />
  <path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
  <path d="M18 4v1" />
  <path d="M18 9v11" />
</svg>
';
    }
    
    /**
     * Ícono de meta tags/SEO
     * 
     * @return string SVG markup
     */
    public static function meta() {
        return '<svg
  xmlns="http://www.w3.org/2000/svg"
  width="32"
  height="32"
  viewBox="0 0 24 24"
  fill="none"
  stroke="#464646"
  stroke-width="1.25"
  stroke-linecap="round"
  stroke-linejoin="round">
  <path d="M15 10v11l-5 -3l-5 3v-11a3 3 0 0 1 3 -3h4a3 3 0 0 1 3 3z" />
  <path d="M11 3h5a3 3 0 0 1 3 3v11" />
</svg>
';
    }
    
    /**
     * Ícono de imagen
     * 
     * @return string SVG markup
     */
    public static function image() {
        return '
<svg
  xmlns="http://www.w3.org/2000/svg"
  width="32"
  height="32"
  viewBox="0 0 24 24"
  fill="none"
  stroke="#464646"
  stroke-width="1.25"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <path d="M15 8h.01" />
  <path d="M10 21h-4a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v5" />
  <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l1 1" />
  <path d="M14 21v-4a2 2 0 1 1 4 0v4" />
  <path d="M14 19h4" />
  <path d="M21 15v6" />
</svg>
';
    }
    
    /**
     * Ícono de cola/lista
     * 
     * @return string SVG markup
     */
    public static function queue() {
        return '
<svg
  xmlns="http://www.w3.org/2000/svg"
  width="32"
  height="32"
  viewBox="0 0 24 24"
  fill="none"
  stroke="#464646"
  stroke-width="1.25"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <path d="M4 6l16 0" />
  <path d="M4 12l16 0" />
  <path d="M4 18l16 0" />
</svg>
';
    }
    
    /**
     * Ícono de dashboard/grid
     * 
     * @return string SVG markup
     */
    public static function dashboard() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#464646" stroke-width="1.25" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>';
    }
    
}




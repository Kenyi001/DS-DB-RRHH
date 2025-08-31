# Ticket: Configurar TailwindCSS y Sistema de Tokens

- **ID del Ticket:** `fase0-0007`
- **Fase:** `Sprint 0: Fundación`
- **Estado:** `Abierto`
- **Prioridad:** `Media`

---

## Descripción

Configurar TailwindCSS con tokens de diseño YPFB (azul/blanco/rojo), sistema de componentes base y build system con Vite para desarrollo y producción del frontend.

---

## Criterios de Aceptación

- [ ] TailwindCSS v3+ instalado y configurado con tokens YPFB
- [ ] `tailwind.config.js` incluye colores corporativos y tipografías
- [ ] Vite configurado para desarrollo y build de producción
- [ ] Componentes CSS base (botones, forms) implementados
- [ ] PostCSS y Autoprefixer configurados correctamente
- [ ] `npm run dev` inicia servidor con hot reload
- [ ] `npm run build` genera assets optimizados para producción
- [ ] Plugins TailwindCSS instalados: forms, typography, aspect-ratio

---

## Detalles Técnicos y Notas de Implementación

### Package.json Dependencies
```json
{
  "devDependencies": {
    "tailwindcss": "^3.4.0",
    "postcss": "^8.4.0",
    "autoprefixer": "^10.4.0",
    "vite": "^5.0.0",
    "@tailwindcss/forms": "^0.5.0",
    "@tailwindcss/typography": "^0.5.0",
    "@tailwindcss/aspect-ratio": "^0.4.0"
  },
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  }
}
```

### Configuración TailwindCSS
```js
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.{blade.php,js,ts,vue}',
    './resources/views/**/*.blade.php'
  ],
  theme: {
    extend: {
      colors: {
        ypfb: {
          blue: '#0A3E8F',
          red: '#E31B23', 
          white: '#FFFFFF'
        },
        neutral: {
          900: '#111827',
          700: '#374151',
          500: '#6B7280',
          300: '#D1D5DB',
          100: '#F3F4F6'
        }
      },
      fontFamily: {
        sans: ['Roboto', 'system-ui', 'sans-serif']
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ]
}
```

### Componentes CSS Base
```css
/* resources/css/components/buttons.css */
.btn {
  @apply inline-flex items-center justify-center rounded-lg px-4 py-2 font-medium transition-colors;
}
.btn-primary { 
  @apply bg-ypfb-blue text-white hover:bg-ypfb-blue/90 focus:ring-2 focus:ring-ypfb-blue/40;
}
.btn-danger { 
  @apply bg-ypfb-red text-white hover:bg-ypfb-red/90;
}
```

### Vite Configuration
```js
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

---

## Especificaciones Relacionadas

- `/Docs/specs/ux-design.md` - Tokens de diseño y componentes
- `/Docs/design/tokens.md` - Especificación completa de tokens

---

## Dependencias

- **Bloquea:** `fase1-0008` (UI Empleados), `fase2-0009` (UI Contratos)
- **Bloqueado por:** `fase0-0001` (Infraestructura de desarrollo)

---

## Sub-Tareas

- [ ] Instalar TailwindCSS y dependencias via npm
- [ ] Configurar tailwind.config.js con tokens YPFB
- [ ] Crear postcss.config.js con Autoprefixer
- [ ] Configurar Vite para Laravel asset compilation
- [ ] Implementar componentes CSS base (buttons, forms)
- [ ] Crear sistema de tokens en variables CSS
- [ ] Configurar hot reload para desarrollo
- [ ] Optimizar build para producción (purge CSS)
- [ ] Añadir plugins TailwindCSS necesarios
- [ ] Documentar uso de componentes y tokens

---

## Comentarios y Discusión

**Owner:** [Placeholder - Frontend Developer]
**Estimación:** 8-12 horas
**Sprint:** Sprint 0 (Semanas 1-2)

**Nota**: Coordinar con equipo de UX para validar tokens de colores y obtener logo oficial YPFB en formato SVG.
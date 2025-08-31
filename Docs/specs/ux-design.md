# Especificación UX/Design - Sistema RRHH YPFB-Andina

## Propósito
Establecer las pautas de diseño, experiencia de usuario y sistema de componentes para garantizar una interfaz coherente, accesible y alineada con la identidad visual de YPFB.

## Alcance
- Tokens de diseño (colores, tipografía, espaciado)
- Sistema de componentes atómicos
- Patrones responsive mobile-first
- Accesibilidad AA
- Guías de UX por módulo

## Tokens de Diseño

### Paleta de Colores YPFB
```css
:root {
  /* Colores corporativos */
  --color-ypfb-blue: #0A3E8F;    /* Primario - botones, headers */
  --color-ypfb-red: #E31B23;     /* Accent - alertas, CTAs críticos */
  --color-ypfb-white: #FFFFFF;   /* Fondos principales */
  
  /* Neutrales */
  --neutral-900: #111827;        /* Texto primario */
  --neutral-700: #374151;        /* Texto secundario */
  --neutral-500: #6B7280;        /* Texto auxiliar */
  --neutral-300: #D1D5DB;        /* Bordes */
  --neutral-100: #F3F4F6;        /* Fondos secundarios */
  
  /* Estados */
  --success: #16A34A;
  --warning: #F59E0B;
  --danger: #DC2626;
}
```

### Tipografía
- **Primaria**: Roboto (400, 500, 700) para interfaz general
- **Secundaria**: Merriweather para reportes e impresión
- **Tamaños**: 12px (xs), 14px (sm), 16px (md), 20px (lg), 24px (xl)
- **Line Heights**: 1.25-1.6 según contexto

### Espaciado y Grid
- **Base**: 4px (0.25rem) para spacing system
- **Container**: Centrado con padding 1rem
- **Breakpoints**: sm(640px), md(768px), lg(1024px), xl(1280px)

## Sistema de Componentes

### Atomic Design Hierarchy
- **Atoms**: Button, Input, Label, Icon, Badge
- **Molecules**: FormGroup, SearchBar, DataTableRow
- **Organisms**: DataTable, Modal, Wizard, Navigation
- **Templates**: PageLayout, FormLayout, DashboardLayout

### Componentes Críticos

#### Button Component
```css
.btn {
  @apply inline-flex items-center justify-center rounded-lg px-4 py-2 font-medium;
}
.btn-primary { 
  @apply bg-ypfb-blue text-white hover:bg-ypfb-blue/90 
         focus:outline-none focus:ring-2 focus:ring-ypfb-blue/40;
}
.btn-danger { 
  @apply bg-ypfb-red text-white hover:bg-ypfb-red/90;
}
```

#### Form Components
- **Input States**: default, focus, error, disabled
- **Validation**: Inline errors con iconos y mensajes claros
- **Required Fields**: Marcados con asterisco y aria-required

#### DataTable Component
- **Server-side Pagination**: Para listados grandes
- **Column Sorting**: Headers clickeables con indicadores visuales
- **Filters**: Drawer en móvil, sidebar en desktop
- **Export**: Botones CSV/PDF accesibles

## Estrategia Responsive

### Mobile-First Approach
- **Navegación**: Drawer colapsable en móvil, sidebar fijo en desktop
- **Acciones**: FAB (Floating Action Button) para acciones primarias en móvil
- **Contenido**: Cards/accordions en móvil, tablas en desktop
- **Forms**: Single column en móvil, multi-column en desktop

### Patrones por Página
- **Dashboard**: KPI cards responsive, gráficos que se adaptan
- **Listados**: Cards en móvil con swipe actions, tabla completa en desktop
- **Detalle**: Tabs horizontales en desktop, accordions en móvil
- **Wizards**: Steps lineales en desktop, verticales en móvil

## Accesibilidad (AA Standard)

### Requisitos Obligatorios
- **Contraste**: Mínimo 4.5:1 para texto normal, 3:1 para texto grande
- **Keyboard Navigation**: Tab order lógico, focus visible
- **Screen Readers**: ARIA labels, roles y states apropiados
- **Images**: Alt text descriptivo para todas las imágenes

### Testing de Accesibilidad
- **Automated**: axe-core integrado en tests E2E
- **Manual**: Navegación completa solo con teclado
- **Screen Reader**: Tests con NVDA/JAWS en flujos críticos

## UX por Módulo

### Dashboard
- **Carga Progresiva**: Skeleton loaders para KPIs
- **Actions**: Accesos directos a tareas frecuentes
- **Alerts**: Notificaciones de procesos pendientes

### Empleados
- **Search**: Búsqueda en tiempo real con debounce
- **Bulk Actions**: Selección múltiple para operaciones batch
- **Photo Upload**: Drag & drop con preview y validación

### Contratos
- **Wizard Flow**: Stepper con validación por paso
- **Date Validation**: Prevención de solapes en tiempo real
- **Document Preview**: Vista previa antes de guardar

### Planilla
- **Progress Tracking**: Barra de progreso para generación
- **Preview Mode**: Vista previa antes de confirmar
- **Batch Operations**: Procesamiento de múltiples empleados

## Performance UX

### Loading States
- **Skeleton Loaders**: Para tablas y cards grandes
- **Progress Indicators**: Para operaciones largas (planilla)
- **Optimistic Updates**: Para acciones inmediatas (favoritos, filtros)

### Error Handling
- **Graceful Degradation**: Funcionalidad básica aunque falle JavaScript
- **Error Boundaries**: Captura errores sin crash total
- **Retry Mechanisms**: Botones de reintentar para operaciones fallidas

## Dependencias
- TailwindCSS v3+ con plugins: forms, typography, aspect-ratio
- Alpine.js o React para interactividad
- Icons: Heroicons o Feather Icons
- Fonts: Roboto y Merriweather desde CDN

## Criterios de Aceptación
- [ ] Tokens de diseño implementados en tailwind.config.js
- [ ] Componentes atómicos documentados con ejemplos
- [ ] Responsive design validado en 5 breakpoints principales
- [ ] Contraste AA verificado con herramientas automatizadas
- [ ] Navegación por teclado funcional en todos los flujos
- [ ] Tests visuales (Playwright snapshots) para componentes críticos
- [ ] Performance: First Contentful Paint < 2s
- [ ] Accessibility score ≥ 95 en Lighthouse
- [ ] Cross-browser testing: Chrome, Firefox, Safari, Edge
- [ ] Dark mode implementado (opcional)

## Referencias al Documento Canónico
Este documento se basa en las secciones 9, 30, 31, 32, 35 y 36 del [Project Chapter](../projectChapter.md). Para ejemplos completos de componentes, configuración de Tailwind y patrones específicos, consultar el documento principal.

**Supuestos:**
- Logo oficial YPFB disponible en formato SVG
- Aprobación de paleta de colores por equipo de marca
- Acceso a herramientas de testing de accesibilidad
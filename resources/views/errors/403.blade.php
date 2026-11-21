<x-pagina-error codigo="403" titulo="Acceso no permitido" :mensaje="$exception->getMessage() ?: 'No tienes permiso para ver esta sección.'" />

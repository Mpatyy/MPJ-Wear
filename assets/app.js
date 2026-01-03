/* assets/app.js */

import './styles/app.css';
import './styles/buscador.css'; 

import React from 'react';
import { createRoot } from 'react-dom/client';
import BuscadorAvanzado from './react/BuscadorAvanzado';

/**
 * Función encargada de inicializar y renderizar el componente React
 * del buscador avanzado en el contenedor correspondiente.
 */
const inicializarBuscador = () => {
    const contenedor = document.getElementById('react-buscador-avanzado');
    
    if (contenedor) {
        try {
            const root = createRoot(contenedor);
            root.render(
                <React.StrictMode>
                    <BuscadorAvanzado />
                </React.StrictMode>
            );
        } catch (error) {
            console.error('Error al inicializar el Buscador de React:', error);
        }
    }
};

// Nos aseguramos de que el script se ejecute una vez que el DOM esté completamente cargado
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarBuscador);
} else {
    inicializarBuscador();
}
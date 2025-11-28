import './styles/app.css'; 

import React from 'react';
import { createRoot } from 'react-dom/client';
import BuscadorAvanzado from './react/BuscadorAvanzado';


const contenedorBuscador = document.getElementById('react-buscador-avanzado');

if (contenedorBuscador) {
    const root = createRoot(contenedorBuscador);
    root.render(<BuscadorAvanzado />);
}

console.log('Buscador Dinámico (React) cargado con Webpack Encore.');
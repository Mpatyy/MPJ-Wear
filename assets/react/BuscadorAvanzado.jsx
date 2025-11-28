import React, { useState, useEffect } from 'react';

// Este componente implementa el buscador dinámico y los filtros.
export default function BuscadorAvanzado() {
    // ESTADO: Almacena los valores de los 4 filtros
    const [filtros, setFiltros] = useState({
        q: '',      // Nombre
        talla: '',  // Talla
        color: '',  // Color
        precio: ''  // Precio Máximo
    });

    const [resultados, setResultados] = useState([]);
    const [cargando, setCargando] = useState(false);
    const [mostrarResultados, setMostrarResultados] = useState(false);

    // OPTIMIZACIÓN DE CARGA (Debounce)
    // Se ejecuta cada vez que cambia 'filtros'
    useEffect(() => {
        // Determinamos si hay algún filtro activo.
        // Convertimos el precio a string para la comprobación
        const precioStr = filtros.precio === 0 ? '' : String(filtros.precio);
        
        const estaActivo = (
            filtros.q.trim() !== '' ||
            filtros.talla !== '' ||
            filtros.color !== '' ||
            precioStr !== ''
        );
        
        if (!estaActivo) {
            setResultados([]);
            setMostrarResultados(false);
            return;
        }

        // Tarea: Optimización de carga dinámica (Debounce: 400ms de espera)
        const timer = setTimeout(() => {
            fetchProductos();
        }, 400);

        return () => clearTimeout(timer); // Limpieza del timer si el usuario escribe rápido
    }, [filtros]);

    // COMUNICACIÓN AJAX Y API REST
    const fetchProductos = async () => {
        setCargando(true);
        // Construimos la URL: /api/buscar-productos?q=camiseta&talla=M...
        // Nota: El endpoint es /api/buscar-productos para coincidir con el controlador.
        const queryParams = new URLSearchParams(filtros).toString();
        
        try {
            const response = await fetch(`/api/buscar-productos?${queryParams}`);
            if (response.ok) {
                const data = await response.json();
                setResultados(data);
                setMostrarResultados(true);
            } else {
                 // Manejo de errores si la respuesta HTTP no es 200 (ej. 404, 500)
                console.error(`Error ${response.status} en la API:`, await response.text());
                setResultados([]);
                setMostrarResultados(true); // Mostrar que no hay resultados por error
            }
        } catch (error) {
            console.error("Error buscando productos (fallo de red/parsing):", error);
            setResultados([]);
            setMostrarResultados(true); 
        }
        setCargando(false);
    };

    // Manejador genérico para actualizar el estado de los filtros
    const handleChange = (e) => {
        let value = e.target.value;
        const name = e.target.name;

        // Aseguramos que el precio sea un número o cadena vacía
        if (name === 'precio' && value !== '') {
            value = parseFloat(value) || ''; 
        }
        
        setFiltros(prevFiltros => ({
            ...prevFiltros,
            [name]: value
        }));
    };

    return (
        <div className="card shadow-sm p-3 mb-4 bg-body rounded">
            <h5 className="mb-3">Buscador Avanzado y Filtros</h5>
            
            {/* INPUTS DE FILTRO */}
            <div className="row g-2">
                {/* 1. Nombre (Buscador principal) */}
                <div className="col-md-5">
                    <input 
                        type="text" 
                        name="q"
                        className="form-control" 
                        placeholder="Buscar por nombre o descripción..."
                        value={filtros.q}
                        onChange={handleChange}
                    />
                </div>

                {/* 2. Talla */}
                <div className="col-md-2">
                    <select name="talla" className="form-select" value={filtros.talla} onChange={handleChange}>
                        <option value="">Talla</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        {/* Aquí puedes añadir las tallas que uses en tu BD */}
                    </select>
                </div>

                {/* 3. Color */}
                <div className="col-md-3">
                    <select name="color" className="form-select" value={filtros.color} onChange={handleChange}>
                        <option value="">Color</option>
                        <option value="Negro">Negro</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Rojo">Rojo</option>
                        <option value="Azul">Azul</option>
                        {/* Aquí puedes añadir los colores que uses en tu BD */}
                    </select>
                </div>

                {/* 4. Precio Máximo */}
                <div className="col-md-2">
                   <input 
                        type="number" 
                        name="precio"
                        className="form-control" 
                        placeholder="Precio Máx (€)"
                        value={filtros.precio}
                        onChange={handleChange}
                    />
                </div>
            </div>

            {/* INDICADOR DE CARGA Y RESULTADOS */}
            {cargando && <div className="mt-3 text-center text-primary small">Buscando y aplicando filtros...</div>}

            {/* LISTA DE RESULTADOS (Autocompletado y Filtro) */}
            {mostrarResultados && (
                <div className="mt-3 list-group results-list">
                    {resultados.length > 0 ? (
                         resultados.map((prod) => (
                            <a href={`/producto/${prod.id}`} key={prod.id} className="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div className="d-flex align-items-center">
                                    {/* Asegúrate de que '/uploads/' es la ruta correcta donde sirves tus imágenes */}
                                    <img 
                                        src={prod.imagen ? `/uploads/${prod.imagen}` : 'https://via.placeholder.com/40'} 
                                        alt={prod.nombre}
                                        style={{width: '40px', height: '40px', objectFit: 'cover', borderRadius: '4px'}}
                                        className="me-3"
                                    />
                                    <div>
                                        <h6 className="mb-0">{prod.nombre}</h6>
                                        <small className="text-muted">Talla: {prod.talla} | Color: {prod.color}</small>
                                    </div>
                                </div>
                                <span className="badge bg-dark rounded-pill">{prod.precio} €</span>
                            </a>
                        ))
                    ) : (
                         <div className="mt-0 alert alert-warning">No se encontraron productos que coincidan con los filtros.</div>
                    )}
                </div>
            )}
            
        </div>
    );
}
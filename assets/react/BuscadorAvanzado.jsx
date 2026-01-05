import React, { useState, useEffect } from 'react';

const BuscadorAvanzado = () => {
    const [nombre, setNombre] = useState('');
    const [talla, setTalla] = useState('Todas');
    const [color, setColor] = useState('Todos');
    const [precioMax, setPrecioMax] = useState('');
    
    const [sugerencias, setSugerencias] = useState([]);
    const [mostrarSugerencias, setMostrarSugerencias] = useState(false);
    
    const [filtros, setFiltros] = useState({
        tallas: ['Todas'],
        colores: ['Todos']
    });
    
    const [productos, setProductos] = useState([]);
    const [cargando, setCargando] = useState(false);
    const [sinResultados, setSinResultados] = useState(false);
    const [busquedaRealizada, setBusquedaRealizada] = useState(false);

    const cargarFiltros = async (nombreBusqueda = '') => {
        try {
            const params = new URLSearchParams();
            if (nombreBusqueda) {
                params.append('nombre', nombreBusqueda);
            }
            
            const response = await fetch(`/api/buscador/filtros?${params.toString()}`);
            const data = await response.json();
            
            console.log('Filtros cargados:', data);
            
            setFiltros({
                tallas: data.tallas || ['Todas'],
                colores: data.colores || ['Todos']
            });
            
            setTalla('Todas');
            setColor('Todos');
        } catch (error) {
            console.error('Error cargando filtros:', error);
        }
    };

    const cargarSugerencias = async (valor) => {
        if (valor.length < 1) {
            setSugerencias([]);
            setMostrarSugerencias(false);
            return;
        }

        try {
            const response = await fetch(`/api/buscador/sugerencias?q=${encodeURIComponent(valor)}`);
            const data = await response.json();
            setSugerencias(data.sugerencias || []);
            setMostrarSugerencias(true);
        } catch (error) {
            console.error('Error cargando sugerencias:', error);
        }
    };

    const handleNombreChange = (e) => {
        const valor = e.target.value;
        setNombre(valor);
        
        cargarSugerencias(valor);
        
        if (valor.length > 0) {
            cargarFiltros(valor);
        } else {
            cargarFiltros('');
        }
    };

    const seleccionarSugerencia = (sugerencia) => {
        setNombre(sugerencia);
        setMostrarSugerencias(false);
        cargarFiltros(sugerencia);
    };

    // BUSCAR PRODUCTOS
    const handleBuscar = async (e) => {
        e.preventDefault();
        console.log('🔍 INICIANDO BÚSQUEDA...');
        
        setCargando(true);
        setSinResultados(false);
        setBusquedaRealizada(true);
        
        try {
            const params = new URLSearchParams();
            if (nombre) params.append('nombre', nombre);
            if (talla && talla !== 'Todas') params.append('talla', talla);
            if (color && color !== 'Todos') params.append('color', color);
            if (precioMax) params.append('precio_max', precioMax);

            const urlBusqueda = `/api/buscador/productos?${params.toString()}`;
            console.log('📡 Llamando:', urlBusqueda);

            const response = await fetch(urlBusqueda);
            const data = await response.json();

            console.log('✅ Respuesta recibida:', data);

            if (data.productos && data.productos.length > 0) {
                console.log(`✨ ${data.productos.length} productos encontrados`);
                setProductos(data.productos);
                setSinResultados(false);
            } else {
                console.log('❌ Sin resultados');
                setProductos([]);
                setSinResultados(true);
            }
        } catch (error) {
            console.error('🚨 Error en búsqueda:', error);
            setSinResultados(true);
        } finally {
            setCargando(false);
        }
    };

    const handleLimpiar = (e) => {
        e.preventDefault();
        console.log('🗑️ Limpiando filtros');
        setNombre('');
        setTalla('Todas');
        setColor('Todos');
        setPrecioMax('');
        setProductos([]);
        setSugerencias([]);
        setMostrarSugerencias(false);
        setSinResultados(false);
        setBusquedaRealizada(false);
        cargarFiltros('');
    };

    const irAlProducto = (productoId) => {
        window.location.href = `/productos/${productoId}`;
    };

    const obtenerUrlImagen = (imagen) => {
        if (!imagen) return '/images/placeholder.png';
        
        if (!imagen.startsWith('http') && !imagen.startsWith('/')) {
            return `/images/productos/${imagen}`;
        }
        return imagen;
    };

    // ⭐ FUNCIÓN PARA CONVERTIR PRECIO A NÚMERO
    const formatearPrecio = (precio) => {
        const precioNumero = typeof precio === 'string' ? parseFloat(precio) : precio;
        return isNaN(precioNumero) ? '0.00' : precioNumero.toFixed(2);
    };

    useEffect(() => {
        cargarFiltros('');
    }, []);

    return (
        <div className="buscador-wrapper">
            {/* BUSCADOR SIEMPRE VISIBLE */}
            <div className="buscador-container">

                <form onSubmit={handleBuscar}>
                    {/* INPUT PRINCIPAL */}
                    <div className="buscador-inputs" style={{ position: 'relative', marginBottom: '15px' }}>
                        <input
                            type="text"
                            className="buscador-input"
                            placeholder="Buscar productos..."
                            value={nombre}
                            onChange={handleNombreChange}
                            onFocus={() => nombre && cargarSugerencias(nombre)}
                            autoComplete="off"
                        />

                        {/* SUGERENCIAS */}
                        {mostrarSugerencias && sugerencias.length > 0 && (
                            <div className="autocomplete-suggestions">
                                {sugerencias.map((sugerencia, index) => (
                                    <div
                                        key={index}
                                        className="autocomplete-suggestion"
                                        onClick={() => seleccionarSugerencia(sugerencia)}
                                    >
                                        {sugerencia}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* FILTROS */}
                    <div className="filtros-container">
                        <div className="filtro-grupo">
                            <label className="filtro-label">TALLA</label>
                            <select
                                className="filtro-select"
                                value={talla}
                                onChange={(e) => setTalla(e.target.value)}
                            >
                                {filtros.tallas.map((t, index) => (
                                    <option key={index} value={t}>
                                        {t}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="filtro-grupo">
                            <label className="filtro-label">COLOR</label>
                            <select
                                className="filtro-select"
                                value={color}
                                onChange={(e) => setColor(e.target.value)}
                            >
                                {filtros.colores.map((c, index) => (
                                    <option key={index} value={c}>
                                        {c}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="filtro-grupo">
                            <label className="filtro-label">PRECIO MÁXIMO</label>
                            <input
                                type="number"
                                className="filtro-input"
                                placeholder="Sin límite"
                                value={precioMax}
                                onChange={(e) => setPrecioMax(e.target.value)}
                                step="0.01"
                                min="0"
                            />
                        </div>
                    </div>

                    {/* BOTONES */}
                    <div className="botones-container">
                        <button type="submit" className="btn-buscar" disabled={cargando}>
                            {cargando ? '⏳ Buscando...' : '🔍 BUSCAR'}
                        </button>
                        <button type="button" className="btn-limpiar" onClick={handleLimpiar}>
                            🗑️ LIMPIAR
                        </button>
                    </div>
                </form>
            </div>

            {/* RESULTADOS */}
            <div className="resultados-section">
                {cargando && (
                    <div className="cargando">
                        ⏳ Cargando productos...
                    </div>
                )}

                {sinResultados && !cargando && busquedaRealizada && (
                    <div className="sin-resultados">
                        😕 No se encontraron productos que coincidan con tu búsqueda.
                    </div>
                )}

                {productos.length > 0 && !cargando && (
                    <div className="resultados-container">
                        <h3 className="resultados-titulo">
                            {productos.length} producto{productos.length !== 1 ? 's' : ''} encontrado{productos.length !== 1 ? 's' : ''}
                        </h3>

                        <div className="productos-grid">
                            {productos.map((producto) => (
                                <div key={producto.id} className="producto-card">
                                    <div className="producto-imagen">
                                        {producto.variaciones && producto.variaciones.length > 0 && producto.variaciones[0].imagen ? (
                                            <img 
                                                src={obtenerUrlImagen(producto.variaciones[0].imagen)}
                                                alt={producto.nombre}
                                                onError={(e) => {
                                                    e.target.src = '/images/placeholder.png';
                                                }}
                                            />
                                        ) : (
                                            <img 
                                                src={obtenerUrlImagen(producto.imagen)}
                                                alt={producto.nombre}
                                                onError={(e) => {
                                                    e.target.src = '/images/placeholder.png';
                                                }}
                                            />
                                        )}
                                    </div>

                                    <div className="producto-info">
                                        <h4 className="producto-nombre">{producto.nombre}</h4>
                                        <p className="producto-descripcion">{producto.descripcion}</p>
                                        
                                        {/* ⭐ PRECIO CORREGIDO */}
                                        <div className="producto-precio">
                                            {formatearPrecio(producto.precio)}€
                                        </div>

                                        {producto.variaciones && producto.variaciones.length > 0 ? (
                                            <div className="producto-stock stock-disponible">
                                                ✓ Disponible
                                            </div>
                                        ) : (
                                            <div className="producto-stock stock-agotado">
                                                ✗ Agotado
                                            </div>
                                        )}

                                        <button
                                            type="button"
                                            className="btn-ver-producto"
                                            onClick={() => irAlProducto(producto.id)}
                                        >
                                            Ver producto →
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default BuscadorAvanzado;

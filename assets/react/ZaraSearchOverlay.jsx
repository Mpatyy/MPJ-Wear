import React, { useEffect, useMemo, useRef, useState } from "react";

function useDebounce(value, ms) {
  const [v, setV] = useState(value);
  useEffect(() => {
    const t = setTimeout(() => setV(value), ms);
    return () => clearTimeout(t);
  }, [value, ms]);
  return v;
}

export default function ZaraSearchOverlay({ abierto, onCerrar, onIrAProducto }) {
  const [q, setQ] = useState("");
  const [cargando, setCargando] = useState(false);
  const [resultados, setResultados] = useState([]);
  const [activo, setActivo] = useState(-1);

  const inputRef = useRef(null);
  const debounced = useDebounce(q.trim(), 250);

  const recientesKey = "mpj_recientes";
  const recientes = useMemo(() => {
    try { return JSON.parse(localStorage.getItem(recientesKey) || "[]"); }
    catch { return []; }
  }, [abierto]); // recalcula al abrir

  const guardarReciente = (texto) => {
    const limpio = texto.trim();
    if (!limpio) return;
    const base = recientes.filter(x => x !== limpio);
    const nuevo = [limpio, ...base].slice(0, 6);
    localStorage.setItem(recientesKey, JSON.stringify(nuevo));
  };

  useEffect(() => {
    if (!abierto) return;
    setTimeout(() => inputRef.current?.focus(), 50);

    const onKey = (e) => {
      if (e.key === "Escape") onCerrar();
      if (e.key === "ArrowDown") {
        e.preventDefault();
        setActivo((a) => Math.min(a + 1, resultados.length - 1));
      }
      if (e.key === "ArrowUp") {
        e.preventDefault();
        setActivo((a) => Math.max(a - 1, -1));
      }
      if (e.key === "Enter") {
        if (activo >= 0 && resultados[activo]) {
          const r = resultados[activo];
          guardarReciente(q);
          onIrAProducto(r);
        }
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [abierto, resultados, activo, q, onCerrar, onIrAProducto]);

  useEffect(() => {
    if (!abierto) return;
    setActivo(-1);

    if (!debounced) {
      setResultados([]);
      return;
    }

    let cancelado = false;
    (async () => {
      setCargando(true);
      try {
        const resp = await fetch(`/api/buscar?q=${encodeURIComponent(debounced)}`, {
          headers: { "Accept": "application/json" }
        });
        if (!resp.ok) throw new Error("Error de búsqueda");
        const data = await resp.json();
        if (!cancelado) setResultados(data.resultados || []);
      } catch {
        if (!cancelado) setResultados([]);
      } finally {
        if (!cancelado) setCargando(false);
      }
    })();

    return () => { cancelado = true; };
  }, [debounced, abierto]);

  if (!abierto) return null;

  return (
    <div className="zara-overlay" role="dialog" aria-modal="true" aria-label="Buscador">
      <div className="zara-top">
        <div className="zara-top-inner">
          <input
            ref={inputRef}
            className="zara-input"
            placeholder="BUSCAR"
            value={q}
            onChange={(e) => setQ(e.target.value)}
          />
          <button className="zara-cerrar" onClick={onCerrar} aria-label="Cerrar">✕</button>
        </div>
      </div>

      <div className="zara-contenido">
        {!q.trim() && (
          <div className="zara-bloque">
            <div className="zara-titulo">Búsquedas recientes</div>
            {recientes.length === 0 ? (
              <div className="zara-vacio">Aún no hay búsquedas.</div>
            ) : (
              <div className="zara-recientes">
                {recientes.map((x) => (
                  <button
                    key={x}
                    className="zara-chip"
                    onClick={() => setQ(x)}
                    type="button"
                  >
                    {x}
                  </button>
                ))}
              </div>
            )}
          </div>
        )}

        {q.trim() && (
          <div className="zara-bloque">
            <div className="zara-titulo">
              Resultados {cargando ? "(buscando…)" : ""}
            </div>

            {(!cargando && resultados.length === 0) ? (
              <div className="zara-vacio">No hay resultados.</div>
            ) : (
              <ul className="zara-lista">
                {resultados.map((r, idx) => (
                  <li key={r.id}>
                    <button
                      className={"zara-item " + (idx === activo ? "activo" : "")}
                      onMouseEnter={() => setActivo(idx)}
                      onClick={() => { guardarReciente(q); onIrAProducto(r); }}
                      type="button"
                    >
                      <div className="zara-item-izq">
                        {r.imagen ? (
                          <img className="zara-thumb" src={r.imagen} alt="" />
                        ) : (
                          <div className="zara-thumb zara-thumb-vacio" />
                        )}
                      </div>
                      <div className="zara-item-der">
                        <div className="zara-nombre">{r.nombre}</div>
                        <div className="zara-precio">{r.precio}</div>
                      </div>
                    </button>
                  </li>
                ))}
              </ul>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
import React, { useEffect, useState } from "react";
import { createRoot } from "react-dom/client";
import ZaraSearchOverlay from "./ZaraSearchOverlay";

function ZaraSearchApp() {
  const [abierto, setAbierto] = useState(false);

  useEffect(() => {
    const btn = document.getElementById("btn-abrir-buscador");
    if (!btn) return;

    const abrir = () => setAbierto(true);
    btn.addEventListener("click", abrir);
    return () => btn.removeEventListener("click", abrir);
  }, []);

  const onIrAProducto = (r) => {
    // r.url viene del backend
    window.location.href = r.url;
  };

  return (
    <ZaraSearchOverlay
      abierto={abierto}
      onCerrar={() => setAbierto(false)}
      onIrAProducto={onIrAProducto}
    />
  );
}

export function montarZaraSearch() {
  const cont = document.getElementById("react-buscador-avanzado");
  if (!cont) return;
  createRoot(cont).render(<ZaraSearchApp />);
}

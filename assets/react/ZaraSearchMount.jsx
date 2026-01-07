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
    // ✅ si backend manda url, la usamos; si no, fallback por id
    if (r?.url) {
      window.location.href = r.url;
      return;
    }
    if (r?.id) {
      window.location.href = `/productos/${r.id}`;
    }
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

/* assets/app.js */

import './styles/app.css';
import './styles/buscador.css';
import './styles/zaraSearch.css';

import { montarZaraSearch } from './react/ZaraSearchMount';

console.log("✅ app.js cargado");

document.addEventListener('DOMContentLoaded', () => {
  montarZaraSearch();
  console.log("✅ ZaraSearch montado");
});

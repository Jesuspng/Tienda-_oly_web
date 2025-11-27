let tamActual = 16;
function cambiarTamano(delta) {
    tamActual += delta;
    if (tamActual < 10) tamActual = 10; 
    document.documentElement.style.fontSize = tamActual + "px";
}

function toggleContraste() {
    document.body.classList.toggle("alto-contraste");
}

const estiloContraste = document.createElement("style");
estiloContraste.innerHTML = `
  .alto-contraste {
    background-color: #000 !important;
    color: #fff !important;
  }
  .alto-contraste a { color: #0ff !important; }
  .alto-contraste .navbar, 
  .alto-contraste footer { background-color:#111 !important; }

  .no-leer { speak: none; }
`;
document.head.appendChild(estiloContraste);

let leyendo = false;
let utterance = null;

function leerPagina() {
    if (leyendo) {
        speechSynthesis.cancel();
        leyendo = false;
        return;
    }

    // Clonamos el body para poder eliminar los elementos no deseados
    const cuerpo = document.body.cloneNode(true);
    cuerpo.querySelectorAll(".no-leer").forEach(el => el.remove());

    // Tomamos solo el texto visible, sin duplicados
    const texto = cuerpo.innerText.trim();

    utterance = new SpeechSynthesisUtterance(texto);
    utterance.lang = "es-ES";

    utterance.onend = () => { leyendo = false; };

    leyendo = true;
    speechSynthesis.speak(utterance);
}

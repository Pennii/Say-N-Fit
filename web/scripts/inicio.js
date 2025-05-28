const bienvenido = document.getElementById("bienvenido");
const rutinas = document.getElementById("rutinas");
const nombreUsuario = document.getElementById("imagenUsuario").dataset.alias;

// Muestra las rutinas del usuario al cargar la página
window.addEventListener('load', () => {
    const numeroDia = new Date().getDay();
    fetch('https://localhost:8080/controladores/inicio_proceso.php', {
        method: 'POST',
        body: JSON.stringify({
            dia: numeroDia,
            nombreUsuario
        })
    }).then(response => response.json()).then(data => {
        if (data.rutinas != false) {
            for (const rutina of data.rutinas) {
                rutinas.innerHTML += `<div class='card'>
                                <div class='card-body'>
                                    <h2 class='card-title'>${rutina.nombre}</h2>
                                    <a class='card-text' id="${rutina.codigo}">Ver rutina</a>
                                </div>
                            </div>`;
            }
        }
        for (const rutina of rutinas.querySelectorAll("a")) {
            rutina.addEventListener("click", () => {
                window.location.href = "./rutinas.html";
                document.cookie = `codigoRutina=${rutina.id}; max-age=5; path=/`;
            });

        }
    });
});
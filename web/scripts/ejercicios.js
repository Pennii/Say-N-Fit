//Filtros
const principiante = document.getElementById("principiante");
const intermedio = document.getElementById("intermedio");
const avanzado = document.getElementById("avanzado");
const filtrar = document.getElementById("filtrar");

//Listado
const listado = document.getElementById("listado");
let ejerciciosRutina = [];
const completarRutina = document.getElementById("completarRutina");

//Actualiza los ejercicios que se ven en pantalla segun los filtros
filtrar.addEventListener("click", () => {
    let datos = {
        principiante: principiante.checked ? "principiante" : null,
        intermedio: intermedio.checked ? "intermedio" : null,
        avanzado: avanzado.checked ? "avanzado" : null,
    };
    let ejercicios;

    fetch("https://localhost:8080/controladores/ejercicio_proceso.php", {
        method: "POST",
        body: JSON.stringify(datos),
    })
        .then((response) => response.json())
        .then((data) => {
            ejercicios = data.ejercicios;
            listado.innerHTML = "";
            cargarEjercicios(ejercicios);
        });
});

//Carga todos los ejercicios en pantalla sin filtrar
window.addEventListener("load", () => {
    let ejercicios;
    completarRutina.style.display = "none";
    fetch("https://localhost:8080/controladores/ejercicio_proceso.php", {
        method: "POST",
    })
        .then((response) => response.json())
        .then((data) => {
            ejercicios = data.ejercicios;
            cargarEjercicios(ejercicios);
            const tarjetasEjerciciso = document.getElementsByClassName("ejercicio");
            for (const ejercicio of tarjetasEjerciciso) {
                ejercicio.addEventListener("mouseover", () => {
                    ejercicio.querySelector("video").play()
                })
                ejercicio.addEventListener("mouseout", () => {
                    ejercicio.querySelector("video").pause();
                })
            }
        });
});

function cargarEjercicios(ejercicios) {
    for (const ejercicio of ejercicios) {
        let ejercicioListado = `<div class="ejercicio">
          <video src="https://localhost:8080/imagenes/ejercicios/${ejercicio["nombre"]}.mp4" preload="metadata" ></video>
          <p>${ejercicio["nombre"]}</p>
          <p>${ejercicio["descripcion"]}</p>
          <p>Nivel: ${ejercicio["nivel"]}</p>
          <p>Musculos: ${ejercicio["musculos"]}</p>`;
        if (ejerciciosRutina.includes(ejercicio["nombre"])) {
            ejercicioListado += `<input type="checkbox" hidden id="${ejercicio["nombre"]}" value="${ejercicio.nombre}" class="seleccionEjercicio" checked>
                    <label for="${ejercicio["nombre"]}">Eliminar</label>
                    </div> `;
        } else {
            ejercicioListado += `<input type="checkbox" hidden id="${ejercicio["nombre"]}" value="${ejercicio.nombre}" class="seleccionEjercicio">
                    <label for="${ejercicio["nombre"]}" class="btn">Añadir</label>
                    </div> `;
        }
        listado.innerHTML += ejercicioListado;
    }
    const seleccionEjercicio =
        document.getElementsByClassName("seleccionEjercicio");
    for (const seleccion of seleccionEjercicio) {
        //Agrega y elimina elementos a un array para cargarlo luego en una rutina
        seleccion.addEventListener("change", () => {
            let opcion = seleccion.parentNode.querySelector("label");
            if (seleccion.checked) {
                ejerciciosRutina.push(seleccion.value);
                opcion.innerText = "Eliminar";
            } else {
                //Si el array contiene el ejercicio y se cancela su seleccion se elimina del array
                if (ejerciciosRutina.includes(seleccion.value)) {
                    let posicion = ejerciciosRutina.indexOf(seleccion.value);
                    ejerciciosRutina.splice(posicion, 1);
                    opcion.innerText = "Agregar";
                }
            }
            if (ejerciciosRutina.length > 0) {
                completarRutina.style.display = "block";
            } else {
                completarRutina.style.display = "none";
            }
        });
    }
}

completarRutina.addEventListener("submit", () => {
    event.preventDefault();
    let salida = "";
    for (const ejercicio of ejerciciosRutina) {
        salida += `${ejercicio};`;
    }
    salida = salida.substring(0, salida.length - 1);
    completarRutina.ejercicios.value = salida;

    const dias = completarRutina.querySelectorAll('input[type="checkbox"]');
    let valido = false;
    for (const dia of dias) {
        if (dia.checked) {
            valido = true;
        }
    }
    if (!valido) {
        alert("Debe seleccionar al menos un dia");
    } else {
        const datos = new FormData(completarRutina);
        const nombreUsuario = document.getElementById("imagenUsuario").dataset.alias;
        datos.append("nombreUsuario", nombreUsuario);
        fetch("https://localhost:8080/controladores/rutina_proceso.php", {
            method: "POST",
            body: datos,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.estado == "ok") {
                    alert("Rutina creada");
                    window.location.href = "./rutinas.html";
                } else {
                    alert("Error al crear la rutina");
                }
            });
    }
});
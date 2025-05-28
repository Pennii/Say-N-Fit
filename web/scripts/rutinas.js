
//Listas de la pagina
const listaRutinas = document.getElementById("listaRutinas");
const rutinas = document.getElementById("rutinas");
const listaEjercicios = document.getElementById("ejercicios");

const nombreUsuario = document.getElementById("imagenUsuario").dataset.alias;

// Carga las rutinas del usuario al cargar la página
window.addEventListener("load", () => {
    if (document.cookie.includes("editarRutina")) {
        document.cookie = "editarRutina=; max-age=0; path=/";
    }
    fetch("https://localhost:8080/controladores/rutina_proceso.php", {
        method: "POST",
        body: JSON.stringify({ nombreUsuario })
    })
        .then((response) => response.json())
        .then((data) => {
            let dias;

            for (const rutina of data.rutinas) {
                dias = "";
                for (const dia of rutina.dias.split(";")) {
                    dias += `${obtenerDia(dia)}, `;
                }
                dias = dias.substring(0, dias.length - 2);
                rutinas.innerHTML += `
                    <div class="rutina" id="${rutina.codigo}">
                        <p>${rutina.nombre}</p>
                        <p>${dias}</p>
                        <i class="bi bi-pencil-fill editar"></i>
                        <i class="bi bi-trash-fill eliminar"></i>
                    </div>
                    `;
            }
            const contenedoresRutina =
                document.getElementsByClassName("rutina");
            //Cambia el color de fondo al hacer click en una rutina
            for (const rutina of contenedoresRutina) {
                rutina.addEventListener("click", () => {
                    const fondo = "#dada09cb";
                    const secundario = "#910606";
                    const textoClaro = "#fff7b3";
                    obtenerEjercicios(rutina.id);
                    for (const rutina of contenedoresRutina) {
                        rutina.style.backgroundColor = fondo;
                        rutina.style.color = "";
                    }
                    rutina.style.backgroundColor = secundario;
                    rutina.style.color = textoClaro;
                });
            }

            const botonesEditar = document.getElementsByClassName("editar");
            for (const boton of botonesEditar) {
                boton.addEventListener("click", () => {
                    document.cookie = `editarRutina=${boton.parentElement.id}; path=/`;
                    window.location.href = "./editar_rutina.html";
                });
            }

            const botonesEliminar = document.getElementsByClassName("eliminar");
            for (const boton of botonesEliminar) {
                boton.addEventListener("click", (event) => {
                    event.stopPropagation();
                    let datos = {
                        nombreUsuario,
                        eliminar: boton.parentElement.id,
                    };

                    fetch("https://localhost:8080/controladores/rutina_proceso.php", {
                        method: "POST",
                        body: JSON.stringify(datos),
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            console.log(data);
                            if (data.eliminado) {
                                boton.parentElement.remove();
                                alert("Rutina eliminada");
                                listaEjercicios.innerHTML = "";
                            } else {
                                alert("Error al eliminar la rutina");
                            }
                        });
                });
            }
        })
        .then(() => {
            if (document.cookie.includes("codigoRutina")) {
                let cookies = document.cookie.split("; ");
                for (const cookie of cookies) {
                    let [nombre, valor] = cookie.split("=");
                    if (nombre == "codigoRutina") {
                        obtenerEjercicios(decodeURIComponent(valor));
                    }
                }
            }
        });
});

// Obtiene los ejercicios de la rutina seleccionada
function obtenerEjercicios(rutina) {
    const datos = {
        nombreUsuario,
        rutina
    };
    fetch("https://localhost:8080/controladores/rutina_proceso.php", {
        method: "POST",
        body: JSON.stringify(datos)
    })
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
            listaEjercicios.innerHTML = "";
            for (const ejercicios of data.ejercicios) {
                listaEjercicios.innerHTML += `
                            <div class="ejercicio">
                              <p>${ejercicios.NOMBRE}</p>
                              <p>series: ${ejercicios.SERIES} x repetciciones: ${ejercicios.REPETICIONES}</p>
                           </div>`;
            }
        });
}

// Obtiene el nombre del día de la semana a partir de su número
function obtenerDia(dia) {
    switch (dia) {
        case "0":
            numero = "domingo";
            break;
        case "1":
            numero = "lunes";
            break;
        case "2":
            numero = "martes";
            break;
        case "3":
            numero = "miercoles";
            break;
        case "4":
            numero = "jueves";
            break;
        case "5":
            numero = "viernes";
            break;
        default:
            numero = "sabado";
            break;
    }
    return numero;
}

const editarRutina = document.getElementById("editarRutina");
const nombreUsuario = document.getElementById("imagenUsuario").dataset.alias;

window.addEventListener("load", () => {
    let codigoRutina;
    let cookiesBuscar = document.cookie.split("; ");
    for (const cookie of cookiesBuscar) {
        let [nombre, valor] = cookie.split("=");
        if (nombre == "editarRutina") {
            codigoRutina = decodeURIComponent(valor);
        }
    }

    fetch("https://localhost:8080/controladores/rutina_proceso.php", {
        method: "POST",
        body: JSON.stringify({ nombreUsuario, editarRutina: codigoRutina, verRutina: true }),
    })
        .then((response) => response.json())
        .then((data) => {
            const rutina = data.datosRutina;
            const ejercicios = data.datosEjercicios;
            editarRutina.innerHTML = `
                    <div class="row">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" name="nombre" id="nombre" value="${rutina.nombre}" class="form-control">
</div>
<label class="form-label">Días:</label><br>
<div class="row">
    <div class="col-6 dia">
        <label for="domingo" class="form-label">Domingo</label>
        <input type="checkbox" name="dias[]" value="domingo" id="domingo" class="form-check-input">
    </div>
    <div class="col-6 dia">
        <label for="lunes" class="form-label">Lunes</label>
        <input type="checkbox" name="dias[]" value="lunes" id="lunes" class="form-check-input">
    </div>
</div>
<div class="row">
    <div class="col-6 dia">
        <label for="martes" class="form-label">Martes</label>
        <input type="checkbox" name="dias[]" value="martes" id="martes" class="form-check-input">
    </div>
    <div class="col-6 dia">
        <label for="miercoles" class="form-label">Miércoles</label>
        <input type="checkbox" name="dias[]" value="miercoles" class="form-check-input" id="miercoles">
    </div>
</div>
<div class="row">
    <div class="col-6 dia">
        <label for="jueves" class="form-label">Jueves</label>
        <input type="checkbox" name="dias[]" value="jueves" id="jueves" class="form-check-input">
    </div>
    <div class="col-6 dia">
        <label for="viernes" class="form-label">Viernes</label>
        <input type="checkbox" name="dias[]" value="viernes" id="viernes" class="form-check-input">
    </div>
</div>
<div class="row">
    <div class="col-6 dia">
        <label for="sabado" class="form-label">Sábado</label>
        <input type="checkbox" name="dias[]" value="sabado" id="sabado" class="form-check-input">
    </div>
</div>
                    `;
            let contadorEjercicio = 0;
            for (const ejercicio of ejercicios) {
                editarRutina.innerHTML += `
                    <div class="ejercicio">
                            <label class="form-label">${ejercicio.NOMBRE
                    }:</label>
                            <input type="text" name="ejerciciosActualizar[${contadorEjercicio}][nombre]" value="${ejercicio.NOMBRE
                    }" hidden class="form-control">
                            <input class="form-control" type="number" min=0 name="ejerciciosActualizar[${contadorEjercicio}][series]" value="${ejercicio.SERIES
                    }">
                            <label class="form-label">X</label>
                            <input class="form-control" type="number" min=0 name="ejerciciosActualizar[${contadorEjercicio++}][repeticiones]" value="${ejercicio.REPETICIONES
                    }">
                            <button type="button" class="eliminar btn" value="${ejercicio.NOMBRE
                    }"><i class="bi bi-trash-fill"></i></button>
                    </div>`;
            }
            editarRutina.innerHTML += `<button name="actualizarRutina" class="btn">Guardar</button>`;
            const botonesEliminar = document.getElementsByClassName("eliminar");
            for (const boton of botonesEliminar) {
                boton.addEventListener("click", () => {
                    let datos = {
                        ejercicio: boton.value,
                        nombreUsuario,
                        editarRutina: codigoRutina
                    };
                    fetch("https://localhost:8080/controladores/rutina_proceso.php", {
                        method: "POST",
                        body: JSON.stringify(datos),
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.eliminado == -1) {
                                window.location.href = "../vistas/rutinas.html";
                            } else if (data.eliminado == 1) {
                                boton.parentElement.remove();
                            }
                        });
                });
            }

            editarRutina.addEventListener("submit", () => {
                event.preventDefault();

                const dias = editarRutina.querySelectorAll(
                    'input[type="checkbox"]'
                );
                let valido = false;
                for (const dia of dias) {
                    if (dia.checked) {
                        valido = true;
                    }
                }
                if (!valido) {
                    alert("Debe seleccionar al menos un dia");
                } else {
                    const datos = new FormData(editarRutina);
                    datos.append("actualizarRutina", "1");
                    datos.append("editarRutina", codigoRutina);
                    datos.append("usuario", nombreUsuario);
                    fetch("https://localhost:8080/controladores/rutina_proceso.php", {
                        method: "POST",
                        body: datos,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.actualizado) {
                                alert("Rutina actualizada");
                                window.location.href = "../vistas/rutinas.html";
                            } else {
                                alert("Error al actualizar la rutina");
                            }
                        });
                }
            });
        });
});

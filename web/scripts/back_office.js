
const listaEjercicios = document.getElementById("listaEjercicios");
const datosEjercicios = document.getElementById("datosEjercicios");
const titulo = document.getElementById("titulo");
const formularioEdicion = document.getElementById("formularioEdicion");
const cancelar = document.getElementById("cancelar");

let nombreEjercicio;

window.addEventListener("load", () => {
    formularioEdicion.editar.style.display = "none";
    cancelar.style.display = 'none';
    titulo.innerText = "Creacion de ejercicio";
    let ejercicios;
    fetch("https://localhost:8080/controladores/ejercicio_proceso.php", {
        method: "POST",
    })
        .then((response) => response.json())
        .then((data) => {
            ejercicios = data.ejercicios;
            listarEjercicios(ejercicios);
        });
});

// Carga los ejercicios en el listado de ejercicios
function listarEjercicios(ejercicios) {
    console.log(ejercicios)
    let salida = "<ul>"
    for (const ejercicio of ejercicios) {
        salida += `<li id=${ejercicio.nombre.replace(/ /g, "_")}>
        <p>${ejercicio.nombre}</p>
        <i class="bi bi-pencil-fill editar"></i>
        <i class="bi bi-trash-fill eliminar"></i>
        </li>
        `
    }
    salida += '</ul>';
    listaEjercicios.innerHTML = salida;

    const botonesEditar = document.getElementsByClassName("editar");
    for (const boton of botonesEditar) {
        boton.addEventListener("click", () => {
            let ejercicio = boton.parentElement.id;
            mostrarDatos(ejercicio);
        });
    }
    const botonesEliminar = document.getElementsByClassName("eliminar");
    for (const boton of botonesEliminar) {
        boton.addEventListener("click", () => {
            let ejercicio = boton.parentElement.id;
            eliminarEjercicio(ejercicio);
        });
    }
}

// Elimina un ejercicio de la base de datos
function eliminarEjercicio(ejercicio) {
    ejercicio = ejercicio.replace(/_/g, " ");
    fetch("https://localhost:8080/controladores/back_office.php", {
        method: "POST",
        body: JSON.stringify({ eliminarEjercicio: ejercicio })
    }).then(response => response.json())
        .then(data => {
            if (data) {
                alert("Ejercicio eliminado correctamente");
                window.location.reload();
            } else {
                alert("Error al eliminar ejercicio");
            }
        });
}

// Muestra los datos de un ejercicio en el formulario de edición
function mostrarDatos(ejercicio) {
    ejercicio = ejercicio.replace(/_/g, " ");
    nombreEjercicio = ejercicio;
    fetch("https://localhost:8080/controladores/back_office.php", {
        method: "POST",
        body: JSON.stringify({ buscarEjercicio: ejercicio })
    }).then(response => response.json())
        .then(data => {
            titulo.innerText = "Edicion de ejercicio";
            formularioEdicion.guardar.style.display = 'none';
            formularioEdicion.editar.style.display = '';
            cancelar.style.display = '';

            formularioEdicion.nombre.value = data.nombre;
            formularioEdicion.descripcion.value = data.descripcion;
            formularioEdicion.nivel.value = data.nivel;
            formularioEdicion.musculos.value = data.musculos;
        });
}

// Evento para guardar un nuevo ejercicio
formularioEdicion.guardar.addEventListener("click", () => {
    event.preventDefault();
    let invalido = false;
    const datosFormulario = {
        nombre: formularioEdicion.nombre.value,
        nivel: formularioEdicion.nivel.value,
        descripcion: formularioEdicion.descripcion.value,
        musculos: formularioEdicion.musculos.value,
    }

    if (datosFormulario.nombre.trim() == '' || datosFormulario.descripcion.trim() == '') {
        valido = true;
    }

    if (!invalido) {
        fetch("https://localhost:8080/controladores/back_office.php", {
            method: "POST",
            body: JSON.stringify({ guardarEjercicio: datosFormulario })
        }).then(response => response.json())
            .then(data => {
                if (data) {
                    alert("Ejercicio creado correctamente");
                    window.location.reload();
                } else {
                    alert("Error al crear ejercicio");
                }
            });
    } else {
        alert("Error al enviar el formulario, el nombre y la descripcion del ejercicio no pueden estar vacios")
    }

});

// Evento para editar un ejercicio existente
formularioEdicion.editar.addEventListener('click', () => {
    event.preventDefault();
    let invalido = false;
    const datosFormulario = {
        nombre: formularioEdicion.nombre.value,
        nivel: formularioEdicion.nivel.value,
        descripcion: formularioEdicion.descripcion.value,
        musculos: formularioEdicion.musculos.value,
    }

    if (datosFormulario.nombre.trim() == '' || datosFormulario.descripcion.trim() == '') {
        valido = true;
    }


    if (!invalido) {
        fetch("https://localhost:8080/controladores/back_office.php", {
            method: "POST",
            body: JSON.stringify({ editarEjercicio: datosFormulario, nombreEjercicio })
        }).then(response => response.json())
            .then(data => {
                if (data) {
                    alert("Ejercicio actualizado correctamente");
                    window.location.reload();
                } else {
                    alert("Error al actualizar ejercicio");
                }
            });
    } else {
        alert("Error al enviar el formulario, el nombre y la descripcion del ejercicio no pueden estar vacios")
    }
});

// Evento para cancelar la edición y volver al listado
cancelar.addEventListener('click', () => {
    window.location.reload();
})
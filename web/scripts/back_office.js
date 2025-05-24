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

formularioEdicion.guardar.addEventListener("click", () => {
    event.preventDefault();
    const datosFormulario = {
        nombre: formularioEdicion.nombre.value,
        nivel: formularioEdicion.nivel.value,
        descripcion: formularioEdicion.descripcion.value,
        musculos: formularioEdicion.musculos.value,
    }
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
});

formularioEdicion.editar.addEventListener('click', () => {
    event.preventDefault();
    const datosFormulario = {
        nombre: formularioEdicion.nombre.value,
        nivel: formularioEdicion.nivel.value,
        descripcion: formularioEdicion.descripcion.value,
        musculos: formularioEdicion.musculos.value,
    }
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
});

cancelar.addEventListener('click', () => {
    window.location.reload();
})
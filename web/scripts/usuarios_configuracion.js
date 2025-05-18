//Formulario
const imagenActualizar = document.getElementById("imagenActualizar");
const alias = document.getElementById("aliasUsuario");
const aliasOriginal = document.getElementById("aliasOriginal");
const nombre = document.getElementById("nombreUsuario");
const fecha = document.getElementById("fecha");
const peso = document.getElementById("peso");
const clave = document.getElementById("clave")
const repetirClave = document.getElementById("repetirClave")
const imagenUsuario = document.getElementById("imgUsuario");
const editar = document.getElementById("editar");
const guardar = document.getElementById("guardar");
const cancelar = document.getElementById("cancelar");
const formularioEdicion = document.getElementById("formularioEdicion")
//Funcion que permite cargar los datos del usuario
function cargarSinEdicion() {
    let cookies = document.cookie.split("; ");
    let nombreUsuario;
    for (const cookie of cookies) {
        let [nombre, valor] = cookie.split("=");
        if (nombre == "nombreUsuario") {
            nombreUsuario = `${decodeURIComponent(valor)}`;
        }
    }
    imagenUsuario.src = `https://localhost:8080/iconos_usuarios/${nombreUsuario}.jpeg`;
    fetch('https://localhost:8080/controladores/configuracion_usuario_proceso.php', {
        method: 'POST',
        body: JSON.stringify(nombreUsuario)
    }).then(response => response.json()).then(data => {
        datos = data.datos;
        alias.value = datos.alias
        alias.disabled = true

        aliasOriginal.value = alias.value;

        nombre.value = datos.nombre;
        nombre.disabled = true

        peso.value = datos.peso;
        peso.disabled = true;

        fecha.value = datos.nacimiento;
        fecha.disabled = true;

        clave.disabled = true;
        clave.style.display = "none";

        repetirClave.disabled = true;
        repetirClave.style.display = "none";

        editar.style.display = 'block';

        imagenActualizar.style.display = "none";
        guardar.style.display = "none";
        cancelar.style.display = "none";
    })
}

window.addEventListener("load", () => {
    //Establecemos el rango maximo para la fecha de nacimiento
    const hoy = new Date();
    const anioMin = hoy.getFullYear() - 16;
    fecha.setAttribute("max", `${anioMin}-12-31`);

    //Muestra en kilogramos el valor que ingresa el usuario
    peso.addEventListener("change", () => {
        document.getElementById("valorPeso").innerText = `${peso.value}kg`;
    });

    cargarSinEdicion();
})

//Cambia el estilo del formulario para editar datos
editar.addEventListener("click", () => {
    event.preventDefault();
    alias.disabled = false;
    nombre.disabled = false;
    fecha.disabled = false;
    peso.disabled = false;
    clave.disabled = false;
    repetirClave.disabled = false;

    clave.style.display = "block";
    repetirClave.style.display = "block";
    imagenActualizar.style.display = "block";
    guardar.style.display = "block";
    cancelar.style.display = "block";

    editar.style.display = 'none';
})

//Envia los datos por json y permite actualizar los datos del usuario siguiendo las mismas reglas que en el registro
formularioEdicion.addEventListener('submit', () => {
    event.preventDefault()
    document.getElementById("error").innerHTML = ''
    let invalido = false;
    let errores = [];
    //validamos nombre de usuario
    const nombreUsuarioValido = new RegExp(/^[A-Za-z0-9\_\-][ A-Za-z0-9\_\-]{1,29}$/);
    if (!nombreUsuarioValido.test(alias.value)) {
        errores.push('El nombre de usuario es invalido, debe contener unicamente letras, numeros, espacios o los caracteres "_" "-"');
        invalido = true;
    }

    //Validamos el nombre
    const nombreValido = new RegExp(/^[A-Za-z][ A-Za-z]{1,49}$/);
    if (!nombreValido.test(nombre.value)) {
        errores.push('El nombre introducido es invalido, debe contener letras o espacios');
        invalido = true;
    }

    //Validamos la fecha de nacimiento
    const fechaSeleccionada = new Date(fecha.value);
    const hoy = new Date();
    const anioMin = hoy.getFullYear() - 16;
    let anio = fechaSeleccionada.getFullYear();

    if (fechaSeleccionada > hoy || anio < 1950 || fecha.value == "") {
        invalido = true;
        errores.push(`La fecha introducida es incorrecta, debe ser entre 1950 y ${anioMin}`);
    }

    //Validamos las contraseñas
    const contValida = new RegExp(/^[A-Za-z 0-9\_\-]{1,16}$/);
    if (!contValida.test(clave.value)) {
        invalido = true;
        errores.push("La contraseña debe tener entre 1 y 16 caracteres, solo se permiten letras, numeros, espacios o los caracteres _ -");
    } else if (clave.value !== repetirClave.value) {
        invalido = true;
        errores.push("Las contraseñas deben ser iguales");
    }

    //Validamos el peso
    const pesoMax = 200;
    const pesoMin = 0;
    if (peso.value > pesoMax || peso.value < pesoMin) {
        invalido = true;
        errores.push(`El peso introducido es invalido debe ser entre ${pesoMin} y ${pesoMax}`);
    }
    if (invalido) {
        let salida = "<ul>";
        for (const error of errores) {
            salida += `<li>${error}</li>`;
        }
        salida += "</ul>";
        document.getElementById("error").innerHTML = salida
    } else {
        fetch("https://localhost:8080/controladores/configuracion_usuario_proceso.php", {
            method: "POST",
            body: new FormData(formularioEdicion)
        }).then(response => response.json()).then(data => {
            if (data.nuevoUsuario) {
                alert("usuario actualizado");
                window.location.href = './inicio.html';
            } else {
                alert("error al actualizar");
            }
        });
    }
})

//Cancela la edicion del usuario y restablece el estilo del formulario
cancelar.addEventListener('click', () => {
    event.preventDefault();
    cargarSinEdicion();
    document.getElementById("error").innerHTML = ''
})

imagenActualizar.addEventListener("change", (evento) => {
    let archivo = evento.target.files[0];

    //Si se elimina la imagen que eligio el usuario, o la cambia, mostramos el cambio
    if (archivo) {
        let reader = new FileReader();

        // Cuando se carga el reader esta funcion mostrara la imagen
        reader.onload = function (e) {
            imagenUsuario.src = e.target.result;
        }
        // Esta funcion carga el reader
        reader.readAsDataURL(archivo);
    }
})
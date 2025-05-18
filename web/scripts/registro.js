
// Datos del formulario
const formulario = document.getElementById("datos");
const imagen = document.getElementById("imagen");
const usuario = document.getElementById("usuario");
const nombre = document.getElementById("nombre");
const clave = document.getElementById("clave");
const confirmar = document.getElementById("confirmar");
const nacimiento = document.getElementById("nacimiento");
const peso = document.getElementById("peso");
const existente = document.getElementById("existente");

// Mensaje de error
const error = document.getElementById("error");

//Establecemos el rango maximo para la fecha de nacimiento
const hoy = new Date();
const anioMin = hoy.getFullYear() - 16;
nacimiento.setAttribute("max", `${anioMin}-12-31`);

//Muestra en kilogramos el valor que ingresa el usuario
peso.addEventListener("change", () => {
    document.getElementById("valorPeso").innerText = `${peso.value}kg`;
});


//Muestra la imagen que envia el usuario
const previsualizacion = document.getElementById("previsualizacion");
imagen.addEventListener("change", (evento) => {
    let archivo = evento.target.files[0];

    //Si se elimina la imagen que eligio el usuario, o la cambia, mostramos el cambio
    if (archivo) {
        let reader = new FileReader();

        // Cuando se carga el reader esta funcion mostrara la imagen
        reader.onload = function (e) {
            previsualizacion.src = e.target.result;
            previsualizacion.style.display = 'block';
        }
        // Esta funcion carga el reader
        reader.readAsDataURL(archivo);
    } else {
        previsualizacion.src = '';
        previsualizacion.style.display = 'none';
    }
});

//validaremos los datos antes de enviarlos al servidor
formulario.addEventListener("submit", (evento) => {
    error.innerHTML = "";
    let invalido = false;
    let errores = []
    evento.preventDefault();

    //validamos nombre de usuario
    const nombreUsuarioValido = new RegExp(/^[A-Za-z0-9\_\-][ A-Za-z0-9\_\-]{1,29}$/);
    if (!nombreUsuarioValido.test(usuario.value)) {
        errores.push('El nombre de usuario es invalido, debe contener letras, numeros, espacios o los caracteres "_" "-"');
        invalido = true;
    }

    //Validamos el nombre
    const nombreValido = new RegExp(/^[A-Za-z][ A-Za-z]{1,49}$/);
    if (!nombreValido.test(nombre.value)) {
        errores.push('El nombre introducido es invalido, debe contener unicamente letras o espacios');
        invalido = true;
    }

    //Validamos la fecha de nacimiento
    const fecha = new Date(nacimiento.value);
    let anio = fecha.getFullYear();

    if (fecha > hoy || anio < 1950 || nacimiento.value == "") {
        invalido = true;
        errores.push(`La fecha introducida es incorrecta, debe ser entre 1950 y ${anioMin}`);
    }

    //Validamos las contraseñas
    const contValida = new RegExp(/^[A-Za-z 0-9\_\-]{1,16}$/);
    if (!contValida.test(clave.value)) {
        invalido = true;
        errores.push("La contraseña debe tener entre 1 y 16 caracteres, solo se permiten letras, numeros, espacios o los caracteres _ -");
    } else if (clave.value !== confirmar.value) {
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

    //Si todo es correcto enviamos el formulario, sino, mostramos los errores en los campos (Una imagen vacia no es un error, se le asignara la imagen por defecto al usuario)
    if (invalido) {
        let salida = "<ul>";
        for (const error of errores) {
            salida += `<li>${error}</li>`;
        }
        salida += "</ul>";
        document.getElementById("error").innerHTML = salida
    } else {
        const datosFormulario = new FormData(formulario);
        fetch("https://localhost:8080/controladores/registro_proceso.php", {
            method: "POST",
            body: datosFormulario
        }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    window.location.href = "https://localhost:443/index.html";
                } else {
                    console.log(data);
                }
            })
            .catch(error => console.error('Error:', error));
    }
})

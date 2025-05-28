// Secciones de la pagina
const listado = document.getElementById('listaGrupos');
const opcionesAdmin = document.getElementById('opcionesAdmin');

//Opciones generales
const crearGrupo = document.getElementById('crear');
const encontrarGrupo = document.getElementById('encontrar');
const insertarClave = document.getElementById('insertarClave');
const usuario = document.getElementById('usuario');
const buscarClave = document.getElementById('buscar');
const abandonar = document.getElementById('abandonar');

//Opciones de administrador
const editar = document.getElementById("editar");
const editarUsuarios = document.getElementById("editarUsuarios");

//Chat
const chat = document.getElementById("chat");
const mensajes = document.getElementById('mensajes');
const enviarMensaje = document.getElementById('enviarMensaje');
let mostrandoChat;
const nombreUsuario = document.getElementById("imagenUsuario").dataset.alias;


// Redirige a la vista donde se crean los grupos
crearGrupo.addEventListener('click', () => {
    document.cookie = `crearGrupo=true; path=/`;
    window.location.href = './grupos_configuracion.html';
})

// Activa el formulario para encontrar un nuevo grupo
encontrarGrupo.addEventListener('click', () => {
    insertarClave.style = '';
    encontrarGrupo.style = '';
})

let claveGrupo;
//Carga de la lista de grupos y modificacion de campos
window.addEventListener('load', () => {
    mostrandoChat = false;
    let grupos;
    //Asignamos el usuario para las opciones necesarias
    let cookies = document.cookie.split("; ");
    for (const cookie of cookies) {
        let [nombre, valor] = cookie.split("=");
        if (nombre == "nombreUsuario") {
            usuario.value = decodeURIComponent(valor);
        }
    }

    fetch(`https://localhost:8080/controladores/grupos_proceso.php?usuario=${usuario.value}`, {
        method: 'GET',

    }).then(response => response.json()).then(data => {
        grupos = data.grupos;
        listado.innerHTML = "";
        //Estructura de los grupos en la lista
        for (const grupo of grupos) {
            console.log(grupo)
            listado.innerHTML += `
                    <div class="grupo">
                        <div>
                        <img src="https://localhost:8080/iconos_grupos/${grupo.clave}.jpeg" class="imagen_grupo"/>
                        <p>${grupo["nombre"]}</p>
                        </div>
                        <p class="claveGrupo">${grupo.clave}</p>
                        <input type="text" value=${grupo.clave} style="display: none;"/>
                    </div>`;
        }
        //Activar las opciones admin
        const gruposListados = document.getElementsByClassName("grupo")
        for (const grupo of gruposListados) {
            grupo.addEventListener('click', () => {
                abandonar.style.display = 'block';
                if (mostrandoChat != false) {
                    clearInterval(mostrandoChat);
                }
                let datos = {
                    grupo: grupo.querySelector("input").value,
                    usuario: usuario.value
                }
                claveGrupo = datos.grupo;
                mostrarChat(datos.grupo)
                mostrandoChat = setInterval(function () {
                    mostrarChat(datos.grupo)
                }, 1000);

                fetch("https://localhost:8080/controladores/grupos_proceso.php", {
                    method: "POST",
                    body: JSON.stringify(datos),
                }).then(response => response.json()).then(data => {
                    if (data.lider) {
                        opcionesAdmin.style.display = '';
                        claveGrupo = grupo.querySelector("input").value;
                    } else {
                        opcionesAdmin.style.display = 'none';
                    }
                })
                chat.style.display = 'block';
            })
        }
    })
    //Oculta las cosas que no se ven al inicio
    chat.style.display = 'none';
    abandonar.style.display = 'none';
    opcionesAdmin.style.display = 'none';
    insertarClave.style.display = 'none';
    usuario.style.visibility = 'hidden';
})

//Opciones admin
editar.addEventListener('click', () => {
    document.cookie = `editarGrupo=true; max-age=600; path=/`
    document.cookie = `claveGrupo=${claveGrupo}; max-age=600; path=/`
    window.location.href = "./grupos_configuracion.html"
})

//Edicion de usuarios
editarUsuarios.addEventListener('click', () => {
    document.cookie = `editarUsuarios=${claveGrupo}; max-age=600; path=/`
    window.location.href = './editar_usuarios.html'
})

//Funcion que carga el contenido del chat
function mostrarChat(grupo) {
    let chatActual;
    fetch(`https://localhost:8080/controladores/chat_proceso.php?grupo=${grupo}&nombreUsuario=${nombreUsuario}`, {
        method: 'GET'
    }).then(response => response.json()).then(data => {
        if (data.noEncontrado) {
            window.location.reload();
        } else {
            chatActual = data.chat;
            formatearChat(chatActual);
        }
    })
}

//Da formato al chat y muestra los mensajes
function formatearChat(chatActual) {
    let listaMensajes = chatActual.split(";")
    let mensaje
    mensajes.innerHTML = ""
    for (const texto of listaMensajes) {
        if (texto.includes("╝")) {
            mensaje = texto.split("╝")
            mensajes.innerHTML += `<p>${mensaje[0]}: ${mensaje[1]} ${mensaje[2]}</p>`
        } else {
            mensajes.innerHTML += `<p>${texto}</p>`
        }
    }
}

//Guarda los mensajes enviados y actualiza el chat que ve el usuario
enviarMensaje.addEventListener('submit', () => {
    event.preventDefault()
    let chatActual
    const datos = {
        mensaje: enviarMensaje.mensaje.value,
        nombreUsuario,
        grupo: claveGrupo
    };
    if (datos.mensaje.trim() != "") {
        fetch("https://localhost:8080/controladores/chat_proceso.php", {
            method: "POST",
            body: JSON.stringify(datos)
        }).then(response => response.json()).then(data => {
            chatActual = data.chat
            formatearChat(chatActual)
        })
        enviarMensaje.mensaje.value = ""
    }
})

//Permite que un usuario abandone el grupo seleccionado
abandonar.addEventListener('click', () => {
    fetch(`https://localhost:8080/controladores/edicion_usuarios.php?nombreUsuario=${nombreUsuario}&abandonarGrupo=${claveGrupo}`, {
        method: 'GET'
    }).then(response => response.json()).then(data => {
        window.location.reload();
    })
})

//Envía la clave del grupo para buscarlo
insertarClave.addEventListener("submit", () => {
    const datos = new FormData(insertarClave);
    datos.append("buscar", "1");
    fetch("https://localhost:8080/controladores/grupos_proceso.php", {
        method: 'POST',
        body: datos
    }).then(response => response.json()).then(data => {
        if (data.encontrado) {
            window.location.reload();
        } else {
            alert("No se ha encontrado ningun grupo con esa clave")
        }
    })
})
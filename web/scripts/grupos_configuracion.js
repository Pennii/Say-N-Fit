
//Opciones de creacion
const menuCrear = document.getElementById('crearGrupo');

//Opciones de edicion
const menuEdicion = document.getElementById('editarGrupo');

const errores = document.getElementById("errores");

// Carga del formulario necesario
let menuActual;
window.addEventListener('load', () => {
    if (document.cookie.includes('crearGrupo') || document.cookie.includes('editarGrupo')) {
        let bienvenido = document.getElementById("bienvenido");
        let cookies = document.cookie.split("; ");
        let usuarioLider;
        let claveGrupo;
        for (const cookie of cookies) {
            let [nombre, valor] = cookie.split("=");
            if (nombre == "nombreUsuario") {
                usuarioLider = decodeURIComponent(valor);
            }

            if (nombre == "claveGrupo") {
                claveGrupo = decodeURIComponent(valor);
            }
        }
        // Con esto se controla si se crea o modifica un grupo
        if (document.cookie.includes('crearGrupo')) {
            menuCrear.style.display = 'block';
            menuActual = menuCrear;
            //Se almacena el nombre del usuario actual para saber quien es el lider del grupo
            let creador = menuActual.lider;
            creador.style.visibility = 'hidden';
            creador.value = usuarioLider;
        }
        if (document.cookie.includes('editarGrupo')) {
            menuEdicion.style.display = 'block';
            menuActual = menuEdicion;
            //Si se edita el grupo se tienen que cargar los datos que ya se tienen
            let datos = {
                grupo: claveGrupo
            }
            fetch('https://localhost:8080/controladores/grupos_proceso.php', {
                method: 'POST',
                body: JSON.stringify(datos)
            }).then(response => response.json()).then(data => {
                datosGrupo = data.grupo;
                usuarios = data.usuarios;
                menuActual.nombre.value = datosGrupo.nombre
                menuActual.clave.value = datosGrupo.clave
                menuActual.clave.disabled = true;
                console.log(data)

                menuActual.previsualizacion.src = `https://localhost:8080/iconos_grupos/${datosGrupo.clave}.jpeg`
                for (const usuario of usuarios) {
                    if (usuario[0] == usuarioLider) {
                        menuActual.lider.innerHTML += `<option value="${usuario[0]}" selected>${usuario[0]}</option>`;
                    } else {
                        menuActual.lider.innerHTML += `<option value="${usuario[0]}">${usuario[0]}</option>`;
                    }
                }
            })
        }


        //Como se usa la variable de menuActual para no repetir codigo los eventos se tienen que declarar cuando la pagina se carga
        // Si se decide cancelar la creacion de grupos se vuelve a la pagina anterior
        menuActual.cancelar.addEventListener('click', () => {
            event.preventDefault();
            document.cookie = "crearGrupo=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
            document.cookie = "editarGrupo=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
            window.location.href = './grupos.html';
        })

        //Para que la imagen se pueda ver antes de cargarla
        const imagen = menuActual.imagen;
        const previsualizacion = menuActual.previsualizacion;
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

        //Verificamos los datos de los formularios y borramos las cookies si hace falta
        const errores = document.getElementById("errores");
        menuActual.addEventListener('submit', (event) => {
            event.preventDefault();
            let listaErrores = [];

            //Se verifica el nombre del grupo
            let nombreValido = new RegExp(/^[a-zA-Z0-9\s]{2,30}$/);
            if (!nombreValido.test(menuActual.nombre.value)) {
                listaErrores.push("El nombre tiene que ser de entre 2 y 30 caracteres")
            }

            //Se verifica la clave del grupo
            let claveValida = new RegExp(/^[a-zA-Z0-9]{3,20}$/);
            if (!claveValida.test(menuActual.clave.value)) {
                listaErrores.push("La clave debe contener entre 3 y 20 caracteres")
            }
            if (listaErrores.length != 0) {
                errores.innerHTML = "<ul>"
                for (const error of listaErrores) {
                    errores.innerHTML += `<li>${error}</li>`
                }
                errores.innerHTML += '</ul>'
            } else {
                menuActual.clave.disabled = false
                const datos = new FormData(menuActual);
                if (menuActual == menuCrear) {
                    datos.append("crear", "1");
                } else {
                    datos.append("confirmarCambios", '1');
                }
                fetch("https://localhost:8080/controladores/grupos_proceso.php", {
                    method: 'POST',
                    body: datos
                }).then(response => response.json()).then(data => {
                    if (data.grupoCreado) {
                        document.cookie = `crearGrupo=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
                        alert("grupo creado")
                        window.location.reload();
                    } else if (data.grupoExistente) {
                        errores.innerHTML = '<ul><li>La clave de grupo ya existe</li></ul>';
                    } else if (data.actualizado) {
                        document.cookie = `editarGrupo=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
                        document.cookie = `claveGrupo=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
                        window.location.reload();
                    } else {
                        alert("errors")
                    }
                })
            }
        })

        //Mostramos un mensaje en pantalla si el usuario lider cambia el lider de grupo
        const aviso = document.getElementById("aviso");
        menuActual.lider.addEventListener('change', () => {
            if (menuActual.lider.value != usuarioLider) {
                aviso.style.display = "block"
            } else {
                aviso.style.display = "none"
            }
        })
    } else {
        window.location.href = "./grupos.html";
    }

})

// SCRIPT GENERAL - Login y Usuario


document.addEventListener('DOMContentLoaded', function() {
    // Detectar en qué página estamos
    const isLoginPage = document.getElementById('Email') !== null;
    const isUsuarioPage = document.getElementById('nombreUsuario') !== null;

    if (isLoginPage) {
        inicializarLogin();
    }

    if (isUsuarioPage) {
        inicializarPanelUsuario();
    }
});


// FUNCIONES DE LOGIN


function inicializarLogin() {
    const loginForm = document.querySelector('form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('Email').value.trim();
            const password = document.getElementById('Password').value.trim();
            const rememberMe = document.getElementById('RememberMe').checked;
            
            // Limpiar mensajes previos
            mostrarMensaje('', ''); 
            
            // Validar campos vacíos
            if (!email || !password) {
                mostrarMensaje('Por favor, completa todos los campos', 'error');
                return;
            }
            
            // Preparar datos
            const formData = new FormData();
            formData.append('email', email);
            formData.append('contrasena', password);
            formData.append('remember', rememberMe);
            
            const submitButton = loginForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Iniciando sesión...';
            
            try {
                 const response = await fetch('../php/controller/control.php?action=login', {
                    method: 'POST',
                    body: formData,
                });
                
                console.log('Status de respuesta:', response.status);
                console.log('URL llamada:', response.url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const textResponse = await response.text();
                console.log('Respuesta del servidor:', textResponse);
                
                let data;
                try {
                    data = JSON.parse(textResponse);
                } catch (e) {
                    console.error('La respuesta no es JSON válido:');
                    console.error('RESPUESTA COMPLETA:', textResponse);
                    throw new Error('El servidor no devolvió JSON válido');
                }
                
                if (data.success) {
                    mostrarMensaje('Inicio de sesión exitoso. Redirigiendo...', 'exito');
                    
                    if (rememberMe) {
                        localStorage.setItem('userEmail', email);
                    } else {
                        localStorage.removeItem('userEmail');
                    }
                    
                    setTimeout(() => {
                        if (data.tipo === 'admin') {
                            window.location.href = 'admin.html';
                        } else {
                            window.location.href = 'usuario.html';
                        }
                    }, 1200);
                } else {
                    mostrarMensaje(data.message || 'Credenciales incorrectas', 'error');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Entrar';
                }
            } catch (error) {
                console.error('Error de conexión:', error);
                mostrarMensaje('No se pudo conectar con el servidor: ' + error.message, 'error');
                submitButton.disabled = false;
                submitButton.textContent = 'Entrar';
            }
        });
    }
    
    // Cargar email guardado si existe
    const savedEmail = localStorage.getItem('userEmail');
    if (savedEmail) {
        const emailInput = document.getElementById('Email');
        const rememberCheckbox = document.getElementById('RememberMe');
        if (emailInput && rememberCheckbox) {
            emailInput.value = savedEmail;
            rememberCheckbox.checked = true;
        }
    }
}

// Función para mostrar mensajes en Login
function mostrarMensaje(mensaje, tipo) {
    const errorDiv = document.querySelector('.text-danger');
    if (errorDiv && mensaje) {
        errorDiv.textContent = mensaje;
        errorDiv.style.marginBottom = '15px';
        errorDiv.style.display = 'block';
        
        if (tipo === 'error') {
            errorDiv.style.color = 'red';
        } else if (tipo === 'exito') {
            errorDiv.style.color = 'green';
        }
    } else if (errorDiv) {
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
    }
}


// FUNCIONES DE PANEL USUARIO


function inicializarPanelUsuario() {
    cargarDatosUsuario();

    // Cerrar sesión
    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function(e) {
            e.preventDefault();
            cerrarSesion();
        });
    }

    // Editar perfil
    const btnEditarPerfil = document.getElementById('btnEditarPerfil');
    if (btnEditarPerfil) {
        btnEditarPerfil.addEventListener('click', function() {
            habilitarEdicion();
        });
    }

    // Guardar perfil
    const btnGuardarPerfil = document.getElementById('btnGuardarPerfil');
    if (btnGuardarPerfil) {
        btnGuardarPerfil.addEventListener('click', function() {
            guardarCambios();
        });
    }
}

// Cargar datos del usuario desde la sesión de PHP
function cargarDatosUsuario() {
    fetch('../php/controller/control.php?action=obtenerSesion', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('nombreUsuario').textContent = data.nombre;
            document.getElementById('email').value = data.email;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('apellido').value = data.apellido || '';
            document.getElementById('telefono').value = data.telefono || '';
            document.getElementById('genero').value = data.genero || 'No especificado';
        } else {
            alert('Sesión no válida. Por favor, inicia sesión nuevamente.');
            window.location.href = 'Login.html';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos. Redirigiendo al login...');
        window.location.href = 'Login.html';
    });
}

// Habilitar edición de campos
function habilitarEdicion() {
    document.getElementById('nombre').removeAttribute('readonly');
    document.getElementById('apellido').removeAttribute('readonly');
    document.getElementById('telefono').removeAttribute('readonly');
    
    document.getElementById('btnEditarPerfil').style.display = 'none';
    document.getElementById('btnGuardarPerfil').style.display = 'inline-block';
}

// Guardar cambios del perfil
function guardarCambios() {
    const formData = new FormData();
    formData.append('nombre', document.getElementById('nombre').value);
    formData.append('apellido', document.getElementById('apellido').value);
    formData.append('telefono', document.getElementById('telefono').value);

    fetch('../php/controller/control.php?action=actualizarPerfil', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Perfil actualizado correctamente');
            
            document.getElementById('nombre').setAttribute('readonly', true);
            document.getElementById('apellido').setAttribute('readonly', true);
            document.getElementById('telefono').setAttribute('readonly', true);
            
            document.getElementById('btnEditarPerfil').style.display = 'inline-block';
            document.getElementById('btnGuardarPerfil').style.display = 'none';
            
            // Actualizar el nombre en el encabezado
            document.getElementById('nombreUsuario').textContent = data.nombre;
        } else {
            alert(data.message || 'Error al actualizar el perfil');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

// Cerrar sesión
function cerrarSesion() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        fetch('../php/controller/control.php?action=logout', {
            method: 'POST',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            window.location.href = 'Login.html';
        })
        .catch(error => {
            console.error('Error:', error);
            window.location.href = 'Login.html';
        });
    }
}
// Script para manejar el login
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevenir el envío tradicional del formulario
            
            // Obtener los valores del formulario
            const email = document.getElementById('Email').value.trim();
            const password = document.getElementById('Password').value.trim();
            const rememberMe = document.getElementById('RememberMe').checked;
            
            // Limpiar mensajes previos
            mostrarMensaje('', ''); 
            
            // Validar campos vacíos
            if (!email || !password) {
                mostrarMensaje('Por favor, completa todos los campos', 'error');
                return; // ✅ Ahora SÍ está dentro de una función
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
                // ✅ CORREGIDO: Agregado .php a la URL
                const response = await fetch('../php/controller/control.php?action=login', {
                    method: 'POST',
                    body: formData,
                });
                
                console.log('Status de respuesta:', response.status);
                console.log('URL llamada:', response.url);
                
                // Verificar si la respuesta es OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Obtener el texto primero para ver qué devuelve
                const textResponse = await response.text();
                console.log('Respuesta del servidor:', textResponse);
                
                // Intentar parsear como JSON
                let data;
                try {
                    data = JSON.parse(textResponse);
                } catch (e) {
                    console.error('La respuesta no es JSON válido:', textResponse.substring(0, 200));
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
                            window.location.href = '../Index.html';
                        }
                    }, 1200);
                } else {
                    mostrarMensaje(data.message || 'Credenciales incorrectas', 'error');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Entrar';
                }
            } catch (error) {
                console.error('Error de conexión:', error);
                mostrarMensaje('No se pudo conectar con el servidor.', 'error');
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
});

// Función para mostrar mensajes
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
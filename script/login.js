 // Script para manejar el login
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form');
            
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const email = document.getElementById('Email').value;
                    const password = document.getElementById('Password').value;
                    const rememberMe = document.getElementById('RememberMe').checked;
                    
                    // Limpiar mensajes de error previos
                    const errorMessages = document.querySelectorAll('.text-danger');
                    errorMessages.forEach(msg => msg.textContent = '');
                    
                    if (!email || !password) {
                        mostrarError('Por favor, complete todos los campos');
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('contrasena', password);
                    formData.append('remember', rememberMe);
                    
                    const submitButton = loginForm.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.textContent = 'Iniciando sesión...';
                    
                    fetch('../controllers/Control.php?action=login', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarExito('¡Inicio de sesión exitoso! Redirigiendo...');
                            
                            if (rememberMe) {
                                localStorage.setItem('userEmail', email);
                            }
                            
                            setTimeout(() => {
                                if (data.tipo === 'admin') {
                                    window.location.href = '../views/admin/Dashboard.html';
                                } else {
                                    window.location.href = '../Index.html';
                                }
                            }, 1000);
                            
                        } else {
                            mostrarError(data.message || 'Credenciales incorrectas');
                            submitButton.disabled = false;
                            submitButton.textContent = 'Entrar';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarError('Error de conexión. Por favor, intente nuevamente.');
                        submitButton.disabled = false;
                        submitButton.textContent = 'Entrar';
                    });
                });
            }
            
            // Cargar email guardado si existe
            const savedEmail = localStorage.getItem('userEmail');
            if (savedEmail) {
                document.getElementById('Email').value = savedEmail;
                document.getElementById('RememberMe').checked = true;
            }
        });

        function mostrarError(mensaje) {
            const errorDiv = document.querySelector('.text-danger');
            if (errorDiv) {
                errorDiv.textContent = mensaje;
                errorDiv.style.color = 'red';
                errorDiv.style.marginBottom = '15px';
                errorDiv.style.display = 'block';
            }
        }

        function mostrarExito(mensaje) {
            const errorDiv = document.querySelector('.text-danger');
            if (errorDiv) {
                errorDiv.textContent = mensaje;
                errorDiv.style.color = 'green';
                errorDiv.style.marginBottom = '15px';
                errorDiv.style.display = 'block';
            }
        }
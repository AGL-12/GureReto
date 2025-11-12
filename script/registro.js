// js/registro.js

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Limpiar errores previos
        document.querySelectorAll('.text-danger').forEach(span => {
            span.textContent = '';
        });

        const formData = new FormData(form);

        // Convertir FormData a objeto + añadir 'action'
        const data = Object.fromEntries(formData);
        data.action = 'register';

        try {
            const response = await fetch("../php/controller/Control.php?action=register", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)  // Enviar como form-urlencoded
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message || '¡Registro exitoso!');
                if (result.redirect) {
                    window.location.href = result.redirect;
                }
            } else {
                // Mostrar errores por campo
                if (result.errores && typeof result.errores === 'object') {
                    Object.keys(result.errores).forEach(campo => {
                        const input = form.querySelector(`[name="${campo}"]`);
                        if (input) {
                            const span = input.parentElement.querySelector('.text-danger');
                            if (span) {
                                span.textContent = result.errores[campo];
                                span.style.color = 'red';
                                span.style.fontSize = '0.9em';
                            }
                        }
                    });
                } else {
                    const general = form.querySelector('.text-danger');
                    if (general) {
                        general.textContent = result.message || 'Error desconocido';
                    } else {
                        alert(result.message);
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión. Inténtalo más tarde.');
        }
    });
});
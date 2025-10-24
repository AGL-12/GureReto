const form = document.querySelector("form");
form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const email = document.getElementById("Email").value;
    const password = document.getElementById("Password").value;
    const rememberMe = document.getElementById("RememberMe").checked;

    // Validación básica del formulario
    if (!email || !password) {
        alert('Por favor, complete todos los campos.');
        return;
    }

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('RememberMe', rememberMe);
    try {
        const response = await fetch("../php/api/iniciarSesion.php", {
            method: 'POST',
            body: formData
        });

        // Verificar el tipo de contenido de la respuesta
        const contentType = response.headers.get('Content-Type');
        if (!contentType || !contentType.includes('application/json')) {
            // Si la respuesta no es JSON, mostrar un mensaje de error
            const errorText = await response.text();
            console.error('La respuesta no es JSON:', errorText);
            alert('Hubo un error en la respuesta del servidor.');
            return;
        }

        const result = await response.json();  // Ahora es seguro parsear a JSON

        console.log(result);
        if (result.success) {
            if (rememberMe) {
                localStorage.setItem('email', email);
            }
            if (result.tipo === 'admin') {
                window.location.href = "../view/admin.html";
            } else {
                window.location.href = "../view/user.html";
            }
        } else {
            alert('Error de inicio de sesión: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de red. Por favor, inténtelo de nuevo más tarde.');
    }
});
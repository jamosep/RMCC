document.addEventListener("DOMContentLoaded", function() {

    // Variables para los botones y formularios
    const recepcionMaterialBtn = document.getElementById('recepcionMaterial');
    const formRecepcion = document.getElementById('formRecepcion');
    const cancelarButton = document.getElementById('cancelar');

    const registroCalidadBtn = document.getElementById('registroCalidad');
    const formRegistroCalidad = document.getElementById('formRegistroCalidad');
    const cancelarRegistroCalidadButton = document.getElementById('cancelarRegistro');

    const consultarRegistroBtn = document.getElementById('consultarRegistro');
    const formConsultarRegistro = document.getElementById('formConsultarRegistro');

    const materialInput = document.getElementById('materialInput');
    const consultarButton = document.getElementById('consultar');
    
    // Función para alternar la visibilidad de los formularios
    function toggleForm(formElement, buttonElement, isVisible) {
        if (isVisible) {
            formElement.style.display = 'none';
            buttonElement.classList.remove('active');
        } else {
            formElement.style.display = 'block';
            buttonElement.classList.add('active');
        }
        return !isVisible;
    }

    function hideOtherForms(formElement, buttonElement, isVisible) {
        if (isVisible) {
            formElement.style.display = 'none';
            buttonElement.classList.remove('active');
            return false;
        }
        return isVisible;
    }

    // Inicialmente, ocultar todos los formularios
    let isFormRecepcionVisible = false;
    let isFormRegistroCalidadVisible = false;
    let isFormConsultarRegistroVisible = false;

    // Eventos para alternar los formularios cuando se hace clic en los botones
    recepcionMaterialBtn.addEventListener('click', function() {
        isFormRecepcionVisible = toggleForm(formRecepcion, recepcionMaterialBtn, isFormRecepcionVisible);
        isFormRegistroCalidadVisible = hideOtherForms(formRegistroCalidad, registroCalidadBtn, isFormRegistroCalidadVisible);
        isFormConsultarRegistroVisible = hideOtherForms(formConsultarRegistro, consultarRegistroBtn, isFormConsultarRegistroVisible);
    });

    registroCalidadBtn.addEventListener('click', function() {
        isFormRegistroCalidadVisible = toggleForm(formRegistroCalidad, registroCalidadBtn, isFormRegistroCalidadVisible);
        isFormRecepcionVisible = hideOtherForms(formRecepcion, recepcionMaterialBtn, isFormRecepcionVisible);
        isFormConsultarRegistroVisible = hideOtherForms(formConsultarRegistro, consultarRegistroBtn, isFormConsultarRegistroVisible);
    });

    consultarRegistroBtn.addEventListener('click', function() {
        isFormConsultarRegistroVisible = toggleForm(formConsultarRegistro, consultarRegistroBtn, isFormConsultarRegistroVisible);
        isFormRecepcionVisible = hideOtherForms(formRecepcion, recepcionMaterialBtn, isFormRecepcionVisible);
        isFormRegistroCalidadVisible = hideOtherForms(formRegistroCalidad, registroCalidadBtn, isFormRegistroCalidadVisible);
    });

    

    // Función para manejar el botón de regresar
    document.getElementById('regresar').addEventListener('click', function() {
        window.location.href = 'index.php';
    });
});

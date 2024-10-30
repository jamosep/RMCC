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

    const options = document.querySelectorAll('.option');
    const returnButtons = document.querySelectorAll('.return-button');
    
    
    
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

        // Modificar estructura HTML para soportar flip
        options.forEach(option => {
            // Crear contenedor para el efecto flip
            const cardContainer = document.createElement('div');
            cardContainer.className = 'card-container';
            
            // Crear frente de la tarjeta
            const cardFront = document.createElement('div');
            cardFront.className = 'card-front';
            cardFront.innerHTML = option.innerHTML;
            
            // Crear reverso de la tarjeta (contendrá el formulario)
            const cardBack = document.createElement('div');
            cardBack.className = 'card-back';
            
            // Reorganizar elementos
            option.innerHTML = '';
            cardContainer.appendChild(cardFront);
            cardContainer.appendChild(cardBack);
            option.appendChild(cardContainer);
        });
    
        // Función para manejar el click en las opciones
        function handleOptionClick(clickedOption) {
            const formId = clickedOption.id.replace('btn', 'form');
            const form = document.getElementById(formId);
            
            if (!form) return;
    
            // Copiar el formulario al reverso de la tarjeta
            const cardBack = clickedOption.querySelector('.card-back');
            cardBack.innerHTML = '';
            cardBack.appendChild(form.cloneNode(true));
            cardBack.style.display = 'block';
    
            // Animar la tarjeta seleccionada
            clickedOption.classList.add('flipped');
            
            // Ocultar otras opciones
            options.forEach(option => {
                if (option !== clickedOption) {
                    option.classList.add('hide-card');
                }
            });
    
            // Expandir el formulario después del flip
            setTimeout(() => {
                cardBack.querySelector('form').classList.add('form-expanded');
            }, 400);
        }
    
        // Asignar eventos click
        options.forEach(option => {
            option.addEventListener('click', () => handleOptionClick(option));
        });
    
        // Manejar botones de cancelar/regresar
        document.querySelectorAll('.return-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Revertir todas las animaciones
                options.forEach(option => {
                    option.classList.remove('flipped', 'hide-card');
                    const form = option.querySelector('form');
                    if (form) form.classList.remove('form-expanded');
                });
            });
        });
});

/**
 * Validación de formularios en el cliente.
 *
 * Se apoya en los atributos HTML5 (required, type, minlength, maxlength, pattern, min, max)
 * y muestra mensajes en español junto a cada campo. Se activa en formularios con
 * el atributo data-validar. El servidor vuelve a validar todo con Form Requests.
 */
const MENSAJES = {
    valueMissing: 'Este campo es obligatorio.',
    typeMismatch: (campo) => (campo.type === 'email' ? 'Escribe un correo electrónico válido.' : 'Escribe una URL válida (https://...).'),
    tooShort: (campo) => `Escribe al menos ${campo.minLength} caracteres.`,
    tooLong: (campo) => `Escribe como máximo ${campo.maxLength} caracteres.`,
    patternMismatch: (campo) => campo.dataset.mensajePatron || 'El formato no es válido.',
    rangeUnderflow: (campo) => `El valor mínimo es ${campo.min}.`,
    rangeOverflow: (campo) => `El valor máximo es ${campo.max}.`,
    stepMismatch: 'Escribe un valor válido.',
    badInput: 'Escribe un valor válido.',
};

function mensajePara(campo) {
    for (const clave of Object.keys(MENSAJES)) {
        if (campo.validity[clave]) {
            const mensaje = MENSAJES[clave];
            return typeof mensaje === 'function' ? mensaje(campo) : mensaje;
        }
    }

    // Reglas personalizadas: data-igual-a="password" (confirmación) y data-mayor-igual="fecha_inicio".
    if (campo.dataset.igualA) {
        const otro = campo.form.elements[campo.dataset.igualA];
        if (otro && otro.value !== campo.value) {
            return 'La confirmación no coincide.';
        }
    }

    if (campo.dataset.mayorIgual) {
        const otro = campo.form.elements[campo.dataset.mayorIgual];
        if (otro && otro.value && campo.value && campo.value < otro.value) {
            return campo.dataset.mensajeMayorIgual || 'Debe ser posterior o igual al campo anterior.';
        }
    }

    return '';
}

function contenedorError(campo) {
    let error = campo.parentElement.querySelector('[data-error-cliente]');
    if (!error) {
        error = document.createElement('p');
        error.dataset.errorCliente = '';
        error.className = 'mt-1 text-xs text-red-600';
        error.setAttribute('role', 'alert');
        campo.insertAdjacentElement('afterend', error);
    }
    return error;
}

function validarCampo(campo) {
    if (!campo.willValidate) {
        return true;
    }

    campo.setCustomValidity('');
    const mensaje = mensajePara(campo);
    campo.setCustomValidity(mensaje);

    const error = contenedorError(campo);
    error.textContent = mensaje;
    campo.classList.toggle('campo-error', mensaje !== '');
    campo.setAttribute('aria-invalid', mensaje !== '' ? 'true' : 'false');

    return mensaje === '';
}

export function iniciarValidacion(raiz = document) {
    raiz.querySelectorAll('form[data-validar]').forEach((formulario) => {
        formulario.setAttribute('novalidate', '');

        formulario.addEventListener('blur', (evento) => {
            if (evento.target.matches('input, select, textarea')) {
                validarCampo(evento.target);
            }
        }, true);

        formulario.addEventListener('submit', (evento) => {
            const campos = [...formulario.querySelectorAll('input, select, textarea')];
            const invalidos = campos.filter((campo) => !validarCampo(campo));

            if (invalidos.length > 0) {
                evento.preventDefault();
                invalidos[0].focus();
                return;
            }

            // Evita envíos dobles (p. ej. doble clic al postularse).
            formulario.querySelectorAll('[type="submit"]').forEach((boton) => {
                boton.disabled = true;
                boton.dataset.textoOriginal = boton.innerHTML;
                boton.innerHTML = 'Enviando…';
            });
        });
    });
}

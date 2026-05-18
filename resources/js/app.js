import './bootstrap';

function toggleVisibility(elementId, hidden) {
    const element = document.getElementById(elementId);

    if (!element) {
        return;
    }

    element.classList.toggle('hidden', hidden);
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

function apiToken() {
    // El token se conserva en localStorage para reutilizarlo en todas las llamadas /api.
    return window.localStorage.getItem('api_token');
}

function storeApiToken(token) {
    if (!token) {
        return;
    }

    window.localStorage.setItem('api_token', token);
}

function clearApiToken() {
    window.localStorage.removeItem('api_token');
}

function apiUrl(url) {
    if (/^https?:\/\//.test(url) || url.startsWith('/api/')) {
        return url;
    }

    return `/api${url.startsWith('/') ? url : `/${url}`}`;
}

async function requestJson(url, options = {}) {
    // Si el body es FormData no hay que forzar Content-Type JSON.
    const isFormData = options.body instanceof FormData;
    const token = apiToken();
    const headers = {
        'Accept': 'application/json',
        ...(options.headers || {}),
    };

    if (token) {
        // Sanctum autentica la API leyendo el Bearer token desde Authorization.
        headers.Authorization = `Bearer ${token}`;
    }

    if (!isFormData) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(apiUrl(url), {
        ...options,
        credentials: 'same-origin',
        headers,
    });

    const data = await response.json().catch(() => ({}));

    if (response.status === 401) {
        clearApiToken();
    }

    return { response, data };
}

async function loginWithApi(form) {
    const formData = new FormData(form);
    const payload = {
        email: formData.get('email'),
        password: formData.get('password'),
    };

    const { response, data } = await requestJson('/login', {
        method: 'POST',
        body: JSON.stringify(payload),
    });

    return { response, data };
}

function documentTemaTypes() {
    return Array.isArray(window.documentTemaTypes) ? window.documentTemaTypes : [];
}

function typeSupportsTema(type) {
    return documentTemaTypes().includes(type);
}

function syncCreateDocumentTemaInput() {
    const select = document.getElementById('document-type');
    const wrapper = document.getElementById('document-tema-wrapper');
    const input = document.getElementById('document-tema');

    if (!select || !wrapper || !input) {
        return;
    }

    const supportsTema = typeSupportsTema(select.value);

    wrapper.classList.toggle('hidden', !supportsTema);
    input.disabled = !supportsTema;

    if (!supportsTema) {
        input.value = '';
    }
}

window.toggleSidebar = function toggleSidebar() {
    document.body.classList.toggle('sidebar-closed');
    document.body.classList.toggle('sidebar-open');
};

window.toggleUserDropdown = function toggleUserDropdown() {
    document.getElementById('userDropdown')?.classList.toggle('hidden');
};

window.showCreatePlantillaForm = function showCreatePlantillaForm() {
    toggleVisibility('create-plantilla-form', false);
};

window.hideCreatePlantillaForm = function hideCreatePlantillaForm() {
    toggleVisibility('create-plantilla-form', true);
};

window.createPlantilla = async function createPlantilla() {
    const tipoDocumento = document.getElementById('plantilla-tipo-documento');
    const archivo = document.getElementById('plantilla-archivo');

    // El formulario necesita un tipo de documento.
    if (!tipoDocumento?.value) {
        alert('Selecciona un tipo de documento');
        return;
    }

    // Y tambien necesita el archivo de Word.
    if (!archivo?.files?.length) {
        alert('Adjunta un archivo .doc o .docx');
        return;
    }

    // Se envia como multipart/form-data para incluir el archivo.
    const formData = new FormData();
    formData.append('tipo_documento', tipoDocumento.value);
    formData.append('archivo', archivo.files[0]);

    const { response, data } = await requestJson('/plantillas/create', {
        method: 'POST',
        body: formData,
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al crear plantilla');
        return;
    }

    location.reload();
};

window.crearPR = async function crearPR(courseId) {
    const { response, data } = await requestJson(`/courses/${courseId}/pr/create`, {
        method: 'POST',
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al crear PR');
        return;
    }

    location.reload();
};

window.showEditFechaLimite = function showEditFechaLimite(prId) {
    toggleVisibility(`fecha-limite-view-${prId}`, true);
    toggleVisibility(`fecha-limite-edit-${prId}`, false);
};

window.hideEditFechaLimite = function hideEditFechaLimite(prId) {
    toggleVisibility(`fecha-limite-edit-${prId}`, true);
    toggleVisibility(`fecha-limite-view-${prId}`, false);
};

window.updateFechaLimite = async function updateFechaLimite(prId) {
    const fecha = document.getElementById(`fecha-limite-input-${prId}`)?.value;
    const { response, data } = await requestJson(`/pr/${prId}/fecha_limite/update`, {
        method: 'POST',
        body: JSON.stringify({ fecha_limite: fecha }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al actualizar fecha límite');
        return;
    }

    location.reload();
};

window.cambiarFase = async function cambiarFase(prId) {
    const fase = document.getElementById(`fase-select-${prId}`)?.value;
    const { response, data } = await requestJson(`/pr/${prId}/fase/update`, {
        method: 'POST',
        body: JSON.stringify({ fase }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al actualizar la fase');
        location.reload();
        return;
    }

    location.reload();
};

window.showEditDocentes = function showEditDocentes(prId) {
    toggleVisibility(`docentes-list-${prId}`, true);
    toggleVisibility(`docentes-edit-${prId}`, false);
};

window.hideEditDocentes = function hideEditDocentes(prId) {
    toggleVisibility(`docentes-edit-${prId}`, true);
    toggleVisibility(`docentes-list-${prId}`, false);
    window.hideAddDocente(prId);
};

window.showAddDocente = function showAddDocente(prId) {
    toggleVisibility(`add-docente-select-${prId}`, false);
};

window.hideAddDocente = function hideAddDocente(prId) {
    toggleVisibility(`add-docente-select-${prId}`, true);
};

window.addDocente = async function addDocente(prId) {
    const docenteId = document.getElementById(`add-docente-${prId}`)?.value;
    const { response, data } = await requestJson(`/pr/${prId}/docentes/add`, {
        method: 'POST',
        body: JSON.stringify({ docentes: [docenteId] }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al agregar docente');
        return;
    }

    location.reload();
};

window.removeDocente = async function removeDocente(prId, docenteId) {
    const { response, data } = await requestJson(`/pr/${prId}/docentes/remove/${docenteId}`, {
        method: 'POST',
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al quitar docente');
        return;
    }

    location.reload();
};

window.showEditRevisores = function showEditRevisores(documentId) {
    toggleVisibility(`revisores-list-${documentId}`, true);
    toggleVisibility(`revisores-edit-${documentId}`, false);
};

window.hideEditRevisores = function hideEditRevisores(documentId) {
    toggleVisibility(`revisores-edit-${documentId}`, true);
    toggleVisibility(`revisores-list-${documentId}`, false);
    window.hideAddRevisor(documentId);
};

window.showAddRevisor = function showAddRevisor(documentId) {
    toggleVisibility(`add-revisor-select-${documentId}`, false);
};

window.hideAddRevisor = function hideAddRevisor(documentId) {
    toggleVisibility(`add-revisor-select-${documentId}`, true);
};

window.showCreateDocumentForm = function showCreateDocumentForm() {
    toggleVisibility('create-document-form', false);
    syncCreateDocumentTemaInput();
};

window.hideCreateDocumentForm = function hideCreateDocumentForm() {
    toggleVisibility('create-document-form', true);
    const temaInput = document.getElementById('document-tema');

    if (temaInput) {
        temaInput.value = '';
    }
};

window.toggleVariants = function toggleVariants(documentId) {
    const row = document.getElementById(`variants-row-${documentId}`);
    const icon = document.getElementById(`variants-icon-${documentId}`);

    if (!row || !icon) {
        return;
    }

    row.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
};

window.addRevisor = async function addRevisor(documentId) {
    const select = document.getElementById(`add-revisor-${documentId}`);

    if (!select?.value || select.selectedOptions[0]?.disabled) {
        alert('No hay revisores disponibles para agregar');
        return;
    }

    const { response, data } = await requestJson(`/document/${documentId}/revisores/add`, {
        method: 'POST',
        body: JSON.stringify({ revisores: [select.value] }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al agregar revisor');
        return;
    }

    location.reload();
};

window.removeRevisor = async function removeRevisor(documentId, revisorId) {
    const { response, data } = await requestJson(`/document/${documentId}/revisores/remove/${revisorId}`, {
        method: 'POST',
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al quitar revisor');
        return;
    }

    location.reload();
};

window.createDocument = async function createDocument(prId) {
    const select = document.getElementById('document-type');
    const temaInput = document.getElementById('document-tema');

    if (!select?.value || select.selectedOptions[0]?.disabled) {
        alert('Selecciona un tipo de documento');
        return;
    }

    const tema = temaInput?.value === '' ? null : Number.parseInt(temaInput.value, 10);

    if (temaInput?.value !== '' && Number.isNaN(tema)) {
        alert('Introduce un numero de tema valido');
        return;
    }

    if (temaInput?.value !== '' && !typeSupportsTema(select.value)) {
        alert('El tipo de documento seleccionado no admite tema');
        return;
    }

    const { response, data } = await requestJson(`/pr/${prId}/documentos/create`, {
        method: 'POST',
        body: JSON.stringify({ type: select.value, tema: typeSupportsTema(select.value) ? tema : null }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al crear documento');
        return;
    }

    location.reload();
};

window.updateDocumentTema = async function updateDocumentTema(documentId) {
    const temaInput = document.getElementById(`tema-input-${documentId}`);
    const tema = temaInput?.value === '' ? null : Number.parseInt(temaInput.value, 10);

    if (!temaInput) {
        return;
    }

    if (temaInput.value !== '' && Number.isNaN(tema)) {
        alert('Introduce un numero de tema valido');
        return;
    }

    const { response, data } = await requestJson(`/document/${documentId}/tema/update`, {
        method: 'POST',
        body: JSON.stringify({ tema }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al cambiar el tema');
        location.reload();
        return;
    }

    location.reload();
};

window.updateDocumentPlantilla = async function updateDocumentPlantilla(documentId) {
    const plantillaId = document.getElementById(`plantilla-select-${documentId}`)?.value;

    if (!plantillaId) {
        alert('Selecciona una plantilla');
        return;
    }

    const { response, data } = await requestJson(`/document/${documentId}/plantilla/update`, {
        method: 'POST',
        body: JSON.stringify({ plantilla_id: plantillaId }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al cambiar la plantilla');
        location.reload();
        return;
    }

    location.reload();
};

window.createVariant = async function createVariant(documentId) {
    const { response, data } = await requestJson(`/document/${documentId}/variants/create`, {
        method: 'POST',
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al crear variante');
        return;
    }

    location.reload();
};

window.updateVariantStatus = async function updateVariantStatus(variantId, statusId) {
    const { response, data } = await requestJson(`/variant/${variantId}/status/update`, {
        method: 'POST',
        body: JSON.stringify({ status_id: statusId }),
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al cambiar estado');
        return;
    }

    location.reload();
};

window.removeVariant = async function removeVariant(variantId) {
    if (!confirm('¿Seguro que quieres borrar esta variante?')) {
        return;
    }

    const { response, data } = await requestJson(`/variant/${variantId}/remove`, {
        method: 'POST',
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al borrar variante');
        return;
    }

    location.reload();
};

window.removeDocument = async function removeDocument(documentId) {
    if (!confirm('¿Seguro que quieres eliminar este documento?')) {
        return;
    }

    const { response, data } = await requestJson(`/document/${documentId}/remove`, {
        method: 'POST',
    });

    if (!response.ok || !data.success) {
        alert(data.message || 'Error al eliminar documento');
        return;
    }

    location.reload();
};

document.addEventListener('click', (event) => {
    const dropdown = document.getElementById('userDropdown');
    const trigger = event.target.closest('button');

    if (!dropdown) {
        return;
    }

    if (trigger && trigger.getAttribute('onclick')?.includes('toggleUserDropdown')) {
        return;
    }

    if (!dropdown.contains(event.target) && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('[data-api-login-form]');
    const documentTypeSelect = document.getElementById('document-type');

    if (loginForm) {
        const errorBox = document.getElementById('login-error');

        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = loginForm.querySelector('button[type="submit"]');
            submitButton?.setAttribute('disabled', 'disabled');

            const { response, data } = await loginWithApi(loginForm);

            submitButton?.removeAttribute('disabled');

            if (!response.ok) {
                if (errorBox) {
                    errorBox.textContent = data.message || 'No se pudo iniciar sesión';
                    errorBox.classList.remove('hidden');
                }

                return;
            }

            // El login API responde con token; se guarda para las siguientes peticiones JSON.
            storeApiToken(data.token);

            window.location.assign('/home');
        });
    }

    if (documentTypeSelect) {
        documentTypeSelect.addEventListener('change', syncCreateDocumentTemaInput);
        syncCreateDocumentTemaInput();
    }
});

window.logoutUser = async function logoutUser() {
    const { response } = await requestJson('/logout', {
        method: 'POST',
    });

    clearApiToken();

    if (!response.ok) {
        alert('No se pudo cerrar sesión');
        return;
    }

    window.location.assign('/login');
};

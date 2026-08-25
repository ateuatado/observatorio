(function () {
    'use strict';

    document.documentElement.classList.remove('validation-no-js');
    document.documentElement.classList.add('validation-js');

    const storageKey = 'ovpdh-validation-workbook-v0.1.0';
    const form = document.getElementById('validation-form');

    if (!form) {
        return;
    }

    const panels = Array.from(form.querySelectorAll('[data-step-panel]'));
    const stepButtons = Array.from(document.querySelectorAll('[data-step-target]'));
    const progressBar = document.getElementById('validation-progress-bar');
    const saveStatus = document.getElementById('validation-save-status');
    const completion = document.getElementById('validation-completion');
    const completionDetail = document.getElementById('validation-completion-detail');
    const errorSummary = document.getElementById('validation-error-summary');
    const downloadButton = document.getElementById('validation-download');
    const printButton = document.getElementById('validation-print');
    const resetButton = document.getElementById('validation-reset');
    let activeIndex = 0;
    let saveTimer = null;

    function serializeForm() {
        const result = {};
        const data = new FormData(form);

        for (const [name, value] of data.entries()) {
            if (Object.prototype.hasOwnProperty.call(result, name)) {
                if (!Array.isArray(result[name])) {
                    result[name] = [result[name]];
                }
                result[name].push(value);
            } else {
                result[name] = value;
            }
        }

        return result;
    }

    function saveResponses() {
        const payload = {
            version: '0.1.0',
            saved_at: new Date().toISOString(),
            active_step: activeIndex,
            answers: serializeForm()
        };

        try {
            localStorage.setItem(storageKey, JSON.stringify(payload));
            saveStatus.textContent = 'Respostas salvas neste navegador.';
        } catch (error) {
            saveStatus.textContent = 'Não foi possível salvar automaticamente. Baixe ou imprima suas respostas antes de sair.';
        }
    }

    function scheduleSave() {
        saveStatus.textContent = 'Salvando…';
        window.clearTimeout(saveTimer);
        saveTimer = window.setTimeout(saveResponses, 250);
    }

    function restoreAnswers(answers) {
        Object.entries(answers || {}).forEach(([name, storedValue]) => {
            const fields = Array.from(form.elements).filter((field) => field.name === name);
            const values = Array.isArray(storedValue) ? storedValue : [storedValue];

            fields.forEach((field) => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = values.includes(field.value);
                } else {
                    field.value = storedValue;
                }
            });
        });
    }

    function restoreSavedResponses() {
        try {
            const raw = localStorage.getItem(storageKey);
            if (!raw) {
                return;
            }

            const saved = JSON.parse(raw);
            restoreAnswers(saved.answers);
            if (Number.isInteger(saved.active_step) && saved.active_step >= 0 && saved.active_step < panels.length) {
                activeIndex = saved.active_step;
            }
            saveStatus.textContent = 'Respostas anteriores recuperadas neste navegador.';
        } catch (error) {
            saveStatus.textContent = 'O salvamento anterior não pôde ser recuperado.';
        }
    }

    function requiredFieldsIn(panel) {
        return Array.from(panel.querySelectorAll('[required]'));
    }

    function invalidFieldsIn(panel) {
        return requiredFieldsIn(panel).filter((field) => !field.checkValidity());
    }

    function isPanelComplete(panel) {
        return invalidFieldsIn(panel).length === 0;
    }

    function clearInvalidState() {
        form.querySelectorAll('.validation-invalid').forEach((field) => field.classList.remove('validation-invalid'));
        errorSummary.hidden = true;
        errorSummary.innerHTML = '';
    }

    function fieldLabel(field) {
        if (field.type === 'radio') {
            const title = field.closest('.validation-question')?.querySelector('.validation-question-title');
            return title ? title.textContent.trim() : 'Pergunta obrigatória';
        }

        const label = field.id ? form.querySelector(`label[for="${CSS.escape(field.id)}"]`) : null;
        return label ? label.textContent.replace('*', '').trim() : 'Campo obrigatório';
    }

    function showErrors(invalidFields) {
        clearInvalidState();

        const unique = [];
        const seenNames = new Set();
        invalidFields.forEach((field) => {
            const key = field.name || field.id;
            if (!seenNames.has(key)) {
                seenNames.add(key);
                unique.push(field);
            }
            field.classList.add('validation-invalid');
        });

        if (unique.length === 0) {
            return true;
        }

        const heading = document.createElement('strong');
        heading.textContent = 'Revise as perguntas obrigatórias:';
        errorSummary.appendChild(heading);

        unique.forEach((field) => {
            if (!field.id) {
                field.id = `validation-required-${Math.random().toString(36).slice(2, 9)}`;
            }
            const link = document.createElement('a');
            link.href = `#${field.id}`;
            link.textContent = fieldLabel(field);
            link.addEventListener('click', () => field.focus());
            errorSummary.appendChild(link);
        });

        errorSummary.hidden = false;
        errorSummary.scrollIntoView({ behavior: 'smooth', block: 'center' });
        unique[0].focus({ preventScroll: true });
        return false;
    }

    function updateCompletion() {
        const allInvalid = panels.flatMap(invalidFieldsIn);
        const uniqueNames = new Set(allInvalid.map((field) => field.name || field.id));

        if (uniqueNames.size === 0) {
            completion.classList.add('validation-complete');
            completion.querySelector('strong').textContent = 'Caderno completo e pronto para download.';
            completionDetail.textContent = 'Revise suas respostas se desejar e encaminhe o arquivo à coordenação.';
            downloadButton.disabled = false;
        } else {
            completion.classList.remove('validation-complete');
            completion.querySelector('strong').textContent = 'Complete as perguntas obrigatórias antes de baixar.';
            completionDetail.textContent = `${uniqueNames.size} resposta(s) obrigatória(s) ainda pendente(s).`;
            downloadButton.disabled = false;
        }

        stepButtons.forEach((button, index) => {
            button.classList.toggle('validation-step-complete', isPanelComplete(panels[index]));
        });
    }

    function showStep(index, options) {
        const settings = Object.assign({ focus: true, save: true }, options || {});
        activeIndex = Math.max(0, Math.min(index, panels.length - 1));

        panels.forEach((panel, panelIndex) => {
            panel.hidden = panelIndex !== activeIndex;
        });

        stepButtons.forEach((button, buttonIndex) => {
            if (buttonIndex === activeIndex) {
                button.setAttribute('aria-current', 'step');
            } else {
                button.removeAttribute('aria-current');
            }
        });

        progressBar.style.width = `${((activeIndex + 1) / panels.length) * 100}%`;
        clearInvalidState();
        updateCompletion();

        if (settings.save) {
            scheduleSave();
        }

        if (settings.focus) {
            panels[activeIndex].querySelector('h2')?.focus({ preventScroll: true });
            panels[activeIndex].scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function validatePanel(index) {
        return showErrors(invalidFieldsIn(panels[index]));
    }

    function findFirstInvalidPanel() {
        return panels.findIndex((panel) => invalidFieldsIn(panel).length > 0);
    }

    function exportFilename() {
        const participant = form.elements.participant_code?.value.trim() || 'participante';
        const safeParticipant = participant.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-zA-Z0-9_-]+/g, '-').replace(/^-|-$/g, '').toLowerCase();
        const date = new Date().toISOString().slice(0, 10);
        return `ovpdh-validacao-${safeParticipant || 'participante'}-${date}.json`;
    }

    function downloadResponses() {
        const firstInvalidPanel = findFirstInvalidPanel();

        if (firstInvalidPanel >= 0) {
            showStep(firstInvalidPanel, { focus: false });
            showErrors(invalidFieldsIn(panels[firstInvalidPanel]));
            return;
        }

        const answers = serializeForm();
        const sensitiveSelections = ['nome_vitima', 'nome_agente', 'endereco', 'original'];
        const selectedPublicElements = Array.isArray(answers.s3_public_elements)
            ? answers.s3_public_elements
            : answers.s3_public_elements ? [answers.s3_public_elements] : [];

        const payload = {
            document: 'Caderno de validação da interface — OVPDH',
            version: '0.1.0',
            exported_at: new Date().toISOString(),
            response_id: window.crypto?.randomUUID ? window.crypto.randomUUID() : `local-${Date.now()}`,
            review_flags: {
                selected_restricted_elements_as_public: selectedPublicElements.filter((value) => sensitiveSelections.includes(value))
            },
            answers
        };

        const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = exportFilename();
        document.body.appendChild(link);
        link.click();
        link.remove();
        saveResponses();
        saveStatus.textContent = 'Arquivo de respostas baixado. Encaminhe-o à coordenação.';
        window.setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    stepButtons.forEach((button, index) => {
        button.addEventListener('click', () => showStep(index));
    });

    form.querySelectorAll('[data-next-step]').forEach((button) => {
        button.addEventListener('click', () => {
            if (validatePanel(activeIndex)) {
                showStep(activeIndex + 1);
            }
        });
    });

    form.querySelectorAll('[data-previous-step]').forEach((button) => {
        button.addEventListener('click', () => showStep(activeIndex - 1));
    });

    form.addEventListener('input', () => {
        clearInvalidState();
        updateCompletion();
        scheduleSave();
    });

    form.addEventListener('change', () => {
        clearInvalidState();
        updateCompletion();
        scheduleSave();
    });

    downloadButton.addEventListener('click', downloadResponses);
    printButton.addEventListener('click', () => window.print());

    resetButton.addEventListener('click', () => {
        const confirmed = window.confirm('Apagar todas as respostas salvas neste navegador e recomeçar?');
        if (!confirmed) {
            return;
        }

        form.reset();
        try {
            localStorage.removeItem(storageKey);
        } catch (error) {
            // The form still resets even if storage is unavailable.
        }
        saveStatus.textContent = 'Respostas apagadas neste navegador.';
        showStep(0, { save: false });
    });

    restoreSavedResponses();
    showStep(activeIndex, { focus: false, save: false });
}());

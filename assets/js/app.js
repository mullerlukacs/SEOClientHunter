/**
 * SEO Client Hunter - Vanilla JavaScript Client Application Engine
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Copy to Clipboard Utility
    document.querySelectorAll('.btn-copy').forEach(button => {
        button.addEventListener('click', function () {
            const targetSelector = this.getAttribute('data-clipboard-target');
            const targetElem = document.querySelector(targetSelector);
            if (!targetElem) return;

            const textToCopy = targetElem.innerText || targetElem.value;
            navigator.clipboard.writeText(textToCopy).then(() => {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copied!';
                this.classList.replace('btn-outline-secondary', 'btn-success');
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.replace('btn-success', 'btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                console.error('Clipboard copy failed:', err);
            });
        });
    });

    // 2. Public / Quick Audit Runner Form
    const quickAuditForm = document.getElementById('quickAuditForm');
    const auditResultsContainer = document.getElementById('auditResultsContainer');
    const auditLoadingSpinner = document.getElementById('auditLoadingSpinner');

    if (quickAuditForm) {
        quickAuditForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const urlInput = document.getElementById('auditUrlInput');
            const url = urlInput ? urlInput.value.trim() : '';
            if (!url) return;

            if (auditLoadingSpinner) auditLoadingSpinner.classList.remove('d-none');
            if (auditResultsContainer) auditResultsContainer.classList.add('d-none');

            fetch('/api/audit?url=' + encodeURIComponent(url))
                .then(res => res.json())
                .then(data => {
                    if (auditLoadingSpinner) auditLoadingSpinner.classList.add('d-none');
                    if (data.success && auditResultsContainer) {
                        auditResultsContainer.classList.remove('d-none');
                        renderAuditResults(data);
                    } else {
                        alert(data.error || 'Failed to complete website audit.');
                    }
                })
                .catch(err => {
                    if (auditLoadingSpinner) auditLoadingSpinner.classList.add('d-none');
                    console.error('Audit request error:', err);
                    alert('Network error connecting to audit service.');
                });
        });
    }

    function renderAuditResults(data) {
        const scoreElem = document.getElementById('auditScoreValue');
        const urlElem = document.getElementById('auditDomainValue');
        const issuesContainer = document.getElementById('auditIssuesList');

        if (scoreElem) scoreElem.textContent = data.seo_score + '/100';
        if (urlElem) urlElem.textContent = data.domain || data.url;

        // Scores breakdown
        ['technical', 'onpage', 'content', 'local', 'authority', 'social'].forEach(cat => {
            const bar = document.getElementById('scoreBar_' + cat);
            const val = document.getElementById('scoreVal_' + cat);
            const score = data.scores[cat] || 0;
            if (bar) bar.style.width = score + '%';
            if (val) val.textContent = score + '%';
        });

        // Issues
        if (issuesContainer && data.issues) {
            issuesContainer.innerHTML = '';
            data.issues.forEach(iss => {
                const sevClass = 'issue-' + (iss.severity ? iss.severity.toLowerCase() : 'medium');
                const badgeClass = iss.severity === 'Critical' ? 'bg-danger' :
                                   iss.severity === 'High' ? 'bg-warning text-dark' :
                                   iss.severity === 'Medium' ? 'bg-info text-dark' :
                                   iss.severity === 'Passed' ? 'bg-success' : 'bg-secondary';

                const card = document.createElement('div');
                card.className = `issue-card ${sevClass}`;
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge ${badgeClass} text-uppercase">${iss.severity}</span>
                        <small class="text-muted fw-bold">${iss.category}</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">${iss.title}</h6>
                    <p class="text-muted small mb-2">${iss.explanation}</p>
                    <div class="p-2 bg-light rounded text-secondary small">
                        <strong><i class="fa-solid fa-wrench me-1 text-primary"></i>Fix Recommendation:</strong> ${iss.recommendation}
                    </div>
                `;
                issuesContainer.appendChild(card);
            });
        }
    }

    // 3. Lead Hunter Search Form (User Dashboard)
    const leadSearchForm = document.getElementById('leadSearchForm');
    const searchLoadingState = document.getElementById('searchLoadingState');
    const searchResultsBox = document.getElementById('searchResultsBox');

    if (leadSearchForm) {
        leadSearchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            if (searchLoadingState) searchLoadingState.classList.remove('d-none');
            if (searchResultsBox) searchResultsBox.classList.add('d-none');

            fetch('/api/search', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (searchLoadingState) searchLoadingState.classList.add('d-none');
                if (data.success) {
                    window.location.href = '/leads?notice=search_completed';
                } else {
                    alert(data.error || 'Search encountered an error.');
                }
            })
            .catch(err => {
                if (searchLoadingState) searchLoadingState.classList.add('d-none');
                console.error('Search error:', err);
                alert('Failed to connect to lead hunter engine.');
            });
        });
    }

    // 4. AI Pitch Generator Trigger (Lead Detail View)
    const btnGenerateAiPitch = document.getElementById('btnGenerateAiPitch');
    const aiLoadingBox = document.getElementById('aiLoadingBox');
    const aiResultsBox = document.getElementById('aiResultsBox');

    if (btnGenerateAiPitch) {
        btnGenerateAiPitch.addEventListener('click', function () {
            const leadId = this.getAttribute('data-lead-id');
            if (!leadId) return;

            if (aiLoadingBox) aiLoadingBox.classList.remove('d-none');
            if (aiResultsBox) aiResultsBox.classList.add('d-none');

            const formData = new FormData();
            formData.append('lead_id', leadId);

            fetch('/api/ai-pitch', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (aiLoadingBox) aiLoadingBox.classList.add('d-none');
                if (data.success && data.data) {
                    if (aiResultsBox) aiResultsBox.classList.remove('d-none');
                    renderAiPitches(data.data);
                } else {
                    alert(data.error || 'Failed to generate AI pitch.');
                }
            })
            .catch(err => {
                if (aiLoadingBox) aiLoadingBox.classList.add('d-none');
                console.error('AI Pitch error:', err);
                alert('Error generating outreach messages.');
            });
        });
    }

    function renderAiPitches(ai) {
        // Summaries
        const sumElem = document.getElementById('aiBusinessSummary');
        const probElem = document.getElementById('aiSeoProblems');
        const oppElem = document.getElementById('aiBusinessOpp');
        const srvElem = document.getElementById('aiSuggestedService');

        if (sumElem) sumElem.textContent = ai.business_summary || '';
        if (probElem) probElem.textContent = ai.seo_problems || '';
        if (oppElem) oppElem.textContent = ai.business_opportunity || '';
        if (srvElem) srvElem.textContent = ai.suggested_service || '';

        // Cold Email
        const emailSub = document.getElementById('aiEmailSubject');
        const emailBody = document.getElementById('aiEmailBody');
        if (emailSub && ai.cold_email) emailSub.value = ai.cold_email.subject || '';
        if (emailBody && ai.cold_email) emailBody.textContent = ai.cold_email.body || '';

        // LinkedIn
        const liBody = document.getElementById('aiLinkedInBody');
        if (liBody && ai.linkedin_message) liBody.textContent = ai.linkedin_message.body || '';

        // Contact Form
        const cfBody = document.getElementById('aiContactFormBody');
        if (cfBody && ai.contact_form_pitch) cfBody.textContent = ai.contact_form_pitch.body || '';

        // Short Pitch
        const spBody = document.getElementById('aiShortPitchBody');
        if (spBody && ai.short_pitch) spBody.textContent = ai.short_pitch.body || '';

        // Follow ups
        if (ai.followups && Array.isArray(ai.followups)) {
            ai.followups.forEach((f, idx) => {
                const elem = document.getElementById(`aiFollowupBody_${idx + 1}`);
                if (elem) elem.textContent = f.body || '';
            });
        }
    }

    // 5. CRM Lead Status Update Dropdown
    document.querySelectorAll('.select-lead-status').forEach(select => {
        select.addEventListener('change', function () {
            const leadId = this.getAttribute('data-lead-id');
            const newStatus = this.value;

            const formData = new FormData();
            formData.append('lead_id', leadId);
            formData.append('status', newStatus);

            fetch('/api/lead/status', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert('Could not update lead status.');
                }
            })
            .catch(err => console.error('Status update failed:', err));
        });
    });

    // 6. CRM Add Note Form
    const addNoteForm = document.getElementById('addNoteForm');
    if (addNoteForm) {
        addNoteForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const content = formData.get('note');
            if (!content) return;

            fetch('/api/lead/note', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to save note.');
                }
            })
            .catch(err => console.error('Note add error:', err));
        });
    }

    // 7. CRM Add Task Form
    const addTaskForm = document.getElementById('addTaskForm');
    if (addTaskForm) {
        addTaskForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/api/lead/task', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to schedule task.');
                }
            })
            .catch(err => console.error('Task add error:', err));
        });
    }

    // 8. CRM Toggle Task Checkbox
    document.querySelectorAll('.task-checkbox').forEach(box => {
        box.addEventListener('change', function () {
            const taskId = this.getAttribute('data-task-id');
            const formData = new FormData();
            formData.append('toggle_task_id', taskId);

            fetch('/api/lead/task', {
                method: 'POST',
                body: formData
            }).then(() => {
                const label = document.getElementById('taskLabel_' + taskId);
                if (label) {
                    if (this.checked) {
                        label.classList.add('text-decoration-line-through', 'text-muted');
                    } else {
                        label.classList.remove('text-decoration-line-through', 'text-muted');
                    }
                }
            });
        });
    });

    // 9. Lead Archive & Delete confirmation
    document.querySelectorAll('.btn-archive-lead').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Archive this lead? It can be restored from the filter anytime.')) return;
            const leadId = this.getAttribute('data-lead-id');
            const formData = new FormData();
            formData.append('lead_id', leadId);

            fetch('/api/lead/archive', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) window.location.href = '/leads';
                });
        });
    });

    document.querySelectorAll('.btn-delete-lead').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Permanently delete this lead and its audit history? This cannot be undone.')) return;
            const leadId = this.getAttribute('data-lead-id');
            const formData = new FormData();
            formData.append('lead_id', leadId);

            fetch('/api/lead/delete', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) window.location.href = '/leads';
                });
        });
    });
});

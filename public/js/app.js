/**
 * ═══════════════════════════════════════════════════════════
 * TerrenoSur - JavaScript del Cliente (Premium Edition)
 * Drag & drop, preview, modales, sidebar, UI interactions
 * ═══════════════════════════════════════════════════════════
 */

document.addEventListener('DOMContentLoaded', function() {

    // ─── Counter Up Animation para Stats ───
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0) {
        statNumbers.forEach(el => {
            const finalValue = parseInt(el.textContent, 10);
            if (isNaN(finalValue)) return;
            
            el.textContent = '0';
            let current = 0;
            const duration = 1500; // ms
            const stepTime = 30;
            const steps = duration / stepTime;
            const inc = finalValue / steps;
            
            const timer = setInterval(() => {
                current += inc;
                if (current >= finalValue) {
                    el.textContent = finalValue;
                    clearInterval(timer);
                    // Add a tiny pop animation when finished
                    el.style.transform = 'scale(1.2)';
                    el.style.color = 'var(--primary)';
                    setTimeout(() => {
                        el.style.transform = 'scale(1)';
                        el.style.color = '';
                    }, 200);
                } else {
                    el.textContent = Math.floor(current);
                }
            }, stepTime);
        });
    }

    // ─── Search en tiempo real (Panel) ───
    const searchInput = document.getElementById('searchVendor');
    const vendorsTable = document.getElementById('vendorsTable');
    if (searchInput && vendorsTable) {
        const rows = vendorsTable.querySelectorAll('tbody tr');
        
        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            let count = 0;
            
            rows.forEach(row => {
                const name = row.querySelector('.user-name-text').textContent.toLowerCase();
                const email = row.querySelector('.user-email-text').textContent.toLowerCase();
                
                if (name.includes(term) || email.includes(term)) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show empty state if no results
            let emptyRow = document.getElementById('tableEmptyRow');
            if (count === 0 && term !== '') {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.id = 'tableEmptyRow';
                    emptyRow.innerHTML = `<td colspan="6" style="text-align:center; padding:40px; color:var(--gray-500);">No hay resultados para "${term}"</td>`;
                    vendorsTable.querySelector('tbody').appendChild(emptyRow);
                } else {
                    emptyRow.style.display = '';
                    emptyRow.innerHTML = `<td colspan="6" style="text-align:center; padding:40px; color:var(--gray-500);">No hay resultados para "${term}"</td>`;
                }
            } else if (emptyRow) {
                emptyRow.style.display = 'none';
            }
        });
    }

    // ─── Auto-dismiss alerts after 6 seconds ───
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease, height 0.4s ease, margin 0.4s ease, padding 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px) scale(0.9)';
            alert.style.height = '0px';
            alert.style.margin = '0px';
            alert.style.padding = '0px';
            setTimeout(function() { alert.remove(); }, 400);
        }, 6000);
    });

    // ─── Password toggle (Login page) ───
    var passwordToggle = document.getElementById('passwordToggle');
    if (passwordToggle) {
        passwordToggle.addEventListener('click', function(e) {
            e.preventDefault();
            var input = document.getElementById('password');
            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
            } else {
                input.type = 'password';
                this.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            }
        });
    }

    // ─── Sidebar toggle (mobile) ───
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        var overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.id = 'sidebarOverlay';
        overlay.style.display = 'none';
        document.body.appendChild(overlay);

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.style.display = 'none';
        });
    }

    // ═══════════════════════════════════════════════════════
    // DROPZONE - File Upload with Drag & Drop
    // ═══════════════════════════════════════════════════════
    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('fileInput');
    var dropzoneContent = document.getElementById('dropzoneContent');
    var dropzonePreview = document.getElementById('dropzonePreview');
    var previewImage = document.getElementById('previewImage');
    var previewPdf = document.getElementById('previewPdf');
    var pdfFileName = document.getElementById('pdfFileName');
    var previewRemove = document.getElementById('previewRemove');
    var uploadBtn = document.getElementById('uploadBtn');

    if (dropzone && fileInput) {
        var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
        var MAX_SIZE = 5 * 1024 * 1024;

        dropzone.addEventListener('click', function(e) {
            if (e.target === previewRemove || previewRemove?.contains(e.target)) return;
            fileInput.click();
        });

        ['dragenter', 'dragover'].forEach(function(evt) {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault(); e.stopPropagation();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function(evt) {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault(); e.stopPropagation();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', function(e) {
            var files = e.dataTransfer.files;
            if (files.length > 0) handleFile(files[0]);
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) handleFile(this.files[0]);
        });

        if (previewRemove) {
            previewRemove.addEventListener('click', function(e) {
                e.stopPropagation();
                resetDropzone();
            });
        }

        function handleFile(file) {
            if (!ALLOWED_TYPES.includes(file.type)) {
                showError('Solo JPG, PNG o PDF.');
                return;
            }
            if (file.size > MAX_SIZE) {
                showError('Máximo 5MB.');
                return;
            }

            var dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            dropzoneContent.style.display = 'none';
            dropzonePreview.style.display = 'block';

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewPdf.style.display = 'none';
                    // Apply nice animation
                    previewImage.style.animation = 'popInModal 0.4s ease';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = 'none';
                previewPdf.style.display = 'block';
                pdfFileName.textContent = file.name;
                previewPdf.style.animation = 'popInModal 0.4s ease';
            }

            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.classList.add('btn-primary');
                uploadBtn.classList.remove('btn-secondary');
            }
        }

        function resetDropzone() {
            fileInput.value = '';
            dropzoneContent.style.display = 'block';
            dropzonePreview.style.display = 'none';
            if (uploadBtn) {
                uploadBtn.disabled = true;
                uploadBtn.classList.remove('btn-primary');
                uploadBtn.classList.add('btn-secondary');
            }
        }

        function showError(msg) {
            alert(msg); // simple alert for now
        }
    }
    
    // Animate submit buttons
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.hasAttribute('data-no-loading')) {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<svg class="spinner" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="animation: spin 1s linear infinite;"><path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/><path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" fill="currentColor"/></svg> Procesando...';
                btn.style.opacity = '0.8';
                // Don't disable immediately to allow form to submit
                setTimeout(() => btn.disabled = true, 10);
            }
        });
    });
});

// Modal functions
function mostrarModal(id) {
    const modal = document.getElementById(id);
    if(modal) modal.style.display = 'flex';
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    if(modal) modal.style.display = 'none';
}

function confirmarAccion(userId, accion, nombre) {
    if (accion === 'aprobado') {
        document.getElementById('aprobarUsuarioId').value = userId;
        document.getElementById('aprobarNombre').textContent = nombre;
        mostrarModal('modalAprobacion');
    }
}

function mostrarModalRechazo(userId, nombre) {
    document.getElementById('rechazarUsuarioId').value = userId;
    document.getElementById('rechazarNombre').textContent = nombre;
    document.getElementById('comentarioRechazo').value = '';
    mostrarModal('modalRechazo');
    setTimeout(() => document.getElementById('comentarioRechazo').focus(), 100);
}

// Close on escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});
// Close on click outside
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) e.target.style.display = 'none';
});

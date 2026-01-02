@extends('layouts.app')

@section('title', 'Paramètres')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Paramètres de l'application</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Company Name -->
                    <div class="mb-4">
                        <label for="company_name" class="form-label">Nom de l'entreprise</label>
                        <input 
                            type="text" 
                            class="form-control @error('company_name') is-invalid @enderror" 
                            id="company_name" 
                            name="company_name" 
                            value="{{ old('company_name', $settings['company_name']->value ?? config('app.name')) }}"
                            placeholder="Entrez le nom de votre entreprise"
                        >
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Ce nom apparaîtra dans tout le système</small>
                    </div>

                    <!-- Company Logo -->
                    <div class="mb-4">
                        <label for="logo" class="form-label">Logo de l'entreprise</label>
                        
                        @if(isset($settings['company_logo']) && $settings['company_logo']->value)
                            <div class="mb-3">
                                <img 
                                    src="{{ asset('storage/' . $settings['company_logo']->value) }}" 
                                    alt="Logo actuel" 
                                    style="max-height: 100px; max-width: 200px;"
                                    class="border rounded p-2"
                                >
                                <p class="text-muted small mt-2">Logo actuel</p>
                            </div>
                        @endif

                        <input 
                            type="file" 
                            class="form-control @error('logo') is-invalid @enderror" 
                            id="logo" 
                            name="logo"
                            accept="image/*"
                        >
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF, SVG (max 2MB)</small>
                    </div>

                    <!-- Primary Color -->
                    <div class="mb-4">
                        <label for="primary_color" class="form-label">Couleur principale</label>
                        <div class="input-group">
                            <input 
                                type="color" 
                                class="form-control form-control-color @error('primary_color') is-invalid @enderror" 
                                id="primary_color" 
                                name="primary_color" 
                                value="{{ old('primary_color', $settings['primary_color']->value ?? '#696cff') }}"
                                title="Choisir une couleur"
                            >
                            <input 
                                type="text" 
                                class="form-control" 
                                id="primary_color_text"
                                value="{{ old('primary_color', $settings['primary_color']->value ?? '#696cff') }}"
                                placeholder="#696cff"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                maxlength="7"
                            >
                        </div>
                        @error('primary_color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Cette couleur sera utilisée pour les éléments principaux (boutons, liens, etc.)</small>
                    </div>

                    <!-- Secondary Color -->
                    <div class="mb-4">
                        <label for="secondary_color" class="form-label">Couleur secondaire</label>
                        <div class="input-group">
                            <input 
                                type="color" 
                                class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                id="secondary_color" 
                                name="secondary_color" 
                                value="{{ old('secondary_color', $settings['secondary_color']->value ?? '#8592a3') }}"
                                title="Choisir une couleur"
                            >
                            <input 
                                type="text" 
                                class="form-control" 
                                id="secondary_color_text"
                                value="{{ old('secondary_color', $settings['secondary_color']->value ?? '#8592a3') }}"
                                placeholder="#8592a3"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                maxlength="7"
                            >
                        </div>
                        @error('secondary_color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Cette couleur sera utilisée pour les éléments secondaires</small>
                    </div>

                    <!-- Sidebar Text Color -->
                    <div class="mb-4">
                        <label for="sidebar_text_color" class="form-label">Couleur du texte du menu (Sidebar)</label>
                        <div class="input-group">
                            <input 
                                type="color" 
                                class="form-control form-control-color @error('sidebar_text_color') is-invalid @enderror" 
                                id="sidebar_text_color" 
                                name="sidebar_text_color" 
                                value="{{ old('sidebar_text_color', isset($settings['sidebar_text_color']) ? $settings['sidebar_text_color']->value : '#697a8d') }}"
                                title="Choisir une couleur"
                            >
                            <input 
                                type="text" 
                                class="form-control" 
                                id="sidebar_text_color_text"
                                value="{{ old('sidebar_text_color', isset($settings['sidebar_text_color']) ? $settings['sidebar_text_color']->value : '#697a8d') }}"
                                placeholder="#697a8d"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                maxlength="7"
                            >
                        </div>
                        @error('sidebar_text_color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Couleur du texte dans le menu latéral (sidebar). Choisissez une couleur qui contraste bien avec le fond pour une meilleure lisibilité.</small>
                    </div>

                    <!-- Sidebar Active Background Color -->
                    <div class="mb-4">
                        <label for="sidebar_active_bg_color" class="form-label">Couleur de fond de l'élément actif du menu</label>
                        <div class="input-group">
                            <input 
                                type="color" 
                                class="form-control form-control-color @error('sidebar_active_bg_color') is-invalid @enderror" 
                                id="sidebar_active_bg_color" 
                                name="sidebar_active_bg_color" 
                                value="{{ old('sidebar_active_bg_color', isset($settings['sidebar_active_bg_color']) ? $settings['sidebar_active_bg_color']->value : '#696cff') }}"
                                title="Choisir une couleur"
                            >
                            <input 
                                type="text" 
                                class="form-control" 
                                id="sidebar_active_bg_color_text"
                                value="{{ old('sidebar_active_bg_color', isset($settings['sidebar_active_bg_color']) ? $settings['sidebar_active_bg_color']->value : '#696cff') }}"
                                placeholder="#696cff"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                maxlength="7"
                            >
                        </div>
                        @error('sidebar_active_bg_color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Couleur de fond quand un élément du menu est actif/sélectionné.</small>
                    </div>

                    <!-- Sidebar Active Text Color -->
                    <div class="mb-4">
                        <label for="sidebar_active_text_color" class="form-label">Couleur du texte de l'élément actif du menu</label>
                        <div class="input-group">
                            <input 
                                type="color" 
                                class="form-control form-control-color @error('sidebar_active_text_color') is-invalid @enderror" 
                                id="sidebar_active_text_color" 
                                name="sidebar_active_text_color" 
                                value="{{ old('sidebar_active_text_color', isset($settings['sidebar_active_text_color']) ? $settings['sidebar_active_text_color']->value : '#ffffff') }}"
                                title="Choisir une couleur"
                            >
                            <input 
                                type="text" 
                                class="form-control" 
                                id="sidebar_active_text_color_text"
                                value="{{ old('sidebar_active_text_color', isset($settings['sidebar_active_text_color']) ? $settings['sidebar_active_text_color']->value : '#ffffff') }}"
                                placeholder="#ffffff"
                                pattern="^#[0-9A-Fa-f]{6}$"
                                maxlength="7"
                            >
                        </div>
                        @error('sidebar_active_text_color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Couleur du texte quand un élément du menu est actif/sélectionné. Choisissez une couleur qui contraste bien avec la couleur de fond active.</small>
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-4">
                        <label class="form-label">Aperçu des couleurs</label>
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn me-2" id="preview-primary" style="background-color: {{ $settings['primary_color']->value ?? '#696cff' }}; color: white;">
                                    Bouton Principal
                                </button>
                                <button type="button" class="btn" id="preview-secondary" style="background-color: {{ $settings['secondary_color']->value ?? '#8592a3' }}; color: white;">
                                    Bouton Secondaire
                                </button>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-body">
                                <label class="form-label small">Aperçu du texte du menu :</label>
                                <div class="p-3" style="background-color: #f5f5f9; border-radius: 4px;">
                                    <div id="preview-sidebar-text" style="color: {{ isset($settings['sidebar_text_color']) ? $settings['sidebar_text_color']->value : '#697a8d' }}; font-size: 14px; font-weight: 500; margin-bottom: 8px;">
                                        <i class="bx bx-home-circle me-2"></i> Exemple de texte du menu (normal)
                                    </div>
                                    <div id="preview-sidebar-active" style="background-color: {{ isset($settings['sidebar_active_bg_color']) ? $settings['sidebar_active_bg_color']->value : '#696cff' }}; color: {{ isset($settings['sidebar_active_text_color']) ? $settings['sidebar_active_text_color']->value : '#ffffff' }}; font-size: 14px; font-weight: 500; padding: 8px; border-radius: 4px;">
                                        <i class="bx bx-home-circle me-2"></i> Exemple de texte du menu (actif)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>
                            Enregistrer les paramètres
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="bx bx-x me-1"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script>
    // Sync color picker with text input
    document.getElementById('primary_color').addEventListener('input', function(e) {
        document.getElementById('primary_color_text').value = e.target.value;
        document.getElementById('preview-primary').style.backgroundColor = e.target.value;
    });

    document.getElementById('primary_color_text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('primary_color').value = e.target.value;
            document.getElementById('preview-primary').style.backgroundColor = e.target.value;
        }
    });

    document.getElementById('secondary_color').addEventListener('input', function(e) {
        document.getElementById('secondary_color_text').value = e.target.value;
        document.getElementById('preview-secondary').style.backgroundColor = e.target.value;
    });

    document.getElementById('secondary_color_text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('secondary_color').value = e.target.value;
            document.getElementById('preview-secondary').style.backgroundColor = e.target.value;
        }
    });

    // Sidebar text color sync
    document.getElementById('sidebar_text_color').addEventListener('input', function(e) {
        document.getElementById('sidebar_text_color_text').value = e.target.value;
        document.getElementById('preview-sidebar-text').style.color = e.target.value;
    });

    document.getElementById('sidebar_text_color_text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('sidebar_text_color').value = e.target.value;
            document.getElementById('preview-sidebar-text').style.color = e.target.value;
        }
    });

    // Sidebar active background color sync
    document.getElementById('sidebar_active_bg_color').addEventListener('input', function(e) {
        document.getElementById('sidebar_active_bg_color_text').value = e.target.value;
        document.getElementById('preview-sidebar-active').style.backgroundColor = e.target.value;
    });

    document.getElementById('sidebar_active_bg_color_text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('sidebar_active_bg_color').value = e.target.value;
            document.getElementById('preview-sidebar-active').style.backgroundColor = e.target.value;
        }
    });

    // Sidebar active text color sync
    document.getElementById('sidebar_active_text_color').addEventListener('input', function(e) {
        document.getElementById('sidebar_active_text_color_text').value = e.target.value;
        document.getElementById('preview-sidebar-active').style.color = e.target.value;
    });

    document.getElementById('sidebar_active_text_color_text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('sidebar_active_text_color').value = e.target.value;
            document.getElementById('preview-sidebar-active').style.color = e.target.value;
        }
    });
</script>
@endpush
@endsection


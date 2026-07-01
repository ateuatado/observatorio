<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/agressores') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <h1 class="page-title-admin mt-2">Vincular <span>Agressor</span></h1>
    <p class="text-muted mb-0">Adicione os dados do agressor/agente público envolvido no caso.</p>
</div>

<form method="POST" action="<?= base_url('painel/agressores/novo') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Vinculação e Corporação</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="ocorrencia_id">Ocorrência Relacionada <span class="required-star">*</span></label>
                <select name="ocorrencia_id" id="ocorrencia_id" class="form-select" required>
                    <option value="">Selecione a ocorrência...</option>
                    <?php foreach ($ocorrencias as $o): ?>
                    <option value="<?= $o['id'] ?>">(#<?= $o['id'] ?>) <?= esc($o['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tipo_agente">Tipo de Agente</label>
                <select name="tipo_agente" id="tipo_agente" class="form-select">
                    <option value="Policial Militar">Policial Militar</option>
                    <option value="Policial Civil">Policial Civil</option>
                    <option value="Guarda Municipal">Guarda Municipal</option>
                    <option value="Agente Penitenciário">Agente Penitenciário/Policial Penal</option>
                    <option value="Policial Federal">Policial Federal</option>
                    <option value="Agente não identificado">Agente não identificado</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="orgao">Órgão / Corporação</label>
                <input type="text" name="orgao" id="orgao" class="form-control" placeholder="Ex: PMMG, PCMG, GM-BH">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title"><span>2</span> Identificação e Patente</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="batalhao">Batalhão / Unidade</label>
                <input type="text" name="batalhao" id="batalhao" class="form-control" placeholder="Ex: 34º Batalhão, ROTAM">
            </div>
            <div class="col-md-4">
                <label for="posto">Posto / Patente / Cargo</label>
                <input type="text" name="posto" id="posto" class="form-control" placeholder="Ex: Cabo, Soldado, Delegado">
            </div>
            <div class="col-md-4">
                <label for="identificacao">Identificação (Nome de guerra, nº farda)</label>
                <input type="text" name="identificacao" id="identificacao" class="form-control" placeholder="Ex: Farda 12.345, Soldado Silva">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="identificado" id="identificado" value="1" class="form-check-input">
                    <label class="form-check-label" for="identificado">O agente foi plenamente identificado?</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title"><span>3</span> Observações Complementares</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="observacoes">Observações sobre o Agressor</label>
                <textarea name="observacoes" id="observacoes" class="form-control" rows="4" placeholder="Descreva traços físicos, conduta específica ou detalhes adicionais..."></textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar Agressor</button>
        <a href="<?= base_url('painel/agressores') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>

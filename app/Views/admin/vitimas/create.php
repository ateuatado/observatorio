<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/vitimas') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <h1 class="page-title-admin mt-2">Vincular <span>Vítima</span></h1>
    <p class="text-muted mb-0">Adicione os dados da vítima à ocorrência selecionada.</p>
</div>

<form method="POST" action="<?= base_url('painel/vitimas/nova') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Vinculação e Identificação</h2>
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
            <div class="col-md-9">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-control" placeholder="Insira o nome (se conhecido)" value="">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input type="checkbox" name="anonimo" id="anonimo" value="1" class="form-check-input">
                    <label class="form-check-label" for="anonimo">Manter Anônimo</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title"><span>2</span> Características Demográficas</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <label for="idade">Idade</label>
                <input type="number" name="idade" id="idade" class="form-control" placeholder="Ex: 25">
            </div>
            <div class="col-md-3">
                <label for="genero">Gênero</label>
                <select name="genero" id="genero" class="form-select">
                    <option value="">Selecione...</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                    <option value="Não-Binário">Não-Binário</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="raca_etnia">Raça/Etnia</label>
                <select name="raca_etnia" id="raca_etnia" class="form-select">
                    <option value="">Selecione...</option>
                    <option value="Branca">Branca</option>
                    <option value="Preta">Preta</option>
                    <option value="Parda">Parda</option>
                    <option value="Amarela">Amarela</option>
                    <option value="Indígena">Indígena</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="desfecho">Desfecho do Caso</label>
                <select name="desfecho" id="desfecho" class="form-select">
                    <option value="Sobrevivente">Sobrevivente</option>
                    <option value="Hospitalizado">Hospitalizado</option>
                    <option value="Óbito">Óbito</option>
                    <option value="Desaparecido">Desaparecido</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title"><span>3</span> Relato e Perfil Adicional</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="condicao_social">Condição Social Estimada</label>
                <input type="text" name="condicao_social" id="condicao_social" class="form-control" placeholder="Ex: Baixa renda, Sem teto">
            </div>
            <div class="col-md-6">
                <label for="profissao">Profissão / Ocupação</label>
                <input type="text" name="profissao" id="profissao" class="form-control" placeholder="Ex: Ambulante, Estudante">
            </div>
            <div class="col-12">
                <label for="relato">Relato ou Observações da Vítima</label>
                <textarea name="relato" id="relato" class="form-control" rows="4" placeholder="Insira o depoimento pessoal da vítima ou observações relevantes..."></textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar Vítima</button>
        <a href="<?= base_url('painel/vitimas') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>

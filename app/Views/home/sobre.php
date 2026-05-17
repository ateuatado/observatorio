<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section class="ovp-section">
    <div class="container" style="max-width:760px;">
        <h1 class="ovp-section-title">Sobre o OVP-SP</h1>
        <span class="ovp-divider"></span>

        <p class="lead text-muted mb-4">
            O <strong>Observatório de Violências Policiais de São Paulo (OVP-SP)</strong> foi criado em 1999 com o objetivo de documentar e sistematizar os dados sobre violência policial no Estado de São Paulo.
        </p>

        <p>Em 2006, o OVP-SP foi integrado ao <strong>Centro de Estudos de História da América Latina (CEHAL)</strong> da Pontifícia Universidade Católica de São Paulo (PUC-SP), dentro do Núcleo Trabalho, Ideologia e Poder.</p>

        <p>Seu acervo reúne:</p>
        <ul>
            <li>Casos individuais de execução, tortura, abuso de poder e mortes em custódia</li>
            <li>Registros de chacinas e eventos coletivos</li>
            <li>Notícias e clippings jornalísticos</li>
            <li>Relatórios de organizações de direitos humanos (Anistia Internacional, ONU, OEA)</li>
            <li>Artigos acadêmicos e entrevistas com especialistas</li>
            <li>Legislação e documentação jurídica relevante</li>
        </ul>

        <p>O sistema de registro e análise do OVP-SP contribui para a produção de trabalhos acadêmicos sobre segurança pública, direitos humanos e violência de Estado no Brasil.</p>

        <div class="mt-4">
            <a href="<?= base_url('ocorrencias') ?>" class="btn-ovp me-2">
                <i class="bi bi-folder2-open me-2"></i>Ver ocorr&ecirc;ncias documentadas
            </a>
            <a href="<?= base_url('estudos') ?>" class="btn-ovp-outline">
                <i class="bi bi-journal-text me-2"></i>Publicações
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

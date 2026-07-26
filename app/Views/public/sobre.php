<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section class="sobre-hero">
    <div class="container">
        <div class="section-label" style="color:var(--ovpdh-vermelho-claro);"><i class="bi bi-building"></i> Institucional</div>
        <h1 class="section-title" style="color:var(--ovpdh-branco);">Sobre o OVPDH</h1>
        <p style="color:rgba(255,255,255,.6); max-width:650px; font-size:.95rem; line-height:1.7;">
            O Observatório de Violência Policial e Direitos Humanos (OVPDH) é um projeto acadêmico vinculado ao Programa de Pós-Graduação em Ciências Sociais da PUC São Paulo, fundado pela Profa. Dra. Helena Ferreira Campos.
        </p>
    </div>
</section>

<!-- Missão Visão Valores -->
<section style="padding:5rem 0; background:var(--ovpdh-branco);">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div style="padding:2rem; border-radius:12px; background:var(--ovpdh-preto); height:100%;">
                    <div class="feature-icon-box mb-3"><i class="bi bi-bullseye"></i></div>
                    <h2 style="font-size:1.1rem; font-weight:800; color:white; margin-bottom:.75rem;">Missão</h2>
                    <p style="color:rgba(255,255,255,.6); font-size:.875rem; line-height:1.7;">Documentar, pesquisar e denunciar casos de violência policial e violações de direitos humanos em Minas Gerais, produzindo conhecimento científico para subsidiar políticas públicas e ações de incidência.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div style="padding:2rem; border-radius:12px; background:var(--ovpdh-vermelho); height:100%;">
                    <div style="width:56px; height:56px; border-radius:10px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:white; margin-bottom:1rem;"><i class="bi bi-eye"></i></div>
                    <h2 style="font-size:1.1rem; font-weight:800; color:white; margin-bottom:.75rem;">Visão</h2>
                    <p style="color:rgba(255,255,255,.75); font-size:.875rem; line-height:1.7;">Ser referência nacional na produção de dados e análises sobre violência policial, contribuindo para a construção de um sistema de segurança pública comprometido com os direitos humanos e a democracia.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div style="padding:2rem; border-radius:12px; background:var(--ovpdh-cinza-ultra); border:1px solid var(--ovpdh-cinza-claro); height:100%;">
                    <div style="width:56px; height:56px; border-radius:10px; background:rgba(192,39,45,.1); display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:var(--ovpdh-vermelho); margin-bottom:1rem;"><i class="bi bi-heart"></i></div>
                    <h2 style="font-size:1.1rem; font-weight:800; color:var(--ovpdh-preto); margin-bottom:.75rem;">Valores</h2>
                    <ul style="color:var(--ovpdh-cinza-escuro); font-size:.875rem; line-height:1.8; padding-left:1.2rem;">
                        <li>Rigor científico e ética na pesquisa</li>
                        <li>Compromisso com as vítimas e familiares</li>
                        <li>Transparência e independência</li>
                        <li>Interseccionalidade e antirracismo</li>
                        <li>Democracia e Estado de Direito</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Equipe -->
<section style="padding:5rem 0; background:var(--ovpdh-cinza-ultra);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label justify-content-center"><i class="bi bi-people"></i> Equipe</div>
            <h2 class="section-title" style="text-align:center;">Quem Somos</h2>
            <p class="section-subtitle" style="margin:0 auto; text-align:center;">Pesquisadores, voluntários e parceiros comprometidos com os direitos humanos.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="team-card">
                    <div class="team-avatar">H</div>
                    <div class="fw-bold" style="font-size:.95rem;">Profa. Dra. Helena Ferreira Campos</div>
                    <div style="font-size:.78rem; color:var(--ovpdh-vermelho); font-weight:600; margin:.25rem 0;">Fundadora e Pesquisadora Principal</div>
                    <div style="font-size:.825rem; color:var(--ovpdh-cinza); line-height:1.6;">Socióloga, doutora em Ciências Sociais pela UFMG. Pesquisadora há mais de 30 anos sobre violência de Estado e direitos humanos.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-card">
                    <div class="team-avatar">A</div>
                    <div class="fw-bold" style="font-size:.95rem;">Profa. Dra. Ana Lúcia Borges</div>
                    <div style="font-size:.78rem; color:var(--ovpdh-vermelho); font-weight:600; margin:.25rem 0;">Coordenadora Acadêmica</div>
                    <div style="font-size:.825rem; color:var(--ovpdh-cinza); line-height:1.6;">Pesquisadora em segurança pública, política criminal e interseccionalidade. Coordena a equipe de pesquisa do OVPDH.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-card">
                    <div class="team-avatar">J</div>
                    <div class="fw-bold" style="font-size:.95rem;">João Paulo Ferreira</div>
                    <div style="font-size:.78rem; color:var(--ovpdh-vermelho); font-weight:600; margin:.25rem 0;">Coordenador de Dados</div>
                    <div style="font-size:.825rem; color:var(--ovpdh-cinza); line-height:1.6;">Mestrando em Ciências Sociais, responsável pela sistematização e curadoria dos dados do observatório.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Parceiros -->
<section style="padding:4rem 0; background:var(--ovpdh-preto);">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-label justify-content-center" style="color:var(--ovpdh-vermelho-claro);"><i class="bi bi-handshake"></i> Parcerias</div>
            <h2 class="section-title" style="color:white; text-align:center;">Parceiros Institucionais</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-6">
                <div style="border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:1.5rem; text-align:center; transition:all .25s;" onmouseover="this.style.borderColor='rgba(192,39,45,.4)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                    <div style="font-size:1.5rem; font-weight:900; color:white; margin-bottom:.5rem;">PUC São Paulo</div>
                    <div style="font-size:.75rem; color:rgba(255,255,255,.4);">Pontificia Universidade Católica de Minas Gerais</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div style="border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:1.5rem; text-align:center; transition:all .25s;" onmouseover="this.style.borderColor='rgba(192,39,45,.4)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                    <div style="font-size:1.1rem; font-weight:900; color:white; margin-bottom:.5rem;">PPGCS PUC</div>
                    <div style="font-size:.75rem; color:rgba(255,255,255,.4);">Prog. de Pós-Graduação em Ciências Sociais</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div style="border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:1.5rem; text-align:center; transition:all .25s;" onmouseover="this.style.borderColor='rgba(192,39,45,.4)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                    <div style="font-size:1.1rem; font-weight:900; color:white; margin-bottom:.5rem;">CDDHM</div>
                    <div style="font-size:.75rem; color:rgba(255,255,255,.4);">Centro de Defesa dos Direitos Humanos</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div style="border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:1.5rem; text-align:center; transition:all .25s;" onmouseover="this.style.borderColor='rgba(192,39,45,.4)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                    <div style="font-size:1.1rem; font-weight:900; color:white; margin-bottom:.5rem;">CNPQ</div>
                    <div style="font-size:.75rem; color:rgba(255,255,255,.4);">Conselho Nacional de Desenvolvimento Científico</div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="<?= base_url('pucsp') ?>" target="_blank" class="btn-ovpdh-primary">
                <i class="bi bi-box-arrow-up-right"></i> Acessar site PUC São Paulo
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

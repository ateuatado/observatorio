#!/bin/bash
# =============================================================
# OVP — Script de instalação no servidor (CloudPanel/Ubuntu)
# Uso: bash install.sh
# =============================================================

set -e  # Para em qualquer erro

REPO="https://github.com/ateuatado/observatorio.git"
SITE_DIR="/home/ovp/htdocs"   # Ajustar para o caminho do site no CloudPanel
PHP="php8.2"                   # Binário PHP do servidor

echo "=========================================="
echo " OVP — Deploy de produção"
echo "=========================================="

# 1. Clonar o repositório
echo "[1/7] Clonando repositório..."
git clone "$REPO" "$SITE_DIR"
cd "$SITE_DIR"

# 2. Instalar dependências
echo "[2/7] Instalando dependências Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Configurar .env
echo "[3/7] Configurando .env..."
cp env.production .env
echo ""
echo ">>> EDITE o arquivo .env antes de continuar:"
echo "    nano .env"
echo ""
read -p "Pressione ENTER após salvar o .env..."

# 4. Permissões
echo "[4/7] Ajustando permissões..."
chmod -R 755 writable/
chown -R www-data:www-data writable/
mkdir -p arquivos && chown www-data:www-data arquivos/

# 5. Migrations
echo "[5/7] Rodando migrations..."
$PHP spark migrate --all --no-interaction

# 6. Criar admin
echo "[6/7] Criando usuário administrador..."
$PHP spark shield:create-user

# 7. Limpar cache
echo "[7/7] Limpando cache..."
$PHP spark cache:clear

echo ""
echo "=========================================="
echo " Deploy concluído com sucesso!"
echo " Acesse: https://SEU_DOMINIO"
echo "=========================================="

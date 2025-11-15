#!/bin/bash

# AdminPanelATS - Script de Configuração Rápida
# Este script ajuda na configuração inicial do sistema

echo "=========================================="
echo "  AdminPanelATS - Setup Script"
echo "=========================================="
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para verificar se comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Verificar PHP
echo "Verificando PHP..."
if command_exists php; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
    echo -e "${GREEN}✓${NC} PHP encontrado: versão $PHP_VERSION"
    
    # Verificar versão mínima
    REQUIRED_VERSION="7.4"
    if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" = "$REQUIRED_VERSION" ]; then
        echo -e "${GREEN}✓${NC} Versão do PHP está OK (>= 7.4)"
    else
        echo -e "${RED}✗${NC} PHP 7.4 ou superior é necessário"
        exit 1
    fi
else
    echo -e "${RED}✗${NC} PHP não encontrado. Por favor, instale o PHP 7.4 ou superior."
    exit 1
fi

# Verificar extensões necessárias
echo ""
echo "Verificando extensões PHP necessárias..."

check_extension() {
    if php -m | grep -q "^$1$"; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 (faltando)"
        return 1
    fi
}

MISSING_EXTENSIONS=0
check_extension "PDO" || ((MISSING_EXTENSIONS++))
check_extension "mbstring" || ((MISSING_EXTENSIONS++))
check_extension "json" || ((MISSING_EXTENSIONS++))

if php -m | grep -q "sqlsrv\|pdo_sqlsrv"; then
    echo -e "${GREEN}✓${NC} SQL Server driver (sqlsrv ou pdo_sqlsrv)"
else
    echo -e "${YELLOW}!${NC} SQL Server driver não detectado. Você precisará instalá-lo."
    echo "  Veja INSTALL.md para instruções detalhadas."
fi

# Criar diretório de uploads se não existir
echo ""
echo "Configurando diretórios..."
if [ ! -d "assets/uploads" ]; then
    mkdir -p assets/uploads
    echo -e "${GREEN}✓${NC} Diretório assets/uploads criado"
else
    echo -e "${GREEN}✓${NC} Diretório assets/uploads já existe"
fi

# Configurar permissões
echo ""
echo "Configurando permissões..."
chmod 755 -R assets/uploads/
echo -e "${GREEN}✓${NC} Permissões configuradas para assets/uploads"

# Criar arquivo de configuração do banco de dados
echo ""
echo "Configurando banco de dados..."
if [ ! -f "config/database.php" ]; then
    cp config/database.example.php config/database.php
    echo -e "${GREEN}✓${NC} Arquivo config/database.php criado"
    echo -e "${YELLOW}!${NC} Edite config/database.php com suas credenciais do SQL Server"
else
    echo -e "${YELLOW}!${NC} config/database.php já existe. Pulando..."
fi

# Resumo
echo ""
echo "=========================================="
echo "  Resumo da Configuração"
echo "=========================================="

if [ $MISSING_EXTENSIONS -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Todas as extensões PHP necessárias estão instaladas"
else
    echo -e "${YELLOW}!${NC} $MISSING_EXTENSIONS extensão(ões) faltando"
fi

echo ""
echo "Próximos passos:"
echo "1. Configure o SQL Server driver se ainda não estiver instalado"
echo "2. Edite config/database.php com suas credenciais"
echo "3. Execute o script database_schema.sql no SQL Server"
echo "4. Configure seu servidor web (Apache/Nginx)"
echo "5. Acesse http://localhost/dashboard.php"
echo ""
echo "Para instruções detalhadas, consulte INSTALL.md"
echo ""
echo -e "${GREEN}Setup concluído!${NC}"

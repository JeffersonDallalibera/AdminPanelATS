# Guia de Instalação - AdminPanelATS

Este guia detalha os passos necessários para configurar e executar o sistema AdminPanelATS.

## Pré-requisitos

### 1. PHP 7.4 ou superior

Verifique se o PHP está instalado:
```bash
php -v
```

Extensões PHP necessárias:
- `pdo`
- `pdo_sqlsrv` ou `sqlsrv` (SQL Server driver)
- `mbstring`
- `json`

### 2. SQL Server 2016 ou superior

Pode ser:
- SQL Server Express (gratuito)
- SQL Server Developer Edition (gratuito)
- SQL Server Standard/Enterprise

### 3. Servidor Web

Opções:
- Apache 2.4+ (com mod_rewrite)
- Nginx 1.18+
- PHP Built-in Server (apenas para desenvolvimento)

## Instalação Passo a Passo

### Passo 1: Clonar o Repositório

```bash
git clone https://github.com/JeffersonDallalibera/AdminPanelATS.git
cd AdminPanelATS
```

### Passo 2: Instalar SQL Server Driver para PHP

#### Windows

1. Baixe os drivers do Microsoft:
   - https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server

2. Extraia os arquivos DLL apropriados para sua versão do PHP

3. Copie os arquivos para o diretório `ext` do PHP:
   ```
   php_sqlsrv_*.dll
   php_pdo_sqlsrv_*.dll
   ```

4. Edite o `php.ini` e adicione:
   ```ini
   extension=php_sqlsrv.dll
   extension=php_pdo_sqlsrv.dll
   ```

5. Reinicie o servidor web

#### Linux

```bash
# Ubuntu/Debian
curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
apt-get update
ACCEPT_EULA=Y apt-get install -y msodbcsql17
ACCEPT_EULA=Y apt-get install -y mssql-tools
apt-get install -y unixodbc-dev

# Instalar extensões PHP
pecl install sqlsrv
pecl install pdo_sqlsrv

# Adicionar ao php.ini
echo "extension=sqlsrv.so" >> /etc/php/7.4/apache2/php.ini
echo "extension=pdo_sqlsrv.so" >> /etc/php/7.4/apache2/php.ini
```

### Passo 3: Criar o Banco de Dados

#### Opção 1: SQL Server Management Studio (Windows)

1. Abra o SSMS
2. Conecte ao servidor
3. Abra o arquivo `database_schema.sql`
4. Execute o script (F5)

#### Opção 2: sqlcmd (Windows/Linux)

```bash
sqlcmd -S localhost -U sa -P YourPassword -i database_schema.sql
```

#### Opção 3: Azure Data Studio (Cross-platform)

1. Abra o Azure Data Studio
2. Conecte ao servidor
3. Abra o arquivo `database_schema.sql`
4. Execute o script

### Passo 4: Configurar a Conexão com o Banco

1. Copie o arquivo de exemplo:
   ```bash
   cp config/database.example.php config/database.php
   ```

2. Edite `config/database.php` com suas credenciais:
   ```php
   <?php
   return [
       'driver' => 'sqlsrv',
       'host' => 'localhost',        // ou IP do servidor
       'port' => '1433',              // porta padrão SQL Server
       'database' => 'AdminPanelATS',
       'username' => 'sa',            // seu usuário
       'password' => 'SuaSenhaForte', // sua senha
       'charset' => 'utf8',
       'options' => [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::ATTR_EMULATE_PREPARES => false,
       ]
   ];
   ```

### Passo 5: Configurar Permissões

```bash
# Linux/Mac
chmod 755 -R assets/uploads/
chown www-data:www-data -R assets/uploads/

# Windows
# Dar permissões de leitura/escrita ao usuário IIS/Apache
```

### Passo 6: Configurar o Servidor Web

#### Apache

O arquivo `.htaccess` já está configurado. Certifique-se de que:

1. `mod_rewrite` está habilitado:
   ```bash
   # Ubuntu/Debian
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. Configure o VirtualHost (opcional):
   ```apache
   <VirtualHost *:80>
       ServerName adminpanel.local
       DocumentRoot "/caminho/para/AdminPanelATS"
       
       <Directory "/caminho/para/AdminPanelATS">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

#### Nginx

Adicione ao arquivo de configuração:

```nginx
server {
    listen 80;
    server_name adminpanel.local;
    root /caminho/para/AdminPanelATS;
    index index.php;

    location / {
        try_files $uri $uri/ /public/$uri /public/index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### PHP Built-in Server (Desenvolvimento)

```bash
cd AdminPanelATS
php -S localhost:8000 -t public
```

### Passo 7: Acessar o Sistema

Abra seu navegador e acesse:
- Apache/Nginx: `http://localhost/dashboard.php` ou `http://adminpanel.local/dashboard.php`
- PHP Built-in: `http://localhost:8000/dashboard.php`

## Verificação da Instalação

### 1. Teste a Conexão com o Banco

Crie um arquivo `test_connection.php` na raiz:

```php
<?php
require_once 'config/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "✅ Conexão com banco de dados OK!<br>";
    
    // Testa uma query
    $stmt = $conn->query("SELECT COUNT(*) as total FROM wiki");
    $result = $stmt->fetch();
    echo "✅ Total de wikis: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
```

Acesse: `http://localhost/test_connection.php`

### 2. Verifique as Extensões PHP

Crie um arquivo `phpinfo.php`:

```php
<?php
phpinfo();
```

Procure por:
- `pdo_sqlsrv`
- `sqlsrv`

### 3. Teste o Upload

1. Acesse `/gerar_wiki.php`
2. Faça upload do arquivo `exemplo_wiki.txt`
3. Verifique se o preview é exibido
4. Salve a wiki

## Solução de Problemas

### Erro: "Could not find driver"

**Solução:** O driver SQL Server não está instalado. Siga o Passo 2 novamente.

### Erro: "Access denied"

**Solução:** Verifique as credenciais em `config/database.php`

### Erro: "Connection refused"

**Soluções possíveis:**
1. Verifique se o SQL Server está rodando
2. Confirme a porta (padrão: 1433)
3. Verifique o firewall
4. Habilite TCP/IP no SQL Server Configuration Manager

### Erro 500 ao acessar páginas

**Soluções:**
1. Verifique os logs do Apache/Nginx
2. Confirme as permissões dos arquivos
3. Verifique se há erros de sintaxe PHP

### Upload de arquivo não funciona

**Soluções:**
1. Verifique permissões do diretório `assets/uploads/`
2. Confirme os limites de upload no `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

## Segurança em Produção

### 1. Proteja o arquivo de configuração

```bash
# Adicione ao .gitignore
echo "config/database.php" >> .gitignore
```

### 2. Desabilite exibição de erros

No `php.ini` ou `.htaccess`:
```ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

### 3. Use HTTPS

Configure SSL/TLS no servidor web.

### 4. Crie usuário dedicado no SQL Server

Não use `sa` em produção:

```sql
CREATE LOGIN admin_panel_user WITH PASSWORD = 'SenhaForte123!';
USE AdminPanelATS;
CREATE USER admin_panel_user FOR LOGIN admin_panel_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON SCHEMA::dbo TO admin_panel_user;
```

### 5. Configure backup automático

Configure rotinas de backup do banco de dados SQL Server.

## Próximos Passos

1. ✅ Instalar e configurar o sistema
2. ✅ Criar sua primeira wiki
3. ✅ Adicionar algumas tarefas
4. 📚 Explorar o dashboard
5. 🔧 Personalizar conforme necessário

## Suporte

- 📧 Issues no GitHub
- 📖 Documentação completa no README.md
- 💬 Wiki do projeto (quando disponível)

---

**Desenvolvido por Jefferson Dallalibera**

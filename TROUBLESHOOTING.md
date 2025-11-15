# Troubleshooting - AdminPanelATS

Este documento contém soluções para problemas comuns que você pode encontrar ao usar o AdminPanelATS.

## Problemas de Conexão com Banco de Dados

### Erro: "Could not find driver"

**Causa:** O driver PDO para SQL Server não está instalado ou habilitado.

**Solução:**

1. **Windows:**
   ```
   - Baixe os drivers SQL Server para PHP
   - Copie os arquivos DLL para o diretório ext do PHP
   - Adicione ao php.ini:
     extension=php_pdo_sqlsrv.dll
     extension=php_sqlsrv.dll
   - Reinicie o servidor web
   ```

2. **Linux:**
   ```bash
   pecl install sqlsrv
   pecl install pdo_sqlsrv
   echo "extension=pdo_sqlsrv.so" >> /etc/php/7.4/apache2/php.ini
   systemctl restart apache2
   ```

### Erro: "Login failed for user"

**Causa:** Credenciais incorretas ou usuário sem permissões.

**Solução:**
1. Verifique username e password em `config/database.php`
2. Teste a conexão com SQL Server Management Studio usando as mesmas credenciais
3. Verifique se o usuário tem permissões no banco `AdminPanelATS`

### Erro: "Named Pipes Provider: Could not open a connection to SQL Server"

**Causa:** SQL Server não está aceitando conexões TCP/IP.

**Solução:**
1. Abra SQL Server Configuration Manager
2. Vá para SQL Server Network Configuration > Protocols
3. Habilite TCP/IP
4. Reinicie o serviço SQL Server
5. Verifique se a porta 1433 está aberta no firewall

## Problemas de Upload de Arquivos

### Erro: "Failed to move uploaded file"

**Causa:** Permissões insuficientes no diretório de uploads.

**Solução:**
```bash
# Linux
chmod 755 assets/uploads/
chown www-data:www-data assets/uploads/

# Windows
# Dar permissão de escrita para IIS_IUSRS ou Apache user
```

### Erro: "File too large"

**Causa:** Arquivo excede o limite configurado.

**Solução:**
Edite o `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

Ou no `.htaccess`:
```apache
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

### Upload funciona mas arquivo não é salvo

**Causa:** Parser do TXT pode estar falhando.

**Solução:**
1. Verifique o formato do arquivo TXT
2. Certifique-se de usar codificação UTF-8
3. Verifique os logs de erro do PHP

## Problemas com AJAX / Monitor de Tarefas

### Tarefas não atualizam automaticamente

**Causa:** JavaScript não está carregando ou há erro de CORS.

**Solução:**
1. Abra o Console do navegador (F12) e verifique erros
2. Verifique se `/assets/js/main.js` está carregando
3. Confirme que os endpoints da API estão acessíveis:
   - `/api/tarefas.php`
   - `/api/stats.php`

### Erro 404 nas requisições AJAX

**Causa:** URLs das APIs não estão corretas.

**Solução:**
1. Verifique o `.htaccess` se usando Apache
2. Confirme a configuração do servidor web
3. Teste acessar diretamente: `http://localhost/api/tarefas.php`

### Auto-update não funciona

**Causa:** JavaScript pode estar sendo bloqueado.

**Solução:**
1. Verifique o console do navegador
2. Certifique-se de que JavaScript está habilitado
3. Teste em modo de navegação anônima

## Problemas de Visualização / Layout

### CSS não carrega

**Causa:** Caminho do CSS incorreto.

**Solução:**
1. Verifique se `/assets/css/style.css` existe
2. Confirme as permissões de leitura
3. Limpe o cache do navegador (Ctrl+F5)
4. Verifique o console do navegador para erros 404

### Layout quebrado / sem estilo

**Causa:** CSS não está sendo aplicado.

**Solução:**
1. Verifique o código-fonte da página (Ctrl+U)
2. Confirme que o link do CSS está correto no header
3. Teste acessar diretamente o arquivo CSS

### Caracteres especiais aparecem incorretamente

**Causa:** Problema de codificação.

**Solução:**
1. Certifique-se de que o banco usa UTF-8
2. Adicione ao início dos arquivos PHP:
   ```php
   header('Content-Type: text/html; charset=utf-8');
   ```
3. Configure o SQL Server para usar UTF-8

## Problemas de Performance

### Sistema lento

**Soluções:**
1. Adicione índices nas tabelas (já incluídos no schema)
2. Otimize queries complexas
3. Habilite cache do PHP (OPcache)
4. Ajuste a configuração do SQL Server

### AJAX muito lento

**Soluções:**
1. Aumente o intervalo de auto-update (padrão: 10s)
2. Adicione paginação nas listagens
3. Otimize as queries SQL

## Problemas de Segurança

### Aviso: SQL Injection

**Prevenção já implementada:**
- Todas as queries usam prepared statements
- Input é sanitizado

**Verificação:**
Confirme que todas as queries usam `?` placeholders:
```php
$stmt = $db->prepare("SELECT * FROM tabela WHERE id = ?");
$stmt->execute([$id]);
```

### Aviso: XSS (Cross-Site Scripting)

**Prevenção já implementada:**
- Output é sanitizado com `htmlspecialchars()`
- Método `sanitize()` no BaseController

**Verificação:**
Certifique-se de que variáveis são escapadas:
```php
echo htmlspecialchars($variavel);
```

## Logs e Debug

### Habilitar logs de erro PHP

Edite `php.ini`:
```ini
error_reporting = E_ALL
display_errors = On
log_errors = On
error_log = /var/log/php_errors.log
```

### Habilitar logs SQL Server

```sql
-- Ver logs de erro
EXEC xp_readerrorlog 0, 1
```

### Debug de queries

No BaseModel, adicione antes de executar queries:
```php
echo $sql . "\n";
print_r($params);
```

## Verificação de Sistema

### Checklist rápido

```bash
# PHP instalado e versão correta?
php -v

# Extensões necessárias?
php -m | grep -E "pdo|sqlsrv|mbstring|json"

# SQL Server rodando?
# Windows: services.msc
# Linux: systemctl status mssql-server

# Permissões corretas?
ls -la assets/uploads/

# Servidor web rodando?
# Apache: systemctl status apache2
# Nginx: systemctl status nginx
```

### Teste de conexão rápido

Crie `test.php`:
```php
<?php
require_once 'config/Database.php';
try {
    $db = Database::getInstance();
    echo "✓ Conexão OK\n";
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
```

## Suporte Adicional

Se o problema persistir:

1. **Verifique os logs:**
   - PHP error log
   - Servidor web error log
   - SQL Server error log

2. **Teste componentes individualmente:**
   - Teste a conexão com o banco
   - Teste o upload de arquivos
   - Teste as APIs via navegador

3. **Abra uma issue no GitHub:**
   - Descreva o problema
   - Inclua mensagens de erro
   - Informe versões (PHP, SQL Server, OS)

## Recursos Úteis

- [Documentação PHP PDO](https://www.php.net/manual/en/book.pdo.php)
- [Microsoft SQL Server Drivers para PHP](https://docs.microsoft.com/en-us/sql/connect/php/)
- [Stack Overflow - PHP + SQL Server](https://stackoverflow.com/questions/tagged/php+sql-server)

---

**Última atualização:** 2025

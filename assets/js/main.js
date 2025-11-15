/**
 * AdminPanelATS - JavaScript Principal
 * 
 * Scripts comuns para todas as páginas
 */

// Funções utilitárias disponíveis globalmente
window.AdminPanel = {
    /**
     * Formata data/hora para padrão brasileiro
     */
    formatarDataHora: function(dataString) {
        const data = new Date(dataString);
        return data.toLocaleDateString('pt-BR') + ' ' + 
               data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    },
    
    /**
     * Formata apenas data para padrão brasileiro
     */
    formatarData: function(dataString) {
        const data = new Date(dataString);
        return data.toLocaleDateString('pt-BR');
    },
    
    /**
     * Escape HTML para prevenir XSS
     */
    escapeHtml: function(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    /**
     * Capitaliza primeira letra
     */
    capitalizarPrimeira: function(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },
    
    /**
     * Mostra notificação
     */
    notificar: function(mensagem, tipo = 'info') {
        // Implementação básica usando alert
        // Em produção, usar biblioteca de notificações
        alert(mensagem);
    },
    
    /**
     * Confirma ação
     */
    confirmar: function(mensagem) {
        return confirm(mensagem);
    }
};

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('AdminPanelATS inicializado');
    
    // Adiciona classe 'loaded' ao body
    document.body.classList.add('loaded');
    
    // Tooltips e outras funcionalidades globais podem ser inicializadas aqui
});

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const sidebar = document.querySelector('.fi-sidebar');
        const logoContainer = document.querySelector('.fi-sidebar-header-logo');
        
        
        function updateLogo() {
            const isCollapsed = sidebar.classList.contains('fi-sidebar-collapsed');
            
            if (isCollapsed) {
                logoContainer.innerHTML = '<img src="/images/9.png" alt="Dataplus" class="h-10 w-10 rounded-full">';
            } else {
                logoContainer.innerHTML = '<div class="flex items-center gap-2"><img src="/images/9.png" alt="Dataplus" class="h-10 w-10 rounded-full flex-shrink-0"><span class="fi-custom-brand-name">dataplus</span></div>';
            }
        }
        
        const observer = new MutationObserver(updateLogo);
        observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        
        updateLogo();
    }, 1000);
});

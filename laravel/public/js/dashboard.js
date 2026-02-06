// dashboard.js

document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar for mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Copy link functionality with toast notification
    const copyButtons = document.querySelectorAll('.copy-btn');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            
            navigator.clipboard.writeText(url).then(() => {
                // Show toast notification
                showToast('Link berhasil disalin ke clipboard!', 'success');
                
                // Visual feedback on button
                const icon = this.querySelector('i');
                const originalClass = icon.className;
                icon.className = 'fas fa-check';
                
                setTimeout(() => {
                    icon.className = originalClass;
                }, 2000);
            }).catch(err => {
                showToast('Gagal menyalin link', 'error');
                console.error('Failed to copy: ', err);
            });
        });
    });
    
    // Show toast notification
    function showToast(message, type = 'info') {
        // Remove existing toast
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
        
        // Close button
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.remove();
        });
    }
    
    // Add CSS for toast
    const toastStyle = document.createElement('style');
    toastStyle.textContent = `
        .toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: white;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            border-left: 4px solid #8B5CF6;
            min-width: 300px;
            max-width: 400px;
        }
        
        .toast-notification.success {
            border-left-color: #10B981;
        }
        
        .toast-notification.error {
            border-left-color: #EF4444;
        }
        
        .toast-icon {
            font-size: 20px;
        }
        
        .toast-notification.success .toast-icon {
            color: #10B981;
        }
        
        .toast-notification.error .toast-icon {
            color: #EF4444;
        }
        
        .toast-message {
            flex: 1;
            font-weight: 500;
        }
        
        .toast-close {
            background: transparent;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
        }
        
        .toast-close:hover {
            color: #6B7280;
            background: #F3F4F6;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .fade-out {
            animation: fadeOut 0.3s ease forwards;
        }
        
        @keyframes fadeOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(toastStyle);
    
    // Chart bar hover effect
    const chartBars = document.querySelectorAll('.chart-bar');
    
    chartBars.forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            const value = this.getAttribute('data-value');
            const day = this.getAttribute('data-day');
            
            // Show tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'chart-tooltip';
            tooltip.innerHTML = `
                <strong>${day}</strong><br>
                ${value} klik
            `;
            tooltip.style.position = 'absolute';
            tooltip.style.background = '#1F2937';
            tooltip.style.color = 'white';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '6px';
            tooltip.style.fontSize = '12px';
            tooltip.style.zIndex = '1000';
            tooltip.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - 50) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2) + 'px';
            tooltip.style.transform = 'translateX(-50%)';
            
            document.body.appendChild(tooltip);
            
            // Add arrow
            const arrow = document.createElement('div');
            arrow.style.position = 'absolute';
            arrow.style.bottom = '-8px';
            arrow.style.left = '50%';
            arrow.style.transform = 'translateX(-50%)';
            arrow.style.width = '0';
            arrow.style.height = '0';
            arrow.style.borderLeft = '8px solid transparent';
            arrow.style.borderRight = '8px solid transparent';
            arrow.style.borderTop = '8px solid #1F2937';
            
            tooltip.appendChild(arrow);
            
            this.tooltip = tooltip;
        });
        
        bar.addEventListener('mouseleave', function() {
            if (this.tooltip) {
                this.tooltip.remove();
                this.tooltip = null;
            }
        });
    });
    
    // Balance top-up simulation
    const topupBtn = document.querySelector('.btn-balance.topup');
    const balanceAmount = document.querySelector('.balance-amount');
    
    if (topupBtn && balanceAmount) {
        topupBtn.addEventListener('click', function() {
            // Simulate top-up modal
            showToast('Fitur Top Up akan segera hadir!', 'info');
        });
    }
    
    // Withdraw button
    const withdrawBtn = document.querySelector('.withdraw-btn');
    if (withdrawBtn) {
        withdrawBtn.addEventListener('click', function() {
            showToast('Fitur Tarik Dana akan segera hadir!', 'info');
        });
    }
    
    // Notification bell
    const notificationBell = document.querySelector('.notification-bell');
    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            const dot = this.querySelector('.notification-dot');
            if (dot) {
                dot.style.display = 'none';
                showToast('Notifikasi dibaca', 'info');
            }
        });
    }
    
    // Create link button
    const createLinkBtn = document.querySelector('.btn-create-link');
    if (createLinkBtn) {
        createLinkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showToast('Membuka halaman buat link baru...', 'info');
            // In real app, this would navigate to create link page
        });
    }
    
    // Upgrade button
    const upgradeBtn = document.querySelector('.btn-upgrade');
    if (upgradeBtn) {
        upgradeBtn.addEventListener('click', function() {
            showToast('Membuka halaman upgrade premium...', 'info');
        });
    }
    
    // Link card analytics button
    const analyticsBtns = document.querySelectorAll('.btn-action.analytics');
    analyticsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            showToast('Membuka halaman analitik...', 'info');
        });
    });
});

document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        toggle.parentElement.classList.toggle('open');
    });
});


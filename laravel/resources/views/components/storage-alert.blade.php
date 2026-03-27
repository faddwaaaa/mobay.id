<!-- Storage Alert Component
     Menampilkan notifikasi dan progress bar penggunaan storage user
-->
@if(isset($showStorageAlert) && $showStorageAlert)
    <style>
        .storage-alert {
            display: flex;
            gap: 12px;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            animation: slideInDown 0.3s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .storage-alert.error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
        }

        .storage-alert.warning {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            color: #92400e;
        }

        .storage-alert.info {
            background: #f0f9ff;
            border: 1px solid #cffafe;
            color: #0c2340;
        }

        .storage-alert-icon {
            font-size: 20px;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
        }

        .storage-alert.error .storage-alert-icon {
            color: #dc2626;
        }

        .storage-alert.warning .storage-alert-icon {
            color: #f59e0b;
        }

        .storage-alert.info .storage-alert-icon {
            color: #0ea5e9;
        }

        .storage-alert-content {
            flex: 1;
            min-width: 0;
        }

        .storage-alert-message {
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .storage-alert-message strong {
            font-weight: 600;
        }

        .storage-bar {
            width: 100%;
            height: 6px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .storage-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .storage-alert.error .storage-bar-fill {
            background: #dc2626;
        }

        .storage-alert.warning .storage-bar-fill {
            background: #f59e0b;
        }

        .storage-alert.info .storage-bar-fill {
            background: #0ea5e9;
        }

        .storage-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 6px;
            opacity: 0.8;
        }

        .storage-alert-close {
            background: none;
            border: none;
            color: currentColor;
            cursor: pointer;
            padding: 4px;
            font-size: 16px;
            display: flex;
            align-items: center;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .storage-alert-close:hover {
            opacity: 1;
        }

        .storage-upgrade-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .storage-upgrade-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
    </style>

    <div class="storage-alert {{ $storageStatus ?? 'info' }} storage-alert-{{ $storageStatus ?? 'info' }}">
        <div class="storage-alert-icon">
            @if($storageStatus === 'error')
                <i class="fas fa-circle-xmark"></i>
            @elseif($storageStatus === 'warning')
                <i class="fas fa-exclamation-triangle"></i>
            @else
                <i class="fas fa-info-circle"></i>
            @endif
        </div>
        <div class="storage-alert-content">
            <div class="storage-alert-message">
                {{ $storageMessage ?? 'Storage usage information' }}
                @if($storageStatus === 'error' && !Auth::user()->isPro())
                    <a href="{{ route('premium.index') }}" class="storage-upgrade-btn">
                        <i class="fas fa-arrow-up-right-dots"></i> Upgrade to Pro
                    </a>
                @endif
            </div>
            @if(isset($storagePercentage))
                <div class="storage-bar">
                    <div class="storage-bar-fill" style="width: {{ min($storagePercentage, 100) }}%"></div>
                </div>
                <div class="storage-details">
                    <span>{{ $storagePercentage }}% used</span>
                    <span>{{ $storageUsed ?? 'N/A' }} / {{ $storageLimit ?? 'N/A' }}</span>
                </div>
            @endif
        </div>
        <button class="storage-alert-close" onclick="this.parentElement.style.display='none'; return false;">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

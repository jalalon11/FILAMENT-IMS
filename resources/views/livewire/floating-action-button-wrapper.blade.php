<div x-data="{ open: false }" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;">
    {{-- Quick Action Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95" @click.away="open = false"
        style="max-height: 500px; position: absolute; bottom: 65px; right: 0; width: 260px; background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); border: 1px solid #e5e7eb; overflow: auto;">

        {{-- Header --}}
        <div style="padding: 16px 20px; background: linear-gradient(135deg, #f59e0b, #ea580c); color: white;">
            <p style="font-size: 16px; font-weight: 700; margin: 0;">Quick Actions</p>
            <p style="font-size: 12px; opacity: 0.9; margin: 4px 0 0 0;">Navigate anywhere quickly</p>
        </div>

        {{-- Navigation Section --}}
        <div style="padding: 8px;">
            <p
                style="font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 12px 4px;">
                Navigation</p>

            {{-- Dashboard --}}
            <a href="{{ route('filament.admin.pages.dashboard') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none; transition: background 0.2s;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #ea580c); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Dashboard</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Overview & stats</p>
                </div>
            </a>
        </div>

        {{-- Inventory Section --}}
        <div style="padding: 0 8px 8px;">
            <p
                style="font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 12px 4px; border-top: 1px solid #f3f4f6;">
                Inventory</p>

            {{-- Products --}}
            <a href="{{ route('filament.admin.resources.products.index') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Products</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Manage inventory</p>
                </div>
            </a>

            {{-- Transactions --}}
            <a href="{{ route('filament.admin.resources.transactions.index') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #22c55e, #16a34a); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Transactions</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Stock In/Out</p>
                </div>
            </a>

            {{-- Bulk Transaction --}}
            <!-- <a href="{{ route('filament.admin.pages.bulk-transaction') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Bulk Transaction</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Multiple items</p>
                </div>
            </a> -->
        </div>

        {{-- Reports Section --}}
        <div style="padding: 0 8px 8px;">
            <p
                style="font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 12px 4px; border-top: 1px solid #f3f4f6;">
                Reports</p>

            {{-- Sales Report --}}
            <a href="{{ route('filament.admin.pages.sales-report') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #ef4444, #dc2626); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Sales Report</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Revenue analytics</p>
                </div>
            </a>

            {{-- Transaction History --}}
            <a href="{{ route('filament.admin.pages.transaction-history') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Transaction History</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">All movements</p>
                </div>
            </a>

            {{-- Activity Logs --}}
            <a href="{{ route('filament.admin.pages.activity-logs') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #4f46e5); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Activity Logs</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">User actions</p>
                </div>
            </a>
        </div>

        {{-- Master Data Section --}}
        <div style="padding: 0 8px 12px;">
            <p
                style="font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 12px 4px; border-top: 1px solid #f3f4f6;">
                Master Data</p>

            {{-- Categories --}}
            <a href="{{ route('filament.admin.resources.categories.index') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #a855f7, #9333ea); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Categories</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Product groups</p>
                </div>
            </a>

            {{-- Companies --}}
            <a href="{{ route('filament.admin.resources.companies.index') }}"
                style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; text-decoration: none;"
                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                <div
                    style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">Companies</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 0;">Suppliers/Vendors</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Floating Action Button --}}
    <button @click="open = !open"
        style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b, #ea580c); border: none; color: white; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
        onmouseover="this.style.boxShadow='0 6px 20px rgba(245, 158, 11, 0.5)'; this.style.transform='scale(1.05)'"
        onmouseout="this.style.boxShadow='0 4px 15px rgba(245, 158, 11, 0.4)'; this.style.transform='scale(1)'">
        <svg :style="open ? 'transform: rotate(45deg)' : ''"
            style="width: 28px; height: 28px; transition: transform 0.2s;" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </button>
</div>
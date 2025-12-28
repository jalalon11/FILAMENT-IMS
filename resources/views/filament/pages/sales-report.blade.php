<x-filament-panels::page>
    {{-- Modern Header with Gradient --}}
    <div style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); border-radius: 16px; padding: 24px 32px; margin-bottom: 24px; color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 700; margin: 0 0 4px 0;">Sales Analytics</h2>
                <p style="font-size: 14px; opacity: 0.9; margin: 0;">
                    {{ Carbon\Carbon::parse($date_from)->format('M d, Y') }} - {{ Carbon\Carbon::parse($date_to)->format('M d, Y') }}
                </p>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <select wire:model.live="preset"
                    style="padding: 10px 16px; border-radius: 10px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-weight: 500; cursor: pointer; backdrop-filter: blur(10px);">
                    <option value="today" style="color: #111;">Today</option>
                    <option value="yesterday" style="color: #111;">Yesterday</option>
                    <option value="this_week" style="color: #111;">This Week</option>
                    <option value="last_week" style="color: #111;">Last Week</option>
                    <option value="this_month" style="color: #111;">This Month</option>
                    <option value="last_month" style="color: #111;">Last Month</option>
                    <option value="this_year" style="color: #111;">This Year</option>
                    <option value="custom" style="color: #111;">Custom Range</option>
                </select>
                @if($preset === 'custom')
                    <input type="date" wire:model.live="date_from"
                        style="padding: 10px 16px; border-radius: 10px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-weight: 500;">
                    <input type="date" wire:model.live="date_to"
                        style="padding: 10px 16px; border-radius: 10px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-weight: 500;">
                @endif
            </div>
        </div>
    </div>

    @php
        $summary = $this->getSummaryData();
        $profitMargin = $summary['total_sales'] > 0 ? ($summary['total_profit'] / $summary['total_sales']) * 100 : 0;
    @endphp

    {{-- Stats Cards Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
        {{-- Total Revenue Card --}}
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f3f4f6; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, #fef3c7, #fcd34d); border-radius: 50%; opacity: 0.5;"></div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 28px; height: 28px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0; font-weight: 500;">Total Revenue</p>
                    <p style="font-size: 28px; font-weight: 800; color: #111827; margin: 4px 0 0 0; line-height: 1;">₱{{ number_format($summary['total_sales'], 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Net Profit Card --}}
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f3f4f6; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, #dcfce7, #86efac); border-radius: 50%; opacity: 0.5;"></div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #22c55e, #16a34a); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 28px; height: 28px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0; font-weight: 500;">Net Profit</p>
                    <p style="font-size: 28px; font-weight: 800; color: #22c55e; margin: 4px 0 0 0; line-height: 1;">₱{{ number_format($summary['total_profit'], 2) }}</p>
                </div>
            </div>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;">
                <span style="font-size: 12px; color: #16a34a; font-weight: 600;">{{ number_format($profitMargin, 1) }}% margin</span>
            </div>
        </div>

        {{-- Transactions Card --}}
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f3f4f6; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, #dbeafe, #93c5fd); border-radius: 50%; opacity: 0.5;"></div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 28px; height: 28px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0; font-weight: 500;">Transactions</p>
                    <p style="font-size: 28px; font-weight: 800; color: #111827; margin: 4px 0 0 0; line-height: 1;">{{ number_format($summary['total_transactions']) }}</p>
                </div>
            </div>
        </div>

        {{-- Items Sold Card --}}
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f3f4f6; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, #f3e8ff, #d8b4fe); border-radius: 50%; opacity: 0.5;"></div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #a855f7, #9333ea); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 28px; height: 28px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0; font-weight: 500;">Items Sold</p>
                    <p style="font-size: 28px; font-weight: 800; color: #111827; margin: 4px 0 0 0; line-height: 1;">{{ number_format($summary['total_quantity']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f3f4f6; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Sales Transactions</h3>
                <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">Detailed breakdown of all sales</p>
            </div>
        </div>
        <div style="padding: 0;">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
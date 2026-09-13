<x-filament-panels::page>
    <div class="space-y-6">

        <!-- 1. Report Type Navigation Tabs -->
        <x-filament::tabs label="Report Type">
            <x-filament::tabs.item
                :active="$report_type === 'issue_summary'"
                icon="heroicon-o-document-text"
                wire:click="$set('report_type', 'issue_summary')"
            >
                Issue Summary Report
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$report_type === 'port_report'"
                icon="heroicon-o-map-pin"
                wire:click="$set('report_type', 'port_report')"
            >
                Port Operational Report
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$report_type === 'importer_report'"
                icon="heroicon-o-building-office-2"
                wire:click="$set('report_type', 'importer_report')"
            >
                Importer Concerns Report
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$report_type === 'manager_performance'"
                icon="heroicon-o-user-group"
                wire:click="$set('report_type', 'manager_performance')"
            >
                Manager Performance Report
            </x-filament::tabs.item>
        </x-filament::tabs>

        <!-- 2. Filter Parameters Section -->
        <x-filament::section
            icon="heroicon-o-funnel"
            collapsible
        >
            <x-slot name="heading">
                Report Criteria & Filter Parameters
            </x-slot>

            <x-slot name="description">
                Filter by period, collection district, importer, assigned manager, or case status.
            </x-slot>

            <x-slot name="headerEnd">
                <div class="flex items-center gap-2">
                    <x-filament::badge color="info">
                        {{ count($reportData['rows'] ?? []) }} Records Matched
                    </x-filament::badge>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 pt-1">
                <!-- Year Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Reporting Year
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-calendar">
                        <x-filament::input.select wire:model.live="year">
                            <option value="">All Years</option>
                            @foreach(range(date('Y'), date('Y') - 4) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Month Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Reporting Month
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-calendar-days">
                        <x-filament::input.select wire:model.live="month">
                            <option value="">All Months</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                            @endfor
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Port Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Port of Entry
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-map-pin">
                        <x-filament::input.select wire:model.live="port_id">
                            <option value="">All Ports</option>
                            @foreach(\App\Models\Port::all() as $p)
                                <option value="{{ $p->port_id }}">{{ $p->port_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Importer Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Importer / Client
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-building-office-2">
                        <x-filament::input.select wire:model.live="importer_id">
                            <option value="">All Importers</option>
                            @foreach(\App\Models\Importer::all() as $imp)
                                <option value="{{ $imp->importer_id }}">{{ $imp->importer_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Manager Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Accounts Manager
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-user">
                        <x-filament::input.select wire:model.live="assigned_manager">
                            <option value="">All Managers</option>
                            @foreach(\App\Models\User::whereHas('role', fn($q) => $q->where('role_name', 'Accounts Manager'))->get() as $mgr)
                                <option value="{{ $mgr->user_id }}">{{ $mgr->full_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Issue Status
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-tag">
                        <x-filament::input.select wire:model.live="status_id">
                            <option value="">All Statuses</option>
                            @foreach(\App\Enums\IssueStatus::cases() as $st)
                                <option value="{{ $st->value }}">{{ $st->getLabel() }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- BOC Office Filter -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Coordinated BOC Office
                    </label>
                    <x-filament::input.wrapper prefix-icon="heroicon-m-building-library">
                        <x-filament::input.select wire:model.live="boc_office">
                            <option value="">All Coordinated Offices</option>
                            @foreach(\App\Models\BocOffice::all() as $office)
                                <option value="{{ $office->office_name }}">{{ $office->office_name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <!-- Active Filter Summary & Live Indicator -->
            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-white/10 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Applied Filter Scope:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                        {{ $reportData['appliedFilters'] ?? 'All Records' }}
                    </span>
                </div>
                <div wire:loading class="text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                    <x-filament::loading-indicator class="w-4 h-4" />
                    <span class="font-medium">Refreshing preview...</span>
                </div>
            </div>
        </x-filament::section>

        <!-- 3. Live Data Preview Table -->
        <x-filament::section
            icon="heroicon-o-table-cells"
        >
            <x-slot name="heading">
                {{ $reportData['title'] ?? 'Report Preview' }}
            </x-slot>

            <x-slot name="description">
                Generated from verified database records. Use the header actions above to download official PDF or CSV files.
            </x-slot>

            <div class="overflow-x-auto -mx-6 -mb-6 border-t border-gray-200 dark:border-white/10">
                <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-white/10">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5 text-gray-700 dark:text-gray-200 font-bold uppercase tracking-wider text-[11px]">
                            @foreach($reportData['headers'] ?? [] as $header)
                                <th class="px-5 py-3.5 whitespace-nowrap">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                        @forelse($reportData['rows'] ?? [] as $row)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5 transition-colors">
                                @foreach($row as $index => $cell)
                                    @php
                                        $headerName = $reportData['headers'][$index] ?? '';
                                    @endphp

                                    <td class="px-5 py-3 text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                        @if($headerName === 'Issue #' || str_contains($headerName, 'Barcode'))
                                            <span class="font-mono font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-800/40">
                                                {{ $cell }}
                                            </span>
                                        @elseif($headerName === 'Status')
                                            @php
                                                $statusColor = match(strtolower((string)$cell)) {
                                                    'new' => 'info',
                                                    'under review' => 'warning',
                                                    'assigned' => 'primary',
                                                    'in progress' => 'sky',
                                                    'pending client' => 'purple',
                                                    'pending boc coordination' => 'amber',
                                                    'resolved' => 'success',
                                                    'closed' => 'gray',
                                                    'cancelled' => 'danger',
                                                    default => 'gray',
                                                };
                                            @endphp
                                            <x-filament::badge :color="$statusColor">
                                                {{ $cell }}
                                            </x-filament::badge>
                                        @elseif(str_contains($headerName, 'Rate'))
                                            <div class="flex items-center gap-2">
                                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, (int) str_replace('%', '', (string)$cell)) }}%"></div>
                                                </div>
                                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $cell }}</span>
                                            </div>
                                        @elseif(str_contains($headerName, 'Overdue') && (int)$cell > 0)
                                            <x-filament::badge color="danger">
                                                {{ $cell }} Overdue
                                            </x-filament::badge>
                                        @elseif(is_numeric($cell) && !str_starts_with((string)$cell, '0'))
                                            <span class="font-semibold">{{ $cell }}</span>
                                        @else
                                            <span>{{ $cell }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($reportData['headers'] ?? [1]) }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-full text-gray-400">
                                            @svg('heroicon-o-document-magnifying-glass', 'w-8 h-8')
                                        </div>
                                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            No records match current filter criteria
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                                            Adjust or clear the filter selections above to display broader report results.
                                        </div>
                                        <x-filament::button
                                            color="gray"
                                            size="sm"
                                            icon="heroicon-m-arrow-path"
                                            wire:click="resetFilters"
                                        >
                                            Reset Filters
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>

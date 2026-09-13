<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Card -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                Operational Report Criteria & Filters
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Report Type -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Select Report Type</label>
                    <select wire:model="report_type" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="issue_summary">Issue Summary Report (All Details)</option>
                        <option value="port_report">Port Operational Report (Resolution Rates)</option>
                        <option value="importer_report">Importer Concern & Filing Report</option>
                        <option value="manager_performance">Accounts Manager Performance Report</option>
                    </select>
                </div>

                <!-- Year Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                    <select wire:model="year" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Years</option>
                        @foreach(range(date('Y'), date('Y') - 4) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                    <select wire:model="month" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Port Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Port of Entry</label>
                    <select wire:model="port_id" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Ports</option>
                        @foreach(\App\Models\Port::all() as $p)
                            <option value="{{ $p->port_id }}">{{ $p->port_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Importer Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Importer / Client</label>
                    <select wire:model="importer_id" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Importers</option>
                        @foreach(\App\Models\Importer::all() as $imp)
                            <option value="{{ $imp->importer_id }}">{{ $imp->importer_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Manager Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Accounts Manager</label>
                    <select wire:model="assigned_manager" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Managers</option>
                        @foreach(\App\Models\User::whereHas('role', fn($q) => $q->where('role_name', 'Accounts Manager'))->get() as $mgr)
                            <option value="{{ $mgr->user_id }}">{{ $mgr->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select wire:model="status_id" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Statuses</option>
                        @foreach(\App\Enums\IssueStatus::cases() as $st)
                            <option value="{{ $st->value }}">{{ $st->getLabel() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- BOC Office Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Coordinated BOC Office</label>
                    <select wire:model="boc_office" wire:change="generatePreview" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-750 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Coordinated Offices</option>
                        @foreach(\App\Models\BocOffice::all() as $office)
                            <option value="{{ $office->office_name }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <strong>Current Filter Scope:</strong> {{ $reportData['appliedFilters'] ?? 'All' }}
                </div>

                <div class="flex items-center gap-3">
                    <button wire:click="generatePreview" type="button" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Refresh Preview
                    </button>

                    <button wire:click="exportPdf" type="button" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export to PDF
                    </button>

                    <button wire:click="exportCsv" type="button" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export to Excel/CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Preview Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $reportData['title'] ?? 'Report Preview' }}
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Displaying {{ count($reportData['rows'] ?? []) }} record(s) matching criteria
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-750 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                            @foreach($reportData['headers'] ?? [] as $header)
                                <th class="px-4 py-3">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($reportData['rows'] ?? [] as $row)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition">
                                @foreach($row as $cell)
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                        {{ $cell }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($reportData['headers'] ?? [1]) }}" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                    No records match the current filter selection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>

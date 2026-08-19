@extends('layouts.app')

@section('title', 'Kalender Akademik')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-lg font-semibold font-heading text-slate-900">Kalender Akademik</h1>
                            <p class="text-xs text-slate-600 mt-1">
                                @role('admin')
                                    Kelola jadwal kegiatan akademik
                                @else
                                    Pantau jadwal kegiatan akademik
                                @endrole
                            </p>

                        </div>
                        @role('admin')
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button onclick="openAddModal()"
                                    class="inline-flex items-center px-4 py-2 bg-white hover:bg-primary-50/50 border border-gray-100 shadow-sm text-black text-xs font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Agenda
                                </button>
                            </div>
                        @endrole
                    </div>
                </div>

                <!-- Search Section -->
                {{-- <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <div class="max-w-sm">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari acara..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg bg-white text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Calendar Container -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-lg overflow-hidden shadow-">
                <div id="calendar" class="p-4 md:p-6"></div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="eventModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEventModal()"></div>

            <!-- Modal panel -->
            <div
                class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-lg font-semibold text-slate-900">Tambah Agenda</h3>
                            <p class="text-xs text-slate-600">Buat atau edit agenda kalender akademik</p>
                        </div>
                    </div>
                    <button onclick="closeEventModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="eventForm" method="POST">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-6">
                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-xs font-medium text-slate-700 mb-2">
                                Judul Agenda <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="judul" name="judul" placeholder="Masukkan judul agenda"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-white text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                required>
                        </div>

                        <!-- Date Mode Toggle -->
                        <div class="space-y-4">
                            <label class="block text-xs font-medium text-slate-700">
                                Mode Tanggal
                            </label>
                            <div class="flex bg-slate-100 rounded-lg p-1">
                                <button type="button" onclick="toggleDateMode('single')" id="singleDateBtn"
                                    class="flex-1 py-2 px-4 text-xs font-medium rounded-md transition-colors bg-white text-slate-700 shadow-sm">
                                    Tanggal Tunggal
                                </button>
                                <button type="button" onclick="toggleDateMode('range')" id="rangeDateBtn"
                                    class="flex-1 py-2 px-4 text-xs font-medium rounded-md transition-colors text-slate-600 hover:text-slate-700">
                                    Rentang Tanggal
                                </button>
                            </div>
                        </div>

                        <!-- Date Fields -->
                        <div id="singleDateSection" class="space-y-4">
                            <div>
                                <label for="tanggal_acara" class="block text-xs font-medium text-slate-700 mb-2">
                                    Tanggal Agenda <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tanggal_acara" name="tanggal_acara"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-white text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>

                        <div id="rangeDateSection" class="hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="tanggal_mulai" class="block text-xs font-medium text-slate-700 mb-2">
                                        Tanggal Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                        class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-white text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div>
                                    <label for="tanggal_selesai" class="block text-xs font-medium text-slate-700 mb-2">
                                        Tanggal Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                        class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-white text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-slate-200">

                        <div class="flex space-x-3">
                            <button type="button" id="deleteBtn" onclick="confirmDelete()"
                                class="px-4 py-2 text-xs font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors hidden">
                                Hapus
                            </button>
                            <button type="button" onclick="closeEventModal()"
                                class="px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors">
                                Batal
                            </button>
                            <button type="submit" id="saveBtn"
                                class="px-4 py-2 text-xs font-medium text-white bg-slate-600 border border-transparent rounded-lg hover:bg-slate-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventDetailModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEventDetailModal()">
            </div>

            <!-- Modal panel -->
            <div
                class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Detail Agenda</h3>
                    <button onclick="closeEventDetailModal()"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="eventDetailContent">
                    <!-- Event details will be loaded here -->
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-slate-200">
                    <button onclick="closeEventDetailModal()"
                        class="px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors">
                        Tutup
                    </button>
                    @role('admin')
                        <button onclick="editEventFromDetail()"
                            class="px-4 py-2 text-xs font-medium text-white bg-slate-600 border border-transparent rounded-lg hover:bg-slate-700 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors">
                            Edit Agenda
                        </button>
                    @endrole
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <!-- FullCalendar v6 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>

        <style>
            /* TOOLBAR STYLING - DESKTOP & MOBILE */
            .fc-toolbar {
                flex-wrap: nowrap !important;
                /* Tidak wrap untuk mobile */
                gap: 1rem;
                margin-bottom: 1.5rem;
                padding: 0.75rem 0;
                align-items: center;
                justify-content: space-between;
                /* Space between title dan navigation */
            }

            /* TITLE STYLING */
            .fc-toolbar-title {
                font-size: 18px !important;
                font-weight: 600;
                color: black;
                /* Primary color */
                margin: 0 !important;
                flex: 1;
                /* Ambil ruang yang tersedia */
                text-align: left !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* TOOLBAR CHUNKS */
            .fc-toolbar-chunk {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            /* LEFT CHUNK (TITLE) */
            .fc-toolbar-chunk:first-child {
                flex: 1;
                min-width: 0;
            }

            /* RIGHT CHUNK (NAVIGATION) */
            .fc-toolbar-chunk:last-child {
                flex-shrink: 0;
            }

            /* NAVIGATION BUTTONS STYLING - PRIMARY THEME */
            .fc-button {
                background: white !important;
                /* Primary-50 */
                border: 1px solid #d1d5db !important;
                /* Primary-100 */
                color: black !important;
                /* Primary-700 */
                padding: 0.525rem 1rem !important;
                border-radius: 0.5rem !important;
                font-size: 0.875rem !important;
                font-weight: 500 !important;
                transition: all 0.2s ease !important;
                min-height: 38px !important;
                margin: 0 0.125rem !important;
                /* Jarak antar button lebih kecil */
            }

            .fc-button:hover {
                background: rgb(227 239 249 / 0.5) !important;
            }

            .fc-button-primary:not(:disabled).fc-button-active {
                background: #0b5095 !important;
                /* Primary-700 */
                border-color: #0b5095 !important;
                color: white !important;
                box-shadow: 0 2px 4px rgba(11, 80, 149, 0.2) !important;
            }

            /* EVENT STYLING - PRIMARY GRADIENT */
            .fc-event {
                border: none !important;
                background: #475569 !important;
                /* Primary gradient */
                border-radius: 0.375rem !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 12px !important;
                font-weight: 500 !important;
                cursor: pointer !important;
                box-shadow: 0 1px 3px rgba(11, 80, 149, 0.15) !important;
                transition: all 0.2s !important;
                color: white !important;
            }

            .fc-event:hover {
                background: #334155 !important;
                /* Primary hover gradient */
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 8px rgba(11, 80, 149, 0.25) !important;
            }

            .fc-daygrid-event {
                margin: 2px !important;
            }

            /* TODAY HIGHLIGHTING - PRIMARY THEME */
            .fc-day-today {
                background: #edf1f6 !important;
                /* Primary-50 */
                border: 1px solid #a0c8ec !important;
                /* Primary-200 */
            }

            .fc-day-past {
                background: #fafafa !important;
            }

            .fc-daygrid-day:hover {
                background: #f8fafc !important;
                cursor: pointer !important;
            }

            /* HEADER STYLING */
            .fc-col-header-cell {
                background: white !important;
                /* Secondary color */
                border-color: #e2e8f0 !important;
                font-weight: 200 !important;
                color: #374151 !important;
                font-size: 14px !important;
                /* Primary-700 */
                padding: 0.5rem 0.5rem !important;
            }

            .fc-daygrid-day-number {
                font-weight: 300 !important;
                color: #374151 !important;
                padding: 1rem !important;
            }

            /* MOBILE OPTIMIZATIONS - ROW LAYOUT */
            @media (max-width: 768px) {
                .fc-toolbar {
                    flex-direction: row !important;
                    /* FORCE ROW LAYOUT */
                    align-items: center !important;
                    justify-content: space-between !important;
                    flex-wrap: nowrap !important;
                    gap: 0.5rem;
                    padding: 0.5rem 0;
                }

                /* MOBILE TITLE */
                .fc-toolbar-title {
                    font-size: 1.1rem !important;
                    font-weight: 600 !important;
                    margin: 0 !important;
                    flex: 1 !important;
                    text-align: left !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                    min-width: 0 !important;
                }

                /* MOBILE TOOLBAR CHUNKS */
                .fc-toolbar-chunk {
                    display: flex !important;
                    align-items: center !important;
                    gap: 0.25rem !important;
                    margin: 0 !important;
                }

                /* MOBILE LEFT CHUNK (TITLE) */
                .fc-toolbar-chunk:first-child {
                    flex: 1 !important;
                    min-width: 0 !important;
                    justify-content: flex-start !important;
                }

                /* MOBILE RIGHT CHUNK (NAVIGATION) */
                .fc-toolbar-chunk:last-child {
                    flex-shrink: 0 !important;
                    justify-content: flex-end !important;
                }

                /* MOBILE BUTTONS */
                .fc-button {
                    font-size: 0.7rem !important;
                    padding: 0.375rem 0.625rem !important;
                    min-height: 32px !important;
                    margin: 0 0.125rem !important;
                    border-radius: 0.375rem !important;
                }

                /* MOBILE CALENDAR CELLS */
                .fc-daygrid-event {
                    font-size: 0.75rem !important;
                    margin: 1px !important;
                    padding: 0.125rem 0.375rem !important;
                }

                .fc-col-header-cell {
                    font-size: 0.75rem !important;
                    padding: 0.75rem 0.25rem !important;
                }

                .fc-daygrid-day-number {
                    font-size: 0.875rem !important;
                    padding: 0.375rem !important;
                }
            }

            /* EXTRA SMALL MOBILE (< 480px) */
            @media (max-width: 480px) {
                .fc-toolbar-title {
                    font-size: 1rem !important;
                }

                .fc-button {
                    font-size: 0.65rem !important;
                    padding: 0.25rem 0.5rem !important;
                    min-height: 28px !important;
                }
            }

            /* LOADING STATES */
            .btn-loading {
                position: relative;
                color: transparent !important;
            }

            .btn-loading::after {
                content: "";
                position: absolute;
                width: 16px;
                height: 16px;
                top: 50%;
                left: 50%;
                margin-left: -8px;
                margin-top: -8px;
                border: 2px solid #ffffff;
                border-radius: 50%;
                border-top-color: transparent;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* ENHANCED MODAL STYLES */
            .modal-enter {
                opacity: 0;
                transform: scale(0.95);
            }

            .modal-enter-active {
                opacity: 1;
                transform: scale(1);
                transition: all 0.2s ease-out;
            }

            /* DATE MODE TOGGLE - PRIMARY THEME */
            .date-mode-active {
                background: #0b5095 !important;
                /* Primary-700 */
                color: white !important;
                box-shadow: 0 2px 4px rgba(11, 80, 149, 0.2) !important;
            }

            .date-mode-inactive {
                background: #e3eff9 !important;
                /* Primary-50 */
                color: #0b5095 !important;
                /* Primary-700 */
            }

            /* ADDITIONAL ENHANCEMENTS */

            /* Event text contrast */
            .fc-event-title {
                color: white !important;
                font-weight: 500 !important;
            }

            /* More link styling */
            .fc-more-link {
                color: #0b5095 !important;
                /* Primary-700 */
                font-weight: 500 !important;
            }

            .fc-more-link:hover {
                color: #083f75 !important;
                /* Primary-800 */
                text-decoration: underline !important;
            }

            /* Popover styling */
            .fc-popover {
                border: 1px solid #c6def3 !important;
                /* Primary-100 */
                box-shadow: 0 4px 6px rgba(11, 80, 149, 0.1) !important;
            }

            .fc-popover-header {
                background: #e3eff9 !important;
                /* Primary-50 */
                color: #0b5095 !important;
                /* Primary-700 */
                font-weight: 600 !important;
            }

            /* Calendar border styling */
            .fc-theme-standard td,
            .fc-theme-standard th {
                border-color: #e5e7eb !important;
            }

            /* Focus states for accessibility */
            .fc-button:focus {
                outline: 2px solid #79afe3 !important;
                /* Primary-300 */
                outline-offset: 2px !important;
            }

            .fc-daygrid-day:focus {
                outline: 2px solid #79afe3 !important;
                /* Primary-300 */
                outline-offset: -2px !important;
            }

            /* Calendar container enhancement */
            #calendar {
                border-radius: 0.5rem;
                overflow: hidden;
            }

            /* Responsive table for very small screens */
            @media (max-width: 375px) {
                .fc-col-header-cell {
                    padding: 0.5rem 0.125rem !important;
                    font-size: 0.7rem !important;
                }

                .fc-daygrid-day-number {
                    font-size: 0.8rem !important;
                    padding: 0.25rem !important;
                }

                .fc-toolbar-title {
                    font-size: 0.9rem !important;
                }
            }
        </style>

        <script>
            let calendar;
            let currentEvent = null;
            let isEditMode = false;
            let currentDateMode = 'single';

            document.addEventListener('DOMContentLoaded', function() {
                initializeCalendar();
                setupEventListeners();
            });

            function initializeCalendar() {
                const calendarEl = document.getElementById('calendar');

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'title',
                        center: '',
                        right: 'prev,next',
                    },
                    height: 'auto',
                    locale: 'id',
                    firstDay: 1,
                    weekends: true,
                    dayMaxEvents: 20,
                    moreLinkClick: 'popover',
                    eventDisplay: 'block',
                    displayEventTime: false,

                    events: {
                        url: '{{ route('kalender-akademik.events') }}',
                        method: 'GET',
                        failure: function(error) {
                            showError('Gagal memuat data kalender');
                        }
                    },

                    eventClick: function(info) {
                        showEventDetail(info.event);
                    },

                    dateClick: function(info) {
                        @role('admin')
                            openAddModal(info.dateStr);
                        @endrole
                    },

                    eventDidMount: function(info) {
                        // Tooltip
                        const startDate = new Date(info.event.start);
                        const endDate = info.event.end ? new Date(info.event.end) : null;

                        let tooltipText = info.event.title;
                        if (endDate && endDate > startDate) {
                            tooltipText +=
                                `\n${formatDate(startDate)} - ${formatDate(new Date(endDate.getTime() - 24*60*60*1000))}`;
                        } else {
                            tooltipText += `\n${formatDate(startDate)}`;
                        }

                        if (info.event.extendedProps && info.event.extendedProps.deskripsi) {
                            tooltipText += `\n${info.event.extendedProps.deskripsi}`;
                        }

                        info.el.title = tooltipText;

                        // Double click handler
                        info.el.addEventListener('dblclick', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            const eventId = info.event.id;
                            if (!eventId || !calendar) {
                                showError('Event tidak dapat diakses');
                                return;
                            }

                            const currentEvent = calendar.getEventById(eventId);
                            if (!currentEvent) {
                                showError('Event tidak ditemukan di kalender');
                                return;
                            }

                            openEditModal(currentEvent);
                        });
                    },
                });

                calendar.render();
            }

            function formatDate(date) {
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function setupEventListeners() {
                try {
                    // Search input
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        let searchTimeout;
                        searchInput.addEventListener('input', function(e) {
                            clearTimeout(searchTimeout);
                            searchTimeout = setTimeout(() => {
                                if (calendar) {
                                    calendar.removeAllEventSources();
                                    calendar.addEventSource({
                                        url: '{{ route('kalender-akademik.events') }}',
                                        method: 'GET',
                                        extraParams: {
                                            search: e.target.value
                                        },
                                        failure: function(error) {
                                            showError('Gagal melakukan pencarian');
                                        }
                                    });
                                }
                            }, 300);
                        });
                    }

                    // Keyboard listeners
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            closeEventModal();
                            closeEventDetailModal();
                        }
                    });

                    // Form submit listener
                    const eventForm = document.getElementById('eventForm');
                    if (eventForm) {
                        eventForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            if (e.target.dataset.submitting === 'true') {
                                return false;
                            }

                            saveEvent();
                            return false;
                        });
                    }
                } catch (error) {
                    showError('Terjadi kesalahan saat menginisialisasi event listeners');
                }
            }

            function toggleDateMode(mode) {
                currentDateMode = mode;

                const singleBtn = document.getElementById('singleDateBtn');
                const rangeBtn = document.getElementById('rangeDateBtn');
                const singleSection = document.getElementById('singleDateSection');
                const rangeSection = document.getElementById('rangeDateSection');

                if (mode === 'single') {
                    singleBtn.className =
                        'flex-1 py-2 px-4 text-xs font-medium rounded-md transition-colors bg-white text-slate-700 shadow-sm';
                    rangeBtn.className =
                        'flex-1 py-2 px-4 text-xs font-medium rounded-md transition-colors text-slate-600 hover:text-slate-700';
                    singleSection.classList.remove('hidden');
                    rangeSection.classList.add('hidden');

                    // Clear range date fields
                    const tanggalMulai = document.getElementById('tanggal_mulai');
                    const tanggalSelesai = document.getElementById('tanggal_selesai');
                    if (tanggalMulai) tanggalMulai.value = '';
                    if (tanggalSelesai) tanggalSelesai.value = '';
                } else {
                    singleBtn.className =
                        'flex-1 py-2 px-4 text-xs font-medium rounded-md transition-colors text-slate-600 hover:text-slate-700';
                    rangeBtn.className =
                        'flex-1 py-2 px-4 text-xs font-medium rounded-md transition-colors bg-white text-slate-700 shadow-sm';
                    singleSection.classList.add('hidden');
                    rangeSection.classList.remove('hidden');

                    // Clear single date field
                    const tanggalAcara = document.getElementById('tanggal_acara');
                    if (tanggalAcara) tanggalAcara.value = '';
                }
            }

            function openAddModal(dateStr = null) {
                currentEvent = null;
                isEditMode = false;
                currentDateMode = 'single';

                document.getElementById('modalTitle').textContent = 'Tambah Agenda';
                document.getElementById('eventForm').reset();
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('deleteBtn').classList.add('hidden');

                toggleDateMode('single');

                if (dateStr) {
                    document.getElementById('tanggal_acara').value = dateStr;
                }

                showModal('eventModal');
                setTimeout(() => {
                    const judulInput = document.getElementById('judul');
                    if (judulInput) {
                        judulInput.focus();
                    }
                }, 100);
            }

            function formatDateForInput(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function openEditModal(event) {
                try {
                    if (!event) {
                        showError('Event tidak ditemukan');
                        return;
                    }

                    currentEvent = event;
                    isEditMode = true;

                    // Set modal elements
                    const modalTitle = document.getElementById('modalTitle');
                    const methodField = document.getElementById('methodField');
                    const deleteBtn = document.getElementById('deleteBtn');
                    const eventForm = document.getElementById('eventForm');

                    if (modalTitle) modalTitle.textContent = 'Edit Agenda';
                    if (methodField) methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    if (deleteBtn) deleteBtn.classList.remove('hidden');
                    if (eventForm) eventForm.reset();

                    // Set form values
                    const judulInput = document.getElementById('judul');
                    if (judulInput && event.title) {
                        judulInput.value = event.title;
                    }

                    // Handle dates
                    const startDate = new Date(event.start);
                    let endDate = null;

                    if (event.end) {
                        endDate = new Date(event.end);
                        endDate.setDate(endDate.getDate() - 1);
                    }

                    // Set date mode and values
                    if (!endDate || endDate.getTime() === startDate.getTime()) {
                        toggleDateMode('single');
                        const tanggalAcara = document.getElementById('tanggal_acara');
                        if (tanggalAcara) {
                            tanggalAcara.value = formatDateForInput(startDate);
                        }
                    } else {
                        toggleDateMode('range');
                        const tanggalMulai = document.getElementById('tanggal_mulai');
                        const tanggalSelesai = document.getElementById('tanggal_selesai');

                        if (tanggalMulai) tanggalMulai.value = formatDateForInput(startDate);
                        if (tanggalSelesai) tanggalSelesai.value = formatDateForInput(endDate);
                    }

                    showModal('eventModal');

                    setTimeout(() => {
                        const judulInput = document.getElementById('judul');
                        if (judulInput) {
                            judulInput.focus();
                        }
                    }, 100);

                } catch (error) {
                    showError('Terjadi kesalahan saat membuka form edit');
                }
            }

            function closeEventModal() {
                hideModal('eventModal');
                currentEvent = null;
                isEditMode = false;
                currentDateMode = 'single';

                const form = document.getElementById('eventForm');
                const saveBtn = document.getElementById('saveBtn');

                if (form) {
                    form.reset();
                    form.dataset.submitting = 'false';
                }

                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Simpan Acara';
                    saveBtn.classList.remove('btn-loading');
                }

                toggleDateMode('single');
            }

            function showEventDetail(event) {
                const modal = document.getElementById('eventDetailModal');
                const content = document.getElementById('eventDetailContent');

                currentEvent = event;

                const startDate = new Date(event.start);
                let endDate = event.end ? new Date(event.end) : null;
                let dateText = '';

                if (endDate) {
                    endDate.setDate(endDate.getDate() - 1);
                    if (endDate.getTime() === startDate.getTime()) {
                        dateText = formatDate(startDate);
                    } else {
                        dateText = `${formatDate(startDate)} - ${formatDate(endDate)}`;
                    }
                } else {
                    dateText = formatDate(startDate);
                }

                content.innerHTML = `
            <div class="space-y-4">
                <div>
                    <h4 class="text-lg font-semibold text-slate-900 mb-3">${event.title}</h4>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center mt-0.5">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-700 mb-1">Tanggal</p>
                        <p class="text-xs text-slate-600">${dateText}</p>
                    </div>
                </div>
            </div>
        `;

                showModal('eventDetailModal');
            }

            function closeEventDetailModal() {
                hideModal('eventDetailModal');
            }

            function editEventFromDetail() {
                try {
                    const eventToEdit = currentEvent;

                    if (!eventToEdit || !eventToEdit.id) {
                        showError('Event tidak tersedia untuk diedit');
                        return;
                    }

                    const detailModal = document.getElementById('eventDetailModal');
                    if (detailModal) {
                        detailModal.classList.add('hidden');
                    }

                    setTimeout(() => {
                        if (calendar) {
                            const freshEvent = calendar.getEventById(eventToEdit.id);
                            if (freshEvent) {
                                openEditModal(freshEvent);
                            } else {
                                openEditModal(eventToEdit);
                            }
                        } else {
                            openEditModal(eventToEdit);
                        }
                    }, 100);

                } catch (error) {
                    showError('Terjadi kesalahan saat membuka form edit');
                }
            }

            function saveEvent() {
                try {
                    const form = document.getElementById('eventForm');
                    const saveBtn = document.getElementById('saveBtn');

                    if (!form) {
                        showError('Form tidak ditemukan');
                        return;
                    }

                    if (form.dataset.submitting === 'true') {
                        return;
                    }

                    // Validate form
                    const judulInput = document.getElementById('judul');
                    if (!judulInput) {
                        showError('Field judul tidak ditemukan');
                        return;
                    }

                    const judul = judulInput.value.trim();
                    if (!judul) {
                        showError('Judul acara wajib diisi');
                        return;
                    }

                    // Get date data
                    let tanggalData = {};

                    if (currentDateMode === 'single') {
                        const tanggalAcaraInput = document.getElementById('tanggal_acara');
                        if (!tanggalAcaraInput) {
                            showError('Field tanggal acara tidak ditemukan');
                            return;
                        }

                        const tanggalAcara = tanggalAcaraInput.value;
                        if (!tanggalAcara) {
                            showError('Tanggal acara wajib diisi');
                            return;
                        }

                        tanggalData = {
                            tanggal_mulai: tanggalAcara,
                            tanggal_selesai: tanggalAcara
                        };
                    } else {
                        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
                        const tanggalSelesaiInput = document.getElementById('tanggal_selesai');

                        if (!tanggalMulaiInput || !tanggalSelesaiInput) {
                            showError('Field tanggal mulai/selesai tidak ditemukan');
                            return;
                        }

                        const tanggalMulai = tanggalMulaiInput.value;
                        const tanggalSelesai = tanggalSelesaiInput.value;

                        if (!tanggalMulai || !tanggalSelesai) {
                            showError('Tanggal mulai dan selesai wajib diisi');
                            return;
                        }

                        if (new Date(tanggalSelesai) < new Date(tanggalMulai)) {
                            showError('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                            return;
                        }

                        tanggalData = {
                            tanggal_mulai: tanggalMulai,
                            tanggal_selesai: tanggalSelesai
                        };
                    }

                    // Set loading state
                    form.dataset.submitting = 'true';

                    if (saveBtn) {
                        saveBtn.disabled = true;
                        saveBtn.classList.add('btn-loading');
                        saveBtn.textContent = 'Menyimpan...';
                    }

                    // Prepare form data
                    const formData = {
                        judul: judul,
                        ...tanggalData,
                        is_all_day: 1,
                        _token: '{{ csrf_token() }}'
                    };

                    // Determine URL and method
                    let url, method;
                    if (isEditMode && currentEvent && currentEvent.id) {
                        url = `{{ route('kalender-akademik.index') }}/${currentEvent.id}`;
                        method = 'PUT';
                        formData._method = 'PUT';
                    } else {
                        url = '{{ route('kalender-akademik.store') }}';
                        method = 'POST';
                    }

                    // Send AJAX request
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(formData)
                        })
                        .then(response => {
                            // Reset loading state
                            form.dataset.submitting = 'false';

                            if (saveBtn) {
                                saveBtn.disabled = false;
                                saveBtn.classList.remove('btn-loading');
                                saveBtn.textContent = 'Simpan Acara';
                            }

                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                closeEventModal();

                                setTimeout(() => {
                                    if (calendar) {
                                        calendar.refetchEvents();
                                    }
                                }, 100);

                                const message = data.message || (isEditMode ? 'Acara berhasil diperbarui' :
                                    'Acara berhasil disimpan');
                                showSuccess(message);
                            } else {
                                showError(data.message || 'Terjadi kesalahan saat menyimpan');
                            }
                        })
                        .catch(error => {
                            // Reset loading state on error
                            form.dataset.submitting = 'false';

                            if (saveBtn) {
                                saveBtn.disabled = false;
                                saveBtn.classList.remove('btn-loading');
                                saveBtn.textContent = 'Simpan Acara';
                            }

                            let errorMessage = 'Terjadi kesalahan saat menyimpan acara';

                            if (error.errors && typeof error.errors === 'object') {
                                const firstError = Object.values(error.errors)[0];
                                if (Array.isArray(firstError) && firstError[0]) {
                                    errorMessage = firstError[0];
                                }
                            } else if (error.message) {
                                errorMessage = error.message;
                            }

                            showError(errorMessage);
                        });

                } catch (error) {
                    // Reset form state on catch error
                    const form = document.getElementById('eventForm');
                    const saveBtn = document.getElementById('saveBtn');

                    if (form) {
                        form.dataset.submitting = 'false';
                    }

                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.classList.remove('btn-loading');
                        saveBtn.textContent = 'Simpan Acara';
                    }

                    showError('Terjadi kesalahan sistem');
                }
            }

            function confirmDelete() {
                if (!currentEvent) return;

                if (confirm(
                        `Apakah Anda yakin ingin menghapus acara "${currentEvent.title}"?\n\nTindakan ini tidak dapat dibatalkan.`
                    )) {
                    deleteEvent();
                }
            }

            function deleteEvent() {
                if (!currentEvent) return;

                const deleteBtn = document.getElementById('deleteBtn');

                if (deleteBtn && deleteBtn.dataset.deleting === 'true') {
                    return;
                }

                if (deleteBtn) {
                    deleteBtn.dataset.deleting = 'true';
                    deleteBtn.disabled = true;
                    deleteBtn.classList.add('btn-loading');
                    deleteBtn.textContent = 'Menghapus...';
                }

                fetch(`{{ route('kalender-akademik.index') }}/${currentEvent.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (deleteBtn) {
                            deleteBtn.dataset.deleting = 'false';
                            deleteBtn.disabled = false;
                            deleteBtn.classList.remove('btn-loading');
                            deleteBtn.textContent = 'Hapus Acara';
                        }

                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            closeEventModal();
                            closeEventDetailModal();

                            setTimeout(() => {
                                calendar.refetchEvents();
                            }, 100);

                            showSuccess(data.message || 'Acara berhasil dihapus');
                        } else {
                            showError(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        if (deleteBtn) {
                            deleteBtn.dataset.deleting = 'false';
                            deleteBtn.disabled = false;
                            deleteBtn.classList.remove('btn-loading');
                            deleteBtn.textContent = 'Hapus Acara';
                        }

                        let errorMessage = 'Terjadi kesalahan saat menghapus acara';
                        if (error.message) {
                            errorMessage = error.message;
                        }

                        showError(errorMessage);
                    });
            }

            // Utility Functions
            function showModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    requestAnimationFrame(() => {
                        const content = modal.querySelector('div > div');
                        if (content) {
                            content.classList.add('modal-enter-active');
                        }
                    });
                }
            }

            function hideModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const content = modal.querySelector('div > div');
                    if (content) {
                        content.classList.remove('modal-enter-active');
                    }
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 150);
                }
            }

            function showSuccess(message) {
                if (window.showAlert) {
                    window.showAlert('success', 'Berhasil', message);
                } else if (window.showSuccess) {
                    window.showSuccess(message);
                } else {
                    createNotification('success', message);
                }
            }

            function showError(message) {
                if (window.showAlert) {
                    window.showAlert('error', 'Gagal', message);
                } else if (window.showError) {
                    window.showError(message);
                } else {
                    createNotification('error', message);
                }
            }

            function createNotification(type, message) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-[10000] p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            // Global functions
            window.openAddModal = openAddModal;
            window.closeEventModal = closeEventModal;
            window.closeEventDetailModal = closeEventDetailModal;
            window.editEventFromDetail = editEventFromDetail;
            window.confirmDelete = confirmDelete;
            window.toggleDateMode = toggleDateMode;
            window.calendar = calendar;
        </script>
    @endpush
@endsection

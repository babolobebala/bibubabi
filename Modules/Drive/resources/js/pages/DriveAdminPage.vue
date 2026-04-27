<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'drive-admin');
</script>

<template>
    <ModuleContentShell :module="'drive'" body-variant="page" :breadcrumbs="pageBreadcrumbs">
        <div class="p-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Admin Drive</h1>
                <p class="text-gray-500">Kelola daftar link drive dan pengaturan akses modul Drive.</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700">Daftar Link Drive</h3>
                    <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Link
                    </button>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">Nama Drive</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">URL / Link</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">Google Drive Utama</td>
                                <td class="px-6 py-4 text-blue-600 underline text-sm truncate max-w-[200px]">https://drive.google.com/...</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">Arsip Backup</td>
                                <td class="px-6 py-4 text-blue-600 underline text-sm truncate max-w-[200px]">https://drive.google.com/...</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </ModuleContentShell>
</template>

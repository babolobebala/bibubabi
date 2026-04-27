<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'drive-index');
</script>

<template>
    <ModuleContentShell :module="'drive'" body-variant="hub" :breadcrumbs="pageBreadcrumbs">
        <div class="flex flex-col items-center justify-center min-h-[400px] text-center p-8">
            <div class="w-24 h-24 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0112 3m0 0a10.003 10.003 0 017.143 17.513l-.054.091m-9.696-10.74A4 4 0 1115.95 6.05M12 11a4 4 0 10-3.95-4.95M12 11c0 3.517 1.009 6.799-2.753 9.571M12 11c1.744 2.772 2.753 6.054 2.753 9.571m-9.696-10.74c.903 0 1.744.25 2.463.689M12 11c-.903 0-1.744.25-2.463.689m9.156-10.74c-.903 0-1.744.25-2.463.689M12 11c.903 0 1.744.25 2.463.689m-9.156 0c.903 0 1.744-.25 2.463-.689" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4 tracking-tight">Kumpulan Drive</h1>
            <p class="text-lg text-gray-600 max-w-lg mb-8 leading-relaxed">
                Akses cepat ke berbagai penyimpanan dokumen digital dan resource penting melalui daftar drive berikut.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-3xl">
                <a href="#" class="group p-6 bg-white border border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-gray-900">Google Drive Utama</h3>
                            <p class="text-sm text-gray-500">Penyimpanan dokumen operasional harian.</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="group p-6 bg-white border border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-gray-900">Arsip Backup</h3>
                            <p class="text-sm text-gray-500">Dokumen lama dan arsip data historis.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </ModuleContentShell>
</template>

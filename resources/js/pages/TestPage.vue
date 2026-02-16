<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    canInstallPwa,
    getDeferredInstallPrompt,
    isInstalled,
    isIosDevice,
    subscribeInstallState,
} from '@/lib/pwa-install';

type NotificationHistoryItem = {
    id: string;
    type: string;
    data: {
        title?: string;
        body?: string;
        sent_at?: string;
    };
    read_at: string | null;
    created_at: string | null;
};

const showInstallButton = ref(false);
const showIosHint = ref(false);
const isLoadingHistory = ref(false);
const unreadCount = ref(0);
const notificationHistory = ref<NotificationHistoryItem[]>([]);
let unsubscribeInstallState: (() => void) | null = null;

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

function syncInstallUi(): void {
    showInstallButton.value = canInstallPwa();
    showIosHint.value = isIosDevice() && !isInstalled() && !showInstallButton.value;
}

async function installApp(): Promise<void> {
    const deferredPrompt = getDeferredInstallPrompt();

    if (!deferredPrompt) {
        return;
    }

    await deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    syncInstallUi();
}

async function fetchNotificationHistory(): Promise<void> {
    isLoadingHistory.value = true;

    try {
        const response = await fetch('/notifications/history?limit=20', {
            credentials: 'same-origin',
        });
        const payload = await response.json();

        notificationHistory.value = Array.isArray(payload.items) ? payload.items : [];
        unreadCount.value = Number(payload.unread_count ?? 0);
    } catch (error) {
        console.error('Failed to load notification history.', error);
    } finally {
        isLoadingHistory.value = false;
    }
}

async function markNotificationsAsRead(ids: string[] = []): Promise<void> {
    if (!csrfToken) {
        return;
    }

    try {
        await fetch('/notifications/read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ ids }),
        });

        await fetchNotificationHistory();
    } catch (error) {
        console.error('Failed to mark notifications as read.', error);
    }
}

onMounted(() => {
    syncInstallUi();
    unsubscribeInstallState = subscribeInstallState(syncInstallUi);
    void fetchNotificationHistory();
});

onBeforeUnmount(() => {
    unsubscribeInstallState?.();
});
</script>

<template>
    <Button v-if="showInstallButton" class="w-full transform rounded-md" @click="installApp">Install App</Button>

    <div v-if="showIosHint" class="rounded-md border p-3 text-sm">
        Install di iPhone/iPad: tekan Share lalu pilih Add to Home Screen.
    </div>

    <Button class="w-full transform rounded-md" as-child>
        <a href="test_auth"> Cek Sini untuk Authorisasi </a>
    </Button>

    <Button id="enable-push">Enable push notifications</Button>
    <Button id="disable-push">Disable push notifications</Button>
    <Button id="clear-push-debug" variant="outline">Clear push debug log</Button>
    <br />
    <a href="/notifications/send?target=all">Send to all users</a>
    <br />
    <a href="/notifications/send?target=role&role=super_admin">Send to super_admin users</a>

    <div class="mt-4 rounded-md border p-3">
        <div class="mb-2 flex items-center justify-between">
            <p class="text-sm font-semibold">Notification History (Unread: {{ unreadCount }})</p>
            <Button variant="outline" @click="markNotificationsAsRead([])">Mark all as read</Button>
        </div>

        <p v-if="isLoadingHistory" class="text-xs">Loading notification history...</p>
        <p v-else-if="notificationHistory.length === 0" class="text-xs">Belum ada notifikasi untuk user ini.</p>

        <div v-else class="space-y-2">
            <div
                v-for="item in notificationHistory"
                :key="item.id"
                class="rounded-md border p-2 text-sm"
            >
                <p class="font-semibold">{{ item.data?.title ?? 'Tanpa judul' }}</p>
                <p class="text-xs">{{ item.data?.body ?? '-' }}</p>
                <p class="text-xs">Waktu: {{ item.created_at ?? '-' }}</p>
                <p class="text-xs">Status: {{ item.read_at ? 'Read' : 'Unread' }}</p>
                <Button
                    v-if="!item.read_at"
                    class="mt-2"
                    variant="outline"
                    @click="markNotificationsAsRead([item.id])"
                >
                    Mark as read
                </Button>
            </div>
        </div>
    </div>

    <div class="mt-4 rounded-md border p-3">
        <p class="mb-2 text-sm font-semibold">Push Debug Log</p>
        <pre id="push-debug-log" class="max-h-64 overflow-auto whitespace-pre-wrap text-xs"></pre>
    </div>

    <Button class="w-full transform rounded-md" as-child>
        <a href="logout"> Logout </a>
    </Button>
</template>

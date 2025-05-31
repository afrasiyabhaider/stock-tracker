<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import {useToast} from 'vue-toast-notification';
import InputError from '@/components/InputError.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Stock History',
        href: '/history',
    },
];

const history = ref<any[]>([]);
const loading = ref(false);
const error = ref('');

const filters = ref({
    symbol: '',
    start: '',
    end: '',
});

async function fetchHistory() {
    loading.value = true;
    error.value = '';
    try {
        const params: Record<string, string> = {};
        if (filters.value.symbol) params.symbol = filters.value.symbol;
        if (filters.value.start) params.start = filters.value.start;
        if (filters.value.end) params.end = filters.value.end;

        const response = await axios.get('/api/stock/history', { params });
        history.value = response.data.data || [];
    } catch (e: any) {
        const $toast = useToast();
        if(e.response.status === 401) {
            fetchHistory();
        }else{
            error.value = e.response?.data?.message || 'Network error.';
            history.value = e.response.data.data || [];
            $toast.error(error.value, {
                    position: 'top-right',
                    duration: 5000,
                    dismissible: true,
                });
        }

    } finally {
        loading.value = false;
    }
}

onMounted(fetchHistory);
</script>

<template>
    <Head title="History" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <h1 class="text-xl font-bold mb-4">Stock Search History</h1>
            <div v-if="loading" class="mb-4">Loading...</div>
            <form
                class="flex flex-wrap gap-4 mb-6 items-end bg-white dark:bg-zinc-900 p-4 rounded-lg shadow-sm border"
                @submit.prevent="fetchHistory"
            >
                <div class="flex flex-col">
                    <Label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300" for="symbol">Symbol</Label>
                    <Input
                        id="symbol"
                        v-model="filters.symbol"
                        type="text"
                        class="border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-zinc-800 text-sm"
                        placeholder="e.g. AAPL"
                        autocomplete="off"
                    />
                </div>
                <div class="flex flex-col">
                    <Label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300" for="start">Start Date</Label>
                    <Input
                        id="start"
                        v-model="filters.start"
                        type="date"
                        class="border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-zinc-800 text-sm"
                    />
                </div>
                <div class="flex flex-col">
                    <Label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300" for="end">End Date</Label>
                    <Input
                        id="end"
                        v-model="filters.end"
                        type="date"
                        class="border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-zinc-800 text-sm"
                    />
                </div>
                <Button
                    type="submit"
                    :disabled="loading"
                >
                    <span v-if="loading" class="animate-spin mr-2 inline-block align-middle">&#9696;</span>
                    Filter
                </Button>
            </form>

            <div v-if="history.length">
                <table class="table-auto w-full border">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-zinc-800">
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border">Symbol</th>
                            <th class="px-4 py-2 border">Open</th>
                            <th class="px-4 py-2 border">High</th>
                            <th class="px-4 py-2 border">Low</th>
                            <th class="px-4 py-2 border">Close</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="entry in history" :key="entry.id">
                            <td class="px-4 py-2 border">{{ new Date(entry.created_at).toLocaleString() }}</td>
                            <td class="px-4 py-2 border">{{ entry.symbol }}</td>
                            <td class="px-4 py-2 border">{{ entry.open }}</td>
                            <td class="px-4 py-2 border">{{ entry.high }}</td>
                            <td class="px-4 py-2 border">{{ entry.low }}</td>
                            <td class="px-4 py-2 border">{{ entry.close }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else-if="!loading && !error" class="text-gray-500">
                <InputError message="No history found." />
            </div>
        </div>
    </AppLayout>
</template>

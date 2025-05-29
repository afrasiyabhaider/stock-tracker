<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head} from '@inertiajs/vue3';
import { ref } from 'vue';
import {useToast} from 'vue-toast-notification';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Search Stock',
        href: '/stock',
    },
];

const symbol = ref('');
const loading = ref(false);
const error = ref('');
const quote = ref<any>(null);

import axios from 'axios';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import InputError from '@/components/InputError.vue';
async function fetchQuote() {
    error.value = '';
    quote.value = null;
    if (!symbol.value) {
        error.value = 'Please enter a stock symbol.';
        return;
    }
    loading.value = true;
    try {
        const response = await axios.get(`/api/stock/quote`, {
            params: { q: symbol.value }
        });
        // The API returns { success, message, data }
        quote.value = response.data.data;
        if (response.data.code == 200) {
            const $toast = useToast();
            $toast.success(`Fetched quote for "${symbol.value.toUpperCase()}" successfully, please check your email!`, {
                position: 'top-right',
                duration: 5000,
                dismissible: true,
            });
            // Show a toast notification using vue-toastification
            // toast.info('Please check your email for further instructions.');
        }
    } catch (e: any) {
        if (e.response && e.response.data && e.response.data.message) {
            error.value = e.response.data.message;
        } else {
            error.value = e.message || 'Network error.';
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-6 max-w-xl">
                <form @submit.prevent="fetchQuote" class="flex gap-2 items-end">
                    <div>
                        <Label for="symbol" class="block text-sm font-medium">Stock Symbol</Label>
                        <Input id="symbol" v-model="symbol" type="text" class="input input-bordered w-full" placeholder="e.g. IBM or IBM,AAPL,TSLA" maxlength="15" />
                    </div>
                    <Button type="submit" class="btn btn-primary" :disabled="loading">
                        <template #default>
                            {{ loading ? 'Searching...' : 'Get Quote' }}
                        </template>
                    </Button>
                </form>
                <div v-if="error" class="mt-2 text-red-500">
                    <InputError :message="error" />
                </div>
            </div>
            <div v-if="quote && quote.length" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="(item, idx) in quote" :key="item.symbol + idx" class="max-w-xl rounded-xl border p-4 bg-white dark:bg-zinc-900 shadow">
                        <h2 class="text-lg font-semibold mb-2">{{ item.symbol }} [{{ item.name }}]</h2>
                        <div class="grid grid-cols-2 gap-2">
                            <div><span class="font-medium">Name:</span> {{ item.name }}</div>
                            <div><span class="font-medium">Symbol:</span> {{ item.symbol }}</div>
                            <div><span class="font-medium">Open:</span> {{ item.open }}</div>
                            <div><span class="font-medium">High:</span> {{ item.high }}</div>
                            <div><span class="font-medium">Close:</span> {{ item.close }}</div>
                            <div><span class="font-medium">Low:</span> {{ item.low }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

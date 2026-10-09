<template>
    <InertiaLayout
        :page="{ page => ({ title: 'Klien' })}"
    >
        <template #header>
            <h2 class="text-xl font-semibold">Klien</h2>

            <a href="#" class="btn btn-primary" @click="showCreateModal">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                    <path d="M12 5v5h5" />
                    <path d="M5 12h5v5" />
                </svg>
                Tambah Klien
            </a>
        </template>

        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama klien atau email..."
                            class="w-full"
                            @input="fetchClients"
                        />
                    </div>

                    <div class="flex items-end gap-2">
                        <Select
                            v-model="filterStatus"
                            :options="[
                                { value: '', label: 'Semua status' },
                                { value: 'active', label: 'Aktif' },
                                { value: 'inactive', label: 'Non-aktif' },
                                { value: 'archived', label: 'Diarsipkan' },
                            ]"
                            @change="fetchClients"
                        />
                    </div>
                </div>
            </div>

            <div v-if="clients.data.length === 0" class="card-body text-center text-muted-overflow">
                <p>Belum ada data klien. <a href="#" class="underline decoration-neutral-300" @click="showCreateModal">Tambah klien pertama</a></p>
            </div>

            <div v-if="clients.data.length > 0" class="card-body">
                <div class="overflow-x-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead>
                            <tr>
                                <th class="text-left">Nama Klien</th>
                                <th class="text-left">Nama Kontak</th>
                                <th class="text-left">Email</th>
                                <th class="text-left">Telepon</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Dibuat</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients.data" :key="client.id">
                                <td class="font-medium">{{ client.name }}</td>
                                <td>{{ client.contact_name || '-' }}</td>
                                <td>{{ client.email || '-' }}</td>
                                <td>{{ client.phone || '-' }}</td>
                                <td>
                                    <span
                                        :class="['px-2', 'py-1', 'rounded-xs', statusClasses[client.status]]"
                                    >
                                        {{ statusLabels[client.status] || client.status }}</span>
                                </td>
                                <td class="text-xs text-muted-foreground">{{ client.created_at ? client.created_at.split(' ')[0] : '-' }}</td>
                                <td class="text-right">
                                    <a href="#" class="text-primary underline mr-2" @click.prevent="editClient(client)">Edit</a>
                                    <button
                                        @click.prevent="deleteClient(client)"
                                        class="text-danger underline"
                                        :disabled="!canDelete(client)"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-body text-end">
                    <Pagination
                        :total="meta.total"
                        :per-page="meta.per_page"
                        :page="meta.current_page"
                        @page-change="fetchClients"
                    />
                </div>
            </div>
        </div>

        <Modal :visible="createModalVisible" @hide="createModalVisible = false">
            <template #header>
                <h3>Tambah Klien Baru</h3>
            </template>

            <ClientForm
                :mode=" 'create'"
                @save="onClientSave"
                :visible="createModalVisible"
            />
        </Modal>
    </InertiaLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { usePage, useInertia, Link } from '@inertiajs/vue3'
import InertiaLayout from '@/layouts/InertiaLayout.vue'
import Input from '@/components/ui/input/Input.vue'
import Select from '@/components/ui/select/Select.vue'
import ClientForm from '@/components/clients/ClientForm.vue'
import Modal from '@/components/modal/Modal.vue'
import { useTranslate } from '@/composables/useTranslate'

const page = usePage()
const { visit } = useInertia()
const { t } = useTranslate()

const clients = computed(() => page.props.clients)
const meta = computed(() => page.props.meta)
const filters = computed(() => page.props.filters || {})

// Format status label
const statusLabels: Record<string, string> = {
    active: 'Aktif',
    inactive: 'Non-aktif',
    archived: 'Diarsipkan',
}

const statusClasses: Record<string, string> = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800',
    archived: 'bg-gray-100 text-gray-800',
}

// Format status label function
function getStatusLabel(status: string): string {
    return statusLabels[status] || status
}

// Fetch clients with optional filters
function fetchClients() {
    const search = (page.props.filters?.search || '').toString()
    const status = (page.props.filters?.status || '').toString()
    visit(route('clients.index', { search, status }))
}

// Show create modal
function showCreateModal() {
    // The modal will be shown - Inertia will handle data refresh after save
}

// Edit client
function editClient(client: any) {
    // Navigate to detail or show form in edit mode
    alert('Fitur edit akan segera tersedia: ' + client.name)
}

// Delete client
function deleteClient(client: any) {
    if (confirm('Apakah yakin ingin menghapus klien ' + client.name + '?')) {
        // Call API to delete via Inertia
        Inertia.visit(route('clients.destroy', { client: client.id }), {
            method: 'DELETE',
            onSuccess: () => {
                fetchClients()
                alert('Klien berhasil dihapus')
            },
            onError: (error) => {
                alert('Gagal menghapus klien: ' + error.message)
            },
        })
    }
}

// Watch for filter changes
watch(
    [() => page.props.filters],
    () => {
        fetchClients()
    }
)
</script>
<script setup lang="ts">
import { defineEmits, defineProps, ref, watch } from 'vue'
import { usePage, useForm, Link } from '@inertiajs/vue3'
import Input from '@/components/ui/input/Input.vue'
import Select from '@/components/ui/select/Select.vue'
import Textarea from '@/components/ui/textarea/Textarea.vue'
import Button from '@/components/ui/button/Button.vue'
import { useTranslate } from '@/composables/useTranslate'

interface Props {
    mode: 'create' | 'edit'
    visible: boolean
    setVisible: (visible: boolean) => void
}

const props = defineProps<Props>()
const emit = defineEmits(['update:visible'])

const { data, setValue, post, put, processing, errors } = useForm({
    name: '',
    contact_name: '',
    email: '',
    phone: '',
    address: '',
    notes: '',
    status: 'active',
})

const { t } = useTranslate()

// Set initial data for edit mode
if (props.mode === 'edit') {
    const client = page.props.client
    setValue('name', client.name)
    setValue('contact_name', client.contact_name ?? '')
    setValue('email', client.email ?? '')
    setValue('phone', client.phone ?? '')
    setValue('address', client.address ?? '')
    setValue('notes', client.notes ?? '')
    setValue('status', client.status ?? 'active')
}

watch(
    [() => form.name, () => form.contact_name, () => form.email, () => form.phone],
    () => {
        // Reset validation errors when user types
        // errors.clear()
    }
)
</script>

<template>
    <TransitionName name="fade">
        <div class="fixed inset-0 bg-black/30 z-50 hidden/0" @click.self="setVisible(false)">
            <Transition>
                <div class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="bg-white rounded-lg w-full max-w-2xl mx-4 shadow-xl transform transition-all duration-300 scale-100">
                        <div class="p-6 space-y-6">
                            <h2 class="text-xl font-semibold">
                                <template v-if="props.mode === 'create'">Tambah Klien Baru</template>
                                <template v-if="props.mode === 'edit'">Perbarui Klien</template>
                            </h2>

                            <Form
                                :errors="errors"
                                v-slot="{ errors, processing }"
                            >
                                <div>
                                    <Label :for=" 'name'">Nama Klien</Label>
                                    <Input
                                        :id=" 'name'"
                                        v-model="form.name"
                                        required
                                        :placeholder="props.mode === 'create' ? 'Nama klien atau perusahaan' : 'Nama klien'"
                                    />
                                    <Error :message="errors.name" />
                                </div>

                                <div>
                                    <Label :for=" 'contact_name'">Nama Kontak</Label>
                                    <Input
                                        :id=" 'contact_name'"
                                        v-model="form.contact_name"
                                        :placeholder="props.mode === 'create' ? 'Nama kontak utama' : 'Nama kontak'"
                                    />
                                </div>

                                <div>
                                    <Label :for=" 'email'">Email</Label>
                                    <Input
                                        :id=" 'email'"
                                        v-model="form.email"
                                        type="email"
                                        :placeholder="props.mode === 'create' ? 'email@contoh.com' : 'Email klien'"
                                    />
                                    <Error :message="errors.email" />
                                </div>

                                <div>
                                    <Label :for=" 'phone'">Telepon</Label>
                                    <Input
                                        :id=" 'phone'"
                                        v-model="form.phone"
                                        type="tel"
                                        :placeholder="props.mode === 'create' ? 'Nomor telepon' : 'Nomor telepon'"
                                    />
                                </div>

                                <div>
                                    <Label :for=" 'address'">Alamat</Label>
                                    <Textarea
                                        :id=" 'address'"
                                        v-model="form.address"
                                        :placeholder="props.mode === 'create' ? 'Alamat klien' : 'Alamat klien'"
                                    />
                                </div>

                                <div>
                                    <Label :for=" 'notes'">Catatan</Label>
                                    <Textarea
                                        :id=" 'notes'"
                                        v-model="form.notes"
                                        :placeholder="props.mode === 'create' ? 'Catatan internal' : 'Catatan'"
                                    />
                                </div>

                                <div>
                                    <Label :for=" 'status'">Status</Label>
                                    <Select
                                        v-model="form.status"
                                        :options="[
                                            { value: 'active', label: 'Aktif' },
                                            { value: 'inactive', label: 'Non-aktif' },
                                            { value: 'archived', label: 'Diarsipkan' },
                                        ]"
                                    />
                                </div>

                                <div class="flex items-center gap-4">
                                    <Button
                                        :disabled="processing"
                                        type="submit"
                                    >
                                        <template v-if="props.mode === 'create'">Simpan Klien</template>
                                        <template v-if="props.mode === 'edit'">Perbarui</template>
                                    </Button>

                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        @click="setVisible(false)"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>
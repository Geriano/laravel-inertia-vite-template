<script setup>
import { getCurrentInstance, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Card from '@/Components/Card.vue'
import Icon from '@/Components/Icon.vue'
import Builder from '@/Components/DataTable/Builder.vue'
import Th from '@/Components/DataTable/Th.vue'
import Swal from 'sweetalert2'
import Select from '@vueform/multiselect'
import Modal from '@/Components/Modal.vue'
import Close from '@/Components/Button/Close.vue'
import ButtonGreen from '@/Components/Button/Green.vue'
import ButtonBlue from '@/Components/Button/Blue.vue'
import ButtonRed from '@/Components/Button/Red.vue'

const self = getCurrentInstance()
const { permissions } = defineProps({
  permissions: Array,
})
const form = useForm({
  id: null,
  name: '',
  permissions: [],
})

const tableRefresh = ref(null)
const open = ref(false)

const show = () => {
  open.value = true
  nextTick(() => self.refs.name?.focus())
}

const close = () => {
  open.value = false
  form.reset()
}

const detach = async (role, permission, refresh) => {
  const response = await Swal.fire({
    title: 'are you sure?',
    icon: 'question',
    showCloseButton: true,
    showCancelButton: true,
  })

  if (!response.isConfirmed)
    return

  Inertia.on('finish', () => refresh())
  Inertia.patch(route('superuser.role.detach', { role: role.id, permission: permission.id }))
}

const store = () => {
  return form.post(route('superuser.role.store'), {
    onSuccess: () => close() || (tableRefresh.value && tableRefresh.value()),
    onError: () => show(),
  })
}

const edit = role => {
  form.id = role.id
  form.name = role.name
  form.permissions = role.permissions.map(permission => permission.id)

  show()
}

const update = () => {
  return form.patch(route('superuser.role.update', form.id), {
    onSuccess: () => close() || (tableRefresh.value && tableRefresh.value()),
    onError: () => show(),
  })
}

const destroy = async (role, refresh) => {
  const response = await Swal.fire({
    title: 'Are you sure?',
    text: 'You can\'t restore it after deleted',
    icon: 'question',
    showCancelButton: true,
    showCloseButton: true,
  })

  if (response.isConfirmed) {
    Inertia.on('finish', () => refresh())

    return Inertia.delete(route('superuser.role.destroy', role.id))
  }
}

const submit = () => form.id ? update() : store()

const esc = e => e.key === 'Escape' && close()
onMounted(() => window.addEventListener('keydown', esc))
onUnmounted(() => window.removeEventListener('keydown', esc))
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <DashboardLayout title="Role">
    <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
      <template #header>
        <div class="flex items-center space-x-2 p-2 bg-gray-200 dark:bg-gray-800">
          <ButtonGreen v-if="can('create role')" @click.prevent="show">
            <Icon name="plus" />
            <p class="uppercase font-semibold">create</p>
          </ButtonGreen>
        </div>
      </template>

      <template #body>
        <div class="flex flex-col space-y-2">
          <Builder :url="route('api.v1.superuser.role.paginate')">
            <template v-slot:thead="table">
              <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
                <Th class="border px-3 py-2 text-center" :table="table" :sort="false">no</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="name">name</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">permissions</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">action</Th>
              </tr>
            </template>

            <template v-slot:tfoot="table">
              <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
                <Th class="border px-3 py-2 text-center" :table="table" :sort="false">no</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">name</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">permissions</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">action</Th>
              </tr>
            </template>

            <template v-slot:tbody="{ data, processing, empty, refresh }">
              <transition-group
                enterActiveClass="transition-all duration-300"
                leaveActiveClass="transition-all duration-100"
                enterFromClass="opacity-0"
                leaveToClass="opacity-0">
                <template v-if="processing">
                  <tr v-for="i in Array(1).fill(0)" :key="i">
                  </tr>
                </template>

                <template v-else>
                  <tr v-for="(role, i) in (tableRefresh = refresh) ? data : data" :key="i" class="dark:hover:bg-gray-600 transition-all duration-300">
                    <td class="px-2 py-1 border dark:border-gray-800 text-center">{{ i + 1 }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ role.name }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800">
                      <div class="flex-wrap">
                        <div v-for="(permission, j) in role.permissions" :key="j" class="inline-block bg-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-900 border dark:border-gray-700 dark:hover:border-gray-800 rounded-md px-3 py-1 m-[1px] text-sm">
                          <div class="flex items-center justify-between space-x-2">
                            <p class="uppercase font-semibold">{{ permission.name }}</p>

                            <Icon @click.prevent="detach(role, permission, refresh)" v-if="can('update role')" name="times" class="px-2 py-1 rounded-md bg-red-500 dark:bg-gray-700 transition-all hover:bg-red-600 text-white cursor-pointer" />
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-2 py-1 border dark:border-gray-800">
                      <div class="flex items-center space-x-2">
                        <ButtonBlue v-if="can('update role')" @click.prevent="edit(role, refresh)">
                          <Icon name="edit" />
                          <p class="uppercase">edit</p>
                        </ButtonBlue>

                        <ButtonRed v-if="can('delete role')" @click.prevent="destroy(role, refresh)">
                          <Icon name="trash" />
                          <p class="uppercase">delete</p>
                        </ButtonRed>
                      </div>
                    </td>
                  </tr>
                </template>

                <tr v-if="empty">
                  <td class="text-5xl text-center p-4" colspan="1000">
                    <p class="lowercase first-letter:capitalize font-semibold">there are no data available</p>
                  </td>
                </tr>
              </transition-group>
            </template>
          </Builder>
        </div>
      </template>
    </Card>
  </DashboardLayout>

  <Modal :show="open">
    <form @submit.prevent="submit" class="w-full max-w-xl h-fit shadow-xl">
      <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
        <template #header>
          <div class="flex items-center justify-end bg-gray-200 dark:bg-gray-800 p-2">
            <Close @click.prevent="close" />
          </div>
        </template>

        <template #body>
          <div class="flex flex-col space-y-4 p-4">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="name" class="w-1/3 lowercase first-letter:capitalize">name</label>
                <input ref="name" type="text" name="name" v-model="form.name" class="w-full bg-white dark:bg-transparent rounded px-3 py-2 placeholder:capitalize" placeholder="name" required>
              </div>

              <p v-if="form.errors.name" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.name }}</p>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="permissions" class="w-1/3 lowercase first-letter:capitalize">permissions</label>
                <Select
                  v-model="form.permissions"
                  :options="permissions.map(p => ({
                    label: p.name,
                    value: p.id,
                  }))"
                  :searchable="true"
                  :clearOnSelect="false"
                  :closeOnSelect="false"
                  class="text-gray-800 uppercase"
                  mode="tags" />
              </div>

              <p v-if="form.errors.permissions" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.permissions }}</p>
            </div>
          </div>
        </template>

        <template #footer>
          <div class="flex items-center justify-end space-x-2 bg-gray-200 dark:bg-gray-800 px-2 py-1">
            <ButtonGreen type="submit">
              <Icon name="check" />
              <p class="uppercase font-semibold">{{ form.id ? 'update' : 'create' }}</p>
            </ButtonGreen>
          </div>
        </template>
      </Card>
    </form>
  </Modal>
</template>
<script setup>
import { getCurrentInstance, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import Swal from 'sweetalert2'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Card from '@/Components/Card.vue'
import Icon from '@/Components/Icon.vue'
import { Inertia } from '@inertiajs/inertia'
import Modal from '@/Components/Modal.vue'
import Close from '@/Components/Button/Close.vue'
import ButtonGreen from '@/Components/Button/Green.vue'
import ButtonBlue from '@/Components/Button/Blue.vue'
import ButtonRed from '@/Components/Button/Red.vue'

const self = getCurrentInstance()
const permissions = ref([])
const search = ref('')
const open = ref(false)
const form = useForm({
  id: null,
  name: '',
})

const fetch = async () => {
  try {
    const response = await axios.get(route('api.v1.superuser.permission'))
    permissions.value = response.data
  } catch (e) {
    const response = await Swal.fire({
      title: 'Do you want to try again?',
      text: `${e}`,
      icon: 'error',
      showCancelButton: true,
      showCloseButton: true,
    })
    
    response.isConfirmed && fetch()
  }
}

fetch()

const show = () => {
  open.value = true

  nextTick(() => self.refs.name?.focus())
}

const close = () => {
  open.value = false
  form.reset()
  form.clearErrors()
}

const esc = e => {
  if (e.key === 'Escape') close()
}

const store = () => form.post(route('superuser.permission.store'), {
  onSuccess: () => close() || fetch(),
  onError: show,
})

const edit = permission => {
  form.id = permission.id
  form.name = permission.name
  show()
}

const update = () => form.patch(route('superuser.permission.update', form.id), {
  onSuccess: () => close() || fetch(),
  onError: show,
})

const destroy = async permission => {
  const response = await Swal.fire({
    title: 'Are you sure?',
    text: 'You can\'t restore it after deleted',
    icon: 'warning',
    showCancelButton: true,
  })

  if (response.isConfirmed) {
    return Inertia.delete(route('superuser.permission.destroy', permission.id), {
      onSuccess: fetch,
    })
  }
}

const submit = () => form.id ? update() : store()

onMounted(() => {
  window.addEventListener('keydown', esc)

  document.querySelector('[type=search]')?.focus()
})
onUnmounted(() => window.removeEventListener('keydown', esc))
</script>

<template>
  <DashboardLayout title="Permission">
    <Card class="bg-gray-50 dark:bg-slate-700 shadow-md">
      <template #header>
        <div class="flex items-center space-x-2 bg-gray-200 dark:bg-gray-800 p-2">
          <ButtonGreen v-if="can('create permission')" @click.prevent="show()">
            <Icon name="plus" />
            <p class="font-semibold uppercase">create</p>
          </ButtonGreen>
        </div>
      </template>

      <template #body>
        <div class="flex flex-col space-y-2  p-4 h-screen max-h-96 overflow-auto">
          <div class="flex items-center justify-end space-x-2 text-sm dark:text-gray-100 px-4">
            <input v-model="search" type="search" class="bg-transparent w-full max-w-sm rounded-md placeholder:capitalize py-1" placeholder="search">
          </div>

          <div class="flex-wrap px-4 pb-2 dark:bg-gray-700 dark:text-gray-100 rounded-b-md">
            <transition-group
                enterActiveClass="transition-all duration-300"
                leaveActiveClass="transition-all duration-300"
                enterFromClass="opacity-0 -translate-y-100"
                leaveToClass="opacity-0 -translate-y-100">
              <div v-for="(permission, i) in permissions.filter(p => p.name?.toLowerCase().includes(search?.trim().toLowerCase()))" :key="i" class="inline-block bg-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-900 transition-all duration-300 border dark:border-gray-700 dark:hover:border-gray-800 rounded-md m-[2px] px-3 py-1">
                <div class="flex items-center space-x-2 text-sm">
                  <p class="uppercase">{{ permission.name }}</p>

                  <div class="flex items-center space-x-1">
                    <Icon v-if="can('update permission')" @click.prevent="edit(permission)" name="pen" class="px-2 py-1 rounded cursor-pointer text-white bg-blue-600 hover:bg-blue-700 transition-all" />
                    <Icon v-if="can('delete permission')" @click.prevent="destroy(permission)" name="trash" class="px-2 py-1 rounded cursor-pointer text-white bg-red-600 hover:bg-red-700 transition-all" />
                  </div>
                </div>
              </div>
            </transition-group>
          </div>
        </div>
      </template>
    </Card>
  </DashboardLayout>
  
  <Modal :show="open">
    <form @submit.prevent="submit" class="w-full max-w-xl h-fit shadow-xl">
      <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100 border dark:border-gray-700">
        <template #header>
          <div class="flex items-center space-x-2 justify-end bg-gray-200 dark:bg-gray-800 dark:text-gray-50 p-2">
            <Close @click.prevent="close" />
          </div>
        </template>

        <template #body>
          <div class="flex flex-col space-y-4 p-4">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="name" class="lowercase first-letter:capitalize flex-none w-1/4">name</label>
                <input ref="name" type="text" v-model="form.name" class="bg-transparent w-full rounded-md px-3 py-1 text-sm placeholder:capitalize" placeholder="name">
              </div>
              
              <transition name="fade">
                <div v-if="form.errors.name" class="text-red-400 text-sm text-right">{{ form.errors.name }}</div>
              </transition>
            </div>
          </div>
        </template>

        <template #footer>
          <div class="flex items-center space-x-2 justify-end bg-gray-200 dark:bg-gray-800 text-white px-2 py-1">
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
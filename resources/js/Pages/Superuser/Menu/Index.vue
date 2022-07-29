<script setup>
import { getCurrentInstance, ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useForm, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Card from '@/Components/Card.vue'
import Icon from '@/Components/Icon.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import Select from '@vueform/multiselect'
import Nested from './Nested.vue'

const self = getCurrentInstance()
const { permissions, routes, icons } = defineProps({
  permissions: Array,
  routes: Array,
  icons: Array,
})

const a = ref(true)
const menus = ref([])
const search = ref('')
const form = useForm({
  id: null,
  name: '',
  icon: 'circle',
  route_or_url: '',
  actives: [],
  permissions: [],
})

const fetch = async () => {
  try {
    const response = await axios.get(route('api.v1.superuser.menu'))
    menus.value = response.data
  } catch (e) {
    const response = await Swal.fire({
      title: 'Are you want to try again?',
      text: `${e}`,
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
}
const icon = ref(false)
const open = ref(false)
const show = () => {
  open.value = true
}

const close = () => {
  open.value = false
  form.id && form.reset()
}

const store = () => {
  return form.post(route('superuser.menu.store'), {
    onSuccess: () => {
      close()
      form.reset()
      Inertia.get(route(route().current()))
    },

    onError: () => {
      show()
    },
  })
}

const edit = menu => {
  form.id = menu.id
  form.name = menu.name
  form.icon = menu.icon
  form.route_or_url = menu.route_or_url
  form.actives = menu.actives
  form.permissions = menu.permissions.map(p => p.id)

  nextTick(show)
}

const update = () => {
  return form.patch(route('superuser.menu.update', form.id), {
    onSuccess: () => {
      close()
      form.reset()
      Inertia.get(route(route().current()))
    },

    onError: () => {
      show()
    },
  })
}

const destroy = async menu => {
  const response = await Swal.fire({
    title: 'Are you sure want to delete?',
    text: 'You can\'t recover it after deleted',
    icon: 'question',
    showCloseButton: true,
    showCancelButton: true,
  })

  response.isConfirmed && Inertia.delete(route('superuser.menu.destroy', menu.id), {
    onSuccess: () => Inertia.get(route(route().current()))
  })
}

const submit = () => form.id ? update() : store()

const timeout = ref(null)
const save = () => {
  timeout.value && clearTimeout(timeout.value)
  timeout.value = setTimeout(() => {
    return useForm({ menus: menus.value }).patch(route('superuser.menu.save'), {
      onFinish: () => {
        clearTimeout(timeout.value)
        Inertia.get(route(route().current()))
      }
    }, 100)
  })
}

const esc = e => e.key === 'Escape' && close()

Inertia.on('finish', () => {
  a.value = false
  nextTick(() => a.value = true)
})

onMounted(fetch)
onMounted(() => window.addEventListener('keydown', esc))
onUnmounted(() => window.removeEventListener('keydown', esc))
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <DashboardLayout title="Menu">
    <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
      <template #header>
        <div class="flex items-center space-x-2 p-2 bg-gray-200 dark:bg-gray-800">
          <button @click.prevent="show" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-sm text-white transition-all">
            <div class="flex items-center space-x-1">
              <Icon name="plus" />
              <p class="uppercase font-semibold">create</p>
            </div>
          </button>
        </div>
      </template>

      <template #body>
        <div class="flex flex-col space-y-1 p-2">
          <Nested :menus="menus" :edit="edit" :destroy="destroy" :save="save" />
        </div>
      </template>
    </Card>
  </DashboardLayout>

  <transition name="fade">
    <div v-if="open" class="fixed top-0 left-0 w-full h-screen flex sm:items-center justify-center bg-black bg-opacity-40 overflow-auto">
      <form @submit.prevent="submit" class="w-full max-w-xl sm:max-w-5xl rounded-md shadow-xl">
        <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
          <template #header>
            <div class="flex items-center space-x-2 p-2 justify-end bg-gray-200 dark:bg-gray-800">
              <Icon @click.prevent="close" name="times" class="px-2 py-1 bg-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-md transition-all cursor-pointer" />
            </div>
          </template>

          <template #body>
            <div class="flex flex-col space-y-2 p-4">
              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label for="name" class="lowercase first-letter:capitalize w-1/3">name</label>
                  <input type="text" name="name" v-model="form.name" ref="name" class="w-full bg-white dark:bg-transparent rounded px-3 py-2 border dark:border-gray-800 uppercase placeholder:capitalize" placeholder="name" required>
                </div>

                <div v-if="form.errors.name" class="text-right text-sm text-red-500">{{ form.errors.name }}</div>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label for="route_or_url" class="lowercase first-letter:capitalize w-1/3">route name or url</label>
                  <Select
                    v-model="form.route_or_url"
                    :options="routes"
                    :searchable="true"
                    :createOption="true"
                    :value="form.route_or_url"
                    class="text-gray-800 placeholder:capitalize"
                    placeholder="route name or url" />
                </div>

                <div v-if="form.errors.route_or_url" class="text-right text-sm text-red-500">{{ form.errors.route_or_url }}</div>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label for="actives" class="lowercase first-letter:capitalize w-1/3">actives</label>
                  <Select
                    v-model="form.actives"
                    :options="[...routes, ...form.actives.filter(active => ! routes.includes(active))]"
                    :searchable="true"
                    :closeOnSelect="false"
                    :clearOnSelect="false"
                    :createTag="true"
                    mode="tags"
                    class="text-gray-800 placeholder:capitalize"
                    placeholder="actives" />
                </div>

                <div v-if="form.errors.actives" class="text-right text-sm text-red-500">{{ form.errors.actives }}</div>
              </div>

              <div class="flex flex-col space-y-1">
                <div class="flex items-center space-x-2">
                  <label for="permissions" class="lowercase first-letter:capitalize w-1/3">permissions</label>
                  <Select
                    v-model="form.permissions"
                    :options="permissions.map(p => ({
                      label: p.name,
                      value: p.id,
                    }))"
                    :searchable="true"
                    :closeOnSelect="false"
                    :clearOnSelect="false"
                    mode="tags"
                    class="text-gray-800 placeholder:capitalize"
                    placeholder="permissions" />
                </div>

                <div v-if="form.errors.permissions" class="text-right text-sm text-red-500">{{ form.errors.permissions }}</div>
              </div>

              <div class="flex items-center space-x-2">
                <label class="flex-none lowercase first-letter:capitalize w-1/4">icon</label>

                <div class="flex items-center justify-between w-full">
                  <p class="text-3xl">
                    <Icon :name="form.icon" class="dark:text-white" />
                  </p>

                  <p class="text-sm uppercase">{{ form.icon }}</p>
                  
                  <button @click.prevent="icon = true" type="button" class="bg-blue-600 hover hover:bg-blue-700 rounded-md px-3 py-1 text-white text-sm transition-all">
                    <div class="flex items-center space-x-1">
                      <Icon name="edit" />

                      <p class="uppercase font-semibold">change</p>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </template>

          <template #footer>
            <div class="flex items-center justify-end space-x-2 bg-gray-200 dark:bg-gray-800 py-1 px-2">
              <button type="submit" class="bg-green-600 hover:bg-green-700 rounded-md px-3 py-1 text-white text-sm transition-all">
                <div class="flex items-center space-x-1">
                  <Icon name="check" />
                  <p class="uppercase font-semibold">{{ form.id ? 'update' : 'create' }}</p>
                </div>
              </button>
            </div>
          </template>
        </Card>
      </form>
    </div>
  </transition>

  <transition name="fade">
    <div v-if="icon" class="fixed top-0 left-0 w-full h-screen flex sm:items-center justify-center bg-black bg-opacity-40 overflow-auto">
      <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100 w-full max-w-xl sm:max-w-5xl">
        <template #header>
          <div class="flex items-center space-x-2 p-2 justify-end bg-gray-200 dark:bg-gray-800">
            <input type="search" v-model="search" class="py-1 w-full bg-white dark:bg-transparent rounded-md text-sm uppercase" placeholder="search something">
            <Icon @click.prevent="icon = false" name="times" class="px-2 py-1 bg-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-md transition-all cursor-pointer" />
          </div>
        </template>

        <template #body>
          <div class="flex-wrap p-4 max-h-96 overflow-auto">
            <Icon v-for="(icx, i) in icons.filter(icx => icx.includes(search.trim().toLocaleLowerCase()))" :key="i" @click.prevent="form.icon = icx; icon = false" :name="icx" class="m-1 text-5xl px-2 py-1 text-gray-800 bg-gray-200 hover:bg-gray-100 dark:text-white dark:bg-gray-600 dark:hover:bg-gray-700 rounded-md cursor-pointer transition-all" />
          </div>
        </template>
      </Card>
    </div>
  </transition>
</template>
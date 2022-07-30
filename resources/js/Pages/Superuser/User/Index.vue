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
import ButtonGreen from '@/Components/Button/Green.vue'
import ButtonBlue from '@/Components/Button/Blue.vue'
import ButtonRed from '@/Components/Button/Red.vue'
import Close from '@/Components/Button/Close.vue'

const self = getCurrentInstance()
const { permissions, roles } = defineProps({
  permissions: Array,
  roles: Array,
})
const form = useForm({
  id: null,
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  permissions: [],
  roles: [],
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
  form.clearErrors()
}

const store = () => {
  return form.post(route('superuser.user.store'), {
    onSuccess: () => close() || (tableRefresh.value && tableRefresh.value()),
    onError: () => show(),
  })
}

const edit = user => {
  form.id = user.id
  form.name = user.name
  form.username = user.username
  form.email = user.email
  form.permissions = user.permissions.map(permission => permission.id)
  form.roles = user.roles.map(role => role.id)

  show()
}

const update = () => {
  return form.patch(route('superuser.user.update', form.id), {
    onSuccess: () => close() || (tableRefresh.value && tableRefresh.value()),
    onError: () => show(),
  })
}

const destroy = async (user, refresh) => {
  const response = await Swal.fire({
    title: 'Are you sure?',
    text: 'You can\'t restore it after deleted',
    icon: 'question',
    showCancelButton: true,
    showCloseButton: true,
  })

  if (response.isConfirmed) {
    Inertia.on('finish', () => refresh())

    return Inertia.delete(route('superuser.user.destroy', user.id))
  }
}

const submit = () => form.id ? update() : store()

const detachPermission = async (user, permission, refresh) => {
  const response = await Swal.fire({
    title: 'Are you sure?',
    text: 'You can re adding it on edit page',
    icon: 'question',
    showCancelButton: true,
    showCloseButton: true,
  })

  if (!response.isConfirmed)
    return

  Inertia.on('finish', () => refresh())
  Inertia.patch(route('superuser.user.permission.detach', { user: user.id, permission: permission.id }))
}

const detachRole = async (user, role, refresh) => {
  const response = await Swal.fire({
    title: 'Are you sure?',
    text: 'You can re adding it on edit page',
    icon: 'question',
    showCancelButton: true,
    showCloseButton: true,
  })

  if (!response.isConfirmed)
    return

  Inertia.on('finish', () => refresh())
  Inertia.patch(route('superuser.user.role.detach', { user: user.id, role: role.id }))
}

const esc = e => e.key === 'Escape' && close()
onMounted(() => window.addEventListener('keydown', esc))
onUnmounted(() => window.removeEventListener('keydown', esc))
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <DashboardLayout title="User">
    <Card class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
      <template #header>
        <div class="flex items-center space-x-2 p-2 bg-gray-200 dark:bg-gray-800">
          <ButtonGreen v-if="can('create user')" @click.prevent="show">
            <Icon name="plus" />
            <p class="uppercase font-semibold">create</p>
          </ButtonGreen>
        </div>
      </template>

      <template #body>
        <div class="flex flex-col space-y-2">
          <Builder :url="route('api.v1.superuser.user.paginate')">
            <template v-slot:thead="table">
              <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
                <Th class="border px-3 py-2 text-center" :table="table" :sort="false">no</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="name">name</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="username">username</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="email">email</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">permissions</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">roles</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="email_verified_at">verified at</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="created_at">created at</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="updated_at">updated at</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">action</Th>
              </tr>
            </template>

            <template v-slot:tfoot="table">
              <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
                <Th class="border px-3 py-2 text-center" :table="table" :sort="false">no</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">name</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">username</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">email</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">permissions</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">roles</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">verified at</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">created at</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">updated at</Th>
                <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">action</Th>
              </tr>
            </template>

            <template v-slot:tbody="{ data, processing, empty, refresh }">
              <transition-group
                enterActiveClass="transition-all duration-100"
                leaveActiveClass="transition-all duration-50"
                enterFromClass="opacity-0 -scale-y-100"
                leaveToClass="opacity-0 -scale-y-100">
                <template v-if="processing">
                  <tr v-for="i in Array(5).fill(0)" :key="i" class="border dark:border-gray-800">
                    <td class="text-5xl text-center p-2" colspan="1000">
                      <div class="lowercase first-letter:capitalize font-semibold dark:bg-gray-800 animate-pulse p-5 rounded-md"></div>
                    </td>
                  </tr>
                </template>

                <template v-else>
                  <tr v-for="(user, i) in (tableRefresh = refresh) ? data : data" :key="i" class="dark:hover:bg-gray-600 transition-all duration-300">
                    <td class="px-2 py-1 border dark:border-gray-800 text-center">{{ i + 1 }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ user.name }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ user.username }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ user.email }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800">
                      <div class="flex-wrap">
                        <div v-for="(permission, j) in user.permissions" :key="j" class="inline-block bg-gray-600 rounded-md px-3 py-1 m-[1px] text-sm">
                          <div class="flex items-center justify-between space-x-1">
                            <p class="uppercase font-semibold">{{ permission.name }}</p>

                            <Icon @click.prevent="detachPermission(user, permission, refresh)" v-if="can('update user')" name="times" class="px-2 py-1 rounded-md dark:bg-gray-700 transition-all hover:bg-red-500 cursor-pointer" />
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-2 py-1 border dark:border-gray-800">
                      <div class="flex-wrap">
                        <div v-for="(role, j) in user.roles" :key="j" class="inline-block dark:bg-gray-800 dark:hover:bg-gray-900 border dark:border-gray-800 rounded-md px-3 py-1 m-[1px] text-sm transition-all">
                          <div class="flex items-center justify-between space-x-2">
                            <p class="uppercase font-semibold">{{ role.name }}</p>

                            <Icon @click.prevent="detachRole(user, role, refresh)" v-if="can('update user')" name="times" class="px-2 py-1 rounded-md dark:bg-gray-700 transition-all hover:bg-red-500 cursor-pointer" />
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ new Date(user.email_verified_at).toLocaleString('id') }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ new Date(user.created_at).toLocaleString('id') }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800 uppercase">{{ new Date(user.updated_at).toLocaleString('id') }}</td>
                    <td class="px-2 py-1 border dark:border-gray-800">
                      <div class="flex items-center space-x-2">
                        <ButtonBlue v-if="can('update user')" @click.prevent="edit(user, refresh)">
                          <Icon name="edit" />
                          <p class="uppercase">edit</p>
                        </ButtonBlue>

                        <ButtonRed v-if="can('delete user')" @click.prevent="destroy(user, refresh)">
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
    <form @submit.prevent="submit" class="w-full max-w-xl sm:max-w-5xl h-fit shadow-xl">
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
                <label for="username" class="w-1/3 lowercase first-letter:capitalize">username</label>
                <input ref="username" type="text" name="username" v-model="form.username" class="w-full bg-white dark:bg-transparent rounded px-3 py-2 placeholder:capitalize" placeholder="username" required>
              </div>

              <p v-if="form.errors.username" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.username }}</p>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="email" class="w-1/3 lowercase first-letter:capitalize">email</label>
                <input ref="email" type="email" name="email" v-model="form.email" class="w-full bg-white dark:bg-transparent rounded px-3 py-2 placeholder:capitalize" placeholder="email" required>
              </div>

              <p v-if="form.errors.email" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.email }}</p>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="password" class="w-1/3 lowercase first-letter:capitalize">password</label>
                <input ref="password" type="password" name="password" v-model="form.password" class="w-full bg-white dark:bg-transparent rounded px-3 py-2 placeholder:capitalize" placeholder="password" :required="form.id === null">
              </div>

              <p v-if="form.errors.password" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.password }}</p>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="password_confirmation" class="w-1/3 lowercase first-letter:capitalize">password confirmation</label>
                <input ref="password_confirmation" type="password" name="password_confirmation" v-model="form.password_confirmation" class="w-full bg-white dark:bg-transparent rounded px-3 py-2 placeholder:capitalize" placeholder="password confirmation" :required="form.id === null">
              </div>

              <p v-if="form.errors.password_confirmation" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.password_confirmation }}</p>
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
                  :clearOnSelect="false"
                  :closeOnSelect="false"
                  :searchable="true"
                  class="text-gray-800 uppercase"
                  mode="tags" />
              </div>

              <p v-if="form.errors.permissions" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.permissions }}</p>
            </div>

            <div class="flex flex-col space-y-2">
              <div class="flex items-center space-x-2">
                <label for="roles" class="w-1/3 lowercase first-letter:capitalize">roles</label>
                <Select
                  v-model="form.roles"
                  :options="roles.map(r => ({
                    label: r.name,
                    value: r.id,
                  }))"
                  :searchable="true"
                  :clearOnSelect="false"
                  :closeOnSelect="false"
                  class="text-gray-800 uppercase"
                  mode="tags" />
              </div>

              <p v-if="form.errors.roles" class="text-red-500 text-right lowercase first-letter:capitalize">{{ form.errors.roles }}</p>
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
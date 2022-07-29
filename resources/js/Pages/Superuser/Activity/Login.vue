<script setup>
import { getCurrentInstance, ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import Icon from '@/Components/Icon.vue';
import Builder from '@/Components/DataTable/Builder.vue';
import Th from '@/Components/DataTable/Th.vue';

const self = getCurrentInstance()
</script>

<template>
  <DashboardLayout title="Login Activity">
    <Card class="bg-white dark:bg-gray-700 dark:text-gray-200">
      <template #header>
        <div class="flex items-center space-x-2 bg-gray-200 dark:bg-gray-800 p-2">
          <p class="lowercase first-letter:capitalize font-semibold">login activities</p>
        </div>
      </template>

      <template #body>
        <Builder :url="route('api.v1.superuser.activity.login')">
          <template #thead="table">
            <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
              <Th class="border px-3 py-2 text-center" :table="table" :sort="false">no</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="users.name">name</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="users.username">username</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="login_activities.ip_address">ip address</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="login_activities.browser">browser</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="login_activities.platform">platform</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="true" name="login_activities.created_at">at</Th>
            </tr>
          </template>

          <template #tfoot="table">
            <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
              <Th class="border px-3 py-2 text-center" :table="table" :sort="false">no</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">name</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">username</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">ip address</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">browser</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">platform</Th>
              <Th class="border px-3 py-2 text-center whitespace-nowrap" :table="table" :sort="false">at</Th>
            </tr>
          </template>

          <template #tbody="{ data }">
            <tr v-for="(activity, i) in data" :key="i" class="dark:hover:bg-gray-600 dark:border-gray-800 transition-all">
              <td class="px-2 py-1 border border-inherit text-center">{{ i + 1 }}</td>
              <td class="px-2 py-1 border border-inherit uppercase">{{ activity.name }}</td>
              <td class="px-2 py-1 border border-inherit uppercase">{{ activity.username }}</td>
              <td class="px-2 py-1 border border-inherit uppercase">{{ activity.ip_address }}</td>
              <td class="px-2 py-1 border border-inherit uppercase">{{ activity.browser }}</td>
              <td class="px-2 py-1 border border-inherit uppercase">{{ activity.platform }}</td>
              <td class="px-2 py-1 border border-inherit uppercase">{{ new Date(activity.created_at).toLocaleString('id') }}</td>
            </tr>
          </template>
        </Builder>
      </template>
    </Card>
  </DashboardLayout>
</template>
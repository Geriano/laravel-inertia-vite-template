<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import Icon from '@/Components/Icon.vue';
import Builder from '@/Components/DataTable/Builder.vue';
import Th from '@/Components/DataTable/Th.vue';
</script>

<template>
  <DashboardLayout
    :title="__('login activities')"
  >
    <Card class="bg-white dark:bg-gray-700 dark:text-gray-200">
      <template #header>
        <div class="flex items-center space-x-2 bg-gray-200 dark:bg-gray-800 p-2">
          <p class="lowercase first-letter:capitalize font-semibold">
            {{ __('login activities') }}
          </p>
        </div>
      </template>

      <template #body>
        <Builder
          :url="route('superuser.activity.login')"
        >
          <template #thead="table">
            <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
              <Th
                :table="table"
                :sort="false"
                class="border p-2 text-center"
              >
                {{ __('no') }}
              </Th>
              
              <Th
                :table="table"
                :sort="true"
                name="users.name"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('name') }}
              </Th>

              <Th
                :table="table"
                :sort="true"
                name="users.username"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('username') }}
              </Th>

              <Th
                :table="table"
                :sort="true"
                name="login_activities.ip_address"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('ip address') }}
              </Th>

              <Th
                :table="table"
                :sort="true"
                name="login_activities.browser"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('browser') }}
              </Th>

              <Th
                :table="table"
                :sort="true"
                name="login_activities.platform"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('platform') }}
              </Th>

              <Th
                :table="table"
                :sort="true"
                name="login_activities.created_at"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('at') }}
              </Th>
            </tr>
          </template>

          <template #tfoot="table">
            <tr class="bg-gray-200 dark:bg-gray-800 border-gray-300 dark:border-gray-900">
              <Th
                :table="table"
                :sort="false"
                class="border p-2 text-center"
              >
                {{ __('no') }}
              </Th>

              <Th
                :table="table"
                :sort="false"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('name') }}
              </Th>

              <Th
                :table="table"
                :sort="false"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('username') }}
              </Th>

              <Th
                :table="table"
                :sort="false"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('ip address') }}
              </Th>

              <Th
                :table="table"
                :sort="false"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('browser') }}
              </Th>

              <Th
                :table="table"
                :sort="false"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('platform') }}
              </Th>

              <Th
                :table="table"
                :sort="false"
                class="border px-3 py-2 text-center whitespace-nowrap"
              >
                {{ __('at') }}
              </Th>

            </tr>
          </template>

          <template #tbody="{ data }">
            <TransitionGroup
              enterActiveClass="transition-all duration-100"
              leaveActiveClass="transition-all duration-50"
              enterFromClass="opacity-0 -scale-y-100"
              leaveToClass="opacity-0 -scale-y-100"
            >
              <tr
                v-for="(activity, i) in data"
                :key="i"
                class="dark:hover:bg-gray-600 dark:border-gray-800 transition-all duration-300"
              >
                <td class="px-2 py-1 border border-inherit text-center">
                  {{ i + 1 }}
                </td>

                <td class="px-2 py-1 border border-inherit uppercase">
                  {{ activity.name }}
                </td>

                <td class="px-2 py-1 border border-inherit uppercase">
                  {{ activity.username }}
                </td>

                <td class="px-2 py-1 border border-inherit uppercase">
                  {{ activity.ip_address }}
                </td>

                <td class="px-2 py-1 border border-inherit uppercase">
                  {{ __(activity.browser) }}
                </td>

                <td class="px-2 py-1 border border-inherit uppercase">
                  {{ __(activity.platform) }}
                </td>

                <td class="px-2 py-1 border border-inherit uppercase">
                  {{ new Date(activity.created_at).toLocaleString('id') }}
                </td>
              </tr>
            </TransitionGroup>
          </template>
        </Builder>
      </template>
    </Card>
  </DashboardLayout>
</template>
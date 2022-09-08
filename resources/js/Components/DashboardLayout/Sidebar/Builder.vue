<script>
import { defineComponent, h } from "vue"
import Link from "./Link.vue"
import Links from "./Links.vue"

export default defineComponent({
  props: {
    open: Boolean,
    menus: Array,
  },

  setup(props, { attrs }) {
    return props => {
      const { menus, open } = props
      const padding = (menu, initial = 0) => menu && menu.parent_id !== null ? padding(menu.parent, initial + 8) : initial

      const generate = (menu, attrs = {}) => {
        if (menu.childs?.length > 0) {
          return h(Links, {
            ...attrs,
            padding: padding(menu),
            menu,
            childs: menu.childs,
            open,
          }, menu.childs.map(child => generate(child, {
            padding: padding(child)
          })))
        }
        
        return h(Link, { ...attrs, padding: padding(menu), menu, open, })
      }

      return h('div', { class: 'flex flex-col' }, menus.map(menu => generate(menu)))
    }
  },
})
</script>

<template>
  <div class="hidden pl-0 pl-8 pl-16 pl-24 pl-32 pl-40 pl-48 pl-56 pl-64 pl-72 pl-80 pl-96"></div>
</template>
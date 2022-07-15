<script>
import { defineComponent, h } from "vue"
import Link from "./Link.vue"
import Links from "./Links.vue"

export default defineComponent({
  props: {
    menus: Array,
  },

  setup(props, { attrs }) {
    return props => {
      const { menus } = props
      const generate = (menu, attrs = {}) => {
        if (menu.childs?.length > 0) {
          return h(Links, {
            ...attrs,
            menu,
            childs: menu.childs,
          }, menu.childs.map(child => generate(child, { class: 'pl-8' })))
        }

        return h(Link, { ...attrs, menu })
      }

      return h('div', { class: 'flex flex-col' }, menus.map(menu => generate(menu)))
    }
  },
})
</script>
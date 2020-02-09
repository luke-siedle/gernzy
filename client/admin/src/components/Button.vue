<template>
  <button :type="type" :class="`gzy-button gzy-button--${mode} gzy-login__button gzy-login__button--${mode}`">
    {{ text }}
  </button>
</template>

<script>

  export default {
    props: {
      type: String,
      mode: String,
      text: String
    },
    data: () => ({

    }),
    methods: {}
  }

</script>
